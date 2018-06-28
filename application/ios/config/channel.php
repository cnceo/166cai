<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

if (ENVIRONMENT === 'production')
{
	$config['channel'] = array('10091');
}
else
{
    $config['channel'] = array('91');
}
