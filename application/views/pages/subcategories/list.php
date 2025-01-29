<div class="content bg-white">
    <nav class="mb-3" aria-label="breadcrumb">
        <ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="#!">Inventory</a></li>
            <li class="breadcrumb-item active">Manage Sub Categories</li>
        </ol>
    </nav>
    <div>
        <div class="row align-items-center justify-content-between g-3 mb-4">
            <div class="col-auto">
                <h3 class="text-bold text-body-emphasis">Sub Category Management</h3>
            </div>
        </div>
        <div class="mx-n4 mx-lg-n6 px-4 px-lg-6 mb-9 bg-body-emphasis border-top mt-2 position-relative top-1">
            <div class="row mt-4">
                <div class="col-md-6">
                    <h5>Create New Sub Category</h5>
                    <?php $this->load->view('pages/subcategories/new') ?>
                </div>
                <div class="col-md-6">
                    <div class="table-responsive scrollbar ms-n1 ps-1">
                        <table class="table fs-9 mb-0" id="subcategories-table">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Category</th>
                                    <th>Subcategory </th>
                                    <th>Status</th>
                                    <th>Description</th>
                                    <th class="text-center">Action</th>
                                </tr>
                            </thead>
                            <tbody id="categories-table-tbody">

                            </tbody>
                        </table>
                    </div>
                    <div class="py-2">
                        <?= renderPaginate('current-page', 'total-pages', 'page-of-pages', 'range-of-records') ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>