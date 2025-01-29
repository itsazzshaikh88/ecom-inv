const tableId = "users-table";
const table = document.getElementById(tableId);
const tbody = document.querySelector(`#${tableId} tbody`);
const numberOfHeaders = document.querySelectorAll(`#${tableId} thead th`).length || 0;

function renderNoResponseCode(option, isAdmin = false) {
    return ``;
}

// Set Up Paginate
const paginate = new Pagination({
    currentPageId: 'current-page',
    totalPagesId: 'total-pages',
    pageOfPageId: 'page-of-pages',
    recordsRangeId: 'range-of-records'
});
paginate.pageLimit = 10; // Set your page limit here

const accountStatus = {
    active: "success",
    inactive: "danger",
    banned: "danger",
    locked: "warning",
    hold: "secondary",
}
const accountType = {
    admin: "success",
    user: "danger",
    verifier: "info",
    subadmin: "secondary"
}

function renderUsersList(users) {
    const usersTbody = document.querySelector(`#${tableId} tbody`);

    if (!users) {
        throw new Error("users list not found");
    }

    if (users && users.length > 0) {
        let content = ''
        let counter = 0;
        users.forEach(user => {

            content += `<tr data-user-id=${user.id}>
                            <td>${++counter}</td>
                            <td><p class="">${user.full_name}</p></td>
                            <td>
                                <p class="text-primary mb-0"> <span class=""><i class="fa-regular fa-envelope mb-0"></i></span> ${user.email}</p>
                            </td>
                            <td>${user.phone_number}</td>
                            <td>
                                <span class="badge badge-phoenix badge-phoenix-secondary">${capitalizeWords(user.role_name)}</span>
                            </td>
                            <td>
                                <span class="badge badge-phoenix badge-phoenix-${accountStatus[user.status || '']}">${capitalizeWords(user.status)}</span>
                            </td>
                            <td>${formatAppDate(user.created_at)}</td>
                            <td class="text-center">
                                <div class="d-flex align-items-center justify-content-center gap-3">
                                    <a href="users/view/${user.id}" class="text-info app-fs-md" title="View User"><i class="fa-solid fa-up-right-from-square"></i></a>
                                    <a href="users/new/${user.id}?action=edit" class="text-primary app-fs-md" title="Edit User"><i class="fa-solid fa-file-pen"></i></a>
                                    <a href="javascript:void(0)" onclick="deleteUser(${user.id})" class="text-danger app-fs-md" title="Delete User"><i class="fa-solid fa-trash-can"></i></a>
                                </div>
                            </td>
                        </tr>`
        });
        usersTbody.innerHTML = '';
        usersTbody.innerHTML = content;

    } else {
        usersTbody.innerHTML = renderNoResponseCode();
    }

}

async function fetchUsersList() {
    try {
        // Check token exist
        const authToken = validateUserAuthToken();
        if (!authToken) return;

        // Set loader to the screen 
        const skeletonLoaderContent = commonSkeletonContent(numberOfHeaders);
        repeatAndAppendSkeletonContent(tableId, skeletonLoaderContent, paginate.pageLimit || 0);

        const url = `${apiURL}users`;
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

        renderUsersList(data.users || []);

    } catch (error) {
        toasterNotification({ type: 'error', message: 'Request failed: ' + error.message });
        tbody.innerHTML = renderNoResponseCode();
        console.error(error);
    }
}

async function deleteusers(usersID) {

    if (!usersID) {
        throw new Error("Invalid users ID, Please try Again");
    }

    try {

        // Show a confirmation alert
        const confirmation = await Swal.fire({
            title: "Are you sure?",
            text: "Do you really want to delete users? This action cannot be undone.",
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
            title: "Deleting users ...",
            text: "Please wait while the users is being deleted.",
            icon: "info",
            showConfirmButton: false,
            allowOutsideClick: false,
            customClass: {
                popup: 'small-swal',
            },
        });

        const url = `${apiURL}/users/delete/${usersID}`;

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

            fetchUsersList();
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
    fetchUsersList();
});
function handlePagination(action) {
    paginate.paginate(action); // Update current page based on the action
    fetchUsersList(); // Fetch records
}

function filterContacts() {
    paginate.currentPage = 1;
    fetchUsersList();
}

async function deleteUser(userID) {
    if (!userID) {
        throw new Error("Invalid User ID, Please try Again");
    }

    try {

        // Show a confirmation alert
        const confirmation = await Swal.fire({
            title: "Are you sure?",
            text: "Do you really want to delete User? This action cannot be undone.",
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
            title: "Deleting User...",
            text: "Please wait while the User is being deleted.",
            icon: "info",
            showConfirmButton: false,
            allowOutsideClick: false,
            customClass: {
                popup: 'small-swal',
            },
        });

        const url = `${apiURL}/users/delete/${userID}`;

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
            throw new Error(data.error || 'Failed to delete User details');
        }

        if (data.status) {
            // Here, we directly handle the deletion without checking data.status
            toasterNotification({ type: 'success', message: 'User Deleted Successfully' });
            // Logic to remove the current row from the table
            fetchUsersList();
        } else {
            throw new Error(data.message || 'Failed to delete User details');
        }

    } catch (error) {
        toasterNotification({ type: 'error', message: 'Request failed: ' + error.message });
        Swal.close();
    }
}