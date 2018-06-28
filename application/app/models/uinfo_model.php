<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * 彩票O2O用户中心 模型层
 */
class Uinfo_Model extends MY_Model 
{

    public function __construct()
    {
        parent::__construct();
        $this->load->model('user_model');
    }

    /*
     * APP 手机号注册接口
     * @返回参数说明:
     * @status：100 - 系统异常
     * @status：200 - 注册成功
     * @status：300 - 缺少必要注册参数
     * @status：400 - 手机号已被注册
     * @status：500 - 注册失败
     * @date:2016-1-19
     */
    public function userRegister($userData)
    {

        // 检查必要参数
        if(!empty($userData))
        {
            foreach ($userData as $val) 
            {
                if(empty($val))
                {
                    $registerStatus = array(
                        'status' => '300',
                        'msg' => '缺少必要注册参数',
                        'data' => $userData
                    );
                    return $registerStatus;
                }
            }
        }
        else
        {
            $registerStatus = array(
                'status' => '300',
                'msg' => '缺少必要注册参数',
                'data' => $userData
            );
            return $registerStatus;
        }

        // 事务开始
        $this->db->trans_start();

        try 
        {
            

            // 查询手机号是否注册
            $uinfo = $this->getUserByPhone($userData['phone']);

            if(!empty($uinfo['phone']))
            {
                // 手机号已被注册
                $this->db->trans_rollback();

                $registerStatus = array(
                    'status' => '400',
                    'msg' => '该手机号已被注册',
                    'data' => $userData
                );
                return $registerStatus;
            }

            // 组装cp_user_register数据
            $registerData = array(
                'phone' => $userData['phone']
            );

            // 生成彩票uid
            $registerFields = array_keys($registerData);
            $registerSql = "insert cp_user_register(" . implode(',', $registerFields) . ", created)
            values(". implode(',', array_map(array($this, 'maps'), $registerFields)) .", now())";
            $this->db->query($registerSql, $registerData);

            $uid = $this->db->query("select last_insert_id()")->getOne();

            if($uid > 0)
            {
                // 组装cp_user数据
                $uniqid = substr(uniqid(), 0, 9);
                $pwd = md5($userData['password']) . $uniqid;
                $cpUserData = array(
                    'uid' => $uid,
                    'passid' => '',
                    'uname' => 'user_' . date('ymdHis') . $uid,  // 默认用户名
                    'pword' => strCode($pwd, 'ENCODE'),
                    'salt' => $uniqid,
                    'email' => '',
                    'reg_type' => $userData['reg_type'] ? $userData['reg_type'] : 2,
                    'platform' => $userData['platform'],
                    'reg_reffer' => $userData['version'],
                    'channel' => $userData['channel'],
                    'reg_ip' => $userData['ip'],
                    'last_login_time' => date('Y-m-d H:i:s'),
                    'visit_times' => 1,
                    'last_login_channel' => $userData['channel'],
                );

                $userUpd = array('last_login_time', 'visit_times', 'pword', 'salt', 'email', 'passid', 'nick_name', 'uname', 'activity_id', 'last_login_channel');
                $userFields = array_keys($cpUserData);
                $userSql = "insert cp_user(" . implode(',', $userFields) . ", created)values(" . 
                implode(',', array_map(array($this, 'maps'), $userFields)) .  ", now())" . $this->onduplicate($userFields, $userUpd);
                $userStatus = $this->db->query($userSql, $cpUserData);

                // 组装cp_user_info数据
                $cpUserInfoData = array(
                    'uid' => $uid,
                    'phone' => $userData['phone'],
                    'nick_name' => $cpUserData['uname'],
                    'nick_name_modify_time' => '0000-00-00 00:00:00'
                );

                $infoUpd = array('real_name', 'nick_name', 'gender', 'phone', 'qq', 'id_card', 'bank_id', 'province', 'city', 'bank_province', 'bank_city', 'pay_pwd', 'bind_id_card_time');
                $infoFields = array_keys($cpUserInfoData);
                $userInfoSql = "insert cp_user_info(" . implode(',', $infoFields) . " ) values(" . implode(',', array_map(array($this, 'maps'), $infoFields)) .  " )" . $this->onduplicate($infoFields, $infoUpd);
                $userInfoStatus = $this->db->query($userInfoSql, $cpUserInfoData);

                if($userStatus && $userInfoStatus)
                {
                    $this->db->trans_complete();

                    // 刷新缓存
                    $this->user_model->freshUserInfo($uid);

                    $registerStatus = array(
                        'status' => '200',
                        'msg' => '手机号注册成功',
                        'data' => array(
                            'uid' => $uid,
                            'phone' => $userData['phone'],
                            'password' => $userData['password'],
                            'ip' => $userData['ip'],
                            'platform' => $userData['platform'],
                            'version' => $userData['version'],
                            'channel' => $userData['channel']
                        )
                    );

					//消息入队
                    apiRequest('common_stomp_send', 'login', array('uid' => $uid, 'last_login_time' => 0));
                    
                    return $registerStatus;
                }
                else
                {
                    // 手机号注册失败
                    $this->db->trans_rollback();

                    $registerStatus = array(
                        'status' => '500',
                        'msg' => '手机号注册失败',
                        'data' => $userData
                    );
                    return $registerStatus;
                }
            }
            else
            {
                // 手机号已被注册
                $this->db->trans_rollback();

                $registerStatus = array(
                    'status' => '400',
                    'msg' => '该手机号已被注册',
                    'data' => $userData
                );
                return $registerStatus;
            }
        }
        catch (Exception $e)
        {
            // 系统异常
            $this->db->trans_rollback();

            $registerStatus = array(
                'status' => '100',
                'msg' => '系统异常',
                'data' => $userData
            );
            return $registerStatus;
        }
    }

    /*
     * APP 手机号登录接口
     * @返回参数说明:
     * @status：100 - 系统异常
     * @status：200 - 注册成功
     * @status：300 - 缺少必要注册参数
     * @status：400 - 手机号已被注册
     * @status：500 - 注册失败
     * @date:2016-1-19
     */
    public function userLogin($loginData)
    {
        // 检查必要参数
        if(!empty($loginData))
        {
            foreach ($loginData as $val) 
            {
                if(empty($val))
                {
                    $loginStatus = array(
                        'status' => '300',
                        'msg' => '缺少必要注册参数',
                        'data' => $loginData
                    );
                    return $loginStatus;
                }
            }
        }
        else
        {
            $loginStatus = array(
                'status' => '300',
                'msg' => '缺少必要注册参数',
                'data' => $loginData
            );
            return $loginStatus;
        }

        // 事务开始
        $this->db->trans_start();

        $registerInfo = $this->getUserByPhone($loginData['phone']);

        if(empty($registerInfo))
        {
            $this->db->trans_rollback();

            $loginStatus = array(
                'status' => '300',
                'msg' => '手机号或密码不正确',
                'data' => $loginData
            );
            return $loginStatus;
        }

        // 获取用户登录密码
        $pwdInfo = $this->getUserPwd($registerInfo['id']);

        if(empty($pwdInfo))
        {
            $this->db->trans_rollback();

            $loginStatus = array(
                'status' => '300',
                'msg' => '手机号或密码不正确',
                'data' => $loginData
            );
            return $loginStatus;
        }

        $uinfo = $this->user_model->getUserInfo($registerInfo['id']);

        // 用户是否注销
        if(isset($uinfo['userStatus']) && $uinfo['userStatus'] == '1')
        {
            $this->db->trans_rollback();

            $loginStatus = array(
                'status' => '300',
                'msg' => '手机号码不存在',
                'data' => $loginData
            );
            return $loginStatus;
        }

        if(empty($uinfo))
        {
            $this->db->trans_rollback();

            $loginStatus = array(
                'status' => '300',
                'msg' => '手机号或密码不正确',
                'data' => $loginData
            );
            return $loginStatus;
        }
        $newpword = md5($loginData['password']);
        if ($pwdInfo['salt'])
        {
            $newpword = $newpword . $pwdInfo['salt'];
            $newpword = strCode($newpword, 'ENCODE');
        }
        // 密码校验
        if( $newpword == $pwdInfo['pword'] )
        {
            $this->db->trans_complete();

            // 更新用户登录信息
            $this->user_model->saveUser(
                array(
                    'uid' => $registerInfo['id'], 
                    'last_login_time' => date('Y-m-d H:i:s'), 
                    'last_login_channel' => $loginData['last_login_channel'],
                    'visit_times' => 1
                )
            );
            
			//消息入队
            apiRequest('common_stomp_send', 'login', array('uid' => $registerInfo['id'], 'last_login_time' => $uinfo['last_login_time']));

            $uinfo['uid'] = $registerInfo['id'];

            $loginStatus = array(
                'status' => '200',
                'msg' => '登陆成功',
                'data' => $uinfo
            );
            return $loginStatus;
        }
        else
        {
            $this->db->trans_rollback();

            $loginStatus = array(
                'status' => '300',
                'msg' => '手机号或密码不正确',
                'data' => $loginData
            );
            return $loginStatus;
        }


    }

    /*
     * 根据手机号码查询用户信息
     * @date:2016-1-19
     */
    public function getUserByPhone($phone)
    {
        $sql = "select id, phone from cp_user_register where phone = ? for update";
        return $this->db->query($sql, array($phone))->getRow();
    }

    /*
     * 检查手机号码是否为空
     * @date:2016-1-19
     */
    public function checkRegister($phone)
    {
        $sql = "select id, phone from cp_user_register where phone = ?";
        $uinfo = $this->db->query($sql, array($phone))->getRow();

        if(empty($uinfo))
        {
            return TRUE;
        }
        else
        {
            return FALSE;
        }
    }

    /*
     * 检查手机号码对应账户是否注销
     * @date:2016-5-19
     */
    public function checkIsLocked($phone)
    {
        $sql = "SELECT r.id FROM cp_user_register r LEFT JOIN cp_user_info i ON r.id = i.uid WHERE r.phone = ? AND i.userStatus = 0";
        $uinfo = $this->db->query($sql, array($phone))->getRow();

        if(empty($uinfo))
        {
            return TRUE;
        }
        else
        {
            return FALSE;
        }
    }

    /*
     * 根据手机号码查询用户信息
     * @date:2016-1-19
     */
    public function getRegisterByPhone($phone)
    {
        $sql = "select id, phone from cp_user_register where phone = ?";
        return $this->db->query($sql, array($phone))->getRow();
    }

    /*
     * 更新手机号码
     * @返回参数说明:
     * @status：200 - 更新成功
     * @status：300 - 用户名或密码不正确
     * @status：400 - 该手机号已被注册
     * @status：500 - 更新失败
     * @date:2016-1-19
     */
    public function updateRegisterPhone($userData)
    {
        // 事务开始
        $this->db->trans_start();

        $registerInfo = $this->getUserByPhone($userData['old_phone']);

        if(empty($registerInfo))
        {
            $this->db->trans_rollback();

            $updateStatus = array(
                'status' => '300',
                'msg' => '用户名或密码不正确',
                'data' => ''
            );
            return $updateStatus;
        }

        if($registerInfo['id'] != $userData['uid'])
        {
            $this->db->trans_rollback();

            $updateStatus = array(
                'status' => '300',
                'msg' => '用户名或密码不正确',
                'data' => ''
            );
            return $updateStatus;
        }

        // 检查新手机号码是否可用
        $checkStatus = $this->checkRegister($userData['new_phone']);

        if(!$checkStatus)
        {
            $this->db->trans_rollback();

            $updateStatus = array(
                'status' => '400',
                'msg' => '该手机号已被注册',
                'data' => ''
            );
            return $updateStatus;
        }

        // 更新cp_user_register表
        $registerSql = "UPDATE cp_user_register SET phone = ? WHERE phone = ? AND id = ?";
        $registerRes = $this->db->query($registerSql, array($userData['new_phone'], $userData['old_phone'], $userData['uid']));
    
        // 同步cp_user_info
        $cpUserInfoData = array(
            'uid' => $userData['uid'],
            'phone' => $userData['new_phone']
        );

        $infoUpd = array('real_name', 'nick_name', 'gender', 'phone', 'qq', 'id_card', 'bank_id', 'province', 'city', 'bank_province', 
        'bank_city', 'pay_pwd', 'bind_id_card_time');
        
        $infoFields = array_keys($cpUserInfoData);
        $infoSql = "insert cp_user_info(" . implode(',', $infoFields) . " ) values(" . 
        implode(',', array_map(array($this, 'maps'), $infoFields)) .  " )" . $this->onduplicate($infoFields, $infoUpd);
        $infoRes = $this->db->query($infoSql, $cpUserInfoData);

        if($registerRes && $infoRes)
        {
            $this->db->trans_complete();

            // 刷新缓存
            $this->user_model->freshUserInfo($userData['uid']);

            $updateStatus = array(
                'status' => '200',
                'msg' => '手机号码更新成功',
                'data' => ''
            );
        }
        else
        {
            $this->db->trans_rollback();

            $updateStatus = array(
                'status' => '500',
                'msg' => '手机号码更新失败',
                'data' => ''
            );
        }
        $this->db->query("insert into cp_user_info_log (uid, type, cbefore, cafter, place) values (?, 4, ?, ?, 1)", array($userData['uid'], $userData['old_phone'], $userData['new_phone']));
        return $updateStatus;
    }

    // 检查登录密码
    public function getUserPwd($uid)
    {
        $sql = "select uid, pword, salt from cp_user where uid = ?";
        return $this->db->query($sql, array($uid))->getRow();
    }

    // 检查用户信息
    public function getUserInfoByPhone($phone)
    {
        $sql = "SELECT r.id, i.wx_unionid,i.userStatus FROM cp_user_register AS r LEFT JOIN cp_user_info AS i ON r.id = i.uid WHERE r.phone = ?";
        return $this->db->query($sql, array($phone))->getRow();
    }

    // 微信登录注册或绑定
    public function wxRegister($userData)
    {
        // 事务开始
        $this->db->trans_start();

        $uinfo = $this->getUserByPhone($userData['phone']);

        if(!empty($uinfo))
        {
            // 绑定
                $this->db->query("UPDATE cp_user_info SET wx_unionid = ? WHERE uid = ? AND wx_unionid = ''", array($userData['unionid'], $uinfo['id']));
                $uid = $uinfo['id'];
                $registerRes = $this->db->affected_rows();
            }
        else
        {
            // 组装cp_user_register数据
            $registerData = array(
                'phone' => $userData['phone']
            );

            // 生成彩票uid
            $registerFields = array_keys($registerData);
            $registerSql = "insert cp_user_register(" . implode(',', $registerFields) . ", created)
            values(". implode(',', array_map(array($this, 'maps'), $registerFields)) .", now())";
            $this->db->query($registerSql, $registerData);

            $uid = $this->db->insert_id();

            if($uid > 0)
            {
                // 组装cp_user数据
                $uniqid = substr(uniqid(), 0, 9);
                $pwd = md5($userData['password']) . $uniqid;
                $cpUserData = array(
                    'uid'               =>  $uid,
                    'passid'            =>  '',
                    'uname'             =>  'user_' . date('ymdHis') . $uid,
                    'pword'             =>  strCode($pwd, 'ENCODE'),
                    'salt'              =>  $uniqid,
                    'email'             =>  '',
                    'reg_type'          =>  3,      // 微信登录来源
                    'platform'          =>  $userData['platform'],
                    'reg_reffer'        =>  $userData['version'],
                    'channel'           =>  $userData['channel'],
                    'reg_ip'            =>  $userData['ip'],
                );

                $userUpd = array('last_login_time', 'visit_times', 'pword', 'salt', 'email', 'passid', 'nick_name', 'uname', 'activity_id', 'last_login_channel');
                $userFields = array_keys($cpUserData);
                $userSql = "insert cp_user(" . implode(',', $userFields) . ", created)values(" . 
                implode(',', array_map(array($this, 'maps'), $userFields)) .  ", now())" . $this->onduplicate($userFields, $userUpd);
                $userStatus = $this->db->query($userSql, $cpUserData);

                // 组装cp_user_info数据
                $cpUserInfoData = array(
                    'uid'                   =>  $uid,
                    'phone'                 =>  $userData['phone'],
                    'nick_name'             =>  $cpUserData['uname'],
                    'nick_name_modify_time' =>  '0000-00-00 00:00:00',
                    'wx_unionid'            =>  $userData['unionid'],
                );

                $infoUpd = array('real_name', 'nick_name', 'gender', 'phone', 'qq', 'id_card', 'bank_id', 'province', 'city', 'bank_province', 'bank_city', 'pay_pwd', 'bind_id_card_time');
                $infoFields = array_keys($cpUserInfoData);
                $userInfoSql = "insert cp_user_info(" . implode(',', $infoFields) . " ) values(" . implode(',', array_map(array($this, 'maps'), $infoFields)) .  " )" . $this->onduplicate($infoFields, $infoUpd);
                $userInfoStatus = $this->db->query($userInfoSql, $cpUserInfoData);

                $registerRes = $userStatus && $userInfoStatus;
            }
            else
            {
                $registerRes = FALSE;
            }    
        }

        if($registerRes)
        {
            $this->db->trans_complete();

            // 刷新缓存
            $this->user_model->freshUserInfo($uid);

            if(empty($uinfo))
            {
                // $msg = '您已成功加入166彩票，初始登录密码为' . $userData['password'] . '，可到安全中心修改，小六祝您购彩愉快！';
                // $this->load->library('tools');
                // $this->tools->sendSms(0, $userData['phone'], $msg, 9, UCIP, 187);
            }

            $registerStatus = array(
                'status'    =>  TRUE,
                'msg'       =>  '手机号注册成功',
                'data'      =>  array(
                    'uid'       =>  $uid,
                    'phone'     =>  $userData['phone'],
                    'password'  =>  $userData['password'],
                    'ip'        =>  $userData['ip'],
                    'platform'  =>  $userData['platform'],
                    'version'   =>  $userData['version'],
                    'channel'   =>  $userData['channel'],
                    'regType'   =>  !empty($uinfo) ? 2 : 1,
                )
            );
            
			//消息入队
            apiRequest('common_stomp_send', 'login', array('uid' => $uid, 'last_login_time' => 0));
        }
        else
        {
            $this->db->trans_rollback();
            
            $registerStatus = array(
                'status'    =>  FALSE,
                'msg'       =>  '微信登录注册或绑定失败',
                'data'      =>  ''
            );
        }
        return $registerStatus;
    }
}
