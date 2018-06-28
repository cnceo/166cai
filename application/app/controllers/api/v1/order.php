<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/*
 * APP 创建订单接口调用
 * @date:2016-01-18
 */
class Order extends MY_Controller 
{
	// 获取彩种信息
	private $lotteryInfo = array(
		'21406' => array(
			'cache' => 'SYXW',
			'maxIssue' => 87
		),
		'21407' => array(
			'cache' => 'JXSYXW',
			'maxIssue' => 84
		),
		'21408' => array(
			'cache' => 'HBSYXW',
			'maxIssue' => 81
		),
	    '21421' => array(
	        'cache' => 'GDSYXW',
	        'maxIssue' => 88
	    ),
		'23529' => array(
			'cache' => 'DLT',
			'maxIssue' => 50
		),
		'33' => array(
			'cache' => 'PLS',
			'maxIssue' => 50
		),
		'35' => array(
			'cache' => 'PLW',
			'maxIssue' => 50
		),
		'23528' => array(
			'cache' => 'QLC',
			'maxIssue' => 50
		),
		'10022' => array(
			'cache' => 'QXC',
			'maxIssue' => 50
		),
		'51' => array(
			'cache' => 'SSQ',
			'maxIssue' => 50
		),
		'52' => array(
			'cache' => 'FCSD',
			'maxIssue' => 50
		),
		'53' => array(
			'cache' => 'KS',
			'maxIssue' => 82
		),
		'56' => array(
			'cache' => 'JLKS',
			'maxIssue' => 87
		),
		'54' => array(
			'cache' => 'KLPK',
			'maxIssue' => 88
		),
		'55' => array(
			'cache' => 'CQSSC',
			'maxIssue' => 120
		),
                '57' => array(
			'cache' => 'JXKS',
			'maxIssue' => 88
		),
	);

	public function __construct() 
	{
		parent::__construct();
		$this->load->library('tools');
		$this->load->model('order_model', 'Order');
		$this->load->model('cache_model','Cache');
		$this->versionInfo = $this->getRequestHeaders();
	}

	public function index()
	{
		$result = array(
            'status' => '1',
            'msg' => '通讯成功',
            'data' => $this->getRequestHeaders()
        );
        echo json_encode($result); 
	}

	/*
 	 * 创建订单接口
 	 * @date:2016-01-18
 	 */
	public function creadOrder()
	{
		$data = $this->strCode($this->input->post('data'));
		$data = json_decode($data, true);

		$headerInfo = $this->getRequestHeaders();

		// log_message('LOG', "创建订单开始 - 请求参数: " . json_encode($headerInfo), 'bn_creadOrder');
		$result = array(
			'status' => '0',
			'msg' => '通讯异常',
			'data' => ''
		);

		$params = array(
			'ctype' => '',
			'uid' => '',
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

		// 必要参数检查
		foreach ($params as $key => $items) 
		{
			if($data[$key] === '' || !isset($data[$key]))
			{
				$result = array(
					'status' => '0',
					'msg' => '缺少必要参数',
					'data' => ''
				);
				echo json_encode($result);
				exit();
			}
		}

		// 检查用户登录状态
		$this->load->model('user_model');
		$uinfo = $this->user_model->getUserInfo($data['uid']);

		if(empty($uinfo))
		{
			$result = array(
				'status'	=>	($this->versionInfo['appVersionCode'] >= '40200') ? '700' : '300',
				'msg' 		=> 	'用户登录信息过期',
				'data' 		=> 	''
			);
			echo json_encode($result);
			exit();
		}

		// 获取版本信息
		$headerInfo = $this->getRequestHeaders();

		// 用户是否注销
		if(isset($uinfo['userStatus']) && $uinfo['userStatus'] == '1' && $headerInfo['appVersionCode'] >= '3')
		{
			$result = array(
				'status'	=>	($this->versionInfo['appVersionCode'] >= '40200') ? '700' : '300',
				'msg' 		=> 	'您的账号已注销，被注销的账号不能使用原手机号再注册，请注册新账号登录',
				'data' 		=> 	''
			);
			echo json_encode($result);
			exit();
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

		$uinfo['uid'] = $data['uid'];

		if(!$this->checkUserAuth($uinfo, $data['auth']) && $headerInfo['appVersionCode'] >= '3')
		{
			$result = array(
				'status'	=>	($this->versionInfo['appVersionCode'] >= '40200') ? '700' : '300',
				'msg' 		=> 	'您的登录密码已修改，请重新登录',
				'data' 		=> 	''
			);
			echo json_encode($result);
			exit();
		}
		unset($data['auth']);

		// 单设备登录检查
        $checkData = $this->checkUserLogin($uinfo['uid']);
        if(!$checkData['status'])
        {
            $result = array(
                'status'    =>  $checkData['code'],
                'msg'       =>  $checkData['msg'],
                'data'      =>  '',
            );
            echo json_encode($result);
            exit();
        }

		// 订单金额范围检查
		if(in_array($data['lid'], array(42, 43)))
		{
			if($data['money'] > 200000)
	        {
	        	$result = array(
					'status' => '0',
					'msg' => '订单金额需小于20万，请修改订单后重新投注',
					'data' => ''
				);
				echo json_encode($result);
				exit();
	        }
		}
		else
		{
			if($data['money'] > 20000)
	        {
	        	$result = array(
					'status' => '0',
					'msg' => '订单金额需小于2万，请修改订单后重新投注',
					'data' => ''
				);
				echo json_encode($result);
				exit();
	        }
		}

        // 版本彩种销售判断
        $appConfig = $this->Cache->getAppConfig('android');
        if(!empty($appConfig[$headerInfo['appVersionCode']]['lotteryConfig']))
        {
        	$saleConfig = json_decode($appConfig[$headerInfo['appVersionCode']]['lotteryConfig'], true);	
        	if(isset($saleConfig[$data['lid']]) && $saleConfig[$data['lid']] == '1')
        	{
        		$result = array(
					'status' => '0',
					'msg' => '当前彩种已停售',
					'data' => ''
				);
				echo json_encode($result);
				exit();
        	}
        }
        
        // 截止时间处理
        $issueInfo = $this->Cache->getIssueInfo($data['lid']);
        $lotteryConfig = $this->Cache->getlotteryConfig();

        if(in_array($data['lid'], array('42','43')))
		{
			if($data['codecc'] === '')
			{
				$result = array(
					'status' => '0',
					'msg' => '缺少必要参数',
					'data' => ''
				);
				echo json_encode($result);
				exit();
			}
		}
		else
		{
			// 期次信息检查
			if($issueInfo['cIssue']['seExpect'] != $data['issue'])
			{
				$result = array(
					'status' => '0',
					'msg' => '投注不在当前销售期',
					'data' => ''
				);
				echo json_encode($result);
				exit();
			}
		}

        if (!empty($issueInfo['aIssue']) && in_array($data['lid'], array('51', '23529', '35', '10022', '23528', '33', '52')) && time() > (floor($issueInfo['aIssue']['seEndtime']/1000)-$lotteryConfig[$data['lid']]['ahead']*60) && time() < floor($issueInfo['aIssue']['seEndtime']/1000))
        {
            $result = array(
				'status' => '0',
				'msg' => '期次更新中，请于' . date('H:i', (floor($issueInfo['aIssue']['seEndtime']/1000))) . '后投注下期' . $issueInfo['cIssue']['seExpect'],
				'data' => ''
			);
			echo json_encode($result);
			exit();
        }

        // 停售
        $is_sale = $this->config->item('is_sale');
		if(!$is_sale)
		{
			$result = array(
				'status' => '0',
				'msg' => '当前彩种已停售',
				'data' => ''
			);
			echo json_encode($result);
			exit();
		}

        // 初始化订单信息
        if(ENVIRONMENT === 'checkout')
		{
			$orderUrl = $this->config->item('cp_host');
			$data['HOST'] = $this->config->item('domain');
		}
		else
		{
			// $orderUrl = $this->config->item('pages_url');
			$orderUrl = $this->config->item('protocol') . $this->config->item('pages_url');
		}

		// 版本 渠道信息处理
		$data['version'] = (isset($headerInfo['appVersionName']) && !empty($headerInfo['appVersionName'])) ? $headerInfo['appVersionName'] : '1.0';
		if($data['remark'])
		{
			$data['version'] .= '_' . $data['remark'];
			unset($data['remark']);
		}
		$data['channel'] = $this->recordChannel($headerInfo['channel']);
		// V3.3新增购彩红包
		$data['redpackId'] = $data['redpackId'] ? $data['redpackId'] : '';
		// 新增预测奖金
		$data['forecastBonus'] = $data['forecastBonusv'] ? $data['forecastBonusv'] : '';

		//临时关闭部分渠道包购彩
		$channelArr = $this->Cache->getLimitChannel();
		if(in_array($data['channel'], $channelArr))
		{
			$result = array(
				'status' => '0',
				'msg' => '暂停售彩',
				'data' => ''
			);
			echo json_encode($result);
			exit();
		}
		$data['buyPlatform'] = $this->config->item('platform');
		
        $data['ctype'] = 'create';
        // 处理空格问题
        $data['issue'] = trim($data['issue']);
		$createStatus = $this->tools->request($orderUrl . 'api/order/createOrder', $data);
		$createStatus = json_decode($createStatus, true);

		if($createStatus['status'])
		{
			// 创建结果处理
			$payView = $this->orderComplete($createStatus['data']);
			$result = array(
				'status' => '1',
				'msg' => '创建订单成功',
				'data' => $payView
			);
			echo json_encode($result);
			exit();
		}
		else
		{
			$result = array(
				'status' => '0',
				'msg' => $createStatus['msg'],
				'data' => ''
			);
			echo json_encode($result);
			exit();
		}
	}

    /*
	 * APP 订单创建成功处理
	 * @date:2016-01-18
	 */
    public function orderComplete($data, $orderType = 0)
    {
		// 订单信息加密
		$orderDetail = $this->strCode(json_encode(array(
			'uid' => $data['uid'],
			'orderId' => $orderType ? $data['chaseId'] : $data['orderId'],
			'orderType' => $orderType
		)), 'ENCODE');

    	// 跳转支付页面
    	$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https:" : "http:";
    	$payView = $protocol . $this->config->item('pages_url') . "app/order/doPay/" . urlencode($orderDetail);
		return $payView;
    }


    /*
 	 * 创建追号订单接口
 	 * @date:2016-03-03
 	 */
	public function createChaseOrder()
	{
		$data = $this->strCode($this->input->post('data'));
		$data = json_decode($data, true);
		// log_message('LOG', "创建订单开始 - 请求参数: " . json_encode($data), 'createChaseOrder');

		$result = array(
			'status' => '0',
			'msg' => '通讯异常',
			'data' => ''
		);

		$params = array(
			'uid' => '',
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
			'endTime' => '',
			'totalIssue' => '',
			'setStatus' => '',
			'setMoney' => ''
		);

		// 必要参数检查
		foreach ($params as $key => $items) 
		{
			if($data[$key] === '' || !isset($data[$key]))
			{
				$result = array(
					'status' => '0',
					'msg' => '缺少必要参数',
					'data' => $key
				);
				echo json_encode($result);
				exit();
			}
		}

		// 检查用户登录状态
		$this->load->model('user_model');
		$uinfo = $this->user_model->getUserInfo($data['uid']);

		if(empty($uinfo))
		{
			$result = array(
				'status'	=>	($this->versionInfo['appVersionCode'] >= '40200') ? '700' : '300',
				'msg' 		=> 	'用户登录信息过期',
				'data' 		=> 	''
			);
			echo json_encode($result);
			exit();
		}

		// 获取版本信息
		$headerInfo = $this->getRequestHeaders();

		// 用户是否注销
		if(isset($uinfo['userStatus']) && $uinfo['userStatus'] == '1' && $headerInfo['appVersionCode'] >= '3')
		{
			$result = array(
				'status'	=>	($this->versionInfo['appVersionCode'] >= '40200') ? '700' : '300',
				'msg' 		=> 	'您的账号已注销，被注销的账号不能使用原手机号再注册，请注册新账号登录',
				'data' 		=> 	''
			);
			echo json_encode($result);
			exit();
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

		$uinfo['uid'] = $data['uid'];

		if(!$this->checkUserAuth($uinfo, $data['auth']) && $headerInfo['appVersionCode'] >= '3')
		{
			$result = array(
				'status'	=>	($this->versionInfo['appVersionCode'] >= '40200') ? '700' : '300',
				'msg' 		=> 	'您的登录密码已修改，请重新登录',
				'data' 		=> 	''
			);
			echo json_encode($result);
			exit();
		}
		unset($data['auth']);

		// 单设备登录检查
        $checkData = $this->checkUserLogin($uinfo['uid']);
        if(!$checkData['status'])
        {
            $result = array(
                'status'    =>  $checkData['code'],
                'msg'       =>  $checkData['msg'],
                'data'      =>  '',
            );
            echo json_encode($result);
            exit();
        }

		if(in_array($data['lid'], array('42','43')))
		{
			$result = array(
				'status' => '0',
				'msg' => '该彩种不支持追号',
				'data' => ''
			);
			echo json_encode($result);
			exit();
		}

        // 停售
        $is_sale = $this->config->item('is_sale');
		if(!$is_sale)
		{
			$result = array(
				'status' => '0',
				'msg' => '当前彩种已停售',
				'data' => ''
			);
			echo json_encode($result);
			exit();
		}

		// 获取版本信息
		$headerInfo = $this->getRequestHeaders();

		// 版本 渠道信息处理
		$data['version'] = (isset($headerInfo['appVersionName']) && !empty($headerInfo['appVersionName'])) ? $headerInfo['appVersionName'] : '1.0';
		$data['channel'] = $this->recordChannel($headerInfo['channel']);
		//临时关闭部分渠道包购彩
		$channelArr = $this->Cache->getLimitChannel();
		if(in_array($data['channel'], $channelArr))
		{
			$result = array(
				'status' => '0',
				'msg' => '暂停售彩',
				'data' => ''
			);
			echo json_encode($result);
			exit();
		}
		$data['orderType'] = 1;		// 购买类型：追号
		$data['buyPlatform'] = $this->config->item('platform');

		// 版本彩种销售判断
        $appConfig = $this->Cache->getAppConfig('android');
        if(!empty($appConfig[$headerInfo['appVersionCode']]['lotteryConfig']))
        {
        	$saleConfig = json_decode($appConfig[$headerInfo['appVersionCode']]['lotteryConfig'], true);	
        	if(isset($saleConfig[$data['lid']]) && $saleConfig[$data['lid']] == '1')
        	{
        		$result = array(
					'status' => '0',
					'msg' => '当前彩种已停售',
					'data' => ''
				);
				echo json_encode($result);
				exit();
        	}
        }

		// 截止时间处理
        $issueInfo = $this->Cache->getIssueInfo($data['lid']);
        $lotteryConfig = $this->Cache->getlotteryConfig();

        if (!empty($issueInfo['aIssue']) && in_array($data['lid'], array('51', '23529', '35', '10022', '23528', '33', '52')) && time() > (floor($issueInfo['aIssue']['seEndtime']/1000)-$lotteryConfig[$data['lid']]['ahead']*60) && time() < floor($issueInfo['aIssue']['seEndtime']/1000))
        {
            $result = array(
				'status' => '0',
				'msg' => '期次更新中，请于' . date('H:i', (floor($issueInfo['aIssue']['seEndtime']/1000))) . '后投注下期' . $issueInfo['cIssue']['seExpect'],
				'data' => ''
			);
			echo json_encode($result);
			exit();
        }

		// 检查投注截止时间
		if($data['endTime'] <= date('Y-m-d H:i:s'))
		{
			$result = array(
				'status' => '0',
				'msg' => '此彩种已过投注结束时间！',
				'data' => ''
			);
			echo json_encode($result);
			exit();
		}

		// 获取追号投注格式
		$chaseResult = $this->getChaseData($data);

		if(!$chaseResult['status'])
		{
			$result = array(
				'status' => '0',
				'msg' => $chaseResult['msg'],
				'data' => ''
			);
			echo json_encode($result);
			exit();
		}

		// 初始化订单信息
        if(ENVIRONMENT === 'checkout')
		{
			$orderUrl = $this->config->item('cp_host');
			$chaseResult['data']['HOST'] = $this->config->item('domain');
		}
		else
		{
			// $orderUrl = $this->config->item('pages_url');
			$orderUrl = $this->config->item('protocol') . $this->config->item('pages_url');
		}
		
		$createStatus = $this->tools->request($orderUrl . 'api/order/createChaseOrder', $chaseResult['data']);
		$createStatus = json_decode($createStatus, true);
		
		if($createStatus['status'])
		{
			// 创建结果处理
			$payView = $this->orderComplete($createStatus['data'], $data['orderType']);
			$result = array(
				'status' => '1',
				'msg' => '创建订单成功',
				'data' => $payView
			);
			echo json_encode($result);
			exit();
		}
		else
		{
			$result = array(
				'status' => '0',
				'msg' => $createStatus['msg'],
				'data' => ''
			);
			echo json_encode($result);
			exit();
		}
	}

	/*
 	 * 创建追号订单接口
 	 * @date:2016-03-03
 	 */
	public function getChaseData($orderData)
	{
		// 获取期次信息
		$lotteryInfo = $this->lotteryInfo;

		// 检查期次信息
		if (!preg_match('/\d+$/', $orderData['totalIssue']))
		{
			$result = array(
				'status' => '0',
				'msg' => '追号期次格式错误',
				'data' => ''
			);
			return $result;
		}
		else
		{
			if($orderData['totalIssue'] <= 1)
			{
				$result = array(
					'status' => '0',
					'msg' => '追号期次错误',
					'data' => ''
				);
				return $result;
			}

			if($orderData['totalIssue'] > $lotteryInfo[$orderData['lid']]['maxIssue'])
			{
				$result = array(
					'status' => '0',
					'msg' => '追号期次超出最大范围',
					'data' => ''
				);
				return $result;
			}
		}

		// 获取追号期次缓存
        $REDIS = $this->config->item('REDIS');
        $followIssues = json_decode($this->cache->hGet($REDIS['ISSUE_COMING'], $lotteryInfo[$orderData['lid']]['cache']), true);

        if(empty($followIssues))
        {
        	$result = array(
				'status' => '0',
				'msg' => '追号期次获取失败',
				'data' => ''
			);
			return $result;
        }

        $index = '-1';
        foreach ($followIssues as $key => $issueData) 
        {
        	if($this->getIssueFormat($issueData['issue'], $orderData['lid']) == $orderData['issue'])
        	{
        		$index = $key;
        	}
        }

        if($index == '-1')
        {
        	$result = array(
				'status' => '0',
				'msg' => '追号期次获取失败',
				'data' => ''
			);
			return $result;
        }

        $followIssues = array_slice($followIssues, $index, $orderData['totalIssue']);

        if($orderData['totalIssue'] > count($followIssues))
        {
        	$result = array(
				'status' => '0',
				'msg' => '追号期次获取失败',
				'data' => ''
			);
			return $result;
        }

        // 处理追号方案格式
        $chaseDetail = $this->getChasePlan($followIssues, $orderData);

		$parmas = array(
            'uid' => $orderData['uid'],
            'userName' => $orderData['userName'],
            'buyPlatform' => $this->config->item('platform'),
            'codes' => $orderData['codes'],
            'lid' => $orderData['lid'],
            'money' => $orderData['money'],
            'playType' => $orderData['playType'],
            'betTnum' => $orderData['betTnum'],
            'isChase' => $orderData['isChase'],
            'totalIssue' => $orderData['totalIssue'],
            'setStatus' => $orderData['setStatus'],
            'setMoney' => $orderData['setMoney'],
            'endTime' => $orderData['endTime'],
            'orderType' => '1',
            'chaseType' => $orderData['chaseType'] ? $orderData['chaseType'] : '0',
            'channel' => $orderData['channel'],
            'app_version' => $orderData['version'],
        );

        if($orderData['remark'])
		{
			$parmas['app_version'] .= '_' . $orderData['remark'];
		}

        $parmas['chaseDetail'] = json_encode($chaseDetail);

        $result = array(
			'status' => '1',
			'msg' => '追号投注方案',
			'data' => $parmas
		);
		return $result;
	}

	/*
 	 * 处理追号方案格式
 	 * @date:2016-03-03
 	 */
	public function getChasePlan($followIssues, $orderData)
	{
		$chaseDetail = array();
		foreach ($followIssues as $key => $items) 
		{
			$chaseDetail[$key]['issue'] = $this->getIssueFormat($items['issue'], $orderData['lid']);
			$chaseDetail[$key]['multi'] = $orderData['multi'];
			$chaseDetail[$key]['money'] = $orderData['money']/$orderData['totalIssue'];
			$chaseDetail[$key]['award_time'] = $items['award_time'];
			$chaseDetail[$key]['endTime'] = $items['show_end_time'];
		}
		return $chaseDetail;
	}

	/*
 	 * 处理追号方案格式
 	 * @date:2016-03-03
 	 */
	public function getIssueFormat($issue, $lid)
	{
		$this->load->library('libcomm');
		switch ($lid) 
		{
			case '23529':
			case '33':
			case '35':
			case '10022':
				$issue = $this->libcomm->format_issue($issue, 1, 2);
				break;
			
			default:
				$issue = $issue;
				break;
		}
		return $issue;
	}

	/*
 	 * APP 订单详情提交支付
 	 * @date:2015-06-15
 	 */
	public function orderPay()
	{
		$data = $this->strCode($this->input->post('codeStr'));
		$data = json_decode($data, true);

		// 调试
		// $data = array(
		// 	'uid' => '108',
		// 	'orderId' => '20160317131934502678',
		// 	'orderType' => '1'
		// );
		if( isset($data['uid']) && isset($data['orderId']) && isset($data['orderType']) && !empty($data['uid']) && !empty($data['orderId']) )
		{
			// 追号订单
			if($data['orderType'] == '1')
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
					if( $order['info']['endTime'] < date('Y-m-d H:i:s') )
					{
						$result = array(
							'status' => '0',
							'msg' => '付款失败,订单超过投注截止时间',
							'data' => ''
						);
						echo json_encode($result);
						exit();
					}

					if( $order['info']['status'] == $orderStatus['create'] )
					{
						// 组装订单参数
						$orderData = array(
							'uid' => $data['uid'],
							'chaseId' => $data['orderId']
						);
						$payView = $this->orderComplete($orderData, $data['orderType']);
						$result = array(
							'status' => '1',
							'msg' => '提交付款',
							'data' => $payView
						);
					}
					else
					{
						$result = array(
							'status' => '0',
							'msg' => '订单支付状态失效',
							'data' => ''
						);
					}
				}
				else
				{
					$result = array(
						'status' => '0',
						'msg' => '订单信息错误',
						'data' => ''
					);
				}
			}
			else
			{
				// 获取订单信息
				$order = $this->Order->getById($data['orderId']);
				if(!empty($order))
				{
					// 截止时间判断
					if( $order['endTime'] < date('Y-m-d H:i:s') )
					{
						$result = array(
							'status' => '0',
							'msg' => '付款失败,订单超过投注截止时间',
							'data' => ''
						);
						echo json_encode($result);
						exit();
					}

					// 订单状态判断
					if( $order['status'] == 10 && $order['uid'] == $data['uid'] )
					{
						$redpackId = $data['redpackId'] ? $data['redpackId'] : 0;
						// 购彩红包判断
						$checkData = $this->checkBetRedpack($order, $redpackId);
						if(!$checkData['status'])
						{
							$result = array(
								'status' => '0',
								'msg' => $checkData['msg'],
								'data' => ''
							);
							echo json_encode($result);
							exit();
						}
						
						// 组装订单参数
						$orderData = array(
							'uid' => $data['uid'],
							'orderId' => $data['orderId']
						);
						$payView = $this->orderComplete($orderData, $data['orderType']);
						$result = array(
							'status' => '1',
							'msg' => '提交付款',
							'data' => $payView
						);
					}
					else
					{
						$result = array(
							'status' => '0',
							'msg' => '订单支付状态失效',
							'data' => ''
						);
					}
				}
				else
				{
					$result = array(
						'status' => '0',
						'msg' => '订单信息错误',
						'data' => ''
					);
				}
			}		
		}
		else
		{
			$result = array(
				'status' => '0',
				'msg' => '订单参数缺失',
				'data' => ''
			);
		}
		echo json_encode($result);
		exit();
	}

	/*
 	 * 获取追号期次缓存
 	 * @date:2016-08-08
 	 */
	public function getChaseIssue($lotteryId)
	{
		// 获取期次信息
		$lotteryInfo = $this->lotteryInfo;

		$this->load->driver('cache', array('adapter' => 'redis'));
        $REDIS = $this->config->item('REDIS');
        $followIssues = json_decode($this->cache->hGet($REDIS['ISSUE_COMING'], $lotteryInfo[$lotteryId]['cache']), true);
        return $followIssues;
	}

	/*
 	 * 智能追号投注
 	 * @date:2016-08-08
 	 */
	public function smartChase()
	{
		$data = $this->strCode($this->input->post('data'));
		$data = json_decode($data, true);
		
		$result = array(
			'status' => '0',
			'msg' => '通讯异常',
			'data' => ''
		);

		$params = array(
			'uid' => '',
    		'userName' => '',
			'buyPlatform' => '',
			'codes' => '',
			'lid' => '',
			'money' => '',
			'playType' => '',
			'isChase' => '',
			'betTnum' => '',
			'endTime' => '',
			'chaseDetail' => '',
			'setStatus' => '',
			'setMoney' => ''
		);

		// 必要参数检查
		foreach ($params as $key => $items) 
		{
			if($data[$key] === '' || !isset($data[$key]))
			{
				$result = array(
					'status' => '0',
					'msg' => '缺少必要参数',
					'data' => $key
				);
				echo json_encode($result);
				exit();
			}
		}

		// 检查用户登录状态
		$this->load->model('user_model');
		$uinfo = $this->user_model->getUserInfo($data['uid']);

		if(empty($uinfo))
		{
			$result = array(
				'status'	=>	($this->versionInfo['appVersionCode'] >= '40200') ? '700' : '300',
				'msg' 		=> 	'用户登录信息过期',
				'data' 		=> 	''
			);
			echo json_encode($result);
			exit();
		}

		// 用户是否注销
		if(isset($uinfo['userStatus']) && $uinfo['userStatus'] == '1')
		{
			$result = array(
				'status'	=>	($this->versionInfo['appVersionCode'] >= '40200') ? '700' : '300',
				'msg' 		=> 	'您的账号已注销，被注销的账号不能使用原手机号再注册，请注册新账号登录',
				'data' 		=> 	''
			);
			echo json_encode($result);
			exit();
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

		$uinfo['uid'] = $data['uid'];

		if(!$this->checkUserAuth($uinfo, $data['auth']))
		{
			$result = array(
				'status'	=>	($this->versionInfo['appVersionCode'] >= '40200') ? '700' : '300',
				'msg'		=> 	'您的登录密码已修改，请重新登录',
				'data' 		=> 	''
			);
			echo json_encode($result);
			exit();
		}
		unset($data['auth']);

		// 单设备登录检查
        $checkData = $this->checkUserLogin($uinfo['uid']);
        if(!$checkData['status'])
        {
            $result = array(
                'status'    =>  $checkData['code'],
                'msg'       =>  $checkData['msg'],
                'data'      =>  '',
            );
            echo json_encode($result);
            exit();
        }

		if(in_array($data['lid'], array('42','43')))
		{
			$result = array(
				'status' => '0',
				'msg' => '该彩种不支持追号',
				'data' => ''
			);
			echo json_encode($result);
			exit();
		}

        // 停售
        $is_sale = $this->config->item('is_sale');
		if(!$is_sale)
		{
			$result = array(
				'status' => '0',
				'msg' => '当前彩种已停售',
				'data' => ''
			);
			echo json_encode($result);
			exit();
		}

		// 获取版本信息
		$headerInfo = $this->getRequestHeaders();

        // 版本彩种销售判断
        $appConfig = $this->Cache->getAppConfig('android');
        if(!empty($appConfig[$headerInfo['appVersionCode']]['lotteryConfig']))
        {
        	$saleConfig = json_decode($appConfig[$headerInfo['appVersionCode']]['lotteryConfig'], true);	
        	if(isset($saleConfig[$data['lid']]) && $saleConfig[$data['lid']] == '1')
        	{
        		$result = array(
					'status' => '0',
					'msg' => '当前彩种已停售',
					'data' => ''
				);
				echo json_encode($result);
				exit();
        	}
        }

		// 版本 渠道信息处理
		$data['version'] = (isset($headerInfo['appVersionName']) && !empty($headerInfo['appVersionName'])) ? $headerInfo['appVersionName'] : '1.0';
		$data['channel'] = $this->recordChannel($headerInfo['channel']);
		//临时关闭部分渠道包购彩
		$channelArr = $this->Cache->getLimitChannel();
		if(in_array($data['channel'], $channelArr))
		{
			$result = array(
				'status' => '0',
				'msg' => '暂停售彩',
				'data' => ''
			);
			echo json_encode($result);
			exit();
		}
		$data['orderType'] = 1;		// 购买类型：追号
		$data['buyPlatform'] = $this->config->item('platform');

		// 检查投注截止时间
		if($data['endTime'] <= date('Y-m-d H:i:s'))
		{
			$result = array(
				'status' => '0',
				'msg' => '此彩种已过投注结束时间！',
				'data' => ''
			);
			echo json_encode($result);
			exit();
		}

		// 获取智能追号投注格式
		$chaseResult = $this->getSmartChaseData($data);

		if(!$chaseResult['status'])
		{
			$result = array(
				'status' => '0',
				'msg' => $chaseResult['msg'],
				'data' => ''
			);
			echo json_encode($result);
			exit();
		}

		// 初始化订单信息
        if(ENVIRONMENT === 'checkout')
		{
			$orderUrl = $this->config->item('cp_host');
			$chaseResult['data']['HOST'] = $this->config->item('domain');
		}
		else
		{
			// $orderUrl = $this->config->item('pages_url');
			$orderUrl = $this->config->item('protocol') . $this->config->item('pages_url');
		}
		
		$createStatus = $this->tools->request($orderUrl . 'api/order/createChaseOrder', $chaseResult['data']);
		$createStatus = json_decode($createStatus, true);
		
		if($createStatus['status'])
		{
			// 创建结果处理
			$payView = $this->orderComplete($createStatus['data'], $data['orderType']);
			$result = array(
				'status' => '1',
				'msg' => '创建订单成功',
				'data' => $payView
			);
			echo json_encode($result);
			exit();
		}
		else
		{
			$result = array(
				'status' => '0',
				'msg' => $createStatus['msg'],
				'data' => ''
			);
			echo json_encode($result);
			exit();
		}
	}

	/*
 	 * 智能追号投注
 	 * @date:2016-08-08
 	 */
	public function getSmartChaseData($orderData)
	{	
		// 获取追号缓存信息
		$followIssues = $this->Cache->getSmartIssue($orderData['lid']);

		$chaseDetail = $this->getSmartChasePlan($followIssues, $orderData);

		if(!empty($chaseDetail))
		{
			$parmas = array(
	            'uid' => $orderData['uid'],
	            'userName' => $orderData['userName'],
	            'buyPlatform' => $this->config->item('platform'),
	            'codes' => $orderData['codes'],
	            'lid' => $orderData['lid'],
	            'money' => $orderData['money'],
	            'playType' => $orderData['playType'],
	            'betTnum' => $orderData['betTnum'],
	            'isChase' => $orderData['isChase'],
	            'totalIssue' => count($chaseDetail),
	            'setStatus' => $orderData['setStatus'],
	            'setMoney' => $orderData['setMoney'],
	            'endTime' => $orderData['endTime'],
	            'orderType' => '1',
	            'channel' => $orderData['channel'],
	            'app_version' => $orderData['version'],
	        );
			
			$parmas['chaseDetail'] = json_encode($chaseDetail);

	        $result = array(
				'status' => '1',
				'msg' => '追号投注方案',
				'data' => $parmas
			);
		}
		else
		{
			$result = array(
				'status' => '0',
				'msg' => '追号方案期次有误，请重新获取',
				'data' => ''
			);
		}

		return $result;
	}

	/*
 	 * 获取智能追号投注方案详情
 	 * @date:2016-08-08
 	 */
	public function getSmartChasePlan($followIssues, $orderData)
	{
		// 追号详情
		$chaseDetail = array();
		// 期次缓存
		$chaseIssue = array();

		$orderData['chaseDetail'] = json_decode($orderData['chaseDetail'], true);

		if(!empty($followIssues) && !empty($orderData['chaseDetail']))
		{
			foreach ($followIssues as $key => $items)
			{
				$chaseIssue[$items['issue']] = $items;
			}

			foreach ($orderData['chaseDetail'] as $key => $items) 
			{
				if(!empty($chaseIssue[$items['issue']]))
				{
					$data = array(
						'issue' 		=>	$items['issue'],
						'multi' 		=>	$items['multi'],
						'money'			=>	$items['money'],
						'award_time'	=>	$chaseIssue[$items['issue']]['award_time'],
						'endTime'		=>	$chaseIssue[$items['issue']]['show_end_time'],
					);
					array_push($chaseDetail, $data);
				}
				else
				{
					return array();
				}
			}
		}

		return $chaseDetail;
	}

	/*
 	 * 获取购彩红包信息
 	 * @date:2017-04-28
 	 */
	public function getBetRedPack()
	{
		$data = $this->strCode($this->input->post('data'));
		$data = json_decode($data, true);

		$result = array(
			'status' => '0',
			'msg' => 'error',
			'data' => array(
				'redpacks'	=>	array()
			)
		);

		if(!empty($data['orderId']))
		{
			$orderData = $this->Order->getOrder(array(
				'uid' 		=>	$data['uid'],
				'orderId' 	=> 	$data['orderId'],
			));

			$orderInfo = $orderData['data'];
			// 自购订单
			if(!empty($orderInfo) && $orderInfo['orderType'] == '0')
			{
				// 组装参数
				$postData = array(
					'uid'			=>	$data['uid'],
					'lid'   		=> 	$orderInfo['lid'],
		            'money'			=> 	$orderInfo['money'],
		            'buyPlatform'	=>	$orderInfo['buyPlatform'],
				);
			}
		}
		else
		{
			// 组装参数
			$postData = array(
				'uid'			=>	$data['uid'],
				'lid'   		=> 	$data['lid'],
	            'money'			=> 	ParseUnit($data['money']),	// 分
	            'buyPlatform'	=>	$this->config->item('platform'),
			);
		}

		if(empty($postData['uid']) || empty($postData['lid']) || empty($postData['money']))
		{
			$result = array(
				'status' => '1',
				'msg' => 'success',
				'data' => array(
					'redpacks'	=>	array()
				)
			);
			echo json_encode($result);
			exit();
		}

		// 初始化订单信息
        if(ENVIRONMENT === 'checkout')
		{
			$postUrl = $this->config->item('cp_host');
			$postData['HOST'] = $this->config->item('domain');
		}
		else
		{
			// $postUrl = $this->config->item('pages_url');
			$postUrl = $this->config->item('protocol') . $this->config->item('pages_url');
		}

		$responeData = $this->tools->request($postUrl . 'api/redpack/getBetRedPack', $postData);
		$responeData = json_decode($responeData, true);

		if($responeData['status'] == '200')
		{
			$result = array(
				'status' => '1',
				'msg' => 'success',
				'data' => array(
					'redpacks'	=>	$responeData['data']
				)
			);
		}
		echo json_encode($result);
		exit();
	}

	public function checkBetRedpack($orderInfo, $redpackId)
	{
		$result = array(
			'status' => FALSE,
			'msg' => '订单支付失败，红包不符合使用条件！'
		);

		//不使用红包
    	if($redpackId == '0')
    	{
    		if(($orderInfo['redpackId'] != $redpackId) || ($orderInfo['redpackId'] == '' && $orderInfo['redpackMoney'] == ''))
    		{
    			$orderDetail = array(
    				'orderId' => $orderInfo['orderId'],
    				'redpackId' => 0,
    				'redpackMoney' => 0,
    			);
    			$this->Order->insertOrderDetail($orderDetail);
    		}
    		$result = array(
				'status' => TRUE,
				'msg' => '红包更新成功'
			);
    	}
    	else
    	{
    		$this->load->model('redpack_model');
    		$redpack = $this->redpack_model->getRedpackById($orderInfo['uid'], $redpackId);
    		if($redpack)
    		{
    			$this->load->config('order');
	    		$cType = $this->config->item("redpack_c_type");
	    		if((!in_array($redpack['c_type'], $cType[$orderInfo['lid']])) || ($orderInfo['money'] < $redpack['money_bar'])
	    				|| (($orderInfo['buyPlatform'] == '0') && !empty($redpack['ismobile_used'])) || (($redpack['valid_start'] > date('Y-m-d H:i:s')) || ($redpack['valid_end'] < date('Y-m-d H:i:s'))))
	    		{
	    			$result = array(
						'status' => FALSE,
						'msg' => '订单支付失败，红包不符合使用条件！'
					);
					return $result;
	    		}
	    		if($orderInfo['redpackId'] != $redpackId)
	    		{
	    			$orderDetail = array(
	    				'orderId' => $orderInfo['orderId'],
	    				'redpackId' => $redpackId,
	    				'redpackMoney' => $redpack['money'],
	    			);
	    			$res = $this->Order->insertOrderDetail($orderDetail);
	    			if($res)
	    			{
	    				$result = array(
							'status' => TRUE,
							'msg' => '红包更新成功'
						);
	    			}
	    		}
	    		else
	    		{
	    			$result = array(
						'status' => TRUE,
						'msg' => '红包更新成功'
					);
	    		}
    		}
    	}
    	return $result;
	}

}