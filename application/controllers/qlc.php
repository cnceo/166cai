<?php

class Qlc extends MY_Controller {

    public function index() 
    {
        $this->display('qlc/index', $this->getData(), 'v1.2');
    }
    public function dssc()
    {
        $this->display('qlc/dssc', $this->getData(), 'v1.2');
    }
    private function getData()
    {
        $lotteryId = Lottery_Model::QLC;
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
        $followIssues = json_decode($this->cache->hGet($REDIS['ISSUE_COMING'], 'QLC'), true);
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
        $miss = unserialize($this->cache->get($REDIS['QLC_MISS']));
        reset($miss);
        $miss = each($miss);
        $info = $this->Lottery->getKjinfo($lotteryId);
        $tzjqurl = '/academy';
        $this->load->model('info_model', 'Info');
        $infoList = $this->Info->getListByCategory(1, 0, 5);
        $this->load->model('lottery_config_model', 'lotteryConfig');
        $dsjzsj = $this->lotteryConfig->getEndTime(QLC);
        $dsjzsj = $dsjzsj[0];
        $data = array(
            'cnName' => '七乐彩',
            'enName' => 'qlc',
            'lotteryId' => QLC,
            'chases' => $chases,
            'chaselength' => 10,
            'multi' => $multi,
            'dsjzsj' => $dsjzsj,
            'info' => $info,
            'miss' => $miss['value'],
            'tzjqurl' => $tzjqurl,
            'infoList' => $infoList['data'],
        	'codes' => $codes,
        );
        if (!empty($info['current']['endTime'])) {
            $data['fendTime'] = floor($info['current']['endTime']/1000)-$lotteryConfig[QLC]['ahead'] * 60;
            $data['endTime'] = floor($info['current']['endTime']/1000);
        }

        return $data;
    }
}