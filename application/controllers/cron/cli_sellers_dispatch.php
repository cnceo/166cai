<?php
/**
 * Copyright (c) 2015,上海二三四五网络科技有限公司.
 * 摘    要:当前订单投注票商缓存处理
 * 作    者: shigx
 * 修改日期: 2016/2/22
 */

defined('BASEPATH') OR exit('No direct script access allowed');

class Cli_Sellers_Dispatch extends MY_Controller
{	
	public function __construct()
	{
		parent::__construct();
		$this->load->model('dispatch_model');
		$this->load->driver('cache', array('adapter' => 'redis'));
	}
	
	public function index()
    {
    	$this->ticket_seller_dispatch();
    	$this->rcg_channel_dispatch();
    }
    /**
	 * 计算当前销售票商并存入缓存
	 */
    private function ticket_seller_dispatch()
    {
    	$data = array();
    	$sellerRate = $this->dispatch_model->getSellerRate();
    	foreach ($sellerRate as $lid => $rate)
    	{
    		$setMaxRate = array_search(max($rate), $rate);
    		$data[$lid] = $setMaxRate;	//取用户设置最大的票商作默认值
    		
    		$issue = $this->getCurrentIssue($lid);
    		$result = $this->dispatch_model->getSellerTotal($lid, $issue);
    		if($result['total'] > 0)
    		{
    			$diffArr = array();
    			foreach ($rate as $seller => $confRate)
    			{
    				$sellerTotal = isset($result[$seller]) ? $result[$seller] : 0;	//票商分配票数
    				$nowRate = round ($sellerTotal / $result['total'] * 100 ,  2);	//分配比例
    				//用户设置出票比例为0或者当前比例大于分配比例的直接跳过
    				if($confRate == 0 || $nowRate > $confRate)
    				{
    					continue;
    				}
    				
    				$difference = $confRate - $nowRate;	//用户分配和目前比例差值
    				$diffArr[$seller] = $difference;
    			}
    			
    			$data[$lid] = array_search(max($diffArr), $diffArr);
    			$data[$lid] = empty($data[$lid]) ? $setMaxRate : $data[$lid]; //取值为空时取默认最大的配置票商
    		}
    	}
    	
    	$REDIS = $this->config->item('REDIS');
    	$this->cache->save($REDIS['TICKET_SELLER'], serialize($data), 0);
    }
    /**
     * 查询彩种当前期次
     * @param int $lid
     */
	private function getCurrentIssue($lid)
   	{
   		$currentIssue = date('Ymd');
   		$issueMap = array(
   			'21406' => 'SYXW_ISSUE_TZ',
   			'33' => 'PLS_ISSUE',
   			'35' => 'PLW_ISSUE',
   			'51' => 'SSQ_ISSUE',
   			'52' => 'FC3D_ISSUE',
   			'10022' => 'QXC_ISSUE',
   			'23528' => 'QLC_ISSUE',
   			'23529' => 'DLT_ISSUE',
   			'11' => 'SFC_ISSUE',
   			'19' => 'RJ_ISSUE',
   			'21407' => 'JXSYXW_ISSUE_TZ',
   			'53' => 'KS_ISSUE_TZ',
            '56' => 'JLKS_ISSUE_TZ',
   		    '57' => 'JXKS_ISSUE_TZ',
   			'21408' => 'HBSYXW_ISSUE_TZ',
        '54' => 'KLPK_ISSUE_TZ',
        '55' => 'CQSSC_ISSUE_TZ',
   		    '21421' => 'GDSYXW_ISSUE_TZ',
   		);
   		$cacheName = isset($issueMap[$lid]) ? $issueMap[$lid] : '';
   		if($cacheName)
   		{
   			$REDIS = $this->config->item('REDIS');
   			$cache = $this->cache->get($REDIS[$cacheName]);
   			$cache = json_decode($cache, true);
   			$currentIssue = $cache['cIssue']['seExpect'];
   		}
   		
   		return $currentIssue;
   }
   
   private function rcg_channel_dispatch()
   {
                $this->load->model('pay_model');
                $config = $this->pay_model->getFreshPayConfig();
   		$channels = $this->dispatch_model->RcgChannelDispatch();
   		$REDIS = $this->config->item('REDIS');
   		$oldchannels = $this->cache->hGetAll($REDIS['RCG_DISPATCH']);
   		$diffchannels= array_diff(array_keys($oldchannels), array_keys($channels));
   		if(!empty($diffchannels))
   		{
	   		foreach ($diffchannels as $key)
	   		{
	   			$this->cache->hDel($REDIS['RCG_DISPATCH'], $key);
	   		}
   		}
                if($config['fresh_payconfig'] == 0){
   		    $this->cache->hMSet($REDIS['RCG_DISPATCH'], $channels);
                }
                $this->cache->hMSet($REDIS['CS_RCG_DISPATCH'], $channels);
   }
   
}