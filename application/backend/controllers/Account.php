<?php

/**
 * Copyright (c) 2014,上海二三四五网络科技股份有限公司
 * 摘    要：帐号及权限管理
 * 作    者：wangl@2345.com
 * 修改日期：2014.11.07
 */
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Account extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('Model_capacity');
        $this->config->load('user_capacity');
        $this->config->load('msg_text');
        $this->user_capacity_cfg = $this->config->item('user_capacity_cfg');
        $this->msg_text_cfg = $this->config->item('msg_text_cfg');
    }
    
    /**
     * 参    数：无
     * 作    者：wangl
     * 功    能：权限列表
     * 修改日期：2014.11.05
     */
    public function index()
    {
        $this->check_capacity('8_1_1');
        $result = $this->Model_capacity->list_capacity();
        $this->load->view('account/account', array(
            'accounts' => $result,
            'user_capacity_cfg' => $this->user_capacity_cfg
        ));
        unset($result);
    }
    
    /**
     * 新建/编辑账号信息
     */
    public function add()
    {
        $id = intval($this->input->get("id"));
        if($id)
        {
            $preview = intval($this->input->get("preview"));
            if($preview)
            {
                $this->check_capacity('8_1_4');
            }
            else
            {
                $this->check_capacity('8_1_3');
            }
        }
        else
        {
            $this->check_capacity('8_1_2');
        }
        $account = array(
            'id' => '',
            'name' => '',
            'phone' => '',
            'capacity' => '',
            'role' => '',
            'mark' => ''
        );
        if($id)
        {
            $account = $this->Model_capacity->get_capacity('', $id);
        }
        $capacityConfig = $this->user_capacity_cfg['user_capacity'];
        $infos = array(
            'userRoles' => $this->user_capacity_cfg['user_role'],
            'id' => $account['id'],
            'name' => $account['name'],
            'phone' => $account['phone'],
            'role' => $account['role'],
            'mark' => $account['mark'],
            'capacityConfig' => $capacityConfig,
            'userCapacity' => explode(',', $account['capacity']),
            'preview' => $preview,
        );

        $this->load->view('account/add', $infos);
    }
    
    /**
     * 参    数：无
     * 作    者：wangl
     * 功    能：ajax修改用户状态
     * 修改日期：2014.11.05
     */
    public function change_status()
    {
        $this->check_capacity('8_1_5', true);
        $id = intval($this->input->post("id"));
        $user = $this->Model_capacity->get_capacity('', $id);
        if (!empty($user))
        {
            $status = $user['status'] == 1 ? 0 : 1;
            $rows = $this->Model_capacity->update_capacity($id, array(
                "status" => $status
            ));
            if ($rows <= 0)
            {
                $this->ajaxReturn('n', $this->msg_text_cfg['falied']);
            }
            else
            {
                $mark = $user['status'] == 1 ? "禁用" : "启用";
                $this->syslog(9, "{$mark}帐号，用户名：{$user['name']}");
                $this->ajaxReturn('y', $this->msg_text_cfg['success'], $this->user_capacity_cfg['status_capacity'][$status]);
            }
        }
    }
    
    /**
     * 参    数：无
     * 作    者：wangl
     * 功    能：添加用户权限
     * 修改日期：2014.11.10
     */
    public function add_account()
    {
        $name = $this->input->post('name', true);
        $pass = $this->input->post('pass', true);
        $phone = $this->input->post('phone', true);
        $role = intval($this->input->post("role"));
        $capacity = $this->input->post('capacity');
        $mark = $this->input->post('mark');
        $capacity = $capacity ? array_unique(explode(',', $capacity)) : array();
        $capacity = implode(",", $capacity);
        if ($name == '')
        {
            $this->ajaxReturn('n', $this->msg_text_cfg['account']['name_required']);
        }
        $user = $this->Model_capacity->get_capacity($name);
        if (!empty($user)) //用户已经存在,则更新
        {
        	$data = array(
                "role" => $role,
                "capacity" => $capacity,
                "mark" => $mark,
                "phone" => $phone    
            );
        	if (!empty($pass))
        	{
        		$data['pass'] = md5($pass);
        	}
            $rows = $this->Model_capacity->update_capacity(0, $data, $name);
            $msg = "更新权限，用户名：{$name}，身份：{$this->user_capacity_cfg['user_role'][$role]}，备注：{$mark}，权限：{$capacity}";
            if ($pass)
            {
            	$msg .= "，密码";
            }
            $rows <= 0 ? $this->ajaxReturn('n', $this->msg_text_cfg['falied']) : $this->syslog(9, $msg);
            $this->ajaxReturn('y', $this->msg_text_cfg['success']);
        }
        //添加用户
        $lid = $this->Model_capacity->add_account(array(
            "name" => $name,
        	"pass" => md5($pass),
            "role" => $role,
            "capacity" => $capacity,
            "phone" => $phone,
            "createName" => $this->uname,
            "status" => 1,
            "mark" => $mark,
            "addTime" => time()
        ));
        $msg = "添加新用户，用户名：{$name}，身份：{$this->user_capacity_cfg['user_role'][$role]}，备注：{$mark}，权限：{$capacity}";
        $lid <= 0 ? $this->ajaxReturn('n', $this->msg_text_cfg['falied']) : $this->syslog(9, $msg);
        $this->ajaxReturn('y', $this->msg_text_cfg['success']);
    }
    
    public function pass()
    {
        $pass = $this->input->post('pass');
        if ($pass)
        {
            $rows = $this->Model_capacity->update_capacity(0, array('pass' => md5($pass)), $this->uname);
            if ($rows)
            {
                $msg = "更新密码，用户名：{$this->uname}";
                $this->syslog(9, $msg);
            }
            exit(json_encode(1));
        }else
        {
            $this->load->view('pass');
        }
    }
}
