<div class="content bg-white">
    <nav class="mb-3" aria-label="breadcrumb">
        <ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="#!">Master</a></li>
            <li class="breadcrumb-item"><a href="users">Users</a></li>
            <li class="breadcrumb-item active">New User</li>
        </ol>
    </nav>
    <div>
        <div class="row align-items-center justify-content-between g-3 mb-4">
            <div class="col-auto">
                <h2 class="text-bold text-body-emphasis">Create New User</h2>
            </div>
            <div class="col-auto">
                <div class="d-flex align-items-center gap-2">
                    <button onclick="startOverNew()" class="btn btn-sm btn-secondary"> <i class="fa-solid fa-refresh"></i> Start Over New User</button>
                    <a href="users" class="btn btn-sm text-primary text-decoration-underline"> <i class="fa-solid fa-users"></i> Users List</a>
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
                                <input class="form-control form-control-sm" type="text" name="full_name" id="full_name" placeholder="Enter User Full Name">
                                <p class="text-danger err-lbl mb-0 app-fs-sm" id="lbl-full_name"></p>
                            </div>
                        </div>
                        <div class="row mb-1">
                            <label class="col-sm-2 col-form-label col-form-label-sm" for="email">Email Address <span class="text-danger">*</span> <span class="float-end d-none d-lg-block">:</span> </label>
                            <div class="col-sm-5">
                                <input class="form-control form-control-sm" type="email" name="email" id="email" placeholder="Enter User's Personal or Professional Email Address">
                                <p class="text-danger err-lbl mb-0 app-fs-sm" id="lbl-email"></p>
                            </div>
                        </div>
                        <div class="row mb-1">
                            <label class="col-sm-2 col-form-label col-form-label-sm" for="phone_number">Contact Number <span class="text-danger">*</span> <span class="float-end d-none d-lg-block">:</span> </label>
                            <div class="col-sm-3">
                                <input class="form-control form-control-sm" type="text" name="phone_number" id="phone_number" placeholder="Enter Valid Contact Number">
                                <p class="text-danger err-lbl mb-0 app-fs-sm" id="lbl-phone_number"></p>
                            </div>
                        </div>
                        <div class="row mb-1">
                            <label class="col-sm-2 col-form-label col-form-label-sm" for="role_id">User Role <span class="text-danger">*</span> <span class="float-end d-none d-lg-block">:</span> </label>
                            <div class="col-sm-3">
                                <select class="form-control form-control-sm" name="role_id" id="role_id" onchange="renderAdditionalDetails()">
                                    <option value="">Choose Role</option>
                                </select>
                                <p class="text-danger err-lbl mb-0 app-fs-sm" id="lbl-role_id"></p>
                            </div>
                        </div>
                        <div class="row mb-1">
                            <label class="col-sm-2 col-form-label col-form-label-sm" for="status">Account Status <span class="text-danger">*</span> <span class="float-end d-none d-lg-block">:</span> </label>
                            <div class="col-sm-3">
                                <select class="form-control form-control-sm" name="status" id="status">
                                    <option value="">Choose</option>
                                    <option selected value="active">Active</option>
                                    <option value="inactive">Inactive</option>
                                    <option value="banned">Banned</option>
                                    <option value="locked">Locked</option>
                                    <option value="hold">Hold</option>
                                </select>
                                <p class="text-danger err-lbl mb-0 app-fs-sm" id="lbl-status"></p>
                            </div>
                        </div>
                        <div class="row mt-4" id="additional-user-details"></div>
                        <div class="row mb-2 password-row">
                            <div class="col-md-12">
                                <hr>
                            </div>
                            <label class="col-sm-2 col-form-label col-form-label-sm" for="password">Set Password <span class="text-danger">*</span> <span class="float-end d-none d-lg-block">:</span> </label>
                            <div class="col-sm-5">
                                <input class="form-control form-control-sm" type="password" name="password" id="password" placeholder="Choose Strong Password">
                                <p class="text-danger err-lbl mb-0 app-fs-sm" id="lbl-password"></p>
                            </div>
                            <!-- <div class="col-sm-5">
                            <label class="col-form-label col-form-label-sm text-warning">Password Selection Criteria</label>
                            <ul>
                                <li><small>Minimum Length: Passwords should be at least 8-12 characters long.</small></li>
                                <li>
                                    <small>Character Variety: Include a mix of the following:</small>
                                    <ul>
                                        <li><small>Uppercase letters (A-Z)</small></li>
                                        <li><small>Lowercase letters (a-z)</small></li>
                                        <li><small>Numbers (0-9)</small></li>
                                        <li><small>Special characters (e.g., !, @, #, $, %, ^, &, *)</small></li>
                                    </ul>
                                </li>
                            </ul>
                        </div> -->
                        </div>
                        <div class="row mt-4">
                            <div class="col-md-12 text-center">
                                <button class="btn btn-sm btn-success" id="submit-btn"> <i class="fa-solid fa-plus"></i> Save User Details</button>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>