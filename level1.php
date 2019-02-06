<?php
include("secret.php");
if(isset($_GET["source"])) {
    highlight_file(__FILE__);
}
class Login {
    public $username;
    public $password;
    public $role;

    function set_userinfo($username, $password) {
        $this->username = $username;
        $this->password = $password;
    }
}

$userinfo = "";

if(!empty($_POST["username"]) AND !empty($_POST["password"])) {
    $login = unserialize($_POST["userinfo"]);
    if(empty($login->username)) {
        $login = new Login();
        $login->set_userinfo($_POST["username"], $_POST["password"]);
    }
    $userinfo = serialize($login);
    $user_role = $login->role;
    if($user_role === "ADMIN") {
        echo "Congratulations, Admin!<br />";
        echo $level1_secret;
        exit;
    } else {
        echo "Hi, $login->username.<br />";
    }
}

?>
<!DOCYPE html>
<html lang="ja">
<head>
	<meta charset="UTF-8">
</head>
<body>
    Login Form
    <form method="POST" action="">
        username: <input type="text" name="username" value=""><br />
        password: <input type="password" name="password" value=""><br />
        <input type="hidden" name="userinfo" value="<?php if(isset($userinfo)) { echo htmlspecialchars($userinfo); } ?>">
        <input type="submit" value="login">
    </form>
    <a href="?source">📝</a>
