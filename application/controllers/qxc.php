<?php

class Qxc extends MY_Controller {
	
	public function index()
	{
		$this->display('qxc/index', $this->getData(), 'v1.2');
	}
	public function dssc()
	{
		$this->display('qxc/dssc', $this->getData(), 'v1.2');
	}
	private function getData()
	{
		$lotteryId = Lottery_Model::QXC;
		$inputGetArr = $this->input->get(NULL, true);
		if( !empty( $inputGetArr['orderId'] ) ) {
            $this->load->model ( 'order_model' );
			$codes = $this->order_model->getCodesById(trim ( $inputGetArr ['orderId'] ));
		}
		if( !empty( $inputGetArr['chaseId'] ) ) {
            $this->load->model ( 'chase_order_model' );
			$codes = $this->chase_order_model->getCodesById(trim ( $inputGetArr ['chaseId'] ));
		}
		$multi = !empty( $inputGetArr['multi'] ) ? $inputGetArr['multi'] : 1;
		$this->load->driver('cache', array('adapter' => 'redis'));
		$REDIS = $this->config->item('REDIS');
		$followIssues = json_decode($this->cache->hGet($REDIS['ISSUE_COMING'], 'QXC'), true);
		$followIssues = array_slice($followIssues, 0, 50);//换期不请求数据,多取一期
		foreach ($followIssues as $i => $issue) {
        	$chases["20".$issue['issue']] = array(
        		'award_time' => $issue['award_time'],
        		'show_end_time' => $issue['show_end_time'],
        		'multi' => $multi,
        		'money' => 0
        	);
        }
		$miss = unserialize($this->cache->get($REDIS['QXC_MISS']));
		reset($miss);
		$miss = each($miss);
		$info = $this->Lottery->getKjinfo($lotteryId);
		$tzjqurl = '/academy';
		$this->load->model('info_model', 'Info');
		$infoList = $this->Info->getListByCategory(1, 0, 5);
        $this->load->model('lottery_config_model', 'lotteryConfig');
        $dsjzsj = $this->lotteryConfig->getEndTime(QXC);
        $dsjzsj = $dsjzsj[0];
		$data = array(
			'qxcType' => 'bz',
			'boxCount' => 7,
			'cnName' => '七星彩',
			'enName' => 'qxc',
        	'chases' => $chases,
        	'chaselength' => 10,
        	'multi' => $multi,
        	'dsjzsj'=>$dsjzsj,
			'lotteryId' => QXC,
        	'info' => $info,
        	'miss' => $miss['value'],
        	'tzjqurl' => $tzjqurl,
			'infoList' => $infoList['data'],
			'codes' => $codes,
		);
		
		if (!empty($info['current']['endTime'])) {
			$data['fendTime'] = floor($info['current']['endTime']/1000)-$lotteryConfig[QXC]['ahead'] * 60;
			$data['endTime'] = floor($info['current']['endTime']/1000);
		}

		return $data;
	}
}