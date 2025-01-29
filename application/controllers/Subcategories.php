<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Subcategories extends App_controller
{
    public function __construct()
    {
        parent::__construct();
    }

    public function index()
    {
        $this->isUserAuthenticated();
        $data['view_path'] = 'pages/subcategories/list';
        $data['navlink'] = ['mainlink' => 'subcategories', 'sublink' => ''];
        $data['scripts'] = ['assets/js/pages/subcategories/list.js', 'assets/js/pages/subcategories/new.js'];
        $data['page_title'] = 'Subcategory Management';
        $this->load->view('layout', $data);
    }

    public function view()
    {
        $this->isUserAuthenticated();
        $data['view_path'] = 'pages/subcategories/view';
        $data['navlink'] = ['mainlink' => 'subcategories', 'sublink' => ''];
        $data['scripts'] = ['assets/js/pages/subcategories/view.js'];
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

        $total_subcategories = $this->Subcategory_model->get_subcategories('total', $limit, $currentPage, $filters, $search);
        $subcategories = $this->Subcategory_model->get_subcategories('list', $limit, $currentPage, $filters, $search);

        $response = [
            'pagination' => [
                'total_records' => $total_subcategories,
                'total_pages' => generatePages($total_subcategories, $limit),
                'current_page' => $currentPage,
                'limit' => $limit
            ],
            'subcategories' => $subcategories,
        ];
        return $this->output
            ->set_content_type('application/json')
            ->set_status_header(200)
            ->set_output(json_encode($response));
    }

    function add()
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
                ['label' => 'Subcategory Name', 'key' => 'name', 'validations' => 'required'],
                ['label' => 'Status', 'key' => 'is_active', 'validations' => 'required'],
                ['label' => 'Category ID', 'key' => 'category_id', 'validations' => 'required'],
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
            $subcategory = $this->Subcategory_model->get_subcategory_by_name($data['name']);
            if (!empty($subcategory)) {
                $this->sendHTTPResponse(409, [
                    'status' => 'error',
                    'code' => 409,
                    'error' => 'Subcategory is already defined in the system.',
                    'message' => 'Subcategory is already defined in the system.'
                ]);
                return;
            }

            $allowedTypes = 'jpg|jpeg|png|gif|webp|svg';

            // Process image_url file upload
            $data['image_url'] = upload_single_file('image_url', './uploads/subcategories/', $allowedTypes);
            $data['banner_image_url'] = upload_single_file('banner_image_url', './uploads/subcategories/', $allowedTypes);

            // Save Data to the product table
            $createdSubCategory = $this->Subcategory_model->add_subcategory($data, $isAuthorized['userid']);

            if ($createdSubCategory) {
                $this->sendHTTPResponse(201, [
                    'status' => 201,
                    'message' => "Subcategory Saved Successfully.",
                    'type' => 'insert',
                    'data' => $createdSubCategory,
                ]);
            } else {
                throw new Exception('Failed to create new subcategory.');
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

    public function details($subcategoryID)
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

        // Validate input and check if `productUUID` is provided
        if (!isset($subcategoryID)) {
            return $this->output
                ->set_status_header(400)
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'status' => 'error',
                    'code' => 400,
                    'message' => 'Invalid subcategory ID, Please provide subcategory id to fetch details.'
                ]));
        }

        $subcategory = $this->Subcategory_model->get_subcategory_by_id($subcategoryID);

        // Check if product data exists
        if (empty($subcategory)) {
            return $this->output
                ->set_status_header(404)
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'status' => 'error',
                    'code' => 404,
                    'message' => 'subcategory details not found.'
                ]));
        }

        // Successful response with product data
        return $this->output
            ->set_status_header(200)
            ->set_content_type('application/json')
            ->set_output(json_encode([
                'status' => 'success',
                'code' => 200,
                'message' => 'subcategory details retrieved successfully',
                'data' => $subcategory
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
                ['label' => 'Subcategory Name', 'key' => 'name', 'validations' => 'required'],
                ['label' => 'Status', 'key' => 'is_active', 'validations' => 'required'],
                ['label' => 'Category ID', 'key' => 'category_id', 'validations' => 'required'],
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
            $subcategory = $this->Subcategory_model->get_subcategory_by_id($id);
            if (empty($subcategory)) {
                $this->sendHTTPResponse(404, [
                    'status' => 'error',
                    'code' => 404,
                    'error' => 'subcategory details not found with provided ID',
                    'message' => 'subcategory details not found with provided ID'
                ]);
                return;
            }

            $allowedTypes = 'jpg|jpeg|png|gif|webp|svg';

            // Process image_url file upload
            $data['image_url'] = upload_single_file('image_url', './uploads/subcategories/', $allowedTypes);
            $data['banner_image_url'] = upload_single_file('banner_image_url', './uploads/subcategories/', $allowedTypes);

            // Save Data to the product table
            $updatedCategory = $this->Subcategory_model->update_subcategory($id, $data, $isAuthorized['userid']);

            if ($updatedCategory) {
                $this->sendHTTPResponse(201, [
                    'status' => 201,
                    'message' => "Subcategory [" . $updatedCategory['name'] . "] Updated Successfully.",
                    'type' => 'update',
                    'data' => $updatedCategory,
                ]);
            } else {
                throw new Exception('Failed to update subcategory details.');
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

        // Check if the user is admin or not
        if (isset($isAuthorized['role']) && strtolower($isAuthorized['role']) !== 'admin') {
            $this->output
                ->set_content_type('application/json')
                ->set_status_header(403) // 403 Forbidden status code
                ->set_output(json_encode(['message' => 'You do not have permission to perform this action.']));
            return;
        }

        // Validate the Request ID
        if (empty($id) || !is_numeric($id)) {
            $this->output
                ->set_content_type('application/json')
                ->set_status_header(400) // 400 Bad Request status code
                ->set_output(json_encode(['message' => 'Invalid subcategory ID.']));
            return;
        }

        // Attempt to delete the Request
        $subcategory = $this->Subcategory_model->get_subcategory_by_id($id);
        $result = $this->Subcategory_model->delete_subcategory_by_id($id);
        if ($result) {
            $this->output
                ->set_content_type('application/json')
                ->set_status_header(200) // 200 OK status code
                ->set_output(json_encode(['status' => true, 'message' => "Subcategory $subcategory[name] deleted successfully."]));
        } else {
            $this->output
                ->set_content_type('application/json')
                ->set_status_header(500) // 500 Internal Server Error status code
                ->set_output(json_encode(['status' => false, 'message' => 'Failed to delete subcategory.']));
        }
    }
}
