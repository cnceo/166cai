<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/*
 * 用户标签 - 模型层
 */

class Tag_Model extends MY_Model
{
	public function __construct()
	{
		parent::__construct();
		$this->tplTable = "{$this->db_config['datatmp']}.cp_tag_logic_operator_tpl";
	}

	// 表字段映射关系
	private $table_map = array(
		'orders'	=>	array(
			'tableName'	=>	'cp_orders',	// 表名
			'suffix'	=>	'1',			// 分表逻辑
			'fields'	=>	'uid, (money - failMoney) as money, created',
		),
		'united'	=>	array(
			'tableName'	=>	'cp_united_join',
			'suffix'	=>	'0',
			'fields'	=>	'uid, money, created',
		),
	);

	// 获取标签配置信息
	public function getTagData()
	{
		$sql = "SELECT id, base_type, sub_type, conditions, scope, runtime FROM cp_tag_name WHERE rundate < DATE_FORMAT(NOW(),'%Y-%m-%d') AND delete_flag = 0 AND (cstate & 1 = 0) ORDER BY id LIMIT 10";
		return $this->data->query($sql)->getAll();
	}
	
	// 查询指定条件的用户数
	public function getDataByTable($date, $table, $sqlCons, $start = 0, $limit = 500)
	{
		// 考虑分表逻辑
		$tableSuffix = '';
		if($this->table_map[$table]['suffix']) $tableSuffix = $this->tools->getTableSuffixByDate($date['start']);
		if($tableSuffix && $tableSuffix < '2014')
    	{
    		return array();
    	}
    	if($tableSuffix) $tableSuffix = '_' . $tableSuffix;

		$sql = "SELECT {$this->table_map[$table]['fields']} FROM {$this->table_map[$table]['tableName']}{$tableSuffix} WHERE 1 AND created >= '{$date['start']}' AND created <= '{$date['end']}' AND status >= 500 AND status not in(600, 610, 620){$sqlCons} ORDER BY id ASC LIMIT " . $start * $limit . ", $limit";
		return $this->slave->query($sql)->getAll();
	}

	// 汇总用户标签统计信息
	public function insertTagUserCollect($fields, $bdata)
	{
		$sql = "INSERT cp_tag_user_collect(" . implode(', ', $fields) . ") values" . 
            implode(', ', $bdata['s_data']) . " on duplicate key update total_money = total_money + values(total_money), total_day = IF(DATE_FORMAT(values(last_buy_time),'%Y%m%d') > statistic_time, total_day + values(total_day), total_day), total_buy_num = total_buy_num + 1, last_buy_time = IF(values(last_buy_time) > last_buy_time, values(last_buy_time), last_buy_time), statistic_time = values(statistic_time)";
        $this->data->query($sql, $bdata['d_data']);
	}

	// 统计逻辑汇总表数据
	public function insertTagLogicOperator($fields, $bdata)
	{
		$sql = "INSERT IGNORE cp_tag_logic_operator(" . implode(', ', $fields) . ") values" . implode(', ', $bdata['s_data']);
        $this->data->query($sql, $bdata['d_data']);
	}

	// 批量查询逻辑表指定分类数据
	public function getDataByLogic($sqlCons, $start = 0, $limit = 500)
	{
		$sql = "SELECT uid FROM cp_tag_logic_operator WHERE 1{$sqlCons} ORDER BY id ASC LIMIT " . $start * $limit . ", $limit";
		return $this->data->query($sql)->getAll();
	}

	// 清空临时表
	public function truncateTagLogicTpl()
    {
    	return $this->data->query("truncate {$this->tplTable}");
    }

    // 入库临时表
    public function insertTagLogicTpl($fields, $bdata)
    {
    	$sql = "INSERT IGNORE {$this->tplTable}(" . implode(', ', $fields) . ") values" . implode(', ', $bdata['s_data']);
        $this->data->query($sql, $bdata['d_data']);
    }

	// 交集插入临时表并联表查询结果集再入实际标签用户
	public function compareTagUser($date, $tag_id, $sqlCons)
	{
		$fields = array('date', 'tag_id', 'uid', 'created');
		$sql = "INSERT cp_tag_user(" . implode(', ', $fields) . ") (SELECT t2.date, t2.tag_id, t2.uid, NOW() AS created FROM {$this->tplTable} AS t2 LEFT JOIN cp_tag_user_collect AS t1 ON t2.date = t1.date AND t2.tag_id = t1.tag_id AND t2.uid = t1.uid WHERE t2.date = ? AND t2.tag_id = ?{$sqlCons} GROUP BY t2.uid)";
        $this->data->query($sql, array($date, $tag_id));
	}

	// 汇总统计实际标签用户
	public function collectTagUser($info, $sqlCons = '', $start = 0, $limit = 500)
	{
		$sql = "SELECT t1.date, t1.tag_id, t1.uid FROM cp_tag_user_collect AS t1 WHERE t1.date = ? AND t1.tag_id = ?{$sqlCons} ORDER BY t1.id ASC LIMIT " . $start * $limit . ", $limit";
		return $this->data->query($sql, array($info['rundate'], $info['id']))->getAll();
	}

	// 标签最终用户保存
	public function insertTagUser($fields, $bdata)
	{
		$sql = "INSERT IGNORE cp_tag_user(" . implode(', ', $fields) . ") values" . 
            implode(', ', $bdata['s_data']);
        $this->data->query($sql, $bdata['d_data']);
	}

	// 更新标签处理时间
	public function updateTagName($info, $dateType = 0)
	{
		$sqlCons = "";
		if($dateType) $sqlCons = ", cstate = (cstate | 1)";
		$sql = "UPDATE cp_tag_name SET rundate = ?, runtime = ?{$sqlCons} WHERE id = ?";
		return $this->data->query($sql, array($info['rundate'], $info['runtime'], $info['id']));
	}

	// 分片获取用户标签数据
	public function getTagUserData($info, $start = 0, $limit = 500)
	{
		$sql = "SELECT uid FROM cp_tag_user WHERE date = ? AND tag_id = ? ORDER BY id ASC LIMIT " . $start * $limit . ", $limit";
		return $this->data->query($sql, array($info['date'], $info['tag_id']))->getAll();
	}

	// 获取标签集群表维护信息
	public function getClusterData()
	{
		$sql = "SELECT id, ctype, tag_ids, conditions, update_logic, update_date FROM cp_tag_cluster WHERE (update_logic = 1 AND DATE_FORMAT(update_date, '%Y%m%d') < DATE_FORMAT(now(), '%Y%m%d')) OR (update_logic = 2 AND DATE_FORMAT(update_date, '%Y%m%d') < DATE_FORMAT(now(), '%Y%m%d') AND DAYOFWEEK(now()) = 2) OR (update_logic = 3 AND DATE_FORMAT(update_date, '%Y%m%d') < DATE_FORMAT(now(), '%Y%m%d') AND DATE_FORMAT(now(), '%d') = '01') AND delete_flag = 0 ORDER BY id";
		return $this->data->query($sql)->getAll();
	}

	// 获取标签最近更新时间
	public function getTagLastDate($tag_id)
	{
		$sql = "SELECT date FROM cp_tag_user WHERE tag_id = ? ORDER BY date DESC LIMIT 1";
		return $this->data->query($sql, array($tag_id))->getRow();
	}

	// 更新时间
	public function updateClusterDate($clusterId, $conditions)
	{
		$sql = "UPDATE cp_tag_cluster SET conditions = ?, update_date = now() WHERE id = ?";
		return $this->data->query($sql, array($conditions, $clusterId));
	}

	// 获取指定标签的配置
	public function getTagInfoById($tag_id)
	{
		$sql = "SELECT id, base_type, sub_type, conditions, scope, runtime FROM cp_tag_name WHERE id = ?";
		return $this->data->query($sql, array($tag_id))->getRow();
	}

	// 清空指定数据
	public function deleteTagData($tag_id, $date)
	{
		$sql1 = "DELETE FROM cp_tag_user_collect WHERE tag_id = ? AND date = ?";
		$this->data->query($sql1, array($tag_id, $date));

		$sql2 = "DELETE FROM cp_tag_user WHERE tag_id = ? AND date = ?";
		$this->data->query($sql2, array($tag_id, $date));
	}
}
