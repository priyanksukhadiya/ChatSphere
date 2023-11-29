<?php

// Create a function to handle database connection
function connectToDatabase()
{
    $hostname = 'localhost';
    $username = 'root'; // Replace with your actual database username
    $password = ''; // Replace with your actual database password
    $database = 'chatgroup';

    // Create a new MySQLi instance
    $connection = new mysqli($hostname, $username, $password, $database);

    // Check the connection
    if ($connection->connect_error) {
        die('Connection failed: ' . $connection->connect_error);
    }

    // Set the character set
    $connection->set_charset('utf8mb4');

    // Create the 'messages' table if it doesn't exist
    $query = "
            CREATE TABLE IF NOT EXISTS `messages` (
                `id` int(255) NOT NULL AUTO_INCREMENT,
                `sender_id` varchar(50) NOT NULL,
                `message_text` varchar(255) NOT NULL,
                `timestamp` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
                PRIMARY KEY (`id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
            ";

    if ($connection->query($query) === false) {
        echo 'Error creating table: ' . $connection->error;
    }

    return $connection;
}

// Get the database connection
$connection = connectToDatabase();
?>
