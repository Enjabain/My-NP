<?php

include("../config_mynonprofit.php");
include("../functions.php");
authAdmin();
include("../connect.php");
include("template_header.php");

$event_details_id = $_REQUEST['event'];
echo '<div class="content">
<form id="editeventform" name="editeventform" method="post" action="editevent.php">
<h2>Admin Recurring Event</h2>
<table style="font-size:12px; color:#000;">
    <tr style="font-weight:bold;"><th>Name</th><th>Description</th><th>Type</th><th>Date/Time</th><th style="width:200px;">Attendees</th><th style="width:100px;">Sign Up</th>';
$signed_up = 'false';
$query1 = $db->prepare('
	SELECT *
	FROM events LEFT JOIN event_details ON events.event_details_id = event_details.event_details_id
	WHERE event_status = 0 AND events.event_details_id = :event_details_id AND event_recurrs = 1
      	ORDER BY event_date
        ');
$query1->execute(array('event_details_id' => $event_details_id));
foreach ($query1 as $row1) {
    $event_name = $row1['event_name'];
    $event_description = $row1['event_description'];
    $event_type = $row1['event_type'];
    $event_ispotluck = $row1['event_ispotluck'];
    $event_date = $row1['event_date'];
    $event_status = $row1['event_status'];
    $event_id = $row1['event_id'];
    $event_details_id = $row1['event_details_id'];
    $event_recurrs = $row1['event_recurrs'];
    $dateTime = new DateTime($event_date);
    $event_date = date_format($dateTime, "l n-j-Y g:i A");

    echo '<tr><td style="font-weight:bold;">' . $event_name . '</td>';
    echo '<td>' . $event_description . '</td><td>' . $event_type . '</td><td>' . $event_date . '</td><td>';
    attendees($event_ispotluck, $event_id);


    echo'</td><td>
<button type="submit" value="' . $event_id . '" name="event_id">Edit</button></td>';
    echo '</tr>';
}
echo '</table></div>';
?>
<?php include("template_footer.php"); ?>

