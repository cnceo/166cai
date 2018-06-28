<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/*
 * ASO广告 - 模型层
 */

class Api_Aso_Model extends MY_Model
{
	public function __construct()
	{
		parent::__construct();
	}
	
	// 设备号统计
	public function recordIdfa($info)
	{
		$fields = array_keys($info);
		$sql = "insert cp_aso_info(" . implode(',', $fields) . ", created)values(" . 
		implode(',', array_map(array($this, 'maps'), $fields)) .  ", now())" . " on duplicate key update version = values(version), callback = IF(cstate >= 0, callback, values(callback)), cstate = (IF(cstate = 1, 2, cstate));";
		return $this->db->query($sql, $info);
	}

	// 设备号去重
	public function checkIdfa($idfa)
	{
		$sql = "SELECT appid, idfa, version, callback, cstate, try_num FROM cp_aso_info WHERE idfa IN('" . implode("','", $idfa) . "');";
		return $this->db->query($sql)->getAll();
	}

	// 推广激活设备号
	public function activateIdfa($info)
	{
		$fields = array_keys($info);
		$sql = "insert cp_aso_info(" . implode(',', $fields) . ", created)values(" . 
		implode(',', array_map(array($this, 'maps'), $fields)) .  ", now())" . " on duplicate key update callback = IF(cstate >= 0, callback, values(callback)), cstate = (IF(cstate >= 0, cstate, values(cstate)));";
		$this->db->query($sql, $info);
		return $this->db->affected_rows();
	}

	// 扫描推送记录
	public function getAsoCallback()
	{
		$sql = "SELECT appid, idfa, callback, cstate, modified FROM cp_aso_info WHERE modified > date_sub(now(), interval 30 minute) AND cstate = 2 AND try_num < 3 ORDER BY modified DESC LIMIT 100;";
		return $this->db->query($sql)->getAll();
	}

	// 异步回调通知
	public function updateNotice($info)
	{
		$this->db->query("UPDATE cp_aso_info SET try_num = try_num + 1, cstate = ? WHERE idfa = ?", array($info['cstate'], $info['idfa']));
	}
}
