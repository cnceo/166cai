<?php

class Fcsd extends MY_Controller {

	public static $TYPE_MAP = array(
        'zx' => array(
            'cnName' => '直选',
            'bonus' => '1040元',
            'rule' => '玩法说明：每位至少选择 1 个号码，所选号码与开奖号码相同（且顺序一致）即中1040元！',
        ),
		'z3' => array(
			'cnName' => '组三',
			'bonus' => '346元',
            'rule' => '玩法说明：至少选择 2 个号码，所选号码与开奖号码一致(顺序不限)，且开奖号码有任意两位相同即中346元！ ',
		),
		'z6' => array(
			'cnName' => '组六',
			'bonus' => '173元',
            'rule' => '玩法说明：至少选择 3 个号码，所选号码与开奖号码相同(顺序不限)即中173元！',
		),
    );
	
	public function index()
	{
        $this->display('fcsd/index', $this->getData(), 'v1.2');
	}
	public function dssc($type = 'zx')
	{
		if (!in_array($type, array('zx', 'z3', 'z6'))) exit('参数错误！');
      $this->display('fcsd/'.$type, $this->getData(), 'v1.2');
	}
	private function getInfo()
	{
		$lotteryId = Lottery_Model::FCSD;
		$this->load->driver('cache', array('adapter' => 'redis'));
		$this->redis = $this->config->item('REDIS');
		$miss = unserialize($this->cache->get($this->redis['FC3D_MISS']));
		reset($miss);
		$miss = each($miss);
		$info = $this->Lottery->getKjinfo($lotteryId);
		return array(
				'miss' => array(
						'zx' => array(
								$miss['value'][0],
								$miss['value'][1],
								$miss['value'][2]
						),
						'z3' => array(
								$miss['value'][3]
						),
						'z6' => array(
								$miss['value'][3]
						)
				),
				'info' => $info
		);
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
		
		$info = $this->getInfo();
		$followIssues = json_decode($this->cache->hGet($this->redis['ISSUE_COMING'], 'FCSD'), true);
		$followIssues = array_slice($followIssues, 0, 50);//换期不请求数据,多取三期
		foreach ($followIssues as $i => $issue) 
		{
        	$chases[$issue['issue']] = array(
        		'award_time' => $issue['award_time'],
        		'show_end_time' => $issue['show_end_time'],
        		'multi' => $multi,
        		'money' => 0
        	);
        }
        
        $this->load->model('info_model', 'Info');
        $infoList = $this->Info->getListByCategory(3, 0, 5);
        $this->load->model('lottery_config_model', 'lotteryConfig');
        $dsjzsj = $this->lotteryConfig->getEndTime(FCSD);
        $dsjzsj = $dsjzsj[0];
		$data = array(
            'fcsdType' => 'zx',
            'typeMAP' => self::$TYPE_MAP,
            'boxCount' => 3,
            'cnName' => '福彩3D',
            'enName' => 'fcsd',
			'chases' => $chases,
        	'chaselength' => 10,
			'multi' => $multi,
            'lotteryId' => FCSD,
            'dsjzsj' =>$dsjzsj,
			'info' => $info['info'],
			'miss' => $info['miss'],
			'tzjqurl' => '/activity/fucai3d',
			'infoList' => $infoList['data'],
			'codes' => $codes,
        );
		if (!empty($info['info']['current']['endTime'])) {
			$data['fendTime'] = floor($info['info']['current']['endTime']/1000)-$lotteryConfig[FCSD]['ahead'] * 60;
			$data['endTime'] = floor($info['info']['current']['endTime']/1000);
		}
		return $data;
	}
}
