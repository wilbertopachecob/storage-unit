<?php
header('Content-type: application/json');
session_start();

require 'lib/db/connection.php';
//$user_id = $_POST['user_id'];
$user_id = $_SESSION['user_id'];
$searchTerm = $_POST['searchTerm'];
$conn = new \StorageUnit\Database\Connection;
$conexion = $conn->getConnection();

/**
 * Doing a LIKE inside PDO is complicated when you want to allow a 
 * literal % or _ character in the search string, without having it act as a 
 * wildcard. More info here https://stackoverflow.com/questions/583336/how-do-i-create-a-pdo-parameterized-query-with-a-like-statement
 */
$sql = $conexion->prepare('SELECT * FROM items WHERE user_id = ? AND title LIKE ? ORDER BY id DESC');
$sql->execute(array( 
    $user_id,
    "%$searchTerm%"
));
$items = $sql->fetchAll(PDO::FETCH_ASSOC);
echo json_encode($items);
