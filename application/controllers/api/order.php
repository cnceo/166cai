<?php

/*
 * 订单信息处理 创建、付款通用接口
 * @date:2015-05-22
 */
class Order extends CI_Controller 
{
	// 订单提交信息
	private $orderInfo = array(
		'uid' => '',			// 用户信息为兼容APP
		'ctype' => '',
		'userName' => '',
		'buyPlatform' => '',
		'codes' => '',
		'lid' => '',
		'money' => '',
		'multi' => '',
		'issue' => '',
		'playType' => '',
		'isChase' => '',
		'betTnum' => '',
		'orderType' => '',
		'endTime' => ''
	);

	// 订单支付提交信息
	private $payInfo = array(
		'uid' => '',			// 用户信息为兼容APP
		'orderId' => '',
		'money' => '',
		'uid' => '',
		'userName' => ''
	);

    public function __construct() 
    {
        parent::__construct();
        $this->load->model('neworder_model');
        $this->load->model('chase_order_model');
    }

     /*
	 * 订单初始化及创建
	 * *********************
 	 * 接口说明：
 	 * 1.验证参数
 	 * 2.订单初始化 create_init 更新状态 status = 0
 	 * 3.订单创建 create 更新状态 status = 10
 	 * 4.返回创建成功或失败
 	 * *********************
	 * @date:2015-05-25
	 */
    public function createOrder()
    {
    	$params = $this->input->post();
        if ($params['orderType'] == 4)
        {
            $this->load->model('united_order_model');
            $res = $this->united_order_model->createUnitedOrder($params);
        }
        else
        {
            $res = $this->neworder_model->createOrder($params);
        }
        echo json_encode($res);
    }

    /*
	 * 追号订单初始化及创建
	 * @date:2016-03-03
	 */
    public function createChaseOrder()
    {
    	$params = $this->input->post();
    	$res = $this->chase_order_model->createChaseOrder($params);
    	echo json_encode($res);
    }

    /*
	 * 订单支付扣款
	 * *********************
 	 * 接口说明：
 	 * 1.创建订单支付流水
 	 * 2.完成扣款
 	 * 3.请求出票
 	 * 4.返回扣款成功或失败
 	 * *********************
	 * @date:2015-05-26
	 */
    public function doPay()
    {
    	$params = $this->input->post();
    	$res = $this->neworder_model->doPay($params);
    	echo json_encode($res);
    }

    public function doChasePay()
    {
    	$params = $this->input->post();
    	$res = $this->neworder_model->doChasePay($params);
    	echo json_encode($res);
    }
    
    public function cancelOrder()
    {
        $id = $this->input->get_post('id');
        $uid = $this->input->get_post('uid');
        $type = $this->input->get_post('type');
        $type = $type ? $type : 0;
        $uid = isset($uid) ? $uid : null;
        $this->load->model('united_order_model');
        $res = $this->united_order_model->cancelOrder($id, $type, true, $uid);
        if (!$res['status'])
        {
            die(json_encode(array('status' => 'fail', 'message' => $res['msg'])));
        }
        else
        {
            die(json_encode(array('status' => 'success', 'message' => '')));
        }
    }
    
    /**
     * 合买支付接口
     */
    public function doUnitedPay()
    {
    	$params = $this->input->post();
        $this->load->model('united_wallet_model');
    	if ($params['type'] == 1) {
            $response = $this->united_wallet_model->payBuyOrder($params['uid'], $params, ParseUnit($params['money']));
        } else {
            $response = $this->united_wallet_model->payUnitedOrder($params['uid'], $params, ParseUnit($params['money']));
        }

        if (!empty($response) && $response['code'] != 400) {
            $response = array(
                'code' => $response['code'],
                'msg' => ($response['code'] != 200) ? $response['msg'] : '<div class="mod-result result-success"><div class="mod-result-bd"><i class="icon-result"></i><div class="result-txt"><h2 class="result-txt-title">恭喜您，' . ($params['type'] == 1 ? '参与' : '发起') . '合买成功</h2><p>支付金额：<em class="main-color-s">' . $params['money'] . '元</em></p></div></div></div>',
                'data' => array('orderId' => $params['orderId'])
            );
        }
        if ($response['code'] == 400) {
            $money = $this->wallet_model->getMoney($this->uid);
            $response = array('code' => $response['code'], 'msg' => $response['msg'], 'data' => array('remain_money' => number_format(ParseUnit($money['money'], 1), 2)));
        }
        echo json_encode($response);
    }
    
    /**
     * 自动支付入口
     * @param type $tradeNo
     */
    public function autopay($tradeNo)
    {
        $this->load->model('wallet_model');
        $this->wallet_model->newAutopay($tradeNo);
    }
}
