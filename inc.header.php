<div class="grid_12 header-repeat">
    <div id="branding">
        <div class="floatleft">
            <font color="white"><h1><img src="<?php echo '//' . $_SERVER['HTTP_HOST'] . "/"; ?>img/logo.png" alt="Logo" />2<span style="font-weight: normal">Do</span>Dash:]</h1></font></div>
        <div class="floatright">
            <div class="floatleft">
                <?php
                //here we need to display their name and a logout button.
                $level = 0;
                connectToSQL();
                if (isset($_COOKIE['userid'])) {
                    $qry = "SELECT * FROM users WHERE id='" . $_COOKIE['userid'] . "'";
                    $result = mysql_query($qry)or die(mysql_error());
                    while ($row = mysql_fetch_assoc($result)) {
                        $username = $row['username'];
                        $level = $row['level'];
                        $company = $row['company'];
                        $icon = $row['avatar'];
                    }
                } else {
                    ?>
                    <script type="text/javascript">
                        window.location = "index.php";
                    </script>
                    <?php
                    die('You are no longer authenticated. Redirecting to login page...');
                }
                ?>
                <img src="https://api.adorable.io/avatars/40/<?= $_COOKIE['userid']; ?>.png" alt="Profile Pic" /></div>
            <div class="floatleft marginleft10">
                <ul class="inline-ul floatleft">
                    <li><?php echo "Hello, " . $username; ?></li>
                    <li><a href="logout.php">Logout</a></li>
                </ul>
                <br />
                <?php
                //here we can display what company's they are looking at.
                $qry = "SELECT * FROM company_cross WHERE u_id='" . $_COOKIE['userid'] . "'";
                $result = mysql_query($qry)or die(mysql_error());
                $companies = "";
                $printBoost = 0;
                while ($row = mysql_fetch_assoc($result)) {
                    //should show us every company they are connected to.  We need to pull these companies from the companies table and set them to a var
                    $c_id = $row['c_id'];

                    //set flags to print orders from different companies.
                    $printBoost = 1;

                    $qry2 = "SELECT name FROM companies WHERE id='" . $c_id . "'";

                    $result2 = mysql_query($qry2)or die(mysql_error());

                    while ($row2 = mysql_fetch_assoc($result2)) {
                        $name = $row2['name'];
                    }

                    $companies .= "Boost Promotions, ";
                }
                ?>
                <span class="small grey">Companies: <?php echo $companies; ?></span>
            </div>
        </div>
        <div class="clear">
        </div>
    </div>
</div>
<div class="clear"></div>
<div class="grid_12">
    <ul class="nav main">
        <li class="ic-dashboard"><a href="<?php echo '//' . $_SERVER['HTTP_HOST'] . "/"; ?>dashboard.php"><span>BoostPromotions</span></a> </li>
        <li class="ic-notifications"><a href="javascript:"><span>Time Clock</span></a>
            <ul>
                <li><a href="<?php echo '//' . $_SERVER['HTTP_HOST'] . "/"; ?>time_clock.php">Employee</a></li>
                <?php
                if ($level == 1 || $level == 2) {
                    ?>
                    <li><a href="<?php echo '//' . $_SERVER['HTTP_HOST'] . "/"; ?>time_tracker.php?supervisor=1">Supervisor</a></li>
                <?php } ?>
            </ul>
        </li>
        <li class="ic-grid-tables">
            <a href="javascript:"><span>Meristone</span></a>
            <ul>
                <li><a href="<?php echo '//' . $_SERVER['HTTP_HOST'] . "/"; ?>tasks.php">
                        <span>Task Management</span></a></li>
                <li><a href="<?php echo '//' . $_SERVER['HTTP_HOST'] . "/"; ?>staff.php">
                        <span>My Report</span></a></li>
                <li><a href="<?php echo '//' . $_SERVER['HTTP_HOST'] . "/"; ?>team.php">
                        <span>Team Report</span></a></li>
                <li><a href="<?php echo '//' . $_SERVER['HTTP_HOST'] . "/"; ?>staff_day.php">
                        Team Day Report</a></li>
            </ul>
        </li>   
        <?php
        if ($level == 1 || $level == 2) {
            ?>
            <li class="ic-charts dd"><a href="#"><span>Reports</span></a>                
                <ul>
                    <li><a href="<?php echo '//' . $_SERVER['HTTP_HOST'] . "/"; ?>product_sales.php" >Product Sales</a></li>
                    <li><a href="<?php echo '//' . $_SERVER['HTTP_HOST'] . "/"; ?>task_tracker.php" >Task Tracking Report</a></li>
                </ul>
            </li>
        <?php } ?>
        <li class="ic-gallery dd"><a href="javascript:"><span>Products</span></a>
            <ul>       
                <li><a href="<?php echo '//' . $_SERVER['HTTP_HOST'] . "/"; ?>ncatalog.php">Call to Order</a> </li>
                <li><a href="<?php echo '//' . $_SERVER['HTTP_HOST'] . "/"; ?>catalog.php">Catalog</a> </li>                
                <li><a href="<?php echo '//' . $_SERVER['HTTP_HOST'] . "/"; ?>designs.php">Design Library</a></li>
                <?php
                if ($level == 1 || $level == 2) {
                    ?>
                    <li><a href="<?php echo '//' . $_SERVER['HTTP_HOST'] . "/"; ?>categories.php">Categories</a></li>
                    <li><a href="<?php echo '//' . $_SERVER['HTTP_HOST'] . "/"; ?>proof_settings_list.php">Proof Settings</a></li>
                <?php } ?>
                <li><a href="<?php echo '//' . $_SERVER['HTTP_HOST'] . "/"; ?>tool_tk_helper.php">TK Helper</a></li>
                <li><a href="<?php echo '//' . $_SERVER['HTTP_HOST'] . "/"; ?>tool_stock_helper.php">Stock Tags Helper</a></li>
            </ul>
        </li> 
        <li class="ic-typography dd"><a href="javascript:"><span>Discounts</span></a>
            <ul>       
                <li><a href="<?php echo '//' . $_SERVER['HTTP_HOST'] . "/"; ?>coupon_admin.php">Coupon Admin</a> </li>
                <li><a href="<?php echo '//' . $_SERVER['HTTP_HOST'] . "/"; ?>discounted_designs.php">Discounted Designs</a> </li>          
                <?php
                if ($level == 1 || $level == 2) {
                    ?>
                    <li><a href="<?php echo '//' . $_SERVER['HTTP_HOST'] . "/"; ?>discount_qty.php">Discount Quantity</a></li>
                <?php } ?>
            </ul>
        </li>

        <?php
        if ($level == 1) {
            ?>
            <li class="ic-form-style"><a href="javascript:"><span>Admin</span></a>
                <ul>
                    <li><a href="<?php echo '//' . $_SERVER['HTTP_HOST'] . "/"; ?>admin.php">Users</a></li>                    
                    <li><a href="<?php echo '//' . $_SERVER['HTTP_HOST'] . "/"; ?>websites.php">Websites</a></li>   
                    <li><a href="<?php echo '//' . $_SERVER['HTTP_HOST'] . "/"; ?>free_samples.php">Free Sample Requests</a></li>  
                    <li><a href="<?php echo '//' . $_SERVER['HTTP_HOST'] . "/"; ?>competitions.php">Contests</a></li>  
                    <li><a href="<?php echo '//' . $_SERVER['HTTP_HOST'] . "/"; ?>terms_editor.php">Terms and Conditions Editor</a></li>
                </ul>
            </li>
            <?php
        }
        ?>
        <li style="float:right;" class="ic-gallery">
            <a href="<?php echo '//' . $_SERVER['HTTP_HOST'] . "/"; ?>uchallenge/index.php">
                <span>Unplugged Challenge</span></a>
        </li>
        <li style="float:right;" class="ic-gallery">
            <a href="<?php echo '//' . $_SERVER['HTTP_HOST'] . "/"; ?>bt/index.php">
                <span>BoosterTags</span></a>
        </li>
    </ul>
</div>
<div class="clear"></div>