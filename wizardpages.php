<?
require('classes.php');
$page = 1;
if (isset($_GET['page'])) {
    $page = (int) $_GET['page'];
}
echo Wizard::getPage($page);
?>
