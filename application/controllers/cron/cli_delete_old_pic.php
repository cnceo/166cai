<?php

/*
 * 删除7天前过期的图片.
 */
defined('BASEPATH') or exit('No direct script access allowed');

class Cli_Delete_Old_Pic extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('info_model', 'Info');
    }

    public function index()
    {
        $allNum = $this->Info->countOldPics();
        for ($i = 0; $i < $allNum; $i = $i + 5000)
        {
            $contents = $this->Info->getOldPics(5000, $i);
            $pic_urls = array();
            foreach ($contents as $content)
            {
                preg_match_all('/src=&quot;\/uploads\/info\/(.*?)&quot/is', $content['content'], $match);
                if (isset($match[1]))
                {
                    foreach ($match[1] as $pic)
                    {
                        $pic_urls[] = '/uploads/info/'.$pic;
                    }
                }
            }
            foreach ($pic_urls as $url)
            {
                $url = dirname(BASEPATH).$url;
                if (file_exists($url))
                {
                    unlink($url);
                }
            }
        }
    }
}
