<?php

class Cli_Award extends MY_Controller {
	
    private $cronConfig;
    private $num = 10;
    public function __construct() {
        parent::__construct();
        $this->load->model('data_model', 'Data');
        $this->cronConfig = $this->Data->getCrons();
    }
    
    public function index()
    {
    	$this->captureJczqScore();
    	log_message('LOG', "captureJczqScore", 'result');
    	$this->captureJclqScore();
    	log_message('LOG', "captureJclqScore", 'result');
    	$this->captureBjdcScore();
    	log_message('LOG', "captureBjdcScore", 'result');
    	$this->captureSfggScore();
    	log_message('LOG', "captureSfggScore", 'result');
    	$this->captureSfcScore();
    	log_message('LOG', "captureSfcScore", 'result');
    	$this->captureBqcScore();
    	log_message('LOG', "captureBqcScore", 'result');
    	$this->captureJqcScore();
    	log_message('LOG', "captureJqcScore", 'result');
    	$this->captureSfcDetail();
    	log_message('LOG', "captureSfcDetail", 'result');
    	$this->captureBqcDetail();
    	log_message('LOG', "captureBqcDetail", 'result');
    	$this->captureJqcDetail();
    	log_message('LOG', "captureJqcDetail", 'result');
    	$this->captureSsqAward();
    	log_message('LOG', "captureSsqAward", 'result');
    	$this->captureRssqAward();
    	log_message('LOG', "captureRssqAward", 'result');
    	$this->captureBqcResult();
    	log_message('LOG', "captureBqcResult", 'result');
    	$this->captureJqcResult();
    	log_message('LOG', "captureJqcResult", 'result');
    	$this->captureDltAward();
    	log_message('LOG', "captureJclqScore", 'result');
    	$this->captureRdltAward();
    	log_message('LOG', "captureRdltAward", 'result');
    	$this->captureQlcAward();
    	log_message('LOG', "captureQlcAward", 'result');
		$this->captureRqlcAward();   
		log_message('LOG', "captureRqlcAward", 'result'); 	
    	$this->captureQxcAward();
    	log_message('LOG', "captureQxcAward", 'result');
    	$this->captureRqxcAward();
    	log_message('LOG', "captureRqxcAward", 'result');
    	$this->captureFc3dAward();
    	log_message('LOG', "captureFc3dAward", 'result');
    	$this->captureRfc3dAward();
    	log_message('LOG', "captureRfc3dAward", 'result');
    	$this->capturePl3Award();
    	log_message('LOG', "capturePl3Award", 'result');
    	$this->captureRpl3Award();
    	log_message('LOG', "captureRpl3Award", 'result');
    	$this->capturePl5Award();
    	log_message('LOG', "capturePl5Award", 'result');
    	$this->captureRpl5Award();
    	log_message('LOG', "captureRpl5Award", 'result');
    	$this->captureSfcResult();
    	log_message('LOG', "captureSfcResult", 'result');
    	
    }
    /*
     * 【竞彩足球】赛果抓取 -- 主函数
     * @author:liuli
     * @date:2015-03-12
     */
    public function captureJczqScore()
    {
    	
        $jczqConfig = $this->cronConfig['jczq'];
        $data = $this->Data->keysOfJczq();
        $data['num'] = $this->num;
        foreach ($jczqConfig as $jczq) 
        {
        	$this->load->library($jczq['lname']);
        	$this->$jczq['lname']->capture($jczq, $data);
        }
    }

    /*
     * 【竞彩篮球】赛果抓取 -- 主函数
     * @author:liuli
     * @date:2015-03-13
     */
    public function captureJclqScore()
    {
        $jclqConfig = $this->cronConfig['jclq'];
        $data = $this->Data->keysOfJclq();
        $data['num'] = $this->num;
        foreach ($jclqConfig as $jclq) 
        {
        	$this->load->library($jclq['lname']);
        	$this->$jclq['lname']->capture($jclq, $data);
        }
    }

    /*
     * 【北单】赛果抓取 -- 主函数
     * @author:liuli
     * @date:2015-03-16
     */
    public function captureBjdcScore()
    {
		
        $bjdcConfig = $this->cronConfig['bjdc'];
        $data = $this->Data->keysOfBjdc();
        $data['num'] = $this->num;
        foreach ($bjdcConfig as $bjdc) 
        {
        	$this->load->library($bjdc['lname']);
        	$this->$bjdc['lname']->capture($bjdc, $data);
        }
    }
    
	public function captureSfggScore()
    {
		
        $sfggConfig = $this->cronConfig['sfgg'];
        $data = $this->Data->keysOfSfgg();
        $data['num'] = $this->num;
        foreach ($sfggConfig as $sfgg) 
        {
        	$this->load->library($sfgg['lname']);
        	$this->$sfgg['lname']->capture($sfgg, $data);
        }
    }

    /*
     * 【胜负彩】赛果抓取 -- 主函数
     * @author:liuli
     * @date:2015-03-16
     */
    public function captureSfcScore()
    {
        $sfcConfig = $this->cronConfig['sfc'];
        $data = $this->Data->keysOfSfc(1);
        $data['num'] = $this->num;
        foreach ($sfcConfig as $sfc) 
        {
        	$this->load->library($sfc['lname']);
        	$this->$sfc['lname']->capture($sfc, $data);
        }
    }

    /*
     * 【半全场】赛果抓取 -- 主函数
     * @author:liuli
     * @date:2015-03-16
     */
    public function captureBqcScore()
    {
        $bqcConfig = $this->cronConfig['bqc'];
        $data = $this->Data->keysOfSfc(2);
        $data['num'] = $this->num;
        foreach ($bqcConfig as $bqc) 
        {
        	$this->load->library($bqc['lname']);
        	$this->$bqc['lname']->capture($bqc, $data);
        }
    }

    /*
     * 【进球彩】赛果抓取 -- 主函数
     * @author:liuli
     * @date:2015-03-16
     */
    public function captureJqcScore()
    {
        $jqcConfig = $this->cronConfig['jqc'];
        $data = $this->Data->keysOfSfc(3);
        $data['num'] = $this->num;
        foreach ($jqcConfig as $jqc) 
        {
        	$this->load->library($jqc['lname']);
        	$this->$jqc['lname']->capture($jqc, $data);
        }
    }

    /*
     * 【双色球】赛果抓取 -- 主函数
     * @author:liuli
     * @date:2015-03-16
     */
    public function captureSsqAward()
    {
        $ssqConfig = $this->cronConfig['ssq'];
        $data = $this->Data->keysOfNumber('ssq');
        $data['num'] = $this->num;
        foreach ($ssqConfig as $ssq) 
        {
        	$this->load->library($ssq['lname']);
        	$this->$ssq['lname']->capture($ssq,$data);
        }
    }
    
	public function captureRssqAward()
    {
        $ssqConfig = $this->cronConfig['rssq'];
        $data = $this->Data->keysOfRNumber('ssq');
        $data['num'] = $this->num;
        foreach ($ssqConfig as $ssq) 
        {
        	$this->load->library($ssq['lname']);
        	$this->$ssq['lname']->capture($ssq,$data);
        }
    }
    
    /*
     * 【半全场】开奖抓取 -- 主函数
     * @date:2015-03-24
     */
 	public function captureBqcResult()
    {
        $rbcqConfig = $this->cronConfig['rbqc'];
        $data = $this->Data->keysOfRsfc(2);
        $data['num'] = $this->num;
        foreach ($rbcqConfig as $rbcq) 
        {
        	$this->load->library($rbcq['lname']);
        	$this->$rbcq['lname']->capture($rbcq, $data);
        }
    }
    
	public function captureBqcDetail()
    {
        $dbcqConfig = $this->cronConfig['dbqc'];
        $data = $this->Data->keysOfDsfc(2);
        $data['num'] = $this->num;
        foreach ($dbcqConfig as $dbcq) 
        {
        	$this->load->library($dbcq['lname']);
        	$this->$dbcq['lname']->capture($dbcq, $data);
        }
    }
    
    /*
     * 【进球彩】开奖抓取 -- 主函数
     * @date:2015-03-24
     */
	public function captureJqcResult()
    {
        $rjcqConfig = $this->cronConfig['rjqc'];
        $data = $this->Data->keysOfRsfc(3);
        $data['num'] = $this->num;
        foreach ($rjcqConfig as $rjcq) 
        {
        	$this->load->library($rjcq['lname']);
        	$this->$rjcq['lname']->capture($rjcq, $data);
        }
    }
    
	public function captureJqcDetail()
    {
        $djcqConfig = $this->cronConfig['djqc'];
        $data = $this->Data->keysOfDsfc(3);
        $data['num'] = $this->num;
        foreach ($djcqConfig as $djcq) 
        {
        	$this->load->library($djcq['lname']);
        	$this->$djcq['lname']->capture($djcq, $data);
        }
    }
    
	/*
     * 【胜负彩】开奖抓取 -- 主函数
     * @date:2015-03-24
     */
    public function captureSfcResult()
    {
        $rsfcConfig = $this->cronConfig['rsfc'];
        $data = $this->Data->keysOfRsfc(1);
        $data['num'] = $this->num;
        foreach ($rsfcConfig as $rsfc) 
        {
            $this->load->library($rsfc['lname']);
            $this->$rsfc['lname']->capture($rsfc, $data);
        }
    }
    
	public function captureSfcDetail()
    {
        $dsfcConfig = $this->cronConfig['dsfc'];
        $data = $this->Data->keysOfDsfc(1);
        $data['num'] = $this->num;
        foreach ($dsfcConfig as $dsfc) 
        {
            $this->load->library($dsfc['lname']);
            $this->$dsfc['lname']->capture($dsfc, $data);
        }
    }

    /*
     * 【大乐透】赛果抓取 -- 主函数
     * @date:2015-03-24
     */
    public function captureDltAward()
    {
        $dltConfig = $this->cronConfig['dlt'];
        $data = $this->Data->keysOfNumber('dlt');
        $data['num'] = $this->num;
        foreach ($dltConfig as $dlt) 
        {
        	$this->load->library($dlt['lname']);
        	$this->$dlt['lname']->capture($dlt,$data);
        }
    }
    
	public function captureRdltAward()
    {
        $dltConfig = $this->cronConfig['rdlt'];
        $data = $this->Data->keysOfRNumber('dlt');
        $data['num'] = $this->num;
        foreach ($dltConfig as $dlt) 
        {
        	$this->load->library($dlt['lname']);
        	$this->$dlt['lname']->capture($dlt,$data);
        }
    }

    /*
     * 【七乐彩】赛果抓取 -- 主函数
     * @date:2015-03-24
     */
    public function captureQlcAward()
    {
        $qlcConfig = $this->cronConfig['qlc'];
        $data = $this->Data->keysOfNumber('qlc');
        $data['num'] = $this->num;
        foreach ($qlcConfig as $qlc) 
        {
            $this->load->library($qlc['lname']);
            $this->$qlc['lname']->capture($qlc,$data);
        }
    }
    
	public function captureRqlcAward()
    {
        $qlcConfig = $this->cronConfig['rqlc'];
        $data = $this->Data->keysOfRNumber('qlc');
        $data['num'] = $this->num;
        foreach ($qlcConfig as $qlc) 
        {
            $this->load->library($qlc['lname']);
            $this->$qlc['lname']->capture($qlc,$data);
        }
    }

    /*
     * 【七星彩】赛果抓取 -- 主函数
     * @date:2015-03-24
     */
    public function captureQxcAward()
    {
        $qxcConfig = $this->cronConfig['qxc'];
        $data = $this->Data->keysOfNumber('qxc');
        $data['num'] = $this->num;
        foreach ($qxcConfig as $qxc) 
        {
            $this->load->library($qxc['lname']);
            $this->$qxc['lname']->capture($qxc,$data);
        }
    }
    
	public function captureRqxcAward()
    {
        $qxcConfig = $this->cronConfig['rqxc'];
        $data = $this->Data->keysOfRNumber('qxc');
        $data['num'] = $this->num;
        foreach ($qxcConfig as $qxc) 
        {
            $this->load->library($qxc['lname']);
            $this->$qxc['lname']->capture($qxc,$data);
        }
    }

    /*
     * 【福彩3D】赛果抓取 -- 主函数
     * @date:2015-03-24
     */
    public function captureFc3dAward()
    {
        $fc3dConfig = $this->cronConfig['fc3d'];
        $data = $this->Data->keysOfNumber('fc3d');
        $data['num'] = $this->num;
        foreach ($fc3dConfig as $fc3d) 
        {
            $this->load->library($fc3d['lname']);
            $this->$fc3d['lname']->capture($fc3d,$data);
        }
    }
    
	public function captureRfc3dAward()
    {
        $fc3dConfig = $this->cronConfig['rfc3d'];
        $data = $this->Data->keysOfRNumber('fc3d');
        $data['num'] = $this->num;
        foreach ($fc3dConfig as $fc3d) 
        {
            $this->load->library($fc3d['lname']);
            $this->$fc3d['lname']->capture($fc3d,$data);
        }
    }

    /*
     * 【排列三】赛果抓取 -- 主函数
     * @date:2015-03-24
     */
    public function capturePl3Award()
    {
        $pl3Config = $this->cronConfig['pl3'];
        $data = $this->Data->keysOfNumber('pl3');
        $data['num'] = $this->num;
        foreach ($pl3Config as $pl3) 
        {
            $this->load->library($pl3['lname']);
            $this->$pl3['lname']->capture($pl3,$data);
        }
    }
    
	public function captureRpl3Award()
    {
        $pl3Config = $this->cronConfig['rpl3'];
        $data = $this->Data->keysOfRNumber('pl3');
        $data['num'] = $this->num;
        foreach ($pl3Config as $pl3) 
        {
            $this->load->library($pl3['lname']);
            $this->$pl3['lname']->capture($pl3,$data);
        }
    }

    /*
     * 【排列五】赛果抓取 -- 主函数
     * @date:2015-03-24
     */
    public function capturePl5Award()
    {
        $pl5Config = $this->cronConfig['pl5'];
        $data = $this->Data->keysOfNumber('pl5');
        $data['num'] = $this->num;
        foreach ($pl5Config as $pl5) 
        {
            $this->load->library($pl5['lname']);
            $this->$pl5['lname']->capture($pl5,$data);
        }
    }
    
	public function captureRpl5Award()
    {
        $pl5Config = $this->cronConfig['rpl5'];
        $data = $this->Data->keysOfRNumber('pl5');
        $data['num'] = $this->num;
        foreach ($pl5Config as $pl5) 
        {
            $this->load->library($pl5['lname']);
            $this->$pl5['lname']->capture($pl5,$data);
        }
    }
 }
