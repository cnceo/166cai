<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
include_once dirname(__FILE__) . '/ticket_model_base.php';
class ticket_caidou_model extends ticket_model_base
{
    protected $seller = 'caidou';
	public function __construct()
	{
		parent::__construct();
	}

	protected function dealRelations($relation)
    {
    	$datas['s_data'] = array();
    	$datas['d_data'] = array();
    	$midPrefix = array('30' => '20', '31' => '20', '98' => '', '99' => '');
    	$parserFlag = true;
    	foreach ($relation as $lid => $relations)
    	{
	    	foreach ($relations as $suboid => $code)
	    	{
	    		$codes = explode(',', $code);
	    		foreach ($codes as $cstr)
	    		{
	    			$detail = "getDetail_$lid";
	    			$pdetail = $this->$detail($cstr);
	    			//如果解析错误，标志位置为false
	    			if(empty($pdetail['mid']) || ($pdetail['detail'] == '[]') || (!is_numeric($pdetail['mid'])))
	    			{
	    			    $parserFlag = false;
	    			}
	    			array_push($datas['d_data'], $suboid);
	    			array_push($datas['d_data'], "{$midPrefix[$lid]}{$pdetail['mid']}");
	    			array_push($datas['d_data'], $pdetail['detail']);
	    			array_push($datas['d_data'], $this->order_status['draw']);
	    			array_push($datas['s_data'], '(?, ?, ?, ?)');
	    		}
	    	}
    	}
    	$fields = array('sub_order_id', 'mid', 'pdetail', 'status');
    	$datas['sql'] = "insert cp_orders_relation(" . implode(',', $fields) . ") values" .
        	implode(',', $datas['s_data']) . $this->onduplicate($fields, array('pdetail', 'status'), array());
    	
    	$return = array(
    	    'parserFlag' => $parserFlag,
    	    'data' => $datas
    	);
    	
    	return $return;
    }
    //处理竞彩篮球出票详情
    private function getDetail_31($str)
    {
    	$regMaps = array(
    		'vs'    => array(
    			'check' => '/(\d+)=((?:[\x{4e00}-\x{9fa5}]@\d+\.?\d*[\x{4e00}-\x{9fa5}]\+?)+)/u',
    			'map'   => '/([\x{4e00}-\x{9fa5}])@(\d+\.?\d*)[\x{4e00}-\x{9fa5}]\+?/u'),
    		'letVs' => array(
    			'check' => '/(\d+)\([\x{4e00}-\x{9fa5}](\-?\d+\.?\d*)\)=((?:[\x{4e00}-\x{9fa5}]@\d+\.?\d*[\x{4e00}-\x{9fa5}]\+?)+)/u',
    	       	'map'   => '/([\x{4e00}-\x{9fa5}])@(\d+\.?\d*)[\x{4e00}-\x{9fa5}]\+?/u'), 
    		'bs'    => array(
    			'check' => '/(\d+)\((\d+\.?\d*)\)=((?:[\x{4e00}-\x{9fa5}]@\d+\.?\d*[\x{4e00}-\x{9fa5}]\+?)+)/u',
    			'map'   => '/([\x{4e00}-\x{9fa5}])@(\d+\.?\d*)[\x{4e00}-\x{9fa5}]\+?/u'),
    		'diff'  => array(
    			'check' => '/(\d+)=((?:\([\x{4e00}-\x{9fa5}](?:(?:\d+\-\d+)|(?:\d+\+?))\)@\d+\.?\d*[\x{4e00}-\x{9fa5}]\+?)+)/u',
    			'map'   => '/\(([\x{4e00}-\x{9fa5}])((?:\d+\-\d+)|(?:\d+\+?))\)@(\d+\.?\d*)[\x{4e00}-\x{9fa5}]\+?/u'),
    	);
    	$pdetail = array(); 
    	$inMaps = array('胜' => '3', '平' => '1', '负' => '0', '大' => '3', '小' => '0', '主' => '0', '客' => '1');
    	$diffMaps = array('1-5' => '1', '6-10' => '2', '11-15' => '3', '16-20' => '4', '21-25' => '5', '26+' => '6',);
    	$bsMaps = array('大' => 'g', '小' => 'l');
    	foreach ($regMaps as $mname => $regMap)
    	{
    		if(preg_match($regMap['check'], $str, $matches))
    		{
    			$mid = $matches[1];
    			switch ($mname)
    			{
    				case 'vs':
    					if(preg_match_all($regMap['map'], $matches[2], $mtchs))
    					{
    						if(is_array($mtchs[0]))
    						{
    							foreach ($mtchs[0] as $in =>$val)
    							{
    								$pdetail[$mname]['letPoint'] = (Object)array('0');
    								$pdetail[$mname]["v{$inMaps[$mtchs[1][$in]]}"] = (Object)array($mtchs[2][$in]);
    							}
    						}
    					}
    					break;
    				case 'letVs':
    					if(preg_match_all($regMap['map'], $matches[3], $mtchs))
    					{
    						if(is_array($mtchs[0]))
    						{
    							foreach ($mtchs[0] as $in =>$val)
    							{
    								$pdetail[$mname]['letPoint'] = (Object)array($matches[2]);
    								$pdetail[$mname]["v{$inMaps[$mtchs[1][$in]]}"] = (Object)array($mtchs[2][$in]);
    							}
    						}
    					}
    					break;
    				case 'bs':
    					if(preg_match_all($regMap['map'], $matches[3], $mtchs))
    					{
    						if(is_array($mtchs[0]))
    						{
    							foreach ($mtchs[0] as $in =>$val)
    							{
    								$pdetail[$mname]['basePoint'] = (Object)array($matches[2]);
    								$pdetail[$mname]["{$bsMaps[$mtchs[1][$in]]}"] = (Object)array($mtchs[2][$in]);
    							}
    						}
    					}
    					break;
    				case 'diff':
    					if(preg_match_all($regMap['map'], $matches[2], $mtchs))
    					{
    						if(is_array($mtchs[0]))
    						{
    							foreach ($mtchs[0] as $in =>$val)
    							{
    								$pdetail[$mname]["v{$inMaps[$mtchs[1][$in]]}{$diffMaps[$mtchs[2][$in]]}"] = (Object)array($mtchs[3][$in]);
    							}
    						}
    					}
    					break;
    			}
    			break;
    		}
    	}
    	return array('mid' => $mid, 'detail' => json_encode($pdetail));
    }
    //处理竞彩足球出票详情
    private function getDetail_30($str)
    {
    	$regMaps = array(
    		'vs'    => array(
    			'check' => '/(\d+)=((?:[\x{4e00}-\x{9fa5}]@\d+\.?\d*[\x{4e00}-\x{9fa5}]\+?)+)/u',
    			'map'   => '/([\x{4e00}-\x{9fa5}])@(\d+\.?\d*)[\x{4e00}-\x{9fa5}]\+?/u'),
    		'letVs' => array(
    			'check' => '/(\d+)\([\x{4e00}-\x{9fa5}](\-?\d+)\)=((?:[\x{4e00}-\x{9fa5}]@\d+\.?\d*[\x{4e00}-\x{9fa5}]\+?)+)/u',
    	       	'map'   => '/([\x{4e00}-\x{9fa5}])@(\d+\.?\d*)[\x{4e00}-\x{9fa5}]\+?/u'), 
    		'score' => array(
    			'check' => '/(\d+)=((?:\(\d+:\d+\)@\d+\.?\d*[\x{4e00}-\x{9fa5}]\+?)+)/u', 
    			'map'   => '/\((\d+):(\d+)\)@(\d+\.?\d*)[\x{4e00}-\x{9fa5}]\+?/u'),
    		'goal'  => array(
    			'check' => '/(\d+)=((?:\(\d+\+?\)@\d+\.?\d*[\x{4e00}-\x{9fa5}]\+?)+)/u', 
    			'map'   => '/\((\d+)\+?\)@(\d+\.?\d*)[\x{4e00}-\x{9fa5}]\+?/u'),
    		'half'  => array(
    			'check' => '/(\d+)=((?:[\x{4e00}-\x{9fa5}]{2}@\d+\.?\d*[\x{4e00}-\x{9fa5}]\+?)+)/u',
    			'map'   => '/([\x{4e00}-\x{9fa5}])([\x{4e00}-\x{9fa5}])@(\d+\.?\d*)[\x{4e00}-\x{9fa5}]\+?/u')
    	);
    	$pdetail = array(); 
    	$mid = '';
    	$inMaps = array('胜' => '3', '平' => '1', '负' => '0');
    	foreach ($regMaps as $mname => $regMap)
    	{
    		if(preg_match($regMap['check'], $str, $matches))
    		{
    			$mid = $matches[1];
    			switch ($mname)
    			{
    				case 'goal':
    					if(preg_match_all($regMap['map'], $matches[2], $mtchs))
    					{
    						if(is_array($mtchs[0]))
    						{
    							foreach ($mtchs[0] as $in =>$val)
    							{
    								$pdetail[$mname]["v{$mtchs[1][$in]}"] = (Object)array($mtchs[2][$in]);
    							}
    						}
    					}
    					break;
    				case 'vs':
    					if(preg_match_all($regMap['map'], $matches[2], $mtchs))
    					{
    						if(is_array($mtchs[0]))
    						{
    							foreach ($mtchs[0] as $in =>$val)
    							{
    								$pdetail[$mname]['letPoint'] = (Object)array('0');
    								$pdetail[$mname]["v{$inMaps[$mtchs[1][$in]]}"] = (Object)array($mtchs[2][$in]);
    							}
    						}
    					}
    					break;
    				case 'letVs':
    					if(preg_match_all($regMap['map'], $matches[3], $mtchs))
    					{
    						if(is_array($mtchs[0]))
    						{
    							foreach ($mtchs[0] as $in =>$val)
    							{
    								$pdetail[$mname]['letPoint'] = (Object)array($matches[2]);
    								$pdetail[$mname]["v{$inMaps[$mtchs[1][$in]]}"] = (Object)array($mtchs[2][$in]);
    							}
    						}
    					}
    					break;
    				case 'score':
    					if(preg_match_all($regMap['map'], $matches[2], $mtchs))
    					{
    						if(is_array($mtchs[0]))
    						{
    							foreach ($mtchs[0] as $in =>$val)
    							{
    								$pdetail[$mname]["v{$mtchs[1][$in]}{$mtchs[2][$in]}"] = (Object)array($mtchs[3][$in]);
    							}
    						}
    					}
    					break;
    				case 'half':
    					if(preg_match_all($regMap['map'], $matches[2], $mtchs))
    					{
    						if(is_array($mtchs[0]))
    						{
    							foreach ($mtchs[0] as $in =>$val)
    							{
    								$pdetail[$mname]["v{$inMaps[$mtchs[1][$in]]}{$inMaps[$mtchs[2][$in]]}"] = (Object)array($mtchs[3][$in]);
    							}
    						}
    					}
    					break;
    			}
    			break;
    		}
    	}
    	return array('mid' => $mid, 'detail' => json_encode($pdetail));
    }
    
    //处理冠军彩出票详情
    private function getDetail_98($str)
    {
    	return $this->comm_98_99($str);
    }
    
    //处理冠亚军彩出票详情
    private function getDetail_99($str)
    {
    	return $this->comm_98_99($str);
    }
    
    private function comm_98_99($str)
    {
    	$strArr = explode('|', $str);
    	$mid = $strArr[1];
    	$matchs = explode('+', $strArr[0]);
    	$pdetail = array();
    	//(01 法国)@1.81元+(05 英格兰)@5.62元+(12 乌克兰)@4.38元
    	$rule = '\((\d+).*?\)@(\d+\.?\d+)[\x{4e00}-\x{9fa5}]';
    	foreach ($matchs as $match)
    	{
    		preg_match("/{$rule}/u", $match, $value);
    		if(!empty($value[1]))
    		{
    			$pdetail[$value[1]] = $value[2];
    		}
    	}
    	
    	return array('mid' => $mid, 'detail' => json_encode($pdetail));
    }
}
