<?php

class fiveLeague extends MY_Controller
{
	private $_type = array('前瞻', '推荐', '分析');
	private $_team = array('英超', '西甲', '德甲', '意甲', '法甲');
	
	public function index() {
	    $this->check_capacity("6_4_1");
		$type = $this->_type;
		$team = $this->_team;
		$this->load->model('model_shouye_link', 'link');
		for ($j = 0; $j < 5; $j++) {
			for ($i = 1; $i <= 3; $i++){
				$data["wdls".$j.$i] = $this->link->getDataByPosition("wdls".$j.$i);
			}
		}
		$info = $this->input->post();
		$opennew = $this->input->get('opennew') ? 1 : 0;
		$index = $this->input->get_post('index');
		$flag = false;
		if ($info) {
			foreach ($info as $key => $val) {
				foreach ($val as $priority => $value) {
					if ($value['redflag']) {
						$value['redflag'] = 1;
					}else {
						$value['redflag'] = 0;
					}
					if (empty($data[$key][$priority])) {
						$value['priority'] = $priority;
						$value['position'] = $key;
						$istData[] = $value;
					}else {
						$this->link->updateData($value, $data[$key][$priority]['id']);
						$flag = true;
					}
				}
			}
		}
		if (!empty($istData))
		{
			$flag = true;
			foreach ($istData as $dt)
			{
				//$this->syslog(32, "首页资讯更新内容：".$dt['title']);
			}
			$this->link->insertAllData($istData);
		}
		if ($flag) {
			$this->redirect("/backend/fiveLeague?index=".$index."&opennew=1");
		}
		$this->load->view('fiveleague/index', compact('type', 'data', 'opennew', 'index', 'team'));
	}
	
	public function zixun($index = 0) {
		$this->load->model('model_shouye_link', 'model');
		for ($i = 1; $i <= 3; $i++) {
			$linktype[] = "wdls".$index.$i;
		}
		foreach ($linktype as $k => $type) {
			$data['info'][$k] = $this->model->getDataByPosition($type);
		}
		$data['team'] = $this->_team[$index];
		$data['infotype'] = $this->_type;
		$this->load->view('fiveleague/zixun', $data);
	}
	
	public function onlinezx($n) {
		for ($i = 1; $i <= 3; $i++) {
			$linktype[] = "wdls".$n.$i;
		}
		foreach ($linktype as $position) {
			$this->refreshCache($position);
		}
		$this->redirect('/backend/fiveLeague?saved=1');
	}
	
	private function refreshCache($position) {
		$this->load->model('model_shouye_link', 'model');
		$data = $this->model->getDataByPosition($position);
		$this->load->driver('cache', array('adapter' => 'redis'));
		$REDIS = $this->config->item('REDIS');
		$res = unserialize($this->cache->get($REDIS['SHOUYE']));
		$res[$position] = $data;
		unset($res['ln'], $res['wdls1'], $res['wdls2'], $res['wdls3']);
		$this->cache->save($REDIS['SHOUYE'], serialize($res), 0);
	}
}