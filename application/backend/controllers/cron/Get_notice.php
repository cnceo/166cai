<?php

/**
 * Copyright (c) 2014,上海二三四五网络科技股份有限公司
 * 摘    要：抓取公告和新闻
 * 作    者：wangl@2345.com
 * 修改日期：2014.11.07
 */
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Get_notice extends CI_Controller
{
    private $host = 'http://cms.51caixiang.com';
    private $newsUrl = "/cxcms/restful/content/fragment/list";
    private $contentUrl = "/cxcms/restful/content/fragment/detail";
    private $reffer = "http://www.51caixiang.com/";
    
    public function __construct()
    {
        parent::__construct();
        if (php_sapi_name() != 'cli')
        {
            exit("no access");
        }
        $this->load->helper(array(
            "fn_curl",
            "fn_common"
        ));
        $this->load->model('Model_notice');
    }
    /**
     * 参    数：无
     * 作    者：wangl
     * 功    能：抓取彩象接口提供的新闻数据
     * 修改日期：2014.11.05
     */
    public function index()
    {
        $categorys = array(
            1,
            2,
            3,
            4
        );
        $options = array(
            CURLOPT_REFERER => $this->reffer
        );
        foreach ($categorys as $cate)
        {
            $result = curl_http_get($this->host . $this->newsUrl, array(
                "category" => $cate,
                "currentPage" => 1,
                "pageSize" => 10,
                "sort" => 1
            ), $options);
            $content = json_decode($result, true);
            
            if ($content['code'] == 1)
            {
                foreach ($content['data']['results'] as $key => $value)
                {
                    if ($value['id'] <= 0)
                    {
                        continue;
                    }
                    $result2 = curl_http_get($this->host . $this->contentUrl, array(
                        "fragmentId" => $value['id']
                    ), $options);
                    $content2 = json_decode($result2, true);
                    if ($content2['code'] != 1)
                    {
                        continue;
                    }
                    $content2 = $this->save_img($content2['data']['content']);
                    $addData = array(
                        "title" => str_hsc($value['title']),
                        "username" => "抓取",
                        "addTime" => $value['createTime'] / 1000,
                        "status" => 0,
                        "weight" => $value['weight'],
                        "category" => $value['category'],
                        "content" => str_hsc($content2),
                    );
                    $this->Model_notice->add($addData);
                }
                echo "{$cate} success";
            }
            else
            {
                echo "{$cate} failed";
            }
        }
    }
    /**
     * 参    数：无
     * 作    者：wangl
     * 功    能：保存图片
     * 修改日期：2014.11.05
     */
    public function save_img($body)
    {
        $body = stripslashes($body);
        $img_array = array();
        preg_match_all("/<img.*?src=[\'|\"](.*?(?:[\.gif|\.jpg|\.png]))[\'|\"].*?>/", $body, $img_array);
        $img_array = array_unique($img_array[1]);
        
        foreach ($img_array as $key => $value)
        {
            $value = trim($value);
            if (strpos($value, "http://") !== 0 && strpos($value, "https://") !== 0)
            {
                $value = "http://www.51caixiang.com" . $value;
            }
            //对图片地址处理
            $value = str_replace(array('http:///', 'https:///', '../'), array('http://', 'https:///', ''), $value);
            $options = array(
                CURLOPT_REFERER => $this->reffer
            );
            $img_content = curl_http_get($value, array(), $options);
            if (!empty($img_content))
            {
                $path = '/uploads/notice/grab/' . date("Y") . "/";
                $basename = pathinfo($value, PATHINFO_BASENAME);
                if (!is_dir(BASEPATH . "/.." . $path))
                {
                    mkdir(BASEPATH . "/.." . $path, 0777, true);
                }
                file_put_contents(BASEPATH . "/.." . $path . $basename, $img_content);
                $body = str_replace($value, $path . $basename, $body);
            }
        }
        return $body;
    }
}
