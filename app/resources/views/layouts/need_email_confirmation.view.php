<?php

$titlePage="Index";

require 'base/header.view.php';

?>
    <div class="container-fluid">
        <?php require "app/resources/views/errors/errors.view.php"; ?>

        <h3> Lupa password </h3>
        
        <form action='forget' method='POST'>
            <input type='email' name='email' placeholder='email' required><br>
            <button type='submit'><span class="glyphicon glyphicon-send"></span> Kirim</button>
        </form>
    </div>
<?php

require 'base/footer.view.php'

?>