<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');
/**
 * CodeIgniter
 *
 * An open source application development framework for PHP 5.1.6 or newer
 *
 * @package		CodeIgniter
 * @author		ExpressionEngine Dev Team
 * @copyright	Copyright (c) 2006 - 2014, EllisLab, Inc.
 * @license		http://codeigniter.com/user_guide/license.html
 * @link		http://codeigniter.com
 * @since		Version 1.0
 * @filesource
 */
// ------------------------------------------------------------------------

/**
 * Shopping Comm_Curl Class
 *
 * @package		CodeIgniter
 * @subpackage	Libraries
 * @category	tools of curl
 * @author		huxm
 */
class CI_Tools
{

    private $ch = null;
    protected $CI = NULL;
    private $jdata = NULL;
    public $recode = null;

    public function __construct($params = array())
    {
        $this->CI = &get_instance();
    }
    //发送邮件
    public function sendMail($msg_info, $config = array(),$type=0)
    {
        $mail_config = array(
        	'protocol' => 'smtp',
            'smtp_host' => '58.211.78.142',
            'smtp_user' => 'caipiao@2345.com',
            'smtp_pass' => 'QTIS87ddw6caip',
            'charset' => 'utf-8',
            'mailtype' =>  'html'
        );
        if($type==1)
        {
            $mail_config = array(
                'protocol' => 'smtp',
                'smtp_host' => 'smtpdm.aliyun.com',
                'smtp_user' => 'caika@166cai.cn',
                'smtp_pass' => 'QTIS87ddw6caip',
                'charset' => 'utf-8',
                'newline'  => "\r\n",
                'mailtype' =>  'html'
            );
        }
        foreach($config as $key => $value)
        {
            $mail_config[$key] = $value;
        }
        $this->CI->load->library("email", $mail_config);
        $this->CI->email->clear();
        $this->CI->email->from((empty($msg_info['from'])?($type==1?'caika@166cai.cn':'customer@166cai.com'):$msg_info['from']), '166彩票');
        $this->CI->email->to($msg_info['to']);
        $this->CI->email->subject($msg_info['subject']);
        $this->CI->email->message($msg_info['message']);
        if(!empty($msg_info['cc']))
        {
            $this->CI->email->cc($msg_info['cc']);
        }
    	if(!empty($msg_info['bcc']))
        {
        	//暂时停止秘送功能
            //$this->CI->email->bcc($msg_info['bcc']);
        }
        $res = $this->CI->email->send();
        return $res;
    }
    //生成订单号
    public function getIncNum($key)
    {
        $REDIS = $this->CI->config->item('REDIS');
        $this->CI->load->driver('cache', array('adapter' => 'redis'));
        $order = $this->CI->cache->redis->increment($REDIS[$key]);
        if(empty($order))
        {
        	log_message('log', '生成订单后缀出现问题', 'getIncNum_error');
			die('操作失败');
        }
        if ($order > 999999)
        {
            $order = 1;
            $this->CI->cache->redis->save($REDIS[$key], $order, 0);
        }
        return sprintf(date('YmdHis') . "%06s", $order);
    }
	//发短信
	public function sendSms($uid, $phone, $msg, $ctype='0', $uip='0', $position = '16')
    {
        // ctype类型ID 9 18互换
        if($ctype == '9')
        {
            $ctype = $this->switchSms($phone, $ctype);
        }
    	$channel = ($ctype == '0') ? '5' : $ctype;     
    	$datas = array('phone' => $phone, 'msg' => iconv('UTF-8', 'GBK', $msg), 'smsType' => $channel, 'pid' => '122', 'positionId' => $position, 'clientIp' => $uip);
    	$sdata = array('uid' => $uid, 'phone' => $phone, 'content' => $msg, 'ctype' => $ctype, 'uip'=>$uip, 'position' => $position);
    	return $this->saveSms(array($sdata));
    }

    /**
     * [sendVoiceCodeMsg 语音验证码]
     * @author LiKangJian 2017-06-12
     * @param  [type] $uid   [description]
     * @param  [type] $phone [description]
     * @return [type]        [description]
     */
    public function sendVoiceCodeMsg($uid, $phone)
    {
        $code = rand(1000,9999);
        $url = $this->getVoiceCodeUrl($phone,$code);
        $sdata = array(
                'uid' => $uid,
                'phone' => $phone,
                'ctype' => '0',
                'content' => '您的语音验证码为：'.$code.'，30分钟内有效，切勿将验证码告知他人！请及时正确输入。',
                'uip' => '0',
                'position' => '17',
                'status'   => '1'
            );
        $re = false;
        if($this->saveSms(array($sdata)))
        {
            $response = $this->request($url);
            // {"RetCode":"00","RetMsg":"\u53d1\u9001\u6210\u529f","BsnsNo":"2018VocSvr040300001004","OrderNo":"166cai201804030920029855"}
            $response = json_decode($response, true);
            if($response['RetCode'] == '00')
            {
                $re = array(
                    'code'      =>  $code,
                    'OrderNo'   =>  $response['OrderNo'],
                );
            }
        }
        return $re;
    }
    /**
     * [getVoiceCodeSign 获取语音签名]
     * @author LiKangJian 2017-06-05
     * @param  [type] $params [description]
     * @param  [type] $key    [description]
     * @return [type]         [description]
     */
    private function getVoiceCodeSign($params,$key)
    {
        ksort($params);
        reset($params);
        $params['key'] = $key;
        $sign = '';
        foreach ($params as $k => $v) 
        {
            $sign .= $k.'='.$v.'&';
        }
        $sign = trim($sign,'&');
        return  strtolower( md5($sign) ) ;
    }
    /**
     * [getVoiceCodeUrl 获取语音验证的链接]
     * @author LiKangJian 2017-06-05
     * @param  [type] $mobile [description]
     * @return [type]         [description]
     */
    private function getVoiceCodeUrl($mobile, $code)
    {
        $url = 'http://121.196.204.71/plain/rcvVocSms?';
        $agentid = 'E00000086';
        $appkey = 'VOC000086';
        $secretKey = '4de10463e6aae792b79a6f24fb875955'; 
        $timestamp = date('YmdHis');
        $params = array(
            'agentid'       =>  $agentid,
            'key'           =>  $appkey,
            'phonenum'      =>  $mobile,
            'orderno'       =>  '166cai' . $timestamp . rand(1000, 9999),
            'cont'          =>  $code,
            'timestamps'    =>  $timestamp,
        );
        $params['sign'] = md5($params['agentid'] . $params['key'] . $params['phonenum'] . $secretKey . $params['cont'] . $params['orderno'] . $params['timestamps']);

        return $url . http_build_query($params);
    }
    // 验证码通道互换
    private function switchSms($phone, $ctype)
    {
        $this->CI->load->library('primarySession');
        $switchSms = $this->CI->primarysession->getArg('switchSms');
        if(!empty($switchSms))
        {
            $switchArr = explode('#', $switchSms);
            $expire = date('Y-m-d H:i:s', strtotime("+10 minutes", strtotime($switchArr[1])));
            if($expire >= date('Y-m-d H:i:s') && $switchArr[2] == $phone)
            {
                $ctype = ($switchArr[0] == '9') ? '18' : '9';
            }
        } 
        $codeStr = $ctype . '#' . date('Y-m-d H:i:s') . '#' . $phone;
        $this->CI->primarysession->setArg('switchSms', $codeStr);
        return $ctype;
    }
    //保存短信
	public function saveSms($datas)
	{
	    $values = array();
	    $dataval = array();
	    foreach ($datas as $data) {
	        if (empty($fields)) $fields = array_keys($data);
	        array_push($values, '(' . implode(',', array_map(array($this, 'maps'), $fields)) . ', now())');
	        $dataval = array_merge($dataval, array_values($data));
	    }
	    $this->CI->load->database();
	    return $this->CI->db->query('insert cp_sms_logs(' . implode(',', $fields) . ', created)values'.implode(',', $values), $dataval);
	}
	private function maps($val)
	{
		return '?';
	}
    private function init_curl($url, $params, $tout)
    {
        $this->ch = curl_init();
        curl_setopt($this->ch, CURLOPT_URL, $url);
        $urls = explode(':', $url);
        if ($urls[0] == 'https')
        {
            curl_setopt($this->ch, CURLOPT_SSL_VERIFYPEER, FALSE);
            curl_setopt($this->ch, CURLOPT_SSL_VERIFYHOST, FALSE);
            /* curl_setopt($this->ch, CURLOPT_SSL_VERIFYPEER,true);
              curl_setopt($this->ch, CURLOPT_CAINFO, dirname(BASEPATH).'/source/cacert.pem');
              curl_setopt($this->ch, CURLOPT_SSL_VERIFYHOST,2); */
        }
        $curlPost = $this->get_curl_str($params);
        if (!empty($curlPost))
        {
            curl_setopt($this->ch, CURLOPT_POST, 1);
            curl_setopt($this->ch, CURLOPT_POSTFIELDS, $curlPost);
        }
        curl_setopt($this->ch, CURLOPT_USERAGENT, 'Mozilla/5.0');
        curl_setopt($this->ch, CURLOPT_TIMEOUT, 2 * $tout);
        curl_setopt($this->ch, CURLOPT_CONNECTTIMEOUT, $tout);
        curl_setopt($this->ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($this->ch, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4);
        curl_setopt($this->ch, CURLOPT_REFERER, $this->CI->config->item('pages_url'));
    }

    public function request($url, $params = array(), $tout = 60)
    {
        $this->init_curl($url, $params, $tout);
        $_start_time = microtime(true);
        $content = curl_exec($this->ch);
        $this->recode = curl_getinfo($this->ch, CURLINFO_HTTP_CODE);
        $_total_exec = microtime(true) - $_start_time;
        $curl_errno = curl_errno($this->ch);
        $curl_error = curl_error($this->ch);
        if ($curl_errno || (!empty($curl_error)))
        {
        	log_message('log', "[$_total_exec]\terrno:{$curl_errno}\terror:{$curl_error}\trequestData:" . json_encode($params) . "\tcurlInfo:" . json_encode(curl_getinfo($this->ch)), 'curl_error');
        }
        curl_close($this->ch);
        return $content;
    }
    
	public function get($url, $params=array()) 
	{
        $query = "?";
        $query .= $this->get_curl_str($params);

        $this->ch = curl_init();
        curl_setopt($this->ch, CURLOPT_URL, $url . $query);
        curl_setopt($this->ch, CURLOPT_RETURNTRANSFER, 1);
        $response = curl_exec($this->ch);
        $curl_errno = curl_errno($this->ch);
        $curl_error = curl_error($this->ch);
        if ($curl_errno || (!empty($curl_error)))
        {
        	log_message('log', "errno:{$curl_errno}\terror:{$curl_error}\trequestData:" . json_encode($params) . "\tcurlInfo:" . json_encode(curl_getinfo($this->ch)), 'curl_error');
        }
        curl_close($this->ch);
        $response = json_decode($response, true);
        return $response;
    }
    
    /**
     *
     * @param string $encoding	压缩类型
     */
    private function SET_ENCODING($encoding)
    {
    	curl_setopt($this->ch, CURLOPT_ENCODING, $encoding);
    }
    
    private function SET_HEADER($boo)
    {
        curl_setopt($this->ch, CURLOPT_HEADER, $boo);
    }

    private function SET_COOKIE($cookie)
    {
        curl_setopt($this->ch, CURLOPT_COOKIE, $cookie);
    }

    private function SET_HOST($opt)
    {
        curl_setopt($this->ch, CURLOPT_HTTPHEADER, array("Host: {$opt}"));
    }
    
    private function SET_PROXY($proxy)
    {
    	curl_setopt($this->ch, CURLOPT_PROXY, $proxy);
    }
    
    private function SET_USERAGENT($uagent)
    {
    	curl_setopt($this->ch, CURLOPT_USERAGENT, $uagent);
    }

    private function SET_JSON($data)
    {
        curl_setopt($this->ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
        $this->jdata = json_encode($data);
    }

    private function SET_PUSHJSON($data)
    {
    	$authorization = $data['appKey'] . ':' . $data['masterSecret'];
    	$base64 = base64_encode($authorization);
    	$header = array("Authorization:Basic $base64","Content-Type:application/json");
        curl_setopt($this->ch, CURLOPT_HTTPHEADER, $header);
        $this->jdata = json_encode($data);
    }

    private function SET_MIPUSHJSON($data)
    {
        $appSecret = $data['appSecret'];
        $header = array("Authorization: key=$appSecret","Content-Type:application/json");
        curl_setopt($this->ch, CURLOPT_HTTPHEADER, $header);
        $this->jdata = json_encode($data);
    }

    private function get_curl_str($params)
    {
        $curlPost = '';
        if (is_array($params) && sizeof($params) >= 1)
        {
            $paramsArr = array();
            foreach ($params as $k => $val)
            {
                $method = "SET_$k";
                if (method_exists($this, $method))
                {
                    $this->$method($val);
                    continue;
                }
                $paramsArr[] = rawurlencode($k) . '=' . rawurlencode($val);
            }
            if (key_exists('JSON', $params))
            {
                $curlPost = $this->jdata;
            }
            elseif(key_exists('PUSHJSON', $params))
            {
            	$curlPost = $this->jdata;
            }
            elseif(key_exists('MIPUSHJSON', $params))
            {
                $curlPost = $this->jdata;
            }
            else
            {
                $curlPost .= implode('&', $paramsArr);
            }
        }
        elseif(!empty($params))
        {
            $curlPost = $params;
        }

        return $curlPost;
    }
	//IP纯真库
	public function convertip($ip)
	{
		$basepath = $this->CI->config->item('base_path');
		$ipdatafile =  $basepath.'/caipiaoimg/src/qqwry.dat';
		if(!$fd = @fopen($ipdatafile, 'rb')) 
		{
			return '- Invalid IP data file';
		}
	
		$ip = explode('.', $ip);
		$ipNum = $ip[0] * 16777216 + $ip[1] * 65536 + $ip[2] * 256 + $ip[3];
	
		if(!($DataBegin = fread($fd, 4)) || !($DataEnd = fread($fd, 4)) ) return;
		@$ipbegin = implode('', unpack('L', $DataBegin));
		if($ipbegin < 0) $ipbegin += pow(2, 32);
		@$ipend = implode('', unpack('L', $DataEnd));
		if($ipend < 0) $ipend += pow(2, 32);
		$ipAllNum = ($ipend - $ipbegin) / 7 + 1;
	
		$BeginNum = $ip2num = $ip1num = 0;
		$ipAddr1 = $ipAddr2 = '';
		$EndNum = $ipAllNum;
	
		while($ip1num > $ipNum || $ip2num < $ipNum) 
		{
			$Middle= intval(($EndNum + $BeginNum) / 2);
	
			fseek($fd, $ipbegin + 7 * $Middle);
			$ipData1 = fread($fd, 4);
			if(strlen($ipData1) < 4) 
			{
				fclose($fd);
				return '- System Error';
			}
			$ip1num = implode('', unpack('L', $ipData1));
			if($ip1num < 0) $ip1num += pow(2, 32);
	
			if($ip1num > $ipNum) 
			{
				$EndNum = $Middle;
				continue;
			}
	
			$DataSeek = fread($fd, 3);
			if(strlen($DataSeek) < 3) 
			{
				fclose($fd);
				return '- System Error';
			}
			$DataSeek = implode('', unpack('L', $DataSeek.chr(0)));
			fseek($fd, $DataSeek);
			$ipData2 = fread($fd, 4);
			if(strlen($ipData2) < 4) 
			{
				fclose($fd);
				return '- System Error';
			}
			$ip2num = implode('', unpack('L', $ipData2));
			if($ip2num < 0) $ip2num += pow(2, 32);
	
			if($ip2num < $ipNum) 
			{
				if($Middle == $BeginNum) 
				{
					fclose($fd);
					return '- Unknown';
				}
				$BeginNum = $Middle;
			}
		}
	
		$ipFlag = fread($fd, 1);
		if($ipFlag == chr(1)) 
		{
			$ipSeek = fread($fd, 3);
			if(strlen($ipSeek) < 3) 
			{
				fclose($fd);
				return '- System Error';
			}
			$ipSeek = implode('', unpack('L', $ipSeek.chr(0)));
			fseek($fd, $ipSeek);
			$ipFlag = fread($fd, 1);
		}
	
		if($ipFlag == chr(2)) {
			$AddrSeek = fread($fd, 3);
			if(strlen($AddrSeek) < 3) 
			{
				fclose($fd);
				return '- System Error';
			}
			$ipFlag = fread($fd, 1);
			if($ipFlag == chr(2)) 
			{
				$AddrSeek2 = fread($fd, 3);
				if(strlen($AddrSeek2) < 3) 
				{
					fclose($fd);
					return '- System Error';
				}
				$AddrSeek2 = implode('', unpack('L', $AddrSeek2.chr(0)));
				fseek($fd, $AddrSeek2);
			} 
			else
			{
				fseek($fd, -1, SEEK_CUR);
			}
	
			while(($char = fread($fd, 1)) != chr(0))
				$ipAddr2 .= $char;
	
			$AddrSeek = implode('', unpack('L', $AddrSeek.chr(0)));
			fseek($fd, $AddrSeek);
	
			while(($char = fread($fd, 1)) != chr(0))
				$ipAddr1 .= $char;
		}
		else
		{
			fseek($fd, -1, SEEK_CUR);
			while(($char = fread($fd, 1)) != chr(0))
				$ipAddr1 .= $char;
	
			$ipFlag = fread($fd, 1);
			if($ipFlag == chr(2)) 
			{
				$AddrSeek2 = fread($fd, 3);
				if(strlen($AddrSeek2) < 3) 
				{
					fclose($fd);
					return '- System Error';
				}
				$AddrSeek2 = implode('', unpack('L', $AddrSeek2.chr(0)));
				fseek($fd, $AddrSeek2);
			}
			else 
			{
				fseek($fd, -1, SEEK_CUR);
			}
			while(($char = fread($fd, 1)) != chr(0))
				$ipAddr2 .= $char;
		}
		fclose($fd);
	
		if(preg_match('/http/i', $ipAddr2)) 
		{
			$ipAddr2 = '';
		}
		$ipaddr = "$ipAddr1 $ipAddr2";
		$ipaddr = preg_replace('/CZ88\.NET/is', '', $ipaddr);
		$ipaddr = preg_replace('/^\s*/is', '', $ipaddr);
		$ipaddr = preg_replace('/\s*$/is', '', $ipaddr);
		if(preg_match('/http/i', $ipaddr) || $ipaddr == '') 
		{
			$ipaddr = '- Unknown';
		}
		return iconv('GB2312', 'UTF-8', $ipaddr);
	}

	//根据订单号返回分表后缀
	public function getTableSuffixByOrder($orderId)
    {
		$suffix = '';
        $date = substr($orderId, 0, 4) . '-' . substr($orderId, 4, 2) . '-' . substr($orderId, 6, 2);
        if (strtotime($date) < strtotime('30 days ago midnight'))
        {
            $suffix = substr($date, 0, 4) . substr($date, 5, 2);
        }
        return $suffix;

    }
    
    /**
     * 查询分表后缀
     * @param string $date	日期  yyyy-mm-dd hh:ii:ss
     * @param int $days		天数
     * @return string
     */
    public function getTableSuffixByDate($date, $days = '90')
    {
    	$suffix = '';
    	$date = strtotime($date);
    	if($date < strtotime("{$days} days ago midnight"))
    	{
    		$suffix = date('Y', $date);
    	}
    	
    	return $suffix;
    }
}
