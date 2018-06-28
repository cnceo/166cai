<?php
/**
 * Copyright (c) 2014,上海二三四五网络科技股份有限公司
 * 摘    要：运营活动
 * 作    者：shigx@2345.com
 * 修改日期：2015.03.25
 */
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Activity extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->config->load('caipiao');
        foreach ($this->config->item('caipiao_all_cfg') as $key => $value)
        {
            $this->$key = $value;
        }
        $this->load->model('Model_activity');
    }
    
    /**
     * 参    数：
     * 作    者：shigx
     * 功    能：活动数据列表
     * 修改日期：2015.03.25
     */
    public function activityLog()
    {
        $this->check_capacity('4_1_1');
        $page = intval($this->input->get("p"));
        $page = $page <= 1 ? 1 : $page;
        $aid = $this->input->get("aid", true);
        $channelId = $this->input->get("channelId", true);
        $start_time = $this->input->get("start_time", true);
        $end_time = $this->input->get("end_time", true);
        $phone = $this->input->get("phone", true);
        $isRegister = $this->input->get("isRegister", true);
        $registerChannel = $this->input->get("registerChannel", true);
        $registerPlatform = $this->input->get("registerPlatform", true);
        $registerVersion = $this->input->get("registerVersion", true);
        $reg_type = $this->input->get("reg_type", true);
        $searchData = array(
            "aid"   => $aid ? $aid : "",
            "channelId" => $channelId ? $channelId : "",
            "start_time" => $start_time ? $start_time : "",
            "end_time" => $end_time ? $end_time : "",
            "phone" => $phone ? $phone : "",
            "isRegister" => $isRegister ? $isRegister : "",
            "registerChannel" => $registerChannel ? $registerChannel : "",
            "registerPlatform" => (isset($registerPlatform) && trim($registerPlatform) !== "") ? $registerPlatform : "",
            "registerVersion" => $registerVersion ? $registerVersion : "",
            "reg_type" => $reg_type ? $reg_type : '',
        );
        //$this->filterTime($searchData['start_time'], $searchData['end_time']);
        $result = $this->Model_activity->listActivityLog($searchData, $page, self::NUM_PER_PAGE);
        $pageConfig = array(
                "page"     => $page,
                "npp"      => self::NUM_PER_PAGE,
                "allCount" => $result[1]
        );
        $pages = get_pagination($pageConfig);
        $infos = array(
            'aid' => $this->getActivityName(),
            'channels' => $this->getChannels(),
            'version' => $this->getVersion(),
            'search'    => $searchData,
            'result'   => $result[0],
            'pages'    => $pages,
            'total' => $result[1]
        );
        $this->load->view("activity/activityLog", $infos);
    }
    
    /**
     * 参    数：无
     * 作    者：shigx
     * 功    能：导出活动数据列表
     * 修改日期：2014.11.05
     */
    public function export()
    {
        $this->check_capacity('4_1_2');
        $searchData = array(
            "aid"   => $this->input->get("aid", true),
            "channelId" => $this->input->get("channelId", true),
            "start_time" => $this->input->get("start_time", true),
            "end_time" => $this->input->get("end_time", true),
            "phone" => $this->input->get("phone", true),
            "isRegister" => $this->input->get("isRegister", true),
            "registerChannel" => $this->input->get("registerChannel", true),
            "registerPlatform" => $this->input->get("registerPlatform", true),
            "registerVersion" => $this->input->get("registerVersion", true),
        );
        $this->filterTime($searchData['start_time'], $searchData['end_time']);
        $result = $this->Model_activity->getExportData($searchData);
        header('Content-Type: application/vnd.ms-excel;charset=GBK');
        header('Content-Disposition: attachment;filename="' . date("Y_m_d_H_i_s") . '.xls"');
        header('Cache-Control: max-age=0');
        
        echo mb_convert_encoding('手机号', "GBK", "UTF-8") . "\t";
        echo mb_convert_encoding('参与活动时间', "GBK", "UTF-8") . "\t";
        echo mb_convert_encoding('活动名称', "GBK", "UTF-8") . "\t";
        echo mb_convert_encoding('参与渠道', "GBK", "UTF-8") . "\t";
        echo mb_convert_encoding('是否注册', "GBK", "UTF-8") . "\t";
        echo mb_convert_encoding('注册时间', "GBK", "UTF-8") . "\t";
        echo mb_convert_encoding('注册渠道', "GBK", "UTF-8") . "\t";
        echo mb_convert_encoding('注册平台', "GBK", "UTF-8") . "\t";
        echo mb_convert_encoding('注册版本', "GBK", "UTF-8") . "\t\n";
        $activity = $this->getActivityName();
        $channels = $this->getChannels();
        foreach ($result as $row)
        {
            echo mb_convert_encoding($row['phone'], "GBK", "UTF-8") . "\t";
            echo mb_convert_encoding($row['created'], "GBK", "UTF-8") . "\t";
            echo mb_convert_encoding($activity[$row['aid']]['a_name'], "GBK", "UTF-8") . "\t";
            echo mb_convert_encoding($channels[$row['channel_id']]['name'], "GBK", "UTF-8") . "\t";
            if($row['uid'])
            {
                echo mb_convert_encoding("是", "GBK", "UTF-8") . "\t";
                echo mb_convert_encoding($row['rTime'], "GBK", "UTF-8") . "\t";
                echo mb_convert_encoding($channels[$row['rChannel']]['name'], "GBK", "UTF-8") . "\t";
                $platform = $row['rPlatform'] == '1' ? 'app' : '网页';
                echo mb_convert_encoding($platform, "GBK", "UTF-8") . "\t";
                $version = $row['rPlatform'] == '1' ? $row['rVersion'] : '';
                echo mb_convert_encoding($version, "GBK", "UTF-8") . "\t\n";
            }
            else
            {
                echo mb_convert_encoding("否", "GBK", "UTF-8") . "\t";
                echo mb_convert_encoding("", "GBK", "UTF-8") . "\t";
                echo mb_convert_encoding("", "GBK", "UTF-8") . "\t";
                echo mb_convert_encoding("", "GBK", "UTF-8") . "\t";
                echo mb_convert_encoding("", "GBK", "UTF-8") . "\t\n";
            }  
        }
        unset($result);
    }
    
    /**
     * 参    数：
     * 作    者：shigx
     * 功    能：红包管理
     * 修改日期：2015.03.25
     */
    public function listRedpack()
    {
        $this->check_capacity('4_2_1');
        $page = intval($this->input->get("p"));
        $page = $page <= 1 ? 1 : $page;
        $uinfo = $this->input->get("uinfo", true);
        $p_type = $this->input->get("p_type", true);
        $g_start_time = $this->input->get("g_start_time", true);
        $g_end_time = $this->input->get("g_end_time", true);
        $aid = $this->input->get("aid", true);
        $channel_id = $this->input->get("channel_id", true);
        $v_start_time = $this->input->get("v_start_time", true);
        $v_end_time = $this->input->get("v_end_time", true);
        $money = $this->input->get("money", true);
        $status = $this->input->get("status", true);
        $s_start_time = $this->input->get("s_start_time", true);
        $s_end_time = $this->input->get("s_end_time", true);
        $u_start_time = $this->input->get("u_start_time", true);
        $u_end_time = $this->input->get("u_end_time", true);
        $ismobile_used = $this->input->get("ismobile_used", true);
        $searchData = array(
            "uinfo"     => $uinfo ? $uinfo : "",
            "p_type"    => $p_type ? $p_type : "",
            "g_start_time"  => $g_start_time ? $g_start_time : "",
            "g_end_time" => $g_end_time ? $g_end_time : "",
            "aid" => $aid ? $aid : "",
            "channel_id" => $channel_id ? $channel_id : "",
            "v_start_time" => $v_start_time ? $v_start_time : "",
            "v_end_time" => $v_end_time ? $v_end_time : "",
            "money" => $money ? $money : "",
            "status" => (isset($status) && trim($status) !== "") ? $status : "",
            "s_start_time" => $s_start_time ? $s_start_time : "",
            "s_end_time" => $s_end_time ? $s_end_time : "",
            "u_start_time" => $u_start_time ? $u_start_time : "",
            "u_end_time" => $u_end_time ? $u_end_time : "",
            'ismobile_used' => $ismobile_used ? 1 : '',
        );
        if (preg_match('/^\d{11}$/', $uinfo)) $searchData['phone'] = $uinfo;
        else $searchData['uname'] = $uinfo;
        $gets = $this->input->get() ? $this->input->get() : array();
        if($this->checkPramsEmpty($gets))
        {
            $searchData['g_start_time'] = date('Y-m-d H:i:s',strtotime(date('Y-m-d')));
            $searchData['g_end_time'] = date('Y-m-d H:i:s',strtotime(date('Y-m-d'))+60*60*24-1);
        }
        //$this->filterTime($searchData['g_start_time'], $searchData['g_end_time']);
        $result = $this->Model_activity->listRedpack($searchData, $page, self::NUM_PER_PAGE);
        $pageConfig = array(
                "page"     => $page,
                "npp"      => self::NUM_PER_PAGE,
                "allCount" => $result[1]
        );
        $pages = get_pagination($pageConfig);
        $infos = array(
            'aid' => $this->getActivityName(),
            'channels' => $this->getChannels(),
            'p_type' => $this->getRedpackType(),
            'search'    => $searchData,
            'result'   => $result[0],
            'pages'    => $pages,
            'countUser' => $result[2],
            'countMoney' => $result[3]
        );
        $this->load->view("activity/listRedpack", $infos);
    }
    
    /**
     * 用户详情调用
     */
    public function ajaxListRedpack()
    {
        $this->check_capacity('4_2_1');
        $page = intval($this->input->get("p"));
        $page = $page <= 1 ? 1 : $page;
        $searchData = array(
            "uid" => $this->input->get("uid", true),
            "p_type"    => $this->input->get("p_type", true),
            "g_start_time"  => $this->input->get("g_start_time", true),
            "g_end_time" => $this->input->get("g_end_time", true),
            "aid" => $this->input->get("aid", true),
            "channel_id" => $this->input->get("channel_id", true),
            "v_start_time" => $this->input->get("v_start_time", true),
            "v_end_time" => $this->input->get("v_end_time", true),
            "status" => $this->input->get("status", true),
            "s_start_time" => $this->input->get("s_start_time", true),
            "s_end_time" => $this->input->get("s_end_time", true),
            "u_start_time" => $this->input->get("u_start_time", true),
            "u_end_time" => $this->input->get("u_end_time", true),
        );
        //$this->filterTime($searchData['g_start_time'], $searchData['g_end_time']);
        $result = $this->Model_activity->listRedpack($searchData, $page, self::NUM_PER_PAGE);
        $pageConfig = array(
                "page"     => $page,
                "npp"      => self::NUM_PER_PAGE,
                "allCount" => $result[1]
        );
        $pages = get_pagination($pageConfig);
        $infos = array(
            'aid' => $this->getActivityName(),
            'channels' => $this->getChannels(),
            'p_type' => $this->getRedpackType(),
            'search'    => $searchData,
            'result'   => $result[0],
            'pages'    => $pages,
            'page' => $page,
            'pageNum' => self::NUM_PER_PAGE,
            "fromType" => $this->input->get("fromType", true),
        );
        $this->load->view("activity/ajaxListRedpack", $infos);
    }
    
    /**
     * 红包导出功能
     */
    public function redpackExport()
    {
        $this->check_capacity('4_2_4');
        $searchData = array(
            "phone" => $this->input->get("phone", true),
            "p_type"    => $this->input->get("p_type", true),
            "g_start_time"  => $this->input->get("g_start_time", true),
            "g_end_time" => $this->input->get("g_end_time", true),
            "aid" => $this->input->get("aid", true),
            "channel_id" => $this->input->get("channel_id", true),
            "v_start_time" => $this->input->get("v_start_time", true),
            "v_end_time" => $this->input->get("v_end_time", true),
            "money" => $this->input->get("money", true),
            "status" => $this->input->get("status", true),
            "s_start_time" => $this->input->get("s_start_time", true),
            "s_end_time" => $this->input->get("s_end_time", true),
            "u_start_time" => $this->input->get("u_start_time", true),
            "u_end_time" => $this->input->get("u_end_time", true),
            "ismobile_used" => $this->input->get("ismobile_used", true),
        );
        $gets = $this->input->get() ? $this->input->get() : array();
        if($this->checkPramsEmpty($gets))
        {
            $searchData['g_start_time'] = date('Y-m-d H:i:s',strtotime(date('Y-m-d')));
            $searchData['g_end_time'] = date('Y-m-d H:i:s',strtotime(date('Y-m-d'))+60*60*24-1);
        }
        //$this->filterTime($searchData['g_start_time'], $searchData['g_end_time']);
        $result = $this->Model_activity->redpackExport($searchData);

        header('Content-Type: application/vnd.ms-excel;charset=GBK');
        header('Content-Disposition: attachment;filename="' . date("Y_m_d_H_i_s") . '.xls"');
        header('Cache-Control: max-age=0');
        
        echo mb_convert_encoding('手机号', "GBK", "UTF-8") . "\t";
        echo mb_convert_encoding('领取时间', "GBK", "UTF-8") . "\t";
        echo mb_convert_encoding('活动名称', "GBK", "UTF-8") . "\t";
        echo mb_convert_encoding('类型', "GBK", "UTF-8") . "\t";
        echo mb_convert_encoding('红包金额', "GBK", "UTF-8") . "\t";
        echo mb_convert_encoding('领取渠道', "GBK", "UTF-8") . "\t";
        echo mb_convert_encoding('生效日', "GBK", "UTF-8") . "\t";
        echo mb_convert_encoding('到期日', "GBK", "UTF-8") . "\t";
        echo mb_convert_encoding('使用状态', "GBK", "UTF-8") . "\t";
        echo mb_convert_encoding('红包使用时间', "GBK", "UTF-8") . "\t";
        echo mb_convert_encoding('使用条件', "GBK", "UTF-8") . "\t";
        echo mb_convert_encoding('提现限制', "GBK", "UTF-8") . "\t";
        echo mb_convert_encoding('备注', "GBK", "UTF-8") . "\t\n";
        $activity = $this->getActivityName();
        $channels = $this->getChannels();
        $redpackType = $this->getRedpackType();
        foreach ($result as $row)
        {
            echo mb_convert_encoding($row['phone'], "GBK", "UTF-8") . "\t";
            echo mb_convert_encoding($row['get_time'], "GBK", "UTF-8") . "\t";
            echo mb_convert_encoding($activity[$row['aid']]['a_name'], "GBK", "UTF-8") . "\t";
            echo mb_convert_encoding($redpackType[$row['p_type']]['p_name'], "GBK", "UTF-8") . "\t";
            echo mb_convert_encoding(m_format($row['money']) ." 元", "GBK", "UTF-8") . "\t";
            echo mb_convert_encoding($channels[$row['channel_id']]['name'], "GBK", "UTF-8") . "\t";
            echo mb_convert_encoding($row['valid_start'], "GBK", "UTF-8") . "\t";
            echo mb_convert_encoding($row['valid_end'], "GBK", "UTF-8") . "\t";
            $status = $row['status'] == '2' ? '已使用' : '未使用';
            echo mb_convert_encoding($status, "GBK", "UTF-8") . "\t";
            echo mb_convert_encoding($row['use_time'], "GBK", "UTF-8") . "\t";
            echo mb_convert_encoding($row['use_desc'], "GBK", "UTF-8") . "\t";
            echo mb_convert_encoding($row['refund_desc'], "GBK", "UTF-8") . "\t";
            echo mb_convert_encoding($row['remark'], "GBK", "UTF-8") . "\t\n";
        }
        unset($result);
    }
    
    /**
     * 删除用户红包
     */
    public function redpackDelete()
    {
        $this->check_capacity('4_2_4', true);
        $ids = $this->input->post('ids', true);
        $idsStr = implode("','", explode(',', $ids));
        $checkUsed = $this->Model_activity->checkRedpackUsed($idsStr);
        if($checkUsed)
        {
            return $this->ajaxReturn('n', "操作异常，操作的记录中有已被使用的红包");
        }
        
        $result = $this->Model_activity->redpackDelete($idsStr);
        if ($result === false)
        {
            return $this->ajaxReturn('n', "删除失败");
        }
        $this->syslog(27, "删除红包{$ids}操作" );
        return $this->ajaxReturn('y', "操作成功");
    }
    
    /*
     * 返回活动名称数组
     */
    private function getActivityName()
    {
        $data = array();
        $result = $this->Model_activity->getActivityName();
        foreach ($result as $val)
        {
            $data[$val['id']] = $val;
        }
        
        return $data;
    }
    
    /**
     * 返回渠道信息
     * @param unknown_type $platform
     */
    private function getChannels($platform = '')
    {
        $data = array();
        $this->load->model('model_channel');
        $result = $this->model_channel->getChannels($platform);
        foreach ($result as $val)
        {
            $data[$val['id']] = $val;
        }
        
        return $data;
    }
    
    /**
     * 返回红包类型信息
     */
    private function getRedpackType()
    {
        $data = array();
        $result = $this->Model_activity->getRedpackType();
        foreach ($result as $val)
        {
            $data[$val['p_type']] = $val;
        }
         
        return $data;
    }
    
    /**
     * 返回版本信息
     */
    private function getVersion()
    {
        return $this->Model_activity->getVersion();
    }

    /**
     * 竞彩活动 - 概览
     */
    public function activityJc()
    {
        $this->check_capacity('4_3_1');
        $page = intval($this->input->get("p"));
        $searchData = array(
            "name" => $this->input->get("name", true),
            "platform" => $this->input->get("platform", true),
            "activity_issue" => $this->input->get("activity_issue", true),
            "start_r_m" => $this->input->get("start_r_m", true),
            "end_r_m" => $this->input->get("end_r_m", true),
        );

        $page = $page <= 1 ? 1 : $page;
        $result = $this->Model_activity->list_JcActivity($searchData, $page, self::NUM_PER_PAGE);

        $pageConfig = array(
            "page"     => $page,
            "npp"      => self::NUM_PER_PAGE,
            "allCount" => $result[1]
        );

        $pages = get_pagination($pageConfig);

        $infos = array(
            'result'   => $result[0],
            'pages'    => $pages,
            'search'   => $searchData,
            'total'    => array(
                'number' => $result[2]['number']?$result[2]['number']:0,
                'money'  => $result[2]['totalMoney']?$result[2]['totalMoney']:0,
                'totalPayMoney'  => $result[3]['totalPayMoney']?$result[3]['totalPayMoney']:0,
                'issue'  => $result[4]['activity_issue']?$result[4]['activity_issue']:0,
            )
        );

        $this->load->view("activity/activityJc", $infos);
    }

    /**
     * 竞彩活动 - 管理
     */
    public function manageJc()
    {
        $this->check_capacity('4_3_2');
        $page = intval($this->input->get("p"));
        $page = $page <= 1 ? 1 : $page;
        $result = $this->Model_activity->getAllJcInfo($searchData, $page, self::NUM_PER_PAGE);
 
        $pageConfig = array(
            "page"     => $page,
            "npp"      => self::NUM_PER_PAGE,
            "allCount" => $result[1]
        );

        $playTypeArr = array(
            'spf' => '胜平负',
            'rqspf' => '让球胜平负'
        );

        $activityInfo = array();
        if(!empty($result[0]))
        {
            $weekDays = array('周日', '周一', '周二', '周三', '周四', '周五', '周六');
            foreach ($result[0] as $key => $items) 
            {
                $activityInfo[$key]['activity_issue'] = $items['activity_issue'];
                $activityInfo[$key]['week'] = $weekDays[date('w', strtotime(substr($items['mid'], 0, 8)))];
                $activityInfo[$key]['matchId'] =  substr($items['mid'], 8, 3);
                $activityInfo[$key]['plan'] = $items['plan'];
                $activityInfo[$key]['playType'] = $playTypeArr[$items['playType']];
                $activityInfo[$key]['pay_money'] = $items['pay_money'];
                $activityInfo[$key]['left_money'] = $items['left_money'];
                $activityInfo[$key]['join_num'] = $items['join_num'];
                $activityInfo[$key]['status'] = $items['status'];
                $activityInfo[$key]['statusMsg'] = $this->getStatusMsg($items);
                $activityInfo[$key]['pay_status'] = $items['pay_status'];
                $activityInfo[$key]['payStatusMsg'] = $this->getPayStatusMsg($items);
                $activityInfo[$key]['startTime'] = $items['startTime'];
            }
        }

        $pages = get_pagination($pageConfig);
        $infos = array(
            'result'   => $activityInfo,
            'pages'    => $pages,
            'total'    => array(
                'allActivity' => $result[1]?$result[1]:0,
                'payActivity' => $result[3]['totalActivity']?$result[2]['totalActivity']:0,
                'totalPeople' => $result[2]['totalPeople']?$result[2]['totalPeople']:0,
                'totalPayMoney' => $result[3]['totalPayMoney']?$result[3]['totalPayMoney']:0,
            )
        );
        $this->load->view("activity/manageJc", $infos);
    }

    /**
     * 竞彩活动 - 活动状态
     */ 
    public function getStatusMsg($activity)
    {
        switch ($activity['status']) 
        {
            case '0':
                $status = '等待开奖';
                break;
            case '1':
                $status = '未中奖';
                break;
            case '2':
                $status = '中奖';
                break;
            default:
                $status = '';
                break;
        }
        return $status;
    }

    /**
     * 竞彩活动 - 赔付状态
     */ 
    public function getPayStatusMsg($activity)
    {
        $status = '无';
        if($activity['status'] == '0')
        {
            $status = '---';
        }
        elseif($activity['status'] == '1')
        {
            if($activity['status'] == '0')
            {
                $status = '等待赔付';
            }
            else
            {
                $status = '已赔付';
            }
        }
        return $status;
    }

    /**
     * 竞彩活动 - 创建活动
     */
    public function createJc()
    {
        $this->check_capacity('4_3_5', true);
        $activity_issue = $this->input->post('activity_issue', true);
        $pay_money = $this->input->post('pay_money', true);
        $mid = $this->input->post('mid', true);
        $plan = $this->input->post('plan', true);
        $startDate = $this->input->post('startDate', true);
        $playType = $this->input->post('playType', true); 

        // 活动期次检查
        if( empty($activity_issue) || !is_numeric($activity_issue) || $activity_issue <= 0 )
        {
            $this->ajaxReturn('n', '活动期次格式错误');
        }

        // 赔付总额检查
        if( empty($pay_money) || !is_numeric($pay_money) || $pay_money <= 0 )
        {
            $this->ajaxReturn('n', '赔付总额格式错误');
        }

        if( $pay_money%10 !== 0)
        {
            $this->ajaxReturn('n', '赔付总额必须是10的倍数');
        }

        // 比赛场次检查
        if(empty($mid))
        {
            $this->ajaxReturn('n', '比赛场次不能为空');
        }

        $matchInfo = $this->Model_activity->getJcMatchInfo();
        
        if(empty($matchInfo[$mid]))
        {
            $this->ajaxReturn('n', '所选场次不在在售期内');
        }

        // 比赛方案检查
        $planArry = array('0', '1', '3');
        if(!in_array($plan, $planArry))
        {
            $this->ajaxReturn('n', '比赛方案只能输入0，1，3');
        }

        // 上架时间
        $endTime = date('Y-m-d H:i:s',substr($matchInfo[$mid]['jzdt'], 0, 10));
        if( $startDate >= $endTime )
        {
            $this->ajaxReturn('n', '上架时间不能超过比赛截止时间：' . $endTime);
        }

        // 玩法选择
        $playTypeArry = array(
            'spf' => 'spfGd',
            'rqspf' => 'rqspfGd'
        );

        if( empty($playTypeArry[$playType]) || empty($matchInfo[$mid][$playTypeArry[$playType]]) )
        {
            $this->ajaxReturn('n', '当前比赛不支持此玩法');
        }

        // 数据组装
        $activityData = array(
            'activity_id' => '3',
            'activity_issue' => $activity_issue,
            'mid' => $mid,
            'plan' => $plan,
            'lid' => '42',
            'playType' => $playType,
            'startTime' => $startDate,
            'pay_money' => ParseUnit($pay_money),
            'left_money' => ParseUnit($pay_money)
        );

        if($this->Model_activity->createJc($activityData))
        {
            $this->ajaxReturn('y', '活动创建成功');
        }
        else
        {
            $this->ajaxReturn('n', '活动期次已存在，创建失败');
        }
    }
    
    public function redpackUse() {
        $id = $this->input->post('id');
        $this->load->model('model_user');
        if ($this->model_user->useRedbag($id)) {
            $this->syslog(36, "使用红包{$id}操作" );
            exit(json_encode(array('status' => 'y', 'message' => '使用成功')));
        }
        exit(json_encode(array('status' => 'n', 'message' => '使用失败')));
    }

    /**
     * 竞彩活动 - 加奖 - 概览
     */
    public function activityJj()
    {
        $this->check_capacity('4_3_3');
        $page = intval($this->input->get("p"));
        $searchData = array(
            "name" => $this->input->get("name", true),
            "platform" => $this->input->get("platform", true),
            "start_r_m" => $this->input->get("start_r_m", true),
            "end_r_m" => $this->input->get("end_r_m", true),
            "jj_id" => $this->input->get("jj_id", true),
            "lid" => $this->input->get("lid", true),
            "status" => $this->input->get("status", true),
            "created" => $this->input->get("created", true),
            "start_r_c" => $this->input->get("start_r_c", true),
            "end_r_c" => $this->input->get("end_r_c", true),
        );

        $page = $page <= 1 ? 1 : $page;
        $result = $this->Model_activity->list_JjActivity($searchData, $page, self::NUM_PER_PAGE);

        $pageConfig = array(
            "page"     => $page,
            "npp"      => self::NUM_PER_PAGE,
            "allCount" => !empty($result[2])?$result[2]:0
        );

        $platformArry = array(
            0 => '网页',
            1 => 'Android',
            2 => 'IOS',
            3 => 'M版'
        );

        $activityInfo = array();
        if(!empty($result[0]))
        {
            $this->load->library('BetCnName');
            foreach ($result[0] as $key => $items) 
            {
                $activityInfo[$key]['jj_id'] = $items['jj_id'];
                $activityInfo[$key]['orderId'] = $items['orderId'];
                $activityInfo[$key]['userName'] = $items['userName'];
                $activityInfo[$key]['lid'] = $items['lid'];
                $activityInfo[$key]['lname'] = BetCnName::$BetCnName[$items['lid']];
                $activityInfo[$key]['issue'] = $items['issue'];
                $activityInfo[$key]['created'] = $items['created'];
                $activityInfo[$key]['money'] = $items['money']?$items['money']:0;
                $activityInfo[$key]['margin'] = $items['margin']?$items['margin']:0;
                $activityInfo[$key]['add_money'] = $items['add_money']?$items['add_money']:0;
                $activityInfo[$key]['status'] = $items['status'];
                $activityInfo[$key]['buyPlatform'] = $platformArry[$items['buyPlatform']];
            }
        }

        $pages = get_pagination($pageConfig);

        $infos = array(
            'result'   => $activityInfo,
            'pages'    => $pages,
            'search'   => $searchData,
            'total'    => array(
                'totalMoney'    => $result[1]['totalMoney']?$result[1]['totalMoney']:0,
                'totalMargin'   => $result[1]['totalMargin']?$result[1]['totalMargin']:0,
                'totalAddMoney' => $result[1]['totalAddMoney']?$result[1]['totalAddMoney']:0,
                'totalPeople'   => $result[1]['totalPeople']?$result[1]['totalPeople']:0,
            )
        );

        $this->load->view("activity/activityJj", $infos);
    }

    /**
     * 竞彩活动 - 加奖 - 管理
     */
    public function manageJj()
    {
        $this->check_capacity('4_3_4');
        $page = intval($this->input->get("p"));
        $page = $page <= 1 ? 1 : $page;
        $result = $this->Model_activity->getAllJjInfo($searchData, $page, self::NUM_PER_PAGE);

        $pageConfig = array(
            "page"     => $page,
            "npp"      => self::NUM_PER_PAGE,
            "allCount" => !empty($result[0]['activityNum'])?$result[0]['activityNum']:0
        );

        // 方案说明
        $playTypeArry = array(
            0 => '单关',
            1 => '2串1'
        );

        // 加奖方式
        $ctypeArry = array(
            0 => '按金额',
            1 => '按比例'
        );

        $activityInfo = array();
        if(!empty($result[1]))
        {
            $this->load->library('BetCnName');
            foreach ($result[1] as $key => $items) 
            {
                $activityInfo[$key]['id'] = $items['id'];
                $activityInfo[$key]['startTime'] = $items['startTime'];
                $activityInfo[$key]['endTime'] = $items['endTime'];
                $activityInfo[$key]['lid'] = $items['lid'];
                $activityInfo[$key]['lname'] = BetCnName::$BetCnName[$items['lid']];
                $activityInfo[$key]['playType'] = $items['playType'];
                $activityInfo[$key]['playTypeName'] = $playTypeArry[$items['playType']];
                $activityInfo[$key]['ctype'] = $items['ctype'];
                $activityInfo[$key]['ctypeName'] = $ctypeArry[$items['ctype']];
                $activityInfo[$key]['num'] = $items['num'];
                $activityInfo[$key]['money'] = $items['money']?$items['money']:0;
                $activityInfo[$key]['margin'] = $items['margin']?$items['margin']:0;
                $activityInfo[$key]['add_money'] = $items['add_money']?$items['add_money']:0;
                $activityInfo[$key]['status'] = $this->getJjStatus($items);
            }
        }

        // hover配置
        $hoverInfo = $this->Model_activity->getJjHover();

        $pages = get_pagination($pageConfig);
        $infos = array(
            'result'   => $activityInfo,
            'pages'    => $pages,
            'total'    => array(
                'activityNum'   => $result[0]['activityNum']?$result[0]['activityNum']:0,
                'totalMoney'    => $result[0]['totalMoney']?$result[0]['totalMoney']:0,
                'totalPeople'   => $result[0]['totalPeople']?$result[0]['totalPeople']:0
            ),
            'hoverInfo' => $hoverInfo,
        );
        $this->load->view("activity/manageJj", $infos);
    }

    /**
     * 竞彩活动 - 加奖 - hover切换
     */
    public function getJjHoverInfo()
    {
        $this->check_capacity('4_3_6', true);
        $lname = $this->input->post("lname", true);

        $info = $this->Model_activity->getJjHover($lname);

        if(!empty($info))
        {
            $hoverInfo = array(
                'startTime' => $info['startTime'],
                'endTime' => $info['endTime'],
                'slogan' => $info['slogan'],
                'platform' => $info['platform'] ? $info['platform'] : 0,
                'playType' => $info['playType'] ? $info['playType'] : 0,
                'params' => json_decode($info['params'], true)
            );
        }
        else
        {
            $planConfig = array(
                0 => array(
                    'min' => '',
                    'max' => '',
                    'dg' => '',
                    '2c1' => ''
                )
            );

            $hoverInfo = array(
                'startTime' => date('Y-m-d H:i:s'),
                'endTime' => date('Y-m-d H:i:s'),
                'slogan' => '',
                'platform' => 0,
                'playType' => 0,
                'params' => $planConfig
            );
        }

        $result = array(
            'status' => '1',
            'message' => '获取成功',
            'data' => $hoverInfo
        );
        die(json_encode($result));
    }

    public function getJjStatus($activityInfo)
    {
        $now = date('Y-m-d H:i:s');
        if($now < $activityInfo['startTime'])
        {
            $status = "未开始";
        }
        elseif ($now > $activityInfo['endTime']) 
        {
            $status = "已结束";
        }
        else
        {
            $status = "进行中";
        }
        return $status;
    }

    /**
     * 竞彩活动 - 加奖 - 创建活动
     */
    public function createJj()
    {
        $this->check_capacity('4_3_6', true);
        $lid = $this->input->post('lid', true);
        $playType = $this->input->post('playType', true); 
        $startTime = $this->input->post('startTime', true);
        $endTime = $this->input->post('endTime', true);
        $ctype = $this->input->post('ctype', true);
        $plan = $this->input->post('plan', true);
        $buyPlatform = $this->input->post('buyPlatform', true);

        // 参数检查
        if( empty($lid) || $playType === '' || $ctype === '' || $buyPlatform === '')
        {
            $this->ajaxReturn('n', '缺少必要参数');
        }

        if(empty($startTime) || empty($endTime))
        {
            $this->ajaxReturn('n', '活动时间不能为空');
        }

        if($startTime >= $endTime)
        {
            $this->ajaxReturn('n', '活动开始时间不能大于结束时间');
        }

        // 检查是否有在当前时间内的活动
        $checkData = array(
            'lid' => $lid,
            'playType' => $playType,
            'startTime' => $startTime,
            'endTime' => $endTime
        );

        $checkRes = $this->Model_activity->checkActivityInfo($checkData);

        if(!empty($checkRes))
        {
            $this->ajaxReturn('n', '当前活动时间已存在活动');
        }

        // 平台
        $buyPlatform = str_replace(array('web', 'app', 'm'), array('0', '1,2', '3'), $buyPlatform);

        if(!empty($plan))
        {
            $planConfig = $this->checkJjPlan($plan);
        }

        if(empty($planConfig))
        {
            $this->ajaxReturn('n', '配置方案错误');
        }

        // 获取活动表信息
        $activityInfo = $this->Model_activity->getActivityInfo($activity_id = 4);

        if(empty($activityInfo))
        {
            $this->ajaxReturn('n', '获取活动信息失败');
        }

        // 组装参数
        $activityRes = array(
            'id'         => '4',
            'start_time' => ($activityInfo['start_time'] > $startTime || $activityInfo['start_time'] == '0000-00-00 00:00:00')?$startTime:$activityInfo['start_time'],
            'end_time'   => ($activityInfo['end_time'] < $endTime || $activityInfo['end_time'] == '0000-00-00 00:00:00')?$endTime:$activityInfo['end_time'],
        );

        $res1 = $this->Model_activity->updateActivityInfo($activityRes);

        if(!$res1)
        {
            $this->ajaxReturn('n', '获取活动信息失败');
        }

        $markInfo = array(
            0 => '竞彩单关加奖',
            1 => '竞彩2串1加奖',
        );

        // 组装参数
        $activityData = array(
            'lid'       => $lid,
            'playType'  => $playType,
            'startTime' => $startTime,
            'endTime'   => $endTime,
            'ctype'     => $ctype,
            'mark'      => $markInfo[$playType],
            'params'    => serialize($planConfig),
            'buyPlatform' => $buyPlatform,
        );

        $lastId = $this->Model_activity->createJj($activityData);
        if($lastId > 0)
        {
            // 日志
            $this->syslog(38, "新建加奖活动：{$lastId}操作" );
            $this->ajaxReturn('y', '活动创建成功');
        }
        
        $this->ajaxReturn('n', '活动创建失败');
    }

    /**
     * 竞彩活动 - 加奖 - 方案检查
     */
    public function checkJjPlan($plan)
    {
        $planArry = explode('|', $plan);
        $planConfig = array();
        $last = '';
        foreach ($planArry as $key => $items) 
        {
            $tplArry = explode(',', $items);

            // 格式处理 去空格 元转分
            $tplArry[0] = is_numeric($tplArry[0]) ? ParseUnit(trim($tplArry[0])) : 0;
            $tplArry[1] = is_numeric($tplArry[1]) ? ParseUnit(trim($tplArry[1])) : (($tplArry[1] == '*') ? trim($tplArry[1]) : 0);
            $tplArry[2] = is_numeric($tplArry[2]) ? ParseUnit(trim($tplArry[2])) : 0;

            if($tplArry[0] === '*')
            {
                return array();
                break;
            }
            if( $tplArry[1] !== '*' && $tplArry[0] >= $tplArry[1])
            {
                return array();
                break;
            }
            if($last !== '' && $last != $tplArry[0])
            {
                return array();
                break;
            }
            $last = $tplArry[1];
            $planConfig[$key]['min'] = (string)$tplArry[0];
            $planConfig[$key]['max'] = (string)$tplArry[1];
            $planConfig[$key]['val'] = (string)$tplArry[2];
        }
        return $planConfig;
    }

    /**
     * 竞彩活动 - 加奖 - 投注栏hover配置
     */
    public function hoverJj()
    {
        $this->check_capacity('4_3_6', true);
        $lname = $this->input->post('lname', true);
        $startTime = $this->input->post('startTime', true);
        $endTime = $this->input->post('endTime', true);
        $slogan = $this->input->post('slogan', true);
        $plan = $this->input->post('plan', true);
        $platform = $this->input->post('platform', true);
        $playType = $this->input->post('playType', true);

        // 参数检查
        if( empty($lname) || empty($slogan) )
        {
            $result = array(
                'status' => '0',
                'message' => '缺少必要参数',
                'data' => ''
            );
            die(json_encode($result));
        }

        if(empty($startTime) || empty($endTime))
        {
            $result = array(
                'status' => '0',
                'message' => '活动时间不能为空',
                'data' => ''
            );
            die(json_encode($result));
        }

        if($startTime >= $endTime)
        {
            $result = array(
                'status' => '0',
                'message' => '活动开始时间不能大于结束时间',
                'data' => ''
            );
            die(json_encode($result));
        }

        $planConfig = '';
        if(!empty($plan))
        {
            $planConfig = $this->checkHoverPlan($plan);
        }

        if(empty($planConfig))
        {
            $result = array(
                'status' => '0',
                'message' => '配置方案错误',
                'data' => ''
            );
            die(json_encode($result));
        }

        // 组装参数
        $hoverInfo = array(
            'startTime' => $startTime,
            'endTime'   => $endTime,
            'slogan'    => $slogan,
            'platform'  => $platform ? $platform : 0,
            'playType'  => $playType ? $playType : 0,
            'params'    => json_encode($planConfig)
        );

        $this->Model_activity->saveJjHover($lname, $hoverInfo);

        $result = array(
            'status' => '1',
            'message' => '保存成功',
            'data' => ''
        );

        die(json_encode($result));
    }

    /**
     * 竞彩活动 - 加奖 - 检查hover配置
     */
    public function checkHoverPlan($plan)
    {
        $planArry = explode('|', $plan);
        $planConfig = array();
        $last = '';
        foreach ($planArry as $key => $items) 
        {
            $tplArry = explode(',', $items);

            // 格式处理 去空格
            $tplArry[0] = is_numeric($tplArry[0]) ? trim($tplArry[0]) : 0;
            $tplArry[1] = is_numeric($tplArry[1]) ? trim($tplArry[1]) : (($tplArry[1] == '*') ? trim($tplArry[1]) : 0);
            $tplArry[2] = is_numeric($tplArry[2]) ? trim($tplArry[2]) : 0;
            $tplArry[3] = is_numeric($tplArry[3]) ? trim($tplArry[3]) : 0;

            if($tplArry[0] === '*')
            {
                return array();
                break;
            }
            if( $tplArry[1] !== '*' && $tplArry[0] >= $tplArry[1])
            {
                return array();
                break;
            }
            if($last !== '' && $last != $tplArry[0])
            {
                return array();
                break;
            }
            $last = $tplArry[1];
            $planConfig[$key]['min'] = (string)$tplArry[0];
            $planConfig[$key]['max'] = (string)$tplArry[1];
            $planConfig[$key]['dg'] = (string)$tplArry[2];
            $planConfig[$key]['2c1'] = (string)$tplArry[3];
        }
        return $planConfig;
    }

    /**
     * 竞彩活动 - 加奖 - 活动详情
     */
    public function JjDetail($activityId)
    {
        $activityInfo = array();

        $info = $this->Model_activity->getJjDetail($activityId);

        // 方案说明
        $playTypeArry = array(
            0 => '单关',
            1 => '2串1'
        );

        // 加奖方式
        $ctypeArry = array(
            0 => '按金额',
            1 => '按比例'
        );

        if(!empty($info))
        {
            $this->load->library('BetCnName');
            $activityInfo['id'] = $info['id'];
            $activityInfo['startTime'] = $info['startTime'];
            $activityInfo['endTime'] = $info['endTime'];
            $activityInfo['lid'] = $info['lid'];
            $activityInfo['lname'] = BetCnName::$BetCnName[$info['lid']];
            $activityInfo['playType'] = $info['playType'];
            $activityInfo['playTypeName'] = $playTypeArry[$info['playType']];
            $activityInfo['ctype'] = $info['ctype'];
            $activityInfo['ctypeName'] = $ctypeArry[$info['ctype']];
            $activityInfo['params'] = unserialize($info['params']);
            $activityInfo['num'] = $info['num']?$info['num']:0;
            $activityInfo['money'] = $info['money']?$info['money']:0;
            $activityInfo['margin'] = $info['margin']?$info['margin']:0;
            $activityInfo['add_money'] = $info['add_money']?$info['add_money']:0;
            $activityInfo['status'] = $this->getJjStatus($info);
            $activityInfo['buyPlatform'] = str_replace(array('0', '1,2', '3'), array('网页端', '移动端', 'M版'), $info['buyPlatform']);
        }

        $this->load->view("activity/jjDetail", $activityInfo);
    }

    /**
     * 拉新活动 - 邀请人
     */
    public function lxInviter()
    {
        $this->check_capacity('4_4_1');
        $page = intval($this->input->get("p"));

        $searchData = array(
            "phone" => $this->input->get("phone", true),
            "from_channel_id" => $this->input->get("from_channel_id", true),
        );
        $page = $page <= 1 ? 1 : $page;
        $result = $this->Model_activity->getLxInviter($searchData, $page, self::NUM_PER_PAGE);

        $pageConfig = array(
            "page"     => $page,
            "npp"      => self::NUM_PER_PAGE,
            "allCount" => !empty($result[0])?$result[0]:0
        );

        $pages = get_pagination($pageConfig);

        $infos = array(
            'result'   => $result[1],
            'pages'    => $pages,
            'search'   => $searchData,
            'totalNum' => $result[2],
        );

        $this->load->view("activity/lxInviter", $infos);
    }

    /**
     * 拉新活动 - 受邀人
     */
    public function lxInvitee()
    {
        $this->check_capacity('4_4_2');
        $page = intval($this->input->get("p"));

        $searchData = array(
            "phone" => $this->input->get("phone", true),
            "to_channel_id" => $this->input->get("to_channel_id", true),
            "start_r_m" => $this->input->get("start_r_m", true),
            "end_r_m" => $this->input->get("end_r_m", true),
            "uname" => $this->input->get("uname", true),
            "puname" => $this->input->get("puname", true),
            "isReg" => $this->input->get("isReg", true),
            "isBind" => $this->input->get("isBind", true),
        );
        $page = $page <= 1 ? 1 : $page;
        $result = $this->Model_activity->getLxInvitee($searchData, $page, self::NUM_PER_PAGE);
        
        $pageConfig = array(
            "page"     => $page,
            "npp"      => self::NUM_PER_PAGE,
            "allCount" => !empty($result[0])?$result[0]:0
        );

        $pages = get_pagination($pageConfig);

        $infos = array(
            'result'   => $result[1],
            'pages'    => $pages,
            'search'   => $searchData,
            'totalNum' => $pageConfig['allCount'],
        );

        $this->load->view("activity/lxInvitee", $infos);
    }

    /**
     * 拉新活动 - 奖品配置
     */
    public function lxPrize()
    {
        $this->check_capacity('4_4_3');
        // 获取奖品配置
        $info = $this->Model_activity->getlxPrize();

        $this->load->view("activity/lxPrize", array('info' => $info));
    }

    public function updateLxPrize()
    {
        $this->check_capacity('4_4_4', true);
        $postData = $this->input->post(null, true);

        if(!empty($postData['prize']))
        {
            $prizeInfo = array();
            $count = 0;
            foreach ($postData['prize'] as $key => $items) 
            {
                $items['lv'] = is_numeric($items['lv'])?$items['lv']:0;
                // 转化为整数比较
                $lv = (doubleval($items['lv']) * 1000) / 10;
                $items['lv'] = floatval($items['lv']/100);

                $data = array(
                    'id' => $items['id'],
                    'lv' => $items['lv'],
                );
                $count = $count + $lv;
                array_push($prizeInfo, $data);
            }

            if($count == 10000)
            {
                $this->Model_activity->updatePrize($prizeInfo);
                $result = array(
                    'status' => '1',
                    'message' => '修改成功',
                    'data' => ''
                );
            }
            else
            {
                $result = array(
                    'status' => '0',
                    'message' => '预设概率总和不等于100%',
                    'data' => $count
                );
            }
        }
        else
        {
            $result = array(
                'status' => '0',
                'message' => '修改失败',
                'data' => ''
            );
        }   
        die(json_encode($result));
    }
    
    /**
     * 追号不中包赔
     */
    public function chaseActivity()
    {
        $this->check_capacity('4_5_1');
        $this->load->model('model_chase');
        $page = intval($this->input->get("p"));
        $page = $page <= 1 ? 1 : $page;
        $chaseType = $this->input->get("chaseType", TRUE);
        $searchData = array(
                "name"          => $this->input->get("name", TRUE),
                "lid"           => $this->input->get("lid", TRUE),
                "start_time"    => $this->input->get("start_time", TRUE),
                "end_time"      => $this->input->get("end_time", TRUE),
                "start_money"   => $this->input->get("start_money", TRUE),
                "end_money"     => $this->input->get("end_money", TRUE),
                "status"        => $this->input->get("status", TRUE),
                "buyPlatform"   => $this->input->get("buyPlatform", true),
                "registerChannel"       => $this->input->get("registerChannel", true),
                "chaseType"     => empty($chaseType) ? 'all' : $chaseType,
                "notBonus"     => $this->input->get("notBonus", TRUE),
                "reg_type"      => $this->input->get("reg_type", true),
        );
        $this->filterTime($searchData['start_time'], $searchData['end_time']);
        $result = $this->model_chase->listChase($searchData, $page, self::NUM_PER_PAGE);
        $pageConfig = array(
                "page"     => $page,
                "npp"      => self::NUM_PER_PAGE,
                "allCount" => $result[1]
        );
        $pages = get_pagination($pageConfig);
        $this->load->model('model_channel');
        $channelRes = $this->model_channel->getChannels();
        foreach ($channelRes as $val){
            $channels[$val['id']] = $val;
        }
        $info = array(
                "orders"   => $result[0],
                "pages"    => $pages,
                "fromType" => $this->input->get("fromType", true),
                "search"   => $searchData,
                "tj"       => $result[2],
                'channels' => $channels,
        );
        $this->load->view("activity/chaseActivity", $info);
    }

    /**
     * [createRedPack 创建派发红包活动红包]
     * @author JackLee 2017-03-21
     * @return [type] [description]
     */
    public function createRedPack()
    {
        //1.获取派发活动充值红包
        //2.获取派发活动购彩红包
        $this->check_capacity('4_2_2');
        $result = $this->Model_activity->getPfRedPack();
        $infos = array('result'=> $result);
        $this->load->view("activity/createRedPack",$infos); 
    }
    /**
     * [storeRedPack 购彩充值红包写入]
     * @author JackLee 2017-03-22
     * @return [type] [description]
     */
    public function storeRedPack()
    {
        $this->check_capacity('4_2_5', true);
        //验证必要参数的正确和合法性
        $validRes = $this->validParams($this->input->post());
        if($validRes['flag'] === false){$this->ajaxReturn('ERROR', $validRes['msg']);}
        $arr = array();
        $arr['aid'] = 7;
        $arr['p_type'] = $this->input->post('p_type', true);
        $arr['c_type'] = $this->input->post('c_type', true);
        $arr['money'] = $this->input->post('money', true)*100;
        $arr['money_bar'] = $this->input->post('money_bar', true)*100;
        $arr['ismobile_used'] = $this->input->post('ismobile_used', true) ? 1 : 0;
        $arr['use_params'] = json_encode(array('start_day'=>0,'end_day'=>$this->input->post('days', true),'money_bar'=>$arr['money_bar']));
        if($arr['p_type'] == 3)//购彩
        {
            $arr['c_name'] = $this->getCName($arr['c_type']);
            $arr['p_name'] = $arr['c_type']==101 ? $arr['c_name'].'购彩红包' : $arr['c_name'].'红包';
            $m_1 = $this->input->post('money_bar', true);
            $m_1 = $m_1 >= 10000 ? floor($m_1/10000).'万' : $m_1;
            $arr['use_desc'] = '满'.$m_1.'元减'.$this->input->post('money', true).'元';
        }
        else if($arr['p_type'] == 2)//充值
        {
            $arr['c_name'] = '';
            $m_1 = $this->input->post('money_bar', true);
            $arr['p_name'] = $m_1.'红包';
            $m_1 = $m_1 >= 10000 ? floor($m_1/10000).'万' : $m_1;
            $arr['use_desc'] = '充'.$this->input->post('money_bar', true).'元送'.$this->input->post('money', true).'元';
        }
        $tag = $this->Model_activity->storeRedPack($arr);
        if($tag == 1)
        {
            //写入日志
            $this->createRedPackLog($arr['p_name'],$arr['c_name'],$arr['use_desc'],$this->input->post('days', true),$arr['ismobile_used']);
            $this->ajaxReturn('SUCCESSS', '添加红包成功~');
        }
        else if($tag == 2)
        {
            $this->ajaxReturn('ERROR', '添加的红包已经存在~');
        }
        else
        {
            $this->ajaxReturn('ERROR', '添加红包失败~');
        }
    }
    /**
     * [validParams 验证参数合法性]
     * @author JackLee 2017-03-28
     * @param  [type] $params [description]
     * @return [type]         [description]
     */
    public function validParams($params)
    {
        if(empty($params['money']) || empty($params['money_bar']) || empty($params['days']) )
        {
            return array('flag' => false,'msg' => '必要参数为空');
        }
        else
        {
            
            if(! preg_match("/^[1-9]\d*$/",$params['money'])  
                || ! preg_match("/^[1-9]\d*$/",$params['money_bar']) 
                || ! preg_match("/^[1-9]\d*$/",$params['days']) 
                )
            {
                return array('flag' => false,'msg' => '输入的金额或者天数必须是正整数');
            }
            if($params['money'] >= $params['money_bar'])
            {
                return array('flag' => false,'msg' => '红包赠送金额大于红包金额');
            }
            if($params['money_bar'] >= 10000 && ! preg_match("/^[1-9]\d*$/",$params['money_bar']/10000) )
            {
                return array('flag' => false,'msg' => '红包金额大于1万必须为万元整数倍');
            }
            if($params['money'] > 1000)
            {
                return array('flag' => false,'msg' => '赠送金额不能大于1000元');
            }
            return array('flag' =>true,'msg' => '');
        }
    }
    /**
     * [hideRedPack 红包隐藏操作]
     * @author JackLee 2017-03-22
     * @return [type] [description]
     */
    public function hideRedPack()
    {
        $this->check_capacity('4_2_5', true);
        $redPackIds = $this->input->post('red_pack_id', true);
        $redPackIds = !$redPackIds ? array() :$redPackIds;
        $pType = $this->input->post('p_type', true);
        $tag = $this->Model_activity->hideRedPack($redPackIds,$pType);
        if($tag===true)
        {
            if(count($redPackIds)) $this->hideRedPackLog($redPackIds);
            $this->ajaxReturn('SUCCESSS', '隐藏红包成功~');
        }else{
            $this->ajaxReturn('ERROR', '隐藏红包失败~');
        } 
        
    }
    /**
     * [getCName 获取购彩红包的名称]
     * @author JackLee 2017-03-22
     * @param  [type] $key [description]
     * @return [type]      [description]
     */
    public function getCName($key)
    {
         $cate = array(
                    '101'=>'通用',
                    '102'=>'竞彩',
                    '103'=>'数字彩',
                    '104'=>'高频彩',
                    '105'=>'双色球',
                    '106'=>'大乐透',
                    '107'=>'福彩3D',
                    '108'=>'排列三',
                    '109'=>'排列五',
                    '110'=>'七乐彩',
                    '111'=>'七星彩',
                    '112'=>'竞足',
                    '113'=>'竞篮',
                    '114'=>'胜负彩',
                    '115'=>'任选九',
                    '116'=>'惊喜11选5',
                    '117'=>'老11选5',
                    '118'=>'新11选5', 
                    '119'=>'经典快3', 
                    '120'=>'快乐扑克',
                    '121'=>'老时时彩',
                    '122'=>'易快3',
                    '123'=>'红快3',
                    '124'=>'乐11选5',
                );
         return $cate[$key];
    }
    /**
     * [pullRedPack 红包派发]
     * @author JackLee 2017-03-22
     * @return [type] [description]
     */
    public function pullRedPack()
    {
        $this->check_capacity('4_2_3');
        $result = $this->Model_activity->getPfRedPack();
        $infos = array('result'=> $result);      
        $this->load->view("activity/pullRedPack",$infos);
    }

    /**
     * [storePullRedPack 批量派发数据处理]
     * @author JackLee 2017-03-23
     * @return [type] [description]
     */
    public function storePullRedPack()
    {
        $this->check_capacity('4_2_6');
        $params = $this->input->post();
        //验证生效日期
        if(strtotime($params['start_time']) < strtotime(date('Y-m-d',time()))){
            $this->returnBack('生效日不能为过去时间，请修改配置！');
        }
        
        $params['validity'] = date("Y-m-d H:i:s",strtotime($params['start_time']));
        //1 验证是否选择派发红包
        if(!count($params['red_pack_id']))
        {
            $this->returnBack('没有选择红包');
        }
        //2.验证红包个数是否合法
        if(!$this->checkRedPackNum($params))
        {
            $this->returnBack('选择的红包个数应该在1-99之间');
        }
        //单用户
        $userTag = false;
        if($params['pull_type'] == 0)
        {
            $userTag = $this->checkUser($params['user']);
        }//批量处理
        else if($params['pull_type'] == 1)
        {
            //验证文件格式
            $fileExt = strtoupper(end(explode('.', $_FILES['usersFile']['name'])));
            if($fileExt !='CSV') $this->returnBack('上传非csv格式文件');
            $filePath = $this->uploadFile(); 
            $res = $this->write($filePath);
            //删除使用完成的文件
            $this->deleteCsv();
            if($res == -1) { $this->returnBack('派发文件内容格式有误'); }
            if(isset($res['flag']) && $res['flag'] == -2) { $this->returnBack('派发文件中用户uid为'.implode(',', $res['uids']).'不符合派发条件，请修改后重试~'); }
            $userTag = count($res) ? $res : false;
        }
        if($userTag === false)
        {
            $this->returnBack('派发用户不符合派发条件');
        }
        //执行派发
        $uids = $userTag;
        $storeTag = $this->Model_activity->storePullRedPack($uids,$params);
        if($storeTag === false) $this->returnBack('提交失败');
        $this->syslog(45, "红包派发，派发期次：{$storeTag}");
        //回跳
        $this->returnBack('提交成功',1);
        //$this->redirect("/backend/Activity/auditList?suc=".'');

    }
    /**
     * [checkUser 派发插入前验证]
     * @author JackLee 2017-03-23
     * @param  [type] $params [description]
     * @return [type]         [description]
     */
    public function checkUser($params)
    {
        //多维数组
        $tag = $this->Model_activity->checkUser($params);

        return $tag;
    }
    /**
     * [ajaxCheckUser 异步验证单用户]
     * @author JackLee 2017-04-10
     * @return [type] [description]
     */
    public function ajaxCheckUser()
    {
        $tag = $this->Model_activity->checkOneUser($this->input->post('user'));
        if($tag === false )
        {
            $this->ajaxReturn('ERROR', '派发用户不符合派发条件');
        }else{
            $this->ajaxReturn('SUCCESSS', '成功');
        } 
    }
    /**
     * [auditList 红包派发审核列表]
     * @author JackLee 2017-03-23
     * @return [type] [description]
     */
    public function auditList()
    {
        $this->check_capacity('2_5_1');
        $page = intval($this->input->get("p"));
        $page = $page <= 1 ? 1 : $page;
        $uname = $this->input->get("uname", true);
        $start_time = $this->input->get("start_time", true);
        $end_time = $this->input->get("end_time", true);
        $status = $this->input->get("status", true);
        $searchData = array(
            "uname" => $uname ? $uname : "",
            "start_time" => $start_time ? $start_time : "",
            "end_time" => $end_time ? $end_time : "",
            "status" => $status!='' ? $status : '',
        );
        $gets = $this->input->get() ? $this->input->get() : array();
        if($this->checkPramsEmpty($gets))
        {
            $searchData['start_time'] = date('Y-m-d ',strtotime("-1 month")).'00:00:00';
            $searchData['end_time'] = date('Y-m-d H:i:s',strtotime(date('Y-m-d'))+60*60*24-1) ;
        }
        $result = $this->Model_activity->getAuditListData($searchData, $page, self::NUM_PER_PAGE);
        $pageConfig = array(
                "page"     => $page,
                "npp"      => self::NUM_PER_PAGE,
                "allCount" => $result[1]
        );
        $pages = get_pagination($pageConfig);
        $infos = array(
            'search'    => $searchData,
            'result'   => $result[0],
            'pages'    => $pages,
            'total' => $result[1]
        );
        $this->load->view("activity/auditList",$infos);
    }
    /**
     * [auditDetail 派发红包审核详情]
     * @author JackLee 2017-03-23
     * @return [type] [description]
     */
    public function auditDetail()
    {
        $this->check_capacity('2_5_2');
        $this->load->library('uri');
        $auditId = $this->uri->segment(4);
        $data = $this->Model_activity->getAuditDetailData($auditId);       
        $this->load->view("activity/auditDetail",$data);
    }
    /**
     * [ajaxAudit 异步审核]
     * @author JackLee 2017-03-24
     * @return [type] [description]
     */
    public function ajaxAudit()
    {
        $this->check_capacity('2_5_3', true);
        $tag = $this->Model_activity->ajaxAudit($this->input->post());
        if($tag ===true)
        {
            $status = $this->input->post('status',true);
            $id = $this->input->post('id',true);
            if($status == 1){ $status = '通过';}
            if($status == 2){ $status = '失败';}
            $this->syslog(46, "红包派发期次{$id}，审核{$status}");
            $this->ajaxReturn('SUCCESSS', '操作成功~');
        }
        else
        {
            $this->ajaxReturn('ERROR', '操作失败~');
        }
    }
    /**
     * [write 写入]
     * @author JackLee 2017-03-24
     * @return [type] [description]
     */
    public function write($filePath = '')
    {
        $file = fopen($filePath,'r');
        $fileData = array();
        while ($data = fgetcsv($file)) 
        { 
            if(empty($data) || count($data) != 2) break;//格式错误
            $fileData[] = $data;
        }
        unset($fileData[0]); //字段行
        if(!count($fileData)) return $data = -1;
        //每次发送5000条,计算发送次数
        $send_num =  ceil(count($fileData) / 5000) ;
        if($send_num == 1){
            $data = $this->Model_activity->batchWrite($fileData);
        }
        if($send_num > 1){
            $res_data = array();
            for ($i = 0 ; $i < $send_num; $i++){
                //计算开始位置
                $start = $i * 5000 ;
                $send_data = array_slice($fileData,$start,5000);
                $res = $this->Model_activity->batchWrite($send_data);
                if($res['flag'] == -2){
                    return $res;
                }
                array_push($res_data,$res);//将多次查询的结果组合在一起
            }
            //将二位素组转换成1维数组
           foreach ($res_data as $v){
                foreach ($v as $v1){
                    $data[] = $v1;
                }
           }
        }
        return $data;
    }

    /**
     * [uploadFile 文件上传]
     * @author JackLee 2017-03-24
     * @return [type] [description]
     */
    public function uploadFile()
    {
        $config['upload_path'] = dirname(BASEPATH).'/uploads/csv/';
        if(!is_dir($config['upload_path'] )){mkdir($config['upload_path'],0777,true);}
        $config['allowed_types'] = 'csv';
        $config['max_size'] = '400';
        $this->load->library('upload', $config);
        $this->upload->do_upload('usersFile');
        $data =  $this->upload->data();

        return $data['full_path'];
    }
    /**
     * [createRedPackLog 新增红包日志]
     * @author JackLee 2017-03-30
     * @param  [type] $name         [description]
     * @param  [type] $cname        [description]
     * @param  [type] $lastDays     [description]
     * @param  [type] $ismobileUsed [description]
     * @return [type]               [description]
     */
    public function createRedPackLog($name,$cname,$useDesc,$lastDays,$ismobileUsed)
    {
        $ismobileUsed = $ismobileUsed ? '（客户端专享）' : '';
        $ismobileUsed  = !empty( $ismobileUsed ) ?"，$ismobileUsed" : '';
        $redName = empty($cname) ? $useDesc.'充值红包' : $useDesc.$name;
        $cname = empty($cname) ? '' : $cname.'，';
        $this->syslog(44, "新增{$redName}:使用条件（{$cname}{$lastDays} 天{$ismobileUsed}）" );
    }
    /**
     * [hideRedPackLog 隐藏红包日志]
     * @author JackLee 2017-03-30
     * @param  [type] $redPackIds [description]
     * @return [type]             [description]
     */
    public function hideRedPackLog($redPackIds)
    {
        $redPackData = $this->Model_activity->getRedpackDataByIds($redPackIds);
        $text = array();
        foreach ($redPackData as $k => $v) 
        {
            $redName = empty($v['c_name']) ? $v['use_desc'].'充值红包' : $v['use_desc'].$v['p_name'];
            $cname = empty($v['c_name']) ? '' : $v['c_name'].'，';
            $days = json_decode($v['use_params'],true);
            $days = $days['end_day'] - $days['start_day'];
            $ismobileUsed = $v['ismobile_used'] ? '（客户端专享）' : '';
            $ismobileUsed  = !empty( $ismobileUsed ) ?"，$ismobileUsed" : '';
            $text[] = "{$redName}：使用条件（{$cname}{$days} 天{$ismobileUsed}）";

        }
        $this->syslog(44, '隐藏'.implode('、', $text));
    }
    /**
     * [checkPramsEmpty 检测空参数方法]
     * @author LiKangJian 2017-04-17
     * @param  [type] $params [description]
     * @return [type]         [description]
     */
    public function checkPramsEmpty($params)
    {
        $tag = true;
        if(!count($params)) return $tag;
        foreach ($params as $k => $v) 
        {
            if(empty($v)) return $tag = false;
        }

        return false;
    }
    /**
     * [returnBack 回退方法]
     * @author LiKangJian 2017-04-17
     * @param  [type] $error [description]
     * @param  string $flag  [description]
     * @return [type]        [description]
     */
    private function returnBack($error,$flag='')
    {
       $this->redirect("/backend/Activity/pullRedPack?error=$error&flag=$flag");
    }
    /**
     * [deleteCsv 删除处理完成的文件]
     * @author LiKangJian 2017-04-17
     * @return [type] [description]
     */
    private function deleteCsv()
    {
        $files = glob(dirname(BASEPATH).'/uploads/csv/*');
        if( empty($files) ) return ;
        foreach ($files as $file) 
        {
            if(is_file($file))
            {
                @unlink($file);
            }
        }
    }
    /**
     * [checkRedPackNum 验证选择红包个数是否合法]
     * @author LiKangJian 2017-04-17
     * @param  [type] $params [description]
     * @return [type]         [description]
     */
    private function checkRedPackNum($params)
    {
        $tag = true;
        foreach ($params['red_pack_id'] as $v) 
        {
            if($params['pack_num_'.$v] > 99 || $params['pack_num_'.$v] < 1 || !preg_match("/^[1-9]\d*$/",$params['pack_num_'.$v]) )
            {
                $tag = false;
                return $tag;
            }
        }

        return $tag;
    }
    
    public function newlxInviter()
    {
        $this->check_capacity('4_6_1');
        $page = intval($this->input->get("p"));

        $searchData = array(
            "phone" => $this->input->get("phone", true),
            "from_channel_id" => $this->input->get("from_channel_id", true),
        );
        $page = $page <= 1 ? 1 : $page;
        $result = $this->Model_activity->getNewLxInviter($searchData, $page, self::NUM_PER_PAGE);

        $pageConfig = array(
            "page"     => $page,
            "npp"      => self::NUM_PER_PAGE,
            "allCount" => !empty($result[0])?count($result[0]):0
        );

        $pages = get_pagination($pageConfig);

        $infos = array(
            'result'   => $result[1],
            'pages'    => $pages,
            'search'   => $searchData,
            'totalNum' => $result[2],
        );

        $this->load->view("activity/newlxInviter", $infos);
    }
    
    /**
     * 新年活动
     */
    public function newYearActivity()
    {
        $this->check_capacity('4_7_1');
        $page = intval($this->input->get("p"));
        
        $searchData = array(
            "phone" => $this->input->get("phone", true),
            "uname" => $this->input->get("uname", true),
            "platform" => $this->input->get("platform", true),
        );
        $page = $page <= 1 ? 1 : $page;
        $result = $this->Model_activity->getNewYearActivity($searchData, $page, self::NUM_PER_PAGE);
        
        $pageConfig = array(
            "page"     => $page,
            "npp"      => self::NUM_PER_PAGE,
            "allCount" => $result[1]['joinNum']
        );
        
        $pages = get_pagination($pageConfig);
        
        $infos = array(
            'result'   => $result[0],
            'pages'    => $pages,
            'search'   => $searchData,
            'totalNum' => $result[1],
        );
        
        $this->load->view("activity/newYearActivity", $infos);
    }
    
    /**
     * 拉新活动 - 受邀人
     */
    public function newlxInvitee()
    {
        $this->check_capacity('4_6_2');
        $page = intval($this->input->get("p"));

        $searchData = array(
            "phone" => $this->input->get("phone", true),
            "to_channel_id" => $this->input->get("to_channel_id", true),
            "start_r_m" => $this->input->get("start_r_m", true),
            "end_r_m" => $this->input->get("end_r_m", true),
            "uname" => $this->input->get("uname", true),
            "puname" => $this->input->get("puname", true),
            "status" => $this->input->get("status", true),
        );
        $page = $page <= 1 ? 1 : $page;
        $result = $this->Model_activity->getNewLxInvitee($searchData, $page, self::NUM_PER_PAGE);
        
        $pageConfig = array(
            "page"     => $page,
            "npp"      => self::NUM_PER_PAGE,
            "allCount" => !empty($result[0])?$result[0]:0
        );

        $pages = get_pagination($pageConfig);

        $infos = array(
            'result'   => $result[1],
            'pages'    => $pages,
            'search'   => $searchData,
            'totalNum' => $pageConfig['allCount'],
        );

        $this->load->view("activity/newlxInvitee", $infos);
    }
    
    /**
     * 新年活动 - 受邀人
     */
    public function newYearInvitee()
    {
        $this->check_capacity('4_7_2');
        $page = intval($this->input->get("p"));
        
        $searchData = array(
            "phone" => $this->input->get("phone", true),
            "start_r_m" => $this->input->get("start_r_m", true),
            "end_r_m" => $this->input->get("end_r_m", true),
            "uname" => $this->input->get("uname", true),
            "puname" => $this->input->get("puname", true),
            "status" => $this->input->get("status", true),
        );
        $page = $page <= 1 ? 1 : $page;
        $result = $this->Model_activity->getNewYearInvitee($searchData, $page, self::NUM_PER_PAGE);
        
        $pageConfig = array(
            "page"     => $page,
            "npp"      => self::NUM_PER_PAGE,
            "allCount" => !empty($result[0])?$result[0]:0
        );
        
        $pages = get_pagination($pageConfig);
        
        $infos = array(
            'result'   => $result[1],
            'pages'    => $pages,
            'search'   => $searchData,
            'totalNum' => $pageConfig['allCount'],
        );
        
        $this->load->view("activity/newYearInvitee", $infos);
    }
    
    public function managelxInvitee()
    {
        $this->check_capacity('4_6_3');
        $ios_status = $this->input->get("ios_status", true);
        $app_status = $this->input->get("app_status", true);
        $ios_content = $this->input->get("ios_content", true) ? $this->input->get("ios_content", true) : '';
        $app_content = $this->input->get("app_content", true) ? $this->input->get("app_content", true) : '';
        if($ios_status>=1 || $app_status>=1)
        {
            $this->check_capacity('4_6_4');
            $this->Model_activity->updateActivityRemark(9, $ios_status, $app_status, $ios_content, $app_content);
        }
        $activity = $this->Model_activity->getActivityInfo(9);
        $open_status = json_decode($activity['remark'], true);
        $this->load->view("activity/managelxInvitee",$open_status);
    }
    
    /**
     * 新年活动管理
     */
    public function manageNewYearInvitee()
    {
        $this->check_capacity('4_7_3');
        $delete_flag = $this->input->get("delete_flag", true);
        if($delete_flag>=1)
        {
            $this->check_capacity('4_7_4');
            if($delete_flag == '2')
            {
                $delete_flag = 0;
            }
            
            $this->Model_activity->activity_update(13, array('delete_flag' => $delete_flag));
            $name = $delete_flag == 0 ? '开启' : '关闭';
            $this->syslog(67, "活动状态修改为" . $name);
        }
        $activity = $this->Model_activity->getActivityInfo(13);
        $delete_flag = $activity['delete_flag'] == '0' ? 2 : $activity['delete_flag'];
        $this->load->view("activity/manageNewYearInvitee",array('delete_flag' => $delete_flag));
    }
    
    /**
     * 新年活动 - 奖品配置
     */
    public function newYearPrize()
    {
        $this->check_capacity('4_7_5');
        // 获取奖品配置
        $info = $this->Model_activity->getNewYearPrize();
        $activity = $this->Model_activity->getActivityInfo(13);
        
        $this->load->view("activity/newYearPrize", array('info' => $info, 'activity' => $activity));
    }
    
    /**
     * 新年活动-奖品配置
     */
    public function updateNewYearPrize()
    {
        $this->check_capacity('4_7_6', true);
        $postData = $this->input->post(null, true);
        
        $message = '';
        if(!empty($postData['prize']))
        {
            $prizeInfo = array();
            $count = 0;
            foreach ($postData['prize'] as $key => $items)
            {
                $items['lv'] = is_numeric($items['lv'])?$items['lv']:0;
                $message .= $this->getReadName($items['id']) . ' 概率修改为：' . $items['lv'] . '%,库存为：' . $items['num'] . ';';
                // 转化为整数比较
                $lv = (doubleval($items['lv']) * 1000) / 10;
                $items['lv'] = floatval($items['lv']/100);
                
                $data = array(
                    'id' => $items['id'],
                    'lv' => $items['lv'],
                    'num' => $items['num'],
                );
                $count = $count + $lv;
                array_push($prizeInfo, $data);
            }
            
            if($count == 10000)
            {
                $this->Model_activity->updateNewYearPrize($prizeInfo);
                $result = array(
                    'status' => '1',
                    'message' => '修改成功',
                    'data' => ''
                );
                $this->syslog(67, $message);
            }
            else
            {
                $result = array(
                    'status' => '0',
                    'message' => '预设概率总和不等于100%',
                    'data' => $count
                );
            }
        }
        else
        {
            $result = array(
                'status' => '0',
                'message' => '修改失败',
                'data' => ''
            );
        }
        die(json_encode($result));
    }
    
    /**
     * 返回新年红包名
     * @param unknown $id
     * @return string
     */
    private function getReadName($id)
    {
        $arr = array(
            '1' => '3元购彩红包',
            '2' => '5元购彩红包',
            '3' => '10元购彩红包',
            '4' => '20元购彩红包',
            '5' => '50元购彩红包',
            '6' => '166元彩金红包',
            '7' => '888元彩金红包',
            '8' => '8888元彩金红包',
        );
        
        return $arr[$id];
    }
    
    public function newYearChjList()
    {
        $this->check_capacity('4_7_7');
        $page = intval($this->input->get("p"));
        
        $searchData = array(
            "uname" => $this->input->get("uname", true),
            "start_r_m" => $this->input->get("start_r_m", true),
            "end_r_m" => $this->input->get("end_r_m", true),
            "award" => $this->input->get("award", true),
        );
        $page = $page <= 1 ? 1 : $page;
        $result = $this->Model_activity->newYearChjList($searchData, $page, self::NUM_PER_PAGE);
        $prize = $this->Model_activity->getNewYearPrize();
        $pageConfig = array(
            "page"     => $page,
            "npp"      => self::NUM_PER_PAGE,
            "allCount" => $result[1]['total'],
        );
        
        $pages = get_pagination($pageConfig);
        
        $infos = array(
            'result'   => $result[0],
            'pages'    => $pages,
            'search'   => $searchData,
            'totalNum' => $result[1],
            'prize'    => $prize,
        );
        
        $this->load->view("activity/newYearChjList", $infos);
    }
    
    public function newActivityJc()
    {
        $this->check_capacity('4_3_1');
        $page = intval($this->input->get("p"));
        $searchData = array(
            "name" => $this->input->get("name", true),
            "platform" => $this->input->get("platform", true),
            "hongbao" => $this->input->get("hongbao", true),
            "activity_issue" => $this->input->get("activity_issue", true),
            "start_r_m" => $this->input->get("start_r_m", true),
            "end_r_m" => $this->input->get("end_r_m", true),
            "start_r_c" => $this->input->get("start_r_c", true),
            "end_r_c" => $this->input->get("end_r_c", true),            
        );

        $page = $page <= 1 ? 1 : $page;
        $result = $this->Model_activity->NewJcActivity($searchData, $page, self::NUM_PER_PAGE);

        $pageConfig = array(
            "page"     => $page,
            "npp"      => self::NUM_PER_PAGE,
            "allCount" => $result[1]
        );

        $pages = get_pagination($pageConfig);

        $infos = array(
            'result'   => $result[0],
            'pages'    => $pages,
            'search'   => $searchData
        );

        $this->load->view("activity/newActivityJc", $infos);
    }
    
    public function newManageJc()
    {
        $this->check_capacity('4_3_2');
        $result = $this->Model_activity->getJcbpInfo();
        $types = array(
            'spf' => '胜平负',
            'rqspf' => '让球胜平负',
            'sf' => '胜负',
            'rfsf' => '让分胜负'
        );
        $status = array(
            0 => '等待开奖',
            240 => '等待开奖',
            500 => '等待开奖',
            1000 => '未中奖',
            2000 => '已中奖'
        );
        foreach ($result as $k => $res) {
            $plans = json_decode($res['plan'], true);
            $fangan = array();
            $playtype = array();
            foreach ($plans as $plan) {
                $fangan[] = $plan['mid'] . '-' . $plan['res'];
                $playtype[] = $types[$plan['playType']];
            }
            $result[$k]['plan'] = implode('/', $fangan);
            $result[$k]['playType'] = implode('/', $playtype);
            $result[$k]['status'] = $status[$res['status']];
        }
        $this->load->view("activity/newManageJc", array('result' => $result));
    }
    
    public function createJcbp()
    {
        $this->check_capacity('4_3_5');
        $params = $this->input->post();
        $id = $this->Model_activity->getLastJcbp();
        if (!empty($params)) {
            if(!$params['start'] || !$params['end']){
                $this->ajaxReturn('n', '必选选择活动时间');
            }
            if ($params['start'] >= $params['end']) {
                $this->ajaxReturn('n', '开始时间必须小于结束时间');
            }
            if(!preg_match("/^[0-9]+$/",$params['money'])){
                $this->ajaxReturn('n', '金额必须是正整数');
            }
            if(!$params['issue1']){
               $this->ajaxReturn('n', '场次1必须选择'); 
            }
            if($params['type']==0 && !$params['issue2']){
                $this->ajaxReturn('n', '场次2必须选择'); 
            }
            if ($params['lid'] == 42) {
                $matchInfo = $this->Model_activity->getJcMatchInfo();
            } else {
                $matchInfo = $this->Model_activity->getJclqMatchInfo();
            }
            if (empty($matchInfo[$params['issue1']])) {
                $this->ajaxReturn('n', '所选场次1不在在售期内');
            }
            if($params['type']==0 && empty($matchInfo[$params['issue2']])){
                $this->ajaxReturn('n', '所选场次2不在在售期内');
            }
            // 上架时间
            $endTime = date('Y-m-d H:i:s', substr($matchInfo[$params['issue1']]['jzdt'], 0, 10));
            if ($params['type'] == 0) {
                $time = date('Y-m-d H:i:s', substr($matchInfo[$params['issue2']]['jzdt'], 0, 10));
                if ($time < $endTime) {
                    $endTime = $time;
                }
            }
            if ($params['end'] > $endTime) {
                $this->ajaxReturn('n', '活动截止时间不能超过比赛截止时间：' . $endTime);
            }
            if ($params['type'] == 1) {
                if ($params['playtype1'] == 0) {
                    if (!$matchInfo[$params['issue1']]['spfFu']) {
                        $this->ajaxReturn('n', '场次1比赛不支持单关');
                    }
                } else {
                    if (!$matchInfo[$params['issue1']]['rqspfFu']) {
                        $this->ajaxReturn('n', '场次1比赛不支持单关');
                    }
                }
            }
            // 玩法选择
            if ($params['lid'] == 42) {
                $playTypeArry = array(
                    0 => 'spfGd',
                    1 => 'rqspfGd'
                );
            } else {
                $playTypeArry = array(
                    0 => 'sfGd',
                    1 => 'rfsfGd'
                );
            }
            if (empty($playTypeArry[$params['playtype1']]) || empty($matchInfo[$params['issue1']][$playTypeArry[$params['playtype1']]])) {
                $this->ajaxReturn('n', '场次1比赛不支持此玩法');
            }
            if ($params['type'] == 0) {
                if (empty($playTypeArry[$params['playtype2']]) || empty($matchInfo[$params['issue2']][$playTypeArry[$params['playtype2']]])) {
                    $this->ajaxReturn('n', '场次2比赛不支持此玩法');
                }
            }
            $data = array(
                'lid' => $params['lid'],
                'playType' => ($params['type'] == 0) ? 2 : 1,
                'payType' => ($params['paystatus'] == 1) ? 0 : 1,
                'startTime' => $params['start'],
                'endTime' => $params['end'],
                'buyMoney' => $params['money'] * 100,
                'plan' => array()
            );
            if ($params['lid'] == 42) {
                $playTypeArry = array(
                    0 => 'spf',
                    1 => 'rqspf'
                );
                $data['plan'][] = array(
                    'mid' => $params['issue1'],
                    'playType' => $playTypeArry[$params['playtype1']],
                    'let' => $matchInfo[$params['issue1']]['let'],
                    'sp' => $matchInfo[$params['issue1']][$playTypeArry[$params['playtype1']] . 'Sp3'] . ',' . $matchInfo[$params['issue1']][$playTypeArry[$params['playtype1']] . 'Sp1'] . ',' . $matchInfo[$params['issue1']][$playTypeArry[$params['playtype1']] . 'Sp0'],
                    'res' => $params['chose1'],
                    'issue' => $matchInfo[$params['issue1']]['issue'],
                    'nameSname' => $matchInfo[$params['issue1']]['nameSname'],
                    'homeSname' => $matchInfo[$params['issue1']]['homeSname'],
                    'awarySname' => $matchInfo[$params['issue1']]['awarySname'],
                    'dt' => date('Y-m-d H:i:s', substr($matchInfo[$params['issue1']]['dt'], 0, 10)),
                    'jzdt' => date('Y-m-d H:i:s', substr($matchInfo[$params['issue1']]['jzdt'], 0, 10))
                );
                if ($params['type'] == 0) {
                    $data['plan'][] = array(
                        'mid' => $params['issue2'],
                        'playType' => $playTypeArry[$params['playtype2']],
                        'let' => $matchInfo[$params['issue2']]['let'],
                        'sp' => $matchInfo[$params['issue2']][$playTypeArry[$params['playtype2']] . 'Sp3'] . ',' . $matchInfo[$params['issue2']][$playTypeArry[$params['playtype2']] . 'Sp1'] . ',' . $matchInfo[$params['issue2']][$playTypeArry[$params['playtype2']] . 'Sp0'],
                        'res' => $params['chose2'],
                        'issue' => $matchInfo[$params['issue2']]['issue'],
                        'nameSname' => $matchInfo[$params['issue2']]['nameSname'],
                        'homeSname' => $matchInfo[$params['issue2']]['homeSname'],
                        'awarySname' => $matchInfo[$params['issue2']]['awarySname'],
                        'dt' => date('Y-m-d H:i:s', substr($matchInfo[$params['issue2']]['dt'], 0, 10)),
                        'jzdt' => date('Y-m-d H:i:s', substr($matchInfo[$params['issue2']]['jzdt'], 0, 10))
                    );
                }
            } else {
                $playTypeArry = array(
                    0 => 'sf',
                    1 => 'rfsf'
                );
                $data['plan'][] = array(
                    'mid' => $params['issue1'],
                    'playType' => $playTypeArry[$params['playtype1']],
                    'let' => $matchInfo[$params['issue1']]['let'],
                    'sp' => $matchInfo[$params['issue1']][$playTypeArry[$params['playtype1']] . 'Hs'] . ',' . $matchInfo[$params['issue1']][$playTypeArry[$params['playtype1']] . 'Hf'],
                    'res' => $params['chose1'],
                    'issue' => $matchInfo[$params['issue1']]['issue'],
                    'nameSname' => $matchInfo[$params['issue1']]['nameSname'],
                    'homeSname' => $matchInfo[$params['issue1']]['homeSname'],
                    'awarySname' => $matchInfo[$params['issue1']]['awarySname'],
                    'dt' => date('Y-m-d H:i:s', substr($matchInfo[$params['issue1']]['dt'], 0, 10)),
                    'jzdt' => date('Y-m-d H:i:s', substr($matchInfo[$params['issue1']]['jzdt'], 0, 10))
                );
                if ($params['type'] == 0) {
                    $data['plan'][] = array(
                        'mid' => $params['issue2'],
                        'playType' => $playTypeArry[$params['playtype2']],
                        'let' => $matchInfo[$params['issue2']]['let'],
                        'sp' => $matchInfo[$params['issue2']][$playTypeArry[$params['playtype2']] . 'Hs'] . ',' . $matchInfo[$params['issue2']][$playTypeArry[$params['playtype2']] . 'Hf'],
                        'res' => $params['chose2'],
                        'issue' => $matchInfo[$params['issue2']]['issue'],
                        'nameSname' => $matchInfo[$params['issue2']]['nameSname'],
                        'homeSname' => $matchInfo[$params['issue2']]['homeSname'],
                        'awarySname' => $matchInfo[$params['issue2']]['awarySname'],
                        'dt' => date('Y-m-d H:i:s', substr($matchInfo[$params['issue2']]['dt'], 0, 10)),
                        'jzdt' => date('Y-m-d H:i:s', substr($matchInfo[$params['issue2']]['jzdt'], 0, 10))
                    );
                }
            }
            $this->Model_activity->insertJcbp($data);
            $this->ajaxReturn('y', '创建成功'); 
        }
        $this->load->view("activity/createJcbp", array('id' => $id));
    }
    
    public function getJcinfo()
    {
        $params = $this->input->post();
        if ($params['lid'] == 42) {
            $matchInfo = $this->Model_activity->getJcMatchInfo();
        } else {
            $matchInfo = $this->Model_activity->getJclqMatchInfo();
        }
        if (empty($matchInfo[$params['issue1']])) {
            $this->ajaxReturn('n', '所选场次1不在在售期内');
        }
        if ($params['type'] == 0 && empty($matchInfo[$params['issue2']])) {
            $this->ajaxReturn('n', '所选场次2不在在售期内');
        }
        if ($params['lid'] == 42) {
            $playTypeArry = array(
                0 => '胜平负玩法',
                1 => '让球胜平负玩法'
            );
        } else {
            $playTypeArry = array(
                0 => '胜负玩法',
                1 => '让分胜负玩法'
            );
        }
        $chose = array(
            0 => array(
                3=>'主胜',
                1=>'主平',
                0=>'主负'
            ),
            1 => array(
                3=>'主让胜',
                1=>'主让平',
                0=>'主让负'
            )
        );
        if ($params['lid'] == 42) {
            $str = $matchInfo[$params['issue1']]['weekId'] . ' ' . $matchInfo[$params['issue1']]['home'] . ' VS ' . $matchInfo[$params['issue1']]['awary'] . ' ' . $playTypeArry[$params['playtype1']] . ' ' . $chose[$params['playtype1']][$params['chose1']];
            if ($params['type'] == 0) {
                $str.='<br>串 ' . $matchInfo[$params['issue2']]['weekId'] . ' ' . $matchInfo[$params['issue2']]['home'] . ' VS ' . $matchInfo[$params['issue2']]['awary'] . ' ' . $playTypeArry[$params['playtype2']] . ' ' . $chose[$params['playtype2']][$params['chose2']];
            }
        } else {
            $str = $matchInfo[$params['issue1']]['weekId'] . ' ' . $matchInfo[$params['issue1']]['awary'] . ' VS ' . $matchInfo[$params['issue1']]['home'] . ' ' . $playTypeArry[$params['playtype1']] . ' ' . $chose[$params['playtype1']][$params['chose1']];
            if ($params['type'] == 0) {
                $str.='<br>串 ' . $matchInfo[$params['issue2']]['weekId'] . ' ' . $matchInfo[$params['issue2']]['awary'] . ' VS ' . $matchInfo[$params['issue2']]['home'] . ' ' . $playTypeArry[$params['playtype2']] . ' ' . $chose[$params['playtype2']][$params['chose2']];
            }
        }
        $this->ajaxReturn('y', $str);
    }

    // 排行榜审核
    public function rankCheck()
    {    
        $this->check_capacity('2_7_1');
        $page = intval($this->input->get("p"));
        $page = $page <= 1 ? 1 : $page;
        $searchData = array(
            "start_time"    =>  $this->input->get("start_time", TRUE),
            "end_time"      =>  $this->input->get("end_time", TRUE),
            "status"        =>  $this->input->get("status", TRUE) ? $this->input->get("status", TRUE) : 0,
        );
        $this->filterTime($searchData['start_time'], $searchData['end_time']);
        $result = $this->Model_activity->rankCheckList($searchData, $page, self::NUM_PER_PAGE);

        $pageConfig = array(
            "page"     => $page,
            "npp"      => self::NUM_PER_PAGE,
            "allCount" => $result[1]
        );

        $pages = get_pagination($pageConfig);

        $list = array();
        if(!empty($result[0]))
        {
            foreach ($result[0] as $items) 
            {
                $data = array(
                    'id'            =>  $items['id'],
                    'issue'         =>  $items['pissue'],
                    'lname'         =>  $this->getRankLname($items['plid']),
                    'start_time'    =>  $items['start_time'],
                    'end_time'      =>  $items['end_time'],
                    'lids'          =>  $this->getRankLids($items['lids']),
                    'created'       =>  $items['created'],
                    'cstate'        =>  $items['cstate'],
                    'statusMsg'     =>  $this->getRankCheckStatus($items['cstate']),
                );
                array_push($list, $data);
            }
        }

        $info = array(
            'result'    =>  $list,
            'pages'     =>  $pages,
            'search'    =>  $searchData
        );

        $this->load->view("activity/rankCheck", $info);
    }

    public function getRankLname($plid)
    {
        $plidArr = array(
            '1' =>  '11选5系列',
            '2' =>  '快3系列',
            '3' =>  '竞彩系列',
        );
        return $plidArr[$plid];
    }

    public function getRankLids($lids)
    {
        $this->load->library('BetCnName');
        $lidArr = array();
        foreach (explode(',', $lids) as $lid) 
        {
            array_push($lidArr, BetCnName::$BetCnName[$lid]);
        }
        return implode(',', $lidArr);
    }

    public function getRankCheckStatus($status)
    {
        if($status == '2')
        {
            $statusMsg = '审核失败';
        }
        elseif($status == '1')
        {
            $statusMsg = '审核成功';
        }
        else
        {
            $statusMsg = '未审核';
        }
        return $statusMsg;
    }

    // 更新审核状态
    public function updateRankCheck()
    {
        $this->check_capacity('2_7_2', true);

        $id = $this->input->post("id", true);
        $status = $this->input->post("status", true);

        $update = $this->Model_activity->updateRankCheck($id, $status);

        if($update['status'])
        {
            if($status)
            {
                $log = "新建活动：" . $this->getRankLname($update['info']['plid']) . "；";
                $log .= "期次：" . $update['info']['pissue'] . "；";
                $log .= "审核成功";
                $this->syslog(73, $log);
            }
            $result = array(
                'status'    =>  'y', 
                'message'   =>  $update['message'],
            );
        }
        else
        {
            $result = array(
                'status'    =>  'n', 
                'message'   =>  $update['message'],
            );
        }
        die(json_encode($result));
    }

    // 中奖排行榜
    public function rankActivity()
    {
        // 彩种系列
        $plidArr = array(
            '1' =>  '11选5系列',
            '2' =>  '快3系列',
            '3' =>  '竞彩系列',
        );

        // 活动状态
        $statusArr = array(
            '1' =>  '全部',
            '2' =>  '未开始',
            '3' =>  '进行中',
            '4' =>  '已截止',
        );

        // 派奖状态
        $cstateArr = array(
            '1' =>  '全部',
            '2' =>  '未派奖',
            '3' =>  '已派奖',
        );

        $this->check_capacity('4_8_1');
        $page = intval($this->input->get("p"));
        $searchData = array(
            "plid"      =>  $this->input->get("plid", true) ? $this->input->get("plid", true) : '1',
            "status"    =>  $this->input->get("status", true) ? $this->input->get("status", true) : '1',
            "cstate"    =>  $this->input->get("cstate", true) ? $this->input->get("cstate", true) : '1',  
        );

        $page = $page <= 1 ? 1 : $page;
        $result = $this->Model_activity->rankList($searchData, $page, self::NUM_PER_PAGE);

        $pageConfig = array(
            "page"     => $page,
            "npp"      => self::NUM_PER_PAGE,
            "allCount" => $result[1]
        );

        $pages = get_pagination($pageConfig);

        $list = array();
        if(!empty($result[0]))
        {
            foreach ($result[0] as $items) 
            {
                $data = array(
                    'id'            =>  $items['id'],
                    'plid'          =>  $items['plid'],
                    'issue'         =>  $items['pissue'],
                    'start_time'    =>  $items['start_time'],
                    'end_time'      =>  $items['end_time'],
                    'lids'          =>  $this->getRankLids($items['lids']),
                    'totalNum'      =>  $items['totalNum'],
                    'totalMoney'    =>  $items['totalMoney'],
                    'totalMargin'   =>  $items['totalMargin'],
                    'totalAdd'      =>  $items['totalAdd'],
                    'statusMsg'     =>  $this->getRankStatusMsg($items['start_time'], $items['end_time']),
                    'cstateMsg'     =>  $this->getRankCstateMsg($items['cstate']),
                );
                array_push($list, $data);
            }
        }

        $infos = array(
            'result'    =>  $list,
            'pages'     =>  $pages,
            'search'    =>  $searchData,
            'plidArr'   =>  $plidArr,
            'statusArr' =>  $statusArr,
            'cstateArr' =>  $cstateArr,
            'total'     =>  $result[2],
        );

        $this->load->view("activity/rankActivity", $infos);
    }

    public function getRankStatusMsg($start_time, $end_time)
    {
        if($start_time < date("Y-m-d H:i:s") && $end_time > date("Y-m-d H:i:s"))
        {
            $msg = "进行中";
        }
        elseif($start_time > date("Y-m-d H:i:s"))
        {
            $msg = "未开始";
        }
        else
        {
            $msg = "已截止";
        }
        return $msg;
    }

    public function getRankCstateMsg($cstate)
    {
        $msg = "未派奖";
        if($cstate)
        {
            $msg = "已派奖";
        }
        return $msg;
    }

    // 图片上传
    public function uploadbanner($index, $type = '')
    {
        if (! file_exists ( "../uploads/banner/" ))
        {
            mkdir ( "../uploads/banner/" );
        }
        
        $config ['upload_path'] = "../uploads/banner/";
        $config ['allowed_types'] = 'jpg|png|bmp|jpeg';
        $extension = pathinfo ( $_FILES ['file'] ['name'], PATHINFO_EXTENSION );
        
        $config ['max_size'] = 10240;
        $this->load->library ( 'upload', $config );

        if ($this->upload->do_upload ( 'file' ))
        {
            $data = $this->upload->data ();
            $res = array (
                'name'  => $data ['file_name'],
                'index' => $index,
                'path'  => "//{$this->config->item('base_url')}/uploads/banner/",
                'type'  => $type,
            );
            exit ( json_encode ( $res ) );
        } 
        else
        {
            $error = $this->upload->display_errors ();
            exit ( $error );
        }
    }

    // 文本上传
    public function uplaodTxtFile($index, $type = '')
    {
        if(!file_exists("../uploads/txt/"))
        {
            mkdir("../uploads/txt/");
        }
        
        $config['upload_path'] = "../uploads/txt/";
        $config['allowed_types'] = 'txt';
        $extension = pathinfo($_FILES['file']['name'], PATHINFO_EXTENSION);

        $config ['max_size'] = 10240;
        $this->load->library('upload', $config);

        if($this->upload->do_upload('file'))
        {
            $data = $this->upload->data();

            $path = $config['upload_path'] . $data['file_name'];
            move_uploaded_file($_FILES['file']['tmp_name'], $path);
            $handle = fopen($path, 'r');
            $txts = '';
            if($handle) 
            {
                $txt = fread($handle, filesize($path));
                $txt = iconv('gbk', 'UTF-8', $txt);
                $txts .= '<li>' . $txt . '</li>';
                $txts = str_replace(PHP_EOL, '</li><li>', $txts);
                fclose($handle);
            }

            $res = array(
                'name'  => $data['file_name'],
                'txt'   => $txts,
                'type'  => $type,
            );

            @unlink($path);
            exit ( json_encode ( $res ) );
        } 
        else
        {
            $error = $this->upload->display_errors ();
            exit ( $error );
        }
    }

    // 创建审核活动
    public function createCheckRank()
    {
        $this->check_capacity('4_8_2', true);
        $postData = $this->input->post(null, true);
        // 参数检查
        if(!in_array($postData['plid'], array('1', '2', '3')))
        {
            $result = array(
                'status'    =>  'n',
                'message'   =>  '彩种系列参数错误',
            );
            die(json_encode($result));
        }

        if(empty($postData['lids']))
        {
            $result = array(
                'status'    =>  'n',
                'message'   =>  '涉及彩种参数错误',
            );
            die(json_encode($result));
        }

        // 获取最近活动期次
        $configInfo = $this->Model_activity->getLastRankConfig($postData['plid']);

        $info = $this->Model_activity->checkRankConfig($postData['plid']);
        if(!empty($info))
        {
            $result = array(
                'status'    =>  'n',
                'message'   =>  '该彩种系列有活动待审核，请先审核',
            );
            die(json_encode($result));
        }

        if(!empty($configInfo) && $configInfo['end_time'] >= $postData['startTime'])
        {
            $result = array(
                'status'    =>  'n',
                'message'   =>  '您选择的时间段错误，请核对后操作',
            );
            die(json_encode($result));
        }

        if($postData['startTime'] >= $postData['endTime'])
        {
            $result = array(
                'status'    =>  'n',
                'message'   =>  '您选择的时间段错误，请核对后操作',
            );
            die(json_encode($result));
        }

        $planConfig = array();
        if(!empty($postData['plan']))
        {
            $planConfig = $this->checkRankPlan($postData['plan']);
        }
        if(empty($planConfig['planConfig']))
        {
            $result = array(
                'status'    =>  'n',
                'message'   =>  '奖励配置错误',
            );
            die(json_encode($result));
        }

        // 组装数据
        $data = array(
            'plid'          =>  $postData['plid'],
            'pissue'        =>  (!empty($configInfo['pissue'])) ? $configInfo['pissue'] + 1 : 1,
            'lids'          =>  $postData['lids'],
            'start_time'    =>  $postData['startTime'],
            'end_time'      =>  $postData['endTime'],
            'statistics_end_time' =>    $this->getStatisticsEndTime($postData['plid'], $postData['endTime']),
            'imgUrl'        =>  $postData['imgUrl'],
            'rule'          =>  $postData['rule'],
            'extra'         =>  json_encode($planConfig['planConfig']),
            'cstate'        =>  0,
        );

        $this->Model_activity->insertRankCheck($data);

        $log = "新建活动：" . $this->getRankLname($data['plid']) . "；";
        $log .= "期次：" . $data['pissue'] . "；";
        $log .= "活动时间：" . $data['start_time'] . "至" . $data['end_time'] . "；";
        $log .= "参与彩种：" . $this->getRankLids($data['lids']) . "；";
        $log .= "加奖总额：" . ParseUnit($planConfig['totalMoney'], 1) . "元";
        $this->syslog(72, $log);

        $result = array(
            'status'    =>  'y',
            'message'   =>  '活动新建成功，待人工审核',
        );
        die(json_encode($result));
    }

    public function checkRankPlan($plan)
    {
        $planConfig = array();
        $last = '';
        $totalMoney = 0;
        foreach (explode('|', $plan) as $items) 
        {
            $tplArry = explode(',', $items);

            // 格式处理 去空格 元转分
            $tplArry[0] = is_numeric($tplArry[0]) ? trim($tplArry[0]) : 0;
            $tplArry[1] = is_numeric($tplArry[1]) ? trim($tplArry[1]) : 0;
            $tplArry[2] = is_numeric($tplArry[2]) ? ParseUnit(trim($tplArry[2])) : 0;

            if($tplArry[0] > $tplArry[1])
            {
                return array();
                break;
            }

            if($last !== '' && $last != ($tplArry[0] - 1))
            {
                return array();
                break;
            }
            $last = $tplArry[1];

            $config = array(
                'min'   =>  (string)$tplArry[0],
                'max'   =>  (string)$tplArry[1],
                'money' =>  (string)$tplArry[2],
            );
            $mulit = $tplArry[1] - $tplArry[0] + 1;
            $mulit = $mulit ? $mulit : 1;
            $totalMoney += ($config['money'] * $mulit);
            array_push($planConfig, $config);
        }
        return array('planConfig' => $planConfig, 'totalMoney' => $totalMoney);
    }

    public function getStatisticsEndTime($plid, $end_time)
    {
        if($plid == '3')
        {
            return date("Y-m-d 17:00:00", strtotime("{$end_time} +1 day"));
        }
        else
        {
            return date("Y-m-d 10:00:00", strtotime("{$end_time} +1 day"));
        }
    }

    public function rankCheckDetail($id)
    {
        $this->check_capacity('4_8_4');
        $info = $this->Model_activity->getRankCheckById($id);

        if($info['cstate'] == '1')
        {
            $info = $this->Model_activity->getRankConfigDetail($info['plid'], $info['pissue']);
            $list = $this->rankDetail($info, 1);
        }
        else
        {
            $list = $this->rankDetail($info, 0);
        }    

        $this->load->view("activity/rankDetail", $list);
    }

    public function rankConfigDetail($plid, $pissue)
    {
        $this->check_capacity('4_8_4');
        $info = $this->Model_activity->getRankConfigDetail($plid, $pissue);
        $list = $this->rankDetail($info, 1);
        $this->load->view("activity/rankDetail", $list);
    }

    public function rankDetail($info, $flag = 0)
    {
        $plidArr = array(
            '1' =>  'syxw',
            '2' =>  'ks',
            '3' =>  'jc',
        );
        $list = array(
            'pissue'        =>  $info['pissue'],
            'lname'         =>  $this->getRankLname($info['plid']),
            'start_time'    =>  $info['start_time'],
            'end_time'      =>  $info['end_time'],
            'lids'          =>  $this->getRankLids($info['lids']),
            'totalNum'      =>  $info['totalNum'] ? $info['totalNum'] : 0,
            'totalMoney'    =>  $info['totalMoney'] ? $info['totalMoney'] : 0,
            'totalMargin'   =>  $info['totalMargin'] ? $info['totalMargin'] : 0,
            'totalAdd'      =>  $info['totalAdd'] ? $info['totalAdd'] : 0,
            'url'           =>  'https://www.ka5188.com/app/activityphb/' . $plidArr[$info['plid']] . '/' . $info['pissue'],
            'statusMsg'     =>  $flag ? $this->getRankStatusMsg($info['start_time'], $info['end_time']) : $this->getRankCheckStatus($info['cstate']),
            'cstateMsg'     =>  $flag ? $this->getRankCstateMsg($info['cstate']) : '--',
            'imgUrl'        =>  '//' . $this->config->item('base_url') . $info['imgUrl'],
            'extra'         =>  json_decode($info['extra'], true),
            'rule'          =>  $info['rule'],
        );
        return $list;
    }

    // 排行榜
    public function rankListDetail()
    {
        $this->check_capacity('4_8_3');
        $page = intval($this->input->get("p"));
        $searchData = array(
            "plid"       =>  $this->input->get("plid", true) ? $this->input->get("plid", true) : '', 
            "pissue"     =>  $this->input->get("pissue", true) ? $this->input->get("pissue", true) : '', 
            "uname"      =>  $this->input->get("uname", true) ? $this->input->get("uname", true) : '', 
        );

        $total = $this->Model_activity->getRankConfigDetail($searchData['plid'], $searchData['pissue']);

        $page = $page <= 1 ? 1 : $page;
        $result = $this->Model_activity->rankListDetail($searchData, $page, self::NUM_PER_PAGE);

        $pageConfig = array(
            "page"     => $page,
            "npp"      => self::NUM_PER_PAGE,
            "allCount" => $result[1]
        );

        $pages = get_pagination($pageConfig);

        $list = array();
        if(!empty($result[0]))
        {
            foreach ($result[0] as $items) 
            {
                $data = array(
                    'rankId'        =>  $items['rankId'],
                    'uname'         =>  $items['userName'],
                    'uid'           =>  $items['uid'],
                    'money'         =>  $items['money'],
                    'margin'        =>  $items['margin'],
                    'addMoney'      =>  $items['addMoney'],
                );
                array_push($list, $data);
            }
        }

        $total['lname'] = $this->getRankLname($total['plid']);
        $total['lids'] = $this->getRankLids($total['lids']);
        $infos = array(
            'result'    =>  $list,
            'pages'     =>  $pages,
            'search'    =>  $searchData,
            'total'     =>  $total,
        );

        $this->load->view("activity/rankListDetail", $infos);
    }
    
    public function worldcupredpack() {
        $this->check_capacity('4_9_1');
        $status = array('1' => '未使用', '2' => '已使用');
        $platformArr = array(1 => '网页', 2 => '安卓', 3 => 'IOS', 4 => 'M版');
        $ptype = array(2 => '充值红包', 3 => '购彩红包');
        $page = intval($this->input->get("p"));
        $page = $page ? $page : 1;
        $search = array(
            "uname" => $this->input->get("uname", true),
            "phone" => $this->input->get("phone", true),
            "section" => $this->input->get("section", true),
            "money" => $this->input->get("money", true),
            "p_type" => $this->input->get("p_type", true),
            "status" => $this->input->get("status", true),
            "platform" => $this->input->get("platform", true),
            "get_time0" => $this->input->get("get_time0", true),
            "get_time1" => $this->input->get("get_time1", true),
            "use_time0" => $this->input->get("use_time0", true),
            "use_time1" => $this->input->get("use_time1", true),
            "valid_end0" => $this->input->get("valid_end0", true),
            "valid_end1" => $this->input->get("valid_end1", true),
        );
        $gets = $this->input->get() ? $this->input->get() : array();
        if($this->checkPramsEmpty($gets))
        {
            $search['get_time0'] = date('Y-m-d 00:00:00',strtotime('-3 day'));
            $search['get_time1'] = date('Y-m-d 23:59:59');
            $search['section'] = 8;
        }
        $result = $this->Model_activity->getWorldcupRedpacks($search, ($page - 1) * self::NUM_PER_PAGE, self::NUM_PER_PAGE);
        $pageConfig = array(
            "page"     => $page,
            "npp"      => self::NUM_PER_PAGE,
            "allCount" => $result['total']['num']
        );
        $pages = get_pagination($pageConfig);
        $this->load->view('activity/worldcupRedpack', compact('status', 'platformArr', 'result', 'ptype', 'search', 'pages'));
    }
    
    public function questionActivity()
    {
        $this->check_capacity('4_11_1');
        $result = $this->Model_activity->getPfRedPack();
        $goucai = array();
        $chongzhi = array();
        foreach ($result as $k => $v) {
            if ($v['p_type'] == 3) {
                if ($v['hidden_flag'] == 0) {
                    $days = json_decode($v['use_params'], true);
                    $days = $days['end_day'] - $days['start_day'];
                    $goucai[] = array('rid' => $v['id'], 'name' => $v['use_desc'] . ',' . $v['c_name'] . ',' . $days . '天');
                }
            }
            if ($v['p_type'] == 2){
                    $days = json_decode($v['use_params'], true);
                    $days = $days['end_day'] - $days['start_day'];
                    $chongzhi[] = array('rid' => $v['id'], 'name' => $v['use_desc']  . ',' . $days . '天');
            }
        }
        $all = $this->Model_activity->countDtUser();
        $all['allred'] = 0;
        $all['allusered'] = 0;
        $count = $this->Model_activity->getRedCount();
        $newCount = array();
        foreach ($count[0] as $c) {
            $newCount[$c['orderId']][$c['rid']]['count'] = $c['count'];
            $newCount[$c['orderId']][$c['rid']]['p_name'] = $c['p_name'];
            $newCount[$c['orderId']][$c['rid']]['use_desc'] = $c['use_desc'];
            $all['allred'] += $c['count'];
        }
        foreach ($count[1] as $c) {
            $newCount[$c['orderId']][$c['rid']]['usecount'] = $c['count'];
            $newCount[$c['orderId']][$c['rid']]['valid_start'] = $c['valid_start'];
            $all['allusered'] += $c['count'];
        }
        $page = intval($this->input->get("p"));
        $page = $page <= 1 ? 1 : $page;
        $results = $this->Model_activity->getDtConfig($page, self::NUM_PER_PAGE);
        $pageConfig = array(
            "page"     => $page,
            "npp"      => self::NUM_PER_PAGE,
            "allCount" => $results['total']
        );
        $pages = get_pagination($pageConfig);
        $datas = array();
        $configs = $results['data'];
        foreach ($configs as $k=>$config) {
            $datas[$k]['id'] = $config['id'];
            $counts = array();
            $extras = json_decode($config['extra'], true);
            $qall = 0;
            $quse = 0;
            foreach ($extras as $j=>$extra) {
                if ($extra['rid'] != '-') {
                    $counts[$extra['rid']]['rid'] = $extra['rid'];
                    $counts[$extra['rid']]['count'] = $newCount[$config['id']][$extra['rid']]['count']?$newCount[$config['id']][$extra['rid']]['count']:0;
                    $counts[$extra['rid']]['usecount'] = $newCount[$config['id']][$extra['rid']]['usecount']?$newCount[$config['id']][$extra['rid']]['usecount']:0;
                    if ($counts[$extra['rid']]['usecount'] > 0 && $extra['ridTime'] != '-') {
                        if ($newCount[$config['id']][$extra['rid']]['valid_start'] != $extra['ridTime']) {
                            $all['allusered'] = $all['allusered'] - $counts[$extra['rid']]['usecount'];
                            $counts[$extra['rid']]['usecount'] = 0;
                        }
                    }
                    $counts[$extra['rid']]['p_name'] = $newCount[$config['id']][$extra['rid']]['p_name'];
                    $counts[$extra['rid']]['use_desc'] = $newCount[$config['id']][$extra['rid']]['use_desc'];
                    if(!$counts[$extra['rid']]['p_name']){
                        $des = $this->Model_activity->getRedDesc($extra['rid']);
                        $counts[$extra['rid']]['p_name'] = $des['p_name'];
                        $counts[$extra['rid']]['use_desc'] = $des['use_desc'];      
                    }
                    $extras[$j]['p_name'] = $counts[$extra['rid']]['use_desc'] . $counts[$extra['rid']]['p_name'];
                    $qall += $counts[$extra['rid']]['count'];
                    $quse += $counts[$extra['rid']]['usecount'];
                }else{
                    $extras[$j]['p_name'] = '-';
                }
            }
            $datas[$k]['qall'] = $qall;
            $datas[$k]['quse'] = $quse;
            $datas[$k]['extra'] = $counts;
            $datas[$k]['extras'] = $extras;
            $datas[$k]['start_time'] = $config['start_time'];
            $datas[$k]['end_time'] = $config['end_time'];
            $datas[$k]['rule'] = $config['rule'];
            $datas[$k]['questionUrl'] = $config['questionUrl'];
            $datas[$k]['titleDesc'] = $config['titleDesc'];
            $datas[$k]['status'] = $config['status'];
        }
        $this->load->view("activity/questionActivity", compact('goucai', 'chongzhi', 'all', 'datas', 'pages'));
    }

    public function createQuestionActivity()
    {
        $this->check_capacity('4_11_2', true);
        $postData = $this->input->post(null, true);
        $hongbao = array();
        foreach ($postData['plan']['min'] as $k => $data) {
            $hongbao[$k]['min'] = $data;
            $hongbao[$k]['max'] = $postData['plan']['max'][$k];
            $hongbao[$k]['rid'] = $postData['plan']['rid'][$k];
            if ($postData['plan']['rid'][$k] != '-' && $postData['plan']['ridTime'][$k] == '-') {
                $redpack = $this->Model_activity->createRedpack($postData['plan']['rid'][$k]);
                $hongbao[$k]['rid'] = $redpack['id'];
            }
            $hongbao[$k]['ridTime'] = $postData['plan']['ridTime'][$k];
        }
        // 组装数据
        $insertData = array(
            'questionUrl'   =>  $postData['url'],
            'titleDesc'     =>  $postData['ldes'].'*'.$postData['rdes'],
            'start_time'    =>  $postData['startTime'],
            'end_time'      =>  $postData['endTime'],
            'rule'          =>  $postData['rule'],
            'extra'         =>  json_encode($hongbao),
            'status'        =>  1,
        );
        $id = $this->Model_activity->insertQuestion($insertData);
        $this->syslog(74, "新建第{$id}期活动" );
        $result = array(
            'status'    =>  'y',
            'message'   =>  '答题活动新建成功',
        );
        die(json_encode($result));
    }

    public function closeQuestionActivity($id)
    {
        $this->check_capacity('4_11_3', true);
        $this->Model_activity->closeQuestionActivity($id);
        $this->syslog(74, "结束第{$id}期活动" );
        $result = array(
            'status'    =>  'y',
            'message'   =>  '答题活动已关闭',
        );
        die(json_encode($result));
    }

}
