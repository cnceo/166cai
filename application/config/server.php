<?php
/**
 * Created by Huxiaoming.
 * User: huxiaoming
 * Date: 2017/10/19
 * Time: 9:05
 * 微服务配置信息
 */
$config['server']['port']  =  10245;
if (ENVIRONMENT === 'production')
{
    $config['server']['ip'] = '172.16.0.38';
}
elseif (ENVIRONMENT === 'checkout')
{
    $config['server']['ip'] = '172.16.0.39';
}
else
{
    $config['server']['ip'] = '192.168.80.128';
}