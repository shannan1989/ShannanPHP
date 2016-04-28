<?php
define('TOKEN', '');
define('APP_ID', '');
define('APP_SECRET', '');

class WeixinChat {

    //valid signature
    public function valid() {
        $tmpArr = array(TOKEN, $_GET['timestamp'], $_GET['nonce']);
        sort($tmpArr);
        $signature = sha1(implode($tmpArr));
        if ($signature == $_GET['signature']) {
            echo $_GET['echostr'];
        }
        exit;
    }

    /**
     * 对微信服务器转发过来的消息进行处理并返回给用户的响应消息
     */
    public function responseMsg() {
        $strReceived = $GLOBALS['HTTP_RAW_POST_DATA'];
        if (!empty($strReceived)) {
            $objReceived = simplexml_load_string($strReceived, 'SimpleXMLElement', LIBXML_NOCDATA);
            $msg_type = trim($objReceived->MsgType);
            switch ($msg_type) {
                case 'event':
                    $result = $this->receiveEvent($objReceived);
                    break;
                case 'text':
                    $result = $this->receiveText($objReceived);
                    break;
                default :
                    $result = '';
                    break;
            }
            echo $result;
        } else {
            echo '';
        }
        die;
    }

    /**
     * 处理event类型的消息
     * @param object $objReceived
     * @return string
     */
    private function receiveEvent($objReceived) {
        $strResponse = '';
        switch ($objReceived->Event) {
            case 'subscribe':
                $strResponse = '欢迎关注';
                if (!empty($objReceived->EventKey)) {
                    $strResponse = '关注二维码 场景' . $objReceived->EventKey;
                }
                break;
            case 'SCAN':
                if (empty($objReceived->EventKey)) {
                    $strResponse = '欢迎扫码';
                } else {
                    $strResponse = '扫描二维码 场景' . $objReceived->EventKey;
                }
                //要实现统计分析，则需要扫描事件写入数据库，这里可以记录 EventKey及用户OpenID，扫描时间
                break;
            case 'VIEW':
                //点击菜单跳转链接时的事件推送
                break;
            case 'CLICK':
                //点击菜单拉取消息时的事件推送
                $strResponse = '';
                switch ($objReceived->EventKey) {
                    case 'NEW_ACTIVITY':
                        $strResponse = '最新活动尚未开启，敬请期待';
                        break;
                    default:
                        break;
                }
                break;
            default:
                break;
        }
        $response = $this->transmitText($objReceived, $strResponse);
        return $response;
    }

    private function receiveText($objReceived) {
        $text = $objReceived->Content;
        //根据用户发送的文字中的关键词来进行处理
        strpos($text, '关键词');
        $strResponce = '请稍后，管理员马上就到~';
        $response = $this->transmitText($objReceived, $strResponce);
        return $response;
    }

    private function transmitText($objReceived, $strResponce) {
        $textTpl = "
<xml>
<ToUserName><![CDATA[%s]]></ToUserName>
<FromUserName><![CDATA[%s]]></FromUserName>
<CreateTime>%s</CreateTime>
<MsgType><![CDATA[text]]></MsgType>
<Content><![CDATA[%s]]></Content>
</xml>";
        return sprintf($textTpl, $objReceived->FromUserName, $objReceived->ToUserName, time(), $strResponce);
    }

    private function transmitImage($objReceived, $mediaId) {
        $imageTpl = "
<xml>
<ToUserName><![CDATA[%s]]></ToUserName>
<FromUserName><![CDATA[%s]]></FromUserName>
<CreateTime>%s</CreateTime>
<MsgType><![CDATA[image]]></MsgType>
<Image>
    <MediaId><![CDATA[%s]]></MediaId>
</Image>
</xml>";
        return sprintf($imageTpl, $objReceived->FromUserName, $objReceived->ToUserName, time(), $mediaId);
    }

    private function uploadMedia($type, $file) {
        $fields['media'] = '@' . $file;
        $access_token = $this->getAccessToken();
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, 'http://file.api.weixin.qq.com/cgi-bin/media/upload?access_token=' . $access_token . '&type=' . $type);
        curl_setopt($ch, CURLOPT_SSLVERSION, CURL_SSLVERSION_TLSv1);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $s = curl_exec($ch);
        $s1 = json_decode($s, true);
        $media_id = $s1['media_id'];
        curl_close($ch);
        return $media_id;
    }

    public function sendTemplateMsg($post) {
        $access_token = $this->getAccessToken();
        $api_url = 'https://api.weixin.qq.com/cgi-bin/message/template/send?access_token=' . $access_token;
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $api_url);
        curl_setopt($ch, CURLOPT_SSLVERSION, CURL_SSLVERSION_TLSv1);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $s = curl_exec($ch);
        curl_close($ch);
        $s1 = json_decode($s, true);
        return $s1;
    }

    /**
     * 生成带自定义场景值的公众号二维码
     * @param int $scene_id
     * @return array
     */
    public function createQrcode($scene_id) {
        $access_token = $this->getAccessToken();
        $api_url = 'https://api.weixin.qq.com/cgi-bin/qrcode/create?access_token=' . $access_token;
        $post = array(
            'expire_seconds' => 60 * 60,
            'action_name' => 'QR_SCENE',
            'action_info' => array('scene' => array('scene_id' => $scene_id)),
        );
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $api_url);
        curl_setopt($ch, CURLOPT_SSLVERSION, CURL_SSLVERSION_TLSv1);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($post, JSON_UNESCAPED_UNICODE));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $s = curl_exec($ch);
        curl_close($ch);
        $s1 = json_decode($s, true);
        $s1['qrcode'] = 'https://mp.weixin.qq.com/cgi-bin/showqrcode?ticket=' . urlencode($s1['ticket']);
        return $s1;
    }

    public function getSignPackage() {
        // 注意 URL 一定要动态获取，不能 hardcode.
        $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? 'https://' : 'http://';
        $url = $protocol . $_SERVER[HTTP_HOST] . $_SERVER[REQUEST_URI];

        $timestamp = time();
        $nonceStr = $this->createNonceStr();
        $jsapiTicket = $this->getJsTicket();

        $string = 'jsapi_ticket=' . $jsapiTicket . '&noncestr=' . $nonceStr . '&timestamp=' . $timestamp . '&url=' . $url;
        $signature = sha1($string);

        $signPackage = array(
            'appId' => APP_ID,
            'nonceStr' => $nonceStr,
            'timestamp' => $timestamp,
            'url' => $url,
            'signature' => $signature,
            'rawString' => $string
        );
        return $signPackage;
    }

    private function createNonceStr($length = 16) {
        $chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
        $str = '';
        for ($i = 0; $i < $length; $i++) {
            $str .= substr($chars, mt_rand(0, strlen($chars) - 1), 1);
        }
        return $str;
    }

    private function getJsTicket() {
        $access_token = $this->getAccessToken();
        $api_url = 'https://api.weixin.qq.com/cgi-bin/ticket/getticket?access_token=' . $access_token . '&type=jsapi';
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $api_url);
        curl_setopt($ch, CURLOPT_SSLVERSION, CURL_SSLVERSION_TLSv1);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $s = curl_exec($ch);
        $s1 = json_decode($s, true);
        $js_ticket = $s1['ticket'];
        curl_close($ch);

        return $js_ticket;
    }

    private function getAccessToken() {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=" . APP_ID . "&secret=" . APP_SECRET);
        curl_setopt($ch, CURLOPT_SSLVERSION, CURL_SSLVERSION_TLSv1);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $s = curl_exec($ch);
        $s1 = json_decode($s, true);
        $access_token = $s1['access_token'];
        curl_close($ch);

        return $access_token;
    }

}
