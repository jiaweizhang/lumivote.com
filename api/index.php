<?php
require_once 'include/DbHandler.php';
require_once 'include/PassHash.php';
require 'Slim/Slim.php';

\Slim\Slim::registerAutoloader();

$app = new \Slim\Slim();

/**
 * User Creation
 */
$app->post('/user', function () use ($app) {

    $response = array();

    $json = $app->request->getBody();
    $input = json_decode($json, true); // parse the JSON into an assoc. array

    $user = $input['user'];

    $db = new DbHandler();
    $res = $db->createUser($user);

    if ($res == USER_CREATED_SUCCESSFULLY) {
        $response = array("error" => false, "message" => "You are successfully registered");
        echoResponse(201, $response);
    } else if ($res == USER_CREATE_FAILED) {
        $response = array("error" => true, "message" => "Oops! An error occurred while registering");
        echoResponse(200, $response);
    } else if ($res == USER_ALREADY_EXISTED) {
        $response["error"] = true;
        $response["message"] = "Sorry, this email already existed";
        echoResponse(200, $response);
    }
});


/**
 * Get all timeline events
 * method GET
 * params - none
 * url - /events
 */
$app->get('/events', function () use ($app) {
    $response = array();
    $db = new DbHandler();

    // creating new task
    $timeline = $db->getEvents();

    if ($timeline != NULL) {
        $response["error"] = false;
        $response["timeline"] = array();
        while ($event = $timeline->fetch_assoc()) {
            $temp = array();
            $temp['name'] = $event['name'];
            $temp['date'] = $event['date'];
            $temp['time'] = $event['time'];
            $temp['party'] = $event['party'];
            $temp['city'] = $event['city'];
            $temp['state'] = $event['state'];
            $temp['type'] = $event['type'];
            $temp['description'] = $event['description'];
            array_push($response["timeline"], $temp);
        }
    } else {
        $response['error'] = true;
        $response['message'] = 'Error. Timeline retrieval failed.';
    }
    echoResponse(200, $response);
});

/**
 * Get candidates
 * method GET
 * params - none
 * url - /events
 */
$app->get('/candidates', function () use ($app) {
    $response = array();
    $db = new DbHandler();
    $partyValue = $app->request->params('party');
    $idValue = $app->request->params('id');
    if ($partyValue != NULL && $idValue != NULL) {
        $response['error'] = true;
        $response['message'] = 'Error. Invalid Query.';
    } else if ($partyValue == NULL && $idValue != NULL) {
        // query by ID
        $candidate = $db->getCandidateById($idValue);
        if ($candidate != NULL) {
            $response["error"] = false;
            $response['ID'] = $candidate['ID'];
            $response['fName'] = $candidate['fName'];
            $response['nickName'] = $candidate['nickName'];
            $response['mName'] = $candidate['mName'];
            $response['lName'] = $candidate['lName'];
            $response['party'] = $candidate['party'];
            $response['occupation'] = $candidate['occupation'];
            $response['birthdate'] = $candidate['birthdate'];
            $response['spouseFName'] = $candidate['spouseFName'];
            $response['spouseMName'] = $candidate['spouseMName'];
            $response['spouseLName'] = $candidate['spouseLName'];
            $response['bio'] = $candidate['bio'];
            $response['twitter'] = $candidate['twitter'];
            $response['url'] = $candidate['url'];
            $response['facebook'] = $candidate['facebook'];
            $response['bioGuide'] = $candidate['bioGuide'];
            $response['image'] = $candidate['image'];
        } else {
            $response['error'] = true;
            $response['message'] = 'Error. Single candidate retrieval failed.';
        }
    } else {
        $candidates = $db->getCandidates($partyValue);
        if ($candidates != NULL) {
            $response["error"] = false;
            $response["candidates"] = array();
            while ($candidate = $candidates->fetch_assoc()) {
                $temp = array();
                $temp['ID'] = $candidate['ID'];
                $temp['fName'] = $candidate['fName'];
                $temp['nickName'] = $candidate['nickName'];
                $temp['mName'] = $candidate['mName'];
                $temp['lName'] = $candidate['lName'];
                $temp['party'] = $candidate['party'];
                $temp['occupation'] = $candidate['occupation'];
                $temp['birthdate'] = $candidate['birthdate'];
                $temp['spouseFName'] = $candidate['spouseFName'];
                $temp['spouseMName'] = $candidate['spouseMName'];
                $temp['spouseLName'] = $candidate['spouseLName'];
                $temp['bio'] = $candidate['bio'];
                $temp['twitter'] = $candidate['twitter'];
                $temp['url'] = $candidate['url'];
                $temp['facebook'] = $candidate['facebook'];
                $temp['bioGuide'] = $candidate['bioGuide'];
                $temp['image'] = $candidate['image'];
                array_push($response["candidates"], $temp);
            }
        } else {
            $response['error'] = true;
            $response['message'] = 'Error. Candidate retrieval failed.';
        }
    }
    echoResponse(200, $response);
});

// http://lumivote.com/api/lumitrivia/question
$app->post('/lumitrivia/question', function () use ($app) {
    $json = $app->request->getBody();
    $input = json_decode($json, true);

    $db = new DbHandler();
    $res = $db->createQuestion($input);

    if ($res == 0) {
        $response = array("error" => false, "message" => "question add success");
        echoResponse(201, $response);
    } else if ($res == 1) {
        $response = array("error" => true, "message" => "question add failure");
        echoResponse(200, $response);
    } else if ($res == 2) {
        $response = array("error" => true, "message" => "qid find failure");
        echoResponse(200, $response);
    } else if ($res == 3) {
        $response = array("error" => true, "message" => "error inserting answer");
        echoResponse(200, $response);
    }
});

//http://lumivote.com/api/lumitrivia/question
$app->put('/lumitrivia/question', function () use ($app) {
    $json = $app->request->getBody();
    $input = json_decode($json, true);

    $db = new DbHandler();
    $res = $db->updateQuestion($input);

    if ($res == 0) {
        $response = array("error" => false, "message" => "success");
        echoResponse(201, $response);
    } else if ($res == 1) {
        $response = array("error" => true, "message" => "failed to update question");
        echoResponse(200, $response);
    } else if ($res == 2) {
        $response = array("error" => true, "message" => "failed to delete answers");
        echoResponse(200, $response);
    } else if ($res == 3) {
        $response = array("error" => true, "message" => "error inserting answer");
        echoResponse(200, $response);
    }
});

//http://lumivote.com/api/lumitrivia/question
$app->post('/lumitrivia/questiondelete', function () use ($app) {
    $json = $app->request->getBody();
    $input = json_decode($json, true);

    $db = new DbHandler();
    $res = $db->deleteQuestion($input);

    if ($res == 0) {
        $response = array("error" => false, "message" => "success");
        echoResponse(201, $response);
    } else if ($res == 1) {
        $response = array("error" => true, "message" => "failed to delete question");
        echoResponse(200, $response);
    } else if ($res == 2) {
        $response = array("error" => true, "message" => "failed to delete answers");
        echoResponse(200, $response);
    }
});

//http://lumivote.com/api/lumitrivia/question/:qid
$app->get('/lumitrivia/question/:qid', function ($qid) use ($app) {
    $db = new DbHandler();
    $res = $db->getQuestionById($qid);

    if ($res == 1) {
        $response = array("error" => true, "message" => "failed getting question");
        echoResponse(201, $response);
    } else if ($res == 1) {
        $response = array("error" => true, "message" => "failed getting answers");
        echoResponse(201, $response);
    } else {
        echoResponse(200, $res);
    }
});

//http://lumivote.com/api/lumitrivia/question
$app->get('/lumitrivia/question', function () use ($app) {
    $db = new DbHandler();
    $res = $db->getRandomQuestion();

    if ($res == 1) {
        $response = array("error" => true, "message" => "failed getting question");
        echoResponse(201, $response);
    } else if ($res == 1) {
        $response = array("error" => true, "message" => "failed getting answers");
        echoResponse(201, $response);
    } else {
        echoResponse(200, $res);
    }

});

//http://lumivote.com/api/lumitrivia/question
$app->get('/lumitrivia/question/user/:username/:iscorrect', function ($username, $iscorrect) use ($app) {
    $db = new DbHandler();
    $res = $db->getQuestionByUsername($username, $iscorrect);

    //var_dump($res);
    if ($res == 1) {
        $response = array("error" => true, "message" => "failed getting question");
        echoResponse(201, $response);
    } else if ($res == 1) {
        $response = array("error" => true, "message" => "failed getting answers");
        echoResponse(201, $response);
    } else {
        echoResponse(200, $res);
    }

});

//http://lumivote.com/api/lumitrivia/usersubmit
$app->put('/lumitrivia/question/usersubmit', function () use ($app) {

    $json = $app->request->getBody();
    $input = json_decode($json, true);

    var_dump($input);

    /*$db = new DbHandler();
    $res = $db->deleteQuestion($input);

    if ($res == 0) {
        $response = array("error" => false, "message" => "success");
        echoResponse(201, $response);
    } else if ($res == 1) {
        $response = array("error" => true, "message" => "failed to delete question");
        echoResponse(200, $response);
    } else if ($res == 2) {
        $response = array("error" => true, "message" => "failed to delete answers");
        echoResponse(200, $response);
    }*/

});


/**
 * Verifying required params posted or not
 */
function verifyRequiredParams($required_fields)
{
    $error = false;
    $error_fields = "";
    $request_params = array();
    $request_params = $_REQUEST;
    // Handling PUT request params
    if ($_SERVER ['REQUEST_METHOD'] == 'PUT') {
        $app = \Slim\Slim::getInstance();
        parse_str($app->request()->getBody(), $request_params);
    }
    foreach ($required_fields as $field) {
        if (!isset ($request_params [$field]) || strlen(trim($request_params [$field])) <= 0) {
            $error = true;
            $error_fields .= $field . ', ';
        }
    }

    if ($error) {
        // Required field(s) are missing or empty
        // echo error json and stop the app
        $response = array();
        $app = \Slim\Slim::getInstance();
        $response ["error"] = true;
        $response ["message"] = 'Required field(s) ' . substr($error_fields, 0, -2) . ' is missing or empty';
        echoResponse(400, $response);
        $app->stop();
    }
}

/**
 * Validating email address
 */
function validateEmail($email)
{
    $app = \Slim\Slim::getInstance();
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $response["error"] = true;
        $response["message"] = 'Email address is not valid';
        echoResponse(400, $response);
        $app->stop();
    }
}

/**
 * Echoing json response to client
 *
 * @param String $status_code
 *            Http response code
 * @param Int $response
 *            Json response
 */
function echoResponse($status_code, $response)
{
    $app = \Slim\Slim::getInstance();
    // Http response code
    $app->status($status_code);

    // setting response content type to json
    $app->contentType('application/json');

    echo json_encode($response);
}

$app->run();
