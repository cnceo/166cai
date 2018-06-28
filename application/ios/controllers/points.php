<?php

/*
 * 积分商城
 * @date:2018-03-08
 */

class Points extends MY_Controller
{

    public function __construct()
    {
        parent::__construct();
        //$this->uid = 4512;
        $this->load->model('member_model');
        $this->load->model('user_model', 'User');
    }
    
    public function getMemberInfo()
    {
        $uid = $this->uid;
        if (empty($uid)) {
            echo '访问错误';
            return;
        }
        $uinfo = $this->User->getUserInfo($uid);
        $user = array();
        $user['uid'] = $uid;
        $user['uname'] = $uinfo['uname'];
        $user['headimgurl'] = $uinfo['headimgurl'];
        $user['grade'] = $uinfo['grade'];
        $user['grade_value'] = $uinfo['points'];
        $user['last_year_points'] = $uinfo['last_year_points'];
        $result = array(
            'status' => '200',
            'msg' => '成功',
            'data' => $user
        );
        echo json_encode($result);
    }
    
    public function getTaskList()
    {
        $uid = $this->uid;
        if (empty($uid)) {
            echo '访问错误';
            return;
        }
        $jobs = $this->member_model->getPointJob();
        foreach ($jobs as $k => $v) {
            $jobs[$k]['doStatus'] = $this->member_model->getJobStatus($v['id'], $v['type'], $this->uid);
        }
        $imgs = array(
            'caipiaoimg/static/images/privilege/task_one.png',
            'caipiaoimg/static/images/privilege/task_two.png',
            'caipiaoimg/static/images/privilege/task_three.png',
            'caipiaoimg/static/images/privilege/task_four.png',
            'caipiaoimg/static/images/privilege/task_five.png',
        );
        $joblist = array();
        foreach ($jobs as $k => $job) {
            $joblist[$k]['id'] = $job['id'];
            $joblist[$k]['hot'] = $job['hot'];
            $joblist[$k]['title'] = $job['title'];
            $joblist[$k]['desc'] = $job['desc'];
            $joblist[$k]['imgurl'] = $this->config->item('protocol') . $this->config->item('pages_url') . $imgs[$k];
            $joblist[$k]['awards'] = $job['value'];
            $joblist[$k]['awardsNum'] = 1;
            $joblist[$k]['doStatus'] = $job['doStatus'];
        }
        $redpacks = $this->member_model->getExchangeRedPack();
        $redpacklist = array();
        $uinfo = $this->User->getUserInfo($uid);
        foreach ($redpacks as $k => $redpack) {
            $redpacklist[$k]['rid'] = $redpack['rid'];
            $redpacklist[$k]['money'] = $redpack['money']/100;
            $redpacklist[$k]['p_name'] = $redpack['p_name'];
            $redpacklist[$k]['out'] = $redpack['today_out'] - $redpack['already_out'];
            $arr = json_decode($redpack['use_params'], true);
            $redpacklist[$k]['price'] = ($arr['lv' . $uinfo['grade']] == '--') ? $arr['price'] : $arr['lv' . $uinfo['grade']];
        }
        $result = array(
            'status' => '200',
            'msg' => '成功',
            'data' => array(
                'joblist' => $joblist,
                'redpacklist' => $redpacklist
            )
        );
        echo json_encode($result);
    }

    public function getPoint()
    {
        $uid = $this->uid;
        $jid = $this->input->post("id", true);
        $type = $this->input->post("type", true);
        if (empty($uid)) {
            $result = array(
                'status' => '300',
                'msg' => '未登录',
                'data' => ''
            );
            die(json_encode($result));
        }
        $res = $this->member_model->insertLog($jid, $type, $uid);
        if ($res['code'] == 200) {
            //刷新缓存
            $this->User->freshUserGrowth($uid);
            $code = '200';
        }else{
            $code = '0';
        }
        $result = array(
            'status' => $code,
            'msg' => $res['msg'],
            'data' => ''
        );
        echo json_encode($result);
    }

    public function exchangeRedPack()
    {
        $uid = $this->uid;
        $rid = $this->input->post("rid", true);
        if (empty($uid)) {
            $result = array(
                'status' => '300',
                'msg' => '未登录',
                'data' => ''
            );
            die(json_encode($result));
        }
        $uinfo = $this->User->getUserInfo($uid);
        if ($uinfo['grade'] < 2) {
            $result = array(
                'status' => '0',
                'msg' => '青铜及以上会员才可以兑换红包哦',
                'data' => ''
            );
            echo json_encode($result);
            die;
        }
        //兑换红包 插入红包
        $res = $this->member_model->exchangeRedPack($rid, $uid);
        if ($res['code'] == 200) {
            //刷新缓存
            $this->User->freshUserGrowth($uid);
            $result = array(
                'status' => '200',
                'msg' => '兑换成功',
                'data' => ''
            );
        } else {
            $result = array(
                'status' => '0',
                'msg' => $res['msg'],
                'data' => ''
            );
        }
        echo json_encode($result);
        die;
    }
    
    public function getRedPackInfo()
    {
        $uid = $this->uid;
        $rid = $this->input->post("rid", true);
        if (empty($uid)) {
            $result = array(
                'status' => '300',
                'msg' => '未登录',
                'data' => ''
            );
            die(json_encode($result));
        }
        $redpacks = $this->member_model->getExchangeRedPack();
        $redpackInfo = array();
        $uinfo = $this->User->getUserInfo($uid);
        foreach ($redpacks as $redpack) {
            if ($rid == $redpack['rid']) {
                $redpackInfo['rid'] = $redpack['rid'];
                $redpackInfo['money'] = $redpack['money'] / 100;
                $redpackInfo['out'] = $redpack['today_out'] - $redpack['already_out'];
                $arr = json_decode($redpack['use_params'], true);
                $redpackInfo['price'] = ($arr['lv' . $uinfo['grade']] == '--') ? $arr['price'] : $arr['lv' . $uinfo['grade']];
            }
        }
        $result = array(
            'status' => '200',
            'msg' => '红包信息',
            'data' => $redpackInfo
        );
        echo json_encode($result);
    }

    public function getPointLists()
    {
        $uid = $this->uid;
        $ctype = $this->input->get("ctype");
        $page = intval($this->input->get("cpage"));
        $size = intval($this->input->get("size"));
        $page = $page <= 1 ? 1 : $page;
        $size = $size > 0 ? $size : 10;
        if (empty($uid)) {
            $result = array(
                'status' => '300',
                'msg' => '未登录',
                'data' => ''
            );
            die(json_encode($result));
        }
        $searchData = array('ctype' => $ctype, 'date' => 2);
        $logs = $this->member_model->getListData($uid, $searchData, $page, $size);
        $pointLists = array();
        $ctypes = array(0 => '购彩获得', 1 => '任务获得', 2 => '积分赠送', 3 => '兑换红包', 4 => '积分过期');
        $token = urlencode($this->strCode(json_encode(array(
            'uid' => $uid)), 'ENCODE'));
        foreach ($logs['res'] as $k => $log) {
            $pointLists[$k]['id'] = $log['id'];
            $pointLists[$k]['type'] = $ctypes[$log['ctype']];
            $pointLists[$k]['created'] = $log['created'];
            if ($log['mark'] == 1 && $log['value'] > 0) {
                $pointLists[$k]['num'] = $log['mark'] == 1 ? $log['value'] : 0;
                $pointLists[$k]['numStatus'] = 1;
            }
            if ($log['mark'] == 0 && $log['value'] > 0) {
                $pointLists[$k]['num'] = $log['mark'] == 0 ? $log['value'] : 0;
                $pointLists[$k]['numStatus'] = -1;
            }
            $pointLists[$k]['uvalue'] = $log['uvalue'];
            $pointLists[$k]['status'] = $log['status'];
            $pointLists[$k]['trade_no'] = $log['trade_no'];
            if ($log['ctype'] == 0) {
                if ($log['status'] == 0) {
                    $pointLists[$k]['url'] = $this->config->item('protocol') . $this->config->item('pages_url') . 'ios/order/detail/' . $log['orderId'] .'/'. $token;
                } elseif ($log['status'] == 1) {
                    $pointLists[$k]['url'] = $this->config->item('protocol') . $this->config->item('pages_url') . 'ios/order/detail/' . $log['orderId'] .'/'. $token;
                } elseif ($log['status'] == 2) {
                    $pointLists[$k]['url'] = $this->config->item('protocol') . $this->config->item('pages_url') . 'ios/hemai/detail/hm' . $log['orderId'] .'/'. $token;
                } else {
                    $pointLists[$k]['url'] = $this->config->item('protocol') . $this->config->item('pages_url') . 'ios/points/jfdetail/' . $log['trade_no'] .'/'. $token;
                }
            } else {
                $pointLists[$k]['url'] = $this->config->item('protocol') . $this->config->item('pages_url') . 'ios/points/jfdetail/' . $log['trade_no'] .'/'. $token;
            }
        }
        $result = array(
            'status' => '200',
            'msg' => '明细列表',
            'data' => $pointLists
        );
        echo json_encode($result);
    }

    public function mall()
    {
        $this->checkUserAgent();
        $type = $this->input->get("type", true) ? 1 : 0;
        $uid = $this->uid;
        if (empty($uid)) {
            echo '访问错误';
            return;
        }
        $this->load->view('points/mall', array('type' => $type));
    }
    
    public function redpackDetail($id)
    {
        $this->checkUserAgent();
        $uid = $this->uid;
        if (empty($uid)) {
            echo '访问错误';
            return;
        }
        $redpacks = $this->member_model->getExchangeRedPack();
        $redpoints = array();
        foreach ($redpacks as $k => $redpack) {
            $arr = json_decode($redpack['use_params'], true);
            $redpoints[$k] = $arr;
        }
        $token = urlencode($this->strCode(json_encode(array(
                    'uid' => $uid)), 'ENCODE'));
        $this->load->view('points/redpackDetail', array('rid' => $id, 'token' => $token, 'redpoints' => $redpoints));
    }

    public function pointList()
    {
        $this->checkUserAgent();
        $this->load->view('points/pointList');
    }
    
    public function help()
    {
        $this->checkUserAgent();
        $this->load->view('points/help');
    }
    
    public function privilege($id)
    {
        $this->checkUserAgent();
        $this->load->view('points/privilege',array('id' => $id));
    }

    public function privilegeHelp()
    {
        $this->checkUserAgent();
        $this->load->view('points/privilegeHelp');
    }
    
    public function jfdetail($tradeNo, $strCode)
    {
        $this->checkUserAgent();
        $data = $this->strCode(urldecode($strCode));
        $data = json_decode($data, true);
        $uid = isset($data['uid']) ? $data['uid'] : '';
        if (empty($uid)) {
            echo '访问错误';
            return;
        }
        $log = $this->member_model->getOnePoint($tradeNo);
        if ($log['mark'] == 1 && $log['value'] > 0) {
            $log['num'] = '+' . ($log['mark'] == 1 ? $log['value'] : 0);
        }
        if ($log['mark'] == 0 && $log['value'] > 0) {
            $log['num'] = '-' . ($log['mark'] == 0 ? $log['value'] : 0);
        }
        $ctypes = array(0 => '购彩获得', 1 => '任务获得', 2 => '积分赠送', 3 => '兑换红包', 4 => '积分过期');
        $log['ctype'] = $ctypes[$log['ctype']];
        $this->load->view('points/jfdetail', array('log' => $log));
    }

}
