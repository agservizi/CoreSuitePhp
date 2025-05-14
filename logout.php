<?php
require_once 'auth.php';

$auth = new Auth();
$auth->logout();

// Reindirizza al login
header('Location: login.php');
exit;
?>
