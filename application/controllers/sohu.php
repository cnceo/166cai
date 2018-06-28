<?php

class Sohu extends MY_Controller {
		
	public function index() {
		$this->display('sohu/index');	
	}

    public function error() {
        $this->display('sohu/error');
    }
	
}