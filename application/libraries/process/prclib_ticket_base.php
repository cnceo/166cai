<?php
include_once dirname(__FILE__) . '/prclib_base.php';
class prclib_ticket_base extends prclib_base
{
	private $tables  = NULL;
	private $methods = NULL;
	private $actions = NULL;
    private $actions_map = array(
        array(1 => 'main', 2 => 'syxw', 4 => 'hbsyxw', 8 => 'jxsyxw', 16 => 'klpk', 32 => 'ks', 64 => 'jlks', 128 => 'jxks'),
        array(1 => 'cqssc', 2 => 'gdsyxw')
    );
	private $sellers = NULL;
    private $sellers_map = array(
        array(1 => 'qihui', 2 => 'caidou', 4 => 'shancai', 8 => 'huayang', 16 => 'hengju')
    );
	protected $workers = array();

	public function __construct($config = array())
	{
        parent::__construct($config);
		$this->tables = array('main' => 'cp_orders_split', 'syxw' => 'cp_orders_split_syxw', 'hbsyxw' => 'cp_orders_split_hbsyxw',
            'jxsyxw' => 'cp_orders_split_jxsyxw', 'klpk' => 'cp_orders_split_klpk', 'ks' => 'cp_orders_split_ks',
            'jlks' => 'cp_orders_split_jlks', 'jxks' => 'cp_orders_split_jxks', 'cqssc' => 'cp_orders_split_cqssc',
            'gdsyxw' => 'cp_orders_split_gdsyxw');
		$this->methods = array('betting', 'rebetting', 'ticketResult', 'ticketConcel');
		$this->WLIMIT = 60;
        $this->getParams();
	}

	public function __get($name)
    {
        $this->CI->load->library("ticket/ticket_{$name}");
        return $this->CI->{"ticket_{$name}"};
    }

    public function run()
    {
        swoole_set_process_name(sprintf('php-ps:%s', 'ticket_base'));
        foreach ($this->actions as $action)
        {
            if(empty($this->sellers[$action])) continue;
            foreach ($this->sellers[$action] as $seller)
            {
                if(empty($seller)) continue;
                $this->CreateProcess($action, $seller);
            }
        }
        $this->processWait();
    }

    /*创建任务进程，去完成具体的任务
     * */
    public function CreateProcess($action, $seller)
    {
        $process = new swoole_process(function(swoole_process $worker)use($action, $seller)
        {
            $this->processLock($worker, "ticket_base_{$action}-{$seller}");
            swoole_set_process_name(sprintf('php-ps:%s', "ticket_base:{$action}-{$seller}"));
            $this->CI->load->model('prcworker/ticket_model_order', 'tmbase');
            $this->CI->tmbase->setValue(array('TBNAME' => $this->tables[$action]));
            $works = $this->methods;
            foreach ($works as $work) 
            {
                if (method_exists($this, "med_$work"))
                {
                    try{
                        $this->{"med_$work"}($action, $seller);
                    }catch (PDOException $e){
                        log_message('LOG', "Fatal Error:(med_$work)" . $e->getMessage(), 'process/ticket_error.log');
                        $worker->exit();
                    }
                }
            }
        }, false, false);
        $this->workers[$this->getWorkKey("{$action}-{$seller}")] = $process->start();
    }

    // 提票
    public function med_betting($action, $seller)
    {
        $orders = $this->CI->tmbase->getTicketOrders($seller);
        if(!empty($orders))
        {
            $newOrders = array_chunk($orders, 50);
            foreach ($newOrders as $norders)
            {
                $this->{$seller}->med_betting($norders);
            }
        }
    }
    
    /**
     * 重提
     */
    public function med_rebetting($action, $seller)
    {
        $messageids = $this->CI->tmbase->getOrderIds($seller);
        if(!empty($messageids))
        {
            $corders = array();
            foreach ($messageids as $messageid)
            {
                $torders = $this->CI->tmbase->getTicketOrdersByMsgId($messageid, $seller);
                $this->{$seller}->med_betting($torders);
            }
        }
    }
    
    /**
     * 查询出票结果
     */
    public function med_ticketResult($action, $seller)
    {
        $messageids = $this->CI->tmbase->getTicketResult($seller, false);
        if(!empty($messageids))
        {
            $corders = array();
            foreach ($messageids as $messageid)
            {
                $torders = $this->CI->tmbase->getSubOrdersByMsg($messageid, false);
                $this->{$seller}->med_ticketResult($torders, false);
            }
        }
    }
    
    /**
     * 设置过期失败的订单
     * @param unknown $action
     * @param unknown $seller
     */
    public function med_ticketConcel($action, $seller)
    {
        $messageids = $this->CI->tmbase->getTicketResult($seller, true);
        if(!empty($messageids))
        {
            $corders = array();
            foreach ($messageids as $messageid)
            {
                $torders = $this->CI->tmbase->getSubOrdersByMsg($messageid, true);
                $this->{$seller}->med_ticketResult($torders, true);
            }
        }
        
        //未提票过期订单置失败操作
        $this->CI->tmbase->ticketConcel($seller);
    }

    /*任务进程的重启工作
      * */
    public function rebootProcess($ret)
    {
        $prcname = array_search($ret['pid'], $this->workers);

        if($prcname !== false)
        {
            $prcnames = explode('-', $prcname);
            $this->CreateProcess($prcnames[0], $prcnames[1]);
        }
        else
        {
            throw new \Exception("rebootProcess Error: no {$ret['pid']}");
        }
        unset($this->workers[$prcname]);
    }

    /* 回收任务进程，如果有任务进程结束或挂掉master进程负责回收，
       * 不能使用manager进程直接回收，因为manager要处理信号问题。
       * */
    public function processWait()
    {
        while(true) 
        {
            if(count($this->workers))
            {
                $ret = swoole_process::wait(false);
                if ($ret) 
                {
                    $this->rebootProcess($ret);
                }
            }
            $this->checkMpid();
            usleep(500000);
        }
    }

    private function getParams()
    {
        $parmarr = array();
        $action = explode(',', $this->ACNAME);
        $params = explode(';', $this->PARAMS);
        foreach ($params as $param) {
            if (empty($param)) continue;
            $tmparam = explode(':', $param);
            $table = explode(',', $tmparam[0]);
            $seller = explode(',', $tmparam[1]);
            $tablein = empty($table[1]) ? 0 : $table[0];
            $sellerin = empty($seller[1]) ? 0 : $seller[0];
            $stablein = empty($table[1]) ? $table[0] : $table[1];
            if (intval($action[$tablein]) & intval($stablein)) {
                $actionname = $this->actions_map[$tablein][$stablein];
                $this->actions[$actionname] = $actionname;
                foreach ($this->sellers_map[$sellerin] as $key => $myseller) {
                    if (intval($seller[0]) & intval($key)) {
                        $this->sellers[$actionname][] = $myseller;
                    }
                }
            }
        }
    }
}
?>
