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
if ($edit == 'Edit') {
    $query1 =  $db->prepare('UPDATE volunteer_types
SET name = :edit_name, description=:edit_description
WHERE volunteer_type_id=:edit_volunteer_type_id');
    $query1->execute(array('edit_name' => $edit_name, 'edit_description' => $edit_description, 'edit_volunteer_type_id' => $edit_volunteer_type_id));
    
} else if ($delete == 'Delete') {
    $query2 =  $db->prepare('DELETE FROM volunteer_types WHERE volunteer_type_id = :edit_volunteer_type_id');
    $query2->execute(array('edit_volunteer_type_id' => $edit_volunteer_type_id));
} else if ($add == 'Add') {
    $add_name = htmlentities($_POST['add_name']);
    $add_description = htmlentities($_POST['add_description']);
    $query3 =  $db->prepare('INSERT INTO volunteer_types(name, description) VALUES(:add_name, :add_description)');
    $query3->execute(array('add_name' => $add_name, 'add_description' => $add_description));
    
}



echo'
<div class="content">
<form id="editvolunteersform" name="editvolunteersform" method="post" action="' . $_SERVER['PHP_SELF'] . '">
	<h2>Volunteer Types</h2>
        <table>
<tr><th>Name</th><th>Description</th><th>Status</th></tr>
';


$query = $db->prepare('
      SELECT *
      FROM volunteer_types
');
$query->execute();
foreach ($query as $row) {
    $name = $row['name'];
    $description = $row['description'];
    $volunteer_type_id = $row['volunteer_type_id'];
    echo'<tr><td><input type="text" name="edit_name" value="' . $name . '" /></td><td><textarea name="edit_description" cols="20" rows="4">' . $description . '</textarea></td><td><input type="hidden" value="' . $volunteer_type_id . '" name="edit_volunteer_type_id"/><button type="submit" value="Edit" name="edit">Edit</button><button type="submit" value="Delete" name="delete">Delete</button></td></tr>';
}
echo '</table></form><br/>';
?>
<h2>Add Volunteer Type</h2>
<form id="addvolunteertype" name="addvolunteertype" method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
    <table border="0" align="center" cellpadding="2" cellspacing="0">
        <tr>
            <th>Volunteer Type Name</th>
            <td><input name="add_name" type="text" class="textfield" id="name" /></td>
        </tr>
        <tr>
            <th>Volunteer Type Description </th>
            <td><textarea name="add_description" cols="40" rows="6" id="description" ></textarea></td>
        </tr>
        <tr>
            <th>&nbsp;</th>
            <td><input type="submit" name="add" value="Add" /></td>
        </tr>
    </table>
</form>
</div>
<?php include("template_footer.php"); ?>