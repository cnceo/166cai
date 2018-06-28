<?php
/**
 * 竞彩篮球订单创建检查类
 * @author shigx
 *
 */
require_once dirname(__FILE__) . '/BaseCheck.php';
class GjcCheck extends BaseCheck
{
    
    private $pattern = array(
        44 => '/^GJ\|18001=(.*?)\|(\d+)$/', 
        45 => '/^GYJ\|18001=(.*?)\|(\d+)$/'
    );
    
    private $arrange = array(
        44 => array('min' => 1, 'max' => 32),
        45 => array('min' => 1, 'max' => 50),
    );
    
	//混合玩法串关定义
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
		
		$this->setParams($params, 0);
		//需要检查的方法
		$checkMethods = array('checkParams', 'checkEndTime', 'checkMaxMoney', 'checkSale', 'checkCodes', 'checkOrderLimit');

		foreach ($checkMethods as $method)
		{
			$result = $this->$method();
			if($result['status'] == false) return $result;
		}
		
		return array('status' => true, 'msg' => '');
	}
	
	public function checkCodes()
	{
		preg_match($this->pattern[$this->params['lid']], $this->params['codes'], $matches);
		$matchs = explode('/', $matches[1]);
		$mids = array();
		foreach ($matchs as $match) {
		    if (!preg_match('/^(\d{2})\((.+)\)$/', $match, $matchstr)) return array('status' => false, 'msg' => '投注串校验错误');
		    $mid = (int)$matchstr[1];
		    if ($mid < $this->arrange[$this->params['lid']]['min'] || $mid > $this->arrange[$this->params['lid']]['max']) 
		        return array('status' => false, 'msg' => '投注串校验错误');
		    array_push($mids, $mid);
		}
		if ($matches[2] != count($matchs)) return array('status' => false, 'msg' => '投注串校验错误');
		$this->CI->load->model('gjc_model');
		if ($this->CI->gjc_model->checkStatus($mids, $this->params['lid'])) return array('status' => false, 'msg' => '请选择在售的场次');
	    if(count($matchs) != $this->params['betTnum']) return array('status' => false, 'msg' => '投注串校验错误');
	    if($this->params['money'] != (count($matchs) * $this->params['multi'] * 2)) return array('status' => false, 'msg' => '订单校验错误');
	
	    return array('status' => true, 'msg' => '');
		
	}
	
	/**
	 * 订单总额校验
	 * @see BaseCheck::checkMaxMoney()
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
	 * 校验投注截止时间
	 */
	public function checkEndTime()
	{
		if ($this->params['endTime'] != '2018-07-15 23:00:00') return array('status' => false, 'code' => '600', 'msg' => '截止时间有误！');
		if (strtotime($this->params['endTime']) < time()) return array('status' => false, 'code' => '600', 'msg' => '已过截止时间');
	
		return array('status' => true, 'msg' => '');
	}
}
