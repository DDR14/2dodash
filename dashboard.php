<?php
session_start();
require_once('inc.functions.php');
secure();
if (isset($_POST['ExportCsv'])) {
    company_db_connect(1);
    $result = mysql_query(
            'SELECT customers_state, customers_street_address, date_purchased, customers_telephone, customers_name, customers_email_address, customers_company, order_total, orders_id, ship_by  FROM `zen_orders`');
    if (!$result)
        die('Couldn\'t fetch records');
    $num_fields = mysql_num_fields($result);
    $headers = array();
    for ($i = 0; $i < $num_fields; $i++) {
        $headers[] = mysql_field_name($result, $i);
    }
    $fp = fopen('php://output', 'w');
    if ($fp && $result) {
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="all_orders.csv"');
        header('Pragma: no-cache');
        header('Expires: 0');
        fputcsv($fp, $headers);
        while ($row = mysql_fetch_row($result)) {
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
        <link href="css/jquery-ui.min.css" rel="stylesheet" type="text/css" />
        <link rel="stylesheet" type="text/css" href="css/text.css" media="screen" />
        <link rel="stylesheet" type="text/css" href="css/grid.css" media="screen" />
        <link rel="stylesheet" type="text/css" href="css/layout.css" media="screen" />
        <link rel="stylesheet" type="text/css" href="css/nav.css" media="screen" />
        <!--[if IE 6]><link rel="stylesheet" type="text/css" href="css/ie6.css" media="screen" /><![endif]-->
        <!--[if IE 7]><link rel="stylesheet" type="text/css" href="css/ie.css" media="screen" /><![endif]-->
        <link href="css/table/demo_page.css" rel="stylesheet" type="text/css" />

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
            connectToSQL();
            $qry = "SELECT * FROM `users` WHERE `f_name`='0' AND `l_name`='0' AND `id`='" . $_COOKIE['userid'] . "'";
            $result = mysql_query($qry)or die(mysql_error());

            if (mysql_num_rows($result) > 0) {
                header('Location: update_name.php');
                exit();
            }
            ?>
            <div class="grid_2">
                <div class="box sidemenu">
                    <?php
                    require_once 'inc.sidebar.php';
                    ?>
                </div>
            </div>
            <div class="grid_10">
                <div class="first grid">
                    <h2>
                        <?php
                        $display = isset($_GET['display']) ? $_GET['display'] : false;

                        switch ($display) {
                            case 'EVERYTHING':
                                ?>
                                <form method='post'>All Orders 
                                    <input type=submit name=ExportCsv value='Export to CSV' />
                                    <a href="dashboard.php?display=ALL_BOOST" class="btn btn-blue btn-small">BoostPromotions</a>
                                    <a href="dashboard.php?display=ALL_CTR"class="btn btn-teal btn-small">CTR Tags</a>
                                    <a href="dashboard.php?display=ALL_ISMILE"class="btn btn-small btn-yellow">ISmile</a>
                                    <a href="dashboard.php?display=ALL_YBA"class="btn btn-purple btn-small">YouthBowlingAwards</a>                            
                                </form>
                                <?php
                                break;
                            case 'ALL_BOOST': echo "All Orders From BoostPromotions.com";
                                break;
                            case 'ALL_CTR': echo "All Orders From CtrTags.com";
                                break;
                            case 'ALL_ISMILE': echo "All Orders From ISmile.com";
                                break;
                            case 'ALL_YBA': echo "All Orders From YouthBowlingAwards.com";
                                break;
                            case 'ALLSHIPPED':
                                ?>
                                ALL Shipped Orders
                                <input placeholder="Start Date" type="text" id="startdate" size="7" class="pick_date" /> -
                                <input placeholder="End Date" type="text" id="enddate" size="7" class="pick_date"/>
                                <?php
                                break;
                            case 'PING': echo "Pending Proofs";
                                break;
                            case 'PINGNR': echo "Pending Proofs - No Response";
                                break;
                            case 'PONG':echo "Graphics";
                                break;
                            case 'PRINT':echo "Printing";
                                break;
                            case 'INVOICE':echo "Pending Payment";
                                break;
                            case 'INVOICENR':echo "Pending Payment - No Reply";
                                break;
                            case 'LAMINATE':echo "Laminating";
                                break;
                            case 'CUT':echo "Cutting";
                                break;
                            case 'COUNT':echo 'Counting';
                                break;
                            case 'SHIP':echo "Shipping";
                                break;
                            case 'NET30':echo "Purchase Orders";
                                break;
                            case 'REJECT':echo "Rejected Proofs";
                                break;
                            case 'PENPO':echo "Pending PO";
                                break;
                            case 'LANYARD':echo "Lanyards to Order";
                                break;
                            case 'LANYARDSHIP':echo "Lanyards to Ship";
                                break;
                            case 'KEYFOBSHIP':echo "Key Fobs to Ship";
                                break;
                            case 'INHOUSE':echo "In House Graphics";
                                break;
                            case 'PENCHECK':echo "Pending Checks";
                                break;
                            case 'SHIPBY':echo "Ship By";
                                break;
                            case 'BACKORDER':echo "Back Orders";
                                break;
                            case "QUANUPDATE":echo "Quantity Update";
                                break;
                            default:echo "Welcome to the dashboard";
                                break;
                        }
                        ?>
                    </h2>

                    <?php
//here we will display all orders unless they have selected an order - which then we will display information on that order.
                    ?>
                    <?php
                    connectToSQL();
                    if ($display) {
                        ?>
                        <table class="data display dataTable" id="example">
                            <thead>
                                <tr>
                                    <th>Order ID</th>
                                    <th><?= ($display == 'ALLSHIPPED') ? 'Date Shipped' : 'Ship by'; ?></th>
                                    <th>Order Amount</th>
                                    <th>Buying Company</th>
                                    <th>Email Address</th>
                                    <th>Customer</th>
                                    <th>Phone Number</th>
                                    <th>Date</th>
                                </tr>
                            </thead>
                            <tfoot><tr>
                                    <th>Order ID</th>
                                    <th>Ship by</th>
                                    <th>Order Amount</th>
                                    <th>Buying Company</th>
                                    <th>Email Address</th>
                                    <th>Customer</th>
                                    <th>Phone Number</th>
                                    <th>Date</th>
                                </tr></tfoot>                                
                            <tbody>
                                <?php
                                if ($printBoost == 1) {
                                    printBoostOrders($display);
                                }
                                ?>
                            </tbody>
                        </table>
                        <?php
                    } else {
                        ?>
                        <table> 
                            <tr>
                                <td><b>Quicklinks:</b></td><td>&nbsp;</td>
                                <td> <form action="customers.php" method="get" /><input type="submit" class="btn-green btn" name="display" value="Create Order" /></form></td>
                                <td>&nbsp;</td><td> <form action="invoice/custom.php" method="POST" /><input type="submit" class="btn btn-grey" name="submit" value="Custom Invoice" /></form></td>
                                <td>&nbsp;</td><td> <form action="autoprintnew.php" method="POST" /><input type="submit" class="btn btn-purple" name="submit" value="Auto Print" /></form></td>
                            </tr>
                        </table>
                        <div class="box round">
                            <h2>Latest Customer Comments</h2>
                            <div class="block">
                                <table class="data display dataTable" id="example">
                                    <thead>
                                        <tr>
                                            <th>Order ID</th>
                                            <th>Comments</th>
                                        </tr>
                                    </thead>
                                    <tfoot><tr>
                                            <th>Order ID</th>
                                            <th>Comments</th>

                                        </tr>
                                    </tfoot>                                
                                    <tbody>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <?php
                    }
//mysql_close();
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

        <!-- BAHA this is great -->
        <?php
        $ajaxmode = ["EVERYTHING", "ALLSHIPPED", "CANCELLED", "ALL_BOOST", "ALL_CTR", "ALL_ISMILE", "ALL_YBA", false];
        ?>
        <!-- end greatness -->
        <script type="text/javascript">
            $(document).ready(function () {
            var table = $('#example').DataTable({
<?php if (in_array($display, $ajaxmode)) { ?>
                "processing": true,
                        "serverSide": true,
                        "ajax": {
                        'url': "inc.ajax_orders.php",
                                "data": function (d) {
                                d.display = "<?= $display; ?>";
                                d.startdate = $('#startdate').val();
                                d.enddate = $('#enddate').val();
                                }
                        },
                        "columnDefs": [{
                        "targets": 0,
                                "render": function (data) {
                                return '<a href="vieworder.php?display=<?= $display ? $display : 'false'; ?>&orderid='
                                        + data + '&amp;companyid=1"><button class="btn btn-small btn-teal">View #'
                                        + data + '</button></a>';
                                }
                        }],
                        "order": [[ 0, "desc" ]],
<?php } ?>
            "aaSorting": [],
                    //"stateSave": true,
                    "iDisplayLength": 25
            });
<?php
if (in_array($display, $ajaxmode)) {
    if ($display == 'ALLSHIPPED') {
        ?>
                    setDatePickerOrder('pick_date');
                    $('#startdate, #enddate').on('change', function() {
                    table.draw();
                    });
    <?php } ?>
                $('#example').on('draw.dt', function () {
                setSidebarHeight();
                });
<?php } else { ?>
                setSidebarHeight();
<?php } ?>
            });
        </script>
    </body>
</html>

