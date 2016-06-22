<?php

$user_check = $_SESSION['UserID'];

if ($user_check == '') {
    header("Location: login.php");
}
?>
