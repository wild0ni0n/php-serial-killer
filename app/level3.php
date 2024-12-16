<?php
include("secret3.php");

if(isset($_GET["source"])) {
    highlight_file(__FILE__);
}


class SQL {
    public $query = '';
    public $conn;
    public function __construct() {
        if(!file_exists("/tmp/database.db")) {
            touch("/tmp/database.db");
            $this->setup();
        }
    }
    
    public function setup() {
        global $level3_secret;
        $this->conn = new SQLite3("/tmp/database.db");
        $this->conn->query("CREATE TABLE users(id integer, username text, password text)");
        $this->conn->query("INSERT INTO users values(1, 'Onion', '{$level3_secret}')");
        $this->execute();
    }
    public function connect() {
        $this->conn = new SQLite3 ("/tmp/database.db", SQLITE3_OPEN_READONLY);
    }

    public function SQL_query($query) {
        $this->query = $query;
    }

    public function execute() {
        return @$this->conn->query($this->query);
    }

    public function __destruct() {
        if (!isset ($this->conn)) {
            $this->connect ();
        }

        $ret = $this->execute ();
        if (false !== $ret) {    
            while (false !== ($row = $ret->fetchArray (SQLITE3_ASSOC))) {
                echo '<p><strong>Username:<strong> ' . $row['username'] . '</p>';
            }
        }
    }
}

$sql = new SQL();
$sql->connect();
$sql->query = 'SELECT username FROM users WHERE id=';


if (isset($_COOKIE['onion_cookie'])) {
    $sess_data = unserialize (base64_decode ($_COOKIE['onion_cookie']));
    try {
        if (is_array($sess_data) && $sess_data['ip'] != $_SERVER['REMOTE_ADDR']) {
            die('CANT HACK US!!!');
        }
    } catch(Exception $e) {
        echo $e;
    }
} else {
    $cookie = base64_encode (serialize (array ( 'ip' => $_SERVER['REMOTE_ADDR']))) ;
    setcookie ('onion_cookie', $cookie, time () + (86400 * 30));
}

if (isset ($_REQUEST['id']) && is_numeric ($_REQUEST['id'])) {
    try {
        $sql->query .= $_REQUEST['id'];
    } catch(Exception $e) {
        echo ' Invalid query';
    }
}
?>

<!DOCTYPE html>
<html>
<head>
        <title>Level3</title>
        <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.2.1/css/bootstrap.min.css" integrity="sha384-GJzZqFGwb1QTTN6wy59ffF1BuGJpLSa9DkKMp0DgiMDm4iYMj70gZWKYbI706tWS" crossorigin="anonymous">
</head>
        <body>
                <div id="main">
                        <div class="container">
                                <div class="row">
                                        <h1>Searching Users</h1>
                                </div>
                                <div class="row">
                                        <p class="lead">
                                        Inspired by <a href="https://websec.fr">websec</a>
                                        <br />
                                        <a href="?source">📝</a>
                                        </p>
                                </div>
                        </div>
                        <div class="container">
                <div class="row">
                    <form class="form-inline" method='post'>
                        <input name='id' class='form-control' type='text' placeholder='User id'>
                        <input class="form-control btn btn-default" name="submit" value='Go' type='submit'>
                                        </form>
                                </div>
                        </div>
                </div>
        </body>
</html>