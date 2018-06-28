<?php

/**
 * 微信登录
 */

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Wechat extends MY_Controller 
{
    public function __construct() 
    {
        parent::__construct ();
        $this->load->library('WeixinLogin');
        $this->load->model('user_model');
    }

    // 微信登录 - 二维码回调确认
    public function callback()
    {
        $code = $this->input->get('code');

        if(!empty($code))
        {
            // 通过code获取access_token
            $tokenInfo = $this->weixinlogin->getAccessToken($code);

            if(!empty($tokenInfo['unionid']))
            {   
                // 检查是否已关联
                $wxInfo = $this->user_model->checkWxUnionid($tokenInfo['unionid']);

                if(!empty($wxInfo))
                {
                    $this->goLogin($wxInfo['uid']);
                }
                else
                {
                    $this->redirect('/weihu', 'location', 301);
                    // 跳转绑定手机号页
                    $this->goWxRegister($tokenInfo['unionid'], $tokenInfo['access_token'], $tokenInfo['openid']);
                }
            }
            else
            {
                $this->redirect('/');
            }
        }
        else
        {
            // 用户禁止授权 返回首页
            $this->redirect('/');
        }
    }

    // 登录
    public function goLogin($uid)
    {
        $userInfo = $this->user_model->getUserInfo($uid);

        if(isset($userInfo['userStatus']) && $userInfo['userStatus'] == '1')
        {
            // 注销
            $this->redirect('/wechat/logoff');
        }
        else
        {
            // 登录记录
            $uData = array(
                'uid'               =>  $uid, 
                'last_login_time'   =>  date('Y-m-d H:i:s'), 
                'visit_times'       =>  1
            );
            $this->user_model->SaveUser($uData);
   
            $loginRecord = array(
                'login_time'    =>  $uData['last_login_time'], 
                'uid'           =>  $uid, 
                'ip'            =>  UCIP, 
                'area'          =>  $this->tools->convertip(UCIP), 
                'reffer'        =>  REFE,
                'login_type'    =>  1,      // 登录类型 - 微信登录
            );
            $this->user_model->loginRecord($loginRecord);
            $sid = $this->calcSessionId($userInfo['passid'], $uid, $userInfo['uname'], $uData['last_login_time']);
            $this->SetCookie($userInfo['uname'], $userInfo['passid'], $uid, 0, $uData['last_login_time'], $sid);
            //消息入队
            $this->load->library('common_stomp_send');
            $this->common_stomp_send->login(array('uid' => $uid, 'last_login_time' => $userInfo['last_login_time']));

            $this->redirect('/');
        }
        exit;
    }

    // 主页展示页
    public function logoff()
    {
        $this->load->view('v1.1/elements/common/header');
        $this->load->view('v1.1/error/error_logoff');
        $this->load->view('v1.1/elements/common/footer_short');
    }

    // 跳转绑定手机号页
    public function goWxRegister($unionid, $access_token, $openid)
    {
        $codeStr = time();
        $sign = $this->getSign($unionid, $codeStr); 

        $url = "wechat/bindPhone?codeStr=" . $codeStr;
        $url .= "&unionid=" . $unionid;
        $url .= "&sign=" . $sign;
        $url .= "&token1=" . $access_token;
        $url .= "&token2=" . $openid;

        $this->redirect('/' . $url);
        exit;
    }

    // 加密
    public function getSign($unionid, $codeStr)
    {
        return md5(__class__ . $unionid . $codeStr);
    }

    // 微信账户绑定手机号
    public function bindPhone()
    {
        if($this->uid)
        {
            $this->redirect('/');
        }

        $getData = $this->input->get();

        if(empty($getData) || $this->getSign($getData['unionid'], $getData['codeStr']) != $getData['sign'])
        {
            $this->redirect('/error');
        }

        $getData['headTitle'] = "绑定手机号";
        $this->displayShort('wechat/bindPhone', $getData, 'v1.1');
    }

    // 微信绑定或者注册
    public function wechatRegister()
    {
        $result = array(
            'code'      =>  0,
            'needfrsh'  =>  0,
            'msg'       =>  '注册失败',
        );

        $captcha = $this->input->post('phoneCaptcha', true);
        $phone = $this->input->post('wechatPhone', true);
        $unionid = $this->input->post('unionid');
        $codeStr = $this->input->post('codeStr');
        $sign = $this->input->post('sign');
        $token1 = $this->input->post('token1', true);
        $token2 = $this->input->post('token2', true);
        $activity_id = $this->input->post('activity_id', true);
        $activity_id = empty( $activity_id ) ? 0 : $activity_id;

        // 参数校验
        if(empty($unionid) || $this->getSign($unionid, $codeStr) != $sign)
        {
            $this->ajaxResult($result);
        }

        // 验证码校验
        $capthaRes = $this->checkCaptcha($captcha, $phone, 'registerCaptcha');
        if($capthaRes)
        {
            $result['code'] = 0;
            $result['msg'] = '验证码错误';
            $result['needfrsh'] = 0;
            if ($capthaRes === 2) 
            {
                $result['needfrsh'] = 1;
            }
            $this->ajaxResult($result);
        }

        // 检查是否已关联
        $userInfo = $this->user_model->checkWxUnionid($unionid);

        if(!empty($userInfo))
        {
            $result = array(
                'code'      =>  0,
                'needfrsh'  =>  0,
                'msg'       =>  '该微信号已绑定',
            );
            $this->ajaxResult($result);
        }

        $userData = array(
            'unionid'           =>  $unionid,
            'phone'             =>  $phone,
            'passid'            =>  0,
            'pword'             =>  $this->createNonceStr(rand(6, 8)),
            'reg_reffer'        =>  REFE,
            'activity_id'       =>  $activity_id,
            'reg_ip'            =>  UCIP,
            'last_login_time'   =>  date('Y-m-d H:i:s'),
            'channel'           =>  $this->getChannelId()
        );
        // 调用注册模块创建用户信息
        $registerStatus = $this->user_model->wxRegister($userData);

        if($registerStatus['status'])
        {
            $uid = $registerStatus['data']['uid'];
            $uname = $registerStatus['data']['uname'];

            // 登录记录
            $loginRecord = array(
                'login_time'    =>  $userData['last_login_time'], 
                'uid'           =>  $uid, 
                'ip'            =>  UCIP, 
                'area'          =>  $this->tools->convertip(UCIP), 
                'reffer'        =>  REFE,
                'login_type'    =>  1,      // 登录类型 - 微信登录
            );
            $this->user_model->loginRecord($loginRecord);
            
            $sid = $this->calcSessionId($userData['passid'], $uid, $uname, $userData['last_login_time']);
            $this->SetCookie($uname, $userData['passid'], $uid, 0, $userData['last_login_time'], $sid);
            
            if ($this->input->cookie('cpk')) 
            {
                $this->user_model->saveCpkUser($this->input->cookie('cpk'), $uid);
            }

            if($registerStatus['data']['regType'] == '1')
            {
                $this->load->library('libredpack');
                $this->libredpack->hongbao166('register', array('phone' => $phone, 'uid' => $uid, 'platformId' => 0, 'channel' => $userData['channel']));
                
                //联盟添加二级用户
                $rebateId = $this->input->cookie('rebateId');
                if($rebateId)
                {
                    $this->load->model('rebates_model');
                    $this->rebates_model->RegAddRebate($uid, $rebateId);
                }
                //拉新活动
                $this->load->model('activity_lx_model');
                $this->activity_lx_model->regAdd($uid, $phone);
            
                // 发送短信
                // $msgData = array(
                //     '#CODE#'    =>  $userData['pword']
                // );

                // $position = $this->config->item('POSITION');
                // $this->user_model->sendSms($uid, $msgData, 'wechat_register', null, '127.0.0.1', $position['wechat_register']);
            }

            $result = array(
                'code'      =>  $registerStatus['data']['regType'],
                'needfrsh'  =>  0,
                'msg'       =>  '绑定成功',
            );
            //消息入队
            $this->load->library('common_stomp_send');
            $this->common_stomp_send->login(array('uid' => $uid, 'last_login_time' => 0));
            
            if($registerStatus['data']['regType'] == '1' && $token1 && $token2) {
                //请求微信头像
                $wxuserInfo = $this->weixinlogin->getUserinfo($token1, $token2);
                if(isset($wxuserInfo['headimgurl']) && !empty($wxuserInfo['headimgurl'])) {
                    $file_path = dirname(BASEPATH) . '/cpiaoimg/headimg/';
                    $file_name = md5(time() . rand(1, 1000)) . '.jpeg';
                    $image = $this->tools->request($wxuserInfo['headimgurl']);
                    if($this->tools->recode == 200) {
                        file_put_contents($file_path . $file_name, $image);
                        $static_url = $this->config->item('img_url');
                        shuffle($static_url);
                        if (ENVIRONMENT === 'production') {
                            $img_url = 'https:' . $static_url[0] . 'cpiaoimg/headimg/' . $file_name;
                        } else {
                            $img_url = 'http:' . $static_url[0] . 'cpiaoimg/headimg/' . $file_name;
                        }
                        $this->user_model->uploadImg($img_url, $uid);
                    }
                }
            }
        }
        else
        {
            $result = array(
                'code'      =>  0,
                'needfrsh'  =>  0,
                'msg'       =>  '注册失败',
            );
        }
        $this->ajaxResult($result);
    }

    private function ajaxResult($result)
    {
        header('Content-type: application/json');
        die(json_encode($result));
    }

    public function createNonceStr($length = 6) 
    {
        $chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
        $str = "";
        for ($i = 0; $i < $length; $i++) 
        {
            $str .= substr($chars, mt_rand(0, strlen($chars) - 1), 1);
        }
        return $str;
    }
}