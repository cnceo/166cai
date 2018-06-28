<?php

/**
 * 移动端 用户单设备登录 公共类
 */
class AppLogin 
{
	public function __construct($config = array())
	{
		$this->CI = &get_instance();
        $this->CI->load->model('app/model_login', 'app_model_login');	
	}

    // 登录、注册更新登录态
    public function recordUserLogin($params)
    {
        $login = array(
            'uid'               =>  $params['uid'],
            'platform'          =>  $params['platform'],
            'version'           =>  $params['version'] ? $params['version'] : '',
            'idfa'              =>  $params['idfa'] ? strtoupper($params['idfa']) : '',
            'last_login_time'   =>  date('Y-m-d H:i:s'),
            'token'             =>  '',
            'auth'              =>  '',
            'cstate'            =>  '1',
        );
        return $this->CI->app_model_login->recordUserLogin($login);
    }

    // 注销登录态
    public function logOutLogin($params)
    {
        $login = array(
            'uid'       =>  $params['uid'],
            'platform'  =>  $params['platform'],
            'idfa'      =>  $params['idfa'] ? strtoupper($params['idfa']) : '',
        );
        return $this->CI->app_model_login->recordUserLogin($login);
    }
	
    // 获取用户态
    public function getUserLogin($uid)
    {
        return $this->CI->app_model_login->getUserLogin($uid);
    }

    // 检查用户登录设备信息
    public function checkUserLogin($params)
    {
        $result = array(
            'status'    =>  TRUE,
            'msg'       =>  '验证成功',
            'data'      =>  ''
        );

        $info = $this->getUserLogin($params['uid']);
        if(!empty($info['idfa']) && (strtoupper($info['idfa']) != strtoupper($params['idfa']) || $info['platform'] != $params['platform']))
        {
            $result = array(
                'status'    =>  FALSE,
                'msg'       =>  '验证失败',
                'data'      =>  '您的账号于' . $info['last_login_time'] . '在其他设备上登录，如果不是你本人的操作，请您确认密码是否已经泄露，您可以在设置里修改登录密码。'
            );
        }
        return $result;
    }
}