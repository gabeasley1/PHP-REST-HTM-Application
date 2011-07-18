<?php
require_once("classes.php");
if (!isset($_SESSION)) {
    session_start();
}

$edit= isset($_GET['edit']) && $_GET['edit'] != '0' && $_GET['edit'] != 'false';$new = isset($_GET['new']) && $_GET['new'] != '0' && $_GET['new'] != 'false';
$copy= isset($_GET['copy']) && $_GET['copy'] != '0' && $_GET['copy'] != 'false';
$edit = $edit | $new | $copy;

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

if ($new) $selectedTask = null;

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
                   <span class="header">
                        <span>Accounts</span>
                        <a href="/new/">
                        <span class="add-item-outer">
                            <span id="add-account" class="add-item"></span>
                        </span>
                        </a>
                    </span>
                    <ul id="accounts-list" class="list">
                    <? foreach($accounts as $account): ?>
                        <? if ($account == $selectedUser): ?>
                        <li class='selected'>
                        <? else: ?>
                        <li>
                        <? endif; ?>
                        <?$username = urlencode($account->getUserName());?>
                            <a href="/<?= $username ?>/">
                                <span class="account-name">
                                    <?= $account->getName() ?>
                                </span>
                            </a>
                        </li>
                    <? endforeach; ?>
                    </ul>
                </div>
                <div id="tasks" class="section">
                    <? $username = urlencode($selectedUser->getUserName());?>
                    <span class="header">
                        <span>Tasks</span>
                        <a href="/<?= $username ?>/new/">
                        <span class="add-item-outer">
                            <span id="add-task" class="add-item"></span>
                        </span>
                        </a>
                    </span>
                    <ul id="tasks-list" class="list">
                    <? foreach($tasks as $task): ?>
                        <? if ($task == $selectedTask && !$copy && !$new): ?>
                        <li class='selected'>
                        <? else: ?>
                        <li>
                        <? endif; ?>
                        <? $username = urlencode($selectedUser->getName());?>
                        <? $tasknum = urlencode($task->getTaskNumber()); ?>
                        <? $taskname = Util::urlifyTaskName($task->getName());?>
                        <a href="/<?=$username?>/<?=$tasknum?>/<?=$taskname?>/">
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
            <?= Task::toEditHtml($selectedUser, $selectedTask, $copy); ?>
        <? else: ?>
            <? if ($selectedTask != null): ?>
                <? $accNum = urlencode($selectedUser->getName()); ?>
                <? $taskNum = urlencode($selectedTask->getTaskNumber()); ?>
                <? $taskname = Util::urlifyTaskName($selectedTask->getName());?>
                <div class='edit-buttons'>
                    <a id='edit-task-link' 
                       href='/<?=$accNum?>/<?=$taskNum?>/<?=$taskname?>/edit/'>
                        Edit
                    </a>
                    <a id='delete-task-link'
                      href='/<?=$accNum?>/<?=$taskNum?>/<?=$taskname?>/delete/'>
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
