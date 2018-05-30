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
	<?php $page_title = "Review Restaurant" ?>
	
	<?php include("includes/resources.php");?>
</head>

<body>
<div class="container">
	<div class="row clearfix">
		<?php include("includes/header.php");?>
		<?php include("includes/navbar.php");?>
		<div class="col-md-12 column">
			<h2 class="text-info text-center">
			<?php
				require('connect.php');
				$id = $_GET['id'];
				$query = "
				SELECT R.name FROM Restaurant R, Location L
				WHERE L.restaurant_id = R.restaurant_id AND L.location_id = $id;
				";
				$result = pg_query($query);
				$row = pg_fetch_assoc($result);
				$rName = $row['name'];
				echo "Writing a review for <strong>$rName</strong>";
			?>
			</h2>
		</div>
	</div>
	<form id="formID" name="formID" method="post" action="" role="form">
		<div class="row clearfix">
			<div class="col-md-3 column">
				<!-- STAR Ratings (1-5) -->
				<!-- See http://plugins.krajee.com/star-rating/demo for more info -->
				<h4>Food</h4>
				<input id="food" name = "food" method = "post" type="number" class="rating" data-min="0" data-max="5" data-step="1" data-size="md" data-show-clear="false" data-show-caption="false" required/>
				<h4>Price</h4>
				<input id="price" name = "price" method = "post" type="number" class="rating" data-min="0" data-max="5" data-step="1" data-size="md" data-show-clear="false" data-show-caption="false" required/>
				<h4>Mood</h4>
				<input id="mood" name = "mood" method = "post" type="number" class="rating" data-min="0" data-max="5" data-step="1" data-size="md" data-show-clear="false" data-show-caption="false" required/>
				<h4>Staff</h4>
				<input id="staff" name = "staff" method = "post" type="number" class="rating" data-min="0" data-max="5" data-step="1" data-size="md" data-show-clear="false" data-show-caption="false" required/>
			</div>


			<!-- Text area for comments -->
			<div class="col-md-9 column">
					<label for="comments">Leave your review below</label>
					<textarea style="width:100%" method="post" name="comments" id="comments" rows="16"  placeholder="Review must be 50 characters minimum!" required></textarea>
					</input>
					<div class="pull-right" style="margin-top:15px">
						<button type="submit" name = "submit" action = "post" class="btn btn-primary"><strong>Submit Review</strong></button>
					</div>
			</div>
		</form>

		<?php
		if($userid != "" && $name != ""){
			$userid = $_SESSION['userid'];
			$id = $_GET['id'];
			if(array_key_exists('food', $_POST) && array_key_exists('price', $_POST) && array_key_exists('mood', $_POST)
				&& array_key_exists('staff', $_POST) && array_key_exists('comments', $_POST)){
				require('connect.php');

				$food = $_POST['food'];
				$price = $_POST['price'];
				$mood = $_POST['mood'];
				$staff = $_POST['staff'];
				$comments = $_POST['comments'];


				$location_id = $id;

				//Current date in YYYY-MM-DD format
				$currentDate = date('m-d-Y H:i:s', time());

				$query = "
					INSERT INTO Rating(user_id, post_date, price, food, mood, staff, comments, location_id)
					VALUES($userid, '$currentDate', $price, $food, $mood, $staff, '$comments', $location_id);
				";

				$result = pg_query($query); 

				$row = pg_fetch_assoc($result);
				$name = $row['name'];
			// 	echo "
			// 	<div class='alert alert-dismissable alert-success'>
			// 	 <button type='button' class='close' data-dismiss='alert' aria-hidden='true'>×</button>
			// 	<h4>
			// 		Alert!
			// 	</h4> <strong>Sucess!</strong> You have sucessfully submitted a review. <a href='#'' class='index.php'>Home</a>
			// </div>";
				echo "You have successfully submited a review <a href='index.php'>Back to home.</a>";
			}
		}else {
			echo "You must have an account as a rater in order to submit a review. <a href = 'register.php'>Join Now!</a>";
		}
		?>

		
	</div>

</div>

</body>
</html>