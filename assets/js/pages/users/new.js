async function submitForm(e) {
    e.preventDefault();
    const form = e.target;
    const formData = new FormData(form);


    // Set Loading Animation on button
    const submitBtn = document.getElementById("submit-btn");
    let buttonText = submitBtn.innerHTML;
    submitBtn.disabled = true;
    submitBtn.innerHTML = `Saving ...`;

    // Hide Error
    hideErrors();
    try {
        // Retrieve the auth_token from cookies
        const authToken = validateUserAuthToken();
        if (!authToken) return;

        const userid = document.getElementById("id").value;
        let url = `${apiURL}users/`;
        if (userid)
            url += `update/${userid}`
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
            if (data?.type === 'insert')
                clearForm();
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
    document.getElementById("id").value = '';
    document.querySelector(".password-row").classList.remove("d-none");
}

async function fetchUserToDisplayForEdit(userid) {
    const url = `${apiURL}users/${userid}`;

    const authToken = validateUserAuthToken();
    if (!authToken) return;


    try {

        fullPageLoader.classList.toggle("d-none");
        // Fetch product data from the API
        const response = await fetch(url, {
            method: 'POST',
            headers: {
                'Authorization': `Bearer ${authToken}`,
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({ userid })
        });

        // Parse the JSON response
        const data = await response.json();

        // Check if the API response contains an error
        if (!response.ok || data.status === 'error') {
            const errorMessage = data.message || `Error: ${response.status} ${response.statusText}`;
            throw new Error(errorMessage);
        }

        fetchRoles(data?.data?.role_id || 0);
        displayUserInfo(data.data);
        // Fetch roles and set it to selected role
    } catch (error) {
        // Show error notification
        toasterNotification({ type: 'error', message: 'Error: ' + error.message });
        console.error(error);

    } finally {
        fullPageLoader.classList.toggle("d-none");
    }
}
function displayUserInfo(data) {
    if (!data) return;

    // Set developer and client details
    renderAdditionalDetails(data?.role_name);

    if (Object.keys(data).length > 0) {
        populateFormFields(data);
    }

    // Remove password row
    document.querySelector(".password-row").classList.add("d-none");
}

function renderRoles(roles, selectedRole = null) {
    const element = document.getElementById("role_id");
    if (!roles) return;
    element.innerHTML = '<option value="">Choose Role</option>';
    if (roles && roles?.length > 0) {
        roles.forEach(role => {
            element.innerHTML +=
                `<option value="${role.id}">${role.role_name}</option>`
        });
    }
    if (selectedRole)
        element.value = selectedRole;
}

function renderAdditionalDetails(selectedRole = null) {
    const selectElement = document.getElementById("role_id");

    // Get the selected option's text
    if (!selectedRole)
        selectedRole = selectElement?.options[selectElement.selectedIndex]?.text?.trim();

    // Determine what to render based on the selected role
    const additionalDetailsContainer = document.getElementById("additional-user-details");

    if (selectedRole.toLowerCase() === 'client') {
        additionalDetailsContainer.innerHTML = renderClientFields();
    } else if (selectedRole.toLowerCase() === 'developer') {
        additionalDetailsContainer.innerHTML = renderDeveloperFields();
    } else {
        additionalDetailsContainer.innerHTML = '';
    }
}


function renderClientFields() {
    return `<div class="col-md-12">
                                <h5>Client Additional Details</h5>
                                <hr>
                            </div>
                            <div class="col-md-12">
                                <div class="row mb-1">
                                    <label class="col-sm-2 col-form-label col-form-label-sm" for="company_name">Company Name <span class="float-end d-none d-lg-block">:</span> </label>
                                    <div class="col-sm-5">
                                        <input class="form-control form-control-sm" type="text" name="company_name" id="company_name" placeholder="Enter Client Company Name ...">
                                    </div>
                                </div>
                                <div class="row mb-1">
                                    <label class="col-sm-2 col-form-label col-form-label-sm" for="contact_name">Contact Person <span class="float-end d-none d-lg-block">:</span> </label>
                                    <div class="col-sm-5">
                                        <input class="form-control form-control-sm" type="text" name="contact_name" id="contact_name" placeholder="Enter Contact Person Name ...">
                                    </div>
                                </div>
                                <div class="row mb-1">
                                    <label class="col-sm-2 col-form-label col-form-label-sm" for="contact_email">Contact Email <span class="float-end d-none d-lg-block">:</span> </label>
                                    <div class="col-sm-5">
                                        <input class="form-control form-control-sm" type="text" name="contact_email" id="contact_email" placeholder="Enter Contact Person Email ...">
                                    </div>
                                </div>
                                <div class="row mb-1">
                                    <label class="col-sm-2 col-form-label col-form-label-sm" for="contact_phone">Contact Number <span class="float-end d-none d-lg-block">:</span> </label>
                                    <div class="col-sm-3">
                                        <input class="form-control form-control-sm" type="text" name="contact_phone" id="contact_phone" placeholder="Enter Contact Person Number ...">
                                    </div>
                                </div>
                                <div class="row mb-1">
                                    <label class="col-sm-2 col-form-label col-form-label-sm" for="billing_address">Billing Address <span class="float-end d-none d-lg-block">:</span> </label>
                                    <div class="col-sm-5">
                                        <input class="form-control form-control-sm" type="text" name="billing_address" id="billing_address" placeholder="Enter Billing Address ...">
                                    </div>
                                </div>
                                <div class="row mb-1">
                                    <label class="col-sm-2 col-form-label col-form-label-sm" for="shipping_address">Shipping Address <span class="float-end d-none d-lg-block">:</span> </label>
                                    <div class="col-sm-5">
                                        <input class="form-control form-control-sm" type="text" name="shipping_address" id="shipping_address" placeholder="Enter Shipping Address ...">
                                    </div>
                                </div>
                                <div class="row mb-1">
                                    <label class="col-sm-2 col-form-label col-form-label-sm" for="client_since">Client Since <span class="float-end d-none d-lg-block">:</span> </label>
                                    <div class="col-sm-3">
                                        <input class="form-control form-control-sm" type="date" name="client_since" id="client_since" placeholder="Enter Valid Details ...">
                                    </div>
                                </div>
                                <div class="row mb-1">
                                    <label class="col-sm-2 col-form-label col-form-label-sm" for="industry_type">Industry Type <span class="float-end d-none d-lg-block">:</span> </label>
                                    <div class="col-sm-5">
                                        <input class="form-control form-control-sm" type="text" name="industry_type" id="industry_type" placeholder="Specify Industry Type ...">
                                    </div>
                                </div>
                            </div>`;
}

function renderDeveloperFields() {
    return `<div class="col-md-12">
                                <h5>Developer Additional Details</h5>
                                <hr>
                            </div>
                            <div class="col-md-12">
                                <div class="row mb-1">
                                    <label class="col-sm-2 col-form-label col-form-label-sm" for="specialization">Specialization <span class="float-end d-none d-lg-block">:</span> </label>
                                    <div class="col-sm-5">
                                        <input class="form-control form-control-sm" type="text" name="specialization" id="specialization" placeholder="Enter Valid Details ...">
                                    </div>
                                </div>
                                <div class="row mb-1">
                                    <label class="col-sm-2 col-form-label col-form-label-sm" for="experience_years">Experience in Year <span class="float-end d-none d-lg-block">:</span> </label>
                                    <div class="col-sm-3">
                                        <input class="form-control form-control-sm" type="text" name="experience_years" id="experience_years" placeholder="Enter Valid Details ...">
                                    </div>
                                </div>
                                <div class="row mb-1">
                                    <label class="col-sm-2 col-form-label col-form-label-sm" for="skills">Skills <span class="float-end d-none d-lg-block">:</span> </label>
                                    <div class="col-sm-5">
                                        <input class="form-control form-control-sm" type="text" name="skills" id="skills" placeholder="Enter Valid Details ...">
                                    </div>
                                </div>
                                <div class="row mb-1">
                                    <label class="col-sm-2 col-form-label col-form-label-sm" for="availability">Availability <span class="float-end d-none d-lg-block">:</span> </label>
                                    <div class="col-sm-3">
                                        <select class="form-control form-control-sm" name="availability" id="availability">
                                            <option value="">Choose</option>
                                            <option selected value="Active">Active</option>
                                            <option value="On Leave">On Leave</option>
                                            <option value="Unavailable">Unavailable</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="row mb-1">
                                    <label class="col-sm-2 col-form-label col-form-label-sm" for="employment_type">Employment Type <span class="float-end d-none d-lg-block">:</span> </label>
                                    <div class="col-sm-3">
                                        <select class="form-control form-control-sm" name="employment_type" id="employment_type">
                                            <option value="">Choose</option>
                                            <option value="Full-Time">Full-Time</option>
                                            <option selected value="Part-Time">Part-Time</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="row mb-1">
                                    <label class="col-sm-2 col-form-label col-form-label-sm" for="joining_date">Joining Date <span class="float-end d-none d-lg-block">:</span> </label>
                                    <div class="col-sm-3">
                                        <input class="form-control form-control-sm" type="date" name="joining_date" id="joining_date" placeholder="Enter Valid Details ...">
                                    </div>
                                </div>
                                <div class="row mb-1">
                                    <label class="col-sm-2 col-form-label col-form-label-sm" for="profile_link">Profile Link <span class="float-end d-none d-lg-block">:</span> </label>
                                    <div class="col-sm-3">
                                        <input class="form-control form-control-sm" type="text" name="profile_link" id="profile_link" placeholder="Enter Valid Details ...">
                                    </div>
                                </div>
                            </div>
    `;
}


async function fetchRoles(selectedRole = null) {
    try {
        // Check token exist
        const authToken = validateUserAuthToken();
        if (!authToken) return;

        // Set loader to the screen 
        fullPageLoader.classList.remove("d-none");

        const url = `${apiURL}roles`;

        const response = await fetch(url, {
            method: 'POST',
            headers: {
                'Authorization': `Bearer ${authToken}`,
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                limit: 0,
                currentPage: 1,
                filters: {}
            })
        });

        if (!response.ok) {
            throw new Error('Failed to fetch data');
        }

        const data = await response.json();

        renderRoles(data.roles || [], selectedRole);

    } catch (error) {
        toasterNotification({ type: 'error', message: 'Request failed: ' + error.message });
        // Set loader to the screen 
    } finally {
        fullPageLoader.classList.add("d-none");
    }
}

// Edit Action code goes here
document.addEventListener('DOMContentLoaded', () => {

    const url = new URL(window.location.href);
    const searchParams = new URLSearchParams(url.search);
    const urlSegments = url.pathname.split('/').filter(segment => segment);
    const userid = urlSegments[urlSegments.length - 1];
    if (searchParams.get('action') === 'edit') {
        fetchUserToDisplayForEdit(userid);
    } else {
        // Fetch User Roles
        fetchRoles();
    }


});

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