<?php

if (!defined('BASEPATH')) exit('No direct script access allowed');
class Recharge extends MY_Controller {

    /**
     * [__construct 充值 1_3微信,1_4支付宝,1_1快捷, 1_5网银,1_6信用卡]
     * @author LiKangJian 2017-06-19
     */
    public function __construct() 
    {
        parent::__construct();
        $this->load->model('wallet_model');
        $this->load->model('Notice_Model');
        $this->load->model('pay_model');
        $this->config->load('pay');
    }
	public function index()
    {
        $this->redirect('/notice/detail/6614', 'location', 301);
        $REDIS = $this->config->item('REDIS');
        if(in_array($_SERVER['SERVER_ADDR'],array('120.132.33.194','123.59.105.39'))){
            $sorts = json_decode($this->cache->hGet($REDIS['CS_PC_PAY_CONFIG'],'1'),true);
        }else{
            $sorts = json_decode($this->cache->hGet($REDIS['PC_PAY_CONFIG'],'1'),true);
        }
        if (!empty($sorts)) {
            $all = array(
                '1_1' => 'kuaijie',
                '1_3' => 'weixin',
                '1_4' => 'alipay',
                '1_5' => 'bank',
                '1_6' => 'credit',
                '1_7' => 'yinlian',
                '1_8' => 'jd',
            );
            $this->$all[$sorts[0]](1);
        } else {
            $this->alipay();
        }
    }
    /**
     * [weixin 微信支付]
     * @author LiKangJian 2017-06-20
     * @return [type] [description]
     */
    public function weixin($flag=0)
    {
        //账户是否注销
        $this->isLogout();
        //获取内容
        $data = $this->getRechargeData(3);
        if($flag)
        {
            $data1 = getRechargeInfo('default');
            $data = array_merge($data,$data1);
        }
        $this->rechageView('wallet/recharge/weixin', $data, 'v1.1');
    }
    /**
     * [alipay 支付宝支付]
     * @author LiKangJian 2017-06-15
     * @return [type] [description]
     */
    public function alipay()
    {
        //账户是否注销
        $this->isLogout();
        //获取内容
        $data = $this->getRechargeData(4);
        $this->rechageView('wallet/recharge/alipay', $data, 'v1.1');
    }
    /**
     * [kuaijie 快捷支付]
     * @author LiKangJian 2017-06-15
     * @return [type] [description]
     */
    public function kuaijie()
    {
        //账户是否注销
        $this->isLogout();
        //获取内容
        $data = $this->getRechargeData(1);
        $this->rechageView('wallet/recharge/kuaijie', $data, 'v1.1');
    }
    /**
     * [银联云支付]
     * @return [type] [description]
     */
    public function yinlian()
    {
        //账户是否注销
        $this->isLogout();
        //获取内容
        $data = $this->getRechargeData(7);
        $this->rechageView('wallet/recharge/yinlian', $data, 'v1.1');
    }
    /**
     * [bank 网上银行]
     * @author LiKangJian 2017-06-15
     * @return [type] [description]
     */
    public function bank()
    {
        $this->redirect('/weihu', 'location', 301);
        //账户是否注销
        $this->isLogout();
        //获取内容
        $data = $this->getRechargeData(5);
        $this->rechageView('wallet/recharge/bank', $data, 'v1.1');
    }
    /**
     * [credit 信用卡]
     * @author LiKangJian 2017-06-15
     * @return [type] [description]
     */
    public function credit()
    {
        $this->redirect('/weihu', 'location', 301);
        //账户是否注销
        $this->isLogout();
        //获取内容
        $data = $this->getRechargeData(6);
        $this->rechageView('wallet/recharge/credit', $data, 'v1.1');
    }
    /**
     * [京东]
     * @return [type] [description]
     */
    public function jd()
    {
        //账户是否注销
        $this->isLogout();
        //获取内容
        $data = $this->getRechargeData(8);
        $this->rechageView('wallet/recharge/jd', $data, 'v1.1');
    }
    /**
     * [getRechargeData 获取充值页面相关信息]
     * @author LiKangJian 2017-06-20
     * @param  [type] $ctype [description]
     * @return [type]        [description]
     */
    private function getRechargeData($ctype)
    {
        // 查询充值红包信息
        $this->load->model('red_pack_model');
        list($status, $msg, $redpackData) = $this->red_pack_model->fetch($this->uid, 3, 0, 0);
        // 按充值金额排序
        if(!empty($redpackData))
        {
            $sortArry = array();
            foreach ($redpackData as $key => $items) 
            {
                if(!empty($items['ismobile_used']))
                {
                    //排除客户端专享红包展示
                    unset($redpackData[$key]);
                    continue;
                }
                $sortArry[] = $items['money'];
            }
            if($sortArry)
            {
                array_multisort($sortArry, SORT_ASC, $redpackData);
            }
        }
        $data = array(
            'redpackData' => $redpackData,
        );
        $data['rfshbind'] = 1;
        $data['htype'] = 1;
        $data['showBind'] = $this->checkRealName($this->uid) ? 0 : 1;
        $payCache = $this->getPayCache();
        $data = array_merge($data,$this->getPayBaseInfo($payCache,$ctype));
        return $data;
    }
    /**
     * [getPayExtInfo 获取信息]
     * @author LiKangJian 2017-06-16
     * @param  [type] $mode [description]
     * @return [type]       [description]
     */
    private function getPayExtInfo($mode)
    {
        $cache = $this->getPayCache();
        foreach ($cache as $k => $v) 
        {
            foreach ($v as $k1 => $v1) 
            {
                if($v1['mode'] == $mode)
                {
                    return $v1;
                }
            }
        }
    }
    /**
     * [processRecharge 充值过程]
     * @author LiKangJian 2017-06-15
     * @return [type] [description]
     */
    public function processRecharge()
    {
        if(empty($this->uid)) $this->redirect('/main/login');
        $this->load->view('v1.1/wallet/recharge/pay_loading',array());
        if($this->isFreeze())
        {
            die('您的账户已被冻结，如需解冻请联系客服。');
        }
        //验证是否实名
        if(!$this->checkRealName($this->uid))
        {
            die('充值前请进行实名认证。');
        }
        $trade_no = $this->tools->getIncNum('UNIQUE_KEY');
        $money = $this->input->get_post('p3_Amt', true);
        $orderId = $this->input->get_post('orderId', true);
        $orderType = $this->input->get_post('orderType', true);
        $redpack = $this->input->get_post('redpack', true);
        $mode = $this->input->get_post('mode', true);
        $pd_FrpId = $this->input->get_post('pd_FrpId', true);
        $checkRedPack = $this->checkRedPack($redpack, $money);
        if($checkRedPack['status'] == false)
        {
            echo $checkRedPack['msg']; die();
        }
        $ext =  $this->getPayExtInfo($mode);
        // 微信支付宝重新分配支付渠道处理
        if(in_array($ext['ctype'], array(2, 3, 4)))
        {
            $ext = $this->exchangeExtInfo($ext['ctype']);
            $mode = $ext['mode'];
        }
        if(!isset($ext['configId']) || $money <= 0)
        {
            echo '操作失败'; die();
        }
        if($ext['ctype']==4  && intval($money) >2000)
        {
            echo '操作失败,支付宝充值限额2000元'; die();
        }

        $params = array(
            'trade_no'  =>  $trade_no,
            'money'     =>  $money*100,
            'lib'       =>  $ext['lib'],
            'mode'      =>  $mode,
            'configId'  =>  $ext['configId'],
            'uid'       =>  $this->uid,
            'real_name' =>  $this->uinfo['real_name'],
            'id_card'   =>  $this->uinfo['id_card'],
            'pd_FrpId'  =>  $pd_FrpId,
            'ip'        =>  UCIP,
            'bank_id'   =>  '',
            'pay_agreement_id'  => '',
            'submit_url' => $ext['submit_url'],//表达提交地址
        );
        $res = $this->doRecharge($params);
        if(empty($res))
        {
            echo '操作失败'; die();
        }
        // 记录 cp_wallet_logs
        if ($orderType == 5) {
            $orderType = 4;
        } else {
            $orderType = ($orderType ? str_replace('4', '3', $orderType) : 0);
        }
        $walletData = array('uid' => $this->uid, 'trade_no' => $trade_no, 'orderId' => $orderId, 'additions' => $mode, 'mark' => '2', 'money' => ParseUnit($money), 'status' => $orderType, 'red_pack' => $checkRedPack['redpackList'] ? $checkRedPack['redpackList'] : '', 'channel' => $this->getChannelId());
        if($params['lib'] == 'XmPay')
        {
            $walletData['content'] = $res['data']['params']['token'];
        }
        $walletRes = $this->wallet_model->SaveOrder($walletData);
        // 记录 cp_pay_logs
        $payLog = array('trade_no' => $trade_no, 'money' => ParseUnit($money), 'pay_time' => date('Y-m-d H:i:s', time()), 'pay_type' => $ext['pay_type'],'rcg_group'=>$ext['mode_str']=='1_6'?'1_5':$ext['mode_str'],'rcg_serial'=>$ext['configId']);
        $payRes = $this->pay_model->recordPayLog($payLog);
        if(!$walletRes || !$payRes)
        {
            echo "交易流水记录失败";die;
        }
        echo $res['data']['html'];
        
    }
    /**
     * [doRecharge 充值统一处理入口]
     * @author LiKangJian 2017-06-16
     * @param  [type] $params [description]
     * @return [type]         [description]
     */
    public function doRecharge($params)
    {
        $params = array(
            'trade_no'  =>  $params['trade_no'],
            'money'     =>  $params['money'],
            'configId'  =>  $params['configId'],
            'lib'       =>  $params['lib'],
            'mode'      =>  $params['mode'],
            'uid'       =>  $this->uid,
            'real_name' =>  $this->uinfo['real_name'],
            'id_card'   =>  $params['id_card'],
            'pd_FrpId'  =>  $params['pd_FrpId'],
            'ip'        =>  $params['ip'],
            'bank_id'   =>  $params['bank_id'],
            'pay_agreement_id'  => $params['pay_agreement_id'],
            'submit_url' => $params['submit_url'],//表达提交地址
        );
        $respone = array(
            'code' => false,
            'msg'  => '请求充值类型错误',
            'data' => $params,
        );
        if($params['lib'] && !empty($params) && !empty($params['configId']))
        {
            require_once APPPATH . '/libraries/recharge/' . $params['lib'] . '.php';
            if(class_exists($params['lib']))
            {
                $configData = $this->pay_model->getPayConfig($params['configId']);
                $config = array();
                if(!empty($configData['extra']))
                {
                    $config = json_decode($configData['extra'], true);
                }
                $paySubmit = new $params['lib']($config);
                $respone = $paySubmit->formSubmit($params);
                return $respone;
            }
        }
        return $respone;
    }

    /**
     * [getWinxin 微信页面扫码页面]
     * @author LiKangJian 2017-06-19
     * @return [type] [description]
     */
    public function getWinxin()
    {
        $vdata = $this->input->post(null, true);
        $this->load->view('v1.1/wallet/weixinpay', array('params' => $vdata));
    }
    /**
     * [getwftZfb 支付宝扫码页面]
     * @author LiKangJian 2017-06-19
     * @return [type] [description]
     */
    public function getwftZfb()
    {
        $vdata = $this->input->post(null, true);
        $this->load->view('v1.1/wallet/wftZfbPay', array('params' => $vdata));
    }
    /**
     * [getWzZfb 微众银行支付宝扫码页面]
     * @author LiKangJian 2017-10-23
     * @return [type] [description]
     */
    public function getWzZfb()
    {
        $vdata = $this->input->post(null, true);
        $this->load->view('v1.1/wallet/wftZfbPay', array('params' => $vdata));
    }
    public function getPaZfb()
    {
        $vdata = $this->input->post(null, true);
        $this->load->view('v1.1/wallet/wftZfbPay', array('params' => $vdata));
    }
    
    /**
     * [getXmZfb 厦门银行支付宝扫码页面]
     * @return [type] [description]
     */
    public function getXmZfb()
    {
        $vdata = $this->input->post(null, true);
        $this->load->view('v1.1/wallet/wftZfbPay', array('params' => $vdata));
    }
    
    /**
     * [银联云支付]
     * @return [type] [description]
     */
    public function getYlyZf()
    {
        $vdata = $this->input->post(null, true);
        $this->load->view('v1.1/wallet/yinlianPay', array('params' => $vdata));
    }

    /**
     * [盈中平安]
     * @return [type] [description]
     */
    public function getYzZfb()
    {
        $vdata = $this->input->post(null, true);
        $vdata['codeUrl'] = $_POST['codeUrl'];
        $vdata['codeUrl'] = base64_decode(urldecode($vdata['codeUrl']));
        $this->load->view('v1.1/wallet/yzPay', array('params' => $vdata));
    }
    
    /**
     * [checkRedPack 检查充值是否符合使用红包]
     * @author LiKangJian 2017-06-20
     * @param  [type] $redpack [description]
     * @param  [type] $money   [description]
     * @return [type]          [description]
     */
    private function checkRedPack($redpack, $money)
    {
        $result = array(
            'status' => true,
            'msg'   => '',
            'redpackList' => ''
        );
        
        // 红包使用条件检查
        if(!empty($redpack))
        {
            $redpacks = array();
            $redpackArry = explode(',', $redpack);
            // 查询红包信息
            $this->load->model('red_pack_model');
            list($status, $msg, $redpackData) = $this->red_pack_model->fetch($this->uid, 3, 0, 0);
            $redpackInfo = array();
            if(!empty($redpackData))
            {
                foreach ($redpackData as $redpack) 
                {
                    //排除客户端专享红包
                    if(!empty($redpack['ismobile_used']))
                    {
                        continue;
                    }
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
                        $result['status'] = false;
                        $result['msg'] = '当前红包不可用';
                        return $result;
                    }
                }   

                // 总金额检查
                if($checkMoney > $money)
                {
                    $result['status'] = false;
                    $result['msg'] = '当前红包不满足使用条件';
                    return $result;
                }           
            }
            else
            {
                $result['status'] = false;
                $result['msg'] = '当前红包不可用';
                return $result;
            }

            // 判断是否存在重复红包
            if(count($redpacks) != count(array_unique($redpacks)))
            {
                $result['status'] = false;
                $result['msg'] = '当前红包不可用';
                return $result;
            }

            $redpackList = implode(',', $redpacks);
            $result['redpackList'] = $redpackList;
        }
        return $result;
    }

    // 微信支付宝按比例随机
    public function exchangeExtInfo($ctype)
    {
        $info = array();
        $params = array(
            'platform'  =>  '1',
            'ctype'     =>  $ctype,
            'isDev'     =>  (in_array($_SERVER['SERVER_ADDR'], array('120.132.33.194', '123.59.105.39'))) ? '1' : '0',
        );
        $dispatch = $this->pay_model->getRateDetail($params);
        if(!empty($dispatch))
        {
            $params = json_decode($dispatch['params'], true);
            $info = array(
                'lib'           =>  $params['lib'],
                'ctype'         =>  $params['ctype'],
                'submit_url'    =>  $params['submit_url'],
                'mode'          =>  $params['mode'],
                'mode_str'      =>  $params['mode_str'],
                'img_src'       =>  $params['img_src'],
                'img_alt'       =>  $params['img_alt'],
                'img_w'         =>  $params['img_w'],
                'img_h'         =>  $params['img_h'],
                'pay_type'      =>  $dispatch['pay_type'],
                'configId'      =>  $dispatch['id'],
                'weight'        =>  $dispatch['weight'],
            );
        }
        return $info;
    }
}