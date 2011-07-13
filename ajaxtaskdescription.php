<? require_once('classes.php'); ?>
<? $edit = isset($_GET['edit']) && $_GET['edit'] != 0 ?>
<? if (isset($_GET['user']) and isset($_GET['task'])): ?>
    <? $user = Account::getUserByUserName($_GET['user']); ?>
    <? $task_uri = Task::uriFromTaskNumber($_GET['task']); ?>
    <? $task = Util::retrieveTaskDescription($user, $task_uri); ?>
    <? if ($edit): ?>
        <?= Task::toEditHtml($user, $task); ?>
    <? else: ?>
        <? if ($task != null): ?>
            <div class='edit-buttons'>
                <a id='edit-task-link' 
               href='?user=<?=$_GET['user']?>&task=<?=$_GET['task']?>&edit=1'>
                    Edit
                </a>
                <a id='delete-task-link'
               href='delete.php?user=<?=$_GET['user']?>&task=<?=$_GET['task']?>'
                >
                    Delete
                </a>
            </div>
            <?= $task->toHtml() ?>
        <? else: ?>
            <h2 style="padding: 10px 30px">
                There are no tasks available to display.
            </h2>
        <? endif; ?>
    <? endif; ?>
<? else: ?>
        // TODO set error code somehow to indicate that request failed
<? endif; ?>
