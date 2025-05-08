const tableId = "orders-users-table";
const table = document.getElementById(tableId);
const tbody = document.querySelector(`#${tableId} tbody`);
const numberOfHeaders = document.querySelectorAll(`#${tableId} thead th`).length || 0;

function renderNoResponseCode(option, isAdmin = false) {
    return `<tr><td class="text-center" colspan="${option?.colspan}">No Users available</td></tr>`;
}

// Set Up Paginate
const paginate = new Pagination({
    currentPageId: 'current-page',
    totalPagesId: 'total-pages',
    pageOfPageId: 'page-of-pages',
    recordsRangeId: 'range-of-records'
});
paginate.pageLimit = 10; // Set your page limit here

const statusColor = {
    active: "success",         // Green
    inactive: "danger",        // Red
    banned: "danger",
};

function renderUserList(users) {
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
                            <td>${user.full_name || ''}</td>
                            <td>${user.phone || ''}</td>
                            <td>${user.email || ''}</td>
                            <td>${user.billing_address || ''}</td>
                            <td>${user.shipping_address || ''}</td>
                            <td><p class="mb-0 badge badge-phoenix badge-phoenix-${statusColor[user.status || '']} rounded-pill">${user.status || ''}</p></td>
                            <td>${formatAppDate(user?.created_at)}</td>
                        </tr>`
        });
        usersTbody.innerHTML = '';
        usersTbody.innerHTML = content;

    } else {
        usersTbody.innerHTML = renderNoResponseCode({ colspan: numberOfHeaders });
    }

}

async function fetchUsers() {
    try {
        // Check token exist
        const authToken = validateUserAuthToken();
        if (!authToken) return;

        // Set loader to the screen 
        const skeletonLoaderContent = commonSkeletonContent(numberOfHeaders);
        repeatAndAppendSkeletonContent(tableId, skeletonLoaderContent, paginate.pageLimit || 0);

        const url = `${apiURL}orders/users-list`;
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

        renderUserList(data.users || []);

    } catch (error) {
        toasterNotification({ type: 'error', message: 'Request failed: ' + error.message });
        tbody.innerHTML = renderNoResponseCode({ colspan: numberOfHeaders });
        console.error(error);
    }
}


document.addEventListener('DOMContentLoaded', () => {
    // Fetch initial User data
    fetchUsers();
});
function handlePagination(action) {
    paginate.paginate(action); // Update current page based on the action
    fetchUsers(); // Fetch records
}

function filterContacts() {
    paginate.currentPage = 1;
    fetchUsers();
}
