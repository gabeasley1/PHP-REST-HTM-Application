<?
/**
 * Handles all submit requests from the login form.
 * @author Kevin Topiwalla
 */
require_once('classes.php');
session_start();

$href = "/login/";
if (isset($_SERVER["HTTP_REFERRER"])) {
    $href = $_SERVER["HTTP_REFERRER"];
}
if (isset($_POST['email']) and isset($_POST['password'])) {
    $email = $_POST['email'];
    $password = $_POST['password'];

    $util = new Util();
    
    $result = $util->authenticateUser($email, $password);
    if ($result) {
        $_SESSION['user'] = $util->getUserByEmail($email);
        $href = "/";
    } else {
        $_SESSION['flash'] = "Email or password is invalid.";
    }
}

header("Location: $href");
?>
