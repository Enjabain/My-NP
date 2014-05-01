<?php

include("../config_mynonprofit.php");
include("../functions.php");
authAdmin();
include("../connect.php");
//Array to store validation errors
$errmsg_arr = array();

//Validation error flag
$errflag = false;

//Function to sanitize values received from the form. Prevents SQL injection
//Sanitize the POST values
$member_id = htmlentities($_POST['member_id']);
$username = htmlentities($_POST['username']);
$firstname = htmlentities($_POST['firstname']);
$lastname = htmlentities($_POST['lastname']);
$email = htmlentities($_POST['email']);
$member_type = htmlentities($_POST['member_type']);
$finalize = htmlentities($_POST['finalize']);
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
    if ($member_type == '') {
        $errmsg_arr[] = 'Member type missing';
        $errflag = true;
    }


    //If there are input validations, redirect back to the registration form
    if ($errflag) {
        $_SESSION['ERRMSG_ARR'] = $errmsg_arr;
        session_write_close();
        header("location: editmember.php");
        exit();
    }

    //Create UPDATE query


    if ($finalize == 'remove') {
        $member_type = 'Removed';
    }
    $query1 = $db->prepare('SELECT member_type, email FROM members
WHERE member_id=:member_id LIMIT 1');
    $query1->execute(array('member_id' => $member_id));
    foreach ($query1 as $row) {
        if ($member_type != $row['member_type']) {
            $to = $email;
            $subject = '' . $site_name . ' Member Type Change';
            $content = '<p>Your membership type has been changed to '.$member_type.'.</p><p>This will allow you to view events of this type.</p>';
            $content .= '<p>Please login <a href="' . $site_url . '">here</a> for updates.</p>';
            $headers = "MIME-Version: 1.0\r\n";
            $headers .= "Content-type: text/html; charset=iso-8859-1\r\n";
            $headers .= "From: mygp@growingplaces.cc\r\n";
            $mailed = mail($to, $subject, $content, $headers);
        }
    }

    $query = $db->prepare('UPDATE members
SET username = :username, firstname=:firstname, lastname = :lastname, email = :email, member_type = :member_type
WHERE member_id=:member_id LIMIT 1');

    $result = $query->execute(array('username' => $username, 'firstname' => $firstname, 'lastname' => $lastname, 'email' => $email, 'member_type' => $member_type, 'member_id' => $member_id));

    //Check whether the query was successful or not
    if ($result) {
        header("location: index.php");
        exit();
    } else {
        die("Query failed");
    }
}
if ($volunteertypememberid != '') {
    $query2 = $db->prepare('DELETE FROM volunteer_types_by_member WHERE member_id = :volunteertypememberid');
    $query2->execute(array('volunteertypememberid' => $volunteertypememberid));
    $query3 = $db->prepare('INSERT INTO volunteer_types_by_member (volunteer_type_id, member_id) VALUES (:checked ,:volunteertypememberid)');
    foreach ($_POST['volunteertypes'] as $checked) {
        $query3->execute(array('checked' => $checked, 'volunteertypememberid' => $volunteertypememberid));
    }
    if ($result4) {
        header("location: index.php");
        exit();
    } else {
        die("Query failed");
    }
}
?>