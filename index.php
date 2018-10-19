<?php
session_start();
if (!empty($_COOKIE['userid'])) {
    header('Location: dashboard.php');
    die('You are how logged in. Redirecting...');
}
if (isset($_POST['loggingin'])):
    include "include/db.php";
    $db = new db();

// 83y74jk36fg83
//we need to check to see if their password matches the hashed password
//then we need to randomly create a session id hash it - upload it to the database and then see if that matches.  this prevents session hijacking
    $hash = sha1("83y74jk36fg83" . $_POST['password']);
    $exists = $db->find('first', 'users', 'username = :username AND password = :password', [
        'password' => $hash,
        'username' => $_POST['username']
            ], 'id, f_name, alarm');

    if ($exists) {
        $user_id = $exists['id'];

        setcookie('userid', $user_id, time() + (86400 * 30), "/"); // 86400 = 1 day
        setcookie('user[f_name]', $exists["f_name"], time() + (86400 * 30), "/"); // 86400 = 1 day

        if ($exists['alarm'] == 1) {
            $_SESSION['alarm'] = 1;
        }
        header('Location: dashboard.php?login_success=1');
        die();
    } else {
        //the password didn't match
        header('Location: index.php?error=1');
        die();
    }
endif;
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <meta http-equiv="content-type" content="text/html; charset=utf-8" />
        <title>Todo:] Dashboard</title>
        <link rel="stylesheet" type="text/css" href="css/reset.css" media="screen" />
        <link rel="stylesheet" type="text/css" href="css/text.css" media="screen" />
        <link rel="stylesheet" type="text/css" href="css/grid.css" media="screen" />
        <link rel="stylesheet" type="text/css" href="css/layout.css" media="screen" />
        <link rel="stylesheet" type="text/css" href="css/nav.css" media="screen" />
        <!--[if IE 6]><link rel="stylesheet" type="text/css" href="css/ie6.css" media="screen" /><![endif]-->
        <!--[if IE 7]><link rel="stylesheet" type="text/css" href="css/ie.css" media="screen" /><![endif]-->
        <script src="js/setup.js" type="text/javascript"></script>
    </head>
    <body>
        <div class="container_12">
            <div class="grid_12 header-repeat">
                <div id="branding">
                    <div class="floatleft">
                        <font color="white"><h1><img src="<?php echo '//' . $_SERVER['HTTP_HOST'] . "/"; ?>img/logo.png" alt="Logo" />2<span style="font-weight: normal">Do</span>Dash:]</h1></font></div>
                    <div class="floatright">
                        <div class="floatleft">
                            <!-- <img src="img/img-profile.jpg" alt="Profile Pic" /> --></div>
                        <div class="floatleft marginleft10">
                            <!-- <ul class="inline-ul floatleft">
                                 <li>Hello Admin</li>
                                 <li><a href="#">Config</a></li>
                                 <li><a href="#">Logout</a></li>
                             </ul>
                            -->
                            <br />
                            <span class="small grey"></span>
                        </div>
                    </div>
                    <div class="clear">
                    </div>
                </div>
            </div>
            <div class="clear">
            </div>
            <div class="grid_12">
                <ul class="nav main">
                    <li class="ic-dashboard"><a href="index.php"><span>Login

                                <?php
                                if (isset($_GET['registered'])) {
                                    echo " - You have successfully registered.";
                                }
                                ?>
                            </span></a> </li>
                    <li class="ic-form-style"><a href="register.php"><span>Register</span></a></li>
                </ul>
            </div>
            <div class="clear">
            </div>
            <div class="grid_12">
                <div class="box round first fullpage">
                    <h2>
                        Login</h2>
                    <div class="block ">
                        <form method="POST" >
                            <table class="form">
                                <?php
                                if (isset($_GET['error'])) {
                                    ?>
                                    <tr><td colspan="2">There was an error with your login - please try again.</td></tr>
                                    <?php
                                }
                                ?>
                                <?php
                                if (isset($_GET['error']) && $_GET['error'] == "needa") {
                                    ?>
                                    <tr><td colspan="2">Your account has not been approved.  Please notify a manager.</td></tr>
                                    <?php
                                }
                                if (isset($_GET['error']) && $_GET['error'] == "banned") {
                                    ?>
                                    <tr><td colspan="2">Your account has been banned.  If this was a mistake please notify an admin.</td></tr>
                                    <?php
                                }
                                ?>
                                <tr>
                                    <td class="col1">
                                        <label>
                                            Username:</label>
                                    </td>
                                    <td class="col2">
                                        <input type="text"  name="username" />
                                    </td>
                                </tr>
                                <tr>
                                    <td class="col1">
                                        <label>
                                            Password:</label>
                                    </td>
                                    <td class="col2">
                                        <input type="password" name="password" />
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <button class="btn btn-green" type="submit" name="loggingin" >Login</button>

                                    </td>
                                    <td>
                                        <small><a href="forgotpassword.php">Forgot password?</a></small>
                                    </td>
                                </tr>

                            </table>
                        </form>
                    </div>
                </div>
            </div>
            <div class="clear">
            </div>
        </div>
        <div class="clear">
        </div>
        <div id="site_info">
            <p>
                Copyright <a href="#">ToDo:]</a>. All Rights Reserved. <?php
                if (isset($_GET['bd']) && $_GET['bd'] == "Mellon") {
                    echo "<a href=\"bd.php\"> Enter</a>";
                }
                ?>
            </p>
        </div>
    </body>
</html>
