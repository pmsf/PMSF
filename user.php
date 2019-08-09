<?php

include('config/config.php');
?>
<!DOCTYPE html>
<html lang="<?= $locale ?>">
<head>
    <meta charset="utf-8">
    <title><?= $title ?></title>
    <meta name="viewport"
          content="width=device-width, initial-scale=1, maximum-scale=1, minimum-scale=1, user-scalable=no, minimal-ui">
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
    <?php if ( file_exists( 'static/css/custom.css' ) ) {
        echo '<link rel="stylesheet" href="static/css/custom.css">';
    } ?>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.12.0/jquery-ui.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.10.12/css/jquery.dataTables.css">
    <script src="static/js/vendor/modernizr.custom.js"></script>
    <!-- Toastr -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
</head>
<body id="top" style="overflow: auto;">
<div class="wrapper">
    <?php
    if ($noNativeLogin === false || $noDiscordLogin === false) {
        if (isset($_COOKIE["LoginCookie"])) {
            validateCookie($_COOKIE["LoginCookie"]);
        }
        if ($noNativeLogin === true && $noDiscordLogin === false && empty($_SESSION['user']->id)) {
            header("Location: ./discord-login");
        } else if ($noNativeLogin === true && $noDiscordLogin === false && !empty($_SESSION['user']->id)) {
            header("Location: .");
        }

        if (isset($_POST['submitUpdatePwdBtn'])) {
            if (!empty($_POST["password"]) && ($_POST["password"] === $_POST["repassword"])) {
                $passwordErr = '';
                $password = $_POST["password"];
                
                if (strlen($_POST["password"]) <= '5') {
                    $passwordErr = i8ln('Your password must contain at least 6 characters!');
                } elseif (!preg_match("#[0-9]#", $password)) {
                    $passwordErr = i8ln('Your password must contain at least 1 number!');
                } elseif (!preg_match("#[A-Z]#", $password)) {
                    $passwordErr = i8ln('Your password must contain at least 1 capital letter!');
                } elseif (!preg_match("#[a-z]#", $password)) {
                    $passwordErr = i8ln('Your password must contain at least 1 lowercase letter!');
                }
            } else {
                $passwordErr = i8ln('Your passwords didn\'t match!');
            }
            
            if (empty($passwordErr)) {
                resetUserPassword($_SESSION['user']->user, $_POST['password'], 2);
                unset($_SESSION['user']->updatePwd);
                
                header("Location: .");
                die();
            }
        }
        if (isset($_POST['submitLoginBtn'])) {
            $info = $manualdb->query(
                "SELECT id, user, password, expire_timestamp, temp_password FROM users WHERE user = :user AND login_system = 'native'", [
                    ":user" => $_POST['email']
                ]
            )->fetch();

            if (password_verify($_POST['password'], $info['password']) === true || password_verify($_POST['password'], $info['temp_password']) === true) {
                setcookie("LoginCookie", session_id(), time()+60*60*24*7);

                $manualdb->update("users", [
                    "Session_ID" => session_id()
                ], [
                    "user" => $_POST['email'],
                    "login_system" => 'native'
                ]);

                if (password_verify($_POST['password'], $info['password']) === true) {
                    if (!empty($info['temp_password'])) {
                        $manualdb->update("users", [
                            "temp_password" => null
                        ], [
                            "user" => $_POST['email'],
                            "login_system" => 'native'
                        ]);
                    }
                }
                header("Location: .?login=true");
                die();
            }
        }
        if (isset($_POST['submitCreateUserOrResetPasswordBtn'])) {
            $count = $manualdb->count("users", [
                "user" => $_POST['email'],
                "login_system" => 'native'
            ]);
            
            $randomPwd = generateRandomString();
            $message = "";
            if ($count === 1) {
                resetUserPassword($_POST['email'], $randomPwd, 0);
                $subject = "[{$title}] - Password Reset";
                
                $message .= i8ln('Dear') . " {$_POST['email']},<br><br>";
                $message .= i8ln('Your password has been reset') . "<br>";
                $message .= i8ln('If you haven\'t requested a new password you can ignore this email.') . "<br>";
                $message .= i8ln('Your old password is still working.') . "<br><br>";
                $message .= i8ln('New password: ') . " {$randomPwd}<br><br>";
            } else {
                createUserAccount($_POST['email'], $randomPwd, time());
                $subject = "[{$title}] - Welcome";
                
                $message .= i8ln('Dear') . " {$_POST['email']},<br><br>";
                $message .= i8ln('Thank you for signing up.') . "<br>";
                $message .= i8ln('Your temporary password is ') . " {$randomPwd}<br><br>";
            }
            
            if ($sellyPage) {
                $message .= i8ln('You can purchase membership on ') . "<a href='{$sellyPage}'>selly</a>.<br><br>";
            }
            if ($discordUrl) {
                $message .= i8ln('For support, ask your questions in the ') . "<a href='{$discordUrl}'>" . i8ln('discord guild') . "</a>!<br><br>";
            }
            $message .= i8ln('Best Regards') . "<br>" . i8ln('Admin');
            if ($title) {
                $message .= " @ {$title}";
            }
                
            !empty($domainName) ? $domainName = $domainName : $domainName = $_SERVER['SERVER_NAME'];
            $headers = "From: no-reply@{$domainName}" . "\r\n" .
                "Reply-To: no-reply@{$domainName}" . "\r\n" .
                'Content-Type: text/html; charset=utf-8' . "\r\n" .
                'X-Mailer: PHP/' . phpversion();

            $sendMail = mail($_POST['email'], $subject, $message, $headers);

            if (!$sendMail) {
                http_response_code(500);
                die("<h1>Warning</h1><p>The email has not been sent.<br>If you're an user please contact your administrator.<br>If you're an administrator install <i><b>apt-get install sendmail</b></i> and restart your web server and try again.</p><p><a href='.'><i class='fas fa-backward'></i> Back to Map</a> - <a href='./user?forgotPwd'>Retry</a></p>");
            }
            
            header("Location: ?sentPwd");
            die();
        }
        if (isset($_POST['submitUpdateUserBtn'])) {
            $Err = '';
            if ($_POST['email'] !== '-1' || !empty($_POST['createUserEmail'])) {
                if ((isset($_POST['ResetPwd']) || $_POST['radioExpireDate'] > 0) && $_POST['email'] !== '-1') {
                    if (strpos($_POST['email'], '#')) {
                        $login_system = 'discord';
                    } else {
                        $login_system = 'native';
                    }

                    $info = $manualdb->query(
                        "SELECT user, expire_timestamp FROM users WHERE user = :user AND login_system = :login_system", [
                            ":user" => $_POST['email'],
                            ":login_system" => $login_system
                        ]
                    )->fetch();

                    if (isset($_POST['ResetPwd']) && $login_system === 'native') {
                        $resetUserPwd = generateRandomString();
                        resetUserPassword($_POST['email'], $resetUserPwd, 1);
                    }

                    if ($_POST['radioExpireDate'] > 0) {
                        if ($_POST['radioExpireDate'] >= 1 && $_POST['radioExpireDate'] <= 12) {
                            if ($info['expire_timestamp'] > time()) {
                                $newExpireTimestamp = $info['expire_timestamp'] + 60 * 60 * 24 * $daysMembershipPerQuantity * $_POST['radioExpireDate'];
                            } else {
                                $newExpireTimestamp = time() + 60 * 60 * 24 * $daysMembershipPerQuantity;
                            }
                        } else {
                            $newExpireTimestamp = strtotime($_POST['customDate']);
                        }

                        updateExpireTimestamp($_POST['email'], $login_system, $newExpireTimestamp);
                    }
                } else {
                    $Err = i8ln('No changes made.');
                }

                if (!empty($_POST['createUserEmail'])) {
                    $createUserPwd = generateRandomString();
                    if (createUserAccount($_POST['createUserEmail'], $createUserPwd, time()) === false) {
                        $Err = i8ln('Email already in use.');
                    }
                }

                if ((!isset($_POST['ResetPwd']) && $_POST['radioExpireDate'] == 0 && $_POST['email'] === '-1') && empty($_POST['createUserEmail'])) {
                    $Err = i8ln('No changes made.');
                }
            } else {
                $Err = i8ln('No changes made.');
            }
        }
        if (isset($_POST['submitKey'])) {
            $Err = '';
            $info = $manualdb->query(
                "SELECT selly_id, activated, quantity FROM payments WHERE selly_id = :selly_id", [
                    ":selly_id" => $_POST['key']
                ]
            )->fetch();

            if (empty($info['selly_id'])) {
                $Err = i8ln('Invalid key.');
            } elseif ($info['activated'] === 1) {
                $Err = i8ln('This key has already been activated.');
            }
        }

        if (isset($_GET['account'])) {
            ?>
            <p><h2><?php echo "[<a href='.'>{$title}</a>] - " . i8ln('Create User / Reset Password'); ?></h2></p>
            <form action='' method='POST'>
                <table style='margin: 0;'>
                    <tr>
                        <th><?php echo i8ln('E-mail'); ?></th><td><input type="text" name="email" required></td>
                    </tr>
                </table>
                <table><tr><td><input class="button" id="margin" type="submit" name="submitCreateUserOrResetPasswordBtn" value="<?php echo i8ln('Submit'); ?>"><a class='button' href='/user'><i class='fas fa-backward'></i> <?php echo i8ln('Back'); ?></a></td></tr></table>
            </form>
        <?php
        } elseif (!empty($_SESSION['user']->updatePwd)) {
            ?>
            <h2><?php echo "[<a href='.'>{$title}</a>] - " . i8ln('Change your password.'); ?></h2>
            <form action='' method='POST'>
                <table style='margin: 0;'>
                    <tr>
                        <th><?php echo i8ln('New password'); ?></th><td><input type="password" name="password" required></td>
                    </tr>
                    <tr>
                        <th><?php echo i8ln('Confirm password'); ?></th><td><input type="password" name="repassword" required></td>
                    </tr>
                    <?php
                    if (!empty($passwordErr)) {
                        ?>
                    <tr>
                        <th><?php echo i8ln('Message'); ?></th>
                        <td><input type="text" name="errMess" value="<?php echo $passwordErr; ?>" id="redBox" disabled></td>
                    </tr>
                    <?php
                    } ?>
                </table>
                <table><tr><td><input class="button" id="margin" type="submit" name="submitUpdatePwdBtn" value="<?php echo i8ln('Submit'); ?>"><a class='button' href='./logout.php'><?php echo i8ln('Logout'); ?></a></td></tr></table>
            </form>
        <?php
        } elseif (in_array(isset($_SESSION['user']->user) ? $_SESSION['user']->user : null, $adminUsers)) {
            ?>
            <h2><?php echo "[<a href='.'>{$title}</a>] - " . i8ln('Admin page'); ?></h2>
            <?php
            if (!file_exists($logfile)) {
                if (file_put_contents($logfile, "-- " . i8ln('This is a test to make sure logging is okay.') . " " . date('Y-m-d H:i:s') ."\r\n", FILE_APPEND) == false) {
                    echo "<h1>" . i8ln('Warning') . "</h1>" .
                        "<p>" . i8ln('Your backup logging doesn\'t work. In case of database corruption all data may be lost.') .
                        "<br>" . i8ln('To solve this, type') .
                        ":<br><i><b>sudo chgrp " . exec('whoami') . " " . dirname(__DIR__) . "<br>sudo chmod g+w " . dirname(__DIR__) . "</b></i></p>";
                }
            } ?>
            <form action='' method='POST'>
                <table style='margin: 0;'>
                    <tr>
                        <th><?php echo i8ln('Select user'); ?></th>
                        <td>
                            <select name="email" class='select' required>
                                <option value='-1'><?php echo i8ln('Select a user...'); ?></option>
                                <?php
                                $users = $manualdb->select("users", [
                                    "user"
                                ], [
                                    "ORDER" => [
                                        "user" => "ASC"
                                    ]
                                ]);

            if ($users) {
                foreach ($users as $user) {
                    echo "<option>{$user['user']}</option>";
                }
            } ?>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <th><?php echo i8ln('Expire Date'); ?></th>
                        <td>
                            <label><input onclick="document.getElementById('customDate').disabled = true;" type="radio" name="radioExpireDate" value="0" checked="checked"><?php echo i8ln('No change'); ?></label>
                            <label><input onclick="document.getElementById('customDate').disabled = true;" type="radio" name="radioExpireDate" value="1"><?php echo i8ln('1 Month'); ?></label>
                            <label><input onclick="document.getElementById('customDate').disabled = true;" type="radio" name="radioExpireDate" value="3"><?php echo i8ln('3 Months'); ?></label>
                            <label><input onclick="document.getElementById('customDate').disabled = true;" type="radio" name="radioExpireDate" value="6"><?php echo i8ln('6 Months'); ?></label>
                            <label><input onclick="document.getElementById('customDate').disabled = true;" type="radio" name="radioExpireDate" value="12"><?php echo i8ln('12 Months'); ?></label>
                            <label><input onclick="document.getElementById('customDate').disabled = false;" type="radio" name="radioExpireDate" value="100"><?php echo i8ln('Custom'); ?></label>
                            <input class="date" type="date" name="customDate" id="customDate" value="<?php echo date('Y-m-d', time()); ?>" disabled="disabled">
                        </td>
                    </tr>
                    <tr>
                        <th><?php echo i8ln('Reset Password'); ?></th><td><input type="checkbox" name="ResetPwd"></td>
                    </tr>
                    <tr>
                        <th><?php echo i8ln('Create User'); ?></th><td><input type="text" name="createUserEmail" placeholder='<?php echo i8ln('E-mail'); ?>'></td>
                    </tr>
                </table>
                <table><tr><td><input class="button" id="margin" type="submit" name="submitUpdateUserBtn" value="<?php echo i8ln('Submit'); ?>"></td></tr></table>
            </form>
            
            <?php
            if (isset($_POST['submitUpdateUserBtn'])) {
                ?>
                <h2><?php echo i8ln('CHANGES'); ?></h2>
                <table style='margin: 0;'>
                    <?php
                    if (empty($Err)) {
                        if (isset($_POST['submitUpdateUserBtn']) && $_POST['radioExpireDate'] > 0 && $_POST['email'] !== '-1') {
                            ?>
                        <tr>
                            <th id="one-third"><?php echo $_POST['email'] . " - " . i8ln('Expire Date'); ?></th>
                            <td><input type="text" name="infoMess" value="<?php echo date('Y-m-d', $newExpireTimestamp); ?>" id="greenBox" disabled></td>
                        </tr>
                        <?php
                        }
                        if (isset($_POST['submitUpdateUserBtn']) && isset($_POST['ResetPwd']) && $login_system === 'native' && $_POST['email'] !== '-1') {
                            ?>
                        <tr>
                            <th id="one-third"><?php echo $_POST['email'] . " - " . i8ln('Password'); ?></th>
                            <td><input type="text" name="infoMess2" value="<?php echo $resetUserPwd; ?>" id="greenBox"></td>
                        </tr>
                        <?php
                        }
                        if (isset($_POST['submitUpdateUserBtn']) && !empty($_POST['createUserEmail'])) {
                            ?>
                            <tr>
                                <th id="one-third"><?php echo $_POST['createUserEmail'] . " - " . i8ln('Created Account'); ?></th>
                                <td><input type="text" name="infoMess2" value="<?php echo $createUserPwd; ?>" id="greenBox"></td>
                            </tr>
                        <?php
                        }
                    } else {
                        ?>
                        <tr>
                            <th id="one-third"><?php echo i8ln('Message'); ?></th>
                            <td><input type="text" name="infoMess" value="<?php echo i8ln($Err); ?>" id="redBox" disabled></td>
                        </tr>
                        <?php
                    } ?>
                </table>
            <?php
            }
        } elseif (!empty($_SESSION['user']->user)) {
            ?>
            <h2><?php echo "[<a href='.'>{$title}</a>] - " . i8ln('Activate key'); ?></h2>
            <?php
                if (isset($_POST['submitKey']) && empty($Err)) {
                    if ($_SESSION['user']->expire_timestamp > time()) {
                        $newExpireTimestamp = $_SESSION['user']->expire_timestamp + 60 * 60 * 24 * $daysMembershipPerQuantity * $info['quantity'];
                    } else {
                        $newExpireTimestamp = time() + 60 * 60 * 24 * $daysMembershipPerQuantity * $info['quantity'];
                    }

                    $_SESSION['user']->expire_timestamp = $newExpireTimestamp;

                    $manualdb->update("payments", [
                        "activated" => 1
                    ], [
                        "selly_id" => $info['selly_id']
                    ]);

                    $manualdb->update("users", [
                        "access_level" => 1
                    ], [
                        "user" => $_SESSION['user']->user
                    ]);

                    updateExpireTimestamp($_SESSION['user']->user, $_SESSION['user']->login_system, $newExpireTimestamp);
                    $time = date("Y-m-d H:i", $newExpireTimestamp);

                    echo "<h3><span style='color: green;'>" . i8ln('Your key has been activated!') . "<br>" . i8ln('Your account expires on: ') . $time . "</span></h3>";
                } elseif (isset($_POST['submitKey']) && !empty($Err)) {
                    echo "<h3><span style='color: red;'>{$Err}</span></h3>";
                } ?>
            <form action='' method='POST'>
                <table style='margin: 0;'>
                    <tr>
                        <th><?php echo i8ln('Selly Order ID'); ?></th><td><input type="text" name="key" required placeholder="123a4b5c-de67-8901-f234-5g6789801h23"></td>
                    </tr>
                    <tr>
                        
                    </tr>
                </table>
                <table><tr><td><input class="button" id="margin" type="submit" name="submitKey" value="<?php echo i8ln('Submit'); ?>"><?php if ($sellyPage) {
                    echo "<a class='button' target='_TAB' id='margin' href='{$sellyPage}'>" . i8ln('Extend Membership') . "</a>";
                } ?><a class='button' id="margin" href='.'><i class='fas fa-backward'></i> <?php echo i8ln('Back to map'); ?></a></td></tr></table>
            </form>
        <?php
        } else {
            ?>
            <p><h2><?php echo "[<a href='.'>{$title}</a>] - " . i8ln('Login'); ?></h2></p>
            <form action='' method='POST'>
                <table style='margin: 0;'>
                    <tr>
                        <th><?php echo i8ln('E-mail'); ?></th><td><input type="text" name="email" required <?php if (isset($_POST['submitLoginBtn'])) {
                echo "value='$_POST[email]'";
            } ?> placeholder="<?php echo i8ln('E-mail'); ?>"></td>
                    </tr>
                    <tr>
                        <th><?php echo i8ln('Password'); ?></th><td><input type="password" name="password" required placeholder="<?php echo i8ln('Password'); ?>"></td>
                    </tr>
                    <?php
                    if (isset($_POST['submitLoginBtn']) && password_verify($_POST['password'], $info['password']) !== 1) {
                        ?>
                        <tr>
                            <th><?php echo i8ln('Message'); ?></th>
                            <td><input type="text" name="infoMess" value="<?php echo i8ln('Wrong credentials'); ?>" id="redBox" disabled></td>
                        </tr>
                    <?php
                    } elseif (isset($_GET['sentPwd'])) {
                        ?>
                        <tr>
                            <th><?php echo i8ln('Message'); ?></th>
                            <td><input type="text" name="infoMess" value="<?php echo i8ln('An email has been sent to the specified email.'); ?>" id="greenBox" disabled></td>
                        </tr>
                    <?php
                    } ?>
                </table>
                <table><tr><td><input class="button" id="margin" type="submit" name="submitLoginBtn" value="<?php echo i8ln('Submit'); ?>"><a class='button' id="margin" href='?account'><i class='fas fa-user'></i> <?php echo i8ln('Create User / Reset Password'); ?></a><?php if ($noDiscordLogin === false) {
                        echo "<a class='button' id='margin' href='./discord-login'><i class='fab fa-discord'></i> " . i8ln('Login with Discord') . "</a>";
                    } ?> <a class='button' id='margin' href='.'><i class='fas fa-backward'></i>  <?php echo i8ln('Back to Map'); ?></a></td></tr></table>
            </form>
       <?php
        }
    } else {
        header("Location: .");
    }
    ?>
</div>
</body>
</html>
