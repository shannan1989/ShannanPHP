<?php
include_once __DIR__ . '/data_utils.php';

$user_id = currentUserId();
$expire24h = strtotime('+24 hours');

$errcode = 0;
$msg = '';
$items = [];
$type = empty($_REQUEST['type']) ? '' : $_REQUEST['type'];
switch ($type) {
    case 'list':
        mustLogin();
        $items[] = array('type' => 'list', 'data' => getList(), 'expire' => $expire24h);
        break;
    case 'item':
        mustLogin();
        if (empty($_REQUEST['id'])) {
            $errcode = 100;
            $msg = '错误的请求';
        } else {
            $id = intval($_REQUEST['id']);
            $item = getItem($id);
            $items[] = array('type' => 'item', 'item_id' => $id, 'data' => $item, 'expire' => $expire24h);
        }
        break;
    default:
        $errcode = 401;
        $msg = '不支持的消息类型';
        break;
}
$ret = array(
    'errcode' => $errcode,
    'msg' => $msg,
    'servertime' => time(),
    'data' => array('user_id' => $user_id, 'timestamp' => time(), 'items' => $items));
echo json_encode($ret, JSON_UNESCAPED_UNICODE);
