<?php

class State_Model extends MY_Model {

    const CURRENT = 100;
    const HISTORY = 201;

    public function __construct() {
        parent::__construct();
    }

}
