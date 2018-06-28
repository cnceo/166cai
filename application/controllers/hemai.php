<?php
class Hemai extends MY_Controller {
	
	private $_openStatus = array(0 => '公开', 1 => '仅对跟单者公开', 2 => '截止后公开');
	
	private $_spfArr = array();
	
	private $_sfArr = array();
	
	private $_playTypes = array(
		JCZQ => array('SPF' => '胜平负', 'RQSPF' => '让球胜平负', 'BQC' => '半全场', 'CBF' => '比分', 'JQS' => '总进球'),
		JCLQ => array('SF' => '胜负', 'RFSF' => '让分胜负', 'DXF' => '大小分', 'SFC' => '胜分差')
	);
	
	private $_perPage = 10;
	
	public function __construct() {
		parent::__construct();
		$this->load->library('BetCnName');
		$this->load->model('united_order_model', 'UOrder');
		$this->load->config('wenan');
		$this->wenan = $this->config->item('wenan');
		$this->_spfArr = $this->wenan['jzspf'];
		$this->_sfArr = $this->wenan['jlsf'];
	}
	
	public function index() {
		$this->displayHmdt();
	}
	
	public function ssq() {
		$data = $this->UOrder->getSzcIssues(SSQ);
		$this->displayHmdt(SSQ, $data);
	}
	
	public function dlt() {
		$data = $this->UOrder->getSzcIssues(DLT);
		$this->displayHmdt(DLT, $data);
	}
	
	public function jczq() {
		$this->displayHmdt(JCZQ);
	}
	
	public function jclq() {
		$this->displayHmdt(JCLQ);
	}
	
	public function fcsd() {
		$data = $this->UOrder->getSzcIssues(FCSD);
		$this->displayHmdt(FCSD, $data);
	}
	
	public function sfc() {
		$data = $this->UOrder->getSzcIssues(SFC);
		$this->displayHmdt(SFC, $data);
	}
	
	public function qlc() {
		$data = $this->UOrder->getSzcIssues(QLC);
		$this->displayHmdt(QLC, $data);
	}
	
	public function qxc() {
		$data = $this->UOrder->getSzcIssues(QXC);
		$this->displayHmdt(QXC, $data);
	}
	
	public function pls() {
		$data = $this->UOrder->getSzcIssues(PLS);
		$this->displayHmdt(PLS, $data);
	}
	
	private function displayHmdt($lid = 0, $data = null) {
		if (empty($this->uid)) $this->load->model('user_model');
		$l = $lid ? $lid : 0;
		if (!$this->is_ajax) {
			$hotPlanner = $this->UOrder->getHotPlanner($lid);
			$data['hotPlanner'] = $hotPlanner;
			foreach ($hotPlanner as $hotp) {
				foreach ($hotp as $hp) {
					$uinfo = $this->user_model->getUserInfo($hp['uid']);
					$hinfo = $this->user_model->getHotInfo($hp['uid']);
					$data['users'][$hp['uid']] = array('uname' => $uinfo['uname'], 'isHot' => $hinfo['isHot_'.$l]);
				}
			}
		}
		$lidArr = array('ssq'=>'双色球', 'dlt'=>'大乐透', 'jczq'=>'竞彩足球', 'jclq'=>'竞彩篮球', 'sfc'=>'胜负/任九', 'fcsd'=>'福彩3D', 'qlc'=>'七乐彩', 'qxc'=>'七星彩', 'pls'=>'排列三/五');
		$data['lidArr'] = $lidArr;
		$data['search'] = $post = $this->input->post(NULL, true);
		$cpage = intval($this->input->get('cpage', true));
		if ($cpage <= 1) $cpage = 1;
		$data['search']['order'] = $order = ($post['order'] && is_string($post['order'])) ? $post['order'] : '00';
		$perPage = 30;
		$data['lid'] = $post['lid'] = $lid;
		$data['stateArr'] = array(0 => '等待满员', 1 => '已满员', 2 => '已撤单');
		if ($data['issues']) {
			if (empty($post['issue'])) {
				$data['search']['issue'] = $post['issue'] = max($data['issues']);
			}elseif ($post['issue'] !== max($data['issues'])) {
				if ($post['state'] == 0) $data['search']['state'] = $post['state'] = 1;
				unset($data['stateArr'][0]);
			}
		}
		$orders = $this->UOrder->getHmdtOrders($post, ($perPage * ($cpage-1)).", ".$perPage, $order, $this->uid);
		$orders = array('num' =>0 ,'data'=>array());
                foreach ($orders['data'] as $od) {
			if (empty($data['users'][$od['uid']])) {
				$uinfo = $this->user_model->getUserInfo($od['uid']);
				$hinfo = $this->user_model->getHotInfo($od['uid']);
				$data['users'][$od['uid']] = array('uname' => $uinfo['uname'], 'isHot' => $hinfo['isHot_'.$l]);
			}
		}
		$data['moneyArr'] = array(0 => '不限金额', 1 => '100以下', 2 => '101-500', 3 => '501-1000', 4 => '1000以上');
		$data['orders'] = $orders;
		$data['cpage'] = $cpage;
		$data['pagenum'] = ceil($orders['num'][0] / $perPage);
		$data['htype'] = 1;
		$data['pagestr'] = $this->load->view('v1.1/elements/common/pages', array('pagenum' => $data['pagenum'], 'ajaxform' => 'hemai_form'), true);
		if ($this->is_ajax) {
			$this->load->view('v1.1/hemai/index', $data);
		}else {
			$this->display('hemai/index', $data, 'v1.1');
		}
	}
	
	public function detail($orderId)
	{
		$orderId = preg_match('/hm(\d{20})/', $orderId, $matches);
		$orderId = $matches[1];
		if ($orderId) {
			if ($this->is_ajax) {
				$action = $this->input->post('action', true);
				$perPage = $this->_perPage;
				switch ($action) {
					case 'user':
						$cpage = intval($this->input->get('cpage', true));
						$cpage = $cpage <= 1 ? 1 : $cpage;
						$users = $this->UOrder->getJoin($orderId, array('userName' => 1), 'o.created, o.buyMoney, o.margin, o.status', true, 'created', (($cpage-1) * $this->_perPage).",".$this->_perPage);
						$num = $this->UOrder->getJoin($orderId, array(), 'count(*) as num, sum(buyMoney) as money');
						$pagestr = $this->load->view('v1.1/elements/common/pages', array('pagenum' => ceil($num['num'] / $this->_perPage), 'ajaxform' => 'form-user'), true);
						echo $this->load->view('v1.1/hemai/detail_user', compact('users', 'cpage', 'perPage', 'pagestr', 'orderId', 'num'), true);
						exit();
						break;
					case 'my':
						if ($this->uid) {
							$cpage = intval($this->input->get('cpage', true));
							$cpage = $cpage <= 1 ? 1 : $cpage;
							$orders = $this->UOrder->getJoin($orderId, array('o.uid' => $this->uid), 'o.created, o.buyMoney, o.margin, o.status', true, 'o.created', (($cpage-1) * $this->_perPage).",".$this->_perPage);
							$num = $this->UOrder->getJoin($orderId, array('o.uid' => $this->uid), 'count(*) as num');
							$pagestr = $this->load->view('v1.1/elements/common/pages', array('pagenum' => ceil($num['num'] / $this->_perPage), 'ajaxform' => 'form-my'), true);
							echo $this->load->view('v1.1/hemai/detail_my', compact('orders', 'cpage', 'perPage', 'pagestr', 'orderId'), true);exit();
						}else {
							exit('not login');
						}
						break;
					case 'split':
						$this->load->library('LotteryDetail');
						$orderInfo = $this->UOrder->getUniteOrderByOrderId($orderId, 'orderId, openStatus, uid, openEndtime');
						$showdetail = $this->getShowDetail($orderInfo);
						if ($showdetail) {
							$this->load->model('order_model', 'Order');
							$detail = $this->UOrder->getOrderInfo($orderId);
							if (in_array($detail['lid'], array(JCZQ, JCLQ))) {
								$orderDetail = $this->Order->getJjcOrderDetail($orderId);
								$matchDetail = $this->Order->getJjcMatchDetail($detail['lid'], $detail['codecc']);
								foreach ($matchDetail as $val) {
									$detailres[$val['mid']] = $val;
								}
								$spfArr = $this->_spfArr;
								$sfArr = $this->_sfArr;
								// 获取对阵信息
								echo $this->load->view('v1.1/hemai/detail_split', compact('orderDetail', 'detailres', 'detail', 'spfArr', 'sfArr', 'showdetail', 'matchData'), true);exit();
							}else {
								$award = $this->Order->getSfcAward($detail['issue']);
								$orderDetail = $this->Order->getNumOrderDetail($orderId, $detail['lid']);
								echo $this->load->view('v1.1/hemai/detail_split', compact('orderDetail', 'award', 'showdetail'), true);exit();
							}
						}
						echo $this->load->view('v1.1/hemai/detail_split', compact('showdetail', 'orderInfo'), true);exit();
						break;
				    case 'bonusOpt':
				        $orderInfo = $this->UOrder->getUniteOrderByOrderId($orderId, 'orderId, openStatus, uid, openEndtime');
				        $showdetail = $this->getShowDetail($orderInfo);
				        if ($showdetail) 
				        {
				            $this->load->model('order_model', 'Order');
				            $detail = $this->UOrder->getOrderInfo($orderId);
				            if (in_array($detail['lid'], array(JCZQ, JCLQ))) 
				            {
				                $award = $this->Order->getJjcMatchDetail($detail['lid'], $detail['codecc']);
				                $bonusOpt = $this->bonusOpt($orderId, $award);
				                echo $this->load->view('v1.1/hemai/detail_bonusOpt', compact('bonusOpt', 'detail', 'showdetail'), true);exit();
				            }
				        }
				        echo $this->load->view('v1.1/hemai/detail_bonusOpt', compact('showdetail', 'orderInfo'), true);exit();
				        break;
					case 'info':
					default:
						$orderInfo = $this->UOrder->getUniteOrderByOrderId($orderId);
                                                if($orderInfo['lid'] == 51)
                                                {
                                                    $orderInfo['otherBonus'] = 0;
                                                    $this->load->model('order_model', 'Order');
                                                    $splitOrders = $this->Order->getNumOrderDetail($orderInfo['orderId'], $orderInfo['lid']);
                                                    foreach ($splitOrders as $sorder) {
                                                        $orderInfo['otherBonus'] += $sorder['otherBonus'];
                                                    }
                                                }                                            
						$showdetail = $this->getShowDetail($orderInfo);
						$this->load->model('order_model', 'Order');
						$award = $this->getAward($orderInfo);
						if ($showdetail) $data = $this->renderInfo($orderInfo, $award);
						$data['awardNum'] = $award['awardNumber'];
						$data['awardTime'] = $award['awardTime'];
						$data['orderInfo'] = $orderInfo;
						$data['showdetail'] = $showdetail;
						$data['weekdayarr'] = array('天', '一', '二', '三', '四', '五', '六');
						$data['spfArr'] = $this->_spfArr;
	 				    $data['sfArr'] = $this->_sfArr;
						// 实际出票
						$data['ticketData'] = $this->getTicketData($orderInfo);
						// 竞足竞篮订单
						if(in_array($orderInfo['lid'], array(JCLQ, JCZQ)))
						{
							$data['orderDetail'] = $this->getOrderDetail($data['orderDetail'], $data['ticketData']);
						}
						echo $this->load->view('v1.1/hemai/detail_info', $data, true);exit();
						break;
				}
			}else {
				$orderInfo = $this->UOrder->getUniteOrderByOrderId($orderId, null, ' and status not in (0, 20)');
				if(!empty($orderInfo['orderId']))
				{
					$this->isMobile();
                                        if($orderInfo['lid'] == 51)
                                        {
                                            $orderInfo['otherBonus'] = 0;
                                            $this->load->model('order_model', 'Order');
                                            $splitOrders = $this->Order->getNumOrderDetail($orderInfo['orderId'], $orderInfo['lid']);
                                            foreach ($splitOrders as $sorder) {
                                                $orderInfo['otherBonus'] += $sorder['otherBonus'];
                                            }
                                        }
					$orderInfo['cnName'] = BetCnName::getCnName($orderInfo['lid']);
					$orderInfo['enName'] = BetCnName::getEgName($orderInfo['lid']);
					if (empty($this->uid)) $this->load->model('user_model');
					$uinfo = $this->user_model->getUserInfo($orderInfo['uid']);
					$orderInfo['uname'] = $uinfo['uname'];
                    $this->load->model('user_model');
                    $user = $this->user_model->findByUid($orderInfo['uid']);
                    $points = $this->UOrder->getPoints($orderInfo['uid'], $orderInfo['lid']);
                    $orderInfo['points'] = $points[0];
					if ($orderInfo['shopId']) {
						$shop = $this->UOrder->getBetstationByShopid($orderInfo['shopId']);
						$orderInfo['cname'] = $shop['cname'];
					}
					$this->load->model('order_model', 'Order');
					$showdetail = $this->getShowDetail($orderInfo);
					$award = $this->getAward($orderInfo);
					if ($showdetail) {
						$data = $this->renderInfo($orderInfo, $award);
					}else {
						$oinfo = $this->UOrder->getJoin($orderInfo['orderId'], array(), (($this->uid) ? "sum(case when o.uid=".$this->uid." then o.id else 0 end) as suid,":'')."sum(case when ".$orderInfo['uid']."=o.uid then o.buyMoney else 0 end) as buyMoney");
						$orderInfo = array_merge($orderInfo, $oinfo);
					}
                    $data['user'] = $user;
					$data['awardNum'] = $award['awardNumber'];
					$data['awardTime'] = $award['awardTime'];
					$data['htype'] = 1;
					$data['openStatus'] = $this->_openStatus;
					$data['showdetail'] = $showdetail;
					$data['orderInfo'] = $orderInfo;
					$data['param1'] = $orderInfo['uname'];//seo
					$data['issue'] = number_format(ParseUnit($orderInfo['money'], 1), 2);//seo
					$data['cnName'] = $orderInfo['cnName'];
					$data['param0'] = $orderInfo['popularity'];
					// 实际出票
					$data['ticketData'] = $this->getTicketData($orderInfo);
					// 竞足竞篮订单
					if(in_array($orderInfo['lid'], array(JCLQ, JCZQ)))
					{
						$data['orderDetail'] = $this->getOrderDetail($data['orderDetail'], $data['ticketData']);
					}
					// 大乐透乐善奖
					$data['lsDetail'] = $this->getLsDetail($orderInfo);
	 				$pagestr = $this->load->view('v1.1/elements/common/pages', array('pagenum' => ceil($orderInfo['popularity'] / $this->_perPage), 'ajaxform' => 'form-user'), true);
	 				$data['weekdayarr'] = array('天', '一', '二', '三', '四', '五', '六');
	 				$data['spfArr'] = $this->_spfArr;
	 				$data['sfArr'] = $this->_sfArr;
					$this->display('hemai/detail', $data, 'v1.1');
				}
				else
				{
					$this->redirect('/error/');
				}
			}
		}else {
			$this->redirect('/error/');
		}
	}
	
	/**
	 * 奖金优化数据
	 * @param unknown_type $orderId
	 * @param unknown_type $matchs
	 */
	private function bonusOpt($orderId, $matchs)
	{
	    $data = array();
	    $split = $this->Order->getBonusOptDetail($orderId);
	    $matchInfo = array();
	    foreach ($matchs as $match)
	    {
	        $matchInfo[$match['mid']] = $match;
	    }
	    $preg = array(
	        '42' => '/([\d\-\:]*)(?:\{(.*)\})?\(?(\d*\.?\d*)?\)?/is',
	        '43' => '/([\d\-\:]*)(?:\{(.*)\})?\(?(\d*\.?\d*)?\)?(?:\{(.*)\})?/is'
	    );
	    //胜平负
        $spf = array(
            '0' => $this->wenan['jzspf']['0'],
            '1' => $this->wenan['jzspf']['1'],
            '3' => $this->wenan['jzspf']['3']
        );
        
        $rqspf = array(
            '0' => $this->wenan['jzspf']['r0'],
            '1' => $this->wenan['jzspf']['r1'],
            '3' => $this->wenan['jzspf']['r3']
        );
        $sf = array(
    	    '0' => $this->wenan['jlsf']['0'],
    		'3' => $this->wenan['jlsf']['3']
    	);
    	$rfsf = array(
    	    '0' => $this->wenan['jlsf']['r0'],
    		'3' => $this->wenan['jlsf']['r3']
    	);
        //大小分
        $dxf = array(
            '0' => '小分',
            '3' => '大分'
        );
        
        //胜分差
        $sfc = array(
            '01' => $this->wenan['jlsf']['3']."1-5分",
            '02' => $this->wenan['jlsf']['3']."6-10分",
            '03' => $this->wenan['jlsf']['3']."11-15分",
            '04' => $this->wenan['jlsf']['3']."16-20分",
            '05' => $this->wenan['jlsf']['3']."21-25分",
            '06' => $this->wenan['jlsf']['3']."26+分",
            '11' => $this->wenan['jlsf']['0']."1-5分",
            '12' => $this->wenan['jlsf']['0']."6-10分",
            '13' => $this->wenan['jlsf']['0']."11-15分",
            '14' => $this->wenan['jlsf']['0']."16-20分",
            '15' => $this->wenan['jlsf']['0']."21-25分",
            '16' => $this->wenan['jlsf']['0']."26+分",
        );
	    foreach ($split as $value)
	    {
	        $info = array();
	        $info['subCodeId'] = $value['subCodeId'];
	        $info['multi'] = $value['multi'];
	        $info['status'] = $value['status'];
	        $info['bonus'] = $value['bonus'];
	        $info['margin'] = $value['margin'];
	        $codes = explode('|', $value['codes']);
	        $codeArr = explode('*', $codes[0]);
	        $type = count($codeArr);
	        $info['type'] = $this->getType($type . '*1');
	        $singleMoney = 1;
	        $matchDetail = '';
	        foreach ($codeArr as $key => $val)
	        {
	            $cArr = explode(',', $val);
	            preg_match($preg[$value['lid']], $cArr['2'], $matches);
	            $cast = $matches[1];
	            $odd = floatval($matches[3]);
	            $singleMoney *= $odd;
	            $oddStr = '';
	            switch ($cArr['1'])
	            {
	                case 'SPF':
	                    $odd = $spf[$cast] . ': ' . $odd;
	                    break;
	                case 'SF':
	                    $odd = $sf[$cast] . ': ' . $odd;
	                    break;
	                case 'RQSPF':
	                    $odd = $rqspf[$cast] . ': ' . $odd;
	                    break;
	                case 'RFSF':
	                    $odd = $rfsf[$cast] . ': ' . $odd;
	                    break;
	                case 'CBF':
	                    $bf = $this->getCbf($cast);
	                    $odd = $bf.': ' . $odd;
	                    break;
	                case 'BQC':
	                case 'JQS':
	                    if($cast == 7)
	                    {
	                        $cast = '7+';
	                    }
	                    $odd = $cast . ': ' . $odd;
	                    break;
	                case 'DXF':
	                    $odd = $dxf[$cast] . ': ' . $odd;
	                    break;
	                case 'SFC':
	                    $odd = $sfc[$cast] . ': ' . $odd;
	            }
	            $matchDetail .=  "<b>{$matchInfo[$cArr['0']]['home']}</b>({$odd})";
	            if($key < $type - 1)
	            {
	                $matchDetail .= ' X ';
	            }
	        }
	        $info['detail'] = $matchDetail;
	        $info['singleMoney'] = $singleMoney * 2 * 100;
	        $info['money'] = $singleMoney * 2 * $value['multi'] * 100;
	        array_push($data, $info);
	    }
	    
	    return $data;
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
	
	private function getShowDetail($orderInfo) {
		$showdetail = 0;
		switch ($orderInfo['openStatus']) {
			case 2:
				if ($this->uid == $orderInfo['uid'] || time() > strtotime($orderInfo['openEndtime'])) $showdetail = 1;
				break;
			case 1:
				if ($this->uid) {
					$res = $this->UOrder->getJoin($orderInfo['orderId'], array('o.uid' => $this->uid), 'o.id');
					if (!empty($res)) $showdetail = 1;
				}
				break;
			case 0:
			default:
				$showdetail = 1;
				break;
		}
		return $showdetail;
	}
	
	private function getAward($orderInfo) {
		if (in_array($orderInfo['lid'], array(SSQ, DLT, FCSD, PLS, PLW, QLC, QXC))) return $this->Order->getNumIssue($orderInfo['lid'], $orderInfo['issue']);
		if (in_array($orderInfo['lid'], array(SFC, RJ))) return $this->Order->getSfcAward($orderInfo['issue']);
	}
	
	private function renderInfo(&$orderInfo, $award) {
		$this->load->library('LotteryDetail');
		$detail = $this->UOrder->getOrderInfo($orderInfo['orderId']);
		$orderInfo['betTnum'] = $detail['betTnum'];
		$orderInfo['multi'] = $detail['multi'];
		$oinfo = $this->UOrder->getJoin($orderInfo['orderId'], array(), (($this->uid) ? "sum(case when o.uid=".$this->uid." then o.margin else 0 end) as margin, sum(case when o.uid=".$this->uid." then o.id else 0 end) as suid,":'')." sum(case when ".$orderInfo['uid']."=o.uid then o.buyMoney else 0 end) as buyMoney");
		$orderInfo = array_merge($orderInfo, $oinfo);
		if (in_array($orderInfo['lid'], array(JCZQ, JCLQ))) {
			$data['orderDetail'] = $this->Order->getJjcMatchDetail($orderInfo['lid'], $detail['codecc']);
			$orderInfo['codes2'] = '';
			foreach (explode(';', $detail['codes']) as $codestr) {
				$codes = explode('|', $codestr);
				$code2 = end($codes);
				if (strpos($orderInfo['codes2'], $code2) === false)  $orderInfo['codes2'] .= $code2.",";
				foreach (explode(',', $codes[1]) as $code) {
					preg_match('/(\w+)\>(\d{11})\=((3|1|0|\d\-\d|\d:\d|[0-7]|\d+)(\{(.+)\})?\((.+)\)\/?)+(\{(.+)\})?/', $code, $matches);
					$data['codeArr'][$matches[2]][$matches[1]][$matches[4]] = $matches;
				}
			}
			$orderInfo['codes2'] = substr($orderInfo['codes2'], 0, -1);
			$data['spfArr'] = $this->_spfArr;
			$data['playTypes'] = $this->_playTypes[$orderInfo['lid']];
		}elseif (in_array($orderInfo['lid'], array(SFC, RJ))) {
			$data['orderDetail'] = $this->Order->getSfcMatchs($orderInfo['issue']);
			$orderInfo['codes'] = $detail['codes'];
		}else {
			if ($orderInfo['status'] < 240 || in_array($orderInfo['status'], array(610, 620))) {
				foreach (explode(';', $detail['codes']) as $code) {
					$isChase = false;
					if ($orderInfo['lid'] == DLT && $orderInfo['isChase']) $isChase = true;
					$res = $this->lotterydetail->renderCode($code, $orderInfo['lid'], null, $award['awardNumber'], $isChase);
					$res['multi'] = $detail['multi'];
					$data['codeArr'][] = $res;
				}
			}else {
				$orderDetail = $this->Order->getNumOrderDetail($orderInfo['orderId'], $orderInfo['lid']);
				foreach ($orderDetail as $code) {
					$codeArr = explode('^', $code['codes']);
					$bDetail = null;
					if ($code['status'] == 2000) $bDetail = json_decode($code['bonus_detail'], true);
					foreach ($codeArr as $k => $val) {
						if ($val) {
							$bonus = 0;
							$mz = array();
							if ($bDetail) {
								switch ($orderInfo['lid']) {
									case PLS:
									case FCSD:
									case PLW:
										$pArr = array(1 => 'zx', 2 => 'z3', 3 => 'z6');
										$mz[] = $bDetail[$k];
										$bonus = $bDetail[$k] * $award['bonusDetail'][$pArr[$code['playType']]]['dzjj'] * $code['multi'] * 100;
										break;
									case SSQ:
									case DLT:
									case QLC:
									case QXC:
										foreach ($bDetail as $bk => $bd) {
											if ($bd[$k] > 0) {
												$mz[$bk] = $bd[$k];
												if ($orderInfo['lid'] == DLT) {
													if ($orderInfo['isChase']) {
														$bonus += $bd[$k] * $award['bonusDetail'][$bk."dj"]['jb']['dzjj'] * $code['multi'] * 100;
														$bonus += $bd[$k] * $award['bonusDetail'][$bk."dj"]['zj']['dzjj'] * $code['multi'] * 100;
													}else {
														$bonus += $bd[$k] * $award['bonusDetail'][$bk."dj"]['jb']['dzjj'] * $code['multi'] * 100;
													}
												}else {
													$bonus += $bd[$k] * $award['bonusDetail'][$bk."dj"]['dzjj'] * $code['multi'] * 100;
												}
											}
										}
										break;
								}
							}
							$res = $this->lotterydetail->renderCode($val, $orderInfo['lid'], $code['playType'], $award['awardNumber'], $orderInfo['isChase']);
							$data['codeArr'][] = array(
									'code' => $res['code'],
									'betNum' => count($codeArr) > 1 ? 1 : $code['betTnum'],
									'multi' => $code['multi'],
									'status' => in_array($orderInfo['status'], array(600, 610, 620)) ? $orderInfo['status'] : $code['status'],
									'bonus' => $bonus,
									'mz' => $mz
							);
						}
					}
				}
			}
		}
		return $data;
	}
	
	public function getOrderInfo() {
		$orderId = $this->input->post('orderId', true);
		$buyMoney = $this->input->post('buyMoney', true);
		$orderInfo = $this->UOrder->getUniteOrderByOrderId($orderId, 'lid, issue, buyTotalMoney, money, endTime, status, playType');
		if (in_array($orderInfo['status'], array(600, 610, 620))) {
			$rst = array('code' => 300, 'msg' => '<i class="icon-font">&#xe611;</i>该合买方案已撤单');
		}elseif (time() > strtotime($orderInfo['endTime'])) {
			$rst = array('code' => 300, 'msg' => '<i class="icon-font">&#xe611;</i>该合买方案已截止');
		}elseif ($orderInfo['status'] < 40 || $orderInfo['status'] > 500 || $orderInfo['money'] == $orderInfo['buyTotalMoney']) {
			$rst = array('code' => 300, 'msg' => '<i class="icon-font">&#xe611;</i>该合买方案已满员');
		}elseif ($buyMoney && $buyMoney * 100 > $orderInfo['money']-$orderInfo['buyTotalMoney']) {
			$rst = array('code' => 300, 'msg' => '<i class="icon-font">&#xe611;</i>该合买方案剩余金额不足');
		}elseif ($buyMoney * 100 > $this->uinfo['money']) {
			$rst = array(
				'code' => 12,
				'restmoney' => ($orderInfo['money'] - $orderInfo['buyTotalMoney']) / 100, 
				'remain_money' => number_format(ParseUnit($this->uinfo['money'], 1), 2),
				'lid' => $orderInfo['lid'],
				'issue' => $orderInfo['issue'],
			);
		}else {
			$rst = array(
				'code' => 0,
				'lid' => $orderInfo['lid'],
				'issue' => $orderInfo['issue'],
				'restmoney' => ($orderInfo['money'] - $orderInfo['buyTotalMoney']) / 100,
				'remain_money' => number_format(ParseUnit($this->uinfo['money'], 1), 2),
				'end' => time() > strtotime($orderInfo['endTime']),
				'status' => $orderInfo['status']
			);
		}
		
		
		if (in_array($orderInfo['lid'], array(JCZQ, JCLQ))) $rst['typeCnName'] = BetCnName::getCnName($orderInfo['lid']) . ',参与合买,' . BetCnName::getCnPlaytype($orderInfo['lid'], $orderInfo['playType']);
		
		header('Content-type: application/json');
		echo json_encode( $rst );
	}
	
        public function getGendanInfo()
        {
            $orderId = $this->input->post('orderId', true);
            $this->load->model('follow_order_model');
            $orderInfo = $this->follow_order_model->getFollowOrderDetail($orderId);
            $rst = array(
                    'code' => 0,
                    'lid' => $orderInfo['lid'],
                    'uid' => $orderInfo['uid'],
                    'puid' => $orderInfo['puid'],                
                    'issue' => '',                                                    
                    'remain_money' => number_format(ParseUnit($this->uinfo['money'], 1), 2),
                    'followType'=> $orderInfo['followType'],
                    'totalMoney' => $orderInfo['totalMoney'],
                    'buyMoney' => $orderInfo['buyMoney'],
                    'buyMoneyRate' => $orderInfo['buyMoneyRate'],     
                    'buyMaxMoney' => $orderInfo['buyMaxMoney'],  
                    'followTotalTimes' => $orderInfo['followTotalTimes'],
                    'typeCnName' => BetCnName::getCnName($orderInfo['lid'])
            );
            header('Content-type: application/json');
            echo json_encode($rst);
    }

        public function getOrderState(){
		$orderId = $this->input->post('orderId', true);
		$orderInfo = $this->UOrder->getUniteOrderByOrderId($orderId, 'buyTotalMoney, status, popularity, endTime, lid');
		$orderInfo['end'] = false;
		if (strtotime($orderInfo['endTime']) <= time()) $orderInfo['end'] = true;
		unset($orderInfo['endTime'], $orderInfo['lid']);
		header('Content-type: application/json');
		echo json_encode( $orderInfo );
	}
	
	public function cancelOrder() {
		$orderId = $this->input->post('orderId', true);
		$res = $this->UOrder->cancelOrder($orderId, 0, true, $this->uid);
		exit(json_encode($res));
	}

	public function getTicketData($orderInfo)
	{
		$ticketData = array();
		if(in_array($orderInfo['lid'], array(JCZQ, JCLQ)) && $orderInfo['status'] >= 500)
		{
			$this->load->model('order_model', 'Order');
			$ticketInfo = $this->Order->getJjcOrderDetail($orderInfo['orderId']);
			$ticketData = $this->parseTicketInfo($ticketInfo);
		}
		return $ticketData;
	}

	private function parseTicketInfo($ticketInfo = array())
	{
		$ticketData = array();
		if(!empty($ticketInfo))
		{
			foreach ($ticketInfo as $detail) 
			{
				$ticketArr = explode('|', $detail['codes']);
				$ticketDetail = $this->ticketMix($ticketArr[0], $detail['info']);
				// 汇总出票信息
                $ticketData = $this->recordTicketInfo($ticketData, $ticketDetail);
			}
		}
		return $ticketData;
	} 

	private function ticketMix($ticket, $info)
	{
		$ticketData = array();
		$ticketInfo = explode('*', $ticket);
		foreach ($ticketInfo as $k_ticket => $v_ticket)
        {
        	$ticketDetail = explode(',', $v_ticket);
        	$ticketData[$ticketDetail[0]][$ticketDetail[1]][] = $this->ticketDetail($ticketDetail[0], $ticketDetail[1], $ticketDetail[2], $info[$ticketDetail[0]]);
        }
        return $ticketData;
	}

	private function ticketDetail($mid, $playType, $tickets, $info)
	{
		// 出票盘口及赔率信息
        $ticketInfo = array();
        $resBet =  explode('/', $tickets);
        $info = json_decode($info, true);

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
                    $ticketInfo[$pk][$matches[1]][] = $pl;
                }
                break;
            //让球胜平负
            case 'RQSPF':
                foreach ($resBet as $kBet => $vBet) 
                {
                	preg_match('/^(\d+)(?:{(.*?)})?\(.*?\)$/is', $vBet, $matches);
                    // 出票盘口及赔率
                    $pk = $info['letVs']['letPoint'][0] > 0 ? '+' . $info['letVs']['letPoint'][0] : $info['letVs']['letPoint'][0];
                    $pl = $info["letVs"]["v{$matches[1]}"][0];
                    $ticketInfo[$pk][$matches[1]][] = $pl;
                }
                break;
            //猜比分
            case 'CBF':
                foreach ($resBet as $kBet => $vBet) 
                {
                    preg_match('/^(.*?)\(.*?\)$/is', $vBet, $matches);
                    $index = preg_replace('/[^\d]/is', '', $matches[1]);
                    // 出票盘口及赔率
                    $pk = '-';
                    $pl = $info["score"]["v$index"][0];
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
                    $ticketInfo[$pk][$matches[1]][] = $pl;
                }
                break;
            //半全场
            case 'BQC':
                foreach ($resBet as $kBet => $vBet) 
                {
                    preg_match('/^(.*?)\(.*?\)$/is', $vBet, $matches);
                    $spfInfo = explode('-', $matches[1]);
                    // 出票盘口及赔率
                    $pk = '-';
                    $pl = $info["half"]["v$spfInfo[0]$spfInfo[1]"][0];
                    $ticketInfo[$pk][$matches[1]][] = $pl;
                }
                break;
            //竞彩篮球 让分胜负
            case 'RFSF':
                foreach ($resBet as $kBet => $vBet)
                {
                   	preg_match('/^(\d+)(?:{(.*?)})?\(.*?\)$/is', $vBet, $matches);
                    // 出票盘口及赔率
                    $pk = $info['letVs']['letPoint'][0] > 0 ? '+' . $info['letVs']['letPoint'][0] : $info['letVs']['letPoint'][0];
                    $pl = $info['letVs']["v$matches[1]"][0];
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
                    $ticketInfo[$pk][$matches[1]][] = $pl;
                }
                break;
            //竞彩篮球 胜分差
            case 'SFC':
                foreach ($resBet as $kBet => $vBet) {
                	preg_match('/^(\d+)\(.*?\)$/is', $vBet, $matches);
                    // 出票盘口及赔率
                    $pk = '-';
                    $pl = $info['diff']["v$matches[1]"][0];      
                    $ticketInfo[$pk][$matches[1]][] = $pl;
                }
                break;    
            default:
                # code...
                break;
        }
        return $ticketInfo;
	}

	// 汇总出票赔率
    private function recordTicketInfo($info, $details = array())
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

    public function getOrderDetail($orderDetail, $ticketData = array())
    {
    	if(!empty($ticketData))
    	{
    		// 汇总RQSPF RFSF DXF盘口信息
    		$pkArrs = array();
    		$let = array();
    		$preScore = array();
    		foreach ($ticketData as $mid => $ticketArr) 
    		{
    			$let[$mid] = array();
    			$preScore[$mid] = array();
    			foreach ($ticketArr as $playType => $pkArr) 
    			{
    				if(in_array($playType, array('RQSPF', 'RFSF')))
    				{
    					foreach ($pkArr as $pk => $value) 
    					{
    						if(!in_array($pk, $let))
    						{
    							array_push($let[$mid], $pk);
    						}
    					}
    				}
    				elseif(in_array($playType, array('DXF')))
    				{
    					foreach ($pkArr as $pk => $value) 
    					{
    						if(!in_array($pk, $preScore))
    						{
    							array_push($preScore[$mid], $pk);
    						}
    					}
    				}
    			}
    		}

    		if(!empty($orderDetail))
    		{
    			foreach ($orderDetail as $key => $items) 
	    		{
	    			$orderDetail[$key]['let'] = !empty($let[$items['mid']]) ? implode('&', $let[$items['mid']]) : '';
	    			$orderDetail[$key]['preScore'] = !empty($preScore[$items['mid']]) ? implode('&', $preScore[$items['mid']]) : '';
	    		}
    		}		
    	}
    	return $orderDetail;
    }
    
    public function gdetail($orderId)
    {
        if (!$this->uid) {
            $this->redirect('/main/login');
        }
        $orderId = preg_match('/gd(\d{20})/', $orderId, $matches);
        $orderId = $matches[1];
        if ($orderId)
        {
            $this->load->model('follow_order_model');
            $this->load->model('user_model');
            $orderInfo = $this->follow_order_model->followOrderDetail($orderId, $this->uid);
            if (empty($orderInfo)) {
                $this->redirect('/error/');
            } else {
                $orders = $this->follow_order_model->getAllFollowOrders($orderId);
                $userinfo = $this->user_model->getUserInfo($orderInfo['puid']);
                $orderInfo['uname'] = $userinfo['uname'];
                $orderInfo['enName'] = BetCnName::getEgName($orderInfo['lid']);
                $orderInfo['cnName'] = BetCnName::getCnName($orderInfo['lid']);
                $cnName = BetCnName::getCnName($orderInfo['lid']);
                $this->display('hemai/gendandetail', compact('orderInfo', 'orders', 'cnName'), 'v1.1');
            }
        }
    }
    
    public function cancelGendan()
    {
        $orderId = $this->input->post('orderId', true);
        if($this->uid && $orderId)
        {
            $this->load->model('follow_order_model');
            $res = $this->follow_order_model->cancelFollowOrder($this->uid, $orderId);
            if ($res['code'] == '200') {
                $res['msg'] = "您好，停止跟单操作成功。若为预付扣款，将退款至您的账户。";
            } else {
                $res['msg'] = "您好，停止跟单操作异常，请重新尝试。";
            }
            header('Content-type: application/json');
            echo json_encode($res);
        }
    }
    
    // 乐善奖
    public function getLsDetail($orderInfo)
    {
        $lsDetail = array();
        $totalMargin = 0;
        $this->load->model('order_model', 'Order');
        $info = $this->Order->getLsDetail($orderInfo['orderId'], $orderInfo['lid']);
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
    public function lsDetail($orderId)
    {
    	$orderInfo = $this->UOrder->getUniteOrderByOrderId($orderId, null, ' and status not in (0, 20)');
    	if(empty($orderInfo['orderId']))
    	{
    		$this->redirect('/error/');
    	}
    	// 检查当前用户合法性
    	$showdetail = $this->getShowDetail($orderInfo);
		if(empty($showdetail))
		{
			$this->redirect('/error/');
		}
     
        $this->load->library('LotteryDetail');
        $this->load->model('order_model', 'Order');
        // 出票明细
        $ticketDetail = array();
        $splitOrders = $this->Order->getLsDetail($orderInfo['orderId'], $orderInfo['lid']);
        if(!empty($splitOrders))
        {
            foreach ($splitOrders as $sorder) 
            {
                if(empty($sorder['awardNum']))
                {
                    continue;
                }

                $margin = $sorder['margin'] ? $sorder['margin'] : 0;
                // 组装数据
                $data = array(
                    'code'          =>  array(),
                    'awardNum'      =>  $this->lsAwardFormat($sorder['awardNum']),
                    'bonusStatus'   =>  $this->lotterydetail->getTicketBonus($sorder['status'], $margin),
                );
                $codes = explode('^', $sorder['codes']);
                foreach ($codes as $key => $code) 
                {
                    if ($code !== '') 
                    {
                        $isChase = false;
                        if ($orderInfo['lid'] == DLT && $orderInfo['isChase']) $isChase = true;
                        $sorder['awardNum'] = str_replace(array('|', '(', ')'), array(':', ':', ''), $sorder['awardNum']);
                        $res = $this->lotterydetail->renderCode($code, $sorder['lid'], $sorder['playType'], $sorder['awardNum'], $isChase);
                        $data['code'][] = $res['code'];
                    }
                }
                array_push($ticketDetail, $data);
            }
        }
        else
        {
            $this->redirect('/error/');
        }

        $this->display('order/ls_detail', array(
            'ticketDetail' => $ticketDetail,
        ), 'v1.1');
    }

    public function lsAwardFormat($awardNum)
    {
        $span = '';
        $numArr = explode('|', $awardNum);
        if($numArr[0] && $numArr[1])
        {
            foreach (explode(',', $numArr[0]) as $num) 
            {
                $span .= '<span class="ball ball-red">';
                $span .= $num;
                $span .= '</span>';
            }
            foreach (explode(',', $numArr[1]) as $num) 
            {
                $span .= '<span class="ball ball-blue">';
                $span .= $num;
                $span .= '</span>';
            }
        }
        return $span;
    }
}
