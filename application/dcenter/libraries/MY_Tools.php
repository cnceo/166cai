<?php
class MY_Tools extends CI_Tools
{
	public function __construct($params = array())
	{
		parent::__construct($params);
	}	
	
	/**
	 * 
	 * @param string $url	url
	 * @param sting $lname	彩种名称
	 * @param int $times	超时时间
	 * @param array $params	规定请求参数设置
	 */
 	public function get_content( $url, $lname, $times = 0, $params = array())
    {
    	if(empty($times)) $times = time();
    	$date = date('Y-m-d', $times);       
        $content = $this->CI->tools->request($url, $params);
        $fname = "{$this->CI->log_path}/SCORE_DATA/"  . date('Y-m-d', $times) . '-' . $lname . '.txt';
        if($this->CI->tools->recode == '200')
        {
        	$encode = mb_detect_encoding($content, array('ASCII', 'UTF-8', 'GB2312', 'GBK', 'BIG5'));
        	if($encode != 'UTF-8')
        	{
        		$content = iconv($encode, 'UTF-8', $content);
        	}
        	file_put_contents($fname, $content);
        }
        else
        {
    		$this->CI->load->model('cron_model');
    		$this->CI->cron_model->set_alarm($lname, 1, $this->CI->tools->recode);
        }
        return $content;
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
    
	public function filter(&$datas, $funs = array())
    {
    	if(!empty($funs))
    	{
    		if(!empty($datas))
    		{
    			foreach ($datas as $key => $val)
    			{
    				foreach ($funs as $fun)
    				{
    					if(method_exists($this, $fun))
    					{
    						$datas[$key] = $this->$fun($datas[$key]);
    					}
    				}
    			}
    		}
    	}
    }
    
    private function mytrim($val)
    {
    	return trim($val);
    }
    
    /**
     * curl请求页面URL
     * @param unknown $url
     * @return void|array
     */
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
        $encode = mb_detect_encoding($body, array('ASCII', 'UTF-8', 'GB2312', 'GBK', 'BIG5'));
        if($encode != 'UTF-8')
        {
            $body = iconv($encode, 'UTF-8', $body);
        }
        
        return $body;
    }
}