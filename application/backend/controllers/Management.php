<?php

/**
 * Copyright (c) 2015,上海二三四五网络科技有限公司.
 * 摘    要: 运营管理系统
 * 作    者: 刁寿钧
 * 修改日期: 2015/5/26
 * 修改时间: 17:00
 */
class Management extends MY_Controller
{
    private $tczq_ctype = array(
        '1' => '胜负14场/任选九',
        '2' => '六场半全场',
        '3' => '四场进球彩'
    );
    private $lrule = array(
        'ssq'  => array(
            'name'     => '双色球',
            'issueLen' => '7',
            'rule'     => array(
                '0' => '2',
                '2' => '2',
                '4' => '3',
            ),
        ),
        'dlt'  => array(
            'name'     => '大乐透',
            'issueLen' => '5',
            'rule'     => array(
                '1' => '2',
                '3' => '3',
                '6' => '2',
            ),
        ),
        'qxc'  => array(
            'name'     => '七星彩',
            'issueLen' => '5',
            'rule'     => array(
                '2' => '3',
                '5' => '2',
                '0' => '2',
            ),
        ),
        'qlc'  => array(
            'name'     => '七乐彩',
            'issueLen' => '7',
            'rule'     => array(
                '1' => '2',
                '3' => '2',
                '5' => '3',
            ),
        ),
        //每天一期
        'fc3d' => array(
            'name'     => '福彩3D',
            'issueLen' => '7',
            'rule'     => array(
                '0' => '1',
            ),
        ),
        'pl3'  => array(
            'name'     => '排列三',
            'issueLen' => '7',
            'rule'     => array(
                '0' => '1',
            ),
        ),
        'pl5'  => array(
            'name'     => '排列五',
            'issueLen' => '7',
            'rule'     => array(
                '0' => '1',
            ),
        ),
        'syxw' => array(
            'name'     => '十一选五',
            'issueLen' => '8',
            'rule'     => array(
                '0' => '1',
            ),
        ),
    	'jxsyxw' => array(
    		'name'     => '新11选5',
    		'issueLen' => '8',
    		'rule'     => array(
    			'0' => '1',
    		),
    	),
    	'hbsyxw' => array(
    		'name'     => '惊喜11选5',
    		'issueLen' => '8',
    		'rule'     => array(
    			'0' => '1',
    		),
    	),
        'gdsyxw' => array(
            'name'     => '乐11选5',
            'issueLen' => '8',
            'rule'     => array(
                '0' => '1',
            ),
        ),
    	'ks' => array(
    		'name'     => '上海快三',
    		'issueLen' => '11',
    		'rule'     => array(
    			'0' => '1',
    		),
    	),
    	'jlks' => array(
    		'name'     => '吉林快三',
    		'issueLen' => '11',
    		'rule'     => array(
    			'0' => '1',
    		),
    	),     
        'jxks' => array(
            'name'     => '江西快三',
            'issueLen' => '11',
            'rule'     => array(
                '0' => '1',
            ),
        ),
        'klpk' => array(
            'name'     => '快乐扑克',
            'issueLen' => '8',
            'rule'     => array(
                '0' => '1',
            ),
        ),
        'cqssc' => array(
            'name'     => '老时时彩',
            'issueLen' => '9',
            'rule'     => array(
                '0' => '1',
            ),
        ),
        'bjdc' => array(
            'name' => '北京单场',
        ),
        'sfgg' => array(
            'name' => '胜负过关',
        ),
        'sfc'  => array(
            'name' => '胜负彩14场',
        ),
    	'rj'  => array(
    		'name' => '任选九',
    	),
        'bqc'  => array(
            'name' => '半全场',
        ),
        'jqc'  => array(
            'name' => '进球彩',
        ),
    );
    
    private $monitorKind = array(
        'ssq'  => array(
            'name'     => '双色球',
            'lid' => '51',
        ),
        'dlt'  => array(
            'name'     => '大乐透',
            'lid' => '23529',
        ),
        'qxc'  => array(
            'name'     => '七星彩',
            'lid' => '10022',
        ),
        'qlc'  => array(
            'name'     => '七乐彩',
            'lid' => '23528',
        ),
    	'syxw' => array(
    		'name' => '老11选5',
    		'lid' => '21406',
    	),
    	'jxsyxw' => array(
    		'name' => '新11选5',
    		'lid' => '21407',
    	),
    	'hbsyxw' => array(
    		'name' => '惊喜11选5',
    		'lid' => '21408',
    	),
        'gdsyxw' => array(
            'name' => '乐11选5',
            'lid' => '21421',
        ),
        //每天一期
        'pl3'  => array(
            'name'     => '排列三',
            'lid' => '33',
        ),
        'pl5'  => array(
            'name'     => '排列五',
            'lid' => '35',
        ),
        'fc3d' => array(
            'name'     => '福彩3D',
            'lid' => '52',
        ),
        'jczq' => array(
            'name'     => '竞彩足球',
            'lid' => '42',
        ),
        'jclq' => array(
            'name'     => '竞彩篮球',
            'lid' => '43',
        ),
        'sfc'  => array(
            'name' => '胜负彩14场',
            'lid' => '11',
        ),
        'rj'  => array(
            'name' => '任选九',
            'lid' => '19',
        ),
        'bqc'  => array(
            'name' => '半全场',
            'lid' => '12',
        ),
        'jqc'  => array(
            'name' => '进球彩',
            'lid' => '13',
        ),
    	"gj"	=> array(
    		"name" => "冠军彩",
    		"lid" => "44",
    	),
    	"gyj"    => array(
    		"name" => "冠亚军彩",
    		"lid" => "45",
    	),
    	"ks"    => array(
    		"name" => "上海快三",
    		"lid" => "53",
    	),
    	"jlks"    => array(
    		"name" => "吉林快三",
    		"lid" => "56",
    	),  
        "jxks"    => array(
            "name" => "江西快三",
            "lid" => "57",
        ),
        "klpk"  => array(
            "name" => "快乐扑克",
            "lid" => "54",
        ),
        "cqssc"  => array(
            "name" => "老时时彩",
            "lid" => "55",
        ),
    );

    private $lottery= array(
        "51"    => array(
            "py"   => "ssq",
            "name" => "双色球"
        ),
        "23529" => array(
            "py"   => "dlt",
            "name" => "大乐透"
        ),
        "23528" => array(
            "py"   => "qlc",
            "name" => "七乐彩"
        ),
        "10022" => array(
            "py"   => "qxc",
            "name" => "七星彩"
        ),
        "21406" => array(
            "py"   => "syxw",
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
    		"py"   => "syxw",
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
    		"py"   => "hbsyxw",
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
            "py"   => "gdsyxw",
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
                "1"  => array(
                    "py"   => "dxds",
                    "name" => "大小单双"
                ),
                "10"  => array(
                    "py"   => "1xzhix",
                    "name" => "一星单式"
                ),
                "20"  => array(
                    "py"   => "2xzhixds",
                    "name" => "二星单式"
                ),
                "21" => array(
                    "py"   => "2xzhixfs",
                    "name" => "二星复式"
                ),
                "23" => array(
                    "py"   => "2xzux",
                    "name" => "二星组选"
                ),
                "25"  => array(
                    "py"   => "2xhz",
                    "name" => "二星和值"
                ),
                "26" => array(
                    "py"   => "2xzuxhz",
                    "name" => "二星组选和值"
                ),
                "27" => array(
                    "py"   => "2xzuxfs",
                    "name" => "二星组选复式"
                ),
                "30"  => array(
                    "py"   => "3xzhixds",
                    "name" => "三星单式"
                ),
                "31" => array(
                    "py"   => "3xzhixfs",
                    "name" => "三星复式"
                ),
                "33" => array(
                    "py"   => "3xzu3",
                    "name" => "三星组三"
                ),
                "34"  => array(
                    "py"   => "3xzu6",
                    "name" => "三星组六"
                ),
                "35" => array(
                    "py"   => "3xhz",
                    "name" => "三星和值"
                ),
                "36" => array(
                    "py"   => "3xzuxhz",
                    "name" => "三星组选和值"
                ),
                "37"  => array(
                    "py"   => "3xzu3fs",
                    "name" => "三星组三复式"
                ),
                "38" => array(
                    "py"   => "3xzu6fs",
                    "name" => "三星组六复式"
                ),
                "40" => array(
                    "py"   => "5xzhixds",
                    "name" => "五星单式"
                ),
                "41"  => array(
                    "py"   => "5xzhixfs",
                    "name" => "五星复式"
                ),
                "43"  => array(
                    "py"   => "5xtx",
                    "name" => "五星通选"
                ),
            )
        ),
        "33"    => array(
            "py"   => "pl3",
            "name" => "排列三",
            "play" => array(
                "0"  => array(
                    "py"   => "hh",
                    "name" => "混合"
                ),
                "1" => array(
                    "py"   => zx,
                    "name" => "直选"
                ),
                "2" => array(
                    "py"   => 'z3',
                    "name" => "组3"
                ),
                "3" => array(
                    "py"   => 'z6',
                    "name" => "组6"
                )
            )
        ),
        "35"    => array(
            "py"   => "pl5",
            "name" => "排列五"
        ),
        "52"    => array(
            "py"   => "fc3d",
            "name" => "福彩3D",
            "play" => array(
                "0"  => array(
                    "py"   => "hh",
                    "name" => "混合"
                ),
                "1" => array(
                    "py"   => 'zx',
                    "name" => "直选"
                ),
                "2" => array(
                    "py"   => 'z3',
                    "name" => "组3"
                ),
                "3" => array(
                    "py"   => 'z6',
                    "name" => "组6"
                )
            )
        ),
    	"44"	=> array(
    		"py"   => "gj",
    		"name" => "冠军彩"
    	),
    	"45"    => array(
    		"py"   => "gyj",
    		"name" => "冠亚军彩"
    	),
    );

    public function __construct()
    {
        parent::__construct();
        $this->load->library('tools');
        $this->config->load('msg_text');
        $this->config->load('caipiao');
        $this->config->load('order');
        $this->order_status = $this->config->item('cfg_orders');
        $this->msg_text_cfg = $this->config->item('msg_text_cfg');
        $this->match_sale_status = $this->config->load('match_sale_status');
        foreach ($this->config->item('caipiao_all_cfg') as $key => $value)
        {
            $this->$key = $value;
        }
    }

    /**
     * 参    数：
     * 作    者：刁寿钧
     * 功    能：期次管理页面
     * 修改日期：2015-06-25
     */
    public function manageIssue()
    {
    	$this->check_capacity('3_1_1');
        $this->load->model('model_issue_cfg', 'issue');
        $type = $this->input->get("type", TRUE);
        if (empty($type))
        {
            $type = 'ssq';            
        }
        $searchData = array(
            "type"       => $type,
            "start_time" => $this->input->get("start_time", TRUE),
            "end_time"   => $this->input->get("end_time", TRUE),
        );

        $per_page = 100;
        $pageMethod = in_array($type, array('bjdc', 'sfgg', 'sfc', 'rj', 'bqc', 'jqc')) ? "pageMethod_$type" : "pageMethod";
        $defaultCount = $this->issue->$pageMethod($searchData);

        $defaultPage = ceil(intval($defaultCount[0]) / $per_page);
        $defaultPage = $defaultPage ? $defaultPage : 1;
        if (in_array($type, array('syxw', 'jxsyxw', 'ks', 'jlks', 'jxks', 'hbsyxw', 'klpk', 'cqssc', 'gdsyxw')))
        {
            $defaultPage = 1;
        }

        $page = intval($this->input->get("p"));
        $page = $page < 1 ? $defaultPage : $page;


        $this->filterTime($searchData['start_time'], $searchData['end_time']);
        $method = in_array($type, array('bjdc', 'sfgg', 'sfc', 'rj', 'bqc', 'jqc'))
            ? "getIssueList_$type"
            : "getIssueList_number";
        
        $result = $this->issue->$method($searchData, $page, $per_page);

        $issueInfo = array();
        if ( ! empty($result[0]))
        {
            $fields = array('id', 'issue', 'sale_time', 'end_time', 'award_time', 'awardNum', 'sale', 'pool',
                'bonusDetail', 'rstatus', 'showEndTime');
            foreach ($result[0] as $k => $issue)
            {
                foreach ($fields as $field)
                {
                    $issueInfo[$k][$field] = $issue[$field];
                }
                $issueInfo[$k]['compare_status'] = $issue['status'];
                $issueInfo[$k]['status'] = $this->issue->showIssueStatus($type, $issue);
                $issueInfo[$k]['awardInfo'] = $this->getAwardedInfoByStatus($issueInfo[$k]['status']);
            }
        }

        $pageConfig = array(
            "page"     => $page,
            "npp"      => $per_page,
            "allCount" => $result[1]
        );

        $pages = get_pagination($pageConfig);
        $info = array(
            "search" => $searchData,
            "result" => $issueInfo,
            "lrule"  => $this->lrule,
            "pages"  => $pages,
        );

        if (in_array($type, array('syxw', 'jxsyxw', 'hbsyxw', 'gdsyxw')))
        {
            $this->load->view("management/issueSyxw", $info);
        }
        elseif (in_array($type, array('ks')))
        {
        	$this->load->view("management/issueKs", $info);
        }
        elseif (in_array($type, array('jlks')))
        {
        	$this->load->view("management/issueJlks", $info);
        }
        elseif (in_array($type, array('jxks')))
        {
            $this->load->view("management/issueJxks", $info);
        }
        elseif (in_array($type, array('klpk')))
        {
            $this->load->view("management/issueKlpk", $info);
        }
        elseif (in_array($type, array('cqssc')))
        {
            $this->load->view("management/issueCqssc", $info);
        }
        elseif (in_array($type, array('bjdc', 'sfgg')))
        {
            $this->load->view("management/issueBjdc", $info);
        }
        elseif (in_array($type, array('sfc', 'rj', 'bqc', 'jqc')))
        {
            $this->load->view("management/issueTczq", $info);
        }
        else
        {
            $this->load->view("management/issue", $info);
        }
    }

    /**
     * 参    数：status
     * 作    者：刁寿钧
     * 功    能：派奖相关信息
     * 修改日期：2015-06-25
     */
    private function getAwardedInfoByStatus($status)
    {
        if($status == '结期')
        {
        	$info = '已派奖';
        }
        elseif ($status == '未开启' || $status == '开启' || $status == '在售')
        {
            $info = '';
        }
        else
        {
            $info = '未派奖';
        }

        return $info;
    }

    //期次详情页面　后面待改成issueDetail
    /**
     * 参    数：
     * 作    者：刁寿钧
     * 功    能：期次详情页面
     * 修改日期：2015-06-25
     */
    public function detail()
    {
        $this->check_capacity('3_1_2');
        $this->load->model('model_issue_cfg', 'issue');
        $lrule = $this->lrule;
        $data = $this->input->get(NULL, TRUE);
        if (in_array($data['lid'], array('sfc', 'rj', 'bqc', 'jqc')))
        {
            $Method = "getDetailList_" . $data['lid'];
            $info = $this->issue->$Method($data);
        }
        else
        {
            $info = $this->issue->getDetailList($data);
        }
        $info['name'] = $lrule[$data['lid']]['name'];
        $info['bonusDetail'] = json_decode($info['bonusDetail'], TRUE);
        $info['lid'] = $data['lid'];
        $this->load->view("management/issueDetail", $info);
    }

    /**
     * 参    数：
     * 作    者：刁寿钧
     * 功    能：开启期次接口
     * 修改日期：2015-06-25
     */
    public function openIssue()
    {
        $this->load->model('model_issue_cfg', 'issue');
        $type = $this->input->post('type', true);
        $issue = $this->input->post('issue', true);
        $this->issue->openIssue($type, $issue);
        $ok = true;
        $msg = $ok ? '已成功' : '开启失败';

        echo json_encode(compact('ok', 'msg'));
    }

    /**
     * 参    数：
     * 作    者：刁寿钧
     * 功    能：完结期次接口
     * 修改日期：2015-06-25
     */
    public function closeIssue()
    {
        $this->check_capacity('3_1_3', true);
        $this->load->model('model_issue_cfg', 'issue');
        $type = $this->input->post('type', true);
        $issue = $this->input->post('issue', true);
        $canClose = $this->issue->canClose($type, $issue);
        if ($canClose)
        {
            $ok = $this->issue->closeIssue($type, $issue);
            $message = $ok ? '已成功' : '完结失败';
        }
        else
        {
            $ok = false;
            $message = '只有派奖中的期次才能执行完结期次操作！';
        }
        $lname = $this->lrule[$type]['name'];
        $this->syslog(13, $lname."第".$issue."期进行完结期次操作" );
        echo json_encode(compact('ok', 'message'));
    }

    /**
     * 参    数：
     * 作    者：刁寿钧
     * 功    能：删除期次接口
     * 修改日期：2015-06-25
     */
    public function deleteIssue()
    {
        $this->check_capacity('15_4');
        $this->load->model('model_issue_cfg', 'issue');
        $return = FALSE;
        $issues = $this->input->post('issues', TRUE);
        $type = $this->input->post('type', TRUE);
        if ( ! empty($issues))
        {
        	if(in_array($type, array('syxw', 'jxsyxw', 'ks', 'jlks', 'jxks', 'hbsyxw', 'klpk', 'cqssc', 'gdsyxw')))
        	{
        		$return = $this->issue->deleteIssue($issues, $type);
        	}
        	else
        	{
        		die($return);
        	}
        }
        $lname = array('jxsyxw' => '江西十一选五', 'syxw' => '十一选五', 'ks' => '上海快三', 'jlks' => '吉林快三', 'jxks' => '江西快三', 'hbsyxw' => '湖北十一选五', 'klpk' => '快乐扑克', 'cqssc' => '老时时彩', 'gdsyxw' => '广东十一选五');
        foreach($issues as $issue)
        {
            $this->syslog(13, $lname[$type] . "第".$issue."期进行删除期次操作" );
        }
        echo $return;
    }

    /**
     * 参    数：
     * 作    者：刁寿钧
     * 功    能：期次管理重算奖金接口
     * 修改日期：2015-06-25
     */
    public function calculateAward()
    {
        $this->check_capacity('3_1_3', true);
        $this->load->model('model_retrybonus', 'retryBonus');
        $type = $this->input->post('type', TRUE);
        $pIssue = $this->input->post('issue', TRUE);
        $ok = $this->retryBonus->calculateAward($type, $pIssue);
        $message = $ok ? '重算成功' : '重算失败';
        $lname = $this->lrule[$type]['name'];
        $this->syslog(13, $lname."第".$pIssue."期进行重算奖金操作" );
        echo json_encode(compact('ok', 'message'));
    }

    /**
     * 参    数：
     * 作    者：刁寿钧
     * 功    能：对阵管理重算奖金接口
     * 修改日期：2015-06-25
     */
    public function calculateMatchAward()
    {
    	$env = $this->input->post('env');
    	$this->checkenv($env, true);
        $this->load->model('model_retrybonus', 'retryBonus');
        $type = $this->input->post('type', TRUE);
        $mid = $this->input->post('mid', TRUE);
        $ok = $this->retryBonus->calculateMatchAward($type, $mid);
        $msg = $ok ? '重算成功' : '重算失败';

        echo json_encode(compact('ok', 'msg'));
    }


    /**
     * 参    数：
     * 作    者：刁寿钧
     * 功    能：对阵管理页面
     * 修改日期：2015-06-25
     */
    public function manageMatch()
    {
    	$this->check_capacity('3_2_1');
        $this->load->model('model_managematch', 'Model_match');
        $this->msg_text_cfg = $this->config->item('msg_text_cfg');
        $this->load->helper(array("fn_common"));
        $matchType = $this->input->get("type", TRUE);
        if (empty($matchType))
        {
            $matchType = 'bjdc';
        }
        switch ($matchType)
        {
            case 'bjdc':
                $this->bjdc();
                break;

            case 'bdsfgg':
                $this->bdsfgg();
                break;

            case 'tczq':
                $this->tczq();
                break;

            case 'jczq':
                $this->jczq();
                break;

            case 'jclq':
                $this->jclq();
                break;

            default:
                show_404();
                break;
        }
    }

    /**
     * 参    数：
     * 作    者：刁寿钧
     * 功    能：更新赛事销售状态接口
     * 修改日期：2015-06-25
     */
    public function updateMatchSaleStatus()
    {
    	$env = $this->input->post('env');
    	$this->checkenv($env, true);
        $this->check_capacity('3_2_2', true);
        $type = $this->input->post('type', true);
        $saleStatus = $this->input->post('status', true);
        $ssid = $this->input->post('ssid', true);
        $id = $this->input->post('id', true);
        $mid = $this->input->post('mid', true);
        $this->load->model('model_managematch', 'Model_match');
        $ok = $this->Model_match->modifySaleStatusById($type, $id, $saleStatus);
        $message = $ok ? '已成功' : '设置失败';
        $saleStatusName = $this->getSaleStatusNameSet($type);
        $saleStatu = $this->getSaleStatusSet($type);
        $saleStatusStrMap = $this->composeSaleStatusStr($saleStatu, $saleStatusName);
        switch($type)
        {
            case 'bjdc':
                $type = '北京单场';
                break;
            case 'bdsfgg':
                $type = '北单胜负过关';
                break;
            case 'jczq':
                $type = '竞彩足球';
                break;
            case 'jclq':
                $type = '竞彩篮球';
                break;
        }
        foreach($saleStatusStrMap as $key => $value)
        {
            if($type == "北京单场")
            {
                if($key == $saleStatus)
                {
                    $this->syslog(14, $type."第".$mid."期".str_pad($ssid, 3,"0", STR_PAD_LEFT)."场修改赛事销售状态对（".$value."玩法）开售操作");

                }
            }
            else
            {
                if($key == $saleStatus)
                {
                    $types = ($type == "竞彩篮球" ? 'jclq' : "jczq");
                    $data = $this->Model_match->selcetData($types, $id);
                    foreach ($data as $mids)
                    {
                        $this->syslog(14, $type.$mids['mid'].$mid."修改赛事销售状态对（".$value."玩法）开售操作");
                    }
                }
            }
        }

         if($type == "北单胜负过关" && ($saleStatus == 0))
         {
             $this->syslog(14, $type."第".$mid."期".str_pad($ssid, 3,"0", STR_PAD_LEFT)."场修改赛事销售状态对（胜负过关玩法）停售操作");
         }
        echo json_encode(compact('ok', 'message'));
    }

    /**
     * 参    数：
     * 作    者：刁寿钧
     * 功    能：北京单场页面
     * 修改日期：2015-06-25
     */
    private function bjdc()
    {
        $matchType = 'bjdc';
        $mids = $this->Model_match->fetchMatchIds($matchType, array(), 10);
        $mid = $this->input->get("mid", TRUE);
        $mid OR $mid = $mids[0];
        $search = compact('mid');

        $result = $this->Model_match->fetchMatchesByMid($matchType, $mid);
        $saleStatusName = $this->getSaleStatusNameSet($matchType);
        $saleStatus = $this->getSaleStatusSet($matchType);
        $saleStatusStrMap = $this->composeSaleStatusStrMap($saleStatus, $saleStatusName);

        $viewFile = 'management/match' . ucfirst($matchType);
        $this->load->view($viewFile, compact('mids', 'search', 'result', 'saleStatusName', 'saleStatus',
            'saleStatusStrMap'));
    }

    /**
     * 参    数：type
     * 作    者：刁寿钧
     * 功    能：获取某一彩种所有赛事状态的名称
     * 修改日期：2015-06-25
     */
    private function getSaleStatusNameSet($type)
    {
        $allSaleStatusName = $this->config->item('saleStatusName');
        $saleStatusName = $allSaleStatusName[$type];
        return $saleStatusName;
    }

    /**
     * 参    数：type
     * 作    者：刁寿钧
     * 功    能：获取某一彩种所有赛事销售状态
     * 修改日期：2015-06-25
     */
    private function getSaleStatusSet($type)
    {
        $allSaleStatus = $this->config->item('saleStatus');
        $saleStatus = $allSaleStatus[$type];
        return $saleStatus;
    }

    /**
     * 参    数：saleStatusSet
     *           saleStatusNameSet
     * 作    者：刁寿钧
     * 功    能：构造一个哈希，由数字映射到赛事销售状态的组合
     * 修改日期：2015-06-25
     */
    private function composeSaleStatusStrMap($saleStatusSet, $saleStatusNameSet)
    {
        $saleStatusStrMap = array();
        $maxStatus = array_sum($saleStatusSet);
        $keys = range(1, $maxStatus);
        $saleStatusToName = array_combine($saleStatusSet, $saleStatusNameSet);
        foreach ($keys as $key)
        {
            $strAry = array();
            foreach ($saleStatusToName as $status => $name)
            {
                if ($key & $status)
                {
                    array_push($strAry, $name);
                }
            }
            $str = implode('; ', $strAry);
            $saleStatusStrMap[$key] = $str;
        }

        return $saleStatusStrMap;
    }
    /**
     * 参    数：saleStatusSet
     *           saleStatusNameSet
     * 作    者：刁寿钧
     * 功    能：构造一个哈希，由数字映射到赛事销售状态的组合
     * 修改日期：2015-06-25
     */
    private function composeSaleStatusStr($saleStatusSet, $saleStatusNameSet)
    {
        $saleStatusStrMap = array();
        $maxStatus = array_sum($saleStatusSet);
        $keys = range(1, $maxStatus);
        $saleStatusToName = array_combine($saleStatusSet, $saleStatusNameSet);
        foreach ($keys as $key)
        {
            $strAry = array();
            foreach ($saleStatusToName as $status => $name)
            {
                if ($key & $status)
                {
                    array_push($strAry, $name);
                }
            }
            $str = implode(', ', $strAry);
            $saleStatusStrMap[$key] = $str;
        }
        return $saleStatusStrMap;
    }
    /**
     * 参    数：
     * 作    者：刁寿钧
     * 功    能：北单胜负过关页面
     * 修改日期：2015-06-25
     */
    private function bdsfgg()
    {
        $matchType = 'bdsfgg';
        $mids = $this->Model_match->fetchMatchIds($matchType, array(), 10);
        $mid = $this->input->get("mid", TRUE);
        $mid OR $mid = $mids[0];
        $search = compact('mid');

        $result = $this->Model_match->fetchMatchesByMid($matchType, $mid);

        $saleStatusName = $this->getSaleStatusNameSet($matchType);
        $saleStatus = $this->getSaleStatusSet($matchType);
        $saleStatusStrMap = $this->composeSaleStatusStrMap($saleStatus, $saleStatusName);

        $viewFile = 'management/match' . ucfirst($matchType);
        $this->load->view($viewFile, compact('mids', 'search', 'result', 'saleStatusStrMap'));
    }

    /**
     * 参    数：
     * 作    者：刁寿钧
     * 功    能：体彩足球页面
     * 修改日期：2015-06-25
     */
    private function tczq()
    {
        $matchType = 'tczq';
        $ctype = $this->input->post("ctype", TRUE);
        $ctype OR $ctype = 1;
        $mids = $this->Model_match->fetchMatchIds($matchType, compact('ctype'), 10);
        $mid = $this->input->post("mid", TRUE);
        if ( ! in_array($mid, $mids))
        {
            $mid = $mids[0];
        }

        $search = compact('mid', 'ctype');
        $result = $this->Model_match->fetchMatches($matchType, $search);
        $ctypes = $this->tczq_ctype;

        $viewFile = 'management/match' . ucfirst($matchType);
        $this->load->view($viewFile, compact('ctypes', 'mids', 'search', 'result'));
    }

    /**
     * 参    数：
     * 作    者：刁寿钧
     * 功    能：竞彩足球页面
     * 修改日期：2015-06-25
     */
    private function jczq()
    {
        $matchType = 'jczq';
        $searchData = array(
            "start_time" => $this->input->post("start_time", TRUE),
            "end_time"   => $this->input->post("end_time", TRUE),
        );
        $this->filteTime($searchData['start_time'], $searchData['end_time']);
        $result = $this->Model_match->list_jczq($searchData);
        $search = $searchData;

        $saleStatusName = $this->getSaleStatusNameSet('jczq');
        $saleStatus = $this->getSaleStatusSet('jczq');
        $saleStatusStrMap = $this->composeSaleStatusStrMap($saleStatus, $saleStatusName);

        $viewFile = 'management/match' . ucfirst($matchType);
        $this->load->view($viewFile, compact('search', 'result', 'saleStatusName', 'saleStatus', 'saleStatusStrMap'));
    }

    /**
     * 参    数：
     * 作    者：刁寿钧
     * 功    能：竞彩篮球页面
     * 修改日期：2015-06-25
     */
    private function jclq()
    {
        $matchType = 'jclq';
        $searchData = array(
            "start_time" => $this->input->post("start_time", TRUE),
            "end_time"   => $this->input->post("end_time", TRUE),
        );
        $this->filteTime($searchData['start_time'], $searchData['end_time']);
        $result = $this->Model_match->list_jclq($searchData);
        $search = $searchData;

        $saleStatusName = $this->getSaleStatusNameSet('jclq');
        $saleStatus = $this->getSaleStatusSet('jclq');
        $saleStatusStrMap = $this->composeSaleStatusStrMap($saleStatus, $saleStatusName);

        $viewFile = 'management/match' . ucfirst($matchType);
        $this->load->view($viewFile, compact('search', 'result', 'saleStatusName', 'saleStatus', 'saleStatusStrMap'));
    }

    /**
     * 参    数：time1
     *           time2
     * 作    者：刁寿钧
     * 功    能：过滤非法时间参数
     * 修改日期：2015-06-25
     */
    private function filteTime(&$time1, &$time2)
    {
        if (!empty($time1) || !empty($time2))
        {
            if (empty($time1))
            {
                $time1 = date("Y-m-d 00:00:00", strtotime('-1 week', strtotime($time2)));
            }
            elseif (empty($time2))
            {
                $time2 = date("Y-m-d 23:59:59", strtotime('+1 week', strtotime($time1)));
            }
            else
            {
                if (strtotime($time1) > strtotime($time2))
                {
                    echo "时间非法";
                    exit;
                }
    
                if (strtotime("-1 week", strtotime($time2)) > strtotime($time1))
                {
                    $time2 = date("Y-m-d 23:59:59", strtotime("+1 week", strtotime($time1)));
                }
            }
        }
        else
        {
            $time2 = date("Y-m-d 23:59:59");
            $time1 = date("Y-m-d 00:00:00");
        }
    }
    
    /**
     * 参    数：
     * 作    者：刁寿钧
     * 功    能：派奖核对页面
     * 修改日期：2015-06-25
     */
    public function checkDistribution()
    {
    	$this->check_capacity('3_3_1');
        $page = intval($this->input->get("p"));
        $per_page = self::NUM_PER_PAGE;
        $page = $page < 1 ? 1 : $page;

        $this->load->library('BetCnName');
        $this->load->model('model_check_distribution', 'checkDistribution');
        $checkItems = $this->checkDistribution->getUnmatchedItems(array('unmatched' => 1, 'distributed' => 0) ,$page, $per_page);
        $pageConfig = array(
            "page"     => $page,
            "npp"      => $per_page,
            "allCount" => $checkItems[1],
        );
        $info = array(
            "checkItems" => $checkItems[0],
            "pages"  => get_pagination($pageConfig),
        );
        $this->load->view('management/checkDistribution', $info);
    }

    /**
     * 参    数：
     * 作    者：刁寿钧
     * 功    能：核对详情页面
     * 修改日期：2015-06-25
     */
    public function distributionDetail()
    {
        $this->check_capacity('3_3_2');
        $this->load->model('model_check_distribution', 'checkDistribution');
        $lotteryId = $this->input->get('lid');
        $issue = $this->input->get('issue');

        $isCsv = $this->input->get('isCsv');
        if ($isCsv)
        {
            $info = $this->checkDistribution->getUnmatchedOrders($lotteryId, $issue);
            $head = array(
                'orderId'      => '开奖信息不一致订单号',
                'bonus'        => '2345税前奖金（元）',
                'ticketBonus'  => '票商税前奖金（元）',
                'margin'       => '2345税后奖金（元）',
                'ticketMargin' => '票商税后奖金（元）',
            );
            $moneyFields = array('bonus', 'ticketBonus', 'margin', 'ticketMargin');
            $body = array();
            $fields = array_keys($head);
            foreach ($info[0] as $order)
            {
                $tempRow = array();
                foreach ($fields as $field)
                {
                    if (in_array($field, $moneyFields))
                    {
                        $tempRow[$field] = m_format($order[$field]);
                    }
                    else
                    {
                        $tempRow[$field] = $order[$field];
                    }
                }
                array_push($body, $tempRow);
            }

            $this->load->library('excel');
            $this->excel->setActiveSheetIndex(0);
            $this->excel->getActiveSheet()->setTitle('不一致订单');

            //just a placeholder
            $ph = 1;
            foreach ($head as $key => $value)
            {
                $this->excel->getActiveSheet()->setCellValueExplicit($this->excel->getColumnForXls($ph) . '1',
                    $value, PHPExcel_Cell_DataType::TYPE_STRING);
                $objStyle = $this->excel->getActiveSheet()->getStyle($this->excel->getColumnForXls($ph ++) . '1');
                $objBorder = $objStyle->getBorders();
                $objBorder->getTop()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
                $objBorder->getTop()->getColor()->setARGB('FFDDDDDD');
                $objBorder->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
                $objBorder->getBottom()->getColor()->setARGB('FFDDDDDD');
                $objBorder->getLeft()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
                $objBorder->getLeft()->getColor()->setARGB('FFDDDDDD');
                $objBorder->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
                $objBorder->getRight()->getColor()->setARGB('FFDDDDDD');

                $objFill = $objStyle->getFill();
                $objFill->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
                $objFill->getStartColor()->setRGB('d6edff');
            }

            $ph = 1;
            foreach ($body as $row)
            {
                $ph ++;
                //yet another placeholder
                $yap = 1;
                foreach ($head as $key => $value)
                {
                    $this->excel->getActiveSheet()->setCellValueExplicit($this->excel->getColumnForXls($yap ++) . $ph,
                        $row[$key], PHPExcel_Cell_DataType::TYPE_STRING);
                }
            }

            $this->load->library('BetCnName');
            $fileName = BetCnName::$BetCnName[$lotteryId] . '-' . $issue . '期派奖核对表.xls';
            header('Content-Type: application/vnd.ms-excel');
            header('Content-Disposition: attachment;filename="' . $fileName . '"');
            header('Cache-Control: max-age=0');

            $objWriter = PHPExcel_IOFactory::createWriter($this->excel, 'Excel5');
            $objWriter->save('php://output');
        }
        else
        {
            $page = intval($this->input->get("p"));
            $per_page = self::NUM_PER_PAGE;
            $page = $page < 1 ? 1 : $page;

            $orders = $this->checkDistribution->getUnmatchedOrders($lotteryId, $issue, $page, $per_page);
            $pageConfig = array(
                "page"     => $page,
                "npp"      => $per_page,
                "allCount" => $orders[1],
            );
            $info = array(
                "orders"    => $orders[0],
                "pages"     => get_pagination($pageConfig),
                "lotteryId" => $lotteryId,
                "issue"     => $issue,
            );
            $this->load->view('management/distributionDetail', $info);
        }
    }

    /**
     * 参    数：
     * 作    者：刁寿钧
     * 功    能：执行派奖接口
     * 修改日期：2015-06-25
     */
    public function forceDistribution()
    {
        $this->check_capacity('3_3_3', true);
        $this->load->model('model_check_distribution', 'checkDistribution');
        $lotteryId = $this->input->post('lid', TRUE);
        $issue = $this->input->post('issue', TRUE);
        $canDistribute = $this->checkDistribution->canDistribute($lotteryId, $issue);
        if ($canDistribute)
        {
            $ok = $this->checkDistribution->forceDistribution($lotteryId, $issue);
            $message = $ok ? '已成功' : '派奖失败';
        }
        else
        {
            $ok = FALSE;
            $message = 'split表尚有未比对完成记录';
        }
        $lname = $this->caipiao_cfg[$lotteryId]['name'];
        $this->syslog(20, $lname."第".$issue."期进行执行派奖操作");
        echo json_encode(compact('ok', 'message'));
    }
    
	/**
     * 参    数：
     * 作    者：胡小明
     * 功    能：执行派奖接口
     * 修改日期：2017-08-21
     */
    public function forceDispatch($issue, $lid=51, $ts='shancai')
    {
        $this->load->model('model_check_distribution', 'checkDistribution');
        $retrun = $this->checkDistribution->forceDispatch($issue, $lid, $ts);
        if($retrun)
        	echo '操作成功';
        else
        	echo '操作失败';
    }

    /**
     * 参    数：
     * 作    者：刁寿钧
     * 功    能：确认派奖接口
     * 修改日期：2015-06-25
     */
    public function fakeDistribution()
    {
        $this->check_capacity('3_3_3', true);
        $this->load->model('model_check_distribution', 'checkDistribution');
        $lotteryId = $this->input->post('lid', true);
        $issue = $this->input->post('issue', true);
        $canDistribute = $this->checkDistribution->canDistribute($lotteryId, $issue);
        if ($canDistribute)
        {
            $ok = $this->checkDistribution->fakeDistribution($lotteryId, $issue);
            $message = $ok ? '确认成功' : '确认失败';
        }
        else
        {
            $ok = FALSE;
            $message = 'split表尚有未比对完成记录';
        }
        $lname = $this->caipiao_cfg[$lotteryId]['name'];
        $this->syslog(20, $lname."第".$issue."期进行执行确认操作");
        echo json_encode(compact('ok', 'message'));
    }

    /**
     * 参    数：
     * 作    者：刁寿钧
     * 功    能：彩种管理页面
     * 修改日期：2015-06-25
     */
    public function manageKind()
    {
    	$this->check_capacity('3_4_1');
        $page = intval($this->input->get("p"));
        $per_page = 100;
        $page = $page < 1 ? 1 : $page;
        $this->load->library('BetCnName');
        $this->load->model('model_lottery_config', 'lotteryConfig');
        $configItem = $this->lotteryConfig->fetchConfigItems($page, $per_page);
		$arrId = array("51", "23529", "10022", "23528", "33", "35", "52", "41", "42", "43", "19", "21406", "44", "45", "21407", "53", "21408", "54", "55","56","57", "21421");
		foreach($configItem[0] as $key => $value)
		{
			if(in_array($value["lotteryId"], $arrId))
			{
				$configItems[$key] = $value;
			}
		}
        $pageConfig = array(
            "page"     => $page,
            "npp"      => $per_page,
            "allCount" => $configItem[1],
        );

        $pages = get_pagination($pageConfig);

        $infos = array(
            "configItems" => $configItems,
            "pages"  => $pages,
        );

        $this->load->view('management/lotteryConfig', $infos);
    }

    /**
     * 参    数：
     * 作    者：刁寿钧
     * 功    能：设置彩种开启参数接口
     * 修改日期：2015-06-25
     */
    public function configLottery()
    {
        $this->check_capacity('3_4_3', true);
    	$lotteryId = $this->input->post('id');
    	$status = $this->input->post('status');
    	$window = $this->input->post('window');
    	$ahead = $this->input->post('ahead');
    	
    	$this->load->model('model_lottery_config', 'lotteryConfig');
        $lotteryId = $this->input->post('id');
        $status = $this->input->post('status');
        $window = $this->input->post('window');
        $ahead = $this->input->post('ahead');
        $res_judge = $this->lotteryConfig->getConfigItems($lotteryId);
        foreach($res_judge as $key => $value)
        {
            if($value["status"] != $status)
            {
                $prestatus = $status;
            }

            if($value["window"] != $window)
            {
                $prewindow = $window;
            }

            if($value["ahead"] != $ahead)
            {
                $preahead = $ahead;
            }
        }
        $data = compact('status', 'window', 'ahead');
        $ok = $this->lotteryConfig->setConfigItems($lotteryId, $data);
        $message = $ok ? '已成功' : '设置失败';
        $status == 0 ? $status = "停售": $status = "开售";
        $lotteryId == 19 ? $lname = "胜负彩/任九" : $lname = $this->caipiao_cfg[$lotteryId]['name'];
        //启动彩种配置任务
        $this->lotteryConfig->updateTaskStop(7, $lotteryId, 0);
        if(isset($prestatus))
        {
            $this->syslog(15, $lname."进行彩种管理修改 销售状态更改为(".$status.")");
        }
        if(isset($prewindow))
        {
            $this->syslog(15, $lname."进行彩种管理修改 开放期次修改为(".$window.")" );
        }
        if(isset( $preahead))
        {
            $this->syslog(15,$lname."进行彩种管理修改 销售提前截止时间修改为(".$ahead.")" );
        }

        echo json_encode(compact('ok', 'message'));
    }

    /**
     * 参    数：
     * 作    者：刁寿钧
     * 功    能：出票监控页面
     * 修改日期：2015-06-25
     */
    public function monitorTicket()
    {
    	$this->check_capacity('3_5_2');
        $this->load->model('model_ordersplit', 'orderSplit');
        $page = intval($this->input->get("p"));
        $page = $page <= 1 ? 1 : $page;
        $searchData = array(
        	'lid' => $this->input->get('lid', true),
        	'status' => $this->input->get('status', true),
        	'playType' => $this->input->get('playType', true),
        	'mid' => $this->input->get('mid', true),
        	'errNum' => $this->input->get('errNum', true),
            'issue' => $this->input->get('issue', true),
            'endTimeOrder' => $this->input->get('endTimeOrder', true),
            'orderId' => $this->input->get('orderId', true),
            'ticket_seller' => $this->input->get('ticket_seller', true),
            'havenot'   => $this->input->get('havenot', true),
            'ticketed'  => $this->input->get('ticketed', true),
            'perPage'   => $this->input->get('perPage', true),
        );
        $searchData['lid'] = empty($searchData['lid']) ? 51 : $searchData['lid'];
        if ($searchData['havenot']) $searchData['havnotendTime'] = $this->orderSplit->getEndTimeByLid($searchData['lid']);
        $perPage = !empty($searchData['perPage']) ? $searchData['perPage'] : self::NUM_PER_PAGE;
        $orders = $this->orderSplit->fetchTicketingOrders($searchData, $page, $perPage);
        $this->load->model('model_order', 'order');
        if (!empty($orders[0])) {
            $orderIds = array();
            $orderInfo = array();
            foreach ($orders[0] as $order) {
                array_push($orderIds, $order['orderId']);
            }
            $otypeRes = $this->order->getOrderTypeByOrderids($orderIds);
            foreach ($otypeRes as $order) {
                $orderInfo[$order['orderId']]['orderType'] = $order['orderType'];
            }
        }
        $pageConfig = array(
        		"page"     => $page,
        		"npp"      => $perPage,
        		"allCount" => $orders[1]
        );

        $pages = get_pagination($pageConfig);
        $ticketSeller = $this->orderSplit->getSeller();
        foreach ($ticketSeller as $sl) {
            $seller[$sl['id']] = $sl['name'];
        }
        $infos = array(
        	'pages'    => $pages,
            'orders' => $orders[0],
            'orderInfo'  => $orderInfo,
            'monitorKind' => $this->monitorKind,
            'search' => $searchData,
        	'page' => $page,
            'ticket_seller' => $seller,
        	'pageNum' => $perPage
        );
        $this->load->view("management/monitorTicket", $infos);
    }
    
    /**
     * 订单人工撤销操作
     */
    public function orderCancel()
    {
    	$this->check_capacity('3_5_5', true);
    	$this->load->model('model_ordersplit', 'orderSplit');
    	$orderIds = $this->input->post('orderIds', true);
    	$result = $this->orderSplit->orderCancel($orderIds);
    	if ($result === false)
    	{
    		return $this->ajaxReturn('n', "撤单操作异常");
    	}
    	
    	$this->syslog(31, "对sub_order_id：" . $orderIds . "进行撤单操作" );
    	return $this->ajaxReturn('y', $this->msg_text_cfg['success']);
    }
    
    /**
     * 切换票商
     */
    public function orderTicket()
    {
    	$this->check_capacity('3_5_5', true);
    	$this->load->model('model_ordersplit', 'orderSplit');
    	$ticketSeller = $this->orderSplit->getSeller();
    	foreach ($ticketSeller as $sl) {
    	    $sellers[$sl['id']] = $sl['name'];
    	}
    	$sellers['5'] = 'huayang';
    	$orderIds = $this->input->post('orderIds', true);
    	$seller = $this->input->post('ticket', true);
    	if(!$sellers[$seller])
    	{
    		return $this->ajaxReturn('n', "选择的票商不存在");
    	}
    	$result = $this->orderSplit->orderTicket($orderIds, $sellers[$seller]);
    	if ($result === false)
    	{
    		return $this->ajaxReturn('n', "手动提票异常");
    	}
    	 
    	$this->syslog(53, "对sub_order_id：" . $orderIds . "手动提票到 $sellers[$seller]" );
    	return $this->ajaxReturn('y', $this->msg_text_cfg['success']);
    }

    /**
     * 参    数：
     * 作    者：刁寿钧
     * 功    能：订单管理页面
     * 修改日期：2015-06-25
     */
    public function manageOrder()
    {
    	$this->check_capacity('3_6_1');
        $this->load->model('model_order', 'order');
        $page = intval($this->input->get("p"));
        $page = $page <= 1 ? 1 : $page;
        $fromType = $this->input->get("fromType", TRUE);
        $searchData = array(
            "name"                 => $this->input->get("name", TRUE),
            "lid"                  => $this->input->get("lid", TRUE),
            "playType"             => $this->input->get("playType", TRUE),
            "issue"                => $this->input->get("issue", TRUE),
            "orderType"            => $this->input->get("orderType", TRUE),
            "start_time"           => $this->input->get("start_time", TRUE),
            "end_time"             => $this->input->get("end_time", TRUE),
            "start_money"          => $this->input->get("start_money", TRUE),
            "end_money"            => $this->input->get("end_money", TRUE),
            "buyPlatform"          => $this->input->get("buyPlatform", true),
            "status"               => $this->input->get("status", TRUE),
            "my_status"            => $this->input->get("my_status", TRUE),
            "channel" 	           => $this->input->get("channel", true),
            "sub_order_id"         => $this->input->get("sub_order_id", true),
            'uid'                  => $this->input->get("uid", true),
        	'seller'               => $this->input->get("seller", true),
            "reg_type"             => $this->input->get("reg_type", true),
        );
        if (empty($searchData['start_time']) && empty($searchData['end_time']) && $fromType !== 'ajax') {
        	$searchData['end_time'] = date("Y-m-d 23:59:59");
        	$searchData['start_time'] = date("Y-m-d 00:00:00");
        }else {
        	$this->filterTime($searchData['start_time'], $searchData['end_time']);
        }
        $result = $this->order->list_orders($searchData, $page, self::NUM_PER_PAGE);
        $this->load->model('model_united_order', 'unitedOrder');
        $pageConfig = array(
            "page"     => $page,
            "npp"      => self::NUM_PER_PAGE,
            "allCount" => $result['count']['count']
        );
        $pages = get_pagination($pageConfig);
        $this->load->model('model_channel');
        $channelRes = $this->model_channel->getChannels();
        foreach ($channelRes as $val){
        	$channels[$val['id']] = $val;
        }
        $this->load->model('model_ordersplit', 'orderSplit');
        $ticketSeller = $this->orderSplit->getSeller();
        foreach ($ticketSeller as $sl) {
            $seller[$sl['id']] = $sl['name'];
        }
        $pageInfo = array(
            "orders"   => $result['data'],
            "fromType" => $fromType,
            "pages"    => $pages,
            "search"   => $searchData,
            "count"    => $result['count'],
            'channels' => $channels,
            'seller'   => $seller,
        );
        echo $this->load->view("management/order", $pageInfo, TRUE);
    }

    /**
     * 参    数：
     * 作    者：刁寿钧
     * 功    能：订单详情
     * 修改日期：2015-06-25
     */
    public function orderDetail()
    {
        $this->check_capacity("3_6_2");
        $this->load->model('model_order', 'order');
        $id = $this->input->get("id", true);
        $order = $this->order->findOrderByOrderId($id);
        $username = $this->input->post("username", true);
        $unitOrders=array();
        $lsDetail = array();
        if ( ! empty($order))
        {
            $this->load->model('model_order_inconsistent', 'inconsistent');
            $order['isConsistent'] = $this->inconsistent->isConsistent($order['orderId']);

            $lotteryMap = $this->config->item('cfg_lidmap');
            $tableTypeTransform = array(
                'pls'  => 'pl3',
                'plw'  => 'pl5',
                'fcsd' => 'fc3d',
            );
            $type = $lotteryMap[$order['lid']];
            if (array_key_exists($type, $tableTypeTransform))
            {
                $type = $tableTypeTransform[$type];
            }
            $this->load->library('issue');
            $pIssue = $this->issue->getPIssueBySIssue($type, $order['issue']);

            $this->load->model('model_issue_cfg', 'issueModel');
            $order['awardNum'] = $this->issueModel->getAwardNum($type, $pIssue);

            $this->load->model('model_ordersplit', 'orderSplit');
            $order['consistencyInfo'] = $this->orderSplit->consistencyInfo($order['lid'], $order['orderId']);
            $order['messageId'] = $this->orderSplit->getMessageId($order['orderId'], $order['lid']);
            if ($order['status'] >= 200)
            {
            	if(in_array($order['lid'], array('42', '43')))
            	{
            		$subOrders = $this->getJjcSplit($order['orderId']);
            	}
            	elseif(in_array($order['lid'], array('44', '45')))
            	{
            		$subOrders = $this->getGjcSplit($order['orderId']);
            	}
            	else
            	{
            		$subOrders = $this->orderSplit->getSplitDetailByOrder($order['orderId'], $order['lid']);
            	}
                $this->load->library('split');
                foreach ($subOrders as & $subOrder)
                {
                    //对慢频彩种 拉取对比奖金核对显示处理：
                    if( in_array( $order['lid'], array('51','23529','23528','10022','33','35','52') ) )
                    {
                        if($subOrder['cpstate']!='4')
                        {
                            $order['consistencyInfo'] = '未比对';
                        }else{
                            if( ($subOrder['bonus']!=$subOrder['ticketBonus'] ) || ($subOrder['margin']!=$subOrder['ticketMargin']))
                            {
                                $order['consistencyInfo'] = '不一致';
                            }else{
                                $order['consistencyInfo'] = '一致';
                            }
                        }
                    }
                    $subOrder['stakeNum'] = $this->split->computeStakeNum($subOrder);
                }
            }
            else
            {
                $subOrders = array();
            }
            // 加奖
            if(($order['activity_ids'] & 4) == 4)
            {
                $order['add_money'] = 0;
                $this->load->model('Model_activity');
                $jjDdetail = $this->Model_activity->getJjMoney($order['orderId']);
                if(!empty($jjDdetail))
                {
                    $order['add_money'] = $jjDdetail['add_money'];
                }          
            }
            // 玩法
            $this->load->library('split');
            $order['playTypeName'] = $this->split->playTypeName($order);
            // 快乐扑克投注内容处理
            $order['codes'] = $this->getCodesFormat($order);
            // 大乐透乐善码
            if($order['lid'] == 23529 && $order['isChase'] == 1)
            {
                $lsDetail = $this->getLsDetail($id, $order['lid']);
            }
        }
        else
        {
            $subOrders = array();
        }
        $this->load->view("management/orderDetail", compact('order', 'subOrders', 'username', 'lsDetail'));
    }

    /**
     * 快乐扑克投注内容处理
     * @param unknown_type $orderId
     */
    private function getCodesFormat($order)
    {
        $codes = '';
        $orderCodes = array();
        if($order['lid'] == '54')
        {
            if(!empty($order['codes']))
            {
                $this->load->library('BetCnName');
                $orderArr = explode(';', $order['codes']);
                foreach ($orderArr as $key => $code) 
                {
                    $tpl = '';
                    $codeArr = explode(':', $code);
                    // 任选玩法
                    if(in_array($codeArr[1], array('1', '2', '21', '22', '3', '31', '32', '4', '41', '42', '5', '51', '52', '6', '61', '62')))
                    {
                        $tpl .= str_replace(array('01', '11', '12', '13'), array('A', 'J', 'Q', 'K'), $codeArr[0]) . BetCnName::$playTypeCnName['54'][$codeArr[1]];
                    }
                    else
                    {
                        $tpl .= $codeArr[0] . BetCnName::$playTypeCnName['54'][$codeArr[1]];
                    }
                    array_push($orderCodes, $tpl);
                }
            }
            $codes = implode(';', $orderCodes);
        }
        else
        {
            $codes = $order['codes'];
        }
        return $codes;
    }

    /**
     * 竞彩出票明细数据
     * @param unknown_type $orderId
     */
    private function getJjcSplit($orderId)
    {
    	$data = array();
    	$this->load->model('model_ordersplit', 'orderSplit');
    	$split = $this->orderSplit->getJjcOrderDetail($orderId);
    	$preg = array(
    			'42' => '/([\d\-\:]*)(?:\{(.*)\})?\(?(\d*\.?\d*)?\)?/is',
    			'43' => '/([\d\-\:]*)(?:\{(.*)\})?\(?(\d*\.?\d*)?\)?(?:\{(.*)\})?/is'
    	);
    	foreach ($split as $value)
    	{
    		$codes = explode('|', $value['content']);
    		$codeArr = explode('*', $codes[0]);
    		$type = count($codeArr);
    		$content = '';
    		foreach ($codeArr as $key => $val)
    		{
    			$cArr = explode(',', $val);
    			$resBet =  explode('/', $cArr['2']);
    			$count = count($resBet);
    			$odd = '';
    			switch ($cArr['1'])
    			{
    				case 'SPF':
    				case 'SF':
    					foreach ($resBet as $kBet => $vBet)
    					{
    						preg_match($preg[$value['lid']], $vBet, $matches);
    						$info = json_decode($value['info'][$cArr[0]], true);
    						$odd .= $matches[1] . '(' . $info["vs"]["v{$matches[1]}"][0] . ')';
    						if($kBet < $count - 1)
    						{
    							$odd .= '/';
    						}
    					}
    					break;
    				case 'RQSPF':
    				case 'RFSF':
    					foreach ($resBet as $kBet => $vBet)
    					{
    						preg_match($preg[$value['lid']], $vBet, $matches);
    						$info = json_decode($value['info'][$cArr[0]], true);
    						$odd .= $matches[1] . '{' . $info['letVs']['letPoint'][0] . '}' . '(' . $info["letVs"]["v{$matches[1]}"][0] . ')';
    						if($kBet < $count - 1)
    						{
    							$odd .= '/';
    						}
    					}
    					break;
    				case 'CBF':
    					foreach ($resBet as $kBet => $vBet)
    					{
    						preg_match($preg[$value['lid']], $vBet, $matches);
    						$info = json_decode($value['info'][$cArr[0]], true);
    						$score = str_replace(':', '', $matches[1]);
    						$odd .= $matches[1] . '(' . $info["score"]["v{$score}"][0] . ')';
    						if($kBet < $count - 1)
    						{
    							$odd .= '/';
    						}
    					}
    					break;
    				case 'BQC':
    					foreach ($resBet as $kBet => $vBet)
    					{
    						preg_match($preg[$value['lid']], $vBet, $matches);
    						$info = json_decode($value['info'][$cArr[0]], true);
    						$score = str_replace('-', '', $matches[1]);
    						$odd .= $matches[1] . '(' . $info["half"]["v{$score}"][0] . ')';
    						if($kBet < $count - 1)
    						{
    							$odd .= '/';
    						}
    					}
    					break;
    				case 'JQS':
    					foreach ($resBet as $kBet => $vBet)
    					{
    						preg_match($preg[$value['lid']], $vBet, $matches);
    						$info = json_decode($value['info'][$cArr[0]], true);
    						$odd .= $matches[1] . '(' . $info["goal"]["v{$matches[1]}"][0] . ')';
    						if($kBet < $count - 1)
    						{
    							$odd .= '/';
    						}
    					}
    					break;
    				case 'DXF':
    					$in_map = array('0' => 'l', '3' => 'g');
    					foreach ($resBet as $kBet => $vBet)
    					{
    						preg_match($preg[$value['lid']], $vBet, $matches);
    						$info = json_decode($value['info'][$cArr[0]], true);
    						$odd .= $matches[1] . '(' . $info['bs'][$in_map[$matches[1]]][0] . ')' . '{' . $info['bs']['basePoint'][0] . '}';
    						if($kBet < $count - 1)
    						{
    							$odd .= '/';
    						}
    					}
    					break;
    				case 'SFC':
    					foreach ($resBet as $kBet => $vBet)
    					{
    						preg_match($preg[$value['lid']], $vBet, $matches);
    						$info = json_decode($value['info'][$cArr[0]], true);
    						$odd .= $matches[1] . '(' . $info["diff"]["v{$matches[1]}"][0] . ')';
    						if($kBet < $count - 1)
    						{
    							$odd .= '/';
    						}
    					}
    					break;
    			}
    			$content .=  $cArr[0] . ',' .$cArr[1] . ',' . $odd . ',' . $count;
    			if($key < $type - 1)
    			{
    				$content .= '*';
    			}
    		}
    		$content .= '|' . $codes[1];
    		$value['content'] = $content;
    		array_push($data, $value);
    	}
    
    	return $data;
    }
    
    /**
     * 冠军彩出票明细数据
     * @param unknown_type $orderId
     */
    private function getGjcSplit($orderId)
    {
    	$data = array();
    	$this->load->model('model_ordersplit', 'orderSplit');
    	$split = $this->orderSplit->getJjcOrderDetail($orderId);
    	foreach ($split as $value)
    	{
    		$codes = explode('|', $value['content']);
    		$codeArr = explode('=', $codes[0]);
    		$content = '';
    		$resBet =  explode('/', $codeArr['1']);
    		$count = count($resBet);
    		$info = json_decode($value['info'][$codeArr[0]], true);
    		$odd = '';
    		foreach ($resBet as $kBet => $vBet)
    		{
    			preg_match('/(\d+)\(.*\)/', $vBet, $matches);
    			$odd .= $matches[1] . '(' . $info["{$matches[1]}"] . ')';
    			if($kBet < $count - 1)
    			{
    				$odd .= '/';
    			}
    		}
    		$content .=  $codeArr[0] . '=' . $odd . '|' . $codes[1];
    		$value['content'] = $content;
    		array_push($data, $value);
    	}
    
    	return $data;
    }

    /**
     * 参    数：
     * 作    者：liuz
     * 功    能：竞彩足球热门赛事状态及排行
     * 修改日期：2015-11-05
     */
    public function updateJczqHotStatus()
    {
    	$env = $this->input->post('env');
    	$this->checkenv($env, true);
        $this->check_capacity("3_2_3", true);
        $data = $this->input->post('data', true);
        $datas = json_decode($data, true);
        $this->load->model('model_managematch', 'Model_match');
        foreach($datas as $value)
        {
            if(!is_numeric($value['hotid']) || $value['hotid'] != (int)$value['hotid'])
            {
                return $this->ajaxReturn('n', $this->msg_text_cfg['falied']);
            }
            else
            {
                $row = $this->Model_match->modifyHotStatus('jczq', $value);
                $ids = $this->Model_match->selcetData('jczq', $value);
                foreach($ids as $id)
                {
                    $value['hot'] == 1 ? $this->syslog(14, "竞彩足球第".$id['mid']."期".$id['mname']."选中为热门赛事标签" ) : $this->syslog(14, "竞彩足球第".$id['mid']."期".$id['mname']."去除热门赛事标签" );

                }
            }

        }
        if ($row === false)
        {
            return $this->ajaxReturn('n', $this->msg_text_cfg['falied']);
        }
        return $this->ajaxReturn('y', $this->msg_text_cfg['success']);
    }

    /**
     * 参    数：
     * 作    者：liuz
     * 功    能：竞彩篮球热门赛事状态及排行
     * 修改日期：2015-11-05
     */
    public function updateJclqHotStatus()
    {
    	$env = $this->input->post('env');
    	$this->checkenv($env, true);
        $data = $this->input->post('data', true);
        $datas = json_decode($data, true);
        $this->load->model('model_managematch', 'Model_match');
        foreach($datas as $value)
        {
            if(!is_numeric($value['hotid']) || $value['hotid'] != (int)$value['hotid'])
            {
                return $this->ajaxReturn('n', $this->msg_text_cfg['falied']);
            }
            else
            {
                $row = $this->Model_match->modifyHotStatus('jclq', $value);
                $ids = $this->Model_match->selcetData('jclq', $value);
                foreach ($ids as $id)
                {
                    $value['hot'] == 1 ? $this->syslog(14, "竞彩篮球第".$id['mid']."期".$id['mname']."选中为热门赛事标签") : $this->syslog(14, "竞彩篮球第".$id['mid']."期".$id['mname']."去除热门赛事标签");
                }
            }
        }
        if ($row === false)
        {
            return $this->ajaxReturn('n', $this->msg_text_cfg['falied']);
        }
        return $this->ajaxReturn('y', $this->msg_text_cfg['success']);
    }
    
    /**
     * 参    数：
     * 作    者：shigx
     * 功    能：追号管理列表
     * 修改日期：2015-12-24
     */
    public function chaseManage()
    {
        $this->check_capacity("3_7_1");
    	$this->load->model('model_chase');
    	$page = intval($this->input->get("p"));
    	$page = $page <= 1 ? 1 : $page;
    	$searchData = array(
    		"name"			=> $this->input->get("name", TRUE),
    		"lid"    		=> $this->input->get("lid", TRUE),
    		"start_time"	=> $this->input->get("start_time", TRUE),
    		"end_time"      => $this->input->get("end_time", TRUE),
    		"start_money"   => $this->input->get("start_money", TRUE),
    		"end_money"     => $this->input->get("end_money", TRUE),
    		"status"        => $this->input->get("status", TRUE),
    		"setStatus"     => $this->input->get("setStatus", TRUE),
    		"buyPlatform"   => $this->input->get("buyPlatform", true),
    		"registerChannel" 		=> $this->input->get("registerChannel", true),
            "uid" => $this->input->get("uid", true),
            "reg_type"      => $this->input->get("reg_type", true),
    	);
    	$this->filterTime($searchData['start_time'], $searchData['end_time']);
    	$result = $this->model_chase->listChase($searchData, $page, self::NUM_PER_PAGE);
    	$pageConfig = array(
    		"page"     => $page,
    		"npp"      => self::NUM_PER_PAGE,
    		"allCount" => $result[1]
    	);
    	$pages = get_pagination($pageConfig);
    	$this->load->model('model_channel');
    	$channelRes = $this->model_channel->getChannels();
    	foreach ($channelRes as $val){
    		$channels[$val['id']] = $val;
    	}
    	$info = array(
    		"orders"   => $result[0],
    		"pages"    => $pages,
            "fromType" => $this->input->get("fromType", true),
    		"search"   => $searchData,
    		"tj"       => $result[2],
    		'channels' => $channels,
    	);
    	$this->load->view("management/chaseManage", $info);
    }
    
    /**
     * 参    数：
     * 作    者：shigx
     * 功    能：追号详情页
     * 修改日期：2015-12-24
     */
    public function chaseDetail()
    {
        $this->check_capacity("3_7_2");
    	$info = array(
    			'manageOrder' => array(),
    			'subOrders' => array(),
    			'awards' => array()
    	);
    	$this->load->model('model_chase');
    	$chaseId = $this->input->get("id", true);
    	$manageOrder = $this->model_chase->getChaseOrder($chaseId);
    	if($manageOrder)
    	{
    		$result = $this->model_chase->getSubOrder($chaseId);
    		$result['hasstop'] = 0;
    		foreach ($result as $val) {
    			if ($manageOrder['status'] == 240 && $val['status'] == 0 && $val['bet_flag'] == 0) 
				{
					$manageOrder['hasstop'] = '1';
				}
    		}
            // 投注串解析
            $manageOrder['codes'] = $this->getCodesFormat($manageOrder);
    		$info['manageOrder'] = $manageOrder;
    		$info['subOrders'] = $result['subOrders'];
    		$info['awards'] = $result['awards'];
    	}
    	$this->load->view("management/chaseDetail", $info);
    }

    /**
     * 参    数：
     * 作    者：shigx
     * 功    能：撤单管理
     * 修改日期：2016-01-08
     */
    public function chaseCancel()
    {
        $this->check_capacity("3_5_3");
        $this->load->model('model_chase');
        $page = intval($this->input->get("p"));
        $page = $page <= 1 ? 1 : $page;
        $lid = $this->input->get("lid", TRUE) ? $this->input->get("lid", TRUE) : '51';
        $issue = $this->input->get("issue", TRUE);
        if($lid != $this->input->get("selectLid", TRUE)) $issue = '';
        $currentIssue = $this->getCurrentIssue($lid);
        $searchData = array(
        	"name"			=> $this->input->get("name", TRUE),
        	"lid"    		=> $lid,
        	"issue"			=> $issue ? $issue : $currentIssue,
        	"status"        => $this->input->get("status", TRUE),
        );

        $issues = $this->model_chase->getIssueByLid($lid);
        if(empty($issues)) array_push($issues, $currentIssue);
        sort($issues);
        $result = $this->model_chase->listChaseOrder($searchData, $page, self::NUM_PER_PAGE);
        $pageConfig = array(
        	"page"     => $page,
        	"npp"      => self::NUM_PER_PAGE,
        	"allCount" => $result['total']['count']
        );
        $pages = get_pagination($pageConfig);
        $info = array(
        	"orders"   => $result['data'],
        	"pages"    => $pages,
        	"search"   => $searchData,
        	"tj"       => $result['total'],
        	"lidMap"   => $this->lottery,
        	"issues"   => $issues,
        	"currentIssue" => $currentIssue,
        );
        $this->load->view("management/chaseCancel", $info);
    }
    
    /**
     * 查询彩种当当前期次
     * @param unknown_type $lid
     */
    private function getCurrentIssue($lid)
    {
    	$curentIssue = '';
    	$this->load->driver('cache', array('adapter' => 'redis'));
    	$lidMap = array(
    			'51' => array('cache' => 'SSQ_ISSUE'),
    			'52' => array('cache' => 'FC3D_ISSUE'),
    			'33' => array('cache' => 'PLS_ISSUE'),
    			'35' => array('cache' => 'PLW_ISSUE'),
    			'10022' => array('cache' => 'QXC_ISSUE'),
    			'23528' => array('cache' => 'QLC_ISSUE'),
    			'23529' => array('cache' => 'DLT_ISSUE'),
    			'21406' => array('cache' => 'SYXW_ISSUE_TZ'),
    			'21407' => array('cache' => 'JXSYXW_ISSUE_TZ'),
    			'53' => array('cache' => 'KS_ISSUE_TZ'),
                '56' => array('cache' => 'JLKS_ISSUE_TZ'),
    	        '57' => array('cache' => 'JXKS_ISSUE_TZ'),
                '54' => array('cache' => 'KLPK_ISSUE_TZ'),
                '55' => array('cache' => 'CQSSC_ISSUE_TZ'),
    	       '21421' => array('cache' => 'GDSYXW_ISSUE_TZ'),
    	);
    	if(isset($lidMap[$lid]))
    	{
    		$keyName = $lidMap[$lid]['cache'];
    		$REDIS = $this->config->item('REDIS');
    		$cache = $this->cache->get($REDIS[$keyName]);
    		$cache = json_decode($cache, true);
    		$curentIssue = $cache['cIssue']['seExpect'];
    	}
    	return $curentIssue;
    }
    
    /**
     * 期次撤单操作
     */
    public function chaseCancelByIssue()
    {
        $this->check_capacity('3_5_6', true);
    	$this->load->model('model_chase');
    	$lid = $this->input->post('lid', true);
    	$issue = $this->input->post('issue', true);
    	$check = $this->model_chase->checkIssueOrder($lid, $issue);
    	if($check)
    	{
    		return $this->ajaxReturn('n', "当前期次无可撤订单");
    	}
    	$result = $this->model_chase->cancelByIssue($lid, $issue);
    	if ($result === false)
    	{
    		return $this->ajaxReturn('n', $this->msg_text_cfg['falied']);
    	}
    	$lname = $this->lottery[$lid]['name'];
    	$this->syslog(23, $lname."第".$issue."期进行撤单操作" );
    	return $this->ajaxReturn('y', $this->msg_text_cfg['success']);
    }
    
    /**
     * 根据id进行撤单操作
     */
    public function chaseCancelById()
    {
        $this->check_capacity('3_5_6', true);
    	$this->load->model('model_chase');
    	$ids = $this->input->post('ids', true);
    	$lid = $this->input->post('lid', true);
    	$issue = $this->input->post('issue', true);
    	$idsStr = implode("','", $ids);
    	$result = $this->model_chase->cancelById($idsStr);
    	if ($result === false)
    	{
    		return $this->ajaxReturn('n', "撤单操作异常，请确认订单状态");
    	}
    	$lname = $this->lottery[$lid]['name'];
    	$unames = $this->model_chase->getUserNameById($idsStr);
    	$unames = array_unique ( $unames );
    	$this->syslog(23, "{$lname}第{$issue}期(".(implode(',', $unames)).")进行撤单操作" );
    	return $this->ajaxReturn('y', $this->msg_text_cfg['success']);
    }
    
    /**
     * 根据id进行撤单操作
     */
    public function chaseCancelByUser()
    {
        $this->check_capacity("3_7_3", true);
    	$this->load->model('model_chase');
    	$ids = $this->input->post('ids', true);
    	$lid = $this->input->post('lid', true);
    	$idsStr = implode("','", $ids);
    	$result = $this->model_chase->cancelById($idsStr);
    	if ($result === false)
    	{
    		return $this->ajaxReturn('n', "撤单操作异常，请确认订单状态");
    	}
    	$lname = $this->lottery[$lid]['name'];
    	$unames = $this->model_chase->getUserNameById($idsStr);
    	$unames = array_unique ( $unames );
    	return $this->ajaxReturn('y', $this->msg_text_cfg['success']);
    }
    
    public function tictselrManage()
    {
    	$this->check_capacity("3_8_1");
    	$search = array(
    			"start_time" => $this->input->get("start_time", TRUE),
    			"end_time"   => $this->input->get("end_time", TRUE),
    	);
        if (empty($search['start_time'])) {
            $search['start_time'] = date('Y-m-d 00:00:00');
        }
        if (empty($search['end_time'])) {
            $search['end_time'] = date('Y-m-d 23:59:59');
        }
    	$this->filterTime($search['start_time'], $search['end_time']);
    	if ($search['start_time'] < '2016-02-01 00:00:00')
    	{
    		$search['start_time'] = '2016-02-01 00:00:00';
    	}
    	$startorder = date('YmdHis', strtotime($search['start_time']))."000000";
    	$endorder = date('YmdHis', strtotime($search['end_time']))."999999";
    	$this->load->model('model_ordersplit', 'orderSplit');
    	$ticketSeller = $this->orderSplit->getSeller();
    	$rates = $this->orderSplit->getAllRates();
    	$data = $this->orderSplit->gettictselrData($startorder, $endorder);
    	foreach ($ticketSeller as $sl)
    	{
    		$seller[$sl['name']] = $sl['cname'];
    	}
    	foreach ($rates as $rt)
    	{
    		$tmpRate = array('mark' => $rt['mark']);
    		foreach (explode('|', $rt['ticketRate']) as $tr)
    		{
    			$trArr = explode(':', $tr);
    			$tmpRate[$trArr[0]] = doubleval($trArr[1]) * 100;
    		}
    		$rate[$rt['lid']] = $tmpRate;
    	}
    	foreach ($data as $dt)
    	{
    		$datas[$dt['lid']][$dt['ticket_seller']] = $dt['money'];
    	}
    	$res = compact('seller', 'rate', 'datas', 'search');
    	$this->load->view("management/tictselr", $res);
    }
    
    public function tictselrModify()
    {
    	$env = $this->input->post('env');
    	$this->checkenv($env, true);
        $this->check_capacity("3_8_2", true);
    	$data = $this->input->post();
    	$lid = $data['lid'];
    	unset($data['lid']);
    	//单一出票商的彩种分配比例进行限制
    	//qihui
    	//if (in_array($lid, array(54)) && ($data['qihui'] != 100 || $data['caidou'] != 0)) exit(json_encode(array('code' => '1', 'msg' => '单一票商，比例调节失败')));
    	//caidou
    	//if (in_array($lid, array(53)) && ($data['qihui'] != 0 || $data['caidou'] != 100)) exit(json_encode(array('code' => '1', 'message' => '单一票商，比例调节失败')));
        if (in_array($lid, array(55)) && ($data['qihui'] != 100 || $data['caidou'] !== 0)) exit(json_encode(array('code' => '1', 'message' => '单一票商，比例调节失败')));
        $lidSellerMap = array(
            11      => array('qihui', 'caidou', 'hengju'),
            19      => array('qihui', 'caidou', 'hengju'),
            33      => array('qihui', 'caidou'),
            35      => array('qihui', 'caidou'),
            42      => array('qihui', 'caidou', 'huayang', 'hengju', 'funiuniu'),
            43      => array('qihui', 'caidou', 'hengju'),
            44      => array('qihui', 'caidou'),
            45      => array('qihui', 'caidou'),
            51      => array('qihui', 'caidou', 'shancai', 'hengju'),
            52      => array('qihui', 'caidou', 'hengju'),
            53      => array('caidou', 'shancai'),
            54      => array('qihui', 'caidou'),
            55      => array('qihui', 'caidou'),
            56      => array('huayang', 'hengju'),
            57      => array('shancai', 'hengju'),
            10022   => array('qihui', 'caidou'),
            21406   => array('qihui', 'caidou', 'huayang', 'hengju'),
            21407   => array('qihui', 'caidou', 'huayang', 'hengju'),
            21408   => array('qihui', 'caidou', 'huayang'),
            23528   => array('qihui', 'caidou', 'hengju'),
            23529   => array('qihui', 'caidou', 'huayang', 'hengju'),
            21421   => array('caidou', 'hengju'),
        );
        $this->load->model('model_ordersplit', 'orderSplit');
        $ticketSeller = $this->orderSplit->getSeller();
        foreach ($ticketSeller as $sl) {
            $sellerArr[$sl['name']] = $sl['cname'];
        }
        $msgArr = array();
        foreach ($data as $param => $value) {
            if (array_key_exists($param, $sellerArr) && $value > 0 && !in_array($param, $lidSellerMap[$lid])) array_push($msgArr, $sellerArr[$param]);
        }
        if (!empty($msgArr)) {
            if (count($lidSellerMap[$lid]) == 1) exit(json_encode(array('code' => '1', 'message' => '单一票商，比例调节失败')));
            exit(json_encode(array('code' => '1', 'message' => "该彩种不支持".implode('、', $msgArr)."票商出票，比例调节失败")));
        }
    	$this->load->library('BetCnName');
    	$this->orderSplit->modifyRates($lid, $data);
    	$this->syslog(29, "调整".BetCnName::$BetCnName[$lid]."比例为".$data['qihui']."%，".$data['caidou']."%，".$data['shancai']."%，".$data['huayang']."%，".$data['hengju']."%");
    	exit(json_encode(array('code' => '0', 'message' => '调节成功')));
    }
    
    public function tickselrMark() {
        $mark = $this->input->post('mark');
        $this->load->model('model_ordersplit', 'orderSplit');
        $this->orderSplit->modifytickselrMark($mark);
        $slog = array();
        $this->load->library('BetCnName');
        foreach ($mark as $lid => $m) {
            array_push($slog, BetCnName::$BetCnName[$lid]."修改备注：".$m);
        }
        $this->syslog(29, implode(';', $slog));
        exit(json_encode(array('code' => '0', 'message' => '调节成功')));
    }
    
    public function downSplitdetail()
    {
    	set_time_limit(0);
    	$data = $this->input->get();
    	$this->load->model('model_ordersplit', 'orderSplit');
    	$this->load->library('BetCnName');
    	$res = $this->orderSplit->getOrders($data['lid'], $data['start'], $data['end']);
    	
    	$start = strtotime($data['start']);
    	$end = strtotime($data['end']);
    	
    	$this->load->library('excel');
    	$this->excel->setActiveSheetIndex(0);
    	$this->excel->getActiveSheet()->setTitle(BetCnName::$BetCnName[$data['lid']]."子订单详情 ".date('Ymd', $start)."-".date('Ymd', $end));
    	
    	
    	$this->excel->getActiveSheet()->setCellValue('A1', '彩种')->setCellValue('B1', '出票商')
    				->setCellValue('C1', '期次')->setCellValue('D1', '订单编号')->setCellValue('E1', '用户名')
			    	->setCellValue('F1', '出票时间')->setCellValue('G1', '计算奖金')->setCellValue('H1', '计算税后')
			    	->setCellValue('I1', '出票金额')->setCellValue('J1', '票商税前奖金')->setCellValue('K1', '票商税后奖金');
    	
    	
    	foreach ($res as $k => $value)
    	{
    		$this->excel->getActiveSheet()->setCellValue('A'.($k+2), BetCnName::$BetCnName[$data['lid']])->setCellValue('B'.($k+2), $value['ticket_seller'])
    		->setCellValue('C'.($k+2), $value['issue'])->setCellValue('D'.($k+2), $value['orderId'])->setCellValue('E'.($k+2), $value['real_name'])
    		->setCellValue('F'.($k+2), $value['ticket_time'])->setCellValue('G'.($k+2), print_str($value['bonus']))->setCellValue('H'.($k+2), print_str($value['margin']))
    		->setCellValue('I'.($k+2), print_str($value['ticket_money']))->setCellValue('J'.($k+2), print_str($value['bonus_t']))->setCellValue('K'.($k+2), print_str($value['margin_t']));
    	}
    	
    	$this->excel->getActiveSheet()->getStyle('A1:J'.($k+2))->applyFromArray(
    			array('alignment' => array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER)));
    	
    	$fileName = BetCnName::$BetCnName[$data['lid']]."子订单详情 ".date('Ymd', $start)."-".date('Ymd', $end).'.xls';
    	header('Content-Type: application/vnd.ms-excel');
    	header('Content-Disposition: attachment;filename="'.$fileName.'"');
    	header('Cache-Control: max-age=0');
    	
    	$objWriter = PHPExcel_IOFactory::createWriter($this->excel, 'Excel5');
    	$objWriter->save('php://output');
    	$this->syslog(29, "下载附件:".$fileName );
    }
    
    /**
     * 后台推广管理
     */
    public function rebateManage()
    {
    	$this->check_capacity("3_9_1");
    	$this->load->model('model_rebates');

	    $page = intval($this->input->get("p"));
	    $page = $page <= 1 ? 1 : $page;
	    $searchData = array(
	        "info"			=> $this->input->get("info", TRUE),
	        "level"    		=> $this->input->get("level", TRUE),
	        "start_time"	=> $this->input->get("start_time", TRUE),
	        "end_time"      => $this->input->get("end_time", TRUE),
	        "start_money"   => $this->input->get("start_money", TRUE),
	        "end_money"     => $this->input->get("end_money", TRUE),
	        "stop_flag"     => $this->input->get("stop_flag", TRUE),
	    );
	    $result = $this->model_rebates->listManage($searchData, $page, self::NUM_PER_PAGE);
    	    	
    	$pageConfig = array(
    			"page"     => $page,
    			"npp"      => self::NUM_PER_PAGE,
    			"allCount" => $result[1]
    	);
    	$pages = get_pagination($pageConfig);
    	$info = array(
    			"lists"   => $result[0],
    			"pages"    => $pages,
    			"search"   => $searchData,
    	);
    	$this->load->view("management/rebateManage", $info);
    }
    
    /**
     * 获取设置返点弹窗
     */
    public function getRebatePopHtml()
    {
    	$id = $this->input->post('id', true);
    	$this->load->model('model_rebates');
    	$rebate = $this->model_rebates->getRebateById($id);
    	if($rebate)
    	{
    		$rebate_odds = json_decode($rebate['rebate_odds'], true);
    		$this->load->config('rebates');
    		$oddType = $this->config->item('rebate_odds_type');
    		$vdata = array(
    			'rebate_odds' => $rebate_odds,
    			'tips' => '',
    			'oddType' => $oddType,
    		);
    		$msg = $this->load->view("management/rebateOddsPop", $vdata, true);
    		return $this->ajaxReturn('y', $msg);
    	}
    	else
    	{
    		return $this->ajaxReturn('n', '操作失败');
    	}
    }
    
    /**
     * 设置用户返点比例
     */
    public function setRebate()
    {
    	$this->check_capacity("3_9_4", true);
    	$vdata = $this->input->post(null, true);
    	$id = $vdata['rebateId'];
    	unset($vdata['rebateId']);
    	$rdata = array(
    			'rebate_odds' => $vdata,
    	);
    	$this->load->config('rebates');
    	$oddType = $this->config->item('rebate_odds_type');
    	$error = false;
    	foreach ($vdata as $lid => $val)
    	{
    		if(!isset($oddType[$val]))
    		{
    			$error = true;
    			break;
    		}
    	}
    	if($error)
    	{
    		$rdata['tips'] = '注意：比例格式不符合要求！';
    		$rdata['oddType'] = $oddType;
    		$msg = $this->load->view("management/rebateOddsPop", $rdata, true);
    		return $this->ajaxReturn('n', $msg);
    	}
    	
    	$this->load->model('model_rebates');
    	$rebate = $this->model_rebates->getRebateOdds($id);
    	
    	if($rebate)
    	{
    		$rebate_odds = json_decode($rebate['rebate_odds'], true);
    		$flag = false;
    		foreach ($vdata as $lid => $odd)
    		{
    			if($odd < $rebate_odds[$lid])
    			{
    				$flag = true;
    			}
    		}
    		$data['rebate_odds'] = json_encode($vdata);
    		if($flag)
    		{
    			$data['odd_flag'] = 1;
    		}
    		$res = $this->model_rebates->updateRebate($id, $data);
    		if ($res === false)
    		{
    			$rdata['tips'] = '操作失败';
    			$msg = $this->load->view("management/rebateOddsPop", $rdata, true);
    			return $this->ajaxReturn('n', $msg);
    		}
    		$user = $this->model_rebates->get_user_info($rebate['uid']);
    		$this->syslog(35, "调整用户：{$user['uname']};返点比例：{$data['rebate_odds']}" );
    		return $this->ajaxReturn('y', $this->msg_text_cfg['success']);
    	}
    	else 
    	{
    		$rdata['tips'] = '操作失败';
    		$msg = $this->load->view("management/rebateOddsPop", $rdata, true);
    		return $this->ajaxReturn('n', $msg);
    	}
    }
    
    /**
     * 停止用户返点
     */
    public function rebateCancel()
    {
    	$this->check_capacity("25_3", true);
    	$id = intval($this->input->post("cancelId", true));
    	$stop_flag = intval($this->input->post("setStatus", true));
    	$this->load->model('model_rebates');
    	$row = $this->model_rebates->updateStop($id, $stop_flag);
    	if ($row === false)
    	{
    		return $this->ajaxReturn('n', $this->msg_text_cfg['falied']);
    	}
    	$user = $this->model_rebates->get_user_info($id);
    	$this->syslog(35, "开启/停止用户：{$user['uname']}获取返点收益" );
    	return $this->ajaxReturn('y', $this->msg_text_cfg['success']);
    }
    
    /**
     * 添加一级代理
     */
    public function addRebate()
    {
        $this->check_capacity("3_9_3", true);
    	$uname = $this->input->post("uname", true);
    	$phone = $this->input->post("phone", true);
    	$this->load->model('model_rebates');
    	$redatas = $this->model_rebates->checkRebateUser($uname, $phone);
    	if ($redatas['rnum'] == 4)
    	{
    		$res = $this->model_rebates->addRebate($redatas['uid']);
    		if($res)
    		{
    			$this->syslog(35, "添加一级代理：{$uname}" );
    			return $this->ajaxReturn('y', $this->msg_text_cfg['success']);
    		}
    	}
    	return $this->ajaxReturn('n', $this->msg_text_cfg['rebateMsg'][$redatas['rnum']]);
    }
    
    /**
     * 返点详细页
     */
    public function rebateDetail()
    {
    	$this->check_capacity("3_9_2");
    	$id = $this->input->get("id", true);
    	$this->load->model('model_rebates');
    	$result = $this->model_rebates->getRelationUserDetail($id);
    	$this->load->view("management/rebateDetail", $result);
    }
    
    /**
     * 返点明细
     */
    public function rebateDetailList()
    {
    	$id = $this->input->get("id", true);
    	$this->load->model('model_rebates');
    	$rebate = $this->model_rebates->getRebateById($id);
    	if($rebate)
    	{
    		$page = intval($this->input->get("p"));
    		$page = $page <= 1 ? 1 : $page;
    		$searchData = array(
    			"uid" => $rebate['uid'],
    			"start_time" => $this->input->get("start_time", true),
    			"end_time" => $this->input->get("end_time", true),
    			"lid" => $this->input->get("lid", true),
    			"userName" => $this->input->get("userName", true)
    		);
    		$this->filterTime($searchData['start_time'], $searchData['end_time']);
    		$result = $this->model_rebates->rebateDetailsList($searchData, $page, self::NUM_PER_PAGE);
    		$pageConfig = array(
    				"page" => $page,
    				"npp" => self::NUM_PER_PAGE,
    				"allCount" => $result[1]
    		);
    		$pages = get_pagination($pageConfig);
    		$todayIncome = $this->model_rebates->getTodayIncome($rebate['uid']);
    		$pageInfo = array(
    				"lists" => $result[0],
    				"pages" => $pages,
    				"search" => $searchData,
    				"fromType" => $this->input->get("fromType", true),
    				"rebate" => $rebate,
    				"todayIncome" => $todayIncome,
    				"totalMoney" => $result[2]
    		);
    		$this->load->view("management/rebateDetailList", $pageInfo);
    	}
    }
    
    /**
     * 下线列表
     */
    public function subordinate()
    {
    	$id = $this->input->get("id", true);
    	$this->load->model('model_rebates');
    	$rebate = $this->model_rebates->getRebateById($id);
    	if($rebate)
    	{
    		$page = intval($this->input->get("p"));
    		$page = $page <= 1 ? 1 : $page;
    		$searchData = array(
    				"uid" => $rebate['uid'],
    				"start_time" => $this->input->get("start_time", true),
    				"end_time" => $this->input->get("end_time", true),
    				"userName" => $this->input->get("userName", true)
    		);
    		$this->filterTime($searchData['start_time'], $searchData['end_time']);
    		$result = $this->model_rebates->subordinateList($searchData, $page, self::NUM_PER_PAGE);
    		$pageConfig = array(
    				"page" => $page,
    				"npp" => self::NUM_PER_PAGE,
    				"allCount" => $result[1]
    		);
    		$pages = get_pagination($pageConfig);
    		$todayIncome = $this->model_rebates->getTodayIncome($rebate['uid']);
    		$pageInfo = array(
    				"lists" => $result[0],
    				"pages" => $pages,
    				"search" => $searchData,
    				"fromType" => $this->input->get("fromType", true),
    				"rebate" => $rebate,
    		);
    		$this->load->view("management/subordinate", $pageInfo);
    	}
    }
    
    public function huizong() {
        $this->check_capacity('3_5_1');
    	$this->load->model('model_ordersplit');
    	$data = $this->model_ordersplit->getStatics();
    	$this->load->driver('cache', array('adapter' => 'redis'));
    	$REDIS = $this->config->item ( 'REDIS' );
    	$ulidArr = array(51 => 'SSQ_ISSUE', 23529 => 'DLT_ISSUE', 52 => 'FC3D_ISSUE', 33 => 'PLS_ISSUE', 
    			35 => 'PLW_ISSUE', 10022 => 'QLC_ISSUE', 23528 => 'QXC_ISSUE', 11 => 'SFC_ISSUE', 19 => 'SFC_ISSUE');
    	foreach ($ulidArr as $ulid => $cache) {
    		$tmp = json_decode ( $this->cache->get ( $REDIS [$cache] ), true );
    		$issue[$ulid] = $tmp['cIssue']['seExpect'];
    	}
    	$this->load->view("management/huizong", compact('data', 'issue'));
    }
    
    public function adjustUmoney() {
    	$this->check_capacity('3_10_1');
    	$form = $_FILES['file'];
    	
    	$dtwrong = 0;
    	if ($form) {
    		if (pathinfo($form['name'], PATHINFO_EXTENSION) !== 'csv') {
    			$dtwrong = "请上传csv文件";
    		}else {
    			$this->load->model('model_ajust_umoney', 'model');
    			$info = $this->input->post('info');
    			$handle = fopen($form['tmp_name'], "r");
    			$data = array();
    			$allmoney = 0;
    			while ($contents = fgets($handle, filesize ($form['tmp_name']))) {
    				$cArr = explode(',', iconv('gbk', 'utf-8', $contents));
    				if ($cArr[0] > 0) {
    				    $phone = trim($cArr[1]);
    				    $orderId = trim($cArr[2]);
    				    $money = intval(trim($cArr[3])) * 100;
    				    $comment = trim($cArr[4]);
    					if (!$this->model->check_order($cArr[0], $orderId, $phone)) {
    						$dtwrong = "调账记录 UID：{$cArr[0]} 用户名：{$cArr[1]} 该条记录校验异常，请校验修改后再次上传批量调账文件";
    						break;
    					}
    					if ($info['ctype'] == 2) $info['iscapital'] = 0;
    					$data[] = array(
    							'uid' 	   	 => $cArr[0],
    							'orderId'	 => $orderId,
    							'money' 	 => $money,
    							'comment'	 => $comment,
    							'ctype'		 => $info['ctype'],
    							'ismustcost' => (int)$info['ismustcost'],
    							'iscapital'	 => (int)$info['iscapital'],
    							'created'    => date('Y-m-d H:i:s')
    					);
    					$allmoney += $money;
    				}
    			}
    			if (!$dtwrong && count($data) == $info['count'] && $allmoney == $info['money'] * 100) {
    				$this->syslog(41, "已操作上传批量调账订单");
    				$this->model->insertData($data);
    				$dtwrong = 1;
    			}elseif (!$dtwrong) {
    				$dtwrong = "单数或总金额不正确";
    			}
    		}
    	}
    	$this->load->view('management/adjustUmoney', compact('dtwrong'));
    }
    
    public function downloadadjust()
    {
    	$file = str_replace("backend/", '', FCPATH)."source/download/backend/moban.xlsx";
    	header ( "Content-type: application/octet-stream" );
    	Header( "Accept-Ranges:  bytes ");
    	header ( "Content-Disposition: attachment; filename=moban.xlsx" );
    	header ( "Content-Length: " . filesize ( $file ) );
    	readfile ( $file );
    }
    /**
     * 补发邮件操作
     */
    public function supplyEmail()
    {
    	$orderId = $this->input->post("orderId", true);
        $united  = $this->input->post("united", true);
        if($united != 1){
            $this->load->model('model_order', 'order');
            $result = $this->order->getOrderEmail($orderId, 1);
            if ($result)
            {
                    $data = array(
                            'to'	  => $result['email'],
                            'subject' => $result['title'],
                            'message' => $result['content'],
                    );
                    $result = $this->tools->sendMail($data);
                    if($result)
                    {
                            $this->syslog(2, "对订单id：{$orderId}进行补发邮件操作" );
                            return $this->ajaxReturn('y', $this->msg_text_cfg['success']);
                    }
            }
        }
        else
        {
            $this->load->model('model_united_join');
            $unitDatas = $this->model_united_join->getAllByOrderId($orderId, 'subscribeId');
            $subIds = '';
            foreach ($unitDatas as $unitData)
            {
                $subIds.= '"'.$unitData['subscribeId'].'",';
            }
            $subIds = substr($subIds, 0, -1);
            $result = $this->model_united_join->getOrderEmail($subIds);
            foreach ($result as $res)
            {
                if (!empty($res))
                {
                    $data = array(
                        'to' => $res['email'],
                        'subject' => $res['title'],
                        'message' => $res['content']
                    );
                    $status = $this->tools->sendMail($data);
                    if ($status)
                    {
                        $this->syslog(2, "对订单id：{$res['orderId']}进行补发邮件操作");
                    }
                }
            }
            return $this->ajaxReturn('y', $this->msg_text_cfg['success']);
        }
    	return $this->ajaxReturn('n', '操作失败，邮件不存在或未送达');
    }
    /*
     * 合买采种管理
     */
    public function manageHemai()
    {
        $this->check_capacity('3_4_2');
        $page = intval($this->input->get("p"));
        $per_page = self::NUM_PER_PAGE;
        $page = $page < 1 ? 1 : $page;
        $this->load->library('BetCnName');
        $this->load->model('model_lottery_config', 'lotteryConfig');
        $configItem = $this->lotteryConfig->fetchConfigItems($page, $per_page);
		$arrId = array("51", "23529", "10022", "42", "43", "11", "52", "23528", "33");
                foreach($configItem[0] as $key => $value)
		{
			if(in_array($value["lotteryId"], $arrId))
			{
				$configItems[$key] = $value;
			}
		}
        $pageConfig = array(
            "page"     => $page,
            "npp"      => $per_page,
            "allCount" => $configItem[1],
        );

        $pages = get_pagination($pageConfig);

        $infos = array(
            "configItems" => $configItems,
            "pages"  => $pages,
        );

        $this->load->view('management/lotteryUnitedConfig', $infos);
    }

    /**
     * 设置合买提前截止时间和开售状态
     */
    public function configUnitedLottery()
    {
        $this->check_capacity('3_4_4', true);
        $lotteryId = $this->input->post('id');
        $united_ahead = $this->input->post('ahead');
        if ($united_ahead <= 0)
        {
            $message = '时间必须大于0';
            die(json_encode(compact('fail', 'message')));
        }
        $united_status = $this->input->post('status');
        $this->load->model('model_lottery_config', 'lotteryConfig');
        $res_judge = $this->lotteryConfig->getConfigItems($lotteryId);
        foreach ($res_judge as $key => $value)
        {
            if ($value["united_status"] != $united_status)
            {
                $prestatus = $united_status;
            }
            if ($value["united_ahead"] != $united_ahead)
            {
                $preahead = $united_ahead;
            }
        }
        $ok = $this->lotteryConfig->setConfigItems($lotteryId, compact('united_ahead', 'united_status'));
        if ($lotteryId == 11)
        {
            $ok = $this->lotteryConfig->setConfigItems(19, compact('united_ahead', 'united_status'));
        }
        if ($lotteryId == 33)
        {
            $ok = $this->lotteryConfig->setConfigItems(35, compact('united_ahead', 'united_status'));
        }
        $message = $ok ? '已成功' : '设置失败';
        $united_status == 0 ? $united_status = "停售" : $united_status = "开售";
        $lotteryId == 19 ? $lname = "胜负彩/任九" : $lname = $this->caipiao_cfg[$lotteryId]['name'];
        //启动彩种配置任务
        $this->lotteryConfig->updateTaskStop(7, $lotteryId, 0);
        if (isset($prestatus))
        {
            $this->syslog(15, $lname . "进行合买彩种管理修改 销售状态更改为(" . $united_status . ")");
        }
        if (isset($preahead))
        {
            $this->syslog(15, $lname . "进行合买彩种管理修改 销售提前截止时间修改为(" . $united_ahead . ")");
        }
        die(json_encode(compact('ok', 'message')));
    }

    /**
     * 合买管理
     */
    public function manageUnited()
    {
        $this->check_capacity('3_11_1');
        //查询的条件
        $page = intval($this->input->get("p"));
        $page = $page <= 1 ? 1 : $page;
        $searchData = array(
            "name"                 => $this->input->get("united_name", TRUE),
            "lid"                  => $this->input->get("united_lid", TRUE),
            "buyPlatform"          => $this->input->get("united_buyPlatform", true),
            "proportion"           => $this->input->get("united_proportion", TRUE),
            "issue"                => $this->input->get("united_issue", TRUE),
            "start_time"           => $this->input->get("united_start_time", TRUE),
            "end_time"             => $this->input->get("united_end_time", TRUE),
            "start_money"          => $this->input->get("united_start_money", TRUE),
            "end_money"            => $this->input->get("united_end_money", TRUE),
            "status"               => $this->input->get("united_status"),
            "channel" 	           => $this->input->get("united_channel", true),
            "guarantee"            => $this->input->get("united_guarantee", true),
            "webGurantee"          => $this->input->get("webGurantee", true),
            'uid'                  => $this->input->get("uid", true),
            'orderType'            => $this->input->get("orderType", true),
            'my_status'            => $this->input->get("united_my_stauts", true),
            'playType'             => $this->input->get("united_playType", true),
            'fromType'             => $this->input->get("fromType", TRUE),
            "reg_type"             => $this->input->get("reg_type", true),
        );
        $fromType = $this->input->get("fromType", TRUE);
        if (empty($searchData['start_time']) && empty($searchData['end_time']) && $fromType != 'ajax')
        {
            $searchData['end_time'] = date("Y-m-d 23:59:59");
            $searchData['start_time'] = date("Y-m-d 00:00:00");
        }
        else
        {
            $this->filterTime($searchData['start_time'], $searchData['end_time']);
        }
        //查询条件需要的参数
        $lottery = array('51' => '双色球', '23529' => '大乐透', '42' => '竞彩足球', '43' => '竞彩篮球', '11' => '胜负', '19' => '任九', '52' => '福彩3D', '23528' => '七乐彩', '10022' => '七星彩', '33' => '排列三', '35' => '排列五');
        $platforms = array(
            '-1' => '不限', 
            '0' => '网页',
            '1' => 'Android',
            '2' => 'IOS',
            '3' => 'M版'
        );
        $proportion= array(
            '-1'  => '不限',
            '0'  => '<=0%',
            '1'  => '<=1%',
            '2'  => '<=2%',
            '3'  => '<=3%',
            '4'  => '<=4%',
            '5'  => '<=5%',
            '6'  => '<=6%',
            '7'  => '<=7%',
            '8'  => '<=8%',
            '9'  => '<=9%',
            '10' => '<=10%'
        );
        $this->load->model('model_channel');
        $channelRes = $this->model_channel->getChannels();
        $status = array(
            '-1'   => '全部',
            '0'    => '待付款',
            '20'   => '逾期未支付',
            '40'  => '等待出票',
            '240'  => '出票中',
            '500'  => '等待开奖',
            '600'  => '方案撤单',
            '2000' => '已中奖',
            '1000' => '未中奖',
            '620'  => '未满员撤单',
            '610'  => '发起人撤单',
            '999'  => '已出票',
            '998'  =>'等待满员',
            '997'  =>'已满员'
        );
        //数据汇总的数据
        $this->load->model('model_united_order', 'unitedOrder');
        //订单列表
        $result = $this->unitedOrder->list_orders($searchData, $page, self::NUM_PER_PAGE);
        $this->load->model('model_order', 'order');
        //分页
        $pageConfig = array(
            "page"     => $page,
            "npp"      => self::NUM_PER_PAGE,
            "allCount" => $result['count']['num']
        );
        $pages = get_pagination($pageConfig);
        $allChannel = array();
        foreach ($channelRes as $channel)
        {
            $allChannel[$channel['id']] = $channel['name'];
        }
        $pageInfo = array(
            'lottery'    => $lottery,
            'platforms'  => $platforms,
            'channels'   => $allChannel,
            'proportion' => $proportion,
            'status'     => $status,
            'searchTime' => array(
                'end_time'   => date("Y-m-d 23:59:59", time()),
                'start_time' => date("Y-m-d 00:00:00", time())
            ),
            'orders'     => $result['data'],
            'pages'      => $pages,
            'count'      => $result['count'],
            'search'     => $searchData,
            'fromType'   => $fromType
        );
        $this->load->view('management/unitedOrders', $pageInfo);
    }
    
    /**
     * 更新置顶状态
     */
    public function updateTop(){
        $this->check_capacity('3_11_5', true);
        $orderId = $this->input->post('id');
        $top= $this->input->post('top');
        $judge= $this->input->get('judge');
        if ($orderId)
        {
            $this->load->model('model_united_order', 'unitedOrder');
            $result = $this->unitedOrder->updateTop($orderId, $top, $judge);
            if ($result['status'] == "success" && $judge != 1)
            {
                if ($top == 1)
                {
                    $this->syslog(15, "设置合买方案置顶操作：{$orderId}（合买订单编号）");
                }
                else
                {
                    $this->syslog(15, "取消合买方案置顶操作：{$orderId}（合买订单编号）");
                }
            }
            die(json_encode($result));
        } else 
        {
            die(json_encode(array('status' => 'fail', 'message' => '请先选择合买方案')));
        }
    }
    
    /**
     * 取消订单
     */
    public function cancelOrder()
    {
        $this->check_capacity('3_11_6', true);
        $orderId = $this->input->post('id',true);
        if ($orderId)
        {
            $this->load->model('model_united_order', 'unitedOrder');
            $result = $this->unitedOrder->cancelOrder($orderId);
            if ($result['status'] == "success")
            {
                $this->syslog(15, "合买订单撤单操作，单号：{$orderId}（合买订单编号）");
            }
            die(json_encode($result));
        } else 
        {
            die(json_encode(array('status' => 'fail', 'message' => '请先选择合买方案')));
        }
    }
    
    /**
     * 合买红人管理
     */
    public function managePlanner()
    {
        $this->check_capacity('3_11_3');
        $lid = $this->input->get('lid', true);
        $lid or $lid = 0;
        $page = $this->input->get('p', true) ? $this->input->get('p', true) : 1;
        $username = $this->input->get('username', true);
        $lottery = array('51' => '双色球', '23529' => '大乐透', '42' => '竞彩足球', '43' => '竞彩篮球', '11' => '胜负/任九', '52' => '福彩3D', '23528' => '七乐彩', '10022' => '七星彩', '33' => '排列三/五');
        $this->load->model('model_united_planner', 'unitedPlanner');
        $offset = 50;
        if ($lid != 0)
        {
            $offset = 30;
        }
        $res = $this->unitedPlanner->getAllPlanner($lid, $username, ($page-1)*$offset, $offset);
        $planners = $res[0];
        $pageConfig = array(
            "page" => $page,
            "npp" => 50,
            "allCount" => $res[1]
       	);
        $pages = get_pagination($pageConfig);
        $this->load->view('management/unitedPlanner', compact('lottery', 'planners', 'lid', 'username', 'pages'));
    }

    /**
     * 合买红人状态修改
     */
    public function updatePlannerTop()
    {
        $this->check_capacity('3_11_7', true);
        $id = $this->input->post('id');
        $lid = $this->input->post('lid');
        $hot = $this->input->post('hot');
        $judge = $this->input->get('judge');
        // $uid = $this->input->post('uid');
        $this->load->model('model_united_planner', 'unitedPlanner');
        $res = $this->unitedPlanner->updatePlannerTop($id, $lid, $hot, $judge);
        if ($res['status'] == "success" && $judge != 1)
        {
            $this->load->model('model_user');
            $this->model_user->freshUserHot($uid, $hot, $lid);
            if ($hot == 1)
            {
                $this->syslog(15, "设置合买红人操作：{$res['message']}（用户名）");
            }
            else
            {
                $this->syslog(15, "取消合买红人操作：{$res['message']}（用户名）");
            }
        }
        die(json_encode($res));
    }
    
    /**
     * 计算等级
     * @param int $points
     * @return string
     */
    private function calGrade($points)
    {
        $grade = "";
        $huangguan = floor($points / 1000);
        if ($huangguan > 0)
        {
            $grade.=$huangguan . '皇冠';
        }
        $taiyang = floor(($points - $huangguan * 1000) / 100);
        if ($taiyang > 0)
        {
            $grade.=$taiyang . '太阳';
        }
        $yueliang = floor(($points - $huangguan * 1000 - $taiyang * 100) / 10);
        if ($yueliang > 0)
        {
            $grade.=$yueliang . '月亮';
        }
        $xingxing = $points - $huangguan * 1000 - $taiyang * 100 - $yueliang * 10;
        if ($xingxing > 0)
        {
            $grade.=$xingxing . '星星';
        }
        return $grade;
    }
    /**
     * 合买订单详情
     */
    public function unitedOrderDetail()
    {
        $this->check_capacity("3_11_2");
        $this->load->model('model_order', 'order');
        $id = $this->input->get("id", true);
        $order = $this->order->findOrderByOrderId($id);
        $searchData = array(
            'username' => $this->input->post("username", true),
            'subOrderType' => $this->input->post("subOrderType", true),
        );
        $unitOrders = array();
        $lsDetail = array();
        if (!empty($order)) {
            $this->load->model('model_order_inconsistent', 'inconsistent');
            $order['isConsistent'] = $this->inconsistent->isConsistent($order['orderId']);
            $lotteryMap = $this->config->item('cfg_lidmap');
            $tableTypeTransform = array(
                'pls' => 'pl3',
                'plw' => 'pl5',
                'fcsd' => 'fc3d',
            );
            $type = $lotteryMap[$order['lid']];
            if (array_key_exists($type, $tableTypeTransform)) {
                $type = $tableTypeTransform[$type];
            }
            $this->load->library('issue');
            $pIssue = $this->issue->getPIssueBySIssue($type, $order['issue']);
            $this->load->model('model_issue_cfg', 'issueModel');
//             $order['awardNum'] = $this->issueModel->getAwardNum($type, $pIssue);
            $this->load->model('model_ordersplit', 'orderSplit');
            $order['consistencyInfo'] = $this->orderSplit->consistencyInfo($order['lid'], $order['orderId']);
            $order['messageId'] = $this->orderSplit->getMessageId($order['orderId'], $order['lid']);
            if ($order['status'] >= 200) {
                if (in_array($order['lid'], array('42', '43'))) {
                    $subOrders = $this->getJjcSplit($order['orderId']);
                } elseif (in_array($order['lid'], array('44', '45'))) {
                    $subOrders = $this->getGjcSplit($order['orderId']);
                } else {
                    $subOrders = $this->orderSplit->getSplitDetailByOrder($order['orderId'], $order['lid']);
                }
                $this->load->library('split');
                foreach ($subOrders as & $subOrder) 
                {
                    //对慢频彩种 拉取对比奖金核对显示处理：
                    if( in_array( $order['lid'], array('51','23529','23528','10022','33','35','52') ) )
                    {
                        if($subOrder['cpstate']!='4')
                        {
                            $order['consistencyInfo'] = '未比对';
                        }else{
                            if( ($subOrder['bonus']!=$subOrder['ticketBonus'] ) || ($subOrder['ticketBonus']!=$subOrder['ticketMargin']))
                            {
                                $order['consistencyInfo'] = '不一致';
                            }else{
                                $order['consistencyInfo'] = '一致';
                            }
                        }
                    }
                    $subOrder['stakeNum'] = $this->split->computeStakeNum($subOrder);
                }
            } else {
                $subOrders = array();
            }
            // 加奖
            if (($order['activity_ids'] & 4) == 4) {
                $order['add_money'] = 0;
                $this->load->model('Model_activity');
                $jjDdetail = $this->Model_activity->getJjMoney($order['orderId']);
                if (!empty($jjDdetail)) {
                    $order['add_money'] = $jjDdetail['add_money'];
                }
            }
            // 玩法
            $this->load->library('split');
            $order['playTypeName'] = $this->split->playTypeName($order);
            // 快乐扑克投注内容处理
            $order['codes'] = $this->getCodesFormat($order);
            $this->load->model('model_united_order', 'unitedOrder');
            $unitOrder = $this->unitedOrder->findByOrderId($id);
            $order['webguranteeAmount'] = $unitOrder['webguranteeAmount'];
            $order['webguranteeAmountScale'] = (round($unitOrder['webguranteeAmount'] / $unitOrder['money'], 2) * 100);
            $order['buyTotalMoneyScale'] = (round($unitOrder['buyTotalMoney'] / $unitOrder['money'], 2) * 100);
            $order['guaranteeAmountScale'] = (round($unitOrder['guaranteeAmount'] / $unitOrder['money'], 2) * 100);
            $order['guaranteeAmount'] = $unitOrder['guaranteeAmount'];
            $order['openStatus'] = $unitOrder['openStatus'];
            $order['commissionRate'] = $unitOrder['commissionRate'];
            $order['commission'] = $unitOrder['commission'];
            $order['popularity'] = $unitOrder['popularity'];
            $order['buyTotalMoney'] = $unitOrder['buyTotalMoney'];
            $order['status'] = $unitOrder['status'];
            $order['bonus'] = $unitOrder['orderBonus'];
            $order['margin'] = $unitOrder['orderMargin'];
            $order['money'] = $unitOrder['money'];
            $order['points'] = $this->calGrade($unitOrder['united_points']);
            $order['actualguranteeAmount'] = $unitOrder['actualguranteeAmount'];
            $order['cstate'] = $unitOrder['cstate'];
            $order['is_hide'] = $unitOrder['is_hide'];
            $order['introduction'] = $unitOrder['introduction'] ? $unitOrder['introduction'] : '';
            $page = intval($this->input->get("p"));
            $page = $page <= 1 ? 1 : $page;
            $this->load->model('model_united_join');
            $unitData = $this->model_united_join->getAllOrders($id, $searchData, $page, 10);
            $unitOrders = $unitData['data'];
            $pageConfig = array(
                "page" => $page,
                "npp" => 10,
                "allCount" => $unitData['count']
            );
            $pages = get_pagination($pageConfig);
            // 大乐透乐善码
            if($order['lid'] == 23529 && $order['isChase'] == 1)
            {
                $lsDetail = $this->getLsDetail($id, $order['lid']);
            }
        } else {
            $subOrders = array();
        }

        // 参与方式
        $subOrderTypes = array(
            '0' =>  '自己参与',
            '1' =>  '定制跟单',
        );
        $this->load->view("management/unitedOrderDetail", compact('order', 'subOrders', 'unitOrders', 'searchData', 'pages', 'subOrderTypes', 'lsDetail'));
    }
    
    public function sendJoinedEmail() {
        $suborderId = $this->input->post('suborderId', true);
        $this->load->model('model_united_join');
        if($this->model_united_join->sendEmail($suborderId)) {
            $this->syslog(2, "对合买子订单id：{$suborderId}进行补发邮件操作" );
            return $this->ajaxReturn('y', $this->msg_text_cfg['success']);
        }
        return $this->ajaxReturn('n', '操作失败，邮件不存在或未送达');
    }
    
    public function payconfig($platform = 1) {
        if($platform == '2' || $platform == '5')
        {
            $this->check_capacity("3_12_2");
        }
        elseif ($platform == '3' || $platform == '6')
        {
            $this->check_capacity("3_12_3");
        }
        elseif ($platform == '4')
        {
            $this->check_capacity("3_12_4");
        }
        else
        {
            $this->check_capacity("3_12_1");
        }
    	
    	$data['search'] = $this->input->get();
    	$this->load->model('model_pay_config', 'model');
    	if (empty($data['search']['start_time']) && empty($data['search']['end_time'])) {
    		$data['search']['end_time'] = date("Y-m-d 23:59:59");
    		$data['search']['start_time'] = date("Y-m-d 00:00:00");
    	}
    	$data['platform'] = $platform;
    	$data['platFormArr'] = array(1 => '网页端', 2 => '安卓', 3 => 'IOS', 4 => 'M版', 5 => '安卓马甲', 6 => 'IOS马甲');
    	$data['ctypeArr'] = array(1 => '快捷支付', 2 => '微信支付', 3 => '微信扫码', 4 => '支付宝', 7=>'银联云闪付', 8=>'京东支付');
    	$data['paytypeArr'] = array(1 => '易宝wap', 2 => '易宝快捷', 3 => '连连快捷 ', 4 => '连连SDK', 5 => '中信微信', 6 => '统统付wap', 7 => '统统付快捷', 
    			8 => '中信SDK', 9 => '全付通扫码', 10 => '全付通扫码/SDK', 11 => '全付通扫码/SDK', 12 => '现在支付宝h5', 13 => '京东支付', 14 => '卡前置支付', 15 => '汇聚无限支付宝h5',16=>'兴业支付宝H5',17=>'微众银行支付宝',18=>'微信H5-兴业银行',
                        19=>'支付宝H5-鸿粤浦发银行',21=>'厦门银行支付宝',22=>'微信H5-鸿粤兴业银行',23=>'微信H5-浦发白名单渠道','24'=>'平安银行支付宝', '25'=>'银联云闪付','28' => '盈中平安银行支付宝','29' => '番茄支付支付宝h5','30' => '银联扫码','31' => '微信H5-上海银行','32' => '京东SDK','33' => '盈中平安银行微信h5','34' => '支付宝H5-上海银行','35' => '微信扫码-长沙中信银行渠道', '36' => '支付宝扫码-长沙中信银行渠道', '37' => '番茄支付微信h5');
    	$res = $this->model->getListByPlatform($platform);
    	$data['data'] = $res['data'];
    	$data['money'] = $this->model->getMoney($data['search']['start_time'], $data['search']['end_time'], $res['idstr']);
        $data['freshpayconfig'] = $this->model->getFreshPayConfig();
        if($platform == 1){
            $data['pcweight'] = $this->model->getPcWeight();
        }
    	$this->load->view("payconfig", $data);
    }
    
    public function payconfigMark()
    {
    	$env = $this->input->post('env');
    	$this->checkenv($env, true);
        $data = $this->input->post();
        if (isset($data['data'])) 
        {
            $this->load->model('model_pay_config', 'model');
            $res = $this->model->updateMark($data['data']);
            $platFormArr = array(1 => '网页端', 2 => '安卓', 3 => 'IOS', 4 => 'M版', 5 => '安卓马甲', 6 => 'IOS马甲');
            $ctypeArr = array(1 => '快捷支付', 2 => '微信支付', 3 => '微信扫码', 4 => '支付宝', 7=>'银联云闪付', 8=>'京东支付');
    	    $paytypeArr = array(1 => '易宝wap', 2 => '易宝快捷', 3 => '连连快捷 ', 4 => '连连SDK', 5 => '中信微信', 6 => '统统付wap', 7 => '统统付快捷', 
    			8 => '中信SDK', 9 => '全付通扫码', 10 => '全付通扫码/SDK', 11 => '全付通扫码/SDK', 12 => '现在支付宝h5', 13 => '京东支付', 14 => '卡前置支付', 15 => '汇聚无限支付宝h5',16=>'兴业支付宝H5',17=>'微众银行支付宝',18=>'微信H5-兴业银行',
                        19=>'支付宝H5-鸿粤浦发银行',21=>'厦门银行支付宝',22=>'微信H5-鸿粤兴业银行','24'=>'平安银行支付宝', '25'=>'银联云闪付',23=>'微信H5-浦发白名单渠道','28' => '盈中平安银行支付宝','29' => '番茄支付支付宝h5','30' => '兴业银行银联扫码','31' => '微信H5-上海银行','32' => '京东SDK','33' => '盈中平安银行微信h5','34' => '支付宝H5-上海银行','35' => '微信扫码-长沙中信银行渠道','36' => '支付宝扫码-长沙中信银行渠道', '37' => '番茄支付微信h5');
            $logArr = array();
            $payName = array(18 => '兴业银行', 19 => '鸿粤浦发银行');
            foreach ($res as $val) {
                    $name = in_array($val['pay_type'],array_keys($payName))?$payName[$val['pay_type']]."--".$val['mer_id']:(($val['ctype']==4?($val['pay_type']==17?'微众银行':'').'支付宝':(in_array($val['pay_type'],array(5,8))?'中信':'全付通').($val['ctype']==2?'SDK':'扫码'))."—".$val['mer_id']);
                    array_push($logArr, $platFormArr[$val['platform']]."平台".$ctypeArr[$val['ctype']].$name."修改备注：".$val['status_mark']);
            }
            $this->syslog(48, implode(',', $logArr));
        }
        exit(json_encode(array('code' => '200', 'msg' => '更新成功')));
    }
    public function payconfigmodify()
    {
    	$env = $this->input->post('env');
    	$this->checkenv($env, true);
        $data = $this->input->post();
        if($data['platform'] == '2' || $data['platform'] == '5')
        {
            $this->check_capacity("3_12_6", true);
        }
        elseif($data['platform'] == '3' || $data['platform'] == '6')
        {
            $this->check_capacity("3_12_7", true);
        }
        elseif($data['platform'] == '4')
        {
            $this->check_capacity("3_12_8", true);
        }
        else
        {
            $this->check_capacity("3_12_5", true);
        }
    	
    	$this->load->model('model_pay_config', 'model');
    	$this->model->updateRate($data['platform'], $data['ctype'], $data['rate']);
    	$platFormArr = array(1 => '网页端', 2 => '安卓', 3 => 'IOS', 4 => 'M版', 5 => '安卓马甲', 6 => 'IOS马甲');
    	$ctypeArr = array(1 => '快捷支付', 2 => '微信支付', 3 => '微信扫码', 4 => '支付宝', 7=>'银联云闪付', 8=>'京东支付');
    	$this->syslog(48, "调整".$platFormArr[$data['platform']]."平台".$ctypeArr[$data['ctype']]."支付比例");
    	exit(json_encode(array('code' => '200', 'msg' => '更新成功')));
    }
    
    public function payconfigweight()
    {
    	$env = $this->input->post('env');
    	$this->checkenv($env, true);
    	$data = $this->input->post();
    	if($data['platform'] == '2' || $data['platform'] == '5')
    	{
    	    $this->check_capacity("3_12_6", true);
    	}
    	elseif($data['platform'] == '3' || $data['platform'] == '6')
    	{
    	    $this->check_capacity("3_12_7", true);
    	}
    	elseif($data['platform'] == '4')
    	{
    	    $this->check_capacity("3_12_8", true);
    	}
    	else
    	{
    	    $this->check_capacity("3_12_5", true);
    	}
    	$ctypeArr = array(1 => '快捷支付', 2 => '微信支付', 3 => '微信扫码', 4 => '支付宝', 7=>'银联云闪付', 8=>'京东支付');
    	$paytypeArr = array(1 => '易宝wap', 2 => '易宝快捷', 3 => '连连快捷 ', 4 => '连连SDK', 5 => '中信微信', 6 => '统统付wap', 7 => '统统付快捷',
    			8 => '微信SDK', 9 => '威富通支付宝', 10 => '威富通微信sdk', 11 => '威富通微信PC', 12 => '现在支付宝h5', 13 => '京东支付', 14 => '卡前置支付', '25'=>'银联云闪付','28' => '盈中平安银行支付宝','29' => '番茄支付支付宝h5','30' => '兴业银行银联扫码','31' => '微信H5-上海银行','32' => '京东SDK','33' => '盈中平安银行微信h5','34' => '支付宝H5-上海银行','35' => '微信扫码-长沙中信银行渠道','36' => '支付宝扫码-长沙中信银行渠道', '37' => '番茄支付微信h5');
    	$platFormArr = array(1 => '网页端', 2 => '安卓', 3 => 'IOS', 4 => 'M版', 5 => '安卓马甲', 6 => 'IOS马甲');
    	$this->load->model('model_pay_config', 'model');
    	$cname0 = '';
    	$cname1 = '';
    	if ($data['kuaijie']) {
    		$paytype1 = array();
    		$paytype0 = array();
    		foreach ($data['kuaijie'] as $pt => $status) {
    			if ($status == 1){
    				array_push($paytype1, $pt);
    				if ($cname1 !== '') $cname1 .= '、';
    				$cname1 .= $paytypeArr[$pt];
    			}else {
    				array_push($paytype0, $pt);
    				if ($cname0 !== '') $cname0 .= '、';
    				$cname0 .= $paytypeArr[$pt];
    			}
    		}
    		if ($paytype0) $this->model->updateStatus($data['platform'], array('ctype' => 1, 'pay_type' => implode(',', $paytype0)), 0);
    		if ($paytype1) $this->model->updateStatus($data['platform'], array('ctype' => 1, 'pay_type' => implode(',', $paytype1)), 1);
    	}
    	if ($data['yinlian']) {
    		$paytype1 = array();
    		$paytype0 = array();
    		foreach ($data['yinlian'] as $pt => $status) {
    			if ($status == 1){
    				array_push($paytype1, $pt);
    				if ($cname1 !== '') $cname1 .= '、';
    				$cname1 .= $paytypeArr[$pt];
    			}else {
    				array_push($paytype0, $pt);
    				if ($cname0 !== '') $cname0 .= '、';
    				$cname0 .= $paytypeArr[$pt];
    			}
    		}
    		if ($paytype0) $this->model->updateStatus($data['platform'], array('ctype' => 7, 'pay_type' => implode(',', $paytype0)), 0);
    		if ($paytype1) $this->model->updateStatus($data['platform'], array('ctype' => 7, 'pay_type' => implode(',', $paytype1)), 1);
    	}
        if ($data['jd']) {
    		$paytype1 = array();
    		$paytype0 = array();
    		foreach ($data['jd'] as $pt => $status) {
    			if ($status == 1){
    				array_push($paytype1, $pt);
    				if ($cname1 !== '') $cname1 .= '、';
    				$cname1 .= $paytypeArr[$pt];
    			}else {
    				array_push($paytype0, $pt);
    				if ($cname0 !== '') $cname0 .= '、';
    				$cname0 .= $paytypeArr[$pt];
    			}
    		}
    		if ($paytype0) $this->model->updateStatus($data['platform'], array('ctype' => 8, 'pay_type' => implode(',', $paytype0)), 0);
    		if ($paytype1) $this->model->updateStatus($data['platform'], array('ctype' => 8, 'pay_type' => implode(',', $paytype1)), 1);
    	}
    	if ($data['other']) {
    		$ctype1 = array();
    		$ctype0 = array();
    		foreach ($data['other'] as $ct => $status) {
    			if ($status == 1){
    				array_push($ctype1, $ct);
    				if ($cname1 !== '') $cname1 .= '、';
    				$cname1 .= $ctypeArr[$ct];
    			}else {
    				array_push($ctype0, $ct);
    				if ($cname0 !== '') $cname0 .= '、';
    				$cname0 .= $ctypeArr[$ct];
    			}
    		}
    		if ($ctype0) $this->model->updateStatus($data['platform'], array('ctype' => implode(',', $ctype0)), 0);
    		if ($ctype1) $this->model->updateStatus($data['platform'], array('ctype' => implode(',', $ctype1)), 1);
    	}
    	$kaiguanstr = $platFormArr[$data['platform']]."平台";
    	if ($cname0) $kaiguanstr.= $cname0."渠道状态调整为正常开启；";
    	if ($cname1) $kaiguanstr.= $cname0."渠道状态调整为维护关闭";
    	$this->syslog(48, $kaiguanstr);
    	$str = '调整'.$platFormArr[$data['platform']]."平台";
    	if ($data['weight']) {
    		$this->model->updateWeight($data['platform'], $data['ctype'], $data['weight']);
                $alltypes = array('weixin'=>'微信支付','weixinsaoma'=>'微信扫码','zhifubao'=>'支付宝','1_1'=>'快捷支付','1_3'=>'微信支付','1_4'=>'支付宝支付','1_5'=>'网上银行','1_7'=>'银联云闪付','1_8'=>'京东支付');
	    	$alltypes = array_merge($alltypes,$paytypeArr);
                foreach ($data['weight'] as $weight => $val) {
	    		$str .= $alltypes[$weight]."、";
	    	}
	    	$str = mb_substr($str, 0, -1, 'utf8')."排序优先级";
	    	$this->syslog(48, $str);
    	}
        if ($data['guide']) {
            $s = '';
            $this->model->updateGuide($data['platform'], $data['guide']);
            foreach ($data['guide'] as $k => $v) {
                $ctype = explode('_', $k);
                $s .= '修改了' . $ctypeArr[$ctype[1]] . '的引导文案为' . $v . '；';
            }
            $this->syslog(48, $s);
        }
        if(in_array($_SERVER['SERVER_ADDR'],array('120.132.33.194','123.59.105.39'))){
            $this->refreshPayCache($data['platform'],194);
        }else{
            $this->refreshPayCache($data['platform']);
        }
    	exit(json_encode(array('code' => '200', 'msg' => '更新成功')));
    }
    
    private function refreshPayCache($platform,$environment = 0) {

        if($platform==1 || $platform==4 )
        {
            $this->load->model('dispatch_model');
            $this->load->driver('cache', array('adapter' => 'redis'));
            $channels = $this->dispatch_model->RcgChannelDispatch();
            $REDIS = $this->config->item('REDIS');
            if($environment == 194){
                $oldchannels = $this->cache->hGetAll($REDIS['CS_RCG_DISPATCH']);
            }else{
                $oldchannels = $this->cache->hGetAll($REDIS['RCG_DISPATCH']);
            }
            $diffchannels= array_diff(array_keys($oldchannels), array_keys($channels));
            if(!empty($diffchannels))
            {
            foreach ($diffchannels as $key)
            {
                if($environment == 194){
                    $this->cache->hDel($REDIS['CS_RCG_DISPATCH'], $key);
                }else{
                    $this->cache->hDel($REDIS['RCG_DISPATCH'], $key);
                }
            }
            }
            if($environment == 194){
               $this->cache->hMSet($REDIS['CS_RCG_DISPATCH'], $channels);  
            }else{
               $this->cache->hMSet($REDIS['RCG_DISPATCH'], $channels); 
            }
            if($platform == 1){
                $this->load->model('model_pay_config', 'model');
                $res = $this->model->getPcWeightSort();
                $pcweights = $res[0];
                $config = $this->model->getFreshPayConfig();
                $this->cache->hSet($REDIS['CS_PC_PAY_CONFIG'], $platform, json_encode($pcweights));
                $this->cache->hSet($REDIS['PC_PAY_GUIDE_CONFIG'], $platform, json_encode($res[1]));
                if ($config['fresh_payconfig'] == 0) {
                    $this->cache->hSet($REDIS['PC_PAY_CONFIG'], $platform, json_encode($pcweights));
                }
            }
        }else{
            $this->load->model('model_pay_config', 'model');
            $data = $this->model->getCtypeByPlatform($platform);
            $config = $this->model->getFreshPayConfig();
            $this->load->driver('cache', array('adapter' => 'redis'));
            $REDIS = $this->config->item('REDIS');
            $this->cache->hSet($REDIS['CS_PAY_CONFIG'], $platform, json_encode($data));  
            if($config['fresh_payconfig'] == 0){
                $this->cache->hSet($REDIS['PAY_CONFIG'], $platform, json_encode($data));  
            }  
        } 
        // 更新指定平台的支付宝微信比例缓存
        $this->refreshRateConfig($platform);
    }
    
    public function manageIntroduce()
    {
        $this->check_capacity("6_10_1");
        $number = $this->input->get('number', true);
        $words = $this->input->get('words', true);
        $chinesenumer = $this->input->get('chinesenumer', true);
        $check_status = $this->input->get('check_status', true);
        $name = $this->input->get('name', true);
        $start_time = $this->input->get('start_time', true);
        if (!$start_time) {
            $start_time = date("Y-m-d 00:00:00");
        }
        $end_time = $this->input->get('end_time', true);
        if (!$end_time) {
            $end_time = date("Y-m-d 23:59:59");
        }
        $page = intval($this->input->get("p"));
        $page = $page <= 1 ? 1 : $page;
        $this->load->model('model_user');
        $all = $this->model_user->getHasIntroduce(array('number' => $number, 'words' => $words, 'chinesenumer' => $chinesenumer, 'check_status' => $check_status, 'name' => $name, 'start_time'=>$start_time, 'end_time'=>$end_time), $page, 20);
        $users = $all['users'];
        $pageConfig = array(
            "page" => $page,
            "npp" => 20,
            "allCount" => $all['count']
        );
        $pages = get_pagination($pageConfig);
        $this->load->view("management/manageIntroduce", compact('number', 'words', 'chinesenumer', 'check_status', 'name', 'pages', 'users', 'start_time','end_time'));
    }
    
    public function emptyIntroduce()
    {
        $this->check_capacity('6_10_2',true);
        $uid = $this->input->post('uid');
        $name = $this->input->post('name');
        $this->load->model('model_user');
        $tag = $this->model_user->emptyIntroduce($uid);
        $this->syslog(77,"清空{$name}个人简介");
        if($tag)
        {
            $this->ajaxReturn('SUCCESSS', "清空{$name}个人简介成功~");
        }else{
            $this->ajaxReturn('ERROR', "清空{$name}个人简介失败~");
        }
    }
    /**
     * [handSucc 手动审核成功]
     * @author LiKangJian 2017-08-24
     * @return [type] [description]
     */
    public function handSucc()
    {
        $this->check_capacity('6_10_2', true);
        $name = $this->input->post('name');
        $this->load->model('model_user');
        $tag = $this->model_user->handSucc( $this->input->post('uid') );
        $this->syslog(77,"手动成功{$name}个人简介");
        if($tag)
        {
            $this->ajaxReturn('SUCCESSS', "手动成功{$name}个人简介成功~");
        }else{
            $this->ajaxReturn('ERROR', "手动成功{$name}个人简介失败~");
        }
    }
    /**
     * 票张数限制
     */
    public function ticketLimit()
    {
    	$this->check_capacity("3_5_4");
    	$this->load->library('BetCnName');
    	$this->load->model('model_lottery_config', 'lotteryConfig');
    	$configItem = $this->lotteryConfig->fetchConfigItems(1, 100);
    	$gaopin = array('21406', '21407', '53', '21408', '54', '55', '56', '57', '21421');
    	$data = array(
    		'mp' => array(),
    		'gp' => array(),
    	);
    	foreach ($configItem[0] as $value)
    	{
    		$orderLimit = json_decode($value['order_limit'], true);
    		$vData = array();
    		$vData['id'] = $value['lotteryId'];
    		$vData['name'] = BetCnName::getCnName($value['lotteryId']);
    		$vData['time1'] = $orderLimit[0]['value'];
    		$vData['time2'] = $orderLimit[1]['value'];
    		$vData['time3'] = $orderLimit[2]['value'];
    		if(in_array($value['lotteryId'], $gaopin))
    		{
    			$data['gp'][] = $vData;
    		}
    		else 
    		{
    			$data['mp'][] = $vData;
    		}
    	}
    	$this->load->view("management/ticketLimit", $data);
    }
    
    /**
     * 更新票张数限制
     */
    public function updateTicket()
    {
    	$this->check_capacity("3_5_7", true);
    	$lotteryId = $this->input->post('id', true);
    	$time1 = $this->input->post('time1', true);
    	$time2 = $this->input->post('time2', true);
    	$time3 = $this->input->post('time3', true);
        $time1 = empty($time1) ? 0 :$time1;
        $time2 = empty($time2) ? 0 :$time2;
        $time3 = empty($time3) ? 0 :$time3;
    	$this->load->model('model_lottery_config', 'lotteryConfig');
    	$lotteryInfo = $this->lotteryConfig->getConfigItems($lotteryId);
    	if($lotteryInfo[0])
    	{
    		$orderLimit = json_decode($lotteryInfo[0]['order_limit'], true);
    		$orderLimit[0]['value'] = $time1;
    		$orderLimit[1]['value'] = $time2;
    		$orderLimit[2]['value'] = $time3;
    		$res = $this->lotteryConfig->setConfigItems($lotteryId, array('order_limit' => json_encode($orderLimit)), false);
    		if($res)
    		{
    			$this->load->library('BetCnName');
                $bt = new BetCnName();
    			$this->syslog(54, "彩种(". $bt->getCnName($lotteryId).")出票限制调整");
    			return $this->ajaxReturn('y', $this->msg_text_cfg['success']);
    		}
    	}
    	
    	return $this->ajaxReturn('n', "操作失败");
    }

    public function payTypeConfig()
    {
       $params = $this->input->post();
       $this->load->model('model_pay_config', 'model');
       $method = 'payType'.$params['pay_type'] ;
       $this->load->library('CreatePayConfig');
       $lib = new CreatePayConfig();
       $data = $lib->$method($params['platform'],1);
       echo json_encode($data);
    }
    /**
     * [storePayConfig 增加商户号]
     * @author LiKangJian 2017-12-20
     * @return [type] [description]
     */
    public function storePayConfig()
    {
        $this->check_capacity("3_12_10", true);
        $params = $this->input->post();
        $method = 'payType'.$params['pay_type'];
        $this->load->library('CreatePayConfig');
        $lib = new CreatePayConfig();
        $res = $lib->$method($params['platform'],2,$params);
        $txt = $params['id'] ? '修改':'新增';
        //查询是否重复
        if(!$params['id'])
        {

        }
        if($res['tag'])
        {
            //修改日志
            if($params['id'])
            {
                $this->syslog(48,"修改".$res['para']['platform'].$res['para']['pay_type'].$res['para']['ctype']."（".$res['para']['mer_id']."）");
            }else{
                $this->syslog(48,"新增".$res['para']['platform'].$res['para']['pay_type'].$res['para']['ctype']."（".$res['para']['mer_id']."）");
            }
            echo json_encode(array('status'=>'SUCCESS','message'=>$txt.'商户号成功~'));die;
        }else{
            $msg = $res['msg'] ? $res['msg'] : $txt.'商户号失败~';
            echo json_encode(array('status'=>'ERROR','message'=>$msg));die;
        }
    }
    /**
     * [delPayConfig 删除商户号]
     * @author LiKangJian 2017-12-20
     * @param  [type] $id [description]
     * @return [type]     [description]
     */
    public function delPayConfig()
    {
        $this->load->model('model_pay_config', 'model');
        $tag = $this->model->delPayConfig($this->input->post('id'));
        if($tag)
        {
            echo json_encode(array('status'=>'SUCCESS','message'=>'商户号删除成功~'));die;
        }else{
            echo json_encode(array('status'=>'ERROR','message'=>'商户号删除失败~'));die;
        }
    }
    /**
     * [payAddList 新增柯删除商户号列表]
     * @author LiKangJian 2017-12-20
     * @return [type] [description]
     */
    public function payAddList()
    {
        $this->check_capacity("3_12_10");
        $page = intval($this->input->get("p"));
        $page = $page <= 1 ? 1 : $page;
        $searchData = array(
            "platform" => $this->input->get("platform1", TRUE),
            "pay_type" => $this->input->get("pay_type1", TRUE)
        );
        $this->load->model('model_pay_config', 'model');
        $result = $this->model->getPayAddList($searchData, $page, self::NUM_PER_PAGE, TRUE);
        $pageConfig = array(
                "page"     => $page,
                "npp"      => self::NUM_PER_PAGE,
                "allCount" => $result[1]
        );
        $pages = get_pagination($pageConfig);

        //配置信息
        $payConfigs = $this->model->getPayConfig();
        $ctyName = array(2=>'微信支付',3=>'微信扫码',4=>'支付宝');
        $platform = array(1=>'网站',2=>'Android',3=>'IOS',4=>'M版',5 => '安卓马甲', 6 => 'IOS马甲');
        //3=>'连连快捷',4=>'连连SDK',
        $payTypes =  array(5=>'中信微信',
        9=>'威富通支付宝',10=>'威富通微信sdk',11=>'威富通微信PC',12=>'现在支付宝h5',15=>'汇聚无限支付宝h5',16=>'兴业支付宝H5',17=>'微众银行支付宝',
        18=>'微信H5-兴业银行',19=>'鸿粤浦发银行',20=>'众邦银行支付宝',21=>'厦门国际银行支付宝',22=>'微信H5-鸿粤兴业银行',24=>'平安银行支付宝',23=>'微信H5-浦发白名单渠道',28 => '盈中平安银行支付宝','29' => '番茄支付支付宝h5', '31' => '上海银行微信h5','33' => '盈中平安银行微信h5','35' => '微信扫码-长沙中信银行渠道','36' => '支付宝扫码-长沙中信银行渠道', '37' => '番茄支付微信h5');
        $cty = array();
        foreach ($payConfigs as $k => $v) 
        {
            if(!isset($cty[$v['ctype']])) $cty[$v['ctype']] = array('name'=>$ctyName[$v['ctype']],'pay_type'=>array(1=>array(),2=>array(),3=>array(),4=>array(),5=>array(),6=>array()));
            array_push($cty[$v['ctype']]['pay_type'][$v['platform']], $v['pay_type']);
            sort($cty[$v['ctype']]['pay_type'][$v['platform']]);
            $cty[$v['ctype']]['pay_type'][$v['platform']] = array_values( array_unique($cty[$v['ctype']]['pay_type'][$v['platform']]) );
        }

        $infos = array(
            'search' => $searchData,
            'data'   => $result[0],
            'pages'  => $pages,
            'cty'    =>$cty,
            'platform'=>$platform,
            'payTypes'=>json_encode($payTypes)
        );
        $this->load->view('management/payAddList',$infos);
    }
    
    public function freshpayconfig()
    {
        $this->check_capacity("3_12_11", true);
        $this->load->model('model_pay_config', 'model');
        $config = $this->model->getFreshPayConfig();
        if($config['fresh_payconfig'] == 0){
            $status = 1;
            $mesage = "关闭支付配置缓存更新";
        }else{
            $status = 0;
            $mesage = "开启支付配置缓存更新";
            //更新app线上支付tab列表缓存
            $this->refreshPayCache(2);
            $this->refreshPayCache(3);
            $this->refreshPayCache(5);
            $this->refreshPayCache(6);
        }
        $this->model->updateFreshPayConfig($status);
        $this->syslog(13, $mesage);
        echo json_encode(array('status'=>'SUCCESS','message'=>'更新成功'));die;
    }

    // 乐善奖
    public function getLsDetail($orderId, $lid)
    {
        $lsDetail = array();
        $totalMargin = 0;
        $this->load->model('model_order', 'order');
        $info = $this->order->getLsDetail($orderId, $lid);
        if(!empty($info))
        {
            foreach ($info as $items) 
            {
                if(empty($items['awardNum']))
                {
                    continue;
                }
                $lsDetail[$items['sub_order_id']] = $items;
                $totalMargin += $items['margin'];
            }
        }
        return array('detail' => $lsDetail, 'totalMargin' => $totalMargin);
    }

    // 合买宣言
    public function unitedIntroduce()
    {
        $this->check_capacity("6_10_3");
        $searchData = array(
            'number'        =>  $this->input->get("number", TRUE),
            'words'         =>  $this->input->get("words", TRUE),
            'chinesenumer'  =>  $this->input->get('chinesenumer', true),
            'check_status'  =>  $this->input->get('check_status', true),
            'name'          =>  $this->input->get('name', true),
            'orderId'       =>  $this->input->get('orderId', true),
            'start_time'    =>  $this->input->get('start_time', true),
            'end_time'      =>  $this->input->get('end_time', true),
        );
        
        if(empty($searchData['start_time'])) 
        {
            $searchData['start_time'] = date("Y-m-d 00:00:00");
        }
        if(empty($searchData['end_time'])) 
        {
            $searchData['end_time'] = date("Y-m-d 23:59:59");
        }
        $page = intval($this->input->get("p"));
        $page = $page <= 1 ? 1 : $page;

        $this->load->model('model_united_order', 'unitedOrder');
        $lists = $this->unitedOrder->list_intro($searchData, $page, self::NUM_PER_PAGE);
        $pageConfig = array(
            "page"      =>  $page,
            "npp"       =>  self::NUM_PER_PAGE,
            "allCount"  =>  $lists[1],
        );
        $pages = get_pagination($pageConfig);

        $info = array(
            'searchData'    =>  $searchData,
            'list'          =>  $lists[0],
            'pages'         =>  $pages,
        );
        $this->load->view('management/unitedIntroduce', $info);
    }

    // 删除
    public function deleteIntroduce()
    {
        $this->check_capacity('6_10_4', true);
        $orderId = $this->input->post("orderId", TRUE);
        $name = $this->input->post("name", TRUE);
        $this->load->model('model_united_order', 'unitedOrder');
        if($this->unitedOrder->deleteIntroduce($orderId))
        {
            $this->syslog(77, '删除' . $orderId . '合买宣言操作');
            $this->ajaxReturn('SUCCESSS', "删除{$name}合买宣言成功~");
        }
        else
        {
            $this->ajaxReturn('ERROR', "删除{$name}合买宣言失败~");
        }
    }

    // 手动成功
    public function handleIntroduce()
    {
        $this->check_capacity('6_10_4', true);
        $orderId = $this->input->post("orderId", TRUE);
        $name = $this->input->post("name", TRUE);
        $this->load->model('model_united_order', 'unitedOrder');
        if($this->unitedOrder->handleIntroduce($orderId))
        {
            $this->syslog(77, '手动成功' . $orderId . '合买宣言操作');
            $this->ajaxReturn('SUCCESSS', "手动成功{$name}合买宣言操作成功~");
        }
        else
        {
            $this->ajaxReturn('ERROR', "手动成功{$name}合买宣言操作失败~");
        }
    }

    public function refreshRateConfig($platform)
    {
        $this->load->model('model_pay_config');
        $this->load->driver('cache', array('adapter' => 'redis'));
        $REDIS = $this->config->item('REDIS');

        $info = array();
        $ctypeArr = array(2, 3, 4);
        $config = $this->model_pay_config->getRateConfig($platform, $ctypeArr);
        if(!empty($config))
        {
            foreach ($config as $items) 
            {
                $info[$items['ctype']][] = $items;
            }
        }

        $config = $this->model_pay_config->getFreshPayConfig();

        // 刷新缓存
        foreach ($ctypeArr as $ctype) 
        {
            if($config['fresh_payconfig'] == 0)
            {
                $redisKey = $platform . '_' . $ctype;
            }
            else
            {
                $redisKey =  'DEV_' . $platform . '_' . $ctype;
            }
            
            $detail = (!empty($info[$ctype])) ? $info[$ctype] : array();
            $this->cache->hSet($REDIS['PAY_RATE_CONFIG'], $redisKey, json_encode($detail));
        }
    }
}
