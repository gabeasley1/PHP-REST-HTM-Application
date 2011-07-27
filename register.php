<?
/**
 * Handles all submit requests from the registration form.
 */
require_once('classes.php');
session_start();

$href = "/login/";
if (isset($_SERVER["HTTP_REFERRER"])) {
    $href = $_SERVER["HTTP_REFERRER"];
}
if (isset($_POST['email']) and isset($_POST['password_confirm']) and 
            isset($_POST['password'])) {
    $email = $_POST['email'];
    $password = $_POST['password'];
    $password_confirm = $_POST['password_confirm'];

    if ($password != $password_confirm) {
        $_SESSION['flash'] = "Passwords did not match.  Please try again.";
    } else {
        $util = new Util();
        
        $result = $util->registerUser($email, $password, $password_confirm);
        if ($result) {
            $_SESSION['user'] = $util->getUserByEmail($email);
            $href = "/";
        } else {
            $_SESSION['flash'] = "An account with that email address already ".
                "exists.  Do you already have an account?";
        }
    }
}
header("Location: $href");
?>

