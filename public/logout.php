<?php
session_start();
session_destroy();
header('Location: /git/login/Proyecto3ro/public/login.php?logout=exito');
exit();
?>
