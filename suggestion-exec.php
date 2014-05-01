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
    header("location: jobsignup-form.php");
    exit();
}


$query = $db->prepare('UPDATE volunteer_opportunities
SET job_status = 1, job_startedtime = NOW(), job_inprogressby = :member_id
WHERE job_id = :job_id');


$result = $query->execute(array('member_id' => $member_id, 'job_id' => $job_id));

//Check whether the query was successful or not
if ($result) {
    header("location: index.php");
    exit();
} else {
    die("Query failed");
}
?>