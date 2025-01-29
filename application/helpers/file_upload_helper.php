<?php
defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Uploads multiple files to a specified path.
 *
 * @param array $files - The $_FILES array containing the files to be uploaded.
 * @param string $uploadPath - The directory path to upload files.
 * @param array $allowedTypes - The types of files that are allowed for upload.
 * @param int $maxSize - The maximum size of the files in KB.
 * @return array|null - An array of uploaded filenames or null if no files were uploaded.
 */
function upload_multiple_files($files, $uploadPath, $allowedTypes = [], $maxSize = 1024)
{
    // Load the CodeIgniter instance
    $CI = &get_instance();
    $CI->load->library('upload');

    // Create the upload directory if it doesn't exist
    if (!is_dir($uploadPath)) {
        mkdir($uploadPath, 0755, true);
    }

    // Array to store uploaded filenames
    $uploadedFiles = [];

    // Check if files are uploaded
    if (!empty($files['name'][0])) {
        // Loop through each file
        foreach ($files['name'] as $key => $fileName) {
            $_FILES['file']['name'] = $files['name'][$key];
            $_FILES['file']['type'] = $files['type'][$key];
            $_FILES['file']['tmp_name'] = $files['tmp_name'][$key];
            $_FILES['file']['error'] = $files['error'][$key];
            $_FILES['file']['size'] = $files['size'][$key];

            // Set upload config
            $config['upload_path'] = $uploadPath;
            $config['allowed_types'] = implode('|', $allowedTypes); // Allowed file types
            $config['max_size'] = $maxSize; // Maximum file size in KB

            $CI->upload->initialize($config);

            // Attempt to upload the file
            if ($CI->upload->do_upload('file')) {
                // Get uploaded data
                $uploadData = $CI->upload->data();
                $uploadedFiles[] = $uploadData['file_name']; // Store the uploaded filename
            } else {
                // Optionally, handle error (e.g., log it or return the error)
                $error = $CI->upload->display_errors();
            }
        }
    }

    // Return the array of filenames or null if none were uploaded
    return !empty($uploadedFiles) ? $uploadedFiles : null;
}

if (!function_exists('upload_single_file')) {
    function upload_single_file($inputName, $uploadPath, $allowedTypes = 'jpg|jpeg|png|gif|webp|svg', $maxSize = 2048)
    {
        // Load CodeIgniter's upload library
        $CI = &get_instance();
        $CI->load->library('upload');

        // Ensure the upload directory exists
        if (!is_dir($uploadPath)) {
            mkdir($uploadPath, 0755, true);
        }

        // Set upload configuration
        $config['upload_path']   = rtrim($uploadPath, '/') . '/'; // Ensure trailing slash
        $config['allowed_types'] = $allowedTypes;
        $config['max_size']      = $maxSize; // Max size in KB
        $config['encrypt_name']  = true; // Generate a unique name for the file

        $CI->upload->initialize($config);

        // Perform the upload
        if ($CI->upload->do_upload($inputName)) {
            // Return the uploaded file name
            return $CI->upload->data('file_name');
        }

        // Return null if upload fails
        return null;
    }
}
