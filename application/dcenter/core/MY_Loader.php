<?php
class MY_Loader extends CI_Loader {
	
	public function __construct()
	{
		parent::__construct();
		
		$this->_ci_model_paths = array(APPPATH, '../application/');
		$this->_ci_library_paths = array(APPPATH, '../application/', BASEPATH);
		$this->_ci_view_paths = array(APPPATH.'views/'	=> TRUE, '../application/views/'	=> TRUE);
	}
	
}