const tableId = "orders-table";
const table = document.getElementById(tableId);
const tbody = document.querySelector(`#${tableId} tbody`);
const numberOfHeaders = document.querySelectorAll(`#${tableId} thead th`).length || 0;

function renderNoResponseCode(option, isAdmin = false) {
    return `<tr><td class="text-center" colspan="${option?.colspan}">No orders available</td></tr>`;
}

// Set Up Paginate
const paginate = new Pagination({
    currentPageId: 'current-page',
    totalPagesId: 'total-pages',
    pageOfPageId: 'page-of-pages',
    recordsRangeId: 'range-of-records'
});
paginate.pageLimit = 10; // Set your page limit here

const orderStatusColors = {
    Pending: "warning",      // Yellow
    Processing: "primary",   // Blue
    Shipped: "info",         // Light Blue
    Delivered: "success",    // Green
    Canceled: "danger"       // Red
};

const paymentStatusColors = {
    Pending: "warning",      // Yellow
    Paid: "success",         // Green
    Failed: "danger",        // Red
    Refunded: "secondary"    // Gray
};

function renderOrderList(orders) {
    const ordersTbody = document.querySelector(`#${tableId} tbody`);

    if (!orders) {
        throw new Error("orders list not found");
    }

    if (orders && orders.length > 0) {
        let content = ''
        let counter = 0;
        orders.forEach(order => {
            content += `<tr data-order-id=${order.id}>
                            <td>${++counter}</td>
                            <td><p class="mb-0">${order.full_name || ''}</p></td>
                            <td><p class="mb-0">${order.phone || ''}</p></td>
                            <td><p class="mb-0">${order.billing_address || ''}</p></td>
                            <td><p class="mb-0 badge badge-phoenix badge-phoenix-${orderStatusColors[order.status || '']} rounded-pill">${order.status || ''}</p></td>
                            <td><p class="mb-0">Rs. ${order.total_amount || ''} /-</p></td>
                            <td><p class="mb-0 badge badge-phoenix badge-phoenix-${paymentStatusColors[order.payment_status || '']} rounded-pill">${order.payment_status || ''}</p></td>
                            <td><p class="mb-0">${order.payment_mode?.toUpperCase() || ''}</p></td>
                            
                            <td>${formatAppDate(order?.created_at)}</td>
                            <td class="text-center">
                                <div class="d-flex align-items-center justify-content-center gap-2">
                                    <a href="orders/view/${order?.id}/${order?.order_number}" class="text-secondary app-fs-md" title="View Order"><i class="fa-solid fa-file-pen fs-9"></i></a>
                                    <a href="javascript:void(0)" onclick="deleteOrder(${order.id})" class="text-danger app-fs-md" title="Delete Order"><i class="fa-solid fa-trash-can fs-9"></i></a>
                                </div>
                            </td>
                        </tr>`
        });
        ordersTbody.innerHTML = '';
        ordersTbody.innerHTML = content;

    } else {
        ordersTbody.innerHTML = renderNoResponseCode({ colspan: numberOfHeaders });
    }

}

async function fetchOrders() {
    try {
        // Check token exist
        const authToken = validateUserAuthToken();
        if (!authToken) return;

        // Set loader to the screen 
        const skeletonLoaderContent = commonSkeletonContent(numberOfHeaders);
        repeatAndAppendSkeletonContent(tableId, skeletonLoaderContent, paginate.pageLimit || 0);

        const url = `${apiURL}orders`;
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

        renderOrderList(data.orders || []);

    } catch (error) {
        toasterNotification({ type: 'error', message: 'Request failed: ' + error.message });
        tbody.innerHTML = renderNoResponseCode({ colspan: numberOfHeaders });
        console.error(error);
    }
}

async function deleteOrder(orderID) {

    if (!orderID) {
        throw new Error("Invalid order ID, Please try Again");
    }

    try {

        // Show a confirmation alert
        const confirmation = await Swal.fire({
            title: "Are you sure?",
            text: "Do you really want to delete order? This action cannot be undone.",
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
            title: "Deleting order ...",
            text: "Please wait while the order is being deleted.",
            icon: "info",
            showConfirmButton: false,
            allowOutsideClick: false,
            customClass: {
                popup: 'small-swal',
            },
        });

        const url = `${apiURL}/orders/delete/${orderID}`;

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
            throw new Error(data.error || 'Failed to delete order');
        }

        if (data.status) {
            // Here, we directly handle the deletion without checking data.status
            toasterNotification({ type: 'success', message: data?.message || "Record Deleted Successfully" });

            fetchOrders();
        } else {
            throw new Error(data.message || 'Failed to delete order');
        }

    } catch (error) {
        toasterNotification({ type: 'error', message: 'Request failed: ' + error.message });
        Swal.close();
    }
}

document.addEventListener('DOMContentLoaded', () => {
    // Fetch initial User data
    fetchOrders();
});
function handlePagination(action) {
    paginate.paginate(action); // Update current page based on the action
    fetchOrders(); // Fetch records
}

function filterContacts() {
    paginate.currentPage = 1;
    fetchOrders();
}
