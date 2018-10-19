<div class="block" id="section-menu">
    <ul class="section menu">
        <?php
        //these menu items will be orders arranged by date fully approved and paid.
        //New Menu and counter
        company_db_connect(1);

        $qry = "SELECT "               
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
	)) AS 'CREFCREDITS'";
                

        $result = mysql_query($qry)or die(mysql_error());

        $average = 0;

        while ($row = mysql_fetch_assoc($result)) {
            $CCREDITS = $row['CCREDITS'];
            $CREFCREDITS = $row['CREFCREDITS'];
        }
        mysql_close();
        ?>
        <li><a class="menuitem">CUSTOMERS</a>
            <ul class="submenu current">
                <li><a href="customers.php">All Customers</a></li>
                <li><a href="customers.php?display=ACTIVE">All Active Customers</a></li>                
                <li><a href="customers.php?display=CREFCREDITS">Referral Credits <small>(<?php echo $CREFCREDITS; ?>)</small></a></li>
                <li><a href="customers.php?display=CCREDITS">Outstanding Credits <small>(<?php echo $CCREDITS; ?>)</small></a></li>
                <li><a href="deposit.php">Make Deposits</a></li>
                <li><a href="deposit.php?posted=1">Payments Deposited</a></li>
            </ul>
        </li>
        <li><a class="menuitem">MISC</a>
            <ul class="submenu current">                
                <li><a href='catalog.php'>Catalog <small>(beta)</small></a></li>
                <li><a href="orders_report.php">Reports</a></li>
            </ul>
        </li>
    </ul>
</div>