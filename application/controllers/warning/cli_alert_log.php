<?php
/**
 * 新的报警逻辑
 */
class Cli_Alert_Log extends MY_Controller
{
	public function __construct() {
		parent::__construct();
		$this->load->model('warning_model', 'wanning');
		$this->load->model('model_order_check', 'orderCheck');
	}
	
	public function index()
	{
		// 开奖详情未抓到
		$this->checkAwarding();
		// 追号报警
		$this->checkChaseWarning();
		// 高频派奖报警
		$this->wanning->checkSendPrize();
		//大奖待审核报警
		$this->wanning->getOrderChecklist();
		//竞技彩15分钟未审核报警
		$this->wanning->jjcAduitWarn();
		//大额充值订单报警
		$this->orderCheck->checkPay();
		//大额购彩订单报警
		$this->orderCheck->checkOrders();
		//慢频彩20分钟未审核报警
		$this->wanning->numAduitWarn();
		//未拉取票商奖金报警
		$this->wanning->warningTicketBonus();
		//竞彩出票格式异常报警
		$this->wanning->jjcRelationCheck();
	}
	
	/*
	 * 开奖详情到设定时间还未抓取到
	* @date:2016-05-22
	*/
	public function checkAwarding()
	{
		$this->wanning->checkAwarding();
	}
	
	/*
	 * 追号报警
	* @date:2016-05-22
	*/
	public function checkChaseWarning()
	{
		$this->load->model('chase_bet_model');
		// 彩种配置信息
		$lotteryConfig = $this->chase_bet_model->getLotteryConfig();
	
		foreach ($lotteryConfig as $lid => $config)
		{
			$this->wanning->checkChaseWarning($lid, '');
		}
	}
	
	/**
	 * 提现10分钟未处理报警  10分钟跑一次
	 */
	public function checkWithdraw()
	{
		$this->wanning->checkWithdraw();
	}
	/**
	 * [jjzqNotGetBonus 竞彩足球篮球未拉取票商奖金报警]
	 * @author LiKangJian 2017-07-19
	 * @return [type] [description]
	 */
	public function jjzqNotGetBonus()
	{
		$this->wanning->jjzqNotGetBonus();
	}
}
