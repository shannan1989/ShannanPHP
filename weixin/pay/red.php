<?php
if (empty($_COOKIE['wx_openid'])) {
    die;
}
$wx_id = $_COOKIE['wx_openid'];

$wepay = new WeixinPay();
$ret = $wepay->sendRedPack('山南小站', $wx_id, 100, '新年快乐！', '新年活动', '新年红包');
if ($ret) {
    echo json_encode(array('errcode' => 0, 'msg' => '领取成功'), JSON_UNESCAPED_UNICODE);
} else {
    echo json_encode(array('errcode' => 1, 'msg' => '领取失败'), JSON_UNESCAPED_UNICODE);
}
die;
