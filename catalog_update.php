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
    //Convert CMYK color profile to correct color   
    if ($image->getImageColorspace() == Imagick::COLORSPACE_CMYK) {
        $profiles = $image->getImageProfiles('*', false);
        // we're only interested if ICC profile(s) exist 
        $has_icc_profile = (array_search('icc', $profiles) !== false);
        // if it doesnt have a CMYK ICC profile, we add one 
        if ($has_icc_profile === false) {
            $icc_cmyk = file_get_contents(dirname(__FILE__) . '/icc/USWebUncoated.icc');
            $image->profileImage('icc', $icc_cmyk);
            unset($icc_cmyk);
        }
        // then we add an RGB profile 
        $icc_rgb = file_get_contents(dirname(__FILE__) . '/icc/sRGB_v4_ICC_preference.icc');
        $image->profileImage('icc', $icc_rgb);
        unset($icc_rgb);
    }
    $image->writeImage($dir . str_replace("psd", $saveFileType, $tmpName));
}

session_start();
require_once('inc.functions.php');
connectToSQL();

foreach ($_POST AS $key => $value) {
    $_POST[$key] = mysql_real_escape_string($value);
}
$products_id = $_GET['Pid'];
$products_name = $_POST['products_name'];
$products_description = $_POST['products_description'];

$products_model = $_POST['products_model'];
$products_price = $_POST['products_price'];

$products_status = $_POST["{$products_id}_products_status"]; //Disabled or Enabled
$manufacturers_id = (int)$_POST['manufacturers_id'];
$products_quantity_order_min = (int)$_POST['products_quantity_order_min']; //minimum order
$products_quantity = (int)$_POST['products_quantity']; //stock

$products_price_sorter = $_POST['products_price'];
$master_categories_id = $_POST['master_categories_id']; //categories
$require_artwork = $_POST['require_artwork'];
//
//REPLACE OLD IMAGE IF IT EXISTS

If (!empty($_FILES["file"]["name"])) {
    if ($_FILES["file"]["error"] > 0) {
        die("error: " . $_FILES["file"]["error"]);
    } else {
        if (file_exists("../images/" . $_FILES["file"]["name"])) {
            echo $_FILES["file"]["name"] . " already exists. ";
            exit();
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
            company_db_connect(1);
            $sql = "UPDATE zen_products SET products_image = '$products_image'"
                    . "WHERE products_id = '$products_id'";
            mysql_query($sql) or die(mysql_error());
            mysql_close();
        }
    }
}
company_db_connect(1);
//MAIN PRODUCTS UPDATE
$sql = "UPDATE zen_products SET "
        . "products_discount_type='2',  "
        . "products_model='$products_model',  "
        . "products_price='$products_price',  "
        . "products_last_modified=NOW(),  "
        . "products_status='$products_status',  "
        . "manufacturers_id='$manufacturers_id',  "
        . "products_quantity_order_min = '$products_quantity_order_min',  "
        . "products_quantity = '$products_quantity',  "
        . "products_price_sorter='$products_price_sorter', "
        . "require_artwork = '$require_artwork', "
        . "master_categories_id = '$master_categories_id'  "
        . "WHERE products_id = '$products_id'";
mysql_query($sql) or die(mysql_error());

////UPDATE zen_products_to_categories
//$sql = "UPDATE zen_products_to_categories SET categories_id = '$master_categories_id' "
//        . "WHERE products_id = '$products_id'";
//mysql_query($sql) or trigger_error(mysql_error());

//UPDATE zen_products_description
$sql = "UPDATE zen_products_description SET "
        . "products_name = '$products_name', "
        . "products_description = '$products_description' "
        . "WHERE products_id = '$products_id'";
mysql_query($sql) or die(mysql_error());

//DELETE INSERT zen_products_discount_quantity
if ($_POST['discount_qty'] != "") {
    $sql = "DELETE FROM zen_products_discount_quantity WHERE products_id='$products_id'";
    mysql_query($sql) or die(mysql_error());

    $sql = "INSERT INTO zen_products_discount_quantity (discount_id ,products_id, discount_qty, discount_price) "
            . "SELECT discount_id, '$products_id',discount_qty, discount_price "
            . "FROM products_discount_quantity_template "
            . "WHERE template_name = '{$_POST['discount_qty']}'";
    mysql_query($sql) or die(mysql_error());
}

if ($_GET['cPath'] == 'pubs') {
    header("Location: publisheddesign.php");
} else {
    header("Location: catalog.php?cPath={$_GET['cPath']}");
}