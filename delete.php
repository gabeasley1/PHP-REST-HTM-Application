<? 
/**
 * Task deletion for those without JavaScript
 */

/**
 * Required to interact with all of the other data.
 */
require_once('classes.php');
if (isset($_GET['user']) and isset($_GET['task'])) {
    $util = new Util();
    $user = $util->getAccountByName($_GET['user']);
    $task = $util->getTaskById($_GET['task'], $user);
    $success = Task::deleteTask($user, $task);
    if ($success === true) {
        $_SESSION['flash'] = "Task successfully deleted.";
    } else {
        $_SESSION['flash'] = $success;
    }
} else {
    $_SESSION['flash'] = "Not enough information provided to delete task.";
}

$referrer = "";
if (isset($_GET['user'])) {
    $refer = "/".urlencode($_GET['user'])."/";
} else {
    $refer = "/";
}

header("Location: $refer");
?>
