<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/*
 * 数据中心 -- 期次预排 模型层
 * @author:liuli
 * @date:2015-03-27
 */

class Issue_Model extends MY_Model {

	private $CI;
    public function __construct() {
        parent::__construct();
        //连接 数据中心database
    }

    //获取需要排期的彩种
    public function getLotteryIssue()
    {
        $sql = "SELECT lid, start_date, status FROM cp_issue_rearrange where start_date <= now() and status = 0";
        $data = $this->dc->query($sql)->getAll();
        return $data;
    }

    //获取彩种预排配置信息
    public function getConfigInfo($type)
    {
        try 
        {
            $sql = "SELECT lid, early_time, award_time, issue_num, start_date, delay_start_time, delay_end_time, status FROM cp_issue_rearrange where lid = ? and delect_flag = 0;";
            $info = $this->dc->query($sql, array($type))->getRow(); 
        }
        catch (Exception $e)
        {
            log_message('LOG', "getConfigInfo error: " . __CLASS__ . ':' . __LINE__ , "ERROR");
            return false;
        }
        return $info;
    }

    //获取最新期次信息
    public function getNewIssue($type)
    {
        try 
        {
            $sql = "SELECT issue, sale_time, end_time, award_time FROM cp_".$type."_paiqi where award_time > 0 AND delect_flag = 0 order by issue DESC limit 1;";
            $issueInfo = $this->dc->query($sql)->getRow(); 
        }
        catch (Exception $e)
        {
            log_message('LOG', "getSelectIssue error: " . __CLASS__ . ':' . __LINE__ , "ERROR");
            return false;
        }
        return $issueInfo;
        
    }

    //更新彩种排期
    public function insertIssue($fields, $bdata, $type)
    {

        if(!empty($bdata['s_data']))
        {
            $upd = array('sale_time', 'end_time', 'award_time', 'synflag', 'status', 'd_synflag');
            $sql = "insert cp_".$type."_paiqi(" . implode(', ', $fields) . ") values" . 
            implode(', ', $bdata['s_data']) . $this->onduplicate($fields, $upd);
            $this->dc->query($sql, $bdata['d_data']);
            //触发任务
            $arr = array(
            	'ssq' => 51,
            	'dlt' => 23529,
            	'qxc' => 10022,
            	'qlc' => 23528,
            	'fc3d' => 52,
            	'pl3' => 33,
            	'pl5' => 35,
            	'ks' => 53,
                'jlks' => 56,
                'jxks' => 57,
            	'syxw' => 21406,
            	'jxsyxw' => 21407,
            	'hbsyxw' => 21408,
                'klpk' => 54,
                'cqssc' => 55,
                'gdsyxw' => 21421,
            );
            $result = $this->updateTicketStop(6, $arr[$type], 0);
        }
    }

    //更新预排状态
    public function updateIssueStatus($type, $status)
    {
        $sql = "UPDATE cp_issue_rearrange SET status = ? where lid = ? and status = 0 and delect_flag = 0";
        $data = $this->dc->query($sql,array($status,$type));
    }

    //获取开奖最近的一期
    public function getAwardIssue($type)
    {
        $sql = "SELECT issue, sale_time, end_time, award_time FROM cp_".$type."_paiqi where award_time > now() AND delect_flag = 0 order by issue ASC limit 1;";
        $issueInfo = $this->dc->query($sql)->getRow(); 
        return $issueInfo;
    }
    
    public function getTczqInfo($issue, $ctype)
    {
    	$this->load->model('compare_model');
    	$sql = "select mname from cp_tczq_paiqi where mid = ? and ctype = ? and status = {$this->compare_model->status_map['compare_delay']}";
    	return $this->dc->query($sql, array($issue, $ctype))->getCol(); 
    }
    
	public function getNumberInfo($issue, $ctype)
    {
    	$this->load->model('compare_model');
    	$sql = "select issue from cp_{$ctype}_paiqi where issue = ? and status = {$this->compare_model->status_map['compare_delay']}";
    	return $this->dc->query($sql, array($issue))->getCol(); 
    }
    
    /**
     * 根据类型和彩种id更新任务状态
     * @param int $type
     * @param int $lid
     * @param int $stop
     */
    public function updateTicketStop($type, $lid, $stop)
    {
    	$this->cfgDB = $this->load->database('cfg', TRUE);
    	$this->cfgDB->query("update cp_task_manage set stop= ? where task_type= ? and lid= ?", array($stop, $type, $lid));
    	return $this->cfgDB->affected_rows();
    }
    
}
