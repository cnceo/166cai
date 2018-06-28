<?php

/**
 * Copyright (c) 2014,上海二三四五网络科技股份有限公司
 * 摘    要：彩票配置
 * 作    者：wangl@2345.com
 * 修改日期：2014.11.06
 */
if ( ! defined('BASEPATH')) {
    exit('No direct script access allowed');
}
//彩票种类及玩法
$caipiao_cfg = array(
    "23529" => array(
        "py"   => "DLT",
        "name" => "大乐透"
    ),
    "23528" => array(
        "py"   => "QLC",
        "name" => "七乐彩"
    ),
    "10022" => array(
        "py"   => "QXC",
        "name" => "七星彩"
    ),
    "21406" => array(
        "py"   => "SYXW",
        "name" => "老11选5",
        "play" => array(
            "0"  => array(
                "py"   => "hh",
                "name" => "混合"
            ),
            "1"  => array(
                "py"   => "q1",
                "name" => "前一直选"
            ),
            "2"  => array(
                "py"   => "rx2",
                "name" => "任选二"
            ),
            "3"  => array(
                "py"   => "rx3",
                "name" => "任选三"
            ),
            "4"  => array(
                "py"   => "rx4",
                "name" => "任选四"
            ),
            "5"  => array(
                "py"   => "rx5",
                "name" => "任选五"
            ),
            "6"  => array(
                "py"   => "rx6",
                "name" => "任选六"
            ),
            "7"  => array(
                "py"   => "rx7",
                "name" => "任选七"
            ),
            "8"  => array(
                "py"   => "rx8",
                "name" => "任选八"
            ),
            "9"  => array(
                "py"   => "qzhi2",
                "name" => "前二直选"
            ),
            "10" => array(
                "py"   => "qzhi3",
                "name" => "前三直选"
            ),
            "11" => array(
                "py"   => "qzu2",
                "name" => "前二组选"
            ),
            "12" => array(
                "py"   => "qzu3",
                "name" => "前三组选"
            )
        )
    ),
	"21407" => array(
		"py"   => "JXSYXW",
		"name" => "新11选5",
		"play" => array(
			"0"  => array(
				"py"   => "hh",
				"name" => "混合"
			),
			"1"  => array(
				"py"   => "q1",
				"name" => "前一直选"
			),
			"2"  => array(
				"py"   => "rx2",
				"name" => "任选二"
			),
			"3"  => array(
				"py"   => "rx3",
				"name" => "任选三"
			),
			"4"  => array(
				"py"   => "rx4",
				"name" => "任选四"
			),
			"5"  => array(
				"py"   => "rx5",
				"name" => "任选五"
			),
			"6"  => array(
				"py"   => "rx6",
				"name" => "任选六"
			),
			"7"  => array(
				"py"   => "rx7",
				"name" => "任选七"
			),
			"8"  => array(
				"py"   => "rx8",
				"name" => "任选八"
			),
			"9"  => array(
				"py"   => "qzhi2",
				"name" => "前二直选"
			),
			"10" => array(
				"py"   => "qzhi3",
				"name" => "前三直选"
			),
			"11" => array(
				"py"   => "qzu2",
				"name" => "前二组选"
			),
			"12" => array(
				"py"   => "qzu3",
				"name" => "前三组选"
			)
		)
	),
	"21408" => array(
		"py"   => "HBSYXW",
		"name" => "惊喜11选5",
		"play" => array(
			"0"  => array(
				"py"   => "hh",
				"name" => "混合"
			),
			"1"  => array(
				"py"   => "q1",
				"name" => "前一直选"
			),
			"2"  => array(
				"py"   => "rx2",
				"name" => "任选二"
			),
			"3"  => array(
				"py"   => "rx3",
				"name" => "任选三"
			),
			"4"  => array(
				"py"   => "rx4",
				"name" => "任选四"
			),
			"5"  => array(
				"py"   => "rx5",
				"name" => "任选五"
			),
			"6"  => array(
				"py"   => "rx6",
				"name" => "任选六"
			),
			"7"  => array(
				"py"   => "rx7",
				"name" => "任选七"
			),
			"8"  => array(
				"py"   => "rx8",
				"name" => "任选八"
			),
			"9"  => array(
				"py"   => "qzhi2",
				"name" => "前二直选"
			),
			"10" => array(
				"py"   => "qzhi3",
				"name" => "前三直选"
			),
			"11" => array(
				"py"   => "qzu2",
				"name" => "前二组选"
			),
			"12" => array(
				"py"   => "qzu3",
				"name" => "前三组选"
			)
		)
	),
    "21421" => array(
        "py"   => "GDSYXW",
        "name" => "乐11选5",
        "play" => array(
            "0"  => array(
                "py"   => "hh",
                "name" => "混合"
            ),
            "1"  => array(
                "py"   => "q1",
                "name" => "前一直选"
            ),
            "2"  => array(
                "py"   => "rx2",
                "name" => "任选二"
            ),
            "3"  => array(
                "py"   => "rx3",
                "name" => "任选三"
            ),
            "4"  => array(
                "py"   => "rx4",
                "name" => "任选四"
            ),
            "5"  => array(
                "py"   => "rx5",
                "name" => "任选五"
            ),
            "6"  => array(
                "py"   => "rx6",
                "name" => "任选六"
            ),
            "7"  => array(
                "py"   => "rx7",
                "name" => "任选七"
            ),
            "8"  => array(
                "py"   => "rx8",
                "name" => "任选八"
            ),
            "9"  => array(
                "py"   => "qzhi2",
                "name" => "前二直选"
            ),
            "10" => array(
                "py"   => "qzhi3",
                "name" => "前三直选"
            ),
            "11" => array(
                "py"   => "qzu2",
                "name" => "前二组选"
            ),
            "12" => array(
                "py"   => "qzu3",
                "name" => "前三组选"
            )
        )
    ),
    "51"    => array(
        "py"   => "SSQ",
        "name" => "双色球"
    ),
    "33"    => array(
        "py"   => "PL3",
        "name" => "排列三",
        "play" => array(
            "0"  => array(
                "py"   => "hh",
                "name" => "混合"
            ),
            "1" => array(
                "py"   => "zx",
                "name" => "直选"
            ),
            "2" => array(
                "py"   => "z3",
                "name" => "组3"
            ),
            "3" => array(
                "py"   => "z6",
                "name" => "组6"
            )
        )
    ),
    "35"    => array(
        "py"   => "PL5",
        "name" => "排列五"
    ),
    "41"    => array(
        "py"   => "DJDC",
        "name" => "北京单场"
    ),
    "42"    => array(
        "py"   => "JCZQ",
        "name" => "竞彩足球",
        "play" => array(
            "0" => array(
                "py"   => "hh",
                "name" => "混合过关"
            ),
            "1" => array(
                "py"   => "spf",
                "name" => "胜平负"
            ),
            "2" => array(
                "py"   => "rqspf",
                "name" => "让球胜平负"
            ),
            "5" => array(
                "py"   => "cbf",
                "name" => "比分"
            ),
            "4" => array(
                "py"   => "jqs",
                "name" => "总进球"
            ),
            "3" => array(
                "py"   => "bqc",
                "name" => "半全场"
            ),
            "6" => array(
                "py"   => "dg",
                "name" => "单关",
            ),
        )
    ),
    "43"    => array(
        "py"   => "JCLQ",
        "name" => "竞彩篮球",
        "play" => array(
            "0" => array(
                "py"   => "hh",
                "name" => "混合过关"
            ),
            "2" => array(
                "py"   => "rfsf",
                "name" => "让分胜负"
            ),
            "1" => array(
                "py"   => "sf",
                "name" => "胜负"
            ),
            "4" => array(
                "py"   => "dxf",
                "name" => "大小分"
            ),
            "3" => array(
                "py"   => "sfc",
                "name" => "胜分差"
            )
        )
    ),
    "52"    => array(
        "py"   => "FCSD",
        "name" => "福彩3D",
        "play" => array(
            "0"  => array(
                "py"   => "hh",
                "name" => "混合"
            ),
            "1" => array(
                "py"   => "zx",
                "name" => "直选"
            ),
            "2" => array(
                "py"   => "z3",
                "name" => "组3"
            ),
            "3" => array(
                "py"   => "z6",
                "name" => "组6"
            )
        )
    ),
    "11"    => array(
        "py"   => "sfc",
        "name" => "胜负彩"
    ),
    "19"    => array(
        "py"   => "rj",
        "name" => "任选九"
    ),
	"44" => array(
		"py"   => "gj",
		"name" => "冠军彩"
	),
	"45" => array(
		"py"   => "gyj",
		"name" => "冠亚军彩"
	),
	"53" => array(
		"py"   => "ks",
		"name" => "上海快三",
		"play" => array(
			"0"  => array(
				"py"   => "hh",
				"name" => "混合"
			),
			"1"  => array(
				"py"   => "hz",
				"name" => "和值"
			),
			"2"  => array(
				"py"   => "sthtx",
				"name" => "三同号通选"
			),
			"3"  => array(
				"py"   => "sthdx",
				"name" => "三同号单选"
			),
			"4"  => array(
				"py"   => "sbth",
				"name" => "三不同号"
			),
			"5"  => array(
				"py"   => "slhtx",
				"name" => "三连号通选"
			),
			"6"  => array(
				"py"   => "ethfx",
				"name" => "二同号复选"
			),
			"7"  => array(
				"py"   => "ethdx",
				"name" => "二同号单选"
			),
			"8"  => array(
				"py"   => "ebth",
				"name" => "二不同号"
			)
		
		)
	),
	"56" => array(
		"py"   => "jlks",
		"name" => "吉林快三",
		"play" => array(
			"0"  => array(
				"py"   => "hh",
				"name" => "混合"
			),
			"1"  => array(
				"py"   => "hz",
				"name" => "和值"
			),
			"2"  => array(
				"py"   => "sthtx",
				"name" => "三同号通选"
			),
			"3"  => array(
				"py"   => "sthdx",
				"name" => "三同号单选"
			),
			"4"  => array(
				"py"   => "sbth",
				"name" => "三不同号"
			),
			"5"  => array(
				"py"   => "slhtx",
				"name" => "三连号通选"
			),
			"6"  => array(
				"py"   => "ethfx",
				"name" => "二同号复选"
			),
			"7"  => array(
				"py"   => "ethdx",
				"name" => "二同号单选"
			),
			"8"  => array(
				"py"   => "ebth",
				"name" => "二不同号"
			)
		
		)
	),    
    "57" => array(
        "py"   => "jxks",
        "name" => "江西快三",
        "play" => array(
            "0"  => array(
                "py"   => "hh",
                "name" => "混合"
            ),
            "1"  => array(
                "py"   => "hz",
                "name" => "和值"
            ),
            "2"  => array(
                "py"   => "sthtx",
                "name" => "三同号通选"
            ),
            "3"  => array(
                "py"   => "sthdx",
                "name" => "三同号单选"
            ),
            "4"  => array(
                "py"   => "sbth",
                "name" => "三不同号"
            ),
            "5"  => array(
                "py"   => "slhtx",
                "name" => "三连号通选"
            ),
            "6"  => array(
                "py"   => "ethfx",
                "name" => "二同号复选"
            ),
            "7"  => array(
                "py"   => "ethdx",
                "name" => "二同号单选"
            ),
            "8"  => array(
                "py"   => "ebth",
                "name" => "二不同号"
            )
    
        )
    ),
    "54" => array(
        "py"   => "klpk",
        "name" => "快乐扑克",
        "play" => array(
            "0"  => array(
                "py"   => "hh",
                "name" => "混合"
            ),
            "1"  => array(
                "py"   => "r1",
                "name" => "任选一"
            ),
            "2"  => array(
                "py"   => "r2ds",
                "name" => "任选二单式"
            ),
            "21" => array(
                "py"   => "r2fs",
                "name" => "任选二复式"
            ),
            "22" => array(
                "py"   => "r2dt",
                "name" => "任选二胆拖"
            ),
            "3"  => array(
                "py"   => "r3ds",
                "name" => "任选三单式"
            ),
            "31" => array(
                "py"   => "r3fs",
                "name" => "任选三复式"
            ),
            "32" => array(
                "py"   => "r3dt",
                "name" => "任选三胆拖"
            ),
            "4"  => array(
                "py"   => "r4ds",
                "name" => "任选四单式"
            ),
            "41" => array(
                "py"   => "r4fs",
                "name" => "任选四复式"
            ),
            "42" => array(
                "py"   => "r4dt",
                "name" => "任选四胆拖"
            ),
            "5"  => array(
                "py"   => "r5ds",
                "name" => "任选五单式"
            ),
            "51" => array(
                "py"   => "r5fs",
                "name" => "任选五复式"
            ),
            "52" => array(
                "py"   => "r5dt",
                "name" => "任选五胆拖"
            ),
            "6"  => array(
                "py"   => "r6ds",
                "name" => "任选六单式"
            ),
            "61" => array(
                "py"   => "r6fs",
                "name" => "任选六复式"
            ),
            "62" => array(
                "py"   => "r6dt",
                "name" => "任选六胆拖"
            ),
            "7"  => array(
                "py"   => "th",
                "name" => "同花"
            ),
            "8"  => array(
                "py"   => "ths",
                "name" => "同花顺"
            ),
            "9"  => array(
                "py"   => "sz",
                "name" => "顺子"
            ),
            "10"  => array(
                "py"   => "bz",
                "name" => "豹子"
            ),
            "11"  => array(
                "py"   => "dz",
                "name" => "对子"
            )
        )
    ),
    "55" => array(
        "py"   => "cqssc",
        "name" => "老时时彩",
        "play" => array(
            "10"  => array(
                "py"   => "1xzhix",
                "name" => "一星直选"
            ),
            "20,21"  => array(
                "py"   => "2xzhix",
                "name" => "二星直选"
            ),
            "23,27" => array(
                "py"   => "2xzux",
                "name" => "二星组选"
            ),
            "30,31"  => array(
                "py"   => "3xzhix",
                "name" => "三星直选"
            ),
            "33" => array(
                "py"   => "3xzu3",
                "name" => "三星组三单"
            ),
            "37"  => array(
                "py"   => "3xzu3fs",
                "name" => "三星组三复"
            ),
            "34,38"  => array(
                "py"   => "3xzu6",
                "name" => "三星组六"
            ),
            "40,41" => array(
                "py"   => "5xzhix",
                "name" => "五星直选"
            ),
            "43"  => array(
                "py"   => "5xtx",
                "name" => "五星通选"
            ),
            "1"  => array(
                "py"   => "dxds",
                "name" => "大小单双"
            ),    
        )
    ),
);
//订单类型
$caipiao_type_cfg = array(
    "0" => "自购",
	"1" => "追号",
	'3' => "不中包赔",
	'6' => "追号不中包赔"
);
//订单状态
$caipiao_status_cfg = array(
    "0"    => array(
        "合买进度＜95%",
        "待付款"
    ),
    "10"    => array(
        "待付款",
        "待付款"
    ),
    "20"   => array(
        "逾期未支付",
        "逾期未支付"
    ),
    "21"   => array(
        "付款请求失败",
        "投注失败"
    ),
    "30"   => array(
        "付款中",
        "付款中"
    ),
    "40"   => array(
        "已付款",
        "已付款"
    ),
    "240"  => array(
        "出票中",
        "出票中"
    ),
    "500"  => array(
        "等待开奖",
        "出票成功"
    ),
	"510"  => array(
		"部分出票成功",
		"部分出票成功"
	),
    "600"  => array(
        "出票失败",
        "出票失败"
    ),
	"601"  => array(
		"手动撤单",
		"手动撤单"
	),
	"602"  => array(
		"系统撤单",
		"系统撤单"
	),
	"603"  => array(
		"中奖后撤单",
		"中奖后撤单"
	),
        "610"  => array(
		"发起人撤单",
		"发起人撤单"
	),
        "620"  => array(
		"未满员撤单",
		"未满员撤单"
	),
    "1000" => array(
        "未中奖",
        "未中奖"
    ),
    "2000" => array(
        "已中奖",
        "已中奖"
    )
);

$caipiao_mystatus_cfg = array(
    "2000" => array(
        "0" => array(
            "系统算奖中",
            "系统算奖中"
        ),
        "1" => array(
            "系统已派奖",
            "已派奖"
        ),
        "2" => array(
            "大奖待审核",
            "大奖待审核"
        ),
        "3" => array(
            "人工已派奖",
            "人工已派奖"
        ),
        "4" => array(
            "派奖失败",
            "派奖失败"
        ),
        "5" => array(
            "奖金已自提",
            "奖金已自提"
        )
    )
);

$caipiao_abnormal_cfg = array(
    "-1"   => "未知错误",
    "1"    => "失败",
    "2"    => "输入参数有错",
    "3"    => "缺少参数",
    "4"    => "系统错误",
    "5"    => "无权访问或操作该系统",
    "6"    => "token过期",
    "7"    => "token解密失败",
    "8"    => "token与uid不匹配",
    "9"    => "token与username不匹配",
    "3549" => "缺少请求的参数",
    "3550" => "投注内容格式校验错误",
    "3551" => "投注的注数检验错误",
    "3552" => "投注的金额检验错误",
    "3554" => "期号不存在",
    "3555" => "期号不在销售中",
    "3557" => "查询的目标不存在",
    "3558" => "订单已失效，不能支付",
    "3559" => "无效的参数",
    "3660" => "订单无效",
    "3661" => "错误的合买注数",
    "3662" => "支付失败，发送网络错误",
    "3663" => "用户没有访问权限"
);
$chase_manage_cfg = array(
	'700' => '追号完成',
	'240' => '追号中',
	'0' => '待付款',
	'500' => '中奖后停止追号',
	'20' => '订单过期',
    '1000' => '已付款',
);

$caipiao_cfg_array = array(
    'caipiao_cfg'        => $caipiao_cfg,
    'caipiao_type_cfg'   => $caipiao_type_cfg,
    'caipiao_status_cfg' => $caipiao_status_cfg,
    'caipiao_ms_cfg'     => $caipiao_mystatus_cfg,
    'caipiao_ab_cfg'     => $caipiao_abnormal_cfg,
	'chase_manage_cfg'   => $chase_manage_cfg
);

$config['caipiao_all_cfg'] = $caipiao_cfg_array;
