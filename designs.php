<?php
//make sure they are logged in and activated.
require_once('inc.functions.php');

include "include/db.php";
$db = new db('boostpr1_boostpromotions');


$prefix = [
    "AC" => "Academic",
    "AW" => "Awards",
    "AR" => "Art",
    "AT" => "Attendance",
    "BE" => "Behavior",
    "HB" => "Birthday",
    "BT" => "BoxTop",
    "BU" => "Business",
    "CP" => "Camping",
    "CI" => "Citizenship",
    "CL" => "Club",
    "CO" => "Collectibles",
    "HT" => "Health",
    "MA" => "Mascots",
    "MT" => "Math",
    "OR" => "Organization",
    "PE" => "Personal",
    "PA" => "Parents",
    "RE" => "Reading",
    "SC" => "Science",
    "SD" => "Special Days",
    "ST" => "States",
    "SP" => "Sports",
    "MI" => "Medical",
    "HO" => "Holiday",
    "PRE" => "Kinder",
    "CCA" => "CCA",
    "X" => "X",
    "LDS" => "LDS"
];

function getImgDir($model) {
    global $prefix;
    $type = explode('-', $model);

    if (array_key_exists($type[0], $prefix)) {
        return $prefix[$type[0]];
    } else {
        die('invalid product model - getImgDir');
    }
}

function ResizePng($dir, $tmpName) {

    $imagePath = $dir . $tmpName;
    $image = new Imagick();
    $image->readimage($imagePath);

    // Resize image using the lanczos resampling algorithm based on width
    $image->resizeImage(0, 183, Imagick::FILTER_LANCZOS, 1);

    //Convert CMYK color profile to correct color   
    $image->writeImage("../images/designs/Resize/" . getImgDir($tmpName) . '/' . $tmpName);
}

function saveImages($saveto, $products_model, $products_id) {
    global $db;
    $products_image = "{$products_model}.png";

    if ($_FILES["png"]["name"]) {
        if ($_FILES["png"]["error"] > 0) {
            die("error: " . $_FILES["file"]["error"]);
        } else {
//            if (file_exists($saveto . $products_image)) {
//                echo $products_image . " already exists. ";
//                exit();
//            } else {
            move_uploaded_file($_FILES["png"]["tmp_name"], $saveto . "{$products_model}.png");

            ResizePng($saveto, $products_image);

            $db->update('zen_designs', [
                'products_image' => $products_image
            ], 'products_id = :pid', ['pid' => $products_id]);
//            }
        }
    }

    if ($_FILES["psd"]["name"]) {
        if ($_FILES["psd"]["error"] > 0) {
            die("error: " . $_FILES["psd"]["error"]);
        } else {
//            if (file_exists($saveto . "{$products_model}.psd")) {
//                echo "{$products_model}.psd already exists. ";
//                exit();
//            } else {
            move_uploaded_file($_FILES["psd"]["tmp_name"], $saveto . "{$products_model}.psd");
//            }
        }
    }
}

if (isset($_POST['add_design'])) {
    $products_model = $_POST['Design']['products_model'];

    if (count($db->find('all', 'zen_designs', "products_model = '$products_model'"))) {
        die('Model number taken, please type a different model number.');
    }

    $saveto = "../images/designs/" . getImgDir($products_model) . '/';

    $products_id = $db->create('zen_designs', $_POST['Design']);
    saveImages($saveto, $products_model, $products_id);
}

if (isset($_POST['update_design'])) {

    $products_id = $_POST['Pid'];
    $products_sort_order = $_POST['products_sort_order'];
    $products_model = $_POST['products_model'];
    $products_status = $_POST["{$products_id}_products_status"]; //Disabled or Enabled
    $manufacturers_id = (int) $_POST['manufacturers_id'];
    $master_categories_id = $_POST['master_categories_id']; //categories
    $saveto = "../images/designs/" . getImgDir($products_model) . '/';
    saveImages($saveto, $products_model, $products_id);

    company_db_connect(1);
//MAIN PRODUCTS UPDATE
    $sql = "UPDATE zen_designs SET "
            . "products_model='$products_model',  "
            . "design_name='{$_POST['design_name']}',  "
            . "products_sort_order='$products_sort_order',  "
            . "products_last_modified=NOW(),  "
            . "products_status='$products_status',  "
            . "manufacturers_id='$manufacturers_id',  "
            . "master_categories_id = '$master_categories_id'  "
            . "WHERE products_id = '$products_id'";
    mysql_query($sql) or die(mysql_error());
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
        secure();
        ?>
        <div class="container_12">
            <?php
            require_once('inc.header.php');
            ?>        
            <div class="grid_2">
                <?php
                if (isset($_GET['cPath'])) {
                    $cPath = (int) $_GET['cPath'];
                } else {
                    $cPath = 0;
                }
                
                //Manufacturers
                $manufacturers = $db->find('all','zen_manufacturers');
                $opt_manufacturers = '';
                foreach($manufacturers as $row) {
                    $opt_manufacturers .= "<option value='{$row['manufacturers_id']}'>{$row['manufacturers_name']}</option>";
                }

                //Discount Quantity
                $pdqtemplate = $db->find('all', 'products_discount_quantity_template', '', [], 'DISTINCT(template_name)');
                $optdiscount_qty = '';
                foreach($pdqtemplate as $row) {
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
                                $categories = $db->find('all', "zen_categories_description a INNER JOIN zen_categories b "
                                        . "ON a.categories_id = b.categories_id", "b.parent_id='48' "
                                        . "ORDER BY b.sort_order, a.categories_name", [], "b.parent_id, a.categories_name, a.categories_id, "
                                        . "(SELECT COUNT(products_id) FROM zen_designs WHERE master_categories_id = a.categories_id) AS total ");

//OPTION BOX FOR CHOOSING CATEGORY also THE SIDEBAR
                                $opt_category = '';
                                $bread_title = '';
                                foreach($categories as $row) {
                                    $submenu = '';
                                    $subopt_category = '';
                                    $parent_id = $row['parent_id'];

                                    $categories_name = $row['categories_name'];
                                    $categories_id = $row['categories_id'];
                                    $total = $row['total'];

                                    $active = '';
                                    if ($cPath == $categories_id) {
                                        $bread_title = $categories_name;
                                        $active = "class='active'";
                                    }
                                    echo "<li><a $active href='designs.php?cPath=$categories_id'><span class='inlineblock ui-icon ui-icon-document'></span>"
                                    . "$categories_name <small>($total)</small></a></li>";
                                    $opt_category .= "<option value='$categories_id'>$categories_name</option>";
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
                <h2>DESIGN LIBRARY</h2>
                <?php
                //use shared product edit page
                $extra_qry = "a.master_categories_id='$cPath' ";
                if (isset($_GET["search"])) {
                    $extra_qry = "a.products_id LIKE '%{$_GET["search"]}%' "
                            . "OR a.products_model LIKE '%{$_GET["search"]}%' ";
                }
                
                $designs = $db->find('all', 'zen_designs a', $extra_qry  . "ORDER BY products_date_added DESC");

                //SEARCH PANEL
                $batch_panel = "<tr><td style='background-color: #DFE3E7;border-bottom:1px solid #A5ACB5;padding:15px;' colspan='10'><h1 style='display:inline'>" . $bread_title . "</h1>";
                if ($cPath == 131) {
                    $batch_panel.= '<form class="floatleft" method="post" action="batchnew_generate.php">
Add the latest<input required type="text" size="5" class="rtl" name="limit" placeholder="25" required /> BoostPromotions tags. 
<input type="submit" value="Generate" /></form>';
                }
                $batch_panel.= "<form class='floatright' method='get' action='' class='floatright'><input placeholder='Enter keywords here' type='text' name='search' /><input type='submit' value='Find' /></form></td></tr>";
                /* ========================================================================================================
                 * ******************TABLE ************************
                 * ========================================================================================================
                 */
                ?>
                <style>
                    input[type="text"], select, textarea, input[type="file"] {font-size: 11px;}        
                    .preview{max-height: 100px; max-width: 100px;}
                </style>
                <table width="100%" style="padding:0" class="box first table" cellspacing="0">
                    <?php echo $batch_panel; ?>
                    <tr>
                        <th rowspan="3">&nbsp;</th>
                        <th rowspan="3">Image</th>
                        <th>MODEL</th>
                        <th>CATEGORY</th>
                        <th>Upload</th>
                        <th class='end'>Date added</th>
                    </tr>
                    <tr>
                        <th rowspan="2"> Design Name | Sort Order</th>
                        <th>WEBSITE</th>
                        <th rowspan="2">Shape Assign</th>
                        <th class="end" rowspan="2">Commands</th>
                    </tr>
                    <tr>
                        <th>Boost visibility</th>
                    </tr>
                    <!--End First Header-->
                    <?php if ($level == 1 || $level == 2) { ?>
                        <form method="post" enctype="multipart/form-data" >
                            <tr class="alt">
                                <th rowspan="3">+</th>
                                <td rowspan="3" class="last">
                                    <center>
                                        <image class="preview" src="img/pop-add-tag.png" />
                                    </center>
                                </td>
                                <td><input placeholder="AA-000-00" name="Design[products_model]" type="text" required="required" style='font-weight: bold;' size="10" /></td>
                                <td ><select name="Design[master_categories_id]">
                                        <option value="">Select Category</option>
                                        <?php echo str_replace("value='$cPath'", "value='$cPath' selected='selected'", $opt_category);
                                        ?>
                                    </select></td>

                                <td>
                                    PNG: <input style="width:150px;" accept=".png, .PNG" required name="png" type="file" />
                                    <br/>*this will create a resized image also
                                </td>
                                <td><?php echo date("Y-m-d H:i:s"); ?></td>
                            </tr>
                            <tr class="alt">
                                <td rowspan="2" class="last">
                                    <input placeholder="Design Name" name="Design[design_name]" type="text" required size="20" />
                                    <input placeholder="Sort Order" name="Design[products_sort_order]" type="text" required size="5" />
                                </td>
                                <td>
                                    <select name="Design[manufacturers_id]">
                                        <option value="">--none--</option>
                                        <?php echo $opt_manufacturers; ?>
                                    </select></td>
                                <td rowspan="2" class="last">
                                    PSD: <input type="file" accept=".psd, .PSD"  name="psd" />
                                </td>
                                <td rowspan="2" class="last"><input type="submit" name="add_design" class="btn btn-green" value="Add Design" /></td>
                            </tr>
                            <tr class="alt">
                                <td colspan="2" class="last"><label>
                                        <input type="radio" checked="checked" value="0" name="Design[products_status]" />
                                        Hide in Boost</label>
                                    <label>
                                        <input type="radio" value="1" name="Design[products_status]" />
                                        Show in Boost</label></td>

                            </tr>
                        </form>
                        <?php
                    }

                    if (count($designs) > 0) {
                        $counter = 0;
                        $alt = "";
                        foreach($designs as $row) {
                            $products_model = $row['products_model'];
                            $products_status = $row['products_status'];
                            $products_id = $row['products_id'];
                            $products_image = $row['products_image'];
                            $products_sort_order = $row['products_sort_order'];
                            $products_date_added = $row['products_date_added'];
                            $manufacturers_id = $row['manufacturers_id'];
                            $master_categories_id = $row['master_categories_id'];
                            $design_name = $row['design_name'];
                            $counter += 1;

                            if ($counter % 2 == 0) {
                                $alt = "class='alt'";
                            } else {
                                $alt = "";
                            }
                            ?>
                            <tr><td></td><td class='last' colspan='9'></td></tr>
                            <form method='post' enctype='multipart/form-data'>
                                <tr <?php echo $alt ?> > <th rowspan='3'><?php echo $counter ?></th>
                                    <td rowspan='3' class='last'> <center><?php echo $products_id ?>
                                            <image class='preview' src='https://www.boostpromotions.com/images/designs/Resize/<?php echo getImgDir($products_model) . '/' . $products_image ?>' /> </center> </td>
                                    <td><input value='<?php echo $products_model ?>' name='products_model' type='text' required='required' style='font-weight:bold' size='10' /></td>
                                    <td >
                                        <input type="text" name="master_categories_id" value="<?= $master_categories_id; ?>" />
                                        <?php if ($cPath != $master_categories_id) { ?>
                                            <small>-linked-</small>
                                        <?php } ?>
                                        <input type="hidden" />
                                    </td> 
                                    <td>
                                        PNG: <input style="width:150px;" accept=".png, .PNG" name="png" type="file" />                                    
                                        <br/>*This will create a resized image also
                                    </td>
                                    <td><?php echo $products_date_added ?></td> </tr>
                                <tr <?php echo $alt ?>> <td rowspan='2' class='last'>
                                        <input type="text" value="<?= $design_name ?>" size="20" name="design_name" />
                                        <input type="text" value="<?= $products_sort_order ?>" size="5" name="products_sort_order" />
                                    </td>
                                    <td> <select name='manufacturers_id'> <option value=''>--none--</option>
                                            <?php echo str_replace("value='$manufacturers_id'", "value='$manufacturers_id' selected='selected'", $opt_manufacturers) ?>
                                        </select></td>
                                    <td rowspan='2' class='last'>
                                        PSD: <input name='psd' accept=".psd, .PSD" type='file' />
                                    </td>
                                    <td rowspan='2' class='last'>
                                        <a target='_blank' title='if master does not exist, please upload the psd file to that directory or update here'
                                           href='https://www.boostpromotions.com/images/designs/<?php echo getImgDir($products_model) . '/' . str_replace('png', 'psd', $products_image) ?>'>Get Master</a><br/><br/>
                                           <?php if ($level == 1 || $level == 2) { ?>
                                            <input type="hidden" name="Pid" value="<?= $products_id ?>" />
                                            <input type='submit' class='btn btn-blue' name="update_design" value='Update' />
                                            <a href='designs_delete.php?cPath=<?php echo $cPath ?>&Pid=<?php echo $products_id ?>&image=<?php echo $products_image ?>'>
                                                <button type='button' onclick="return confirm('Are you sure?')" class='btn btn-red btn-small'>Delete</button></a>
                                        <?php } ?>
                                    </td>
                                </tr>
                                <tr <?php echo $alt ?> ><td colspan='2' class='last'>
                                        <label><input <?php echo ($products_status == '0' ? "checked='checked'" : "") ?> value='0' type='radio' name='<?php echo $products_id ?>_products_status' />Hide in Boost</label>
                                        <label><input <?php echo ($products_status == '1' ? "checked='checked'" : "") ?> value='1' type='radio' name='<?php echo $products_id ?>_products_status' />Show in Boost</label>
                                    </td>

                                </tr>
                            </form>
                            <?php
                        }
                    } else {
                        ?>                
                        <tr>
                            <th></th>
                            <td colspan="9" style="padding: 20px 50px;">

                                <div class="message info">
                                    <h5>Notes:</h5>
                                    <ul>
                                        <li>Browse products by categories on the left.</li>
                                        <li>Sort Order is 0</li>
                                        <li>Products URL is blank</li>
                                        <li>Quantity is 9999</li>
                                        <li>Minimum Order is 25</li>
                                        <li>Taxable = yes</li>
                                        <li>Product weight is 0</li>                                    
                                    </ul>
                                </div>                                            
                            </td>
                        </tr>
                        <?php
                    }
                    ?>
                </table>
                <?php
                /* ========================================================================================================
                 * ****************** END************************
                 * ========================================================================================================
                 */
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
