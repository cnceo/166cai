<?php

/**
 * 彩豆异步处理服务类
 * @author Administrator
 *
 */
include_once dirname(__FILE__) . '/ticket_base.php';
class ticket_caidou extends ticket_base
{
    protected $seller = 'caidou';

    private $pctype_map = array
    (
        '23529' => array('T001', 'dlt'),
        '23528' => array('F7', 'qlc'),
        '10022' => array('D7', 'qxc'),
        '21406' => array('C511', 'syxw'),
        '51'    => array('F001', 'ssq'),
        '33'    => array('D3', 'pls'),
        '35'    => array('D5', 'plw'),
        '42'    => array('3', 'jczq'),
        '43'    => array('0', 'jclq'),
        '52'    => array('F3', 'fc3d'),
        '11'    => array('D14', 'sfc'),
        '19'	=> array('D9', 'rj'),
        '44'	=> array('GJ', 'gj'),
        '45'	=> array('GYJ', 'gyj'),
        '21406' => array('56', 'syxw'),
        '21407' => array('54', 'jxsyxw'),
        '21408' => array('58', 'hbsyxw'),
        '21421' => array('55', 'gdsyxw'),
        '53'    => array('SHKS', 'ks'),
        '54'    => array('57', 'klpk')
    );
    private $ptype_map = array
    (
        'RQSPF' => array('RSPF', 1),
        'SPF' => array('SPF', 1),
        'CBF' => array('CBF', 3),
        'JQS' => array('JQS', 1),
        'BQC' => array('BQC', 3),
        'HH42' => array('ZQHH', 0),

        'SF' => array('SF', 1),
        'RFSF' => array('RFSF', 1),
        'SFC' => array('SFC', 2),
        'DXF' => array('DXF', 1),
        'HH43' => array('LQHH', 0),
    );
    protected $ggtype_map = array
    (
        '1' => '1*1',
        '2' => '2*1',
        '3' => '3*1',
        '4' => '4*1',
        '5' => '5*1',
        '6' => '6*1',
        '7' => '7*1',
        '8' => '8*1',
        '9' => '9*1',
        '10' => '10*1',
        '11' => '11*1',
        '12' => '12*1',
        '13' => '13*1',
        '14' => '14*1',
        '15' => '15*1',
        '16' => '2*3',
        '17' => '3*3',
        '18' => '3*4',
        '19' => '3*7',
        '20' => '4*4',
        '21' => '4*5',
        '22' => '4*6',
        '23' => '4*11',
        '24' => '4*15',
        '25' => '5*5',
        '26' => '5*6',
        '27' => '5*10',
        '28' => '5*16',
        '29' => '5*20',
        '30' => '5*26',
        '31' => '5*31',
        '32' => '6*6',
        '33' => '6*7',
        '34' => '6*15',
        '35' => '6*20',
        '36' => '6*22',
        '37' => '6*35',
        '38' => '6*42',
        '39' => '6*50',
        '40' => '6*57',
        '41' => '6*63',
        '42' => '7*7',
        '43' => '7*8',
        '44' => '7*21',
        '45' => '7*35',
        '46' => '7*120',
        '47' => '8*8',
        '48' => '8*9',
        '49' => '8*28',
        '50' => '8*56',
        '51' => '8*70',
        '52' => '8*247',
    );

    private $jjc_map = array
    (
        '42'    => array('10002', '30'),
        '43'    => array('10003', '31'),
        '51'	=> array('10001', '01'),
        '52'	=> array('10001', '03'),
        '23528' => array('10001', '07'),
        '23529' => array('10001', '50'),
        '10022' => array('10001', '51'),
        '44'	=> array('10006', '98', 'GJ'),
        '45'	=> array('10006', '99', 'GYJ'),
        '21406' => array('10001', '56'),
        '21407' => array('10001', '54'),
        '21408' => array('10001', '58'),
        '21421' => array('10001', '55'),
        '53'    => array('10001', '66'),
        '11'    => array('10001', '80'),
        '19'    => array('10001', '81'),
        '35'    => array('10001', '52'),
        '33'    => array('10001', '53'),
        '54'    => array('10001', '57'),
    );
    
    public function __construct()
    {
        parent::__construct();
    }

    private function cmt_comm($mdid, $body, $datas)
    {
        $lid = 0;
        if(!empty($datas['lid'])) $lid = $datas['lid'];
        if(empty($datas['msgid']))
        {
            $UId = $this->CI->tools->getIncNum('UNIQUE_KEY');
            $datas['msgid'] = $UId;
        }
        else
        {
            $UId = $datas['msgid'];
        }
        
        $header  = "<?xml version=\"1.0\" encoding=\"utf-8\"?>";
        $header .= "<request>";
        $header .= "<head sid=\"$mdid\" agent=\"" . $this->CI->config->item('cdtob_sellerid')
            .  "\" messageid=\"$UId\" timestamp=\"" . date('Y-md H:i:s') . "\" memo=\"$mdid\" />";
        $header .= "<body>";
        $header .= $body;
        $header .= "</body>";
        $header .= "</request>";
        $content['xml'] = $header;
        $content['sign'] = md5($header . $this->CI->config->item('cdtob_secret'));
        /*请求前日志记录*/
        $pathTail = "caidou$mdid/" . date('YmdH');
        if(empty($datas['batch'])) $datas['batch'] = $UId;
        $LogHead = "{$datas['batch']}-" . md5($mdid . microtime(true));
        log_message('LOG', $LogHead . $pathTail, "caidou$mdid/$mdid");
        log_message('LOG', "{$LogHead}[REQ]: " . $header, $pathTail);
        $parse_url =  parse_url($this->CI->config->item('cdtob_pji'));
        $senddatas = [
            'post_data' => $content,
            'post_ip'   => $parse_url['host'],
            'back_data' => [
                'datas' => $datas, 'callfun' => "result_$mdid", 'pathTail' => $pathTail,
                'LogHead' => $LogHead, 'failCall' => "fail_$mdid"
            ],
        ];
        $this->CI->http_client->request($this->CI->config->item('cdtob_pji'), array($this, 'ticketcallback'), $senddatas);
    }
    
    /**
     * 查询出票结果
     * @param unknown $orders
     * @param string $concel
     */
    public function med_ticketResult($orders, $concel = false)
    {
        if(!empty($orders))
        {
            $sub_order_ids = array();
            $lid = 0;
            $message_id = 0;
            foreach ($orders as $order)
            {
                array_push($sub_order_ids, $order['sub_order_id']);
                if(empty($lid) || empty($message_id))
                {
                    $lid = $order['lid'];
                    $message_id = $order['message_id'];
                }
            }
            
            $nlid = $this->jjc_map[$lid][1];
            $body = "<query gid=\"$nlid\" apply=\"" . implode(',', $sub_order_ids) . "\"/>";
            $this->cmt_comm('20008', $body, array('sub_order_ids' => $sub_order_ids, 'concel' => $concel, 'lid' => $lid, 'msgid' => $message_id));
        }
    }

    /*
     * 功能：彩豆查询出票回调函数
     * 作者：huxm
     * 日期：2016-03-09
     * */
    protected function result_20008($xmlobj, $data)
    {
        $result = (string)$xmlobj->result['code'];
        if($result == '0')
        {
            $this->CI->load->model('prcworker/ticket_caidou_model');
            $fields = array('sub_order_id', 'error_num', 'status', 'ticket_time', 'ticketId');
            $datas['s_data'] = array();
            $datas['d_data'] = array();
            $datas['relation'] = array();
            $datas['relationConcel'] = array();
            $concelIds = array();
            // 乐善奖
            $lsDatas['s_data'] = array();
            $lsDatas['d_data'] = array();
            foreach ($xmlobj->body->rows->row as $ticket)
            {
                $result = intval($ticket['state']);
                $tcode  = intval($ticket['tcode']);
                $ticket_time = date('Y-m-d H:i:s');
                $err_num = "{$result}_{$tcode}";
                if(in_array($result, array(0, 1)))   //出票中
                {
                    $status = $this->order_status['drawing'];
                    if($data['concel'])
                    {
                        $status = $this->order_status['concel'];
                        array_push($datas['relationConcel'], (string)$ticket['apply']);
                    }
                }
                elseif($result == 2 && $tcode == 0)//出票成功
                {
                    $err_num = 0;
                    $status = $this->order_status['draw'];
                    $ticket_time = "{$ticket['tdate']}";
                }
                elseif($result == 2 && in_array($tcode, array(1, 2))) //限号出票失败
                {
                    $status = $this->order_status['concel'];
                    //如果是过期主动查询时 应设置失败
                    if($data['concel'])
                    {
                        array_push($datas['relationConcel'], (string)$ticket['apply']);
                    }
                }
                if($status == $this->order_status['drawing'])
                {
                    //设置下次查询时间
                    if(in_array($data['lid'], $this->gaoping))
                    {
                        $ticket_time = 'date_add(now(), interval 10 second)';
                    }
                    else
                    {
                        $ticket_time = 'date_add(now(), interval 30 minute)';
                    }
                    array_push($datas['s_data'], "(?, ?, ?, $ticket_time, ?)");
                    array_push($datas['d_data'], "{$ticket['apply']}");
                    array_push($datas['d_data'], "{$result}_{$tcode}");
                    array_push($datas['d_data'], $status);
                    array_push($datas['d_data'], "{$ticket['tid']}");
                }
                else if(($status == $this->order_status['concel']) && empty($data['concel']))
                {
                    $concelIds[] = (string)$ticket['apply'];
                }
                else
                {
                    array_push($datas['s_data'], '(?, ?, ?, ?, ?)');
                    array_push($datas['d_data'], "{$ticket['apply']}");
                    array_push($datas['d_data'], $err_num);
                    array_push($datas['d_data'], $status);
                    array_push($datas['d_data'], $ticket_time);
                    array_push($datas['d_data'], "{$ticket['tid']}");
                }
                if(!empty($ticket['memo']))
                {
                    // 出票成功 - 大乐透乐善奖
                    if($status == $this->order_status['draw'] && $data['lid'] == '23529')
                    {
                        array_push($lsDatas['s_data'], '(?, ?, ?, ?, ?)');
                        array_push($lsDatas['d_data'], "{$ticket['apply']}");
                        array_push($lsDatas['d_data'], $data['lid']);
                        array_push($lsDatas['d_data'], $this->seller);
                        array_push($lsDatas['d_data'], "{$ticket['memo']}");
                        array_push($lsDatas['d_data'], date('Y-m-d H:i:s'));
                    }
                    else
                    {
                        $datas['relation']["{$ticket['gid']}"]["{$ticket['apply']}"] = "{$ticket['memo']}";
                    }
                }                    
            }

            // 大乐透乐善奖临时表
            if(!empty($lsDatas['s_data']))
            {
                $lsHandle = $this->CI->ticket_caidou_model->getSplitDetailSql($lsDatas, $data['lid']);
                $this->back_update('execute', $lsHandle);
            }
            
            $result = $this->CI->ticket_caidou_model->getSplitResponseSql($fields, $datas, $data['lid']);
            if($result)
            {
                //组织sql数据交给连接池处理
                $this->back_update($result['function'], $result['datas']);
            }
            else
            {
                $msg = "对messageId:{$data['msgid']}进行查票操作时合作商 {$this->seller} 返回格式解析失败 ,请及时处理。";
                $dbparams = array(
                    'db' => 'DB',
                    'sql' => $this->CI->ticket_model_order->insertAlert(),
                    'data' => array(28, $msg, '彩豆出票格式异常报警')
                );
                $this->back_update('execute', $dbparams);
            }
            
            //失败切票商操作
            if($concelIds)
            {
                $conResult = $this->CI->ticket_caidou_model->getUpdateTicket($concelIds, $data['lid']);
                foreach ($conResult as $conData)
                {
                    $this->back_update('execute', $conData);
                }
            }
        }
    }
    
    /**
     * 查询出票请求失败回调
     * @param unknown $params
     */
    protected function fail_20008($params)
    {
        //TODO  有业务逻辑处理时可处理
    }

    protected function result_10001($xmlobj, $datas)
    {
        return $this->result_betting($xmlobj, $datas);
    }

    protected function result_10002($xmlobj, $datas)
    {
        return $this->result_betting($xmlobj, $datas);
    }

    protected function result_10003($xmlobj, $datas)
    {
        return $this->result_betting($xmlobj, $datas);
    }

    protected function result_10006($xmlobj, $datas)
    {
        return $this->result_betting($xmlobj, $datas);
    }

    /**
     * 提票请求成功后处理方法
     * @param unknown $xmlobj
     * @param unknown $data
     * @return boolean
     */
    protected function result_betting($xmlobj, $data)
    {
        $suborderid = $data['sub_order_ids'];
        $lid = $data['lid'];
        $splitConcel = array();
        $messageId = (string)$xmlobj->head['messageid'];
        $result = intval($xmlobj->result['code']);
        if($result == 0)
        {
            $fields = array('sub_order_id', 'error_num', 'status', 'ticket_time', 'ticket_submit_time', 'message_id', 'ticket_flag');
            $d_data['sql'] = array();
            $d_data['data'] = array();
            $enum = 0;
            $errArr = array();
            foreach ($xmlobj->body->tickets->ticket as $ticket)
            {
                $status = $this->order_status['drawing'];
                if(intval($ticket['code']) > 1)
                {
                    $status = $this->order_status['split_ini'];
                    array_push($splitConcel, (string)$ticket['apply']);
                    array_push($errArr, intval($ticket['code']));
                }
                    
                array_push($d_data['sql'], "(?, ?, ?, date_add(now(), interval 1 minute), now(), ?, ?)");
                array_push($d_data['data'], (string)$ticket['apply']);
                array_push($d_data['data'], intval($ticket['code']));
                array_push($d_data['data'], $status);
                array_push($d_data['data'], $data['msgid']);
                array_push($d_data['data'], 2);
            }
            if(!empty($d_data['sql']))
            {
                $cfparams = array(
                    'db' => 'CF',
                    'sql' => $this->CI->ticket_model_order->ticket_succ($fields, $d_data['sql'], $lid),
                    'data' => $d_data['data']
                );
                $this->back_update('execute', $cfparams);
                
                if(!empty($splitConcel))
                {
                    $errArr = array_unique($errArr);
                    $errNum = implode(',', $errArr);
                    $msg = "对messageId:{$messageId}进行提票操作时合作商 {$this->seller} 返回状态码：{$errNum} ,请及时处理。";
                    $dbparams = array(
                        'db' => 'DB',
                        'sql' => $this->CI->ticket_model_order->insertAlert(),
                        'data' => array(4, $msg, '彩豆合作商提票报警')
                    );
                    $this->back_update('execute', $dbparams);
                }
                
            }
            return true;
        }
        else
        {
            $cfparams = array(
                'db' => 'CF',
                'sql' => $this->CI->ticket_model_order->ticket_fail($lid),
                'data' => array($data['msgid'], $result, $suborderid)
            );
            $this->back_update('execute', $cfparams);
            $msg = "对messageId:{$data['msgid']}进行提票操作时合作商 {$this->seller} 返回状态码：{$result} ,请及时处理。";
            $dbparams = array(
                'db' => 'DB',
                'sql' => $this->CI->ticket_model_order->insertAlert(),
                'data' => array(4, $msg, '彩豆合作商提票报警')
            );
            $this->back_update('execute', $dbparams);
            return false;
        }
    }
    
    /**
     * 提票失败处理
     * @param unknown $params
     * @return unknown
     */
    protected function fail_10001($params)
    {
        return $this->fail_betting($params);
    }
    
    /**
     * 提票失败处理
     * @param unknown $params
     * @return unknown
     */
    protected function fail_10002($params)
    {
        return $this->fail_betting($params);
    }
    
    /**
     * 提票失败处理
     * @param unknown $params
     * @return unknown
     */
    protected function fail_10003($params)
    {
        return $this->fail_betting($params);
    }
    
    /**
     * 提票失败处理
     * @param unknown $params
     * @return unknown
     */
    protected function fail_10006($params)
    {
        return $this->fail_betting($params);
    }
    
    /**
     * 提票失败异步回调方法
     * @param unknown $params
     */
    protected function fail_betting($params)
    {
        $cfparams = array(
            'db' => 'CF',
            'sql' => $this->CI->ticket_model_order->ticket_fail($params['lid']),
            'data' => array($params['msgid'], '', $params['sub_order_ids'])
        );
        $this->back_update('execute', $cfparams);
    }

    /**
     * 提票入口
     * @param unknown $orders
     */
    public function med_betting($orders)
    {
        if(!empty($orders))
        {
            $corders = array();
            foreach ($orders as $order)
            {
                $corders[$order['lid']][$order['issue']][] = $order;
            }
            foreach ($corders as $lid => $corder)
            {
                $mname = "lid_{$this->pctype_map[$lid][1]}";
                if(method_exists($this, $mname))
                    call_user_func_array(array($this, $mname),array($corder));
            }
        }
    }

    /**
     * 冠军彩种
     * @param unknown_type $corders
     */
    private function lid_gj($corders)
    {
        $this->lid_gyj_comm($corders, '44');
    }

    /**
     * 冠亚军操作
     * @param unknown_type $corders
     */
    private function lid_gyj($corders)
    {
        $this->lid_gyj_comm($corders, '45');
    }

    private function lid_gyj_comm($corders, $lid)
    {
        if(!empty($corders))
        {
            foreach ($corders as $issue => $orders)
            {
                $reorders = array();
                foreach ($orders as $in => $order)
                {
                    $codes = $order['codes'];
                    $codess = explode('|', $codes);
                    $betcbts = explode('=', $codess[0]);
                    $plvs = explode('/', $betcbts[1]);
                    if(!empty($plvs))
                    {
                        $newPlvs = array();
                        foreach ($plvs as $plv)
                        {
                            $cde = substr($plv, 0, 2);
                            $newPlvs[] = $cde;
                        }
                        $plvs = implode('/', $newPlvs);
                        $order['codes'] = $this->jjc_map[$lid][2] . '|' . $betcbts[0] . '=' . $plvs;
                        array_push($reorders, $order);
                    }
                }
                if(count($reorders))
                {
                    $re = $this->bdy_10006($reorders, $lid, $issue);
                }
            }
        }
    }

    /*
     * 对接彩豆的福彩3D彩种
     * */
    private function lid_fc3d($corders)
    {
        $this->lid_fcsd($corders, '52');
    }

    /*
     * 对接彩豆的福彩QLC彩种
     * */
    private function lid_qlc($corders)
    {
        $this->lid_number($corders, '23528');
    }

    private function lid_ssq($corders)
    {
        $this->lid_number($corders, '51');
    }

    /**
     * 竞彩足球提票前组织投注串
     * @param unknown $corders
     */
    private function lid_jczq($corders)
    {
        $this->lid_jjc($corders, '42');
    }

    private function lid_jclq($corders)
    {
        $this->lid_jjc($corders, '43');
    }

    private function lid_syxw($corders)
    {
        $this->lid_number($corders, '21406');
    }

    private function lid_jxsyxw($corders)
    {
        $this->lid_number($corders, '21407');
    }

    private function lid_hbsyxw($corders)
    {
        $this->lid_number($corders, '21408');
    }
    
    private function lid_gdsyxw($corders)
    {
        $this->lid_number($corders, '21421');
    }

    private function lid_ks($corders)
    {
        $this->lid_kscom($corders, '53');
    }

    private function lid_klpk($corders)
    {
        $this->lid_klpkcom($corders, '54');
    }

    private function lid_sfc($corders)
    {
        $this->lid_tcomm($corders, '11');
    }

    private function lid_rj($corders)
    {
        $this->lid_tcomm($corders, '19');
    }

    private function lid_dlt($corders)
    {
        $this->lid_number($corders, '23529');
    }

    private function lid_qxc($corders)
    {
        $this->lid_number($corders, '10022');
    }

    private function lid_plw($corders)
    {
        $this->lid_number($corders, '35');
    }

    private function lid_pls($corders)
    {
        $this->lid_number($corders, '33');
    }

    private function lid_tcomm($corders, $lid)
    {
        if(!empty($corders))
        {
            foreach ($corders as $issue => $orders)
            {
                $count = 0;
                $reorders = array();
                $issue_tmp = $this->formatIssue($issue, $lid);
                foreach ($orders as $in => $order)
                {
                    $bettype = '1';
                    if(strpos($order['codes'], ',') == true)
                    {
                        $bettype = 2;
                    }
                    $order['codes'] = str_replace(array(',', '*', '4'), array('', ',', '#'), $order['codes']) .
                        ":1:$bettype";
                    array_push($reorders, $order);
                    ++$count;
                }
                if($count > 0)
                {
                    $re = $this->bdy_10001($reorders, $lid, $issue_tmp);
                }
            }
        }
    }

    /**
     * 竞技彩组串公用方法
     * @param unknown $corders
     * @param unknown $lid
     */
    private function lid_jjc($corders, $lid)
    {
        if(!empty($corders))
        {
            foreach ($corders as $issue => $orders)
            {
                $reorders = array();
                foreach ($orders as $in => $order)
                {
                    $codes = $order['codes'];
                    $codess = explode('|', $codes);
                    $betcbts = explode('*', $codess[0]);
                    $codesData = array();
                    $ptypes = array();
                    foreach ($betcbts as $betcbt)
                    {
                        $dtmp = array();
                        $fields = explode(',', $betcbt);
                        $dtmp['mid'] = substr($fields[0], 2);
                        $dtmp['ptype'] = $this->ptype_map[trim($fields[1])][0];
                        $ptypes[] = $dtmp['ptype'];
                        $plvs = explode('/', $fields[2]);
                        if(!empty($plvs))
                        {
                            $newPlvs = array();
                            foreach ($plvs as $plv)
                            {
                                $cde = substr($plv, 0, $this->ptype_map[trim($fields[1])][1]);
                                $newPlvs[] = $cde;
                            }
                            $dtmp['plvs'] = implode('/', $newPlvs);
                        }
                        $codesData[] = $dtmp;
                    }
                    $ptypes = array_unique($ptypes);
                    $strArr = array();
                    if(count($ptypes) > 1)
                    {
                        //混合玩法
                        $codestr = $this->ptype_map["HH" . $lid][0]. "|";
                        foreach ($codesData as $val)
                        {
                            $strArr[] = $val['mid'] . '>' . $val['ptype'] . '=' . $val['plvs'];
                        }
                    }
                    else
                    {
                        $codestr = $ptypes[0] . "|";
                        foreach ($codesData as $val)
                        {
                            $strArr[] = $val['mid'] . '=' . $val['plvs'];
                        }
                    }
                    if($order['playType'] == 53){
                        $ggtype = implode(',', $this->get_ggtype($codess[1]));
                    }else{
                        $ggtype = $this->ggtype_map[$order['playType']];
                    }
                    $order['codes'] = $codestr . implode(',', $strArr) . '|' . $ggtype;
                    array_push($reorders, $order);
                }
                if(count($reorders))
                {
                    $re = $this->bdy_jjc($reorders, $lid);
                }
            }
        }
    }

    private function lid_fcsd($corders, $lid)
    {
        if(!empty($corders))
        {
            foreach ($corders as $issue => $orders)
            {
                $reorders = array();
                $issue_tmp = $this->formatIssue($issue, $lid);
                foreach ($orders as $in => $order)
                {
                    $order['codes'] = preg_replace('/\^$/is', '', $order['codes']);
                    $codes = explode('^', $order['codes']);
                    $codestrs = array();
                    if($order['playType'] == 1)
                    {
                        if(count($codes) == 1 && $order['betTnum'] > 1)
                        {
                            array_push($codestrs, str_replace(array(',', '*'), array('', ','), $codes[0]) . ":{$order['playType']}:2");
                        }
                        elseif(count($codes) > 1 || $order['betTnum'] == 1)
                        {
                            //单式
                            foreach ($codes as $code)
                            {
                                array_push($codestrs, str_replace(array('*'), array(','), $code) . ":{$order['playType']}:1");
                            }
                        }
                    }
                    elseif(in_array($order['playType'], array(2, 3)))
                    {
                        if($order['betTnum'] > 1)
                        {
                            array_push($codestrs, $codes[0] . ":{$order['playType']}:3");
                        }
                        else
                        {
                            array_push($codestrs, str_replace(array('*'), array(','), $codes[0]) . ":{$order['playType']}:1");
                        }
                    }
                    $order['codes'] = implode($codestrs, ';');
                    array_push($reorders, $order);
                }
                if(count($reorders))
                {
                    $re = $this->bdy_10001($reorders, $lid, $issue_tmp);
                }
            }
        }
    }

    private function lid_kscom($corders, $lid)
    {
        if(!empty($corders))
        {
            foreach ($corders as $issue => $orders)
            {
                $reorders = array();
                $issue_tmp = $this->formatIssue($issue, $lid);
                foreach ($orders as $in => $order)
                {
                    switch ($order['playType'])
                    {
                        case 1:
                            $cnum = intval($order['codes']) / 3;
                            if(in_array($cnum, array(1, 6)))
                            {
                                $order['codes'] = "$cnum,$cnum,$cnum:3:1";
                            }
                            else
                            {
                                $order['codes'] .= ":{$order['playType']}:4";
                            }
                            break;
                        case 2:
                        case 3:
                        case 4:
                        case 5:
                            $order['codes'] .= ":{$order['playType']}:1";
                            break;
                        case 6:
                            $betnums = array_map('trim', explode(',', $order['codes']));
                            $onums = array('1', '2', '3', '4', '5', '6');
                            $crsbnum = array_intersect($onums, $betnums);
                            if(!empty($crsbnum))
                            {
                                $order['codes'] = implode(' ', $crsbnum) . ":{$order['playType']}:1";
                            }
                            break;
                        case 7:
                            $betnums = array_map('trim', explode(',', $order['codes']));
                            $onums = array('1', '2', '3', '4', '5', '6');
                            $crsbnum = array_intersect($onums, $betnums);
                            if(count($crsbnum) == 2)
                            {
                                $vkcount = array_flip(array_count_values($betnums));
                                krsort($vkcount);
                                $order['codes'] = implode('|', $vkcount) . ":{$order['playType']}:1";
                            }
                            break;
                        case 8:
                            $betnums = array_map('trim', explode(',', $order['codes']));
                            $onums = array('1', '2', '3', '4', '5', '6');
                            $crsbnum = array_intersect($onums, $betnums);
                            if(!empty($crsbnum))
                            {
                                $order['codes'] = implode(',', $crsbnum) . ":{$order['playType']}:1";
                            }
                            break;
                        default:
                            break;
                    }
                    array_push($reorders, $order);
                }
                if(count($reorders))
                {
                    $re = $this->bdy_10001($reorders, $lid, $issue_tmp);
                }
            }
        }
    }

    private function lid_klpkcom($corders, $lid)
    {
        if(!empty($corders))
        {
            foreach ($corders as $issue => $orders)
            {
                $reorders = array();
                $issue_tmp = $this->formatIssue($issue, $lid, '20');
                foreach ($orders as $in => $order)
                {
                    switch ($order['playType'])
                    {
                        case $order['playType'] <= 6 && $order['playType'] >= 1:
                            if($order['betTnum'] > 1)
                            {
                                $codes = explode('^', $order['codes']);
                                if(isset($codes[1]))
                                {
                                    $codestrs = array();
                                    foreach ($codes as $code)
                                    {
                                        array_push($codestrs, $code . ':'.$order['playType'].':1');
                                    }
                                    $order['codes'] = implode($codestrs, ';');
                                }
                                else
                                {
                                    $order['codes'] .= ":{$order['playType']}:2";
                                }
                            }
                            else
                            {
                                $order['codes'] .= ":{$order['playType']}:1";
                            }
                            break;
                        case $order['playType'] >= 7 && $order['playType'] <= 11:
                            if($order['codes']=='00')
                            {
                                $order['codes'] = sprintf("%02d",$order['playType']).":{$order['playType']}:3";
                            }
                            else
                            {
                                if($order['betTnum'] > 1)
                                {
                                    $codes = explode('^', $order['codes']);
                                    if(isset($codes[1]))
                                    {
                                        $codestrs = array();
                                        foreach ($codes as $code)
                                        {
                                            array_push($codestrs, $code . ':'.$order['playType'].':1');
                                        }
                                        $order['codes'] = implode($codestrs, ';');
                                    }
                                    else
                                    {
                                        $order['codes'] .= ":{$order['playType']}:2";
                                    }
                                }
                                else
                                {
                                    $order['codes'] .= ":{$order['playType']}:1";
                                }
                            }
                            break;
                        case 21:
                            $order['codes'] .= ":2:2";
                            break;
                        case 31:
                            $order['codes'] .= ":3:2";
                            break;
                        case 41:
                            $order['codes'] .= ":4:2";
                            break;
                        case 51:
                            $order['codes'] .= ":5:2";
                            break;
                        case 61:
                            $order['codes'] .= ":6:2";
                            break;
                        case 22:
                            $order['codes'] = str_replace('#', '$', $order['codes']);
                            $order['codes'] .= ":2:5";
                            break;
                        case 32:
                            $order['codes'] = str_replace('#', '$', $order['codes']);
                            $order['codes'] .= ":3:5";
                            break;
                        case 42:
                            $order['codes'] = str_replace('#', '$', $order['codes']);
                            $order['codes'] .= ":4:5";
                            break;
                        case 52:
                            $order['codes'] = str_replace('#', '$', $order['codes']);
                            $order['codes'] .= ":5:5";
                            break;
                        case 62:
                            $order['codes'] = str_replace('#', '$', $order['codes']);
                            $order['codes'] .= ":6:5";
                            break;
                        default:
                            break;
                    }
                    array_push($reorders, $order);
                }
                if(count($reorders))
                {
                    $re = $this->bdy_10001($reorders, $lid, $issue_tmp);
                }
            }
        }
    }

    private function lid_number($corders, $lid)
    {
        if(!empty($corders))
        {
            foreach ($corders as $issue => $orders)
            {
                $reorders = array();
                $issue_tmp = $this->formatIssue($issue, $lid);
                foreach ($orders as $in => $order)
                {
                    $playType = 1;
                    switch ($lid)
                    {
                        case '21406':
                            $playType = $order['playType'];
                            if($playType==13)
                            {
                                $order['codes'] = str_replace('*', ',', $order['codes']);
                            }
                            else
                            {
                                $order['codes'] = str_replace('*', '|', $order['codes']);
                            }
                            break;
                        case '21407':
                        case '21408':
                            $playType = $order['playType'];
                            $order['codes'] = str_replace('*', '|', $order['codes']);
                            break;
                        case '21421':
                            $issue_tmp = $this->formatIssue($issue, $lid, '20');
                            $playType = $order['playType'];
                            $order['codes'] = str_replace('*', '|', $order['codes']);
                            break;
                        case '10022':
                        case    '35':
                            $order['codes'] = str_replace(array(',', '*'), array('', ','), $order['codes']);
                            break;
                        case 	'33':
                            $playType = $order['playType'];
                            if($playType == '1')
                            {
                                $order['codes'] = str_replace(array(',', '*'), array('', ','), $order['codes']);
                            }
                            elseif($playType == '2')
                            {
                                $order['codes'] = str_replace('*', ',', $order['codes']);
                            }
                            elseif($playType == '3')
                            {
                                $order['codes'] = str_replace('*', ',', $order['codes']);
                            }
                            break;
                        case '23529':
                            if($order['isChase'] == '1')
                            {
                                $playType = 2;
                            }
                            break;
                        default:
                            break;
                    }
                    $order['codes'] = preg_replace('/\^$/is', '', $order['codes']);
                    $codes = explode('^', $order['codes']);
                    $codestrs = array();
                    if(empty($codes[1]) && strpos($order['codes'], '#'))
                    {
                        //胆拖
                        if((strpos($order['codes'], '#') > strpos($order['codes'], '|')) && $lid =='23529')
                        {
                            $order['codes'] = "#" . $order['codes'];
                        }
                        array_push($codestrs, str_replace(array('#'), array('$'), $order['codes']) . ':'.$playType.':5');
                    }
                    elseif(!empty($codes[1]) || $order['betTnum'] == 1)
                    {
                        //单式
                        foreach ($codes as $code)
                        {
                            array_push($codestrs, $code . ':'.$playType.':1');
                        }
                    }
                    elseif(empty($codes[1]) && $order['betTnum'] > 1)
                    {
                        //排列三组三组六复式投注
                        if($lid == '33' && in_array($playType, array('2', '3')))
                        {
                            array_push($codestrs, $order['codes'] . ':'.$playType.':3');
                        }
                        else
                        {
                            array_push($codestrs, $order['codes'] . ':'.$playType.':2');
                        }
                    }
                    $order['codes'] = implode($codestrs, ';');
                    array_push($reorders, $order);
                }
                if(count($reorders))
                {
                    $re = $this->bdy_10001($reorders, $lid, $issue_tmp);
                }
            }
        }
    }

    private function formatIssue($issue, $lid, $pre='')
    {
        $issue_format = array('23529' => 0, '51' => 0, '21406' => 0, '33' => 0, '52' => 0,
            '53' => 0, '35' => 0, '10022' => 0, '23528' => 0, '11' => 0, '19' => 0, '21406' => 0,
            '21407' => 0, '21408' => 0, '21421' => 0);
        if(empty($pre))
        {
            return substr($issue, $issue_format[$lid]);
        }
        else
        {
            return "$pre$issue";
        }
    }

    private function bdy_10001($orders, $lid, $issue)
    {
        if(!empty($orders))
        {
            $sub_order_ids = array();
            $messageid = NULL;
            $body  = "<user idcard=\"\" name=\"\" mobile=\"\" />";
            $body .= "<tickets gid=\"{$this->jjc_map[$lid][1]}\" pid=\"$issue\" >";
            foreach ($orders as $order)
            {
                $sub_order_ids[] = $order['sub_order_id'];
                if(empty($messageid)) $messageid = $order['message_id'];
                $body .= "<ticket apply=\"{$order['sub_order_id']}\" codes=\"{$order['codes']}\" mulity=\"{$order['multi']}\" money=\"" . $order['money']/100 . "\" />";
            }
            $body .= "</tickets>";
            return $this->cmt_comm(10001, $body, array('sub_order_ids' => $sub_order_ids, 'msgid' => $messageid, 'lid' => $lid));
        }
    }

    /**
     * 组织彩豆通信格式参数
     * @param unknown $orders
     * @param unknown $lid
     * @return unknown
     */
    private function bdy_jjc($orders, $lid)
    {
        if(!empty($orders))
        {
            $sub_order_ids = array();
            $messageid = NULL;
            $body  = "<user idcard=\"\" name=\"\" mobile=\"\" />";
            $body .= "<tickets gid=\"{$this->jjc_map[$lid][1]}\" >";
            foreach ($orders as $order)
            {
                $sub_order_ids[] = $order['sub_order_id'];
                if(empty($messageid)) $messageid = $order['message_id'];
                $body .= "<ticket apply=\"{$order['sub_order_id']}\" codes=\"{$order['codes']}\" mulity=\"{$order['multi']}\" money=\"" . $order['money']/100 . "\" />";
            }
            $body .= "</tickets>";
            return $this->cmt_comm($this->jjc_map[$lid][0], $body, array('sub_order_ids' => $sub_order_ids, 'msgid' => $messageid, 'lid' => $lid));
        }
    }

    private function bdy_10006($orders, $lid, $issue)
    {
        if(!empty($orders))
        {
            $sub_order_ids = array();
            $messageid = NULL;
            $body  = "<user idcard=\"\" name=\"\" mobile=\"\" />";
            $body .= "<tickets gid=\"{$this->jjc_map[$lid][1]}\" pid=\"{$issue}\" >";
            foreach ($orders as $order)
            {
                $sub_order_ids[] = $order['sub_order_id'];
                if(empty($messageid)) $messageid = $order['message_id'];
                $body .= "<ticket apply=\"{$order['sub_order_id']}\" codes=\"{$order['codes']}\" mulity=\"{$order['multi']}\" money=\"" . $order['money']/100 . "\" />";
            }
            $body .= "</tickets>";
            return $this->cmt_comm($this->jjc_map[$lid][0], $body, array('sub_order_ids' => $sub_order_ids, 'msgid' => $messageid, 'lid' => $lid));
        }
    }
}
