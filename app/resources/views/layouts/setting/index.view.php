<?php

$titlePage="Dashboard";

define("base", $_SERVER['DOCUMENT_ROOT']."/app/resources/views/layouts/");

require base.'base/header.view.php';

?>

<main>
    <div class="container-fluid">

        <div class="row">
            
            <div class="col-md-2">
                <?= require 'header.php'; ?>
            </div>

            <div class="col-md-10">
                <?php require "app/resources/views/errors/errors.view.php"; ?>
                
                <h1><?= makeFirstLetterUpper($_GET['c']); ?></h1>
                <hr>

                <?php foreach($profile as $p): ?>
                    <?php $photo = ($p->photo==null)?"StockSnap_Igor_Ovsyannykov.jpg":$p->photo; ?>
                    <div class="col-md-2">
                        <img src="/public/upload/<?= $photo; ?>" class="img-responsive">
                    </div>
                    <div class="col-md-10">
                        <h3><?= ucwords($p->name); ?></h3>
                        <h3><?= makeFirstLetterUpper($p->email); ?></h3>
                        <h3><?= "Code: ".$p->code; ?></h3>
                        <h3><?= "Departemen: ".makeFirstLetterUpper($p->department); ?></h3>
                        <h3><?= "Status: ".$p->active; ?></h3>
                        <h3><?= "Terdaftar pada: ".$p->created_at; ?></h3>
                        <h3><?= "Diperbaharui pada: ". $p->updated_at; ?></h3>
                        <button type="button" class="btn btn-md btn-primary"><span class="glyphicon glyphicon-edit"></span> Update</button>
                    </div>
                <?php endforeach; ?>          
            </div>

        </div>

    </div>
</main>

<script>
    $(document).ready(function(){

    });
</script>

<?php

require base.'base/footer.view.php'

?>
