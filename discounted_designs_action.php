<?php

session_start();
include "include/db.php";
$db = new db('boostpr1_boostpromotions');
//delete proofs on db
$id = $_GET['id'];

if (isset($_GET['del'])) {
    $db->delete('zen_discounted_designs', 'design_id = :id', ['id' => $id]);
} else {
    $db->create('zen_discounted_designs', [
        'design_id' => $id,
        'created' => date('Y-m-d H:i:s'),
        'modified' => date('Y-m-d H:i:s'),
    ]);
}
header('location: discounted_designs.php');

