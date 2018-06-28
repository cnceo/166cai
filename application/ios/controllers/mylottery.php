<?php
defined('BASEPATH') OR die('No direct script access allowed');

class Mylottery extends MY_Controller
{
	//订单tab类型
	public $betType = array(
			'1' => '',		//全部
			'2' => '2000',	//已中奖
			'3' => '500'	//待开奖
	);
    public function __construct()
    {
        parent::__construct();
        $this->load->model('wallet_model', 'Wallet');
        $this->load->library('BetCnName');
    }

    /*
      * 账户明细 -- 首页 充值、购彩、提现等现金流记录
      * @date:2015-05-13
      */
    public function detail($token)
    {
        $this->checkUserAgent();
        list($cons, $page, $pageSize) = $this->parseToken($token);
        $cons['ctype'] = 'all';
        $details = $this->Wallet->getTradeDetail($cons, $page, $pageSize);
        $info = $this->getInfoList($details);
        $info['title'] = '账户明细';
        $info['token'] = $token;
        $info['ctype'] = $cons['ctype'];
        $this->load->view('mylottery/detail', $info);
    }

    private function parseToken($token)
    {
        $token = urldecode($token);
        $data = $this->strCode($token);
        $data = json_decode($data, TRUE);
        if (empty($data['uid']))
        {
            die("参数错误！");
        }
        $cons['uid'] = $data['uid'];
        $page = ( ! empty($data['cpage']) && is_int($data['cpage']) && $data['cpage'] > 0) ? $data['cpage'] : 1;
        $pageSize = ( ! empty($data['psize']) && is_int($data['psize']) && $data['psize'] > 0) ? $data['psize'] : 15;

        return array($cons, $page, $pageSize);
    }

    private function getInfoList($details)
    {
        // if (empty($details['total']))
        // {
        //     die("没有交易记录！");
        // }
        $info['income'] = $details['income'];
        if (empty($details['datas']))
        {
            $info['orders'] = array();
        }
        else
        {
            foreach ($details['datas'] as & $order)
            {
                $balance = $order['income'] - $order['expend'];
                $sign = ($balance > 0) ? '+' : '';
                $order['balance'] = $sign . number_format(ParseUnit($balance, 1), 2);

                if ($order['ctype'] == 1)
                {
                    $token = $this->strCode(json_encode(array(
                        'uid' => $order['uid'],
                    )), 'ENCODE');
                    $order['tradeDetailUrl'] = $this->config->item('pages_url') . 'app/order/detail/' . $order['orderId']
                        . '/' . urlencode($token);
                }
                else
                {
                    $tradeToken = $this->strCode(json_encode(array(
                        'uid'     => $order['uid'],
                        'tradeNo' => $order['trade_no'],
                    )), 'ENCODE');
                    $order['tradeDetailUrl'] = $this->config->item('pages_url') . 'app/trade/detail/' . urlencode($tradeToken);
                }
            }
            $info['orders'] = $details['datas'];
        }

        return $info;
    }

    /*
      * 账户明细 -- 购彩记录
      * @date:2015-05-13
      */
    public function betlog($token)
    {
        $this->checkUserAgent();
        list($cons, $page, $pageSize) = $this->parseToken($token);
        $cons['ctype'] = $this->Wallet->ctype['pay'];
        $details = $this->Wallet->getTradeDetail($cons, $page, $pageSize);
        $info = $this->getInfoList($details);
        $info['title'] = '购彩记录';
        $info['token'] = $token;
        $info['ctype'] = $cons['ctype'];
        $this->load->view('mylottery/detail', $info);
    }

    /*
      * 账户明细 -- 充值记录
      * @date:2015-05-13
      */
    public function recharge($token)
    {
        $this->checkUserAgent();
        list($cons, $page, $pageSize) = $this->parseToken($token);
        $cons['ctype'] = $this->Wallet->ctype['recharge'];
        $details = $this->Wallet->getTradeDetail($cons, $page, $pageSize);
        $info = $this->getInfoList($details);
        $info['title'] = '充值记录';
        $info['token'] = $token;
        $info['ctype'] = $cons['ctype'];
        $this->load->view('mylottery/detail', $info);
    }

    /*
      * 账户明细 -- 中奖记录
      * @date:2015-05-15
      */
    public function reward($token)
    {
        $this->checkUserAgent();
        list($cons, $page, $pageSize) = $this->parseToken($token);
        $cons['ctype'] = $this->Wallet->ctype['reward'];
        $details = $this->Wallet->getTradeDetail($cons, $page, $pageSize);
        $info = $this->getInfoList($details);
        $info['title'] = '中奖记录';
        $info['token'] = $token;
        $info['ctype'] = $cons['ctype'];
        $this->load->view('mylottery/detail', $info);
    }

    /*
      * 账户明细 -- 提现记录
      * @date:2015-05-15
      */
    public function withdraw($token)
    {
        $this->checkUserAgent();
        list($cons, $page, $pageSize) = $this->parseToken($token);
        $cons['ctype'] = 'withdraw';
        $details = $this->Wallet->getTradeDetail($cons, $page, $pageSize);
        $info = $this->getInfoList($details);
        $info['title'] = '提现记录';
        $info['token'] = $token;
        $info['ctype'] = $cons['ctype'];
        $this->load->view('mylottery/detail', $info);
    }

    /*
     * 账户明细 -- 红包记录
     * @date:2015-05-15
     */
    public function bonus($token)
    {
        $this->checkUserAgent();
        list($cons, $page, $pageSize) = $this->parseToken($token);
        //预留字段，现在还没有红包的记录
        $cons['ctype'] = $this->Wallet->ctype['bonus'];
        $details = $this->Wallet->getTradeDetail($cons, $page, $pageSize);
        $info = $this->getInfoList($details);
        $info['title'] = '红包记录';
        $info['token'] = $token;
        $info['ctype'] = $cons['ctype'];
        $this->load->view('mylottery/detail', $info);
    }

    /*
     * 账户明细 -- AJAX
     * @date:2015-05-15
     */
    public function ajax_detail()
    {
        $token = $this->input->post('token', true);
        $ctype = $this->input->post('ctype', true);
        list($cons, $page, $pageSize) = $this->parseToken($token);
        $cons['ctype'] = $ctype;
        $page = $this->input->post('page', true);
        $details = $this->Wallet->getTradeDetail($cons, $page, $pageSize);

        if($details['datas'])
        {
            $info = $this->getInfoList($details);
            $result = array(
                'status' => '1',
                'msg' => '加载中',
                'data' => $this->load->view('mylottery/ajaxDetail', $info, true)
            );
        }
        else
        {
            $result = array(
                'status' => '0',
                'msg' => '已加载所有信息',
                'data' => ''
            );
        }
        die(json_encode($result));
    }
    
    //订单列表
    public function betlist($strCode = '')
    {
    	$data = $this->strCode(urldecode($strCode));
    	$data = json_decode($data, true);
    	$uid = isset($data['uid']) ? $data['uid'] : '';
    	$this->load->model('lottery_model');
    	$this->load->model('order_model');
    	if (empty($uid))
    	{
    		die("参数错误！");
    	}
    	// 查询条件
    	$cons['uid'] = $uid;
    	$odatas = $this->order_model->getOrders($cons, 1, 15);
    	$vdata = array(
            'title' => '订单列表',
    		'orders' => $odatas['datas'],
    		'strCode' => $this->strCode(json_encode(array('uid'=> $uid)), 'ENCODE'),
    	);
    	$this->load->view('mylottery/betlist', $vdata);
    }
    
    //订单列表ajax请求
    public function ajax_betlist()
    {
    	$data = $this->strCode($this->input->post('strCode', true));
    	$data = json_decode($data, true);
    	$uid = isset($data['uid']) ? $data['uid'] : '';
    	$cpage = $this->input->post('cpage', true);
    	$cpage = max(1, $cpage);
    	$status = $this->betType[$this->input->post('type', true)];
    	$psize = 15;
    	$this->load->model('lottery_model');
    	$this->load->model('order_model');
    	// 查询条件
    	$cons['uid'] = $uid;
    	if($status){
    		$cons['status'] = $status;
    	}
    	$odatas = $this->order_model->getOrders($cons, $cpage, $psize);
    	$vdata = array(
    			'orders' => $odatas['datas'],
    			'strCode' => $this->strCode(json_encode(array('uid'=> $uid)), 'ENCODE'),
    	);
    	echo $this->load->view('mylottery/ajaxbetlist', $vdata, true);
    }

    /*
     * APP wap网关充值记录 同步回调 充值成功地址
     * @version:V1.6
     * @date:2015-12-23
     */
    public function rechargeSucc()
    {
        $data = $this->input->get(NULL, true);
        $this->rechargeDetail($data['orderNo'], TRUE);
    }

    /*
     * APP wap网关充值记录 同步回调 充值失败地址
     * @version:V1.6
     * @date:2015-12-23
     */
    public function rechargeFail()
    {
        $data = $this->input->get(NULL, true);
        $this->rechargeDetail($data['orderNo'], FALSE);
    }

    /*
     * APP wap网关充值记录 同步回调地址
     * @version:V1.6
     * @date:2015-12-23
     */
    private function rechargeDetail($orderNo, $status)
    {
        if(!empty($orderNo))
        {
            // 查询流水信息
            $rechargeInfo = $this->Wallet->getRechargeLog($orderNo);

            $detail = array();
            if(!empty($rechargeInfo))
            {
                // 提取组装用户信息

                $this->config->load('pay');
                $payAllCfg = $this->config->item('pay_all_cfg');
                $tmpAry = explode('@', $rechargeInfo['additions']);

                $detail = array(
                    'money' => ParseUnit($rechargeInfo['money'],1),
                    'payType' => $payAllCfg['pay_cfg'][$tmpAry[0]]['name'],
                    'redirectPage' => 'recharge'
                );

                if(!empty($rechargeInfo['orderId']))
                {
                    // 订单信息加密
                    $orderDetail = $this->strCode(json_encode(array(
                        'uid' => $rechargeInfo['uid'],
                        'orderId' => $rechargeInfo['orderId'],
                    )), 'ENCODE');

                    // 跳转支付页面
                    $detail['redirectPage'] = 'order';
                    $detail['payView'] = $this->config->item('pages_url') . "app/order/doPay/" . urlencode($orderDetail);
                }              
            }

            $detail['status'] = $status;
            $this->load->view('wallet/result', $detail); 
        }
        else
        {
            die('订单参数缺失');
        }
    }
}