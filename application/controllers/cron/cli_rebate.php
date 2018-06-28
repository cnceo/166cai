<?php
/**
 * Copyright (c) 2015,上海二三四五网络科技有限公司.
 * 摘    要: 每隔一分钟需要执行的脚本
 * 作    者: shigx
 * 修改日期: 2016/3/11
 */

defined('BASEPATH') OR exit('No direct script access allowed');

class Cli_Rebate extends MY_Controller
{
	public function __construct()
    {
        parent::__construct();
        $this->load->model('rebates_model');
    }

    /**
     * 设置二级返点脚本
     */
    public function index()
    {
    	$upRebates = $this->rebates_model->getSetRebate();
    	while(!empty($upRebates))
    	{
    		foreach ($upRebates as $upRebate)
    		{
    			$rebates = $this->rebates_model->getSubOdds($upRebate['uid']);
    			if($rebates)
    			{
    				$upOdds = json_decode($upRebate['rebate_odds'], true);
    				foreach ($rebates as $val)
    				{
    					$odds = json_decode($val['rebate_odds'], true);
    					$updateFlag = false;
    					foreach ($odds as $lid => $odd)
    					{
    						//判断二级返点是否大于一级
    						if($odd > $upOdds[$lid])
    						{
    							$updateFlag = true;
    							$odds[$lid] = $upOdds[$lid];
    						}
    					}
    					
    					if($updateFlag)
    					{
    						$odds = json_encode($odds);
    						$this->rebates_model->upBebate($val['uid'], array('rebate_odds' => $odds));
    					}
    				}
    			}
    			
    			$this->rebates_model->upBebate($upRebate['uid'], array('odd_flag' => '0'));
    		}
    		
    		$upRebates = $this->rebates_model->getSetRebate();
    	}
    }
}