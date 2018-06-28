<?php

class Weihu extends MY_Controller {

    public function index() 
    {
        $this->load->view ('v1.2/weihu/index');
        //$this->display('weihu/index', array(), 'v1.2');
    }
}
