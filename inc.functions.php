<?php

require_once 'inc.dbconnect.php';

function readyShipArtwork($orderid, array $params = array()) {
    // Set defaults for all passed options
    $options = array_merge(array(
        'date' => date('Ymd'),
        'history' => true
            ), $params);
    company_db_connect(1);

    $qry = "SELECT payment_made, ship_by, 
    (SELECT COUNT(x.orders_products_id)
    FROM zen_orders_products x
    WHERE x.products_id IN('990','853') 
    AND x.orders_id = {$orderid}) AS hasLanyard
FROM zen_orders WHERE orders_id='" . $orderid . "' LIMIT 1";
    $result = mysql_query($qry) or die(mysql_error());
    while ($row = mysql_fetch_assoc($result)) {
        $ship_by = $row['ship_by'];
        $payment_made = $row['payment_made'];
        $hasLanyard = $row['hasLanyard'] > 0;
    }

    $ok = 6;
    $sql = '';
    $date = $options['date'];
    if ($payment_made != '0') {
        $mod_date = $hasLanyard ? strtotime(date("Ymd") . "+ 15 weekdays") : strtotime(date("Ymd") . "+ 7 weekdays");
        $ship_by = ($ship_by == "0") ? date("m/d/Y", $mod_date) : $ship_by;
        $ok = 8;
    }

    $qry = "UPDATE zen_orders SET follow_up = 0, ship_by='$ship_by', artwork_approved='" . $options['date'] . "', naz_flags='1', orders_status='$ok' $sql WHERE orders_id='" . $orderid . "'";
    mysql_query($qry);

    if ($options['history']) {
        //ZenCart Status Update                
        $qry = "INSERT INTO zen_orders_status_history(orders_id, orders_status_id, date_added, customer_notified, comments) "
                . "VALUES ('$orderid','$ok',NOW(),'1','Artwork approved by {$_COOKIE["user"]["f_name"]}')";
        mysql_query($qry)or die(mysql_error());
    }
}

function readyShipPayment($orderid, array $params = array()) {
    // Set defaults for all passed options
    $options = array_merge(array(
        'date' => date('Ymd'),
        'credit_card' => false,
        'payment' => 0,
        'method' => 'Credit',
        'memo' => '',
        'txn_id' => 0,
        'reference_no' => '',
        'attachment' => ''
            ), $params);

    $payment = round($options['payment'], 2);
    $payment_method = $options['method'];
    //we are going to allow printing.
    //GET CHARGES HERE
    $amount = 0;
    $sql = '';
    company_db_connect(1);
    $qry = "SELECT (SELECT SUM(amount) FROM boostpr1_tododash.charges 
        WHERE orders_id='{$orderid}') AS amount, order_total, artwork_approved, ship_by, customers_id,
        (SELECT COUNT(x.orders_products_id)
        FROM zen_orders_products x
        WHERE x.products_id IN('990','853') 
        AND x.orders_id = {$orderid}) AS hasLanyard, ponumber,
        NOT EXISTS(SELECT 1 FROM zen_orders_products a INNER JOIN zen_products b ON a.products_id = b.products_id WHERE b.require_artwork = 1 AND a.orders_id = {$orderid}) AS noProof
    FROM zen_orders WHERE orders_id='{$orderid}' LIMIT 1";
    $result = mysql_query($qry)or die('75 ' . mysql_error());

    $row = mysql_fetch_assoc($result);
    $amount += $row['amount'];
    $orig_total = $row['order_total'];
    $artwork_approved = $row['artwork_approved'];
    $ship_by = $row['ship_by'];
    $customers_id = $row['customers_id'];
    $hasLanyard = $row['hasLanyard'] > 0;
    $noProof = $row['noProof'];
    $ponumber = $row['ponumber'];

    $balance = round($orig_total - $amount, 2);
    $date = $options['date'];

    if ($artwork_approved == 0) {
        $ok = 2;
    } elseif ($artwork_approved == 1) {
        $ok = 3;
    } else {
        //update ship by.
        $mod_date = $hasLanyard ? strtotime(date("Ymd") . "+ 15 weekdays") : strtotime($date . "+ 7 weekdays");
        $ship_by = ($ship_by == "0") ? date("m/d/Y", $mod_date) : $ship_by;
        $ok = 8;
        if ($noProof) {
            $sql = ", cut='$date', laminated='$date', printed='$date' ";
        }
    }

    if (isset($_POST['po-number'])) {
        $qry = "UPDATE zen_orders SET payment_made = '" . $date . "', orders_status='$ok', ship_by = '$ship_by' WHERE orders_id='" . $orderid . "'";
        mysql_query($qry)or die(mysql_error());
        
        $qry = "INSERT INTO zen_orders_status_history(orders_id, orders_status_id, date_added, customer_notified, comments) "
                . "VALUES ('{$orderid}','$ok','" . $date . "','1','PO approved no.: " . $_POST['po-number'] . ". by {$_COOKIE["user"]["f_name"]}')";
        mysql_query($qry)or die(mysql_error());

        return $options['txn_id'];
        //stop here
    }

    //IF EXACT OR SURPLUS
    if ($payment >= $balance) {
        //run normally
        if ($ponumber != '') {
            $sql .= ", popaid = '" . time() . "'";
            $ok = 13;
        }
        $qry = "UPDATE zen_orders SET payment_made = '" . $date . "', orders_status='$ok', ship_by = '$ship_by' $sql 
WHERE orders_id='" . $orderid . "'";
        mysql_query($qry)or die('99' . mysql_error());

        //ZenCart Status Update                
        $qry = "INSERT INTO zen_orders_status_history(orders_id, orders_status_id, date_added, customer_notified, comments) "
                . "VALUES ('{$orderid}','$ok','" . $date . "','1','$$payment $payment_method Payment Received by {$_COOKIE["user"]["f_name"]}')";
        mysql_query($qry)or die('104' . mysql_error());
    }
    if ($options['txn_id'] == 0) {
        $qry = "INSERT INTO zen_transactions(payment_method, txn_type, amount, ref_no, memo, attachment, txn_date, customers_id) "
                . "VALUES ('{$options['method']}', 'Payment', '{$options['payment']}', '"
                . $options['reference_no'] . "','"
                . $options['memo'] . "', '{$options['attachment']}', '"
                . $options['date'] . "', '$customers_id')";
        mysql_query($qry) or die(mysql_error());

        $options['txn_id'] = mysql_insert_id();
    }
    $qry = "INSERT INTO boostpr1_tododash.charges (orders_id, amount, method, insert_date, txn_id) "
            . "VALUES ('" . $orderid . "', '" . $payment . "', '" . $payment_method . "', '" . $date . "', {$options['txn_id']})";
    mysql_query($qry)or die('131 ' . mysql_error());

    return $options['txn_id'];
}

function paymentOrderAlign($mode, $charges_id, array $params = array()) {

    $options = array_merge(array(
        'date' => date('Y-m-d'),
        'amount' => 0,
        'payment_method' => '',
        'orders_id' => 0
            ), $params);

    $sql = '';
    company_db_connect(1);
    if ($mode == 'DELETE') {
        $qry = "SELECT orders_id, amount FROM boostpr1_tododash.charges WHERE id = {$charges_id}";
        $result = mysql_query($qry) or die(mysql_error());
        while ($row = mysql_fetch_assoc($result)) {
            $options['orders_id'] = $row['orders_id'];
            $options['amount'] = $row['amount'];
        }

        $qry = "DELETE FROM boostpr1_tododash.charges WHERE id = {$charges_id}";
        mysql_query($qry) or die(mysql_error());
    } elseif ($mode == 'UPDATE') {
        $options['orders_id'] = $options['orders_id'];

        $qry = "UPDATE boostpr1_tododash.charges SET 
            amount='{$options['amount']}', method='{$options['payment_method']}',
            insert_date='{$options['date']}'
            WHERE id = $charges_id";
        mysql_query($qry) or trigger_error(mysql_error());
    }
    //IF deletion/updating of payment resulted returning of balance
    $qry = "SELECT (SELECT SUM(amount) FROM boostpr1_tododash.charges 
    WHERE orders_id='{$options['orders_id']}') AS amount, order_total, artwork_approved, ship_by, payment_made, ponumber, popaid,
    (SELECT COUNT(x.orders_products_id)
    FROM zen_orders_products x
    WHERE x.products_id IN('990','853') 
    AND x.orders_id = {$options['orders_id']}) AS hasLanyard,
    NOT EXISTS(SELECT 1 FROM zen_orders_products a INNER JOIN zen_products b ON a.products_id = b.products_id WHERE b.require_artwork = 1 AND a.orders_id = {$options['orders_id']}) AS noProof
    FROM zen_orders WHERE orders_id='{$options['orders_id']}'";
    $result = mysql_query($qry)or die('75 ' . mysql_error());

    while ($row = mysql_fetch_assoc($result)) {
        $amount = $row['amount'];
        $payment_made = $row['payment_made'];
        $ponumber = $row['ponumber'];
        $orig_total = $row['order_total'];
        $artwork_approved = $row['artwork_approved'];
        $ship_by = $row['ship_by'];
        $hasLanyard = $row['hasLanyard'] > 0;
        $noProof = $row['noProof'];
        $popaid = $row['popaid'];
    }
    $balance = round($orig_total - $amount, 2);
    if ($payment_made != '0' && $balance > 0) {
        $balance = round($orig_total - $amount, 2);
        if ($artwork_approved == 0) {
            $ok = 2;
        } elseif ($artwork_approved == 1) {
            $ok = 3;
        } else {
            if ($ponumber != '') {
                $ok = 5;
            } else {
                $ok = 6;
            }
        }
        $qry = "UPDATE zen_orders SET 
payment_made = IF(ponumber = '', 0, payment_made), orders_status='$ok', popaid = '' 
WHERE orders_id='" . $options['orders_id'] . "'";
        mysql_query($qry)or die(mysql_error());

        //ZenCart Status Update                
        $qry = "INSERT INTO zen_orders_status_history 
(orders_id, orders_status_id, date_added, customer_notified, comments) 
VALUES ('{$options['orders_id']}', $ok, NOW(),'1','Order updated to require additional payment by {$_COOKIE["user"]["f_name"]}')";
        mysql_query($qry) or die(mysql_error());
    }
    if ($payment_made == '0' && $balance <= 0) {
        $date = $options['date'];

        if ($artwork_approved == 0) {
            $ok = 2;
        } elseif ($artwork_approved == 1) {
            $ok = 3;
        } else {
            //update ship by.
            $mod_date = $hasLanyard ? strtotime(date("Ymd") . "+ 15 weekdays") : strtotime($date . "+ 7 weekdays");
            $ship_by = ($ship_by == "0") ? date("m/d/Y", $mod_date) : $ship_by;
            $ok = 8;
            if ($noProof) {
                $sql = ", cut='$date', laminated='$date', printed='$date', counted='$date'";
            }
        }
        $qry = "UPDATE zen_orders SET payment_made = '" . $date . "', orders_status='$ok', ship_by = '$ship_by' $sql WHERE orders_id='" . $options['orders_id'] . "'";
        mysql_query($qry)or die(mysql_error());

        //ZenCart Status Update                
        $qry = "INSERT INTO zen_orders_status_history(orders_id, orders_status_id, date_added, customer_notified, comments) "
                . "VALUES ('{$options['orders_id']}','$ok','" . $date . "','1','Order Updated by {$_COOKIE["user"]["f_name"]}')";
        mysql_query($qry)or die(mysql_error());
    }
    if ($popaid == '' && $ponumber != '' && $balance <= 0) {
        $sql .= ", popaid = '" . time() . "'";
        $ok = 13;
        
        $qry = "UPDATE zen_orders SET orders_status='$ok' $sql WHERE orders_id='" . $options['orders_id'] . "'";
        mysql_query($qry)or die(mysql_error());
        
        $qry = "INSERT INTO zen_orders_status_history(orders_id, orders_status_id, date_added, customer_notified, comments) "
                . "VALUES ('{$options['orders_id']}',$ok,'$date','1',' Purchase order payment updated by {$_COOKIE["user"]["f_name"]}')";
        mysql_query($qry)or die(mysql_error());
    }
}

function getcols($alias = '') {
    return $alias . 'orders_id, '
            . $alias . 'ship_by, '
            . $alias . 'shipped, '
            . $alias . 'order_total, '
            . $alias . 'customers_company, '
            . $alias . 'customers_email_address, '
            . $alias . 'customers_telephone, '
            . $alias . 'customers_name, '
            . $alias . 'date_purchased ';
}

function printBoostOrders($input, $customerid = null) {
    //this function is especially designed for the database on boostpromotions.com  it will not work for any one else.
    //a new function needs to be written for each database needed.
    //valid inputs are ALL for all orders, APPROVED for artwork approved orders, PAID for orders paid - COMPLETED for orders shipped
    //as well as EVERYTHING which prints everything.

    company_db_connect(1);

    //mysql_connect("boostpromotions.com", "jruesch_nazca", "LedG1090")or die(mysql_error());
    //mysql_select_db("jruesch_boost_promotions")or die(mysql_error());

    if ($input == "APPROVED") {
        //we only want to display products that have had their artwork approved.
        $qry = "SELECT " . getcols() . " FROM zen_orders WHERE artwork_approved <> '0' AND orders_status<>'15' ORDER BY ship_by=0, ship_by ASC, orders_id DESC";
    } elseif ($input == "PAID") {
        //only orders that have been paid
        $qry = "SELECT " . getcols() . " FROM zen_orders WHERE payment_made <> '0' AND orders_status<>'15' ORDER BY ship_by=0, ship_by ASC, orders_id DESC";
    } elseif ($input == "COMPLETED") {
        $qry = "SELECT " . getcols() . " FROM zen_orders WHERE payment_made <> '0' AND artwork_approved <> '0' AND shipped <> '0' AND orders_status<>'15' ORDER BY orders_id DESC";
    } elseif ($input == "PAYPO") {
        $qry = "SELECT " . getcols() . " FROM zen_orders WHERE shipped <> '0' AND ponumber>'0'  AND popaid<'1' AND orders_status<>'15' ORDER BY ship_by=0, ship_by ASC, orders_id DESC";
    } elseif ($input == "PING") {
        $qry = "SELECT " . getcols() . " FROM zen_orders WHERE artwork_approved='1' AND orders_status<>'15' AND follow_up < 3 ORDER BY orders_id DESC";
    } elseif ($input == "PINGNR") {
        $qry = "SELECT " . getcols() . " FROM zen_orders WHERE artwork_approved='1' AND orders_status<>'15' AND follow_up >= 3 ORDER BY orders_id DESC";
    } elseif ($input == "PONG") {
        $qry = "SELECT " . getcols() . " FROM zen_orders WHERE artwork_approved='0' AND inhouse=0 AND orders_status<>'15' ORDER BY orders_id DESC";
    } elseif ($input == "INHOUSE") {
        $qry = "SELECT " . getcols() . " FROM zen_orders WHERE artwork_approved='0' AND inhouse=1 AND orders_status<>'15' ORDER BY orders_id DESC";
    } elseif ($input == "PRINT") {
        $qry = "SELECT " . getcols() . " FROM zen_orders WHERE artwork_approved<>'0' AND artwork_approved<>'1' AND printed='0' AND payment_made <> '0' AND orders_status<>'15' ORDER BY ship_by=0, ship_by ASC, orders_id DESC";
    } elseif ($input == "INVOICE") {
        $qry = "SELECT " . getcols() . " FROM zen_orders WHERE artwork_approved<>'0' AND artwork_approved<>'1' AND payment_made='0' AND orders_status<>'15' AND follow_up < 3 ORDER BY orders_id DESC";
    } elseif ($input == "INVOICENR") {
        $qry = "SELECT " . getcols() . " FROM zen_orders WHERE artwork_approved<>'0' AND artwork_approved<>'1' AND payment_made='0' AND orders_status<>'15' AND follow_up >= 3 ORDER BY orders_id DESC";
    } elseif ($input == "LAMINATE") {
        $qry = "SELECT " . getcols() . " FROM zen_orders WHERE artwork_approved<>'0' AND artwork_approved<>'1' AND printed<>'0' AND payment_made <> '0' AND printed<>'0' AND laminated='0' AND orders_status<>'15' ORDER BY ship_by=0, ship_by ASC, orders_id DESC";
    } elseif ($input == "CUT") {
        $qry = "SELECT " . getcols() . " FROM zen_orders WHERE artwork_approved<>'0' AND artwork_approved<>'1' AND printed<>'0' AND payment_made <> '0' AND printed<>'0' AND laminated <>'0' AND cut='0' AND orders_status<>'15' ORDER BY ship_by=0, ship_by ASC, orders_id DESC";
    } elseif ($input == "COUNT") {
        $qry = "SELECT " . getcols() . " FROM zen_orders WHERE artwork_approved<>'0' AND artwork_approved<>'1' AND printed<>'0' AND payment_made <> '0' AND printed<>'0' AND laminated <>'0' AND cut<>'0' AND counted='0' AND orders_status<>'15' ORDER BY ship_by=0, ship_by ASC, orders_id DESC";
    } elseif ($input == "SHIP") {
        $qry = "SELECT " . getcols() . " FROM zen_orders WHERE artwork_approved<>'0' AND artwork_approved<>'1' AND printed<>'0' AND payment_made <> '0' AND printed<>'0' AND laminated <>'0' AND cut<>'0' AND counted<>'0' AND shipped='0' AND orders_status<>'15' ORDER BY ship_by=0, ship_by ASC, orders_id DESC";
    } elseif ($input == "NET30") {
        $qry = "SELECT " . getcols() . " FROM zen_orders WHERE ponumber<>'' AND popaid='' AND shipped<>'0' AND orders_status<>'15' ORDER BY ship_by=0, ship_by ASC, orders_id DESC";
    } elseif ($input == "REJECT") {
        $qry = "SELECT DISTINCT " . getcols('x.') . " FROM naz_custom_co z
INNER JOIN proofs y ON y.naz_custom_id = z.id INNER JOIN zen_orders x ON x.orders_id = y.order_id WHERE x.orders_status = 2
GROUP BY z.orders_products_id HAVING MIN(y.status) = 3 "
                . "ORDER BY x.ship_by=0, x.ship_by ASC, x.orders_id DESC";
    } elseif ($input == "PENPO") {
        $qry = "SELECT DISTINCT " . getcols('b.') . "  FROM po_receipts a INNER JOIN zen_orders b ON a.orders_id = b.orders_id WHERE a.status = '0' AND orders_status<>'15' ORDER BY ship_by=0, ship_by ASC, orders_id DESC";
    } elseif ($input == "PENCHECK") {
        $qry = "SELECT DISTINCT " . getcols('b.') . "  FROM check_receipts a INNER JOIN zen_orders b ON a.orders_id = b.orders_id WHERE a.status = '0' AND orders_status<>'15' ORDER BY ship_by=0, ship_by ASC, orders_id DESC";
    } elseif ($input == "LANYARD") {
        $qry = "SELECT " . getcols('b.') . " FROM zen_orders b WHERE b.orders_id IN (SELECT a.orders_id FROM zen_orders_products a WHERE a.products_id IN ('990', '853')) AND b.artwork_approved NOT IN('0', '1') AND b.payment_made <> '0' AND b.shipped = '0' AND b.orders_status <> '15' AND b.orders_id NOT IN (SELECT c.orders_id FROM zen_orders_notes c WHERE c.note LIKE 'LANYARDS_ORDERED%')";
    } elseif ($input == "LANYARDSHIP") {
        $qry = "SELECT " . getcols('b.') . " FROM zen_orders b WHERE b.orders_id IN (SELECT a.orders_id FROM zen_orders_products a WHERE a.products_id IN ('990', '853')) AND b.artwork_approved NOT IN('0', '1') AND b.payment_made <> '0' AND b.shipped = '0' AND b.orders_status <> '15' AND b.orders_id IN (SELECT c.orders_id FROM zen_orders_notes c WHERE c.note LIKE 'LANYARDS_ORDERED%')";
    } elseif ($input == "KEYFOBSHIP") {
        $qry = "SELECT " . getcols('b.') . " FROM zen_orders b WHERE b.orders_id IN (SELECT a.orders_id FROM zen_orders_products a WHERE a.products_id IN ('2609', '2610', '2611', '1047')) AND b.artwork_approved NOT IN('0', '1') AND b.payment_made <> '0' AND b.shipped = '0' AND b.orders_status <> '15' AND b.orders_id";
    } elseif ($input == "SHIPBY") {
        $qry = "SELECT " . getcols() . ", DATE_FORMAT(STR_TO_DATE(ship_by, '%m/%d/%Y'), '%Y%m%d') AS shipby_new FROM zen_orders WHERE ship_by NOT IN ('0','shipped','') AND orders_status NOT IN (9, 15) ORDER BY shipby_new ASC";
    } elseif ($input == "QUANUPDATE") {
        $qry = "SELECT DISTINCT " . getcols('d.') . "  FROM proofs a INNER JOIN naz_custom_co c ON a.naz_custom_id = c.id INNER JOIN zen_orders_products b ON c.orders_products_id= b.orders_products_id
INNER JOIN zen_orders d ON b.orders_id = d.orders_id WHERE a.new_qty<> 0 AND a.new_qty <> b.products_quantity AND a.status<>3
AND b.orders_id > 1900 AND d.orders_status <> '15'";
    } elseif ($input == "PERCUSTOMER") {
        $qry = "SELECT " . getcols() . " FROM zen_orders WHERE customers_id = $customerid";
    } elseif ($input == "BACKORDER") {
        $qry = "SELECT DISTINCT " . getcols('a.') . "  FROM zen_orders a
INNER JOIN zen_orders_products b 
ON a.orders_id = b.orders_id
WHERE a.artwork_approved <>'0' AND artwork_approved <>'1' AND a.printed <>'0' 
AND a.payment_made <> '0' AND a.printed <>'0' AND a.laminated <>'0' 
AND a.cut <> '0' AND a.shipped ='0' AND a.orders_status <> '15' AND b.products_shipping <> '0'";
    } else {
        return;
    }



    if (!isset($_COOKIE['userid'])) {
        if (!isset($_SESSION)) {
            session_start();
        }
    }

    $result = mysql_query($qry)or die(mysql_error());

    while ($row = mysql_fetch_assoc($result)) {
        //echo all the information we need in table format with 5 rows
        //ID, company, customer name, date, status.
        $name = $row['customers_name'];
        $id = $row['orders_id'];
        $phone = $row['customers_telephone'];
        $email = $row['customers_email_address'];
        $date = $row['date_purchased'];
        $customers_company = $row['customers_company'];
        $order_total = $row['order_total'];
        $shipped = $row['shipped'];
        $ship_by = $row['ship_by'];
        if ($ship_by != '0') {
            if ($ship_by == "shipped") {
                $ship_by = 'Shipped @ ' . date("d M Y", strtotime($shipped));
            } else {
                $var = date("d M Y", strtotime($ship_by));
                $ship_by = $var;
            }
        }


        echo "<tr class=\"odd gradeX\"><td><a href=\"vieworder.php?display=" . 
              $input . "&orderid="
        . $id . "&companyid=1\">";

        echo "<button class=\"btn btn-small btn-teal\">View #$id</button>";
        echo "</a></td><td>$ship_by</td><td class='dt-right'>$$order_total</td><td class=\"center\">"
        . $customers_company . "</td><td>" . $email . "</td><td>$name</td><td>$phone</td><td class=\"center\">" . $date . "</td></tr>";
    }
    mysql_close();
}

function secure() {

    if (isset($_COOKIE['userid'])) {
        //here we will make sure they are activated.
        connectToSQL();
        $qry = "SELECT approved FROM users WHERE id='" . $_COOKIE['userid'] . "'";
        $result = mysql_query($qry)or die(mysql_error());
        while ($row = mysql_fetch_assoc($result)) {
            $approved = $row['approved'];
        }
        if ($approved == 3) {
            //they are banned
            $_COOKIE['userid'] = -1;
            unset($_COOKIE['userid']);
            session_destroy();
            //redirect them
            ?>
            <script type="text/javascript">
                <!--
                window.location = "/index.php?error=banned";
                //-->
            </script>
            <?php

        } elseif ($approved != 1) {
            //their account has not been approved.  Log them back out and redirect them to login.php
            $_COOKIE['userid'] = -1;
            unset($_COOKIE['userid']);
            session_destroy();
            //redirect them
            ?>
            <script type="text/javascript">
                window.location = "/index.php?error=needa";
            </script>
            <?php

        }
    } else {
        ?>
        <script type="text/javascript">
            window.location = "/index.php";
        </script>
        <?php

    }
}

function adminSecure($input = null) {

    connectToSQL();
    $qry = "SELECT level FROM users WHERE id='" . $_COOKIE['userid'] . "'";
    $result = mysql_query($qry)or die(mysql_error());


    while ($row = mysql_fetch_assoc($result)) {
        $level = $row['level'];
    }
    mysql_close();


    if ($level == 1) {
        //they are admin
    } else {
        if ($input == 1) {
            die('Don\'t touch anything.');
        } else {
            ?>
            <script type="text/javascript">
                <!--
                window.location = "logout.php";
                //-->
            </script>
            <?php

            die();
        }
    }
}

//we are recording every ones actions from here using this script.  Since this is already called on all pages.
//we cannot echo ANYTHING as this will ruin headers on many pages.

function getAddress() {

    $protocol = (isset($_SERVER['HTTPS']) and $_SERVER['HTTPS'] == 'on') ? 'https' : 'http';

    //return $protocol.'://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];

    return $_SERVER['REQUEST_URI'];
}

if (isset($_COOKIE['userid'])) {
    $address = getAddress();

    $ip = $_SERVER["REMOTE_ADDR"];

    connectToSQL();

    $needcode = array('approve_proof.php', 'cancelorder.php', 'creatingorder.php',
        'delete_charge.php' .
        'delete_po.php',
        'converge.php',
        'response.php',
        'paymentmade.php',
        'popaid.php',
        'proofdone.php',
        'recall_proof_approve.php',
        'reject_proof.php',
        'updateorder.php',
        'upload_po.php');

    if (in_array(basename($_SERVER['PHP_SELF']), $needcode)) {
        
    } else {
        if (strtoupper($_SERVER['REQUEST_METHOD']) == 'POST') {
            
        }
        $qry = "INSERT IGNORE INTO naz_log (userid, ip, location, date_time) VALUES ('" . $_COOKIE['userid'] . "', '" . $ip . "', '" . $address . "', NOW())";
        mysql_query($qry) or die(mysql_error());
    }
   
    if (!isset($_SESSION)) {
        session_start();
    }
}