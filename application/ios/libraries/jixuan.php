<?php

class Jixuan {
	
	private $_minLength = array(
		'51' => array(6, 1),
		'23529' => array(5, 2),
	);
	
	private $_Amount = array(
		'51' => array(33, 16),
		'23529' => array(35, 12),
	);
	
	private $CI;
	public function __construct()
	{
		$this->CI = &get_instance();
	}
	
    public function getBalls($lid) 
    {
    	$balls = array();
    	$minLength = $this->_minLength[$lid];
    	$amont = $this->_Amount[$lid];
    	
    	foreach ($minLength as $key => $min) 
        {
    		$res = $this->rand($min, $amont[$key]);
    		$balls[$key] = $res;
    	}
    	$ball = implode('|', $balls);
    	
    	if (empty($rand)) $rand = array();
    	array_push($rand, $ball);

    	return $ball;
    }
    
    public function rand($min, $amont) 
    {
    	$arr = array();
    	while (count($arr) < $min) 
        {
    		$j = ceil(rand(1, $amont));
    		if (!in_array($j, $arr)) 
            {
    			array_push($arr, str_pad($j, 2, "0", STR_PAD_LEFT));
    		}
    	}
    	sort($arr);
    	if ($min > 1) 
        {
            //校验连号
    		$flag = false;
    		$count = 0;
    		foreach ($arr as $k => $v) 
            {
    			if ($k > 0 && $arr[$k-1] + 1 == $v) 
                {
    				$count++;
    			}
    		}
    		if ($count < 4) 
            {
    			return implode(',', $arr);
    		}
    	}
        else 
        {
    		return implode(',', $arr);
    	}
    	$this->rand($min, $amont);
    }

}
