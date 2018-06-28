<?php

/**
 * Copyright (c) 2014,上海二三四五网络科技股份有限公司
 * 摘    要：订单管理
 * 作    者：wangl@2345.com
 * 修改日期：2014.11.11
 */
defined('BASEPATH') OR die('No direct script access allowed');

class Order extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->library('tools');
        $this->load->model('Model_order');
        $this->config->load('msg_text');
        $this->config->load('caipiao');
        $this->msg_text_cfg = $this->config->item('msg_text_cfg');
        foreach ($this->config->item('caipiao_all_cfg') as $key => $value)
        {
            $this->$key = $value;
        }
    }
    
    /**
     * 参    数：无
     * 作    者：wangl
     * 功    能：订单列表（这里有2部分 一个是订单管理 一个是用户详情里面的订单）
     * 修改日期：2014.11.05
     */
    public function index()
    {
        $this->check_capacity("2_1");
        $page = intval($this->input->get("p"));
        $page = $page <= 1 ? 1 : $page;
        $fromType = $this->input->get("fromType", true); //来源
        $searchData = array(
            "name" => $this->input->get("name", true),
            "lid" => $this->input->get("lid", true),
            "playType" => $this->input->get("playType", true),
            "issue" => $this->input->get("issue", true),
            "orderType" => $this->input->get("orderType", true),
            "start_time" => $this->input->get("start_time", true),
            "end_time" => $this->input->get("end_time", true),
            "start_money" => $this->input->get("start_money", true),
            "end_money" => $this->input->get("end_money", true),
            "status" => $this->input->get("status", true),
            "start_sendprize_time" => $this->input->get("dstatus", true),
            "end_sendprize_time" => $this->input->get("dstatus", true),
            "my_status" => $this->input->get("my_status", true),
            "uid" => $this->input->get("uid", true) //区分是否来自用户详情模块
        );
        $this->filterTime($searchData['start_time'], $searchData['end_time']);
        
        $result = $this->Model_order->list_orders($searchData, $page, self::NUM_PER_PAGE);
        $pageConfig = array(
            "page" => $page,
            "npp" => self::NUM_PER_PAGE,
            "allCount" => $result[1]
        );
        $pages = get_pagination($pageConfig);
        $pageInfo = array(
            "orders" => $result[0],
            "fromType" => $fromType,
            "pages" => $pages,
            "search" => $searchData,
            "tj" => $result[2]
        );
        echo $this->load->view("order", $pageInfo, true);
    }
    
    /**
     * 参    数：无
     * 作    者：wangl
     * 功    能：订单详情
     * 修改日期：2014.11.05
     */
    public function order_detail()
    {
        $this->check_capacity("2_2");
        $id = intval($this->input->get("id"));
        $order = $this->Model_order->findOrderByOrderId($id);

        if ( ! empty($order))
        {
            $this->load->model('Model_winning');
            $winning = $this->Model_winning->get_winning(array(
                "lid" => $order['lid'],
                "playType" => $order['playType'],
                "issue" => $order['issue']
            ));

            $this->load->model('model_order_inconsistent', 'inconsistent');
            $order['isConsistent'] = $this->inconsistent->isConsistent($order['orderId']);

            $lotteryMap = $this->config->item('cfg_lidmap');
            $tableTypeTransform = array(
                'pls'  => 'pl3',
                'plw'  => 'pl5',
                'fcsd' => 'fc3d',
            );
            $type = $lotteryMap[$order['lid']];
            if (array_key_exists($type, $tableTypeTransform))
            {
                $type = $tableTypeTransform[$type];
            }
            $this->load->library('issue');
            $pIssue = $this->issue->getPIssueBySIssue($type, $order['issue']);

            $this->load->model('model_issue_cfg', 'issueModel');
            $order['awardNum'] = $this->issueModel->getAwardNum($type, $pIssue);

            $this->load->model('model_ordersplit', 'orderSplit');
            $order['consistencyInfo'] = $this->orderSplit->consistencyInfo($order['orderId']);
            if ($order['status'] >= 500 && $order['status'] != 600)
            {
                $subOrders = $this->orderSplit->getSplitDetailByOrder($order['orderId'], $order['lid']);
                $this->load->library('split');
                foreach ($subOrders as & $subOrder) {
                    $subOrder['stakeNum'] = $this->split->computeStakeNum($subOrder);
                }
            }
            else
            {
                $subOrders = array();
            }
        }
        else
        {
            $winning = array();
            $subOrders = array();
        }

        $this->load->view("order_detail", compact('order', 'winning', 'subOrders'));
    }
    
    /**
     * 参    数：无
     * 作    者：wangl
     * 功    能：订单大奖审核列表
     * 修改日期：2014.11.05
     */
    public function check_list()
    {
        $this->check_capacity('2_2_1');
        $page = intval($this->input->get("p"));
        $page = $page <= 1 ? 1 : $page;
        $searchData = array(
            "name" => $this->input->get("name", true),
            "orderId" => $this->input->get("orderId", true),
            "lid" => $this->input->get("lid", true),
            "playType" => $this->input->get("playType", true),
            "start_money" => $this->input->get("start_money", true),
            "end_money" => $this->input->get("end_money", true),
            "issue" => $this->input->get("issue", true),
            "start_w_time" => $this->input->get("start_w_time", true),
            "end_w_time" => $this->input->get("end_w_time", true)
        );
        $this->filterTime($searchData['start_w_time'], $searchData['end_w_time']);
        $result = $this->Model_order->list_check($searchData, $page, self::NUM_PER_PAGE);
        $pageConfig = array(
            "page" => $page,
            "npp" => self::NUM_PER_PAGE,
            "allCount" => $result[1]
        );
        $pages = get_pagination($pageConfig);
        $pageInfo = array(
            "checks" => $result[0],
            "pages" => $pages,
            "search" => $searchData,
            "tj" => $result[2],
            "fromType" => $this->input->get("fromType", true)
        );
        $this->load->view("order_check", $pageInfo);
    }
    
    /**
     * 参    数：无
     * 作    者：wangl
     * 功    能：订单大奖审核
     * 修改日期：2014.11.05
     */
    public function check()
    {
        $this->check_capacity('2_2_2', true);
        $orderId = $this->input->post("hid_order_id", true);
        $my_status = $this->input->post("hid_status", true);
        $row = $this->Model_order->check($orderId, $my_status, $this->caipiao_cfg);
        if ($row == false)
        {
            return $this->ajaxReturn('n', $this->msg_text_cfg['falied']);
        }
        $this->syslog(7, "派奖审核，订单ID:{$orderId}，审核状态：" . $this->caipiao_ms_cfg['2000'][$my_status][0]);
        return $this->ajaxReturn('y', $this->msg_text_cfg['success']);
    }
    
    /**
     * 参    数：无
     * 作    者：wangl
     * 功    能：异常监控
     * 修改日期：2014.11.05
     */
    public function abnormal_list()
    {
        $page = intval($this->input->get("p"));
        $page = $page <= 1 ? 1 : $page;
        $searchData = array(
            "name" => $this->input->get("name", true),
            "orderId" => $this->input->get("orderId", true),
            "start_time" => $this->input->get("start_time", true),
            "end_time" => $this->input->get("end_time", true),
            "status" => $this->input->get("status", true),
            "mark" => $this->input->get("mark", true),
        	"lid" => $this->input->get("lid", true),
        	"issue" => $this->input->get("issue", true)
        );
        if (empty($searchData['start_time']) && empty($searchData['end_time'])) 
        {
        	$searchData['start_time'] = date("Y-m-d 00:00:00");
        	$searchData['end_time'] = date("Y-m-d 23:59:59");
        }
        else 
        {
        	$this->filterTime($searchData['start_time'], $searchData['end_time']);
        }
        $this->filterTime($searchData['start_time'], $searchData['end_time']);
        $result = $this->Model_order->abnormal_list($searchData, $page, self::NUM_PER_PAGE);
        $pageConfig = array(
            "page" => $page,
            "npp" => self::NUM_PER_PAGE,
            "allCount" => $result[1]
        );
        $pages = get_pagination($pageConfig);
        $pageInfo = array(
            "abnormals" => $result[0],
            "pages" => $pages,
            "search" => $searchData,
            "fromType" => $this->input->get("fromType", true)
        );
        $this->load->view("abnormal", $pageInfo);
    }
    /**
     * 参    数：无
     * 作    者：wangl
     * 功    能：隐藏异常订单
     * 修改日期：2014.11.05
     */
    public function hide_ab()
    {
        $id = intval($this->input->post("id", true));
        $row = $this->Model_order->hide_ab($id);
        if ($row <= 0)
        {
            return $this->ajaxReturn('n', $this->msg_text_cfg['falied']);
        }
        $this->syslog(2, "隐藏异常订单_订单ID:{$id}");
        return $this->ajaxReturn('y', $this->msg_text_cfg['success']);
    }

}
