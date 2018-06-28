<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * 竞彩即时消息读取脚本
 * @author shigx
 *
 */
class Cli_Zhisheng_Messages extends MY_Controller 
{
	private $apiUrl;
	public function __construct() 
	{
		parent::__construct();
		$this->load->driver('cache', array('adapter' => 'redis'));
		$this->load->library('tools');
		$this->load->model('api_zhisheng_model', 'api_model');
		$this->config->load('jcMatch');
		$this->redis = $this->config->item('redisList');
		$this->apiUrl = $this->config->item('apiUrl');
	}
	
	/**
	 * 入口方法
	 */
	public function index()
	{
		while (true)
		{
			$this->jclqMessages();
			$this->jczqMessages();
			sleep(1);
		}
	}
	
	/**
	 * 篮球赛事对阵信息抓取
	 */
	public function jclqMessages()
	{
		$url = $this->apiUrl . "home/base/?oid=6003";
		$content = $this->tools->request($url);
		$content = json_decode($content, true);
		if($content['desc'] != 'succ' || $content['code'] != '0000')
		{
			return ;
		}
		$messgeId = $content['c']['fn'];
		$lMessgeId = $this->cache->get($this->redis['JCLQ_MESSGEID']);
		//最新消息和上次存取消息相等
		if($messgeId == $lMessgeId)
		{
			return ;
		}
		//先将6003接口返回的数据处理掉
		$datas = array();
		$mids = array();
		foreach ($content['row'] as $value)
		{
			$mids[] = $value['bh'];
		}
		if($mids)
		{
			//过滤非竞彩赛事
			$dMids = $this->api_model->getMatchMids($mids);
			$intersectMids = array_intersect($dMids, $mids);
			foreach ($content['row'] as $value)
			{
				if(in_array($value['bh'], $intersectMids))
				{
					$datas[] = $this->formatLqData($value);
				}
			}
		}
		//消息存入缓存
		$this->cache->save($this->redis['JCLQ_MESSGEID'] . $messgeId, json_encode($datas), 600);
		
		if($lMessgeId && ($messgeId > $lMessgeId))
		{
			$messIndx = $messgeId - 2; //最新消息已在上一步操作 这里从倒数第二期开始处理
			for($i = $messIndx; $i >= $lMessgeId; $i--)
			{
				$url = $this->apiUrl . "home/base/?oid=6004&fn={$i}";
				$content = $this->tools->request($url);
				$content = json_decode($content, true);
				if($content['desc'] != 'succ' || $content['code'] != '0000' || empty($content['row']))
				{
					continue;
				}
				$mids = array();
				$datas = array();
				foreach ($content['row'] as $value)
				{
					$mids[] = $value['bh'];
				}
				if($mids)
				{
					$dMids = $this->api_model->getMatchMids($mids);
					$intersectMids = array_intersect($dMids, $mids);
					foreach ($content['row'] as $value)
					{
						if(in_array($value['bh'], $intersectMids))
						{
							$datas[] = $this->formatLqData($value);
						}
					}
				}
				$this->cache->save($this->redis['JCLQ_MESSGEID'] . ($i + 1), json_encode($datas), 600);
			}
		}
		
		$this->cache->save($this->redis['JCLQ_MESSGEID'], $messgeId, 600);
	}
	
	/**
	 * 篮球赛事对阵信息抓取
	 */
	public function jczqMessages()
	{
		$url = $this->apiUrl . "home/base/?oid=3007";
		$content = $this->tools->request($url);
		$content = json_decode($content, true);
		if($content['desc'] != 'succ' || $content['code'] != '0000')
		{
			return ;
		}
		$messgeId = $content['c']['fn'];
		$lMessgeId = $this->cache->get($this->redis['JCZQ_MESSGEID']);
		//最新消息和上次存取消息相等
		if($messgeId == $lMessgeId)
		{
			return ;
		}
		//先将3007接口返回的数据处理掉
		$datas = array();
		$mids = array();
		foreach ($content['row'] as $value)
		{
			$mids[] = $value['bh'];
		}
		if($mids)
		{
			//过滤赛事
			$dMids = $this->api_model->getZqMatchMids($mids);
			$intersectMids = array_intersect($dMids, $mids);
			foreach ($content['row'] as $value)
			{
				if(in_array($value['bh'], $intersectMids))
				{
					$datas[] = $this->formatZqData($value);
				}
			}
		}
		
		//消息存入缓存
		$this->cache->save($this->redis['JCZQ_MESSGEID'] . $messgeId, json_encode($datas), 600);
	
		if($lMessgeId && ($messgeId > $lMessgeId))
		{
			$messIndx = $messgeId - 2; //最新消息已在上一步操作 这里从倒数第二期开始处理
			for($i = $messIndx; $i >= $lMessgeId; $i--)
			{
				$url = $this->apiUrl . "home/base/?oid=3008&fn={$i}";
				$content = $this->tools->request($url);
				$content = json_decode($content, true);
				if($content['desc'] != 'succ' || $content['code'] != '0000' || empty($content['row']))
				{
					continue;
				}
				$mids = array();
				$datas = array();
				foreach ($content['row'] as $value)
				{
					$mids[] = $value['bh'];
				}
				if($mids)
				{
					$dMids = $this->api_model->getZqMatchMids($mids);
					$intersectMids = array_intersect($dMids, $mids);
					foreach ($content['row'] as $value)
					{
						if(in_array($value['bh'], $intersectMids))
						{
							$datas[] = $this->formatZqData($value);
						}
					}
				}
				
				$this->cache->save($this->redis['JCZQ_MESSGEID'] . ($i + 1), json_encode($datas), 600);
			}
		}
		
		$this->cache->save($this->redis['JCZQ_MESSGEID'], $messgeId, 600);
	}
	
	/**
	 * 篮球数据格式化
	 * @param unknown_type $rows
	 */
	private function formatLqData($value)
	{
		$data = array();
		$data['mid'] = $value['bh'];
		$data['state'] = $value['state'];
		/*$data['hs1'] = $value['hs1'];
		$data['hs2'] = $value['hs2'];
		$data['hs3'] = $value['hs3'];
		$data['hs4'] = $value['hs4'];
		$data['hot'] = $value['hsj'];*/
		$data['hqt'] = $value['hs1'] + $value['hs2'] + $value['hs3'] + $value['hs4'] + $value['hsj'];
		/*$data['as1'] = $value['as1'];
		$data['as2'] = $value['as2'];
		$data['as3'] = $value['as3'];
		$data['as4'] = $value['as4'];
		$data['aot'] = $value['asj'];*/
		$data['aqt'] = $value['as1'] + $value['as2'] + $value['as3'] + $value['as4'] + $value['asj'];
		$data['type'] = $value['type'];
		$data['stime'] = (isset($value['stime']) && !empty($value['stime'])) ? '00:' . $value['stime'] : 0;
		return $data;
	}
	
	/**
	 * 足球数据格式化
	 * @param unknown_type $value
	 */
	private function formatZqData($value)
	{
		$data = array();
		$data['mid'] = $value['bh'];
		$data['state'] = $this->getZqState($value['state']);
		//$data['bc'] = str_replace('-', ':', $value['bc']);
		$data['hqt'] = $value['hf'];
		$data['aqt'] = $value['af'];
		if($value['tstime'])
		{
			$times = explode(',', $value['tstime']);
			$data['stime'] = date("Y-m-d H:i:s", mktime($times[3], $times[4], $times[5], $times[1], $times[2], $times[0]));
		}
		else
		{
			$data['stime'] = 0;
		}
		
		return $data;
	}
	
	/**
	 * 足球状态转换处理
	 * @param unknown_type $state
	 * @return Ambigous <string>
	 */
	private function getZqState($state)
	{
		$stateArr = array(
			'1' => '1',
			'2' => '2',
			'3' => '3',
			'4' => '4',
			'5' => '5',
			'6' => '6',
			'7' => '7',
			'8' => '7',
			'9' => '7',
			'10' => '8',
			'11' => '9',
			'12' => '4',
			'13' => '10',
			'14' => '11',
			'15' => '12',
			'16' => '13',
			'17' => '0',
		);
	
		return $stateArr[$state];
	}
}
