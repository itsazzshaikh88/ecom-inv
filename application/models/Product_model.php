<?php
defined('BASEPATH') or exit('No direct script access allowed');
class Product_model extends CI_Model
{
    protected $product_table;

    public function __construct()
    {
        parent::__construct();
        $this->product_table = 'products';
    }


    function get_products($type = 'list', $limit = 10, $currentPage = 1, $filters = [], $search = [])
    {
        $offset = get_limit_offset($currentPage, $limit);

        $this->db->select("p.id, p.name, p.slug, p.description, p.short_description, p.category_id, p.sub_category_id, p.brand_id, p.sku, p.barcode, p.stock_quantity, p.low_stock_threshold, p.is_featured, p.is_active, p.created_at, p.updated_at, p.selling_price, p.product_price, p.uoms, p.qty_measure,p.images, c.name as category_name, u.name as unit_name, u.abbreviation as unit_abbrevation");
        $this->db->from($this->product_table . " p");
        $this->db->join("categories c", " c.category_id = p.category_id", 'left');
        $this->db->join("units_of_measurement u", " u.id = p.uoms", 'left');
        $this->db->order_by("p.id", "DESC");

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
    function get_product_by_id($id)
    {
        $this->db->select('p.*, c.name as category_name')
            ->from('products p')
            ->join('categories c', 'c.category_id = p.category_id', 'left')
            ->where('p.id', $id)
            ->order_by('p.name', 'ASC'); // Order by product name (you can change it as needed)
        return $this->db->get()->row_array();
    }
    function add_product($data, $userid)
    {
        // supplier data
        $product_data = [
            'name' => $data['name'],
            'is_active' => $data['is_active'],
            'description' => $data['description'],
            'category_id' => $data['category_id'],
            'stock_quantity' => $data['stock_quantity'],
            'low_stock_threshold' => $data['low_stock_threshold'],
            'uoms' => $data['uoms'],
            'qty_measure' => $data['qty_measure'],
            'product_price' => $data['product_price'],
            'selling_price' => $data['selling_price'],
            'images' => $data['product_images'] != [] ? json_encode($data['product_images']) : '',
            'is_featured' => $data['is_featured'] == 'on' ? 1 : 0,
            'slug' => generate_slug($data['name'], $this->product_table, 'slug')
        ];

        if ($this->db->insert($this->product_table, $product_data)) {
            $new_product_id = $this->db->insert_id();
            return $this->get_product_by_id($new_product_id);
        } else {
            return false;
        }
    }

    public function update_product($id, $data, $userid)
    {
        // supplier data
        $product_data = [
            'name' => $data['name'],
            'is_active' => $data['is_active'],
            'description' => $data['description'],
            'category_id' => $data['category_id'],
            'stock_quantity' => $data['stock_quantity'],
            'low_stock_threshold' => $data['low_stock_threshold'],
            'uoms' => $data['uoms'],
            'qty_measure' => $data['qty_measure'],
            'product_price' => $data['product_price'],
            'selling_price' => $data['selling_price'],
            'is_featured' => $data['is_featured'] == 'on' ? 1 : 0,
        ];


        if ($this->db->where('id', $id)->update($this->product_table, $product_data)) {
            return $this->get_product_by_id($id);
        } else {
            return false;
        }
    }
    public function delete_product_by_id($userID)
    {
        $this->db->trans_start();

        $this->db->delete($this->product_table, array('id' => $userID));

        $this->db->trans_complete();

        if ($this->db->trans_status() === FALSE) {
        } else {
            return true;
        }
    }
}
