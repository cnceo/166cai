<?php
class Act_2
{
	private $CI;
	public function __construct()
	{
		$this->CI = &get_instance();
		$this->CI->load->model('activity_model');
	}
	
	public function index($order)
	{
		$status = array('510', '500', '1000', '2000');
		if(in_array($order['status'], $status))
		{
			$relation = $this->CI->activity_model->getRelations($order['uid']);
			if(!empty($relation))
			{
				$relation['orderid'] = $order['orderId'];
				
				$rebate_odds = 0;
				$prebate_odds = 0;
				
				if(!empty($relation['rebate_odds']))
				{
					$relation['rebate_odds'] = json_decode($relation['rebate_odds'], true);
					$relation['rebate_odds'][$order['lid']] = empty($relation['rebate_odds'][$order['lid']]) ? 0 : $relation['rebate_odds'][$order['lid']];
					$rebate_odds = $relation['rebate_odds'][$order['lid']] * 10;
					$relation['rebate_odds'] = $relation['rebate_odds'][$order['lid']];
				}
				
				if(!empty($relation['prebate_odds']))
				{
					$relation['prebate_odds'] = json_decode($relation['prebate_odds'], true);
					$relation['prebate_odds'][$order['lid']] = empty($relation['prebate_odds'][$order['lid']]) ? 0 : $relation['prebate_odds'][$order['lid']];
					$prebate_odds = $relation['prebate_odds'][$order['lid']] * 10;
					$relation['prebate_odds'] = $relation['prebate_odds'][$order['lid']];
				}
				
				//购买用户的返利
				if($relation['stop_flag'] == 0)
				{
					//判断是否有上级用户
					if(!empty($relation['puid']))
					{
						$rebate_odds = ($prebate_odds - $rebate_odds > 0) ? $rebate_odds : $prebate_odds;
					}
					$relation['rebate'] = floor(($order['calMoney'] * $rebate_odds) / 1000);
				}
				else
				{
					$relation['rebate'] = 0;
				}
				
				//上级用户的返利计算
				if(!empty($relation['puid']) && $relation['pstop_flag'] == 0)
				{
					$prebate_odds = ($prebate_odds - $rebate_odds > 0) ? ($prebate_odds - $rebate_odds) : 0;
					$relation['prebate'] = floor(($order['calMoney'] * $prebate_odds) / 1000);
				}
				else
				{
					$relation['prebate'] = 0;
				}
				return $this->CI->activity_model->saveDetails($relation, $order);
			}
			return true;
		}
	}
	
}