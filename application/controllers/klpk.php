<?php
class Klpk extends MY_Controller
{

	public function index()
	{
		$playTypeArr = array (
			11 => array('cname' => '对子', 'bonus' => 88, 'ename' => 'dz'),
			7 => array('cname' => '同花', 'bonus' => 90, 'ename' => 'th'),
			9 => array('cname' => '顺子', 'bonus' => 400, 'ename' => 'sz'),
			8 => array('cname' => '同花顺', 'bonus' => 2150, 'ename' => 'ths'),
			10 => array('cname' => '豹子', 'bonus' => 6400, 'ename' => 'bz'),
			1 => array('cname' => '任选一', 'bonus' => 5, 'ename' => 'rx1'),
			2 => array('cname' => '任选二', 'bonus' => 33, 'ename' => 'rx2'),
			3 => array('cname' => '任选三', 'bonus' => 116, 'ename' => 'rx3'),
			4 => array('cname' => '任选四', 'bonus' => 46, 'ename' => 'rx4'),
			5 => array('cname' => '任选五', 'bonus' => 22, 'ename' => 'rx5'),
			6 => array('cname' => '任选六', 'bonus' => 12, 'ename' => 'rx6')
		);
		
		$numArr = array(1 => 'A', 2 => '2', 3 => '3', 4 => '4', 5 => '5', 6 => '6', 
				7 => '7', 8 => '8', 9 => '9', 10 => '10', 11 => 'J', 12 => 'Q', 13 => 'K');
		$lotteryId = KLPK;
		$inputGetArr = $this->input->get ( NULL, true );
		$this->load->driver ( 'cache', array ('adapter' => 'redis') );
		$REDIS = $this->config->item ( 'REDIS' );
		$info = json_decode ( $this->cache->get ( $REDIS ['KLPK_ISSUE_TZ'] ), true );
		$miss = json_decode ($this->cache->get ( $REDIS ['KLPK_MISS'] ), true );
		$lastIssue = empty ( $info ['aIssue'] ) ? $info ['lIssue'] ['seExpect'] : $info ['aIssue'] ['seExpect'];
		$awardArr = unserialize( $this->cache->get ( $REDIS ['KLPK_AWARD'] ));
		$count = 0;
		$followIssues = json_decode ( $this->cache->hGet ( $REDIS ['ISSUE_COMING'], 'KLPK' ), true );
		$multi = !empty( $inputGetArr['multi'] ) ? $inputGetArr['multi'] : 1;

		if (! empty ( $inputGetArr ['orderId'] )){
            $this->load->model ( 'order_model' );
			$codes = $this->order_model->getCodesById(trim ( $inputGetArr ['orderId'] ));
		}elseif (! empty ( $inputGetArr ['chaseId'] )) {
            $this->load->model ( 'chase_order_model' );
			$codes = $this->chase_order_model->getCodesById(trim ( $inputGetArr ['chaseId'] ));
		}
		
		$chases = array();
		$issue = 1;
		while (count($chases) < 88 && $issue) {
			$issue = each($followIssues);
			$issue = $issue['value'];
			if ($issue && $issue['show_end_time'] > date('Y-m-d H:i:s')) {
				$chases [$issue ['issue']] = array (
						'award_time' => $issue ['award_time'],
						'show_end_time' => $issue ['show_end_time'],
						'multi' => $multi,
						'money' => 0
				);
			}
		}
		
		$awardNum = "'正', '在', '开', '奖', '中'";
		if ($info['nlIssue']['seExpect'] == $awardArr[0]['issue']) {
			$awardNum = '';
			$awardArr = explode('|', $awardArr[0]['awardNum']);
			foreach ($awardArr as $awards) {
				$aArr = explode(',', $awards);
				foreach ($aArr as $ar) {
					$awardNum .= "'".$ar."', ";
				}
			}
			$awardNum = substr($awardNum, 0, -1);
		}
		
		//$awardNum = ($info['nlIssue']['seExpect'] == $awardArr[0]['issue']) ? $awardArr[0]['awardNum'] : "'正', '在', '开', '奖', '中'";
		// 计算已售、剩余期数
		$rest = 89-substr($info ['cIssue'] ['seExpect'], -2);
		
		reset ( $miss );
		$ms = each ( $miss );
		$data ['lotteryId'] = $lotteryId;
		$data ['playTypeArr'] = $playTypeArr;
		$data['enName'] = 'klpk';
		$data['info'] = $info;
		$data['rest'] = $rest;
		$data['prev'] = $lastIssue;
		$data['awardNum'] = $awardNum;
		$data['chases'] = $chases;
		$data['chaselength'] = 10;
		$data['miss'] = $ms['value'];
		$data['numArr'] = $numArr;
		$lotteryConfig = $this->cache->get($REDIS['LOTTERY_CONFIG']);
		$data['lotteryConfig'] = json_decode($lotteryConfig, true);
		// 是否绑定了 身份证, 手机
		$data['showBind'] = false;
		$data['uservpop'] = $this->cache->get($this->REDIS['USERVPOP']);
		$data['codes'] = $codes;
		if (!empty($this->uinfo) && !$this->isBindForRecharge())
		{
			$data['showBind'] = true;
		}
		$this->load->view ( 'v1.2/klpk/index', $data );
	}
}