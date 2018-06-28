<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Cli_Dismantle_Table extends MY_Controller 
{

    public function __construct() 
    {
        parent::__construct();
    }
    
    public function index()
    {
    	$this->load->model('dismantle_table_model', 'DTM');
    	$this->DTM->dis_order_split();
    	$this->DTM->dis_order_split(53);
    	$this->DTM->dis_order_split(21406);
    	$this->DTM->dis_order_split(21407);
    	$this->DTM->dis_order_split(21408);
    	$this->DTM->dis_order_split(21421);
        $this->DTM->dis_order_split(54);
        $this->DTM->dis_order_split(55);
        $this->DTM->dis_order_split(56);
        $this->DTM->dis_order_split(57);
    	$this->DTM->dis_orders_ori();
		$this->DTM->dis_orders();
    	$this->DTM->dis_wallet_logs();
        $this->DTM->dis_redpack_log();
        $this->DTM->dis_order_email_log();
    }
    
	public function dis_tables_gp($flag = false)
    {
    	$this->load->model('dismantle_table_model', 'DTM');
    	$this->DTM->dis_tables_gp($flag);
    }
}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */