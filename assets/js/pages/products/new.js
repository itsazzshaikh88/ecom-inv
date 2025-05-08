let cachedUOMs = [];

let selectedCategory = null;
let selectedUOM = null;

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

        const category_id = document.getElementById("id").value;
        let url = `${apiURL}products/`;
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
}

async function fetchCategoryDetailsForEdit(categoryID) {
    const url = `${apiURL}categories/${categoryID}`;

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
        window.location = 'products/new';

    } catch (error) {
        toasterNotification({ type: 'error', message: 'Request failed: ' + error.message });
        Swal.close();
    }
}

function toggleLoader(elementID, action = 'show') {
    let element = document.getElementById(elementID);
    if (element) {
        if (action === 'show')
            element.classList.remove("d-none");
        else
            element.classList.add("d-none");
    }
}
function toggleElementDisabled(elementID, action = 'enable') {
    let element = document.getElementById(elementID);
    if (element) {
        if (action === 'enable')
            element.disabled = false;
        else
            element.disabled = true;
    }
}

// Fetch Categories and show
async function fetchCategories(selectedCategory = null) {
    try {
        // Check token exist
        const authToken = validateUserAuthToken();
        if (!authToken) return;

        // set loader 
        toggleLoader("category-loader", 'show');
        toggleElementDisabled("category_id", 'disable');

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

        renderCategories(data.categories || [], selectedCategory);

    } catch (error) {
        toasterNotification({ type: 'error', message: 'Request failed: ' + error.message });
        console.error(error);
    } finally {
        toggleLoader("category-loader", 'hide');
        toggleElementDisabled("category_id", 'enable');
    }
}

function renderCategories(categories, selectedCategory = null) {
    if (!categories) return '';
    let categoryElement = document.getElementById("category_id");
    if (categories && categories.length > 0) {
        let content = '<option value="">Choose Category</option>';
        categories.forEach((category) => {
            content += `<option value="${category?.category_id}">${category?.name}</option>`;
        });
        categoryElement.innerHTML = content;
    }

    if (selectedCategory)
        categoryElement.value = selectedCategory;
}

function renderUOMS(selected = null) {
    let content = '<option value="">Choose UOM</option>';
    if (cachedUOMs && cachedUOMs.length > 0) {
        cachedUOMs.forEach((uom) => {
            content += `<option ${selected == uom?.id ? 'selected' : ''} value="${uom?.id}">${uom?.name} - ${uom?.abbreviation}</option>`;
        });
    }
    return content;
}

// Fetch Sub Categories
async function fetchSubcategories(element = null, categoryID = null, subCatgoryID = null, source = 'HTMLSelectElement') {
    let category_id;
    if (source === 'HTMLSelectElement')
        category_id = element.value;
    else
        category_id = categoryID;
    if (category_id) {
        try {
            // Check token exist
            const authToken = validateUserAuthToken();
            if (!authToken) return;

            // Set loader to the screen 
            toggleLoader("sub-category-loader", 'show');
            toggleElementDisabled("sub_category_id", "disable");

            const url = `${apiURL}subcategories`;

            const response = await fetch(url, {
                method: 'POST',
                headers: {
                    'Authorization': `Bearer ${authToken}`,
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    limit: null,
                    currentPage: null,
                    filters: { category_id }
                })
            });

            if (!response.ok) {
                throw new Error('Failed to fetch data');
            }

            const data = await response.json();

            renderSubcategories(data.subcategories || [], subCatgoryID);

        } catch (error) {
            toasterNotification({ type: 'error', message: 'Request failed: ' + error.message });
            console.error(error);
        } finally {
            // Set loader to the screen 
            toggleLoader("sub-category-loader", 'hide');
            toggleElementDisabled("sub_category_id", "enable");
        }
    }
}

function renderSubcategories(subcategories, selectedSubCategory = null) {
    if (!subcategories) return '';
    let subcategoryElement = document.getElementById("sub_category_id");
    if (subcategories && subcategories.length > 0) {
        let content = '<option>Choose Sub Category</option>';
        subcategories.forEach((subcategory) => {
            content += `<option value="${subcategory?.subcategory_id}">${subcategory?.name}</option>`;
        });
        subcategoryElement.innerHTML = content;
    }
    if (selectedSubCategory)
        subcategoryElement.value = selectedSubCategory;
}

function renderUOMSSelectOptions(selected = null) {
    const selectElement = document.getElementById("uoms");
    if (selectElement) {
        selectElement.innerHTML = renderUOMS(selected);
    }
}

// ADD REMOVE OPTIONS
function addVariantRow() {
    const tbody = document.querySelector('#variant-table tbody');

    // Create a new row element
    const row = document.createElement('tr');
    const uomString = renderUOMS();
    row.innerHTML = `
        <td>
            <select class="form-control form-control-sm" name="unit_id[]">${uomString}</select>
        </td>
        <td>
            <input type="text" placeholder="Enter Value of Measurement eg. 250, 500" class="form-control form-control-sm" name="measure[]">
        </td>
        <td>
            <input type="text" placeholder="Enter Price" class="form-control form-control-sm" name="price[]">
        </td>
        <td>
            <input type="text" placeholder="Enter Selling Price" class="form-control form-control-sm" name="sale_price[]">
        </td>
        <td>
            <input type="text" placeholder="Enter Qty" class="form-control form-control-sm" name="stock_qty[]">
        </td>
        <td class="align-middle">
            <i class="fa-regular fa-trash-can text-danger cursor-pointer" onclick="removeRow(this)"></i>
        </td>
    `;

    // Append the new row to the tbody
    tbody.appendChild(row);
}

function removeRow(icon) {
    // Remove the row containing the clicked trash icon
    const row = icon.closest('tr'); // Find the closest <tr> ancestor
    row.remove();
}
// ADD OR REMOVE FILES 
function addFileInput() {
    const container = document.getElementById('file-container');

    // Create a new file input element
    const fileInputElement = document.createElement('div');
    fileInputElement.className = 'col-md-12 border-bottom pb-2';

    fileInputElement.innerHTML = `
        <div class="d-flex align-items-center justify-content-between">
            <label class="col-form-label col-form-label-sm" for="short_description">Product Image<span class="float-end d-none d-lg-block">:</span></label>
            <a href="javascript:void(0)" class="text-danger" onclick="removeFileInput(this)">
                <i class="fa-solid fa-trash-can"></i>
            </a>
        </div>
        <input type="file" class="form-control form-control-sm" name="product_image[]">
    `;

    // Append the new file input element to the container
    container.appendChild(fileInputElement);
}

function removeFileInput(element) {
    // Remove the parent container of the clicked trash icon
    const fileInputElement = element.closest('.col-md-12');
    fileInputElement.remove();
}

// Fetch unit of measurements and show
async function fetchUOMS(type = null, selectedOption = null) {
    try {
        // Check token exist
        const authToken = validateUserAuthToken();
        if (!authToken) return;

        const url = `${apiURL}UOM`;
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

        cachedUOMs = data?.uoms || {};
        renderUOMSSelectOptions(selectedUOM);

    } catch (error) {
        toasterNotification({ type: 'error', message: 'Request failed: ' + error.message });
        console.error(error);
    }
}

// Edit Action code goes here
document.addEventListener('DOMContentLoaded', () => {
    const url = new URL(window.location.href);
    const searchParams = new URLSearchParams(url.search);
    const urlSegments = url.pathname.split('/').filter(segment => segment);
    const productID = urlSegments[urlSegments.length - 2];
    if (searchParams.get('action') === 'edit') {
        fetchProductToDisplayForEdit(productID);
    } else {
        // Fetch User Roles
        fetchCategories();
        fetchUOMS('load');
    }
});

async function fetchProductToDisplayForEdit(productID) {
    const url = `${apiURL}products/${productID}`;

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
            body: JSON.stringify({ productID })
        });

        // Parse the JSON response
        const data = await response.json();

        // Check if the API response contains an error
        if (!response.ok || data.status === 'error') {
            const errorMessage = data.message || `Error: ${response.status} ${response.statusText}`;
            throw new Error(errorMessage);
        }
        selectedCategory = data?.data?.category_id;
        selectedUOM = data?.data?.category_id;
        fetchCategories(data?.data?.category_id);
        if (cachedUOMs && cachedUOMs?.length > 0) {
            renderUOMSSelectOptions(selectedUOM);
        } else {
            fetchUOMS('load', selectedUOM);
        }
        fetchSubcategories(null, data?.data?.category_id, data?.data?.sub_category_id, 'load');
        displayProductInfo(data.data);
        // Fetch roles and set it to selected role
    } catch (error) {
        // Show error notification
        toasterNotification({ type: 'error', message: 'Error: ' + error.message });
        console.error(error);

    } finally {
        fullPageLoader.classList.toggle("d-none");
    }
}

function displayProductInfo(productDetails) {
    if (!productDetails) return;


    if (productDetails && typeof productDetails === 'object' && Object.keys(productDetails).length > 0) {
        populateFormFields(productDetails);
    }
}