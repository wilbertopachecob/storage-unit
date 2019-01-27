<?php
//echo realpath("./lib/db/Models");
require "./lib/db/Models/item.php";
class ItemController
{
    private $user_id;
    private $items = [];
    private $connection;

    public function __construct($conn, $user_id)
    {
        $this->user_id = $user_id;
        $this->connection = $conn;
    }

    public function getAllItems()
    {
        $conexion = $this->connection->getConnection();
        $sql = $conexion->prepare('SELECT * FROM items WHERE user_id = :user_id ORDER BY id DESC');
        $sql->bindParam(':user_id', $user_id);
        $sql->execute();
        $this->items = $sql->fetchAll();
        return $this->items;
    }
}

//$controller = new ItemController();
