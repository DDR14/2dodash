<?php
session_start();
//make sure they are logged in and activated.
require_once('inc.functions.php');
secure();
company_db_connect(1);
if (isset($_POST['sent_info2'])) {
    $result = mysql_query("UPDATE `gw_widget` SET samples_sent =DATE(NOW()) WHERE `id`={$_POST['id']} ") or trigger_error(mysql_error());
}
if (isset($_POST['sent_info'])) {
    $result = mysql_query("UPDATE `zen_secret_santa` SET sent =DATE(NOW()) WHERE `id`={$_POST['id']} ") or trigger_error(mysql_error());
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
        <!--Jquery UI CSS-->        
        <link href="css/jquery-ui.min.css" rel="stylesheet" type="text/css" />
        <link href="css/vieworder.css" rel="stylesheet" type="text/css" />       
        <!-- BEGIN: load jquery -->
        <script src="js/jquery-1.6.4.min.js" type="text/javascript"></script>
        <script src="js/jquery-ui/jquery-ui.min.js" type="text/javascript"></script>
 
        <script src="js/table/jquery.dataTables.min.js" type="text/javascript"></script>
        <!-- END: load jquery -->
        <script src="js/setup.js" type="text/javascript"></script> 
    </head>
    <body>
        <div class="container_12">
            <?php
            require_once('inc.header.php');
            ?>
            <div class="grid_2">
                <div class="box sidemenu">
                    <ul class="section menu">
                            <li><a class="menuitem">CHANGE CONTEST</a>
                                <ul class="submenu current">         
                                    <?php                                    
                                    company_db_connect(1);
                                    $id = 0;
                                    $active = '';
                                    $contest = [];
                                    $result = mysql_query("SELECT a.*, 
                                            (SELECT COUNT(1) FROM gw_widget c WHERE c.free_samples <> 0 AND c.samples_sent = '0000-00-00') AS count_fsrequests,
                                            (SELECT COUNT(1) FROM gw_widget b WHERE a.id = b.contest_id) AS count_widget 
                                            FROM gw_contests a ORDER BY a.created DESC") or die(mysql_error());
                                    while ($row = mysql_fetch_array($result)) {                                        
                                        echo '<li><a href="competitions.php?id=' . (int) $row['id'] . '">' . $row['name'] . ' <small>(' . (int) $row['count_widget'] . ')</small></a></li>';
                                     $count_fsrequests= $row['count_fsrequests'];
                                        
                                    }
                                    ?>
                                </ul>
                            </li>
                            <li><a class="menuitem">Free Sample Requests</a>
                                <ul class="submenu current">         
                                    <?php 
                                    $fr_inactive = "";
                                    $fr_active = "";
                                    if(isset($_GET['sent'])){
                                        $fr_inactive = "class='active'";
                                    } else {
                                        
                                        $fr_active = "class='active'";
                                    }                                    
                                    ?>
                                    <li><a <?= $fr_active; ?> href="free_samples.php" >Requests <small>(<?= $count_fsrequests; ?>)</small></a></li>
                                    <li><a <?= $fr_inactive ?> href="free_samples.php?sent=1" >Sent</a></li>
                                </ul>
                            </li>
                        </ul>
                </div>
            </div>
            <div class="grid_10">
                <div class="first grid">
                    <h2>Free Sample Requests</h2>
                    <table class="data display dataTable" id="example">                        
                            <thead>
                                <tr> 
                                    <th>CONTEST</th>
                                <th>Source Info</th>
                                <th>Teacher Name</th>
                                <th>Teacher School</th> 
                                <th>Teacher City</th>
                                <th>Teacher State</th>
                                <th>Phone Number</th>
                                <th>Date Created</th> 
                                <th>Sent</th>
                                </tr>
                            </thead>                         
                        <tbody>
                        <?php                        
                        if(isset($_GET['sent'])){
                            $sql = "SELECT * FROM(SELECT 'Secret Santa Promotion' AS contest, a.id, 
CONCAT(a.your_firstname,' ', a.your_lastname) AS full_name, 
a.your_email, CONCAT(a.teacher_firstname, ' ', a.teacher_lastname) AS teacher_name, 
a.teacher_email, a.teacher_school, a.teacher_city, 
a.teacher_state, a.date_created, '' AS phone_number, a.sent
FROM zen_secret_santa a
UNION
SELECT b.name, a.id, '','', a.full_name, a.email_address, a.organization, 
CONCAT(a.address_street_1, '<br/>', a.address_street_2, '<br/>', a.address_city), 
CONCAT(a.address_state, ', ', a.address_zip), a.created, a.phone_number, a.samples_sent
FROM gw_widget a INNER JOIN gw_contests b
ON a.contest_id = b.id
WHERE free_samples <> 0 AND a.samples_sent <> '0000-00-00') AS x ORDER BY date_created DESC";
                        } else {
                            $sql = "SELECT b.name AS contest, a.id, '' AS full_name,'' AS your_email, 
                                a.full_name AS teacher_name, a.email_address AS teacher_email, 
                                a.organization AS teacher_school, 
CONCAT(a.address_street_1, '<br/>', a.address_street_2, '<br/>', a.address_city) AS teacher_city, 
CONCAT(a.address_state, ', ', a.address_zip) AS teacher_state, a.created AS date_created, a.phone_number, a.samples_sent AS sent
FROM gw_widget a INNER JOIN gw_contests b
ON a.contest_id = b.id
WHERE free_samples <> 0 AND a.samples_sent = '0000-00-00'";
                        }
                        
                        $result = mysql_query($sql) or trigger_error(mysql_error());
                        while ($row = mysql_fetch_array($result)) {
                            foreach ($row AS $key => $value) {
                                $row[$key] = stripslashes($value);
                            }
                            echo "<tr>";
                            echo "<td>" . nl2br($row['contest']) . "</td>";
                            echo "<td valign='top'>" . nl2br($row['full_name']) . "<br/>";
                            echo "<a href='mailto:" . nl2br($row['your_email']) . "'>" 
                                    . nl2br($row['your_email']) . "</a></td>";
                            echo "<td valign='top'>" . nl2br($row['teacher_name']) . "<br/>";
                            echo "<a href='mailto:" . nl2br($row['teacher_email']) . "'>" . nl2br($row['teacher_email']) . "</a></td>";
                            echo "<td valign='top'>" . nl2br($row['teacher_school']) . "</td>";
                            echo "<td valign='top'>" . nl2br($row['teacher_city']) . "</td>";
                            echo "<td valign='top'>" . nl2br($row['teacher_state']) . "</td>";
                            echo "<td valign='top'>" . nl2br($row['phone_number']) . "</td>";
                            echo "<td valign='top'>" . nl2br($row['date_created']) . "</td>";    
                            echo "<td><form method='post'>";
                            if($row['sent'] == '0000-00-00'){
                                if($row['contest'] == 'Secret Santa Promotion'){
                                    echo "<input type='submit' name='sent_info' value='Sent' />";
                                } else {
                                    echo "<input type='submit' name='sent_info2' value='Sent' />";
                                }                                
                            } else {
                                echo $row['sent'];
                            }                            
                            echo "<input type='hidden' name='id' value='" . $row['id'] . "' /></form></td></tr>";
                        }
                        ?>
                        <tbody>
                    </table>
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
        <script>
            $(document).ready(function () {
                var table = $('#example').DataTable({
                    "aaSorting": [],
                    "stateSave": true
                }); 
                setSidebarHeight();              
            });
        </script>
    </body>
</html>
