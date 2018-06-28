<?php

class Award_Notice_Model extends MY_Model 
{

    public function __construct() 
    {
        parent::__construct();
    }

    public function getOrderWin() 
    {
//         $sql1 = "SELECT SUM(bonus) AS bonus, SUM(margin) AS margin FROM `cp_orders_win`";
//         $countInfo = $this->slave->query($sql1)->getRow();

//         $sql2 = "SELECT w.uid, w.orderId, w.money, w.bonus, w.margin, w.created, o.lid, u.nick_name FROM cp_orders_win AS w LEFT JOIN cp_orders AS o ON w.orderId = o.orderId LEFT JOIN cp_user_info AS u ON w.uid = u.uid WHERE w.margin >= 10000 ORDER BY w.id DESC LIMIT 300";
//         $orderInfo = $this->slave->query($sql2)->getAll();
 
        return array('count' => 0, 'orderInfo' => array());
    }

    public function saveOrderWin($info)
    {
        $this->load->driver('cache', array('adapter' => 'redis'));
        $REDIS = $this->config->item('REDIS');
        $this->cache->save($REDIS['AWARD_NOTICE'], serialize($info), 0);
    }

    public function getOrderWin2()
    {
        $this->load->driver('cache', array('adapter' => 'redis'));
        $REDIS = $this->config->item('REDIS');
        $info = unserialize($this->cache->get($REDIS['AWARD_NOTICE']));
        return $info;
    }

}
