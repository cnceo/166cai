<?php

/**
 * 摘    要：合买子订单
 * 作    者：yindefu
 * 修改日期：2017.02.09
 */
class Model_united_join extends MY_Model
{

    public function __construct()
    {
        parent::__construct();
        $this->get_db();
    }
    
    /**
     * 获取所有合买订单记录
     * @param int $orderId
     * @param string $username
     * @param int $page
     * @param int $num
     * @return array
     */
    public function getAllOrders($orderId, $searchData, $page, $num)
    {
        $where = "where o.orderId='{$orderId}' ";
        if($searchData['username'])
        {
            $where .= " and {$this->cp_user}.uname like '%{$searchData['username']}%'";
        }
        if(isset($searchData['subOrderType']) && $searchData['subOrderType'] !== FALSE && $searchData['subOrderType'] >= 0)
        {
            if($searchData['subOrderType'])
            {
                $where .= " and o.subOrderType = 1";
            }
            else
            {
                $where .= " and o.subOrderType = 0";
            }
        }
        $sql = "select o.uid,o.status,o.money,o.buyMoney,o.subscribeId,o.margin,o.orderType,o.subOrderType,o.buyPlatform,o.cstate,o.created,{$this->cp_user}.uname from cp_united_join as o left join {$this->cp_user} on o.uid = {$this->cp_user}.uid {$where}";
        $countSql="select count(o.id) as num from cp_united_join as o left join {$this->cp_user} on o.uid = {$this->cp_user}.uid {$where}";
        $allCount = $this->BcdDb->query($countSql)->getRow();
        $sql.=" order by o.created LIMIT " . ($page - 1) * $num . "," . $num;
        $res = $this->BcdDb->query($sql)->getAll();
        return array('data' => $res, 'count' => $allCount['num']);
    }
    
    public function sendEmail($subOrderId) {
        $data = $this->BcdDb->query("select email as `to`, title as `subject`, content as message, '166cai@km.com' as bcc 
            from cp_order_email_logs where orderId = ?", array($subOrderId))->getRow();
        return $this->tools->sendMail($data);
    }
    
    /**
     * 查询所有子订单
     * @param int $orderId
     * @param string $fileds
     * @return array
     */
    public function getAllByOrderId($orderId, $fileds = '')
    {
        if (!$fileds)
        {
            $fileds = '*';
        }
        $sql = "select {$fileds} from cp_united_join where orderId='{$orderId}' and cstate >=128";
        $res = $this->BcdDb->query($sql)->getAll();
        return $res;
    }

    /**
     * 查询发送邮件信息
     * @param unknown_type $subIds
     */
    public function getOrderEmail($subIds)
    {
        return $this->BcdDb->query("select * from cp_order_email_logs where orderId in ({$subIds})")->getAll();
    }
}

