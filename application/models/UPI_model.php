<?php
defined('BASEPATH') or exit('No direct script access allowed');
class UPI_model extends CI_Model
{
    protected $upi_table;

    public function __construct()
    {
        parent::__construct();
        $this->upi_table = 'upi_qr_codes';
    }


    function get_upi($type = 'list', $limit = 10, $currentPage = 1, $filters = [], $search = [])
    {
        $offset = get_limit_offset($currentPage, $limit);

        $this->db->select("*");
        $this->db->from($this->upi_table . " u");
        $this->db->order_by("u.id", "DESC");

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

    function get_upi_by_name($name)
    {
        return $this->db->where('upi_name', strtolower($name))->get('upi_qr_codes')->row_array();
    }
    function get_upi_by_id($id)
    {
        return $this->db->where('id', $id)->get('upi_qr_codes')->row_array();
    }
    function add_upi($data, $userid)
    {
        // supplier data
        $upi_data = [
            'upi_name' => $data['upi_name'],
            'is_active' => $data['is_active'],
            'qr_code_image' => $data['qr_code_image'],
        ];

        if ($this->db->insert($this->upi_table, $upi_data)) {
            $inserted_id = $this->db->insert_id();
            return $this->get_upi_by_id($inserted_id);
        } else {
            return false;
        }
    }

    public function update_upi($id, $data, $userid)
    {
        // supplier data
        $upi_data = [
            'upi_name' => $data['upi_name'],
            'is_active' => $data['is_active']
        ];
        if ($data['qr_code_image']) {
            $upi_data['qr_code_image'] = $data['qr_code_image'];
        }

        if ($this->db->where('id', $id)->update($this->upi_table, $upi_data)) {

            return $this->get_upi_by_id($id);
        } else {
            return false;
        }
    }
    public function delete_upi_by_id($userID)
    {
        $this->db->trans_start();

        $this->db->delete($this->upi_table, array('id' => $userID));

        $this->db->trans_complete();

        if ($this->db->trans_status() === FALSE) {
        } else {
            return true;
        }
    }
}
