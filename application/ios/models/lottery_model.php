<?php

class Lottery_Model extends MY_Model {

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
    const HBSYXW = 21408;
    const KS = 53;
    const KLPK = 54;
    const CQSSC = 55;
    const JLKS = 56;
    const JXKS = 57;
    const GDSYXW = 21421;

    private static $CN_NAMES = array(
        self::SFC => '胜负彩',
        self::RJ => '任选九',
        self::PLS => '排列三',
        self::PLW => '排列五',
        self::BJDC => '北京单场',
        self::JCZQ => '竞彩足球',
        self::JCLQ => '竞彩篮球',
        self::SSQ => '双色球',
        self::FCSD => '福彩3D',
        self::QXC => '七星彩',
        self::SYYDJ => '老11选5',
        self::QLC => '七乐彩',
        self::DLT => '大乐透',
        self::GJ => '冠军彩',
        self::GYJ => '冠亚军彩',
        self::JXSYXW => '新11选5',
        self::HBSYXW => '惊喜11选5',
    	self::KS => '经典快3',
    	self::KLPK => '快乐扑克',
    	self::CQSSC => '老时时彩',
    	self::JLKS => '易快3',
        self::JXKS => '红快3',
        self::GDSYXW => '乐11选5',
    );

    private static $EN_NAMES = array(
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
        self::QLC => 'qlc',
        self::DLT => 'dlt',
        self::GJ => 'gj',
        self::GYJ => 'gyj',
        self::JXSYXW => 'jxsyxw',
        self::HBSYXW => 'hbsyxw',
    	self::KS => 'ks',
    	self::KLPK => 'klpk',
    	self::CQSSC => 'cqssc',
    	self::JLKS => 'jlks',
        self::JXKS => 'jxks',
        self::GDSYXW => 'gdsyxw',
    );

    private static $TB_NAMES = array(
        self::SFC => 'cp_rsfc_paiqi',
        self::RJ => 'cp_rsfc_paiqi',
        self::PLS => 'cp_pl3_paiqi',
        self::PLW => 'cp_pl5_paiqi',
        self::BJDC => 'cp_bjdc_paiqi',
        self::JCZQ => 'cp_jczq_paiqi',
        self::JCLQ => 'cp_jclq_paiqi',
        self::SSQ => 'cp_ssq_paiqi',
        self::FCSD => 'cp_fc3d_paiqi',
        self::QXC => 'cp_qxc_paiqi',
        self::SYYDJ => 'cp_syxw_paiqi',
        self::QLC => 'cp_qlc_paiqi',
        self::DLT => 'cp_dlt_paiqi',
        self::JXSYXW => 'cp_jxsyxw_paiqi',
        self::HBSYXW => 'cp_hbsyxw_paiqi',
    	self::KS => 'cp_ks_paiqi',
    	self::KLPK => 'cp_klpk_paiqi',
    	self::CQSSC => 'cp_cqssc_paiqi',
    	self::JLKS => 'cp_jlks_paiqi',
        self::JXKS => 'cp_jxks_paiqi',
        self::GDSYXW => 'cp_gdsyxw_paiqi',
    );

    public function __construct() {
        parent::__construct();
    }

    public function getCnName($lotteryId) {
        $cnName = '未知';
        if (isset(self::$CN_NAMES[$lotteryId])) {
            $cnName = self::$CN_NAMES[$lotteryId];
        }

        return $cnName;
    }

    public function getCnNames() {
        return self::$CN_NAMES;
    }

    public function getEnName($lotteryId) {
        $enName = 'unknown';
        if (isset(self::$EN_NAMES[$lotteryId])) {
            $enName = self::$EN_NAMES[$lotteryId];
        }

        return $enName;
    }

    public function getEnNames() {
        return self::$EN_NAMES;
    }

    public function getTbName($lotteryId)
    {
        $tbName = '未知';
        if (isset(self::$TB_NAMES[$lotteryId])) {
            $tbName = self::$TB_NAMES[$lotteryId];
        }
    
        return $tbName;
    }

    /**
     * 获取采种配置
     * @param int $lid
     * @param string $filed
     * @return array
     */
    public function getLotteryConfig($lid, $filed = '')
    {
        if (!$filed)
        {
            $filed = '*';
        }
        $sql = "select {$filed} from cp_lottery_config where lottery_id='{$lid}'";
        $date = $this->slaveCfg->query($sql)->getRow();
        return $date;
    }
}
