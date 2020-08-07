<?php

include('config/config.php');

if ($_GET['action'] == 'new') {
    if ($manualdb->has("users", ["user" => $_POST['uname']])) {
        header("Location: ./login?action=login");
        die();
    } else if ($_POST['psw'] == $_POST['repsw']){
        if (createUserAccount($_POST['uname'], $_POST['psw'], time() + 86400)) {
            header("Location: ./login?action=login");
            die();
        }
        header("Location: .");
        die();
    } else {
        header("Location: .");
        die();
    }
}
if ($_GET['action'] == 'reset') {
    if (!$manualdb->has("users", ["user" => $_POST['uname']])) {
        header("Location: ./login?action=login");
        die();
    } else if ($_POST['uname']){
        $randomPwd = generateRandomString();
        if (resetUserPassword($_POST['uname'], $randomPwd, 0)) {
            header("Location: ./login?action=login");
            die();
        }
        header("Location: .");
        die();
    } else {
        header("Location: .");
        die();
    }
}
if ($_GET['action'] == 'update') {
    if (!empty($_POST['uname'])) {
        $info = $manualdb->query(
            "SELECT id, user, password, expire_timestamp, temp_password FROM users WHERE user = :user AND login_system = 'native'", [
                ":user" => $_POST['uname']
            ]
        )->fetch();
        if (password_verify($_POST['prepsw'], $info['password']) === true || password_verify($_POST['prepsw'], $info['temp_password']) === true) {
            if (!$manualdb->has("users", ["user" => $_POST['uname'], "login_system" => "native"])) {
                header("Location: ./login?action=login");
                die();
            } else if ($_POST['psw'] == $_POST['repsw']){
                $hashedPwd = password_hash($_POST['psw'], PASSWORD_DEFAULT);
                $manualdb->update("users", [
                    "password" => $hashedPwd,
                    "temp_password" => null
                ], [
                    "user" => $_POST['uname'],
                    "login_system" => 'native'
                ]);
                header("Location: .");
                die();
            } else {
                header("Location: .");
                die();
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="<?= $locale ?>">
<head>
    <meta charset="utf-8">
    <title><?= $title ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, minimum-scale=1, user-scalable=no, minimal-ui">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black">
    <meta name="apple-mobile-web-app-title" content="PokeMap">
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="theme-color" content="#3b3b3b">
    <!-- Fav- & Apple-Touch-Icons -->
    <!-- Favicon -->
    <link rel="shortcut icon" href="static/appicons/favicon.ico"
          type="image/x-icon">
    <!-- non-retina iPhone pre iOS 7 -->
    <link rel="apple-touch-icon" href="static/appicons/114x114.png"
          sizes="57x57">
    <!-- non-retina iPad pre iOS 7 -->
    <link rel="apple-touch-icon" href="static/appicons/144x144.png"
          sizes="72x72">
    <!-- non-retina iPad iOS 7 -->
    <link rel="apple-touch-icon" href="static/appicons/152x152.png"
          sizes="76x76">
    <!-- retina iPhone pre iOS 7 -->
    <link rel="apple-touch-icon" href="static/appicons/114x114.png"
          sizes="114x114">
    <!-- retina iPhone iOS 7 -->
    <link rel="apple-touch-icon" href="static/appicons/120x120.png"
          sizes="120x120">
    <!-- retina iPad pre iOS 7 -->
    <link rel="apple-touch-icon" href="static/appicons/144x144.png"
          sizes="144x144">
    <!-- retina iPad iOS 7 -->
    <link rel="apple-touch-icon" href="static/appicons/152x152.png"
          sizes="152x152">
    <!-- retina iPhone 6 iOS 7 -->
    <link rel="apple-touch-icon" href="static/appicons/180x180.png"
          sizes="180x180">
    <script>
        var token = '<?php echo (!empty($_SESSION['token'])) ? $_SESSION['token'] : ""; ?>';
    </script>
    <link rel="stylesheet" href="static/dist/css/app.min.css">
    <?php if (file_exists('static/css/custom.css')) {
        echo '<link rel="stylesheet" href="static/css/custom.css?' . time() . '">';
    } ?>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.12.0/jquery-ui.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.10.12/css/jquery.dataTables.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
    <script src="static/js/vendor/modernizr.custom.js"></script>
    <!-- Toastr -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
</head>
<body>
    <h2><? $title ?>Login</h2>
    <div id="login-force" class="force-modal">
        <?php
        if ($_GET['action'] == 'account') {
            $html = '<form class="force-modal-content animate" action="/register?action=new" method="post">
                <div class="imgcontainer">
                    <i class="fas fa-user" style="font-size:80px"></i>
                </div>
                <div class="force-container">
                    <label for="uname"><b>Email address</b></label>
                        <input type="email" placeholder="Enter email address" name="uname" required>
                    <label for="psw"><b>Password</b></label>
                        <input type="password" id="psw" name="psw" pattern="(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{8,}" title="Must contain at least one number and one uppercase and lowercase letter, and at least 8 or more characters" required>
                    <label for="repsw"><b>Retype Password</b></label>
                        <input type="password" id="repsw" name="repsw" placeholder="Enter Password" required>
                        <span id="validity"></span>
                    <button type="submit" class="force-button" disabled>Register</button>
                </div>
            </form>';
            echo $html;
        }
        if ($_GET['action'] == 'password-reset') {
            $html = '<form class="force-modal-content animate" action="/register?action=reset" method="post">
                <div class="imgcontainer">
                    <i class="fas fa-user" style="font-size:80px"></i>
                </div>
                <div class="force-container">
                    <label for="uname"><b>Email address</b></label>
                        <input type="email" placeholder="Enter Email address" name="uname" required>

                    <button type="submit" class="force-button">Reset password</button>
                </div>
            </form>';
            echo $html;
        }
        if ($_GET['action'] == 'password-update') {
            $html = '<form class="force-modal-content animate" action="/register?action=update" method="post">
                <div class="imgcontainer">
                    <i class="fas fa-user" style="font-size:80px"></i>
                </div>
                <div class="force-container">
                    <label for="uname"><b>Email address</b></label>
                        <input type="text" id="uname" name="uname" value="' . $_GET['username'] . '" readonly>
                    <label for="prepsw"><b>Password</b></label>
                        <input type="password" id="prepsw" name="prepsw" placeholder="Enter Password" required>
                    <label for="psw"><b>New Password</b></label>
                        <input type="password" id="psw" name="psw" pattern="(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{8,}" title="Must contain at least one number and one uppercase and lowercase letter, and at least 8 or more characters" required>
                    <label for="repsw"><b>Retype New Password</b></label>
                        <input type="password" id="repsw" name="repsw" placeholder="Enter Password" required>
                        <span id="validity"></span>
                    <button type="submit" class="force-button">Update password</button>
                </div>
            </form>';
            echo $html;
        } ?>
    </div>

    <script>
        $('#psw, #repsw').on('keyup', function () {
            if ($('#psw').val() == $('#repsw').val()) {
                $('#validity').html('Passwords match').css('color', 'green');
                $(':input[type="submit"]').prop('disabled', false);
            } else {
                $('#validity').html('Passwords do not match').css('color', 'red');
                $(':input[type="submit"]').prop('disabled', true);
            }
        });
    </script>
</body>
</html>
