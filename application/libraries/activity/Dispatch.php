<?php
class Dispatch
{
	private $CI;
	public function __construct()
	{
		$this->CI = &get_instance();
		$this->CI->load->model('activity_model');
	}
	
	public function index($order, $act_maps)
	{
		// 目前只考虑 activity_id = 4 加奖活动
		$activityArry = array('4');
		$activityInfo = $this->CI->activity_model->getActivityInfo($activityArry);

		if(!empty($activityInfo))
		{
			// 初始化订单活动标识
			$activity_ids = !empty($order['activity_ids']) ? $order['activity_ids'] : '0';
			$activity_status = !empty($order['activity_status']) ? $order['activity_status'] : '0';

			foreach ($activityInfo as $activity) 
			{	
				// 没有打上过当前标识
				$checkFlag = 0;
				$checkRes = FALSE;
				if( isset($act_maps[$activity['id']][1]) && (($activity_ids & $act_maps[$activity['id']][1]) != $act_maps[$activity['id']][1]) )
				{
					// 未参与返利 不中包赔
					if( (($activity_ids & 1) != 1) && (($activity_ids & 2) != 2) )
					{
						$checkFlag = 1;
					}
					elseif(($activity_ids & 2) == 2)
					{
						// 不中包赔互斥
						$checkFlag = 0;
					}
					else
					{
						// 已参与返利 判断返点比例是否为0
						if(($activity_ids & 1) == 1)
						{
							if($this->checkUserRebate($order))
							{
								$checkFlag = 1;
							}
						}
					}
				}
				else
				{
					// 忽略
					$checkRes = TRUE;
				}	

				if($checkFlag)
				{
					// 检查是否满足当前活动
					$checkRes = $this->checkActivity($activity['id'], $order);

					if($checkRes)
					{
						$activity_ids = $activity_ids | $act_maps[$activity['id']][1];
						// LOG
						log_message('LOG', "请求参数 - 活动ID: " . $activity_ids, 'dispatch');
						log_message('LOG', "请求参数 - 订单ID: " . $order['orderId'], 'dispatch');
						// 更新活动标识
						$this->CI->activity_model->updateActivityInfo($order['orderId'], $activity_ids);
					}
				}

				// 不满足条件的订单标记为已处理
				if(!$checkRes)
				{
					$activity_status = $activity_status | $act_maps[$activity['id']][1];
					$this->CI->activity_model->updateActivityComplete($order['orderId'], $activity_status);
				}
			}
		}
	}

	public function checkUserRebate($order)
	{
		$result = FALSE;

		$rebateInfo = $this->CI->activity_model->getUserRebate($order['uid']);

		if(!empty($rebateInfo) && !empty($rebateInfo['rebate_odds']))
		{
			$rebate_odds = json_decode($rebateInfo['rebate_odds'], true);

			if(isset($rebate_odds[$order['lid']]) && $rebate_odds[$order['lid']] == '0.0')
			{
				$result = TRUE;
			}
		}
		else
		{
			$result = TRUE;
		}
		return $result;
	}

	public function checkActivity($activityId, $orderInfo)
	{
		switch ($activityId) 
		{
			// 加奖
			case '4':
				$result = $this->checkJjActivity($orderInfo);
				break;
			
			default:
				$result = FALSE;
				break;
		}
		return $result;
	}

	/**
     * 检查加奖活动是否满足
     */
	public function checkJjActivity($orderInfo)
	{
		$this->CI->load->model('jjactivity_model');

		$result = FALSE;

		// 投注串类型
		$playTypeArry = array(
			0 => '1*1',
			1 => '2*1'
		);

		// 关闭结束的活动
		$this->CI->jjactivity_model->closeJjActivity();
		// 查询当前正在进行的活动
		$activityInfo = $this->CI->jjactivity_model->getJjActivityInfo();

		if(!empty($activityInfo))
		{
			foreach ($activityInfo as $activity) 
			{
				// 创建时间 彩种
				if($orderInfo['created'] >= $activity['startTime'] && $orderInfo['created'] <= $activity['endTime'] && $orderInfo['lid'] == $activity['lid'])
				{
					// 解析投注串是否满足条件
					$checkRes = $this->CI->jjactivity_model->checkOrderCodes($activity, $orderInfo);
					if($checkRes)
					{
						// 记录活动订单
						$recordData = array(
							'jj_id' 	=> $activity['id'],
							'orderId'	=> $orderInfo['orderId']
						);
						$res = $this->CI->jjactivity_model->recordActivity($recordData);
						if($res > 0)
						{
							$result = TRUE;
						}
					}
					
				}
			}
		}
		return $result;
	}
	
}