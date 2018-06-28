<?php

/**
 * Copyright (c) 2014,上海二三四五网络科技股份有限公司
 * 摘    要：后台首页
 * 作    者：wangl@2345.com
 * 修改日期：2014.11.06
 */
defined('BASEPATH') OR die('No direct script access allowed');

class Main extends MY_Controller
{
    /**
     * 彩票后台入口控制器
     */
    public function __construct()
    {
        parent::__construct();
    }
    
    /**
     * 参    数：无
     * 作    者：wangl
     * 功    能：后台首页
     * 修改日期：2014.11.05
     */
    public function index()
    {
        $this->load->view('cmain');
    }
    
    /**
     * 参    数：无
     * 作    者：wangl
     * 功    能：OA头部
     * 修改日期：2014.11.07
     */
    public function top()
    {
        header("Content-type: text/html; charset = utf-8");
        $uid = intval($this->input->cookie('d_uid'));
        $this->isTest && $uid = 3;
        $mk = $this->input->cookie('d_mk', TRUE);
        $this->load->view('head', array('uname' => $this->uname));
//        echo file_get_contents('http://oa.2345.cn/oaAllTop.php');
//        if ($uid > 0)
//        {
//            echo file_get_contents('http://oa.2345.cn/oaAllTop.php?from=qsnew&userid=' . $uid . '&mk=' . $mk);
//        }
//        else
//        {
//            echo "<script type='text/javascript'>parent.location.href='https://oa.2345.cn/login.php?r=http://qs.ruichuang.net';</script>";
//            exit;
//        }
    }
    
    /**
     * 参    数：无
     * 作    者：wangl
     * 功    能：后台起始页面
     * 修改日期：2014.11.05
     */
    public function right()
    {
        /*$rediskeys = $this->config->item('rediskeys');
        $this->load->driver('cache', array(
            'adapter' => 'redis'
        ));
        $result = $this->cache->redis->hGetAll($rediskeys['count_yestoday']);
        $thirty = $this->cache->redis->hGetAll($rediskeys['count_thirty']);
        if (empty($result) || empty($thirty))
        {
            $this->load->model("Model_count");
            $result = $this->Model_count->get_ndays(1);
            $thirty = $this->Model_count->get_ndays(30);
            if (!empty($result) && !empty($thirty))
            {
                $this->cache->redis->hMSet($rediskeys['count_yestoday'], $result);
                $this->cache->redis->hMSet($rediskeys['count_thirty'], $thirty);
            }
        }
        
        $pageInfo = array(
            "yestoday" => $result,
            "thirty" => $thirty
        );*/
    	$pageInfo = array();
        $this->load->view('start', $pageInfo);
        unset($pageInfo);
    }
    
    /**
     * 参    数：无
     * 作    者：wangl
     * 功    能：后台左侧目录
     * 修改日期：2014.11.05
     */
    public function navigate()
    {
        $this->config->load('user_capacity');
        $user_capacity_cfg = $this->config->item('user_capacity_cfg');
        $pageInfo['left'] = array();
        foreach ($user_capacity_cfg['user_capacity'] as $k1 => $level1)
        {
            if(in_array("$k1", $this->capacity, true))
            {
                $pageInfo['left'][$k1]['name'] = $level1['name'];
                $pageInfo['left'][$k1]['url'] = $level1['url'];
                $pageInfo['left'][$k1]['child'] = array();
                foreach ($level1['child'] as $k2 => $level2)
                {
                    if(in_array($k2, $this->capacity))
                    {
                        $pageInfo['left'][$k1]['child'][$level2['name']] = $level2['url'];
                    }
                }
            }
        }
        $pageInfo['allLinkNum'] = count($pageInfo['left']);
        $this->load->view('templates/side', $pageInfo);
    }
    
    public function echart()
    {
    	$this->load->view('echart/index');
    }
    
    public function callback()
    {
        log_message('LOG', $_SESSION['currenturl'], 'oacheck');
    	$state = $_GET['state'];
    	$code = $_GET['code'];
    	session_start();
    	if ($state == $_SESSION['oa_auth_state'] && $state != '') {
    		//实例化sdk客户端
    		$this->load->library('oacheck');
    		$token = $this->oacheck->getToken($code);
    		$userinfo = $this->oacheck->api("user/info", $token['access_token']);
    		if ($userinfo && in_array($this->oacheck->_systemId, $userinfo['auths'])) {
    			$_SESSION['oa_auth_token'] = $token;
    			$_SESSION['oa_auth_userinfo'] = $userinfo;
    			$_SESSION['oa_auth_ip'] = $_SERVER['REMOTE_ADDR'];
    			$_SESSION['oa_auth_at'] = time();
    			header("Location: ".(empty($_SESSION['currenturl']) ? '/backend/' : $this->config->item('base_url').$_SESSION['currenturl']));
    		} else {
    			echo "未授权!";
    		}
    	} else {
    		echo "非法操作!";
    	}
    }
}
