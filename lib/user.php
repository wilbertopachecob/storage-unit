<?php
declare (strict_types = 1);
if (!isFileIncluded('connection.php')) {
    include 'db/connection.php';
}
class User
{
    private $id;
    private $email;
    private $name;
    private $password;
    private $user;

    public function __construct(string $email, string $password, $name)
    {
        $this->email = $email;
        $this->password = $password;
        $this->name = $name;
    }

    public function addUser(): bool
    {
        $conn = new Connection();
        $conexion = $conn->getConnection();
        $hash = password_hash($this->password, PASSWORD_BCRYPT);
        if (!$this->ifExistsEmail($this->email)) {
            $sql = $conexion->prepare("INSERT INTO users (email, password, name) VALUES (:email, :password, :name)");
            $sql->bindParam(':email', $this->email);
            $sql->bindParam(':password', $hash);
            $sql->bindParam(':name', $this->name);
            if ($sql->execute()) {
                $_SESSION['user_id'] = $conexion->lastInsertId();
                return true;
            }
            return false;
        } else {
            throw new Exception('This email already exist on our Database.');
        }

    }
    public function login()
    {
        $user = $this->_checkCredentials();
        if ($user) {
            $this->user = $user; // store it so it can be accessed later
            $_SESSION['user_id'] = $user['id'];
            return $user['id'];
        }
        return false;
    }

    public static function logout()
    {
        session_start();
        unset($_SESSION["user_id"]);
        session_destroy();
        session_write_close();
        //return $_SESSION["user_id"];
    }

    public static function ifExistsEmail(string $email): bool
    {
        $conn = new Connection();
        $conexion = $conn->getConnection();
        $sql = $conexion->prepare("SELECT * FROM users WHERE email = :email ");
        $sql->bindParam(':email', $email);
        $sql->execute();
        //Count the number of returned rows from the query and cast into bool value
        return boolval($sql->rowCount());
    }

    protected function _checkCredentials()
    {
        $conn = new Connection();
        $conexion = $conn->getConnection();
        $sql = $conexion->prepare('SELECT * FROM users WHERE email=?');
        $sql->execute(array($this->email));
        if ($sql->rowCount() > 0) {
            $user = $sql->fetch(PDO::FETCH_ASSOC);
            $submitted_pass = password_verify($this->password, $user['password']);
            if ($submitted_pass) {
                return $user;
            }
        }
        return false;
    }

    public function getUser()
    {
        return $this->user;
    }
}
