async function submitForm(e) {
    e.preventDefault();
    const form = e.target;
    const formData = new FormData(form);


    // Set Loading Animation on button
    const submitBtn = document.getElementById("submit-btn");
    let buttonText = submitBtn.innerHTML;
    submitBtn.disabled = true;
    submitBtn.innerHTML = `Saving Data ...`;

    // Hide Error
    hideErrors();
    try {
        // Retrieve the auth_token from cookies
        const authToken = validateUserAuthToken();
        if (!authToken) return;

        const category_id = document.getElementById("subcategory_id").value;
        let url = `${apiURL}subcategories/`;
        if (category_id)
            url += `update/${category_id}`
        else
            url += 'new'
        // Fetch API with Bearer token in Authorization header
        const response = await fetch(url, {
            method: 'POST', // or POST, depending on the API endpoint
            headers: {
                'Authorization': `Bearer ${authToken}`
            },
            body: formData
        });


        // Check if the response is OK (status 200-299)
        if (response.ok) {
            const data = await response.json();
            toasterNotification({ type: 'success', message: data?.message || 'Record Saved Successfully' });
            clearForm();
            fetchSubcategoryList();
        } else {
            const errorData = await response.json();
            if (errorData.status === 422) {
                showErrors(errorData.validation_errors ?? []);
            } else {
                toasterNotification({ type: 'error', message: errorData.message ?? 'Internal Server Error' });
            }
        }
    } catch (error) {
        toasterNotification({ type: 'error', message: 'Request failed:' + error });
        console.error(error);

    } finally {
        submitBtn.disabled = false;
        submitBtn.innerHTML = buttonText;
    }
}

function clearForm() {
    const form = document.getElementById("form")
    form?.reset();
    document.getElementById("subcategory_id").value = '';
}

async function fetchSubcategoryDetailsForEdit (subcategoryID) {
    const url = `${apiURL}subcategories/${subcategoryID}`;

    const authToken = validateUserAuthToken();
    if (!authToken) return;

    try {

        fullPageLoader.classList.toggle("d-none");
        // Fetch product data from the API
        const response = await fetch(url, {
            method: 'POST',
            headers: {
                'Authorization': `Bearer ${authToken}`
            }
        });

        // Parse the JSON response
        const data = await response.json();

        // Check if the API response contains an error
        if (!response.ok || data.status === 'error') {
            const errorMessage = data.message || `Error: ${response.status} ${response.statusText}`;
            throw new Error(errorMessage);
        }

        displayCategoryInfo(data.data);
        // Fetch roles and set it to selected role
    } catch (error) {
        // Show error notification
        toasterNotification({ type: 'error', message: 'Error: ' + error.message });
        console.error(error);

    } finally {
        fullPageLoader.classList.toggle("d-none");
    }
}
function displayCategoryInfo(data) {
    if (!data) return;

    if (Object.keys(data).length > 0) {
        populateFormFields(data);
    }
}


async function startOverNew() {
    try {

        // Show a confirmation alert
        const confirmation = await Swal.fire({
            title: "Are you sure?",
            text: "Do you really want to start new action? This action cannot be undone.",
            icon: "warning",
            showCancelButton: true,
            confirmButtonText: "Yes, Start New",
            cancelButtonText: "Cancel",
            customClass: {
                popup: 'small-swal',
                confirmButton: 'swal-confirm-btn',
                cancelButton: 'swal-cancel-btn',
            },
        });

        if (!confirmation.isConfirmed) return;

        Swal.close();
        window.location = 'users/new';

    } catch (error) {
        toasterNotification({ type: 'error', message: 'Request failed: ' + error.message });
        Swal.close();
    }
}

// Fetch all Sub categories
async function fetchAllCategories(selectedCategory = null) {
    try {
        // Check token exist
        const authToken = validateUserAuthToken();
        if (!authToken) return;

        toggleFullPageLoader();

        const url = `${apiURL}categories`;
        const filters = filterCriterias([]);

        const response = await fetch(url, {
            method: 'POST',
            headers: {
                'Authorization': `Bearer ${authToken}`,
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                limit: null,
                currentPage: null,
                filters: filters
            })
        });

        if (!response.ok) {
            throw new Error('Failed to fetch data');
        }

        const data = await response.json();

        showCategories(data.categories || [], selectedCategory);

    } catch (error) {
        toasterNotification({ type: 'error', message: 'Request failed: ' + error.message });
        console.error(error);
    } finally {
        toggleFullPageLoader('hide');
    }
}

function showCategories(categories, selected) {

    if (!categories) return '';
    if (categories && categories?.length > 0) {
        let content = '<option value="">Select Category</option>';
        let categorySelect = document.getElementById("category_id");
        categories.forEach((category) => {
            content += `<option value="${category?.category_id}">${category?.name}</option>`;
        });

        categorySelect.innerHTML = content;
        if (selected && selected != '') {
            categorySelect.value = selected
        }
    }
}

// 
document.addEventListener('DOMContentLoaded', () => {
    // Fetch initial User data
    fetchAllCategories();
});