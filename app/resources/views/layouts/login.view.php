<?php
    $titlePage="Login";
    require 'base/header.view.php';
?>

    <div class="container-fluid">
        <h1>login page</h1>
        <div class='welcome'>
            <h2>Selamat datang di Simplify</h2>
            <p>aplikasi ini didesain untuk memudahkan operasional admin</p>
        </div>
        <div class='login'>
            <button type='button' class='btn btn-default' id='btn-login'>Login</button>
        </div>
        <div class='form' id='form-login' style='display:none;'>
            <form action='/login' method='POST'>
                <div>
                    <label>Email</label>
                    <input type='text' name='email' placeholder='email'>
                </div>
                <div>
                    <label>Password</label>
                    <input type='password' name='password' placeholder='Password'> 
                </div>
                <button type='submit' name='login' class='btn btn-primary'>Login</button>
            </form>
        </div>
    </div>

    <script type="text/javascript">
        $(document).ready(function(){
            $('#btn-login').on('click',function(){
                $('#form-login').toggle('fast');
            });
        });
    </script>

<?php

    require 'base/footer.view.php'

?>