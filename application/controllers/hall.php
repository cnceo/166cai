<?php

class Hall extends MY_Controller {

    public function __construct() {
       parent::__construct();
    }

    public function index() 
    {
        $this->displayMore('hall/index', 
            array(
                'htype'=>1,
            ),'v1.1'
        );
    }

}
