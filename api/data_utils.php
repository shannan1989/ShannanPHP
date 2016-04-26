<?php

function getToken($user_id) {
    return $user_id . '-' . md5("U2FsdGVkX18WHgi7\t{$user_id}");
}

function currentUserId() {
    if (isset($_REQUEST['token']) == false) {
        return 0;
    }
    $token = explode('-', $_REQUEST['token']);
    if (count($token) != 2) {
        return 0;
    }

    $vc = md5("U2FsdGVkX18WHgi7\t$token[0]");
    if ($vc != $token[1]) {
        return 0;
    }

    return intval($token[0]);
}

function mustLogin() {
    $user_id = currentUserId();
    if (empty($user_id)) {
        $ret = array(
            'errcode' => 400,
            'msg' => '无效登录信息',
            'timestamp' => time(),
            'data' => array('instructor_id' => 0)
        );
        echo json_encode($ret, JSON_UNESCAPED_UNICODE);
        exit;
    }
}

function getList() {
    
}

function getItem($id) {
    
}
