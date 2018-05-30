<?php

	//code to conect to database
	$link = pg_connect("host=web0.site.uottawa.ca port=15432 dbname=mshan072 user=mshan072 password=\$Hanti1095");

	if(!$link){
	  die('Could not connect: ' . pg_last_error());
	}
	//Select the database to run the queries on
	$query = "set search_path = 'project'";

	//code to excecute query
	pg_query($query);

	//echo "The current date is $date";

?> 