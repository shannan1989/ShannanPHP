<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <title>微信网页js接口</title>
    </head>
    <body>
        <?php
        $wechat = new WeixinChat();
        $sign_package = $wechat->getSignPackage();
        ?>
        <script src="http://res.wx.qq.com/open/js/jweixin-1.0.0.js"></script>
        <script>
            $(document).ready(function () {
                wx.config({
                    debug: false,
                    appId: '<?php echo $sign_package['appId']; ?>',
                    timestamp: <?php echo $sign_package['timestamp']; ?>,
                    nonceStr: '<?php echo $sign_package['nonceStr']; ?>',
                    signature: '<?php echo $sign_package['signature']; ?>',
                    jsApiList: [
                        'onMenuShareTimeline',
                        'onMenuShareAppMessage'
                    ]
                });
                wx.ready(function () {
                    wx.hideOptionMenu();
                });
            });
        </script>
    </body>
</html>
