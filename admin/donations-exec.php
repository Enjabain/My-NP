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
	$donations_total = htmlentities($_POST['donations_total']);
	$donations_goal = htmlentities($_POST['donations_goal']);
	$job_status = '0';
	//Input Validations
	if($donations_total == '') {
		$errmsg_arr[] = 'donation total missing';
		$errflag = true;
	}
	if($donations_goal == '') {
		$errmsg_arr[] = 'donation goal missing';
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
	$query = $db->prepare('
	UPDATE donations
    SET donations_goal= :donations_goal , donations_total= :donations_total
	WHERE donations_id = 1
	');
	$result = $query->execute(array('donations_goal' => $donations_goal, 'donations_total' => $donations_total));
	
	//Check whether the query was successful or not
	if($result) {
		header("location: index.php");     
		exit();
	}else {
		die("Query failed");
	}
?>