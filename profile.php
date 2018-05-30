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
	<?php $page_title = "Profile" ?>

	<?php include("includes/resources.php");?>

	<script type="text/javascript">
		function popularQueryH() {
			var name = getParameterByName("name");
			document.location.href="popular.php?query=h&extrao=" + name;
		}

		function popularQueryN(higherOrLower) {
			var name = getParameterByName("name");
			document.location.href="popular.php?query=n&extrao=" + name + "&extrat=" + higherOrLower;
		}
	</script>
</head>

<body>
<div class="container">
	<div class="row clearfix">
		<div class="col-md-12 column">
			<?php include("includes/header.php");?>
			<?php include("includes/navbar.php");?>
		<?php
			$name = $_GET['name'];
			echo "
			<!-- USER INFO -->
			<h2 class='text-center text-info'>
					Viewing <strong>$name</strong>'s Profile
			</h2>";

			require('connect.php');
			$result = pg_query("SELECT * FROM Rater WHERE Rater.name = '$name'");
			$result = pg_fetch_assoc($result);

			if ($result == false) {
				echo "<h3 style='text-align:center'>This user does not exist!</h3>";
				exit;
			}

			$email = $result['email'];
			$join = $result['join_date'];
			$join = substr($join, 0, -8);
			$type = $result['type_id'];
			$result = pg_query("SELECT description FROM RaterType WHERE RaterType.type_id = $type");
			$result = pg_fetch_assoc($result);
			$type = $result['description'];

			echo "
			<dl class='dl' style='font-size:20px'>
				<dt>Username</dt> <dd>$name</dd>
				<dt>Email</dt> <dd>$email</dd>
				<dt>Join Date</dt> <dd>$join</dd>
				<dt>Type of User</dt> <dd>$type</dd>
			";
		?>
				
			</dl>

		<?php
			require('connect.php');
			if (strlen(strval($userid)) > 0) {
				$result = pg_query("SELECT use.name FROM Rater use WHERE use.user_id=$userid");
				$result = pg_fetch_array($result);
			} else {
				$result = false;
			}
			if ($result) {
				if ($result[0] == $name) {
					echo "<script type='text/javascript'>
							function deleteUser() {
								var name = getParameterByName('name');
								var result;
								jQuery.ajax({
									type: 'POST',
									url: 'delete-user.php',
									dataType: 'json',
									data: {functionname: 'deleteUser', arguments: [name]},

									success: function(obj, textstatus) {
											if (!('error' in obj)) {
												document.location.href='logout.php';
											} else {
												console.log(obj, error);
											}
										}
								});
							}
						</script>";
					echo "<a onClick='return deleteUser();' href='#'><strong>Delete your account!</strong></a>";
				}
			}
		?>

		</div>
		<div class="col-md-12 column">

		<!-- RESTAURANT REVIEWS -->
			<h2 class="text-info">Restaurant Reviews</h2>
			<h5><a onClick="popularQueryH(); return false;" href="#">(See other restaurants this user might like)</a></h5>

			<table class="table table-hover" style="margin-top:20px"> <!-- match margin of H2 next to it -->
				<!-- Header -->
				<thead>
					<tr>
						<?php 
							$name = $_GET['name'];
							$sm = "";
							if (isset($_GET['sm'])) {
								$sm = $_GET['sm'];
							}
							echo "
							<th><a href='profile.php?name=$name&sr=date&sm=$sm'>Date</a></th>
							<th><a href='profile.php?name=$name&sr=name&sm=$sm'>Name</a></th>
							<th><a href='profile.php?name=$name&sr=food&sm=$sm'>Food</a></th>
							<th><a href='profile.php?name=$name&sr=mood&sm=$sm'>Mood</a></th>
							<th><a href='profile.php?name=$name&sr=price&sm=$sm'>Price</a></th>
							<th><a href='profile.php?name=$name&sr=staff&sm=$sm'>Staff</a></th>
							<th><a href='profile.php?name=$name&sr=overall&sm=$sm'>Overall</a></th>
							<th>Comments</th>
							";
						?>
					</tr>
				</thead>
				<!-- All restaurant reviews -->
				<tbody>
				<?php
					$name = $_GET['name'];
					$query = "SELECT rest.name, loc.location_id, rate.food, rate.mood, rate.staff, rate.price, rate.comments, COALESCE((rate.food+rate.mood+rate.staff+rate.price)/4.0, 0) avgRate, rate.post_date
						FROM Rater use
						INNER JOIN Rating rate
							ON use.user_id=rate.user_id
						INNER JOIN Location loc
							ON rate.location_id=loc.location_id
						INNER JOIN Restaurant rest
							ON loc.restaurant_id=rest.restaurant_id
						WHERE use.name='$name'
						ORDER BY ";

					$sortRest = 'date';
					if (isset($_GET['sr'])) {
						$sortRest = $_GET['sr'];
					}
					switch($sortRest) {
						case 'date': default: $query.="rate.post_date DESC"; break;
						case 'name': $query.="rest.name, rate.post_date DESC"; break;
						case 'food': $query.="rate.food DESC, rate.post_date DESC"; break;
						case 'mood': $query.="rate.mood DESC, rate.post_date DESC"; break;
						case 'price': $query.="rate.price DESC, rate.post_date DESC"; break;
						case 'staff': $query.="rate.staff DESC, rate.post_date DESC"; break;
						case 'overall': $query.="avgRate DESC, rate.post_date DESC"; break;
					}

					$result = pg_query($query);
					while($res = pg_fetch_array($result)){
						$restName = $res[0];
						$locationId = $res[1];
						$food = $res[2];
						$mood = $res[3];
						$staff = $res[4];
						$price = $res[5];
						$comment = $res[6];
						$overall = round($res[7], 1);
						$postDate = substr($res[8], 0, -8);

						echo "
								<tr>
									<td width='100px'>$postDate</td>
									<td width='150px'><a href='restaurant.php?id=$locationId'>$restName</a></td>
									<td>$food</td>
									<td>$mood</td>
									<td>$price</td>
									<td>$staff</td>
									<td>$overall</td>
									<td>$comment</td>
								</tr
								";
					}
				?>
				</tbody>
			</table>
			<h5 class='text-right'><a onClick="popularQueryN('higher'); return false;" href="#">(See raters who gave overall higher ratings to restaurants)</a></h5>
			<h5 class='text-right'><a onClick="popularQueryN('lower'); return false;" href="#">(See raters who gave overall lower ratings to restaurants)</a></h5>

		<hr>
			<!-- MENU ITEM REVIEWS -->
			<h2 class="text-info">
				Menu Item Reviews
			</h2>
			
			<table class="table table-hover" style="margin-top:20px"> <!-- match margin of H2 next to it -->
				<!-- Header -->
				<thead>
					<tr>
						<?php 
							$name = $_GET['name'];
							$sr = "";
							if (isset($_GET['sr'])) {
								$sr = $_GET['sr'];
							}
							echo "
							<th><a href='profile.php?name=$name&sr=$sr&sm=date'>Date</a></th>
							<th><a href='profile.php?name=$name&sr=$sr&sm=name'>Item</a></th>
							<th><a href='profile.php?name=$name&sr=$sr&sm=price'>Price</a></th>
							<th><a href='profile.php?name=$name&sr=$sr&sm=type'>Type</a></th>
							<th><a href='profile.php?name=$name&sr=$sr&sm=rating'>Rating</a></th>
							<th>Comments</th>
							";
						?>
					</tr>
				</thead>
				<!-- All MENU ITEMS -->
				<tbody>
				<?php
					$name = $_GET['name'];
					$sortMenu = 'date';
					if (isset($_GET['sm'])) {
						$sortMenu = $_GET['sm'];
					}

					$query = "SELECT iRate.post_date, item.name, item.price, ct.description, iRate.rating, iRate.comments
						FROM Rater use
						INNER JOIN RatingItem iRate
							ON use.user_id=iRate.user_id
						INNER JOIN MenuItem item
							ON iRate.item_id=item.item_id
						INNER JOIN ItemType iType
							ON item.type_id=iType.type_id
						INNER JOIN Restaurant rest
							ON item.restaurant_id=rest.restaurant_id
						INNER JOIN CuisineType ct
							ON rest.cuisine=ct.cuisine_id
						WHERE use.name='$name'
						ORDER BY ";
					switch($sortMenu) {
						case 'date': default: $query.="iRate.post_date DESC"; break;
						case 'name': $query.="item.name, iRate.post_date DESC"; break;
						case 'price': $query.="item.price DESC, iRate.post_date DESC"; break;
						case 'type': $query.="ct.description, iRate.post_date DESC"; break;
						case 'rating': $query.="iRate.rating DESC, iRate.post_date DESC"; break;
					}
					
					$result = pg_query($query);
					while($res = pg_fetch_array($result)){
						$postDate = substr($res[0], 0, -8);
						$itemName = $res[1];
						$itemPrice = $res[2];
						$cuisineType = $res[3];
						$rating = $res[4];
						$comments = $res[5];

						echo "
								<tr>
									<td width='100px'>$postDate</td>
									<td width='150px'>$itemName</td>
									<td>\$$itemPrice</td>
									<td>$cuisineType</td>
									<td>$rating</td>
									<td>$comments</td>
								</tr
								";
					}
				?>
				</tbody>
			</table>
		</div>
	</div>
</div>
</body>
</html>