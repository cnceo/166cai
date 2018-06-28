<?php

	/*
	 * 数据中心 -- 期次预排执行脚本
	 * @author:liuli
	 * @date:2015-03-27
	 */

class Cli_Issue extends MY_Controller {

	//初始期号
    private $default = array(
        'ssq' => array(
            'issue' => '2005001',                   //期号 2005001 
            'award_time' => '2005-01-02 21:30:00',  //开奖日期 2005-01-02 21:30:00   
            'sale_time' => '09:00:00',              //销售时间
        ),
        'dlt' => array(
            'issue' => '08001',
            'award_time' => '2008-01-02 20:30:00',
            'sale_time' => '09:00:00',
        ),
        'qxc' => array(
            'issue' => '05001',
            'award_time' => '2005-01-02 20:30:00',
            'sale_time' => '09:00:00',
        ),
        'qlc' => array(
            'issue' => '2008001',
            'award_time' => '2008-01-02 21:30:00',
            'sale_time' => '09:00:00',
        ),
        'fc3d' => array(
            'issue' => '2005001',
            'award_time' => '2005-01-01 21:15:00',
            'sale_time' => '09:00:00',
        ),
        'pl3' => array(
            'issue' => '05001',
            'award_time' => '2005-01-01 20:30:00',
            'sale_time' => '09:00:00',
        ),
        'pl5' => array(
            'issue' => '05001',
            'award_time' => '2005-01-01 20:30:00',
            'sale_time' => '09:00:00',
        ),
        //十一运 每日第一期时间
        'syxw' => array(
            'issue' => '14123101',
            'award_time' => '2017-04-07 08:36:20',
            'sale_time' => '08:26:20',
        ),
    	'jxsyxw' => array(
    		'issue' => '16010101',
    		'award_time' => '2016-01-01 09:10:00',
    		'sale_time' => '09:00:00',
    	),
    	'ks' => array(
    		'issue' => '20160520001',
    		'award_time' => '2016-05-20 08:58:00',
    		'sale_time' => '08:48:00',
    	),
    	'jlks' => array(
    		'issue' => '20171110001',
    		'award_time' => '2017-11-10 08:29:00',
    		'sale_time' => '08:20:00',
    	),    
        'jxks' => array(
            'issue' => '20171222001',
            'award_time' => '2017-12-22 09:05:00',
            'sale_time' => '08:55:00',
        ),
    	'hbsyxw' => array(
    		'issue' => '16082901',
    		'award_time' => '2016-08-29 08:35:00',
    		'sale_time' => '08:25:00',
    	),
        'klpk' => array(
            'issue' => '16101601',
            'award_time' => '2017-04-07 08:31:40',
            'sale_time' => '08:20:50',
        ),
        'cqssc' => array(
            'issue' => '170630001',
            'award_time' => '2017-06-30 00:05:00',
            'sale_time' => '00:00:00',
        ),
        'gdsyxw' => array(
            'issue' => '16010101',
            'award_time' => '2016-01-01 09:10:00',
            'sale_time' => '09:00:00',
        ),
    );

	//停售时间
    private $endTime = array(
        'ssq' => array(
            'start_time' => '2005-02-09 00:00:00',
            'end_time' => '2005-02-15 00:00:00',
        ),
        'dlt' => array(
            'start_time' => '2008-02-06 00:00:00',
            'end_time' => '2008-02-12 00:00:00',
        ),
        'qxc' => array(
            'start_time' => '2005-02-09 00:00:00',
            'end_time' => '2005-02-15 00:00:00',
        ),
        'qlc' => array(
            'start_time' => '2008-02-06 00:00:00',
            'end_time' => '2008-02-12 00:00:00',
        ),
        'fc3d' => array(
            'start_time' => '2005-02-09 00:00:00',
            'end_time' => '2005-02-15 00:00:00',
        ),
        'pl3' => array(
            'start_time' => '2005-02-09 00:00:00',
            'end_time' => '2005-02-15 00:00:00',
        ),
        'pl5' => array(
            'start_time' => '2005-02-09 00:00:00',
            'end_time' => '2005-02-15 00:00:00',
        ),
    );

	//彩种规则
    private $lrule = array(
        'ssq' => array(
            'name' => '双色球',
            'issueLen' => '7',
            'rule' => array(
                '0' => '2',
                '2' => '2',
                '4' => '3',
            ),              
        ),
        'dlt' => array(
            'name' => '大乐透',
            'issueLen' => '5',
            'rule' => array(
                '1' => '2',
                '3' => '3',
                '6' => '2',
            ),
        ),
        'qxc' => array(
            'name' => '七星彩',
            'issueLen' => '5',
            'rule' => array(
                '2' => '3',
                '5' => '2',
                '0' => '2',
            ),
        ),
        'qlc' => array(
            'name' => '七乐彩',
            'issueLen' => '7',
            'rule' => array(
                '1' => '2',
                '3' => '2',
                '5' => '3',
            ),
        ),
        //每天一期
        'fc3d' => array(
            'name' => '福彩3D',
            'issueLen' => '7',
            'rule' => array(
                '0' => '1',
            ),
        ),
        'pl3' => array(
            'name' => '排列三',
            'issueLen' => '5',
            'rule' => array(
                '0' => '1',
            ),
        ),
        'pl5' => array(
            'name' => '排列五',
            'issueLen' => '5',
            'rule' => array(
                '0' => '1',
            ),
        ),
        //每天多期
        'syxw' => array(
            'name' => '老11选5',
            'issueLen' => '8',
            'rule' => array(
                '0' => '1',
            ),
        ),
    	'jxsyxw' => array(
    		'name' => '新11选5',
    		'issueLen' => '8',
    		'rule' => array(
    			'0' => '1',
    		),
    	),
    	'ks' => array(
    		'name' => '上海快三',
    		'issueLen' => '11',
    		'rule' => array(
    			'0' => '1',
    		),
    	),
    	'jlks' => array(
    		'name' => '吉林快三',
    		'issueLen' => '11',
    		'rule' => array(
    			'0' => '1',
    		),
    	),  
        'jxks' => array(
            'name' => '江西快三',
            'issueLen' => '11',
            'rule' => array(
                '0' => '1',
            ),
        ),
    	'hbsyxw' => array(
    		'name' => '惊喜11选5',
    		'issueLen' => '8',
    		'rule' => array(
    			'0' => '1',
    		),
    	),
        'klpk' => array(
            'name' => '快乐扑克',
            'issueLen' => '8',
            'rule' => array(
                '0' => '1',
            ),
        ),
        'cqssc' => array(
            'name' => '老时时彩',
            'issueLen' => '9',
            'rule' => array(
                '0' => '1',
            ),
        ),
        'gdsyxw' => array(
            'name' => '乐11选5',
            'issueLen' => '8',
            'rule' => array(
                '0' => '1',
            ),
        ),
    );

    public function __construct() {
        parent::__construct();
        $this->load->model('issue_model', 'Issue');
    }

    //主函数
    public function index()
    {
        $lottery = $this->Issue->getLotteryIssue();
        if($lottery)
        {
            foreach ($lottery as $key => $linfo) {
                $this->preIssueByType($linfo['lid']);
                $lottery = $this->Issue->updateIssueStatus($linfo['lid'],$status=1);
            }
        }
    	
    }

    /*
	 * 数据中心 -- 彩种期次预排
	 * @author:liuli
	 * @date:2015-03-27
	 */
    public function preIssueByType($type)
    {

        //读取参数
        $default = $this->default;
        //从配置表读取 预排参数
        $info = $this->Issue->getConfigInfo($type);       

        $lrule = $this->lrule;
        //$sweek = date('w',strtotime($default[$type]['award_time']));

        if(!empty($lrule[$type]))
        {
            $data = array();
            //起始期号
            $issueInfo = $this->getStartIssue($type);
            // $issueInfo = $this->Issue->getNewIssue($type);
            if(!empty($issueInfo))
            {   
                $issue = $issueInfo['issue'];
                $default_time = $issueInfo['award_time'];
            }else{
                $issue = $default[$type]['issue'];
                $default_time = $default[$type]['award_time'];
            }
			//$default_time = '2017-03-28';
            //入库
            $fields = array('issue', 'sale_time', 'end_time', 'award_time', 'synflag', 'status', 'd_synflag', 'created');
            $bdata['s_data'] = array();
            $bdata['d_data'] = array();
            $count = 0;
            $k = 0;

            //老11选5
            if(in_array($type,array('syxw', 'jxsyxw', 'hbsyxw', 'gdsyxw')))
            {
            	$periods = array('syxw' => 87, 'jxsyxw' => 84, 'hbsyxw' => 81, 'gdsyxw' => 84);
                for ($i=0; $i < $info['issue_num']; $i++) 
                {
                    $award_time = $award_time?$award_time:$default_time;

                    //上一期的日期
                    $datetime = strtotime($award_time) + 86400;
                    $ntime = date('Y-m-d',$datetime);
                    $issue_day = substr(date('Ymd',$datetime), 2);
                    //本日第一期开售时间
                    $start_time = $default[$type]['sale_time'];
                    $sale_time = '';
                    for ($j=0; $j < $periods[$type]; $j++) 
                    {
                        $issueNum = $j + 1;
                        $sale_time = $sale_time?$sale_time:$start_time;
                        //销售时间
                        $data[$k]['sale_time'] = $ntime.' '.$sale_time;
                        if(in_array($type, array('hbsyxw')))
                        {
                        	//停售时间
                        	$data[$k]['end_time'] = $ntime.' '.date('H:i:s',strtotime($sale_time)+10*60);
                        	//开奖时间
                        	$data[$k]['award_time'] = $ntime.' '.date('H:i:s',strtotime($sale_time)+11*60);
                        	$sale_time = date('H:i:s',strtotime($data[$k]['end_time']));
                        }
                        elseif(in_array($type, array('jxsyxw')))
                        {
                        	//停售时间
                        	$data[$k]['end_time'] = $ntime.' '.date('H:i:s',strtotime($sale_time) + 9*60 + 30);
                        	//开奖时间
                        	$data[$k]['award_time'] = $ntime.' '.date('H:i:s',strtotime($sale_time) + 10*60);
                        	$sale_time = date('H:i:s',strtotime($data[$k]['award_time']));
                        }
                        elseif (in_array($type, array('gdsyxw')))
                        {
                            //停售时间
                            $data[$k]['end_time'] = $ntime.' '.date('H:i:s',strtotime($sale_time) + 9*60 + 50);
                            //开奖时间
                            $data[$k]['award_time'] = $ntime.' '.date('H:i:s',strtotime($sale_time) + 10*60);
                            $sale_time = date('H:i:s',strtotime($data[$k]['award_time']));
                        }
                        else 
                        {	
                        	//停售时间
                        	$data[$k]['end_time'] = $ntime.' '.date('H:i:s',strtotime($sale_time)+9*60);
                        	//开奖时间
                        	$data[$k]['award_time'] = $ntime.' '.date('H:i:s',strtotime($sale_time)+10*60);
                        	$sale_time = date('H:i:s',strtotime($data[$k]['award_time']));
                        }
                       
                        //补全期号规则
                        $issue_num = str_pad($issueNum,2,"0",STR_PAD_LEFT);
                        $data[$k]['issue'] = $issue_day.$issue_num;
                        $award_time = $data[$k]['award_time'];                       

                        $data[$i]['synflag'] = 0; 
                        
                        array_push($bdata['s_data'], "(?, ?, ?, ?, ?, ?, ?, now())");
                        array_push($bdata['d_data'], $data[$k]['issue']);
                        array_push($bdata['d_data'], $data[$k]['sale_time']);
                        array_push($bdata['d_data'], $data[$k]['end_time']);
                        array_push($bdata['d_data'], $data[$k]['award_time']);
                        array_push($bdata['d_data'], $data[$i]['synflag']);
                        array_push($bdata['d_data'], '0');
                        array_push($bdata['d_data'], $this->config->item('web_instance_all'));

                        $k++;
                        
	                    if(++$count >= 50)
	                    {
	                        $this->Issue->insertIssue($fields, $bdata, $type);
	                        $bdata['s_data'] = array();
	                        $bdata['d_data'] = array();
	                        $count = 0;
	                    }
                    }
                }
                if(!empty($bdata['s_data']))
                {
                    $this->Issue->insertIssue($fields, $bdata, $type);
                    $bdata['s_data'] = array();
                    $bdata['d_data'] = array();
                    $count = 0;
                }   

            }
            else if(in_array($type,array('ks', 'jlks', 'jxks')))
            {
            	//快三
            	for ($i=0; $i < $info['issue_num']; $i++)
            	{
            		$award_time = $award_time?$award_time:$default_time;
	            	//上一期的日期
	            	$datetime = strtotime($award_time) + 86400;
	            	$ntime = date('Y-m-d',$datetime);
	            	$issue_day = date('Ymd',$datetime);
	            	//本日第一期开售时间
	            	$start_time = $default[$type]['sale_time'];
	            	$sale_time = '';
                        $periods = array('jlks' => 87, 'ks' => 82, 'jxks' => 84);
	            	for ($j=0; $j < $periods[$type]; $j++)
	            	{
		            	$issueNum = $j + 1;
		            	$sale_time = $sale_time?$sale_time:$start_time;
		            	//销售时间
		            	$data[$k]['sale_time'] = $ntime.' '.$sale_time;
		            	//停售时间
                                $data[$k]['end_time'] = $ntime.' '.date('H:i:s',strtotime($sale_time)+10*60);
                                //开奖时间
		            	$data[$k]['award_time'] = $ntime.' '.date('H:i:s',strtotime($sale_time)+10*60);
                                if($type == 'jlks'){
                                    $data[$k]['end_time'] = $ntime.' '.date('H:i:s',strtotime($sale_time)+9*60);
                                    $data[$k]['award_time'] = $ntime.' '.date('H:i:s',strtotime($sale_time)+9*60);
                                }
                                $sale_time = date('H:i:s',strtotime($data[$k]['award_time']));
		            	//补全期号规则
		            	$issue_num = str_pad($issueNum,3,"0",STR_PAD_LEFT);
		            	$data[$k]['issue'] = $issue_day.$issue_num;
		            	$award_time = $data[$k]['award_time'];
		            
		            	$data[$i]['synflag'] = 0;
		            
		            	array_push($bdata['s_data'], "(?, ?, ?, ?, ?, ?, ?, now())");
		            	array_push($bdata['d_data'], $data[$k]['issue']);
		            	array_push($bdata['d_data'], $data[$k]['sale_time']);
		            	array_push($bdata['d_data'], $data[$k]['end_time']);
		            	array_push($bdata['d_data'], $data[$k]['award_time']);
		            	array_push($bdata['d_data'], $data[$i]['synflag']);
		            	array_push($bdata['d_data'], '0');
		            	array_push($bdata['d_data'], $this->config->item('web_instance_all'));
		            	
		            	$k++;
	            	}
	            	if(++$count >= 50)
	            	{
	            		$this->Issue->insertIssue($fields, $bdata, $type);
	            		$bdata['s_data'] = array();
	            		$bdata['d_data'] = array();
	            		$count = 0;
	            	}
            	}
            	if(!empty($bdata['s_data']))
            	{
            		$this->Issue->insertIssue($fields, $bdata, $type);
            		$bdata['s_data'] = array();
            		$bdata['d_data'] = array();
            		$count = 0;
            	}
            }
            else if(in_array($type, array('klpk')))
            {
                // 快乐扑克
                for ($i=0; $i < $info['issue_num']; $i++)
                {
                    $award_time = $award_time ? $award_time : $default_time;
                    // 上一期的日期
                    $datetime = strtotime($award_time) + 86400;
                    $ntime = date('Y-m-d', $datetime);
                    $issue_day = substr(date('Ymd', $datetime), 2);
                    // 本日第一期开售时间
                    $start_time = $default[$type]['sale_time'];
                    $sale_time = '';
                    // 每天79期
                    for ($j=0; $j < 88; $j++)
                    {
                        $issueNum = $j + 1;
                        $sale_time = $sale_time ? $sale_time : $start_time;
                        // 销售时间
                        $data[$k]['sale_time'] = $ntime . ' ' . $sale_time;
                        // 停售时间
                        $data[$k]['end_time'] = $ntime . ' ' . date('H:i:s', strtotime($sale_time) + 9*60 + 30);
                        // 开奖时间
                        $data[$k]['award_time'] = $ntime.' '.date('H:i:s', strtotime($sale_time) + 10*60 + 50);
                        // 下一期开售时间
                        $sale_time = date('H:i:s',strtotime($data[$k]['sale_time']) + 10*60);

                        //补全期号规则
                        $issue_num = str_pad($issueNum, 2, "0", STR_PAD_LEFT);
                        $data[$k]['issue'] = $issue_day . $issue_num;
                        $award_time = $data[$k]['award_time'];
                    
                        $data[$i]['synflag'] = 0;

                        array_push($bdata['s_data'], "(?, ?, ?, ?, ?, ?, ?, now())");
                        array_push($bdata['d_data'], $data[$k]['issue']);
                        array_push($bdata['d_data'], $data[$k]['sale_time']);
                        array_push($bdata['d_data'], $data[$k]['end_time']);
                        array_push($bdata['d_data'], $data[$k]['award_time']);
                        array_push($bdata['d_data'], $data[$i]['synflag']);
                        array_push($bdata['d_data'], '0');
                        array_push($bdata['d_data'], $this->config->item('web_instance_all'));
                        $k++;
	                    if(++$count >= 50)
	                    {
	                        $this->Issue->insertIssue($fields, $bdata, $type);
	                        $bdata['s_data'] = array();
	                        $bdata['d_data'] = array();
	                        $count = 0;
	                    }
                    }
                }
                if(!empty($bdata['s_data']))
                {
                    $this->Issue->insertIssue($fields, $bdata, $type);
                    $bdata['s_data'] = array();
                    $bdata['d_data'] = array();
                    $count = 0;
                }
            }
            elseif(in_array($type, array('cqssc')))
            {
                for ($i=0; $i < $info['issue_num']; $i++) 
                {
                    $award_time = $award_time ? $award_time : $default_time;
                    // 下一期的日期
                    $oneDaySplit = ($i == 0) ? 86400 : 0;
                    $datetime = strtotime($award_time) + $oneDaySplit;
                    $ntime = date('Y-m-d', $datetime);
                    $issue_day = substr(date('Ymd', $datetime), 2);
                    // 本日第一期开售时间
                    $start_time = $default[$type]['sale_time'];
                    $sale_time = '';
                    // 每天120期 三阶段
                    
                    date_default_timezone_set('PRC');
                    for ($j = 0; $j < 120; $j++)
                    {
                        $issueNum = $j + 1;
                        $sale_time = $sale_time ? $sale_time : $start_time;
                        // 00:00-02:00 5分钟/期，10:00-22:00 10分钟/期，22:00-24:00 5分钟/期
                        if((strtotime($sale_time) >= strtotime('00:00:00') && strtotime($sale_time) <= strtotime('02:00:00')) || (strtotime($sale_time) >= strtotime('22:00:00') && strtotime($sale_time) <= strtotime('24:00:00')))
                        {
                            $split = 5;
                        }
                        else
                        {
                            $split = 10;
                        }
                        // 销售时间
                        $data[$k]['sale_time'] = $ntime . ' ' . $sale_time;
                        
                        if(intval($issueNum) == 24)
                        {
                            // 第024期特殊 销售至十点
                            $endstrtime = strtotime($ntime . ' 10:00:00');
                        }
                        else
                        {        
                            $endstrtime = strtotime($ntime.' '.date('H:i:s', strtotime($sale_time))) + $split * 60;
                        }

                        // 停售时间
                        $data[$k]['end_time'] = date('Y-m-d H:i:s', ($endstrtime));
                        // 开奖时间
                        $data[$k]['award_time'] = date('Y-m-d H:i:s', ($endstrtime));
                        
                        // 下一期开售时间
                        $sale_time = date('H:i:s',strtotime($data[$k]['sale_time']) + $split * 60);
                        if(strtotime($sale_time) >= strtotime('02:00:00') && strtotime($sale_time) <= strtotime('10:00:00'))
                        {
                            $sale_time = '10:00:00';
                        }
                        //补全期号规则
                        $issue_num = str_pad($issueNum, 3, "0", STR_PAD_LEFT);
                        $data[$k]['issue'] = $issue_day . $issue_num;
                        $award_time = $data[$k]['award_time'];
                    
                        $data[$i]['synflag'] = 0;

                        array_push($bdata['s_data'], "(?, ?, ?, ?, ?, ?, ?, now())");
                        array_push($bdata['d_data'], $data[$k]['issue']);
                        array_push($bdata['d_data'], $data[$k]['sale_time']);
                        array_push($bdata['d_data'], $data[$k]['end_time']);
                        array_push($bdata['d_data'], $data[$k]['award_time']);
                        array_push($bdata['d_data'], $data[$i]['synflag']);
                        array_push($bdata['d_data'], '0');
                        array_push($bdata['d_data'], $this->config->item('web_instance_all'));
                        $k++;
                        if(++$count >= 50)
                        {
                            $this->Issue->insertIssue($fields, $bdata, $type);
                            $bdata['s_data'] = array();
                            $bdata['d_data'] = array();
                            $count = 0;
                        }
                    }
                }
                if(!empty($bdata['s_data']))
                {
                    $this->Issue->insertIssue($fields, $bdata, $type);
                    $bdata['s_data'] = array();
                    $bdata['d_data'] = array();
                    $count = 0;
                }
            }
            else
            {
                for ($i=0; $i < $info['issue_num']; $i++) 
                {
                    $award_time = $award_time?$award_time:$default_time;

                    if(in_array($type, array('ssq','dlt','qxc','qlc')))
                    {
                        $week = date('w',strtotime($award_time)); 
                    }else{
                        //每天一期 福彩3D/排列三/排列五
                        $week = 0;
                    }
                    
                   
                    //销售时间
                    $data[$i]['sale_time'] = $this->getSaleTime($award_time,$default[$type]['sale_time'],1);
                    //停售时间
                    $data[$i]['end_time'] = $this->getAwardTime($type,$award_time,$info['award_time'],$lrule[$type]['rule'][$week],$info,$info['early_time']);
                    //开奖时间
                    $data[$i]['award_time'] = $this->getAwardTime($type,$award_time,$info['award_time'],$lrule[$type]['rule'][$week],$info);
                    $award_time = $data[$i]['award_time'];
                    //补全期号规则
                    $data[$i]['issue'] = $this->getIssue($type,$issue,$award_time);
                    $issue = $data[$i]['issue'];

                    $data[$i]['synflag'] = 0;                  

                    array_push($bdata['s_data'], "(?, ?, ?, ?, ?, ?, ?, now())");
                    array_push($bdata['d_data'], $data[$i]['issue']);
                    array_push($bdata['d_data'], $data[$i]['sale_time']);
                    array_push($bdata['d_data'], $data[$i]['end_time']);
                    array_push($bdata['d_data'], $data[$i]['award_time']);
                    array_push($bdata['d_data'], $data[$i]['synflag']);
                    array_push($bdata['d_data'], '0');
                    array_push($bdata['d_data'], $this->config->item('web_instance_all'));
                    
                    if(++$count >= 500)
                    {
                        $this->Issue->insertIssue($fields, $bdata, $type);
                        $bdata['s_data'] = array();
                        $bdata['d_data'] = array();
                        $count = 0;
                    }
                }
                if(!empty($bdata['s_data']))
                {
                    $this->Issue->insertIssue($fields, $bdata, $type);
                    $bdata['s_data'] = array();
                    $bdata['d_data'] = array();
                    $count = 0;
                }   
            }          
        }
        
        //print_r($data);
    }

    //获取销售时间
    private function getSaleTime($award_time, $sale_time, $days, $preTime='')
    {
        
        $day = date('Y-m-d',strtotime($award_time));
        $day .= ' '; 
        $day .= $sale_time; 
        $t = $days * 86400;
        $datetime = strtotime($day) + $t;
        $time = date('Y-m-d H:i:s',$datetime);
        //提前时间
        if(!empty($preTime))
        {
            $p = $preTime * 60;
            $datetime = $datetime - $p;
            $time = date('Y-m-d H:i:s',$datetime);
        }
        return $time;
    }

    //获取截止/开奖时间
    private function getAwardTime($type, $award_time, $begin_time, $days, $info, $preTime='')
    {
        
        $day = date('Y-m-d',strtotime($award_time));
        $day .= ' '; 
        $day .= $begin_time; 
        $t = $days * 86400;
        $datetime = strtotime($day) + $t;
        $time = date('Y-m-d H:i:s',$datetime);
        $Ndate = date('Y-m-d',$datetime);

        //判断停售时间
        // $endTime = $this->endTime;
        if(isset($info))
        {
            $start_date = date('Y-m-d',strtotime($info['delay_start_time']));
            $end_date = date('Y-m-d',strtotime($info['delay_end_time']));
            if( strtotime($Ndate) >= strtotime($start_date) && strtotime($Ndate) <= strtotime($end_date) )
            {
                $push_day = $end_date;
                $push_day .= ' '; 
                $push_day .= $begin_time; 
                $pt = 86400;
                $datetime = strtotime($push_day) + $pt;
                if(!in_array($type, array('fc3d','pl3','pl5')))
                {
                    //推迟后的某一天
                    $datetime = $this->getLastAward($type,$datetime);
                }               
                $time = date('Y-m-d H:i:s',$datetime);
            }
        }

        //提前时间
        if(!empty($preTime))
        {
            $p = $preTime * 60;
            $datetime = $datetime - $p;
            $time = date('Y-m-d H:i:s',$datetime);
        }
        return $time;
    }

    //获取期号
    private function getIssue($type, $issue, $award_time)
    {
        $lrule = $this->lrule;
        $issue_year = substr($issue, 0, $lrule[$type]['issueLen']-3);
        $issue_Num = substr($issue, -3);
        $award_year = substr(date('Y',strtotime($award_time)), -2);
        $year = substr($issue_year, -2);

        if( $year == $award_year )
        {
            $issue_Num++;            
        }else{
            //重新开始
            $issue_Num = 1;     //check
            $issue_year++;
        }
        $issue_Num = str_pad($issue_Num,3,"0",STR_PAD_LEFT);
        $issue_year = str_pad($issue_year,$lrule[$type]['issueLen']-3,"0",STR_PAD_LEFT);
        $issue = $issue_year.$issue_Num;
        return $issue;
    }

    //获取最近的开奖时间
    private function getLastAward($type, $ntime)
    {
    	$lrule = $this->lrule;
        for($day = 0; $day < 7; ++$day)
        {
            $stime = strtotime("+ $day day", $ntime);
            $week = date('w', $stime);
            if(!empty($lrule[$type]['rule'][$week]))
            {
            	return $stime;
            }            
        }
    }

    //获取开始期次  以当前时间为分割点
    private function getStartIssue($type)
    {
        $issueInfo = array();
        $issueInfo = $this->Issue->getNewIssue($type);
        if(!empty($issueInfo))
        {
            if($issueInfo['award_time'] > date('Y-m-d H:i:s'))
            {
                //获取离当前开奖时间最近的一期
                $issueInfo = $this->Issue->getAwardIssue($type);
            }
        }
        return $issueInfo;
    }

}
