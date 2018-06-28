<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

if (ENVIRONMENT === 'production')
{
	$config['channel'] = array('10092', '10093', '10096', '10204', '10207');
}
else
{
    $config['channel'] = array();
}
