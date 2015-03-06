<?php # Admin - change_password.php
// This page allows a logged-in admin to change their password.
require ('includes/config.inc.php');
$page_title = 'Change Your Password | Guru | Vagabond Dancers';
include ('includes/header.php');

// If no first_name session variable exists, redirect the user:
if (!isset($_SESSION['admin_id'])) {

	$url = BASE_URL . 'login.php'; // Define the URL.
	ob_end_clean(); // Delete the buffer.
	header("Location: $url");
	exit(); // Quit the script.
	
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
	require (MYSQL);
	
	// Check for a new password and match against the confirmed password:
	$p = FALSE;
	if (preg_match ('/^(\w){4,20}$/', $_POST['password1']) ) {
		if ($_POST['password1'] == $_POST['password2']) {
			$p = mysqli_real_escape_string($dbc, $_POST['password1']);
		} else {
			echo '<p class="error">Your password did not match the confirmed password!</p>';
		}
	} else {
		echo '<p class="error">Please enter a valid password!</p>';
	}
	
	if ($p) { // If everything ran okay.
	
		// Make the query:
		$q = "UPDATE admin SET admin_password=SHA1('$p') WHERE admin_id={$_SESSION['admin_id']} LIMIT 1";
		$r = mysqli_query ($dbc, $q) or trigger_error("Query: $q\n<br />MySQL Error: " . mysqli_error($dbc));
		if (mysqli_affected_rows($dbc) == 1) { // If it ran okay.
		
			// Semd an email, if desired.
			echo '<h3>Your password has been changed.</h3>';
			mysqli_close($dbc); // Close the database connection.
			include ('includes/footer.php'); // Include the HTML footer.
			exit();
			
		} else { // IF it did not run okay.
		
			echo '<p class="error">Your password was not changed.  Make sure your new password is different than the current password.  Contact the system administrator if you think an error occurred.</p>';
			
		}
		
	} else { // Failed the validation test.
		echo '<p class="error">Please try again.</p>';
	}
	
	mysqli_close($dbc); // Close the databse connection.
	
} // End of the main Submit conditional
?>

<h1>Change Your Password</h1>
<form action="change_password.php" method="post">
	<fieldset>
    <p><b>New Password:</b> <input type="password" name="password1" size="20" maxlength="20" /> <small>Use only letter, number, and the underscore.  Must be between 4 and 20 characters long.</small></p>
    <p><b>Confirm New Password:</b> <input type="password" name="password2" size="20" maxlength="20" /></p>
    </fieldset>
    <div align="center"><input type="submit" name="submit" value="Change My Password" /></div>
</form>

<?php include ('includes/footer.php'); ?>