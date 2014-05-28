<?php
include("config_mynonprofit.php");
include("connect.php");
include("functions.php");
auth();

$suggested_by = $_SESSION['SESS_MEMBER_ID'];

//Array to store validation errors
$errmsg_arr = array();

//Validation error flag
$errflag = false;



//Sanitize the POST values
$suggestion = htmlentities($_POST['suggestion']);
$category = htmlentities($_POST['category']);
$publicprivate = htmlentities($_POST['publicprivate']);
$namedanonymous = htmlentities($_POST['namedanonymous']);
$votes = 1;
if ($publicprivate == 'private') {
    $suggestion_status = 3; //active
} elseif ($publicprivate == 'public') {
    $suggestion_status = 2;//pending (requires moderation)
}
if ($namedanonymous == 'anonymous') {
    $suggested_by = '';
}
//Input Validations
if ($suggestion == '') {
    $errmsg_arr[] = 'Suggestion content missing';
    $errflag = true;
}
if ($category == '') {
    $errmsg_arr[] = 'Suggestion category missing';
    $errflag = true;
}



//If there are input validations, redirect back to the registration form
if ($errflag) {
    $_SESSION['ERRMSG_ARR'] = $errmsg_arr;
    session_write_close();
    header("location: index.php");
    exit();
}

//Create INSERT query
$query = $db->prepare('INSERT INTO mynp_suggestions(suggestion, category, suggested_by, suggestion_status, votes) VALUES(:suggestion, :category, :suggested_by, :suggestion_status, :votes)');
$result = $query->execute(array('suggestion' => $suggestion, 'category' => $category, 'suggested_by' => $suggested_by, 'suggestion_status' => $suggestion_status, 'votes' => $votes));

//Check whether the query was successful or not
if ($result) {
    header("location: index.php");
    exit();
} else {
    die("Query failed");
}
?>
</body>
</html>