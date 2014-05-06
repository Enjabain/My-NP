<?php
include("../config_mynonprofit.php");
include("../functions.php");
authAdmin();
include("../connect.php");
include("template_header.php");



echo'
<div class="section">
<div class="leftmain">
<form id="editjobform" name="editjobform" method="post" action="editjob.php">
	<h2>Volunteer Opportunites</h2>
	<table style="font-size:12px; color:#000;">
<tr><th>Name</th><th>Description</th><th>Type</th><th>Status</th>
';


$query = $db->prepare('SELECT *
      FROM volunteer_opportunities
      LEFT JOIN members
      ON volunteer_opportunities.job_inprogressby=members.member_id
      WHERE job_status != "-1"
      AND job_status != "3"
      ORDER BY job_type');

$query->execute();

foreach ($query as $row) {
//}
//$result = mysql_query("$query") or die("Error: " . mysql_error());
//while ($row = mysql_fetch_array($result)) {
    $job_name = $row['job_name'];
    $job_description = $row['job_description'];
    $job_type = $row['job_type'];
    $job_timeallowance = $row['job_timeallowance'];
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

    echo '<tr><td style="font-weight:bold;">' . $job_name . '</td>';
    echo '<td>' . $job_description . '</td><td>' . $job_type . '</td><td>
' . $job_status_message . '
<button type="submit" value="' . $job_id . '" name="job_id">Edit</button></td>';
    echo '</tr>';
}
echo '</table></form><a style="float:right; margin-right:10px;" href="completedjobs.php">Completed Jobs</a></div>';
?>
<?php
echo'
<div class="rightmain">
<form id="editeventform" name="editeventform" method="post" action="editevent.php">
	<h2>Upcoming Events</h2>
	<table style="font-size:12px; color:#000;">
<tr><th>Name</th><th>Description</th><th>Type</th><th>Date</th><th style="width:200px;">Attendees</th><th>Status</th>
';



$query1 = $db->prepare('
	SELECT *
	FROM events LEFT JOIN event_details ON events.event_details_id = event_details.event_details_id
	WHERE event_status = "0"
        GROUP BY events.event_details_id
	ORDER BY event_date
	');
$query1->execute();
foreach ($query1 as $row1) {
    $event_name = $row1['event_name'];
    $event_description = $row1['event_description'];
    $event_type = $row1['event_type'];
    $event_ispotluck = $row1['event_ispotluck'];
    $event_date = $row1['event_date'];
    $event_status = $row1['event_status'];
    $event_id = $row1['event_id'];
    $event_details_id = $row1['event_details_id'];
    $event_recurrs = $row1['event_recurrs'];
    $dateTime = new DateTime($event_date);
    $event_date = date_format($dateTime, "l") .
            "<br/>" .
            date_format($dateTime, "n-j-Y") .
            "<br/>" .
            date_format($dateTime, "g:i A");
    echo '<tr><td style="font-weight:bold;">' . $event_name . '</td>';
    echo '<td>' . $event_description . '</td><td>' . $event_type . '</td><td>' . $event_date . '';
    if ($event_recurrs == 1) {
        echo'<br /><a href="recurringevent.php?event=' . $event_details_id . '">view recurrences</a>';
    }
    echo '</td><td class="attendeestd">';
    attendees($event_ispotluck, $event_id);


    echo'</td><td>
<button type="submit" value="' . $event_id . '" name="event_id">Edit</button></td>';
    echo '</tr>';
}
echo '</table></form><a style="float:right; margin-right:10px;"  href="pastevents.php">Past Events</a></div>';
?>
</div>
<br style="clear:both;"/>
<div class="section">
    <div class="leftmain">
        <h2>Add Job</h2>
        <form id="volunteerform" name="volunteerform" method="post" action="addjob-exec.php">
            <table border="0" align="center" cellpadding="2" cellspacing="0">
                <tr>
                    <th>Job Name</th>
                    <td><input name="job_name" type="text" class="textfield" id="job_name" /></td>
                </tr>
                <tr>
                    <th>Job Description </th>
                    <td><textarea name="job_description" style="width:180px;" rows=6 id="job_description" ></textarea></td>
                </tr>
                <tr>
                    <th>Job Type</th>
                    <td><input name="job_type" type="text" class="textfield" id="job_type" /></td>
                </tr>
                <tr>
                    <th>&nbsp;</th>
                    <td><input type="submit" name="Submit" value="Add Job" /></td>
                </tr>
            </table>
        </form>
    </div>
    <div class="rightmain">
        <h2>Add Event</h2>
        <form id="eventform" name="eventform" method="post" action="addevent-exec.php">
            <table>
                <tr>
                    <th>Event Name</th>
                    <td><input name="event_name" type="text" class="textfield" id="event_name" /></td>
                </tr>
                <tr>
                    <th>Event Description </th>
                    <td><textarea name="event_description" style="width:180px;" rows=6 id="event_description" ></textarea></td>
                </tr>
                <tr>
                    <th>Event Type</th>
                    <td><select name="event_type" id="event_type">
                            <?php
                            $query2 = $db->prepare('
      SELECT name
      FROM member_types');
                            $query2->execute();
                            foreach ($query2 as $row) {
                                $member_type = $row['name'];

                                echo '<option value="' . $member_type . '">' . $member_type . '</option>';
                            }
                            ?> 
                        </select>
                    </td>
                </tr>
                <tr>
                    <th>Event is Potluck?</th>
                    <td><select name="event_ispotluck"><option value="1">Yes</option><option selected value="0">No</option></select></td>
                </tr>
                <tr>
                    <th>Event Date</th>
                    <td>
                        <script>
                            $(function() {
                                $.datepicker.setDefaults($.datepicker.regional['']);
                                $('.event_date').datetimepicker({
                                    dateFormat: 'yy-mm-dd',
                                    timeFormat: 'hh:mm:ss',
                                });
                            });
                        </script>
                        <script type="text/javascript">
                            $(document).ready(function() {

                                $("tr.extra1").css("display", "none");
                                $("tr.extra2").css("display", "none");

                                $("#showExtra1").click(function() {

                                    if ($("#showExtra1").is(":checked"))
                                    {
                                        $("tr.extra1").show("fast");
                                        if ($("input[name=weeklymonthly]:checked").val() === 'monthly') {
                                            $("tr.extra2").show("fast");
                                        }
                                    }
                                    else
                                    {
                                        $("tr.extra1").hide("fast");
                                        if ($("input[name=weeklymonthly]:checked").val() === 'monthly') {
                                            $("tr.extra2").hide("fast");
                                        }
                                    }
                                });
                                $("input[name=weeklymonthly]").change(function() {
                                    var test = $(this).val();
                                    $("tr.extra2").hide();
                                    $(".extra" + test).show();
                                });
                            });

                        </script>

                        <script>
                            function confirmDelete() {
                                if (confirm("Are you sure you want to delete?")) {
                                    return true;
                                }
                                else
                                    return false;
                            }
                        </script>

                        <input name="event_date" type="text" class="textfield event_date" /> <label>Recurring?<input type="checkbox" id="showExtra1" name="event_recurrs" value="1" /></label></td>
                </tr>
                <tr class="extra1"><th>Number of Additional Recurrences</th><td><select name="event_recurrences"><option value="1">1</option><option value="2">2</option><option value="3">3</option><option value="4">4</option><option value="5">5</option><option value="6">6</option><option value="7">7</option><option value="8">8</option><option value="9">9</option><option value="10">10</option><option value="11">11</option><option value="12">12</option><option value="13">13</option><option value="14">14</option><option value="15">15</option><option value="16">16</option><option value="17">17</option><option value="18">18</option><option value="19">19</option><option value="20">20</option></select></td></tr>
                <tr class="extra1"><th>Weekly/Monthly</th><td><label>Weekly:<input name="weeklymonthly" type="radio" value="weekly" checked /></label><label>Monthly:<input name="weeklymonthly" type="radio" value="monthly" id="showExtra2" /><label></td></tr>
                                <tr class="extra2 extramonthly"><th>Week</th><td><label>First:<input name="period[]" value="first" type="checkbox" /></label><label>Second:<input name="period[]" value="second" type="checkbox" /></label><label>Third:<input name="period[]" value="third" type="checkbox" /></label><label>Fourth:<input name="period[]" value="fourth" type="checkbox" /></label><label>Last:<input name="period[]" value="last" type="checkbox" /></label></td></tr>
                                <tr class="extra1"><th>Day of Week</th><td><select name="weekday"><option value="Sunday">Sunday</option><option value="Monday">Monday</option><option value="Tuesday">Tuesday</option><option value="Wednesday">Wednesday</option><option value="Thursday">Thursday</option><option value="Friday">Friday</option><option value="Saturday">Saturday</option></td></tr>
                                <tr>
                                    <th>&nbsp;</th>
                                    <td><input type="submit" name="Submit" value="Add Event" /></td>
                                </tr>
                                </table>
                                </form>
                                </div>
                                </div>
                                <?php include("template_footer.php"); ?>