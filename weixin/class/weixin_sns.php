<?php
define('APP_ID', 'wx6f81e35c0534ea90');
define('APP_SECRET', 'b816de1db85cc64f8980709bbaf80538');

/**
 * 网页授权获取用户信息，与cgi不同
 */
class WeixinSns {

    static function getAccessToken($code) {
        $url = 'https://api.weixin.qq.com/sns/oauth2/access_token?code=' . $code . '&grant_type=authorization_code&appid=' . APP_ID . "&secret=" . APP_SECRET;
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_SSLVERSION, CURL_SSLVERSION_TLSv1);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $s = curl_exec($ch);
        $s1 = json_decode($s, true);
        curl_close($ch);
        return $s1;
    }

    static function getUserInfo($access_token, $open_id) {
        $url = 'https://api.weixin.qq.com/sns/userinfo?access_token=' . $access_token . '&openid=' . $open_id . '&lang=zh_CN';
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_SSLVERSION, CURL_SSLVERSION_TLSv1);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $s = curl_exec($ch);
        $s1 = json_decode($s, true);
        curl_close($ch);
        return $s1;
    }

    static function getAuthUrl($redirect_uri, $state) {
        return 'https://open.weixin.qq.com/connect/oauth2/authorize?appid=' . APP_ID . '&redirect_uri=' . urlencode($redirect_uri) . '&response_type=code&scope=snsapi_base&state=' . $state . '#wechat_redirect';
    }

    static function getHighAuthUrl($redirect_uri, $state) {
        return 'https://open.weixin.qq.com/connect/oauth2/authorize?appid=' . APP_ID . '&redirect_uri=' . urlencode($redirect_uri) . '&response_type=code&scope=snsapi_userinfo&state=' . $state . '#wechat_redirect';
    }

}
