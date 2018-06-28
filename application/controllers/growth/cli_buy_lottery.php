<?php

/**
 * 常驻脚本 实时运行
 * 购彩出票成功操作
 * @date:2017-12-28
 */

class Cli_Buy_Lottery extends MY_Controller
{
    private $lid_job = array(
        '21406' => '1',  //彩种lid => 任务id
        '21407' => '1',
        '21408' => '1',
        '53'    => '1',
        '54'    => '1',
        '55'    => '1',
        '56'    => '1',
        '57'    => '1',
        '42'    => '2',
        '21421' => '1',
    );
    public function __construct()
    {
        parent::__construct();
    }

    public function index()
    {
        $multi = true; //本地执行的话  改为 fales
        $pLimit = 1;
        $threads = array();
        $pNum = 0;
        while (true)
        {
            if($multi)
            {
                $pnum ++;
                $pid = pcntl_fork();
                if($pid == -1)
                {
                    //进程创建失败 跳出循环
                    $pnum --;
                    continue;
                }
                else if($pid)
                {
                    $threads[$pid] = "runCron";
                    if($pnum >= $plimit)
                    {
                        $wpid = pcntl_wait($status);
                        if(!empty($wpid))
                        {
                            unset($threads[$wpid]);
                            $pnum --;
                        }
                    }
                }
                else
                {
                    if(method_exists($this, "runCron") && !in_array($method, $threads))
                    {
                        $this->runCron();
                    }
                    die(0);
                }
            }
            else 
            {
                $this->runCron();
            }
            
        }
    }

    public function runCron()
    {
        $this->load->model('growth_cli_model');
        $this->load->model('user_model');
        $this->load->library('stomp');
        $this->load->library('BetCnName');
        
        $job_uids = array();
        //非合买单操作
        $orders_trg = $this->growth_cli_model->getOrderTrg(0);
        if($orders_trg)
        {
            $config = $this->config->item('stomp');
            $connect = $this->stomp->connect();
            if($connect)
            {
                $o_trg = array();
                foreach ($orders_trg as $trg)
                {
                    $o_trg[$trg['orderId']] = $trg['modified'];
                }
                $orders = $this->growth_cli_model->getOrders(array_keys($o_trg));
                $succ_orderIds = array();
                foreach ($orders as $order)
                {
                    $value = floor(($order['money'] - $order['failMoney'] - $order['redpackMoney'])/100);
                    if($value > 0)
                    {
                        $this->stomp->begin('t1');
                        $lname = BetCnName::getCnName($order['lid']);
                        $data = array(
                            'type' => 'buyLottery',
                            'ctype' => 'growth',
                            'data' => array('uid' => $order['uid'], 'value' => $value, 'overTime' => $o_trg[$order['orderId']], 'orderId' => $order['orderId'], 'orderType' => $order['orderType'], 'status' => (in_array($order['orderType'], array('1', '6'))) ? 1 : 0, 'lid' => $order['lid'], 'content' => '购买' . $lname),
                        );
                        //成长值
                        $res1 = $this->stomp->push($config['queueName'], $data, array('persistent'=>'true', 'transaction' => 't1'));
                        //积分
                        $data['ctype'] = 'point';
                        $res2 = $this->stomp->push($config['queueName'], $data, array('persistent'=>'true', 'transaction' => 't1'));
                        if($res1 && $res2)
                        {
                            $this->stomp->commit('t1');
                            $succ_orderIds[] = $order['orderId'];
                        }
                        else
                        {
                            $this->stomp->rollback('t1');
                        }
                    }
                    else 
                    {
                        $succ_orderIds[] = $order['orderId'];
                    }
                    
                    //$job_uids['11'][] = $order['uid'];
                    if(isset($this->lid_job[$order['lid']]))
                    {
                        $job_uids[$this->lid_job[$order['lid']]][] = $order['uid'];
                    }
                    //追号任务
                    if(in_array($order['orderType'], array('1', '6')))
                    {
                        $job_uids['3'][] = $order['uid'];
                    }
                   
                }
                $this->stomp->disconnect();
                
                if($succ_orderIds)
                {
                    $this->growth_cli_model->delTrgs($succ_orderIds);
                }
            }
        }
        
        //合买单操作
        $orders_trg = $this->growth_cli_model->getOrderTrg('4');
        if($orders_trg)
        {
            $config = $this->config->item('stomp');
            $connect = $this->stomp->connect();
            if($connect)
            {
                $o_trg = array();
                foreach ($orders_trg as $trg)
                {
                    $o_trg[$trg['orderId']] = $trg['modified'];
                }
                $orders = $this->growth_cli_model->getUnitedJoins(array_keys($o_trg));
                $succ_orderIds = array();
                $job_uids = array();
                foreach ($orders as $order)
                {
                    $this->stomp->begin('t2');
                    $lname = BetCnName::getCnName($order['lid']);
                    $content = "购买" . $lname;
                    $data = array(
                        'type' => 'buyLottery',
                        'ctype' => 'growth',
                        'data' => array('uid' => $order['uid'], 'value' => ($order['buyMoney']/100), 'overTime' => $o_trg[$order['orderId']], 'orderId' => $order['orderId'], 'subscribeId' => $order['subscribeId'], 'orderType' => '4', 'status' => '2', 'content' => $content),
                    );
                    //成长值
                    $res1 = $this->stomp->push($config['queueName'], $data, array('persistent'=>'true', 'transaction' => 't2'));
                    //积分
                    $data['ctype'] = 'point';
                    $res2 = $this->stomp->push($config['queueName'], $data, array('persistent'=>'true', 'transaction' => 't2'));
                    if($res1 && $res2)
                    {
                        $succ_orderIds[] = $order['orderId'];
                        $this->stomp->commit('t2');
                        //发单
                        if($order['orderType'] == '1')
                        {
                            $job_uids['4'][] = $order['uid'];
                        }
                        else 
                        {
                            //参与合买
                            $job_uids['5'][] = $order['uid'];
                        }
                    }
                    else
                    {
                        $this->stomp->rollback('t2');
                    }
                }
                $this->stomp->disconnect();
                
                if($succ_orderIds)
                {
                    $this->growth_cli_model->delTrgs($succ_orderIds);
                }
            }
        }
        
        //任务处理
        if($job_uids)
        {
            $config = $this->config->item('stomp');
            $connect = $this->stomp->connect();
            if($connect)
            {
                foreach ($job_uids as $jobId => $uids)
                {
                    $uids = array_unique($uids);
                    $jobType = 0;
                    $result = $this->growth_cli_model->getJobStatus($jobId, $jobType, $uids);
                    foreach ($result as $value)
                    {
                        if($value['jobStatus'] != '0')
                        {
                            continue;
                        }
                        
                        $data = array(
                            'type' => 'buyLottery',
                            'ctype' => 'job',
                            'data' => array('uid' => $value['uid'], 'jobId' => $jobId, 'overTime' => date('Y-m-d H:i:s'), 'jobType' => $jobType),
                        );
                        $res3 = $this->stomp->push($config['queueName'], $data, array('persistent'=>'true'));
                        if(!$res3)
                        {
                            $this->user_model->errorRecord(json_encode($data));
                        }
                    }
                }
                
                $this->stomp->disconnect();
            }
        }
    }
}