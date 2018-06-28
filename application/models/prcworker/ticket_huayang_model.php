<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
include_once dirname(__FILE__) . '/ticket_model_base.php';
class ticket_huayang_model extends ticket_model_base
{
	protected $seller = 'huayang';
	public function __construct()
	{
		parent::__construct();
	}

	protected function dealRelations($relation)
	{
	    $datas['s_data'] = array();
	    $datas['d_data'] = array();
	    $parserFlag = true;
	    foreach ($relation as $lid => $relations)
	    {
	        foreach ($relations as $suboid => $code)
	        {
	            $codes = explode(';', $code);
	            foreach ($codes as $cstr)
	            {
	                if($lid != '208')
	                {
	                    //不是混合投注拼装成混合投注格式
	                    $cstr = $lid . '^' . $cstr;
	                    $lid = 208;
	                }
	                $detail = "getDetail_$lid";
	                $pdetail = $this->$detail($cstr);
	                //如果解析错误，标志位置为false
	                if(empty($pdetail['mid']) || $pdetail['mid'] == '20' || ($pdetail['detail'] == '[]') || (!is_numeric($pdetail['mid'])))
	                {
	                    $parserFlag = false;
	                }
	                array_push($datas['d_data'], $suboid);
	                array_push($datas['d_data'], "{$pdetail['mid']}");
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
	
	//处理竞彩足球出票详情
	private function getDetail_208($str)
	{
	    $regMaps = array(
	        'vs'    => '/209\^(.*?)\((.*?)\)/',
	        'letVs' => '/210\^(.*?)\((.*?)\)/',
	        'score' => '/211\^(.*?)\((.*?)\)/',
	        'goal'  => '/212\^(.*?)\((.*?)\)/',
	        'half'  => '/213\^(.*?)\((.*?)\)/',
	    );
	    $pdetail = array();
	    $mid = '';
	    foreach ($regMaps as $mname => $rule)
	    {
	        if(preg_match($rule, $str, $matches))
	        {
	            $mid = '20' . str_replace('-', '', $matches[1]);
	            switch ($mname)
	            {
	                case 'vs':
	                    $pdetail[$mname]['letPoint'] = (Object)array('0');
	                    $spvalue = explode(',', $matches[2]);
	                    foreach ($spvalue as $val)
	                    {
	                        $sp = explode('_', $val);
	                        $pdetail[$mname]["v{$sp[0]}"] = (Object)array($sp[1]);
	                    }
	                    break;
	                 case 'letVs':
	                     $letPoint = $this->getzqRqByMid($mid);
	                     $letPoint = str_replace('+', '', $letPoint);
	                     $pdetail[$mname]['letPoint'] = (Object)array($letPoint);
	                     $spvalue = explode(',', $matches[2]);
	                     foreach ($spvalue as $val)
	                     {
	                         $sp = explode('_', $val);
	                         $pdetail[$mname]["v{$sp[0]}"] = (Object)array($sp[1]);
	                     }
	                     break;
	                 case 'score':    
	                 case 'goal':
	                 case 'half':
	                     $spvalue = explode(',', $matches[2]);
	                     foreach ($spvalue as $val)
	                     {
	                         $sp = explode('_', $val);
	                         $pdetail[$mname]["v{$sp[0]}"] = (Object)array($sp[1]);
	                     }
	                     break;
	            }
	        }
	    }

	    return array('mid' => $mid, 'detail' => json_encode($pdetail));
	}
	
	/**
	 * 足球让球
	 * @param unknown $mid
	 * @return unknown
	 */
	private function getzqRqByMid($mid)
	{
	    return $this->dc->query("select rq from cp_jczq_paiqi where mid = ? ", $mid)->getOne();
	}
}
