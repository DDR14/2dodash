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
        <link href="css/jquery-ui.min.css" rel="stylesheet" type="text/css" />
        <link href="css/vieworder.css" rel="stylesheet" type="text/css" />
        <!-- BEGIN: load jquery -->
        <script src="js/jquery-1.6.4.min.js" type="text/javascript"></script>
        <script type="text/javascript" src="js/jquery-ui/jquery-ui.min.js"></script>
        <script src="js/table/jquery.dataTables.min.js" type="text/javascript"></script>
        <script src="js/setup.js" type="text/javascript"></script>
        <!-- END: load jquery -->    
        <script type="text/javascript">
            $(document).ready(function () {
                //setupLeftMenu();
                $('.datatable').dataTable();
                setSidebarHeight();
                $('.tree li').each(function () {
                    if ($(this).children('ul').length > 0) {
                        $(this).addClass('parent');
                    }
                });

                $('.tree li.parent > a').click(function ( ) {
                    $(this).parent().toggleClass('active');
                    setSidebarHeight();
                });
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
            <?php
            require_once('inc.header.php');
            ?>        
            <div class="grid_2">
                <?php
                if (isset($_GET['elvis'])) {
                    company_db_connect(1);
                    $result = mysql_query("SELECT * FROM
                        zen_categories a 
                        INNER JOIN zen_categories_description b
                            ON a.categories_id = b.categories_id                           
                            WHERE a.parent_id = 322 AND a.categories_id <> 323
                            ORDER BY categories_name") or trigger_error(mysql_error());
                    
                    while ($row = mysql_fetch_array($result)) {                        
                        $products_image = $row['categories_image'];
                        $products_name = 'Custom ' .  $row['categories_name'];
                        $products_model = strtoupper(str_replace(' ', '-',$products_name));
                        $products_description = $products_name;
                        $master_categories_id = $row['categories_id'];
                        
                        //MAIN PRODUCTS INSERT
$sql = "INSERT INTO zen_products ( require_artwork, products_type ,  products_quantity ,  products_model ,  products_image ,  products_price ,  products_virtual ,  products_date_added ,  products_last_modified ,  products_date_available ,  products_weight ,  products_status ,  products_tax_class_id ,  manufacturers_id ,  products_ordered ,  products_quantity_order_min ,  products_quantity_order_units ,  products_priced_by_attribute ,  product_is_free ,  product_is_call ,  products_quantity_mixed ,  product_is_always_free_shipping ,  products_qty_box_status ,  products_quantity_order_max ,  products_sort_order ,  products_discount_type ,  products_discount_type_from ,  products_price_sorter ,  master_categories_id ,  products_mixed_discount_quantity ,  metatags_title_status ,  metatags_products_name_status ,  metatags_model_status ,  metatags_price_status ,  metatags_title_tagline_status  ) 
    SELECT  require_artwork, products_type,  products_quantity ,  '$products_model' ,  '$products_image' ,  products_price ,  
    products_virtual ,  NOW() ,  NOW() ,  products_date_available ,  products_weight ,  products_status , 
    products_tax_class_id ,  manufacturers_id ,  products_ordered ,  products_quantity_order_min ,  products_quantity_order_units, 
    products_priced_by_attribute ,  product_is_free ,  product_is_call ,  products_quantity_mixed ,  product_is_always_free_shipping ,
    products_qty_box_status ,  products_quantity_order_max ,  products_sort_order ,  products_discount_type , 
    products_discount_type_from ,  products_price_sorter ,  '$master_categories_id' ,  products_mixed_discount_quantity , 
    metatags_title_status,  metatags_products_name_status ,  metatags_model_status ,  metatags_price_status , 
        metatags_title_tagline_status FROM zen_products WHERE products_id = 3487 "; 
mysql_query($sql) or die(mysql_error() . '1');
//GET new row
$products_id = mysql_insert_id(); 

//INTO zen_products_discount_quantity
$sql = "INSERT INTO zen_products_discount_quantity (discount_id ,products_id, discount_qty, discount_price) "
        . "SELECT discount_id, '$products_id',discount_qty, discount_price "
        . "FROM products_discount_quantity_template "
        . "WHERE template_name = 'bdgeJR'";
mysql_query($sql) or die(mysql_error(). '2');


///INTO zen_products_to_categories
$sql="INSERT INTO zen_products_to_categories(products_id, categories_id) VALUES ('$products_id', '$master_categories_id')";
mysql_query($sql) or die(mysql_error() . '3');

//INTO zen_products_description
$sql="INSERT INTO zen_products_description(products_id, language_id, products_name, products_description, products_url) "
        . "VALUES ('$products_id','1','$products_name' , '$products_description','')";
mysql_query($sql) or die(mysql_error() . '4');

                    }
                    die();
                    
                }
                
                
                
                if (isset($_GET['elvisdelete'])) {
                    company_db_connect(1);
                    $result = mysql_query("SELECT * FROM
                        zen_categories a 
                        INNER JOIN zen_categories_description b
                            ON a.categories_id = b.categories_id 
                            INNER JOIN zen_products c 
                            ON c.master_categories_id = a.categories_id
                            WHERE a.parent_id = 322 AND a.categories_id <> 323
                            ORDER BY categories_name") or trigger_error(mysql_error());
                    while ($row = mysql_fetch_array($result)) {                        
                        $products_id = $row['products_id'];
                        
                       //MAIN PRODUCTS DELETE
$sql = "DELETE FROM `zen_products` WHERE `products_id`='$products_id'";
mysql_query($sql) or die(mysql_error());

//DELETE zen_products_discount_quantity
$sql = "DELETE FROM `zen_products_discount_quantity` WHERE `products_id`='$products_id'";
mysql_query($sql) or die(mysql_error());

///DELETE zen_products_to_categories
$sql="DELETE FROM `zen_products_to_categories` WHERE `products_id`='$products_id'";
mysql_query($sql) or die(mysql_error());

///DELETE zen_specials
$sql="DELETE FROM `zen_specials` WHERE `products_id`='$products_id'";
mysql_query($sql) or die(mysql_error());

///DELETE zen_meta_tags_products_description
$sql="DELETE FROM `zen_meta_tags_products_description` WHERE `products_id`='$products_id'";
mysql_query($sql) or die(mysql_error());

///DELETE zen_products_attributes
$sql="DELETE FROM `zen_products_attributes` WHERE `products_id`='$products_id'";
mysql_query($sql) or die(mysql_error());

///DELETE zen_customers_basket
$sql="DELETE FROM `zen_customers_basket` WHERE `products_id`='$products_id'";
mysql_query($sql) or die(mysql_error());

///DELETE zen_customers_basket_attributes
$sql="DELETE FROM `zen_customers_basket_attributes` WHERE `products_id`='$products_id'";
mysql_query($sql) or die(mysql_error());

//DELETE zen_products_description
$sql="DELETE FROM `zen_products_description`WHERE `products_id`='$products_id'";
mysql_query($sql) or die(mysql_error());

                    }
                    die();
                    
                }
                
                
                connectToSQL();
                if (isset($_GET['cPath'])) {
                    $cPath = mysql_real_escape_string((int) $_GET['cPath']);
                } else {
                    $cPath = 0;
                }
                //OPTIONS FOR NEW
                //Shapes
                $opt_shape = "";
                $result = mysql_query("SELECT * FROM `proof_settings`") or trigger_error(mysql_error());
                while ($row = mysql_fetch_array($result)) {
                    $opt_shape = $opt_shape . "<option value=\"{$row['id']}\">{$row['setting']}</option>";
                }
                company_db_connect(1);


                //Manufacturers
                $qry = "SELECT * FROM  `zen_manufacturers` ";
                $result = mysql_query($qry)or die(mysql_error());
                $opt_manufacturers = '';
                while ($row = mysql_fetch_assoc($result)) {
                    $opt_manufacturers .= "<option value='{$row['manufacturers_id']}'>{$row['manufacturers_name']}</option>";
                }

                //Discount Quantity
                $qry = "SELECT DISTINCT(template_name) FROM `products_discount_quantity_template`";
                $result = mysql_query($qry)or die(mysql_error());
                $optdiscount_qty = '';
                while ($row = mysql_fetch_assoc($result)) {
                    $optdiscount_qty .= "<option value='{$row['template_name']}'>{$row['template_name']} discount group</option>";
                }

                //Require Artwork
                $opt_reqart = "<option value='1'>Required</option><option value='0'>Not Required</option>";
                ?>
                <div class="box sidemenu">
                    <div class="block" id="section-menu">
                        <ul class="section menu">
                            <li><a class="menuitem">CATEGORIES</a></li>
                        </ul>
                        <div id="accordion">
                            <ul class="tree">
                                <?php
//MAKE TREEVIEW OF CATEGORIES                            
                                $qry = "SELECT b.parent_id, a.categories_name, a.categories_id, "
                                        . "(SELECT COUNT(products_id) FROM zen_products_to_categories WHERE categories_id = a.categories_id) AS total "
                                        . "FROM zen_categories_description a "
                                        . "INNER JOIN zen_categories b "
                                        . "ON a.categories_id = b.categories_id "
                                        . "WHERE b.parent_id='293'"
                                        . "ORDER BY b.sort_order, a.categories_name";
                                $result = mysql_query($qry)or die(mysql_error());

//OPTION BOX FOR CHOOSING CATEGORY also THE SIDEBAR
                                $opt_category = '';
                                $bread_title = '';
                                while ($row = mysql_fetch_assoc($result)) {
                                    $submenu = '';
                                    $subopt_category = '';
                                    $parent_id = $row['parent_id'];

                                    $categories_name = $row['categories_name'];
                                    $categories_id = $row['categories_id'];
                                    $total = $row['total'];

                                    if ($total == '0') {
                                        $qry2 = "SELECT b.parent_id, a.categories_name, a.categories_id, "
                                                . "(SELECT COUNT(products_id) FROM zen_products_to_categories WHERE categories_id = a.categories_id) AS total "
                                                . "FROM zen_categories_description a "
                                                . "INNER JOIN zen_categories b "
                                                . "ON a.categories_id = b.categories_id "
                                                . "WHERE b.parent_id='$categories_id' "
                                                . "ORDER BY b.sort_order, a.categories_name";
                                        $result2 = mysql_query($qry2)or die(mysql_error());
                                        $active = '';
                                        while ($row2 = mysql_fetch_assoc($result2)) {
                                            $cat_id = $row2['categories_id'];
                                            $orphan = $row2['parent_id'];
                                            $cat_name = $row2['categories_name'];
                                            $total2 = $row2['total'];

                                            $subactive = '';
                                            if ($cPath == $cat_id) {
                                                $active = "class='active'";
                                                $bread_title = $cat_name;
                                                $subactive = $active;
                                            }

                                            $submenu .= "<li><a $subactive href='?cPath=$cat_id'><span class='inlineblock ui-icon ui-icon-document'></span>$cat_name <small>($total2)</small></a></li>";
                                            $subopt_category .= "<option value='$cat_id'>$cat_name</option>";
                                        }
                                        if ($submenu != '') {
                                            $submenu = "<ul>$submenu</ul>";
                                        }

                                        if ($active != '') {
                                            $bread_title = $categories_name . " <span class='inlineblock ui-icon ui-icon-carat-1-e'></span> " . $bread_title;
                                        }
                                        echo "<li $active><a href='#$categories_id'><span class='inlineblock ui-icon ui-icon-folder-collapsed'></span>"
                                        . "$categories_name</a>$submenu</li>";
                                        $opt_category .="<optgroup label='$categories_name'>$subopt_category</optgroup>";
                                    } else {
                                        $active = '';
                                        if ($cPath == $categories_id) {
                                            $bread_title = $categories_name;
                                            $active = "class='active'";
                                        }
                                        echo "<li><a $active href='?cPath=$categories_id'><span class='inlineblock ui-icon ui-icon-document'></span>"
                                        . "$categories_name <small>($total)</small></a></li>";
                                        $opt_category .= "<option value='$categories_id'>$categories_name</option>";
                                    }
                                }
                                if (isset($_GET["search"])) {
                                    $bread_title = 'Search "' . $_GET["search"] . '"';
                                }
                                ?>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
            <div class="grid_10">
                <h2><?php echo $bread_title; ?></h2>
                <?php
                //use shared product edit page
                $extra_qry = "LEFT JOIN zen_products_to_categories c "
                        . "ON c.products_id = a.products_id "
                        . "WHERE c.categories_id='$cPath' ";
                if (isset($_GET["search"])) {
                    $extra_qry = "WHERE a.products_id LIKE '%{$_GET["search"]}%' "
                            . "OR a.products_model LIKE '%{$_GET["search"]}%' "
                            . "OR b.products_description LIKE '%{$_GET["search"]}%' ";
                }
                $qry = "SELECT * "
                        . "FROM zen_products a "
                        . "LEFT JOIN zen_products_description b "
                        . "ON a.products_id = b.products_id "
                        . $extra_qry
                        . "ORDER BY products_date_added DESC";

                //SEARCH PANEL
                $batch_panel = "<tr><td style='background-color: #DFE3E7;border-bottom:1px solid #A5ACB5;padding:15px;' colspan='10'>";
                $level = 1;
                if ($cPath != 0 && ($level == 1 || $level == 2)) {
                    if ($cPath == 131) {
                        $batch_panel.= '<form class="floatleft" method="post" action="batchnew_generate.php">
Add the latest<input required type="text" size="5" class="rtl" name="limit" placeholder="25" required /> BoostPromotions tags. 
<input type="submit" value="Generate" /></form>';
                    } else {

                        $batch_panel.= "<form class='floatleft' method='post' action='batchprice_update.php?cPath=$cPath' >
<select required name='discount_qty'>
<option value=''>-Qty Discounts-</option>$optdiscount_qty</select>
$<input required placeholder='0.00' type='text' name='products_price' size='5' />
<input required placeholder='25' type='text' name='products_min_quantity' size='3' />
<input type='submit' value='Batch Update'/></form>";
                    }
                }
                $batch_panel.= "<form class='floatright' method='get' action='' class='floatright'><input placeholder='Enter keywords here' type='text' name='search' /><input type='submit' value='Find' /></form></td></tr>";
                require_once 'inc.catalog.php';
                ?>
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
