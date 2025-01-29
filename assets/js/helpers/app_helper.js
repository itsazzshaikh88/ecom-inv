/**
 * Strips HTML tags and converts HTML entities to plain text.
 * @param {string} inputString - The input string containing HTML.
 * @returns {string} - The cleaned plain text string.
 */
function stripHtmlTags(inputString) {
    // Check if the input is a string
    if (typeof inputString !== 'string') {
        return ''
    }

    // Remove HTML tags and decode HTML entities
    const cleanedString = inputString
        .replace(/<[^>]*>/g, '') // Remove HTML tags
        .replace(/&nbsp;/g, ' ') // Replace non-breaking spaces
        .replace(/&lt;/g, '<')   // Decode less than
        .replace(/&gt;/g, '>')   // Decode greater than
        .replace(/&amp;/g, '&')   // Decode ampersand
        .replace(/&quot;/g, '"')  // Decode double quotes
        .replace(/&apos;/g, "'")   // Decode single quotes
        .trim(); // Remove leading and trailing whitespace

    return cleanedString || '';
}


/**
 * Parses a JSON string and returns an object or an element at a specified index.
 *
 * @param {string|null} jsonString - The JSON string to parse.
 * @param {number|null} index - The index of the element to return (if applicable).
 * @returns {object|null} - Returns the parsed object, the element at the specified index, or null.
 */
function parseJsonString(jsonString, index = null) {
    // Validate input: check for null or empty string
    if (jsonString === null || jsonString.trim() === '') {
        return null;
    }

    let parsedObject;

    try {
        // Attempt to parse the JSON string
        parsedObject = JSON.parse(jsonString);
    } catch (error) {
        // If parsing fails, log the error (optional) and return null
        console.error('Failed to parse JSON string:', error);
        return null;
    }

    // If no index is provided, return the entire parsed object
    if (index === null) {
        return parsedObject;
    }

    // Check if the parsed object is an array
    if (Array.isArray(parsedObject)) {
        // Validate the index and return the corresponding element
        return index >= 0 && index < parsedObject.length ? parsedObject[index] : null;
    }

    // If parsedObject is not an array, return null
    return null;
}


function populateFormFields(data) {
    // Loop through each key in the object
    for (const [key, value] of Object.entries(data)) {
        // Find the element by ID that matches the key
        const element = document.getElementById(key);

        if (!element) continue; // Skip if element not found

        // Check the type of form element and set value accordingly
        if (element.tagName === 'INPUT') {
            switch (element.type) {
                case 'text':
                case 'email':
                case 'password':
                case 'hidden':
                case 'date':
                    element.value = value;
                    break;
                case 'number':
                    element.value = parseFloat(value)
                    break;

                case 'radio':
                    const radioElements = document.querySelectorAll(`input[name="${key}"]`);
                    radioElements.forEach(radio => {
                        if (radio.value === value) {
                            radio.checked = true;
                        }
                    });
                    break;

                case 'checkbox':
                    element.checked = Array.isArray(value) ? value.includes(element.value) : Boolean(value);
                    break;
            }
        } else if (element.tagName === 'SELECT') {
            element.value = value;
        } else if (element.tagName === 'TEXTAREA') {
            element.value = value;
        }
    }
}


function showFieldContent(data) {
    // Check if the data is a valid object
    if (typeof data === 'object' && data !== null) {
        for (const key in data) {
            if (data.hasOwnProperty(key)) {
                // Select the HTML element by ID
                const element = document.getElementById(`lbl-${key}`);
                // If the element exists, update its innerHTML
                if (element) {
                    if (['null', '', ' ', "", "\"\""].includes(data[key]))
                        element.innerHTML = '';
                    else {
                        element.innerHTML = data[key] != '' && data[key] != 'null' ? capitalizeWords(data[key], true) : '';
                    }
                }
            }
        }
    }
}

function capitalizeWords(str, capitalizeAll = false) {
    if (str != null && str != 'null' && str != '')
        str = str.toLowerCase();
    // Validate input
    if (typeof str !== 'string' || str.trim() === '') {
        return ''; // Return an empty string if input is not a valid string
    }

    if (capitalizeAll) {
        // Capitalize the first letter of each word
        return str.replace(/\b\w/g, char => char.toUpperCase());
    } else {
        // Capitalize only the first letter of the first word in the string
        return str.charAt(0).toUpperCase() + str.slice(1).toLowerCase();
    }
}

/**
 * Formats a date string from "YYYY-MM-DD" or "YYYY-MM-DD HH:mm:ss" to "Sat, Aug 24 2024" format.
 *
 * @param {string} dateString - The date string in "YYYY-MM-DD" or "YYYY-MM-DD HH:mm:ss" format.
 * @returns {string|null} The formatted date string, or null if input is invalid or null.
 */
function formatAppDate(dateString) {

    // Return null for null, undefined, or empty input
    if (!dateString) return null;

    // Ensure the date string matches "YYYY-MM-DD" or "YYYY-MM-DD HH:mm:ss" format
    const dateRegex = /^\d{4}-\d{2}-\d{2}(?: \d{2}:\d{2}:\d{2})?$/;
    if (!dateRegex.test(dateString)) return null;

    // Parse the date and check validity
    const date = new Date(dateString);
    if (isNaN(date.getTime())) return null;

    // Format the date to "Sat, Aug 24 2024"
    const options = { weekday: 'short', month: 'short', day: 'numeric', year: 'numeric' };
    return date.toLocaleDateString('en-US', options);
}

function get2FAActions(type = 'enable') {
    let className = type === 'enable' ? "primary" : "danger";
    if (type === 'enable')
        return `
                    <!--begin::Icon-->
                    <i class="fa-solid fa-shield-halved fs-2tx text-primary me-4"></i>
                    <!--end::Icon-->
                    <!--begin::Wrapper-->
                    <div class="d-flex flex-stack flex-grow-1 flex-wrap flex-md-nowrap">
                        <!--begin::Content-->
                        <div class="mb-3 mb-md-0 fw-semibold">
                            <h4 class="text-gray-900 fw-bold">Secure Your Account</h4>
                            <div class="fs-6 text-gray-700 pe-7 fw-normal">
                                Two-factor authentication adds an extra layer of security to your account. In addition to your password, you'll need to provide a time-based One-Time Password (OTP) to log in. <br /><br /> This unique, <b>6-digit code</b> changes every 30 seconds and ensures that only you can access your account.
                            </div>
                        </div>
                        <!--end::Content-->
                        <!--begin::Action-->
                        <button type="button" class="btn btn-primary px-6 align-self-center text-nowrap" id="enable2FAButton" onclick="enable2FA('enable')">Enable</button>
                        <!--end::Action-->
                    </div>
                    <!--end::Wrapper-->`;
    if (type === 'disable')
        return `
                    <!--begin::Icon-->
                    <i class="fa-solid fa-shield-halved fs-2tx text-danger me-4"></i>
                    <!--end::Icon-->
                    <!--begin::Wrapper-->
                    <div class="d-flex flex-stack flex-grow-1 flex-wrap flex-md-nowrap">
                        <!--begin::Content-->
                        <div class="mb-3 mb-md-0 fw-semibold">
                            <h4 class="text-gray-900 fw-bold">Disable Two-Factor Authentication</h4>
                            <div class="fs-6 text-gray-700 pe-7 fw-normal">
                                Disabling two-factor authentication will remove an extra layer of security from your account. After disabling, you will only need your password to log in, which may make your account more vulnerable to unauthorized access. <br /><br /> We recommend keeping two-factor authentication enabled to protect your account.
                            </div>
                        </div>
                        <!--end::Content-->
                        <!--begin::Action-->
                        <button type="button" class="btn btn-danger px-6 align-self-center text-nowrap" id="enable2FAButton" onclick="enable2FA('disable')">Disable</button>
                        <!--end::Action-->
                    </div>
                    <!--end::Wrapper-->`;
}

function toggleMFADetails(action = 'show', data = {}) {
    if (action == 'show') {
        return ``
    }
}

function getSegment(segmentNumber) {
    // Get the current URL path (excluding the query string)
    const path = window.location.pathname;

    // Split the path into segments, removing any leading/trailing slashes
    const segments = path.split('/').filter(segment => segment.trim() !== '');

    // If no segment number is provided, return all segments as an array
    if (!segmentNumber) {
        return segments;
    }

    // If segment number is provided, return the specific segment (1-based index)
    const index = segmentNumber - 1;
    return segments[index] || null; // Return null if the segment doesn't exist
}


function validateUserAuthToken() {
    // Retrieve the auth_token from cookies
    const authToken = getCookie('userTaskAuthToken');
    if (!authToken) {
        toasterNotification({ type: 'error', message: "Authorization token is missing. Please Login again to make API request." });
        return false;
    }
    return authToken;
}

function getSubmitAPI(config) {
    if (!config) return '';

    if (config?.id)
        return `${ApiUrl}${config.resource}/update/${config?.id}`
    else
        return `${ApiUrl}${config.resource}/new`
}
