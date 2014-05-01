<?php

include("../config_mynonprofit.php");
include("../functions.php");
authAdmin();
include("../connect.php");
include("template_header.php");

echo'
<div class="content">
<form id="editeventform" name="editeventform" method="post" action="editevent.php">
	<h2>Past Events</h2>
	<table>
<tr><th>Name</th><th>Description</th><th>Type</th><th>Date</th><th width="200px">Attendees</th><th>Status</th>
';



$query = $db->prepare('
	SELECT *
	FROM events LEFT JOIN event_details ON events.event_details_id = event_details.event_details_id
	WHERE event_status = "2" OR event_status = "1"
        ORDER BY event_date DESC');
$query->execute();

foreach ($query as $row) {
    $event_name = $row['event_name'];
    $event_description = $row['event_description'];
    $event_type = $row['event_type'];
    $event_ispotluck = $row['event_ispotluck'];
    $event_date = $row['event_date'];
    $event_status = $row['event_status'];
    $event_id = $row['event_id'];





    $event_status_message = '';
    if ($event_status == '0') {
        $event_status_message = 'Signup open';
    } else if ($event_status == '1') {
        $event_status_message = 'Canceled';
    } else if ($event_status == '2') {
        $event_status_message = 'Completed';
    }

    $dateTime = new DateTime($event_date);
    $event_date = date_format($dateTime, "l n-d-Y g:i A");

    echo '<tr><td style="font-weight:bold;">' . $event_name . '</td>';
    echo '<td>' . $event_description . '</td><td>' . $event_type . '</td><td>' . $event_date . '</td><td>';
    attendees($event_ispotluck, $event_id);
    echo'</td><td>' . $event_status_message . '<button type="submit" value="' . $event_id . '" name="event_id">Edit</button></td>';
    echo '</tr>';
}
echo '</table></form></div>';
?>
<?php include("template_footer.php"); ?>