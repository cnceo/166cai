<?php
class Main_Model extends MY_Model
{
	
	public function __construct() {
		$this->load->driver('cache', array('adapter' => 'redis'));
		$this->redis = $this->config->item('REDIS');
		parent::__construct();
	}
	
	public function noticeList($condition, $limit, $offset)
    {
    	$sql = "select id,title,content,url,username,addTime,status,weight,category,isTop from cp_notice";
        if ($condition) {
            $sql .= " where ";
            if (is_array($condition)) {
                $len = count($condition);
                $i = 0;
                foreach ($condition as $k => $v) {
                    $sql .= $k . '=' . $v;
                    $i = $i + 1;
                    if ($i < $len) {
                        $sql .= " and ";
                    }
                }
            } else if (is_string($condition)) {
                $sql .= $condition;
            }
        }
        $sql .= " order by isTop desc,addTime desc limit " . (empty($limit) ? '0' : ($limit - 1) * $offset) . ',' . $offset;
        return $this->slave->query($sql)->getAll();
    }
    
    public function getOrderWin()
    {
//     	$sql1 = "SELECT SUM(bonus) AS bonus, SUM(margin) AS margin FROM `cp_orders_win`";
//     	$countInfo = $this->slave->query($sql1)->getRow();
//     	$sql2 = "SELECT w.uid, w.orderId, w.money, w.bonus, w.margin, w.created, o.lid, u.nick_name FROM cp_orders_win AS w LEFT JOIN cp_orders AS o ON w.orderId = o.orderId LEFT JOIN cp_user_info AS u ON w.uid = u.uid WHERE w.margin >= 10000 ORDER BY w.id DESC LIMIT 300";
//     	$orderInfo = $this->slave->query($sql2)->getAll();
    	return array('count' => 0, 'orderInfo' => array());
    }
    
    public function getJingTai() {
        $data = array();
        $data['banner'] = $this->slave->query("select id, title, bgcolor, path, url, priority from cp_shouye_img
            where position = 'banner' and start_time <= NOW() and end_time > NOW()
            order by priority desc")->getAll();
        if (empty($data['banner'])) {
            $data['banner'] = $this->slave->query("select id, title, bgcolor, path, url, priority from cp_shouye_img
            where position = 'banner'
            order by end_time desc, priority desc
            limit 1")->getALL();
        }
        $linkArr = array('numtype', 'jctype', 'numrm', 'jcrm', 'num1', 'num2', 'num3', 'num4', 'jc1', 'jc2', 'jc3', 'jc4');
        $shouye = unserialize($this->cache->get($this->redis['SHOUYE']));
        foreach ($linkArr as $position) {
            $data[$position] = $shouye[$position];
        }
        return $data;
    }
}