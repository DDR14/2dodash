<?php

session_start();

//change this to $db
include "include/db.php";
$db = new db('boostpr1_boostpromotions');

//boost promotions!
$orderid = (int) $_GET['orderid'];

if (isset($_POST["2Sided"])) {
    //this function converts a custom tag into a version 2 proof tag + front side and back side design
    $naz_id = (int) $_GET['naz_id'];
    $products_quantity = (int) $_POST['products_quantity'];

    if ($_POST['title']) {
        die('title is not empty. Click back to continue.');
    }

    $db->update('naz_custom_co', [
        'customs' => 'X-BACK='
        . $products_quantity
        . ', X-FRONT='
        . $products_quantity . ','
        . $_POST['catchall_custom'],
        'title' => '[[v2_proof:on]]',
            ], 'order_id=:orderid AND id=:naz_id', ['orderid' => $orderid, 'naz_id' => $naz_id]);

    //END UPDATE STATUS
    header('Location: vieworder.php?orderid=' . $orderid);
    
} elseif (isset($_POST["delete"])) {
    $rowid = (int) $_GET['rowid'];

    $fields = <<<SQL
        a.orders_id, a.products_model,
        (SELECT COUNT( x.id ) 
        FROM gw_codes x
        WHERE x.orders_products_id = a.orders_products_id
        AND x.status <> 0
        ) AS code_ctr
SQL;

    $row = $db->find('first', 'zen_orders_products a', "orders_products_id = :rowid", ['rowid' => $rowid], $fields);
    if ($row['code_ctr'] > 0) {
        die('The Back of these Tags are activated. cannot delete. press back to continue');
    }

    //delete proof also, forbid deleting if proof is not deleted yet
    $db->delete('gw_codes', 'orders_products_id = :rowid', ['rowid' => $rowid]);
    $db->delete('naz_custom_co', 'order_id = :orderid AND orders_products_id = :rowid', ['rowid' => $rowid, 'orderid' => $row['orders_id']]);
    $db->delete('zen_orders_products', 'orders_products_id = :rowid', ['rowid' => $rowid]);

    //IF everything is shipped, update stats
    $db->raw("UPDATE `zen_orders` SET `shipped`=`counted`, `ship_by`='shipped', orders_status='9' "
            . "WHERE `orders_id` = '{$row['orders_id']}' AND (SELECT COUNT(1) FROM `zen_orders_products` "
            . "WHERE products_shipping = '0'  AND orders_id ='{$row['orders_id']}') = '0'");

    header('Location: editorder.php?opt=skynet&orderid=' . $orderid);
} else {

    if (!isset($_GET['opt'])) {
        $rowid = (int) $_GET['rowid'];
        $naz_id = (int) $_GET['naz_id'];

        //post info
        $products_quantity = (int) $_POST['products_quantity'];
        $prev_quantity = (int) $_POST['prev_quantity'];

        //CHECK IF MODEL NUMBER EXISTS      
        $product = $db->find('first', 'zen_products a 
        INNER JOIN zen_products_description b 
        ON a.products_id = b.products_id', 'a.products_model = :products_model', [
            'products_model' => trim($_POST['products_model'])], 'a.products_id, a.products_quantity_order_min, b.products_name, a.products_model');

        $products_quantity_order_min = (int) $product["products_quantity_order_min"];

        if (!$product) {
            die('That model number could not be found. Press Back to continue');
        }

        //VALIDATE IF QUANTITY IS UPDATED CORRECTLY
        if ($products_quantity_order_min > $products_quantity) {
            //die('the quantity you entered is less than the minimum order. Press Back to continue');
        }

        //START UPDATE
        $db->update('naz_custom_co', [
            'customs' => $_POST['catchall_custom'],
            'title' => $_POST['title'],
            'footer' => $_POST['footer'],
            'background' => $_POST['background'],
            'website' => $_POST['website'],
            'model' => $product['products_model']
                ], 'order_id=:orderid AND id=:naz_id', ['orderid' => $orderid, 'naz_id' => $naz_id]);

        $db->update('zen_orders_products', [
            'products_name' => $product['products_name'],
            'products_id' => $product['products_id'],
            'products_model' => $product['products_model'],
            'products_price' => $_POST['products_price'],
            'final_price' => $_POST['products_price'],
            'products_quantity' => $products_quantity
                ], 'orders_products_id = :rowid', ['rowid' => $rowid]);

        //IF THE ORDER HAS MULTIPLE DESIGNS
        $design_total = 0;
        foreach (explode(",", $_POST['catchall_custom']) as $design) {
            if (strpos($design, '=') === false) {
                continue;
            }
            list($k, $v) = explode("=", $design);
            $design_total += $v;
        }
        //Validation Done. Remove all quantity change reuquests to remove from quantity change folder
        if ($design_total && $products_quantity == $design_total) {
            $db->update('proofs', ['new_qty' => 0], 'order_id=:orderid AND naz_custom_id=:naz_custom_id', ['orderid' => $orderid, 'naz_custom_id' => $naz_id]);
        }

        if ($prev_quantity != $products_quantity) {
            //Record Quantity Change
            $db->raw("INSERT INTO `zen_orders_status_history`(`orders_id`, `orders_status_id`, `date_added`, `customer_notified`, `comments`) "
                    . "VALUES (:oid,(SELECT orders_status FROM zen_orders WHERE orders_id = :oid),NOW(),'1','{$product['products_model']} "
                    . "quantity updated from {$prev_quantity} to {$products_quantity} pcs by {$_COOKIE["user"]["f_name"]}')", ['oid' => $orderid]);
        }
    }

    //GET RAW TOTAL
    $fields = "a.checked, a.onetime_charges, a.artwork_approved, a.payment_made, a.ship_by, b.coupon_amount,
        (SELECT SUM(x.products_price * x.products_quantity) 
        FROM  zen_orders_products x
        WHERE  x.orders_id =  '$orderid' 
        GROUP BY x.orders_id) AS raw_total";

    $order = $db->find('first', 'zen_orders a
                LEFT JOIN zen_coupons b
                ON a.coupon_code = b.coupon_code', 'orders_id = :oid', ['oid' => $orderid], $fields);

    // TRANSFORMS ORDER TO DASHBOARD MODE
    $raw_total = $order['raw_total'];
    $onetime = $order['onetime_charges'];

    $artwork_approved = $order['artwork_approved'];
    $payment_made = $order['payment_made'];
    $ship_by = $order["ship_by"];
    $coupon_amount = (float) $order['coupon_amount'];

    if ($order['checked'] == '0') {
        //One Time and Low order fee are now separate
        //FINISH
        $db->update('zen_orders', ['checked' => 1], "orders_id = $orderid");
    }

    //************************START RECOMPUTE TOTAL************************//
    //UPDATE SUBTOTAL
    $raw_total_text = "$" . number_format($raw_total, 2);
    $tax = $raw_total * 6.85 / 100;
    $tax_text = "$" . number_format($tax, 2);
    $discount = $raw_total * $coupon_amount / 100;
    $discount_text = "$" . number_format($discount, 2);

    $db->raw("UPDATE zen_orders_total
    SET text = (CASE WHEN class = 'ot_subtotal' THEN '$raw_total_text'
                    WHEN class = 'ot_tax' AND value <> 0 THEN '$tax_text'
                    WHEN class = 'ot_coupon' THEN '$discount_text'
                ELSE text
                END),
        value = (CASE WHEN class = 'ot_subtotal' THEN '$raw_total'
                    WHEN class = 'ot_tax' AND value <> 0 THEN '$tax'
                    WHEN class = 'ot_coupon' THEN '$discount'
                ELSE value
                END)
    WHERE orders_id = :oid", ['oid' => $orderid]);

    $db->update('zen_orders', ['order_tax' => $tax], "orders_id = $orderid AND order_tax <> 0");

    //COMPUTE NEW TOTAL
    $almost_total = $db->find('first', "(SELECT sum(case class        
        WHEN 'ot_coupon' THEN value * -1
        ELSE value END) AS value 
        FROM zen_orders_total WHERE orders_id='$orderid' AND class<>'ot_total') a");

    //ADD EVERYTHING
    $total = $almost_total['value'] + $onetime;
    $text = "$" . number_format($total, 2);

    $db->update('zen_orders_total', ['value' => $total, 'text' => $text], "orders_id=:oid && class='ot_total'", ['oid' => $orderid]);
    $db->update('zen_orders', ['order_total' => $total], "orders_id = $orderid");

    //*********************END RECOMPUTE TOTAL******************************//
    require_once('inc.functions.php');

    //START UPDATE STATUS (still in mysql deprecated)
    //possible situation status will be updated: item is deleted/ added or modified 
    //to become a product which doesnt require proofs, also put the script in payment
    //check IF there are still pending proofs before updating artwork_approved

    $table = "(SELECT a.id FROM naz_custom_co a 
INNER JOIN zen_orders_products d
ON d.orders_products_id = a.orders_products_id
INNER JOIN zen_products b
ON d.products_id = b.products_id
LEFT JOIN proofs c
ON c.naz_custom_id = a.id 
WHERE a.order_id = '$orderid'
AND b.require_artwork = '1'
GROUP BY a.orders_products_id, c.design_model HAVING MIN(status) IS NULL OR MIN(status) <> 1 )as a";

    $check = $db->find('first', $table, '', [], 'COUNT(*) AS RESULT');

    if ($check['RESULT'] == 0) { //everything is approved, need to update status
        if ($artwork_approved == '0' || $artwork_approved == '1') { //dont touch if already updated
            readyShipArtwork($orderid, ['history' => false]);
        }
    } else {
        if ($artwork_approved != '0' && $artwork_approved != '1') { //touch if already updated
            //if approval is recalled, get it out of printing folder           
            $db->update('zen_orders', [
                'artwork_approved' => 1,
                'cut' => 0,
                'laminated' => 0,
                'printed' => 0,
                'naz_flags' => 1,
                'orders_status' => 3
                    ], "orders_id = $orderid");
        }
    }

    paymentOrderAlign('ALIGN', 0, array('orders_id' => $orderid));

    //END UPDATE STATUS
    header('Location: vieworder.php?orderid=' . $orderid);
}
