<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cater Web</title>
    <link href="<?= base_url(); ?>/assets/css/login.css" rel="stylesheet" />
</head>

<body>
    <div class="container">
        <div class="screen">
            <div class="screen__content">
                <form action="<?= base_url() ?>auth/login" class="login" method="post">
                    <div class="login__field">
                        <i class="login__icon fas fa-user"></i>
                        <input type="text" class="login__input" placeholder="User Name" name="username" autofocus
                            required="true">
                    </div>
                    <div class="login__field">
                        <i class="login__icon fas fa-lock"></i>
                        <input type="password" class="login__input" placeholder="Password" name="password"
                            required="true">
                    </div>
                    <button class="button login__submit">
                        <span class="button__text">Log In Now</span>
                        <i class="button__icon fas fa-chevron-right"></i>
                    </button>
                </form>
                <div class="social-login">
                    <h3>Dashboard Pembaca Meter</br>PERUMDAM Tirta Satria</h3>

                </div>
            </div>
            <div class="screen__background">
                <span class="screen__background__shape screen__background__shape4"></span>
                <span class="screen__background__shape screen__background__shape3"></span>
                <span class="screen__background__shape screen__background__shape2"></span>
                <span class="screen__background__shape screen__background__shape1"></span>
            </div>
        </div>
    </div>
    <script>
        <?php if (session()->getFlashdata("message")): ?>
            alert("<?= session()->getFlashdata("message") ?>");
        <?php endif; ?>
    </script>
</body>

</html>