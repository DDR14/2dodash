<?php
session_start();
include "include/db.php";
$db = new db('boostpr1_boostpromotions');

require_once('inc.functions.php');
secure();
if (isset($_POST['ExportCsv'])) {
    $table = 'zen_customers';
    $fields = 'DISTINCT customers_email_address';
    $where = "customers_newsletter=1 AND customers_email_address NOT LIKE '%gmail%' 
AND customers_email_address NOT LIKE '%yahoo%' 
AND customers_id IN (SELECT customers_id FROM zen_orders)"; // remove this line on next giveaway
    $filename = 'blast_school';

    if (isset($_POST['web_based'])) {
        $table = 'zen_customers';
        $fields = "DISTINCT customers_email_address";
        $where = "customers_newsletter=1 
AND (customers_email_address LIKE '%gmail%' 
OR customers_email_address LIKE '%yahoo%' )
AND customers_id IN (SELECT customers_id FROM zen_orders)"; // remove this line on next giveaway
        $filename = 'blast_web';
    }

    if (isset($_POST['free_samples'])) {
        $table = "gw_widget";
        $fields = "id, email_address, full_name, organization, 
                address_street_1, address_street_2, address_city, 
                address_state, address_zip, phone_number, created";
        $where = "free_samples = 1 AND contest_id = {$_POST['contest_id']}";
        $filename = 'free_samples';
    }

    $export = $db->find('all', $table, $where, [], $fields);
    if (!$export) {
        die('Couldn\'t fetch records');
    }
    $num_fields = count($export[0]);
    $headers = array();
    foreach ($export[0] as $key => $value) {
        $headers[] = $key;
    }
    $fp = fopen('php://output', 'w');
    if ($fp) {
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="' . $filename . '.csv"');
        header('Pragma: no-cache');
        header('Expires: 0');
        fputcsv($fp, $headers);
        foreach ($export as $row) {
            fputcsv($fp, array_values($row));
        }
        die;
    }
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
        <script type="text/javascript" src="js/jquery-ui/jquery-ui.min.js"></script>
        <!-- END: load jquery -->
        <script src="js/table/jquery.dataTables.min.js" type="text/javascript"></script>
        <script src="js/setup.js" type="text/javascript"></script>
        <script type="text/javascript">
            $(document).ready(function () {
                setSidebarHeight();
            });
            $(function () {
                var iframe = $('<iframe frameborder="0" marginwidth="0" marginheight="0" allowfullscreen></iframe>');
                var dialog = $("<div></div>").append(iframe).appendTo("body").dialog({
                    autoOpen: false,
                    modal: true,
                    resizable: false,
                    width: "auto",
                    height: "auto",
                    close: function () {
                        iframe.attr("src", "");
                    }
                });
                $("a.xframe").on("click", function (e) {
                    e.preventDefault();
                    var src = $(this).attr("href");
                    var title = $(this).attr("data-title");
                    var width = $(this).attr("data-width");
                    var height = $(this).attr("data-height");
                    iframe.attr({
                        width: +width,
                        height: +height,
                        src: src
                    });
                    dialog.dialog("option", "title", title).dialog("open");
                });
            });
        </script>
    </head>
    <body>
        <div class="container_12">
            <?php
            require_once('inc.header.php');

            if (isset($_POST['update_contest'])) {

                //if there is a file uploaded
                if ($_FILES["file"]["error"] != 4) {
                    //start processing file
                    if ($_FILES["file"]["error"] > 0 && $_FILES["file"]["error"] != 4) {
                        echo "Error: " . $_FILES["file"]["error"] . "<br />";
                        exit();
                    } else {
                        $image = new Imagick();
                        $image->readimage($_FILES["file"]["tmp_name"]);

                        $dir = "../gowin/webroot/img/";
                        $_POST['contest']['contests_image'] = uniqid() . $_FILES["file"]["name"] . ".jpg";
                        $size = 600;

                        // Resize image using the lanczos resampling algorithm based on width
                        $image->resizeImage($size, 0, Imagick::FILTER_LANCZOS, 1);
                        $image->writeImage($dir . $_POST['contest']['contests_image']);
                    }
                }

                $db->update('gw_contests', $_POST['contest'], 'id = :id', ['id' => $_POST['contest']['id']]);

                echo '<div class="message success" >UPDATED</div>';
            }
            if (isset($_POST['copy_contest'])) {
                $db->raw("INSERT INTO gw_contests (name,contests_name,contests_description,contests_image,prize,created)
                    SELECT CONCAT(name,' COPY'),contests_name,contests_description,contests_image,prize, NOW() FROM gw_contests 
                    WHERE id = :id", ['id' => $_POST['contest']['id']]);

                $insert_id = $db->db->lastInsertId();

                $db->raw("INSERT INTO gw_contest_howtos (contest_id, howto_type, worth, subtype, param1, param2, enabled, created)
                    SELECT $insert_id, howto_type, worth, subtype, param1, param2, enabled, NOW() 
                    FROM gw_contest_howtos 
                    WHERE contest_id = :id", ['id' => $_POST['contest']['id']]);

                echo '<div class="message success" >COPIED</div>';
                echo '<script type="text/javascript">window.location.href ="competitions.php";</script>';
                die();
            }
            if (isset($_POST['delete_contest'])) {
                echo $db->delete('gw_contests', 'id = :id', ['id' => $_POST['contest']['id']]);
                echo $db->delete('gw_contest_howtos', 'contest_id = :id', ['id' => $_POST['contest']['id']]);


                echo '<div class="message success" >DELETED</div>';
                echo '<script type="text/javascript">window.location.href ="competitions.php";</script>';
                die();
            }
            ?>
            <div class="grid_2">
                <div class="box sidemenu" style="height: 1061px;">
                    <div class="block" id="section-menu">
                        <ul class="section menu">
                            <li><a class="menuitem">CHANGE CONTEST</a>
                                <ul class="submenu current">         
                                    <?php
                                    $id = 0;
                                    $active = '';
                                    $contest = [];
                                    $contests = $db->find('all', 'gw_contests a ORDER BY a.created DESC', '', [], "a.*, 
                                            (SELECT COUNT(1) FROM gw_widget c WHERE c.free_samples <> 0 AND c.samples_sent = '0000-00-00') AS count_fsrequests,
                                            (SELECT COUNT(1) FROM gw_widget b WHERE a.id = b.contest_id) AS count_widget") or die(mysql_error());
                                    foreach ($contests as $row) {
                                        $count_fsrequests = $row['count_fsrequests'];
                                        if ($id == 0) {
                                            $id = isset($_GET['id']) ? (int) $_GET['id'] : $row['id'];
                                        }
                                        if ($row['id'] == $id) {
                                            $contest = $row;
                                        }
                                        $active = $id == $row['id'] ? 'class="active"' : '';
                                        echo '<li><a ' . $active . ' href="competitions.php?id=' . (int) $row['id'] . '">' . $row['name'] . ' <small>(' . (int) $row['count_widget'] . ')</small></a></li>';
                                    }
                                    ?>
                                    <li><a data-width='470' 
                                           data-title="New Competition" 
                                           data-height='550' 
                                           href="competitions_new.php" class="xframe">
                                            <span class="inlineblock ui-icon ui-icon-plus"></span>add new</a>
                                    </li>
                                </ul>
                            </li>
                            <li><a class="menuitem">Free Sample Requests</a>
                                <ul class="submenu current">         
                                    <li><a href="free_samples.php" >Requests <small>(<?= $count_fsrequests; ?>)</small></a></li>
                                    <li><a href="free_samples.php?sent=1" >Sent</a></li>
                                </ul>
                            </li>
                        </ul>
                    </div>                
                </div>
            </div>
            <div class="grid_7">
                <div class="first grid">
                    <h2><form method='post'>CONTEST: <span style="font-weight:normal"><?= $contest['name']; ?></span>
                            <input type="submit" name=school_based value='School CSV' />
                            <input type=submit name=web_based value='Web CSV' />
                            <input type=submit name=free_samples value='Free Sample CSV' />
                            <input type="hidden" name="ExportCsv" value="1" />
                            <input type="hidden" name="contest_id" value="<?= $contest['id']; ?>" />
                        </form></h2>
                    <br/>
                    <form method="post">
                        <input name="draw_winner" class="btn btn-large btn-green" type="submit" value="Draw One Winner" />
                    </form>
                    <?php
                    $statr = ['0' => 'Valid', '1' => 'Winner', '3' => 'Invalid'];
                    if (isset($_POST['change_status'])) {
                        $status = (int) $_POST['status'];
                        if ($status == 0) {
                            $db->raw("UPDATE gw_widget SET win_date ='0000-00-00 00:00:00', status = $status WHERE id = " . (int) $_POST['id']);
                        }
                        echo '<div class="message success" >Win Status of this contestant is revoked</div>';
                    }
                    if (isset($_POST['draw_winner'])) {
                        $winner_drawn = $db->find('first', "(SELECT id FROM gw_widget a JOIN numbers b ON a.points >= b.number WHERE a.contest_id = $id) as raffle ORDER BY RAND() LIMIT 1");

                        $db->raw("UPDATE gw_widget SET win_date =NOW(), status = 1 WHERE id = " . $winner_drawn['id']);

                        echo '<div class="message success" >Winner Drawn!</div>';
                    }

                    $winners = $db->find('all', 'gw_widget', 'status = 1 AND contest_id = :id', ['id' => $id]);
                    foreach ($winners as $winner) {
                        ?>

                        <table class='data display dataTable' > 
                            <thead>
                                <tr> 
                                    <th>Entries</th> 
                                    <th>Contestant</th> 
                                    <th>Action(s)</th> 
                                    <th>IP Address</th>                         
                                    <th>Date Joined</th> 
                                    <th>Status</th>
                                </tr> 
                            </thead>
                            <tbody>
                                <tr>
                                    <td style="vertical-align: middle; text-align: center;"><h2><?= $winner['points'] ?></h2></td>
                                    <td style="vertical-align: middle;"><?= $winner['full_name'] ?><br/>
                                        <?= '<a href="mailto:' . $winner['email_address'] . '">' . $winner['email_address'] . '</a>'; ?></td>
                                    <td style="vertical-align: middle;"></td>
                                    <td style="vertical-align: middle;"><?= $winner['ip_address'] ?></td>
                                    <td style="vertical-align: middle;"><?= $winner['created'] ?></td>
                                    <td style="vertical-align: middle;">
                                        <form method="post" onsubmit="return confirm('Are you sure? ')">
                                            <select name="status">
                                                <option value='1'>WINNER</option>
                                                <option value='0'>REVOKE WIN</option>
                                            </select>
                                            <input type='hidden' value='<?= $winner['id']; ?>' name='id' />
                                            <input type="submit" name="change_status" value="Change" />
                                        </form>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                        <br/>
                        <?php
                    }
                    ?>

                    <table id="example" class='data display dataTable' > 
                        <thead >
                            <tr> 
                                <th>Id</th> 
                                <th>Email Address</th> 
                                <th>Full Name</th> 
                                <th>Points</th>                         
                                <th>Status</th> 
                                <th>Win Date</th> 
                                <th>Ip Address</th> 
                                <th>Referred By</th> 
                                <th>Date Joined</th> 
                            </tr> 
                        </thead>
                        <?php
                        $contestants = $db->find('all', 'gw_widget', 'status <> 1 AND contest_id = :id', ['id' => $id]);
                        foreach ($contestants as $contestant) {
                            echo "<tr>";
                            echo "<td valign='top'>" . nl2br($contestant['id']) . "</td>";
                            echo "<td valign='top'>" . nl2br($contestant['email_address']) . "</td>";
                            echo "<td valign='top'>" . nl2br($contestant['full_name']) . "</td>";
                            echo "<td valign='top'>" . nl2br($contestant['points']) . "</td>";
                            echo "<td valign='top'><a class='btn-small btn btn-blue' >" . $statr[$contestant['status']] . "</a></td>";
                            echo "<td valign='top'>" . nl2br($contestant['win_date']) . "</td>";
                            echo "<td valign='top'>" . nl2br($contestant['ip_address']) . "</td>";
                            echo "<td valign='top'>" . nl2br($contestant['referred_by']) . "</td>";
                            echo "<td valign='top'>" . nl2br($contestant['created']) . "</td>";
                            echo "</tr>";
                        }
                        ?>
                    </table>
                </div>
            </div>
            <div class="grid_3">
                <div class="box first round">
                    <h2>EDIT CONTEST</h2>

                    <form action='' method='POST' enctype="multipart/form-data"> 
                        <p><b>Name: (hidden)</b><br /><input type='text' name='contest[name]' value='<?= stripslashes($contest['name']) ?>' /> </p>
                        <p><b>Contests Title:</b><br /><input type='text' name='contest[contests_name]' value='<?= stripslashes($contest['contests_name']) ?>' /> </p>
                        <p><b>Contests Description:</b><br /><input type='text' name='contest[contests_description]' value='<?= stripslashes($contest['contests_description']) ?>' /> </p>
                        <p>
                            <b>Contests Image:</b><br />
                            <input type="file" name="file" accept=".jpg, .jpeg" />
                            <br/><br/>
                            Or, select an existing image file from server, filename: 
                            <input type='text' name='contest[contests_image]' value='<?= stripslashes($contest['contests_image']) ?>' /> </p>
                        <i>preview:</i><br/>
                        <img width="200px;" src="https://www.boostpromotions.com/gowin/img/<?= stripslashes($contest['contests_image']) ?>" />
                        <p><b>Prize:</b><br /><input type='text' name='contest[prize]' value='<?= stripslashes($contest['prize']) ?>' /> </p>
                        <p><input class="btn btn-blue" type='submit' value='Update Info' name='update_contest' />
                            <input class="btn btn-small" onclick="return confirm('Proceed copying?')" type='submit' value='Copy Contest' name='copy_contest' />
                            <?php if (!$contestants): ?>
                                <input class="btn btn-red btn-small" onclick="return confirm('Delete?')" type='submit' value='Delete' name='delete_contest' />
                            <?php endif; ?>
                            <input type='hidden' value='<?= $contest['id'] ?>' name='contest[id]' /> </p>
                    </form> 
                    <p><b>Contest Link</b><br/><a target='_blank' href='https://www.boostpromotions.com/index.php?main_page=giveaway&id=<?= $contest['id'] ?>'>
                            https://www.boostpromotions.com/index.php?main_page=giveaway&id=<?= $contest['id'] ?></a> </p>
                </div>
                <div class="box round">
                    <h2>How to Enter</h2>     
                    <br/>
                    <a data-width='470' data-title="Ways to Enter" data-height='550' 
                       href="competitions_howto.php?contest_id=<?= $id; ?>" 
                       class="xframe btn btn-green" >
                        View Ways To Enter</a>
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
                var table = $('#example').DataTable();
            });
        </script>
    </body>
</html>
