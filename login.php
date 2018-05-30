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
	<?php $page_title = "Login" ?>
	
	<?php include("includes/resources.php");?>
</head>

<body>
<div class="container">
	<div class="row clearfix">
		<?php include("includes/header.php");?>
		<?php include("includes/navbar.php");?>
		<h2 class="text-center text-info">
				Log into your Sizzl account
		</h2>
		
		<div class="register-form">
			<div class="row clearfix">
				<div class="col-md-12 column">
					<form id="formID" name="formID" method="post" action="" role="form">
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
						<!-- DISABLED UNLESS YOU KNOW HOW TO DO THIS
						<div class="checkbox">
							 <label><input type="checkbox" /> Remember Me</label>
						</div> -->
						<div class="text-center">
							<button type="submit" class="btn btn-primary"><strong>Login!</strong></button>
						</div>
					</form>
					
					<?php
						if (array_key_exists('input-email', $_POST) && array_key_exists('input-pw', $_POST)){
							
							$getEmail = $_POST['input-email'];
							$getPass = $_POST['input-pw'];
							
							require('connect.php');
							
							$result = pg_query("SELECT * FROM Rater WHERE Rater.email = '$getEmail';") or die('Query failed: ' . pg_last_error());
							
							$numRows = pg_num_rows($result);
							
							if($numRows != 0){
								$res = pg_query("SELECT * FROM Rater WHERE Rater.email = '$getEmail';") or die('Query failed: ' . pg_last_error());
								$row = pg_fetch_assoc($res);
								
								$dbPass = $row['password'];
								$dbName = $row['name'];
								$dbUserid = $row['user_id'];
								
								if($dbPass == $getPass){
									$_SESSION['name'] = $dbName;
									$_SESSION['userid'] = $dbUserid;
									?>
									<script>
									redirectMenu("index.php");
									</script>
									<?php
								}
								else{
									echo "<p class = 'error'>The password for that email does not match. Please try again</p>";
								}
							}
							else{
								echo "<p class 'error'>The email you have entered is not in our system</p>";
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