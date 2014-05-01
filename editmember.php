<?php

include("config_mynonprofit.php");
include("functions.php");
auth();
include("connect.php");

$member_id = $_SESSION['SESS_MEMBER_ID'];

include("template_header.php");


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
    $member_type = $row['member_type'];
    $username = $row['username'];
    $member_id = $row['member_id'];

    echo'<div class="content">
<form id="editmemberform" name="editmemberform" method="post" action="editmember-exec.php">
<h2>Edit Profile</h2>
	<table>
<tr><th>Username</th><th>Firstname</th><th>Lastname</th><th>Email</th><th>Status</th>
';

    echo '<tr><td><input type="text" name="username" id="username" value="' . $username . '"/></td><td><input type="text" name="firstname" id="firstname" value="' . $firstname . '"/></td>';
    echo '<td><input type="text" name="lastname" id="lastname" value="' . $lastname . '"/></td>
<td><input type="text" name="email" id="email" value="' . $email . '"/></td>
<td><button type="submit" value="' . $member_id . '" name="member_id">Submit</button></td>';
    echo '</tr>';
    echo '</table></form><br/>';




    echo'<form id="editmemberform" name="editmemberform" method="post" action="editmember-exec.php">
<h2>Change Password</h2>
	<table>
<tr><th>Current Password</th><th>New Password</th><th></th>';

    echo '<tr><td><input type="password" name="oldpassword" id="oldpassword" value=""/></td><td><input type="password" name="newpassword" id="newpassword" value=""/></td><td><button type="submit" value="' . $member_id . '" name="passwordmemberid">Submit</button></td>';
    echo '</tr>';
    echo '</table></form>';
    echo'<form id="editmemberform" name="editmemberform" method="post" action="editmember-exec.php">
<h2>Request Membership Access</h2>
	<table>
<tr><th>Membership Type</th><th></th></tr>
<tr><td><select name="member_type" id="member_type">';
    $query3 = $db->prepare('
      SELECT name
      FROM member_types');
    $query3->execute();
    foreach ($query3 as $row3) {
        $member_type = $row3['name'];
        echo '<option value="' . $member_type . '">' . $member_type . '</option>';
    }
    echo'</select>';
    echo '</td><td><button type="submit" value="' . $member_id . '" name="membertypememberid">Submit</button></td>';
    echo '</tr>';
    echo '</table></form>';


    echo'<form id="editmemberform" name="editmemberform" method="post" action="editmember-exec.php">
<h2>Volunteer Interests</h2>
	<table>
<tr><th>Your Selections</th><th></th>
<tr><td>
';
    $query2 = $db->prepare('
      SELECT volunteer_types.volunteer_type_id, member_id, name
      FROM volunteer_types LEFT OUTER JOIN
	volunteer_types_by_member ON volunteer_types.volunteer_type_id = volunteer_types_by_member.volunteer_type_id AND member_id = :member_id');
    $query2->execute(array('member_id' => $member_id));

    foreach ($query2 as $row2) {

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