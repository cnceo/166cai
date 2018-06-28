<?php

/**
 * Copyright (c) 2014,上海二三四五网络科技股份有限公司
 * 摘    要: 银行卡验证
 * 作    者: lizq
 * 来    源: www.yinhangkahao.com/bank_luhm.html
 * 修改日期: 2014-11-25 
 */

class BankCard
{
	public static $bankName = array(
		'alipay'=> array('name' => '支付宝', 'banks' => array()),
		'tenpay'=> array('name' => '财付通', 'banks' => array()),
		'shengpay'=> array('name' => '盛付通', 'banks' => array()),
		'alipaysdk'=> array('name' => '支付宝', 'banks' => array()),
		'shengpaysdk'=> array('name' => '盛付通', 'banks' => array()),
		'shengpaywap'=> array('name' => '盛付通', 'banks' => array()),
		'1'=> array('name' => '移动卡', 'banks' => array()),
		'2'=> array('name' => '联通卡', 'banks' => array()),
		'3'=> array('name' => '电信卡', 'banks' => array()),
		'chinabank' => array('name' => '网银',
			'banks' => array(
				'1025' => '中国工商银行',
				'3230' => '中国邮政储蓄银行',
				'103' => '中国农业银行',
				'3080' => '招商银行',
				'105' => '中国建设银行',
				'104' => '中国银行',
				'301' => '交通银行',
				'305' => '中国民生银行',
				'309' => '兴业银行',
				'312' => '光大银行',
				'311' => '华夏银行',
				'313' => '中信银行',
				
				'307' => '平安银行',
				'306' => '广东发展银行',
				'314' => '上海浦东发展银行',
				'316' => '南京银行',
				'324' => '杭州银行',
				'302' => '宁波银行',
				'310' => '北京银行',
				'326' => '上海银行',
				'329' => '浙江泰隆银行',
				'332' => '金华银行',
				'339' => '富滇银行',
				'344' => '恒丰银行',
				'317' => '渤海银行',
				'335' => '北京农商行',
				'336' => '成都银行',
				'340' => '汉口银行',
				
				'308' => '招商银行',
				'1054' => '中国建设银行',
				'106' => '中国银行',
				'3112' => '华夏银行',
				'3051' => '中国民生银行',
				'3121' => '光大银行',
				'3231' => '中国邮政储蓄银行',
				'3241' => '杭州银行',
				'303' => '宁波银行',
				'3261' => '上海银行',
				'334' => '青岛银行',
			)
		)
	);
	
    /**
     * 校验银行卡卡号
     */
     static public function checkBankCard( $cardId ) 
    {
        $cardId = preg_replace('/\s/', '', $cardId); // 去掉空格
        if ($cardId == "") {
            // 请填写银行卡号
            return false;
        }
        if ( strlen( $cardId ) < 16 || 
             strlen( $cardId ) > 19 ) 
        {
            // 银行卡号长度必须在16到19之间
            return false;
        }

        if ( !preg_match('/^\d*$/', $cardId) ) 
        {
            // 银行卡号必须全为数字 ;
            return false;
        }
        return true; // 不进行校验算法!!!!!!!

        // $bit = self::getBankCardCheckCode( substr($cardId, 0, strlen( $cardId ) - 1) );
        // return substr( $cardId, -1, 1) == $bit;        
    }
    
    /**
     * 从不含校验位的银行卡卡号采用 Luhm 校验算法获得校验位
     */
    static public function getBankCardCheckCode( $nonCheckCodeCardId) 
    {
        $luhmSum = 0;
        for($i = strlen($nonCheckCodeCardId) - 1, $j = 0; $i >= 0; $i--, $j++) 
        {
            $k = $nonCheckCodeCardId{$i} ;
            if($j % 2 == 0) {
                $k *= 2;
                $k = intval($k / 10) + $k % 10;
            }
            $luhmSum += $k;
        }
        return ($luhmSum % 10 == 0) ? 0 : (10 - $luhmSum % 10);
    }
}