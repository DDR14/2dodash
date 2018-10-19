<?php

require('inc.functions.php');
connectToSQL();

//find an account associated with this email address.
$qry = "SELECT * FROM `users` WHERE `email`='" . mysql_real_escape_string($_POST['email']) . "'";
$result = mysql_query($qry)or die(mysql_error());
$row_count = mysql_num_rows($result);
if (!$row_count) {
    die('There is no account associated with this email address.');
} elseif ($row_count > 1) {
    die("There are multiple accounts associated with this email address, please change one of the accounts to a unique email address.");
} else {
    //create hash
    while ($row = mysql_fetch_assoc($result)) {
        $userid = $row['id'];
        $email = $row['email'];
    }
    //hash the time
    $hash = md5(time()); //this will be the link in the email
    //hash the hash with the users id
    $db_hash = md5($userid . $hash);
    //add this to the database.
    $qry = "UPDATE `users` SET `p_reset`='" . $db_hash . "', `orig_hash`='" . $hash . "' WHERE `id`='" . $userid . "'";
    mysql_query($qry)or die(mysql_error());

    //send email
    $headers = "From: no-reply@2dodash.com\r\n";
    $headers .= "Reply-To: " . strip_tags('no-reply@2dodash.com') . "\r\n";
    $headers .= "MIME-Version: 1.0\r\n";
    $headers .= "Content-Type: text/html; charset=ISO-8859-1\r\n";
    $message = "A password reset request has been initiated for your account on dashboard.  Click this link to reset your password. <a href=\"http://www.2dodash.com/passwordreset.php?h=" . $hash . "\">http://www.2dodash.com/passwordreset.php?h=" . $hash . "</a>";
    mail($email, "Dashboard Password Reset", $message, $headers);
}

mysql_close();

//redirect to index.php
header('Location: index.php');
?>