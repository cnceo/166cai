<?php

class BetCnName {

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
    const JXSYXW = 21407;
    const QLC = 23528;
    const DLT = 23529;
    const GJ = 44;
    const GYJ = 45;
    const KS = 53;
    const JLKS = 56;
    const JXKS = 57;
    const HBSYXW = 21408;
    const KLPK = 54;
    const CQSSC = 55;
    const GDSYXW = 21421;

    public static $playTypeCnName = array(
        self::SFC => array(
            0 => '',
        ),
        self::RJ => array(
            0 => '',    
        ),
        self::PLS => array(
            0 => '',
            1 => '直选', 
            2 => '组三', 
            3 => '组六',
        ),
        self::PLW => array(
            0 => '',
        ),
        self::BJDC => array(
            0 => '',
        ),
        self::JCZQ => array(
            0 => '混合过关',
            1 => '胜平负',
            2 => '让分胜平负',
            3 => '半全场',
            4 => '进球数',
            5 => '比分',
        ),
        self::JCLQ => array(
            0 => '混合过关',
            1 => '胜负',
            2 => '让分胜负',
            3 => '胜分差',
            4 => '大小分',
        ),
        self::SSQ => array(
            0 => '',    
        ),
        self::FCSD => array(
            1   =>'直选', 
            2   =>'组三', 
            3   =>'组六',
        ),
        self::QXC => array(
            0 => '',
        ),
        self::SYYDJ => array(
            0   => '',
            1   => '前1',       	
            2   => '任选二',    
            3   => '任选三',    
            4   => '任选四',    
            5   => '任选五',    
            6   => '任选六',    
            7   => '任选七',    
            8   => '任选八',    
            9   => '前二直选',  
            10   => '前三直选',  
            11   => '前二组选',  
            12   => '前三组选',
        ),
    	self::JXSYXW => array(
    		0   => '',
    		1   => '前1',
    		2   => '任选二',
    		3   => '任选三',
    		4   => '任选四',
    		5   => '任选五',
    		6   => '任选六',
    		7   => '任选七',
    		8   => '任选八',
    		9   => '前二直选',
    		10   => '前三直选',
    		11   => '前二组选',
    		12   => '前三组选',
    	),
    	self::HBSYXW => array(
    		0   => '',
    		1   => '前1',
    		2   => '任选二',
    		3   => '任选三',
    		4   => '任选四',
    		5   => '任选五',
    		6   => '任选六',
    		7   => '任选七',
    		8   => '任选八',
    		9   => '前二直选',
    		10   => '前三直选',
    		11   => '前二组选',
    		12   => '前三组选',
    	),
        self::GDSYXW => array(
            0   => '',
            1   => '前1',
            2   => '任选二',
            3   => '任选三',
            4   => '任选四',
            5   => '任选五',
            6   => '任选六',
            7   => '任选七',
            8   => '任选八',
            9   => '前二直选',
            10   => '前三直选',
            11   => '前二组选',
            12   => '前三组选',
        ),
        self::QLC => array(
            0 => '',
        ),
        self::DLT => array(
            0 => '普通',
            2 => '普通 追加',
        ), 
    	self::KS => array(
    		0 => '',
    		1 => '和值',
    		2 => '三同号通选',
    		3 => '三同号单选',
    		4 => '三不同号',
    		5 => '三连号通选',
    		6 => '二同号复选',
    		7 => '二同号单选',
    		8 => '二不同号',
    	),    
    	self::JLKS => array(
    		0 => '',
    		1 => '和值',
    		2 => '三同号通选',
    		3 => '三同号单选',
    		4 => '三不同号',
    		5 => '三连号通选',
    		6 => '二同号复选',
    		7 => '二同号单选',
    		8 => '二不同号',
    	),  
        self::JXKS => array(
            0 => '',
            1 => '和值',
            2 => '三同号通选',
            3 => '三同号单选',
            4 => '三不同号',
            5 => '三连号通选',
            6 => '二同号复选',
            7 => '二同号单选',
            8 => '二不同号',
        ),
        self::KLPK => array(
            0   =>  '',
            1   =>  '任选一',
            2   =>  '任选二单式',
            21  =>  '任选二复式',
            22  =>  '任选二胆拖',
            3   =>  '任选三单式',
            31  =>  '任选三复式',
            32  =>  '任选三胆拖',
            4   =>  '任选四单式',
            41  =>  '任选四复式',
            42  =>  '任选四胆拖',
            5   =>  '任选五单式',
            51  =>  '任选五复式',
            52  =>  '任选五胆拖',
            6   =>  '任选六单式',
            61  =>  '任选六复式',
            62  =>  '任选六胆拖',
            7   =>  '同花',
            8   =>  '同花顺',
            9   =>  '顺子',
            10  =>  '豹子',
            11  =>  '对子',
        ),    
        self::CQSSC => array(
            1   =>  '大小单双',
            10  =>  '一星单式',
            20  =>  '二星单式',
            21  =>  '二星复式',
            23  =>  '二星组选',
            25  =>  '二星和值',
            26  =>  '二星组选和值',
            27  =>  '二星组选复式',
            30  =>  '三星单式',
            31  =>  '三星复式',
            33  =>  '三星组三',
            34  =>  '三星组六',
            35  =>  '三星和值',
            36  =>  '三星组选和值',
            37  =>  '三星组三复式',
            38  =>  '三星组六复式',
            40  =>  '五星单式',
            41  =>  '五星复式',
            43  =>  '五星通选',
        ),        
    );
    
    public static $playTypeEgName = array(
        self::SYYDJ => array(
            0   => '',
            1   => 'q1',       	
            2   => 'rx2',    
            3   => 'rx3',    
            4   => 'rx4',    
            5   => 'rx5',    
            6   => 'rx6',    
            7   => 'rx7',    
            8   => 'rx8',    
            9   => 'qzhi2',  
            10   => 'qzhi3',  
            11   => 'qzu2',  
            12   => 'qzu3',
        ),
    	self::JXSYXW => array(
    		0   => '',
    		1   => 'q1',
    		2   => 'rx2',
    		3   => 'rx3',
    		4   => 'rx4',
    		5   => 'rx5',
    		6   => 'rx6',
    		7   => 'rx7',
    		8   => 'rx8',
    		9   => 'qzhi2',
    		10   => 'qzhi3',
    		11   => 'qzu2',
    		12   => 'qzu3',
    	),
        self::GDSYXW => array(
            0   => '',
            1   => 'q1',
            2   => 'rx2',
            3   => 'rx3',
            4   => 'rx4',
            5   => 'rx5',
            6   => 'rx6',
            7   => 'rx7',
            8   => 'rx8',
            9   => 'qzhi2',
            10   => 'qzhi3',
            11   => 'qzu2',
            12   => 'qzu3',
        ),
        self::FCSD => array(
            1   =>'zx', 
            2   =>'z3', 
            3   =>'z6',
        ),
        self::PLS => array(
            0 => '',
            1 => 'zx', 
            2 => 'z3', 
            3 => 'z6',
        ),
        self::JCZQ => array(
            0 => 'hh',
            1 => 'spf',
            2 => 'rqspf',
            3 => 'bqc',
            4 => 'jqs',
            5 => 'cbf',
        ),
        self::JCLQ => array(
            0 => 'hh',
            1 => 'sf',
            2 => 'rfsf',
            3 => 'sfc',
            4 => 'dxf',
        ),
    	self::KS => array(
    		0 => '',
    		1 => 'hz',
    		2 => 'sthtx',
    		3 => 'sthdx',
    		4 => 'sbth',
    		5 => 'slhtx',
    		6 => 'ethfx',
    		7 => 'ethdx',
    		8 => 'ebth',
    	),
    	self::JLKS => array(
    		0 => '',
    		1 => 'hz',
    		2 => 'sthtx',
    		3 => 'sthdx',
    		4 => 'sbth',
    		5 => 'slhtx',
    		6 => 'ethfx',
    		7 => 'ethdx',
    		8 => 'ebth',
    	),     
        self::JXKS => array(
            0 => '',
            1 => 'hz',
            2 => 'sthtx',
            3 => 'sthdx',
            4 => 'sbth',
            5 => 'slhtx',
            6 => 'ethfx',
            7 => 'ethdx',
            8 => 'ebth',
        ),
        self::KLPK => array(
            0   =>  '',
            1   =>  'r1',
            2   =>  'r2ds',
            21  =>  'r2fs',
            22  =>  'r2dt',
            3   =>  'r3ds',
            31  =>  'r3fs',
            32  =>  'r3dt',
            4   =>  'r4ds',
            41  =>  'r4fs',
            42  =>  'r4dt',
            5   =>  'r5ds',
            51  =>  'r5fs',
            52  =>  'r5dt',
            6   =>  'r6ds',
            61  =>  'r6fs',
            62  =>  'r6dt',
            7   =>  'th',
            8   =>  'ths',
            9   =>  'sz',
            10  =>  'bz',
            11  =>  'dz',
        ),
        self::CQSSC => array(
            1   =>  'dxds',
            10  =>  '1xzhix',
            20  =>  '2xzhixds',
            21  =>  '2xzhixfs',
            23  =>  '2xzux',
            25  =>  '2xhz',
            26  =>  '2xzuxhz',
            27  =>  '2xzuxfs',
            30  =>  '3xzhixds',
            31  =>  '3xzhixfs',
            33  =>  '3xzu3',
            34  =>  '3xzu6',
            35  =>  '3xhz',
            36  =>  '3xzuxhz',
            37  =>  '3xzu3fs',
            38  =>  '3xzu6fs',
            40  =>  '5xzhixds',
            41  =>  '5xzhixfs',
            43  =>  '5xtx',
        ),        
    );

    public static $BetCnName = array(
        self::SFC => '胜负彩',
        self::RJ => '任九',
        self::PLS => '排列3',
        self::PLW => '排列5',
        self::BJDC => '北京单场',
        self::JCZQ => '竞彩足球',
        self::JCLQ => '竞彩篮球',
        self::SSQ => '双色球',
        self::FCSD => '福彩3D',
        self::QXC => '七星彩',
        self::SYYDJ => '11运夺金',
    	self::JXSYXW => '新11选5',
        self::QLC => '七乐彩',
        self::DLT => '大乐透',
    	self::GJ => '冠军彩',
    	self::GYJ => '冠亚军彩',  
    	self::KS => '上海快三',
        self::JLKS => '吉林快三',
        self::JXKS => '江西快三',
    	self::HBSYXW => '惊喜11选5',
        self::KLPK => '快乐扑克',
        self::CQSSC => '老时时彩',
        self::GDSYXW => '乐11选5',
    );
    
    public static $BetEgName = array(
        self::SFC => 'sfc',
        self::RJ => 'rj',
        self::PLS => 'pls',
        self::PLW => 'plw',
        self::BJDC => 'bjdc',
        self::JCZQ => 'jczq',
        self::JCLQ => 'jclq',
        self::SSQ => 'ssq',
        self::FCSD => 'fcsd',
        self::QXC => 'qxc',
        self::SYYDJ => 'syxw',
    	self::JXSYXW => 'jxsyxw',
        self::QLC => 'qlc',
        self::DLT => 'dlt',
    	self::GJ => 'gj',
    	self::GYJ => 'gyj',
    	self::KS => 'ks',
        self::JLKS => 'jlks',
        self::JXKS => 'jxks',
    	self::HBSYXW => 'hbsyxw',
        self::KLPK => 'klpk',
        self::CQSSC => 'cqssc',
        self::GDSYXW => 'gdsyxw',
    );

    public function getCnName($lotteryId) {
        $cnName = '未知';
        if (isset(self::$BetCnName[$lotteryId])) {
            $cnName = self::$BetCnName[$lotteryId];
        }

        return $cnName;
    }
    
 	public function getEgName($lotteryId) 
 	{
        $cnName = '';
        if (isset(self::$BetEgName[$lotteryId])) 
        {
            $cnName = self::$BetEgName[$lotteryId];
        }

        return $cnName;
    }

    // 彩种对应
    public function getLotteryInfo()
    {
        return self::$BetCnName;
    }

    public function getLotteryEgName()
    {
        return self::$BetEgName;
    }

}
