<?php

/**
 * Copyright (c) 2014,上海二三四五网络科技股份有限公司
 * 摘    要：OA帐号体系登录
 * 作    者：wangl@2345.com
 * 修改日期：2014.11.06
 */
class oalogin extends CI_Controller
{
    private $oa_url = 'http://oa.2345.cn/api/login.php';
    private $oa_refer = 'http://www.oa.ruichuang.net/yigebucunzaidemulu/api.php';
    
    /**
     * 参    数：空
     * 作    者：wangl
     * 功    能：通过OA登录后台
     * 修改日期：2014.11.05
     */
    public function api($do)
    {
        if (strtolower($do) == 'oalogin.php')
        {
            $this->load->helper(array(
                "fn_curl",
                "fn_common"
            ));
            $query_string = $_SERVER['QUERY_STRING'];
            parse_str($query_string, $output);
            $options = array(
                CURLOPT_REFERER => $this->oa_refer
            );
            $result = curl_http_post($this->oa_url, array(
                "uid" => $output['uid'],
                "username" => $output['username'],
                "mk" => $output['mk'],
                "ip" => get_client_ip()
            ), $options);
            if (empty($result))
            {
                die('Access denied');
            }
            $row = unserialize($result);
            if (!is_array($row))
            {
                die("<script type='text/javascript'>alert('登录链接已过期,请重新登录！');location.href='https://oa.2345.cn/';</script>");
            }
            setcookie('d_uid', $output['uid'], 0, "/");
            setcookie('d_mk', $output['mk'], 0, "/");
            $username = mb_convert_encoding($output['username'], "UTF-8", "GBK");
            //登录判断结束，以下为该系统登录所需设置。
             
            $this->load->library(array(
                'session',
                'encrypt'
            ));
            $this->load->model('Model_capacity', 'capacity');
            $this->load->model('Model_syslog');
            $user_capacity = $this->capacity->get_capacity($username, 0, true);
            if (!empty($user_capacity))
            {
                //$this->session->set_userdata('cp_session_cap', $this->encrypt->encode($user_capacity['capacity'], MANAGER_ENCODE_KEY));
            }
            else
            {
                exit("请通知管理员开通权限");
            }
            $this->session->set_userdata('cp_session_man', $this->encrypt->encode($username, MANAGER_ENCODE_KEY));
            $this->session->set_userdata('cp_session_user', $username);
            $this->Model_syslog->add_syslog(11, "成功登录", $username);
            header("location: /backend/");
            exit;
        }
        elseif (strtolower($do) == 'oalogout.php')
        {
            $this->load->library('session');
            $this->session->unset_userdata('cp_session_man');
            //$this->session->unset_userdata('cp_session_cap');
            $this->session->unset_userdata('cp_session_user');
            setcookie('d_mk', NULL, time() - 3600, "/");
            setcookie('d_uid', NULL, time() - 3600, "/");
            exit;
        }
        else
        {
            echo "Access denied";
            exit;
        }
    }
    
}
