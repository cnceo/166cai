<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * 足球方案内容解析类
 * @author Administrator
 *
 */
class JcBuyContent
{
    private $CI;
	private $playTypes = array('SPF', 'RQSPF', 'CBF', 'JQS', 'BQC');
	private $playNames = array(
		'SPF' => '胜平负',
		'RQSPF' => '让球',
		'CBF' => '比分',
		'JQS' => '总进球',
		'BQC' => '半全场'
	);
	private $playOptions = array(
		'SPF' => array(3, 1, 0),
		'RQSPF' => array(3, 1, 0),
		'JQS' => array(0, 1, 2, 3, 4, 5, 6, 7),
		'CBF' => array('1:0', '2:0', '2:1', '3:0', '3:1', '3:2', '4:0', '4:1', '4:2', '5:0', '5:1', '5:2', '9:0', '0:0', '1:1', '2:2', '3:3', '9:9', '0:1', '0:2', '1:2', '0:3', '1:3', '2:3', '0:4', '1:4', '2:4', '0:5', '1:5', '2:5', '0:9'),
		'BQC' => array('3:3', '3:1', '3:0', '1:3', '1:1', '1:0', '0:3', '0:1', '0:0')
	);
	
    private $playOptionNames = array();
	
	public function __construct() {
	    $this->CI = &get_instance();
	    if ($this->CI->config->item('wenan') == null) $this->CI->load->config('wenan');
	    $this->wenan = $this->CI->config->item('wenan');
	    $this->playOptionNames = array(
	        'SPF' => array(
    			3 => $this->wenan['jzspf']['3'],
    			1 => $this->wenan['jzspf']['1'],
    			0 => $this->wenan['jzspf']['0']
    		),
    		'RQSPF' => array(
    			3 => $this->wenan['jzspf']['r3'],
    			1 => $this->wenan['jzspf']['r1'],
    			0 => $this->wenan['jzspf']['r0']
    		),
	        'JQS' => array(
	            7 => '7+',
	        ),
	        'CBF' => array(
	            '9:0' => '胜其他',
	            '9:9' => '平其他',
	            '0:9' => '负其他',
	        ),
	        'BQC' => array(
	            '3-3' => '胜-胜',
	            '3-1' => '胜-平',
	            '3-0' => '胜-负',
	            '1-3' => '平-胜',
	            '1-1' => '平-平',
	            '1-0' => '平-负',
	            '0-3' => '负-胜',
	            '0-1' => '负-平',
	            '0-0' => '负-负'
	        )
	    );
	}
	
	public function index($cast, $awards, $ticketInfo = array(), $versionInfo = array())
	{
		$matches = array();
		//$cast = 'HH|SPF>20150506001=3(1.97),RQSPF>20150506001=1{-1}(3.6)/3{-1}(4.1),JQS>20150506001=4(6.25),BQC>20150506001=1-1(4.3)/1-3(4.2)/3-0(38),CBF>20150506001=3:3(80)/4:2(80)/5:1(125)/9:9(500)/0:3(40),SPF>20150506002=0(1.98)/1(3.2),RQSPF>20150506002=1{1}(3.55),JQS>20150506002=4(6.5),BQC>20150506002=1-0(4.2),CBF>20150506002=3:3(80)/4:0(125)/0:3(17),SPF>20150506003=1(3.05),RQSPF>20150506003=0{1}(5.5)/1{1}(3.75),JQS>20150506003=6(26),BQC>20150506003=1-1(3.75),CBF>20150506003=4:0(125)/0:4(80)|2*1,3*1';
		//$awards = json_decode('[{"issue":"2015-05-06","spfGd":1,"rqspfGd":1,"bqcGd":1,"jqsGd":1,"bfGd":1,"spfFu":1,"rqspfFu":0,"bqcFu":1,"jqsFu":1,"bfFu":1,"mid":"20150506001","st":3,"score":"4:1","homeId":202,"homeSname":"\u798f\u5188","awary":"\u7fa4\u9a6c\u6e29\u6cc9","awarySname":"\u7fa4\u9a6c","awaryId":212,"dt":1430884800000,"jzdtflag":0,"jzdt":1430884440000,"let":-1,"cl":"#22C126","rs":null,"scoreHalf":"3:1","modifyflag":0,"ckst":1,"jyst":0,"oh":"2.19","od":"3.09","oa":"3.21","bfSp00":"8.00","bfSp01":"7.75","bfSp02":"15.00","bfSp03":"40.00","bfSp04":"125.0","bfSp05":"500.0","bfSp10":"6.00","bfSp11":"6.50","bfSp12":"10.00","bfSp13":"29.00","bfSp14":"80.00","bfSp15":"400.0","bfSp20":"8.25","bfSp21":"7.25","bfSp22":"15.00","bfSp23":"40.00","bfSp24":"125.0","bfSp25":"500.0","bfSp30":"17.00","bfSp31":"16.00","bfSp32":"29.00","bfSp33":"80.00","bfSp40":"45.00","bfSp41":"40.00","bfSp42":"80.00","bfSp50":"150.0","bfSp51":"125.0","bfSp52":"250.0","bfSp90":"150.0","bfSp91":"500.0","bfSp93":"80.00","bqcSp00":"5.25","bqcSp01":"15.00","bqcSp03":"28.00","bqcSp10":"6.20","bqcSp11":"4.20","bqcSp13":"4.50","bqcSp30":"38.00","bqcSp31":"15.00","bqcSp33":"3.35","jqsSp0":"8.00","jqsSp1":"3.65","jqsSp2":"3.15","jqsSp3":"3.75","jqsSp4":"6.25","jqsSp5":"11.50","jqsSp6":"22.00","jqsSp7":"33.00","spfSp3":"2.03","spfSp1":"3.20","spfSp0":"3.15","rqspfSp3":"4.30","rqspfSp1":"3.70","rqspfSp0":"1.60","fuBqcSp00":null,"fuBqcSp01":null,"fuBqcSp03":null,"fuBqcSp10":null,"fuBqcSp11":null,"fuBqcSp13":null,"fuBqcSp30":null,"fuBqcSp31":null,"fuBqcSp33":null,"fuJqsSp0":null,"fuJqsSp1":null,"fuJqsSp2":null,"fuJqsSp3":null,"fuJqsSp4":null,"fuJqsSp5":null,"fuJqsSp6":null,"fuJqsSp7":null,"fuSpfSp3":null,"fuSpfSp1":null,"fuSpfSp0":null,"fuRqspfSp3":null,"fuRqspfSp1":null,"fuRqspfSp0":null,"fuBqcDzjj":null,"fuJqsDzjj":null,"fuSpfDzjj":null,"fuRqspfDzjj":null,"name":"\u65e5\u672c\u4e59\u7ea7\u8054\u8d5b","id":19542,"home":"\u798f\u5188\u9ec4\u8702"},{"issue":"2015-05-06","spfGd":1,"rqspfGd":1,"bqcGd":1,"jqsGd":1,"bfGd":1,"spfFu":1,"rqspfFu":0,"bqcFu":1,"jqsFu":1,"bfFu":1,"mid":"20150506002","st":3,"score":"1:1","homeId":208,"homeSname":"\u6b67\u961c","awary":"\u4eac\u90fd\u4e0d\u6b7b\u9e1f","awarySname":"\u4e0d\u6b7b\u9e1f","awaryId":200,"dt":1430884800000,"jzdtflag":0,"jzdt":1430884440000,"let":1,"cl":"#22C126","rs":null,"scoreHalf":"1:0","modifyflag":0,"ckst":1,"jyst":0,"oh":"3.29","od":"3.13","oa":"2.12","bfSp00":"8.00","bfSp01":"6.00","bfSp02":"8.25","bfSp03":"17.00","bfSp04":"45.00","bfSp05":"150.0","bfSp10":"7.75","bfSp11":"6.50","bfSp12":"7.25","bfSp13":"16.00","bfSp14":"40.00","bfSp15":"125.0","bfSp20":"15.00","bfSp21":"10.00","bfSp22":"15.00","bfSp23":"29.00","bfSp24":"80.00","bfSp25":"250.0","bfSp30":"40.00","bfSp31":"29.00","bfSp32":"40.00","bfSp33":"80.00","bfSp40":"125.0","bfSp41":"80.00","bfSp42":"125.0","bfSp50":"500.0","bfSp51":"400.0","bfSp52":"500.0","bfSp90":"80.00","bfSp91":"500.0","bfSp93":"150.0","bqcSp00":"3.20","bqcSp01":"15.00","bqcSp03":"39.00","bqcSp10":"4.20","bqcSp11":"4.35","bqcSp13":"6.50","bqcSp30":"28.00","bqcSp31":"15.00","bqcSp33":"5.55","jqsSp0":"8.00","jqsSp1":"3.65","jqsSp2":"3.15","jqsSp3":"3.75","jqsSp4":"6.50","jqsSp5":"11.50","jqsSp6":"21.00","jqsSp7":"30.00","spfSp3":"3.26","spfSp1":"3.20","spfSp0":"1.98","rqspfSp3":"1.64","rqspfSp1":"3.55","rqspfSp0":"4.20","fuBqcSp00":null,"fuBqcSp01":null,"fuBqcSp03":null,"fuBqcSp10":null,"fuBqcSp11":null,"fuBqcSp13":null,"fuBqcSp30":null,"fuBqcSp31":null,"fuBqcSp33":null,"fuJqsSp0":null,"fuJqsSp1":null,"fuJqsSp2":null,"fuJqsSp3":null,"fuJqsSp4":null,"fuJqsSp5":null,"fuJqsSp6":null,"fuJqsSp7":null,"fuSpfSp3":null,"fuSpfSp1":null,"fuSpfSp0":null,"fuRqspfSp3":null,"fuRqspfSp1":null,"fuRqspfSp0":null,"fuBqcDzjj":null,"fuJqsDzjj":null,"fuSpfDzjj":null,"fuRqspfDzjj":null,"name":"\u65e5\u672c\u4e59\u7ea7\u8054\u8d5b","id":19549,"home":"\u5c90\u961cFC"},{"issue":"2015-05-06","spfGd":1,"rqspfGd":1,"bqcGd":1,"jqsGd":1,"bfGd":1,"spfFu":1,"rqspfFu":0,"bqcFu":1,"jqsFu":1,"bfFu":1,"mid":"20150506003","st":3,"score":"1:3","homeId":206,"homeSname":"\u6c34\u6237","awary":"\u957f\u5d0e\u822a\u6d77","awarySname":"\u957f\u5d0e\u822a\u6d77","awaryId":1959,"dt":1430884800000,"jzdtflag":0,"jzdt":1430884440000,"let":1,"cl":"#22C126","rs":null,"scoreHalf":"1:3","modifyflag":0,"ckst":1,"jyst":0,"oh":"2.85","od":"2.91","oa":"2.50","bfSp00":"6.00","bfSp01":"5.50","bfSp02":"9.50","bfSp03":"24.00","bfSp04":"80.00","bfSp05":"300.0","bfSp10":"6.25","bfSp11":"6.00","bfSp12":"8.25","bfSp13":"23.00","bfSp14":"75.00","bfSp15":"250.0","bfSp20":"11.00","bfSp21":"9.00","bfSp22":"18.00","bfSp23":"40.00","bfSp24":"125.0","bfSp25":"400.0","bfSp30":"30.00","bfSp31":"28.00","bfSp32":"50.00","bfSp33":"100.0","bfSp40":"125.0","bfSp41":"90.00","bfSp42":"150.0","bfSp50":"400.0","bfSp51":"300.0","bfSp52":"500.0","bfSp90":"150.0","bfSp91":"600.0","bfSp93":"200.0","bqcSp00":"3.90","bqcSp01":"15.00","bqcSp03":"39.00","bqcSp10":"4.80","bqcSp11":"3.75","bqcSp13":"5.70","bqcSp30":"33.00","bqcSp31":"15.00","bqcSp33":"4.75","jqsSp0":"6.00","jqsSp1":"3.20","jqsSp2":"3.05","jqsSp3":"4.00","jqsSp4":"7.70","jqsSp5":"15.50","jqsSp6":"28.00","jqsSp7":"45.00","spfSp3":"2.80","spfSp1":"3.05","spfSp0":"2.29","rqspfSp3":"1.47","rqspfSp1":"3.75","rqspfSp0":"5.50","fuBqcSp00":null,"fuBqcSp01":null,"fuBqcSp03":null,"fuBqcSp10":null,"fuBqcSp11":null,"fuBqcSp13":null,"fuBqcSp30":null,"fuBqcSp31":null,"fuBqcSp33":null,"fuJqsSp0":null,"fuJqsSp1":null,"fuJqsSp2":null,"fuJqsSp3":null,"fuJqsSp4":null,"fuJqsSp5":null,"fuJqsSp6":null,"fuJqsSp7":null,"fuSpfSp3":null,"fuSpfSp1":null,"fuSpfSp0":null,"fuRqspfSp3":null,"fuRqspfSp1":null,"fuRqspfSp0":null,"fuBqcDzjj":null,"fuJqsDzjj":null,"fuSpfDzjj":null,"fuRqspfDzjj":null,"name":"\u65e5\u672c\u4e59\u7ea7\u8054\u8d5b","id":19550,"home":"\u6c34\u6237\u8700\u8475"}]', true);
		$result = $this->parseTicket($cast, $ticketInfo);
		if(!empty($awards))
		{
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
		}
		return $this->renderResult($result, $matches, $ticketInfo, $versionInfo);
	}
	
	private function renderResult($casts, $matches, $ticketInfo, $versionInfo)
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
	
	private function renderMatch($matchId, $cast, $matchInfo, $ticketInfo, $versionInfo) 
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
		$tpl .= '<td rowspan="'.$rowspan.'">' . $midData . '<br>' . date('m-d', $matchInfo['dt']/1000).'<b>' . date('H:i', $matchInfo['dt']/1000) . '</b></td>';
		$tpl .= '<td rowspan="' . $rowspan . '"' . 'class="jcMatchLive" data-index="' . $matchInfo['jcMid'] . '"' . '>';
		$tpl .= ($versionInfo['appVersionCode'] >= 2080001) ? '<a href="javascript:;">' : '';
		$tpl .= $matchInfo['home'];
		$tpl .= '<b>' . $score . '</b>';
		$tpl .= $matchInfo['away'];
		$tpl .= ($versionInfo['appVersionCode'] >= 2080001) ? '</a>' : '';
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
			if($playType == 'RQSPF' && !empty($cast['let']) && $cast['let'] !== '-')
			{
				// $let = intval($cast['let']) > 0 ? '+' . intval($cast['let']) : intval($cast['let']);
				$tpl .= '('. $cast['let'] .')';
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
	        	$tpl .= '<td>'.$this->_renderOptions($playType, $cast[$playType], $cast['let'], $matchInfo, $ticketData).'</td>';
	        }
			$tpl .= '</tr>';
			
			$playCount += 1;
		}
		return array('tpl' => $tpl, 'exception' => $exception);
	}
	
	private function _renderOptions($playType, $options, $let, $matchInfo, $ticketData)
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

                if($playType == 'RQSPF')
                {
                	$letArr = explode('&', $let);
                	foreach ($letArr as $letVal) 
                	{
                		$isRight = $this->_getCastOption($playType, $matchInfo['score'], $letVal, $option, $matchInfo['halfScore']);
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
                else
                {
                	$isRight = $this->_getCastOption($playType, $matchInfo['score'], $let, $option, $matchInfo['halfScore']);
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
				if($playType == 'RQSPF')
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
	
	private function _getCastName($playType, $cast) 
	{
		if (isset($this->playOptionNames[$playType][$cast])) 
		{
			return $this->playOptionNames[$playType][$cast];
		} 
		else 
		{
			return $cast;
		}
	}
	
	private function _getCastOption($playType, $score, $let, $option, $halfScore)
	{
		$scores = explode(':', $score);
		$homeScore = intval($scores[0]);
		$awayScore = intval($scores[1]);
		$result = null;
		$gap = 0;
		if ($playType == 'SPF') 
		{
			$result = $this->_parseScore($homeScore, $awayScore, 0);
		} 
		else if ($playType == 'RQSPF') 
		{
			$result = $this->_parseScore($homeScore, $awayScore, $let);
		} 
		else if ($playType == 'JQS') 
		{
			$result = $homeScore + $awayScore;
            $result = ($result > 7) ? 7 : $result;
		} 
		else if ($playType == 'CBF') 
		{
            $result = $homeScore . ':' . $awayScore;
            if ( !in_array($result, $this->playOptions[$playType]) ) 
            {
                if ($homeScore > $awayScore) 
                {
                    $result = '9:0';
                } 
                else if ($homeScore === $awayScore) 
                {
                    $result = '9:9';
                } 
                else 
                {
                    $result = '0:9';
                }
            }
        } 
        else if ($playType == 'BQC') 
        {
        	$halfscores = explode(':', $halfScore);
            $halfHome = intval($halfscores[0]);
            $halfAway = intval($halfscores[1]);
            $halfResult = $this->_parseScore($halfHome, $halfAway, 0);
            $fullResult = $this->_parseScore($homeScore, $awayScore, 0);
            $result = $halfResult . '-' . $fullResult;
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
	
	private function parseTicket($tickets, $ticketInfo)
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
						'let' => 0
					);
				}
				$pkeys = array_keys($cast[$matchId]);
				if(!(in_array($playType, $pkeys)))
				{
					$cast[$matchId][$playType] = array();
					$cast[$matchId]['play'] += 1;
				}
				$rule = '/([\d\-\:]*)(?:\{(.*)\})?\(?(\d*\.?\d*)?\)?/is';
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
							if ($playType == 'RQSPF')
							{
								$cast[$matchId]['let'] = $pks; // $matchs[2];
							}
							
							$cast[$matchId][$playType][] = $option;
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