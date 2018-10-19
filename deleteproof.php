<?php
session_start();
if (file_exists($_GET['location'])) {
    echo $_GET['location'] . " the file exists. deleting file... ";
    unlink($_GET['location']);
    $hasPSD = substr($_GET['location'], 0, -4);
    if (file_exists($hasPSD)) {
        echo " the PSD file exists. deleting file... ";
        unlink($hasPSD);
    }
} else {
    echo "File not found";
}

include "include/db.php";
$db = new db('boostpr1_boostpromotions');
//delete proofs on db
echo $db->delete('proofs', 'id = :id', ['id' => $_GET['proof_id']]);
echo $db->update('zen_orders', ['artwork_approved' => 0, 'orders_status' => 2], 'orders_id = :oid', ['oid' => $_GET['oid']]);
?>
<script type="text/javascript">window.location.href = 'vieworder.php?orderid=<?php echo $_GET['oid']; ?>';</script>
