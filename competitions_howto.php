<?php
session_start();
include "include/db.php";
$db = new db('boostpr1_boostpromotions');

$contest_id = (int) $_GET['contest_id'];
?>
<link rel="stylesheet" type="text/css" href="css/reset.css" media="screen" />
<link rel="stylesheet" type="text/css" href="css/text.css" media="screen" />
<link rel="stylesheet" type="text/css" href="css/layout.css" media="screen" />
<link rel="stylesheet" type="text/css" href="css/vieworder.css" media="screen" />
<link rel="stylesheet" type="text/css" href="css/table/demo_page.css" media="screen" />
<?php
if (isset($_GET['id'])) {
    $id = (int) $_GET['id'];
} else {
    $id = 0;
}

$howto_types = $db->find('all', 'gw_contest_howto_types');
$opt_types = '';
foreach ($howto_types as $htype) {
    $opt_types .= '<option>' . $htype['type_name'] . '</option>';
}

if (isset($_POST['submitted'])) {
    $db->update('gw_contest_howtos', $_POST['contest_howtos'], 'id = :id', ['id' => $id]);

    echo '<div class="message success" >UPDATED</div>';
    echo '<script type="text/javascript">window.location.href ="competitions_howto.php?contest_id=' . $contest_id . '");</script>';
}
if (isset($_POST['deleted'])) {
    $db->delete('gw_contest_howtos', 'id = :id', ['id' => $id]);

    echo '<div class="message success" >DELETED</div>';
    echo '<script type="text/javascript">window.location.href ="competitions_howto.php?contest_id=' . $contest_id . '");</script>';
    die();
}
if (isset($_POST['added'])) {
    $_POST['contest_howtos']['created'] = date('Y-m-d H:i:s');
    $db->create('gw_contest_howtos', $_POST['contest_howtos']);

    echo '<div class="message success" >ADDED</div>';
    echo '<script type="text/javascript">window.location.href ="competitions_howto.php?contest_id=' . $contest_id . '");</script>';
}

$row = $db->find('first', 'gw_contest_howtos', 'id =:id', ['id' => $id]);
$new = false;
if ($id == 0) {
    $row = ['contest_id' => $contest_id,
        'enabled' => 1,
        'howto_type' => '',
        'worth' => 1,
        'subtype' => '',
        'param1' => '',
        'param2' => '',
        'created' => date('Y-m-d H:i:s')
    ];
    $new = true;
}
?>

<b>How to Earn points:</b><br />
<table class="table">
    <thead>
    <th></th>
    <th>Type</th>
    <th>Worth</th>
    <th>Parameters</th>
    <th></th>
</thead>
<?php
$contest_howtos = $db->find('all', 'gw_contest_howtos', 'contest_id=:contest_id', ['contest_id' => $contest_id]);
foreach ($contest_howtos as $contest_howto) {
    $checked = $contest_howto['enabled'] ? 'checked' : '';
    ?>
    <tr>
        <td><input disabled <?= $checked ?>  type="checkbox" /></td>
        <td><?= $contest_howto['subtype'] . ' ' . $contest_howto['howto_type']; ?></td>
        <td><?= ' +' . $contest_howto['worth'] ?></td>
        <td><?= $contest_howto['param1'] ?><br/><?= $contest_howto['param2'] ?></td>
        <td><a href="competitions_howto.php?id=<?= $contest_howto['id'] ?>&contest_id=<?= $contest_id; ?>"  ><button>Edit</button></a></td>
    </tr>

    <?php
}
?>  
</table>

<form action='' method='POST'>
    <input type="hidden" name='contest_howtos[contest_id]' value="<?= $row['contest_id']; ?>"  />
    <p><b>Howto Type:</b><br />
        <select name='contest_howtos[howto_type]'  >
            <?= str_replace('>' . $row['howto_type'], ' selected >' . $row['howto_type'], $opt_types) ?>
        </select> 
    <p><b>Worth:</b><br /><input type='text' name='contest_howtos[worth]' value='<?= stripslashes($row['worth']) ?>' /> 
    <p><b>Subtype:</b><br /><input type='text' name='contest_howtos[subtype]' value='<?= stripslashes($row['subtype']) ?>' /> 
    <p><b>Parameter 1:</b><br /><input type='text' name='contest_howtos[param1]' value='<?= stripslashes($row['param1']) ?>' /> 
    <p><b>Parameter 2:</b><br /><input type='text' name='contest_howtos[param2]' value='<?= stripslashes($row['param2']) ?>' /> 
    <p><b>Enabled:</b><br /><input type='text' name='contest_howtos[enabled]' value='<?= stripslashes($row['enabled']) ?>' /> 
    <p><b>Created:</b><br />
        <?= stripslashes($row['created']); ?>
    </p>

    <?php if (!$new): ?>
        <input type='submit' value='Edit Row' class="btn btn-blue" name="submitted" />
        <input type='submit' value='Delete' class="btn btn-red" onclick="return confirm('You sure?')" 
               formnovalidate="" name='deleted' /> 
           <?php else: ?>
        <input type='submit' value='Add Row' class="btn btn-green" name="added" />
    <?php endif; ?>

</form> 