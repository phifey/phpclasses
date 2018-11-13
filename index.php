<?php 
session_start();
include 'connectionLocal.php';
include 'user.php';
$error = "";

    if($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['presenter_submit']))
		$_SESSION['presenter_name'] = $_POST['set_presenter'];
	else
		$_SESSION['presenter_name'] = "";
	
    if($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['my_submit']))
    {
        $event_user = $_POST['event_user'];
        $event_password = $_POST['event_password'];
		
		$userLogin = new User($conn, $event_user, $event_password);
		if($userLogin->login()) {
			$_SESSION['loginSuccess'] = true;
			header("Location: index.php?loginSuccess=true");
			exit();
        }
        else {
			if($userLogin->_error)
			{
				$error = $userLogin->getErrorMsg();
				$_SESSION['errorMsg'] = $error;
				$_SESSION['loginSuccess'] = false;
				header("Location: index.php?loginSuccess=false");
				exit();
			}
        }
	}
	
	if($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['logout']))
	{
		if(isset($_SESSION['userID']) && isset($_SESSION['userPass']))
		{
			$userLogout = new User($conn, $_SESSION['userID'], $_SESSION['userPass']);
			$userLogout->logoutUser();
		}
	}
?>
<!doctype html>
<head>
	<title>Login</title>
	<link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.5.0/css/all.css" integrity="sha384-B4dIYHKNBt8Bc12p+WXckhzcICo0wtJAoU8YZTY5qE0Id1GSseTk6S+L3BlXeVIU" crossorigin="anonymous">
	<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/js/bootstrap.min.js" integrity="sha384-ChfqqxuZUCnJSK3+MXmPNIyE6ZbWh2IMqE241rYiqJxyMiZ6OW/JmZQ5stwEULTy" crossorigin="anonymous"></script>
	<link href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-MCw98/SFnGE8fJT3GXwEOngsV7Zt27NXFoaoApmYm81iuXoPkFOJwJ8ERdknLPMO" crossorigin="anonymous">
	<style>
	body {
		overflow: hidden;
	}
	
	aside, main {
		width: 100%;
		height: 1000px;
	}
	
	.flex-container {
		display: flex;
		justify-content: center;
		align-items: center;
	}
	
	table, #set_presenter {
		width: 100%;
		height: auto;
	}
	
	.fontaws {
		color: #fff;
		opacity: 0.8;
		text-shadow: 1px 1px 1px;
	}
	
	i:hover {
		color: black;
		cursor: pointer;
	}
	</style>
</head>
<body>
<?php if(isset($_SESSION['loginSuccess']) && $_SESSION['loginSuccess'] == 1 && isset($_SESSION['validUser']) && $_SESSION['validUser'] == 1) { ?>
<nav id="main-controls" class="navbar navbar-light justify-content-between" style="background-color:#030303">
  <a style="color:white;" class="navbar-brand"><span class="text-uppercase">Admin:</span> <?php echo $_SESSION['userID'] ?></a>
	<form method="post"><button method="post" name="logout" class="btn btn-danger">Logout</button></form>
</nav>

<div id="container" class="container-fluid">
	<div class="row">
		<aside style="background-color: #121212;" id="aside-controls" class="col-2">
			<div class="util flex-container m-5 p-2">
				<i class="fontaws fas fa-6x fa-database"></i>
			</div>
			<div class="util flex-container m-5 p-2">
				<i class="fontaws fas fa-6x fa-table"></i>
			</div>
			<div class="util flex-container m-5 p-2">
				<i class="fontaws fas fa-6x fa-calendar-alt"></i>
			</div>
			<div class="util flex-container m-5 p-2">
				<i class="fontaws fas fa-6x fa-info"></i>
			</div>
			<div class="util flex-container m-5 p-2">
				<i class="fontaws fa-6x fas fa-cog"></i>
			</div>
		</aside>
		<main style="background-color: #292929;" id="main-display" class="col-10">
			<h1 class="jumbotron p-2 m-2 text-center text-uppercase font-weight-light">
				control user events table
			</h1>
		<div class="flex-container">
			<div class="col-12">
				<form method="post">
					<input class="mb-2" type="submit" name="presenter_submit" value="Set presenter name">
					<input class="mb-2" type="text" id="set_presenter" name="set_presenter">
				</form>
				<table>
<?php 
$displayTable = new User($conn);
if($displayTable->displayTable()){
}
else if($displayTable->_error) {
	$error = $displayTable->getErrorMsg();
	echo $error;
}
?>
				</table>
			</div>
		</div>
		</main>
	</div>
</div>
<?php } else if(empty($_SESSION['loginSuccess']) || $_SESSION['loginSuccess'] == "") { ?>
<form name="event_form" method="post" action="index.php">
    <p>
		<?php if(isset($_SESSION['errorMsg']) && !empty($_SESSION['errorMsg'])) echo $_SESSION['errorMsg']; ?>
		<?php if(isset($_GET['LogoutSuccess']) && !empty($_GET['LogoutSuccess']) && $_GET['LogoutSuccess'] == "true") echo "You've been logged out, thank you."; ?>
    </p>
    <p>
        <label for="event_user">User:</label>
        <input type="text" name="event_user" id="event_user" value="">
    </p>
    <p>
        <label for="event_password">Password:</label>
        <input type="text" name="event_password" id="event_password" value="">
    </p>
    <input type="submit" name="my_submit" id="my_submit" value="Submit">
</form>
<?php } ?>
</body>
</html>