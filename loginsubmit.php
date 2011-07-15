<?
require_once('classes.php');
$href = "login.php";
if (isset($_SERVER["HTTP_REFERRER"])) {
    $href = $_SERVER["HTTP_REFERRER"];
}
if (isset($_POST['username']) and isset($_POST['uri']) and 
            isset($_POST['password'])) {
    $username = $_POST['username'];
    $uri = $_POST['uri'];
    $password = $_POST['password'];
    
    $result = Util::addAccount($username, $password, $uri);
    if ($result or $result===null) {
        $name = str_replace('+', '%20', urlencode($username));
        $href = "tasklist.php?user=$name";
    } else {
        if (!isset($_SESSION)) session_start();
        $_SESSION['flash'] = "Oops! Something went wrong.  Are you sure that ".
            "everything is entered in correctly?";
    }
}

header("Location: $href");
?>