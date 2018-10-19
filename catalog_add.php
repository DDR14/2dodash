<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

function thumbGenerator($dir, $tmpName, $fileType) {

    $selection = $_POST['shape'];
    $qry = "SELECT * FROM proof_settings WHERE id='" . $selection . "'";
    $result = mysql_query($qry)or die(mysql_error());

    while ($row = mysql_fetch_assoc($result)) {
        $id = nl2br($row['id']);
        $setting = nl2br($row['setting']);
        $size = nl2br($row['max_size']);
        $end_height = nl2br($row['end_height']);
        $end_width = nl2br($row['end_width']);
        $offset_x = nl2br($row['offset_x']);
        $offset_y = nl2br($row['offset_y']);
        $mask_file = nl2br($row['mask_file']);
    }

    $saveFileType = "png";
    $imagePath = $dir . $tmpName;
    $image = new Imagick();
    $image->readimage($imagePath);
    if ($fileType == "psd") {
        $image->setIteratorIndex(0);
    }
    // Resizes to whichever is larger, width or height
    if ($image->getImageHeight() <= $image->getImageWidth()) {
        // Resize image using the lanczos resampling algorithm based on width
        $image->resizeImage($size, 0, Imagick::FILTER_LANCZOS, 1);
    } else {
        // Resize image using the lanczos resampling algorithm based on height
        $image->resizeImage(0, $size, Imagick::FILTER_LANCZOS, 1);
    }

    // CROP
    $image->cropImage($end_width, $end_height, $offset_x, $offset_y);
    //MASK
    $image->setImageMatte(1);
    $mask = new Imagick($mask_file);
    // Copy opacity mask
    $image->compositeImage($mask, Imagick::COMPOSITE_DSTIN, 0, 0, Imagick::CHANNEL_ALPHA);
    //Make Transparent Bsckground            
    $image->writeImage($dir . str_replace("psd", $saveFileType, $tmpName));
}

session_start();
require_once('inc.functions.php');
connectToSQL();

foreach ($_POST AS $key => $value) {
    $_POST[$key] = mysql_real_escape_string($value);
}
$products_type = 1;
$products_quantity = "9999";
$products_name = $_POST['products_name'];
$products_description = $_POST['products_description'];

$products_model = $_POST['products_model'];
$products_price = $_POST['products_price'];
$products_virtual = 0;

$products_date_available = ''; //can be used
$products_weight = 0.0025;
$products_status = $_POST['products_status']; //Disabled or Enabled
$products_tax_class_id = 1; //ALWAYS TAXABLE
$manufacturers_id = $_POST['manufacturers_id'];
$products_ordered = 0; //GENERATED
$products_quantity_order_min = $_POST['products_quantity_order_min']; //minimum order
$products_quantity_order_units = 1;
$products_priced_by_attribute = 0;
$product_is_free = 0;
$product_is_call = 0;
$products_quantity_mixed = 1; //1 or 0
$product_is_always_free_shipping = 0;
$products_qty_box_status = 1;
$products_quantity_order_max = 0;
$products_sort_order = 0;
$products_discount_type = 2;
$products_discount_type_from = 0; //1 or 0
$products_price_sorter = $_POST['products_price'];
$master_categories_id = $_POST['master_categories_id']; //categories
$products_mixed_discount_quantity = 1; //1 OR 0
$metatags_title_status = 0;
$metatags_products_name_status = 0;
$metatags_model_status = 0;
$metatags_price_status = 0;
$metatags_title_tagline_status = 0;
$products_image = "{$products_model}.png";
$discount_qty = $_POST['discount_qty'];
$require_artwork = $_POST['require_artwork'];

If ($_FILES["file"]["name"]) {
    if ($_FILES["file"]["error"] > 0) {
        die($_FILES["file"]["error"]);
    } else {
        $ext = end(explode('.', $_FILES['file']['name']));
        if ($ext == 'psd') {
            if ($_POST['shape'] != "") {
                move_uploaded_file($_FILES["file"]["tmp_name"], "../images/{$products_model}.psd");
                thumbGenerator("../images/", "{$products_model}.psd", "psd");
                $products_image = "{$products_model}.png";
            } else {
                die("no shape selected");
            }
        } else {
            move_uploaded_file($_FILES["file"]["tmp_name"], "../images/{$products_model}.{$ext}");
            $products_image = "{$products_model}.{$ext}";
        }
    }
}
company_db_connect(1);
//MAIN PRODUCTS INSERT
$sql = "INSERT INTO zen_products ( require_artwork, products_type ,  products_quantity ,  products_model ,  products_image ,  products_price ,  products_virtual ,  products_date_added ,  products_last_modified ,  products_date_available ,  products_weight ,  products_status ,  products_tax_class_id ,  manufacturers_id ,  products_ordered ,  products_quantity_order_min ,  products_quantity_order_units ,  products_priced_by_attribute ,  product_is_free ,  product_is_call ,  products_quantity_mixed ,  product_is_always_free_shipping ,  products_qty_box_status ,  products_quantity_order_max ,  products_sort_order ,  products_discount_type ,  products_discount_type_from ,  products_price_sorter ,  master_categories_id ,  products_mixed_discount_quantity ,  metatags_title_status ,  metatags_products_name_status ,  metatags_model_status ,  metatags_price_status ,  metatags_title_tagline_status  ) VALUES( '$require_artwork', '$products_type' ,  '$products_quantity' ,  '$products_model' ,  '$products_image' ,  '$products_price' ,  '$products_virtual' ,  NOW() ,  NOW() ,  '$products_date_available' ,  '$products_weight' ,  '$products_status' ,  '$products_tax_class_id' ,  '$manufacturers_id' ,  '$products_ordered' ,  '$products_quantity_order_min' ,  '$products_quantity_order_units' ,  '$products_priced_by_attribute' ,  '$product_is_free' ,  '$product_is_call' ,  '$products_quantity_mixed' ,  '$product_is_always_free_shipping' ,  '$products_qty_box_status' ,  '$products_quantity_order_max' ,  '$products_sort_order' ,  '$products_discount_type' ,  '$products_discount_type_from' ,  '$products_price_sorter' ,  '$master_categories_id' ,  '$products_mixed_discount_quantity' ,  '$metatags_title_status' ,  '$metatags_products_name_status' ,  '$metatags_model_status' ,  '$metatags_price_status' ,  '$metatags_title_tagline_status'  ) ";
mysql_query($sql) or die(mysql_error());
//GET new row
$products_id = mysql_insert_id();

//INTO zen_products_discount_quantity
$sql = "INSERT INTO zen_products_discount_quantity (discount_id ,products_id, discount_qty, discount_price) "
        . "SELECT discount_id, '$products_id',discount_qty, discount_price "
        . "FROM products_discount_quantity_template "
        . "WHERE template_name = '$discount_qty'";
mysql_query($sql) or die(mysql_error());


///INTO zen_products_to_categories
$sql = "INSERT INTO zen_products_to_categories(products_id, categories_id) VALUES ('$products_id', '$master_categories_id')";
mysql_query($sql) or die(mysql_error());

//INTO zen_products_description
$sql = "INSERT INTO zen_products_description(products_id, language_id, products_name, products_description, products_url) "
        . "VALUES ('$products_id','1','$products_name' , '$products_description','')";
mysql_query($sql) or die(mysql_error());

if ($_GET['cPath'] == 'pubs') {
    header("Location: publisheddesign.php");
} else {
    header("Location: catalog.php?cPath={$_GET['cPath']}");
}