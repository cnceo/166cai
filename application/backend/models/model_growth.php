<?php

/**
 * 成长值管理
 */
class Model_Growth extends MY_Model
{
    public function __construct()
    {
        parent::__construct();
    }
    /**
     * [getStockData 库存数据]
     * @author LiKangJian 2018-01-03
     * @return [type] [description]
     */
    public function getStockData()
    {
    	$sql = "select s.*,r.money,r.use_params,r.use_desc,r.c_name from cp_redpack_stock as s left join cp_redpack as r on r.id = s.rid ";
    	return $this->BcdDb->query($sql)->getAll();
    }
    /**
     * [updateStock 更新库存]
     * @author LiKangJian 2018-01-03
     * @param  [type] $arr [description]
     * @return [type]      [description]
     */
    public function updateStock($arr)
    {
    	$this->master->trans_start();
        $flag = true;
        foreach ($arr['id'] as $k => $v) 
    	{
    		if($arr['emptyStock'][$k]==1)
    		{
    			$sql = "update cp_redpack_stock set already_out = today_out where id=?";
    			$tag = $this->master->query($sql,array($v));
                if(!$tag) return $flag = false;
    		}
    		if(!empty($arr['modifyConfig'][$k]))
    		{
    			$sql = "update cp_redpack set use_params = ? where id=?";
    			$tag = $this->master->query($sql,array($arr['modifyConfig'][$k],$arr['rid'][$k]));
                //更新库存
                $sql = "update cp_redpack_stock set next_out = ? where id=?";
                $tag1 = $this->master->query($sql,array($arr['next_out'][$k],$v));
                if(!$tag ||!$tag1 ) return $flag = false;  
    		}
    	}
        if($flag===false)
        {
            $this->master->trans_rollback();
            return false;
        }else{
            $this->master->trans_complete();  
            return true;  
        }
    	
    }
    /**
     * [exchange 兑换记录]
     * @author LiKangJian 2018-01-03
     * @param  [type] $searchData [description]
     * @param  [type] $page       [description]
     * @param  [type] $pageCount  [description]
     * @return [type]             [description]
     */
    public function exchange($searchData, $page, $pageCount)
    {
        //条件块
        $where = "where p.ctype =3  ";
        if(!empty($searchData['name']))
        {
        	$where .= " and u.uname like '%".$searchData['name']."%'";
        }
        if(!empty($searchData['p_type']))
        {
        	$where .= " and r.p_type ='".$searchData['p_type']."'";
        }
        if(!empty($searchData['use_status']))
        {
        	$where .= "  and l.status ='".$searchData['use_status']."'";
        }
        if(!empty($searchData['money']))
        {
        	$where .= "  and r.money ='".($searchData['money']*100)."'";
        }
        //领取时间
        if(!empty($searchData['get_time_s']) && empty($searchData['get_time_e']))
        {
        	$where .= " and  l.get_time ='".$searchData['get_time_s']."'";
        }else if(empty($searchData['get_time_s']) && !empty($searchData['get_time_e'])){
            $where .= " and  l.get_time ='".$searchData['get_time_e']."'";
        }else if(!empty($searchData['get_time_s']) && !empty($searchData['get_time_e']))
        {
            $where .= " and  l.get_time >='".$searchData['get_time_s']."'";
            $where .= " and  l.get_time <='".$searchData['get_time_e']."'";
        }
        //生效日
        if(!empty($searchData['valid_start_s']) && !empty($searchData['valid_start_e']))
        {
        	$where .= " and  l.valid_start >='".$searchData['valid_start_s']."'";
            $where .= "  and l.valid_start <='".$searchData['valid_start_e']."'";
        }else if(!empty($searchData['valid_start_s']) && empty($searchData['valid_start_e']))
        {
            $where .= " and  l.valid_start ='".$searchData['valid_start_s']."'";
        }else if(empty($searchData['valid_start_s']) && !empty($searchData['valid_start_e']))
        {
            $where .= " and  l.valid_start ='".$searchData['valid_start_e']."'";
        }
        //到期日
        if(!empty($searchData['valid_end_s']) && !empty($searchData['valid_end_e']) )
        {
        	$where .= "  and l.valid_end >='".$searchData['valid_end_s']."'";
            $where .= "  and l.valid_end <='".$searchData['valid_end_e']."'";
        }else if(!empty($searchData['valid_end_s']) && empty($searchData['valid_end_e']) )
        {
            $where .= "  and l.valid_end ='".$searchData['valid_end_s']."'";
        }else if(empty($searchData['valid_end_s']) && !empty($searchData['valid_end_e']) )
        {
            $where .= "  and l.valid_end ='".$searchData['valid_end_e']."'";
        }
        //使用时间
        if(!empty($searchData['use_time_s']) && !empty($searchData['use_time_e']))
        {
        	$where .= "  and l.use_time >='".$searchData['use_time_s']."'";
            $where .= "  and l.use_time <='".$searchData['use_time_e']."'";
        }else if(!empty($searchData['use_time_s']) && empty($searchData['use_time_e']))
        {
            $where .= "  and l.use_time ='".$searchData['use_time_s']."'";
        }else if(empty($searchData['use_time_s']) && !empty($searchData['use_time_e']))
        {
            $where .= "  and l.use_time ='".$searchData['use_time_e']."'";
        }
	    //总人数 1 消耗积分总额 2650 红包总金额 5 元
        $countSql = "select sum(p.value) as v, sum(r.money) as m,count(p.id) as c,COUNT(DISTINCT p.uid) as u 
                  from cp_points_logs as p 
                  left join cp_redpack_log  as l on l.id = p.rid
                  left join cp_redpack as r on r.id = l.rid
                  left join cp_user as u on u.uid = p.uid
                  $where
                  ";
        $count = $this->BcdDb->query($countSql)->getRow();
        $sql = "select  p.id,p.ctype,u.uname,u.uid,l.get_time,r.p_type,r.money,r.money_bar,p.value,l.status,l.use_time,r.use_desc,r.c_name,r.c_type,l.valid_start,l.valid_end 
        		  from cp_points_logs as p 
                  left join cp_redpack_log  as l on l.id = p.rid
        		  left join cp_redpack as r on r.id = l.rid
        		  left join cp_user as u on u.uid = p.uid
        		  $where
        		  ORDER BY p.modified DESC 
        		  LIMIT " . ($page - 1) * $pageCount . "," . $pageCount;
        $res = $this->BcdDb->query($sql)->getAll();
        return array(
        			'res' =>$res,
        			'count'=>$count
        		   );
    }
    /**
     * [pointList 积分明细]
     * @author LiKangJian 2018-01-08
     * @param  [type] $searchData [description]
     * @param  [type] $page       [description]
     * @param  [type] $pageCount  [description]
     * @return [type]             [description]
     */
    public function pointList($searchData, $page, $pageCount)
    {
        $where = "where 1 ";
        if(!empty($searchData['name']))
        {
        	$where .= ' and (u.uname like '."'".'%'.trim($searchData['name']).'%'."'" .'or i.real_name like '."'".'%'.trim($searchData['name']).'%'."'".' )';
        }

        //交易号
        if(!empty($searchData['trade_no']))
        {
        	$where .= ' and p.trade_no='."'".trim($searchData['trade_no'])."'";
        }
        //用户uid
        if(!empty($searchData['uid']))
        {
            $where .= ' and p.uid='."'".$searchData['uid']."'";
        }
        //交易0 减 1 加
        if($searchData['mark']==1 && $searchData['mark1']!=1)
        {
        	$where .= " and p.mark='".$searchData['mark']."'";
        }else if($searchData['mark']!=1 && $searchData['mark1']==1)
        {
            $where .= " and p.mark='0'";
        }
        //交易类型
        if(in_array($searchData['ctype'],array(1,2,3,4,5)) )
        {
        	$where .= '  and p.ctype ='."'".($searchData['ctype']-1)."'";
        }
        //交易时间
        if(!empty($searchData['created_s']) && !empty($searchData['created_e']) )
        {
        	$where .= " and  p.created >='".$searchData['created_s']."'";
            $where .= " and  p.created <='".$searchData['created_e']."'";;
        }else if(!empty($searchData['created_s']) && empty($searchData['created_e']) )
        {
            $where .= " and  p.created ='".$searchData['created_s']."'";
        }else if(empty($searchData['created_s']) && !empty($searchData['created_e']) )
        {
            $where .= " and  p.created ='".$searchData['created_e']."'";
        }
        //交易积分
        if(!empty($searchData['value_s']) && empty($searchData['value_e']))
        {
            $where .= " and  p.value ='".$searchData['value_s']."'";
        }else if(empty($searchData['value_s']) && !empty($searchData['value_e']))
        {
           $where .= "  and p.value ='".$searchData['value_e']."'";
        }else if(!empty($searchData['value_s']) &&!empty($searchData['value_e']))
        {
            $where .= " and  p.value >='".$searchData['value_s']."'";
            $where .= "  and p.value <='".$searchData['value_e']."'";
        }

        $countSql = "select count(mark) as mark ,mark as m,sum(value) as value from
    	        cp_points_logs as p 
    	        left join cp_user as u on u.uid = p.uid
    	        left join cp_user_info as i on i.uid = p.uid
    	        $where
    	        GROUP BY p.mark 
    	        ";

    	$count = $this->BcdDb->query($countSql)->getAll();
    	$sql = "select p.uid,p.id,p.value,p.mark,p.ctype,p.rid,p.cvalue,p.trade_no,p.orderId,p.subscribeId,p.uvalue,p.status,p.overTime,p.content,p.created,u.uname,i.real_name from
    	        cp_points_logs as p 
    	        left join cp_user as u on u.uid = p.uid
    	        left join cp_user_info as i on i.uid = p.uid
    	        $where
        		ORDER BY p.modified DESC 
        		LIMIT " . ($page - 1) * $pageCount . "," . $pageCount;
        $res = $this->BcdDb->query($sql)->getAll();
        return array(
        			'res' =>$res,
        			'count'=>$count
        		   );
    	       
    }
    /**
     * [growthList 成长值明细]
     * @author LiKangJian 2018-01-09
     * @param  [type] $searchData [description]
     * @param  [type] $page       [description]
     * @param  [type] $pageCount  [description]
     * @return [type]             [description]
     */
    public function growthList($searchData, $page, $pageCount)
    {
        $where = "where 1 ";
        if(!empty($searchData['name']))
        {
        	$where .= " and (u.uname like '%".$searchData['name']."%'" ." or i.real_name like '%".$searchData['name']."%' )";
        }
        //交易号
        if(!empty($searchData['trade_no']))
        {
        	$where .= ' and p.trade_no='."'".$searchData['trade_no']."'";
        }
        //交易0 减 1 加
        if(!empty($searchData['mark']))
        {
        	$where .= " and p.mark='".$searchData['mark']."'";;
        }
        //交易类型
        if(!empty($searchData['ctype']))
        {
        	$where .= "  and p.ctype ='".$searchData['ctype']."'";
        }
        //交易时间
        if(!empty($searchData['created_s']))
        {
        	$where .= " and  p.created >='".$searchData['created_s']."'";
        }
        if(!empty($searchData['created_e']))
        {
        	$where .= " and  p.created <='".$searchData['created_e']."'";
        }
        if(empty($searchData['created_e']) && empty($searchData['created_s']))
        {
        	$where .= ' and  p.created >='."'".date('Y-m-d',strtotime("-1 month")).' 00:00:00'."'";
        	$where .= ' and  p.created <='."'".date("Y-m-d").' 23:59:59'."'";
        }
        //成长值
        if(!empty($searchData['value_s']) && empty($searchData['value_e']))
        {
            $where .= " and  p.value ='".$searchData['value_s']."'";
        }else if(empty($searchData['value_s']) && !empty($searchData['value_e']))
        {
            $where .= " and  p.value ='".$searchData['value_e']."'";
        }else if(!empty($searchData['value_s']) && !empty($searchData['value_e']))
        {
            $where .= " and  p.value >='".$searchData['value_s']."'";
            $where .= "  and p.value <='".$searchData['value_e']."'";;
        }
        $countSql = "select count(mark) as mark ,sum(value) as value from
    	        cp_growth_logs as p 
    	        left join cp_user as u on u.uid = p.uid
    	        left join cp_user_info as i on i.uid = p.uid
    	        $where
    	        GROUP BY p.mark 
    	        ";
    	$count = $this->BcdDb->query($countSql)->getAll();
    	$sql = "select p.uid,p.id,p.value,p.mark,p.ctype,p.cvalue,p.trade_no,p.orderId,p.subscribeId,p.uvalue,p.status,p.overTime,p.content,p.created,u.uname,i.real_name from
    	        cp_growth_logs as p 
    	        left join cp_user as u on u.uid = p.uid
    	        left join cp_user_info as i on i.uid = p.uid
    	        $where
        		ORDER BY p.created DESC 
        		LIMIT " . ($page - 1) * $pageCount . "," . $pageCount;
        $res = $this->BcdDb->query($sql)->getAll();
        return array(
        			'res' =>$res,
        			'count'=>$count
        		   );
    	       
    }

}