<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Api_Model extends MY_Model
{
	public function __construct()
	{
		parent::__construct();
	}
	
	public function RefreshSalt()
	{
		$nsalt = $this->input->post(null, true);
		$result = false;
		if($nsalt['mid'] == 'CP')
		{
			$csalt = $this->db->query('select content from cp_secret where cid = 1')->getOne();
			if(!empty($csalt))
			{
				$csalt = unserialize($csalt);
				$token = md5($nsalt['mid'].$nsalt['new_token'].$nsalt['dateline'].$csalt['new_token']);
				if($token == $nsalt['token'])
				{
					$result = $this->db->query('update cp_secret set content = ? where cid = 1', array(serialize($nsalt)));
				}
			}
			else 
			{
				$result = $this->db->query('update cp_secret set content = ? where cid = 1', array(serialize($nsalt)));
			}
		}
		if($result)
		{
			echo '1|succ';
		}
		else
		{
			echo '0|fail';
			$url = $this->config->item('rcg_salt');
			$this->load->model('wallet_model');
	    	$this->wallet_model->InitSalt();
	    	$PostData['mid'] = 'CP';
	    	$result = $this->tools->request($url, $PostData);
	    	log_message('LOG', "refresh token: $result", 'SYS');
		}
	}
	
	public function RefreshOrder()
	{
		$orders = $this->input->post(null, true);
		if(!empty($orders))
		{
			$this->load->model('wallet_model');
			$salt = $this->wallet_model->GetSalt();
			$token = md5("{$orders['trade_no']}{$orders['status']}{$orders['dateline']}$salt");
			if($orders['status'] == 1 && ($token == $orders['old_token'] || $token == $orders['new_token']))
			{
				$real_amount = 0;
				if(!empty($orders['real_amount']))
					$real_amount = intval($orders['real_amount']);
				$re = $this->wallet_model->recharge($orders['trade_no'], $real_amount);
				if($re)
				{
					echo '1|succ';
				}
				else 
				{
					echo '0|err';
					log_message('LOG', "recharge error: ".print_r($orders, true), 'SYS');
				}
			}
		}
	}
	
	public function CPOrder()
	{
		$orders = $GLOBALS['HTTP_RAW_POST_DATA'];
		$re = false;
		if(!empty($orders))
		{
			$datetime = date('Y-m-d H:i:s');
			$this->load->model('order_model');
			$response = json_decode($orders, true);
	    	$response = $this->tools->decrypt($response['encryptStr']);
	    	$response = json_decode($response, true);
	    	$bdata = array('uid' => $response['uid'], 'userName' => $response['userName'],
	    	'bonus' => ParseUnit($response['bonus']), 'margin' => ParseUnit($response['margin']),
	    	'eachAmount' => ParseUnit($response['eachAmount']), 'channel' => $response['channel'], 
	    	'codecc' => $response['codecc'], 'status' => $response['status'], 'qsFlag' => $response['qsFlag'],
	    	'orderId' => $response['orderCode'], 'ticket_time' => $datetime, 'win_time' => $datetime);
	    	if($response['status'] > 40)
	    	{
	    		$re = true;
	    	}
	    	else 
	    	{
	    		$re = $this->order_model->SaveOrder('notify', $bdata);
	    	}
		}
		if($re)
			echo 0;
		else
		{
			echo 1;
			log_message('LOG', "push OrderInfo error: ".print_r($response, true), 'SYS');
		}
	}

	/**
     * 获取世界杯赛程信息
     *
     * @return array
     */
    public function getWorldCupCourse()
    {
        $sql = 'select * from cp_worldcup_course order by number';
        $info = $this->slave->query($sql)->getAll();
        return $info;
    }
}
