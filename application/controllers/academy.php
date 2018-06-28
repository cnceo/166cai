<?php

class Academy extends MY_Controller {

    public function __construct() {
       parent::__construct();
    }

    public function index() {
        $clickTimes = $this->newClickTimes();
        $this->displayMore('academy/index',
            array(
                'htype' => 1,
                'clickTimes' => $clickTimes
            )
       ,"v1.1" );
    }
    //计算点击次数
    public function countClick()
    {
        $this->CI = &get_instance();
        $this->load->driver('cache', array('adapter' => 'redis'));
        $order = $this->input->post('order', true);
        $REDIS = $this->CI->config->item('REDIS');
        if ( ! $clickNums = unserialize($this->cache->get($REDIS["CLICK_NUM"])))
        {
            $clickNums = $this->clickTimes();
            $clickNums[$order] = $clickNums[$order]+1;
            $this->cache->save($REDIS["CLICK_NUM"], serialize($clickNums), 0);
        }
        else
        {
            $clickNums[$order] = $clickNums[$order]+1;
            $this->cache->save($REDIS["CLICK_NUM"], serialize($clickNums), 0);
        }
    }
    //从数据库获取某个点击次数
    public function clickTimes($order=null)
    {
        $this->load->model('model_countclick', 'count');
        if($order)
        {
            $row = $this->count->getClicks($order);
            $clickTimes = (int)$row['clickTimes'];
            return $clickTimes;
        }
        else
        {
            $row = $this->count->getClicks();
            foreach ($row as $key => $value)
            {
                $clickTimes[] = $value['clickTimes'];
            }
            return $clickTimes;
        }
    }
    //读取点击次数
    public function newClickTimes()
    {
        $this->CI = &get_instance();
        $this->load->driver('cache', array('adapter' => 'redis'));
        $REDIS = $this->CI->config->item('REDIS');
        if ( ! $clickNums = unserialize($this->cache->get($REDIS["CLICK_NUM"])))
        {
            $clickNums = $this->clickTimes();
            $this->cache->save($REDIS["CLICK_NUM"], $clickNums, 0);
        }
        return $clickNums; 
    }
    
    public function reWriteRedis()
    {
        $uid =  $this->input->get('uid', true);
        $uname = $this->input->get('uname',true);
        if($uid == "14959313" && $uname == "donguuuu")
        {
            $clickTimes = array(
                '0' => '12190',
                '1' => '4849',
                '2' => '864',
                '3' => '2386',
                '4' => '1020',
                '5' => '5876',
                '6' => '54742',
                '7' => '8333',
                '8' => '4780',
                '9' => '40922',
                '10' => '3968',
                '11' => '338',
            	'12' => '851'
            );
            $this->load->model('model_countclick', 'count');
            $this->count->reWriteRedis($clickTimes);
        }
    }
}
