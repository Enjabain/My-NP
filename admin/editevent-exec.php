<?php

include("../config_mynonprofit.php");
include("../functions.php");
authAdmin();
include("../connect.php");

//Array to store validation errors
$errmsg_arr = array();

//Validation error flag
$errflag = false;


//Sanitize the POST values
$event_id = htmlentities($_POST['event_id']);
$event_name = htmlentities($_POST['event_name']);
$event_type = htmlentities($_POST['event_type']);
$event_description = htmlentities($_POST['event_description']);
$event_date = htmlentities($_POST['event_date']);
$event_ispotluck = htmlentities($_POST['event_ispotluck']);
$event_details_id = htmlentities($_POST['event_details_id']);
$event_status = htmlentities($_POST['event_status']);
$finalize = htmlentities($_POST['finalize']);

$query5 = $db->prepare('DELETE 
  FROM event_details
  WHERE event_details.event_details_id = :event_details_id AND NOT EXISTS  
      (SELECT * FROM events 
       WHERE events.event_details_id = event_details.event_details_id)');

//Input Validations
if ($event_id == '') {
    $errmsg_arr[] = 'Event ID missing';
    $errflag = true;
}
if ($event_name == '') {
    $errmsg_arr[] = 'Event name missing';
    $errflag = true;
}
if ($event_type == '') {
    $errmsg_arr[] = 'Event type missing';
    $errflag = true;
}
if ($event_description == '') {
    $errmsg_arr[] = 'Event description missing';
    $errflag = true;
}
if ($event_date == '') {
    $errmsg_arr[] = 'Event date missing';
    $errflag = true;
}



if ($errflag) {
    $_SESSION['ERRMSG_ARR'] = $errmsg_arr;
    session_write_close();
    header("location: editevent.php?event_id=".$event_id."");
    exit();
}

//Create UPDATE query

if ($finalize == 'complete') {
    $event_status = 2;
} elseif ($finalize == 'reactivate') {
    $event_status = 0;
} elseif ($finalize == 'cancel') {
    $event_status = 1;
    $query3 = $db->prepare('SELECT email FROM events_by_member LEFT JOIN members ON events_by_member.member_id = members.member_id WHERE event_id =:event_id');
    $query3->execute();
    if ($query3->rowCount() != 0) {
        foreach ($query3 as $row3) {
            $tos[] = $row3['email'];
        }
        $to = implode(", ", $tos);

        $dateTime = new DateTime($event_date);
        $event_date_display = date_format($dateTime, "g:i A l F jS, Y");
        $subject = '' . $site_name . ' Event Canceled';
        $content = '<p>The ' . $event_name . ' event to happen at ' . $event_date_display . ' has been canceled.</p>';
        $content .= '<p>Please login <a href="' . $site_url . '">here</a> for updates.</p>';


        $headers = "MIME-Version: 1.0\r\n";
        $headers .= "Content-type: text/html; charset=iso-8859-1\r\n";
        $headers .= "From: mygp@growingplaces.cc\r\n";
        $mailed = mail($to, $subject, $content, $headers);
    }
}
if ($finalize == 'delete') {
    $query2 = $db->prepare('DELETE FROM events
WHERE event_id=:event_id');

    $result2 = $query2->execute(array('event_id' => $event_id));
    $result5 = $query5->execute(array('event_details_id' => $event_details_id));
} elseif ($finalize == 'deleterecurring') {
    $query4 = $db->prepare('DELETE FROM events
WHERE event_details_id=:event_details_id AND event_recurrs=1 AND event_status != 2');
    $result4 = $query4->execute(array('event_details_id' => $event_details_id));
    $result5 = $query5->execute(array('event_details_id' => $event_details_id));
} else {
    $query = $db->prepare('UPDATE events, event_details
SET event_name = :event_name, event_type=:event_type, event_ispotluck=:event_ispotluck, event_description = :event_description, event_date = :event_date, event_status = :event_status
WHERE event_id=:event_id AND event_details.event_details_id = :event_details_id');
    $result = $query->execute(array('event_name' => $event_name, 'event_type' => $event_type, 'event_ispotluck' => $event_ispotluck, 'event_description' => $event_description, 'event_date' => $event_date, 'event_status' => $event_status, 'event_id' => $event_id, 'event_details_id' => $event_details_id));
}


//Check whether the query was successful or not
if ($result || $result2 || $result4) {
    header("location: index.php");
    exit();
} else {
    die("Query failed");
}
?>