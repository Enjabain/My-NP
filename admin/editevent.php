<?php

include("../config_mynonprofit.php");
include("../functions.php");
authAdmin();
include("../connect.php");
include("template_header.php");
?>
<script>
    $(function() {
        $.datepicker.setDefaults($.datepicker.regional['']);
        $('#event_date').datetimepicker({
            dateFormat: 'yy-mm-dd',
            timeFormat: 'hh:mm:ss'
        });
    });
</script>
<?php

//Array to store validation errors
$errmsg_arr = array();

//Validation error flag
$errflag = false;

//Sanitize the POST values
$event_id = htmlentities($_POST['event_id']);
if ($event_id == '') {
    $event_id = $_GET['event_id'];
}
//Input Validations
if ($event_id == '') {
    $errmsg_arr[] = 'Event id missing';
    $errflag = true;
}



//If there are input validations, redirect back to the registration form
if ($errflag) {
    $_SESSION['ERRMSG_ARR'] = $errmsg_arr;
    session_write_close();
    header("location: index.php");
    exit();
}
?>


<?php

echo'
<div class="content">
<form id="editeventform" name="editeventform" method="post" action="editevent-exec.php">
<h2>Edit Event</h2>
	<table>
<tr><th>Name</th><th>Description</th><th>Type</th><th>Is Potluck?</th><th>Date</th><th>Finalize</th><th>Status</th>
';

$query = $db->prepare('SELECT *
      FROM events LEFT JOIN event_details ON events.event_details_id = event_details.event_details_id
      WHERE event_id = :event_id');
$query->execute(array('event_id' => $event_id));

foreach ($query as $row) {
    $event_name = $row['event_name'];
    $event_description = $row['event_description'];
    $event_type = $row['event_type'];
    $event_ispotluck = $row['event_ispotluck'];
    $event_date = $row['event_date'];
    $event_status = $row['event_status'];
    $event_recurrs = $row['event_recurrs'];
    $event_reccurence = $row['event_details_id'];
    $event_id = $row['event_id'];

    echo '<tr><td><input type=textarea size="10" name="event_name" id="event_name" value="' . $event_name . '"/></td>';
    echo '<td><textarea cols=40 rows=6 name="event_description" id="event_description"/>' . $event_description . '</textarea></td><td>
	  <select name="event_type" id="event_type">';

    $query2 = $db->prepare('SELECT name
      FROM member_types');
    $query2->execute();

    foreach ($query2 as $row2) {

        $member_type = $row2['name'];
        if ($event_type == $member_type) {
            echo '<option value="' . $member_type . '" selected="selected">' . $member_type . '</option>';
        } else {
            echo '<option value="' . $member_type . '">' . $member_type . '</option>';
        }
    }
    echo'</select>
</td>
<td>
	  <select name="event_ispotluck" id="event_ispotluck" value="' . $event_ispotluck . '">
  		<option value="1"';
    if ($event_ispotluck == 1) {
        echo "selected='selected'";
    }
    echo '>Yes</option>
  		<option value="0" ';
    if ($event_ispotluck == 0) {
        echo "selected='selected' ";
    }
    echo '>No</option>
	  </select>
</td>
<td><input type=textarea size="10" name="event_date" id="event_date" value="' . $event_date . '"/></td>
<td><select name="finalize"><option value="update">Update</option>
<option value="complete">Complete</option>
<option value="reactivate">Incomplete</option>
<option value="delete">Delete</option>
<option value="cancel">Cancel (will send email to attendees)</option>';
    if ($event_recurrs == '1') {
        echo'<option value="deleterecurring">Delete <b>All Recurrences</b></option>';
    }
    echo '</select><input type="hidden" name="event_details_id" value="' . $event_reccurence . '" />';

    echo'</td><td><button type="submit" value="' . $event_id . '" name="event_id">Submit</button></td>';
    echo '</tr>';
}
echo '</table></form></div>';
?>
<?php include("template_footer.php"); ?>