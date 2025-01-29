<form id="form" method="post" enctype="multipart/form-data" onsubmit="submitForm(event)">
    <input type="hidden" name="subcategory_id" id="subcategory_id">
    <div class="row">
        <div class="col-md-12 mt-4">
            <div class="row mb-1">
                <label class="col-sm-4 col-form-label col-form-label-sm" for="category_id">Category <span class="text-danger">*</span> <span class="float-end d-none d-lg-block">:</span> </label>
                <div class="col-sm-8">
                    <select class="form-control form-control-sm" name="category_id" id="category_id">
                        <option value="">Choose</option>
                    </select>
                    <p class="text-danger err-lbl mb-0 app-fs-sm" id="lbl-category_id"></p>
                </div>
            </div>
            <div class="row mb-1">
                <label class="col-sm-4 col-form-label col-form-label-sm" for="name">Sub Category Name <span class="text-danger">*</span> <span class="float-end d-none d-lg-block">:</span> </label>
                <div class="col-sm-8">
                    <input class="form-control form-control-sm" type="text" name="name" id="name" placeholder="Enter User Full Name">
                    <p class="text-danger err-lbl mb-0 app-fs-sm" id="lbl-name"></p>
                </div>
            </div>
            <div class="row mb-1">
                <label class="col-sm-4 col-form-label col-form-label-sm" for="is_active">Status <span class="text-danger">*</span> <span class="float-end d-none d-lg-block">:</span> </label>
                <div class="col-sm-4">
                    <select class="form-control form-control-sm" name="is_active" id="is_active">
                        <option value="">Choose</option>
                        <option selected value="1">Active</option>
                        <option value="0">Inactive</option>
                    </select>
                    <p class="text-danger err-lbl mb-0 app-fs-sm" id="lbl-is_active"></p>
                </div>
            </div>
            <div class="row mb-1">
                <label class="col-sm-4 col-form-label col-form-label-sm" for="description">Description <span class="float-end d-none d-lg-block">:</span> </label>
                <div class="col-sm-8">
                    <textarea class="form-control form-control-sm" rows="3" name="description" id="description" placeholder="Enter Description ..."></textarea>
                </div>
            </div>
            <div class="row mb-1">
                <label class="col-sm-4 col-form-label col-form-label-sm" for="image_url">Image <span class="float-end d-none d-lg-block">:</span> </label>
                <div class="col-sm-8">
                    <input class="form-control form-control-sm" type="file" name="image_url" id="image_url" placeholder="Enter User Full Name">
                </div>
            </div>
            <div class="row mb-1">
                <label class="col-sm-4 col-form-label col-form-label-sm" for="banner_image_url">Banner Image <span class="float-end d-none d-lg-block">:</span> </label>
                <div class="col-sm-8">
                    <input class="form-control form-control-sm" type="file" name="banner_image_url" id="banner_image_url" placeholder="Enter User Full Name">
                </div>
            </div>
            <div class="row mb-1">
                <label class="col-sm-4 col-form-label col-form-label-sm" for="alt_text">Alternate Text <span class="float-end d-none d-lg-block">:</span> </label>
                <div class="col-sm-8">
                    <input class="form-control form-control-sm" type="text" name="alt_text" id="alt_text" placeholder="Enter User Full Name">
                </div>
            </div>
            <div class="row mt-4">
                <div class="col-md-12 text-center">
                    <button class="btn btn-sm btn-success" id="submit-btn"> <i class="fa-solid fa-plus"></i> Save Role Details</button>
                </div>
            </div>
        </div>
    </div>
</form>