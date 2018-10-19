<div class="block" id="section-menu">
    <ul class="section menu">
        <?php
        //these menu items will be orders arranged by date fully approved and paid.
        //New Menu and counter
        company_db_connect(1);

        $qry = "SELECT "
                . "(SELECT COUNT(1) FROM zen_orders WHERE orders_id>='0') AS 'EVERYTHING',"
                . "(SELECT COUNT(1) FROM zen_orders WHERE shipped<>'0') AS 'ALLSHIPPED',"
                . "(SELECT COUNT(*) FROM (SELECT DISTINCT z.order_id FROM naz_custom_co z
INNER JOIN proofs y ON y.naz_custom_id = z.id INNER JOIN zen_orders x ON x.orders_id = y.order_id WHERE x.orders_status = 2
GROUP BY z.orders_products_id HAVING MIN(y.status) = 3) AS a) AS 'REJECT',"
                . "(SELECT COUNT(DISTINCT a.order_id) FROM proofs a INNER JOIN naz_custom_co c ON a.naz_custom_id = c.id INNER JOIN zen_orders_products b ON c.orders_products_id= b.orders_products_id INNER JOIN zen_orders d ON b.orders_id = d.orders_id WHERE a.new_qty<> 0 AND a.new_qty <> b.products_quantity AND a.status<>3 AND b.orders_id > 1900 AND d.orders_status <> '15') AS 'QUANUPDATE',"
                . "(SELECT COUNT(1)  FROM zen_orders WHERE artwork_approved='1' AND orders_status <> '15' AND follow_up < 3) AS 'PING',"
                . "(SELECT COUNT(1)  FROM zen_orders WHERE artwork_approved='1' AND orders_status <> '15' AND follow_up >=3) AS 'PINGNR',"
                . "(SELECT COUNT(1)  FROM zen_orders WHERE artwork_approved='0' AND inhouse=0 AND orders_status <> '15') AS 'PONG',"
                . "(SELECT COUNT(1)  FROM zen_orders WHERE artwork_approved<>'0' AND artwork_approved<>'1' AND printed='0' AND payment_made <> '0' AND orders_status <> '15') AS 'PRINT',"
                . "(SELECT COUNT(1)  FROM zen_orders WHERE artwork_approved<>'0' AND artwork_approved<>'1' AND payment_made='0' AND orders_status <> '15' AND follow_up < 3) AS 'INVOICE',"
                . "(SELECT COUNT(1)  FROM zen_orders WHERE artwork_approved<>'0' AND artwork_approved<>'1' AND payment_made='0' AND orders_status <> '15' AND follow_up >=3) AS 'INVOICENR',"
                . "(SELECT COUNT(1)  FROM zen_orders WHERE artwork_approved<>'0' AND artwork_approved<>'1' AND printed<>'0' AND payment_made <> '0' AND printed<>'0' AND laminated='0' AND orders_status <> '15') AS 'LAMINATE',"
                . "(SELECT COUNT(1)  FROM zen_orders WHERE artwork_approved<>'0' AND artwork_approved<>'1' AND printed<>'0' AND payment_made <> '0' AND printed<>'0' AND laminated <>'0' AND cut='0' AND orders_status <> '15') AS 'CUT',"
                . "(SELECT COUNT(1)  FROM zen_orders WHERE artwork_approved<>'0' AND artwork_approved<>'1' AND printed<>'0' AND payment_made <> '0' AND printed<>'0' AND laminated <>'0' AND cut<>'0' AND counted='0'  AND orders_status <> '15') AS 'COUNT',"
                . "(SELECT COUNT(1)  FROM zen_orders WHERE artwork_approved<>'0' AND artwork_approved<>'1' AND printed<>'0' AND payment_made <> '0' AND printed<>'0' AND laminated <>'0' AND cut<>'0' AND counted<>'0' AND shipped='0' AND orders_status <> '15') AS 'SHIP',"
                . "(SELECT COUNT(DISTINCT a.orders_id) FROM zen_orders a
INNER JOIN zen_orders_products b 
ON a.orders_id = b.orders_id
WHERE a.artwork_approved <>'0' AND artwork_approved <>'1' AND a.printed <>'0' 
AND a.payment_made <> '0' AND a.printed <>'0' AND a.laminated <>'0' 
AND a.cut <> '0' AND a.shipped ='0' AND a.orders_status <> '15' AND b.products_shipping <> '0') AS 'BACKORDER',"
                . "(SELECT COUNT(1)  FROM zen_orders WHERE ponumber<>'' AND popaid='' AND shipped<>'0' AND orders_status <> '15') AS 'NET30',"
                . "(SELECT COUNT(1)  FROM zen_orders WHERE artwork_approved='0' AND inhouse=1 AND orders_status <> '15') AS 'INHOUSE',"
                . "(SELECT COUNT(1) FROM zen_orders b WHERE b.orders_id IN (SELECT a.orders_id FROM zen_orders_products a WHERE a.products_id IN ('990', '853')) AND b.artwork_approved NOT IN('0', '1') AND b.payment_made <> '0' AND b.shipped='0' AND b.orders_status <> '15' AND b.orders_id NOT IN (SELECT c.orders_id FROM zen_orders_notes c WHERE c.note LIKE 'LANYARDS_ORDERED%')) AS 'LANYARD',"
                . "(SELECT COUNT(1) FROM zen_orders b WHERE b.orders_id IN (SELECT a.orders_id FROM zen_orders_products a WHERE a.products_id IN ('990', '853')) AND b.artwork_approved NOT IN('0', '1') AND b.payment_made <> '0' AND b.shipped='0' AND b.orders_status <> '15' AND b.orders_id IN (SELECT c.orders_id FROM zen_orders_notes c WHERE c.note LIKE 'LANYARDS_ORDERED%')) AS 'LANYARDSHIP',"
                . "(SELECT COUNT(1) FROM zen_orders b WHERE b.orders_id IN (SELECT a.orders_id FROM zen_orders_products a WHERE a.products_id IN ('2609', '2610', '2611', '1047')) AND b.artwork_approved NOT IN('0', '1') AND b.payment_made <> '0' AND b.shipped='0' AND b.orders_status <> '15') AS 'KEYFOBSHIP',"
                . "(SELECT COUNT(DISTINCT a.orders_id) FROM po_receipts a INNER JOIN zen_orders b ON a.orders_id = b.orders_id WHERE a.status = '0' AND orders_status <> '15') AS 'PENPO',"
                . "(SELECT COUNT(1) FROM zen_orders WHERE ship_by NOT IN('0','shipped','') AND orders_status NOT IN (9, 15)) AS 'SHIPBY',"
                . "(SELECT COUNT(1) FROM zen_customers a WHERE a.customers_id IN (SELECT b.customers_id FROM zen_transactions b 
                    LEFT JOIN boostpr1_tododash.charges c
                    ON c.txn_id = b.txn_id                    
                    WHERE b.txn_type <> 'Refund'
                    AND c.txn_id IS NULL
                    AND a.customers_id = b.customers_id
                    AND b.ref_no NOT LIKE '%REF%')
                    OR a.customers_id IN (SELECT a.customers_id FROM zen_orders a INNER JOIN boostpr1_tododash.charges b ON a.orders_id = b.orders_id AND a.order_total < b.amount)
            ) AS 'CCREDITS',"
                . "(SELECT COUNT(1) FROM zen_customers a WHERE a.customers_id IN (SELECT b.customers_id FROM zen_transactions b 
	WHERE b.txn_type = 'Credit'
        AND a.customers_id = b.customers_id
        AND b.ref_no LIKE '%REF%'
	)) AS 'CREFCREDITS',"
                . "(SELECT COUNT(DISTINCT a.orders_id) FROM check_receipts a INNER JOIN zen_orders b ON a.orders_id = b.orders_id WHERE a.status = '0' AND orders_status <> '15') AS 'PENCHECK'";

        $result = mysql_query($qry)or die(mysql_error());

        $average = 0;

        while ($row = mysql_fetch_assoc($result)) {
            $EVERYTHING = $row['EVERYTHING'];
            $ALLSHIPPED = $row['ALLSHIPPED'];
            $PING = $row['PING'];
            $PINGNR = $row['PINGNR'];
            $PONG = $row['PONG'];
            $PRINT = $row['PRINT'];
            $INVOICE = $row['INVOICE'];
            $INVOICENR = $row['INVOICENR'];
            $LAMINATE = $row['LAMINATE'];
            $COUNT = $row['COUNT'];
            $CUT = $row['CUT'];
            $SHIP = $row['SHIP'];
            $BACKORDER = $row['BACKORDER'];
            $NET30 = $row['NET30'];
            $REJECT = $row['REJECT'];
            $PENPO = $row['PENPO'];
            $LANYARD = $row['LANYARD'];
            $LANYARDSHIP = $row['LANYARDSHIP'];
            $KEYFOBSHIP = $row['KEYFOBSHIP'];
            $INHOUSE = $row['INHOUSE'];
            $PENCHECK = $row['PENCHECK'];
            $SHIPBY = $row['SHIPBY'];
            $QUANUPDATE = $row["QUANUPDATE"] == '0' ? '' : '<small>(' . $row["QUANUPDATE"] . ')</small>';
            $CCREDITS = $row['CCREDITS'];
            $CREFCREDITS = $row['CREFCREDITS'];
        }
        mysql_close();
        ?>
        <li><a class="menuitem">ORDERS</a>
            <ul class="submenu current">
                <?php
                $warn = $LANYARD > 0 ? "style='color:red;'" : '';
                $sidebar = "<li><a href='dashboard.php?display=EVERYTHING'>All Orders <small>($EVERYTHING)</small></a></li>
                    <li><a href='dashboard.php?display=ALLSHIPPED'>-All Shipped Orders <small>($ALLSHIPPED)</small></a></li>
                <li><a href='dashboard.php?display=REJECT'>Rejected Proofs <small>($REJECT)</small></a></li>
                <li><a href='dashboard.php?display=QUANUPDATE'>Quantity Update $QUANUPDATE</a></li>
                <li><a href='dashboard.php?display=PING'>Pending Proofs <small>($PING)</small></a></li>
                <li><a href='dashboard.php?display=PINGNR'>- No Response <small>($PINGNR)</small></a></li>
                <li><a href='dashboard.php?display=PONG'>Graphics <small>($PONG)</small></a></li>
                <li><a href='dashboard.php?display=INHOUSE'>In House Graphics <small>($INHOUSE)</small></a></li>
                <li><a href='dashboard.php?display=INVOICE'>Pending Payment <small>($INVOICE)</small></a></li>   
                <li><a href='dashboard.php?display=INVOICENR'>- No Response <small>($INVOICENR)</small></a></li> 
                <li><a href='dashboard.php?display=PRINT'>Printing <small>($PRINT)</small></a></li>        
                <li><a href='dashboard.php?display=LAMINATE'>Laminating <small>($LAMINATE)</small></a></li>
                <li><a href='dashboard.php?display=CUT'>Cutting <small>($CUT)</small></a></li>      
                <li><a href='dashboard.php?display=COUNT'>Counting <small>($COUNT)</small></a></li>    
                <li><a href='dashboard.php?display=SHIP'>Shipping <small>($SHIP)</small></a></li>
                <li><a href='dashboard.php?display=BACKORDER'>-Back Orders <small>($BACKORDER)</small></a></li>
                <li><a href='dashboard.php?display=NET30'>Purchase Orders <small>($NET30)</small></a></li>
                <li><a href='dashboard.php?display=PENPO'>Pending PO <small>($PENPO)</small></a></li>
                <li><a href='dashboard.php?display=PENCHECK'>Pending Checks <small>($PENCHECK)</small></a></li>
                <li><a $warn href='dashboard.php?display=LANYARD'>Lanyards to Order <small>($LANYARD)</small></a></li>
                <li><a href='dashboard.php?display=LANYARDSHIP'>Lanyards to Ship <small>($LANYARDSHIP)</small></a></li>
                <li><a href='dashboard.php?display=KEYFOBSHIP'>Key fobs to Ship <small>($KEYFOBSHIP)</small></a></li>
                <li><a href='dashboard.php?display=SHIPBY'>Ship By <small>($SHIPBY)</small></a></li>";

                if (isset($_GET['display'])) {
                    $sidebar = str_replace("=" . $_GET['display'] . "'", "=" . $_GET['display'] . "' class='active'", $sidebar);
                }
                echo $sidebar;
                ?>
            </ul>
        </li>
        <li><a class="menuitem">CUSTOMERS</a>
            <ul class="submenu current">
                <li><a href="customers.php?">All Customers</a></li>
                <li><a href="customers.php?&display=ACTIVE">All Active Customers</a></li>  
                <li><a href="customerwcart.php">Customers with Cart Items</a></li>
                <li><a href="customers.php?&display=CREFCREDITS">Referral Credits <small>(<?php echo $CREFCREDITS; ?>)</small></a></li>
                <li><a href="customers.php??&display=CCREDITS">Outstanding Credits <small>(<?php echo $CCREDITS; ?>)</small></a></li>
                <li><a href="deposit.php?">Make Deposits</a></li>
                <li><a href="deposit.php?&posted=1">Payments Deposited</a></li>
            </ul>
        </li>
        <li><a class="menuitem">MISC</a>
            <ul class="submenu current">                
                <li><a href="newdesign.php?">Add to Library <small>(beta)</small></a></li>
                <li><a href='catalog.php?'>Catalog </a></li>
                <li><a href='designs.php?'>Design Library </a></li>
                <li><a href="orders_report.php?">Reports</a></li>
            </ul>
        </li>
    </ul>
</div>