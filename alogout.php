<?php
session_start();
$_SESSION = array();
session_destroy();
echo "<script>window.location.href = 'adlf.html';</script>";
exit;
?>
