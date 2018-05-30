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
	<?php $page_title = "Review Menu Item" ?>
	
	<?php include("includes/resources.php");?>
</head>

<body>
<div class="container">
	<div class="row clearfix">
		<?php include("includes/header.php");?>
		<?php include("includes/navbar.php");?>
		<div class="col-md-12 column">
			<!-- ALL REVIEWS PREVIOUSLY WRITTEN -->
			
			<?php
				require('connect.php');
				$id = $_GET['id'];
				$query = "
				SELECT R.name AS rname, M.description AS idesc, M.name AS iname
				FROM Restaurant R, MenuItem M
				WHERE M.restaurant_id = R.restaurant_id AND M.item_id = $id
				";
				$result = pg_query($query);
				$row = pg_fetch_assoc($result);
				$rName = $row['rname'];
				$iName = $row['iname'];
				$idesc = $row['idesc']; 
				echo "<h2 class='text-info text-center'><strong>$iName</strong> at <strong>$rName</strong></h2>";
				echo "<h4 class='text-muted text-center' style='margin-bottom:40px;'><i>$idesc</i></h4>";
				echo "<h3 class='text-info text-center'>All Reviews</h3>";
			?>
			
			<table class="table table-hover"> <!-- match margin of H2 next to it -->
				<!-- Header -->
				<thead>
					<tr>
						<th>Username</th>
						<th>Posting Date</th>
						<th>Rating</th>
						<th>Comment</th>
					</tr>
				</thead>
				<tbody>
				<?php
					require('connect.php');
					$id = $_GET['id'];
					$query = "
						SELECT R.name, RI.Post_Date, RI.rating, RI.comments 
						FROM RatingItem RI, Rater R
						WHERE item_id = $id AND R.user_id = RI.user_id
					";
					$result = pg_query($query);
					while($row = pg_fetch_assoc($result)){
						$name = $row['name'];
						$date = $row['post_date'];
						$rating = $row['rating'];
						$comment = $row['comments'];
						$date = substr($date, 0, -8);
						if($comment == "")
							$comment = "N/A";
						echo "
							<tr>
								<td><a href='profile.php?name=$name'>$name</a></td>
								<td>$date</td>
								<td>$rating</td>
								<td>$comment</td>
								<td>
							</tr>";
					}
				?>
					
				</tbody>
			</table>
			
			<!-- WRITE A NEW REVIEW -->
			<hr>
			<?php
				require('connect.php');
				$id = $_GET['id'];
				$query = "
				SELECT R.name AS rname, M.name AS iname
				FROM Restaurant R, MenuItem M
				WHERE M.restaurant_id = R.restaurant_id AND M.item_id = $id
				";
				$result = pg_query($query);
				$row = pg_fetch_assoc($result);
				$rName = $row['rname'];
				$iName = $row['iname'];
				echo "<h3 class='text-info text-center'>Write a review for <strong>$iName</strong> at <strong>$rName</strong></h3>";
			?>
		</div>
	</div>
	<form id="formID" name="formID" method="post" action="" role="form">
		<div class="row clearfix">
			<div class="col-md-3 column">
				<!-- STAR Ratings (1-5) -->
				<!-- See http://plugins.krajee.com/star-rating/demo for more info -->
				<h4>Rating</h4>
				<input id="food" name = "food" method = "post" type="number" class="rating" data-min="0" data-max="5" data-step="1" data-size="md" data-show-clear="false" data-show-caption="false" required/>
			</div>

			<!-- Text area for comments -->
			<div class="col-md-9 column">
					<label for="comments">Leave your review below</label>
					<textarea style="width:100%" method="post" name="comments" id="comments" rows="8"  placeholder="Note that leaving a written comment is not necessary"></textarea>
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
			if(array_key_exists('food', $_POST)){
				require('connect.php');

				$food = $_POST['food'];
				$comments = $_POST['comments'];

				$item_id = $_GET['id'];

				//Current date in YYYY-MM-DD format
				$currentDate = date('m-d-Y H:i:s', time());

				$query = "
					INSERT INTO RatingItem(user_id, post_date, item_id, rating, comments)
					VALUES($userid, '$currentDate', $item_id, $food, '$comments');
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
			echo "You have to be rater in order to submit a review! <a href = 'register.php'>Join Now!</a>";
		}
		?>
	</div>
</div>

</body>
</html>