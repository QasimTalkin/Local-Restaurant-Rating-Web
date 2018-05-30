<!DOCTYPE html>
<?php 
	session_start();
	$name = "";
	$userid = "";
	if(array_key_exists('name', $_SESSION) && array_key_exists('userid', $_SESSION)){
		$name = $_SESSION['name'];
		$userid = $_SESSION['userid'];
	}
		
?>

<html lang="en">
<head>  
	<?php $page_title = "Register" ?>

	<?php include("includes/resources.php");?>
</head>

<body>
<div class="container">
	<div class="row clearfix">
		<?php include("includes/header.php");?>
		<?php include("includes/navbar.php");?>
			<h2 class="text-center text-info">
					Register a new account with Sizzl
			</h2>
			
			<div class="register-form">
				<div class="row clearfix">
					<div class="col-md-12 column">
						<form id="formID" name="formID" method="post" action="" role="form">
							<div class="row">
								<div class="form-group-xs">
									<label for="input-name">Name</label>
									<input name ="input-name" type="name" class="form-control" id="input-name" required autofocus/>
								</div>
							</div>
							<div class="row">
								<div class="form-group-xs">
									 <label for="input-email">Email address</label>
									 <input name ="input-email" type="email" class="form-control" id="input-email" required autofocus/>
								</div>
							</div>
							<div class="row">
								<div class="form-group-xs">
									 <label for="input-pw">Password</label>
									 <input name ="input-pw" type="password" class="form-control" id="input-pw" required/>
								</div>
							</div>
							<div class="row">
								<div class="form-group-xs">
									 <label for="input-pw-confirm">Confirm Password</label>
									 <input name ="input-pw-confirm" type="password" class="form-control" id="input-pw-confirm" required/>
								</div>
							</div>
							<div class="row">
								<div class="form-group-xs">
								<label id = "input-type" name = "input-type" method="post" for="form-control">Type of Rater</label>
								<select name = "input-type" id = "input-type" method= "post" class="form-control">
									<option>Casual</option>
									<option>Blogger</option>
									<option>Verified Critic</option>
									<option>Other</option>
								</select>
								</div>
							</div>
							<div class="text-center">
								<button name="register" id="register" type="submit" class="btn btn-primary"><strong>Register!</strong></button>
							</div>
						</form>
						<?php

						if (array_key_exists('input-email', $_POST) && array_key_exists('input-pw', $_POST) && array_key_exists('input-name', $_POST)
						 && array_key_exists('input-pw-confirm', $_POST) && array_key_exists('input-type', $_POST)){
				
							//get form variables
							$getName = $_POST['input-name'];
							$getEmail = $_POST['input-email'];
							$getPass = $_POST['input-pw'];
							$getConf = $_POST['input-pw-confirm'];
							$getType = $_POST['input-type'];

							if($getType == "Casual")
								$getType = 1;
							else if($getType == "Blogger")
								$getType = 2;
							else if($getType == "Verified Critic")
								$getType = 3;
							else if($getType == "Other")
								$getType = 0;


							require("connect.php");
							
							$query = "SELECT * FROM Rater WHERE Rater.name='$getName'";
							$result = pg_query($query) or die('Query failed: ' . pg_last_error());
							
							$numRows = pg_num_rows($result);
							
							if(strpos($getName,'@')){
								echo "Your name cannot contain the @ symbol";
							}
							else if($numRows == 0){
								
								$query = "SELECT * FROM Rater WHERE Rater.email='$getEmail'";
								$result = pg_query($query) or die('Query failed: ' . pg_last_error());
							
								$numRows = pg_num_rows($result);
								
								if($numRows == 0){
									if($getPass == $getConf){
										//connect to DB
										require("connect.php");
										//Current date in YYYY-MM-DD format
										$currentDate = date('Y-m-d');
										pg_query("
											INSERT INTO Rater(email, name, join_date, type_id, reputation, password)
											VALUES('$getEmail', '$getName', '$currentDate', $getType, 1, '$getPass');
										");
										
										echo "<p align='center'>Welcome <b> $getName </b> you are now registered. <a href= './login.php'> Continue </a></p>";
									}
									else{
										echo "<p class='error'>Your password and confirmation do not match.</p>";
									}
								}
								else{
									echo "<p class='error'>That email is already taken please enter another</p>";

								}
							}
							else {
								echo "<p class='error'>That name is already taken please enter another</p>";
							}
						}
						?>
					</div>
				</div>
			</div>
	</div>
</div>

</body>
</html>