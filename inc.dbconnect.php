<?php
//50.87.226.168
function connectToSQL() {
    mysql_connect("50.87.226.168", "boostpr1_tododas", "Draper24@") or die(mysql_error());
    mysql_select_db("boostpr1_tododash") or die(mysql_error());
}

function company_db_connect($input) {
    //$input is the id of the company
    if ($input == 1) {
        mysql_connect("50.87.226.168", "boostpr1_boostpr", "Draper24@")or die(mysql_error());
        mysql_select_db("boostpr1_boostpromotions")or die(mysql_error());
    }
    if ($input == 7) {
        mysql_connect("50.87.226.168", "boostpr1_boostpr", "Draper24@")or die(mysql_error());
        mysql_select_db("boostpr1_unplugged")or die(mysql_error());
    }  
    
    if ($input == 6) {
        mysql_connect("uchallenge.db.2010831.hostedresource.com", "uchallenge", "Draper24@")or die(mysql_error());
        mysql_select_db("uchallenge")or die(mysql_error());
    }
}