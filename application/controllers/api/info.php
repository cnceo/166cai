<?php

/*
 * 订单信息处理 创建、付款通用接口
 * @date:2015-05-22
 */
require_once APPPATH . '/core/CommonController.php';
class Info extends CommonController 
{
	
	public function __construct()
	{
		parent::__construct();
		$this->load->driver('cache', array('adapter' => 'redis'));
		if(!in_array($this->get_client_ip(), $this->config->item('own_ip')))
		{
			$response = array(
					'code' => 1,
					'msg'  => '查询失败',
					'data' => array(),
			);
			echo json_encode($response);
			die();
		}
	}
	
	public function getIndex() {

		$this->load->driver('cache', array('adapter' => 'redis'));
		$REDIS = $this->config->item('REDIS');
		$sy = unserialize($this->cache->redis->get($REDIS['SHOUYE']));
		for ($i = 1; $i <= 4; $i++) {
			$id = 0;
			$tmp = array();
			foreach ($sy['num'.$i] as $val) {
				$urlArr = parse_url($val['url']);
				preg_match('/\d+/', $urlArr['path'], $matches);
				if ($matches[0] > $id) {
					$id = $matches[0];
					$tmp = array(
						'type' => $sy['numtype'][$i]['title'],
						'title'=> $val['title'],
						'url' => $val['url'],
					);
				}
			}
			$res[] = $tmp;
		}
		exit(json_encode(array('lottery' => $res)));
	}
}
