<?php
defined('BASEPATH') OR die('No direct script access allowed');

class Split
{
    const SFC = 11;
    const RJ = 19;
    const PLS = 33;
    const PLW = 35;
    const BJDC = 41;
    const JCZQ = 42;
    const JCLQ = 43;
    const SSQ = 51;
    const FCSD = 52;
    const QXC = 10022;
    const SYYDJ = 21406;
    const QLC = 23528;
    const DLT = 23529;
    const GJ = 44;
    const GYJ = 45;
    const JXSYXW = 21407;
    const KS = 53;
    const JLKS = 56;
    const JXKS = 57;
    const HBSYXW = 21408;
    const KLPK = 54;
    const CQSSC = 55;
    const GDSYXW = 21421;

    private $playTypeCnName = array(
        self::PLS => array(
            '0' => '',
            '1' => '直选', 
            '2' => '组三', 
            '3' => '组六',
        ),
        self::JCZQ => array(
            'SPF'   => '胜平负',
            'RQSPF' => '让球胜平负',
            'CBF'   => '比分',
            'JQS'   => '总进球',
            'BQC'   => '半全场',
        ),
        self::JCLQ => array(
            'SF'    => '胜负',
            'RFSF'  => '让分',
            'DXF'   => '大小分',
            'SFC'   => '胜分差',
        ),
        self::FCSD => array(
            '0' => '',
            '1' => '直选', 
            '2' => '组三', 
            '3' => '组六',
        ),
        self::SYYDJ => array(
            '0'   => '',
            '1'   => '前一直选',        
            '2'   => '任二',    
            '3'   => '任三',    
            '4'   => '任四',    
            '5'   => '任五',    
            '6'   => '任六',    
            '7'   => '任七',    
            '8'   => '任八',    
            '9'   => '前二直选',  
            '10'   => '前三直选',  
            '11'   => '前二组选',  
            '12'   => '前三组选',
            '01'   => '前一直选',        
            '02'   => '任二',    
            '03'   => '任三',    
            '04'   => '任四',    
            '05'   => '任五',    
            '06'   => '任六',    
            '07'   => '任七',    
            '08'   => '任八',    
            '09'   => '前二直选',
            '13'   => '乐选3',
            '14'   => '乐选4',
            '15'   => '乐选5',
        ),
        self::JXSYXW => array(
            '0'   => '',
            '1'   => '前一直选',        
            '2'   => '任二',    
            '3'   => '任三',    
            '4'   => '任四',    
            '5'   => '任五',    
            '6'   => '任六',    
            '7'   => '任七',    
            '8'   => '任八',    
            '9'   => '前二直选',  
            '10'   => '前三直选',  
            '11'   => '前二组选',  
            '12'   => '前三组选',
            '01'   => '前一直选',        
            '02'   => '任二',    
            '03'   => '任三',    
            '04'   => '任四',    
            '05'   => '任五',    
            '06'   => '任六',    
            '07'   => '任七',    
            '08'   => '任八',    
            '09'   => '前二直选',
        ),
    	self::HBSYXW => array(
    		'0'   => '',
    		'1'   => '前一直选',
    		'2'   => '任二',
    		'3'   => '任三',
    		'4'   => '任四',
    		'5'   => '任五',
    		'6'   => '任六',
    		'7'   => '任七',
    		'8'   => '任八',
    		'9'   => '前二直选',
    		'10'   => '前三直选',
    		'11'   => '前二组选',
    		'12'   => '前三组选',
    		'01'   => '前一直选',
    		'02'   => '任二',
    		'03'   => '任三',
    		'04'   => '任四',
    		'05'   => '任五',
    		'06'   => '任六',
    		'07'   => '任七',
    		'08'   => '任八',
    		'09'   => '前二直选',
    	),
        self::GDSYXW => array(
            '0'   => '',
            '1'   => '前一直选',
            '2'   => '任二',
            '3'   => '任三',
            '4'   => '任四',
            '5'   => '任五',
            '6'   => '任六',
            '7'   => '任七',
            '8'   => '任八',
            '9'   => '前二直选',
            '10'   => '前三直选',
            '11'   => '前二组选',
            '12'   => '前三组选',
            '01'   => '前一直选',
            '02'   => '任二',
            '03'   => '任三',
            '04'   => '任四',
            '05'   => '任五',
            '06'   => '任六',
            '07'   => '任七',
            '08'   => '任八',
            '09'   => '前二直选',
        ),
        self::KS => array(
            '0' => '',
            '1' => '和值',
            '2' => '三同号通选',
            '3' => '三同号单选',
            '4' => '三不同号',
            '5' => '三连号通选',
            '6' => '二同号复选',
            '7' => '二同号单选',
            '8' => '二不同号',
        ),
        self::JLKS => array(
            '0' => '',
            '1' => '和值',
            '2' => '三同号通选',
            '3' => '三同号单选',
            '4' => '三不同号',
            '5' => '三连号通选',
            '6' => '二同号复选',
            '7' => '二同号单选',
            '8' => '二不同号',
        ),    
        self::JXKS => array(
            '0' => '',
            '1' => '和值',
            '2' => '三同号通选',
            '3' => '三同号单选',
            '4' => '三不同号',
            '5' => '三连号通选',
            '6' => '二同号复选',
            '7' => '二同号单选',
            '8' => '二不同号',
        ),
        self::KLPK => array(
            '0'   =>  '',
            '1'   =>  '任选一',
            '2'   =>  '任选二单式',
            '21'  =>  '任选二复式',
            '22'  =>  '任选二胆拖',
            '3'   =>  '任选三单式',
            '31'  =>  '任选三复式',
            '32'  =>  '任选三胆拖',
            '4'   =>  '任选四单式',
            '41'  =>  '任选四复式',
            '42'  =>  '任选四胆拖',
            '5'   =>  '任选五单式',
            '51'  =>  '任选五复式',
            '52'  =>  '任选五胆拖',
            '6'   =>  '任选六单式',
            '61'  =>  '任选六复式',
            '62'  =>  '任选六胆拖',
            '7'   =>  '同花',
            '8'   =>  '同花顺',
            '9'   =>  '顺子',
            '10'  =>  '豹子',
            '11'  =>  '对子',
        ),
        self::CQSSC => array(
            '1'   =>  '大小单双',
            '10'  =>  '一星单式',
            '20'  =>  '二星单式',
            '21'  =>  '二星复式',
            '23'  =>  '二星组选',
            '25'  =>  '二星和值',
            '26'  =>  '二星组选和值',
            '27'  =>  '二星组选复式',
            '30'  =>  '三星单式',
            '31'  =>  '三星复式',
            '33'  =>  '三星组三单式',
            '34'  =>  '三星组六单式',
            '35'  =>  '三星和值',
            '36'  =>  '三星组选和值',
            '37'  =>  '三星组三复式',
            '38'  =>  '三星组六复式',
            '40'  =>  '五星单式',
            '41'  =>  '五星复式',
            '43'  =>  '五星通选',
        ),   
    );
    private $lx = array(
            'lx3' => array('q3','z3','r3'),
            'lx4' => array('r44','r43'),
            'lx5' => array('r55','r54')
        );
    private $lx_detail = array('q3'=>'乐选3一等奖','z3'=>'乐选3二等奖','r3'=>'乐选3三等奖','r44'=>'乐选4一等奖','r43'=>'乐选4二等奖','r55'=>'乐选5一等奖','r54'=>'乐选5二等奖');
    private $me = array(
        'DLT' => '23529',
        'QLC' => '23528',
        'QXC' => '10022',
        'SYXW'=> '21406',
        'SSQ' => '51',
        'PL3' => '33',
        'PLS' => '33',
        'PL5' => '35',
        'PLW' => '35',
        'FCSD'=> '52',
        'SFC' => '11',
        'RJ' => '19',
        'JXSYXW' => '21407',
        'KS' => '53',
        'JLKS' => '56',
        'JXKS' => '57',
        'JCZQ'=> '42',
        'JCLQ'=> '43',
    	'HBSYXW' => '21408',
        'KLPK' => '54',
        'CQSSC' => '55',
        'GDSYXW' => '21421',
    );

    public function computeStakeNum($order)
    {
        if ($order['status'] <= 1000)
        {
            return 0;
        }
        $stakeNum = 0;
        $multi = $order['multi'];
        if (in_array($order['lid'], array(42, 43)))
        {
            $stakeNum = $order['bonusDetail'] * $multi;
        }
        elseif (in_array($order['lid'], array(44, 45)))
        {
        	$stakeNum = $order['bonusDetail'] * $multi;
        }
        elseif (in_array($order['lid'], array(19, 33, 35, 52, 21406, 21407, 21408, 54, 55, 21421)))
        {
            $bonusDetailAry = json_decode($order['bonusDetail'], TRUE);
            if(is_array($bonusDetailAry[0]))
            {
                $playType = array_search(array_keys($bonusDetailAry[0]), $this->lx);
                if(in_array($playType,array('lx3','lx4','lx5')))
                {
                    $stakeNum = $this->getLxDetail($bonusDetailAry[0],$playType,$multi);
                }else{
                     $stakeNum = 0;
                }
                
            }else{
              $stakeNum = array_sum($bonusDetailAry) * $multi;  
            }
        }
        elseif(in_array($order['lid'], array(53, 56, 57)))
        {
        	$bonusDetailAry = json_decode($order['bonusDetail'], TRUE);
        	$stakeNum = $bonusDetailAry[0] > 0 ? (1 * $multi) : 0;
        }
        elseif (in_array($order['lid'], array(11, 51, 10022, 23528, 23529)))
        {
            $bonusDetailAry = json_decode($order['bonusDetail'], TRUE);
            $stakeNumAry = array();
            foreach ($bonusDetailAry as $key => $value)
            {
                $temp = is_array($value) ? array_sum($value) : $value;
                if ($temp > 0)
                {
                    array_push($stakeNumAry, "{$key}等奖" . ($temp * $multi) . "注");
                }
            }
            $stakeNum = $stakeNumAry ? implode(',', $stakeNumAry) : 0;
        }

        return $stakeNum;
    }
    /**
     * [getLxDetail 乐选详情字符串]
     * @author LiKangJian 2017-04-19
     * @param  [type] $bonusDetailAry [description]
     * @param  [type] $playType       [description]
     * @param  [type] $multi          [description]
     * @return [type]                 [description]
     */
    public function getLxDetail($bonusDetailAry,$playType,$multi)
    {
        $stakeNumAry = array();
        foreach ($bonusDetailAry as $k => $v) 
        {
            if($v>0)
            {
                array_push($stakeNumAry,$this->lx_detail[$k].($v * $multi).'注');
            }
            
        }
        return implode('/', $stakeNumAry);
    }
    // 获取玩法
    public function playTypeName($order)
    {
        $playTypeArr = array();

        if( $order['lid'] == $this->me['JCLQ'] )
        {
            $casts = explode(';', $order['codes']);
            foreach ($casts as $key => $cast) 
            {
                $castCodes = explode('|', $cast);
                $matchs = explode(',', $castCodes[1]);
                foreach ($matchs as $match) 
                {
                    $castInfo = $this->parseJcCast($order['lid'], $match);
                    $playType = $this->playTypeCnName[$order['lid']][$castInfo['playTypeCode']];
                    $playType = $playType ? $playType : '--';
                    array_push($playTypeArr, $playType);
                }
                
            }
            $playTypeName = implode(',', array_unique($playTypeArr));
        }
        elseif( $order['lid'] == $this->me['JCZQ'] )
        {
            if($order['playType'] == 6)
            {
                $playTypeName = '单关';
            }
            elseif($order['playType'] == 7)
            {
                $playTypeName = '奖金优化';
            }
            else
            {
                $casts = explode(';', $order['codes']);
                foreach ($casts as $key => $cast) 
                {
                    $castCodes = explode('|', $cast);
                    $matchs = explode(',', $castCodes[1]);
                    foreach ($matchs as $match) 
                    {
                        $castInfo = $this->parseJcCast($order['lid'], $match);
                        $playType = $this->playTypeCnName[$order['lid']][$castInfo['playTypeCode']];
                        $playType = $playType ? $playType : '--';
                        array_push($playTypeArr, $playType);
                    }  
                }
                $playTypeName = implode(',', array_unique($playTypeArr));
            }
        }
        else
        {
            $casts = explode(';', $order['codes']);
            foreach ($casts as $code)
            {   
                $castInfo = $this->parseNumCast($order['lid'], $code);
                $playType = $this->playTypeCnName[$order['lid']][$castInfo['playTypeCode']];
                $playType = $playType ? $playType : '--';
                array_push($playTypeArr, $playType);
            }
            $playTypeName = implode(',', array_unique($playTypeArr));
        }
        return $playTypeName;
    }

    // 数字彩
    private function parseNumCast($lotteryId, $code)
    {
        $parts = explode(':', $code);
        $numbers = $parts[0];
        $playType = $parts[1];
        $hasDan = strpos($code, '$') > 0;
        $hasPost = strpos($code, '|') > 0;
        $preDan = array();
        $preTuo = array();
        $postDan = array();
        $postTuo = array();
        if($lotteryId == $this->me['DLT'] || $lotteryId == $this->me['SSQ'])
        {
            $preBalls = explode('|', $numbers);
            $pre = $preBalls[0];
            $post = $preBalls[1];
            if($hasDan)
            {
                $pres = explode('$', $pre);
                $preDan = explode(',', $pres[0]);
                $preTuo = explode(',', $pres[1]);
                $postSplit = explode('$', $post);
                if(count($postSplit) == 2)
                {
                    $postDan = explode(',', $postSplit[0]);
                    $postTuo = explode(',', $postSplit[1]);
                }
                else
                {
                    $postDan = array();
                    $postTuo = explode(',', $postSplit[0]);
                }
            }
            else
            {
                $preTuo = explode(',', $pre);
                $postTuo = explode(',', $post);
            }
        }
        else if($lotteryId == $this->me['SYXW'] || $lotteryId == $this->me['JXSYXW'] || $lotteryId == $this->me['GDSYXW'])
        {
            if($playType == '09' || $playType == '10')
            {
                $preTuo = explode('|', $numbers);
            }
            else
            {
                // 胆拖
                if($hasDan)
                {
                    $pres = explode('$', $numbers);
                    $preDan = explode(',', $pres[0]);
                    $preTuo = explode(',', $pres[1]);
                }
                else
                {
                    $preTuo = explode(',', $numbers);
                }   
            }
        }
        else
        {
            $preTuo = explode(',', $numbers);
        }

        return array(
            'playTypeCode' => $parts[1],
            'modeCode' => $parts[2],
            'preDan' => $preDan,
            'preTuo' => $preTuo,
            'postDan' => $postDan,
            'postTuo' => $postTuo
        );
    }

    // 竞足竞篮
    public function parseJcCast($lotteryId, $match)
    {
        $rule = '/(\w+)>.*?/';
        $playType = '--';
        preg_match($rule, $match, $matches);
        $playType = $matches[1] ? $matches[1] : '--';   

        return array(
            'playTypeCode' => $playType,
        );
    }
}