<!--
Author: Simeng Yang
Project: ICS4U Final Project - Database-Driven Website with PHP & MySQL
Item: Student Information Database
Desc: Gets student information from MySQL database and displays fields in the browser.

Optimized for viewing on Google Chrome. 
-->

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
?>

<html>
<head>
<!-- Google fonts -->
<link href='https://fonts.googleapis.com/css?family=Nothing+You+Could+Do' rel='stylesheet' type='text/css'>
<!-- CSS style sheets -->
<link rel="StyleSheet" href="./css/registration-design.css" />
<link rel="StyleSheet" href="./css/message-design.css" />
<title>Fetch Student Data</title>
</head>
<body>

<!-- Redirect button for adding a student to database -->
<table align="center" cellspacing = "8">
<tr><td align>
<form action="./addNewStudent.php" method="post">
<input type="submit" name="go"  class="redirectButton" value="Add Student to Database" /></td></tr>
</form>
</table>

<?php
// Get a connection for the database
require_once('mysqli_connect.php');

//	Deletes the specified record from the database when button is clicked
//	Hidden field provides record-specific student ID (primary unique key) for deletion
if (isset($_POST['student_id'])) {
         $student_id = $_POST['student_id'];
         if (isset($_POST['delete'])) {
             $sql = "DELETE FROM students WHERE student_id = " . $student_id;
			 //	Note that the $dbc variable is from the required .php file included
			 if (mysqli_query($dbc, $sql))
			{?>
				<!-- Outputs success message for deletion -->
				<div class="isa_success">
				<i class="fa fa-check"></i>
					<?php echo "Student Deleted"; ?>
				</div><?php
			 }
			 else
				// Outputs error message for failure of deletion
			{?>
				<div class="isa_error">
				<i class="fa fa-warning"></i>
					<?php echo "Error Occurred ".mysqli_error($link); ?>
				</div><?php
			 }
         }
    }
?>

<div id = "title">
<h1>Student Information Database:</h1>
<img src="./pictures/header.png" alt="Banner" style="width:600px;height:100px;"/>
</div>

<?php
//	Create a query for the database
//	Student records are sorted alphabetically by last and first names for ease of reference
$query = "SELECT first_name, last_name, gender, email, street_name, street_type, city, postal_code,
phone, course, dob_day, dob_month, dob_year, student_id FROM students ORDER BY last_name, first_name";

// Get a response from the database by sending the connection and the query
$response = @mysqli_query($dbc, $query);

// If the query executed properly, proceed
if($response){
//	Output column names
echo '<table class = "studentinfo" align="center" cellpadding="8">

<tr><td align="left"><font face = "Architects Daughter" size="3"><b>Last Name</b></font></td>
<td align="left"><font face = "Architects Daughter" size="3"><b>First Name</b></font></td>
<td align="left"><font face = "Architects Daughter" size="3"><b>Gender</b></font></td>
<td align="left"><font face = "Architects Daughter" size="3"><b>Email</b></font></td>
<td align="left"><font face = "Architects Daughter" size="3"><b>Street</b></font></td>
<td align="left"><font face = "Architects Daughter" size="3"><b>City</b></font></td>
<td align="left"><font face = "Architects Daughter" size="3"><b>Postal Code</b></font></td>
<td align="left"><font face = "Architects Daughter" size="3"><b>Phone</b></font></td>
<td align="left"><font face = "Architects Daughter" size="3"><b>Birth Date</b></font></td>
<td align="left"><font face = "Architects Daughter" size="3"><b>Course Code</b></font></td></tr>';

// Returns a row of data from the query until no further data is available
while($row = mysqli_fetch_array($response)){
echo '<tr><td align="left">' . 
$row['last_name'] . '</td><td align="left">' . 
$row['first_name'] . '</td><td align="left">' .
$row['gender'] . '</td><td align="left">' .
$row['email'] . '</td><td align="left">' . 
$row['street_name'] ." ".$row['street_type'].'</td><td align="left">' .
$row['city'] . '</td><td align="left">' . 
$row['postal_code'] . '</td><td align="left">' . 
$row['phone'] . '</td><td align="left">' .
$row['dob_month'] ." ".$row['dob_day'] .", ".$row['dob_year'] .'</td><td align="left">'.
$row['course'] . '</td><td align="left">';
?>

<!-- Events triggered in form stay on page -->
<form method="post" action="">
<!-- Hidden button stores record-specific student ID -->
<td><input type="hidden" id = "student_id" name="student_id" value="<?php echo $row['student_id']; ?>" /></td>
<!-- Delete button for triggering record deletion -->
<td><input type="submit" name="delete" class="deleteButton" value="Delete" onclick="return confirm('Are you sure?')" /></td>
</form>

<?php
echo '</tr>';
}		
echo '</table>';
} 
//	Query did not execute properly
else {
echo "Couldn't issue database query<br />";
echo mysqli_error($dbc);
}

// Close connection to the database
mysqli_close($dbc);
}
//	Session variable was not set
else
{
	//	User has to log-in to view database information
	require_once('login.php');
}
?>