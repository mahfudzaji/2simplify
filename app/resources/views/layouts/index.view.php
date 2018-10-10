<?php

$titlePage="Index";

require 'base/header.view.php';

?>
<main>
    <div class="container-fluid text-center">
        
        <?php require "app/resources/views/errors/errors.view.php"; ?>

        <div class='welcome'>
            <h2>Hello, Selamat datang di <span class="label label-success">Simplify</span></h2>
            <h4 style="margin-top:20px;">Aplikasi yang didesain untuk memudahkan operasional perkantoran</h4>
        </div>

        <button type='button' class='btn btn-default btn-modal' id='btn-login'>Mulai disini</button>

        <div style="max-width: 50%; margin:20px auto 5px auto;">
            <img style="border-radius:5px;width:100%" src="/public/upload/pexels-Unsplash2.jpg" class="img-responsive">
        </div>

    </div>

    <?php if(!isset($_SESSION['sim-isLogin'])): ?>
        <div class="app-form modal" id="modal-btn-login">         
            <div class="modal-content" style="width:35%;">
                <div class="modal-header">
                    <h3>LOGIN</h3>
                </div>

                <form action='/login' method='POST'>
                    <div class="form-group">
                        <label>Email</label>
                        <input type='email' class="form-control" name='email' placeholder='Email' autofocus required>
                    </div>
                    <div class="form-group">
                        <label>Password</label>
                        <input type='password' class="form-control" name='password' placeholder='Password' required> 
                    </div>
                    <div class="form-group">
                        <div class="g-recaptcha" data-sitekey="<?= $recaptcha['siteKey']; ?>"></div>
                        <script type="text/javascript"
                                src="https://www.google.com/recaptcha/api.js?hl=<?= $recaptcha['lang']; ?>">
                        </script>
                    </div>
                    <button type='submit' name='login' class='btn btn-primary'>Login</button>
                    <button class="btn btn-link btn-modal" id="btn-forget" style="float:right">Lupa password?</button>
                </form>
                
                <button type="button" class="btn btn-danger btn-close" style="position: absolute;top: 0;right: 0;transform: translate(100%,-100%);"><span class="glyphicon glyphicon-remove"></span> </button>
            </div>
        </div>

        <div class="app-form modal" id="modal-btn-forget">         
            <div class="modal-content" style="width:35%;">
                <div class="modal-header">
                    <h3>LUPA PASSWORD</h3>
                </div>

                <div class="description">
                    <p>Email konfirmasi akan dikirimkan ke email.</p>
                </div>

                <form action='forget' method='POST'>
                    <div class="form-group">
                        <label>Email</label>
                        <input type='email' class="form-control" name='email' placeholder='Email' autofocus required>
                    </div>
                    <button type='submit' class='btn btn-primary'>Reset</button>
                </form>
                
                <button type="button" class="btn btn-danger btn-close" style="position: absolute;top: 0;right: 0;transform: translate(100%,-100%);"><span class="glyphicon glyphicon-remove"></span> </button>
            </div>
        </div>

    <?php endif; ?>

</main>

<?php

require 'base/footer.view.php'

?>