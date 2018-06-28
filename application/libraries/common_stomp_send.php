<?php
/**
 * 消息入队公用类
 * @author Administrator
 *
 */
class Common_Stomp_Send
{
	private $CI;
	public function __construct()
	{
		$this->CI = &get_instance();
		$this->CI->load->library('stomp');
		$this->CI->load->model('user_model');
	}
	
	/**
	 * 登录入队操作
	 * @param unknown $userInfo
	 * @return boolean
	 */
	public function login($userInfo)
	{
	    if(empty($userInfo['uid']))
	    {
	        return true;
	    }
	    
	    if(!(ENVIRONMENT == 'development'))
	    {
	        $lastLoginTime = $userInfo['last_login_time'];
	        $nowDate = date('Y-m-d');
	        if($lastLoginTime < $nowDate)
	        {
	            $config = $this->CI->config->item('stomp');
	            $connect = $this->CI->stomp->connect();
	            $data = array(
	                'type' => 'login',
	                'ctype' => 'growth',
	                'data' => array('uid' => $userInfo['uid'], 'value' => 5, 'overTime' => date('Y-m-d H:i:s')),
	            );
	            $error = true;
	            if($connect)
	            {
	                $res = $this->CI->stomp->push($config['queueName'], $data, array('persistent'=>'true'));
	                if($res)
	                {
	                    $error = false;
	                }
	            }
	            
	            if($error)
	            {
	                $this->CI->user_model->errorRecord(json_encode($data));
	            }
	        }
	    }
	    
	    return true;
	}
}
