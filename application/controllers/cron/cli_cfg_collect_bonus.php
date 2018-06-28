<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

/*
 * 统计订单销售金额
 * @date:2015-06-03
 */
class Cli_Cfg_Collect_Bonus extends MY_Controller 
{
	private $lidmap = array(
		11 => 'sfc',
		19 => 'rj',
		33 => 'pls',
		35 => 'plw',
		42 => 'jczq',
		43 => 'jclq',
		51 => 'ssq',
		52 => 'fcsd',
		10022 => 'qxc',
		21406 => 'syxw',
		21407 => 'jxsyxw',
		23528 => 'qlc',
		23529 => 'dlt',
		53 => 'ks',
		21408 => 'hbsyxw',
		54 => 'klpk',
		55 => 'cqssc',
	    21421 => 'gdsyxw',
	);
	
	public function __construct()
	{
		parent::__construct();
		$this->load->model('backaward_model');
		$this->lidmap = $this->backaward_model->orderConfig('lidmap');
		$this->load->library('libcomm');
		$this->multi_process = $this->config->item('multi_process');
		$this->order_status = $this->backaward_model->orderConfig('orders');
		$this->load->model('collect_model');	
	}
	
	public function index()
	{
		// $this->lidmap = array(
		// 	11 => 'sfc',
		// 	19 => 'rj',
		// 	33 => 'pls',
		// 	35 => 'plw',
		// 	42 => 'jczq',
		// 	43 => 'jclq',
		// 	51 => 'ssq',
		// 	52 => 'fcsd',
		// 	10022 => 'qxc',
		// 	21406 => 'syxw',
		// 	23528 => 'qlc',
		// 	23529 => 'dlt'
		// );
		// 按彩种
		foreach ($this->lidmap as $lid => $lname)
		{
			// 筛选未统计销售，status = 60 的期次
			$method = "collect_$lname";
			if(method_exists($this, $method))
			{
				$this->$method($lid);
			}
		}
	}

	// 胜负彩 任选九
	public function collect_sfc($lid)
	{
		$tname = 'rsfc';
		$lists = $this->collect_model->getCfgTczq($tname, $this->order_status['paiqi_jjsucc'], $lid);
		if(!empty($lists))
		{
			// 15146
			foreach ($lists as $pIssue) 
			{
				// split表期号格式处理
				$sIssue = $this->libcomm->format_issue($pIssue, 1, 2);

				$this->getDistribution($lid, $tname, $pIssue, $sIssue);
				// 更新 paiqi 表状态 synflag
				$this->collect_model->updatePaiqi($tname, $pIssue, $lid);
			}
		}
	}

	// 任选九
	public function collect_rj($lid)
	{
		$tname = 'rsfc';
		$lists = $this->collect_model->getCfgTczq($tname, $this->order_status['paiqi_jjsucc'], $lid);	
		if(!empty($lists))
		{
			// 15146
			foreach ($lists as $pIssue) 
			{
				// split表期号格式处理
				$sIssue = $this->libcomm->format_issue($pIssue, 1, 2);

				$this->getDistribution($lid, $tname, $pIssue, $sIssue);
				// 更新 paiqi 表状态 synflag
				$this->collect_model->updatePaiqi($tname, $pIssue, $lid);
			}
		}
	}

	// 排列三
	public function collect_pls($lid)
	{
		$tname = 'pl3';
		$lists = $this->collect_model->getCfgPaiqi($tname, $this->order_status['paiqi_jjsucc']);
		if(!empty($lists))
		{
			// 15146
			foreach ($lists as $pIssue) 
			{
				// split表期号格式处理
				$sIssue = $this->libcomm->format_issue($pIssue, 1, 2);
				$this->getDistribution($lid, $tname, $pIssue, $sIssue);
				// 更新 paiqi 表状态 synflag
				$this->collect_model->updatePaiqi($tname, $pIssue, $lid);
			}
		}
	}

	// 排列五
	public function collect_plw($lid)
	{
		$tname = 'pl5';
		$lists = $this->collect_model->getCfgPaiqi($tname, $this->order_status['paiqi_jjsucc']);
		if(!empty($lists))
		{
			// 15146
			foreach ($lists as $pIssue) 
			{
				// split表期号格式处理
				$sIssue = $this->libcomm->format_issue($pIssue, 1, 2);
				$this->getDistribution($lid, $tname, $pIssue, $sIssue);
				// 更新 paiqi 表状态 synflag
				$this->collect_model->updatePaiqi($tname, $pIssue, $lid);
			}
		}
	}

	// 竞彩足球
	// public function collect_jczq($lid)
	// {
	// 	$tname = 'jczq';
	// 	$lists = $this->collect_model->getCfgPaiqi($tname, $this->order_status['paiqi_jjsucc']);
	// 	if(!empty($lists))
	// 	{
	// 		// 15146
	// 		foreach ($lists as $pIssue) 
	// 		{
	// 			// split表期号格式处理
	// 			$sIssue = $pIssue;
	// 			$this->getDistribution($lid, $tname, $pIssue, $sIssue);
	// 			// 更新 paiqi 表状态 synflag
	// 			$this->collect_model->updatePaiqi($tname, $pIssue);
	// 		}
	// 	}
	// }

	// 双色球
	public function collect_ssq($lid)
	{
		$tname = 'ssq';
		$lists = $this->collect_model->getCfgPaiqi($tname, $this->order_status['paiqi_jjsucc']);
		if(!empty($lists))
		{
			// 15146
			foreach ($lists as $pIssue) 
			{
				// split表期号格式处理
				$sIssue = $pIssue;
				$this->getDistribution($lid, $tname, $pIssue, $sIssue);
				// 更新 paiqi 表状态 synflag
				$this->collect_model->updatePaiqi($tname, $pIssue, $lid);
			}
		}
	}

	// 福彩3D
	public function collect_fcsd($lid)
	{
		$tname = 'fc3d';
		$lists = $this->collect_model->getCfgPaiqi($tname, $this->order_status['paiqi_jjsucc']);
		if(!empty($lists))
		{
			// 15146
			foreach ($lists as $pIssue) 
			{
				// split表期号格式处理
				$sIssue = $pIssue;
				$this->getDistribution($lid, $tname, $pIssue, $sIssue);
				// 更新 paiqi 表状态 synflag
				$this->collect_model->updatePaiqi($tname, $pIssue, $lid);
			}
		}
	}
	
	// 七星彩
	public function collect_qxc($lid)
	{
		$tname = 'qxc';
		$lists = $this->collect_model->getCfgPaiqi($tname, $this->order_status['paiqi_jjsucc']);
		if(!empty($lists))
		{
			// 15146
			foreach ($lists as $pIssue) 
			{
				// split表期号格式处理
				$sIssue = $this->libcomm->format_issue($pIssue, 1, 2);
				$this->getDistribution($lid, $tname, $pIssue, $sIssue);
				// 更新 paiqi 表状态 synflag
				$this->collect_model->updatePaiqi($tname, $pIssue, $lid);
			}
		}
	}

	// 十一选五
	public function collect_syxw($lid)
	{
		$tname = 'syxw';
		$lists = $this->collect_model->getCfgPaiqi($tname, $this->order_status['paiqi_jjsucc']);
		if(!empty($lists))
		{
			// 15146
			foreach ($lists as $pIssue) 
			{
				// split表期号格式处理
				$sIssue = $pIssue;
				$this->getDistribution($lid, $tname, $pIssue, $sIssue);
				// 更新 paiqi 表状态 synflag
				$this->collect_model->updatePaiqi($tname, $pIssue, $lid);
			}
		}
	}
	
	// 江西十一选五
	public function collect_jxsyxw($lid)
	{
		$tname = 'jxsyxw';
		$lists = $this->collect_model->getCfgPaiqi($tname, $this->order_status['paiqi_jjsucc']);
		if(!empty($lists))
		{
			// 15146
			foreach ($lists as $pIssue)
			{
				// split表期号格式处理
				$sIssue = $pIssue;
				$this->getDistribution($lid, $tname, $pIssue, $sIssue);
				// 更新 paiqi 表状态 synflag
				$this->collect_model->updatePaiqi($tname, $pIssue, $lid);
			}
		}
	}
	
	// 湖北十一选五
	public function collect_hbsyxw($lid)
	{
		$tname = 'hbsyxw';
		$lists = $this->collect_model->getCfgPaiqi($tname, $this->order_status['paiqi_jjsucc']);
		if(!empty($lists))
		{
			// 15146
			foreach ($lists as $pIssue)
			{
				// split表期号格式处理
				$sIssue = $pIssue;
				$this->getDistribution($lid, $tname, $pIssue, $sIssue);
				// 更新 paiqi 表状态 synflag
				$this->collect_model->updatePaiqi($tname, $pIssue, $lid);
			}
		}
	}
	
	// 湖北十一选五
	public function collect_gdsyxw($lid)
	{
	    $tname = 'gdsyxw';
	    $lists = $this->collect_model->getCfgPaiqi($tname, $this->order_status['paiqi_jjsucc']);
	    if(!empty($lists))
	    {
	        // 15146
	        foreach ($lists as $pIssue)
	        {
	            // split表期号格式处理
	            $sIssue = $pIssue;
	            $this->getDistribution($lid, $tname, $pIssue, $sIssue);
	            // 更新 paiqi 表状态 synflag
	            $this->collect_model->updatePaiqi($tname, $pIssue, $lid);
	        }
	    }
	}
	
	// 上海快三
	public function collect_ks($lid)
	{
		$tname = 'ks';
		$lists = $this->collect_model->getCfgPaiqi($tname, $this->order_status['paiqi_jjsucc']);
		if(!empty($lists))
		{
			// 15146
			foreach ($lists as $pIssue)
			{
				// split表期号格式处理
				$sIssue = $pIssue;
				$this->getDistribution($lid, $tname, $pIssue, $sIssue);
				// 更新 paiqi 表状态 synflag
				$this->collect_model->updatePaiqi($tname, $pIssue, $lid);
			}
		}
	}

	// 快乐扑克
	public function collect_klpk($lid)
	{
		$tname = 'klpk';
		$lists = $this->collect_model->getCfgPaiqi($tname, $this->order_status['paiqi_jjsucc']);
		if(!empty($lists))
		{
			// 15146
			foreach ($lists as $pIssue)
			{
				// split表期号格式处理
				$sIssue = $pIssue;
				$this->getDistribution($lid, $tname, $pIssue, $sIssue);
				// 更新 paiqi 表状态 synflag
				$this->collect_model->updatePaiqi($tname, $pIssue, $lid);
			}
		}
	}

	// 老时时彩
	public function collect_cqssc($lid)
	{
		$tname = 'cqssc';
		$lists = $this->collect_model->getCfgPaiqi($tname, $this->order_status['paiqi_jjsucc']);
		if(!empty($lists))
		{
			foreach ($lists as $pIssue)
			{
				// split表期号格式处理
				$sIssue = $pIssue;
				$this->getDistribution($lid, $tname, $pIssue, $sIssue);
				// 更新 paiqi 表状态 synflag
				$this->collect_model->updatePaiqi($tname, $pIssue, $lid);
			}
		}
	}

	// 七乐彩
	public function collect_qlc($lid)
	{
		$tname = 'qlc';
		$lists = $this->collect_model->getCfgPaiqi($tname, $this->order_status['paiqi_jjsucc']);
		if(!empty($lists))
		{
			// 15146
			foreach ($lists as $pIssue) 
			{
				// split表期号格式处理
				$sIssue = $pIssue;
				$this->getDistribution($lid, $tname, $pIssue, $sIssue);
				// 更新 paiqi 表状态 synflag
				$this->collect_model->updatePaiqi($tname, $pIssue, $lid);
			}
		}
	}

	// 大乐透
	public function collect_dlt($lid)
	{
		$tname = 'dlt';
		$lists = $this->collect_model->getCfgPaiqi($tname, $this->order_status['paiqi_jjsucc']);
		if(!empty($lists))
		{
			// 15146
			foreach ($lists as $pIssue) 
			{
				// split表期号格式处理
				$sIssue = $this->libcomm->format_issue($pIssue, 1, 2);
				$this->getDistribution($lid, $tname, $pIssue, $sIssue);
				// 更新 paiqi 表状态 synflag
				$this->collect_model->updatePaiqi($tname, $pIssue, $lid);
			}
		}
	}

	// 北京单场
	// public function collect_bjdc($lid)
	// {
	// 	$tname = 'bjdc';
	// 	$lists = $this->collect_model->getCfgPaiqi($tname, $this->order_status['paiqi_jjsucc']);
	// 	var_dump($lists);die;
	// 	if(!empty($lists))
	// 	{
	// 		// 15146
	// 		foreach ($lists as $pIssue) 
	// 		{
	// 			// split表期号格式处理
	// 			$sIssue = $this->libcomm->format_issue($pIssue, 1, 2);
	// 			$this->getDistribution($lid, $tname, $pIssue, $sIssue);
	// 			// 更新 paiqi 表状态 synflag
	// 			$this->collect_model->updatePaiqi($tname, $pIssue);
	// 		}
	// 	}
	// }

	// 公共函数
	public function getDistribution($lid, $tname, $pIssue, $sIssue)
	{
		$details = $this->collect_model->countSplitDetail($lid, $sIssue);

		// 组装数据
		$data = array();
		$data['lottery_id'] = $lid;
		$data['issue'] = $sIssue;
		$data['total_sales'] = $details['money']?$details['money']:0;
		$data['bonus'] = $details['bonus']?$details['bonus']:0;
		$data['margin'] = $details['margin']?$details['margin']:0;
		// $data['bonus_t'] = $details['bonus_t']?$details['bonus_t']:0;
		// $data['margin_t'] = $details['margin_t']?$details['margin_t']:0;

		$this->collect_model->setDistribution($data);
	}
}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */