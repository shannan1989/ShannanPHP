<?php

/**
 * 获取浏览器Client的IP地址
 * @return string
 */
function getClientIP() {
    $ip = '';
    if (getenv('HTTP_CLIENT_IP') && strcasecmp(getenv('HTTP_CLIENT_IP'), 'unknown')) {
        $ip = getenv('HTTP_CLIENT_IP');
    } else if (getenv('HTTP_X_FORWARDED_FOR') && strcasecmp(getenv('HTTP_X_FORWARDED_FOR'), 'unknown')) {
        $ip = getenv('HTTP_X_FORWARDED_FOR');
    } else if (getenv('REMOTE_ADDR') && strcasecmp(getenv('REMOTE_ADDR'), 'unknown')) {
        $ip = getenv('REMOTE_ADDR');
    } else if (isset($_SERVER['REMOTE_ADDR']) && $_SERVER['REMOTE_ADDR'] && strcasecmp($_SERVER['REMOTE_ADDR'], 'unknown')) {
        $ip = $_SERVER['REMOTE_ADDR'];
    }
    return $ip;
}

/**
 * 获取当前地址链接
 * @return string
 */
function getCurrentUrl() {
    $url = 'http://';
    if (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on') {
        $url = 'https://';
    }
    return $url . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
}

/**
 * 根据Client判断是否为搜索引擎爬虫
 * @return boolean
 */
function isSpider() {
    if (isset($_SERVER['HTTP_USER_AGENT'])) {
        return preg_match('/(Googlebot|bingbot|MJ12bot|spider)/i', $_SERVER['HTTP_USER_AGENT']);
    } else {
        return false;
    }
}
