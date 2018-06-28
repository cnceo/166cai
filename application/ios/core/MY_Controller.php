<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class MY_Controller extends CI_Controller 
{
	public $is_ajax = 0;
    public $pub_salt;

    public function __construct() 
    {
        parent::__construct();
        $this->load->library('primarySession');
        $this->load->helper('string');
        $this->load->library('tools');
        $this->is_ajax = $this->input->is_ajax_request();
        define('UCIP', $_SERVER['REMOTE_ADDR']);
        define('DOMAIN', $this->config->item('domain'));
        define('REFE', $_SERVER["HTTP_REFERER"]);
        $this->_browserRequest();
    }

    /*
     * 网页加载初始化
     * @date:2016-01-18
     */
    private function _browserRequest()
    {
        // 加密功能
        $this->pre_decrypt();
        $this->cre_pubkey();
        // 获取版本信息
        $this->version = $this->getUserAgentInfo();
        // 获取用户信息
        $cookie = $this->getLoginInfo();
        $this->cookie = $cookie;
        if (!empty($cookie['u'])) 
        {     
            $this->uid = $cookie['u'];
            $this->uname =$cookie['n'];
            //用户信息获得
            $this->load->model('user_model');
            $this->uinfo = $this->user_model->getUserInfo($this->uid);
        }
        else
        {
            $this->uid = '';
            $this->uname = '';
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

    /*
     * 短信验证码
     * @date:2016-01-18
     * @parm:phone 手机号, ctype 短信类型, sName sessionId
     */
    public function getSmsCode($phone, $ctype, $sName='captcha')
    {
        $this->primarysession->startSession();
        if (!empty($phone)) 
        {
            $code = $this->_getSmsCode($phone, $ctype);
            if (!empty($code)) 
            {
                $out_time = $this->config->item('OUTTIME');
                $time = time();
                $expire = $time + 60 * $out_time['captche'];
                $second = $time + 60;
                $codestr = "{$code}:$expire:$second:$phone";
                $this->primarysession->setArg($sName, $codestr);
                return true;
            }
        }
        return false;
    }
    
    /*
     * 短信验证码
     * @date:2016-01-18
     * @parm:phone 手机号, ctype 短信类型
     */
    protected function _getSmsCode($phone, $ctype) 
    {
    	$code = '';
    	for ($in = 0; $in < 4; $in++) 
        {
    		$code .= mt_rand(0, 9);
    	}
    	$this->load->model('user_model');
    	$vdatas = array('#CODE#' => $code);
    	$re = $this->user_model->sendSms($this->uid, $vdatas, $ctype, $phone);
    	if (!$re) 
        {
    		$code = '';
    	}
    	return $code;
    }
    
    /*
     * 语音验证码
     * @date:2016-01-18
     */
    public function getPhoneCode($phone, $ctype='captcha') 
    {
    	$this->primarysession->startSession();
    	if (!empty($phone)) 
        {
    		$code = $this->getVoiceCode($phone, $ctype);
    		if (!empty($code)) 
            {
    			$out_time = $this->config->item('OUTTIME');
    			$time = time();
    			$expire = $time + 60 * $out_time['captche'];
    			$second = $time + 60;
    			$codestr = "{$code['code']}:$expire:$second:{$code['codeId']}:{$code['codeVerify']}";
    			$this->primarysession->setArg($ctype, $codestr);
    			return true;
    		}
    	}
    	return false;
    }
    
    protected function getVoiceCode($phone, $sname='captcha') 
    {
    	$this->primarysession->startSession();
    	$code = array();
    	$codestr = $this->primarysession->getArg($sname);
    	if (!empty($codestr))
    		$codestr = explode(':', $codestr);
    	if ($codestr[2] < time()) 
        {
    		$this->load->library('tools');
			$url = $this->config->item('phone_voice') . "/index.php?passid={$this->uinfo['passid']}&from=cp&phone=$phone";
			$postData = array();
			if(ENVIRONMENT != 'production')
			{
				//$postData['HOST'] = 'voice.2345.cn';
			}
			$response = $this->tools->request($url, $postData);
			if ($this->tools->recode == '200' && !empty($response)) 
            {
				$response = json_decode($response, true);
				if ($response['status'] == '200' && !empty($response['code']))
				{
					$code = $response;
				}
			}
    	} 
        else 
        {
    		$code['code'] = $codestr[0];
    	}
    	return $code;
    }

    /*
     * 图片验证码
     * @date:2016-01-18
     */
    public function captcha($img = true, $key = 'captcha')
    {
        $this->primarysession->startSession();
        $this->load->library('captcha');
        $this->captcha->doimg();
        $code = $this->captcha->getCode();
        $this->primarysession->setArg($key, $code);
        if ($img)
            $this->captcha->outPut();
        else
            return $code;
    }

    /*
     * UA 检查
     * @date:2016-01-18
     */
    public function checkUserAgent()
    {
        $agentInfo = $_SERVER['HTTP_USER_AGENT'];
        if( strpos($agentInfo, '166cai/iOS') == FALSE && ENVIRONMENT === 'production')
        {
            echo "非法的浏览器请求！";die;
        }
    }

     /*
     * UA 获取版本信息
     * @date:2016-01-18
     */
    public function getUserAgentInfo()
    {
        $agentInfo = $_SERVER['HTTP_USER_AGENT'];
        preg_match("/#appVersionName:(.*?),appVersionCode:(\d+),channel:(.*?)#/is", $agentInfo, $match);
        $versionInfo = array(
            'appVersionName' => $match[1]?$match[1]:'1.0',
            'appVersionCode' => $match[2]?$match[2]:0,
            'channel' => $match[3]?$match[3]:'',
        );
        return $versionInfo;
 
    }

    /*
     * 加密处理函数
     * @date:2015-06-15
     */
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

    /*
     * 渠道信息检查
     * @date:2016-01-18
     */
    public function recordChannel($channelId) 
    {
        $this->load->model('channel_model', 'channel');
        $channelId = $this->channel->getValidChannelId($channelId);
        return $channelId;
    }

    /*
     * 获取请求头 版本信息 接口扩展时使用
     * @date:2016-01-18
     */
    public function getRequestHeaders()
    {
        $headers = getallheaders();

        $headerInfo = array(
            'appVersionName' => (isset($headers['appVersionName']) && !empty($headers['appVersionName']))?$headers['appVersionName']:'1.0',
            'appVersionCode' => (isset($headers['appVersionCode']) && !empty($headers['appVersionCode']))?$headers['appVersionCode']:1,
            'channel' => (isset($headers['channel']) && !empty($headers['channel']))?$headers['channel']:'',
            'deviceId' => (isset($headers['idfa']) && !empty($headers['idfa']))?$headers['idfa']:'',
            // 手机型号
            'model' => (isset($headers['model']) && !empty($headers['model']))?$headers['model']:'',
            // 手机系统
            'OSVersion' => (isset($headers['OSVersion']) && !empty($headers['OSVersion']))?$headers['OSVersion']:'',
            'platform' => (isset($headers['Platform']) && !empty($headers['Platform']))?$headers['Platform']:'',
            'old_platform' => $headers['old_platform'],
        );
        return $headerInfo;
    }

    /*
     * 获取网页内用户登录信息
     * @date:2016-01-18
     */
    public function getLoginInfo() {
        
        $cookie = $this->input->cookie('I');
        $loginInfo = array();
        if (isset($cookie)) 
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
                // $own = !empty($loginInfo['o']) ? true : false;
                //刷新cookie
                $this->SetCookie($loginInfo['n'], $loginInfo['i'], $loginInfo['u'], $loginInfo['m'], $loginInfo['t'], $loginInfo['s'], FALSE);
                return $loginInfo;
            }
        }
        return false;
    }

    /*
     * 刷新用户信息
     * @date:2016-01-18
     */
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

    /*
     * 计算用户中心cookie中的s值
     * @date:2016-01-18
     */
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

    private function str2unicode($str) 
    {
        $len = mb_strlen($str, "GB2312");
        for ($i = 0; $i < $len; $i++) 
        {
            $char = mb_substr($str, $i, 1, "GB2312");
            if (!(strlen($char) > 1)) 
            {
                $dec = ord($char);
                if ($dec > 16) 
                {
                    $ret .= "%" . $dec;
                } 
                else 
                {
                    $ret .= "%" . $dec;
                }
            } 
            else 
            {
                $temp = base_convert(bin2hex(iconv("GB2312", "UTF-8", $char)), 16, 2);
                $temp = substr($temp, 4, 4) . substr($temp, 10, 6) . substr($temp, 18);
                $ret .= "%" . hexdec(strtoupper(base_convert($temp, 2, 16)));
            }
        }
        return $ret;
    }

    public function loginout() 
    {
        $this->load->helper('cookie');
        $domain = str_replace('www.', '', $this->config->item('domain'));
        delete_cookie('I', $domain, '/');
        delete_cookie('name_ie', $domain, '/');
        delete_cookie('need_modify_name', $domain, '/');
    }

    // 获取订单状态
    public function orderConfig($cfg)
    {
        $this->load->config('order');
        return $this->config->item("cfg_$cfg");
    }

    /*
     * 获取设置cookie的token
     * @date:2016-01-18
     */
    public function getUserToken($uinfo)
    {
        $loginTime = strtotime($uinfo['last_login_time']);
        // 加密验证
        $sid = $this->calcSessionId($uinfo['passid'], $uinfo['uid'], $uinfo['uname'], $loginTime);
        $token = "i={$uinfo['passid']}&u={$uinfo['uid']}&n=" . urlencode($uinfo['uname']) . "&t={$loginTime}&s={$sid}";
        return $token;
    }

    /*
     * 获取设置cookie的auth
     * @date:2016-01-18
     */
    public function getUserAuth($uinfo)
    {
        return md5($uinfo['uid'] . $uinfo['pword']);
    }

    public function checkUserAuth($uinfo, $auth = '')
    {
        if($this->getUserAuth($uinfo) !== $auth)
        {
            return false;
        }
        else
        {
            return true;
        }
    }

    /*
     * 客户端外部地址信息加密
     * @date:2016-01-18
     */
    public function encryptData($data, $key = 'cai166', $authtype = 'md5')
    {
        ksort($data);
        $str = '';
        foreach ($data as $k => $v) 
        {
            $str .= $k . $v;
        }
        $str .= $key;
        $sign = $authtype($str);
        return $sign;
    }

    public function authData($data, $authtype = 'sha1')
    {
        $sha1_key = 'ios166cai';
        ksort($data);
        $str = '';
        foreach ($data as $k => $v) 
        {
            $str .= $k . $v;
        }
        $str .= $sha1_key;
        $data['sign'] = $authtype($str);
        return $data['sign'];
    }

    // 汇总返回客户端信息
    public function callbackUinfo($uinfo = array())
    {
        $uinfo['money'] = number_format(ParseUnit($uinfo['money'], 1), 2);
        $uinfo['nick_name'] = (!empty($uinfo['nick_name'])) ? $uinfo['nick_name'] : $this->config->item('defaultName');
        // 短信通知开关
        $uinfo['msg_send'] = isset($uinfo['msg_send']) ? $uinfo['msg_send'] : '0';
        // 中奖推送开关
        $uinfo['push_status'] = isset($uinfo['push_status']) ? $uinfo['push_status'] : '1';
        // APP推送设置
        $uinfo['app_push'] = isset($uinfo['app_push']) ? $uinfo['app_push'] : '1';
        // 红包个数
        $redpacks = $this->getUserRedpacks($uinfo['uid']);
        $uinfo['redpack'] = $redpacks['totals'] ? $redpacks['totals'] : '0';
        $uinfo['redpackInfo'] = $redpacks['msg'] ? $redpacks['msg'] : '';
        $uinfo['avatar'] = $uinfo['headimgurl'];

        $uinfo['token'] = $this->getUserToken($uinfo);
        return $uinfo;
    }
    
    // 获取红包信息
    public function getUserRedpacks($uid)
    {
        $this->load->model('app/model_uinfo', 'app_model_uinfo');
        $redpacks = $this->app_model_uinfo->getUserRedpack($uid);
        $msg = '';
        if($redpacks[0]['num'] > 0)
        {
            $msg = $redpacks[0]['num'] . '个红包快过期';
        }
        elseif($redpacks[1]['num'] > 0)
        {
            $msg =  '共' . $redpacks[1]['num'] . '个红包可用';
        }
        //        elseif($redpacks[2]['num'] > 0)
        //        {
        //            $msg =  '共' . $redpacks[2]['num'] . '个红包';
        //        }
        $info = array(
            'totals'    =>  $redpacks[1]['num'] ? (string)$redpacks[1]['num'] : '0',
            'msg'       =>  $msg,
        );
        return $info;
    }

    /**
     * [checkParams 必要参数验证]
     * @author LiKangJian 2017-05-24
     * @param  [type] $parms [description]
     * @return [type]        [description]
     */
    public function checkParams($valid, $params)
    {
        $keys = array_keys($params);
        $res_arr = array_intersect($valid,$keys);
        if( count($res_arr) != count($valid) )
        {
             $this->errorOut('000201','必要参数缺失');
        }
    }

    /**
     * [outPut 成功输出]
     * @author LiKangJian 2017-05-25
     * @param  [type] $data [description]
     * @return [type]       [description]
     */
    public function outPut($data)
    {
        $result = array(
            'status' => '1',
            'msg'   => 'success',
            'data' =>$data  
        );
        header('Content-type: application/json');
        die(json_encode($result));
    }
    /**
     * [errorOut 错误输出]
     * @author LiKangJian 2017-05-25
     * @param  string $code [description]
     * @param  string $msg  [description]
     * @return [type]       [description]
     */
    public function errorOut($code='999999', $msg='系统内部错误')
    {
        $error = array(
            'status' => $code,
            'msg'   =>$msg,
            'data'=>array()
        );
        header('Content-type: application/json');
        die(json_encode($error));
    }

    public function redirect($uri = '', $method = 'location', $http_response_code = 302) 
    {
        switch ($method) 
        {
            case 'refresh' : header("Refresh:0;url=" . $uri);
                break;
            default : header("Location: " . $uri, TRUE, $http_response_code);
                break;
        }
        exit;
    }

    public function checkNeedAppStars($money = 0)
    {
        // 单位 分
        $margin = 500 * 100;
        if($money > 0 && $money >= $margin)
        {
            $result = '1';
        }
        else
        {
            $result = '0';
        }
        return $result;
    }

    public function createNonceStr($length = 6) 
    {
        $chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
        $str = "";
        for ($i = 0; $i < $length; $i++) 
        {
            $str .= substr($chars, mt_rand(0, strlen($chars) - 1), 1);
        }
        return $str;
    }

    // 单设备登录
    public function recordIdfaLogin($uid)
    {
        $headerInfo = $this->getRequestHeaders();
        if(!empty($uid) && !empty($headerInfo['deviceId']))
        {
            $params = array(
                'uid'       =>  $uid,
                'platform'  =>  $this->config->item('platform'),
                'version'   =>  $headerInfo['appVersionName'],
                'idfa'      =>  $headerInfo['deviceId'],
            );
            apiRequest('api/AppLogin', 'recordUserLogin', $params);
            $this->load->config();
        }
    }

    // 单设备检查
    public function checkUserLogin($uid)
    {
        $result = array(
            'status'    =>  TRUE,
            'code'      =>  '200',
            'msg'       =>  '',
            'data'      =>  '',
        );
        $headerInfo = $this->getRequestHeaders();
        if(!empty($uid) && !empty($headerInfo['deviceId']))
        {
            $params = array(
                'uid'       =>  $uid,
                'idfa'      =>  $headerInfo['deviceId'],
                'platform'  =>  $this->config->item('platform'),
            );
            $check = apiRequest('api/AppLogin', 'checkUserLogin', $params);
            $this->load->config();
            $result = array(
                'status'    =>  $check['status'],
                'code'      =>  ($headerInfo['appVersionCode'] >= '3020001') ? '700' : '300',
                'msg'       =>  $check['data'],
                'data'      =>  '',
            );
        }
        return $result;
    }
    
    // 检查马甲包渠道
    public function checkChannelPackage($channel)
    {
        $channelId = $this->recordChannel($channel);
        $this->load->model('channel_model', 'channel');
        $channelInfo = $this->channel->getById($channelId);
        if($channelInfo['package'] != 2)
        {
            return true;
        }
        else
        {
            return false;
        }
    }
}