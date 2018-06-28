<?php

class Account_Model extends MY_Model {

    public function __construct() {
        parent::__construct();
    }

    //已废弃
    public function getAccount($uid, $token) {
        $account = array();

        $accountResponse = $this->tools->get($this->passApi . 'query/accountinfo.do', array(
            'uid' => $uid,
            'token' => $token,
        ));
        if ($accountResponse['code'] == self::CODE_SUCCESS) {
            $account = $accountResponse['data'];
        }

        return $account;
    }

}
