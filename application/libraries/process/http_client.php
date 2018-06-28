<?php
/**
 * swoole_http_client请求封装
 * @author Administrator
 *
 *用法
$config = [
'timeout' => '3',
];
$httpClient = new HttpClient($config);
$post_data = ['codes' => '08,14,18,21,27,28|15:1:1', 'issueCount' => '30'];
$httpClient->request('https://123.59.105.39/api/ssqcompareresult/', [$this, 'hello'], $post_data);
 */
class http_client
{
    //结束符检测参数
    private $open_eof_check = false; //是否开启结束符检测
    private $package_eof = "\r\n\r\n";  //包结束符
    //长度检测参数
    private $open_length_check = 0; //是否开启检测
    private $package_length_type = 'N'; //包长度类型
    private $package_length_offset = 0; //第N个字节是包长度的值
    private $package_body_offset = 4; //第几个字节开始计算长度
    private $package_max_length = 1024 * 1024 * 2;  //包长度
    //Nagle合并算法
    private $open_tcp_nodelay = true;   //true 关闭  
    //SSL/TLS证书参数
    private $ssl_cert_file = '';    //证书地址
    private $ssl_key_file = ''; //私钥地址
    //绑定IP和端口参数
    private $bind_address = ''; //绑定ip
    private $bind_port = ''; //绑定端口
    //http代理参数
    private $http_proxy_host = ''; //代理地址
    private $http_proxy_port = ''; //代理端口
    //启用或关闭http长连接
    private $keep_alive = false;
    private $headers = [];
    private $set_datas = []; //set方法需要的属性列表
    //请求参数
    private $timeout = '3'; //超时时间 单位s
    private $method = '';

    public function __construct($config = [])
    {
        if (count($config) > 0)
        {
            $this->initialize($config);
        }
    }

    /**
     * 初始化set参数
     * @param array $config
     */
    public function initialize($config = [])
    {
        foreach ($config as $key => $val)
        {
            if (isset($this->{$key}))
            {
                $method = 'set_'.$key;

                if (method_exists($this, $method))
                {
                    $this->{$method}($val);
                }
                else
                {
                    $this->{$key} = $val;
                }
            }
        }
    }

    /**
     * http_client请求  GET|POST
     * @param unknown $url 请求url
     * @param callable $callback 回调函数
     * @param array $datas post请求时参数数组
     */
    public function request($url, callable $callback, $datas = [])
    {
        $parse_url = $this->parse_request_url($url);
        \Swoole\Async::dnsLookup($parse_url['host'], function ($domainName, $ip) use($parse_url, $callback, $datas)
        {
            if(empty($ip) || in_array($ip, array('127.0.0.1')))
            {
                //域名解析失败处理TODO
                if(!empty($datas['post_ip'])){
                    $ip = $datas['post_ip'];
                }else{
                    $status = array('statusCode' => 0, 'errCode' => 0, 'content' => '');
                    call_user_func($callback, array('status' => $status, 'response' => $ip, 'back_data' => $datas['back_data']));
                    return ;
                }
            }

            $cli = new \swoole_http_client($ip, $parse_url['port'], $parse_url['ssl']);
            //设置set参数
            $this->build_set_datas();
            $cli->set($this->set_datas);
            //设置请求头参数
            $this->build_headers();
            $datas['set_headers']['Host'] = $domainName;
            $this->set_headers($datas['set_headers']);
            $cli->setHeaders($this->headers);
            //设置post参数
            $parse_url['path'] = empty($parse_url['path']) ?  '/' : $parse_url['path'];
            if($this->method == 'POST')
            {
                $cli->setMethod("POST");
            }
            if(!empty($datas['post_data']))
            {
                $cli->setData($datas['post_data']);
            }
            $path = $parse_url['path'] . (empty($parse_url['query']) ?  '' : "?{$parse_url['query']}");
            $cli->execute($path, function ($cli) use($callback, $datas){
                $status = array('statusCode' => $cli->statusCode, 'errCode' => $cli->errCode, 'content' => '');
                if($cli->statusCode != '200' || $cli->errCode != '0')
                {
                    //TODO 记录错误日志
                    $status['content'] = print_r($cli, true);
                    log_message('log', json_encode($cli), 'swoole_http_error');
                }
                //成功回调
                call_user_func($callback, array('status' => $status, 'response' => $cli->body, 'back_data' => $datas['back_data']));
                $cli->close();
            });
        });
    }

    /**
     * 设置请求头部信息
     * @param array $data  键名=>健值 对
     */
    private function set_headers($data = [])
    {
        if(!empty($data)){
            foreach ($data as $header => $value)
            {
                $this->headers[$header] = $value;
            }
        }
    }

    /**
     * 初始化header
     */
    private function build_headers()
    {
        $this->headers['accept-encoding'] = "gzip, deflate";
        $this->headers['accept'] = "text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,*/*;q=0.8";
        $this->headers['accept-language'] = "zh-CN,zh;q=0.8,en;q=0.6,zh-TW;q=0.4,ja;q=0.2";
        $this->headers['user-agent'] = "Mozilla/5.0 (X11; Linux x86_64) Chrome/58.0.3026.3 Safari/537.36";
    }

    /**
     * swoole_http_client->set 方法参数组装
     */
    private function build_set_datas()
    {
        //结束符参数设置
        if($this->open_eof_check)
        {
            $this->set_datas['open_eof_check'] = $this->open_eof_check;
            $this->set_datas['package_eof'] = $this->package_eof;
        }
        //长度参数设置
        if($this->open_length_check)
        {
            $this->set_datas['open_length_check'] = $this->open_length_check;
            $this->set_datas['package_length_type'] = $this->package_length_type;
            $this->set_datas['package_length_offset'] = $this->package_length_offset;
            $this->set_datas['package_body_offset'] = $this->package_body_offset;
            $this->set_datas['package_max_length'] = $this->package_max_length;
        }
        //Nagle合并算法
        $this->set_datas['open_tcp_nodelay'] = $this->open_tcp_nodelay;
        //证书地址设置
        if($this->ssl_cert_file && $this->ssl_key_file)
        {
            $this->set_datas['ssl_cert_file'] = $this->ssl_cert_file;
            $this->set_datas['ssl_key_file'] = $this->ssl_key_file;
        }
        //绑定IP和端口设置
        if($this->bind_address && $this->bind_port)
        {
            $this->set_datas['bind_address'] = $this->bind_address;
            $this->set_datas['bind_port'] = $this->bind_port;
        }
        //http代理设置
        if($this->http_proxy_host && $this->http_proxy_port)
        {
            $this->set_datas['http_proxy_host'] = $this->http_proxy_host;
            $this->set_datas['http_proxy_port'] = $this->http_proxy_port;
        }
        //启用或关闭Http长连接
        $this->set_datas['keep_alive'] = $this->keep_alive;
        //超时时间
        $this->set_datas['timeout'] = $this->timeout;
    }

    /**
     * 设置超时时间
     * @param number $timeout
     */
    private function set_timeout($timeout = 10)
    {
        $this->timeout = $timeout;
    }

    /**
     * 解析URL参数
     */
    private function parse_request_url($url)
    {
        $parse_url =  parse_url($url);
        $parse_url['ssl']  = ($parse_url['scheme'] == 'https') ? true : false;
        //端口为空时设置端口
        if(empty($parse_url['port']))
        {
            if($parse_url['scheme'] == 'https')
            {
                $parse_url['port'] = '443';
            }
            else
            {
                $parse_url['port'] = '80';
            }
        }

        return $parse_url;
    }

    private function set_method($method)
    {
        $this->method = $method;
    }
}