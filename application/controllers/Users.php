<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Users extends App_controller
{
	public function __construct()
	{
		parent::__construct();
	}

	public function index()
	{
		$this->isUserAuthenticated();
		$data['view_path'] = 'pages/users/list';
		$data['navlink'] = ['mainlink' => 'users', 'sublink' => ''];
		$data['scripts'] = ['assets/js/pages/users/list.js'];
		$this->load->view('layout', $data);
	}

	public function new()
	{
		$this->isUserAuthenticated();
		$data['view_path'] = 'pages/users/new';
		$data['navlink'] = ['mainlink' => 'users', 'sublink' => ''];
		$data['scripts'] = ['assets/js/pages/users/new.js'];
		$this->load->view('layout', $data);
	}

	public function view()
	{
		$this->isUserAuthenticated();
		$data['view_path'] = 'pages/users/view';
		$data['navlink'] = ['mainlink' => 'users', 'sublink' => ''];
		$data['scripts'] = ['assets/js/pages/users/view.js'];
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

		$total_users = $this->User_model->get_users('total', $limit, $currentPage, $filters, $search);
		$users = $this->User_model->get_users('list', $limit, $currentPage, $filters, $search);

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
				['label' => 'Name', 'key' => 'full_name', 'validations' => 'required'],
				['label' => 'Email Address', 'key' => 'email', 'validations' => 'required|valid_email'],
				['label' => 'Contact Number', 'key' => 'phone_number', 'validations' => 'required'],
				['label' => 'Role', 'key' => 'role_id', 'validations' => 'required'],
				['label' => 'Status ', 'key' => 'status', 'validations' => 'required'],
				['label' => 'Password ', 'key' => 'password', 'validations' => 'required'],
				// ['label' => 'Address', 'key' => 'address', 'validations' => 'required'],
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
			$user = $this->User_model->get_user_by_email($data['email']);
			if (!empty($user)) {
				$this->sendHTTPResponse(409, [
					'status' => 'error',
					'code' => 409,
					'error' => 'User with this email already exists.',
					'message' => 'User with this email already exists.'
				]);
				return;
			}


			// Save Data to the product table
			$createduser = $this->User_model->add_user($data, $isAuthorized['userid']);

			if ($createduser) {
				$this->sendHTTPResponse(201, [
					'status' => 201,
					'message' => "User Saved Successfully.",
					'type' => 'insert',
					'data' => $createduser,
				]);
			} else {
				throw new Exception('Failed to create new User account.');
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

	public function details($userID)
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
		if (!isset($userID)) {
			return $this->output
				->set_status_header(400)
				->set_content_type('application/json')
				->set_output(json_encode([
					'status' => 'error',
					'code' => 400,
					'message' => 'Invalid user ID, Please provide user id to fetch details.'
				]));
		}

		$user = $this->User_model->get_user_by_id($userID);

		// Check if product data exists
		if (empty($user)) {
			return $this->output
				->set_status_header(404)
				->set_content_type('application/json')
				->set_output(json_encode([
					'status' => 'error',
					'code' => 404,
					'message' => 'user details not found.'
				]));
		}

		// Successful response with product data
		return $this->output
			->set_status_header(200)
			->set_content_type('application/json')
			->set_output(json_encode([
				'status' => 'success',
				'code' => 200,
				'message' => 'user details retrieved successfully',
				'data' => $user
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
				['label' => 'Name', 'key' => 'full_name', 'validations' => 'required'],
				['label' => 'Email Address', 'key' => 'email', 'validations' => 'required|valid_email'],
				['label' => 'Contact Number', 'key' => 'phone_number', 'validations' => 'required'],
				['label' => 'Role', 'key' => 'role_id', 'validations' => 'required'],
				['label' => 'Status ', 'key' => 'status', 'validations' => 'required']
				// ['label' => 'Address', 'key' => 'address', 'validations' => 'required'],
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
			$user = $this->User_model->get_user_by_id($id);
			if (empty($user)) {
				$this->sendHTTPResponse(404, [
					'status' => 'error',
					'code' => 404,
					'error' => 'user details not found with provided ID',
					'message' => 'user details not found with provided ID'
				]);
				return;
			}


			// Save Data to the product table
			$updateduser = $this->User_model->update_user($id, $data, $isAuthorized['userid']);

			if ($updateduser) {
				$this->sendHTTPResponse(201, [
					'status' => 201,
					'message' => "User [" . $updateduser['user_id'] . "] Updated Successfully.",
					'type' => 'update',
					'data' => $updateduser,
				]);
			} else {
				throw new Exception('Failed to update user details.');
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
				->set_output(json_encode(['message' => 'Invalid user ID.']));
			return;
		}

		// Attempt to delete the Request
		$user = $this->User_model->get_user_by_id($id);
		$result = $this->User_model->delete_user_by_id($id);
		if ($result) {
			$this->output
				->set_content_type('application/json')
				->set_status_header(200) // 200 OK status code
				->set_output(json_encode(['status' => true, 'message' => "user $user[user_id] deleted successfully."]));
		} else {
			$this->output
				->set_content_type('application/json')
				->set_status_header(500) // 500 Internal Server Error status code
				->set_output(json_encode(['status' => false, 'message' => 'Failed to delete user.']));
		}
	}
}
