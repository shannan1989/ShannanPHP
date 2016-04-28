<?php
define('APP_ID', '');
define('MCH_ID', '');
define('MCH_KEY', '');

class WeixinPay {

    /**
     * 发微信红包
     * @param string $send_name 红包发送者名称
     * @param string $open_id 接受红包的用户
     * @param int $amount 红包金额，单位分
     * @param string $wishing 红包祝福语
     * @param string $act_name 活动名称
     * @param string $remark 备注信息
     * @return boolean
     */
    public function sendRedPack($send_name, $open_id, $amount, $wishing, $act_name, $remark) {
        $ip = ''; //实现获取IP
        $data = array(
            'mch_billno' => 'shannan_' . date('Ymd') . uniqid(),
            'mch_id' => MCH_ID,
            'wxappid' => APP_ID,
            'send_name' => $send_name,
            're_openid' => $open_id,
            'total_amount' => $amount,
            'total_num' => 1,
            'wishing' => $wishing,
            'client_ip' => $ip,
            'act_name' => $act_name,
            'remark' => $remark,
            'nonce_str' => $this->createNonceStr(32),
        );
        unset($data['sign']);
        ksort($data);
        $s = '';
        foreach ($data as $key => $value) {
            $s = "{$s}{$key}={$value}&";
        }
        $s.='key=' . MCH_KEY;
        $sign = strtoupper(md5($s));
        $tpl = "
<xml>
    <sign><![CDATA[$sign]]></sign>
    <mch_billno><![CDATA[{$data['mch_billno']}]]></mch_billno>
    <mch_id><![CDATA[{$data['mch_id']}]]></mch_id>
    <wxappid><![CDATA[{$data['wxappid']}]]></wxappid>
    <send_name><![CDATA[{$data['send_name']}]]></send_name>
    <re_openid><![CDATA[{$data['re_openid']}]]></re_openid>
    <total_amount><![CDATA[{$data['total_amount']}]]></total_amount>
    <total_num><![CDATA[{$data['total_num']}]]></total_num>
    <wishing><![CDATA[{$data['wishing']}]]></wishing>
    <client_ip><![CDATA[{$data['client_ip']}]]></client_ip>
    <act_name><![CDATA[{$data['act_name']}]]></act_name>
    <remark><![CDATA[{$data['remark']}]]></remark>
    <nonce_str><![CDATA[{$data['nonce_str']}]]></nonce_str>
</xml>";

        $ret = $this->curl_post_ssl('https://api.mch.weixin.qq.com/mmpaymkttransfers/sendredpack', $tpl);

        $t = simplexml_load_string($ret);
        if ($t->return_code == 'SUCCESS' && $t->result_code == 'SUCCESS') {
            return true;
        } else {
            return false;
        }
    }

    private function curl_post_ssl($url, $vars, $second = 30, $headers = array()) {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_TIMEOUT, $second);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);

        $cert_dir = ''; //改为证书文件所在目录
        //以下两种方式需选择一种
        //第一种方法，cert 与 key 分别属于两个.pem文件
        //默认格式为PEM，可以注释
        curl_setopt($ch, CURLOPT_SSLCERTTYPE, 'PEM');
        curl_setopt($ch, CURLOPT_SSLCERT, $cert_dir . 'wxpay/apiclient_cert.pem');
        //默认格式为PEM，可以注释
        curl_setopt($ch, CURLOPT_SSLKEYTYPE, 'PEM');
        curl_setopt($ch, CURLOPT_SSLKEY, $cert_dir . 'wxpay/apiclient_key.pem');
        //第二种方式，两个文件合成一个.pem文件
        //curl_setopt($ch, CURLOPT_SSLCERT, $cert_dir . 'wxpay/all.pem');

        if (count($headers) >= 1) {
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        }

        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $vars);
        $data = curl_exec($ch);
        if ($data) {
            curl_close($ch);
            return $data;
        } else {
            curl_close($ch);
            return false;
        }
    }

    private function createNonceStr($length = 16) {
        $chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
        $str = '';
        for ($i = 0; $i < $length; $i++) {
            $str .= substr($chars, mt_rand(0, strlen($chars) - 1), 1);
        }
        return $str;
    }

}
