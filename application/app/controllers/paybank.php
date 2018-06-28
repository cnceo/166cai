<?php
class PayBank extends MY_Controller 
{
	
	public function __construct() {
		parent::__construct();
		$this->load->model('pay_bank_model', 'PayBank');
	}
	
	public function add($token)
	{
		$params = json_decode($this->strCode(urldecode($token)), true);
		$title = '添加银行卡';
		$this->load->view('paybank/add', compact('params', 'title'));
	}
	
	public function cardlist($token)
	{
		$this->config->load('bank');
		$bankcode = $this->config->item('bank_code');
		if (empty($this->uid)) exit('访问错误！');
		$uidcode = urlencode($this->strCode(json_encode(array('uid' => $this->uid)), 'ENCODE'));
		$data = $this->PayBank->getBankList($this->uid);
		$params = json_decode($this->strCode(urldecode($token)), true);
		$platform = 'app';
		$title = '充值银行卡';
		$this->load->view('paybank/list', compact('data', 'token', 'platform', 'params', 'bankcode', 'uidcode', 'title'));
	}
	
	public function setDefault()
	{
		$bid = $this->input->post('bid');
		$res = $this->PayBank->setDefault(preg_replace('/\D+/', '', $bid), $this->uid);
		if ($res) {
			exit('success');
		}
		exit('fail');
	}
	
	public function delBank()
	{
		$postData = array(
			'bank_id' => $this->input->post('bankid'),
			'uid'	=>	$this->uid
		);
		$this->load->library('tools');
		if(ENVIRONMENT != 'production')
		{
			$orderUrl = $this->config->item('cp_host');
			$postData['HOST'] = $this->config->item('domain');
		}
		else
		{
			$orderUrl = $this->config->item('protocol') . $this->config->item('pages_url');
		}
		$responeData = $this->tools->request($orderUrl . 'api/recharge/breakPayRequest', $postData);
		$responeData = json_decode($responeData, true);
		
		$res = array(
			'status'	=> $responeData['code'],
			'msg'		=>	$responeData['msg']
		);
	
		exit(json_encode($res));
	}
	
}
