<?php
session_start();

include "include/db.php";
$db = new db('boostpr1_boostpromotions');

$id = isset($_GET['id']) ? $_GET['id'] : 0;

if (isset($_POST['submitted'])) {
    if (!$_POST['CouponDesc']['design_id']) {
        $_POST['CouponDesc']['design_id'] = uniqid();
    }
    if ($_POST['submitted'] == 'Update') {
        $_POST['Coupon']['modified'] = date('Y-m-d H:i:s');

        $db->update('zen_discounted_designs', $_POST['Coupon'], 'design_id = :id', ['id' => $id]);
    }
    header('Location: discounted_designs.php');
}
if (isset($_POST['delete_'])) {
    $db->delete('zen_discounted_designs', 'design_id = :id', ['id' => $id]);

    header('Location: discounted_designs.php');
}
if(isset($_POST['cancel'])){
    header('Location: discounted_designs.php');
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
        <link href="css/jquery-ui.min.css" rel="stylesheet" type="text/css" />
        <link href="css/table/demo_page.css" rel="stylesheet" type="text/css" />
        <!-- BEGIN: load jquery -->
        <script src="js/jquery-1.6.4.min.js" type="text/javascript"></script>
        <script src="js/jquery-ui/jquery-ui.min.js" type="text/javascript"></script>
        <script type="text/javascript" src="js/jquery-ui/timepicker.min.js"></script>
        <!-- END: load jquery -->
        <script src="js/setup.js" type="text/javascript"></script>   
        <script type="text/javascript">
            $(document).ready(function () {
                setDatePickerOrder('pick_date');
                setSidebarHeight();

                var startDateTextBox = $('#range_example_2_start');
                var endDateTextBox = $('#range_example_2_end');

                $.timepicker.datetimeRange(
                        startDateTextBox,
                        endDateTextBox,
                        {
                            minInterval: (1000 * 60), // 1hr
                            dateFormat: 'yy-mm-dd',
                            timeFormat: 'HH:mm:ss'
                        }
                );
            });
        </script>
    </head>
    <body>

        <?php
        require_once('inc.functions.php');
//        secure();
        ?>
        <div class="container_12">
            <?php
            require_once('inc.header.php');
            ?>  
            <div class="grid_10">
                <?php
                $coupon = $db->find('first', 'zen_designs a INNER JOIN zen_discounted_designs b ON a.products_id = b.design_id', 'a.products_id = :id', ['id' => $id], 'a.*,b.* ,a.design_name');
                // for add new
                $update = true;
                
                ?>
                <h2>EDIT DISCOUNTED DESIGN INFORMATION</h2>
                <div class=" box first">
                    <h2><?= 'EDIT DISCOUNTED DESIGN # ' . $coupon['products_id']; ?></h2>
                    <form action='' method='POST'> 
                        <div class="grid_3" >
                            <p><b>Design Name:</b><br />
                                <input required type='text' value="<?= $coupon['design_name']; ?>" 
                                       name='CouponDesc[design_name]' readonly/> 
                            </p>   
                            <p><b>Model Number:</b><br />
                                <input required type='text' value="<?= $coupon['products_model']; ?>" 
                                       name='CouponDesc[products_model]' readonly/>  
                            </p>
                            <p><b>Price Discount:</b><br/>
                                <input type='text' value="<?= $coupon['dd_new_products_price']; ?>" 
                                       name='Coupon[dd_new_products_price]'/> 
                            </p>       
                        </div>
                        <div class="grid_4" >
                            
                            <p><b>Special Date Available:</b><br /><input type='text' id="range_example_2_start" value="<?= $coupon['specials_date_available']; ?>" name='Coupon[specials_date_available]'/> </p>
                            <p><b>Expiration Date:</b><br /><input type='text' id="range_example_2_end" value="<?= $coupon['expires_date']; ?>" name='Coupon[expires_date]'/> </p>
                            <p><b>Number of Customers Avail:</b><br />
                                <input type='text' value="<?= $coupon['dd_uses']; ?>" 
                                       name='Coupon[dd_uses]' readonly/> </p>
                            <?php if ($update): ?>
                                <p><input type='submit' class="btn btn-blue" name='submitted' value='Update' />                                                                   
                                    <input type="submit" formnovalidate="" value="Delete" onclick="return confirm('Are you sure?')" name="delete_" class="btn btn-red"/>

                                    <input type='submit' class="btn btn-green" name='cancel' value='Cancel' /> 
                                </p>    
                            <?php endif; ?>
                        </div>
                    </form>
                    <div class="clear"></div>
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

