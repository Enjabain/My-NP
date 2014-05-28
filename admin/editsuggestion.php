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
$suggestion_id = htmlentities($_POST['suggestion_id']);
if ($suggestion_id == '') {
    $suggestion_id = $_GET['suggestion_id'];
}
//Input Validations
if ($suggestion_id == '') {
    $errmsg_arr[] = 'Suggestion id missing';
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
<form id="editsuggestionform" name="editsuggestionform" method="post" action="editsuggestion-exec.php">
<h2>Edit - Suggestion</h2>
	<table>
<tr><th>Suggestion</th><th>Category</th><th>Suggested By</th><th>Finalize</th><th>Status</th>
';
$query5 = $db->prepare('
        SELECT *
        FROM mynp_suggestions LEFT JOIN members ON suggested_by = member_id
        WHERE suggestion_id = :suggestion_id');
$query5->execute(array('suggestion_id' => $suggestion_id));
foreach ($query5 as $row) {
    $suggestion = $row['suggestion'];
    $member_id = $row['member_id'];
    $username = $row['username'];
    $suggested_by = $row['suggested_by'];
    $category1 = $row['category'];
    $votes = $row['votes'];
    $suggestion_id = $row['suggestion_id'];
    $suggestion_status = $row['suggestion_status'];
    if ($suggestion_status == 1) {
        $status = 'Public Approved';
    } elseif ($suggestion_status == 2) {
        $status = 'Public Pending';
    } elseif ($suggestion_status == 3) {
        $status = 'Private';
    }
    $suggested_by_display = '<a href="member.php?member_id=' . $member_id . '">' . $username . '</a>';
    if ($member_id == '') {
        $suggested_by_display = 'Anonymous';
    }

    echo '<tr><td><textarea cols=40 rows=6 name="suggestion" id="suggestion"/>' . $suggestion . '</textarea></td>';

    echo'
	  <td><select name="category" id="category">';
    $query3 = $db->prepare('
      SELECT name
      FROM mynp_suggestion_categories');
    $query3->execute();
    foreach ($query3 as $row3) {
        $category = $row3['name'];
        if ($category1 == $category) {
            echo '<option value="' . $category . '" selected="selected">' . $category . '</option>';
        } else {
            echo '<option value="' . $category . '">' . $category . '</option>';
        }
    }
    echo'</select></td><td>' . $suggested_by_display . '</td>
<td>
<select name="finalize"><option value="update">Update</option>
<option value="approve">Approve</option>
<option value="pending">Pending</option>
<option value="public">Public</option>
<option value="private">Private</option>
<option value="delete">Delete</option>
</select>
</td>
<td><input type="hidden" name="suggested_by" value="' . $suggested_by . '" /><input type="hidden" name="suggestion_status" value="' . $suggestion_status . '" />' . $status . '<button type="submit" value="' . $suggestion_id . '" name="suggestion_id">Submit</button></td>';
    echo '</tr>';
}
echo '</table></form></div><br/>';
?>