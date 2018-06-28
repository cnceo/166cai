<?php
// +----------------------------------------------------------------------
// | Created by  PhpStorm.
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2018 上海彩咖网络科技有限公司.
// +----------------------------------------------------------------------
// | Create Time (2018/4/13-9:17)
// +----------------------------------------------------------------------
// | Author: 唐轶俊 <tangyijun@km.com>
// +----------------------------------------------------------------------
// | 中奖排行榜
// +----------------------------------------------------------------------
class activityphb extends MY_Controller{
    /**
     * @var int
     * 定义每页加载条数
     */
    private $pageNUm = 20;
    /**
     * @var array
     * 彩种定义
     */
    private $plid = array(
        'syxw' =>  1,
        'ks'   =>  2,
        'jc'   =>  3,
    );

    /**
     * @var array
     * 彩种方法名
     */
    private $func = array(
        '1'   =>  'syxw',
        '2'   =>  'ks',
        '3'   =>  'jc',
    );
    /**
     * @var
     * 活动是否截止
     */
    private $isStop = false;

    /**
     * @var
     * 该期、该彩种的配置信息
     */
    private $configRow;

    /**
     * @var bool
     * 是否有上一期
     */
    private $isLast = false;

    /**
     * @var bool
     * 是否有下一期
     */
    private $isNext = false;

    private $lotteryUrlAndroid = array(
        '105' => "bet.btnclick4NotFinish('51')",
        '106' => "bet.btnclick4NotFinish('23529')",
        '107' => "bet.btnclick4NotFinish('52')",
        '108' => "bet.btnclick4NotFinish('33')",
        '109' => "bet.btnclick4NotFinish('35')",
        '110' => "bet.btnclick4NotFinish('23528')",
        '111' => "bet.btnclick4NotFinish('10022')",
        '112' => "bet.btnclick4NotFinish('42')",
        '113' => "bet.btnclick4NotFinish('43')",
        '114' => "bet.btnclick4NotFinish('11')",
        '115' => "bet.btnclick4NotFinish('19')",
        '116' => "bet.btnclick4NotFinish('21408')",
        '117' => "bet.btnclick4NotFinish('21406')",
        '118' => "bet.btnclick4NotFinish('21407')",
        '119' => "bet.btnclick4NotFinish('53')",
        '120' => "bet.btnclick4NotFinish('54')",
        '121' => "bet.btnclick4NotFinish('55')",
        '122' => "bet.btnclick4NotFinish('56')",
        '123' => "bet.btnclick4NotFinish('57')",
        '124' => "bet.btnclick4NotFinish('21421')",
    );


    private $lotteryUrlIos = array(
        '105' => "window.webkit.messageHandlers.doBet.postMessage({lid:'51'})",
        '106' => "window.webkit.messageHandlers.doBet.postMessage({lid:'23529'})",
        '107' => "window.webkit.messageHandlers.doBet.postMessage({lid:'52'})",
        '108' => "window.webkit.messageHandlers.doBet.postMessage({lid:'33'})",
        '109' => "window.webkit.messageHandlers.doBet.postMessage({lid:'35'})",
        '110' => "window.webkit.messageHandlers.doBet.postMessage({lid:'23528'})",
        '111' => "window.webkit.messageHandlers.doBet.postMessage({lid:'10022'})",
        '112' => "window.webkit.messageHandlers.doBet.postMessage({lid:'42'})",
        '113' => "window.webkit.messageHandlers.doBet.postMessage({lid:'43'})",
        '114' => "window.webkit.messageHandlers.doBet.postMessage({lid:'11'})",
        '115' => "window.webkit.messageHandlers.doBet.postMessage({lid:'19'})",
        '116' => "window.webkit.messageHandlers.doBet.postMessage({lid:'21408'})",
        '117' => "window.webkit.messageHandlers.doBet.postMessage({lid:'21406'})",
        '118' => "window.webkit.messageHandlers.doBet.postMessage({lid:'21407'})",
        '119' => "window.webkit.messageHandlers.doBet.postMessage({lid:'53'})",
        '120' => "window.webkit.messageHandlers.doBet.postMessage({lid:'54'})",
        '121' => "window.webkit.messageHandlers.doBet.postMessage({lid:'55'})",
        '122' => "window.webkit.messageHandlers.doBet.postMessage({lid:'56'})",
        '124' => "window.webkit.messageHandlers.doBet.postMessage({lid:'21421'})",
    );

    public function __construct()
    {
        parent::__construct();
        $this->load->model('win_rank_model','winrank');
    }

    /**
     * @param int $pissue
     * @param string $history
     * 快三
     */
    public function ks($pissue = 0,$history = ''){
        $this->configRow = $this->winrank->getConfigRow($this->plid['ks'],$pissue);
        if(empty($this->configRow)){
            die('活动期次不存在');
        }
        $this->getWinRankList($this->plid['ks'],$pissue,$history);
    }

    /**
     * @param int $pissue
     * @param string $history
     * 十一选五
     */
    public function syxw($pissue = 0,$history = ''){
        $this->configRow= $this->winrank->getConfigRow($this->plid['syxw'],$pissue);
        if(empty($this->configRow)){
            die('活动期次不存在');
        }
        $this->getWinRankList($this->plid['syxw'],$pissue,$history);
    }

    /**
     * @param int $pissue
     * @param string $history
     * 竞彩
     */
    public function jc($pissue = 0,$history = ''){
        //先查询期次是否存在
        $this->configRow = $this->winrank->getConfigRow($this->plid['jc'],$pissue);
        if(empty($this->configRow)){
            die('活动期次不存在');
        }
        $this->getWinRankList($this->plid['jc'],$pissue,$history);
    }

    /**
     * @param $plid
     * @param $pissue
     * @param string $history 是否查询历史期
     * 中奖排行榜统一方法
     */
    public function getWinRankList($plid,$pissue,$history = ''){
        //重新设置cookie
        $platform = $this->platform();
        if('IOS' == $platform){
            $uid = $this->input->cookie('u');
            if(empty($uid)){
                $this->input->set_cookie("u",$this->uid,3600 * 24 * 7);
            }
            if(empty($this->uid)){
                $this->uid = $this->input->cookie('u');
            }
        }
        $maxRank = $this->getMaxRank($plid, $pissue);
        $list  = $this->winrank->getWinRankUser($plid,$pissue,0,$this->pageNUm,$history,$maxRank); //查排名列表
        //活动状态
        $current_time = time();
        if($current_time <= strtotime($this->configRow['end_time'])){
            $this->isStop = true;
        }
        //活动奖项派发预期
        if(empty($this->configRow['cstate'])){
            $predict_time = strtotime($this->configRow['statistics_end_time']) + 600;
            if($current_time >= strtotime($this->configRow['end_time']) && $current_time <= $predict_time){
                $predict_time = date("Y-m-d H:i:s",$predict_time);
            }else{
                $predict_time = date("Y-m-d H:i:s",$predict_time);
            }
        }
        //查询最大期
        $max_pissue = $this->winrank->getMaxPissue($plid);
        //是否有上一期
        $last = $this->winrank->getConfigRow($plid,$pissue - 1);
        if($last && !empty($last)){
            $this->isLast = $pissue - 1;
        }
        //是否有下一期
        if($pissue + 1 < $max_pissue['max_pissue']){
            $next = $this->winrank->getConfigRow($plid,$pissue + 1);
            if($next && !empty($next)){
                $this->isNext = $pissue + 1;
            }
        }
        $rank_id = '未上榜';
        $count_prize = '未上榜';
        $expect_bonuses = 0;
        //登录
        if($this->uid){
            //查看是否在排行榜
            $isRank = $this->winrank->getIsRank($this->uid,$plid,$pissue);
            if(!empty($isRank) && !empty($isRank['rankId'])){
                $rank_id = '第'.$isRank['rankId'].'名';
            }
            $detail = $this->winrank->getContPrice($this->uid,$plid,$pissue);
            $count_prize = (!empty($detail)) ? ParseUnit($detail['margin'], 1) . '元' : $count_prize;
            $expect_bonuses =  ParseUnit($isRank['addMoney'], 1);
        }
        $data = array(
            'list'           => $list, //中奖名单
            'rank_id'        => $rank_id, //我的排名
            'count_prize'    => $count_prize,
            'plid'           => $plid,
            'pissue'         => $pissue,
            'is_stop'        => $this->isStop, //活动是否截止
            'is_cstate'      => $this->configRow['cstate'], //是否派奖
            'expect_bonuses' => $expect_bonuses,
            'rule'            => $this->configRow['rule'], //活动规则
            'func'            => $this->func[$plid],
            'time_limit'     => date('m.d',strtotime($this->configRow['start_time'])).'-'.date('m.d',strtotime($this->configRow['end_time'])),//活动时间
            'is_last'        => $this->isLast,
            'is_next'        => $this->isNext,
            'predict_time'  => $predict_time,
            'banner'         => $this->configRow['imgUrl'],
            'config'         => $this->configRow,
            'max_pissue'     => $max_pissue['max_pissue'],
            'platform'       => $platform
        );
        if(!empty($list)) {
            if ($history == 'history') {
                $this->load->view('winrank/history', $data);
            } else {
                $this->load->view('winrank/index', $data);
            }
        } else{
            if($history == 'history'){
                $this->load->view('winrank/historynone',$data);
            }else{
                $this->load->view('winrank/none',$data);
            }
        }
    }

    /**
     * ajax加载分页数据
     */
    public function ajaxGetWin(){
        $page = $this->input->post('page');
        $plid = $this->input->post('plid');//获取期次
        $pissue = $this->input->post('pissue'); //获取彩种
        $is_history = $this->input->post('is_history');
        $page = max(1,$page);  //开始位置
        $start = ($page - 1) * $this->pageNUm;
        // 获取最大人数
        $maxRank = $this->getMaxRank($plid, $pissue);
        $data['list'] = $this->winrank->getWinRankUser($plid,$pissue,$start,$this->pageNUm,$is_history,$maxRank);
        echo $this->load->view('winrank/ajaxGetwin',$data,true);
    }

    /**
     * 查询排名情况
     */
    public function ajaxSearchRank(){
        $data = $this->input->post();
        if(!$this->uid){
            die(json_encode(array('code'=> 10010,'msg'=>'还未登陆哦！')));
        }
        $win = $this->winrank->getWindCount($this->uid,$data['star_time'],$data['end_time'],$data['lids']);

        die(json_encode(array('code' => 10000,'win_count'=>$win['tmargin'] ? ParseUnit($win['tmargin']) : 0)));
    }

    /**
     * 投注弹窗
     */
    public function bettingWindow(){
        $data = $this->input->post('lids', false);
        $data = explode(',',$data);
        $platform = $this->platform();
        $datas = array();
        foreach ($data as $lid)
        {
            $datas[] = $this->getLotteryData($lid,$platform);
        }
        $result = array(
            'status' => '1',
            'msg' => '加载中',
            'data' => $this->load->view('winrank/bettingWindow', array('datas' => $datas), true)
        );
        die(json_encode($result));
    }

    private function getLotteryData($lid,$platform)
    {
        if($platform == 'Android'){
            $data = array(
                '51' => array(
                    'name' => '双色球',
                    'onclick' => 'onclick="' . $this->lotteryUrlAndroid['105'] . ';"',
                    'imgUrl' => 'logo3/goucai_logo_ssq.png',
                ),
                '23529' => array(
                    'name' => '大乐透',
                    'onclick' => 'onclick="' . $this->lotteryUrlAndroid['106'] . ';"',
                    'imgUrl' => 'logo3/goucai_logo_dlt.png',
                ),
                '52' => array(
                    'name' => '福彩3D',
                    'onclick' => 'onclick="' . $this->lotteryUrlAndroid['107'] . ';"',
                    'imgUrl' => 'logo3/goucai_logo_fc3d.png',
                ),
                '33' => array(
                    'name' => '排列三',
                    'onclick' => 'onclick="' . $this->lotteryUrlAndroid['108'] . ';"',
                    'imgUrl' => 'logo3/goucai_logo_pl3.png',
                ),
                '35' => array(
                    'name' => '排列五',
                    'onclick' => 'onclick="' . $this->lotteryUrlAndroid['109'] . ';"',
                    'imgUrl' => 'logo3/goucai_logo_pl5.png',
                ),
                '23528' => array(
                    'name' => '七乐彩',
                    'onclick' => 'onclick="' . $this->lotteryUrlAndroid['110'] . ';"',
                    'imgUrl' => 'logo3/goucai_logo_qlc.png',
                ),
                '10022' => array(
                    'name' => '七星彩',
                    'onclick' => 'onclick="' . $this->lotteryUrlAndroid['111'] . ';"',
                    'imgUrl' => 'logo3/goucai_logo_qxc.png',
                ),
                '42' => array(
                    'name' => '竞彩足球',
                    'onclick' => 'onclick="' . $this->lotteryUrlAndroid['112'] . ';"',
                    'imgUrl' => 'logo3/goucai_logo_jczq.png',
                ),
                '43' => array(
                    'name' => '竞彩篮球',
                    'onclick' => 'onclick="' . $this->lotteryUrlAndroid['113'] . ';"',
                    'imgUrl' => 'logo3/goucai_logo_jclq.png',
                ),
                '11' => array(
                    'name' => '胜负彩',
                    'onclick' => 'onclick="' . $this->lotteryUrlAndroid['114'] . ';"',
                    'imgUrl' => 'logo3/goucai_logo_sfc.png',
                ),
                '19' => array(
                    'name' => '任选九',
                    'onclick' => 'onclick="' . $this->lotteryUrlAndroid['115'] . ';"',
                    'imgUrl' => 'logo3/goucai_logo_r9.png',
                ),
                '21408' => array(
                    'name' => '惊喜11选5',
                    'onclick' => 'onclick="' . $this->lotteryUrlAndroid['116'] . ';"',
                    'imgUrl' => 'logo3/goucai_logo_hbsyxw.png',
                ),
                '21406' => array(
                    'name' => '老11选5',
                    'onclick' => 'onclick="' . $this->lotteryUrlAndroid['117'] . ';"',
                    'imgUrl' => 'logo3/goucai_logo_syxw.png',
                ),
                '21407' => array(
                    'name' => '新11选5',
                    'onclick' => 'onclick="' . $this->lotteryUrlAndroid['118'] . ';"',
                    'imgUrl' => 'logo3/goucai_logo_jxsyxw.png',
                ),
                '53' => array(
                    'name' => '经典快3',
                    'onclick' => 'onclick="' . $this->lotteryUrlAndroid['119'] . ';"',
                    'imgUrl' => 'logo3/goucai_logo_ks.png',
                ),
                '54' => array(
                    'name' => '快乐扑克',
                    'onclick' => 'onclick="' . $this->lotteryUrlAndroid['120'] . ';"',
                    'imgUrl' => 'logo3/goucai_logo_klpk.png',
                ),
                '55' => array(
                    'name' => '老时时彩',
                    'onclick' => 'onclick="' . $this->lotteryUrlAndroid['121'] . ';"',
                    'imgUrl' => 'logo3/goucai_logo_cqssc.png',
                ),
                '56' => array(
                    'name' => '易快3',
                    'onclick' => 'onclick="' . $this->lotteryUrlAndroid['122'] . ';"',
                    'imgUrl' => 'logo3/goucai_logo_jlks.png',
                ),
                '57' => array(
                    'name' => '红快3',
                    'onclick' => 'onclick="' . $this->lotteryUrlAndroid['123'] . ';"',
                    'imgUrl' => 'logo3/goucai_logo_jxks.png',
                ),
                '21421' => array(
                    'name' => '乐11选5',
                    'onclick' => 'onclick="' . $this->lotteryUrlAndroid['124'] . ';"',
                    'imgUrl' => 'logo3/goucai_logo_gdsyxw.png',
                ),
            );
        }

        if($platform == 'IOS'){
            $data = array(
                '51' => array(
                    'name' => '双色球',
                    'onclick' => 'onclick="' . $this->lotteryUrlIos['105'] . ';"',
                    'imgUrl' => 'logo3/goucai_logo_ssq.png',
                ),
                '23529' => array(
                    'name' => '大乐透',
                    'onclick' => 'onclick="' . $this->lotteryUrlIos['106'] . ';"',
                    'imgUrl' => 'logo3/goucai_logo_dlt.png',
                ),
                '52' => array(
                    'name' => '福彩3D',
                    'onclick' => 'onclick="' . $this->lotteryUrlIos['107'] . ';"',
                    'imgUrl' => 'logo3/goucai_logo_fc3d.png',
                ),
                '33' => array(
                    'name' => '排列三',
                    'onclick' => 'onclick="' . $this->lotteryUrlIos['108'] . ';"',
                    'imgUrl' => 'logo3/goucai_logo_pl3.png',
                ),
                '35' => array(
                    'name' => '排列五',
                    'onclick' => 'onclick="' . $this->lotteryUrlIos['109'] . ';"',
                    'imgUrl' => 'logo3/goucai_logo_pl5.png',
                ),
                '23528' => array(
                    'name' => '七乐彩',
                    'onclick' => 'onclick="' . $this->lotteryUrlIos['110'] . ';"',
                    'imgUrl' => 'logo3/goucai_logo_qlc.png',
                ),
                '10022' => array(
                    'name' => '七星彩',
                    'onclick' => 'onclick="' . $this->lotteryUrlIos['111'] . ';"',
                    'imgUrl' => 'logo3/goucai_logo_qxc.png',
                ),
                '42' => array(
                    'name' => '竞彩足球',
                    'onclick' => 'onclick="' . $this->lotteryUrlIos['112'] . ';"',
                    'imgUrl' => 'logo3/goucai_logo_jczq.png',
                ),
                '43' => array(
                    'name' => '竞彩篮球',
                    'onclick' => 'onclick="' . $this->lotteryUrlIos['113'] . ';"',
                    'imgUrl' => 'logo3/goucai_logo_jclq.png',
                ),
                '11' => array(
                    'name' => '胜负彩',
                    'onclick' => 'onclick="' . $this->lotteryUrlIos['114'] . ';"',
                    'imgUrl' => 'logo3/goucai_logo_sfc.png',
                ),
                '19' => array(
                    'name' => '任选九',
                    'onclick' => 'onclick="' . $this->lotteryUrlIos['115'] . ';"',
                    'imgUrl' => 'logo3/goucai_logo_r9.png',
                ),
                '21408' => array(
                    'name' => '惊喜11选5',
                    'onclick' => 'onclick="' . $this->lotteryUrlIos['116'] . ';"',
                    'imgUrl' => 'logo3/goucai_logo_hbsyxw.png',
                ),
                '21406' => array(
                    'name' => '老11选5',
                    'onclick' => 'onclick="' . $this->lotteryUrlIos['117'] . ';"',
                    'imgUrl' => 'logo3/goucai_logo_syxw.png',
                ),
                '21407' => array(
                    'name' => '新11选5',
                    'onclick' => 'onclick="' . $this->lotteryUrlIos['118'] . ';"',
                    'imgUrl' => 'logo3/goucai_logo_jxsyxw.png',
                ),
                '53' => array(
                    'name' => '经典快3',
                    'onclick' => 'onclick="' . $this->lotteryUrlIos['119'] . ';"',
                    'imgUrl' => 'logo3/goucai_logo_ks.png',
                ),
                '54' => array(
                    'name' => '快乐扑克',
                    'onclick' => 'onclick="' . $this->lotteryUrlIos['120'] . ';"',
                    'imgUrl' => 'logo3/goucai_logo_klpk.png',
                ),
                '55' => array(
                    'name' => '老时时彩',
                    'onclick' => 'onclick="' . $this->lotteryUrlIos['121'] . ';"',
                    'imgUrl' => 'logo3/goucai_logo_cqssc.png',
                ),
                '56' => array(
                    'name' => '易快3',
                    'onclick' => 'onclick="' . $this->lotteryUrlIos['122'] . ';"',
                    'imgUrl' => 'logo3/goucai_logo_jlks.png',
                ),
                '57' => array(
                    'name' => '红快3',
                    'onclick' => 'onclick="' . $this->lotteryUrlIos['123'] . ';"',
                    'imgUrl' => 'logo3/goucai_logo_jxks.png',
                ),
                '21421' => array(
                    'name' => '乐11选5',
                    'onclick' => 'onclick="' . $this->lotteryUrlIos['124'] . ';"',
                    'imgUrl' => 'logo3/goucai_logo_gdsyxw.png',
                ),
            );
        }
        return $data[$lid];
    }

    public function platform(){
        if(strpos($_SERVER['HTTP_USER_AGENT'], 'iPhone')||strpos($_SERVER['HTTP_USER_AGENT'], 'iPad')){
            return 'IOS';
        }else if(strpos($_SERVER['HTTP_USER_AGENT'], 'Android')){
            return 'Android';
        }else{
            return 'Other';
        }
    }

    // 最大翻页数
    public function getMaxRank($plid, $pissue)
    {
        $maxRank = 1;
        $configRow = $this->winrank->getConfigRow($plid, $pissue);
        if(!empty($configRow['extra']))
        {
            $extra = json_decode($configRow['extra'], true);
            if(!empty($extra))
            {
                $extra = end($extra);
                $maxRank = $extra['max'];
            }
        }
        return $maxRank;
    }
}