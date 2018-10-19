<div class="block" id="section-menu">
                        <?php
                //CONFIG SIDEBAR
                $sql = "(SELECT COUNT(a.id) FROM proofs a INNER JOIN naz_custom_co b ON a.naz_custom_id = b.id WHERE (LEFT(b.model , 6) IN ('CUSTOM', 'BACKSID')) AND a.status = 1";
                        
                $qry = "SELECT "
                        . "$sql AND a.design_status=2) AS 'ADDTO',"
                        . "$sql AND a.design_status=3) AS 'ADDED',"
                        . "$sql AND a.design_status=1) AS 'DISREGARD',"
                        . "$sql AND a.design_status=0) AS 'REVIEW',"
                        . "$sql AND a.design_status > 3) AS 'PUBLISHED',"
                        . "$sql AND a.status='3' AND a.design_status='0') AS 'REJECTED'";
                $result = mysql_query($qry)or die(mysql_error());

                $average = 0;

                while ($row = mysql_fetch_assoc($result)) {
                    $ADDTO = $row['ADDTO'];
                    $ADDED = $row['ADDED'];
                    $DISREGARD = $row['DISREGARD'];
                    $REVIEW = $row['REVIEW'];
                }
                ?>
    <ul class="section menu">
        <li><a class="menuitem current">Navigation</a>
            <ul class="submenu current">                
                <li><a href="newdesign.php">For Review <small>(<?php echo $REVIEW; ?>)</small></a></li>
                <li><a href="newdesign.php?display=ADDTO">Add to Library <small>(<?php echo $ADDTO; ?>)</small></a></li>
                <li><a href="newdesign.php?display=ADDED">Added <small>(<?php echo $ADDED; ?>)</small></a></li>

                <li><a href="newdesign.php?display=DISREGARD">Disregarded Designs <small>(<?php echo $DISREGARD; ?>)</small></a></li>            
                <li><a href="catalog.php">Go to Products Catalog</a></li>
            </ul>
        </li>                        
    </ul>
</div>