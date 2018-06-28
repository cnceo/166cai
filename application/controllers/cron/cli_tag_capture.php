<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/*
 * 半自动化推送
 * @date:2018-01-30
 */

class Cli_Tag_Capture extends MY_Controller
{
	public function __construct()
    {
        parent::__construct();
        $this->load->model('tag_model');
        $this->load->library('usertag/Capture');
    }

    // 标签数据收集
    public function captureTag()
    {
        $tagInfo = $this->tag_model->getTagData();
        while (!empty($tagInfo)) 
        {
            foreach ($tagInfo as $params) 
            {
                $this->handleTag($params);
            }
            $tagInfo = $this->tag_model->getTagData();
        }
    }

    public function handleTag($params)
    {
        $this->capture->index($params); 
    }

    // 标签集群更新
    public function updateCluster()
    {
        $this->capture->clusterUpdate();
    }

    // 指定标签及日期重跑
    public function recaptureTag($tag_id = 0, $date = '')
    {
        // 可重跑90天内的数据
        if(!empty($tag_id) && !empty($date) && (strtotime($date) >= strtotime("-90 days")))
        {
            $basetime = date("Y-m-d", strtotime($date));
            // 获取指定标签配置
            $params = $this->tag_model->getTagInfoById($tag_id);
            if(!empty($params))
            {
                // 清空指定表数据
                $this->tag_model->deleteTagData($tag_id, $basetime);
                // 当前基准日期
                $params['basetime'] = $basetime;
                $this->capture->index($params);
                var_dump('OK');
            }
        }
    }
}