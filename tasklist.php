<?php
require_once("classes.php");
if (!isset($_SESSION)) {
    session_start();
}

$edit= isset($_GET['edit']) && $_GET['edit'] != '0' && $_GET['edit'] != 'false';

$selectedUserUri = null;
$selectedTaskUri = null;
$selectedTaskNumber = null;
$selectedUserName = null;

if (isset($_GET['user'])) {
    $selectedUserName = urldecode($_GET['user']);
}
if (isset($_GET['task'])) {
    $selectedTaskNumber = $_GET['task'];
}

if ($selectedTaskNumber != null) {
    $selectedTaskUri = Task::uriFromTaskNumber($selectedTaskNumber);
}

if ($selectedUserName != null) {
    $selectedUserUri = Account::uriFromUserName($selectedUserName);
}

$accounts = Util::getAccounts();
if (count($accounts) > 0) {
    $selectedUser = $accounts[0];
} else {
    $selectedUser = null;
}

foreach ($accounts as $account) {
    if ($account->getUri() == $selectedUserUri) {
        $selectedUser = $account;
    }
}

$tasks = Util::getTasksForAccount($selectedUser);
if (count($tasks) > 0) {
    $selectedTask = $tasks[0];
} else {
    $selectedTask = null;
}

foreach ($tasks as $task) {
    if ($task->getUri() == $selectedTaskUri) {
        $selectedTask = $task;
    }
}

?>
<!DOCTYPE html>
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
        <title>Task list for <?= $selectedUser->getName() ?></title>
        <link href="/css/custom-theme/taskstyle.css" 
              type="text/css" rel="Stylesheet" />
        <script type='text/javascript' src='/js/compiled.js'></script>
    </head>
    <body>
    <? if (isset($_SESSION['flash'])): ?>
        <div id='flash'>
            <div id='flash-inner'><?= $_SESSION['flash']; ?></div>
        </div>
        <? unset($_SESSION['flash']); ?>
    <? else: ?>
        <div id='flash' style='display: none;'>
            <div id='flash-inner'><?= $_SESSION['flash']; ?></div>
        </div>
    <? endif; ?>
        <div id="body">
            <div id="navigation" class="navbar">
                <div class='overflow-catch'>
                <div id="accounts" class="section">
                    <a href="#" class="header">
                        <span>Accounts</span>
                    </a>
                    <ul id="accounts-list" class="list">
                    <? foreach($accounts as $account): ?>
                        <? if ($account == $selectedUser): ?>
                        <li class='selected'>
                        <? else: ?>
                        <li>
                        <? endif; ?>
                        <?$username = Util::escape($account->getUserName());?>
                            <a href="?user=<?=$username?>">
                                <span class="account-name">
                                    <?= $account->getName() ?>
                                </span>
                            </a>
                        </li>
                    <? endforeach; ?>
                    </ul>
                </div>
                <div id="tasks" class="section">
                    <a href="#" class="header">
                        <span>Tasks</span>
                    </a>
                    <ul id="tasks-list" class="list">
                    <? foreach($tasks as $task): ?>
                        <? if ($task == $selectedTask): ?>
                        <li class='selected'>
                        <? else: ?>
                        <li>
                        <? endif; ?>
                        <? $username = Util::escape($selectedUser->getName());?>
                        <? $tasknum = Util::escape($task->getTaskNumber()); ?>
                            <a href="?user=<?=$username?>&task=<?=$tasknum?>">
                                <span class="task-name">
                                    <?= $task->getName() ?>
                                </span>
                            </a>
                        </li>
                    <? endforeach; ?>
                    </ul>
                </div>
            </div>
            </div>
            <div id="task-content" class="content">
            <? if ($edit): ?>
                <?= Task::toEditHtml($selectedUser, $selectedTask); ?>
            <? else: ?>
                <? if ($selectedTask != null): ?>
                    <? $accNum = Util::escape($selectedUser->getName()); ?>
                    <? $taskNum = Util::escape($selectedTask->getTaskNumber()); ?>
                    <div class='edit-buttons'>
                        <a id='edit-task-link' 
                           href='?user=<?=$accNum?>&task=<?=$taskNum?>&edit=1'>
                            Edit
                        </a>
                        <a id='delete-task-link'
                           href='delete.php?user=<?=$accNum?>&task=<?=$taskNum?>'>
                            Delete
                        </a>
                    </div>
                    <?= $selectedTask->toHtml() ?>
                <? else: ?>
                    <h2 style="padding: 10px 30px">
                        There are no tasks available to display.
                    </h2>
                <? endif; ?>
            <? endif; ?>
            </div>
        </div>
    </body>
</html>
