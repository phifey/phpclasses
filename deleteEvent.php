<?php
session_start();
include 'connectionLocal.php';
include 'user.php';

if(isset($_SESSION['validUser']))
	{
?>
<html>
<head>
</head>
<body>
<div>
<?php
$userDelete = new User($conn);
if($userDelete->deleteUser())
	echo $userDelete->getSuccessMsg();
else
	echo $userDelete->getErrorMsg();
?>
<a href="index.php">Back to admin page</a>
<?php } else {
	echo "You are not logged in! You cannot access information on this page."; ?>
<a href="index.php">Login now</a>
<?php } ?>
</div>
</body>
</html>