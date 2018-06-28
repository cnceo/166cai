<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/*
 * APP 内页请求
 * @date:2015-04-21
 */
class Order extends MY_Controller 
{
	public function __construct() 
	{
		parent::__construct();
		$this->load->library('tools');
		$this->load->library('comm');
		$this->load->model('order_model','Order');
		$this->order_status = $this->orderConfig('orders');
	}

	/*
 	 * 订单支付
 	 * @date:2015-05-11
 	 * *********************
 	 * 函数说明：
 	 * 1.验证订单参数
 	 * 2.根据订单状态跳转支付、充值、订单详情页面
 	 * *********************
 	 */
	public function doPay($token)
	{
		$this->checkUserAgent();
		$data = $this->strCode(urldecode($token));
		$data = json_decode($data, true);
		// 获取版本信息
    		$versionInfo = $this->version;

		if( isset($data['uid']) && isset($data['orderId']) && !empty($data['uid']) && !empty($data['orderId']) )
		{
			$this->load->library('BetCnName');
			// 追号订单
			if ($data['orderType'] == '4') 
			{
				$this->load->model('united_order_model', 'united_model');
				$orderInfo = $this->united_model->getUniteOrderByOrderId($data['orderId'], 'status, buyMoney, guaranteeAmount, issue, lid, created');
				$orderInfo['uid'] = $data['uid'];
				$orderInfo['orderId'] = $data['orderId'];
				$orderInfo['orderType'] = 4;
				$orderInfo['ctype'] = $data['ctype'];
				if ($data['ctype'] == 1) 
				{
					$orderInfo['pay_money'] = $data['buyMoney'] * 100;
				}
				else 
				{
					$orderInfo['pay_money'] = $orderInfo['buyMoney'] + $orderInfo['guaranteeAmount'];
				}
				
				$orderStatus = $this->united_model->getStatus();
				
				if (!in_array($orderInfo['status'], array($orderStatus['create'], $orderStatus['pay'], $orderStatus['drawing'], $orderStatus['draw'])))
				{
					$codeStr = $this->strCode(json_encode(array('uid' => $data['uid'])), 'ENCODE');
					header('Location: ' . $this->config->item('pages_url') . 'ios/hemai/detail/hm'. $data['orderId'] . '/'. urlencode($codeStr));
				}
				
				$userMoney = $this->Order->getMoney($data['uid']);
				$orderInfo['account_money'] = number_format(ParseUnit($userMoney['money'], 1), 2);
				$orderInfo['lname'] = BetCnName::$BetCnName[$orderInfo['lid']];
				if ($orderInfo['pay_money'] <= $userMoney['money'])
				{
					$orderInfo['token'] = $token;
					$data['payMoney'] = $orderInfo['pay_money'];
					// 区分版本走内页还是safari
					if($versionInfo['appVersionCode'] >= 2060001)
					{
						$orderInfo['payToken'] = $this->getPayToken($data);
						$this->load->view('order/pay_inner', $orderInfo);
					}
					else
					{
						$orderInfo['payUrl'] = $this->getPayUrl($data);
						$this->load->view('order/pay', $orderInfo);
					}					
				}
				else 
				{
					$orderInfo['token'] = $token;
					$orderInfo['balance_money'] = number_format(ParseUnit($orderInfo['pay_money']-$userMoney['money'], 1), 2);
					$orderInfo['recharge_money'] = ParseUnit($orderInfo['pay_money']-$userMoney['money'], 1);
					$orderInfo['rechargeUrl'] = $this->config->item('pages_url') . 'ios/wallet/recharge/' . $token . '/' . $orderInfo['recharge_money'];
					$this->load->view('order/recharge', $orderInfo);
				}
			}
                        elseif($data['orderType'] == '5'){
                            $this->load->model('follow_order_model');
                            $followOrder = $this->follow_order_model->getFollowOrderDetail($data['orderId']);
                            $orderInfo = array();
                            $orderInfo['uid'] = $data['uid'];
                            $orderInfo['orderId'] = $data['orderId'];
                            $orderInfo['orderType'] = 5;
                            $orderInfo['appVersion'] = $this->getUserAgentInfo();
                            $userMoney = $this->Order->getMoney($data['uid']);
                            $orderInfo['account_money'] = number_format(ParseUnit($userMoney['money'], 1), 2);
                            $orderInfo['lname'] = BetCnName::$BetCnName[$followOrder['lid']];
                            $orderInfo['totalMoney'] = $followOrder['totalMoney'];
                            if ($orderInfo['totalMoney'] <= $userMoney['money'])
                            {
                                $orderInfo['token'] = $token;
                                // 区分版本走内页还是safari
                                if($versionInfo['appVersionCode'] >= 2060001)
                                {
                                    $orderInfo['payToken'] = $this->getPayToken($data);
                                    $this->load->view('order/pay_inner', $orderInfo);
                                }
                                else
                                {
                                    $orderInfo['payUrl'] = $this->getPayUrl($data);
                                    $this->load->view('order/pay', $orderInfo);
                                }
                            }
                            else 
                            {
                                $orderInfo['token'] = $token;
                                $orderInfo['balance_money'] = number_format(ParseUnit($orderInfo['totalMoney']-$userMoney['money'], 1), 2);
                                $orderInfo['recharge_money'] = ParseUnit($orderInfo['totalMoney']-$userMoney['money'], 1);
                                $orderInfo['rechargeUrl'] = $this->config->item('pages_url') . 'ios/wallet/recharge/' . $token . '/' . $orderInfo['recharge_money'];
                                $orderInfo['versionInfo'] = $this->getUserAgentInfo();
                                $this->load->view('order/recharge', $orderInfo);
                            }
                        }
			elseif($data['orderType'] == '1')
			{
				// 获取订单信息
				$this->load->model('chase_order_model');
				$chaseData = array(
					'uid' => $data['uid'],
					'chaseId' => $data['orderId']
				);

				$order = $this->chase_order_model->getChaseInfoById($chaseData);
				$orderStatus = $this->chase_order_model->getStatus();
				
				if(!empty($order))
				{
					if( $order['info']['status'] == $orderStatus['create'] )
					{
						// 待付款 创建订单成功之后判断余额
						$money = $this->Order->getMoney($data['uid']);

						//组装数据
						$orderData = array();
						$orderData['lname'] =  BetCnName::$BetCnName[$order['info']['lid']];
						$orderData['lid'] = $order['info']['lid'];
						$orderData['issue'] = $order['detail'][0]['issue'];
						$orderData['pay_money'] = number_format($order['info']['money']/100, 2);
						$orderData['account_money'] = number_format(ParseUnit($money['money'], 1), 2);
						$orderData['orderType'] = $data['orderType'];

						if( $order['info']['money'] > $money['money'] )
						{
							// 查询红包信息
					        $this->load->model('redpack_model');
					        $this->eventType = $this->redpack_model->getEventType();
					        $redpackData = $this->redpack_model->getRedpackInfo($data['uid'], $this->eventType['recharge'], 0, 0);

	        				$orderData['redpackData'] = $redpackData;

							// 用户信息 token
							$rechargeToken = $this->strCode(json_encode(array(
									'uid' => $data['uid'],
									'orderId' => $data['orderId'],
									'lid' => $order['info']['lid'],
									'orderType' => $data['orderType'],
								)), 'ENCODE');
							
							$orderData['token'] = urlencode($rechargeToken);

							$balance = $order['info']['money'] - $money['money'];
							$orderData['balance_money'] = number_format($balance/100, 2);
							$orderData['recharge_money'] = $balance/100;

							// 跳转至IOS充值支付第二步
							$orderData['rechargeUrl'] = $this->config->item('pages_url') . 'ios/wallet/recharge/' . $token . '/' . $orderData['recharge_money'];
							$this->load->view('order/recharge', $orderData);
						}
						else
						{
							// 跳转至外部支付页
							$data['payMoney'] = $order['info']['money'];
							// 区分版本走内页还是safari
							if($versionInfo['appVersionCode'] >= 2060001)
							{
								$orderData['payToken'] = $this->getPayToken($data);
								$this->load->view('order/pay_inner', $orderData);
							}
							else
							{
								$orderData['payUrl'] = $this->getPayUrl($data);
								$this->load->view('order/pay', $orderData);
							}
						}
					}
					else
					{
						// 跳转至详情页
						$codeStr = $this->strCode(json_encode(array(
								'uid' => $data['uid']
							)), 'ENCODE');
						// 已付款 跳转至详情页
						header('Location: ' . $this->config->item('pages_url') . 'ios/chase/detail/'. $data['orderId'] . '/'. urlencode($codeStr));
					}
				}
			}
			else
			{
				// 普通订单获取订单信息
				$order = $this->Order->getById($data['orderId']);

				if(!empty($order))
				{
					if( $order['status'] == 10 && $order['uid'] == $data['uid'] )
					{
						//创建订单成功之后判断余额
	    				$money = $this->Order->getMoney($order['uid']);
						//组装数据
				    	$orderData = array();
				    	$orderData['lname'] = BetCnName::$BetCnName[$order['lid']];
				    	$orderData['lid'] = $order['lid'];
				    	$orderData['issue'] = $order['issue'];
				    	$orderData['pay_money'] = number_format($order['money']/100, 2);
						$orderData['account_money'] = number_format(ParseUnit($money['money'], 1), 2);
						$orderData['orderType'] = $data['orderType'];
						// 自购订单 购彩红包
						$orderData['redpackMoney'] = $order['redpackMoney'] ? $order['redpackMoney'] : 0;

						if( $order['money'] > $money['money'] + $orderData['redpackMoney'] )
						{
							// 查询红包信息
					        $this->load->model('redpack_model');
					        $this->eventType = $this->redpack_model->getEventType();
					        $redpackData = $this->redpack_model->getRedpackInfo($data['uid'], $this->eventType['recharge'], 0, 0);

	        				$orderData['redpackData'] = $redpackData;

							// 用户信息 token
							$rechargeToken = $this->strCode(json_encode(array(
									'uid' => $data['uid'],
									'orderId' => $data['orderId'],
									'lid' => $order['lid'],
									'orderType' => $data['orderType'],
								)), 'ENCODE');
							
							$orderData['token'] = urlencode($rechargeToken);

							$balance = $order['money'] - $money['money'] - $orderData['redpackMoney'];
							$orderData['balance_money'] = number_format($balance/100, 2);
							$orderData['recharge_money'] = $balance/100;

							// 跳转至IOS充值支付第二步
							$orderData['rechargeUrl'] = $this->config->item('pages_url') . 'ios/wallet/recharge/' . $token . '/' . $orderData['recharge_money'];
							$this->load->view('order/recharge', $orderData);
						}
						else
						{
							// 跳转至外部支付页
							$orderData['actual_money'] = number_format(ParseUnit($order['money'] - $order['redpackMoney'], 1), 2);
							// 订单金额
							$data['payMoney'] = $order['money'];
							// 红包金额
							$data['redpackMoney'] = $order['redpackMoney'];
							// 区分版本走内页还是safari
							if($versionInfo['appVersionCode'] >= 2060001)
							{
								$orderData['payToken'] = $this->getPayToken($data);
								$this->load->view('order/pay_inner', $orderData);
							}
							else
							{
								$orderData['payUrl'] = $this->getPayUrl($data);
								$this->load->view('order/pay', $orderData);
							}
						}
					}
					else
					{
						$codeStr = $this->strCode(json_encode(array(
								'uid' => $data['uid']
							)), 'ENCODE');
						// 已付款 跳转至详情页
						header('Location: ' . $this->config->item('pages_url') . 'ios/order/detail/'. $data['orderId'] . '/'. urlencode($codeStr));
					}
				}
				else
				{
					var_dump("订单信息不存在");die;
				}
			}
		}
		else
		{
			var_dump("订单参数缺失");die;
		}		
	}

	public function getPayToken($data)
	{
		// 获取版本信息
    	$versionInfo = $this->version;
        // 组装必要参数
        $payData = array(
            'userId' => $data['uid'],
            'orderId' => $data['orderId'],
            'orderType' => $data['orderType'],
            'payMoney' => $data['payMoney'],
            'redpackMoney' => $data['redpackMoney'] ? $data['redpackMoney'] : 0,
            'appVersionCode' => $versionInfo['appVersionCode'],
            'channel' => $this->recordChannel($versionInfo['channel']),	// 渠道信息
        );
        
        if ($data['orderType'] == 4) 
        {
        	$payData['uid'] = $data['uid'];
        	$payData['ctype'] = $data['ctype'];
        }

        return $this->strCode(json_encode($payData), 'ENCODE');
	}

	/*
	 * V2.6版本需求中已废弃使用外部支付
     * IOS 支付跳转safari加密处理
     * @date:2016-03-22
     */
    public function getPayUrl($data)
    {
    	// 获取版本信息
    	$versionInfo = $this->version;
        // 组装必要参数
        $payData = array(
            'userId' => $data['uid'],
            'orderId' => $data['orderId'],
            'orderType' => $data['orderType'],
            'payMoney' => $data['payMoney'],
            'redpackMoney' => $data['redpackMoney'] ? $data['redpackMoney'] : 0,
            'appVersionCode' => $versionInfo['appVersionCode'],
            'channel' => $this->recordChannel($versionInfo['channel']),	// 渠道信息
        );
        
        if ($data['orderType'] == 4) 
        {
        	$payData['uid'] = $data['uid'];
        	$payData['ctype'] = $data['ctype'];
        }

        $payToken = $this->strCode(json_encode($payData), 'ENCODE');

        $sign = $this->encryptData($payData);

        $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https:" : "http:";
        $url =  $protocol . $this->config->item('pages_url') . "ios/order/payConfirm/" . urlencode($payToken) . '/' . urlencode($sign);

        return $url;
    }

    /*
     * IOS 外部支付确认页
     * @date:2016-03-22
     */
    public function payConfirm($payToken, $sign = '')
    {
    	// 验证提交信息
        $data = $this->strCode(urldecode($payToken));
        $data = json_decode($data, true);

        if(empty($data) || empty($sign))
        {
            var_dump("请求参数错误");die;
        }

        if( $this->encryptData($data) !== urldecode($sign) )
        {
            var_dump("校验参数错误");die;
        }

        // 获取账户余额
        $money = $this->Order->getMoney($data['userId']);

        $orderData = array(
        	'account_money' => number_format(ParseUnit($money['money'], 1), 2),
        	'payMoney' => ParseUnit($data['payMoney'], 1),
        	'redpackMoney' => $data['redpackMoney'],
        	'actual_money' => ParseUnit($data['payMoney'] - $data['redpackMoney'], 1),
        	'payToken' => $payToken
        );

        // 马甲版渠道区分
        $orderData['isChannel'] = FALSE;
        $this->config->load('channel');
        $channelArr = $this->config->item('channel');
        if(in_array($data['channel'], $channelArr))
        {
            $orderData['isChannel'] = TRUE;
            $this->load->model('channel_model', 'channel');
            $channelInfo = $this->channel->getChannelInfo($data['channel']);
            $orderData['channelName'] = $channelInfo['name'] ? $channelInfo['name'] : '';
        }

        $this->load->view('order/pay2', $orderData);
    }
	
	/**
	 * 订单详细页面
	 * @param unknown_type $orderId
	 * @param unknown_type $strCode
	 */
	public function detail($orderId, $strCode)
	{
		$this->checkUserAgent();
		$versionInfo = $this->getUserAgentInfo();
		$data = $this->strCode(urldecode($strCode));
		$data = json_decode($data, true);
		$uid = isset($data['uid']) ? $data['uid'] : '';
		if(empty($uid))
		{
			echo '访问错误';
			return ;
		}
		 
		$this->load->library('BetCnName');
		$this->load->model('order_model', 'Order');
		$this->load->model('lottery_model', 'Lottery');

		//# 查出订单的情况
		$orderResponse = $this->Order->getOrder(array(
			'uid' => $uid,
			'orderId' => $orderId,
		));
		if (!empty($orderResponse['data']))
		{
			$order = $orderResponse['data'];
			// 加奖活动
			$order['add_money'] = $this->getJjActivity($order);
			$orderDetail = array();
			$passWay = '';
			$betStr = array();
			$orderPlan = array();	// 方案
			if($order['lid'] == Lottery_Model::JCZQ || $order['lid'] == Lottery_Model::JCLQ)
			{
				// 切换出票
				$award = $this->Order->getJjcMatchDetail($order['lid'], $order['codecc']);
				
				//出票成功 显示订单详情
				$ticketData = array();
	            if($order['status'] >= $this->order_status['draw'])
	            {
	                //查询拆单详情
	                $orderDetail = $this->orderDetail($orderId);
	                $ticketData = $orderDetail['ticketData'];
	            }

				if($order['lid'] == Lottery_Model::JCZQ)
				{	
					// 针对单关处理
					if($order['playType'] == 6)
					{
						$this->load->library('JcDgBuyContent');
						$award = $this->jcdgbuycontent->index($order['codes'], $award, $ticketData, $versionInfo);
					}
					// 针对奖金优化处理
					elseif($order['playType'] == 7)
					{
						$this->load->library('JcBonusBuyContent');
						$award = $this->jcbonusbuycontent->index($order['codes'], $award, $ticketData, $versionInfo);
					}
					else
					{
						$this->load->library('JcBuyContent');
						$award = $this->jcbuycontent->index($order['codes'], $award, $ticketData, $versionInfo);
					}		
				}
				else
				{
					$this->load->library('JclqBuyContent');
					$award = $this->jclqbuycontent->index($order['codes'], $award, $ticketData, $versionInfo);
				}

	            //玩法
	            if($order['playType'] == 7)
	            {
	            	//竞彩足球奖金优化 玩法
	            	$passWayArr = array();
	            	$codeStr = explode(';', $order['codes']);
	            	foreach ($codeStr as $code) 
	            	{
	            		$codes = explode('|', $code);
	            		array_push($passWayArr, $this->getType(trim($codes[3])));
	            	}
	            	$passWayArr = array_unique($passWayArr);
	            	$passWay = implode(',', $passWayArr);
	            }
	            else
	            {
	            	$codes = explode('|', $order['codes']);
					$passWay = $this->getType($codes[2]);
	            }

	            // 出票时间
    			$order['ticket_time'] = $orderDetail['ticketTime'];
			}
			elseif($order['lid'] == Lottery_Model::GJ || $order['lid'] == Lottery_Model::GYJ)
			{
				// 冠亚军 冠军彩
				$orderDetail = $this->gjorderDetail($order);
				// 出票时间
    			$order['ticket_time'] = $orderDetail['ticketTime'];
			}
			elseif($order['lid'] == Lottery_Model::SFC || $order['lid'] == Lottery_Model::RJ)
			{
				
				//# 获知档期彩票中奖号码
	            $awardDetail = $this->Order->getSfcAward($order['issue']);
	            $awardDetail['seLotid'] = $order['lid'];
	            $this->load->library('Lottery');
	            $award = $this->lottery->index($order['codes'], $awardDetail);
	            //获取比赛对阵信息
	            $matches = $this->Order->getSfcMatchs($order['issue']);
	            //获取订单投注信息
	            $betInfos = $this->splitBetStr($order['lid'],$order['codes']);
	            //获取开奖内容
	            $awardNumber = explode(',', $awardDetail['awardNumber']);
	            //方案与开奖结果对比
	            $betStr = array();
	            foreach ($betInfos as $in => $betInfo) 
	            {
	            	foreach ($betInfo['bet'] as $key => $value) 
		            {
		                //单场选择结果
		                $bet1 = $this->strToAry($value);
		                rsort($bet1);

		                $betArray = array();
		                foreach ($bet1 as $k => $v) 
		                {
		                    if(!empty($awardNumber))
		                    {
		                        if($v == $awardNumber[$key])
		                        {
		                            $betArray[$k]['is_win'] = 1;
		                        }
		                        else
		                        {
		                            $betArray[$k]['is_win'] = 0;
		                        }
		                    }
		                    else
		                    {
		                        $betArray[$k]['is_win'] = 0;
		                    
		                    }
		                    $betArray[$k]['bet'] = $v;
		                }                
		                $betStr[$in][$key] = $betArray;
		            }
	            }
	            //出票明细
        		$splitOrders = $this->Order->getNumOrderDetail($orderId, $order['lid']);
        		// 出票时间
		        $order['ticket_time'] = '';
		        if(!empty($splitOrders))
		        {
		            foreach ($splitOrders as $items) 
		            {
		                // 最晚出票时间
		                if($items['status'] >= 500)
		                {
		                    if(empty($order['ticket_time']) || (!empty($order['ticket_time']) && $order['ticket_time'] <= $items['ticket_time']))
		                    {
		                        $order['ticket_time'] = $items['ticket_time'];
		                    }
		                }
		            }
		        }
			}
			elseif(in_array($order['lid'], array(Lottery_Model::KS, Lottery_Model::JLKS, Lottery_Model::JXKS)))
			{
				//# 获知档期彩票中奖号码
            	$awardDetail = $this->Order->getNumIssue($order['lid'], $order['issue']);
            	$this->load->library('Ks');
				$award = $this->ks->index($order['codes'], $awardDetail);

				if(empty($awardDetail['awardNumber']))
				{
					$awardDetail['tip'] = $this->getAwardTime($order['lid'], $awardDetail['awardTime']);
				}

				// 数字彩继续购买方案
				$orderPlan = array(
					'codes' 	=> $order['codes'],
					'lid' 		=> $order['lid'],
					'isChase'	=> $order['isChase'],
				);
				//出票明细
        		$splitOrders = $this->Order->getNumOrderDetail($orderId, $order['lid']);
        		// 出票时间
		        $order['ticket_time'] = '';
		        if(!empty($splitOrders))
		        {
		            foreach ($splitOrders as $items) 
		            {
		                // 最晚出票时间
		                if($items['status'] >= 500)
		                {
		                    if(empty($order['ticket_time']) || (!empty($order['ticket_time']) && $order['ticket_time'] <= $items['ticket_time']))
		                    {
		                        $order['ticket_time'] = $items['ticket_time'];
		                    }
		                }
		            }
		        }
			}
			else
			{
				//# 获知档期彩票中奖号码
            	$awardDetail = $this->Order->getNumIssue($order['lid'], $order['issue']);
            	$this->load->library('Lottery');
				$award = $this->lottery->index($order['codes'], $awardDetail);

				if(empty($awardDetail['awardNumber']))
				{
					$awardDetail['tip'] = $this->getAwardTime($order['lid'], $awardDetail['awardTime']);
				}

				// 解决福彩3D不显示继续购买
				if($order['lid'] == Lottery_Model::FCSD && $versionInfo['appVersionCode'] <= '14')
				{
					$orderPlan = array(
						'codes' 	=> '',
						'lid' 		=> '',
						'isChase'	=> '',
					);
				}
				else
				{
					// 数字彩继续购买方案
					if($order['lid'] == Lottery_Model::FCSD || $order['lid'] == Lottery_Model::PLS)
					{
						// 是否包含组三单式
						if(strpos($order['codes'], ':2:1') !== FALSE && $versionInfo['appVersionCode'] < '3010000')
						{
							$order['codes'] = '';
						}
					}
					// 数字彩继续购买方案
					$orderPlan = array(
						'codes' 	=> $order['codes'],
						'lid' 		=> $order['lid'],
						'isChase'	=> $order['isChase'],
					);
				}

				
				//出票明细
        		$splitOrders = $this->Order->getNumOrderDetail($orderId, $order['lid']);
        		// 出票时间
		        $order['ticket_time'] = '';
		        if(!empty($splitOrders))
		        {
		            foreach ($splitOrders as $items) 
		            {
		                // 最晚出票时间
		                if($items['status'] >= 500)
		                {
		                    if(empty($order['ticket_time']) || (!empty($order['ticket_time']) && $order['ticket_time'] <= $items['ticket_time']))
		                    {
		                        $order['ticket_time'] = $items['ticket_time'];
		                    }
		                }
		            }
		        }
			}

			// 订单信息加密
			$codeStr = $this->strCode(json_encode(array('uid'=> $uid, 'orderId'=>$order['orderId'])), 'ENCODE');

			// O2O投注站信息
			if($order['status'] >= $this->order_status['drawing'])
			{
				$this->load->model('betstation_model', 'Betstation');
				$shopDetail = $this->Betstation->getBetShopDetail($order['shopId']);
				//数字彩加奖查询
				if(!in_array($order['lid'], array(Lottery_Model::JCZQ, Lottery_Model::JCLQ)))
				{
					$order['add_money'] = $this->Order->getOtherBonus($order['orderId'], $order['lid']);
				}
			}

			$lsDetail = array();
			// 大乐透乐善奖
			if($order['lid'] == Lottery_Model::DLT && $order['status'] >= 500)
			{
				$lsDetail = $this->getLsDetail($order['orderId'], $order['lid']);
			}

			$res = array(
				'title' => '订单详情',
				'order' => $order,
				'award' => $award,
				'awardDetail' => $awardDetail,
				'cnName' => $this->Lottery->getCnName($order['lid']),
				'enName' => $this->Lottery->getEnName($order['lid']),
				'orderDetail' => $orderDetail,
				'passWay' => $passWay,
				'betStr' => $betStr,
				'orderId' => $orderId,
				'shopDetail' => $shopDetail,
				'orderStatus' => $this->order_status,
				'strCode' => $strCode,
				'codeStr' => urlencode($codeStr),
				'versionInfo' => $versionInfo,
				'orderPlan' => $orderPlan,
				'banner' => $this->getDetailBanner($order['lid']),
				'appStars' => $this->checkAppStars($order),
				'lsDetail' => $lsDetail,
				'exception' =>	$this->getJjcException($award['exception']),
			);
			
			if ($matches)
			{
				$res['matches'] = $matches;
			}

			$this->load->view('order/detail', $res);
		}
	}

	/*
	 * APP 数字彩开奖提示
	 * @date:2015-09-24
	 */
	private function getAwardTime($lid, $awardTime)
	{		
		$nowTime = strtotime('now');
		$awardTime = substr($awardTime, 0, 10);
		if($nowTime >= $awardTime)
		{
			$tip = "开奖中";	
		}
		else
		{
			$timeDif = $awardTime-$nowTime;
			if($lid == '21406' || $lid == '21407' || $lid == '21408' || $lid == '53' || $lid == '54' || $lid == '56' || $lid == '57' || $lid == '21421' )
			{
				$remain = $timeDif%86400;
				$difH = intval($remain/3600);
				$difM = intval(($remain%3600)/60);

				$tip = "";
				if($difH > 0)
				{
					$tip .= $difH . "小时";
				}

				if( $difH > 0 || $difM > 0 )
				{
					$tip .= $difM . "分钟后开奖";
				}
				else
				{
					$tip = "开奖中";
				}
				
			}
			else
			{
				$nowTime = strtotime(date('Y-m-d'));
				$dif = intval(($awardTime-$nowTime)/3600/24);
				$dayInfo = array('今天', '明天', '后天');
				if(in_array($lid, array('51','23529','23528','10022','33','35','52')) && $dif >2 )
				{
					$tip = date('m-d H:i',$awardTime) . "开奖";
				}else{
					$tip = $dayInfo[$dif] . date('H:i',$awardTime) . "开奖";
				}
			}
		}
		return $tip;
	}
	
	/**
	 * 返回单式玩法
	 * @param unknown_type $lid
	 * @param unknown_type $playType
	 * @param unknown_type $playType1
	 */
	private function getPlayTypeName($lid, $playType, $betTnum, $hasDan)
	{
		$dfs = $betTnum > 1 ? '复式' : '单式';
		if (in_array($lid, array('21406', '21407', '21408', '21421')))
		{
			$dfs = $hasDan ? '胆拖' : $dfs;
			$playCnNames = array(
					'1' => '前一' . $dfs,
					'2' => '任二' . $dfs,
					'3' => '任三' . $dfs,
					'4' => '任四' . $dfs,
					'5' => '任五' . $dfs,
					'6' => '任六' . $dfs,
					'7' => '任七' . $dfs,
					'8' => '任八' . $dfs,
					'9' => '前二直选',
					'10' => '前三直选',
					'11' => '前二' . ($hasDan ? '胆拖' : '组选'),
					'12' => '前三' . ($hasDan ? '胆拖' : '组选')
			);
			$cnName = $playCnNames[$playType];
		}
		else if (in_array($lid, array('33', '52')))
		{
			$playCnNames = array(
					1 => '直选' . $dfs,
					2 => '组三' . $dfs,
					3 => '组六' . $dfs
			);
			$cnName = $playCnNames[$playType];
		}
		else if (in_array($lid, array('51', '23529')))
		{
			$playCnNames = array(
					1 	=> $dfs,
					2   => $dfs,
					135 => '胆拖',
			);
			$cnName = $playCnNames[$playType];
		}
		else if (in_array($lid, array('53', '56', '57')))
		{
			$playCnNames = array(
					'1' => '和值',
					'2' => '三同号通选',
					'3' => '三同号单选',
					'4'	=> '三不同号',
					'5' => '三连号通选',
					'6' => '二同号复选',
					'7' => '二同号单选',
					'8' => '二不同号',
			);
			$cnName = $playCnNames[$playType];
		}
		else if($lid == '54')
		{
			$playCnNames = array(
				'1' => '任选一',
				'2' => '任选二单式',
				'21' => '任选二复式',
				'22' => '任选二胆拖',
				'3' => '任选三单式',
				'31' => '任选三复式',
				'32' => '任选三胆拖',
				'4' => '任选四单式',
				'41' => '任选四复式',
				'42' => '任选四胆拖',
				'5' => '任选五单式',
				'51' => '任选五复式',
				'52' => '任选五胆拖',
				'6' => '任选六单式',
				'61' => '任选六复式',
				'62' => '任选六胆拖',
				'7' => '同花',
				'8' => '同花顺',
				'9' => '顺子',
				'10' => '豹子',
				'11' => '对子',
				'12' => '包选',
			);
			$cnName = $playCnNames[$playType];
		}
		else
		{
			$cnName = $dfs;
		}
		return $cnName;
	}

	/*
	 * APP 竞彩足球 篮球 出票明细
	 * @date:2015-06-12
	 */
	public function tickets($orderId, $strCode = null)
	{
		$data = $this->strCode(urldecode($strCode));
		$data = json_decode($data, true);
		$uid = isset($data['uid']) ? $data['uid'] : '';
		 
		$this->load->library('BetCnName');
		$this->load->model('order_model', 'Order');
		$this->load->model('lottery_model', 'Lottery');
		//# 查出订单的情况
		if (preg_match('/hm(\d{20})/', $orderId, $matches)) {
			$orderId = $matches[1];
			$this->load->model('united_order_model', 'UOrder');
			$uoinfo = $this->UOrder->getUniteOrderByOrderId($orderId);
			if (empty($uid) || $uoinfo['uid'] !== $uid) {
				if ($uoinfo['openStatus'] == 1) {
					if (empty($uid)) {
						exit('访问错误');
					}else {
						$res = $this->UOrder->getJoin($orderId, array('uid' => $uid), 'id');
					}
				}elseif ($uoinfo['openStatus'] == 2 && $uoinfo['endTime'] > time()) {
					exit('访问错误');
				}
			}
			$oinfo = $this->UOrder->getOrderInfo($orderId);
			$orderResponse = array('data' => array_merge($uoinfo, $oinfo));
		}else
		{
			if (empty($uid)) exit('访问错误');
			$orderResponse = $this->Order->getOrder(array(
					'uid' => $uid,
					'orderId' => $orderId,
			));
		}
		
		$orderDetail = array();
		
		$this->load->config('order');
		
		if (!empty($orderResponse['data']))
		{
			$orderData = $orderResponse['data'];
			$passWay = '';
			$betStr = array();
			switch ($orderData['lid'])
			{
				case Lottery_Model::JCZQ:
				case Lottery_Model::JCLQ:
					$orders = $this->orderDetail($orderId);
					$orderDetail = $orders['detail'];
					$lidtype = 'jc';
					break;
				case Lottery_Model::SFC:
				case Lottery_Model::RJ:
					$award = $this->Order->getSfcAward($orderResponse['data']['issue']);
					$awardArr = explode(',', $award['awardNumber']);
					$orders = $this->Order->getSfcOrderDetail($orderId);
					foreach ($orders as $order)
					{
						$codesArr = array_diff(explode('^', $order['codes']), array(''));
						$betNum = 1;
						if (count($codesArr) == 1) {
							$betNum = $order['betTnum'];
						}
						foreach ($codesArr as $codes)
						{
							if (!empty($codes))
							{
								$orderDetail[] = array(
										'codes'  => $codes,
										'betNum' => $betNum,
										'multi'  => $order['multi'],
										'status' => $order['status'],
										'bonus'  => $order['bonus']
								);
							}
						}
					}
					$lidtype = 'sfc';
					break;
				case Lottery_Model::GJ:
				case Lottery_Model::GYJ:
					$orderDetail = array();
					$details = $this->gjorderDetail($orderData);
					if(!empty($details['info']) && !empty($details['detail']))
					{
						foreach ($details['detail'] as $detail) 
						{
							foreach ($details['info'] as $key => $info) 
							{
								$orderItems['id'] = $key + 1;
								$orderItems['mid'] = $info['mid'];
								$orderItems['name'] = $info['name'];
								$orderItems['sp'] = $details['pDetail'][$detail['sub_order_id']][$info['mid']];
								$orderItems['betTnum'] = $detail['betTnum']/count($details['info']);
								$orderItems['multi'] = $detail['multi'];
								$orderItems['money'] = number_format(ParseUnit($detail['money']/count($details['info']), 1), 2);
								$orderItems['bonus'] = ($info['status'] == 2 && $detail['status'] == 2000)?'1':'0';
								$orderItems['status'] = ($detail['status'] == 600)?'出票失败':'出票成功'; 
								$orderItems['statusMsg'] = $this->getGyjStatus($detail, $info);
								array_push($orderDetail, $orderItems);
							}
						}
					}
					break;
				case Lottery_Model::KS:
				case Lottery_Model::JLKS:
				case Lottery_Model::JXKS:
				case Lottery_Model::CQSSC:
					$orderDetail = array();
					$orders = $this->Order->getNumOrderDetail($orderId, $orderData['lid']);
					$award = $this->Order->getNumIssue($orderResponse['data']['lid'], $orderResponse['data']['issue']);

					// 出票详情
					$orderDetail = $this->getTicketDetail($orderData['lid'], $orders, $award['awardNumber']);

					break;
				case Lottery_Model::KLPK:
					$orders = $this->Order->getNumOrderDetail($orderId, $orderData['lid']);
					$award = $this->Order->getNumIssue($orderResponse['data']['lid'], $orderResponse['data']['issue']);
						
					// 出票详情
					$orderDetail = $this->getKlpkTicketDetail($orders, $award);
					break;
				default:
					$orders = $this->Order->getNumOrderDetail($orderId, $orderData['lid']);
					$award = $this->Order->getNumIssue($orderResponse['data']['lid'], $orderResponse['data']['issue']);
					if (in_array($orderData['lid'], array(Lottery_Model::SSQ, Lottery_Model::DLT, Lottery_Model::SYYDJ, Lottery_Model::JXSYXW, Lottery_Model::HBSYXW, Lottery_Model::QLC, Lottery_Model::GDSYXW)))
					{
						foreach (explode(':', $award['awardNumber']) as $awardNum) 
						{
							$awardArr[] = explode(',', $awardNum);
						}
					}
					else
					{
						foreach (explode(',', $award['awardNumber']) as $awardNum) 
						{
							$awardArr[] = array($awardNum);
						}
					}
					
					$this->load->library('Lottery');
					foreach ($orders as $order)
					{
						$codesArr = array_diff(explode('^', $order['codes']), array(''));
						$betNum = 1;
						if (count($codesArr) == 1) 
						{
							$betNum = $order['betTnum'];
						}
						foreach ($codesArr as $key => $codes)
						{
							$hasDan = false;
							if (strpos($codes, '#') !== false)
							{
								$hasDan = true;
							}
							$type = $this->getPlayTypeName($orderData['lid'], $order['playType'], $betNum, $hasDan);
							if ($order['isChase'] > 0)
							{
								$type .= "追加";
							}
							if (!empty($codes))
							{
								$tmp = array(
										'codes'   =>  $codes,
										'type'    =>  $type,
										'betNum'  =>  $betNum,
										'multi'   =>  $order['multi'],
										'bonus'   =>  0,
										'status'  =>  $order['status'],
										'playType'=>  $order['playType']
								);
								if ($order['bonus'] > 0)
								{
									switch ($orderData['lid']) {
										case '51':
										case '10022':
										case '23528':
											foreach (json_decode($order['bonus_detail'], true) as $dk => $detail)
											{
												if ($detail[$key] > 0)
												{
													$tmp['bonus'] += $award['bonusDetail'][$dk."dj"]['dzjj'] * 100 * $detail[$key] * $order['multi'];
												}
											}
											break;
										case '23529':
											foreach (json_decode($order['bonus_detail'], true) as $dk => $detail)
											{
												if ($detail[$key] > 0)
												{
													$tmp['bonus'] += $award['bonusDetail'][$dk."dj"]['jb']['dzjj'] * 100 * $detail[$key] * $order['multi'];
													if ($order['isChase'] > 0) 
													{
														$tmp['bonus'] += $award['bonusDetail'][$dk."dj"]['zj']['dzjj'] * 100 * $detail[$key] * $order['multi'];
													}
												}
											}
											break;
										case '33':
										case '35':
										case '52':
										case '21406':
										case '21407':
										case '21408':
										case '21421':
											$detail = json_decode($order['bonus_detail'], true);
											if ($detail[$key] > 0)
											{
											    if ($orderData['lid'] == 21406 || $orderData['lid'] == 21407 || $orderData['lid'] == 21408 || $orderData['lid'] == 21421) 
												{
													$typeArr = array(1 => 'qy', 2 => 'r2', 3 => 'r3', 4 => 'r4', 5 => 'r5', 6 => 'r6', 
															7 => 'r7', 8 => 'r8', 9 => 'q2zhix', 10 => 'q3zhix', 11 => 'q2zux', 12 => 'q3zux');
												}else 
												{
													$typeArr = array(1 => 'zx', 2 => 'z3', 3 => 'z6');
												}
												
												$tmp['bonus'] += $award['bonusDetail'][$typeArr[$order['playType']]]['dzjj'] * 100 * $detail[$key] * $order['multi'];
											}
											break;
									}
								}
								$orderDetail[] = $tmp;
							}
						}
					}
					$lidtype = 'num';
					break;
			}
			
			$this->load->view('order/ticket', array(
						'title'       => '出票明细',
	            		'orderDetail' => $orderDetail,
						'lidtype'     => $lidtype,
						'awardArr'    => $awardArr,
						'lid'         => $orderData['lid']
	            ));
		}

		

		
	}
	
	public function splitBetStr($lid, $codes)
	{
		$data = array();
		//胜负彩
		if($lid == '11' || $lid == '19')
		{
			$preCodes = explode(';',$codes);
			foreach ($preCodes as $key => $preCode) 
			{
				$codesStr = explode(':',$preCode,2);
				$betStr = str_replace('#', '-', $codesStr[0]);
				$data[$key]['bet'] = explode(',',$betStr);
				$data[$key]['mode'] = $codesStr[1];
			}
			
		}
		return $data;
	}
	
	/*
	 * 字符串拆分成数组
	* @author:liuli
	* @date:2015-02-03
	*/
	public function strToAry($str,$charset='utf8') {
		$strlen = mb_strlen($str);
		$array = array();
		for($i=0;$i<$strlen;$i++){
			$array[$i] = mb_substr($str,$i,1,$charset);
		}
		return $array;
	}
	
	/*
     * 竞彩足球出票拆单明细
     * @author:shigx
     * @date:2015-06-09
     */
    public function orderDetail($orderId)
    {
        $orderDetail = $this->Order->getJjcOrderDetail($orderId);
        $counts = 0;
        $orders = array();
        // 出票赔率汇总
        $ticketData = array();
        // 出票时间
        $ticketTime = '';
        if(!empty($orderDetail))
        {
            //统计总方案数
            $counts = count($orderDetail);
            $key = 1;
            foreach ($orderDetail as $detail) 
            {
            	$orderInfo = array();
                $orderInfo['id'] = $key++;
                //场次、过关方式从【ticketMix】拆分获取
                $ticketInfo = explode('|', $detail['codes']);
                $ticketDetail = $this->ticketMix($ticketInfo[0], $detail['info'], $detail['odds']);
                // 汇总出票信息
                $ticketData = $this->recordTicketInfo($ticketData, $ticketDetail['ticketData']);
                $orderInfo['matchInfo'] = $ticketDetail['detailInfo'];
                $orderInfo['type'] = $this->getType($this->jjcPlayType($detail['playType']));;
                $orderInfo['betNum'] = $detail['betTnum'];
                $orderInfo['multi'] = $detail['multi'];
                $orderInfo['money'] = $detail['money'];
                $orderInfo['bonus'] = $detail['bonus'];
                $orderInfo['status'] = $detail['status'];
                array_push($orders, $orderInfo);

                // 最晚出票时间
                if($detail['status'] >= 500)
                {
                    if(empty($ticketTime) || (!empty($ticketTime) && $ticketTime <= $detail['ticket_time']))
                    {
                        $ticketTime = $detail['ticket_time'];
                    }
                }
            }

        }

        $orders=array(
            'detail' => $orders,
            'counts' => $counts,
            'ticketData' => $ticketData,
            'ticketTime' => $ticketTime
        );
        return $orders;
    }
	
	
	public function ticketMix($ticket, $info, $odds)
    {
        $detailInfo = '';
        $ticketInfo = explode('*', $ticket);
        $count = count($ticketInfo);
        foreach ($ticketInfo as $k_ticket => $v_ticket)
        {
        	$ticketDetail = explode(',', $v_ticket);
        	$resDetail = $this->ticketDetail($ticketDetail[0],$ticketDetail[1],$ticketDetail[2], $info[$ticketDetail[0]], $odds[$ticketDetail[0]]);
            $detailInfo .= $resDetail['detail'];
            // 赔率信息
            $ticketData[$ticketDetail[0]][$ticketDetail[1]][] = $resDetail['ticketInfo'];
            if($k_ticket < $count - 1)
            {
            	$detailInfo .= ' X ';
            }
        	
        }
        
        return array('detailInfo' => $detailInfo, 'ticketData' => $ticketData) ;
    }
	
	public function ticketDetail($mid, $playType, $tickets, $info, $odds)
    {
    	// 出票盘口及赔率信息
        $ticketInfo = array();
        $matchDetail = '';
        
        if ($this->config->item('wenan') == null) $this->load->config('wenan');
        $wenan = $this->config->item('wenan');
        
        $spf = array(
            '0' => $wenan['jzspf']['0'],
            '1' => $wenan['jzspf']['1'],
            '3' => $wenan['jzspf']['3']
        );
        
        $rqspf = array(
            '0' => $wenan['jzspf']['r0'],
            '1' => $wenan['jzspf']['r1'],
            '3' => $wenan['jzspf']['r3']
        );
        
        $sf = array(
            '0' => $wenan['jlsf']['0'],
            '3' => $wenan['jlsf']['3']
        );
        
        $rfsf = array(
            '0' => $wenan['jlsf']['r0'],
            '3' => $wenan['jlsf']['r3']
        );

        //大小分
    	$dxf = array(
    		'0' => '小分',
            '3' => '大分',
    	);

        //胜分差
        $sfc = array(
            '01' => $wenan['jlsf']['3']."1-5",
            '02' => $wenan['jlsf']['3']."6-10",
            '03' => $wenan['jlsf']['3']."11-15",
            '04' => $wenan['jlsf']['3']."16-20",
            '05' => $wenan['jlsf']['3']."21-25",
            '06' => $wenan['jlsf']['3']."26+",
            '11' => $wenan['jlsf']['0']."1-5",
            '12' => $wenan['jlsf']['0']."6-10",
            '13' => $wenan['jlsf']['0']."11-15",
            '14' => $wenan['jlsf']['0']."16-20",
            '15' => $wenan['jlsf']['0']."21-25",
            '16' => $wenan['jlsf']['0']."26+"
        );
        
        
        //获取赛事日期
        $date = substr($mid, 0,4).'-'.substr($mid, 4,2).'-'.substr($mid, 6,2);
        //拆分获取 日期 + 赛事编号
        $weekarray = array("日","一","二","三","四","五","六"); 
        $matchDetail = "<b>周".$weekarray[date("w",strtotime($date))];
        $matchDetail .= substr($mid, 8).'</b>';
        $matchDetail .= '<em>[';
        //拆分获取 赛果选择 + 赔率  存在单场复试 分隔符 /
        $resBet =  explode('/', $tickets);
        $info = json_decode($info, true);
        $count = count($resBet);
        switch ($playType)
        {
            //胜平负
            case 'SPF':
                foreach ($resBet as $kBet => $vBet) 
                {
                	preg_match('/^(\d+)\(.*?\)$/is', $vBet, $matches);
                	// 出票盘口及赔率
                    $pk = '-';
                	$pl = $info["vs"]["v{$matches[1]}"][0];
                	$matchDetail .= "<i>".$spf[$matches[1]].':('.print_str($pl). ')</i>';
                    if($kBet < $count - 1)
                    {
                        $matchDetail .= ', ';
                    }
                    $ticketInfo[$pk][$matches[1]][] = $pl;
                }
                break;
            //让球胜平负
            case 'RQSPF': 
                foreach ($resBet as $kBet => $vBet) 
                {
                	// 出票盘口
                	$pk = $info['letVs']['letPoint'][0] > 0 ? '+' . $info['letVs']['letPoint'][0] : $info['letVs']['letPoint'][0];
                	preg_match('/^(\d+)(?:{(.*?)})?\(.*?\)$/is', $vBet, $matches);
                	$pl = $info["letVs"]["v{$matches[1]}"][0];
                	$matchDetail .= '<i>' . $rqspf[$matches[1]] . '[' . print_str($pk) .']:(' . print_str($pl) . ')</i>';
                	if($kBet < $count - 1)
                	{
                		$matchDetail .= ', ';
                	}
                	$ticketInfo[$pk][$matches[1]][] = $pl;
                }
                break;
            //猜比分
            case 'CBF':
                foreach ($resBet as $kBet => $vBet) 
                {
                    preg_match('/^(.*?)\(.*?\)$/is', $vBet, $matches);
                    $index = preg_replace('/[^\d]/is', '', $matches[1]);                
                    $bf = $this->getCbf($matches[1]);
                    // 出票盘口及赔率
                    $pk = '-';
                    $pl = $info["score"]["v$index"][0];
                    $matchDetail .= "<i>".$bf.':(' . print_str($pl) . ')</i>';
                    if($kBet < $count - 1)
                    {
                        $matchDetail .= ', ';
                    }
                    $ticketInfo[$pk][$matches[1]][] = $pl;
                }
                break;
            //总进球
            case 'JQS':
                foreach ($resBet as $kBet => $vBet) 
                {
                	preg_match('/^(\d+)\(.*?\)$/is', $vBet, $matches);
                	// 出票盘口及赔率
                	$pk = '-';
                    if($matches[1] >= 7)
                    {
                    	$pl = $info["goal"]["v7"][0];
                        $matches[1] = '7+';
                    }
                    else
                    {
                    	$pl = $info["goal"]["v".$matches[1]][0];
                    }
                    $matchDetail .= "<i>".$matches[1] . ':(' . print_str($pl) . ')</i>';
                    if($kBet < $count - 1)
                    {
                        $matchDetail .= ', ';
                    }
                    $ticketInfo[$pk][$matches[1]][] = $pl;
                }
                break;
            //半全场
            case 'BQC':
                foreach ($resBet as $kBet => $vBet) 
                {
                    preg_match('/^(.*?)\(.*?\)$/is', $vBet, $matches);
                    // 出票盘口及赔率
                    $pk = '-';
                    $spfInfo = explode('-', $matches[1]);
                    $pl = $info["half"]["v$spfInfo[0]$spfInfo[1]"][0];            
                    $matchDetail .= "<i>".$spf[$spfInfo[0]] . '-'. $spf[$spfInfo[1]] . ':(' . print_str($pl) . ')</i>';
                    if($kBet < $count - 1)
                    {
                        $matchDetail .= ', ';
                    }
                    $ticketInfo[$pk][$matches[1]][] = $pl;
                }
                break;
            //竞彩篮球 让分胜负
            case 'RFSF':
                foreach ($resBet as $kBet => $vBet)
                {
                	// 出票盘口及赔率
                	$pk = $info['letVs']['letPoint'][0] > 0 ? '+' . $info['letVs']['letPoint'][0] : $info['letVs']['letPoint'][0];
                   	preg_match('/^(\d+)(?:{(.*?)})?\(.*?\)$/is', $vBet, $matches);
                   	$pl = $info['letVs']["v$matches[1]"][0];
                   	$matchDetail .= '<i>' . $rfsf[$matches[1]] . '[' . print_str($pk) .']:(' . print_str($pl) . ')</i>';
                    if($kBet < $count - 1)
                    {
                        $matchDetail .= ', ';
                    }
                    $ticketInfo[$pk][$matches[1]][] = $pl;
                }
                break;
            //竞彩篮球 胜负
            case 'SF':
                foreach ($resBet as $kBet => $vBet) 
                {                  
                    preg_match('/^(\d+)\(.*?\)$/is', $vBet, $matches);
                    // 出票盘口及赔率
                    $pk = '-';
                    $pl = $info["vs"]["v{$matches[1]}"][0];
                    $matchDetail .= "<i>".$sf[$matches[1]].':('.print_str($pl) . ')</i>';
                    if($kBet < $count - 1)
                    {
                        $matchDetail .= ', ';
                    }
                    $ticketInfo[$pk][$matches[1]][] = $pl;
                }
                break;
            //竞彩篮球 大小分
            case 'DXF':
                foreach ($resBet as $kBet => $vBet)
                {
                	$in_map = array('0' => 'l', '3' => 'g');
                	preg_match('/^(\d+)\(.*?\).*?$/is', $vBet, $matches);
                	// 出票盘口及赔率
                	$pk = $info['bs']['basePoint'][0];
                	$pl = $info['bs'][$in_map[$matches[1]]][0];
                	// 出票盘口
                	$matchDetail .= "<i>".$dxf[$matches[1]] . '[' . print_str($pk) .']:(' . print_str($pl).")</i>";
                    if($kBet < $count - 1)
                    {
                        $matchDetail .= ', ';
                    }
                    $ticketInfo[$pk][$matches[1]][] = $pl;
                }
                break;
            //竞彩篮球 胜分差
            case 'SFC':
                foreach ($resBet as $kBet => $vBet) {
                	preg_match('/^(\d+)\(.*?\)$/is', $vBet, $matches);  
                	// 出票盘口及赔率
                    $pk = '-'; 
                	$pl = $info['diff']["v{$matches[1]}"][0];
                	$matchDetail .= "<i>".$sfc[$matches[1]] . ':(' . print_str($pl).")</i>";
                    if($kBet < $count - 1)
                    {
                        $matchDetail .= ', ';
                    }
                    $ticketInfo[$pk][$matches[1]][] = $pl;
                }
                break;    
            default:
                # code...
                break;
        }
        
        $matchDetail .= ']</em>';

        return array('detail' => $matchDetail, 'ticketInfo' => $ticketInfo);
    }


    public function getCbf($bf)
    {
        $bfStr = explode(':', $bf);
        if($bfStr[0] == $bfStr[1])
        {
            if($bfStr[0] >3)
            {
                $bfRes = '平其他';
            }
            else
            {
                $bfRes = $bf;
            }
        }
        elseif($bfStr[0]>5)
        {
            $bfRes = '胜其他';
        }
        elseif($bfStr[1]>5)
        {
            $bfRes = '负其他';
        }
        else
        {
            $bfRes = $bf;
        }
        return $bfRes;
    }

    public function getType($type)
    {
        $types = '';

        if($type == '1*1')
        {
            $types = '单关';
        }
        else
        {
            $types = str_replace('*', '串', $type);
        }
        return $types;
    }
	
	/*
	 * 竞彩足球【奖金明细】解析
	* @author:liuli
	* @date:2015-02-04
	*/
	public function bonusMix($bonus)
	{
		// TAG 标识奖金状态 0：等待开奖 1：未中奖 2：已中奖
		$bonusInfo =array(
				'tag' => '',
				'bonus' => ''
		);
		if(empty($bonus))
		{
			$bonusInfo['tag'] = '0';
			$bonusInfo['bonus'] = '';
		}else{
			if($bonus>0)
			{
				$bonusInfo['tag'] = '2';
				$bonusInfo['bonus'] = $bonus;
			}else{
				$bonusInfo['tag'] = '1';
				$bonusInfo['bonus'] = '';
			}
		}
		return $bonusInfo;
	}

	/*
	 * 出票时间说明
	* @author:liuli
	* @date:2015-10-16
	*/
	public function ticketTime()
	{
		$this->load->view('order/ticket_time');
	}

	/*
 	 * 竞彩足球 奖金优化明细
 	 * @date:2015-11-25
 	 */
	public function bonusopt($orderId, $strCode)
	{	
		$data = $this->strCode(urldecode($strCode));
		$data = json_decode($data, true);
		$uid = isset($data['uid']) ? $data['uid'] : '';
		

		$this->load->library('BetCnName');
		$this->load->model('order_model', 'Order');
		$this->load->model('lottery_model', 'Lottery');
		// 订单状态标示
		$this->load->config('order');
		$orderStatus = $this->config->item("cfg_orders");
		// 查出订单的情况
		
		if (preg_match('/hm(\d{20})/', $orderId, $matches)) {
			$orderId = $matches[1];
			$this->load->model('united_order_model', 'UOrder');
			$uoinfo = $this->UOrder->getUniteOrderByOrderId($orderId);
			if (empty($uid) || $uoinfo['uid'] !== $uid) {
				if ($uoinfo['openStatus'] == 1) {
					if (empty($uid)) {
						exit('访问错误');
					}else {
						$res = $this->UOrder->getJoin($orderId, array('uid' => $uid), 'id');
					}
				}elseif ($uoinfo['openStatus'] == 2 && $uoinfo['endTime'] > time()) {
					exit('访问错误');
				}
			}
			$oinfo = $this->UOrder->getOrderInfo($orderId);
			$orderResponse = array('data' => array_merge($uoinfo, $oinfo));
		}else {
			if (empty($uid)) exit('访问错误');
			$orderResponse = $this->Order->getOrder(array(
					'uid' => $uid,
					'orderId' => $orderId,
			));
		}
		
		$orderDetail = array();
		if (!empty($orderResponse['data']))
		{
			$order = $orderResponse['data'];
			$passWay = '';
			$betStr = array();
			if($order['lid'] == Lottery_Model::JCZQ || $order['lid'] == Lottery_Model::JCLQ)
			{	
				// 查询场次信息
				$matchInfo = $this->Order->getJjcMatchDetail($order['lid'], $order['codecc']);
				$ticketData = array();
				if($order['status']>=500)
				{
					//查询拆单详情
                	$orderDetail = $this->orderDetail($orderId);
                	$ticketData = $orderDetail['ticketData'];
				}
				$this->load->library('JcBonusBuyContent');
				$orderDetail = $this->jcbonusbuycontent->bonusOpt($order['codes'], $matchInfo, $ticketData);

				// 显示优化中奖明细 
				if($order['status']>=500)
				{
		            $orderDetail = $this->bonusOptDetail($orderId, $orderDetail);
				}
				else
				{
					foreach ($orderDetail as $key => $detail) 
					{
						$orderDetail[$key]['status'] = $order['status'];
					}
				}
			}	
		}

		$orderStatus = $this->config->item("cfg_orders");

		$this->load->view('order/bonusopt', array(
			'title' => '奖金优化明细',
			'orderStatus' => $orderStatus,
			'bonusInfo' => $orderDetail
		));
	}

	// 优化明细
	private function bonusOptDetail($orderId, $orderDetail)
	{
		$detailInfo = array();
		if(!empty($orderDetail))
		{
			$subInfo = array();
			foreach ($orderDetail as $subDetail) 
			{
				$subInfo[$subDetail['index']] = $subDetail;
			}

			$splitDetail = $this->Order->getJjcOrderDetail($orderId);
			// 方案池
			$subCodeId = array();
        	$orders = array();
        	if(!empty($splitDetail))
        	{
        		foreach ($splitDetail as $detail) 
        		{
        			$orderInfo = array();
        			if(empty($subCodeId[$detail['subCodeId']]))
            		{
            			$orderInfo['index'] = $detail['subCodeId'];
            			$orderInfo['matchInfo'] = $subInfo[$detail['subCodeId']]['matchInfo'];
            			$orderInfo['type'] = $subInfo[$detail['subCodeId']]['type'];
            			$orderInfo['multis'] = $subInfo[$detail['subCodeId']]['multis'];;
            			$orderInfo['bonus'] = $detail['bonus'];
            			$orderInfo['status'] = $detail['status'];
            			$subCodeId[$detail['subCodeId']] = $orderInfo;
            		}
            		else
            		{
            			$orderInfo['index'] = $detail['subCodeId'];
            			$orderInfo['matchInfo'] = $subInfo[$detail['subCodeId']]['matchInfo'];
            			$orderInfo['type'] = $subInfo[$detail['subCodeId']]['type'];
            			$orderInfo['multis'] = $subInfo[$detail['subCodeId']]['multis'];;
            			$orderInfo['bonus'] = $detail['bonus'] + $subCodeId[$detail['subCodeId']]['bonus'];
            			$orderInfo['status'] = $this->getBonusOptStatus($subCodeId[$detail['subCodeId']]['status'], $detail['status']);
            			$subCodeId[$detail['subCodeId']] = $orderInfo;
            		}
        		}
        		$detailInfo = $subCodeId;
        	}
		}
		return $detailInfo;
	}

	// 获取子订单状态
	private function getBonusOptStatus($cStatus, $nStatus)
	{
		$orderStatus = $this->config->item("cfg_orders");
		$status = $orderStatus['draw'];
		// 等待开奖
		if($cStatus == $nStatus)
		{
			$status = $cStatus;
		}
		elseif ($cStatus == $orderStatus['concel'] || $nStatus == $orderStatus['concel']) 
		{
			$status = $orderStatus['concel'];
		}
		return $status;
	}
	// 冠军彩
	public function gjorderDetail($order) 
	{
		$ticketTime = '';
    	preg_match_all('/(\d+)\((\d+\.*\d*)\)/', $order['codes'], $matches);
    	if (!empty($matches[1])) 
    	{
    		$orderDetail = array();
    		if (in_array($order['status'], array(500, 510, 1000, 1010, 2000))) 
    		{
    			$orderDetail = $this->Order->getGjOrderDetail($order['orderId']);
    		}
    		$res = $this->Order->getGjDetail($matches[1], $order['lid']);
    		if ($orderDetail) 
    		{
    			$pDetail = array();
    			foreach ($orderDetail as $detail) 
    			{
    				$pDetail[$detail['sub_order_id']] = json_decode($detail['pdetail'], true);

    				// 最晚出票时间
	                if($detail['status'] >= 500)
	                {
	                    if(empty($ticketTime) || (!empty($ticketTime) && $ticketTime <= $detail['ticket_time']))
	                    {
	                        $order['ticket_time'] = $detail['ticket_time'];
	                    }
	                }
    			}
    		}
    		foreach ($res as $val) 
    		{
    			$odres[$val['mid']] = $val;
    		}
    		$strfa = '';
    		if ($order['lid'] == Lottery_Model::GJ) 
    		{
    			$statusArr = array(0 => '---', 1 => '出局', 2 => '夺冠', 3 => '---');
    		}
    		else 
    		{
    			$statusArr = array(0 => '---', 1 => '出局', 2 => '晋级决赛', 3 => '---');
    		}
    		$info = array();
    		
    		foreach ($matches[1] as $k => $val) 
    		{
    			$info[] = array(
    				'mid'  		=> $val,
    				'name' 		=> $odres[intval($val)]['name'],
    				'sp'   		=> $matches[2][$k],
    				'status' 	=> $odres[intval($val)]['status'],
    				'statusMsg' => $statusArr[$odres[intval($val)]['status']],
    				'bonus'		=> ($odres[intval($val)]['status'] == 2)?'1':'0'
    			);
    		}
    	}
    	return array('detail' => $orderDetail, 'info' => $info, 'pDetail' => $pDetail, 'ticketTime' => $ticketTime);
    }

    public function getGyjStatus($detail, $info)
    {
    	$status = '';
    	if($info['status'] == 2 && $detail['status'] == 2000)
    	{
    		$status = "奖金" . number_format(ParseUnit($detail['bonus'], 1), 2) . "元";
    	}
    	elseif(in_array($detail['status'], array(1000, 2000)))
    	{
    		$status = '未中奖';
    	}
    	elseif($detail['status'] == '600')
    	{
    		$status = '---';
    	}
    	else
    	{
    		$status = '等待开奖';
    	}
    	return $status;
    }
	// 加奖
	public function getJjActivity($order)
	{
		$add_money = 0;
		if(($order['activity_ids'] & 4) == 4)
		{
			// 查询订单加奖金额
			$detail = $this->Order->getJjDetail($order['orderId']);

			if(!empty($detail))
			{
				$add_money = $detail['add_money'];
			}
		}
		return $add_money;
	}
    /*
 	 * 出票明细详情
 	 * @date:2016-07-20
 	 */
    public function getTicketDetail($lid, $cast, $award)
    {
		$orderDetail = array();
		$award = explode(',', $award);
    	switch ($lid) 
    	{
    		case '53':
    		case '56':
    		case '57':
    			foreach ($cast as $key => $order) 
    			{
    				$detail = array(
						'playTypeName' 	=> 	$this->getPlayTypeName($lid, $order['playType'], $order['betTnum'], false),
						'betTnum'  		=>  $order['betTnum'],
						'multi'   		=>  $order['multi'],
						'bonus'   		=>  $order['bonus'],
						'status'  		=>  $order['status'],
						'tpl'	 		=>  '',
					);

					$playTypeCode = intval($order['playType']);
					$castPre = explode(',', $order['codes']);
					$preTpl = '';
    				if( $playTypeCode == '1' )
					{	
						// 和值
						foreach($castPre as $key => $number)
						{
							if($number == array_sum($award))
							{
								$preTpl .= $this->renderRedDetail($number);
							}
							else
							{
								$preTpl .= $this->renderGrayDetail($number);
							}
						}	
					}
					elseif( $playTypeCode == '2' )
					{
						// 三同号通选
						if(!empty($award[0]) && $award[0] == $award[1] && $award[1] == $award[2])
						{
							$preTpl .= $this->renderRedDetail('三同号通选');
						}
						else
						{
							$preTpl .= $this->renderGrayDetail('三同号通选');
						}
					}
					elseif( $playTypeCode == '3' )
					{
						// 三同号单选
						$number = implode('', $castPre);
						if($award[0] == $award[1] && $award[1] == $award[2])
						{
							if( $castPre[0] == $award[0])
							{
								$preTpl .= $this->renderRedDetail($number);
							}
							else
							{
								$preTpl .= $this->renderGrayDetail($number);
							}
						}
						else
						{
							$preTpl .= $this->renderGrayDetail($number);
						}	
					}
					elseif( $playTypeCode == '4' )
					{
						// 三不同号
						$number = implode('', $castPre);
						$awardNumber = implode('', $award);
						if($number == $awardNumber)
						{
							$preTpl .= $this->renderRedDetail($number);
						}
						else
						{
							$preTpl .= $this->renderGrayDetail($number);
						}
					}
					elseif( $playTypeCode == '5' )
					{
						// 三连号通选
						$number = implode('', $castPre);
						asort($award);
						$max = end($award);

						if($award[0] == $award[1] || $award[1] == $award[2] || $award[0] == $award[2])
						{
							$preTpl .= $this->renderGrayDetail('三连号通选');
						}
						else
						{
							if( array_sum($award) > 0 && ($max * 3 - array_sum($award) <= 3) )
							{
								$preTpl .= $this->renderRedDetail('三连号通选');
							}
							else
							{
								$preTpl .= $this->renderGrayDetail('三连号通选');
							}
						}		
					}
					elseif( $playTypeCode == '6' )
					{
						// 二同号复选
						$number = implode('', $castPre);
						asort($award);
						$awardNumber = implode('', $award);
						$numberStr = str_replace('*', '', $number);

						if(strpos($awardNumber, $numberStr) !== FALSE)
						{
							$preTpl .= $this->renderRedDetail($number);
						}
						else
						{
							$preTpl .= $this->renderGrayDetail($number);
						}
					}
					elseif( $playTypeCode == '7' )
					{
						// 二同号单选
						$number = implode('', $castPre);
						// 排序
						asort($castPre);
						asort($award);
						$numberStr = implode('', $castPre);
						$awardNumber = implode('', $award);

						if($numberStr == $awardNumber)
						{
							$preTpl .= $this->renderRedDetail($number);
						}
						else
						{
							$preTpl .= $this->renderGrayDetail($number);
						}
					}
					elseif( $playTypeCode == '8' )
					{
						// 二不同号
						$number = implode('', $castPre);
						$numberStr = str_replace('*', '', $number);

						if(in_array($castPre[0], $award) && in_array($castPre[1], $award))
						{
							$preTpl .= $this->renderRedDetail($numberStr);
						}
						else
						{
							$preTpl .= $this->renderGrayDetail($numberStr);
						}
					}
					$detail['tpl'] = $preTpl;
					$orderDetail[] = $detail;
    			}
    			break;
    		case '55':
    			$this->load->library('BetCnName');
    			$dxds = array(1 => '大', 2 => '小', 4 => '单', 5 => '双');
    			foreach ($cast as $key => $order)
    			{
    				$detail = array(
   							'playTypeName' 	=> 	BetCnName::$playTypeCnName[$lid][$order['playType']],
   							'betTnum'  		=>  $order['betTnum'],
   							'multi'   		=>  $order['multi'],
   							'bonus'   		=>  $order['bonus'],
    						'status'  		=>  $order['status'],
    						'tpl'	 		=>  '',
    				);
    					 
    				$castPre = explode(',', $order['codes']);
    				$preTpl = '';
    				foreach ($castPre as $key => $number)
   					{
   						switch ($order['playType'])
   						{
   							case '1':
    							if($preTpl != '') $preTpl .= $this->renderGrayDetail('|');
    							$num = explode(',', $number);
    							$awardcode = array($award[3], $award[4]);
    							foreach ($num as $val)
    							{
    								if(!is_null($awardcode[$key]) && (($val == '1' && $awardcode[$key] > 4) || ($val == '2' && $awardcode[$key] <= 4) || ($val == '4' && $awardcode[$key]%2 == 1) || ($val == '5' && $awardcode[$key]%2 === 0)))
    								{
    									$preTpl .= $this->renderRedDetail($dxds[$val]);
   									}
   									else
   									{
   										$preTpl .= $this->renderGrayDetail($dxds[$val]);    									}
    								}
    							break;
    						case '10':
    							$num = strlen($number) > 1 ? str_split($number) : array($number);
    							foreach ($num as $val)
    							{
    								if($val == $award[4])
   									{
   										$preTpl .= $this->renderRedDetail($val);
   									}
   									else
   									{
   										$preTpl .= $this->renderGrayDetail($val);
   									}
   								}
   								break;
   							case '20':
   							case '21':
   								$awardcode = array($award[3], $award[4]);
   								if($preTpl != '') $preTpl .= $this->renderGrayDetail('|');
   								$num = strlen($number) > 1 ? str_split($number) : array($number);
   								foreach ($num as $val)
   								{
   									if($val == $awardcode[$key])
   									{
   										$preTpl .= $this->renderRedDetail($val);
   									}
   									else
   									{
   										$preTpl .= $this->renderGrayDetail($val);
   									}
   								}
   								break;
   							case '23':
   							case '27':
   								$awardcode = array($award[3], $award[4]);
   								if(in_array($number, $awardcode))
   								{
   									$preTpl .= $this->renderRedDetail($number);
   								}
   								else
   								{
   									$preTpl .= $this->renderGrayDetail($number);
   								}
   								break;
   							case '30':
   							case '31':
   								$awardcode = array($award[2], $award[3], $award[4]);
   								if($preTpl != '') $preTpl .= $this->renderGrayDetail('|');
   								$num = strlen($number) > 1 ? str_split($number) : array($number);
   								foreach ($num as $val)
   								{
   									if($val == $awardcode[$key])
   									{
   										$preTpl .= $this->renderRedDetail($val);
   									}
   									else
   									{
   										$preTpl .= $this->renderGrayDetail($val);
   									}
   								}
   								break;
   							case '33':
   							case '34':
   							case '37':
   							case '38':
   								$awardcode = array($award[2], $award[3], $award[4]);
   								if(in_array($number, $awardcode))
   								{
   									$preTpl .= $this->renderRedDetail($number);
   								}
   								else
   								{
   									$preTpl .= $this->renderGrayDetail($number);
   								}
   								break;
   							case '40':
   							case '41':
   							case '43':
   								if($preTpl != '') $preTpl .= $this->renderGrayDetail('|');
   								$num = strlen($number) > 1 ? str_split($number) : array($number);
   								foreach ($num as $val)
   								{
   									if($val == $award[$key])
   									{
   										$preTpl .= $this->renderRedDetail($val);
   									}
   									else
   									{
   										$preTpl .= $this->renderGrayDetail($val);
   									}
   								}
   								break;
   									
   							}
	   					}
    			
    					$detail['tpl'] = $preTpl;
    					$orderDetail[] = $detail;
    				}
    				 
    				break;
    		default:
    			# code...
    			break;
    	}
    	return $orderDetail;
    }
    
	private function getKlpkTicketDetail($cast, $award)
    {
    	$orderDetail = array();
		foreach ($cast as $order)
		{
			$codesArr = array_diff(explode('^', $order['codes']), array(''));
			$betNum = 1;
			if (count($codesArr) == 1)
			{
				$betNum = $order['betTnum'];
			}
			foreach ($codesArr as $key => $codes)
			{
				$hasDan = false;
				if (strpos($codes, '#') !== false)
				{
					$hasDan = true;
				}
				$type = $this->getPlayTypeName(54, $order['playType'], $betNum, $hasDan);
				if (!empty($codes))
				{
					$tmp = array(
						'codes'   =>  $codes,
						'playTypeName' =>  $type,
						'betNum'  =>  $betNum,
						'multi'   =>  $order['multi'],
						'bonus'   =>  0,
						'status'  =>  $order['status'],
						'playType'=>  $order['playType'],
						'tpl'	  => '',
						'hasDan'  => $hasDan,
					);
					if ($order['bonus'] > 0)
					{
						if(in_array($order['playType'], array('2', '3', '4', '5', '6')))
						{
							$detail = json_decode($order['bonus_detail'], true);
							if ($detail[$key] > 0)
							{
								$tmp['bonus'] = $award['bonusDetail']['r' . $order['playType']]['dzjj'] * 100 * $detail[$key] * $order['multi'];
							}
						}
						else
						{
							$tmp['bonus'] = $order['bonus'];
						}
						
					}
					
					$orderDetail[] = $tmp;
				}
			}
		}
		
		$awardArr[0] = array();
		$awardArr[1] = array();
		foreach (explode(':', $award['awardNumber']) as $key => $awardNum)
		{
			$aw = explode(',', $awardNum);
			sort($aw);
			$awardArr[$key] = array_map (array($this, 'getKlpkAlias'), $aw);
		}
		
		foreach ($orderDetail as $key => $order)
		{
			$preDan = array();
			$preTuo = array();
			if($order['hasDan'])
			{
				$pres = explode('#', $order['codes']);
				$preDan = explode(',', $pres[0]);
				$preTuo = explode(',', $pres[1]);
			}
			else
			{
				$preTuo = explode(',', $order['codes']);
			}
			$preDan = array_map (array($this, 'getKlpkAlias'), $preDan);
			$preTuo = array_map (array($this, 'getKlpkAlias'), $preTuo);
			if($preDan)
			{
				$orderDetail[$key]['tpl'] .= $this->renderGrayDetail('(');
				foreach ($preDan as $dan)
				{
					if(in_array($dan, $awardArr[0]))
					{
						$orderDetail[$key]['tpl'] .= $this->renderRedDetail($dan);
					}
					else
					{
						$orderDetail[$key]['tpl'] .= $this->renderGrayDetail($dan);;
					}
				}
				$orderDetail[$key]['tpl'] .= $this->renderGrayDetail(')');
			}
			if($order['playType'] == '7')
			{
				//同花处理
				$th = array('S' => '黑桃', 'H' => '红桃', 'C' => '梅花', 'D' => '方块', 'B' => '包选');
				$code = array('A' => 'S', '2' => 'H', '3' => 'C', '4' => 'D', '00' => 'B');
				$countData = array_count_values($awardArr[1]); //开奖花色统计处理
				$countData = array_flip($countData);
				$isTh = isset($countData[3]) ? true : false;
				$thValue = isset($countData[3]) ? $countData[3] : '';
				foreach ($preTuo as $number)
				{
					if($isTh && ($code[$number] == $thValue || $number == '00'))
					{
						$orderDetail[$key]['tpl'] .= $this->renderRedDetail($th[$code[$number]]);
					}
					else
					{
						$orderDetail[$key]['tpl'] .= $this->renderGrayDetail($th[$code[$number]]);
					}
				}
			}
			elseif ($order['playType'] == '8')
			{
				//同花顺处理
				$th = array('S' => '黑桃顺子', 'H' => '红桃顺子', 'C' => '梅花顺子', 'D' => '方块顺子', 'B' => '同花顺包选');
				$code = array('A' => 'S', '2' => 'H', '3' => 'C', '4' => 'D', '00' => 'B');
				$countData = array_count_values($awardArr[1]); //开奖花色统计处理
				$countData = array_flip($countData);
				$isTh = isset($countData[3]) ? true : false;
				$thValue = isset($countData[3]) ? $countData[3] : '';
				$sz = array('A23', '234', '345', '456', '567', '678', '789', '8910', '910J', '10JQ', 'JQK', 'AQK');
				$awardStr = implode('', $awardArr[0]);
				$isSz = in_array($awardStr, $sz) ? true : false;
				foreach ($preTuo as $number)
				{
					if($isSz && $isTh && ($code[$number] == $thValue || $number == '00'))
					{
						$orderDetail[$key]['tpl'] .= $this->renderRedDetail($th[$code[$number]]);
					}
					else
					{
						$orderDetail[$key]['tpl'] .= $this->renderGrayDetail($th[$code[$number]]);
					}
				}
			}
			elseif ($order['playType'] == '9')
			{
				//顺子处理
				$sz = array('A' =>'A23', '2' => '234', '3' => '345', '4' => '456', '5' => '567', '6' => '678', '7' => '789', '8' => '8910', '9' => '910J', '10' => '10JQ', 'J' => 'JQK', 'Q' => 'AQK', '00' => '包选');
				$awardStr = implode('', $awardArr[0]);
				$isSz = in_array($awardStr, array_values($sz)) ? true : false;
				foreach ($preTuo as $number)
				{
					$number1 = $sz[$number] == 'AQK' ? 'QKA' : $sz[$number];
					if($isSz && ($sz[$number] == $awardStr || $number == '00'))
					{
						$orderDetail[$key]['tpl'] .= $this->renderRedDetail($number1);
					}
					else
					{
						$orderDetail[$key]['tpl'] .= $this->renderGrayDetail($number1);
					}
				}
			}
			elseif ($order['playType'] == '10')
			{
				//豹子处理
				$countData = array_count_values($awardArr[0]); //开奖号码统计处理
				$countData = array_flip($countData);
				$isBz = isset($countData[3]) ? true : false;
				$bz = isset($countData[3]) ? $countData[3] : '';
				foreach ($preTuo as $number)
				{
					$number1 = $number == '00' ? '包选' : str_repeat($number, 3);
					if($isBz && ($number == $bz || $number == '00'))
					{
						$orderDetail[$key]['tpl'] .= $this->renderRedDetail($number1);
					}
					else
					{
						$orderDetail[$key]['tpl'] .= $this->renderGrayDetail($number1);
					}
				}
			}
			elseif($order['playType'] == '11')
			{
				$countData = array_count_values($awardArr[0]); //开奖号码统计处理
				$countData = array_flip($countData);
				$isDz = isset($countData[2]) ? true : false;
				$dz = isset($countData[2]) ? $countData[2] : '';
				foreach ($preTuo as $number)
				{
					$number1 = $number == '00' ? '包选' : str_repeat($number, 2);
					if($isDz && ($number == $dz || $number == '00'))
					{
						$orderDetail[$key]['tpl'] .= $this->renderRedDetail($number1);
					}
					else
					{
						$orderDetail[$key]['tpl'] .= $this->renderGrayDetail($number1);
					}
				}
			}
			else 
			{
				foreach ($preTuo as $number)
				{
					if(in_array($number, $awardArr[0]))
					{
						$orderDetail[$key]['tpl'] .= $this->renderRedDetail($number);
					}
					else
					{
						$orderDetail[$key]['tpl'] .= $this->renderGrayDetail($number);
					}
				}
			}
		}
		return $orderDetail;
	}

    private function renderRedDetail($number)
    {
    	return '<span class="bingo">' . $number . '</span>';
    }

    private function renderGrayDetail($number)
    {
    	return '<span>' . $number . '</span>';
    }

    /*
 	 * 中奖tips
 	 * @date:2015-11-25
 	 */
    public function winTips()
    {
    	$this->load->view('order/winTips');
    }
    
    //快乐扑克别名返回
    private function getKlpkAlias($key)
    {
    	$klpkAlias = array(
    		'01' => 'A',
    		'02' => '2',
    		'03' => '3',
    		'04' => '4',
    		'05' => '5',
    		'06' => '6',
    		'07' => '7',
    		'08' => '8',
    		'09' => '9',
    		'10' => '10',
    		'11' => 'J',
    		'12' => 'Q',
    		'13' => 'K',
    	);
    
    	return isset($klpkAlias[$key]) ? $klpkAlias[$key] : $key;
    }

    // 汇总出票赔率
    public function recordTicketInfo($info, $details = array())
    {
        if(!empty($details))
        {
            foreach ($details as $mid => $playItems) 
            {
                foreach ($playItems as $playType => $tickets) 
                {
                    foreach ($tickets as $key => $ticketArr) 
                    {
                        foreach ($ticketArr as $pk => $items) 
                        {
                            foreach ($items as $fa => $plArr)
                            {
                                foreach ($plArr as $k => $pl)
                                {
                                    if(!empty($info[$mid][$playType][$pk][$fa]) && in_array($pl, $info[$mid][$playType][$pk][$fa]))
                                    {
                                        continue;
                                    }
                                    else
                                    {
                                        if(!empty($pl))
                                    	{
                                    		$info[$mid][$playType][$pk][$fa][] = $pl;
                                    	}
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }
        return $info;
    }
    
    private function jjcPlayType($playType)
    {
    	$jjcPlayType = array(
    			'1' => '1*1',
    			'2' => '2*1',
    			'3' => '3*1',
    			'4' => '4*1',
    			'5' => '5*1',
    			'6' => '6*1',
    			'7' => '7*1',
    			'8' => '8*1',
    			'9' => '9*1',
    			'10' => '10*1',
    			'11' => '11*1',
    			'12' => '12*1',
    			'13' => '13*1',
    			'14' => '14*1',
    			'15' => '15*1',
    			'16' => '2*3',
    			'17' => '3*3',
    			'18' => '3*4',
    			'19' => '3*7',
    			'20' => '4*4',
    			'21' => '4*5',
    			'22' => '4*6',
    			'23' => '4*11',
    			'24' => '4*15',
    			'25' => '5*5',
    			'26' => '5*6',
    			'27' => '5*10',
    			'28' => '5*16',
    			'29' => '5*20',
    			'30' => '5*26',
    			'31' => '5*31',
    			'32' => '6*6',
    			'33' => '6*7',
    			'34' => '6*15',
    			'35' => '6*20',
    			'36' => '6*22',
    			'37' => '6*35',
    			'38' => '6*42',
    			'39' => '6*50',
    			'40' => '6*57',
    			'41' => '6*63',
    			'42' => '7*7',
    			'43' => '7*8',
    			'44' => '7*21',
    			'45' => '7*35',
    			'46' => '7*120',
    			'47' => '8*8',
    			'48' => '8*9',
    			'49' => '8*28',
    			'50' => '8*56',
    			'51' => '8*70',
    			'52' => '8*247',
    			'53' => '自由过关',
    	);
    	return $jjcPlayType[$playType];
    }
    
    private function getDetailBanner($lid)
    {
    	// 版本信息
    	$versionInfo = $this->getUserAgentInfo();
    	$bannerInfo = array();
    	// 获取弹窗信息缓存
    	$this->load->model('cache_model','Cache');
    	$info = $this->Cache->getPreloadInfo($platform = 'ios', 'orderdetail');
    	$detail = $info[$lid];
    	if(!empty($detail) && (!empty($detail['webUrl']) || in_array($detail['appAction'], array('bet', 'email'))))
    	{
    		if($detail['appAction'] == 'email' && $versionInfo['appVersionCode'] < '11') $detail['appAction'] = 'unsupport';
    		if($detail['appAction'] == 'email' && !empty($this->uinfo['email'])) $detail['appAction'] = 'ignore';
    		$bannerInfo = array(
    				'imgUrl' => (strpos($detail['imgUrl'], 'http') !== FALSE) ? $detail['imgUrl'] : $this->config->item('protocol') . $detail['imgUrl'],
    				'webUrl' => $detail['webUrl'],
    				'appAction' => $detail['appAction'],
    				'tlid' => $detail['tlid'],
    				'enName' => $this->Lottery->getEnName($detail['tlid'])
    		);
    	}
    	return $bannerInfo;
    }

    public function checkAppStars($orderInfo)
    {
    	$margin = 0;
    	if($orderInfo['margin'] > 0)
    	{
    		if($orderInfo['add_money'] > 0 && in_array($orderInfo['lid'], array(42, 43)))
    		{
    			// 竞足竞篮加奖
    			$margin = $orderInfo['margin'] + $orderInfo['add_money'];
    		}
    		else
    		{
    			$margin = $orderInfo['margin'];
    		}
    	}
    	$result = $this->checkNeedAppStars($margin);
    	return $result;
    }

    // web创建订单
    public function createOrder()
    {
    	$headerInfo = $this->getUserAgentInfo();

    	$postData = $this->input->post(null, true);

        $result = array(
            'status' => '400',
            'msg' => '通讯异常',
            'data' => ''
        );

        if(empty($this->uid))
        {
            $result = array(
                'status' => '300',
                'msg' => '用户信息已失效，重新登录',
                'data' => ''
            );
            die(json_encode($result));
        }
        
        $params = array(
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
        
        // 必要参数检查
        foreach ($params as $key => $items) 
        {
            if($postData[$key] === '' || !isset($postData[$key]))
            {
                $result = array(
                    'status' => '400',
                    'msg' => '订单必要参数缺失' . $key,
                    'data' => ''
                );
                die(json_encode($result));
            }
        }

        // 检查用户登录状态
        $this->load->model('user_model');
        $uinfo = $this->user_model->getUserInfo($this->uid);

        if(isset($uinfo['userStatus']) && $uinfo['userStatus'] == '1')
        {
            $result = array(
                'status' => '400',
                'msg' => '您的账号已注销！',
                'data' => ''
            );
            die(json_encode($result));
        }

        if(isset($uinfo['userStatus']) && $uinfo['userStatus'] == '2')
        {
            $result = array(
                'status' => '400',
                'msg' => '您的账户已被冻结，如需解冻请联系客服。',
                'data' => ''
            );
            die(json_encode($result));
        }

        // 检查是否实名
        if(empty($uinfo['real_name']) || empty($uinfo['id_card']))
        {
            $result = array(
                'status' => '500',
                'msg' => '您的账户尚未实名。',
                'data' => ''
            );
            die(json_encode($result));
        }

        // 订单金额范围检查
        if(in_array($postData['lid'], array(42, 43)))
		{
			if(!is_numeric($postData['money']) || $postData['money'] > 200000)
	        {
	        	$result = array(
					'status' => '0',
					'msg' => '订单金额需小于20万，请修改订单后重新投注',
					'data' => ''
				);
				die(json_encode($result));
	        }
		}
		else
		{
			if(!is_numeric($postData['money']) || $postData['money'] > 20000)
	        {
	            $result = array(
	                'status' => '400',
	                'msg' => '订单金额需小于2万，请修改订单后重新投注',
	                'data' => ''
	            );
	            die(json_encode($result));
	        }
		}

        $this->load->model('cache_model','Cache');
        // 版本彩种销售判断
        $appConfig = $this->Cache->getAppConfig('ios');
        if(!empty($appConfig[$headerInfo['appVersionCode']]['lotteryConfig']))
        {
        	$saleConfig = json_decode($appConfig[$headerInfo['appVersionCode']]['lotteryConfig'], true);	
        	if(isset($saleConfig[$postData['lid']]) && $saleConfig[$postData['lid']] == '1')
        	{
        		$result = array(
					'status' => '400',
					'msg' => '当前彩种已停售',
					'data' => ''
				);
				die(json_encode($result));
        	}
        }

        // 截止时间处理
        $issueInfo = $this->Cache->getIssueInfo($postData['lid']);
        $lotteryConfig = $this->Cache->getlotteryConfig();

        if(in_array($postData['lid'], array('42','43')))
		{
			if($postData['codecc'] === '')
			{
				$result = array(
					'status' => '400',
					'msg' => '缺少必要参数',
					'data' => ''
				);
				die(json_encode($result));
			}
		}
		else
		{
			// 期次信息检查
			if($issueInfo['cIssue']['seExpect'] != $postData['issue'])
			{
				$result = array(
					'status' => '400',
					'msg' => '投注不在当前销售期',
					'data' => ''
				);
				die(json_encode($result));
			}
		}

        if (!empty($issueInfo['aIssue']) && in_array($postData['lid'], array('51', '23529', '35', '10022', '23528', '33', '52')) && time() > (floor($issueInfo['aIssue']['seEndtime']/1000)-$lotteryConfig[$postData['lid']]['ahead']*60) && time() < floor($issueInfo['aIssue']['seEndtime']/1000))
        {
            $result = array(
				'status' => '0',
				'msg' => '期次更新中，请于' . date('H:i', (floor($issueInfo['aIssue']['seEndtime']/1000))) . '后投注下期' . $issueInfo['cIssue']['seExpect'],
				'data' => ''
			);
			die(json_encode($result));
        }

        $orderData = array(
			'ctype'         =>  'create',
			'uid'           =>  $this->uid,
			'userName'      =>  $uinfo['uname'],
			'codes'         =>  $postData['codes'],
			'lid'           =>  $postData['lid'],
			'money'         =>  $postData['money'],
			'multi'         =>  $postData['multi'],
			'issue'         =>  trim($postData['issue']),
			'playType'      =>  $postData['playType'],
			'isChase'       =>  $postData['isChase'],
			'betTnum'       =>  $postData['betTnum'],
			'codecc'        =>  $postData['codecc'] ? $postData['codecc'] : '',
			'orderType'     =>  '0',
			'endTime'       =>  $postData['endTime'],
			'buyPlatform'   =>  $this->config->item('platform'),
			'version'		=>	(isset($headerInfo['appVersionName']) && !empty($headerInfo['appVersionName'])) ? $headerInfo['appVersionName'] : '1.0',
			'channel'       =>  $this->recordChannel($headerInfo['channel'])
    	);

        // 初始化订单信息
        if(ENVIRONMENT === 'checkout')
		{
			$orderUrl = $this->config->item('cp_host');
			$orderData['HOST'] = $this->config->item('domain');
		}
		else
		{
			$orderUrl = $this->config->item('protocol') . $this->config->item('pages_url');
		}

		$createStatus = $this->tools->request($orderUrl . 'api/order/createOrder', $orderData);
		$createStatus = json_decode($createStatus, true);

		if($createStatus['status'])
		{
			// 创建结果处理
			$payView = $this->orderComplete($createStatus['data'], $orderData['orderType']);
			$result = array(
				'status' => '200',
				'msg' => '创建订单成功',
				'data' => $payView
			);
		}
		else
		{
			$result = array(
				'status' => '400',
				'msg' => $createStatus['msg'],
				'data' => ''
			);
		}

		die(json_encode($result));
    }

    public function orderComplete($data, $orderType = 0)
    {
		// 订单信息加密
		$orderDetail = $this->strCode(json_encode(array(
			'uid' => $data['uid'],
			'orderId' => $orderType ? $data['chaseId'] : $data['orderId'],
			'orderType' => $orderType
		)), 'ENCODE');

    	// 跳转支付页面
    	$payView = $this->config->item('protocol') . $this->config->item('pages_url') . "ios/order/doPay/" . urlencode($orderDetail);
		return $payView;
    }

    // 乐善奖
    public function getLsDetail($orderId, $lid)
    {
        $lsDetail = array();
        $totalMargin = 0;
        $info = $this->Order->getLsDetail($orderId, $lid);
        if(!empty($info))
        {
            foreach ($info as $items) 
            {
                if(empty($items['awardNum']))
                {
                    continue;
                }
                $lsDetail[$items['sub_order_id']] = $items;
                $totalMargin += $items['margin'];
            }
        }
        return array('detail' => $lsDetail, 'totalMargin' => $totalMargin);
    }

    // 乐善奖详情
    public function lsDetail($orderId, $strCode)
    {
    	$this->checkUserAgent();
		$versionInfo = $this->getUserAgentInfo();
		$data = $this->strCode(urldecode($strCode));
		$data = json_decode($data, true);
		$uid = isset($data['uid']) ? $data['uid'] : '';
		if(empty($uid))
		{
			echo '访问错误';
			return ;
		}

		//# 查出订单的情况
		$orderResponse = $this->Order->getOrder(array(
			'uid' 		=> 	$uid,
			'orderId'	=>	$orderId,
		));
		$ticketDetail = array();
		$totalMargin = 0;
		if(!empty($orderResponse['data']))
		{
			$splitOrders = $this->Order->getLsDetail($orderId, $orderResponse['data']['lid']);
			$this->load->library('Lottery');
			foreach ($splitOrders as $order)
			{
				if(empty($order['awardNum']))
                {
                    continue;
                }

                $margin = $order['margin'] ? $order['margin'] : 0;
                $totalMargin += $margin;
                // 组装数据
                $data = array(
                    'code'          =>  array(),
                    'awardNum'      =>  $this->lsAwardFormat($order['awardNum']),
                    'bonusStatus'   =>  $this->getTicketBonus($order['status'], $margin),
                );

                $codesArr = array_diff(explode('^', $order['codes']), array(''));
                $betNum = 1;
                if(count($codesArr) == 1)
                {
                	$betNum = $order['betTnum'];
                }
				
				$codes = explode('^', $order['codes']);
                foreach($codes as $key => $code) 
                {
                    if($code !== '') 
                    {
                    	$res = $this->renderLsCode($code, $order['playType'], $order['awardNum'], $betNum);
                        $data['code'][] = $res;
                    }
                }
                array_push($ticketDetail, $data);
			}
		}
		$this->load->view('order/ls_detail', array(
			'title' 		=>  '乐善奖详情',
			'bonusStatus'	=>	$this->getTicketBonus($order['status'], $totalMargin),
			'ticketDetail'	=>	$ticketDetail,
        ));
    }

    public function lsAwardFormat($awardNum)
    {
        $span = '';
        $numArr = explode('|', $awardNum);
        if($numArr[0] && $numArr[1])
        {
            foreach (explode(',', $numArr[0]) as $num) 
            {
                $span .= '<span>';
                $span .= $num;
                $span .= '</span>';
            }
            foreach (explode(',', $numArr[1]) as $num) 
            {
                $span .= '<span class="blue-ball">';
                $span .= $num;
                $span .= '</span>';
            }
        }
        return $span;
    }

    public function renderLsCode($codes, $playType, $awardNum, $betNum)
    {
    	$span = '';
    	// 开奖号码
    	$awardArr = array();
    	foreach (explode('|', $awardNum) as $nums) 
		{
			$awardArr[] = explode(',', $nums);
		}
		$hasDan = false;
		if(strpos($codes, '#') !== false)
		{
			$hasDan = true;
		}
		$type = 
		$span .= '<span class="num-group-tag">';
		$span .= $this->getPlayTypeName(23529, $playType, $betNum, $hasDan) . '追加';
		$span .= '</span>';
		$span .= '<div class="num-group">';
    	foreach(explode('|', str_replace('*', '|', $codes)) as $ck => $code)
    	{
    		foreach(explode('#', $code) as $dk => $cd) 
    		{
    			if($dk == 0 && count(explode('#', $code)) > 1)
    			{
    				$span .= '<span class="symbol-bracket">(</span>';
    			}
    			foreach(explode(',', $cd) as $c)
    			{
    				$span .= '<span ';
    				if((!empty($awardArr[$ck]) || $awardArr[$ck] === '0') && in_array($c, $awardArr[$ck]))
    				{
    					$span .= "class='bingo'";
    				}
    				$span .= '>';
    				$span .= $c . '</span>';
    			}
    			if($dk == 0 && count(explode('#', $code)) > 1) 
    			{
    				$span .= '<span>)</span>';
    			}			
    		}
    		if($ck + 1 !== count(explode('|', str_replace('*', '|', $codes))))
    		{
    			$span .= '<span class="symbol-colon">:</span>';
    		}
    	}
    	$span .= '</div>';
    	return $span;
    }

    public function getTicketBonus($status, $margin = 0)
    {
    	$bonusStatus = '<b>等待开奖</b>';
    	if(in_array($status, array(1000, 2000)))
    	{
    		if($margin > 0)
    		{
    			$bonusStatus = '<em>' . number_format(ParseUnit($margin, 1), 2) . '元</em>';
    		}
    		else
    		{
    			$bonusStatus = '<b>未中奖</b>';
    		}
    	}
    	return $bonusStatus;
    }

    // 竞技彩取消或延期公告
    public function getJjcException($exception = array())
    {
    	$result = '';
    	if(!empty($exception))
    	{
    		$ctypeArr = array(
    			'1'	=>	'延期',
    			'2'	=>	'取消',
    		);
    		$matches = array();
    		$ctypes = array();
    		foreach ($exception as $items) 
    		{
    			array_push($matches, $items['match']);
    			array_push($ctypes, $items['ctype']);
    		}
    		$ctypes = array_unique($ctypes);
    		$ctypeName = (count($ctypes) > 1) ? '延期取消' : $ctypeArr[$ctypes[0]];
    		$result = '注：';
    		if(count($matches) > 2)
    		{
    			$result .= implode('、', array_slice($matches, 0, 2)) . '等多场';
    		}
    		else
    		{
    			$result .= implode('、', array_slice($matches, 0, 2)) . '比赛';
    		}
    		$result .= $ctypeName . '，点击查看' . $ctypeName . '规则';
    	}
    	return $result;
    }
}
