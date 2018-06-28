<?php

class Bank_Model extends MY_Model {

    public function __construct() {
        parent::__construct();
    }

    public function getBanks() {
        $banks = array();

        $bankResponse = $this->tools->get($this->payApi . 'banks/query');
        if ($bankResponse['code'] == 0) {
            $banks = $bankResponse['data'];
        }

        return $banks;
    }

}
