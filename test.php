<?
$users = array(array('a','A'),array('b','B'),array());
$pointer = $users[2];
$pointer[] = 'c';
$pointer[] = 'C';
var_dump($users);
phpinfo();
?>
