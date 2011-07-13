<? require_once('classes.php'); ?>

<? if (isset($_GET['user'])): ?>
    <? $user = Account::getUserByUserName(urldecode($_GET['user'])); ?>
    <? foreach(Util::getTasksForAccount($user) as $task): ?>
<li>
    <a href="?user=<?= $user->getName() ?>&task=<?= $task->getTaskNumber() ?>">
        <span class="task-name">
            <?= $task->getName() ?>
        </span>
    </a>
</li>    
    <? endforeach; ?>
<? else: ?>
    // TODO set error code somehow to indicate that request failed
<? endif; ?>
