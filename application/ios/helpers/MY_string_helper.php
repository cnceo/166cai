<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

if(!function_exists('getStaticFile'))
{
	function getStaticFile($filePath)
	{
		$CI = &get_instance();
		//为了兼容vanish的GBK编码做如下处理
		$ver = 116;
		$host = $CI->config->item('domain');
		return '//' . $host . $filePath . '?v=' . $ver;
	}
}

if(! function_exists('ParseUnit'))
{
	/*
	 * $io 0=乘 1=除*/
	function ParseUnit($val, $io=0)
	{
		$unit = 1000;
		if($io)
		{
			$val = (doubleval($val) / $unit) * 10;
		}else 
		{
			$val = (doubleval($val) * $unit) / 10;
		}
		return $val;
	}
}

if(! function_exists('checkMoney'))
{
	/*
	 * $io 0=乘 1=除*/
	function checkMoney($val)
	{
		$result = FALSE;
		if($val > 0)
		{
			 $check = ceil($val) > $val;
			 $result = !$check;
		}
		return $result;
	}
}

if( ! function_exists('my_crumbs'))
{
	function my_crumbs()
	{
		$CI = &get_instance();
		list(, $con, $act, $pm) = $CI->router->uri->rsegments;
		$crumb_str = '';
		$crumbs = array(
			'ssq' => array('name' => '双色球', 'acts' => array()),
			'dlt' => array('name' => '大乐透', 'acts' => array()),
			'syxw' => array('name' => '老11选5', 'acts' => array()),
			'hall' => array('name' => '购彩大厅', 'acts' => array()),
			'kaijiang' => array('name' => '全国开奖', 'acts' => array()),
			'help' => array('name' => '帮助中心', 'acts' => array()),
		    'app_buy' => array('name' => '手机购彩', 'acts' => array()),
			'fcsd' => array('name' => '福彩3D', 'acts' => array()),
			'qlc' => array('name' => '七乐彩', 'acts' => array()),
			'pls' => array('name' => '排列3', 'acts' => array()),
			'qxc' => array('name' => '七星彩', 'acts' => array()),
			'plw' => array('name' => '排列5', 'acts' => array()),
			'sfc' => array('name' => '胜负彩', 'acts' => array()),
			'rj' => array('name' => '任选9', 'acts' => array()),
		
			'jczq' => array('name' => '竞彩足球', 'acts' => array(
				'hh'=>array('name' =>'混合过关', 'acts' => array()),
				'spf'=>array('name' =>'胜平负', 'acts' => array()),
				'rqspf'=>array('name' =>'让球胜平负', 'acts' => array()),
				'cbf'=>array('name' =>'比分', 'acts' => array()),
				'bqc'=>array('name' =>'半全场', 'acts' => array()),
				'bqc'=>array('name' =>'混合选', 'acts' => array()),
			)),
			'jclq' => array('name' => '竞彩篮球', 'acts' => array(
				'hh'=>array('name' =>'混合过关', 'acts' => array()),
				'rfsf'=>array('name' =>'让分胜负', 'acts' => array()),
				'sf'=>array('name' =>'胜负', 'acts' => array()),
				'dxf'=>array('name' =>'大小分', 'acts' => array()),
				'sfc'=>array('name' =>'胜分差', 'acts' => array()),
				'bqc'=>array('name' =>'混合选', 'acts' => array()),
			)),
			'awards' => array('name' => '历史开奖', 'acts' => array(/*
				'jczq' => array('name' => '竞彩足球', 'acts' => array()),
				'jclq' => array('name' => '竞彩篮球', 'acts' => array()),
				'number' => array('name' => 'continue', 'acts' => array()),*/
			)),
			'mylottery' => array('name' => '用户中心', 'acts' => array(
                'index' => array('name' => '我的账户', 'acts' => array()),
				'account' => array('name' => '我的账户', 'acts' => array()),
				'betlog' => array('name' => '投注记录', 'acts' => array()),
				'detail' => array('name' => '账户明细', 'acts' => array()),
				'recharge' => array('name' => '充值记录', 'acts' => array()),
				'withdrawals' => array('name' => '提现记录', 'acts' => array()),
			)),
			'safe' => array('name' => '用户中心', 'acts' => array(
				'index' => array('name' => '安全中心', 'acts' => array()),
				'baseinfo' => array('name' => '基本资料', 'acts' => array()),
				'paypwd' => array('name' => '修改密码', 'acts' => array()),
				'update_password' => array('name' => '修改密码', 'acts' => array()),
			)),
			'wallet' => array('name' => '用户中心', 'acts' => array(
				'recharge' => array('name' => '充值', 'acts' => array()),
				'withdraw' => array('name' => '提现', 'acts' => array()),
				'directPay' => array('name' => '收银台', 'acts' => array()),
			)),
            //网站公告  @Author liusijia
            'notice' => array('name' => '网站公告', 'acts' => array(
                'detail'=>array('name' =>'详情', 'acts' => array()
            ))),
            'orders' => array('name' => '订单详情', 'acts' => array()),
		);
		$crumb_str = $crumbs[$con]['name'];
		if(!empty($crumbs[$con]['acts'][$act]))
		{
        	/*if($con == 'notice')
        	{//@Author liusijia
            	$crumb_str = "<a href='/$con/index' target='_self'>{$crumb_str}</a>";
            }else
            {
				$crumb_str = "<a href='/$con/' target='_self'>{$crumb_str}</a>";
            }*/
			if($crumbs[$con]['acts'][$act]['name'] == 'continue')
			{
				$CI = &get_instance();
				$CI->load->library('BetCnName');
				$crumbs[$con]['acts'][$act]['name'] = BetCnName::$BetCnName[$pm];
			}
            $crumb_str = "<a href='/$con/' target='_self'>{$crumb_str}</a>";
			$crumb_str .= "<i>&gt;</i><span>{$crumbs[$con]['acts'][$act]['name']}</span>";
		}
		return $crumb_str;
	}
}

if ( ! function_exists('bank_name'))
{
	function bank_name($ctype, $bank=0)
	{
		$name = '';
		$CI = &get_instance();
		$CI->load->library('BankCard');
		$name = BankCard::$bankName[$ctype]['name'];
		if(!empty(BankCard::$bankName[$ctype]['banks'][$bank]))
		{
			$name .= "(" . BankCard::$bankName[$ctype]['banks'][$bank] . ")";
		}
		return $name;
	}
}

if ( ! function_exists('cutstr'))
{
	function cutstr($str,$position,$length)
	{
		$len = utf8_strlen($str);
		if($len > $length)
		{
			return utf8_substr($str,$position,$length).str_repeat(' *', $len - $length);
		}else{
			return $str;
		}
	}
}

if ( ! function_exists('utf8_strlen'))
{
	function utf8_strlen($str){
		$count = 0;
		for($i=0;$i<strlen($str);$i++){
			$value = ord($str[$i]);
			if($value>127){
				//$count++;
				if($value>=192&&$value<=223)$i++;
				elseif($value>=224&&$value<=239)$i+=2;
				elseif($value>=240&&$value<=247)$i+=3;
				else die('Not a UTF-8 compatible string');
			}
			$count++;
		}
		return $count;
	}
}
if ( ! function_exists('utf8_substr'))
{
	function utf8_substr($str,$position,$length){
		$start_position=strlen($str);
		$end_position=strlen($str);
		$count=0;
		$countlen = utf8_strlen($str);
		$start_byte=$countlen;
		if(($position+$length)>$countlen) $length=$countlen%$length;
		for($i=0;$i<strlen($str);$i++){
			if($count>=$position&&$start_position>$i){
				$start_position=$i;
				$start_byte=$count;
			}
			if(($count-$start_byte)>=$length){
				$end_position=$i;
				break;
			}
			$value = ord($str[$i]);
			if($value>127){
				//$count++;
				if($value>=192&&$value<=223)$i++;
				elseif($value>=224&&$value<=239)$i+=2;
				elseif($value>=240&&$value<=247)$i+=3;
				else die('Not a UTF-8 compatible string');
			}
			$count++;
		}
		return (substr($str,$start_position,$end_position-$start_position));
	}
}
if ( ! function_exists('wallet_status'))
{
	function wallet_status($stat)
	{
		$status = array(
		 	0 => '提现申请',
			1 => '提现审核中',
			2 => '提现成功',
			3 => '提现撤销',
			4 => '提现失败',
			5 => '提现审核中'
		);
		return $status[$stat];
	}
}
if ( ! function_exists('wallet_ctype'))
{
	function wallet_ctype($type, $addition)
	{
		$ctype = array(
            0 => '充值',
            1 => '购买',
            2 => '奖金派送',
            3 => '订单失败返款',
            4 => '提现',
            5 => '提款成功解除冻结预付款',
            6 => '扣除预付款',
            7 => '解除冻结预付款',
            8 => '提款失败还款',
            9 => '彩金派送',
            10 => '其他应收款项',
            11 => '其他',
			12 => '冻结',  
			13 => '返还', 
			14 => '返利',
                        15 => '合买保底退款',
                        16 => '冻结',
                        17 => '返还',
    	);
    	$banks = array();
    	$title = $ctype[$type];
    	switch ($type)
    	{
    		case 0:
    			$addition = explode('@', $addition);
    			$title = bank_name($addition[0], $addition[1]) . $title;
    			break;
    		case 1:
    			$CI = &get_instance();
				$CI->load->library('BetCnName');
				$title .= BetCnName::$BetCnName[$addition];
    			break;
    		case 12:
    		case 13:
    			$CI = &get_instance();
    			$CI->load->library('BetCnName');
    			$title .= BetCnName::$BetCnName[$addition]."追号预付款";
    			break;
    		case 16:
                case 17:
    			$CI = &get_instance();
    			$CI->load->library('BetCnName');
    			$title .= BetCnName::getCnName($addition)."跟单预付款";
    			break;                      
    		default:
    			break;
    	}
    	return $title;
	}
}

if ( ! function_exists('parse_order_status'))
{
    //解析订单状态对于文字
	function parse_order_status( $preCode, $postCode )
    {
        $orderStatus = array(
            0 => '创建订单',
            10 => '待付款',
            20 => '投注失败',
            21 => '投注失败',
            30 => '付款中',
            40 => '已付款',
            200 => '等待出票',
            240 => '出票中',
            500 => '等待开奖',
            510 => '部分出票成功',
            600 => '出票失败',
            610 => '发起人撤单',
            620 => '未满员撤单',
            1000 => '未中奖',
            2000 => '已中奖',
        );

        $sendPrizeStatus = array(
            0 => '系统算奖中',
            1 => '已派奖',
            2 => '大奖待审核',
            3 => '已派奖',
            4 => '派奖失败',
            5 => '奖金已自提',
        );
        $cnStatus = '未知';
        if( $preCode == 2000 && !empty($postCode) )
        {
            $cnStatus = $sendPrizeStatus[$postCode];
        }
        else
        {
            $cnStatus = $orderStatus[$preCode];
        }
        return $cnStatus;
	}
}

if ( ! function_exists('parse_hemai_status'))
{
	//解析订单状态对于文字
	function parse_hemai_status( $preCode, $postCode = null )
	{
		$orderStatus = array(
				0 => '等待出票',
				20 => '过期未付款',
				40 => '等待出票',
				240 => '出票中',
				500 => '等待开奖',
				600 => '方案撤单',
				610 => '发起人撤单',
				620 => '未满员撤单',
				1000 => '未中奖',
				2000 => '已中奖',
		);

		$sendPrizeStatus = array(
				0 => '系统算奖中',
				1 => '已派奖',
				2 => '大奖待审核',
				3 => '已派奖',
				4 => '派奖失败',
				5 => '奖金已自提',
		);
		$cnStatus = '未知';
		if( $preCode == 2000 && !empty($postCode))
		{
			$cnStatus = $sendPrizeStatus[$postCode];
		}
		else
		{
			$cnStatus = $orderStatus[$preCode];
		}
		return $cnStatus;
	}
}


//获取银行卡信息
if ( ! function_exists('BanksDetail'))
{
    //解析订单状态对于文字
	function BanksDetail($bank_type,$type)
    {
    	$bankTypeList = array(
	        '1025' => array(
	        	'name' => '工商银行',
	        	'dname' => '中国工商银行',
	        	'img' => 'bank-icon1.png',
	        	'st' => 'gs'
	        ),
	        '3080' => array(
	        	'name' => '招商银行',
	        	'dname' => '招商银行',
	        	'img' => 'bank-icon10.png',
	        	'st' => 'zs'
	        ),
	        '105' => array(
	        	'name' => '建设银行',
	        	'dname' => '中国建设银行',
	        	'img' => 'bank-icon5.png',
	        	'st' => 'js'
	        ),
	        '103' => array(
	        	'name' => '农业银行',
	        	'dname' => '中国农业银行',
	        	'img' => 'bank-icon2.png',
	        	'st' => 'ny'
	        ),
	        '104' => array(
	        	'name' => '中国银行',
	        	'dname' => '中国银行',
	        	'img' => 'bank-icon7.png',
	        	'st' => 'zg'
	        ),
	        '301' => array(
	        	'name' => '交通银行',
	        	'dname' => '交通银行',
	        	'img' => 'bank-icon11.png',
	        	'st' => 'jt'
	        ),
	        '307' => array(
	        	'name' => '平安银行',
	        	'dname' => '平安银行',
	        	'img' => 'bank-icon17.png',
	        	'st' => 'pa'
	        ),
	        '309' => array(
	        	'name' => '兴业银行',
	        	'dname' => '兴业银行',
	        	'img' => 'bank-icon13.png',
	        	'st' => 'xy'
	        ),
	        '311' => array(
	        	'name' => '华夏银行',
	        	'dname' => '华夏银行',
	        	'img' => 'bank-icon9.png',
	        	'st' => 'hx'
	        ),
	        '305' => array(
	        	'name' => '民生银行',
	        	'dname' => '中国民生银行',
	        	'img' => 'bank-icon15.png',
	        	'st' => 'ms'
	        ),
	        '306' => array(
	        	'name' => '广发银行',
	        	'dname' => '广发银行',
	        	'img' => 'bank-icon4.png',
	        	'st' => 'gf'
	        ),
	        '314' => array(
	        	'name' => '浦发银行',
	        	'dname' => '上海浦东发展银行',
	        	'img' => 'bank-icon12.png',
	        	'st' => 'pf'
	        ),
	        '313' => array(
	        	'name' => '中信银行',
	        	'dname' => '中信银行',
	        	'img' => 'bank-icon16.png',
	        	'st' => 'zx'
	        ),
	        '312' => array(
	        	'name' => '光大银行',
	        	'dname' => '中国光大银行',
	        	'img' => 'bank-icon6.png',
	        	'st' => 'gd'
	        ),
	        '316' => array(
	        	'name' => '南京银行',
	        	'dname' => '南京银行',
	        	'img' => 'bank-icon18.png',
	        	'st' => 'nj'
	        ),
	        '326' => array(
	        	'name' => '上海银行',
	        	'dname' => '上海银行',
	        	'img' => 'bank-icon8.png',
	        	'st' => 'sh'
	        ),
	        '3230' => array(
	        	'name' => '中国邮政储蓄',
	        	'dname' => '中国邮政储蓄银行',
	        	'img' => 'bank-icon14.png',
	        	'st' => 'yz'
	        ),
	    );
	return $bankTypeList[$bank_type][$type];
    }
}

//获取日期字符串
if ( ! function_exists('getWeekByTime'))
{
	function getWeekByTime($time)
	{
		$weekarray = array("日","一","二","三","四","五","六");
		return "周".$weekarray[date("w",$time)];
	}
}

/**
 * 参    数：$str 字符串
 * 		 $digit保留小数点位数
 * 功    能：金额格式话
 */
if (!function_exists('m_format'))
{

	function m_format($str, $digit = 2)
	{
		$str = $str / 100;
		return number_format($str, $digit, ".", ",");
	}

}

// 获取红包到期截止时间
if(! function_exists('ParseEnd'))
{
	function ParseEnd($endDate)
	{
		$nowTime = strtotime(date('Y-m-d', strtotime('now')));
		$endTime = strtotime(date('Y-m-d', strtotime($endDate)));
		if($endTime <= $nowTime)
        {
            $difD = '今天过期';
        }
        else
        {
            $difD = intval(($endTime - $nowTime)/86400) . '天后过期';
        }
		return $difD;
	}
}

// 获取充值红包使用说明
if(! function_exists('ParseDesc'))
{
	function ParseDesc($useDesc)
	{
		preg_match('/.*?(\d+).*?(\d+).*?/is', $useDesc, $matches);

		$poolArry = explode('.', $matches[1]);
		$pool = $poolArry[0];

		$unit = array('', '万', '亿');
        $tpl = "";
        if(is_numeric($pool) && !empty($pool))
        {
            $temp = str_split(strrev(floatval($pool)), 4);
            // 升序
            krsort($temp);
            if(isset($temp[2]))
            {
                $temp[0] = '0000';
            }
            foreach ($temp as $key => $items) 
            {
                if(!isset($unit[$key]))
                {
                    $tpl .= intval(strrev($items));
                }
                else
                {
                    $num = intval(strrev($items));
                    if(!empty($num) || $key == 2)
                    {
                        $str = $num . $unit[$key];
                        $tpl .= $str;
                    }             
                }
            }
        }
        else
        {
            $tpl .= 0;
        }
        
		return '充' . $tpl . '送<b>' . $matches[2] . '</b>';
	}
}

if (! function_exists ( 'print_str' ))
{
	function print_str($string)
	{
		return empty($string) ? '---' : $string;
	}
}

if (! function_exists ( 'chase_status' ))
{
	function chase_status($status, $chaseStatus)
	{
		switch ($status) 
        {
            case $chaseStatus['create']:
                $msg = "待付款";
                break;     
            case $chaseStatus['is_chase']:
                $msg = "追号中";
                break;
            case $chaseStatus['stop_by_award']:
                $msg = "中奖停止追号";
                break;
            case $chaseStatus['chase_over']:
                $msg = "追号完成";
                break;   
            default:
                $msg = "";
                break;
        }
        return $msg;
	}
}

// html转内容
if (! function_exists ( 'htmltochars' ))
{
	function htmltochars($content)
	{
		$content = htmlspecialchars_decode($content);
        $content = str_replace('&nbsp;', '', $content);
        $content = strip_tags($content);
        $content = str_replace(array(" ","　","\t","\n","\r"), array("","","","",""), $content);    
        return $content;
	}
}

// IOS Safari交互
if (! function_exists ( 'BackToLottery' ))
{
	function BackToLottery($type, $parms = array())
	{
		$jsonData = json_encode(array('type' => $type, 'parms' => $parms));
		$detail = array(
		    '返回参数为：'.urlencode($jsonData),
		    '最后跳转为：lottery166cai://' . urlencode($jsonData)
        );
        return 'lottery166cai://' . urlencode($jsonData);
	}
}

if (! function_exists ( 'BackToLotteryByChannel' ))
{
	function BackToLotteryByChannel($name = '', $type = 0, $parms = array())
	{
		$jsonData = json_encode(array('type' => $type, 'parms' => $parms));
        return 'lottery166cai' . $name . '://' . urlencode($jsonData);
	}
}

if (!function_exists('getallheaders')) 
{
    function getallheaders() 
    {
        foreach ($_SERVER as $name => $value) 
        {
            if (substr($name, 0, 5) == 'HTTP_') 
            {
                $headers[str_replace(' ', '-', ucwords(strtolower(str_replace('_', ' ', substr($name, 5)))))] = $value;
            }
        }

        // Nginx转换
        $headers['appVersionName'] = (isset($headers['Appversionname']) && !empty($headers['Appversionname']))?$headers['Appversionname']:'';
        $headers['appVersionCode'] = (isset($headers['Appversioncode']) && !empty($headers['Appversioncode']))?$headers['Appversioncode']:'';
        $headers['channel'] = (isset($headers['Channel']) && !empty($headers['Channel']))?$headers['Channel']:'';
        $headers['idfa'] = (isset($headers['Idfa']) && !empty($headers['Idfa']))?$headers['Idfa']:'';
        // 手机型号
        $headers['model'] = (isset($headers['Model']) && !empty($headers['Model']))?$headers['Model']:'';
        // 手机系统
        $headers['OSVersion'] = (isset($headers['Osversion']) && !empty($headers['Osversion']))?$headers['Osversion']:'';
        $headers['platform'] = (isset($headers['platform']) && !empty($headers['platform']))?$headers['platform']:'';
        $headers['old_platform'] = isset($headers['Idfa']) ? 'ios' : 'app'; //旧版本判断平台字段  新版本已新增platform
        return $headers;
    }
}

// https路径解析
if (!function_exists('ParseHttp')) 
{
    function ParseHttp($url = '') 
    {
    	$path = '';
        if(!empty($url))
        {
        	$CI = &get_instance();
        	$path = $CI->config->item('protocol') . str_replace(array('http:', 'https:'), array('', ''), $url);
        }
        return $path;
    }
}


if (!function_exists('uname_cut'))
{
	function uname_cut($uname, $mode = 1, $len = 5, $pad = '...') {
		switch ($mode) {
			case 1:
				return mb_strlen($uname, 'utf-8') > $len ? mb_substr($uname, 0, $len, 'utf-8').$pad : $uname;
				break;
			case 2:
				return (mb_strlen($uname, 'utf8') > $len) ? mb_substr($uname, 0, $len, 'utf8')."***" : mb_substr($uname, 0, -1, 'utf8')."*";
				break;
		}

	}
}

/**
 * 计算等级
 * @param int $points
 * @return string
 */
if (!function_exists('calGrade'))
{
	function calGrade($points, $jiequ = 0)
	{
		$picArr = array('hg', 'ty', 'yl', 'xx');
		$grade = "";
		$huangguan = floor($points / 1000);
		$l = 0;
		if ($huangguan > 0)
		{
			for ($n = $huangguan; $n > 10; $n = $n - 10)
			{
				if ($jiequ && $jiequ <= $l) return $grade;
				$l++;
				$grade.="<span class='level-".$picArr[0]." level-10'></span>";
			}

			if ($jiequ && $jiequ <= $l) return $grade;
			$l++;
			$grade.="<span class='level-".$picArr[0]." level-".$n."'></span>";
		}
		$taiyang = floor(($points - $huangguan * 1000) / 100);
		if ($taiyang > 0)
		{
			if ($jiequ && $jiequ <= $l) return $grade;
			$l++;
			$grade.="<span class='level-".$picArr[1]." level-".$taiyang."'></span>";
		}
		$yueliang = floor(($points - $huangguan * 1000 - $taiyang * 100) / 10);
		if ($yueliang > 0)
		{
			if ($jiequ && $jiequ <= $l) return $grade;
			$l++;
			$grade.="<span class='level-".$picArr[2]." level-".$yueliang."'></span>";
		}
		$xingxing = $points - $huangguan * 1000 - $taiyang * 100 - $yueliang * 10;
		if ($xingxing > 0)
		{
			if ($jiequ && $jiequ <= $l) return $grade;
			$l++;
			$grade.="<span class='level-".$picArr[3]." level-".$xingxing."'></span>";
		}
		return $grade;
	}
}
if ( ! function_exists('strCode'))
{
	function strCode ( $str , $action = 'DECODE' )
	{
		$action == 'DECODE' && $str = base64_decode ($str);
		$code = '';
		$CI = &get_instance();
		$hash = $CI->config->item('encrypt_hash');
		$key = md5 ( $hash );
		$keylen = strlen ( $key );
		$strlen = strlen ( $str );
		for($i = 0; $i < $strlen; $i ++)
		{
			$k = $i % $keylen; //余数  将字符全部位移
			$code .= $str[$i] ^ $key[$k]; //位移
		}
		return ($action == 'DECODE' ? $code : base64_encode ( $code ));
	}
}

if (!function_exists('dd'))
{
    function dd($arr) 
    {
        echo '<pre>';
        is_array($arr) ? print_r($arr) : var_dump($arr);
        echo '</pre>';
        exit(); 
    }
}


if (!function_exists('apiRequest'))
{
    function apiRequest($libPath,$method,$params,$addPath='') 
    {
		$CI = &get_instance();
		//设置访问外部目录
        $CI->load->remove_package_path();
        $CI->load->library($libPath);
        $library = str_replace('api/','',$libPath);
        $request = new $library();
        $response = $request->$method($params);
        //重新设置地址
        $CI->load->add_package_path( empty($addPath) ? APPPATH : $addPath);
		if(empty($addPath))
        {
            $CI->load->config();
        }
        return $response;
    }
}

// 资讯链接转义
if (!function_exists('ParseUrl'))
{
    function ParseUrl($content = '') 
    {
    	$info = file_get_contents('http://www.166cai.net/domain.php');
        $info = json_decode($info, true);

    	return preg_replace('/https\:\/\/888\.166cai\.cn\/info\/\w+\/(\d+)/', $info['data']['ios'] . "/ios/info/detail/$1", $content);
    }
}

// 福彩3D 排列三 排列五 统一转义
if (!function_exists('ParseLname'))
{
    function ParseLname($lname = '') 
    {
    	return str_replace(array('fc3d', 'pl3', 'pl5'), array('fcsd', 'pls', 'plw'), $lname);
    }
}
