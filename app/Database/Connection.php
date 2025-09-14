<?php

namespace StorageUnit\Database;

if (!class_exists('Connection')) {
class Connection {
    // function __construct(){

    // }

    function getConnection(){
        // Check if running in Docker environment
        $isDocker = getenv('DOCKER_ENV') === 'true' || file_exists('/.dockerenv');
        
        if ($isDocker) {
            // Docker configuration
            $user = 'root';
            $pass = 'rootpassword';
            $dbname = 'storageunit';
            $host = 'db'; // Docker service name
        } else {
            // Local configuration
            $user = 'root';
            $pass = '';
            $dbname = 'storageunit';
            $host = 'localhost';
        }
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
}
