<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Cli_Chase_Statistics extends MY_Controller
{
    public function __construct()
    {
    	parent::__construct();
    	$this->load->model('chase_model');
    	$this->load->driver('cache', array('adapter' => 'redis'));
    }
    
    /**
     * 快频彩
     */
    public function index()
    {
        $lids = array(21406, 21407, 21408, 53, 54, 55, 56, 57, 21421);
        $this->chase_model->syncStatus($lids);
        $this->chase_model->calBouns($lids, '_quick');
        $this->chaseCancel($lids);
        $this->updateFailOrder($lids);
    }
    
    /**
     * 慢频彩
     */
    public function slowStatistics()
    {
        $lids = array(33, 35, 51, 52, 10022, 23528, 23529);
        $this->chase_model->syncStatus($lids);
        $this->chase_model->calBouns($lids);
        $this->chaseCancel($lids);
        $this->updateFailOrder($lids);
    }
    
    /**
     * 追号系统撤单脚本
     */
    public function chaseCancel($lids)
    {
        $this->chase_model->chaseCancel($lids);
    }
    
    /**
     * 未付款订单状态更新
     */
    public function updateFailOrder($lids)
    {
        $this->chase_model->updateFailOrder($lids);
    }
    

}
/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */