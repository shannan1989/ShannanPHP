<?php
if (empty($_COOKIE['wx_openid'])) {
    header('Location:auth.php');
    die;
}

$openid = $_COOKIE['wx_openid'];
