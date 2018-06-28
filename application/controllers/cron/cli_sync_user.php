<?php

class Cli_Sync_User extends MY_Controller {

	public function __construct() 
    {
		parent::__construct();
		$this->load->model('syncuser_model');
	}

	public function index()
	{
        $info = $this->syncuser_model->getSynflagUser();
        while (!empty($info)) 
        {
            foreach ($info as $k => $items) 
            {
                $data = array();
                $uinfo = $this->getUserInfo($items['passid']);

                if(!empty($uinfo))
                {
                    // 组装数据
                    $data['uid'] = $items['uid'];
                    $data['passid'] = $items['passid'];
                    $data['username'] = iconv('GBK', 'UTF-8', $uinfo['username']);
                    $data['m_uid'] = $uinfo['m_uid'];
                    $data['gid'] = $uinfo['gid'];
                    $data['reg_ip'] = $uinfo['reg_ip'];
                    $data['reg_time'] = $uinfo['reg_time'];
                    $data['login_ip'] = $uinfo['login_ip'];
                    $data['login_time'] = $uinfo['login_time'];
                    $data['name'] = iconv('GBK', 'UTF-8', $uinfo['name']);
                    $data['gender'] = $uinfo['gender'];
                    $data['bday'] = $uinfo['bday'];
                    $data['qq'] = $uinfo['qq'];
                    $data['area'] = $uinfo['area'];
                    $data['email'] = $uinfo['email'];
                    $data['email_status'] = $uinfo['email_status'];
                    $data['phone'] = $uinfo['phone'];
                    $data['phone_redundancy'] = $uinfo['phone_redundancy'];
                    // var_dump($data);die;
                    $this->syncuser_model->insertUserTemp($data);
                    
                } 
                $this->syncuser_model->updateSynflagUser($items['uid']);            
            }
            $info = $this->syncuser_model->getSynflagUser();
        }
	}

    // 获取用户中心个人信息
    public function getUserInfo($passid)
    {
        $postData = array(
            'uid' => $passid
        );
        // 用户中心接口切换
        if(ENVIRONMENT === 'production')
        {
            $info_url = 'http://login.2345.com/api/userinfo';
        }
        else
        {
            $info_url = 'http://183.136.203.154/api/userinfo';
            $postData['HOST'] = 'login.2345.com';
        }
        $redata = $this->tools->request($info_url, $postData);
        $redata = unserialize($redata);
        return $redata;
    }

    // 同步手机号、邮箱至原表
    public function syncUserInfo()
    {
        $info = $this->syncuser_model->getAlreadyUserInfo();

        while(!empty($info))
        {
            foreach ($info as $k => $items) 
            {
                // $items['passid'] = '400000714';
                $uinfo = $this->getUserInfo($items['passid']);
                if(!empty($uinfo['email']) || !empty($uinfo['phone']))
                {
                    $data = array();
                    $data['uid'] = $items['uid'];
                    $data['passid'] = $items['passid'];
                    $data['email'] = $uinfo['email'];
                    $data['phone'] = $uinfo['phone'];
                    $this->syncuser_model->updateUserInfo($data);
                }
                $this->syncuser_model->freshUserCache($items['uid']);
                $this->syncuser_model->updateSynflagUser2($items['uid']);
            }
            $info = $this->syncuser_model->getAlreadyUserInfo();
        }
    }

}