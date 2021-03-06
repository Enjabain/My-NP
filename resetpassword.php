<?php 
include("config_mynonprofit.php");
session_start();
include("connect.php");
include("template_header.php");

define('IN_SCRIPT', true);
// Start a session

function error($msg) {
    ?>
    <html>
    <head>
    <script language="JavaScript">
    <!--
        alert("<?=$msg?>");
        history.back();
    //-->
    </script>
    </head>
    <body>
    </body>
    </html>
    <?php
    exit;
}

function check_email_address($email) {
  // First, we check that there's one @ symbol, and that the lengths are right
  if (!ereg("^[^@]{1,64}@[^@]{1,255}$", $email)) {
    // Email invalid because wrong number of characters in one section, or wrong number of @ symbols.
    return false;
  }
  // Split it into sections to make life easier
  $email_array = explode("@", $email);
  $local_array = explode(".", $email_array[0]);
  for ($i = 0; $i < sizeof($local_array); $i++) {
     if (!ereg("^(([A-Za-z0-9!#$%&'*+/=?^_`{|}~-][A-Za-z0-9!#$%&'*+/=?^_`{|}~\.-]{0,63})|(\"[^(\\|\")]{0,62}\"))$", $local_array[$i])) {
      return false;
    }
  }  
  if (!ereg("^\[?[0-9\.]+\]?$", $email_array[1])) { // Check if domain is IP. If not, it should be valid domain name
    $domain_array = explode(".", $email_array[1]);
    if (sizeof($domain_array) < 2) {
        return false; // Not enough parts to domain
    }
    for ($i = 0; $i < sizeof($domain_array); $i++) {
      if (!ereg("^(([A-Za-z0-9][A-Za-z0-9-]{0,61}[A-Za-z0-9])|([A-Za-z0-9]+))$", $domain_array[$i])) {
        return false;
      }
    }
  }
  return true;
}


if (isset($_POST['submit'])) {
	
	if ($_POST['forgotpassword']=='') {
		error('Please Fill in Email.');
	}
	if(get_magic_quotes_gpc()) {
		$forgotpassword = htmlspecialchars(stripslashes($_POST['forgotpassword']));
	} 
	else {
		$forgotpassword = htmlspecialchars($_POST['forgotpassword']);
	}
	//Make sure it's a valid email address, last thing we want is some sort of exploit!
	if (!check_email_address($_POST['forgotpassword'])) {
  		error('Email Not Valid - Must be in format of name@example.com');
	}
    // Lets see if the email exists
    $query = $db->prepare('SELECT * FROM members WHERE email = :forgotpassword');
    $query->execute(array('forgotpassword' => $forgotpassword));
    if ($query->rowCount() == 0) {
        error('Email Not Found!');
    }

	//Generate a RANDOM MD5 Hash for a password
	$random_password=md5(uniqid(rand()));
	
	//Take the first 8 digits and use them as the password we intend to email the user
	$emailpassword=substr($random_password, 0, 8);
	
	//Encrypt $emailpassword in MD5 format for the database
	$newpassword = md5($emailpassword);
	
        // Make a safe query
       	$query2 = $db->prepare('UPDATE members SET password = :newpassword 
						  WHERE email = :forgotpassword');
        $query2->execute(array('newpassword' => $newpassword ,'forgotpassword' => $forgotpassword));
//Email out the infromation
$subject = "".$site_name." Password Reset"; 
$message = "Your new password is : $emailpassword"; 
                       
          if(!mail($forgotpassword, $subject, $message,  "FROM: $site_name <$site_email>")){ 
             die ("Sending Email Failed, Please Contact Site Admin. ($site_email)"); 
          }else{ 
                error('New Password Sent.');
         } 
		
	}
	
else {
?>
<h2>Reset Password</h2>
<div class="content">
      <form name="forgotpasswordform" action="" method="post">
        <table>
          <tr>
            <th>Email Address:</th>
            <td><input name="forgotpassword" type="text" value="" id="forgotpassword" /></td>
          </tr>
          <tr>
            <th></th><td  class="footer"><input type="submit" name="submit" value="Submit" class="mainoption" /></td>
          </tr>
        </table>
      </form>
</div>
      <?php
}
?>