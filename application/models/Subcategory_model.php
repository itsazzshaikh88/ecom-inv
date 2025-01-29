<?php
defined('BASEPATH') or exit('No direct script access allowed');
class Subcategory_model extends CI_Model
{
    protected $subcategory_table;

    public function __construct()
    {
        parent::__construct();
        $this->subcategory_table = 'subcategories';
    }


    function get_subcategories($type = 'list', $limit = 0, $currentPage = 1, $filters = [], $search = [])
    {
        $offset = get_limit_offset($currentPage, $limit);

        $this->db->select("s.subcategory_id, s.category_id, s.name, s.description, s.image_url, s.banner_image_url, s.alt_text, s.meta_title, s.meta_description, s.slug, s.display_order, s.is_active, s.created_at, s.updated_at, c.name as category_name");
        $this->db->from($this->subcategory_table . " s");
        $this->db->join("categories c", "c.category_id = s.category_id", "left");
        $this->db->order_by("s.subcategory_id", "DESC");

        if (!empty($filters) && is_array($filters)) {
            foreach ($filters as $key => $value) {
                if ($key == 'category_id') {
                    $this->db->where('s.category_id', $value);
                } else {
                    // Apply filter for other columns as usual
                    $this->db->where($key, $value);
                }
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

    function get_subcategory_by_name($name)
    {
        return $this->db->where('name', strtolower($name))->get('subcategories')->row_array();
    }
    function get_subcategory_by_id($id)
    {
        $this->db->select("s.subcategory_id, s.category_id, s.name, s.description, s.image_url, s.banner_image_url, s.alt_text, s.meta_title, s.meta_description, s.slug, s.display_order, s.is_active, s.created_at, s.updated_at, c.name as category_name");
        $this->db->from($this->subcategory_table . " s");
        $this->db->join("categories c", "c.category_id = s.category_id", "left");
        $this->db->where('s.subcategory_id', $id);
        $result = $this->db->get()->row_array();

        return $result;
    }
    function add_subcategory($data, $userid)
    {
        // supplier data
        $subcategory_data = [
            'category_id' => $data['category_id'],
            'name' => $data['name'],
            'is_active' => $data['is_active'],
            'alt_text' => $data['alt_text'],
            'description' => $data['description'],
            'image_url' => $data['image_url'],
            'banner_image_url' => $data['banner_image_url'],
            'slug' => generate_slug($data['name'], $this->subcategory_table, 'slug')
        ];

        if ($this->db->insert($this->subcategory_table, $subcategory_data)) {
            $inserted_id = $this->db->insert_id();
            return $this->get_subcategory_by_id($inserted_id);
        } else {
            return false;
        }
    }

    public function update_subcategory($id, $data, $userid)
    {
        // supplier data
        $category_data = [
            'category_id' => $data['category_id'],
            'name' => $data['name'],
            'is_active' => $data['is_active'],
            'alt_text' => $data['alt_text'],
            'description' => $data['description'],
            'slug' => generate_slug($data['name'], $this->subcategory_table, 'slug')
        ];
        if ($data['banner_image_url']) {
            $category_data['banner_image_url'] = $data['banner_image_url'];
        }
        if ($data['image_url']) {
            $category_data['image_url'] = $data['image_url'];
        }

        if ($this->db->where('subcategory_id', $id)->update($this->subcategory_table, $category_data)) {

            return $this->get_subcategory_by_id($id);
        } else {
            return false;
        }
    }
    public function delete_subcategory_by_id($userID)
    {
        $this->db->trans_start();

        $this->db->delete($this->subcategory_table, array('subcategory_id' => $userID));

        $this->db->trans_complete();

        if ($this->db->trans_status() === FALSE) {
        } else {
            return true;
        }
    }
}
