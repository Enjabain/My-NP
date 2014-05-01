<?php

session_start();
include("config_mynonprofit.php");
include("functions.php");
include("connect.php");

if (!isset($_SESSION['SESS_MEMBER_ID']) || (trim($_SESSION['SESS_MEMBER_ID']) == '')) {
    $loggedin = "false";
} else
    $loggedin = "true";
include("template_header.php");
?>
<script type="text/javascript">
    $(document).ready(function() {
        $("input[name=namedanonymous]").change(function() {

            if ($("input[name=namedanonymous]:checked").val() === 'anonymous') {
                $('input:radio[name=publicprivate][value="private"]').prop('checked', true);
                $('input[name=publicprivate][value="public"]').prop('disabled',true);
            }
            if ($("input[name=namedanonymous]:checked").val() === 'named') {
                $('input:radio[name=publicprivate][value="public"]').prop('checked', true);
                $('input[name=publicprivate][value="public"]').prop('disabled',false);
            }
        });
    });
</script>
<?php

if ($loggedin == "false") {
    echo '<p id="welcome">Welcome! Here you can sign up for volunteer opportunities and events. Some events are only visible when logged in.</p>';
}
if ($loggedin == "true") {


    echo'<div class="section"><div class="leftmain"><form id="jobforfitform" name="jobforfitform" method="post" action="jobforfeit-exec.php">
<h2>My Jobs</h2><table>
<tr><th>Name</th><th>Description</th><th>Type</th><th>Status</th>';
    $query = $db->prepare('
        SELECT *
        FROM volunteer_opportunities
        WHERE job_inprogressby = :member_id AND job_status != 3 AND job_status != -1
        ORDER BY job_bestfinishedby');
    $query->execute(array('member_id' => $_SESSION['SESS_MEMBER_ID']));
    if ($query->rowCount() != 0) {
        foreach ($query as $row) {
            $job_name = $row['job_name'];
            $job_description = $row['job_description'];
            $job_type = $row['job_type'];
            $job_bestfinishedby = $row['job_bestfinishedby'];
            $job_status = $row['job_status'];
            $job_time = $row['job_time'];
            $job_inprogressby = $row['job_inprogressby'];
            $job_id = $row['job_id'];
            $dateTime = new DateTime($job_bestfinishedby);
            $job_bestfinishedby = date_format($dateTime, "n-d-Y");

            $hours = floor($job_time / 3600);
            $minutes = floor(($job_time - ($hours * 3600)) / 60);

            if ($job_status == 1) {
                echo '<tr><td style="font-weight:bold;">' . $job_name . '</td>';
                echo '<td>' . $job_description . '</td><td>' . $job_type . '</td><td>In progress<br />
<button type="submit" value="' . $job_id . '" name="job_forfeit_id">Forfeit</button><br /><br />Hours:<input type="text" style="width:20px;" name="hours" value="" /><br />Minutes:<input type="text"  style="width:20px;" name="minutes" value="" /><button type="submit" value="' . $job_id . '" name="job_complete_id">Complete</button></td>';
                echo '</tr>';
            } elseif ($job_status == 2) {
                echo '<tr><td style="font-weight:bold;">' . $job_name . '</td>';
                echo '<td>' . $job_description . '</td><td>' . $job_type . '</td><td>Completion pending<br />
<button type="submit" value="' . $job_id . '" name="job_inprogress_id">Incomplete</button><br />Hours:<input type="text"  style="width:20px;" name="hours" value="' . $hours . '" /><br />Minutes:<input type="text" style="width:20px;" name="minutes" value="' . $minutes . '" /></td>';
                echo '</tr>';
            }
        }
        echo '<tr><td colspan="5">If your job requires further arrangments, <a href="contact.php">contact us</a> or sign up for a volunteer event.</tr>';
    } else {

        echo '<tr><td colspan="5">You are not signed up for any jobs.</tr>';
    }
    echo '</table></form></div>';

    echo'<div class="rightmain"><form id="eventcancelform" name="eventcancelform" method="post" action="eventcancel-exec.php">
<h2>My Events</h2><table style="font-size:12px; color:#000;" width="500" border="1">
<tr><th>Name</th><th>Description</th><th>Type</th><th width="75">Date/Time</th><th style="width:200px;">Attendees</th><th style="width:100px;">Status</th>';

    $query2 = $db->prepare('
	SELECT *
	FROM members AS M, events_by_member AS EM, events AS E LEFT JOIN event_details ON E.event_details_id = event_details.event_details_id 
	WHERE M.member_id = EM.member_id
	AND EM.event_id = E.event_id
	AND M.member_id = :member_id
        AND event_status = 0
      	ORDER BY E.event_date');
    $query2->execute(array('member_id' => $_SESSION['SESS_MEMBER_ID']));
    if ($query2->rowCount() != 0) {
        foreach ($query2 as $row) {
            $event_name = $row['event_name'];
            $event_description = $row['event_description'];
            $event_type = $row['event_type'];
            $event_ispotluck = $row['event_ispotluck'];
            $event_date = $row['event_date'];
            $event_id = $row['event_id'];
            $event_status = $row['event_status'];
            $dateTime = new DateTime($event_date);
            $event_date = date_format($dateTime, "l") .
                    "<br/>" .
                    date_format($dateTime, "n-j-Y") .
                    "<br/>" .
                    date_format($dateTime, "g:i A");

            if ($event_status == 0) {
                echo '<tr><td style="font-weight:bold;">' . $event_name . '</td>';
                echo '<td>' . $event_description . '</td><td>' . $event_type . '</td><td>' . $event_date . '</td><td class="attendeestd">';
                attendees($event_ispotluck, $event_id);
                echo '
</td><td>Attending<br />
<button type="submit" value="' . $event_id . '" name="event_id">Decline</button><br />
To edit your RSVP, decline and sign up again.
</td>';
                echo '</tr>';
//            } elseif ($event_status == 1) {
//                echo '<tr><td>' . $event_name . '</td>';
//                echo '<td>' . $event_description . '</td><td>' . $event_type . '</td><td>' . $event_date . '</td><td></td><td>Canceled</td>';
//                echo '</tr>';
            }
        }
    } else {
        echo '<tr><td colspan="6">You are not signed up for any events.</td></tr>';
    }
    echo '</table></form></div></div>';
}

echo'<br style="clear:both;" /><div class="section"><div class="leftmain">
<form id="jobsignupform" name="jobsignupform" method="post" action="jobsignup-exec.php">
    <h2>Volunteer Opportunities</h2>
    <table>
<tr style="font-weight:bold;" ><th>Name</th><th>Description</th><th>Type</th><th>Status</th>';


$query3 = $db->prepare('
      SELECT *
      FROM
	volunteer_opportunities
      LEFT JOIN members
      ON volunteer_opportunities.job_inprogressby=members.member_id
      WHERE job_status != "-1"
      AND job_status != "3"
      ORDER BY job_type');
$query3->execute();
if ($query3->rowCount() != 0) {
    foreach ($query3 as $row) {
        $job_name = $row['job_name'];
        $job_description = $row['job_description'];
        $job_type = $row['job_type'];
        $job_bestfinishedby = $row['job_bestfinishedby'];
        $job_status = $row['job_status'];
        $job_inprogressby = $row['job_inprogressby'];
        $job_id = $row['job_id'];
        $member_username = $row['username'];
        $member_id = $row['member_id'];
        $job_status_message = '';
        if ($job_status == '0') {
            $job_status_message = '';
        } else if ($job_status == '1') {
            $job_status_message = 'In progress by <a href="member.php?member_id=' . $member_id . '">' . $member_username . '</a><br />';
        } else if ($job_status == '2') {
            $job_status_message = 'Completion pending by <a href="member.php?member_id=' . $member_id . '">' . $member_username . '</a><br />';
        }
        $dateTime = new DateTime($job_bestfinishedby);
        $job_bestfinishedby = date_format($dateTime, "n-d-Y");

        if ($job_status == 0) {
            echo '<tr><td style="font-weight:bold;">' . $job_name . '</td>';
            echo '<td>' . $job_description . '</td><td>' . $job_type . '</td><td>';

            if ($loggedin == "true") {
                echo '<button type="submit" value="' . $job_id . '" name="job_id">Sign up</button>';
            } else {
                echo '<a href="login.php">Login to sign up</a>';
            }
            echo '</td></tr>';
        } else {
            echo '<tr><td style="font-weight:bold;">' . $job_name . '</td>';
            echo '<td>' . $job_description . '</td><td>' . $job_type . '</td><td>' . $job_status_message . '</td>';
            echo '</tr>';
        }
    }
} else {
    echo '<tr><td colspan="5">There are no jobs listed currently.</tr>';
}
echo '</table></form><a style="float:right; margin-right:10px;" href="completedjobs.php">Completed Jobs</a><br/></div>';


echo'<div class="rightmain"><h2>Upcoming Events</h2><table style="font-size:12px; color:#000;">
    <tr style="font-weight:bold;"><th>Name</th><th>Description</th><th>Type</th><th>Date/Time</th><th style="width:200px;">Attendees</th><th class="signupcell">Sign up</th>';
$signed_up = 'false';
$query4 = $db->prepare('
	SELECT *
	FROM events LEFT JOIN event_details ON events.event_details_id = event_details.event_details_id
	WHERE event_status = 0
        GROUP BY events.event_details_id
      	ORDER BY event_date
        ');
$query4->execute();
if ($query4->rowCount() != 0) {
    foreach ($query4 as $row) {
        $event_name = $row['event_name'];
        $event_description = $row['event_description'];
        $event_type = $row['event_type'];
        $event_ispotluck = $row['event_ispotluck'];
        $event_auth_type = $row['event_auth_type'];
        $event_date = $row['event_date'];
        $event_status = $row['event_status'];
        $event_details_id = $row['event_details_id'];
        $event_id = $row['event_id'];
        $event_recurrs = $row['event_recurrs'];
        $dateTime = new DateTime($event_date);
        $event_date = date_format($dateTime, "l") .
                "<br />" .
                date_format($dateTime, "n-j-Y") .
                "<br />" .
                date_format($dateTime, "g:i A");

        if ($loggedin == 'true') {
            $query2 = $db->prepare('
	SELECT *
	FROM events_by_member
	WHERE event_id = :event_id
	AND member_id = :member_id');
            $query2->execute(array('event_id' => $event_id, 'member_id' => $_SESSION['SESS_MEMBER_ID']));
            if ($query2->rowCount() != 0) {
                $signed_up = 'true';
            } else {
                $signed_up = 'false';
            }
        }

        if ($signed_up == 'false' && authEvent($event_auth_type, $event_type)) {
            echo '<tr><td style="font-weight:bold;">' . $event_name . '</td>';
            echo '<td>' . $event_description . '</td><td>' . $event_type . '</td><td>' . $event_date . '';
            if ($event_recurrs == 1) {
                echo'<br /><a href="recurringevent.php?event=' . $event_details_id . '">view recurrences</a>';
            }
            echo '</td><td class="attendeestd">';
            attendees($event_ispotluck, $event_id);
            echo '</td><td class="signupcell">';
            if ($loggedin == "true") {
                echo'
<form method="post" action="eventsignup-exec.php">                
<table class="signup">
    <tr>
        <td><div class="signupadults"># Adults<br />
            <select name="number_adults">
                <option value="0">0</option>
                <option value="1" selected>1</option>
                <option value="2">2</option>
                <option value="3">3</option>
                <option value="4">4</option>
                <option value="5">5</option>
                <option value="6">6</option>
                <option value="7">7</option>
                <option value="8">8</option>
                <option value="9">9</option>
                <option value="10">10</option>
            </select>
            </div>
            <div class="signupkids">
        # Kids<br />
            <select name="number_children">
                <option value="0">0</option>
                <option value="1">1</option>
                <option value="2">2</option>
                <option value="3">3</option>
                <option value="4">4</option>
                <option value="5">5</option>
                <option value="6">6</option>
                <option value="7">7</option>
                <option value="8">8</option>
                <option value="9">9</option>
                <option value="10">10</option>
            </select>
        </td>
    </tr>';
                if ($event_ispotluck == 1) {
                    echo'<tr><td colspan="2">Potluck Item(s)<input class="potluck" type="text" value="none" name="potluck_item" size="5"></input></td></tr>';
                }
                echo'<tr><td colspan="2"><button type="submit" value="' . $event_id . '" name="event_id">Sign up</button></td></tr>
</table></form>';
            } else {
                echo '<a href="login.php">Login to sign up</a>';
            }
            echo '</td>';
            echo'</tr>';
        }
    }
} else {
    echo '<tr><td colspan="6">There are no events listed currently.</tr>';
}
echo '</table><a style="float:right; margin-right:10px;" href="pastevents.php">Past Events</a><br/></div>';

echo'
<br style="clear:both;"/>
<div class="section"><div class="leftmain"><h2>Suggestions</h2>
    <table><tr><th>Suggestion</th><th>Category</th><th>Suggested by</th><th>Votes</th></tr>
           <tr><td></td><td></td><td></td><td></td></tr></table></div>';
if ($loggedin == "true") {
    echo'<div class="rightmain">
      <h2>Make a Suggestion</h2>
            <form method="post" action="suggestion-exec.php" >
           <table><tr><th>Suggestion</th><td><textarea></textarea></td></tr>
           <tr><th>Category</th><td><select><option>General</option><option>School</option><option>Garden</option><option>Quaker</option><option>Website</option><option>Other</option></select></td></tr>
           <tr><th>Anonymity</th><td><label><input value="named" checked name="namedanonymous" type="radio" />Named (' . $_SESSION['SESS_USERNAME'] . ')</label><br /><label><input value="anonymous" name="namedanonymous" type="radio" />Anonymous (Must be private)</label></td></tr>
           <tr><th>Public/Private</th><td><label><input value="public" checked name="publicprivate" type="radio" />Public (Requires Moderation)</label><br /><label><input value="private" name="publicprivate" type="radio" />Private</label></td></tr>
           <tr><th></th><td><input type="submit" value="Submit" /></td></tr>
    </table></form></div></div>';
}
include("template_footer.php");
?>