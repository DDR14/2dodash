<!DOCTYPE html>
<html lang="en">
    <head></head>
    <body>

        <?php
        session_start();
        if (file_exists($_GET['location'])) {
            echo $_GET['location'] . " the file exists. deleting file... ";
            unlink($_GET['location']);
        } else {
            echo "File not found";
        }
        //echo "done!";
        require_once('inc.functions.php');
        connectToSQL();
        company_db_connect(1);
        
        $orderid = mysql_real_escape_string($_GET['order']);
        $status = mysql_real_escape_string($_GET['status']);
                       
        if($status == '1'){
            $qry = "UPDATE zen_orders SET ponumber = '', po_enter_date = '' WHERE orders_id = '$orderid'";
            mysql_query($qry)or die(mysql_error());
            echo mysql_affected_rows() . ' Removed Payment Status';
                        
            $qry = "DELETE FROM zen_transactions WHERE txn_id = 
(SELECT txn_id FROM boostpr1_tododash.charges WHERE orders_id = '$orderid' AND amount = 0) AND amount = 0";
            mysql_query($qry)or die(mysql_error());
            echo mysql_affected_rows() . ' Deleted Transaction container';
            
            $qry = "DELETE FROM boostpr1_tododash.charges WHERE orders_id = '$orderid' AND amount = 0";
            mysql_query($qry)or die(mysql_error());
            echo mysql_affected_rows() . ' Deleted Payment on order';
            
            $orderstatus = 6;
        }else{            
            $qry = "SELECT orders_status_id FROM zen_orders_status_history WHERE orders_id = '$orderid' "
                    . "AND orders_status_id <> '18' ORDER BY orders_status_history_id DESC LIMIT 1";
            $result = mysql_query($qry)or die(mysql_error());
            
            $orderstatus = mysql_fetch_assoc($result)['orders_status_id'];

            $qry = "UPDATE zen_orders SET orders_status='$orderstatus' WHERE orders_id = '$orderid'";
            mysql_query($qry)or die(mysql_error());          
            
        }
        
        if (!isset($_GET["checkid"])) {
            $qry = "DELETE FROM `po_receipts` WHERE id = '" . $_GET['poid'] . "'";
            mysql_query($qry)or die(mysql_error());

            $qry = "INSERT INTO `zen_orders_status_history`(`orders_id`, `orders_status_id`, `date_added`, `customer_notified`, `comments`) "
                    . "VALUES ('$orderid','$orderstatus',NOW(),'1','PO deleted id: {$_GET['poid']}. by {$_COOKIE["user"]["f_name"]}')";
            mysql_query($qry)or die(mysql_error());            
        } else {
            $qry = "DELETE FROM `check_receipts` WHERE id = '" . $_GET["checkid"] . "'";
            mysql_query($qry)or die(mysql_error());

            $qry = "INSERT INTO `zen_orders_status_history`(`orders_id`, `orders_status_id`, `date_added`, `customer_notified`, `comments`) "
                    . "VALUES ('$orderid','$orderstatus',NOW(),'1','Check deleted id {$_GET["checkid"]}. by {$_COOKIE["user"]["f_name"]}')";
            mysql_query($qry)or die(mysql_error());
        }       
        
        paymentOrderAlign('ALIGN', 0, array('orders_id' => $orderid));        
        ?>
        <script type="text/javascript">window.location.href = 'vieworder.php?orderid=<?php echo $orderid; ?>&companyid=1'</script>
    </body>
</html>