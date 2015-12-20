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
            $jsonData = login($username, $password);
            // $this->response->headers->set('Content-Type', 'application/json');
            // $this->response->setBody($jsonData);
            echo $jsonData;
        });

        $this->get('/getdeckcards/:deckID', function ($deckID) {
            $jsonData = getDeckCards($deckID);
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
                    $user['userID'] = $row[0];
                    $user['username'] = $row[1];
                }
          }
          return $user;
        }
    }

    // public function createUser($username, $password)
    // {
    //     $myArray = array();
    //     if($conn->multi_query("call CreateUser('" . $username . "', '" . $password . "');")){
    //         do {
    //             if($result = $conn->store_result()){
    //                 $newRow = array();
    //                 while($row = $result->fetch_row()){
    //                     if($row === "Error: Username already exists"){
    //                       return $row;
    //                     } else{
    //                        $newRow['userID'] = $row[0];
    //                        $newRow['username'] = $row[1];
    //                     }
    //                     $myArray[] = $newRow;
    //                 }
    //             }
    //         }   while ($conn->next_result());
    //     }
    //     else {
    //         printf("<br>Error: %s\n", $conn->error);
    //     }
    //     return json_encode($myArray);
    // }

    public function login($username, $password)
    {
        $myArray = array();
        if($conn->multi_query("call Login('" . $username . "', '" . $password . "');")) {
            // do {
                if($result = $conn->store_result()){
                    $newRow = array();
                    while($row = $result->fetch_row()){
                      // can this happen?
                        if($row === "Error: Username already exists"){
                          return $row;
                        } else{
                           $newRow['userID'] = $row[0];
                           $newRow['username'] = $row[1];
                        }
                        $myArray[] = $newRow;
                    }
                }
            // }
        } else {
            // printf("<br>Error: %s\n", $this->conn->error);
        }
        // printf($user[0]);
        return $user;
    }

    public function getDeckCards($deckID)
    {
        $myArray = array();
        if($conn->multi_query("call GetDeckCards('" . $deckID . "');")){
            do {
                if($result = $conn->store_result()){
                    $newRow = array();
                    while($row = $result->fetch_row()){
                        if($row === "Error: Not a Valid Deck"){
                            return $row;
                        } else{
                            $newRow['cardID'] = $row[0];
                        }
                        $myArray[] = $newRow;
                    }
                }
            } while ($conn->next_result());
        }
        else {
            printf("<br>Error: %s\n", $conn->error);
        }
        return json_encode($myArray);
    }
}
