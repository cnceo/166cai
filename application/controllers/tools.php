<?php
class Tools extends MY_Controller {
	
	public function caculate($lottery = 'ssq') {
		
		$lotteryMap = array('ssq' => '双色球', 'dlt' => '大乐透');
		if (!array_key_exists($lottery, $lotteryMap)) $this->redirect('/error');
		
		$cnName = $lotteryMap[$lottery];
		$this->load->model('lottery_model');
		$issueArr = $this->lottery_model->getAwardList($lottery, 50);
		$this->display("tools/caculate{$lottery}", compact('zjtjUrl', 'chartUrl', 'issueArr', 'cnName'), 'v1.1');
	}
}