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
$suggestion_id = htmlentities($_POST['suggestion_id']);
$suggestion = htmlentities($_POST['suggestion']);
$category = htmlentities($_POST['category']);
$suggested_by = htmlentities($_POST['suggested_by']);
$finalize = htmlentities($_POST['finalize']);
$suggestion_status = htmlentities($_POST['suggestion_status']);


//Input Validations
if ($suggestion_id == '') {
    $errmsg_arr[] = 'Suggestion ID missing';
    $errflag = true;
}
if ($suggestion == '') {
    $errmsg_arr[] = 'Suggestion content missing';
    $errflag = true;
}
if ($category == '') {
    $errmsg_arr[] = 'Category missing';
    $errflag = true;
}



if ($errflag) {
    $_SESSION['ERRMSG_ARR'] = $errmsg_arr;
    session_write_close();
    header("location: editsuggestion.php?suggestion_id=" . $suggestion_id . "");
    exit();
}

//Create UPDATE query
if ($finalize != 'update') {
    if ($finalize == 'approve') {
        $suggestion_status = 1;
    } elseif ($finalize == 'pending') {
        $suggestion_status = 2;
    } elseif ($finalize == 'private') {
        $suggestion_status = 3;
    } elseif ($finalize == 'public') {
        if ($suggested_by != '') {
            $suggestion_status = 2;
        } else {
            $suggestion_status = 3;
        }
    }
}


if ($finalize == 'delete') {
    $query3 = $db->prepare('DELETE FROM mynp_suggestions
WHERE suggestion_id=:suggestion_id LIMIT 1');
    $result3 = $query3->execute(array('suggestion_id' => $suggestion_id));
} else {
    $query = $db->prepare('UPDATE mynp_suggestions
SET suggestion = :suggestion, category=:category, suggestion_status = :suggestion_status
WHERE suggestion_id=:suggestion_id');
    $result = $query->execute(array('suggestion' => $suggestion, 'category' => $category, 'suggestion_status' => $suggestion_status, 'suggestion_id' => $suggestion_id));
}



//Check whether the query was successful or not
if ($result || $result3) {
    header("location: index.php");
    exit();
} else {
    die("Query failed");
}
?>