<?php

class Compare_Result_Model extends CI_Model 
{

    public function __construct() 
    {
    }
    
    public function getSsqAward($limit)
    {
    	$limitStr = '';
    	if($limit > 0 )
    	{
    		$limitStr = " limit {$limit}";
    	}
    	$sql = "select issue, awardNum, bonusDetail from cp_ssq_paiqi where award_time < now() order by issue desc {$limitStr}";
    	return $this->slaveCfg->query($sql)->getAll();
    }
    
    public function getDltAward($limit)
    {
    	$limitStr = '';
    	if($limit > 0 )
    	{
    		$limitStr = " limit {$limit}";
    	}
    	$sql = "select issue, awardNum, bonusDetail from cp_dlt_paiqi where award_time < now() order by issue desc {$limitStr}";
    	return $this->slaveCfg->query($sql)->getAll();
    }
}
