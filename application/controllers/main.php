<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Main extends MY_Controller {

	private $_lidMap = array(
		SSQ => array('cache' => 'SSQ_ISSUE'),
		FCSD => array('cache' => 'FC3D_ISSUE'),
		PLS => array('cache' => 'PLS_ISSUE'),
		PLW => array('cache' => 'PLW_ISSUE'),
		QXC => array('cache' => 'QXC_ISSUE'),
		QLC => array('cache' => 'QLC_ISSUE'),
		DLT => array('cache' => 'DLT_ISSUE'),
		SFC => array('cache' => 'SFC_ISSUE'),
		RJ => array('cache' => 'RJ_ISSUE'),
		SYXW => array('cache' => 'SYXW_ISSUE_TZ'),
	);
	
    /**
     * 彩票后台入口控制器
     */
    public function __construct() {
        parent::__construct();
    }
    
    public function index() {
    	$this->isMobile();
    	$this->load->view('v1.1/static/main', array());
    }

    /**
     * 注册欢迎页
     */
    public function welcome() 
    {
    	if(!$this->uid)
    	{
    		$this->redirect('/');
    	}
    	$this->load->library('libredpack');
    	$hasActivity = $this->libredpack->hongbao166('hasAttend', array('phone' => $this->uinfo['phone']));
    	$data['hongbao'] = false;
    	if($hasActivity && empty($this->uinfo['id_card']))
    	{
    		$data['hongbao'] = true;
    	}
        $this->displayShortHeader('main/welcome', $data, 'v1.1');
    }

    //权限限制
    private function restriction($entrance)
    {
        $visitor_ips = array(
            'loginReCall' => array(),
            'RecgErr' => array(),
            'RefreshSalt' => array(),
            'RefreshOrder' => array(),
            'CPOrder' => array(),
        );
    }

    public function ReCallApi($fun) {
        $this->restriction($fun);
        if (method_exists($this, $fun)) {
            $this->$fun();
        } else {
            $this->load->model('api_model');
            if (method_exists($this->api_model, $fun)) {
                $this->api_model->$fun();
            }
        }
    }

    private function RecgErr() {
        echo 'PaySucc';
    }
    
    /**
     * 登录方法
     * @param unknown_type $permit
     */
	public function login($permit = '')
    {
    	if(in_array($permit, array('part')) || $this->uid)
    	{
    		$this->loginout();
    	}
        $data['headTitle'] = '欢迎登录';
        $this->displayShort('main/login', $data, 'v1.1');
    }

    /**
     * 注册页面
     */
	public function register()
    {
        $this->redirect('/weihu', 'location', 301);
    	$from = $this->input->get('from');
        if ($this->uid)
        {
            $this->redirect('/');
            exit;
        }
        $data['headTitle'] = '欢迎注册';
        $data['from'] = $from;
        $this->displayShort('main/register', $data, 'v1.1');
    }

    // 验证码是否一致
    private function isCaptchaPass() {
		
        if ($this->primarysession->getArg('loginError') >= 3 || $this->input->cookie('needCaptcha')) {
            $this->FreshCookie('needCaptcha', true);
            $captcha = $this->input->post('captcha', true);
            if ($this->primarysession->getArg('captcha') != strtolower($captcha)) {
                return false;
            }
        }
        return true;
    }

    public function loginout() {
        $this->load->helper('cookie');
        $url = $this->input->post('barUrl', true);
        $domain = str_replace('www.', '', $this->config->item('domain'));
        delete_cookie('I', $domain, '/');
        delete_cookie('name_ie', $domain, '/');
        delete_cookie('need_modify_name', $domain, '/');
//        $this->redirect($url);
    }

    private function ucheck($uname) {
        // 小于2个字符, 或字符超出给定范围
        if (!preg_match('/^[\d\w_\p{Han}]+$|^\w+[\w]*@[\w]+\.[\w]+$/u', $uname)) {
            return false;
        }

        $gbkUname = iconv('UTF-8', 'GBK', $uname);
        if (strlen($gbkUname) > 24 || strlen($gbkUname) < 2) {
            return false;
        }
        return true;
    }
    
    /**
     * 服务协议页面
     */
    public function serviceAgreement()
    {
    	$this->displayMore('main/serviceAgreement', array(), 'v1.1');	
    }
    
    public function IniSalt() 
    {
        $url = $this->config->item('rcg_salt');
        $PostData = array();
    	if(ENVIRONMENT != 'production')
        {
            $postData['HOST'] = 'pay.2345.com';
        }
        $this->load->model('wallet_model');
        $this->wallet_model->InitSalt();
        $PostData['mid'] = 'CP';
        $result = $this->tools->request($url, $PostData);
        print_r($result);
    }
    
    public function refresh($uid=0)
    {
    	$this->load->model('user_model');
    	$this->user_model->refresh(0, array($uid));
    }
    
    public function inicache()
    {
    	$REDIS = $this->config->item('REDIS');
		$this->load->driver('cache', array('adapter' => 'redis'));
		$cx_api_sets = $this->cache->redis->sMembers($REDIS['CX_API_SETS']);
		if(!empty($cx_api_sets))
		{
			foreach ($cx_api_sets as $cx_api_set)
			{
				$this->cache->redis->sRemove($REDIS['CX_API_SETS'], $cx_api_set);
				$this->cache->redis->hDel($REDIS['CX_API'], $cx_api_set);
				$this->cache->redis->hDel($REDIS['CX_API_PARAMS'], $cx_api_set);
			}
		}
    }
    
    public function getCliTime()
    {
    	$REDIS = $this->config->item('REDIS');
    	$this->load->driver('cache', array('adapter' => 'redis'));	
    	//$this->cache->redis->save($REDIS['ORDERS_CHECK_START_TIME'], '21321312', 0);
    	$datatime = $this->cache->redis->get($REDIS['ORDERS_CHECK_START_TIME']);
    	print_r(date('Y-m-d H:i:s', $datatime));
    }

    /*
     * 新手帮助模块 刷新用户登录信息
     * @author:liuli
     * @date:2014-12-30
     */
    public function refreshNewUser(){
        $res = array();
        $res['status'] = '01';
        $res['msg'] = 'failed';
        if($this->uid){
            $this->load->model('user_model');
            $this->user_model->freshUserInfo($this->uid);
            $res = array();
            $res['status'] = '00';
            $res['msg'] = 'success';
        }
        echo json_encode($res);
        exit;       
    }
    
    /**
     * 参    数：无
     * 作    者：shigx
     * 功    能：收据用户手机号码
     * 修改日期：2015.03.17
     */
    public function savePhone()
    {
    	$codestr = $this->primarysession->getArg('newphoneyzm');
    	header('Content-type: application/json');
    	$res = array(
    			'status' => '1',
    			'msg' => '系统异常',
    	);
    	$vdata = $this->input->post(null, true);
    	if(empty($vdata['phone']) || !preg_match("/^1[34578][0-9]{9}$/",$vdata['phone']))
    	{
    		$res = array(
    			'status' => '2',
    			'msg' => '手机号码输入错误',
    		);
    	}
    	else
    	{
    		$codestr = explode(':', $codestr);
    		$this->reCallVoice($codestr);
    		
    		if ( (empty($vdata['newphoneyzm']) || $codestr[1] < time() || $vdata['newphoneyzm'] != $codestr[0]))
            {
    			$res = array(
    					'status' => '3',
    					'msg' => '验证码不正确',
    			);
    		}
    		else
    		{
    			$this->load->model('Tips_model');
    			$result = $this->Tips_model->savePhone($vdata['phone']);
    			if($result)
    			{
    				$this->primarysession->setArg('newphoneyzm', '');
    				$res = array(
    						'status' => '0',
    						'msg' => '操作成功',
    				);
    			}
    		}
    	}
    	
    	echo json_encode($res);
        exit;
    }
    public function temp()
    {
        $this->load->view("v1.1/temp");
    }
    
    //清空用户必须消费金额
    public function cleanMust() 
    {
        $this->load->model('user_model');
        $this->user_model->cleanMust();
    }
}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */