<?php
require_once(__DIR__ . '/../includes/config.php');
session_destroy();
header('Location: ' . PUBLIC_PATH . '/login.php?logout=exito');
exit();
?>
