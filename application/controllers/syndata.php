<?php

class Syndata extends MY_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('user_model');
    }

    public function index() {
        $data = $this->user_model->getAllBank();
        var_dump($data);
    }

}
