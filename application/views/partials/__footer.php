<?php
$this->load->view('loaders/full-page-loader');
?>
</main><!-- ===============================================-->
<!--    End of Main Content-->
<!-- ===============================================-->

<!-- ===============================================-->
<!--    JavaScripts-->
<!-- ===============================================-->
<script>
    // Initialize the developer object and its properties if they don't already exist
    window.developer = window.developer || {};
    window.loggedInUser = window.loggedInUser || '';
    window.developer.config = window.developer.config || {};
    window.developer.config.urls = {
        baseURL: <?= json_encode(base_url()) ?>,
        apiURL: <?= json_encode(base_url('api/' . API_VERSION . '/')) ?>
    };

    // Assign LoggedInUser
    window.loggedInUser = '<?= base64_encode(json_encode($loggedInUser)) ?>';

    const {
        baseURL,
        apiURL
    } = window.developer.config.urls;

    const fullPageLoader = document.getElementById("full-page-loader");

    function toggleFullPageLoader(action = 'show') {
        if (action === 'show')
            fullPageLoader.classList.remove("d-none");
        else
            fullPageLoader.classList.add("d-none");
    }
</script>

<script src="vendors/popper/popper.min.js"></script>
<script src="vendors/bootstrap/bootstrap.min.js"></script>
<script src="vendors/anchorjs/anchor.min.js"></script>
<script src="vendors/is/is.min.js"></script>
<script src="vendors/fontawesome/all.min.js"></script>
<script src="vendors/lodash/lodash.min.js"></script>
<script src="vendors/list.js/list.min.js"></script>
<script src="vendors/feather-icons/feather.min.js"></script>
<script src="vendors/dayjs/dayjs.min.js"></script>
<script src="vendors/leaflet/leaflet.js"></script>
<script src="vendors/leaflet.markercluster/leaflet.markercluster.js"></script>
<script src="vendors/leaflet.tilelayer.colorfilter/leaflet-tilelayer-colorfilter.min.js"></script>
<script src="assets/js/phoenix.js"></script>
<script src="vendors/echarts/echarts.min.js"></script>
<script src="assets/js/ecommerce-dashboard.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script type="text/javascript" src="https://cdn.jsdelivr.net/npm/toastify-js"></script>
<script src="assets/js/ckeditor.js"></script>
<script src="assets/js/helpers/ckeditor.js"></script>


<!-- user defined js files  -->
<script src="assets/js/helpers/app_helper.js"></script>
<script src="assets/js/helpers/skeleton.js"></script>
<script src="assets/js/loaders/skeleton-loaders.js"></script>
<script src="assets/js/pages/common.js"></script>
<script src="assets/js/pages/app.js"></script>
<script src="assets/js/pages/pagination.js"></script>



<?php
if (isset($scripts) && is_array($scripts)):
    foreach ($scripts as $script):
?>
        <script src="<?= $script ?>"></script>
<?php
    endforeach;
endif; ?>
</body>

</html>