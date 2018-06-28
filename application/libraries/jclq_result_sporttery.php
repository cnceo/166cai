<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Jclq_Result_Sporttery
{	
	private $ctypeMap = array(
		'sf'	=> 1,
		'rfsf'	=> 2,
		'sfc'	=> 3,
		'dxf'	=> 4,
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
		$url = "http://info.sporttery.cn/basketball/match_result.php?page=$page&search_league=&start_date=$fdate&end_date=$edate";
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
    			$url = "http://info.sporttery.cn/basketball/match_result.php?page=$page&search_league=&start_date=$fdate&end_date=$edate";
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
        $reg .= '<td.*?>.*?<\/td>.*?';
        $reg .= '<td.*?>.*?<\/td>.*?';
        $reg .= '<td.*?>.*?<\/td>.*?';
        $reg .= '<td.*?<span.*?>(.*?)<\/span>.*?<\/td>.*?';
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
                $mid = $weeks[$match].$matches[3][$key];
                $m_date = $matches[1][$key];
                $captureUrl = $matches[8][$key];
                if(strpos($captureUrl, 'pool_result') != FALSE && strpos($matches[7][$key], ':') != FALSE)
                {
                	$this->getDetail($captureUrl, $mid, $m_date);
                }
            }
        }
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
    			$fun = "getJclq" . ucfirst($name);
    			$details = $this->$fun($content);

    			array_push($bdata['s_data'], "(?, ?, ?, ?, now())");
                array_push($bdata['d_data'], $mid);
                array_push($bdata['d_data'], $m_date);
                array_push($bdata['d_data'], $ctype);
                array_push($bdata['d_data'], json_encode($details));
    		}

    		if(!empty($bdata['s_data']))
            {
                $this->CI->match_model->insertJclqResult($fields, $bdata);
                $bdata['s_data'] = array();
                $bdata['d_data'] = array();
            }
    	}
    }

    // 胜负固定奖金
    private function getJclqSf($content = '')
    {
        $details = array();
        // 获取指定表单
        $reg  = '胜负固定奖金.*?<\/div>.*?<table.*?kj-table.*?>.*?';
        $reg .= '<tr.*?>.*?主.*?负.*?主.*?胜.*?<\/tr>.*?';
        $reg .= '<\/table>';
        preg_match("/$reg/is", $content, $match);

        if(!empty($match[0]))
        {
            $reg  = '<tr.*?>.*?';
            $reg .= '<td.*?>(\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2})<\/td>.*?';
            $reg .= '<td.*?>([-+]?\d+\.\d+).*?<\/td>.*?';
            $reg .= '<td.*?>([-+]?\d+\.\d+).*?<\/td>.*?';
            $reg .= '<\/tr>';
            preg_match_all("/$reg/is", $match[0], $matches);
            unset($matches[0]);
            if(!empty($matches[1]))
            {
                foreach ($matches[1] as $key => $match) 
                {
                    $details[$key]['t'] = trim($match);
                    $details[$key]['zf'] = $matches[2][$key];
                    $details[$key]['zs'] = $matches[3][$key];
                }
            }
        }
        return $details;
    }

    // 让分胜负固定奖金
    private function getJclqRfsf($content = '')
    {
    	$details = array();
        // 获取指定表单
        $reg  = '让分胜负固定奖金.*?<\/div>.*?<table.*?kj-table.*?>.*?';
        $reg .= '<tr.*?>.*?负.*?让.*?分.*?胜.*?<\/tr>.*?';
        $reg .= '<\/table>';
        preg_match("/$reg/is", $content, $match);

        if(!empty($match[0]))
        {
            $reg  = '<tr.*?>.*?';
            $reg .= '<td.*?>(\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2})<\/td>.*?';
            $reg .= '<td.*?>([-+]?\d+\.\d+).*?<\/td>.*?';
            $reg .= '<td.*?>([-+]?\d+\.\d+).*?<\/td>.*?';
            $reg .= '<td.*?>([-+]?\d+\.\d+).*?<\/td>.*?';
            $reg .= '<td.*?><span.*?win.*?>(.*?)<\/span>.*?<\/td>.*?';
            $reg .= '<\/tr>';
            preg_match_all("/$reg/is", $match[0], $matches);
            unset($matches[0]);
            if(!empty($matches[1]))
            {
                foreach ($matches[1] as $key => $match) 
                {
                    $details[$key]['t'] = trim($match);
                    $details[$key]['rfzf'] = $matches[2][$key];
                    $details[$key]['rf'] = $matches[3][$key];
                    $details[$key]['rfzs'] = $matches[4][$key];
                    $details[$key]['cg'] = trim($matches[5][$key]);
                }
            }
        }        
        return $details;
    }

    // 大小分固定奖金
    private function getJclqDxf($content = '')
    {
    	$details = array();
        // 获取指定表单
        $reg  = '大小分固定奖金.*?<\/div>.*?<table.*?kj-table.*?>.*?';
        $reg .= '<tr.*?>.*?大.*?预设总分.*?小.*?<\/tr>.*?';
        $reg .= '<\/table>';
        preg_match("/$reg/is", $content, $match);

        if(!empty($match[0]))
        {
            $reg  = '<tr.*?>.*?';
            $reg .= '<td.*?>(\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2})<\/td>.*?';
            $reg .= '<td.*?>([-+]?\d+\.\d+).*?<\/td>.*?';
            $reg .= '<td.*?>([-+]?\d+\.\d+).*?<\/td>.*?';
            $reg .= '<td.*?>([-+]?\d+\.\d+).*?<\/td>.*?';
            $reg .= '<td.*?><span.*?win.*?>(.*?)<\/span>.*?<\/td>.*?';
            $reg .= '<\/tr>';
            preg_match_all("/$reg/is", $match[0], $matches);
            unset($matches[0]);
            if(!empty($matches[1]))
            {
                foreach ($matches[1] as $key => $match) 
                {
                    $details[$key]['t'] = trim($match);
                    $details[$key]['d'] = $matches[2][$key];
                    $details[$key]['zf'] = $matches[3][$key];
                    $details[$key]['x'] = $matches[4][$key];
                    $details[$key]['cg'] = trim($matches[5][$key]);
                }
            }
        }
        return $details;
    }

    // 胜分差
    private function getJclqSfc($content = '')
    {
    	$details = array();
        // 获取指定表单
        $reg  = '胜分差固定奖金.*?<\/div>.*?<table.*?kj-table.*?>.*?';
        $reg .= '<\/table>';
        preg_match("/$reg/is", $content, $match);
        // print_r($match[0]);die;
        if(!empty($match[0]))
        {
            $reg  = '<tr.*?>.*?';
            $reg .= '<td.*?>(\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2})<\/td>.*?';
            $reg .= '<td.*?>([-+]?\d+\.\d+).*?<\/td>.*?';
            $reg .= '<td.*?>([-+]?\d+\.\d+).*?<\/td>.*?';
            $reg .= '<td.*?>([-+]?\d+\.\d+).*?<\/td>.*?';
            $reg .= '<td.*?>([-+]?\d+\.\d+).*?<\/td>.*?';
            $reg .= '<td.*?>([-+]?\d+\.\d+).*?<\/td>.*?';
            $reg .= '<td.*?>([-+]?\d+\.\d+).*?<\/td>.*?';
            $reg .= '<td.*?>([-+]?\d+\.\d+).*?<\/td>.*?';
            $reg .= '<td.*?>([-+]?\d+\.\d+).*?<\/td>.*?';
            $reg .= '<td.*?>([-+]?\d+\.\d+).*?<\/td>.*?';
            $reg .= '<td.*?>([-+]?\d+\.\d+).*?<\/td>.*?';
            $reg .= '<td.*?>([-+]?\d+\.\d+).*?<\/td>.*?';
            $reg .= '<td.*?>([-+]?\d+\.\d+).*?<\/td>.*?';
            $reg .= '<\/tr>';
            preg_match_all("/$reg/is", $match[0], $matches);
            unset($matches[0]);
            if(!empty($matches[1]))
            {
                foreach ($matches[1] as $key => $match) 
                {
                    $details[$key]['t'] = trim($match);
                    $details[$key]['ks_k1'] = $matches[2][$key];
                    $details[$key]['ks_k2'] = $matches[3][$key];
                    $details[$key]['ks_k3'] = $matches[4][$key];
                    $details[$key]['ks_k4'] = $matches[5][$key];
                    $details[$key]['ks_k5'] = $matches[6][$key];
                    $details[$key]['ks_k6'] = $matches[7][$key];
                    $details[$key]['zs_z1'] = $matches[8][$key];
                    $details[$key]['zs_z2'] = $matches[9][$key];
                    $details[$key]['zs_z3'] = $matches[10][$key];
                    $details[$key]['zs_z4'] = $matches[11][$key];
                    $details[$key]['zs_z5'] = $matches[12][$key];
                    $details[$key]['zs_z6'] = $matches[13][$key];
                }
            }
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
