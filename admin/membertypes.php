<?php
include("../config_mynonprofit.php");
include("../functions.php");
authAdmin();
include("../connect.php");
include("template_header.php");
$edit = htmlentities($_POST['edit']);
$delete = htmlentities($_POST['delete']);
$add = htmlentities($_POST['add']);
$edit_volunteer_type_id = htmlentities($_POST['edit_volunteer_type_id']);
$edit_name = htmlentities($_POST['edit_name']);
$edit_description = htmlentities($_POST['edit_description']);
if ($edit == 'edit') {
    $query1 = "UPDATE volunteer_types
SET name = '$edit_name', description='$edit_description'
WHERE volunteer_type_id='$edit_volunteer_type_id'";
    $result1 = mysql_query("$query1") or die("Error: " . mysql_error());
} else if ($delete == 'delete') {
    $query2 = 'DELETE FROM volunteer_types WHERE volunteer_type_id = ' . $edit_volunteer_type_id . '';
    $result2 = mysql_query("$query2") or die("Error: " . mysql_error());
} else if ($add == 'Add') {
    $add_name = htmlentities($_POST['add_name']);
    $add_description = htmlentities($_POST['add_description']);
    $query3 = "INSERT INTO volunteer_types(name, description) VALUES('$add_name', '$add_description')";
    $result3 = mysql_query("$query3") or die("Error: " . mysql_error());
}



echo'
<div style="padding:10px; width:600px; background-color:#75A1D0;">
<form id="editvolunteersform" name="editvolunteersform" method="post" action="' . $_SERVER['PHP_SELF'] . '">
	<h2>Volunteer Types</h2>
        <table style="width:600px;">
<tr><th>Name</th><th>Description</th><th>Status</th></tr>
';


$query = '
      SELECT *
      FROM volunteer_types
';

$result = mysql_query("$query") or die("Error: " . mysql_error());
while ($row = mysql_fetch_array($result)) {
    $name = $row['name'];
    $description = $row['description'];
    $volunteer_type_id = $row['volunteer_type_id'];
    echo'<tr><td><input type="text" name="edit_name" value="' . $name . '" /></td><td><textarea name="edit_description" cols="20" rows="4">' . $description . '</textarea></td><td><input type="hidden" value="' . $volunteer_type_id . '" name="edit_volunteer_type_id"/><button type="submit" value="edit" name="edit">Edit</button><button type="submit" value="delete" name="delete">Delete</button></td></tr>';
}
echo '</table></form></div><br/>';
?>
<div style="width:500px;">
    <h2>Add Member Type</h2>
    <form id="addvolunteertype" name="addvolunteertype" method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
        <table width="500" border="0" align="center" cellpadding="2" cellspacing="0">
            <tr>
                <th>Volunteer Type Name</th>
                <td><input name="add_name" type="text" class="textfield" id="name" /></td>
            </tr>
            <tr>
                <th>&nbsp;</th>
                <td><input type="submit" name="add" value="Add" /></td>
            </tr>
        </table>
    </form>
</div>

<?php include("template_footer.php"); ?>