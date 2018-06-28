<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/*
 * 数据中心 -- 模型层
 * @author:liuli
 * @date:2015-02-28
 */

class Data_Model extends MY_Model {

	private $CI;
	private $compare_succ;
    public function __construct() {
        parent::__construct();
        //连接 数据中心database
        $this->load->library('tools');
        $this->load->model('compare_model');
    	$this->compare_succ = $this->compare_model->status_map['compare_succ'];
    }

    public function getCrons()
    {
    	$cronList = array();
    	$crons = $this->dc->query("select lname, ctype, source from cp_cron_score where start = 1")->getALL();
    	if(!empty($crons))
    	{
    		foreach ($crons as $cron)
    		{
                if(in_array($cron['ctype'], array('syxw', 'jxsyxw', 'hbsyxw', 'ks','jlks','jxks', 'klpk', 'cqssc', 'gdsyxw')) && in_array($cron['source'], array('2', '3', '4', '5')) )
                {
                    continue;
                }
    			$cronList[$cron['ctype']][] = $cron;
    		}
    	}
    	return $cronList;
    }
    
    public function insertJczqScore($fields, $bdata)
    {
    	if(!empty($bdata['s_data']))
    	{
	    	$upd = array('half_score', 'full_score', 'status');
	    	$sql = "insert cp_jczq_score(" . implode(', ', $fields) . ") values" . 
	    	implode(', ', $bdata['s_data']) . $this->onduplicate($fields, $upd);
	    	$this->tools->filter($bdata['d_data'], array('mytrim'));
	    	$this->dc->query($sql, $bdata['d_data']);
    	}
    }
    
	public function insertBjdcScore($fields, $bdata)
    {
    	if(!empty($bdata['s_data']))
    	{
	    	$upd = array('half_score', 'full_score', 'status');
	    	$sql = "insert cp_bjdc_score(" . implode(', ', $fields) . ") values" . 
	    	implode(', ', $bdata['s_data']) . $this->onduplicate($fields, $upd);
	    	$this->tools->filter($bdata['d_data'], array('mytrim'));
	    	$this->dc->query($sql, $bdata['d_data']);
    	}
    }
    
	public function insertSfggScore($fields, $bdata)
    {
    	if(!empty($bdata['s_data']))
    	{
	    	$upd = array('full_score', 'status');
	    	$sql = "insert cp_sfgg_score(" . implode(', ', $fields) . ") values" . 
	    	implode(', ', $bdata['s_data']) . $this->onduplicate($fields, $upd);
	    	$this->tools->filter($bdata['d_data'], array('mytrim'));
	    	$this->dc->query($sql, $bdata['d_data']);
    	}
    }
    
	public function insertJclqScore($fields, $bdata)
    {
    	if(!empty($bdata['s_data']))
    	{
	    	$upd = array('full_score', 'status');
	    	$sql = "insert cp_jclq_score(" . implode(', ', $fields) . ") values" . 
	    	implode(', ', $bdata['s_data']) . $this->onduplicate($fields, $upd);
	    	$this->tools->filter($bdata['d_data'], array('mytrim'));
	    	$this->dc->query($sql, $bdata['d_data']);
    	}
    }
    
	public function insertSfcScore($fields, $bdata)
    {
    	if(!empty($bdata['s_data']))
    	{
	    	$upd = array('half_score', 'full_score', 'status', 'result');
	    	$sql = "insert cp_sfc_score(" . implode(', ', $fields) . ") values" . 
	    	implode(', ', $bdata['s_data']) . $this->onduplicate($fields, $upd);
	    	$this->tools->filter($bdata['d_data'], array('mytrim'));
	    	$this->dc->query($sql, $bdata['d_data']);
    	}
    }
    
 	public function insertBqcScore($fields, $bdata)
    {
    	if(!empty($bdata['s_data']))
    	{
	    	$upd = array('half_score', 'full_score', 'status', 'half_result', 'full_result');
	    	$sql = "insert cp_bqc_score(" . implode(', ', $fields) . ") values" . 
	    	implode(', ', $bdata['s_data']) . $this->onduplicate($fields, $upd);
	    	$this->tools->filter($bdata['d_data'], array('mytrim'));
	    	$this->dc->query($sql, $bdata['d_data']);
    	}
    }
    
    public function insertJqcScore($fields, $bdata)
    {
    	if(!empty($bdata['s_data']))
    	{
	    	$upd = array('half_score', 'full_score', 'status', 'home_result', 'away_result');
	    	$sql = "insert cp_jqc_score(" . implode(', ', $fields) . ") values" . 
	    	implode(', ', $bdata['s_data']) . $this->onduplicate($fields, $upd);
	    	$this->tools->filter($bdata['d_data'], array('mytrim'));
	    	$this->dc->query($sql, $bdata['d_data']);
    	}
    }

    /*
     * 更新数字彩赛果
     * @date:2015-03-23
     */
    public function insertNumberAwards($match)
    {
        try 
        {
            $upd = array('awardNum', 'time', 'sale', 'pool', 'bonusDetail', 'status', 'rstatus');
            $fields = array_keys($match);
            $sql = "insert cp_number_award(" . implode(',', $fields) . ",created)values(" . implode(',', array_map(array($this, 'maps'), $fields)) .  ", now())" . $this->onduplicate($fields, $upd);
        }
        catch (Exception $e)
        {
            log_message('LOG', "insertNumberAwards error: " . __CLASS__ . ':' . __LINE__ , "ERROR");
            return false;
        }
        return $this->dc->query($sql, $match);
    }


    /*
     * 更新胜负彩/任选九开奖详情
     * @date:2015-03-23
     */
    public function insertSfcAwards($match)
    {
        try 
        {
            $upd = array('result', 'sfc_sale', 'rj_sale', 'award', 'award_detail', 'status', 'rstatus');
            $fields = array_keys($match);
            $sql = "insert cp_rsfc_score(" . implode(',', $fields) . ",created)values(" . implode(',', array_map(array($this, 'maps'), $fields)) .  ", now())" . $this->onduplicate($fields, $upd);
        }
        catch (Exception $e)
        {
            log_message('LOG', "insertSfcAwards error: " . __CLASS__ . ':' . __LINE__ , "ERROR");
            return false;
        }
        return $this->dc->query($sql, $match);
    }
    
	public function insertBqcResult($datas)
    {
        try 
        {
            $upd = array('result', 'sale', 'award', 'award_detail', 'status', 'rstatus');
            $fields = array_keys($datas['d_data']);
            $sql = "insert cp_rbqc_score(" . implode(',', $fields) . ", source, created)values" . implode(',', $datas['s_data']) . $this->onduplicate($fields, $upd);
        }
        catch (Exception $e)
        {
            log_message('LOG', "insertBqcResult error: " . __CLASS__ . ':' . __LINE__ , "ERROR");
            return false;
        }
        $this->tools->filter($datas['d_data'], array('mytrim'));
        return $this->dc->query($sql, $datas['d_data']);
    }
    
	public function insertJqcResult($datas)
    {
        try 
        {
            $upd = array('result', 'sale', 'award', 'award_detail', 'status', 'rstatus');
            $fields = array_keys($datas['d_data']);
            $sql = "insert cp_rjqc_score(" . implode(',', $fields) . ", source, created)values" . implode(',', $datas['s_data']) . $this->onduplicate($fields, $upd);
        }
        catch (Exception $e)
        {
            log_message('LOG', "insertSfcAwards error: " . __CLASS__ . ':' . __LINE__ , "ERROR");
            return false;
        }
        $this->tools->filter($datas['d_data'], array('mytrim'));
        return $this->dc->query($sql, $datas['d_data']);
    }
    
	public function keysOfJczq()
    {
    	$sql = "SELECT mid, m_date, if(date_add(`end_sale_time`, interval 115 minute) < now(), 1, 0) as status 
    	FROM `cp_jczq_paiqi` 
		WHERE 1 and status < {$this->compare_succ} and `end_sale_time` < date_sub(now(), interval 115 minute)
		and end_sale_time  > date_sub(now(), interval 7 day)
		order by m_date desc";
    	$datas = $this->dc->query($sql)->getAll();
    	$rekeys = array();
    	if(!empty($datas))
    	{
	    	foreach ($datas as $data)
	    	{
	    		if($data['status'] == 1)
	    		{
	    			$keys[$data['m_date']][] = $data['mid'];
	    		}
	    	}
	    	if(!empty($keys))
	    		$rekeys = $keys;
    	}
    	return $rekeys;
    }
    
	public function keysOfJclq()
    {
    	$sql = "SELECT mid, m_date, if(date_add(`begin_time`, interval 100 minute) < now(), 1, 0) as status 
    	FROM `cp_jclq_paiqi` 
		WHERE 1 and status < {$this->compare_succ} and `begin_time` < date_sub(now(), interval 100 minute)
		and  begin_time > date_sub(now(), interval 7 day)
		order by m_date desc";
    	$datas = $this->dc->query($sql)->getAll();
    	$rekeys = array();
    	if(!empty($datas))
    	{
	    	foreach ($datas as $data)
	    	{
	    		if($data['status'] == 1)
	    		{
	    			$keys[$data['m_date']][] = $data['mid'];
	    		}
	    	}
	    	if(!empty($keys))
	    		$rekeys = $keys;
    	}
    	return $rekeys;
    }
    
	public function keysOfBjdc()
    {
    	$sql = "SELECT mid, mname, if(date_add(`begin_time`, interval 120 minute) < now(), 1, 0) as status 
    	FROM `cp_bjdc_paiqi` 
		WHERE 1 and status < {$this->compare_succ} and `begin_time` < date_sub(now(), interval 120 minute)
		and begin_time  > date_sub(now(), interval 7 day)
		order by mid desc";
    	$datas = $this->dc->query($sql)->getAll();
    	$rekeys = array();
    	if(!empty($datas))
    	{
	    	foreach ($datas as $data)
	    	{
	    		if($data['status'] == 1)
	    		{
	    			$keys[$data['mid']][] = $data['mname'];
	    		}
	    	}
	    	if(!empty($keys))
	    		$rekeys = $keys;
    	}
    	return $rekeys;
    }
    
	public function keysOfSfgg()
    {
    	$sql = "SELECT mid, mname, if(date_add(`begin_time`, interval 120 minute) < now(), 1, 0) as status 
    	FROM `cp_sfgg_paiqi` 
		WHERE 1 and status < {$this->compare_succ} and `begin_time` < date_sub(now(), interval 120 minute)
		and begin_time  > date_sub(now(), interval 7 day)
		order by mid desc";
    	$datas = $this->dc->query($sql)->getAll();
    	$rekeys = array();
    	if(!empty($datas))
    	{
	    	foreach ($datas as $data)
	    	{
	    		if($data['status'] == 1)
	    		{
	    			$keys[$data['mid']][] = $data['mname'];
	    		}
	    	}
	    	if(!empty($keys))
	    		$rekeys = $keys;
    	}
    	return $rekeys;
    }
    
    
	public function keysOfSfc($ctype)
    {
    	$sql = "SELECT mid, mname, if(date_add(`begin_date`, interval 120 minute) < now(), 1, 0) as status 
    	FROM `cp_tczq_paiqi` 
		WHERE 1 and ctype = $ctype and status < {$this->compare_succ} and `begin_date` <  date_sub(now(), interval 120 minute)
		and begin_date  > date_sub(now(), interval 7 day)
		order by mid desc limit 10";
    	$datas = $this->dc->query($sql)->getAll();
    	$rekeys = array();
    	if(!empty($datas))
    	{
	    	foreach ($datas as $data)
	    	{
	    		if($data['status'] == 1)
	    		{
	    			$keys[$data['mid']][] = $data['mname'];
	    		}
	    	}
	    	if(!empty($keys))
    			$rekeys = $keys;
    	}
    	return $rekeys;
    }
    
	public function keysOfNumber($ctype)
    {
    	$sql = "SELECT issue as mid, if(date_add(`award_time`, interval 10 minute) < now(), 1, 0) as status 
    	FROM `cp_{$ctype}_paiqi` 
		WHERE 1 and status < {$this->compare_succ} and `award_time` <  date_sub(now(), interval 10 minute)
		and award_time > date_sub(now(), interval 7 day)
		order by issue desc limit 10";
    	$datas = $this->dc->query($sql)->getAll();
    	$rekeys = array();
    	if(!empty($datas))
    	{
	    	foreach ($datas as $data)
	    	{
	    		if($data['status'] == 1)
	    		{
	    			$keys[$data['mid']] = $data['mid'];
	    		}
	    	}
	    	if(!empty($keys))
	    		$rekeys['issues'] = array_values($keys);
    	}
    	return $rekeys;
    }
    
	public function keysOfRNumber($ctype)
    {
    	$sql = "SELECT issue as mid, if(date_add(`award_time`, interval 30 minute) < now(), 1, 0) as status 
    	FROM `cp_{$ctype}_paiqi` 
		WHERE 1 and rstatus < {$this->compare_succ} and `award_time` <  date_sub(now(), interval 30 minute)
		and award_time  > date_sub(now(), interval 7 day)
		order by issue desc limit 10";
    	$datas = $this->dc->query($sql)->getAll();
    	$rekeys = array();
    	if(!empty($datas))
    	{
	    	foreach ($datas as $data)
	    	{
	    		if($data['status'] == 1)
	    		{
	    			$keys[$data['mid']] = $data['mid'];
	    		}
	    	}
	    	if(!empty($keys))
	    		$rekeys['issues'] = array_values($keys);
    	}
    	return $rekeys;
    }
    
	public function keysOfRsfc($ctype)
    {
    	$sql = "select mid, if(date_add(`begin_date`, interval 120 minute) < now(), 1, 0) as status from cp_tczq_paiqi 
    	where begin_date <  date_sub(now(), interval 120 minute) and begin_date > date_sub(now(), interval 7 day)
		and (result1 = '' or result1 is null) and ctype = $ctype";
    	$datas = $this->dc->query($sql)->getAll();
    	$rekeys = array();
    	if(!empty($datas))
    	{
	    	foreach ($datas as $data)
	    	{
	    		if($data['status'] == 1)
	    		{
	    			$keys[$data['mid']] = $data['mid'];
	    		}
	    	}
	    	if(!empty($keys))
	    		$rekeys['issue'] = array_values($keys);
    	}
    	return $rekeys;
    }
    
	public function keysOfDsfc($ctype)
    {
    	$maps = array('1' => 'rsfc','2' => 'rbqc', '3' => 'rjqc');
    	$sql = "select n.mid, max(n.begin_date) mtimes from cp_{$maps[$ctype]}_paiqi m 
    	join cp_tczq_paiqi n on m.mid = n.mid and n.ctype = $ctype
    	where m.rstatus < 50 and n.begin_date > date_sub(now(), interval 7 day) 
    	group by n.mid";
    	$datas = $this->dc->query($sql)->getAll();
    	$rekeys = array();
    	if(!empty($datas))
    	{
	    	foreach ($datas as $data)
	    	{
	    		if($data['mtimes'] <= date('Y-m-d H:i:s', strtotime('-3 hour')))
	    		{
	    			$keys[$data['mid']] = $data['mid'];
	    		}
	    	}
	    	if(!empty($keys))
	    		$rekeys['issue'] = array_values($keys);
    	}
    	return $rekeys;
    }

}
