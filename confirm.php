<?php
include("config_mynonprofit.php");
include("connect.php");
include("template_header.php");
?>
<?php
$confirm = htmlentities($_GET['confirm']);
if ($confirm != '') {
    $query = $db->prepare('UPDATE members
		SET auth_type="1"
		WHERE confirm_hash=:confirm LIMIT 1');

    $result = $query->execute(array('confirm' => $confirm));

    if ($result) {
        echo '<h3>Confirmation Successful</h3>You may now sign up for volunteer opportunities and events <a href="index.php">here</a>.';
        //    $myFilePath = "";
        //    $myFileName = "sql.log";
        //    $myPointer = fopen($myFilePath . $myFileName, "a+");
        //    if ($myPointer) {
        //        $sql .= "\n";
        //        fputs($myPointer, $sql);
        //        fclose($myPointer);
        //    }
    }
} else {
    echo '<p>You should have received a confirmation email. You must click the link contained in that email to confirm your email address.</p>';
}
?>
</div>
</body>
</html>