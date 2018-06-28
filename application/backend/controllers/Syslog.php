<?php

/**
 * Copyright (c) 2014,上海二三四五网络科技股份有限公司
 * 摘    要：系统日志管理
 * 作    者：wangl@2345.com
 * 修改日期：2014.11.11
 */
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Syslog extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('Model_syslog');
        $this->config->load('user_capacity');
        $this->user_capacity_cfg = $this->config->item('user_capacity_cfg');
    }
    
    /**
     * 参    数：
     * 作    者：wangl
     * 功    能：日志列表
     * 修改日期：2014.11.11
     */
    public function index()
    {
        $this->check_capacity("8_2_1");
        $page = intval($this->input->get("p"));
        $page = $page <= 1 ? 1 : $page;
        $searchData = array(
            "mark" => $this->input->get("mark", true),
            "name" => $this->input->get("name", true),
            "start_time" => $this->input->get("start_time", true),
            "end_time" => $this->input->get("end_time", true),
            "lmod" => $this->input->get("lmod", true)
        );
        $this->filterTime($searchData['start_time'], $searchData['end_time']);
        $result = $this->Model_syslog->list_logs($searchData, $page, self::NUM_PER_PAGE);
        $pageConfig = array(
            "page" => $page,
            "npp" => self::NUM_PER_PAGE,
            "allCount" => $result[1]
        );
        $pages = get_pagination($pageConfig);
        $pageInfo = array(
            "logs" => $result[0],
            "pages" => $pages,
            "search" => $searchData,
            "mods" => $this->get_mod()
        );
        
        $this->load->view("syslog", $pageInfo);
    }
    
    /**
     * 参    数：$key 模块键值
     * 作    者：wangl
     * 功    能：获取日志模块
     * 修改日期：2014.11.11
     */
    public function get_mod($key = 'all')
    {
        $mod = array(
            1 => "报表管理——用户管理",
            2 => "订单管理",
            3 => "交易明细",
            4 => "充值记录",
            5 => "提款记录",
            6 => "提款审核",
            7 => "派奖审核",
            8 => "公告管理",
            9 => "帐号管理",
            10 => "系统日志",
            11 => "用户登录",
            12 => "用户反馈",
            13 => "运营管理——期次管理",
            14 => "运营管理——对阵管理",
            15 => "运营管理——彩种管理",
            16 => "数据中心——期次预排",
            17 => "数据中心——期次管理",
            18 => "数据中心——对阵管理",
            19 => "数据中心——抓取配置",
            20 => "运营管理——派奖核对",
            21 => "财务对账——存入押金",
            22 => "财务对账——修改预警",
            23 => "运营管理——追号管理",
            24 => "代码发布——开启关闭",
            25 => "投注站管理——投注站管理",
        	26 => "用户登录",
        	27 => "删除红包",
            28 => "上下架配置——春节停售",
        	29 => "运营管理——出票商管理",
        	30 => "系统管理——报警配置",
        	31 => "出票监控——人工撤单",
			32 => "信息管理——首页管理",
        	35 => "运营管理——推广管理",
            36 => "使用红包",
            37 => "注销用户",
            38 => "新建加奖活动",
        	39 => "信息管理——banner管理",
        	40 => "用户管理——手动调款",
        	41 => "运营管理--批量转账",
        	42 => "审核管理--调账审核",
        	43 => "上下架配置——服务承诺弹层",
            44 => "运营活动-红包管理-新增红包",
            45 => "运营活动-红包管理-红包派发",
            46 => "审核管理-红包派发审核",
            47 => "用户管理-红包明细",
            48 => '运营管理——支付渠道管理',
            49 => "渠道分析-渠道管理",
            50 => "运营管理——限号管理",
            51 => "运营管理-合买管理-个人简介",
            52 => "信息管理-敏感词",
        	53 => "出票监控——手动提票",
        	54 => "出票限制",
            55 => "推送管理-购彩提醒",
            56 => "评论管理",
            57 => "信息管理-APP配置",
            58 => "运营管理-定制跟单-跟单管理",
            59 => "评论管理",
            60 => "客户端配置-Android配置",
            61 => "客户端配置-iOS配置",
            62 => "客户端配置-M配置",
            63 => "数据中心——快3期次更新",
            64 => "用户成长管理-积分管理",
            65 => "推送管理--未注册未实名推送",
            66 => "彩票脚本重启",
            67 => "新年活动",
            68 => "运营管理——用户标签管理",
            69 => "运营管理——用户集群管理",
            70 => "信息管理-用户头像管理",
            71 => "运营管理-支付渠道管理",
            72 => "运营活动-排行榜活动",
            73 => "审核管理-排行榜活动审核",
            74 => "运营活动—答题活动",
            75 => "竞彩竞猜活动",
            76 => "渠道分析-渠道账号管理",
            77 => "信息管理-合买宣言、发起人简介",
            78 => "世界杯赛场接口",
        );
        return $key == 'all' ? $mod : $mod[$key];
    }
    
}
