<?php

class Cli_Match extends MY_Controller {
	
    private $cronConfig;
    public function __construct() {
        parent::__construct();
        $this->load->model('data_model', 'Data');
        $this->cronConfig = $this->Data->getCrons();
    }

    
    
    public function index()
    {
    	//北京单场
    	$this->captureBjdcSfggMatch();
    	$this->captureBjdcSpfMatch();
    	$this->captureBjdcJqsMatch();
    	$this->captureBjdcBqcMatch();
    	$this->captureBjdcDssMatch();
    	$this->captureBjdcDcbfMatch();
    	//竞彩篮球
    	$this->captureJclqSfMatch();
    	$this->captureJclqRfsfMatch();
    	$this->captureJclqSfcMatch();
    	$this->captureJclqDxfMatch();
    	//竞彩足球
    	$this->captureJczqSpfMatch();
    	$this->captureJczqRqspfMatch();
    	$this->captureJczqCbfMatch();
    	$this->captureJczqJqsMatch();
    	$this->captureJczqBqcMatch();
    	//老足彩
    	$this->captureTczqSfcMatch();
    	$this->captureTczqBqcMatch();
    	$this->captureTczqJqcMatch();
    }
    /*
     * 【北京单场】对阵抓取 -- 主函数
     * @author:shigx
     * @date:2015-04-09
     */
    public function captureBjdcSfggMatch()
    {
    	
        $bjdcSfggConfig = isset($this->cronConfig['bjdc_sfgg']) ? $this->cronConfig['bjdc_sfgg'] : array();
        foreach ($bjdcSfggConfig as $bjdcSfgg) 
        {
        	$this->load->library($bjdcSfgg['lname']);
        	$bjdcSfgg['type'] = 'sfgg';
        	$this->$bjdcSfgg['lname']->capture($bjdcSfgg);
        }
    }
    
    /*
     * 【北京单场】对阵抓取 -- 主函数
    * @author:shigx
    * @date:2015-04-09
    */
    public function captureBjdcSpfMatch()
    {
    	 
    	$bjdcSpfConfig = isset($this->cronConfig['bjdc_spf']) ? $this->cronConfig['bjdc_spf'] : array();
    	foreach ($bjdcSpfConfig as $bjdcSpf)
    	{
    		$this->load->library($bjdcSpf['lname']);
    		$bjdcSpf['type'] = 'spf';
    		$this->$bjdcSpf['lname']->capture($bjdcSpf);
    	}
    }
    
    /*
     * 【北京单场】对阵抓取 -- 主函数
    * @author:shigx
    * @date:2015-04-09
    */
    public function captureBjdcJqsMatch()
    {
    
    	$bjdcJqsConfig = isset($this->cronConfig['bjdc_jqs']) ? $this->cronConfig['bjdc_jqs'] : array();
    	foreach ($bjdcJqsConfig as $bjdcJqs)
    	{
    		$this->load->library($bjdcJqs['lname']);
    		$bjdcJqs['type'] = 'jqs';
    		$this->$bjdcJqs['lname']->capture($bjdcJqs);
    	}
    }
    
    /*
     * 【北京单场】对阵抓取 -- 主函数
    * @author:shigx
    * @date:2015-04-09
    */
    public function captureBjdcBqcMatch()
    {
    
    	$bjdcBqcConfig = isset($this->cronConfig['bjdc_bqc']) ? $this->cronConfig['bjdc_bqc'] : array();
    	foreach ($bjdcBqcConfig as $bjdcBqc)
    	{
    		$this->load->library($bjdcBqc['lname']);
    		$bjdcBqc['type'] = 'bqc';
    		$this->$bjdcBqc['lname']->capture($bjdcBqc);
    	}
    }
    
    /*
     * 【北京单场】对阵抓取 -- 主函数
    * @author:shigx
    * @date:2015-04-09
    */
    public function captureBjdcDssMatch()
    {
    
    	$bjdcDssConfig = isset($this->cronConfig['bjdc_dss']) ? $this->cronConfig['bjdc_dss'] : array();
    	foreach ($bjdcDssConfig as $bjdcDss)
    	{
    		$this->load->library($bjdcDss['lname']);
    		$bjdcDss['type'] = 'dss';
    		$this->$bjdcDss['lname']->capture($bjdcDss);
    	}
    }
    
    /*
     * 【北京单场】对阵抓取 -- 主函数
    * @author:shigx
    * @date:2015-04-09
    */
    public function captureBjdcDcbfMatch()
    {
    
    	$bjdcDcbfConfig = isset($this->cronConfig['bjdc_dcbf']) ? $this->cronConfig['bjdc_dcbf'] : array();
    	foreach ($bjdcDcbfConfig as $bjdcDcbf)
    	{
    		$this->load->library($bjdcDcbf['lname']);
    		$bjdcDcbf['type'] = 'dcbf';
    		$this->$bjdcDcbf['lname']->capture($bjdcDcbf);
    	}
    }
    
    /*
     * 【竞彩篮球】对阵抓取 -- 主函数
    * @author:shigx
    * @date:2015-04-09
    */
    public function captureJclqSfMatch()
    {
    	 
    	$jclqSfConfig = isset($this->cronConfig['jclq_sf']) ? $this->cronConfig['jclq_sf'] : array();
    	foreach ($jclqSfConfig as $jclqSf)
    	{
    		$this->load->library($jclqSf['lname']);
    		$jclqSf['type'] = 'sf';
    		$this->$jclqSf['lname']->capture($jclqSf);
    	}
    }
    
    /*
     * 【竞彩篮球】对阵抓取 -- 主函数
    * @author:shigx
    * @date:2015-04-09
    */
    public function captureJclqRfsfMatch()
    {
    
    	$jclqRfsfConfig = isset($this->cronConfig['jclq_rfsf']) ? $this->cronConfig['jclq_rfsf'] : array();
    	foreach ($jclqRfsfConfig as $jclqRfsf)
    	{
    		$this->load->library($jclqRfsf['lname']);
    		$jclqRfsf['type'] = 'rfsf';
    		$this->$jclqRfsf['lname']->capture($jclqRfsf);
    	}
    }
    
    /*
     * 【竞彩篮球】对阵抓取 -- 主函数
    * @author:shigx
    * @date:2015-04-09
    */
    public function captureJclqSfcMatch()
    {
    
    	$jclqSfcConfig = isset($this->cronConfig['jclq_sfc']) ? $this->cronConfig['jclq_sfc'] : array();
    	foreach ($jclqSfcConfig as $jclqSfc)
    	{
    		$this->load->library($jclqSfc['lname']);
    		$jclqSfc['type'] = 'sfc';
    		$this->$jclqSfc['lname']->capture($jclqSfc);
    	}
    }
    
    /*
     * 【竞彩篮球】对阵抓取 -- 主函数
    * @author:shigx
    * @date:2015-04-09
    */
    public function captureJclqDxfMatch()
    {
    
    	$jclqDxfConfig = isset($this->cronConfig['jclq_dxf']) ? $this->cronConfig['jclq_dxf'] : array();
    	foreach ($jclqDxfConfig as $jclqDxf)
    	{
    		$this->load->library($jclqDxf['lname']);
    		$jclqDxf['type'] = 'dxf';
    		$this->$jclqDxf['lname']->capture($jclqDxf);
    	}
    }
    
    /*
     * 【竞彩足球】对阵抓取 -- 主函数
    * @author:shigx
    * @date:2015-04-09
    */
    public function captureJczqSpfMatch()
    {
    
    	$jczqSpfConfig = isset($this->cronConfig['jczq_spf']) ? $this->cronConfig['jczq_spf'] : array();
    	foreach ($jczqSpfConfig as $jczqSpf)
    	{
    		$this->load->library($jczqSpf['lname']);
    		$jczqSpf['type'] = 'spf';
    		$this->$jczqSpf['lname']->capture($jczqSpf);
    	}
    }
    
    /*
     * 【竞彩足球】对阵抓取 -- 主函数
    * @author:shigx
    * @date:2015-04-09
    */
    public function captureJczqRqspfMatch()
    {
    
    	$jczqRqspfConfig = isset($this->cronConfig['jczq_rqspf']) ? $this->cronConfig['jczq_rqspf'] : array();
    	foreach ($jczqRqspfConfig as $jczqRqspf)
    	{
    		$this->load->library($jczqRqspf['lname']);
    		$jczqRqspf['type'] = 'rqspf';
    		$this->$jczqRqspf['lname']->capture($jczqRqspf);
    	}
    }
    
    /*
     * 【竞彩足球】对阵抓取 -- 主函数
    * @author:shigx
    * @date:2015-04-09
    */
    public function captureJczqCbfMatch()
    {
    
    	$jczqCbfConfig = isset($this->cronConfig['jczq_cbf']) ? $this->cronConfig['jczq_cbf'] : array();
    	foreach ($jczqCbfConfig as $jczqCbf)
    	{
    		$this->load->library($jczqCbf['lname']);
    		$jczqCbf['type'] = 'cbf';
    		$this->$jczqCbf['lname']->capture($jczqCbf);
    	}
    }
    
    /*
     * 【竞彩足球】对阵抓取 -- 主函数
    * @author:shigx
    * @date:2015-04-09
    */
    public function captureJczqJqsMatch()
    {
    
    	$jczqJqsConfig = isset($this->cronConfig['jczq_jqs']) ? $this->cronConfig['jczq_jqs'] : array();
    	foreach ($jczqJqsConfig as $jczqJqs)
    	{
    		$this->load->library($jczqJqs['lname']);
    		$jczqJqs['type'] = 'jqs';
    		$this->$jczqJqs['lname']->capture($jczqJqs);
    	}
    }
    
    /*
     * 【竞彩足球】对阵抓取 -- 主函数
    * @author:shigx
    * @date:2015-04-09
    */
    public function captureJczqBqcMatch()
    {
    
    	$jczqBqcConfig = isset($this->cronConfig['jczq_bqc']) ? $this->cronConfig['jczq_bqc'] : array();
    	foreach ($jczqBqcConfig as $jczqBqc)
    	{
    		$this->load->library($jczqBqc['lname']);
    		$jczqBqc['type'] = 'bqc';
    		$this->$jczqBqc['lname']->capture($jczqBqc);
    	}
    }
    
    /*
     * 【体彩足球】对阵抓取 -- 主函数
    * @author:shigx
    * @date:2015-04-09
    */
    public function captureTczqSfcMatch()
    {
    
    	$tczqSfcConfig = isset($this->cronConfig['tczq_sfc']) ? $this->cronConfig['tczq_sfc'] : array();
    	foreach ($tczqSfcConfig as $tczqSfc)
    	{
    		$this->load->library($tczqSfc['lname']);
    		$tczqSfc['type'] = 'sfc';
    		$this->$tczqSfc['lname']->capture($tczqSfc);
    	}
    }
    
    /*
     * 【体彩足球】对阵抓取 -- 主函数
    * @author:shigx
    * @date:2015-04-09
    */
    public function captureTczqBqcMatch()
    {
    
    	$tczqBqcConfig = isset($this->cronConfig['tczq_bqc']) ? $this->cronConfig['tczq_bqc'] : array();
    	foreach ($tczqBqcConfig as $tczqBqc)
    	{
    		$this->load->library($tczqBqc['lname']);
    		$tczqBqc['type'] = 'bqc';
    		$this->$tczqBqc['lname']->capture($tczqBqc);
    	}
    }
    
    /*
     * 【体彩足球】对阵抓取 -- 主函数
    * @author:shigx
    * @date:2015-04-09
    */
    public function captureTczqJqcMatch()
    {
    
    	$tczqJqcConfig = isset($this->cronConfig['tczq_jqc']) ? $this->cronConfig['tczq_jqc'] : array();
    	foreach ($tczqJqcConfig as $tczqJqc)
    	{
    		$this->load->library($tczqJqc['lname']);
    		$tczqJqc['type'] = 'jqc';
    		$this->$tczqJqc['lname']->capture($tczqJqc);
    	}
    }
 }
