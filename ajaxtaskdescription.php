<? 
/**
 * Allows for retrieving Html for Task Descriptions and forms for editing those
 * descritpions.
 */

require_once('classes.php'); ?>
<? $edit = isset($_GET['edit']) && $_GET['edit'] != 0 ?>
<? $copy = isset($_GET['copy']) && $_GET['copy'] != 0 ?>
<? if (isset($_GET['user'])): ?>
    <? $util = new Util(); ?>
    <? $user = $util->getAccountByName($_GET['user']); ?>
    <? if (isset($_GET['task'])): ?>
        <? $task_uri = Task::uriFromTaskNumber($_GET['task']); ?>
        <? $task = $util->retrieveTaskDescription($user, $task_uri); ?>
    <? else: ?>
        <? $task = null; ?>
    <? endif; ?>
    <? if ($edit): ?>
        <?= Task::toEditHtml($user, $task, $copy); ?>
    <? else: ?>
        <? if ($task != null): ?>
            <? $user = urlencode(urldecode($_GET['user'])); ?>
            <? $taskId = urlencode(urldecode($_GET['task'])); ?>
    <div class='edit-buttons'>
        <div id='edit-task-link-wrapper'>
            <div id='edit-task-link' 
                   class='goog-custom-button goog-custom-button-collapse-right'>
                <a id='edit-task-link-inner' 
                   href='/<?=$user?>/<?=$taskId?>/edit/'>
                    Edit
                </a>
            </div><div id='create-copy-link' 
                   class='goog-custom-button goog-custom-button-collapse-left'>
                <? $href = "/$user/$taskId/copy/"; ?>
                <a id='create-copy-link-inner'
                    href='<?= $href ?>'>
                    Copy
                </a>
            </div>
        </div>
        <div id='delete-task-link' class='goog-custom-button'>
            <a id='delete-task-link-inner' 
               href='/<?=$user?>/<?=$taskId?>/delete/'>
                    Delete
            </a>
        </div>
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
