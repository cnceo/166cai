<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
/**
 * 竞彩篮球投注内容解析类
 * @author Administrator
 *
 */
class JclqBuyContent
{
    private $CI;
	private $playTypes = array('SF', 'RFSF', 'DXF', 'SFC');
	private $playNames = array(
		'SF' => '胜负',
		'RFSF' => '让分',
		'DXF' => '大小分',
		'SFC' => '胜分差'
	);
	private $playOptions = array(
		'SPF' => array(3, 0),
		'RQSPF' => array(3,  0),
		'DXF' => array(3, 0),
		'SFC' => array('01', '02', '03', '04', '05', '06', '11', '12', '13', '14', '15', '16')
	);
	
    private $playOptionNames = array();
	
	public function __construct() {
	    $this->CI = &get_instance();
	    if ($this->CI->config->item('wenan') == null) $this->CI->load->config('wenan');
	    $this->wenan = $this->CI->config->item('wenan');
	    $this->playOptionNames = array(
	        'SF' => array(
	            3 => $this->wenan['jlsf']['3'],
	            0 => $this->wenan['jlsf']['0']
	        ),
	        'RFSF' => array(
	            3 => $this->wenan['jlsf']['r3'],
	            0 => $this->wenan['jlsf']['r0']
	        ),
	        'DXF' => array(
	            3 => '大分',
	            0 => '小分'
	        ),
	        'SFC' => array(
	            '01' => $this->wenan['jlsf']['3']."1-5",
	            '02' => $this->wenan['jlsf']['3']."6-10",
	            '03' => $this->wenan['jlsf']['3']."11-15",
	            '04' => $this->wenan['jlsf']['3']."16-20",
	            '05' => $this->wenan['jlsf']['3']."21-25",
	            '06' => $this->wenan['jlsf']['3']."26+",
	            '11' => $this->wenan['jlsf']['0']."1-5",
	            '12' => $this->wenan['jlsf']['0']."6-10",
	            '13' => $this->wenan['jlsf']['0']."11-15",
	            '14' => $this->wenan['jlsf']['0']."16-20",
	            '15' => $this->wenan['jlsf']['0']."21-25",
	            '16' => $this->wenan['jlsf']['0']."26+"
	        )
	    );
	}
	
	public function index($cast, $awards, $ticketInfo = array(), $versionInfo = array())
	{
		$matches = array();
		//$cast = 'HH|SF>20150506301=0(2.6)/3(1.31),RFSF>20150506301=0{-5.5}(1.79)/3{-5.5}(1.7),SFC>20150506301=13(11.5)/14(27)/03(5)/04(8.35)/06(17),DXF>20150506301=0(1.75)/3(1.75),SF>20150506302=0(2.88)/3(1.25),RFSF>20150506302=0{-6.5}(1.69)/3{-6.5}(1.8),SFC>20150506302=13(12.5)/15(70)/05(11.5)/04(7.25)/03(4.6),DXF>20150506302=0(1.75)/3(1.75)|2*1';
		//$awards = json_decode('[{"issue":"2015-05-06","rfsfGd":1,"sfGd":1,"sfcGd":1,"dxfGd":1,"mid":"20150506301","st":3,"score":"91:106","awary":"\u516c\u725b","dt":1430953200000,"jzdtflag":0,"jzdt":1430926500000,"let":"-5.50","cl":"#006BBB","modifyflag":0,"sfcHs2125":"12.50","sfcHs26":"17.00","sfcAs15":"5.35","sfcAs610":"6.75","sfcAs1115":"11.50","sfcAs1620":"27.00","sfcAs2125":"60.00","sfcAs26":"80.00","score1":null,"score2":null,"score3":null,"score4":null,"preScore":"+194.50","sfHs":"1.31","sfHf":"2.60","rfsfHs":"1.70","rfsfHf":"1.79","sfcHs15":"3.80","sfcHs610":"3.45","sfcHs1115":"5.00","sfcHs1620":"8.35","dxfBig":"1.70","dxfSmall":"1.79","sfFu":0,"rfsfFu":1,"sfcFu":1,"dxfFu":1,"ckst":1,"jyst":0,"oh":null,"od":null,"oa":null,"name":"NBA","id":2738,"home":"\u9a91\u58eb"},{"issue":"2015-05-06","rfsfGd":1,"sfGd":1,"sfcGd":1,"dxfGd":1,"mid":"20150506302","st":3,"score":"109:115","awary":"\u5feb\u8239","dt":1430962200000,"jzdtflag":0,"jzdt":1430960700000,"let":"-7.50","cl":"#006BBB","modifyflag":0,"sfcHs2125":"10.50","sfcHs26":"14.00","sfcAs15":"5.90","sfcAs610":"7.55","sfcAs1115":"14.50","sfcAs1620":"29.00","sfcAs2125":"70.00","sfcAs26":"95.00","score1":null,"score2":null,"score3":null,"score4":null,"preScore":"+214.50","sfHs":"1.17","sfHf":"3.40","rfsfHs":"1.75","rfsfHf":"1.75","sfcHs15":"4.40","sfcHs610":"3.30","sfcHs1115":"4.35","sfcHs1620":"7.00","dxfBig":"1.75","dxfSmall":"1.75","sfFu":0,"rfsfFu":1,"sfcFu":1,"dxfFu":1,"ckst":1,"jyst":0,"oh":null,"od":null,"oa":null,"name":"NBA","id":2739,"home":"\u4f11\u65af\u6566\u706b\u7bad"}]', true);
		$result = $this->parseTicket($cast, $ticketInfo);
		foreach ($awards as $award)
		{
			$matches[$award['mid']] = array(
				'home' 		=> 	$award['home'],
				'away' 		=> 	$award['awary'],
				'score'		=> 	$award['score'],
				'halfScore'	=> 	$award['scoreHalf'],
				'dt' 		=> 	$award['dt'],
				'issue' 	=> 	$award['issue'],
				'jcMid' 	=> 	$award['jcMid'],
				'm_status'	=>	$award['m_status'] ? $award['m_status'] : 0,
				'cstate'	=>	$award['cstate'] ? $award['cstate'] : 0,
			);
		}
		return $this->renderResult($result, $matches, $ticketInfo, $versionInfo);
	}
	
	public function renderResult($casts, $matches, $ticketInfo, $versionInfo)
	{
		$tpl = '';
		$exception = array();
		foreach ($casts as $mid => $cast)
		{
			$render = $this->renderMatch($mid, $cast, $matches[$mid], $ticketInfo, $versionInfo);
			$tpl .= $render['tpl'];
			if(!empty($render['exception']))
			{
				array_push($exception, $render['exception']);
			}
		}
		return array('tpl' => $tpl, 'exception' => $exception);
	}
	
	public function renderMatch($matchId, $cast, $matchInfo, $ticketInfo, $versionInfo) 
	{
		$ticketData = array();
		$midData = $this->getWeekByTime(strtotime($matchInfo['issue'])) . substr($matchId, 8);
		// 取消或延期处理
		$exception = array();
		if($matchInfo['score'])
		{
			$score = $matchInfo['score'];
		}
		else
		{
			if($matchInfo['m_status'] > 0)
			{
				$score = '<em class="warning">取消</em>';
				$exception = array(
					'ctype'	=>	'2',
					'match'	=>	$midData,
				);
			}
			elseif(($matchInfo['cstate'] & 1) == 1)
			{
				$score = '<em class="warning">延期</em>';
				$exception = array(
					'ctype'	=>	'1',
					'match'	=>	$midData,
				);
			}
			else
			{
				$score = 'vs';
			}
		}
		$rowspan = $cast['play'];
		$tpl = '<tr>';
		$tpl .= '<td rowspan="'.$rowspan.'">' . $midData . '<br/>'.date('m-d', $matchInfo['dt']/1000) . '<b>' . date('H:i', $matchInfo['dt']/1000) . '</b></td>';
		$tpl .= '<td rowspan="'. $rowspan . '"' . 'class="jcMatchLive" data-index="' . $matchInfo['jcMid'] . '"' . '>';
		$tpl .= ($versionInfo['appVersionCode'] >= 2090001) ? '<a href="javascript:;">' : '';
		$tpl .= $matchInfo['away'];
		$tpl .= '<b>' . $score . '</b>';
		$tpl .= $matchInfo['home'];
		$tpl .= ($versionInfo['appVersionCode'] >= 2090001) ? '</a>' : '';
		$tpl .= '</td>';
		$pts = array_keys($cast);
		$playCount = 0;
		foreach ($this->playTypes as $playType)
		{
			if(!in_array($playType, $pts))
			{
				continue;
			}
			if ($playCount > 0) {
				$tpl .= '<tr>';
			}
			$tpl .= '<td>'.$this->playNames[$playType];
			if($playType == 'RFSF' && !empty($cast['let']) && $cast['let'] !== '-')
			{
				$let = $cast['let'];
				$tpl .= '('. $let .')';
			}
			if($playType == 'DXF' && !empty($cast['preScore']) && $cast['preScore'] !== '-')
			{
				$cast['preScore'] = str_replace('+', '', $cast['preScore']);
				$tpl .= '('. $cast['preScore'] .')';
			}
			$tpl .= '</td>';
			// 获取实际开奖赔率信息
	        if(!empty($ticketInfo))
	        {
	            $ticketData = $ticketInfo[$matchId][$playType];
	        }
	        // 场次取消
	        if($matchInfo['m_status'] > 0)
	        {
	        	$tpl .= '<td><b><span class="bingo">比赛取消（1.00）</span></b></td>';
	        }
	        else
	        {
	        	$tpl .= '<td>'.$this->_renderOptions($playType, $cast[$playType], $cast['let'], $matchInfo, $cast['preScore'], $ticketData).'</td>';
	        }
			$tpl .= '</tr>';
			
			$playCount += 1;
		}
		return array('tpl' => $tpl, 'exception' => $exception);
	}
	
	private function _renderOptions($playType, $options, $let, $matchInfo, $preScore, $ticketData)
	{
		$tpl = '';
		$isRight = false;
		$castName = '';
		$casts = array();
		foreach ($options as $option)
		{
			if(in_array($option['cast'], $casts))
			{
				return ;
			}
			if ($matchInfo['score']) 
			{
				$isRight = false;
                $getRight = 0;
                // 命中的赔率
                $rightOdds = array();
                // 让分胜负let 大小分根据preScore
                if($playType == 'RFSF')
                {
                	$letArr = explode('&', $let);
                	foreach ($letArr as $letVal) 
                	{
                		$isRight = $this->_getCastOption($playType, $matchInfo['score'], $letVal, $option, $matchInfo['halfScore'], $preScore);
                		if($isRight && !empty($ticketData[$letVal][$option['cast']]))
                		{
                			foreach ($ticketData[$letVal][$option['cast']] as $val) 
                			{
                				array_push($rightOdds, $val);
                			}
                			$getRight = $getRight + 1;
                		}
                	}
                }
                elseif($playType == 'DXF')
                {
                	$preScoreArr = explode('&', $preScore);
                	foreach ($preScoreArr as $preScoreVal) 
                	{
                		$isRight = $this->_getCastOption($playType, $matchInfo['score'], $let, $option, $matchInfo['halfScore'], $preScoreVal);
                		if($isRight && !empty($ticketData[$preScoreVal][$option['cast']]))
                		{
                			foreach ($ticketData[$preScoreVal][$option['cast']] as $val) 
                			{
                				array_push($rightOdds, $val);
                			}
                			$getRight = $getRight + 1;
                		}
                	}
                }
                else
                {
                	$isRight = $this->_getCastOption($playType, $matchInfo['score'], $let, $option, $matchInfo['halfScore'], $preScore);
                	if($isRight)
                	{
                		$getRight = 1;
                	}
                }	
			}
			$castName = $this->_getCastName($playType, $option['cast']);
			if($getRight > 0)
			{
				$tpl .= '<b><span class="bingo">' . $castName . '</span>';
			}
			else
			{
				$tpl .= '<b>' . $castName;
			}
			if ($option['odd'])
			{
				if(in_array($playType, array('RFSF', 'DXF')))
				{
					$tpl .= '(';
					$optionOdds = explode('&', $option['odd']);
					foreach ($optionOdds as $k => $odds) 
					{
						if(!empty($rightOdds) && in_array($odds, $rightOdds))
						{
							$tpl .= '<span class="bingo">' . $odds . '</span>';
						}
						else
						{
							$tpl .= $odds;
						}
						if($k < count($optionOdds) - 1)
						{
                            $tpl .= '&';
                        }
					}
					$tpl .= ')';
				}
				else
				{
					if($getRight > 0)
					{
						$tpl .= '<span class="bingo">(' . $option['odd'] . ')<span>';
					}
					else
					{
						$tpl .= '(' . $option['odd'] . ')';
					}
				}
			}
			$tpl .= '</b>';
			array_push($casts, $option['cast']);
		}	
		return $tpl;
	}
	
	private function _getCastName($playType, $cast) {
		if (isset($this->playOptionNames[$playType][$cast])) 
		{
			return $this->playOptionNames[$playType][$cast];
		} 
		else 
		{
			return $cast;
		}
	}
	
	private function _getCastOption($playType, $score, $let, $option, $halfScore, $preScore)
	{
		$scores = explode(':', $score);
		$awayScore = intval($scores[0]);
		$homeScore = intval($scores[1]);
		$result = null;
		$gap = 0;
		if ($playType == 'SF') 
		{
			$result = $this->_parseScore($homeScore, $awayScore, 0);
		} 
		else if ($playType == 'RFSF') 
		{
			$result = $this->_parseScore($homeScore, $awayScore, $let);
		} 
		else if ($playType == 'DXF') 
		{
			$result = ($homeScore + $awayScore > $preScore) ? '3' : '0';
		} 
		else if ($playType == 'SFC') 
		{
			if ($homeScore >= $awayScore) 
			{
				$result = '0';
				$gap = $homeScore - $awayScore;
			} 
			else 
			{
				$result = '1';
				$gap = $awayScore - $homeScore;
			}
			if ($gap >=1 && $gap <= 5) 
			{
				$result .= '1';
			} 
			else if ($gap >= 6 && $gap <= 10) 
			{
				$result .= '2';
			} 
			else if ($gap >= 11 && $gap <= 15) 
			{
				$result .= '3';
			} 
			else if ($gap >= 16 && $gap <= 20) 
			{
				$result .= '4';
			} 
			else if ($gap >= 21 && $gap <=25) 
			{
				$result .= '5';
			} 
			else if ($gap >= 26) 
			{
				$result .= '6';
			}
		}
		return $result == $option['cast'];
	}
	
	private function _parseScore($homeScore, $awayScore, $let) 
	{
		$homeScore += $let;
		if ($homeScore > $awayScore) 
		{
			return 3;
		} 
		else if ($homeScore === $awayScore) 
		{
			return 1;
		} 
		else 
		{
			return 0;
		}
	}
	
	public function parseTicket($tickets, $ticketInfo)
	{
		$cast = array();
		if (!$tickets) 
		{
			return $cast;
		}
		$tickets = explode(';', $tickets);
		foreach ($tickets as $ticket)
		{
			$parts = explode('|', $ticket);
			$playType = $parts[0];
			$codes = $parts[1];
			$passWays = $parts[2];
			$codesArr = explode(',', $codes);
			foreach ($codesArr as $v)
			{
				$detail = explode('=', $v);
				if(strpos($v, '>') === false)
				{
					$playType = $parts[0];
					$matchId = $detail[0];
				}
				else
				{
					$tmp = explode('>', $detail[0]);
					$playType = $tmp[0];
					$matchId = $tmp[1];
				}
				$options = explode(',', $detail[1]);
				$mkeys = array_keys($cast);
				if(!(in_array($matchId, $mkeys)))
				{
					$cast[$matchId] = array(
						'play' => 0,
						'let' => 0,
						'preScore' => 0
					);
				}
				$pkeys = array_keys($cast[$matchId]);
				if(!(in_array($playType, $pkeys)))
				{
					$cast[$matchId][$playType] = array();
					$cast[$matchId]['play'] += 1;
				}
				$rule = '/([\d\-\:]*)(?:\{(.*)\})?\(?(\d*\.?\d*)?\)?(?:\{(.*)\})?/is';
				foreach ($options as $vs)
				{
					$vTmp = explode('/', $vs);
					foreach ($vTmp as $v)
					{
						preg_match($rule,$v,$matchs);
						if($matchs)
						{
							// 实际出票赔率
                            $pl = '';
                            $plArr = array();
                            $pks = '';
                            $pksArr = array();

                            if(!empty($ticketInfo))
                            {
                            	foreach ($ticketInfo[$matchId][$playType] as $pk => $items) 
                            	{
                            		if($pk != '-')
                            		{
                            			array_push($pksArr, $pk);
                            		}
                            		foreach ($ticketInfo[$matchId][$playType][$pk] as $fa => $plArrs) 
                            		{
                            			if(strpos($fa, $matchs[1]) > -1)
                            			{
                            				foreach ($plArrs as $key => $val) 
                            				{
                            					if(!in_array($val, $plArr))
		                            			{
		                            				array_push($plArr, $val);
		                            			}
                            				}
                            			}
                            		}
                            	}
                            	$pl = implode('&', $plArr);
                                $pks = implode('&', $pksArr);
                            }
                            else
                            {
                            	$pl = '';
                                $pks = '-';
                            }

							$option = array(
								'cast' => $matchs[1],
								'odd' => $pl // $matchs[3]
							);
							if ($playType == 'RFSF')
							{
								$cast[$matchId]['let'] = $pks; // $matchs[2];
							}
							if($playType == 'DXF') {
								$cast[$matchId]['preScore'] = $pks; // $matchs[4];
                            }
							
							if(!in_array($option, $cast[$matchId][$playType])) $cast[$matchId][$playType][] = $option;
						}
					}
				}
			}
			
		}
		return $cast;
	}
	
	private function getWeekByTime($time)
	{
		$weekarray = array("日","一","二","三","四","五","六");
		return "周".$weekarray[date("w",$time)];
	}
}