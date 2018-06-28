<?php

/*
 * APP 帮助中心
 * @date:2015-05-21
 */

class Help extends MY_Controller {
	
    public function __construct() 
    {
        parent::__construct();
    }

    /*
     * APP 帮助中心首页
     * @date:2015-05-21
     */
    public function index()
    {
        $this->load->view('help/index');
    }

    /*
     * APP 帮助中心详情
     * @date:2015-05-21
     */
    public function detail()
    {
        $this->load->view('help/detail');
    }

    /*
     * APP 帮助中心 - 彩种玩法
     * @date:2015-05-21
     */
	public function play($lid)
    {
        $this->load->view('help/play_' . ParseLname($lid));
    }

    /*
     * APP 帮助中心 - 委托投注协议
     * @date:2015-05-21
     */
    public function agreement()
    {
        $this->load->view('help/agreement');
    }

    /*
     * APP 帮助中心 - 彩种出票时间
     * @date:2015-05-21
     */
    public function ticketTime($lid)
    {
        $this->load->view('help/ticket_' . $lid);
    }
    
    public function hemai()
    {
    	$this->load->view('help/hemai');
    }

    public function miss()
    {
        $this->load->view('help/miss');
    }

    public function gendan()
    {
    	$this->load->view('help/gendan');
    }

    // 玩法介绍 改传lid
    public function introduce($lid)
    {
        $this->load->model('lottery_model', 'Lottery');
        $lname = $this->Lottery->getEnName($lid);
        $this->load->view('help/play_' . $lname);
    }
}