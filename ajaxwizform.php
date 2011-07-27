<?
require('classes.php');
$results = array();
if (isset($_POST['username']) and isset($_POST['uri']) and 
        isset($_POST['password'])) {
    $util = new Util();        
    $username = $_POST['username'];
    $uri = $_POST['uri'];
    $password = $_POST['password'];
    $status = $util->addAccounts(new Account($username, $password, $uri));
    $success = false;
    $message = '';
    $account = null;
    switch ($status) {
    case AccountEntry::SUCCESS:
    case AccountEntry::ACCOUNT_EXISTS:
        $success = true;
        $message = "Account successfully created.";
        $account = array('name'=>$_POST['username'], 'uri'=>$_POST['uri']);
        break;
    case AccountEntry::ACCOUNT_NOT_FOUND:
        $success = false;
        $message = "Account not found.  Did you provide a bad link?";
    case AccountEntry::NOT_AUTHORIZED:
        $success = false;
        $message = "Bad username/password combination.";
    case AccountEntry::ACCOUNT_ALREADY_LINKED:
        $success = false;
        $message = "It looks like you have already linked that account.";
        break;
    default:
        $success = false;
        $message = "Oops! Looks like something went wrong on our end.  ".
            "Please try again.";
    }
    $results = array("success"=>$success, "reason"=>$message);
    if ($account != null) $results['account'] = $account;
} else {
    $results = array("success"=>false, "reason"=>"Failed to provide all ".
        "of the necessary data.");
}
echo json_encode($results);
?>
