<?php
include("config_mynonprofit.php");
include("connect.php");
	//Start session
	session_start();
	
	//Unset the variables stored in session
	unset($_SESSION['SESS_MEMBER_ID']);
	unset($_SESSION['SESS_FIRST_NAME']);
	unset($_SESSION['SESS_LAST_NAME']);
	unset($_SESSION['SESS_AUTH_TYPE']);
	unset($_SESSION['SESS_USERNAME']);
?>
<?php include("template_header.php") ?>
<p align="center">&nbsp;</p>
<h4 align="center" class="err">You have been logged out.</h4>
<p align="center">Click here to <a href="login.php">Login</a></p>
</body>
</html>
