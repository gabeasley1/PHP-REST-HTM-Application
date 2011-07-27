<? 
/**
 * Allows for deleting tasks through a series of GIT requests.
 * @todo {Andrew Hays} Make a more secure method of deleting tasks.
 */

require_once('classes.php'); 
if (isset($_GET['user']) and isset($_GET['task'])) {
    $user = Account::getUserByUserName($_GET['user']);
    $task_uri = Task::uriFromTaskNumber($_GET['task']);
    $task = Util::retrieveTaskDescription($user, $task_uri);
    $success = Task::deleteTask($user, $task);
    $message = $success === true ? "Task successfully deleted." : $success;
    $successtxt = $success === true ? "true" : "false";
    echo "{\n";
    echo "\t\"success\": $successtxt,\n";
    echo "\t\"message\": \"$message\"\n";
    echo "}";
} else {
    echo "{\n";
    echo "\t\"success\": false,\n";
    echo "\t\"message\": \"Not enough information to delete task.\"\n";
    echo "}";
}
?>
