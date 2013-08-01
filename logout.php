<?php
    session_start();
    unset($_SESSION["authorized"]);
    unset($_SESSION["username"]);
    session_destroy();
    session_write_close();
    session_regenerate_id();
    header('Location: index.php');
?>
