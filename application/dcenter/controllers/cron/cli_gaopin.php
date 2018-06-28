<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Cli_Gaopin extends MY_Controller 
{
	private $cronConfig;
	public function __construct() 
	{
		parent::__construct();
		$this->load->model('data_model', 'Data');
		$this->cronConfig = $this->Data->getCrons();
	}
	
	public function index()
	{
		$this->captureSyxw();
		$this->captureKs();
		$this->captureJxsyxw();
		$this->captureKlpk();
		$this->captureHbsyxw();
		$this->captureCqssc();
		$this->captureJlks();
		$this->captureJxks();
	}
	
	/*
	 * 【十一选五】开奖号码抓取
	* @author:shigx
	* @date:2015-04-09
	*/
	public function captureSyxw()
	{
		if(!empty($this->cronConfig['syxw']))
		{
			foreach ($this->cronConfig['syxw'] as $syxw)
			{
				$this->load->library($syxw['lname']);
				$this->$syxw['lname']->capture($syxw);
			}
		}	
	}
	
	/*
	 * 【上海快三】开奖号码抓取
	* @author:shigx
	* @date:2015-04-09
	*/
	public function captureKs()
	{
		if(!empty($this->cronConfig['ks']))
		{
			foreach ($this->cronConfig['ks'] as $ks)
			{
				$this->load->library($ks['lname']);
				$this->$ks['lname']->capture($ks);
			}
		}	
	}
	
	/**
	 * [captureJlks 【吉林快三】开奖号码抓取]
	 * @author LiKangJian 2017-11-16
	 * @return [type] [description]
	 */
	public function captureJlks()
	{
		if(!empty($this->cronConfig['jlks']))
		{
			foreach ($this->cronConfig['jlks'] as $jlks)
			{
				$this->load->library($jlks['lname']);
				$this->$jlks['lname']->capture($jlks);
			}
		}	
	}
	
	/**
	 * [captureJxks 【江西快三】开奖号码抓取]
	 * @author LiKangJian 2017-11-16
	 * @return [type] [description]
	 */
	public function captureJxks()
	{
	    if(!empty($this->cronConfig['jxks']))
	    {
	        foreach ($this->cronConfig['jxks'] as $jxks)
	        {
	            $this->load->library($jxks['lname']);
	            $this->$jxks['lname']->capture($jxks);
	        }
	    }
	}
	
	/*
	 * 【江西十一选五】开奖号码抓取
	* @author:shigx
	* @date:2015-04-09
	*/
	public function captureJxsyxw()
	{
		if($this->cronConfig['jxsyxw'])
		{
			foreach ($this->cronConfig['jxsyxw'] as $jxsyxw)
			{
				$this->load->library($jxsyxw['lname']);
				$this->$jxsyxw['lname']->capture($jxsyxw);
			}
		}	
	}
	
	/*
	 * 【快乐扑克】开奖号码抓取
	* @author:shigx
	* @date:2015-04-09
	*/
	public function captureKlpk()
	{
		if(!empty($this->cronConfig['klpk']))
		{
			foreach ($this->cronConfig['klpk'] as $klpk)
			{
				$this->load->library($klpk['lname']);
				$this->$klpk['lname']->capture($klpk);
			}
		}	
	}
	
	/*
	 * 【湖北十一选五】开奖号码抓取
	* @author:shigx
	* @date:2015-04-09
	*/
	public function captureHbsyxw()
	{
		if(!empty($this->cronConfig['hbsyxw']))
		{
			foreach ($this->cronConfig['hbsyxw'] as $hbsyxw)
			{
				$this->load->library($hbsyxw['lname']);
				$this->$hbsyxw['lname']->capture($hbsyxw);
			}
		}	
	}

	/*
	 * 【老时时彩】开奖号码抓取
	* @author:liul
	* @date:2017-07-05
	*/
	public function captureCqssc()
    {
        if(!empty($this->cronConfig['cqssc']))
        {
            foreach ($this->cronConfig['cqssc'] as $cqssc)
            {
                $this->load->library($cqssc['lname']);
                $this->$cqssc['lname']->capture($cqssc);
            }
        }
    }
}
