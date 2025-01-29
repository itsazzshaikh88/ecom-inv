// Save Form Data
function resetForm() {
    if (confirm("Do you want to reset form?")) {
        window.location.reload();
    }
}
function showErrors(errors, id_lbl = "lbl") {
    // Loop through each error field in the errors object
    for (const fieldName in errors) {

        if (errors.hasOwnProperty(fieldName)) {
            const errorMessage = errors[fieldName];
            const errorElement = document.getElementById(`${id_lbl}-${fieldName}`);
            if (errorElement) {
                // Update the span element with the error message
                errorElement.innerHTML = errorMessage;
                // Optionally, add a CSS class to highlight the error, e.g., errorElement.classList.add('text-danger');
            }
        }
    }
}

function hideErrors(class_name = "err-lbl") {
    const errorLabels = document.querySelectorAll(`.${class_name}`); // Select all elements with class 'err-lbl'

    errorLabels.forEach((label) => {
        label.innerHTML = ""; // Clear innerHTML of each error label
    });
}

function validateNumberInput(input) {
    // Allow only digits and decimal points
    input.value = input.value.replace(/[^\d.]/g, '');
}


function getCookie(name) {
    const decodedCookie = decodeURIComponent(document.cookie);
    const cookieArray = decodedCookie.split(';');
    const cookieName = name + "=";

    for (let cookie of cookieArray) {
        let c = cookie.trim();
        if (c.indexOf(cookieName) === 0) {
            return c.substring(cookieName.length, c.length);
        }
    }
    return null; // Return null if the cookie is not found
}

function toasterNotification(options) {    
    Toastify({
        text: options?.message,
        duration: 3000,
        close: true,
        gravity: "top", // `top` or `bottom`
        position: "right", // `left`, `center` or `right`
        stopOnFocus: true, // Prevents dismissing of toast on hover
        style: {
            background: `${options?.type === 'success' ? "#72BF78" : "#FF4545"}`,
        },
        // onClick: function () { } // Callback after click
    }).showToast();
}

function filterCriterias(filters = []) {

    if (filters != []) {
        // Create an object to hold the values
        let filteredObject = {};
        // Loop through the filters to get values from DOM elements
        filters.forEach(filter => {
            let element = document.getElementById(filter); // Get the element by ID
            if (element && element.value) { // Check if element exists and has a non-empty value
                filteredObject[filter] = element.value; // Use the filter name as the key
            }
        });
        // Return the object as a JSON string
        return filteredObject;
    }
    return {};
}

function uuid_v4() {
    // Generate 16 random bytes (128 bits)
    const data = crypto.getRandomValues(new Uint8Array(16));

    // Set the version to 4 (UUID v4)
    data[6] = (data[6] & 0x0f) | 0x40;

    // Set the variant to RFC 4122
    data[8] = (data[8] & 0x3f) | 0x80;

    // Format as UUID (xxxxxxxx-xxxx-4xxx-yxxx-xxxxxxxxxxxx)
    return [...data]
        .map((b, i) =>
            [4, 6, 8, 10].includes(i) ? `-${b.toString(16).padStart(2, '0')}` : b.toString(16).padStart(2, '0')
        )
        .join('');
}

/**
 * Escapes special characters in a string to make it safe for use in code.
 * Specifically, it escapes single quotes, double quotes, and backslashes.
 * @param {string} str - The input string to be escaped.
 * @returns {string} - The escaped string.
 */
function escapeSpecialCharacters(str) {
    if (typeof str !== 'string') {
        throw new TypeError('Input must be a string');
    }
    // Escape single quotes, double quotes, and backslashes
    return str.replace(/['"\\]/g, '\\$&');
}

// Debounce function
function debounce(func, delay = 300) {
    let timer;
    return function (...args) {
        clearTimeout(timer); // Clear the previous timeout
        timer = setTimeout(() => func.apply(this, args), delay); // Set a new timeout
    };
}