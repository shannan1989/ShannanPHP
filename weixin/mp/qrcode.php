<?php
$scene_id = empty($_GET['scene_id']) ? 0 : intval($_GET['scene_id']);
$wechat = new WeixinChat();
$ret = $wechat->createQrcode($scene_id);

echo '<img src="' . $ret['qrcode'] . '"/>';
