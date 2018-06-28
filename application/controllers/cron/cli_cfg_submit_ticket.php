<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Cli_Cfg_Submit_Ticket extends MY_Controller 
{
	private $TEngine = array();
	
	public function __construct()
	{
		parent::__construct();
		$this->load->library('tools');
		$this->load->model('ticket_model');
		$this->load->config('order');
		$this->sellers = $this->ticket_model->getSeller();
		$this->sellers = array(
			0 => array(
				'name' => 'funiuniu',
				'weight' => '5',
			)
		);
		$this->multi_process = $this->config->item('multi_process');
		if(!empty($this->sellers))
		{
			foreach ($this->sellers as $seller)
			{
				$this->load->library("ticket_{$seller['name']}");
			}
		}
	}
	
	public function index()
	{
		$cname = strtolower(__CLASS__);
		$multi = $this->multi_process[$cname];
		$stop = $this->ticket_model->ctrlRun(null, null, true, array('model' => 'ticket_model', 'func' => 'ctrlRun', 'params' => $cname));
		$threads = array();
		$pnum = 0;
		while(!$stop)
		{
			$methods = $this->getCrontabList(2);
			$plimit = count($methods);
			foreach ($methods as $methodstr)
			{
				$methodstrs = explode('@', $methodstr);
				$method = $methodstrs[0];
				$params = $methodstrs[1];
				if($multi)
				{//开启多进程
					$pnum ++;
					$pid = pcntl_fork();
					if($pid == -1)
					{
						//进程创建失败 跳出循环
						$pnum --;
						continue;
					}
					else if($pid)
					{
						if(!in_array($methodstr, $threads))
						{
							$threads[$pid] = $methodstr;
						}
						if($pnum >= $plimit)
						{
							$wpid = pcntl_wait($status);
							if(!empty($wpid) || $wpid == -1)
							{
								unset($threads[$wpid]);
								$pnum --;
							}
						}
					}
					else 
					{
						if(!in_array($methodstr, $threads))
		    			{
		    				if(empty($params))
		    				{
								$croname = "cron/{$this->con} $method";
		    				}
							else 
							{
								$croname = "cron/{$this->con} $method/$params";
							}
							system("{$this->php_path} {$this->cmd_path} $croname", $status);
							if($status)
							{
								log_message('LOG', "$croname:$status", 'procerr');
							}
		    			}
		    			die(0);
					}
				}
				else 
				{
					if(method_exists($this, $method))
	    			{
	    				if(empty($params))
	    				{
							$this->$method();
	    				}
						else 
						{
							$this->$method($params);
						}
	    			}
				}
			}
			if($multi) 
			{
				$this->ticket_model->threadWait($threads, 0);
			}
			$stop = $this->ticket_model->ctrlRun(null, null, true, array('model' => 'ticket_model', 'func' => 'ctrlRun', 'params' => $cname));
			$pnum = count($threads);
			//break;
		}
	}
	
	private function params($param)
	{
		$datas = array('lid' => 0, 'seller' => '');
		if(!empty($param))
		{
			$parastr = explode('-', $param);
			$datas['lid'] = $parastr[0];
			$datas['seller'] = $parastr[1];
		} 
		return $datas;
	}
	/*
	 *功能：出票预约
	 *日期：2016-03-11
	 *作者：huxm
	 * */
	public function betting($param = '')
	{
		// 福牛牛 - 竞彩足球
		$param = '42-funiuniu';
		$pdata = $this->params($param);
		foreach ($this->sellers as $seller)
		{
			if(!empty($pdata['seller']) && $pdata['seller'] != $seller['name'])
			{
				continue;
			}
			$torders = $this->ticket_model->getTicketOrders($seller['name'], $pdata['lid']);
			if(!empty($torders))
			{
				$lseller = "ticket_{$seller['name']}";
				$this->$lseller->med_betting($torders);
			}
		}
		//补漏提票失败的订单
		$this->reBetting($param);
	}
	
	/*
	 *功能：出票预约重试
	 *日期：2016-03-11
	 *作者：huxm
	 * */
	private function reBetting($param = '')
	{
		$pdata = $this->params($param);
		foreach ($this->sellers as $seller)
		{
			if(!empty($pdata['seller']) && $pdata['seller'] != $seller['name'])
			{
				continue;
			}
			$messageids = $this->ticket_model->getOrderIds($seller['name'], $pdata['lid']);
			if(!empty($messageids))
			{
				foreach ($messageids as $messageid)
				{
					$torders = $this->ticket_model->getTicketOrdersByMsgId($messageid, $pdata['lid']);
					if(!empty($torders))
					{
						$lseller = "ticket_{$seller['name']}";
						$this->$lseller->med_betting($torders);
					}
				}
			}
		}
	}
	//更新赔率
	private function peiLv()
	{
		$this->ticket_qihui->med_peilv();
	}
	//出票结果(竞彩赔率)
	public function ticketResult($param = '')
	{
		// 福牛牛 - 竞彩足球
		$param = '42-funiuniu';
		$pdata = $this->params($param);
		foreach ($this->sellers as $seller)
		{
			if(!empty($pdata['seller']) && $pdata['seller'] != $seller['name'])
			{
				continue;
			}
			$lseller = "ticket_{$seller['name']}";
			$this->$lseller->med_ticketResult(false, $pdata['lid']);
		}
	}
	//设置过期失败的订单
	public function ticketConcel($param = '')
	{
		// 福牛牛 - 竞彩足球
		$param = '42-funiuniu';
		$pdata = $this->params($param);
		foreach ($this->sellers as $seller)
		{
			if(!empty($pdata['seller']) && $pdata['seller'] != $seller['name'])
			{
				continue;
			}
			$lseller = "ticket_{$seller['name']}";
			$this->$lseller->med_ticketResult(true, $pdata['lid']);
		}
		//未提票过期订单置失败操作
		$this->ticket_model->ticketConcel($pdata['lid']);
	}
	//中奖明细，对账用
	public function ticketBonus($param = '')
	{
		return false;
		$pdata = $this->params($param);
		foreach ($this->sellers as $seller)
		{
			if(!empty($pdata['seller']) && $pdata['seller'] != $seller['name'])
			{
				continue;
			}
			$lseller = "ticket_{$seller['name']}";
			$this->$lseller->med_ticketBonus($pdata['lid']);
		}
	}
	//拉取文件对账
	private function FBonusDetail()
	{
		return false;
		$this->ticket_qihui->file_bonusDetail();
	}
	
	//拉取十一选五开奖结果
	public function syxwResult()
	{
		return false;
		$issues = $this->ticket_model->getSyxwIssue();
		if(!empty($issues))
		{
			foreach ($issues as $issue)
			{
				$this->ticket_qihui->med_syxwResult($issue);
			}
		}
	}
	
	//拉取江西十一选五开奖结果
	public function kjResult($params = '')
	{
		return false;
		$myparams = explode('-', $params);
		$lids = explode('_', $myparams[1]);
		$lidmap = $this->ticket_model->orderConfig('lidmap');

		if(!empty($lids))
		{
			foreach ($lids as $lid)
			{
			    if(empty($lidmap[$lid])) continue;
				$issues = $this->ticket_model->getSyxwIssue($lidmap[$lid]);
				if(!empty($issues))
				{
					foreach ($issues as $issue)
					{
						$lseller = "ticket_{$myparams[0]}";
						$this->$lseller->med_kjResult($issue, $lid);
					}
				}
			}
		}
	}
	
 	private function getCrontabList($ctype)
    {
    	$this->load->driver('cache', array('adapter' => 'redis'));
    	$REDIS = $this->config->item('REDIS');
    	$lists = json_decode($this->cache->get($REDIS['CRONTAB_CONFIG']), true);
    	return $lists[$ctype];
    }
}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */