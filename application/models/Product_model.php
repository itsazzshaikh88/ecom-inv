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

        $this->db->select("p.id, p.name, p.slug, p.description, p.short_description, p.category_id, p.sub_category_id, p.brand_id, p.sku, p.barcode, p.stock_quantity, p.low_stock_threshold, p.is_featured, p.is_active, p.created_at, p.updated_at, c.name as category_name, s.name as subcategory_name");
        $this->db->from($this->product_table . " p");
        $this->db->join("categories c", " c.category_id = p.category_id", 'left');
        $this->db->join("subcategories s", " s.subcategory_id = p.sub_category_id", 'left');
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
        return $this->db->where('category_id', $id)->get('categories')->row_array();
    }
    function add_product($data, $userid)
    {
        // supplier data
        $product_data = [
            'name' => $data['name'],
            'is_active' => $data['is_active'],
            'short_description' => $data['short_description'],
            'description' => $data['description'],
            'category_id' => $data['category_id'],
            'sub_category_id' => $data['sub_category_id'],
            'brand_id' => $data['brand_id'] ?? null,
            'sku' => $data['sku'],
            'barcode' => $data['barcode'],
            'stock_quantity' => $data['stock_quantity'],
            'low_stock_threshold' => $data['low_stock_threshold'],
            'is_featured' => $data['is_featured'] ? 1 : 0,
            'slug' => generate_slug($data['name'], $this->product_table, 'slug')
        ];

        if ($this->db->insert($this->product_table, $product_data)) {
            $new_product_id = $this->db->insert_id();
            // Insert Data in Product Variants Table
            $total_variants = count($data['unit_id'] ?? []);
            // Insert data in Product Images Table
            for ($i = 0; $i < $total_variants; $i++) {
                $variant = [
                    'product_id' => $new_product_id,
                    'unit_id' => $data['unit_id'][$i],
                    'measure' => $data['measure'][$i],
                    'price' => $data['price'][$i],
                    'sale_price' => $data['sale_price'][$i],
                    'is_active' => 1
                ];
                $this->db->insert('product_variants', $variant);
            }

            // Insert product images
            $total_images = count($data['product_images'] ?? []);
            for ($j = 0; $j < $total_images; $j++) {
                $image = [
                    'product_id' => $new_product_id,
                    'image_url' => $data['product_images'][$j],
                    'is_main' => $j == 0 ? 1 : 0
                ];
                $this->db->insert('product_images', $image);
            }
            return $this->get_product_by_id($new_product_id);
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
            'slug' => generate_slug($data['name'], $this->product_table, 'slug')
        ];
        if ($data['banner_image_url']) {
            $category_data['banner_image_url'] = $data['banner_image_url'];
        }
        if ($data['image_url']) {
            $category_data['image_url'] = $data['image_url'];
        }

        if ($this->db->where('category_id', $id)->update($this->product_table, $category_data)) {

            return $this->get_product_by_id($id);
        } else {
            return false;
        }
    }
    public function delete_category_by_id($userID)
    {
        $this->db->trans_start();

        $this->db->delete($this->product_table, array('category_id' => $userID));

        $this->db->trans_complete();

        if ($this->db->trans_status() === FALSE) {
        } else {
            return true;
        }
    }
}
