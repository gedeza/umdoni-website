<script type="text/javascript" src="<?php url('assets/js/jquery-slim.min.js'); ?>"></script>
<script type="text/javascript" src="<?php url('assets/js/popper.min.js'); ?>"></script>
<script type="text/javascript" src="<?php echo url("assets/js/jquery-slim.min.js"); ?>"></script>
<script type="text/javascript" src="<?php echo url('assets/js/bootstrap.min.js'); ?>"></script>
<script type="text/javascript" src="<?php echo url('assets/css/bootstrap/js/bootstrap.min.js'); ?>"></script>
<script type="text/javascript" src="<?php echo url('assets/js/bootstrap.bundle.min.js'); ?>"></script>
<script type="text/javascript" src="<?php echo url('assets/css/bootstrap/js/bootstrap.min.js'); ?>"></script>
<script type="text/javascript" src="<?php echo url("themes/mazor/assets/vendors/simple-datatables/simple-datatables.js") ?>"></script>
<script defer src="https://use.fontawesome.com/releases/v5.15.4/js/all.js" integrity="sha384-rOA1PnstxnOBLzCLMcre8ybwbTmemjzdNlILg8O7z1lUkLXozs4DHonlDtnE7fpc" crossorigin="anonymous"></script>
<script type="text/javascript" src="https://cdn.jsdelivr.net/npm/toastify-js"></script>
<!-- Removed external Bootstrap demo script - not for production use -->
<script type="text/javascript" src="<?php echo url('assets/js/offcanvas-navbar.js'); ?>"></script>
<script src="<?php echo url("themes/mazor/assets/vendors/simple-datatables/simple-datatables.js") ?>"></script>  
<script>
    // Initialize DataTables only if elements exist
    let table1 = document.querySelector('#table1');
    let table2 = document.querySelector('#table2');
    let table3 = document.querySelector('#table3');

    if (table1) {
        let dataTable = new simpleDatatables.DataTable(table1);
    }
    if (table2) {
        let dataTable2 = new simpleDatatables.DataTable(table2);
    }
    if (table3) {
        let dataTable3 = new simpleDatatables.DataTable(table3);
    }
</script>