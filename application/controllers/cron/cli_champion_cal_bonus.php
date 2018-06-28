<?php
/**
 * 冠军彩过关算奖执行脚本
 */
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Cli_Champion_Cal_Bonus extends MY_Controller
{   
	public function __construct()
	{
		parent::__construct();
		$this->load->model('bonus_model');
		$this->order_status = $this->bonus_model->orderConfig('orders');
	}
	
	/**
	 * 冠军彩过关操作
	 * @param string $issue	期次	例：16001
	 * @param string $mid	夺冠场次  01~24 注：1~9必须有前导0 即01~09
	 */
    public function calculate44($issue, $mid)
    {
    	if(strlen($mid) != 2 || ($mid <= 0 || $mid > 24))
    	{
    		die('夺冠场次输入错误');
    	}
    	$this->bonus_model->trans_start();
    	$orders = $this->bonus_model->championOrders($issue, 44);
    	$bdata = array();
		$flag = $orders['flag'];
		while(!empty($orders['data']))
		{
			foreach ($orders['data'] as $in => $order)
			{
				$order['odds'] = $this->cal_odds($order, $mid);
				$order['hitnum'] = 0;
				$order['status'] = $this->order_status['relation_ggsucc'];
				$bdata[] = $order;
			}
			$re = $this->bonus_model->setJJcResult($bdata);
			if(!$re)
			{
				echo '执行失败';
				$this->bonus_model->trans_rollback();
				return false;
			}
			$bdata = array();	
			$orders = $this->bonus_model->championOrders($issue, 44);
		}
		
		$this->bonus_model->trans_complete();
		echo '执行成功';
    }
    
    /**
     * 冠军彩过关操作
     * @param string $issue	期次	例：16001
     * @param string $mid	夺冠场次  01~50 注：1~9必须有前导0 即01~09
     */
    public function calculate45($issue, $mid)
    {
    	if(strlen($mid) != 2 || ($mid <= 0 || $mid > 50))
    	{
    		die('夺冠场次输入错误');
    	}
    	 
    	$this->bonus_model->trans_start();
    	$orders = $this->bonus_model->championOrders($issue, 45);
    	$bdata = array();
    	$flag = $orders['flag'];
    	while(!empty($orders['data']))
    	{
    		foreach ($orders['data'] as $in => $order)
    		{
    			$order['odds'] = $this->cal_odds($order, $mid);
    			$order['hitnum'] = 0;
    			$order['status'] = $this->order_status['relation_ggsucc'];
    			$bdata[] = $order;
    		}
    		$re = $this->bonus_model->setJJcResult($bdata);
    		if(!$re)
    		{
    			echo '执行失败';
    			$this->bonus_model->trans_rollback();
    			return false;
    		}
    		$bdata = array();
    		$orders = $this->bonus_model->championOrders($issue, 45);
    	}
    
    	$this->bonus_model->trans_complete();
    	echo '执行成功';
    }
    
    /**
     * 冠亚军算奖操作
     */
    public function calBonus()
    {
    	$this->bonus_model->calBonusChampionOrders();
    }
    
    /**
     * 查询中奖赔率并返回
     * @param unknown_type $order
     * @param unknown_type $mid
     */
    private function cal_odds($order, $mid)
    {
    	$odds = 0;
    	$pscores = explode('/', $order['pscores']);
    	$details = json_decode($order['pdetail'], true);
    	foreach ($pscores as $pscore)
    	{
    		preg_match('/^(\d+)\(.*?\)$/is', $pscore, $matches);
    		if($matches[1] === $mid)
    		{
    			$odds = $details[$mid];
    			break;
    		}
    	}
    	
    	return $odds;
    }
}
/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */