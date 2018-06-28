<?php

/**
 * 合买订单根据endTime扫描脚本
 * @date:2016-12-08
 */

defined('BASEPATH') OR exit('No direct script access allowed');

class Cli_United_Order_Scan_Endtime extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('united_order_model');
        $this->load->model('united_wallet_model');
    }

    // 按endTime处理过期订单
    public function index()
    {
        $orders = $this->united_order_model->getExpiredOrders();
        // 合买订单状态
        $orderStatus = $this->united_order_model->getStatus();
        $this->controlRestart($this->con);
        while(!empty($orders))
        {
            $this->controlRestart($this->con);
            foreach ($orders as $order) 
            {
                // 当前认购比例
                $sMoney = intval($order['buyTotalMoney']) + intval($order['guaranteeAmount']);
                $tMoney = intval($order['money']);
                $percent = $this->united_order_model->getUnitedPercent($sMoney, $tMoney);

                if($order['status'] == $orderStatus['create'])
                {
                    // 更新过期未付款订单
                    $this->united_order_model->updateExpiredStatus($order, true);
                }
                elseif($order['status'] == $orderStatus['pay'] && $percent < 95 && ($order['cstate'] & 1) == 0)
                {
                    // 处理过期已付款订单
                    $this->united_order_model->handleExpiredOrder($order);
                }
                elseif($order['guaranteeAmount'] > 0 && $order['buyTotalMoney'] + $order['guaranteeAmount'] >= $order['money'] && ($order['cstate'] & 2) == 0 && ($order['cstate'] & 4) == 0 && ($order['cstate'] & 1024) != 0)
                {
                    // 未处理保底转认购，用户设置了保底，所有认购保底大于总额，cstate & 2 = 0，需部分保底退款，部分保底转认购
                    $this->united_wallet_model->handleGuaranteeRefund($order);
                }
                elseif($order['buyTotalMoney'] + $order['guaranteeAmount'] < $order['money'] && ($order['cstate'] & 2) == 0 && ($order['cstate'] & 8) == 0 && ($order['cstate'] & 1024) != 0)
                {
                    // 所有认购+保底小于等于总额，cstate & 8 = 0，认购人保底转认购，可能存在网站保底认购介入
                    $this->united_wallet_model->handleGuaranteeTrans($order);
                }
            }
            $orders = $this->united_order_model->getExpiredOrders();
        }
    }
}