<?php

/**
 * 恒钜异步处理服务类
 * @author Administrator
 *
 */
include_once dirname(__FILE__) . '/ticket_base.php';
class ticket_hengju extends ticket_base
{
    protected $seller = 'hengju';

    private $ptype_map = array
    (
        'SPF' => array('SPF', 1),
        'RQSPF' => array('RQSPF', 1),
        'CBF' => array('CBF', 3),
        'JQS' => array('JQS', 1),
        'BQC' => array('BQC', 3),

        'SF' => array('SF', 1),
        'RFSF' => array('RFSF', 1),
        'SFC' => array('SFC', 2),
        'DXF' => array('DXF', 1),
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
        '43' => array(
            'HH' => array('0' => '6501', '1' => '6501', '2' => '6502', '3' => '6504'), //0,1,2,3 单式，复式，胆拖, 自由过关
            'SF' => array('0' => '6101', '1' => '6101', '2' => '6102', '3' => '6104'),
            'RFSF' => array('0' => '6201', '1' => '6201', '2' => '6202', '3' => '6204'),
            'SFC' => array('0' => '6301', '1' => '6301', '2' => '6302', '3' => '6304'),
            'DXF' => array('0' => '6401', '1' => '6401', '2' => '6402', '3' => '6404'),
        ),
        '21406' => array(
            '1'  => array('0' => '11051', '1' => '11051'),
            '2'  => array('0' => '11052', '1' => '11052', '2' => '11062'),
            '3'  => array('0' => '11053', '1' => '11053', '2' => '11063'),
            '4'  => array('0' => '11054', '1' => '11054', '2' => '11064'),
            '5'  => array('0' => '11055', '1' => '11055', '2' => '11065'),
            '6'  => array('0' => '11056', '1' => '11056', '2' => '11066'),
            '7'  => array('0' => '11057', '1' => '11057', '2' => '11067'),
            '8'  => array('0' => '11058', '1' => '11058', '2' => '11068'),
            '9'  => array('0' => '11012', '1' => '11022'),
            '10' => array('0' => '11013', '1' => '11023'),
            '11' => array('0' => '11032', '1' => '11032', '2' => '11042'),
            '12' => array('0' => '11033', '1' => '11033', '2' => '11043'),
            '13' => array('0' => '11071'),
            '14' => array('0' => '11072'),
            '15' => array('0' => '11073'),
        ),
        '21407' => array(
            '1'  => array('0' => '1151', '1' => '1151'),
            '2'  => array('0' => '1152', '1' => '1152', '2' => '1162'),
            '3'  => array('0' => '1153', '1' => '1153', '2' => '1163'),
            '4'  => array('0' => '1154', '1' => '1154', '2' => '1164'),
            '5'  => array('0' => '1155', '1' => '1155', '2' => '1165'),
            '6'  => array('0' => '1156', '1' => '1156', '2' => '1166'),
            '7'  => array('0' => '1157', '1' => '1157', '2' => '1167'),
            '8'  => array('0' => '1158', '1' => '1158', '2' => '1168'),
            '9'  => array('0' => '1112', '1' => '1122'),
            '10' => array('0' => '1113', '1' => '1123'),
            '11' => array('0' => '1132', '1' => '1132', '2' => '1142'),
            '12' => array('0' => '1133', '1' => '1133', '2' => '1143'),
        ),
        '21421' => array(
            '1'  => array('0' => '11151', '1' => '11151'),
            '2'  => array('0' => '11152', '1' => '11152', '2' => '11162'),
            '3'  => array('0' => '11153', '1' => '11153', '2' => '11163'),
            '4'  => array('0' => '11154', '1' => '11154', '2' => '11164'),
            '5'  => array('0' => '11155', '1' => '11155', '2' => '11165'),
            '6'  => array('0' => '11156', '1' => '11156', '2' => '11166'),
            '7'  => array('0' => '11157', '1' => '11157', '2' => '11167'),
            '8'  => array('0' => '11158', '1' => '11158'),
            '9'  => array('0' => '11112', '1' => '11122'),
            '10' => array('0' => '11113', '1' => '11123'),
            '11' => array('0' => '11132', '1' => '11132', '2' => '11142'),
            '12' => array('0' => '11133', '1' => '11133', '2' => '11143'),
        ),
        '11' => array('0' => '1401', '1' => '1403'),
        '19' => array('0' => '901', '1' => '903'),
        '52' => array(
            '1' => array('0' => '0301', '1' => '0330'),
            '2' => array('0' => '0303', '1' => '0343'),
            '3' => array('0' => '0306', '1' => '0346'),
        ),
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
        '21406' => array('SD115', 'syxw'),
        '21407' => array('SYW', 'jxsyxw'),
        '19'    => array('RX9', 'rj'),
        '21421' => array('GD115', 'gdsyxw'),
        '43'    => array('JCLQ', 'jclq'),
        '52'    => array('D3', 'fc3d'),
        '23528' => array('QL730', 'qlc'),
    );
    
    public function __construct()
    {
        parent::__construct();
    }
    
    private function cmt_comm($mdid, $reqData, $datas = array())
    {
        if(empty($datas['msgid']))
        {
            $UId = $this->CI->tools->getIncNum('UNIQUE_KEY');
            $datas['msgid'] = $UId;
        }
        else
        {
            $UId = $datas['msgid'];
        }

        $wPara = ($mdid == '1101') ? $reqData : $this->paramsFormat($reqData);
        // 请求参数组装
        $requestData = array(
            'wAgent'    =>  $this->CI->config->item('hjtob_sellerid'),
            'wAction'   =>  $mdid,
            'wMsgID'    =>  $UId,
            'wParam'    =>  $wPara,
        );

        $requestData['wSign'] = iconv("UTF-8", "GBK", $this->getSign($requestData));
        $requestData['wParam'] = iconv("UTF-8", "GBK", $requestData['wParam']);

        /*请求前日志记录*/
        $pathTail = "hengju$mdid/" . date('YmdH');
        if(empty($datas['batch'])) $datas['batch'] = $UId;
        $LogHead = "{$datas['batch']}-" . md5($mdid . microtime(true));
        log_message('LOG', $LogHead . $pathTail, "hengju$mdid/$mdid");
        log_message('LOG', "{$LogHead}[REQ]: " . print_r($requestData, true), $pathTail);
        $parse_url =  parse_url($this->CI->config->item('hjtob_pji'));
        $senddatas = [
            'post_data' => $requestData,
            'post_ip'   => $parse_url['host'],
            'back_data' => [
                'datas' => $datas, 'callfun' => "result_$mdid", 'pathTail' => $pathTail,
                'LogHead' => $LogHead, 'failCall' => "fail_$mdid"
            ],
        ];

        $this->CI->http_client->request($this->CI->config->item('hjtob_pji'), array($this, 'ticketcallback'), $senddatas);
        
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
    public function med_ticketResult($orders, $concel = false)
    {
        if(!empty($orders))
        {
            $sub_order_ids = array();
            foreach ($orders as $order)
            {
                array_push($sub_order_ids, (string)$order['sub_order_id']);
            }
            $orderIds = implode(',', $sub_order_ids);
            $reqData = array(
                'OrderIDs' => $orderIds,
            );
            $this->cmt_comm('209', $reqData, array('sub_order_ids' => array($orderIds), 'concel' => $concel, 'lid' => $order['lid'], 'msgid' => $order['message_id']));
        }
    }

    // 出票结果查询处理
    protected function result_209($xmlobj, $params)
    {
        $result = (string)$xmlobj->xCode;
        $messageid = (string)$xmlobj->xMsgId;

        if($result == '0' && $messageid)
        {
            $this->CI->load->model('prcworker/ticket_hengju_model');
            $xValue = (string)$xmlobj->xValue;

            // 1-出票成功，2002-投注中，2003-已撤单，2004-方案失败
            if(!empty($xValue))
            {
                $fields = array('sub_order_id', 'error_num', 'status', 'ticket_time', 'ticketId');
                $datas['s_data'] = array();
                $datas['d_data'] = array();
                $datas['relation'] = array();
                $datas['relationConcel'] = array();
                $concelIds = array();
                // 乐善奖
                $lsDatas['s_data'] = array();
                $lsDatas['d_data'] = array();
                foreach (explode(',', $xValue) as $order) 
                {
                    // 投注订单号_订单状态_投注金额_中奖金额_票号_出票时间_SP值/乐善码
                    $lsCode = '';
                    $orderArr = explode('_', $order);
                    
                    $sub_order_id = $orderArr[0];

                    $lid = $params['lid'];

                    if(!empty($sub_order_id))
                    {
                        $orderTicket = array();
                        $tickStatus = $orderArr[1];
         
                        $err_num = $tickStatus;
                        if( in_array($tickStatus, array('1', '2006', '2007', '2008')))
                        {
                            // 出票成功
                            $err_num = 0;
                            $status = $this->order_status['draw'];
                            if(strpos($orderArr[6], 'match') !== false)
                            {
                                $datas['relation'][$lid]["$orderArr[0]"] = $orderArr[6];
                            }
                        }
                        elseif($tickStatus == '2003' || $tickStatus == '2004')
                        {
                            $status = $this->order_status['concel'];
                            if($params['concel'])
                            {
                                array_push($datas['relationConcel'], $sub_order_id);
                            }else{
                                // 出票失败切换票商
                                $concelIds[] = $sub_order_id;
                            }
                        }
                        else
                        {
                            // 出票中
                            $status = $this->order_status['drawing'];
                            if($params['concel'])
                            {
                                $status = $this->order_status['concel'];
                                array_push($datas['relationConcel'], $sub_order_id);
                            }
                        }

                        // 乐善码
                        if(!empty($orderArr[6]) && (strpos($orderArr[6], '#') !== false) && $params['lid'] == '23529')
                        {
                            $lsArr = explode('#', $orderArr[6]);
                            $numArr = str_split($lsArr[1], 2);
                            $lsCode = implode(',', array_slice($numArr, 0, 5)) . '|' . implode(',', array_slice($numArr, 5, 2));
                        }

                        if($status != $this->order_status['concel'] || $params['concel'])
                        {
                            $successTime = $orderArr[5] ? date('Y-m-d H:i:s', strtotime($orderArr[5])) : '';
                            if($status == $this->order_status['drawing'])
                            {
                                // 出票中
                                $time = in_array($params['lid'], $this->gaoping) ? 10 : 1800;
                                $successTime = empty($successTime) ? date('Y-m-d H:i:s', time() + $time) : $successTime;
                            }
                            array_push($datas['s_data'], '(?, ?, ?, ?, ?)');
                            array_push($datas['d_data'], $sub_order_id);
                            array_push($datas['d_data'], $err_num);
                            array_push($datas['d_data'], $status);
                            array_push($datas['d_data'], $successTime);
                            array_push($datas['d_data'], $orderArr[4]);
                        }

                        // 出票成功 - 大乐透乐善奖
                        if($status == $this->order_status['draw'] && !empty($lsCode) && $params['lid'] == '23529')
                        {
                            array_push($lsDatas['s_data'], '(?, ?, ?, ?, ?)');
                            array_push($lsDatas['d_data'], $sub_order_id);
                            array_push($lsDatas['d_data'], $params['lid']);
                            array_push($lsDatas['d_data'], $this->seller);
                            array_push($lsDatas['d_data'], $lsCode);
                            array_push($lsDatas['d_data'], date('Y-m-d H:i:s'));
                        }
                    }
                }

                // 大乐透乐善奖临时表
                if(!empty($lsDatas['s_data']))
                {
                    $lsHandle = $this->CI->ticket_hengju_model->getSplitDetailSql($lsDatas, $params['lid']);
                    $this->back_update('execute', $lsHandle);
                }
                
                $result = $this->CI->ticket_hengju_model->getSplitResponseSql($fields, $datas, $params['lid']);
                if($result)
                {
                    //组织sql数据交给连接池处理
                    $this->back_update($result['function'], $result['datas']);
                }
                else
                {
                    $msg = "对messageId:{$params['msgid']}进行查票操作时合作商 {$this->seller} 返回格式解析失败 ,请及时处理。";
                    $dbparams = array(
                        'db' => 'DB',
                        'sql' => $this->CI->ticket_model_order->insertAlert(),
                        'data' => array(28, $msg, '恒钜出票格式异常报警')
                    );
                    $this->back_update('execute', $dbparams);
                }
                
                //失败切票商操作
                if($concelIds)
                {
                    $conResult = $this->CI->ticket_hengju_model->getUpdateTicket($concelIds, $params['lid']);
                    foreach ($conResult as $conData)
                    {
                        $this->back_update('execute', $conData);
                    }
                }
            }
        }
    }
    
    /**
     * 查询出票请求失败回调
     * @param unknown $params
     */
    protected function fail_209($params)
    {
        //TODO  有业务逻辑处理时可处理
    }
    
    /**
     * 提票
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
                    $re = $this->bdy_1101($reorders, 51, $issue);
                }
            }
        }
    }
    
    // 易快3提票
    private function lid_jlks($corders)
    {
        $this->lid_number_jlks($corders, '56');
    }
    
    // 红快3提票
    private function lid_jxks($corders)
    {
        $this->lid_number_jxks($corders, '57');
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
	
	private function lid_syxw($corders)
	{
	    $this->lid_syxwcomm($corders, '21406');
	}
	
	private function lid_jxsyxw($corders)
	{
	    $this->lid_syxwcomm($corders, '21407');
	}
	private function lid_gdsyxw($corders)
	{
	    $this->lid_syxwcomm($corders, '21421');
	}
	
	/**
	 * 福彩3D
	 * @param unknown $corders
	 */
	private function lid_fc3d($corders)
	{
	    if(!empty($corders)) {
	        foreach ($corders as $issue => $orders) {
	            $reorders = array();
	            foreach ($orders as $in => $order) {
	                $order['codes'] = preg_replace('/\^$/is', '', $order['codes']);
	                $codes = str_replace(',', '', $order['codes']);
	                $bets = explode('^', $codes);
	                //复式
	                if(count($bets) == 1 && $order['betTnum'] > 1) {
	                    $codes = '1#' . $this->saleCode['52'][$order['playType']]['1'] . '#' . $codes . '#' . $order['multi'];
	                } else {
	                    //单式
	                    $codes = implode('^', $bets);
	                    $codes = '1#' . $this->saleCode['52'][$order['playType']]['0'] . '#' . str_replace(array('*', '^'), array('', '**'), $codes) . '#' . $order['multi'];
	                }
	                
	                $order['codes'] = $codes;
	                array_push($reorders, $order);
	            }
	            
	            if ($reorders) {
	                $re = $this->bdy_1101($reorders, 52, $issue);
	            }
	        }
	    }
	}
	
	/**
	 * 七乐彩
	 */
	private function lid_qlc($corders) 
	{
	    if(!empty($corders)) {
	        foreach ($corders as $issue => $orders) {
	            $reorders = array();
	            foreach ($orders as $in => $order) {
	                $order['codes'] = preg_replace('/\^$/is', '', $order['codes']);
	                $codes = str_replace(array('#', ','), array('*', ''), $order['codes']);
	                $bets = explode('^', $codes);
	                if(count($bets) <= 1 && $order['betTnum'] > 1) {
	                    if(strpos($codes, '*')) {
	                        // 胆拖
	                        $codes = '1#027#' . $codes . '#' . $order['multi'];
	                    } else {
	                        // 复式
	                        $codes = '1#023#' . $codes . '#' . $order['multi'];
	                    }
	                } else {
	                    // 单式
	                    $codes = implode('**', $bets);
	                    $codes = '1#022#' . $codes . '#' . $order['multi'];
	                }
	                
	                $order['codes'] = $codes;
	                array_push($reorders, $order);
	            }
	            
	            if($reorders) {
	                $re = $this->bdy_1101($reorders, 23528, $issue);
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
                    $re = $this->bdy_1101($reorders, $lid, $issue_tmp);
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
                    $re = $this->bdy_1101($reorders, $lid, $issue);
                }
            }
        }
    }
    
    private function lid_syxwcomm($corders, $lid) {
        if(!empty($corders))
        {
            foreach ($corders as $issue => $orders)
            {
                $count = 0;
                $reorders = array();
                $issue_tmp = $this->formatIssue($issue, $lid, '20');
                foreach ($orders as $in => $order)
                {
                    $codes = str_replace(array(','), array(''), $order['codes']);
                    $codes = preg_replace('/\^$/is', '', $codes);
                    $bets = explode('^', $codes);
                    // 销售代码
                    if(count($bets) <= 1 && $order['betTnum'] > 1)
                    {
                        if(strpos($codes, '#'))
                        {
                            // 胆拖
                            $codes = str_replace(array(','), array(''), $codes);
                            $codes = $codes = $this->saleCode[$lid][$order['playType']]['2'] . '#' . $codes;
                        }
                        else
                        {
                            // 复式
                            $codes = str_replace(array('*'), array('#'), $codes);
                            $codes = $codes = $this->saleCode[$lid][$order['playType']]['1'] . '#' . $codes;
                        }
                    }
                    else
                    {
                        // 单式
                        $codes = str_replace(array('*', '^'), array('', '#'), $codes);
                        $codes = $codes = $this->saleCode[$lid][$order['playType']]['0'] . '#' . $codes;
                    }
        
                    // 倍数
                    $codes .= '#B' . $order['multi'];
        
                    $order['codes'] = $codes;
        
                    array_push($reorders, $order);
                    ++$count;
                }
                if($count > 0)
                {
                    $re = $this->bdy_1101($reorders, $lid, $issue_tmp);
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
                    $re = $this->bdy_1101($reorders, $lid, $issue_tmp);
                }
            }
        }
    }
    
    private function lid_number_jxks($corders, $lid)
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
                            $order['codes'] = '1#2901#'.$order['codes'].'#'.$order['multi'];
                            break;
                        case 2:
                            $order['codes'] = '1#2912#AAA#'.$order['multi'];
                            break;
                        case 3:
                            $order['codes'] = '1#2911#'.str_replace(',','',$order['codes']).'#'.$order['multi'];
                            break;
                        case 4:
                            $order['codes'] = '1#2931#'.str_replace(',','',$order['codes']).'#'.$order['multi'];
                            break;
                        case 5:
                            $order['codes'] = '1#2921#ABC#'.$order['multi'];
                            break;
                        case 6:
                            $order['codes'] = '1#2942#'.substr($order['codes'],0,1).'#'.$order['multi'];
                            break;
                        case 7:
                            $order['codes'] = '1#2941#'.str_replace(',','',$order['codes']).'#'.$order['multi'];
                            break;
                        case 8:
                            $order['codes'] = '1#2951#'.trim(str_replace(',','',$order['codes']),'*').'#'.$order['multi'];
                            break;
                        default:
                            break;
                    }
                    array_push($reorders, $order);
                    ++$count;
                }
                if($count > 0)
                {
                    $re = $this->bdy_1101($reorders, $lid, $issue_tmp);
                }
            }
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
                $this->cmt_comm('101', $reqData, array('sub_order_ids' => array($order['sub_order_id']), 'lid' => $lid, 'msgid' => $messageid));
            }   
        }
    }
    
    // 批量投注
    private function bdy_1101($orders, $lid, $issue)
    {
        if(!empty($orders))
        {
            $sub_order_id = array();
            $reqDatas = array();
            foreach ($orders as $k=>$order)
            {
                //恒钜最大20个
                if($k < 20){
                    $messageid = $order['message_id'];
                    array_push($sub_order_id, $order['sub_order_id']);
                    $reqData = array(
                        'OrderID'       =>  $order['sub_order_id'],
                        'LotID'         =>  $this->pctype_map[$lid][0],
                        'LotIssue'      =>  $issue,
                        'LotMoney'      =>  ParseUnit($order['money'], 1), // 单位元
                        'LotCode'       =>  $order['codes'],
                        'LotMulti'      =>  $order['multi'],
                        'OneMoney'      => ($lid == '23529' && $order['isChase']) ? 3 : 2,
                    );
                    $reqDatas[] = $this->paramsFormat($reqData);
                }
            }
            $reqDatas = implode('^', $reqDatas);
            $this->cmt_comm('1101', $reqDatas, array('sub_order_ids' => $sub_order_id, 'lid' => $lid, 'msgid' => $messageid));
        }
    }
    
    // 投注结果处理
    protected function result_1101($xmlobj, $params)
    {
        $this->result_betting($xmlobj, $params);
    }

    // 数字彩投注结果处理
    protected function result_betting($xmlobj, $params)
    {
        $result = (string)$xmlobj->xCode;
        $messageid = (string)$xmlobj->xMsgId;
        $xValue = (string)$xmlobj->xValue;
        $orders = explode(',', $xValue);
        //0 成功  1008 已存在
        if(($result == '0' || $result == '1008') && $messageid == $params['msgid'])
        {
            $errArr = array();
            $fields = array('sub_order_id', 'error_num', 'status', 'ticket_time', 'ticket_submit_time', 'message_id', 'ticket_flag');
            $d_data['sql'] = array();
            $d_data['data'] = array();
            foreach ($orders as $order)
            {
                $values = explode('_', $order);
                $err_num = $values[1];
                if($err_num == '0' || $err_num == '1008')
                {
                    $status = $this->order_status['drawing'];
                    $errorNum = 0;
                }
                else
                {
                    $status = $this->order_status['split_ini'];
                    $errArr[$err_num] = $err_num;
                    $errorNum = $err_num;
                }
                array_push($d_data['sql'], "(?, ?, ?, date_add(now(), interval 1 minute), now(), ?, ?)");
                array_push($d_data['data'], $values[0]);
                array_push($d_data['data'], $errorNum);
                array_push($d_data['data'], $status);
                array_push($d_data['data'], $params['msgid']);
                array_push($d_data['data'], 16);
            }
            
            $cfparams = array(
                'db' => 'CF',
                'sql' => $this->CI->ticket_model_order->ticket_succ($fields, $d_data['sql'], $params['lid']),
                'data' => $d_data['data']
            );
            $this->back_update('execute', $cfparams);
            
            if(!empty($errArr))
            {
                $errArr = array_unique($errArr);
                $errNum = implode(',', $errArr);
                $msg = "对messageId:{$params['msgid']}进行提票操作时合作商 {$this->seller} 返回状态码：{$errNum} ,请及时处理。";
                $dbparams = array(
                    'db' => 'DB',
                    'sql' => $this->CI->ticket_model_order->insertAlert(),
                    'data' => array(4, $msg, '恒钜合作商提票报警')
                );
                $this->back_update('execute', $dbparams);
            }
            return true;
        }
        else
        {
            $cfparams = array(
                'db' => 'CF',
                'sql' => $this->CI->ticket_model_order->ticket_fail($params['lid']),
                'data' => array($params['msgid'], $result, $params['sub_order_ids'])
            );
            $this->back_update('execute', $cfparams);
            $msg = "对messageId:{$params['msgid']}进行提票操作时合作商 {$this->seller} 返回状态码：{$result} ,请及时处理。";
            $dbparams = array(
                'db' => 'DB',
                'sql' => $this->CI->ticket_model_order->insertAlert(),
                'data' => array(4, $msg, '恒钜合作商提票报警')
            );
            $this->back_update('execute', $dbparams);
            return false;
        }
    }
    
    /**
     * 提票失败异步回调方法
     * @param unknown $params
     */
    protected function fail_1101($params)
    {
        $cfparams = array(
            'db' => 'CF',
            'sql' => $this->CI->ticket_model_order->ticket_fail($params['lid']),
            'data' => array($params['msgid'], '', $params['sub_order_ids'])
        );
        $this->back_update('execute', $cfparams);
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
                    $re = $this->bdy_1101($reorders, 42, date('Ymd', time()));
                }
            }
        }
    }

    /**
     * 竞彩篮球提票
     * @param unknown $corders
     */
    private function lid_jclq($corders)
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
                    if($order['playType'] == 53)
                    {
                        $saleCode = $this->saleCode[$order['lid']]['HH']['3'];
                        $ggtype = implode('/', $this->get_ggtype($codess[1]));
                    }
                    $order['codes'] = $saleCode . '#' . $lotCode . '|' . $ggtype . '#B' . $order['multi'];
                    array_push($reorders, $order);
                    ++$count;
                }
                if($count > 0)
                {
                    $re = $this->bdy_1101($reorders, 43, date('Ymd', time()));
                }
            }
        }
    }
}
