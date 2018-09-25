<?php 
    $titlePage="Set password";
    require 'base/header.view.php';
?>
<main>
    <div class="container-fluid" style="width:40%">

        <!-- messages -->

        <?php include "app/resources/views/errors/errors.view.php" ?>

        <h3 class="text-center">Set password</h3>
        <form action='/confirmation' method='POST'>
            <input type="hidden" name="c" value=<?= $_GET['c']; ?> >
            <input type="hidden" name="u" value=<?= $_GET['u']; ?> >
            <div class="form-group">
                <input type='password' name='password' placeholder='password' class="form-control">
            </div>
            <div class="form-group">
                <input type='password' name='repassword' placeholder='Ketik ulang password' class="form-control">
            </div>
            <button type='submit' class="btn btn-md btn-primary"><span class="glyphicon glyphicon-send"></span> Kirim</button>
        </form>
    </div>
</main>
<?php
    require 'base/footer.view.php';
?>