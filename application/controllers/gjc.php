<?php

class Gjc extends MY_Controller {
    
    public function __construct() {
        parent::__construct();
        $this->load->model('gjc_model');
    }

    public function index() {
        $this->display2018('gj');
    }
    
    public function gyj() {
        $this->display2018('gyj');
    }
    
    private function display2018($lottery) {
        $teams = $this->gjc_model->getTeams();
        $combines = $this->gjc_model->getCombines();
        $this->load->driver('cache', array('adapter' => 'redis'));
        $REDIS = $this->config->item('REDIS');
        $lotteryConfig = $this->cache->get($REDIS['LOTTERY_CONFIG']);
        $showBind = false;
        if (!empty($this->uinfo) && !$this->isBindForRecharge()) $showBind = true;
        $classArr = array('', 'out', 'win', 'stop');
        $statusArr = array('开售', '淘汰', '冠军', '停售');
        $baseUrl = $this->config->item('base_url');
        $data = compact('teams', 'combines', 'showBind', 'classArr', 'statusArr', 'lottery', 'baseUrl');
        $data['lotteryConfig'] = json_decode($lotteryConfig, true);
        $this->load->view('v1.1/gjc/index2018', $data);
    }
    
    private function display2016() {
        $teams = $this->gjc_model->getTeams();
        $gystop = true;
        $taotai = 0;
        foreach ($teams as $team) {
            switch ($team['status']) {
                case '1':
                    $taotai++;
                    break;
            }
            $gjtm[$team['mid']] = array('name'   => $team['name'],'logo'   => $team['logo']);
            $gjList[$team['groups']][$team['mid']] = array(
                'name'   => $team['name'],
                'odds'   => $this->treateOdd($team['odds']),
                'rank'   => $team['rank'],
                'logo'   => $team['logo'],
                'status' => $team['status']
            );
        }
        $combines = $this->gjc_model->getCombines();
        foreach ($combines as $combine) {
            $cmidList[$combine['mid']] = $combine['groups'];
            $combineList[$combine['groups']] = array(
                'mid'    => $combine['mid'],
                'name'   => $combine['name'],
                'odds'   => $this->treateOdd($combine['odds']),
                'status' => $combine['status']
            );
        }
        ksort($cmidList);
        $this->load->driver('cache', array('adapter' => 'redis'));
        $REDIS = $this->config->item('REDIS');
        $lotteryConfig = $this->cache->get($REDIS['LOTTERY_CONFIG']);
        $showBind = false;
        if (!empty($this->uinfo) && !$this->isBindForRecharge())
        {
            $showBind = true;
        }
        $other = $this->gjc_model->getTaotai();
        foreach ($other as $val) {
            $ttRes[$val['id']] = $val;
        }
        if ($ttRes[51]['hid'] > 0) {
            $gystop = false;
            $hlteam = $gjtm[$ttRes[51]['hid']];
            $alteam = $gjtm[$ttRes[51]['aid']];
            $lastteam[] = "<div class='euro-bet-img'><img src='/caipiaoimg/v1.1/img/active/euro/img-".$hlteam['logo'].".png' width='100' height='100' alt=''>".$hlteam['name']."</div>";
            $lastteam[] = "<div class='euro-bet-img'><img src='/caipiaoimg/v1.1/img/active/euro/img-".$alteam['logo'].".png' width='100' height='100' alt=''>".$alteam['name']."</div>";
        }
        $data = compact('gjList', 'combineList', 'showBind', 'gystop', 'lastteam', 'cmidList', 'ttRes');
        $data['lotteryConfig'] = json_decode($lotteryConfig, true);
        $this->load->view('v1.1/gjc/index', $data);
    }
    
    private function treateOdd($odd)
    {
    	$arr = explode('.', $odd);
    	$arr[1] = empty($arr[1]) ? '00' : str_pad($arr[1], 2, '0', STR_PAD_RIGHT);
    	return implode('.', $arr);
    }
    
}
