<?php

if (!defined('BASEPATH')) exit('No direct script access allowed');

class Point extends MY_Controller {

    /**
     * [__construct 积分商城]
     * @author LiKangJian 2017-12-25
     */
    public function __construct() 
    {
        parent::__construct();
        $this->load->model('member_model');
        $this->load->model('user_model');
    }
    /**
     * [index 积分首页]
     * @author LiKangJian 2017-12-29
     * @return [type] [description]
     */
	public function index()
    {
        //0 未完成 1 完成未领取 2 领取
        //冻结用户可以正常获得积分，但是无法消耗（兑换、抽奖）
        //$this->user_model->freshUserGrowth($this->uid);
        //$this->user_model->freshUserInfo($this->uid);
        $this->load->driver('cache', array('adapter' => 'redis'));
        $REDIS = $this->config->item('REDIS');
        $jfbanner = unserialize($this->cache->redis->get($REDIS['JIFEN']));
        $jfbanner = $jfbanner['jfbanner'];
        $this->checkLogin();
        $info = $this->uinfo;
        $jobs = $this->member_model->getPointJob();
        foreach ($jobs as $k => $v) 
        {
            $jobs[$k]['doStatus'] = $this->member_model->getJobStatus($v['id'],$v['type'],$this->uid);
        }
        $redpack = $this->member_model->getExchangeRedPack();
        $this->memberView('point/index', array('jobs'=>$jobs,'info'=>$info,'redpack'=>$redpack,'jfbanner'=>$jfbanner), 'v1.1');
    }
    /**
     * [lists 积分明细]
     * @author LiKangJian 2017-12-29
     * @return [type] [description]
     */
    public function lists()
    {
        $this->checkLogin();
        $page = intval($this->input->get("cpage"));
        $page = $page <= 1 ? 1 : $page;
        $date = $this->input->post("date", true);
        if($date===false)
        {
            $date = 1;
        }
        $ctype = $this->input->post("ctype", true);
        $searchData = array('ctype'=>$ctype,'date'=>$date);
        $log = $this->member_model->getListData($this->uid,$searchData,$page, 10);
        $count = 0;
        $counts = array();
        foreach ($log['count'] as $k => $v) 
        {
            $count+=$v['c'];
            $counts[$v['m']] = $v;
        }
        $log['count'] = $counts;
        $pageConfig = array(
            "page"     => $page,
            "npp"      => 10,
            "allCount" => $count
        );
        $pdata['pagenum'] = ceil($count / 10);
        $pdata['cpnum'] = count($log['res']);
        $pdata['ajaxform'] = 'point-form';
        $pagestr = $this->load->view('v1.1/elements/common/pages3', $pdata, true);
        $vdata = array('list'=>$log,'page'=>$pagestr,'count'=>$count,'pageNum'=>$pdata['pagenum'],'search'=>$searchData,'cpnum'=>$pdata['cpnum']); 
        if($this->is_ajax)
        {
            echo $this->load->view('v1.1/point/list', $vdata, true);

        }else{
           $this->memberView('point/list', $vdata, 'v1.1');   
        }
        
        
    }
    /**
     * [help 积分帮助]
     * @author LiKangJian 2017-12-29
     * @return [type] [description]
     */
    public function help()
    {
        $this->memberView('point/help', $vdata, 'v1.1');
    }
    /**
     * [exchangeRedPack 红包兑换功能]
     * @author LiKangJian 2017-12-28
     * @return [type] [description]
     */
    public function exchangeRedPack()
    {
        $res = array('code'=>200,'msg'=>'');
        $rid = $this->input->post("rid", true);
        if(empty($this->uid))
        {
            $res['code'] = 3;
            $res['msg'] = '';
            echo json_encode($res);die;
        }
        if($this->uinfo['grade']<2)
        {
            $res['code'] = 4;
            $res['msg'] = '青铜及以上会员才可以兑换红包哦';
            echo json_encode($res);die;
        }
        //兑换红包 插入红包
        $res = $this->member_model->exchangeRedPack($rid,$this->uid);
        if($res['code']==200)
        {
            //刷新缓存
            $this->user_model->freshUserGrowth($this->uid);
        }
        echo json_encode($res);die;
    }
    /**
     * [getPoint 积分领取功能]
     * @author LiKangJian 2018-01-02
     * @return [type] [description]
     */
    public function getPoint()
    {
        $res = array('code'=>200,'msg'=>'');
        $jid = $this->input->post("jid", true);
        $type = $this->input->post("type", true);
        if(empty($this->uid))
        {
            $res['code'] = '3';
            $res['msg'] = '';
            echo json_encode($res);die;
        }
        $res = $this->member_model->insertLog($jid,$type,$this->uid);
        if($res['code']==200)
        {
            //刷新缓存
            $this->user_model->freshUserGrowth($this->uid);
        }
        echo json_encode($res);die;

    }
    /**
     * [redpackUse 彩金红包使用]
     * @author LiKangJian 2018-01-09
     * @return [type] [description]
     */
    public function redpackUse()
    {
        $res = array('code'=>400,'msg'=>'使用失败，红包不存在或已使用');
        if(empty($this->uid))
        {
            $res['code'] = '300';
            $res['msg'] = '';
            echo json_encode($res);die;
        }
        //验证实名
        if(!$this->uinfo['id_card'] || empty($this->uinfo['real_name']) || !$this->uinfo['phone'] || $this->uinfo['userStatus'] !=0)
        {
            $res['code'] = '500';
            $res['msg'] = '未实名';
            echo json_encode($res);die;
        }
        $rid = $this->input->post("rid", true);
        $tag = $this->member_model->redpackUse($this->uid,$rid);
        if($tag){
            $this->user_model->freshUserInfo($this->uid);
            echo json_encode(array('code'=>200,'msg'=>''));die;
        }else{
            echo json_encode($res);die;
        }
        
    }
    public function checkJobStatus()
    {
        if(empty($this->uid))
        {
            $res['code'] = '3';
            $res['status'] = '3';
            $res['msg'] = '';
            echo json_encode($res);die;
        }
        $jid = $this->input->post("jid", true);
        $type = $this->input->post("type", true);
        $status = $this->member_model->getJobStatus($jid,$type,$this->uid);
        $res = array('code'=>200,'status'=>$status,'msg'=>'');
        if($status==1)
        {
            $res = $this->member_model->insertLog($jid,$type,$this->uid);
            if($res['code']==200)
            {
                //刷新缓存
                $this->user_model->freshUserGrowth($this->uid);
            }
            $res['status'] = 1;
            echo json_encode($res);die;
        }
        
        echo json_encode($res);die;
    }
    /**
     * [checkLogin 验证登录]
     * @author LiKangJian 2017-12-26
     * @return [type] [description]
     */
    public function checkLogin()
    {
        if(empty($this->uid))
        {
            header('Location: /main/login');
        }
        return true;
    }


}