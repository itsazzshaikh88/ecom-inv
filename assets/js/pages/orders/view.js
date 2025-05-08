const orderTbody = document.querySelector('#order-details tbody');
const orderItemsTbody = document.querySelector('#order-table tbody');


async function fetchOrderDetails(orderID) {
    const url = `${apiURL}orders/${orderID}`;

    const authToken = validateUserAuthToken();
    if (!authToken) return;


    try {

        fullPageLoader.classList.toggle("d-none");
        // Fetch product data from the API
        const response = await fetch(url, {
            method: 'GET',
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

        displayOrderInfo(data.data);
    } catch (error) {
        // Show error notification
        toasterNotification({ type: 'error', message: 'Error: ' + error.message });
        console.error(error);

    } finally {
        fullPageLoader.classList.toggle("d-none");
    }
}
function displayOrderInfo(data) {
    console.log(data);

    // Use optional chaining to avoid errors if properties are missing
    const header = data?.header ?? {};
    const lines = data?.lines ?? [];
    const customer = data?.customer ?? {};

    if (header) {
        showOrderHeader(header, customer);
    }
    if (header) {
        showOrderItems(lines);
    }
}

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

function showOrderItems(lines) {
    if (lines?.length > 0) {
        let content = '';
        lines.forEach((line, index) => {
            content += `<tr>
                            <td class="py-2">${index + 1}</td>
                            <td class="py-2">${line?.name}</td>
                            <td class="py-2">${line?.selling_price}</td>
                            <td class="py-2">${line?.quantity}</td>
                            <td class="py-2">${line?.total_price}</td>
                        </tr>`;
        })
        orderItemsTbody.innerHTML = content;
    } else {
        orderItemsTbody.innerHTML = ``
    }

}

function showOrderHeader(header, customer) {
    if (header) {
        let receiptRow = '';
        
        // Show payment receipt row if the payment mode is UPI
        if (header?.payment_mode === 'upi' && header?.payment_receipt) {
            const receiptUrl = `${FRONT_STORE}uploads/payments/${header.payment_receipt}`;
            receiptRow = `
                <tr>
                    <th>Payment Receipt <span class="float-end">:</span> </th>
                    <td>
                        <a href="${receiptUrl}" target="_blank" class="btn btn-link text-primary">Click to View Payment Receipt</a>
                    </td>
                </tr>`;
        }

        orderTbody.innerHTML = `
            <tr>
                <th>Order Number <span class="float-end">:</span> </th>
                <td>${header?.order_number}</td>
            </tr>
            <tr>
                <th>Order At <span class="float-end">:</span> </th>
                <td>${header?.created_at}</td>
            </tr>
            <tr>
                <th>Payment Mode <span class="float-end">:</span> </th>
                <td>${capitalizeWords(header?.payment_mode, true)}</td>
            </tr>
            ${receiptRow}
            <tr>
                <th>Payment Status <span class="float-end">:</span> </th>
                <td>
                    <span class="badge badge-phoenix badge-phoenix-${paymentStatusColors[header.payment_status || '']} rounded-pill">
                        ${header.payment_status || ''}
                    </span>
                </td>
            </tr>
            <tr>
                <th>Order Status <span class="float-end">:</span> </th>
                <td>
                    <span class="badge badge-phoenix badge-phoenix-${orderStatusColors[header.status || '']} rounded-pill">
                        ${header.status || ''}
                    </span>
                </td>
            </tr>
            <tr>
                <th>Total Amount <span class="float-end">:</span> </th>
                <td>Rs. ${header?.total_amount} /-</td>
            </tr>
        `;

        // Set form values
        document.getElementById("id").value = header?.id || '';
        document.getElementById("status").value = header?.status || '';
        document.getElementById("payment_status").value = header?.payment_status || '';
    } else {
        orderTbody.innerHTML = ``;
    }
}



// Update order status
async function updateOrderStatus(e) {
    e.preventDefault();
    const form = e.target;
    const formData = new FormData(form);


    // Set Loading Animation on button
    const submitBtn = document.getElementById("submit-btn");
    let buttonText = submitBtn.innerHTML;
    submitBtn.disabled = true;
    submitBtn.innerHTML = `Saving Order ...`;

    // Hide Error
    hideErrors();
    try {
        // Retrieve the auth_token from cookies
        const authToken = validateUserAuthToken();
        if (!authToken) return;

        const order_id = document.getElementById("id").value;
        let url = `${apiURL}orders/update-order-details/${order_id}`;
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
            setTimeout(() => window.location.reload(), 200);
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

// Edit Action code goes here
document.addEventListener('DOMContentLoaded', () => {
    const url = new URL(window.location.href);
    const searchParams = new URLSearchParams(url.search);
    const urlSegments = url.pathname.split('/').filter(segment => segment);
    const orderID = urlSegments[urlSegments.length - 2];
    fetchOrderDetails(orderID);

});