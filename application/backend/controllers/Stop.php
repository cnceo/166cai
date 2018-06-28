<?php
class Stop extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->driver('cache', array('adapter' => 'redis'));
        $this->REDIS = $this->config->item('REDIS');
    }

    public function index()
    {
        $this->check_capacity("17_1");
        $act = $this->input->post('action', true);
        $state = $this->cache->get($this->REDIS['ssSelling']);
        if($act)
        {
            if( $act == 'start' )
            {
                echo $this->start('ssSelling');
                $this->syslog(28, "开启春节停售操作" );
            }
            elseif( $act == 'close')
            {
                echo $this->close('ssSelling');
                $this->syslog(28, "关闭春节停售操作" );
            }
            exit;
        }
        $this->load->view("stopselling", compact('state'));

    }
    
    public function uservpop() {
    	$this->check_capacity("12_1_1");
    	$act = $this->input->post('action', true);
    	$this->load->model('model_click');
    	$count = $this->model_click->getCount('escort');
    	$state = $this->cache->get($this->REDIS['USERVPOP']);
    	if($act)
    	{
    	    $this->check_capacity("12_1_2");
    		if( $act == 'start' )
    		{
    			echo $this->start('USERVPOP');
    			$this->syslog(43, "上架用户服务弹层" );
    		}
    		elseif( $act == 'close')
    		{
    			echo $this->close('USERVPOP');
    			$this->syslog(43, "下架用户服务弹层" );
    		}
    		exit;
    	}
    	$this->load->view("uservpop", compact('count', 'state'));
    }

    private function start($key)
    {
        $this->cache->save($this->REDIS[$key], true, 0);
        return true;
    }

    private function close($key)
    {
        $this->cache->save($this->REDIS[$key], false, 0);
        return true;
    }
}