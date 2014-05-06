<!DOCTYPE html>
<html manifest="mynp.appcache">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <meta name="viewport" content="width=device-width; initial-scale=1.0">
        <title><?php echo $site_name; ?></title>
        <link href="style.css" rel="stylesheet" type="text/css" />
        <script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.6.2/jquery.min.js"></script>
        <script>
            (function() {
                function install(ev) {
                    ev.preventDefault();
                    // define the manifest URL
                    var manifest_url = "<?php echo $site_url; ?>manifest.webapp";
                    // install the app
                    var myapp = navigator.mozApps.install(manifest_url);
                    myapp.onsuccess = function(data) {
                        // App is installed, remove button
                        this.parentNode.removeChild(this);
                    };
                    myapp.onerror = function() {
                        // App wasn't installed, info is in this.error.name
                        console.log('Install failed, error: ' + this.error.name);
                    };
                }
                ;
                // get a reference to the button and call install() on click
                var button = document.getElementById('install');
                button.addEventListener('click', install, false);
            })();
        </script>
    </head>
    <body>
        <div class="main">
            <a id="backtomain" style="position:absolute; top:0px; left:10px;" href="<?php echo $main_url; ?>">&larr; Back to Main Site</a>
            <div class="sitename"><a class="siteurl" href="<?php echo $site_url; ?>"><h1><?php echo $site_name; ?></h1></a>

                <?php
                if ($_SESSION['SESS_MEMBER_ID'] != "") {
                    echo '<a href="member.php?member_id=' . $_SESSION['SESS_MEMBER_ID'] . '">Profile</a> ';
                    echo'<a href="logout.php">Logout</a>';
                } else {
                    echo '<a href="login.php">Sign in</a> or <a  href="registration.php">Sign up</a>';
                }
                if ($_SESSION['SESS_AUTH_TYPE'] == 6) {
                    echo'<br /><a href="admin/index.php">Admin</a> <a href="admin/members.php">Members</a> <a href="admin/volunteertypes.php">Volunteer Types</a> ';
                }
                ?>
            </div>

            <?php
            if ($donations == "1") {
                $query = $db->prepare('SELECT donations_goal, donations_total
				  FROM donations');
                $query->execute();
                foreach ($query as $row) {
                    $donations_goal = $row['donations_goal'];
                    $donations_total = $row['donations_total'];
                }
                $donations_percent = floor(($donations_total / $donations_goal) * 100);
                echo '<div id="donationsmain"><span style="float:left;">Donations: </span><span style="float:right;">' . $donations_percent . '%</span><br /><div style="width:110px; height:10px; background-color:#EFFFA7; border:solid 1px #ffffff;"><div style="width:' . $donations_percent . '%; height:8px; background-color:#bce774;"></div></div>';
                echo '<a href="paypal.php">Donate</a> ';
                if ($_SESSION['SESS_AUTH_TYPE'] >= 2) {
                    echo '<a href="paypal.php">Pay Tuition</a>';
                }
                echo '</div>';
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