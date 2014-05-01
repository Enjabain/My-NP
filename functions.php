<?php

function auth() {

    session_start();
    if (!isset($_SESSION['SESS_USERNAME']) || (trim($_SESSION['SESS_USERNAME']) == '') || ($_SESSION['SESS_AUTH_TYPE'] == "")) {
        header("location: login.php");
        exit();
    } elseif ($_SESSION['SESS_AUTH_TYPE'] == 0) {
        header("location: confirm.php");

        exit();
    }
}

function authAdmin() {

    session_start();

    if (!isset($_SESSION['SESS_USERNAME']) || (trim($_SESSION['SESS_USERNAME']) == '') || ($_SESSION['SESS_AUTH_TYPE'] != '6')) {
        header("location: ../index.php");
        exit();
    }
}

function authEvent($event_auth_type, $event_type) {
    $authedevent = 0;
    if ($event_auth_type == 1 || $_SESSION['SESS_MEMBER_TYPE'] == $event_type || ($_SESSION['SESS_AUTH_TYPE'] >= 3)) {
        $authedevent = 1;
    }
    return $authedevent;
}

function attendees($event_ispotluck, $event_id) {
    global $db;
    $query1 = $db->prepare('
	SELECT M.member_id, M.username, EM.potluck_item, EM.number_adults, EM.number_children
	FROM members AS M, events_by_member AS EM, events AS E
	WHERE M.member_id = EM.member_id
	AND EM.event_id = E.event_id
	AND E.event_id = :event_id');
    $query1->execute(array('event_id' => $event_id));
    if ($query1->rowCount() != 0) {
        $total_adults = 0;
        $total_children = 0;
        $total_attendees = 0;
        echo'<span class="attendeeshover">Hover to see attendees.</span>';
        echo '<table class="attendees"><tr><th>Name&nbsp;</th><th>Adults&nbsp;</th><th>Kids&nbsp;</th>';
        if ($event_ispotluck == 1) {
            echo'<th>Potluck</th>';
        }
        echo '</tr>';
        foreach ($query1 as $row1) {

            $member_username = $row1['username'];
            $member_id = $row1['member_id'];
            $potluck_item = $row1['potluck_item'];
            $number_adults = $row1['number_adults'];
            $number_children = $row1['number_children'];
            $total_adults = $total_adults + $number_adults;
            $total_children = $total_children + $number_children;
            if ($member_id == $_SESSION['SESS_MEMBER_ID']) {
                echo '<tr style="background-color:#D6FF8E;">';
            } else
                echo '<tr>';
            echo '<td><a href="member.php?member_id=' . $member_id . '">' . $member_username . '</a></td><td>' . $number_adults . '</td><td>' . $number_children . '</td>';
            if ($event_ispotluck == 1)
                echo'<td>' . $potluck_item . '</td>';
            echo '</tr>';
        }
        $total_attendees = $total_adults + $total_children;
        echo '<tr style="border-top:solid 1px #000000;"><td></td><td>' . $total_adults . '</td><td>' . $total_children . '</td>';
        if ($event_ispotluck == 1)
            echo'<td></td>'; echo'</tr></table>';
    } else
        echo 'No one has signed up yet.';
}

?>