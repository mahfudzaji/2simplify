<?php

$titlePage="Index";

require 'base/header.view.php';

?>
<main>

    <div class="container-fluid">
        <?php require "app/resources/views/errors/errors.view.php"; ?>
        
        <div class="main-data" style="width:50%; margin:auto;">
            <h3>Lupa password</h3>
            <hr>

            <form action="/reset" method="POST">
                <div class="form-group">
                    <label>Email</label>
                    <input type="email" name="email" class="form-control" placeholder="email" required>
                </div>
                <div class="form-group">
                    <label>Password lama</label>
                    <input type="password" name="password" class="form-control" placeholder="Masukkan password" required>
                </div>
                <div class="form-group">
                    <label>Password baru</label>
                    <input type="password" name="new_password" class="form-control" placeholder="Masukkan password baru" required>
                </div>
                <div class="form-group">
                    <label>Konfirmasi password baru</label>
                    <input type="password" name="re_password" class="form-control" placeholder="Konfirmasi password baru" required>
                </div>
                <button type='submit' class="btn btn-md btn-primary"><span class="glyphicon glyphicon-send"></span> Kirim</button>
            </form>

        </div>
    
    </div>

</main>

<?php

require 'base/footer.view.php'

?>