<!DOCTYPE html> <?php      session_start();     $name = "";     $userid = "";
if(array_key_exists('name', $_SESSION) && array_key_exists('userid',$_SESSION)){
         $name = $_SESSION['name'];         $userid =
		 $_SESSION['userid']; 
	     }
		
?>
<html lang="en">
<head>
	<?php $page_title = "Popular Queries" ?>
	
	<?php include("includes/resources.php");?>

	<script type="text/javascript">
		function changeCuisine_query_c() {
			var cName = document.getElementById('cuisineDrop').value;
			document.location.href="popular.php?query=c&extrao=" + cName;
		}

		function changeCuisine_query_e() {
			var cName = document.getElementById('cuisineDrop').value;
			document.location.href="popular.php?query=e&extrao=" + cName;
		}

		function changeDate() {
			var month = document.getElementById('monthDrop').value;
			var year = document.getElementById('yearDrop').value;
			document.location.href="popular.php?query=g&extrao=" + month + "&extrat=" + year;
		}

		function changeRating() {
			var name = getParameterByName("extrao");
			var rating = document.getElementById('rateDrop').value;
			document.location.href="popular.php?query=h&extrao=" + name + "&extrat=" + rating;
		}
	</script>
</head>

<body>
<div class="container">
	<div class="row clearfix">
		<?php include("includes/header.php");?>
		<?php include("includes/navbar.php");?>
		
		<?php
			require('connect.php');
			$queryName = "";
			if (isset($_GET['query'])) {
				$queryName = $_GET['query'];
			}

			if (strlen($queryName) > 0) {
				$query = file_get_contents("sql/query_".$queryName.".sql");
			}
						
			switch($queryName) {
				case "":
					echo "
						<h2 class='text-center text-info' style='margin-top:20px;margin-bottom:20px'>
							Popular Queries
						</h2>
						<h4 class='text-center' style='margin-bottom:20px'>
							Try clicking some of the links below to view a variety of data about our restaurants and users
						</h4>

						<div class='well well-sm text-center' style='line-height:1.75; font-size:16px'>
							<a href='popular.php?query=p'>Most recent reviews</a><br>
						</div>
						<div class='well well-sm text-center' style='line-height:1.75; font-size:16px'>
							<a href='popular.php?query=c'>Managers & opening dates by restaurant type</a><br>
						</div>
						<div class='well well-sm text-center' style='line-height:1.75; font-size:16px'>
							<a href='popular.php?query=e'>Average menu prices by restaurant type</a><br>
						</div>
						<div class='well well-sm text-center' style='line-height:1.75; font-size:16px'>
							<a href='popular.php?query=f'>Number of ratings, by rater, for each restaurant</a><br>
						</div>
						<div class='well well-sm text-center' style='line-height:1.75; font-size:16px'>
							<a href='popular.php?query=g'>Restaurants not rated in a certain month</a><br>
						</div>
						<div class='well well-sm text-center' style='line-height:1.75; font-size:16px'>
							<a href='popular.php?query=i'>Top rated restaurants by category</a><br>
						</div>
						<div class='well well-sm text-center' style='line-height:1.75; font-size:16px'>
							<a href='popular.php?query=j'>Average ratings by restaurant type</a><br>
						</div>
						<div class='well well-sm text-center' style='line-height:1.75; font-size:16px'>
							<a href='popular.php?query=k'>Raters who give the highest ratings</a><br>
						</div>
						<div class='well well-sm text-center' style='line-height:1.75; font-size:16px'>
							<a href='popular.php?query=o'>Raters with the most diverse ratings</a><br>
						</div>
						<div class='well well-sm text-center' style='line-height:1.75; font-size:16px'>
							<a href='popular.php?query=q'>List of all raters</a><br>
						</div>
					";
					break;
				case "c": default:
					$extraOne = 'Breakfast';
					if (isset($_GET['extrao'])) {
						$extraOne = $_GET['extrao'];
					}

					echo "	
						<h2 class='text-center text-info' style='margin-bottom:20px'>
							Managers &amp; opening dates for <strong>$extraOne</strong> restaurants
						</h2>
						";
					$result = pg_query("SELECT ct.description FROM CuisineType ct ORDER BY ct.description");
					echo "<select style='margin-bottom:20px' id='cuisineDrop' name='cuisineDrop' onchange='changeCuisine_query_c()' class='center-block'>";
					while($res = pg_fetch_array($result)) {
						$description = $res[0];
						echo "<option value='$description'";
						if (strcmp($extraOne, $description) == 0) {
							echo " selected";
						}
						echo ">$description</option>";
					}
					echo "</select><br>";

					$query = str_replace("CUISINE_DESCRIPTION", $extraOne, $query);
					$result = pg_query($query);
					
					while ($res = pg_fetch_array($result)) {
						$id = $res[0];
						$restName = $res[1];
						$address = $res[2];
						$manager = $res[3];
						$openDate = substr($res[4], 0, -8);

						echo "
						<div class='well well-sm' style='line-height:1.75; font-size:16px'>
							<strong><a href='restaurant.php?id=$id'>$restName</a></strong><br>
							$address<br>
							Managed by: $manager<br>
							First Opened: $openDate
						</div>
						";
					}
					break;
				case "e":
					$extraOne = 'Breakfast';
					if (isset($_GET['extrao'])) {
						$extraOne = $_GET['extrao'];
					}

					echo "	
						<h2 class='text-center text-info' style='margin-bottom:20px'>
							Average <strong>$extraOne</strong> restaurant menu prices
						</h2>
						";
					$result = pg_query("SELECT ct.description FROM CuisineType ct ORDER BY ct.description");
					echo "<select style='margin-bottom:20px' id='cuisineDrop' name='cuisineDrop' onchange='changeCuisine_query_e()' class='center-block'>";
					while($res = pg_fetch_array($result)) {
						$description = $res[0];
						echo "<option value='$description'";
						if (strcmp($extraOne, $description) == 0) {
							echo " selected";
						}
						echo ">$description</option>";
					}
					echo "</select><br>";

					$query = str_replace("CUISINE_DESCRIPTION", $extraOne, $query);
					$result = pg_query($query);
					
					while ($res = pg_fetch_array($result)) {
						$itemDesc = $res[0];
						$price = round($res[1], 2);

						echo "
						<div class='well well-sm' style='line-height:1.75; font-size:16px'>
							<strong>$itemDesc: </strong>\$$price<br>
						</div>
						";
					}
					break;
				case "f":
					echo "	
						<h2 class='text-center text-info' style='margin-bottom:20px'>
							Total number of ratings by each rater, for each restaurant
						</h2>
						";

					$result = pg_query($query);
					while($res = pg_fetch_array($result)) {
						$userId = $res[0];
						$restId = $res[1];
						$userName = $res[2];
						$restName = $res[3];
						$ratingCount = $res[4];
						$avgRating = round($res[5], 1);

						echo "
						<div class='well well-sm' style='line-height:1.75; font-size:16px'>
							<strong><a href='profile.php?name=$userName'>$userName</a></strong><br>
							<a href='restaurant.php?id=$restId'>$restName</a><br>
							# of ratings: $ratingCount<br>
							Average Rating: $avgRating
						</div>
						";
					}
					break;
				case "g":
					$extraOne = strval(date("m"));
					if (isset($_GET['extrao'])) {
						$extraOne = $_GET['extrao'];
					}
					$extraTwo = strval(date("Y"));
					if (isset($_GET['extrat'])) {
						$extraTwo = $_GET['extrat'];
					}

					switch(intval($extraOne)) {
						case 1: $monthName = "January"; break;
						case 2: $monthName = "Febraury"; break;
						case 3: $monthName = "March"; break;
						case 4: $monthName = "April"; break;
						case 5: $monthName = "May"; break;
						case 6: $monthName = "June"; break;
						case 7: $monthName = "July"; break;
						case 8: $monthName = "August"; break;
						case 9: $monthName = "September"; break;
						case 10: $monthName = "October"; break;
						case 11: $monthName = "November"; break;
						case 12: $monthName = "December"; break;
					}
					echo "
						<h2 class='text-center text-info' style='margin-bottom:20px'>
							Restaurants not rated in <strong>$monthName $extraTwo</strong>
						</h2>
						";

					echo "<select style='margin-bottom:20px' class='center-block' id='monthDrop' name='monthDrop' onchange='changeDate()'>";
					$month = 1;
					while($month <= 12) {
						switch($month) {
							case 1: $monthName = "January"; break;
							case 2: $monthName = "Febraury"; break;
							case 3: $monthName = "March"; break;
							case 4: $monthName = "April"; break;
							case 5: $monthName = "May"; break;
							case 6: $monthName = "June"; break;
							case 7: $monthName = "July"; break;
							case 8: $monthName = "August"; break;
							case 9: $monthName = "September"; break;
							case 10: $monthName = "October"; break;
							case 11: $monthName = "November"; break;
							case 12: $monthName = "December"; break;
						}

						echo "<option value='$month'";
						if (strcmp(ltrim($extraOne, '0'), strval($month)) == 0) {
							echo " selected";
						}
						echo ">$monthName</option>";
						$month += 1;
					}
					echo "</select>";

					echo "<select style='margin-bottom:20px' class='center-block' id='yearDrop' name='yearDrop' onchange='changeDate()'>";
					$year = date("Y");
					while($year >= 1900) {
						echo "<option value='$year'";
						if (strcmp($extraTwo, strval($year)) == 0) {
							echo " selected";
						}
						echo ">$year</option>";
						$year -= 1;
					}
					echo "</select><br>";

					$query = str_replace("MONTH", strval($month), $query);
					$query = str_replace("YEAR", strval($year), $query);
					$result = pg_query($query);
					while($res = pg_fetch_array($result)) {
						$restName = $res[0];
						$cuisine = $res[1];
						$number = $res[2];
						$numbers_only = preg_replace("/[^\d]/", "", $number);
						$number = preg_replace("/^1?(\d{3})(\d{3})(\d{4})$/", "$1-$2-$3", $numbers_only);
						$address = $res[3];
						$open = $res[4];
						$open = substr_replace($open, ":", strlen($open)-2, 0);
						$close = $res[5];
						$close = substr_replace($close, ":", strlen($close)-2, 0);
						$id = $res[6];

						echo "
						<div class='well well-sm' style='line-height:1.75; font-size:16px'>
							<strong><a href='restaurant.php?id=$id'>$restName</a></strong><br>
							<a href = 'results.php?query=$cuisine&cui=$cuisine'>$cuisine</a><br>
							$number <br>
							$address <br>
							$open - $close
						</div>
						";
					}
					break;
				case "h":
					$extraOne = "";
					if (isset($_GET['extrao'])) {
						$extraOne = $_GET['extrao'];
					}
					$extraTwo = "Food";
					if (isset($_GET['extrat'])) {
						$extraTwo = $_GET['extrat'];
					}

					$food = "<option value='Food'>Food</option>";
					$mood = "<option value='Mood'>Mood</option>";
					$staff = "<option value='Staff'>Staff</option>";
					$price = "<option value='Price'>Price</option>";
					switch($extraTwo) {
						case 'Food': default: $food = "<option value='Food' selected>Food</option>"; break;
						case 'Mood': $mood = "<option value='Mood' selected>Mood</option>"; break;
						case 'Staff': $staff = "<option value='Staff' selected>Staff</option>"; break;
						case 'Price': $price = "<option value='Price' selected>Price</option>"; break;
					}

					echo "	
						<h2 class='text-center text-info' style='margin-bottom:20px'>
							<strong>$extraTwo</strong> rated better than <strong>$extraOne</strong>&#39;s ratings
						</h2>


						<select style='margin-bottom:20px' class='center-block' id='rateDrop' name='rateDrop' onchange='changeRating()'>
							$food
							$mood
							$staff
							$price
						</select>
						";

					$query = str_replace("NAME_REPLACE", $extraOne, $query);
					$query = str_replace("RATING_REPLACE", $extraTwo, $query);
					$result = pg_query($query);
					while($res = pg_fetch_array($result)) {
						$locationId = $res[0];
						$restName = $res[1];
						$openDate = substr($res[2], 0, -8);
						$food = round($res[3], 1);
						$staff = round($res[4], 1);
						$mood = round($res[5], 1);
						$price = round($res[6], 1);
						$overall = round($res[7], 1);
						$cuisine = $res[8];

						echo "
						<div class='well well-sm' style='line-height:1.75; font-size:16px'>
							<strong><a href='restaurant.php?id=$locationId'>$restName</a></strong><br>
							<a href='results.php?query=$cuisine&cui=$cuisine'>$cuisine</a><br>
							First opened: $openDate <br>
							Food: $food, Mood: $mood, Staff: $staff, Price: $price <br>
							<strong>Overall Rating:</strong> $overall
						</div>
						";
					}
					break;
				case "i":
					echo "	
						<h2 class='text-center text-info' style='margin-bottom:20px'>
							Top rated restaurants by category
						</h2>
						";

					$result = pg_query($query);
					$lastRestaurant = "";
					$restCount = 0;
					$userCount = 0;
					while($res = pg_fetch_array($result)) {
						$restCount += 1;
						$description = $res[0];
						$locationId = $res[1];
						$restName = $res[2];
						$userName = $res[4];
						$avgRate = round($res[5], 1);

						$isNotLast = (strcmp($restName, $lastRestaurant) !== 0);
						$lastRestaurant = $restName;
						if ($isNotLast) {
							$userCount = 0;
							if ($restCount > 1) {
								echo "</div>";
							}
							echo "
								<div class='well well-sm' style='line-height:1.75; font-size:16px'>
									<strong><a href='restaurant.php?id=$locationId'>$restName</a></strong><br>
									<a href = 'results.php?query=$description&cui=$description'>$description</a><br>
									Average Rating: $avgRate <br>
									Raters: 
								";
						}
						$userCount += 1;
						if ($userCount > 1) {
							echo ", ";
						}
						echo "<a href='profile.php?name=$userName'>$userName</a>";

					}
					break;
				case "j":
					echo "	
						<h2 class='text-center text-info' style='margin-bottom:20px'>
							Average restaurant ratings by <strong>cuisine</strong> 
						</h2>
						";
					$result = pg_query($query);
					while($res = pg_fetch_array($result)) {
						$cuisine = $res[0];
						$avgRate = round($res[1], 1);
						echo "
						<div class='well well-sm' style='line-height:1.75; font-size:16px'>
							<strong><a href='results.php?query=$cuisine&cui=$cuisine'>$cuisine: </a></strong>$avgRate<br>
						</div>
						";
					}
					break;
				case "k":
					echo "	
						<h2 class='text-center text-info' style='margin-bottom:20px'>
							Raters who give the highest ratings
						</h2>
						";

					$result = pg_query($query);
					while($res = pg_fetch_array($result)) {
						$userName = $res[0];
						$joinDate = substr($res[1], 0, -8);
						$ratingCount = round($res[2], 1);
						$avgRate = round($res[3], 1);

						echo "
							<div class='well well-sm' style='line-height:1.75; font-size:16px'>
								<strong><a href='profile.php?name=$userName'>$userName</a></strong><br>
								Joined: $joinDate <br>
								<strong>Total # of ratings:</strong> $ratingCount <br>
								<strong>Average rating:</strong> $avgRate <br>
							</div>
						";
					}
					break;
				case "m":
					$extraOne = "";
					if (isset($_GET['extrao'])) {
						$extraOne = $_GET['extrao'];
					}

					$result = pg_query("SELECT rest.name FROM Location loc INNER JOIN Restaurant rest ON loc.restaurant_id=rest.restaurant_id WHERE loc.location_id=$extraOne");
					$result = pg_fetch_array($result);
					$restName = $result[0];
					echo "
						<h2 class='text-center text-info' style='margin-bottom:20px'>
							Raters who most frequently rated <strong><a href='restaurant.php?id=$extraOne'>$restName</a></strong>
						</h2>";

					$query = str_replace("LOCATION_REPLACE", $extraOne, $query);
					$result = pg_query($query);
					$lastUser = "";
					$userInfo = "";
					$userComments = "";
					$userCount = 0;
					while($res = pg_fetch_array($result)) {
						$userName = $res[0];
						$comments = $res[1];
						$ratingCount = $res[2];

						if (strcmp($lastUser, $userName) != 0) {
							$lastUser = $userName;
							$userCount += 1;
							if ($userCount > 1) {
								echo $userInfo.$userComments."</div>";
							}

							$userInfo = "<div class='well well-sm' style='line-height:1.75; font-size:16px'>
									<strong><a href='profile.php?name=$userName'>$userName</a></strong><br>
									<strong>Reputation:</strong> $ratingCount <br>";
							$userComments = "<strong>Comments</strong><br>";
						}

						$userComments.="<p style='padding-left:2em'>$comments </p>";
					}

					if (strlen($userInfo) > 0) {
						echo $userInfo.$userComments."</div>";
					}
					break;
				case "n":
					$extraOne = "";
					if (isset($_GET['extrao'])) {
						$extraOne = $_GET['extrao'];
					}
					$extraTwo = "true";
					if (isset($_GET['extrat'])) {
						$extraTwo = $_GET['extrat'];
					}

					if ($extraTwo == 'true') {
						$compare = ">=";
						$compareText = "higher";
						$compareLink = "popular.php?query=n&extrao=$extraOne&extrat=false";
					} else {
						$compare = "<";
						$compareText = "lower";
						$compareLink = "popular.php?query=n&extrao=$extraOne&extrat=true";
					}

					echo "
						<h2 class='text-center text-info' style='margin-bottom:20px'>
							Raters who give overall <strong><a href='$compareLink'>$compareText</a></strong> ratings than <strong>$extraOne</strong>
						</h2>";

					$query = str_replace("NAME_REPLACE", $extraOne, $query);
					$query = str_replace("COMPARE_REPLACE", $compare, $query);
					$result = pg_query($query);
					while ($res = pg_fetch_array($result)) {
						$userName = $res[0];
						$userEmail = $res[1];
						$ratingCount = $res[2];
						$avgRate = round($res[3], 1);

						echo "
							<div class='well well-sm' style='line-height:1.75; font-size:16px'>
								<strong><a href='profile.php?name=$userName'>$userName</a></strong><br>
								Email: $userEmail <br>
								<strong>Total # of ratings:</strong> $ratingCount <br>
								<strong>Average rating:</strong> $avgRate <br>
							</div>
						";
					}
					break;
				case "o":
					echo "	
						<h2 class='text-center text-info' style='margin-bottom:20px'>
							Raters with the most diverse ratings
						</h2>
						";
					$result = pg_query($query);
					$userCount = 0;
					while ($res = pg_fetch_array($result)) {
						$userCount += 1;
						$userName = $res[0];
						$userId = $res[1];
						$userDescription = $res[2];
						$userEmail = $res[3];
						$locationId = $res[4];
						$restName = $res[5];
						$highRate = round($res[6]/4.0, 2);
						$lowRate = round($res[7]/4.0, 2);
						$description = $res[9];
						echo "
							<div class='well well-sm' style='line-height:1.75; font-size:16px'>
								<strong><a href='restaurant.php?id=$locationId'>$restName</a></strong><br>
								<a href='results.php?query=$description&cui=$description'>$description</a><br>
								Rater: <a href='profile.php?name=$userName'>$userName</a> | $userDescription <br>
								Low rating: $lowRate <br>
								High rating: $highRate
							</div>
							";
					}

					if ($userCount == 0) {
						echo "
							<div class='well well-sm' style='line-height:1.75; font-size:16px'>
								<strong>No users with diverse enough ratings.</strong>
							</div>
						";
					}
					break;
				case 'p':
					$extraOne = "25";
					if (isset($_GET["extrao"])) {
						$extraOne = $_GET["extrao"];
					}

					switch($extraOne) {
						case '25': default: $link = "<a href='popular.php?query=p&amp;extrao=50'>25</a>"; break;
						case '50': default: $link = "<a href='popular.php?query=p&amp;extrao=100'>50</a>"; break;
						case '100': default: $link = "<a href='popular.php?query=p&amp;extrao=25'>100</a>"; break;
					}
					echo "	
						<h2 class='text-center text-info' style='margin-bottom:20px'>
							<strong>$link</strong> most recent reviews
						</h2>
						";
					$query = str_replace("LIMIT_REPLACE", $extraOne, $query);
					$result = pg_query($query);
					while($res = pg_fetch_array($result)) {
						$restName = $res[0];
						$userName = $res[1];
						$raterType = $res[2];
						$postDate = substr($res[3], 0, -8);
						$comments = $res[4];
						$food = $res[5];
						$mood = $res[6];
						$price = $res[7];
						$staff = $res[8];
						$locationId = $res[9];

						echo "
							<div class='well well-sm' style='line-height:1.75; font-size:16px'>
								<strong><a href='restaurant.php?id=$locationId'>$restName</a></strong><br>
								<h4>by <a href='profile.php?name=$userName'>$userName</a> | $raterType <br>
								on $postDate </h4>
								$comments <br>
								<strong>Food:</strong> $food | <strong>Mood:</strong> $mood | <strong>Price:</strong> $price | <strong>Staff:</strong> $staff
							</div>
						";
					}
					break;
				case 'q':
					$extraOne = 'rep';
					if (isset($_GET['extrao'])) {
						$extraOne = $_GET['extrao'];
					}

					switch($extraOne) {
						case 'rep': default:
							$link = "<a href='popular.php?query=q&amp;extrao=name'>Reputation</a>";
							$orderBy = "ratingCount DESC, use.name";
							break;
						case 'name':
							$link = "<a href='popular.php?query=q&amp;extrao=date'>Name</a>";
							$orderBy = "use.name";
							break;
						case 'date':
							$link = "<a href='popular.php?query=q&amp;extrao=rep'>Join Date</a>";
							$orderBy = "use.join_date DESC, ratingCount DESC, use.name";
							break;
					}

					echo "	
						<h2 class='text-center text-info' style='margin-bottom:20px'>
							List of all raters, sorted by <strong>$link</strong>
						</h2>
						";

					$query = str_replace("ORDER_REPLACE", $orderBy, $query);
					$result = pg_query($query);
					while ($res = pg_fetch_array($result)) {
						$userName = $res[0];
						$userEmail = $res[1];
						$userJoinDate = substr($res[2], 0, -8);
						$type = $res[3];
						$ratingCount = $res[4];

						echo "
							<div class='well well-sm' style='line-height:1.75; font-size:16px'>
								<strong><a href='profile.php?name=$userName'>$userName</a></strong><br>
								<strong>Joined on:</strong> $userJoinDate <br>
								<strongEmail:</strong $userEmail <br>
								<strong>Type:</strong> $type <br>
								<strong>Total # of ratings:</strong> $ratingCount
							</div>
						";
					}
					break;
			} 
		?>
	</div>
</div>

</body>
</html>