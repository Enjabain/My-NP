<?php
include("config_mynonprofit.php");
include("functions.php");
auth();
include("connect.php");

	$member_id = $_SESSION['SESS_MEMBER_ID'];

	
	//Sanitize the POST values
	$event_id = htmlentities($_POST['event_id']);


$query = $db->prepare('DELETE FROM events_by_member
        WHERE event_id=:event_id
	AND member_id=:member_id LIMIT 1');

$result = $query->execute(array('event_id' => $event_id, 'member_id' => $member_id));
	
	//Check whether the query was successful or not
	if($result) {
		header("location: index.php");                                         
		exit();
	}else {
		die("Query failed");
	}
?>