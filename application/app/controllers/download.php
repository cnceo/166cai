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
        $c = $this->input->get("c", true);
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
                $this->load->model('channel_model', 'Channel');
                $channel = $this->Channel->getById(intval($c));
                if (isset($channel['ios_download']) && $channel['ios_download'])
                {
                    header('Location: ' . $channel['ios_download']);
                }
                else
                {
                   header('Location: https://itunes.apple.com/cn/app/166cai-piao-shuang-se-qiu/id1108268497?mt=8'); 
                }                
            }
            else
            {
                if(!empty($c) && $c == 'zcq')
                {
                    header('Location: ' . $this->config->item('download_url') . 'source/download/android/app-release_zucaiquan.apk');
                }
                elseif($c == 'shoujiliulanqi')
                {
                    header('Location: ' . $this->config->item('download_url') . 'source/download/android/app-release-shoujiliulanqi.apk');
                }
                elseif($c == 'invite_activity')
                {
                    header('Location: ' . $this->config->item('download_url') . 'source/download/android/app-release-invite_activity.apk');
                }
                elseif($c == 'm_default')
                {
                    header('Location: ' . $this->config->item('download_url') . 'source/download/android/app-release-m_default.apk');
                }
                elseif($c == 'jcjj')
                {
                    header('Location: ' . $this->config->item('download_url') . 'source/download/android/app-release-jcjj.apk');
                }
                else
                {
                    $defaultChannels = $this->config->item('defaultChannel');
                    $c = intval($c) ? intval($c) : $defaultChannels['app'];
                    $this->load->model('channel_model', 'Channel');
                    $channel = $this->Channel->getById($c);
                    if (isset($channel['app_path']) && $channel['app_path'])
                    {
                        $downHref = substr($this->config->item('download_url'), 0, -1) . $channel['app_path'];
                    }
                    else
                    {
                        $channel = $this->Channel->getById($defaultChannels['app']);
                        $downHref = substr($this->config->item('download_url'), 0, -1) . $channel['app_path'];
                    }
                    header('Location: '.$downHref);
                }
            }
        }
        
    }

    /*
     * APP 帮助中心 - 下载
     * @date:2015-05-21
     */
    public function about()
    {
        $backUrl = $this->input->get("backUrl");
        $backUrl = $backUrl ? $backUrl : $this->config->item('pages_url');
        $info = array(
            'backUrl' => $backUrl,
        );
        $this->load->view('help/about', $info);
    }
}