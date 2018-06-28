<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
// +----------------------------------------------------------------------
// | Created by  PhpStorm.
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2018 上海彩咖网络科技有限公司.
// +----------------------------------------------------------------------
// | Create Time (2018/4/13-9:54)
// +----------------------------------------------------------------------
// | Author: 唐轶俊 <tangyijun@km.com>
// +----------------------------------------------------------------------
// | 中奖排行榜模型
// +----------------------------------------------------------------------
class win_rank_model extends MY_Model {
    /**
     * @param $plid
     * @param $pissue
     * @param int $start
     * @param int $show_num
     * @param string $is_history
     * @return mixed
     */
    public function getWinRankUser($plid,$pissue,$start = 0,$show_num = 20,$is_history = '', $maxRank = 0){
        $cons = ($maxRank > 0 ) ? " AND rankId <= {$maxRank}" : "";
        $limit = $start.','.$show_num;
        if($is_history == 'history'){
            $sql = "select * from cp_win_rank_user where  uid > 0 and  plid = {$plid}  and pissue = {$pissue} and addMoney > 0{$cons}  order by rankId asc  limit {$limit}";
        }else{
            $sql = "select * from cp_win_rank_user where  uid > 0 and  plid = {$plid}  and pissue = {$pissue}{$cons} order by rankId asc  limit {$limit}";
        }
        return  $this->slave->query($sql)->getAll();
    }

    /**
     * @param $uid
     * @param $plid
     * @param $pissue
     * @return mixed
     * 根据用户uid获取排行版1条
     */
    public function getIsRank($uid,$plid,$pissue){
        $sql = "select * from cp_win_rank_user where uid = {$uid} and plid = {$plid} and pissue = {$pissue}";
        return $this->slave->query($sql)->getRow();
    }

    /**
     * @param $plid
     * @param $pissue
     * @return mixed
     * 查看该期、该彩种配置
     */
    public function getConfigRow($plid,$pissue){
        $sql = "select * from cp_win_rank_config where plid = {$plid} and pissue = {$pissue}";
        return $this->slave->query($sql)->getRow();
    }

    /**
     * @param $uid
     * @param $start_time
     * @param $end_time
     * @param $lids
     * @return mixed
     * 查询用户在活动期间的总中奖金额
     */
    public function getWindCount($uid,$start_time,$end_time,$lids){
        $sql = "SELECT uid, SUM(margin) AS tmargin FROM cp_orders FORCE INDEX (created) WHERE created >= '{$start_time}' AND created <= '{$end_time}' AND lid in ({$lids}) AND  `status` = 2000  and uid = {$uid}";
        return $this->slave->query($sql)->getRow();
    }

    /**
     * @param $plid
     * @return mixed
     */
    public function getMaxPissue($plid){
        $sql = "select max(pissue) as max_pissue from cp_win_rank_config where plid = {$plid} ";
        return $this->slave->query($sql)->getRow();
    }

    /**
     * @param $uid
     * @param $plid
     * @param $pissue
     * @return mixed
     * 获取累计中奖信息
     */
    public function getContPrice($uid,$plid,$pissue){
        $sql = "select * from cp_win_rank_detail where uid = {$uid} and plid = {$plid} and pissue = {$pissue}";
        return $this->slave->query($sql)->getRow();
    }
    
    public function getJcMoney()
    {
        $sql = "select money from cp_activity_jchd_config where status = 1";
        return $this->slave->query($sql)->getRow();
    }
    
    public function getTopUser($plid, $pissue)
    {
        $sql = "select userName from cp_win_rank_user where plid=? and pissue=? and rankId = 1";
        return $this->slave->query($sql, array($plid, $pissue))->getRow();
    }
    
    public function getNewPissue($plid)
    {
        $sql = "select pissue as max_pissue,start_time,end_time from cp_win_rank_config where plid = {$plid} order by pissue desc limit 1";
        return $this->slave->query($sql)->getRow();
    }

    public function getHbGameStatus($uid)
    {
        $sql = "select uid from cp_worldcup_redpack_log where uid =? and status=1 limit 1";
        $user = $this->slave->query($sql, array($uid))->getRow();
        if(!empty($user)){
            return 'true';
        }else{
            return 'false';
        }
    }
    
    public function getDtGameStatus($uid)
    {
        $sql = "select uid from cp_win_question_user where uid =? limit 1";
        $user = $this->slave->query($sql, array($uid))->getRow();
        if(!empty($user)){
            return 'true';
        }else{
            return 'false';
        }
    }

    public function getJcGameStatus($uid)
    {
        $sql = "select uid from cp_activity_jchd_join where uid =? limit 1";
        $user = $this->slave->query($sql, array($uid))->getRow();
        if(!empty($user)){
            return 'true';
        }else{
            return 'false';
        }
    }
}