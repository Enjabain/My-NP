<?php

include("config_mynonprofit.php");
include("functions.php");
auth();
if (!isset($_SESSION['SESS_MEMBER_ID']) || (trim($_SESSION['SESS_MEMBER_ID']) == '')) {
    $loggedin = "false";
} else {
    $loggedin = "true";
}
include("connect.php");
include("template_header.php");

$event_details_id = $_REQUEST['event'];
echo '<div style="width:600px;">';
echo'<h2>Recurring Event</h2><table style="font-size:12px; color:#000;">
    <tr style="font-weight:bold;"><th>Name</th><th>Description</th><th>Type</th><th>Date/Time</th><th style="width:200px;">Attendees</th><th style="width:100px;">Sign Up</th>';
$signed_up = 'false';
$query = $db->prepare('
	SELECT *
	FROM events LEFT JOIN event_details ON events.event_details_id = event_details.event_details_id
	WHERE event_status = 0 AND events.event_details_id = :event_details_id AND event_recurrs = 1
      	ORDER BY event_date
        ');
$query->execute(array('event_details_id' => $event_details_id));
if ($query->rowCount() != 0) {
    foreach ($query as $row) {
        $event_name = $row['event_name'];
        $event_description = $row['event_description'];
        $event_type = $row['event_type'];
        $event_ispotluck = $row['event_ispotluck'];
        $event_auth_type = $row['event_auth_type'];
        $event_date = $row['event_date'];
        $event_status = $row['event_status'];
        $event_details_id = $row['event_details_id'];
        $event_id = $row['event_id'];
        $count = $row['COUNT(*)'];

        if ($loggedin == 'true') {
            $query2 = $db->prepare('
	SELECT *
	FROM events_by_member
	WHERE event_id = :event_id
	AND member_id = :member_id');
            $query2->execute(array('event_id' => $event_id, 'member_id' => $_SESSION['SESS_MEMBER_ID']));
            if ($query2->rowCount() != 0) {
                $signed_up = 'true';
            } else {
                $signed_up = 'false';
            }
        }



        $dateTime = new DateTime($event_date);
        $event_date_display = date_format($dateTime, "l n-j-Y g:i A");

        if ($signed_up == 'false' && authEvent($event_auth_type, $event_type)) {
            echo '<tr><td style="font-weight:bold;">' . $event_name . '</td>';
            echo '<td>' . $event_description . '</td><td>' . $event_type . '</td><td>' . $event_date_display . '</td><td>';
            attendees($event_ispotluck, $event_id);
            echo '</td><td>';
            if ($loggedin == "true") {
                echo'
<form method="post" action="eventsignup-exec.php">                
<table class="signup">
    <tr>
        <td># Adults
            <select name="number_adults">
                <option value="0">0</option>
                <option value="1" selected>1</option>
                <option value="2">2</option>
                <option value="3">3</option>
                <option value="4">4</option>
                <option value="5">5</option>
                <option value="6">6</option>
                <option value="7">7</option>
                <option value="8">8</option>
                <option value="9">9</option>
                <option value="10">10</option>
            </select>
        </td>
        <td># Kids<br/><select name="number_children">
                <option value="0">0</option>
                <option value="1">1</option>
                <option value="2">2</option>
                <option value="3">3</option>
                <option value="4">4</option>
                <option value="5">5</option>
                <option value="6">6</option>
                <option value="7">7</option>
                <option value="8">8</option>
                <option value="9">9</option>
                <option value="10">10</option>
            </select>
        </td>
    </tr>';
                if ($event_ispotluck == 1) {
                    echo'<tr><td colspan="2">Potluck Item(s)<input type="text" value="none" name="potluck_item" size="5"></input></td></tr>';
                }
                echo'<tr><td colspan="2"><button type="submit" value="' . $event_id . '" name="event_id">Sign Up</button></td></tr>
</table></form>';
            } else {
                echo '<a href="login.php">Login to sign up</a>';
            }
            echo '</td>';
            echo'</tr>';
        }
    }
} else {
    echo '<tr><td colspan="6">There are no events listed currently.</tr>';
}
echo '</table></div>';
?>
<?php include("template_footer.php"); ?>

