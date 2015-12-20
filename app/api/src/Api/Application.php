<?php

namespace Api;

use Api\Model\Features;
use \Slim\Slim;
use \Exception;

// TODO Move all "features" things to a class with index() and get() methods
class Application extends Slim
{
    public $configDirectory;
    public $config;

    // $dbhost = 'us-cdbr-iron-east-03.cleardb.net';
    // $dbuser = 'b81fae531e0b8e';
    // $dbpass = '5b2cbbd2';
    // $dbname = 'heroku_86ad99128ce745f';
    private $conn;

    protected function initConfig()
    {
        $config = array();
        if (!file_exists($this->configDirectory) || !is_dir($this->configDirectory)) {
            throw new Exception('Config directory is missing: ' . $this->configDirectory, 500);
        }
        foreach (preg_grep('/\\.php$/', scandir($this->configDirectory)) as $filename) {
            $config = array_replace_recursive($config, include $this->configDirectory . '/' . $filename);
        }
        return $config;
    }

    public function __construct(array $userSettings = array(), $configDirectory = 'config')
    {
        // Slim initialization
        parent::__construct($userSettings);
        $this->config('debug', false);
        $this->notFound(function () {
            $this->handleNotFound();
        });
        $this->error(function ($e) {
            $this->handleException($e);
        });

        // Config
        $this->configDirectory = __DIR__ . '/../../' . $configDirectory;
        $this->config = $this->initConfig();

        $dbhost = 'us-cdbr-iron-east-03.cleardb.net';
        $dbuser = 'b81fae531e0b8e';
        $dbpass = '5b2cbbd2';
        $dbname = 'heroku_86ad99128ce745f';

        // $this->conn = new mysqli($dbhost, $dbuser, $dbpass, $dbname);
        $this->conn = mysqli_connect($dbhost, $dbuser, $dbpass, $dbname);

        // /features
        $this->get('/features', function () {
            $features = new Features($this->config['features']);
            $this->response->headers->set('Content-Type', 'application/json');
            $this->response->setBody(json_encode($features->getFeatures()));
        });

        $this->get('/features/:id', function ($id) {
            $features = new Features($this->config['features']);
            $feature = $features->getFeature($id);
            if ($feature === null) {
                return $this->notFound();
            }
            $this->response->headers->set('Content-Type', 'application/json');
            $this->response->setBody(json_encode($feature));
        });



        $this->get('/createuser/:username/:password', function ($username, $password) {
            $userData = $this->createUser($username, $password);
            if ($userData === "Error: Username already exists") {
                $this->response->setStatus(500);
            }
            echo json_encode($userData);
        });

        $this->get('/login/:username/:password', function ($username, $password) {
            $userData = $this->login($username, $password);
            if ($userData === "Incorrect Username/Password") {
                $this->response->setStatus(500);
            }
            echo json_encode($userData);
        });

        $this->get('/getdeckcards/:deckID', function ($deckID) {
            $jsonData = getDeckCards($deckID);
            // $this->response->headers->set('Content-Type', 'application/json');
            // $this->response->setBody($jsonData);
            echo $jsonData;
        });

        $this->get('/getuserdecks/:userID', function ($userID) {
            $deckData = $this->getUserDecks($userID);
            if ($deckData === "Error: Not a valid User ID") {
                $this->response->setStatus(500);
            }
            echo json_encode($deckData);
        });

        $this->get('/createdeck/:userID/:deckName/:heroID', function ($userID, $deckName, $heroID) {
            $deckData = $this->createDeck($userID, $deckName, $heroID);
            echo json_encode($deckData);
        });

        $this->get('/deletedeck/:deckID', function ($deckID) {
            $jsonData = deleteDeck($deckID);
            // $this->response->headers->set('Content-Type', 'application/json');
            // $this->response->setBody($jsonData);
            echo $jsonData;
        });

        $this->get('/renamedeck/:deckID/:newName', function ($deckID,$newName) {
            $jsonData = renameDeck($deckID,$newName);
            // $this->response->headers->set('Content-Type', 'application/json');
            // $this->response->setBody($jsonData);
            echo $jsonData;
        });

        $this->get('/addcardtodeck/:deckID/:cardID', function ($deckID,$cardID) {
            $jsonData = addCardToDeck($deckID,$cardID);
            // $this->response->headers->set('Content-Type', 'application/json');
            // $this->response->setBody($jsonData);
            echo $jsonData;
        });

        $this->get('/deletecardfromdeck/:deckID/:cardID', function ($deckID,$cardID) {
            $jsonData = deleteCardFromDeck($deckID,$cardID);
            // $this->response->headers->set('Content-Type', 'application/json');
            // $this->response->setBody($jsonData);
            echo $jsonData;
        });
    }

    public function handleNotFound()
    {
        throw new Exception(
            'Resource ' . $this->request->getResourceUri() . ' using '
            . $this->request->getMethod() . ' method does not exist.',
            404
        );
    }

    public function handleException(Exception $e)
    {
        $status = $e->getCode();
        $statusText = \Slim\Http\Response::getMessageForCode($status);
        if ($statusText === null) {
            $status = 500;
            $statusText = 'Internal Server Error';
        }

        $this->response->setStatus($status);
        $this->response->headers->set('Content-Type', 'application/json');
        $this->response->setBody(json_encode(array(
            'status' => $status,
            'statusText' => preg_replace('/^[0-9]+ (.*)$/', '$1', $statusText),
            'description' => $e->getMessage(),
        )));
    }

    /**
     * @return \Slim\Http\Response
     */
    public function invoke()
    {
        foreach ($this->middleware as $middleware) {
            $middleware->call();
        }
        $this->response()->finalize();
        return $this->response();
    }

    public function createUser($username, $password) {
        $user = array();
        if ($result = mysqli_query($this->conn, "call CreateUser('" . $username . "', '" . $password . "');")) {
            while ($row = mysqli_fetch_row($result)) {
                if ($row[0] === "Error: Username already exists") {
                    return $row[0];
                } else {
                    $user['userID']   = $row[0];
                    $user['username'] = $row[1];
                }
            }
        }
        return $user;
    }

    public function login($username, $password) {
        $user = array();
        if ($result = mysqli_query($this->conn, "call Login('" . $username . "', '" . $password . "');")) {
            while ($row = mysqli_fetch_row($result)) {
                if ($row[0] === "Incorrect Username/Password") {
                    return $row[0];
                } else{
                    $user['userID']   = $row[0];
                    $user['username'] = $row[1];
                }
            }
        }
        return $user;
    }

    public function getDeckCards($deckID) {
        $cards = array();
        if ($result = mysqli_query($this->conn, "call GetDeckCards('" . $deckID . "');")) {
            while ($row = mysqli_fetch_row($result)) {
                $newRow = array();
                if ($row[0] === "Error: Not a Valid Deck") {
                    return $row[0];
                } else {
                  $newRow['cardID'] = $row[0];
                }
                $cards[] = $newRow;
            }
        }
        return $cards;
    }

    public function getUserDecks($userID) {
        $decks = array();
        if ($result = mysqli_query($this->conn, "call GetUserDecks('" . $userID . "');")) {
            while ($row = mysqli_fetch_row($result)) {
                $newRow = array();
                if ($row[0] === "Error: Not a valid User ID") {
                    return $row[0];
                } else {
                  $newRow['deckID']   = $row[0];
                  $newRow['heroID']   = $row[1];
                  $newRow['heroImg']  = $row[2];
                  $newRow['deckName'] = $row[3];
                }
                $decks[] = $newRow;
            }
        }
        return $decks;
    }

    public function createDeck($userID, $deckName, $heroID) {
        if ($result = mysqli_query($this->conn, "call CreateDeck('" . $userID . "','" . $deckName . "', '" . $heroID . "');")) {
            while ($row = mysqli_fetch_row($result)) {
                return $row[0];
            }
        }
    }

    public function deleteDeck($deckID)
    {
        if($conn->multi_query("call DeleteDeck('" . $deckID . "');")){
            do {
                if($result = $conn->store_result()){
                    $row = $result->fetch_row();
                    return $row[0];
                }
            }   while ($conn->next_result());
        }
        else {
            printf("<br>Error: %s\n", $conn->error);
        }
    }

    public function renameDeck($deckID,$newName)
    {
        if($conn->multi_query("call RenameDeck('" . $deckID . "','" . $newName . "');")){
            do {
                if($result = $conn->store_result()){
                    $row = $result->fetch_row();
                    return $row[0];
                }
            }   while ($conn->next_result());
        }
        else {
            printf("<br>Error: %s\n", $conn->error);
        }
    }

    public function addCardToDeck($deckID,$cardID)
    {
        if($conn->multi_query("call addCardToDeck('" . $deckID . "','" . $cardID . "');")){
            do {
                if($result = $conn->store_result()){
                    $row = $result->fetch_row();
                    return $row[0];
                }
            }   while ($conn->next_result());
        }
        else {
            printf("<br>Error: %s\n", $conn->error);
        }
    }

    public function deleteCardFromDeck($deckID,$cardID)
    {
        if($conn->multi_query("call DeleteCardFromDeck('" . $deckID . "','" . $cardID . "');")){
            do {
                if($result = $conn->store_result()){
                    $row = $result->fetch_row();
                    return $row[0];
                }
            }   while ($conn->next_result());
        }
        else {
            printf("<br>Error: %s\n", $conn->error);
        }
    }
}
