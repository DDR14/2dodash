<?php
/*
 * GENERATES FORMS FOR approve, reject, recall and remove
 * 
 * 
 * 2018 Elvis Jared B De Ocampo
 * 
 */

if ($shipped == true || $products_shipping != '0') {
    if ($products_shipping == '0') {
        $item_ship_text = "Tracking:{$trackingnumber}, {$trackingtype} - $shipped";
    } else {
        $item_ship_text = "Tracking:$products_shipping - $products_shipping_date";
    }
    $shipped_msg = "<p><strong style='color:blue;'>[Item Shipped!]</strong><br /><i>$item_ship_text</i></p>";
}
if ($row['manufacturers_id'] == 9) {
    ?>

    <a href="uc_codes_get.php?uc_per_design=1&opid=<?= $rowid; ?>" target="_blank">
        <button type="button"  >
            Unplugged Challenge Codes</button></a>
    <br/><br/>
    <a href="uc_codes_get.php?activate=1&opid=<?= $rowid; ?>" target="_blank" onclick="return confirm('activate codes as valid for gowin website?')" >
        <button 
            type="button" name="activate" >Activate</button>
    </a>

    <?php
} elseif ($require_artwork == 1) {
    //PROOF LIST
    $valid_reject = 1;
    $qry2 = "SELECT location, reason, status, id AS proof_id, name, new_qty, date_modified "
            . "FROM proofs WHERE naz_custom_id ='$naz_id' AND order_id ='$orderid' "
            . $proof_optional_qry
            . "ORDER BY status, date_modified DESC";
    $result2 = mysql_query($qry2)or die(mysql_error());
    $proof_count = mysql_num_rows($result2);
    $first = TRUE;
    while ($row = mysql_fetch_assoc($result2)) {
        $name = $row['name'];
        $location = $row['location'];
        $reason = $row['reason'];
        $status = $row['status'];
        $proof_id = $row['proof_id'];
        $new_qty = (int) $row["new_qty"];
        $date_modified = $row["date_modified"] != '0000-00-00 00:00:00' ?
                date("m/d", strtotime($row["date_modified"])) : "unrecorded";

        $img = "//www.2dodash.com/" . $bt . $location;
        if ($first == TRUE) {
            $first = FALSE;
            ?>
            <div class='floatleft' style="width: 50%">
                <img class='preview' src='<?php echo $img; ?>' />
                <br />
                <a href="//www.2dodash.com/<?= $bt; ?>proofs/<?php echo $name; ?>" ><?php echo $name; ?></a>
            </div>
            <div class='floatleft' style="width: 50%"><?php
                if ($status == '1') {
                    $proofs[] = $bt . 'proofs/' . $name;
                    $valid_reject = 0;
                    if ($shipped_msg != "") {
                        echo $shipped_msg;
                    } else {
                        echo "<p>" . echo_proof_status($status) . " on $date_modified</p>";
                        if ($shipped == false && $readonly == false) {
                            ?><a onclick="return confirm('Are you sure?')"
                               href="recall_proof_approve.php?proofid=<?php
                               echo $proof_id . '&amp;oid='
                               . $orderid . '&amp;companyid=1';
                               ?>" ><button type="button">Recall</button></a>
                               <?php if ($payment_made == true) { ?>
                                <a class="zframe" href="#" data-popid="popup2"
                                   data-action="<?php echo "updateorder.php?orderid=$orderid&companyid=1&process=6"; ?>"
                                   data-title="Shipping for <?php echo $model; ?>"
                                   data-pid="<?php echo $rowid; ?>"
                                   data-qty="<?php echo $products_quantity; ?>">
                                    <button type="button">Ship</button></a>
                                <?php
                            }
                        }
                    }
                } else {
                    echo "<p>" . echo_proof_status($status) . " on $date_modified<br /><i>$reason</i></p>";
                    if ($status == '3') {
                        if ($valid_reject == 1) {
                            $upload_done = FALSE;
                        }
                        if($isVersion2):
                            echo "<a onclick='return confirm(\"This will remove this proof from preview. you can still check this rejected proof on reject history. Proceed?\")' href='unlinkproof.php?proof_id=$proof_id&amp;oid=$orderid&amp;location=$location&amp;companyid=1' ><button type='button'>Remove</button></a>";
                        endif;
                    } elseif ($status == '0') {
                        $valid_reject = 0;
                        echo "<a onclick='return confirm(\"Are you sure? Removing proof also means deleting this file in the server.\")' href='deleteproof.php?proof_id=$proof_id&amp;oid=$orderid&amp;location=$location&amp;companyid=1'>"
                        . "<button type='button' >Remove</button></a>";
                        echo '<a onclick="return confirm(\'Are you sure?\')" href="approve_proof.php?proofid=' . $proof_id . '&amp;oid=' . $orderid . '&amp;companyid=1" >
					                                                                            <button type="button">Approve</button>
					                                                                        </a>';
                        ?>
                        <button type='button' onclick="rejectProof('reject_proof.php?proofid=<?php echo $proof_id . '&oid=' . $orderid . '&companyid=1' ?>')">Reject</button>
                        <?php
                    }
                }
                if ($new_qty != 0 && $new_qty != $products_quantity) {
                    ?>
                    <br/>
                    <a class="zframe" href="#" data-popid="popup3"
                       data-action="update_qrequest.php?proofid=<?php
                       echo $proof_id
                       . "&orderid=" . $orderid . "&orig_q=" . $products_quantity . "&name=" . $name;
                       ?>"
                       data-title="Quanitity change request for <?php echo $model; ?>"
                       data-onew="<?php echo $new_qty; ?>">
                        <small style='color:orange;font-weight:normal;'>* pls change quantity to <?php echo $new_qty; ?>
                        </small>
                    </a>
                    <?php
                }
                ?>
            </div>
            <br style="height:5px;clear:both;" />
            <?php
        } else {
            echo '<hr/><div class="small" title="' . htmlspecialchars($reason) . '"><span class="floatleft ui-icon ui-icon-triangle-1-e"></span><a href="'
            . $img . '">' . $name . '</a> '
            . '<a href="//www.2dodash.com/' . $bt . 'proofs/' . $name . '">PSD</a> '
            . echo_proof_status($status) . ' on ' . $date_modified . '</div>';
        }
    }
    if ($proof_count == 0) {
        $upload_done = FALSE;
        echo "<img class='preview' src='img/no_artwork.png' />";
    }
    echo "<a class='zframe' href='#' "
    . "data-popid='popup' data-action='uploadproof_custom.php?design_model={$model}&nazid=$naz_id&oid=$orderid&acid=$customers_id&companyid=1' "
    . "data-title='Upload Proof Form for model #$model'>Upload Proof</a>";
} else {
    echo '<div class="floatleft" style="width: 50%"><img class="preview" src="img/na_proof.png" /></div>';
    if ($shipped_msg != "") {
        echo '<div class="floatleft" style="width: 50%">' . $shipped_msg . '</div>';
    } elseif ($count_done == true && $shipped != true && $payment_made == true) {
        ?>
        <a class="zframe" href="#" data-popid="popup2"
           data-action="<?php echo "updateorder.php?orderid=$orderid&companyid=1&process=6"; ?>"
           data-title="Shipping for <?php echo $model; ?>"
           data-pid="<?php echo $rowid; ?>"
           data-qty="<?php echo $products_quantity; ?>">
            <button type="button">Ship</button></a>
        <?php
    }
}
?>