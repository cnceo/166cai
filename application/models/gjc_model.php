<?php
class Gjc_Model extends MY_Model {
    
    private $issue;
	
	public function __construct() {
	    $this->issue = '18001';
	}
	
	public function getTeams()
	{
		$sql = "select  mid, `name`, odds, logo, status from cp_champion_paiqi
				where type = 1 and issue = '{$this->issue}' order by groups, mid";
		return $this->dc->query($sql)->getAll();
	}
	
	public function getCombines()
	{
		$sql = "select  mid, `name`, odds, logo, status from cp_champion_paiqi
				where type = 2 and issue = '{$this->issue}' order by groups, mid";
		return $this->dc->query($sql)->getAll();
	}
	
	public function getScheduleByGroup($group) {
		$sql = "select id, begin_time, home, away, groups, type, hid, aid, mid from cp_champion_schedule";
		$data = array();
		if ($group && $group === 'other') {
			$sql .= " where type = 2";
		}elseif ($group) {
			$sql .= " where groups = ?";
			$data = array($group);
		}
		$sql .= " order by begin_time, groups";
		$res = $this->dc->query($sql, $data)->getAll();
		$mid = array();
		foreach ($res as $val) {
			if ($val['mid']) {
				$mid[] = $val['mid'];
			}
		}
		if (!empty($mid)) {
			$this->cfgDB = $this->load->database('cfg', true);
			$sqlpq = "select full_score, half_score, sale_status, mid from cp_jczq_paiqi where mid in (".implode(',', $mid).")";
			$respq = $this->cfgDB->query($sqlpq, $data)->getAll();
			foreach ($respq as $val) {
				$resmid[$val['mid']] = $val;
			}
			foreach ($res as &$val) {
				if ($val['mid']) {
					$val['full_score'] = $resmid[$val['mid']]['full_score'];
					$val['half_score'] = $resmid[$val['mid']]['half_score'];
					$val['sale_status'] = $resmid[$val['mid']]['sale_status'];
				}
			}
		}
		
		return $res;
	}
	
	public function getTaotai()
	{
		$sql = "select id, hid, aid, home, away from cp_champion_schedule where type=2";
		return $this->dc->query($sql)->getAll();
	}

	public function checkStatus($mids, $lid) {
	    return $this->dc->query("select 1 from cp_champion_paiqi where mid in ? and status <> '0' and type = '".($lid - 43)."' and issue = '{$this->issue}'", array($mids))->getOne();
	}
}