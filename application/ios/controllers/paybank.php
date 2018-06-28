<?php
class PayBank extends MY_Controller 
{
		
	public function __construct() {
		parent::__construct();
		$this->load->model('pay_bank_model', 'PayBank');
	}
	
	public function add($token)
	{
		$bankid = $this->input->post('bank_id', true);
		$sign = $this->input->get('sign', true);
		$params = json_decode($this->strCode(urldecode($token)), true);
		$uidcode = urlencode($this->strCode(json_encode(array('uid' => $params['uid'])), 'ENCODE'));
		if (!empty($bankid)) {
			$params['bank_id'] = $bankid;
			if ($params['change_bankid']) unset($params['change_bankid']);
			$token = urlencode($this->strCode(json_encode($params), 'ENCODE'));
			header("Location: /ios/wallet/safariPay?token=".$token."&sign=".$sign);
		}
		$title = '添加银行卡';
		$this->load->view('paybank/add', compact('token', 'sign', 'params', 'uidcode', 'title'));
	}
	
	public function cardlist($token)
	{
		$sign = $this->input->get('sign', true);
		$bankid = $this->input->post('bank_id', true);
		$pay_agreement_id = $this->input->post('pay_agreement_id', true);
		$params = json_decode($this->strCode(urldecode($token)), true);
		$uidcode = urlencode($this->strCode(json_encode(array('uid' => $params['uid'])), 'ENCODE'));
		unset($params['change_bankid']);
		if (!empty($bankid) || !empty($pay_agreement_id)) 
		{
			if (!empty($pay_agreement_id)) {
				$params['pay_agreement_id'] = $pay_agreement_id;
			}elseif (!empty($bankid)) {
				$params['bank_id'] = $bankid;
			}
			
			$token = urlencode($this->strCode(json_encode($params), 'ENCODE'));
			header("Location: /ios/wallet/safariPay?token=".$token."&sign=".$sign);
		}
		$this->config->load('bank');
		$bankcode = $this->config->item('bank_code');
		
		$data = $this->PayBank->getBankList($params['uid']);
		
		$platform = 'ios';
		$title = '充值银行卡';
		$this->load->view('paybank/list', compact('data', 'token', 'platform', 'params', 'bankcode', 'sign', 'uidcode', 'title'));
	}
	
	public function setDefault($token)
	{
		$params = json_decode($this->strCode(urldecode($token)), true);
		$bid = $this->input->post('bid');
		$res = $this->PayBank->setDefault(preg_replace('/\D+/', '', $bid), $params['uid']);
		if ($res) {
			exit('success');
		}
		exit('fail');
	}
	
	public function delBank($token)
	{
		$params = json_decode($this->strCode(urldecode($token)), true);
		$postData = array(
			'bank_id' => $this->input->post('bankid'),
			'uid'	=>	$params['uid']
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