<?php
class oacheck 
{
	private $CI;
	private $_clientId = "4_62ipy8mmbv0o84ww8s04ggkwgo0c8080oo0ok8sks4sgswko40";
	private $_clientSecret = "idrwur73drkss80o840048k4w0480kkowww44kgwgscgcksco";
	private $_redirectUri;
	private $_baseUri = 'https://work.km.com/';
	
	public $_systemId = 22;

    public function __construct()
    {
        $this->CI = &get_instance();
        $this->CI->load->library(array('primarySession'));
        $this->CI->primarysession->startSession();
        $url_prefix = $this->CI->config->item('url_prefix');
        $this->url_prefix = isset($url_prefix[$this->CI->config->item('base_url')]) ? $url_prefix[$this->CI->config->item('base_url')] : 'http';
        $this->_redirectUri = $this->url_prefix . "://" . $this->CI->config->item('base_url')."/backend/main/callback";
    }
	
	public function index()
	{
		if ($this->CI->primarysession->getArg('oa_auth_token') && $this->CI->primarysession->getArg('oa_auth_userinfo') 
				&& $this->CI->primarysession->getArg('oa_auth_ip') && $this->CI->primarysession->getArg('oa_auth_at')) {
			//登录IP和当前IP必须一致
			if ($this->CI->primarysession->getArg('oa_auth_ip') != $_SERVER['REMOTE_ADDR']) {
				$this->auth();
				exit;
			}
			//认证5分钟后重新认证,确认身份是否失效,时间可自定义(不超过两小时)
			if ((time() - $this->CI->primarysession->getArg('oa_auth_at')) > 60 * 5) {
				$token = $this->CI->primarysession->getArg('oa_auth_token');
				$userinfo = $this->api("user/info", $token['access_token']);
				if ($userinfo && in_array($this->_systemId, $userinfo['auths'])) {
					$this->CI->primarysession->setArg('oa_auth_userinfo', $userinfo);
					$this->CI->primarysession->setArg('oa_auth_at', time());
				} else {
					$this->auth();
					exit;
				}
			}
		} else {
			$this->auth();
		}
	}
	
	public function auth()
	{
		//设置防csrf字符串
		$this->CI->primarysession->setArg('oa_auth_state', md5(microtime(true) . mt_rand(100000, 999999)));
		echo "<script type='text/javascript'>parent.location.href='".$this->generateUrl($this->CI->primarysession->getArg('oa_auth_state'))."';</script>";
		exit();
// 		header("Location: " . $this->generateUrl($this->CI->primarysession->getArg('oa_auth_state')));
	}
	
	private function generateUrl($state, $scope = '')
    {
        $params = sprintf('response_type=code&client_id=%s&redirect_uri=%s&state=%s&scope=%s', $this->_clientId, $this->_redirectUri, $state, $scope);
        return "{$this->_baseUri}oauth/v2/auth?$params";
    }

    public function getToken($code)
    {
        $params = sprintf('grant_type=authorization_code&client_id=%s&client_secret=%s&redirect_uri=%s&code=%s', $this->_clientId, $this->_clientSecret, $this->_redirectUri, $code);
        try {
            $res = file_get_contents($this->_baseUri."oauth/v2/token?$params");
            if ($res) {
                return json_decode($res, true);
            } else {
                return false;
            }
        } catch (Exception $e) {
            return false;
        }
    }

    public function api($uri, $token, $method = 'GET')
    {
        try {
            $res = $this->get($this->_baseUri."api/$uri", $token);
            if ($res) {
                return json_decode($res, true);
            } else {
                return false;
            }
        } catch (Exception $e) {
            return false;
        }
    }
    
    public function get($url, $token)
    {
    	$ch = curl_init($url);
    	curl_setopt($ch, CURLOPT_HEADER, 0);
    	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
    	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    	curl_setopt($ch, CURLOPT_HTTPHEADER, array("AUTHORIZATION:Bearer {$token}"));
    	$output = curl_exec($ch);
    	curl_close($ch);
    	return $output;
    }
}