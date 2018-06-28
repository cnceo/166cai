<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Cli_Check_Cache extends MY_Controller 
{
	public function __construct()
	{
		parent::__construct();
        $this->load->model('cache_model');
        $this->load->model('lottery_cache_model', 'lotteryCache');
        $this->load->model('task_model');
        $this->load->driver('cache', array('adapter' => 'redis'));
        $this->REDIS = $this->config->item('REDIS');
        $this->filepath = APPPATH . '/logs/plock';
	}

	public function index()
	{
		while(true)
		{
			if(file_exists("{$this->filepath}/cli_check_cache.start"))
			{
				if(file_exists("{$this->filepath}/cli_check_cache.stop"))
					unlink("{$this->filepath}/cli_check_cache.stop");
				if(file_exists("{$this->filepath}/cli_check_cache.start"))
					unlink("{$this->filepath}/cli_check_cache.start");
			}
			$date = date('H:i:s');
			if(file_exists("{$this->filepath}/cli_check_cache.stop") || ($date >= '02:00:00' && $date <= '07:50:00'))
			{
				//凌晨两点-7：50时间段运行直接退出
				die();
			}
			
			//便于测试环境的调试
        	if(ENVIRONMENT==='development')
        	{
        		$lidMap = $this->cache_model->getCheckCacheType();
        	}else{
        		$lidMap = $this->cache_model->getByCron(array('model' => 'cache_model', 'func' => 'getCheckCacheType'));
        	}
			foreach ($lidMap as $lid => $value)
			{
				if(!empty($value['method']))
				{
					if(ENVIRONMENT==='development')
        			{
        				$this->checkCache($value['cache'], $lid, $value['method']);
        			}else{
        				$croname = "cron/cli_check_cache checkCache/{$value['cache']}/$lid/{$value['method']}";
	    				system("{$this->php_path} {$this->cmd_path} $croname",  $status);
        			}
				}
			}
			sleep(1);
		}
	}
	
	public function checkCache($cachekey, $lid, $method)
	{
		$cache = $this->cache->get($this->REDIS[$cachekey]);
		$cache = json_decode($cache, TRUE);
		if(!isset($cache['cIssue']['seFsendtime']) || ($cache['cIssue']['seFsendtime'] / 1000 <= time()))
		{
			$this->cache_model->$method($lid);
			/*添加刷新追号投注的缓存刷新*/
			$this->lotteryCache->run($lid);
            if(in_array($lid, array(56, 57))) $this->task_model->updateStop(9, $lid, 0);
			//换期后触发抓取开奖号码任务
			$this->task_model->updateStop(11, $lid, 0);
		}
	}
}
