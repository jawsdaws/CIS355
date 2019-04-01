<?php
/* ---------------------------------------------------------------------------
 * filename    : api.php
 * author      : James Daws	
 * description : This program returns a JSON of the entire database.
 * ---------------------------------------------------------------------------
 */
require "database.php";

$pdo = Database::connect();
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
$sql = "SELECT *
		FROM lrp_items
        NATURAL JOIN lrp_stores
        NATURAL JOIN lrp_companies";
foreach ($pdo->query($sql) as $row) {
	echo json_encode($row);
}

Database::disconnect();
?>