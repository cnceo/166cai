<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
include_once dirname(__FILE__) . '/ticket_model_base.php';
class ticket_hengju_model extends ticket_model_base
{
    protected $seller = 'hengju';
	public function __construct()
	{
		parent::__construct();
	}
	
	protected function dealRelations($relation)
	{
		$lotteryId = '';
	    $datas['s_data'] = array();
	    $datas['d_data'] = array();
	    $parserFlag = true;
	    foreach ($relation as $lid => $relations)
	    {
	    	$lotteryId = $lid;
	        foreach ($relations as $suboid => $code)
	        {
	            preg_match_all('/<match id="(.*?)">(.*?)<\/match>/', $code, $codes);
	            foreach ($codes[1] as $key => $mid)
	            {
	            	// 竞彩篮球格式处理
		        	if($lid == '43')
		        	{
		        		preg_match('/^(\d+).*?/', trim($mid), $mids);
		        		$mid = $mids[1];
		        	}
	                $detail = "getDetail_$lid";
	                $pdetail = $this->$detail($codes[2][$key], $codes[1][$key]);
	                //如果解析错误，标志位置为false
	                if(empty($mid) || ($pdetail == '[]') || (!is_numeric($mid)))
	                {
	                    $parserFlag = false;
	                }
	                array_push($datas['s_data'], '(?, ?, ?, ?)');
	                array_push($datas['d_data'], $suboid);
	                array_push($datas['d_data'], "20{$mid}");
	                array_push($datas['d_data'], $pdetail);
	                array_push($datas['d_data'], $this->order_status['draw']);
	            }
	        }
	    }
	    $fields = array('sub_order_id', 'mid', 'pdetail', 'status');

	    if($lotteryId == '42')
	    {
	    	$datas['sql'] = "insert cp_orders_relation(" . implode(',', $fields) . ") values" . 
	    	implode(',', $datas['s_data']) . " ON duplicate key UPDATE
	        pdetail= CONCAT(
                case ptype when 'RQSPF' then
                    CONCAT('{\"letVs\":{\"letPoint\":{\"0\":\"',REPLACE(substring_index(SUBSTRING(pscores, locate(CONCAT(SUBSTRING(substring_index(VALUES(pdetail), '\":{\"', 1), -1),'{'), pscores)+2), '}', 1), '+', ''), '\"},')
                when 'SPF' then
                    '{\"vs\":{\"letPoint\":{\"0\":\"0\"},'
                when 'JQS' then
                    '{\"goal\":{'
                else '' END, VALUES(pdetail)
           ),
           status=if(status < values(status), values(status), status)";
	    }
	    else
	    {
	    	$datas['sql'] = "insert cp_orders_relation(" . implode(',', $fields) . ") values" . implode(',', $datas['s_data']) . $this->onduplicate($fields, array('pdetail', 'status'), array());
	    }
	    
	    
   	   $return = array(
   	       'parserFlag' => $parserFlag,
   	       'data' => $datas
   	   );
   	   
   	   return $return;
	}
	
	//处理竞彩足球出票详情
	private function getDetail_42($str, $midStr = '')
	{
	    $pdetail = array();
	    //半全场
	    if(preg_match('/^\d+-\d+=.*/', $str))
	    {
	        $spvalue = explode('|', $str);
	        foreach ($spvalue as $val)
	        {
	            $sp = explode('=', $val);
	            $sp[0] = str_replace('-', '', $sp[0]);
	            //出票赔率空时直接返回
	            if((!isset($sp[0])) || (!isset($sp[1])))
	            {
	                return json_encode(array());
	            }
	            $pdetail['half']["v{$sp[0]}"] = (Object)array($sp[1]);
	        }
	        $pdetail = json_encode($pdetail);
	    }
	    //猜比分
	    elseif(preg_match('/^\d+:\d+=.*/', $str))
	    {
	        $spvalue = explode('|', $str);
	        foreach ($spvalue as $val)
	        {
	            $sp = explode('=', $val);
	            $sp[0] = str_replace(':', '', $sp[0]);
	            //出票赔率空时直接返回
	            if((!isset($sp[0])) || (!isset($sp[1])))
	            {
	                return json_encode(array());
	            }
	            $pdetail['score']["v{$sp[0]}"] = (Object)array($sp[1]);
	        }
	        $pdetail = json_encode($pdetail);
	    }
	    else
	    {
	        $spvalue = explode('|', $str);
	        foreach ($spvalue as $val)
	        {
	            $sp = explode('=', $val);
	            //出票赔率空时直接返回
	            if((!isset($sp[0])) || (!isset($sp[1])))
	            {
	                return json_encode(array());
	            }
	            $pdetail["v{$sp[0]}"] = (Object)array($sp[1]);
	        }
	        $pdetail = substr(json_encode($pdetail) . '}', 1);
	    }
	    
	    return $pdetail;
	}

	//处理竞彩篮球出票详情
	private function getDetail_43($str, $midStr = '')
	{
	    $pdetail = array();
	    if(strpos($midStr, 'rf') !== FALSE)
	    {
	    	// 让分胜负
	    	$spvalue = explode('|', $str);
	    	preg_match('/rf=.*?([-]?[0-9]+([.]{1}[0-9]+){0,1})/', $midStr, $lets);
	    	$pdetail['letVs']['letPoint'] = (Object)array($lets[1]);
	    	foreach ($spvalue as $val)
	        {
	            $sp = explode('=', $val);
	            $sp[0] = str_replace('-', '', $sp[0]);
	            //出票赔率空时直接返回
	            if((!isset($sp[0])) || (!isset($sp[1])))
	            {
	                return json_encode(array());
	            }
	            $pdetail['letVs']["v{$sp[0]}"] = (Object)array($sp[1]);
	        }
	        $pdetail = json_encode($pdetail);
	    }
	    elseif(strpos($midStr, 'zf') !== FALSE)
	    {
	    	// 大小分
	    	$spvalue = explode('|', $str);
	    	preg_match('/zf=.*?([0-9]+([.]{1}[0-9]+){0,1})/', $midStr, $lets);
	    	$pdetail['bs']['basePoint'] = (Object)array($lets[1]);
	    	$bsMaps = array('3' => 'g', '0' => 'l');
	    	foreach ($spvalue as $val)
	        {
	            $sp = explode('=', $val);
	            $sp[0] = str_replace('-', '', $sp[0]);
	            //出票赔率空时直接返回
	            if((!isset($sp[0])) || (!isset($sp[1])))
	            {
	                return json_encode(array());
	            }
	            $pdetail['bs']["{$bsMaps[$sp[0]]}"] = (Object)array($sp[1]);
	        }
	        $pdetail = json_encode($pdetail);
	    }
	    else
	    {
	    	$spvalue = explode('|', $str);
	    	foreach ($spvalue as $val)
	        {
	            $sp = explode('=', $val);
	            $sp[0] = str_replace('-', '', $sp[0]);
	            //出票赔率空时直接返回
	            if((!isset($sp[0])) || (!isset($sp[1])))
	            {
	                return json_encode(array());
	            }
	            if(strlen($sp[0]) == 1 && in_array($sp[0], array(0, 3)))
	            {
	            	// 胜负
	            	if(!isset($pdetail['vs']['letPoint'])) $pdetail['vs']['letPoint'] = (Object)array('0');
	            	$pdetail['vs']["v{$sp[0]}"] = (Object)array($sp[1]);
	            }
	            else
	            {
	            	// 胜分差
	            	$pdetail['diff']["v{$sp[0]}"] = (Object)array($sp[1]);
	            }
	        }
	        $pdetail = json_encode($pdetail);
	    } 
	    return $pdetail;
	}
}
