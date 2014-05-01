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
$member_id = htmlentities($_POST['member_id']);
//Input Validations
if ($member_id == '') {
    $errmsg_arr[] = 'Member id missing';
    $errflag = true;
}



//If there are input validations, redirect back to the registration form
if ($errflag) {
    $_SESSION['ERRMSG_ARR'] = $errmsg_arr;
    session_write_close();
    header("location: index.php");
    exit();
}

include("template_header.php");
echo'
<div class="content">
<form id="editmemberform" name="editmemberform" method="post" action="editmember-exec.php">
<h2>Admin - Member</h2>
	<table>
<tr><th>Username</th><th>Firstname</th><th>Lastname</th><th>Email</th><th>Member Type</th><th>Finalize</th><th>Status</th>
';


$query = $db->prepare('
      SELECT *
      FROM
	members
      WHERE member_id = :member_id');
$query->execute(array('member_id' => $member_id));
foreach ($query as $row) {
    $firstname = $row['firstname'];
    $lastname = $row['lastname'];
    $email = $row['email'];
    $member_type1 = $row['member_type'];
    $username = $row['username'];
    $member_id = $row['member_id'];

    echo '<tr><td><input type=textarea size="10" name="username" id="username" value="' . $username . '"/></td><td><input type=textarea size="10" name="firstname" id="firstname" value="' . $firstname . '"/></td>';
    echo '<td><input type=textarea size="10" name="lastname" id="lastname" value="' . $lastname . '"/></td>
<td><input type=textarea size="10" name="email" id="email" value="' . $email . '"/></td>
    

	  <td><select name="member_type" id="member_type">';
    $query3 = $db->prepare('
      SELECT name
      FROM member_types');
    $query3->execute();
    foreach ($query3 as $row3) {
        $member_type = $row3['name'];
        if ($member_type1 == $member_type) {
            echo '<option value="' . $member_type . '" selected="selected">' . $member_type . '</option>';
        } else {
            echo '<option value="' . $member_type . '">' . $member_type . '</option>';
        }
    }
    echo'</select></td>



<td>
<input type="radio" name="finalize" value="remove" /> Mark For Deletion </td>
<td><button type="submit" value="' . $member_id . '" name="member_id">Submit</button></td>';
    echo '</tr>';
    echo '</table></form>';
    echo'<form id="editmemberform" name="editmemberform" method="post" action="editmember-exec.php">
<h2>Volunteer Interests</h2>
	<table>
<tr><th>Their Selections</th><th></th>
<tr><td>
';
    $query2 = $db->prepare('
      SELECT volunteer_types.volunteer_type_id, member_id, name
      FROM volunteer_types LEFT OUTER JOIN
	volunteer_types_by_member ON volunteer_types.volunteer_type_id = volunteer_types_by_member.volunteer_type_id AND member_id = :member_id');
    $query2->execute(array('member_id' => $member_id));
    foreach($query2 as $row2) {
        $name = $row2['name'];
        $volunteer_type_id = $row2['volunteer_type_id'];
        $volunteermember_id = $row2['member_id'];


        echo ' <label>' . $name . ': <input type="checkbox" name="volunteertypes[]" value="' . $volunteer_type_id . '"';
        if ($volunteermember_id != "") {
            echo 'checked ';
        }
        echo '/></label><br />';
    }

    echo '</td><td><button type="submit" value="' . $member_id . '" name="volunteertypememberid">Submit</button></td>';
    echo '</tr>';
    echo '</table></form></div>';
}
?>
<?php include("template_footer.php"); ?>
