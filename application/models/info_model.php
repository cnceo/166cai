<?php

/**
 * Copyright (c) 2015,上海二三四五网络科技有限公司.
 * 摘    要:
 * 作    者: 刁寿钧
 * 修改日期: 2016/3/21
 * 修改时间: 20:16.
 */
class Info_Model extends MY_Model
{
    /**
     * 公告列表.
     *
     * @param type $condition
     * @param type $pagesize
     * @param type $page
     *
     * @return type
     * @Author diaosj
     */
    public function noticeList($condition, $limit, $offset, $cache = false)
    {
        $ukey = '';
        if ($cache) {
            $REDIS = $this->config->item('REDIS');
            $ukey = $REDIS['NOTICE_RECORDS'].md5("$condition$limit$offset");
            $this->load->driver('cache', array('adapter' => 'redis'));
            $uinfo = unserialize($this->cache->redis->get($ukey));
        }
        if (empty($uinfo)) {
            $uinfo = $this->refreshCache($condition, $limit, $offset, $cache, $ukey);
        }

        return $uinfo;
    }

    public function refreshCache($condition, $limit, $offset, $cache, $ukey)
    {
        $sql = 'select * from cp_info';
        if ($condition) {
            $sql .= ' where ';
            if (is_array($condition)) {
                $len = count($condition);
                $i = 0;
                foreach ($condition as $k => $v) {
                    $sql .= $k.'='.$v;
                    $i = $i + 1;
                    if ($i < $len) {
                        $sql .= ' and ';
                    }
                }
            } elseif (is_string($condition)) {
                $sql .= $condition;
            }
        }
        $sql .= ' order by is_top desc,created desc limit '.(empty($limit) ? '0' : ($limit - 1) * $offset).','.$offset;
        $uinfo = $this->slave->query($sql)->getAll();
        if ($cache) {
            $this->cache->redis->save($ukey, serialize($uinfo), 300);
        }

        return $uinfo;
    }

    /**
     * 根据条件查询公告数量.
     *
     * @param type $condition
     *
     * @return type
     * @Author diaosj
     */
    public function noticeCount($condition)
    {
        $this->slave->where($condition);
        $this->slave->from('cp_info');

        return $this->slave->count_all_results();
    }

    /**
     * 通过id值获取单条公告数据.
     *
     * @param type $id
     *
     * @return type
     * @Author diaosj
     */
    public function getInfoById($id)
    {
        $sql = 'select id, title, created, show_time, content, category_id, submitter_id from cp_info where id = ?';
        $data = $this->slave->query($sql, array($id))->getRow();
        $sqll = "select id, title from cp_info where category_id = '{$data['category_id']}' and created < '{$data['created']}' and is_show = 1 order by created desc limit 1";
        $sqlr = "select id, title from cp_info where category_id = '{$data['category_id']}' and created > '{$data['created']}' and is_show = 1 order by created limit 1";
        $datal = $this->slave->query($sqll)->getRow();
        $datar = $this->slave->query($sqlr)->getRow();

        return array(
            'l' => $datal,
            'n' => $data,
            'r' => $datar,
        );
    }

    /**
     * 列表页.
     */
    public function getListByCategory($category, $start, $offset)
    {
        $sql = 'select count(*) as num from cp_info where platform & 1 and category_id = ? and is_show = 1';
        $nres = $this->slave->query($sql, array($category))->getRow();
        $sql = "select id, title, created, category_id, show_time from cp_info where platform & 1 and category_id = ? and is_show = 1 order by show_time desc limit {$start}, {$offset}";
        $res = $this->slave->query($sql, array($category))->getAll();

        return array(
            'num' => $nres['num'],
            'data' => $res,
        );
    }

    public function getPagesByCategory($category)
    {
        $sql = 'select id from cp_info where platform & 1 and category_id = ? and is_show = 1';
        $res = $this->slave->query($sql, array($category))->getCol();

        return $res;
    }

    /**
     * 返回总数
     * @return int
     */
    public function countOldPics()
    {
        $end     = date('Y-m-d H:i:s', strtotime('-7 day'));
        $start   = date('Y-m-d H:i:s', strtotime('-8 day'));
        $this->slave->select('id');
        $this->slave->where('is_show', 0);
        $this->slave->where('submitter', '抓取');
        $this->slave->where('created <=', $end);
        $this->slave->where('created >=', $start);
        $res = $this->slave->count_all_results('cp_info');

        return $res;
    }

    /**
     * 一次拉5000个数据.
     *
     * @param int $limit
     * @param int $offet
     *
     * @return array
     */
    public function getOldPics($limit, $offet)
    {
        $end     = date('Y-m-d H:i:s', strtotime('-7 day'));
        $start   = date('Y-m-d H:i:s', strtotime('-8 day'));
        $this->slave->select('content');
        $this->slave->where('is_show', 0);
        $this->slave->where('submitter', '抓取');
        $this->slave->where('created <=', $end);
        $this->slave->where('created >=', $start);
        $this->slave->limit($limit, $offet);
        $res = $this->slave->get('cp_info')->getAll();

        return $res;
    }
}
