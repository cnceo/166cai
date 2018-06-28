<?php

if (! defined ( 'BASEPATH' ))
	exit ( 'No direct script access allowed' );
	
/*
 * APP 基础数据接口
 * @date:2017-10-19
 */
class Data extends MY_Controller 
{
	public function __construct() 
	{
		parent::__construct();
		$this->load->model('cache_model','Cache');
	}

	public function index()
	{
		$result = array(
			'status' => '1',
			'msg' => '通讯成功',
			'data' => $this->getRequestHeaders()
		);
		echo json_encode($result);	
	}

	// 首页接口数据
	public function getIndex()
	{
		$info = $this->Cache->getIndex($platform = 'android');
		// 请求头信息
		$headerInfo = $this->getRequestHeaders();
		// $headerInfo['channel'] = 'alpha';
		$channel = $this->recordChannel($headerInfo['channel']);
		// 按渠道处理
		$indexData = array();
		if(!empty($info))
		{
			foreach ($info as $items) 
			{
				$data = $this->handleData($items, $channel);
				if(!empty($data))
				{
					array_push($indexData, $data);
				}	
			}
		}

		// 按渠道判断是否停售，但展示
		if(!empty($indexData))
		{
			foreach ($indexData as $key => $items) 
			{
				if($items['type'] == 'lottery')
				{
					$indexData[$key]['content'][$items['type']] = $this->getLotterys($items['content'][$items['type']]);
				}
			}
		}

		$result = array(
			'status' => '1',
			'msg' => '通讯成功',
			'data' => $indexData
		);
		echo json_encode($result);
	}

	// 
	public function handleData($info, $channel)
	{
		if(!in_array($info['type'], array('notice')))
		{
			$filter = array();
			// 轮播图、彩种
			if(in_array($info['type'], array('banner')))
			{
				foreach ($info['content'][$info['type']] as $key => $items) 
				{
					// 按渠道判断是否展示信息
					if(!empty($items['channels']) && in_array($channel, explode(',', $items['channels'])))
					{
						unset($items['channels']);
						array_push($filter, $items);
					}	
				}
			}
			elseif(in_array($info['type'], array('lottery')))
			{
				foreach ($info['content'][$info['type']] as $key => $items) 
				{
					// 按渠道判断是否展示信息
					if(!empty($items['channels']) && in_array($channel, explode(',', $items['channels'])))
					{
						unset($items['channels']);
						// 子彩种判断
						if(!empty($items['subs']))
						{
							$subs = array();
							foreach ($items['subs'] as $val) 
							{
								// 按渠道判断是否展示信息
								if(!empty($val['channels']) && in_array($channel, explode(',', $val['channels'])))
								{
									unset($val['channels']);
									array_push($subs, $val);
								}	
							}
							$items['subs'] = $subs;
						}
						array_push($filter, $items);
					}	
				}
			}
			else
			{
				$detail = $info['content'][$info['type']];
				if(!empty($detail['channels']) && in_array($channel, explode(',', $detail['channels'])))
				{
					unset($detail['channels']);
					$filter = $detail;
				}
			}
			
			if(!empty($filter))
			{
				$info['content'][$info['type']] = $filter;
			}
			else
			{
				return array();
			}
		}
		return $info;
	}

	// 判断渠道销售
	public function getLotterys($info)
	{
		$headerInfo = $this->getRequestHeaders();

		$channelArr = $this->Cache->getLimitChannel();

		if(!empty($info))
		{
			// 按渠道判断彩种停售
			$isSale = 1;
			if(in_array($this->recordChannel($headerInfo['channel']), $channelArr))
			{
				$isSale = 0;
			}

			// 按版本判断彩种停售
			$saleConfig = array();
			$appConfig = $this->Cache->getAppConfig('android');
			if(!empty($appConfig[$headerInfo['appVersionCode']]['lotteryConfig']))
			{
				$saleConfig = json_decode($appConfig[$headerInfo['appVersionCode']]['lotteryConfig'], true);
			}

			foreach ($info as $key => $items) 
			{
				if(!empty($items['subs']))
				{
					foreach ($items['subs'] as $k => $val) 
					{
						$info[$key]['subs'][$k]['isSale'] = $this->getIsSale($isSale, $val['isSale'], $val['lid'], $saleConfig);
					}
				}
				$info[$key]['isSale'] = $this->getIsSale($isSale, $items['isSale'], $items['lid'], $saleConfig);			
			}

			// 针对v4.0版本快三系列显示问题特殊处理
			if($headerInfo['appVersionCode'] < '40100')
			{
				$lotterys = array();
				$weightArr = array();
				foreach ($info as $key => $items) 
				{
					if(in_array($items['lid'], array('3')) && !empty($items['subs']))
					{
						foreach ($items['subs'] as $data) 
						{
							if($data['lid'] == '53')
							{
								$data['subs'] = array();
								$items = $data;
								break;
							}
							else
							{
								$items = array();
							}	
						}
					}
					if(!empty($items))
					{
						$weightArr[] = $items['weight'];
						array_push($lotterys, $items);
					}	
				}
				array_multisort($weightArr, SORT_DESC, $lotterys);
				$info = $lotterys;
			}
		}
		return $info;
	}

	private function getIsSale($channelSale, $webSale, $lid, $saleConfig = array())
	{
		// 竞足单关
		$lid = ($lid == '4201') ? '42' : $lid;
		return (empty($channelSale) || empty($webSale) || (!empty($saleConfig[$lid]) && $saleConfig[$lid] == '1')) ? '0' : '1';
	}

	// 客户端模块初始化
	public function appInit()
	{
		$result = array(
			'status'	=>	'1',
			'msg' 		=> 	'通讯成功',
			'data' 		=> 	array(
				'isSale'	=>	$this->getAppInit()
			)
		);
		echo json_encode($result);
	}

	private function getAppInit()
	{
		$headerInfo = $this->getRequestHeaders();
		$channelArr = $this->Cache->getLimitChannel();

		$isSale = '1';
		if(in_array($this->recordChannel($headerInfo['channel']), $channelArr))
		{
			$isSale = '0';
		}
		return $isSale;
	}
	
	/**
	 * 客户端记录登录信息接口
	 */
	public function loginRecord()
	{
	    $result = array(
	        'status' => '1',
	        'msg' => '通讯成功'
	    );
	    
	    $data = $this->strCode($this->input->post('data'));
	    $data = json_decode($data, true);
	    $headerInfo = $this->getRequestHeaders();
	    //判断是否有uid或者设备id
	    if(empty($data['uid']) || empty($headerInfo['deviceId']))
	    {
	        die(json_encode($result));
	    }
	    $this->load->model('user_model');
	    $uinfo = $this->user_model->getUserInfo($data['uid']);
	    if(empty($uinfo))
	    {
	        //用户信息错误直接返回
	        die(json_encode($result));
	    }
	    
	    // 更新用户登录信息
	    $this->user_model->saveUser(
	        array(
	            'uid' => $uinfo['uid'],
	            'last_login_time' => date('Y-m-d H:i:s'),
	            'last_login_channel' => $this->recordChannel($headerInfo['channel']),
	        )
	    );
	    
	    //记录登录信息
	    $refe = REFE ? REFE : '';
	    $loginRecord = array(
	        'login_time' => date('Y-m-d H:i:s'),
	        'uid' => $data['uid'],
	        'ip' => UCIP,
	        'area' => $this->tools->convertip(UCIP),
	        'reffer' => $refe,
	        'platform' => $this->config->item('platform'),
	        'model' => $headerInfo['model'] ? $headerInfo['model'] : '',
	        'system' => $headerInfo['OSVersion'] ? $headerInfo['OSVersion'] : '',
	        'version' => $headerInfo['appVersionName'],
                'channel' => $this->recordChannel($headerInfo['channel']),
	    );
	    
	    $this->user_model->loginRecord($loginRecord);
	    
	    //消息入队
	    apiRequest('common_stomp_send', 'login',array('uid' => $uinfo['uid'], 'last_login_time' => $uinfo['last_login_time']));
	    
	    die(json_encode($result));
	}
}