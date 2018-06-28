<?php
defined('BASEPATH') OR exit('No direct script access allowed');

if (ENVIRONMENT === 'production')
{
    $config['redis'] = array(
        'socket_type' => 'tcp',
        'host'        => '172.16.0.34',
        'password'    => 'rc_redis',
        'port'        => '6379',
        'timeout'     => 10,
    );

    $config['slave'] = array(
        'socket_type' => 'tcp',
        'host'        => '172.16.0.40',
        'password'    => 'rc_redis',
        'port'        => '6379',
        'timeout'     => 10,
    );
}
elseif (ENVIRONMENT === 'checkout')
{
    $config['redis'] = array(
        'socket_type' => 'tcp',
        'host'        => '172.16.0.39',
        'password'    => 'redis',
        'port'        => '6379',
        'timeout'     => 10,
    );

    $config['slave'] = array(
        'socket_type' => 'tcp',
        'host'        => '172.16.0.39',
        'password'    => 'redis',
        'port'        => '6379',
        'timeout'     => 10,
    );
}
else
{
    $config['redis'] = array(
        'socket_type' => 'tcp',
        'host'        => '123.59.105.39',
        'password'    => 'redis',
        'port'        => '6379',
        'timeout'     => 10,
    );

    $config['slave'] = array(
        'socket_type' => 'tcp',
        'host'        => '123.59.105.39',
        'password'    => 'redis',
        'port'        => '6379',
        'timeout'     => 10,
    );
}
