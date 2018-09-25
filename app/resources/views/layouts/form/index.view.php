<?php
    $titlePage="form";

    define("base", $_SERVER['DOCUMENT_ROOT']."/app/resources/views/layouts/");

    require base.'base/header.view.php';

?>

    <div class="container-fluid">
        <header>
            <h1>hello this is form page</h1>
            ini adalah halaman form
        </header>
        <?php require "app/resources/views/errors/errors.view.php"; ?>

        <div class="forms">
            <div class="form">
                <a href="/form/tanda-terima">tanda terima</a>
            </div>
            <div class="form">
                <a href="/form/activity-report">activity report</a>
            </div>
            <div class="form">
                <a href="/form/reimburse">reimburse form</a>
            </div> 
            <div class="form">
                <a href="/form/cuti">form cuti</a>
            </div> 
        </div>
    </div>

    <script type="text/javascript">
        <?= $script; ?>
    </script>

<?php
    require base.'base/footer.view.php';
?>