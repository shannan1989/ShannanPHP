<?php
$flag = true;
if (empty($_GET['code'])) {
    $flag = false;
} else {
    $ret = WeixinSns::getAccessToken(trim($_GET['code']));
    if (isset($ret['access_token'])) {//获取到用户信息
        setcookie('wx_openid', $ret['openid']);
        header('Location:index.php');
        die;
    } else {
        $flag = false;
    }
}

$state = 'auth';
if ($flag == false) {
    if (empty($_GET['state']) || $_GET['state'] != $state) {
        header('Location:' . WeixinSns::getAuthUrl('http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'], $state));
        die;
    }
}