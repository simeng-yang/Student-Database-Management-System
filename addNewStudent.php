<!--
Author: Simeng Yang
Project: ICS4U Final Project - Database-Driven Website with PHP & MySQL
Item: Entry-page for adding a student
Desc: Loads interface for user to allocate a student, with necessary information. 
Has input filtration and sanitation. Processes user input and sends input to database.

Optimized for viewing on Google Chrome.
-->

<html>
<head>
<!-- Google fonts  -->
<link href='https://fonts.googleapis.com/css?family=Nothing+You+Could+Do' rel='stylesheet' type='text/css'>
<link href='https://fonts.googleapis.com/css?family=Shadows+Into+Light' rel='stylesheet' type='text/css'>
<!-- CSS style sheets  -->
<link rel="StyleSheet" href="./css/registration-design.css" />
<link rel="StyleSheet" href="./css/message-design.css" />
<title>Add New Student</title>
</head>
<body>

<!-- Redirects to student database  -->
<table align="center" cellspacing = "8">
<tr><td align>
<form action="./login.php" method="post">
<input type="submit" name="go" class="redirectButton" value="View Students Database" /></td></tr>
</form>
</table>

<!-- Events triggered in form stay on page -->
<form action="" method="post">

<div id="studentWrapper">
<div id = "title">
<h1>Add a New Student:</h1>
<img src="./pictures/header.png" alt="Banner" style="width:600px;height:100px;"/>
</div>
</head>

<?php
//	Takes a string and "renormalizes" it to conform to input rules
//	For instance, (student) names should not contain numbers and should begin with an upper-case letter
//	Second parameter is a bool that decides whether to screen for only letters or to also include numeric characters
function TextNormalizer($str, $numbers){
	
	if ($numbers == false){
	//	Gets rid of all non-letter characters, including digits,
	//  except for apostrophes (') and hyphens (-)
		$str = preg_replace("/[^-'a-zA-Z]/", "", $str);
	}
	else
	{
	//	Gets rid of all non-alphanumeric characters,
	//  except for apostrophes (') and hyphens (-)
		$str = preg_replace("/[^-'0-9a-zA-Z]/", "", $str);	
	}
	
	//	Capitalizes first letter of each substring separated by hyphen(s) and apostrophe(s)
	
	//	Decompose string into substrings separated by hyphen
	$pieces = explode("-", $str);
	//	var_dump($pieces);
	//	Empty string for appending
	$str = "";
	
	foreach ($pieces as $piece){
		//	 If the substring contains a letter or digit 
		//	(i.e. not empty for an "extra" consecutive hyphen or dangling apostrophe)
		//	 Note that $piece != "" would be insufficient in this case
		//	 Consider a string such as -'''-, with no letters; the apostrophes will be kept, alone
		//	 Below, when the apostrophes get "chopped off", the hyphen appended here will be "extra"
		if (preg_match('/[0-9a-z]/i', $piece)){
		//	Capitalizes only first letter of piece
		$first_part = strtoupper(substr($piece, 0, 1));
		//	Sets every other letter of the piece to lower-case
		$last_part = strtolower(substr($piece, 1));
		//	Concatenation of parts
		$piece = $first_part.$last_part;
		
		// Also need to do this process for apostrophes
		$fragments = explode("'", $piece);
		//	var_dump($fragments);
		
		//	Empty substring for appending
		$piece = "";

		foreach ($fragments as $fragment){
			//	If the substring contains a letter or digit 
			//	(i.e. not empty for an "extra" consecutive apostrophe)
			if (preg_match('/[0-9a-z]/i', $fragment)){ 	// Alternatively, if $fragment != "" would also work here
			//	Capitalizes only first letter of fragment
			$f_part = strtoupper(substr($fragment, 0, 1));
			//	Note that the rest has already been set to lower-case from the above case-correction for hyphens
			$l_part = substr($fragment, 1);
			//	Concatenation of parts
			$fragment = $f_part.$l_part;
			//	Re-adds to piece
			$piece .= $fragment."'";
			}
		}
	
		//	Gets rid of trailing apostrophe
		$piece = rtrim($piece, "'");
		
		//	Re-adds to word
		$str .= $piece."-";
		}
	}
		
	//	Gets rid of trailing hyphen
	$str = rtrim($str, "-");
	
	//	Returns formatted string to main
	return $str;
}

//	Error handler function for emails
function CheckEmail($email) {
	// Protecting from invalid submitted data
		if (filter_var($email, FILTER_VALIDATE_EMAIL) === false) {
			throw new Exception('Invalid email submitted!');
		}
		
	return true;
}

//	Error handler function for phone numbers
function CheckPhone($phone) {
	// Protecting from invalid submitted data
	$regex = "/^(\d[\s-]?)?[\(\[\s-]{0,2}?\d{3}[\)\]\s-]{0,2}?\d{3}[\s-]?\d{4}$/i";
	
		if (!(preg_match($regex, $phone))) {
			throw new Exception('Invalid phone number!');
		}
		
	return true;
}

//	Error handling function for Postal Codes
function CheckPostalCode($postal_code) {
	// Protecting from invalid submitted data
	
	/*
	All Canadian postal codes do not include the letters D, F, I, O, Q or U, 
	and the first position also does not make use of the letters W or Z. 
	Characters alternate between letters and digits (0-9), with the first being a letter. 
	All postal codes are 6 characters in length, excluding spaces; spaces do not matter.
	*/
	
	//	Regular exprssion to screen for valid Postal Codes
	$regex = "/^([a-zA-Z]\d[a-zA-Z])\ {0,1}(\d[a-zA-Z]\d)$/";
	
		if (!(preg_match($regex, $postal_code))) {
			throw new Exception('Invalid postal code!');
		}
		
	return true;
}

//	Determines days that be selected based on month (and year); 31 by default
//	Note that this is not dynamic; the user has to submit (incorrectly) all fields for the loop to have an effect
function CheckMonthLimit($b_month, $b_year)	{	
	//	Except for February alone,
	if ($b_month == "FEB")
	{
		//	Which hath but twenty-eight days clear,
		//	And twenty-nine in each leap year.
		if ($b_year % 4 == 0){
			$limit = 30;
		}
		else{
			$limit = 29;
		}
	}
	//	Thirty days hath September,
	//	April, June, and November.
	else if ($b_month == "SEP" OR $b_month == "APL" OR $b_month == "JUN" OR $b_month == "NOV")
	{
		$limit = 31;
	}
	else
	//	All the rest have thirty-one,
	{
		$limit = 32;
	}
	
	return $limit;
}

//	When submit button is pressed
if(isset($_POST['submit'])){
    
	//	To be used for missing and reformatted data message
    $data_missing = array();
	$data_changed = array();
    
	//	If field is empty
    if(empty($_POST['first_name'])){

        // Adds name to array and updates error message
        $data_missing[] = 'First Name';
		$firstError = "* First Name missing!";

    } else {

        // Trim white space from the name and store the name
        $f_name = trim($_POST['first_name']);
		$temp = $f_name;
		// Further sanitize the name
		$f_name = TextNormalizer($f_name, false);
		
		$firstError = "";
		
		//	Determines whether the field has been reformatted or not
		if ($temp != $f_name){
			$data_changed[] = 'First Name';
			//	If name has been reformatted to empty, it is reconsidered missing
			if ($f_name == ""){
				$data_missing[] = 'First Name';
				$firstError = "* First Name missing!";
			}
		}
    }

	// If field is empty
    if(empty($_POST['last_name'])){

        // Adds name to array and update error message
        $data_missing[] = 'Last Name';
		$lastError = "* Last Name missing!";

    } else{

        // Trim white space from the name and store the name
        $l_name = trim($_POST['last_name']);
		$temp = $l_name;
		// Further sanitize the name
		$l_name = TextNormalizer($l_name, false);
		
		$lastError = "";
		
		//	Determines whether the field has been reformatted or not
		if ($temp != $l_name){
			$data_changed[] = 'Last Name';
			if ($l_name == ""){
				//	If name has been reformatted to empty, it is reconsidered missing
				$data_missing[] = 'Last Name';
				$lastError = "* Last Name missing!";
			}
		}
    }

	//	If field is empty
    if(empty($_POST['email'])){

        // Adds name to array and update error message
        $data_missing[] = 'Email';
		$emailError = "* Email missing!";

    } else {
		
    // Trim white space from the name, sanitize and store the name
    $email = trim($_POST['email']);
	$email = filter_var($email, FILTER_SANITIZE_EMAIL);
	
	//	If no error, set error message to empty
	try {
		CheckEmail($email);
		$emailError = "";
	}
	
	//	If there is an error, add to missing array and update error message
	catch(Exception $e){
        // Adds name to array
        $data_missing[] = 'Email';
		$emailError = '* Error: ' .$e-> getMessage()."<br><br>";
	}
    }

	//	Variables for street input
	// 	Error array is used for properly formatting error messages that take
	//	the number of errors in related fields into account
	$streetArray = array();
	$streetNameError = "";
	$streetTypeError = "";
	
	//	If field is empty
    if(empty($_POST['street_name'])){

        // Adds name to array
        $data_missing[] = 'Street Name';
		//	Adds error to array
		$streetNameError = "Name";
		$streetArray[] = $streetNameError;

    } else {

        // Trim white space from the name and store the name
        $street_name = trim($_POST['street_name']);
		$temp = $street_name;
		// Further sanitize the name
		$street_name = TextNormalizer($street_name, true);
		
		//	Field must be non-empty, so unset error value (Name) from array if found
		$key = array_search($streetNameError, $streetArray);
		if($key!==false){
			unset($streetArray[$key]);
		}
		
		//	Determines whether string has been reformatted or not
		if ($temp != $street_name){
			$data_changed[] = 'Street Name';
			//	If field has been set to empty, it is reconsidered as missing
			//	It is important to do this after resetting the field to empty as done above, due to "new information"
			if ($street_name == ""){
				$data_missing[] = 'Street Name';
				$streetNameError = "Name";
				$streetArray[] = $streetNameError;
			}
		}
    }
	
	//	If field is empty
    if(empty($_POST['street_type'])){

        // Adds name to array
        $data_missing[] = 'Street Type';
		//	Adds error to array
		$streetTypeError = "Type";
		$streetArray[] = $streetTypeError;

    } else {

        // Store the name
        $street_type = $_POST['street_type'];
		
		//	Field is now non-empty, so unset error if found in array
		$key = array_search($streetTypeError,$streetArray);
		if($key!==false){
			unset($streetArray[$key]);
		}
    }

    if(empty($_POST['city'])){

        // Adds name to array
        $data_missing[] = 'City';
		$cityError = "* City missing!";

    } else {

        // Trim white space from the name and store the name
        $city = trim($_POST['city']);
		$temp = $city;
		// Further sanitize the name
		$city = TextNormalizer($city, true);
		
		$cityError = "";
		
		if ($temp != $city){
			$data_changed[] = 'City';
			if ($city == ""){
				$data_missing[] = 'City';
				$cityError = "* City missing!";
			}
		}
    }

    if(empty($_POST['province'])){

        // Adds name to array
        $data_missing[] = 'Province';
		$provinceError = "* Province missing!";

    } else {

        // Store the name
        $province = $_POST['province'];
		$provinceError = "";

    }

	//	If field is empty
    if(empty($_POST['postal_code'])){

        // Adds name to array and updates error message
        $data_missing[] = 'Postal Code';
		$postalCodeError = "* Postal Code missing!";

    } else {

        // Trim white space from the name and store the name
        $postal_code = trim($_POST['postal_code']);

		//	Error handling
		//	If the Postal Code is valid
		try {
			CheckPostalCode($postal_code);
			
			//	Get rid of spaces and convert to uppercase
			$postal_code = strtoupper(str_replace(' ', '', $postal_code));
			//	Add a space between the first and last halves of postal code
			$first_part = substr($postal_code, 0, 3);
			$last_part = substr($postal_code, 3);
			$postal_code = $first_part." ".$last_part;
			$postalCodeError = "";
		}
	
		//	The Postal Code is invalid
		catch(Exception $e){
			// Adds name to array and updates error message
			$data_missing[] = 'Postal Code';
			$postalCodeError = '* Error: ' .$e -> getMessage()."<br><br>";
		}
    }

	//	If field is empty
    if(empty($_POST['phone'])){

        // Adds name to array and updates error message
        $data_missing[] = 'Phone Number';
		$phoneError = "* Phone missing!";

    } else {

        // Trim white space from the name and store the name
        $phone = trim($_POST['phone']);
		
		//	Error handling
		//	If phone number is valid
		try {
			CheckPhone($phone);
			//	Reformat phone number and update error message
			$phone = preg_replace('~.*(\d{3})[^\d]{0,7}(\d{3})[^\d]{0,7}(\d{4}).*~', '($1) $2-$3', $phone);
			$phoneError = "";
		}
		
		//	If phone number is invalid
		catch(Exception $e){
			// Adds name to array and updates error message
			$data_missing[] = 'Phone Number';
			$phoneError = '* Error: ' .$e -> getMessage()."<br><br>";
		}
    }
	
	//	Variables for date of birth (month, day, year) input fields
	//	Again, adding errors to array allows error message to account for the number (if any) of related empty fields
	$dobArray = array();
	$monthError = "";
	$dayError = "";
	$yearError = "";

	//	Field is empty
    if(empty($_POST['dob_month'])){

        // Adds name to array
        $data_missing[] = 'DOB Month';
		//	Adds error to array
		$monthError = "Month";
		$dobArray[] = $monthError;

    } else {

        // Store the name
        $b_month = $_POST['dob_month'];
		
		//	Field is not empty, so unset error if found
		$key = array_search($monthError,$dobArray);
		if($key!==false){
			unset($dobArray[$key]);
		}
    }

	//	Field is empty
	if(empty($_POST['dob_day'])){

        // Adds name to array
        $data_missing[] = 'DOB Day';
		//	Adds error to array
		$dayError = "Day";
		$dobArray[] = $dayError;

    } else {

        // Store the name
        $b_day = $_POST['dob_day'];

		//	Field is not empty, so unset error if found
		$key = array_search($dayError,$dobArray);
		if($key!==false){
			unset($dobArray[$key]);
		}
    }
	
	//	If field is empty
    if(empty($_POST['dob_year'])){

        // Adds name to array
        $data_missing[] = 'DOB Year';
		//	Adds error to array
		$yearError = "Year";
		$dobArray[] = $yearError;

    } else {

        // Store the name
        $b_year = $_POST['dob_year'];

		//	Field is not empty, so unset error if found in array
		$key = array_search($yearError,$dobArray);
		if($key!==false){
			unset($dobArray[$key]);
		}
    }
	
	//	Makes sure that the day chosen is valid, in compliance with given month & year
	if (isset($b_month) AND isset($b_day) AND isset($b_year)){
	//	Sets the number of days (+1, for < inequality), depending on the user month and year (31 by default)
	$limit = CheckMonthLimit($b_month, $b_year);
		
		//	If the day chosen originally exceeds the number of days for the changed-to month, it is invalid
		if ($b_day > ($limit - 1))
		{
			// Adds name to array
			$data_missing[] = 'DOB Day';
			//	Adds error to array
			$dayError = "Day";
			$dobArray[] = $dayError;
		}
	}

	//	If field is empty
    if(empty($_POST['gender'])){

        // Adds name to array and updates error message
        $data_missing[] = 'Gender';
		$genderError = "* Gender missing!";

    } else {

        // Store the name
        $gender = $_POST['gender'];
		$genderError = "";

    }

	//	If field is empty
    if(empty($_POST['course'])){

        // Adds name to array and updates error message
        $data_missing[] = 'Course';
		$courseError = "* Course missing!";

    } else {
		
        // Store the name
        $course = $_POST['course'];
		$courseError = "";

    }
    
	//	If there are no missing fields (empty array)
    if(empty($data_missing)){
        
		//	Connect to database
        require_once('./mysqli_connect.php');
        
		//	Insert input values into database table for students
        $query = "INSERT INTO students (first_name, last_name, email,
        street_name, street_type, city, province, postal_code, phone, dob_month, dob_day, dob_year, gender, date_entered,
        course, student_id) VALUES (?, ?, ?,
        ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW(), ?, NULL)";
        
		// Prepares the SQL query, and returns a  handle to be used for further operations.
		//	Note that $stmt is short-hand for 'statement'
        $stmt = mysqli_prepare($dbc, $query);
        
		// Bind variables for the parameters in the SQL statement that was passed to mysqli_prepare().
		
		// Note: Using prepared statements and bound parameters helps to prevent against SQL injection
		// Prepared statements do not combine variables with SQL strings, so SQL statements cannot be modified
		// Variables are treated as strings and not part of the SQL statement
		
		/*
		Second parameter is a listing of variable types for parameters:
		i	corresponding variable has type integer
		d	corresponding variable has type double
		s	corresponding variable has type string
		b	corresponding variable is a blob and will be sent in packets
		*/
		
        mysqli_stmt_bind_param($stmt, "ssssssssssiiss", $f_name,
                               $l_name, $email, $street_name, $street_type, $city,
                               $province, $postal_code, $phone, $b_month, $b_day, $b_year,
                               $gender, $course);
        
		// 	Executes query; any parameter markers will automatically be replaced with the appropriate data.
        mysqli_stmt_execute($stmt);
        
		//	Returns the total number of rows inserted by query
        $affected_rows = mysqli_stmt_affected_rows($stmt);
		
        //	One row should be affected by insertion (for one record)
		//	Output success message
        if($affected_rows == 1){?>
            <div class="isa_success">
			<i class="fa fa-check"></i>
					<?php echo 'Student Entered'; ?>
			</div>
            
			<?php
        
		//	If not one row was inserted, there was an error
        } else {
            
            echo 'Error Occurred<br />';
            echo mysqli_error();     
        }
		
		//	Close prepared statement
		   mysqli_stmt_close($stmt);
        //	Close connection to database
            mysqli_close($dbc);
		
	//	Data is missing
    } else {
		//	Set all variables to empty string if not already set
		//	Variables will still be initialized for referencing
		if (isset($f_name) == false)
		{
			$f_name = "";
		}
		if (isset($l_name) == false)
		{
			$l_name = "";
		}
		if (isset($email) == false)
		{
			$email = "";
		}
		if (isset($street_name) == false)
		{
			$street_name = "";
		}
		if (isset($street_type) == false)
		{
			$street_type = "";
		}
		if (isset($city) == false)
		{
			$city = "";
		}
		if (isset($province) == false)
		{
			$province = "";
		}
		if (isset($postal_code) == false)
		{
			$postal_code = "";
		}
		if (isset($phone) == false)
		{
			$phone = "";
		}
		if (isset($b_month) == false)
		{
			$b_month = "";
		}
		if (isset($b_day) == false)
		{
			$b_day = "";
		}
		if (isset($b_year) == false)
		{
			$b_year = "";
		}
		if (isset($gender) == false)
		{
			$gender = "";
		}
		if (isset($course) == false)
		{
			$course = "";
		}?>
		
		<!-- Table for error & info messages  -->
		<table>
		<table align="center"
		cellspacing="8">
	
		<!-- Error message for missing data -->
		<tr style = "height:100px" "width:800px"><td align="left" valign = "top">
		<div class="isa_error">
		<i class="fa fa-warning"></i>
		<div id = "message">
		<?php
        echo 'You need to enter or revise the following data: <br /><br />';
        
        foreach($data_missing as $missing){
            
            echo "☢ $missing<br />";
            
        }
		?>
		</div></div><td>
		
		<!-- Info message for reformatted data -->
		<?php
		if (!empty($data_changed)){?>
		<td align="middle" valign = "top">
		<div class="isa_info">
		<i class="fa fa-info"></i>
		<div id = "message">
		<?php

			echo 'The following data have been reformatted: <br /><br />';
        
			foreach($data_changed as $changed){
            
				echo "☢ $changed<br />";
			}
		
		?>
		</div></div></td>
		<?php } ?>
		</tr></table><?php
    }
}

//	If user resets page or correctly submits a record into database, all variables are reset to empty
//	Includes error messages and arrays
if (isset($_POST['erase']) OR (empty($data_missing))) {
	$f_name = "";
	$l_name = "";
	$email = "";
	$street_name = "";
	$street_type = "";
	$city = "";
    $province = "";
	$postal_code = "";
	$phone = "";
	$b_month = "";
	$b_day = "";
	$b_year = "";
    $gender = "";
	$course = "";
	
	$firstError = "";
	$lastError = "";
	$emailError = "";
	$streetNameError = "";
	$streetTypeError = "";
	$cityError = "";
	$provinceError = "";
	$postalCodeError = "";
	$phoneError = "";
	$monthError = "";
	$dayError = "";
	$yearError = "";
	$genderError = "";
	$courseError = "";
	
	$dobArray = array();
	$streetArray = array();
}
?>

<!-- Loads user input boxes and dropdown selection for data fields -->
<body>
<!-- Table for information fields, with input boxes -->
<!-- Input boxes have maximum string lengths to prevent mischief -->
<!-- Fields are initially set to blank -->
<table>
<table align="center"
cellspacing="8">

<!-- First name, with error message -->
<tr><td align="left"><p><font face = "Architects Daughter" size="4" ><b>First Name:</b></font></td>
<td align ="left"><input type="text" name="first_name" value = "<?php echo $f_name; ?>" size="30" maxlength="30" class = "inputs" /></p></td>
<td align = "middle"> <font face = "Architects Daughter" size="4" color="red"><?php echo $firstError ?></font> </td></tr>

<!-- Last name, with error message -->
<tr><td align="left"><p><font face = "Architects Daughter" size="4"><b>Last Name:</b></font></td>
<td align ="left"><input type="text" name="last_name" value = "<?php echo $l_name; ?>" size="30" maxlength="30" class = "inputs" /></p></td>
<td align = "middle"> <font face = "Architects Daughter" size="4" color="red"><?php echo $lastError ?></font> </td></tr>

<!-- Email, with error message -->
<tr><td align="left"><p><font face = "Architects Daughter" size="4"><b>Email:</b></font></td>
<td align ="left"><input type="text" name="email" value = "<?php echo $email; ?>" size="30" maxlength="30" class = "inputs" /></p></td>
<td align = "middle"> <font face = "Architects Daughter" size="4" color="red"><?php echo $emailError ?></font> </td></tr>

<!-- Street name & type -->
<!-- Name is fed in via input box -->
<tr><td align="left"><p>
    <font face = "Architects Daughter" size="4"><b><label for= "street"> Street:</label></b></font></td>
	
    <td align ="left">
	<input type="text" name="street_name" value = "<?php echo $street_name; ?>" size="30" maxlength="30" class = "inputs" />

<!-- Dropdown selection for street type -->
<select name="street_type" class = "inputs">
	<option value=""></option>
	<option value = "" disabled> - Type - </option>
	<?php
    foreach(array(
	"St" => "St",
	"Ave" => "Ave",
	"Blvd" => "Blvd",
	"Crcl" => "Crcl",
	"Crct" => "Crct",
	"Cres" => "Cres",
	"Crt" => "Crt",
	"Dr" => "Dr",
	"Gdns" => "Gdns",
	"Hts" => "Hts",
	"Lane" => "Lane",
	"Pkwy" => "Pkwy",
	"Pl" => "Pl",
	"Rd" => "Rd",
	"Rdwy" => "Rdwy",
	"Sq" => "Sq"
    ) as $key => $val){
		//	Remembers what option is used between rounds with some incorrect or missing data input
        ?><option value="<?php echo $key; ?>"<?php
            if($key==$street_type)echo ' selected="selected"';
        ?>><?php echo $val; ?></option><?php
    }
	?>
    </select>

<!-- Outputs error message for street fields (name & type) -->
</p></td><td align = "middle"><font face = "Architects Daughter" size="4" color="red">
<?php 
$result = count($streetArray) - 1;
//	If array is non-empty
if ($result != -1)
	{
	//	Output error-segment with the comma for all but the last error-segment
	echo "* Street ";
		for ($i = 0; $i < $result; $i++)
		{
			echo $streetArray[$i].", ";
		}
	//	Append the last error-segment without comma
	echo $streetArray[$result]." missing!";
	}
?>
</font></td></tr>

<!-- City, with error message -->
<tr><td align="left"><p><font face = "Architects Daughter" size="4"><b>City</b></font></td>
<td align ="left"><input type="text" name="city" value = "<?php echo $city; ?>" size="30" maxlength="30" class = "inputs" /></p></td>
<td align = "middle"> <font face = "Architects Daughter" size="4" color="red"><?php echo $cityError ?></font> </td></tr>

<!-- Dropdown selection for province -->
<tr><td align="left"><p>
    <font face = "Architects Daughter" size="4"><b><label for= "province"> Province:</label></b></font></td>
    <td align ="left"><select name="province" class = "inputs">
	
	<?php
    foreach(array(
	""   => "",
	"ON" => "ON",
	"NT" => "NT",
	"NU" => "NU",
	"BC" => "BC",
	"AB" => "AB",
	"SK" => "SK",
	"MB" => "MB",
	"QB" => "QB",
	"NL" => "NL",
	"PE" => "PE",
	"NB" => "NB",
	"NS" => "NS"
    ) as $key => $val){
		//	Remembers what option is used between rounds with some incorrect or missing data input
        ?><option value="<?php echo $key; ?>"<?php
            if($key==$province)echo ' selected="selected"';
        ?>><?php echo $val; ?></option><?php
    }
	?>
    </select>
</p></td>
<td align = "middle"> <font face = "Architects Daughter" size="4" color="red"><?php echo $provinceError ?></td></font></tr>

<!-- Postal code, with error message -->
<tr><td align="left"><p><font face = "Architects Daughter" size="4"><b>Postal Code:</b></font></td>
<td align ="left"><input type="text" name="postal_code" value = "<?php echo $postal_code; ?>" size="30" maxlength = "6" class = "inputs" /></p></td>
<td align = "middle"> <font face = "Architects Daughter" size="4" color="red"><?php echo $postalCodeError ?> </td></font> </tr>

<!-- Phone number, with error message -->
<tr><td align="left"><p><font face = "Architects Daughter" size="4"><b>Phone Number:</b></font></td>
<td align ="left"><input type="text" name="phone" size="30" value = "<?php echo $phone; ?>" maxlength = "14" value="" class = "inputs" /></p></td>
<td align = "middle"> <font face = "Architects Daughter" size="4" color="red"><?php echo $phoneError ?></font> </td> </tr></tr>

<!-- Dropdown selection for date of birth (proxy for age) -->
<tr><td align="left"><p>
    <font face = "Architects Daughter" size="4"><b><label for= "course"> Date of Birth:</label></b></font></td>

	<!-- Broken down into three separate dropdowns -->
	<!-- Month -->
    <td align ="left"><select name="dob_month" class = "inputs">
	
	<option value=""></option>
	<option value = "" disabled> - Month - </option>
	
	<?php
    foreach(array(
	"JAN"   => "JAN",
	"FEB"   => "FEB",
	"MAR"   => "MAR",
	"APR"   => "APR",
	"MAY"   => "MAY",
	"JUN"   => "JUN",
	"JUL"   => "JUL",
	"AUG"   => "AUG",
	"SEP"   => "SEP",
	"OCT"   => "OCT",
	"NOV"   => "NOV",
	"DEC"   => "DEC"
    ) as $key => $val){
		//	Remembers what option is used between rounds with some incorrect or missing data input
        ?><option value="<?php echo $key; ?>"<?php
            if($key==$b_month)echo ' selected="selected"';
        ?>><?php echo $val; ?></option><?php
    }
	?>
</select>

<!-- Day (1 - 31 [max], inclusive) -->
<select name="dob_day" class = "inputs">
	<option value=""></option>
	<option value = "" disabled> - Day - </option>
	<?php
	
	//	Sets the number of days (+1, for < inequality), depending on the user month and year (31 by default)
	$limit = CheckMonthLimit($b_month, $b_year);
	
	//	Remembers what option is used between rounds with some incorrect or missing data input
	for($i = 1; $i < $limit; $i++){ ?>
	  <option value="<?php echo $i ?>"<?php
            if($i==$b_day)echo ' selected="selected"';
        ?>><?php echo $i; ?></option><?php
    }
	?>
</select>

<!-- Year (upwards from 1990 to the present year - 10 -->
<!-- Basically encompasses birth years for current HS students with some generous wiggle room -->
<select name="dob_year" class = "inputs">
	<option value=""></option>
	<option value = "" disabled> - Year - </option>
	<?php
	//	Ages of 10 - 25 (currently for range of 1991 - 2006); will be used to submit student information
	//	Remembers what option is used between rounds with some incorrect or missing data input
	for($i = date("Y")-25; $i < date("Y")-9; $i++){ ?>
	  <option value="<?php echo $i ?>"<?php
            if($i==$b_year)echo ' selected="selected"';
        ?>><?php echo $i; ?></option><?php
    }
	?>
</select>

<!-- Outputs error message for all DOB fields (month, day, year), taking number of related fields, if any, into account  -->
</p></td><td align = "middle"><font face = "Architects Daughter" size="4" color="red">
<?php 
$result = count($dobArray) - 1;
//	If array is non-empty
if ($result != -1)
	{
		//	Output error-segments for each field that are not the last error-segment
		//	These are all appended with commas
	echo "* DOB ";
		for ($i = 0; $i < $result; $i++)
		{
			echo $dobArray[$i].", ";
		}
		//	Output last error-segment without comma
	echo $dobArray[$result]." missing!";
	}
?>
</font></td></tr>

<!-- Gender of student -->
<!-- Note: Sex refers to the biological and physiological characteristics that define men and women. 
Gender refers to the roles and attributes that society considers appropriate for men and women. -->
<!-- 3 options: traditional M & F and Oth for 'Other' -->
<tr><td align="left"><p>
    <font face = "Architects Daughter" size="4"><b><label for= "gender"> Gender:</label></b></font></td>
    <td align ="left"><select name="gender" class = "inputs">
	<option value=""></option>
	<?php
    foreach(array(
	"M"   => "M",
	"F"   => "F",
	"Oth" => "Oth"
    ) as $key => $val){
		//	Remembers what option is used between rounds with some incorrect or missing data input
        ?><option value="<?php echo $key; ?>"<?php
            if($key==$gender)echo ' selected="selected"';
        ?>><?php echo $val; ?></option><?php
    }
	?>
    </select>
</p></td>
<td align = "middle"> <font face = "Architects Daughter" size="4" color="red"><?php echo $genderError ?></font> </td></tr>

<!-- Course student can choose; can only choose one -->
<tr><td align="left"><p>
    <font face = "Architects Daughter" size="4"><b><label for= "course"> Course:</label></b></font></td>
    <td align ="left"><select name="course" class = "inputs">
	<option value=""></option>
	<?php
    foreach(array(
	"PAD2O1"   => "PAD2O1",
	"PAD3O1"   => "PAD3O1",
	"PLF4M & IDP4U"   => "PLF4M & IDP4U"
    ) as $key => $val){
		//	Remembers what option is used between rounds with some incorrect or missing data input
        ?><option value="<?php echo $key; ?>"<?php
            if($key==$course)echo ' selected="selected"';
        ?>><?php echo $val; ?></option><?php
    }
	?>
    </select>
</p></td>
<td align = "middle"> <font face = "Architects Daughter" size="4" color="red"><?php echo $courseError ?></font> </td></tr>
</table>

<table>
<table align="center">
<tr><td align="middle"><p>
<!-- Submits entered information and redirects to addNewStudent.php -->
<input type="submit" name="submit" class="submitButton" value="Send" />
<!-- Clears fields; has a confirmation pop-up -->
<!-- This is not a normal reset button and is in fact a submit button, but triggers a function to clear values from variables as mentioned." -->
<input type="submit" name="erase" onclick="return confirm('Are you sure?')" class="submitButton" value="Reset" /></p></td></tr>
</table>
</form>
</div>
</body>
</html>