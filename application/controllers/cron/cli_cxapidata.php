<?php
class Cli_CxApiData extends MY_Controller {

    public function __construct() 
    {
        parent::__construct();
    }

    public function index($stime=0) 
    {
    	while (true)
    	{
    		$con = $this->router->class;
            $this->controlRun($con);
    		$this->setCache();
    	}
    }
    
    //已废弃
    private function setCache()
    {
    	$REDIS = $this->config->item('REDIS');
		$OUTTIME = $this->config->item('OUTTIME');
		$this->load->driver('cache', array('adapter' => 'redis'));
		$cx_api_sets = $this->cache->redis->sMembers($REDIS['CX_API_SETS']);
		if(!empty($cx_api_sets))
		{
			$this->load->library('tools');
			foreach ($cx_api_sets as $cx_api_set)
			{
				$datas = $this->cache->redis->get($ukey);
				if(!empty($datas) && !$datas['code'])
				{
					continue;					
				}
				$url = $this->cache->redis->hGet($REDIS['CX_API'], $cx_api_set);
				$params = $this->cache->redis->hGet($REDIS['CX_API_PARAMS'], $cx_api_set);
				$datas = $this->tools->_get($url, unserialize($params));
				if(!empty($datas) && !$datas['code'])
				{
					$OUTTIME = $this->config->item('OUTTIME');
					$tails = explode(':', $cx_api_set);
					$this->cache->redis->save($cx_api_set, $datas, $OUTTIME['cx_data'] * $tails[2]);
				}
		    	if (empty($datas)) 
		    	{
		            $datas = array(
		                'code' => -9999,
		                'msg' => '',
		                'data' => array(),
		            );
		        }
		        if($datas['code'] != 0)
		        {
		        	log_message('LOG', serialize($datas), 'CXAPI');
		        }
			}
		}
    }
}
