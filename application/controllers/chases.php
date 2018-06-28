<?php
class Chases extends MY_Controller
{

	public function __construct()
	{
		parent::__construct ();
		$this->load->model ( 'chase_order_model' );
	}

	public function detail($chaseId)
	{
		$this->load->library (array('BetCnName', 'LotteryDetail'));
		$this->load->model ( 'lottery_model', 'Lottery' );
		$REDIS = $this->config->item ( 'REDIS' );
		// # 查出订单的情况
		$order = $this->chase_order_model->getChaseInfoById ( array (
				'uid' => $this->uid,
				'chaseId' => $chaseId 
		) );
		$enName = $this->Lottery->getEnName ( $order ['info'] ['lid'] );
		$current = json_decode ( $this->cache->get ( $REDIS [$this->Lottery->getCache($order ['info'] ['lid'])] ), true );
		$info = array();
		foreach (explode(';', $order['info']['codes']) as $code) {
			$isChase = false;
			if ($order['lid'] == DLT && strpos($code, '135:1') !== false) $isChase = true;
			$res = $this->lotterydetail->renderCode($code, $order['info']['lid'], null, null, $isChase, 'chase');
			preg_match('/\[(.+)\](.+)/', $res['code'], $matches);
			array_push($info, array($matches[1], $matches[2]));
		}
		
		if (! empty ( $order ))
		{
			// 数字彩
			$this->display ( 'chases/detail', array (
					'order' => $order ['info'],
					'cnName' => $this->Lottery->getCnName ( $order ['info'] ['lid'] ),
					'enName' => $enName,
					'detail' => $order ['detail'],
					'award' => $order ['award'],
					'singleFlag' =>$order ['singleFlag'],
					'curissue' => $current ['cIssue'] ['seExpect'],
					'info'	=> $info,
			), 'v1.2' );
		} else
		{
			$this->redirect ( '/error/' );
		}
	}

	public function info()
	{
		$chaseId = $this->input->post ( 'orderId', true );
		
		$this->load->model ( 'lottery_model', 'Lottery' );
		
		$order = $this->chase_order_model->getManageById ( array (
				'uid' => $this->uid,
				'chaseId' => $chaseId 
		) );
		$money = number_format ( ParseUnit ( $order ['money'], 1 ), 2 );
		$remain_money = number_format ( ParseUnit ( $this->uinfo ['money'], 1 ), 2 );
		$rst = array (
				'code' => $order ['money'] > $this->uinfo ['money'] ? 12 : 0,
				'lid' => $order ['lid'],
				'betMoney' => $order ['isChase'] == 1 ? $order ['betTnum'] * 3 : $order ['betTnum'] * 2,
				'totalIssue' => $order ['totalIssue'],
				'money' => $money,
				'remain_money' => $remain_money 
		);
		
		header ( 'Content-type: application/json' );
		echo json_encode ( $rst );
	}

	public function stopChase()
	{
		$chaseId = $this->input->post ( 'chaseId', true );
		$lid = $this->input->post ( 'lid', true );
		if ($this->uid && $chaseId && $lid)
		{
			$res = $this->chase_order_model->stopOrders ( $this->uid, $chaseId, $lid );
			exit ( $res );
		}
	}

	public function stoporders()
	{
		$issues = $this->input->post ( 'issue', true );
		$chaseId = $this->input->post ( 'chaseId', true );
		$lid = $this->input->post ( 'lid', true );
		if ($this->uid && $chaseId && $issues)
		{
			$res = $this->chase_order_model->stopOrders ( $this->uid, $chaseId, $lid, $issues );
			exit ( $res );
		}
	}
}