<?php 
    $titlePage="Register";
    require 'base/header.view.php';
?>

<div class="container-fluid">

    <?php
        
        $actionTo='register';

        if(count($contents)>0){
            $actionTo=$contents['firstUser']?'registerFirstUser':'register';
            echo $contents['message'];
        }
        
    ?>

    <h3>Register user</h3>
    <form action=<?= $actionTo; ?> method='POST'>
        <input type='text' name='username' placeholder='Username'><br>
        <input type='email' name='email' placeholder='Alamat email'><br>
        <input type='text' name='department' placeholder='Departemen/divisi'>
        <button type='submit'>Register</button>
    </form>
</div>

<?php
    require 'base/footer.view.php';
?>