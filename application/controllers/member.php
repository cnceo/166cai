<?php

if (!defined('BASEPATH')) exit('No direct script access allowed');

class Member extends MY_Controller {

    /**
     * [__construct 会员中心]
     * @author LiKangJian 2017-12-25
     */
    public function __construct() 
    {
        parent::__construct();
        $this->load->model('member_model');
        $this->load->model('user_model');
    }
    /**
     * [index 会员首页]
     * @author LiKangJian 2017-12-26
     * @return [type] [description]
     */
	public function index()
    {
    	$this->checkLogin();
        $info = $this->uinfo;
        $level = $this->member_model->getGrowthLevel();
        foreach ($level as $k => $v) 
        {
            $level[$k]['privilege'] = json_decode($v['privilege'],true);
            $next = $info['grade'] +1 ;
            $next = $next >6 ? 6 :$next;
            if($v['grade'] == $next)
            {
                $info['next_grade_name'] = $v['grade_name'];
                $info['next_grade_value'] = $v['value_start'];
            }
        }
        $this->memberView('member/index', array('info'=>$info,'level'=>$level), 'v1.1');
    }
    /**
     * [help 会员帮助]
     * @author LiKangJian 2017-12-26
     * @return [type] [description]
     */
    public function help()
    {
        $level = $this->member_model->getGrowthLevel();
        foreach ($level as $k => $v) 
        {
            $level[$k]['privilege'] = json_decode($v['privilege'],true);
        }        
        $this->memberView('member/help', array('level'=>$level), 'v1.1');
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
        //查询用户等级以及积分
        return true;
    }
}