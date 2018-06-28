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
        $channels = $this->slave->query($sql)->getAll();

        return $channels;
    }

    /**
     * 参    数：
     * 作    者：刁寿钧
     * 功    能：默认渠道ID 移动端默认渠道号 2
     * 修改日期：2015-07-09
     */
    public function defaultChannelId()
    {
        $defaultChannels = $this->config->item('defaultChannel');

        return $defaultChannels['ios'];
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

        $this->load->driver('cache', array('adapter' => 'redis'));
        $redis = $this->config->item('REDIS');
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

        $this->load->driver('cache', array('adapter' => 'redis'));
        $redis = $this->config->item('REDIS');
        $validChannels = unserialize($this->cache->get($redis['validChannels']));

        if ( ! is_array($validChannels))
        {
            return $defaultId;
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

    public function getChannelInfo($id)
    {
        $lastSql = "SELECT name, platform FROM cp_channel where id= ?";
        $channel = $this->slave->query($lastSql, array($id))->getRow();
        return $channel;
    }

    /**
     * 通过id查询
     * @param int $id
     * @return array
     */
    public function getById($id)
    {
        $lastSql = "SELECT * FROM cp_channel where id=" . $id;
        $channel = $this->slave->query($lastSql)->getRow();
        return $channel;
    }
}
