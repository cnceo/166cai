<?php if (!defined('BASEPATH'))
{
    exit('No direct script access allowed');
}

// SDK支付配置
if (ENVIRONMENT === 'production')
{
    // 连连支付SDK
    $config['llpaySdk'] = array(
        'oid_partner'           => '201605111000852692',    // 商户编号
        'sign_type'             => 'MD5',   // 加密类型
        'key'                   => 'swssfsgffdk670934',
        'busi_partner'          => '101001',    // 商户业务类型
        'notify_url'            => 'https://www.166cai.com/api/pay/llpayWebAsync',   // 异步回调地址
        'frms_ware_category'    => '1007',  // 商品类目
    );
}
else
{
    // 连连支付SDK
    $config['llpaySdk'] = array(
        'oid_partner'           => '201605111000852692',    // 商户编号
        'sign_type'             => 'MD5',   // 加密类型
        'key'                   => 'swssfsgffdk670934',
        'busi_partner'          => '101001',    // 商户业务类型
        'notify_url'            => 'https://123.59.105.39/api/pay/llpayWebAsync',   // 异步回调地址
        'frms_ware_category'    => '1007',  // 商品类目
    );
}


//交易类型
$jylx_cfg = array(
    "0"  => "充值",
    "1"  => "付款",
    "2"  => "奖金",
    "3"  => "退款",
    "4"  => "申请提现",
    "5"  => "申请提现成功,解冻金额",
    "6"  => "申请提现成功",
    "7"  => "用户撤销提现",
    "8"  => "提现打款失败",
    "9"  => "彩金派送",
    "10" => "其他应收款项",
    "11" => '其他',
    "12" => '冻结',  
    "13" => '返还', 
    "14" => '返利',
    "15" => '合买返还预付款',
    "16" => '冻结合买跟单预付款',
    "17" => '合买跟单退款',
);
//支付方式
$pay_cfg = array(
    "yeepayMPay"     => array(
        "name"  => "易宝支付",
        "child" => array(
            "default" => array(
                "易宝支付",
                1
            )
        )
    ),
    "yeepayCredit"     => array(
        "name"  => "易宝支付",
        "child" => array(
            "default" => array(
                "易宝支付",
                1
            )
        )
    ),
    "yeepayKuaij"     => array(
        "name"  => "易宝支付",
        "child" => array(
            "default" => array(
                "易宝支付",
                1
            )
        )
    ),
    "yeepayWangy"     => array(
        "name"  => "易宝支付",
        "child" => array(
            "default" => array(
                "易宝支付",
                1
            )
        )
    ), 
    "yeepayWeix"     => array(
        "name"  => "易宝支付",
        "child" => array(
            "default" => array(
                "易宝支付",
                1
            )
        )
    ), 
    "llpaySdk"      => array(
        "name"  => "连连支付",
        "child" => array(
            "default" => array(
                "连连支付",
                1
            )
        )
    ),
    "llpayKuaij"     => array(
        "name"  => "连连支付",
        "child" => array(
            "default" => array(
                "连连支付",
                1
            )
        )
    ),
    "sumpayWap"     => array(
        "name"  => "统统付",
        "child" => array(
            "default" => array(
                "统统付",
                1
            )
        )
    ),
    "sumpayWeb"     => array(
        "name"  => "统统付",
        "child" => array(
            "default" => array(
                "统统付",
                1
            )
        )
    ),
    "payWeix"       => array(
        "name"  => "微信支付",
        "child" => array(
            "default" => array(
                "微信支付",
                1
            )
        )
    ),
    "zxwxSdk"       => array(
        "name"  => "微信支付",
        "child" => array(
            "default" => array(
                "微信支付",
                1
            )
        )
    ),
	"wftWxSdk"       => array(
		"name"  => "微信支付",
		"child" => array(
			"default" => array(
				"微信支付",
				1
			)
		)
	),
    "wftWx"       => array(
        "name"  => "微信支付",
        "child" => array(
            "default" => array(
                "微信支付",
                1
            )
        )
    ),
    "xzZfbWap"  => array(
        "name"  => "支付宝",
        "child" => array(
            "default" => array(
                "支付宝",
                1
            )
        )
    ),
    "hjZfbPay"  => array(
        "name"  => "支付宝",
        "child" => array(
            "default" => array(
                "支付宝",
                1
            )
        )
    ),
    "wftZfbWap"  => array(
        "name"  => "支付宝",
        "child" => array(
            "default" => array(
                "支付宝",
                1
            )
        )
    ),
    "wftWxWap"  => array(
        "name"  => "微信支付",
        "child" => array(
            "default" => array(
                "微信支付",
                1
            )
        )
    ),
    "jdPay"  => array(
        "name"  => "京东支付",
        "child" => array(
            "default" => array(
                "京东支付",
                1
            )
        )
    ),
    "umPay"  => array(
        "name"  => "银行卡快捷支付",
        "child" => array(
            "default" => array(
                "银行卡快捷支付",
                1
            )
        )
    ),
    "hjWxWap"  => array(
        "name"  => "微信支付",
        "child" => array(
            "default" => array(
                "微信支付",
                1
            )
        )
    ),
    "hjZfbWap"  => array(
        "name"  => "支付宝",
        "child" => array(
            "default" => array(
                "支付宝",
                1
            )
        )
    ),
    "pfWxWap"  => array(
        "name"  => "微信支付",
        "child" => array(
            "default" => array(
                "微信支付",
                1
            )
        )
    ),
    "yzpayh"  => array(
        "name"  => "支付宝",
        "child" => array(
            "default" => array(
                "支付宝",
                1
            )
        )
    ),
    "tomatoZfbWap"  => array(
        "name"  => "支付宝",
        "child" => array(
            "default" => array(
                "支付宝",
                1
            )
        )
    ),
    "ulineWxWap"  => array(
        "name"  => "微信支付",
        "child" => array(
            "default" => array(
                "微信支付",
                1
            )
        )
    ),
    "hjZfbSh"  => array(
        "name"  => "支付宝",
        "child" => array(
            "default" => array(
                "支付宝",
                1
            )
        )
    ),
    "yzWxWap"  => array(
        "name"  => "微信",
        "child" => array(
            "default" => array(
                "微信",
                1
            )
        )
    ),
    "jdSdk"  => array(
        "name"  => "京东支付",
        "child" => array(
            "default" => array(
                "京东支付",
                1
            )
        )
    ),
    "tomatoWxWap"  => array(
        "name"  => "微信",
        "child" => array(
            "default" => array(
                "微信",
                1
            )
        )
    ),
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
//提现状态
$withdrawal_status_cfg = array(
    "0" => "提现申请",
    "1" => "提现撤销锁定",
    "2" => "提现成功",
    "3" => "提现撤销",
    "4" => "打款失败",
    "5" => "已操作打款"

);
$pay_cfg_array = array(
    'pay_cfg'  => $pay_cfg,
    'jylx_cfg' => $jylx_cfg,
    'r_s_cfg'  => $recharge_status_cfg,
    'w_s_cfg'  => $withdrawal_status_cfg,
    "p_p_cfg"  => $pay_platform_cfg
);

$config['pay_all_cfg'] = $pay_cfg_array;
