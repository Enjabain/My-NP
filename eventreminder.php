<?php

include("config_mynonprofit.php");
include("functions.php");
include("connect.php");
include("template_header.php");

$tomorrow = date("Y-m-d", strtotime("+1 day"));
$nextday = date("Y-m-d", strtotime("+2 day"));
$query = $db->prepare('SELECT event_id, event_name, event_description, event_date, event_ispotluck
      FROM events LEFT JOIN event_details ON events.event_details_id = event_details.event_details_id
      WHERE event_date between :tomorrow AND :nextday AND event_status = 0
      ORDER BY event_date');

$query->execute(array('tomorrow' => $tomorrow, 'nextday' => $nextday));
if ($query->rowCount() != 0) {
    foreach ($query as $row) {
        $event_id = $row['event_id'];
        $event_name = $row['event_name'];
        $event_description = $row['event_description'];
        $event_date = $row['event_date'];
        $event_ispotluck = $row['event_ispotluck'];

        $dateTime = new DateTime($event_date);
        $event_date = date_format($dateTime, "g:i A l F jS, Y");

        $subject = '' . $site_name . ' ' . $event_name . ' - Event Tomorrow.';
        $query2 = $db->prepare('
SELECT event_id, events_by_member.member_id, number_adults, number_children, potluck_item, email
FROM events_by_member LEFT JOIN members ON events_by_member.member_id = members.member_id
WHERE event_id = :event_id'); // AND events_by_member.member_id = "1"';
        $query2->execute(array('event_id' => $event_id));
        if ($query2->rowCount() != 0) {
            foreach ($query2 as $row) {

                $event_id = $row2['event_id'];
                $member_id = $row2['member_id'];
                $number_adults = $row2['number_adults'];
                $number_children = $row2['number_children'];
                $potluck_item = $row2['potluck_item'];
                $email = $row2['email'];



                $content = '<p>You are signed up to attend ' . $event_name . ' at Growing Places.</p><p>The event is tomorrow at <b>' . $event_date . '</b>.</p><p>Bringing: ' . $number_adults . ' Adult(s) and ' . $number_children . ' Kid(s).</p>';
                if ($event_ispotluck) {
                    $content .= '<p>Potluck Item: ' . $potluck_item . '</p>';
                }
                $content .= '<p>If any of this has changed please modify your RSVP <a href="http://www.growingplaces.cc/mygp/">here</a>.</p>';


                echo $email;
                echo $subject;
                echo $content;

                $to = $email;



                $headers = "MIME-Version: 1.0\r\n";
                $headers .= "Content-type: text/html; charset=iso-8859-1\r\n";
                $headers .= "From: mygp@growingplaces.cc\r\n";

                $mailed = mail($to, $subject, $content, $headers);
            }
        }
    }
}

//This is where events get completed.
$today = date("Y-m-d");
$query3 = $db->prepare('UPDATE events SET event_status = "2" WHERE event_date < :today AND event_status = "0"');
$query3->execute(array('today' => $today));
echo '<p>';
echo $query3->rowCount();
echo ' Event(s) set to status Completed.</p>';
?>