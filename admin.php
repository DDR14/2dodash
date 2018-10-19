<?php
session_start();
require_once('inc.functions.php');
connectToSQL();
secure();
adminSecure();
if (isset($_POST['delete_worker'])) {
    connectToSQL();
    $id = (int) $_GET['uid'];
    mysql_query("DELETE FROM `users` WHERE `id` = '$id' ");
    mysql_query("DELETE FROM `user_time` WHERE `user_id` = '$id' ");
    mysql_query("DELETE FROM `staff_activities_data` WHERE `user_id` = '$id' ");
    mysql_query("DELETE FROM `staff_tasks` WHERE `users_id` = '$id' ");
    header("Location: admin.php");
    die("Record Deleted. Redirecting...");
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <meta http-equiv="content-type" content="text/html; charset=utf-8" />
        <title>ToDo:]- Dashboard</title>
        <link rel="stylesheet" type="text/css" href="css/reset.css" media="screen" />
        <link rel="stylesheet" type="text/css" href="css/text.css" media="screen" />
        <link rel="stylesheet" type="text/css" href="css/grid.css" media="screen" />
        <link rel="stylesheet" type="text/css" href="css/layout.css" media="screen" />
        <link rel="stylesheet" type="text/css" href="css/nav.css" media="screen" />
        <!--[if IE 6]><link rel="stylesheet" type="text/css" href="css/ie6.css" media="screen" /><![endif]-->
        <!--[if IE 7]><link rel="stylesheet" type="text/css" href="css/ie.css" media="screen" /><![endif]-->
        <link href="css/table/demo_page.css" rel="stylesheet" type="text/css" />
        <!-- BEGIN: load jquery -->
        <script src="js/jquery-1.6.4.min.js" type="text/javascript"></script>
        <script src="js/table/jquery.dataTables.min.js" type="text/javascript"></script>
        <!-- END: load jquery -->
        <script type="text/javascript">
            $(document).ready(function () {
                $('.datatable').dataTable({
                    "order": [[ 0, "desc" ]],
                    "aaSorting": [],
                    "stateSave": true,
                    "iDisplayLength": 25
                });
            });
        </script>
    </head>
    <body>
        <div class="container_12">
            <?php
            require_once('inc.header.php');
            ?>
            <div class="grid_8">
                <div class="box round first fullpage">
                    <h2>
                        Dashboard Users</h2>
                    <div class="block">
                        <?php
//here we will display all orders unless they have selected an order - which then we will display information on that order.
                        ?>
                        <table class="data display datatable" id="example">
                            <thead>
                                <tr>
                                    <th>Id</th>
                                    <th>Username</th>
                                    <th>Name</th>
                                    <th>Companies</th>
                                    <th>Level</th>
                                    <th>Approved</th>
                                    <th>Edit</th>
                                </tr>
                            </thead>
                            <tbody>

                                <?php
//lets print users here with edit boxes
                                connectToSQL();
                                $qry = "SELECT * FROM `users` WHERE `id` >= '0'";
                                $result = mysql_query($qry)or die(mysql_error());

                                while ($row = mysql_fetch_assoc($result)) {
                                    //we should have all the information now lets get it and print it.
                                    $id = $row['id'];
                                    $username = $row['username'];
                                    $email = $row['email'];
                                    $approved = $row['approved'];
                                    $level = $row['level'];
                                    $name = $row['f_name'] . ' ' . $row['l_name'];
                                    //lets figure on what companies they are apart of.

                                    $qry2 = "SELECT `c_id` FROM `company_cross` WHERE `u_id`='" . $id . "'";
                                    $result2 = mysql_query($qry2)or die(mysql_error());
                                    $companiess = "";

                                    while ($row2 = mysql_fetch_assoc($result2)) {
                                        $company_id = $row2['c_id'];

                                        $qry3 = "SELECT `name` FROM `companies` WHERE `id`='" . $company_id . "'";
                                        $result3 = mysql_query($qry3)or die(mysql_error());

                                        while ($row3 = mysql_fetch_assoc($result3)) {
                                            $companiess .= $row3['name'] . ", ";
                                        }
                                    }

                                    if ($approved == 0) {
                                        $approved = "<button class=\"btn btn-small btn-yellow\">Unapproved</button>";
                                    } elseif ($approved == 1) {
                                        $approved = "<button class=\"btn btn-small btn-green\">Approved</button>";
                                    } elseif ($approved == 3) {
                                        $approved = "<button class=\"btn btn-small btn-red\">Banned</button>";
                                    } elseif ($approved == 4) {
                                        $approved = "<button class=\"btn btn-small btn-pink\">Time Clock Only</button>";
                                    }

                                    if ($level == 1) {
                                        //they are admin
                                        $level = "<a href=\"administrating.php?levelup=1&userid=" . $id . "\"><button class=\"btn btn-small btn-blue\">Admin</button></a>";
                                    } elseif ($level == 0) {
                                        //they are user
                                        $level = "<a href=\"administrating.php?levelup=1&userid=" . $id . "\"><button class=\"btn btn-small btn-navy\">User</button></a>";
                                    } else {
                                        $level = "<a href=\"administrating.php?levelup=1&userid=" . $id . "\"><button class=\"btn btn-small btn-purple\">Manager</button></a>";
                                    }

                                    echo "<tr><td>$id</td><td>" . $username . "</td><td>" . $name . "</td><td>" . $companiess . "<br />";
                                    ?>
                                    <form action="administrating.php?addcompany=1&userid=<?php echo $id; ?>" method="POST">
                                        <select name="company">
                                            <?php
                                            $qry4 = "SELECT * FROM `companies` WHERE `id` >= '0'";
                                            $result4 = mysql_query($qry4)or die(mysql_error());

                                            while ($row4 = mysql_fetch_assoc($result4)) {
                                                echo "<option value=\"" . $row4['id'] . "\">" . $row4['name'] . "</option>";
                                            }
                                            ?>
                                        </select>
                                        <input type="submit" value="add" />
                                    </form>

                                    <?php
                                    echo "</td><td>" . $level . "</td><td><center><a href=\"administrating.php?approve=1&userid=" . $id . "\">" . $approved . "</a></center></td><td><a href='admin.php?uid=" . $id . "' class='btn btn-small btn-teal'>Edit</td></tr>";
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="grid_4">
                <div class="box round first">
                    <h2>Edit User</h2>
                    <br/>
                    <?php
                    if (isset($_GET['uid'])) {
                        $id = (int) $_GET['uid'];
                        $opt_gender = "<label><input required type='radio' name='gender' value='m' />Mr </label>"
                                . "<label><input required type='radio' name='gender' value='f' />Ms </label>";
                        if (isset($_POST['submitted'])) {
                            foreach ($_POST AS $key => $value) {
                                $_POST[$key] = mysql_real_escape_string($value);
                            }
                            $sql = "UPDATE `users` SET  `username` =  '{$_POST['username']}' , `email` =  '{$_POST['email']}' , `f_name` =  '{$_POST['f_name']}' ,  `l_name` =  '{$_POST['l_name']}' ,  `phone` =  '{$_POST['phone']}' ,  `hubstaff_id` =  '{$_POST['hubstaff_id']}' ,  `code` =  '{$_POST['code']}', gender='{$_POST['gender']}', position='{$_POST['position']}', company='{$_POST['company']}' WHERE `id` = '$id' ";
                            mysql_query($sql) or die(mysql_error());
                            echo '<br/><div class="message success first"><h5>Success</h5>' . ((mysql_affected_rows()) ? "Edited row." : "Nothing changed.") . '</div>';
                        }
                        
                        $row = mysql_fetch_array(mysql_query("SELECT * FROM `users` WHERE `id` = '$id' "));
                        ?>

                        <form action='' method='POST'> 
                            <p><b>Username:</b><br /><input type='text' name='username' value='<?= stripslashes($row['username']) ?>' /> </p>
                            <p><b>Email:</b><br /><input type='text' name='email' value='<?= stripslashes($row['email']) ?>' /> </p>
                            <p><b>Gender:</b><br />
                            <?php echo str_replace("value='{$row["gender"]}'", "value='{$row["gender"]}' checked", $opt_gender); ?>
                            </p>
                            <p><b>Name:</b><br /><input type='text' placeholder="First" size="10" name='f_name' value='<?= stripslashes($row['f_name']) ?>' /> <input type='text' placeholder="Last" size="10" name='l_name' value='<?= stripslashes($row['l_name']) ?>' /> </p>
                            <p><b>Position:</b><br /><input required value="<?php echo $row['position']; ?>" type='text' name='position'/> </p>  
                            <p><b>Phone:</b><br /><input type='text' name='phone' value='<?= stripslashes($row['phone']) ?>' /> </p>
                            <p><b>Hubstaff Id:</b><br /><input type='text' name='hubstaff_id' value='<?= stripslashes($row['hubstaff_id']) ?>' /> </p>
                            <p><b>Company:</b><br /><input type='text' name='company' value='<?= stripslashes($row['company']) ?>' /> </p>
                            <p><b>Code:</b><br /><input type='text' name='code' value='<?= stripslashes($row['code']) ?>' /> </p>
                            <p><input type='submit' class="btn btn-blue" value='Edit Row' /><input type='hidden' value='1' name='submitted' /> </p>
                        </form> 
                        <form method="post" action="" class="floatright" style="margin-top: -40px">
                            <input type="hidden" name="uid" value="<?php echo $id; ?>" />
                            <input onclick="return confirm('Are you really sure about this?')" type="submit" class="btn btn-red " name="delete_worker" value="delete this worker">
                        </form>
                <?php } ?> 
                </div>
                <div class="box round first">
                    <h2>Add New User</h2>
                    <br/>
                    <a href="register.php">
                        <button class="btn btn-large btn-green">
                            Go to Registration Page
                        </button>
                    </a>
                </div>
            </div>
            <div class="clear">
            </div>
        </div>
        <div class="clear">
        </div> 
        <div id="site_info">
            <p>
                Copyright <a href="#">ToDo:]</a>. All Rights Reserved.
            </p>
        </div>
    </body>
</html>
