<?php
defined('BASEPATH') or exit('No direct script access allowed');
?>
<!DOCTYPE html>
<html>

<head>
    <base href="<?= base_url() ?>">
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- ===============================================-->
    <!--    Document Title-->
    <!-- ===============================================-->
    <title>Phoenix</title>

    <!-- ===============================================-->
    <!--    Favicons-->
    <!-- ===============================================-->
    <link rel="apple-touch-icon" sizes="180x180" href="assets/img/favicons/apple-touch-icon.png">
    <link rel="icon" type="image/png" sizes="32x32" href="assets/img/favicons/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="assets/img/favicons/favicon-16x16.png">
    <link rel="shortcut icon" type="image/x-icon" href="assets/img/favicons/favicon.ico">
    <meta name="msapplication-TileImage" content="assets/img/favicons/mstile-150x150.png">
    <meta name="theme-color" content="#ffffff">
    <script src="vendors/simplebar/simplebar.min.js"></script>
    <script src="assets/js/config.js"></script>
    <link rel="stylesheet" href="assets/css/pages/app.css">
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/toastify-js/src/toastify.min.css">

    <!-- ===============================================-->
    <!--    Stylesheets-->
    <!-- ===============================================-->

    <link href="https://fonts.googleapis.com/css2?family=Nunito+Sans:wght@300;400;600;700;800;900&amp;display=swap" rel="stylesheet">
    <link href="vendors/simplebar/simplebar.min.css" rel="stylesheet">

    <link href="assets/css/theme-rtl.min.css" type="text/css" rel="stylesheet" id="style-rtl">
    <link href="assets/css/theme.min.css" type="text/css" rel="stylesheet" id="style-default">
    <link href="assets/css/user-rtl.min.css" type="text/css" rel="stylesheet" id="user-style-rtl">
    <link href="assets/css/user.min.css" type="text/css" rel="stylesheet" id="user-style-default">
    <script>
        var phoenixIsRTL = window.config.config.phoenixIsRTL;
        if (phoenixIsRTL) {
            var linkDefault = document.getElementById('style-default');
            var userLinkDefault = document.getElementById('user-style-default');
            linkDefault.setAttribute('disabled', true);
            userLinkDefault.setAttribute('disabled', true);
            document.querySelector('html').setAttribute('dir', 'rtl');
        } else {
            var linkRTL = document.getElementById('style-rtl');
            var userLinkRTL = document.getElementById('user-style-rtl');
            linkRTL.setAttribute('disabled', true);
            userLinkRTL.setAttribute('disabled', true);
        }
    </script>
    <style>
        .form-label {
            padding-left: 0px !important;
        }
    </style>
</head>

<body>
    <!-- ===============================================-->
    <!--    Main Content-->
    <!-- ===============================================-->
    <main class="main" id="top">
        <div class="container-fluid bg-body-tertiary dark__bg-gray-1200">
            <div class="bg-holder bg-auth-card-overlay" style="background-image:url(assets/img/bg/37.png);"></div>
            <!--/.bg-holder-->
            <div class="row flex-center position-relative min-vh-100 g-0 py-5">
                <div class="col-11 col-sm-10 col-xl-8">
                    <div class="card border border-translucent auth-card">
                        <div class="card-body pe-md-0">
                            <div class="row align-items-center gx-0 gy-7">
                                <div class="col-auto bg-body-highlight dark__bg-gray-1100 rounded-3 position-relative overflow-hidden auth-title-box">
                                    <div class="bg-holder" style="background-image:url(assets/img/bg/38.png);"></div>
                                    <!--/.bg-holder-->
                                    <div class="position-relative px-4 px-lg-7 pt-7 pb-7 pb-sm-5 text-center text-md-start pb-lg-7 pb-md-7">

                                    </div>
                                    <div class="position-relative z-n1 mb-6 d-none d-md-block text-center mt-md-15"><img class="auth-title-box-img d-dark-none" src="assets/images/login-bg.png" alt="" />
                                        <img class="auth-title-box-img d-light-none" src="assets/img/spot-illustrations/auth-dark.png" alt="" />
                                    </div>
                                </div>
                                <div class="col mx-auto">
                                    <form id="form" method="POST" enctype="multipart/form-data" onsubmit="validate(event)">
                                        <div class="auth-form-box">
                                            <div class="text-center mb-7"><a class="d-flex flex-center text-decoration-none mb-4" href="index.html">
                                                    <div class="d-flex align-items-center fw-bolder fs-3 d-inline-block"><img src="assets/img/logo/logo-task.png" alt="TaskUp" width="58" /></div>
                                                </a>
                                                <h3 class="text-body-highlight">Sign In</h3>
                                                <p class="text-body-tertiary">Get access to your account</p>
                                            </div>
                                            <div class="mb-3 text-start">
                                                <label class="form-label" for="email">Email address</label>
                                                <div class="form-icon-container">
                                                    <input class="form-control form-icon-input" type="email" placeholder="name@example.com" name="email" id="email" />
                                                    <span class="fas fa-user text-body fs-9 form-icon"></span>
                                                    <p class="text-danger err-lbl mb-0 my-1 app-fs-sm" id="lbl-email"></p>
                                                </div>
                                            </div>
                                            <div class="mb-3 text-start">
                                                <label class="form-label" for="password">Password</label>
                                                <div class="form-icon-container" data-password="data-password">
                                                    <input class="form-control form-icon-input pe-6" id="password" type="password" placeholder="Password" name="password" />
                                                    <span class="fas fa-key text-body fs-9 form-icon"></span>
                                                    <button class="btn px-3 py-0 h-100 position-absolute top-0 end-0 fs-7 text-body-tertiary" data-password-toggle="data-password-toggle">
                                                        <span class="uil uil-eye show"></span>
                                                        <span class="uil uil-eye-slash hide"></span>
                                                    </button>
                                                    <p class="text-danger err-lbl mb-0 my-1 app-fs-sm" id="lbl-password"></p>
                                                </div>
                                            </div>
                                            <div class="row flex-between-center mb-7">
                                                <div class="col-auto">
                                                    <div class="form-check mb-0">
                                                        <input class="form-check-input" id="basic-checkbox" type="checkbox" checked="checked" />
                                                        <label class="form-check-label mb-0" for="basic-checkbox">Remember me</label>
                                                    </div>
                                                </div>
                                                <div class="col-auto">
                                                    <a class="fs-9 fw-semibold" href="forgot-password.html">Forgot Password?</a>
                                                </div>
                                            </div>
                                            <button type="submit" id="login-btn" class="btn btn-primary w-100 mb-3">Sign In</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <script src="vendors/popper/popper.min.js"></script>
    <script src="vendors/bootstrap/bootstrap.min.js"></script>
    <script src="vendors/anchorjs/anchor.min.js"></script>
    <script src="vendors/is/is.min.js"></script>
    <script src="vendors/fontawesome/all.min.js"></script>
    <script src="vendors/lodash/lodash.min.js"></script>
    <script src="vendors/list.js/list.min.js"></script>
    <script src="vendors/feather-icons/feather.min.js"></script>
    <script src="vendors/dayjs/dayjs.min.js"></script>
    <script src="assets/js/phoenix.js"></script>
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/toastify-js"></script>
    <script src="assets/js/pages/common.js"></script>
    <script src="assets/js/helpers/app_helper.js"></script>
    <script src="assets/js/pages/auth/login.js"></script>
</body>

</html>