<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * @property Syncr_Model $syncr_model
 * @property             $multi_process
 * @property             $db_config
 * @property             $cfg_orders
 */
class Cli_Cfg_Syncr_Data_Orders extends MY_Controller
{

    private $methods = array(
        /*'orders_new',
        'pushorderstatus_new',*/
        'orders',
        'pushorderstatus'
    );

    public function __construct()
    {
        parent::__construct();
        $this->load->library('tools');
        $this->load->model('syncr_model');
        $this->cfg_orders = $this->syncr_model->orderConfig('orders');
        $this->multi_process = $this->config->item('multi_process');
    }

    public function index()
    {
        $cName = strtolower(__CLASS__);
        $multi = $this->multi_process[$cName];
        $pLimit = 2;
        $stop = $this->syncr_model->ctrlRun($cName);
        $threads = array();
        $pNum = 0;
        while ( ! $stop)
        {
            foreach ($this->methods as $method)
            {
                $method = "cfg_$method";
                if ($multi)
                {
                	$pNum ++;
                    $pid = pcntl_fork();
                    if($pid == -1)
					{
						//进程创建失败 跳出循环
						$pNum --;
						continue;
					}
					else if($pid)
					{
                    	if(!in_array($method, $threads))
                    	{
                        	$threads[$pid] = $method;
                    	}
                        if ($pNum >= $pLimit)
                        {
                            $wPid = pcntl_wait($status);
                            if (!empty($wPid) || $wPid == -1)
                            {
                                unset($threads[$wPid]);
                                $pNum --;
                            }
                        }
                    }
                    else
                    {
                        if (method_exists($this, $method) && !in_array($method, $threads))
                        {
							$croname = "cron/{$this->con} $method";
							system("{$this->php_path} {$this->cmd_path} $croname", $status);
							if($status)
							{
								log_message('LOG', "$croname:$status", 'procerr');
							}
                        }
                        die(0);
                    }
                }
                else
                {
                    if (method_exists($this, $method))
                    {
                        $this->$method();
                    }
                }
            }
            if ($multi)
            {
                $this->syncr_model->threadWait($threads, 0);
            }
            $stop = $this->syncr_model->ctrlRun($cName);
            $pNum = count($threads);
            //break;
        }
    }

    /**
     * @uses    cfg_orders()
     */
    public function cfg_orders()
    {
        $cfg = array(
            'sdh' => $this->load->database('default', TRUE),
            //源DB
            'ddh' => $this->load->database('cfg', TRUE),
            //目标DB
            'stb' => "{$this->db_config['cp']}.cp_orders m force index(`modify`) join {$this->db_config['cp']}.cp_user_info n on m.uid = n.uid",
            //源数据表
            'dtb' => "{$this->db_config['cfg']}.cp_orders_ori",
            //目标数据表
            //拉取的字段
            'fld' => array(
                'm.id',
                'orderId',
                'm.uid',
                'codes',
                'lid',
                'money',
                'multi',
                'issue',
                'playType',
                'isChase',
                'betTnum',
                'endTime',
				'ticket_seller',
                'n.real_name',
                'n.phone',
                'n.id_card',
                'created'
            ),
            //更新的字段
            'ups' => array(
                'uid',
                'codes',
                'lid',
                'money',
                'multi',
                'issue',
                'playType',
                'isChase',
                'betTnum',
                'endTime',
				'ticket_seller',
                'n.real_name',
                'n.phone',
                'n.id_card',
                'created'
            ),
            //源数据条件
            'cdt' => 'm.modified > date_sub(now(), interval 30 minute) and m.synflag = 0 and m.status = ' . $this->cfg_orders['pay'],
            //源表排序字段
            'odr' => 'id',
            //源表需要拉取数据的标志
            'sfg' => 'synflag',
            //拉取标志所在的表
            'ftb' => "{$this->db_config['cp']}.cp_orders",
            //更新拉取标志的条件
            'cfg' => '',
            //按顺序递增式同步关闭
            'inc_syn_close' => true
        );
        $this->syncr_model->syncr_start($cfg);
    }

    public function cfg_orders_new()
    {
        $cfg = array(
            'sdh' => $this->load->database('default', TRUE),
            //源DB
            'ddh' => $this->load->database('cfg', TRUE),
            //目标DB
            'stb' => "{$this->db_config['tmp']}.cp_orders_trg e 
            join {$this->db_config['cp']}.cp_orders m on e.orderId = m.orderId
            join {$this->db_config['cp']}.cp_user_info n on m.uid = n.uid",
            //源数据表
            'dtb' => "{$this->db_config['cfg']}.cp_orders_ori",
            //目标数据表
            //拉取的字段
            'fld' => array(
                'e.id', 'm.orderId', 'm.uid', 'codes', 'lid', 'money',
                'multi', 'issue', 'playType', 'isChase', 'betTnum', 'endTime',
                'ticket_seller', 'n.real_name', 'n.phone', 'n.id_card', 'm.created'
            ),
            //更新的字段
            'ups' => array('orderId'),
            //源数据条件
            'cdt' => 'e.modified > date_sub(now(), interval 30 minute) and e.synflag & 1 = 1 ',
            //源表排序字段
            'odr' => 'id',
            //源表需要拉取数据的标志
            'sfg' => 'synflag',
            //拉取标志所在的表
            'ftb' => "{$this->db_config['tmp']}.cp_orders_trg",
            //增加删除标识
            'bit' => 3,
            //增加标识位置数
            'bitval' => 1,
            //更新拉取标志的条件
            'cfg' => '',
            //按顺序递增式同步关闭
            'inc_syn_close' => true
        );
        $this->syncr_model->syncr_start($cfg);

    }

    /**
     * @uses    cfg_pushorderstatus()
     */
    public function cfg_pushorderstatus()
    {
        $cfg = array(
            'sdh'   => $this->load->database('cfg', TRUE),
            'ddh'   => $this->load->database('default', TRUE),
            'stb'   => "{$this->db_config['cfg']}.cp_orders_ori m force index(`modify`) ",
            'dtb'   => "{$this->db_config['cp']}.cp_orders",
            'fld'   => array('orderId', 'status', 'ticket_time', 'bonus', 'margin', 'win_time', 'failMoney'),
            'ups'   => array('status', 'ticket_time', 'bonus', 'margin', 'win_time', 'failMoney'),
            'cdt'   => 'm.modified > date_sub(now(), interval 30 minute) and m.synflag > 1',
            'odr'   => 'orderId',
            'sfg'   => 'synflag',
            'extra' => 'c_synflag',        //目标表额外更新字段
            'ftb'   => "{$this->db_config['cfg']}.cp_orders_ori",
        	'bit'   => 2 //位运算--位移
        );
        $this->syncr_model->syncr_start($cfg);
    }

    public function cfg_pushorderstatus_new()
    {
        $cfg = array(
            'sdh'   => $this->load->database('cfg', TRUE),
            'ddh'   => $this->load->database('default', TRUE),
            'stb'   => "{$this->db_config['cfgtmp']}.cp_orders_ori_trg e 
            join {$this->db_config['cfg']}.cp_orders_ori m on e.orderId = m.orderId ",
            'dtb'   => "{$this->db_config['cp']}.cp_orders",
            'fld'   => array('m.orderId', 'm.status', 'ticket_time', 'bonus', 'margin', 'win_time', 'failMoney'),
            'ups'   => array('status', 'ticket_time', 'bonus', 'margin', 'win_time', 'failMoney'),
            'cdt'   => 'e.modified > date_sub(now(), interval 30 minute) and e.synflag > 1 ',
            'odr'   => 'orderId',
            'sfg'   => 'synflag',
            'extra' => 'c_synflag',        //目标表额外更新字段
            'ftb'   => "{$this->db_config['cfgtmp']}.cp_orders_ori_trg",
            'cfg'   => 'synflag = 1',
            //增加更新后删除标识
            'act' => 'udel',
            'bit'   => 2 //位运算--位移
        );
        $this->syncr_model->syncr_start($cfg);
    }
}
