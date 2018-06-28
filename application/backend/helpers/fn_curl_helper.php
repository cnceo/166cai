<?php

/**
 * Copyright (c) 2014,上海二三四五网络科技股份有限公司
 * 摘    要：curl 帮助
 * 作    者：wangl@2345.com
 * 修改日期：2014.11.06
 */
if (!defined('BASEPATH'))
    exit('No direct script access allowed');
/**
 * 参    数：$url 链接地址
 *                $options 参数
 * 作    者：wangl
 * 功    能：curl获取内容
 * 修改日期：2014.09.04
 */
if (!function_exists('curl_get_contents'))
{

    function curl_get_contents($url, $options = array())
    {
        $default = array(
            CURLOPT_URL => $url,
            CURLOPT_HEADER => 0,
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_USERAGENT => "Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/35.0.1916.153 Safari/537.36",
            CURLOPT_CONNECTTIMEOUT => 10,
            CURLOPT_TIMEOUT => 10,
            CURLOPT_IPRESOLVE => CURL_IPRESOLVE_V4
        );
        foreach ($options as $key => $value)
        {
            $default[$key] = $value;
        }
        $ch = curl_init();
        curl_setopt_array($ch, $default);
        $result = curl_exec($ch);
        curl_close($ch);
        return $result;
    }

}
/* 参    数：$url 链接地址
 *                $params 链接参数
 *                $options 参数
 * 作    者：wangl
 * 功    能：curl get获取内容
 * 修改日期：2014.09.04
 */
if (!function_exists('curl_http_get'))
{

    function curl_http_get($url, $params = array(), $options = array())
    {
        $paramsFMT = array();
        foreach ($params as $key => $val)
        {
            $paramsFMT[] = $key . "=" . urlencode($val);
        }
        return curl_get_contents($url . ($paramsFMT ? ("?" . join("&", $paramsFMT)) : ""), $options);
    }

}
/* 参    数：$url 链接地址
 *                $params 链接参数
 *                $options 参数
 * 作    者：wangl
 * 功    能：curl post获取内容
 * 修改日期：2014.09.04
 */
if (!function_exists('curl_http_post'))
{

    function curl_http_post($url, $params = array(), $options = array())
    {
        $paramsFMT = array();
        foreach ($params as $key => $val)
        {
            $paramsFMT[] = $key . "=" . urlencode($val);
        }
        $options[CURLOPT_POST] = 1;
        $options[CURLOPT_POSTFIELDS] = join("&", $paramsFMT);
        return curl_get_contents($url, $options);
    }

}