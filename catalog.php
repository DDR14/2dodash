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
                $('li').each(function () {
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
        secure();
        ?>
        <div class="container_12">
            <?php
            require_once('inc.header.php');
            ?>        
            <div class="grid_2">
                <?php
                include "include/db.php";
                $db = new db('boostpr1_boostpromotions');

                if (isset($_GET['cPath'])) {
                    $cPath = mysql_real_escape_string((int) $_GET['cPath']);
                } else {
                    $cPath = 0;
                }
                //OPTIONS FOR NEW
                //Shapes
                $opt_shape = "";
                $psettings = $db->find('all', 'boostpr1_tododash.proof_settings');
                foreach ($psettings as $row):
                    $opt_shape = $opt_shape . "<option value=\"{$row['id']}\">{$row['setting']}</option>";
                endforeach;


                //Manufacturers                
                $opt_manufacturers = '';
                $manufacturers = $db->find('all', 'zen_manufacturers');
                foreach ($manufacturers as $row):
                    $opt_manufacturers .= "<option value='{$row['manufacturers_id']}'>{$row['manufacturers_name']}</option>";
                endforeach;

                //Discount Quantity
                $optdiscount_qty = '';
                $dquantities = $db->find('all', 'products_discount_quantity_template', '', [], 'DISTINCT(template_name)');
                foreach ($dquantities as $row):
                    $optdiscount_qty .= "<option value='{$row['template_name']}'>{$row['template_name']} discount group</option>";
                endforeach;

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
                                $parent_id = '0';
                                if (isset($_GET['parent_id'])):
                                    $parent_id = $_GET['parent_id'];
                                endif;
                                /* Categories Treeview    */
                                $bread_title = '';
                                $categories = $db->find('all', "zen_categories_description a 
                                    INNER JOIN zen_categories b 
                                    ON a.categories_id = b.categories_id ", '1=1 
                                    ORDER BY b.parent_id DESC, b.sort_order ASC, a.categories_name ASC', [
                                        ], "b.parent_id, a.categories_name, a.categories_id, 
                                        (SELECT COUNT(products_id) FROM zen_products_to_categories 
                                            WHERE categories_id = a.categories_id) AS total");

                                /* Array Play */
                                $hasactive = false;
                                function buildtree($rows, $parent_id) {
                                    global $cPath, $hasactive;
                                    
                                    $branch = [];
                                    foreach ($rows as $row):
                                        if ($row['parent_id'] == $parent_id):
                                            if ($cPath == $row['categories_id']):
                                                $row['active'] = true;
                                                $hasactive = $parent_id;
                                            endif;
                                            $sub = buildtree($rows, $row['categories_id']);
                                            if ($sub):
                                                $row['sub'] = $sub;
                                                if ($hasactive == $row['categories_id']):
                                                    $row['hasactive'] = true;
                                                    $hasactive = $row['parent_id'];
                                                endif;
                                            endif;                                            
                                            $branch[] = $row;
                                        endif;
                                    endforeach;

                                    return $branch;
                                }

                                $tree = buildtree($categories, 0);
                                
                                function genTree($tree) {
                                    global $bread_title;
                                    
                                    foreach ($tree as $row):
                                        $actclass = '';
                                        if (isset($row['sub'])):
                                            if (isset($row['hasactive'])):
                                                $bread_title .= $row['categories_name'] . " <span class='inlineblock ui-icon ui-icon-carat-1-e'></span> ";
                                                $actclass = "class='active'";
                                            endif;

                                            echo "<li $actclass ><a href='#" . $row['categories_id']
                                            . "'><span class='inlineblock ui-icon ui-icon-folder-collapsed'></span>"
                                            . $row['categories_name']
                                            . "</a><ul>";
                                            genTree($row['sub']);
                                            echo "</ul></li>";
                                        else:
                                            if (isset($row['active'])) {
                                                $bread_title .= $row['categories_name'];
                                                $actclass = "class='active'";
                                            }

                                            echo "<li><a $actclass href='catalog.php?cPath="
                                            . $row['categories_id']
                                            . "'><span class='inlineblock ui-icon ui-icon-document'></span>"
                                            . $row['categories_name']
                                            . " <small>("
                                            . $row['total']
                                            . ")</small></a></li>";
                                        endif;
                                    endforeach;
                                }

                                //function echoes stuff
                                genTree($tree);

//Option box for choosing categories                
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
                <h2><?= $bread_title; ?></h2>
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
