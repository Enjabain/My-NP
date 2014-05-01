<?php

include("config_mynonprofit.php");
include("functions.php");
auth();
include("connect.php");
include("template_header.php");

echo'
<div style="max-width:600px;">
	<h2>Past Events</h2>
	<table>
<tr><th>Name</th><th>Description</th><th>Type</th><th>Date</th><th width="200px">Attendees</th><th>Status</th></tr>
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
    $event_auth_type = $row['event_auth_type'];
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
    $event_date_display = date_format($dateTime, "l n-d-Y g:i A");
    if (authEvent($event_auth_type, $event_type)) {
        echo '<tr><td style="font-weight:bold;">' . $event_name . '</td>';
        echo '<td>' . $event_description . '</td><td>' . $event_type . '</td><td>' . $event_date_display . '</td><td>';
        attendees($event_ispotluck, $event_id);
        echo'</td><td>' . $event_status_message . '</td>';
        echo '</tr>';
    }
}
echo '</table></div>';
?>
<?php include("template_footer.php"); ?>