<?php
/**
 * items
 * php version 7.2.10
 *
 */
session_start();

//Controls the session.  If
if(!isset($_SESSION["tJHSQRuoNnWUwLRe"])) { // if "user" not set,
    session_destroy();
    header('Location: login.php');     // go to login page
    exit();
}

// include the class that handles database connections
require "database.php";

// include the class containing functions/methods for "item" table
require "items.class.php";

$item = new Items();

if(isset($_SESSION["email"])) {
    $item->username = $_SESSION["email"];
}

// set active record field values, if any
// (field values not set for display_list and display_create_form)
if (isset($_GET["id"])) {
    $id = $_GET["id"];
}
if (isset($_POST["Item"])) {
    $item->item_name = htmlspecialchars($_POST["Item"]);
}
if (isset($_POST["Price"])) {
    $item->item_price = htmlspecialchars($_POST["Price"]);
}
if (isset($_POST["Quantity"])) {
    $item->item_quantity = htmlspecialchars($_POST["Quantity"]);
}
if (isset($_POST["store_id"])) {
    $item->store_id = htmlspecialchars($_POST["store_id"]);
}
if (isset($_POST["Quantity_Unit"])) {
    $item->item_quantity_unit = htmlspecialchars($_POST["Quantity_Unit"]);
}
if (isset($_POST["password"])) {
    $item->password = $_POST["password"];
}

// "fun" is short for "function" to be invoked
if (isset($_GET["fun"])) {
    $fun = $_GET["fun"];
} else {
    $fun = "display_list";
}

// This switch uses the get data returned from the server to decide
// which method to call from the items class.
switch ($fun) {
case "join":
    $item->joinForm();
    break;
case "logout":
    $item->logout();
    break;
case "display_list":
    $item->list_records();
    break;
case "display_create_form":
    $item->create_record();
    break;
case "display_update_form":
    $item->update_record($id);
    break;
case "display_delete_form":
    $item->delete_record($id);
    break;
case "insert_db_record":
    $item->insert_db_record();
    break;
case "update_db_record":
    $item->update_db_record($id);
    break;
case "delete_db_record":
    $item->delete_db_record($id);
    break;
default:
    echo "Error: Invalid function call (item.php)";
    exit();
    break;
}
