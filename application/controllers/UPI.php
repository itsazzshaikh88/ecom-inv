<?php
defined('BASEPATH') or exit('No direct script access allowed');

class UPI extends App_controller
{
    public function __construct()
    {
        parent::__construct();
    }

    public function index()
    {
        $this->isUserAuthenticated();
        $data['view_path'] = 'pages/upi/list';
        $data['navlink'] = ['mainlink' => 'upi', 'sublink' => ''];
        $data['scripts'] = ['assets/js/pages/upi/list.js', 'assets/js/pages/upi/new.js'];
        $data['page_title'] = 'Management';
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

        $total_upi = $this->UPI_model->get_upi('total', $limit, $currentPage, $filters, $search);
        $upi = $this->UPI_model->get_upi('list', $limit, $currentPage, $filters, $search);

        $response = [
            'pagination' => [
                'total_records' => $total_upi,
                'total_pages' => generatePages($total_upi, $limit),
                'current_page' => $currentPage,
                'limit' => $limit
            ],
            'upi' => $upi,
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
                ['label' => 'UPI Name', 'key' => 'upi_name', 'validations' => 'required'],
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
            $upi = $this->UPI_model->get_upi_by_name($data['upi_name']);
            if (!empty($upi)) {
                $this->sendHTTPResponse(409, [
                    'status' => 'error',
                    'code' => 409,
                    'error' => 'UPI is already defined in the system.',
                    'message' => 'UPI is already defined in the system.'
                ]);
                return;
            }

            $allowedTypes = 'jpg|jpeg|png|gif|webp|svg';

            // Process qr_code_image file upload
            $data['qr_code_image'] = upload_single_file('qr_code_image', './uploads/upi/', $allowedTypes);

            // Save Data to the product table
            $createdUPI = $this->UPI_model->add_upi($data, $isAuthorized['userid']);

            if ($createdUPI) {
                $this->sendHTTPResponse(201, [
                    'status' => 201,
                    'message' => "UPI Saved Successfully.",
                    'type' => 'insert',
                    'data' => $createdUPI,
                ]);
            } else {
                throw new Exception('Failed to create new upi.');
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

    public function details($upiID)
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
        if (!isset($upiID)) {
            return $this->output
                ->set_status_header(400)
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'status' => 'error',
                    'code' => 400,
                    'message' => 'Invalid upi ID, Please provide upi id to fetch details.'
                ]));
        }

        $upi = $this->UPI_model->get_upi_by_id($upiID);

        // Check if product data exists
        if (empty($upi)) {
            return $this->output
                ->set_status_header(404)
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'status' => 'error',
                    'code' => 404,
                    'message' => 'upi details not found.'
                ]));
        }

        // Successful response with product data
        return $this->output
            ->set_status_header(200)
            ->set_content_type('application/json')
            ->set_output(json_encode([
                'status' => 'success',
                'code' => 200,
                'message' => 'upi details retrieved successfully',
                'data' => $upi
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
                ['label' => 'UPI Name', 'key' => 'upi_name', 'validations' => 'required'],
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
            $upi = $this->UPI_model->get_upi_by_id($id);
            if (empty($upi)) {
                $this->sendHTTPResponse(404, [
                    'status' => 'error',
                    'code' => 404,
                    'error' => 'upi details not found with provided ID',
                    'message' => 'upi details not found with provided ID'
                ]);
                return;
            }

            $allowedTypes = 'jpg|jpeg|png|gif|webp|svg';

            // Process qr_code_image file upload
            $data['qr_code_image'] = upload_single_file('qr_code_image', './uploads/upi/', $allowedTypes);

            // Save Data to the product table
            $updatedUPI = $this->UPI_model->update_upi($id, $data, $isAuthorized['userid']);

            if ($updatedUPI) {
                $this->sendHTTPResponse(201, [
                    'status' => 201,
                    'message' => "UPI [" . $updatedUPI['upi_name'] . "] Updated Successfully.",
                    'type' => 'update',
                    'data' => $updatedUPI,
                ]);
            } else {
                throw new Exception('Failed to update upi details.');
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
                ->set_output(json_encode(['message' => 'Invalid upi ID.']));
            return;
        }

        // Attempt to delete the Request
        $upi = $this->UPI_model->get_upi_by_id($id);
        $result = $this->UPI_model->delete_upi_by_id($id);
        if ($result) {
            $this->output
                ->set_content_type('application/json')
                ->set_status_header(200) // 200 OK status code
                ->set_output(json_encode(['status' => true, 'message' => "UPI $upi[upi_name] deleted successfully."]));
        } else {
            $this->output
                ->set_content_type('application/json')
                ->set_status_header(500) // 500 Internal Server Error status code
                ->set_output(json_encode(['status' => false, 'message' => 'Failed to delete upi.']));
        }
    }
}
