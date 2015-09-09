<?php

/**
 * Handling database connection
 *
 * @author Ravi Tamada
 * @link URL Tutorial link
 */
class DbConnect {

    private $conn;

    function __construct() {        
    }

    /**
     * Establishing database connection
     * @return database connection handler
     */
    function connect() {
        include_once dirname(__FILE__) . '/Config.php';
        //include_once 'Config.php';

        // Connecting to mysql database
        $this->conn = new mysqli(null, DB_USERNAME, DB_PASSWORD, DB_NAME, null, DB_UNIX);
        /*try {
        	$this->conn = new pdo(DB_PDO, DB_USERNAME, DB_PASSWORD);
        } catch (PDOException $e) {
        	echo "Error!: " . $e->getMessage() ."<br/>";
        }*/
        // Check for database connection error
        if (mysqli_connect_errno()) {
            echo "Failed to connect to MySQL: " . mysqli_connect_error();
        }

        // returing connection resource
        return $this->conn;
    }

}

?>
