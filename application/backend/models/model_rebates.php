<?php

/**
 * Copyright (c) 2014,上海二三四五网络科技股份有限公司
 * 摘    要：联盟返点
 * 作    者：shigx@2345.com
 * 修改日期：2016.05.09
 */
class Model_rebates extends MY_Model
{
    public function __construct()
    {
        $this->get_db();
    }
    
    /**
     * 参    数：$searchData 搜索条件
     *                 $page 页码
     *                 $pageCount 单页条数
     * 作    者：shigx
     * 功    能：获取推广管理列表
     * 修改日期：2015.12.24
     */
    function listManage($searchData, $page, $pageCount)
    {
        $where = ' where 1 ';
        if ($this->emp($searchData['info']))
        {
        	$where .= " and ({$this->cp_relationship}.id = '{$searchData['info']}' or {$this->cp_user}.uname = '{$searchData['info']}' or {$this->cp_u_i}.real_name='{$searchData['info']}' or {$this->cp_u_i}.phone = '{$searchData['info']}')";
        }
        if($this->emp($searchData['level']))
        {
        	if($searchData['level'] == 1)
        	{
        		$where .= " and {$this->cp_relationship}.puid = 0";
        	}
        	else
        	{
        		$where .= " and {$this->cp_relationship}.puid != 0";
        	}
        }
        $where .= $this->condition("{$this->cp_relationship}.created", array(
        		$searchData['start_time'],
        		$searchData['end_time']
        ), "time");
        $where .= $this->condition("{$this->cp_relationship}.total_sale", array(
        		$searchData['start_money'],
        		$searchData['end_money']
        ), "during", "m");
        $where .= $this->condition("{$this->cp_relationship}.stop_flag", $searchData['stop_flag']);
        $countSql = "select count(1) from {$this->cp_relationship} 
        left join {$this->cp_user} on {$this->cp_user}.uid = {$this->cp_relationship}.uid 
        left join {$this->cp_u_i} on {$this->cp_u_i}.uid = {$this->cp_relationship}.uid {$where}";
        $count = $this->BcdDb->query($countSql)->getOne();
        $select = "select {$this->cp_relationship}.id, {$this->cp_relationship}.puid, {$this->cp_relationship}.uid, {$this->cp_user}.uname, {$this->cp_u_i}.real_name, {$this->cp_u_i}.phone,
        u.uname as up_uname, {$this->cp_relationship}.total_sale, {$this->cp_relationship}.total_income, {$this->cp_relationship}.stop_flag, {$this->cp_relationship}.created
        from {$this->cp_relationship} 
        left join {$this->cp_user} on {$this->cp_user}.uid = {$this->cp_relationship}.uid 
        left join {$this->cp_u_i} on {$this->cp_u_i}.uid = {$this->cp_relationship}.uid
        left join {$this->cp_user} u on u.uid = {$this->cp_relationship}.puid 
        {$where} ORDER BY {$this->cp_relationship}.created DESC LIMIT " . ($page - 1) * $pageCount . "," . $pageCount;
        $result = $this->BcdDb->query($select)->getAll();       
        return array(
            $result,
            $count,
        );
    }
    
    /**
     * 根据id查询用户返点配置信息
     * @param unknown_type $id
     */
    public function getRebateById($id)
    {
        return $this->BcdDb->query("select * from {$this->cp_relationship} where id= ?", $id)->getRow();
    }
    
    /**
     * 根据id查询用户返点信息
     * @param unknown_type $id
     */
    public function getRebateOdds($id)
    {
        return $this->BcdDb->query("SELECT a.uid,a.puid,a.rebate_odds,b.rebate_odds upOdds FROM cp_relationship a LEFT JOIN cp_relationship b ON a.puid = b.uid WHERE a.id=?", array($id))->getRow();
    }
    
    /**
     * 更新用户返点信息
     * @param unknown_type $id
     * @param unknown_type $data
     */
    public function updateRebate($id, $data = array())
    {
    	$this->master->where('id', $id);
    	$this->master->update('cp_relationship', $data);
    	return $this->master->affected_rows();
    }
    
    /**
     * 检查用户是否符合添加代理
     * @param unknown_type $uname
     * @param unknown_type $phone
     */
   	public function checkRebateUser($uname, $phone)
   	{
   		$sql = "select u.uid, r.phone from cp_user u join cp_user_register r on r.id = u.uid where u.uname = ? and u.channel = '10001'";
   		$users = $this->BcdDb->query($sql, array($uname))->getRow();
   		if(!empty($users))
   		{
   			if($users['phone'] == $phone)
   			{
	   			$sql1 = "select count(1) from cp_relationship where uid=?";
	   			$res = $this->BcdDb->query($sql1, array($users['uid']))->getOne();
	   			if($res <= 0)
	   			{
	   				return array('rnum' => 4, 'uid' => $users['uid']);
	   			}
	   			else 
	   			{
	   				return array('rnum' => 3, 'uid' => '');
	   			}
   			}
   			else 
   			{
   				return array('rnum' => 2, 'uid' => '');
   			}
   		}
   		else 
   		{
   			return array('rnum' => 1, 'uid' => '');
   		}
   	}
   	
   	/**
   	 * 添加一级用户
   	 * @param unknown_type $uid
   	 */
   	public function addRebate($uid)
   	{
   		$rebate_odds = '{"42":"0.0","43":"0.0","51":"0.0","23529":"0.0","52":"0.0","23528":"0.0","21406":"0.0","10022":"0.0","33":"0.0","35":"0.0","11":"0.0","19":"0.0","21407":"0.0","53":"0.0","21408":"0.0","54":"0.0","55":"0.0","56":"0.0","57":"0.0","21421":"0.0"}'; //初始化配置
   		$this->master->trans_start();
   		$sql = "insert into cp_relationship(uid, rebate_odds, created) values (?, '{$rebate_odds}', now())";
   		$res1 = $this->master->query($sql, array($uid));
   		$newId = $this->master->insert_id();
   		$url_prefix = $this->config->item('url_prefix');
   		$urlprefix = isset($url_prefix[$this->config->item('domain')]) ? $url_prefix[$this->config->item('domain')] : 'http';
   		$pro_link = $urlprefix."://".$this->config->item('base_url')."/rebates/svip?id=" . $newId;
   		$res2 = $this->master->query("update cp_user set rebates_level = 1 where uid = ?", $uid);
   		$res3 = $this->master->query("update cp_relationship set pro_link = ? where uid = ?", array($pro_link, $uid));
   		$res = $res1 && $res2 && $res3;
   		if ($res)
   		{
   			$this->master->trans_complete();
   			//刷新用户钱包缓存
   			$rediskeys = $this->config->item("REDIS");
   			$this->load->driver('cache', array('adapter' => 'redis'));
   			if($this->cache->redis->hGet($rediskeys['USER_INFO'] . $uid, "uname"))
   			{
   				$this->cache->redis->hSet($rediskeys['USER_INFO'] . $uid, "rebates_level", 1);
   			}
   			else
   			{
   				$this->load->model('model_user');
   				$this->model_user->freshUserInfo($uid);
   			}
   			
   			return true;
   		}
   		else
   		{
   			$this->master->trans_rollback();
   			return false;
   		}
   	}
   	
   	/**
   	 * 查询推广用户详细信息
   	 * @param unknown_type $id
   	 */
   	public function getRelationUserDetail($id)
   	{
   		$select = "select a.id, a.puid, a.uid, b.uname, b.created, c.real_name, c.phone,
   		d.uname as up_uname, a.rebate_odds, a.total_sale, a.total_income, a.stop_flag, a.created applyTime
   		from {$this->cp_relationship} a
   		left join {$this->cp_user} b on b.uid = a.uid
   		left join {$this->cp_u_i} c on c.uid = a.uid
   		left join {$this->cp_user} d on d.uid = a.puid where a.id=?";
   		return $this->BcdDb->query($select, $id)->getRow();
   	}
   	
   	/**
   	 * 返点交易明细列表
   	 * @param unknown_type $searchData
   	 * @param unknown_type $page
   	 * @param unknown_type $pageCount
   	 * @return multitype:unknown
   	 */
   	public function rebateDetailsList($searchData, $page, $pageCount)
   	{
   		$where = ' where 1 ';
   		$where .= $this->condition("created", array(
   				$searchData['start_time'],
   				$searchData['end_time']
   		), "time");
   		$where .= $this->condition("uid", $searchData['uid']);
   		$where .= $this->condition("lid", $searchData['lid']);
   		$where .= $this->condition("userName", $searchData['userName']);
   		$sqlCount = "select count(1) count, sum(income) totalMoney from cp_rebate_details {$where}";
   		$count = $this->BcdDb->query($sqlCount)->getRow();
   		$selectSql = "select * from cp_rebate_details {$where} ORDER BY created DESC LIMIT " . ($page - 1) * $pageCount . "," . $pageCount;
   		$result = $this->BcdDb->query($selectSql)->getAll();
   		return array(
   			$result,
   			$count['count'],
   			$count['totalMoney']
   		);
   	}
   	
   	/**
   	 * 下线列表
   	 * @param unknown_type $searchData
   	 * @param unknown_type $page
   	 * @param unknown_type $pageCount
   	 * @return multitype:unknown
   	 */
   	public function subordinateList($searchData, $page, $pageCount)
   	{
   		$where = ' where 1 ';
   		$where .= $this->condition("{$this->cp_relationship}.created", array(
   				$searchData['start_time'],
   				$searchData['end_time']
   		), "time");
   		$where .= $this->condition("{$this->cp_relationship}.puid", $searchData['uid']);
   		$where .= $this->condition("{$this->cp_user}.uname", $searchData['userName']);
   		$countSql = "select count(1) from {$this->cp_relationship} 
        left join {$this->cp_user} on {$this->cp_user}.uid = {$this->cp_relationship}.uid 
        left join {$this->cp_u_i} on {$this->cp_u_i}.uid = {$this->cp_relationship}.uid {$where}";
   		$count = $this->BcdDb->query($countSql)->getOne();
   		$select = "select {$this->cp_relationship}.id, {$this->cp_relationship}.uid, {$this->cp_user}.uname, {$this->cp_u_i}.real_name, {$this->cp_u_i}.phone,
        {$this->cp_relationship}.total_sale, {$this->cp_relationship}.total_income, {$this->cp_relationship}.stop_flag, {$this->cp_relationship}.created
        from {$this->cp_relationship} 
        left join {$this->cp_user} on {$this->cp_user}.uid = {$this->cp_relationship}.uid 
        left join {$this->cp_u_i} on {$this->cp_u_i}.uid = {$this->cp_relationship}.uid
        {$where} ORDER BY {$this->cp_relationship}.created DESC LIMIT " . ($page - 1) * $pageCount . "," . $pageCount;
        $result = $this->BcdDb->query($select)->getAll();
   		return array(
   			$result,
   			$count,
   		);
   	}
   	
   	/**
   	 * 参    数：$uid 用户uid
   	 * 作    者：shigx
   	 * 功    能：返回用户今日收入
   	 * 修改日期：2016.05.09
   	 */
   	public function getTodayIncome($uid)
   	{
   		$start = date('Y-m-d');
   		$end = $start . ' 23:59:59';
   		return $this->BcdDb->query("select sum(income) from cp_rebate_details where uid = ? and (created between ? and ?)", array($uid, $start, $end))->getOne();
   	}
   	
   	//开启、关闭返点
   	public function updateStop($uid, $stop_flag)
   	{
   		return $this->master->query("update cp_relationship set stop_flag = ? where uid=?", array($stop_flag, $uid));
   	}
}
