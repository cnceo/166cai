<?php if (!defined('BASEPATH'))
{
    exit('No direct script access allowed');
}

// 支付宝SDK
$config['alipaysdk'] = array(
    'partner'     => '2088611881384172',
    // 商户PID
    'seller'      => 'caipiao@2345.com',
    // 商户收款账号
    'notify_url'  => 'https://pay.2345.com/alipaysdk/notify_url.php',
    // 异步通知地址
    'rsa_private' => 'MIICdwIBADANBgkqhkiG9w0BAQEFAASCAmEwggJdAgEAAoGBAM0pUT7ferSoO7247ZzG+wWaSZEJGY3GFdu1XHw9y6G8I+O1K8oTbyCQMjR4zcj5IaG3HMzHE1zRFRygQtOgfutFlPG/WoTb9Wae+DziRyKXU9frylGuzK9w4W9lCQy8gOc4BdfDHPYkNZZMnIeLf1Rgjyh+rO9t4T5CpKoVBf81AgMBAAECgYEAy+cnn3RfMRQPJBWa2vmMXNomCabgpd5ctRuASt7j9t/VT6gtWE6OYO/PZgU2iWrJ+T7TudaVsOcAs424PTrDR+uGEE1oNVuxcMo+SLHQpduhhwy92ix2Kc6wew3jP2iId3UHLyxsEpi1sFQ536PHmgr1GptoqqU+7uo3PqUyC3UCQQDyPHnzTVhLwoIB9aA298o/fMKj4C+9GaP5S/8dnRGCkb6ZiZjmu7Fvi3F70R7l9Y4rGCXUdlixzVO0ktIsBb7DAkEA2NGOpV8TFmRLg4KYNDd0P0U2Gw/5Tbnb3y1rmbmZgxfhanUbell3d2TiSauL3w+jEd0ZArPtAk5X6jkIb5JapwJBAOITmUl+3TJPxaBoiu/iUYrxOINTn5pgTM5FpEMSLZ5rwbRwXBi0AgT14qNJaAn9JIOJ1Py2u06uMDoucSMO9ZsCQAme4tF7soEf2invtYk7nLDiBVCWGN3WDWeNwGSF08X5XUs3/wwixCZMF65lKkOvkfDM+rsf+LhNeaEu/qzUtakCQDEOw/EEfE42EWlnP5FxQ6cyj5Gwigqcz48WpFYRTyFEkWzI2f3s8X3qKrmGRcKoEGRRDvfMW5+gqHoCBugNxVE=',
    // 商户私钥
    'orderid'     => '',
    // 订单流水
    'price'       => '',
    // 商品金额
    'subject'     => '',
    // 商品名称
    'body'        => ''
    // 商品详情
);

// 盛付通SDK
$config['shengpaysdk'] = array(
    'partner'     => '431450',
    // 商户PID
    'seller'      => '',
    // 商户收款账号
    'notify_url'  => 'https://pay.2345.com/shengpay/notify_url.php',
    // 异步通知地址
    'rsa_private' => 'cp2345fldh95dkft',
    // 商户私钥
    'orderid'     => '',
    // 订单流水
    'price'       => '',
    // 商品金额
    'subject'     => '',
    // 商品名称
    'body'        => ''
    // 商品详情
);


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
    "17" => '合买跟单退款'
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
    "hjWxWap"  => array(
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
