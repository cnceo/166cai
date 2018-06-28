<?php

class Dismantle_Model extends MY_Model 
{

    public function __construct() 
    {
        parent::__construct();
    }

    public function saveDisOrders($bdata, $ctype = 0, $lid = 0)
    {
    	$tables = $this->getSplitTable($lid);
    	$fields = array('sub_order_id', 'issue', 'playType', 'lid', 'codes', 'orderId', 'money', 'multi', 'betTnum', 'isChase', 'endTime', 'saleTime', 'ticket_seller',
    			'real_name', 'id_card', 'created');
    	if($ctype == 1)
    	{
    		$fields = array('sub_order_id', 'issue', 'playType', 'lid', 'subCodeId', 'codes', 'orderId', 'money', 'multi', 'betTnum', 'isChase', 'endTime', 'saleTime', 'ticket_seller',
    					'real_name', 'id_card', 'created');
    	}
    	$sql = "insert {$tables['split_table']}(" . implode(',', $fields) . ")values " . implode(',', $bdata['s_data']);
    	$re0 = $this->cfgDB->query($sql, $bdata['d_data']);
    	
    	$fields_relation = array('sub_order_id', 'mid', 'lid', 'ptype', 'pscores', 'created');
    	$sql_relation = "insert {$tables['relation_table']}(" . implode(',', $fields_relation) . ")values ";
    	if($ctype == 1)
    	{
    		$sql_relation .= implode(',', $bdata['ss_data']);
    		$re1 = $this->cfgDB->query($sql_relation, $bdata['sd_data']);
    		if($re1 && $re0)
    		{
    			return true;
    		}
    	}
    	else 
    	{
    		if($re0)
    		{
    			return true;
    		}
    	}
    	return false;
    }
    
    /**
     * 返回比赛销售截止时间
     * @param int $lid
     * @param array $midArr	场次id
     */
    public function getJjcEndtime($lid, $midArr)
    {
    	$data = array();
    	if(empty($midArr) || !(is_array($midArr)))
    	{
    		return $data;
    	}
    	
    	$table = array(
    		'42' => 'cp_jczq_paiqi',
    		'43' => 'cp_jclq_paiqi'
    	);
    	$REDIS = $this->config->item('REDIS');
    	$lotteryConfig = $this->cache->get($REDIS['LOTTERY_CONFIG']);
    	$lotteryConfig = json_decode($lotteryConfig, true);
    	$sql = "select mid, show_end_time from {$table[$lid]} where mid in ?";
    	$result = $this->cfgDB->query($sql, array($midArr))->getAll();
    	foreach ($result as $val)
    	{
    		$data[$val['mid']] = date('Y-m-d H:i:s', strtotime("+{$lotteryConfig[$lid]['ahead']} minute", strtotime($val['show_end_time'])));
    	}
    	
    	return $data;
    }
    
    /**
     * 返回数字彩的开售时间
     * @param unknown_type $lid
     * @param unknown_type $issue
     */
    public function getNumSaleTime($lid, $issue)
    {
    	$lidMap = array(
    		'51' => array('table' => 'cp_ssq_paiqi', 'sub' => 0),
    		'52' => array('table' => 'cp_fc3d_paiqi', 'sub' => 0),
    		'23529' => array('table' => 'cp_dlt_paiqi', 'sub' => 2),
    		'33' => array('table' => 'cp_pl3_paiqi', 'sub' => 2),
    		'35' => array('table' => 'cp_pl5_paiqi', 'sub' => 2),
    		'10022' => array('table' => 'cp_qxc_paiqi', 'sub' => 2),
    		'23528' => array('table' => 'cp_qlc_paiqi', 'sub' => 0),
    		'21406' => array('table' => 'cp_syxw_paiqi', 'sub' => 0),
    		'21407' => array('table' => 'cp_jxsyxw_paiqi', 'sub' => 0),
    		'53'    => array('table' => 'cp_ks_paiqi', 'sub' => 0),
    		'21408' => array('table' => 'cp_hbsyxw_paiqi', 'sub' => 0),
            '54' => array('table' => 'cp_klpk_paiqi', 'sub' => 0),
            '55' => array('table' => 'cp_cqssc_paiqi', 'sub' => 0),
            '56'    => array('table' => 'cp_jlks_paiqi', 'sub' => 0),
    	    '57'    => array('table' => 'cp_jxks_paiqi', 'sub' => 0),
    	    '21421' => array('table' => 'cp_gdsyxw_paiqi', 'sub' => 0),
    	);
    	
    	$sql = "select sale_time from {$lidMap[$lid]['table']} where issue = ?";
    	return $this->cfgDB->query($sql, array(substr($issue, $lidMap[$lid]['sub'])))->getOne();
    }
    
    /**
     * 返回老足彩的开售时间
     * @param unknown_type $mid
     * @param unknown_type $ctype
     */
    public function getLzcSaleTime($issue, $ctype = 1)
    {
    	$sql = "select start_sale_time as sale_time from cp_tczq_paiqi where mid=? and ctype=? limit 1";
    	return $this->cfgDB->query($sql, array(substr($issue, 2), $ctype))->getOne();
    }
    
    public function getDisOrders($rtype = false)
    {
    	if($rtype)	
    	{
    		$lnames = explode('::', __METHOD__);
    		return $this->getByCron(array('model' => $lnames[0], 'func' => $lnames[1], 'params' => ''));
    	}
    	else 
    	{
    		$sql = "select orderId, codes, lid, money, multi, issue, playType,
	    	isChase, betTnum, endTime, ticket_seller, real_name, phone, id_card from cp_orders_ori m
	    	where 1 and status = 0 and modified > date_sub(now(), interval 30 minute) order by endTime asc limit 100";
    		$orders = $this->cfgDB->query($sql)->getAll();
    		$liborders = array();
    		if(!empty($orders))
    		{
    			foreach ($orders as $order)
    			{
    				$liborders[$order['lid']][] = $order;
    			}
    		}
    		return $liborders;
    	}
    	
    }


    public function getCaidouRateByLid($lid)
    {
        $sql = "SELECT ticketRate FROM cp_seller_rate where lid = {$lid} and ticketSeller = 'caidou' ";
        $row = $this->cfgDB->query($sql)->getRow();
        return $row['ticketRate'];
    }
    
    public function getRetByLid($lid) {
        $sql = "SELECT ticketSeller FROM cp_seller_rate where lid = ? and ticketRate > 0 order by ticketRate desc";
        return $this->cfgDB->query($sql, array($lid))->getCol();
    }
    
    /**
     * 根据lid返回票商分配比例
     * @param unknown $lid
     * @return unknown[]
     */
    public function getTicketRate($lid)
    {
        $data = array();
        $sql = "select ticketSeller, ticketRate from cp_seller_rate where lid = ? and ticketRate > 0 order by ticketRate asc";
        $res = $this->cfgDB->query($sql, array($lid))->getAll();
        foreach ($res as $val) {
            $data[$val['ticketSeller']] = $val['ticketRate'];
        }
        
        return $data;
    }
}
