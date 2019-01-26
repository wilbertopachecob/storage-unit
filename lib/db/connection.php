<?php

class Connection {
    // function __construct(){

    // }

    function getConnection(){
        $user = 'root';
        $pass = '';
        $dbname = 'storageunit';
        $host = 'localhost';
        try{
            $connection = new PDO("mysql:host=$host;dbname=$dbname", $user, $pass);
        }
        catch (PDOException $e) {
            print "Error!: " . $e->getMessage() . "<br/>";
            die();
        }

        return $connection;

    }
}
