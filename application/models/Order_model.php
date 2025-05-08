<?php
defined('BASEPATH') or exit('No direct script access allowed');
class Order_model extends CI_Model
{
    protected $order_table;
    protected $order_line_table;

    public function __construct()
    {
        parent::__construct();
        $this->order_table = 'orders';
        $this->order_line_table = 'order_items';
    }


    function get_orders($type = 'list', $limit = 10, $currentPage = 1, $filters = [], $search = [])
    {
        $offset = get_limit_offset($currentPage, $limit);

        $this->db->select("o.id, o.user_id, cart_id, o.order_number, o.status, o.total_amount, o.payment_status, o.payment_mode, o.payment_receipt, o.created_at, o.updated_at, a.full_name, a.email, a.phone,a.billing_address");
        $this->db->from($this->order_table . " o");
        $this->db->join("app_users a", " a.id = o.user_id", 'left');
        $this->db->order_by("o.id", "DESC");

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

    function get_order_by_key($key, $value)
    {
        return $this->db->where($key, $value)->get('orders')->row_array();
    }

    public function update_order_status($id, $data, $userid)
    {
        // supplier data
        $status_data = [
            'status' => $data['status'],
            'payment_status' => $data['payment_status'],
        ];

        if ($this->db->where('id', $id)->update("orders", $status_data)) {

            return $this->get_order_by_key("id", $id);
        } else {
            return false;
        }
    }

    public function delete_order_by_id($orderID)
    {
        $this->db->trans_start();

        $this->db->delete($this->order_table, array('id' => $orderID));

        $this->db->trans_complete();

        if ($this->db->trans_status() === FALSE) {
        } else {
            return true;
        }
    }

    function get_order_details_by_key($key, $value)
    {
        $order = ['header' => [], 'lines' => [], 'customer' => []];
        $order['header'] = $this->db->where($key, $value)->get('orders')->row_array();
        if (!empty($order['header'])) {
            $order_header_id = $order['header']['id'] ?? 0;
            $order_user_id = $order['header']['user_id'] ?? 0;
            $this->db->select("oi.id, oi.quantity, oi.unit_price, oi.total_price, p.name, p.description, p.images, p.qty_measure, p.product_price, p.selling_price");
            $this->db->from(" order_items oi");
            $this->db->join("products p", "p.id = oi.product_id", "left");
            $this->db->where('oi.order_id', $order_header_id);
            $order['lines'] = $this->db->get()->result_array();
            $order['customer'] = $this->db->where("id", $order_user_id)->get('app_users')->row_array();
        }
        return $order;
    }
}
