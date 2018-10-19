<div class="grid_3">    
    <?php
    //display order information
    company_db_connect(1);

    $qry = "SELECT a.*,b.orders_status_name FROM zen_orders a LEFT JOIN zen_orders_status b ON a.orders_status = b.orders_status_id WHERE a.orders_id='" . $orderid . "'";
    $result = mysql_query($qry)or die(mysql_error());
    $num_rows = mysql_num_rows($result);
    if ($num_rows <= 0) {
        die("Order number $orderid does not exist.");
    }

    while ($row = mysql_fetch_assoc($result)) {
        $customers_id = $row['customers_id'];
        $customers_phone = htmlspecialchars($row['customers_telephone']);
        $customers_mobile = htmlspecialchars($row['customers_mobile']);
        $customers_email = htmlspecialchars($row['customers_email_address']);

        //Customer Address
        $customers_name = htmlspecialchars($row['customers_name']);
        $customers_company = htmlspecialchars($row['customers_company']);
        $customers_street = htmlspecialchars($row['customers_street_address']);
        $customers_suburb = htmlspecialchars($row['customers_suburb']);
        $customers_city = htmlspecialchars($row['customers_city']);
        $customers_postcode = htmlspecialchars($row['customers_postcode']);
        $customers_state = htmlspecialchars($row['customers_state']);
        $customers_country = htmlspecialchars($row['customers_country']);

        //Billing Address
        $billing_name = htmlspecialchars($row['billing_name']);
        $billing_company = htmlspecialchars($row['billing_company']);
        $billing_street = htmlspecialchars($row['billing_street_address']);
        $billing_suburb = htmlspecialchars($row['billing_suburb']);
        $billing_city = htmlspecialchars($row['billing_city']);
        $billing_postcode = htmlspecialchars($row['billing_postcode']);
        $billing_state = htmlspecialchars($row['billing_state']);
        $billing_country = htmlspecialchars($row['billing_country']);

        //Shipping Address Address
        $ship_to = htmlspecialchars($row['delivery_name']);
        $ship_to_company = htmlspecialchars($row['delivery_company']);
        $ship_to_street = htmlspecialchars($row['delivery_street_address']);
        $delivery_suburb = htmlspecialchars($row['delivery_suburb']);
        $delivery_city = htmlspecialchars($row['delivery_city']);
        $delivery_postcode = htmlspecialchars($row['delivery_postcode']);
        $delivery_state = htmlspecialchars($row['delivery_state']);
        $delivery_country = htmlspecialchars($row['delivery_country']);

        $shipping_method = $row['shipping_method'];
        $payment_method = $row['payment_method'];
        $date_purchased = $row['date_purchased'];
        $order_total = $row['order_total'];
        $order_tax = $row['order_tax'];

        $coupon_code = $row['coupon_code'];
        $artwork_approved = $row['artwork_approved'];
        $payment_made = $row['payment_made'];
        $shipped = $row['shipped'];
        $cut = $row['cut'];
        $counted = $row['counted'];
        $laminated = $row['laminated'];
        $printed = $row['printed'];
        $sheets = $row['sheets'];
        $ponumber = $row['ponumber'];
        $poTime = $row['po_enter_date'];
        $popaid = $row['popaid'];
        $checked = $row['checked'];
        $trackingtype = $row['ship_type'];
        $trackingnumber = $row['tracking'];

        $cleared = $row['cleared'];

        $onetime_charges = $row['onetime_charges'];
        $ship_by = $row['ship_by'];
        $ship_notes = $row['ship_notes'];
        $inhouse = $row['inhouse'];
        $orders_status_name = $row['orders_status_name'];
        $orders_status = $row['orders_status'];
    }


    if ($checked != 1) {
        //header('Location: checking.php');
        ?>
        <script type="text/javascript">

            window.location = "checking.php?orderid=<?php echo $orderid; ?>"

        </script>
        <?php
        die('transforming order...');
    }

    //convert these vars into human readable format
    function hRead($epoch) {
        $dt = strtotime("$epoch");
        return date('Y-m-d', $dt);
    }

    if ($artwork_approved > $payment_made) {
        $totalTimeTook = $shipped - $artwork_approved;
    } else {
        $totalTimeTook = $shipped - $payment_made;
    }

    if ($artwork_approved > '1') {
        $artwork_approved = hRead($artwork_approved);
    }
    if ($payment_made != '0') {
        $payment_made = hRead($payment_made);
    }
    if ($shipped != '0') {
        $shipped = hRead($shipped);
    }
    if ($counted != '0') {
        $counted = hRead($counted);
    }
    if ($cut != '0') {
        $cut = hRead($cut);
    }
    if ($laminated != '0') {
        $laminated = hRead($laminated);
    }
    if ($printed != '0') {
        $printed = hRead($printed);
    }

    $readonly = $level == 1 || $level == 2 ? false : true; // false; //(boolean)$shipped;
//$grab shipping
    $qry = "SELECT * FROM zen_orders_total WHERE orders_id='" . $orderid . "' && class<>'ot_total'";
    $result = mysql_query($qry)or die(mysql_error());
    $coupon_title = "";
    $loworder_amount = 0.00;
    $tax_amount = 0.00;
    while ($row = mysql_fetch_assoc($result)) {
        switch ($row['class']) {
            case "ot_shipping":
                $shipping_amount = $row['value'];
                $shipping_title = $row['title'];
                break;
            case 'ot_coupon':
                $coupon_amount = $row['value'];
                $coupon_title = $row['title'];
                break;
            case 'ot_tax':
                $tax_amount = $row['value'];
                break;
            case 'ot_subtotal':
                $subtotal_text = $row['text'];
                $subtotal_amount = (float) $row['value'];
                break;
            case 'ot_loworderfee';
                $loworder_amount = $row["value"];
                break;
        }
    }
    //lets find all the charges for this account and their dates.
    //we need to reset the connection and connect back to zen carts DB after this.
    $qry = "SELECT a.id, a.amount, a.method, a.insert_date, b.attachment 
        FROM boostpr1_tododash.charges a
        LEFT JOIN zen_transactions b 
        ON a.txn_id = b.txn_id
        WHERE a.amount<>'0' && a.orders_id='" . $orderid . "'";
    $result = mysql_query($qry)or die(mysql_error());
    $count = 1;
    $charges_table = '';
    $amount_paid = 0;
    while ($row = mysql_fetch_assoc($result)) {
        $chargeid = $row['id'];
        $amount = $row['amount'];
        $method = $row['method'];
        $insert_date = $row['insert_date'];
        $amount_paid += $amount;

        $charges_table .= "<tr><td>$count</td><td>"
                . $insert_date . "</td><td>";

        $charges_table .= "<form method='POST' action='update_charge.php?orderid=$orderid&chargeid=$chargeid'>
            <input size='5' name='payment_method' type='text' value='" . $method . "' />
                $<input size='5' name='amount' type='text' value='" . $amount . "' />
                    <input type='submit' onclick='return confirm(\"Are you sure to update this?\")' value='Update'></form></td>";
        if ($readonly != true) {
            $charges_table .= "<td><form method='POST' action='delete_charge.php?orderid=$orderid&chargeid=$chargeid&amount=$amount'>
                <input type='submit' onclick=\"return confirm('Are you sure?')\" value='x' /></form></td>";
        }
        $charges_table.= "</tr>";
        $count++;
    }
    $balance = round($order_total - $amount_paid, 2);
    if ($payment_made != '0') {
        //REVERT IF STILL BALANCE       
        if ($balance > 0 && $ponumber == "") { //dont touch ones with po/chek     
            $payment_done = false;
        } elseif ($balance < 0 && $ponumber == "") {
            $payment_done = true;
        } else {
            $payment_done = true;
        }
    } else {
        //scan if kicked back
        if ($balance <= 0) {
            $payment_done = true;
        } else {
            $payment_done = false;
        }
    }
    ?>
    <h2>Order <?php echo $orderid; ?> for Boost Promotions</h2>        
    <div class="box round first grid">
        <h2><?php echo $orders_status_name ?></h2>
        <div class="block">
            <table border="0">
                <tr><td>In House Order</td>
                    <td>
                        <form method="post" action="inhouse_update.php?oid=<?php echo $orderid; ?>">
                            <input name="inhouse" type="checkbox" <?php echo ($inhouse == '1') ? "checked" : "" ?> />
                            <input type="submit" value="update"/>
                        </form>
                    </td>         
                </tr>
                <tr><td colspan="2">&nbsp;</td></tr>
                <tr>
                    <td>Needs to be shipped by:</td><td>                        
                        <?php
                        if ($readonly == true) {
                            echo $ship_by;
                            echo "<br/><textarea disabled>$ship_notes</textarea>";
                        } else {
                            ?>
                            <form method="post" action="update_ship_by.php?orderid=<?php echo $orderid; ?>" >
                                <input size="16" type="text" id="ship_by" name="ship_by" value="<?php echo $ship_by; ?>" />
                                <textarea name="ship_notes" placeholder="Shipping Notes"><?php echo $ship_notes; ?></textarea>
                                <input type="submit" value="Submit" />
                            </form>
                        <?php } ?>
                    </td></tr>
                <tr><td colspan="2">&nbsp;</td></tr>
                <tr><td>Customer Name:</td><td><a href="viewcustomer.php?customerid=<?php echo $customers_id; ?>"><?php echo $customers_name; ?> (<?php echo $customers_id; ?>)</a>
                        <br/><a class="xframe" href='transfer_order.php?oid=<?php echo $orderid ?>' data-title='Transfer this order to another customer' 
                                data-width='800' data-height='350' >
                            [Change customer]
                        </a>
                    </td></tr>
                <tr><td>Phone Number:</td><td><?php echo $customers_phone; ?></td></tr>
                <tr><td>Mobile:</td><td><?php echo $customers_mobile; ?></td></tr>
                <tr><td>Email:</td><td><?php echo $customers_email; ?></td></tr>
                <tr><td colspan="2">&nbsp;</td></tr>
                <tr><td>Billing/Payment Address:</td>
                    <td>
                        <?php
                        if ($readonly == true) {
                            echo $billing_name . "<br />"
                            . $billing_company . "<br />"
                            . $billing_street . $billing_suburb . "<br />"
                            . $billing_city . " "
                            . $billing_state . ", "
                            . $billing_postcode
                            . "<br />"
                            . $billing_country;
                        } else {
                            ?>
                            <form action="update_address.php?mode=billing&orderid=<?php echo $orderid; ?>&companyid=1" method="POST" >
                                <input placeholder="Name" name="billing_name" type="text" value="<?php echo $billing_name; ?>" /><br />
                                <input placeholder="Company" name="billing_company" type="text" value="<?php echo $billing_company; ?>" /><br /> 
                                <input placehodler="Street" name="billing_street" type="text" value="<?php echo $billing_street; ?>" /><br />
                                <input placeholder="Suburb" name="billing_suburb" type="text" value="<?php echo $billing_suburb; ?>" /><br />
                                <input placeholder="City" name="billing_city" type="text" value="<?php echo $billing_city; ?>" /> 
                                <input placeholder="State" name="billing_state" size="10" type="text" value="<?php echo $billing_state; ?>" />, 
                                <input placeholder="PostCode" name="billing_postcode" size="5" type="text" value="<?php echo $billing_postcode; ?>" /><br /> 
                                <input placeholder="Country" name="billing_country" type="text" value="<?php echo $billing_country; ?>" /><br /><br />                                                
                                <input onclick="return confirm('Confirm?')" type="submit" value="Update" />
                            </form>
                            <?php
                        }
                        ?>
                    </td>
                </tr>
                <tr><td colspan="2">&nbsp;</td></tr>
                <tr><td>Shipping/Delivery Address:</td>
                    <td>
                        <?php
                        if ($readonly == true) {
                            echo $ship_to . "<br />"
                            . $ship_to_company . "<br />"
                            . $ship_to_street . $delivery_suburb . "<br />"
                            . $delivery_city . " "
                            . $delivery_state . ", "
                            . $delivery_postcode
                            . "<br />"
                            . $delivery_country;
                        } else {
                            ?>
                            <form action="update_address.php?mode=delivery&orderid=<?php echo $orderid; ?>&companyid=1" method="POST" >
                                <input placeholder="Name" name="ship_to" type="text" value="<?php echo $ship_to; ?>" /><br />
                                <input placeholder="Company" name="ship_to_company" type="text" value="<?php echo $ship_to_company; ?>" /><br /> 
                                <input placehodler="Street" name="ship_to_street" type="text" value="<?php echo $ship_to_street; ?>" /><br />
                                <input placeholder="Suburb" name="delivery_suburb" type="text" value="<?php echo $delivery_suburb; ?>" /><br />
                                <input placeholder="City" name="delivery_city" type="text" value="<?php echo $delivery_city; ?>" /> 
                                <input placeholder="State" name="delivery_state" type="text" size="10" value="<?php echo $delivery_state; ?>" />, 
                                <input placeholder="PostCode" name="delivery_postcode" size="5" type="text" value="<?php echo $delivery_postcode; ?>" /><br /> 
                                <input placeholder="Country" name="delivery_country" type="text" value="<?php echo $delivery_country; ?>" /><br /><br />                                                
                                <input onclick="return confirm('Confirm?')" type="submit" value="Update" />
                            </form>
                            <?php
                        }
                        ?>
                    </td>
                </tr>
                <tr><td colspan="2">&nbsp;</td></tr>
                <tr><td>Payment Method:</td><td><?php echo $payment_method; ?></td></tr>
                <tr><td>Order Date:</td><td><?php echo $date_purchased; ?></td></tr>
                <tr><td>Product Total:</td><td><?php echo $subtotal_text; ?></td></tr>
                <?php if ($coupon_code != "") { ?>
                    <tr><td><?php
                            echo $coupon_title;
                            $discount_percent = number_format(($coupon_amount / $subtotal_amount) * 100, 2);
                            ?></td><td>
                            <form method="post" action="updatecoupon.php?orderid=<?php echo $orderid; ?>" >
                                -$<input disabled size="2" autocomplete="off" type="text" value="<?php echo $coupon_amount; ?>" />
                                <input type="hidden" value="<?= $coupon_code ?>" name='coupon_code' />
                                (<input disabled size="2" autocomplete="off" 
                                        type="text" value="<?= $discount_percent; ?>" />%)
                                        <?php if ($level == 1 || $level == 2) { ?>
                                    <input type="submit" name="delete" value="x" onclick="return confirm('delete?')" />
                                <?php } ?>
                            </form>
                        </td></tr>
                    <?php
                } else {
                    ?>
                    <tr><td colspan="2">
                            <a onclick="$('#coupon_form').toggle()">add discount</a>
                            <div  id="coupon_form" style="display:none" >                                
                                <form method="post" action="updatecoupon.php?orderid=<?php echo $orderid; ?>" >
                                    <input type="hidden" name="firstname" value="<?php echo $customers_name; ?>" />
                                    <input type="hidden" name="customers_id" value="<?php echo $customers_id; ?>" />
                                    <input type="hidden" name="subtotal_amount" value="<?php echo $subtotal_amount; ?>" />
                                    <input type="text" name="redemption_code" placeholder="XXXXXXX####" size="15" />
                                    <input type="submit" value="Redeem" />
                                </form>
                                <br/>
                            </div>
                        </td></tr>
                    <?php
                }
                $tax_percent = round(($tax_amount / $subtotal_amount) * 100, 2);
                if ($readonly == true) {
                    //echo shipping
                    ?>
                    <tr><td><?php echo $shipping_title; ?></td><td><?php echo "$" . $shipping_amount; ?></td></tr>
                    <tr><td>Tax:</td><td><?php echo "$" . $tax_amount . " ($tax_percent%)"; ?></td></tr>                    
                    <tr><td>One Time Charges:</td><td><?php echo "$" . $onetime_charges; ?></td></tr>
                    <?php
                } else {
                    ?>
                    <tr><td><?php echo $shipping_title; ?></td><td>
                            <form action="updateshipping.php?orderid=<?php echo $orderid; ?>&companyid=1" method="POST" >
                                $<input type="text" size="5" name="shipping" value="<?php echo $shipping_amount; ?>" />
                                <input type="submit" value="Update" />
                            </form>
                        </td>
                    </tr>
                    <tr><td>Tax:</td><td>                            
                            <form action="updatetax.php?orderid=<?php echo $orderid; ?>&companyid=1" method="POST" >
                                <input name="shipping" type="hidden" value="<?php echo $shipping_amount; ?>" />
                                $<input id="tax" autocomplete="off" name="tax" size="2" type="text" value="<?php echo $tax_amount; ?>" />
                                (<input id="tax_percent" autocomplete="off" name="tax_percent" size="2" type="text" value="<?php echo $tax_percent; ?>" />%)
                                <input type="submit" value="Update" />
                            </form>
                            <script>
                                $(document).ready(function () {
                                    var total = <?php echo $subtotal_amount; ?>;
                                    $("#tax").keyup(function () {
                                        var val = $("#tax").val();
                                        var result = (val / total) * 100;
                                        $("#tax_percent").val(result.toFixed(2));
                                    });
                                    $("#tax_percent").keyup(function () {
                                        var val = $("#tax_percent").val();
                                        var result = (val * total) / 100;
                                        $("#tax").val(result.toFixed(2));
                                    });
                                });
                            </script>
                        </td>
                    </tr>
                    <tr><td>One Time Charges:</td><td>
                            <form action="updateonetime.php?orderid=<?php echo $orderid; ?>&companyid=1" method="POST" >
                                $<input type="text" size="5" name="one_time" value="<?php echo $onetime_charges; ?>" />
                                <input type="submit" value="Update" />
                            </form>
                        </td>
                    </tr>
                    <?php
                } if ($loworder_amount != 0) {
                    ?>
                    <tr><td>Low Order Fee:</td><td>                      
                            <form action="updateloworder.php" method="POST">
                                <input type="hidden" name="orderid" value="<?php echo $orderid; ?>" />
                                $<input size="2" type="text" name="loworder" value="<?php echo number_format($loworder_amount, 2); ?>" />
                                <input type="submit" name="process" value="Update" />
                                <input name="process" type="submit" value="x" />
                            </form>
                        </td></tr>  
                <?php } ?>
                <tr><td>Order Total:</td><td><b>$<?php echo number_format($order_total, 2); ?></b></td></tr>
            </table>


            <?php
            //for artwork approved 0 waiting on graphics
            //1 waiting on customer
            //a date is approved
            $art_done = false;
            $print_done = false;
            $lam_done = false;
            $ship_done = false;
            $cut_done = false;
            $count_done = false;
            $hasLanyard = false;
            ?>
            <i>Status Information</i><br />
            <table border="1">
                <?php
                if ($ponumber != '') {
                    //$poTime = date("Ymd", time());
                    $date = date("Ymd", $poTime);

                    if ($date == 19691231) {
                        $date = "Not recorded";
                    }
                    echo "<tr><td>PO Number:</td><td> &nbsp;" . $ponumber . " (" . $date . ")</td></tr>";
                }
                //figure out who did what
                $log_qry = "SELECT DISTINCT b.f_name, b.l_name, RIGHT(a.location,1) AS expr 
                    FROM boostpr1_tododash.naz_log a 
                    INNER JOIN boostpr1_tododash.users b ON b.id = a.userid 
                    WHERE a.location LIKE '/updateorder.php?orderid=" . $orderid . "&companyid=1&process=%'";
                $log_result = mysql_query($log_qry)or die(mysql_error());
                $approver = $printer = $laminator = $cutter = $counter = $shipper = "customer";
                while ($row = mysql_fetch_assoc($log_result)) {
                    switch ($row['expr']) {
                        case '1':
                            $approver = $row['f_name'];
                            break;
                        case '2':
                            $printer = $row['f_name'];
                            break;
                        case '3':
                            $laminator = $row['f_name'];
                            break;
                        case '4':
                            $cutter = $row['f_name'];
                            break;
                        case '7':
                            $counter = $row['f_name'];
                            break;
                        case '5':
                        case '6':
                            $shipper = $row['f_name'];
                            break;
                    }
                }
                ?>
                <tr><td>Artwork:</td><td><?php
                        if ($artwork_approved != 1 && $artwork_approved != 0) {
                            ?>
                            <form method="post" action="status_update.php?orderid=<?php echo $orderid; ?>&companyid=1">
                                Approved by <?php echo $approver; ?> on <br/>
                                <input size="6" type="text" class="pick_date" required name="date_approved" value="<?php echo $artwork_approved; ?>" />
                                <input type="submit" value="update" />
                            </form>
                            <?php
                            $art_done = true;
                        } else {
                            if ($artwork_approved == 0) {
                                echo "Waiting on Graphics to provide artwork.";
                            } elseif ($artwork_approved == 1) {
                                echo "Waiting on customer to approve artwork.";
                            }
                            ?><form method="post" onsubmit="return orderConfirm(this)" action="updateorder.php?orderid=<?php echo $orderid; ?>&companyid=1&process=1">
                                <input type="text" required class="pick_date" size="6" name="date_approved" value="<?php echo date("Y-m-d") ?>" />
                                <button type="submit" class="btn btn-small">
                                    Approve All Artwork</button>
                            </form>
                            <?php
                        }
                        ?></td></tr>
                <tr><td>Printing:</td><td><?php
                        if ($art_done == true) {
                            //at printing if printing is not a date
                            if ($printed == 0) {
                                if ($payment_made == false) {
                                    echo "Waiting on payment...";
                                } else {
                                    echo "At Printing...";
                                }
                            } else {
                                ?><form method="post" action="status_update.php?orderid=<?php echo $orderid; ?>&companyid=1">
                                    Printed <?php echo $printed . " by " . $printer . "."; ?><br/>
                                    <input style="width:50px" type="text" required name="sheet_update" value="<?php echo $sheets; ?>" />
                                    sheet(s)
                                    <input type="submit" value="update" />
                                </form>
                                <?php
                                $print_done = true;
                            }
                        } else {
                            echo "Waiting on order...";
                        }
                        ?></td></tr>
                <tr><td>Laminating:</td><td>&nbsp;<?php
                        if ($print_done == true) {
                            if ($laminated == 0) {
                                $lam_status = "At Laminating...";
                            } else {
                                $lam_status = "Laminated " . $laminated . " by " . $laminator . ".";
                                $lam_done = true;
                            }
                        } else {
                            $lam_status = "Waiting on order...";
                        }
                        echo $lam_status;
                        ?></td></tr>
                <tr><td>Cutting:</td><td>&nbsp;<?php
                        if ($lam_done == true) {
                            if ($cut == 0) {
                                $cut_status = "At cutting...";
                            } else {
                                $cut_status = "Cut " . $cut . " by " . $cutter . ".";
                                $cut_done = true;
                            }
                        } else {
                            $cut_status = "Waiting on order...";
                        }
                        echo $cut_status;
                        ?></td></tr>
                <tr><td>Counting:</td><td>&nbsp;<?php
                        if ($cut_done == true) {
                            if ($counted == 0) {
                                $count_status = 'At counting...';
                            } else {
                                $count_status = "Counted " . $counted . " by " . $counter . ".";
                                $count_done = true;
                            }
                        } else {
                            $count_status = "Waiting on order...";
                        }
                        echo $count_status;
                        ?></td></tr>
                <tr><td>Shipping:</td><td>&nbsp;<?php
                        if ($count_done == true) {
                            if ($shipped == 0) {
                                if ($payment_made == false) {
                                    $ship_status = "Waiting on payment...";
                                } else {
                                    $ship_status = "Waiting to be packed and shipped...";
                                }
                            } else {
                                $ship_status = "Shipped " . $shipped . " by " . $shipper . ".";
                                $ship_status .= "<br />Tracking: " . $trackingtype . " " . $trackingnumber;
                                $ship_done = true;
                            }
                        } else {
                            $ship_status = "Waiting on order...";
                        }
                        echo $ship_status;
                        ?></td></tr>
                <tr><td>Payment(s):</td><td>
                        &nbsp;<?php
                        if ($payment_done == true) {
                            $pay_status = $payment_made;
                        } else {
                            $pay_status = "<font color=\"red\" size=\"+2\">Payment has not been made nor completed</font> - order will not proceed past artwork.<br />";
                        }
                        echo $pay_status;
                        ?>                        
                    </td>
                </tr>
            </table>
            <table class="table" cellspacing="0">
                <?php echo $charges_table; ?>
            </table>
            <table>
                <tr><td>Balance Due:</td><td>&nbsp;<?php echo "$" . number_format(($order_total - $amount_paid), 2); ?></td></tr>
            </table>

            <?php
            company_db_connect(1);
            $po_qry = "SELECT * FROM po_receipts WHERE orders_id='$orderid'";
            $po_result = mysql_query($po_qry)or die(mysql_error());
            $po_data = array();
            while ($row = mysql_fetch_assoc($po_result)) {
                $po_data[] = $row;
            }
            if ($level == 1) {
                // IF IT HAS PO NUMBER
                if ($ponumber != "") {
                    if ($popaid == "") { //must always show
                        foreach ($po_data as $row) {
                            ?>
                            <form action="popaid.php?orderid=<?php echo $orderid; ?>&cid=<?php echo $customers_id; ?>" method="POST"
                                  onSubmit="return lessThan()" enctype="multipart/form-data">
                                <input type="hidden" name="reference_no" value="<?php echo $row['po_number']; ?>" />
                                <input type="hidden" name="attachment" value="<?php echo $row['location']; ?>" />                               
                                <table border="0">
                                    <tr><td><input type="radio" name="method" value="Check"/>Check<br />                                        
                                            <input type="radio" name="method" value="Cash" />Cash<br />
                                            <input type="radio" name="method" value="Credit" />Credit<br />
                                        </td>

                                        <td>&nbsp;&nbsp;&nbsp;&nbsp;</td>
                                        <td>Date: <input size="6" type="text" class="pick_date" name="date_paid" required value="<?php echo date("Y-m-d"); ?>" />
                                            <br/>    
                                            Amount: <input type="number" min="1" step="0.01" required placeholder="balance: <?php echo $balance; ?>" 
                                                           id="input_total" name="amount" /><br />
                                        </td></tr>
                                    <tr>
                                        <td colspan="3">
                                            <input type="submit" value= "PO Payment Received" />
                                        </td>
                                    </tr>
                                </table>                               
                            </form>
                            <?php
                        }
                    }
                }
                if ($pay_status == 0) {
                    //payment received button - need to know po number if required and need a po paid field.
                    if ($ponumber == "") {
                        if ($level == 1 || $level == 2) {
                            ?>
                            <form action="paymentmade.php?orderid=<?php echo $orderid; ?>&companyid=1" method="POST" 
                                  onSubmit="return lessThan()">
                                <input type="hidden" name="method" value="<?php echo $payment_method; ?>" />
                                <input type="hidden" name="order_total" value="<?php echo $order_total; ?>" />
                                <table border="0">
                                    <tr><td>
                                            <input type="radio" name="method" value="Check" />Check<br />
                                            <input type="radio" name="method" value="Credit" />Credit<br />
                                            <input type="radio" name="method" value="Cash" />Cash<br /></td>
                                        <td>&nbsp;&nbsp;&nbsp;&nbsp;</td>
                                        <td>
                                            Date: <input size="6" type="text" class="pick_date" name="date_paid" required value="<?php echo date("Y-m-d"); ?>" />
                                            <br/>
                                            Amount:<input type="number" min="1" step="0.01" placeholder="$<?php echo number_format($balance, 2); ?> left to pay" 
                                                          name="amount" id="input_total" /><br />
                                            <input type="submit" value="Payment Received" onclick="return confirm('Confirm?')" />
                                        </td></tr>
                                </table>
                            </form>
                            <?php
                        }
                    }
                }
            }
            //Make Payment Visible to Anywone
            ?>            
            <a class="xframe" href="elavon/converge.php?orderid=<?php echo $orderid . "&ssl_amount=" . $balance . "&custid=" . $customers_id; ?>" 
               data-title="Secure Payment" data-width="400" data-height="500">Pay With Credit Card(Payment Gateway)</a>
            <br/><br/>
            <script>
                var db_total = <?php echo $balance; ?>;
                function lessThan() {
                    var input_total = document.getElementById('input_total').value;
                    if (input_total < db_total)
                    {
                        if (!confirm('Your inputted total is LESS THAN the orders total.  Continue anyways?')) {
                            return false;
                        }
                        ;
                    } else if (input_total > db_total)
                    {
                        if (!confirm('Your inputted total is GREATER THAN the orders total.  Give them a credit?')) {
                            return false;
                        }
                        ;
                    }
                }
                function orderConfirm(form) {
                    var empcode = prompt('Enter Your Employee Code');
                    var hiddenInput;
                    hiddenInput = document.createElement('input');
                    hiddenInput.type = 'hidden';
                    hiddenInput.name = 'code';
                    hiddenInput.value = empcode;

                    if (empcode) {
                        form.appendChild(hiddenInput);
                        return true;
                    }
                    return false;
                }
            </script>
            <?php
            $qry = "SELECT COUNT(1) AS hasLanyard FROM zen_orders_products WHERE orders_id = {$orderid} AND products_id IN('990','853')";
            $result = mysql_query($qry)or die(mysql_error());
            while ($row = mysql_fetch_assoc($result)) {
                $hasLanyard = $row["hasLanyard"] > 0;
            }

            if ($art_done != true) {
                //accept art button
            } elseif ($print_done != true) {
                //done printing button
                if ($payment_made == false) {
                    ?>
                    <button class="btn btn-red">Printing Hold</button>
                    <?php
                } else {
                    ?>
                    <br/>
                    <form class="box message warning" action="updateorder.php?orderid=<?php echo $orderid; ?>&companyid=1&process=2" method="POST" onSubmit="return orderConfirm(this)">
                        <p>Make sure all tags are printed with codes at the back correctly before pushing this button. </p>
                        <label>Sheets: <input type="number" style="width:50px" required value="<?php echo $sheets; ?>" name="sheets" /></label>                        
                        <input class="btn btn-yellow" type="submit" value="Done Printing" />
                    </form>
                    <?php
                }
            } elseif ($lam_done != true) {
                //done laminating button
                ?>
                <form method="post" action="updateorder.php?orderid=<?php echo $orderid; ?>&companyid=1&process=3" 
                      onsubmit="return orderConfirm(this)"><button type="submit" class="btn btn-large">Done Laminating</button></form>
                      <?php
                  } elseif ($cut_done != true) {
                      //done cutting button
                      ?>
                <form method="post" action="updateorder.php?orderid=<?php echo $orderid; ?>&companyid=1&process=4" 
                      onsubmit="return orderConfirm(this)">
                    <button type="submit" class="btn btn-large">Done Cutting</button></form>
                <?php
            } elseif ($count_done != true) {
                //done counting button
                ?>
                <form method="post" action="updateorder.php?orderid=<?php echo $orderid; ?>&companyid=1&process=7" 
                      onsubmit="return orderConfirm(this)">
                    <button type="submit" class="btn btn-large">Done Counting</button></form>
                <?php
            } elseif ($ship_done != true && $payment_made == true) {
                //order shipped button                    
                //This is hidden because admin must use the ship per item button for shipping, not this one
                if (!$hasLanyard) {
                    ?>
                    <form action="updateorder.php?orderid=<?php echo $orderid; ?>&companyid=1&process=5" method="POST" onSubmit="return orderConfirm(this)">
                        <label><input type="radio" name="ship_type" value="USPS" />USPS</label>
                        &nbsp;&nbsp;<label><input type="radio" name="ship_type" value="UPS" />UPS</label><br />
                        Tracking:<input required="required" type="text" name="tracking" />
                        <input type="submit" value="Packed and shipped" />
                    </form>
                    <?php
                }
            }
            if ($ship_done == true) {
                //display amount in days order took.
                //echo $totalTimeTook ;
                $totalTimeTook = ceil($totalTimeTook / 60 / 60 / 24);
                echo "Order took " . $totalTimeTook . " days to ship.";
            }
            ?> 
        </div>
    </div>
    <div class='box round'><h2>Notes:</h2>
        <?php
        //display notes      
        $isKeyfobOrdered = false;
        $isLanyardOrdered = false;
        $qry = "SELECT * FROM zen_orders_notes WHERE orders_id='$orderid'";
        $result = mysql_query($qry)or die(mysql_error());
        echo "<table>";
        while ($row = mysql_fetch_assoc($result)) {
            if (strpos($row['note'], 'LANYARDS_ORDERED') !== false) {
                $isLanyardOrdered = true;
            }
            //display notes
            echo "<form action='deletenote.php?orderid=$orderid&noteid={$row['id']}' method='post'>"
            . "<tr><td>" . nl2br($row['note']) . "</td><td>&nbsp;<input onclick='return confirm(\"sure?\");' type='submit' value='x'></td></tr>"
            . "</form>";
        }
        echo "</table>";
        ?>
        <form action="addnote.php?orderid=<?php echo $orderid; ?>&companyid=1" method="POST">
            <textarea name="note"></textarea>
            <br />
            <input type="submit" value="addNote" />
        </form>
    </div>                
    <?php
    //PO APPROVE
    //CHECK APPROVE    
    $qry = "SELECT * FROM check_receipts WHERE orders_id='$orderid'";
    $result = mysql_query($qry)or die(mysql_error());
    $chk_data = array();
    while ($row = mysql_fetch_assoc($result)) {
        $chk_data[] = $row;
    }
    ?>
    <div class='box round'><h2>Customer PO:</h2>
        <br/>
        <?php
        //display PO
        if (count($chk_data) > 0) {
            echo 'You have already uploaded a check in this order. Delete it first to upload PO.';
        } elseif (count($po_data) > 0) {
            ?>        
            <table>                
                <?php
                foreach ($po_data as $row) {
                    ?>
                    <tr>
                        <td><i>Date:</i></td> 
                        <td><?= $row["date"]; ?></td>
                    </tr><tr>
                        <td><i>P.O. #:</i></td><td><?= $row["po_number"] ?></td>
                    </tr><tr>
                        <td><i>File:</i> </td><td><a target='_blank' href='<?= $row['location'] ?>' ><?= $row["remarks"] ?></a></td>
                    </tr><tr>
                        <td><i>Status:</i></td><td>
                            <?php
                            switch ($row["status"]) {
                                case "0":
                                    echo 'Pending';
                                    break;
                                case "1":
                                    echo 'Approved';
                                    break;
                            }
                            ?>
                        </td>
                    </tr><tr>


                        <?php
                        if ($row["status"] == "0") {
                            echo "<td><form action='paymentmade.php?orderid=$orderid&companyid=1' method='POST'>"
                            . "<input type='hidden' name='po-number' value='" . $row["po_number"] . "' />"
                            . "<input type='hidden' name='date_paid' value='" . $row["date"] . "' />"
                            . "<input type='hidden' name='order_total' value='$order_total' />"
                            . "<input type='hidden' name='method' value='Purchase Order' />"
                            . "<input type='hidden' name='amount' value='0' />"
                            . "<input type='hidden' name='po-file' value='" . $row["id"] . "' />"
                            . "<input type='submit' onclick='return confirm(\"Approve this PO??\")' value='Approve' /></form>  &nbsp;</td>";
                        } else {
                            echo "<td style='color:green'>[Approved]  &nbsp;</td>";
                        }
                        if ($readonly != true && $level == 1 && $popaid == "") {
                            echo "<td><form method='post' action='delete_po.php?status={$row["status"]}&order=$orderid&poid=" . $row["id"] . "&location=" . $row['location'] . "'>"
                            . "<input type='submit' onclick='return confirm(\"Are you sure?\")' value='Delete' /></form></td>";
                        }
                    }
                    ?>
                </tr>
            </table>
        <?php } elseif ($readonly != true) { ?>
            <form action="upload_po.php?orderid=<?php echo $orderid; ?>&companyid=1" method="POST" enctype="multipart/form-data">
                <input type="file" required name="file" />
                <br/>
                Date: <input size="6" class="pick_date" value="<?php echo date("Y-m-d"); ?>" type="text" name="date_po" required />
                <br/>
                P.O. #:<input required type="text" name="po_number" />
                <br/><br/>
                <input type="submit" value="Upload PO" />
            </form>
        <?php } ?>
    </div>


    <div class='box round'><h2>Customer Check:</h2>
        <br/>
        <?php
        //display Check                    
        if (count($po_data) > 0) {
            echo 'You have already uploaded a PO in this order. Delete it first to upload check.';
        } elseif (count($chk_data) > 0) {
            ?>
            <table class="table">
                <tr><td><i>Date</i></td>
                    <td><i>check number</i></td>
                    <td><i>file</i></td>
                    <td><i>status</i></td>
                    <td colspan="2"></td>
                </tr>
                <?php
                foreach ($chk_data as $row) {
                    echo "<tr><td>{$row['date']}</td>"
                    . "<td><a target='_blank' href='" . $row['location'] . "' >" . $row["remarks"] . "</a></td>";
                    switch ($row["status"]) {
                        case "0":
                            echo '<td>Pending</td>';
                            break;
                        case "1":
                            echo '<td>Approved</td>';
                            break;
                        case "2":
                            echo '<td></td>';
                            break;
                    }
                    if ($row["status"] == "0") {
                        echo "<td><form action='paymentmade.php?orderid=$orderid&companyid=1' method='POST'>"
                        . "<input type='hidden' name='date_paid' value='" . $row["date"] . "' />"
                        . "<input type='hidden' name='order_total' value='$order_total' />"
                        . "<input type='hidden' name='method' value='Check' />"
                        . "<input type='hidden' name='check-file' value='" . $row["id"] . "' />"
                        . "<input name='amount' required placeholder='$$balance' size='5' type='text' class='dt-right' /><br/>"
                        . "<input type='submit' onclick='return confirm(\"Approve this Check??\")' value='Approve' /></form></td>";
                    } else {
                        echo "<td style='color:green'>[Approved]</td>";
                    }
                    if ($readonly != true && $level == 1) {
                        echo "<td><form method='post' action='delete_po.php?status={$row["status"]}&order=$orderid&checkid=" . $row["id"] . "&location=" . $row['location'] . "'>"
                        . "<input type='submit' onclick='return confirm(\"Are you sure?\")' value='x' /></form></td>";
                    }
                    echo "</tr>";
                }
                ?>
            </table>
        <?php } elseif ($readonly != true) { ?>
            <form action="upload_po.php?checkreceipt=1&orderid=<?php echo $orderid; ?>&companyid=1" method="POST" enctype="multipart/form-data">
                Date: <input class="pick_date" size="6" value="<?php echo date("Y-m-d"); ?>" type="text" name="date_check" required/>
                <input type="file" name="file" />
                <br/><br/>
                <input type="submit" value="Upload Check" />
            </form>
        <?php } ?>
    </div>          
</div>