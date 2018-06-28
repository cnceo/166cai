<?php

class Account extends MY_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('account_model', 'Account');
        $this->load->model('wallet_model', 'Wallet');
        $this->load->model('Notice_Model');
    }

    public function index() {
        $this->display('account/index', array(
            'account' => $this->Account->getAccount($this->uid, $this->token),
            'wallet' => $this->Wallet->getWallet($this->uid, $this->token),
            'topBanner' => 'account',
            'token' => $this->token,
            'uid' => $this->uid,
        ));
    }

    public function bindBank() {
        
        $this->load->model('bank_model', 'Bank');

        $this->display('account/bind_bank', array(
            'banks' => $this->Bank->getBanks(),
            'topBanner' => 'account',
        ));
    }

    public function bindAlipay() {
        $this->display('account/bind_alipay');
    }

    public function withdraw() {
        $this->load->model('bank_model', 'Bank');
        $this->load->model('wallet_model', 'Wallet');
        $this->load->model('bind_model', 'Bind');

        $this->display('account/withdraw', array(
            'banks' => $this->Bank->getBanks(),
            'wallet' => $this->Wallet->getWallet($this->uid, $this->token),
            'binds' => $this->Bind->getBinds($this->uid, $this->token),
            'topBanner' => 'account',
        ));
    }

    public function recharge() {
        $this->display('account/recharge', array(
            'topBanner' => 'account',
        ));
    }

}
