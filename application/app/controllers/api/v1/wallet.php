<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/*
 * APP 支付中心
 * @date:2016-01-18
 */
class Wallet extends MY_Controller 
{
    // SDK 验证 salt
    private $salt = '166androidcp';

    public function __construct() 
    {
        parent::__construct();
        $this->load->model('order_model','Order');
        $this->load->model('user_model','User');
        $this->load->model('wallet_model','Wallet');
        $this->load->model('cache_model','Cache');
        $this->versionInfo = $this->getUserAgentInfo();
    }

    /*
     * APP 充值调用 支付宝、微信、盛付通
     * @date:2015-05-11
     */
    public function doRecharge()
    {
        $redata = $this->input->post(null);
        // 充值渠道关闭
        $result = array(
         'status' => '0',
         'msg' => '系统升级维护中',
         'data' => array()
        );
        die(json_encode($result));

        if( empty($redata['token']) || empty($redata['pay_type']) || empty($redata['money']) || empty($redata['redirectPage']) )
        {
            $result = array(
                'status' => '0',
                'msg' => '必要参数缺失',
                'data' => $redata
            );
            die(json_encode($result));
        }

        // 验证提交信息
        $data = $this->strCode(urldecode($redata['token']));
        $data = json_decode($data, true);
        if(empty($data['uid']))
        {
            $result = array(
                'status' => '0',
                'msg' => '参数校验失败',
                'data' => $redata
            );
            die(json_encode($result));
        }

        $uinfo = $this->User->getUserInfo($data['uid']);
        if(isset($uinfo['userStatus']) && $uinfo['userStatus'] == '1')
        {
            $result = array(
                'status' => '0',
                'msg' => '此账户已注销',
                'data' => $redata
            );
            die(json_encode($result));
        }
        if($uinfo['userStatus'] == '2')
        {
            $result = array(
                    'status' => '0',
                    'msg' => '您的账户已被冻结，如需解冻请联系客服。',
                    'data' => $redata
            );
            die(json_encode($result));
        }

        // 是否实名认证
        if(empty($uinfo['real_name']) || empty($uinfo['id_card']))
        {
            $result = array(
                'status' => '0',
                'msg' => '请先完成实名认证',
                'data' => $redata
            );
            die(json_encode($result));
        }
        // 实名信息
        $redata['real_name'] = $uinfo['real_name'];
        $redata['id_card'] = $uinfo['id_card'];
        $redata['created'] = $uinfo['created'];

        $redata['orderId'] = $data['orderId']?$data['orderId']:'';
        $redata['orderType'] = $data['orderType']?'1':'0';
                if($data['orderType'] == 4)
                {
                    $redata['orderType'] = 3;
                }
                if($data['orderType'] == 5)
                {
                    $redata['orderType'] = 5;
                }
        $redata['lid'] = $data['lid']?$data['lid']:0;
        // 平台、渠道及版本信息
        $redata['platform'] = $this->config->item('platform');
        $channelName = $redata['channel'];
        $redata['channel'] = $this->recordChannel($redata['channel']);
        $redata['app_version'] = (isset($redata['app_version']) && !empty($redata['app_version']))?$redata['app_version']:'1.0';

        // 渠道关闭
        $channelArr = $this->Cache->getLimitChannel();
        if(in_array($redata['channel'], $channelArr))
        {
            $result = array(
                'status' => '0',
                'msg' => '暂停充值',
                'data' => $redata
            );
            die(json_encode($result));
        }

        // 金额校验
        if( !is_numeric($redata['money']) || $redata['money'] <= 0 )
        {
            $result = array(
                'status' => '0',
                'msg' => '充值金额错误',
                'data' => $redata
            );
            die(json_encode($result));
        }


        // 按充值渠道判断金额限制
        if($redata['redirectPage'] == 'order')
        {
            if(!preg_match('/^\+?([0-9]+|[0-9]+\.[0-9]{0,2})$/', $redata['money']))
            {
                $result = array(
                    'status' => '0',
                    'msg' => '充值金额格式错误',
                    'data' => $redata
                );
                die(json_encode($result));
            }
            if($redata['money'] < 200 && $redata['pay_type'] == '4_0'){
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
            //if (!in_array($channelName, array('sc-cwyx1_cpa_taj','sc-cwyx2_cpa_taj','sc-juzhang_cpc_taj','166cai-sc-lyc_cpa_taj','sc-tuia1_as_lj','sc-tuia_as_lj','sc-smhz_cpc_taj','sc-huaqianwuyou1_cps_taj','sc-huaqianwuyou2_cps_taj','sc-huaqianwuyou3_cps_taj','sc-ruibo_cps_taj','sc-tieluwifi_cpc_taj','sc-moji_cpt_taj','sc-tuiajx_cpc_taj')))
            {
                if(preg_match('/^\d+$/', $redata['money']))
                {
                    if($redata['money'] < 10)
                    {
                        $result = array(
                            'status' => '0',
                            'msg' => '请至少充值10元',
                            'data' => $redata
                        );
                        die(json_encode($result));
                    }
                    if ($redata['money'] < 200 && $redata['pay_type'] == '4_0') {
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
                        'data' => $redata
                    );
                    die(json_encode($result));
                }
            }
        }

        // 红包使用条件检查
        if(!empty($redata['redpackId']))
        {
            $redpacks = array();
            $redpackArry = explode(',', $redata['redpackId']);
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
                        array_push($redpacks, $redPackStr[0]);
                        $redpackParams = json_decode($redpackInfo[$redPackStr[0]]['use_params'], true);
                        
                        $checkMoney += ParseUnit($redpackParams['money_bar'], 1);
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
                if($checkMoney > $redata['money'])
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

            $redata['redpackId'] = implode(',', $redpacks);
        }

        // 充值开启状态
        $is_recharge = $this->config->item('is_recharge');
        if(!$is_recharge)
        {
            $result = array(
                'status' => '0',
                'msg' => '因彩票停售，充值通道暂时关闭',
                'data' => $redata
            );
            die(json_encode($result));
        }

        // 调试参数
        // if(ENVIRONMENT != 'production')
        // {
        //             $redata['money'] = '0.01';
        // } 
        // 分配具体支付参数 - 区分马甲包
        if($this->checkChannelPackage($channelName))
        {
            $platform = $this->config->item('platform') + 4;
        }
        else
        {
            $platform = $this->config->item('platform') + 1;
        }
        $rechargeInfo = $this->exchangeRecharge($platform, $redata['pay_type']);

        if ($rechargeInfo['pay_type'] == 'tomatoWxWap' && ParseUnit($redata['money']) > 300000) {
            $rechargeInfo['pay_type'] = 'wftWxSdk';
            $rechargeInfo['merId'] = '102510034424';
            if (ENVIRONMENT != 'production') {
                $rechargeInfo['configId'] = '45';
            } else {
                $rechargeInfo['configId'] = '45';
            }
        }
        if(empty($rechargeInfo['pay_type']))
        {
            $result = array(
                'status' => '0',
                'msg' => '支付系统维护中',
                'data' => ''
            );
            die(json_encode($result));
        }

        // 京东支付区分SDK版本，低版本判断h5状态并默认跳h5充值
        if($rechargeInfo['pay_type'] == 'jdSdk' && $this->versionInfo['appVersionCode'] < '40600')
        {
            $payData = $this->Wallet->getPayConfigData($platform, 13);
            if(!empty($payData))
            {
                $rechargeInfo = array(
                    'pay_type'      =>  'jdPay',
                    'merId'         =>  $payData['mer_id'],
                    'configId'      =>  $payData['id'],
                    'rcg_group'     =>  '2_8',
                    'pay_type_id'   =>  '13',
                );
            }
            else
            {
                $result = array(
                    'status' => '0',
                    'msg' => '京东支付维护中，请选择其他充值方式',
                    'data' => ''
                );
                die(json_encode($result));
            }
        }

        // 组装支付必要参数
        $params = array(
            'trade_no'      =>  $this->tools->getIncNum('UNIQUE_KEY'),
            'uid'           =>  $data['uid'],
            'real_name'     =>  $uinfo['real_name'] ? $uinfo['real_name'] : '',
            'id_card'       =>  $uinfo['id_card'] ? $uinfo['id_card'] : '',
            'money'         =>  $redata['money'],
            'pay_type'      =>  $rechargeInfo['pay_type'],
            'merId'         =>  $rechargeInfo['merId'] ? $rechargeInfo['merId'] : '',
            'configId'      =>  $rechargeInfo['configId'] ? $rechargeInfo['configId'] : '',
            'redpackId'     =>  $redata['redpackId'],
            'orderId'       =>  $redata['orderId'],
            'orderType'     =>  $redata['orderType'],
            'ip'            =>  UCIP,
            'pay_time'      =>  date('Y-m-d H:i:s'),
            'channel'       =>  $this->recordChannel($redata['channel']),
            'appVersion'    =>  (isset($redata['app_version']) && !empty($redata['app_version'])) ? $redata['app_version'] : '1.0',
        );
        // 充值渠道判断
        switch ($params['pay_type']) 
        {
            // 快捷
            case 'yeepayMPay':
            case 'sumpayWap':
            case 'xzZfbWap':
            case 'jdPay':
            case 'umPay':
            case 'hjZfbPay':
            case 'hjWxWap': 
            case 'hjZfbWap':
            case 'hjZfbSh':
                $returnData = $this->doSubmitForm($params); 
                break;
            // 快捷
            case 'llpaySdk':
                // $returnData = $this->doLlpaysdkRecharge($data['uid'], $redata);  
                $returnData = array(
                    'status' => '0',
                    'msg' => '暂停充值',
                    'data' => ''
                );
                break;
            case 'zxwxSdk':
                $returnData = $this->doZxwxSdkRecharge($params);    
                break;
                
            case 'wftWxSdk': 
                $returnData = $this->doWftWxSdkRecharge($params);
                break;
            case 'wftZfbWap': 
                $returnData = $this->dowftZfbWapRecharge($params);
                break;
            case 'wftWxWap': 
                $returnData = $this->dowftWxWapRecharge($params);
                break;
            
            case 'wftWx': 
                $returnData = $this->doWftWxRecharge($params);
                break;
            case 'ulineWxWap': 
                $returnData = $this->doUlineWxRecharge($params);
                break;
            case 'pfWxWap': 
                $returnData = $this->doPfWxWapRecharge($params);
                break;
            case 'tomatoZfbWap':
                $returnData = $this->doTomatoZfbWapRecharge($params);
                break;
            case 'tomatoWxWap':
                $returnData = $this->doTomatoWxWapRecharge($params);
                break;
            case 'yzpayh':
                $returnData = $this->doYzpayh($params);
                break;
            case 'yzWxWap':
                $returnData = $this->doYzpayh($params);
                break;
            case 'jdSdk':
                $returnData = $this->doJdSdkRecharge($params);
                break;
            default:
                $returnData = array(
                    'status' => '0',
                    'msg' => '支付系统维护中',
                    'data' => ''
                );
                break;
        }
        // 处理结果
        if( $returnData['status'] != '1' )
        {
            $result = array(
                'status' => '0',
                'msg' => $returnData['msg'],
                'data' => $redata,
                'token' => ''
            );      
            die(json_encode($result));
        }

        // 记录 cp_wallet_logs
        $walletData = array(
            'uid'       =>  $params['uid'], 
            'trade_no'  =>  $params['trade_no'], 
            'orderId'   =>  $params['orderId'], 
            'additions' =>  $params['pay_type'], 
            'mark'      =>  '2', 
            'money'     =>  ParseUnit($params['money']),    // 转化为分
            'status'    =>  $params['orderType'],   // 0普通 1追号 3合买 
            'red_pack'  =>  $params['redpackId'] ? $params['redpackId'] : '', 
            'platform'  =>  $this->config->item('platform'),
            'channel'   =>  $params['channel']
        );

        if($params['orderType'] == 3 && $data['ctype'] == 1)
        {
            $walletData['subscribeId'] = "1";
        }
        $res1 = $this->Wallet->recordWalletLog($walletData);

        // 记录 cp_pay_log
        $payData = array(
            'trade_no'  =>  $params['trade_no'],
            'money'     =>  ParseUnit($params['money']),    // 转化为分
            'pay_time'  =>  $params['pay_time'],
            'pay_type'  =>  $rechargeInfo['pay_type_id'],   // 支付类型
            'rcg_group' =>  $rechargeInfo['rcg_group'],     // 充值方式组号如3_1
            'rcg_serial'=>  $rechargeInfo['configId'],
        );

        $res2 = $this->Wallet->recordPayLog($payData);

        if($res1 && $res2)
        {
            $result = array(
                'status' => '1',
                'msg' => '创建订单成功',
                'data' => $returnData['data'],
                'token' => $returnData['token']
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
        die(json_encode($result));
    }   

    /*
     * APP 易宝支付wap
     * @date:2016-03-29
     */
    public function doSubmitForm($params)
    {
        // 支付中心跳转参数组装
        $recParams = array(
            'uid'           =>  $params['uid'],
            'trade_no'      =>  $params['trade_no'],
            'money'         =>  ParseUnit($params['money']),        // 分
            'ip'            =>  UCIP,
            'pay_type'      =>  $params['pay_type'],
            'real_name'     =>  $params['real_name'],
            'id_card'       =>  $params['id_card'],
            'merId'         =>  $params['merId'],
            'configId'      =>  $params['configId'],
            'token'         =>  '',
            'salt'          =>  ''
        );

        //获取 salt
        $recParams['salt'] = $this->salt;
        $recParams['token'] = md5("{$recParams[trade_no]}{$recParams[uid]}{$recParams[money]}{$recParams[ip]}{$recParams[real_name]}{$recParams[id_card]}{$recParams[merId]}{$recParams[configId]}{$recParams[pay_type]}{$recParams[salt]}");

        $result = array(
            'status' => '1',
            'msg' => '创建订单成功',
            'data' => $recParams,
            'token' => ''
        );

        return $result;
    }
    /**
     * [dowftZfbWapRecharge 支付宝]
     * @author LiKangJian 2017-09-11
     * @param  [type] $params [description]
     * @return [type]         [description]
     */
    public function dowftZfbWapRecharge($params)
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
        $postData['lib'] =  'WftPay';
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
        $backurl = $postUrl.'app/wallet/wftZfbWap/'.$responeData['data']['orderId'].'?backurl='.$responeData['data']['backUrl'].'&scheme='.urlencode('alipays://platformapi/startapp?').'saId'.urlencode('=10000007&').'qrcode='.urlencode($responeData['data']['code_url'].'?_s=web-other');
        $result = array(
            'status' => '1',
            'msg' => '创建订单成功',
            'data' => array(
                     'code_url'=>$backurl,
                     'way'=>'wftZfbWap'
                     ),
        );
        return $result;
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
                'status' => '0',
                'msg' => '支付宝充值需200元起',
                'data' => ''
            );
            return $result;
        }
        if ($postData['money'] > 1000000) {
            $result = array(
                'status' => '0',
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
        $backurl = $protocol . $this->config->item('pages_url') .'app/wallet/tomatoPay/'.$responeData['data']['orderId'].'?backurl='.urlencode($responeData['data']['backurl']);
        $result = array(
            'status' => '1',
            'msg' => '创建订单成功',
            'data' => array(
                     'code_url'=>$backurl,
                     'way'=>'wftZfbWap'
                     ),
        );
        return $result;
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
        $backurl = $protocol . $this->config->item('pages_url') .'app/wallet/tomatoWxPay/'.$responeData['data']['orderId'].'?backurl='.urlencode($responeData['data']['backurl']);
        $result = array(
            'status' => '1',
            'msg' => '创建订单成功',
            'data' => array(
                     'code_url'=>$backurl,
                     'way'=>'wftWxWap'
                     ),
        );
        return $result;
    }
    
    public function dowftWxWapRecharge($params)
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
        $postData['lib'] =  'WftPay';
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
        
        $backurl = $protocol . $this->config->item('pages_url') .'app/wallet/wftWxWap/'.$responeData['data']['orderId'].'?backurl='.$responeData['data']['pay_info'];
        $result = array(
            'status' => '1',
            'msg' => '创建订单成功',
            'data' => array(
                     'code_url'=>$backurl,
                     'way'=>'wftWxWap'
                     ),
        );
        return $result;
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
            'app' => $responeData['data']['appid'],
            'transid' => $responeData['data']['transid'],
            'paytype' => 1,
            'backurl' => $responeData['data']['backurl']  //支付后要跳转到的页面 
        );
        $data_zhifu = urlencode(json_encode($data));  //用户url传输的data数据
        $sign_zhifu = $this->getSign($data, $responeData['data']['key']);
        $url_zhifu = 'https://payh5.bbnpay.com/h5pay/way.php?data=' . $data_zhifu . '&sign=' . $sign_zhifu . '&signtype=MD5';
        $backurl =   $protocol . $this->config->item('pages_url') .'app/wallet/pufaWxWap/'.$responeData['data']['orderId'].'?backurl='.urlencode($url_zhifu);
        $result = array(
            'status' => '1',
            'msg' => '创建订单成功',
            'data' => array(
                 'code_url'=> $backurl,
                 'way'=>'wftWxWap'
                 ),
        );
        return $result;
    }
    
    /*
     * APP 连连支付SDK充值
     * @date:2016-05-25
     */
    public function doLlpaysdkRecharge($uid, $redata)
    {
        // cp_wallet_log
        $walletData = array(
            'uid'           => $uid,
            'trade_no'      => $this->tools->getIncNum('UNIQUE_KEY'),
            'orderId'       => $redata['orderId'],
            'status'        => $redata['orderType'],
            'additions'     => $redata['pay_type'],
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
            'pay_type'  => '4',     // 连连支付SDK
        );

        // 入库 cp_pay_log
        $res2 = $this->Wallet->recordPayLog($payData);

        if($res1 && $res2)
        {
            // 调用支付宝SDK参数
            $this->config->load('pay');
            $llpaySdk = $this->config->item('llpaySdk');

            // 组装数据
            $sdkData = array(
                'oid_partner'   => $llpaySdk['oid_partner'],    // 商户编号
                'sign_type'     => $llpaySdk['sign_type'],      // 签名方式
                'busi_partner'  => $llpaySdk['busi_partner'],   // 商户业务类型
                'key'           => $llpaySdk['key'],
                'no_order'      => $payData['trade_no'],        // 商户唯一订单号
                'dt_order'      => date('YmdHis', strtotime($payData['pay_time'])),     // 商户订单时间
                'name_goods'    => '166cp',                     // 商品名称 
                'info_order'    => '166cp',                     // 订单描述 
                'money_order'   => $redata['money'],            // 交易金额 元
                'notify_url'    => $llpaySdk['notify_url'],
                'user_id'       => $uid,
                'register_time' => date('YmdHis', strtotime($redata['created'])),
                'id_no'         => $redata['id_card'],
                'acct_name'     => $redata['real_name'],
                'flag_modify'   => '1',
            );

            // LOG
            // log_message('LOG', "充值 - 请求参数: " . json_encode($payData), 'recharge');
            
            // 加密
            $sdkJs = $this->strCode(json_encode($sdkData), 'ENCODE');

            // token验证
            $token = md5("{$sdkData['no_order']}{$sdkData['money_order']}{$sdkData['sign_type']}{$sdkData['key']}{$sdkData['user_id']}{$this->salt}");

            $result = array(
                'status' => '1',
                'msg' => '创建订单成功',
                'data' => $sdkJs,
                'token' => $token
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
     * APP 中信微信SDK
     * @date:2016-08-10
     */
    public function doZxwxSdkRecharge($params)
    {
        // 调用中信微信生成预支付订单
        $postData = array(
            'trade_no'      =>  $params['trade_no'],
            'money'         =>  ParseUnit($params['money']),    // 按分处理
            'merId'         =>  $params['merId'],
            'configId'      =>  $params['configId'],
            'uid'           =>  $params['uid'],
        );

        $postData['lib'] =  'ZxWeixinPay';
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

        $payData = $responeData['data'];
        
        if(empty($payData['prepayid']))
        {
            $result = array(
                'status' => '0',
                'msg' => '支付系统维护中',
                'data' => ''
            );
            return $result;
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
        );
        
        // 加密
        $sdkJs = $this->strCode(json_encode($sdkData), 'ENCODE');

        // token验证
        $token = md5("{$sdkData['appid']}{$sdkData['partnerid']}{$sdkData['noncestr']}{$sdkData['sign']}{$sdkData['timestamp']}{$sdkData['prepayid']}{$sdkData['trade_no']}{$this->salt}");

        $result = array(
            'status' => '1',
            'msg' => '创建订单成功',
            'data' => $sdkJs,
            'token' => $token
        );
        return $result;
    }
    
    /*
     * APP 威富通微信SDK
    * @date:2016-08-10
    */
    public function doWftWxSdkRecharge($params)
    {
        // 调用中信微信生成预支付订单
        $postData = array(
            'trade_no'      =>  $params['trade_no'],
            'money'         =>  ParseUnit($params['money']),    // 按分处理
            'merId'         =>  $params['merId'],
            'configId'      =>  $params['configId'],
            'ip'            =>  $params['ip'],
            'uid'           =>  $params['uid'],
        );

        // 调用接口获取参数
        $postData['lib'] =  'WftPay';
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

        $respone = $responeData['data'];
        $payData = json_decode($respone['pay_info'], true);

        if(empty($payData) || empty($payData['prepayid']))
        {
            $result = array(
                    'status' => '0',
                    'msg' => '支付系统维护中',
                    'data' => ''
            );
            return $result;
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
        );

        // 加密
        $sdkJs = $this->strCode(json_encode($sdkData), 'ENCODE');

        // token验证
        $token = md5("{$sdkData['appid']}{$sdkData['partnerid']}{$sdkData['noncestr']}{$sdkData['sign']}{$sdkData['timestamp']}{$sdkData['prepayid']}{$sdkData['trade_no']}{$this->salt}");

        $result = array(
            'status' => '1',
            'msg' => '创建订单成功',
            'data' => $sdkJs,
            'token' => $token
        );

        return $result;
    }
    /*
     * APP 威富通微信扫码
    * @date:2017-03-30
    */
    public function doWftWxRecharge($params)
    {
        // 调用中信微信生成预支付订单
        $postData = array(
            'trade_no'      =>  $params['trade_no'],
            'money'         =>  ParseUnit($params['money']),    // 按分处理
            'merId'         =>  $params['merId'],
            'configId'      =>  $params['configId'],
            'ip'            =>  $params['ip'],
            'uid'           =>  $params['uid'],
        );

        // 调用接口获取参数
        $postData['lib'] =  'WftPay';
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

        $respone = $responeData['data'];

        if(empty($respone['code_img_url']))
        {
            $result = array(
                    'status' => '0',
                    'msg' => '支付系统维护中',
                    'data' => ''
            );
            return $result;
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

        $payView = $protocol . $this->config->item('pages_url') . "app/wallet/getWinxin/" . urlencode($payDetail);

        $result = array(
                'status' => '1',
                'msg' => '创建订单成功',
                'data' => $payView
        );
    
        return $result;
    }

    /**
     * @param $params
     * @return array
     * 盈中平安银行渠道
     */
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

        $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https:" : "http:";
        $backurl =   $protocol . $this->config->item('pages_url') .'app/wallet/pufaWxWap/1'.'?backurl='.urlencode($responeData['data']['code_url']);
        $result = array(
            'status' => '1',
            'msg' => '创建订单成功',
            'data' => array(
                'code_url'=> $backurl,
                'way'=>'wftWxWap'
            ),
        );
        return $result;
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
        $backurl =   $protocol . $this->config->item('pages_url') .'app/wallet/pufaWxWap/'.$responeData['data']['out_trade_no'].'?backurl='.urlencode($url_zhifu);
        $result = array(
            'status' => '1',
            'msg' => '创建订单成功',
            'data' => array(
                 'code_url'=> $backurl,
                 'way'=>'wftWxWap'
                 ),
        );
        return $result;
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
                'pay_type_id' => $ptype,
            );
        }
        return $rcgInfo;
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
            32  =>  'jdSdk',
            34  =>  'hjZfbSh',
            33  =>  'yzWxWap',
            37  =>  'tomatoWxWap',
        );
        return $payConfig;
    }
    
    public function getSign($array, $appkey) {
        $str = "";
        ksort($array); //按字典排序
        foreach ($array as $k => $v) {
            $str .= $k . '=' . $v . '&';   //以key=value&key=value格式处理好数据
        }
        $str .= 'key=' . $appkey;  //最后加上签名
        return md5($str);
    }

    // 京东支付SDK
    public function doJdSdkRecharge($params)
    {
        // 生成预支付订单
        $postData = array(
            'trade_no'      =>  $params['trade_no'],
            'money'         =>  ParseUnit($params['money']),    // 按分处理
            'merId'         =>  $params['merId'],
            'configId'      =>  $params['configId'],
            'ip'            =>  $params['ip'],
            'uid'           =>  $params['uid'],
            'real_name'     =>  $params['real_name'],
            'id_card'       =>  $params['id_card'],
        );

        // 调用接口获取参数
        $postData['lib'] =  'jdSdk';
        $responeData = apiRequest('api/RechargeHandle', 'request' , $postData);

        if(!$responeData['code'])
        {
            $result = array(
                'status' => '0',
                'msg' => '支付系统维护中',
                'data' => ''
            );
            return $result;
        }

        $respone = $responeData['data'];
        if(empty($respone['orderId']) || empty($respone['appid']))
        {
            $result = array(
                    'status' => '0',
                    'msg' => '支付系统维护中',
                    'data' => ''
            );
            return $result;
        }

        // 组装数据
        $sdkData = array(
            'appid'         =>  $respone['appid'],
            'merchant'      =>  $respone['merchant'],
            'orderId'       =>  $respone['orderId'],
            'signData'      =>  $respone['signData'],
            'extraInfo'     =>  array(
                'trade_no'  =>  $respone['trade_no'],
            ),
        );

        // 加密
        $sdkJs = $this->strCode(json_encode($sdkData), 'ENCODE');

        // token验证
        $token = md5("{$sdkData['appid']}{$sdkData['merchant']}{$sdkData['orderId']}{$sdkData['signData']}{$this->salt}");

        $result = array(
            'status'    =>  '1',
            'msg'       =>  '创建订单成功',
            'data'      =>  $sdkJs,
            'token'     =>  $token
        );

        return $result;
    }

}
