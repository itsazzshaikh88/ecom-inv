<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Orders extends App_controller
{
    public function __construct()
    {
        parent::__construct();
    }

    public function index()
    {
        $this->isUserAuthenticated();
        $data['view_path'] = 'pages/orders/list';
        $data['navlink'] = ['mainlink' => 'orders', 'sublink' => ''];
        $data['scripts'] = ['assets/js/pages/orders/list.js'];
        $data['page_title'] = 'Orders';
        $this->load->view('layout', $data);
    }

    public function view()
    {
        $this->isUserAuthenticated();
        $data['view_path'] = 'pages/orders/view';
        $data['navlink'] = ['mainlink' => 'orders', 'sublink' => ''];
        $data['scripts'] = ['assets/js/pages/orders/view.js'];
        $this->load->view('layout', $data);
    }

    // List of users who have ordered items
    public function users()
    {
        $this->isUserAuthenticated();
        $data['view_path'] = 'pages/orders/users/list';
        $data['navlink'] = ['mainlink' => 'users', 'sublink' => ''];
        $data['scripts'] = ['assets/js/pages/orders/users/list.js'];
        $this->load->view('layout', $data);
    }

    // =========================== API Functions ===========================
    function list()
    {
        // Check if the authentication is valid
        $isAuthorized = $this->isAuthorized();
        if (!$isAuthorized['status']) {
            $this->output
                ->set_status_header(401) // Set HTTP response status to 400 Bad Request
                ->set_content_type('application/json')
                ->set_output(json_encode(['message' => 'Unauthorized access. You do not have permission to perform this action.']))
                ->_display();
            exit;
        };

        // Get the raw input data from the request
        $input = $this->input->raw_input_stream;

        // Decode the JSON data
        $data = json_decode($input, true); // Decode as associative array

        // Check if data is received
        if (!$data) {
            // Handle the error if no data is received
            $this->output
                ->set_status_header(400) // Set HTTP response status to 400 Bad Request
                ->set_content_type('application/json')
                ->set_output(json_encode(['message' => 'Invalid JSON input']))
                ->_display();
            exit;
        }

        // Access the parameters
        $limit = isset($data['limit']) ? $data['limit'] : null;
        $currentPage = isset($data['currentPage']) ? $data['currentPage'] : null;
        $filters = isset($data['filters']) ? $data['filters'] : [];
        $search = isset($data['search']) ? $data['search'] : [];

        $total_orders = $this->Order_model->get_orders('total', $limit, $currentPage, $filters, $search);
        $orders = $this->Order_model->get_orders('list', $limit, $currentPage, $filters, $search);

        $response = [
            'pagination' => [
                'total_records' => $total_orders,
                'total_pages' => generatePages($total_orders, $limit),
                'current_page' => $currentPage,
                'limit' => $limit
            ],
            'orders' => $orders,
        ];
        return $this->output
            ->set_content_type('application/json')
            ->set_status_header(200)
            ->set_output(json_encode($response));
    }


    public function details($orderID)
    {
        // Check if the authentication is valid
        $isAuthorized = $this->isAuthorized();
        if (!$isAuthorized['status']) {
            $this->output
                ->set_status_header(401) // Set HTTP response status to 400 Bad Request
                ->set_content_type('application/json')
                ->set_output(json_encode(['message' => 'Unauthorized access. You do not have permission to perform this action.']))
                ->_display();
            exit;
        };

        // Validate input and check if `order ID` is provided
        if (!isset($orderID)) {
            return $this->output
                ->set_status_header(400)
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'status' => 'error',
                    'code' => 400,
                    'message' => 'Invalid order ID, Please provide order id to fetch details.'
                ]));
        }

        $order = $this->Order_model->get_order_details_by_key("id", $orderID);

        // Check if order data exists
        if (empty($order)) {
            return $this->output
                ->set_status_header(404)
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'status' => 'error',
                    'code' => 404,
                    'message' => 'order details not found.'
                ]));
        }

        // Successful response with order data
        return $this->output
            ->set_status_header(200)
            ->set_content_type('application/json')
            ->set_output(json_encode([
                'status' => 'success',
                'code' => 200,
                'message' => 'order details retrieved successfully',
                'data' => $order
            ]));
    }

    function update($id)
    {
        // Check if the authentication is valid
        $isAuthorized = $this->isAuthorized();
        if (!$isAuthorized['status']) {
            $this->output
                ->set_status_header(401) // Set HTTP response status to 400 Bad Request
                ->set_content_type('application/json')
                ->set_output(json_encode(['message' => 'Unauthorized access. You do not have permission to perform this action.']))
                ->_display();
            exit;
        };

        try {
            // Check if the request method is POST
            if (strtolower($this->input->method()) !== 'post') {
                $this->sendHTTPResponse(405, [
                    'status' => 405,
                    'error' => 'Method Not Allowed',
                    'message' => 'The requested HTTP method is not allowed for this endpoint. Please check the API documentation for allowed methods.'
                ]);
                return;
            }

            // Set validation rules
            $validation_rules = [
                ['label' => 'Category Name', 'key' => 'name', 'validations' => 'required'],
                ['label' => 'Status', 'key' => 'is_active', 'validations' => 'required']
            ];
            foreach ($validation_rules as $rule)
                $this->form_validation->set_rules($rule['key'], $rule['label'], $rule['validations']);

            // Run validation
            if ($this->form_validation->run() == FALSE) {
                // Validation failed, prepare response with errors
                $errors = $this->form_validation->error_array();

                $this->sendHTTPResponse(422, [
                    'status' => 422,
                    'error' => 'Unprocessable Entity',
                    'message' => 'The submitted data failed validation.',
                    'validation_errors' => $errors
                ]);
                return;
            }

            // Retrieve POST data and sanitize it
            $data = $this->input->post();
            $data = array_map([$this->security, 'xss_clean'], $data);


            // Check data is already created with given filter
            $category = $this->Order_model->get_category_by_id($id);
            if (empty($category)) {
                $this->sendHTTPResponse(404, [
                    'status' => 'error',
                    'code' => 404,
                    'error' => 'category details not found with provided ID',
                    'message' => 'category details not found with provided ID'
                ]);
                return;
            }

            $allowedTypes = 'jpg|jpeg|png|gif|webp|svg';

            // Process image_url file upload
            $data['image_url'] = upload_single_file('image_url', './uploads/categories/', $allowedTypes);
            $data['banner_image_url'] = upload_single_file('banner_image_url', './uploads/categories/', $allowedTypes);

            // Save Data to the order table
            $updatedCategory = $this->Order_model->update_category($id, $data, $isAuthorized['userid']);

            if ($updatedCategory) {
                $this->sendHTTPResponse(201, [
                    'status' => 201,
                    'message' => "Category [" . $updatedCategory['name'] . "] Updated Successfully.",
                    'type' => 'update',
                    'data' => $updatedCategory,
                ]);
            } else {
                throw new Exception('Failed to update category details.');
            }
        } catch (Exception $e) {
            // Catch any unexpected errors and respond with a standardized error
            $this->sendHTTPResponse(500, [
                'status' => 500,
                'error' => 'Internal Server Error',
                'message' => 'An unexpected error occurred on the server.',
                'details' => $e->getMessage()
            ]);
        }
    }

    function delete($id)
    {
        // Check if the authentication is valid
        $isAuthorized = $this->isAuthorized();
        if (!$isAuthorized['status']) {
            $this->output
                ->set_status_header(401) // Set HTTP response status to 400 Bad Request
                ->set_content_type('application/json')
                ->set_output(json_encode(['message' => 'Unauthorized access. You do not have permission to perform this action.']))
                ->_display();
            exit;
        };

        // Validate the Request ID
        if (empty($id) || !is_numeric($id)) {
            $this->output
                ->set_content_type('application/json')
                ->set_status_header(400) // 400 Bad Request status code
                ->set_output(json_encode(['message' => 'Invalid order ID.']));
            return;
        }

        // Attempt to delete the Request
        $order = $this->Order_model->get_order_by_key("id", $id);
        $result = $this->Order_model->delete_order_by_id($id);
        if ($result) {
            $this->output
                ->set_content_type('application/json')
                ->set_status_header(200) // 200 OK status code
                ->set_output(json_encode(['status' => true, 'message' => "order deleted successfully."]));
        } else {
            $this->output
                ->set_content_type('application/json')
                ->set_status_header(500) // 500 Internal Server Error status code
                ->set_output(json_encode(['status' => false, 'message' => 'Failed to delete order.']));
        }
    }

    function update_order_status($id)
    {
        // Check if the authentication is valid
        $isAuthorized = $this->isAuthorized();
        if (!$isAuthorized['status']) {
            $this->output
                ->set_status_header(401) // Set HTTP response status to 400 Bad Request
                ->set_content_type('application/json')
                ->set_output(json_encode(['message' => 'Unauthorized access. You do not have permission to perform this action.']))
                ->_display();
            exit;
        };

        try {
            // Check if the request method is POST
            if (strtolower($this->input->method()) !== 'post') {
                $this->sendHTTPResponse(405, [
                    'status' => 405,
                    'error' => 'Method Not Allowed',
                    'message' => 'The requested HTTP method is not allowed for this endpoint. Please check the API documentation for allowed methods.'
                ]);
                return;
            }

            // Set validation rules
            $validation_rules = [
                ['label' => 'Status', 'key' => 'status', 'validations' => 'required'],
                ['label' => 'Payment Status', 'key' => 'payment_status', 'validations' => 'required']
            ];
            foreach ($validation_rules as $rule)
                $this->form_validation->set_rules($rule['key'], $rule['label'], $rule['validations']);

            // Run validation
            if ($this->form_validation->run() == FALSE) {
                // Validation failed, prepare response with errors
                $errors = $this->form_validation->error_array();

                $this->sendHTTPResponse(422, [
                    'status' => 422,
                    'error' => 'Unprocessable Entity',
                    'message' => 'The submitted data failed validation.',
                    'validation_errors' => $errors
                ]);
                return;
            }

            // Retrieve POST data and sanitize it
            $data = $this->input->post();
            $data = array_map([$this->security, 'xss_clean'], $data);


            // Check data is already created with given filter
            $order = $this->Order_model->get_order_by_key("id", $id);
            if (empty($order)) {
                $this->sendHTTPResponse(404, [
                    'status' => 'error',
                    'code' => 404,
                    'error' => 'order details not found with provided ID',
                    'message' => 'order details not found with provided ID'
                ]);
                return;
            }

            // Save Data to the order table
            $updatedOrder = $this->Order_model->update_order_status($id, $data, $isAuthorized['userid']);

            if ($updatedOrder) {
                $this->sendHTTPResponse(201, [
                    'status' => 201,
                    'message' => "Order Updated Successfully.",
                    'type' => 'update',
                    'data' => $updatedOrder,
                ]);
            } else {
                throw new Exception('Failed to update order details.');
            }
        } catch (Exception $e) {
            // Catch any unexpected errors and respond with a standardized error
            $this->sendHTTPResponse(500, [
                'status' => 500,
                'error' => 'Internal Server Error',
                'message' => 'An unexpected error occurred on the server.',
                'details' => $e->getMessage()
            ]);
        }
    }


    function users_list()
    {
        // Check if the authentication is valid
        $isAuthorized = $this->isAuthorized();
        if (!$isAuthorized['status']) {
            $this->output
                ->set_status_header(401) // Set HTTP response status to 400 Bad Request
                ->set_content_type('application/json')
                ->set_output(json_encode(['message' => 'Unauthorized access. You do not have permission to perform this action.']))
                ->_display();
            exit;
        };

        // Get the raw input data from the request
        $input = $this->input->raw_input_stream;

        // Decode the JSON data
        $data = json_decode($input, true); // Decode as associative array

        // Check if data is received
        if (!$data) {
            // Handle the error if no data is received
            $this->output
                ->set_status_header(400) // Set HTTP response status to 400 Bad Request
                ->set_content_type('application/json')
                ->set_output(json_encode(['message' => 'Invalid JSON input']))
                ->_display();
            exit;
        }

        // Access the parameters
        $limit = isset($data['limit']) ? $data['limit'] : null;
        $currentPage = isset($data['currentPage']) ? $data['currentPage'] : null;
        $filters = isset($data['filters']) ? $data['filters'] : [];
        $search = isset($data['search']) ? $data['search'] : [];

        $total_users = $this->User_model->get_app_users('total', $limit, $currentPage, $filters, $search);
        $users = $this->User_model->get_app_users('list', $limit, $currentPage, $filters, $search);

        $response = [
            'pagination' => [
                'total_records' => $total_users,
                'total_pages' => generatePages($total_users, $limit),
                'current_page' => $currentPage,
                'limit' => $limit
            ],
            'users' => $users,
        ];
        return $this->output
            ->set_content_type('application/json')
            ->set_status_header(200)
            ->set_output(json_encode($response));
    }
}
