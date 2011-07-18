<? require_once('classes.php'); ?>

<? if (isset($_GET['user'])): ?>
    <? $user = Account::getUserByUserName(urldecode($_GET['user'])); ?>
    <? foreach(Util::getTasksForAccount($user) as $task): ?>
        <li>
            <? $username = urlencode($user->getName()); ?>
            <? $tasknum  = urlencode($task->getTaskNumber()); ?>
            <? $taskname = Util::urlifyTaskName($task->getName()); ?>
            <a href="/<?= $username ?>/<?= $tasknum ?>/<?=$taskname?>/">
                <span class="task-name">
                    <?= $task->getName() ?>
                </span>
            </a>
        </li>    
    <? endforeach; ?>
<? else: ?>
    <?= $_SERVER['REQUEST_URI']; ?><br/>
    // TODO set error code somehow to indicate that request failed
<? endif; ?>
