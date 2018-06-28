<?php

/*
 * 竞彩活动
 * @date:2016-05-10
 */

class Jcmatch extends MY_Controller {

	public function __construct() 
    {
        parent::__construct();
        $this->load->model('jcmatch_model');
        $this->load->model('user_model');
    }

    /*
     * 竞彩活动 - 首页
     * @date:2016-05-11
     */
    public function index()
    {
        $id = $this->input->get('id', true);

        if(!empty($id))
        {
            $matchInfo = $this->jcmatch_model->getActivityDetail($id, $activity_id = '3');
        }

        if(empty($matchInfo))
        {
            $matchInfo = $this->jcmatch_model->getLastActivity($activity_id = '3');
        }
        
        var_dump($matchInfo);
    }

    /*
     * 竞彩活动 - 预约购买
     * @date:2016-05-11
     */
    public function prePay()
    {
        $postData = $this->input->get(null, true);

        $postData['uid'] = $this->uid;
        if(empty($postData['uid']))
        {
            $result = array(
                'status' => '300',
                'msg' => '用户登录信息过期',
                'data' => ''
            );
            echo json_encode($result);
            exit();
        }

        $uinfo = $this->user_model->getUserInfo($postData['uid']);
        if(empty($uinfo))
        {
            $result = array(
                'status' => '300',
                'msg' => '用户登录信息过期',
                'data' => ''
            );
            echo json_encode($result);
            exit();
        }

        $postData['userName'] = $uinfo['userName'];

        // 用户余额基本检查
        if($uinfo['money'] < ParseUnit($postData['money']))
        {
            $result = array(
                'status' => '400',
                'msg' => '余额不足，请先充值',
                'data' => ''
            );
            echo json_encode($result);
            exit();
        }

        // 预约购买
        $payResult = $this->jcmatch_model->doPay($postData);





    }


    
}