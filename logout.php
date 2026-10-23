<?php
session_start();
$_SESSION = [];       // kosongkan semua data session
session_destroy();    // hancurkan session di server
header("Location: login.php");
exit;