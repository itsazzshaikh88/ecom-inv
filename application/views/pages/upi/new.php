<form id="form" method="post" enctype="multipart/form-data" onsubmit="submitForm(event)">
    <input type="hidden" name="id" id="id">
    <div class="row">
        <div class="col-md-12 mt-4">
            <div class="row mb-1">
                <label class="col-sm-4 col-form-label col-form-label-sm" for="upi_name">UPI Name <span class="text-danger">*</span> <span class="float-end d-none d-lg-block">:</span> </label>
                <div class="col-sm-8">
                    <input class="form-control form-control-sm" type="text" name="upi_name" id="upi_name" placeholder="Enter User Full Name">
                    <p class="text-danger err-lbl mb-0 app-fs-sm" id="lbl-upi_name"></p>
                </div>
            </div>
            <div class="row mb-1">
                <label class="col-sm-4 col-form-label col-form-label-sm" for="qr_code_image">QR Code Image <span class="float-end d-none d-lg-block">:</span> </label>
                <div class="col-sm-8">
                    <input class="form-control form-control-sm" type="file" name="qr_code_image" id="qr_code_image" placeholder="Enter User Full Name">
                    <p class="text-danger err-lbl mb-0 app-fs-sm" id="lbl-qr_code_image"></p>
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
            <div class="row mt-4">
                <div class="col-md-12 text-center">
                    <button class="btn btn-sm btn-success" id="submit-btn"> <i class="fa-solid fa-plus"></i> Save UPI </button>
                </div>
            </div>
        </div>
    </div>
</form>