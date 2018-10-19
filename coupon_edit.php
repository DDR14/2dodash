<?php
session_start();

include "include/db.php";
$db = new db('boostpr1_boostpromotions');

$id = isset($_GET['id']) ? $_GET['id'] : 0;

if (isset($_POST['submitted'])) {
    if (!$_POST['Coupon']['coupon_code']) {
        $_POST['Coupon']['coupon_code'] = uniqid();
    }
    if ($_POST['submitted'] == 'Update') {
        $_POST['Coupon']['date_modified'] = date('Y-m-d H:i:s');

        $db->update('zen_coupons', $_POST['Coupon'], 'coupon_id = :id', ['id' => $id]);
        $db->update('zen_coupons_description', $_POST['CouponDesc'], 'coupon_id = :id', ['id' => $id]);
    } else {
        $_POST['Coupon']['date_created'] = date('Y-m-d H:i:s');
        $_POST['Coupon']['uses_per_user'] = 1;

        $id = $db->create('zen_coupons', $_POST['Coupon']);
        
        $_POST['CouponDesc']['coupon_id'] = $id;
        $db->create('zen_coupons_description', $_POST['CouponDesc']);

        header('Location: coupon_edit.php?id=' . $id);
    }
}
if (isset($_POST['delete_'])) {
    $db->delete('zen_coupons', 'coupon_id = :id', ['id' => $id]);
    $db->delete('zen_coupons_description', 'coupon_id = :id', ['id' => $id]);

    header('Location: coupon_admin.php');
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
                <?php
                $coupon = $db->find('first', 'zen_coupons a LEFT JOIN zen_coupons_description b ON a.coupon_id = b.coupon_id', 'a.coupon_id = :id', ['id' => $id], 'a.*, b.coupon_name, b.coupon_description');
                // for add new
                $update = true;
                $mode = 'EDIT COUPON';
                if (!$coupon) {
                    $coupon = [
                        'coupon_name' => '',
                        'coupon_description' => '',
                        'coupon_code' => '',
                        'coupon_amount' => '',
                        'uses_per_coupon' => 0,
                        'coupon_type' => '',
                        'free_shipping' => 0,
                        'loyal_customer' => 0,
                        'coupon_start_date' => date('Y-m-d'),
                        'coupon_expire_date' => date('Y-m-d', strtotime('+1 month'))
                    ];
                    $update = false;
                }
                ?>
                <h2><?= $update ? 'EDIT' : 'NEW'; ?> COUPON</h2>
                <div class=" box first">
                    <h2><?= $update ? 'EDIT COUPON # ' . $coupon['coupon_id'] : 'NEW COUPON'; ?></h2>
                    <form action='' method='POST'> 
                        <div class="grid_3" >
                            <p><b>Coupon Name:</b><br />
                                <input required type='text' value="<?= $coupon['coupon_name']; ?>" 
                                       name='CouponDesc[coupon_name]'/> 
                            </p>   
                            <p><b>Description:</b><br />
                                <textarea required rows='13' cols='30' name='CouponDesc[coupon_description]'><?= $coupon['coupon_description']; ?></textarea> 
                            </p>   
                        </div>
                        <div class="grid_4" >
                            <p><b>Code:</b><br />
                                <input type='text' value="<?= $coupon['coupon_code']; ?>" 
                                       name='Coupon[coupon_code]'/> * leave blank to generate code
                            </p>       
                            <p><b>Discount Type:</b><br/>
                                <select name='Coupon[coupon_type]' >
                                    <?php
                                    $opt_coupon_type = '<option value="P">Percentage</option><option value="F">Fixed</option>';
                                    echo str_replace('"' . $coupon['coupon_type'] . '"', '"' . $coupon['coupon_type'] . '" selected', $opt_coupon_type)
                                    ?>                                        
                                </select>
                            </p>
                            <p><b>Amount:</b><br /><input requried type='text' value="<?= $coupon['coupon_amount']; ?>"
                                                          name='Coupon[coupon_amount]'/> </p>
                            <p><b>Free Shipping:</b><br />
                                <select name='Coupon[free_shipping]' >
                                    <?php
                                    $opt_free_shipping = '<option value="0">No</option><option value="1">Yes</option>';
                                    echo str_replace('"' . $coupon['free_shipping'] . '"', '"' . $coupon['free_shipping'] . '" selected', $opt_free_shipping)
                                    ?>                                        
                                </select></p>
                            <p><b>Start Date:</b><br /><input type='text' id="range_example_2_start" value="<?= $coupon['coupon_start_date']; ?>" name='Coupon[coupon_start_date]'/> </p>
                            <p><b>Expire Date:</b><br /><input type='text' id="range_example_2_end" value="<?= $coupon['coupon_expire_date']; ?>" name='Coupon[coupon_expire_date]'/> </p>

                            <p><b>Uses Per Coupon</b><br />
                                <input type='number' 
                                       value="<?= $coupon['uses_per_coupon']; ?>" 
                                       name='Coupon[uses_per_coupon]'/> * The maximum number of times the coupon can be used, 0 if you want no limit. </p>
                            <p>
                                <label><b><input type='radio' name='Coupon[loyal_customer]' <?= !$coupon['loyal_customer'] ? 'checked' : ''; ?> value="0" />First Time Customer</b></label> * no orders made<br/>
                                <label><b><input type='radio' name='Coupon[loyal_customer]' <?= $coupon['loyal_customer'] ? 'checked' : ''; ?> value="1" />Old Customer</b></label> * last order older than 360 days
                            </p>

                            <p><b>Coupons can only be used once per user</b></p>
                            <br/>
                            <?php if ($update): ?>
                                <p><input type='submit' class="btn btn-blue" name='submitted' value='Update' />                                                                   
                                    <input type="submit" formnovalidate="" value="Delete" onclick="return confirm('Are you sure?')" name="delete_" class="btn btn-red"/>
                                </p>
                            <?php else: ?>
                                <p><input type='submit' class="btn btn-green" value='Add Row' />
                                    <input type='hidden' value='1' name='submitted' /> </p>
                            <?php endif; ?>
                        </div>
                    </form>

                    <div class="grid_4">
                        <div class="box first">
                            <div class="message info">
                                <h5>Discount Types:</h5>
                                <strong>[P] Percentage</strong> Will add a discount percentage of the subtotal. Amount field should be 1-100.
                                <br/><br/>
                                <strong>[F] Fixed</strong> Will subtract certain amount to subtract to order total. Amount field can be any $.
                            </div>            
                        </div>
                    </div>
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

