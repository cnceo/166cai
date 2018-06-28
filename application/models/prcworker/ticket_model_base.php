<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class ticket_model_base extends MY_Model
{
    protected $order_status;
	public function __construct()
	{
		parent::__construct();
		$this->order_status = $this->orderConfig('orders');
	}

    /**
     * 返回split表组装更新数据
     * @param unknown $fields
     * @param unknown $datas
     * @param number $lid
     * @return string
     */
    public function getSplitResponseSql($fields, $datas, $lid = 0)
    {
        $returnData = array();
        if(!empty($datas['s_data']))
        {
            $tables = $this->getSplitTable($lid);
            $sql = "insert {$tables['split_table']}(" . implode($fields, ',') . ")values" . implode($datas['s_data'], ',')
                . " on duplicate key update status = if(status < values(status), values(status), status),
	        ticketId = if(values(status) = '{$this->order_status['draw']}', values(ticketId), ticketId),
	        ticket_time = if(values(status) = '{$this->order_status['drawing']}' && status = '{$this->order_status['drawing']}',
	        if(values(ticket_time) > endTime, date_sub(endTime, interval 5 second), values(ticket_time)),
	        if(status = '{$this->order_status['concel']}', ticket_time, values(ticket_time))),
	        error_num = if((status < '{$this->order_status['draw']}' || status = '{$this->order_status['concel']}'), values(error_num), '')";
            $sql_data = $datas['d_data'];
            $sql1 = $sql2 = '';
            if(in_array($lid, array('42', '43', '44', '45')))
            {
                if(!empty($datas['relation']))
                {
                    $result = $this->dealRelations($datas['relation']);
                    $bdatas = $result['data'];
                    $parserFlag = $result['parserFlag'];
                    if(!$parserFlag)
                    {
                        //解析错误直接返回
                        return array();
                    }
                    $sql1 = $bdatas['sql'];
                    $sql1_data = $bdatas['d_data'];
                }

                if(!empty($datas['relationConcel']))
                {
                    $sql2 = "update {$tables['relation_table']} set status = ? where sub_order_id in('" .
                        implode("','", $datas['relationConcel']) . "')";
                    $sql2_data = array($this->order_status['concel']);
                }
            }
            //sql1和sql2不为空时启用事物
            if((!empty($sql1)) || (!empty($sql2)))
            {
                $returnData = array(
                    'function' => 'transction',
                    'datas' => array(
                        'db' => 'CF',
                        'sql' => [],
                        'data' => [],
                    ),
                );
                $returnData['datas']['sql'][] = $sql;
                $returnData['datas']['data'][] = $sql_data;
                if($sql1)
                {
                    $returnData['datas']['sql'][] = $sql1;
                    $returnData['datas']['data'][] = $sql1_data;
                }
                if($sql2)
                {
                    $returnData['datas']['sql'][] = $sql2;
                    $returnData['datas']['data'][] = $sql2_data;
                }
            }
            else
            {
                $returnData = array(
                    'function' => 'execute',
                    'datas' => array(
                        'db' => 'CF',
                        'sql' => $sql,
                        'data' => $sql_data,
                    ),
                );
            }
        }

        return $returnData;
    }

    /**
     * 失败订单切换票商操作
     * @param unknown_type $subIds
     */
    public function getUpdateTicket($subIds, $lid)
    {
        $returnDatas = array();
        $tables = $this->getSplitTable($lid);
        $sql = "select message_id, sub_order_id, status, ticket_seller, ticket_flag from {$tables['split_table']} where sub_order_id in ?";
        $result = $this->cfgDB->query($sql, array($subIds))->getAll();
        $this->load->driver('cache', array('adapter' => 'redis'));
        $this->REDIS = $this->config->item('REDIS');
        $lotteryConfig = json_decode($this->cache->get($this->REDIS['LOTTERY_CONFIG']), true);
        $otherSeller = array(
            '1' => 'qihui',
            '2' => 'caidou',
            '4' => 'shancai',
            '8' => 'huayang',
            '16' => 'hengju',
        );
        $bdata['s_data'] = array();
        $bdata['d_data'] = array();
        $alertSubid1 = array();
        $alertSubid2 = array();
        foreach ($result as $value)
        {
            //状态大于240或票商已变就不操作
            if(($value['status'] > 240) || ($value['ticket_seller'] !=  $this->seller))
            {
                continue;
            }
            array_push($bdata['s_data'], "(?, ?, ?, ?)");
            $ticketSeller = '';
            $ticketId = 0;
            if($value['ticket_flag'] != $lotteryConfig[$lid]['ticket_flag'])
            {
                foreach ($otherSeller as $id => $seller)
                {
                    //如果票商id不允许出该彩种 跳过
                    if((!($id & $lotteryConfig[$lid]['ticket_flag'])) || ($seller == $this->seller))
                    {
                        continue;
                    }
                    if(!($value['ticket_flag'] & $id))
                    {
                        $ticketSeller = $seller;
                        $ticketId = $id;
                        break;
                    }
                }
            }
            if($ticketSeller)
            {
                array_push($bdata['d_data'], $value['sub_order_id']);
                array_push($bdata['d_data'], '');
                array_push($bdata['d_data'], 0);
                array_push($bdata['d_data'], $ticketSeller);
                $alertSubid1[] = $value['sub_order_id'];
            }
            else
            {
                array_push($bdata['d_data'], $value['sub_order_id']);
                array_push($bdata['d_data'], $value['message_id']);
                array_push($bdata['d_data'], 0);
                array_push($bdata['d_data'], $ticketSeller);
                $alertSubid2[] = $value['sub_order_id'];
            }
        }

        if(!empty($bdata['s_data']))
        {
            $fields = array('sub_order_id', 'message_id', 'status', 'ticket_seller');
            $sql = "insert {$tables['split_table']}(" . implode(', ', $fields) . ") values" .
                implode(', ', $bdata['s_data']) . " on duplicate key update message_id = values(message_id), 
            status = values(status), ticket_seller = values(ticket_seller) ";
            $data = [
                'db' => 'CF',
                'sql' => $sql,
                'data' => $bdata['d_data'],
            ];

            $returnDatas[] = $data;
        }
        if($alertSubid1)
        {
            $this->load->library('BetCnName');
            $title = BetCnName::getCnName($lid) . "有订单在{$this->seller}出票失败，将切换票商";
            $content = "将切换票商的子订单id信息：" . implode(',', $alertSubid1);
            $sql = "INSERT INTO cp_alert_log
    		(ctype,title,content,created) VALUES (?, ?, ?, NOW())";
            $data = [
                'db' => 'DB',
                'sql' => $sql,
                'data' => array(4,$title,$content),
            ];
            $returnDatas[] = $data;
        }

        if($alertSubid2)
        {
            $this->load->library('BetCnName');
            $title = BetCnName::getCnName($lid) . "有订单在所有票商均未能出票";
            $content = "所有票商均未能出票的子订单id信息：" . implode(',', $alertSubid2);
            $sql = "INSERT INTO cp_alert_log
    		(ctype,title,content,created) VALUES (?, ?, ?, NOW())";
            $data = [
                'db' => 'DB',
                'sql' => $sql,
                'data' => array(4,$title,$content),
            ];
            $returnDatas[] = $data;
        }
        return $returnDatas;
    }

    // 乐善奖临时表
    public function getSplitDetailSql($datas, $lid)
    {
        $fields = array('sub_order_id', 'lid', 'ticket_seller', 'awardNum', 'created');
        $returnData = array();
        if(!empty($datas['s_data']))
        {
            $sql = "insert cp_orders_split_detail(" . implode($fields, ',') . ")values" . implode($datas['s_data'], ',')
                . " on duplicate key update awardNum = values(awardNum)";

            $returnData = array(
                'db'    =>  'CF',
                'sql'   =>  $sql,
                'data'  =>  $datas['d_data'],
            );
        }
        return $returnData;
    }
}
