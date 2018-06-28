<?php
class Limit_Code_Model extends MY_Model
{
	
	private $_lidTab = array(
		FCSD	=> array(
				'table' => 'cp_fc3d_paiqi', 
				'library' => 'Fcsd', 
				'playType' => array('1' => 'zx', '2' => 'z3', '3' => 'z6')
		),
		PLS		=> array(
				'table' => 'cp_pl3_paiqi', 
				'library' => 'Pls', 
				'playType' => array('1' => 'zx', '2' => 'z3', '3' => 'z6')
		),
		PLW 	=> array(
				'table' => 'cp_pl5_paiqi', 
				'library' => 'Plw', 
				'playType' => array('1' => 'gg')
		),
		SYXW	=> array(
				'table' => 'cp_syxw_paiqi', 
				'library' => 'Syxw', 
				'playType' => array('1'=>'qy','2'=>'r2','3'=>'r3','4'=>'r4','5'=>'r5','6'=>'r6','7'=>'r7','8'=>'r8','9'=>'q2zhix','10'=>'q3zhix','11'=>'q2zux','12'=>'q3zux','13'=>'lx3','14'=>'lx4','15'=>'lx5')
		),
		JXSYXW 	=> array(
				'table' => 'cp_jxsyxw_paiqi', 
				'library' => 'Jxsyxw', 
				'playType' => array('1'=>'qy','2'=>'r2','3'=>'r3','4'=>'r4','5'=>'r5','6'=>'r6','7'=>'r7','8'=>'r8','9'=>'q2zhix','10'=>'q3zhix','11'=>'q2zux','12'=>'q3zux')
		),
		HBSYXW	=> array(
				'table' => 'cp_hbsyxw_paiqi', 
				'library' => 'Hbsyxw', 
				'playType' => array('1'=>'qy','2'=>'r2','3'=>'r3','4'=>'r4','5'=>'r5','6'=>'r6','7'=>'r7','8'=>'r8','9'=>'q2zhix','10'=>'q3zhix','11'=>'q2zux','12'=>'q3zux')
		),
	    GDSYXW	=> array(
	        'table' => 'cp_gdsyxw_paiqi',
	        'library' => 'Gdsyxw',
	        'playType' => array('1'=>'qy','2'=>'r2','3'=>'r3','4'=>'r4','5'=>'r5','6'=>'r6','7'=>'r7','8'=>'r8','9'=>'q2zhix','10'=>'q3zhix','11'=>'q2zux','12'=>'q3zux')
	    ),
		KS		=> array(
				'table' => 'cp_ks_paiqi', 
				'library' => 'Ks', 
				'playType' => array('1' => 'hz', '2' => 'sthtx', '3' => 'sthdx', '4' => 'sbth', '5' => 'slhtx', '6' => 'ethfx', '7' => 'ethdx', '8' => 'ebth')
		),
		JLKS	=> array(
			'table' => 'cp_jlks_paiqi',
			'library' => 'Jlks',
			'playType' => array('1' => 'hz', '2' => 'sthtx', '3' => 'sthdx', '4' => 'sbth', '5' => 'slhtx', '6' => 'ethfx', '7' => 'ethdx', '8' => 'ebth')
		),
	    JXKS	=> array(
	        'table' => 'cp_jxks_paiqi',
	        'library' => 'Jxks',
	        'playType' => array('1' => 'hz', '2' => 'sthtx', '3' => 'sthdx', '4' => 'sbth', '5' => 'slhtx', '6' => 'ethfx', '7' => 'ethdx', '8' => 'ebth')
	    ),
		KLPK	=> array(
				'table' => 'cp_klpk_paiqi', 
				'library' => 'Klpk', 
				'playType' => array('1' => 'r1', '2' => 'rx2', '3' => 'rx3', '4' => 'rx4', '5' => 'rx5', '6' => 'rx6', '7' => 'th', '8' => 'ths', '9' => 'sz', '10' => 'bz', '11' => 'dz')
		),
		CQSSC	=> array(
			'table' => 'cp_cqssc_paiqi',
			'library' => 'Cqssc',
			'playType' => array('10' => '1xzhix', '20' => '2xzhix', '23' => '2xzux', '30' => '3xzhix', '33' => '3xzu3', '34' => '3xzu6', '40' => '5xzhix', '43' => '5xtx', '1' => 'dxds'),
			'playTypeCname' => array('10' => '一星直选', '20' => '二星直选', '23' => '二星组选', '30' => '三星直选', '33' => '三星组三', '34' => '三星组六', '40' => '五星直选', '43' => '五星通选', '1' => '大小单双'),
			'codeName' => array('1' => array(array(1, 2, 4, 5), array('大', '小', '单', '双')))
				
		)
	);
	
	public function __construct()
	{
		parent::__construct();
	}
	
	public function checkcode()
	{
		$this->load->library('BetCnName');
		$str = '';
		$this->load->driver('cache', array('adapter' => 'redis'));
        $REDIS = $this->config->item('REDIS');
		foreach ($this->_lidTab as $lid => $lidinfo) {
			$awardNum = $this->cfgDB->query("select awardNum, issue, award_time from {$lidinfo['table']} 
					where award_time < NOW() and status >= 50 and award_time > date_sub(now(), interval 1 day) and delect_flag = 0 AND end_time<NOW()
					order by issue desc LIMIT 1")->getRow();
			$codes = $this->cache->hGet($REDIS['LIMIT_CODE'], $lid);
			$strArr = array();
			$alimitArr = array();
			if ($awardNum && !empty($codes)) {
				$codes = json_decode($codes, true);
				$this->load->library("award/{$lidinfo['library']}", array(), 'award');
				foreach ($lidinfo['playType'] as $pid => $fun) {
					if (!empty($codes[$pid])) {
						foreach ($codes[$pid] as $lissue => $code) {
							if ($this->award->caculatelimit($fun, $code, $awardNum['awardNum'])) {
								$this->db->query("update cp_limit_codes set awardTime = ? where lid = ? and playType = ? and issue = ? and created < '{$awardNum['award_time']}'", array($awardNum['award_time'], $lid, $pid, $lissue));
								if ($this->db->affected_rows()) {
									$strArr[$lid."_".$pid] .= ($this->_lidTab[$lid]['playTypeCname'] ? BetCnName::getCnName($lid).$this->_lidTab[$lid]['playTypeCname'][$pid] : BetCnName::getCnName($lid).BetCnName::getCnPlaytype($lid, $pid));
									$strArr[$lid."_".$pid] .= ($this->_lidTab[$lid]['codeName'][$pid] ? str_replace($this->_lidTab[$lid]['codeName'][$pid][0], $this->_lidTab[$lid]['codeName'][$pid][1], $code) : $code)."开奖";
								}
							}
						}						
					}
				}
				if ($strArr) 
					$this->db->query("INSERT IGNORE INTO cp_alert_log (ctype, ufiled, title, content,status,created)
						VALUES ('22', '".$lid."_".$awardNum['issue']."', '限号方案开奖报警', '".implode('，', array_values($strArr))."请尽快人工确认', '0', NOW())");
			}
		}
	}
}