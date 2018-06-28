<?php

/*
 * APP 追号
 * @date:2016-03-10
 */

class Chase extends MY_Controller {
	
    public function __construct() 
    {
        parent::__construct();
        $this->checkUserAgent();
        $this->versionInfo = $this->getUserAgentInfo();
        $this->load->model('chase_order_model');
        $this->load->model('lottery_model', 'Lottery');
    }

    /*
     * APP 追号大订单详情
     * @date:2016-03-10
     */
    public function detail($chaseId, $strCode)
    {
        $data = $this->strCode(urldecode($strCode));
        $data = json_decode($data, true);

        // if($data['uid'] != $this->uid)
        // {
        //     echo '访问错误';
        //     die;
        // }
        
        // 查询大订单详情
        $chaseData = array(
            'uid' => $data['uid'],
            'chaseId' => $chaseId
        );
        $chaseManageInfo = $this->chase_order_model->getChaseInfoById($chaseData);

        // 获取大订单状态
        $chaseStatus = $this->chase_order_model->getStatus();

        // 获取子订单状态
        $this->load->config('order');
        $orderStatus = $this->config->item("cfg_orders");

        $chaseInfo = array();
        if(!empty($chaseManageInfo))
        {
            $chaseInfo = array(
                'lid' => $chaseManageInfo['info']['lid'],
                'chaseId' => $chaseManageInfo['info']['chaseId'],
                'cnName' => $this->Lottery->getCnName($chaseManageInfo['info']['lid']),
                'enName' => $this->Lottery->getEnName($chaseManageInfo['info']['lid']),
                'status' => $chaseManageInfo['info']['status'],
                'chaseStatus' => $chaseStatus,
                'hasstop' => $chaseManageInfo['info']['hasstop'],
                'money' => $chaseManageInfo['info']['money'],
                'setStatus' => $chaseManageInfo['info']['setStatus'],
                'setMoney' => $chaseManageInfo['info']['setMoney'],
                'bonus' => $chaseManageInfo['info']['bonus'],
                'mangeStatus' => $this->getChaseStatus($chaseManageInfo['info'], $chaseStatus),
                'mangeProgress' => chase_status($chaseManageInfo['info']['status'], $chaseStatus),
                'chaseIssue' => $chaseManageInfo['info']['chaseIssue'],
                'totalIssue' => $chaseManageInfo['info']['totalIssue'],   
                'created' => $chaseManageInfo['info']['created'],    
                'pay_time' => $chaseManageInfo['info']['pay_time'],
                'orderStatus' => $this->orderConfig('orders'),   // 获取订单状态
                'chaseBtnStatus' => ($chaseManageInfo['info']['status'] == $chaseStatus['create']) ? '10' : '0',    // 用于原生待支付按钮
                'orderType' => '1',
            	'chaseType' => $chaseManageInfo['info']['chaseType'],
                'is_hide' => ($this->versionInfo['appVersionCode'] >= '40100') ? $chaseManageInfo['info']['is_hide'] : '1',
                'versionInfo' => $this->versionInfo,
                'token' => $this->strCode(json_encode(array(
                        'uid' => $data['uid'],
                    )), 'ENCODE'),
                'codeStr' => $this->strCode(json_encode(array(
                        'uid' => $data['uid'],
                        'chaseId' => $chaseManageInfo['info']['chaseId'],
                    )), 'ENCODE'),
            );
            $this->load->model('order_model', 'Order');
            $this->load->library('BetCnName');
            $orderList = array();
            if(!empty($chaseManageInfo['detail']))
            {
                foreach ($chaseManageInfo['detail'] as $key => $items) 
                {
                    $orderList[$key]['index'] = $key + 1;
                    $orderList[$key]['issue'] = $items['issue'];
                    $orderList[$key]['status'] = $items['status'];
                    $orderList[$key]['money'] = $items['money'];
                    $orderList[$key]['statusMsg'] = ($chaseInfo['status'] < $chaseStatus['is_chase']) ? '待付款' : $this->getOrderStatus($items, $chaseInfo['orderStatus']);
                    $orderList[$key]['orderUrl'] = ($items['status'] < $orderStatus['drawing'] || $items['status'] == $orderStatus['revoke_by_user'] || $items['status'] == $orderStatus['revoke_by_system'] || $items['status'] == $orderStatus['revoke_by_award']) ? 'javascript:void(0);' : $this->config->item('pages_url') . 'app/order/detail/' . $items['orderId'] . '/' . urlencode($chaseInfo['token']);
                    if(strtotime($items['award_time']) > time()){
                        $orderList[$key]['str'] = date("m-d H:i", strtotime($items['award_time'])).'开奖';
                    }else{
                        $awardDetail = $this->Order->getNumIssue($chaseManageInfo['info']['lid'], $items['issue']);
                        if (in_array($chaseManageInfo['info']['lid'], array(Lottery_Model::KS, Lottery_Model::JLKS, Lottery_Model::JXKS))) {
                            $this->load->library('Ks');
                            $award = $this->ks->index($chaseManageInfo['info']['codes'], $awardDetail);
                        }else{
                            $this->load->library('Lottery');
                            $award = $this->lottery->index($chaseManageInfo['info']['codes'], $awardDetail);
                        }
                        $award['atpl'] = str_replace(array('blue-ball'), array('blue-num'), $award['atpl']);
                        if ($chaseManageInfo['info']['lid'] == 54) {
                            $orderList[$key]['str'] = '<div class="poker">' . $award['atpl'] . '</div>';
                        } else {
                            $orderList[$key]['str'] = '<div class="num-group">' . $award['atpl'] . '</div>';
                        }
                    }
                }
            }
            // 追号子订单信息
            $chaseInfo['orderList'] = $orderList;

            // 数字彩继续购买方案
            if($chaseManageInfo['info']['lid'] == Lottery_Model::FCSD || $chaseManageInfo['info']['lid'] == Lottery_Model::PLS)
            {
                // 是否包含组三单式
                if(strpos($chaseManageInfo['info']['codes'], ':2:1') !== FALSE && $this->versionInfo['appVersionCode'] < '11')
                {
                    $chaseManageInfo['info']['codes'] = '';
                }
                if($chaseManageInfo['info']['buyPlatform'] == '0' && $this->versionInfo['appVersionCode'] <= '30701')
                {
                    $chaseManageInfo['info']['codes'] = '';
                }
            }
            $chaseInfo['orderPlan'] = array(
                'codes'     => $chaseManageInfo['info']['codes'],
                'lid'       => $chaseManageInfo['info']['lid'],
                'isChase'   => $chaseManageInfo['info']['isChase'],
            );
        }
        if (!isset($award)) {
            if (in_array($chaseManageInfo['info']['lid'], array(Lottery_Model::KS, Lottery_Model::JLKS, Lottery_Model::JXKS))) {
                $awardDetail = $this->Order->getNumIssue($chaseManageInfo['info']['lid'], $chaseInfo['orderList'][0]['issue']);
                $this->load->library('Ks');
                $award = $this->ks->index($chaseManageInfo['info']['codes'], $awardDetail);
            } else {
                $awardDetail = $this->Order->getNumIssue($chaseManageInfo['info']['lid'], $chaseInfo['orderList'][0]['issue']);
                $this->load->library('Lottery');
                $award = $this->lottery->index($chaseManageInfo['info']['codes'], $awardDetail);
            }
        }
        $award['tpl'] = str_replace(array('bingo', 'special'), array('', '',), $award['tpl']);
        $chaseInfo['award'] = $award;
        $this->load->view('chase/detail', $chaseInfo);
    }

    /*
     * APP 追号大订单状态
     * @date:2016-03-10
     */
    public function getChaseStatus($chaseInfo, $chaseStatus)
    {
        if($chaseInfo['status'] == $chaseStatus['create'])
        {
            $status = '待付款';
        }
        elseif($chaseInfo['status'] == $chaseStatus['is_chase'] && $chaseInfo['bonus'] == 0)
        {
            $status = '静待大奖';
        }
        else
        {
            if($chaseInfo['bonus'] > 0)
            {
                $status = '中奖<em>' . ParseUnit($chaseInfo['bonus'], 1) . '</em>元';
            }
            else
            {
                $status = '未中奖';
            }
        }
        return $status;
    }

    /*
     * APP 子订单状态
     * @date:2016-03-10
     */
    public function getOrderStatus($orderInfo, $orderStatus)
    {
        if($orderInfo['status'] < $orderStatus['drawing'])
        {
            $status = '待出票';
        }
        elseif($orderInfo['status'] == $orderStatus['win']) 
        {
            $status = '中奖' . ParseUnit($orderInfo['bonus'], 1) . '元';
        }
        elseif($orderInfo['status'] == $orderStatus['notwin']) 
        {
            $status = '未中奖';
        }
        elseif($orderInfo['status'] == $orderStatus['draw_part'])
        {
            $status = '部分出票成功';
        }
        elseif($orderInfo['status'] == $orderStatus['revoke_by_user'])
        {
            $status = '手动撤单';
        }
        elseif($orderInfo['status'] == $orderStatus['revoke_by_system'])
        {
            $status = '系统撤单';
        }
        elseif($orderInfo['status'] == $orderStatus['revoke_by_award'])
        {
            $status = '中奖后撤单';
        }
        else
        {
            $status = parse_order_status($orderInfo['status'], $orderInfo['my_status']);
        }
        return $status;
    }

    /*
     * APP 全部撤单
     * @date:2016-03-10
     */
    public function cancelChase()
    {
        $chaseId = $this->input->post('chaseId', true);

        if(empty($this->uid))
        {
            $result = array(
                'status' => '0',
                'msg' => '订单信息失效',
                'data' => ''
            );
            echo json_encode($result);
            exit();
        }

        // 查询大订单详情
        $chaseData = array(
            'uid' => $this->uid,
            'chaseId' => $chaseId
        );
        $chaseManageInfo = $this->chase_order_model->getChaseInfoById($chaseData);

        // 获取大订单状态
        $chaseStatus = $this->chase_order_model->getStatus();

        // 检查大订单是否为追号中
        if($chaseManageInfo['info']['status'] != $chaseStatus['is_chase'])
        {
            $result = array(
                'status' => '0',
                'msg' => '订单信息失效',
                'data' => ''
            );
            echo json_encode($result);
            exit();
        }

        if(empty($chaseManageInfo['detail']))
        {
            $result = array(
                'status' => '0',
                'msg' => '订单信息失效',
                'data' => ''
            );
            echo json_encode($result);
            exit();
        }
        
        // 筛选未投注子订单
        $issueArry = array();
        foreach ($chaseManageInfo['detail'] as $items) 
        {
            if($items['status'] == '0')
            {
                array_push($issueArry, $items['issue']);
            }
        }
        
        if(empty($issueArry))
        {
            $result = array(
                'status' => '0',
                'msg' => '没有可撤单的期次',
                'data' => ''
            );
            echo json_encode($result);
            exit();
        }

        // 撤单
        $handleResult = $this->chase_order_model->stopOrders($this->uid, $chaseId, $chaseManageInfo['info']['lid'], $issueArry);

        if($handleResult == '0' || $handleResult == '1')
        {
            $result = array(
                'status' => '1',
                'msg' => '撤单成功',
                'data' => ''
            );
        }
        else
        {
            $result = array(
                'status' => '0',
                'msg' => '撤单失败',
                'data' => ''
            );
        }
        echo json_encode($result);
        exit();
    }

    // 删除订单
    public function chaseOrderDel()
    {
        $data = $this->strCode(urldecode($this->input->post('codeStr')));
        $data = json_decode($data, true);

        $result = array(
            'status' => '0',
            'msg' => '订单删除失败，请稍后再试',
            'data' => ''
        );

        if(!empty($data['uid']) && !empty($data['chaseId']) && $data['uid'] == $this->uid)
        {
            if($this->chase_order_model->hideChaseOrder($data['uid'], $data['chaseId']))
            {
                $result = array(
                    'status' => '1',
                    'msg' => '订单删除成功',
                    'data' => ''
                );
            } 
        }
        die(json_encode($result));
    }
}