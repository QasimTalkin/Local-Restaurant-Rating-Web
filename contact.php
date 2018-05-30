<!DOCTYPE html> 
<?php
	session_start();     $name = "";     $userid = "";
	if(array_key_exists('name', $_SESSION) && array_key_exists('userid',$_SESSION)){
         $name = $_SESSION['name'];         $userid =
		 $_SESSION['userid']; 
	}
		
?>
<html lang="en">
<head>
	<?php $page_title = "Contact" ?>
	
	<?php include("includes/resources.php");?>
</head>

<body>
<div class="container">
	<div class="row clearfix">
			<?php include("includes/header.php");?>
			<?php include("includes/navbar.php");?>
			
			<h2 class="text-center text-info">
				Contact us, we promise not to bite
			</h2>
			
			<div class="register-form">
				<div class="row clearfix">
					<div class="col-md-12 column">
						<form method = "post" role="form">
							<div class="row">
							<!-- Name -->
								<div class="form-group-xs">
									 <label for="input-name">Name</label>
									 <input name = "input-name" id = "input-name" type="text" class="form-control" id="input-name" autofocus/>
								</div>
							</div>
							<div class="row">
							<!-- Email -->
								<div class="form-group-xs">
									 <label for="input-email">Email address</label>
									 <input name = "input-email" id = "input-email" type="email" class="form-control" id="input-email" required/>
								</div>
							</div>
							<!-- Comments -->
							<div class="row">
								<div class="form-group-xs">
									 <label for="input-comments">Comments? Suggestions?</label>
									 <textarea name = "input-comment" name = "input-comment" style="width:100%" name="comments" rows="10"  placeholder="We're listening...!" required></textarea>
								</div>
							</div>
							<!-- Submit button -->
							<div class="text-center">
								<button type="submit" class="btn btn-primary"><strong>Submit</strong></button>
							</div>
						</form>
						<?php
							if(array_key_exists('input-name', $_POST) && array_key_exists('input-email', $_POST)
								&& array_key_exists('input-comment', $_POST)){
								require('connect.php');
								$name = $_POST['input-name'];
								$email = $_POST['input-email'];
								$comment = $_POST['input-comment'];

								$query = "
									INSERT INTO Contact(name, email, comments)
									VALUES('$name', '$email', '$comment');
								";
								pg_query($query);

								echo "Thank you for your comment, we will contact you back shortly. <a href = 'index.php'>Back to home.</a>";
							}
						?>
					</div>
				</div>
			</div>
			
	</div>
</div>

</body>
</html>