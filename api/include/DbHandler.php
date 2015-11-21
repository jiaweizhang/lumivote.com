<?php

/**
 * Class to handle all db operations
 * This class will have CRUD methods for database tables
 *
 * @author Ravi Tamada
 * @link URL Tutorial link
 */
class DbHandler
{

    private $conn;

    function __construct()
    {
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
    public function getEvents()
    {
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
    public function getCandidates($param)
    {
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
    public function getCandidateById($candidateId)
    {
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

    public function createQuestion($input)
    {
        $author = $input['author'];
        $question = $input['question'];
        $answers = $input['answers'];

        $stmt = $this->conn->prepare("INSERT INTO questions(question, author) values(?, ?)");
        $stmt->bind_param("ss", $question, $author);
        $result = $stmt->execute();
        $stmt->close();
        if (!$result) {
            return 1;
        }

        $stmt = $this->conn->prepare("SELECT qid from questions WHERE question=?");
        $stmt->bind_param("s", $question);
        if (!$stmt->execute()) {
            return 2;
        }
        $qid = $stmt->get_result()->fetch_assoc();
        $stmt->close();

        foreach ($answers as $val) {
            $stmt = $this->conn->prepare("INSERT INTO answers(qid, answer, iscorrect) values(?, ?, ?)");
            $stmt->bind_param("sss", $qid['qid'], $val['answer'], $val['iscorrect']);
            $answerresult = $stmt->execute();
            if (!$answerresult) {
                return 3;
            }
            $stmt->close();
        }
        return 0;
    }

    public function updateQuestion($input)
    {
        $editor = $input['editor'];
        $qid = $input['qid'];
        $question = $input['question'];
        $answers = $input['answers'];

        $stmt = $this->conn->prepare("UPDATE questions SET question=?, editor=? WHERE qid=?");
        $stmt->bind_param("sss", $question, $editor, $qid);
        $result = $stmt->execute();
        $stmt->close();
        if (!$result) {
            return 1;
        }

        $stmt = $this->conn->prepare("DELETE from answers WHERE qid=?");
        $stmt->bind_param("s", $qid);
        $result = $stmt->execute();
        $stmt->close();
        if (!$result) {
            return 2;
        }

        foreach ($answers as $val) {
            $stmt = $this->conn->prepare("INSERT INTO answers(qid, answer, iscorrect) values(?, ?, ?)");
            $stmt->bind_param("sss", $qid, $val['answer'], $val['iscorrect']);
            $answerresult = $stmt->execute();
            if (!$answerresult) {
                return 3;
            }
            $stmt->close();
        }
        return 0;
    }


    public function deleteQuestion($input)
    {
        $qid = $input['qid'];

        $stmt = $this->conn->prepare("DELETE from questions WHERE qid=?");
        $stmt->bind_param("s", $qid);
        $result = $stmt->execute();
        $stmt->close();
        if (!$result) {
            return 1;
        }

        $stmt = $this->conn->prepare("DELETE from answers WHERE qid=?");
        $stmt->bind_param("s", $qid);
        $result = $stmt->execute();
        $stmt->close();
        if (!$result) {
            return 2;
        }
        return 0;
    }

    public function getQuestionById($qid)
    {

        $stmt = $this->conn->prepare("SELECT * from questions WHERE qid=?");
        $stmt->bind_param("s", $qid);
        if (!$stmt->execute()) {
            return 1;
        }
        $question_result = $stmt->get_result()->fetch_assoc();
        $stmt->close();

        $output = array();


        if($stmt = $this->conn->prepare("SELECT answer, iscorrect FROM answers WHERE qid=?")){

            $stmt->bind_param("s", $qid); //query is $_GET['query'], user input

            $stmt->execute();
            $answer = null;
            $iscorrect = null;
            $stmt->bind_result($answer, $iscorrect);

            $menu = array();
            while($stmt->fetch()){ //problematic code...
                $menu[] = array(
                    "answer" => $answer,
                    "iscorrect" => $iscorrect
                );
            }
            $stmt->close();
        } else {
            return 2;
        }
        $question_result['answers'] = $menu;
        //$answers['answers'] = $menu;

        return $question_result;
    }

    public function getRandomQuestion()
    {
        $stmt = $this->conn->prepare("SELECT qid FROM questions ORDER BY RAND() LIMIT 1");
        if (!$stmt->execute()) {
            return 1;
        }

        $qid2 = $stmt->get_result()->fetch_assoc();
        $stmt->close();

        $qid = $qid2['qid'];


        $stmt = $this->conn->prepare("SELECT * from questions WHERE qid=?");
        $stmt->bind_param("s", $qid);
        if (!$stmt->execute()) {
            return 1;
        }
        $question_result = $stmt->get_result()->fetch_assoc();
        $stmt->close();

        $output = array();


        if($stmt = $this->conn->prepare("SELECT answer, iscorrect FROM answers WHERE qid=?")){

            $stmt->bind_param("s", $qid); //query is $_GET['query'], user input

            $stmt->execute();
            $answer = null;
            $iscorrect = null;
            $stmt->bind_result($answer, $iscorrect);

            $menu = array();
            while($stmt->fetch()){ //problematic code...
                $menu[] = array(
                    "answer" => $answer,
                    "iscorrect" => $iscorrect
                );
            }
            $stmt->close();
        } else {
            return 2;
        }
        $question_result['answers'] = $menu;
        //$answers['answers'] = $menu;

        return $question_result;
    }
}

?>
