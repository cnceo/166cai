<?php

class Test1 extends MY_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->library('DisOrder');
        $this->load->library('tools');
        $this->load->library('libcomm');
        $this->load->model('ticket_model');
    }
    
    //提票
    public function inTest()
    {
        $this->load->library("ticket/ticket_caidou");
        $this->load->model('ticket_model_base', 'tmbase');
        $this->tmbase->setValue(array('TBNAME' => 'cp_orders_split'));
        if(empty($this->PARAMS)) $this->PARAMS = '166cai';
        $orders = $this->tmbase->getTicketOrders($this->PARAMS);
        if(!empty($orders))
        {
            $corders = array();
            echo '开始' . date('Y-m-d H:i:s');
            echo '<br/>';
            foreach ($orders as $order)
            {
                $corders[$order['ticket_seller']][] = $order;
            }
            foreach ($corders as $seller => $norders)
            {
                $this->ticket_caidou->med_betting($norders);
            }
            echo '结束' . date('Y-m-d H:i:s');
        }
    }
    
    //重提
    public function inTest1()
    {
        $this->load->library("ticket/ticket_caidou");
        $this->load->model('ticket_model_base', 'tmbase');
        $this->tmbase->setValue(array('TBNAME' => 'cp_orders_split'));
        if(empty($this->PARAMS)) $this->PARAMS = '166cai';
        $messageids = $this->tmbase->getOrderIds($this->PARAMS);
        if(!empty($messageids))
        {
            echo '开始' . date('Y-m-d H:i:s');
            echo '<br/>';
            $corders = array();
            foreach ($messageids as $messageid)
            {
                $torders = $this->tmbase->getTicketOrdersByMsgId($messageid);
                foreach ($torders as $order)
                {
                    $corders[$order['ticket_seller']][] = $order;
                }
            }
            foreach ($corders as $seller => $norders)
            {
                $this->ticket_caidou->med_betting($norders);
            }
            echo '结束' . date('Y-m-d H:i:s');
        }
    }
    
    //查询出票结果
    public function inTest2()
    {
        $this->load->library("ticket/ticket_caidou");
        $this->load->model('ticket_model_base', 'tmbase');
        $this->tmbase->setValue(array('TBNAME' => 'cp_orders_split'));
        if(empty($this->PARAMS)) $this->PARAMS = '166cai';
        $messageids = $this->tmbase->getTicketResult($this->PARAMS, false);
        if(!empty($messageids))
        {
            $corders = array();
            foreach ($messageids as $messageid)
            {
                $torders = $this->tmbase->getSubOrdersByMsg($messageid, false);
                foreach ($torders as $order)
                {
                    $corders[$order['ticket_seller']][] = $order;
                }
            }
            foreach ($corders as $seller => $norders)
            {
                $lseller = "ticket_{$seller}";
                $this->{$lseller}->med_ticketResult($norders, false);
            }
        }
    }

    public function index1()
    {
        $this->load->config('server');
        $config = $this->config->item("server");
        $this->load->library('process/send_base', array('host' => $config['ip'], 'port' => $config['port']));
        $sql ='{"model":"worker_model","method":"transction","errnum":0,"params":{"db":"CF","sql":["insert cp_orders_split(sub_order_id,error_num,status,ticket_time,request_flag)values(?, ?, ?, date_add(now(), interval 1 minute)),(?, ?, ?, date_add(now(), interval 1 minute)) on duplicate key update sub_order_id = values(sub_order_id), error_num = values(error_num), status = if(status < values(status), values(status), status), ticket_time = if(status <= \'240\',\r\n    \t\t if(values(ticket_time) > endTime, date_sub(endTime, interval 10 second), values(ticket_time)),\r\n    \t\t ticket_time), request_flag = if(status <= \'240\', request_flag ^ values(request_flag), request_flag)"],"data":[["2017111017021953409006",0,240,1,"2017111017021953409106",0,240,1]]}}';
        $this->send_base->send($sql);
    }
    
    public function dao()
    {
    	/*用户分类定义：
		1. 忠诚用户：最近30日内登录超过15天及以上；
		select uid, count(DISTINCT(date(created))) lnum from  bn_cpiao.cp_login_info force INDEX (created)
		where 1 and  created >= DATE_SUB('2017-08-23',INTERVAL 30 day) and created < '2017-08-23'
		group by uid having lnum >= 15
		2. 普通活跃用户：最近30日内登录在2~14天的用户；
		select uid, count(DISTINCT(date(created))) lnum from  bn_cpiao.cp_login_info force INDEX (created)
		where 1 and  created >= DATE_SUB('2017-08-23',INTERVAL 30 day) and created < '2017-08-23'
		group by uid having lnum >= 2 and lnum < 15
		3. 新增用户：注册时间在最近30日内的用户；
		select uid from bn_cpiao.cp_user where created >= DATE_SUB('2017-08-23',INTERVAL 30 day) and created < '2017-08-23'
		4. 不活跃用户：最近登录时间距今为31~60天；
		select uid, last_login_time from bn_cpiao.cp_user where last_login_time >= DATE_SUB('2017-08-23',INTERVAL 60 day)
		and last_login_time < DATE_SUB('2017-08-23',INTERVAL 31 day)
		5. 流失用户：最近登录时间距今超过60天；
		select uid, last_login_time from bn_cpiao.cp_user where last_login_time < DATE_SUB('2017-08-23',INTERVAL 60 day)
		and last_login_time > 0 */
    	set_time_limit(0);
		$this->load->model('tips_model');
		$this->tips_model->getDatas();
    }
}
