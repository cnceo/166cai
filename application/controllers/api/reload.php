<?php

/**
 * 重新load
 */
require_once APPPATH . '/core/CommonController.php';
class Reload extends CommonController
{
    public function __construct()
    {
        parent::__construct();
    }

	public function process($type='pro')
	{
    	if(in_array(UCIP,array('120.132.33.194','123.59.105.39')))
    	{
            $types = array('pro' => 'clii_process', 'srv' => 'clii_server_data');
            touch("/opt/case/www.166cai.com/application/logs/plock/restart/{$types[$type]}.php.reload");
            echo 1;
    	}else{
            echo 0;
        }

	}
}