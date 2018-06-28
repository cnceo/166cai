<?php

/*
 * APP 帮助中心
 * @date:2015-05-21
 */

class Download extends MY_Controller {
	
    public function __construct() 
    {
        parent::__construct();
    }

    /*
     * APP 帮助中心 - 下载
     * @date:2015-05-21
     */
    public function index()
    {
        // UA获取
        $useragent = $_SERVER['HTTP_USER_AGENT'];
        if( strpos($useragent, 'MicroMessenger') !== FALSE )
        {
            if( strpos($useragent, 'iPhone') !== FALSE || strpos($useragent, 'iPad') !== FALSE || strpos($useragent, 'Mac OS') !== FALSE)
            {
                $this->load->view('help/download_ios');
            }
            else
            {
                $this->load->view('help/download');
            }
        }
        else
        {
            if( strpos($useragent, 'iPhone') !== FALSE || strpos($useragent, 'iPad') !== FALSE || strpos($useragent, 'Mac OS') !== FALSE)
            {
                header('Location: https://itunes.apple.com/cn/app/166cai-piao-shuang-se-qiu/id1108268497?mt=8');
            }
            else
            {
                header('Location: ' . $this->config->item('pages_url') . 'source/download/android/app-release.apk');
            }
        }
        
    }
}