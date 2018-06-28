<?php

class Match_Model extends MY_Model {

    public function __construct() {
        parent::__construct();
    }
	
    public function test()
    {
    	$ss = $this->db->query('select * from cp_jczq_score where 1')->getAll();
    	print_r($ss);
    }
    
    public function getMatch($lottery) {
    	$this->dcDB = $this->load->database('dc', true);
    	$this->cfgDB = $this->load->database('cfg', true);
    	if ($lottery === 'jczq') {
    		$fields = 'mid, hot, hotid, end_sale_time, sale_status, show_end_time, if (hotid = 0, 100, hotid) as hid';
    	}else {
    		$fields = 'mid, hot, hotid, begin_time, sale_status, show_end_time, if (hotid = 0, 100, hotid) as hid';
    	}
    	$sql = "select ".$fields." from cp_".$lottery."_paiqi where 1 and show_end_time >= now() ORDER BY hot DESC, hid, show_end_time";
    	$res = $this->cfgDB->query($sql)->getAll();
    	$matchs = array();
    	foreach ($res as &$val)
    	{
    		if ($lottery === 'jczq' && !($val['sale_status'] & 1) || ($lottery === 'jclq'  && !($val['sale_status'] & 2))) {
    			unset($val);
    			continue;
    		}
    		$matchs[$val['mid']] = $val;
    		$mids[] = $val['mid'];
    		$hotids[$val['hotid']][] = $val['mid'];
    	}
    	if($mids)
    	{
    		$midStr = implode("','", $mids);
    		$sql1 = "select mid, league_abbr, home_abbr, away_abbr, ctype, codes from cp_".$lottery."_match where mid in ('{$midStr}')";
    		$result = $this->dcDB->query($sql1)->getAll();
    		
    		$matches = array();
    		foreach ($result as &$value) {
    			$codes = @unserialize($value['codes']);
    			$matches[$value['mid']]['jzdt'] = strtotime($matchs[$value['mid']]['show_end_time'])*1000;
    			$matches[$value['mid']]['nameSname'] = $value['league_abbr'];
    			$matches[$value['mid']]['homeSname'] = $value['home_abbr'];
    			$matches[$value['mid']]['awarySname'] = $value['away_abbr'];
    			if (isset($codes['h'])) {
    				if ($lottery === 'jczq' && $value['ctype'] == 1) {
    					$matches[$value['mid']]['spfSp3'] = $codes['h'];
    					$matches[$value['mid']]['spfSp1'] = $codes['d'];
    					$matches[$value['mid']]['spfSp0'] = $codes['a'];
    				}elseif ($lottery === 'jclq' && $value['ctype'] == 2) {
    					$matches[$value['mid']]['let'] = $codes['fixedodds'];
    					$matches[$value['mid']]['rfsfHf'] = $codes['h'];
    					$matches[$value['mid']]['rfsfHs'] = $codes['a'];
    				}
    			}
    			if (empty($matches[$value['mid']]['spfSp3']) && empty($matches[$value['mid']]['let'])) {
    				unset($matches[$value['mid']]);
    			}else {
    				$matches[$value['mid']] = array_merge($matches[$value['mid']], $matchs[$value['mid']]);
    			}
    		}
    		foreach ($hotids as $h => &$hotid) {
    			foreach ($hotid as $k => $hmid) {
    				if (count($data) < 3 && $matches[$hmid]) {
    					$data[$hmid] = $matches[$hmid];
    					$hots[$h][] = $hmid;
    				}
    			}
    		}
    	}
    	return array('data' => $data, 'hotid' => $hots);
    }

    public function insertJclqResult($fields, $bdata)
    {
        if(!empty($bdata['s_data']))
        {
            $this->cfgDB = $this->load->database('cfg', TRUE);
            $upd = array('detail');
            $sql = "insert cp_jclq_detail(" . implode(', ', $fields) . ") values" . 
            implode(', ', $bdata['s_data']) . $this->onduplicate($fields, $upd);
            $this->cfgDB->query($sql, $bdata['d_data']);
        }
    }

    public function insertJczqResult($fields, $bdata)
    {
        if(!empty($bdata['s_data']))
        {
            $this->cfgDB = $this->load->database('cfg', TRUE);
            $upd = array('detail');
            $sql = "insert cp_jczq_detail(" . implode(', ', $fields) . ") values" . 
            implode(', ', $bdata['s_data']) . $this->onduplicate($fields, $upd);
            $this->cfgDB->query($sql, $bdata['d_data']);
        }
    }
    

}
