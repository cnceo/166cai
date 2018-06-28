<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class MY_Model extends CI_Model {

	const CODE_SUCCESS = 0;
	public $CI;
    public function __construct() {
        parent::__construct();
        $this->load->library('tools');
        
        $this->load->helper('string');
        $this->baseUrl = $this->config->item('base_url');
        $this->passApi = $this->config->item('pass_api');
        $this->payApi = $this->config->item('pay_api');
        $this->cmsApi = $this->config->item('cms_api');
        $this->payUrl = $this->config->item('pay_url');
        $this->fileUrl = $this->config->item('file_url');
        
        $this->load->config('scheme');
        $this->db_config = $this->config->item('db_config'); 
    }
	
	protected function maps($v)
	{
		return '?';
	}
	
	protected function onduplicate($fields, $upd, $apd=array(), $_ = '', $status = null)
	{
		$tail = array();
		if(empty($status))
		{ 
			$status = array('status');
		}
		foreach ($fields as $field)
		{
			if(in_array($field, $upd))
			{
				if(in_array($field, $apd))
				{
					array_push($tail, "$field = $_$field + values($field)");
				}else 
				{
					if(in_array($field, $status))
					{
						array_push($tail, "$field = if($_$field < values($field), values($field), $_$field)");
					}
					else 
					{
						array_push($tail, "$field = values($field)");
					}
				}
			}
		}
		if(!empty($tail))
			return " on duplicate key update " . implode(', ', $tail);
	}
	
	protected function getFields($table, $schema = null, $DB = null)
	{
		$schema = empty($schema) ? $this->db_config['cp'] : $schema;
		$sql = "SELECT column_name FROM information_schema.COLUMNS
		WHERE table_schema = '$schema'
		AND table_name = ?";
		if(!empty($DB)) 
		{
			$fields = $DB->query($sql, array($table))->getCol();
		}
		else 
		{
			$fields = $this->db->query($sql, array($table))->getCol();
		}
		if(in_array('id', $fields))
			$fields = array_slice($fields, 1);
		return $fields;
	}
	
	public function trans_start($DB = 'cfgDB')
    {
    	$this->$DB->trans_start();
    }
    
	public function trans_complete($DB = 'cfgDB')
    {
    	$this->$DB->trans_complete();
    }
    
	public function trans_rollback($DB = 'cfgDB')
    {
    	$this->$DB->trans_rollback();
    }
    
	public function orderConfig($cfg)
   	{
   		$this->load->config('order');
   		return $this->config->item("cfg_$cfg");
   	}
   	
   	public function getLidMap($rtype = false, $params = array())
   	{
   		if($rtype)
    	{
    		return $this->getByCron(array('model' => $params['model'], 'func' => $params['func'], 'params' => $params['params']));
    	}
    	else
    	{
	   		$this->load->driver('cache', array('adapter' => 'redis'));
	    	$REDIS = $this->config->item('REDIS');
	    	$datas = json_decode($this->cache->get($REDIS['LOTTERY_CONFIG']), true);
	    	$lids = array();
	    	foreach ($datas as $lid => $lottery)
	    	{
	    		$lids[$lid] = $lottery['lname'];
	    	}
	    	return $lids;
    	}
   	}
   	
	/**
     * 更新cp_orders_ori表状态
     * @param unknown_type $orderId
     * @param unknown_type $status
     */
    public function updateOrdersOriStatus($orderId, $status, $fields = array())
    {
    	$this->order_status = $this->orderConfig('orders');
    	$err_num = $fields['err_num'];
    	unset($fields['err_num']);
    	$set_err = 1;
    	if(in_array($err_num, $this->orderConfig('errnum')))
    	{
    		$set_err = 0;
    	}
    	$setVal = '';
    	foreach ($fields as $field => $val)
    	{
    		$setVal .= ", $field = '$val'";
    	}
    	$sql = "UPDATE cp_orders_ori SET status = if($status < status, status, 
    	if(($status != {$this->order_status['concel']} || $set_err = 1),
    	$status, if(endTime <= now(), $status, status))), synflag=(synflag << 1) $setVal WHERE orderId in('" . implode("','", $orderId) ."')";
    	return $this->cfgDB->query($sql, array($status));
    }
    
    public function ctrlRun($cname, $mname = 'index', $rtype = false, $params = array())
    {
    	if($rtype)
    	{
    		return $this->getByCron(array('model' => $params['model'], 'func' => $params['func'], 'params' => $params['params']));
    	}
    	else
    	{
	    	$this->getDB('cfgDB');
	    	$stop = $this->cfgDB->query("select stop from cp_cron_list where con = ? and act = ?", 
	    	array($cname, $mname))->getOne();
	    	$dieMemSize = $this->config->item('dieMemSize');
	    	$mem = intval(memory_get_usage(true) / (1024 * 1024));
	    	if($stop == 1 || $mem >= $dieMemSize)
	    	{
	    		return true;
	    	}
	    	else 
	    	{
	    		return false;
	    	}
    	}
    }
    
    public function getRunFlag($params = array())
    {
    	$croname = "cron/clii_cfg_clear_pid getDatas/{$params['model']}/{$params['func']}/{$params['params']}";
    	exec("{$this->php_path} {$this->cmd_path} $croname", $outPut, $status);
    	if(count($outPut) == 1)
    	{
    		return json_decode($outPut[0], true);
    	}
    }
    
	public function getByCron($params = array())
    {
    	$croname = "cron/clii_cfg_clear_pid getDatas/{$params['model']}/{$params['func']}/{$params['params']}";
    	exec("{$this->php_path} {$this->cmd_path} $croname", $outPut, $status);
    	if(count($outPut) == 1)
    	{
    		return json_decode($outPut[0], true);
    	}
    }
    
    public function threadWait(&$threads, $minute)
    {
    	$dead_line = time() + 60 * $minute;
	 	while(count($threads) > 0)
        {
        	foreach($threads as $pid => $metd)
            {
            	$rpid = pcntl_waitpid($pid, $status, WNOHANG);
                if($rpid > 0 || $rpid == -1)
                {
                	unset($threads[$pid]);
                }
            }
            sleep(1);
            if($dead_line < time())
            {
            	break;
            }
        }
    }
    
    protected function getSplitTable($lid)
    {
    	$splitlid = $this->config->item('split_lid');
    	$tables = array();
		if(in_array($lid, $splitlid))
    	{
    		$lidmap = $this->orderConfig('lidmap');
    		$tables['split_table'] = "cp_orders_split_{$lidmap[$lid]}";
    		$tables['relation_table'] = "cp_orders_relation_{$lidmap[$lid]}";
    	}
    	else 
    	{
    		$tables['split_table'] = "cp_orders_split";
    		$tables['relation_table'] = "cp_orders_relation";
    	}
    	return $tables;
    }
    
	protected function getDB($db)
    {
    	$dbmap = array('cfgDB' => 'cfg', 'dc' => 'dc', 'db' => 'default');
    	$this->$db = $this->load->database($dbmap[$db], true);	
    }
    
    //获得当前的期次的开售时间点
	protected function get_cissue_stime($lid, $issue)
    {
    	$lidMap = array(
			'51' => array('table' => 'cp_ssq_paiqi', 'issuePrefix' => ''),
			'52' => array('table' => 'cp_fc3d_paiqi', 'issuePrefix' => ''),
			'33' => array('table' => 'cp_pl3_paiqi', 'issuePrefix' => '20'),
			'35' => array('table' => 'cp_pl5_paiqi', 'issuePrefix' => '20'),
			'10022' => array('table' => 'cp_qxc_paiqi', 'issuePrefix' => '20'),
			'23528' => array('table' => 'cp_qlc_paiqi', 'issuePrefix' => ''),
			'23529' => array('table' => 'cp_dlt_paiqi', 'issuePrefix' => '20'),
			'11' => array('table' => 'cp_tczq_paiqi', 'issuePrefix' => '20'),
			'19' => array('table' => 'cp_tczq_paiqi', 'issuePrefix' => '20'),
			'21406' => array('table' => 'cp_syxw_paiqi', 'issuePrefix' => ''),
			'21407' => array('table' => 'cp_jxsyxw_paiqi', 'issuePrefix' => ''),
			'21408' => array('table' => 'cp_hbsyxw_paiqi', 'issuePrefix' => ''),
			'53' => array('table' => 'cp_ks_paiqi', 'issuePrefix' => ''),
			'54' => array('table' => 'cp_klpk_paiqi', 'issuePrefix' => ''),
            '55' => array('table' => 'cp_cqssc_paiqi', 'issuePrefix' => ''),
    	    '21421' => array('table' => 'cp_gdsyxw_paiqi', 'issuePrefix' => ''),
		);
		$stime = date('Y-m-d H:i:s', time() - 86400 * 2);
		if($lidMap[$lid])
		{
			$issue = preg_replace("/^{$lidMap[$lid]['issuePrefix']}/is", '', $issue);
			$sql = "SELECT show_end_time FROM {$lidMap[$lid]['table']} WHERE 
			issue < '$issue' and is_open = 1 and delect_flag = 0 ORDER BY issue DESC LIMIT 1";
			if(in_array($lid, array(11, 19)))
			{
				$sql = "select show_end_time from {$lidMap[$lid]['table']} where show_end_time > 0 and mid < '$issue' and ctype = 1 
						order by mid desc limit 1";
			}
			$stime = $this->cfgDB->query($sql)->getOne();
		}
		return $stime;
    }
    
	/**
     * 查询票商设置的出票比例
     */
    protected function _getSeller($lid, $money)
    {
    	$seller = '';
    	$sql = "select ticketSeller, lid, ticketRate from cp_seller_rate where 1";
    	$res = $this->cfgDB->query($sql)->getAll();
    	foreach ($res as $val)
    	{
    		if($val['lid'] != $lid) continue;
    		if($val['ticketRate'] == 100)
    		{
    			$seller = $val['ticketSeller'];
    			break;
    		}
    	}
    	if(empty($seller))
    	{
    		$seller = ($money >= 2500) ? 'caidou' : 'qihui';
    	}
    	return $seller;
    }
}
