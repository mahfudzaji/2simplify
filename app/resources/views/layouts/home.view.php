<?php

$titlePage="Beranda";

require 'base/header.view.php';

/*
-list and register user
-todays activities & list
-announcements & events
*/


?>
<main>
    <div class="container-fluid">

        <!-- messages -->

        <?php include "app/resources/views/errors/errors.view.php" ?>
        
        <div class='welcome text-center'>
            <h2>Hello, Selamat datang di <span class="label label-success">Simplify</span>, <?= ucfirst($_SESSION['sim-name']); ?></h2>
            <h4 style="margin-top:20px;">Aplikasi yang didesain untuk memudahkan operasional perkantoran</h4>
        </div>

        <!-- <div style="margin-top:20px;display: flex;align-content: center;flex-wrap: wrap;flex-direction: row; justify-content: space-around;">
            <div>
                <p>FORM</p>
            </div>
            <div>PARTNER</div>
            <div>PRODUCT</div>
            <div>STOCK</div>
            <div>PROJECT</div>
        </div> -->

    </div>

</main>

<script>
function toggleStatusOfUser(e){
    var email=e.getAttribute('data-email');

    var c = confirm("Anda yakin untuk mengubah status user ini?");

    if(c){
        $.post('toggleUserStatus', {email:email}, function(data, status){
            //alert(data);
            location.reload();
        
        });
    } 
}

$(document).ready(function(){

});
</script>


<?php

require 'base/footer.view.php'

?>