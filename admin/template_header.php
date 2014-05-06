<!DOCTYPE html>
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
        <meta name="viewport" content="width=device-width; initial-scale=1.0">
        <title><?php echo $site_name; ?></title>
        <link href="../style.css" rel="stylesheet" type="text/css" />
        <link rel="stylesheet" href="../css/ui-lightness/jquery-ui-1.8.16.custom.css" type="text/css" media="all" />
        <script src="http://ajax.googleapis.com/ajax/libs/jquery/1.6.2/jquery.min.js" type="text/javascript"></script>
        <script src="http://ajax.googleapis.com/ajax/libs/jqueryui/1.8.16/jquery-ui.min.js" type="text/javascript"></script>
        <script src="../js/jquery-ui-timepicker-addon.js" type="text/javascript"></script>
    </head>
    <body>
        <div style="position:relative; background-color:#EFFFA7; max-width:1250px; margin:0 auto; padding:20px 10px 10px 10px;">
            <a id="backtomain" style="position:absolute; top:0px; left:10px;" href="<?php echo $main_url; ?>">&larr; Back to Main Site</a>
            <div class="sitename"><a class="siteurl" href="<?php echo $site_url; ?>"><h1 style="z-index:1;"><?php echo $site_name; ?></h1></a>
                <?php
             //   echo '<a href="../index.php">Home</a> ';
                if ($_SESSION['SESS_MEMBER_ID'] != "") {
                    echo '<a href="../member.php?member_id=' . $_SESSION['SESS_MEMBER_ID'] . '">Profile</a> ';
                    echo'<a href="../logout.php">Logout</a>';
                } else {
                    echo '<a href="../login.php">Sign in</a> or <a href="../registration.php">Sign up</a>';
                }
                if ($_SESSION['SESS_AUTH_TYPE'] == 6) {
                    echo'<br /><a href="index.php">Admin</a> <a href="members.php">Members</a> <a href="volunteertypes.php">Volunteer Types</a>
';
                }
                ?>
            </div>

            <?php
            if ($donations == "1") {
                $query = $db->prepare('SELECT donations_goal, donations_total
				  FROM donations');
                $query->execute();
                if ($query->rowCount() != 0) {
                    foreach ($query as $row) {

                        $donations_goal = $row['donations_goal'];
                        $donations_total = $row['donations_total'];
                    }
                    $donations_percent = floor(($donations_total / $donations_goal) * 100);
                    echo '<div id="donations"><span style="float:left;">Donations: </span><span style="float:right;">' . $donations_percent . '%</span><br /><div style="width:110px; height:10px; background-color:#EFFFA7; border:solid 1px #ffffff;"><div style="width:' . $donations_percent . '%; height:8px; background-color:#bce774;"></div></div>';
                    echo'<form action="donations-exec.php" method="post">Total: <input type="text" value="' . $donations_total . '" name="donations_total" size="1" style="width:30px;">
					   Goal: <input type="text" value="' . $donations_goal . '" name="donations_goal" size="1" style="width:30px;">
					   <button name="submit" value="submit" type="submit">Update</button>
				</form>';
                    echo '</div>';
                }
            }
            ?>
            <br style="clear:both;" />
            <?php
            if (isset($_SESSION['ERRMSG_ARR']) && is_array($_SESSION['ERRMSG_ARR']) && count($_SESSION['ERRMSG_ARR']) > 0) {
                echo '<ul class="err">';
                foreach ($_SESSION['ERRMSG_ARR'] as $msg) {
                    echo '<li>', $msg, '</li>';
                }
                echo '</ul>';
                unset($_SESSION['ERRMSG_ARR']);
            }?>