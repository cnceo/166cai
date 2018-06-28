<?php

/**
 * Copyright (c) 2015,上海二三四五网络科技有限公司.
 * 摘    要: 渠道Model
 * 作    者: 刁寿钧
 * 修改日期: 2015/7/9
 * 修改时间: 16:43
 */

class Channel_Model extends MY_Model
{

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * 参    数：
     * 作    者：刁寿钧
     * 功    能：取出所有渠道信息
     * 修改日期：2015-07-09
     */
    public function fetchAllChannels()
    {
        $sql = "SELECT id, name FROM cp_channel";
        $channels = $this->db->query($sql)->getAll();

        return $channels;
    }

    /**
     * 参    数：
     * 作    者：刁寿钧
     * 功    能：默认渠道ID
     * 修改日期：2015-07-09
     */
    public function defaultChannelId()
    {
        $defaultChannels = $this->config->item('defaultChannel');

        return $defaultChannels['web'];
    }

    /**
     * 参    数：id
     * 作    者：刁寿钧
     * 功    能：验证是否合法渠道ID或参数
     * 修改日期：2015-07-09
     */
    public function isValidChannelId($id)
    {
        if (empty($id))
        {
            return FALSE;
        }

        $this->CI = & get_instance();
        $this->load->driver('cache', array('adapter' => 'redis'));
        $redis = $this->CI->config->item('REDIS');
        $validChannels = unserialize($this->cache->get($redis['validChannels']));

        if ( ! is_array($validChannels))
        {
            return FALSE;
        }

        //之前是用channelId判断，后来说App端只能传渠道参数（即渠道名），所以修改数据结构
        //Redis中存的是channelId=>channelName的哈希，找得到key或value都可以算合法……
        return in_array($id, $validChannels) OR array_key_exists($id, $validChannels);
    }

    /**
     * 参    数：id
     * 作    者：刁寿钧
     * 功    能：验证是否合法渠道ID或参数
     *           合法则返回对应ID，不合法则返回默认ID
     * 修改日期：2015-07-27
     */
    public function getValidChannelId($id)
    {
        $defaultId = $this->defaultChannelId();

        if (empty($id))
        {
            return $defaultId;
        }

        $this->CI = & get_instance();
        $this->load->driver('cache', array('adapter' => 'redis'));
        $redis = $this->CI->config->item('REDIS');
        $validChannels = unserialize($this->cache->get($redis['validChannels']));

        if ( ! is_array($validChannels))
        {
            return $defaultId;
        }

        //之前是用channelId判断，后来说App端只能传渠道参数（即渠道名），所以修改数据结构
        //Redis中存的是channelId=>channelName的哈希，找得到key或value都可以算合法……
        if (array_key_exists($id, $validChannels))
        {
            return $id;
        }

        if (in_array($id, $validChannels))
        {
            $nameToId = array_flip($validChannels);
            return $nameToId[$id];
        }

        return $defaultId;
    }

    /**
     * 参    数：name
     * 作    者：刁寿钧
     * 功    能：创建指定名称的渠道
     * 修改日期：2015-07-09
     */
    public function createChannel($name)
    {
        $sql = "INSERT cp_channel (name, created) VALUES (?, NOW())";
        $success = $this->db->query($sql, $name);
        if ($success)
        {
            $this->CI = & get_instance();
            $this->load->driver('cache', array('adapter' => 'redis'));
            $redis = $this->CI->config->item('REDIS');
            $lastSql = "SELECT id, name FROM cp_channel";
            $channels = $this->db->query($lastSql)->getAll();
            $validChannels = array();
            foreach ($channels as $ch)
            {
                $validChannels[$ch['id']] = $ch['name'];
            }
            $this->cache->save($redis["validChannels"], serialize($validChannels), 0);
        }

        return $success;
    }

    // 渠道包信息缓存
    public function getChannelInfo($channelId)
    {
        $REDIS = $this->config->item('REDIS');
        $ukey = "{$REDIS['CHANNEL_INFO']}$channelId";
        $this->load->driver('cache', array('adapter' => 'redis'));
        $info = $this->cache->redis->hGetAll($ukey);
        if(empty($info))
        {
            $info = $this->freshChannelInfo($channelId);
        }
        return $info;
    }

    public function freshChannelInfo($channelId)
    {
        $REDIS = $this->config->item('REDIS');
        $ukey = "{$REDIS['CHANNEL_INFO']}$channelId";
        $this->load->driver('cache', array('adapter' => 'redis'));
        $sql = "SELECT c.id, c.name, c.package, c.platform, p.name AS packageName FROM cp_channel AS c LEFT JOIN cp_channel_package AS p ON c.package = p.id WHERE c.id = ?";
        $info = $this->db->query($sql, array($channelId))->getRow();     
        $this->cache->redis->hMSet($ukey, $info);
        return $info;
    }
}
