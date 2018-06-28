<?php

/**
 * 追号方案自动投注脚本
 * @date:2015-12-08
 */

defined('BASEPATH') OR exit('No direct script access allowed');

class Cli_Chase_Bet extends MY_Controller
{
    //快频彩彩种配置
    private $quickLotteryMap = array(
        '21406' => array('cCache' => 'SYXW_ISSUE_TZ', 'bTime' => '120', 'lname' => 'syxw'),
        '21407' => array('cCache' => 'JXSYXW_ISSUE_TZ', 'bTime' => '120', 'lname' => 'jxsyxw'),
        '53' => array('cCache' => 'KS_ISSUE_TZ', 'bTime' => '120', 'lname' => 'ks'),
        '21408' => array('cCache' => 'HBSYXW_ISSUE_TZ', 'bTime' => '120', 'lname' => 'hbsyxw'),
        '54' => array('cCache' => 'KLPK_ISSUE_TZ', 'bTime' => '120', 'lname' => 'klpk'),
        '55' => array('cCache' => 'CQSSC_ISSUE_TZ', 'bTime' => '60', 'lname' => 'cqssc'),
        '56' => array('cCache' => 'JLKS_ISSUE_TZ', 'bTime' => '120', 'lname' => 'jlks'),
        '57' => array('cCache' => 'JXKS_ISSUE_TZ', 'bTime' => '120', 'lname' => 'jxks'),
        '21421' => array('cCache' => 'GDSYXW_ISSUE_TZ', 'bTime' => '120', 'lname' => 'gdsyxw'),
    );
    //慢频彩彩种配置
    private $slowLotteryMap = array(
        '33' => array('cCache' => 'PLS_ISSUE', 'bTime' => '7200', 'lname' => 'pls'),
        '35' => array('cCache' => 'PLW_ISSUE', 'bTime' => '7200', 'lname' => 'plw'),
        '51' => array('cCache' => 'SSQ_ISSUE', 'bTime' => '7200', 'lname' => 'ssq'),
        '52' => array('cCache' => 'FC3D_ISSUE', 'bTime' => '7200', 'lname' => 'fc3d'),
        '10022' => array('cCache' => 'QXC_ISSUE', 'bTime' => '7200', 'lname' => 'qxc'),
        '23528' => array('cCache' => 'QLC_ISSUE', 'bTime' => '7200', 'lname' => 'qlc'),
        '23529' => array('cCache' => 'DLT_ISSUE', 'bTime' => '7200', 'lname' => 'dlt'),
    );
    public function __construct()
    {
        parent::__construct();
        $this->load->model('chase_bet_model');
        $this->load->model('chase_order_model');
        $this->load->model('chase_model');
        $this->load->driver('cache', array('adapter' => 'redis'));
    }

    /**
     * 快频彩
     */
    public function index()
    {
        $this->chaseBet($this->quickLotteryMap, '_quick');
        $this->doBet('quick');
    }
    
    /**
     * 慢频彩
     */
    public function slowBet()
    {
        $this->chaseBet($this->slowLotteryMap);
        $this->doBet('slow');
    }

    public function doBet($type)
    {
        // 彩种配置信息
        $lotteryConfig = $this->chase_bet_model->getLotteryConfig($type);

        // 获取订单状态
        $orderStatus = $this->chase_order_model->getStatus();

        foreach ($lotteryConfig as $lid => $config) 
        {
            // 获取指定彩种的当前期下期 cIssue nIssue 信息
            $issueCache = $this->chase_bet_model->getCurrentLottery($config['cache']);

            // 获取彩种当前和上期的期次信息缓存
            $issueInfo = $this->getIssueConfig($config, $issueCache);

            // 投注
            $this->autoBet($lid, $config, $orderStatus);
        }

    }

    // 期次格式处理
    public function getIssueFormat($lotteryId, $issue)
    {
        if(!empty($issue))
        {
            if(in_array($lotteryId, array('23529', '33', '35', '10022')))
            {
                $issue = strlen($issue) >= 7 ? substr($issue, -5) : $issue;
            }
        }    
        return $issue;
    }

    // 获取彩种当前和上期的期次信息缓存
    public function getIssueConfig($issueConfig, $issueCache)
    {
        $issueInfo = array(
            'cIssue' => array(
                'issue' => '',
                'sale_time' => '',
                'show_end_time' => '',
                'end_time' => ''
            ),
            'lIssue' => array(
                'issue' => '',
                'sale_time' => '',
                'show_end_time' => '',
                'end_time' => ''
            ),
        );
		$betCache = $this->chase_bet_model->getBetCache($issueConfig['lname']);
		if(empty($betCache))
		{
			$issueInfo = array(
	            'cIssue' => array(
	                'issue' => $issueCache['cIssue']['issue'],
	                'sale_time' => $issueCache['cIssue']['sale_time'],
                    'show_end_time' => $issueCache['cIssue']['show_end_time'],
                    'end_time' => $issueCache['cIssue']['end_time']
	            ),
	            'lIssue' => array(
	                'issue' => '',
	                'sale_time' => '',
                    'show_end_time' => '',
                    'end_time' => ''
	            ),
        	);
		}
		else 
		{
			$issueInfo = array(
	            'cIssue' => array(
	                'issue' => $betCache['cIssue']['issue'],
	                'sale_time' => $betCache['cIssue']['sale_time'],
                    'show_end_time' => $betCache['cIssue']['show_end_time'],
                    'end_time' => $betCache['cIssue']['end_time']
	            ),
	            'lIssue' => array(
	                'issue' => $betCache['lIssue']['issue'],
	                'sale_time' => $betCache['lIssue']['sale_time'],
                    'show_end_time' => $betCache['lIssue']['show_end_time'],
                    'end_time' => $betCache['lIssue']['end_time']
	            ),
        	);
        	
			if($betCache['cIssue']['issue'] != $issueCache['cIssue']['issue'])
            {
            	$issueInfo = array(
		            'cIssue' => array(
		                'issue' => $issueCache['cIssue']['issue'],
		                'sale_time' => $issueCache['cIssue']['sale_time'],
                        'show_end_time' => $issueCache['cIssue']['show_end_time'],
                        'end_time' => $issueCache['cIssue']['end_time']
		            ),
		            'lIssue' => array(
		                'issue' => $betCache['cIssue']['issue'],
		                'sale_time' => $betCache['cIssue']['sale_time'],
                        'show_end_time' => $betCache['cIssue']['show_end_time'],
                        'end_time' => $betCache['cIssue']['end_time']
		            ),
        		);
            }
		}
		$res = $this->chase_bet_model->refreshBetCache($issueConfig['lname'], $issueInfo);
		return $issueInfo;
    }

    // 投注
    public function autoBet($lid, $config, $orderStatus)
    {
        // 捞取指定彩种指定期次的订单
        $orderInfo = $this->chase_bet_model->getBetOrderInfo($lid, $orderStatus);
        
		$count = 0;
        while (!empty($orderInfo) && ++$count < 10) 
        {
            foreach ($orderInfo as $order)
            {
                // 投注订单
                $betStatus = $this->chase_bet_model->doBet($order);
                
                if(!$betStatus['code'])
                {
                    // 更新订单状态投注失败
                    $orderData = array(
                        'status' => '600',
                    );
                    $this->chase_bet_model->failChaseOrder($betStatus['data'], $orderData, 3);
                }
            }
            $orderInfo = $this->chase_bet_model->getBetOrderInfo($lid, $orderStatus);
        }
    }

    /**
     * 操作追号单位可投状态
     */
    public function chaseBet($issueMap, $tableSuffix = '')
    {
        $REDIS = $this->config->item('REDIS');
        foreach ($issueMap as $lid => $value)
        {
            $cache = $this->cache->get($REDIS[$value['cCache']]);
            $cache = json_decode($cache, true);
            if(empty($cache['cIssue']))
            {
                continue;
            }
            $ukey = $REDIS['CHASE_BET_ISSUE'] . $value['lname'];
            $betCache = unserialize($this->cache->redis->get($ukey));
            $lastIssue = isset($betCache['lIssue']['issue']) ? $betCache['lIssue']['issue'] : '';
            $nowTime = time();
            if(($cache['cIssue']['sale_time']/1000) > $nowTime)
            {
                //处理上一期遗漏单
                if($lastIssue && ($betCache['lIssue']['end_time'] > date('Y-m-d H:i:s', strtotime('-5 minute', $nowTime)) && $betCache['lIssue']['sale_time'] < date('Y-m-d H:i:s', $nowTime)))
                {
                    $time = array('endTime'=>$cache['nlIssue']['seEndtime']/1000,
                        'awardTime'=>$cache['nlIssue']['awardTime']/1000);
                    $this->chase_model->chaseLastBet($cache['cIssue']['seLotid'], $lastIssue, $time);
                }
                continue;
            }
            //处理当前期
            $this->chase_model->chaseNoSetAwardBet($cache['cIssue'], $value['bTime'], $lastIssue, $tableSuffix);
        }
    }

}