<?php
include("config_mynonprofit.php");
include("functions.php");
auth();
include("connect.php");


	$member_id = $_SESSION['SESS_MEMBER_ID'];


	
	//Array to store validation errors
	$errmsg_arr = array();
	
	//Validation error flag
	$errflag = false;
	
	

	
	//Sanitize the POST values
	$job_forfeit_id = htmlentities($_POST['job_forfeit_id']);
	$job_complete_id = htmlentities($_POST['job_complete_id']);
	$job_inprogress_id = htmlentities($_POST['job_inprogress_id']);
	$hours = htmlentities($_POST['hours']);
	$minutes = htmlentities($_POST['minutes']);
        $seconds = $hours * 3600 + $minutes * 60;

	//Input Validations
	if($job_forfeit_id == '' && $job_complete_id == '' && $job_inprogress_id == '') {
		$errmsg_arr[] = 'Job id missing';
		$errflag = true;
	}

	
	
	//If there are input validations, redirect back to the registration form
	if($errflag) {
		$_SESSION['ERRMSG_ARR'] = $errmsg_arr;
		session_write_close();
		//header("location: jobsignup-form.php");
		exit();
	}

if ($job_forfeit_id != '') {
$query1 = $db->prepare('UPDATE volunteer_opportunities
SET job_status = 0, job_startedtime = NULL, job_inprogressby = NULL, job_time = 0
WHERE job_id = :job_forfeit_id');
$result = $query1->execute(array('job_forfeit_id'=>$job_forfeit_id));

}
if ($job_complete_id != '') {
$query2 = $db->prepare('UPDATE volunteer_opportunities
SET job_status = 2, job_time = :seconds
WHERE job_id = :job_complete_id');
$result = $query2->execute(array('job_complete_id'=>$job_complete_id, 'seconds' => $seconds));


}
if ($job_inprogress_id != '') {
$query3 = $db->prepare('UPDATE volunteer_opportunities
SET job_status = 1, job_time = 0
WHERE job_id = :job_inprogress_id');
$result = $query3->execute(array('job_inprogress_id'=>$job_inprogress_id));

}
	
	//Check whether the query was successful or not
	if($result) {
		header("location: index.php");                    
		exit();
	}else {
		die("Query failed");
	}
?>