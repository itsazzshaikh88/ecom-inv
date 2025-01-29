<div class="content bg-white">
    <nav class="mb-3" aria-label="breadcrumb">
        <ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="#!">Master</a></li>
            <li class="breadcrumb-item"><a href="users">Users</a></li>
            <li class="breadcrumb-item active">New User</li>
        </ol>
    </nav>
    <div>
        <div class="row align-items-center justify-content-end g-3 mb-4">
            <div class="col-auto">
                <div class="d-flex align-items-center">
                    <a href="users" class="btn btn-sm text-primary text-decoration-underline"> <i class="fa-solid fa-users"></i> Users List</a>
                    <a href="users/new" class="btn btn-sm text-primary text-decoration-underline"> <i class="fa-solid fa-user-plus"></i> Create New User</a>
                </div>
            </div>
        </div>
        <div class="mx-n4 mx-lg-n6 px-4 px-lg-6 mb-9 bg-body-emphasis border-top mt-2 position-relative top-1">
            <form id="form" method="post" enctype="multipart/form-data" onsubmit="submitForm(event)">
                <input type="hidden" name="id" id="id">
                <div class="row">
                    <div class="col-md-12 mt-4">
                        <div class="row mb-1">
                            <label class="col-sm-2 col-form-label col-form-label-sm" for="full_name">Full Name <span class="text-danger">*</span> <span class="float-end d-none d-lg-block">:</span> </label>
                            <div class="col-sm-5">
                                <label class="col-form-label col-form-label-sm fw-normal" id="lbl-full_name"></label>
                            </div>
                        </div>
                        <div class="row mb-1">
                            <label class="col-sm-2 col-form-label col-form-label-sm" for="email">Email Address <span class="text-danger">*</span> <span class="float-end d-none d-lg-block">:</span> </label>
                            <div class="col-sm-5">
                                <label class="col-form-label col-form-label-sm fw-normal" id="lbl-email"></label>
                            </div>
                        </div>
                        <div class="row mb-1">
                            <label class="col-sm-2 col-form-label col-form-label-sm" for="phone_number">Contact Number <span class="text-danger">*</span> <span class="float-end d-none d-lg-block">:</span> </label>
                            <div class="col-sm-3">
                                <label class="col-form-label col-form-label-sm fw-normal" id="lbl-phone_number"></label>
                            </div>
                        </div>
                        <div class="row mb-1">
                            <label class="col-sm-2 col-form-label col-form-label-sm" for="role_name">User Role <span class="text-danger">*</span> <span class="float-end d-none d-lg-block">:</span> </label>
                            <div class="col-sm-3">
                                <label class="col-form-label col-form-label-sm fw-normal" id="lbl-role_name"></label>
                            </div>
                        </div>
                        <div class="row mb-1">
                            <label class="col-sm-2 col-form-label col-form-label-sm" for="status">Account Status <span class="text-danger">*</span> <span class="float-end d-none d-lg-block">:</span> </label>
                            <div class="col-sm-3">
                                <label class="col-form-label col-form-label-sm fw-normal" id="lbl-status"></label>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>