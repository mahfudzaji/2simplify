<?php

$titlePage="Index";

require 'base/header.view.php';

?>
<main>
    <div class="container-fluid">
        <?php require "app/resources/views/errors/errors.view.php"; ?>
        
        <h3>Lupa password</h3>
        
        <form action='forget' method='POST'>
            <input type='email' name='email' placeholder='email'><br>
            <button type='submit'><span class="glyphicon glyphicon-send"></span> Kirim</button>
        </form>
    </div>
</main>
<?php

require 'base/footer.view.php'

?>