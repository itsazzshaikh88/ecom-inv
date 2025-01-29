<?php
$core_links = sidebarCoreLinks();

$usertype = strtolower($loggedInUser['usertype']) ?? 'guest';
?>

<nav class="navbar navbar-vertical navbar-expand-lg">
    <div class="collapse navbar-collapse" id="navbarVerticalCollapse">
        <!-- scrollbar removed-->
        <div class="navbar-vertical-content">
            <ul class="navbar-nav flex-column" id="navbarVerticalNav">
                <li class="nav-item">
                    <!-- parent pages-->
                    <div class="nav-item-wrapper"><a class="nav-link label-1" href="<?= base_url() ?>" role="button" data-bs-toggle="" aria-expanded="false">
                            <div class="d-flex align-items-center"><span class="nav-link-icon">
                                    <i class="fa fa-house"></i>
                                </span><span class="nav-link-text-wrapper"><span class="nav-link-text">Home</span></span></div>
                        </a></div>
                </li>
                <li class="nav-item">
                    <!-- label-->
                    <p class="navbar-vertical-label">Inventory</p>
                    <hr class="navbar-vertical-line" /><!-- parent pages-->

                    <div class="nav-item-wrapper">
                        <a class="nav-link label-1" href="categories" role="button" data-bs-toggle="" aria-expanded="false">
                            <div class="d-flex align-items-center">
                                <span class="nav-link-icon"><i class="fa-solid fa-layer-group"></i></span>
                                <span class="nav-link-text-wrapper">
                                    <span class="nav-link-text">Categories</span>
                                </span>
                            </div>
                        </a>
                    </div>
                    <div class="nav-item-wrapper">
                        <a class="nav-link label-1" href="subcategories" role="button" data-bs-toggle="" aria-expanded="false">
                            <div class="d-flex align-items-center">
                                <span class="nav-link-icon"><i class="fa-solid fa-folder-tree"></i></span>
                                <span class="nav-link-text-wrapper">
                                    <span class="nav-link-text">Sub Categories</span>
                                </span>
                            </div>
                        </a>
                    </div>
                    <div class="nav-item-wrapper">
                        <a class="nav-link label-1" href="products" role="button" data-bs-toggle="" aria-expanded="false">
                            <div class="d-flex align-items-center">
                                <span class="nav-link-icon"><i class="fa-solid fa-boxes-stacked"></i></span>
                                <span class="nav-link-text-wrapper">
                                    <span class="nav-link-text">Products</span>
                                </span>
                            </div>
                        </a>
                    </div>
                </li>
                <li class="nav-item">
                    <!-- label-->
                    <p class="navbar-vertical-label">Orders</p>
                    <hr class="navbar-vertical-line" /><!-- parent pages-->
                    <div class="d-flex flex-column gap-1" id="sidebarProjectListContainer"></div>
                </li>
                <li class="nav-item">
                    <!-- label-->
                    <p class="navbar-vertical-label">Activities</p>
                    <hr class="navbar-vertical-line" /><!-- parent pages-->

                </li>
                <li class="nav-item">
                    <!-- label-->
                    <p class="navbar-vertical-label">Account & Settings</p>
                    <hr class="navbar-vertical-line" /><!-- parent pages-->
                    <div class="nav-item-wrapper">
                        <a class="nav-link label-1" href="settings" role="button" data-bs-toggle="" aria-expanded="false">
                            <div class="d-flex align-items-center">
                                <span class="nav-link-icon"><i class="fa-solid fa-gear"></i></span>
                                <span class="nav-link-text-wrapper">
                                    <span class="nav-link-text">Settings</span>
                                </span>
                            </div>
                        </a>
                    </div>
                    <div class="nav-item-wrapper">
                        <a class="nav-link label-1" href="account/password" role="button" data-bs-toggle="" aria-expanded="false">
                            <div class="d-flex align-items-center">
                                <span class="nav-link-icon"><i class="fa-solid fa-key"></i></span>
                                <span class="nav-link-text-wrapper">
                                    <span class="nav-link-text">Change Password</span>
                                </span>
                            </div>
                        </a>
                    </div>
                    <div class="nav-item-wrapper mt-2">
                        <a class="nav-link label-1 text-danger border border-danger" href="javascript:void(0)" onclick="logoutAction()" role="button" data-bs-toggle="" aria-expanded="false">
                            <div class="d-flex align-items-center">
                                <span class="nav-link-icon"><i class="fa-solid fa-right-from-bracket"></i></span>
                                <span class="nav-link-text-wrapper">
                                    <span class="nav-link-text">Log Out</span>
                                </span>
                            </div>
                        </a>
                    </div>
                </li>
            </ul>
        </div>
    </div>
    <div class="navbar-vertical-footer">
        <button class="btn navbar-vertical-toggle border-0 fw-semibold w-100 white-space-nowrap d-flex align-items-center">
            <span class="uil uil-left-arrow-to-left fs-8"><i class="fa-solid fa-caret-left"></i></span>
            <span class="uil uil-arrow-from-right fs-8"><i class="fa-solid fa-caret-right"></i></span>
            <span class="navbar-vertical-footer-text ms-2">Collapsed View</span>
        </button>
    </div>
</nav>
<?php
$this->load->view('partials/__topnav');
?>