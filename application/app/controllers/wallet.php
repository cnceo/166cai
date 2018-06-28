<?php

/*
 * APP 支付逻辑
 * @date:2016-01-28
 */

class Wallet extends MY_Controller {
    
    private $salt = '166androidcp';

    public function __construct() 
    {
        parent::__construct();
        $this->load->model('order_model','Order');
        $this->load->model('user_model','User');
        $this->load->model('wallet_model','Wallet');
    }

    /*
     * 扣款 Ajax
     * *********************
     * 流程说明：
     * 1.验证参数，支付密码
     * 2.创建支付流水 trade_no
     * 3.账户扣款
     * 4.扣款完成，通知出票
     * *********************
     * @date:2015-05-08
     */
    public function pay()
    {
        $data = $this->strCode(urldecode($this->input->post('token')));
        $data = json_decode($data, true);

        if( empty($data['uid']) || empty($data['orderId']) )
        {
            $payResult = array(
                'status' => '0',
                'msg' => '订单信息错误',
                'data' => ''
            );
            echo json_encode($payResult);
            die();
        }

        //获取用户基本信息
        $uinfo = $this->User->getUserInfo($data['uid']);
        
        if ($data['orderType'] == '4') {
            $this->load->model('united_order_model');
            
            $orderInfo = $this->united_order_model->getUniteOrderByOrderId($data['orderId'], '(buyMoney+guaranteeAmount) as money, lid');
            
            if ($data['ctype'] == 0) {
                $data['buyMoney'] = $orderInfo['money']/100;
            }
            
            // 组装支付参数
            $uPay = array(
                'uid' => $data['uid'],
                'orderId' => $data['orderId'],
                'money' => $data['buyMoney'],
                'type' => $data['ctype'],
                'buyPlatform' => 1
            );
            
            // 组装订单参数
            $orders = array(
                'uid' => $data['uid'],
                'lid' => $orderInfo['lid'],
                'orderId' => $data['orderId'],
            );
            
            if(ENVIRONMENT === 'checkout')
            {
                $orderUrl = $this->config->item('cp_host');
                $chasePay['HOST'] = $this->config->item('domain');
            }
            else
            {
                // $orderUrl = $this->config->item('pages_url');
                $orderUrl = $this->config->item('protocol') . $this->config->item('pages_url');
            }
            $payStatus = $this->tools->request($orderUrl . 'api/order/doUnitedPay', $uPay);
            $payStatus = json_decode($payStatus, true);
            if($payStatus['code'] == 200)
            {
                $orders['pay_status'] = 'true';
            }
            else
            {
                // 订单状态异常，跳转至投注记录列表页
                if( in_array($payStatus['code'], array('300', '301')) )
                {
                    // 组装用户信息
                    $token = $this->strCode(json_encode(array(
                            'uid' => $data['uid']
                    )), 'ENCODE');
            
                    $payResult = array(
                            'status' => '2',
                            'msg' => $payStatus['msg'],
                            'data' => $this->config->item('pages_url') . 'app/mylottery/betlist/' . urlencode($token)
                    );
                    echo json_encode($payResult);
                    die();
                }
                else
                {
                    $orders['pay_status'] = 'false';
                }
            }
            
        }elseif($data['orderType'] == '5')
        {
            $this->load->model('follow_wallet_model');
            $followOrder = $this->follow_order_model->getFollowOrderDetail($data['orderId']);
            $response = $this->follow_wallet_model->payForAdvance($followOrder);
            $orders = array(
                'uid' => $followOrder['uid'],
                'lid' => $followOrder['lid'],
                'orderId' => $followOrder['followId'],
                'money' => $followOrder['totalMoney']
            );
            if($response['code'] == 200)
            {
                $orders['pay_status'] = 'true';
            }
            else{
                // 订单状态异常，跳转至投注记录列表页
                if( in_array($response['code'], array('400', '401', '402', '403', '404', '405')) )
                {
                    $payResult = array(
                        'status' => '3',
                        'msg' => $response['msg'],
                        'data' => ''
                    );
                    echo json_encode($payResult);
                    die();
                }else{
                   $orders['pay_status'] = 'false'; 
                }                
            }
        }
        // 根据orderType调用网站支付逻辑
        else if($data['orderType'] == '1')
        {
            // 获取订单信息
            $this->load->model('chase_order_model');
            $chaseData = array(
                'uid' => $data['uid'],
                'chaseId' => $data['orderId']
            );

            $chaseInfo = $this->chase_order_model->getChaseInfoById($chaseData);

            // 组装支付参数
            $chasePay = array(
                'uid' => $data['uid'],
                'chaseId' => $chaseInfo['info']['chaseId'],
                'money' => $chaseInfo['info']['money'],
            );

            // 组装订单参数
            $orders = array(
                'uid' => $data['uid'],
                'orderId' => $chaseInfo['info']['chaseId'],
                'lid' => $chaseInfo['info']['lid']
            );

            if(ENVIRONMENT === 'checkout')
            {
                $orderUrl = $this->config->item('cp_host');
                $chasePay['HOST'] = $this->config->item('domain');
            }
            else
            {
                // $orderUrl = $this->config->item('pages_url');
                $orderUrl = $this->config->item('protocol') . $this->config->item('pages_url');
            }

            $payStatus = $this->tools->request($orderUrl . 'api/order/doChasePay', $chasePay);
            $payStatus = json_decode($payStatus, true);

            if($payStatus['status'])
            {
                $orders['pay_status'] = 'true';
            }
            else
            {
                // 订单状态异常，跳转至投注记录列表页
                if( in_array($payStatus['code'], array('300', '301')) )
                {
                    // 组装用户信息
                    $token = $this->strCode(json_encode(array(
                        'uid' => $data['uid']
                    )), 'ENCODE');

                    $payResult = array(
                        'status' => '2',
                        'msg' => $payStatus['msg'],
                        'data' => $this->config->item('pages_url') . 'app/mylottery/betlist/' . urlencode($token)
                    );
                    echo json_encode($payResult);
                    die();
                }
                else
                {
                    $orders['pay_status'] = 'false';
                }
            }

        }
        else
        {
            // 获取订单信息
            $orders = $this->Order->getById($data['orderId']);

            if( $orders['uid'] != $data['uid'] )
            {
                $payResult = array(
                    'status' => '0',
                    'msg' => '订单信息错误',
                    'data' => ''
                );
                echo json_encode($payResult);
                die();
            }

            if(ENVIRONMENT === 'checkout')
            {
                $orderUrl = $this->config->item('cp_host');
                $orders['HOST'] = $this->config->item('domain');
            }
            else
            {
                // $orderUrl = $this->config->item('pages_url');
                $orderUrl = $this->config->item('protocol') . $this->config->item('pages_url');
            }

            $payStatus = $this->tools->request($orderUrl . 'api/order/doPay', $orders);
            $payStatus = json_decode($payStatus, true);

            // log_message('LOG', "统一扣款接口 - 返回参数: " . json_encode($payStatus), 'app_pay');

            if($payStatus['status'])
            {
                $orders['pay_status'] = 'true';
            }
            else
            {
                // 支付失败场景处理
                if(in_array($payStatus['data']['code'], array('12', '16')))
                {
                    // 组装用户信息
                    $token = $this->strCode(json_encode(array(
                        'uid' => $orders['uid']
                    )), 'ENCODE');

                    $payResult = array(
                        'status' => '2',
                        'msg' => $payStatus['data']['msg'],
                        'data' => $this->config->item('pages_url') . 'app/mylottery/betlist/' . urlencode($token)
                    );
                    echo json_encode($payResult);
                    die();
                }
                else
                {
                    $orders['pay_status'] = 'false';
                }           
            }
        }
        
        
        
        $oinfo = array(
            'uid' => $orders['uid'],
            'orderId' => $orders['orderId'],
            'lid' => $orders['lid'],
            'money' => ($data['orderType'] == '1') ? $chaseInfo['info']['money'] : ($data['orderType'] == 4 ? $data['buyMoney'] * 100 : $orders['money']),
            'pay_status' => $orders['pay_status'],
            'orderType' => $data['orderType'] ? $data['orderType'] : 0
        );
        
        if ($data['orderType'] == 4) $oinfo['ctype'] = $data['ctype'];

        // 跳转支付结果
        $token = $this->strCode(json_encode($oinfo), 'ENCODE');
        
        

        $payResult = array(
            'status' => '1',
            'msg' => $payStatus['data']['msg'],
            'data' => $this->config->item('protocol') . $this->config->item('pages_url') . 'app/wallet/payComplete/' . urlencode($token)
        );

        echo json_encode($payResult);
        die();
    }

    /*
     * APP 支付结果页
     * @date:2015-05-06
     */
    public function payComplete($token)
    {
        $orders = $this->strCode(urldecode($token));
        $orders = json_decode($orders, true);

        $this->load->model('lottery_model', 'Lottery');
        $orders['enName'] = $this->Lottery->getEnName($orders['lid']);
        $orders['cnName'] = $this->Lottery->getCnName($orders['lid']);
        $orders['token'] = $this->strCode(json_encode(array(
                    'uid' => $orders['uid']
                )), 'ENCODE');

        // 查看自购、追号订单
        switch ($orders['orderType']) {
            case 4:
                $orders['orderUrl'] = $this->config->item('pages_url') . 'app/hemai/detail/hm' . $orders['orderId'] . '/' . urlencode($orders['token']);
                break;
            case 5:
                $orders['orderUrl'] = $this->config->item('pages_url') . 'app/hemai/gdetail/gd' . $orders['orderId'] . '/' . urlencode($orders['token']);
                break;                    
            case 1:
                $orders['orderUrl'] = $this->config->item('pages_url') . 'app/chase/detail/' . $orders['orderId'] . '/' . urlencode($orders['token']);
                break;
            case 0:
                $orders['orderUrl'] = $this->config->item('pages_url') . 'app/order/detail/' . $orders['orderId'] . '/' . urlencode($orders['token']);
                break;
        }
        // 支付成功页banner
        $orders['banner'] = $this->getPayBanner($orders);

        $this->load->view('order/pay_result', $orders);
    }

    /*
     * APP 充值页接口
     * @date:2015-05-07
     */
    public function recharge($token)
    {   
        $data = $this->strCode(urldecode($token));
        $data = json_decode($data, true);
        //$data['uid'] = 1024;
        if(empty($data['uid']))
        {
            echo "参数校验失败";
            die;
        }
        // 查询用户信息
        $info = $this->User->getUserInfo($data['uid']);
        if(empty($info))
        {
            echo "参数校验失败";
            die;
        }
        $money = $this->Order->getMoney($data['uid']);
        // 查询红包信息
        $this->load->model('redpack_model');
        $this->eventType = $this->redpack_model->getEventType();
        $redpackData = $this->redpack_model->getRedpackInfo($data['uid'], $this->eventType['recharge'], 0, 0);
        // 按充值金额排序
        if(!empty($redpackData))
        {
            $sortArry = array();
            foreach ($redpackData as $items) 
            {
                $use_params = json_decode($items['use_params'], true);
                $sortArry[] = $use_params['money_bar'];
            }
            array_multisort($sortArry, SORT_ASC, $redpackData);
        }
        $versionInfo = $this->getUserAgentInfo();
        // 充值配置
        $payConfig = $this->getPayConfig($versionInfo);
        if (isset($data['checked_idName'])) {
            foreach ($payConfig as $val) {
                if ($val['idName'] == $data['checked_idName']) $checked_idName = $data['checked_idName'];
            }
        }
        if (!isset($checked_idName)) $checked_idName = $payConfig[0]['idName'];
        $uInfo = array(
            'uname'             => $info['uname'],
            'money'             => number_format(ParseUnit($money['money'], 1), 2),
            'token'             => $token,
            'redpackData'       => $redpackData,
            'versionInfo'       => $versionInfo,
            'payConfig'         => $payConfig,
            'checked_idName'    => $checked_idName,
        );
//        if (in_array($versionInfo['channel'], array('sc-cwyx1_cpa_taj','sc-cwyx2_cpa_taj','sc-juzhang_cpc_taj','166cai-sc-lyc_cpa_taj','sc-tuia1_as_lj','sc-tuia_as_lj','sc-smhz_cpc_taj','sc-huaqianwuyou1_cps_taj','sc-huaqianwuyou2_cps_taj','sc-huaqianwuyou3_cps_taj','sc-ruibo_cps_taj','sc-tieluwifi_cpc_taj','sc-moji_cpt_taj','sc-tuiajx_cpc_taj'))) {
//            $this->load->view('wallet/rechargecc', $uInfo);
//        } else {
//            $this->load->view('wallet/recharge', $uInfo);
//        }
        $this->load->view('wallet/recharge', $uInfo);
    }

    /*
     * APP SDK 公共同步通知
     * @date:2016-05-25
     */
    public function rechargeRes($trade_no, $status = 1)
    {
        if(!empty($trade_no))
        {
            $walletInfo = $this->Wallet->getRechargeLog($trade_no);
            if(empty($walletInfo))
            {
                var_dump("订单信息错误");die;
            }

            // 用户信息加密
            $rechargeData = array(
                'uid' => $walletInfo['uid'],                // 用户ID
                'tradeNo' => $walletInfo['trade_no'],       // wallet_log流水号
                'redirectPage' => (!empty($walletInfo['orderId']))?'order':'recharge',   // 跳转类型 充值详情、订单支付页
                'cp_orderId' => $walletInfo['orderId']      // 订单号
            );

            $token = $this->strCode(json_encode($rechargeData), 'ENCODE');

            // 更新同步标识
            $payData['sync_flag'] = 1;
            $this->Wallet->updatePayLog($walletInfo['trade_no'], $payData);

            // SDK跳转至中转页
            $rechargeView = $this->config->item('pages_url') . "app/wallet/rechargeComplete/" . urlencode($token);
            header('Location: ' . $rechargeView);
        }
    }

    /*
     * APP 充值成功中转页
     * @date:2016-08-01
     */
    public function rechargeComplete($token)
    {
        // 等待查询支付结果
        $data = $this->strCode(urldecode($token));
        $data = json_decode($data, true);

        if (empty($data['uid']) OR empty($data['tradeNo']))
        {
            die('订单参数缺失');
        }
        // var_dump($data);die;
        $payLog = $this->Wallet->getPayLog($data['tradeNo'], 1);

        if(empty($payLog))
        {
            die('订单参数缺失');
        }

        if($payLog['status'] == 1)
        {
            // 跳转至成功页
            $rechargeView = $this->config->item('pages_url') . "app/wallet/rechargeDetail/" . $token;
            header('Location: ' . $rechargeView);
        }
        else
        {
            // 进入等待查询页
            $info = array(
                'token' => $token,
            );

            $this->load->view('wallet/paywait', $info);
        } 
    }

    /*
     * APP 充值成功详情页
     * @date:2015-06-15
     */
    public function rechargeDetail($token, $status = 1)
    {
        $data = $this->strCode(urldecode($token));
        $data = json_decode($data, true);

        // 必要参数说明
        // $data = array(
        //  'uid' => '',    // 用户ID
        //  'tradeNo' => '',    // wallet_log流水号
        //  'redirectPage' => '',   // 跳转类型 充值详情、订单支付页
        //  'cp_orderId' => ''  // 订单号
        // );

        if (empty($data['uid']) OR empty($data['tradeNo']))
        {
            die('订单参数缺失');
        }

        $order = $this->Wallet->getWalletLog($data['uid'], $data['tradeNo']);

        $detail = array();
        if(!empty($order))
        {
            $this->config->load('pay');
            $payAllCfg = $this->config->item('pay_all_cfg');
            $tmpAry = explode('@', $order['additions']);

            $detail = array(
                'money' => ParseUnit($order['money'],1),
                'payType' => $payAllCfg['pay_cfg'][$tmpAry[0]]['name'],
                'redirectPage' => $data['redirectPage']
            );

            if( $data['redirectPage'] == 'order' && !empty($data['cp_orderId']) )
            {
                if(ENVIRONMENT === 'checkout')
                {
                    $postUrl = $this->config->item('cp_host');
                }
                else
                {
                    $postUrl = $this->config->item('protocol') . $this->config->item('pages_url');
                }
                //$this->tools->request($postUrl . 'api/order/autopay/' . $data['tradeNo']);
                // 订单信息加密
                $orderDetail = $this->strCode(json_encode(array(
                    'uid' => $data['uid'],
                    'orderId' => $data['cp_orderId'],
                    'orderType' => $order['status']   // 根据status判断 0：自购 1：追号
                )), 'ENCODE');
                if($order['status'] == 3)
                {
                    $this->load->model('united_order_model');
                    $unitedOrder = $this->united_order_model->getOrder(array('o.orderId' => $data['cp_orderId']));
                    // 跳转支付页面
                    $detail['redirectPage'] = $data['redirectPage'];
                    if ($unitedOrder['status'] >= 40)
                    {
                        if($order['subscribeId'] == '1'){
                            $orderDetail = $this->strCode(json_encode(array(
                            'uid' => $data['uid'],
                            'orderId' => $data['cp_orderId'],
                            'orderType' => 4,
                            'ctype' => 1,
                            )), 'ENCODE');
                            $detail['payView'] = $this->config->item('pages_url') . 'app/hemai/detail/hm' . $data['cp_orderId'] . '/' . urlencode($orderDetail);
                        }else{
                            if (in_array($unitedOrder['status'], array(40, 240, 500, 1000, 2000))){
                                $oinfo = array(
                                    'uid' => $data['uid'],
                                    'orderId' => $data['cp_orderId'],
                                    'lid' => $unitedOrder['lid'],
                                    'money' => $unitedOrder['buyTotalMoney'],
                                    'pay_status' => true,
                                    'orderType' => 4
                                );
                                // 跳转支付结果
                                $token = $this->strCode(json_encode($oinfo), 'ENCODE');
                                header('Location: ' . $this->config->item('pages_url') . 'app/wallet/payComplete/'. urlencode($token));
                                die();
                            }
                        }
                    }
                    else
                    {
                        $orderDetail = $this->strCode(json_encode(array(
                        'uid' => $data['uid'],
                        'orderId' => $data['cp_orderId'],
                        'orderType' => 4,
                        'buyMoney' => $order['money'],
                        'ctype' => 0,
                        )), 'ENCODE');
                        $detail['payView'] = $this->config->item('pages_url') . "app/order/doPay/" . urlencode($orderDetail);
                    }
                }
                else
                {
                    if($order['status'] == 0){
                        $neworder = $this->Order->getById($data['cp_orderId']);
                        if (in_array($neworder['status'], array(40, 240, 500, 1000, 2000)))
                        {
                            $oinfo = array(
                                'uid' => $data['uid'],
                                'orderId' => $data['cp_orderId'],
                                'lid' => $neworder['lid'],
                                'money' => $neworder['money'],
                                'pay_status' => true,
                                'orderType' => 0
                            );
                            // 跳转支付结果
                            $token = $this->strCode(json_encode($oinfo), 'ENCODE');
                            header('Location: ' . $this->config->item('pages_url') . 'app/wallet/payComplete/'. urlencode($token));
                            die();
                        }
                    }elseif($order['status'] == 1){
                        $this->load->model('chase_order_model');
                        $chaseData = array(
                                'uid' => $data['uid'],
                                'chaseId' => $data['cp_orderId']
                        );
                        $chaseOrder = $this->chase_order_model->getChaseInfoById($chaseData);
                        $orderStatus = $this->chase_order_model->getStatus();
                        if(in_array($chaseOrder['info']['status'], array(240,500,700)))
                        {
                            $oinfo = array(
                                'uid' => $data['uid'],
                                'orderId' => $data['cp_orderId'],
                                'lid' => $chaseOrder['info']['lid'],
                                'money' => $chaseOrder['info']['money'],
                                'pay_status' => true,
                                'orderType' => 1
                            );
                            // 跳转支付结果
                            $token = $this->strCode(json_encode($oinfo), 'ENCODE');
                            header('Location: ' . $this->config->item('pages_url') . 'app/wallet/payComplete/'. urlencode($token));
                            die();
                        }
                    }
                    // 自购和追号
                    $orderDetail = $this->strCode(json_encode(array(
                        'uid' => $data['uid'],
                        'orderId' => $data['cp_orderId'],
                        'orderType' => $order['status']   // 根据status判断 0：自购 1：追号
                    )), 'ENCODE');
                    // 跳转支付页面
                    $detail['redirectPage'] = $data['redirectPage'];
                    $detail['payView'] = $this->config->item('pages_url') . "app/order/doPay/" . urlencode($orderDetail);
                }
            }
        }

        $detail['status'] = $status ? TRUE : FALSE;
        $this->load->view('wallet/result', $detail); 
    }

    /*
     * APP 模拟充值提交
     * @date:2015-12-21
     */
    public function doPayForm()
    {
        $params = $this->input->post(null, true);
        // 验证前端提交参数
        $tokenStr = md5("{$params['trade_no']}{$params['uid']}{$params['money']}{$params['ip']}{$params['real_name']}{$params['id_card']}{$params['merId']}{$params['configId']}{$params['pay_type']}{$this->salt}");

        if($tokenStr == $params['token'])
        {
            switch ($params['pay_type']) 
            {
                case 'yeepayMPay':
                    $this->yeepayMPay($params);
                    break;
                case 'sumpayWap':
                    $this->sumpayWap($params);
                    break;
                case 'xzZfbWap':
                    $this->xzZfbWap($params);
                    break;         
                case 'jdPay':
                    $this->jdPayWap($params);
                    break;
                case 'umPay':
                    $this->UmPay($params);
                    break;
                case 'hjZfbPay':
                    $this->hjZfbPay($params);
                    break;
                case 'hjWxWap':
                    $this->hjWxWap($params);
                    break;
                case 'hjZfbWap':
                case 'hjZfbSh':
                    $this->hjZfbWap($params);
                    break;
                case 'yzpay':
                    $this->hjZfbWap($params);
                    break;
                default:
                    $postData = array();
                    break;
            }
        }
        else
        {
            die("充值参数错误");
        }        
    }

    // 易宝支付
    private function yeepayMPay($params)
    {
        $params['configId'] = 2;
        $params['lib'] = 'YeepayMPay';
        $responeData = apiRequest('api/RechargeHandle','request',$params);
        header('Location: ' . $responeData['data']['url']);
    }
    // 统统付wap
    private function sumpayWap($params)
    {
        $params['configId'] = 9;
        $params['lib'] = 'SumPay';
        $responeData = apiRequest('api/RechargeHandle','request',$params);
        $payData = $responeData['data'];
        // 填充页面 提交表单
        $this->load->view('wallet/pay/sumpay', array('payData' => $payData));
    }

    // 统统付wap
    private function xzZfbWap($params)
    {

        $params['configId'] = 9;
        $params['lib'] = 'SumPay';
        $responeData = apiRequest('api/RechargeHandle','request',$params);;
        $payData = $responeData['data'];
        header('location:' . $payData['action']); die;
        // 填充页面 提交表单
        $this->load->view('wallet/pay/xzzfb', array('payData' => $payData));
    }
    /**
     * [hjZfbPay 汇聚无限支付宝h5]
     * @author LiKangJian 2017-07-05
     * @param  [type] $params [description]
     * @return [type]         [description]
     */
    private function hjZfbPay($params)
    {
        $params['lib'] = 'HjPay';
        $responeData = apiRequest('api/RechargeHandle','request',$params);
        if($responeData['code'] && $responeData['data']['return_code'] == 'SUCCESS' && $responeData['data']['result_code'] == 'SUCCESS')
        {
          // 填充页面 提交表单
          $this->load->view('wallet/pay/hjzfb', array('payData' => $responeData['data']));
        }else{
          echo '支付失败~';die;
        }

    }
    /**
     * [hjWxWap 微信H5-兴业银行]
     * @author LiKangJian 2017-11-20
     * @param  [type] $params [description]
     * @return [type]         [description]
     */
    private function hjWxWap($params)
    {
        $params['lib'] = 'HjPay';
        $responeData = apiRequest('api/RechargeHandle','request',$params);
        if($responeData['code'] && $responeData['data']['return_code'] == 'SUCCESS' && $responeData['data']['result_code'] == 'SUCCESS')
        {
          $this->load->view('wallet/pay/hjWxWap', array('payData' => $responeData['data']));
        }else{
          echo '支付失败~';die;
        }

    }
    /**
     * [hjZfbWap 支付宝H5-鸿粤浦发银行]
     * @author LiKangJian 2017-11-20
     * @param  [type] $params [description]
     * @return [type]         [description]
     */
    private function hjZfbWap($params)
    {
        $params['lib'] = 'HjPay';
        $responeData = apiRequest('api/RechargeHandle','request',$params);
        if($responeData['code'] && $responeData['data']['return_code'] == 'SUCCESS' && $responeData['data']['result_code'] == 'SUCCESS')
        {
          // 填充页面 提交表单
          $this->load->view('wallet/pay/hjZfbWap', array('payData' => $responeData['data']));
          
        }else{
          echo '支付失败~';die;
        }

    }
    //京东支付
    private function jdPayWap($params)
    {
        $params['configId'] = 69;
        $params['lib'] = 'JdPay';
        $responeData = apiRequest('api/RechargeHandle','request',$params);
        $payData = $responeData['data'];
        // 填充页面 提交表单
        $this->load->view('wallet/pay/jdpay', array('payData' => $payData));
    }
    
    private function UmPay($params)
    {
        $postData = array(
                'trade_no'      =>  $params['trade_no'],
                'money'         =>  $params['money'],   // 按分处理
                'merId'         =>  '50045',
                'configId'      =>  '72',
                'id_card'       =>  $params['id_card'],
                'ip'            =>  UCIP,
                'uid'           =>  $this->uid,
                'real_name'     =>  $params['real_name'],
        );
        if ($params['change_bankid']) $this->redirect("/app/paybank/cardlist/".urlencode($this->strCode(json_encode($params), 'ENCODE')));
        
        $this->load->model('pay_bank_model');
        $this->load->model('wallet_model', 'Wallet');
        $REDIS = $this->config->item('REDIS');
        $bankinfo = unserialize($this->cache->hGet($REDIS['PAY_BANK_INFO'], $this->uid));
        $bdfault = 0;
        foreach ($bankinfo as $bval)
        {
            $binfo[$bval['bank_id']] = $bval['pay_agreement'];
            if ($bval['is_default']) $bdfault = $bval['bank_id'];
        }
        
        if (empty($params['bank_id'])) {
                        
            if (empty($bdfault)) {
                $this->redirect("/app/paybank/add/".urlencode($this->strCode(json_encode($params), 'ENCODE')));
            }else {
                if (!empty($binfo[$bdfault])) {
                    $payagreement = json_decode($binfo[$bdfault], true);
                    $postData['pay_agreement_id'] = $payagreement['umpay'];
                }else {
                    $postData['bank_id'] = $bdfault;
                }
                $this->load->model('wallet_model', 'Wallet');
                $this->Wallet->updatePayLog($params['trade_no'], array('bank_id' => $bdfault));
            }
        }elseif ($binfo[$params['bank_id']]) {
            
            $this->Wallet->updatePayLog($params['trade_no'], array('bank_id' => $params['bank_id']));
            if (!empty($binfo[$params['bank_id']])) {
                $payagreement = json_decode($binfo[$params['bank_id']], true);
                $postData['pay_agreement_id'] = $payagreement['umpay'];
            }else {
                $postData['bank_id'] = $params['bank_id'];
            }
        }else {
            $this->Wallet->updatePayLog($params['trade_no'], array('bank_id' => $params['bank_id']));
            $postData['bank_id'] = $params['bank_id'];
        }
                    
        $postData['lib'] = 'UmPay';
        $responeData = apiRequest('api/RechargeHandle','request',$postData);    

        $this->redirect($responeData['data']['payUrl']);
    }

    /*
     * 查询订单支付结果
     * @date:2016-08-15
     */
    public function getRechargeStatus()
    {
        $token = $this->input->post('token');
        $num = $this->input->post('num', true);
        $flag = $this->input->post('flag', true);
        $data = $this->strCode(urldecode($token));
        $data = json_decode($data, true);

        $num = $num ? $num : 10;
        $flag = $flag ? 0 : 1;

        $result = array(
            'status' => '0',
            'msg' => '无查询结果',
            'data' => ''
        );

        if(!empty($data['tradeNo']))
        {
            $payLog = $this->Wallet->getPayLog($data['tradeNo'], $flag);

            if(!empty($payLog) && $payLog['status'] == 1)
            {
                // 跳转至成功页
                
                $result = array(
                    'status' => '1',
                    'msg' => '支付成功',
                    'data' => $this->config->item('pages_url') . "app/wallet/rechargeDetail/" . $token
                );
            }
            elseif($payLog['select_num'] < $num)
            {
                $payData['select_num'] = $payLog['select_num'] + 1;
                $this->Wallet->updatePayLog($data['tradeNo'], $payData); //更新刷新次数
            }
            else
            {
                $result = array(
                    'status' => '2',
                    'msg' => '查询超时',
                    'data' => ''
                );
            }
        }
        
        echo json_encode($result); 
        exit();
    }
    
    private function getPayBanner($orderInfo)
    {
        // 版本信息
        $versionInfo = $this->getUserAgentInfo();
        
        $bannerInfo = array();
        if($orderInfo['pay_status'])
        {
            // 获取弹窗信息缓存
            $this->load->model('cache_model','Cache');
            $info = $this->Cache->getPreloadInfo($platform = 'android', 'payResult');
            
            $detail = $info[$orderInfo['lid']];
            if(!empty($detail) && (!empty($detail['webUrl']) || in_array($detail['appAction'], array('bet', 'email'))))
            {
                if($detail['appAction'] == 'email' && $versionInfo['appVersionCode'] < '11')
                {
                    $detail['appAction'] = 'unsupport';
                }
                $uinfo = $this->User->getUserInfo($orderInfo['uid']);  
                if($detail['appAction'] == 'email' && !empty($uinfo['email']))
                {
                    $detail['appAction'] = 'ignore';
                }
                $bannerInfo = array(
                    'imgUrl' => (strpos($detail['imgUrl'], 'http') !== FALSE) ? $detail['imgUrl'] : $this->config->item('protocol') . $detail['imgUrl'],
                    'webUrl' => $detail['webUrl'],
                    'appAction' => $detail['appAction'],
                    'tlid' => $detail['tlid'],
                    'enName' => $this->Lottery->getEnName($detail['tlid'])
                );
            }
        }
        return $bannerInfo;
    }

    public function getWinxin($token = '')
    {
        $data = $this->strCode(urldecode($token));
        $data = json_decode($data, true);
        $walletInfo = $this->Wallet->getRechargeLog($data['orderId']);
        if(empty($walletInfo))
        {
            var_dump("订单信息错误");die;
        }

        // 用户信息加密
        $rechargeData = array(
            'uid' => $walletInfo['uid'],                // 用户ID
            'tradeNo' => $walletInfo['trade_no'],       // wallet_log流水号
            'redirectPage' => (!empty($walletInfo['orderId']))?'order':'recharge',   // 跳转类型 充值详情、订单支付页
            'cp_orderId' => $walletInfo['orderId']      // 订单号
        );

        $data['token'] = urlencode($this->strCode(json_encode($rechargeData), 'ENCODE'));
        
        $this->load->view('wallet/pay/weixinpay', array('params' => $data));
    }

    // 二维码
    public function qrCode($content, $size = 7)
    {
        require_once APPPATH . '/libraries/phpqrcode.php';
        $content = base64_decode(urldecode($content));
        QRcode::png($content, false, QR_ECLEVEL_L, $size, 0);
    }

    // 扫码介绍
    public function wxintro()
    {
        $this->load->view('wallet/pay/wxintro');
    }

    public function getPayConfig($versionInfo)
    {
        // 获取缓存 - 区分马甲包
        if($this->checkChannelPackage($versionInfo['channel']))
        {
            $platform = $this->config->item('platform') + 4;
        }
        else
        {
            $platform = $this->config->item('platform') + 1;
        }
        
        $this->load->driver('cache', array('adapter' => 'redis'));
        $REDIS = $this->config->item('REDIS');
        if(in_array($_SERVER['SERVER_ADDR'],array('120.132.33.194','123.59.105.39'))){
            $info = $this->cache->hGet($REDIS['CS_PAY_CONFIG'], $platform);
        }else{
            $info = $this->cache->hGet($REDIS['PAY_CONFIG'], $platform);
        }
        $info = json_decode($info, true);
        $payConfig = array();
        if(!empty($info))
        {
            $config = array(
                '6_0' =>  array(
                    'pay_type'  =>  '6_0',
                    'idName'    =>  'payCard',
                    'className' =>  'pay-card',
                    'title'     =>  '使用银行卡支付',
                    'desc'      =>  '最高5万/日',
                    'jsaction'  =>  'dopayform',
                    'maxmoney'  =>  '0',
                    'secredpack'=>  '0',
                ),
                '2_0' =>  array(
                    'pay_type'  =>  '2_0',
                    'idName'    =>  'payWx',
                    'className' =>  'pay-wx',
                    'title'     =>  '微信支付',
                    'desc'      =>  '最高5万/日',
                    'jsaction'  =>  'wxpay',
                    'maxmoney'  =>  '0',
                    'secredpack'=>  '0',
                ),
                '4_0' =>  array(
                    'pay_type'  =>  '4_0',
                    'idName'    =>  'payZfb',
                    'className' =>  'pay-zfb',
                    'title'     =>  '支付宝支付',
                    'desc'      =>  '最高5千/笔',
                    'jsaction'  =>  'dopayform',
                    'maxmoney'  =>  '5000',
                    'secredpack'=>  '5000',
                ),
                '1_6' =>  array(
                    'pay_type'  =>  '1_6',
                    'idName'    =>  'payTtf',
                    'className' =>  'pay-ttf',
                    'title'     =>  '银行卡快捷-统统付',
                    'desc'      =>  '最高3千/笔，5千/日，1万/月',
                    'jsaction'  =>  'dopayform',
                    'maxmoney'  =>  '3000',
                    'secredpack'=>  '3000',
                ),
                '1_1' =>  array(
                    'pay_type'  =>  '1_1',
                    'idName'    =>  'payYbzf',
                    'className' =>  'pay-ybzf',
                    'title'     =>  '银行卡快捷-易宝支付',
                    'desc'      =>  '最高5千/笔，1万/日，2万/月',
                    'jsaction'  =>  'dopayform',
                    'maxmoney'  =>  '5000',
                    'secredpack'=>  '2000',
                ),
                '3_0' =>  array(
                    'pay_type'  =>  '3_0',
                    'idName'    =>  'payWxSao',
                    'className' =>  'pay-wx',
                    'title'     =>  '微信扫码支付',
                    'desc'      =>  '最高5万/日',
                    'jsaction'  =>  'hrefrediect',
                    'maxmoney'  =>  '0',
                    'secredpack'=>  '0',
                ),
                '1_4'  =>  array(
                    'pay_type'  =>  '1_4',
                    'idName'    =>  'payLl',
                    'className' =>  'pay-ll',
                    'title'     =>  '银行卡快捷-连连支付',
                    'desc'      =>  '免手续费，最高5000/笔',
                    'jsaction'  =>  'llpay',
                    'maxmoney'  =>  '5000',
                    'secredpack'=>  '5000',
                ),
                '8_0'  =>  array(
                    'pay_type'  =>  '8_0',
                    'idName'    =>  'payJd',
                    'className' =>  'pay-jd',
                    'title'     =>  '京东支付',
                    'desc'      =>  '最高5千/笔，1万/日',
                    'jsaction'  =>  'jdpay',
                    'maxmoney'  =>  '20000',
                    'secredpack'=>  '5000',
                ),
            );
            
            $info = array_unique($info);
            $count = 0;
            $this->config->load('bank');
            $bankcode = $this->config->item('bank_code');
            $bankinfo = unserialize($this->cache->hGet($REDIS['PAY_BANK_INFO'], $this->uid));
            foreach ($info as $key => $val) 
            {
                // 微信支付版本判断
                if($val == '2_0' && $versionInfo['appVersionCode'] < '8') continue;
                if($val == '4_0' && $versionInfo['appVersionCode'] < '17') continue;
                if ($val == '6_0') {
                    if (!empty($bankinfo)) {
                        foreach ($bankinfo as $bval) {
                            if ($bval['is_default']) {
                                $config[$val]['bank_id'] = $bval['bank_id'];
                                $config[$val]['className'] = "pay-card pay-".$bval['bank_type'];
                                $config[$val]['title'] = mb_strlen($bankcode[$bval['bank_type']], 'utf-8') > 6 ? mb_substr($bankcode[$bval['bank_type']], 0, 6, 'utf-8') : $bankcode[$bval['bank_type']];
                            }
                        }
                    }
                }
                $payConfig[] = $config[$val];
            }
        }
        return $payConfig;
    }
    /**
     * [wftZfbWap 针对兴业支付宝H5]
     * @author LiKangJian 2017-09-14
     * @return [type] [description]
     */
    public function wftZfbWap($orderId)
    {
        $backUrl = $_GET['backurl'].'?trade_no='.$orderId;
        $this->load->view('wallet/pay/wftZfbWap',array('backUrl' =>$backUrl) );
    }
    
    public function wftWxWap($orderId)
    {
        $backUrl = $_GET['backurl'].'&service=pay.weixin.wappayv2&trade_no='.$orderId;
        $this->load->view('wallet/pay/wftWxWap',array('backUrl' =>$backUrl) );
    }

    public function getWalletStatus($token)
    {
        $data = $this->strCode(urldecode($token));
        $data = json_decode($data, true);
        if (empty($data['trade_no'])) {
            die(json_encode(array('status' => false)));
        }
        $payLog = $this->Wallet->getPayLogStatus($data['trade_no']);
        if (empty($payLog)) {
            die(json_encode(array('status' => false)));
        }
        if ($payLog['status'] == 1) {
            // 用户信息加密
            $walletInfo = $this->Wallet->getRechargeLog($data['trade_no']);
            $rechargeData = array(
                'uid' => $walletInfo['uid'],                // 用户ID
                'tradeNo' => $walletInfo['trade_no'],       // wallet_log流水号
                'redirectPage' => (!empty($walletInfo['orderId']))?'order':'recharge',   // 跳转类型 充值详情、订单支付页
                'cp_orderId' => $walletInfo['orderId']      // 订单号
            );
            $token = urlencode($this->strCode(json_encode($rechargeData), 'ENCODE'));
            $rechargeView = $this->config->item('pages_url') . "app/wallet/rechargeDetail/" . $token;
            die(json_encode(array('status' => true, 'url' => $rechargeView)));
        } else {
            die(json_encode(array('status' => false)));
        }
    }

    public function pufaWxWap($orderId)
    {
        $backUrl = ($_GET['backurl']);
        $this->load->view('wallet/pay/wftWxWap',array('backUrl' =>$backUrl) );
    }
    
    public function tomatoPay($orderId)
    {
        $backUrl = base64_decode($_GET['backurl']);
        echo $backUrl;
    }
    
    public function tomatoWxPay($orderId)
    {
        $backUrl = base64_decode($_GET['backurl']);
        $this->load->view('wallet/pay/wftWxWap',array('backUrl' =>$backUrl) );
    }
}