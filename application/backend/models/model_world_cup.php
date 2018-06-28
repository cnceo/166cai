<?php
// +----------------------------------------------------------------------
// | Created by  PhpStorm.
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2018 上海彩咖网络科技有限公司.
// +----------------------------------------------------------------------
// | Create Time (2018/4/19-16:25)
// +----------------------------------------------------------------------
// | Author: 唐轶俊 <tangyijun@km.com>
// +----------------------------------------------------------------------
// | 世界杯竞猜活动
// +----------------------------------------------------------------------
class model_world_cup extends MY_Model{
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * @return mixed
     * 获取最大期次
     */
    public function getMaxIssue(){
        $sql = "select max(issue) as max_issue from cp_activity_jchd_config";
        return $this->master->query($sql)->getRow();
    }

    /**
     * @return mixed
     * 获取主题
     */
    public function getActivityTheme(){
        $sql = "select `id`,`name` from `cp_activity_jchd_theme` where `delete_flag` = 0";
        return $this->BcdDb->query($sql)->getAll();
    }

    /**
     * @param $data
     * @return mixed
     * 插入活动配置
     */
    public function insertConfig($data){
        $sql = "insert into `cp_activity_jchd_config`(theme_id,issue,money,plan,start_time,end_time,award_time,created) values (?,?,?,?,?,?,?,now())";
        return $this->master->query($sql,array($data['theme_id'],$data['issue'],$data['money'],$data['plan'],$data['start_time'],$data['end_time'],$data['award_time']));
    }

    /**
     * @param $max_id
     * @return mixed
     */
    public function getConfigByMax($max_id){
        $sql = "select end_time from cp_activity_jchd_config where issue = $max_id";
        return $this->master->query($sql)->getRow();
    }

    /**
     * @param $page
     * @param int $page_num
     * @return array
     * 获取活动配置列表
     */
    public function getConfigList($page = 1,$page_num = 1,$search = ''){
        $where = 'where 1';
        $limit = ($page - 1) * $page_num.','.$page_num;
        if($search['start_time'] && empty($search['end_time'])){
            $where .= " and c.start_time >= '{$search['start_time']}'";
        }
        if($search['end_time'] && empty($search['start_time'])){
            $where .= " and c.end_time  <= '{$search['end_time']}'";
        }
        if($search['start_time'] && $search['end_time']){
            $where .= " and c.start_time >= '{$search['start_time']}' and c.end_time <= '{$search['end_time']}'";
        }
        if($search['theme_id']){
            $where .= " and c.theme_id = {$search['theme_id']}";
        }

        if($search['issue']){
            $where .= " and c.issue = {$search['issue']}";
        }

        $sql = "select c.*,t.name  from cp_activity_jchd_config as c left join cp_activity_jchd_theme as t on c.theme_id = t.id {$where} order by c.start_time desc limit {$limit} ";
        $list =  $this->BcdDb->query($sql)->row_array();
        $sql = "select count(*) as `count` from cp_activity_jchd_config as c {$where}";
        $count = $this->BcdDb->query($sql)->row();
        return array(
            $list,
            $count->count
        );
    }

    //是否是正确的手机号
    function isMobile($mobile) {
        if (!is_numeric($mobile)) {
            return false;
        }
        return preg_match('#^13[\d]{9}$|^14[5,7]{1}\d{8}$|^15[^4]{1}\d{8}$|^17[0,6,7,8]{1}\d{8}$|^18[\d]{9}$#', $mobile) ? true : false;
    }
    /**
     * @param int $page
     * @param int $page_num
     * @param string $search
     * @return array
     * 获取参与竞猜记录
     */
    public function getGuessingJoinList($page = 1,$page_num = 1,$search = ''){
        $where = 'where 1';
        $limit = ($page - 1) * $page_num.','.$page_num;
        if($search['start_time'] && empty($search['end_time'])){
            $where .= " and c.start_time >= '{$search['start_time']}'";
        }
        if($search['end_time'] && empty($search['start_time'])){
            $where .= " and c.end_time  <= '{$search['end_time']}'";
        }
        if($search['start_time'] && $search['end_time']){
            $where .= " and c.start_time >= '{$search['start_time']}' and c.end_time <= '{$search['end_time']}'";
        }
        if($search['theme_id']){
            $where .= " and a.theme_id = {$search['theme_id']}";
        }
        if($search['issue']){
            $where .= " and c.issue = {$search['issue']}";
        }
        if($search['uname']){
            $where .= " and (u.uname = '{$search['uname']}' or i.phone = '{$search['uname']}')";
        }
        if($this->emp($search['status'])){
            $where .= " and a.status = {$search['status']}";
        }
        if($this->emp($search['platform'])){
            $where .= " and a.platform = {$search['platform']}";
        }
        //查询数据
        $sql = "select a.*,u.uname,t.name,c.bouns,c.plan  from  cp_activity_jchd_join  as a 
            left join cp_user as u on a.uid = u.uid 
            left join cp_activity_jchd_theme as t on a.theme_id = t.id
            left join cp_activity_jchd_config as c on a.issue = c.issue 
            left join cp_user_info as i on a.uid = i.uid
            {$where} order by created desc limit {$limit}";
        $list = $this->BcdDb->query($sql)->row_array();
        //统计所有数据
        $sql = "select count(*) as `count`  from  cp_activity_jchd_join  as a 
            left join cp_user as u on a.uid = u.uid 
            left join cp_activity_jchd_theme as t on a.theme_id = t.id
            left join cp_activity_jchd_config as c on a.issue = c.issue 
            left join cp_user_info as i on a.uid = i.uid
            {$where}";
        $count = $this->BcdDb->query($sql)->row();
        return array(
            $list,
            $count->count
        );
    }

    /**
     * @param int $page
     * @param int $page_num
     * @param string $search
     * @return array
     * 获取用户竞猜脚本
     */
    public function getRankList($page = 1,$page_num = 1,$search = ''){
        $where = 'where 1';
        $limit = ($page - 1) * $page_num.','.$page_num;
        if(!$search['theme_id']){
            $where .= " and  r.theme_id = (select max(theme_id) from cp_activity_jchd_rank)";
        }else{
            $where .= " and r.theme_id = {$search['theme_id']}";
        }
        if($search['uname']){
            $where .= " and (u.uname = '{$search['uname']}' or i.phone = '{$search['uname']}')";
        }
        $sql = "select r.*,u.uname,t.name from cp_activity_jchd_rank as r  
                left join cp_user as u on r.uid = u.uid 
                left join cp_activity_jchd_theme as t  on r.theme_id = t.id 
                left join cp_user_info as i on r.uid = i.uid
                {$where}  ORDER BY  rank ASC  limit {$limit}";

        $list = $this->BcdDb->query($sql)->row_array();

        $sql = "select count(*) as `count` from cp_activity_jchd_rank as r  
            left join cp_user as u on r.uid = u.uid  left join cp_user_info as i on r.uid = i.uid {$where}";

        $count = $this->BcdDb->query($sql)->row();
        return array(
            $list,
            $count->count
        );
    }

    /**
     * 获取世界杯赛程信息
     *
     * @return array
     */
    public function getWorldCupCourse()
    {
        $sql = 'select * from cp_worldcup_course order by number';
        $info = $this->BcdDb->query($sql)->getAll();
        return $info;
    }

    /**
     * 修改世界杯赛信息的字段值
     *
     * @param $param 关联数组
     * @return bool|number
     */
    public function alterCourseField($param)
    {
        $this->master->where('id', $param['id']);
        unset($param['id']);
        $tag = $this->master->update('cp_worldcup_course', $param);
        return $tag;
    }
}