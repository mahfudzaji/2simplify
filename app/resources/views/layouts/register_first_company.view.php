<?php 
    $titlePage="Register your company";
    require 'base/header.view.php';
?>

<div class="container-fluid">

    <?php require "app/resources/views/errors/errors.view.php"; ?>

    <div class="row">
        <div class="col-sm-4"></div>
        <div class="col-sm-4">
            <h3 class="text-center">Register Your Company</h3>
            <form action="partner/create" method="POST" enctype="multipart/form-data">
                <input type="hidden" name="relationship" value=1>
                <div class="form-group">
                    <label>Nama</label>
                    <input type="text" name="name" class="form-control" placeholder="Nama" autofocus required>
                </div>
                <div class="form-group">
                    <label>Kode untuk dokumen</label>
                    <input type="text" name="code" class="form-control" placeholder="Kode untuk dokumen" required>
                </div>
                <div class="form-group">
                    <label>Badan usaha</label>
                    <select name="bussiness_entity" class="form-control">
                        <option value=''>BADAN USAHA</option>
                        <option value='1'>PT</option>
                        <option value='2'>CV</option>
                        <option value='3'>Individu</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Propinsi</label>
                    <select name="province" class="form-control" required>
                    <option>PROPINSI</option>
                    <?php foreach($provinces as $province): ?>
                        <option value=<?= $province->id; ?> ><?= ucwords($province->province); ?> </option>
                    <?php endforeach; ?>               
                    </select>
                </div>
                <div class="form-group">
                    <label>Alamat lengkap</label>
                    <input type="text" name="address" class="form-control" placeholder="Alamat" required>
                </div>
                <div class="form-group">
                    <label>Nomor telepon</label>
                    <input type="text" name="phone" class="form-control" placeholder="Nomor telepon">
                </div>
                <div class="form-group">
                    <label>Email</label>
                    <input type="email" name="email" class="form-control" placeholder="Email" required>
                </div>  
                <div class="form-group">
                    <label>Keterangan tambahan</label>
                    <textarea name="remark" class="form-control" placeholder="Keterangan tambahan"></textarea>
                </div>
                <div class="form-group">
                    <label>Logo</label>
                    <input type="file" name="logo">
                </div>
                <button type="submit" name="submit" class="btn btn-primary" style="float:right;"><span class="glyphicon glyphicon-send"></span> Kirim</button>
            </form>
        </div>
        <div class="col-sm-4"></div>
    </div>
</div>

<?php
    require 'base/footer.view.php';
?>