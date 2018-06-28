<?php

class Plw extends MY_Controller {
	
	public function index()
	{
        $this->display('plw/index', $this->getData(), 'v1.2');
	}
	public function dssc()
	{
        $this->display('plw/dssc', $this->getData(), 'v1.2');
	}
	private function getData()
	{
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
		$lotteryId = Lottery_Model::PLW;
		$this->load->driver('cache', array('adapter' => 'redis'));
		$REDIS = $this->config->item('REDIS');
		$followIssues = json_decode($this->cache->hGet($REDIS['ISSUE_COMING'], 'PLW'), true);
		$followIssues = array_slice($followIssues, 0, 50);//换期不请求数据,多取三期
		foreach ($followIssues as $i => $issue) {
        	$chases["20".$issue['issue']] = array(
        		'award_time' => $issue['award_time'],
        		'show_end_time' => $issue['show_end_time'],
        		'multi' => $multi,
        		'money' => 0
        	);
        }
		$miss = unserialize($this->cache->get($REDIS['PL5_MISS']));
		reset($miss);
		$miss = each($miss);
		$info = $this->Lottery->getKjinfo($lotteryId);
		$tzjqurl = '/academy';
		$this->load->model('info_model', 'Info');
		$infoList = $this->Info->getListByCategory(1, 0, 5);
        $this->load->model('lottery_config_model', 'lotteryConfig');
        $dsjzsj = $this->lotteryConfig->getEndTime(PLW);
        $dsjzsj = $dsjzsj[0];
		$data = array(
		  	'plwType' => 'bz',
			'boxCount' => 5,
			'cnName' => '排列五',
			'enName' => 'plw',
        	'chases' => $chases,
        	'chaselength' => 10,
        	'multi' => $multi,
        	'dsjzsj'=> $dsjzsj,
			'lotteryId' => PLW,
        	'info' => $info,
        	'miss' => $miss['value'],
        	'tzjqurl' => $tzjqurl,
			'infoList' => $infoList['data'],
			'codes' => $codes,
		);
		if (!empty($info['current']['endTime'])) {
			$data['fendTime'] = floor($info['current']['endTime']/1000)-$lotteryConfig[PLW]['ahead'] * 60;
			$data['endTime'] = floor($info['current']['endTime']/1000);
		}
		return $data;
	}
}