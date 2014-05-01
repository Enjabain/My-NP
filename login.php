<?php 
include("config_mynonprofit.php");
include("connect.php");
include("template_header.php") 
?>
<div class="content">
<form id="loginForm" name="loginForm" method="post" action="login-exec.php">
  <table>
    <tr>
      <th style="width:112px;">Username</th>
      <td style="width:188px;"><input name="username" type="text" class="textfield" id="username" /></td>
    </tr>
    <tr>
      <th>Password</th>
      <td><input name="password" type="password" class="textfield" id="password" /></td>
    </tr>
    <tr>
      <th><a href="registration.php">Register Here</a></th>
      <td><input type="submit" name="Submit" value="Login" /><span style="float:right;"><a style="font-size:8px;" href="resetpassword.php">forgot password?</a></span></td>
    </tr>
  </table>
</form>
</div>
<?php include("template_footer.php");?>