<?php
declare (strict_types = 1);
class Item
{
    private $id;
    private $title;
    private $description;
    private $user_id;
    private $qty;
    private $img;
    protected $db;

    public function __construct(string $title, string $description, int $qty, int $user_id, $img)
    {
        $this->title = $title;
        $this->description = $description;
        $this->qty = $qty;
        $this->user_id = $user_id;
        $this->img = $img;
    }

    public function setDb($db)
    {
        $this->db = $db;
    }

    public function add():bool
    {
        $conexion =  $this->db->getConnection();
        //Coverting the first letter to Uppercase
        $description = ucfirst($this->description);
        $sql = $conexion->prepare("INSERT INTO items (title, description, qty, user_id, img) VALUES (:title, :description, :qty, :user_id , :img)");
        $sql->bindParam(':title', $this->title);
        $sql->bindParam(':description', $this->description);
        $sql->bindParam(':qty', $this->qty);
        $sql->bindParam(':user_id', $this->user_id);
        $sql->bindParam(':img', $this->img);
        //execute return TRUE or FALSE after calling the DB server
        return $sql->execute();

    }

    public function edit($id):bool
    {
        $conexion = $this->db->getConnection();
        $sql = $conexion->prepare("UPDATE items SET title = :title, description = :description, qty = :qty, user_id = :user_id, img = :img  WHERE id = :id");
        $sql->bindParam(':title', $this->title);
        $sql->bindParam(':description', $this->description);
        $sql->bindParam(':qty', $this->qty);
        $sql->bindParam(':user_id', $this->user_id);
        $sql->bindParam(':img', $this->img);
        $sql->bindParam(':id', $id);
        //execute return TRUE or FALSE after calling the DB server
        return $sql->execute();
    }

    public static function delete(int $id, $conn):bool
    {
        $conexion = $conn->getConnection();
        $sql = $conexion->prepare('DELETE FROM items WHERE id = :id');
        $sql->bindParam(':id', $id);
        return $sql->execute();
    }

    public static function getItemById(int $id, $conn):array
    {
        $conexion = $conn->getConnection();
        $sql = $conexion->prepare('SELECT * FROM items WHERE id = :id');
        $sql->bindParam(':id', $id);
        $sql->execute();
        return $sql->fetchAll();
    }

    public static function getItemByTitle(string $title, int $user_id, $conn):array
    {
        $conexion = $conn->getConnection();
        $sql = $conexion->prepare('SELECT * FROM items WHERE title = :title AND user_id = :user_id');
        $sql->bindParam(':title', $title);
        $sql->bindParam(':user_id', $user_id);
        $sql->execute();
        return $sql->fetchAll();
    }

    public static function getAllItems(int $user_id, $conn):array
    {
        $conexion = $conn->getConnection();
        $sql = $conexion->prepare('SELECT * FROM items WHERE user_id = :user_id ORDER BY id DESC');
        $sql->bindParam(':user_id', $user_id);
        $sql->execute();
        return $sql->fetchAll();
    }

    public static function getItemsAmountTotal(int $user_id, $conn):int
    {
        $conexion = $conn->getConnection();
        $sql = $conexion->prepare('SELECT qty FROM items WHERE user_id = :user_id');
        $sql->bindParam(':user_id', $user_id);
        $sql->execute();
        //Returns a numeric array where the key represent the field returned from the DB
        return array_sum($sql->fetchAll(PDO::FETCH_COLUMN));
    }

}
