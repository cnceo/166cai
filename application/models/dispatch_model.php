<?php

class Dispatch_Model extends MY_Model 
{

    public function __construct() 
    {
        parent::__construct();
    }
	
    /**
     * 查询票商设置的出票比例
     */
    public function getSellerRate()
    {
    	$data = array();
    	$sql = "select ticketSeller, lid, ticketRate from cp_seller_rate where 1";
    	$res = $this->cfgDB->query($sql)->getAll();
    	foreach ($res as $val)
    	{
    		$data[$val['lid']][$val['ticketSeller']] = $val['ticketRate'];
    	}
    	
    	return $data;
    }
    
    /**
     * 查询票商出票单数汇总
     * @param int $lid		彩种id
     * @param int $issue	期次
     */
    public function getSellerTotal($lid, $issue)
    {
    	$tables = $this->getSplitTable($lid);
    	$stime = $this->get_cissue_stime($lid, $issue);
    	$data = array(
    		'total' => 0
    	);
    	$sql = "SELECT ticket_seller, COUNT(1) total FROM {$tables['split_table']} WHERE modified > ? AND lid=? AND issue = ? GROUP BY ticket_seller";
    	$result = $this->cfgDB->query($sql, array($stime, $lid, $issue))->getAll();
    	if($result)
    	{
    		foreach ($result as $val)
    		{
    			$data['total'] += $val['total'];
    			$data[$val['ticket_seller']] = $val['total'];
    		}
    	}
    	
    	return $data;
    }
    
    /**
     * 充值渠道分配功能
     * @author huxiaoming
     * @param int $issue
     */
    public function RcgChannelDispatch()
    {
    	$grppoint= array();//存储最终商户指针;
    	$sql = "select concat(platform, '_', ctype) rgroup, sum(rate) trate,
				group_concat(concat(id, '/', rate, '/', mer_id, '/', pay_type)) datas 
				from bn_cpiao.cp_pay_config 
				where status = 0 and rate > 0 group by platform, ctype
				having trate = 100";
    	$results = $this->db->query($sql)->getAll();
    	if(!empty($results))
    	{
    		$groups  = array();//安组分类商户号
    		$grpname = array();//组名下的字段名
    		$serail  = array();//各商户号充值汇总语句
    		$grpmids = array();//各组商户号
    		
    		foreach ($results as $result)
    		{
    			$tmps = explode(',', $result['datas']);
    			foreach ($tmps as $tmp)
    			{
    				$datas = explode('/', $tmp);
    				$groups[$result['rgroup']][$datas[0]] = $datas;
    			}
    		}
    		/*子渠道的金额汇总语句*/
    		foreach ($groups as $group => $grpvals)
    		{
    			foreach ($grpvals as $id => $data)
    			{
    				$grpname[$group][] = "money$id";
    				$serail[] = "sum(if(rcg_serial=$id, money, 0)) money$id";
    			}
    		}
    		$sql = "select rcg_group, sum(money) grpmoney, " . implode(', ', $serail) . 
    		" from cp_pay_logs where modified > date_sub(now(), interval 1 day) and status = 1 and 
    		rcg_group in ('" . implode("', '", array_keys($grpname)). "') group by rcg_group";
    		$datas = $this->db->query($sql)->getAll();/*依据platform，ctype分组查询汇总充值金额*/
    		if(!empty($datas))
    		{
    			$ratios = array();
    			foreach ($datas as $data)
    			{
    				if($data['grpmoney'] > 0)
    				{
	    				foreach($grpname[$data['rcg_group']] as $fname)
	    				{
	    					preg_match('/^money(\d+)$/is', $fname, $match);
	    					$ratios[$data['rcg_group']][$match[1]] = intval(($data[$fname] / $data['grpmoney']) * 100000);
	    				}
    				}
    			}
    		}
    		/*1 用分配比例减去已充值比例
    		 *2 获取差值最大*/
    		foreach ($groups as $group => $grpvals)
    		{
    			foreach ($grpvals as $id => $data)
    			{
    				$tmprate = $data[1] * 1000; //该商户号占比
    				$grpmids[$group][$id] = $tmprate;
    				if($ratios[$group][$id])
    				{
    					$tmprate -= $ratios[$group][$id];
    					$grpmids[$group][$id] = ($tmprate > 0) ? $tmprate : 0; 
    				}
    			}
    			asort($grpmids[$group]);
    			$pointer = array_pop(array_keys($grpmids[$group]));
    			$splitgroup = explode('_', $group);
    			$grppoint[$splitgroup[0]][$splitgroup[1]][] = $this->get_params($groups[$group][$pointer][0]);
    		}
    	}
    	$this->add_channels($grppoint);
        //M版本的缓存
        $this->add_mcache($grppoint);
    	return $this->arr_seriallize($grppoint);
    }
    /**
     * 获取其他字段数据
     * @author huxiaoming
     */
    private function get_params($id)
    {
    	$sql = "select id, rate, mer_id, pay_type, weight, params from bn_cpiao.cp_pay_config 
				where id = ?";
    	return $this->db->query($sql, array($id))->getRow();
    }
    /**
     * 数据序列化
     * @author huxiaoming
     */
    private function arr_seriallize($grppoint)
    {
    	foreach ($grppoint as $key => $point)
    	{
    		$grppoint[$key] = json_encode($point);
    	}
    	return $grppoint;
    }
    /**
     * 新增存储无需分配比例的渠道
     * @author huxiaoming
     * @param int $issue
     */
    private function add_channels(&$grppoint)
    {
    	$sql = "select id, platform, ctype, pay_type, weight, params from bn_cpiao.cp_pay_config 
				where status = 0 and rate = -1";
    	$datas = $this->db->query($sql)->getAll();
    	$redatas = array();
    	if(!empty($datas))
    	{
    		foreach ($datas as $data)
    		{
    			$platform = $data['platform']; 
    			$ctype    = $data['ctype'];    
    			unset($data['ctype']);
    			unset($data['platform']);
    			$grppoint[$platform][$ctype][] = $data;
    		}
    	}
    }

     /**
      * [add_mcache 新增M版本充值快捷缓存]
      * @author LiKangJian 2017-08-21
      * @param  [type] &$grppoint [description]
      */
    private function add_mcache(&$grppoint)
    {
        $sql = "select id, platform, ctype, pay_type, weight, params from bn_cpiao.cp_pay_config 
                where status = 0 and platform = 4 and ctype=1";
        $datas = $this->db->query($sql)->getAll();
        $redatas = array();
        if(!empty($datas))
        {
            foreach ($datas as $data)
            {
                $platform = $data['platform']; 
                $ctype    = $data['ctype'];    
                unset($data['ctype']);
                unset($data['platform']);
                $grppoint[$platform][$ctype][] = $data;
            }
        }
    }   
}