<?php
defined('BASEPATH') or exit('No direct script access allowed');
class Category_model extends CI_Model
{
    protected $category_table;

    public function __construct()
    {
        parent::__construct();
        $this->category_table = 'categories';
    }


    function get_categories($type = 'list', $limit = 10, $currentPage = 1, $filters = [], $search = [])
    {
        $offset = get_limit_offset($currentPage, $limit);

        $this->db->select("*");
        $this->db->from($this->category_table . " c");
        $this->db->order_by("c.category_id", "DESC");

        // Apply filters dynamically from the $filters array
        if (!empty($filters) && is_array($filters)) {
            foreach ($filters as $key => $value) {
                $this->db->where($key, $value);
            }
        }

        // Apply limit and offset only if 'list' type and offset is greater than zero
        if ($type == 'list') {
            if ($limit > 0) {
                $this->db->limit($limit, ($offset > 0 ? $offset : 0));
            }
        }

        // Execute query
        $query = $this->db->get();

        if ($type == 'list') {
            return $query->result_array();
        } else {
            return $query->num_rows();
        }
    }

    function get_category_by_name($name)
    {
        return $this->db->where('name', strtolower($name))->get('categories')->row_array();
    }
    function get_category_by_id($id)
    {
        return $this->db->where('category_id', $id)->get('categories')->row_array();
    }
    function add_category($data, $userid)
    {
        // supplier data
        $category_data = [
            'name' => $data['name'],
            'is_active' => $data['is_active'],
            'alt_text' => $data['alt_text'],
            'description' => $data['description'],
            'image_url' => $data['image_url'],
            'banner_image_url' => $data['banner_image_url'],
            'slug' => generate_slug($data['name'], $this->category_table, 'slug')
        ];

        if ($this->db->insert($this->category_table, $category_data)) {
            $inserted_id = $this->db->insert_id();
            return $this->get_category_by_id($inserted_id);
        } else {
            return false;
        }
    }

    public function update_category($id, $data, $userid)
    {
        // supplier data
        $category_data = [
            'name' => $data['name'],
            'is_active' => $data['is_active'],
            'alt_text' => $data['alt_text'],
            'description' => $data['description'],
            'slug' => generate_slug($data['name'], $this->category_table, 'slug')
        ];
        if ($data['banner_image_url']) {
            $category_data['banner_image_url'] = $data['banner_image_url'];
        }
        if ($data['image_url']) {
            $category_data['image_url'] = $data['image_url'];
        }

        if ($this->db->where('category_id', $id)->update($this->category_table, $category_data)) {

            return $this->get_category_by_id($id);
        } else {
            return false;
        }
    }
    public function delete_category_by_id($userID)
    {
        $this->db->trans_start();

        $this->db->delete($this->category_table, array('category_id' => $userID));

        $this->db->trans_complete();

        if ($this->db->trans_status() === FALSE) {
        } else {
            return true;
        }
    }
}
