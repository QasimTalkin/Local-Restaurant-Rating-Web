<?php
    header('Content-Type: application/json');
    require('connect.php');

    $aResult = array();

    if( !isset($_POST['functionname']) ) { $aResult['error'] = 'No function name!'; }

    if( !isset($_POST['arguments']) ) { $aResult['error'] = 'No function arguments!'; }

    if( !isset($aResult['error']) ) {

        switch($_POST['functionname']) {
            case 'deleteRestaurant':
               if( !is_array($_POST['arguments']) || (count($_POST['arguments']) < 1) ) {
                   $aResult['error'] = 'Error in arguments!';
               }
               else {
               		$id = $_POST['arguments'][0];
               		$restId = pg_query("SELECT loc.restaurant_id FROM Location loc WHERE loc.location_id=$id");
               		$restId = pg_fetch_array($restId);
               		$restId = intval($restId[0]);

               		$query = "DELETE FROM Location loc WHERE loc.location_id=$id";
                   	pg_query($query);

                   	$query = "SELECT * FROM Location loc where loc.restaurant_id=$restId";
                   	$result = pg_query($query);
                   	$remainingCount = 0;
                   	while ($res = pg_fetch_array($result)) {
                   		$remainingCount += 1;
                   	}
                   	$aResult['result'] = $remainingCount;
                   	if ($remainingCount == 0) {
                   		pg_query("DELETE FROM Restaurant rest WHERE rest.restaurant_id=$restId");
                   	}
               }
               break;

            default:
               $aResult['error'] = 'Not found function '.$_POST['functionname'].'!';
               break;
        }

    }
    echo json_encode($aResult);
?>