<?php

/**
 * Copyright (c) 2014,上海二三四五网络科技股份有限公司
 * 摘    要：统计
 * 作    者：wangl@2345.com
 * 修改日期：2014.11.07
 */
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Get_count extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();
        if (php_sapi_name() != 'cli')
        {
            exit("no access");
        }
        $this->load->model("Model_count");
    }

    /**
     * 参    数：无
     * 作    者：wangl
     * 功    能：统计昨日的用户概况数据
     * 修改日期：2014.11.05
     */
    public function count_yestoday($day = '')
    {
        if($day == '')
        {
            $t_start = strtotime(date("Y-m-d", time()));
        }
        else 
        {
            $t_start = strtotime($day);
        }
        
        $id = $this->Model_count->count_yestoday($t_start);
        $id2 = $this->Model_count->count_yestoday($t_start, 86400*30, 30);
        if ($id > 0 && $id2 > 0)
        {
            echo "OK";
        }
        else
        {
            //something log
            echo "NO";
        }
        $this->clearCache();
    }
    /**
    * 参    数：无
    * 作    者：wangl
    * 功    能：清除缓存
    * 修改日期：2014.11.05
    */
    public function clearCache()
    {
        $rediskeys = $this->config->item('rediskeys');
        $this->load->driver('cache', array('adapter' => 'redis'));
        $this->cache->redis->delete(array($rediskeys['count_yestoday'], $rediskeys['count_thirty']));
    }

}
