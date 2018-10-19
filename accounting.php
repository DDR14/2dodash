<?php
session_start();
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
    <script type="text/javascript" src="js/jquery-ui/jquery.ui.core.min.js"></script>
    <script src="js/jquery-ui/jquery.ui.widget.min.js" type="text/javascript"></script>
    <script src="js/jquery-ui/jquery.ui.accordion.min.js" type="text/javascript"></script>
    <script src="js/jquery-ui/jquery.effects.core.min.js" type="text/javascript"></script>
    <script src="js/jquery-ui/jquery.effects.slide.min.js" type="text/javascript"></script>
    <script src="js/jquery-ui/jquery.ui.mouse.min.js" type="text/javascript"></script>
    <script src="js/jquery-ui/jquery.ui.sortable.min.js" type="text/javascript"></script>
    <script src="js/table/jquery.dataTables.min.js" type="text/javascript"></script>
    <!-- END: load jquery -->
    <script type="text/javascript" src="js/table/table.js"></script>
    <script src="js/setup.js" type="text/javascript"></script>
    <script type="text/javascript">

        $(document).ready(function () {
            setupLeftMenu();

            $('.datatable').dataTable();
			setSidebarHeight();


        });
    </script>
</head>
<body>

	<?php
	//make sure they are logged in and activated.
	require_once('inc.functions.php');
	connectToSQL();
	secure();
	mysql_close();
	
	?>



    <div class="container_12">
        <div class="grid_12 header-repeat">
            <div id="branding">
                <div class="floatleft">
                    <!--<img src="img/logo.png" alt="Logo" />--><font color="white"><h1>ToDo:]</h1></font></div>
                <div class="floatright">
                    <div class="floatleft">
						
						<?php
						    //here we need to display their name and a logout button.
							
							connectToSQL();
							$qry = "SELECT * FROM `users` WHERE `id`='" . $_COOKIE['userid'] . "'";
							$result = mysql_query($qry)or die(mysql_error());
							while ($row = mysql_fetch_assoc($result)){
								$username = $row['username'];
								$level = $row['level'];
								$icon = $row['avatar'] ;
							}
							mysql_close();
						?>
						<img src="<?php echo $icon ; ?>" alt="Profile Pic" /></div>
							<div class="floatleft marginleft10">
                        <ul class="inline-ul floatleft">
                            <li><?php echo "Hello, " . $username ; ?></li>
                            <li><a href="logout.php">Logout</a></li>
                        </ul>
                        <br />
						<?php
						connectToSQL();
						//here we can display what company's they are looking at.
						$qry = "SELECT * FROM `company_cross` WHERE `u_id`='" . $_COOKIE['userid'] . "'";
						$result = mysql_query($qry)or die(mysql_error());
						$companies = "";
						while ($row = mysql_fetch_assoc($result)){
							//should show us every company they are connected to.  We need to pull these companies from the companies table and set them to a var
							$c_id = $row['c_id'];
							
							//set flags to print orders from different companies.
							//echo $c_id ;
							if ($c_id == 1){
								$printBoost = 1 ;
								//echo "pringBoost is set to " . $printBoost ;
							}
							
							$qry2 = "SELECT `name` FROM `companies` WHERE `id`='" . $c_id . "'";
							
							$result2 = mysql_query($qry2)or die(mysql_error());
							
							while ($row2 = mysql_fetch_assoc($result2)){
								$name = $row2['name'];
							}
							
							$companies .= $name . ", ";
							
						}
						mysql_close();
						?>
                        <span class="small grey">Companies: <?php echo $companies ; ?></span>
                    </div>
                </div>
                <div class="clear">
                </div>
            </div>
        </div>
        <div class="clear">
        </div>
        <div class="grid_12">
            <ul class="nav main">
                <li class="ic-dashboard"><a href="dashboard.php"><span>Dashboard</span></a> </li>
                <?php
				/*
				<li class="ic-form-style"><a href="javascript:"><span>Controls</span></a>
                    <ul>
                        <li><a href="form-controls.html">Forms</a> </li>
                        <li><a href="buttons.html">Buttons</a> </li>
                        <li><a href="form-controls.html">Full Page Example</a> </li>
                        <li><a href="table.html">Page with Sidebar Example</a> </li>
                    </ul>
                </li>
				<li class="ic-typography"><a href="typography.html"><span>Typography</span></a></li>
				*/
				?>
				<li class="ic-charts"><a href="timeclock.php"><span>Time Clock</span></a></li>
				<?php
				/*
                <li class="ic-grid-tables"><a href="table.html"><span>Data Table</span></a></li>
                <li class="ic-gallery dd"><a href="javascript:"><span>Image Galleries</span></a>
               		 <ul>
                        <li><a href="image-gallery.html">Pretty Photo</a> </li>
                        <li><a href="gallery-with-filter.html">Gallery with Filter</a> </li>
                    </ul>
                </li>
				*/
				?>
				<?php
				if ($level == 1 || $level == 2){
					?>
					<li class="ic-grid-tables"><a href="accounting.php"><span>Accounting</span></a></li>
					
					<li><a href="inventory.php"><span>Inventory</span></a></li>
				<?php
				}
				?>
                <li class="ic-notifications"><a href="notifications.php"><span>Notifications</span></a></li>
				<?php
				if ($level == 1){
					?>
					<li class="ic-form-style"><a href="admin.php"><span>Admin</span></a></li>
				<?php
				}
				?>

            </ul>
        </div>
        <div class="clear">
        </div>
        <div class="grid_2">
            <div class="box sidemenu">
                <div class="block" id="section-menu">
                    <ul class="section menu">
						<?php
						    //these menu items will be orders arranged by date fully approved and paid.
						?>
                        <li><a class="menuitem">Office Orders</a>
                            <ul class="submenu">
                                <li><a href="dashboard.php?display=EVERYTHING">All Orders</a> </li>
								<li><a href="dashboard.php?display=PING">Wait on Customer Orders</a> </li>
								<li><a href="dashboard.php?display=PONG">Wait on Graphics Orders</a> </li>
								<li><a href="dashboard.php?display=INVOICE">Invoice Orders</a> </li>
								<li><a href="dashboard.php?display=NET30">NET30 Orders</a></li>
                                <li><a href="dashboard.php?display=PRINT">Print Orders</a> </li>
								<li><a href="dashboard.php?display=LAMINATE">Laminate Orders</a> </li>
								<li><a href="dashboard.php?display=CUT">Cut Orders</a> </li>
								<li><a href="dashboard.php?display=SHIP">Ship Orders</a> </li>
								<li><a href="dashboard.php?display=COMPLETED">Completed Orders</a> </li>
                            </ul>
                        </li>
						<li><a class="menuitem">Manager Attention Orders</a>
                            <ul class="submenu">
                                <li><a href="dashboard.php?display=EVERYTHING">All Orders</a> </li>
								<li><a href="dashboard.php?display=PING">Warning orders</a> </li>
								<li><a href="dashboard.php?display=PONG">Late orders</a> </li>
                                <li><a href="dashboard.php?display=COMPLETED">Completed Orders</a> </li>
								These orders are in this column because they need the attention of a manager.  The order has gone by the alotted amount of time to be in the current stage.
                            </ul>
                        </li>
						<?php
						/*
                        <li><a class="menuitem">Menu 2</a>
                            <ul class="submenu">
                                <li><a>Submenu 1</a> </li>
                                <li><a>Submenu 2</a> </li>
                                <li><a>Submenu 3</a> </li>
                                <li><a>Submenu 4</a> </li>
                                <li><a>Submenu 5</a> </li>
                            </ul>
                        </li>
                        <li><a class="menuitem">Menu 3</a>
                            <ul class="submenu">
                                <li><a>Submenu 1</a> </li>
                                <li><a>Submenu 2</a> </li>
                                <li><a>Submenu 3</a> </li>
                                <li><a>Submenu 4</a> </li>
                                <li><a>Submenu 5</a> </li>
                            </ul>
                        </li>
                        <li><a class="menuitem">Menu 4</a>
                            <ul class="submenu">
                                <li><a>Submenu 1</a> </li>
                                <li><a>Submenu 2</a> </li>
                                <li><a>Submenu 3</a> </li>
                                <li><a>Submenu 4</a> </li>
                                <li><a>Submenu 5</a> </li>
                            </ul>
                        </li>
						*/
						?>
                    </ul>
                </div>
            </div>
        </div>
        <div class="grid_10">
            <div class="box round first grid">
                <h2>
                    Accounting</h2>
					<p>
						Down for maintenance...
					</p>
				<?php /*
					
                <div class="block">
                    <table>
					<tr>
                    <td><form action="accounting.php?post=1" method="POST"><input type="submit" value="Post payments" /></form></td>
					<td><form action="accounting.php?accounts=1" method="POST"><input type="submit" value="View Accounts" /></form></td>
                    </tr>
					</table>
					
					<?php
					if (isset($_GET['post'])){
					?>
						<form action="posting.php" method="POST" />
						<center><input type="submit" value="Post selected charges" /></center>
						<table class="data display datatable" id="example">
						<thead>
							<tr>
								<th>Order ID</th>
								<th>Company</th>
								<th>Amount</th>
								<th>Method</th>
								<th>Post</th>
							</tr>
						</thead>
						<tbody>
						
						<?php
						connectToSQL();
						
						$qry = "SELECT * FROM `charges` WHERE `posted` = '0'";
						$result = mysql_query($qry)or die(mysql_error());
						
						while ($row = mysql_fetch_assoc($result)){
							$id = $row['id'];
							$orders_id = $row['orders_id'];
							$company_id = $row['company_id'];
							$amount = $row['amount'];
							$method = $row['method'];
							$insert_date = $row['insert_date'];
							
							//need to figure out the company's name from the database
							
							$qry2 = "SELECT `name` FROM `companies` WHERE `id`='" . $company_id . "'";
							$result2 = mysql_query($qry2)or die(mysql_error());
							
							while ($row2 = mysql_fetch_assoc($result2)){
								$company_name = $row2['name'];
							}
							
							echo "<tr><td>" . $orders_id . "</td><td>" . $company_name . "</td><td>" . $amount . "</td><td>" . $method . "</td><td><input type=\"checkbox\" name=\"posts[]\" value=\"" . $id . "\" /></a></td></tr>";
						}
						
						mysql_close();
						?>
						</form>	
						</tbody>
						</table>
					<?php
					}elseif(isset($_GET['accounts'])){
					?>
                    
					
					<?php
					//this is going to be kind of interesting.
					//here we need to display all of the "accounts" with us.
					//we need to know how much money they owe us
					//and we need to know if that money has been payed or not.
					//we need to know - do they have a credit?
					
					
					//so to get started we need to select all the orders and display them.
					connectToSQL();
					company_db_connect(1);
					
					$qry = "SELECT * FROM `zen_orders`";
					$result = mysql_query($qry)or die(mysql_error());
					
					while ($row = mysql_fetch_assoc($result)){
						
					}
					
					mysql_close();
					?>
					
					<?php
					}else{
					?>
                    <p>
					Please select an action.
					</p>
					<?php
					}
					?>
                </div>
				*/ ?>
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
