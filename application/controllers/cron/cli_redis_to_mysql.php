<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Cli_Redis_To_Mysql extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->CI = &get_instance();
        $this->REDIS = $this->CI->config->item('REDIS');
        $this->load->driver('cache', array('adapter' => 'redis'));
    }
    
    
    public function index()
    {
        $this->countdaily();
    }
    
    //原先彩票学院逻辑已废弃移到这里
    public function count() {
    	$this->load->model('model_countclick', 'count');
    	if ($clickTimes = unserialize($this->cache->get($this->REDIS["CLICK_NUM"])))
    	{
    		foreach ($clickTimes as $id=>$value)
    		{
    			$this->count->writeToDB($id+1,$value);
    		}
    	}
    }
    
    public function countdaily() {
    	$this->load->model('model_countclick_daily', 'model');
    	if ($clickTimes = unserialize($this->cache->get($this->REDIS["CLICK_COUNT"])))
    	{
    		foreach ($clickTimes as $type => &$value)
    		{
    			$this->model->writeToDB($type, $value);
    			$value = 0;
    		}
    		$this->cache->save($this->REDIS["CLICK_COUNT"], $clickTimes, 0);
    	}
    }
}
