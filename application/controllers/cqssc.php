<?php
class Cqssc extends MY_Controller
{

	public function index()
	{
	    //时时彩下架
	    $this->redirect('/error');
		$inputGetArr = $this->input->get ( NULL, true );
		//获取订单数据
		if (! empty ( $inputGetArr ['orderId'] )) {
			$this->load->model ( 'order_model' );
			$codes = $this->order_model->getCodesById(trim ( $inputGetArr ['orderId'] ));
		} elseif ($this->uid) {
			$this->load->model ( 'order_model' );
		} if (! empty ( $inputGetArr ['chaseId'] )) {
			$this->load->model ( 'chase_order_model' );
			$codes = $this->chase_order_model->getCodesById(trim ( $inputGetArr ['chaseId'] ));
		} if (! empty ( $inputGetArr ['codes'] )) {
			$codes = $inputGetArr ['codes'];
		}
		
		$this->load->driver ( 'cache', array ('adapter' => 'redis') );
		$REDIS = $this->config->item ( 'REDIS' );
		$awardArr = unserialize($this->cache->get ( $REDIS ['CQSSC_AWARD'] ));
		$miss = unserialize($this->cache->get ( $REDIS ['CQSSC_MISS'] ));
		$info = json_decode ( $this->cache->get ( $REDIS ['CQSSC_ISSUE_TZ'] ), true );
		$followIssues = json_decode ( $this->cache->hGet ( $REDIS ['ISSUE_COMING'], 'CQSSC' ), true );
		$lastIssue = empty ( $info ['aIssue'] ) ? $info ['lIssue'] ['seExpect'] : $info ['aIssue'] ['seExpect'];
		$multi = !empty( $inputGetArr['multi'] ) ? $inputGetArr['multi'] : 1;
		
		$chases = array();
		$issue = 1;
		while (count($chases) < 120 && $issue) {
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
		$count = substr($info ['cIssue'] ['seExpect'], -3)-1;
		$rest = 120-$count;
		
		$history = array_slice ( $awardArr, 0, 10 );
		
		foreach ( $history as $k => $h ) {
			if ($history [$k] ['issue'] == $lastIssue) $flag = true;
		}
		if (! $flag) {
			$arr = array ('issue' => $lastIssue);
			$awardNum = "'正', '在', '开', '奖', '中'";
			array_unshift ( $history, $arr );
			array_pop ( $history );
		} else {
			$awardNum = $history [0] ['awardNum'];
		}
		krsort ( $history );
		$history = array_values ( $history );
		$ms = each ( $miss );
		$misscurrent = array(
			'1xzhi' => array($ms['value'][5]), 
			'2xzhi' => explode('|', $ms['value'][3]), 
			'2xzu' => array($ms['value'][4]), 
			'3xzhi' => explode('|', $ms['value'][1]), 
			'3xzu3' => array($ms['value'][2]), 
			'3xzu6' => array($ms['value'][2]),
			'5xzhi' => explode('|', $ms['value'][0]), 
			'5xt' => explode('|', $ms['value'][0]), 
			'dxds' => explode('|', $ms['value'][6])
		);
		$data = array(
			'lotteryId' => CQSSC,
			'info' => $info,
			'enName' => 'cqssc',
			'count' => $count,
			'rest' => $rest,
			'multi' => $multi,
			'chases' => $chases,
			'chaselength' => 10,
			'history' => $history,
			'mall' => $miss,
			'awardNum' => $awardNum,
			'miss' => $misscurrent,
			'codes' => $codes,
		);
		
		$this->display('cqssc/index', $data, 'v1.2');
	}

}
