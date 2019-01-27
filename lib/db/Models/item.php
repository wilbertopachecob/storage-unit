<?php
declare (strict_types = 1);
if (!isFileIncluded('connection.php')) {
    include 'db/connection.php';
}
class Item
{
    private $id;
    private $title;
    private $description;
    private $user_id;
    private $qty;
    private $img;

    public function __construct(string $title, string $description, int $qty, int $user_id, $img)
    {
        $this->title = $title;
        $this->description = $description;
        $this->qty = $qty;
        $this->user_id = $user_id;
        $this->img = $img;
    }

    public static function addItem($title, $description, $qty, $user_id, $img):bool
    {
        $conn = new Connection();
        $conexion = $conn->getConnection();
        //Coverting the first letter to Uppercase
        $description = ucfirst($description);
        $sql = $conexion->prepare("INSERT INTO items (title, description, qty, user_id, img) VALUES (:title, :description, :qty, :user_id , :img)");
        $sql->bindParam(':title', $title);
        $sql->bindParam(':description', $description);
        $sql->bindParam(':qty', $qty);
        $sql->bindParam(':user_id', $user_id);
        $sql->bindParam(':img', $img);
        //execute return TRUE or FALSE after calling the DB server
        return $sql->execute();

    }

    public static function editItem($id, $title, $description, $qty, $user_id, $img):bool
    {

        $conn = new Connection();
        $conexion = $conn->getConnection();
        $sql = $conexion->prepare("UPDATE items SET title = :title, description = :description, qty = :qty, user_id = :user_id, img = :img  WHERE id = :id");
        $sql->bindParam(':title', $title);
        $sql->bindParam(':description', $description);
        $sql->bindParam(':qty', $qty);
        $sql->bindParam(':user_id', $user_id);
        $sql->bindParam(':img', $img);
        $sql->bindParam(':id', $id);
        //execute return TRUE or FALSE after calling the DB server
        return $sql->execute();
    }

    public function deleteItem(int $id):bool
    {
        $conn = new Connection();
        $conexion = $conn->getConnection();
        $sql = $conexion->prepare('DELETE FROM items WHERE id = :id');
        $sql->bindParam(':id', $id);
        return $sql->execute();
    }

    public static function getItemById(int $id)
    {
        $conn = new Connection();
        $conexion = $conn->getConnection();
        $sql = $conexion->prepare('SELECT * FROM items WHERE id = :id');
        $sql->bindParam(':id', $id);
        $sql->execute();
        return $sql->fetchAll();
    }

    public function getItemByTitle(string $title, int $user_id)
    {
        $conn = new Connection();
        $conexion = $conn->getConnection();
        $sql = $conexion->prepare('SELECT * FROM items WHERE title = :title AND user_id = :user_id');
        $sql->bindParam(':title', $title);
        $sql->bindParam(':user_id', $user_id);
        $sql->execute();
        return $sql->fetchAll();
    }

    public static function getAllItems($user_id)
    {
        $conn = new Connection();
        $conexion = $conn->getConnection();
        $sql = $conexion->prepare('SELECT * FROM items WHERE user_id = :user_id ORDER BY id DESC');
        $sql->bindParam(':user_id', $user_id);
        $sql->execute();
        return $sql->fetchAll();
    }

}

// $item = new Item('maller', 'just a maller', 5, null);
// echo var_dump($item->getItemById(1));
// echo var_dump($item->editItem(1, 'maller', 'just a maller', 5, null)). PHP_EOL;
// echo 'Element Added'.var_dump($item->addItem('saw saw', 'broken saw saw', 1, 'saw_saw.jpg')).PHP_EOL;

