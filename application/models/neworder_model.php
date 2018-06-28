<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Neworder_Model extends MY_Model
{
	
	// 订单支付提交信息
	private $payInfo = array(
			'uid' => '',			// 用户信息为兼容APP
			'orderId' => '',
			'money' => '',
			'uid' => '',
			'userName' => ''
	);

	// 追号订单提交参数
	private $payChaseInfo = array(
			'uid' => '',			// 用户信息为兼容APP
			'chaseId' => '',
			'money' => ''
	);

	public function __construct()
	{
		parent::__construct();
		$this->load->library('DisOrder');
        $this->load->model('wallet_model');
        $this->load->model('chase_wallet_model');
        $this->load->model('order_model');
        $this->load->model('user_model');
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
	public function createOrder($params = array())
	{
		$checkMap = array(
			'21406' => 'SyxwCheck',
			'21407' => 'SyxwCheck',
			'53' => 'KsCheck',
			'56' => 'KsCheck',//易快3
		    '57' => 'KsCheck',//红快3
			'54' => 'KlpkCheck',
			'51' => 'SsqCheck',
			'23529' => 'DltCheck',
			'23528' => 'QlcCheck',
			'35' => 'PlwCheck',
			'10022' => 'PlcommCheck',
			'33' => 'PlsAnd3dCheck',
			'52' => 'PlsAnd3dCheck',
			'11' => 'LzcCheck',
			'19' => 'LzcCheck',
			'42' => 'JczqCheck',
			'43' => 'JclqCheck',
		    '44' => 'GjcCheck',
		    '45' => 'GjcCheck',
			'21408' => 'SyxwCheck',
			'55' => 'CqsscCheck',
		    '21421' => 'SyxwCheck',
		);
		if(isset($checkMap[$params['lid']]))
		{
			$this->load->library("createcheck/{$checkMap[$params['lid']]}");
			$lName = strtolower($checkMap[$params['lid']]);
			$result = $this->$lName->check($params);
			if($result['status'] == false)
			{
				return $result;
			}
			// 彩种销售状态判断 为了获取提前截止时间的提前量
			$this->load->driver('cache', array('adapter' => 'redis'));
			$REDIS = $this->config->item('REDIS');
			$lotteryConfig = $this->cache->get($REDIS['LOTTERY_CONFIG']);
			$lotteryConfig = json_decode($lotteryConfig, true);
			// 获取用户信息
			$uinfo = $this->user_model->getUserInfo($params['uid']);
			if(empty($uinfo))
			{
				$result = array(
					'status' => FALSE,
					'msg' => '用户信息获取失败',
					'data' => ''
				);
				return $result;
			}
			// 组装数据
			$this->load->model('activity_model');
			$activity_ids = $this->activity_model->checkRebateByUid($params['uid']);
			if(isset($params['activity_ids'])) $activity_ids = ($activity_ids | $params['activity_ids']);
			$orderData = array(
				'ctype' => $params['ctype'],
				'endTime' => date('Y-m-d H:i:s', strtotime("+{$lotteryConfig[$params['lid']]['ahead']} minute", strtotime($params['endTime']))),
				'pay_pwd' => '',
				'codecc' => isset($params['codecc'])?$params['codecc']:'',
				'buyPlatform' => $params['buyPlatform'],
				'codes' => $params['codes'],
				'lid' => $params['lid'],
				'money' => ParseUnit($params['money']),
				'multi' => $params['multi'],
				'issue' => $params['issue'],
				'playType' => $this->getLotteryPlayType($params),	// 针对十一选五、排列三、福彩3D 混合投注playType处理
				'betTnum' => $params['betTnum'],
				'isChase' => $params['isChase'],
				'orderType' => $params['orderType'],
				'token' => $params['uid'],
				'uid' => $params['uid'],
				'userName' => $uinfo['uname'],
				'orderId' => '',
				'mark' => '0',
				'channel' => $params['channel'],
				'app_version' => isset($params['version'])?$params['version']:'0',
				'activity_ids' => $activity_ids,
				'singleFlag'=>isset($params['singleFlag']) ? $params['singleFlag'] : 0,
				'is_hide' => isset($params['is_hide']) ? $params['is_hide'] : '0',
				'forecastBonus' => !empty($params['forecastBonus']) ? $params['forecastBonus'] : '',
			);

			switch ($params['ctype'])
			{
				// 订单初始化
				case 'create':
					// 入库
					$orderData['orderId'] = $this->tools->getIncNum('UNIQUE_KEY');
					$orderStatus = $this->order_model->SaveOrder($params['ctype'], $orderData);
					// 如果存在redpackId 添加到cp_orders_detail
					if(in_array($orderData['buyPlatform'], array('1', '2', '3')) && !empty($params['redpackId']))
					{
						$this->load->model('red_pack_model');
						$redpack = $this->red_pack_model->getRedpackById($orderData['uid'], $params['redpackId']);
						if($redpack)
						{
							$this->load->config('order');
    						$cType = $this->config->item("redpack_c_type");
    						if((!in_array($redpack['c_type'], $cType[$orderData['lid']])) || ($orderData['money'] < $redpack['money_bar']) || (($orderData['buyPlatform'] == '0') && !empty($redpack['ismobile_used'])) || (($redpack['valid_start'] > date('Y-m-d H:i:s')) || ($redpack['valid_end'] < date('Y-m-d H:i:s'))))
				    		{
				    			$orderStatus = $orderStatus;
				    		}
				    		else
				    		{
				    			$orderDetail = array(
									'orderId' => $orderData['orderId'],
									'redpackId' => $params['redpackId'],
									'redpackMoney' => $redpack['money'],
								);
								$res = $this->order_model->insertOrderDetail($orderDetail);
								$orderStatus = $orderStatus && $res;
				    		}		
						}
					}
					break;
				default:
					$orderStatus = FALSE;
					break;
			}
				
			if($orderStatus)
			{
				$result = array(
					'status' => TRUE,
					'msg' => '订单创建成功',
					'data' => $orderData
				);
			}
			else
			{
				$result = array(
					'status' => FALSE,
					'msg' => '订单创建失败',
					'data' => $orderData
				);
				
			}
		}
		else
		{
			$result = array(
				'status' => FALSE,
				'msg' => '订单创建失败',
				'data' => $params
			);
		}
		
		return $result;
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
	public function doPay($params = array())
	{
		// 必要参数检查
		if(empty($params))
		{
			$result = array(
					'status' => FALSE,
					'msg' => '请求参数错误',
					'data' => ''
			);
			return $result;
		}
	
		foreach ($params as $key => $items)
		{
			// 必要参数检查
			foreach ($this->payInfo as $key => $items)
			{
				if($params[$key] == '')
				{
					$result = array(
							'status' => FALSE,
							'msg' => '缺少必要订单参数',
							'data' => $key
					);
					return $result;
				}
			}
		}
	
		// 组装请求出票数据
		$PostData = array(
				'orderId' => $params['orderId'],
				'money' => $params['money']/100,
				'token' => $params['uid'],
				'uid' => $params['uid'],
		);
	
		// 组装订单
		$orderData = array(
			'ctype' => 'pay',
			'uid' => $params['uid'],
			'userName' => $params['userName'],
			'token' => $params['uid'],
			'orderId' => $params['orderId'],
			'money' => $params['money']/100,
			'codecc' => $params['codecc'],
			'endTime' => $params['endTime'],
			'redpackId' => $params['redpackId'] ? $params['redpackId'] : 0,
		);
	
		$response = $this->wallet_model->payOrder($params['uid'], $PostData, $orderData, ParseUnit($orderData['money']));
		if(!empty($response) && !in_array($response['code'], array('12', '16')))
		{
			$result = array(
					'status' => TRUE,
					'msg' => '<div class="mod-result result-success"><div class="mod-result-bd"><i class="icon-result"></i><div class="result-txt"><h2 class="result-txt-title">恭喜您，支付成功</h2><p>我们将尽快预约投注站出票</p></div></div></div>',
					'data' => array(
							'orderId' => $params['orderId'],
					)
			);
		}
		else
		{
			$result = array(
					'status' => FALSE,
					'msg' => '付款失败！',
					'data' => $response
			);
		}
		return $result;
	}

	// 针对十一选五、排列三、福彩3D 混合投注playType处理
	public function getLotteryPlayType($params)
	{
		if(in_array($params['lid'], array('21406', '33', '52', '21407', '21408', '54', '55' , '53', '56', '57', '21421')))
        {
            $typeArry = array();
            $codes = explode(';', $params['codes']);
            foreach ($codes as $code) 
            {
                $codeArry = explode(':', $code);
                array_push($typeArry, intval($codeArry[1]));
            }
            $typeArry = array_unique($typeArry);
            if(count($typeArry) == 1)
            {
                $params['playType'] = $typeArry[0];
            }
            else
            {
                $params['playType'] = 0;
            }
        }
		return $params['playType'];
	}

   /*
	* 追号订单支付扣款
	* @date:2016-03-08
	*/
	public function doChasePay($params = array())
	{
		// 必要参数检查
		if(empty($params))
		{
			$payResult = array(
					'status' => FALSE,
					'code' => '',
					'msg' => '请求参数错误',
					'data' => ''
			);
			return $payResult;
		}
	
		foreach ($params as $key => $items)
		{
			// 必要参数检查
			foreach ($this->payChaseInfo as $key => $items)
			{
				if($params[$key] == '')
				{
					$payResult = array(
							'status' => FALSE,
							'code' => '',
							'msg' => '缺少必要订单参数',
							'data' => $key
					);
					return $payResult;
				}
			}
		}

		$payResponse = $this->chase_wallet_model->payChaseOrder($params['uid'], $params, $params['money']);
	
		if($payResponse['code'] == '200')
		{
			$payResult = array(
				'status' => TRUE,
				'code' => $payResponse['code'],
				'msg' => '付款成功，等待出票！',
				'data' => $payResponse['data']
			);
		}
		else
		{
			$payResult = array(
				'status' => FALSE,
				'code' => $payResponse['code'],
				'msg' => $payResponse['msg'],
				'data' => $payResponse['data']
			);
		}

		return $payResult;
	}
	
	/**
	 * 获取用户有效的购彩红包
	 * @param int $uid	用户uid
	 * @param array $order	订单数组
	 */
	public function getBetRedPack($uid, $order)
	{
		$datas = array();
		$this->load->model('red_pack_model');
		list($success, $msg, $redPacks) = $this->red_pack_model->fetchBetPack($uid, '', '');
		if($redPacks)
		{
			$this->load->config('order');
			$cType = $this->config->item("redpack_c_type");
			$usable = array();
			$disable = array();
			$selected = array();
			foreach ($redPacks as $value)
			{
				if(!in_array($value['c_type'], $cType[$order['lid']]))
				{
					continue;
				}
				$checkAble = 0;
				//判断红包类型
				//$checkAble = (empty($checkAble) && in_array($value['c_type'], $cType[$order['lid']])) ? 0 : 1;
				//判断使用金额
				$checkAble = (empty($checkAble) && $order['money'] >= ($value['money_bar'] / 100)) ? 0 : 1;
				
				//判断客户端专享
				$checkAble = (empty($checkAble) && empty($value['ismobile_used'])) ? 0 : 1;
				
				$info = array(
					'id' => $value['id'],
					'money' => number_format(ParseUnit($value['money'], 1), 2),
					'use_desc' => $value['use_desc'],
					'valid_end' => date('Y/m/d H:i', strtotime($value['valid_end'])),
					'c_name' => $value['c_name'],
					'ismobile_used' => $value['ismobile_used'],
					'p_name' => $value['p_name'],
					'disable' => $checkAble
				);
				//获取用户上次选择的红包
				if(isset($order['redpackId']) && ($order['redpackId'] == $info['id']))
				{
					$selected = $info;
					continue;
				}
				if(empty($checkAble))
				{
					$usable[] = $info;
				}
				else
				{
					$disable[] = $info;
				}
			}
			
			$datas = array_merge($usable, $disable);
			if($selected)
			{
				array_unshift($datas, $selected);
			}
		}
		
		return $datas;
	}
}
