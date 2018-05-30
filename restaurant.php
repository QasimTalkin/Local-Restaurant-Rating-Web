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
	<?php $page_title = "Restaurant" ?>
	
	<?php include("includes/resources.php");?>

	<script type="text/javascript">
		function popularQueryM() {
			var id = getParameterByName("id");
			document.location.href="popular.php?query=m&extrao=" + id;
		}
	</script>
</head>

<body>
<div class="container">
	<div class="row clearfix">
			<?php include("includes/header.php");?>
			<?php include("includes/navbar.php");?>
		<div class="col-md-12 column">
		</div>
		
		<!-- Textual description -->
		<div class="col-md-5 column" style="line-height:2">
			<h2 class="text-primary"><strong>
				<?php
					require('connect.php');
					$id = $_GET['id'];
					$delete="-1";
					if(array_key_exists('delete', $_GET))
						$delete = $_GET['delete'];
					if($delete != -1){
						$deletQuery = pg_query("DELETE FROM MenuItem MI WHERE item_id = $delete");
					}
					$result = pg_query("
						SELECT * 
						FROM restaurant R, location L
						WHERE L.location_id = $id AND L.restaurant_id = R.restaurant_id; 
					");
					
					$row = pg_fetch_assoc($result);
					$rName = $row['name'];
					$rUrl = $row['url'];
					echo "<a href = 'http://$rUrl'>$rName</a>";
				?>
			</strong></h2>
			<?php
					require('connect.php');
					$id = $_GET['id'];
					
					$result = pg_query("
						SELECT * FROM location WHERE location_id = $id;
					");
					
					$row = pg_fetch_assoc($result);
			?>
			<h3 class="text-info" style="margin-top:-5px; margin-bottom:20px">
				<?php
					$number = $row['phone_number'];
					 $numbers_only = preg_replace("/[^\d]/", "", $number);

  					$number = preg_replace("/^1?(\d{3})(\d{3})(\d{4})$/", "$1-$2-$3", $numbers_only);
					echo "$number";
				?>
			</h3>
			<p>
			<?php 
				$open = $row['hour_open'];
				while (strlen($open) < 4) {
					$open = "0".$open;
				}
				$open = substr_replace($open, ":", strlen($open)-2, 0);
				$close = $row['hour_close'];
				while (strlen($close) < 4) {
					$close = "0".$close;
				}
				$close = substr_replace($close, ":", strlen($close)-2, 0);
				$first = $row['first_open_date'];
				$first = substr($first, 0, -8);
				$manager = $row['manager_name'];
				
				$result = pg_query("
					SELECT * 
					FROM cuisinetype C, restaurant R, location L
					WHERE L.location_id = $id AND L.restaurant_id = R.restaurant_id AND R.cuisine = C.cuisine_id; 
				");
				
				$row = pg_fetch_assoc($result);
				
				$cuisine = $row['description'];

				$location = $row['street_address'];
				
				
			echo"<strong>
				Hours Open: $open - $close
				</strong>
				<br>
				<a href = 'results.php?query="; echo $cuisine; echo "&cui="; echo $cuisine; echo "'>$cuisine</a>
				<br>
				Established: $first
				<br>
				Address: $location
				<br>
				Currently managed by: <i>$manager</i>
				";
			?>
			</p>

			<button  onclick = "redirect('review-restaurant.php')" name = "write-review" method  = "post"  type="write-review" class="btn btn-primary">
				<strong><span class=" glyphicon glyphicon-pencil" style="margin-right:10px"></span>Write a Review</strong>
			</button>
			</form>
			
			<div class='text-danger' style='margin-top:10px'>
			<?php
				require('connect.php');
				if (strlen(strval($userid)) > 0) {
					$result = pg_query("SELECT rt.type_id FROM Rater use INNER JOIN RaterType rt ON use.type_id=rt.type_id WHERE use.user_id=$userid");
					$result = pg_fetch_array($result);
				} else {
					$result = false;
				}
				if ($result) {
					if ($result[0] == 4) {
						echo "<script type='text/javascript'>
								function deleteRestaurant() {
									var id = getParameterByName('id');
									var result;
									jQuery.ajax({
										type: 'POST',
										url: 'delete-restaurant.php',
										dataType: 'json',
										data: {functionname: 'deleteRestaurant', arguments: [id]},

										success: function(obj, textstatus) {
												if (!('error' in obj)) {
													document.location.href='index.php';
												} else {
													console.log(obj, error);
												}
											}
									});
								}
							</script>";
						echo "<a onClick='return deleteRestaurant();' href='#'><strong>Delete this restaurant!</strong></a>";
					} else {
						echo "Want this restaurant taken down? <a href='contact.php'><strong>Contact the administrator!</a></strong>";
					}
				} else {
					echo "Want this restaurant taken down? <a href='contact.php'><strong>Contact the administrator!</a></strong>";
				}
			?>
			</div>
			
		</div>
		<!-- Maps interface & avg. rating -->
		<div class="col-md-7 column text-center" style="padding-top: 10px">
		
		<a target='_blank' href="https://www.google.com/maps/place/
		<?php
			$result = pg_query("
				SELECT * FROM location WHERE location_id = $id;
			");
					
			$row = pg_fetch_assoc($result);
			echo $row['street_address'];
		?>/"><img src="http://maps.googleapis.com/maps/api/staticmap?center=		
		<?php
			$result = pg_query("
				SELECT * FROM location WHERE location_id = $id;
			");
					
			$row = pg_fetch_assoc($result);
			echo $row['street_address'];
		?>&zoom=17&scale=1&size=400x250&maptype=roadmap&format=png&visual_refresh=true"></a>

			<br>
			<div class="rating">
				<h2 class="text-info" style="margin-bottom: -10px">
					Average Rating
				</h2>
				<strong><p style="font-size:60px">
				<?php
				require('connect.php');
					$avgRating = 0;
					$query = "
						SELECT price, food, mood, staff
						FROM Rating RA, Location L
						WHERE L.location_id = $id AND RA.location_id = L.location_id
						";
					$result = pg_query($query);
					
					$total = 0;
					while($row = pg_fetch_assoc($result)){
						$total = (int) $total + 1;
						$price = (int) $row['price'];
						$food = (int) $row['food'];
						$mood = (int) $row['mood'];
						$staff = (int) $row['staff'];
						$avg = (int) ($price + $food + $mood + $staff)/4;
						$avgRating = $avgRating + $avg;
					}
					if($total!= 0)
						$avgRating = $avgRating/$total;
				$avgRating = round($avgRating, 1); 
				if($avgRating != 0)
					echo "$avgRating";
				else echo "N/A";
				?></font></strong>
			</div>
		</div>
	</div>
	<div class="row clearfix">
		<!-- Menu Table -->
		<div class="col-md-6 column">
		<h2 class="text-info" style="margin-bottom:-5px">
		Menu
		</h2>
			<table class="table table-hover" style="margin-top:20px"> <!-- match margin of H2 next to it -->
				<!-- Header -->
				<thead>
					<tr>
						<?php
							$id = $_GET['id'];
							echo "<th><a href='restaurant.php?id=$id&sort=item'>Item</a></th>";
							echo "<th><a href='restaurant.php?id=$id&sort=price'>Price</a></th>";
							echo "<th><a href='restaurant.php?id=$id&sort=type'>Type</a></th>";
							echo "<th><a href='restaurant.php?id=$id&sort=rating'>Rating</a></th>";
						?>
						<th />
						<th />
					</tr>
				</thead>
				<!-- All menu items -->
				<tbody>
				<?php
					$rId = pg_query("SELECT restaurant_id FROM Location WHERE Location.location_id = $id");
					$rId = pg_fetch_assoc($rId);
					$rId = $rId['restaurant_id'];

					$orderBy = "";

					$menuQuery = "SELECT item.name,item.item_id, item.price, iType.description, COALESCE(AVG(itemRate.rating), 0) AS avgRating, item.type_id
						FROM MenuItem item
						LEFT JOIN RatingItem itemRate
							ON item.item_id=itemRate.item_id
						LEFT JOIN ItemType iType
							ON item.type_id=iType.type_id
						WHERE item.restaurant_id=$rId
						GROUP BY item.item_id, item.name, item.price, iType.description, item.type_id
						ORDER BY ";

					if (isset($_GET['sort'])) {
						$orderBy = $_GET['sort'];
					} else {
						$orderBy = "type";
					}

					switch($orderBy) {
						case 'type': default: $menuQuery .= "item.type_id"; break;
						case 'item': $menuQuery .="item.name"; break;
						case 'price': $menuQuery.="item.price DESC"; break;
						case 'rating': $menuQuery.="avgRating DESC"; break;
					}
					
					$result = pg_query($menuQuery);
					while($res = pg_fetch_assoc($result)){
						$iName = $res['name'];
						$price = $res['price'];
						$itemid = $res['item_id'];
						$description = $res['description'];
						$itemAvgRating = $res['avgrating'];
						if($itemAvgRating > 0){
							$itemAvgRating = round($itemAvgRating, 1);
						}
						else{
							$itemAvgRating = "N/A";
						}
						echo "
							<tr>
								<td>$iName</td>
								<td>\$$price</td>
								<td>$description</td>
								<td>$itemAvgRating</td>
								<td>";

								include("includes/edit-menu.php");

								echo "</td>
								<td>";
								
								//include("includes/delete-menu.php");
								
								echo '
								<button onclick="redirectMenu(';
									echo "'restaurant.php?id=$rId&delete=$itemid'";
									echo ')"  name = "edit-item" method  = "post"  type="edit-item" class="btn" style="padding-bottom:5px;padding-top:5px">
									<span class=" glyphicon glyphicon-remove"></span>
								</button>';


								echo "</td></tr>";
					}

				?>
					
				</tbody>
			</table>
			
			<!-- BUTTON FOR ADDING NEW MENU ITEM -->
			<button  onclick = "redirect('add-item.php')" name = "add-item" method  = "post"  type="add-item" class="btn btn-primary">
				<strong><span class=" glyphicon glyphicon-plus" style="margin-right:10px"></span>Add a Menu Item</strong>
			</button>
			
		</div>
		<!-- Reviews -->
		<div class="col-md-6 column">
			<h2 class="text-info">
				Reviews
				<h5><a onClick="popularQueryM(); return false;" href="#">(See most frequent raters)</a></h5><br>
			</h2>
			
			<?php
				require('connect.php');
				$result = pg_query("
					SELECT * FROM Rating R WHERE R.location_id = $id; 
				");
				
				while($row = pg_fetch_assoc($result)){
					$comment = $row['comments'];
					$price = $row['price'];
					$food = $row['food'];
					$mood = $row['mood'];
					$staff = $row['staff'];
					$author = $row['user_id'];
					$res1 = pg_query("SELECT type_id, name FROM Rater WHERE Rater.user_id = $author");
					$res1 = pg_fetch_assoc($res1);
					$author = $res1['name'];
					$type = $res1['type_id'];
					$res1 = pg_query("SELECT description FROM RaterType WHERE RaterType.type_id = $type");
					$res1 = pg_fetch_assoc($res1);
					$type = $res1['description'];

					echo "	
					<p>
						$comment
					</p>
					<h4>
						by <a href='profile.php?name=$author'>$author</a> | $type
					</h4>
					<strong>Food: </strong> $food | <strong>Mood: </strong> $mood | <strong>Price: </strong> $price | <strong>Staff: </strong> $staff
					<hr>
					";
				}
			?>
		</div>
	</div>
</div>

</body>
</html>