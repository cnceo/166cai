<?php
class Model_Ajust_Umoney extends MY_Model
{
	
	public function __construct()
	{
		parent::__construct();
		$this->get_db();
	}
	
	public function insertData($datas)
	{
		$this->load->library('tools');
		if (!empty($datas))
    	{
    		$field = '';
    		$fields = array_keys($datas[0]);
    		foreach ($fields as $value)
    		{
    			$field .= $value.", ";
    		}
    		$field .= 'num';
    		$sql = "insert into cp_adjust_umoney_log ({$field}) values ";
    		foreach ($datas as $data)
    		{
    			$sql .= "(";
    			foreach ($fields as $value)
    			{
    				$sql .= "'{$data[$value]}', ";
    			}
    			$num = $this->tools->getIncNum('UNIQUE_KEY');
    			$sql .= $num."), ";
    		}
    		$sql = substr($sql, 0, -2);
    		$this->master->query($sql);
    		return $this->master->affected_rows();
    	}
    	return false;
	}
	
	public function listData($searchData, $page, $pageCount)
	{
		$where = 'where 1';
		$where .= $this->condition(" aul.created", array($searchData['start_time'], $searchData['end_time']), "time");
		unset($searchData['start_time'], $searchData['end_time']);
		foreach ($searchData as $key => $val) {
			if($key == 'status' && $val === '01'){
				$where .= " and aul.status in ('0', '1')";
			}elseif ($this->emp($val)) {
				if ($key === 'uname') {
					$where .= " and u.`{$key}` = ?";
					$data[] = $val;
				}
				elseif ($key === 'comment')
				{
					$where .= $this->condition("aul.{$key}", $val, "like");
				}
				else {
					$where .= " and aul.`{$key}` = ?";
					$data[] = $val;
				}
			}
		}
		
		$sql = "select aul.id, aul.num, aul.uid, u.uname, ui.real_name, aul.type, aul.ctype, aul.money, aul.ismustcost, aul.iscapital,
		aul.comment, aul.status, u.money as umoney, aul.created, aul.review_time, aul.failreason
		from cp_adjust_umoney_log as aul inner join cp_user as u on aul.uid=u.uid left join cp_user_info as ui on aul.uid=ui.uid 
				{$where} order by aul.created desc LIMIT " . ($page - 1) * $pageCount . "," . $pageCount;
				$res = $this->BcdDb->query($sql, $data)->getAll();
				$count = $this->BcdDb->query("select count(*) as count, count(distinct aul.uid) as ucount, sum(case when `status`='0' then 1 else 0 end) as dcount 
				from cp_adjust_umoney_log as aul inner join cp_user as u on aul.uid=u.uid {$where}", $data)->getRow();
		return array('data' => $res, 'count' => $count);
	}
	
	public function adjust($id)
	{
		$REDIS = $this->config->item('REDIS');
		$this->load->driver('cache', array('adapter' => 'redis'));
		$this->load->library('tools');
		$this->load->model('model_user');
		$data = $this->master->query("select * from cp_adjust_umoney_log where num = ? and status='0' for update", array($id))->getRow();
		if ($data && $data['type'] === '0') {//加款
			$this->master->trans_start();
			//必花
			if ($data['ismustcost']) {
				$field = 'must_cost';
				if ($data['ctype'] == 1) {
					$field = 'dispatch';
				}
			}
			$this->master->query("select money ".(isset($field) ? ", ".$field : '')." from cp_user where uid = ? for update", array($data['uid']));
			$this->master->query("update cp_user set money = money + {$data['money']}".(isset($field) ? ", {$field} = {$field} + {$data['money']}" : '' )." where uid = ?", array($data['uid']));
			if (!$this->master->affected_rows()) {
				$this->master->trans_rollback();
				return false;
			}
			//流水
			$trade_no = $this->tools->getIncNum('UNIQUE_KEY');
			if (!empty($data['orderId']) && !$orderType = $this->master->query('select orderType from cp_orders where orderId = ?', array($data['orderId']))->getOne()) {
		        if ($orderId = $this->master->query('select orderId from cp_united_join where subscribeId = ?', array($data['orderId']))->getOne()) {
		            $data['subscribeId'] = $data['orderId'];
		            $data['orderId'] = $orderId;
		            $orderType = 4;
		        } else {
		            $this->master->trans_rollback();
		            return false;
		        }
			}
			
			$status = (isset($orderType) && $orderType == 4) ? 3 : 0;
			
			$wctypeArr = array(1 => 9, 2 => 2, 3 => 10);
			$res = $this->master->query("insert cp_wallet_logs(uid, ctype, mark, trade_no, orderId, money, umoney, status, created, content, cstate)
					select uid, '{$wctypeArr[$data['ctype']]}', '1', {$trade_no}, '{$data['orderId']}', {$data['money']}, money, '{$status}', now(), '{$data['comment']}', '1' from cp_user where uid = ?", array($data['uid']));
			if (!$this->master->affected_rows()) {
				$this->master->trans_rollback();
				return false;
			}
			//总账
			if ($data['iscapital']) {
				//$this->master->query("select money from cp_capital where id = 2 for update");
				//$this->master->query("update cp_capital set money = money - {$data['money']} where id = 2");
				$this->master->query("insert into cp_capital_log (capital_id, trade_no, ctype, money, status, created) values ('2', {$trade_no}, '10', {$data['money']}, 2, now())");
				if (!$this->master->affected_rows()) {
					$this->master->trans_rollback();
					return false;
				}
			}
			$this->master->query("update cp_adjust_umoney_log set status='1', review_time = now(), trade_no = '{$trade_no}' where num = ? and status='0'", array($id));
			if (!$this->master->affected_rows()) {
				$this->master->trans_rollback();
				return false;
			}
			$this->master->trans_complete();
			$ukey = $REDIS['USER_INFO'] . $data['uid'];
			$uinfo = $this->cache->redis->hGet($ukey, "uname");
			if(!empty($uinfo))
			{
				$uinfo = $this->master->query("select  money from cp_user where uid = ?", array($data['uid']))->getRow();
				if(!empty($uinfo))
				{
					$this->cache->redis->hSet($ukey, "money", $uinfo['money']);
				}
			}
			else
			{
				$this->model_user->freshUserInfo($data['uid']);
			}
			
			return true;
		}else if ($data) {
			$this->master->trans_start();
			//必花
			$udata = $this->master->query("select money, must_cost, dispatch from cp_user where uid = ? for update", array($data['uid']))->getRow();
			if ($udata['dispatch'] > $data['money']) {
				$this->master->query("update cp_user set money = money-{$data['money']}, dispatch = dispatch - {$data['money']} where uid = ?", array($data['uid']));
			}elseif ($udata['dispatch'] + $udata['must_cost'] > $data['money']) {
				$this->master->query("update cp_user set money = money-{$data['money']}, must_cost = must_cost + {$udata['dispatch']} - {$data['money']}, dispatch = 0 where uid = ?", array($data['uid']));
			}elseif ($udata['money'] >= $data['money']) {
				$this->master->query("update cp_user set money = money-{$data['money']}, must_cost = 0, dispatch = 0 where uid = ?", array($data['uid']));
			}else {
				$this->master->trans_rollback();
				return 'moneyless';
			}
			if (!$this->master->affected_rows()) {
				$this->master->trans_rollback();
				return false;
			}
			//流水
			$trade_no = $this->tools->getIncNum('UNIQUE_KEY');
			$res = $this->master->query("insert cp_wallet_logs(uid, ctype, mark, trade_no, orderId, money, umoney, created, content, cstate)
				select uid, '11', '0', {$trade_no}, '{$data['orderId']}', {$data['money']}, money, now(), '{$data['comment']}', '1' from cp_user where uid = ?", array($data['uid']));
				if (!$this->master->affected_rows()) {
				$this->master->trans_rollback();
				return false;
			}
			//总账
			if ($data['iscapital']) {
				//$this->master->query("select money from cp_capital where id = 2 for update");
				//$this->master->query("update cp_capital set money = money + {$data['money']} where id = 2");
				$this->master->query("insert into cp_capital_log (capital_id, trade_no, ctype, money, status, created) values ('2', {$trade_no}, '10', {$data['money']}, 1, now())");
				if (!$this->master->affected_rows()) {
					$this->master->trans_rollback();
					return false;
				}
			}
			$this->master->query("update cp_adjust_umoney_log set status='1', review_time = now(), trade_no = '{$trade_no}' where num = ? and status='0'", array($id));
			if (!$this->master->affected_rows()) {
				$this->master->trans_rollback();
				return false;
			}
			$this->master->trans_complete();
			$ukey = $REDIS['USER_INFO'] . $data['uid'];
			$uinfo = $this->cache->redis->hGet($ukey, "uname");
			if(!empty($uinfo))
			{
				$uinfo = $this->master->query("select  money from cp_user where uid = ?", array($data['uid']))->getRow();
				if(!empty($uinfo))
				{
					$this->cache->redis->hSet($ukey, "money", $uinfo['money']);
				}
			}
			else
			{
				$this->model_user->freshUserInfo($data['uid']);
			}
			
			return true;
		}
	}
	
	public function adjustfail($id, $failreason) {
		return $this->master->query("update cp_adjust_umoney_log set review_time=now(), failreason = ?, status = '2' where num = ?", array($failreason, $id));
	}
	
	public function check_order($uid, $orderId = null, $phone = null) {
		if ($phone && $orderId) {
		if ($this->BcdDb->query("select o.id from cp_orders as o inner join cp_user_info as ui on o.uid=ui.uid 
			where o.uid = ? and ui.phone = ? and o.orderId = ?", array($uid, $phone, $orderId))->getOne()) return true;
			if ($this->BcdDb->query("select o.id from cp_united_join as o inner join cp_user_info as ui on o.uid=ui.uid 
			where o.uid = ? and ui.phone = ? and o.subscribeId = ?", array($uid, $phone, $orderId))->getOne()) return true;
		}else if ($phone) { 
		if ($this->BcdDb->query("select id from cp_user_info where uid = ? and phone = ?", array($uid, $phone))->getOne()) return true;
		}else if ($orderId) { 
		if ($this->BcdDb->query("select id from cp_orders where uid = ? and orderId = ?", array($uid, $orderId))->getOne()) return true;
		if ($this->BcdDb->query("select id from cp_united_join where uid = ? and subscribeId = ?", array($uid, $orderId))->getOne()) return true;
		}
		return false;
	}
}