<?php
class Jlks extends MY_Controller {

	public function index()
	{
		$lotteryId = JLKS;
		$inputGetArr = $this->input->get ( NULL, true );
		$this->load->driver ( 'cache', array ('adapter' => 'redis') );
		$REDIS = $this->config->item ( 'REDIS' );
		$info = json_decode ( $this->cache->get ( $REDIS ['JLKS_ISSUE_TZ'] ), true );
		$miss = json_decode ($this->cache->get ( $REDIS ['JLKS_MISS'] ), true );
		$lastIssue = empty ( $info ['aIssue'] ) ? $info ['lIssue'] ['seExpect'] : $info ['aIssue'] ['seExpect'];
		$awardArr = unserialize($this->cache->get ( $REDIS ['JLKS_AWARD'] ));
		$followIssues = json_decode ( $this->cache->hGet ( $REDIS ['ISSUE_COMING'], 'JLKS' ), true );
		$multi = !empty( $inputGetArr['multi'] ) ? $inputGetArr['multi'] : 1;
		
		if (! empty ( $inputGetArr ['orderId'] )){
			//1.2废弃之前在后端拼接js数组的方法，等到全部切到1.2把原来方法删除。
            $this->load->model ( 'order_model' );
			$codes = $this->order_model->getCodesById(trim ( $inputGetArr ['orderId'] ));
		}elseif (! empty ( $inputGetArr ['chaseId'] )) {
			//1.2废弃之前在后端拼接js数组的方法，等到全部切到1.2把原来方法删除。
            $this->load->model ( 'chase_order_model' );
			$codes = $this->chase_order_model->getCodesById(trim ( $inputGetArr ['chaseId'] ));
		}

		if (!empty($codes[0])) {
			foreach (explode(';', $codes[0]) as $code) {
				$codeArr = explode(':', $code);
				if ($codeArr[1] == 1) {
					$codesArr[$codeArr[1]] = explode(',', $codeArr[0]);
				}else {
					$codesArr[$codeArr[1]][] = $codeArr[0];
				}
			}
		}
		
		$chases = array();
		$issue = 1;
		while (count($chases) < 87 && $issue) {
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
		
		$awardNum = ($info['nlIssue']['seExpect'] == $awardArr[0]['issue']) ? $awardArr[0]['awardNum'] : "'正', '在', '开', '奖', '中'";
		$rest = 88-substr($info ['cIssue'] ['seExpect'], -2);
		
		reset ( $miss );
		$ms = each ( $miss );
		
		$data['lotteryId'] = $lotteryId;
		$data['enName'] = 'jlks';
		$data['info'] = $info;
		$data['rest'] = $rest;
		$data['prev'] = $lastIssue;
		$data['awardNum'] = $awardNum;
		$data['chases'] = $chases;
		$data['chaselength'] = 10;
		$data['miss'] = explode('|', $ms['value'][0]);
		$data['lenr'] = explode('|', $ms['value'][1]);
		$lotteryConfig = $this->cache->get($REDIS['LOTTERY_CONFIG']);
		$data['lotteryConfig'] = json_decode($lotteryConfig, true);
		$data['uservpop'] = $this->cache->get($this->REDIS['USERVPOP']);
		$data['codes'] = $codesArr;
		// 是否绑定了 身份证, 手机
		$data['showBind'] = false;
		if (!empty($this->uinfo) && !$this->isBindForRecharge()) $data['showBind'] = true;
		$this->load->view('v1.2/jlks/index', $data);
	}

}