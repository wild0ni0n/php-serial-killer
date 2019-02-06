<?php
date_default_timezone_set('Asia/Tokyo');
//include("/var/www/html/secret.php");

if(isset($_GET["source"])) {
    highlight_file(__FILE__);
}

class Logger {
    const LOGDIR = "/tmp/";
    private $filename = '';
    private $log = '';

    public function __construct($filename) {
        $this->filename = $filename;
        $this->log = '';
    }

    public function __destruct() {
        $path = self::LOGDIR . $this->filename;
        $fp = fopen($path, 'a');
        if($fp === false) {
            die('Logger: File can not be open. '. htmlspecialchars($path));
        }
        if(!flock($fp, LOCK_EX)) {
            die('Logger: Failed to lock the file. ');
        }
        fwrite($fp, $this->log);
        flush($fp);
        flock($fp, LOCK_UN);
        fclose($fp);
    }

    public function add($log) {
        $this->log .=$log;
    }

}


$status = array();
$result = array();

//2019/1/30 comment: updated. by wild0ni0n
//$log = new Logger("access.log");

if(!empty($_COOKIE['status'])) {
    $status = unserialize(urldecode($_COOKIE['status']));
    if(!is_array($status)) {
        setcookie('status',time()-60);
    }
}    

if(!empty($_POST['username']) AND !empty($_POST['text'])) {
    $status = array();
    $status['username'] = $_POST['username'];
    $status['text'] = $_POST['text'];
    setcookie('status', serialize($status));
}
if(!empty($status['username']) AND !empty($status['text'])) {
    //output html
    $result[] = "Username:". htmlspecialchars($status['username']) ."<br />";
    $result[] = "text:". htmlspecialchars($status['text']) ."<br />";

    //output log
    //2019/1/30 comment: updated. by wild0ni0n
    //$logmsg = "[".date("Y/m/d H:i:s")."] ";
    //$logmsg .= "Username:".htmlspecialchars($status['username']). " ";
    //$logmsg .= "text:".htmlspecialchars($status['text']);
    //$logmsg .= PHP_EOL;
    //$log->add($logmsg);
    
}
?>
<!DOCYPE html>
<html lang="ja">
<head>
	<meta charset="UTF-8">
</head>
<body>
    <form method="POST" action="">
        username: <input type="text" name="username" value=""><br />
        text: <textarea name="text"></textarea></br>
        <input type="submit" value="Post">
    </form>
    Logger クラスは<a href="https://blog.tokumaru.org/2015/07/phpunserialize.html">徳丸さんの記事のコード</a>を引用をしております。
    <a href="?source">📝</a>
    <hr>
<?php
if(isset($result)) {
    foreach($result as $v) {
        print($v);
    }
}
?>
