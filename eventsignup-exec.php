<?php

include("config_mynonprofit.php");
include("functions.php");
auth();
include("connect.php");



$member_id = $_SESSION['SESS_MEMBER_ID'];

//Sanitize the POST values
$event_id = htmlentities($_POST['event_id']);
$potluck_item = htmlentities($_POST['potluck_item']);
$number_adults = htmlentities($_POST['number_adults']);
$number_children = htmlentities($_POST['number_children']);


try {

$query = $db->prepare('INSERT INTO events_by_member
SET event_id= :event_id, member_id= :member_id, potluck_item= :potluck_item, number_adults= :number_adults, number_children= :number_children');
//ON DUPLICATE KEY UPDATE event_id= :event_id, member_id= :member_id, potluck_item= :potluck_item, number_adults= :number_adults, number_children= :number_children');
    


$result = $query->execute(array('event_id' => $event_id, 'member_id' => $member_id, 'potluck_item' => $potluck_item, 'number_adults' => $number_adults, 'number_children' => $number_children));
} catch (PDOException $ex) {
   echo $ex->getMessage();
}
//Check whether the query was successful or not
if ($result) {
    header("location: index.php");
    exit();
} else {
    die("Query failed");
}
?>