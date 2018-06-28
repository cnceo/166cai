<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * 移动端 用户单设备登录 模型层
 */
class Model_Login extends MY_Model 
{

    public function __construct()
    {
        parent::__construct();
        $this->config->load('config');
        $this->redis = $this->config->item('REDIS');
    }

    // 获取用户登录态
    public function getUserLogin($uid)
    {
        $ukey = "{$this->redis['APP_LOGIN']}$uid";
        $this->load->driver('cache', array('adapter' => 'redis'));
        $uinfo = $this->cache->redis->hGetAll($ukey);
        if(empty($uinfo))
        {
            $uinfo = $this->refreshUserLogin($uid);
        }
        return $uinfo;
    }

    // 刷新用户登录态
    public function refreshUserLogin($uid)
    {
        $sql = "SELECT uid, platform, version, idfa, last_login_time, token, auth, cstate FROM cp_app_login WHERE uid = ?";
        $uinfo = $this->db->query($sql, array($uid))->getRow();
        $uinfo = $uinfo ? $uinfo : array();  

        $ukey = "{$this->redis['APP_LOGIN']}$uid";
        $this->load->driver('cache', array('adapter' => 'redis'));
        $this->cache->redis->hMSet($ukey, $uinfo);
        return $uinfo;
    }

    // 记录用户登录态
    public function recordUserLogin($info)
    {
        $upd = array('platform', 'version', 'idfa', 'last_login_time', 'token', 'auth', 'cstate');
        $fields = array_keys($info);
        $sql = "insert cp_app_login(" . implode(',', $fields) . ", created)values(" . 
        implode(',', array_map(array($this, 'maps'), $fields)) .  ", now())" . $this->onduplicate($fields, $upd);
        $this->db->query($sql, $info);
        // 刷新登录态
        return $this->refreshUserLogin($info['uid']);
    }

    // 注销登录态
    public function logOutLogin($info)
    {
        $sql = "UPDATE cp_app_login SET cstate = 0 WHERE uid = ? AND platform = ? AND idfa = ?";
        return $this->db->query($sql, array($info['uid'], $info['platform'], $info['idfa']));
    }
}
