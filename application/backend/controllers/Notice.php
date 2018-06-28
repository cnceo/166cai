<?php

/**
 * Copyright (c) 2014,上海二三四五网络科技股份有限公司
 * 摘    要：公告管理
 * 作    者：wangl@2345.com
 * 修改日期：2014.11.11
 */
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Notice extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('Model_notice');
        $this->config->load('msg_text');
        $this->msg_text_cfg = $this->config->item('msg_text_cfg');
        $this->category = array(
            "1" => "公告",
            "2" => "博文",
            "3" => "新闻",
            "4" => "活动"
        );
    }
    
    /**
     * 参    数：无
     * 作    者：wangl
     * 功    能：公告列表
     * 修改日期：2014.11.05
     */
    public function index()
    {
        $this->check_capacity("6_1_1");
        $page = intval($this->input->get("p"));
        $page = $page <= 1 ? 1 : $page;
        $searchData = array(
            "title" => str_hsc($this->input->get("title", true)),
            "name" => $this->input->get("name", true),
            "start_time" => $this->input->get("start_time", true),
            "end_time" => $this->input->get("end_time", true),
            "source" => $this->input->get("source", true),
            "isshow" => $this->input->get("isshow", true),
            "ishide" => $this->input->get("ishide", true),
            "category" => $this->input->get("category", true)
        );
        $this->filterTime($searchData['start_time'], $searchData['end_time']);
        $result = $this->Model_notice->list_notice($searchData, $page, self::NUM_PER_PAGE);
        
        foreach ($result[0] as $key => $value)
        {
            if (mb_strlen($value['content']) > 30)
            {
                $result[0][$key]['content'] = mb_substr($value['content'], 0, 30) . "...";
            }
            $result[0][$key]['status'] = $value['status'] == 0 ? "否" : "是";
        }
        
        $pageConfig = array(
            "page" => $page,
            "npp" => self::NUM_PER_PAGE,
            "allCount" => $result[1]
        );
        $pages = get_pagination($pageConfig);
        $pageInfo = array(
            "notices" => $result[0],
            "pages" => $pages,
            "search" => $searchData
        );
        $this->load->view("notice", $pageInfo);
        unset($pageInfo);
    }
    
    /**
     * 参    数：无
     * 作    者：wangl
     * 功    能：添加和更新页面
     * 修改日期：2014.11.05
     */
    public function add_update()
    {
        $this->check_capacity("6_1_2");
        $pageInfo = array();
        $id = intval($this->input->get("id"));
        if ($id > 0)
        {
            $result = $this->Model_notice->get_notice_by_id($id);
            $pageInfo['notice'] = $result;
        }
        $this->load->view("add_notice", $pageInfo);
    }
    
    /**
     * 参    数：无
     * 作    者：wangl
     * 功    能：添加更新操作
     * 修改日期：2014.11.05
     */
    public function do_add_update()
    {
        $this->check_capacity("6_1_2", true);
        $addData = array(
            "title" => str_hsc($this->input->post("title", true)),
            "content" => str_hsc($this->input->post("content")),
            "status" => intval($this->input->post("status")),
            "weight" => intval($this->input->post("weight")),
            "category" => intval($this->input->post("category"))
        );
        $id = intval($this->input->post("id"));
        if ($id > 0)
        {
            $result = $this->Model_notice->update($addData, $id);
        }
        else
        {
            $addData['username'] = $this->uname;
            $addData['addTime'] = time();
            $result = $this->Model_notice->add($addData);
        }
        if ($result > 0)
        {
            if ($id > 0)
            {
                $this->syslog(8, "更新公告：{$addData['title']}");
            }
            else
            {
                $this->syslog(8, "新建公告：{$addData['title']}");
            }
            $this->ajaxReturn('y', "恭喜你,操作成功");
        }
        else
        {
            $this->ajaxReturn('n', "对不起,操作失败");
        }
    }
    /**
     * 参    数：无
     * 作    者：wangl
     * 功    能：上传图片
     * 修改日期：2014.11.05
     */
    public function upload()
    {
        $config['upload_path'] = BASEPATH . '/../uploads/notice/';
        $config['allowed_types'] = 'gif|jpg|png';
        $config['max_size'] = 500;
        $config['max_width'] = 1024;
        $config['max_height'] = 768;
        $config['file_name'] = time() . rand(1, 1000);
        $this->load->library('upload', $config);
        echo $config['upload_path'];
        if (!is_dir($config['upload_path']))
        {
            mkdir($config['upload_path'], 0777, true);
        }
        if (!$this->upload->do_upload("imgFile"))
        {
            $error = array(
                'error' => 1,
                "message" => $this->upload->display_errors()
            );
        }
        else
        {
            $info = $this->upload->data();
            $error = array(
                'error' => 0,
                "url" => "/uploads/notice/" . $info['file_name']
            );
        }
        echo json_encode($error);
    }
    /**
     * 参    数：无
     * 作    者：wangl
     * 功    能：公告预览
     * 修改日期：2014.11.05
     */
    public function notice_view()
    {
        $this->check_capacity("6_1_2");
        $id = intval($this->input->get("id"));
        $result = $this->Model_notice->get_notice_by_id($id);
        $pageInfo = array(
            "notice" => $result
        );
        $this->load->view("notice_view", $pageInfo);
        
    }
    /**
    * 参    数：无
    * 作    者：wangl
    * 功    能：置顶
    * 修改日期：2015.02.09
    */    
    public function setTop()
    {
        $this->check_capacity("6_1_2", true);
        $id = intval($this->input->get("id"));
        $notice = $this->Model_notice->get_notice_by_id($id);
        if(!empty($notice))
        {
            $isTop = $notice['isTop'] == 1 ? 0 : 1;
            $result = $this->Model_notice->update(array("isTop" => $isTop), $id);
            if($result > 0)
            {
                $this->syslog(8, "置顶公告：{$id}");
                $this->ajaxReturn('y', $this->msg_text_cfg['success'], array("isTop" => $isTop));
            }
            else 
            {
                $this->ajaxReturn('n', $this->msg_text_cfg['failed']);
            }
        }
        else 
        {
            $this->ajaxReturn('n', "公告不存在");
        }
    }
}
