<?php

require_once ("deployment_params.inc");

// Some variables used throughout the script.
$fileName = dirname (__FILE__) . "/tables.sql";

// Get the file contents and replace placeholders with provided values!
$fileStr = file_get_contents($fileName);
$fileStr = str_replace("ADMIN_EMAIL_PLACEHOLDER", $adminEmail, $fileStr);

// connect to mysql
$link = mysqli_connect( $dbHost, $dbUsername, $dbPassword );
mysqli_select_db($link, $dbName);

$queries = explode(";", $fileStr);
foreach ( $queries as $id => $query ) {

	if ($query != '') {

		$result = mysqli_query ( $link, $query );
		if (! $result) {
			echo ( "Invalid query [$query]: " . mysqli_error ($link) );
			die (1 );
		}
		;

	}
}

mysqli_close($link);
echo "Imported database structure to $dbHost." . PHP_EOL;

?>