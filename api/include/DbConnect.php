<?php

/**
 * Handling database connection
 *
 * @author Ravi Tamada
 * @link URL Tutorial link
 */
class DbConnect {

    private $conn;
    private $conn2;

    function __construct() {        
    }

    /**
     * Establishing database connection
     * @return database connection handler
     */
    function connect() {
        include_once dirname(__FILE__) . '/Config.php';

        // Connecting to mysql database
        $this->conn = new mysqli(null, DB_USERNAME, DB_PASSWORD, DB_NAME, null, DB_UNIX);

        // Check for database connection error
        if (mysqli_connect_errno()) {
            echo "Failed to connect to MySQL: " . mysqli_connect_error();
        }

        // returing connection resource
        return $this->conn;
    }

    function connect2() {
        include_once dirname(__FILE__) . '/Config.php';

        // Connecting to mysql database
        $this->conn = new mysqli(null, DB_USERNAME, DB_PASSWORD, DB_NAME, null, DB_UNIX);

        $this->conn2 = new pdo(DB_PDO,
            DB_USERNAME,  // username
            DB_PASSWORD       // password
        );



        // returing connection resource
        return $this->conn2;
    }


}

?>
