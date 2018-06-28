<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Jczq_Result_Sporttery
{	
    private $ctypeMap = array(
        'sg'    => 0,   // 单场开奖结果
        'spf'   => 1,
        'rqspf' => 2,
        'bqc'   => 3,
        'jqs'   => 4,
        'cbf'   => 5,
    );
    
    private $_captureCtypeMap = array(
    	'sg'    => 'had',   // 单场开奖结果
    	'spf'   => 'had',
    	'rqspf' => 'hhad',
    	'bqc'   => 'hafu',
    	'jqs'   => 'ttg',
    	'cbf'   => 'crs',
    );
    
    private $_spfMap = array(
    	'h' => '胜',
    	'd' => '平',
    	'a' => '负',
    );
    
	private $CI;
	public function __construct()
	{
		$this->CI = &get_instance();
		$this->CI->load->library('tools');
		$this->CI->load->model('match_model');
	}

	public function capture()
	{
		$edate = date('Y-m-d');
		$fdate = date('Y-m-d', strtotime('-1 day', strtotime($edate)));
		$page = 1;
		// 竞彩篮球
		$url = "http://info.sporttery.cn/football/match_result.php?page=$page&search_league=&start_date=$fdate&end_date=$edate";
		$content = $this->get_content($url, __CLASS__ . 1);
		// 获取分页信息
		$dinfos = $this->get_dinfo($content);
		// 更新当前页场次赔率
		$this->get_datas($content);

		if($dinfos['tpage'] > 1)
    	{
    		for ($sp = 2; $sp <= $dinfos['tpage']; $sp++)
    		{
    			$page = $sp;
    			$url = "http://info.sporttery.cn/football/match_result.php?page=$page&search_league=&start_date=$fdate&end_date=$edate";
    			$content = $this->get_content($url, __CLASS__ . $sp);
    			$this->get_datas($content);
    		}
    	}
	}

	private function get_dinfo(&$content)
    {
        $rule  = '<input.*?start_date.*?value=[\'"](.*?)[\'"].*?\/>.*?';
        $rule .= '<input.*?end_date.*?value=[\'"](.*?)[\'"].*?\/>.*?';
        preg_match("/$rule/is", $content, $dinfos);
        $info['fdate'] = $dinfos['1'];
        $info['edate'] = $dinfos['2'];
        preg_match("/\/a>.*?<a.*?match_result.php\?page=(\d+)[^<>]+>尾页.*?<\/a>/is", $content, $dinfos);
        unset($dinfos[0]);
        $info['tpage'] = isset($dinfos[1]) ? $dinfos[1] : 0;
        return $info;
    }

    public function get_datas($content)
    {
    	$reg  = '<tr.*?>.*?';
        $reg .= '<td.*?>(.*?)<\/td>.*?';
        $reg .= '<td.*?>(.*?)(\d+)<\/td>.*?';
        $reg .= '<td.*?>(.*?)<\/td>.*?';
        $reg .= '<td.*?<span.*?>(.*?)<\/span>.*?<span.*?<span.*?>(.*?)<\/span>.*?<\/td>.*?';
        $reg .= '<td.*?><span.*?>(.*?)<\/span><\/td>.*?';
        $reg .= '<td.*?><span.*?>(.*?)<\/span><\/td>.*?';
        $reg .= '<td.*?>.*?<\/td>.*?';
        $reg .= '<td.*?u-detal.*?<a.*?href="(.*?)".*?<\/td>.*?';
        $reg .= '<\/tr>';
        preg_match_all("/$reg/is", $content, $matches);
        unset($matches[0]);
        if(!empty($matches[1]))
        {
        	foreach ($matches[2] as $key => $match) 
            {
            	$weeks = $this->getWeekArrayByDate($matches[1][$key]);
            	$mid = $weeks[$match].trim($matches[3][$key]);
            	$m_date = $matches[1][$key];
            	preg_match('/pool\_result\.php\?id=(.*)/', $matches[9][$key], $matchcapture);
            	$captureMid = $matchcapture[1];
            	
            	$api = "http://i.sporttery.cn/api/fb_match_info/get_pool_rs/?f_callback=pool_prcess&mid=".$captureMid;
            	$data = $this->getJson($api);
            	if (isset($data['status']) && $data['status']['code'] == 0) {
            		$oddslist = $data['result']['odds_list'];
            		$poolrs = $data['result']['pool_rs'];
            		$fields = array('mid', 'm_date', 'ctype', 'detail', 'created');
            		$bdata['s_data'] = array();
            		$bdata['d_data'] = array();
            		foreach ($this->_captureCtypeMap as $ctype => $captureCtype) {
            			$fun = "getJczq" . ucfirst($ctype);
            			if ($ctype == 'sg') {
            				if (!empty($poolrs)) $details = $this->$fun($poolrs);
            			}else {
            				if (!empty($oddslist[$captureCtype])) $details = $this->$fun($oddslist[$captureCtype]);
            			}
            			
            			array_push($bdata['s_data'], "(?, ?, ?, ?, now())");
            			array_push($bdata['d_data'], $mid);
            			array_push($bdata['d_data'], $m_date);
            			array_push($bdata['d_data'], $this->ctypeMap[$ctype]);
            			array_push($bdata['d_data'], json_encode($details));
            		}
            		
            		if(!empty($bdata['s_data']))
            		{
            			$this->CI->match_model->insertJczqResult($fields, $bdata);
            			$bdata['s_data'] = array();
            			$bdata['d_data'] = array();
            		}
            	}
            }
        }
    }
    
    private function getJson($url)
    {
    	$content = $this->getCurlUrl($url);
    	preg_match('/pool_prcess\((.*)\)/', $content, $matches);
    	$json = $matches[1];
    	return json_decode($json, true);
    }

    private function getDetail($url, $mid, $m_date)
    {
    	$content = $this->get_content($url, __CLASS__ . $mid);
    	
    	if(!empty($this->ctypeMap))
    	{
    		$fields = array('mid', 'm_date', 'ctype', 'detail', 'created');
            $bdata['s_data'] = array();
            $bdata['d_data'] = array();
    		foreach ($this->ctypeMap as $name => $ctype) 
    		{
    			$fun = "getJczq" . ucfirst($name);
    			$details = $this->$fun($content);

    			array_push($bdata['s_data'], "(?, ?, ?, ?, now())");
                array_push($bdata['d_data'], $mid);
                array_push($bdata['d_data'], $m_date);
                array_push($bdata['d_data'], $ctype);
                array_push($bdata['d_data'], json_encode($details));
    		}

    		if(!empty($bdata['s_data']))
            {
                $this->CI->match_model->insertJczqResult($fields, $bdata);
                $bdata['s_data'] = array();
                $bdata['d_data'] = array();
            }
    	}
    }

    // 单场开奖结果
    private function getJczqSg($data)
    {
        $details = array();
        $details['spf'] = $this->_spfMap[$data['had']['pool_rs']];
        $details['rqspf'] = "(".$data['hhad']['goalline'].")".$this->_spfMap[$data['hhad']['pool_rs']];
        $details['cbf'] = $data['crs']['prs_name'];
        $details['jqs'] = $data['ttg']['prs_name'];
        $bqc = str_split($data['hafu']['pool_rs']);
        $details['bqc'] = $this->_spfMap[$bqc[0]].$this->_spfMap[$bqc[1]];
        return $details;
    }

    // 胜平负过关固定奖金
    private function getJczqSpf($data)
    {
    	$details = array();
    	log_message('Log', json_encode($data), 'award');
    	foreach ($data['odds'] as $key => $odds) {
    		$details[$key]['t'] = $odds['date']." ".$odds['time'];
    		$details[$key]['s'] = $odds['h'];
    		$details[$key]['p'] = $odds['d'];
    		$details[$key]['f'] = $odds['a'];
    	}
        return $details;
    }

    // 让分胜负固定奖金
    private function getJczqRqspf($data)
    {
    	$details = array();
    	foreach ($data['odds'] as $key => $odds) {
    		$details[$key]['t'] = $odds['date']." ".$odds['time'];
    		$details[$key]['rq'] = rtrim($odds['goalline'], '0|.00');
    		$details[$key]['s'] = $odds['h'];
    		$details[$key]['p'] = $odds['d'];
    		$details[$key]['f'] = $odds['a'];
    	}
        return $details;
    }

    // 半全场胜平负过关固定奖金
    private function getJczqBqc($data)
    {
    	$details = array();
    	foreach ($data['odds'] as $key => $odds) {
    		$details[$key]['t'] = $odds['date']." ".$odds['time'];
    		$details[$key]['ss'] = $odds['hh'];
            $details[$key]['sp'] = $odds['hd'];
            $details[$key]['sf'] = $odds['ha'];
            $details[$key]['ps'] = $odds['dh'];
            $details[$key]['pp'] = $odds['dd'];
            $details[$key]['pf'] = $odds['da'];
            $details[$key]['fs'] = $odds['ah'];
            $details[$key]['fp'] = $odds['ad'];
            $details[$key]['ff'] = $odds['aa'];
    	}
        return $details;
    }

    // 总进球数过关固定奖金
    private function getJczqJqs($data)
    {
    	$details = array();
    	foreach ($data['odds'] as $key => $odds) {
    		$details[$key]['t'] = $odds['date']." ".$odds['time'];
            $details[$key]['0'] = $odds['s0'];
            $details[$key]['1'] = $odds['s1'];
            $details[$key]['2'] = $odds['s2'];
            $details[$key]['3'] = $odds['s3'];
            $details[$key]['4'] = $odds['s4'];
            $details[$key]['5'] = $odds['s5'];
            $details[$key]['6'] = $odds['s6'];
            $details[$key]['7'] = $odds['s7'];
    	}
        return $details;
    }

    // 总进球数过关固定奖金
    private function getJczqCbf($data)
    {
        $details = array();
    	foreach ($data['odds'] as $key => $odds) {
    		$details[$key]['t'] = $odds['date']." ".$odds['time'];
    		$details[$key]['s1'] = $odds['0100'];
    		$details[$key]['s2'] = $odds['0200'];
    		$details[$key]['s3'] = $odds['0201'];
    		$details[$key]['s4'] = $odds['0300'];
    		$details[$key]['s5'] = $odds['0301'];
    		$details[$key]['s6'] = $odds['0302'];
    		$details[$key]['s7'] = $odds['0400'];
    		$details[$key]['s8'] = $odds['0401'];
    		$details[$key]['s9'] = $odds['0402'];
    		$details[$key]['s10'] = $odds['0500'];
    		$details[$key]['s11'] = $odds['0501'];
    		$details[$key]['s12'] = $odds['0502'];
    		$details[$key]['s13'] = $odds['-1-h'];
    		$details[$key]['s14'] = $odds['0000'];
    		$details[$key]['s15'] = $odds['0101'];
    		$details[$key]['s16'] = $odds['0202'];
    		$details[$key]['s17'] = $odds['0303'];
    		$details[$key]['s18'] = $odds['-1-d'];
    		$details[$key]['s19'] = $odds['0001'];
    		$details[$key]['s20'] = $odds['0002'];
    		$details[$key]['s21'] = $odds['0102'];
    		$details[$key]['s22'] = $odds['0003'];
    		$details[$key]['s23'] = $odds['0103'];
    		$details[$key]['s24'] = $odds['0203'];
    		$details[$key]['s25'] = $odds['0004'];
    		$details[$key]['s26'] = $odds['0104'];
    		$details[$key]['s27'] = $odds['0204'];
    		$details[$key]['s28'] = $odds['0005'];
    		$details[$key]['s29'] = $odds['0105'];
    		$details[$key]['s30'] = $odds['0205'];
    		$details[$key]['s31'] = $odds['-1-a'];
    	}
        return $details;
    }

    public function get_content( $url, $lname, $times = 0, $params = array())
    {
        if(empty($times)) $times = time();
        $date = date('Y-m-d', $times);       
        $content = $this->getCurlUrl($url);

        $encode = mb_detect_encoding($content, array('ASCII', 'UTF-8', 'GB2312', 'GBK', 'BIG5'));
        if($encode != 'UTF-8')
        {
            $content = iconv($encode, 'UTF-8', $content);
        }
        return $content;
    }

    public function getCurlUrl($url)
    {
        $curl = curl_init(); // 启动一个CURL会话
        curl_setopt($curl, CURLOPT_URL, $url); // 要访问的地址
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0); // 对认证证书来源的检查
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 2); // 从证书中检查SSL加密算法是否存在
        curl_setopt($curl, CURLOPT_USERAGENT, "Mozilla/5.0 (Macintosh; Intel Mac OS X 10_11_4) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/47.0.2526.111 Safari/537.36");
        curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 0); // 使用自动跳转
        curl_setopt($curl, CURLOPT_ENCODING, "gzip");
        curl_setopt($curl, CURLOPT_TIMEOUT, 30); // 设置超时限制防止死循环
        curl_setopt($curl, CURLOPT_HEADER, 1); // 显示返回的Header区域内容
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1); // 获取的信息以文件流的形式返回
        
        $result = curl_exec($curl); // 执行操作
        $info = curl_getinfo($curl);
        if ($info['http_code'] == 302)
        {
            //解析HTTP数据流
            list($header, $body) = explode("\r\n\r\n", $result, 2);
            $location= $info['redirect_url'];
            preg_match("/Set-Cookie:(.*?)$/",$header,$match_cookie);
            //cookie获取成功
            $cookie = '';
            if (count($match_cookie) ==2 )
            {
                $cookie =$match_cookie[1];
            }    
            
            $curl = curl_init(); // 启动一个CURL会话
            curl_setopt($curl, CURLOPT_URL, $location); // 要访问的地址
            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0); // 对认证证书来源的检查
            curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 2); // 从证书中检查SSL加密算法是否存在
            curl_setopt($curl, CURLOPT_USERAGENT, "Mozilla/5.0 (Macintosh; Intel Mac OS X 10_11_4) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/47.0.2526.111 Safari/537.36");
            
            if ($cookie!="")
            {
                curl_setopt($curl, CURLOPT_COOKIE, $cookie);
            }
            curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 0); // 使用自动跳转
            curl_setopt($curl, CURLOPT_ENCODING, "gzip");
            curl_setopt($curl, CURLOPT_TIMEOUT, 30); // 设置超时限制防止死循环
            curl_setopt($curl, CURLOPT_HEADER, 1); // 显示返回的Header区域内容
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1); // 获取的信息以文件流的形式返回
            $result = curl_exec($curl); // 执行操作
        }
        
        if (curl_errno($curl)) 
        {
            log_message('Error', 'Errno'. curl_error($curl));
            return;
        }
        
        curl_close($curl); // 关闭CURL会话
        
        //解析HTTP数据流
        list($header, $body) = explode("\r\n\r\n", $result, 2);
        return $body;
    }

    public function getWeekArrayByDate( $date )
    {
        //获取比赛编号
        $weekarray=array('周日', '周一', '周二', '周三', '周四', '周五', '周六');
        $ntime = strtotime($date);
        $nweek = date('w', $ntime);
        $sdate = strtotime("-6 day", $ntime);
        $weeks = array();
        for($day = 0; $day < 7; ++$day)
        {
            $ntime = strtotime("+ $day day", $sdate);
            $week = date('w', $ntime);
            $weeks[$weekarray[$week]] = date('Ymd', $ntime);
        }
        return $weeks;
    }
}
