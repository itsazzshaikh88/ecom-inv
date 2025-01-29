<?php
defined('BASEPATH') or exit('No direct script access allowed');
class User_model extends CI_Model
{
    protected $user_table; // Holds the name of the user table
    protected $client_table;
    protected $developer_table;

    public function __construct()
    {
        parent::__construct();
        $this->user_table = 'users'; // Initialize table
        $this->client_table = 'clients'; // Initialize table
        $this->developer_table = 'developers'; // Initialize table

    }

    /**
     * Create a new user
     *
     * @param array $data User data
     * @return int Inserted user ID
     */
    // public function create_user(array $data)
    // {
    //     $this->db->insert($this->user_table, $data);
    //     return $this->db->insert_id();
    // }

    function add_user($data, $userid)
    {
        // supplier data
        $user_data = [
            'full_name' => $data['full_name'],
            'email' => $data['email'],
            'phone_number' => $data['phone_number'],
            'role_id' => $data['role_id'],
            'status' => $data['status'],
        ];

        if ($this->db->insert($this->user_table, $user_data)) {
            $inserted_id = $this->db->insert_id();
            $user_id = "USR-" . sprintf("%04d", $inserted_id);
            $password = password_hash($data['password'], PASSWORD_ARGON2ID);
            $this->db->where('id', $inserted_id)->update($this->user_table, ['user_id' => $user_id, 'password' => $password]);

            // Save Additional information as well
            $role_detail = $this->db->where('id', $data['role_id'])->get('roles')->row_array();
            if (strtolower($role_detail['role_name'] ?? '') === 'client')
                $this->save_client_additional_details($data, $inserted_id, $userid);
            else if (strtolower($role_detail['role_name'] ?? '') === 'developer')
                $this->save_developer_additional_details($data, $inserted_id, $userid);

            return $this->get_user_by_id($inserted_id);
        } else {
            return false;
        }
    }

    function save_client_additional_details($data, $recordID, $userid)
    {
        $client_data = [
            'company_name' => $data['company_name'],
            'contact_name' => $data['contact_name'],
            'contact_email' => $data['contact_email'],
            'contact_phone' => $data['contact_phone'],
            'billing_address' => $data['billing_address'],
            'shipping_address' => $data['shipping_address'],
            'client_since' => $data['client_since'],
            'industry_type' => $data['industry_type'],
        ];
        $client = $this->db->where('user_id', $recordID)->get('clients')->row_array();
        if ($client == []) {
            $client_data['user_id'] = $recordID;
            $this->db->insert('clients', $client_data);
        } else
            $this->db->where('user_id', $recordID)->update('clients', $client_data);
    }

    function save_developer_additional_details($data, $recordID, $userid)
    {
        $developer_data = [
            'specialization' => $data['specialization'],
            'experience_years' => $data['experience_years'],
            'skills' => $data['skills'],
            'availability' => $data['availability'],
            'employment_type' => $data['employment_type'],
            'joining_date' => $data['joining_date'],
            'profile_link' => $data['profile_link'],
        ];
        $developer = $this->db->where('user_id', $recordID)->get('developers')->row_array();
        if ($developer == []) {
            $developer_data['user_id'] = $recordID;
            $this->db->insert('developers', $developer_data);
        } else
            $this->db->where('user_id', $recordID)->update('developers', $developer_data);
    }

    /**
     * Get user by ID
     *
     * @param int $user_id User ID
     * @return array|null User data or null if not found
     */
    public function get_user_by_id(int $user_id)
    {
        return $this->db->select("u.id, u.user_id, u.full_name, u.email, u.phone_number, u.status, u.is_2fa_enabled, r.role_name,u.role_id,  c.company_name, c.contact_name, c.contact_email, c.contact_phone, c.billing_address, c.shipping_address, c.client_since, c.industry_type, d.specialization, d.experience_years, d.skills, d.availability, d.employment_type, d.project_count, d.joining_date, d.profile_link, d.created_at, d.updated_at")
            ->from('users u')
            ->join('roles r', 'r.id = u.role_id')
            ->join('clients c', 'c.user_id = u.id', 'left')
            ->join('developers d', 'd.user_id = u.id', 'left')
            ->where('u.id', $user_id)
            ->get()->row_array();
    }


    function get_user_details_by_id($user_id)
    {
        $this->db->select('u.*, ud.*');
        $this->db->from($this->user_table . ' as u');
        $this->db->join($this->user_details . ' as ud', 'u.id = ud.id', 'left'); // Adjust the join type if needed
        $this->db->where('u.id', $user_id);
        return $this->db->get()->row_array();
    }

    /**
     * Get user by email
     *
     * @param string $email User email
     * @return array|null User data or null if not found
     */
    public function get_user_by_email(string $email): ?array
    {
        $query = $this->db->get_where($this->user_table, ['email' => $email]);
        return $query->row_array(); // Return user data or null
    }

    /**
     * Update user data
     *
     * @param int $user_id User ID
     * @param array $data User data to update
     * @return bool TRUE on success, FALSE on failure
     */
    public function update_user($id, $data, $userid)
    {
        // supplier data
        $user_data = [
            'full_name' => $data['full_name'],
            'email' => $data['email'],
            'phone_number' => $data['phone_number'],
            'role_id' => $data['role_id'],
            'status' => $data['status'],
        ];

        if ($this->db->where('id', $id)->update($this->user_table, $user_data)) {

            // Save Additional information as well
            $role_detail = $this->db->where('id', $data['role_id'])->get('roles')->row_array();

            if (strtolower($role_detail['role_name'] ?? '') === 'client')
                $this->save_client_additional_details($data, $id, $userid);
            else if (strtolower($role_detail['role_name'] ?? '') === 'developer')
                $this->save_developer_additional_details($data, $id, $userid);

            return $this->get_user_by_id($id);
        } else {
            return false;
        }
    }

    /**
     * Delete a user
     *
     * @param int $user_id User ID
     * @return bool TRUE on success, FALSE on failure
     */
    public function delete_user(int $user_id): bool
    {
        $this->db->where('id', $user_id);
        return $this->db->delete($this->user_table);
    }

    /**
     * Get all users
     *
     * @return array List of users
     */
    public function get_all_users(): array
    {
        $query = $this->db->get($this->user_table);
        return $query->result_array(); // Return array of user data
    }

    /**
     * Get all users by query
     *
     * @return array List of users
     */
    public function get_all_users_by_query(): array
    {
        $query = $this->db->query("SELECT * FROM users");
        return $query->result_array(); // Return array of user data
    }

    /**
     * Check if a user exists by user ID
     *
     * @param int $user_id User ID
     * @return bool TRUE if user exists, FALSE otherwise
     */
    public function validate_user(int $user_id)
    {
        $query = $this->db->get_where($this->user_table, ['id' => $user_id]);
        return $query->row(); // Return true if user exists
    }


    // Function to update user password
    function update_password($password, $userid)
    {
        // Generate the hashed password using ARGON2ID
        $hashedPassword = password_hash($password, PASSWORD_ARGON2ID);
        $this->db->where('id', $userid);
        return $this->db->update($this->user_table, ['password' => $hashedPassword]);
    }



    public function getUserDetail($user_id, $email)
    {
        // Query to fetch user details from the 'users' table
        $this->db->select('id,name, phone, email, user_id');
        $this->db->where('id', $user_id);
        $this->db->where('email', $email);
        $query = $this->db->get('users');


        // Check if user exists and return result
        if ($query->num_rows() > 0) {
            return $query->row();  // Return the first matching record
        } else {
            return null;  // No user found
        }
    }

    public function update_user_password($userID, $data, $created_by)
    {

        // Insert new lead
        return $this->db->where('id', $userID)->update($this->user_table, ['password' => password_hash($data['new_password'], PASSWORD_ARGON2ID)]);
    }

    // Get Users
    function get_users($type = 'list', $limit = 10, $currentPage = 1, $filters = [], $search = [])
    {
        $offset = get_limit_offset($currentPage, $limit);

        $this->db->select("u.id, u.user_id, u.full_name, u.email, u.phone_number, u.status, u.is_2fa_enabled, r.role_name, u.role_id, u.created_at");
        $this->db->from($this->user_table . " u");
        $this->db->join('roles r', 'r.id = u.role_id');
        $this->db->order_by("u.id", "DESC");

        // Apply filters dynamically from the $filters array
        if (!empty($filters) && is_array($filters)) {
            foreach ($filters as $key => $value) {
                $this->db->where("LOWER($key)", strtolower($value));
            }
        }


        // if (!empty($search) && is_array($search)) {
        //     if (isset($search['product'])) {
        //         $this->db->group_start(); // Begin group for OR conditions
        //         $this->db->like('p.PRODUCT_NAME', $search['product'], 'both', false);
        //         $this->db->or_like('p.PRODUCT_CODE', $search['product'], 'both', false);
        //         $this->db->group_end(); // End group for OR conditions
        //     }
        // }


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

    public function delete_user_by_id($userID)
    {
        $this->db->trans_start();

        $this->db->delete($this->client_table, array('user_id' => $userID));

        $this->db->delete($this->developer_table, array('user_id' => $userID));

        $this->db->delete($this->user_table, array('id' => $userID));

        $this->db->trans_complete();

        if ($this->db->trans_status() === FALSE) {
        } else {
            return true;
        }
    }
}
