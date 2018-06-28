<?php

/*
 * 防刷限制类
 * @date:2016-08-11
 */

class Limit
{

    private $CI;
    public function __construct()
    {
        $this->CI = &get_instance();
        $this->CI->load->driver('cache', array('adapter' => 'redis'));
    }

    private $limitConfig = array(
        // 注册、修改密码 IP限制
        'ipRegister' => array(
            'expire'    =>  600,    // 过期时间（秒）
            'count'     =>  30,     // 超过3次   
        ),
        // 注册、修改密码 设备限制
        'deviceReg' => array(
            'expire'    =>  600,    // 过期时间（秒）
            'count'     =>  3,      // 超过3次   
        ),
        // 修改手机、提现 用户限制
        'userSms' => array(
            'expire'    =>  86400,  // 过期时间（秒）
            'count'     =>  10,     // 超过3次   
        ),
    );

    /*
    * 防刷限制类 - IP检查
    * @date:2016-08-11
    */
	public function checkIp($ctype = 'ipRegister', $ip = '')
	{
        // 获取配置
        $limitConfig = $this->limitConfig;

        $ip = $ip ? $ip : UCIP;
        $result = FALSE;

        switch ($ctype) 
        {
            case 'ipRegister':
                $count = $this->getCountByIp($ctype, $ip, $limitConfig[$ctype]['expire']);
                // 次数限制
                if($count > $limitConfig[$ctype]['count'])
                {
                    $result = TRUE;
                }
                break;
            
            default:
                # code...
                break;
        }
        return $result;
	}

    /*
    * 防刷限制类 - 获取指定场景的该IP统计
    * @date:2016-08-11
    */
    public function getCountByIp($ctype, $ip, $expire)
    {
        $REDIS = $this->CI->config->item('REDIS');
        $ukey = "{$REDIS['CHECK_LIMIT']}{$ctype}_{$ip}";

        // 统计
        $count = $this->CI->cache->redis->get($ukey);

        if(empty($count))
        {
            $this->CI->cache->redis->multi();
            $count = '1';
            // 设置过期时间
            $this->CI->cache->redis->save($ukey, $count, $expire);
            $this->CI->cache->redis->exec();
        }
        else
        {
            $count = $this->CI->cache->redis->increment($ukey);
        }
        return $count;
    }

    /*
    * 防刷限制类 - IP及设备检查
    * @date:2016-08-12
    */
    public function checkDevice($ctype = 'deviceReg', $deviceId = '', $ip = '')
    {
        // 获取配置
        $limitConfig = $this->limitConfig;

        $result = FALSE;

        switch ($ctype) 
        {
            case 'deviceReg':
                $count = $this->getCountByDevice($ctype, $deviceId, $ip, $limitConfig[$ctype]['expire']);
                // 次数限制
                if($count > $limitConfig[$ctype]['count'])
                {
                    $result = TRUE;
                }
                break;
            
            default:
                # code...
                break;
        }
        return $result;
    }

    /*
    * 防刷限制类 - 获取指定场景的该IP统计
    * @date:2016-08-11
    */
    public function getCountByDevice($ctype, $deviceId, $ip, $expire)
    {
        $REDIS = $this->CI->config->item('REDIS');
        $ukey = "{$REDIS['CHECK_LIMIT']}{$ctype}_{$deviceId}_{$ip}";

        // 统计
        $count = $this->CI->cache->redis->get($ukey);

        if(empty($count))
        {
            $this->CI->cache->redis->multi();
            $count = '1';
            // 设置过期时间
            $this->CI->cache->redis->save($ukey, $count, $expire);
            $this->CI->cache->redis->exec();
        }
        else
        {
            $count = $this->CI->cache->redis->increment($ukey);
        }
        return $count;
    }

    /*
    * 防刷限制类 - 用户检查
    * @date:2016-08-15
    */
    public function checkUser($ctype = 'userSms', $uid = '')
    {
        // 获取配置
        $limitConfig = $this->limitConfig;

        $result = FALSE;
        switch ($ctype) 
        {
            case 'userSms':
                $count = $this->getCountByUser($ctype, $uid, $limitConfig[$ctype]['expire']);
                // 次数限制
                if($count > $limitConfig[$ctype]['count'])
                {
                    $result = TRUE;
                }
                break;
            
            default:
                # code...
                break;
        }
        return $result;
    }

    /*
    * 防刷限制类 - 获取指定场景的该用户统计
    * @date:2016-08-11
    */
    public function getCountByUser($ctype, $uid, $expire)
    {
        $REDIS = $this->CI->config->item('REDIS');
        $ukey = "{$REDIS['CHECK_LIMIT']}{$ctype}_{$uid}";

        // 统计
        $count = $this->CI->cache->redis->get($ukey);

        if(empty($count))
        {
            $this->CI->cache->redis->multi();
            $count = '1';
            // 设置过期时间
            $this->CI->cache->redis->save($ukey, $count, $expire);
            $this->CI->cache->redis->exec();
        }
        else
        {
            $count = $this->CI->cache->redis->increment($ukey);
        }
        return $count;
    }

    /*
    * 防刷限制类 - 清除限制
    * @date:2016-08-11
    */
    public function deleteCheck($ctype, $deviceId = '', $ip = '')
    {
        $REDIS = $this->CI->config->item('REDIS');
        switch ($ctype) 
        {
            case 'ipRegister':
                $key = "{$REDIS['CHECK_LIMIT']}{$ctype}_{$ip}";
                break;

            case 'deviceReg':
                $key = "{$REDIS['CHECK_LIMIT']}{$ctype}_{$deviceId}_{$ip}";
                break;
            
            default:
                $key = '';
                break;
        }
        if($key)
        {
            $this->CI->cache->redis->delete($key);
        }
    }

}