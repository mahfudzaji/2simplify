<?php 
    $titlePage="Register";
    require 'base/header.view.php';
?>

<div class="container-fluid text-center">

    <?php require "app/resources/views/errors/errors.view.php"; ?>

    <?php
        
        $actionTo='register';

        if(count($contents)>0){
            $actionTo=$contents['firstUser']?'registerFirstUser':'register';
            echo $contents['message'];
        }
        
    ?>
    <div class="row">
        <div class="col-sm-4"></div>
        <div class="col-sm-4">
            <h3>Register</h3>
            <form action=<?= $actionTo; ?> method='POST'>
                <div class="form-group">
                    <input type='text' class="form-control" name='username' placeholder='Username'>
                </div>
                <div class="form-group">
                    <input type='email' class="form-control" name='email' placeholder='Alamat email'>
                </div>
                <div class="form-group">
                    <select name="department" class="form-control">
                        <option value="">Department</option>
                        <?php foreach($contents['departments'] as $department): ?>
                            <option value=<?= $department->id; ?> ><?= ucfirst($department->name); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <button class="btn btn-primary" type='submit'>Register</button>
            </form>
        </div>
        <div class="col-sm-4"></div>
    </div>
</div>

<?php
    require 'base/footer.view.php';
?>