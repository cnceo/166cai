<?php

class Ssq extends MY_Controller {

    public function index() 
    {
        $this->display('ssq/index', $this->getData(), 'v1.2');
    }
    public function dssc()
    {
        $this->display('ssq/dssc', $this->getData(), 'v1.2');
    }
    private function getData()
    {
        $lotteryId = Lottery_Model::SSQ;
        $inputGetArr = $this->input->get(NULL, true);
        if( !empty( $inputGetArr['orderId'] ) ) {
            $this->load->model ( 'order_model' );
			$codes = $this->order_model->getCodesById(trim ( $inputGetArr ['orderId'] ));
        } 
        if( !empty( $inputGetArr['chaseId'] ) ) {
            $this->load->model ( 'chase_order_model' );
			$codes = $this->chase_order_model->getCodesById(trim ( $inputGetArr ['chaseId'] ));
        }
        if( !empty( $inputGetArr['codes'] ) ) $codes = array($inputGetArr ['codes']);
        $multi = !empty( $inputGetArr['multi'] ) ? $inputGetArr['multi'] : 1;    
        $this->load->driver('cache', array('adapter' => 'redis'));
        $REDIS = $this->config->item('REDIS');
        $followIssues = json_decode($this->cache->hGet($REDIS['ISSUE_COMING'], 'SSQ'), true);
        $chases = array();
        $issue = 1;
        while (count($chases) < 50 && $issue) {
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
        $info = $this->Lottery->getKjinfo($lotteryId);
        $tzjqurl = '/activity/kxzhuihao3';
        $this->load->model('lottery_config_model', 'lotteryConfig');
        $dsjzsj = $this->lotteryConfig->getEndTime($lotteryId);
        $dsjzsj = $dsjzsj[0];
        $data = array(
            'cnName' => $this->Lottery->getCnName($lotteryId),
            'enName' => $this->Lottery->getEnName($lotteryId),
            'chases' => $chases,
            'chaselength' => 10,
            'dsjzsj'=> $dsjzsj,
            'lotteryId' => $lotteryId,
            'info' => $info,
            'multi' => $multi,
            'tzjqurl' => $tzjqurl,
        	'codes' => $codes,
        );

        return $data;
    }
}
