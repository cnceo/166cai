<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/*
 * 数据中心 -- 模型层
 * @author:liuli
 * @date:2015-02-28
 */

class Compare_Model extends MY_Model
{

	private $CI;
	public $status_map = array(
		'compare_fail' => '2',
		'compare_succ' => '50',
	    'compare_delay'=> '51'
	);
	private $tables_map = array(
		'bjdc' => array('tbname' => 'cp_bjdc_score', 'dtbname' => 'cp_bjdc_paiqi', 'addfun' => 'updateBjdcOdds', 'cuk' => array('mid' => 'mid', 'mname' => 'mname'), 'lid' => 41),
		'sfgg' => array('tbname' => 'cp_sfgg_score', 'dtbname' => 'cp_sfgg_paiqi', 'addfun' => 'updateSfggOdds', 'cuk' => array('mid' => 'mid', 'mname' => 'mname'), 'lid' => 40),
		'jczq' => array('tbname' => 'cp_jczq_score', 'dtbname' => 'cp_jczq_paiqi', 'addfun' => 'updateJczqRq', 'cuk' => array('mid' => 'mid'), 'lid' => 42),
		'jclq' => array('tbname' => 'cp_jclq_score', 'dtbname' => 'cp_jclq_paiqi', 'addfun' => 'updateJclqRq', 'cuk' => array('mid' => 'mid'), 'lid' => 43),
		'jqc' => array(
			'tbname' => 'cp_jqc_score', 
			'dtbname' => 'cp_tczq_paiqi',
			'addfun' => '',
		    'condition' => '',
			'cuk' => array('mid' => 'mid', 'mname' => 'mname', 'ctype' => '3'),
			'lid' => 10,
		),
		'bqc' => array(
			'tbname' => 'cp_bqc_score',
			'dtbname' => 'cp_tczq_paiqi',
			'addfun' => '',
			'condition' => '',
			'cuk' => array('mid' => 'mid', 'mname' => 'mname', 'ctype' => '2'),
			'lid' => 10,
		),
		'sfc' => array(
			'tbname' => 'cp_sfc_score',
			'dtbname' => 'cp_tczq_paiqi',
			'addfun' => 'scoreWarning',
			'condition' => '',
			'cuk' => array('mid' => 'mid', 'mname' => 'mname', 'ctype' => '1'),
			'lid' => 10,
		),
		'rsfc' => array(
			'tbname' => 'cp_rsfc_score',
			'dtbname' => 'cp_rsfc_paiqi',
			'addfun' => 'add_fun',
			'condition' => '',
			'cuk' => array('mid' => 'mid'),
			'lid' => 11,
		),
		'dsfc' => array(
			'tbname' => 'cp_rsfc_score',
			'dtbname' => 'cp_rsfc_paiqi',
			'addfun' => 'awardResultWarning',
			'condition' => '',
			'status' => 'rstatus',
        	'state' => 'rstate',
			'cuk' => array('mid' => 'mid'),
			'lid' => 11,
		),
		'rbqc' => array(
			'tbname' => 'cp_rbqc_score',
			'dtbname' => 'cp_rbqc_paiqi',
			'addfun' => 'add_fun',
			'condition' => '',
			'cuk' => array('mid' => 'mid'),
			'lid' => 16,
		),
		'dbqc' => array(
			'tbname' => 'cp_rbqc_score',
			'dtbname' => 'cp_rbqc_paiqi',
			'addfun' => '',
			'condition' => '',
			'status' => 'rstatus',
        	'state' => 'rstate',
			'cuk' => array('mid' => 'mid'),
			'lid' => 16,
		),
		'rjqc' => array(
			'tbname' => 'cp_rjqc_score',
			'dtbname' => 'cp_rjqc_paiqi',
			'addfun' => 'add_fun',
			'condition' => '',
			'cuk' => array('mid' => 'mid'),
			'lid' => 18,
		),
		'djqc' => array(
			'tbname' => 'cp_rjqc_score',
			'dtbname' => 'cp_rjqc_paiqi',
			'addfun' => '',
			'condition' => '',
			'status' => 'rstatus',
        	'state' => 'rstate',
			'cuk' => array('mid' => 'mid'),
			'lid' => 18,
		),
        'ssq' => array(
            'tbname' => 'cp_number_award', 
            'dtbname' => 'cp_ssq_paiqi',
            'addfun' => 'awardNumberWarning',
            'condition' => "and lid = 'ssq'",
            'cuk' => array('issue' => 'issue'),
        	'lid' => 51,
        ),
        'dlt' => array(
            'tbname' => 'cp_number_award', 
            'dtbname' => 'cp_dlt_paiqi',
            'addfun' => 'awardNumberWarning',
            'condition' => "and lid = 'dlt'",
            'cuk' => array('issue' => 'issue'),
        	'lid' => 23529,
        ),
        'qlc' => array(
            'tbname' => 'cp_number_award', 
            'dtbname' => 'cp_qlc_paiqi',
            'addfun' => 'awardNumberWarning',
            'condition' => "and lid = 'qlc'",
            'cuk' => array('issue' => 'issue'),
        	'lid' => 23528,
        ),
        'qxc' => array(
            'tbname' => 'cp_number_award', 
            'dtbname' => 'cp_qxc_paiqi',
            'addfun' => 'awardNumberWarning',
            'condition' => "and lid = 'qxc'",
            'cuk' => array('issue' => 'issue'),
        	'lid' => 10022,
        ),
        'fc3d' => array(
            'tbname' => 'cp_number_award', 
            'dtbname' => 'cp_fc3d_paiqi',
            'addfun' => 'awardNumberWarning',
            'condition' => "and lid = 'fc3d'",
            'cuk' => array('issue' => 'issue'),
        	'lid' => 52,
        ),
        'pl3' => array(
            'tbname' => 'cp_number_award', 
            'dtbname' => 'cp_pl3_paiqi',
            'addfun' => 'awardNumberWarning',
            'condition' => "and lid = 'pl3'",
            'cuk' => array('issue' => 'issue'),
        	'lid' => 33,
        ),
        'pl5' => array(
            'tbname' => 'cp_number_award', 
            'dtbname' => 'cp_pl5_paiqi',
            'addfun' => 'awardNumberWarning',
            'condition' => "and lid = 'pl5'",
            'cuk' => array('issue' => 'issue'),
        	'lid' => 35,
        ),
        'rssq' => array(
            'tbname' => 'cp_number_award', 
            'dtbname' => 'cp_ssq_paiqi',
            'addfun' => 'awardResultWarning',
            'condition' => "and lid = 'ssq'",
        	'status' => 'rstatus',
        	'state' => 'rstate',
            'cuk' => array('issue' => 'issue'),
        	'lid' => 51,
        ),
        'rdlt' => array(
            'tbname' => 'cp_number_award', 
            'dtbname' => 'cp_dlt_paiqi',
            'addfun' => 'awardResultWarning',
            'condition' => "and lid = 'dlt'",
        	'status' => 'rstatus',
        	'state' => 'rstate',
            'cuk' => array('issue' => 'issue'),
        	'lid' => 23529,
        ),
        'rqlc' => array(
            'tbname' => 'cp_number_award', 
            'dtbname' => 'cp_qlc_paiqi',
            'addfun' => 'awardResultWarning',
            'condition' => "and lid = 'qlc'",
        	'status' => 'rstatus',
        	'state' => 'rstate',
            'cuk' => array('issue' => 'issue'),
        	'lid' => 23528,
        ),
        'rqxc' => array(
            'tbname' => 'cp_number_award', 
            'dtbname' => 'cp_qxc_paiqi',
            'addfun' => 'awardResultWarning',
            'condition' => "and lid = 'qxc'",
        	'status' => 'rstatus',
        	'state' => 'rstate',
            'cuk' => array('issue' => 'issue'),
        	'lid' => 10022,
        ),
        'rfc3d' => array(
            'tbname' => 'cp_number_award', 
            'dtbname' => 'cp_fc3d_paiqi',
            'addfun' => 'awardResultWarning',
            'condition' => "and lid = 'fc3d'",
        	'status' => 'rstatus',
        	'state' => 'rstate',
            'cuk' => array('issue' => 'issue'),
        	'lid' => 52,
        ),
        'rpl3' => array(
            'tbname' => 'cp_number_award', 
            'dtbname' => 'cp_pl3_paiqi',
            'addfun' => 'awardResultWarning',
            'condition' => "and lid = 'pl3'",
        	'status' => 'rstatus',
        	'state' => 'rstate',
            'cuk' => array('issue' => 'issue'),
        	'lid' => 33,
        ),
        'rpl5' => array(
            'tbname' => 'cp_number_award', 
            'dtbname' => 'cp_pl5_paiqi',
            'addfun' => 'awardResultWarning',
            'condition' => "and lid = 'pl5'",
        	'status' => 'rstatus',
        	'state' => 'rstate',
            'cuk' => array('issue' => 'issue'),
        	'lid' => 35,
        ),
	);
    public function __construct() {
        parent::__construct();
        //连接 数据中心database
    }
    
    public function get_compare_list()
    {
    	$sql = 'SELECT ctype, group_concat(source) sources FROM `cp_cron_score` WHERE START =1 AND compare =1 group by ctype';
    	return $this->dc->query($sql)->getAll();
    }
    
    public function compare_data($ctype, $sources, $cfields, $ukeys)
    {
    	$state = empty($this->tables_map[$ctype]['state']) ? 'state' :  $this->tables_map[$ctype]['state'];
    	$status = empty($this->tables_map[$ctype]['status']) ? 'status' :  $this->tables_map[$ctype]['status'];
    	$check_sql = "select count(*) from {$this->tables_map[$ctype]['tbname']} where $status = 1 and $state = 0 and 
    	modified > date_sub(now(), interval 7 day) {$this->tables_map[$ctype]['condition']}";
    	$check_flag = $this->dc->query($check_sql)->getOne();
    	if($check_flag > 0)
    	{
	    	$ufname = array(
		    	'mid' => " char(15) not null default ''",
		    	'mname' => " char(50) not null default ''",
	    		'lid' => " char(20) not null default ''",
	            'issue' => " char(20) not null default ''",
	    	);
	    	$ufnames = array();
	    	$joincon = array();
	    	foreach ($ukeys as $ukey)
	    	{
				 array_push($ufnames, "$ukey {$ufname[$ukey]}");
				 array_push($joincon, "m.$ukey = n.$ukey");	
	    	}
	    	$tmp_table = "{$this->db_config['tmp']}.compare_{$ctype}_tmp";
	    	$csql = "create table if not exists $tmp_table(
	    		id int unsigned not null auto_increment primary key,
	    		:ufname:,
	    		:fname:
	    		status tinyint(2) not null default 0,
	    		del tinyint(2) not null default 0,
	    		unique key(:ukeys:)
	    	)engine = memory";
	    	$ssql = '';
	    	$fields = array();
	    	$sfields = array();
	    	$wheres = array();
	    	$dfields = array();
	    	
	    	foreach ($cfields as $cfield)
	    	{
	    		$conditions = array();
	    		foreach ($sources as $source)
	    		{
	    			$size = 100;
	    			if(!(stripos($cfield, 'detail') === false))
	    			{
	    				$size = 2000;
	    			}
	    			$ssql .= "$cfield$source varchar($size) not null default '', \n";
	    			array_push($fields, "$cfield$source");
	    			array_push($sfields, "if(source = $source, $cfield, '') $cfield$source");
	    			$dfields["$cfield{$sources[0]}"] = "$cfield{$sources[0]}";
	    			
	    			if("$cfield{$sources[0]}" != "$cfield$source")
	    				array_push($conditions, $this->tryMd5("$cfield{$sources[0]}") . " = " . $this->tryMd5("$cfield$source"));
	    			else 
	    				array_push($conditions, "$cfield{$sources[0]} != '' && $cfield{$sources[0]} is not null");
	    		}
	    		array_push($wheres, implode(' && ', $conditions));
	    	}
	    	$unkey = implode(',', $ukeys);
	    	$csql = str_replace(array(':ufname:', ':fname:', ':ukeys:'), array(implode(', ', $ufnames), $ssql, $unkey), $csql);
	    	$this->dc->query($csql);
	    	//插入原数据
	    	$isql = "insert $tmp_table($unkey, " . implode(',', $fields) . ") select $unkey, " . 
	    	implode(', ', $sfields) . " from {$this->tables_map[$ctype]['tbname']} where $status = 1 and $state = 0 and 
	    	modified > date_sub(now(), interval 7 day) {$this->tables_map[$ctype]['condition']} on duplicate key update " . $this->updatefields($fields);
	       	$this->dc->query($isql);
	       	$dtbname = empty($this->tables_map[$ctype]['dtbname']) ? "cp_{$ctype}_paiqi" : $this->tables_map[$ctype]['dtbname'];
	       	//排除已结期次操作
	       	$djoincon = array();
	       	$djoinStr = '';
	       	foreach ($this->tables_map[$ctype]['cuk'] as $cK => $ukey)
	       	{
	       		if($cK == 'ctype')
	       		{
	       			$djoinStr = " and n.ctype='{$ukey}'";
	       			continue;
	       		}
	       		array_push($djoincon, "m.$ukey = n.$ukey");
	       	}
	       	$delsql = "update $tmp_table m join $dtbname n on " . implode(' and ', $djoincon) . " $djoinStr set m.del = 1 
	       	where n.$status = '{$this->status_map['compare_succ']}'";
	       	$this->dc->query($delsql);
	       	//更新原表
	       	$delsql1 = "update {$this->tables_map[$ctype]['tbname']} m
	       	join $tmp_table n on " . implode(' && ', $joincon) .
	       	" set m.{$state} = 1
	       	where n.del = 1";
	       	$this->dc->query($delsql1);
	       	$this->dc->query("delete from $tmp_table where del = 1");
	    	//做比较
	    	$upsql = "update $tmp_table set status = 1 where 1 ";
	    	if(count($sources) >= 1)
	    	{
	    		$upsql .= " && " . implode(' && ', $wheres);
	    	}
	    	$this->dc->query($upsql);
	    	//更新原表
	    	$upstable = "update {$this->tables_map[$ctype]['tbname']} m 
	    	join $tmp_table n on " . implode(' && ', $joincon) . 
	    	" set m.{$state} = 1 
	    	where n.status = 1";
	    	$this->dc->query($upstable);
	    	//插入目标表
	    	array_push($cfields, $status);
	        //匹配异常处理
	        $dsql_error = "insert $dtbname(" . implode(',', array_keys($this->tables_map[$ctype]['cuk'])) . ", " . 
	        implode(', ', $cfields) . " ) select " . implode(',', array_values($this->tables_map[$ctype]['cuk'])) . ", " . 
	        implode(', ', $dfields) . ", {$this->status_map['compare_fail']} from $tmp_table where status = 0 " . $this->onduplicate($cfields, array($status), array('con' => array($status)), "$dtbname.");
	        $this->dc->query($dsql_error);
	        //设置同步字段
	        array_push($cfields, 'd_synflag');
	        //匹配成功处理
	    	//对rsfc 和 dsfc进行特殊处理
	    	if($dtbname == 'cp_rsfc_paiqi')
	    	{
	    		$rjstatus = ($status == 'rstatus') ? 'rjrstatus' : 'rjstatus';
	    		array_push($cfields, $rjstatus);
	    		$dsql = "insert $dtbname(" . implode(',', array_keys($this->tables_map[$ctype]['cuk'])) . ", " . 
		    	implode(', ', $cfields) . " ) select " . implode(',', array_values($this->tables_map[$ctype]['cuk'])) . ", " . 
		    	implode(', ', $dfields) . ", {$this->status_map['compare_succ']}, 0, {$this->status_map['compare_succ']}
		    	from $tmp_table where status = 1 " . $this->onduplicate($cfields, $cfields, array('con' => array($status)), "$dtbname.");
	    	}
	    	elseif (in_array($dtbname, array('cp_jczq_paiqi', 'cp_jclq_paiqi')) || in_array($ctype, array('rssq', 'rdlt', 'rqlc', 'rqxc', 'rfc3d', 'rpl3', 'rpl5')))
	    	{
	    		array_push($cfields, 'aduitflag');
	    		$dsql = "insert $dtbname(" . implode(',', array_keys($this->tables_map[$ctype]['cuk'])) . ", " .
	    		implode(', ', $cfields) . " ) select " . implode(',', array_values($this->tables_map[$ctype]['cuk'])) . ", " .
	    		implode(', ', $dfields) . ", {$this->status_map['compare_succ']}, 0, 2 from $tmp_table where status = 1 " . $this->onduplicate($cfields, $cfields, array('con' => array($status)), "$dtbname.");
	    	}
	    	else 
	    	{
		    	$dsql = "insert $dtbname(" . implode(',', array_keys($this->tables_map[$ctype]['cuk'])) . ", " . 
		    	implode(', ', $cfields) . " ) select " . implode(',', array_values($this->tables_map[$ctype]['cuk'])) . ", " . 
		    	implode(', ', $dfields) . ", {$this->status_map['compare_succ']}, 0 from $tmp_table where status = 1 " . $this->onduplicate($cfields, $cfields, array('con' => array($status)), "$dtbname.");
	    	}
	    	$this->dc->query($dsql);
	    	$affectedRows = $this->dc->affected_rows();
	    	
	    	if(!empty($this->tables_map[$ctype]['addfun']))
	    	{
	    		$addfun = $this->tables_map[$ctype]['addfun'];
	    		if(method_exists($this, $addfun))
	    			$this->$addfun($ctype);
	    	}
	    	//如果排期表有变动触发同步任务
	    	if($affectedRows > 0 && isset($this->tables_map[$ctype]['lid']))
	    	{
	    		$this->cfgDB = $this->load->database('cfg', TRUE);
	    		$this->cfgDB->query("update cp_task_manage set stop= 0 where task_type= 1 and lid= ?", array($this->tables_map[$ctype]['lid']));
	    	}
	    	$this->drop_table($tmp_table);
    	}
    }

    private function tryMd5($field)
    {
    	if($this->isDetail($field))
    	{
    		$field = "md5($field)";
    	}
    	return $field;
    }
    
    private function isDetail($field)
    {
    	return !(stripos($field, 'detail') === false);
    }

    private function add_fun($ctype)
    {
    	$map_para = array(
    	'rbqc' => array('span' => 2, 'ctype' => 2),
    	'rjqc' => array('span' => 2, 'ctype' => 3),
    	'rsfc' => array('span' => 1, 'ctype' => 1),
    	);
    	$dtname = $this->tables_map[$ctype]['dtbname'];
    	$sql = "select mid, result from $dtname where state = 0 and status = 50";
    	$results = $this->dc->query($sql)->getAll();
    	
    	if(!empty($results))
    	{
	    	foreach ($results as $result)
	    	{
	    		$s_data = array();
	    		$mids = array();
	    		$scores = $this->result($result['result'], $map_para[$ctype]['span']);
	    		if(!empty($scores))
	    		{
	    			array_push($mids, $result['mid']);
	    			foreach ($scores as $mname => $score)
	    			{
	    				array_push($s_data, "('{$result['mid']}', '$mname', '{$score[0]}', '{$score[1]}', {$map_para[$ctype]['ctype']}, 0)");
	    			}
	    			$sql = "insert cp_tczq_paiqi(mid, mname, result1, result2, ctype, d_synflag) values" .
	    					implode(',', $s_data) . " on duplicate key update result1 = values(result1), result2 = values(result2), d_synflag = values(d_synflag)";
	    			$this->dc->query($sql);
	    			//触发任务
	    			$this->cfgDB = $this->load->database('cfg', TRUE);
	    			$this->cfgDB->query("update cp_task_manage set stop= 0 where task_type= 1 and lid= 10");
	    		}
	    		
	    		if(!empty($mids))
	    		{
	    			$mids = implode(",", $mids);
	    			$this->dc->query("update $dtname set state = 1 where mid in ( {$mids} )");
	    		}
	    	}
    	}
    	
    	//开奖号码报警
    	$this->awardNumberWarning($ctype);
    }
    
    //更新竞彩足球最终让球数
    private function updateJczqRq($ctype)
    {
    	$sql = "SELECT mid, codes FROM cp_jczq_match WHERE mid IN (SELECT mid FROM {$this->db_config['tmp']}.compare_{$ctype}_tmp WHERE 1) AND ctype=2";
    	$results = $this->dc->query($sql)->getAll();
    	foreach ($results as $val)
    	{
    		$codes = unserialize($val['codes']);
    		$sql = "UPDATE cp_jczq_paiqi SET rq='{$codes['fixedodds']}', d_synflag = 0 WHERE mid={$val['mid']}";
    		$this->dc->query($sql);
    	}
    	
    	//足球比对异常报警操作
    	$this->scoreWarning($ctype);
    }
    
    //更新竞彩篮球最终让球数
    private function updateJclqRq($ctype)
    {
    	$sql = "SELECT mid, codes, ctype FROM cp_jclq_match WHERE mid IN (SELECT mid FROM {$this->db_config['tmp']}.compare_{$ctype}_tmp WHERE 1) AND ctype IN (2,4)";
    	$results = $this->dc->query($sql)->getAll();
    	foreach ($results as $val)
    	{
    		$codes = unserialize($val['codes']);
    		if($val['ctype'] == '2')
    		{
    			$sql = "UPDATE cp_jclq_paiqi SET rq='{$codes['fixedodds']}', d_synflag = 0 WHERE mid={$val['mid']}";
    		}
    		else
    		{
    			$sql = "UPDATE cp_jclq_paiqi SET preScore='{$codes['score']}', d_synflag = 0 WHERE mid={$val['mid']}";
    		}
    		
    		$this->dc->query($sql);
    	}
    	
    	//篮球比对异常报警操作
    	$this->scoreWarning($ctype);
    }
    
    private function result($str, $span)
    {
    	$results = explode(',', $str);
		$records = array();
		foreach ($results as $in => $result)
		{
			$mname = intval($in / $span);
			$records[1 + $mname][] = $result;
		}
		
		return $records;
    }
    
    private function updateBjdcOdds($ctype)
    {
    	$dtname = $this->tables_map[$ctype]['dtbname'];
    	$sql = "SELECT mid,mname FROM {$dtname} WHERE status={$this->status_map['compare_succ']} AND state=0 AND modified > date_sub(now(), interval 7 day)";
    	$results = $this->dc->query($sql)->getAll();
    	foreach ($results as $val)
    	{
    		$sql1 = "SELECT ctype,codes FROM cp_bjdc_match WHERE mid=? AND mname=? AND ctype!=1 AND status=3";
    		$res = $this->dc->query($sql1, array($val['mid'], $val['mname']))->getAll();
    		$data = array();
    		foreach ($res as $v)
    		{
    			$codes = unserialize($v['codes']);
    			$odds = $this->getOdds($codes);
    			switch ($v['ctype'])
    			{
    				case 2:
    					$data['spf_odds'] = $odds;
    					$data['rq'] = $codes['fixedodds'];
    					break;
    				case 3:
    					$data['jqs_odds'] = $odds;
    					break;
    				case 4:
    					$data['bqc_odds'] = $odds;
    					break;
    				case 5:
    					$data['dss_odds'] = $odds;
    					break;
    				case 6:
    					$data['dcbf_odds'] = $odds;
    					break;
    				case 7:
    					$data['xbcbf_odds'] = $odds;
    					break;
    			}
    			$data['state'] = (empty($data['spf_odds']) || empty($data['jqs_odds']) || empty($data['bqc_odds']) || empty($data['dss_odds']) || empty($data['dcbf_odds'])) ? 0 : 1;
    		}
    		if(!empty($data))
    		{
    			if($data['state'] == 1)
    			{
    				$data['d_synflag'] = 0;
    			}
    			$this->dc->where(array('mid' => $val['mid'], 'mname' => $val['mname']));
    			$this->dc->update($dtname, $data);
    		}
    			
    	}   	
    }
    
    private function updateSfggOdds($ctype)
    {
    	$dtname = $this->tables_map[$ctype]['dtbname'];
    	$sql = "SELECT mid,mname FROM {$dtname} WHERE status={$this->status_map['compare_succ']} AND state=0 AND modified > date_sub(now(), interval 7 day)";
    	$results = $this->dc->query($sql)->getAll();
    	foreach ($results as $val)
    	{
    		$sql1 = "SELECT codes FROM cp_bjdc_match WHERE mid=? AND mname=? AND ctype=1 AND status=3";
    		$res = $this->dc->query($sql1, array($val['mid'], $val['mname']))->getAll();
    		$data = array();
    		foreach ($res as $v)
    		{
    			$codes = unserialize($v['codes']);
    			$odds = $this->getOdds($codes);
    			$data['sfgg_odds'] = $odds;
    			$data['state'] = empty($odds) ? 0 : 1;
    			$data['rq'] = $codes['fixedodds'];
    		}
    		if(!empty($data))
    		{
    			if($data['state'] == 1)
    			{
    				$data['d_synflag'] = 0;
    			}
	    		$this->dc->where(array('mid' => $val['mid'], 'mname' => $val['mname']));
	    		$this->dc->update($dtname, $data);
    		}
    	}
    }
    
    private function getOdds($odds = array())
    {
    	$return = '';
    	if(isset($odds['fixedodds'])) unset($odds['fixedodds']);
    	foreach ($odds as $val)
    	{
    		if(!empty($val))
    		{
    			$return = abs($val);
    			break;
    		}
    	}
    	
    	return $return;
    }
    
    private function updatefields($fields)
    {
    	$upfields = array();
    	foreach ($fields as $field)
    	{
    		array_push($upfields, "$field = if(values($field) != '', values($field), $field)");
    	}
    	return implode(', ', $upfields);
    }
    
    public function drop_table($table)
    {
    	return $this->dc->query("drop table if exists $table");
    }
    
    /**
     * 比分比对异常报警
     * @param string $ctype 彩种类型
     */
    private function scoreWarning($ctype)
    {
    	$tmpTable = "{$this->db_config['tmp']}.compare_{$ctype}_tmp";
    	if(in_array($ctype, array('jczq', 'jclq')))
    	{
    		$titleName = array(
    			'jczq' => '竞彩足球',
    			'jclq' => '竞彩篮球',
    		);
    		
    		$sql = "insert ignore cp_alert_log(ctype, ufiled, title, content, created)
    		select 20 , CONCAT('{$this->tables_map[$ctype]['lid']}_', mid), '{$titleName[$ctype]}比分比对异常报警', CONCAT('{$titleName[$ctype]}场次', mid, '比分比对异常，请尽快人工审核'), NOW() from {$tmpTable}
    		where status = '0'";
    	}
    	else
    	{
    		$sql = "insert ignore cp_alert_log(ctype, ufiled, title, content, created)
    		select 20 , CONCAT('{$this->tables_map[$ctype]['lid']}_', mid), '胜负彩比分比对异常报警', CONCAT('胜负彩第', mid, '期场次', mname, '比分比对异常，请尽快人工处理'), NOW() from {$tmpTable}
    		where status = '0'";
    	}
    	
    	$this->db->query($sql);
    }
    
    /**
     * 开奖号码比对异常报警
     * @param unknown_type $ctype
     */
    private function awardNumberWarning($ctype)
    {
    	$tmpTable = "{$this->db_config['tmp']}.compare_{$ctype}_tmp";
    	$lidArr = array(51 => '双色球', 23529 => '大乐透', 52 => '福彩3D', 33 => '排列三', 35 => '排列五', 23528 => '七乐彩', 10022 => '七星彩', 11 => '胜负彩');
    	if(!in_array($this->tables_map[$ctype]['lid'], array_keys($lidArr)))
    	{
    		return ;
    	}
    	
    	if(in_array($this->tables_map[$ctype]['lid'], array('11')))
    	{
    		$sql = "insert ignore cp_alert_log(ctype, ufiled, title, content, created)
    		select 16 , CONCAT('{$this->tables_map[$ctype]['lid']}_', mid), '开奖号码比对异常报警', CONCAT('{$lidArr[$this->tables_map[$ctype]['lid']]}第', mid, '期开奖号码比对异常，请尽快处理'), NOW() from {$tmpTable}
    		where status = '0'";
    	}
    	else
    	{
    		$sql = "insert ignore cp_alert_log(ctype, ufiled, title, content, created)
    		select 16 , CONCAT('{$this->tables_map[$ctype]['lid']}_', issue), '开奖号码比对异常报警', CONCAT('{$lidArr[$this->tables_map[$ctype]['lid']]}第', issue, '期开奖号码比对异常，请尽快处理'), NOW() from {$tmpTable}
    		where status = '0'";
    	}
    	
    	$this->db->query($sql);
    }
    
    /**
     * 开奖详情比对异常报警
     * @param unknown_type $ctype
     */
    private function awardResultWarning($ctype)
    {
    	$tmpTable = "{$this->db_config['tmp']}.compare_{$ctype}_tmp";
    	$lidArr = array(51 => '双色球', 23529 => '大乐透', 52 => '福彩3D', 33 => '排列三', 35 => '排列五', 23528 => '七乐彩', 10022 => '七星彩', 11 => '胜负彩');
    	if(!in_array($this->tables_map[$ctype]['lid'], array_keys($lidArr)))
    	{
    		return ;
    	}
    	 
    	if(in_array($this->tables_map[$ctype]['lid'], array('11')))
    	{
    		$sql = "insert ignore cp_alert_log(ctype, ufiled, title, content, created)
    		select 17, CONCAT('{$this->tables_map[$ctype]['lid']}_', mid), '开奖详情比对异常报警', CONCAT('{$lidArr[$this->tables_map[$ctype]['lid']]}第', mid, '期开奖详情比对异常，请尽快处理'), NOW() from {$tmpTable}
    		where status = '0'";
    	}
    	else
    	{
    	$sql = "insert ignore cp_alert_log(ctype, ufiled, title, content, created)
    	select 17, CONCAT('{$this->tables_map[$ctype]['lid']}_', issue), '开奖详情比对异常报警', CONCAT('{$lidArr[$this->tables_map[$ctype]['lid']]}第', issue, '期开奖详情比对异常，请尽快处理'), NOW() from {$tmpTable}
    	where status = '0'";
    	}
    	 
    	$this->db->query($sql);
    }
}
