<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
include_once dirname(__FILE__) . '/ticket_model_base.php';
class ticket_qihui_model extends ticket_model_base
{
    protected $seller = 'qihui';
	public function __construct()
	{
		parent::__construct();
	}
	
	/**
	 * 组装relation数据
	 * @param unknown $relation
	 * @return array[]
	 */
	protected function dealRelations($relation)
	{
	    $datas['s_data'] = array();
	    $datas['d_data'] = array();
	    $parserFlag = true;
	    foreach ($relation as $sub_order_id => $info)
	    {
	        foreach ($info->item as $items)
	        {
	            if(empty($items->id)) continue;
	            $orderResult = array();
	            foreach ($items as $key => $item)
	            {
	                if(count($item))
	                {
	                    foreach ($item as $fk => $val)
	                    {
	                        $orderResult['pdetail'][$key][$fk] = $val;
	                    }
	                }
	                else
	                {
	                    if($key == 'id')
	                    {
	                        $item = explode('_', $item);
	                        $orderResult['mid'] = "{$item[0]}{$item[2]}";
	                    }
	                    else
	                    {
	                        $orderResult['pdetail'][$key] = "{$item[0]}{$item[2]}";
	                    }
	                    
	                }
	            }
	            array_push($datas['s_data'], '(?, ?, ?, ?)');
	            array_push($datas['d_data'], $sub_order_id);
	            array_push($datas['d_data'], "{$orderResult['mid']}");
	            array_push($datas['d_data'], json_encode($orderResult['pdetail']));
	            array_push($datas['d_data'], $this->order_status['draw']);
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
}
