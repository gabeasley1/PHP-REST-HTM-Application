<?
session_start();

function getPage($page) {
    switch($page) {
    case 1:
?>
<div class='page-1'>
<h1>Welcome!</h1>
<p>In this tutorial, we will walk you through step-by-step how to work with this client for the Human Task Management application.  By the end of it, you should be set up well enough to have an account set up and be able to view all of the tasks for that account.</p>
<p>Please click the "next" button in order to keep going.</p>
<a href='/wizard/page/2' rel='next' class='next-link'>Next</a>
</div>
<?
        break;

    case 2:
?>
<div class='page-2'>
<h1>Accounts</h1>
<p>In order to do anything with this application, you will need to have at least one account set up on the REST server and attach it to your account on this server.  Use the following form to set this up now.</p>
<div id='flash'>
<? if (isset($_SESSION['flash'])) echo $_SESSION['flash']; ?>
</div>
<form action='new_account_wiz.php' method='POST'>
    <table>
        <tr>
            <td><label for='username' id='username-label'>Username</label></td>
            <td>
<?
$name = '';
if (isset($_SESSION['username'])) {
    $name = $_SESSION['username'];
}
?>
                <input type='text' name='username' id='username'
                       value='<?=$name?>' />
            </td>
        </tr>
        <tr>
            <td><label for='uri' id='uri-label'>Link</label></td>
            <td>
<?
$uri = '';
if (isset($_SESSION['uri'])) {
    $name = $_SESSION['uri'];
}
?>
                <input type='text' name='uri' id='uri'
                       value='<?=$uri?>' />
            </td>
        </tr>
        <tr>
            <td><label for='password' id='password-label'>Password</label></td>
            <td>
                <input type='password' name='password' id='password' />
            </td>
        </tr>
    </table>
<a href='/wizard/page/1' rel='prev' class='prev-link'>Prev</a>
<input type='submit' href='/wizard/page/3' rel='next' class='next-link'
       value='Next' />
</form>
</div>
<?
        break;
    case 3:
?>
<div class='page-3'>
<h1>Great Job!</h1>
<?
        $name = $util->getAccounts();
        if ($name == null or count($name) == 0) {
            $name = 'Johnny';
        } else {
            $name = $name[0]->getName();
        }
?>
<p>Now every time you log in to the application, you will see <?=$name?>'s tasks, along with anyone elses tasks that you add to your account on this server.  Have a group account with shared tasks?  No problem!  Just have everyone add the same account and any tasks that you add, all of your partners will be able to see as well.  You can also see any tasks that they add for the account.</p>
<p>Click on the "finish" button to go to your homescreen now and start managing all of our tasks!</p>
<a href='/wizard/page/1' rel='prev' class='prev-link'>Prev</a>
<a href='/' rel='last' class='next-link'>Next</a>
</div>
<?
        break;
    };
}?>
