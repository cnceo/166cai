<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Cli_Paiqi extends MY_Controller 
{
	private $typeMap = array('tczq', 'jczq', 'jclq', 'bjdc');
	public function __construct()
	{
		parent::__construct();
		$this->load->model('Match_model');
	}

	public function index($type = '')
	{
		if(in_array($type, $this->typeMap))
		{
			$this->$type();
		}
		else
		{
			foreach ($this->typeMap as $v)
			{
				$this->$v();
			}
		}
	}
	
	/**
	 * 参    数：无
	 * 作    者：shigx
	 * 功    能：北京单场从match入paiqi表操作
	 * 修改日期：2015-03-27
	 */
	private function bjdc()
	{
		$matchs = $this->Match_model->getBjdcMatchs();
		if($matchs)
		{
			$bdata1['s_data'] = array();
    		$bdata1['d_data'] = array();
    		$bdata2['s_data'] = array();
    		$bdata2['d_data'] = array();
    		$bdata3['s_data'] = array();
    		$bdata3['d_data'] = array();
    		$id_arr = array();
			foreach ($matchs as $val)
			{
				array_push($id_arr, $val['id']);
				switch ($val['ctype'])
				{
					case 1:
						array_push($bdata1['s_data'], "(?, ?, ?, ?, ?, ?, ?, ?, ?, 1, now())");
						array_push($bdata1['d_data'], $val['mid']);
						array_push($bdata1['d_data'], $val['m_date']);
						array_push($bdata1['d_data'], $val['mname']);
						array_push($bdata1['d_data'], $val['game_type']);
						array_push($bdata1['d_data'], $val['league']);
						array_push($bdata1['d_data'], $val['home']);
						array_push($bdata1['d_data'], $val['away']);
						array_push($bdata1['d_data'], $val['begin_time']);
						$codes = unserialize($val['codes']);
						array_push($bdata1['d_data'], $codes['fixedodds']);
						break;
					case 2:
						array_push($bdata2['s_data'], "(?, ?, ?, ?, ?, ?, ?, ?, ?, 1, now())");
						array_push($bdata2['d_data'], $val['mid']);
						array_push($bdata2['d_data'], $val['m_date']);
						array_push($bdata2['d_data'], $val['mname']);
						array_push($bdata2['d_data'], $val['game_type']);
						array_push($bdata2['d_data'], $val['league']);
						array_push($bdata2['d_data'], $val['home']);
						array_push($bdata2['d_data'], $val['away']);
						array_push($bdata2['d_data'], $val['begin_time']);
						$codes = unserialize($val['codes']);
						array_push($bdata2['d_data'], $codes['fixedodds']);
						break;
					case 3:
					case 4:
					case 5:
					case 6:
						array_push($bdata3['s_data'], "(?, ?, ?, ?, ?, ?, ?, ?, 1, now())");
						array_push($bdata3['d_data'], $val['mid']);
						array_push($bdata3['d_data'], $val['m_date']);
						array_push($bdata3['d_data'], $val['mname']);
						array_push($bdata3['d_data'], $val['game_type']);
						array_push($bdata3['d_data'], $val['league']);
						array_push($bdata3['d_data'], $val['home']);
						array_push($bdata3['d_data'], $val['away']);
						array_push($bdata3['d_data'], $val['begin_time']);
						break;
					case 7:
						break;
				}
			}
			
			if(!empty($bdata1['s_data']))
			{
				$fields = array('mid', 'm_date', 'mname', 'game_type', 'league', 'home', 'away', 'begin_time', 'rq', 'status', 'created');
				$this->Match_model->saveSfggPaiqi($fields, $bdata1);
				unset($fields, $bdata1);
			}
			if(!empty($bdata2['s_data']))
			{
				$fields = array('mid', 'm_date', 'mname', 'game_type', 'league', 'home', 'away', 'begin_time', 'rq', 'status', 'created');
				$this->Match_model->saveBjdcPaiqi($fields, $bdata2);
				unset($fields, $bdata2);
			}
			if(!empty($bdata3['s_data']))
			{
				$fields = array('mid', 'm_date', 'mname', 'game_type', 'league', 'home', 'away', 'begin_time', 'status', 'created');
				$this->Match_model->saveBjdcPaiqi($fields, $bdata3);
				unset($fields, $bdata3);
			}
			
			$id_str = implode(',', $id_arr);
			$this->Match_model->updateBjdcMatch($id_str);
		}
	}
	
	/**
	 * 参    数：无
	 * 作    者：shigx
	 * 功    能：体彩足球从match入paiqi表操作
	 * 修改日期：2015-03-27
	 */
	private function tczq()
	{
		$this->Match_model->saveTczqPaiqi();
		$this->tczqBegindate();
	}
	
	private function tczqBegindate()
	{
		$mids = $this->Match_model->getNewMid(3);
		$mDatas = $this->Match_model->getBegindateByMid($mids, 1);
		$mArr = array();
		foreach ($mDatas as $mdata)
		{
			$mArr[$mdata['mid']][$mdata['mname']] = $mdata['begin_date'];
		}
		foreach ($mArr as $mid => $data)
		{
			$api = $this->config->item('api_bf')."apps?lotyid=1&expect=20".$mid;
			$apiContent = file_get_contents($api);
			$apiData = json_decode($apiContent, true);
			foreach ($data as $mname => $begindate)
			{
				if (!empty($apiData[$mname]['mtime']))
				{
					list($y, $m, $d, $h, $i, $s) = explode(',', $apiData[$mname]['mtime']);
					if ($begindate !== $y."-".$m."-".$d." ".$h.":".$i)
					{
						$this->Match_model->updateBegindate($y."-".$m."-".$d." ".$h.":".$i, $mname, $mid);
					}
				}
			}
		}
	}
	
	/**
	 * 参    数：无
	 * 作    者：shigx
	 * 功    能：竞彩足球从match入paiqi表操作
	 * 修改日期：2015-03-27
	 */
	private function jczq()
	{
		$matchs = $this->Match_model->getJczqMatchs();
		if($matchs)
		{
			$bdata1['s_data'] = array();
			$bdata1['d_data'] = array();
			$bdata2['s_data'] = array();
			$bdata2['d_data'] = array();
			$id_arr = array();
			foreach ($matchs as $val)
			{
				array_push($id_arr, $val['id']);
				switch ($val['ctype'])
				{
					case 2:
						array_push($bdata1['s_data'], "(?, ?, ?, ?, ?, ?, ?, ?, ?, now())");
						array_push($bdata1['d_data'], $val['mid']);
						array_push($bdata1['d_data'], $val['m_date']);
						array_push($bdata1['d_data'], $val['mname']);
						array_push($bdata1['d_data'], $val['league_abbr']);
						array_push($bdata1['d_data'], $val['home_abbr']);
						array_push($bdata1['d_data'], $val['away_abbr']);
						$end_sale_time = $val['end_sale_date'] . ' ' . $val['end_sale_time'];
						array_push($bdata1['d_data'], $end_sale_time);
						$codes = unserialize($val['codes']);
						array_push($bdata1['d_data'], $codes['fixedodds']);
						array_push($bdata1['d_data'], $val['status']);
						break;
					case 1:
					case 3:
					case 4:
					case 5:
						array_push($bdata2['s_data'], "(?, ?, ?, ?, ?, ?, ?, ?, now())");
						array_push($bdata2['d_data'], $val['mid']);
						array_push($bdata2['d_data'], $val['m_date']);
						array_push($bdata2['d_data'], $val['mname']);
						array_push($bdata2['d_data'], $val['league_abbr']);
						array_push($bdata2['d_data'], $val['home_abbr']);
						array_push($bdata2['d_data'], $val['away_abbr']);
						$end_sale_time = $val['end_sale_date'] . ' ' . $val['end_sale_time'];
						array_push($bdata2['d_data'], $end_sale_time);
						array_push($bdata2['d_data'], $val['status']);
						break;
				}
			}

			if(!empty($bdata1['s_data']))
			{
				$fields = array('mid', 'm_date', 'mname', 'league', 'home', 'away', 'end_sale_time', 'rq', 'status', 'created');
				$this->Match_model->saveJczqPaiqi($fields, $bdata1);
				unset($fields, $bdata1);
			}
			if(!empty($bdata2['s_data']))
			{
				$fields = array('mid', 'm_date', 'mname', 'league', 'home', 'away', 'end_sale_time', 'status', 'created');
				$this->Match_model->saveJczqPaiqi($fields, $bdata2);
				unset($fields, $bdata2);
			}
			
			$id_str = implode(',', $id_arr);
			$this->Match_model->updatejczqMatch($id_str);
		}
	}
	
	/**
	 * 参    数：无
	 * 作    者：shigx
	 * 功    能：竞彩篮球从match入paiqi表操作
	 * 修改日期：2015-03-27
	 */
	private function jclq()
	{
		$matchs = $this->Match_model->getJclqMatchs();
		if($matchs)
		{
			$bdata1['s_data'] = array();
			$bdata1['d_data'] = array();
			$bdata2['s_data'] = array();
			$bdata2['d_data'] = array();
			$bdata3['s_data'] = array();
			$bdata3['d_data'] = array();
			$id_arr = array();
			foreach ($matchs as $val)
			{
				array_push($id_arr, $val['id']);
				switch ($val['ctype'])
				{
					case 2:
						array_push($bdata1['s_data'], "(?, ?, ?, ?, ?, ?, ?, ?, ?, now())");
						array_push($bdata1['d_data'], $val['mid']);
						array_push($bdata1['d_data'], $val['m_date']);
						array_push($bdata1['d_data'], $val['mname']);
						array_push($bdata1['d_data'], $val['league_abbr']);
						array_push($bdata1['d_data'], $val['home_abbr']);
						array_push($bdata1['d_data'], $val['away_abbr']);
						array_push($bdata1['d_data'], $val['begin_time']);
						$codes = unserialize($val['codes']);
						array_push($bdata1['d_data'], $codes['fixedodds']);
						array_push($bdata1['d_data'], $val['status']);
						break;
					case 1:
					case 3:
						array_push($bdata2['s_data'], "(?, ?, ?, ?, ?, ?, ?, ?, now())");
						array_push($bdata2['d_data'], $val['mid']);
						array_push($bdata2['d_data'], $val['m_date']);
						array_push($bdata2['d_data'], $val['mname']);
						array_push($bdata2['d_data'], $val['league_abbr']);
						array_push($bdata2['d_data'], $val['home_abbr']);
						array_push($bdata2['d_data'], $val['away_abbr']);
						array_push($bdata2['d_data'], $val['begin_time']);
						array_push($bdata2['d_data'], $val['status']);
						break;
					case 4:
						array_push($bdata3['s_data'], "(?, ?, ?, ?, ?, ?, ?, ?, ?, now())");
						array_push($bdata3['d_data'], $val['mid']);
						array_push($bdata3['d_data'], $val['m_date']);
						array_push($bdata3['d_data'], $val['mname']);
						array_push($bdata3['d_data'], $val['league_abbr']);
						array_push($bdata3['d_data'], $val['home_abbr']);
						array_push($bdata3['d_data'], $val['away_abbr']);
						array_push($bdata3['d_data'], $val['begin_time']);
						$codes = unserialize($val['codes']);
						array_push($bdata3['d_data'], $codes['score']);
						array_push($bdata3['d_data'], $val['status']);
						break;
				}
			}
				
			if(!empty($bdata1['s_data']))
			{
				$fields = array('mid', 'm_date', 'mname', 'league', 'home', 'away', 'begin_time', 'rq', 'status', 'created');
				$this->Match_model->saveJclqPaiqi($fields, $bdata1);
				unset($fields, $bdata1);
			}
			if(!empty($bdata2['s_data']))
			{
				$fields = array('mid', 'm_date', 'mname', 'league', 'home', 'away', 'begin_time', 'status', 'created');
				$this->Match_model->saveJclqPaiqi($fields, $bdata2);
				unset($fields, $bdata2);
			}
			if(!empty($bdata3['s_data']))
			{
				$fields = array('mid', 'm_date', 'mname', 'league', 'home', 'away', 'begin_time', 'preScore', 'status', 'created');
				$this->Match_model->saveJclqPaiqi($fields, $bdata3);
				unset($fields, $bdata3);
			}
				
			$id_str = implode(',', $id_arr);
			$this->Match_model->updatejclqMatch($id_str);
		}
	}
}
