<!DOCTYPE html> <?php      session_start();     $name = "";     $userid = "";
if(array_key_exists('name', $_SESSION) && array_key_exists('userid',$_SESSION)){
         $name = $_SESSION['name'];         $userid =
		 $_SESSION['userid']; 
	     }
		
?>
<html lang="en">
<head>
	<?php $page_title = "Results" ?>
	
	<?php include("includes/resources.php");?>

	<script type="text/javascript">
		function sortResults(sorting) {
			var query = getParameterByName("query");
			var cui = getParameterByName("cui");
			document.location.href="results.php?query=" + query + "&cui=" + cui + "&sort=" + sorting;
		}
	</script>
</head>

<body>
<div class="container">
	<div class="row clearfix">
		<?php include("includes/header.php");?>
		<?php include("includes/navbar.php");?>

		<div class="col-md-12 column">
		
					<!-- Buttons to sort results - rating/relevance/alphabetical-->
		<div class="col-md-12 column text-center" style="margin-bottom:20px">
			<p style="font-size:125%">Sort these results by:</p> 
			<div class="btn-group" role="group" aria-label="..." style="margin-top:-5px">
				<a onClick="sortResults('rating'); return false;" class="btn-group" role="group" href="results.php">
					<button type="button" class="btn btn-default">
					<span class=" glyphicon glyphicon-star" style="margin-right:5px"></span>Rating</button>
				</a>
				<a onClick="sortResults('rel'); return false;" class="btn-group" role="group" href="results.php">
					<button type="button" class="btn btn-default">
					<span class=" glyphicon glyphicon-sort-by-attributes-alt" style="margin-right:5px"></span>Relevance</button>
				</a>
				<a onClick="sortResults('alpha'); return false;" class="btn-group" role="group" href="results.php">
					<button type="button" class="btn btn-default">
					<span class=" glyphicon glyphicon-sort-by-alphabet" style="margin-right:5px"></span>Alphabetical</button>
				</a>
			</div>
		</div>
		<hr>
		
			<?php
			require('connect.php');
			$aQuery;
			$query = $_GET['query'];

			$cui = "";
			if (isset($_GET['cui'])) {
				$cui = $_GET['cui'];
			}

			$sort = "rating";
			if (isset($_GET['sort'])) {
				$sort = $_GET['sort'];
			}
			
			$gQuery = $query;
			if($gQuery == ""){
				$gQuery = "All Restaurants";
				$aQuery = 
					"SELECT loc.location_id, rest.name, AVG((coalesce(rate.food,0)+coalesce(rate.price,0)+coalesce(rate.mood,0)+coalesce(rate.staff,0))/4.0) rateAvg
					FROM Location loc
					LEFT JOIN Restaurant rest
						ON rest.restaurant_id=loc.restaurant_id
					LEFT JOIN Rating rate
						ON rate.location_id=loc.location_id
					GROUP BY loc.location_id, rest.name
				";
				switch($sort) {
					case 'rating': default: $aQuery.=' ORDER BY rateAvg DESC, rest.name'; break;
					case 'rel': $aQuery.=' ORDER BY rateAvg DESC, rest.name'; break;
					case 'alpha': $aQuery.=' ORDER BY rest.name, rateAvg DESC'; break;
				}
			}
			else{
				$exQuery = explode(" ",$query);
				$aQuery = 
					"SELECT loc.location_id, rest.name, AVG((coalesce(rate.food,0)+coalesce(rate.price,0)+coalesce(rate.mood,0)+coalesce(rate.staff,0))/4.0) rateAvg, COUNT(loc.location_id) idCount
					FROM Location loc
					LEFT JOIN Restaurant rest
						ON loc.restaurant_id=rest.restaurant_id
					LEFT JOIN CuisineType ct
						ON ct.cuisine_id=rest.cuisine
					LEFT JOIN MenuItem item
						ON rest.restaurant_id=item.restaurant_id
					LEFT JOIN Rating rate
						ON loc.location_id=rate.location_id
					WHERE ";
				if (strlen($cui) > 0) {
					$aQuery.="ct.description='$cui' AND";
				}
				$aQuery.=" (ct.description ~* '%*$query%*'
						OR rest.name ~* '%*$query%*'
						OR item.name ~* '%*$query%*'
						OR rest.url ~* '%*$query%*'";
				foreach($exQuery as $queryTerm) {
					$aQuery.=" OR ct.description ~* '%*$queryTerm%*'";
					$aQuery.=" OR rest.name ~* '%*$queryTerm%*'";
					$aQuery.=" OR item.name ~* '%*$queryTerm%*'";
					$aQuery.=" OR rest.url ~* '%*$queryTerm%*'";
				}
				$aQuery.=") 
				GROUP BY loc.location_id, rest.name";

				switch($sort) {
					case 'rating': default: $aQuery.=' ORDER BY rateAvg DESC, idCount DESC, rest.name'; break;
					case 'rel': $aQuery.=' ORDER BY idCount DESC, rateAvg DESC, rest.name'; break;
					case 'alpha': $aQuery.=' ORDER BY rest.name, rateAvg DESC, idCount DESC'; break;
				}
			}
			$result = pg_query($aQuery);
			$count = pg_num_rows($result);
			
			echo "	
				<h2 class='text-center text-info' style='margin-bottom:20px'>
					<strong>$count</strong> restaurants found for \"$gQuery\" 
				</h2>
				";
			while($res = pg_fetch_assoc($result)){
				$rateAvg = round($res['rateavg'], 1);
				$location_id = $res['location_id'];
				$q1 = pg_query(
					"SELECT * FROM Restaurant R 
					INNER JOIN Location L 
						ON L.restaurant_id=R.restaurant_id 
					WHERE L.location_id = $location_id");
				$tmp = pg_fetch_assoc($q1);
				$name = $tmp['name'];
				$url = $tmp['url'];
				$address = $tmp['street_address'];
				$open = $tmp['hour_open'];
				while (strlen($open) < 4) {
					$open = "0".$open;
				}
				$open = substr_replace($open, ":", strlen($open)-2, 0);
				$close = $tmp['hour_close'];
				while (strlen($close) < 4) {
					$close = "0".$close;
				}
				$close = substr_replace($close, ":", strlen($close)-2, 0);
				$cuisine = $tmp['cuisine'];

				$q1 = pg_query("SELECT description FROM CuisineType WHERE cuisine_id = $cuisine");
				$q1 = pg_fetch_assoc($q1);

				$cuisine = $q1['description'];

				echo "
					<!-- Results for all restaurants matching query -->
					<div class='well well-sm' style='line-height:1.75; font-size:16px'>
						<strong><a href='restaurant.php?id=$location_id'>$name</a></strong><br>
						<a href = 'results.php?query=$cuisine&cui=$cuisine'>$cuisine</a><br>
						$address <br>
						$open - $close <br>
						<strong>Average Rating:</strong> $rateAvg
					</div>";
			}
				?>	
				<!-- ADD RESTAURANT bottom right -->
			<div class="pull-right">
				<strong>Can't find the restaurant you're looking for?
				<a href="add-restaurant.php">
				<button  name = "add-item" method  = "post"  type="add-item" class="btn btn-primary" style="margin-left:10px">
					<span class="glyphicon glyphicon-plus" style="margin-right:10px"></span>Add a Restaurant</strong>
				</button></a>
			</div>
		</div>
	</div>
</div>

</body>
</html>