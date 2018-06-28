<?php

if (!defined('BASEPATH')) exit('No direct script access allowed');

class DirectPay extends MY_Controller {

    /**
     * [__construct 收银台处理]
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
        $this->redirect('/weihu', 'location', 301);
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
    public function weixin($flag=0)
    {
        //账户是否注销
        $this->isLogout();
        //获取内容
        $data = $this->getCashierData(3);
        if($flag)
        {
            $data1 = getRechargeInfo('default',0,array('orderId'=>trim($this->input->get('orderId', true))));
            $data = array_merge($data,$data1);
        }
        $this->cashierView('wallet/cashier/weixin', $data, 'v1.1');
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
        $data = $this->getCashierData(4);
        if (ParseUnit($data['data']['money'] - $data['money'], 1) < 10 || ParseUnit($data['data']['money'] - $data['money'], 1) > 2000) {
            $REDIS = $this->config->item('REDIS');
            if (in_array($_SERVER['SERVER_ADDR'], array('120.132.33.194', '123.59.105.39'))) {
                $sorts = json_decode($this->cache->hGet($REDIS['CS_PC_PAY_CONFIG'], '1'), true);
            } else {
                $sorts = json_decode($this->cache->hGet($REDIS['PC_PAY_CONFIG'], '1'), true);
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
                return $this->$all[$sorts[1]](1);
            } else {
                return $this->bank();
            }
        }
        $this->cashierView('wallet/cashier/alipay', $data, 'v1.1');
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
        $data = $this->getCashierData(1);
        $this->cashierView('wallet/cashier/kuaijie', $data, 'v1.1');
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
        $data = $this->getCashierData(7);
        $this->cashierView('wallet/cashier/yinlian', $data, 'v1.1');
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
        $data = $this->getCashierData(8);
        $this->cashierView('wallet/cashier/jd', $data, 'v1.1');
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
        $data = $this->getCashierData(5);
        $this->cashierView('wallet/cashier/bank', $data, 'v1.1');
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
        $data = $this->getCashierData(6);
        $this->cashierView('wallet/cashier/credit', $data, 'v1.1');
    }

    /**
     * [getCashierData 获取收银台数据]
     * @author LiKangJian 2017-06-19
     * @return [type] [description]
     */
    private function getCashierData($ctype)
    {
        $vdata = array();
        $vdata['orderType'] = trim($this->input->get('orderType', true));
        $vdata['orderType'] = $vdata['orderType'] ? $vdata['orderType'] : 0;
        // 账户是否注销
        $this->isLogout();
        $vdata['orderId'] = trim($this->input->get('orderId', true));
        if (!empty($vdata['orderId'])) 
        {
            $this->load->library('BetCnName');
            // 根据订单来源区分收银台
            if (empty($vdata['orderType'])) 
            {
                $this->load->model('order_model');
                $orders = $this->order_model->getOrder(array('uid' => $this->uid, 'orderId' => $vdata['orderId']));
                if($orders['data']['status'] != '10')
                {
                    $this->redirect('/mylottery/detail');
                }
                $vdata['betRedpack'] = intval($this->input->get('betRedpack', true));
                if(isset($vdata['betRedpack']))
                {
                    $res = $this->wallet_model->checkBetRedpack($this->uid, $vdata['orderId'], $vdata['betRedpack']);
                    if(!$res)
                    {
                        $this->redirect('/mylottery/detail');
                    }
                    $orders['data']['money'] = $res['data']['cost'];
                }
                $userMoney = $this->wallet_model->getMoney($this->uid);
                if($orders['data']['money'] <= $userMoney['money'])
                {
                    $this->redirect('/mylottery/betlog');
                }
            }
            elseif($vdata['orderType'] == 1)
            {
                $this->load->model('chase_order_model', 'chase_model');
                $orderInfo = $this->chase_model->getChaseInfoById(array('uid' => $this->uid, 'chaseId' => $vdata['orderId']));
                $orderStatus = $this->chase_model->getStatus();
                if ($orderInfo['info']['status'] != $orderStatus['create'])
                {
                    // 账户明细
                    $this->redirect('/mylottery/detail');
                }
                $userMoney = $this->wallet_model->getMoney($this->uid);
                if ($orderInfo['info']['money'] <= $userMoney['money']) 
                {
                    $this->redirect('/mylottery/chaselog');
                }
                // 订单格式处理
                $orders['data'] = $orderInfo['info'];
                $orders['data']['orderId'] = $orderInfo['info']['chaseId'];
                $orders['data']['issue'] = $orderInfo['detail'][0]['issue'];
            }
            elseif($vdata['orderType'] == 5)
            {
                $this->load->model('follow_order_model');
                $orderInfo = $this->follow_order_model->getFollowOrderDetail($vdata['orderId']);
                $orderInfo['money'] = $orderInfo['totalMoney'];
                if ($orderInfo['status'] != 0)
                {
                    // 账户明细
                    $this->redirect('/mylottery/detail');
                }
                $userMoney = $this->wallet_model->getMoney($this->uid);
                if ($orderInfo['money'] <= $userMoney['money']) 
                {
                    $this->redirect('/mylottery/gendanlog');
                }
                // 订单格式处理
                $orders['data'] = $orderInfo;
                $orders['data']['orderId'] = $orderInfo['followId'];
            }               
            else
            {
                $this->load->model('united_order_model', 'united_model');
                $orderInfo = $this->united_model->getUniteOrderByOrderId($vdata['orderId'], 'status, (buyMoney+guaranteeAmount) as money, issue, lid, created');
                $orderStatus = $this->united_model->getStatus();
                if (!in_array($orderInfo['status'], array($orderStatus['create'], $orderStatus['pay'], $orderStatus['drawing'], $orderStatus['draw'])))
                {
                    // 账户明细
                    $this->redirect('/mylottery/detail');
                }
                $userMoney = $this->wallet_model->getMoney($this->uid);
                if ($orderInfo['money'] <= $userMoney['money'])
                {
                    $this->redirect('/mylottery/betlog');
                }
                // 订单格式处理
                $orders['data'] = $orderInfo;
                $orders['data']['orderId'] = $vdata['orderId'];
                $orders['data']['issue'] = $orderInfo['issue'];
            }

            $vdata = array_merge($vdata, $orders);
            $wallet = $this->wallet_model->getMoney($this->uid);
            $vdata['money'] = $wallet['money'];
        } 
        else 
        {
            // 无订单号
            $this->redirect('/mylottery/detail');
        }
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
            array_multisort($sortArry, SORT_ASC, $redpackData);
        }
        // 充值差额
        $blanceMoney = $vdata['data']['money'] - $vdata['money'];
        $redpackList = array();
        if(!empty($redpackData))
        {
            // 筛选小于当前充值余额的红包
            foreach ($redpackData as $redpack)
            {
                $use_params = json_decode($redpack['use_params'], true);
                if($use_params['money_bar'] <= $blanceMoney)
                {
                    array_push($redpackList, $redpack);
                }
            }
        }
        $vdata['redpackData'] = $redpackList;
        //为了加载不同的CSS
        $vdata['htype'] = 1;
        $vdata['isNeedShowBindId'] = true;
        $vdata['showBind'] = $this->checkRealName($this->uid) ? 0 : 1;
        $vdata['rfshbind'] = 1;
        $payCache = $this->getPayCache();
        $vdata = array_merge($vdata,$this->getPayBaseInfo($payCache,$ctype,0,array('orderId'=>$vdata['orderId'],'orderType'=>$vdata['orderType'])));
        return $vdata;
    }


}