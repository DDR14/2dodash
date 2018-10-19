<?php

session_start();
include "include/db.php";
$db = new db('boostpr1_boostpromotions');
if (isset($_POST['submitted'])) {
    $howtos = implode(',', $_POST['howto']);

    $id = $db->create('gw_contests', $_POST['contest']);

    echo '<div class="message success" >SAVE COMPLETE</div>';
    ?>
    <script type="text/javascript">window.top.location.href = 'competitions.php?id=<?= $id?>';</script>
    <?php
}
?>
<link rel="stylesheet" type="text/css" href="css/reset.css" media="screen" />
<link rel="stylesheet" type="text/css" href="css/text.css" media="screen" />
<link rel="stylesheet" type="text/css" href="css/layout.css" media="screen" />
<link rel="stylesheet" type="text/css" href="css/vieworder.css" media="screen" />
<link rel="stylesheet" type="text/css" href="css/table/demo_page.css" media="screen" />
<form action='' method='POST'> 
    <p><b>Name: (hidden)</b><br /><input type='text' name='contest[name]'  /> </p>
    <p><b>Contests Title:</b><br /><input type='text' name='contest[contests_name]'  /> </p>
    <p><b>Contests Description:</b><br /><input type='text' name='contest[contests_description]' /> </p>
    <p><b>Contests Image:</b><br /><input type='text' name='contest[contests_image]'  /> </p>
    <p><b>Prize:</b><br /><input type='text' name='contest[prize]'  /> </p>
    <p><input class="btn btn-green" type='submit' value='Add new' name="submitted" /> </p>
</form> 