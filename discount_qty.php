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
        <body>
            <?php
            //make sure they are logged in and activated.
            require_once('inc.functions.php');
            connectToSQL();
            secure();
            //adminSecure();
            ?>



            <div class="container_12">
                <?php
                require_once('inc.header.php');
                ?>
                <div class="grid_12">
                    <div class="box round first fullpage">
                        <h2>
                            Discount Quantity</h2>
                        <div class="block">
                            <table class="data display dataTable" id="example">
                                <thead>
                                    <tr>
                                         <th>Discount Quantity ID</th>
                                        <th>Template Name</th>
                                        <th>Discount ID</th>
                                        <th>Discount Quantity</th>
                                        <th>Discount Price</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    company_db_connect(1);
                                    $result = mysql_query("SELECT * FROM `products_discount_quantity_template` ORDER BY template_name, discount_id") or trigger_error(mysql_error());
                                    while ($row = mysql_fetch_array($result)) {
                                        foreach ($row AS $key => $value) {
                                            $row[$key] = stripslashes($value);
                                        }
                                        echo "<form action='discount_qty_edit.php?Wid=$row[discount_qty_id]' method='post'>" . "<tr>";
                                    echo "<td valign='top'>" . "$row[discount_qty_id]" . "</td>";
                                        echo "<td valign='top'>" . "<input type='text' name='template_name' value='$row[template_name]'>" . "</td>";
                                        echo "<td valign='top'>" . "<input type='text' name='discount_id' value='$row[discount_id]'>" . "</td>";
                                        echo "<td valign='top'>" . "<input type='text' name='discount_qty' value='$row[discount_qty]'>" . "</td>";
                                        echo "<td valign='top'>" . "<input type='text' name='discount_price' value='$row[discount_price]'>" . "</td>";
                                       echo "<td valign='top'><button name='submitted' type='submit' class='btn btn-blue'>update</button> <a onclick='return confirm(\"Are you sure? THIS CANNOT BE UNDONE!\")' href=discount_qty_delete.php?Wid={$row['discount_qty_id']}><button type='button' class='btn btn-red'>Delete</button></a></td> ";
                                        echo "</tr>" . "</form>";
                                    }
                                    ?>
                                    <form action="discount_qty_new.php" method="post" name="discount">
                                        <tr class="add">
                                            <td>0</td>
                                           <td valign="top">
                                                <input type="text" name="template_name" placeholder="Template Name">
                                            </td>
                                            <td valign="top">
                                                <input type="text" name="discount_id" placeholder="Discount ID">
                                            </td>
                                            <td valign="top">
                                                <input type="text" name="discount_qty" placeholder="Discount Quantity">
                                            </td>
                                            <td valign="top">
                                                <input type="text" name="discount_price" placeholder="Discount Price">
                                            </td>
                                            <td><button class="btn btn-green" type="submit" name="submitted">Add Row</button></td>
                                        </tr>

                                    </form>
                                </tbody>
                            </table>

                        </div>
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
