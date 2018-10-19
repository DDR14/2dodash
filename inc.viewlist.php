
<?php error_reporting(E_ALL ^ E_DEPRECATED); ?>
<div class="grid_7" >
    <style>
        td>.custom-form{font-size: 8px;}
        .custom-form a{font-size: 11px;}
        .custom-form.stock input[type="text"], .custom-form.stock textarea{background-color: #DFF0D8;}
    </style>
    <?php if ($shipping_method == 'unspecified') { ?>
        <div class="box first message warning">
            <h5>Add Shipping Info:</h5>Shipping is not specified for this order.
            <form action="updateshipping.php?orderid=<?php echo $orderid; ?>&companyid=1" method="POST">
                <input type="hidden" name="orderid" value="<?php echo $orderid; ?>" />
                <input type="hidden" name="process" value="add" />
                <input type="text" required value="Flat Rate (Best Way)" name="shipping_description" size="15" />
                $<input type="text" required value="7.95" placeholder="0.00" name="shipping" size="5" />
                <input type="submit" class="btn btn-yellow" value="Update" />
            </form>
        </div>
        <?php
    }
    if ($hasLanyard && !$isLanyardOrdered) {
        ?>
        <div class="box first message warning">
            <h5>Lanyards to Order:</h5>This order has lanyard(s) in it, push the button if you already ordered lanyard(s) from China.
            <form action="addnote.php?orderid=<?php echo $orderid; ?>&companyid=1" method="POST">
                <input type="hidden" name="note" value="<?php echo 'LANYARDS_ORDERED ' . date('d/m/Y'); ?>"/>
                <br />
                <input type="submit" class="btn btn-yellow" value="Yes, Lanyards are Ordered" />
            </form>
        </div>
        <?php
    }
    if ($loworder_amount == 0 && $subtotal_amount < 25 && $ship_done != true) {
        ?>
        <div class="box first message error">
            <h5>Low Order Fee Required:</h5>The sub total of this order is lower than $25.00.
            <?php if ($onetime_charges == 10) { ?>
                <br/><b>You have set One Time charges to $10 (which may be <i>the</i> low order fee) If it is, set it to 0 and press the button below instead.</b>
            <?php } ?>
            <form action="updateloworder.php" method="POST">
                <input type="hidden" name="orderid" value="<?php echo $orderid; ?>" />
                <input type="hidden" name="process" value="add" />
                <input type="submit" class="btn btn-red" value="Add low order fee of $10.00" />
            </form>
        </div>
    <?php } elseif ($loworder_amount != 0 && $subtotal_amount > 25 && $ship_done != true) { ?>
        <div class="box first message error">
            <h5>Low Order Fee Detected:</h5>There is a low order fee even though the products total is greater than $25.
            <form action="updateloworder.php" method="POST">
                <input type="hidden" name="orderid" value="<?php echo $orderid; ?>" />
                <input type="hidden" name="process" value="remove" />
                <input type="submit" class="btn btn-red" value="Remove Low Order Fee" />
            </form>
        </div>
        <?php
    }
    if ($count_done == true && $ship_done != true && $payment_made == true) {
        ?>
        <div class='box first message info'>
            <h5>Items are now ready to be shipped.</h5>
            Please select which items will be shipped by checking the checkbox before each item.
            <label>Check All<input type="checkbox" class="checker" /></label>
            <form id="shipby_item" action="updateorder.php?orderid=<?php echo $orderid; ?>&companyid=1&process=6"
                  method="POST" onSubmit="return shipbyItem(this)">
                <select required name="ship_type">
                    <option value="">select type</option>
                    <option>UPS</option>
                    <option>USPS</option>
                </select>
                <input required name="tracking" type="text" placeholder="Enter Tracking Number" />
                <input type="submit" class="btn btn-black" value="Ship Checked" />
            </form>
        </div>
        <?php
    }
    connectToSQL();
    $orderid = mysql_real_escape_string($_GET['orderid']);

    function echo_proof_status($status) {
        switch ($status) {
            case '0': $status = '<strong style="color:#EFE000;">[Awaiting Approval]</strong>';
                break;
            case '1': $status = '<strong style="color:green;">[Artwork Approved!]</strong>';
                break;
            case '3': $status = '<strong style="color:red;">[Artwork Rejected]</strong>';
                break;
        }
        return $status;
    }

// Get the selected design img folder directory
    function getImgDir($model) {
        $type = explode('-', $model);
        $dir = ["AC" => "Academic",
            "PRE" => "Kinder",
            "CCA" => "CCA",
            "AW" => "Awards",
            "AR" => "Art",
            "AT" => "Attendance",
            "BE" => "Behavior",
            "HB" => "Birthday",
            "HO" => "Holiday",
            "BT" => "Box Top",
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
            "X" => "X",
            "MI" => "Medical"];

        return $dir[$type[0]];
    }

    $option = "";
    $result = mysql_query("SELECT * FROM `proof_settings`") or trigger_error(mysql_error());

    while ($row = mysql_fetch_array($result)) {
        $id_opt = nl2br($row['id']);
        $setting_opt = nl2br($row['setting']);
        $option = $option . "<option value=\"" . $id_opt . "\">" . $setting_opt . "</option>";
    }

    mysql_close();
    company_db_connect(1);
    ?>

    <table style="padding:0" width="100%" class="box first table <?php echo ($shipped == true || $readonly == true) ? "shipped" : ""; ?>" cellspacing="0" cellpadding="0">
        <tr>
            <th></th>
            <th colspan="4"><em>Main</em></th>
            <th colspan="2"><em>Customizations</em></th>
            <th><em>Proofs</em></th>
        </tr>
        <tr>
            <th rowspan="4">&nbsp;</th>
            <th rowspan="4"><input type="checkbox" class="checker" /></th>
            <th rowspan="4">Image</th><th colspan="2">Model</th>
            <th>Title</th>
            <th rowspan="2">Custom</th>
            <th rowspan="4">Proofs list</th>
        </tr>
        <tr>
            <th>Price</th>
            <th>Qty</th>
            <th>Footer</th>
        </tr>
        <tr>
            <th colspan="2">Total</th>
            <th>Background</th>
            <th>Get PSD</th>
        </tr>
        <tr>
            <th colspan="2">Controls</th>
            <th>Image</th>
            <th>Website</th>
        </tr>
        <?php
        //ADD NEW ITEM
        if ($shipped == true || $readonly == true) {
            //make this constantly false.
            //they want to be able to edit until shipped
        } else {
            ?>
            <!-- add an item -->
            <form action="add_item_order.php?orderid=<?php echo $orderid; ?>&companyid=1" method="POST" >
                <tr>
                    <th rowspan="3">+</th>
                    <td rowspan="3" class="last"></td>
                    <td rowspan="3" class='last'><a id="opener" style="text-align: center" href="#">Select Model<br/><img height="110px" class="floatleft" src="img/pop-add-tag.png" /></a>
                    </td>
                    <td colspan="2"><input required style='font-weight:bold;' type="text" name="modelNumber" size="12" /></td>
                    <td rowspan="3" class="last"><div class='custom-form'>TITLE:
                            <input type='text' name='title' />
                            <br />
                            FOOTER:
                            <input type='text' name='footer' />
                            <br />
                            BACKGROUND:
                            <input type='text' name='background' />
                        </div></td>
                    <td rowspan="3" class="last end"><div class='custom-form'>CATCHALL CUSTOMS:
                            <textarea rows="4" type="text" name="customs" ></textarea>
                            WEBSITE:<br/>
                            <input type="text" name="website" />
                        </div></td>
                    <td rowspan="3" class="last"><div class="message info">Proof List Goes here..</div></td>
                </tr>
                <tr>
                    <td>$<input type="text" size="5" name="price" /></td>
                    <td>x<input type="text" name="qty" size="2" /></td>
                </tr>
                <tr>
                    <td colspan="2" class="last"><input class="btn btn-green" type="submit" value="Add Item" /></td>
                </tr>
            </form>

            <?php
        }

        //Display the tags and all relevant information
        $qry = "SELECT a.products_shipping, a.products_shipping_date, a.orders_products_id, a.orders_id, a.products_id, a.products_model, a.products_name, "
                . "d.products_image, d.manufacturers_id, d.require_artwork, d.master_categories_id, a.products_price, a.final_price, a.products_tax, a.products_quantity, a.products_priced_by_attribute, a.product_is_free, a.products_discount_type, a.products_discount_type_from, a.products_prid, "
                . "b.id, b.order_id, b.date, b.model, b.title, b.footer, b.background, b.upload, b.customs, b.website "
                . "FROM zen_orders_products a "
                . "INNER JOIN zen_products d "
                . "ON a.products_id = d.products_id "
                . "LEFT JOIN naz_custom_co b "
                . "ON a.orders_products_id = b.orders_products_id "
                . "AND b.order_id = a.orders_id "
                . "WHERE a.orders_id ='$orderid' "
                . "ORDER BY a.orders_products_id DESC";
        $products = mysql_query($qry)or die(mysql_error());

        $counter = 0;
        $alt = "";
        $upload_done = TRUE;
        $proofs = [];

        while ($row = mysql_fetch_assoc($products)):
            echo "<tr>";
            $rowid = $row['orders_products_id'];
            $products_id = $row['products_id'];
            $products_model = $row['products_model'];
            $products_name = $row['products_name'];
            $products_image = $row['products_image'];
            $master_categories_id = $row['master_categories_id'];
            $products_price = $row['products_price'];
            $final_price = $row['final_price'];
            $products_tax = $row['products_tax'];
            $products_quantity = $row['products_quantity'];
            $products_discount_type = $row['products_discount_type'];
            $products_prid = $row['products_prid'];
            $naz_id = $row['id'];

            $website = $row['website'];
            $customs = $row['customs'];
            $model = $row['model'];
            $title = $row['title'];
            $footer = $row['footer'];
            $background = $row['background'];
            $upload = $row['upload'];

            $date = $row['date'];
            $total = $products_price * $products_quantity;

            $products_shipping = $row['products_shipping'];
            $products_shipping_date = $row['products_shipping_date'];

            $require_artwork = $row['require_artwork'];

            //booster tags directory to add
            $bt = '';
            if ($row['manufacturers_id'] == 15) {
                $bt = 'boostertags/';
            }

            $counter += 1;
            $alt = '';
            if ($counter % 2 == 0) {
                $alt = "class='alt'";
            }
            $stock = '';
            if ($customs . $title . $footer . $background . $upload == '') {
                $stock = 'stock';
            }

            // Explode the products_model to get the product type
            $explodedModel = explode("-", $products_model, 2);
            $isVersion2 = (in_array($explodedModel[0], ['STOCK', 'MODIFIED', '2STOCK', '2SIDESD'])) || $title == '[[v2_proof:on]]';
            // catchall contains special code
            ?>
            <tr><td class='last' colspan='8'></td></tr>
            <?php
            if ($shipped == false && $readonly == false) {
                echo "<form action=\"editorder.php?orderid=" . $orderid . "&naz_id=" . $naz_id . "&companyid=1&rowid=" . $rowid . "\" method=\"POST\">";
            }
            ?>
            <tr <?php echo $alt; ?> >
                <th rowspan='3'><?php echo $counter ?>
                </th><td style='vertical-align: middle;' class='last' rowspan='3'>
                    <?php
                    if ($products_shipping == '0' && $shipped != true) {
                        echo "<input value='$rowid' type='checkbox' name='item_selection' />";
                    } else {
                        echo "<input disabled type='checkbox' checked />";
                    }
                    ?>
                </td>
                <td class="last" rowspan="3" ><div><?php echo $rowid ?></div>
                    <img class="preview" src="<?= ('https://www.boostpromotions.com/images/' . $products_image); ?>" />
                </td>
                <td height="21" colspan="2">
                    <input style="font-weight:bold;" type="text" name="products_model" size="15" value="<?php echo $products_model ?>" />
                </td>

                <?php
                $shipped_msg = "";
                ?>

                <td class="last" rowspan="3">
                    <div class="custom-form <?= $stock; ?>">TITLE:<br /><input type="text" name="title" value ="<?php echo htmlspecialchars($title) ?>" /><br />
                        FOOTER: <br />
                        <input type="text" name="footer" value="<?php echo htmlspecialchars($footer) ?>" /><br />
                        BACKGROUND:<br />
                        <input type="text" name="background" value ="<?php echo htmlspecialchars($background) ?>" /><br />
                        IMAGE: 
                        <?php
                        if (!$upload):
                            echo "No Image uploaded";
                        else:
                            foreach (explode(',', $upload) as $upld):
                                ?>
                                <a target="_blank" <?= "href='http://www.boostpromotions.com/images/uploads/" . ($upld) . "'"; ?> >
                                    <?= $upld; ?></a>
                                <?php
                            endforeach;
                        endif;
                        ?>
                    </div>
                </td>                    
                <td rowspan='3' class='last end'>
                    <div class='custom-form <?= $stock; ?>'>
                        CATCHALL CUSTOMS:
                        <textarea rows='4' name='catchall_custom'><?php echo htmlspecialchars($customs) ?></textarea>
                        <?php
                        if (substr($products_model, 0, 3) === "TK-"):
                            $download_link = "tool_tk_helper.php?tk_list=" . urlencode($customs);
                        elseif (in_array($explodedModel[0], ['STOCK', 'MODIFIED', 'CUSTOM', '2STOCK'])):
                            $download_link = "https://www.boostpromotions.com/Templates/" . strtolower($explodedModel[1]) . ".psd";
                        else:
                            $download_link = "https://www.boostpromotions.com/images/$products_model.psd";
                        endif;
                        ?>                            
                        <a target='_blank' style='display: block; text-align: center;' 
                           href='<?= $download_link; ?>'>Download PSD File</a>

                        WEBSITE:<br/>
                        <input type="text" name="website" value="<?php echo htmlspecialchars($website); ?>" />
                    </div>

                </td>
                <td rowspan='3' class='last' style="width: 30%;" >
                    <?php
                    if (!$isVersion2):
                        $proof_optional_qry = "";
                        require 'inc.viewproof.php';
                    else:
                        ?>
                        <div class="message warning">
                            See Models and Proofs Below: <br/>
                            <small>(Selected Models and Quantities are based on the catchall customs field.)</small>
                        </div>
                    <?php endif;
                    ?>
                </td>
            </tr>

            <tr height="24" <?php echo $alt; ?>>
                <td>$<input type="text" name="products_price" size="5" value="<?php echo $products_price ?>" /></td>
                <td>x<input type="text" autocomplete="off" name="products_quantity" size="2" value="<?php echo $products_quantity; ?>" />
                    <input type="hidden" name="prev_quantity" value="<?php echo $products_quantity; ?>" />
                </td></tr>
            <tr <?php echo $alt; ?>>
                <td colspan='2' class='last'><input type='text' disabled value='$ <?php echo $total; ?>' size='10' />
                    <?php
                    if ($shipped == false && $readonly == false) {
                        ?>
                        <br/><br/>
                        <input class='btn btn-blue' onclick="return confirm('Update?')" name="update" type="submit" value="Update" style='margin-right: 10px' />
                        <input class='btn btn-red' onclick="return confirm('Are you sure want to delete this?')" name="delete" type="submit" value="Delete" />
                        <?php
                        if (!$isVersion2 && in_array($explodedModel[0], ['CUSTOM'])) {
                            ?><br/><br/>
                            <input class='btn btn-navy btn-small' 
                                   onclick="return confirm('This tool does NOT automatically update pricing \nTitle field must be empty to insert this code [[v2_proof:on]] to enable multi proof uploading \nProceed?')" 
                                   name="2Sided" type="submit" value="Make 2 sided tag" />
                                   <?php
                               }
                           }
                           ?></td>
            </tr>
            <?php
            if ($shipped == false && $readonly == false) {
                echo "</form>";
            }
            echo "</tr>";
            /*             * ===============================================END ROW==============================================* */
            if ($isVersion2):
                // ARRAY PLAY
                $designs_only = [];
                $designandqty = [];
                foreach (explode(",", $customs) as $design) {
                    if (strpos($design, '=') === false) {
                        continue;
                    }
                    list($k, $v) = explode("=", $design);
                    $s = '';
                    if (strpos($k, '>') !== false) {
                        list($k, $s) = explode(">", $k);
                    }

                    $designandqty[strtoupper(trim($k))] = ['qty' => $v, 'shp' => $s];
                    $designs_only[] = trim($k);
                }

                $qry_param = join("','", $designs_only);
                //i still need to get the image and psd links through database :*(
                $query = "SELECT b.products_image, b.products_model 
                    FROM zen_designs b
                    WHERE b.products_model IN ('$qry_param')
                    ORDER BY b.products_model";
                $customs_result = mysql_query($query)or die(mysql_error());
                ?>
                <tr>
                    <td class="last" colspan="8">
                        <table class="stocktag-table" style="margin:0;">
                            <tr>
                                <?php
                                for ($x = 0; $x < mysql_numrows($customs_result) && $x < 3; $x++):
                                    ?>
                                    <th width="50px">SELECTED MODEL</th>
                                    <th>PROOF</th>
                                    <th></th>
                                    <?php
                                endfor;
                                ?>
                            </tr>
                            <tr>                                
                                <?php
                                $odd = 0;
                                while ($cr = mysql_fetch_assoc($customs_result)):
                                    ?>
                                    <td>
                                        <img src="https://www.boostpromotions.com/images/designs/Resize/<?= getImgDir($cr['products_model']); ?>/<?= $cr['products_image'] ?>"
                                             class="preview" style="width: 55px; margin-right: 10px !important;"
                                             />
                                        <p>
                                            <b><?= $cr['products_model'] ?></b> <?= $designandqty[$cr['products_model']]['shp'] ?>
                                            <br />
                                            <?= $designandqty[$cr['products_model']]['qty'] ?> pcs.
                                            <br/>
                                            <a href="https://www.boostpromotions.com/images/designs/<?= getImgDir($cr['products_model']); ?>/<?= str_replace('png', 'psd', $cr['products_image']) ?>">
                                                Get PSD</a>
                                        </p>
                                    </td>
                                    <td>
                                        <?php
                                        //call proofy system form
                                        $model = $cr['products_model'];
                                        $products_quantity = $designandqty[$model]['qty'];
                                        $proof_optional_qry = "AND design_model = '{$cr['products_model']}' ";
                                        require 'inc.viewproof.php';
                                        ?>
                                    </td>
                                    <td class="end"></td>
                                    <?php
                                    //line break;
                                    $odd++;
                                    if ($odd % 3 == 0) {
                                        echo '</tr><tr><td class="last" colspan="8" ></td></tr><tr>';
                                    }
                                endwhile;
                                ?>
                            </tr>
                        </table>
                    </td>
                </tr>
                <?php
            endif;
        endwhile;
        ?>
    </table>
    <!--MODALS -->
    <?php if ($shipped != true) { ?>
        <div style='display:none'>
            <div id="dialog" title="This customer also ordered..">
                <p>
                    Previous order of this customer </p>
                <?php
                $qry = "SELECT a.products_model, b.orders_id FROM `zen_orders_products` a "
                        . "INNER JOIN zen_orders b ON a.orders_id = b.orders_id "
                        . "WHERE b.customers_id = '" . $customers_id . "' "
                        . "AND a.orders_id <> '" . $orderid . "';";
                $result = mysql_query($qry)or die(mysql_error());

                while ($row = mysql_fetch_assoc($result)) {
                    $products_model = $row['products_model'];
                    $orders_id = $row['orders_id'];

                    echo "<div>$products_model $orders_id</div>";
                }
                ?>
            </div>
            <div id="popup">
                <form action="" method="POST" enctype="multipart/form-data">
                    <p>Choose the type of Tag you are uploading, Upload an image, then hit upload proof.</p>
                    <p><i><small>Except the Dog Tags, other shapes are not correctly configured yet. you may insert a new row by clicking "configure". [also has a setting tester]</small></i></p>
                    <p><select name="selection" ><?php echo $option; ?>
                        </select>
                        <a class='xframe' href='proof_settings_list.php' data-title='Upload Proof Configuration'
                           data-width='400' data-height='500'>-configure-</a>
                    </p>
                    <input required type="file" name="file" id="file"/>
                    <hr/>
                    <input class="btn btn-maroon" type="submit" value="Upload Proof"/>
                </form>
            </div>
            <div id="popup2">
                <form action="" method="post" onsubmit="return orderConfirm(this)">
                    <p>Fill out shipping information for this item only.</p>
                    <select required="" name="ship_type">
                        <option value="">select type</option>
                        <option>UPS</option>
                        <option>USPS</option>
                    </select>
                    <input required="" name="tracking" type="text" placeholder="Enter Tracking Number"/>
                    <input type="hidden" id="pid" name="orders_pid"/>
                    <input type="hidden" id="qty" name="ordered_qty" />
                    <label><input checked="checked" type="radio" name="partial" value="0" /> Full</label>
                    <label><input type="radio" name="partial" value="1" /> Partial</label>
                    <div id="competeyes" ></div>
                    <br/><br/>
                    <script>
                        //radio toggle
                        $(function () {
                            $('input[name="partial"]').bind('change', function () {
                                if ($(this).val() === "1") {
                                    var oqty = $('#qty').val();
                                    $('#competeyes').append('Quantity: <br/><input required min=25 max=' + oqty
                                            + ' style="width:80px" type="number" name="shipping_partial_quantity" placeholder="'
                                            + oqty + '"  />');
                                } else {
                                    $('#competeyes').empty();
                                }
                            });
                        });
                    </script>
                    <input type="submit"  class="btn btn-blue" value="Ship this item" />
                </form>
            </div>
            <div id="popup3">
                <form method="post" action="">
                    This customer requested to change quantity to
                    <input class="small" size="4" name="new_qty" id="onew" reqiured type="text" value="" />
                    <input class="small" type="submit" onclick="confirm('Are you sure?')" />
                </form>
            </div>
        </div>
    <?php } ?>
    <div class="box round">
        <div class="floatright">
            <?php
            if ($art_done == false) {
                ?>
                <!-- One by One Graphics upload -->
                When you are done uploading proofs per tag, press this to notify the
                <br />
                customer and update status to await customer response.
                <br/>
                <?php
                if ($upload_done != FALSE) {
                    ?>
                    <form onsubmit="return orderConfirm(this)" action="proofdone.php?oid=<?php echo $orderid; ?>&acid=<?php echo $customers_id; ?>&companyid=1" method="POST" enctype="multipart/form-data">
                        <input onclick="return confirm('Are you sure all uploaded proofs are correct?')" class="btn btn-purple" type="submit" value="Done Uploading Proofs/Tag - Send Email" />
                    </form>
                    <?php
                } else {
                    ?>
                    <input onclick="return alert('Oops. It seems that some items doesnt have their proofs uploaded yet.')" class="btn btn-grey" type="button" value="Done Uploading Proofs/Tag - Send Email" />
                    <?php
                }
                ?>

                <!--REMOVED OLD UPLOAD FORM-->
                <?php
            } else {
                ?>
                <form action="download_zip.php" method="GET" target="_blank">
                    <input type="hidden" name="oid" value="<?= $orderid; ?>" />
                    <input type="hidden" name="proofs" value="<?= htmlspecialchars(serialize($proofs)); ?>" />
                    <button type="submit" name="zip" class="btn btn-navy btn-icon btn-print" ><span></span>Download All Proofs</button>
                    <button type="submit" name="csv" class="btn btn-black btn-icon btn-star" ><span></span>Download All Codes</button>
                    <br/>
                    <br/>
                    <button type="submit" name="unplugged" class="btn btn-pink btn-icon btn-star" ><span></span>Unplugged Challenge Codes</button>
                    <button onclick="return confirm('activate codes as valid for gowin website?')" 
                            class="btn btn-dollar btn-pink" type="submit" name="activate" >Activate</button>
                </form>
                <?php
            }
            ?>
        </div>
        <?php
        //lets get proofs that is not indexed to the order product for really really old orders
        //this seems to get all rows.
        $qry = "SELECT * FROM proofs WHERE order_id='" . $orderid . "' AND naz_custom_id=0 ORDER BY name";
        $result = mysql_query($qry)or die(mysql_error());
        ?>

        <br /><br />
        <?php
        echo "<table><caption>EXTRA PROOF FILES</caption><th>Proof Name</th><td>&nbsp;</td><th>Reason</th><th>Status</th></tr>";
        while ($row = mysql_fetch_assoc($result)) {
            $name = $row['name'];
            $reason = $row['reason'];
            $status = $row['status'];

            echo "<tr><td><a href=\"http://www.2dodash.com/" . $bt . "proofs/" . $name . "\" target=\"_NEW\">"
            . $name . "</a></td><td></td><td>" . $reason . "</td><td>" . echo_proof_status($status) . "</td></tr>";
        }
        echo "</table>";
        ?>
    </div>
    <?php
    // Get only First, and show the rest below
    $qry = "SELECT b.orders_status_name, a.date_added, a.comments "
            . "FROM zen_orders_status_history a "
            . "INNER JOIN zen_orders_status b "
            . "ON a.orders_status_id = b.orders_status_id "
            . "WHERE a.orders_id='$orderid' ORDER BY a.orders_status_history_id DESC";

    $result = mysql_query($qry)or die(mysql_error());
    echo "<div class='box message warning'>";
    while ($row = mysql_fetch_assoc($result)) {
        $date_added = $row['date_added'];
        $comments = $row['comments'];
        $orders_status_name = $row['orders_status_name'];

        echo "$date_added - <i>$orders_status_name:</i> <strong style='color:red;'>"
        . "$comments</strong><br />";
    }
    echo "</div>";
    mysql_close();
    ?>

    <a class="btn-icon btn-teal btn-print" href="invoice/index.php?orderid=<?php echo $orderid; ?>&companyid=1" target="_blank"><span></span>View Quote</a>
    <a class="btn-icon btn-teal btn-dollar" href="invoice/index.php?orderid=<?php echo $orderid; ?>&companyid=1&invoice=1" target="_blank" ><span></span>View Invoice</a>
    <?php if ($level == 1) { ?>
        <script>
            function deleteForReal() {
                var msg = prompt('Are you sure you want to delete this order? \n\
    this action cannot be undone.\n Please delete all uploaded proofs, po`s or checks, unplugged codes, and payment must also be removed before deletion.\n\
    Please enter the order number to proceed');
                if (msg === '<?php echo $orderid; ?>') {
                    return true;
                }
                return false;
            }
            $(function () {
                $(document).tooltip({
                    track: true
                });
            });
        </script>
        <form class="floatright" action="deleteorder.php?orderid=<?php echo $orderid; ?>&companyid=1" method="POST">
            <button class="btn-icon btn-red btn-cross" type="submit" onclick="return deleteForReal()" ><span></span>Delete Order</button></form>
    <?php } if ($level == 1 || $level == 2) { ?>
        <form class="floatright" action="cancelorder.php?orderid=<?php echo $orderid; ?>&companyid=1" method="POST"><button onclick="return confirm('You are about to cancel this order.\n\nThis will come back to orders folders when status is changed by uploading paying etc.\n\nProceed?')" class="btn-icon btn-pink btn-minus" type="submit"><span></span>Cancel Order</button></form>
        <br /><br />
    <?php } ?>
</div>
