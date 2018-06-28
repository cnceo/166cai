<?php

class Bills extends MY_Controller {

    public function  __construct() {
        parent::__construct();
    }

    public function index() {
        $this->load->model('account_model', 'Account');
        $this->load->model('wallet_model', 'Wallet');

        $this->display('bills/index', array(
            'account' => $this->Account->getAccount($this->uid, $this->token),
            'wallet' => $this->Wallet->getWallet($this->uid, $this->token),
            'topBanner' => 'account',
            'token' => $this->token,
        ));
    }

    public function page() {
        $this->load->model('bill_model', 'Bill');

        $ps = 15;
        $recent = $this->input->get('recent');
        $pn = $this->input->get('pn');
        if (empty($pn)) {
            $pn = 1;
        }
        if (empty($recent)) {
            $recent = 7;
        }
        $endtime = time() . '000';
        $starttime = strtotime("-{$recent} day") . '000';
        $bills = $this->tools->get($this->payApi . 'transaction/query', array(
            'token' => $this->token,
            'uid' => $this->uid,
            'ps' => $ps,
            'pn' => $pn,
            'startTime' => $starttime,
            'endTime' => $endtime,
        ));
        if (empty($bills['data'])) {
            $bills['data'] = array(
                'totalPage' => 0,
                'currentPageNo' => 1,
                'transList' => array(),
            );
        }

        $this->load->view('bills/page', array(
            'bills' => $bills,
            'billTypes' => Bill_Model::$TYPES,
        ));
    }

}
