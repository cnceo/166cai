<?php

/**
 * 走势图
 * @Author liuli
 */
class Chart extends MY_Controller 
{

    public function __construct() 
    {
        parent::__construct();
    }

    public function index() 
    {
        $this->displayMore('chart/index',array('htype' => 1),'v1.1');
    }

    public function header()
    {
        $this->load->model('award_model');
        $awardInfo = array();
        $awardData = $this->award_model->getCurrentAward();
        foreach ($awardData as $items)
        {
            $awardInfo[$items['seLotid']] = $items;
        }
        $data['dltPool'] = floor($awardInfo['23529']['awardPool'] / 100000000);
        $this->load->view('v1.1/chart/header',$data);
    }
    
    public function header2()
    {
    	$this->redirect('http://', 'location', 301);
    }

    public function footer()
    {
    	$this->load->view('v1.1/chart/footer',array());
    }
    
    public function footer2()
    {
    	$this->redirect('http://', 'location', 301);
    }
}
