<?php
// +----------------------------------------------------------------------
// | Created by  PhpStorm.
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2018 上海彩咖网络科技有限公司.
// +----------------------------------------------------------------------
// | Create Time (2018/4/19-16:22)
// +----------------------------------------------------------------------
// | Author: 唐轶俊 <tangyijun@km.com>
// +----------------------------------------------------------------------
// | 世界杯竞猜活动
// +----------------------------------------------------------------------
if ( ! defined('BASEPATH')) {
    exit('No direct script access allowed');
}
class WorldCup extends MY_Controller{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('model_world_cup');
        $this->load->driver('cache', array('adapter' => 'redis'));
    }

    /**
     * 活动配置信息
     */
    public function index(){
        $this->check_capacity('4_10_1');
        $search = array(
            'theme_id' => $this->input->get('theme_id'),
            'issue'    => $this->input->get('issue'),
            'start_time'    => $this->input->get('start_time'),
            'end_time'    => $this->input->get('end_time'),
        );
        $page = intval($this->input->get("p"));
        $page = $page <= 1 ? 1 : $page;
        $result = $this->model_world_cup->getConfigList($page,self::NUM_PER_PAGE,$search);
        $pageConfig = array(
            "page" => $page,
            "npp" => self::NUM_PER_PAGE,
            "allCount" => $result[1]
        );
        $pages = get_pagination($pageConfig);
        $theme = $this->model_world_cup->getActivityTheme(); //主题
        $data = array(
            'theme' => $theme,
            'max_issue' => $this->getMaxIssue(),
            'list' => $result[0],
            'pages' => $pages,
            'search' => $search
        );
        $this->load->view('worldcap/index',$data);
    }

    /**
     * 获取竞猜记录
     */
    public function guessingRecord(){
        $this->check_capacity('4_10_2');
        $theme_id = $this->input->get('theme_id');
        $search = array(
            'uname'    => $this->input->get('uname'),
            'theme_id' => empty($theme_id) ? 1 : $theme_id,
            'issue'    => $this->input->get('issue'),
            'start_time'    => $this->input->get('start_time'),
            'end_time'    => $this->input->get('end_time'),
            'status'      => $this->input->get('status'),
            'platform'      => $this->input->get('platform'),
        );
        $page = intval($this->input->get("p"));
        $page = $page <= 1 ? 1 : $page;
        $theme = $this->model_world_cup->getActivityTheme(); //主题
        $result = $this->model_world_cup->getGuessingJoinList($page,self::NUM_PER_PAGE,$search);
        $pageConfig = array(
            "page" => $page,
            "npp" => self::NUM_PER_PAGE,
            "allCount" => $result[1]
        );
        $pages = get_pagination($pageConfig);
        $data = array(
            'theme' => $theme,
            'list' => $result[0],
            'pages' => $pages,
            'search' => $search
        );
        $this->load->view('worldcap/guessing',$data);
    }

    /**
     * 竞猜排名列表
     */
    public function rankList(){
        $this->check_capacity('4_10_3');
        $search = array(
            'uname'    => $this->input->get('uname'),
            'theme_id' => $this->input->get('theme_id'),
        );
        $page = intval($this->input->get("p"));
        $page = $page <= 1 ? 1 : $page;
        $theme = $this->model_world_cup->getActivityTheme(); //主题
        $result = $this->model_world_cup->getRankList($page,self::NUM_PER_PAGE,$search);
        $pageConfig = array(
            "page" => $page,
            "npp" => self::NUM_PER_PAGE,
            "allCount" => $result[1]
        );
        $pages = get_pagination($pageConfig);
        $data = array(
            'theme' => $theme,
            'list' => $result[0],
            'pages' => $pages,
            'search' => $search
        );
        $this->load->view('worldcap/ranklist',$data);
    }

    /**
     * 添加期次活动
     */
    public function addConfig(){
        $this->check_capacity('4_10_4', true);
        $data = $this->input->post();
        $check = $this->checkData($data); //验证期次
        if($check!==true){die($check);}
        $ronda_info = $this->getEncounterInfo(); //获取所有场次信息
        $arr = array();
        foreach ($data['plan'] as $k=>$v){
            if(!empty($v)){
                if(empty($ronda_info[$v])){
                    die($this->format('10020','【'.$v.'】竞猜场次不存在,请确认场次'));
                }else{
                    $arr[] = $ronda_info[$v];
                }
            }
        }
        $max_time_arr = array();
        $mids = array();
        foreach ($arr as $k=>$v){
            $plans[$k+1]['mid'] = $v['mid'];
            $plans[$k+1]['playType'] = 'spf';
            $plans[$k+1]['sp'] = $v['spfSp3'].','.$v['spfSp1'].','.$v['spfSp0'];
            $plans[$k+1]['ratio'] = '0%,0%,0%';
            $plans[$k+1]['home'] = $v['homeSname'];
            $plans[$k+1]['away'] = $v['awarySname'];
            $plans[$k+1]['score'] = '';
            $plans[$k+1]['dt'] = date('Y-m-d H:i:s',substr($v['dt'],0,10));
            $max_time_arr[] = $v['dt']; //存储比赛开始时间
            $mids[] = $v['mid'];
        }
        $data['plan'] = json_encode($plans);
        $data['money'] = intval($data['money']) * 100;
        $data['award_time'] = date('Y-m-d H:i:s',substr(max($max_time_arr),0,10) + 3600 * 4);
        $res = $this->model_world_cup->insertConfig($data);
        if(!$res){
            die($this->format('10020','数据库操作失败'));
        }
        
        $theme = array(1 => '世界杯竞猜');
        $midstr = implode(',', $mids);
        $money  = $data['money'] / 100;
        $msg = "主题：{$theme[$data['theme_id']]};期次：{$data['issue']};活动时间：{$data['start_time']} 至 {$data['end_time']};竞猜场次：{$midstr};当期奖金：{$money}元";
        $this->syslog(75, $msg);
        
        die($this->format('10000','添加活动配置成功'));
    }

    /**
     * @param $data
     * @return string
     * 验证数据
     */
    public function checkData($data){
        if(!$this->checkIssue($data['issue'])){
            return $this->format('10020','活动期次不正确');
        }
        //验证奖金
        if(!preg_match('/^[0-9][0-9]*$/', $data['money']) || $data['money'] < 0){
            return $this->format('10020','奖金格式不正确');
        }
        //验证竞猜场次
        foreach ($data['plan'] as $k => $v){
            if(empty($v)){
                unset($data['plan'][$k]);
            }
        }
        if(count($data['plan']) < 1){
            return $this->format('10020','至少添加一场竞猜场次');
        }

        if(count($data['plan']) > 4){
            return $this->format('10020','最多添加4长竞猜场次');
        }
        //验证期次是否重复
        if(count($data['plan']) !== count(array_unique($data['plan']))){
            return $this->format('10020','有重复的期次，请删除');
        }
        //验证时间
        if(strtotime($data['end_time']) <= (strtotime($data['start_time']) + 600)){
            return $this->format('10020','活动时间设置不正确');
        }
        //验证在该活动期间是否还有进行中的活动
        $max_issue = $this->getMaxIssue();
        $max_end_time  = $this->model_world_cup->getConfigByMax($max_issue['max_issue']);
        if($max_end_time){
            if(strtotime($data['start_time']) < strtotime($max_end_time['end_time'])){
                return $this->format('10020','活动开始时间必须大于最大期次活动的结束时间');
            }
        }
        return true;
    }

    /**
     * @param $code
     * @param $message
     * @param string $data
     * @return string
     * 返回参数格式化
     */
    public function format($code,$message,$data = ''){
        return json_encode(array('status' => $code,'message' => $message,'data' => $data));
    }

    /**
     * 获取最大期次
     */
    public function getMaxIssue(){
        $maxIssue = $this->model_world_cup->getMaxIssue();;
        if(empty($maxIssue['max_issue'])){
            $maxIssue['max_issue'] = 0;
        }
        return  $maxIssue;
    }

    /**
     * @param $max_issue
     * @return bool
     * 验证期次处理函数
     */
    public function checkIssue($max_issue){
        $maxIssue = $this->getMaxIssue();
        if($max_issue  != $maxIssue['max_issue'] + 1){
            return false;
        }
        return true;
    }


    /**
     * @return mixed
     * 获取对阵信息
     */
    public function getEncounterInfo(){
        $REDIS = $this->config->item('REDIS');
        $this->load->driver('cache', array('adapter' => 'redis'));
        $data =  json_decode($this->cache->redis->get($REDIS['JCZQ_MATCH']), TRUE);
        return $data;
    }

    /**
     * 世界杯赛程信息
     */
    public function course()
    {
        $this->check_capacity('4_12_1', true);
        $res = $this->model_world_cup->getWorldCupCourse();
        $param = array(
            'datas' => $res,
        );
        $this->load->view('worldcap/course', $param);
    }

    /**
     * 世界杯赛程修改字段
     */
    public function alter()
    {
        $this->check_capacity('4_12_2', true);
        $data = $this->input->post('data', true);
        $datas = json_decode($data, true);
        $tag = TRUE;
        if(empty($datas))
        {
            echo $this->ajaxReturn('y', '');
            exit();
        } else {
            foreach ($datas as $one) {
                if (count($one) >= 2) {
                    $flag = $this->model_world_cup->alterCourseField($one);
                    $flag || $tag = FALSE;
                }
            }
        }

        if ($tag === TRUE) {
            $courseInfo = $this->model_world_cup->getWorldCupCourse();
            $this->cache->redis->save('worldcup_course', json_encode($courseInfo), 0);
            $this->syslog(78, '世界杯赛场接口修改赛程信息/赛事资讯');
            echo $this->ajaxReturn('y', '');
        }
        echo $this->ajaxReturn('n', '修改失败，请稍后再试');
    }
}