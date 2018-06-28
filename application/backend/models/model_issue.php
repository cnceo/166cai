<?php

class Model_issue extends MY_Model
{

    private $CI;
    public function __construct() {
        parent::__construct();
        //连接 数据中心database
        $this->dbDc = $this->load->database('dc', true);
        $this->CI = &get_instance();

    }

    //获取彩种预排配置信息
    public function getConfigInfo($type)
    {
        try 
        {
            $sql = "SELECT lid, early_time, award_time, issue_num, start_date, delay_start_time, delay_end_time, status FROM cp_issue_rearrange where lid = ? and delect_flag = 0;";
            $info = $this->slaveDc->query($sql, array($type))->getRow(); 
        }
        catch (Exception $e)
        {
            log_message('LOG', "getConfigInfo error: " . __CLASS__ . ':' . __LINE__ , "ERROR");
            return false;
        }
        return $info;
    }

    //获取对应期号数据
    public function getSelectIssue($issue, $type)
    {
        try 
        {
            $sql = "SELECT issue, sale_time, end_time, award_time FROM cp_".$type."_paiqi where issue >= ? and delect_flag = 0;";
            $issueInfo = $this->slaveDc->query($sql, array($issue))->getAll(); 
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
            $upd = array('sale_time', 'end_time', 'award_time', 'status');
            $sql = "insert cp_".$type."_paiqi(" . implode(', ', $fields) . ") values" . 
            implode(', ', $bdata['s_data']) . $this->onduplicate($fields, $upd);;
            $this->dbDc->query($sql, $bdata['d_data']);
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

    //更新彩种推迟信息
    public function updateDelayConfig($data)
    {
        $sql = "UPDATE cp_issue_rearrange SET delay_start_time = ?, delay_end_time = ? where lid = ? and delect_flag = 0";
        $this->dbDc->query($sql, array($data['delay_start_time'],$data['delay_end_time'],$data['type']));
    }


    //更新预设配置表 + 更新脚本启动表
    public function modifyPreIssue($data)
    {
        try 
        {
            $sql1 = "UPDATE cp_issue_rearrange SET early_time = ?, start_date = ?, issue_num = ?, status = 0 where lid = ? and delect_flag = 0;";
            $res1 = $this->dbDc->query($sql1, array($data['early_time'],$data['start_date'],$data['issue_num'],$data['type'])); 

            // $actName = 'preIssueByType/'.$data['type'];
            // $sql2 = "UPDATE cp_cron_list SET span = 0, state = 1, start_time = '', end_time = '0000-00-00 00:00:00' where act = ?;";
            // $res2 = $this->dbDc->query($sql2,array($actName));

        }
        catch (Exception $e)
        {
            log_message('LOG', "modifyPreIssue error: " . __CLASS__ . ':' . __LINE__ , "ERROR");
            return false;
        }
        return $res1;
    }

    //查询数据
    public function getIssueData($data)
    {
        $sql = "select early_time, start_date, issue_num, delay_start_time, delay_end_time  from  cp_issue_rearrange where lid = \"".$data['type']."\";";
        $res = $this->slaveDc->query($sql)->row_array();
        return $res;
    }
    //彩种状态信息
    public function getIssueStatus($searchData)
    {
        $where = " 1";
        if(in_array($searchData['type'], array('syxw', 'jxsyxw', 'ks', 'jlks', 'jxks', 'hbsyxw', 'gdsyxw')))
        {
            $where .= " AND sale_time >= '{$searchData['sale_time']}' AND sale_time <= '{$searchData['end_time']}' AND delect_flag = 0";
        }
        $where .= ' AND issue = '.$searchData['issue'];
        $sql = "SELECT end_time, status, rstatus FROM cp_{$searchData['type']}_paiqi WHERE {$where} AND delect_flag = 0";
        $list = $this->slaveDc->query($sql)->getAll();
        return array($list);
    }

    //北京单场 彩果信息
    public function getIssueStatusbjdc($searchData)
    {
        $sql1 = "SELECT  MAX(begin_time) as end_time, sum(if(status < 50, 1, 0)) as status, sum(if(state = 1, 0, 1)) as rstatus
        FROM cp_{$searchData['type']}_paiqi AND mid = ".$searchData['issue'];

        $list = $this->slaveDc->query($sql1)->getAll();

        return array($list);
    }

    //北京单场 胜负过关
    public function getIssueStatussfgg($searchData)
    {
        $sql1 = "SELECT  MAX(begin_time) as end_time, sum(if(status < 50, 1, 0)) as status, sum(if(state = 1, 0, 1)) as rstatus
        FROM cp_{$searchData['type']}_paiqi AND  mid= ".$searchData['issue'];

        $list = $this->slaveDc->query($sql1)->getAll();

        return array($list);
    }

    //胜负彩/任选九 彩果信息
    public function getIssueStatussfc($searchData)
    {
        $sql = "SELECT t.end_sale_time as end_time, r.status as status, r.rstatus as rstatus FROM cp_tczq_paiqi AS t LEFT JOIN cp_rsfc_paiqi AS r ON t.mid = r.mid WHERE t.ctype = 1 AND t.mid = ".$searchData['issue'];

        $list = $this->slaveDc->query($sql)->getAll();

        return array($list);
    }

    //半全场 彩果信息
    public function getIssueStatusbqc($searchData)
    {
        $sql1 = "SELECT  t.end_sale_time as end_time, r.status as status, r.rstatus as rstatus FROM cp_tczq_paiqi AS t LEFT JOIN cp_rbqc_paiqi AS r ON t.mid = r.mid WHERE t.ctype = 2  AND t.mid =  ".$searchData['issue'] ;

        $list = $this->slaveDc->query($sql1)->getAll();

        return array($list);
    }

    //进球彩 彩果信息
    public function getIssueStatusjqc($searchData)
    {
        $sql1 = "SELECT t.end_sale_time as end_time, r.status as status, r.rstatus as rstatus FROM cp_tczq_paiqi AS t LEFT JOIN cp_rjqc_paiqi AS r ON t.mid = r.mid WHERE t.ctype = 3 AND t.mid = ".$searchData['issue'];
        $list = $this->slaveDc->query($sql1)->getAll();
        return array($list);
    }

    //查询彩种期次信息
    public function getIssueList_number($searchData, $page, $pageCount)
    {
        if(empty($searchData['type']))
        {
            $searchData['type'] = 'ssq';
        }
        $where = " 1";
        if(in_array($searchData['type'], array('syxw', 'jxsyxw', 'ks', 'jlks', 'jxks', 'hbsyxw', 'klpk', 'cqssc', 'gdsyxw')))
        {
            $where .= " AND sale_time >= '{$searchData['start_time']}' AND sale_time <= '{$searchData['end_time']}' AND delect_flag = 0";
        }else if($searchData['aduitflag']!=''){
            $where .= " AND aduitflag='{$searchData['aduitflag']}'";//加入审核查询
        }
        $sql1 = "SELECT id, issue, sale_time, end_time, award_time, awardNum, sale, pool, bonusDetail, status, rstatus,aduitflag FROM cp_{$searchData['type']}_paiqi WHERE {$where} AND delect_flag = 0 ORDER BY issue DESC LIMIT ". ($page - 1) * $pageCount . "," . $pageCount;
        $list = $this->slaveDc->query($sql1)->getAll();

        $sql2 = "SELECT count(*) FROM cp_{$searchData['type']}_paiqi WHERE {$where} AND delect_flag = 0";
        $count = $this->slaveDc->query($sql2)->getCol();

        return array($list,$count[0]);
    }
    //北京单场 彩果信息
    public function getIssueList_bjdc($searchData, $page, $pageCount)
    {
        $sql1 = "SELECT mid as issue, MIN(begin_time) as sale_time, MAX(begin_time) as end_time, sum(if(status < 50, 1, 0)) as status, sum(if(state = 1, 0, 1)) as rstatus
        FROM cp_{$searchData['type']}_paiqi GROUP BY mid ORDER BY mid DESC LIMIT ". ($page - 1) * $pageCount . "," . $pageCount;

        $list = $this->slaveDc->query($sql1)->getAll(); 

        $sql2 = "SELECT count(*) from ( SELECT mid, MIN(begin_time), MAX(begin_time) FROM cp_{$searchData['type']}_paiqi GROUP BY mid) a;";

        $count = $this->slaveDc->query($sql2)->getCol();

        return array($list,$count[0]);
    }

    //北京单场 胜负过关
    public function getIssueList_sfgg($searchData, $page, $pageCount)
    {
        $sql1 = "SELECT mid as issue, MIN(begin_time) as sale_time, MAX(begin_time) as end_time, sum(if(status < 50, 1, 0)) as status, sum(if(state = 1, 0, 1)) as rstatus
        FROM cp_{$searchData['type']}_paiqi GROUP BY mid ORDER BY mid DESC LIMIT ". ($page - 1) * $pageCount . "," . $pageCount;

        $list = $this->slaveDc->query($sql1)->getAll(); 

        $sql2 = "SELECT count(*) from ( SELECT mid, MIN(begin_time), MAX(begin_time) FROM cp_{$searchData['type']}_paiqi GROUP BY mid) a;";

        $count = $this->slaveDc->query($sql2)->getCol();

        return array($list,$count[0]);
    }

    //胜负彩/任选九 彩果信息
    public function getIssueList_sfc($searchData, $page, $pageCount)
    {
        $sql1 = "SELECT t.mid as issue, t.start_sale_time as sale_time, t.end_sale_time as end_time, r.result as awardNum, r.rj_sale as rj_sale, r.sfc_sale as sfc_sale, r.award as pool, r.award_detail as bonusDetail, r.status as status, r.rstatus as rstatus FROM cp_tczq_paiqi AS t LEFT JOIN cp_rsfc_paiqi AS r ON t.mid = r.mid WHERE t.ctype = 1 GROUP BY t.mid ORDER BY t.mid DESC LIMIT ". ($page - 1) * $pageCount . "," . $pageCount;

        $list = $this->slaveDc->query($sql1)->getAll(); 

        $sql2 = "SELECT count(*) from ( SELECT mid FROM cp_tczq_paiqi WHERE ctype = 1 GROUP BY mid) a;";

        $count = $this->slaveDc->query($sql2)->getCol();

        return array($list,$count[0]);
    }

    //半全场 彩果信息
    public function getIssueList_bqc($searchData, $page, $pageCount)
    {
        $sql1 = "SELECT t.mid as issue, t.start_sale_time as sale_time, t.end_sale_time as end_time, r.result as awardNum, r.sale, r.award as pool, r.award_detail as bonusDetail, r.status as status, r.rstatus as rstatus FROM cp_tczq_paiqi AS t LEFT JOIN cp_rbqc_paiqi AS r ON t.mid = r.mid WHERE t.ctype = 2 GROUP BY t.mid ORDER BY t.mid DESC LIMIT ". ($page - 1) * $pageCount . "," . $pageCount;

        $list = $this->slaveDc->query($sql1)->getAll(); 

        $sql2 = "SELECT count(*) from ( SELECT mid FROM cp_tczq_paiqi WHERE ctype = 2 GROUP BY mid) a;";

        $count = $this->slaveDc->query($sql2)->getCol();

        return array($list,$count[0]);
    }

    //进球彩 彩果信息
    public function getIssueList_jqc($searchData, $page, $pageCount)
    {
        $sql1 = "SELECT t.mid as issue, t.start_sale_time as sale_time, t.end_sale_time as end_time, r.result as awardNum, r.sale, r.award as pool, r.award_detail as bonusDetail, r.status as status, r.rstatus as rstatus FROM cp_tczq_paiqi AS t LEFT JOIN cp_rjqc_paiqi AS r ON t.mid = r.mid WHERE t.ctype = 3 GROUP BY t.mid ORDER BY t.mid DESC LIMIT ". ($page - 1) * $pageCount . "," . $pageCount;

        $list = $this->slaveDc->query($sql1)->getAll(); 

        $sql2 = "SELECT count(*) from ( SELECT mid FROM cp_tczq_paiqi WHERE ctype = 3 GROUP BY mid) a;";

        $count = $this->slaveDc->query($sql2)->getCol();

        return array($list,$count[0]);
    }

    //查询开奖详情
    public function getDetailList($data)
    {
        $list = array();
        if(!empty($data))
        {
            $sql = "SELECT id, issue, sale_time, end_time, award_time, awardNum, sale, pool, bonusDetail, status FROM cp_{$data['lid']}_paiqi WHERE issue = {$data['issue']} AND delect_flag = 0;";
            $list = $this->slaveDc->query($sql)->getRow();
        }
        return $list;
    }

    //查询开奖详情 胜负彩
    public function getDetailList_sfc($data)
    {
        $list = array();
        if(!empty($data))
        {
            $sql = "SELECT mid as issue, result as awardNum, rj_sale, sfc_sale, award as pool, award_detail as bonusDetail FROM cp_rsfc_paiqi WHERE mid = {$data['issue']};";
            $list = $this->slaveDc->query($sql)->getRow(); 
        }
        return $list;
    }

    //更新开奖详情 胜负彩
    public function updateDetailList_sfc($data)
    {
        $fields = array(
            'mid' => $data['issue'],
            'result' => $data['awardNum'],
            'rj_sale' => $data['rj_sale'],
            'sfc_sale' => $data['sfc_sale'],
            'award' => $data['pool'],
            'award_detail' => $data['bonusDetail'],
            'state' => 1,
            'status' => 50,
            'rstatus' => 50,
            'rjstatus' => 50,
            'rjrstatus' => 50,
            'd_synflag' => $data['d_synflag']
        );
        $this->dbDc->query("insert ignore cp_rsfc_paiqi(mid)values('{$data['issue']}')");
        if($data['d_synflag'] === 0)
        {
            $sql = "UPDATE cp_rsfc_paiqi SET result = ?, rj_sale = ?, sfc_sale = ?, award = ?, award_detail = ?, state = 1, status = 50, rstatus = 50, rjstatus = 50, rjrstatus = 50, d_synflag = ? WHERE mid = ?;";
            $res = $this->dbDc->query($sql,array($data['awardNum'],$data['rj_sale'],$data['sfc_sale'],$data['pool'],$data['bonusDetail'],$data['d_synflag'],$data['issue']));
        }
        else
        {
            $sql = "UPDATE cp_rsfc_paiqi SET result = ?, rj_sale = ?, sfc_sale = ?, award = ?, award_detail = ?, state = 1, status = 50, rstatus = 50, rjstatus = 50, rjrstatus = 50 WHERE mid = ?;";
            $res = $this->dbDc->query($sql,array($data['awardNum'],$data['rj_sale'],$data['sfc_sale'],$data['pool'],$data['bonusDetail'],$data['issue']));
        }
        //抓取表设置为已比对
        if($res)
        {
        	$this->dbDc->query("update cp_rsfc_score set state=1, rstate=1 where mid=?", array($data['issue']));
        }
        //启动任务
        $this->updateTicketStop(1, 11, 0);
        return $res;
    }

    //查询开奖详情 半全场
    public function getDetailList_bqc($data)
    {
        $list = array();
        if(!empty($data))
        {
            $sql = "SELECT mid as issue, result as awardNum, sale, award as pool, award_detail as bonusDetail FROM cp_rbqc_paiqi WHERE mid = {$data['issue']};";
            $list = $this->slaveDc->query($sql)->getRow(); 
        }
        return $list;
    }

    //更新开奖详情 半全场
    public function updateDetailList_bqc($data)
    {
        $fields = array(
            'mid' => $data['issue'],
            'result' => $data['awardNum'],
            'sale' => $data['sale'],
            'award' => $data['pool'],
            'award_detail' => $data['bonusDetail'],
            'state' => 1,
            'status' => 50,
            'rstatus' => 50,
            'd_synflag' => $data['d_synflag']
        );
        if($data['d_synflag'] === 0)
        {
            $sql = "UPDATE cp_rbqc_paiqi SET result = ?, sale = ?, award = ?, award_detail = ?, state = 1, status = 50, rstatus = 50, d_synflag = ? WHERE mid = ?;";
            $res = $this->dbDc->query($sql,array($data['awardNum'],$data['sale'],$data['pool'],$data['bonusDetail'],$data['d_synflag'],$data['issue']));
        }
        else
        {
            $sql = "UPDATE cp_rbqc_paiqi SET result = ?, sale = ?, award = ?, award_detail = ?, state = 1, status = 50, rstatus = 50 WHERE mid = ?;";
            $res = $this->dbDc->query($sql,array($data['awardNum'],$data['sale'],$data['pool'],$data['bonusDetail'],$data['issue']));

        }
        //抓取表设置为已比对
        if($res)
        {
        	$this->dbDc->query("update cp_rbqc_score set state=1, rstate=1 where mid=?", array($data['issue']));
        }
        // $sql = "UPDATE cp_rbqc_paiqi SET result = ?, sale = ?, award = ?, award_detail = ?, state = 1, status = 50, rstatus = 50 WHERE mid = ?;";
        // $res = $this->dbDc->query($sql,array($data['awardNum'],$data['sale'],$data['pool'],$data['bonusDetail'],$data['issue']));
        //启动任务
        $this->updateTicketStop(1, 16, 0);
        return $res;
    }

    //查询开奖详情 进球彩
    public function getDetailList_jqc($data)
    {
        $list = array();
        if(!empty($data))
        {
            $sql = "SELECT mid as issue, result as awardNum, sale, award as pool, award_detail as bonusDetail FROM cp_rjqc_paiqi WHERE mid = {$data['issue']};";
            $list = $this->slaveDc->query($sql)->getRow(); 
        }
        return $list;
    }

    //更新开奖详情 进球彩
    public function updateDetailList_jqc($data)
    {
        $fields = array(
            'mid' => $data['issue'],
            'result' => $data['awardNum'],
            'sale' => $data['sale'],
            'award' => $data['pool'],
            'award_detail' => $data['bonusDetail'],
            'state' => 1,
            'status' => 50,
            'rstatus' => 50,
            'd_synflag' => $data['d_synflag']
        );
        if($data['d_synflag'] === 0)
        {
            $sql = "UPDATE cp_rjqc_paiqi SET result = ?, sale = ?, award = ?, award_detail = ?, state = 1, status = 50, rstatus = 50, d_synflag = ? WHERE mid = ?;";
            $res = $this->dbDc->query($sql,array($data['awardNum'],$data['sale'],$data['pool'],$data['bonusDetail'],$data['d_synflag'],$data['issue']));

        }
        else
        {
            $sql = "UPDATE cp_rjqc_paiqi SET result = ?, sale = ?, award = ?, award_detail = ?, state = 1, status = 50, rstatus = 50 WHERE mid = ?;";
            $res = $this->dbDc->query($sql,array($data['awardNum'],$data['sale'],$data['pool'],$data['bonusDetail'],$data['issue']));
        }
        //抓取表设置为已比对
        if($res)
        {
        	$this->dbDc->query("update cp_rjqc_score set state=1, rstate=1 where mid=?", array($data['issue']));
        }
        // $sql = "UPDATE cp_rjqc_paiqi SET result = ?, sale = ?, award = ?, award_detail = ?, state = 1, status = 50, rstatus = 50 WHERE mid = ?;";
        // $res = $this->dbDc->query($sql,array($data['awardNum'],$data['sale'],$data['pool'],$data['bonusDetail'],$data['issue']));
        //启动任务
        $this->updateTicketStop(1, 18, 0);
        return $res;
    }

    public function saleStart($issues)
    {
    	$sql = "update cp_syxw_paiqi set status = 1 where status < 2 and issue in ('" . implode("','", $issues) . "')";
    	$result = $this->dbDc->query($sql);
    	//启动任务
    	$this->updateTicketStop(1, 21406, 0);
    	return $result;
    }
    
	public function issueDelete($issues, $type = 'syxw')
    {
    	$sql = "update cp_{$type}_paiqi set delect_flag = 1, d_synflag = 0 where delect_flag < 1 and status < 50 and rstatus < 50 and issue in ('" . implode("','", $issues) . "')";
    	$result = $this->dbDc->query($sql);
    	$arr = array(
    		'syxw' => 21406,
    		'jxsyxw' => 21407,
    		'hbsyxw' => 21408,
            'ks' => 53,
            'jlks' => 56,
    	    'jxks' => 57,
            'klpk' => 54,
            'cqssc' => 55,
    	    'gdsyxw' => 21421,
    	);
    	$this->updateTicketStop(1, $arr[$type], 0);
    	return $result;
    }
    
	public function bjdcStart($issue)
    {
    	$sql = "update cp_bjdc_paiqi set status = 1 where status < 1 and mid = ?";
    	return $this->dbDc->query($sql, array($issue));
    }

    public function sfcStart($issue)
    {
        $sql = "update cp_tczq_paiqi set status = 1 where status < 1 and mid = ? and ctype = 1";
        return $this->dbDc->query($sql, array($issue));
    }

    //获取对比异常的数据
    public function getCompareDetail($data)
    {
        $sql = "SELECT a.lid, a.issue, a.awardNum, a.time, a.sale, a.pool, a.bonusDetail, a.status, a.state, a.source, s.lname FROM cp_number_award as a LEFT JOIN cp_cron_score as s ON a.lid = s.ctype AND a.source = s.source WHERE a.lid = ? AND a.issue = ?;";
        $detail = $this->slaveDc->query($sql, array($data['type'],$data['issue']))->getAll();
        return $detail;
    }

    //获取对比异常的数据 体彩足球
    public function getCompareDetailByTczq($data)
    {
        $dbName = array(
            'sfc' => 'rsfc',
            'bqc' => 'rbqc',
            'jqc' => 'rjqc',
        );
        $db = $dbName[$data['type']];
        $where = "";
        $add = "";
        if($data['type'] == 'sfc')
        {
            $where = " p.lid = s.ctype AND";
            $add = " p.lid = '{$data['type']}' AND";
        }       
        $sql = "SELECT p.result as awardNum, s.lname FROM cp_{$db}_score as p LEFT JOIN cp_cron_score as s ON{$where} p.source = s.source WHERE{$add} p.mid = ? GROUP BY p.source;";
        $detail = $this->slaveDc->query($sql, array($data['issue']))->getAll();
        return $detail;
    }

    //获取期次管理的开始页面 数字彩
    public function pageMothed($searchData)
    {
        $where = " 1";
        if(in_array($searchData['type'], array('syxw', 'jxsyxw', 'ks', 'jlks', 'jxks', 'hbsyxw', 'klpk', 'cqssc', 'gdsyxw')))
        {
            $where .= " AND sale_time >= '{$searchData['start_time']}' AND sale_time <= '{$searchData['end_time']}'";
        }
        $sql = "SELECT count(*) FROM cp_{$searchData['type']}_paiqi WHERE {$where} AND award_time >= now() AND delect_flag = 0";
        $count = $this->slaveDc->query($sql)->getCol();
        return $count;
    }

    //期次管理的开始页面 北京单场
    public function pageMothed_bjdc($searchData)
    {
        $sql = "SELECT count(*) from ( SELECT mid, MIN(begin_time), MAX(begin_time) FROM cp_{$searchData['type']}_paiqi WHERE begin_time >= NOW() GROUP BY mid) a;";
        $count = $this->slaveDc->query($sql)->getCol();
        return $count;
    }

    //期次管理的开始页面 胜负过关
    public function pageMothed_sfgg($searchData)
    {
        $sql = "SELECT count(*) from ( SELECT mid, MIN(begin_time), MAX(begin_time) FROM cp_{$searchData['type']}_paiqi WHERE begin_time >= NOW() GROUP BY mid) a;";
        $count = $this->slaveDc->query($sql)->getCol();
        return $count;
    }

    //期次管理的开始页面 胜负彩
    public function pageMothed_sfc($searchData)
    {
        $sql = "SELECT count(*) from ( SELECT mid FROM cp_tczq_paiqi WHERE ctype = 1 AND start_sale_time >= NOW() GROUP BY mid) a;";
        $count = $this->slaveDc->query($sql)->getCol();
        return $count;
    }

    //期次管理的开始页面 半全场
    public function pageMothed_bqc($searchData)
    {
        $sql = "SELECT count(*) from ( SELECT mid FROM cp_tczq_paiqi WHERE ctype = 2 AND start_sale_time >= NOW() GROUP BY mid) a;";
        $count = $this->slaveDc->query($sql)->getCol();
        return $count;
    }

    //期次管理的开始页面 进球彩
    public function pageMothed_jqc($searchData)
    {
        $sql = "SELECT count(*) from ( SELECT mid FROM cp_tczq_paiqi WHERE ctype = 3 AND start_sale_time >= NOW() GROUP BY mid) a;";
        $count = $this->slaveDc->query($sql)->getCol();
        return $count;
    }

	/**
	 * 参    数：type
	 *           issue
	 *           awardnum 抓取开奖结果设置$bonusDetail
     *        updateAwardNum     
	 * 作    者：liuz
	 * 功    能：
	 * 修改日期：2015-12-08
	 */
	public function updateAwardNum($type, $issue, $awardnum)
	{
		$where = " 1";
		$querywhere = $where.$this->condition("issue", intval($issue));
        if($type =='syxw')
        {
            $bonusDetail = '{"qy":{"dzjj":"13"},"r2":{"dzjj":"6"},"r3":{"dzjj":"19"},"r4":{"dzjj":"78"},"r5":{"dzjj":"540"},"r6":{"dzjj":"90"},"r7":{"dzjj":"26"},"r8":{"dzjj":"9"},"q2zhix":{"dzjj":"130"},"q2zux":{"dzjj":"65"},"q3zhix":{"dzjj":"1170"},"q3zux":{"dzjj":"195"},"lx3":{"q3":{"dzjj":"1384"},"z3":{"dzjj":"214"},"r3":{"dzjj":"19"}},"lx4":{"r44":{"dzjj":"154"},"r43":{"dzjj":"19"}},"lx5":{"r55":{"dzjj":"1080"},"r54":{"dzjj":"90"}}}'; 
        }
        else
        {
           $bonusDetail = '{"qy":{"dzjj":"13"},"r2":{"dzjj":"6"},"r3":{"dzjj":"19"},"r4":{"dzjj":"78"},"r5":{"dzjj":"540"},"r6":{"dzjj":"90"},"r7":{"dzjj":"26"},"r8":{"dzjj":"9"},"q2zhix":{"dzjj":"130"},"q2zux":{"dzjj":"65"},"q3zhix":{"dzjj":"1170"},"q3zux":{"dzjj":"195"}}'; 
        }
		$sql = "update cp_".$type."_paiqi set awardnum = ? ,bonusDetail = ?, d_synflag = ?, status = ?, rstatus = ?, state = ? where ".$querywhere;
		$this->dbDc->query($sql, array($awardnum,$bonusDetail,0, 50, 50, 1));
		$arr = array(
			'syxw' => 21406,
			'jxsyxw' => 21407,
			'hbsyxw' => 21408,
		    'gdsyxw' => 21421,
		);
		$result = $this->updateTicketStop(1, $arr[$type], 0);
		return $result;
	}

	/**
	 * 参    数：type
	 *           issue
	 *           awardnum
	 * 作    者：liuz
	 * 功    能：
	 * 修改日期：2015-12-08
	 */
	public function updateKsAwardNum($type, $issue, $awardnum)
	{
		$where = " 1";
		$querywhere = $where.$this->condition("issue", $issue);
		$bonusDetail = '{"hz":{"z4":"80","z5":"40","z6":"25","z7":"16","z8":"12","z9":"10","z10":"9","z11":"9","z12":"10","z13":"12","z14":"16","z15":"25","z16":"40","z17":"80"},"sthtx":"40","sthdx":"240","sbth":"40","slhtx":"10","ethfx":"15","ethdx":"80","ebth":"8"}';
		$sql = "update cp_".$type."_paiqi set awardnum = ? ,bonusDetail = ?, d_synflag = ?, status = ?, rstatus = ?, state = ? where ".$querywhere;
		$this->dbDc->query($sql, array($awardnum,$bonusDetail,0, 50, 50, 1));
		//启动同步号码任务
                $arr = array(
             'jxks' => 57,
			'jlks' => 56,
			'ks' => 53
		);
		$result = $this->updateTicketStop(1, $arr[$type], 0);
		return $result;
	}

    // 更新快乐扑克开奖信息
    public function updateKlpkAwardNum($type, $issue, $awardnum)
    {
        $where = " 1";
        $querywhere = $where.$this->condition("issue", $issue);
        $bonusDetail = '{"thbx":{"dzjj":"22"},"thdx":{"dzjj":"90"},"thsbx":{"dzjj":"535"},"thsdx":{"dzjj":"2150"},"szbx":{"dzjj":"33"},"szdx":{"dzjj":"400"},"bzbx":{"dzjj":"500"},"bzdx":{"dzjj":"6400"},"dzbx":{"dzjj":"7"},"dzdx":{"dzjj":"88"},"r1":{"dzjj":"5"},"r2":{"dzjj":"33"},"r3":{"dzjj":"116"},"r4":{"dzjj":"46"},"r5":{"dzjj":"22"},"r6":{"dzjj":"12"}}';
        $sql = "update cp_".$type."_paiqi set awardnum = ? ,bonusDetail = ?, d_synflag = ?, status = ?, rstatus = ?, state = ? where ".$querywhere;
        $this->dbDc->query($sql, array($awardnum,$bonusDetail,0, 50, 50, 1));
        //启动同步号码任务
        $result = $this->updateTicketStop(1, 54, 0);
        return $result;
    }

    // 更新老时时彩开奖信息
    public function updateCqsscAwardNum($type, $issue, $awardnum)
    {
        $where = " 1";
        $querywhere = $where.$this->condition("issue", $issue);
        $bonusDetail = '{"1xzhix":"10","2xzhix":"100","2xzux":"50","3xzhix":"1000","3xzu3":"320","3xzu6":"160","5xzhix":"100000","5xtx":{"qw":"20440","3w":"220","2w":"20"},"dxds":"4"}';
        $sql = "update cp_".$type."_paiqi set awardnum = ? ,bonusDetail = ?, d_synflag = ?, status = ?, rstatus = ?, state = ? where ".$querywhere;
        $this->dbDc->query($sql, array($awardnum,$bonusDetail,0, 50, 50, 1));
        //启动同步号码任务
        $result = $this->updateTicketStop(1, 55, 0);
        return $result;
    }
    
	/**
	 * 根据类型和彩种id更新任务状态
	 * @param int $type
	 * @param int $lid
	 * @param int $stop
	 */
	public function updateTicketStop($type, $lid, $stop)
	{
		$this->cfgDB->query("update cp_task_manage set stop= ? where task_type= ? and lid= ?", array($stop, $type, $lid));
		return $this->cfgDB->affected_rows();
	}

     /**
     * [aduitIssue 开奖号码审核]
     * @author LiKangJian 2017-09-22
     * @param  [type] $issue    [description]
     * @param  [type] $type     [description]
     * @param  [type] $awardnum [description]
     * @return [type]           [description]
     */
    public function aduitIssue($issue,$type,$awardnum)
    {
        //必须是未审核的 中奖详情
        $sql = "SELECT id,aduitflag,bonusDetail FROM cp_".$type."_paiqi WHERE issue= ? and awardNum = ?";
        $res = $this->dbDc->query($sql, array($issue,trim($awardnum)))->getRow();
        if(isset($res['id']) && !empty($res['id']))
        {
            if($res['aduitflag'] ==1){ return 2;}
            $bonusDetail = $this->getBonusDetailBytype($type);
            $upSql = "UPDATE cp_".$type."_paiqi set aduitflag = ?,bonusDetail = ?,status=50,rstatus = 0,d_synflag = 0 WHERE issue = ? and awardNum = ?;";
            //更新
            $tag = $this->dbDc->query($upSql, array(1,$bonusDetail,$issue,trim($awardnum)) );
            //触发任务
            if($tag) $this->triggerSync($type);
            return $tag;
        }else{
           return false; 
        }

    }
    /**
     * [getBonusDetailBytype 根据类型构建BonusDetailBytype]
     * @author LiKangJian 2017-09-26
     * @param  [type] $type [description]
     * @return [type]       [description]
     */
    private function getBonusDetailBytype($type)
    {
        
        switch ($type)
        {
            case 'ssq':
                $bonusDetail = '{"1dj":{"zs":"--","dzjj":"--"},"2dj":{"zs":"--","dzjj":"--"},"3dj":{"zs":"--","dzjj":"3000"},"4dj":{"zs":"--","dzjj":"200"},"5dj":{"zs":"--","dzjj":"10"},"6dj":{"zs":"--","dzjj":"5"}}';
                break;  
            case 'dlt':
                $bonusDetail = '{"1dj":{"jb":{"zs":"--","dzjj":"--"},"zj":{"zs":"--","dzjj":"--"}},"2dj":{"jb":{"zs":"--","dzjj":"--"},"zj":{"zs":"--","dzjj":"--"}},"3dj":{"jb":{"zs":"--","dzjj":"--"},"zj":{"zs":"--","dzjj":"--"}},"4dj":{"jb":{"zs":"--","dzjj":"200"},"zj":{"zs":"--","dzjj":"100"}},"5dj":{"jb":{"zs":"--","dzjj":"10"},"zj":{"zs":"--","dzjj":"5"}},"6dj":{"jb":{"zs":"--","dzjj":"5"}}}';
                break;
            case 'qxc':
                $bonusDetail = '{"1dj":{"zs":"--","dzjj":"--"},"2dj":{"zs":"--","dzjj":"--"},"3dj":{"zs":"--","dzjj":"1800"},"4dj":{"zs":"--","dzjj":"300"},"5dj":{"zs":"--","dzjj":"20"},"6dj":{"zs":"--","dzjj":"5"}}';
                break;
            case 'qlc':
                $bonusDetail = '{"1dj":{"zs":"--","dzjj":"--"},"2dj":{"zs":"--","dzjj":"--"},"3dj":{"zs":"--","dzjj":"--"},"4dj":{"zs":"--","dzjj":"200"},"5dj":{"zs":"--","dzjj":"50"},"6dj":{"zs":"--","dzjj":"10"},"7dj":{"zs":"--","dzjj":"5"}}';
                break;
            case 'fc3d':
            case 'pl3':
                $bonusDetail = '{"zx":{"zs":"--","dzjj":"1040"},"z3":{"zs":"--","dzjj":"346"},"z6":{"zs":"--","dzjj":"173"}}';
                break;
            case 'pl5':
                $bonusDetail = '{"zx":{"zs":"--","dzjj":"100000"}}';
                break;
            default:
                $bonusDetail = '';
                break;
        }

        return $bonusDetail;
    }
    /**
     * [insertAwardNum 录入开奖号码]
     * @author LiKangJian 2017-09-25
     * @param  [type] $issue    [description]
     * @param  [type] $type     [description]
     * @param  [type] $awardNum [description]
     * @return [type]           [description]
     */
    public function insertAwardNum($issue,$type,$awardNum)
    {
        $sql = "UPDATE cp_{$type}_paiqi SET awardNum = ?,status = 50,aduitflag = 0, d_synflag = 0  WHERE issue = ?;";
        $tag = $this->dbDc->query($sql,array($awardNum,$issue));
        //触发同步任务
        if($tag) $this->triggerSync($type);
        return $tag;
    }
    /**
     * [checkHasAwardNum 验证是否有开奖号码]
     * @author LiKangJian 2017-09-25
     * @param  [type] $issue [description]
     * @param  [type] $type  [description]
     * @return [type]        [description]
     */
    public function checkHasAwardNum($issue,$type)
    {
        $sql = "SELECT awardNum FROM cp_{$type}_paiqi WHERE issue = ?";
        $row = $this->slaveDc->query($sql,array($issue))->getRow();
        if(isset($row['awardNum']) && !empty($row['awardNum']))
        {
            return false;
        }
        return true;
    }
    /**
     * [checkIsAduit 验证是否有已经审核]
     * @author LiKangJian 2017-09-25
     * @param  [type] $issue [description]
     * @param  [type] $type  [description]
     * @return [type]        [description]
     */
    public function checkIsAduit($issue,$type)
    {
        $sql = "SELECT aduitflag FROM cp_{$type}_paiqi WHERE issue = ?";
        $row = $this->slaveDc->query($sql,array($issue))->getRow();
        if(isset($row['aduitflag']) && $row['aduitflag']==0)
        {
            return false;
        }
        return true;
    }
    /**
     * [updateDetailList 更新开奖详情]
     * @author LiKangJian 2017-09-25
     * @param  [type] $data [description]
     * @return [type]       [description]
     */
    public function updateDetailList($data)
    {
        $aduitflag = 1 ;
        if($this->checkIsAduit($data['issue'],$data['type'])===false)
        {
            $aduitflag = 2;//系统审核
        }
        //不修改中奖号码 
        if($data['d_synflag'] === 0)
        {
            $sql = "UPDATE cp_{$data['type']}_paiqi SET awardNum = ?, sale = ?, pool = ?, bonusDetail = ?, status = 50, rstatus = 50, aduitflag = {$aduitflag},d_synflag = ? WHERE issue = ?;";
            $res = $this->dbDc->query($sql,array($data['awardNum'],$data['sale'],$data['pool'],$data['bonusDetail'],$data['d_synflag'],$data['issue']));
        }
        else
        {
            $sql = "UPDATE cp_{$data['type']}_paiqi SET awardNum = ?, sale = ?, pool = ?, bonusDetail = ?, d_synflag=0,status = 50, rstatus = 50 ,aduitflag = {$aduitflag} WHERE issue = ?;";
            $res = $this->dbDc->query($sql,array($data['awardNum'],$data['sale'],$data['pool'],$data['bonusDetail'],$data['issue']));
        }
        //将数字彩开奖信息表比对状态设置为已比对
        if($res)
        {
            $this->dbDc->query("update cp_number_award set state=1, rstate=1 where lid=? and issue=?", array($data['type'], $data['issue']));
        }
        
        //触发任务
        return $this->triggerSync($data['type']);
    }
    /**
     * [triggerSys description]
     * @author LiKangJian 2017-09-27
     * @return [type] [description]
     */
    private function triggerSync($type)
    {
        $arr = array(
            'ssq' => 51,
            'dlt' => 23529,
            'qxc' => 10022,
            'qlc' => 23528,
            'fc3d' => 52,
            'pl3' => 33,
            'pl5' => 35,
        );
        return $this->updateTicketStop(1, $arr[$type], 0);
    }

}
