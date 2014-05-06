<?php
include("config_mynonprofit.php");
include("connect.php");
session_start();
include("template_header.php");
?>
<div class="content">
    <form id="loginForm" name="loginForm" method="post" action="register-exec.php">
        <table width="300" border="0" align="center" cellpadding="2" cellspacing="0">
            <tr>
                <th>First Name </th>
                <td><input name="fname" type="text" class="textfield" id="fname" /></td>
            </tr>
            <tr>
                <th>Last Name </th>
                <td><input name="lname" type="text" class="textfield" id="lname" /></td>
            </tr>
            <tr>
                <th>Email Address</th>
                <td><input name="email" type="text" class="textfield" id="email" /></td>
            </tr>
            <tr>
                <th width="124">Username</th>
                <td width="168"><input name="username" type="text" class="textfield" id="username" /></td>
            </tr>
            <tr>
                <th>Volunteer Interests</th>
                <td>
                    <?php
                    $query2 = $db->prepare('
      SELECT volunteer_type_id, name
      FROM volunteer_types');
                    $query2->execute();
                    foreach ($query2 as $row2) {

                        $name = $row2['name'];
                        $volunteer_type_id = $row2['volunteer_type_id'];


                        echo ' <label>' . $name . ': <input type="checkbox" name="volunteertypes[]" value="' . $volunteer_type_id . '"';
                        echo '/></label><br />';
                    }
                    ?>
                </td>
            </tr>
            <tr>
                <th>Membership Type</th>

                <td><select name="member_type" id="member_type">';
                        <?php
                        $query3 = $db->prepare('
      SELECT name
      FROM member_types');

                        $query3->execute();
                        foreach ($query3 as $row3) {
                            $member_type = $row3['name'];
                            if ($member_type1 == $member_type) {
                                echo '<option value="' . $member_type . '" selected="selected">' . $member_type . '</option>';
                            } else {
                                echo '<option value="' . $member_type . '">' . $member_type . '</option>';
                            }
                        }
                        echo'</select><br />Membership requests other than General will require approval by an Admin. You will become a General member in the meantime.</td>';
                        ?>




            </tr>
            <tr>
                <th>Password</th>
                <td><input name="password" type="password" class="textfield" id="password" /></td>
            </tr>
            <tr>
                <th>Confirm Password </th>
                <td><input name="cpassword" type="password" class="textfield" id="cpassword" /></td>
            </tr>
            <tr>
                <th>&nbsp;</th>
                <td><input type="submit" name="Submit" value="Register" /></td>
            </tr>
        </table>
    </form>
</body>
</html>