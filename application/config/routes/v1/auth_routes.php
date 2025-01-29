<?php
defined('BASEPATH') or exit('No direct script access allowed');

// Auth Routes
$route['api/v1/auth/login'] = 'auth/validate';
$route['api/v1/auth/mfa'] = 'auth/mfa';
$route['api/v1/auth/register'] = 'auth/register';
$route['api/v1/auth/logout'] = 'auth/logout';
$route['api/v1/auth/forgot-password'] = 'auth/forgot_password';
$route['api/v1/auth/reset-password'] = 'auth/reset_password';
