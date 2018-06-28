<?php

class Home extends MY_Controller {

    public function __construct() {
        parent::__construct();
        //$this->load->library('webapi');
        $this->load->library('money');

        $this->load->model('wallet_model', 'Wallet');
        $this->load->model('crowd_model', 'Crowd');
        $this->load->model('cms_model', 'Cms');
    }

    public function index() {
        $account = array();
        if ($this->session->userdata('uid')) {
            $account = $this->Wallet->getWallet($this->uid, $this->token);
        }
        $crowds = $this->Crowd->getOrders(array(
            'ps' => 9
        ));
        if (!empty($crowds['items'])) {
            $crowds = $crowds['items'];
        } else {
            $crowds = array();
        }
        $data = array(
            'baseUrl' => $this->config->item('base_url'),
            'pagesUrl' => $this->config->item('pages_url'),
            'account' => $account,
            'crowds' => $crowds,
            'lotteryNames' => $this->Lottery->getCnNames(),
        );
        $this->display('home/index', $data);
    }

}
