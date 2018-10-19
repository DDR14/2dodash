<style>
    input[type="text"], select, textarea, input[type="file"] {font-size: 11px;}        
    .preview{max-height: 100px; max-width: 100px;}
</style>
<table width="100%" style="padding:0" class="box first table" cellspacing="0">
    <?php echo $batch_panel; ?>
    <!--<tr>
        <th>&nbsp;</th>
        <th colspan="3"><i>-Basic Info-</i></th>
        <th colspan="4"><i>-Details-</i></th>
        <th class="end" colspan="2"><i>-Process PSD-</i></th>
    </tr>-->
    <tr>
        <th rowspan="3">&nbsp;</th>
        <th rowspan="3">Image</th>
        <th>Name</th>
        <th>MODEL</th>
        <th colspan="2">CATEGORY</th>
        <th>Price</th>
        <th>Min Qty</th>
        <th>Upload</th>
        <th class='end'>Date added</th>
    </tr>
    <tr>
        <th colspan="2" rowspan="2">Description</th>
        <th>Artworks</th>
        <th>WEBSITE</th>
        <th colspan="2">Price Category</th>
        <th rowspan="2">Shape Assign</th>
        <th class="end" rowspan="2">Commands</th>
    </tr>
    <tr>
        <th colspan="2">Boost visibility</th>
        <th colspan="2">Stock</th>
    </tr>
    <!--End First Header-->
    <?php if ($level == 1 || $level == 2) { ?>
    <form action="catalog_add.php?cPath=<?php echo $cPath; ?>" method="post" enctype="multipart/form-data" >
        <tr class="alt">
            <th rowspan="3">+</th>
            <td rowspan="3" class="last">
        <center>
            <image class="preview" src="img/pop-add-tag.png" />
        </center>
        </td>
        <td><input placeholder="New Tag" name="products_name" type="text" required size="15" /></td>
        <td><input placeholder="AA-000-00" name="products_model" type="text" required="required" style='font-weight: bold;' size="10" /></td>
        <td colspan="2" ><input type="text" name="master_categories_id" value="<?= $cPath; ?>" /></td>
        <td>$
            <input placeholder="0.00" name="products_price" class="rtl" type="text" required="required" style='font-weight: bold;' size="7" /></td>
        <td><input placeholder="25" name="products_quantity_order_min" class="rtl" type="text" required="required" size="3" /></td>
        <td><input style="width:150px;" name="file" type="file" /></td>
        <td><?php echo date("Y-m-d H:i:s"); ?></td>
        </tr>
        <tr class="alt">
            <td colspan="2" rowspan="2" class="last">
                <textarea rows='4' placeholder="For Search Keywords" name="products_description" cols="30" id="products_description"></textarea></td>
            <td>
                <select name="require_artwork" >
                    <?php echo $opt_reqart; ?>
                </select></td>
            <td>
                <select name="manufacturers_id">
                    <option value="">--none--</option>
                    <?php echo $opt_manufacturers; ?>
                </select></td>
            <td colspan="2">
                <select name="discount_qty">
                    <?php echo $optdiscount_qty; ?>
                </select>
            </td><td rowspan="2" class="last"><select name="shape">
                    <option value="">Select Shape</option>
                    <?php echo $opt_shape; ?>
                </select></td>
            <td rowspan="2" class="last"><input type="submit" class="btn btn-green" value="Add Design" /></td>
        </tr>
        <tr class="alt">
            <td colspan="2" class="last"><label>
                    <input type="radio" checked="checked" value="0" name="products_status" />
                    Hide in Boost</label>
                <label>
                    <input type="radio" value="1" name="products_status" />
                    Show in Boost</label></td>
                    <td colspan="2" class="last"><input class="rtl" required name="products_quantity" type="text" /></td>
        </tr>
    </form>
    <?php    
    }
    company_db_connect(1);        
    $result = mysql_query($qry)or die(mysql_error());
        
    if (mysql_numrows($result)>0) {        
        $counter = 0;
        $alt = "";
        while ($row = mysql_fetch_assoc($result)) {
            $products_quantity_order_min = $row['products_quantity_order_min'];
            $products_model = $row['products_model'];
            $products_description = $row['products_description'];
            $products_name = $row['products_name'];
            $products_status = $row['products_status'];
            $require_artwork = $row['require_artwork'];
            $products_id = $row['products_id'];
            $products_image = $row['products_image'];
            $products_price = $row['products_price'];
            $products_quantity = $row['products_quantity'];
            $products_discount_type = $row['products_discount_type'];
            $products_date_added = $row['products_date_added'];
            $manufacturers_id = $row['manufacturers_id'];
            $master_categories_id = $row['master_categories_id'];
            $counter += 1;

            if ($counter % 2 == 0) {
                $alt = "class='alt'";
            } else {
                $alt = "";
            }
            ?>
            <tr><td></td><td class='last' colspan='9'></td></tr>
            <form method='post' action='catalog_update.php?Pid=<?php echo $products_id . "&cPath=" . $cPath?>' enctype='multipart/form-data'>
            <tr <?php echo $alt?> > <th rowspan='3'><?php echo $counter ?></th>
            <td rowspan='3' class='last'> <center><?php echo $products_id ?><image class='preview' src='https://www.boostpromotions.com/images/<?php echo $products_image?>' /> </center> </td>
            <td><input value="<?php echo htmlspecialchars($products_name); ?>" name='products_name' type='text' style='' size='15' /></td>
            <td><input value='<?php echo $products_model ?>' name='products_model' type='text' required='required' style='font-weight:bold' size='10' /></td>
            <td colspan='2'>
<!--                <select name='master_categories_id'>
            <option value=''>Select Category</option>            
            <? echo str_replace("value='$master_categories_id'", "value='$master_categories_id' selected='selected'", $opt_category) ?>
            </select>-->
                <input type="text" name="master_categories_id" value="<?= $master_categories_id; ?>" />
            <?php if ($cPath != $master_categories_id) { ?>
                <small>-linked-</small>
            <?php } ?>
                <input type="hidden" >
            </td> <td>$ <input value='<?php echo $products_price?>' name='products_price' class="rtl" type='text' required='required' style='font-weight:bold' size='7' /></td>
            <td><input value='<?php echo $products_quantity_order_min ?>' class="rtl" placeholder='25' name='products_quantity_order_min' type='text' required='required' size='3' /></td>
            <td><input name='file' type='file' /></td>
            <td><?php echo $products_date_added ?></td> </tr>
            <tr <?php echo $alt ?>> <td colspan='2' rowspan='2' class='last'><textarea rows='5' name='products_description' cols='30' ><?php echo htmlspecialchars($products_description); ?></textarea></td>
            <td> <select name='require_artwork'>
            <?php echo str_replace("value='$require_artwork", "value='$require_artwork' selected='selected'", $opt_reqart) ?>
            </select></td>
            <td> <select name='manufacturers_id'> <option value=''>--none--</option>
            <?php echo str_replace("value='$manufacturers_id'", "value='$manufacturers_id' selected='selected'", $opt_manufacturers) ?>
            </select></td>
            <td colspan='2'><select name='discount_qty'>
            <option value='' >--no change--</option>
            <?php echo $optdiscount_qty ?>
            </select>
            <br/><a target='_blank' href='http://www.youthbowlingawards.com/productsinfo.php?products_id=<?php echo $products_id ?>'>
            view qty discount <span class='floatleft ui-icon ui-icon-extlink'></span></a></td>
            <td rowspan='2' class='last'><select name='shape'> <option value=''>Select Shape</option><?php echo $opt_shape ?></select><p>*This will replace the current image.</p></td>
            <td rowspan='2' class='last'>
            <a target='_blank' title='if master does not exist, please upload the psd file to that directory or update here'
            href='https://www.boostpromotions.com/images/<?php echo $products_model ?>.psd'>Get Master</a><br/><br/>
            <?php if ($level == 1 || $level == 2) { ?>
                <input type='submit' class='btn btn-blue' value='Update' />
                <a href='catalog_delete.php?cPath=<?php echo $cPath ?>&Pid=<?php echo $products_id ?>&image=<?php echo $products_image?>'>
                <button type='button' onclick="return confirm('Are you sure?')" class='btn btn-red btn-small'>Delete</button></a>
            <?php } ?>
            <tr <?php echo $alt ?> ><td colspan='2' class='last'>
            <label><input <?php echo ($products_status == '0' ? "checked='checked'" : "") ?> value='0' type='radio' name='<?php echo $products_id ?>_products_status' />Hide in Boost</label>
            <label><input <?php echo ($products_status == '1' ? "checked='checked'" : "") ?> value='1' type='radio' name='<?php echo $products_id ?>_products_status' />Show in Boost</label>
            </td>
            <td colspan='2' class='last'><input class="rtl" placeholder="9999999" required type='text' value="<?php echo $products_quantity; ?>" name="products_quantity" /></td>
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