<?php
date_default_timezone_set('PRC');
/*
 * APP 红包
 * @date:2016-02-03
 */

 class Redpack extends MY_Controller 
{
	private $redpackType = array(
		'1' => '彩金红包',
		'2' => '充值红包'
	);
	
	private $lotteryUrl = array(
		'105' => "window.webkit.messageHandlers.doBet.postMessage({lid:'51'})",
		'106' => "window.webkit.messageHandlers.doBet.postMessage({lid:'23529'})",
		'107' => "window.webkit.messageHandlers.doBet.postMessage({lid:'52'})",
		'108' => "window.webkit.messageHandlers.doBet.postMessage({lid:'33'})",
		'109' => "window.webkit.messageHandlers.doBet.postMessage({lid:'35'})",
		'110' => "window.webkit.messageHandlers.doBet.postMessage({lid:'23528'})",
		'111' => "window.webkit.messageHandlers.doBet.postMessage({lid:'10022'})",
		'112' => "window.webkit.messageHandlers.doBet.postMessage({lid:'42'})",
		'113' => "window.webkit.messageHandlers.doBet.postMessage({lid:'43'})",
		'114' => "window.webkit.messageHandlers.doBet.postMessage({lid:'11'})",
		'115' => "window.webkit.messageHandlers.doBet.postMessage({lid:'19'})",
		'116' => "window.webkit.messageHandlers.doBet.postMessage({lid:'21408'})",
		'117' => "window.webkit.messageHandlers.doBet.postMessage({lid:'21406'})",
		'118' => "window.webkit.messageHandlers.doBet.postMessage({lid:'21407'})",
		'119' => "window.webkit.messageHandlers.doBet.postMessage({lid:'53'})",
		'120' => "window.webkit.messageHandlers.doBet.postMessage({lid:'54'})",
		'121' => "window.webkit.messageHandlers.doBet.postMessage({lid:'55'})",
		'122' => "window.webkit.messageHandlers.doBet.postMessage({lid:'56'})",
	    '123' => "window.webkit.messageHandlers.doBet.postMessage({lid:'57'})",
	    '124' => "window.webkit.messageHandlers.doBet.postMessage({lid:'21421'})",
	);
	public function __construct() 
	{
		parent::__construct();
		$this->load->model('redpack_model');
		$this->eventType = $this->redpack_model->getEventType();
	}

	// 红包使用状态
	public $redpackStatus = array(
		'available' => '100',	// 可用红包
		'expire' => '200',		// 即将到期
		'used' => '300',		// 已使用
		'expired' => '400'		// 已过期
	);

	/*
	 * APP 红包首页
	 * @date:2016-02-03
	 */
	public function index($token, $ctype = 1)
	{	
		// 检查参数
		$data = $this->strCode(urldecode($token));
		$data = json_decode($data, true);
		if($data['uid'] != $this->uid)
		{
			die("参数错误！");
		}

		// 查询用户红包详情
		$info = $this->redpack_model->getUserRedpacks($this->uid, 1, 1);
		$validRedpack = array();
		if(!empty($info['datas']))
		{
			foreach ($info['datas'] as $items) 
			{
				$items['eventType'] = 1;
				$validRedpack[] = $this->formatData($this->uid, $items);
			}
		}
		$historyInfo = $this->redpack_model->getUserRedpacks($this->uid, 2, 1);
		$historyRedpack = array();
		if ($historyInfo)
		{
			foreach ($historyInfo['datas'] as $items)
			{
				$items['eventType'] = 2;
				$historyRedpack[] = $this->formatData($this->uid, $items);
			}
		}
    	$this->load->view('redpack/index', array(
    		'title' => '红包',
    		'total' => $info['totals']?$info['totals']:0,
    		'validRedpack' => $validRedpack,
    		'historyRedpack' => $historyRedpack,
    		'token' => $token,
    	    'versionInfo' => $this->getUserAgentInfo()
    	));
	}

	/*
	 * APP 红包首页 AJAX
	 * @date:2016-02-03
	 */
	public function ajaxRedpack()
	{
		$eventType = $this->input->post('eventType', true);
		$page = $this->input->post('page', true);
		if(empty($this->uid))
		{
			$result = array(
				'status' => '0',
				'msg' => '用户信息获取失败',
				'data' => ''
			);
			die(json_encode($result));
		}
		// 查询用户红包详情
		$info = $this->redpack_model->getUserRedpacks($this->uid, $eventType, $page, $pageNum = 10);

		$redpack = array();
		if(!empty($info['datas']))
		{
			foreach ($info['datas'] as $key => $items) 
			{
				$items['eventType'] = $eventType;
				$redpack[] = $this->formatData($this->uid, $items);
			}
			$result = array(
				'status' => '1',
				'msg' => '加载中',
				'data' => $this->load->view('redpack/ajaxRedpack', array('redpack' => $redpack, 'eventType' => $eventType), true)
			);
		}
		else
		{
			$result = array(
				'status' => '0',
				'msg' => '无更多红包信息',
				'data' => $this->load->view('redpack/ajaxRedpack', array('redpack' => $redpack, 'eventType' => $eventType), true)
			);
		}

		die(json_encode($result));
	}
	
    /**
     * [redpackUse 彩金红包使用]
     * @author LiKangJian 2018-01-09
     * @return [type] [description]
     */
    public function redpackUse()
    {
        $res = array('code'=>400,'msg'=>'使用失败，红包不存在或已使用');
        if(empty($this->uid))
        {
            $res['code'] = '3';
            $res['msg'] = '用户信息获取失败';
            echo json_encode($res);die;
        }
        //验证实名
        if(!$this->uinfo['id_card'] || !$this->uinfo['real_name'] || !$this->uinfo['phone'] || $this->uinfo['userStatus'] !=0)
        {
            $res['code'] = '500';
            $res['msg'] = '未实名';
            echo json_encode($res);die;
        }
        $rid = $this->input->post("rid", true);
        $this->load->model('member_model');
        $tag = $this->member_model->redpackUse($this->uid,$rid);
        if($tag){
            $this->user_model->freshUserInfo($this->uid);
            echo json_encode(array('code'=>200,'msg'=>''));die;
        }else{
            echo json_encode($res);die;
        }
        
    }

	/**
	 * 彩种跳转弹窗
	 */
	public function getLotteryPop()
	{
		if(empty($this->uid))
		{
			$result = array(
				'status' => '0',
				'msg' => '用户信息获取失败',
				'data' => ''
			);
			die(json_encode($result));
		}
		$c_type = intval($this->input->post('c_type', true));
		if(in_array($c_type, array('101', '102', '103', '104')))
		{
			$data = array(
				//通用
				'101' => array('51', '23529', '42', '43', '21408', '53', '56', '52', '21406', '21407', '33', '35', '54', '11', '19', '10022', '23528', '55', '57', '21421'),
				'102' => array('42', '43', '11', '19'),
				'103' => array('51', '23529', '52', '33', '35', '10022', '23528'),
				'104' => array('21408', '53', '56', '21406', '21407', '54', '55', '57', '21421'),
			);
			$datas = array();
			foreach ($data[$c_type] as $lid)
			{
				$datas[] = $this->getLotteryData($lid);
			}
			$result = array(
				'status' => '1',
				'msg' => '加载中',
				'data' => $this->load->view('redpack/lotteryPop', array('datas' => $datas), true)
			);
		}
		else
		{
			$result = array(
				'status' => '2',
				'msg' => '操作失败，参数错误',
				'data' => ''
			);
		}
		
		die(json_encode($result));
	}
	
	/**
	 * 彩种跳转定义
	 * @param unknown_type $lid
	 */
	private function getLotteryData($lid)
	{
		$data = array(
			'51' => array(
				'name' => '双色球',
				'onclick' => 'onclick="' . $this->lotteryUrl['105'] . ';"',
				'imgUrl' => 'logo2/goucai_logo_ssq.png',
			),
			'23529' => array(
				'name' => '大乐透',
				'onclick' => 'onclick="' . $this->lotteryUrl['106'] . ';"',
				'imgUrl' => 'logo2/goucai_logo_dlt.png',
			),
			'52' => array(
				'name' => '福彩3D',
				'onclick' => 'onclick="' . $this->lotteryUrl['107'] . ';"',
				'imgUrl' => 'logo2/goucai_logo_fc3d.png',
			),
			'33' => array(
				'name' => '排列三',
				'onclick' => 'onclick="' . $this->lotteryUrl['108'] . ';"',
				'imgUrl' => 'logo2/goucai_logo_pl3.png',
			),
			'35' => array(
				'name' => '排列五',
				'onclick' => 'onclick="' . $this->lotteryUrl['109'] . ';"',
				'imgUrl' => 'logo2/goucai_logo_pl5.png',
			),
			'23528' => array(
				'name' => '七乐彩',
				'onclick' => 'onclick="' . $this->lotteryUrl['110'] . ';"',
				'imgUrl' => 'logo2/goucai_logo_qlc.png',
			),
			'10022' => array(
				'name' => '七星彩',
				'onclick' => 'onclick="' . $this->lotteryUrl['111'] . ';"',
				'imgUrl' => 'logo2/goucai_logo_qxc.png',
			),
			'42' => array(
				'name' => '竞彩足球',
				'onclick' => 'onclick="' . $this->lotteryUrl['112'] . ';"',
				'imgUrl' => 'logo2/goucai_logo_jczq.png',
			),
			'43' => array(
				'name' => '竞彩篮球',
				'onclick' => 'onclick="' . $this->lotteryUrl['113'] . ';"',
				'imgUrl' => 'logo2/goucai_logo_jclq.png',
			),
			'11' => array(
				'name' => '胜负彩',
				'onclick' => 'onclick="' . $this->lotteryUrl['114'] . ';"',
				'imgUrl' => 'logo2/goucai_logo_sfc.png',
			),
			'19' => array(
				'name' => '任选九',
				'onclick' => 'onclick="' . $this->lotteryUrl['115'] . ';"',
				'imgUrl' => 'logo2/goucai_logo_r9.png',
			),
			'21408' => array(
				'name' => '惊喜11选5',
				'onclick' => 'onclick="' . $this->lotteryUrl['116'] . ';"',
				'imgUrl' => 'logo2/goucai_logo_hbsyxw.png',
			),
			'21406' => array(
				'name' => '老11选5',
				'onclick' => 'onclick="' . $this->lotteryUrl['117'] . ';"',
				'imgUrl' => 'logo2/goucai_logo_syxw.png',
			),
			'21407' => array(
				'name' => '新11选5',
				'onclick' => 'onclick="' . $this->lotteryUrl['118'] . ';"',
				'imgUrl' => 'logo2/goucai_logo_jxsyxw.png',
			),
			'53' => array(
				'name' => '经典快3',
				'onclick' => 'onclick="' . $this->lotteryUrl['119'] . ';"',
				'imgUrl' => 'logo2/goucai_logo_ks.png',
			),
			'54' => array(
				'name' => '快乐扑克',
				'onclick' => 'onclick="' . $this->lotteryUrl['120'] . ';"',
				'imgUrl' => 'logo2/goucai_logo_klpk.png',
			),
			'55' => array(
				'name' => '老时时彩',
				'onclick' => 'onclick="' . $this->lotteryUrl['121'] . ';"',
				'imgUrl' => 'logo2/goucai_logo_cqssc.png',
			),
			'56' => array(
				'name' => '易快3',
				'onclick' => 'onclick="' . $this->lotteryUrl['122'] . ';"',
				'imgUrl' => 'logo2/goucai_logo_jlks.png',
			),
		    '57' => array(
		        'name' => '红快3',
		        'onclick' => 'onclick="' . $this->lotteryUrl['123'] . ';"',
		        'imgUrl' => 'logo2/goucai_logo_jxks.png',
		    ),
		    '21421' => array(
		        'name' => '乐11选5',
		        'onclick' => 'onclick="' . $this->lotteryUrl['124'] . ';"',
		        'imgUrl' => 'logo2/goucai_logo_gdsyxw.png',
		    ),
		);
		
		return $data[$lid];
	}

	/*
	 * 查询指定使用条件的红包跳转
	 * @date:2016-08-23
	 */
	public function getGoUrl($uid, $items)
	{
		$versionInfo = $this->getUserAgentInfo();
		$data = array(
			'url' => 'javascript:;',
			'onclick' => '',
			'class' => '',
		);
		if($items['status'] == '1' && $items['p_type'] == '2')
		{
			if($items['valid_start'] <= date('Y-m-d H:i:s') && $items['valid_end'] >= date('Y-m-d H:i:s'))
			{
				$token = $this->strCode(json_encode(array(
					'uid' => $uid
				)), 'ENCODE');

				$url = $this->config->item('pages_url') . 'ios/wallet/recharge/' . urlencode($token);
				$data['onclick'] = 'onclick="window.location.href=\'' . $url .'\';"';
			}
		}
		else if($items['p_type'] == '3')
		{
			if(in_array($items['c_type'], array('101', '102', '103', '104')))
			{
				if($versionInfo['appVersionCode'] <= '37')
				{
					$data['class'] = 'tipToken';
				}
				else
				{
					$data['class'] = 'popOpen';
				}
			}
			else
			{
				if($versionInfo['appVersionCode'] <= '37')
				{
					$data['class'] = 'tipToken';
				}
				elseif($items['valid_start'] <= date('Y-m-d H:i:s') && $items['valid_end'] >= date('Y-m-d H:i:s'))
				{
					$data['onclick'] = 'onclick="' . $this->lotteryUrl[$items['c_type']] . ';"';
				}				
			}
		}
		
		if ($items['aid'] == '14') $data['class'] .= ' sjb-rp';
		
		return $data;
	}

	/*
	 * Btn 信息
	 * @date:2016-08-30
	 */
	private function getBtn($redpack)
	{
		// 状态：立即使用 未激活 未到有效期
		$tips = '';
		if($redpack['status'] == '0')
		{
			$tips = '未激活';
		}
		elseif($redpack['status'] == '1')
		{
			$tips = '立即使用';
			if($redpack['valid_start'] > date('Y-m-d H:i:s'))
			{
				$tips = '立即使用';
			}
		}
		return $tips;
	}

	/*
	 * Tips 信息
	 * @date:2016-08-30
	 */
	public function getTips($redpack)
	{
		// 状态：x天后过期 已使用 已过期
		$tips = '';
		if($redpack['status'] == '1' && $redpack['valid_end'] < date('Y-m-d H:i:s', strtotime('+7days')))
		{
			$tips = ParseEnd($redpack['valid_end']);
		}
		
		if($redpack['status'] == '2')
		{
			$tips = '已使用';
		}
		
		if(in_array($redpack['status'], array('0', '1')) && $redpack['valid_end'] < date('Y-m-d H:i:s'))
		{
			$tips = '已过期';
		}
		return $tips;
	}
	
	/**
	 * 格式化红包数据
	 * @param unknown_type $uid
	 * @param unknown_type $items
	 */
	private function formatData($uid, $items)
	{
		$redpack = array();
		if($items)
		{
			$redpack['p_name'] = $items['p_type'] == '3' ? $items['p_name'] : $this->redpackType[$items['p_type']];
			$redpack['money'] = (string)ParseUnit($items['money'], 1);
			$redpack['valid_start'] = $items['valid_start'];
			$redpack['valid_end'] = $items['valid_end'];
			$items['money_bar'] = $items['money_bar'] >= 1000000 ? ($items['money_bar']/1000000) . '万' : $items['money_bar'] / 100;
			$redpack['use_desc'] = $items['p_type'] == '1' ? $items['use_desc'] : (($items['p_type'] == '2' ? '充' : '满') . $items['money_bar'] . '元可用');
			if($items['aid'] == 10){
                            $redpack['use_desc'] = "积分兑换红包";
                        }
                        if($items['aid'] == 11){
                            $redpack['use_desc'] = "会员升级礼包";
                        }
                        $redpack['status'] = $items['status'];
			$redpack['tips'] = $this->getTips($items);
			$redpack['btn'] = $this->getBtn($items);
			$redpack['a_data'] = $this->getGoUrl($uid, $items);
			$redpack['eventType'] = $items['eventType'];
			$redpack['c_type'] = $items['c_type'];
			$redpack['p_type'] = $items['p_type'];
			$redpack['id'] = $items['id'];
			$redpack['left'] = $this->getValidMsg($items);
		}
	
		return $redpack;
	}

	public function rule()
	{
		$this->load->view('redpack/rule');
	}

	public function getValidMsg($redpack)
    {
    	$msg = '';
    	if($redpack['status'] == '1' && date('Y-m-d', strtotime($redpack['valid_start'])) > date('Y-m-d'))
    	{
    		$nowTime = strtotime(date('Y-m-d'));
    		$diff = intval((strtotime($redpack['valid_start']) - $nowTime)/3600/24);
    		if($diff > 0)
    		{
    			$msg = $diff . '天后生效';
    		}
    	}
    	return $msg;
    }

}