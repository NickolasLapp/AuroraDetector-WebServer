<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">

<?php
	// This is a very rough start to a user manageable Aurora Network system.
	// Most likely this will need to be changed around quite significantly 
	// before being deployed.

?>

<script type="text/javascript">
</script>

<html>
	<head>
		<link rel="stylesheet" href="2col_leftNav.css" type="text/css">
	</head>
	
	<body>
		<h2><p>Aurora Detector Network Account Administration</p></h2>
		<h4>Returning User Login</h4>
		<form action="verify_login.php" method="post">
			<table border="0">
				<tr>
					<td>User Name:</td>
					<td><input type="text" name="user"></td>
				</tr>
				<tr>
					<td>Password:</td>
					<td><input type="password" name="pwd" MAXLENGTH="20"></td>
				</tr>
				<tr>
					<td>
						<input type="submit" value="Log in">
					</td>
				</tr>
			</table>
		</form>
		
		<br>
		<h4>New User - Create Account</h4>
		<table>
			<form action="verify_new.php" method="post">
			<table border="0">
				<tr>
					<td>User Name:</td>
					<td><input type="text" name="user"></td>
				</tr>
				<tr>
					<td>Password:</td>
					<td><input type="password" name="pwd" MAXLENGTH="20"></td>
				</tr>
				<tr>
					<td>Confirm Password:</td>
					<td><input type="password" name="confirm" MAXLENGTH="20"></td>
				</tr>
				<tr>
					<td>
						<input type="submit" value="Sign up">
					</td>
				</tr>
			</table>
		</form>
		</table>
	</body>
</html>