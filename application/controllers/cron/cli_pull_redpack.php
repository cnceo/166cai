<?php
/**
 * Copyright (c) 2015,上海快猫文化传媒有限公司.
 * 摘    要: 红包派发验证派发
 * 作    者: 李康建
 * 修改日期: 2017/03/27
 * 修改时间: 14:14
 */

defined('BASEPATH') OR exit('No direct script access allowed');

class Cli_Pull_Redpack extends MY_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->model('activity_model', 'activity');
    }

    /**
     * [index 红包派发]
     * @author JackLee 2017-03-27
     * @return [type] [description]
     */
    public function index()
    {
        $this->activity->doPullRedPack();
        $this->lxredpack();
        $this->redpackpush();
    }
    
    /**
     * 拉新红包发放
     */
    public function lxredpack()
    {
        $this->load->model('activity_lx_model');
        $this->load->model('order_model');
        $this->load->model('redpack/model_hongbao');
        $this->load->model('user_model');
        $users = $this->activity_lx_model->getlxUsers();
        $endtime = $this->activity_lx_model->getlxEndtime();
        foreach ($users as $user) {
            $count = $this->order_model->countOrder($user['uid']);
            if ($count['count'] >= 1) {
                $uids = $this->user_model->userIdCount($user['uid']);
                if (count($uids) > 1) {
                    $alluid = array();
                    foreach ($uids as $uid) {
                        $alluid[] = $uid['uid'];
                    }
                    $has = $this->activity_lx_model->hasLxjoin($alluid, $user['puid'], $user['uid']);
                    if ($has) {
                        continue;
                    }
                }
                $lxCount = $this->activity_lx_model->countLxRed($user['puid']);
                if (!empty($endtime) && strtotime($endtime['start_time']) < time() && strtotime($endtime['end_time']) > time()) {
                    if ($lxCount['count'] < 50) {
                        $res = $this->model_hongbao->sendLxHongbao($user['puid'], $user['uid']);
                        if ($res) {
                            $this->tools->sendSms($user['puid'], $user['phone'], "您有一个新的邀请红包，请及时使用", 10, '127.0.0.1', 193);
                        }
                    }
                    else
                    {
                        $this->activity_lx_model->updateStatus($user['puid'], $user['uid'], 4);
                    }
                }
                else
                {
                    $this->activity_lx_model->updateStatus($user['puid'], $user['uid'], 4);
                }    
            }
        }
    }
    
    //及时推送
    public function redpackpush()
    {
        $this->load->model('redpack_push_model');
        $this->redpack_push_model->redpackPush();
    }

}