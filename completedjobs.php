<?php

include("config_mynonprofit.php");
include("functions.php");
auth();
include("connect.php");
include("template_header.php");


echo'
<div style="max-width:600px;">
	<h2>Completed Jobs</h2>
	<table>
<tr><th>Name</th><th>Description</th><th>Type</th><th>Best Finished By Date</th><th style="width:75px;">Time Spent</th><th>Status</th></tr>
';


$query = $db->prepare('
      SELECT *
      FROM
	volunteer_opportunities
      LEFT JOIN members
      ON volunteer_opportunities.job_inprogressby=members.member_id
      WHERE job_status = "3"
      ORDER BY job_bestfinishedby DESC
');
$query->execute();
$total_jobs = 0;
foreach ($query as $row) {
    $job_name = $row['job_name'];
    $job_description = $row['job_description'];
    $job_type = $row['job_type'];
    $job_bestfinishedby = $row['job_bestfinishedby'];
    $job_timeallowance = $row['job_timeallowance'];
    $job_status = $row['job_status'];
    $job_time = $row['job_time'];
    $job_inprogressby = $row['job_inprogressby'];
    $job_id = $row['job_id'];
    $member_username = $row['username'];
    $member_id = $row['member_id'];

    $hours = floor($job_time / 3600);
    $minutes = floor(($job_time - ($hours * 3600)) / 60);


    $job_status_message = '';
    if ($job_status == '0') {
        $job_status_message = '';
    } else if ($job_status == '1') {
        $job_status_message = 'In progress by <a href="member.php?member_id=' . $member_id . '">' . $member_username . '</a><br />';
    } else if ($job_status == '2') {
        $job_status_message = 'Completion pending by <a href="member.php?member_id=' . $member_id . '">' . $member_username . '</a><br />';
    } else if ($job_status == '3') {
        $total_jobs ++;
        $total_seconds += $job_time;
        $job_status_message = 'Completed by <a href="member.php?member_id=' . $member_id . '">' . $member_username . '</a><br />';
    }
    $dateTime = new DateTime($job_bestfinishedby);
    $job_bestfinishedby = date_format($dateTime, "n-d-Y");

    echo '<tr><td style="font-weight:bold;">' . $job_name . '</td>';
    echo '<td>' . $job_description . '</td><td>' . $job_type . '</td><td>' . $job_bestfinishedby . '</td><td>';
    if ($hours > 0) {
        echo '' . $hours . ' Hours<br />';
    }
    if ($minutes > 0) {
        echo '' . $minutes . ' Minutes';
    }
    echo '</td><td>' . $job_status_message . '</td>';
    echo '</tr>';
}


$total_hours = floor($total_seconds / 3600);
$total_minutes = floor(($total_seconds - ($total_hours * 3600)) / 60);
if ($total_jobs > 0) {
    echo '<tr><td colspan="3">Jobs completed by community: ' . $total_jobs . '</td><td colspan="3">Total community time spent: ';
    if ($total_hours > 0) {
        echo '' . $total_hours . ' Hours<br />';
    }
    if ($total_minutes > 0) {
        echo '' . $total_minutes . ' Minutes';
    }
    echo '</td></tr>';
}
echo '</table></div>';
?>
<?php include("template_footer.php"); ?>