<?php

include("config_mynonprofit.php");
include("connect.php");
session_start();


//Array to store validation errors
$errmsg_arr = array();

//Validation error flag
$errflag = false;

$username = htmlentities($_POST['username']);
$password = htmlentities($_POST['password']);
//Input Validations
if ($username == '') {
    $errmsg_arr[] = 'Username missing';
    $errflag = true;
}
if ($password == '') {
    $errmsg_arr[] = 'Password missing';
    $errflag = true;
}
$password2 = md5($password);

//If there are input validations, redirect back to the username form
if ($errflag) {
    $_SESSION['ERRMSG_ARR'] = $errmsg_arr;
    session_write_close();
    header("location: login.php");
    exit();
}
//Create query

$query = $db->prepare('
SELECT * FROM members WHERE username = :username AND password = :password');
$result = $query->execute(array('username' => $username, 'password' => $password2));


if ($result) {
    if ($query->rowCount() == 1) {
        foreach ($query as $row) {
            session_regenerate_id();
            $_SESSION['SESS_MEMBER_ID'] = $row['member_id'];
            $_SESSION['SESS_FIRST_NAME'] = $row['firstname'];
            $_SESSION['SESS_LAST_NAME'] = $row['lastname'];
            $_SESSION['SESS_MEMBER_TYPE'] = $row['member_type'];
            $_SESSION['SESS_AUTH_TYPE'] = $row['auth_type'];
            $_SESSION['SESS_USERNAME'] = $row['username'];
            session_write_close();
            header("location: index.php");
            exit();
        }
        } else {
            //Login failed
            header("location: login-failed.php");
            exit();
        }
    } else {
        die("Query failed");
    }
?>