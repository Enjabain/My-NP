<?php

include("../config_mynonprofit.php");
include("../functions.php");
authAdmin();
include("../connect.php");

$event_creator = $_SESSION['SESS_MEMBER_ID'];

//Array to store validation errors
$errmsg_arr = array();

//Validation error flag
$errflag = false;

//Sanitize the POST values
$event_name = htmlentities($_POST['event_name']);
$event_type = htmlentities($_POST['event_type']);
$event_description = htmlentities($_POST['event_description']);
$event_date = htmlentities($_POST['event_date']);
$event_ispotluck = htmlentities($_POST['event_ispotluck']);
$event_weekday = htmlentities($_POST['weekday']);
$event_recurrs = htmlentities($_POST['event_recurrs']);
$event_recurrences = htmlentities($_POST['event_recurrences']);


//Input Validations
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


//If there are input validations, redirect back to the registration form
if ($errflag) {
    $_SESSION['ERRMSG_ARR'] = $errmsg_arr;
    session_write_close();
    header("location: index.php");
    exit();
}



//Create INSERT query
$query = $db->prepare('INSERT INTO events(event_date, event_creator, event_status, event_details_id, event_recurrs) VALUES(:event_date, :event_creator, "0", :event_details_id, :event_recurrs)');
$result = $query->execute(array('event_date' => $event_date, 'event_creator' => $event_creator, 'event_details_id' => 0, 'event_recurrs' => $event_recurrs));
$event_details_id = $event_id = $db->lastInsertId('event_id');
$query2 = $db->prepare('UPDATE events
    SET event_details_id = :event_details_id
    WHERE event_id = :event_id');
$query2->execute(array('event_details_id' => $event_details_id , 'event_id' => $event_id));
$query3 = $db->prepare('INSERT INTO event_details(event_details_id, event_name, event_type, event_description, event_ispotluck) VALUES(:event_details_id, :event_name,:event_type,:event_description,:event_ispotluck)');
$query3->execute(array('event_details_id' => $event_details_id, 'event_name' => $event_name, 'event_type' => $event_type, 'event_description' => $event_description, 'event_ispotluck' => $event_ispotluck));
$Month = date("m", strtotime($event_date));
$Year = date("Y", strtotime($event_date));
$time = date("H:i:s", strtotime($event_date));

//echo $event_date;
$recurrence_date = strtotime($event_date);
$first_date = strtotime($event_date);
if ($event_recurrs == '1') {
    $i = 1;
    while ($i <= $event_recurrences && $i <= 20) {
        if ($_POST['weeklymonthly'] == "monthly") {
            foreach ($_POST['period'] as $period) {
                if ($i <= $event_recurrences) {
                    $recurrence_date = GetDayByPosition($period, $event_weekday, $Month, $Year);
                    $recurrence_date = $recurrence_date . ' ' . $time;
                    //echo $recurrence_date;
                    if ($recurrence_date > $event_date) {
                        //echo 'DATE ADDED';
                        $i++;
                        $query->execute(array('event_date' => $recurrence_date, 'event_creator' => $event_creator, 'event_details_id' => $event_details_id, 'event_recurrs' => $event_recurrs));
                    }
                }
            }
            $Month += 1;
        }

        if ($_POST['weeklymonthly'] == "weekly") {

            $recurrence_date = strtotime("+7 day", $recurrence_date);
//            echo date("Y-m-d H:i:s", $recurrence_date);
            if ($recurrence_date > $first_date) {
//                echo 'DATE ADDED';
                $i++;
                $insert_date = date("Y-m-d H:i:s", $recurrence_date);
                $query->execute(array('event_date' => $insert_date, 'event_creator' => $event_creator, 'event_details_id' => $event_details_id, 'event_recurrs' => $event_recurrs));
            }
        }
    }
}

//Check whether the query was successful or not
if ($result) {
    header("location: index.php");
    exit();
} else {
    die("Query failed");
}

function GetDayByPosition($Position, $Weekday, $Month, $Year) {
    // Sanatize some of the inputs
    $Position = strtolower($Position);
    $Weekday = strtolower($Weekday);

    // Go to next month so we can then go back 1 week at end of script for calculating last xxx of month
    if ($Position == 'last') {
        $Month += 1;
    }
    // We cannot have 13 months so set as January of the next year.
    if ($Month > 12) {
        $Month = 1;
        $Year += 1;
    }
    // Create new date object for the first of the month
    $D = new DateTime($Year . '-' . $Month . '-1');
    $DOW = $D->format('w'); // Get the current day of the week based on the 1st of the month
    $keys = array('Sunday' => 0, 'Monday' => 1, 'Tuesday' => 2, 'Wednesday' => 3,
        'Thursday' => 4, 'Friday' => 5, 'Saturday' => 6);
    // Calculate what the offset is based on the current day of the week
    // vs the day in the week you want to get
    $offset = $keys[$Weekday] - $DOW;
    if ($offset < 0) {
        $offset += 7;
    }

    switch ($Position) {
        // Don't need to add anything to first
        case 'second':
            $offset += 7; // Add 1 week
            break;
        case 'third':
            $offset += 14; // Add 2 weeks
            break;
        case 'fourth':
            $offset += 21; // Add 3 weeks
            break;
        case 'last':
            $offset -= 7; // Go back 7 days
            break;
    }
    // Add the current offset of days to the date object
    $D->modify('+' . $offset . ' days');
    return $D->format('Y-m-d');
}

?>