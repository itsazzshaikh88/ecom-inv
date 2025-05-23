<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Products extends App_controller
{
    public function __construct()
    {
        parent::__construct();
    }

    public function index()
    {
        $this->isUserAuthenticated();
        $data['view_path'] = 'pages/products/list';
        $data['navlink'] = ['mainlink' => 'products', 'sublink' => ''];
        $data['scripts'] = ['assets/js/pages/products/list.js'];
        $data['page_title'] = 'Products';
        $this->load->view('layout', $data);
    }
    public function new()
    {
        $this->isUserAuthenticated();
        $data['view_path'] = 'pages/products/new';
        $data['navlink'] = ['mainlink' => 'products', 'sublink' => ''];
        $data['scripts'] = ['assets/js/pages/products/new.js'];
        $data['page_title'] = 'Product Management';
        $this->load->view('layout', $data);
    }

    public function view()
    {
        $this->isUserAuthenticated();
        $data['view_path'] = 'pages/categories/view';
        $data['navlink'] = ['mainlink' => 'categories', 'sublink' => ''];
        $data['scripts'] = ['assets/js/pages/categories/view.js'];
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

        $total_products = $this->Product_model->get_products('total', $limit, $currentPage, $filters, $search);
        $products = $this->Product_model->get_products('list', $limit, $currentPage, $filters, $search);

        $response = [
            'pagination' => [
                'total_records' => $total_products,
                'total_pages' => generatePages($total_products, $limit),
                'current_page' => $currentPage,
                'limit' => $limit
            ],
            'products' => $products,
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
                ['label' => 'Category', 'key' => 'category_id', 'validations' => 'required'],
                ['label' => 'Product Name', 'key' => 'name', 'validations' => 'required'],
                ['label' => 'Status', 'key' => 'is_active', 'validations' => 'required'],
                ['label' => 'Stock Qty', 'key' => 'stock_quantity', 'validations' => 'required'],
                ['label' => 'Low Stock Value', 'key' => 'low_stock_threshold', 'validations' => 'required'],
                ['label' => 'Unit of Measurement', 'key' => 'uoms', 'validations' => 'required'],
                ['label' => 'Qty / Measure', 'key' => 'qty_measure', 'validations' => 'required'],
                ['label' => 'Product Price', 'key' => 'product_price', 'validations' => 'required'],
                ['label' => 'Selling Price', 'key' => 'selling_price', 'validations' => 'required'],
            ];
            foreach ($validation_rules as $rule)
                $this->form_validation->set_rules($rule['key'], $rule['label'], $rule['validations']);


            // Retrieve POST data and sanitize it
            $data = $this->input->post();
            $data = array_map([$this->security, 'xss_clean'], $data);

            // Upload Files
            // Directory to upload files
            $uploadPath = './uploads/product_images/';
            $allowedTypes = ['jpg', 'jpeg', 'png', 'gif', 'svg', 'webp', 'JPG', 'JPEG', 'PNG', 'GIF', 'SVG', 'WEBP'];
            $uploadedFiles = null;

            // Check if files are attached
            if (!empty($_FILES['product_image']['name'][0])) {
                $uploadedFiles = upload_multiple_files($_FILES['product_image'], $uploadPath, $allowedTypes);
            }

            $data['product_images'] = $uploadedFiles ?? [];


            // Save Data to the product table
            $createdProduct = $this->Product_model->add_product($data, $isAuthorized['userid']);

            if ($createdProduct) {
                $this->sendHTTPResponse(201, [
                    'status' => 201,
                    'message' => "Product Saved Successfully.",
                    'type' => 'insert',
                    'data' => $createdProduct,
                ]);
            } else {
                throw new Exception('Failed to create new Product.');
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

    public function details($productID)
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
        if (!isset($productID)) {
            return $this->output
                ->set_status_header(400)
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'status' => 'error',
                    'code' => 400,
                    'message' => 'Invalid product ID, Please provide product id to fetch details.'
                ]));
        }

        $product = $this->Product_model->get_product_by_id($productID);

        // Check if product data exists
        if (empty($product)) {
            return $this->output
                ->set_status_header(404)
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'status' => 'error',
                    'code' => 404,
                    'message' => 'product details not found.'
                ]));
        }

        // Successful response with product data
        return $this->output
            ->set_status_header(200)
            ->set_content_type('application/json')
            ->set_output(json_encode([
                'status' => 'success',
                'code' => 200,
                'message' => 'product details retrieved successfully',
                'data' => $product
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
                ['label' => 'Category', 'key' => 'category_id', 'validations' => 'required'],
                ['label' => 'Product Name', 'key' => 'name', 'validations' => 'required'],
                ['label' => 'Status', 'key' => 'is_active', 'validations' => 'required'],
                ['label' => 'Stock Qty', 'key' => 'stock_quantity', 'validations' => 'required'],
                ['label' => 'Low Stock Value', 'key' => 'low_stock_threshold', 'validations' => 'required'],
                ['label' => 'Unit of Measurement', 'key' => 'uoms', 'validations' => 'required'],
                ['label' => 'Qty / Measure', 'key' => 'qty_measure', 'validations' => 'required'],
                ['label' => 'Product Price', 'key' => 'product_price', 'validations' => 'required'],
                ['label' => 'Selling Price', 'key' => 'selling_price', 'validations' => 'required'],
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
            $product = $this->Product_model->get_product_by_id($id);
            if (empty($product)) {
                $this->sendHTTPResponse(404, [
                    'status' => 'error',
                    'code' => 404,
                    'error' => 'product details not found with provided ID',
                    'message' => 'product details not found with provided ID'
                ]);
                return;
            }

            // Upload Files
            // Directory to upload files
            $uploadPath = './uploads/product_images/';
            $allowedTypes = ['jpg', 'jpeg', 'png', 'gif', 'svg', 'webp', 'JPG', 'JPEG', 'PNG', 'GIF', 'SVG', 'WEBP'];
            $uploadedFiles = null;

            // Check if files are attached
            if (!empty($_FILES['product_image']['name'][0])) {
                $uploadedFiles = upload_multiple_files($_FILES['product_image'], $uploadPath, $allowedTypes);
            }

            $data['product_images'] = $uploadedFiles ?? [];

            // Save Data to the product table
            $updateProduct = $this->Product_model->update_product($id, $data, $isAuthorized['userid']);

            if ($updateProduct) {
                $this->sendHTTPResponse(201, [
                    'status' => 201,
                    'message' => "Category [" . $updateProduct['name'] . "] Updated Successfully.",
                    'type' => 'update',
                    'data' => $updateProduct,
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
                ->set_output(json_encode(['message' => 'Invalid product ID.']));
            return;
        }

        // Attempt to delete the Request
        $product = $this->Product_model->get_product_by_id($id);
        $result = $this->Product_model->delete_product_by_id($id);
        if ($result) {
            $this->output
                ->set_content_type('application/json')
                ->set_status_header(200) // 200 OK status code
                ->set_output(json_encode(['status' => true, 'message' => "Category $product[name] deleted successfully."]));
        } else {
            $this->output
                ->set_content_type('application/json')
                ->set_status_header(500) // 500 Internal Server Error status code
                ->set_output(json_encode(['status' => false, 'message' => 'Failed to delete category.']));
        }
    }
}
