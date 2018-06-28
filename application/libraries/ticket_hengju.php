<?php

/**
 * Copyright (c) 2014,上海二三四五网络科技股份有限公司
 * 摘    要: 银行卡验证
 * 作    者: lizq
 * 来    源: www.yinhangkahao.com/bank_luhm.html
 * 修改日期: 2014-11-25
 */

class ticket_hengju
{
    private $phone = '13636430451';
    private $id_card = '500236199011290653';
    private $real_name = '李军';
    private $seller = 'hengju';

    private $ptype_map = array
    (
        'SPF' => array('SPF', 1),
        'RQSPF' => array('RQSPF', 1),
        'CBF' => array('CBF', 3),
        'JQS' => array('JQS', 1),
        'BQC' => array('BQC', 3),
    );
    private $saleCode = array(
        '42' => array(
            'HH' => array('0' => '3602', '1' => '3602', '2' => '3603', '3' => '3604'), //0,1,2,3 单式，复式，胆拖, 自由过关
            'SPF' => array('0' => '3501', '1' => '3502', '2' => '3503', '3' => '3504'),
            'RQSPF' => array('0' => '3101', '1' => '3102', '2' => '3103', '3' => '3104'),
            'CBF' => array('0' => '3301', '1' => '3302', '2' => '3303', '3' => '3304'),
            'JQS' => array('0' => '3201', '1' => '3202', '2' => '3203', '3' => '3204'),
            'BQC' => array('0' => '3401', '1' => '3402', '2' => '3403', '3' => '3404'),
        ),
        '11' => array('0' => '1401', '1' => '1403'),
        '19' => array('0' => '901', '1' => '903'),
    );
    
    private $ggtype_map = array
    (
        '1' => '1*1',
        '2' => '2*1',
        '3' => '3*1',
        '4' => '4*1',
        '5' => '5*1',
        '6' => '6*1',
        '7' => '7*1',
        '8' => '8*1',
        '17' => '3*3',
        '18' => '3*4',
        '20' => '4*4',
        '21' => '4*5',
        '22' => '4*6',
        '23' => '4*11',
        '25' => '5*5',
        '26' => '5*6',
        '27' => '5*10',
        '28' => '5*16',
        '29' => '5*20',
        '30' => '5*26',
        '32' => '6*6',
        '33' => '6*7',
        '34' => '6*15',
        '35' => '6*20',
        '36' => '6*22',
        '37' => '6*35',
        '38' => '6*42',
        '39' => '6*50',
        '40' => '6*57',
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
    
    private $pctype_map = array
    (
        '23529' => array('DLT', 'dlt'),
        '42'    => array('JC', 'jczq'),
        '51'    => array('B001', 'ssq'),
        '56'    => array('JLK3', 'jlks'),
        '57'    => array('JXK3', 'jxks'),
        '11'    => array('SF14', 'sfc'),
        '19'    => array('RX9', 'rj'),
        '21406' => array('SD115', 'syxw'),
        '21407' => array('SYW', 'jxsyxw'),
        '21421' => array('GD115', 'gdsyxw'),
        '43'    => array('JCLQ', 'jclq'),
        '52'    => array('D3', 'fc3d'),
        '23528' => array('QL730', 'qlc'),
    );
    
    private $CI;
    public function __construct()
    {
        $this->CI = &get_instance();
        $this->CI->load->library('tools');
        $this->CI->load->model('ticket_model');
        $this->order_status = $this->CI->ticket_model->orderConfig('orders');
    }
    
    private function cmt_comm($mdid, $reqData, $datas = array())
    {
        //测试商户编号TWOTOFIVE
        if(empty($datas['msgid']))
        {
            $UId = $this->CI->tools->getIncNum('UNIQUE_KEY');
            $datas['msgid'] = $UId;
            if($mdid == '101')
            {
                $sub_order_ids = array();
                array_push($sub_order_ids, $datas['oids']['sub_order_id']);
                $this->CI->ticket_model->saveMessageId($sub_order_ids, $UId, $datas['lid'], 16);
            }
        }
        else 
        {
            $UId = $datas['msgid'];
        }
        // 请求参数组装
        $requestData = array(
            'wAgent'    =>  $this->CI->config->item('hjtob_sellerid'),
            'wAction'   =>  $mdid,
            'wMsgID'    =>  $UId,
            'wParam'    =>  $this->paramsFormat($reqData),
        );

        $requestData['wSign'] = iconv("UTF-8", "GBK", $this->getSign($requestData));
        $requestData['wParam'] = iconv("UTF-8", "GBK", $requestData['wParam']);

        /*请求前日志记录*/
        $pathTail = "hengju$mdid/" . date('YmdH');
        if(empty($datas['batch'])) $datas['batch'] = $UId;
        $LogHead = "{$datas['batch']}-" . md5($mdid . microtime(true));
        log_message('LOG', $LogHead . $pathTail, "hengju$mdid/$mdid");
        log_message('LOG', "{$LogHead}[REQ]: " . print_r($requestData, true), $pathTail);
        $result = $this->CI->tools->request($this->CI->config->item('hjtob_pji'), $requestData);

        if($this->CI->tools->recode != 200 || empty($result))
        {
            if($datas['concel'] && in_array($mdid, array('209')))
            {
                $this->ticket_model->ticketConcel_searchFail($UId, $datas['lid']);
            }
            return ;
        }

        // 返回xml转码utf-8
        $result = str_replace('gb2312', 'UTF-8', $result);
        $result = iconv('GBK', 'UTF-8', $result);

        /*请求返回日志记录*/
        log_message('LOG', "{$LogHead}[RES]: " . $result, $pathTail);
        $datas['result'] = $result;
        $xmlobj = simplexml_load_string($result);
        $rfun = "result_$mdid";
        return $this->$rfun($xmlobj, $datas);
        
    }

    // 业务参数格式化
    private function paramsFormat($reqData = array())
    {
        $params = array();
        if(!empty($reqData))
        {
            foreach ($reqData as $key => $val) 
            {
                $params[] = $key . '=' . $val;
            }
        }
        return implode('_', $params);
    }

    // 客户端签名 wAgent + wAction + wMsgID + wParam + 代理商密钥按顺序
    private function getSign($reqData)
    {
        return md5($reqData['wAgent'] . $reqData['wAction'] . $reqData['wMsgID'] . $reqData['wParam'] . $this->CI->config->item('hjtob_secret'));
    }
    
    // 出票结果的获取
    public function med_ticketResult($concel = FALSE, $lid = 0)
    {
        $tickets = $this->CI->ticket_model->getTicketResult($this->seller, $concel, $lid);
        
        if(!empty($tickets))
        {
            foreach ($tickets as $ticket)
            {
                $this->med_209($ticket['message_id'], $concel, $ticket['lid']);
            }
        }
    }

    public function med_209($messageId, $concel, $lid)
    {
        $subOrders = $this->CI->ticket_model->getSubOrdersByMsg($messageId, $concel, $lid);
        
        if(!empty($subOrders))
        {
            foreach ($subOrders as $subOrder)
            {
                $reqData = array(
                    'OrderIDs'   =>  (string)$subOrder,  
                );
                $this->cmt_comm('209', $reqData, array('concel' => $concel, 'lid' => $lid, 'msgid' => $messageId));
            }
        }
    }

    // 出票结果查询处理
    private function result_209($xmlobj, $params)
    {
        $result = (string)$xmlobj->xCode;
        $messageid = (string)$xmlobj->xMsgId;

        if($result == '0' && $messageid)
        {
            $this->CI->load->model('api_hengju_model');
            // 解析xValue
            $dJSON = $this->getXvalue($params['result']);
            $xValue = $dJSON->xValue;

            // 1-出票成功，2002-投注中，2003-已撤单，2004-方案失败
            if(!empty($xValue))
            {
                $allData = array();
                $errmsg = array();
                $delaysids = array();

                $fields = array('sub_order_id', 'error_num', 'status', 'ticket_time', 'ticketId');
                $datas[$params['lid']]['s_data'] = array();
                $datas[$params['lid']]['d_data'] = array();
                $datas[$params['lid']]['relation'] = array();
                $datas[$params['lid']]['concelIds'] = array();
                $datas[$params['lid']]['relationConcel'] = array();

                foreach (explode(',', $xValue) as $order) 
                {
                    // 投注订单号_订单状态_投注金额_中奖金额_票号_出票时间_SP值
                    $orderArr = explode('_', $order);
                    $sub_order_id = $orderArr[0];

                    $lid = $params['lid'];

                    if(!empty($sub_order_id))
                    {
                        $orderTicket = array();
                        $tickStatus = $orderArr[1];
         
                        $err_num = $tickStatus;
                        if($tickStatus == '1')
                        {
                            // 出票成功
                            $err_num = 0;
                            $status = $this->order_status['draw'];
                            if(strpos($orderArr[6], 'match') !== false)
                            {
                                $datas[$lid]['relation']["$orderArr[0]"] = $orderArr[6];
                            }
                        }
                        elseif($tickStatus == '2003' || $tickStatus == '2004')
                        {
                            // 出票失败
                            $datas[$lid]['concelIds'][] = $sub_order_id;
                            $status = $this->order_status['concel'];
                            if($params['concel'])
                            {
                                array_push($datas[$lid]['relationConcel'], $sub_order_id);
                            }
                        }
                        else
                        {
                            // 出票中
                            $status = $this->order_status['drawing'];
                            if($params['concel'])
                            {
                                $status = $this->order_status['concel'];
                                array_push($datas[$lid]['relationConcel'], $sub_order_id);
                            }
                        }

                        if($status != $this->order_status['concel'] || $params['concel'])
                        {
                            $successTime = $orderArr[5] ? date('Y-m-d H:i:s', strtotime($orderArr[5])) : '';
                            if($status == $this->order_status['drawing'])
                            {
                                // 出票中
                                $successTime = empty($successTime) ? date('Y-m-d H:i:s', time() + 60) : $successTime;
                            }
                            array_push($datas[$lid]['s_data'], '(?, ?, ?, ?, ?)');
                            array_push($datas[$lid]['d_data'], $sub_order_id);
                            array_push($datas[$lid]['d_data'], $err_num);
                            array_push($datas[$lid]['d_data'], $status);
                            array_push($datas[$lid]['d_data'], $successTime);
                            array_push($datas[$lid]['d_data'], $orderArr[4]);
                        }

                        return $this->CI->api_hengju_model->saveResponse($fields, $datas);
                    }
                }
            }
        }
    }

    // 订单中奖明细
    public function med_ticketBonus($lid = 0)
    {
        $lids = array('23529', '51', '56', '57', '11', '19', '21406', '21407');
        foreach ($lids as $mlid)
        {
            $issues = $this->CI->ticket_model->getIssuesForCpBonus($this->pctype_map[$mlid][1], $this->seller);
            if(!empty($issues))
            {
                foreach ($issues as $issue)
                {
                    $this->med_204($issue, $mlid);
                }
            }
        }
        $tickets = $this->CI->ticket_model->getTicketBonus($this->seller, $lid);
        if(!empty($tickets))
        {
            foreach ($tickets as $ticket)
            {
                $this->CI->ticket_model->saveBonustime($ticket['message_id'], $ticket['lid']);
                $this->med_111($ticket['message_id'], $ticket['lid']);
            }
        }
    }

    // 按期拉取中奖明细
    public function med_204($issue, $lid)
    {
        $reqData = array(
            'LotID'         =>  $this->pctype_map[$lid][0],
            'LotIssue'      =>  in_array($lid, array('23529', '11', '19', '21406', '21407', '21421')) ? $this->formatIssue($issue, $lid, '20') : $issue,
        );
        return $this->cmt_comm('204', $reqData, array('lid' => $lid, 'issue' => $issue));
    }

    // 按期拉取中奖明细处理
    public function result_204($xmlobj, $params)
    {
        $result = (string)$xmlobj->xCode;
        $messageid = (string)$xmlobj->xMsgId;
        $oissue = $params['issue'];
        // 排期表期次格式
        $lids = array('10022', '23529', '33', '35', '11', '19', '54', '56', '57', '21406', '21407', '21421');
        if(in_array($params['lid'], $lids))
        {
            $issue = $this->formatIssue($params['issue'], $params['lid'], '20');
        }
        else
        {
            $issue = $params['issue'];
        }
        // 0 - 所有中奖明细已出
        if($result == '0' && $messageid)
        {
            $xValue = $xmlobj->xValue;
            $start = 1;
            $pages = 1;
            if($xValue > 0)
            {
                $per = 100;
                $pages = ceil($xValue/$per);
                for($start = 1; $start <= $pages; $start++)
                {
                    $reqData = array(
                        'LotID'         =>  $this->pctype_map[$params['lid']][0],
                        'LotIssue'      =>  $issue,
                        'PageStart'     =>  (string)$start,
                        'NumPerPage'    =>  (string)$per,
                    );
                    $re205 = $this->cmt_comm('205', $reqData, array('lid' => $params['lid'], 'issue' => $params['issue']));
                    if(!$re205) break;
                }
            }

            if($xValue == 0 || $start > $pages)
            {
                $this->CI->ticket_model->trans_start();
                $re = $this->CI->ticket_model->setIssueStatus($this->pctype_map[$params['lid']][1], $oissue, $this->seller);
                $upissue = $issue;
                if (in_array($params['lid'], array('21406', '21407', '21421'))) $upissue = $oissue;
                $re1 = $this->CI->ticket_model->setCpstate($this->pctype_map[$params['lid']][1], $upissue, $this->seller);
                if($re && $re1)
                {
                    $this->CI->ticket_model->trans_complete();
                    return true;
                }
                else 
                {
                    $this->CI->ticket_model->trans_rollback();
                }
            }
        }
        return false;
        // 更新拉取时间
        // $this->CI->ticket_model->setCdBonusTime($this->pctype_map[$params['lid']][1], $oissue, $this->seller);
    }

    // 按期分页拉取
    public function result_205($xmlobj, $params)
    {
        $result = (string)$xmlobj->xCode;
        $messageid = (string)$xmlobj->xMsgId;
        $xValue = (string)$xmlobj->xValue;
        if($result == '0' && $messageid)
        {
            if(!empty($xValue))
            {
                $s_data = array();
                $d_data = array();
                $fields = array('sub_order_id', 'bonus_t', 'margin_t', 'pull_bonus_time', 'cpstate');

                foreach (explode(',', $xValue) as $order) 
                {
                    // 投注订单号_投注金额_税前奖金_税后奖金
                    $orderArr = explode('_', $order);

                    array_push($s_data, '(?, ?, ?, ?, ?)');
                    array_push($d_data, "{$orderArr[0]}");
                    array_push($d_data, ParseUnit($orderArr[2]));
                    array_push($d_data, ParseUnit($orderArr[3]));
                    array_push($d_data, date('Y-m-d H:i:s'));
                    $cpstate = 3;
                    if(in_array(intval($params['lid']), array(11,19)))
                    {
                        $cpstate = 1;
                    }
                    array_push($d_data, $cpstate);
                }
                if(!empty($s_data))
                {
                    return $this->CI->ticket_model->setCdBonus($fields, $s_data, $d_data, $params['lid']);
                }
            }
        }
    }

    // 按订单拉取中奖明细
    public function med_111($messageId, $lid)
    {
        $subOrders = $this->CI->ticket_model->getSubOrdersByMsg_bonus($messageId, $lid);

        if(!empty($subOrders))
        {
            foreach ($subOrders as $subOrder)
            {
                $reqData = array(
                    'OrderID'   =>  (string)$subOrder,
                );
                return $this->cmt_comm('111', $reqData, array('lid' => $lid, 'msgid' => $messageId));
            }
        }
        return false;
    }

    // 按订单中奖明细处理
    private function result_111($xmlobj, $params)
    {
        $result = (string)$xmlobj->xCode;
        $messageid = (string)$xmlobj->xMsgId;
        $xValue = (string)$xmlobj->xValue;
        // 1 - 未开奖, 2006 - 未中奖, 2007 - 已中奖, 2008 - 已派奖
        if(in_array($result, array('2006', '2008')) && $messageid && $xValue)
        {
            // 投注订单号_税前奖金_税后奖金_已派发奖金
            $valArr = explode('_', $xValue);

            $lid = $params['lid'];
            $data['s_datas'] = array();
            $data['d_datas'] = array();

            $cpstate = 3;
            if(in_array($lid, array('11', '19')))
            {
                $cpstate = 1;
            }
            array_push($data['s_datas'], '(?, ?, ?, ?, ?)');
            array_push($data['d_datas'], "{$valArr[0]}");
            array_push($data['d_datas'], ParseUnit($valArr[1]));
            array_push($data['d_datas'], ParseUnit($valArr[2]));
            array_push($data['d_datas'], date('Y-m-d H:i:s'));
            array_push($data['d_datas'], $cpstate);

            if(!empty($data['s_datas']))
            {
                $this->CI->ticket_model->setTicketBonus($data, $lid);
                return true;
            } 
        }
        return false;
    }
    
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
    
    //双色球
    private function lid_ssq($corders)
    {
        if(!empty($corders))
        {
            foreach ($corders as $issue => $orders)
            {
                $count = 0;
                $reorders = array();
                foreach ($orders as $in => $order)
                {
                    $codes = preg_replace('/\^$/is', '', $order['codes']);
                    $bets = explode('^', $codes);
                    // 销售代码
                    if(count($bets) <= 1 && $order['betTnum'] > 1)
                    {
                        if(strpos($codes, '#'))
                        {
                            //胆拖
                            $codes = str_replace(array(',', '#', '|'), array('', '*', '*'), $codes);
                            $codes = '1#017#' . $codes;
                        }
                        else
                        {
                            $rcodes = explode('|', $codes);
                            $redcode = count(explode(',', $rcodes[0]));
                            $bulcode = count(explode(',', $rcodes[1]));
                            $codes = str_replace(array(',', '|'), array('', '*'), $codes);
                            if($redcode >= 7 && $bulcode == 1 )
                            {
                                //红球复式
                                $codes = '1#013#' . $codes;
                            }
                            elseif($redcode == 6 && $bulcode >= 2)
                            {
                                //蓝球复式
                                $codes = '1#014#' . $codes;
                            }
                            else
                            {
                                //全复式
                                $codes = '1#016#' . $codes;
                            }
                        }
                    }
                    else
                    {
                        //单式
                        $codes = str_replace(array(',' ,'|', '^'), array('', '*', '**'), $codes);
                        $codes = '1#012#' . $codes;
                    }
                    // 倍数
                    $codes .= '#' . $order['multi'];
                    $order['codes'] = $codes;
                    
                    array_push($reorders, $order);
                    ++$count;
                }
                if($count > 0)
                {
                    $re = $this->bdy_101($reorders, 51, $issue);
                }
            }
        }
    }
    
    //胜负彩
    private function lid_sfc($corders)
    {
        $this->lid_tcomm($corders, '11');
    }
    
    //胜负彩
    private function lid_rj($corders)
    {
        $this->lid_tcomm($corders, '19');
    }

    // 大乐透提票
    private function lid_dlt($corders)
	{
		$this->lid_number($corders, '23529');
	}
    // 易快3提票
    private function lid_jlks($corders)
    {
        $this->lid_number_jlks($corders, '56');
    }
    private function lid_number_jlks($corders, $lid)
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
                    
                    switch ($order['playType'])
                    {
                        case 1:
                            $order['codes'] = '1#1901#'.$order['codes'].'#'.$order['multi'];
                            break;
                        case 2:
                            $order['codes'] = '1#1912#AAA#'.$order['multi'];
                            break;
                        case 3:
                            $order['codes'] = '1#1911#'.str_replace(',','',$order['codes']).'#'.$order['multi'];
                            break;
                        case 4:
                            $order['codes'] = '1#1931#'.str_replace(',','',$order['codes']).'#'.$order['multi'];
                            break;
                        case 5:
                            $order['codes'] = '1#1921#ABC#'.$order['multi'];
                            break;  
                        case 6:
                            $order['codes'] = '1#1942#'.substr($order['codes'],0,1).'#'.$order['multi'];
                            break;  
                        case 7:
                            $order['codes'] = '1#1941#'.str_replace(',','',$order['codes']).'#'.$order['multi'];
                            break;  
                        case 8:
                            $order['codes'] = '1#1951#'.trim(str_replace(',','',$order['codes']),'*').'#'.$order['multi'];
                            break;                  
                        default:
                            break;
                    }  
                    array_push($reorders, $order);
                    ++$count;
                }
                if($count > 0)
                {
                    $re = $this->bdy_101($reorders, $lid, $issue_tmp);
                }
            }
        }
    }
    // 数字彩提票
    private function lid_number($corders, $lid, $saleCode = NULL)
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
                    $codes = str_replace(array('#', ','), array('*', ''), $order['codes']);
                    $codes = preg_replace('/\^$/is', '', $codes);
                    $bets = explode('^', $codes);
                    // 销售代码
                    if(count($bets) <= 1 && $order['betTnum'] > 1)
                    {
                        if(strpos($codes, '*'))
                        {
                            // 胆拖
                            $codes = str_replace(array('|', '*'), array('#', '#'), $codes);
                            $codes = '24#' . $codes;
                        }
                        else
                        {
                            // 复式
                            $codes = str_replace(array('|'), array('#'), $codes);
                            $codes = '23#' . $codes;
                        }
                    }
                    else
                    {
                        // 单式
                        $codes = str_replace(array('|', '^'), array('', '#'), $codes);
                        $codes = '21#' . $codes;
                    }

                    // 倍数
                    $codes .= '#B' . $order['multi'];
                    // 追加
                    if($order['isChase'])
                    {
                        $codes .= '#Z';
                    }

                    $order['codes'] = $codes;

                    array_push($reorders, $order);
                    ++$count;
                }
                if($count > 0)
                {
                    $re = $this->bdy_101($reorders, $lid, $issue_tmp);
                }
            }
        }
    }
    
    /**
     * 老足彩组串
     * @param unknown $corders
     * @param unknown $lid
     */
    private function lid_tcomm($corders, $lid)
    {
        if(!empty($corders))
        {
            foreach ($corders as $issue => $orders)
            {
                $count = 0;
                $reorders = array();
                foreach ($orders as $in => $order)
                {
                    if(strpos($order['codes'], ',') == true)
                    {
                        //复式
                        $codes = str_replace(array(',', '*', '4'), array('', '#', '*'), $order['codes']);
                        $codes = $this->saleCode[$lid]['1'] . '#' . $codes;
                    }
                    else
                    {
                        //单式
                        $codes = str_replace(array('*', '4'), array('', '*'), $order['codes']);
                        $codes = $this->saleCode[$lid]['0'] . '#' . $codes;
                    }
                    // 倍数
                    $codes .= '#B' . $order['multi'];
                    $order['codes'] = $codes;
                    
                    array_push($reorders, $order);
                    ++$count;
                }
                
                if($count > 0)
                {
                    $re = $this->bdy_101($reorders, $lid, $issue);
                }
            }
        }
    }
        
    // 期次格式
    private function formatIssue($issue, $lid, $pre='')
    {
        $issue_format = array('23529' => 0,'56'=>0,'57'=>0);
        if(in_array($lid, array(56, 57))) $pre = ''; 
        if(empty($pre))
        {
            return substr($issue, $issue_format[$lid]);
        }
        else
        {
            return "$pre$issue";
        }
    }

    // 投注
    private function bdy_101($orders, $lid, $issue)
    {
        if(!empty($orders))
        {
            foreach ($orders as $order)
            {
                $messageid = NULL;
                if(empty($messageid)) $messageid = $order['message_id'];
                $reqData = array(
                    'OrderID'       =>  $order['sub_order_id'],
                    'LotID'         =>  $this->pctype_map[$lid][0],
                    'LotIssue'      =>  $issue,
                    'LotMoney'      =>  ParseUnit($order['money'], 1), // 单位元
                    'LotCode'       =>  $order['codes'],
                    'LotMulti'      =>  $order['multi'],
                    'OneMoney'      => ($lid == '23529' && $order['isChase']) ? 3 : 2,
                );
                $this->cmt_comm('101', $reqData, array('oids' => $order, 'lid' => $lid, 'msgid' => $messageid, 'sub_order_id' => $order['sub_order_id']));
            }   
        }
    }

    // 投注结果处理
    private function result_101($xmlobj, $params)
    {
        $this->result_betting($xmlobj, $params);
    }

    // 数字彩投注结果处理
    private function result_betting($xmlobj, $params)
    {
        
        $result = (string)$xmlobj->xCode;
        $messageid = (string)$xmlobj->xMsgId;
        if($result == '0' && $messageid == $params['msgid'])
        {
            $updatas = array('sids' => array($params['sub_order_id']));
            $this->CI->ticket_model->ticket_succ($updatas, $params['lid']);
            return true;
        }
        else
        {
            $updatas = array('sids' => array($params['sub_order_id']), 'error' => $result);
            $this->CI->ticket_model->ticket_fail($updatas, $params['lid']);
            $msg = "对messageId:{$params['msgid']}进行提票操作时合作商 {$this->seller} 返回状态码：{$result} ,请及时处理。";
            $this->CI->ticket_model->insertAlert(4, $params['msgid'], $msg, '恒钜合作商提票报警');
            return false;
        }
    }
    
    /**
     * 竞彩足球提票
     * @param unknown $corders
     */
    private function lid_jczq($corders)
    {
        if(!empty($corders))
        {
            foreach ($corders as $issue => $orders)
            {
                $count = 0;
                $reorders = array();
                foreach ($orders as $in => $order)
                {
                    $codes = $order['codes'];
                    $codess = explode('|', $codes);
                    $betcbts = explode('*', $codess[0]);
                    $coArr = array();
                    $lotteryid = $this->pctype_map[$order['lid']]['0'];
                    $saleCode = $this->saleCode[$order['lid']]['HH']['1']; //默认销售代码为混合  单关需要特殊处理
                    foreach ($betcbts as $betcbt)
                    {
                        $fields = explode(',', $betcbt);
                        $ccid = substr($fields[0], 2);
                        if($order['playType'] == '1')
                        {
                            //单关特殊处理
                            if($order['betTnum'] == '1')
                            {
                                //单式票
                                $saleCode = $this->saleCode[$order['lid']][$fields['1']]['0'];
                            }
                            else
                            {
                                //复式票
                                $saleCode = $this->saleCode[$order['lid']][$fields['1']]['1'];
                            }
                            
                            $codStr = $ccid;
                        }
                        else 
                        {
                            $codStr = $this->ptype_map[$fields['1']]['0'] . '>' . $ccid;
                        }
                        $plvs = explode('/', $fields[2]);
                        $zmcde = '';
                        foreach ($plvs as $plv)
                        {
                            $cde = substr($plv, 0, $this->ptype_map[$fields['1']]['1']);
                            $zmcde .= $cde . '/';
                        }
                        
                        $zmcde = preg_replace('/\/$/is', '', $zmcde);
                        $codStr .= '=' . $zmcde;
                        array_push($coArr, $codStr);
                    }
                    $lotCode = implode(',', $coArr);
                    //自由过关
                    $ggtype = $this->ggtype_map[$order['playType']];
                    if($order['playType'] == 53){
                        $saleCode = $this->saleCode[$order['lid']]['HH']['3'];
                        $ggtype = implode('/', $this->get_ggtype($codess[1]));
                    }
                    $order['codes'] = $saleCode . '#' . $lotCode . '|' . $ggtype . '#B' . $order['multi'];
                    array_push($reorders, $order);
                    ++$count;
                }
                if($count > 0)
                {
                    $re = $this->bdy_101($reorders, 42, date('Ymd', time()));
                }
            }
        }
    }
    //开奖结果
    public function med_kjResult($issue, $lid)
    {
        $this->med_110($issue, $lid);
    }
    /**
     * [med_110 开奖号码拉取]
     * @author LiKangJian 2017-11-13
     * @param  [type] $issue [description]
     * @param  [type] $lid   [description]
     * @return [type]        [description]
     */
    private function med_110($issue, $lid)
    {
        $reqData = array(
            'LotID'         =>  $this->pctype_map[$lid][0],
            'LotIssue'      =>  $issue,
        ); 
        $this->cmt_comm('110', $reqData,array('lid'=>$lid,'issue'=>$issue));
        
    }
    /**
     * [result_110 开奖号处理]
     * @author LiKangJian 2017-11-13
     * @param  [type] $xmlobj [description]
     * @param  [type] $params [description]
     * @return [type]         [description]
     */
    private function result_110($xmlobj, $params)
    {
        $result = (string)$xmlobj->xCode;
        $xValue = explode('_', (string)$xmlobj->xValue);
        $awardNum = trim(str_replace('0', ',', $xValue[1]),',');
        $issue = $params['issue'];
        $lid = $params['lid'];
        $lname = $this->pctype_map[$lid][1];
        if($result == 0)
        {
            if($awardNum != '')
            {
                $bonusDetail = $this->getBonusDetail($lid);
                if(in_array($lid, array(56, 57)))
                {
                    $arrs = explode(',', $awardNum);  
                    sort($arrs);
                    $awardNum = implode(',', $arrs);
                }
                $data = array($awardNum, json_encode($bonusDetail), $issue);
                $this->CI->ticket_model->updateByIssue($data, $lname);
                //启动同步号码任务
                $this->CI->ticket_model->updateStop(1, $lid, 0);
            }
        }
        $this->CI->ticket_model->updateTryNum($issue, $lname);
    }
    public function getXvalue($xml)
    {
        $obj = simplexml_load_string($xml, 'SimpleXMLElement', LIBXML_NOCDATA);
        $eJSON = json_encode($obj);
        $dJSON = json_decode($eJSON);
        return $dJSON;
    }

    private function get_ggtype($codes)
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
    
    //当前期次
    public function med_104($lid)
    {
        $reqData = array(
            'LotID'         =>  $this->pctype_map[$lid][0],
        ); 
        return $this->cmt_comm('104', $reqData,array('lid'=>$lid,'issue'=>$issue));
    }
    private function result_104($xmlobj, $params)
    {
        $result = (string)$xmlobj->xCode;
        $xValue = explode('_', (string)$xmlobj->xValue);
        return $xValue[0];
    }
    /**
     * [getBonusDetail 奖集]
     * @author LiKangJian 2017-11-13
     * @param  [type] $lid [description]
     * @return [type]      [description]
     */
    private function getBonusDetail($lid)
    {
            $bonusDetail = array();
            switch ($lid) 
            {

                    case 53:
                            $detail['hz']['z4'] = '80';
                            $detail['hz']['z5'] = '40';
                            $detail['hz']['z6'] = '25';
                            $detail['hz']['z7'] = '16';
                            $detail['hz']['z8'] = '12';
                            $detail['hz']['z9'] = '10';
                            $detail['hz']['z10'] = '9';
                            $detail['hz']['z11'] = '9';
                            $detail['hz']['z12'] = '10';
                            $detail['hz']['z13'] = '12';
                            $detail['hz']['z14'] = '16';
                            $detail['hz']['z15'] = '25';
                            $detail['hz']['z16'] = '40';
                            $detail['hz']['z17'] = '80';
                            $detail['sthtx'] = '40';
                            $detail['sthdx'] = '240';
                            $detail['sbth'] = '40';
                            $detail['slhtx'] = '10';
                            $detail['ethfx'] = '15';
                            $detail['ethdx'] = '80';
                            $detail['ebth'] = '8';
                            $bonusDetail = $detail;
                            break;
                    case 56:
                    case 57:
                            $detail['hz']['z4'] = '80';
                            $detail['hz']['z5'] = '40';
                            $detail['hz']['z6'] = '25';
                            $detail['hz']['z7'] = '16';
                            $detail['hz']['z8'] = '12';
                            $detail['hz']['z9'] = '10';
                            $detail['hz']['z10'] = '9';
                            $detail['hz']['z11'] = '9';
                            $detail['hz']['z12'] = '10';
                            $detail['hz']['z13'] = '12';
                            $detail['hz']['z14'] = '16';
                            $detail['hz']['z15'] = '25';
                            $detail['hz']['z16'] = '40';
                            $detail['hz']['z17'] = '80';
                            $detail['sthtx'] = '40';
                            $detail['sthdx'] = '240';
                            $detail['sbth'] = '40';
                            $detail['slhtx'] = '10';
                            $detail['ethfx'] = '15';
                            $detail['ethdx'] = '80';
                            $detail['ebth'] = '8';
                            $bonusDetail = $detail;
                            break;
                    default:
                            break;
            }
            return $bonusDetail;
    } 

    /**
     * 获取吉林快三期次
     * @param int $lid
     */
    public function met_getIssue($lid)
    {
        $this->med_116($lid);
    }
    
    private function med_116($lid)
    {
        $reqData = array(
            'LotID' => $this->pctype_map[$lid][0],
        );
        return $this->cmt_comm('116', $reqData, array('lid' => $lid));
    }
    
    public function result_116($xmlobj, $params)
    {
        $result = (string) $xmlobj->xCode;
        $messageid = (string) $xmlobj->xMsgId;
        if ($result == 0) {
            $times = (string) $xmlobj->xValue;
            $times = explode(',', $times);
        }
        $issues = array();
        foreach ($times as $time) {
            $issue = explode('_', $time);
            if (!empty($issue[0])) {
                $issues[$issue[0]]['start'] = $issue[5];
                $issues[$issue[0]]['end'] = $issue[6];
            }
        }
        $this->CI->load->model('issue_model');
        $this->CI->issue_model->compareIssue($params['lid'], $issues);
    }   

    // 新订单中奖明细
    public function med_getTicketBonus($lid = 0)
    {
        // 票商不支持彩种处理
        if(empty($this->pctype_map[$lid]))
        {
            return false;
        }

        $handleCount = 0;
        if(in_array($lid, array(42, 43)))
        {
            // 按单处理
            $handle = true;
            $tickets = $this->CI->ticket_model->getTicketBonusByLid($this->seller, $lid);
            while ($handle && !empty($tickets)) 
            {
                $succNum = 0;
                foreach ($tickets as $ticket)
                {
                    $this->CI->ticket_model->saveBonustime($ticket['message_id'], $ticket['lid']);
                    $ticketRes = $this->med_111($ticket['message_id'], $ticket['lid']);
                    if($ticketRes)
                    {
                        $succNum ++;
                    }
                }
                if($succNum > 0)
                {
                    $handleCount ++;
                    // 继续循环
                    $tickets = $this->CI->ticket_model->getTicketBonusByLid($this->seller, $lid);
                }
                else
                {
                    // 退出循环
                    $handle = false;
                }
            }
        }
        else
        {
            // 按期次处理
            $issues = $this->CI->ticket_model->getIssuesByCdBonus($this->pctype_map[$lid][1], $this->seller);
            if(!empty($issues))
            {
                foreach ($issues as $issue)
                {
                    $splitIssue = $this->CI->ticket_model->paiqiToSplit($issue, $lid);
                    $counts = $this->CI->ticket_model->countTicketDetailByLid($lid, $splitIssue, $this->seller);
                    if($counts > 0)
                    {
                        $ticketRes = $this->med_204($issue, $lid);
                        if($ticketRes)
                        {
                            $handleCount ++;
                        }
                    }
                    else
                    {
                        $this->CI->ticket_model->setIssueStatus($this->pctype_map[$lid][1], $issue, $this->seller);
                    }     
                }
            }
        }
        if($handleCount > 0)
        {
            return true;
        }
        else
        {
            return false;
        }
    } 
}
