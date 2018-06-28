<?php
class Cli_test extends MY_Controller {
	
	
	public function distribute166() {
		$this->load->model('test_model');
		$uidRes = $this->test_model->getUserIdByRegistertime('2018-01-01 00:00:00', '2018-01-01 13:23:51');
		foreach ($uidRes as $uid) {
			$this->load->library('libredpack');
			$this->load->model('user_model');
			$uinfo = $this->user_model->getUserInfo($uid);
			$this->libredpack->hongbao166('distribute', array('uid' => $uid, 'phone' => $uinfo['phone'], 'platformId' => $uinfo['platform'], 'channel' => 0));
		}
	}
}