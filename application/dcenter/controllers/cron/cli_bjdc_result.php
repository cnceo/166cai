<?php

class Cli_Bjdc_Result extends MY_Controller {
	
    public function __construct() {
        parent::__construct();
        $this->load->model('Match_model');
    }

    
    
    public function index()
    {
    	$this->captureBjdcSfggResult();
    	$this->captureBjdcSpfResult();
    	$this->captureBjdcJqsResult();
    	$this->captureBjdcBqcResult();
    	$this->captureBjdcDssResult();
    	$this->captureBjdcDcbfResult();
    	$this->captureBjdcXbcbfResult();
    }
    
    /*
     * 【北京单场】赔率抓取 -- 主函数
    * @author:shigx
    * @date:2015-04-09
    */
    public function captureBjdcSfggResult()
    {
    	$this->load->library('bjdc_result_bjlot');
    	$datas = $this->Match_model->keysOfSfgg();
    	$this->bjdc_result_bjlot->capture('sfgg', $datas);
    }
    
    /*
     * 【北京单场】赔率抓取 -- 主函数
    * @author:shigx
    * @date:2015-04-09
    */
    public function captureBjdcSpfResult()
    {
    	$this->load->library('bjdc_result_bjlot');
    	$datas = $this->Match_model->keysOfBjdc();
    	$this->bjdc_result_bjlot->capture('spf', $datas);
    }
    
    /*
     * 【北京单场】赔率抓取 -- 主函数
    * @author:shigx
    * @date:2015-04-09
    */
    public function captureBjdcJqsResult()
    {
    	$this->load->library('bjdc_result_bjlot');
    	$datas = $this->Match_model->keysOfBjdc();
    	$this->bjdc_result_bjlot->capture('jqs', $datas);
    }
    
    /*
     * 【北京单场】赔率抓取 -- 主函数
    * @author:shigx
    * @date:2015-04-09
    */
    public function captureBjdcBqcResult()
    {
    	$this->load->library('bjdc_result_bjlot');
    	$datas = $this->Match_model->keysOfBjdc();
    	$this->bjdc_result_bjlot->capture('bqc', $datas);
    }
    
    /*
     * 【北京单场】赔率抓取 -- 主函数
    * @author:shigx
    * @date:2015-04-09
    */
    public function captureBjdcDssResult()
    {
    	$this->load->library('bjdc_result_bjlot');
    	$datas = $this->Match_model->keysOfBjdc();
    	$this->bjdc_result_bjlot->capture('dss', $datas);
    }
    
    /*
     * 【北京单场】赔率抓取 -- 主函数
    * @author:shigx
    * @date:2015-04-09
    */
    public function captureBjdcDcbfResult()
    {
    	$this->load->library('bjdc_result_bjlot');
    	$datas = $this->Match_model->keysOfBjdc();
    	$this->bjdc_result_bjlot->capture('dcbf', $datas);
    }
    
    /*
     * 【北京单场】赔率抓取 -- 主函数
    * @author:shigx
    * @date:2015-04-09
    */
    public function captureBjdcXbcbfResult()
    {
    	$this->load->library('bjdc_result_bjlot');
    	$datas = $this->Match_model->keysOfBjdc();
    	$this->bjdc_result_bjlot->capture('xbcbf', $datas);
    }
 }
