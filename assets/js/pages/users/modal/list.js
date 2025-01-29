// Set Up Paginate
const UserModalListPagination = new Pagination({
    currentPageId: 'user-listing-modal-current-page',
    totalPagesId: 'user-listing-modal-total-pages',
    pageOfPageId: 'user-listing-modal-page-of-pages',
    recordsRangeId: 'user-listing-modal-range-of-records'
});
UserModalListPagination.pageLimit = 10;

var userListingModal = new bootstrap.Modal(document.getElementById("userListingModal"), {
    keyboard: false,        // Disable closing on escape key
    backdrop: 'static'      // Disable closing when clicking outside the modal
});

const userListContainer = document.getElementById("user-list-container");

function renderNoDataCode() {
    return ``;
}

function openUserListingModal(filters) {
    userListingModal.show();
    fetchUsersForModals(filters);
}

function closeUserListingModal() {

}
const accountStatus = {
    active: "success",
    inactive: "danger",
    banned: "danger",
    locked: "warning",
    hold: "secondary",
}


function renderUserListItem(user) {
    const details = {
        id: user?.id,
        full_name: user?.full_name,
    };

    return `
        <div class="col-md-12 cursor-pointer mb-3" 
             onclick='setUser(${JSON.stringify(details)})'>
            <div class="border-bottom border-light">
                <div class="w-100 d-flex align-items-center justify-content-between mb-2">
                    <div class="flex-1">
                        <h5>${user?.full_name}</h5>
                        <p class="mb-0 fs-9 fw-bold text-primary">
                            <small>${user?.user_id}</small>
                        </p>
                    </div>
                    <div class="d-flex gap-1">
                        <small class="badge border border-${accountStatus[user?.status]} text-${accountStatus[user?.status]}">${user?.status}</small>
                    </div>
                </div>
                <p class="fs-9 mb-1 fw-bold">
                    ${user?.role_name}
                </p>
            </div>
        </div>`;
}



function renderUsersList(users) {
    if (!users) {
        userListContainer.innerHTML = renderNoDataCode();
        return;
    }
    let content = '';
    if (users && users.length > 0) {
        users.forEach(user => content += renderUserListItem(user));
        userListContainer.innerHTML = content;
    } else {
        userListContainer.innerHTML = renderNoDataCode();
    }


}

async function fetchUsersForModals(filters = {}) {
    try {
        // Check token exist
        const authToken = validateUserAuthToken();
        if (!authToken) return;

        // Set loader to the screen 
        // const skeletonLoaderContent = commonSkeletonContent(numberOfHeaders);
        // repeatAndAppendSkeletonContent(tableId, skeletonLoaderContent, UserModalListPagination.pageLimit || 0);

        const url = `${apiURL}users`;

        const response = await fetch(url, {
            method: 'POST',
            headers: {
                'Authorization': `Bearer ${authToken}`,
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                limit: UserModalListPagination.pageLimit,
                currentPage: UserModalListPagination.currentPage,
                filters: filters
            })
        });

        if (!response.ok) {
            throw new Error('Failed to fetch data');
        }

        const data = await response.json();
        UserModalListPagination.totalPages = parseFloat(data?.pagination?.total_pages) || 0;
        UserModalListPagination.totalRecords = parseFloat(data?.pagination?.total_records) || 0;

        renderUsersList(data?.users || {});


    } catch (error) {
        toasterNotification({ type: 'error', message: 'Request failed: ' + error.message });
        console.error(error);
    }
}