<?php
class Cron_Tools extends CI_Controller 
{
	public function __construct()
	{	
		parent::__construct();
		if(!$this->input->is_cli_request())
		{
			exit('�����������ִ��!');
		}
	}
	
  	public function message($to = 'World')
  	{
    	echo "Hello {$to}!".PHP_EOL;
  	}
}