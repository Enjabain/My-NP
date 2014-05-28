<?php

include("config_mynonprofit.php");
include("functions.php");
auth();
include("connect.php");
include("template_header.php");

$member_id = $_REQUEST['member_id'];


$query = $db->prepare('
      SELECT *
      FROM
	members
      WHERE member_id = :member_id');
$query->execute(array('member_id' => $member_id));
foreach ($query as $row) {
    $member_firstname = $row['firstname'];
    $member_lastname = $row['lastname'];
    $member_email = $row['email'];
    $member_username = $row['username'];


    echo '<div class="content">';
    if ($member_id == $_SESSION['SESS_MEMBER_ID']) {
        echo '<form id="editmembersform" name="editmembersform" method="post" action="editmember.php">';
    }
    echo '<h2>' . $member_username . '&apos;s Info</h2><table><tr><th>Name</th><th>Email</th><th>Volunteer Interests</th>';
    if ($member_id == $_SESSION['SESS_MEMBER_ID']) {
        echo '<th>Edit</th>';
    }
    echo '</tr>';
    echo '<tr><td>' . $member_firstname . ' ' . $member_lastname . '</td>';
    echo '<td><a href="mailto:' . $member_email . '">' . $member_email . '</a></td>';
    echo '<td>';
    $query2 = $db->prepare('
      SELECT volunteer_types.volunteer_type_id, member_id, name
      FROM volunteer_types LEFT OUTER JOIN
	volunteer_types_by_member ON volunteer_types.volunteer_type_id = volunteer_types_by_member.volunteer_type_id AND member_id = :member_id');

    $query2->execute(array('member_id' => $member_id));
    foreach ($query2 as $row2) {
        $name = $row2['name'];
        $volunteer_type_id = $row2['volunteer_type_id'];
        $volunteermember_id = $row2['member_id'];


        echo ' <label>' . $name . ': <input type="checkbox" disabled name="volunteertypes[]" value="' . $volunteer_type_id . '"';
        if ($volunteermember_id != "") {
            echo 'checked ';
        }
        echo '/></label><br />';
    }
    echo'</td>';









    if ($member_id == $_SESSION['SESS_MEMBER_ID']) {
        echo '<td><button type="submit" value="' . $member_id . '" name="member_id">Edit</button></td>';
    }
    echo '</tr>';
}
echo '</table>';
if ($member_id == $_SESSION['SESS_MEMBER_ID']) {
    echo '</form>';
}
echo'<h2>' . $member_username . '&apos;s Jobs</h2>
	<table>
<tr><th>Name</th><th>Description</th><th>Type</th><th>Time Spent</th><th>Status</th></tr>';



$query3 = $db->prepare('
      SELECT *
      FROM
	volunteer_opportunities
      WHERE job_inprogressby = :member_id
      ORDER BY job_status');

$query3->execute(array('member_id' => $member_id));
if ($query3->rowCount() != 0) {
    $total_jobs = 0;
    foreach ($query3 as $row) {
        $job_name = $row['job_name'];
        $job_description = $row['job_description'];
        $job_type = $row['job_type'];
        $job_time = $row['job_time'];
        $job_status = $row['job_status'];
        $job_inprogressby = $row['job_inprogressby'];

        $hours = floor($job_time / 3600);
        $minutes = floor(($job_time - ($hours * 3600)) / 60);

        $job_status_message = '';

        if ($job_status == 1) {
            $job_status_message = '</td><td>In Progress';
        } elseif ($job_status == 2) {
            $job_status_message = '</td><td>Completion Pending';
        } elseif ($job_status == 3) {
            $total_jobs ++;
            $total_seconds += $job_time;
            if ($hours > 0) {
                $job_status_message .= '' . $hours . ' Hours<br />';
            }
            if ($minutes > 0) {
                $job_status_message .= '' . $minutes . ' Minutes';
            }

            $job_status_message .= '</td><td>Completed';
        }

        echo '<tr><td style="font-weight:bold;">' . $job_name . '</td>';
        echo '<td>' . $job_description . '</td><td>' . $job_type . '</td><td>' . $job_status_message . '</td>';
        echo '</tr>';
    }
    $total_hours = floor($total_seconds / 3600);
    $total_minutes = floor(($total_seconds - ($total_hours * 3600)) / 60);
    if ($total_jobs > 0) {
        echo '<tr><td colspan="3">Completed jobs: ' . $total_jobs . '</td><td colspan="3">Total time spent: ';
        if ($total_hours > 0) {
            echo '' . $total_hours . ' Hours<br />';
        }
        if ($total_minutes > 0) {
            echo '' . $total_minutes . ' Minutes';
        }
        echo '</td></tr>';
    }
} else {
    echo '<tr><td colspan="6">';
    if ($member_id == $_SESSION['SESS_MEMBER_ID']) {
        echo 'You ';
    } else {
        echo 'They ';
    }
    echo 'are not signed up for any jobs.</tr>';
}

echo '</table></div>';
?>
<?php include("template_footer.php"); ?>

