<?php
/**
 * 竞猜活动脚本文件
 * @author Administrator
 *
 */
class Cli_Jchd extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('activity_jchd_model');
    }
    
    public function index() {
        $this->startIssue();
        $this->totalRatio();
        $this->updateMatch();
        //$this->sendPrice();
        //$this->rank();
    }
    
    /**
     * 更新期次状态  设置开始
     */
    public function startIssue()
    {
        $jchdConfig = $this->activity_jchd_model->getConfigByStatus(0);
        $nowTime = date('Y-m-d H:i:s');
        if(!$jchdConfig || ($nowTime < $jchdConfig['start_time'])) {
            //无期次或未到开始时间直接结束
            return ;
        }
        
        $this->activity_jchd_model->updateConfig($jchdConfig['id'], array('status' => '1'));
    }
    
    /**
     * 更新投注比例
     */
    public function totalRatio() { 
        $jchdConfig = $this->activity_jchd_model->getConfigByStatus(1);
        if(!$jchdConfig) {
            return ;
        }
        
        $plan = json_decode($jchdConfig['plan'], true);
        //胜平负处理  如果有其它玩法需要修改
        if($jchdConfig['join_num'] > 0) {
            $codes = $this->activity_jchd_model->getCountSpfCode($jchdConfig['theme_id'], $jchdConfig['issue']);
            foreach ($plan as $key => $match) {
                $sRatio = round($codes['code' . $key . '_s'] / $jchdConfig['join_num'] * 100);
                $pRatio = round($codes['code' . $key . '_p'] / $jchdConfig['join_num'] * 100);
                $fRatio = 100 - $sRatio - $pRatio;
                $fRatio = $fRatio < 0 ? 0 : $fRatio;
                $plan[$key]['ratio'] = $sRatio . '%,' . $pRatio . '%,' . $fRatio . '%';
            }
            
            $this->activity_jchd_model->updateConfig($jchdConfig['id'], array('plan' => json_encode($plan)));
        }
        
        //如果已过结束时间  更新状态
        if($jchdConfig['end_time'] <= date('Y-m-d H:i:s')) {
            $this->activity_jchd_model->updateConfig($jchdConfig['id'], array('status' => '2'));
        }
    }
    
    /**
     * 更新比赛信息和奖金信息
     * @return void|boolean
     */
    public function updateMatch() {
        $jchdConfig = $this->activity_jchd_model->getConfigByStatus(2);
        if(!$jchdConfig) {
            return ;
        }
        $this->activity_jchd_model->trans_start();
        
        $plan = json_decode($jchdConfig['plan'], true);
        $now = time();
        $scoreNum = 0;
        foreach ($plan as $key => $value) {
            //比赛时间加120分钟后开始获取比分
            if(strtotime("+120 minute", strtotime($value['dt'])) <= $now && empty($value['score'])) {
                $match = $this->activity_jchd_model->getMatchScore($value['mid']);
                if(!empty($match['full_score']) && (!isset($value['flag']))) {
                    $plan[$key]['score'] = $match['full_score'];
                    $scores = explode(':', $match['full_score']);
                    if($scores[0] > $scores[1]) {
                        //主胜
                        $res = $this->activity_jchd_model->updateWinNum($jchdConfig['theme_id'], $jchdConfig['issue'], 'code' . $key, 3);
                    } elseif ($scores[0] == $scores[1]) {
                        //平
                        $res = $this->activity_jchd_model->updateWinNum($jchdConfig['theme_id'], $jchdConfig['issue'], 'code' . $key, 1);
                    } else {
                        //负
                        $res = $this->activity_jchd_model->updateWinNum($jchdConfig['theme_id'], $jchdConfig['issue'], 'code' . $key, 0);
                    }
                    
                    if(!$res)
                    {
                        $this->activity_jchd_model->trans_rollback();
                        return false;
                    }
                    //更新标识
                    $plan[$key]['flag'] = 1;
                }
            }
            
            if(!empty($plan[$key]['score'])) {
                $scoreNum ++;
            }
        }
        
        //是否可算奖
        if(count($plan) == $scoreNum) {
            $cpstate = 1;
            $bounsNum = $this->activity_jchd_model->getWinCounts($jchdConfig['theme_id'], $jchdConfig['issue'], $scoreNum);
            if($bounsNum == 0) {
                $bouns = $jchdConfig['money'];
                $bouns_num = 0;
            } else {
                $bouns = floor($jchdConfig['money'] / $bounsNum);
                $bouns_num = $bounsNum;
            }
            $res1 = $this->activity_jchd_model->updateConfig($jchdConfig['id'], array('plan' => json_encode($plan), 'bouns_num' => $bouns_num, 'bouns' =>$bouns, 'cpstate' => $cpstate));
            if(!$res1)
            {
                $this->activity_jchd_model->trans_rollback();
                return false;
            }
            //更新打败人数和打败率
            if($scoreNum == '4') {
                //对4场打败人数和打败比例
                $num1 = $this->activity_jchd_model->getJoinCounts($jchdConfig['theme_id'], $jchdConfig['issue'], 4);
                $data = array(
                    'defeat_num' => $num1,
                    'defeat_ratio' => round($num1 / $jchdConfig['join_num'], 2) * 100,
                    'show_status' => 1,
                );
                $re1 = $this->activity_jchd_model->updateDefeatNum($jchdConfig['theme_id'], $jchdConfig['issue'], 4, $data);
                //对3场
                $num2 = $this->activity_jchd_model->getJoinCounts($jchdConfig['theme_id'], $jchdConfig['issue'], 3);
                $data = array(
                    'defeat_num' => $num2,
                    'defeat_ratio' => round($num2 / $jchdConfig['join_num'], 2) * 100,
                    'show_status' => 2,
                );
                $re2 = $this->activity_jchd_model->updateDefeatNum($jchdConfig['theme_id'], $jchdConfig['issue'], 3, $data);
                //对2场
                $num3 = $this->activity_jchd_model->getJoinCounts($jchdConfig['theme_id'], $jchdConfig['issue'], 2);
                $data = array(
                    'defeat_num' => $num3,
                    'defeat_ratio' => round($num3 / $jchdConfig['join_num'], 2) * 100,
                    'show_status' => 2,
                );
                $re3 = $this->activity_jchd_model->updateDefeatNum($jchdConfig['theme_id'], $jchdConfig['issue'], 2, $data);
                //对1场
                $num4 = $this->activity_jchd_model->getJoinCounts($jchdConfig['theme_id'], $jchdConfig['issue'], 1);
                $data = array(
                    'defeat_num' => $num4,
                    'defeat_ratio' => round($num4 / $jchdConfig['join_num'], 2) * 100,
                    'show_status' => 2,
                );
                $re4 = $this->activity_jchd_model->updateDefeatNum($jchdConfig['theme_id'], $jchdConfig['issue'], 1, $data);
                //全黑
                $data = array(
                    'defeat_num' => 0,
                    'defeat_ratio' => 0,
                    'show_status' => 3,
                );
                $re5 = $this->activity_jchd_model->updateDefeatNum($jchdConfig['theme_id'], $jchdConfig['issue'], 0, $data);
                if(!($re1 && $re2 && $re3 && $re4 && $re5)) {
                    $this->activity_jchd_model->trans_rollback();
                    return false;
                }
            } elseif($scoreNum == '3') {
                //对3场打败人数和打败比例
                //对3场
                $num2 = $this->activity_jchd_model->getJoinCounts($jchdConfig['theme_id'], $jchdConfig['issue'], 3);
                $data = array(
                    'defeat_num' => $num2,
                    'defeat_ratio' => round($num2 / $jchdConfig['join_num'], 2) * 100,
                    'show_status' => 1,
                );
                $re2 = $this->activity_jchd_model->updateDefeatNum($jchdConfig['theme_id'], $jchdConfig['issue'], 3, $data);
                //对2场
                $num3 = $this->activity_jchd_model->getJoinCounts($jchdConfig['theme_id'], $jchdConfig['issue'], 2);
                $data = array(
                    'defeat_num' => $num3,
                    'defeat_ratio' => round($num3 / $jchdConfig['join_num'], 2) * 100,
                    'show_status' => 2,
                );
                $re3 = $this->activity_jchd_model->updateDefeatNum($jchdConfig['theme_id'], $jchdConfig['issue'], 2, $data);
                //对1场
                $num4 = $this->activity_jchd_model->getJoinCounts($jchdConfig['theme_id'], $jchdConfig['issue'], 1);
                $data = array(
                    'defeat_num' => $num4,
                    'defeat_ratio' => round($num4 / $jchdConfig['join_num'], 2) * 100,
                    'show_status' => 2,
                );
                $re4 = $this->activity_jchd_model->updateDefeatNum($jchdConfig['theme_id'], $jchdConfig['issue'], 1, $data);
                //全黑
                $data = array(
                    'defeat_num' => 0,
                    'defeat_ratio' => 0,
                    'show_status' => 3,
                );
                $re5 = $this->activity_jchd_model->updateDefeatNum($jchdConfig['theme_id'], $jchdConfig['issue'], 0, $data);
                if(!($re2 && $re3 && $re4 && $re5)) {
                    $this->activity_jchd_model->trans_rollback();
                    return false;
                }
            } elseif($scoreNum == '2') {
                //对2场打败人数和打败比例
                $num3 = $this->activity_jchd_model->getJoinCounts($jchdConfig['theme_id'], $jchdConfig['issue'], 2);
                $data = array(
                    'defeat_num' => $num3,
                    'defeat_ratio' => round($num3 / $jchdConfig['join_num'], 2) * 100,
                    'show_status' => 1,
                );
                $re3 = $this->activity_jchd_model->updateDefeatNum($jchdConfig['theme_id'], $jchdConfig['issue'], 2, $data);
                //对1场
                $num4 = $this->activity_jchd_model->getJoinCounts($jchdConfig['theme_id'], $jchdConfig['issue'], 1);
                $data = array(
                    'defeat_num' => $num4,
                    'defeat_ratio' => round($num4 / $jchdConfig['join_num'], 2) * 100,
                    'show_status' => 2,
                );
                $re4 = $this->activity_jchd_model->updateDefeatNum($jchdConfig['theme_id'], $jchdConfig['issue'], 1, $data);
                //全黑
                $data = array(
                    'defeat_num' => 0,
                    'defeat_ratio' => 0,
                    'show_status' => 3,
                );
                $re5 = $this->activity_jchd_model->updateDefeatNum($jchdConfig['theme_id'], $jchdConfig['issue'], 0, $data);
                if(!($re3 && $re4 && $re5)) {
                    $this->activity_jchd_model->trans_rollback();
                    return false;
                }
            } else {
                //对1场打败人数和打败比例
                $num4 = $this->activity_jchd_model->getJoinCounts($jchdConfig['theme_id'], $jchdConfig['issue'], 1);
                $data = array(
                    'defeat_num' => $num4,
                    'defeat_ratio' => round($num4 / $jchdConfig['join_num'], 2) * 100,
                    'show_status' => 1,
                );
                $re4 = $this->activity_jchd_model->updateDefeatNum($jchdConfig['theme_id'], $jchdConfig['issue'], 1, $data);
                //全黑
                $data = array(
                    'defeat_num' => 0,
                    'defeat_ratio' => 0,
                    'show_status' => 3,
                );
                $re5 = $this->activity_jchd_model->updateDefeatNum($jchdConfig['theme_id'], $jchdConfig['issue'], 0, $data);
                if(!($re4 && $re5)) {
                    $this->activity_jchd_model->trans_rollback();
                    return false;
                }
            }
            
        } else {
            //未开奖 有更新 则执行更新
            if(md5($jchdConfig['plan']) != md5(json_encode($plan))) {
                $res1 = $this->activity_jchd_model->updateConfig($jchdConfig['id'], array('plan' => json_encode($plan)));
                if(!$res1)
                {
                    $this->activity_jchd_model->trans_rollback();
                    return false;
                }
            }
        }
        
        $this->activity_jchd_model->trans_complete();
    }
    
    /**
     * 派奖
     */
    public function sendPrice() {
        $this->activity_jchd_model->sendPrice();
    }
    
    /**
     * 排名计算
     */
    public function rank() {
        $this->activity_jchd_model->rank();
    }
    
    //排名测试
    public function testSendPrice($issue = 2) {
        $this->activity_jchd_model->testSendPrice($issue);
    }
    
    //排名测试
    public function testrank($issue = 1) {
        $this->activity_jchd_model->testrank($issue);
    }
}