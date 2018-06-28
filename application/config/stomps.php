<?php
defined('BASEPATH') OR exit('No direct script access allowed');

if (ENVIRONMENT === 'production')
{
    $config['stomp'] = array(
        'url'       => 'tcp://172.16.0.37:61613',
        'user'        => 'system',
        'password'    => 'km#888cai%manager',
        'queueName'   => 'userTask',
    );
}
elseif (ENVIRONMENT === 'checkout')
{
    $config['stomp'] = array(
        'url'       => 'tcp://172.16.0.39:61613',
        'user'        => 'system',
        'password'    => 'km#166cai%manager',
        'queueName'   => 'userTask',
    );
}
else
{
    $config['stomp'] = array(
        'url'       => 'tcp://123.59.105.39:61613',
        'user'        => 'system',
        'password'    => 'km#166cai%manager',
        'queueName'   => 'userTask',
    );
}

$config['stomp']['queueNames'] = array(
        'usertag' => 'userTagQueue',
);
