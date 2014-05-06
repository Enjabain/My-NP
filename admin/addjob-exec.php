<?php
include("../config_mynonprofit.php");
include("../functions.php");
authAdmin();
include("../connect.php");

	$job_creator = $_SESSION['SESS_MEMBER_ID'];
	
	//Array to store validation errors
	$errmsg_arr = array();
	
	//Validation error flag
	$errflag = false;
	

	
	//Sanitize the POST values
	$job_name = htmlentities($_POST['job_name']);
	$job_type = htmlentities($_POST['job_type']);
	$job_description = htmlentities($_POST['job_description']);
	$job_status = '0';
	//Input Validations
	if($job_name == '') {
		$errmsg_arr[] = 'Job name missing';
		$errflag = true;
	}
	if($job_type == '') {
		$errmsg_arr[] = 'Job type missing';
		$errflag = true;
	}
	if($job_description == '') {
		$errmsg_arr[] = 'Job description missing';
		$errflag = true;
	}

	
	//If there are input validations, redirect back to the registration form
	if($errflag) {
		$_SESSION['ERRMSG_ARR'] = $errmsg_arr;
		session_write_close();
		header("location: index.php");
		exit();
	}

	//Create INSERT query
        $query = $db->prepare('INSERT INTO volunteer_opportunities(job_name, job_type, job_description, job_status, job_creator) VALUES(:job_name, :job_type, :job_description, :job_status, :job_creator)');
	$result = $query->execute(array('job_name' => $job_name, 'job_type' => $job_type, 'job_description' => $job_description, 'job_status' => $job_status, 'job_creator' => $job_creator));
	
	//Check whether the query was successful or not
	if($result) {
		header("location: index.php");     
		exit();
	}else {
		die("Query failed");
	}
?>