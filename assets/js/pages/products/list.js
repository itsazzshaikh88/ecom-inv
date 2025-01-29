const tableId = "products-table";
const table = document.getElementById(tableId);
const tbody = document.querySelector(`#${tableId} tbody`);
const numberOfHeaders = document.querySelectorAll(`#${tableId} thead th`).length || 0;

function renderNoResponseCode(option, isAdmin = false) {
    return `<tr><td class="text-center" colspan="${option?.colspan}">No data available</td></tr>`;
}

// Set Up Paginate
const paginate = new Pagination({
    currentPageId: 'current-page',
    totalPagesId: 'total-pages',
    pageOfPageId: 'page-of-pages',
    recordsRangeId: 'range-of-records'
});
paginate.pageLimit = 10; // Set your page limit here


function renderProductList(products) {
    const productsTbody = document.querySelector(`#${tableId} tbody`);

    if (!products) {
        throw new Error("products list not found");
    }

    if (products && products.length > 0) {
        let content = ''
        let counter = 0;
        products.forEach(product => {
            let stockIcon = `${parseFloat(product.stock_quantity || 0) < parseFloat(product.low_stock_threshold || 0) ? "<i class='fa-solid fa-arrow-down text-danger'></i>" : "<i class='fa-solid fa-arrow-up text-success'></i>"}`;

            content += `<tr data-product-id=${product.product_id}>
                            <td>${++counter}</td>
                            <td class="fw-bold text-primary"><p class="mb-0">${product.name || ''}</p></td>
                            <td><p class="mb-0">${product.category_name || ''}</p></td>
                            <td><p class="mb-0">${product.subcategory_name || ''}</p></td>
                            <td><p class="mb-0 fw-bold">${stockIcon} ${product.stock_quantity || '0'}  </p></td>
                            <td><p class="mb-0 fw-bold">${product.low_stock_threshold || '0'}</p></td>
                            <td><p class="mb-0">${product.is_featured === '1' ? 'Yes' : 'No'}</p></td>
                            <td>
                            <span class="badge badge-phoenix badge-phoenix-${product.is_active == '1' ? 'success' : 'danger'}">${product.is_active == '1' ? 'Active' : 'In-Active'}</span>
                            </td>
                            <td>${formatAppDate(product?.created_at)}</td>
                            <td class="text-center">
                                <div class="d-flex align-items-center justify-content-center gap-2">
                                    <a href="products/new/${product?.id}/${product?.slug}?action=edit" class="text-secondary app-fs-md" title="Edit product"><i class="fa-solid fa-file-pen fs-9"></i></a>
                                    <a href="javascript:void(0)" onclick="deleteProduct(${product.id})" class="text-danger app-fs-md" title="Delete product"><i class="fa-solid fa-trash-can fs-9"></i></a>
                                </div>
                            </td>
                        </tr>`
        });
        productsTbody.innerHTML = '';
        productsTbody.innerHTML = content;

    } else {
        productsTbody.innerHTML = renderNoResponseCode({ colspan: numberOfHeaders });
    }

}

async function fetchProductList() {
    try {
        // Check token exist
        const authToken = validateUserAuthToken();
        if (!authToken) return;

        // Set loader to the screen 
        const skeletonLoaderContent = commonSkeletonContent(numberOfHeaders);
        repeatAndAppendSkeletonContent(tableId, skeletonLoaderContent, paginate.pageLimit || 0);

        const url = `${apiURL}products`;
        const filters = filterCriterias([]);

        const response = await fetch(url, {
            method: 'POST',
            headers: {
                'Authorization': `Bearer ${authToken}`,
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                limit: paginate.pageLimit,
                currentPage: paginate.currentPage,
                filters: filters
            })
        });

        if (!response.ok) {
            throw new Error('Failed to fetch data');
        }

        const data = await response.json();
        paginate.totalPages = parseFloat(data?.pagination?.total_pages) || 0;
        paginate.totalRecords = parseFloat(data?.pagination?.total_records) || 0;

        renderProductList(data.products || []);

    } catch (error) {
        toasterNotification({ type: 'error', message: 'Request failed: ' + error.message });
        tbody.innerHTML = renderNoResponseCode({ colspan: numberOfHeaders });
        console.error(error);
    }
}

async function deleteProduct(productID) {

    if (!productID) {
        throw new Error("Invalid product ID, Please try Again");
    }

    try {

        // Show a confirmation alert
        const confirmation = await Swal.fire({
            title: "Are you sure?",
            text: "Do you really want to delete product? This action cannot be undone.",
            icon: "warning",
            showCancelButton: true,
            confirmButtonText: "Yes, delete it",
            cancelButtonText: "Cancel",
            customClass: {
                popup: 'small-swal',
                confirmButton: 'swal-confirm-btn',
                cancelButton: 'swal-cancel-btn',
            },
        });

        if (!confirmation.isConfirmed) return;

        const authToken = validateUserAuthToken();
        if (!authToken) return;

        // Show a non-closable alert box while the activity is being deleted
        Swal.fire({
            title: "Deleting product ...",
            text: "Please wait while the product is being deleted.",
            icon: "info",
            showConfirmButton: false,
            allowOutsideClick: false,
            customClass: {
                popup: 'small-swal',
            },
        });

        const url = `${apiURL}/products/delete/${productID}`;

        const response = await fetch(url, {
            method: 'DELETE', // Change to DELETE for a delete request
            headers: {
                'Authorization': `Bearer ${authToken}`
            }
        });

        const data = await response.json(); // Parse the JSON response

        // Close the loading alert box
        Swal.close();

        if (!response.ok) {
            // If the response is not ok, throw an error with the message from the response
            throw new Error(data.error || 'Failed to delete users details');
        }

        if (data.status) {
            // Here, we directly handle the deletion without checking data.status
            toasterNotification({ type: 'success', message: data?.message || "Record Deleted Successfully" });

            fetchProductList();
        } else {
            throw new Error(data.message || 'Failed to delete users details');
        }

    } catch (error) {
        toasterNotification({ type: 'error', message: 'Request failed: ' + error.message });
        Swal.close();
    }
}

document.addEventListener('DOMContentLoaded', () => {
    // Fetch initial User data
    fetchProductList();
});
function handlePagination(action) {
    paginate.paginate(action); // Update current page based on the action
    fetchProductList(); // Fetch records
}

function filterContacts() {
    paginate.currentPage = 1;
    fetchProductList();
}
