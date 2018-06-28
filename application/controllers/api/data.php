<?php

/*
 * @date:2015-08-10
 */
require_once APPPATH . '/core/CommonController.php';
class Data extends CommonController
{
	private $lidMap = array(
			'51' => array('cache' => 'SSQ_ISSUE'),
			'52' => array('cache' => 'FC3D_ISSUE'),
			'33' => array('cache' => 'PLS_ISSUE'),
			'35' => array('cache' => 'PLW_ISSUE'),
			'10022' => array('cache' => 'QXC_ISSUE'),
			'23528' => array('cache' => 'QLC_ISSUE'),
			'23529' => array('cache' => 'DLT_ISSUE'),
			'11' => array('cache' => 'SFC_ISSUE'),
			'19' => array('cache' => 'RJ_ISSUE'),
			'21406' => array('cache' => 'SYXW_ISSUE_TZ'),
	);
    public function __construct()
    {
        parent::__construct();
        $this->load->driver('cache', array('adapter' => 'redis'));
         $this->permitIp = array('127.0.0.1', '42.62.31.39', '172.16.0.39',
        '172.16.0.34', '172.16.0.35', '172.16.0.36', '172.16.0.37',
        '42.62.31.34', '42.62.31.35', '42.62.31.36', '42.62.31.37', '120.132.33.194', '120.132.33.195', '120.132.33.196', '120.132.33.197', '120.132.33.198', '123.59.105.39', '120.132.33.200', '120.132.33.201', '120.132.33.202', '120.132.33.203', '120.132.33.204', '120.132.33.205', '120.132.33.206');
        if(!in_array($this->get_client_ip(), $this->config->item('own_ip')))
        {
        	$response = array(
    				'code' => 1,
    				'msg'  => '查询失败',
    				'data' => array(),
    		);
        	echo json_encode($response);
        	die();
        }
    }

    /**
     * 数字彩当前期次查询
     */
    public function getNumCurrent()
    {
    	$response = array(
    		'code' => 0,
    		'msg'  => '查询成功',
    		'data' => array(),
    	);
    	$lid = $this->input->get('lid', true);
    	if(isset($this->lidMap[$lid]))
    	{
    		$keyName = $this->lidMap[$lid]['cache'];
    		$REDIS = $this->config->item('REDIS');
    		$cache = $this->cache->get($REDIS[$keyName]);
    		$cache = json_decode($cache, true);
    		if($cache['cIssue']['seDsendtime']/1000 <= time() && $cache['nIssue'] 
    			&& $lid != '21406')
    		{
    			$response['data'] = $cache['nIssue'];
    		}
    		else
    		{
    			$response['data'] = $cache['cIssue'];
    		}
    		$response['data']['nowTime'] = time() * 1000;
    	}
    	else
    	{
    		$response = array(
    				'code' => 1,
    				'msg'  => '查询失败',
    				'data' => array(),
    		);
    	}
    	echo json_encode($response);
    }

    /**
     * 数字彩上一期开奖查询
     */
    public function getLastAward()
    {
    	$response = array(
    			'code' => 0,
    			'msg'  => '查询成功',
    			'data' => array(),
    	);
    	$lid = $this->input->get('lid', true);
    	if(isset($this->lidMap[$lid]))
    	{
    		$keyName = $this->lidMap[$lid]['cache'];
    		$REDIS = $this->config->item('REDIS');
    		$cache = $this->cache->get($REDIS[$keyName]);
    		$cache = json_decode($cache, true);
    		$cache['lIssue']['nowTime'] = time() * 1000;
    		$response['data']['items'][0] = $cache['lIssue']; //构造符合js调用形式返回值
    	}
    	else
    	{
    		$response = array(
    				'code' => 1,
    				'msg'  => '查询失败',
    				'data' => array(),
    		);
    	}

    	echo json_encode($response);
    }
    
	public function refreshCache($ctype)
    {
    	$UIP = $this->get_client_ip();
    	if(!in_array($UIP, $this->permitIp))
    	{
    		die('IP LIMITED');
    	}
    	$this->load->library('libcomm');
    	$this->libcomm->refreshCache($ctype);
    }
}

