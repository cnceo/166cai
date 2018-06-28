<?php
/**
 * 大乐透历史对比服务类
 * @author shigx
 *
 */
class DltCompareResult extends CI_Controller
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
    		$awards = $this->compare_result_model->getDltAward($issueCount);
    		$this->cache->save($cacheKey, serialize($awards), 600);
    	}
    	
    	$result = array(
    		'0' => array('max' => 0, 'total' => 0, 'detail' => array()),
    		'1' => array('max' => 0, 'total' => 0, 'detail' => array()),
    		'2' => array('max' => 0, 'total' => 0, 'detail' => array()),
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
    			if($data['playType'] == 135)
    			{
    				$mRedDan = count(array_intersect($data['rsalts'], $rballs));
    				$mRedTuo = count(array_intersect($data['rballs'], $rballs));
    				$mBlueDan = count(array_intersect($data['bsalts'], $bballs));
    				$mBlueTuo = count(array_intersect($data['bballs'], $bballs));
    				$this->dtBetting($allNums, $data['rsaltCount'], $data['rCount'], $data['bsaltCount'], $data['bCount'], $mRedDan, $mRedTuo, $mBlueDan, $mBlueTuo);
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
    			if(in_array($level, array(0,1,2)))
    			{
    				$result[$level]['max'] = ($allNums[$level] > 0 && $bonusDetail[$bLevel]['jb']['dzjj'] > $result[$level]['max']) ? $bonusDetail[$bLevel]['jb']['dzjj'] : $result[$level]['max']; 
    			}
    			if($allNums[$level] > 0 && count($result[$level]['detail']) < 5)
    			{
    				$aTmp = array(
    					'issue' => $award['issue'],
    					'awardNum' => $award['awardNum'],
    					'bonus' => $bonusDetail[$bLevel]['jb']['dzjj']
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
    		
    		$oricode['playType'] = $code[1];
    		if($oricode['playType'] == 135)
    		{
    			$oricode['rsalts'] = array();
    			$oricode['rsaltCount'] = 0;
    			if(!empty($rmatches[1]))
    			{
    				$oricode['rsalts'] = explode(',', $rmatches[1]);
    				$oricode['rsaltCount'] = count($oricode['rsalts']);
    				if($oricode['rsaltCount'] > 4)
    				{
    					$allData = array(); //不符合条件串直接返回空
    					break;
    				}
    			}
    			$oricode['bsalts'] = array();
    			$oricode['bsaltCount'] = 0;
    			if(!empty($bmatches[1]))
    			{
    				$oricode['bsalts'] = explode(',', $bmatches[1]); //后驱胆
    				$oricode['bsaltCount'] = count($oricode['bsalts']);
    				if($oricode['bsaltCount'] != 1)
    				{
    					$allData = array(); //不符合条件串直接返回空
    					break;
    				}
    			}
    			$oricode['rballs'] = explode(',', $rmatches[2]);
    			$oricode['rCount'] = count($oricode['rballs']);
    			$oricode['bballs'] = explode(',', $bmatches[2]);
    			$oricode['bCount'] = count($oricode['bballs']);
    			if(($oricode['rsaltCount'] + $oricode['rCount']) < 5 || ($oricode['rsaltCount'] + $oricode['rCount']) > 35)
    			{
    				$allData = array(); //不符合条件串直接返回空
    				break;
    			}
    			if(($oricode['bsaltCount'] + $oricode['bCount']) < 2 || ($oricode['bsaltCount'] + $oricode['bCount']) > 12)
    			{
    				
    				$allData = array(); //不符合条件串直接返回空
    				break;
    			}
    		}
    		else
    		{
    			$oricode['rballs'] = explode(',', $rmatches[2]);
    			$oricode['rCount'] = count($oricode['rballs']);
    			$oricode['bballs'] = explode(',', $bmatches[2]);
    			$oricode['bCount'] = count($oricode['bballs']);
    			if($oricode['rCount'] < 5 || $oricode['rCount'] > 35)
    			{
    				$allData = array(); //不符合条件串直接返回空
    				break;
    			}
    			if($oricode['bCount'] < 2 || $oricode['bCount'] > 12)
    			{
    				$allData = array(); //不符合条件串直接返回空
    				break;
    			}
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
    	for($u = 0; $u <= $mRed; $u++)
    	{
    		for($a = 0; $a <= $mblue; $a++)
    		{
    			$level = '';
    			$level = $this->rank($u, $a);
    			if($level != -1)
    			{
    				$allNums[$level] += $this->libcomm->combine($mRed, $u) * $this->libcomm->combine($red - $mRed, 5 - $u) * $this->libcomm->combine($blue - $mblue, 2 - $a) * $this->libcomm->combine($mblue, $a);
    			}
    		}
    	}
    }
    
    /**
     * 判断中奖等级
     * @param int $mRed		红球命中
     * @param int $mBlue	蓝球命中
     */
    private function rank($mRed, $mBlue)
    {
    	$num = $mRed + $mBlue;
    	switch ($num)
    	{
    		case 7:
    			return 0;
    		case 6:
    			return $mBlue == 1 ? 1 : 2;
    		case 5:
    			return $mBlue === 0 ? 2 : 3;
    		case 4:
    			return 4;
    		case 3:
    			return 5;
    		case 2:
    			return $mRed == 0 ? 5 : -1;
    		default:
    			return -1;
    	}
    }
    
    /**
     * 胆拖玩法计算中奖注数
     * @param array $allNums	中奖注数数组
     * @param int $redDan		用户选择红球胆码个数
     * @param int $redTuo		用户选择红球拖码个数
     * @param int $blueDan		用户选择蓝球胆码个数
     * @param int $blueTuo		用户选择蓝球拖码个数
     * @param int $mRedDan		用户命中红球胆码个数
     * @param int $mRedTuo		用户命中红球拖码个数
     * @param int $mBlueDan		用户命中蓝球胆码个数
     * @param int $mBlueTuo		用户命中蓝球拖码个数
     */
	private function dtBetting(&$allNums, $redDan, $redTuo, $blueDan, $blueTuo, $mRedDan, $mRedTuo, $mBlueDan, $mBlueTuo)
	{
		$rBall = $mRedTuo <= (5 - $redDan) ? $mRedTuo : (5 - $redDan);
		$bBall = $mBlueTuo <= (2 - $blueDan) ? $mBlueTuo : (2 - $blueDan);
		$rTmp = $bTmp = array();
		for($u = 0; $u <= $rBall; $u++)
		{
			$rTmp[$u] = $u + $mRedDan;
		}
		for($a = 0; $a <= $bBall; $a++)
		{
			$bTmp[$a] = $a + $mBlueDan;
		}
		$level = '';
		for($u = 0; $u <= $rBall; $u++)
		{
			for ($a = 0; $a <= $bBall; $a++)
			{
				$level = $this->rank($rTmp[$u], $bTmp[$a]);
				if($level != -1)
				{
					$allNums[$level] += $this->libcomm->combine($redTuo - $mRedTuo, 5 - $redDan - $u) * $this->libcomm->combine($mRedTuo, $u) * $this->libcomm->combine($blueTuo - $mBlueTuo, 2 - $blueDan - $a) * $this->libcomm->combine($mBlueTuo, $a);
				}
			}
		}
	}
}
