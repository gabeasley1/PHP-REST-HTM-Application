<? require_once('classes.php'); ?>
<? $edit = isset($_GET['edit']) && $_GET['edit'] != 0 ?>
<? $copy = isset($_GET['copy']) && $_GET['copy'] != 0 ?>
<? if (isset($_GET['user'])): ?>
    <? $user = Account::getUserByUserName($_GET['user']); ?>
    <? if (isset($_GET['task'])): ?>
        <? $task_uri = Task::uriFromTaskNumber($_GET['task']); ?>
        <? $task = Util::retrieveTaskDescription($user, $task_uri); ?>
    <? else: ?>
        <? $task = null; ?>
    <? endif; ?>
    <? if ($edit): ?>
        <?= Task::toEditHtml($user, $task, $copy); ?>
    <? else: ?>
        <? if ($task != null): ?>
            <div class='edit-buttons'>
                <a id='edit-task-link' 
               href='/<?=$_GET['user']?>/<?=$_GET['task']?>/edit/'>
                    Edit
                </a>
                <a id='delete-task-link'
               href='/<?=$_GET['user']?>/<?=$_GET['task']?>/delete/'
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
    <?= $_SERVER['REQUEST_URI']; ?><br/>
    // TODO set error code somehow to indicate that request failed
<? endif; ?>
