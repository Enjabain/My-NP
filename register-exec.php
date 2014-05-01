<?php

include("config_mynonprofit.php");
include("connect.php");
//Start session
session_start();

//Array to store validation errors
$errmsg_arr = array();

//Validation error flag
$errflag = false;


//Sanitize the POST values
$fname = htmlentities($_POST['fname']);
$lname = htmlentities($_POST['lname']);
$email = htmlentities($_POST['email']);
$username = htmlentities($_POST['username']);
$member_type = htmlentities($_POST['member_type']);
$password = htmlentities($_POST['password']);
$cpassword = htmlentities($_POST['cpassword']);
$today = date('Y-m-d H:i:s');
$hash = md5($today);

//Input Validations
if ($fname == '') {
    $errmsg_arr[] = 'First name missing';
    $errflag = true;
}
if ($lname == '') {
    $errmsg_arr[] = 'Last name missing';
    $errflag = true;
}
if ($email == '') {
    $errmsg_arr[] = 'Email missing';
    $errflag = true;
}
if ($username == '') {
    $errmsg_arr[] = 'Username missing';
    $errflag = true;
}
if ($password == '') {
    $errmsg_arr[] = 'Password missing';
    $errflag = true;
}
if ($cpassword == '') {
    $errmsg_arr[] = 'Confirm password missing';
    $errflag = true;
}
if (strcmp($password, $cpassword) != 0) {
    $errmsg_arr[] = 'Passwords do not match';
    $errflag = true;
}

//Check for duplicate username
if ($username != '') {


    $query = $db->prepare('SELECT * FROM members WHERE username=:username');
    $query->execute(array('username' => $username));

    if ($query->rowCount() > 0) {
        $errmsg_arr[] = 'Username already in use';
        $errflag = true;
    }
}

//If there are input validations, redirect back to the registration form
if ($errflag) {
    $_SESSION['ERRMSG_ARR'] = $errmsg_arr;
    session_write_close();
    header("location: registration.php");
    exit();
}
$password2 = md5($password);
if ($member_type != 'General') {
    $to = 'ruthwitte@gmail.com, bjwitte@gmail.com';
    $subject = 'New member requesting membership type.';
    $body = 'New member ' . $username . ' ' . $fname . ' ' . $lname . ' ' . $email . ' requesting membership type of ' . $member_type . '';
    $headers = "From: mygp@growingplaces.cc\r\n" .
            mail($to, $subject, $body, $headers);
}

//Create INSERT query
$query2 = $db->prepare('INSERT INTO members(firstname, lastname, email, username, password, date_registered, member_type, confirm_hash, auth_type) VALUES(:fname,:lname,:email,:username,:password,:today ,"General",:hash, 0)');
$result = $query2->execute(array('fname' => $fname, 'lname' => $lname, 'email' => $email, 'username' => $username, 'password' => $password2, 'today' => $today, 'hash' => $hash));
$member_id = $db->lastInsertId('member_id');
if (is_array($_POST['volunteertypes'])) {
    $query4 = $db->prepare('INSERT INTO volunteer_types_by_member (volunteer_type_id, member_id) VALUES (:checked ,:member_id)');
    foreach ($_POST['volunteertypes'] as $checked) {
        $query4->execute(array('checked' => $checked, 'member_id' => $member_id));
    }
}
//Check whether the query was successful or not
if ($result) {
    $to = $email;

    $subject = "" . $site_name . " Confirmation";

    $message = "<html><head><title>" . $site_name . " Confirmation</title></head><body><p> Click the following link to confirm your registration information.</p><a href='http://www.growingplaces.cc/mygp/confirm.php?confirm=" . $hash . "'>http://www.growingplaces.cc/mygp/confirm.php?confirm=" . $hash . "</a>";


    $headers = "MIME-Version: 1.0\r\n";
    $headers .= "Content-type: text/html; charset=iso-8859-1\r\n";
    $headers .= "From: mygp@growingplaces.cc\r\n";

    $mailed = mail($to, $subject, $message, $headers);


    header("location: register-success.php");
    exit();
} else {
    die("Query failed");
}
?>