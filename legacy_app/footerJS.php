<!-- Bootstrap core JavaScript-->
<script src="vendor/jquery/jquery.min.js"></script>
<script src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script src="vendor/datatables/jquery.dataTables.min.js"></script>
<script src="vendor/datatables/dataTables.bootstrap4.min.js"></script>

<!-- Core plugin JavaScript-->
<script src="vendor/jquery-easing/jquery.easing.min.js"></script>

<!-- Custom scripts for all pages-->
<script src="js/sb-admin-2.min.js"></script>

<!-- Page level plugins -->
<script src="vendor/chart.js/Chart.min.js"></script>

<!-- Page level custom scripts -->
<script src="js/demo/chart-area-demo.js"></script>
<script src="js/demo/chart-pie-demo.js"></script>

<!-- Toastify JS -->
<script>
if (window.jQuery && $.fn && $.fn.DataTable) {
    $.fn.dataTable.ext.errMode = 'none';

    if (typeof window.DataTable === 'undefined') {
        window.DataTable = function(selector, options) {
            const defaults = {
                pageLength: 10,
                lengthMenu: [10, 25, 50, 100],
                scrollCollapse: false
            };

            return $(selector).DataTable($.extend(true, {}, defaults, options || {}));
        };

        window.DataTable.isDataTable = function(selector) {
            return $.fn.DataTable.isDataTable(selector);
        };
    }
}
</script>
