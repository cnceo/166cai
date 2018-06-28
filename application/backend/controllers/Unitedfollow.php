<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Unitedfollow extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('model_united_follow', 'unitedFollow');
        $this->load->library('tools');
        $this->config->load('msg_text');
        $this->config->load('caipiao');
        $this->config->load('order');
        $this->order_status = $this->config->item('cfg_orders');
        $this->msg_text_cfg = $this->config->item('msg_text_cfg');
        $this->match_sale_status = $this->config->load('match_sale_status');
        foreach ($this->config->item('caipiao_all_cfg') as $key => $value)
        {
            $this->$key = $value;
        }
    }

    // 彩种
    private $lottery = array('51' => '双色球', '23529' => '大乐透', '42' => '竞彩足球', '43' => '竞彩篮球', '11' => '胜负彩', '19' => '任选九', '52' => '福彩3D', '23528' => '七乐彩', '10022' => '七星彩', '33' => '排列三', '35' => '排列五');

    // 跟单状态
    private $status = array(
        '0'     =>  '预支付待付款',
        '5'     =>  '预支付已付款',
        '1'     =>  '跟单中',
        '2'     =>  '跟单完成',
        '3'     =>  '用户撤单',
        '4'     =>  '系统撤单',
    );

    // 扣款方式
    private $payTypes = array(
        '0'     =>  '预付款',
        '1'     =>  '实时付款',
    );
    
    // 跟单管理
    public function index()
    {
        $this->check_capacity('3_15_1');
        //查询的条件
        $page = intval($this->input->get("p"));
        $page = $page <= 1 ? 1 : $page;
        $searchData = array(
            "name"              =>  $this->input->get("name", TRUE),
            "lid"               =>  $this->input->get("lid", TRUE),
            "buyPlatform"       =>  $this->input->get("buyPlatform", true),
            "followType"        =>  $this->input->get("followType", TRUE),
            "start_money"       =>  $this->input->get("start_money", TRUE),
            "end_money"         =>  $this->input->get("end_money", TRUE),
            "channel"           =>  $this->input->get("channel", true),
            "payType"           =>  $this->input->get("payType", TRUE),
            "status"            =>  $this->input->get("status"),
            "start_time"        =>  $this->input->get("start_time", TRUE),
            "end_time"          =>  $this->input->get("end_time", TRUE),
            'uid'               =>  $this->input->get("uid", true),
            'fromType'          =>  $this->input->get("fromType", TRUE),
        );

        $fromType = $this->input->get("fromType", TRUE);
        $this->filterTime($searchData['start_time'], $searchData['end_time']);
        
        // 投注平台
        $platforms = array(
            '0'     =>  '网页',
            '1'     =>  'Android',
            '2'     =>  'IOS',
        );
        // 每次认购
        $followTypes = array(
            '0'     =>  '按固定金额',
            '1'     =>  '按百分比',
        );
        // 注册渠道
        $this->load->model('model_channel');
        $channelRes = $this->model_channel->getChannels();

        $result = $this->unitedFollow->list_orders($searchData, $page, self::NUM_PER_PAGE);

        // 分页
        $pageConfig = array(
            "page"     => $page,
            "npp"      => self::NUM_PER_PAGE,
            "allCount" => $result['count']['num']
        );
        $pages = get_pagination($pageConfig);

        $allChannel = array();
        foreach ($channelRes as $channel)
        {
            $allChannel[$channel['id']] = $channel['name'];
        }

        $pageInfo = array(
            'lottery'    => $this->lottery,
            'platforms'  => $platforms,
            'followTypes'=> $followTypes,
            'payTypes'   => $this->payTypes,
            'channels'   => $allChannel,
            'status'     => $this->status,
            'searchTime' => array(
                'end_time'   => date("Y-m-d 23:59:59", time()),
                'start_time' => date("Y-m-d 00:00:00", time())
            ),
            'orders'     => $result['data'],
            'pages'      => $pages,
            'count'      => $result['count'],
            'search'     => $searchData,
            'fromType'   => $fromType
        );

        $this->load->view("unitedfollow/index", $pageInfo);
    }

    // 定制管理
    public function followManage()
    {
        $this->check_capacity('3_15_2');
        //查询的条件
        $page = intval($this->input->get("p"));
        $page = $page <= 1 ? 1 : $page;
        $searchData = array(
            "name"             =>  $this->input->get("name", TRUE),
            "lid"               =>  $this->input->get("lid", TRUE),
            "status"            =>  $this->input->get("status"),
        );

        // 定制状态
        $status = array(
            '0'     =>  '未跟满',
            '1'     =>  '已跟满',
        );

        $result = $this->unitedFollow->list_planner($searchData, $page, self::NUM_PER_PAGE);

        // 分页
        $pageConfig = array(
            "page"     => $page,
            "npp"      => self::NUM_PER_PAGE,
            "allCount" => $result['count']['num']
        );
        $pages = get_pagination($pageConfig);

        $lottery = array('51' => '双色球', '23529' => '大乐透', '42' => '竞彩足球', '43' => '竞彩篮球', '11' => '胜负/任九', '52' => '福彩3D', '23528' => '七乐彩', '10022' => '七星彩', '33' => '排列三/五');

        $pageInfo = array(
            'lottery'    => $lottery,
            'status'     => $status,
            'orders'     => $result['data'],
            'pages'      => $pages,
            'search'     => $searchData,
            'fromType'   => $fromType
        );

        $this->load->view("unitedfollow/followManage", $pageInfo);
    }

    // 合买红人详情
    public function plannerDetail()
    {
        //查询的条件
        $page = intval($this->input->get("p"));
        $page = $page <= 1 ? 1 : $page;
        $searchData = array(
            "puid"              =>  $this->input->get("puid", TRUE),
            "lid"               =>  $this->input->get("lid", TRUE),
            "status"            =>  $this->input->get("status"),
            'name'             =>  $this->input->get("name", TRUE),
        );

        $result = $this->unitedFollow->list_followers($searchData, $page, self::NUM_PER_PAGE);

        // 分页
        $pageConfig = array(
            "page"     => $page,
            "npp"      => self::NUM_PER_PAGE,
            "allCount" => $result['count']['num']
        );
        $pages = get_pagination($pageConfig);

        $status = array(
            '1'     =>  '跟单中',
            '2'     =>  '跟单完成',
            '3'     =>  '用户撤单',
            '4'     =>  '系统撤单',
        );

        $pageInfo = array(
            'lottery'    => $this->lottery,
            'status'     => $status,
            'orders'     => $result['data'],
            'pages'      => $pages,
            'search'     => $searchData,
            'fromType'   => $fromType
        );

        $this->load->view("unitedfollow/followList", $pageInfo);
    }

    // 跟单详情
    public function followOrderDetail()
    {
        //查询的条件
        $page = intval($this->input->get("p"));
        $page = $page <= 1 ? 1 : $page;
        $searchData = array(
            "followId"          =>  $this->input->get("followId", TRUE),
            "fromType"          =>  $this->input->get("fromType", TRUE),
        );

        $result = $this->unitedFollow->list_detail($searchData, $page, self::NUM_PER_PAGE);

        // 合买状态
        $ustatus = array(
            '40'    =>  '已付款',
            '240'   =>  '出票中',
            '500'   =>  '出票成功',
            '600'   =>  '出票失败',
            '610'   =>  '发起人撤单',
            '620'   =>  '未满员撤单',
            '1000'  =>  '未中奖',
            '2000'  =>  '已中奖',
        );

        // 分页
        $pageConfig = array(
            "page"     => $page,
            "npp"      => self::NUM_PER_PAGE,
            "allCount" => $result['count']['num']
        );
        $pages = get_pagination($pageConfig);

        $pageInfo = array(
            'ustatus'    => $ustatus,
            'status'     => $this->status,
            'payTypes'   => $this->payTypes,
            'info'       => $result['info'],
            'orders'     => $result['data'],
            'pages'      => $pages,
            'search'     => $searchData,
            'fromType'   => $fromType
        );

        $this->load->view("unitedfollow/detail", $pageInfo);
    }

    // 停止跟单
    public function cancelFollowOrder()
    {
        $this->check_capacity('3_15_3', true);
        $followId = $this->input->post("followId", TRUE);
        $uid = $this->input->post("uid", TRUE);

        $this->load->model('follow_order_model');
        $handleRes = $this->follow_order_model->cancelFollowOrder($uid, $followId, 0);

        if($handleRes['code'] == '200')
        {
            // 日志
            $this->syslog(57, "定制跟单撤单操作，单号：GD" . $followId);
            return $this->ajaxReturn('y', $handleRes['msg']);
        }
        else
        {
            return $this->ajaxReturn('n', $handleRes['msg']);
        }
    }
}
