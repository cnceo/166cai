<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

/**
 * APP 跟单接口.
 *
 * @date:2017-02-23
 */
class gendan extends MY_Controller
{
    
    private $lottery = array('51' => '双色球', '23529' => '大乐透', '42' => '竞彩足球', '43' => '竞彩篮球', '11' => '胜负/任九', '52' => '福彩3D', '23528' => '七乐彩', '10022' => '七星彩', '33' => '排列三/五');
    
    private $alllottery = array('51' => '双色球', '23529' => '大乐透', '42' => '竞彩足球', '43' => '竞彩篮球', '11' => '胜负彩','19' => '任选九', '52' => '福彩3D', '23528' => '七乐彩', '10022' => '七星彩', '33' => '排列三', '35' => '排列五');
    
    public function __construct()
    {
        parent::__construct();
    }
    
    /**
     * 跟单大厅
     */
    public function gendanList()
    {
        $data = $this->input->get(null, true);
//        $data=array(
//            'page'      =>  '1',    //  第几页
//            'number'    =>  '20',   //  一页几个        
//            'uid'       =>  $this->strCode(json_encode(array('uid' => 1024)), 'ENCODE'),     //  当前用户token
//            'lid'       =>  0        //采种默认0
//        );
        $params = array(
            'lid' => '',
            'number' => '',
            'page' => ''
        );
        // 必要参数检查
        foreach ($params as $key => $items) {
            if ($data[$key] === '' || !isset($data[$key])) {
                $result = array(
                    'status' => '0',
                    'msg' => '缺少必要参数',
                    'data' => '',
                );
                die(json_encode($result));
            }
        }      
        $lid = $data['lid'] ? $data['lid'] : 0;
        if ($data['uid']) {
        	$uid = $data['uid'] ? json_decode($this->strCode($data['uid']), true) : null;
        	$uid = $uid['uid'];
        }
        $data['type'] = 0;
        $data['order'] = 20;
        $this->load->model('united_planner_model');
        $offset = ($data['page'] - 1) * $data['number'];
        $datas = $this->united_planner_model->getAllUser($lid, $data, $offset, $data['number']);
        if ($uid)
        {
            $this->load->model('follow_order_model');
            $allGendans = $this->follow_order_model->getHasGendan($uid);
            $has = array();
            $hasIds = array();
            foreach ($allGendans as $gendan) {
                $has[] = $gendan['puid'] . ',' . $gendan['lid'];
                $hasIds[$gendan['puid'] . ',' . $gendan['lid']] = $gendan['followId'];
            }
            foreach ($allGendans as $gendan) {                
                if ($gendan['lid'] == 11) {
                    if (!in_array($gendan['puid'] . ',11', $has) || !in_array($gendan['puid'] . ',19', $has)) {
                        $key = array_search($gendan['puid'] . ',11', $has);
                        if ($key || $key===0) array_splice($has, $key, 1);
                    }
                }
                if ($gendan['lid'] == 33) {
                    if (!in_array($gendan['puid'] . ',33', $has) || !in_array($gendan['puid'] . ',35', $has)) {
                        $key = array_search($gendan['puid'] . ',33', $has);
                        if($key || $key===0)array_splice($has, $key, 1);
                    }
                }
            }
        }
        foreach ($datas as $k=>$data)
        {
            $datas[$k]['hasGendan'] = 0;
            $datas[$k]['followId'] = '';
            $datas[$k]['lidName'] = $this->lottery[$datas[$k]['lid']];
            $bouns = '';
            if (floor($data['bonus'] / 10000000000) > 0) {
                $bouns.= floor($data['bonus'] / 10000000000) . '亿';
            }
            if (floor($data['bonus'] / 1000000) > 0) {
                $bouns.= floor(($data['bonus'] - (floor($data['bonus'] / 10000000000) * 10000000000)) / 1000000) . '万';
            }
            if (floor($data['bonus'] / 10000000000) <= 0) {
                $bouns.= floor(($data['bonus'] - (floor($data['bonus'] / 1000000) * 1000000)) / 100) . '元';
            }
            $datas[$k]['bonus'] = $bouns;
            $datas[$k]['uname'] = uname_cut($data['uname'], 1, 5);
            if (!empty($has)) {
                foreach ($has as $h) {
                    if ($data['uid'] . ',' . $data['lid'] == $h) {
                        $datas[$k]['hasGendan'] = 1;
                        $datas[$k]['followId'] = $hasIds[$h];
                    }
                }
            }
        }
        $result = array(
            'status' => '1',
            'msg' => 'success',
            'data' => $datas,
        );
        echo json_encode($result);
    }
    
    /**
     * 校验是否跟单
     */
    public function hasGendan()
    {
        $redata = json_decode($this->strCode($this->input->post('data')), true);
//        $redata = array(
//            'uid' => '', //当前登陆用户uid
//            'puid' => 1050, //跟单人的uid
//            'lid' => 11//采种lid
//        );
        $headerInfo = $this->getRequestHeaders();
        if(empty($redata['uid']))
        {
            $result = array(
                'status'    =>  ($headerInfo['appVersionCode'] >= '40200') ? '700' : '300',
                'msg'       =>  '用户未登录',
                'data'      =>  $redata
            );
            die(json_encode($result));
        }
        $this->load->model('user_model','User');
        $uinfo = $this->User->getUserInfo($redata['uid']);
        if(isset($uinfo['userStatus']) && $uinfo['userStatus'] == '1')
        {
            $result = array(
                'status'    =>  ($headerInfo['appVersionCode'] >= '40200') ? '700' : '300',
                'msg'       => '您的账号已注销，被注销的账号不能使用原手机号再注册，请注册新账号登录',
                'data'      =>  $redata
            );
            die(json_encode($result));
        }
        if($uinfo['userStatus'] == '2')
        {
            $result = array(
                'status' => '0',
                'msg' => '您的账户已被冻结，如需解冻请联系客服。',
                'data' => $redata
            );
            die(json_encode($result));
        }
        // 是否实名认证
        if(empty($uinfo['real_name']) || empty($uinfo['id_card']))
        {
            $result = array(
                'status' => '400',
                'msg' => '请先完成实名认证',
                'data' => $redata
            );
            die(json_encode($result));
        }
        $this->load->model('follow_order_model');
        $res = $this->follow_order_model->checkHasGendan($redata['uid'], $redata['puid'], $redata['lid']);
        if ($res['code'] == 1) {
            $result = array(
                'status' => '0',
                'msg' => '您已定制发起人的方案',
                'data' => $redata
            );
        } elseif ($res['code'] == 2) {
            $result = array(
                'status' => '0',
                'msg' => '定制人数已达上限',
                'data' => $redata
            );
        } else {
            $result = array(
                'status' => '1',
                'msg' => 'success',
                'data' => $redata
            );
        }
        die(json_encode($result));
    }
    
    /**
     * 创建定制跟单
     */
    public function createGendan()
    {
        $redata = json_decode($this->strCode($this->input->post('data')), true);
//        $redata = array(
//            'uid'     => 1024, //当前登陆用户uid
//            'puid'    => 1050, //跟单人的uid
//            'lid'     => 43,//采种lid
//            'money'   => 5, //每次认购金额
//            'followType'    => 1, // 0 按固定金额 1 按百分比
//            'num'     => 10, //定制次数
//            'payType' => 1, //付款方式 0 预付款 1 实时付款
//            'percent' => 5, //百分比扣款比例
//            'max'     => 10 //百分比扣款最大金额
//        );
        $headerInfo = $this->getRequestHeaders();
        $params = array(
            'uid' => '',
            'puid' => '',
            'lid' => '',
            'money' => '',
            'followType' => '',
            'num' => '',
            'payType' => ''
        );
        if ($redata['followType'] == 1) {
            $params['percent'] = '';
            $params['max'] = '';
        }
        // 必要参数检查
        foreach ($params as $key => $items) {
            if ($redata[$key] === '' || !isset($redata[$key])) {
                $result = array(
                    'status' => '0',
                    'msg' => '缺少必要参数',
                    'data' => '',
                );
                die(json_encode($result));
            }
        } 
        if(empty($redata['uid']))
        {
            $result = array(
                'status'    =>  ($headerInfo['appVersionCode'] >= '40200') ? '700' : '300',
                'msg'       =>  '用户未登录',
                'data'      =>  $redata
            );
            die(json_encode($result));
        }
        $this->load->model('user_model','User');
        $uinfo = $this->User->getUserInfo($redata['uid']);
        if(isset($uinfo['userStatus']) && $uinfo['userStatus'] == '1')
        {
            $result = array(
                'status'    =>  ($headerInfo['appVersionCode'] >= '40200') ? '700' : '300',
                'msg'       =>  '您的账号已注销，被注销的账号不能使用原手机号再注册，请注册新账号登录',
                'data'      =>  $redata
            );
            die(json_encode($result));
        }
        if($uinfo['userStatus'] == '2')
        {
            $result = array(
                'status' => '0',
                'msg' => '您的账户已被冻结，如需解冻请联系客服。',
                'data' => $redata
            );
            die(json_encode($result));
        }
        $uinfo['uid'] = $redata['uid'];
        if(!$this->checkUserAuth($uinfo, $redata['auth']) && $headerInfo['appVersionCode'] >= '3') 
        {
            $result = array(
                'status'    =>  ($headerInfo['appVersionCode'] >= '40200') ? '700' : '300',
                'msg'       =>  '您的登录密码已修改，请重新登录',
                'data'      =>  '',
            );
            echo json_encode($result);
            exit();
        }
        unset($redata['auth']);  
        // 单设备登录检查
        $checkData = $this->checkUserLogin($uinfo['uid']);
        if(!$checkData['status'])
        {
            $result = array(
                'status'    =>  $checkData['code'],
                'msg'       =>  $checkData['msg'],
                'data'      =>  '',
            );
            echo json_encode($result);
            exit();
        }
        // 是否实名认证
        if(empty($uinfo['real_name']) || empty($uinfo['id_card']))
        {
            $result = array(
                'status' => '400',
                'msg' => '请先完成实名认证',
                'data' => $redata
            );
            die(json_encode($result));
        }
        if($redata['followType'] == 0)
        {
            $totalMoney = $redata['money'] * $redata['num'];
        }
        if($redata['followType'] == 1)
        {
            $redata['money'] = $redata['max'];
            $totalMoney = $redata['max'] * $redata['num'];
        }
        $this->load->model('follow_order_model');
        $datas = array(
            'uid' => $redata['uid'],
            'puid' => $redata['puid'],
            'lid' => $redata['lid'],
            'payType' => $redata['payType'],
            'followType' => $redata['followType'],
            'totalMoney' => $totalMoney,
            'buyMoney' => $redata['money'],
            'buyMoneyRate' => $redata['percent'],
            'buyMaxMoney' => $redata['max'],
            'followTotalTimes' => $redata['num'],
            'buyPlatform' => $this->config->item('platform'),
            'channel' => '0',
        );
        $res = $this->follow_order_model->createFollowOrder($datas);
        if ($res['code'] == 200){
            $payView = $this->orderComplete($res['data'], 5);
            $result = array(
                'status' => '1',
                'msg' => '创建订单成功',
                'data' => $payView
            );
        }else{
            $result = array(
                'status' => '0',
                'msg' => $res['msg'],
                'data' => $redata
            );
        }
        die(json_encode($result));
    }
    
    /**
     * APP 订单创建成功处理.
     *
     * @date:2016-01-18
     */
    public function orderComplete($data, $orderType)
    {
        $param = array(
            'uid' => $data['uid'],
            'orderId' => $data['followId'],
            'orderType' => $orderType,
            'payType' => $data['payType'],
            'buyMoney' => $data['totalMoney'],
        );
        if($data['payType'] == 1){
            $param['pay_status'] = 'true';
            $param['lid'] = $data['lid'];
        }
        // 订单信息加密
        $orderDetail = $this->strCode(json_encode($param), 'ENCODE');

        // 跳转支付页面
        $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? 'https:' : 'http:';
        if ($data['payType'] == 0) {
            $payView = $protocol . $this->config->item('pages_url') . 'app/order/doPay/' . urlencode($orderDetail);
        } else {
            $payView = $protocol . $this->config->item('pages_url') . 'app/wallet/payComplete/' . urlencode($orderDetail);
        }

        return $payView;
    }
    
    /**
     * 我的跟单记录
     */
    public function myFollow()
    {
        $redata = $this->input->get(null, true);
//        $redata = array(
//            'page'      =>  '1',    //  第几页
//            'number'    =>  '20',   //  一页几个
//            'lid'       =>  '0',   // 采种lid
//            'uid'       =>  $this->strCode(json_encode(array('uid' => 1024)), 'ENCODE'),     //  当前用户token
//        );
        $headerInfo = $this->getRequestHeaders();
        $params = array(
            'uid' => '',
            'lid' => '',
            'number' => '',
            'page' => ''
        );
        // 必要参数检查
        foreach ($params as $key => $items) {
            if ($redata[$key] === '' || !isset($redata[$key])) {
                $result = array(
                    'status' => '0',
                    'msg' => '缺少必要参数',
                    'data' => '',
                );
                die(json_encode($result));
            }
        }         
        if ($redata['uid']) {
        	$uid = $redata['uid'] ? json_decode($this->strCode($redata['uid']), true) : null;
        	$redata['uid'] = $uid['uid'];
        }
        if(empty($redata['uid']))
        {
            $result = array(
                'status'    =>  ($headerInfo['appVersionCode'] >= '40200') ? '700' : '300',
                'msg'       =>  '用户未登录',
                'data'      =>  $redata
            );
            die(json_encode($result));
        }
        $this->load->model('user_model','User');
        $uinfo = $this->User->getUserInfo($redata['uid']);
        if(isset($uinfo['userStatus']) && $uinfo['userStatus'] == '1')
        {
            $result = array(
                'status'    =>  ($headerInfo['appVersionCode'] >= '40200') ? '700' : '300',
                'msg'       =>  '您的账号已注销，被注销的账号不能使用原手机号再注册，请注册新账号登录',
                'data'      =>  $redata
            );
            die(json_encode($result));
        }
        if($uinfo['userStatus'] == '2')
        {
            $result = array(
                'status' => '0',
                'msg' => '您的账户已被冻结，如需解冻请联系客服。',
                'data' => $redata
            );
            die(json_encode($result));
        }
        // 是否实名认证
        if(empty($uinfo['real_name']) || empty($uinfo['id_card']))
        {
            $result = array(
                'status' => '400',
                'msg' => '请先完成实名认证',
                'data' => $redata
            );
            die(json_encode($result));
        }
        $this->load->model('follow_order_model');
        $cons = array(
            'uid' => $redata['uid'],
            'start' => "2017-08-01 00:00:00",
            'end' => date("Y-m-d H:i:s", time()),
        );
        if ($redata['lid'] > 0) {
            $cons['lid'] = $redata['lid'];
        }
        $orders = $this->follow_order_model->getOrders($cons, $redata['page'], $redata['number']);
        $this->load->model('api_data', 'Data');
        $linfo = $this->Data->getLotteryInfo('android');
        $lotteryInfo = array();
        foreach ($linfo as $info) {
            $lotteryInfo[$info['lid']]['logUrl'] = $info['logUrl'];
        }
        foreach ($orders as $k=>$order)
        {
            $orders[$k]['lidName'] = $this->alllottery[$order['lid']];
            $orders[$k]['uname'] = uname_cut($order['uname'], 1, 5);
            $orders[$k]['logUrl'] = $lotteryInfo[$order['lid']]['logUrl'];
            $orders[$k]['effectTime'] = date("m-d H:i", strtotime($order['effectTime']));
            if ($order['totalMargin'] > 0) {
                $orders[$k]['award'] = '中奖' . ($order['totalMargin'] / 100) . '元';
            }
            if ($order['my_status'] == 0) {
                $orders[$k]['gendanStatus'] = '跟单中';
                if ($order['totalMargin'] == 0) {
                    $orders[$k]['award'] = '静待大奖';
                }
            }
            if ($order['my_status'] == 1 && $order['status'] > 1) {
                $orders[$k]['gendanStatus'] = '跟单完成';
                if ($order['totalMargin'] == 0) {
                    $orders[$k]['award'] = '未中奖';
                }
            }
        }
        $result = array(
            'status' => '1',
            'msg' => 'success',
            'data' => $orders,
        );
        echo json_encode($result);
    }
    
    /**
     * 定制我的采种
     */
    public function myGendan()
    {
        $redata = $this->input->get(null, true);
//        $redata = array( 
//            'uid'       =>  $this->strCode(json_encode(array('uid' => 1)), 'ENCODE'),     //  当前用户token
//            'lid'       =>  '42'
//        );
        $headerInfo = $this->getRequestHeaders();
        if ($redata['uid']) {
        	$uid = $redata['uid'] ? json_decode($this->strCode($redata['uid']), true) : null;
        	$redata['uid'] = $uid['uid'];
        }
        if(empty($redata['uid']))
        {
            $result = array(
                'status'    =>  ($headerInfo['appVersionCode'] >= '40200') ? '700' : '300',
                'msg'       =>  '用户未登录',
                'data'      =>  $redata
            );
            die(json_encode($result));
        }
        $this->load->model('user_model','User');
        $uinfo = $this->User->getUserInfo($redata['uid']);
        if(isset($uinfo['userStatus']) && $uinfo['userStatus'] == '1')
        {
            $result = array(
                'status'    =>  ($headerInfo['appVersionCode'] >= '40200') ? '700' : '300',
                'msg'       =>  '您的账号已注销，被注销的账号不能使用原手机号再注册，请注册新账号登录',
                'data'      =>  $redata
            );
            die(json_encode($result));
        }
        if($uinfo['userStatus'] == '2')
        {
            $result = array(
                'status' => '0',
                'msg' => '您的账户已被冻结，如需解冻请联系客服。',
                'data' => $redata
            );
            die(json_encode($result));
        }
        // 是否实名认证
        if(empty($uinfo['real_name']) || empty($uinfo['id_card']))
        {
            $result = array(
                'status' => '400',
                'msg' => '请先完成实名认证',
                'data' => $redata
            );
            die(json_encode($result));
        }
        $this->load->model('united_planner_model');
        $follows = $this->united_planner_model->getAllFollow($redata['uid'], $redata['lid']);
        $this->load->model('api_data', 'Data');
        $linfo = $this->Data->getLotteryInfo('android');
        $lotteryInfo = array();
        foreach ($linfo as $info) {
            $lotteryInfo[$info['lid']]['logUrl'] = $info['logUrl'];
        }        
        foreach ($follows as $k=>$follow)
        {
            $follows[$k]['logUrl'] = $lotteryInfo[$follow['lid']]['logUrl'];
            $follows[$k]['lidName'] = $this->lottery[$follow['lid']];
            $follows[$k]['leftNum'] = 2000 - $follow['isFollowNum'];
        }
        $result = array(
            'status' => '1',
            'msg' => 'success',
            'data' => $follows,
        );        
        echo json_encode($result);
    }
    
    
    /**
     * 定制我的列表
     */
    public function myGendanList()
    {
        $redata = $this->input->get(null, true);
//        $redata = array(
//            'page'      =>  '1',    //  第几页
//            'number'    =>  '20',   //  一页几个                
//            'uid'       =>  $this->strCode(json_encode(array('uid' => 1050)), 'ENCODE'),     //  当前用户token
//            'lid'       => 42 //采种id
//        );
        $headerInfo = $this->getRequestHeaders();
        $params = array(
            'uid' => '',
            'lid' => '',
            'number' => '',
            'page' => ''
        );
        // 必要参数检查
        foreach ($params as $key => $items) {
            if ($redata[$key] === '' || !isset($redata[$key])) {
                $result = array(
                    'status' => '0',
                    'msg' => '缺少必要参数',
                    'data' => '',
                );
                die(json_encode($result));
            }
        }             
        if ($redata['uid']) {
        	$uid = $redata['uid'] ? json_decode($this->strCode($redata['uid']), true) : null;
        	$redata['uid'] = $uid['uid'];
        }
        if(empty($redata['uid']))
        {
            $result = array(
                'status'    =>  ($headerInfo['appVersionCode'] >= '40200') ? '700' : '300',
                'msg'       =>  '用户未登录',
                'data'      =>  $redata
            );
            die(json_encode($result));
        }
        $this->load->model('user_model','User');
        $uinfo = $this->User->getUserInfo($redata['uid']);
        if(isset($uinfo['userStatus']) && $uinfo['userStatus'] == '1')
        {
            $result = array(
                'status'    =>  ($headerInfo['appVersionCode'] >= '40200') ? '700' : '300',
                'msg'       =>  '您的账号已注销，被注销的账号不能使用原手机号再注册，请注册新账号登录',
                'data'      =>  $redata
            );
            die(json_encode($result));
        }
        if($uinfo['userStatus'] == '2')
        {
            $result = array(
                'status' => '0',
                'msg' => '您的账户已被冻结，如需解冻请联系客服。',
                'data' => $redata
            );
            die(json_encode($result));
        }
        // 是否实名认证
        if(empty($uinfo['real_name']) || empty($uinfo['id_card']))
        {
            $result = array(
                'status' => '400',
                'msg' => '请先完成实名认证',
                'data' => $redata
            );
            die(json_encode($result));
        }
        $this->load->model('follow_order_model');
        $offset = ($redata['page'] - 1) * $redata['number'];
        $res = $this->follow_order_model->gendanList($redata['uid'], $redata['lid'], $offset, $redata['number']);
        $users = $res[0];
        foreach ($users as $k=>$user)
        {
            if ($user['followType'] == 0) {
                $users[$k]['buyMoney'] = ($user['buyMoney'] / 100) . '元';
                $users[$k]['buyMoneyRate'] = '/';
            } else {
                $users[$k]['buyMoney'] = '/';
                $users[$k]['buyMoneyRate'] = $user['buyMoneyRate'] . '%';
            }
            $users[$k]['uname'] = uname_cut($user['uname'],2, 3);
            $users[$k]['effectTime'] = date("m-d H:i", strtotime($user['effectTime']));
        }
        $result = array(
            'status' => '1',
            'msg' => 'success',
            'data' => $users,
        );              
        echo json_encode($result);
    }    
}    
