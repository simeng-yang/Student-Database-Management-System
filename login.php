<?php
//	Note: Sessions allow variables to be preserved over multiple pages.
//	If session has not been started
if (session_status() == PHP_SESSION_NONE) {
//  Start the session
session_start();
}

//	If the session variable from login.php is set, the user has logged-in
//	Prevents direct-access to database information without logging-in (once per session)
if (isset($_SESSION['credentialsEntered'])) {
	//	User is authorized, so loads student database
	require_once('fetchStudentData.php');
}
//	Otherwise, user is not (yet) authorized and must log-in to view database
else {
?>

<!DOCTYPE HTML> 
<html>
<head>
	<!-- CSS style sheets -->
	<link rel="StyleSheet" href="./css/message-design.css" />
	<title>Sign-In</title> 
	<link rel="stylesheet" type="text/css" href="./css/login-design.css"> 
</head> 

<?php 
// Get a connection for the database
require_once('mysqli_connect.php');
// Choose the appropriate database
$db = mysqli_select_db($dbc,DB_NAME) or die("Failed to connect to MySQL: " . mysqli_connect_error()); 

//  Checks if user credentials are correct and acts accordingly		

//	Note that this function accesses credentials stored in a database, although
//	this can also be accomplished with use of internal variables
//	For more complex projects with larger scope, this retrieval system can be better
//  as a specific set of credentials can be securely stored for multiple user accounts in the database
function SignIn($dbc) 
{ 
	//	If neither credential is missing
	if(!empty($_POST['username']) AND !empty($_POST['pass'])) 
	{ 	
		//	Get all records from account table where the username and password match with user input
		//	Testing: Username is "root" & password is "password"; change as you please
		$query = "SELECT * FROM user_account WHERE userName = '$_POST[username]' AND pass = '$_POST[pass]'";
		$result = mysqli_query($dbc, $query);
		$row = mysqli_fetch_array($result); 

		//	If there is a match (non-empty)
		if(!empty($row['userName']) AND !empty($row['pass'])) 
		{ 
			//	Set session variable to be referenced in getstudentinfo.php
			$_SESSION['credentialsEntered'] = true;
			// Redirect to database
			?>
			<meta http-equiv="refresh" content="0; url = ./fetchStudentData.php">
			<?php
		} 
		//	Otherwise, output error for incorrect credentials
		else
		{
			?>
				<div class="isa_error">
				<i class="fa fa-warning"></i>
					<?php echo "INCORRECT SIGN-IN!"; ?>
				</div>
				<?php
		}
	} 
	//	Not all credentials have been entered
	else
	{
			//	Error is output for missing credentials
			?>
				<div class="isa_error">
				<i class="fa fa-warning"></i>
					<?php echo "MISSING CREDENTIALS!"; ?>
				</div>
				<?php
	}
} 

//	If button is pressed, run function
if(isset($_POST['submit'])) 
{ 
	SignIn($dbc); 
}
?>

<!-- Setting up the visual sign-in box -->
<body> 
	<div id="Sign-In"> 
	<fieldset style="width:30%">
	<legend><b>LOG-IN HERE</b></legend> 
	<form method="POST" action=""> 
	Username <br><input type="text" name="username" size="40"><br> 
	Password <br><input type="password" name="pass" 
	size="40"><br> <input id="button" 
	type="submit" name="submit" value="Log-In"> 
</form> 
</fieldset> 
</div> 
</body> 
</html> 

<?php } ?>
