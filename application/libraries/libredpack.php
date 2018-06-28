<?php
class Libredpack {
	
	private $CI;
	public function __construct()
	{
		$this->CI = &get_instance();
		$this->position = $this->CI->config->item('POSITION');
	}
	
	public function hongbao166($type, $data)
	{	
		$this->CI->load->model('redpack/model_hongbao166');
		switch ($type) 
		{
			case 'register':
		        $aid = 16;
		        $oaids = array(1, 8);
				//红包操作
				if ($this->CI->model_hongbao166->hasAttend($oaids, $data['phone']) && $data['uid']) {
					return $this->CI->model_hongbao166->send($data['uid'], 1);
				}
				elseif($this->CI->model_hongbao166->hasAttend($aid, $data['phone']))
				{
					return $this->CI->model_hongbao166->send($data['uid'], 2);
				}
				else
				{
					$res = $this->CI->model_hongbao166->attend($aid, $data['phone'], $data['platformId'], $data['channel']);
					if ($res[0] === true)
					{
						$this->CI->load->model('user_model');
                        //记录拉新活动
                        if($data['fromUid'])
                        {
                            if($data['activityType'] == 'xnhk')
                            {
                                //$this->CI->user_model->sendSms('', array(), 'activity_xnhk', $data['phone'], $this->get_client_ip(), $this->position['166_hongbao']);
                                $this->CI->load->model('activity_xn_model');
                                $this->CI->activity_xn_model->attend($data['fromUid'], $data['phone'], $data['platformId'], $data['channel'], 13);
                            }
                            else
                            {
                                //$this->CI->user_model->sendSms('', array(), 'laxin', $data['phone'], $this->get_client_ip(), $this->position['166_hongbao']);
                                $this->CI->load->model('activity_lx_model');
                                $this->CI->activity_lx_model->attend($data['fromUid'], $data['phone'], $data['platformId'], $data['channel'], 9);
                            }
                            
                        }
					}
					return $res;
				}
				break;
			case 'bindcard':
			    if ($hasAttend = $this->CI->model_hongbao166->hasAttend(1, $data['phone'])) {
			        $aid = 1;
			    }elseif ($hasAttend = $this->CI->model_hongbao166->hasAttend(8, $data['phone'])) {
			        $aid = 8;
			    } elseif ($hasAttend = $this->CI->model_hongbao166->hasAttend(16, $data['phone'])) {
			        $aid = 16;
			    }
				if($hasAttend)
				{
				    if ($aid == 1) {
				        $this->CI->load->model('redpack/model_hongbao188');
				        $checkResult = $this->CI->model_hongbao188->checkBound($data['uid'], $data['id_card']);
				        if($checkResult[0] == TRUE) $this->CI->model_hongbao188->activatePack(1, $data['uid']);
				        else $this->CI->model_hongbao188->deleteOwnPack(1, $data['uid']);
				    }else {
				        $checkResult = $this->CI->model_hongbao166->checkBound($data['uid'], $data['id_card']);
				        if($checkResult[0] == TRUE) $this->CI->model_hongbao166->activatePack($aid, $data['uid']);
				        else $this->CI->model_hongbao166->deleteOwnPack($aid, $data['uid']);
				    }
				}
				break;
			case 'hasAttend':
			    $aid = 0;
			    if ($this->CI->model_hongbao166->hasAttend(1, $data['phone'])) {
			        $aid = 1;
			    }elseif ($this->CI->model_hongbao166->hasAttend(8, $data['phone'])) {
			        $aid = 8;
			    } elseif ($this->CI->model_hongbao166->hasAttend(16, $data['phone'])) {
			        $aid = 16;
			    }
				return $aid;
				break;
			case 'checkBound':
				return $this->CI->model_hongbao166->checkBound($data['uid'], $data['id_card']);
				break;
			case 'activatePack':
				return $this->CI->model_hongbao166->activatePack($data['activityId'], $data['uid']);
				break;
			case 'deleteOwnPack':
				return $this->CI->model_hongbao166->deleteOwnPack($data['activityId'], $data['uid']);
				break;
			case 'send':
				return $this->CI->model_hongbao166->send($data['uid'], $data['userType']);
				break;
			case 'lx':
				//红包操作
				$this->CI->load->model('redpack/model_hongbaolx166');
				$res = $this->CI->model_hongbaolx166->attend($data['phone'], $data['platformId'], $data['channel'], $data['ip'], $data['reffer']);
				if ($res[0] === true) {
					$this->CI->load->model('user_model');
					$this->CI->user_model->sendSms('', array(), '166_hongbao', $data['phone'], $this->get_client_ip(), $this->position['166_hongbao']);
				}
				return $res;
				break;
			case 'distribute':
			    if ($hasAttend = $this->CI->model_hongbao166->hasAttend(8, $data['phone'])) {
			        $aid = 8;
			    } elseif ($hasAttend = $this->CI->model_hongbao166->hasAttend(16, $data['phone'])) {
			        $aid = 16;
			    }
				$this->CI->model_hongbao166->distribute($aid, $data['phone'], $data['platformId'], $data['channel']);
				break;
		}
	}
	
	public function hongbao188($type, $data)
	{
		$this->CI->load->model('redpack/model_hongbao188');
		switch ($type)
		{
			case 'register':
				//红包操作
				if($this->CI->model_hongbao188->hasAttend(1, $data['phone']))
				{
					$this->CI->model_hongbao188->send($data['uid'], 2);
				}
				else
				{
					$res = $this->CI->model_hongbao188->attend(1, $data['phone'], $data['platformId'], $data['channel']);
					if ($res[0] === true)
					{
						$this->CI->load->model('user_model');
					}
				}
				break;
			case 'bindcard':
				$hasAttend = $this->CI->model_hongbao188->hasAttend(1, $data['phone']);
				if($hasAttend)
				{
					$checkResult = $this->CI->model_hongbao188->checkBound($data['uid'], $data['id_card']);
					if($checkResult[0] == TRUE)
					{
						// 绑定激活
						$this->CI->model_hongbao188->activatePack(1, $data['uid']);
					}
					else
					{
						$this->CI->model_hongbao188->deleteOwnPack(1, $data['uid']);
					}
				}
				break;
			case 'hasAttend':
				return $this->CI->model_hongbao188->hasAttend(1, $data['phone']);
				break;
			case 'checkBound':
				return $this->CI->model_hongbao188->checkBound($data['uid'], $data['id_card']);
				break;
			case 'activatePack':
				return $this->CI->model_hongbao188->activatePack(1, $data['uid']);
				break;
			case 'deleteOwnPack':
				return $this->CI->model_hongbao188->deleteOwnPack(1, $data['uid']);
				break;
			case 'send':
				return $this->CI->model_hongbao188->send($data['uid'], $data['userType']);
				break;
		}
	}
	
	public function hongbaoworldcup($type, $data) {
	    $this->CI->load->model('redpack/model_hongbaoworldcup');
	    switch ($type) {
	        case 'attend':
	            return $this->CI->model_hongbaoworldcup->attend(14, $data['phone'], $data['platformId'], $data['channel']);
	            break;
                case 'sendHongbao':
	            return $this->CI->model_hongbaoworldcup->sendHongbao(15, $data);
	            break;
            case 'bindcard':
                $hasAttend = $this->CI->model_hongbaoworldcup->hasAttend(14, $data['phone']);
                if($hasAttend) $this->CI->model_hongbaoworldcup->activatePack(14, $data['uid']);
                $this->CI->model_hongbaoworldcup->activatePack(15, $data['uid']);
                break;
	        default:
	            break;
	    }
	}
	
	private function get_client_ip()
	{
		//代理IP白名单
		$allowProxys = array(
				'42.62.31.40',
				'172.16.0.40',
		);
		$onlineip = $_SERVER['REMOTE_ADDR'];
		if (in_array($onlineip, $allowProxys))
		{
			$ips = $_SERVER['HTTP_X_FORWARDED_FOR'];
			if ($ips)
			{
				$ips = explode(",", $ips);
				$curIP = array_pop($ips);
				$onlineip = trim($curIP);
			}
		}
		if (filter_var($onlineip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4))
		{
			return $onlineip;
		}
		else
		{
			return '0.0.0.0';
		}
	}
}