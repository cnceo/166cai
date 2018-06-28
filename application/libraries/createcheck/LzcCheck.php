<?php
/**
 * 老足彩订单创建检查类
 * @author shigx
 *
 */
require_once APPPATH . '/libraries/createcheck/BaseCheck.php';
class LzcCheck extends BaseCheck
{
	//后缀  1:1 正常 
	private $mode = array('1:1');
	//场次
	private $matchs = array('1', '2', '3', '4', '5', '6', '7', '8', '9', '10', '11', '12', '13', '14');
	//玩法  胜 平 负
	private $playOption = array('3', '1', '0');
	public function __construct()
	{
		parent::__construct();
		$this->CI->load->library('libcomm');
	}
	
	/**
	 * 投注检查主方法
	 * @param unknown_type $params
	 * @return unknown|multitype:boolean string
	 */
	public function check($params = array())
	{
		if ($params['orderType'] == 4 && $params['type'] == 1)
		{
			$this->setParams($params, 41);
			$checkMethods = array('checkParams');
		}else {
			$this->setParams($params, $params['orderType']);
			//需要检查的方法
			$checkMethods = array('checkParams', 'checkIsChase', 'checkMaxMoney', 'checkSale', 'checkIssue', 'checkCodes', 'checkOrderLimit');
		}
		
		foreach ($checkMethods as $method)
		{
			$result = $this->$method();
			if($result['status'] == false)
			{
				return $result;
			}
		}
		
		$result = array(
			'status' => true,
			'msg' => '',
		);
		
		return $result;
	}
	
	public function checkCodes()
	{
		$lidMethod = array('11' => 'sfcCheck', '19' => 'rjCheck');
		$codes_arr = explode(';', $this->params['codes']);
		$rebetnum = 0;
		$check = true;
		foreach ($codes_arr as $betcode)
		{
			$codes = explode(':', $betcode);
			$mode = $codes['1'] . ':' . $codes['2'];
			if(empty($codes[0]) || (!in_array($mode, $this->mode)))
			{
				$check = false;
				break;
			}
			$result = $this->$lidMethod[$this->params['lid']]($codes[0]);
			if($result['status'] == false)
			{
				$check = false;
				break;
			}
			$rebetnum += $result['betNum'];
		}
		
		if($check == false)
		{
			return array('status' => false, 'msg' => '投注串校验错误');
		}
		else
		{
			if($rebetnum != $this->params['betTnum'])
			{
				return array('status' => false, 'msg' => '投注串校验错误');
			}
			if($this->params['money'] != ($rebetnum * $this->params['multi'] * 2))
			{
				return array('status' => false, 'msg' => '订单校验错误');
			}
				
			return array('status' => true, 'msg' => '');
		}
	}
	
	/**
	 * 胜负彩串校验
	 * @param unknown_type $ball
	 * @return multitype:boolean |multitype:boolean number
	 */
	private function sfcCheck($ball)
	{
		$balls = explode(',', $ball);
		if(count($balls) != 14)
		{
			return array('status' => false);
		}
		$betTnum = 1;
		foreach ($balls as $key => $code)
		{
			$codes = str_split($code);
			if(($this->isValueRepeat($codes)) || (!$this->checkPlayOption($codes)))
			{
				return array('status' => false);
			}
			$betTnum *= count($codes);
		}
		
		//计算票张数
		$this->ticketCount += 1;
		
		$result = array(
			'status' => true,
			'betNum' => $betTnum
		);
		
		return $result;
	}
	
	/**
	 * 任九串校验
	 * @param unknown_type $ball
	 */
	private function rjCheck($ball)
	{
		$balls = explode(',', $ball);
		if(count($balls) != 14)
		{
			return array('status' => false);
		}
		$selectd = array();
		$mcode = array();
		foreach ($balls as $key => $code)
		{
			if($code == '#')
			{
				continue;
			}
			$codes = str_split($code);
			if(($this->isValueRepeat($codes)) || (!$this->checkPlayOption($codes)))
			{
				return array('status' => false);
			}
			$mcode[] = $key + 1;
			$selectd[$key + 1] = $codes;
		}
		
		if(count($mcode) < 9)
		{
			return array('status' => false);
		}
		
		//算注数
		$combineList = $this->CI->libcomm->combineList($mcode, 9);	//场次组合
		$betTnum = 0;
		foreach ($combineList as $value)
		{
			$num = 1;
			foreach ($value as $val)
			{
				$num *= count($selectd[$val]);
			}
			$betTnum += $num;
			//计算票张数
			$this->ticketCount += 1;
		}
	
		$result = array(
			'status' => true,
			'betNum' => $betTnum
		);
		return $result;
	}
	
	/**
	 * 检查选择的玩法   true 球正确  false 选球错误
	 * @param unknown_type $balls
	 * @return boolean
	 */
	private function checkPlayOption($balls = array())
	{
		foreach ($balls as $ball)
		{
			if(!in_array($ball, $this->playOption, true))
			{
				return false;
			}
		}
		
		return true;
	}
	
	/**
	 * 检查金额最大值
	 */
	public function checkMaxMoney()
	{
		if($this->params['money'] > 200000)
		{
			$result = array(
				'status' => false,
				'msg' => "订单金额需小于20万，请修改订单后重新投注",
			);
		}
		else
		{
			$result = array(
				'status' => true,
				'msg' => '',
			);
		}
	
		return $result;
	}
	
	/**
	 * 检查期次是否正确
	 */
	public function checkIssue()
	{
		$REDIS = $this->CI->config->item('REDIS');
		$cache = json_decode($this->CI->cache->get($REDIS['SFC_ISSUE_NEW']), true);
		$issues = array();
		$currIssueIds = array();
		foreach ($cache as $value)
		{
			if ($value['sale_time'] <= time() * 1000 && $value['seFsendtime'] >= time() * 1000)
            {
                array_push($currIssueIds, $value['seExpect']);
            }
			if ($value['sale_time'] / 1000 > time())
			{
				continue;
			}
			$issue = '20' . $value['mid'];
			$issues[$issue]['issue'] = $issue;
			$issues[$issue]['seFsendtime'] = $value['seFsendtime'];
			$issues[$issue]['seEndtime'] = $value['seEndtime'];
		}

		if (empty($currIssueIds))
        {
            foreach ($cache as $issue)
            {
                if ($issue['seFsendtime'] >= time() * 1000)
                {
                    array_push($currIssueIds, $issue['seExpect']);
                    break;
                }
            }
        }

        $currIssue = array();
        if (!empty($currIssueIds)) 
        {
        	$issueId = min($currIssueIds);
        	if($issueId)
        	{
        		 foreach ($cache as $key => $issue)
		        {
		            if ($issue['seExpect'] == $issueId)
		            {
		                $currIssue = $issue;
		            }
		        }
        	}
        }

        if(!empty($currIssue) && ($currIssue['sale_time'] > time() * 1000))
        {
        	$result = array(
				'status' => false,
				'msg' => '本期次尚未开售，请于' . date('Y-m-d H:i:s', $currIssue['sale_time']/1000) . '后投注！',
			);
			return $result;
        }
		
		if((in_array($this->params['orderType'], array(0, 1)) && "20{$currIssue['mid']}" != $this->params['issue'])
				|| ($this->params['orderType'] == 4
						&& ($issues[$this->params['issue']]['seFsendtime']/1000-$this->_lotteryConfig[$this->params['lid']]['united_ahead']*60 != strtotime($this->params['endTime'])
							|| $issues[$this->params['issue']]['seEndtime']/1000 != strtotime($this->params['openEndtime'])))
        	)
		{
			$result = array(
				'status' => false,
				'msg' => '投注期次错误',
			);
			return $result;
		}
		if(strtotime($this->params['endTime']) < time())
		{
			$result = array(
				'status' => false,
				'msg' => '期次已过投注时间',
			);
			return $result;
		}
	
		$result = array(
			'status' => true,
			'msg' => '期次正确',
		);
		return $result;
	}
}
