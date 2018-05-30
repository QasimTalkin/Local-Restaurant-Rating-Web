<?php 
	session_start();
	$name = "";
	$userid = "";
	if(array_key_exists('name', $_SESSION) && array_key_exists('userid', $_SESSION)){
		$name = $_SESSION['name'];
		$userid = $_SESSION['userid'];
	}
		
?>


<?php
	session_destroy();
?>
<script>
	window.location.href = "index.php";
</script>