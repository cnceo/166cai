<?php
class index extends My_Controller
{
	public function __construct() 
	{
		parent::__construct();
	}
	
	public function index()
	{
		
	}

	public function login()
	{
		$name = $this->input->post ( 'name' );
		$pass = $this->input->post ( 'pass' );
		$this->cre_pubkey ();
		if ($name && $pass)
		{
			$this->decrypt($pass);
			//验证用户
			$this->load->model ( 'cp_partner', 'partner' );
			$res = $this->partner->checkUser ( $name, $pass );
			if ($res)
			{
				$this->session->set_userdata ( array (
						'pid' => $res ['id'],
						'pname' => $res ['name'] 
				) );
				$this->_pid = $this->session->userdata ( 'pid' );
				$this->_pname = $this->session->userdata ( 'pname' );
				$this->redirect ( $this->config->item ( 'base_url' ) . '/shop' );
			} else
			{
				echo '<script>alert(\'用户名密码错误！\')</script>';
				$this->load->view ( 'index/login' );
			}
		} else
		{
			$this->load->view ( 'index/login' );
		}
	}

	public function logout()
	{
		$this->_pname = $this->_pid = null;
		
		$this->session->unset_userdata ( 'pid' );
		$this->session->unset_userdata ( 'pname' );
		$this->redirect ( $this->config->item ( 'base_url' ) . '/index/login' );
	}
	
	//解密
	private function decrypt (&$pass)
	{
		$decrypt = '';
		$passArr = explode ( ' ', $pass );
		foreach ( $passArr as $ps )
		{
			$decrypt .= trim ( $this->tools->rsa_decrypt ( $ps, true ) );
		}
		if (! empty ( $decrypt ))
		{
			$decrypts = explode ( '<PSALT>', $decrypt );
			$pass = $decrypts [0];
		}
	}
}