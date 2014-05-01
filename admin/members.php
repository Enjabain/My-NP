<?php
include("../config_mynonprofit.php");
include("../functions.php");
authAdmin();
include("../connect.php");
include("template_header.php");

echo'
<div class="content">
<form id="editmembersform" name="editmembersform" method="post" action="editmember.php">
	<h2>Members</h2>
	<table style="font-size:12px; color:#000;">
<tr style="font-weight:bold;" ><th>Username</th><th>Full Name</th><th>Email</th><th>Date Joined</th><th>User Type</th><th>Edit</th>
';


    $query = $db->prepare('
      SELECT *
      FROM
	members
      ORDER BY date_registered DESC');
$query->execute();
foreach($query as $row)  {
	$username = $row['username'];
	$firstname = $row['firstname'];
	$lastname = $row['lastname'];
	$email = $row['email'];
        $date_joined = $row['date_registered'];
	$member_type = $row['member_type'];
	$member_id = $row['member_id'];



echo '<tr><td><a href="member.php?member_id='.$member_id.'">'.$username.'</a></td>';
echo '<td>'.$firstname.' '.$lastname.'</td><td><a href="mailto:'.$email.'">'.$email.'</a></td><td>'.$date_joined.'</td><td>'.$member_type.'</td>
<td><button type="submit" value="'.$member_id.'" name="member_id">Edit</button></td>';
echo '</tr>';

}
echo '</table></form></div><br/>';
?>






</body>
</html>