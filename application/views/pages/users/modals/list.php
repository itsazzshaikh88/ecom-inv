<div class="modal fade" id="userListingModal" tabindex="-1" aria-hidden="true" style="display: none;">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-light d-flex flex-column justify-content-start align-items-start">
                <div class="w-100 d-flex align-items-center justify-content-between mb-4">
                    <h6 class="modal-title fs-8" id="exampleModalLabel">Users List</h6>
                    <button class="btn btn-close p-1" type="button" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="w-100 d-flex align-items-center justify-content-between gap-2">
                    <input type="text" class="form-control form-control-sm flex-1" name="filter_product_name" id="filter_product_name" autocomplete="off" placeholder="Search User Name ....">
                    <button class="btn btn-sm btn-secondary"><i class="fa-solid fa-magnifying-glass"></i> Search</button>
                </div>
            </div>
            <div class="modal-body">
                <div class="row" id="user-list-container">
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <?= renderPaginate('user-listing-modal-current-page', 'user-listing-modal-total-pages', 'user-listing-modal-page-of-pages', 'user-listing-modal-range-of-records') ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>