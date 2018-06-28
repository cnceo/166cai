<?php

/**
 * 成长值管理
 */
if (!defined('BASEPATH')) exit('No direct script access allowed');

class Growth extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('Model_capacity');
        $this->load->model('Model_Growth','growth');
        $this->config->load('user_capacity');
        $this->config->load('msg_text');
        $this->user_capacity_cfg = $this->config->item('user_capacity_cfg');
        $this->msg_text_cfg = $this->config->item('msg_text_cfg');
    }
    /**
     * [pointMonitor 积分监测]
     * @author LiKangJian 2018-01-03
     * @return [type] [description]
     */
    public function pointMonitor()
    {
        $this->check_capacity('14_1_1');
        $this->load->view("growth/pointMonitor");
    }
    /**
     * [exchangeLog 兑换记录]
     * @author LiKangJian 2018-01-03
     * @return [type] [description]
     */
    public function exchangeLog()
    {
        $this->check_capacity('14_1_2');
        $page = intval($this->input->get("p"));
        $page = $page <= 1 ? 1 : $page;
        $fromType = $this->input->get("fromType", TRUE); //来源
        $searchData = array(
            "name"        => trim($this->input->get("name", TRUE) ),
            "p_type"  => trim($this->input->get("p_type", TRUE) ),
            "get_time_s"  => trim($this->input->get("get_time_s", TRUE) ),
            "get_time_e"  => trim($this->input->get("get_time_e", TRUE) ),
            "money"  => trim($this->input->get("money", TRUE) ),
            'use_status' =>trim($this->input->get("use_status", TRUE) ),
            "valid_start_s"  => trim($this->input->get("valid_start_s", TRUE) ),
            "valid_start_e"  => trim($this->input->get("valid_start_e", TRUE) ),
            "use_time_s"  => trim($this->input->get("use_time_s", TRUE) ),
            "use_time_e"  => trim($this->input->get("use_time_e", TRUE) ),
            "valid_end_s"  => trim($this->input->get("valid_end_s", TRUE) ),
            "valid_end_e"  => trim($this->input->get("valid_end_e", TRUE) ),
        );
        if(!$searchData['get_time_s'])
        {
            $searchData['get_time_s']  = date('Y-m-d').' 00:00:00';;
        }
        if(!$searchData['get_time_e'])
        {
            $searchData['get_time_e']  = date('Y-m-d').' 23:59:59';
        }
        if($searchData['get_time_s'] && $searchData['get_time_e'])
        {
            if($this->getDayLen($searchData['get_time_s'],$searchData['get_time_e']))
            {
                $searchData['get_time_e'] = date("Y-m-d",strtotime("+3 month",strtotime($searchData['get_time_s']))).' 23:59:59';;
            }
            
        }
        if($searchData['valid_start_s'] && $searchData['valid_start_e'])
        {
            if($this->getDayLen($searchData['valid_start_s'],$searchData['valid_start_e']))
            {
                $searchData['valid_start_e'] = date("Y-m-d",strtotime("+3 month",strtotime($searchData['valid_start_s']))).' 23:59:59';;
            }
            
        }
        if($searchData['use_time_s'] && $searchData['use_time_e'])
        {
            if($this->getDayLen($searchData['use_time_s'],$searchData['use_time_e']))
            {
                $searchData['use_time_e'] = date("Y-m-d",strtotime("+3 month",strtotime($searchData['use_time_s']))).' 23:59:59';;
            }
            
        }
        $result = $this->growth->exchange($searchData, $page, self::NUM_PER_PAGE);
        $pageConfig = array(
            "page"     => $page,
            "npp"      => self::NUM_PER_PAGE,
            "allCount" => $result['count']['c']
        );
        $pages = get_pagination($pageConfig);
        $pageInfo = array(
            "res"    => $result['res'],
            'count' => $result['count'],
            "pages"    => $pages,
            "search"   => $searchData,
        );
        $this->load->view("growth/exchangeLog",$pageInfo);
    }
    /**
     * [stockManage 库存管理]
     * @author LiKangJian 2018-01-03
     * @return [type] [description]
     */
    public function stockManage()
    {
        $this->check_capacity('14_1_3');
        $data = $this->growth->getStockData();
        $this->load->view("growth/stockManage",array('data'=>$data));
    }
    /**
     * [modifyStock 更新操作]
     * @author LiKangJian 2018-01-03
     * @return [type] [description]
     */
    public function modifyStock()
    {
        $this->check_capacity('14_1_5',true);
        $post = $this->input->post();
        $res = $this->growth->updateStock($post);
        if($res===false)
        {
            echo json_encode(array('status'=>'ERROR','message'=>'对不起，操作失败'));die;
        }
        $data = $this->growth->getStockData();
        $emptyStr = array();
        foreach ($post['emptyStock'] as $k => $v) 
        {
            if($v==1)
            {
                if($data[$k]['money'])
                {
                  $emptyStr[] = ($data[$k]['money']/100).'元';  
                }
                
            }
        }
        //清零
        if(count($emptyStr))
        {
            $this->syslog(64, implode('、', $emptyStr).'红包今日库存清零操作');
        }
        $ji = array();
        $stock = array();
        foreach ($data as $k => $v) 
        {
            $money = $v['money'] /100;
            $para = json_decode($v['use_params'],true);
            if($para['lv3']=='--')
            {
                $str = $money."元：黄金铂金".$para['lv4']."、钻石".$para['lv6'];
            }else{
                $str = $money."元：青铜白银".$para['lv3']."、黄金铂金".$para['lv4']."、钻石".$para['lv6'];
            }
            $str2 = $money.'元明日'.$v['next_out'].'个';
            array_push($ji, $str);
            array_push($stock, $str2);
        }
        //写入日志
        $this->syslog(64, implode(';', $ji).implode(',', $stock));
        //用户成长管理-积分管理
        echo json_encode(array('status'=>'SUCCESSS','message'=>'恭喜您，操作成功'));die;
    }
    /**
     * [pointDetail 积分明细]
     * @author LiKangJian 2018-01-03
     * @return [type] [description]
     */
    public function pointDetail()
    {
        $this->check_capacity('14_1_4');
        $this->load->view("growth/pointDetail",$this->getPointData());
    }

    /**
     * [ajaxPointDetail 用户中心调用]
     * @author LiKangJian 2018-01-08
     * @return [type] [description]
     */
    public function ajaxPointDetail()
    {
        $this->check_capacity('14_1_4');
        $this->load->view("growth/ajaxPointDetail",$this->getPointData());
    }
    /**
     * [getPointData 获取积分数据]
     * @author LiKangJian 2018-01-08
     * @return [type] [description]
     */
    private function getPointData()
    {
        $page = intval($this->input->get("p"));
        $page = $page <= 1 ? 1 : $page;
        $searchData = array(
            "uid" => trim($this->input->get("uid", true) ),
            "name"        => trim($this->input->get("name", TRUE) ),
            "trade_no"  => trim($this->input->get("trade_no", TRUE) ),
            "created_s"  => trim($this->input->get("created_s", TRUE) ),
            "created_e"  => trim($this->input->get("created_e", TRUE) ),
            "ctype"  => trim($this->input->get("ctype", TRUE) ),
            "value_s"  => trim($this->input->get("value_s", TRUE) ),
            "value_e"  => trim($this->input->get("value_e", TRUE) ),
            'mark'=>trim($this->input->get("mark", TRUE) ),
            'mark1'=>trim($this->input->get("mark1", TRUE) ),
        );
        if(!$searchData['created_s'])
        {
            $searchData['created_s']  = date('Y-m-d', strtotime( '-1 month' )).' 00:00:00';;
        }
        if(!$searchData['created_e'])
        {
            $searchData['created_e']  = date('Y-m-d').' 23:59:59';
        }
        if($searchData['created_s'] && $searchData['created_e'])
        {
            if($this->getDayLen($searchData['created_s'],$searchData['created_e']))
            {
                $searchData['created_e'] = date("Y-m-d",strtotime("+3 month",strtotime($searchData['created_s']))).' 23:59:59';;
            }
            
        }
        $result = $this->growth->pointList($searchData, $page, self::NUM_PER_PAGE);
        $count = 0;
        $counts = array();
        foreach ($result['count'] as $k => $v) 
        {
            $count +=$v['mark'];
            $counts[$v['m']] = $v;
        }
        $pageConfig = array(
            "page"     => $page,
            "npp"      => self::NUM_PER_PAGE,
            "allCount" => $count
        );

        $pages = get_pagination($pageConfig);
        $pageInfo = array(
            "res"    => $result['res'],
            'count' => $counts,
            "pages"    => $pages,
            "search"   => $searchData, 
            "fromType" => $this->input->get("fromType", true),
        );
        return $pageInfo;
    }
    /**
     * [index 成长值明细]
     * @author LiKangJian 2018-01-04
     * @return [type] [description]
     */
    public  function index()
    {
        $this->check_capacity('14_2_1');
        $page = intval($this->input->get("p"));
        $page = $page <= 1 ? 1 : $page;
        $fromType = $this->input->get("fromType", TRUE); //来源
        $searchData = array(
            "name"        => trim($this->input->get("name", TRUE)),
            "trade_no"  => trim($this->input->get("trade_no", TRUE)),
            "created_s"  => trim($this->input->get("created_s", TRUE)),
            "created_e"  => trim($this->input->get("created_e", TRUE)),
            "ctype"  => trim($this->input->get("ctype", TRUE)),
            "value_s"  => trim($this->input->get("value_s", TRUE)),
            "value_e"  => trim($this->input->get("value_e", TRUE)),
            'mark'=>trim($this->input->get("mark", TRUE)),
        );
        if(!$searchData['created_s'])
        {
            $searchData['created_s']  = date('Y-m-d', strtotime( '-1 month' )).' 00:00:00';;
        }
        if(!$searchData['created_e'])
        {
            $searchData['created_e']  = date('Y-m-d').' 23:59:59';
        }
        if($searchData['created_s'] && $searchData['created_e'])
        {
            if($this->getDayLen($searchData['created_s'],$searchData['created_e']))
            {
                $searchData['created_e'] = date("Y-m-d",strtotime("+3 month",strtotime($searchData['created_s']))).' 23:59:59';;
            }
            
        }
        $result = $this->growth->growthList($searchData, $page, self::NUM_PER_PAGE);
        $count = 0;
        foreach ($result['count'] as $k => $v) 
        {
            $count +=$v['mark'];
        }
        $pageConfig = array(
            "page"     => $page,
            "npp"      => self::NUM_PER_PAGE,
            "allCount" => $count
        );
        $pages = get_pagination($pageConfig);
        $pageInfo = array(
            "res"    => $result['res'],
            'count' => $result['count'],
            "pages"    => $pages,
            "search"   => $searchData,
        );
        $this->load->view("growth/index",$pageInfo);
    }
    /**
     * [levelManage 等级监测]
     * @author LiKangJian 2018-01-04
     * @return [type] [description]
     */
    public function levelManage()
    {
        $this->check_capacity('14_2_2');
        $this->load->view("growth/levelManage");
    }
    /**
     * [getDayLen 时间差]
     * @author LiKangJian 2018-01-18
     * @param  [type] $day1 [description]
     * @param  [type] $day2 [description]
     * @return [type]       [description]
     */
    public function getDayLen($day1,$day2)
    {
        $days =  abs(ceil((strtotime ($day1) -strtotime ($day2) )/86400)) ; 
        return $days > 90 ? true : false;
    }
}