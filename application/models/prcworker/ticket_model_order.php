<?php
/**
 * 票商交互数据层
 * @author Administrator
 *
 */
class ticket_model_order extends MY_Model
{
    /**
     * 表名称
     * @var unknown
     */
    private $TBNAME = NULL;
    public function __construct()
    {
        parent::__construct();
        $this->order_status = $this->orderConfig('orders');
    }

    /**
     * 私有属性值设置
     * @param array $params
     */
    public function setValue($params = array())
    {
        foreach($params as $key => $value)
        {
            $this->{$key} = $value;
        }

    }

    /**
     * 查询可用票商
     * @return unknown
     */
    public function getSeller()
    {
        $sql = "select name, weight from cp_ticket_sellers where status = 1 order by weight";
        return $this->cfgDB->query($sql)->getAll();
    }
    
    /**
     * 查询提票订单
     * @param unknown $seller
     * @return unknown
     */
    public function getTicketOrders($seller)
    {
        $datetime = date('H:i:s');
        if($datetime >= '07:50:00' and $datetime < '09:00:00')
        {
            $sql = "select orderId, message_id, sub_order_id, real_name, id_card,
            codes, lid, playType, money, multi, issue, betTnum, isChase, ticket_seller from {$this->TBNAME}
            where status = ? and ticket_seller = ? and (message_id is null or message_id = '') and endTime > now()
            and (saleTime < now() or (saleTime >= now() and ({$this->createTpiaoSql()}))) order by endTime limit 1000";
        }
        else 
        {
            $sql = "select orderId, message_id, sub_order_id, real_name, id_card,
            codes, lid, playType, money, multi, issue, betTnum, isChase, ticket_seller from {$this->TBNAME}
            where status = ? and ticket_seller = ? and (message_id is null or message_id = '') and endTime > now()
            and saleTime < now() order by endTime limit 1000";
        }
        return $this->cfgDB->query($sql, array($this->order_status['create_init'], $seller))->getAll();
    }
    
    /**
     * 提票第一期查询特殊处理
     * @return string
     */
    private function createTpiaoSql()
    {
        $sqlArr = array(
            'cp_orders_split' => "DATE_FORMAT(now(), '%H:%i') >= '07:50' and DATE_FORMAT(now(), '%H:%i') <= '09:00'", //慢频彩
            'cp_orders_split_syxw' => "DATE_FORMAT(now(), '%H:%i') >= '07:50' and issue = date_format(now(), '%y%m%d01') and DATE_FORMAT(now(), '%H:%i:%s') <= '08:26:20'", //老11选5
            'cp_orders_split_jxsyxw' => "DATE_FORMAT(now(), '%H:%i') >= '07:50' and issue = date_format(now(), '%y%m%d01') and DATE_FORMAT(now(), '%H:%i') <= '09:00'", //新11选5
            'cp_orders_split_hbsyxw' => "issue = date_format(now(), '%y%m%d01') and DATE_FORMAT(now(), '%H:%i') <= '08:25'", //惊喜11选5
            'cp_orders_split_ks' => "issue = date_format(now(), '%Y%m%d001') and DATE_FORMAT(now(), '%H:%i') <= '08:48'", //上海快三
            'cp_orders_split_klpk' => "issue = date_format(now(), '%y%m%d01') and DATE_FORMAT(now(), '%H:%i:%s') <= '08:20:50'", //快乐扑克
            'cp_orders_split_gdsyxw' => "DATE_FORMAT(now(), '%H:%i') >= '07:50' and issue = date_format(now(), '%y%m%d01') and DATE_FORMAT(now(), '%H:%i') <= '09:00'", //乐11选5
        );
        
        //有特殊需求的彩种取相应表sql，未定义默认和split表sql相同
        $sql = isset($sqlArr[$this->TBNAME]) ? $sqlArr[$this->TBNAME] : $sqlArr['cp_orders_split'];
        return $sql;
    }

    /**
     * 提票
     * @param unknown $fields
     * @param unknown $sqls
     * @param number $lid
     * @return string
     */
    public function ticket_succ($fields, $sqls, $lid = 0)
    {
        $tables = $this->getSplitTable($lid);
        $upfields = array('sub_order_id', 'error_num', 'status', 'message_id');
        if(in_array('seller_order_id', $fields)){
            array_push($upfields, 'seller_order_id');
        }
        $sql = "insert {$tables['split_table']}(" . implode($fields, ',') . ")values" . implode($sqls, ',')
            . $this->onduplicate($fields, $upfields)
            . ", ticket_time = if(status <= '{$this->order_status['drawing']}',
    		 if(values(ticket_time) > endTime, date_sub(endTime, interval 10 second), values(ticket_time)),ticket_time),
             ticket_submit_time = if(status = '{$this->order_status['drawing']}', values(ticket_submit_time), if(date_add(now(), interval 30 second) > endTime, date_sub(endTime, interval 20 second), date_add(now(), interval 30 second))),
             ticket_flag = ticket_flag ^ values(ticket_flag)";
        return $sql;
    }
    
    /**
     * 报警sql
     * @return string
     */
    public function insertAlert()
    {
        $sql = "INSERT INTO cp_alert_log(ctype,content,title,status,created) VALUES (?, ?, ?, '0', NOW())";
        return $sql;
    }

   /**
    * 提票失败更新数据
    * @param number $lid
    * @return string
    */
    public function ticket_fail($lid = 0)
    {
        $tables = $this->getSplitTable($lid);
        $sql = "update {$tables['split_table']} set message_id = ?, error_num = ?, ticket_submit_time = now() where sub_order_id in ?";
        return $sql;
    }
    
    /**
     * 专门处理疑似提交失败的订单重复提交
     * @param unknown $seller 票商
     * @return unknown
     */
    public function getOrderIds($seller)
    {
        $sql = "select message_id from (select distinct(message_id) message_id, lid, endTime from {$this->TBNAME}
        where modified > date_sub(now(), interval 3 day) and status = ?	and message_id <> ''
        and ticket_seller = ? 
        and ticket_submit_time < if(date_sub(now(), interval 30 SECOND) >= endTime, date_sub(endTime, interval 10 SECOND), date_sub(now(), interval 30 SECOND)) 
        and endTime > now() group by message_id) m
        order by m.endTime limit 10";
        return $this->cfgDB->query($sql, array($this->order_status['split_ini'], $seller))->getCol();
    }
    
    /**
     * 提票重提专用  根据messageid 查询订单信息
     * @param unknown $msgid
     * @return unknown
     */
    public function getTicketOrdersByMsgId($msgid, $seller)
    {
        return $this->cfgDB->query("select orderId, message_id, sub_order_id, real_name, id_card,
        codes, lid, playType, money, multi, issue, betTnum, isChase, ticket_seller from {$this->TBNAME}
        where message_id = ? and status = ? and ticket_seller = ?", array($msgid, $this->order_status['split_ini'], $seller))->getAll();
    }
    
    /**
     * 出票订单查询
     * @param string $seller  票商
     * @param string $concel  是否失败标识
     * @return unknown
     */
    public function getTicketResult($seller, $concel)
    {
        //查询不包括冠亚军彩(冠亚军有可能几天不出票，一直查询浪费性能。主要靠票商推送出票结果)
        if($concel)
        {
            $sql = "select message_id from (select message_id, endTime, seller_order_id from {$this->TBNAME} where modified > date_sub(now(), interval 1 day)
            and lid not in(44, 45) and endTime < now() and status = ? and ticket_seller = ? 
            order by endTime) m group by m.message_id order by m.endTime limit 10";
        }
        else
        {
            $sql = "select message_id from (select message_id, endTime, seller_order_id from {$this->TBNAME} where modified > date_sub(now(), interval 1 day)
            and lid not in(44, 45) and ticket_time < now() and endTime > now() and status = ? and ticket_seller = ? 
            order by endTime) m group by m.message_id order by m.endTime limit 10";
        }

        return $this->cfgDB->query($sql, array($this->order_status['drawing'], $seller))->getCol();
    }
    
    /**
     * 根据messageid查询订单sub_order_id
     * @param unknown $messageid
     * @param string $concel
     * @return unknown
     */
    public function getSubOrdersByMsg($messageid, $concel = false)
    {
        if($concel)
        {
            $sql = "select sub_order_id, lid, ticket_seller, message_id, seller_order_id from {$this->TBNAME} where message_id = ? and status = ?
            and endTime < now()";
        }
        else
        {
            $sql = "select sub_order_id, lid, ticket_seller, message_id, seller_order_id from {$this->TBNAME} where message_id = ? and status = ?
            and endTime > now()";
        }
        
        return $this->cfgDB->query($sql, array($messageid, $this->order_status['drawing']))->getAll();
    }
    
    /**
     * 过期订单设置成失败操作
     */
    public function ticketConcel($seller)
    {
        $sql = "select id from {$this->TBNAME} WHERE modified > date_sub(now(), interval 7 day) AND endTime<NOW()
        AND `status`='{$this->order_status['split_ini']}'";
        $result = $this->cfgDB->query($sql)->getCol();
        if(!empty($result))
        {
            $sql = "UPDATE {$this->TBNAME} a LEFT JOIN cp_orders_relation b ON a.sub_order_id=b.sub_order_id
            SET a.`status`='{$this->order_status['concel']}', b.`status`='{$this->order_status['concel']}'
            WHERE a.id in ?";
            return $this->cfgDB->query($sql, array($result));
        }
    }
}

?>