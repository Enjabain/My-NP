<?php

include("config_mynonprofit.php");
include("functions.php");
auth();
include("connect.php");
//Array to store validation errors
$errmsg_arr = array();

//Validation error flag
$errflag = false;


//Sanitize the POST values
$member_id = htmlentities($_POST['member_id']);
$username = htmlentities($_POST['username']);
$firstname = htmlentities($_POST['firstname']);
$lastname = htmlentities($_POST['lastname']);
$email = htmlentities($_POST['email']);
$passwordmemberid = htmlentities($_POST['passwordmemberid']);
$oldpassword = htmlentities($_POST['oldpassword']);
$newpassword = htmlentities($_POST['newpassword']);
$membertypememberid = htmlentities($_POST['membertypememberid']);
$member_type = htmlentities($_POST['member_type']);
$volunteertypememberid = htmlentities($_POST['volunteertypememberid']);
if ($member_id != '') {

//Input Validations
    if ($member_id == '') {
        $errmsg_arr[] = 'Member ID missing';
        $errflag = true;
    }
    if ($username == '') {
        $errmsg_arr[] = 'username missing';
        $errflag = true;
    }
    if ($firstname == '') {
        $errmsg_arr[] = 'First name missing';
        $errflag = true;
    }
    if ($lastname == '') {
        $errmsg_arr[] = 'Last name missing';
        $errflag = true;
    }
    if ($email == '') {
        $errmsg_arr[] = 'Email missing';
        $errflag = true;
    }
}
if ($passwordmemberid != '') {

    if ($oldpassword == '') {
        $errmsg_arr[] = 'Password missing';
        $errflag = true;
    }
    if ($newpassword == '') {
        $errmsg_arr[] = 'New Password missing';
        $errflag = true;
    }
    $newpassword = md5($newpassword);
    $oldpassword = md5($oldpassword);


    $query2 = $db->prepare('
      SELECT password FROM members WHERE member_id = :passwordmemberid');
    $query2->execute(array('passwordmemberid' => $passwordmemberid));

    foreach ($query2 as $row2) {

        $password = $row2['password'];
    }
    if ($password == $oldpassword) {
        $query7 = $db->prepare('UPDATE members SET password = :newpassword WHERE member_id = :passwordmemberid');
        $result7 = $query7->execute(array('newpassword' => $newpassword, 'passwordmemberid' => $passwordmemberid));
    } else {

        $errmsg_arr[] = 'Current Password Incorrect';
        $errflag = true;
    }
}


if ($membertypememberid != '') {


    $query3 = $db->prepare('SELECT * FROM members WHERE member_id = :membertypememberid');
    $result3 = $query3->execute(array('membertypememberid' => $membertypememberid));
    foreach ($query3 as $row3) {
        $username = $row3['username'];
        $firstname = $row3['firstname'];
        $lastname = $row3['lastname'];
        $email = $row3['email'];
    }


    $to = 'ruthwitte@gmail.com, bjwitte@gmail.com';
    $subject = 'Existing member requesting membership type.';
    $body = 'Existing member ' . $username . ' ' . $firstname . ' ' . $lastname . ' ' . $email . ' requesting membership type of ' . $member_type . '';
    $headers = "From: mygp@growingplaces.cc\r\n" .
            mail($to, $subject, $body, $headers);
}


if ($volunteertypememberid != '') {
    $query5 = $db->prepare('DELETE FROM volunteer_types_by_member WHERE member_id = :volunteertypememberid');
    $query5->execute(array('volunteertypememberid' => $volunteertypememberid));
    foreach ($_POST['volunteertypes'] as $checked) {
        $query4 = $db->prepare('INSERT INTO volunteer_types_by_member (volunteer_type_id, member_id) VALUES (:checked ,:volunteertypememberid)');
        $query4->execute(array('checked' => $checked, 'volunteertypememberid' => $volunteertypememberid));
    }
}

//If there are input validations, redirect back to the registration form
if ($errflag) {
    $_SESSION['ERRMSG_ARR'] = $errmsg_arr;
    session_write_close();
    header("location: editmember.php");
    exit();
}
if ($member_id != $_SESSION['SESS_MEMBER_ID'] && $passwordmemberid != $_SESSION['SESS_MEMBER_ID']) {

    header("location: index.php");
    exit();
}




if ($member_id != '') {

//Create UPDATE query

    $query6 = $db->prepare('UPDATE members
SET username = :username, firstname=:firstname, lastname = :lastname, email = :email
WHERE member_id=:member_id');


    $result6 = $query6->execute(array('username' => $username, 'firstname' => $firstname, 'lastname' => $lastname, 'email' => $email, 'member_id' => $member_id));
}
//Check whether the query was successful or not
if ($result7 || $result6 || $result3 || $result5) {
    header("location: index.php");
    exit();
} else {
    die("Query failed");
}
?>