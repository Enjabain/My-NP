<?php
include("config_mynonprofit.php");
include("connect.php");
include("functions.php");
auth();
include("template_header.php");
?>

<h2>Contact us</h2>

Email <a href="<?php echo $contact_email; ?>"><?php echo $contact_email; ?></a> to make volunteer or event arrangements.

<?php/*
$query = $db->prepare('SELECT email FROM members WHERE auth_type="6"');

$query->execute();
foreach ($query as $row) {
$tos[] = $row['email'];
}
$to = implode(", ", $tos);

$subject = '' . $site_name . ' Contact Us';
$content = '<p>The ' . $event_name . ' event to happen at ' . $event_date_display . ' has been canceled.</p>';
$content .= '<p>Please login <a href="' . $site_url . '">here</a> for updates.</p>';


$headers = "MIME-Version: 1.0\r\n";
$headers .= "Content-type: text/html; charset=iso-8859-1\r\n";
$headers .= "From: mygp@growingplaces.cc\r\n";
$mailed = mail($to, $subject, $content, $headers);
}*/
?>




<?php include("template_footer.php"); ?>


