<?php session_start();
if(isset($_POST['submit'])){
    var_dump($_POST);
}

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <meta http-equiv="Content-Type" content="text/html;charset=UTF-8" />
        <title>ToDo:]- Dashboard</title>
        <link rel="stylesheet" type="text/css" href="css/reset.css" media="screen" />
        <link rel="stylesheet" type="text/css" href="css/text.css" media="screen" />
        <link rel="stylesheet" type="text/css" href="css/grid.css" media="screen" />
        <link rel="stylesheet" type="text/css" href="css/layout.css" media="screen" />
        <link rel="stylesheet" type="text/css" href="css/nav.css" media="screen" />
        <link href="css/jquery-ui.min.css" rel="stylesheet" type="text/css" />
        <link href="css/jquery-ui.min.css" rel="stylesheet" type="text/css" />
        <!--[if IE 6]><link rel="stylesheet" type="text/css" href="css/ie6.css" media="screen" /><![endif]-->
        <!--[if IE 7]><link rel="stylesheet" type="text/css" href="css/ie.css" media="screen" /><![endif]-->
        <!-- BEGIN: load jquery -->
        <script src="js/jquery-1.6.4.min.js" type="text/javascript"></script>
        <script type="text/javascript" src="js/jquery-ui/jquery-ui.min.js"></script>
        <script src="js/setup.js" type="text/javascript"></script>
        <script type="text/javascript">
            $(document).ready(function () {
                setDatePickerOrder('pick_date');
            });
        </script>
    </head>
    <body>

        <?php
//make sure they are logged in and activated.
        require_once('inc.functions.php');
        connectToSQL();
        secure();
        ?>
        <div class="container_12">
            <?php
            require_once('inc.header.php');
            //IF ADMIN he can control
            $uid = $_COOKIE['userid'];
            if ($level == 1 && isset($_GET['uid'])) {
                $uid = $_GET['uid'];
            }

            $opttasks = "</optgroup><optgroup label='Pending'>";
            connectToSQL();

            $qry = "SELECT * FROM users WHERE id = '$uid'";
            $result = mysql_query($qry)or die(mysql_error());
            while ($row = mysql_fetch_assoc($result)) {
                $x_fname = $row["f_name"] . " " . $row['l_name'] ."'s";
                $hubstaff_id = $row["hubstaff_id"];
            }
            $users = [];
            $qry = "SELECT id,f_name FROM users WHERE hubstaff_id <> 0 AND id <> $uid";
            $result = mysql_query($qry) or die(mysql_error());
            while ($row = mysql_fetch_assoc($result)) {
                $users[] = $row;
            }   
            ?>
            <div class="grid_10">
                <br/>
                <h1>Employee of the month</h1>
                <form method="post" >
                    <select name="month">
                     <?php 
                    for ($i = 1; $i < 13; $i++) {
                        $dateObj   = DateTime::createFromFormat('!m', $i);
                    $monthName = $dateObj->format('F');
                    echo "<option value='$i' " . ($monthName == date('m')?'selected':'') . " >$monthName</option>";
                    }
                    ?>   
                    </select>
                    
                <table class="table">  
                    <tr>
                        <th>
                        </th>
                    
                    </tr>
                   <?php 
                foreach ($users as $row) {
                    if(in_array($users, $row)){
                        continue;
                    }        
                    ?>
                    <tr><td><?= $row['f_name'] ?></td><td><input type="number" width="20px" name="eotm[<?= $row['id']?>][score]" />
                            <input type="text" name="eotm[<?= $row['id']?>][comment]" size="50" /><br/></td></tr>
                    <?php
                }
                ?>          
                </table>      
                    <input name="submit" type ="submit" class="btn btn-success" value="submit" />
                </form>
            </div>
            <div class="grid_2">
                <div class="box round first grid">
                    <h2>Reports </h2>
                    <br/>
                    <form target="_blank" action="staff_report.php" method="get">
                        Start Date:
                        <input type="text" name="startdate" size="10" class="pick_date" value="<?php echo date('Y-m-d', strtotime('1 month ago')) ?>" />
                        <br/><br/>
                        End Date:
                        <input type="text" name="enddate" size="10" class="pick_date" value="<?php echo date('Y-m-d') ?>" />
                        <input type="hidden" name="uid" value="<?php echo $uid; ?>"/>   
                        <input type="hidden" name="fname" value="<?php echo $x_fname; ?>" />
                        <br/><br/>
                        <input class="btn btn-purple" type="submit" value="Generate Report" />
                    </form>
                </div>
                <div class="box round first grid">
                    <h2>Users: </h2>
                    <br/>
                    <?php   
                    foreach ($users as $row) {
                        echo "<a href='staff.php?uid={$row["id"]}'><button class='btn btn-black' >{$row["f_name"]} Work</button></a><br/><br/>";
                    }                    
                    ?>
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
