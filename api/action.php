<?php
include_once __DIR__ . '/data_utils.php';

$user_id = currentUserId();
$expire24h = strtotime('+24 hours');

$errcode = 0;
$msg = '';
$items = [];
$type = empty($_REQUEST['type']) ? '' : $_REQUEST['type'];
switch ($type) {
    case 'check_update':
        break;
    case 'user_login':
        $items[] = array('type' => 'token', 'data' => array('token' => getToken($user_id)), 'expire' => $expire24h);
        break;
    default :
        $errcode = 401;
        $msg = '不支持的消息类型！';
        break;
}