const tableId = "upi-table";
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


function renderUPIList(upi) {
    const upiTbody = document.querySelector(`#${tableId} tbody`);

    if (!upi) {
        throw new Error("upi list not found");
    }

    if (upi && upi.length > 0) {
        let content = ''
        let counter = 0;
        upi.forEach(upi => {
            let upiImage = `uploads/upi/${upi.qr_code_image}`;
            content += `<tr data-upi-id=${upi.upi_id}>
                            <td>${++counter}</td>
                            <td><p class="mb-0">${upi.upi_name}</p></td>
                            <td><a class="" href="${upiImage || 'javascript:void(0)'}">View</a></td>
                            <td>
                            <span class="badge badge-phoenix badge-phoenix-${upi.is_active == '1' ? 'success' : 'danger'}">${upi.is_active == '1' ? 'Active' : 'In-Active'}</span>
                            </td>
                            <td class="text-center">
                                <div class="d-flex align-items-center justify-content-center gap-2">
                                    <a href="javascript:void(0)" onclick="fetchUPIDetailsForEdit(${upi.id})" class="text-secondary app-fs-md" title="Edit upi"><i class="fa-solid fa-file-pen fs-9"></i></a>
                                    <a href="javascript:void(0)" onclick="deleteUPI(${upi.id})" class="text-danger app-fs-md" title="Delete upi"><i class="fa-solid fa-trash-can fs-9"></i></a>
                                </div>
                            </td>
                        </tr>`
        });
        upiTbody.innerHTML = '';
        upiTbody.innerHTML = content;

    } else {
        upiTbody.innerHTML = renderNoResponseCode({ colspan: numberOfHeaders });
    }

}

async function fetchUPIList() {
    try {
        // Check token exist
        const authToken = validateUserAuthToken();
        if (!authToken) return;

        // Set loader to the screen 
        const skeletonLoaderContent = commonSkeletonContent(numberOfHeaders);
        repeatAndAppendSkeletonContent(tableId, skeletonLoaderContent, paginate.pageLimit || 0);

        const url = `${apiURL}UPI`;
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

        renderUPIList(data.upi || []);

    } catch (error) {
        toasterNotification({ type: 'error', message: 'Request failed: ' + error.message });
        tbody.innerHTML = renderNoResponseCode({ colspan: numberOfHeaders });
        console.error(error);
    }
}

async function deleteUPI(upiID) {

    if (!upiID) {
        throw new Error("Invalid upi ID, Please try Again");
    }

    try {

        // Show a confirmation alert
        const confirmation = await Swal.fire({
            title: "Are you sure?",
            text: "Do you really want to delete upi? This action cannot be undone.",
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
            title: "Deleting upi ...",
            text: "Please wait while the upi is being deleted.",
            icon: "info",
            showConfirmButton: false,
            allowOutsideClick: false,
            customClass: {
                popup: 'small-swal',
            },
        });

        const url = `${apiURL}/UPI/delete/${upiID}`;

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

            fetchUPIList();
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
    fetchUPIList();
});
function handlePagination(action) {
    paginate.paginate(action); // Update current page based on the action
    fetchUPIList(); // Fetch records
}

function filterContacts() {
    paginate.currentPage = 1;
    fetchUPIList();
}
