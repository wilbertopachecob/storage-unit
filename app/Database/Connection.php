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
            // Local configuration - connect to Docker database
            $user = 'root';
            $pass = 'rootpassword';
            $dbname = 'storageunit';
            $host = '127.0.0.1'; // Use 127.0.0.1 instead of localhost for Docker port forwarding
        }
        try{
            $connection = new \PDO("mysql:host=$host;dbname=$dbname", $user, $pass);
        }
        catch (\PDOException $e) {
            print "Error!: " . $e->getMessage() . "<br/>";
            die();
        }

        return $connection;

    }
}
}
