<?php

/**
 * 合买订单根据modified扫描脚本
 * @date:2016-12-08
 */

defined('BASEPATH') OR exit('No direct script access allowed');

class Cli_United_Order_Scan_Modified extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('united_order_model');
        $this->load->model('united_wallet_model');
    }

    // 按modified设置投单，返还保底
    public function index($stime = 0)
    {
        $tspan = 60;
        $stime = $this->startTime($stime);
        $count = array();
        $contine = true;
        while($contine)
        {
            $this->controlRestart($this->con);
           	$sdate = date('Y-m-d H:i:s', $stime);
            $etime = strtotime("$tspan min", $stime);
        	//便于测试环境的调试
        	if(ENVIRONMENT==='development')
        	{
        		// 扫描订单
            	$re = $this->scanModified($stime, $etime);
        	}else{
        		$rarray = null;
        		$croname = "united/cli_united_order_scan_modified scanModified/$stime/$etime";
        		exec("{$this->php_path} {$this->cmd_path} $croname", $rarray, $status);
        		$re = $rarray[0];
        	}
            if(($re && $etime < time()) || (!$re && ($count["{$stime}-$tspan"]++ < 3)))
            {
                if($re)
                {
                    $stime = $this->startTime($etime);
                }
                $contine = true;
            }
            else 
            {
                sleep(5);
                // $contine = false;
            }
            //重试三次失败
            if(!$re && $count["{$stime}-$tspan"] >= 3)
            {
                log_message('LOG', "United Order Fail: {$sdate}->$tspan min");
            }
        } 
    }

    // 按modified设置投单，返还保底
    public function scanModified($stime, $etime)
    {
    	$sdate = date('Y-m-d H:i:s', $stime);
        $edate = date('Y-m-d H:i:s', $etime);
        // 合买订单状态
        $orderStatus = $this->united_order_model->getStatus();

        $orders = $this->united_order_model->getModifiedOrders($sdate, $edate);
        
        while(!empty($orders))
        {
            foreach ($orders as $order) 
            {
                // 认购保底总额大于95%
                $sMoney = intval($order['buyTotalMoney']) + intval($order['guaranteeAmount']);
                $tMoney = intval($order['money']);
                $percent = $this->united_order_model->getUnitedPercent($sMoney, $tMoney);
                if($order['status'] == $orderStatus['pay'] && $percent >= 95 && $order['bet_flag'] == 0)
                {
                    // 投单
                    $this->united_order_model->doBetOrder($order);
                }
                elseif($order['status'] <= $orderStatus['draw'] && $order['buyTotalMoney'] == $order['money'] && $order['guaranteeAmount'] > 0 && ($order['cstate'] & 2) == 0 && ($order['cstate'] & 4) == 0)
                {
                    // 认购总额100%，但可能未到endTime，保底退款
                    $this->united_wallet_model->handleGuaranteeRefund($order);
                }
                elseif($order['status'] >= $orderStatus['draw'] && !in_array($order['status'], array($orderStatus['concel'], $orderStatus['revoke_by_user'], $orderStatus['revoke_by_system'])) && $order['buyTotalMoney'] + $order['guaranteeAmount'] >= $order['money'] && $order['webguranteeAmount'] == 0 && ($order['cstate'] & 8) == 0 && ($order['cstate'] & 16) == 0)
                {
                    // 出票成功，网站没有保底，cstate & 8 = 0，检查返点
                    // 更新cp_orders activity_ids走返点逻辑
                    $this->united_wallet_model->handleRebate($order);
                }
            }
            $orders = $this->united_order_model->getModifiedOrders($sdate, $edate);
        }
        
        //合买满员订单入触发器表
        $this->united_order_model->scanModifiedGrowth($sdate, $edate);
        if(ENVIRONMENT==='development')
        {
        	return true;
        }else{
        	echo 1;
        }
        
    }

    // 扫描开始时间
    private function startTime($stime = null)
    {
        $REDIS = $this->config->item('REDIS');
        $this->load->driver('cache', array('adapter' => 'redis'));
        $ini_time = strtotime('-1 day', time());
        if(empty($stime))
        {
            $stime = $this->cache->redis->get($REDIS['UNITED_ORDERS_CHECK_START_TIME']);
        }
        if($stime < $ini_time)
        {
            $stime = $ini_time;
        }
        $this->cache->redis->save($REDIS['UNITED_ORDERS_CHECK_START_TIME'], $stime, 0);
        return $this->cache->redis->get($REDIS['UNITED_ORDERS_CHECK_START_TIME']);
    }
}