<?php

/**
 * Copyright (c) 2014,上海二三四五网络科技股份有限公司
 * 摘    要：抓取资讯内容
 * 作    者：shigx@2345.com
 * 修改日期：2014.11.07
 */
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Get_news extends CI_Controller
{   
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
        $this->load->library('tools');
        $this->load->model('Model_info');
    }

    public function index($ctype)
    {
    	$category = array(
    		'1' => array('1', '2', '3', '4', '5', '6', '7'),
    		'2' => array('8', '9', '10'));
    	$crawls = $this->Model_info->getInfoCrawl($category[$ctype]);
    	$sources = array(
    		'1' => 'get163News',
    		'2' => 'getSinaNews',
    		'3' => 'getBaiduNews',
    		'4' => 'getHupuNews',
                '6' => 'getOkoooNews'
    	);
    	foreach ($crawls as $parms)
    	{
    		$data = $this->$sources[$parms['source']]($parms);
    		$this->Model_info->saveData($data);
    	}
    }
    
    /**
     * 虎扑新闻抓取
     * @param unknown_type $parms
     * @return multitype:multitype:NULL string unknown Ambigous <string, mixed>
     */
    private function getHupuNews($parms)
    {
    	$listContent = file_get_contents($parms['url']);	//curl抓取不到页面 故用file_get_contents()
    	$rule  = '<div.*?news-list.*?>.*?<ul>(.*?)<\/ul>'; //获取所有li
    	preg_match("/$rule/is", $listContent, $match);
    	$listContent = $match[1];
    	unset($match);
    	$rule = '<li>.*?<div.*?list-hd.*?>.*?<a.*?href="(.*?)".*?>(.*?)<\/a>.*?<\/li>';
    	preg_match_all("/$rule/is", $listContent, $matches);
    	$data = array();
    	if(!empty($matches[1]))
    	{
    		$nowDate = strtotime(date('Y-m-d'));
    		foreach ($matches[1] as $key => $cUrl)
    		{
    			$news = array();
    			$content = $this->getContent($cUrl);
    			if(empty($content))
    			{
    				continue;
    			}
    			$news['title'] = $matches[2][$key];
    			$news['category_id'] = $parms['category_id'];
    			$news['source_id'] = $parms['source'];
    			$news['submitter'] = '抓取';
    			$rule  = '<span.*?id="pubtime_baidu".*?>(.*?)<\/span>'; //获取发布时间
    			preg_match("/$rule/is", $content, $match);
    			$news['created'] = trim($match[1]);
    			if(strtotime($news['created']) < $nowDate)
    			{
    				continue;
    			}
    			$news['platform'] = 15;
    			$rule = '<div.*?artical-content.*?>(.*?)<span\s+id="editor_baidu".*?>';
    			preg_match("/$rule/is", $content, $match);
    			if(empty($match[1]))
    			{
    				continue;  //内容为空跳出
    			}
    			$body = strip_tags($match[1], '<img><p><span>'); //保留指定HTML标签
    			$news['content'] = $this->save_img($body);
    			$news['content'] = str_hsc($news['content']);
                $news['likeNum'] = rand(0, 200);
                $news['num'] = $news['likeNum'] + rand(50, 100);
    			$data[] = $news;
    			sleep(1); //放慢抓取速度减少图片抓取失败
    		}
    	}
    	return $data;
    }
    
    /**
     * 获取新浪新闻
     * @param unknown_type $parms
     * @return Ambigous <multitype:multitype:NULL, multitype:multitype:NULL string unknown_type mixed Ambigous <string, mixed>  >|multitype:multitype:NULL string unknown_type mixed Ambigous <string, mixed>
     */
    private function getSinaNews($parms)
    {
    	if(strpos($parms['url'], "roll.sports.sina.com.cn") !== false)
    	{
    		return $this->getSinaRollNews($parms);
    	}
    	else 
    	{
    		return $this->getSinaSportsNews($parms);
    	}
    }
    
    /**
     * 新浪彩票频道新闻抓取
     * @param unknown_type $parms
     */
    private function getSinaSportsNews($parms)
    {
    	$parse = parse_url($parms['url']);
    	$host = $parse['scheme'] . '://' . $parse['host'];
    	$listContent = $this->getContent($parms['url']);
    	$rule  = '<ul.*?list1.*?>(.*?)<\/ul>'; //获取所有li
    	preg_match_all("/$rule/is", $listContent, $matches);
    	$data = array();
    	if(!empty($matches[1]))
    	{
    		$nowDate = date('d');
    		foreach ($matches[1] as $val)
    		{
    			$rule = '<li>.*?<a.*?href=(.*?) .*?>(.*?)<\/a>.*?<span.*?fDate.*?>\((.*?)日.*?<\/li>';
    			preg_match_all("/$rule/is", $val, $matches1);
    			foreach ($matches1[1] as $key => $cUrl)
    			{
    				$news = array();
    				if(trim($matches1[3][$key]) != $nowDate)
    				{
    					continue;
    				}
    				if (strpos($cUrl, "http://") !== 0 && strpos($cUrl, "https://") !== 0)
    				{
    					$cUrl = $host . $cUrl;
    				}
    				$content = $this->getContent($cUrl);
    				if(empty($content))
    				{
    					continue;
    				}
    				$news['title'] = $matches1[2][$key];
    				$news['category_id'] = $parms['category_id'];
    				$news['source_id'] = $parms['source'];
    				$news['submitter'] = '抓取';
    				$rule  = '<span class="date">(.*?)<\/span>'; //获取发布时间
    				preg_match("/$rule/is", $content, $match);
    				$news['created'] = str_replace(array('年', '月', '日'), array('-', '-', ''), trim($match[1]));
    				$rule = '<div.*?id="artibody".*?>(.*?)<div.*?ct_page_wrap.*?>';
    				preg_match("/$rule/is", $content, $match);
    				if(empty($match[1]))
    				{
    					continue;  //内容为空跳出
    				}
    				
    				$body = preg_replace('/<link.*?stylesheet.*?\/>/is', '', $match[1]);
    				$body = preg_replace('/<figcaption.*?figcaption>/is', '', $body);
    				$body = preg_replace('/<figure.*?>(.*?)<\/figure>/is', '$1', $body);
    				$body = preg_replace('/<!--.*?-->/is', '', $body);
    				$news['content'] = $this->save_img($body);
    				$news['content'] = str_hsc($news['content']);
    				$news['platform'] = 15;
                    $news['likeNum'] = rand(0, 200);
                    $news['num'] = $news['likeNum'] + rand(50, 100);
    				$data[] = $news;
    			}
    		}
    	}
    	return $data;
    }
    
    /**
     * 新浪彩通新闻抓取
     * @param unknown_type $parms
     * @return multitype:multitype:NULL string unknown mixed Ambigous <string, mixed>
     */
    private function getSinaRollNews($parms)
    {
    	$listContent = $this->getContent($parms['url']);
    	$rule  = '<div.*?id="d_list".*?>.*?<ul.*?>(.*?)<\/ul>'; //获取所有li
    	preg_match("/$rule/is", $listContent, $match);
    	$listContent = $match[1];
    	unset($match);
    	$rule = '<li>.*?<a.*?href="(.*?)".*?>(.*?)<\/a>.*?<span.*?c_time.*?>(.*?) .*?<\/span>.*?<\/li>';
    	preg_match_all("/$rule/is", $listContent, $matches);
    	$data = array();
    	if(!empty($matches[1]))
    	{
    		$nowDate = date('Y年m月d日');
    		foreach ($matches[1] as $key => $cUrl)
    		{
    			$news = array();
    			echo trim($matches[3][$key]) . "\n";
    			if(trim($matches[3][$key]) != $nowDate)
    			{
    				continue;
    			}
    			$content = $this->getContent($cUrl);
    			if(empty($content))
    			{
    				continue;
    			}
    			$news['title'] = $matches[2][$key];
    			$news['category_id'] = $parms['category_id'];
    			$news['source_id'] = $parms['source'];
    			$news['submitter'] = '抓取';
    			$rule  = '<div.*?news-time.*?>.*?<span>(.*?)<\/span>'; //获取发布时间
    			preg_match("/$rule/is", $content, $match);
    			$news['created'] = str_replace(array('年', '月', '日'), array('-', '-', ''), trim($match[1]));
    			$rule = '<div.*?article-txt.*?>(.*?)<p.*?article-kw.*?>';
    			preg_match("/$rule/is", $content, $match);
    			if(empty($match[1]))
    			{
    				continue;  //内容为空跳出
    			}
    			$body = preg_replace('/<div[^<]+img_wrapper[^<]+><img[^<]+default.*?<\/div>/is', '', $match[1]);
    			$news['content'] = $this->save_img($body);
    			$news['content'] = str_hsc($news['content']);
    			$news['platform'] = 15;
                $news['likeNum'] = rand(0, 200);
                $news['num'] = $news['likeNum'] + rand(50, 100);
    			$data[] = $news;
    		}
    	}
    	return $data;
    }
    
    /**
     * 网页新闻抓取
     * @param unknown_type $parms
     */
    private function get163News($parms)
    {
    	$listContent = $this->getContent($parms['url']);
    	$rule  = '<ul class="clearfix">(.*?)<\/ul>'; //获取所有li
    	preg_match_all("/$rule/is", $listContent, $matches);
    	$data = array();
    	if(!empty($matches[1]))
    	{
    		$nowDate = date('m-d');
    		foreach ($matches[1] as $val)
    		{
    			$rule = '<li><span><a.*?href=[\'|\"](.*?)[\'|\"].*?>(.*?)<\/a><\/span><em>(.*?) .*?<\/li>';
    			preg_match_all("/$rule/is", $val, $matches1);
    			foreach ($matches1[1] as $key => $cUrl)
    			{
    				$news = array();
    				if(trim($matches1[3][$key]) != $nowDate)
    				{
    					continue;
    				}
    				$content = $this->getContent($cUrl);
    				if(empty($content))
    				{
    					continue;
    				}
    				$news['title'] = $matches1[2][$key];
    				$news['category_id'] = $parms['category_id'];
    				$news['source_id'] = $parms['source'];
    				$news['submitter'] = '抓取';
    				$rule  = '.*?(\d{4}-\d{2}-\d{2}.*?\d{2}:\d{2}:\d{2}).*?来源:.*'; //获取发布时间
    				preg_match("/$rule/is", $content, $match);
    				$news['created'] = trim($match[1]);
    				$rule = '<div.*?endText.*?>.*?<\/div>(.*?)<div.*?seolinks-bottom.*?>';
    				preg_match("/$rule/is", $content, $match);
    				if(empty($match[1]))
    				{
    					continue;  //内容为空跳出
    				}
    				$body = str_replace("<!-- 样式表位于 '彩票文章页正文前seo链接列表' 模板中，若单独引用此模板需要复制样式表 -->", "", $match[1]);
    				$body = preg_replace('/<a.*?>(.*?)<\/a>/', '$1', $body);
    				$news['content'] = $this->save_img($body);
    				$news['content'] = str_hsc($news['content']);
    				$news['platform'] = 15;
                    $news['likeNum'] = rand(0, 200);
                    $news['num'] = $news['likeNum'] + rand(50, 100);
    				$data[] = $news;
    			}
    		}
    	}
    	return $data;
    }
    
    /**
     * 百度乐彩新闻抓取
     * @param unknown_type $parms
     * @return multitype:multitype:NULL string unknown Ambigous <string, mixed>
     */
    private function getBaiduNews($parms = array())
    {
    	$parse = parse_url($parms['url']);
    	$host = $parse['scheme'] . '://' . $parse['host'];
    	$listContent = $this->getContent($parms['url']);
    	$rule  = '<ul.*?class.*?list_ul.*?>(.*?)<\/ul>'; //获取所有li
    	preg_match("/$rule/is", $listContent, $match);
    	$listContent = $match[1];
    	unset($match);
    	$rule = '<li>.*?<a.*?href="(.*?)".*?>(.*?)<\/a><span>(.*?) .*?<\/span>.*?<\/li>';
    	preg_match_all("/$rule/is", $listContent, $matches);
    	$data = array();
    	if(!empty($matches[1]))
    	{
    		$nowDate = date('Y-m-d');
    		foreach ($matches[1] as $key => $cUrl)
    		{
    			$news = array();
    			if(trim($matches[3][$key]) != $nowDate)
    			{
    				continue;
    			}
    			if (strpos($cUrl, "http://") !== 0 && strpos($cUrl, "https://") !== 0)
    			{
    				$cUrl = $host . $cUrl;
    			}
    			$content = $this->getContent($cUrl);
    			if(empty($content))
    			{
    				continue;
    			}
    			$news['title'] = $matches[2][$key];
    			$news['category_id'] = $parms['category_id'];
    			$news['source_id'] = $parms['source'];
    			$news['submitter'] = '抓取';
    			$rule  = '<div.*?class.*?article_info.*?>(.*?)<span>.*?<\/div>'; //获取发布时间
    			preg_match("/$rule/is", $content, $match);
    			$news['created'] = $match[1];
    			$rule = '<div.*?class="content">(.*?)<div.*?class="share-box">';
    			preg_match("/$rule/is", $content, $match);
    			if(empty($match[1]))
    			{
    				continue;  //内容为空跳出
    			}
    			$body = preg_replace('/<a.*?activities_lecai.*?>.*?<\/a><span.*?>.*?<\/a>/', '', $match[1]);
    			$body = preg_replace('/<a.*?activities_lecai.*?>.*?<\/a><span.*?>.*?<\/span>/', '', $body);
    			$news['content'] = $this->save_img($body, 'http:');
    			$news['content'] = str_hsc($news['content']);
    			$news['platform'] = 15;
                $news['likeNum'] = rand(0, 200);
                $news['num'] = $news['likeNum'] + rand(50, 100);
    			$data[] = $news;
    		}
    	}
    	return $data;
    }
    /**
     *
     * @param string $url	url
     * @param array $params	规定请求参数设置
     */
    private function getContent($url, $params = array())
    {
    	$content = $this->tools->request($url, $params);
    	if($this->tools->recode == '200')
    	{
    		$encode = mb_detect_encoding($content, array('ASCII', 'UTF-8', 'GB2312', 'GBK', 'BIG5'));
    		if($encode != 'UTF-8')
    		{
    			$content = iconv($encode, 'UTF-8', $content);
    		}
    		return $content;
    	}
    	
    	return '';
    }
    
    /**
     * 参    数：无
     * 作    者：wangl
     * 功    能：保存图片
     * 修改日期：2014.11.05
     */
    public function save_img($body, $host = '')
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
                $value = $host . $value;
            }
            //对图片地址处理
            $options = array();
            $img_content = curl_http_get($value, array(), $options);
            if (!empty($img_content))
            {
                $path = '/uploads/info/' . date("Ym") . "/";
                $basename = time() . $key . '.' . pathinfo($value, PATHINFO_EXTENSION);
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
    
    private function getOkoooNews($parms)
    {
        $content = $this->getContent($parms['url']);
        $parse = parse_url($parms['url']);
    	$host = $parse['scheme'] . '://' . $parse['host'];
        $rule = '<div.*?news_left_item.*?>(.*?)<\/div>';
        preg_match_all("/$rule/is", $content, $matches);
        $contents = $matches[1];
        unset($matches);
        $data = array();
    	if (!empty($contents)) {
            $nowDate = strtotime(date('Y-m-d'));
            foreach ($contents as $val) {
                $rule='<h2.*?class="news_title">.*?<a target="_blank".*?href=[\'|\"](.*?)[\'|\"].*?>(.*?)<\/a>.*?<span.*?class="cyq_newope_time">(.*?)<\/span>';
                preg_match_all("/$rule/is", $val, $matches);
                $news = array();
                if (strtotime(trim($matches[3][0])) < $nowDate) {
                    continue;
                }
                $news['title'] = $matches[2][0];
                $news['category_id'] = $parms['category_id'];
                $news['source_id'] = $parms['source'];
                $news['submitter'] = '抓取';
                $news['created'] = trim($matches[3][0]);
                $news['platform'] = 15;
                $cUrl = $matches[1][0];
                if (strpos($cUrl, "http://") !== 0 && strpos($cUrl, "https://") !== 0) {
                    $cUrl = $host . $cUrl;
                }
                $article = $this->getContent($cUrl);
                $rule = '<div.*?class="newsDetail_txt">(.*?)<\/div>';
                preg_match("/$rule/is", $article, $match);
                if (empty($match[1])) {
                    continue;  //内容为空跳出
                }
                $rule = "本文转载.*?<\/p>";
                $article = preg_replace("/$rule/is", '', $match[1]);
                $news['content'] = $this->save_img($article);
                $news['content'] = str_hsc($news['content']);
                $news['likeNum'] = rand(0, 200);
                $news['num'] = $news['likeNum'] + rand(50, 100);
                $data[] = $news;
            }
        }
        return $data;
    }
}
