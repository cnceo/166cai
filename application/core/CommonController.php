<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class CommonController extends CI_Controller {

	public $_version = array('v1.1', 'v1.2');
    public $uname;
    public $uid;
    public $uinfo;
    public $bankInfo;
    protected $cookie;
    public $is_ajax = 0;
	
    public function __construct() {
        parent::__construct();
        header("Content-type: text/html; charset=utf-8");
        $this->load->library('primarySession');
        $this->load->library('tools');
        $this->load->helper('string');
        $this->is_ajax = $this->input->is_ajax_request();
        define('DOMAIN', $this->config->item('domain'));
        define('UCIP', $this->get_client_ip());
        define('REFE', $_SERVER["HTTP_REFERER"]);
        $this->recordChannel();
        $this->initializeUser();
    }

    private function initializeUser()
    {
        $cookie = $this->getLoginInfo();
        $this->cookie = $cookie;
        if (!empty($cookie['u']))
        {
            $this->uid = $cookie['u'];
            $this->uname = $cookie['n'];
            //用户信息获得
            $this->load->model('user_model');
            $this->uinfo = $this->user_model->getUserInfo($this->uid);
            //银行卡信息获取
            $this->bankInfo = $this->user_model->getBankInfo($this->uid);
        }
    }
    
    private function getLoginInfo()
    {
    	$cookie = $this->input->cookie('I');
    	$loginInfo = array();
    	//查看用户是否需要完善信息
    	$loginInfo['need_modify_name'] = $this->input->cookie('need_modify_name');
    	if ($loginInfo['need_modify_name']) {
    		$this->FreshCookie('need_modify_name', $loginInfo['need_modify_name']);
    	}
    	if (!empty($cookie))
    	{
    		$infos = explode('&', $cookie);
    		foreach ($infos as $info)
    		{
    			$info = explode("=", $info);
    			if ($info[0] == 'n')
    			{
    				$loginInfo[$info[0]] = urldecode($info[1]);
    			}
    			else
    			{
    				$loginInfo[$info[0]] = $info[1];
    			}
    		}
    		if ($this->calcSessionId( $loginInfo['i'] , $loginInfo['u'] , $loginInfo['n'] , $loginInfo['t'] ) == $loginInfo['s'])
    		{
    			//判断是否第三方登录
    			$own = !empty($loginInfo['o']) ? true : false;
    			//刷新cookie
    			$this->SetCookie($loginInfo['n'], $loginInfo['i'], $loginInfo['u'], $loginInfo['m'], $loginInfo['t'], $loginInfo['s'], $own);
    			return $loginInfo;
    		}
    		else 
    		{
    			log_message('LOG', $this->calcSessionId( $loginInfo['i'] , $loginInfo['u'] , $loginInfo['n'] , $loginInfo['t'] ) . 
    			"{$loginInfo['i']} , {$loginInfo['u']} , {$loginInfo['n']} , {$loginInfo['t']} == {$loginInfo['s']}", 'cookie');
    		}
    	}
    	return false;
    }
    
    // 计算用户中心cookie中的s值
    public function calcSessionId( $i, $u, $n, $t )
    {
        if(ENVIRONMENT === 'production')
        {
            $LOGIN_SESSION_KEY = "00a6c9fb8f5c6d708dde2225b35bec84";
        }
        else
        {
            $LOGIN_SESSION_KEY = "testSessionKey";
        }  
    	return md5($i . $u . $n . $t . $LOGIN_SESSION_KEY);
    }
    
    protected function FreshCookie($name, $value, $exp_ratio='void')
    {
    	$exp_ratios = array(
    			'session' => 0,
    	);
    	$expire = key_exists($exp_ratio, $exp_ratios) ? $exp_ratios[$exp_ratio] : $this->config->item('cookie_expire');
    	$domain = str_replace('www.', '', $this->config->item('domain'));
    	$cval = array(
    			'name' => $name,
    			'value' => $value,
    			'expire' => $expire * 60,
    			'domain' => $domain,
    			'path' => '/',
    			'prefix' => '',
    			'secure' => false
    	);
    	if (in_array($name, array('I')))
    		$cval['httponly'] = true;
    	$this->input->set_cookie($cval);
    }
    
    /**
     * 设置cookie
     * @param unknown_type $uname
     * @param unknown_type $passid
     * @param unknown_type $uid
     * @param unknown_type $mod
     * @param unknown_type $loginTime
     * @param unknown_type $sid
     * @param unknown_type $own
     */
    protected function SetCookie($uname, $passid, $uid, $mod, $loginTime, $sid, $own=true)
    {
    	$cvals = array(
    			'name_ie' => $uname,
    			'I' => "i={$passid}&u={$uid}&n=" . urlencode($uname) . "&m={$mod}&t={$loginTime}&s={$sid}&v=1.1"
    			);
    	if ($own) {
    		$cvals['I'] .= "&o=1";
    	}
    	foreach ($cvals as $name => $cval)
    	{
    		$this->FreshCookie($name, $cval);
    	}
    }

    /**
     * 渠道初始化
     */
    public function recordChannel()
    {
        $this->primarysession->startSession();
        $this->load->model('channel_model', 'channel');
        $channelId = $this->input->get('cpk', true);
        if ($this->channel->isValidChannelId($channelId))
        {
            $channelId = $this->channel->getValidChannelId($channelId);
            $this->primarysession->setArg('channelId', $channelId);
        }
        else
        {
            $sessionChannel = $this->primarysession->getArg('channelId');
            if (empty($sessionChannel))
            {
                $channelId = $this->channel->defaultChannelId();
                $this->primarysession->setArg('channelId', $channelId);
            }
        }
    }

    /**
     * 获得渠道id
     */
    public function getChannelId()
    {
        $this->primarysession->startSession();

        return $this->primarysession->getArg('channelId');
    }
    
    protected function pre_decrypt()
    {
    	$encrypt = $this->input->post('encrypt');
    	if(!empty($encrypt))
    	{
    		$fields = explode('|', $encrypt);
    		foreach ($fields as $field)
    		{
    			if(preg_match("/$field\|/", $encrypt))
    			{
    				$values = $this->input->post($field, true);
    				if(!empty($values))
    				{
    					$_POST[$field] = '';
    					$decrypt = '';
    					$values = explode(' ', $values);
    					foreach ($values as $value)
    					{
    						$decrypt .= trim($this->tools->rsa_decrypt($value, true));
    					}
    					if(!empty($decrypt))
    					{
    						$decrypts = explode('<PSALT>', $decrypt);
    						$_POST[$field] = $decrypts[0];
    						$dsec = intval(substr(time(), -5)) - intval($this->tools->rsa_decrypt($decrypts[1]));
    					}
    				}
    			}
    		}
    	}
    }
    
    protected function cre_pubkey()
    {
    	$sec = substr(time(), -5);
    	$this->pub_salt = $this->tools->rsa_encrypt($sec);
    }
    
    // 所需绑定信息是否完全
    public function isBindForRecharge() {
    	$userBaseInfo = $this->uinfo;
    	if ($userBaseInfo['phone'] && $userBaseInfo['id_card'])
    	{
    		return true;
    	} 
    	else 
    	{
    		return false;
    	}
    }
    
    // 判断账号是否被冻结
    public function isFreeze() {
    	// 未登录
    	if (empty($this->uid)) {
    		return false;
    	}
    
    	if($this->uinfo['userStatus'] == '2')
        {
        	return true;
        }
        else
        {
        	return false;
        }
    }
    
    /**
     * ajax 加载页面
     * @param unknown_type $file
     * @param unknown_type $data
     * @param unknown_type $version
     */
    protected function ajaxDisplay($file, $data=array(), $version = null)
    {
    	if(in_array($version, $this->_version))
    	{
    		return $this->load->view($version.'/'.$file, $data, true);
    	}
    	else
    	{
    		return $this->load->view($file, $data, true);
    	}
    }
    
    protected function checkCaptcha($captcha, $phone, $position)
    {
    	$code = $this->primarysession->getArg($position);
    	$codestr = explode(':', $code);
    
    	if (($codestr[1] > time()) && (strtolower($captcha) == strtolower($codestr[0])) && ($phone === $codestr[3])) {
    		$this->primarysession->setArg($position, '');
    		return false;
    	}
    
    	if (time() < $codestr[2]) {//60秒以内
    		if (empty($codestr[4])) {
    			$codestr[4] = 0;
    		}
    		$codestr[4]++;
    		if ($codestr[4] <= 2) {
    			$this->primarysession->setArg($position, implode(':', $codestr));
    		}else {
    			$this->primarysession->setArg($position, '');
    			return 2;
    		}
    	}else {
    		$this->primarysession->setArg($position, '');
    		return 2;
    	}
    	return true;
    }
    
	public function get_client_ip()
	{
	    //代理IP白名单
	    $allowProxys = array(
	        '42.62.31.40',
	        '172.16.0.40',
	    );
	    $onlineip = $_SERVER['REMOTE_ADDR'];
	    if (in_array($onlineip, $allowProxys))
	    {
	        $ips = $_SERVER['HTTP_X_FORWARDED_FOR'];
	        if ($ips)
	        {
	            $ips = explode(",", $ips);
	            $curIP = array_pop($ips);
	            $onlineip = trim($curIP);
	        }
	    }
	    if (filter_var($onlineip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4))
	    {
	        return $onlineip;
	    }
	    else
	    {
	        return '0.0.0.0';
	    }
	}
}