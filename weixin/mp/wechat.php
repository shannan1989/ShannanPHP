<?php
$wechat = new WeixinChat();
if (isset($_GET['echostr'])) {
    $wechat->valid();
} else {
    $wechat->responseMsg();
}