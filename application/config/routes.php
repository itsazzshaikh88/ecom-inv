<?php
defined('BASEPATH') or exit('No direct script access allowed');

$route['default_controller'] = 'welcome';
$route['404_override'] = '';
$route['translate_uri_dashes'] = FALSE;

$version = API_VERSION;

// Include Auth Routes
require_once(APPPATH . "config/routes/$version/auth_routes.php");
// Include API Routes
require_once(APPPATH . "config/routes/$version/api_routes.php");
