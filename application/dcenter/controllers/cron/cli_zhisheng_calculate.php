<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * 智胜直播数据智能预测计算
 * @author shigx
 *
 */
class Cli_Zhisheng_Calculate extends MY_Controller 
{
	private $apiUrl;
	private $matchs = array();
	public function __construct() 
	{
		parent::__construct();
		$this->load->library('tools');
		$this->load->model('api_zhisheng_model', 'api_model');
		$this->config->load('jcMatch');
		$this->apiUrl = $this->config->item('apiUrl');
		
		$matchs = $this->api_model->getZqMatchs();
		$matchData = array();
		foreach ($matchs as $match)
		{
		    //只对竞彩未开赛的场次处理
		    if($match['state'] != '0' || empty($match['xid']))
		    {
		        continue;
		    }
		    $matchData[$match['mid']] = $match;
		}
		
		$this->matchs = $matchData;
	}
	
	public function index()
	{    
	    //足球最近、历史交锋数据统计
	    $this->zqMatchStatic($this->matchs);
	    //球队排名统计
	    $this->zqMatchRank($this->matchs);
	    //足球99家欧赔
	    $this->zqMatchOdds($this->matchs);
	    //必发销量
	    $this->zqTransaction($this->matchs);
	    //六维图数据计算
	    $this->zqMatchAiData($this->matchs);
	}
	
	/**
	 * 球队排名数据
	 * @param unknown $matchs
	 */
	private function zqMatchRank($matchs)
	{
	    $insertData = array();
	    foreach ($matchs as $match)
	    {
	        $hurl= $this->apiUrl . "home/base/?oid=1029&tid={$match['htid']}";
	        $content = $this->tools->request($hurl);
	        $respone = json_decode($content, true);
	        $hrank = '';
	        $arank = '';
	        if($respone['code'] == '0000')
	        {
	            $num = count($respone['row']);
	            foreach ($respone['row'] as $key => $value)
	            {
	                //主队
	                if($value['tid'] == $match['htid'])
	                {
	                    $w = $value['w'] ? $value['w'] : 0;
	                    $d = $value['d'] ? $value['d'] : 0;
	                    $l = $value['l'] ? $value['l'] : 0;
	                    $hrank = $key + 1 . ',' . $num . ',' . $w . ',' . $d . ',' . $l;
	                }
	                //主客队在同联赛的话 客队直接处理
	                if($value['tid'] == $match['atid'])
	                {
	                    $w = $value['w'] ? $value['w'] : 0;
	                    $d = $value['d'] ? $value['d'] : 0;
	                    $l = $value['l'] ? $value['l'] : 0;
	                    $arank = $key + 1 . ',' . $num . ',' . $w . ',' . $d . ',' . $l;
	                }
	            }
	        }
	        if(empty($arank))
	        {
	            $aurl= $this->apiUrl . "home/base/?oid=1029&tid={$match['atid']}";
	            $content = $this->tools->request($aurl);
	            $respone = json_decode($content, true);
	            if($respone['code'] == '0000')
	            {
	                $num = count($respone['row']);
	                foreach ($respone['row'] as $key => $value)
	                {
	                    //客队
	                    if($value['tid'] == $match['atid'])
	                    {
	                        $w = $value['w'] ? $value['w'] : 0;
	                        $d = $value['d'] ? $value['d'] : 0;
	                        $l = $value['l'] ? $value['l'] : 0;
	                        $arank = $key + 1 . ',' . $num . ',' . $w . ',' . $d . ',' . $l;
	                    }
	                }
	            }
	        }
	        $data = array(
	            'mid' => $match['mid'],
	            'hrank' => $hrank,
	            'arank' => $arank
	        );
	        $insertData[] = $data;
	    }
	    
	    if($insertData)
	    {
	        $this->api_model->saveZqCalculate($insertData);
	    }
	}
	
	/**
	 * 爆冷、六维图计算
	 * @param unknown $matchs
	 */
	private function zqMatchAiData($matchs)
	{
	    if($matchs)
	    {
	        $mids = array_keys($matchs);
	        $result = $this->api_model->getZqCalculate($mids);
	        $insertData = array();
	        foreach ($result as $match)
	        {
	            $data = array();
	            $data['mid'] = $match['mid'];
	            $data['upset'] = $this->getZqUpset($match['odds'], $match['transaction'], $match['prediction']);
	            $data['recommend'] = $this->getRecommend($match['prediction'], $data['upset']);
	            $match['sid'] = $matchs[$match['mid']]['sid'];
	            $match['htid'] = $matchs[$match['mid']]['htid'];
	            $match['atid'] = $matchs[$match['mid']]['atid'];
	            $data['aidata'] = $this->getZqAiData($match);
	            $insertData[] = $data;
	        }
	        
	        if($insertData)
	        {
	            $this->api_model->saveZqCalculate($insertData);
	        }
	    }
	}
	
	/**
	 * 足球ai六维图数据计算
	 * @param unknown $match
	 */
	private function getZqAiData($match)
	{
	    $data = array(
	        'home' => array('strength' => 0, 'recentState' => 0, 'historyBattle' => 0, 'oddRecommend' => 0, 'betOptimistic' => 0, 'tradingHeat' => 0),
	        'away' => array('strength' => 0, 'recentState' => 0, 'historyBattle' => 0, 'oddRecommend' => 0, 'betOptimistic' => 0, 'tradingHeat' => 0)
	    );
	    //球队实力 - 主队
	    if($match['hrank'])
	    {
	        $hrank = explode(',', $match['hrank']);
	        if($hrank[1] > 1)
	        {
	            $denominator = (1-pow($hrank[1], 2));
	            $data['home']['strength'] = round(9 / $denominator * pow($hrank[0], 2) + (10 - 9 / $denominator), 1);
	        }
	    }
	    //如果是世界杯比赛特殊处理  世界杯结束后该逻辑可言删除
	    if($match['sid'] == '7574') {
	        $data['home']['strength'] = $this->sjbStrength($match['htid']);
	    }
	    //球队实力 - 客队
	    if($match['arank'])
	    {
	        $arank = explode(',', $match['arank']);
	        if($arank[1] > 1)
	        {
	            $denominator = (1-pow($arank[1], 2));
	            $data['away']['strength'] = round(9 / $denominator * pow($arank[0], 2) + (10 - 9 / $denominator), 1);
	        }
	    }
	    //如果是世界杯比赛特殊处理  世界杯结束后该逻辑可言删除
	    if($match['sid'] == '7574') {
	        $data['away']['strength'] = $this->sjbStrength($match['atid']);
	    }
	    //近期状态 - 主队
	    if($match['hrecent'])
	    {
	        $hrecent = explode(',', $match['hrecent']);
	        $denominator = $hrecent[0] + $hrecent[1] + $hrecent[2];
	        if($denominator > 0)
	        {
	            $data['home']['recentState'] = round(($hrecent[0] / $denominator + 0.5 * $hrecent[1] / $denominator) * (($denominator + 10) / 2), 1);
	        }
	    }
	    //近期状态 - 客队
	    if($match['arecent'])
	    {
	        $arecent = explode(',', $match['arecent']);
	        $denominator = $arecent[0] + $arecent[1] + $arecent[2];
	        if($denominator > 0)
	        {
	            $data['away']['recentState'] = round(($arecent[0] / $denominator + 0.5 * $arecent[1] / $denominator) * (($denominator + 10) / 2), 1);
	        }
	    }
	    
	    //历史交锋 - 主队
	    if($match['hhistorical'])
	    {
	        $hhistorical = explode(',', $match['hhistorical']);
	        $denominator = $hhistorical[0] + $hhistorical[1] + $hhistorical[2];
	        if($denominator > 0)
	        {
	            $data['home']['historyBattle'] = round(($hhistorical[0] / $denominator + 0.5 * $hhistorical[1] / $denominator) * (($denominator + 10) / 2), 1);
	        }
	    }
	    //历史交锋 - 客队
	    if($match['ahistorical'])
	    {
	        $ahistorical = explode(',', $match['ahistorical']);
	        $denominator = $ahistorical[0] + $ahistorical[1] + $ahistorical[2];
	        if($denominator > 0)
	        {
	            $data['away']['historyBattle'] = round(($ahistorical[0] / $denominator + 0.5 * $ahistorical[1] / $denominator) * (($denominator + 10) / 2), 1);
	        }
	    }
	    $odds = explode(',', $match['odds']);
	    //欧赔推荐 - 主队
	    if($odds[0] > 0)
	    {
	        $data['home']['oddRecommend'] = round(9 / $odds[0], 1);
	    }
	    //欧赔推荐 - 客队
	    if($odds[2] > 0)
	    {
	        $data['away']['oddRecommend'] = round(9 / $odds[2], 1);
	    }
	    //亚盘看好
	    if($match['bet'])
	    {
	        $bet = explode(',', $match['bet']);
	        if($bet[0] >= 0)
	        {
	            $bValue = $this->getZqBetValue($bet[0]);
	            $data['home']['betOptimistic'] = $bValue ? $bValue : 0;
	            $data['away']['betOptimistic'] = $data['home']['betOptimistic'] > 0 ? (10 - $data['home']['betOptimistic']) : 0;
	        }
	        else
	        {
	            $bValue = $this->getZqBetValue(abs($bet[0]));
	            $data['away']['betOptimistic'] = $bValue ? $bValue : 0;
	            $data['home']['betOptimistic'] = $data['away']['betOptimistic'] > 0 ? (10 - $data['away']['betOptimistic']) : 0;
	        }
	    }
	    //交易热度
	    if($match['transaction'])
	    {
	        $transaction = explode(',', $match['transaction']);
	        $denominator = $transaction[0] + $transaction[1] + $transaction[2];
	        if($denominator > 0)
	        {
	            if($denominator <= 10000)
	            {
	                $coefficient = 6;
	            }
	            elseif($denominator <= 100000)
	            {
	                $coefficient = 7;
	            }
	            elseif ($denominator <= 1000000)
	            {
	                $coefficient = 8;
	            }
	            elseif ($denominator <= 10000000)
	            {
	                $coefficient = 9;
	            }
	            else
	            {
	                $coefficient = 10;
	            }
	            $data['home']['tradingHeat'] = round($transaction[0] / $denominator * $coefficient, 1);
	            $data['away']['tradingHeat'] = round($transaction[2] / $denominator * $coefficient, 1);
	        }
	    }
	    
	    return json_encode($data);
	}
	
	/**
	 * 返回亚盘盘口对应的值
	 * @param unknown $bet
	 * @return number
	 */
	private function getZqBetValue($bet)
	{
	    $arr = array(
	        '0' => 5,
	        '0.25' => 5.1,
	        '0.5' => 5.4,
	        '0.75' => 5.7,
	        '1' => 6,
	        '1.25' => 6.4,
	        '1.5' => 6.8,
	        '1.75' => 7.3,
	        '2' => 7.8,
	        '2.25' => 8.3,
	        '2.5' => 8.8,
	        '2.75' => 9.4,
	        '3' => 10,
	    );
	    $bet = $bet >= 3 ? 3 : $bet;
	    $bet = (string)$bet;
	    
	    return $arr[$bet];
	}
	
	/**
	 * 返回亚盘盘口对应汉字
	 * @param unknown $bet
	 * @return string
	 */
	private function getZqBet($bet)
	{
	    $arr = array(
	        '0' => '',
	        "0.25" => '平半球',
	        '0.5' => '半球',
	        '0.75' => '半/一球',
	        '1' => '一球',
	        '1.25' => '一球/一球半',
	        '1.5' => '球半',
	        '1.75' => '球半/二球',
	        '2' => '二球',
	        '2.25' => '二球/二球半',
	        '2.5' => '二球半',
	        '2.75' => '二球半/三球',
	        '3' => '三球',
	        '3.25' => '三球/三球半',
	        '3.5' => '三球半',
	        '3.75' => '三球半/四球',
	        '4' => '四球',
	        '4.25' => '四球/四球半',
	        '4.5' => '四球半',
	        '4.75' => '四球半/五球',
	        '5' => '五球',
	        '5.25' => '五球/五球半',
	        '5.5' => '五球半',
	        '5.75' => '五球半/六球',
	        '6' => '六球',
	    );
	    if($bet > 6)
	    {
	        return $bet . '球';
	    }
	    else
	    {
	        $bet = (string)$bet;
	        return $arr[$bet];
	    }
	}
	
	/**
	 * 足球爆冷计算
	 * @param unknown $odds
	 * @param unknown $transaction
	 * @param unknown $prediction
	 * @return number
	 */
	private function getZqUpset($odds, $transaction, $prediction)
	{
	    $return = 0;
	    if($odds && ($odds != '0,0,0'))
	    {
	        $odds = explode(',', $odds);
	        $transaction = explode(',', $transaction);
	        $prediction = explode(',', $prediction);
	        $money = $transaction[0] + $transaction[1] + $transaction[2];
	        if($money > 0)
	        {
	            asort($odds);
	            $oddTmp = array_flip($odds);
	            $key = array_shift($oddTmp);
	            $amoney = $transaction[$key];
	            $pa = $prediction[$key] / 100;
	            if($money <= 10000)
	            {
	                $s = 0.6;
	            }
	            elseif($money <= 100000)
	            {
	                $s = 0.7;
	            }
	            elseif ($money <= 1000000)
	            {
	                $s = 0.8;
	            }
	            elseif($money <= 10000000)
	            {
	                $s = 0.9;
	            }
	            else
	            {
	                $s = 1;
	            }
	            $value = ($amoney/$money - $pa) * $s;
	            if($value >= 0.2)
	            {
	                $return = 3;
	            }
	            elseif ($value >= 0.05)
	            {
	                $return = 2;
	            }
	            else
	            {
	                $return = 1;
	            }
	        }
	    }
	    
	    return $return;
	}
	
	/**
	 * 足球最近、历史交锋数据统计入口
	 * @param unknown $matchs
	 */
	private function zqMatchStatic($matchs)
	{
	    $insertData = array();
	    foreach ($matchs as $match)
	    {
	        $data = array();
	        $data['mid'] = $match['mid'];
	        $hmatchs = $this->api_model->getzqRecentMatch($match['htid']);
	        $data['hrecent'] = $this->zqWinCalcu($match['htid'], $hmatchs);
	        $amatchs = $this->api_model->getzqRecentMatch($match['atid']);
	        $data['arecent'] = $this->zqWinCalcu($match['atid'], $amatchs);
	        $history = $this->api_model->getzqHistoryMatch($match);
	        $data['hhistorical'] = $this->zqWinCalcu($match['htid'], $history);
	        $hTmp = explode(',', $data['hhistorical']);
	        $data['ahistorical'] = $hTmp['2'] . ',' . $hTmp['1'] . ',' . $hTmp['0'];
	        $insertData[] = $data;
	    }
	    
	    if($insertData)
	    {
	        $this->api_model->saveZqCalculate($insertData);
	    }
	}
	
	/**
	 * 足球99家平均欧赔获取
	 * @param unknown $matchs
	 */
	private function zqMatchOdds($matchs)
	{
	    $url = $this->config->item('api_bf') . "/apps/?lotyid=6&cid=0";
	    $content = $this->tools->request($url);
	    $apiData = json_decode($content, true);
	    $insertData = array();
	    if(is_array($apiData) && !empty($matchs))
	    {
	        $mids = array_keys($matchs);
	        foreach ($apiData as $match)
	        {
	            if(in_array($match['mid'], $mids))
	            {
	                $data = array();
	                $data['mid'] = $match['mid'];
	                $od = $match['odds']['od'] ? $match['odds']['od'] : 0;
	                $oa = $match['odds']['oa'] ? $match['odds']['oa'] : 0;
	                $oh = $match['odds']['oh'] ? $match['odds']['oh'] : 0;
	                $data['odds'] = $oh . ',' . $od . ',' . $oa;
	                $match['sq'] = floatval($match['sq']);
	                $bet = abs($match['sq']);
	                if($match['sq'] < 0 && $bet != 0)
	                {
	                    $betName = $this->getZqBet($bet);
	                    $data['bet'] = $match['sq'] . ',受让' . $betName . ' ' . $bet . ',' . '让' . $betName . ' ' . $bet;
	                }
	                elseif($bet == 0)
	                {
	                    
	                    $data['bet'] = $bet . ',平手' . ' ' . $bet . ',' . '平手' . ' ' . $bet;
	                }
	                else
	                {
	                    $betName = $this->getZqBet($bet);
	                    $data['bet'] = $match['sq'] . ',让' . $betName . ' ' . $bet . ',' . '受让' . $betName . ' ' . $bet;
	                }
	                
	                if($oh == 0 || $od == 0 || $oa == 0)
	                {
	                    //欧赔只要有一个未0就不进行计算
	                    $data['prediction'] = '0,0,0';
	                }
	                else
	                {
	                    $ohPercent = round(9/(10 * $oh), 2) * 100;
	                    $odPercent = round(9/(10 * $od), 2) * 100;
	                    $oaPercnet = 100 - $ohPercent - $odPercent;
	                    $oaPercnet = $oaPercnet < 0 ? 0 : $oaPercnet;
	                    $data['prediction'] = $ohPercent . ',' . $odPercent . ',' . $oaPercnet;         
	                }
	                
	                $insertData[] = $data;
	            }
	        }
	    }
	    
	    if($insertData)
	    {
	        $this->api_model->saveZqCalculate($insertData);
	    }
	}
	
	/**
	 * 必发交易量获取
	 * @param unknown $matchs
	 */
	private function zqTransaction($matchs)
	{
	    $url = $this->config->item('liaoUrl') . "betfair/statistic";
	    $insertData = array();
	    foreach ($matchs as $match)
	    {
	        $params = array(
	            'f' => $this->config->item('from'),
	            't' => time(),
	            'r' => $this->randStr(),
	            'mid' => $match['mid'],
	        );
	        
	        $params['sign'] = $this->getSign($params);
	        $respone = $this->tools->get($url, $params);
	        $data = array();
	        $data['mid'] = $match['mid'];
	        if($respone['code'] == '200')
	        {
	            $h = $respone['data']['deal']['3']['volume'] ? $respone['data']['deal']['3']['volume'] : 0;
	            $d = $respone['data']['deal']['1']['volume'] ? $respone['data']['deal']['1']['volume'] : 0;
	            $a = $respone['data']['deal']['0']['volume'] ? $respone['data']['deal']['0']['volume'] : 0;
	            $data['transaction'] = $h . ',' . $d . ',' . $a;
	        }
	        else
	        {
	            $data['transaction'] = '0,0,0';
	        }
	        
	        $insertData[] = $data;
	    }
	    
	    if($insertData)
	    {
	        $this->api_model->saveZqCalculate($insertData);
	    }
	}
	
	/**
	 * 足球计算对阵胜平负
	 * @param unknown_type $tid
	 * @param unknown_type $matchData
	 */
	private function zqWinCalcu($tid, $matchData)
	{
	    // 胜平负
	    $result = '0,0,0';
	    if($matchData)
	    {
	        $win = 0;
	        $draw = 0;
	        $lose = 0;
	        foreach ($matchData as $val)
	        {
	            if($tid == $val['atid'])
	            {
	                $win += ($val['aqt'] > $val['hqt']) ? 1 : 0;
	                $draw += ($val['aqt'] == $val['hqt']) ? 1 : 0;
	                $lose += ($val['aqt'] < $val['hqt']) ? 1 : 0;
	            }
	            else
	            {
	                $win += ($val['hqt'] > $val['aqt']) ? 1 : 0;
	                $draw += ($val['hqt'] == $val['aqt']) ? 1 : 0;
	                $lose += ($val['hqt'] < $val['aqt']) ? 1 : 0;
	            }
	        }
	        
	        $result = $win . ',' . $draw . ',' . $lose;
	    }
	    
	    return $result;
	}
	
	/**
	 * 智能推荐计算
	 * @param unknown $prediction
	 * @param unknown $upset
	 * @return string|string|unknown
	 */
	private function getRecommend($prediction, $upset)
	{
	    $recommend = '暂无';
	    if($prediction == '0,0,0' || $upset == 0) {
	        return $recommend;
	    }
	    
	    $prediction = explode(',', $prediction);
	    $oMap = array('主胜' => $prediction[0], '平' => $prediction[1], '客胜' => $prediction[2]);
	    //极易爆冷 取最低的2个玩法推荐
	    if($upset == 3) {
	        asort($oMap);
	        $tmpPer = array_slice($oMap, 0, 2);
	        $perKeys = array_keys($tmpPer);
	        $recommend = implode('、', $perKeys);
	    } else {
	        //普通取最高的1-2个玩法推荐
	        arsort($oMap);
	        $tmpPer = array_slice($oMap, 0, 2);
	        $perKeys = array_keys($tmpPer);
	        if(array_shift($oMap) >= 50) {
	            $recommend = $perKeys[0];
	        } else {
	            $recommend = implode('、', $perKeys);
	        }
	    }
	    
	    return $recommend;
	}
	
	/**
	 * 生成随机数
	 * @param number $length
	 * @return string
	 */
	private function randStr($length = 8)
	{
	    // 允许的字符串
	    $chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
	    $charsLength = strlen($chars) - 1;
	    $str = '';
	    for ($i = 0; $i < $length; $i++)
	    {
	        $str .= $chars[mt_rand(0, $charsLength)];
	    }
	    
	    return $str;
	}
	
	/**
	 * 获得签名信息
	 * @param unknown $params
	 * @return string
	 */
	private function getSign($params)
	{
	    ksort($params);
	    $str = '';
	    $secretKey = $this->config->item('secretKey');
	    foreach ($params as $key => $value) 
	    {
	        $str .= "{$key}{$secretKey}{$value}";
	    }
	    
	    $sign = md5($str);
	    
	    return $sign;
	}
	
	/**
	 * 2018世界杯球队实力特殊处理  世界杯结束可删除
	 * @param unknown $tid
	 * @return number
	 */
	private function sjbStrength($tid)
	{
	    $strength = array(
	        '431' => 10, //德国
	        '510' => 9.82, //巴西
	        '186' => 9.64, //葡萄牙
	        '580' => 9.46, //阿根廷
	        '481' => 9.28, //比利时
	        '467' => 9.1, //波兰
	        '410' => 8.92, //法国
	        '51'  => 8.74, //西班牙
	        '742' => 8.56, //秘鲁
	        '171' => 8.38, //瑞士
	        '13'  => 8.2, //英格兰
	        '383' => 8.02, //哥伦比亚
	        '214' => 7.84, //墨西哥
	        '52'  => 7.66, //乌拉圭
	        '310' => 7.48, //克罗地亚
	        '433' => 7.3, //丹麦
	        '471' => 7.12, //冰岛
	        '381' => 6.94, //哥斯达黎加
	        '172' => 6.76, //瑞典
	        '907' => 6.58, //突尼斯
	        '565' => 6.4, //埃及
	        '164' => 6.22, //塞内加尔
	        '16'  => 6.04, //伊朗
	        '165' => 5.86, //塞尔维亚
	        '204' => 5.68, //尼日利亚
	        '632' => 5.5, //澳大利亚
	        '173' => 5.32, //日本
	        '912' => 5.14, //摩洛哥
	        '834' => 4.96, //巴拿马
	        '208' => 4.78, //韩国
	        '152' => 4.6, //沙特
	        '414' => 4.42, //俄罗斯
	    );
	    
	    return $strength[$tid];
	}
}
