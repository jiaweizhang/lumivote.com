<?php

/**
 * Class to handle all db operations
 * This class will have CRUD methods for database tables
 *
 * @author Ravi Tamada
 * @link URL Tutorial link
 */
class DbHandler {

    private $conn;

    function __construct() {
        require_once dirname(__FILE__) . '/DbConnect.php';
        //require_once 'DbConnect.php';
        // opening db connection
        $db = new DbConnect();
        $this->conn = $db->connect();
    }

    /* ------------- `users` table method ------------------ */

    public function createUser($user)
    {
        require_once 'PassHash.php';
        $username = $user['username'];
        $email = $user['email'];
        $password = $user['password'];
        // Generating password hash
        $password_hash = PassHash::hash($password);

        // insert query
        $stmt = $this->conn->prepare("INSERT INTO users(username, email, password_hash) values(?, ?, ?)");
        $stmt->bind_param("sss", $username, $email, $password_hash);

        $result = $stmt->execute();

        $stmt->close();

        // Check for successful insertion
        if ($result) {
            // User successfully inserted
            return USER_CREATED_SUCCESSFULLY;
        } else {
            // Failed to create user
            return USER_CREATE_FAILED;
        }
    }



    /* --------------------- Non-authenticated methods ---------- */
    /**
     * Fetching all events
     */
    public function getEvents() {
        $stmt = $this->conn->prepare("SELECT * FROM timeline");
        if ($stmt->execute()) {
            $timeline = $stmt->get_result();
            $stmt->close();
            return $timeline;
        } else {
            return NULL;
        }
    }

    /**
     * Fetching all candidates or by party
     */
    public function getCandidates($param) {
        if ($param == NULL) {
            $sql = "SELECT * FROM candidates";
        } else if (strcmp($param, "democratic") == 0) {
            $sql = "SELECT * FROM candidates WHERE party='Democratic'";
        } else if (strcmp($param, "republican") == 0) {
            $sql = "SELECT * FROM candidates WHERE party='Republican'";
        } else if (strcmp($param, "independent") == 0) {
            $sql = "SELECT * FROM candidates WHERE party<>'Republican' AND party <>'Democratic'";
        } else {
            return NULL;
        }
        $stmt = $this->conn->prepare($sql);
        if ($stmt->execute()) {
            $candidates = $stmt->get_result();
            $stmt->close();
            return $candidates;
        } else {
            return NULL;
        }
    }

    /**
     * Fetching candidate by ID
     */
    public function getCandidateById($candidateId) {
        $stmt = $this->conn->prepare("SELECT * from candidates WHERE ID=?");
        $stmt->bind_param("i", $candidateId);
        if ($stmt->execute()) {
            $candidate = $stmt->get_result()->fetch_assoc();
            $stmt->close();
            return $candidate;
        } else {
            return NULL;
        }
    }
}

?>
