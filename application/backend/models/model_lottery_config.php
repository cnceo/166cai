<?php
/**
 * Copyright (c) 2015,上海二三四五网络科技有限公司.
 * 摘    要: 彩种配置
 * 作    者: 刁寿钧
 * 修改日期: 2015/5/28
 * 修改时间: 16:07
 */

class Model_Lottery_Config extends MY_Model{
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * 参    数：page
     *           pageCount
     * 作    者：刁寿钧
     * 功    能：取出所有配置规则
     * 修改日期：2015-06-25
     */
    public function fetchConfigItems($page, $pageCount)
    {
        $sql1 = "SELECT lottery_id lotteryId, status, window, ahead, united_ahead, united_status,order_limit FROM cp_lottery_config LIMIT ". ($page - 1) * $pageCount . "," . $pageCount;
        $items = $this->slaveCfg1->query($sql1)->getAll();

        $sql2 = "SELECT count(*) FROM cp_lottery_config";
        $count = $this->slaveCfg1->query($sql2)->getCol();

        return array($items,$count[0]);
    }

    /**
     * 参    数：lotteryId
     *           params
     * 作    者：刁寿钧
     * 功    能：设置彩种规则
     * 修改日期：2015-06-25
     */
    public function setConfigItems($lotteryId, $params, $flag = true)
    {
        $columnSql = "SHOW COLUMNS FROM cp_lottery_config";
        $columns = $this->cfgDB->query($columnSql)->getAll();
        $fields = array();
        foreach ($columns as $col)
        {
            array_push($fields, $col['Field']);
        }

        $legalKeys = array_intersect(array_keys($params), $fields);
        $setAry = array();
        $valAry = array();
        foreach ($params as $key => $value)
        {
            if (in_array($key, $legalKeys))
            {
                array_push($setAry, "$key = ?");
                array_push($valAry, $value);
            }
        }
        array_push($valAry, $lotteryId);
		if($lotteryId == 19 && $flag)
		{
			$updateSqlSfc = "UPDATE cp_lottery_config SET " . implode(',', $setAry) . " WHERE lottery_id = 11";
			$result = $this->cfgDB->query($updateSqlSfc, $valAry);
		}
        $updateSql = "UPDATE cp_lottery_config SET " . implode(',', $setAry) . " WHERE lottery_id = ?";
        $result = $this->cfgDB->query($updateSql, $valAry);
        if($result)
        {
        	$this->load->model('cache_model');
        	$this->cache_model->refreshLotteryConfig();//彩种管理缓存
        	return $result;
        }
    }



/**
 * 参    数：lotteryId
 *           params
 * 作    者：liuz
 * 功    能：获取彩种信息
 * 修改日期：2015-11-17
 */
public function getConfigItems($data)
{
    $sql = "select status, window, ahead, united_ahead, united_status,order_limit  from  cp_lottery_config where lottery_id = \"".$data."\";";
    $res = $this->slaveCfg1->query($sql)->row_array();
    return $res;

}

public function updateTaskStop($type, $lid, $stop)
{
	$lidmap = array('19' => 10);
	$lid = empty($lidmap[$lid]) ? $lid : $lidmap[$lid];
	$this->cfgDB->query("update cp_task_manage set stop= ? where task_type= ? and lid= ?", array($stop, $type, $lid));
	return $this->cfgDB->affected_rows();
}

}