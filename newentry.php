<?
/**
 * Handles forms for creating a new task or updating an existing one.
 * @package PhpHtmRestApplicationTaskCreation
 */

require_once('classes.php');

foreach ($_POST as $k=>$v) {
    if ($k != 'tags') $v = trim($v);
    if (empty($v) and $v != 0 and $v != '0') $_POST[$k] = null;
}

$name = $_POST['name'];
$priority = $_POST['priority'];
$status = $_POST['status'];
$activation = $_POST['start-date'] . "T" . $_POST['start-time'];
$expiration = $_POST['expiration-date'] . "T" . $_POST['expiration-time'];
$addition = $_POST['addition-date'] . "T". $_POST['addition-time'];
$modification = $_POST['modification-date'] . "T" . $_POST['modification-time'];
$progress = $_POST['progress'];
$tags = empty($_POST['tags']) ? array() : $_POST['tags'];
$type = $_POST['type'];
$uri = $_POST['uri'];
$username = $_POST['user'];
$method = strtoupper($_POST['method']);
$details = $_POST['details'];
$etag = $_POST['etag'];
$tasknumber = $_POST['tasknumber'];
if ($tasknumber != null) {
    $tasknumber = (int) $tasknumber;
}

$modificationDT = DateTime::createFromFormat('m/d/Y\TG:i', $modification);
$modification = $modificationDT->format('Y-m-d\TH:i:sP');

$additionDT = DateTime::createFromFormat('m/d/Y\TG:i', $addition);
$addition = $additionDT->format('Y-m-d\TH:i:sP');

$expirationDT = DateTime::createFromFormat('m/d/Y\TG:i', $expiration);
$expiration = $expirationDT->format('Y-m-d\TH:i:sP');

$activationDT = DateTime::createFromFormat('m/d/Y\TG:i', $activation);
$activation = $activationDT->format('Y-m-d\TH:i:sP');

$user = Account::getUserByUserName($username);
$response = null;
if ($method == "POST") {
    $response = Util::AddEntry($user, $name, $priority, $status, null, $activation, 
        $expiration, $addition, $modification, $progress, null, $tags, $type, 
        $details);
} else if ($method == "PUT") {
    $response = Util::EditEntry($user, $name, $priority, $status, null, $activation,
        $expiration, $addition, $modification, $progress, null, $tags, $type, 
        $details, $uri, $etag);
} else {
    die("Unsupported method $method.");
}


if ((int) ($response->getStatus()/100) == 2) {
    $_SESSION['flash'] = "Task successfully ". 
        (($method == "POST") ? "created" : "updated") . ".";
    $username = urlencode($username);
    if ($tasknumber == -1) {
        $redirect = "/$username/";
    } else {
        $redirect = "/$username/$tasknumber/";
    }
} else {
    $_SESSION['flash'] = "Oops. ".$response->getReasonPhrase();
    $redirect = isset($_SERVER['HTTP_REFERRER']) ? $_SERVER['HTTP_REFERRER'] :
                                                   "/tasklist/";
}
header("Location: $redirect");
?>
