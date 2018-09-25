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
                <div class="row">
                    <div class="col-md-6">
                        <h2>Daftar user</h2>
                    </div>
                    <div class="col-md-6">
                        <h2>Mendaftarkan user</h2>
                        <form action='/register' method='POST' enctype="multipart/form-data">
                            <input type="hidden" name="private" value=1>
                            <div class="form-group">
                                <label>Nama</label>
                                <input type='text' name='username' placeholder='username' class="form-control">
                            </div>
                            <div class="form-group">
                                <label>Email address</label>
                                <input type='email' name='email' placeholder='email address' class="form-control">
                            </div>
                            <div class="form-group">
                                <label>Departemen</label>
                                <select name="department" class="form-control">
                                    <option value="">Department</option>
                                    <?php foreach($departments as $department): ?>
                                        <option value=<?= $department->id; ?> ><?= $department->name ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <div class="form-group">
                                <label>Jabatan</label>
                                <select name="role" class="form-control">
                                    <option>PILIH JABATAN UNTUK USER</option>
                                    <option value=2>Supervisor/Manager</option>
                                    <option value=3>Staff</option>
                                    <option value=4>Viewer</option>
                                </select>
                            </div>

                            <!--FOTO -->
                            <div class="form-group">
                                <label>Photo</label>
                                <input type="file" name="photo" >
                            </div>

                            <!--TANDA TANGAN -->
                            <div class="form-group">
                                <label>Tanda tangan</label>
                                <input type="file" name="signature" >
                            </div>

                            <button type='submit' class="btn btn-primary"><span class="glyphicon glyphicon-send"></span> Kirim</button>
                        
                        </form>
                    </div>
                </div>
                
                                
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
