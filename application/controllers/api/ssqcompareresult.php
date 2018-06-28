<?php
/**
 * 双色球历史对比服务类
 * @author shigx
 *
 */
class SsqCompareResult extends CI_Controller
{
    public function __construct() 
    {
        parent::__construct();
        $this->load->library('libcomm');
        $this->load->driver('cache', array('adapter' => 'redis'));
    }
	
    public function index()
    {	
    	$codes = $this->input->post('codes', true);
    	$issueCount = intval($this->input->post('issueCount', true));
    	$allData = $this->parseCodes($codes);
    	if(empty($allData))
    	{
    		$response = array(
    			'code' => 1,
    			'msg'  => '操作失败',
    			'data' => array(),
    		);
    		echo json_encode($response);
    		return ;
    	}
    	$cacheKey = __CLASS__ . $issueCount;
    	$awards = unserialize($this->cache->get($cacheKey));
    	if(empty($awards))
    	{
    		$this->load->model('compare_result_model');
    		$awards = $this->compare_result_model->getSsqAward($issueCount);
    		$this->cache->save($cacheKey, serialize($awards), 600);
    	}
    	
    	$result = array(
    		'0' => array('max' => 0, 'total' => 0, 'detail' => array()),
    		'1' => array('max' => 0, 'total' => 0, 'detail' => array()),
    		'2' => array('max' => 3000, 'total' => 0, 'detail' => array()),
    		'3' => array('max' => 200, 'total' => 0, 'detail' => array()),
    		'4' => array('max' => 10, 'total' => 0, 'detail' => array()),
    		'5' => array('max' => 5, 'total' => 0, 'detail' => array())
    	);
    	
    	foreach($awards as $award)
    	{
    		$allNums = array('0' => 0, '1' => 0, '2' => 0, '3' => 0, '4' => 0, '5' => 0);
    		$awards = explode('|', $award['awardNum']);
    		$rballs = explode(',', $awards[0]);
    		$bballs = explode(',', $awards[1]);
    		foreach ($allData as $data)
    		{
    			if($data['rsalts'])
    			{
    				$mRedDan = count(array_intersect($data['rsalts'], $rballs));
    				$mRedTuo = count(array_intersect($data['rballs'], $rballs));
    				$mBlue = count(array_intersect($data['bballs'], $bballs));
    				$this->dtBetting($allNums, $data['saltCount'], $data['rCount'], $data['bCount'], $mRedDan, $mRedTuo, $mBlue);
    			}
    			else
    			{
    				$mRed = count(array_intersect($data['rballs'], $rballs));
    				$mBlue = count(array_intersect($data['bballs'], $bballs));
    				$this->ptBetting($allNums, $data['rCount'], $data['bCount'], $mRed, $mBlue);
    			}
    		}
    		$bonusDetail = json_decode($award['bonusDetail'], true);
    		foreach ($result as $level => $value)
    		{
    			$aTmp = array();
    			$result[$level]['total'] += $allNums[$level];
    			$bLevel = ($level + 1) . 'dj';
    			if(in_array($level, array(0,1)))
    			{
    				$result[$level]['max'] = ($allNums[$level] > 0 && $bonusDetail[$bLevel]['dzjj'] > $result[$level]['max']) ? $bonusDetail[$bLevel]['dzjj'] : $result[$level]['max']; 
    			}
    			if($allNums[$level] > 0 && count($result[$level]['detail']) < 5)
    			{
    				$aTmp = array(
    					'issue' => $award['issue'],
    					'awardNum' => $award['awardNum'],
    					'bonus' => $bonusDetail[$bLevel]['dzjj']
    				);
    				$result[$level]['detail'][] = $aTmp;
    			}
    		}
    	}
    	$response = array(
    		'code' => 0,
    		'msg'  => '操作成功',
    		'data' => $result,
    	);
    	echo json_encode($response);
    }
    
    /**
     * 订单串解析
     * @param unknown_type $codestrs
     */
    private function parseCodes($codestrs)
    {
    	$allData = array();
    	$codestrs = explode(';', $codestrs);
    	foreach ($codestrs as $code)
    	{
    		if(empty($code))
    		{
    			$allData = array(); //不符合条件串直接返回空
    			break;
    		}
    		$code = explode(':', $code);
    		$codes = explode('|', $code[0]);
    		preg_match('/(?:(.*)\$)?(.*)/', $codes[0], $rmatches);
    		preg_match('/(?:(.*)\$)?(.*)/', $codes[1], $bmatches);
    		
    		$oricode['rsalts'] = array();
    		$oricode['saltCount'] = 0;
    		if(!empty($rmatches[1]))
    		{
    			$oricode['rsalts'] = explode(',', $rmatches[1]);
    			$oricode['saltCount'] = count($oricode['rsalts']);
    			if($oricode['saltCount'] < 1 || $oricode['saltCount'] > 5)
    			{
    				$allData = array(); //不符合条件串直接返回空
    				break;
    			}
    		}
    		
    		$oricode['rballs'] = explode(',', $rmatches[2]);
    		$oricode['rCount'] = count($oricode['rballs']);
    		$oricode['bballs'] = explode(',', $bmatches[2]);
    		$oricode['bCount'] = count($oricode['bballs']);
    		if(($oricode['saltCount'] + $oricode['rCount']) < 6 || ($oricode['saltCount'] + $oricode['rCount']) > 33)
    		{
    			$allData = array(); //不符合条件串直接返回空
    			break;
    		}
    		if($oricode['bCount'] < 1 || $oricode['bCount'] > 16)
    		{
    			$allData = array(); //不符合条件串直接返回空
    			break;
    		}
    		
    		array_push($allData, $oricode);
    	}
    	
    	return $allData;
    }
    
    /**
     * 普通玩法计算中奖注数
     * @param array $allNums	中奖注数数组
     * @param int $red			用户投注红球数
     * @param int $blue			用户投注蓝球数
     * @param int $mRed			用户命中红球数
     * @param int $mblue		用户命中蓝球数
     */
    private function ptBetting(&$allNums, $red, $blue, $mRed, $mblue)
    {
    	switch ($mRed)
    	{
    		case 0:
    		case 1:
    		case 2:
    			if($mblue > 0)
    			{
    				$allNums[5] += $this->libcomm->combine($red, 6);
    			}
    			break;
    		case 3:
    			if($mblue > 0)
    			{
    				$allNums[5] += $this->libcomm->combine($red - $mRed, 4) * $this->libcomm->combine($mRed, 2);
    				$allNums[5] += $this->libcomm->combine($red - $mRed, 5) * $this->libcomm->combine($mRed, 1);
    				$allNums[5] += $this->libcomm->combine($red - $mRed, 6);
    				$allNums[4] += $this->libcomm->combine($red - $mRed, 3);
    			}
    			break;
    		case 4:
    			if($mblue > 0)
    			{
    				$allNums[5] += $this->libcomm->combine($red - $mRed, 4) * $this->libcomm->combine($mRed, 2);
    				$allNums[5] += $this->libcomm->combine($red - $mRed, 5) * $this->libcomm->combine($mRed, 1);
    				$allNums[5] += $this->libcomm->combine($red - $mRed, 6);
    				$allNums[4] += $this->libcomm->combine($red - $mRed, 3) * $this->libcomm->combine($mRed, 3);
    				$allNums[4] += $this->libcomm->combine($red - $mRed, 2) * $this->libcomm->combine($mRed, 4) * ($blue - 1);
    				$allNums[3] += $this->libcomm->combine($red - $mRed, 2);
    			}
    			else
    			{
    				$allNums[4] += $this->libcomm->combine($red - $mRed, 2) * $blue;
    			}
    			break;
    		case 5:
    			if($mblue > 0)
    			{
    				$allNums[5] += $this->libcomm->combine($red - $mRed, 4) * $this->libcomm->combine($mRed, 2);
    				$allNums[5] += $this->libcomm->combine($red - $mRed, 5) * $this->libcomm->combine($mRed, 1);
    				$allNums[5] += $this->libcomm->combine($red - $mRed, 6);
    				$allNums[4] += $this->libcomm->combine($red - $mRed, 3) * $this->libcomm->combine($mRed, 3);
    				$allNums[4] += $this->libcomm->combine($red - $mRed, 2) * $this->libcomm->combine($mRed, 4) * ($blue - 1);
    				$allNums[3] += $this->libcomm->combine($red - $mRed, 2) * $this->libcomm->combine($mRed, 4);
    				$allNums[3] += $this->libcomm->combine($red - $mRed, 1) * $this->libcomm->combine($mRed, 5) * ($blue - 1);
    				$allNums[2] += $this->libcomm->combine($red - $mRed, 1);
    			}
    			else
    			{
    				$allNums[4] += $this->libcomm->combine($red - $mRed, 2) * $this->libcomm->combine($mRed, 4) * $blue;
    				$allNums[3] += $this->libcomm->combine($red - $mRed, 1) * $this->libcomm->combine($mRed, 5) * $blue;
    			}
    			break;
    		case 6:
    			if($mblue > 0)
    			{
    				$allNums[5] += $this->libcomm->combine($red - $mRed, 4) * $this->libcomm->combine($mRed, 2);
    				$allNums[5] += $this->libcomm->combine($red - $mRed, 5) * $this->libcomm->combine($mRed, 1);
    				$allNums[5] += $this->libcomm->combine($red - $mRed, 6);
    				$allNums[4] += $this->libcomm->combine($red - $mRed, 3) * $this->libcomm->combine($mRed, 3);
    				$allNums[4] += $this->libcomm->combine($red - $mRed, 2) * $this->libcomm->combine($mRed, 4) * ($blue - 1);
    				$allNums[3] += $this->libcomm->combine($red - $mRed, 2) * $this->libcomm->combine($mRed, 4);
    				$allNums[3] += $this->libcomm->combine($red - $mRed, 1) * $this->libcomm->combine($mRed, 5) * ($blue - 1);
    				$allNums[2] += $this->libcomm->combine($red - $mRed, 1) * $this->libcomm->combine($mRed, 5);
    				$allNums[1] += $blue - 1;
    				$allNums[0]++;
    			} else {
    				$allNums[4] += $this->libcomm->combine($red - $mRed, 2) * $this->libcomm->combine($mRed, 4) * $blue;
    				$allNums[3] += $this->libcomm->combine($red - $mRed, 1) * $this->libcomm->combine($mRed, 5) * $blue;
    				$allNums[1] += $blue;
    			}
    			break;
    	}
    }
    
    /**
     * 胆拖玩法计算中奖注数
     * @param array $allNums	中奖注数数组
     * @param int $redDan		用户选择红球胆码个数
     * @param int $redTuo		用户选择红球拖码个数
     * @param int $blue			用户选择蓝球个数
     * @param int $mRedDan		用户命中红球胆码个数
     * @param int $mRedTuo		用户命中红球拖码个数
     * @param int $mBlue		用户命中蓝球个数
     */
	private function dtBetting(&$allNums, $redDan, $redTuo, $blue, $mRedDan, $mRedTuo, $mBlue)
	{
		$tBall = $mRedTuo <= 6 - $redDan ? $mRedTuo : 6 -$redDan;
		$tArr = array();
		for($i = 0; $i <= $tBall; $i++)
		{
			$tArr[$i] = $mRedDan + $i;
		}
		$level = 0;
		if($mBlue == 1)
		{
			for($i = 0; $i <= $tBall; $i++)
			{
				$level = $this->rank($tArr[$i], 0);
				if($level != -1)
				{
					$allNums[$level] += $this->libcomm->combine($redTuo - $mRedTuo, 6 - $redDan - $i) * ($blue - 1) * $this->libcomm->combine($mRedTuo, $i);
				}
				$level = $this->rank($tArr[$i], 1);
				$allNums[$level] += $this->libcomm->combine($redTuo - $mRedTuo, 6 - $redDan - $i) * $this->libcomm->combine($mRedTuo, $i);
			}
		}
		if($mBlue == 0)
		{
			for($i = 0; $i <= $tBall; $i++)
			{
				$level = $this->rank($tArr[$i], 0);
				if($level != -1)
				{
					$allNums[$level] += $this->libcomm->combine($redTuo - $mRedTuo, 6 - $redDan - $i) * $blue * $this->libcomm->combine($mRedTuo, $i);
				}
			}
		}	
	}
	
	//确定中奖等级
	private function rank($parm1, $parm2)
	{
		$count = $parm1 + $parm2;
		switch ($count) {
			case 7:
				return 0;
			case 6:
				return $parm2 == 1 ? 2 : 1;
			case 5:
				return 3;
			case 4:
				return 4;
			default:
				return $parm2 == 1 ? 5 : -1;
		}
	}
}
