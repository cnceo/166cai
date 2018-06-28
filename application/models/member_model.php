<?php

class Member_Model extends MY_Model {

    public function __construct() 
    {
        parent::__construct();
        $this->load->library('tools');
    }
    /**
     * [getMemberInfoByUid 获取会员信息]
     * @author LiKangJian 2017-12-26
     * @param  [type] $uid [description]
     * @return [type]      [description]
     */
    public function getMemberInfoByUid($uid)
    {
        $sql = "SELECT g.grade,g.grade_name,g.value_start,g.value_end,g.privilege,u.* FROM cp_user_growth as u left join cp_growth_level as g on g.grade = u.grade where u.uid = ?";
        return $this->db->query( $sql,array($uid) )->getRow();
    }
    /**
     * [getGrowthLevel 成长等级]
     * @author LiKangJian 2017-12-26
     * @return [type] [description]
     */
    public function getGrowthLevel()
    {
        $sql  = 'SELECT id,grade,grade_name,value_start,value_end,privilege FROM cp_growth_level order by id asc';
        return $this->db->query( $sql)->getAll();
    }
    /**
     * [getPointJob 积分任务]
     * @author LiKangJian 2017-12-26
     * @return [type] [description]
     */
    public function getPointJob()
    {
        $sql  = "SELECT * FROM `cp_points_jobs` as j where j.is_delete = 0 order by j.hot desc,j.sort asc ";
        return $this->db->query( $sql)->getAll();
    }
    /**
     * [getListData 积分明细]
     * @author LiKangJian 2018-01-05
     * @param  [type] $uid        [description]
     * @param  [type] $searchData [description]
     * @param  [type] $page       [description]
     * @param  [type] $pageCount  [description]
     * @return [type]             [description]
     */
    public function getListData($uid,$searchData,$page, $pageCount)
    {

        $where = " where l.uid = ?  ";
        if($searchData['date'] ==1)
        {
            $where .= ' and  DATE_SUB(CURDATE(), INTERVAL 1 MONTH) <= date(l.created) ';
        }else if($searchData['date'] ==2){
            $where .= ' and  DATE_SUB(CURDATE(), INTERVAL 3 MONTH) <= date(l.created) ';
        }else if($searchData['date'] ==3){
            $where .= ' and  DATE_SUB(CURDATE(), INTERVAL 6 MONTH) <= date(l.created) ';
        }else{
            $where .= ' and  DATE_SUB(CURDATE(), INTERVAL 1 YEAR) <= date(l.created) ';
        }
        //
        $data = $this->getRes($where,array($uid),$page, $pageCount);
        if($searchData['ctype']!='')
        {
            $where .= " and l.ctype = ? ";
            $data = $this->getRes($where,array($uid,$searchData['ctype']),$page, $pageCount);
        }
        return $data;
    }
    /**
     * [getRes 获取查询结果]
     * @author LiKangJian 2018-01-05
     * @param  [type] $where     [description]
     * @param  [type] $data      [description]
     * @param  [type] $page      [description]
     * @param  [type] $pageCount [description]
     * @return [type]            [description]
     */
    private function getRes($where,$data,$page, $pageCount)
    {
        $field = 'l.id,l.uid,l.value,l.mark,l.ctype,l.value,l.trade_no,l.orderId,l.subscribeId,l.uvalue,l.status,l.content,l.created';
        $selectSql = "SELECT $field FROM `cp_points_logs` as l $where  order by l.created desc  LIMIT " . ($page - 1) * $pageCount . "," . $pageCount;
        $countSql = "SELECT sum(value) as s,count(mark) as c,mark as m FROM `cp_points_logs` as l $where   group by l.mark";
        $res = $this->slave->query($selectSql,$data)->getAll();
        $count = $this->slave->query($countSql,$data)->getAll();
        return array('res'=>$res,'count'=>$count);
    }
    /**
     * [getExchangeRedPack 获取兑换红包]
     * @author LiKangJian 2018-01-05
     * @return [type] [description]
     */
    public  function getExchangeRedPack()
    {
        $sql = "SELECT s.rid,s.today_out,s.next_out,s.already_out,r.use_params,r.p_name,r.c_name,r.use_desc,r.refund_desc,r.money 
        FROM cp_redpack_stock as s left join cp_redpack as r on s.rid = r.id where r.aid = 10 ";
        return $this->db->query( $sql )->getAll();
    }
    /**
     * [getRedPackInfo 获取红包兑换信息]
     * @author LiKangJian 2017-12-28
     * @param  [type] $rid [description]
     * @return [type]      [description]
     */
    public function getRedPackInfo($rid)
    {
        $sql = "SELECT s.rid,s.today_out,s.next_out,s.already_out,r.use_params,r.p_name,r.c_name,r.use_desc,r.refund_desc,r.money 
                FROM cp_redpack_stock as s 
                left join cp_redpack as r 
                on s.rid = r.id 
                where r.aid = 10 and r.id= ?";
        return $this->db->query( $sql ,array($rid))->getRow();
    }
    /**
     * [getExchangeTime 获取当时红包领取的次数]
     * @author LiKangJian 2017-12-28
     * @param  [type] $uid [description]
     * @return [type]      [description]
     */
    public function getExchangeTime($uid)
    {
        $start_time = date('Y-m-d').' 00:00:00';
        $end_time = date('Y-m-d').' 23:59:59';
        $sql = "SELECT count(id) as c FROM cp_points_logs where ctype='3' and uid = ? and created>='{$start_time}' and created<='{$end_time}' ";
        return $this->db->query( $sql,array($uid) )->getOne();
    } 
    /**
     * [exchangeRedPack 红包兑换过程]
     * @author LiKangJian 2017-12-28
     * @param  [type] $rid [description]
     * @param  [type] $uid [description]
     * @return [type]      [description]
     */
    public function exchangeRedPack($rid,$uid)
    {
        $this->db->trans_start();
        //加锁锁住rid的库存
        $redInfo =  $this->db->query("SELECT s.rid,s.today_out,s.next_out,s.already_out,r.use_params,r.p_name,r.c_name,r.use_desc,r.refund_desc,r.money 
                FROM cp_redpack_stock as s 
                left join cp_redpack as r 
                on s.rid = r.id 
                where r.aid = 10 and r.id= ? for update", array($rid))->getRow();
        //积分不足
        $redInfo['use_params'] = json_decode($redInfo['use_params'],true);
        //取得用户信息
        $uinfo = $this->db->query("SELECT * FROM cp_user_growth where uid = ?",array($uid))->getRow();
        if( $redInfo['use_params']['lv'.$uinfo['grade']]=='--' || $uinfo['points'] < $redInfo['use_params']['lv'.$uinfo['grade']] )
        {
            $this->db->trans_rollback();
            return  array('code'=>5,'msg'=>'您的积分不足，投注可以攒积分哦');
        }
        //查询库存
        if($redInfo['today_out']==$redInfo['already_out'])
        {
            $this->db->trans_rollback();
            return  array('code'=>6,'msg'=>'红包已经抢光啦，每天0点会更新哦');
        }
        //兑换次数
        $count = $this->getExchangeTime($uinfo['uid']);
        if($count>=3)
        {
            $this->db->trans_rollback();
            return  array('code'=>7,'msg'=>'您好，每天兑换红包最多3次');
        }
        $money  = ParseUnit($redInfo['money'], 1);
        $points = $redInfo['use_params']['lv'.$uinfo['grade']]=='--'?$redInfo['use_params']['price'] : $redInfo['use_params']['lv'.$uinfo['grade']];
        $currPoints = $uinfo['points'] - $points;
        //写入更新数据
        //1.写入红包
        $insertlog = 'insert into cp_redpack_log (`aid`,`platform_id`,`uid`,`rid`,`valid_start`,`valid_end`,`get_time`,`status`,`created`) values (?,?,?,?,?,?,?,?,now())';
        $tag = $this->db->query($insertlog,array(10,1,$uinfo['uid'],$rid,date('Y-m-d H:i:s'),date("Y-m-d h:i:s",strtotime("+10 year")),date('Y-m-d H:i:s'),1));
        //插入后返回的Id
        $insert_id = $this->db->insert_id();
        //更新成长表中兑换次数
        $last_year_points = $uinfo['last_year_points'] - $points;
        $last_year_points = $last_year_points < 0 ? 0 : $last_year_points ;
        $updateSql = "update cp_user_growth set points=points-$points,last_year_points=$last_year_points where uid =?";
        $tag1 = $this->db->query($updateSql,array($uinfo['uid']));

        //更新库存
        $updateRedpack = "update cp_redpack_stock set already_out= already_out+1 where rid = ?;";
        $tag2 = $this->db->query($updateRedpack,array($rid));
        //写入积分流水 积分减少
        $trade_no = $this->tools->getIncNum('UNIQUE_KEY');
        $insert = 'insert into cp_points_logs (`uid`,`value`,`mark`,`trade_no`,`ctype`,`uvalue`,`overTime`,`content`,`rid`,`created`) values (?,?,?,?,?,?,?,?,?,now())';
        $tag3 = $this->db->query($insert,array($uinfo['uid'],$points,'0',$trade_no,3,$currPoints,date('Y-m-d H:i:s'),'兑换'.$money.'元红包',$insert_id)); 
        //更新用户money dispath 
        //$updateUser = "update cp_user set money=money+?,dispatch=dispatch+? where uid = ?";
        //$tag4 = $this->db->query($updateUser,array($redInfo['money'],$redInfo['money'],$uinfo['uid']));

        if($tag && $tag1 && $tag2 && $tag3)
        {
            $this->db->trans_complete();
            return  array('code'=>200,'msg'=>'恭喜您，兑换<span style="color:#f00;">'.$money.'</span>元红包成功');
        }else{
            $this->db->trans_rollback();
            return  array('code'=>8,'msg'=>'兑换失败');
        }
    }
    /**
     * [insertLog 任务领取]写入记录]
     * @author LiKangJian 2018-01-05
     * @param  [type] $jid   [description]
     * @param  [type] $type  [description]
     * @param  [type] $uid   [description]
     * @return [type]        [description]
     */
    public function insertLog($jid,$type,$uid)
    {
        $this->db->trans_start();
        $status =  $this->db->query("select my_task_get(points_job_params, ?, ?) as status from cp_user_growth  where uid = ? for update", array($jid,$type,$uid))->getOne();
        if($status==0)
        {
            $this->db->trans_rollback();
            return  array('code'=>4,'msg'=>'您好，任务未完成');
        }else if($status==2)
        {
            $this->db->trans_rollback();
            return  array('code'=>5,'msg'=>'您好，积分已经领取过啦');
        }else{
            //取得用户信息
            $uinfo = $this->db->query("SELECT * FROM cp_user_growth where uid = ?",array($uid))->getRow();
            //查询任务积分 
            $job = $this->getJobPoint($jid);
            $trade_no = $this->tools->getIncNum('UNIQUE_KEY');
            $content = '完成任务：'.$job['title'];
            $currPoints = $uinfo['points'] + $job['value'];
            //写入流水
            $insert = 'insert into cp_points_logs (`uid`,`value`,`mark`,`trade_no`,`ctype`,`uvalue`,`overTime`,`content`,`cvalue`,`created`) values (?,?,?,?,?,?,?,?,?,now())';
            $tag = $this->db->query($insert,array($uinfo['uid'],$job['value'],'1',$trade_no,1,$currPoints,date('Y-m-d H:i:s'),$content,$jid) );
            //更新积分
            $updateSql = "update cp_user_growth set points=points+{$job['value']},points_job_params = my_task_set(points_job_params, ?, ?) where uid =?";
            $tag1 = $this->db->query($updateSql,array($jid,2,$uinfo['uid']));
            if($tag && $tag1)
            {
                $this->db->trans_complete();
                return  array('code'=>200,'msg'=>'恭喜您，成功领取<span style="color:#f00;">'.$job['value'].'</span>积分');
            }else{
                $this->db->trans_rollback();
                return  array('code'=>6,'msg'=>'您好，积分领取失败');
            }
        }


    }
    /**
     * [getJobPoint 查询任务积分]
     * @author LiKangJian 2018-01-02
     * @param  [type] $id [description]
     * @return [type]     [description]
     */
    public function getJobPoint($id)
    {
        $sql =  "select value,title from cp_points_jobs where id = ?";
        return $this->db->query($sql,array($id))->getRow();
    }
    /**
     * [redpackUse 使用红包]
     * @author LiKangJian 2018-01-09
     * @param  [type] $uid [description]
     * @param  [type] $rid [description]
     * @return [type]      [description]
     */
    public function redpackUse($uid,$rid)
    {
        $this->db->trans_start(); 
        $redPackSql = "select r.money as money from cp_redpack_log as l left join cp_redpack as r on r.id =l.rid where l.id =? and l.status =1 for update";
        $row = $this->db->query($redPackSql,array($rid))->getRow();
        if(!$row )
        {
            $this->db->trans_rollback();
            return  false;
        }
        $money = $row['money'];
        $updateUser = "update cp_user set money=money+?,dispatch=dispatch+? where uid = ?";
        $tag = $this->db->query($updateUser,array($money,$money,$uid));
        $umoney = $this->db->query("select money from cp_user where uid=? ",array($uid))->getOne();
        //更新红包表
        $updateSql = "update cp_redpack_log set status=2,use_time=? where id = ? and uid = ?";
        $tag1 = $this->db->query($updateSql,array(date('Y-m-d H:i:s'),$rid,$uid));
        //更新cp_wallet_logs
        $insert = 'insert into cp_wallet_logs (`uid`,`money`,`mark`,`trade_no`,`ctype`,`umoney`,`status`,`platform`,`channel`,`content`,`created`) values (?,?,?,?,?,?,?,?,?,?,now())';
        $tag2 = $this->db->query($insert,array($uid,$money,'1',$this->tools->getIncNum('UNIQUE_KEY'),9,$umoney,2,0,1,'红包'));
        if($tag && $tag1 && $tag2)
        {
            $this->db->trans_complete();
            return  true;
        }else{
            $this->db->trans_rollback();
            return  false;
        }    
    }
    /**
     * [getJobStatus 获取任务状态]
     * @author LiKangJian 2018-01-16
     * @param  [type] $jid  [description]
     * @param  [type] $type [description]
     * @param  [type] $uid  [description]
     * @return [type]       [description]
     */
    public function getJobStatus($jid,$type,$uid)
    {
        $sql = "select my_task_get(points_job_params, ?, ?) as status from cp_user_growth  where uid = ?";
        return $this->db->query($sql, array($jid,$type,$uid))->getOne();
    }
    
    public function getOnePoint($tradeNo)
    {
        $sql = "select * from cp_points_logs where trade_no = ? limit 1";
        return $this->db->query($sql, array($tradeNo))->getRow();
    }

}
