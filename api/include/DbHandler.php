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

    /**
     * Creating new user
     * @param String $email User login email id
     * @param String $password User login password
     */
    /*public function createUser($email, $password) {
        require_once 'PassHash.php';
        $response = array();

        // First check if user already existed in db
        if (!$this->isUserExists($email)) {
            // Generating password hash
            $password_hash = PassHash::hash($password);

            // Generating API key
            $api_key = $this->generateApiKey();

            // insert query
            $stmt = $this->conn->prepare("INSERT INTO users(email, password_hash, api_key, status, profile_created) values(?, ?, ?, 1, 0)");
            $stmt->bind_param("sss", $email, $password_hash, $api_key);

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
        } else {
            // User with same email already existed in the db
            return USER_ALREADY_EXISTED;
        }

        return $response;
    }*/

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

    /**
     * Checking user login
     * @param String $email User login email id
     * @param String $password User login password
     * @return boolean User login status success/fail
     */
    public function checkLogin($email, $password) {
        // fetching user by email
        $stmt = $this->conn->prepare("SELECT password_hash FROM users WHERE email = ?");

        $stmt->bind_param("s", $email);

        $stmt->execute();

        $stmt->bind_result($password_hash);

        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            // Found user with the email
            // Now verify the password

            $stmt->fetch();

            $stmt->close();

            if (PassHash::check_password($password_hash, $password)) {
                // User password is correct
                return TRUE;
            } else {
                // user password is incorrect
                return FALSE;
            }
        } else {
            $stmt->close();

            // user not existed with the email
            return FALSE;
        }
    }

    /**
     * Checking for duplicate user by email address
     * @param String $email email to check in db
     * @return boolean
     */
    private function isUserExists($email) {
        $stmt = $this->conn->prepare("SELECT id from users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->store_result();
        $num_rows = $stmt->num_rows;
        $stmt->close();
        return $num_rows > 0;
    }

    /**
     * Fetching user by email
     * @param String $email User email id
     */
    public function getUserByEmail($email) {
        $stmt = $this->conn->prepare("SELECT email, api_key, status, created_at, profile_created FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        if ($stmt->execute()) {
            $user = $stmt->get_result()->fetch_assoc();
            $stmt->close();
            return $user;
        } else {
            return NULL;
        }
    }

    /**
     * Fetching user api key
     * @param String $user_id user id primary key in user table
     */
    public function getApiKeyById($user_id) {
        $stmt = $this->conn->prepare("SELECT api_key FROM users WHERE id = ?");
        $stmt->bind_param("i", $user_id);
        if ($stmt->execute()) {
            $api_key = $stmt->get_result()->fetch_assoc();
            $stmt->close();
            return $api_key;
        } else {
            return NULL;
        }
    }

    /**
     * Fetching user id by api key
     * @param String $api_key user api key
     */
    public function getUserId($api_key) {
        $stmt = $this->conn->prepare("SELECT id FROM users WHERE api_key = ?");
        $stmt->bind_param("s", $api_key);
        if ($stmt->execute()) {
            $user_id = $stmt->get_result()->fetch_assoc();
            $stmt->close();
            return $user_id;
        } else {
            return NULL;
        }
    }

    /**
     * Validating user api key
     * If the api key is there in db, it is a valid key
     * @param String $api_key user api key
     * @return boolean
     */
    public function isValidApiKey($api_key) {
        $stmt = $this->conn->prepare("SELECT id from users WHERE api_key = ?");
        $stmt->bind_param("s", $api_key);
        $stmt->execute();
        $stmt->store_result();
        $num_rows = $stmt->num_rows;
        $stmt->close();
        return $num_rows > 0;
    }

    /**
     * Generating random Unique MD5 String for user Api key
     */
    private function generateApiKey() {
        return md5(uniqid(rand(), true));
    }

    /* ----------- Authenticated User methods ----------- */

    /**
     * Creating new user profile
     * @param String $user_id user id to whom task belongs to
     * @param String $task task text
     */
    public function createProfile($user_id, $name, $unit, $city, $state, $country) {
        if (!$this->isProfileExists($user_id)) {
            $stmt = $this->conn->prepare("INSERT INTO userprofiles(id, name, unit, city, state, country) VALUES(?,?,?,?,?,?)");
            $stmt->bind_param("ssssss", $user_id, $name, $unit, $city, $state, $country);
            $result = $stmt->execute();
            $stmt->close();

            if ($result) {
                // User successfully inserted
                $stmt = $this->conn->prepare("UPDATE users SET profile_created=1 WHERE id=?");
                $stmt->bind_param("s", $user_id);
                $stmt->execute();
                $stmt->close();
                return USERPROFILE_CREATED_SUCCESSFULLY;
            } else {
                // Failed to create user
                return USERPROFILE_CREATE_FAILED;
            }
        } else {
            return USERPROFILE_ALREADY_EXISTED;
        }
    }

    /**
     * Checking for duplicate profile by id
     * @param int id
     * @return boolean
     */
    private function isProfileExists($user_id) {
        $stmt = $this->conn->prepare("SELECT name from userprofiles WHERE id = ?");
        $stmt->bind_param("s", $user_id);
        $stmt->execute();
        $stmt->store_result();
        $num_rows = $stmt->num_rows;
        $stmt->close();
        return $num_rows > 0;
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
