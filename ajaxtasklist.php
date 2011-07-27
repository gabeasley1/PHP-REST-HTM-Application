<? 
/**
 * Retrieves a list of tasks for a user.
 * @todo {Andrew Hays} modify this so that it accepts a user param.  Probably
 *      through the session variable.
 */

require_once('classes.php'); ?>

<? if (isset($_GET['user'])): ?>
    <? $util = new Util(); ?>
    <? $user = $util->getAccountByName(urldecode($_GET['user'])); ?>
    <? foreach($util->getTasksForAccounts($user, false) as $task): ?>
        <li>
            <? $username = urlencode($user->getName()); ?>
            <? $tasknum  = urlencode($task->getId()); ?>
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
