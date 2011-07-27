<?php
/**
 * The main view screen.  Handles all views for all tasks and accounts.
 */

require_once("classes.php");

$util = new Util();

$edit= isset($_GET['edit']) && $_GET['edit'] != '0' && $_GET['edit'] != 'false';$new = isset($_GET['new']) && $_GET['new'] != '0' && $_GET['new'] != 'false';
$copy= isset($_GET['copy']) && $_GET['copy'] != '0' && $_GET['copy'] != 'false';
$edit = $edit | $new | $copy;

$selectedAccount = null;
$selectedTask = null;
$selectedTaskNumber = null;
$selectedAccountName = null;

if (isset($_GET['user'])) {
    $selectedAccountName = urldecode($_GET['user']);
}
if (isset($_GET['task'])) {
    $selectedTaskNumber = $_GET['task'];
}

if ($selectedAccountName != null) {
    $selectedAccount = $util->getAccountByName($selectedAccountName);
}

$accounts = $util->getAccounts();
if (count($accounts) > 0 && $selectedAccount == null) {
    $selectedAccount = $accounts[0];
} else if (count($accounts) == 0) {
    header("Location: /wizard/");
}

if ($selectedTaskNumber != null) {
    $selectedTask = $util->getTaskById($selectedTaskNumber, $selectedAccount);
    if ($selectedTask == null) {
        $_SESSION['flash'] = "Task doesn't exist or access denied.";
    }
}

$tasks = array();
if (count($accounts) > 0) {
    $tasks = $util->getTasksForAccounts($selectedAccount, false);
    if (count($tasks) > 0 && $selectedTask == null) {
        $selectedTask = $util->retrieveTaskDescription($selectedAccount,
            $tasks[0]->getUri());
    }
}

if ($new) $selectedTask = null;

?>
<!DOCTYPE html>
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
        <title>Task list for <?= $selectedAccount->getName() ?></title>
        <link href="/css/custom-theme/taskstyle.css" 
              type="text/css" rel="Stylesheet" />
        <script type='text/javascript' 
                src='/closure-library/closure/goog/base.js'></script>
        <script>
goog.require('goog.History');
goog.require('goog.Uri');
goog.require('goog.date');
goog.require('goog.date.Date');
goog.require('goog.dom');
goog.require('goog.dom.forms');
goog.require('goog.editor.Field');
goog.require('goog.editor.plugins.BasicTextFormatter');
goog.require('goog.editor.plugins.EnterHandler');
goog.require('goog.editor.plugins.HeaderFormatter');
goog.require('goog.editor.plugins.LinkBubble');
goog.require('goog.editor.plugins.LinkDialogPlugin');
goog.require('goog.editor.plugins.ListTabHandler');
goog.require('goog.editor.plugins.RemoveFormatting');
goog.require('goog.editor.plugins.UndoRedo');
goog.require('goog.events');
goog.require('goog.history.EventType');
goog.require('goog.history.Html5History');
goog.require('goog.i18n.DateTimeFormat');
goog.require('goog.i18n.DateTimeParse');
goog.require('goog.i18n.DateTimeSymbols');
goog.require('goog.i18n.DateTimeSymbols_en_US');
goog.require('goog.locale');
goog.require('goog.net.XhrIo');
goog.require('goog.string');
goog.require('goog.style');
goog.require('goog.ui.Button');
goog.require('goog.ui.ButtonRenderer');
goog.require('goog.ui.ButtonSide');
goog.require('goog.ui.Component');
goog.require('goog.ui.CustomButton');
goog.require('goog.ui.CustomButtonRenderer');
goog.require('goog.ui.Dialog');
goog.require('goog.ui.Dialog.ButtonSet');
goog.require('goog.ui.DatePicker');
goog.require('goog.ui.ProgressBar');
goog.require('goog.ui.Slider');
goog.require('goog.ui.decorate');
goog.require('goog.ui.editor.DefaultToolbar');
goog.require('goog.ui.editor.ToolbarController');
        </script>
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
                        <? if ($account == $selectedAccount): ?>
                        <li class='selected'>
                        <? else: ?>
                        <li>
                        <? endif; ?>
                        <?$username = urlencode($account->getName());?>
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
                    <? $username = urlencode($selectedAccount->getName());?>
                    <span class="header">
                        <span>Tasks</span>
                        <a id="add-task" href="/<?= $username ?>/new/">
                        <span class="add-item-outer">
                            <span class="add-item"></span>
                        </span>
                        </a>
                    </span>
                    <ul id="tasks-list" class="list">
                    <? foreach($tasks as $task): ?>
                        <? if ($task->getUri() == $selectedTask->getUri() && 
                                !$copy && !$new): ?>
                        <li class='selected'>
                        <? else: ?>
                        <li>
                        <? endif; ?>
                        <? $username = urlencode($selectedAccount->getName());?>
                        <? $tasknum = urlencode($task->getId()); ?>
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
        <? if ($edit and !$new): ?>
            <?= Task::toEditHtml($selectedAccount, $selectedTask, $copy); ?>
        <? elseif ($edit and $new): ?>
            <?= Task::toEditHtml($selectedAccount); ?>
        <? else: ?>
            <? if ($selectedTask != null): ?>
                <? $accNum = urlencode($selectedAccount->getName()); ?>
                <? $taskNum = urlencode($selectedTask->getId()); ?>
                <? $taskname = Util::urlifyTaskName($selectedTask->getName());?>
                <div class='edit-buttons'>
                <div id='edit-task-link-wrapper'>
                <div id='edit-task-link' 
                   class='goog-custom-button goog-custom-button-collapse-right'>
                    <a id='edit-task-link-inner' 
                       href='/<?=$accNum?>/<?=$taskNum?>/<?=$taskname?>/edit/'>
                        Edit
                    </a>
                </div><div id='create-copy-link' 
                   class='goog-custom-button goog-custom-button-collapse-left'>
                    <a id='create-copy-link-inner'
                        href='/<?=$accNum?>/<?=$taskNum?>/<?=$taskname?>/copy/'>
                        Copy
                    </a>
                </div>
                </div>
                <div id='delete-task-link' class='goog-custom-button'>
                    <a id='delete-task-link-inner' 
                      href='/<?=$accNum?>/<?=$taskNum?>/<?=$taskname?>/delete/'>
                        Delete
                    </a>
                </div>
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
        <script type='text/javascript' src='/js/tasklist.js'></script>
    </body>
</html>
