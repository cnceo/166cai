<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');
date_default_timezone_set('PRC');
class MY_Controller extends CI_Controller {

    public $uname;
    public $uid;
    public $uinfo;
    public $is_ajax = 0;
    public $con;
    public $act;
    protected $cookie;
    public $safe_level;
	public $pub_salt;
	
	public $cmd_path;
	public $php_path;
	public $app_path;
	public $log_path;
	
    public function __construct() {
        parent::__construct();
        $this->load->helper('string');
        $this->load->driver('cache', array('adapter' => 'redis'));
        $this->REDIS = $this->config->item('REDIS');
        $this->con = $this->router->class;
        $this->act = $this->router->method;
        $this->cmd_path = $this->config->item('cmd_path');
        $this->php_path = $this->config->item('php_path');
        $this->app_path = $this->config->item('app_path');
        $this->log_path = $this->app_path . 'logs';
        $this->load->config('scheme');
        $this->db_config = $this->config->item('db_config'); 
        if(preg_match('/^cli_/i', $this->con))
        {
         	if ($this->input->is_cli_request()) 
         	{
	            $this->controlRun($this->con);
	            $this->load->library('processlock');
	            $dislock = $this->disLock($this->act);
	            $param = $this->con . '-' . $this->act.(isset($this->router->uri->segments[4]) ? '-' . $this->router->uri->segments[4] : '');
	            if (!$this->processlock->getLock($param, $dislock)) 
	            {
	                log_message('LOG', "This file({$this->con}) is running!", 'LOCK');
	                die("This file({$this->con}) is running!");
	            }
        	}
        	else 
        	{
        		$this->redirect('/error');
        		die('不能从浏览器访问！');
        	}
        }
        elseif(php_sapi_name () != 'cli')
        {
            $this->_browserRequest();
        }
    }

    /**
     * [rechargeTypeList 支付列表  1_3微信,1_4支付宝,1_1快捷, 1_5网银,1_6信用卡]
     * @author LiKangJian 2017-06-22
     * @param  string $orderId   [description]
     * @param  string $orderType [description]
     * @return [type]            [description]
     */
    public function rechargeTypeList($orderId='',$orderType='')
    {
        $REDIS = $this->config->item('REDIS');
        $guides = json_decode($this->cache->hGet($REDIS['PC_PAY_GUIDE_CONFIG'],'1'),true);
        $betRedpack = intval($this->input->get('betRedpack', true));
        if($orderId !='' && $orderType !=='')
        {
            if(empty($betRedpack))
            {
                $arr = array(
                              '1_4'=>array('url'=>'/wallet/directPay/alipay?orderId='.$orderId.'&orderType='.$orderType,'name'=>'支付宝支付','guide'=> isset($guides['1_4'])?$guides['1_4']:''),
                              '1_5'=>array('url'=>'/wallet/directPay/bank?orderId='.$orderId.'&orderType='.$orderType,'name'=>'网上银行','guide'=> isset($guides['1_5'])?$guides['1_5']:''),
                              '1_1'=>array('url'=>'/wallet/directPay/kuaijie?orderId='.$orderId.'&orderType='.$orderType,'name'=>'快捷支付','guide'=> isset($guides['1_1'])?$guides['1_1']:''),
                              '1_6'=>array('url'=>'/wallet/directPay/credit?orderId='.$orderId.'&orderType='.$orderType,'name'=>'信用卡','guide'=> isset($guides['1_6'])?$guides['1_6']:''),
                              '1_7'=>array('url'=>'/wallet/directPay/yinlian?orderId='.$orderId.'&orderType='.$orderType,'name'=>'银联云闪付','guide'=> isset($guides['1_7'])?$guides['1_7']:''),
                              '1_3'=>array('url'=>'/wallet/directPay/weixin?orderId='.$orderId.'&orderType='.$orderType,'name'=>'微信支付','guide'=> isset($guides['1_3'])?$guides['1_3']:''),
                              '1_8'=>array('url'=>'/wallet/directPay/jd?orderId='.$orderId.'&orderType='.$orderType,'name'=>'京东支付','guide'=> isset($guides['1_8'])?$guides['1_8']:''),
                            );
            }else{
                $arr = array(
                              '1_4'=>array('url'=>'/wallet/directPay/alipay?orderId='.$orderId.'&orderType='.$orderType.'&betRedpack='.$betRedpack,'name'=>'支付宝支付','guide'=> isset($guides['1_4'])?$guides['1_4']:''),
                              '1_5'=>array('url'=>'/wallet/directPay/bank?orderId='.$orderId.'&orderType='.$orderType.'&betRedpack='.$betRedpack,'name'=>'网上银行','guide'=> isset($guides['1_5'])?$guides['1_5']:''),
                              '1_1'=>array('url'=>'/wallet/directPay/kuaijie?orderId='.$orderId.'&orderType='.$orderType.'&betRedpack='.$betRedpack,'name'=>'快捷支付','guide'=> isset($guides['1_1'])?$guides['1_1']:''),
                              '1_6'=>array('url'=>'/wallet/directPay/credit?orderId='.$orderId.'&orderType='.$orderType.'&betRedpack='.$betRedpack,'name'=>'信用卡','guide'=> isset($guides['1_6'])?$guides['1_6']:''),
                              '1_7'=>array('url'=>'/wallet/directPay/yinlian?orderId='.$orderId.'&orderType='.$orderType.'&betRedpack='.$betRedpack,'name'=>'银联云闪付','guide'=> isset($guides['1_7'])?$guides['1_7']:''),
                              '1_3'=>array('url'=>'/wallet/directPay/weixin?orderId='.$orderId.'&orderType='.$orderType.'&betRedpack='.$betRedpack,'name'=>'微信支付','guide'=> isset($guides['1_3'])?$guides['1_3']:''),
                              '1_8'=>array('url'=>'/wallet/directPay/jd?orderId='.$orderId.'&orderType='.$orderType.'&betRedpack='.$betRedpack,'name'=>'京东支付','guide'=> isset($guides['1_8'])?$guides['1_8']:''),
                            );                
            }

        }else{
          $arr = array(
                     '1_4'=>array('url'=>'/wallet/recharge/alipay','name'=>'支付宝支付','guide'=> isset($guides['1_4'])?$guides['1_4']:''),
                     '1_5'=>array('url'=>'/wallet/recharge/bank','name'=>'网上银行','guide'=> isset($guides['1_5'])?$guides['1_5']:''),
                     '1_1'=>array('url'=>'/wallet/recharge/kuaijie','name'=>'快捷支付','guide'=> isset($guides['1_1'])?$guides['1_1']:''),
                     '1_6'=>array('url'=>'/wallet/recharge/credit','name'=>'信用卡','guide'=> isset($guides['1_6'])?$guides['1_6']:''),
                     '1_7'=>array('url'=>'/wallet/recharge/yinlian','name'=>'银联云闪付','guide'=> isset($guides['1_7'])?$guides['1_7']:''),
                     '1_3'=>array('url'=>'/wallet/recharge/weixin','name'=>'微信支付','guide'=> isset($guides['1_3'])?$guides['1_3']:''),
                     '1_8'=>array('url'=>'/wallet/recharge/jd','name'=>'京东支付','guide'=> isset($guides['1_8'])?$guides['1_8']:''),
                    );          
        }
        if(in_array($_SERVER['SERVER_ADDR'],array('120.132.33.194','123.59.105.39'))){
            $sorts = json_decode($this->cache->hGet($REDIS['CS_PC_PAY_CONFIG'],'1'),true);
        }else{
            $sorts = json_decode($this->cache->hGet($REDIS['PC_PAY_CONFIG'],'1'),true);
        }
        if(empty($sorts)){
            return $arr;
        }
        $newArr = array();
        foreach ($sorts as $sort){
            $newArr[$sort] = $arr[$sort];
        }
        return $newArr;
    }
    /**
     * [getPayBaseInfo 获取支付页面数据]
     * @author LiKangJian 2017-06-23
     * @param  [type] $cache [description]
     * @param  [type] $ctype [description]
     * @return [type]        [description]
     */
    public function getPayBaseInfo($cache,$ctype,$is_recharge=1,$params = array())
    {
        $res = array();
        $res['mode_str'] = '1_'.$ctype;//当前支付标识
        $res['pay_list'] =count($params)? $this->rechargeTypeList($params['orderId'],$params['orderType']):$this->rechargeTypeList();
        $res['is_show'] = isset($cache[$ctype]) ? 1 : 0;
        $res = array_merge($res,getRechargeInfo($ctype,$is_recharge,$params)); 
        if(!isset($cache[$ctype])) return $res;
        $res['pay_way'] = arraySortByKey($cache[$ctype], 'weight');
        return $res;
    }
    /**
     * [getPayCache 获取]
     * @author LiKangJian 2017-06-22
     * @return [type] [description]
     */
    public function getPayCache()
    {
        $REDIS = $this->config->item('REDIS');
        if(in_array($_SERVER['SERVER_ADDR'],array('120.132.33.194','123.59.105.39'))){
            $data = json_decode($this->cache->hGet($REDIS['CS_RCG_DISPATCH'],'1'),true);
        }else{
            $data = json_decode($this->cache->hGet($REDIS['RCG_DISPATCH'],'1'),true);
        }

        $arr = array();
        foreach ($data as $k => $v) 
        {   
            $new_arr = array();
            foreach ($v as $k1 => $v1) 
            {
                $temp = json_decode($v1['params'],true);
                $way = explode(',', $temp['mode']);
                $mode_str = explode(',', $temp['mode_str']);
                
                foreach ($way as $k2 => $v2) 
                {
                    $arr1 = array();
                    $k3 = $mode_str[$k2].'/'.$v2;
                    $arr1 = $temp;
                    $arr1['mode'] = $v2;
                    $arr1['mode_str'] = $mode_str[$k2];
                    $arr1['pay_type'] = $v1['pay_type'];
                    $arr1['configId'] = $v1['id'];
                    $arr1['weight'] = $v1['weight'];
                    $arr1['img_src'] = isset($arr1['img_src']) ? $arr1['img_src'] : '' ;
                    $arr1['img_alt'] = isset($arr1['img_alt']) ? $arr1['img_alt'] : '' ;
                    $arr1['img_w'] = isset($arr1['img_w']) ? $arr1['img_w'] : '' ;
                    $arr1['img_h'] = isset($arr1['img_h']) ? $arr1['img_h'] : '' ;
                    array_push($new_arr, $arr1);
                }
                
            }
            $data[$k] = $new_arr;
            //对网银和信用卡处理
            if($k==5)
            {
                foreach ($data[$k] as $key => $value) 
                {
                    if($value['mode_str'] == '1_6')
                    {
                        $data[6] =array($data[$k][$key]);
                        unset($data[$k][$key]);
                    }
                }
               $data[$k] = array_values($data[$k]); 
            }
        }
        return $data;
    }
    /**
     * [checkRealName 验证是否实名登记]
     * @author LiKangJian 2017-07-19
     * @param  [type] $uid [description]
     * @return [type]      [description]
     */
    public function checkRealName($uid)
    {
        $this->load->model('red_pack_model');
        return  $this->red_pack_model->checkRealName($uid);
    }
    /**
     * [isLogout 账户是否注销]
     * @author LiKangJian 2017-06-15
     * @return boolean [description]
     */
    public function isLogout()
    {
        if(empty($this->uinfo)) $this->redirect('/main/login');
        if(isset($this->uinfo['userStatus']) && $this->uinfo['userStatus'] == '1')
        {
            $this->redirect('/main/login');
        }
    }
    private function _browserRequest() {
    	//临时跳转
    	if(strpos($_SERVER['HTTP_HOST'], '166cai.com') !== false)
    	{
            $url_prefix = $this->config->item('url_prefix');
            $this->redirect($url_prefix['www.166cai.com'] . '://www.166cai.com/ndomain/index.html', 'location', '301');
            die();
    	}
    	if(strpos($_SERVER['HTTP_HOST'], 'www.166cai.cn') !== false)
    	{
    	    $notredirectArr = array(
    	        '/activity/mobile',
    	    );
    	    $urlArr = parse_url($_SERVER['REQUEST_URI']);
    	    if (!in_array($urlArr['path'], $notredirectArr)) {
    	        $url_prefix = $this->config->item('url_prefix');
    	        $this->redirect($url_prefix['www.166cai.com'] . '://www.166cai.cn/ndomain/indexs.html', 'location', '301');
    	        die();
    	    }
    	}
        header("Content-type: text/html; charset=utf-8");
        $this->redirectHTTPS();
        define('DOMAIN', $this->config->item('domain'));
        define('UCIP', $this->get_client_ip());
        define('REFE', $_SERVER["HTTP_REFERER"]);
        $this->load->library('primarySession');
        $this->recordChannel();
        $this->is_ajax = $is_ajax = $this->input->is_ajax_request();
        $this->load->library('tools');
        //加密功能
        $this->pre_decrypt();
        $this->cre_pubkey();
        $this->load->helper('url');
        $this->config->load('seo');
        if(!method_exists($this, $this->act))
        {
        	$rdctArr = $this->config->item('redirect');
        	$currentUrl = str_replace(site_url(), '', current_url());
        	foreach ($rdctArr as $rdct) 
        	{
        		if (preg_match($rdct['from'], $currentUrl) == 1) 
        		{
        			$this->redirect($rdct['to'], 'location', '301');
        		}
        	}
        	return true;
        }
        $cookie = $this->getLoginInfo();
        $this->cookie = $cookie;
        if (!empty($cookie['u'])) {
        	
            $this->uid = $cookie['u'];
            $this->uname = $cookie['n'];
            //用户信息获得
            $this->load->model('user_model');
            $this->uinfo = $this->user_model->getUserInfo($this->uid);
            //银行卡信息获取
            $this->bankInfo = $this->user_model->getBankInfo($this->uid);
            $this->safe_level = $this->safetyLevel();
        }
        
        $this->CheckLogged();

        $this->load->model('lottery_model', 'Lottery');
        $this->load->model('state_model', 'State');

        //$this->load->library('webapi');
        

        $this->baseUrl = $this->config->item('base_url');
        $this->passApi = $this->config->item('pass_api');
        $this->payApi = $this->config->item('pay_api');
        $this->pagesUrl = $this->config->item('pages_url');

        $this->payUrl = $this->config->item('pay_url');
        $this->fileUrl = $this->config->item('file_url');
        //SEO @Author liusijia
		
		
    }
    
    private function redirectHTTPS()
    {
    	if($_SERVER["HTTPS"] != 'on')
        {
        	$ie67 = preg_match('/MSIE\s+[6-7].*/is', $_SERVER["HTTP_USER_AGENT"]);
        	if($ie67 || preg_match('/header|footer.*/is', $this->act) || ENVIRONMENT == 'development')
        	{
	        	return ;
        	}
        	else
        	{
        		$currentUrl = 'https://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
        		$this->redirect($currentUrl, 'location', '301');
        	}
        } 
    }

    public function recordChannel() {
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

    public function getChannelId()
    {
        $this->primarysession->startSession();

        return $this->primarysession->getArg('channelId');
    }

    private function _getData($data=array()) {
        if (empty($data['title'])) {
            $data['title'] = '166彩票';
        }
        $data['baseUrl'] = $this->config->item('base_url');
        $data['pagesUrl'] = $this->config->item('pages_url');
        $data['isLogin'] = !empty($this->uid);
        $data['passUrl'] = $this->passApi;
        $data['payUrl'] = $this->payApi;
        $data['avatarUrl'] = $this->config->item('avatar_url');
        $data['fileUrl'] = $this->fileUrl;

        // 是否绑定了 身份证, 手机
        $data['showBind'] = false;
        if (!empty($this->uinfo) && !$this->isBindForRecharge())
        {
            $data['showBind'] = true;
        }

        $data['showBindBank'] = !empty($this->bankInfo) ? false : true;

        $data['is_bank_bind'] = $this->bankInfo?1:0;
        $data['is_phone_bind'] = $this->uinfo['phone'];
        $data['is_id_bind'] = $this->uinfo['id_card'];
        $data['is_pay_pwd'] = !empty($this->uinfo['pay_pwd']);
        $data['uinfo'] = $this->uinfo;

        // 控制是否显示 信息绑定 弹层
        if (!isset($data['isNeedShowBindId'])) {
            $data['isNeedShowBindId'] = true;
        }
        
        $lotteryConfig = $this->cache->get($this->REDIS['LOTTERY_CONFIG']);
        $data['lotteryConfig'] = json_decode($lotteryConfig, true);
        $data['uservpop'] = $this->cache->get($this->REDIS['USERVPOP']);
        $this->load->model('award_model');
        $awardInfo = array();
        $awardData = $this->award_model->getCurrentAward();
        foreach ($awardData as $items)
        {
            $awardInfo[$items['seLotid']] = $items;
        }
        $data['dltPool'] = floor($awardInfo['23529']['awardPool'] / 100000000);
        return $data;
    }

    public function displayMore($file, $data=array(), $version = null) {
        $data = $this->_getData($data);
        $data['htype'] = empty($data['htype']) ? 0 : $data['htype'];
        if ($version)
        {
        	$this->load->view($version.'/elements/common/header', $data);
        	$this->displayBanner($version, $data);
        	$this->load->view($version.'/'.$file, $data);
        	$this->load->view($version.'/elements/common/footer', $data);
        	$this->displaySpring();
        }else {
        	$this->load->view('elements/common/header', $data);
        	$this->load->view($file, $data);
        	$this->load->view('elements/common/footer', $data);
        }
    }

    public function displayLess($file, $data=array()) {
        $data = $this->_getData($data);
        $data['htype'] = empty($data['htype']) ? 0 : $data['htype'];
        $this->displayBanner('v1.1', $data, 'ycfc');
        $this->load->view($file, $data);
    }

    // ajax处理
    public function displayAjaxLess($file, $data=array()) {
        $data = $this->_getData($data);
        return $this->load->view($file, $data, true);
    }

    public function display($file, $data=array(), $version = null) {
        $data = $this->_getData($data);
        $data['htype'] = empty($data['htype']) ? 0 : $data['htype'];
        if ($version)
        {
        	$this->load->view($version.'/elements/common/header', $data);
        	$this->displayBanner($version, $data);
        	$this->displayBanner($version, $data, 'chase');
        	$this->load->view($version.'/'.$file, $data);
        	$this->load->view($version.'/elements/common/footer_mid', $data);
        	$this->displaySpring();
        }
        else 
        {
        	$this->load->view('elements/common/header', $data);
        	$this->load->view($file, $data);
        	$this->load->view('elements/common/footer_mid', $data);
        }
    }
    /**
     * [rechageView 充值视图]
     * @author LiKangJian 2017-06-15
     * @param  [type] $file    [description]
     * @param  array  $data    [description]
     * @param  [type] $version [description]
     * @return [type]          [description]
     */
    public function rechageView($file, $data=array(), $version)
    {
        $this->load->view($version.'/elements/common/header_recharge', $data);
        $this->load->view($version.'/'.$file, $data);
        $this->load->view($version.'/elements/common/footer_recharge', $data);
    }
    /**
     * [rechageView 收银台]
     * @author LiKangJian 2017-06-15
     * @param  [type] $file    [description]
     * @param  array  $data    [description]
     * @param  [type] $version [description]
     * @return [type]          [description]
     */
    public function cashierView($file, $data=array(), $version)
    {
        $this->load->view($version.'/elements/common/header_cashier', $data);
        $this->load->view($version.'/'.$file, $data);
        $this->load->view($version.'/elements/common/footer_recharge', $data);
    } 
    public function memberView($file, $data=array(), $version)
    {
        $data = $this->_getData($data);
        $this->load->view($version.'/elements/common/header_member', $data);
        $this->load->view($version.'/'.$file, $data);
        $this->load->view($version.'/elements/common/footer_member', $data);
    }
    /**
     * 简短头尾调用
     * @param unknown_type $file
     * @param unknown_type $data
     * @param unknown_type $version
     */
    public function displayShort($file, $data=array(), $version = null)
    {
    	$data = $this->_getData($data);
    	if ($version)
    	{
    		$this->load->view($version.'/elements/common/header_notlogin', $data);
    		$this->displayBanner($version, $data, 'ycfc');
    		$this->load->view($version.'/'.$file, $data);
    		$this->load->view($version.'/elements/common/footer_short', $data);
    	}else
    	{
    		$this->load->view('elements/common/header_notlogin', $data);
    		$this->load->view($file, $data);
    		$this->load->view('elements/common/footer_short', $data);
    	}
    }
    
    /**
     * 简短头尾调用
     * @param unknown_type $file
     * @param unknown_type $data
     * @param unknown_type $version
     */
    public function displayShortIndex($file, $data=array(), $version = null)
    {
    	$data = $this->_getData($data);
    	if ($version)
    	{
    		$this->load->view($version.'/elements/common/header_index', $data);
    		$this->load->view($version.'/'.$file, $data);
    		$this->load->view($version.'/elements/common/footer_short', $data);
    	}else
    	{
    		$this->load->view('elements/common/header_index', $data);
    		$this->load->view($file, $data);
    		$this->load->view('elements/common/footer_short', $data);
    	}
    }

    /**
     * 简短头尾调用 登录框、无彩种
     * @param unknown_type $file
     * @param unknown_type $data
     * @param unknown_type $version
     */
    public function displayShortHeader($file, $data=array(), $version = null)
    {
        $data = $this->_getData($data);
        if ($version)
        {
            $this->load->view($version.'/elements/common/header_short_notlogin', $data);
            $this->displayBanner($version, $data, 'ycfc');
            $this->load->view($version.'/'.$file, $data);
            $this->load->view($version.'/elements/common/footer_short', $data);
        }else
        {
            $this->load->view('elements/common/header_short_notlogin', $data);
            $this->load->view($file, $data);
            $this->load->view('elements/common/footer_short', $data);
        }
    }
    
    public function displayBanner($version, &$data, $type = 'tzy') {
    	$banner = json_decode($this->cache->hGet($this->REDIS['BANNERS'], $type), true);
    	if ($banner) {
    		$data['cpbanner'][$type] = $banner[$this->con."/".$this->act];
//     		$data['bntitle'] = $banner['title'];
//     		$data['bnpath'] = $banner['path'];
//     		$data['bnurl'] = $banner['url'];
			if ($type === 'tzy') $this->load->view($version.'/elements/banner', $data);
    	}
    }
    
    public function displaySpring($iscli = false, $con = null, $act = null)
    {
    	$zcfc = json_decode($this->cache->hGet($this->REDIS['BANNERS'], 'zcfc'), true);
    	$zcfc = $zcfc[($con ? $con : $this->con)."/".($act ? $act : $this->act)];
    	$data = array();
    	if ($zcfc) {
    		$data['zctitle'] = $zcfc['title'];
    		$data['zcpath'] = $zcfc['path'];
    		$data['zcurl'] = $zcfc['url'];
    	}
    	if ($iscli) {
    		return $this->load->view('v1.1/elements/pop-spring', $data, true);
    	}else {
    		$this->load->view('v1.1/elements/pop-spring', $data);
    	}
    }

    public function var_dump($data) {
        header('Content-type: application/json');
        echo json_encode($data);
        exit;
    }

    //登录判断
    private function CheckLogged() {
        if (empty($this->uid) && $this->checkPermition($this->con, $this->act)) {
            $this->primarysession->setArg('reffer', $_SERVER['REQUEST_URI']);
            $this->redirect('/main/login');
        } else {
            return true;
        }
    }

    //判断权限
    private function checkPermition($con, $method) {
        $limits = array(
            'wallet' => array('recharge', 'directPay', 'withdraw', 'requestPay', 'index'),
            'mylottery' => array('account', 'detail', 'recharge', 'withdrawals', 'betlog', 'chaselog', 'withdrawConceal', 'redpack', 'index','gendanlog'),
            'safe' => array('index', 'baseinfo', 'bankcard', 'idcard', 'phone', 'paypwd', 'one', 'modifyUserPhone', 'modifyUserPword'),
        	'orders' => array('detail', 'jcDetail', 'sfcDetail', 'rjDetail', 'index', 'lsDetail'),
        	'chases' => array('detail'),
        	'rebates' => array('index'),
        );
        $limit = empty($limits[$con]) ? array() : $limits[$con];
        return in_array($method, $limit);
    }

    /**
     * 获取用户cookie信息
     * @return multitype:string NULL Ambigous <> |boolean
     */
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

    protected function redirect($uri = '', $method = 'location', $http_response_code = 302) {
        switch ($method) {
            case 'refresh' : header("Refresh:0;url=" . $uri);
                break;
            default : header("Location: " . $uri, TRUE, $http_response_code);
                break;
        }
        exit;
    }

    protected static function controlRun($fname) {
        $fpath = APPPATH . '/logs/plock';
        if (file_exists("$fpath/$fname.start")) {
            unlink("$fpath/$fname.stop");
            unlink("$fpath/$fname.start");
        } else if (file_exists("$fpath/$fname.stop")) {
            die($fname . date('Y-m-d H:i:s') . ":被手动停止\n");
        }
    }

    public function controlRestart($fname) 
    {
        $this->load->model('task_model');
        $fpath = APPPATH . '/logs/plock/restart';
        if (file_exists("$fpath/$fname.start")) 
        {
            // 更新启停状态
            $this->task_model->updateRestart($fname, 1);
            @unlink("$fpath/$fname.stop");
            @unlink("$fpath/$fname.start");
        } 
        else if (file_exists("$fpath/$fname.stop")) 
        {
            // 更新启停状态
            $this->task_model->updateRestart($fname, 0);
            die($fname . date('Y-m-d H:i:s') . ":被手动停止\n");
        }
    }

    protected function getVoiceCode($phone, $sname='captcha') {
        $code = array();
        $codestr = $this->primarysession->getArg($sname);
        if (!empty($codestr))
            $codestr = explode(':', $codestr);
        if ($codestr[2] < time()) {
            $this->load->library('tools');
            $url = $this->config->item('phone_voice') . "/index.php?passid={$this->uinfo['passid']}&from=cp&phone=$phone";
            $postData = array();
            if(ENVIRONMENT != 'production')
            {
                //$postData['HOST'] = 'voice.2345.cn';
            }
            $response = $this->tools->request($url, $postData);
            if ($this->tools->recode == '200' && !empty($response)) {
                $response = json_decode($response, true);
                if ($response['status'] == '200' && !empty($response['code'])) 
                {
                    $code = $response;
                }
            }
        } else {
            $code['code'] = $codestr[0];
        }
        return $code;
    }

    protected function getSmsCode($phone, $position) {
        $code = '';
        for ($in = 0; $in < 4; $in++) {
            $code .= mt_rand(0, 9);
        }
        $this->load->model('user_model');
        $vdatas = array('#CODE#' => $code);
        $re = $this->user_model->sendSms($this->uid, $vdatas, 'captche', $phone, UCIP, $position);
        if (!$re) {
            $code = '';
        }
        return $code;
    }

    public function getPhoneCode($ctype)
    {
    	switch ($ctype)
    	{
    		case 'findPwdCaptcha':
    			$token = $this->input->post('token');
    			$position = $this->input->post('position', true);
    			$pImgCode = $this->input->post('code', true);
    			if(empty($pImgCode) || $this->primarysession->getArg('captcha') != strtolower($pImgCode))
    			{
                    $this->captcha(false);
    				echo json_encode(array('status'=> false, 'msg'=>'请输入正确的验证码'));
    				return ;
    			}
    			$this->captcha(false);
    			// 解密手机号码
    			$data = $this->strCode(urldecode($token));
    			$data = json_decode($data, true);
    			$phone = $data['phone'];
    			break;
    	    case 'modifyYzm':
    	        if (empty($this->uid)) exit(json_encode(array('status'=> false, 'msg'=>'用户未登录')));
    	        $position = $this->input->post('position', true);
    	        $pImgCode = $this->input->post('code', true);
    	        $phone = $this->input->post('phone', true);
    	        if(empty($pImgCode) || $this->primarysession->getArg('captcha') != strtolower($pImgCode)) {
    	            $this->captcha(false);
    	            exit(json_encode(array('status'=> false, 'msg'=>'请输入正确的验证码')));
    	        }
    	        $this->captcha(false);
    	        break;
    		default:
    			$position = $this->input->post('position', true);
    			$pImgCode = $this->input->post('code', true);
    			if(empty($pImgCode) || $this->primarysession->getArg('captcha') != strtolower($pImgCode)) {
                    $this->captcha(false);
    				echo json_encode(array('status'=> false, 'msg'=>'请输入正确的验证码'));
    				return ;
    			}
    			$this->captcha(false);
    			if ($this->uid) {
    				$this->load->model('user_model');
    				$uinfo = $this->user_model->getUserInfo($this->uid);
    				$phone = $uinfo['phone'];
    			}
    	}
        
        if (!empty($phone)) 
        {
        	$code = $this->getSmsCode($phone, $position);
            if (!empty($code)) {
                $out_time = $this->config->item('OUTTIME');
                $time = time();
                $expire = $time + 60 * $out_time['captche'];
                $second = $time + 60;
                $codestr = "{$code}:$expire:$second:$phone";
                $this->primarysession->setArg($ctype, $codestr);
                echo json_encode(array('status'=> true, 'msg'=>''));
                return ;
            }
        }
        echo json_encode(array('status'=> false,'msg'=>''));
    }
    
    public function getPhcodeNE($ctype) {
        $position = $this->input->post('position', true);
        $validate = $this->input->post('validate', true);
        if(empty($validate)) exit(json_encode(array('status'=> false, 'msg'=>'请先滑动验证码完成校验')));
        switch ($ctype) { //ctype保留，以区分以后不同的场景
            case 'findPwdCaptcha':
                $phone = $this->primarysession->getArg('findphone');
                break;
            case 'modifyYzm':
                if (empty($this->uid)) exit(json_encode(array('status'=> false, 'msg'=>'用户未登录')));
                if (!$this->primarysession->getArg('modifyCaptcha')) exit(json_encode(array('status'=> false, 'msg'=>'系统异常，请稍后再试')));
                $phone = $this->input->post('phone', true);
                break;
            case 'registerCaptcha':
                $phone = $this->input->post('phone', true);
                break;
            default:
                if ($this->uid) $phone = $this->uinfo['phone'];
                break;
        }
        
        if (!empty($phone)) {
            $this->load->library('NECaptcha');
            if (!$this->necaptcha->verifier($validate)) exit(json_encode(array('status'=> false, 'msg'=>'请先滑动验证码完成校验')));
            
            $code = $this->getSmsCode($phone, $position);
            if (!empty($code)) {
                $out_time = $this->config->item('OUTTIME');
                $time = time();
                $expire = $time + 60 * $out_time['captche'];
                $second = $time + 60;
                $codestr = "{$code}:$expire:$second:$phone";
                $this->primarysession->setArg($ctype, $codestr);
                exit(json_encode(array('status'=> true, 'msg'=>'')));
            }
        }
        echo json_encode(array('status'=> false,'msg'=>''));
    }

    protected function FreshCookie($name, $value, $exp_ratio='void') {
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

    public function captcha($img = true, $key = 'captcha') {
        $this->load->library('captcha');
        $this->captcha->doimg();
        $code = $this->captcha->getCode();
        $this->primarysession->setArg($key, $code);
        if ($img)
            $this->captcha->outPut();
        else
            return $code;
    }

    protected function SetCookie($uname, $passid, $uid, $mod, $loginTime, $sid, $own=true) {
        $cvals = array(
            'name_ie' => $uname,
            'I' => "i={$passid}&u={$uid}&n=" . urlencode($uname) . "&m={$mod}&t={$loginTime}&s={$sid}&v=1.1"
        );
        if ($own) {
            $cvals['I'] .= "&o=1";
        }
        foreach ($cvals as $name => $cval) {
            $this->FreshCookie($name, $cval);
        }
    }

    // 所需绑定信息是否完全
    public function isBindForRecharge() 
    {
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

    public function safetyLevel() {
        $level = 0;
        $safeFiles = array('phone', 'id_card', 'email');
        $safeFiles2 = array('bank_id');
        $safeLevel = array(
            '25'  => array('', '弱'),
            '50'  => array('m', '较弱'),
            '75'  => array('p', '较强'),
            '100' => array('h', '安全'),
        );
        foreach ($safeFiles as $safeFile) {
            if (!empty($this->uinfo[$safeFile])) {
                $level += 25;
            }
        }
        //银行卡信息     
        if(!empty($this->bankInfo)){
            $level += 25;
        }
        if($this->uinfo['safe_grade'] != $level)
        {
        	$this->load->model('user_model');
        	$this->user_model->saveSafeGrade($this->uid, $level);
        }
        return $safeLevel[$level];
    }
	
	public function __call($name = '', $arg = array())
	{
		$this->redirect('/error');
	}
	
	protected function loginLog()
	{
		$reffer = REFE;
        $reffer = empty($reffer) ? '' : $reffer;
        $loginRecord = array('login_time' => date('Y-m-d H:i:s', $this->cookie['t']),
            'uid' => $this->cookie['u'], 'ip' => UCIP, 'area' => $this->tools->convertip(UCIP), 'reffer' => $reffer);
        $this->load->model('user_model');
        $this->user_model->loginRecord($loginRecord);
	}
	
	protected function checkCaptcha($captcha, $phone, $position, $del = true)
	{
		$code = $this->primarysession->getArg($position);
		$codestr = explode(':', $code);
		if (($codestr[1] > time()) && ($captcha == $codestr[0]) && ($phone === $codestr[3])) {
			if ($del) {
				$this->primarysession->setArg($position, '');
			}
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
    
    private function pre_decrypt()
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
	
   	private function cre_pubkey()
   	{
   		$sec = substr(time(), -5);
   		$this->pub_salt = $this->tools->rsa_encrypt($sec);
   	}

    public function isMobile()
    {
        $useragent = $_SERVER['HTTP_USER_AGENT'];
        $from = $this->input->get('from', true);
        if($from != 'm')
        {
            if( (strpos($useragent, 'Android') !== FALSE && strpos($useragent, 'Mobile') !== FALSE) || strpos($useragent, 'iPhone') !== FALSE)
            {
                if(strpos($useragent, 'Android 2.3') !== FALSE && strpos($useragent, 'Mobile') !== FALSE)
                {
                    $appIgnore = $this->input->cookie('appIgnore');
                    if($appIgnore != '1')
                    {
                        // 当前地址
                        $backUrl = '//'.$_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
                        $this->redirect('/app/download/about?backUrl=' . urlencode($backUrl));
                    }
                }
                else
                {
                    $url = str_replace(array('com/', 'cn/'), array('com', 'cn'), $this->config->item('m_pages_url')) . $_SERVER['REQUEST_URI'];
                    header('Location: ' . $url);
                }   
            }
        }
    }

    /*
     * 加密解密公共函数
     * @date:2016-01-18
     */
    public function strCode ( $str , $action = 'DECODE' )
    {
        $action == 'DECODE' && $str = base64_decode ($str);
        $code = '';
        $hash = $this->config->item('encrypt_hash');
        $key = md5 ( $hash );
        $keylen = strlen ( $key );
        $strlen = strlen ( $str );
        for($i = 0; $i < $strlen; $i ++)
        {
           $k = $i % $keylen; //余数  将字符全部位移
           $code .= $str[$i] ^ $key[$k]; //位移
        }
        return ($action == 'DECODE' ? $code : base64_encode ( $code ));
    }
    
    private function disLock($act)
    {
    	$acts = array('cfg_jxsyxw_score', 'cfg_syxw_score', 'cfg_ks_score');
    	return in_array($act, $acts);
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
