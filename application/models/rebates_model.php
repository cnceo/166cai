<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Rebates_Model extends MY_Model
{
	/**
     * 参    数：$uid 用户uid
     * 作    者：shigx
     * 功    能：查询用户返点配置信息
     * 修改日期：2016.05.09
     */
    public function getRebatesByUid($uid)
    {
        return $this->db->query("select * from cp_relationship where uid = ?", array($uid))->getRow();
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
    	return $this->db->query("select sum(income) from cp_rebate_details where uid = ? and (created between ? and ?)", array($uid, $start, $end))->getOne();
    }
    
    /**
     * 查询返点明细
     * @param unknown_type $cons
     * @param unknown_type $cpage
     * @param unknown_type $psize
     */
    public function getRebatesDetail($cons, $cpage, $psize)
    {
    	$constr = "and uid = ? and created >= ? and created <= ? ";
    	if( $cons['lid'] == 'all' || empty($cons['lid']))
    	{
    		unset( $cons['lid'] );
    	}
    	else
    	{
    		$constr .= " AND lid = ?";
    	}
    	
    	if(empty($cons['userName']))
    	{
    		unset($cons['userName']);
    	}
    	else
    	{
    		$constr .= " AND userName = ?";
    	}
    	$sqlCount = "select count(1) count, sum(income) totalMoney from cp_rebate_details where 1 {$constr}";
    	$count = $this->db->query($sqlCount, $cons)->getRow();
    	$selectSql = "select puid,uid,userName,lid,issue,money,income,created from cp_rebate_details where 1 {$constr} ORDER BY created DESC LIMIT " . ($cpage - 1) * $psize . "," . $psize;
    	$result = $this->db->query($selectSql, $cons)->getAll();
    	return array(
    		$result,
    		$count['count'],
    		$count['totalMoney']
    	);
    }
    
    /**
     * 查询我的下线
     * @param unknown_type $cons
     * @param unknown_type $cpage
     * @param unknown_type $psize
     */
    public function getSubordinate($cons, $cpage, $psize, $orderBy = '')
    {
    	$constr = "and a.puid = ? and a.created >= ? and a.created <= ? ";
    	$orderStr = " ORDER BY id DESC";
    	if(empty($cons['uname']))
    	{
    		unset($cons['uname']);
    	}
    	else
    	{
    		$constr .= " AND b.uname = ?";
    	}
    	if($orderBy == 'asc')
    	{
    		$orderStr = " ORDER BY total_sale ASC";
    	}
    	elseif ($orderBy == 'desc')
    	{
    		$orderStr = " ORDER BY total_sale DESC";
    	}
    	$sqlCount = "select count(1) count from cp_relationship a left join cp_user b on b.uid= a.uid where 1 {$constr}";
    	$count = $this->db->query($sqlCount, $cons)->getRow();
    	$selectSql = "select a.id, a.uid,b.uname,a.created,a.rebate_odds,a.total_sale,a.stop_flag from cp_relationship a left join cp_user b on b.uid=a.uid where 1 {$constr} {$orderStr} LIMIT " . ($cpage - 1) * $psize . "," . $psize;
    	$result = $this->db->query($selectSql, $cons)->getAll();
    	return array(
    		$result,
    		$count['count'],
    	);
    }
    
    /**
     * 检查用户是否符合添加
     * @param unknown_type $uname
     * @param unknown_type $phone
     */
    public function checkRebateUser($uname, $phone)
    {
    	$sql = "select u.uid,u.rebates_level,channel from cp_user u join cp_user_register r on r.id = u.uid where u.uname=? and r.phone=?";
    	$res = $this->db->query($sql, array($uname, $phone))->getRow();
    	$channelinfo = $this->db->query('select unit_price, share_ratio from cp_channel where id=?', array($res['channel']))->getRow();
    	if($res['uid'] && empty($res['rebates_level']) && $res['channel'] !== '10075' && $channelinfo['unit_price'] == 0 && $channelinfo['share_ratio'] == 0)
    	{
    		$sql1 = "select count(1) from cp_relationship where uid=?";
    		$result = $this->db->query($sql1, array($res['uid']))->getOne();
    		if($result <= 0)
    		{
    			return $res['uid'];
    		}
    	}
    	return false;
    }
    
    /**
     * 添加二级用户
     * @param unknown_type $uid
     */
    public function addRebate($uid, $puid, $source = 0)
    {
    	$rebate_odds = '{"42":"0.0","43":"0.0","51":"0.0","23529":"0.0","52":"0.0","23528":"0.0","21406":"0.0","10022":"0.0","33":"0.0","35":"0.0","11":"0.0","19":"0.0","21407":"0.0","53":"0.0","21408":"0.0","54":"0.0","55":"0.0"}'; //初始化配置
    	$this->db->trans_start();
    	$sql = "insert into cp_relationship(puid, uid, rebate_odds, source, created) values (?, ?, '{$rebate_odds}', ?, now())";
    	$res1 = $this->db->query($sql, array($puid, $uid, $source));
    	$res2 = $this->db->query("update cp_user set rebates_level = 3 where uid = ?", $uid);
    	$res = $res1 && $res2;
    	if ($res)
    	{
    		$this->db->trans_complete();
    		//刷新用户钱包缓存
    		$rediskeys = $this->config->item("REDIS");
    		$this->load->driver('cache', array('adapter' => 'redis'));
    		if($this->cache->redis->hGet($rediskeys['USER_INFO'] . $uid, "uname"))
    		{
    			$this->cache->redis->hSet($rediskeys['USER_INFO'] . $uid, "rebates_level", 3);
    		}
    		else
    		{
    			$this->load->model('user_model');
    			$this->user_model->freshUserInfo($uid);
    		}
    		
    		return true;
    	}
    	else
    	{
    		$this->db->trans_rollback();
    		return false;
    	}
    }
    
    /**
     * 添加二级用户
     * @param unknown_type $uid
     * @param unknown_type $rebateId
     */
    public function RegAddRebate($uid, $rebateId)
    {
    	$sql = "select puid,uid from cp_relationship where id=?";
    	$rebate = $this->db->query($sql, $rebateId)->getRow();
    	if($rebate && empty($rebate['puid']))
    	{
    		return $this->addRebate($uid, $rebate['uid'], 1);
    	}
    	return false;
    }
    
    /**
     * 更新二级用户返点信息
     * @param unknown_type $uid
     * @param unknown_type $data
     */
    public function updateRebateOdd($uid, $odds, $rebates_level)
    {
    	$this->db->trans_start();
    	$res1 = $this->db->query("update cp_relationship set rebate_odds=? where uid=?", array($odds, $uid));
    	$res2 = $this->db->query("update cp_user set rebates_level = ? where uid = ?", array($rebates_level, $uid));
    	if ($res1 && $res2)
    	{
    		$this->db->trans_complete();
    		//刷新用户钱包缓存
    		$rediskeys = $this->config->item("REDIS");
    		$this->load->driver('cache', array('adapter' => 'redis'));
    		if($this->cache->redis->hGet($rediskeys['USER_INFO'] . $uid, "uname"))
    		{
    			$this->cache->redis->hSet($rediskeys['USER_INFO'] . $uid, "rebates_level", $rebates_level);
    		}
    		else
    		{
    			$this->load->model('user_model');
    			$this->user_model->freshUserInfo($uid);
    		}
    		
    		return true;
    	}
    	else
    	{
    		$this->db->trans_rollback();
    		return false;
    	}
    }
    
    /**
     * 查询一级、二级
     * @param unknown_type $uid
     * @param unknown_type $puid
     */
    public function getRebatesOdd($uid, $puid)
    {
    	return $this->db->query("SELECT a.rebate_odds, a.total_income,b.rebate_odds upOdds FROM cp_relationship a LEFT JOIN cp_relationship b ON a.puid = b.uid WHERE a.uid=? and a.puid=?", array($uid, $puid))->getRow();
    }
    
    //获得今日添加下线记录
    public function getRebatesLog($uid)
    {
    	$count = $this->db->query("SELECT count(*) FROM cp_relationship
    	WHERE puid = ? AND (created >= date(now()) && created < date(date_add(now(), INTERVAL 1 DAY))) AND source=0", array($uid))->getOne();
    	if($count >= 30)
    	{
    		return true;
    	}
    	return false;
    }
    
    /**
     * 查询需要设置二级返点的一级用户返点信息
     */
    public function getSetRebate()
    {
    	$sql = "select uid,rebate_odds from cp_relationship where modified > date_sub(now(), interval 1 day) and puid=0 and odd_flag=1 limit 10";
    	return $this->db->query($sql)->getAll();
    }
    
    /**
     * 脚本方法  查询一级用户的下线返点比例
     * @param unknown_type $puid
     */
    public function getSubOdds($puid)
    {
    	return $this->db->query("select uid,rebate_odds from cp_relationship where puid=?", array($puid))->getAll();
    }
    
    /**
     * 根据uid更新返点信息
     * @param unknown_type $uid
     * @param unknown_type $data
     */
    public function upBebate($uid, $data)
    {
    	$this->db->where('uid', $uid);
    	$this->db->update('cp_relationship', $data);
    	return $this->db->affected_rows();
    }
}
