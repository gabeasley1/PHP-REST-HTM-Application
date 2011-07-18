<? require_once('classes.php');
session_start();
if (isset($_GET['user']) and isset($_GET['task'])) {
    $user = Account::getUserByUserName($_GET['user']);
    $task_uri = Task::uriFromTaskNumber($_GET['task']);
    $task = Util::retrieveTaskDescription($user, $task_uri);
    $success = Task::deleteTask($user, $task);
    if ($success === true) {
        $_SESSION['flash'] = "Task successfully deleted.";
    } else {
        $_SESSION['flash'] = $success;
    }
} else {
    $_SESSION['flash'] = "Not enough information provided to delete task.";
}

if (isset($_SERVER['HTTP_REFERER']) and $_SERVER['HTTP_REFERER']) {
    $referrer = $_SERVER['HTTP_REFERER'];
} else {
    $referrer = "/tasklist/";
}

header("Location: $referrer");
?>
