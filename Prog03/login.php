<?php
/* ---------------------------------------------------------------------------
 * filename    : login.php
 * author      : George Corser, gcorser@gmail.com
 * description : This program logs the user in by setting $_SESSION variables
 * ---------------------------------------------------------------------------
 */
// Start or resume session, and create: $_SESSION[] array
session_start();
// include the class that handles database connections
require "database.php";

if (!empty($_POST["join"])) {
	$username = $_POST['username']; // username is email address
	$password = $_POST['password'];
	$passwordhash = MD5($password);
	$labelError = "";
	//Make sure the email is in valid form.
	if (!filter_var($username,FILTER_VALIDATE_EMAIL)) {
		echo "Email must be of the form something@something.somthing";
		exit();
	}
	$subject = 'Email Verification';
	$message = 'hello';
	$headers = 'From: webmaster@customersdatabase.com' . "\r\n" .
    'Reply-To: webmaster@customersdatabase.com' . "\r\n" .
    'X-Mailer: PHP/' . phpversion();
	mail($username, $subject, $message, $headers);
}

if (!empty($_POST["valid"])) {
	// if valid data, insert record into table
	$pdo = Database::connect();
	$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	$sql = "INSERT INTO customers (email,password_hash) values(?, ?)";
	$q = $pdo->prepare($sql);
	$q->execute(array($username, $passwordhash));
	Database::disconnect();
}


if (!empty($_POST) && empty($_POST["join"]) && empty($_POST["valid"])) { // if $_POST filled then process the form
	// initialize $_POST variables
	$username = $_POST['username']; // username is email address
	$password = $_POST['password'];
	$passwordhash = MD5($password);
	$labelError = "";

	// verify the username/password
	$pdo = Database::connect();
	$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	$sql = "SELECT * FROM customers WHERE email = ? AND password_hash = ? LIMIT 1";
	$q = $pdo->prepare($sql);
	$q->execute(array($username,$passwordhash));
	$data = $q->fetch(PDO::FETCH_ASSOC);

	if($data) { // if successful login set session variables
		// echo "success!";
		$_SESSION['tJHSQRuoNnWUwLRe'] = $data['id'];
		$_SESSION['name'] = $data['name'];
		$_SESSION['email'] = $data['email'];
		$_SESSION['mobile'] = $data['mobile'];
		$sessionid = $data['id'];
		Database::disconnect();
		header("Location: customers.php ");
		// javascript below is necessary for system to work on github
		// echo "<script type='text/javascript'> document.location = 'fr_assignments.php'; </script>";
		exit();
	}
	else { // display error message
		Database::disconnect();
		$labelError = "Incorrect username/password";
	}


}
// if $_POST NOT filled then display login form, below.
?>

<!DOCTYPE html>
<html lang="en">
	<head>
        <meta charset='UTF-8'>
        <link href='https://stackpath.bootstrapcdn.com/bootstrap/4.1.2/css/bootstrap.min.css' rel='stylesheet'>
        <script src='https://stackpath.bootstrapcdn.com/bootstrap/4.1.2/js/bootstrap.min.js'></script>
	</head>

<body>
    <div class="container">
		<div class="span10 offset1">

			<div class="row">
				<h3>Login</h3>
			</div>

			<form class="form-horizontal" action="login.php" method="post">

				<div class="control-group">
					<label class="control-label">Username (Email)</label>
					<div class="controls">
						<input name="username" type="text"  placeholder="me@email.com" required>
					</div>
				</div>

				<div class="control-group">
					<label class="control-label">Password</label>
					<div class="controls">
						<input name="password" type="password" placeholder="not your SVSU password, please" required>
					</div>
				</div>

				<div class="form-actions">
					<button type="submit" class="btn btn-success">Sign in</button>
					&nbsp; &nbsp;
					<button type="submit" class="btn btn-success" name="join" value="true">Join</a>
				</div>

				<div>
					<?php
						echo "<br>";
						echo "<span style='color: red;' class='help-inline'>";
						echo "&nbsp;&nbsp;" . $labelError;
						echo "</span>";
						echo "<br>";
					?>
				</div>

				<footer>
					<small>&copy; Copyright 2019, George Corser
					</small>
				</footer>

			</form>


		</div> <!-- end div: class="span10 offset1" -->

    </div> <!-- end div: class="container" -->

  </body>

</html>
