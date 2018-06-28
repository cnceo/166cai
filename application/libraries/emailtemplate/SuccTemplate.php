<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * 出票成功邮件模板
 * @author shigx
 *
 */
class SuccTemplate
{
	private $playTypes = array('SPF', 'RQSPF', 'CBF', 'JQS', 'BQC', 'SF', 'RFSF', 'DXF', 'SFC');
	private $playOptionNames = array();
	private $ruleMap = array(
		'42' => array(
			'0' => '/([\d\-\:]*)(?:\{(.*)\})?\(?(\d*\.?\d*)?\)?/is',
			'6' => '/([\d\-\:]*)(?:\{(.*)\})?\(?(\d*\.?\d*)?\)?@(\d*)?/is',
		),
		'43' => array(
			'0' => '/([\d\-\:]*)(?:\{(.*)\})?\(?(\d*\.?\d*)?\)?(?:\{(.*)\})?/is',
		),
	);
	private $passWay = '';
	private $CI;
	public function __construct()
	{
		$this->CI = &get_instance();
		$url_prefix = $this->CI->config->item('url_prefix');
		$this->url_prefix = isset($url_prefix[$this->CI->config->item('domain')]) ? $url_prefix[$this->CI->config->item('domain')] : 'http';
		$this->CI->load->library('BetCnName');
		$this->CI->load->model('order_model');
		$this->CI->load->config('wenan');
		$wenan = $this->CI->config->item('wenan');
		$this->playOptionNames = array(
		    'SPF' => array(
		        3 => $wenan['jzspf']['3'],
		        1 => $wenan['jzspf']['1'],
		        0 => $wenan['jzspf']['0']
		    ),
		    'RQSPF' => array(
		        3 => $wenan['jzspf']['r3'],
		        1 => $wenan['jzspf']['r1'],
		        0 => $wenan['jzspf']['r0']
		    ),
		    'JQS' => array(
    			0 => '0',
    			1 => '1',
    			2 => '2',
    			3 => '3',
    			4 => '4',
    			5 => '5',
    			6 => '6',
    			7 => '7+',
    		),
    		'CBF' => array(
    			'1:0' => '1:0',
    			'2:0' => '2:0',
    			'2:1' => '2:1',
    			'3:0' => '3:0',
    			'3:1' => '3:1',
    			'3:2' => '3:2',
    			'4:0' => '4:0',
    			'4:1' => '4:1',
    			'4:2' => '4:2',
    			'5:0' => '5:0',
    			'5:1' => '5:1',
    			'5:2' => '5:2',
    			'0:0' => '0:0',
    			'1:1' => '1:1',
    			'2:2' => '2:2',
    			'3:3' => '3:3',
    			'0:1' => '0:1',
    			'0:2' => '0:2',
    			'1:2' => '1:2',
    			'0:3' => '0:3',
    			'1:3' => '1:3',
    			'2:3' => '2:3',
    			'0:4' => '0:4',
    			'1:4' => '1:4',
    			'2:4' => '2:4',
    			'0:5' => '0:5',
    			'1:5' => '1:5',
    			'2:5' => '2:5',
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
    		),
		    'SF' => array(
		        3 => $wenan['jlsf']['3'],
		        0 => $wenan['jlsf']['0']
		    ),
		    'RFSF' => array(
		        3 => $wenan['jlsf']['r3'],
		        0 => $wenan['jlsf']['r0']
		    ),
		    'DXF' => array(
				3 => '大分',
				0 => '小分'
		    ),
		    'SFC' => array(
				'01' => $wenan['jlsf']['3']."1-5",
				'02' => $wenan['jlsf']['3']."6-10",
				'03' => $wenan['jlsf']['3']."11-15",
				'04' => $wenan['jlsf']['3']."16-20",
				'05' => $wenan['jlsf']['3']."21-25",
				'06' => $wenan['jlsf']['3']."26+",
				'11' => $wenan['jlsf']['0']."1-5",
				'12' => $wenan['jlsf']['0']."6-10",
				'13' => $wenan['jlsf']['0']."11-15",
				'14' => $wenan['jlsf']['0']."16-20",
				'15' => $wenan['jlsf']['0']."21-25",
				'16' => $wenan['jlsf']['0']."26+"
		    )
		);
	}
	
	public function index($order)
	{
		$ticketDetail = $this->parseTicketDetail($order);
		$order['ticket_time'] = $ticketDetail[1];
		return $this->parseTemplate($order, $ticketDetail[0]);
	}
	
	/**
	 * 出票明细解析
	 * @param unknown_type $order
	 */
	private function parseTicketDetail($order)
	{
		if (in_array($order['lid'], array(JCZQ, JCLQ)))
		{
			return $this->jcParse($order);
		}
		elseif (in_array($order['lid'], array(SFC, RJ)))
		{
			return $this->lzcParse($order);
		}
		elseif (in_array($order['lid'], array(GJ, GYJ)))
		{
		    return $this->gjcParse($order['orderId'], $order['status']);
		}
		else
		{
			return $this->numberParse($order['orderId'], $order['lid']);
		}
	}
	
	/**
	 * 竞技彩出票方案解析
	 * @param unknown_type $order
	 */
	private function jcParse($order)
	{
		// 获取实际出票赔率
		$ticketInfo = $this->CI->order_model->getJjcOrderDetail($order['orderId']);
		// 解析盘口赔率
		$ticketInfo = $this->parseTicketInfo($ticketInfo);
		$result = $this->parseTicket($order, $ticketInfo);
		ksort($result);
		//获取比赛对阵信息
		$matchInfo = $this->CI->order_model->getJjcMatchDetail($order['lid'], $order['codecc']);
		$tickettime = $this->CI->order_model->getMaxTickettime($order['orderId']);
		$tickettime = $tickettime['ticket_time'];
		$matches = array();
		foreach ($matchInfo as $match)
		{
			$matches[$match['mid']] = array(
				'home' => $match['home'],
				'away' => $match['awary'],
				'dt' => $match['dt'],
				'issue' => $match['issue'],
				'mname' => $match['mname']
			);
		}
		
		$template = '<table width="100%" border="0" cellpadding="0" cellspacing="0" style="border-left:1px solid #ccc;border-top:1px solid #ccc;">
        <tr>
            <td style="font-size:12px;line-height:21px;padding:5px 0;border-right:1px solid #ccc;border-bottom:1px solid #ccc;text-align:center;font-family:\'Microsoft YaHei\';">场次</td>
        	<td style="font-size:12px;line-height:21px;padding:5px 0;border-right:1px solid #ccc;border-bottom:1px solid #ccc;text-align:center;font-family:\'Microsoft YaHei\';">比赛时间</td>
        	<td style="font-size:12px;line-height:21px;padding:5px 0;border-right:1px solid #ccc;border-bottom:1px solid #ccc;text-align:center;font-family:\'Microsoft YaHei\';">'.($order['lid'] == JCZQ ? '主队' : '客队').'</td>
        	<td style="font-size:12px;line-height:21px;padding:5px 0;border-right:1px solid #ccc;border-bottom:1px solid #ccc;text-align:center;font-family:\'Microsoft YaHei\';">'.($order['lid'] == JCZQ ? '客队' : '主队').'</td>
        	<td style="font-size:12px;line-height:21px;padding:5px 0;border-right:1px solid #ccc;border-bottom:1px solid #ccc;text-align:center;font-family:\'Microsoft YaHei\';">盘口</td>
        	<td style="font-size:12px;line-height:21px;padding:5px 0;border-right:1px solid #ccc;border-bottom:1px solid #ccc;text-align:center;font-family:\'Microsoft YaHei\';">投注方案/出票赔率</td>
  		</tr>';
		foreach ($result as $mid => $cast)
		{
			$template .= '<tr>';
			$rowspan = $cast['play'];
			$template .= '<td rowspan="'.$rowspan.'" style="font-size:12px;line-height:21px;padding:5px 0;border-right:1px solid #ccc;border-bottom:1px solid #ccc;text-align:center;font-family:\'Microsoft YaHei\';">'.$matches[$mid]['mname'].'</td>';
			$template .= '<td rowspan="'.$rowspan.'" style="font-size:12px;line-height:21px;padding:5px 0;border-right:1px solid #ccc;border-bottom:1px solid #ccc;text-align:center;font-family:\'Microsoft YaHei\';">'.date('m-d H:i', $matches[$mid]['dt']/1000).'</td>';
			if($order['lid'] == JCZQ)
			{
				$template .= '<td rowspan="'.$rowspan.'" style="font-size:12px;line-height:21px;padding:5px 0;border-right:1px solid #ccc;border-bottom:1px solid #ccc;text-align:center;font-family:\'Microsoft YaHei\';">'.$matches[$mid]['home'].'</td>';
				$template .= '<td rowspan="'.$rowspan.'" style="font-size:12px;line-height:21px;padding:5px 0;border-right:1px solid #ccc;border-bottom:1px solid #ccc;text-align:center;font-family:\'Microsoft YaHei\';">'.$matches[$mid]['away'].'</td>';
			}
			else
			{
				$template .= '<td rowspan="'.$rowspan.'" style="font-size:12px;line-height:21px;padding:5px 0;border-right:1px solid #ccc;border-bottom:1px solid #ccc;text-align:center;font-family:\'Microsoft YaHei\';">'.$matches[$mid]['away'].'</td>';
				$template .= '<td rowspan="'.$rowspan.'" style="font-size:12px;line-height:21px;padding:5px 0;border-right:1px solid #ccc;border-bottom:1px solid #ccc;text-align:center;font-family:\'Microsoft YaHei\';">'.$matches[$mid]['home'].'</td>';
			}
			$pts = array_keys($cast);
			$playCount = 0;
			foreach ($this->playTypes as $playType)
			{
				if(!in_array($playType, $pts))
				{
					continue;
				}
				if ($playCount > 0) 
				{
					$template .= '<tr>';
				}
				$handicap = '-';
				if($playType == 'RQSPF')
				{
					$handicap = $cast['let']; //intval($cast['let']) > 0 ? '+' . intval($cast['let']) : intval($cast['let']);
				}
				if($playType == 'RFSF')
				{
					$handicap = $cast['let']; //doubleval($cast['let']) > 0 ? '+' . doubleval($cast['let']) : doubleval($cast['let']);
				}
				if($playType == 'DXF')
				{
					$handicap = $cast['preScore']; //str_replace('+', '', $cast['preScore']);
				}
				$template .= '<td style="font-size:12px;line-height:21px;padding:5px 0;border-right:1px solid #ccc;border-bottom:1px solid #ccc;text-align:center;font-family:\'Microsoft YaHei\';">' . $handicap . '</td>';	//盘口
				$template .= '<td style="font-size:12px;line-height:21px;padding:5px 0;border-right:1px solid #ccc;border-bottom:1px solid #ccc;text-align:center;font-family:\'Microsoft YaHei\';">' . $this->renderOptions($playType, $cast[$playType]) . '</td>';	//投注方案
				$template .= '</tr>';
				$playCount += 1;
			}
		}
		$template .= '</table>';
		
		return array($template, $tickettime);
	}
	
	private function gjcParse($orderId, $status)
	{
	    $template = '<table width="100%" border="0" cellpadding="0" cellspacing="0" style="border-left:1px solid #ccc;border-top:1px solid #ccc;table-layout:fixed;">
        <tr>
        	<td style="font-size:12px;line-height:21px;padding:5px 0;border-right:1px solid #ccc;border-bottom:1px solid #ccc;text-align:center;font-family:\'Microsoft YaHei\';width:400px;">投注方案/出票赔率</td>
            <td style="font-size:12px;line-height:21px;padding:5px 0;border-right:1px solid #ccc;border-bottom:1px solid #ccc;text-align:center;font-family:\'Microsoft YaHei\';">订单状态</td>
            <td style="font-size:12px;line-height:21px;padding:5px 0;border-right:1px solid #ccc;border-bottom:1px solid #ccc;text-align:center;font-family:\'Microsoft YaHei\';">注数</td>
            <td style="font-size:12px;line-height:21px;padding:5px 0;border-right:1px solid #ccc;border-bottom:1px solid #ccc;text-align:center;font-family:\'Microsoft YaHei\';">倍数</td>
        </tr>';
	    $splitOrder = $this->CI->order_model->getGjOrderDetail($orderId, $status);
	    $tickettime = '1970-01-01 00:00:00';
	    if (strtotime($splitOrder[0]['ticket_time']) > strtotime($tickettime)) $tickettime = $splitOrder[0]['ticket_time'];
	    preg_match_all('/(\d+)\((\d+\.*\d*)\)/', $splitOrder[0]['codes'], $matches);
	    if (!empty($matches[1])) {
	        $res = $this->CI->order_model->getGjDetail($matches[1], $splitOrder[0]['lid']);
	        $pDetail = json_decode($splitOrder[0]['pdetail'], true);
	        foreach ($res as $val) {
	            $odres[$val['mid']] = $val;
	        }
    		$result = array();
    		foreach ($matches[1] as $k => $val) {
    		    foreach ($splitOrder as $order) {
    		        $data = array();
    		        $data['codes'] = $odres[intval($val)]['name']."（".$pDetail[$val]."）";
    		        $data['status'] = ($order['status'] == '600') ? '出票失败' : '出票成功';
    		        $data['betNum'] = 1;
    		        $data['multi'] = $order['multi'];
    		        $result[] = $data;
    		    }
    		}
	    }
	    foreach ($result as $value)
	    {
	        $template .= '<tr>';
	        $template .= '<td style="font-size:12px;line-height:21px;padding:5px 0;border-right:1px solid #ccc;border-bottom:1px solid #ccc;text-align:center;font-family:\'Microsoft YaHei\';">'.$value['codes'].'</td>';
	        $template .= '<td style="font-size:12px;line-height:21px;padding:5px 0;border-right:1px solid #ccc;border-bottom:1px solid #ccc;text-align:center;font-family:\'Microsoft YaHei\';">'.$value['status'].'</td>';
	        $template .= '<td style="font-size:12px;line-height:21px;padding:5px 0;border-right:1px solid #ccc;border-bottom:1px solid #ccc;text-align:center;font-family:\'Microsoft YaHei\';">'.$value['betNum'].'</td>';
	        $template .= '<td style="font-size:12px;line-height:21px;padding:5px 0;border-right:1px solid #ccc;border-bottom:1px solid #ccc;text-align:center;font-family:\'Microsoft YaHei\';">'.$value['multi'].'</td>';
	        $template .= '</tr>';
	    }
	    $template .= '</table>';
	    
	    return array($template, $tickettime);
	}
	
	/**
	 * 投注方案解析
	 * @param unknown_type $playType
	 * @param unknown_type $options
	 */
	private function renderOptions($playType, $options)
	{
		$tpl = '';
		$castName = '';
		$casts = array();
		foreach ($options as $key => $option)
		{
			if(in_array($option['cast'], $casts))
			{
				return ;
			}
			$castName = $this->playOptionNames[$playType][$option['cast']];
			if ($option['odd'])
			{
				$tpl .= $castName . '(' . $option['odd'] . ')<br>';
			}
			array_push($casts, $option['cast']);
		}
		
		return $tpl;
	}
	
	private function parseTicket($order, $ticketInfo)
	{
		$cast = array();
		$tickets = explode(';', $order['codes']);
		$passWays = array();
		foreach ($tickets as $ticket)
		{
			$parts = explode('|', $ticket);
			$playType = $parts[0];
			$codes = $parts[1];
			$passWays[] = ($order['playType'] == '7') ? $parts[3] : $parts[2];
			$codesArr = explode(',', $codes);
			foreach ($codesArr as $v)
			{
				$detail = explode('=', $v);
				$tmp = explode('>', $detail[0]);
				$playType = $tmp[0];
				$matchId = $tmp[1];
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
				$rule = ($order['playType'] == '6') ? $this->ruleMap[$order['lid']]['6'] : $this->ruleMap[$order['lid']]['0'];
				foreach ($options as $vs)
				{
					$vTmp = explode('/', $vs);
					foreach ($vTmp as $v)
					{
						preg_match($rule,$v,$matchs);
						if($matchs)
						{
							// 实际赔率
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
								'odd' => $pl
							);
							if ($playType == 'RQSPF')
							{
								$cast[$matchId]['let'] = $pks; //$matchs[2];
							}
							if ($playType == 'RFSF')
							{
								$cast[$matchId]['let'] = $pks; //$matchs[2];
							}
							if($playType == 'DXF') 
							{
								$cast[$matchId]['preScore'] = $pks; //$matchs[4];
							}

							if(!in_array($option, $cast[$matchId][$playType]))
							{
								$cast[$matchId][$playType][] = $option;
							}	
						}
					}
				}
			}
				
		}
		$passWays = array_unique($passWays);
		sort($passWays);
		$this->passWay = str_replace('*', '串', implode(',', $passWays));
		
		return $cast;
	}
	
	/**
	 * 老足彩出票详情解析
	 * @param unknown_type $order
	 * @return string
	 */
	private function lzcParse($order)
	{
		$tickettime = $this->CI->order_model->getMaxTickettime($order['orderId']);
		$tickettime = $tickettime['ticket_time'];
		$template = '<table width="100%" border="0" cellpadding="0" cellspacing="0" style="border-left:1px solid #ccc;border-top:1px solid #ccc;">
        <tr>
        	<td style="font-size:12px;line-height:21px;padding:5px 0;border-right:1px solid #ccc;border-bottom:1px solid #ccc;text-align:center;font-family:\'Microsoft YaHei\';">场次</td>
            <td style="font-size:12px;line-height:21px;padding:5px 0;border-right:1px solid #ccc;border-bottom:1px solid #ccc;text-align:center;font-family:\'Microsoft YaHei\';">1</td>
            <td style="font-size:12px;line-height:21px;padding:5px 0;border-right:1px solid #ccc;border-bottom:1px solid #ccc;text-align:center;font-family:\'Microsoft YaHei\';">2</td>
            <td style="font-size:12px;line-height:21px;padding:5px 0;border-right:1px solid #ccc;border-bottom:1px solid #ccc;text-align:center;font-family:\'Microsoft YaHei\';">3</td>
            <td style="font-size:12px;line-height:21px;padding:5px 0;border-right:1px solid #ccc;border-bottom:1px solid #ccc;text-align:center;font-family:\'Microsoft YaHei\';">4</td>
            <td style="font-size:12px;line-height:21px;padding:5px 0;border-right:1px solid #ccc;border-bottom:1px solid #ccc;text-align:center;font-family:\'Microsoft YaHei\';">5</td>
            <td style="font-size:12px;line-height:21px;padding:5px 0;border-right:1px solid #ccc;border-bottom:1px solid #ccc;text-align:center;font-family:\'Microsoft YaHei\';">6</td>
            <td style="font-size:12px;line-height:21px;padding:5px 0;border-right:1px solid #ccc;border-bottom:1px solid #ccc;text-align:center;font-family:\'Microsoft YaHei\';">7</td>
            <td style="font-size:12px;line-height:21px;padding:5px 0;border-right:1px solid #ccc;border-bottom:1px solid #ccc;text-align:center;font-family:\'Microsoft YaHei\';">8</td>
            <td style="font-size:12px;line-height:21px;padding:5px 0;border-right:1px solid #ccc;border-bottom:1px solid #ccc;text-align:center;font-family:\'Microsoft YaHei\';">9</td>
            <td style="font-size:12px;line-height:21px;padding:5px 0;border-right:1px solid #ccc;border-bottom:1px solid #ccc;text-align:center;font-family:\'Microsoft YaHei\';">10</td>
            <td style="font-size:12px;line-height:21px;padding:5px 0;border-right:1px solid #ccc;border-bottom:1px solid #ccc;text-align:center;font-family:\'Microsoft YaHei\';">11</td>
            <td style="font-size:12px;line-height:21px;padding:5px 0;border-right:1px solid #ccc;border-bottom:1px solid #ccc;text-align:center;font-family:\'Microsoft YaHei\';">12</td>
            <td style="font-size:12px;line-height:21px;padding:5px 0;border-right:1px solid #ccc;border-bottom:1px solid #ccc;text-align:center;font-family:\'Microsoft YaHei\';">13</td>
            <td style="font-size:12px;line-height:21px;padding:5px 0;border-right:1px solid #ccc;border-bottom:1px solid #ccc;text-align:center;font-family:\'Microsoft YaHei\';">14</td>
    	</tr>';
		//获取比赛对阵信息
		$matchInfo = $this->CI->order_model->getSfcMatchs($order['issue']);
		$template .= '<tr><td style="font-size:12px;line-height:21px;padding:5px 0;border-right:1px solid #ccc;border-bottom:1px solid #ccc;text-align:center;font-family:\'Microsoft YaHei\';">主队</td>';
		foreach ($matchInfo as $match)
		{
			$template .= '<td style="font-size:12px;line-height:18px;width:20px;padding:10px;border-right:1px solid #ccc;border-bottom:1px solid #ccc;text-align:center;font-family:\'Microsoft YaHei\';">'.$match['teamName1'].'</td>';
		}
		$template .= '</tr><tr><td style="font-size:12px;line-height:21px;padding:5px 0;border-right:1px solid #ccc;border-bottom:1px solid #ccc;text-align:center;font-family:\'Microsoft YaHei\';">客队</td>';
		foreach ($matchInfo as $match)
		{
			$template .= '<td style="font-size:12px;line-height:18px;width:20px;padding:10px;border-right:1px solid #ccc;border-bottom:1px solid #ccc;text-align:center;font-family:\'Microsoft YaHei\';">'.$match['teamName2'].'</td>';
		}
		$template .= '</tr>';
		$codes = $this->renderLzc($order['codes']);
		$rowspan = count($codes);
		foreach ($codes as $key => $code)
		{
			$template .= '<tr>';
			if($key == '0')
			{
				$template .= '<td rowspan="'.$rowspan.'" style="padding:10px 0;font-size:12px;line-height:18px;border-right:1px solid #ccc;border-bottom:1px solid #ccc;text-align:center;font-family:\'Microsoft YaHei\';">投注<br>方案</td>';
			}
			foreach ($code as $val)
			{
				$template .= '<td style="font-size:12px;line-height:21px;width:20px;padding:5px 10px;border-right:1px solid #ccc;border-bottom:1px solid #ccc;text-align:center;font-family:\'Microsoft YaHei\';">'.$val.'</td>';
			}
			$template .= '</tr>';
		}
		$template .= '</table>';
		
		return array($template, $tickettime);
	}
	
	/**
	 * 老足彩投注串解析
	 * @param unknown_type $codes
	 */
	private function renderLzc($codes)
	{
		$data = array();
		$codes = explode(';', $codes);
		foreach ($codes as $code)
		{
			$codesStr = explode(':',$code,2);
			$betStr = str_replace('#', '-', $codesStr[0]);
			$tmp = explode(',',$betStr);
			$data[] = $tmp;
		}
		
		return $data;
	}
	
	/**
	 * 数字彩出票详情数据组装
	 * @param unknown_type $orderId
	 * @param unknown_type $lid
	 * @return string
	 */
	private function numberParse($orderId, $lid)
	{
		$template = '<table width="100%" border="0" cellpadding="0" cellspacing="0" style="border-left:1px solid #ccc;border-top:1px solid #ccc;table-layout:fixed;">
        <tr>
        	<td style="font-size:12px;line-height:21px;padding:5px 0;border-right:1px solid #ccc;border-bottom:1px solid #ccc;text-align:center;font-family:\'Microsoft YaHei\';width:400px;">方案信息</td>
            <td style="font-size:12px;line-height:21px;padding:5px 0;border-right:1px solid #ccc;border-bottom:1px solid #ccc;text-align:center;font-family:\'Microsoft YaHei\';">订单状态</td>
            <td style="font-size:12px;line-height:21px;padding:5px 0;border-right:1px solid #ccc;border-bottom:1px solid #ccc;text-align:center;font-family:\'Microsoft YaHei\';">注数</td>
            <td style="font-size:12px;line-height:21px;padding:5px 0;border-right:1px solid #ccc;border-bottom:1px solid #ccc;text-align:center;font-family:\'Microsoft YaHei\';">倍数</td>
        </tr>';
		$splitOrders = $this->CI->order_model->getNumOrderDetail($orderId, $lid);
		$result = array();
		$tickettime = '1970-01-01 00:00:00';
		foreach ($splitOrders as $order)
		{
			if (strtotime($order['ticket_time']) > strtotime($tickettime)) $tickettime = $order['ticket_time'];
			$order['codes'] = preg_replace('/\^$/is', '', $order['codes']);
			$codes = explode('^', $order['codes']);
			if(count($codes) > 1)
			{
				foreach ($codes as $code)
				{
					$data = array();
					$order['betTnum'] = 1;
					$order['codes'] = $code;
					$data['codes'] = $this->renderNumber($order);
					$data['status'] = ($order['status'] == '600') ? '出票失败' : '出票成功';
					$data['betNum'] = 1;
					$data['multi'] = $order['multi'];
					$result[] = $data;
				}
			}
			else
			{
				$data = array();
				$data['codes'] = $this->renderNumber($order);
				$data['status'] = ($order['status'] == '600') ? '出票失败' : '出票成功';
				$data['betNum'] = $order['betTnum'];
				$data['multi'] = $order['multi'];
				$result[] = $data;
			}
		}
		foreach ($result as $value)
		{
			$template .= '<tr>';
			$template .= '<td style="font-size:12px;line-height:21px;padding:5px 0;border-right:1px solid #ccc;border-bottom:1px solid #ccc;text-align:left;font-family:\'Microsoft YaHei\';">'.$value['codes'].'</td>';
			$template .= '<td style="font-size:12px;line-height:21px;padding:5px 0;border-right:1px solid #ccc;border-bottom:1px solid #ccc;text-align:center;font-family:\'Microsoft YaHei\';">'.$value['status'].'</td>';
			$template .= '<td style="font-size:12px;line-height:21px;padding:5px 0;border-right:1px solid #ccc;border-bottom:1px solid #ccc;text-align:center;font-family:\'Microsoft YaHei\';">'.$value['betNum'].'</td>';
			$template .= '<td style="font-size:12px;line-height:21px;padding:5px 0;border-right:1px solid #ccc;border-bottom:1px solid #ccc;text-align:center;font-family:\'Microsoft YaHei\';">'.$value['multi'].'</td>';
			$template .= '</tr>';
		}
		$template .= '</table>';
		
		return array($template, $tickettime);
	}
	
	/**
	 * 数字彩投注串解析
	 * @param unknown_type $order
	 */
	private function renderNumber($order)
	{
		$tpl = '';
		if($order['playType'] == '135')
		{
			$playType = '胆拖';
		}
		elseif ($order['betTnum'] > 1)
		{
			$playType = '复式';
		}
		else
		{
			$playType = '单式';
		}
		if(($order['lid'] == DLT) && $order['isChase'])
		{
			$playType .= '追加';
		}
		if(in_array($order['lid'], array(PLS, FCSD)))
		{
			$pT = array('1' => '直选', '2' => '组三', '3' => '组六');
			$playType = $pT[$order['playType']] . $playType;
		}
		switch ($order['lid'])
		{
			case QLC:
			case SSQ:
				$code = str_replace(array(',', '|'), array(' ', '+'), $order['codes']);
				if($order['playType'] == '135')
				{
					$code = '(' . str_replace('#', ')', $code);
				}
				break;
			case DLT:
				if($order['playType'] == '135')
				{
					$code = '(' . str_replace(array(',', '|', '#'), array(' ', '+(', ')'), $order['codes']);
				}
				else
				{
					$code = str_replace(array(',', '|'), array(' ', '+'), $order['codes']);
				}
				break;
			case PLS:
			case FCSD:
				if($order['playType'] == '1')
				{
					$code = str_replace(array(',', '*'), array(' ', '|'), $order['codes']);
				}
				else
				{
					$code = str_replace(array(',', '*'), ' ', $order['codes']);
				}
				break;
			case PLW:
			case QXC:
				$code = str_replace(array(',', '*'), array(' ', '|'), $order['codes']);
				break;
			default:
				$code = '';
		}
		
		return '【' . $playType . '】' . $code;;
	}
	
	/**
	 * 邮件模板返回
	 * @param array() $order	订单数组
	 * @param string $ticketDetail	出票详情字符串
	 */
	private function parseTemplate($order, $ticketDetail = '')
	{
		$template = '<style>body {background: #fbf7eb;}</style>
				<table width="100%" border="0" cellpadding="0" cellspacing="0" align="center">
        			<tr>
						<td style="padding: 38px 10px; background: #fbf7eb;">
							<table width="710" border="0" cellpadding="0" cellspacing="0" align="center">
        						<tr>
									<td>
										<table width="100%" border="0" cellpadding="0" cellspacing="0" align="center" style="background: #eee;">
											<tr><td><img src="'. $this->url_prefix . ':' . getStaticFile('/caipiaoimg/v1.1/img/logo-email.png').'" width="280" height="68" alt="166彩票为生活添彩" border="0"></td></tr>
										</table>
									</td>
								</tr>
        						<tr>
									<td style="margin:0 auto; padding: 30px 30px 0; background: #fff;">
										<table width="650" border="0" cellpadding="0" cellspacing="0" align="center">
											<tr><td style="font-size:16px;font-weight: bold; color: #666; font-family: \'Microsoft YaHei\';">尊敬的166彩票网用户'.$order['userName'].'，您好！</b></td></tr>
        									<tr>
												<td style="padding-left: 32px;line-height:1.4;font-size:16px; font-family: \'Microsoft YaHei\'; ">
													您在166彩票网预约投注的'.BetCnName::getCnName($order['lid']).'<b style="font-weight: bold; font-size: 20px; color: #39a538;">已'.($order['status'] == '510' ? '部分成功出票' : '成功出票').'</b>
        										</td>
        									</tr>
											<tr><td style="padding-top:26px; font-weight: bold; font-size: 14px; color: #7d4f13; font-family: \'Microsoft YaHei\'; ">以下为您的方案内容：</td></tr><tr><td style="padding-top:8px;">'.$ticketDetail.'</td></tr>
        									<tr>
        										<td style="padding:15px 0 40px;line-height:1.8;font-size:14px;font-family:\'Microsoft YaHei\'; color: #7d4e16;">
        											<table width="100%" border="0" cellpadding="0" cellspacing="0">
        												'.((in_array($order['lid'], array(SSQ, DLT, FCSD, PLS, PLW, QLC, QXC, SFC, RJ))) ? '<tr><td style="color: #ab9c73;">订单期次</td><td>'.$order['issue'].'</td></tr>' : '').
        												'<tr>
        													<td width="11%" style="color: #ab9c73;">订单金额</td>
        													<td width="89%" style="font-size:14px;">
        														<b style="font-weight: bold;">'.(!empty($this->passWay) ? $this->passWay.'&nbsp;' : '') . $order['betTnum'].'注&nbsp;'.(!in_array($order['playType'], array('6', '7')) ? $order['multi'].'倍&nbsp;共' : '共') . number_format($order['money'] / 100).'元</b>
        													</td>
        												</tr>
        												<tr><td style="color: #ab9c73;">订单编号</td><td style="font-size:14px;">'.$order['orderId'].'</td></tr>
        												<tr><td style="color: #ab9c73;">购买方式</td><td style="font-size:14px;">'.(in_array($order['orderType'], array(1, 6)) ? '追号投注' : '普通投注').'</td></tr>
        												<tr><td style="color: #ab9c73;">方案地址</td><td style="font-size:14px;"><a href="'.$this->url_prefix.'://'.$this->CI->config->item('domain').'/orders/detail/'.$order['orderId'].'" style="color: #7d4e16;">'.$this->url_prefix.'://'.$this->CI->config->item('domain').'/orders/detail/'.$order['orderId'].'</a></td></tr>
        												<tr><td style="color: #ab9c73;">创建时间</td><td style="font-size:14px;">'.$order['created'].'</td></tr><tr><td style="color: #ab9c73;">支付时间</td><td style="font-size:14px;">'.$order['pay_time'].'</td></tr>
        												<tr><td style="color: #ab9c73;">出票时间</td><td style="font-size:14px;">'.$order['ticket_time'].'</td></tr>
        											</table>
        										</td>
        									</tr>
        									<tr>
        										<td style="line-height:1.8;font-size:12px;font-family:\'Microsoft YaHei\';padding: 24px 0 15px; color: #999; border-top: 5px solid #f5f4ef; border-bottom: 1px solid #f7f4ef;">
                            						 温馨提示：本邮件仅作为您在166彩票预约成功并出票的邮件，不能用于兑奖；<br> 出票内容：如您对订单内容存在疑问，可前往<a href="'.$this->url_prefix.'://'.$this->CI->config->item('domain').'" target="_blank" style="color: #3e8be7;">166彩票</a>查阅订单详情及出票明细；<br> 奖金派送：单注100万以下的奖金将直接派送至您的166彩票账户，单注100万以上的奖金会有客服第一时间联系您完成兑奖。
        										</td>
        									</tr>
        									<tr>
        										<td>
        											<table width="100%" border="0" cellpadding="0" cellspacing="0">
        												<tr>
        													<td style="line-height:1.8;font-size:12px;font-family:\'Microsoft YaHei\';color: #999;">
        														此邮件为系统自动发送，请勿直接回复<br>如对以上内容有所疑问，欢迎前往<a href="'.$this->url_prefix.'://'.$this->CI->config->item('domain').'" target="_blank" style="color: #3e8be7;">166彩票</a>联系在线客服<br>
	        													 客服热线：400-690-6760<br>查看《<a href="'.$this->url_prefix.'://'.$this->CI->config->item('domain').'/activity/fwcn" target="_blank" style="color: #3e8be7;">166彩票用户服务承诺</a>》<br>更多优惠活动可扫描右侧二维码下载客户端查看
        													</td>
        													<td align="center" style="padding:20px 0;"><img src="' . $this->url_prefix . ':' . getStaticFile('/caipiaoimg/v1.1/img/qrcode.png').'" alt="" border="0"></td>
	        											</tr>
	        										</table>
	        									</td>
	        								</tr>
	        							</table>
	        						</td>
	        					</tr>
	        				</table>
	        			</td>
	        		</tr>
	        	</table>';
		$this->passWay = ''; //清空私有属性
		
		return $template;
	}

	private function parseTicketInfo($ticketInfo = array())
	{
		$ticketData = array();
		if(!empty($ticketInfo))
		{
			foreach ($ticketInfo as $detail) 
			{
				$ticketArr = explode('|', $detail['codes']);
				$ticketDetail = $this->ticketMix($ticketArr[0], $detail['info']);
				// 汇总出票信息
                $ticketData = $this->recordTicketInfo($ticketData, $ticketDetail);
			}
		}
		return $ticketData;
	} 

	private function ticketMix($ticket, $info)
	{
		$ticketData = array();
		$ticketInfo = explode('*', $ticket);
		foreach ($ticketInfo as $k_ticket => $v_ticket)
        {
        	$ticketDetail = explode(',', $v_ticket);
        	$ticketData[$ticketDetail[0]][$ticketDetail[1]][] = $this->ticketDetail($ticketDetail[0], $ticketDetail[1], $ticketDetail[2], $info[$ticketDetail[0]]);
        }
        return $ticketData;
	}

	private function ticketDetail($mid, $playType, $tickets, $info)
	{
		// 出票盘口及赔率信息
        $ticketInfo = array();
        $resBet =  explode('/', $tickets);
        $info = json_decode($info, true);

        switch ($playType)
        {
            //胜平负
            case 'SPF':
                foreach ($resBet as $kBet => $vBet) 
                {
                	preg_match('/^(\d+)\(.*?\)$/is', $vBet, $matches);
                    // 出票盘口及赔率
                    $pk = '-';
                    $pl = $info["vs"]["v{$matches[1]}"][0];
                    $ticketInfo[$pk][$matches[1]][] = $pl;
                }
                break;
            //让球胜平负
            case 'RQSPF':
                foreach ($resBet as $kBet => $vBet) 
                {
                	preg_match('/^(\d+)(?:{(.*?)})?\(.*?\)$/is', $vBet, $matches);
                    // 出票盘口及赔率
                    $pk = $info['letVs']['letPoint'][0] > 0 ? '+' . $info['letVs']['letPoint'][0] : $info['letVs']['letPoint'][0];
                    $pl = $info["letVs"]["v{$matches[1]}"][0];
                    $ticketInfo[$pk][$matches[1]][] = $pl;
                }
                break;
            //猜比分
            case 'CBF':
                foreach ($resBet as $kBet => $vBet) 
                {
                    preg_match('/^(.*?)\(.*?\)$/is', $vBet, $matches);
                    $index = preg_replace('/[^\d]/is', '', $matches[1]);
                    // 出票盘口及赔率
                    $pk = '-';
                    $pl = $info["score"]["v$index"][0];
                    $ticketInfo[$pk][$matches[1]][] = $pl;
                }
                break;
            //总进球
            case 'JQS':
                foreach ($resBet as $kBet => $vBet) 
                {
                	preg_match('/^(\d+)\(.*?\)$/is', $vBet, $matches);
                    // 出票盘口及赔率
                    $pk = '-';
                    if($matches[1] >= 7)
                    {
                    	$pl = $info["goal"]["v7"][0];
                        $matches[1] = '7+';
                    }
                    else
                    {
                    	$pl = $info["goal"]["v".$matches[1]][0];
                    }
                    $ticketInfo[$pk][$matches[1]][] = $pl;
                }
                break;
            //半全场
            case 'BQC':
                foreach ($resBet as $kBet => $vBet) 
                {
                    preg_match('/^(.*?)\(.*?\)$/is', $vBet, $matches);
                    $spfInfo = explode('-', $matches[1]);
                    // 出票盘口及赔率
                    $pk = '-';
                    $pl = $info["half"]["v$spfInfo[0]$spfInfo[1]"][0];
                    $ticketInfo[$pk][$matches[1]][] = $pl;
                }
                break;
            //竞彩篮球 让分胜负
            case 'RFSF':
                foreach ($resBet as $kBet => $vBet)
                {
                   	preg_match('/^(\d+)(?:{(.*?)})?\(.*?\)$/is', $vBet, $matches);
                    // 出票盘口及赔率
                    $pk = $info['letVs']['letPoint'][0] > 0 ? '+' . $info['letVs']['letPoint'][0] : $info['letVs']['letPoint'][0];
                    $pl = $info['letVs']["v$matches[1]"][0];
                    $ticketInfo[$pk][$matches[1]][] = $pl;
                }
                break;
            //竞彩篮球 胜负
            case 'SF':
                foreach ($resBet as $kBet => $vBet) 
                {                  
                    preg_match('/^(\d+)\(.*?\)$/is', $vBet, $matches);
                    // 出票盘口及赔率
                    $pk = '-';
                    $pl = $info["vs"]["v{$matches[1]}"][0];
                    $ticketInfo[$pk][$matches[1]][] = $pl;
                }
                break;
            //竞彩篮球 大小分
            case 'DXF':
                foreach ($resBet as $kBet => $vBet)
                {
                	$in_map = array('0' => 'l', '3' => 'g');
                	preg_match('/^(\d+)\(.*?\).*?$/is', $vBet, $matches);
                    // 出票盘口及赔率
                    $pk = $info['bs']['basePoint'][0];
                    $pl = $info['bs'][$in_map[$matches[1]]][0];
                    $ticketInfo[$pk][$matches[1]][] = $pl;
                }
                break;
            //竞彩篮球 胜分差
            case 'SFC':
                foreach ($resBet as $kBet => $vBet) {
                	preg_match('/^(\d+)\(.*?\)$/is', $vBet, $matches);
                    // 出票盘口及赔率
                    $pk = '-';
                    $pl = $info['diff']["v$matches[1]"][0];      
                    $ticketInfo[$pk][$matches[1]][] = $pl;
                }
                break;    
            default:
                # code...
                break;
        }
        return $ticketInfo;
	}

	// 汇总出票赔率
    private function recordTicketInfo($info, $details = array())
    {
        if(!empty($details))
        {
            foreach ($details as $mid => $playItems) 
            {
                foreach ($playItems as $playType => $tickets) 
                {
                    foreach ($tickets as $key => $ticketArr) 
                    {
                        foreach ($ticketArr as $pk => $items) 
                        {
                            foreach ($items as $fa => $plArr)
                            {
                            	foreach ($plArr as $k => $pl)
                            	{
                            		if(!empty($info[$mid][$playType][$pk][$fa]) && in_array($pl, $info[$mid][$playType][$pk][$fa]))
	                                {
	                                    continue;
	                                }
	                                else
	                                {
	                                    if(!empty($pl))
                                    	{
                                    		$info[$mid][$playType][$pk][$fa][] = $pl;
                                    	}
	                                }
                            	}
                            }
                        }
                    }
                }
            }
        }
        return $info;
    }
}
