<?php

/**
 * Copyright (c) 2014,上海二三四五网络科技股份有限公司
 * 摘    要：运营管理
 * 作    者：wangl@2345.com
 * 修改日期：2014.12.02
 */
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Operation extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('Model_operation');
        $this->load->helpers('fn_string_helper');
        $this->config->load('msg_text');
        $this->msg_text_cfg = $this->config->item('msg_text_cfg');
        $this->o_type = array(
            1 => "问题",
            2 => "建议"
        );
        $this->load->library('tools');
    }
    /**
     * 参    数：无
     * 作    者：wangl
     * 功    能：首页
     * 修改日期：2014.11.05
     */
    public function index()
    {
        $this->check_capacity("2_3_1");
        $page = intval($this->input->get("p"));
        $page = $page <= 1 ? 1 : $page;
        $searchData = array(
            "name" => $this->input->get("name", true),
            "content" => $this->input->get("content", true),
            "start_time" => $this->input->get("start_time", true),
            "end_time" => $this->input->get("end_time", true),
            "platform" => $this->input->get("platform", true),
            "reply_name" => $this->input->get("reply_name", true),
            "reply_s_time" => $this->input->get("reply_s_time", true),
            "reply_e_time" => $this->input->get("reply_e_time", true),
            "type" => $this->input->get("type", true)
        );
        $searchData["platform"] = $searchData["platform"] - 1;
        $this->filterTime($searchData['start_time'], $searchData['end_time']);
        $result = $this->Model_operation->list_operation($searchData, $page, self::NUM_PER_PAGE);
        $pageConfig = array(
            "page" => $page,
            "npp" => self::NUM_PER_PAGE,
            "allCount" => $result[1]
        );
        $pages = get_pagination($pageConfig);
        $pageInfo = array(
            "opes" => $result[0],
            "pages" => $pages,
            "search" => $searchData
        );
        echo $this->load->view("operation", $pageInfo, true);
    }
    /**
     * 参    数：无
     * 作    者：wangl
     * 功    能：详情页面
     * 修改日期：2014.11.05
     */
    public function detail()
    {
        $this->check_capacity("2_3_2");
        $id = intval($this->input->get("id", true));
        $result = $this->Model_operation->get_operation_by_id($id);
        $reply_result = array();
        if (!empty($result))
        {
            $reply_result = $this->Model_operation->get_reply_by_id($id);
        }
        
        $result['reply'] = $reply_result;
        $pageInfo = array(
            "ope" => $result
        );
        echo $this->load->view("operation_detail", $pageInfo, true);
    }
    /**
     * 参    数：无
     * 作    者：wangl
     * 功    能：回复
     * 修改日期：2014.11.05
     */
    public function reply()
    {
        $this->check_capacity("2_3_3", true);
        $uid = intval($this->input->post("uid", true));
        $id = intval($this->input->post("id", true));
        $content = str_hsc($this->input->post("content", true));
        $name = $this->input->post("name", true);
        $created = $this->input->post("created", true);
        if ($content == '')
        {
            return $this->ajaxReturn('n', $this->msg_text_cfg['operation']['content_required']);
        }
        $row = $this->Model_operation->reply($id, $content, $this->uname);
        if ($row)
        {
            // 日志
            $this->syslog(12, "回复{$name}在{$created}提交的反馈，回复内容：" . cutstr($content, 0, 15));
            // APP消息推送
            $this->load->library('mipush');
            $pushData = array(
                'type'      =>  'feedback',
                'uid'       =>  $uid,
                'content'   =>  $content,
            );
            $this->mipush->index('user', $pushData);
            return $this->ajaxReturn('y', $this->msg_text_cfg['success']);
        }
        else
        {
            return $this->ajaxReturn('n', $this->msg_text_cfg['failed']);
        }
    }
    /**
     * 参    数：无
     * 作    者：wangl
     * 功    能：删除回复
     * 修改日期：2014.11.05
     */
    public function del()
    {
        $this->check_capacity("2_3_3", true);
        $id = intval($this->input->post("id", true));
        $content = $this->input->post("content", true);
        $name = $this->input->post("name", true);
        $created = $this->input->post("created", true);
        $row = $this->Model_operation->delete($id);
        if ($row > 0)
        {
            $this->syslog(12, "删除用户{$name}在{$created}提交的反馈下的回复内容：" . cutstr($content, 0, 15));
            return $this->ajaxReturn('y', $this->msg_text_cfg['success']);
        }
        else
        {
            return $this->ajaxReturn('n', $this->msg_text_cfg['failed']);
        }
        
    }
    /**
     * 参    数：无
     * 作    者：wangl
     * 功    能：修改回复
     * 修改日期：2014.11.05
     */
    public function edit()
    {
        $this->check_capacity("2_3_3", true);
        $id = intval($this->input->post("reply_id", true));
        $content = str_hsc($this->input->post("edit_content", true));
        $row = $this->Model_operation->edit($id, $content);
        if ($content == '')
        {
            return $this->ajaxReturn('n', $this->msg_text_cfg['operation']['content_required']);
        }
        $row = $this->Model_operation->reply($id, $content, $this->uname);
        if ($row > 0)
        {
            return $this->ajaxReturn('y', $this->msg_text_cfg['success']);
        }
        else
        {
            return $this->ajaxReturn('n', $this->msg_text_cfg['failed']);
        }
    }
}