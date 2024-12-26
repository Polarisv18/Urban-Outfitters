<?php
session_start();
$_SESSION['mannequin_items'] = [];
header('Location: mannequin.php');
exit;
?>
