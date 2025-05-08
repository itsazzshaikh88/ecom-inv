<div class="content">
    <nav class="mb-3" aria-label="breadcrumb">
        <ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="#!">Inventory</a></li>
            <li class="breadcrumb-item active">Products</li>
        </ol>
    </nav>
    <div>
        <div class="row align-items-center justify-content-between g-3 mb-4">
            <div class="col-auto">
                <h2 class="text-bold text-body-emphasis">All Products</h2>
            </div>
            <div class="col-auto">
                <div class="d-flex align-items-center gap-2">
                    <a href="products/new" class="btn btn-sm text-primary text-decoration-underline"> <i class="fa-solid fa-plus"></i> Add New Product</a>
                </div>
            </div>
        </div>
        <style>
            .product-listing-image-container {
                height: 40px;
                width: 40px;
            }

            .product-listing-image-container img {
                height: 100%;
                width: 100%;
                object-fit: contain;
            }
        </style>
        <div class="mx-n4 mx-lg-n6 px-4 px-lg-6 mb-9 bg-body-emphasis border-y mt-2 position-relative top-1">
            <div class="table-responsive scrollbar ms-n1 ps-1">
                <table class="table fs-9 mb-0" id="products-table">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Image</th>
                            <th>Product</th>
                            <th>Category</th>
                            <th>Price</th>
                            <th>Selling Price</th>
                            <th>Stock Qty</th>
                            <th>Is Featured</th>
                            <th>Status</th>
                            <th>Created At</th>
                            <th class="text-center">Action</th>
                        </tr>
                    </thead>
                    <tbody id="products-table-tbody">

                    </tbody>
                </table>
            </div>
            <div class="py-2">
                <?= renderPaginate('current-page', 'total-pages', 'page-of-pages', 'range-of-records') ?>
            </div>
        </div>
    </div>
</div>