<?php

/*
 * 统计订单销售金额 - 模型层
 * @date:2015-06-03
 */
class Collect_Model extends MY_Model 
{

    public function __construct() 
    {
        parent::__construct();
        $this->order_status = $this->orderConfig('orders');
    }

    public function getCfgPaiqi($lname, $status)
    {
        try 
        {
            $sql = "SELECT issue from cp_{$lname}_paiqi where rstatus >= ? and synflag = 0 limit 10;";
            $lists = $this->cfgDB->query($sql, array($status))->getCol(); 
        }
        catch (Exception $e)
        {
            log_message('LOG', "getCfgPaiqi error: " . __CLASS__ . ':' . __LINE__ , "ERROR");
            return false;
        }
        return $lists;
    }

    // 老足彩排期表
    public function getCfgTczq($tname, $status, $lid)
    {
        $tczqStatus = array(
            '11' => 'rstatus',
            '19' => 'rjrstatus'
        );

        $flag = array(
            '11' => 'synflag',
            '19' => 'rjsynflag'
        );

        try 
        {
            $sql = "SELECT mid from cp_{$tname}_paiqi where {$tczqStatus[$lid]} >= ? and {$flag[$lid]} = 0 limit 10;";
            $lists = $this->cfgDB->query($sql, array($status))->getCol(); 
        }
        catch (Exception $e)
        {
            log_message('LOG', "getCfgTczq error: " . __CLASS__ . ':' . __LINE__ , "ERROR");
            return false;
        }
        return $lists;
    }

    public function countSplitDetail($lid, $issue)
    {
        try 
        {
        	$tables = $this->getSplitTable($lid);
            $sql = "SELECT sum(money) as money, sum(bonus) as bonus, sum(margin) as margin, sum(bonus_t) as bonus_t, sum(margin_t) as margin_t from {$tables['split_table']} where lid = ? and issue = ? and status >= '{$this->order_status['draw']}' and status != '{$this->order_status['concel']}';";
            $bonus = $this->cfgDB->query($sql, array($lid, $issue))->getRow(); 
        }
        catch (Exception $e)
        {
            log_message('LOG', "countSplitDetail error: " . __CLASS__ . ':' . __LINE__ , "ERROR");
            return false;
        }
        return $bonus;
    }

    public function setDistribution($datas)
    {
        $fields = array('lottery_id', 'issue', 'total_sales', 'bonus', 'margin');
        $s_datas = array();
        $d_datas = array();

        array_push($s_datas, '(?, ?, ?, ?, ?, now())');
        foreach ($fields as $field)
        {
            array_push($d_datas, $datas[$field]);
        }

        if(!empty($s_datas))
        {
            $sql  = "insert cp_check_distribution(" . implode(',', $fields) . ", created) values" . implode(',', $s_datas);
            $sql .= $this->onduplicate($fields, array('total_sales', 'bonus', 'margin'));
            return $this->cfgDB->query($sql, $d_datas);
        }
    }

    public function updatePaiqi($tname, $issue, $lid = 0)
    {
        if(in_array($tname, array('rsfc')))
        {
            $flag = array(
                '11' => 'synflag',
                '19' => 'rjsynflag'
            );

            $sql = "update cp_{$tname}_paiqi set {$flag[$lid]} = 1 where mid = ?";
        }
        else
        {
            $sql = "update cp_{$tname}_paiqi set synflag = 1 where issue = ?";
        }
        return $this->cfgDB->query($sql, array($issue));
    }

}
