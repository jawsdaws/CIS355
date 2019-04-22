<?php
/* ---------------------------------------------------------------------------
 * filename    : api.php
 * author      : James Daws	
 * description : This program returns a JSON of the entire items database.
 * ---------------------------------------------------------------------------
 */
require "database.php";

$pdo = Database::connect();
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
$sql = "SELECT *
		FROM lrp_items
        NATURAL JOIN lrp_stores
        NATURAL JOIN lrp_companies";
header('Content-Type: application/json; charset=utf-8');

$data = $pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);

echo json_encode($data);
Database::disconnect();
?>