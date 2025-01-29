<?php
defined('BASEPATH') or exit('No direct script access allowed');

class App_controller extends CI_Controller
{
    private $secret_key;
    protected $userDetails;
    public function __construct()
    {
        parent::__construct();
        $this->secret_key = SECRET_KEY;
        $this->userDetails = $this->getUserDetails();
        // Share user details with all views
        $this->load->vars(['loggedInUser' => $this->userDetails]);
    }

    protected function isAuthorized()
    {
        // Get the Authorization header
        $headers = $this->input->request_headers();

        // Check if the Authorization header exists and is not empty
        if (!isset($headers['Authorization']) || empty(trim($headers['Authorization']))) {
            $this->sendHTTPResponse(401, [
                'status' => 401,
                'error' => 'Authorization Header Missing',
                'message' => 'The Authorization header is missing from the request or does not contain a token. Please include the header to access the API.'
            ]);
            return ['status' => false];
        }

        // Extract the token
        $authHeader = trim($headers['Authorization']); // Trim to avoid extra spaces
        // Check for valid token format
        if (strpos($authHeader, ' ') === false) {
            // If there is no space in the header, it's not valid
            $this->sendHTTPResponse(401, [
                'status' => 401,
                'error' => 'Invalid Token Format',
                'message' => 'The Authorization header must be in the format "Bearer <token>".'
            ]);
            return ['status' => false];
        }

        // Assuming the format is "Bearer <token>"
        list($type, $token) = explode(" ", $authHeader, 2);

        // Check if the token type is Bearer
        if (strcasecmp($type, 'Bearer') !== 0) {
            $this->sendHTTPResponse(401, [
                'status' => 401,
                'error' => 'Invalid Token Type',
                'message' => "The token type must be 'Bearer'. Please provide a valid authorization token."
            ]);
            return ['status' => false];
        }

        // Query the database to check for the token and get type from users
        $this->db->select('t.token, t.expiry, t.user_id, r.role_name');
        $this->db->from('auth_tokens t');
        $this->db->join('users u', 't.user_id = u.id');
        $this->db->join('roles r', 'r.id = u.role_id');
        $this->db->where('t.token', $token);
        $this->db->where('t.token_type', 'auth');
        $query = $this->db->get();

        // Check if the token exists and is valid
        if ($query->num_rows() === 0) {
            $this->sendHTTPResponse(401, [
                'status' => 401,
                'error' => 'Token Not Found',
                'message' => "The provided token is invalid or does not exist. Please check the token and try again."
            ]);
            return ['status' => false];
        }

        $row = $query->row();
        // Check if the token is expired
        if (time() >= $row->expiry) {
            $this->sendHTTPResponse(401, [
                'status' => 401,
                'error' => 'Token Expired',
                'message' => "The token has expired. Please obtain a new token to continue accessing the API."
            ]);
            return ['status' => false];
        }

        return ['status' => true, 'userid' => $row->user_id, 'role' => $row->role_name];
    }

    protected function isUserAuthenticated()
    {
        // Retrieve the auth token from the cookie
        $auth_token = $this->input->cookie('userTaskAuthToken');
        if (!$auth_token) {
            // If no token is found, redirect to login
            redirect(base_url() . 'auth/login');
        } else {

            $tokenData = $this->Auth_model->validate_token($auth_token);
            if (empty($tokenData) || time() > $tokenData['expiry']) {
                // If token is not valid then delete cookie and redirect
                delete_cookie('userTaskAuthToken');
                redirect(base_url() . 'auth/login');
            }

            // Split the token into payload and hash
            list($encoded_payload, $token_hash) = explode('.', $auth_token);
            // Decode the payload
            $payload = json_decode(base64_decode($encoded_payload), true);
            // Recreate the hash from the payload and secret key
            $recreated_hash = hash_hmac('sha256', base64_decode($encoded_payload), $this->secret_key);
            // Validate the hash
            if ($recreated_hash === $token_hash) {
                // Hash is valid, so extract the user data
                $user_id = $payload['userid'];
                if (!$this->User_model->validate_user($user_id)) {
                    // If user is not valid, delete cookie and redirect
                    delete_cookie('userTaskAuthToken');
                    redirect(base_url() . 'auth/login');
                }
            } else {
                // If the hash doesn't match, delete cookie and redirect
                delete_cookie('userTaskAuthToken');
                redirect(base_url() . 'auth/login');
            }
        }
    }

    protected function getUserDetails(): ?array
    {
        $cookieName = 'userTaskAuthToken';
        // Check if the cookie is set
        if (!isset($_COOKIE[$cookieName])) {
            return null; // Return null if the cookie is not set
        }

        // Split the token into payload and hash
        [$encodedPayload, $hash] = explode('.', $_COOKIE[$cookieName]);

        // Decode the payload
        $payloadJson = base64_decode($encodedPayload);
        if ($payloadJson === false) {
            return null; // Return null if decoding fails
        }

        $payload = json_decode($payloadJson, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            return null; // Return null if JSON decoding fails
        }

        // Verify the hash
        $expectedHash = hash_hmac('sha256', $payloadJson, $this->secret_key);
        // Check if the hash matches
        if (!hash_equals($expectedHash, $hash)) {
            return null; // Return null if the hash does not match
        }



        // Token is valid, return user details
        return $payload;
    }

    function sendHTTPResponse($statusCode, $response)
    {
        $this->output
            ->set_content_type('application/json')
            ->set_status_header($statusCode)
            ->set_output(json_encode($response));
        return;
    }
}
