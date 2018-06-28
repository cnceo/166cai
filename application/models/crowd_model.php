<?php

class Crowd_Model extends MY_Model {

    public function __construct() {
        parent::__construct();
    }

    public function getOrders($options = array()) {
        if (empty($options['pn'])) {
            $options['pn'] = 1;
        }
        if (empty($options['ps'])) {
            $options['ps'] = 10;
        }
        $orders = array();
        $orderResponse = $this->tools->get($this->busiApi . 'ticket/order/hemailist', $options);
        if ($orderResponse['code'] == self::CODE_SUCCESS) {
            $orders = $orderResponse['data'];
        }

        return $orders;
    }

}
