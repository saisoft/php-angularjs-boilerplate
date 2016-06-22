<?php

session_start();

if (!isset($_SESSION['LOGIN_STATUS'])) {
    header('Location: login');
} else {
    if (isset($_SESSION['LOGIN_EMP'])) {
        header('Location: employeehome');
    } else if (isset($_SESSION['LOGIN_EMPR'])) {
        header('Location: employerhome');
    } else if (isset($_SESSION['LOGIN_ADM'])) {
        header('Location: adminhome');
    }
}
?>
