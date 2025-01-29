<?php
defined('BASEPATH') or exit('No direct script access allowed');

if (!function_exists('set_active_link')) {
    /**
     * Sets the navigation link as 'active' if the selected link matches the current link.
     * 
     * @param string $selected_link The selected link to check.
     * @param string $current_link The current active link.
     * @return string|null Returns 'active' if the links match, otherwise null.
     */
    function set_active_link($selected_link, $current_link)
    {
        return ($selected_link === $current_link) ? 'active' : null;
    }
}

if (!function_exists('application_module')) {
    /**
     * returns application module name with proper formating
     * 
     * @param string $module module name input.
     * @return string Returns module name with string formating
     */
    function application_module(string $module): string
    {
        return ucwords(str_replace("-", " ", $module));
    }
}



function beautify_array($array, $exit = false)
{
    echo "<pre>";
    print_r($array);
    echo "</pre>";
    if ($exit)
        exit(0);
}

if (! function_exists('uuid_v4')) {
    /**
     * Generate a strictly standards-compliant UUID v4 (RFC 4122)
     *
     * @return string
     */
    function uuid_v4()
    {
        // Generate 16 random bytes (128 bits)
        $data = random_bytes(16);

        // Set the version to 4 (UUID v4)
        $data[6] = chr(ord($data[6]) & 0x0f | 0x40);

        // Set the variant to RFC 4122
        $data[8] = chr(ord($data[8]) & 0x3f | 0x80);

        // Format as UUID (xxxxxxxx-xxxx-4xxx-yxxx-xxxxxxxxxxxx)
        return sprintf(
            '%08s-%04s-%04s-%04s-%12s',
            bin2hex(substr($data, 0, 4)),
            bin2hex(substr($data, 4, 2)),
            bin2hex(substr($data, 6, 2)),
            bin2hex(substr($data, 8, 2)),
            bin2hex(substr($data, 10, 6))
        );
    }

    function setDiscountType($value)
    {
        if ($value == '2')
            return 'percentage';
        return 'no discount';
    }
}

function setNavigationLinkActive($current, $target, $class)
{
    if ($current === $target)
        return $class;
    return '';
}
function survey_questions()
{
    // create survey questions
    $questions = array(
        array(
            'title' => "A - Products & Technology",
            'questions' => array(
                array(
                    'title' => "Product Quality",
                    'questions' => "How would you rate the quality of our products?"
                ),
                array(
                    'title' => "Packaging And Labelling Quality",
                    'questions' => "How would you rate the suitability of our outer packaging, palletization and labelling?"
                ),
                array(
                    'title' => "Innovation & Development",
                    'questions' => "How would you rate our ability to innovate and develop products?"
                ),
                array(
                    'title' => "Technological Capacity",
                    'questions' => "How would you rate our company's use of high-technology in production?"
                )
            )
        ),
        array(
            'title' => "B - Delivery And Logistics",
            'questions' => array(
                array(
                    'title' => "On-Time Delivery Performance",
                    'questions' => "How would you rate our on-time delivery performance?"
                ),
                array(
                    'title' => "Order Confirmation Time",
                    'questions' => ""
                ),
                array(
                    'title' => "Approach To Urgent Orders",
                    'questions' => "How would you rate our ability to innovate and develop products?"
                ),
                array(
                    'title' => "Accuracy",
                    'questions' => "How would you rate our compliance with your specified delivery quantities?"
                ),
                array(
                    'title' => "Means Of Transport",
                    'questions' => "How would you rate our network and the transport alternatives we offer?"
                ),
                array(
                    'title' => "Logistics Follow Up Services",
                    'questions' => "How would you rate the information flow for transport information and services of our logistics department?"
                )
            )
        ),
        array(
            'title' => "C - Customer Service",
            'questions' => array(
                array(
                    'title' => "Communication Quality",
                    'questions' => "How would you rate the quality of communication with our sales representative?"
                ),
                array(
                    'title' => "Easy Access To Sales Team",
                    'questions' => "How would you rate your access to our sales team? (exp. By phone, mail etc.)"
                ),
                array(
                    'title' => "Professionalism",
                    'questions' => "How would you rate our Professionalism in dealing with you?"
                ),
                array(
                    'title' => "After Sales Service",
                    'questions' => "How supportive do you find our customer service following the purchase of a product?"
                ),
                array(
                    'title' => "Responsiveness To Documentation Requirements",
                    'questions' => "How would you rate our responses to documentation requirements?"
                )
            )
        ),
        array(
            'title' => "D - Technical Service And Development",
            'questions' => array(
                array(
                    'title' => "Technical Support",
                    'questions' => "how would you rate the technical competence of our engineers and their response time?"
                ),
                array(
                    'title' => "Trial Performance",
                    'questions' => "How do you find our lead times and performance quality for trials?"
                ),
                array(
                    'title' => "Complaint Handling",
                    'questions' => "How do you rate the response time and content of our replies to your complaints?"
                )
            )
        ),
        array(
            'title' => "E - Company Reputation",
            'questions' => array(
                array(
                    'title' => "Our Position Within The Industry",
                    'questions' => "How would you rate Zamil Plastic amongst the major Packaging companies?"
                ),
                array(
                    'title' => "Competitiveness",
                    'questions' => "How do you rate the competititveness of our products?"
                ),
                array(
                    'title' => "Approach",
                    'questions' => "How would we rank in terms of building trust as a business partner?"
                ),
                array(
                    'title' => "Website",
                    'questions' => "How would you rate our website? (in terms of informative, user-friendly, easy-to-access etc.)"
                )
            )
        )
    );
    return $questions;
}
function survey_ratings()
{
    return array(
        array(
            "icon" => "<i class='bi bi-emoji-angry text-danger' style= 'font-size:xx-large'></i>",
            "title" => "Strongly Disagree",
            "color" => "danger"
        ),
        array(
            "icon" => "<i class='bi bi-emoji-frown text-warning' style= 'font-size:xx-large'></i>",
            "title" => "Disagree",
            "color" => "warning"
        ),
        array(
            "icon" => "<i class='bi bi-emoji-neutral text-secondary' style= 'font-size:xx-large'></i>",
            "title" => "Neutral",
            "color" => "secondary",

        ),
        array(
            "icon" => "<i class='bi bi-emoji-smile text-info' style= 'font-size:xx-large'></i>",
            "title" => "Agree",
            "color" => "info"
        ),
        array(
            "icon" => "<i class='bi bi-emoji-heart-eyes text-success' style= 'font-size:xx-large'></i>",
            "title" => "Strongly Agree",
            "color" => "success"
        )
    );
}

function sidebarCoreLinks()
{
    return [
        [
            'name' => 'Users',
            'icon' => 'fa-solid fa-users',
            'link' => 'users',
            'users' => ['admin']
        ],
        [
            'name' => 'Roles',
            'icon' => 'fa-regular fa-id-badge',
            'link' => 'roles',
            'users' => ['admin']
        ],
        [
            'name' => 'Projects',
            'icon' => 'fa-solid fa-bars-progress',
            'link' => 'projects',
        ],
        [
            'name' => 'Tasks',
            'icon' => 'fa-solid fa-list-check',
            'link' => 'tasks'
        ],
        [
            'name' => 'ToDos',
            'icon' => 'fa-solid fa-check-double',
            'link' => 'todo'
        ]
    ];
}

if (!function_exists('generate_slug')) {
    /**
     * Generate a URL-friendly slug
     *
     * @param string $string The input string
     * @param string $table The table name for uniqueness check
     * @param string $field The column name for slug storage
     * @param int|null $id Optional, exclude current ID for updates
     * @param int $max_length Optional, max slug length (default: 255)
     * @return string The generated unique slug
     */
    function generate_slug($string, $table, $field, $id = null, $max_length = 255)
    {
        $CI = &get_instance();
        $CI->load->database();

        // Normalize the string
        $slug = strtolower($string);
        $slug = preg_replace('/[^a-z0-9\s-]/u', '', $slug); // Remove special characters
        $slug = preg_replace('/\s+/', '-', $slug); // Replace spaces with hyphens
        $slug = trim($slug, '-'); // Trim hyphens

        // Truncate if too long
        $slug = substr($slug, 0, $max_length);

        // Ensure uniqueness
        $original_slug = $slug;
        $counter = 1;

        while (true) {
            $CI->db->select($field);
            $CI->db->from($table);
            $CI->db->where($field, $slug);

            if ($id !== null) {
                $CI->db->where('id !=', $id); // Exclude current record
            }

            $query = $CI->db->get();
            if ($query->num_rows() == 0) {
                break;
            }

            $slug = $original_slug . '-' . $counter;
            $counter++;
        }

        return $slug;
    }
}
