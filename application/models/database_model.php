<?php
class DataBase_model extends CI_Model 
{
	public function __construct()
	{
		parent::__construct();
	    $this->load->database('one');
	}
	  
	public function get_news($slug = FALSE)
	{
	  if ($slug === FALSE)
	  {
	  	$query =  $this->db->query('select * from user')->result_array();
	    //$query = $this->db->get('user');
	    print_r($query);
	    //return $query->result_array();
	  }
	  /*$query = $this->db->get_where('news', array('slug' => $slug));
	  return $query->row_array();*/
	}
}