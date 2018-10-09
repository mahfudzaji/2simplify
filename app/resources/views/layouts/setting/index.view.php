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
                        <h3 data-item='name'><?= ucwords($p->name); ?></h3>
                        <h3 data-item='email'><?= makeFirstLetterUpper($p->email); ?></h3>
                        <h3>Code: <span data-item='code'><?= ($p->code=='')?'-':$p->code; ?></span></h3>
                        <h3 data-item='department' data-item-val=<?= $p->idd; ?>><?= "Departemen: ".makeFirstLetterUpper($p->department); ?></h3>
                        <h3><?= "Status: ".$p->active; ?></h3>
                        <h3><?= "Terdaftar pada: ".$p->created_at; ?></h3>
                        <h3><?= "Diperbaharui pada: ". $p->updated_at; ?></h3>
                        <button type="button" class="btn btn-md btn-primary btn-modal" id="update-profile"><span class="glyphicon glyphicon-edit"></span> Update</button>
                    </div>
                <?php endforeach; ?>          
            </div>

        </div>

        <div class="app-form modal" id="modal-update-profile">         
            <div class="modal-content">
                <div class="modal-header">
                    <h3>Perbaharui profile</h3>
                </div>

                <div class="description">
                    <p>Form ini digunakan untuk memperbaharui profile.</p>
                </div>
                <form action="/settings/profile/update" method="post">
                    <div class="form-group">
                        <label>Name</label>
                        <input type="text" name="name" class="form-control" required>
                    </div>
                    
                    <div class="form-group">
                        <label>Email</label>
                        <input type="text" name="email" class="form-control" required>
                    </div>

                    <div class="form-group">
                        <label>Code</label>
                        <input type="text" name="code" class="form-control" required>
                    </div>

                    <div class="form-group">
                        <label>Departemen</label>
                        <select name="department" class="form-control">
                            <option value="">Department</option>
                            <?php foreach($departments as $department): ?>
                                <option value=<?= $department->id; ?> ><?= ucfirst($department->name); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <button type="button" class="btn btn-danger btn-close" >Tutup</button>

                    <div class="nav-right">
                        <button type="submit" name="submit" class="btn btn-primary btn-next">Kirim <span class="glyphicon glyphicon-send"></span></button>
                    </div>
                </form>

                <button type="button" class="btn btn-danger btn-close btn-close-top"><span class="glyphicon glyphicon-remove"></span> </button>

            </div>
        </div>

    </div>
</main>

<script>
    $(document).ready(function(){

        $("#update-profile").on("click", function(){
 
            var department = $(this).parent().find("[data-item~='department']").attr("data-item-val");
            var name = $(this).parent().find("[data-item~='name']").html();
            var code = $(this).parent().find("[data-item~='code']").html();
            var email = $(this).parent().find("[data-item~='email']").html();

            //$("#modal-update-profile").find("select[name~='department']").find("option").attr("selected", false);
            $("#modal-update-profile").find("select[name~='department']").find("option[value~='"+department+"']").attr("selected", true);
            $("#modal-update-profile").find("input[name~='name']").val(name); 
            $("#modal-update-profile").find("input[name~='code']").val(code); 
            $("#modal-update-profile").find("input[name~='email']").val(email);
            
        });
    });
</script>

<?php

require base.'base/footer.view.php'

?>
