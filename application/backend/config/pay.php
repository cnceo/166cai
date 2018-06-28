<?php

/**
 * Copyright (c) 2014,上海二三四五网络科技股份有限公司
 * 摘    要：支付配置
 * 作    者：wangl@2345.com
 * 修改日期：2014.11.06
 */
//交易类型
$jylx_cfg = array(
    "0" => "充值",
    "1" => "付款",
    "2" => "奖金",
    "3" => "退款",
    "4" => "申请提款",
    //"5" => "申请提款成功,解冻金额", //已废弃
    "6" => "提款成功",	
    //"7" => "用户撤销提款",	//已废弃
    "8" => "提款失败",
    "9" => "彩金派送",
    "10" => "其他应收款项",
    "11" => "其他",
	"12" => "冻结追号预付款",
	"13" => "返还追号预付款",
	"14" => "推广返利",
    "15" => "合买返还预付款",
    "16" => '冻结合买跟单预付款',
    "17" => '合买跟单退款',
);
//支付方式
$pay_cfg = array(
    "alipay" => array(
        "name" => "支付宝",
        "child" => array(
            "directPay" => array(
                "支付宝",
                1
            ),
            "default" => array(
                "支付宝",
                1
            )
        )
    ),
    "chinabank" => array(
        "name" => "网银",
        "child" => array(
            '1025' => array(
                '中国工商银行（借记卡）',
                2
            ),
            '3080' => array(
                '招商银行（借记卡）',
                2
            ),
            '105' => array(
                '中国建设银行（借记卡）',
                2
            ),
            '103' => array(
                '中国农业银行（借记卡）',
                2
            ),
            '104' => array(
                '中国银行（借记卡）',
                2
            ),
            '301' => array(
                '交通银行',
                2
            ),
            '307' => array(
                '平安银行',
                2
            ),
            '309' => array(
                '兴业银行',
                2
            ),
            '311' => array(
                '华夏银行（借记卡）',
                2
            ),
            '305' => array(
                '中国民生银行（借记卡）',
                2
            ),
            '306' => array(
                '广发银行（借记卡）',
                2
            ),
            '314' => array(
                '上海浦东发展银行（借记卡）',
                2
            ),
            '313' => array(
                '中信银行（借记卡）',
                2
            ),
            '312' => array(
                '光大银行（借记卡）',
                2
            ),
            '316' => array(
                '南京银行（借记卡）',
                2
            ),
            '3230' => array(
                '邮政储蓄银行（借记卡）',
                2
            ),
            '324' => array(
                '杭州银行（借记卡）',
                2
            ),
            '302' => array(
                '宁波银行（借记卡）',
                2
            ),
            '310' => array(
                '北京银行（借记卡）',
                2
            ),
            '326' => array(
                '上海银行（借记卡）',
                2
            ),
            '329' => array(
                '浙江泰隆银行（借记卡）',
                2
            ),
            '332' => array(
                '金华银行（借记卡）',
                2
            ),
            '342' => array(
                '重庆农商银行（借记卡）',
                2
            ),
            '345' => array(
                '重庆银行（借记卡）',
                2
            ),
            '339' => array(
                '富滇银行（借记卡）',
                2
            ),
            '344' => array(
                '恒丰银行（借记卡）',
                2
            ),
            '317' => array(
                '渤海银行（借记卡）',
                2
            ),
            '335' => array(
                '北京农商行（借记卡）',
                2
            ),
            '336' => array(
                '成都银行（借记卡）',
                2
            ),
            '340' => array(
                '汉口银行（借记卡）',
                2
            ),
            '1027' => array(
                '中国工商银行（信用卡）',
                3
            ),
            '308' => array(
                '招商银行（信用卡）',
                3
            ),
            '1054' => array(
                '中国建设银行（信用卡）',
                3
            ),
            '106' => array(
                '中国银行（信用卡）',
                3
            ),
            '3112' => array(
                '华夏银行（信用卡）',
                3
            ),
            '3051' => array(
                '中国民生银行（信用卡）',
                3
            ),
            '3121' => array(
                '光大银行（信用卡）',
                3
            ),
            '3231' => array(
                '邮政储蓄银行（信用卡）',
                3
            ),
            '3241' => array(
                '杭州银行（信用卡）',
                3
            ),
            '303' => array(
                '宁波银行（信用卡）',
                3
            ),
            '3261' => array(
                '上海银行（信用卡）',
                3
            ),
            '334' => array(
                '青岛银行（信用卡）',
                3
            ),
            '327' => array(
                '其它银行',
                2
            )
        )
    ),
    "shengpaybank" => array(
        "name" => "盛付通银行",
        "child" => array(
            "19_ICBC" => array(
                '中国工商银行（借记卡）',
                2
            ),
            "19_PSBC" => array(
                '中国邮政储蓄银行（借记卡）',
                2
            ),
            "19_ABC" => array(
                '中国农业银行（借记卡）',
                2
            ),
            "19_CMB" => array(
                '招商银行（借记卡）',
                2
            ),
            "19_CCB" => array(
                '中国建设银行（借记卡）',
                2
            ),
            "19_BOC" => array(
                '中国银行（借记卡）',
                2
            ),
            "19_COMM" => array(
                '交通银行（借记卡）',
                2
            ),
            "19_CMBC" => array(
                '中国民生银行（借记卡）',
                2
            ),
            "19_CIB" => array(
                '兴业银行（借记卡）',
                2
            ),
            "19_CEB" => array(
                '光大银行（借记卡）',
                2
            ),
            "19_HXB" => array(
                '光大银行（借记卡）',
                2
            ),
            "19_CITIC" => array(
                '中信银行（借记卡）',
                2
            ),
            "19_GDB" => array(
                '广东发展银行（借记卡）',
                2
            ),
            "19_SPDB" => array(
                '上海浦东发展银行（借记卡）',
                2
            ),
            "19_NJCB" => array(
                '南京银行（借记卡）',
                2
            ),
            "19_NBCB" => array(
                '宁波银行（借记卡）',
                2
            ),
            "19_BOS" => array(
                '上海银行（借记卡）',
                2
            ),
            "19_BOCD" => array(
                '成都银行（借记卡）',
                2
            ),
            "19_SZPAB" => array(
                '平安银行（借记卡）',
                2
            ),
            "19_SHRCB" => array(
                '上海农商银行（借记卡）',
                2
            ),
            "19_BCCB" => array(
                '北京银行（借记卡）',
                2
            ),
            "20_ICBC" => array(
                '中国工商银行（信用卡）',
                3
            ),
            "20_CMB" => array(
                '招商银行（信用卡）',
                3
            ),
            "20_CCB" => array(
                '中国建设银行（信用卡）',
                3
            ),
            "20_BOC" => array(
                '中国银行（信用卡）',
                3
            ),
            "20_CMBC" => array(
                '中国民生银行（信用卡）',
                3
            ),
            "20_CEB" => array(
                '光大银行（信用卡）',
                3
            ),
            "20_BOS" => array(
                '上海银行（信用卡）',
                3
            ),
            "20_SZPAB" => array(
                '平安银行（信用卡）',
                3
            ),
            "20_COMM" => array(
                '交通银行（信用卡）',
                3
            ),
            "20_CIB" => array(
                '兴业银行（信用卡）',
                3
            ),
            "20_GDB" => array(
                '广东发展银行（信用卡）',
                3
            ),
            "20_SPDB" => array(
                '上海浦东发展银行（信用卡）',
                3
            )
        )
    ),
    "alipaywap" => array(
        "name" => "支付宝移动版",
        "child" => array(
            "directPay" => array(
                "支付宝移动版",
                1
            ),
            "default" => array(
                "支付宝移动版",
                1
            )
        )
    ),
    "tenpay" => array(
        "name" => "财付通",
        "child" => array(
            "tenpay" => array(
                "财付通",
                1
            ),
            "directPay" => array(
                "财付通",
                1
            ),
            "default" => array(
                "财付通",
                1
            )
        )
    ),
    "shengpay" => array(
        "name" => "盛付通",
        "child" => array(
            "default" => array(
                "盛付通",
                1
            )
        )
    ),
    "alipaysdk" => array(
        "name" => "支付宝SDK",
        "child" => array(
            "default" => array(
                "支付宝SDK",
                1
            ),
            "directPay" => array(
                "支付宝SDK",
                1
            ),
        )
    ),
    "shengpaysdk" => array(
        "name" => "盛付通SDK",
        "child" => array(
            "default" => array(
                "盛付通SDK",
                1
            ),
            "directPay" => array(
                "盛付通SDK",
                1
            ),
        )
    ),
    "shengpaywap" => array(
        "name" => "盛付通WAP",
        "child" => array(
            "default" => array(
                "盛付通WAP",
                1
            ),
            "directPay" => array(
                "盛付通WAP",
                1
            ),
        )
    ),
    "1" => array(
        "name" => "移动卡",
        "child" => array(
            "default" => array(
                "移动卡",
                1
            )
        )
    ),
    "2" => array(
        "name" => "联通卡",
        "child" => array(
            "default" => array(
                "联通卡",
                1
            )
        )
    ),
    "3" => array(
        "name" => "电信卡",
        "child" => array(
            "default" => array(
                "电信卡",
                1
            )
        )
    )
);

$pay_platform_cfg = array(
    "1" => "平台支付",
    "2" => "网上银行",
    "3" => "信用卡"
);

//充值状态(看mark)
$recharge_status_cfg = array(
    "2" => "未支付",
    "1" => "充值成功"
);
//提款状态
$withdrawal_status_cfg = array(
    "1" => "提款申请",
    //"1" => "提款撤销锁定", //已废弃
    "2" => "提款成功",
   // "3" => "提款撤销", //已废弃
    "4" => "打款失败",
	"5" => "已提交银行"
    
);
$pay_cfg_array = array(
    'pay_cfg' => $pay_cfg,
    'jylx_cfg' => $jylx_cfg,
    'r_s_cfg' => $recharge_status_cfg,
    'w_s_cfg' => $withdrawal_status_cfg,
    "p_p_cfg" => $pay_platform_cfg
);

$config['pay_all_cfg'] = $pay_cfg_array;
