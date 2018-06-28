<?php

/*
 * IOS 支付逻辑
 * @date:2016-01-28
 */

class Wallet extends MY_Controller {
    
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
        $postData = $this->input->post();

        $data = $this->strCode(urldecode($postData['token']));
        $data = json_decode($data, true);

        if( empty($data['userId']) || empty($data['orderId']) )
        {
            $payResult = array(
                'status' => '0',
                'msg' => '订单信息错误',
                'data' => ''
            );
            echo json_encode($payResult);
            die();
        }

        // 1.0版本关闭支付充值
        if($data['appVersionCode'] < 3)
        {
            $payResult = array(
                'status' => '0',
                'msg' => '最后调试中，将于近期开放，敬请期待！',
                'data' => ''
            );
            echo json_encode($payResult);
            exit();
        }     

        //获取用户基本信息
        $uinfo = $this->User->getUserInfo($data['userId']);
        if ($data['orderType'] == 4) 
        {
            $this->load->model('united_order_model');
             
            $orderInfo = $this->united_order_model->getUniteOrderByOrderId($data['orderId'], '(buyMoney+guaranteeAmount) as money, lid');
            
            if ($data['ctype'] == 0) 
            {
                $data['payMoney'] = $orderInfo['money'];
            }
            
            // 组装支付参数
            $uPay = array(
                    'uid' => $data['uid'],
                    'orderId' => $data['orderId'],
                    'money' => $data['payMoney']/100,
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
                            'data' => $this->config->item('pages_url') . 'ios/mylottery/betlist/' . urlencode($token)
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
        elseif($data['orderType'] == '5')
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
        elseif($data['orderType'] == '1')
        {
            // 获取订单信息
            $this->load->model('chase_order_model');
            $chaseData = array(
                'uid' => $data['userId'],
                'chaseId' => $data['orderId']
            );

            $chaseInfo = $this->chase_order_model->getChaseInfoById($chaseData);

            // 组装支付参数
            $chasePay = array(
                'uid' => $data['userId'],
                'chaseId' => $chaseInfo['info']['chaseId'],
                'money' => $chaseInfo['info']['money'],
            );

            // 组装订单参数
            $orders = array(
                'uid' => $data['userId'],
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
                        'uid' => $data['userId']
                    )), 'ENCODE');

                    $payResult = array(
                        'status' => '2',
                        'msg' => $payStatus['msg'],
                        'data' => $this->config->item('pages_url') . 'ios/mylottery/betlist/' . urlencode($token)
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

            if( $orders['uid'] != $data['userId'] )
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
                        'data' => $this->config->item('pages_url') . 'ios/mylottery/betlist/' . urlencode($token)
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
            'money' => ($data['orderType'] == '1') ? $chaseInfo['info']['money'] : ($data['orderType'] == 4 ? $data['payMoney'] : $orders['money']),
            'pay_status' => $orders['pay_status'],
            'orderType' => $data['orderType'] ? $data['orderType'] : 0,
            'channel' => $data['channel'],
            'appVersionCode' => $data['appVersionCode']
        );
        
        if ($data['orderType'] == 4) $oinfo['ctype'] = $data['ctype'];
        
        // 跳转支付结果
        $token = $this->strCode(json_encode($oinfo), 'ENCODE');

        $payResult = array(
            'status' => '1',
            'msg' => $payStatus['data']['msg'],
            'data' => $this->config->item('pages_url') . 'ios/wallet/payComplete/' . urlencode($token)
        );

        echo json_encode($payResult);
        die();
    }

    /*
     * IOS 支付结果页
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
                $orders['orderUrl'] = $this->config->item('protocol') . $this->config->item('pages_url') . 'ios/hemai/detail/hm' . $orders['orderId'] . '/' . urlencode($orders['token']);
                break;
            case 5:
                $orders['orderUrl'] = $this->config->item('pages_url') . 'ios/hemai/gdetail/gd' . $orders['orderId'] . '/' . urlencode($orders['token']);
                break; 
            case 1:
                $orders['orderUrl'] = $this->config->item('protocol') . $this->config->item('pages_url') . 'ios/chase/detail/' . $orders['orderId'] . '/' . urlencode($orders['token']);
                break;
            case 0:
                $orders['orderUrl'] = $this->config->item('protocol') . $this->config->item('pages_url') . 'ios/order/detail/' . $orders['orderId'] . '/' . urlencode($orders['token']);
                break;
        }
        // 支付成功页banner
        $orders['banner'] = $this->getPayBanner($orders);

        $orders['isChannel'] = FALSE;
        // 马甲版渠道区分
        $this->config->load('channel');
        $channelArr = $this->config->item('channel');
        if(in_array($orders['channel'], $channelArr))
        {
            $orders['isChannel'] = TRUE;
            $this->load->model('channel_model', 'channel');
            $channelInfo = $this->channel->getChannelInfo($orders['channel']);
            $orders['channelName'] = $channelInfo['name'] ? $channelInfo['name'] : '';
        }

        // V2.6版本需求客户端内部支付结果页
        if($orders['appVersionCode'] >= 2060001)
        {
            $this->load->view('order/pay_inner_result', $orders);
        }
        else
        {
            $this->load->view('order/pay_result', $orders);
        }
    }

    /*
     * IOS 充值页接口
     * @parms:token包含uid,可能包含orderId、orderType
     * @date:2015-05-07
     */
    public function recharge($token, $rechargeMoney = '')
    {   
        $data = $this->strCode(urldecode($token));
        $data = json_decode($data, true);

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

        // 获取渠道信息
        $this->versionInfo = $this->getUserAgentInfo();

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

        $payView = '';
        if(!empty($data['orderId']))
        {
            if ($data['orderType'] == 4)
            {
                if ($data['ctype'] == 1)
                {
                    $token = $this->strCode(json_encode(array(
                        'uid' => $data['uid'],
                        'orderId' => $data['orderId'],
                        'orderType' => $data['orderType']   
                            )), 'ENCODE');
                    $payView = $this->config->item('protocol') . $this->config->item('pages_url') . 'ios/hemai/detail/hm' . $data['orderId'] . '/' . urlencode($token);
                }
                else
                {
                    $payView = $this->config->item('protocol') . $this->config->item('pages_url') . "ios/order/doPay/" . $token;
                }
            }
            else
            {
                $orderDetail = $this->strCode(json_encode(array(
                    'uid' => $data['uid'],
                    'orderId' => $data['orderId'],
                    'orderType' => $data['orderType']
                        )), 'ENCODE');

            // 跳转支付页面
            $payView = $this->config->item('protocol') . $this->config->item('pages_url') . "ios/order/doPay/" . urlencode($orderDetail);
            }
        }

        // 充值配置
        $payConfig = $this->getPayConfig($this->versionInfo);
        if (isset($data['checked_idName'])) {
            foreach ($payConfig as $val) {
                if ($val['idName'] == $data['checked_idName']) $checked_idName = $data['checked_idName'];
            }
        }
        if (!isset($checked_idName)) $checked_idName = $payConfig[0]['idName'];
        $uInfo = array(
            'uname' => $info['uname'],
            'money' => number_format(ParseUnit($money['money'], 1), 2),
            'token' => $token,
            'payView' => $payView,
            'redirectPage' => !empty($data['orderId'])?'order':'recharge',
            'redpackData' => $redpackData,
            'rechargeMoney' => $rechargeMoney,
            'channel' => $this->recordChannel($this->versionInfo['channel']),
            'appVersion' => $this->versionInfo['appVersionName'],
            'versionInfo' => $this->versionInfo,
            'payConfig' => $payConfig,
            'url'       => $this->config->item('protocol') . $this->config->item('pages_url'). "ios/wallet/recharge/".$token.($rechargeMoney ? "/".$rechargeMoney : ''),
            'checked_idName'    => $checked_idName,
        );

        // $this->load->view('wallet/recharge', $uInfo);

        // V1.6及以上调用新版充值页
        $this->load->view('wallet/doRecharge', $uInfo);
    }

    /*
     * IOS 充值确认
     * @date:2016-03-21
     */
    public function checkRecharge()
    {
        $postData = array();
        $postData['money'] = $this->input->post('money', true);
        $postData['token'] = $this->input->post('token');
        $postData['redirectPage'] = $this->input->post('redirectPage', true);
        $postData['redpackId'] = $this->input->post('redpackId', true);
        $postData['channel'] = $this->input->post('channel', true);
        $postData['appVersion'] = $this->input->post('appVersion', true);

        if( empty($postData['money']) || empty($postData['token']) || empty($postData['redirectPage']) )
        {
            $result = array(
                'status' => '0',
                'msg' => '必要参数缺失',
                'data' => $postData
            );
            die(json_encode($result));
        }

        // 1.0版本关闭支付充值
        $versionInfo = $this->version;
        if($versionInfo['appVersionCode'] < '3')
        {
            $result = array(
                'status' => '0',
                'msg' => '最后调试中，将于近期开放，敬请期待！',
                'data' => ''
            );
            die(json_encode($result)); 
        }
        // 验证提交信息
        $data = $this->strCode(urldecode($postData['token']));
        $data = json_decode($data, true);

        if(empty($data['uid']))
        {
            $result = array(
                'status' => '0',
                'msg' => '参数校验失败',
                'data' => ''
            );
            die(json_encode($result));
        }

        // 检查用户实名信息
        $uinfo = $this->User->getUserInfo($data['uid']);
        
        if(isset($uinfo['userStatus']) && $uinfo['userStatus'] == '1')
        {
            $result = array(
                'status' => '0',
                'msg' => '此账户已注销',
                'data' => ''
            );
            die(json_encode($result));
        }
        
        if(isset($uinfo['userStatus']) && $uinfo['userStatus'] == '2')
        {
            $result = array(
                'status' => '0',
                'msg' => '您的账户已被冻结，如需解冻请联系客服。',
                'data' => ''
            );
            die(json_encode($result));
        }

        if(empty($uinfo['real_name']) || empty($uinfo['id_card']))
        {
            $result = array(
                'status' => '3',
                'msg' => '请先完善个人信息',
                'data' => $postData
            );
            die(json_encode($result));
        }

        $postData['uid'] = $data['uid'];
        $postData['orderId'] = $data['orderId']?$data['orderId']:'0';
        $postData['orderType'] = $data['orderType']?$data['orderType']:'0';

        // 金额校验
        if( !is_numeric($postData['money']) || $postData['money'] <= 0 )
        {
            $result = array(
                'status' => '0',
                'msg' => '充值金额错误',
                'data' => $postData
            );
            die(json_encode($result));
        }

        // 按充值渠道判断金额限制
        if($postData['redirectPage'] == 'order')
        {
            if(!preg_match('/^\+?([0-9]+|[0-9]+\.[0-9]{0,2})$/', $postData['money']))
            {
                $result = array(
                    'status' => '0',
                    'msg' => '充值金额格式错误',
                    'data' => $postData
                );
                die(json_encode($result));
            }
            if($postData['money'] < 200 && $postData['pay_type'] == '4_0'){
                $result = array(
                    'status' => '0',
                    'msg' => '支付宝充值需200元起',
                    'data' => array()
                );
                die(json_encode($result));
            }
        }
        else
        {
            if(preg_match('/^\d+$/', $postData['money']))
            {
                if($postData['money'] < 10)
                {
                    $result = array(
                        'status' => '0',
                        'msg' => '请至少充值10元',
                        'data' => $postData
                    );
                    die(json_encode($result));
                }
                if ($postData['money'] < 200 && $postData['pay_type'] == '4_0') {
                    $result = array(
                        'status' => '0',
                        'msg' => '支付宝充值需200元起',
                        'data' => array()
                    );
                    die(json_encode($result));
                }
            }
            else
            {
                $result = array(
                    'status' => '0',
                    'msg' => '请输入整数金额',
                    'data' => $postData
                );
                die(json_encode($result));
            }
        }

        // 红包使用条件检查
        $addMoney = 0;
        $redpackMsg = array();
        if(!empty($postData['redpackId']))
        {
            $redpacks = array();
            $redpackArry = explode(',', $postData['redpackId']);
            // 查询红包信息
            $this->load->model('redpack_model');
            $this->eventType = $this->redpack_model->getEventType();
            $redpackData = $this->redpack_model->getRedpackInfo($data['uid'], $this->eventType['recharge']);
            
            if(!empty($redpackData))
            {
                $redpackInfo = array();
                foreach ($redpackData as $redpack) 
                {
                    $redpackInfo[$redpack['id']] = $redpack;
                }

                // 遍历用户所选红包 
                $checkMoney = 0;
                foreach ($redpackArry as $packItems) 
                {
                    $redPackStr = explode('#', $packItems);
                    if(!empty($redpackInfo[$redPackStr[0]]))
                    {
                        // 红包ID
                        array_push($redpacks, $redPackStr[0]);
                        // 红包信息
                        array_push($redpackMsg, $redpackInfo[$redPackStr[0]]['use_desc']);
                        $redpackParams = json_decode($redpackInfo[$redPackStr[0]]['use_params'], true);
                        
                        $checkMoney += ParseUnit($redpackParams['money_bar'], 1);
                        $addMoney += ParseUnit($redpackInfo[$redPackStr[0]]['money'], 1);
                    }
                    else
                    {
                        $result = array(
                            'status' => '2',
                            'msg' => '当前红包不可用',
                            'data' => ''
                        );
                        die(json_encode($result));
                    }
                }   

                // 总金额检查
                if($checkMoney > $postData['money'])
                {
                    $result = array(
                        'status' => '2',
                        'msg' => '当前红包不可用',
                        'data' => ''
                    );
                    die(json_encode($result));
                }           
            }
            else
            {
                $result = array(
                    'status' => '2',
                    'msg' => '当前红包不可用',
                    'data' => ''
                );
                die(json_encode($result));
            }

            // 判断是否存在重复红包
            if(count($redpacks) != count(array_unique($redpacks)))
            {
                $result = array(
                    'status' => '2',
                    'msg' => '当前红包不可用',
                    'data' => ''
                );
                die(json_encode($result));
            }

            $postData['redpackId'] = implode(',', $redpacks);       
        }

        // 实际到账金额
        $postData['addMoney'] = $postData['money'] + $addMoney;
        // 红包信息
        $postData['redpackMsg'] = implode(',', $redpackMsg);

        // 组装跳转safari地址
        $result = array(
            'status' => '1',
            'msg' => 'success',
            'data' => $this->getRechargeUrl($postData)
        );
        die(json_encode($result));
    }

    /*
     * IOS 跳转safari加密处理
     * @date:2016-03-22
     */
    public function getRechargeUrl($postData)
    {
        // 组装必要参数
        $rechargeData = array(
            'userId' => $postData['uid'],
            'money' => $postData['money'],
            'addMoney' => $postData['addMoney'],
            'redpackId' => $postData['redpackId']?$postData['redpackId']:'',
            'orderId' => $postData['orderId'],
            'orderType' => $postData['orderType'],
            'redirectPage' => $postData['redirectPage'],
            'channel' => $postData['channel'],
            'appVersion' => $postData['appVersion']
        );

        $rechargeToken = $this->strCode(json_encode($rechargeData), 'ENCODE');

        $sign = $this->encryptData($rechargeData);

        $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https:" : "http:";
        $url = $protocol . $this->config->item('pages_url') . "ios/wallet/doRecharge/" . urlencode($rechargeToken) . '/' . urlencode($sign) . '/' . urlencode($postData['redpackMsg']);

        return $url;
    }

    /*
     * IOS safari外部充值页
     * @date:2016-03-22
     */
    public function doRecharge($rechargeToken, $sign = '', $redpackInfo = '')
    {
        // 验证提交信息
        $data = $this->strCode(urldecode($rechargeToken));
        $data = json_decode($data, true);

        if(empty($data) || empty($sign))
        {
            var_dump("请求参数错误");die;
        }

        if( $this->encryptData($data) !== urldecode($sign) )
        {
            var_dump("校验参数错误");die;
        }

        $rechargeInfo = array(
            'rechargeData' => $data,
            'rechargeToken' => $rechargeToken,
            'sign' => $sign,
            'redpackInfo' => urldecode($redpackInfo)
        );

        $this->load->view('wallet/recharge2', $rechargeInfo);
    }

    /*
     * IOS 充值提交信息
     * @date:2015-06-15
     */
    public function jumpRecharge()
    {
        $postData = array();
        $postData['payType'] = $this->input->post('payType', true);
        $postData['token'] = $this->input->post('token');
        $postData['sign'] = $this->input->post('sign', true);

        if( empty($postData['payType']) || empty($postData['token']) || empty($postData['sign']) )
        {
            $result = array(
                'status' => '0',
                'msg' => '必要参数缺失',
                'data' => $postData
            );
            die(json_encode($result));
        }

        // 验证提交信息
        $rechargeData = $this->strCode(urldecode($postData['token']));
        $rechargeData = json_decode($rechargeData, true);

        if(empty($rechargeData['userId']))
        {
            $result = array(
                'status' => '0',
                'msg' => '参数校验异常',
                'data' => ''
            );
            die(json_encode($result));
        }

        if( $this->encryptData($rechargeData) !== urldecode($postData['sign']) )
        {
            $result = array(
                'status' => '0',
                'msg' => '充值信息校验异常',
                'data' => ''
            );
            die(json_encode($result));
        }

        $rechargeData['payType'] = $postData['payType'];
        // 平台、渠道及版本信息
        $rechargeData['platform'] = $this->config->item('platform');
        $rechargeData['channel'] = $this->recordChannel($rechargeData['channel']);
        $rechargeData['app_version'] = (!empty($rechargeData['appVersion']))?$rechargeData['appVersion']:'1.0';

        // 充值渠道判断
        switch ($postData['payType']) 
        {
            case 'shengpaywap':
                $returnData = $this->doShengpaywapRecharge($rechargeData);  
                break;
            case 'yeepayMPay':
                $returnData = $this->doYeepayMPayRecharge($rechargeData);  
                break;
            case 'sumpayWap':
                $returnData = $this->doSumpayWapRecharge($rechargeData);  
                break;
            default:
                $returnData = array(
                    'status' => '0',
                    'msg' => '暂不支持此充值方式',
                    'data' => ''
                );
                break;
        }

        // 处理结果
        if( $returnData['status'] == '1' )
        {
            $result = array(
                'status' => '1',
                'msg' => '创建订单成功',
                'data' => $returnData['data'],
                'token' => ''
            );
        }
        else
        {
            $result = array(
                'status' => '0',
                'msg' => $returnData['msg'],
                'data' => '',
                'token' => ''
            );
        }

        die(json_encode($result));
    }

    /*
     * IOS 盛付通wap
     * @date:2015-06-15
     */
    public function doShengpaywapRecharge($redata)
    {
        // 支付中心跳转参数组装
        $parmas = array(
            'mid' => 'CP',
            'OrderNo' => $this->tools->getIncNum('UNIQUE_KEY'),
            'OrderAmount' => $redata['money'],
            'OrderTime' => date('YmdHis', time()),
            'BuyerIp' => UCIP,
            'ProductName' => '2345CP',
            'PayType' => $redata['payType'],
            'token' => '',
            'salt' => ''
        );

        //获取 salt
        $parmas['salt'] = $this->Wallet->GetSalt();
        $parmas['token'] = md5("{$parmas[OrderNo]}{$parmas[mid]}{$parmas[OrderAmount]}{$parmas[OrderTime]}{$parmas[BuyerIp]}{$parmas[ProductName]}{$parmas[salt]}");

        // 创建充值流水
        $walletData = array(
            'uid' => $redata['userId'],
            'trade_no' => $parmas['OrderNo'],
            'orderId' => $redata['orderId'],
            'status' => $redata['orderType'],
            'additions' => $this->getAddition($parmas['PayType']),
            'money' => ParseUnit($parmas['OrderAmount']),
            'mark' => '2',
            'red_pack' => $redata['redpackId'],
            'platform' => $redata['platform'],
            'app_version' => $redata['app_version'],
            'channel' => $redata['channel']
        );

        // 入库 cp_wallet_log
        $resLog = $this->Wallet->recordWalletLog($walletData);

        if($resLog)
        {
            $result = array(
                'status' => '1',
                'msg' => '创建订单成功',
                'data' => $this->strCode(json_encode($parmas), 'ENCODE'),
                'token' => ''
            );
        }
        else
        {
            $result = array(
                'status' => '0',
                'msg' => '流水记录错误,创建订单失败',
                'data' => '',
                'token' => ''
            );
        }
        return $result;
    }

    /*
     * IOS 易宝支付wap
     * @date:2015-06-15
     */
    public function doYeepayMPayRecharge($redata)
    {
        // 支付中心跳转参数组装
        $parmas = array(
            'uid' => $redata['userId'],
            'OrderNo' => $this->tools->getIncNum('UNIQUE_KEY'),
            'OrderAmount' => ParseUnit($redata['money']),   // 修改为 1 分调试,
            'OrderTime' => date('Y-m-d H:i:s', time()),
            'BuyerIp' => UCIP,
            'ProductName' => '166彩票充值',
            'ProductDesc' => '彩金充值',
            'PayType' => $redata['payType'],
            'token' => '',
            'salt' => ''
        );

        //获取 salt
        $parmas['salt'] = $this->Wallet->GetSalt();
        $parmas['token'] = md5("{$parmas[OrderNo]}{$parmas[uid]}{$parmas[OrderAmount]}{$parmas[OrderTime]}{$parmas[BuyerIp]}{$parmas[ProductName]}{$parmas[ProductDesc]}{$parmas[salt]}");

        // 创建充值流水
        $walletData = array(
            'uid' => $redata['userId'],
            'trade_no' => $parmas['OrderNo'],
            'orderId' => $redata['orderId'],
            'status' => $redata['orderType'],
            'additions' => "{$parmas[PayType]}",
            'money' => $parmas['OrderAmount'],
            'mark' => '2',
            'red_pack' => $redata['redpackId'],
            'platform' => $redata['platform'],
            'app_version' => $redata['app_version'],
            'channel' => $redata['channel']
        );

        // 入库 cp_wallet_log
        $resLog = $this->Wallet->recordWalletLog($walletData);

        if($resLog)
        {
            $result = array(
                'status' => '1',
                'msg' => '创建订单成功',
                'data' => $this->strCode(json_encode($parmas), 'ENCODE'),
                'token' => ''
            );
        }
        else
        {
            $result = array(
                'status' => '0',
                'msg' => '流水记录错误,创建订单失败',
                'data' => '',
                'token' => ''
            );
        }
        return $result;
    }

    /*
     * IOS 统统付付wap
     * @date:2016-07-01
     */
    public function doSumpayWapRecharge($redata)
    {
        // cp_wallet_log
        $walletData = array(
            'uid'           => $redata['userId'],
            'trade_no'      => $this->tools->getIncNum('UNIQUE_KEY'),
            'orderId'       => $redata['orderId'],
            'status'        => $redata['orderType'],
            'additions'     => $redata['payType'],
            'money'         => ParseUnit($redata['money']),     // 按分处理
            'mark'          => '2',
            'red_pack'      => $redata['redpackId'],
            'platform'      => $redata['platform'],
            'app_version'   => $redata['app_version'],
            'channel'       => $redata['channel']
        );

        // 入库 cp_wallet_log
        $res1 = $this->Wallet->recordWalletLog($walletData);

        // cp_pay_log
        $payData = array(
            'trade_no'  => $walletData['trade_no'],
            'money'     => $walletData['money'],
            'pay_time'  => date('Y-m-d H:i:s'),
            'pay_type'  => '6',     // 统统付wap
        );

        // 入库 cp_pay_log
        $res2 = $this->Wallet->recordPayLog($payData);

        if($res1 && $res2)
        {
            // 组装参数
            $parmas = array(
                'uid'           => $redata['userId'],
                'OrderNo'       => $walletData['trade_no'],
                'OrderAmount'   => $redata['money'],        // 按元处理
                'PayType'       => $redata['payType'],
                'OrderTime'     => $payData['pay_time'],
            );

            $result = array(
                'status' => '1',
                'msg' => '订单创建成功',
                'data' =>  $this->strCode(json_encode($parmas), 'ENCODE'),
            );
        }
        else
        {
            $result = array(
                'status' => '0',
                'msg' => '流水记录错误,创建订单失败',
                'data' => ''
            );
        }
        
        return $result;
    }

    /*
     * IOS 充值成功中转页
     * @date:2015-06-15
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

        $payLog = $this->Wallet->getPayLog($data['tradeNo'], 1);

        if(empty($payLog))
        {
            die('订单参数缺失');
        }

        if($payLog['status'] == 1)
        {
            // 跳转至成功页
            $rechargeView = $this->config->item('pages_url') . "ios/wallet/rechargeDetail/" . $token;
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
     * IOS 充值成功详情页
     * @date:2015-06-15
     */
    public function rechargeDetail($token)
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
                if($order['status'] == 3)
                {
                    $this->load->model('united_order_model');
                    $unitedOrder = $this->united_order_model->getOrder(array('o.orderId' => $data['cp_orderId']), 'o.status');
                    // 跳转支付页面
                    $detail['redirectPage'] = $data['redirectPage'];
                    if ($unitedOrder['status'] >= 40)
                    {
                        $orderDetail = $this->strCode(json_encode(array(
                                'uid' => $data['uid'],
                                'orderId' => $data['cp_orderId'],
                                'orderType' => 4,
                                'ctype' => 1,
                        )), 'ENCODE');
                        $detail['payView'] = $this->config->item('protocol') . $this->config->item('pages_url') . 'ios/hemai/detail/hm' . $data['cp_orderId'] . '/' . urlencode($orderDetail);
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
                        $detail['payView'] = $this->config->item('protocol') . $this->config->item('pages_url') . "ios/order/doPay/" . urlencode($orderDetail);
                    }
                }else {
                    // 订单信息加密
                    $orderDetail = $this->strCode(json_encode(array(
                            'uid' => $data['uid'],
                            'orderId' => $data['cp_orderId'],
                            'orderType' => $order['status']   // 根据status判断 0：自购 1：追号
                    )), 'ENCODE');
                    
                    $detail['payView'] = $this->config->item('protocol') . $this->config->item('pages_url') . "ios/order/doPay/" . urlencode($orderDetail);
                }
                // 跳转支付页面
                $detail['redirectPage'] = $data['redirectPage'];
            }
        }

        $detail['isChannel'] = FALSE;
        // 马甲版渠道区分
        $this->config->load('channel');
        $channelArr = $this->config->item('channel');
        if(in_array($order['channel'], $channelArr))
        {
            $detail['isChannel'] = TRUE;
            $this->load->model('channel_model', 'channel');
            $channelInfo = $this->channel->getChannelInfo($order['channel']);
            $detail['channelName'] = $channelInfo['name'] ? $channelInfo['name'] : '';
        }

        $detail['status'] = TRUE;
        $this->load->view('wallet/result', $detail); 
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
                    'data' => $this->config->item('pages_url') . "ios/wallet/rechargeDetail/" . $token
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

    /*
     * 检查用户实名信息
     * @date:2016-10-12
     */
    public function checkUserStatus($uinfo)
    {
        if(isset($uinfo['userStatus']) && $uinfo['userStatus'] == '1')
        {
            $result = array(
                'status' => FALSE,
                'msg' => '此账户已注销',
                'data' => ''
            );
            return $result;
        }
        
        if(isset($uinfo['userStatus']) && $uinfo['userStatus'] == '2')
        {
            $result = array(
                'status' => FALSE,
                'msg' => '您的账户已被冻结，如需解冻请联系客服。',
                'data' => ''
            );
            return $result;
        }

        if(empty($uinfo['real_name']) || empty($uinfo['id_card']))
        {
            $result = array(
                'status' => FALSE,
                'msg' => '请先完善个人信息',
                'data' => $postData
            );
            return $result;
        }

        $result = array(
            'status' => TRUE,
            'msg' => '检查通过',
            'data' => ''
        );
        return $result;
    }

    /*
     * 充值金额检查
     * @date:2016-10-12
     */
    public function checkRechargeMoney($postData)
    {
        if( !is_numeric($postData['money']) || $postData['money'] <= 0 )
        {
            $result = array(
                'status' => FALSE,
                'msg' => '充值金额错误',
                'data' => $postData
            );
            return $result;
        }

        // 按充值渠道判断金额限制
        if($postData['redirectPage'] == 'order')
        {
            if(!preg_match('/^\+?([0-9]+|[0-9]+\.[0-9]{0,2})$/', $postData['money']))
            {
                $result = array(
                    'status' => FALSE,
                    'msg' => '充值金额格式错误',
                    'data' => $postData
                );
                return $result;
            }
            if($postData['money'] < 200 && $postData['pay_type'] == '4_0'){
                $result = array(
                    'status' => '0',
                    'msg' => '支付宝充值需200元起',
                    'data' => array()
                );
                die(json_encode($result));
            }
        }
        else
        {
            if(preg_match('/^\d+$/', $postData['money']))
            {
                if($postData['money'] < 10)
                {
                    $result = array(
                        'status' => FALSE,
                        'msg' => '请至少充值10元',
                        'data' => $postData
                    );
                    return $result;
                }
                if ($postData['money'] < 200 && $postData['pay_type'] == '4_0') {
                    $result = array(
                        'status' => '0',
                        'msg' => '支付宝充值需200元起',
                        'data' => array()
                    );
                    die(json_encode($result));
                }
            }
            else
            {
                $result = array(
                    'status' => FALSE,
                    'msg' => '请输入整数金额',
                    'data' => $postData
                );
                return $result;
            }
        }

        $result = array(
            'status' => TRUE,
            'msg' => '检查通过',
            'data' => ''
        );
        return $result;
    }

    /*
     * 红包检查
     * @date:2016-10-12
     */
    public function checkRedpack($postData)
    {
        $addMoney = 0;
        $redpackMsg = array();
        $redpackId = '';
        if(!empty($postData['redpackId']))
        {
            $redpacks = array();
            $redpackArry = explode(',', $postData['redpackId']);
            // 查询红包信息
            $this->load->model('redpack_model');
            $this->eventType = $this->redpack_model->getEventType();
            $redpackData = $this->redpack_model->getRedpackInfo($postData['uid'], $this->eventType['recharge']);
            
            if(!empty($redpackData))
            {
                $redpackInfo = array();
                foreach ($redpackData as $redpack) 
                {
                    $redpackInfo[$redpack['id']] = $redpack;
                }

                // 遍历用户所选红包 
                $checkMoney = 0;
                foreach ($redpackArry as $packItems) 
                {
                    $redPackStr = explode('#', $packItems);
                    if(!empty($redpackInfo[$redPackStr[0]]))
                    {
                        // 红包ID
                        array_push($redpacks, $redPackStr[0]);
                        // 红包信息
                        array_push($redpackMsg, $redpackInfo[$redPackStr[0]]['use_desc']);
                        $redpackParams = json_decode($redpackInfo[$redPackStr[0]]['use_params'], true);
                        
                        $checkMoney += ParseUnit($redpackParams['money_bar'], 1);
                        $addMoney += ParseUnit($redpackInfo[$redPackStr[0]]['money'], 1);
                    }
                    else
                    {
                        $result = array(
                            'status' => FALSE,
                            'msg' => '当前红包不可用',
                            'data' => ''
                        );
                        return $result;
                    }
                }   

                // 总金额检查
                if($checkMoney > $postData['money'])
                {
                    $result = array(
                        'status' => FALSE,
                        'msg' => '当前红包不可用',
                        'data' => ''
                    );
                    return $result;
                }           
            }
            else
            {
                $result = array(
                    'status' => FALSE,
                    'msg' => '当前红包不可用',
                    'data' => ''
                );
                return $result;
            }

            // 判断是否存在重复红包
            if(count($redpacks) != count(array_unique($redpacks)))
            {
                $result = array(
                    'status' => FALSE,
                    'msg' => '当前红包不可用',
                    'data' => ''
                );
                return $result;
            }

            $redpackId = implode(',', $redpacks);       
        }

        $result = array(
            'status' => TRUE,
            'msg' => '检查通过',
            'data' => $redpackId
        );
        return $result;
    }

    /*
     * 新版充值流程
     * @date:2016-10-12
     */
    public function requestRecharge()
    {
        $postData = array();
        $postData['money'] = $this->input->post('money', true);
        $postData['token'] = $this->input->post('token');
        $postData['redirectPage'] = $this->input->post('redirectPage', true);
        $postData['redpackId'] = $this->input->post('redpackId', true);
        $postData['channel'] = $this->input->post('channel', true);
        $postData['appVersion'] = $this->input->post('appVersion', true);
        $postData['pay_type'] = $this->input->post('pay_type', true);
        $postData['refer'] = $this->input->post('refer', true);
        $postData['change_bankid'] = $this->input->post('change_bankid', true);

        if( empty($postData['money']) || empty($postData['token']) || empty($postData['redirectPage']) || empty($postData['pay_type']))
        {
            $result = array(
                'status' => '400',
                'msg' => '必要参数缺失',
                'data' => $postData
            );
            die(json_encode($result));
        }
        // 1.0版本关闭支付充值
        $versionInfo = $this->version;
        if($versionInfo['appVersionCode'] < '3')
        {
            $result = array(
                'status' => '400',
                'msg' => '最后调试中，将于近期开放，敬请期待！',
                'data' => ''
            );
            die(json_encode($result));
        }

        //验证提交信息
        $data = $this->strCode(urldecode($postData['token']));
        $data = json_decode($data, true);
        if(empty($data['uid']))
        {
            $result = array(
                'status' => '400',
                'msg' => '参数校验失败',
                'data' => ''
            );
            die(json_encode($result));
        }

        $postData['uid'] = $data['uid'];
        $postData['orderId'] = $data['orderId']?$data['orderId']:'0';
        $postData['orderType'] = $data['orderType']?$data['orderType']:'0';

        // 用户信息检查
        $uinfo = $this->User->getUserInfo($data['uid']);

        $checkUserRes = $this->checkUserStatus($uinfo);

        if(!$checkUserRes['status'])
        {
            $result = array(
                'status' => '400',
                'msg' => $checkUserRes['msg'],
                'data' => ''
            );
            die(json_encode($result));
        }

        // 充值金额检查
        $checkMoneyRes = $this->checkRechargeMoney($postData);

        if(!$checkMoneyRes['status'])
        {
            $result = array(
                'status' => '400',
                'msg' => $checkMoneyRes['msg'],
                'data' => ''
            );
            die(json_encode($result));
        }

        // 红包检查
        $checkRedpackRes = $this->checkRedpack($postData);

        if(!$checkRedpackRes['status'])
        {
            $result = array(
                'status' => '300',
                'msg' => $checkRedpackRes['msg'],
                'data' => ''
            );
            die(json_encode($result));
        }

        // 红包信息
        $postData['redpackId'] = $checkRedpackRes['data'];

        // 调试参数
        // if(ENVIRONMENT != 'production')
        // {
//             $postData['money'] = '0.01';
        // }     

        // 分配具体支付参数
        // 马甲版本判断
        $this->config->load('channel');
        $channelArr = $this->config->item('channel');
        if($postData['pay_type'] == '2_0' && in_array($this->recordChannel($versionInfo['channel']), $channelArr))
        {
            // 中信SDK
            $rechargeInfo = array(
                'pay_type'  =>  'zxwxSdk',
                'merId'     =>  '886600000002569',
                'configId'  =>  71,
                'rcg_group' =>  '3_2',
            );
        }
        else
        {
            if($this->checkChannelPackage($versionInfo['channel']))
            {
                $platform = $this->config->item('platform') + 4;
            }
            else
            {
                $platform = $this->config->item('platform') + 1;
            }
            $rechargeInfo = $this->exchangeRecharge($platform, $postData['pay_type']);
        }
        if ($rechargeInfo['pay_type'] == 'pfWxWap' && ($postData['money'] < 1 || $postData['money'] >3000)) {
            $rechargeInfo['pay_type'] = 'wftWxWap';
            $rechargeInfo['merId'] = '101530269330';
            $rechargeInfo['configId'] = '138';
        }
        if ($rechargeInfo['pay_type'] == 'tomatoWxWap' && $postData['money'] >3000) {
            $rechargeInfo['pay_type'] = 'wftWxSdk';
            $rechargeInfo['merId'] = '102510034424';
            if (ENVIRONMENT != 'production') {
                $rechargeInfo['configId'] = '46';
            } else {
                $rechargeInfo['configId'] = '46';
            }
        }
        if(empty($rechargeInfo['pay_type']))
        {
            $result = array(
                'status' => '400',
                'msg' => '支付系统维护中',
                'data' => ''
            );
            die(json_encode($result));
        }

        // 组装支付必要参数
        $params = array(
            'trade_no'      =>  $this->tools->getIncNum('UNIQUE_KEY'),
            'uid'           =>  $postData['uid'],
            'real_name'     =>  $uinfo['real_name'],
            'id_card'       =>  $uinfo['id_card'],
            'money'         =>  $postData['money'],
            'pay_type'      =>  $rechargeInfo['pay_type'],
            'merId'         =>  $rechargeInfo['merId'],
            'configId'      =>  $rechargeInfo['configId'],
            'redpackId'     =>  $postData['redpackId'],
            'orderId'       =>  $postData['orderId'],
            'orderType'     =>  $postData['orderType'],
            'ip'            =>  UCIP,
            'pay_time'      =>  date('Y-m-d H:i:s'),
            'channel'       =>  $this->recordChannel($versionInfo['channel']),
            'appVersion'    =>  $versionInfo['appVersionName'],
            'refer'         =>  $postData['refer'],
            'change_bankid' =>  $postData['change_bankid']
        );
        // 请求支付参数
        $rechParams = $this->rechParams($params);
        if(empty($rechParams))
        {
            $result = array(
                'status' => '400',
                'msg' => '支付系统维护中',
                'data' => ''
            );
            die(json_encode($result));
        }

        if($rechParams['status'] == '400')
        {
            $result = array(
                'status' => '400',
                'msg' => $rechParams['msg'],
                'data' => ''
            );
            die(json_encode($result));
        }
        
        if ($params['orderType'] == 4) {
            $status = 3;
        } elseif ($params['orderType'] == 5) {
            $status = 5;
        } else {
            $status = $params['orderType'] ? 1 : 0;
        }


        // 记录 cp_wallet_logs
        $walletData = array(
            'uid'       =>  $params['uid'], 
            'trade_no'  =>  $params['trade_no'], 
            'orderId'   =>  $params['orderId'], 
            'additions' =>  $params['pay_type'], 
            'mark'      =>  '2', 
            'money'     =>  ParseUnit($params['money']),    // 转化为分
            'status'    =>  $status, 
            'red_pack'  =>  $params['redpackId'] ? $params['redpackId'] : '', 
            'platform'  =>  $this->config->item('platform'),
            'channel'   =>  $params['channel']
        );


        $res1 = $this->Wallet->recordWalletLog($walletData);

        // 记录 cp_pay_log
        $payData = array(
            'trade_no'  =>  $params['trade_no'],
            'money'     =>  ParseUnit($params['money']),    // 转化为分
            'pay_time'  =>  $params['pay_time'],
            'pay_type'  =>  $rechParams['payType'],         // 支付类型
            'rcg_group' =>  $rechargeInfo['rcg_group'],     // 充值方式组号如3_1
            'rcg_serial'=>  $rechargeInfo['configId'],
        );

        $res2 = $this->Wallet->recordPayLog($payData);

        if($res1 && $res2)
        {
            $result = array(
                'status' => '200',
                'msg' => '创建订单成功',
                'data' => $rechParams
            );
        }
        else
        {
            $result = array(
                'status' => '400',
                'msg' => '流水记录错误,创建订单失败',
                'data' => ''
            );
        }
        die(json_encode($result));
    }

    /*
     * 根据支付方式选择调用SDK或Safari外链
     * @date:2016-10-12
     */
    public function rechParams($params)
    {
        $rechParams = array();
        switch ($params['pay_type']) 
        {
            case 'yeepayMPay':
                $rechParams = array(
                    'status' =>  '200',
                    'msg' => '创建成功',
                    'type' => 'safari',
                    'payType' => '1',
                    'data' => $this->payToSafari($params)
                );
                break;
            case 'sumpayWap':
                $rechParams = array(
                    'status' =>  '200',
                    'msg' => '创建成功',
                    'type' => 'safari',
                    'payType' => '6',
                    'data' => $this->payToSafari($params)
                );
                break;
            case 'zxwxSdk':
                $rechParams = $this->zxwxSdkRecharge($params);
                break;
            case 'wftWxSdk':
                $rechParams = $this->wftWxSdkRecharge($params);
                break;
            case 'wftWx':
                $rechParams = $this->wftWxRecharge($params);
                break;
            case 'xzZfbWap':
                $rechParams = array(
                    'status' =>  '200',
                    'msg' => '创建成功',
                    'type' => 'safari',
                    'payType' => '12',
                    'data' => $this->payToSafari($params)
                );
                break;
            case 'jdPay':
                $rechParams = array(
                    'status' =>  '200',
                    'msg' => '创建成功',
                    'type' => 'safari',
                    'payType' => '13',
                    'data' => $this->payToSafari($params)
                );
                break;
           case 'umPay':
                $rechParams = array(
                    'status' =>  '200',
                    'msg' => '创建成功',
                    'type' => 'rediret',
                    'payType' => '14',
                    'data' => $this->payToSafari($params)
                );
                break;
            case 'hjZfbPay':
                $rechParams = array(
                    'status' =>  '200',
                    'msg' => '创建成功',
                    'type' => 'safari',
                    'payType' => '15',
                    'data' => $this->payToSafari($params)
                );
                break;
            case 'hjWxWap':
                $rechParams = array(
                    'status' =>  '200',
                    'msg' => '创建成功',
                    'type' => 'safari',
                    'payType' => '18',
                    'data' => $this->payToSafari($params)
                );
                break;
            case 'hjZfbWap':
                $rechParams = array(
                    'status' =>  '200',
                    'msg' => '创建成功',
                    'type' => 'safari',
                    'payType' => '19',
                    'data' => $this->payToSafari($params)
                );
                break;
            case 'hjZfbSh':
                $rechParams = array(
                    'status' =>  '200',
                    'msg' => '创建成功',
                    'type' => 'safari',
                    'payType' => '34',
                    'data' => $this->payToSafari($params)
                );
                break;
            case 'wftZfbWap':
                $rechParams = $this->wftZfbWapRecharge($params);
                break;
            case 'wftWxWap':
                $rechParams = $this->wftWxWapRecharge($params);
                break;
            case 'tomatoZfbWap': 
                $rechParams = $this->doTomatoZfbWapRecharge($params);
                break;
            case 'tomatoWxWap': 
                $rechParams = $this->doTomatoWxWapRecharge($params);
                break;
            case 'ulineWxWap': 
                $rechParams = $this->doUlineWxRecharge($params);
                break;
            case 'pfWxWap': 
                $rechParams = $this->doPfWxWapRecharge($params);
                break;
            case 'yzpayh':
                $rechParams =  $this->doYzpayh($params);
                break;
            case 'yzWxWap':
                $rechParams = $this->doYzpayh($params);
                break;
            default:
                $rechParams = array();
                break;
        }
        return $rechParams;
    }

    public function doYzpayh($params)
    {
        // 构建参数
        $postData = array(
            'uid'           =>  $params['uid'],
            'trade_no'      =>  $params['trade_no'],
            'money'         =>  ParseUnit($params['money']),        // 分
            'ip'            =>  UCIP,
            'pay_type'      =>  $params['pay_type'],
            'real_name'     =>  $params['real_name'],
            'id_card'       =>  $params['id_card'],
            'merId'         =>  $params['merId'],
            'configId'      =>  $params['configId'],
        );

        // 调用接口获取参数
        $postData['lib'] =  'YzPay';
        $responeData = apiRequest('api/RechargeHandle','request',$postData);
        if(!$responeData['code'])
        {
            $result = array(
                'status' => '0',
                'msg' => '支付系统维护中',
                'data' => ''
            );
            return $result;
        }

        $rechParams = array(
            'status' =>  '200',
            'msg' => '预支付订单创建成功',
            'type' => 'safari',
            'payType' => '8',
            'data' => $responeData['data']['code_url']
        );
        return $rechParams;
    }

    /*
     * Safari外链生成
     * @date:2016-10-12
     */
    public function payToSafari($params)
    {
        $rechargeData = array(
            'trade_no'      =>  $params['trade_no'],
            'uid'           =>  $params['uid'],
            'money'         =>  $params['money'],
            'real_name'     =>  $params['real_name'],
            'id_card'       =>  $params['id_card'],
            'pay_type'      =>  $params['pay_type'],
            'ip'            =>  $params['ip'],
            'pay_time'      =>  $params['pay_time'],
            'merId'         =>  $params['merId'] ? $params['merId'] : '0',
            'configId'      =>  $params['configId'] ? $params['configId'] : '0',
            'refer'         =>  $params['refer'],
            'change_bankid' =>  $params['change_bankid'],
        );
        
        $token = $this->strCode(json_encode($rechargeData), 'ENCODE');
        
        unset($rechargeData['change_bankid']);

        $sign = $this->authData($rechargeData);

        $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
        $url = "$protocol$_SERVER[HTTP_HOST]";
        $url .= "/ios/wallet/safariPay?token=";
        $url .= urlencode($token);
        $url .= "&sign=";
        $url .= $sign;

        return $url;
    }

    /*
     * Safari外链生成
     * @date:2016-10-12
     */
    public function safariPay()
    {
        $token = $this->input->get('token');
        $sign = $this->input->get('sign');
        $rechargeData = $this->strCode($token);
        $rechargeData = json_decode($rechargeData, true);
        $params = $rechargeData;
        if ($params['bank_id']) unset($params['bank_id']);
        if (isset($params['change_bankid'])) unset($params['change_bankid']);
        if (isset($params['pay_agreement_id'])) unset($params['pay_agreement_id']);
        // 验签
        if(!empty($rechargeData) && $this->authData($params) == $sign)
        {
            // 请求支付参数
            $rechParams = $this->rechargeParams($rechargeData, $sign);
            // if($rechParams['data']['view']=='hjZfbWap')
            // {
            //     $redirectUrl = '/ios/wallet/hjZfbWap/'.$rechargeData['trade_no'].'?backurl='.$rechParams['data']['params']['jump_url'].'&scheme='.urlencode('alipays://platformapi/startapp?').'saId'.urlencode('=10000007&').'qrcode='.urlencode($rechParams['data']['params']['code_url'].'?_s=web-other');
            //     header('Location:'.$redirectUrl);
            //     exit();
            // }
            if(!$rechParams['status'])
            {
                $this->load->view('wallet/error');
            }
            else
            {
                if($rechParams['type'] == 'page')
                {
                    $this->load->view('wallet/pay/' . $rechParams['data']['view'], array('payData' => $rechParams['data']['params']));
                }
                else
                {
                    header('Location: ' . $rechParams['data']);
                    die;
                }
            } 
        }
        else
        {
            // 验签失败
            $this->load->view('wallet/error');
        }
    }

    /*
     * Safari外链充值方式
     * @date:2016-10-12
     */
    public function rechargeParams($params, $sign)
    {
        $data = array(
            'status' => false,
            'data' => ''
        );
        switch ($params['pay_type'])
        {
            case 'yeepayMPay':
                    $params['configId'] = 2;
                    $params['money']  =  ParseUnit($params['money']);
                    $params['lib'] = 'YeepayMPay';
                    $responeData = apiRequest('api/RechargeHandle','request',$params);
                    if(!$responeData['code'])
                    {
                        $data = array(
                            'status' => false,
                            'type' => 'url',
                            'data' => ''
                        );
                    }
                    else
                    {
                        $data = array(
                            'status' => true,
                            'type' => 'url',
                            'data' => $responeData['data']['url']
                        );
                    }
                    break;
            case 'sumpayWap':
                    $params['configId'] = 10;
                    $params['money']  =  ParseUnit($params['money']);
                    $params['lib'] = 'SumPay';
                    $responeData = apiRequest('api/RechargeHandle','request',$params);
                    if(!$responeData['code'])
                    {
                        $data = array(
                            'status' => false,
                            'type' => 'page',
                            'data' => ''
                        );
                    }
                    else
                    {
                        $payData = $responeData['data'];

                        $data = array(
                            'status' => true,
                            'type' => 'page',
                            'data' => array(
                                'view' => 'sumpay',
                                'params' => $payData
                            )
                        );
                    }
                    break;
            case 'xzZfbWap':
                    $params['lib'] = 'XzPay';
                    $params['money']  =  ParseUnit($params['money']);
                    $responeData = apiRequest('api/RechargeHandle','request',$params);
                    if(!$responeData['code'])
                    {
                        $data = array(
                            'status' => false,
                            'type' => 'url',
                            'data' => ''
                        );
                    }
                    else
                    {
                        $response = $responeData['data'];

                        $data = array(
                            'status' => true,
                            'type' => 'url',
                            'data' => $response['action']
                        );
                    }
                    break;
            case 'hjZfbPay':
            case 'hjWxWap':
            case 'hjZfbWap':
            case 'hjZfbSh':
                    $params['lib'] = 'HjPay';
                    $params['money']  =  ParseUnit($params['money']);
                    $responeData = apiRequest('api/RechargeHandle','request',$params);
                    if(!$responeData['code'] || 
                      !isset($responeData['data']['result_code']) ||
                      $responeData['data']['result_code'] != 'SUCCESS' ||
                      !isset($responeData['data']['return_code']) ||
                      $responeData['data']['return_code'] != 'SUCCESS'
                      )
                    {
                        $data = array(
                            'status' => false,
                            'type' => 'page',
                            'data' => ''
                        );
                    }
                    else
                    {
                        $response = array();
                        $response['params'] = $responeData['data'];
                        $response['view'] = $params['pay_type']=='hjZfbPay' ? 'hjzfb' : $params['pay_type'] ; 
                        $data = array(
                            'status' => true,
                            'type' => 'page',
                            'data' => $response
                        );
                    }
                    break;
            case 'jdPay':
                    $params['lib'] = 'JdPay';
                    $params['configId'] = 70;
                    $params['money']  =  ParseUnit($params['money']);
                    $params['uid'] = (string)$params['uid'];
                    $responeData = apiRequest('api/RechargeHandle','request',$params);
                    if(!$responeData['code'])
                    {
                        $data = array(
                            'status' => false,
                            'type' => 'page',
                            'data' => ''
                        );
                    }
                    else
                    {
                        $response = $responeData['data'];

                        $data = array(
                            'status' => true,
                            'type' => 'page',
                            'data' => array(
                                'view' => 'jdpay',
                                'params' => $response
                            )
                        );
                    }
                    break;
            case 'umPay':
                $postData = array(
                        'trade_no'      =>  $params['trade_no'],
                        'money'         =>  ParseUnit($params['money']),    // 按分处理
                        'merId'         =>  '50045',
                        'configId'      =>  '73',
                        'id_card'       =>  $params['id_card'],
                        'ip'            =>  UCIP,
                        'uid'           =>  $params['uid'],
                        'real_name'     =>  $params['real_name'],
                );
                
                if ($params['change_bankid']) {
                    header("Location: /ios/paybank/cardlist/".urlencode($this->strCode(json_encode($params), 'ENCODE'))."?sign=".$sign);
                    die;
                }
                $this->load->model('pay_bank_model');
                $this->load->model('wallet_model', 'Wallet');
                $this->load->driver('cache', array('adapter' => 'redis'));
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
                        header("Location: /ios/paybank/add/".urlencode($this->strCode(json_encode($params), 'ENCODE'))."?sign=".$sign);
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
                if($responeData['code'])
                {
                    $response = $responeData['data'];
                    $data = array(
                            'status' => true,
                            'type' => 'page',
                            'data' => array(
                                    'view' => 'umpay',
                                    'params' => array(
                                            'payUrl' => $responeData['data']['payUrl'],
                                            'url' => $params['refer']
                                    )
                            )
                    );
                }
                else
                {
                    $data = array(
                        'status' => false,
                        'type' => 'page',
                        'data' => ''
                    );
                }
                
                break;
            default:
                $data = array(
                    'status' => false,
                    'data' => ''
                );
                break;
        }

        return $data;
    }

    /*
     * 中信微信SDK充值方式
     * @date:2016-10-12
     */
    public function zxwxSdkRecharge($params)
    {
        // 调用中信微信生成预支付订单
        $params['lib'] = 'ZxWeixinPay';
        $params['money'] = ParseUnit($params['money']);
        $responeData = apiRequest('api/RechargeHandle','request',$params);
        if(!$responeData['code'])
        {
            $rechParams = array(
                'status' => '400',
                'msg' => '支付系统维护中',
                'type' => 'sdk',
                'payType' => '8',
                'data' => $responeData
            );
            return $rechParams;
        }

        $payData = $responeData['data'];

        if(empty($payData['prepayid']))
        {
            $rechParams = array(
                'status' =>  '400',
                'msg' => '支付系统维护中',
                'type' => 'sdk',
                'payType' => '8',
                'data' => ''
            );
            return $rechParams;
        }

        // 组装数据
        $sdkData = array(
            'appid'         =>  $payData['appid'],
            'partnerid'     =>  $payData['partnerid'],
            'noncestr'      =>  $payData['noncestr'],
            'sign'          =>  $payData['sign'],       
            'timestamp'     =>  $payData['timestamp'],
            'prepayid'      =>  $payData['prepayid'],
            'trade_no'      =>  $params['trade_no'],
            'package'       =>  'Sign=WXPay'
        );
        
        // 加密
        $sdkJs = $this->strCode(json_encode($sdkData), 'ENCODE');

        $salt = 'ios166cai';

        // token验证
        $token = md5("{$sdkData['appid']}{$sdkData['partnerid']}{$sdkData['noncestr']}{$sdkData['sign']}{$sdkData['timestamp']}{$sdkData['prepayid']}{$sdkData['trade_no']}{$sdkData['package']}{$salt}");

        $rechParams = array(
            'status' =>  '200',
            'msg' => '预支付订单创建成功',
            'type' => 'sdk',
            'payType' => '8',
            'data' => array(
                'sdkJs' => $sdkJs,
                'token' => $token
            )
        );
        return $rechParams;
    }
    
    /*
     * 威富通微信SDK充值方式
    * @date:2016-10-12
    */
    public function wftWxSdkRecharge($params)
    {
        // 调用威富通微信生成预支付订单
        $params['lib'] = 'WftPay';
        $params['money'] = ParseUnit($params['money']);
        $responeData = apiRequest('api/RechargeHandle','request',$params);
        if(!$responeData['code'])
        {
            $rechParams = array(
                'status' => '400',
                'msg' => '支付系统维护中',
                'type' => 'sdk',
                'payType' => '10',
                'data' => ''
            );
            return $rechParams;
        }

        $respone = $responeData['data'];
        $payData = json_decode($respone['pay_info'], true);

        if(empty($payData) || empty($payData['prepayid']))
        {
            $rechParams = array(
                    'status' =>  '400',
                    'msg' => '支付系统维护中',
                    'type' => 'sdk',
                    'payType' => '10',
                    'data' => ''
            );
            return $rechParams;
        }
    
        // 组装数据
        $sdkData = array(
                'appid'         =>  $payData['appid'],
                'partnerid'     =>  $payData['partnerid'],
                'noncestr'      =>  $payData['noncestr'],
                'sign'          =>  $payData['sign'],
                'timestamp'     =>  $payData['timestamp'],
                'prepayid'      =>  $payData['prepayid'],
                'trade_no'      =>  $params['trade_no'],
                'package'       =>  'Sign=WXPay'
        );
    
        // 加密
        $sdkJs = $this->strCode(json_encode($sdkData), 'ENCODE');
    
        $salt = 'ios166cai';
    
        // token验证
        $token = md5("{$sdkData['appid']}{$sdkData['partnerid']}{$sdkData['noncestr']}{$sdkData['sign']}{$sdkData['timestamp']}{$sdkData['prepayid']}{$sdkData['trade_no']}{$sdkData['package']}{$salt}");
    
        $rechParams = array(
                'status' =>  '200',
                'msg' => '预支付订单创建成功',
                'type' => 'sdk',
                'payType' => '10',
                'data' => array(
                        'sdkJs' => $sdkJs,
                        'token' => $token
                )
        );
        return $rechParams;
    }

    /*
     * IOS SDK充值 - 内部查询页
     * @date:2015-10-12
     */
    public function innerPayWait($trade_no = 0)
    {
        // 等待查询支付结果 SDK未设置flag
        if(empty($this->uid))
        {
            die('订单参数缺失');
        }

        $payLog = $this->Wallet->getUserPayLog($this->uid, $trade_no);

        if(empty($payLog))
        {
            die('订单参数缺失');
        }

        if($payLog['status'] == 1)
        {
            // 跳转至成功页
            $rechargeView = $this->config->item('pages_url') . "ios/wallet/innerDetail/" . $trade_no;
            header('Location: ' . $rechargeView);
        }
        else
        {
            // 进入等待查询页
            $info = array(
                'trade_no' => $trade_no,
            );

            $this->load->view('wallet/innerPayWait', $info);
        }   
    }

    /*
     * 查询订单支付结果
     * @date:2016-10-12
     */
    public function getPayStatus()
    {
        $trade_no = $this->input->post('trade_no', null);

        $result = array(
            'status' => '0',
            'msg' => '无查询结果',
            'data' => ''
        );

        if(!empty($trade_no) && !empty($this->uid))
        {
            $payLog = $this->Wallet->getUserPayLog($this->uid, $trade_no);

            if(!empty($payLog) && $payLog['status'] == 1)
            {
                // 跳转至成功页
                $result = array(
                    'status' => '1',
                    'msg' => '支付成功',
                    'data' => $this->config->item('pages_url') . "ios/wallet/innerDetail/" . $trade_no
                );
            }
            elseif($payLog['select_num'] < 10)
            {
                $payData['select_num'] = $payLog['select_num'] + 1;
                $this->Wallet->updatePayLog($trade_no, $payData); //更新刷新次数
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

    /*
     * IOS SDK充值 - 内部成功页
     * @date:2015-10-12
     */
    public function innerDetail($trade_no = 0)
    {
        $order = $this->Wallet->getWalletLog($this->uid, $trade_no);

        $detail = array();
        if(!empty($order))
        {
            $this->config->load('pay');
            $payAllCfg = $this->config->item('pay_all_cfg');
            $tmpAry = explode('@', $order['additions']);

            $detail = array(
                'money' => ParseUnit($order['money'],1),
                'payType' => $payAllCfg['pay_cfg'][$tmpAry[0]]['name'],
                'redirectPage' => $order['orderId'] ? 'order' : 'recharge',
            );

            if( $detail['redirectPage'] == 'order' && !empty($order['orderId']) )
            {
                if($order['status'] == 3)
                {
                    $this->load->model('united_order_model');
                    $unitedOrder = $this->united_order_model->getOrder(array('o.orderId' => $order['orderId']), 'o.status');
                    // 跳转支付页面
                    if ($unitedOrder['status'] >= 40)
                    {
                        $orderDetail = $this->strCode(json_encode(array(
                                'uid'       =>  $this->uid,
                                'orderId'   =>  $order['orderId'],
                                'orderType' =>  4,
                                'ctype'     =>  1,
                        )), 'ENCODE');
                        $detail['payView'] = $this->config->item('protocol') . $this->config->item('pages_url') . 'ios/hemai/detail/hm' . $order['orderId'] . '/' . urlencode($orderDetail);
                    }
                    else
                    {
                        $orderDetail = $this->strCode(json_encode(array(
                                'uid'       =>  $this->uid,
                                'orderId'   =>  $order['orderId'],
                                'orderType' =>  4,
                                'buyMoney'  =>  $order['money'],
                                'ctype'     =>  0,
                        )), 'ENCODE');
                        $detail['payView'] = $this->config->item('protocol') . $this->config->item('pages_url') . "ios/order/doPay/" . urlencode($orderDetail);
                    }
                }
                else 
                {
                    // 订单信息加密
                    $orderDetail = $this->strCode(json_encode(array(
                            'uid'       =>  $this->uid,
                            'orderId'   =>  $order['orderId'],
                            'orderType' =>  $order['status']   // 根据status判断 0：自购 1：追号
                    )), 'ENCODE');
                    
                    $detail['payView'] = $this->config->item('protocol') . $this->config->item('pages_url') . "ios/order/doPay/" . urlencode($orderDetail);
                }
                // 跳转支付页面
                $detail['redirectPage'] = $detail['redirectPage'];
            }
        }

        $detail['status'] = TRUE;
        $this->load->view('wallet/innerResult', $detail); 
    }

    /*
     * 中信微信SDK马甲充值方式
     * @date:2016-10-12
     */
    public function zxwxSdkByChannel($params)
    {
        // 调用中信微信生成预支付订单
        $postData = array(
            'trade_no'      =>  $params['trade_no'],
            'money'         =>  ParseUnit($params['money']),   // 按分处理
            'channel'       =>  $params['channel']
        );

        // 调用接口获取参数
        $payData = apiRequest('api/RechargeHandle','zxwxSdkByChannel',$postData);
        if(empty($payData['prepayid']))
        {
            $rechParams = array(
                'status' =>  '400',
                'msg' => '支付系统维护中',
                'type' => 'sdk',
                'payType' => '8',
                'data' => ''
            );
            return $rechParams;
        }

        // 组装数据
        $sdkData = array(
            'appid'         =>  $payData['appid'],
            'partnerid'     =>  $payData['partnerid'],
            'noncestr'      =>  $payData['noncestr'],
            'sign'          =>  $payData['sign'],       
            'timestamp'     =>  $payData['timestamp'],
            'prepayid'      =>  $payData['prepayid'],
            'trade_no'      =>  $params['trade_no'],
            'package'       =>  'Sign=WXPay'
        );
        
        // 加密
        $sdkJs = $this->strCode(json_encode($sdkData), 'ENCODE');

        $salt = 'ios166cai';

        // token验证
        $token = md5("{$sdkData['appid']}{$sdkData['partnerid']}{$sdkData['noncestr']}{$sdkData['sign']}{$sdkData['timestamp']}{$sdkData['prepayid']}{$sdkData['trade_no']}{$sdkData['package']}{$salt}");

        $rechParams = array(
            'status' =>  '200',
            'msg' => '预支付订单创建成功',
            'type' => 'sdk',
            'payType' => '8',
            'data' => array(
                'sdkJs' => $sdkJs,
                'token' => $token
            )
        );
        return $rechParams;
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
            $info = $this->Cache->getPreloadInfo($platform = 'ios', 'payResult');
            
            $detail = $info[$orderInfo['lid']];
            if(!empty($detail) && (!empty($detail['webUrl']) || in_array($detail['appAction'], array('bet', 'email'))))
            {
                if($detail['appAction'] == 'email' && $versionInfo['appVersionCode'] <= '16')
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

    public function wftWxRecharge($params)
    {
        // 调用威富通微信生成预支付订单
        $params['lib'] = 'WftPay';
        $params['money'] = ParseUnit($params['money']);
        $responeData = apiRequest('api/RechargeHandle','request',$params);
        if(!$responeData['code'])
        {
            $rechParams = array(
                'status' => '400',
                'msg' => '支付系统维护中',
                'type' => 'safari',
                'payType' => '11',
                'data' => ''
            );
            return $rechParams;
        }

        $respone = $responeData['data'];

        if(empty($respone['code_img_url']))
        {
            $rechParams = array(
                'status' =>  '400',
                'msg' => '支付系统维护中',
                'type' => 'safari',
                'payType' => '11',
                'data' => ''
            );
            return $rechParams;
        }
    
        $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https:" : "http:";

        $payDetail = $this->strCode(json_encode(array(
            'code_img_url' => $respone['code_img_url'],
            'codeUrl' => $respone['code_url'],
            'code_status' => $respone['code_status'],
            'orderId' => $respone['orderId'],
            'orderTime' => $respone['orderTime'],
            'txnAmt' => $respone['txnAmt']
        )), 'ENCODE');

        $payView = $protocol . $this->config->item('pages_url') . "ios/wallet/getWinxin/" . urlencode($payDetail);
    
        $rechParams = array(
            'status' =>  '200',
            'msg' => '预支付订单创建成功',
            'type' => 'safari',
            'payType' => '11',
            'data' => $payView
        );
        return $rechParams;
    }
    /**
     * [wftZfbWapRecharge 威富通支付宝wap]
     * @author LiKangJian 2017-09-11
     * @param  [type] $params [description]
     * @return [type]         [description]
     */
    public function wftZfbWapRecharge($params)
    {
        // 调用威富通微信生成预支付订单
        $params['lib'] = 'WftPay';
        $params['money'] = ParseUnit($params['money']);
        $responeData = apiRequest('api/RechargeHandle','request',$params);
        if(!$responeData['code'])
        {
            $rechParams = array(
                'status' => '400',
                'msg' => '支付系统维护中',
                'type' => 'safari',
                'payType' => '16',
                'data' => ''
            );
            return $rechParams;
        }

        $respone = $responeData['data'];

        if(empty($respone['code_img_url']))
        {
            $rechParams = array(
                'status' =>  '400',
                'msg' => '支付系统维护中',
                'type' => 'safari',
                'payType' => '16',
                'data' => ''
            );
            return $rechParams;
        }
        $backurl = $postUrl.'app/wallet/wftZfbWap/'.$respone['orderId'].'?backurl='.$respone['backUrl'].'&scheme='.urlencode('alipays://platformapi/startapp?').'saId'.urlencode('=10000007&').'qrcode='.urlencode($respone['code_url'].'?_s=web-other');
        $rechParams = array(
            'status' =>  '200',
            'msg' => '预支付订单创建成功',
            'type' => 'safari',
            'payType' => '16',
            'data' => $backurl
        );
        return $rechParams;
    }
    public function wftWxWapRecharge($params)
    {
        // 调用威富通微信生成预支付订单
        $params['lib'] = 'WftPay';
        $params['money'] = ParseUnit($params['money']);
        $responeData = apiRequest('api/RechargeHandle','request',$params);
        if(!$responeData['code'])
        {
            $rechParams = array(
                'status' => '400',
                'msg' => '支付系统维护中',
                'type' => 'safari',
                'payType' => '22',
                'data' => ''
            );
            return $rechParams;
        }

        $respone = $responeData['data'];

        $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https:" : "http:";
        
        $backurl = $protocol . $this->config->item('pages_url') .'ios/wallet/wftWxWap/'.$respone['orderId'].'?backurl='.$responeData['data']['pay_info'];
        $rechParams = array(
            'status' =>  '200',
            'msg' => '预支付订单创建成功',
            'type' => 'safari',
            'payType' => '22',
            'data' => $backurl
        );
        return $rechParams;
    }
    
    public function doTomatoZfbWapRecharge($params)
    {
        // 构建参数
        $postData = array(
            'uid'           =>  $params['uid'],
            'trade_no'      =>  $params['trade_no'],
            'money'         =>  ParseUnit($params['money']),        // 分
            'ip'            =>  UCIP,
            'pay_type'      =>  $params['pay_type'],
            'real_name'     =>  $params['real_name'],
            'id_card'       =>  $params['id_card'],
            'merId'         =>  $params['merId'],
            'configId'      =>  $params['configId'],
        );
        if ($postData['money'] < 20000) {
            $result = array(
                'status' => '400',
                'msg' => '支付宝充值需200元起',
                'data' => ''
            );
            return $result;
        }
        if ($postData['money'] > 1000000) {
            $result = array(
                'status' => '400',
                'msg' => '支付宝充值限额10000元',
                'data' => ''
            );
            return $result;
        }
        // 调用接口获取参数
        $postData['lib'] =  'TomatoPay';
        $responeData = apiRequest('api/RechargeHandle','request',$postData);
        if(!$responeData['code'])
        {
            $result = array(
                'status' => '0',
                'msg' => '支付系统维护中',
                'data' => ''
            );
            return $result;
        }
        $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https:" : "http:";
        $backurl = $protocol . $this->config->item('pages_url') .'ios/wallet/tomatoPay/'.$responeData['data']['orderId'].'?backurl='.urlencode($responeData['data']['backurl']);
        $rechParams = array(
            'status' =>  '200',
            'msg' => '预支付订单创建成功',
            'type' => 'safari',
            'payType' => '16',
            'data' => $backurl
        );
        return $rechParams;
    }
    
    public function doTomatoWxWapRecharge($params)
    {
        // 构建参数
        $postData = array(
            'uid'           =>  $params['uid'],
            'trade_no'      =>  $params['trade_no'],
            'money'         =>  ParseUnit($params['money']),        // 分
            'ip'            =>  UCIP,
            'pay_type'      =>  $params['pay_type'],
            'real_name'     =>  $params['real_name'],
            'id_card'       =>  $params['id_card'],
            'merId'         =>  $params['merId'],
            'configId'      =>  $params['configId'],
        );
        // 调用接口获取参数
        $postData['lib'] =  'TomatoPay';
        $responeData = apiRequest('api/RechargeHandle','request',$postData);
        if(!$responeData['code'])
        {
            $result = array(
                'status' => '0',
                'msg' => '支付系统维护中',
                'data' => ''
            );
            return $result;
        }
        $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https:" : "http:";
        $backurl = $protocol . $this->config->item('pages_url') .'ios/wallet/tomatoWxPay/'.$responeData['data']['orderId'].'?backurl='.urlencode($responeData['data']['backurl']);
        $rechParams = array(
            'status' =>  '200',
            'msg' => '预支付订单创建成功',
            'type' => 'safari',
            'payType' => '16',
            'data' => $backurl
        );
        return $rechParams;
    }
    
    /**
     * @param $params
     * @return array
     * 上海银行微信h5银行渠道
     */
    public function doUlineWxRecharge($params)
    {
        // 构建参数
        $postData = array(
            'uid'           =>  $params['uid'],
            'trade_no'      =>  $params['trade_no'],
            'money'         =>  ParseUnit($params['money']),        // 分
            'ip'            =>  UCIP,
            'pay_type'      =>  $params['pay_type'],
            'real_name'     =>  $params['real_name'],
            'id_card'       =>  $params['id_card'],
            'merId'         =>  $params['merId'],
            'configId'      =>  $params['configId'],
        );

        // 调用接口获取参数
        $postData['lib'] =  'UlinePay';
        $responeData = apiRequest('api/RechargeHandle','request',$postData);
        if(!$responeData['code'])
        {
            $result = array(
                'status' => '0',
                'msg' => '支付系统维护中',
                'data' => ''
            );
            return $result;
        }
        $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https:" : "http:";
        $url_zhifu = $responeData['data']['mweb_url'] . '&redirect_url=' . urlencode($responeData['data']['return_url'].'?trade_no='.$responeData['data']['out_trade_no']);
        $backurl = $protocol . $this->config->item('pages_url') . 'ios/wallet/pufaWxWap/' . $responeData['data']['out_trade_no'] . '?backurl=' . urlencode($url_zhifu);
        $rechParams = array(
            'status' => '200',
            'msg' => '预支付订单创建成功',
            'type' => 'safari',
            'payType' => '23',
            'data' => $backurl
        );
        return $rechParams;
    }
    
    public function doPfWxWapRecharge($params)
    {
        // 构建参数
        $postData = array(
            'uid'           =>  $params['uid'],
            'trade_no'      =>  $params['trade_no'],
            'money'         =>  ParseUnit($params['money']),        // 分
            'ip'            =>  UCIP,
            'pay_type'      =>  $params['pay_type'],
            'real_name'     =>  $params['real_name'],
            'id_card'       =>  $params['id_card'],
            'merId'         =>  $params['merId'],
            'configId'      =>  $params['configId'],
        );
        // 调用接口获取参数
        $postData['lib'] =  'BbnPay';
        $responeData = apiRequest('api/RechargeHandle','request',$postData);
        if(!$responeData['code'])
        {
            $result = array(
                'status' => '0',
                'msg' => '支付系统维护中',
                'data' => ''
            );
            return $result;
        }
        $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https:" : "http:";
        $data = array(
            'appid' => $responeData['data']['appid'],
            'transid' => $responeData['data']['transid'],
            'paytype' => 1,
            'backurl' => $responeData['data']['backurl']  //支付后要跳转到的页面 
        );
        $data_zhifu = urlencode(json_encode($data));  //用户url传输的data数据
        $sign_zhifu = $this->getSign($data, $responeData['data']['key']);
        $url_zhifu = 'https://payh5.bbnpay.com/h5pay/way.php?data=' . $data_zhifu . '&sign=' . $sign_zhifu . '&signtype=MD5';
        $backurl =   $protocol . $this->config->item('pages_url') .'ios/wallet/pufaWxWap/'.$responeData['data']['orderId'].'?backurl='.urlencode($url_zhifu);
        $rechParams = array(
            'status' =>  '200',
            'msg' => '预支付订单创建成功',
            'type' => 'safari',
            'payType' => '23',
            'data' => $backurl
        );
        return $rechParams;
    }
    
    public function getSign($array, $appkey)
    {
        $str = "";
        ksort($array); //按字典排序
        foreach ($array as $k => $v) {
            $str .= $k . '=' . $v . '&';   //以key=value&key=value格式处理好数据
        }
        $str .= 'key=' . $appkey;  //最后加上签名
        return md5($str);
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

    // 分配支付参数
    public function exchangeRecharge($platform, $pay_type = '')
    {
        // yeepayMPay sumpayWap weixinPay weixinScan zfbWap
        preg_match('/(\d+)_(\d+)/', $pay_type, $matches);
        $ctype = $matches[1];
        $ptype = $matches[2];
        $getPayInfo = $this->getPayInfo();
        if(in_array($ctype, array('6', '8')))
        {
            //新的逻辑代码
            $this->load->driver('cache', array('adapter' => 'redis'));
            $REDIS = $this->config->item('REDIS');
            if(in_array($_SERVER['SERVER_ADDR'],array('120.132.33.194','123.59.105.39'))){
                $dispatchInfo = json_decode($this->cache->hGet($REDIS['CS_RCG_DISPATCH'], $platform),true);
            }else{
                $dispatchInfo = json_decode($this->cache->hGet($REDIS['RCG_DISPATCH'], $platform),true);
            }
            $redisKey = $platform . '_' . $ctype;
            if( isset( $dispatchInfo[$ctype] ) )
            {
                $payInfo = $dispatchInfo[$ctype][0];
                $rcgInfo = array(
                    'pay_type'  =>  $getPayInfo[$payInfo['pay_type']],     // 实际支付名如wftWx
                    'merId'     =>  $payInfo['mer_id'],
                    'configId'  =>  $payInfo['id'],
                    'rcg_group' =>  $redisKey,
                );
                $getPayInfo = array_flip($getPayInfo);
                $rcgInfo['pay_type_id'] = $getPayInfo[$rcgInfo['pay_type']];
            }else{
                // TODO 取默认
                $configDetail = $this->Wallet->getPayConfigByCtype($platform, $ctype);
                if(!empty($configDetail[0]))
                {
                    $rcgInfo = array(
                        'pay_type'  =>  $getPayInfo[$configDetail[0]['pay_type']],
                        'merId'     =>  $configDetail[0]['mer_id'],
                        'configId'  =>  $configDetail[0]['id'],
                        'rcg_group' =>  $redisKey,
                    );
                    $rcgInfo['pay_type_id'] = $configDetail[0]['pay_type'];
                }
            }
        }
        elseif (in_array($ctype, array('2', '3', '4'))) 
        {
            // 支付宝微信按当下配置的概率计算出分配的商户号
            $rcgInfo = array();
            $params = array(
                'platform'  =>  $platform,
                'ctype'     =>  $ctype,
                'isDev'     =>  (in_array($_SERVER['SERVER_ADDR'], array('120.132.33.194', '123.59.105.39'))) ? '1' : '0',
            );
            $dispatch = apiRequest('api/RechargeHandle', 'randomRate' , $params);
            if(!empty($dispatch))
            {
                $rcgInfo = array(
                    'pay_type'      =>  $getPayInfo[$dispatch['pay_type']],
                    'merId'         =>  $dispatch['mer_id'],
                    'configId'      =>  $dispatch['id'],
                    'rcg_group'     =>  $platform . '_' . $ctype,
                    'pay_type_id'   =>  $dispatch['pay_type'],
                );
            }
        }
        else
        {
            // 针对易宝 统统
            $configDetail = $this->Wallet->getPayConfigDetail($platform, $ptype);
            $rcgInfo = array(
                'pay_type'  =>  $getPayInfo[$ptype],     // 实际支付名如wftWx
                'merId'     =>  $configDetail[0]['mer_id'] ? $configDetail[0]['mer_id'] : '',
                'configId'  =>  $configDetail[0]['id'] ? $configDetail[0]['id'] : '0',
                'rcg_group' =>  $platform . '_1',   // 默认快捷
            );
        }
                
        return $rcgInfo;
    }
    
    private function getAddition($pay_type)
    {
        $additions = array(
                '1_1'   => 'yeepayMPay',
                '1_4'   => 'LianlianPay',
                '1_6'   => 'sumpayWap',
                '1_13'  => 'jdPay',
                '2_0'   => 'weixinPay',
                '3_0'   => 'weixinScan',
                '4_0'   => 'zfbWap',
                '6_0'   =>  'umPay',
        );
         
        return empty($additions[$pay_type]) ? $pay_type : $additions[$pay_type];
    }

    // 获取支付类型映射表
    public function getPayInfo()
    {
        $payConfig = array(
            1   =>  'yeepayMPay',
            6   =>  'sumpayWap',
            8   =>  'zxwxSdk',
            10  =>  'wftWxSdk',
            11  =>  'wftWx',
            12  =>  'xzZfbWap',
            13  =>  'jdPay',
            14  =>  'umPay',
            15  =>  'hjZfbPay',
            16  =>  'wftZfbWap',
            18  =>  'hjWxWap',
            19  =>  'hjZfbWap',
            22  =>  'wftWxWap',
            23  =>  'pfWxWap',
            28  =>  'yzpayh',
            29  =>  'tomatoZfbWap',
            31  =>  'ulineWxWap',
            34  =>  'hjZfbSh',
            33  =>  'yzWxWap',
            37  =>  'tomatoWxWap',
        );
        return $payConfig;
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
                    'maxmoney'  =>  '5000',
                    'secredpack'=>  '5000',
                ),
                '1_6' =>  array(
                    'pay_type'  =>  '1_6',
                    'idName'    =>  'payTtf',
                    'className' =>  'pay-ttf',
                    'title'     =>  '银行卡快捷-统统付',
                    'desc'      =>  '最高3千/笔，5千/日，1万/月',
                    'maxmoney'  =>  '3000',
                    'secredpack'=>  '3000',
                ),
                '1_1' =>  array(
                    'pay_type'  =>  '1_1',
                    'idName'    =>  'payYbzf',
                    'className' =>  'pay-ybzf',
                    'title'     =>  '银行卡快捷-易宝支付',
                    'desc'      =>  '最高5千/笔，1万/日，2万/月',
                    'maxmoney'  =>  '5000',
                    'secredpack'=>  '2000',
                ),
                '3_0' =>  array(
                    'pay_type'  =>  '3_0',
                    'idName'    =>  'payWxSao',
                    'className' =>  'pay-wx',
                    'title'     =>  '微信扫码支付',
                    'desc'      =>  '最高5万/日',
                    'maxmoney'  =>  '0',
                    'secredpack'=>  '0',
                ),
                '1_4'  =>  array(
                    'pay_type'  =>  '1_4',
                    'idName'    =>  'payLl',
                    'className' =>  'pay-ll',
                    'title'     =>  '银行卡快捷-连连支付',
                    'desc'      =>  '免手续费，最高5000/笔',
                    'maxmoney'  =>  '5000',
                    'secredpack'=>  '5000',
                ),
                '8_0'  =>  array(
                    'pay_type'  =>  '8_0',
                    'idName'    =>  'payJd',
                    'className' =>  'pay-jd',
                    'title'     =>  '京东支付',
                    'desc'      =>  '最高5千/笔，1万/日',
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
     * [hjZfbWap 针对鸿粤浦发银行支付宝封装方法]
     * @author LiKangJian 2017-12-05
     * @param  [type] $orderId [description]
     * @return [type]          [description]
     */
    public function hjZfbWap($orderId)
    {
        $backUrl = $_GET['backurl'].'?trade_no='.$orderId;
        $this->load->view('wallet/pay/hjZfbWap',array('backUrl' =>$backUrl) );
    }

    public function hjZfbSh($orderId)
    {
        $backUrl = $_GET['backurl'].'?trade_no='.$orderId;
        $this->load->view('wallet/pay/hjZfbWap',array('backUrl' =>$backUrl) );
    }
    
    public function wftWxWap($orderId)
    {
        $backUrl = $_GET['backurl'].'&service=pay.weixin.wappayv2&trade_no='.$orderId;
        $this->load->view('wallet/pay/wftWxWap',array('backUrl' =>$backUrl) );
    }
    
    public function pufaWxWap($orderId)
    {
        $backUrl = $_GET['backurl'];
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
