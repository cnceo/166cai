<?php

/**
 * 网站公告 model
 * @Author liusijia
 */
class Notice_Model extends MY_Model 
{

    public $tbname;

    public function __construct() 
    {
        $this->tbname = "cp_notice";
    }

    /**
     * 公告列表
     * @param type $condition
     * @param type $pagesize
     * @param type $page
     * @return type 
     * @Author liusijia
     */
    public function noticeList($condition, $limit, $offset, $cache = false) 
    {
    	$ukey = '';
    	if($cache)
    	{
    		$uinfo = array();
	    	$REDIS = $this->config->item('REDIS');
	    	$ukey = $REDIS['NOTICE_RECORDS'].md5("$condition$limit$offset");
			$this->load->driver('cache', array('adapter' => 'redis'));
			$uinfo = unserialize($this->cache->redis->get($ukey));
    	}
    	if(empty($uinfo))
    	{
	    	$uinfo = $this->refreshCache($condition, $limit, $offset, $cache, $ukey);
    	}
    	return $uinfo;
    }
    
    public function refreshCache($condition, $limit, $offset, $cache, $ukey)
    {
    	$sql = "select id, title, content, url, username, addTime, status, weight, category, isTop from " . $this->tbname;
        if ($condition) 
        {
            $sql .= " where ";
            if (is_array($condition)) 
            {
                $len = count($condition);
                $i = 0;
                foreach ($condition as $k => $v) 
                {
                    $sql .= $k . '=' . $v;
                    $i = $i + 1;
                    if ($i < $len) 
                    {
                        $sql .= " and ";
                    }
                }
            } 
            else if (is_string($condition)) 
            {
                $sql .= $condition;
            }
        }
        $sql .= " order by isTop desc,addTime desc limit " . (empty($limit) ? '0' : ($limit - 1) * $offset) . ',' . $offset;
        $uinfo = $this->slave->query($sql)->getAll();
        if($cache)
    	{
	        $this->cache->redis->save($ukey, serialize($uinfo), 300);
    	}
    	return $uinfo;
    }
    /**
     * 根据条件查询公告数量
     * @param type $condition
     * @return type 
     * @Author liusijia
     */
    public function noticeCount($condition) 
    {
        $this->slave->where($condition);
        $this->slave->from($this->tbname);
        return $this->slave->count_all_results();
    }

    /**
     * 通过id值获取单条公告数据
     * @param type $id
     * @return type 
     * @Author liusijia
     */
    public function getInfoById($id) 
    {
        $data = $this->slave->get_where($this->tbname, array('id' => $id))->row_array();
        return $data[0];
    }

}
