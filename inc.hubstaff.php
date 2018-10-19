<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class HubstaffApi {

    private $app_token = '';
    private $auth_token = '';

    public function __construct($app_token, $auth_token) {
        $this->app_token = $app_token;
        $this->auth_token = $auth_token;
    }

    /**
     * Send a HTTP request to the API
     *
     * @param string $api_method The API method to be called
     * @param string $http_method The HTTP method to be used (GET, POST, PUT, DELETE, etc.)
     * @param array $data Any data to be sent to the API
     * @return string
     * */
    private function sendRequest($api_method, $http_method = 'GET', $data = null) {
// Standard data
        $data['app_token'] = $this->app_token;
        $request_url = "https://api.hubstaff.com/v1/";

// Debugging output
        $this->debug = array();
        $this->debug['Request URL'] = $request_url . $api_method;

// Create a cURL handle
        $ch = curl_init();

// Set the request
        curl_setopt($ch, CURLOPT_URL, $request_url . $api_method);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'App-Token: ' . $this->app_token,
            'Auth-Token: ' . $this->auth_token
        ));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $http_method);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);

// Send data
        if (!empty($data)) {

            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));

// Debugging output
            $this->debug['Posted Data'] = $data;
        }

// Execute cURL request
        $curl_response = curl_exec($ch);

// Save CURL debugging info
        $this->debug['Last Response'] = $curl_response;
        $this->debug['Curl Info'] = curl_getinfo($ch);

// Close cURL handle
        curl_close($ch);

// Parse response
        $response = $curl_response;

// Return parsed response
        return $response;
    }

    public function authenticate(array $parameters = array()) {
        return $this->sendRequest('auth', 'POST', $parameters);
    }

    public function users(array $parameters = array()) {
        return $this->sendRequest('users', 'GET', $parameters);
    }

    public function activities(array $parameters = array()) {
        return $this->sendRequest('activities', 'GET', $parameters);
    }

    public function screenshots(array $parameters = array()) {
        return $this->sendRequest('screenshots', 'GET', $parameters);
    }

}


function isJson($string) {
    json_decode($string);
    return (json_last_error() == JSON_ERROR_NONE);
}

$timezone = "+08:00"; 

if (isset($_POST["generate"])) {
    $work_date = mysql_real_escape_string($_POST["work_date"]);
    $users = mysql_real_escape_string($_POST["users"]);
    $Hubstaff = new HubstaffApi(
            'RDeeBqd7t5SC09Sd9y2AFxu9Obe-ktY3kntGgCAOGZ8', 'U3F2A7rRN2D1-3yLqbGT47c2F1dNag7C8HnNLD8vavI');

    $response = $Hubstaff->activities([
        "start_time" => $work_date . "T00:00:00$timezone",
        "stop_time" => $work_date . "T24:00:00$timezone",
        "users" => $users
    ]);
        
    $total_hours = 0;
    $activity = 0;
    if (isJson($response)) {
        $myArray = json_decode($response, true);
        if (!array_key_exists("error", $myArray)) {
            if (count($myArray["activities"]) > 0) {
                foreach ($myArray["activities"] as $row) {
                    $sql[] = "('$work_date','"
                            . $row['id'] . "', '"
                            . $row['time_slot'] . "', '"
                            . $row['starts_at'] . "', '"
                            . $row['project_id'] . "', '"
                            . $row['mouse'] . "', '"
                            . $row['keyboard'] . "', '"
                            . $row['overall'] . "', '"
                            . $row['tracked'] . "', '"
                            . $row["user_id"] . "')";

                    $total_hours += (int) $row["tracked"];
                    $activity += (int) $row["overall"];
                }

                $activity_percent = ($activity / $total_hours) * 100;

                $qry = "INSERT IGNORE INTO staff_activities_data(work_date, id, time_slot, starts_at, project_id, mouse, "
                        . "keyboard, overall, tracked, user_id) "
                        . "VALUES " . implode(',', $sql);
                mysql_query($qry)or die(mysql_error());

                $qry = "INSERT INTO staff_activities(hid, work_date, total_hours, activity) "
                        . "VALUES ('$users','$work_date','" . gmdate("H:i:s", $total_hours) . "','$activity_percent') "
                        . "ON DUPLICATE KEY UPDATE total_hours='" . gmdate("H:i:s", $total_hours)
                        . "', activity='$activity_percent'";
                mysql_query($qry)or die(mysql_error());
            } else {
                echo "0 results";
            }
        } else {
            echo $myArray["error"];
        }
    } else {
        echo $response;
    }
}
if (isset($_POST["screenshots"])) {
    $work_date = mysql_real_escape_string($_POST["work_date"]);
    $users = mysql_real_escape_string($_POST["users"]);
    $Hubstaff = new HubstaffApi(
            'RDeeBqd7t5SC09Sd9y2AFxu9Obe-ktY3kntGgCAOGZ8', 'U3F2A7rRN2D1-3yLqbGT47c2F1dNag7C8HnNLD8vavI');

    $response = $Hubstaff->screenshots([
        "start_time" => $work_date . "T00:00:00$timezone",
        "stop_time" => $work_date . "T11:59:59$timezone",
        "users" => $users
    ]);
    if (isJson($response)) {
        $myArray = json_decode($response, true);
        if (!array_key_exists("error", $myArray)) {
            if (count($myArray["screenshots"]) > 0) {
                foreach ($myArray["screenshots"] as $row) {
                    $sql[] = "('$work_date','"
                            . $row['id'] . "', '"
                            . $row['time_slot'] . "', '"
                            . $row['recorded_at'] . "', '"
                            . $row['url'] . "', '"
                            . $row["user_id"] . "')";
                }

                $qry = "INSERT IGNORE INTO staff_screenshots(work_date, id, time_slot, recorded_at, "
                        . "url, user_id) "
                        . "VALUES " . implode(',', $sql);
                mysql_query($qry)or die(mysql_error());
            } else {
                echo "0 results";
            }
        } else {
            echo $myArray["error"];
        }
    } else {
        echo $response;
    }

    $response = $Hubstaff->screenshots([
        "start_time" => $work_date . "T12:00:00$timezone",
        "stop_time" => $work_date . "T23:59:59$timezone",
        "users" => $users
    ]);

    if ($response != '') {
        $myArray = json_decode($response, true);
        if (!array_key_exists("error", $myArray)) {
            if (count($myArray["screenshots"]) > 0) {
                foreach ($myArray["screenshots"] as $row) {
                    $sql[] = "('$work_date','"
                            . $row['id'] . "', '"
                            . $row['time_slot'] . "', '"
                            . $row['recorded_at'] . "', '"
                            . $row['url'] . "', '"
                            . $row["user_id"] . "')";
                }

                $qry = "INSERT IGNORE INTO staff_screenshots(work_date, id, time_slot, recorded_at, "
                        . "url, user_id) "
                        . "VALUES " . implode(',', $sql);
                mysql_query($qry)or die(mysql_error());
            } else {
                echo "0 results";
            }
        } else {
            echo $myArray["error"];
        }
    } else {
        echo "no response.. please try again";
    }
}
if (isset($_POST["notes"])) {
    $qry = "UPDATE staff_activities SET notes = '" . mysql_real_escape_string($_POST["notes"])
            . "', tasks ='" . mysql_real_escape_string($_POST["tasks"])
            . "', pending ='" . mysql_real_escape_string($_POST["pending"])
            . "', extra_hours = '" . $_POST['extra_hours']
            . "' WHERE work_date='{$_POST["work_date"]}' AND hid = $hid";
    mysql_query($qry)or die(mysql_error());
}
if (isset($_POST["recurring"])) {    
    foreach ($_POST['recurring'] as $day_no => $recurring) {
        $recurring = mysql_real_escape_string($recurring);
        $qry = "INSERT INTO staff_recurring (hid, recurring, day_no) VALUES ($hid, '$recurring', $day_no) 
                ON DUPLICATE KEY UPDATE recurring = '$recurring'";
        mysql_query($qry)or die(mysql_error());
    }
}