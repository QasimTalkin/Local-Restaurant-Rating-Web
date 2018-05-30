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
	<?php $page_title = "About" ?>

	<?php include("includes/resources.php");?>
</head>

<body>
<div class="container">
	<div class="row clearfix">
		<?php include("includes/header.php");?>
		<?php include("includes/navbar.php");?>
		<div class="col-md-12 column">
		
			<h2 class="text-center text-info">
					About the team
			</h2>
			
			<div class="the-team">
				
				<!-- James -->
				<div class="col-md-4">
					<div class="thumbnail" style="height:500px">
					<a href="https://github.com/jreinlein">
					<div class="cropped-img" style="background-image:url('media/james.png'); min-height:300px" /> </div>

					<div class="caption">
						<h2>
							James Reinlein</a>
						</h2>
						<p>
							A natural-born artist among engineers, you can thank James for the beautiful website you see in front of your eyes. In charge of the front-end, he built this website from scratch (not really, but we can pretend). 
						</p>
					</div>
				</div>
			</div>
				<!-- Joseph -->
				<div class="col-md-4">
					<div class="thumbnail" style="height:500px">
					<a href="https://github.com/josephroque">
					<div class="cropped-img" style="background-image:url('media/joseph.png'); min-height:300px" /> </div>

					<div class="caption">
						<h2>
							Joseph Roque</a>
						</h2>
						<p>
							Master of the back-end, Joseph commandeered the mission to create and maintain the database. It is rumoured he once created an Android app in one straight sitting without eating or going to the bathroom. 
						</p>
					</div>
				</div>
				</div>
				<!-- Mohammed -->
				<div class="col-md-4">
					<div class="thumbnail" style="height:500px">
					<a href="https://github.com/mshanti">
					<div class="cropped-img" style="background-image:url('media/mohammed.png'); min-height:300px"/> </div>
						
					<div class="caption">
						<h2>
							Mohammed Shanti</a>
						</h2>
						<p>
							Also known as <i>Habibi</i>, Mohammed is Sizzl's glue. Responsible for connecting the database to the front-end, his knowledge of PHP is impressive. Unfortunately, his comfort with commenting isn't quite up to par.
						</p>

					</div>
				</div>
	</div>
</div>

</body>
</html>
