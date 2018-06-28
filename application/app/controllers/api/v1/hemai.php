<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

/**
 * APP 合买接口.
 *
 * @date:2017-02-23
 */
class hemai extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('cache_model', 'Cache');
        $this->versionInfo = $this->getRequestHeaders();
    }

    /**
     * 合买列表查询接口.
     */
    public function planList()
    {
        $data = $this->input->get(null, true);

        /*
        $data = array(
            'order'     =>  '40',   //  (合买战绩 21升序,20降序)(进度 00降序,01升序)(金额 30降序,31升序)(截止时间 40降序,41升序)
            'state'     =>  '0',
            'page'      =>  '1',    //  第几页
            'number'    =>  '10',   //  一页几个
            'lid'       =>  '0',    //  彩种lid,全部为0
            'openStatus'=>  '99',   //  公开状态: 99 全部 ,0 公开,1 仅对跟单者公开,2 截止后公开
            'guaranteeAmount' => '0', // 是否保底  0全部,1有保底，2无保底
            'commission'=>  '0',    //  是否有佣金:0全部,1有佣金，2无佣金
            'uname'     =>  '',     //  搜索人名
            'uid'       =>  '',     //  当前用户token
        );
        */
        $this->load->model('united_order_model');
        $lid = $data['lid'] ? $data['lid'] : 0;
        $perPage = $data['number'];
        $cpage = $data['page'];
        $order = $data['order'] ? $data['order'] : '00';
        if ($data['uid']) {
        	$uid = $data['uid'] ? json_decode($this->strCode($data['uid']), true) : null;
        	$uid = $uid['uid'];
        	unset($data['uid']);
        }
        $orders = $this->united_order_model->getHmdtOrders($data, ($perPage * ($cpage - 1)).', '.$perPage, $order, $uid);
        $orders = array('data'=>array());
        $lottery = array('51' => '双色球', '23529' => '大乐透', '42' => '竞彩足球', '43' => '竞彩篮球', '11' => '胜负', '19' => '任九', '52' => '福彩3D', '23528' => '七乐彩', '10022' => '七星彩', '33' => '排列三', '35' => '排列五');
        $this->load->model('user_model');
    	foreach ($orders['data'] as $k => &$order) {
            $uinfo = $this->user_model->getUserInfo($order['uid']);
            $hinfo = $this->user_model->getHotInfo($order['uid']);
            $order['lid'] = $lottery[$order['lid']];
            $order['uname'] = $uinfo['uname'];
            $order['isHot'] = isset($hinfo['isHot_'.$lid]) ? $hinfo['isHot_'.$lid] : '0';
        }

        $headerInfo = $this->getRequestHeaders();
        if(empty($orders['data']) && !empty($data['uname']) && $headerInfo['appVersionCode'] >= '30600')
        {
            // 精确匹配用户
            $userData = $this->united_order_model->getUnitedUser($data['uname']);
            if(!empty($userData) && $cpage == 1)
            {
                $result = array(
                    'status' => '2',
                    'msg' => 'success',
                    'data' =>  array(),
                    'info' => $this->strCode(json_encode(array(
                        'uid' => $userData['uid'],
                    )), 'ENCODE')
                );
            }
            else
            {
                $result = array(
                    'status' => '1',
                    'msg' => 'success',
                    'data' => array(),
                );
            }
        }
        else
        {
            $result = array(
                'status' => '1',
                'msg' => 'success',
                'data' => $orders['data'],
            );
        }
        
        echo json_encode($result);
    }

    /**
     * 个人战绩.
     *
     * @param int $uid
     */
    public function userRecord($uid)
    {
        $this->load->model('user_model');
        $uinfo = $this->user_model->getUserInfo($uid);
        $this->load->model('united_planner_model');
        $award = $this->united_planner_model->findByUid($uid, 'winningTimes,bonus,monthBonus,monthWinTimes');
        $award['uname'] = $uinfo['uname'];
        $award['points'] = $uinfo['united_points'];
        $award['bonus'] = $this->calNum($award['bonus']);
        $award['monthBonus'] = $this->calNum($award['monthBonus']);
        $result = array(
            'status' => '1',
            'msg' => 'success',
            'data' => $award,
        );
        echo json_encode($result);
    }

    /**
     * 个人战绩.
     *
     * @param int $uid
     */
    public function userRecordList($uid)
    {
        $this->load->model('united_order_model');
        $data = $this->input->get(null, true);
        $where = array(
            'o.uid' => intval($uid), 
            'o.status' => 2000, 
            'o.my_status' => array(1, 3)
        );
        if(intval($data['lid'])) {
        	if (in_array($data['lid'], array(33, 35))) {
        		$where['o.lid'] = array(33, 35);
        	}elseif (in_array($data['lid'], array(11, 19))) {
        		$where['o.lid'] = array(11, 19);
        	}else {
        		$where['o.lid'] = intval($data['lid']);
        	}
        }
        $limit = $data['page'] * $data['number'].','.$data['number'];
        $unitedOrders = $this->united_order_model->getOrder($where, 'o.created,o.lid,o.money,o.orderBonus', true, 'created desc', $limit);
        foreach ($unitedOrders as &$order) {
            $order['money'] = $order['money'] / 100;
            $order['orderBonus'] = $order['orderBonus'] / 100;
            $order['scale'] = round($order['orderBonus'] / $order['money'], 2);
            $order['cname'] = BetCnName::getCnName($order['lid']);
        }
        $result = array(
            'status' => '1',
            'msg' => 'success',
            'data' => $unitedOrders,
        );
        echo json_encode($result);
    }

    /**
     * 发起人创建订单接口.
     */
    public function createOrder()
    {
        $data = $this->strCode($this->input->post('data'));
        $data = json_decode($data, true);
        $result = array(
            'status' => '0',
            'msg' => '通讯异常',
            'data' => '',
        );
//        $data = array(
//            'ctype' => 'create',//创建订单类型
//            'uid' => 1024,//uid
//            'userName' => 'yidefu',//用户名
//            'buyPlatform' => '1',//固定值
//            'codes' => '01,03,06,08,15,25|15:1:1',//投注号
//            'lid' => '51',//彩种id
//            'money' => 20,//金额
//            'multi' => 10,//固定值
//            'issue' => 2017030,//期次
//            'playType' => '0',//玩法
//            'isChase' => '0',//固定值
//            'betTnum' => 1,//几注
//            'orderType' => '4',//固定值
//            'endTime' => '2017-03-16 20:29:00',//合买截止时间
//            'buyMoney' => 5,//购买总金额
//            'guaranteeAmount' => 0,//保底金额
//            'commissionRate' => '0',//佣金比例
//            'openStatus' => '0',//公开状态: 0 公开,1 仅对跟单者公开,2 截止后公开
//            'openEndtime' => '2017-03-16 21:05:00',//截止时间
//        );
        $params = array(
            'ctype' => '',
            'uid' => '',
            'userName' => '',
            'buyPlatform' => '',
            'codes' => '',
            'lid' => '',
            'money' => '',
            'multi' => '',
            'issue' => '',
            'playType' => '',
            'isChase' => '',
            'betTnum' => '',
            'orderType' => '',
            'endTime' => '',
            'buyMoney' => '',
            'guaranteeAmount' => '',
            'commissionRate' => '',
            'openStatus' => '',
            'openEndtime' => '',
        );
        // 必要参数检查
        foreach ($params as $key => $items) {
            if ($data[$key] === '' || !isset($data[$key])) {
                $result = array(
                    'status' => '0',
                    'msg' => '缺少必要参数',
                    'data' => '',
                );
                echo json_encode($result);
                exit();
            }
        }

        // 检查用户登录状态
        $this->load->model('user_model');
        $uinfo = $this->user_model->getUserInfo($data['uid']);

        if (empty($uinfo)) {
            $result = array(
                'status'    =>  ($this->versionInfo['appVersionCode'] >= '40200') ? '700' : '300',
                'msg'       =>  '用户登录信息过期',
                'data'      =>  '',
            );
            echo json_encode($result);
            exit();
        }

        // 获取版本信息
        $headerInfo = $this->getRequestHeaders();

        // 用户是否注销
        if (isset($uinfo['userStatus']) && $uinfo['userStatus'] == '1' && $headerInfo['appVersionCode'] >= '3') {
            $result = array(
                'status'    =>  ($this->versionInfo['appVersionCode'] >= '40200') ? '700' : '300',
                'msg'       =>  '您的账号已注销，被注销的账号不能使用原手机号再注册，请注册新账号登录',
                'data'      =>  '',
            );
            echo json_encode($result);
            exit();
        }

        if (isset($uinfo['userStatus']) && $uinfo['userStatus'] == '2') {
            $result = array(
                    'status' => '0',
                    'msg' => '您的账户已被冻结，如需解冻请联系客服。',
                    'data' => '',
            );
            die(json_encode($result));
        }

        $uinfo['uid'] = $data['uid'];

        if (!$this->checkUserAuth($uinfo, $data['auth']) && $headerInfo['appVersionCode'] >= '3') {
            $result = array(
                'status'    =>  ($this->versionInfo['appVersionCode'] >= '40200') ? '700' : '300',
                'msg'       =>  '您的登录密码已修改，请重新登录',
                'data'      =>  '',
            );
            echo json_encode($result);
            exit();
        }
        unset($data['auth']);

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

        // 订单金额范围检查
        if(in_array($data['lid'], array(42, 43)))
        {
            if($data['money'] > 200000)
            {
                $result = array(
                    'status' => '0',
                    'msg' => '订单金额需小于20万，请修改订单后重新投注',
                    'data' => ''
                );
                echo json_encode($result);
                exit();
            }
        }
        else
        {
            if ($data['money'] > 20000) 
            {
                $result = array(
                    'status' => '0',
                    'msg' => '订单金额需小于2万，请修改订单后重新投注',
                    'data' => '',
                );
                echo json_encode($result);
                exit();
            }
        }

        // 版本彩种销售判断
        $appConfig = $this->Cache->getAppConfig('android');
        if (!empty($appConfig[$headerInfo['appVersionCode']]['lotteryConfig'])) {
            $saleConfig = json_decode($appConfig[$headerInfo['appVersionCode']]['lotteryConfig'], true);
            if (isset($saleConfig[$data['lid']]) && $saleConfig[$data['lid']] == '1') {
                $result = array(
                    'status' => '0',
                    'msg' => '当前彩种已停售',
                    'data' => '',
                );
                echo json_encode($result);
                exit();
            }
        }

        // 截止时间处理
        $issueInfo = $this->Cache->getIssueInfo($data['lid']);
        $lotteryConfig = $this->Cache->getlotteryConfig();

        if (in_array($data['lid'], array('42', '43'))) {
            if ($data['codecc'] === '') {
                $result = array(
                    'status' => '0',
                    'msg' => '缺少必要参数',
                    'data' => '',
                );
                echo json_encode($result);
                exit();
            }
        } else {
            // 期次信息检查
            if ($issueInfo['cIssue']['seExpect'] != $data['issue']) {
                $result = array(
                    'status' => '0',
                    'msg' => '投注不在当前销售期',
                    'data' => '',
                );
                echo json_encode($result);
                exit();
            }
        }

        if (!empty($issueInfo['aIssue']) && in_array($data['lid'], array('51', '23529', '35', '10022', '23528', '33', '52')) && time() > (floor($issueInfo['aIssue']['seEndtime'] / 1000) - $lotteryConfig[$data['lid']]['ahead'] * 60) && time() < floor($issueInfo['aIssue']['seEndtime'] / 1000)) {
            $result = array(
                'status' => '0',
                'msg' => '期次更新中，请于'.date('H:i', (floor($issueInfo['aIssue']['seEndtime'] / 1000))).'后投注下期'.$issueInfo['cIssue']['seExpect'],
                'data' => '',
            );
            echo json_encode($result);
            exit();
        }

        // 停售
        $is_sale = $this->config->item('is_sale');
        if (!$is_sale) {
            $result = array(
                'status' => '0',
                'msg' => '当前彩种已停售',
                'data' => '',
            );
            echo json_encode($result);
            exit();
        }

        $this->load->model('lottery_model');
        $lotteryConfig = $this->lottery_model->getLotteryConfig($data['lid'], 'status,united_status');
        if ($lotteryConfig['status'] == 0 || $lotteryConfig['united_status'] == 0 )
        {
            $result = array(
                'status' => '0',
                'msg' => '当前彩种合买已停售',
                'data' => '',
            );
            echo json_encode($result);
            exit();
        }
        // 初始化订单信息
        if (ENVIRONMENT === 'checkout') {
            $orderUrl = $this->config->item('cp_host');
            $data['HOST'] = $this->config->item('domain');
        } else {
            // $orderUrl = $this->config->item('pages_url');
            $orderUrl = $this->config->item('protocol') . $this->config->item('pages_url');
        }

        // 版本 渠道信息处理
        $data['app_version'] = (isset($headerInfo['appVersionName']) && !empty($headerInfo['appVersionName'])) ? $headerInfo['appVersionName'] : '1.0';
        $data['channel'] = $this->recordChannel($headerInfo['channel']);
        //临时关闭部分渠道包购彩
        $channelArr = $this->Cache->getLimitChannel();
        if (in_array($data['channel'], $channelArr)) {
            $result = array(
                'status' => '0',
                'msg' => '暂停售彩',
                'data' => '',
            );
            echo json_encode($result);
            exit();
        }
        $data['buyPlatform'] = $this->config->item('platform');

        $data['ctype'] = 'create';
        // 处理空格问题
        $data['issue'] = trim($data['issue']);
        // 处理 ForecastBonusv 字段
        $data['ForecastBonusv'] = $data['ForecastBonusv'] ? $data['ForecastBonusv'] : ($data['forecastBonusv'] ? $data['forecastBonusv'] : '');
        // 新增合买宣言 - v4.6
        if(!empty($data['united_intro']))
        {
            $this->load->library('comm');
            $data['united_intro'] = $this->security->xss_clean($data['united_intro']);
            if($this->comm->abslength($data['united_intro']) > 80)
            {
                $result = array(
                    'status' => '0',
                    'msg' => '合买宣言内容过长，请重新输入！',
                    'data' => '',
                );
                echo json_encode($result);
                exit();
            }
        }

        $createStatus = $this->tools->request($orderUrl.'api/order/createOrder', $data);
        $createStatus = json_decode($createStatus, true);

        if ($createStatus['status']) {
            // 创建结果处理
            $payView = $this->orderComplete($createStatus['data'], 4);
            $result = array(
                'status' => '1',
                'msg' => '创建订单成功',
                'data' => $payView,
            );
            echo json_encode($result);
            exit();
        } else {
            $result = array(
                'status' => '0',
                'msg' => $createStatus['msg'],
                'data' => '',
            );
            echo json_encode($result);
            exit();
        }
    }

    /**
     * APP 订单创建成功处理.
     *
     * @date:2016-01-18
     */
    public function orderComplete($data, $orderType = 0)
    {
        // 订单信息加密
        $orderDetail = $this->strCode(json_encode(array(
            'uid' => $data['uid'],
            'orderId' => $data['orderId'],
            'orderType' => $orderType,
            'ctype' => '0',
            'buyMoney' => $data['buyMoney'],
                )), 'ENCODE');

        // 跳转支付页面
        $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? 'https:' : 'http:';
        $payView = $protocol.$this->config->item('pages_url').'app/order/doPay/'.urlencode($orderDetail);

        return $payView;
    }

    /**
     * 合买撤单接口.
     *
     * @param int $orderId
     */
    public function cancelUnitedOrder($orderId)
    {
        if (!$orderId) {
            $result = array(
                'status' => '0',
                'msg' => 'orderId不能为空',
                'data' => '',
            );
            echo json_encode($result);
            exit();
        }
        if (ENVIRONMENT === 'checkout') {
            $host = $this->config->item('cp_host');
        } else {
            // $host = 'http://www.166cai.com/';
            $host = $this->config->item('protocol') . $this->config->item('pages_url');
        }
        $this->load->model('united_order_model', 'unitedOrder');
        $res = $this->unitedOrder->cancelOrder($orderId);
        if ($res['status'] != "success") {
            $result = array(
                'status' => '0',
                'msg' => "{$res['message']}",
                'data' => '',
            );
            die(json_encode($result));
        }
        $createStatus = $this->tools->request($host.'api/order/cancelOrder?id='.$orderId);
        $createStatus = json_decode($createStatus, true);
        if ($createStatus['status'] == 'success') {
            $result = array(
                'status' => '1',
                'msg' => '撤单成功',
                'data' => '',
            );
        } else {
            $result = array(
                'status' => '0',
                'msg' => "{$createStatus['message']}",
                'data' => '',
            );
        }
        echo json_encode($result);
    }

    /**
     * 参与合买列表.
     *
     * @param int $orderId
     */
    public function unitedList($orderId)
    {
        $this->load->model('united_order_model');
        $data = $this->input->get(null, true);
        $limit = $data['page'] * $data['number'].','.$data['number'];
        $users = $this->united_order_model->getJoin($orderId, array('userName' => 1), 'o.puid,o.uid,o.created, o.buyMoney, o.margin, o.status', true, 'created', $limit);
        foreach ($users as $k => $user) {
            $users[$k]['start'] = 0;
            if ($user['uid'] == $user['puid']) {
                $users[$k]['start'] = 1;
            }
            $users[$k]['buyMoney'] = $user['buyMoney'] / 100;
            $users[$k]['margin'] = $user['margin'] / 100;
            if ($user['uname']) {
                $users[$k]['uname'] = mb_strlen($user['uname'], 'utf-8') > 3 ? mb_substr($user['uname'], 0, 3, 'utf-8') . '***' : mb_substr($user['uname'], 0, mb_strlen($user['uname'], 'utf-8') - 1, 'utf-8') . '*';
            } else {
                $users[$k]['uname'] = '166彩票保底';
            }
            unset($users[$k]['uid']);
            unset($users[$k]['puid']);
        }
        $result = array(
            'status' => '1',
            'msg' => 'success',
            'data' => $users,
        );
        echo json_encode($result);
    }

    /**
     * 转换金额.
     *
     * @param int $num
     *
     * @return string
     */
    private function calNum($num)
    {
        $str = '';
        if (floor($num / 10000000000) > 0) {
            $str .= floor($num / 10000000000).'亿';
        }
        if (floor($num / 1000000) > 0) {
            $str .= floor(($num - (floor($num / 10000000000) * 10000000000)) / 1000000).'万';
        }
        if (floor($num / 10000000000) <= 0) {
            $str .= floor(($num - (floor($num / 1000000) * 1000000)) / 100).'元';
        }

        return $str;
    }

    /**
     * 合买跟单.
     */
    public function followOrder()
    {
        $data = $this->strCode($this->input->post('data'));
        $data = json_decode($data, true);
//        $data = array(
//            'ctype' => 'pay',//创建订单类型
//            'uid' => 1024,//uid
//            'buyPlatform' => '1',//固定值
//            'orderId' => '20170313110538212339',//orderid
//            'money' => 2,//金额
//        );
        $params = array(
            'ctype' => '',
            'uid' => '',
            'buyPlatform' => '',
            'orderId' => '',
            'money' => '',
        );
        // 必要参数检查
        foreach ($params as $key => $items) {
            if ($data[$key] === '' || !isset($data[$key])) {
                $result = array(
                    'status' => '0',
                    'msg' => '缺少必要参数',
                    'data' => '',
                );
                echo json_encode($result);
                exit();
            }
        }

        // 检查用户登录状态
        $this->load->model('user_model');
        $uinfo = $this->user_model->getUserInfo($data['uid']);

        if (empty($uinfo)) {
            $result = array(
                'status'    =>  ($this->versionInfo['appVersionCode'] >= '40200') ? '700' : '300',
                'msg'       =>  '用户登录信息过期',
                'data'      =>  '',
            );
            echo json_encode($result);
            exit();
        }

        // 获取版本信息
        $headerInfo = $this->getRequestHeaders();

        // 用户是否注销
        if (isset($uinfo['userStatus']) && $uinfo['userStatus'] == '1' && $headerInfo['appVersionCode'] >= '3') {
            $result = array(
                'status'    =>  ($this->versionInfo['appVersionCode'] >= '40200') ? '700' : '300',
                'msg'       =>  '您的账号已注销，被注销的账号不能使用原手机号再注册，请注册新账号登录',
                'data'      =>  '',
            );
            echo json_encode($result);
            exit();
        }

        if (isset($uinfo['userStatus']) && $uinfo['userStatus'] == '2') {
            $result = array(
                    'status' => '0',
                    'msg' => '您的账户已被冻结，如需解冻请联系客服。',
                    'data' => '',
            );
            die(json_encode($result));
        }

        $uinfo['uid'] = $data['uid'];

        if (!$this->checkUserAuth($uinfo, $data['auth']) && $headerInfo['appVersionCode'] >= '3') {
            $result = array(
                'status'    =>  ($this->versionInfo['appVersionCode'] >= '40200') ? '700' : '300',
                'msg'       =>  '您的登录密码已修改，请重新登录',
                'data'      =>  '',
            );
            echo json_encode($result);
            exit();
        }
        unset($data['auth']);

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

        // 订单金额范围检查
        $this->load->model('united_order_model');
        $orderInfo = $this->united_order_model->getUniteOrderByOrderId($data['orderId'], 'lid, issue, buyTotalMoney, money, endTime, status, playType');
        if(in_array($orderInfo['lid'], array(42, 43)))
        {
            if($data['money'] > 200000)
            {
                $result = array(
                    'status' => '0',
                    'msg' => '订单金额需小于20万，请修改订单后重新投注',
                    'data' => ''
                );
                echo json_encode($result);
                exit();
            }
        }
        else
        {
            if ($data['money'] > 20000) {
                $result = array(
                    'status' => '0',
                    'msg' => '订单金额需小于2万，请修改订单后重新投注',
                    'data' => '',
                );
                echo json_encode($result);
                exit();
            }
        }
        
        // 初始化订单信息
        if (ENVIRONMENT === 'checkout') {
            $orderUrl = $this->config->item('cp_host');
            $data['HOST'] = $this->config->item('domain');
        } else {
            // $orderUrl = $this->config->item('pages_url');
            $orderUrl = $this->config->item('protocol') . $this->config->item('pages_url');
        }

        // 版本 渠道信息处理
        $data['app_version'] = (isset($headerInfo['appVersionName']) && !empty($headerInfo['appVersionName'])) ? $headerInfo['appVersionName'] : '1.0';
        $data['channel'] = $this->recordChannel($headerInfo['channel']);
        //临时关闭部分渠道包购彩
        $channelArr = $this->Cache->getLimitChannel();
        if (in_array($data['channel'], $channelArr)) {
            $result = array(
                'status' => '0',
                'msg' => '暂停售彩',
                'data' => '',
            );
            echo json_encode($result);
            exit();
        }
        
        $this->load->model('lottery_model');
        $lotteryConfig = $this->lottery_model->getLotteryConfig($orderInfo['lid'], 'status,united_status');
        if ($lotteryConfig['status'] == 0 || $lotteryConfig['united_status'] == 0 )
        {
            // $result = array(
            //     'status' => '0',
            //     'msg' => '当前彩种合买已停售',
            //     'data' => '',
            // );
            // echo json_encode($result);
            // exit();
        }
        $message = '';
        if (in_array($orderInfo['status'], array(600, 610, 620))) {
            $message = '该合买方案已撤单';
        } elseif (time() > strtotime($orderInfo['endTime'])) {
            $message = '该合买方案已截止';
        } elseif ($orderInfo['status'] < 40 || $orderInfo['status'] > 500 || $orderInfo['money'] == $orderInfo['buyTotalMoney']) {
            $message = '该合买方案已满员';
        } elseif ($data['money'] && $data['money'] * 100 > $orderInfo['money'] - $orderInfo['buyTotalMoney']) {
            $message = '该合买方案剩余金额不足';
        }
        if ($message) {
            $result = array(
                'status' => '0',
                'msg' => "{$message}",
                'data' => '',
            );
            echo json_encode($result);
            exit();
        }
        $orderDetail = $this->strCode(json_encode(array(
            'uid' => $data['uid'],
            'orderId' => $data['orderId'],
            'orderType' => 4,
            'buyMoney' => $data['money'],
            'ctype' => '1',
                )), 'ENCODE');

        // 跳转支付页面
        $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? 'https:' : 'http:';
        $payView = $protocol.$this->config->item('pages_url').'app/order/doPay/'.urlencode($orderDetail);
        $result = array(
                'status' => '1',
                'msg' => 'success',
                'data' => $payView,
            );
        echo json_encode($result);
    }

    /**
     * 热门红人.
     */
    public function hotUserList()
    {
        $lid = $this->input->get('lid', true);
        $lid ? $lid : $lid = 0;
        $this->load->model('united_order_model');
        $hotPlanner = $this->united_order_model->getHotPlanner($lid);
        $data = array();
        $this->load->model('united_planner_model');
        foreach ($hotPlanner as $hp) {
            $uinfo = $this->united_planner_model->findUserStatus($hp['uid']);
            $data[] = array('uname' => $uinfo['uname'], 'isOrdering' => $uinfo['isOrdering']);
        }
        $result = array(
                'status' => '1',
                'msg' => 'success',
                'data' => $data,
            );
        echo json_encode($result);
    }

    /**
     * 模糊查询用户名.
     */
    public function queryUser()
    {
        $name = $this->input->get('name', true);
        // 关停接口
        $result = array(
            'status' => '1',
            'msg' => 'success',
            'data' => '',
        );
        die(json_encode($result));
        if ($name) {
            $this->load->model('user_model');
            $users = $this->user_model->queryUserName($name);
            $name = '';
            array_walk($users, function ($value, $key) use (&$name) {
                $name .= $value['uname'];
                if ($key < 4) {
                    $name .= ',';
                }
                return $name;
            });
            $result = array(
                'status' => '1',
                'msg' => 'success',
                'data' => $name,
            );
            echo json_encode($result);
        } else {
            $result = array(
                'status' => '1',
                'msg' => 'success',
                'data' => array(),
            );
            echo json_encode($result);
        }
    }

    // 查看个人战绩
    public function getUserRecord()
    {
        $data = $this->strCode($this->input->post('data'));
        $data = json_decode($data, true);
        
        if(empty($data['uid']))
        {
            $result = array(
                'status' => '0',
                'msg' => '缺少必要参数',
                'data' => '',
            );
            echo json_encode($result);
            exit();
        }

        $this->load->model(array('united_planner_model', 'user_model'));
        $uinfo = $this->user_model->getUserInfo($data['uid']);

        if(!empty($uinfo))
        {
            $info = $this->united_planner_model->findByUid($data['uid'], null, $data['lid']);

            // 合买注人数、定制人数
            $followData = $this->united_planner_model->getCountFollowed($data['uid'], $data['lid']);

            // 个人简介
            $desInfo = $this->united_planner_model->getUserDescription($data['uid']);

            $records = array(
                'uname'         =>  $uinfo['uname'],
                'uid'           =>  $data['uid'],
                'lid'           =>  $data['lid'] ? $data['lid'] : 0,
                'winningTimes'  =>  $info ? $info['winningTimes'] : 0,
                'bonus'         =>  $info ? $this->calNum($info['bonus']) : '0元',
                'monthWinTimes' =>  $info ? $info['monthWinTimes'] : 0,
                'monthBonus'    =>  $info ? $this->calNum($info['monthBonus']) : '0元',
                'unitedPoints'  =>  $info ? $info['united_points'] : 0,
                'lids'          =>  '',
                'lidNames'      =>  '',
                'description'   =>  (!empty($desInfo['introduction']) && ( ($desInfo['introduction_status'] == 1 && $data['puid'] != $data['uid']) || ($data['puid'] == $data['uid'] && $desInfo['introduction_status'] < 2) )) ? $desInfo['introduction'] : $this->config->item('united_intro'),
                'followCount'   =>  $followData['followCount'] ? $followData['followCount'] : 0,
                'followed'      =>  '0',    // 合买关注状态 0:未关注 1:已关注
                'followLimit'   =>  '0',    // 定制状态 0:可定制 1:不可定制    
                'gendanCount'   =>  $followData['gendanCount'] ? $followData['gendanCount'] : 0,
            );
            // 当前登录用户
            if(!empty($data['puid']))
            {
                $this->load->model('follow_order_model');
                $lottery = array('51' => '双色球', '23529' => '大乐透', '42' => '竞彩足球', '43' => '竞彩篮球', '11' => '胜负', '19' => '任九', '52' => '福彩3D', '23528' => '七乐彩', '10022' => '七星彩', '33' => '排列三', '35' => '排列五');
                if($data['lid'] == 0) 
                {
                    $lid = array('51', '23529', '42', '43', '11', '19', '52', '23528', '10022', '33', '35');
                } 
                else 
                {
                    if($data['lid'] == 11) 
                    {
                        $lid = array(11, 19);
                    } 
                    elseif($data['lid'] == 33) 
                    {
                        $lid = array(33, 35);
                    } 
                    else 
                    {
                        $lid = array($data['lid']);
                    }
                }
                $followCheck = $this->follow_order_model->getAllCanGendan($data['puid'], $data['uid'], $lid);
                $lids = $followCheck['lids'];
                $lidNames = array();
                foreach($lids as $lid) 
                {
                    $lidNames[] = $lottery[$lid];
                }
                if(!empty($lids)) 
                {
                    $records['lids'] = implode(',', $lids);
                    $records['lidNames'] = implode(',', $lidNames);
                } 
                else 
                {
                    $records['lids'] = '';
                    $records['lidNames'] = '';
                }
                // 合买关注状态
                $records['followed'] = $this->united_planner_model->getFollowStatus($data['uid'], $data['puid']);
                // 跟单限制
                $records['followLimit'] = (!$followCheck['followed']) ? '1' : $records['followLimit'];
            }

            $result = array(
                'status' => '1',
                'msg' => 'success',
                'data' => $records,
            );
        }
        else
        {
            $result = array(
                'status' => '0',
                'msg' => '请求参数错误',
                'data' => '',
            );
        }
        echo json_encode($result);     
    }

    // 新版个人合买中奖记录
    public function getUserRecordList()
    {
        $data = $this->strCode($this->input->post('data'));
        $data = json_decode($data, true);

        if(empty($data['uid']))
        {
            $result = array(
                'status' => '0',
                'msg' => '缺少必要参数',
                'data' => '',
            );
            echo json_encode($result);
            exit();
        }

        $this->load->model('united_order_model');

        $where = array(
            'o.uid'         =>  $data['uid'], 
            'o.status'      =>  2000, 
            'o.my_status'   =>  array(1, 3)
        );

        if(intval($data['lid']))
        {
            // 胜负彩/任九 排三/五
            if(in_array(intval($data['lid']), array('11', '19')))
            {
                $where['o.lid'] = array('11', '19');
            }
            elseif(in_array(intval($data['lid']), array('33', '35')))
            {
                $where['o.lid'] = array('33', '35');
            }
            else
            {
                $where['o.lid'] = intval($data['lid']);
            } 
        }
        $data['page'] = intval($data['page']) ? intval($data['page']) : 1;
        $data['number'] = intval($data['number']) ? intval($data['number']) : 10;

        $limit = ($data['page'] - 1) * $data['number'] . ',' . $data['number'];
        $unitedOrders = $this->united_order_model->getOrder($where, 'o.created, o.lid, o.money, o.orderBonus', true, 'created desc', $limit);

        if(!empty($unitedOrders))
        {
            foreach ($unitedOrders as $k => &$order) 
            {
                $order['money'] = $order['money'] / 100;
                $order['orderBonus'] = $order['orderBonus'] / 100;
                $order['scale'] = round($order['orderBonus'] / $order['money'], 2);
                $order['cname'] = BetCnName::getCnName($data['lid']);
            }
        }
        
        $result = array(
            'status' => '1',
            'msg' => 'success',
            'data' => $unitedOrders,
        );
        echo json_encode($result);
    }

    // 个人战绩 - 编辑个人简介
    public function editDescription()
    {
        $data = $this->strCode($this->input->post('data'));
        $data = json_decode($data, true);

        if(!empty($data['uid']) && !empty($data['description']))
        {
            $data['description'] = $this->security->xss_clean($data['description']);
            // 检查用户登录状态
            $this->load->model('user_model');
            $uinfo = $this->user_model->getUserInfo($data['uid']);

            if (empty($uinfo)) {
                $result = array(
                    'status'    =>  ($this->versionInfo['appVersionCode'] >= '40200') ? '700' : '300',
                    'msg'       =>  '用户登录信息过期',
                    'data'      =>  '',
                );
                echo json_encode($result);
                exit();
            }

            // 获取版本信息
            $headerInfo = $this->getRequestHeaders();

            // 用户是否注销
            if (isset($uinfo['userStatus']) && $uinfo['userStatus'] == '1' && $headerInfo['appVersionCode'] >= '3') 
            {
                $result = array(
                    'status'    =>  ($this->versionInfo['appVersionCode'] >= '40200') ? '700' : '300',
                    'msg'       =>  '您的账号已注销，被注销的账号不能使用原手机号再注册，请注册新账号登录',
                    'data'      =>  '',
                );
                echo json_encode($result);
                exit();
            }

            if (isset($uinfo['userStatus']) && $uinfo['userStatus'] == '2') 
            {
                $result = array(
                    'status'    =>  '0',
                    'msg'       =>  '您的账户已被冻结，如需解冻请联系客服。',
                    'data'      =>  '',
                );
                die(json_encode($result));
            }

            if(mb_strlen($data['description']) < 10)
            {
                $result = array(
                    'status'    =>  '0',
                    'msg'       =>  '个人简介最少填写10个字',
                    'data'      =>  '',
                );
                die(json_encode($result));
            }

            if(mb_strlen($data['description']) > 80)
            {
                $result = array(
                    'status'    =>  '0',
                    'msg'       =>  '个人简介最多填写80个字',
                    'data'      =>  '',
                );
                die(json_encode($result));
            }

            $this->user_model->editIntroduction($data['uid'], $data['description']);
            $result = array(
                'status'    =>  '1',
                'msg'       =>  '编辑成功',
                'data'      =>  '',
            );
            die(json_encode($result));
        }
        else
        {
            $result = array(
                'status'    =>  '0',
                'msg'       =>  '缺少必要参数',
                'data'      =>  '',
            );
            die(json_encode($result));
        }
    }
}
