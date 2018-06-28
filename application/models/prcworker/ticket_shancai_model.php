<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

include_once dirname(__FILE__) . '/ticket_model_base.php';
class ticket_shancai_model extends ticket_model_base
{
    protected $seller = 'shancai';
	public function __construct()
	{
		parent::__construct();
	}
}
