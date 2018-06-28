<?php

class Ssq
{
	private $lids = array(
		'ssq' => '51',
	);

	private $lbinfo = 	array(
		//SSQ
		'51' => array(
			'fnum' => 6,
			'bnum' => 1,
			'bonus' => array(
				'1' => array(array(6, 1)),
				'2' => array(array(6, 0)),
				'3' => array(array(5, 1)),
				'4' => array(array(5, 0), array(4, 1)),
				'5' => array(array(4, 0), array(3, 1)),
				'6' => array(array(2, 1), array(1, 1), array(0, 1)),
			),
		),
	);
	private $CI;
	public function __construct()
	{
		$this->CI = &get_instance();
		$this->CI->load->library('libcomm');
	}
	
	/*
 	 * 彩种奖金计算器 - 双色球
 	 * @version:V1.2
 	 * @date:2015-08-17
 	 */
	public function getBonusDetail($data, $awards)
	{
		// 投注串过关
		$bounsDetail = $this->cal_sd($data, $awards['base'], $data['lid']);

		// 算奖
		$details = $this->cal_ssq_bonus($bounsDetail, $awards['detail']);
		return $details;
	}

	/*
 	 * 双色球 - 过关
 	 * @version:V1.2
 	 * @date:2015-08-17
 	 */
	private function cal_sd($order, $ainfo, $lid)
	{
		// 06,09,13,26,27,33|01:1:1
		$codestrs = explode(':', $order['codes']);
		$levs = array_keys($this->lbinfo[$lid]['bonus']);
		$award = str_replace(':', '|', $ainfo['awardNumber']);
		$awards = explode('|', $award);
		$params = array();
		
		if(empty($codestrs[0])) continue;
		$codes = explode('|', $codestrs[0]);
		preg_match('/(?:(.*)#)?(.*)/', $codes[0], $rmatches);
		preg_match('/(?:(.*)#)?(.*)/', $codes[1], $bmatches);
		$oricode['rsalts'] = array();
		if(!empty($rmatches[1]))
		{
			$oricode['rsalts'] = explode(',', $rmatches[1]);
		}
		$oricode['bsalts'] = array();
		if(!empty($bmatches[1]))
		{
			$oricode['bsalts'] = explode(',', $bmatches[1]);
		}
		$oricode['rballs'] = explode(',', $rmatches[2]);
		$oricode['bballs'] = explode(',', $bmatches[2]);
		$oricode['award_rballs'] = explode(',', $awards[0]);
		$oricode['award_bballs'] = explode(',', $awards[1]);
		$param = $this->calBonusParams($oricode, $lid);
		foreach ( $levs as $lev)
		{
			$params[$lev][] = $param[$lev];
		}
		
		return $params;
	}

	private function checkWinNum($balls, $aballs)
	{
		return array_intersect($balls, $aballs);
	}

	private function calBonusParams($oricode, $lid)
	{
		$rsaltn = $this->checkWinNum($oricode['rsalts'], $oricode['award_rballs']);
		$rballn = $this->checkWinNum($oricode['rballs'], $oricode['award_rballs']);
	
		$bsaltn = $this->checkWinNum($oricode['bsalts'], $oricode['award_bballs']);
		$bballn = $this->checkWinNum($oricode['bballs'], $oricode['award_bballs']);
	
		$params = array(
				'RH' => count($rsaltn), //前区胆中个数
				'RT' => count($rballn), //前区拖中个数
				'RN' => count($oricode['rsalts']), //前区胆个数
				'PN' => count($oricode['rballs']), //前区拖个数
	
				'BH' => count($bsaltn), //后区胆中个数
				'BT' => count($bballn), //后区拖中个数
				'BN' => count($oricode['bsalts']), //后区胆个数
				'TN' => count($oricode['bballs'])  //后区拖个数
		);
		return $this->win_sd($params, $lid);
	}

	// 计算前区或后区命中指定个数的方案注数
	private function solveHits($num, $req, $opt, $reqHit, $optHit)
	{
		$optLeft = $num - $req; //拖中还需选择的个数
		$optMiss = $opt - $optHit; //拖中未中球数
		$max = $reqHit + $optHit; //总中球数
		$hits = array();
		for ($i = 0; $i <= $num; ++ $i)
		{
			if ($i < $reqHit || $i > $max)
			{
				$hits[$i] = 0;
			}
			else
			{
				$optNeed = $i - $reqHit; //还要选几个中的球
				$optNhit = $optLeft - $optNeed;
				if($optNeed > $optHit || $optNhit > $optMiss)
				{
					$hits[$i] = 0;
				}
				else 
				{
					$hits[$i] = $this->CI->libcomm->combine($optHit, $optNeed) *
								$this->CI->libcomm->combine($optMiss, $optNhit);
				}
			}
		}
		return $hits;
	}

	// 计算各奖项命中的方案注数
	private function win_sd($params, $lid)
	{
		$lbinfo = $this->lbinfo[$lid];
		$fHits = $this->solveHits($lbinfo['fnum'], $params['RN'], $params['PN'], $params['RH'], $params['RT']);
		$bHits = $this->solveHits($lbinfo['bnum'], $params['BN'], $params['TN'], $params['BH'], $params['BT']);
		$result = array();
		$winners = $lbinfo['bonus'];
		$levels = count($winners);
		for ($i = 1; $i <= $levels; ++ $i)
		{
			$winner = $winners[$i];
			$count = 0;
			for ($j = 0; $j < count($winner); ++ $j)
			{
				$item = $winner[$j];
				$count += $fHits[$item[0]] * $bHits[$item[1]];
			}
			$result[$i] = $count;
		}
		return $result;
	}

	/*
 	 * 双色球 - 算奖
 	 * @version:V1.2
 	 * @date:2015-08-17
 	 */
	private function cal_ssq_bonus($bounsDetail, $awards)
	{
		$abonus = json_decode($awards['bonusDetail'], true);

		// 三、四、五、六等奖奖金固定
		$abonus['3dj']['dzjj'] = 3000;
		$abonus['4dj']['dzjj'] = 200;
		$abonus['5dj']['dzjj'] = 10;
		$abonus['6dj']['dzjj'] = 5;

		$detailInfo = array();

		$totalBonus = 0;
		$totalMargin = 0;
		foreach ($bounsDetail as $lev => $bonus)
		{
			$detail = array();
			$levBonus = 0;
			$levmargin = 0;
			foreach ($bonus as $in => $bnum)
			{
				// 税前奖级-奖金
				$levBonus += $bnum * $abonus["{$lev}dj"]['dzjj'];
				// 税后奖级-奖金
				$dzjj = $abonus["{$lev}dj"]['dzjj'] >=10000 ? $abonus["{$lev}dj"]['dzjj'] * 0.8 : $abonus["{$lev}dj"]['dzjj'];
				$levmargin += $dzjj * $bnum;

				// 累计奖金
				$totalBonus += $levBonus;
				$totalMargin += $levmargin;
			}
			$detail['zs'] = $bnum;
			$detail['jj'] = number_format($levBonus);		//税前
			array_push($detailInfo, $detail);
		}
		// 倍数默认1倍
		$mbonus['bonus'] = $totalBonus * 1;
		$mbonus['bonus'] = number_format($mbonus['bonus']);
		$mbonus['margin'] = $totalMargin * 1;
		$mbonus['margin'] = number_format($mbonus['margin']);
		return array('bonus' => $mbonus['bonus'], 'margin' => $mbonus['margin'], 'detail' => $detailInfo);
	}

}
