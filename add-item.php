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
	<?php $page_title = "Add Menu Item" ?>

	<?php include("includes/resources.php");?>
</head>

<body>
<div class="container">
	<div class="row clearfix">
		<?php include("includes/header.php");?>
		<?php include("includes/navbar.php");?>
			<h2 class="text-center text-info" style="margin-bottom:20px">
			<?php
				require('connect.php');
				$id = $_GET['id'];
				$rName = pg_query("SELECT R.name FROM Restaurant R, Location L WHERE L.location_id = $id AND L.restaurant_id = R.restaurant_id");
				$rName = pg_fetch_assoc($rName);
				$rName = $rName['name'];
			
				echo"Add a new menu item for <strong>$rName</strong>";
			?>
			</h2>
			
			<div class="col-md-12 column">
				<form id="formID" name="formID" method="post" action="" role="form">
					<!-- ITEM NAME -->
					<div class="row">
						<div class="form-group-xs">
							<label for="input-name">Item Name</label>
							<input name ="input-name" type="name" class="form-control" id="input-name" required autofocus/>
						</div>
					</div>
					<!-- PRICE -->
					<div class="row">
						<div class="form-group-xs">
							<label for="input-price">Price ($)</label>
							<input name ="input-price" type="name" class="form-control" id="input-price" placeholder="12.50" required />
						</div>
					</div>
					<!-- FOOD TYPE -->
					<div class="row">
						<div class="form-group-xs">
							<label for="input-description">Description</label>
							<input name ="input-description" type="name" class="form-control" id="input-description" placeholder="Describe this item!" required />
							
						</div>
					</div>
						<div class="row">
						<div class="form-group-xs">
							<!-- FETCH ALL POSSIBLE CUISINE TYPES IN HERE -->
							<label for="input-type">Type of Food</label>
							<select name = "input-type" id = "input-type" method= "post" class="form-control">
								<option>Other</option>
								<option>Appetizer</option>
								<option>Entree</option>
								<option>Dessert</option>
								<option>Beverage</option>
								<option>Alcoholic</option>
							</select>
						</div>
					</div>
					
					<div class="text-center" style="margin-top:20px">
						<button name="add-item" id="add-item" type="submit" class="btn btn-primary"><strong>Add Menu Item</strong></button>
					</div>
				</form>

				<?php 
				if(array_key_exists('input-name', $_POST) && array_key_exists('input-type', $_POST) 
					&& array_key_exists('input-price', $_POST) && array_key_exists('input-description', $_POST)){
					$iName = $_POST['input-name'];
					$type = $_POST['input-type'];
					if($type == "Other")
						$type = 0;
					else if($type == "Appetizer")
						$type = 1;
					else if($type == "Entree")
						$type = 2;
					else if($type == "Dessert")
						$type = 3;
					else if($type == "Beverage")
						$type = 4;
					else if($type == "Alcoholic")
						$type = 5;
					$description = $_POST['input-description'];
					$price = $_POST['input-price'];
					$location_id = $_GET['id'];
					require('connect.php');
					$result = pg_query("SELECT * FROM Location L WHERE L.location_id = $location_id;");
					$result = pg_fetch_assoc($result);
					$rId = $result['restaurant_id'];

					$result = pg_query("SELECT * FROM MenuItem MI WHERE MI.restaurant_id = $rId AND 
						MI.name = '$iName'");
					$num = pg_num_rows($result);

					if($num == 0){
						$result = pg_query("INSERT INTO MenuItem(name, type_id, description, price, restaurant_id)
							VALUES('$iName', $type, '$description', $price, $rId);");
						echo "Sucess!";
					}
					else {
						echo "That item already exists!";
					}
					
				}
				?>
				
			</div>
	</div>
</div>
</body>
</html>