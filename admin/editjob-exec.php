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
$job_id = htmlentities($_POST['job_id']);
$job_name = htmlentities($_POST['job_name']);
$job_type = htmlentities($_POST['job_type']);
$job_description = htmlentities($_POST['job_description']);
$job_inprogressby = htmlentities($_POST['job_inprogressby']);
$finalize = htmlentities($_POST['finalize']);
$job_status = htmlentities($_POST['job_status']);
$hours = htmlentities($_POST['job_hours']);
$minutes = htmlentities($_POST['job_minutes']);
$seconds = $hours * 3600 + $minutes * 60;


//Input Validations
if ($job_id == '') {
    $errmsg_arr[] = 'Job ID missing';
    $errflag = true;
}
if ($job_name == '') {
    $errmsg_arr[] = 'Job name missing';
    $errflag = true;
}
if ($job_type == '') {
    $errmsg_arr[] = 'Job type missing';
    $errflag = true;
}
if ($job_description == '') {
    $errmsg_arr[] = 'Job description missing';
    $errflag = true;
}
if ($job_inprogressby == '') {
    $errmsg_arr[] = 'Job in progress by missing';
    $errflag = true;
}


if ($errflag) {
    $_SESSION['ERRMSG_ARR'] = $errmsg_arr;
    session_write_close();
    header("location: editjob.php?job_id=".$job_id."");
    exit();
}

//Create UPDATE query
if ($finalize != 'update') {
    if ($finalize == 'complete') {
        $job_status = 3;
    } elseif ($finalize == 'completionpending') {
        $job_status = 2;
    } elseif ($finalize == 'inprogress') {
        $job_status = 1;
        $seconds = 0;
    }
}
if ($job_inprogressby == 'none') {
    $job_status = 0;
}

if ($finalize == 'forfeit') {
    $query2 = $db->prepare('UPDATE volunteer_opportunities
SET job_name = :job_name, job_type=:job_type, job_description = :job_description, job_time = 0 , job_status = "0", job_inprogressby = NULL
WHERE job_id=:job_id');
    $result2 = $query2->execute(array('job_name' => $job_name, 'job_type' => $job_type, 'job_description' => $job_description, 'job_id' => $job_id));
} elseif ($finalize == 'delete') {
    $query3 = $db->prepare('DELETE FROM volunteer_opportunities
WHERE job_id=:job_id LIMIT 1');
    $result3 = $query3->execute(array('job_id' => $job_id));
} else {
    $query = $db->prepare('UPDATE volunteer_opportunities
SET job_name = :job_name, job_type=:job_type, job_description = :job_description, job_inprogressby =:job_inprogressby, job_time = :seconds, job_status = :job_status
WHERE job_id=:job_id');
    $result = $query->execute(array('job_name' => $job_name, 'job_type' => $job_type, 'job_description' => $job_description, 'job_inprogressby' => $job_inprogressby, 'seconds' => $seconds, 'job_status' => $job_status, 'job_id' => $job_id));
}



//Check whether the query was successful or not
if ($result || $result2 || $result3) {
    header("location: index.php");
    exit();
} else {
    die("Query failed");
}
?>