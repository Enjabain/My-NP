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
        $('#job_bestfinishedby').datepicker({
            dateFormat: 'yy-mm-dd'
        });
    });
</script>

<?php

$member_id = $_SESSION['SESS_MEMBER_ID'];

//Array to store validation errors
$errmsg_arr = array();

//Validation error flag
$errflag = false;

//Sanitize the POST values
$job_id = htmlentities($_POST['job_id']);
//Input Validations
if ($job_id == '') {
    $errmsg_arr[] = 'Job id missing';
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
<form id="editjobform" name="editjobform" method="post" action="editjob-exec.php">
<h2>Admin - Volunteer Opportunities</h2>
	<table>
<tr><th>Name</th><th>Description</th><th>Type</th><th>Best Finished By Date</th><th>Time Spent</th><th>In Progress By</th><th>Change Status</th><th>Status</th>
';


$query = $db->prepare('
      SELECT *
      FROM
	volunteer_opportunities
      LEFT JOIN members
      ON volunteer_opportunities.job_inprogressby=members.member_id
      WHERE job_id = :job_id
      ORDER BY job_type');
$query->execute(array('job_id' => $job_id));
foreach ($query as $row) {
    $job_name = $row['job_name'];
    $job_description = $row['job_description'];
    $job_type = $row['job_type'];
    $job_bestfinishedby = $row['job_bestfinishedby'];
    $job_time = $row['job_time'];
    $job_status = $row['job_status'];
    $job_inprogressby = $row['job_inprogressby'];
    $job_id = $row['job_id'];
    $member_username = $row['username'];
    $member_id = $row['member_id'];
    $job_status_message = '';
    if ($job_status == '0') {
        $job_status_message = 'No one has signed up';
    } else if ($job_status == '1') {
        $job_status_message = 'In progress by <a href="../member.php?member_id=' . $member_id . '">' . $member_username . '</a><br />';
    } else if ($job_status == '2') {
        $job_status_message = 'Completion pending by <a href="../member.php?member_id=' . $member_id . '">' . $member_username . '</a><br />';
    }
    $hours = floor($job_time / 3600);
    $minutes = floor(($job_time - ($hours * 3600)) / 60);

    echo '<tr><td><input type=textarea size="10" name="job_name" id="job_name" value="' . $job_name . '"/></td>';
    echo '<td><textarea cols=40 rows=6 name="job_description" id="job_description"/>' . $job_description . '</textarea></td><td><input type=textarea size="10" name="job_type" id="job_type" value="' . $job_type . '"/></td><td><input type=textarea size="10" name="job_bestfinishedby" id="job_bestfinishedby" value="' . $job_bestfinishedby . '"/></td><td>';
    if ($job_status == '2') {
        echo '<span style="color:red; font-weight:bold;">Time requires approval</span><br />';
    }
    echo'Hours: <input type="text" style="width:20px;" name="job_hours" value="' . $hours . '" /><br />Minutes: <input type="text" style="width:20px;" name="job_minutes" value="' . $minutes . '" /></td>
<td><select name="job_inprogressby">';
    echo '<option value="none"';
    if ($job_inprogressby == '') {
        echo 'selected="selected"';
    }
    echo '>none</option>';
    $query = $db->prepare('
      SELECT username, member_id
      FROM members');
    $query->execute();
    foreach ($query as $row) {
        echo '<option value="' . $row['member_id'] . '"';
        if ($row[member_id] == $job_inprogressby) {
            echo'selected="selected"';
        }
        echo '>' . $row['username'] . '</option>';
    }
    echo '
</select></td>
<td>
<select name="statusupdate"><option value="donothing">Do Nothing</option>
<option value="complete">Complete</option>
<option value="completionpending">Completion Pending</option>
<option value="inprogress">In Progress</option>
<option value="forfeit">Forfeit</option>
<option value="delete">Delete</option>
</select>
</td>
<td><input type="hidden" name="job_status" value="' . $job_status . '" />' . $job_status_message . '<button type="submit" value="' . $job_id . '" name="job_id">Submit</button></td>';
    echo '</tr>';
}
echo '</table></form></div><br/>';
?>