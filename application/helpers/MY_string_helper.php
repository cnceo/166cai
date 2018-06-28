<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

if(!function_exists('getStaticFile'))
{
	function getStaticFile($filePath)
	{
		$CI = &get_instance();
		$base_path = $CI->config->item('base_path');
		
		//为了兼容vanish的GBK编码做如下处理
		$ver = 234;
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
			'kaijiang' => array('name' => '全国开奖', 'acts' => array(
				'ssq' => array('name' => '双色球开奖', 'acts' => array()),
			    'fc3d' => array('name' => '福彩3D开奖', 'acts' => array()),
			    'dlt' => array('name' => '大乐透开奖', 'acts' => array()),
			    'qlc' => array('name' => '七乐彩开奖', 'acts' => array()),
			    'qxc' => array('name' => '七星彩开奖', 'acts' => array()),
			    'pl3' => array('name' => '排列3开奖', 'acts' => array()),
			    'pl5' => array('name' => '排列5开奖', 'acts' => array()),
			    'syxw' => array('name' => '老11选5开奖', 'acts' => array()),
			    'rj' => array('name' => '任选九开奖', 'acts' => array()),
			    'sfc' => array('name' => '胜负彩开奖', 'acts' => array())
			)),
			'chart' => array('name' => '走势图表', 'acts' => array(
				'ssq' => array('name' => '双色球', 'acts' => array()),
				'dlt' => array('name' => '大乐透', 'acts' => array()),
				'fcsd' => array('name' => '福彩3D', 'acts' => array()),
				'qlc' => array('name' => '七乐彩', 'acts' => array()),
				'qxc' => array('name' => '七星彩', 'acts' => array()),
				'pls' => array('name' => '排列三', 'acts' => array()),
				'plw' => array('name' => '排列五', 'acts' => array()),
				'syxw' => array('name' => '十一选五', 'acts' => array()),
			)),
		    'academy' => array('name' => '彩票学院', 'acts' => array()),
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
				'withdrawals' => array('name' => '提款记录', 'acts' => array()),
			)),
			'safe' => array('name' => '用户中心', 'acts' => array(
				'index' => array('name' => '安全中心', 'acts' => array()),
				'baseinfo' => array('name' => '基本资料', 'acts' => array()),
				'paypwd' => array('name' => '修改密码', 'acts' => array()),
				'update_password' => array('name' => '修改密码', 'acts' => array()),
			)),
			'wallet' => array('name' => '用户中心', 'acts' => array(
				'recharge' => array('name' => '充值', 'acts' => array()),
				'withdraw' => array('name' => '提款', 'acts' => array()),
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
				$crumbs[$con]['acts'][$act]['name'] = BetCnName::getCnName($pm);
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

// 获取充值红包使用说明
if(! function_exists('ParseDesc'))
{
	function ParseDesc($useDesc)
	{
		preg_match('/.*?(\d+).*?(\d+).*?/is', $useDesc, $matches);
		return '充' . $matches[1] . '送<b>' . $matches[2] . '</b>';
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
		 	0 => '申请提款',
			1 => '提款审核中', //已废弃
			2 => '银行已到账',
			3 => '提款撤销',  //已废弃
			4 => '提款失败',
			5 => '财务已转账' 
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
            4 => '提款',
            //5 => '提款成功解除冻结预付款', //已废弃
            //6 => '扣除预付款',		//已废弃
            //7 => '解除冻结预付款',	//已废弃
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
				$title .= BetCnName::getCnName($addition);
    			break;
    		case 12:
    		case 13:
    			$CI = &get_instance();
    			$CI->load->library('BetCnName');
    			$title .= BetCnName::getCnName($addition)."追号预付款";
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
	function parse_order_status( $preCode, $postCode = null )
    {
        $orderStatus = array(
            0 => '创建订单',
            10 => '待付款',
            20 => '投注失败',
            21 => '投注失败',
            30 => '付款中',
            40 => '已付款',
        	50 => '用户撤单',
            200 => '等待出票',
            240 => '出票中',
            500 => '等待开奖',
            510 => '等待开奖',
            600 => '出票失败',
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

if ( ! function_exists('parse_chase_status'))
{
	function parse_chase_status($status)
	{
		
		switch ($status) {
			case 0:
				return '待付款';
				break;
			case 20:
			case 21:
				return '订单过期';
				break;
			case 240:
				return '追号中';
				break;
			case 500:
				return '中奖后停止追号';
				break;
			case 700:
				return '追号完成';
				break;
		}
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

if ( ! function_exists('parse_chase_order_status'))
{
	function parse_chase_order_status($cStatus, $status, $mystatus = null)
	{
		$orderStatus = array(
				0   => '等待出票',
				20  => '订单过期',
				21  => '订单过期',
				40  => '等待出票',
				240 => '出票中',
				500 => '等待开奖',
				510 => '等待开奖（部分成功）',
				600 => '出票失败',
				601 => '已撤单（手动停止追号）',
				602 => '系统撤单',
				603 => '已撤单（中奖后停止追号）',
				1000 => '未中奖',
				2000 => '已中奖',
		);
		
		switch ($cStatus) {
			case 0:
				if ($status == 0) {
					return '待付款';
				}else {
					return $orderStatus[$status];
				}
				break;
			default:
				return $orderStatus[$status];
				break;
		}
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
	        ),
	        '3080' => array(
	        	'name' => '招商银行',
	        	'dname' => '招商银行',
	        	'img' => 'bank-icon10.png',
	        ),
	        '105' => array(
	        	'name' => '建设银行',
	        	'dname' => '中国建设银行',
	        	'img' => 'bank-icon5.png',
	        ),
	        '103' => array(
	        	'name' => '农业银行',
	        	'dname' => '中国农业银行',
	        	'img' => 'bank-icon2.png',
	        ),
	        '104' => array(
	        	'name' => '中国银行',
	        	'dname' => '中国银行',
	        	'img' => 'bank-icon7.png',
	        ),
	        '301' => array(
	        	'name' => '交通银行',
	        	'dname' => '交通银行',
	        	'img' => 'bank-icon11.png',
	        ),
	        '307' => array(
	        	'name' => '平安银行',
	        	'dname' => '平安银行',
	        	'img' => 'bank-icon17.png',
	        ),
	        '309' => array(
	        	'name' => '兴业银行',
	        	'dname' => '兴业银行',
	        	'img' => 'bank-icon13.png',
	        ),
	        '311' => array(
	        	'name' => '华夏银行',
	        	'dname' => '华夏银行',
	        	'img' => 'bank-icon9.png',
	        ),
	        '305' => array(
	        	'name' => '民生银行',
	        	'dname' => '中国民生银行',
	        	'img' => 'bank-icon15.png',
	        ),
	        '306' => array(
	        	'name' => '广发银行',
	        	'dname' => '广发银行',
	        	'img' => 'bank-icon4.png',
	        ),
	        '314' => array(
	        	'name' => '浦发银行',
	        	'dname' => '上海浦东发展银行',
	        	'img' => 'bank-icon12.png',
	        ),
	        '313' => array(
	        	'name' => '中信银行',
	        	'dname' => '中信银行',
	        	'img' => 'bank-icon16.png',
	        ),
	        '312' => array(
	        	'name' => '光大银行',
	        	'dname' => '中国光大银行',
	        	'img' => 'bank-icon6.png',
	        ),
	        '316' => array(
	        	'name' => '南京银行',
	        	'dname' => '南京银行',
	        	'img' => 'bank-icon18.png',
	        ),
	        '326' => array(
	        	'name' => '上海银行',
	        	'dname' => '上海银行',
	        	'img' => 'bank-icon8.png',
	        ),
	        '3230' => array(
	        	'name' => '中国邮政',
	        	'dname' => '中国邮政银行',
	        	'img' => 'bank-icon14.png',
	        ),
	    );
	return $bankTypeList[$bank_type][$type];
    }
}

if ( ! function_exists('checkRebateOdds'))
{
	/**
	 * 检查联盟返点是否设置
	 * @param unknown_type $rebateOdds
	 */
	function checkRebateOdds($rebateOdds = array())
	{
		if($rebateOdds)
		{
			foreach ($rebateOdds as $lid => $val)
			{
				if($val > 0)
				{
					return true;
				}
			}
		}
		return false;
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
	function calGrade($points, $jiequ = 0, $num = 2)
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
				$grade.="<i class='lv10'><img src='/caipiaoimg/v1.1/img/".$picArr[0].$num.".gif'><sub>10</sub></i>";
			}
			
			if ($jiequ && $jiequ <= $l) return $grade;
			$l++;
			$grade.="<i class='lv{$n}'><img src='/caipiaoimg/v1.1/img/".$picArr[0].$num.".gif'><sub>{$n}</sub></i>";
		}
		$taiyang = floor(($points - $huangguan * 1000) / 100);
		if ($taiyang > 0)
		{
			if ($jiequ && $jiequ <= $l) return $grade;
			$l++;
			$grade.="<i class='lv{$taiyang}'><img src='/caipiaoimg/v1.1/img/".$picArr[1].$num.".gif'><sub>{$taiyang}</sub></i>";
		}
		$yueliang = floor(($points - $huangguan * 1000 - $taiyang * 100) / 10);
		if ($yueliang > 0)
		{
			if ($jiequ && $jiequ <= $l) return $grade;
			$l++;
			$grade.="<i class='lv{$yueliang}'><img src='/caipiaoimg/v1.1/img/".$picArr[2].$num.".gif'><sub>{$yueliang}</sub></i>";
		}
		$xingxing = $points - $huangguan * 1000 - $taiyang * 100 - $yueliang * 10;
		if ($xingxing > 0)
		{
			if ($jiequ && $jiequ <= $l) return $grade;
			$l++;
			$grade.="<i class='lv{$xingxing}'><img src='/caipiaoimg/v1.1/img/".$picArr[3].$num.".gif'><sub>{$xingxing}</sub></i>";
		}
		return $grade;
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

if (!function_exists('arraySortByKey'))
{
    /**
     * [arraySortByKey 二维数组按照键名排序]
     * @author LiKangJian 2017-06-22
     * @param  [type] $array [description]
     * @param  [type] $field [description]
     * @param  string $sort  [description]
     * @return [type]        [description]
     */
    function arraySortByKey($array, $field, $sort = 'SORT_DESC')
    {
        $arrSort = array();
        foreach ($array as $uniqid => $row) {
            foreach ($row as $key => $value) {
                $arrSort[$key][$uniqid] = $value;
            }
        }
        array_multisort($arrSort[$field], constant($sort), $array);
        return $array;
    }
}


if (!function_exists('getRechargeInfo'))
{
    /**
     * [getRechargeTitle 获取充值的头部信息]
     * @author LiKangJian 2017-06-22
     * @param  [type] $mode [description]
     * @return [type]       [description]
     */
    function getRechargeInfo($ctype,$is_recharge=1,$params=array())
    {
        $data_recharge = array(
                        '3'=>array(
                        			'meta_title' =>'微信支付-我要充值-166彩票官网',
                        			'meta_desc'=>'166彩票官网是专业彩民首选的安全购彩平台，为彩民提供安全便捷的微信支付充值方式。',
                        			'meta_keywords' => '166彩票官网，彩票，彩票合买，微信支付',
                        			'meta_url' => '888.166cai.cn/wallet/recharge/weixin'
                        	      ), 

                        '4'=>array(
                        			'meta_title' =>'支付宝支付-我要充值-166彩票官网',
                        			'meta_desc'=>'166彩票官网是专业彩民首选的安全购彩平台，为彩民提供安全便捷的支付宝支付充值方式。',
                        			'meta_keywords' => '166彩票官网，彩票，彩票合买，支付宝支付',
                        			'meta_url' => '888.166cai.cn/wallet/recharge/alipay'
                        	      ), 

                        '1'=>array(
                        			'meta_title' =>'快捷支付-我要充值-166彩票官网',
                        			'meta_desc'=>'166彩票官网是专业彩民首选的安全购彩平台，为彩民提供安全便捷的快捷支付充值方式。',
                        			'meta_keywords' => '1166彩票官网，彩票，彩票合买，快捷支付',
                        			'meta_url' => '888.166cai.cn/wallet/recharge/kuaijie'
                        	      ), 

                        '5'=>array(
                        			'meta_title' =>'网银支付-我要充值-166彩票官网',
                        			'meta_desc'=>'166彩票官网是专业彩民首选的安全购彩平台，为彩民提供安全便捷的网银支付充值方式。',
                        			'meta_keywords' => '166彩票官网，彩票，彩票合买，网银支付',
                        			'meta_url' => '888.166cai.cn/wallet/recharge/bank'
                        	      ), 
                        '6'=>array(
                        			'meta_title' =>'信用卡支付-我要充值-166彩票官网',
                        			'meta_desc'=>'166彩票官网是专业彩民首选的安全购彩平台，为彩民提供安全便捷的信用卡支付充值方式。',
                        			'meta_keywords' => '166彩票官网，彩票，彩票合买，信用卡支付',
                        			'meta_url' => '888.166cai.cn/wallet/recharge/credit'
                        	      ), 
                        '7'=>array(
                        			'meta_title' =>'银联云闪付-我要充值-166彩票官网',
                        			'meta_desc'=>'166彩票官网是专业彩民首选的安全购彩平台，为彩民提供安全便捷的网银支付充值方式。',
                        			'meta_keywords' => '166彩票官网，彩票，彩票合买，网银支付',
                        			'meta_url' => '888.166cai.cn/wallet/recharge/yinlian'
                        	      ),
                        '8'=>array(
                        			'meta_title' =>'京东支付-我要充值-166彩票官网',
                        			'meta_desc'=>'166彩票官网是专业彩民首选的安全购彩平台，为彩民提供安全便捷的网银支付充值方式。',
                        			'meta_keywords' => '166彩票官网，彩票，彩票合买，京东支付',
                        			'meta_url' => '888.166cai.cn/wallet/recharge/jd'
                        	      ), 

                        'default'=>array(
                        			'meta_title' =>'我要充值-166彩票官网',
                        			'meta_desc'=>'166彩票官网是专业彩民首选的安全购彩平台，提供微信支付、支付宝支付、快捷支付、网银支付、银行卡快捷支付充值方式。',
                        			'meta_keywords' => '166彩票官网，彩票，彩票合买，微信支付、支付宝支付、快捷支付、网银支付、信用卡支付',
                        			'meta_url' => 'www.166cai.cn/wallet/recharge'
                        	      ), 
                     );

        $data_cashier = array(
                        '3'=>array(
                        			'meta_title' =>'微信支付-收银台-166彩票官网',
                        			'meta_desc'=>'166彩票官网是专业彩民首选的安全购彩平台，为彩民提供安全便捷的微信支付。',
                        			'meta_keywords' => '166彩票官网，彩票，彩票合买，微信支付',
                        			'meta_url' => '/'
                        	      ), 

                        '4'=>array(
                        			'meta_title' =>'支付宝支付-收银台-166彩票官网',
                        			'meta_desc'=>'166彩票官网是专业彩民首选的安全购彩平台，为彩民提供安全便捷的支付宝支付。',
                        			'meta_keywords' => '166彩票官网，彩票，彩票合买，支付宝支付',
                        			'meta_url' => '/'
                        	      ), 

                        '1'=>array(
                        			'meta_title' =>'快捷支付-收银台-166彩票官网',
                        			'meta_desc'=>'166彩票官网是专业彩民首选的安全购彩平台，为彩民提供安全便捷的快捷支付。',
                        			'meta_keywords' => '166彩票官网，彩票，彩票合买，快捷支付',
                        			'meta_url' => '/'
                        	      ), 

                        '5'=>array(
                        			'meta_title' =>'网银支付-收银台-166彩票官网',
                        			'meta_desc'=>'166彩票官网是专业彩民首选的安全购彩平台，为彩民提供安全便捷的网银支付。',
                        			'meta_keywords' => '166彩票官网，彩票，彩票合买，网银支付',
                        			'meta_url' => '/'
                        	      ), 
                        '6'=>array(
                        			'meta_title' =>'信用卡支付-收银台-166彩票官网',
                        			'meta_desc'=>'166彩票官网是专业彩民首选的安全购彩平台，为彩民提供安全便捷的信用卡支付。',
                        			'meta_keywords' => '166彩票官网，彩票，彩票合买，信用卡支付',
                        			'meta_url' => '/'
                        	      ), 
                        '7'=>array(
                        			'meta_title' =>'银联云闪付-我要充值-166彩票官网',
                        			'meta_desc'=>'166彩票官网是专业彩民首选的安全购彩平台，为彩民提供安全便捷的网银支付充值方式。',
                        			'meta_keywords' => '166彩票官网，彩票，彩票合买，网银支付',
                        			'meta_url' => '/'
                        	      ), 
                         '8'=>array(
                        			'meta_title' =>'京东支付-我要充值-166彩票官网',
                        			'meta_desc'=>'166彩票官网是专业彩民首选的安全购彩平台，为彩民提供安全便捷的网银支付充值方式。',
                        			'meta_keywords' => '166彩票官网，彩票，彩票合买，京东支付',
                        			'meta_url' => '/'
                        	      ), 

                        'default'=>array(
                        			'meta_title' =>'我要购彩-166彩票官网',
                        			'meta_desc'=>'166彩票官网是专业彩民首选的安全购彩平台，提供微信支付、支付宝支付、快捷支付、网银支付、银行卡快捷支付',
                        			'meta_keywords' => '166彩票官网，彩票，彩票合买，微信支付、支付宝支付、快捷支付、网银支付、信用卡支付',
                        			'meta_url' => 'http://www.166cai.cn/wallet/directPay?orderId='.$params['orderId']
                        	      ), 
                     );
        return $is_recharge==1 ? $data_recharge[$ctype] : $data_cashier[$ctype];
    }
}
if ( ! function_exists('parse_gendan_order_status'))
{
	function parse_gendan_order_status($status,$my_status)
	{
            if($my_status == 0){
                return "跟单中";
            }
            if($my_status == 1 && $status > 1){
                return "跟单完成";
            }
	}
}
