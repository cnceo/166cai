<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * 上海快三投注方案解析
 * @author Liuli
 *
 */
class Ks
{
	private $me = array(
		'KS' => '53',
		'JLKS' => '56',
	    'JXKS' => '57',
	);

	public function index($cast, $awards)
	{
		// $cast = '3,6,18:1:1;0,0,0:2:1;1,1,1:3:1;4,4,4:3:1;0,0,0:5:1;2,2,*:6:1;5,5,*:6:1;1,1,4:7:1;6,6,3:7:1;1,2,*:8:1;5,6,*:8:1';
		$datas = array();
		$award = $this->parseAward($awards['awardNumber']);
		$datas['atpl'] = $this->renderAward($awards['seLotid'], $award);
		$datas['tpl'] = $this->renderCast($cast, $awards['seLotid'], $award);
		return $datas;
	}
	
	private function renderCast($cast, $lotteryId, $award)
	{
		// $cast = '5,6,*:8:1';
		$ctpl = '';
		$casts = explode(';', $cast);
		foreach ($casts as $val)
		{		
			$castHtml = $this->castToTpl($lotteryId, $val, $award);
			$ctpl .= '<dd>';
			// 玩法显示
			if(BetCnName::$playTypeCnName[$lotteryId][$castHtml['playTypeCode']])
			{			
				$ctpl .= '<span class="bet-detail-tag"><small>' . BetCnName::$playTypeCnName[$lotteryId][$castHtml['playTypeCode']] . '</small></span>';
			}
			$ctpl .= '<div class="num-group">';
			$castTpl = $castHtml['preTpl'];
            if ($castHtml['postTpl'])
			{
                $castTpl .= $this->renderGrayDetail(':', 2) . $castHtml['postTpl'];
            }
			$ctpl .= $castTpl . '</div></dd>';
		}
		return $ctpl;
	}

	// 解析单注投注串内容
	private function castToTpl($lotteryId, $cast, $award)
	{
		$parsedCast = $this->parseCast($lotteryId, $cast);
		$parsedAward = $award;

		$preTpl = '';
        $postTpl = '';
        $preDan = '';
        $postDan = '';
		$castPre = array_merge_recursive($parsedCast['preDan'], $parsedCast['preTuo']);

		$playTypeCode = intval($parsedCast['playTypeCode']);
		$modeCode = $parsedCast['modeCode'];

		if( $playTypeCode == '1' )
		{	
			// 和值
			foreach($castPre as $key => $number)
			{
				if($number == $parsedAward['sum'])
				{
					$preTpl .= $this->renderRedDetail($number);
				}
				else
				{
					$preTpl .= $this->renderGrayDetail($number);
				}
			}	
		}
		elseif( $playTypeCode == '2' )
		{
			// 三同号通选
			if(!empty($parsedAward['preCode']) && $parsedAward['preCode'][0] == $parsedAward['preCode'][1] && $parsedAward['preCode'][1] == $parsedAward['preCode'][2])
			{
				$preTpl .= $this->renderRedTxtDetail('三同号通选');
			}
			else
			{
				$preTpl .= $this->renderGrayTxtDetail('三同号通选');
			}
		}
		elseif( $playTypeCode == '3' )
		{
			// 三同号单选
			$number = implode('', $castPre);
			if($parsedAward['preCode'][0] == $parsedAward['preCode'][1] && $parsedAward['preCode'][1] == $parsedAward['preCode'][2])
			{
				if( $castPre[0] == $parsedAward['preCode'][0])
				{
					$preTpl .= $this->renderRedDetail($number);
				}
				else
				{
					$preTpl .= $this->renderGrayTxtDetail($number);
				}
			}
			else
			{
				$preTpl .= $this->renderGrayTxtDetail($number);
			}	
		}
		elseif( $playTypeCode == '4' )
		{
			// 三不同号
			$number = implode('', $castPre);
			$awardNumber = implode('', $parsedAward['preCode']);
			if($number == $awardNumber)
			{
				$preTpl .= $this->renderRedDetail($number);
			}
			else
			{
				$preTpl .= $this->renderGrayDetail($number);
			}
		}
		elseif( $playTypeCode == '5' )
		{
			// 三连号通选
			$number = implode('', $castPre);
			asort($parsedAward['preCode']);
			$max = end($parsedAward['preCode']);

			if($parsedAward['preCode'][0] == $parsedAward['preCode'][1] || $parsedAward['preCode'][1] == $parsedAward['preCode'][2] || $parsedAward['preCode'][0] == $parsedAward['preCode'][2])
			{
				$preTpl .= $this->renderGrayTxtDetail('三连号通选');
			}
			else
			{
				if( $parsedAward['sum'] > 0 && ($max * 3 - $parsedAward['sum'] <= 3) )
				{
					$preTpl .= $this->renderRedTxtDetail('三连号通选');
				}
				else
				{
					$preTpl .= $this->renderGrayTxtDetail('三连号通选');
				}
			}		
		}
		elseif( $playTypeCode == '6' )
		{
			// 二同号复选
			$number = implode('', $castPre);
			asort($parsedAward['preCode']);
			$awardNumber = implode('', $parsedAward['preCode']);
			$numberStr = str_replace('*', '', $number);

			if(strpos($awardNumber, $numberStr) !== FALSE)
			{
				$preTpl .= $this->renderRedDetail($number);
			}
			else
			{
				$preTpl .= $this->renderGrayDetail($number);
			}
		}
		elseif( $playTypeCode == '7' )
		{
			// 二同号单选
			$number = implode('', $castPre);
			// 排序
			asort($castPre);
			asort($parsedAward['preCode']);
			$numberStr = implode('', $castPre);
			$awardNumber = implode('', $parsedAward['preCode']);

			if($numberStr == $awardNumber)
			{
				$preTpl .= $this->renderRedDetail($number);
			}
			else
			{
				$preTpl .= $this->renderGrayDetail($number);
			}
		}
		elseif( $playTypeCode == '8' )
		{
			// 二不同号
			$number = implode('', $castPre);
			$numberStr = str_replace('*', '', $number);
			if(!empty($parsedAward['preCode']))
			{
				if(in_array($castPre[0], $parsedAward['preCode']) && in_array($castPre[1], $parsedAward['preCode']))
				{
					$preTpl .= $this->renderRedDetail($numberStr);
				}
				else
				{
					$preTpl .= $this->renderGrayDetail($numberStr);
				}
			}
			else
			{
				$preTpl .= $this->renderGrayDetail($numberStr);
			}
		}

		$preTpl = $preDan . $preTpl;
		$postTpl = $postDan .  $postTpl;

		// $preTpl = '[' . $parsedCast['playType'] . ']' . $preTpl;

		return array(
			'preTpl' => $preTpl,
			'postTpl' => $postTpl,
			'playTypeCode' => $playTypeCode,
			'modeCode' => $modeCode,
			'dfsType' => $dfsType
		);
	}
	
	private function parseCast($lotteryId, $code)
	{
		$parts = explode(':', $code);
		$numbers = $parts[0];
		$playType = $parts[1];
		$hasDan = strpos($code, '$') > 0;
		$hasPost = strpos($code, '|') > 0;
		$preDan = array();
		$preTuo = explode(',', $numbers);
		$postDan = array();
		$postTuo = array();

		return array(
			'playTypeCode' => $parts[1],
			'modeCode' => $parts[2],
			'preDan' => $preDan,
			'preTuo' => $preTuo,
			'postDan' => $postDan,
			'postTuo' => $postTuo
		);
	}
	
	private function renderAward($lotteryId, $award)
	{
		$atpl = '';
		if($award['hasAward'])
		{
			foreach ($award['preCode'] as $number)
			{
				$atpl .= "<span>{$number}</span>";
			}
			foreach ($award['postCode'] as $number)
			{
				$atpl .= '<span class="blue-ball">' . $number . '</span>';
			}
		}
		else
		{
			$atpl = '<span>?</span><span>?</span><span>?</span>';
		}
		
		return $atpl;
	}
	
	private function parseAward($code)
	{
		$preCode = array();
		$postCode = array();
		$hasAward = false;
		$sum = 0;
		if ($code)
		{
			$code .= '';
			$hasAward = true;
			$code = preg_replace('/\s+/is', ',', $code);
			$hasPost = strpos($code, ':') > 0 ? true : false;
			$parts = explode(':', $code);
			$preCode = explode(',', $parts[0]);
			if ($hasPost) 
			{
				$postCode = explode(',', $parts[1]);
			}

			// 和值
			foreach ($preCode as $key => $number) 
			{
				$sum = $sum + $number;
			}
		}
		
		$award = array(
			'hasAward' => $hasAward,
			'preCode' => $preCode,
			'postCode' => $postCode,
			'sum' => $sum
		);
		
		return $award;
	}

	private function renderRedDetail($num)
	{
		if(is_numeric($num))
		{
			return '<span class="bingo num-txt">' . $num . '</span>';
		}
		else
		{
			$tpl = '<span>';
			if(strpos($num, '(') !== FALSE)
			{
				$tpl .= '(';
			}
			$number = str_replace(array('(',')'), array('', ''), $num);
			$tpl .= '<i class="bingo">' . $number . '</i>';
			if(strpos($num, ')') !== FALSE)
			{
				$tpl .= ')';
			}
			$tpl .= '</span>';
			return $tpl;
		}	
	}

	// 文字
	private function renderRedTxtDetail($txt)
	{
		return '<span class="num-txt bingo">' . $txt . '</span>';
	}

	private function renderGrayDetail($num ,$class = '')
	{
		if(!empty($class))
		{
			if($class == 1)
			{
				return '<span class="symbol-line">' .$num. '</span>';
			}
			elseif($class == 3) 
			{
				return '<span class="symbol-bracket">' .$num. '</span>';
			}
			else
			{
				return '<span class="symbol-colon">' .$num. '</span>';
			}
		}
		else
		{
			return '<span class="num-txt">' .$num. '</span>';
		}		
	}

	// 文字
	private function renderGrayTxtDetail($txt)
	{
		return '<span class="num-txt">' . $txt . '</span>';
	}

	private function renderBlueDetail($num)
	{
		// class="blue-ball"
		return '<span class="bingo special-color">' . $num . '</span>';
	}

}