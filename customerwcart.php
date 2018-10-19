<?php
session_start();
require_once('inc.functions.php');
if (isset($_POST['ExportCsv'])) {
    company_db_connect(1);
    $result = mysql_query(
            "SELECT a.customers_id,b.entry_company, b.entry_lastname, b.entry_firstname, b.entry_city,IFNULL(d.zone_name,b.entry_state) AS state,c.countries_name,a.customers_email_address,a.customers_telephone 
FROM zen_customers AS a 
INNER JOIN zen_address_book b 
ON b.address_book_id = a.customers_default_address_id 
INNER JOIN zen_countries c 
ON b.entry_country_id = c.countries_id 
LEFT JOIN zen_zones d 
ON d.zone_id = b.entry_zone_id ");
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
        header('Content-Disposition: attachment; filename="all_customers.csv"');
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

        <?php
        require_once('inc.functions.php');
        connectToSQL();
        secure();
        ?>
        <div class="container_12">
            <?php
            require_once('inc.header.php');
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
                    <h2>Customers with items in cart</h2>
                    <p class="message info">Modify Customer Info or Create Orders for Customers</p>
                    <table class="data display dataTable" id="example">
                        <thead>
                            <tr>
                                <th>Customer ID</th>
                                <th>Company</th>
                                <th>Last Name</th>
                                <th>First Name</th>
                                <th>City</th>
                                <th>State</th>
                                <th>Country</th>
                                <th>Last Cart Update</th>
                                <th>Cart Item Count</th>
                            </tr>
                        </thead>                             
                        <tbody>
                        </tbody>
                        <tfoot>
                            <tr>
                                <th>Customer ID</th>
                                <th>Company</th>
                                <th>Last Name</th>
                                <th>First Name</th>
                                <th>City</th>
                                <th>State</th>
                                <th>Country</th>
                                <th>Last Cart Update</th>
                                <th>Cart Item Count</th>
                            </tr>
                        </tfoot>
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
        <script type="text/javascript">
            $(document).ready(function () {
                $('#example').dataTable({
                    "processing": true,
                    "serverSide": true,
                    "ajax": "inc.ajax_customerwcart.php",
                    "columnDefs": [{
                            "targets": 0,
                            "render": function (data) {
                                return '<a href="viewcustomer.php?customerid='
                                        + data + '&amp;companyid=1"><button class="btn btn-small btn-grey">View #'
                                        + data + '</button></a>';
                            }
                        }],
                    "order": [[0, "desc"]],
                    "aaSorting": [],
                    //"stateSave": true,
                    "iDisplayLength": 25
                });
                $('#example').on('draw.dt', function () {
                    setSidebarHeight();
                });
            });
        </script>
    </body>
</html>

