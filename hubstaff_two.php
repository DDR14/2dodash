<?php
session_start();
$hid = $_GET["hid"];
$timezone = "+08:00"; //$_GET["timezone"];
$uid = $_GET["uid"];
if ($timezone != "+08:00") {
    $timezone_setting = new DateTimeZone("America/Denver");
} else {
    $timezone_setting = new DateTimeZone('Asia/Singapore');
}

$read_only = isset($_GET['read_only']);

require_once('inc.functions.php');
connectToSQL();

require_once 'inc.hubstaff.php';
?>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<link rel="stylesheet" type="text/css" href="css/reset.css" media="screen" />
<link rel="stylesheet" type="text/css" href="css/text.css" media="screen" />
<link rel="stylesheet" type="text/css" href="css/layout.css" media="screen" />
<link href="css/vieworder.css" rel="stylesheet" type="text/css" />
<script src="js/jquery-1.6.4.min.js"></script>
<script src="js/jquery.lazyload.min.js"></script>
<script>
    $(function () {
        $("img.lazy").lazyload({
            container: '#hubstafftable'
        });
    });
</script>
<?php
$date = isset($_GET["selecteddate"]) ? $_GET["selecteddate"] : date('Ymd');

$selecteddate = new \DateTime($date);

$saturday = clone $selecteddate->modify("last Saturday");
$friday = clone $selecteddate->modify("this Friday")->modify("+8 day");

$prev = clone $selecteddate->modify("-1 week");
$next = clone $selecteddate->modify("+2 week");

$interval = new DateInterval('P1D');
$period = new DatePeriod($saturday, $interval, $friday);

$sqlbet = new \DateTime($date);
$sqlbet->modify("Friday this week")->modify("+8 day");

$qry = "SELECT SEC_TO_TIME(SUM(TIME_TO_SEC(total_hours))) AS wk_hours, "
        . "SEC_TO_TIME(SUM(TIME_TO_SEC(extra_hours))) AS mn_hours, "
        . "AVG(activity) AS wk_activity FROM staff_activities "
        . "WHERE (work_date BETWEEN '"
        . $saturday->format("Y-m-d") . "' AND '" . $sqlbet->format("Y-m-d") . "') AND hid=$hid";
$result = mysql_query($qry)or die(mysql_error());
while ($row = mysql_fetch_assoc($result)) {
    $week_hours = $row["wk_hours"];
    $week_activity = $row["wk_activity"];
    $manual_hours = $row['mn_hours'];
}
?>
<style>
    table.table td{border-right:1px solid #D2D2D2;border-bottom:1px solid #D2D2D2;}
    table.table, h5{margin-bottom: 0;}
    .activity-display{width:100%;}
    .responsive{max-width: 140px;display:block;margin:0 auto;}
    #hubstafftable.table a{font-weight:normal;}
    .table{border-top-style: none;border-right-style: none; table-layout: fixed;}
    textarea, .textarea{width:100%;font-size:10px;
                        background-color: transparent;
                        background-image: 
                            linear-gradient(#eee .1em, transparent .1em);
                        background-size: 100% 11px;
    }
    <?php
    $dayno = new DateTime(null, $timezone_setting);
    if (($dayno > $saturday) && ($dayno < $friday)) {
        echo ".table tr td:nth-child(" . ($dayno->format("w") + 3 ) . ") { background: #FFFFCC; }";
    }
    ?>    
</style>
<div style="background-color: #DFE3E7;border-bottom: 1px solid #A5ACB5;padding: 5px;" colspan="9">
    <div class="floatleft">        
        <a class="btn-mini btn-black btn-arrow-left" href="hubstaff_two.php?uid=<?php echo $uid . "&hid=" . $hid . "&selecteddate=" . $prev->format("Ymd"); ?>" 
           ><span></span> Previous</a>        
        <a class="btn-mini btn-black btn-arrow-right" href="hubstaff_two.php?uid=<?php echo $uid . "&hid=" . $hid . "&selecteddate=" . $next->format("Ymd"); ?>" 
           ><span></span>Next</a>
        <a class="btn-small btn btn-black" style="overflow:hidden" href="hubstaff.php?uid=<?php echo $uid . "&hid=" . $hid; ?>" 
           >Today</a>

    </div>    
    <div class="floatright">
        <small>ACTIVITY:</small><h5><center><?php echo round($week_activity); ?>%</center></h5>
    </div>
    <div class="floatright" style="margin-right:15px;">
        <small>MANUAL HOURS:</small><h5><?php echo $manual_hours; ?></h5>
    </div>
    <div class="floatright" style="margin-right:15px;">
        <small>TOTAL HOURS:</small><h5><?php echo $week_hours; ?></h5>
    </div>
    <div style="text-align:center">
        <h1 style="font-size:20px"><?php echo $saturday->format("M d") . " — " . $sqlbet->format("M d, Y"); ?></h1>
    </div>    
    <div style="clear:both"></div>
</div>
<?php
$qry = "SELECT recurring, day_no FROM staff_recurring WHERE hid=$hid";
$result = mysql_query($qry)or die(mysql_error());
$recurring = array();
while ($row = mysql_fetch_array($result)) {
    $recurring[$row['day_no']] = $row['recurring'];
}
?>
<table cellpadding="0" cellspacing="0" width="100%" class="table">           
    <form method="post">
        <thead> 
            <tr>
                <th width="10px"></th>
                <?php
                //Generate Header
                foreach ($period as $dt) {
                    ?>
                    <th>
                        <?php
                        $day_no = $dt->format("w");
                        echo $dt->format("D m/d");
                        if ($day_no === '0') {
                            echo "<br/><input class='btn btn-purple btn-small' value='Update Recurring' type='submit'/>";
                        } else if ($day_no !== '6') {
                            echo "<textarea rows='7' name='recurring[" . $dt->format("w") . "]'>" .
                            (isset($recurring[$day_no]) ? $recurring[$day_no] : '') . "</textarea>";
                        }
                        ?>
                    </th>
                    <?php
                }
                ?>     
                <th width="13px"></th>
            </tr>
        </thead>
    </form>
    <tr>
        <th width="10px"></th>
        <?php
        $qry = "SELECT * FROM staff_activities WHERE (work_date BETWEEN '"
                . $saturday->format("Y-m-d") . "' AND '" . $sqlbet->format("Y-m-d") . "') AND hid=$hid";
        $result = mysql_query($qry)or die(mysql_error());
        $data = array();
        while ($row = mysql_fetch_array($result)) {
            $data[] = $row;
        }
        //REPORTING
        foreach ($period as $dt) {
            $comp = $dt->format("Y-m-d");
            ?><td style='text-align:center;'><?php
                foreach ($data as $row) {
                    if ($row["work_date"] == $comp) {
                        ?>
                        <div class="activity-display">
                            <?php if (!$read_only) { ?>
                                <form action="" method="post" >
                                <?php } ?>
                                <input type="hidden" name="work_date" value="<?php echo $row["work_date"]; ?>" />
                                TASKS:
                                <textarea rows="16" name="tasks"><?php echo $row["tasks"]; ?></textarea>
                                UPCOMING:
                                <textarea rows="16" name="pending"><?php echo $row["pending"]; ?></textarea>
                                MANUAL TIME:                                    
                                <input class='textarea' type='text' name='extra_hours' size="6" value='<?php echo $row['extra_hours']; ?>' />
                                <textarea name="notes" rows="4"><?php echo $row["notes"]; ?></textarea>
                                <?php if (!$read_only) { ?>
                                    <input class="btn btn-blue" type="submit" value="Update" />
                                </form>
                            <?php } ?>
                        </div>
                        <?php
                    }
                }
                ?></td><?php
        }
        ?>     
        <th width="13px"></th>
    </tr>

</table>
<?php if (!$read_only) { ?>
    <table cellpadding="0" cellspacing="0" width="100%" class="table">
        <thead>
            <tr>
                <th width="10px"></th>
                <?php
                //Generate Header
                foreach ($period as $dt) {
                    echo "<th>" . $dt->format("D m/d") . "</th>";
                }
                ?>     
                <th width="13px"></th>
            </tr>
            <tr>
                <th width="10px"></th>
                <?php
                //Generate Header
                foreach ($period as $dt) {
                    $comp = $dt->format("Y-m-d");
                    echo "<td style='text-align:center;'>";
                    foreach ($data as $row) {
                        if ($row["work_date"] == $comp) {
                            ?>
                    <small>Total hours:</small><h5><?php echo $row["total_hours"] ?></h5>
                    <small>Activity:</small>
                    <h5><?php echo $row["activity"]; ?>%</h5>
                    <?php
                }
            }
            echo "</td>";
        }
        ?>
        <th></th>
    </tr>
    <tr>
        <th width="10px"></th>
        <?php
        //Generate Forms
        foreach ($period as $dt) {
            ?>
            <td style="text-align:center;">
                <form method="post" action="">
                    <input type="hidden" name="work_date" value="<?php echo $dt->format("Y-m-d"); ?>" />
                    <input type="hidden" name="users" value="<?php echo $hid; ?>" />
                    <small>Generate</small>
                    <input type="submit" class="btn btn-teal" name="generate" value="Activities" />
                    <input type="submit" class="btn btn-orange btn-small" name="screenshots" value="Screenshots" />
                </form>
            </td>
            <?php
        }
        ?>
    </tr>
    </thead>
    </table>
    <?php
}