<?php
class Model_limit_codes extends MY_Model
{
	public function __construct()
	{
		parent::__construct();
		$this->get_db();
	}
	
	public function getData($lid, $start, $offset)
	{
	    $total = $this->BcdDb->query("select count(*) from cp_limit_codes where lid = ?", array($lid))->getOne();
	    $data = $this->BcdDb->query("select id, issue, playType, codes, msg, endTime, awardTime, created 
				from cp_limit_codes where lid = ? order by status, issue desc limit {$start}, {$offset}", array($lid))->getAll();
		return array('total' => $total, 'data' => $data);
	}
	
	public function getDataByLid($lid, $fields = null, $where = '', $order = 'status, issue desc')
	{
		if (empty($fields)) $fields = 'id, issue, playType, codes, endTime, awardTime, created';
		return $this->BcdDb->query("select {$fields} from cp_limit_codes where lid = ? {$where} order by {$order}", array($lid))->getAll();
	}
	
	public function addCode($data)
	{
		if ($this->db->query("select 1 from cp_limit_codes where lid = '{$data['lid']}' and playType = '{$data['playType']}' and codes = '{$data['codes']}' and status = 0")->getOne()) return false;
		$maxissue = $this->db->query("select max(issue) from cp_limit_codes where lid = '{$data['lid']}'")->getOne();
		$this->db->query("insert into cp_limit_codes (lid, issue, playType, codes, created) 
				values ('{$data['lid']}', '".((int)$maxissue+1)."' , '{$data['playType']}', '{$data['codes']}', NOW())");
		return true;
	}
	
	public function addJcCode($data) {
	    $maxissue = $this->db->query("select max(issue) from cp_limit_codes where lid = '{$data['lid']}'")->getOne();
	    $this->db->query("insert into cp_limit_codes (lid, issue, playType, codes, msg, created)
	        values ('{$data['lid']}', '".((int)$maxissue+1)."' , '{$data['playType']}', '{$data['codes']}', '{$data['msg']}', NOW())");
	    return true;
	}
	
	public function overLimit($id)
	{
		$this->db->query('update cp_limit_codes set status = 1 , endTime = NOW() where id = ?', array($id));
		$info = $this->db->query("select lid, playType, codes from cp_limit_codes where id = ?", array($id))->getRow();
		if (in_array($info['lid'], array(42, 43))) $this->refreshJcCache($info['lid']);
		else $this->refreshCache($info['lid']);
		return $info;
	}
	
	public function refreshCache($lid)
	{
		$this->load->driver('cache', array('adapter' => 'redis'));
		$REDIS = $this->config->item('REDIS');
		$res = $this->getDataByLid($lid, 'issue, playType, codes', ' and status = 0', 'issue asc');
		if ($res)
		{
			foreach ($res as $val)
			{
				$data[$val['playType']][$val['issue']] = $val['codes'];
			}
		}
		$this->cache->hSet($REDIS['LIMIT_CODE'], $lid, json_encode($data));
	}
	
	public function refreshJcCache($lid) {
	    $this->load->driver('cache', array('adapter' => 'redis'));
		$REDIS = $this->config->item('REDIS');
		$res = $this->getDataByLid($lid, 'issue, codes, msg', ' and status = 0', 'issue asc');
		if ($res) {
			foreach ($res as $val) {
			    $codesArr = explode('|', $val['codes']);
				$data[$val['issue']] = array(
				    'matchs' => explode(',', $codesArr[0]),
				    'ggtype' => $codesArr[1],
				    'msg'    => !empty($val['msg']) ? $val['msg'] : '您选择的方案体彩中心已限售，请选择其他方案购买'
				);
			}
		}
		$this->cache->redis->hSet($REDIS['LIMIT_CODE'], $lid, json_encode($data));
	}
}
		