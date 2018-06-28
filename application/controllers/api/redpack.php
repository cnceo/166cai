<?php

/**
 * Copyright (c) 2015,上海二三四五网络科技有限公司.
 * 摘    要:
 * 作    者: 刁寿钧
 * 修改日期: 2016/1/25
 * 修改时间: 15:18
 */

/**
 * @property Red_Pack_Model $red_pack_model
 */
class RedPack extends CI_Controller
{
		
    public function __construct()
    {
        parent::__construct();
        $this->method = 'hongbao166';
        
        $this->load->model('red_pack_model');
    }

    public function send()
    {
    	
        $userId = $this->input->post('userId', TRUE);
        $userType = $this->input->post('userType', TRUE);
        if (empty($userId) || ! is_numeric($userId) || empty($userType) || ! is_numeric($userType))
        {
            list($status, $success, $msg, $data) = array(400, FALSE, '参数不正确', array());

            echo json_encode(compact('status', 'success', 'msg', 'data'));
            exit;
        }
        
        $this->load->library('libredpack');

        list($success, $msg, $data) = $this->libredpack->{$this->method}('send', array('uid' => $userId, 'userType' => $userType ));
        $status = $success ? 200 : 400;

        echo json_encode(compact('status', 'success', 'msg', 'data'));
        exit;
    }

    public function prepare($tradeNo, $userId, $scenario, $packIds)
    {
        list($success, $msg, $data) = $this->red_pack_model->prepare($tradeNo, $userId,
            $scenario, $packIds);
        $status = $success ? 200 : 400;

        echo json_encode(compact('status', 'success', 'msg', 'data'));
        exit;
    }

    public function consume($tradeNo, $packIds)
    {
        list($success, $msg, $data) = $this->red_pack_model
            ->consumeRechargePack($tradeNo, $packIds);
        $status = $success ? 200 : 400;

        echo json_encode(compact('status', 'success', 'msg', 'data'));
        exit;
    }

    public function fetch()
    {
        $userId = $this->input->post('userId', TRUE);
        $eventType = $this->input->post('eventType', TRUE);
        $page = $this->input->post('page', TRUE);
        $step = $this->input->post('step', TRUE);
        if (empty($userId) || ! is_numeric($userId) || ! is_numeric($eventType) ||
            ! is_numeric($page) || ! is_numeric($step)
        )
        {
            list($status, $success, $msg, $data) = array(400, FALSE, '参数不正确', array());

            echo json_encode(compact('status', 'success', 'msg', 'data'));
            exit;
        }

        list($success, $msg, $data) = $this->red_pack_model->fetch($userId, intval($eventType),
            intval($page), intval($step));
        $status = $success ? 200 : 400;

        echo json_encode(compact('status', 'success', 'msg', 'data'));
        exit;
    }

    public function deleteOwn()
    {
        $userId = $this->input->post('userId', TRUE);
        if (empty($userId) || ! is_numeric($userId))
        {
            list($status, $success, $msg, $data) = array(400, FALSE, '参数不正确', array());

            echo json_encode(compact('status', 'success', 'msg', 'data'));
            exit;
        }

        $this->load->library('libredpack');
        list($success, $msg, $data) = $this->libredpack->{$this->method}('deleteOwnPack', array('uid' => $userId));
        $status = $success ? 200 : 400;

        echo json_encode(compact('status', 'success', 'msg', 'data'));
        exit;
    }

    public function activate()
    {
        $userId = $this->input->post('userId', TRUE);
        if (empty($userId) || ! is_numeric($userId))
        {
            list($status, $success, $msg, $data) = array(400, FALSE, '参数不正确', array());

            echo json_encode(compact('status', 'success', 'msg', 'data'));
            exit;
        }
        
        $this->load->library('libredpack');
        $hasAttend = $this->libredpack->{$this->method}('activatePack', array('uid' => $userId));

        $status = $success ? 200 : 400;

        echo json_encode(compact('status', 'success', 'msg', 'data'));
        exit;
    }

    public function checkBound()
    {
        $userId = $this->input->post('userId', TRUE);
        $idCard = $this->input->post('idCard', TRUE);
        if (empty($idCard))
        {
            list($status, $success, $msg, $data) = array(400, FALSE, '参数不正确', array());

            echo json_encode(compact('status', 'success', 'msg', 'data'));
            exit;
        }

        $this->load->library('libredpack');
        list($success, $msg, $data) = $this->libredpack->{$this->method}('checkBound', array('uid' => $userId, 'id_card' => $idCard));
        $status = $success ? 200 : 400;

        echo json_encode(compact('status', 'success', 'msg', 'data'));
        exit;
    }

    public function getUserRedpacks()
    {
        $userId = $this->input->post('userId', TRUE);
        $ctype = $this->input->post('ctype', TRUE);
        $cpage = $this->input->post('cpage', TRUE);
        $psize = $this->input->post('psize', TRUE);

        if (empty($userId) || ! is_numeric($userId) || ! is_numeric($ctype) ||
            ! is_numeric($cpage) || ! is_numeric($psize)
        )
        {
            $res = array(
                'status' => '400',
                'msg' => '参数不正确',
                'data' => ''
            );
            echo json_encode($res);
            exit;
        }

        $cons = array(
            'uid'   =>  $userId,
            'ctype' =>  $ctype,
        );

        $redpacks = $this->red_pack_model->getUserRedpacks($cons, $cpage, $psize);

        $res = array(
            'status' => '200',
            'msg' => '',
            'data' => $redpacks
        );
        echo json_encode($res);
        exit;
    }

    // 购彩红包查询
    public function getBetRedPack()
    {
        $params = array(
            'uid'           =>  $this->input->post('uid', TRUE),
            'lid'           =>  $this->input->post('lid', TRUE),
            'money'         =>  $this->input->post('money', TRUE),
            'buyPlatform'   =>  $this->input->post('buyPlatform', TRUE),
        );
        
        $datas = array();
        list($success, $msg, $redPacks) = $this->red_pack_model->fetchBetPack($params['uid'], '', '', TRUE);
        if($redPacks)
        {
            $this->load->config('order');
            $cType = $this->config->item("redpack_c_type");
            $usable = array();
            $disable = array();
            foreach ($redPacks as $value)
            {
                if(!in_array($value['c_type'], $cType[$params['lid']]))
                {
                    continue;
                }
                $checkAble = 0;

                // 判断使用金额
                $checkAble = (empty($checkAble) && $params['money'] >= $value['money_bar']) ? 1 : 0;
                
                // 判断客户端专享
                if($checkAble && $value['ismobile_used'])
                {
                    if($params['buyPlatform'] == '0')
                    {
                        $checkAble = 0;
                    }
                }

                $info = array(
                    'id' => $value['id'],
                    'money' => ParseUnit($value['money'], 1),
                    'use_desc' => '满' . ParseUnit($value['money_bar'], 1) . '元可用',
                    'money_bar' => ParseUnit($value['money_bar'], 1),
                    'valid_end' => date('m/d H:i', strtotime($value['valid_end'])),
                    'c_name' => $value['c_name'],
                    'ismobile_used' => $value['ismobile_used'],
                    'p_name' => $value['p_name'],
                    'enable' => $checkAble
                );

                if(empty($checkAble))
                {
                    $usable[] = $info;
                }
                else
                {
                    $disable[] = $info;
                }
            }
            
            $datas = array_merge($disable, $usable);
        }

        $res = array(
            'status' => '200',
            'msg' => '通讯成功',
            'data' => $datas
        );
        echo json_encode($res);
        exit;  
    }

    // 查询指定的购彩红包
    public function getRedpackById()
    {
        $params = array(
            'uid'           =>  $this->input->post('uid', TRUE),
            'redpackId'     =>  $this->input->post('redpackId', TRUE),
        );

        $redpackInfo = $this->red_pack_model->getRedpackById($params['uid'], $params['redpackId']);

        $res = array(
            'status' => '200',
            'msg' => '通讯成功',
            'data' => $redpackInfo
        );
        echo json_encode($res);
        exit;
    }


}