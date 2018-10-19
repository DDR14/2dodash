<?php
session_start();
include "include/db.php";
$db = new db('boostpr1_boostpromotions');

require_once('inc.functions.php');
secure();

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
        </script>
    </head>
    <body>
        <div class="container_12">
            <?php
            require_once('inc.header.php');
            ?>
            <div class="grid_2">
                <div class="box sidemenu" style="height: 1061px;">
                    <div class="block" id="section-menu">
                        <ul class="section menu">
                            <li><a class="menuitem">COUPON ADMIN</a>
                                <ul class="submenu current">   
                                    <li><a href="coupon_admin.php">Coupons</a>
                                    </li>
                                    <li><a href="coupon_admin.php?display=REF">Referral Coupons</a>
                                    </li>
                                    <li><a href="coupon_admin.php?display=FIRST">-First Time Customer Coupons</a>
                                    </li>
                                    <li><a href="coupon_admin.php?display=OLD">-OLD Customer Coupons</a>
                                    </li>
                                </ul>
                            </li>
                        </ul>
                    </div>                
                </div>
            </div>
            <div class="grid_10">
                <div class="first grid">
                    <h2>
                        <?php
                        if (isset($_GET['display'])) {
                            $display = $_GET['display'];

                            switch ($display) {
                                case 'REF':
                                    echo "Referral Coupons";
                                    break;
                                case 'FIRST':
                                    echo "First Time Customer Coupons";
                                    break;
                                case 'OLD':
                                    echo "Old Customer Coupons";
                                    break;
                            }
                        } else {
                            $display = "";
                            echo "Coupons";
                        }
                        ?>
                        <a href='coupon_edit.php' class='btn btn-green'>Add New</a>
                    </h2>
                    <table class="data display dataTable" id="example">
                        <thead>
                            <tr> 
                                <td>Id</td> 
                                <td>Coupon Name</td>
                                <td>Coupon Amount</td> 
                                <td>Coupon Code</td> 
                                <td>Status</td>                            
                                <td>Starts</td> 
                                <td>Expires</td>
                            </tr> 
                        </thead>
                        <tbody>                            
                        </tbody>                       
                        <tfoot>
                            <tr> 
                                <td>Id</td> 
                                <td>Coupon Name</td>
                                <td>Coupon Amount</td> 
                                <td>Coupon Code</td> 
                                <td>Status</td>                            
                                <td>Starts</td> 
                                <td>Expires</td>
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
        <!-- script -->
        <script type="text/javascript">
            $(document).ready(function () {
                $('#example').dataTable({
                    "processing": true,
                    "serverSide": true,
                    "ajax": "inc.ajax_coupon.php?display=<?php echo $display; ?>",
                    "columnDefs": [{
                            "targets": 0,
                            "render": function (data) {
                                return '<a href="coupon_edit.php?id='
                                        + data + '"><button class="btn btn-small">View #'
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
