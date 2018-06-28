<?php
class Gjc extends MY_Controller {
    
    public function index() {
        $this->load->view('mobileview/gjc/index', array('agent' => 'app'));
    }
        
    public function doBet()
    {
        $lid = $this->input->post('lid', true);
        $codes = $this->input->post('codes', true);
        $betNum = $this->input->post('betTnum', true);
        $multi = $this->input->post('multi', true);
    
        // 参数检查
        if(empty($lid) || !in_array($lid, array(44, 45)) || empty($codes) || empty($betNum) || empty($multi))
        {
            $result = array(
                'status' => '0',
                'msg' => '请求参数错误',
                'data' => ''
            );
            echo json_encode($result);
            exit();
        }
    
        // 登录检查
        if(empty($this->uid))
        {
            $result = array(
                'status' => '2',
                'msg' => '您尚未登录，请先登录',
                'data' => ''
            );
            echo json_encode($result);
            exit();
        }
    
        // 用户状态检查
        $uinfo = $this->user_model->getUserInfo($this->uid);
        if(empty($uinfo))
        {
            $result = array(
                'status' => '2',
                'msg' => '用户信息异常，请重新登录',
                'data' => ''
            );
            echo json_encode($result);
            exit();
        }
    
        if(isset($uinfo['userStatus']) && $uinfo['userStatus'] == '1')
        {
            $result = array(
                'status' => '0',
                'msg' => '您的账户已被注销。',
                'data' => ''
            );
            echo json_encode($result);
            exit();
        }
    
        if(isset($uinfo['userStatus']) && $uinfo['userStatus'] == '2')
        {
            $result = array(
                'status' => '0',
                'msg' => '您的账户已被冻结，如需解冻请联系客服。',
                'data' => ''
            );
            echo json_encode($result);
            exit();
        }
    
        if(empty($uinfo['real_name']) || empty($uinfo['id_card']))
        {
            $result = array(
                'status' => '3',
                'msg' => '请先完成实名认证',
                'data' => ''
            );
            echo json_encode($result);
            exit();
        }
    
        // 获取版本信息
        $versionInfo = $this->version;
        
        // 组装请求参数
        $postData = array(
            'ctype' => 'create',
            'uid' => $this->uid,
            'userName' => $uinfo['uname'],
            'codes' => $codes,
            'lid' => $lid,
            'money' => $betNum * $multi * 2,
            'multi' => trim($multi),
            'issue' => '18001',
            'playType' => '0',
            'betTnum' => $betNum,
            'isChase' => '0',
            'orderType' => '0',
            'endTime' => '2018-07-15 23:00:00',
            'codecc' => '',
            'version' => $versionInfo['appVersionName'],
            'channel' => $this->recordChannel($versionInfo['channel']),
            'buyPlatform' => $this->config->item('platform'),
        );
    
        if(ENVIRONMENT === 'checkout')
        {
            $orderUrl = $this->config->item('cp_host');
            $postData['HOST'] = $this->config->item('domain');
        }
        else
        {
            // $orderUrl = $this->config->item('pages_url');
            $orderUrl = $this->config->item('protocol') . $this->config->item('pages_url');
        }
    
        $createStatus = $this->tools->request($orderUrl . 'api/order/createOrder', $postData);
        $createStatus = json_decode($createStatus, true);
    
        if($createStatus['status'])
        {
            // 创建结果处理
            $payView = $this->orderComplete($createStatus['data']);
            $result = array(
                'status' => '1',
                'msg' => '创建订单成功',
                'data' => $payView
            );
        }
        else
        {
            $result = array(
                'status' => '0',
                'msg' => $createStatus['msg'],
                'data' => ''
            );
        }
        echo json_encode($result);
        exit();
    }
    
    public function orderComplete($data)
    {
        // 订单信息加密
        $orderDetail = $this->strCode(json_encode(array(
            'uid' => $data['uid'],
            'orderId' => $data['orderId'],
            'orderType' => 0
        )), 'ENCODE');
    
        // 跳转支付页面
        $payView = $this->config->item('pages_url') . "app/order/doPay/" . urlencode($orderDetail);
        return $payView;
    }
    
    public function getTeams() {
        $this->load->model('gjc_model');
        $res = $this->gjc_model->getTeams();
        exit(json_encode($res));
    }
    
    public function getCombines() {
        $this->load->model('gjc_model');
        $res = $this->gjc_model->getCombines();
        exit(json_encode($res));
    }
}