<?php

class Bind_Model extends MY_Model {

    public function __construct() {
        parent::__construct();
    }

    public function getBinds($uid, $token) {
        $binds = array();

        $bindResponse = $this->tools->get($this->payApi . 'payBinding/query', array(
            'uid' => $uid,
            'token' => $token,
        ));
        if ($bindResponse['code'] == 0) {
            $binds = $bindResponse['data'];
        }

        return $binds;
    }

}
