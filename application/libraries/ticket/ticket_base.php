<?php
/**
 * Created by HuXiaoMing.
 * User: Huxiaoming
 * Date: 2017/11/17
 * Time: 11:47
 */
class ticket_base{
    protected $phone = '13636430451';
    protected $id_card = '500236199011290653';
    protected $real_name = '李军';
    protected $CI;
    protected $order_status;
    protected $databack;
    protected $httpasyc;
    protected $gaoping = array('53', '21406', '21407', '21408', '54', '55', '56', '57', '21421');
    protected $ticket_model_order;
    public function __construct()
    {
        $this->CI = &get_instance();
        $this->CI->load->library('tools');
        $this->CI->load->config('server');
        $config = $this->CI->config->item("server");
        $this->CI->load->model('prcworker/ticket_model_order');
        $this->CI->load->library('process/send_base', array('host' => $config['ip'], 'port' => $config['port']));

        $config = [
            'timeout' => '60',
            'keep_alive' => '3',
            'method' => 'POST'
        ];
        $this->CI->load->library('process/http_client', $config);
        $this->httpasyc = $this->CI->http_client;
        $this->databack = $this->CI->send_base;
        $this->ticket_model_order = $this->CI->ticket_model_order;
        $this->order_status = $this->CI->ticket_model_order->orderConfig('orders');
    }

    /**
     * 异步请求回调函数
     * @param unknown $params
     */
    public function ticketcallback($params)
    {
        //请求成功处理
        if($params['status']['statusCode'] == 200)
        {
            $xmlobj = $this->getResponse($params['response']);
            log_message('LOG', "{$params['back_data']['LogHead']}[RES]: " . $params['response'], $params['back_data']['pathTail']);
            $this->{$params['back_data']['callfun']}($xmlobj, $params['back_data']['datas']);
        }
        else
        {
            //失败回调处理
            $this->{$params['back_data']['failCall']}($params['back_data']['datas']);
        }
    }

    /**
     * 特殊格式需要特殊处理 默认xml
     * @param unknown $response
     * @return mixed|unknown
     */
    private function getResponse($response)
    {
        $result = '';
        switch ($this->seller)
        {
            case 'shancai' :
                $result = json_decode($response, true);
                break;
            case 'hengju' :
                $response = str_replace('gb2312', 'UTF-8', $response);
                $response = iconv('GBK', 'UTF-8', $response);
                $result = simplexml_load_string($response, 'SimpleXMLElement', LIBXML_NOCDATA);
                break;
            default :
                $result = simplexml_load_string($response);
                break;
        }
        
        return $result;
    }

    /**
     * 异步sql执行回调
     * @param unknown $method
     * @param unknown $params
     */
    protected function back_update($method, $params){
        $data = array(
            'model' => 'worker_model',
            'method' => $method,
            'errnum' => 0,
            'params' => $params
        );
        $this->databack->send($data);
    }

    protected function get_ggtype($codes)
    {
        preg_match('/ZS=\d+,BS=\d+,JE=\d+,GG=(\d+)/is', $codes, $matches);
        if(!empty($matches[1])){
            $ggarr = array();
            $ggmaps = array(2, 3, 4, 5, 6, 7, 8);
            foreach ($ggmaps as $ggtype){
                if($matches[1] & (1 << ($ggtype - 2))){
                    array_push($ggarr, $this->ggtype_map[$ggtype]);
                }
            }
            return $ggarr;
        }
    }
}