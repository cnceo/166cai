<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Apppush extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('model_pushconfig', 'pushconfig');
    }

    // 配置
    private $lidMaps = array(
        51 => '双色球',
        23529 => '大乐透',
    );

    // 日期
    private $weekMaps = array(
        51 => array(
            '0' => '星期日',
            '2' => '星期二',
            '4' => '星期四',
        ),
        23529 => array(
            '1' => '星期一',
            '3' => '星期三',
            '6' => '星期六',
        ),
    );
    
    // 购彩提醒
    public function index()
    {
        $this->check_capacity('15_2_1');
        $postData = $this->input->post(null, true);
        if(!empty($postData))
        {
            $this->updatePushConfig($postData);
        }
        $lid = $postData['lid'] ? $postData['lid'] : 51;    // 默认双色球
        $configs = $this->pushconfig->getPushConfig($lid);
        // 排序
        if(!empty($configs))
        {
            $weeks = array();
            foreach ($configs as $key => $items) 
            {
                $weeks[] = ($items['week'] == 0) ? 7 : $items['week'];
            }
            array_multisort($weeks, SORT_ASC, $configs);
        }
        $this->load->view("pushconfig/index", array(
            'lid'       =>  $lid,
            'configs'   =>  $configs,
            'lidArr'    =>  $this->lidMaps,
            'weekArr'   =>  $this->weekMaps[$lid],
        ));
    }

    // 更新配置
    public function updatePushConfig($postData)
    {
        $this->check_capacity('15_2_2');
        if(!empty($postData['info']) && !empty($postData['lid']))
        {
            $configs = array();
            $fields = array('ctype', 'lid', 'week', 'title', 'content', 'send_time', 'status', 'created');
            $pdata['s_data'] = array();
            $pdata['d_data'] = array();
            foreach ($postData['info'] as $key => $items) 
            {
                if(!empty($items['title']) && !empty($items['content']))
                {
                    array_push($pdata['s_data'], "(?, ?, ?, ?, ?, ?, ?, now())");
                    array_push($pdata['d_data'], 1);
                    array_push($pdata['d_data'], $postData['lid']);
                    array_push($pdata['d_data'], $items['week']);
                    array_push($pdata['d_data'], $items['title']);
                    array_push($pdata['d_data'], $items['content']);
                    array_push($pdata['d_data'], $items['send_time']);
                    array_push($pdata['d_data'], $items['status']);
                }
            }

            if(!empty($pdata['s_data']))
            {
                // 记录日志
                $this->recodeConfigLog($postData);
                // 删除指定彩种
                $this->pushconfig->delPushConfig($postData['lid']);
                // 更新
                $this->pushconfig->updatePushConfig($fields, $pdata);
            }
        }
    }

    public function recodeConfigLog($postData)
    {
        $log = "修改推送内容：" . $this->lidMaps[$postData['lid']] . ' ';
        foreach ($postData['info'] as $key => $items) 
        {
            $log .= $this->weekMaps[$postData['lid']][$items['week']] . '，' .  $items['title'] . '，' . $items['content'] . '，';
            $log .= ($items['status']) ? '开启' : '关闭';
            $log .= '；';
        }
        $this->syslog(55, $log);
    }
    
    public function management()
    {
        $this->check_capacity('15_1_1');
        $this->load->model('Model_Auto_Push_Config');
        $lists = $this->Model_Auto_Push_Config->getPushList();
        $redpacks = $this->Model_Auto_Push_Config->getRedPacks();
        foreach ($lists as $k=>$list)
        {
            $time = json_decode($list['config'], true);
            if ($time[0]['time'] == 30) {
                $lists[$k]['first'] = '半小时';
            } else {
                $t = explode('-', $time[0]['time']);
                $lists[$k]['first'] = '次日' . $t[1];
            }
            if (isset($time[1])) {
                $t = explode('-', $time[1]['time']);
                if ($t[0] == 1) {
                    $lists[$k]['secend'] = '次日' . $t[1];
                } else {
                    $lists[$k]['secend'] = '第三日' . $t[1];
                }
            } else {
                $lists[$k]['secend'] = '/';
            }
            if ($list['rid'] == 0) {
                $lists[$k]['red'] = '/';
            } else {
                $lists[$k]['red'] = $list['rname'];
            }
        }
        foreach ($redpacks as $k => $redpack)
        {
            $days = json_decode($redpack['use_params'], true);
            $days = $days['end_day'] - $days['start_day'];
            $redpacks[$k]['content'] = $redpack['use_desc'] . ',' . $redpack['c_name'] . ',' . $days . '天';
        }
        $this->load->view("pushconfig/list", array('lists' => $lists, 'redpacks' => $redpacks));
    }
    
    public function updateManagement()
    {
        $this->check_capacity('15_1_3', true);
        $datas = $this->input->post('data', true);
        if(!empty($datas)){
            foreach ($datas as $data) {
                if (!empty($data)) {
                    $param = array();
                    $param['id'] = $data['id'];
                    $param['ptype'] = $data['type'];
                    $param['status'] = $data['status'];
                    $str = "推送管理编辑：推送主题：" . $data['topic'] . '，';
                    $str .= "推送方式：" . ($data['type'] == 0 ? '短信' : 'push') . '，';
                    $param['content'] = $data['content'];
                    $str.= "推送内容：" . $data['content'] . '，';
                    $time = array();
                    if(isset($data['first'])){
                        $time[] = array('time' => $data['first']);
                        if($data['first'] == 30){
                            $str.= "第一次推送时间：半小时，";
                        } else {
                            $t = explode('-', $data['first']);
                            $str.= "第一次推送时间：次日".$t[1]."，";
                        }
                    }
                    if(isset($data['secend'])){
                        $time[] = array('time' => $data['secend']);
                        $t = explode('-', $data['secend']);
                        if ($t[0] == 1) {
                            $str.= "第二次推送时间：次日".$t[1]."，";
                        } else {
                            $str.= "第二次推送时间：第三日".$t[1]."，";
                        }
                    }
                    if(!empty($time)){
                        $param['config'] = json_encode($time);
                    }
                    if (isset($data['redpack'])) {
                        $redpack = explode(',', $data['redpack']);
                        $param['rid'] = $redpack[0];
                        $param['rname'] = $redpack[1];
                        if($param['rid']>0){
                           $str.= "红包：".$param['rname'];
                        }
                    }
                    if (isset($data['pushtitle'])) {
                        $param['title'] = $data['pushtitle'];
                    }
                    if (isset($data['pushurl'])) {
                        $param['url'] = $data['pushurl'];
                        $str.= "推送地址：".$data['pushurl'];
                    }
                    if (isset($data['pushAction'])) {
                        $param['action'] = $data['pushAction'];
                        if($data['pushAction'] != 2){
                            $param['url'] = "";
                        }
                    }
                    $this->syslog(65, $str);
                    $this->load->model('Model_Auto_Push_Config');
                    $this->Model_Auto_Push_Config->updateConfig($param);
                }
            }
        }
        return $this->ajaxReturn('y', "操作成功");
    }
    
    public function effect()
    {
        $this->check_capacity('15_1_2');
        $this->load->model('Model_Auto_Push_Config');
        $lists = $this->Model_Auto_Push_Config->getPushList();
        $searchData = array(
            "topic" => $this->input->get("topic", TRUE),
            "type" => $this->input->get("type", TRUE),
            "redpack" => $this->input->get("redpack", TRUE),
            "start_time" => $this->input->get("start_time", TRUE),
            "end_time" => $this->input->get("end_time", TRUE)
        );
        if (!$searchData['start_time'] && !$searchData['end_time']) {
            $searchData['start_time'] = date("Y-m-d 00:00:00");
            $searchData['end_time'] = date("Y-m-d 23:59:59");
        }
        $page = intval($this->input->get("p"));
        $page = $page < 1 ? 1 : $page;
        $effects = $this->Model_Auto_Push_Config->getEffectList($searchData, $page, 20);
        $pageConfig = array(
            "page" => $page,
            "npp" => 20,
            "allCount" => $effects[1]['num'],
        );
        $pages = get_pagination($pageConfig);
        $this->load->view("pushconfig/effect", array('lists' => $lists, 'search' => $searchData, 'effects' => $effects[0], 'pages' => $pages, 'sum' => $effects[2]));
    }
    
    public function getEffectDetail()
    {
        $id = $this->input->post('id', true);
        if ($id) {
            $this->load->model('Model_Auto_Push_Config');
            $lists = $this->Model_Auto_Push_Config->getEffectDetail($id);
            foreach ($lists as $k=>$list){
                $time = json_decode($list['config'], true);
                if ($time['time'] == 30) {
                    $lists[$k]['time'] = '半小时';
                } else {
                    $t = explode('-', $time['time']);
                    if ($t[0] == 1) {
                        $lists[$k]['time'] = '次日' . $t[1];
                    } else {
                        $lists[$k]['time'] = '第三日' . $t[1];
                    }
                }
                $lists[$k]['regNum'] = (strstr($list['topic'], '注册未实名') || strstr($list['topic'], '实名未购彩')) ? '-' : $list['regNum'];
                $lists[$k]['authNum'] = ($list['topic'] == '手机领红包未注册' || strstr($list['topic'], '实名未购彩')) ? '-' : $list['authNum'];
                $lists[$k]['recNum'] = ($list['topic'] == '手机领红包未注册' || strstr($list['topic'], '注册未实名')) ? '-' : $list['recNum'];
            }
            return $this->ajaxReturn('y', $lists);
        }else{
            return $this->ajaxReturn('n', '失败');
        }
    }
    
    public function export()
    {
        $this->check_capacity('15_1_4');
        $id = $this->input->get('id', true);
        $pdate = $this->input->get('pdate', true);
        if ($id) {
            $this->load->model('Model_Auto_Push_Config');
            $list = $this->Model_Auto_Push_Config->getPushListByid($id);
            $details = $this->Model_Auto_Push_Config->getPushDetails($list);
            $time = json_decode($list['config'], true);
            if ($time[0]['time'] == 30) {
                $first = '半小时';
            } else {
                $t = explode('-', $time[0]['time']);
                $first = '次日' . $t[1];
            }
            if (isset($time[1])) {
                $t = explode('-', $time[1]['time']);
                if ($t[0] == 1) {
                    $secend = '次日' . $t[1];
                } else {
                    $secend = '第三日' . $t[1];
                }
            } else {
                $secend = '/';
            }
            $fileName = $list['topic'] . '_' . $pdate . '.xls';
            $this->syslog(65, "推送效果导出UID或手机号操作，文件名：" . $fileName);
            header('Content-Type: application/vnd.ms-excel;charset=GBK');
            header('Content-Disposition: attachment;filename=' . $fileName);
            header('Cache-Control: max-age=0');
            echo mb_convert_encoding('创建日期', "GBK", "UTF-8") . "\t";
            echo mb_convert_encoding('第一次推送时间', "GBK", "UTF-8") . "\t";
            echo mb_convert_encoding('第二次推送时间', "GBK", "UTF-8") . "\t";
            echo mb_convert_encoding('uid', "GBK", "UTF-8") . "\t";
            echo mb_convert_encoding('手机号', "GBK", "UTF-8") . "\t\n";
            foreach ($details as $detail)
            {
                echo mb_convert_encoding($list['pdate'], "GBK", "UTF-8") . "\t";
                echo mb_convert_encoding($first, "GBK", "UTF-8") . "\t";
                echo mb_convert_encoding($secend, "GBK", "UTF-8") . "\t";
                echo mb_convert_encoding($detail['uid'], "GBK", "UTF-8") . "\t";
                echo mb_convert_encoding($detail['phone'], "GBK", "UTF-8") . "\t\n";
            }
        }
    }
}
