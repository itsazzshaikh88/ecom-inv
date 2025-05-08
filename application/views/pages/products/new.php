<div class="content">
    <nav class="mb-3" aria-label="breadcrumb">
        <ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="#!">Inventory</a></li>
            <li class="breadcrumb-item active">Manage Products</li>
        </ol>
    </nav>
    <div>
        <div class="row align-items-center justify-content-between g-3 mb-4">
            <div class="col-auto">
                <h2 class="text-bold text-body-emphasis">Create New Product</h2>
            </div>
            <div class="col-auto">
                <div class="d-flex align-items-center gap-2">
                    <button onclick="startOverNew()" class="btn btn-sm btn-secondary"> <i class="fa-solid fa-refresh"></i> Start Over New Product</button>
                    <a href="products" class="btn btn-sm text-primary text-decoration-underline"> <i class="fa-solid fa-users"></i> Product List</a>
                </div>
            </div>
        </div>
        <div class="mx-n4 mx-lg-n6 px-4 px-lg-6 mb-9 bg-body-emphasis border-top mt-2 position-relative top-1">
            <div class="row">
                <div class="col-md-12">
                    <form id="form" method="post" enctype="multipart/form-data" onsubmit="submitForm(event)">
                        <input type="hidden" name="id" id="id">
                        <div class="row">
                            <div class="col-md-12 mt-4">
                                <h5 class="mb-3">General Details</h5>
                                <div class="row mb-1">
                                    <label class="col-sm-3 col-form-label col-form-label-sm" for="category_id">Product Category <span class="text-danger">*</span> <span class="float-end d-none d-lg-block">:</span> </label>
                                    <div class="col-sm-3">
                                        <div class="d-flex align-items-center gap-2">
                                            <select class="form-control form-control-sm" name="category_id" id="category_id">
                                                <option value="">Choose</option>
                                            </select>
                                            <p class="mb-0 d-none" id="category-loader"><i class="fa-solid fa-spinner fa-spin text-primary"></i></p>
                                        </div>
                                        <p class="text-danger err-lbl mb-0 app-fs-sm" id="lbl-category_id"></p>
                                    </div>
                                    <label class="col-sm-3 col-form-label col-form-label-sm" for="is_featured"><span class="float-end d-none d-lg-block">Featured On Homepage:</span> </label>
                                    <div class="col-sm-3">
                                        <div class="d-flex align-items-center gap-4">
                                            <div class="d-flex align-items-center gap-2">
                                                <input type="radio" name="is_featured" id=""> Yes
                                            </div>
                                            <div class="d-flex align-items-center gap-2">
                                                <input checked type="radio" name="is_featured" id=""> No
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row mb-1">
                                    <label class="col-sm-3 col-form-label col-form-label-sm" for="name">Product Name <span class="text-danger">*</span> <span class="float-end d-none d-lg-block">:</span> </label>
                                    <div class="col-sm-8">
                                        <input class="form-control form-control-sm" type="text" name="name" id="name" placeholder="Enter Product Name ...">
                                        <p class="text-danger err-lbl mb-0 app-fs-sm" id="lbl-name"></p>
                                    </div>
                                </div>
                                <div class="row mb-1">
                                    <label class="col-sm-3 col-form-label col-form-label-sm" for="is_active">Status <span class="text-danger">*</span> <span class="float-end d-none d-lg-block">:</span> </label>
                                    <div class="col-sm-3">
                                        <select class="form-control form-control-sm" name="is_active" id="is_active">
                                            <option value="">Choose</option>
                                            <option selected value="1">Active</option>
                                            <option value="0">Inactive</option>
                                        </select>
                                        <p class="text-danger err-lbl mb-0 app-fs-sm" id="lbl-is_active"></p>
                                    </div>
                                </div>
                                <div class="row mb-1">
                                    <label class="col-sm-3 col-form-label col-form-label-sm" for="description">Description <span class="float-end d-none d-lg-block">:</span> </label>
                                    <div class="col-sm-8">
                                        <textarea rows="5" class="form-control form-control-sm" rows="3" name="description" id="description" placeholder="Enter Description ..."></textarea>
                                    </div>
                                </div>
                                <!-- <div class="row my-4">
                                    <div class="col-md-8 bg-light py-3">
                                        <div class="d-flex align-items-center justify-content-between">
                                            <h5 class="mb-3">Product Variants</h5>
                                            <button type="button" class="btn btn-sm btn-secondary" onclick="addVariantRow()"><i class="fa-solid fa-plus mb-0"></i></button>
                                        </div>
                                        <table class="table table-sm mt-2" id="variant-table">
                                            <thead>
                                                <tr class="fw-normal">
                                                    <th class="fw-bold app-fs-sm">Unit</th>
                                                    <th class="fw-bold app-fs-sm">Measurement</th>
                                                    <th class="fw-bold app-fs-sm">Price</th>
                                                    <th class="fw-bold app-fs-sm">Sale Price</th>
                                                    <th class="fw-bold app-fs-sm">Stock Qty</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                            </tbody>
                                        </table>
                                    </div>
                                    <div class="col-md-4 py-3">
                                        <div class="d-flex align-items-center justify-content-between">
                                            <h5 class="mb-3">Product Images</h5>
                                            <p class="fw-bold cursor-pointer text-primary"><small class="text-decoration-underline" onclick="addFileInput()"> <i class="fa-solid fa-plus"></i> Add New</small></p>
                                        </div>
                                        <div class="row mb-1" id="file-container">
                                            <div class="col-md-12 border-bottom pb-2">
                                                <div class="d-flex align-items-center justify-content-between">
                                                    <label class="col-form-label col-form-label-sm" for="short_description">Product Main Image<span class="float-end d-none d-lg-block">:</span> </label>
                                                </div>
                                                <input type="file" class="form-control form-control-sm" name="product_image[]">
                                            </div>
                                        </div>
                                    </div>
                                </div> -->
                                <div class="row mb-1">
                                    <label class="col-sm-3 col-form-label col-form-label-sm" for="uoms">Unit of Measurement<span class="text-danger">*</span><span class="float-end d-none d-lg-block">:</span> </label>
                                    <div class="col-sm-3">
                                        <select class="form-control form-control-sm" name="uoms" id="uoms">
                                        </select>
                                        <p class="text-danger err-lbl mb-0 app-fs-sm" id="lbl-uoms"></p>
                                    </div>
                                    <label class="col-sm-2 col-form-label col-form-label-sm text-end" for="qty_measure">Qty/Measure <span class="text-danger">*</span> : </label>
                                    <div class="col-sm-3">
                                        <input class="form-control form-control-sm" type="text" name="qty_measure" id="qty_measure" placeholder="Enter Measure of product">
                                        <p class="text-danger err-lbl mb-0 app-fs-sm" id="lbl-qty_measure"></p>
                                    </div>
                                </div>
                                <div class="row mb-1">
                                    <label class="col-sm-3 col-form-label col-form-label-sm" for="product_price">Product Price / MRP<span class="text-danger">*</span><span class="float-end d-none d-lg-block">:</span> </label>
                                    <div class="col-sm-3">
                                        <input class="form-control form-control-sm" type="text" name="product_price" id="product_price" placeholder="Enter product price">
                                        <p class="text-danger err-lbl mb-0 app-fs-sm" id="lbl-product_price"></p>
                                    </div>
                                    <label class="col-sm-2 col-form-label col-form-label-sm text-end" for="selling_price">Selling Price <span class="text-danger">*</span> : </label>
                                    <div class="col-sm-3">
                                        <input class="form-control form-control-sm" type="text" name="selling_price" id="selling_price" placeholder="Enter selling price">
                                        <p class="text-danger err-lbl mb-0 app-fs-sm" id="lbl-selling_price"></p>
                                    </div>
                                </div>
                                <div class="row mb-1">
                                    <label class="col-sm-3 col-form-label col-form-label-sm" for="stock_quantity">Stock Quantity<span class="text-danger">*</span><span class="float-end d-none d-lg-block">:</span> </label>
                                    <div class="col-sm-3">
                                        <input class="form-control form-control-sm" type="text" name="stock_quantity" id="stock_quantity" placeholder="Enter onhand stock qty">
                                        <p class="text-danger err-lbl mb-0 app-fs-sm" id="lbl-stock_quantity"></p>
                                    </div>
                                    <label class="col-sm-2 col-form-label col-form-label-sm text-end" for="low_stock_threshold">Low Stock Threshold <span class="text-danger">*</span> : </label>
                                    <div class="col-sm-3">
                                        <input class="form-control form-control-sm" type="text" name="low_stock_threshold" id="low_stock_threshold" placeholder="Enter lowest qty to alert ...">
                                        <p class="text-danger err-lbl mb-0 app-fs-sm" id="lbl-low_stock_threshold"></p>
                                    </div>
                                </div>
                                <div class="row mb-1">
                                    <label class="col-sm-3 col-form-label col-form-label-sm" for="product_image">Product Images <span class="float-end d-none d-lg-block">:</span> </label>
                                    <div class="col-sm-5">
                                        <input type="file" class="form-control form-control-sm" name="product_image[]" multiple id="product_image" placeholder="Enter product_image ..."></input>
                                    </div>
                                </div>
                                <div class="row mt-4">
                                    <div class="col-md-12 text-center">
                                        <button class="btn btn-sm btn-success" id="submit-btn"> <i class="fa-solid fa-plus"></i> Save Product Details</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>