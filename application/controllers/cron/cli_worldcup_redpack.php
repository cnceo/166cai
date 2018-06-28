<?php
class Cli_Worldcup_redpack extends MY_Controller
{
    
    public function __construct() {
        parent::__construct();
        $this->load->model('redpack/model_hongbaoworldcup');
    }
    
    public function index() {
        set_time_limit(0);
        $this->model_hongbaoworldcup->countWordcupUsers();
    }
    
    public function createRedpack() {
        $this->model_hongbaoworldcup->createRedpack();
    }
    
    public function sendAll() {
        set_time_limit(0);
        $this->model_hongbaoworldcup->sendAll();
    }
    
    public function sendSms() {
        set_time_limit(0);
        $this->model_hongbaoworldcup->sendSms();
    }
    
    public function getTotalMoney() {
        $this->model_hongbaoworldcup->getTotalMoney();
    }
}