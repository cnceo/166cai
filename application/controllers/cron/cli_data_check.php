<?php
/**
 * 对账文件拉取比对操作类
 */
defined('BASEPATH') OR exit('No direct script access allowed');

class Cli_Data_Check extends MY_Controller
{
	public function __construct()
    {
        parent::__construct();
        $this->load->model('other_data_check_model', 'dataModel');
    }

    public function index()
    {
    	$configData = array();
        //执行拉取对账信息
        $configs = $this->dataModel->getCheckConfig();
        foreach ($configs as $config)
        {
        	$configData[$config['id']] = $config;
        	//已经执行到昨天时跳过
        	if($config['exc_date'] == date('Y-m-d', strtotime("-1 day")))
        	{
        		continue;
        	}
        	
        	//未到执行时间跳过
        	if($config['exc_time'] > date('H:i:s'))
        	{
        		continue;
        	}
        	$this->load->library("dataCheck/{$config['lib_name']}");
        	$class = strtolower($config['lib_name']);
	        $this->$class->exec($config); 
        }
        
        //需要重新拉取
        $datas = array();
        //出票重拉查询
        $result = $this->dataModel->getTotalSplitRflag();
        foreach ($result as $value)
        {
        	$datas[] = $value;
        }
        //充值重拉查询
        $result = $this->dataModel->getTotalRechargeRflag();
        foreach ($result as $value)
        {
        	$datas[] = $value;
        }
        //提现重拉查询
        $result = $this->dataModel->getTotalWithdrawRflag();
        foreach ($result as $value)
        {
        	$datas[] = $value;
        }
        
        foreach ($datas as $data)
        {
        	$config = $configData[$data['config_id']];
        	if(empty($config))
        	{
        		continue;
        	}
        	
        	$config['exc_date'] = date('Y-m-d', strtotime($data['date']) - 86400);
        	$this->load->library("dataCheck/{$config['lib_name']}");
        	$class = strtolower($config['lib_name']);
        	$this->$class->exec($config, 1);
        }
    }
}