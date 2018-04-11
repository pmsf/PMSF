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
    <link rel="stylesheet" href="static/css/login.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.12.0/jquery-ui.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.10.12/css/jquery.dataTables.css">
    <script src="static/js/vendor/modernizr.custom.js"></script>
    <!-- Toastr -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
</head>
<body id="top" style="overflow: auto;">
<div class="wrapper">
<?php
if ($noLogin === false) {

    if (isset($_COOKIE["LoginCookie"])) {
		if (validateCookie($_COOKIE["LoginCookie"]) === true) {
			if (!in_array($_SESSION['user']->email, $adminEmail)) {
				header("Location: .");
			}
		} else {
			header("Location: ./login.php");
		}
    }
    if (isset($_POST['submit_updatePwd'])) {
        if (!empty($_POST["password"]) && ($_POST["password"] == $_POST["repassword"])) {
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

            resetUserPassword($_SESSION['user']->email, $_POST['password'], 2);
            unset($_SESSION['user']->updatePwd);
            
            header("Location: .");
            die();
        }
    }
    if (isset($_POST['submit_login'])) {
        $info = $db->query(
            "SELECT email, password, expire_timestamp, temp_password FROM users WHERE email = :email", [
                ":email" => $_POST['email']
            ]
        )->fetch();

        if (password_verify($_POST['password'], $info['password']) == 1 || password_verify($_POST['password'], $info['temp_password']) == 1) {
            
            $_SESSION['user']->email = $info['email'];
            $_SESSION['user']->expire_timestamp = $info['expire_timestamp'];

            setcookie("LoginCookie",session_id(),time()+60*60*24*7);

            $db->update("users", [
                "Session_ID" => session_id()
            ], [
                "email" => $_POST['email']
            ]);

            if (password_verify($_POST['password'], $info['password']) == 1) {
            
                if (!empty($info['temp_password'])) {
                    $db->update("users", [
                        "temp_password" => null
                    ], [
                        "email" => $_POST['email']
                    ]);
                }

                if (in_array($info['email'], $adminEmail)){
                    header("Location: /login.php");
                } else {
                    header("Location: .");
                }
                die();

            } else {
                
                $_SESSION['user']->updatePwd = 1;
                
                header("Location: /login.php");
                die();

            }
        }
    }
    if (isset($_POST['submit_forgotPwd'])) {

        $count = $db->count("users",[
            "email" => $_POST['email']
        ]);
        
        if ($count == 1 || (in_array($_POST['email'], $adminEmail))) {

            $randomPwd = generateRandomString();
            
            if ($count == 1) {
                resetUserPassword($_POST['email'], $randomPwd, 0);
            } else {
                $expire_timestamp = time() + 60 * 60 * 24 * 365 * 10;
                createUserAccount($_POST['email'], $randomPwd, $expire_timestamp);
            }
            
            $message = "";
            $message .= i8ln('Dear') . " {$_POST['email']},<br><br>";
            $message .= i8ln('Your password has been reset') . "<br>";
            $message .= i8ln('If you haven\'t requested a new password you can ignore this email.') . "<br>";
            $message .= i8ln('Your old password is still working.') . "<br><br>";
            $message .= i8ln('New password:') . " {$randomPwd}<br><br>";
            
            if ($discordUrl) {
                $message .= i8ln('For support, ask your questions in the ') . "<a href='{$discordUrl}'>discord guild</a>!<br><br>";
            }
            $message .= i8ln('Best Regards') . "<br>Admin";
            if ($title) {
                $message .= " @ {$title}";
            }
            
            $subject = "[{$title}] - Password Reset";
            $headers = "From: no-reply@{$_SERVER['SERVER_NAME']}" . "\r\n" .
                "Reply-To: no-reply@{$_SERVER['SERVER_NAME']}" . "\r\n" .
                'Content-Type: text/html; charset=ISO-8859-1' . "\r\n" .
                'X-Mailer: PHP/' . phpversion();

            $sendMail = mail($_POST['email'], $subject, $message, $headers);

            if (!$sendMail) {
                http_response_code(500);
                die("<h1>Warning</h1><p>The email has not been sent.<br>If you're an user please contact your administrator.<br>If you're an administrator install <i><b>apt-get install sendmail</b></i> and restart your web server and try again.</p><p><a href='.'>Back to Map</a> - <a href='./login.php?forgotPwd'>Retry</a></p>");
            }
        }
        
        header("Location: ?sentPwd");
        die();
    }
    if (isset($_POST['submit_updateUser'])) {
        $Err = '';
        if ($_POST['email'] != i8ln('Select a user...') || !empty($_POST['createUserEmail'])) {
            if (($_POST['ResetPwd'] == "on" || $_POST['checkboxDate'] != 0) && $_POST['email'] != i8ln('Select a user...')) {
                $info = $db->query(
                    "SELECT email, expire_timestamp FROM users WHERE email = :email", [
                        ":email" => $_POST['email']
                    ]
                )->fetch();

                if ($_POST['ResetPwd'] == "on") {
                    $resetUserPwd = generateRandomString();
                    resetUserPassword($_POST['email'], $resetUserPwd, 1);
                }

                if ($_POST['checkboxDate'] != 0) {

                    if ($_POST['checkboxDate'] >= 1 && $_POST['checkboxDate'] <= 12) {
                        if ($info['expire_timestamp'] > time()) {
                            $new_expire_timestamp = $info['expire_timestamp'] + 60 * 60 * 24 * 31 * $_POST['checkboxDate'];
                        } else {
                            $new_expire_timestamp = time() + 60 * 60 * 24 * 31;
                        }
                    } else {
                        $new_expire_timestamp = strtotime($_POST['customDate']);
                    }

                    updateExpireTimestamp($info['email'], $new_expire_timestamp);

                }

            }

            if (!empty($_POST['createUserEmail'])) {
                $createUserPwd = generateRandomString();
                if (createUserAccount($_POST['createUserEmail'], $createUserPwd, 0) == false) {
                    $Err = i8ln('Email already in use.');
                }
            }

            if (($_POST['ResetPwd'] == "off" && $_POST['checkboxDate'] == 0 && $_POST['email'] == i8ln('Select a user...')) && empty($_POST['createUserEmail'])) {
                $Err = i8ln('No changes made.');
            }
        } else {
            $Err = i8ln('No changes made.');
        }
    }
    if (isset($_GET['resetPwd'])) {
    ?>
        <p><h2><?php echo "[<a href='.'>{$title}</a>] - "; echo i8ln('Forgot password'); ?></h2></p>
        <form action='' method='POST'>
            <table>
                <tr>
                    <th><?php echo i8ln('E-mail'); ?></th><td><input type="text" name="email" required></td>
                </tr>
                <tr>
                    <td id="one-third"><input id="margin" type="submit" name="submit_forgotPwd"><a class='button' href='/login.php'><?php echo i8ln('Back'); ?></a></td><td></td>
                </tr>
            </table>
        </form>
    <?php
    } elseif (!empty($_SESSION['user']->updatePwd)) {
        ?>
        <p><h2><?php echo "[<a href='.'>{$title}</a>] - "; echo i8ln('Change your password.'); ?></h2></p>
        <form action='' method='POST'>
            <table>
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
                }
                ?>
                <tr>
                    <td id="one-third"><input id="margin" type="submit" name="submit_updatePwd"><a class='button' href='/login.php'><?php echo i8ln('Back'); ?></a></td><td></td>
                </tr>
            </table>
        </form>
   <?php
    } elseif (!empty($_SESSION['user']->email && in_array($_SESSION['user']->email, $adminEmail))) {
    ?>
        <p><h2><?php echo "[<a href='.'>{$title}</a>] - "; echo i8ln('Admin page'); ?></h2></p>
        <?php
        if (!file_exists($logfile)) {
            if(file_put_contents($logfile, "-- This is a test to make sure logging is okay. " . date('Y-m-d H:i:s') ."\r\n", FILE_APPEND) == false){
                echo "<h1>Warning</h1><p>Your backup logging doesn't work. In case of database corruption all data may be lost.<br>To solve this, type:<br><i><b>sudo chgrp www-data " . dirname(__DIR__) . "<br>sudo chmod g+w " . dirname(__DIR__) . "</b></i></p>";
            }
        }
        ?>
        <form action='' method='POST'>
            <table>
                <tr>
                    <th><?php echo i8ln('Select user'); ?></th>
                    <td>
                        <select name="email" class='select' required>
                            <option><?php echo i8ln('Select a user...'); ?></option>
                            <?php
                            $users = $db->select("users", [
                                "email"
                            ]);

                            foreach($users as $user)
                            {
                                echo "<option>{$user['email']}</option>";
                            }
                            ?>
                        </select>
                    </td>
                </tr>
                <tr>
                    <th><?php echo i8ln('Expire Date'); ?></th>
                    <td>
                        <label><input onclick="document.getElementById('customDate').disabled = true;" type="radio" name="checkboxDate" value="0" checked="checked"><?php echo i8ln('No change'); ?></label>
                        <label><input onclick="document.getElementById('customDate').disabled = true;" type="radio" name="checkboxDate" value="1"><?php echo i8ln('1 Month'); ?></label>
                        <label><input onclick="document.getElementById('customDate').disabled = true;" type="radio" name="checkboxDate" value="3"><?php echo i8ln('3 Months'); ?></label>
                        <label><input onclick="document.getElementById('customDate').disabled = true;" type="radio" name="checkboxDate" value="6"><?php echo i8ln('6 Months'); ?></label>
                        <label><input onclick="document.getElementById('customDate').disabled = true;" type="radio" name="checkboxDate" value="12"><?php echo i8ln('12 Months'); ?></label>
                        <label><input onclick="document.getElementById('customDate').disabled = false;" type="radio" name="checkboxDate" value="100"><?php echo i8ln('Custom'); ?></label>
                        <input class="date" type="date" name="customDate" id="customDate" value="<?php echo date('Y-m-d', time()); ?>" disabled="disabled">
                    </td>
                </tr>
                <tr>
                    <th><?php echo i8ln('Reset Password'); ?></th><td><input type="checkbox" name="ResetPwd"></td>
                </tr>
                <tr>
                    <th><?php echo i8ln('Create User'); ?></th><td><input type="text" name="createUserEmail" placeholder='E-mail'></td>
                </tr>
                <tr>
                    <td id="one-third"><input id="margin" type="submit" name="submit_updateUser"></td><td></td>
                </tr>
            </table>
        </form>
        
        <?php
        if (isset($_POST['submit_updateUser'])) {
        ?>
            <p><h2>CHANGES</h2></p>
            <table>
                <?php
                if (empty($Err)) {
                    if (isset($_POST['submit_updateUser']) && $_POST['checkboxDate'] != 0 && $_POST['email'] != i8ln('Select a user...')) {
                    ?>
                    <tr>
                        <th id="one-third"><?php echo $_POST['email'] . " - " . i8ln('Expire Date'); ?></th>
                        <td><input type="text" name="infoMess" value="<?php echo date('Y-m-d', $new_expire_timestamp); ?>" id="greenBox" disabled></td>
                    </tr>
                    <?php
                    }
                    if (isset($_POST['submit_updateUser']) && $_POST['ResetPwd'] == "on" && $_POST['email'] != i8ln('Select a user...')) {
                    ?>
                    <tr>
                        <th id="one-third"><?php echo $_POST['email'] . " - " . i8ln('Password'); ?></th>
                        <td><input type="text" name="infoMess2" value="<?php echo $resetUserPwd;?>" id="greenBox"></td>
                    </tr>
                    <?php
                    }
                    if (isset($_POST['submit_updateUser']) && !empty($_POST['createUserEmail'])) {
                    ?>
                    <tr>
                        <th id="one-third"><?php echo $_POST['createUserEmail'] . " - " . i8ln('Created Account'); ?></th>
                        <td><input type="text" name="infoMess2" value="<?php echo $createUserPwd;?>" id="greenBox"></td>
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
                }
                ?>
            </table>
        <?php
        }
    } else {
        ?>
        <p><h2><?php echo "[<a href='.'>{$title}</a>] - "; echo i8ln('Login'); ?></h2></p>
        <form action='' method='POST'>
            <table>
                <tr>
                    <th><?php echo i8ln('E-mail'); ?></th><td><input type="text" name="email" required <?php if(isset($_POST['submit_login'])) { echo "value='$_POST[email]'"; } ?> placeholder="E-mail"></td>
                </tr>
                <tr>
                    <th><?php echo i8ln('Password'); ?></th><td><input type="password" name="password" required placeholder="Password"></td>
                </tr>
                <?php
                if (isset($_POST['submit_login']) && password_verify($_POST['password'], $info['password']) != 1) {
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
                }
                ?>
                <tr>
                    <td id="one-third"><input id="margin" type="submit" name="submit_login"><a class='button' href='?resetPwd'><?php echo i8ln('Reset Password'); ?></a></td><td></td>
                </tr>
            </table>
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
