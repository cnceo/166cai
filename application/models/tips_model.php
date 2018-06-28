<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Tips_Model extends MY_Model
{
	/**
     * 参    数：$phone 手机号码
     * 作    者：shigx
     * 功    能：添加数据
     * 修改日期：2015.03.17
     */
    public function savePhone($phone)
    {
        $sql = "INSERT INTO `cp_tips_phone`(phone,created) VALUES(?,now()) ON DUPLICATE KEY UPDATE phone = VALUES(phone)";
        return $this->db->query($sql, $phone);
    }
    
    public function getDatas()
    {
	    $fname = 'C:\Users\Administrator\Desktop\work\2017-08\datas';
	    $date = '2017-07-31';
	    while($date >= '2017-01-31')
	    {
	    	$this->get_inaction_user($date, $fname);
	    	$sdate = date('Y-m-01', strtotime($date));
	    	$date = date('Y-m-d', strtotime('-1 day', strtotime($sdate)));
	    }
	    /*$date = '2017-08-20';
    	while($date >= '2017-07-02')
	    {
	    	$this->get_new_user($date, $fname);
	    	echo $date = date('Y-m-d', strtotime('-7 day', strtotime($date)));
	    }*/
    }
    
    private function get_loyal_user($date, $fname)
    {
    	$datas['header'] = array('UID');
    	$datas['fname'] = "{$fname}\week_[zcyh]_[{$date}].csv";
    	$subsql = "select uid, count(DISTINCT(date(created))) lnum from  bn_cpiao.cp_login_info_tmp force INDEX (created)
		where 1 and  created >= DATE_SUB('$date',INTERVAL 30 day) and created < '$date'
		group by uid having lnum >= 15";
    	$datas['rows'] = $this->db->query("select uid from ($subsql) st")->getAll();
    	$this->saveFiles($datas);
    }
    
    private function get_ptaction_user($date, $fname)
    {
    	$datas['header'] = array('UID');
    	$datas['fname'] = "{$fname}\week_[pthyyh]_[{$date}].csv";
    	$subsql = "select uid, count(DISTINCT(date(created))) lnum from  bn_cpiao.cp_login_info_tmp force INDEX (created)
		where 1 and  created >= DATE_SUB('$date',INTERVAL 30 day) and created < '$date'
		group by uid having lnum >= 2 and lnum < 15";
    	$datas['rows'] = $this->db->query("select uid from ($subsql) st")->getAll();
    	$this->saveFiles($datas);
    }
    
    private function get_new_user($date, $fname)
    {
    	$datas['header'] = array('UID');
    	$datas['fname'] = "{$fname}\month_[xzyh]_[{$date}].csv";
    	$subsql = "select uid from bn_cpiao.cp_user where created >= DATE_SUB('$date',INTERVAL 30 day) and created < '$date'";
    	$datas['rows'] = $this->db->query("select uid from ($subsql) st")->getAll();
    	$this->saveFiles($datas);
    }
    
    private function get_inaction_user($date, $fname)
    {
    	$datas['header'] = array('UID');
    	$datas['fname'] = "{$fname}\month_[bhyyh]_[{$date}].csv";
    	#step 1
    	$this->db->query('truncate table bn_cpiao_tmp.jjd');
    	$this->db->query("insert bn_cpiao_tmp.jjd(uid)
		select uid
		from bn_cpiao.cp_login_info_tmp where created >= DATE_SUB('$date',INTERVAL 60 day) 
		and created < DATE_SUB('$date',INTERVAL 30 day) group by uid");
    	#step 2
    	$this->db->query('truncate table bn_cpiao_tmp.jjo');
    	$this->db->query("insert bn_cpiao_tmp.jjo(uid)
		select uid 
		from bn_cpiao.cp_login_info_tmp where created >= DATE_SUB('$date',INTERVAL 30 day) 
		and created < '$date' group by uid");
    	#step 3
    	$datas['rows'] = $this->db->query("select m.uid from bn_cpiao_tmp.jjd m
		left join bn_cpiao_tmp.jjo n on m.uid = n.uid
		where n.uid is null")->getAll();
    	$this->saveFiles($datas);
    }
        
    private function get_wastage_user($date, $fname)
    {
    	$datas['header'] = array('UID');
    	$datas['fname'] = "{$fname}\month_[lsyh]_[{$date}].csv";
    	#step 1
    	$this->db->query('truncate table bn_cpiao_tmp.jjd');
    	$this->db->query("insert bn_cpiao_tmp.jjd(uid)
		select uid
		from bn_cpiao.cp_login_info_tmp where created < DATE_SUB('$date',INTERVAL 60 day) 
		and created > 0 group by uid");
    	#step 2
    	$this->db->query('truncate table bn_cpiao_tmp.jjo');
    	$this->db->query("insert bn_cpiao_tmp.jjo(uid)
		select uid
		from bn_cpiao.cp_login_info_tmp where created >= DATE_SUB('$date',INTERVAL 60 day) 
		and created < '$date' group by uid");
    	#step 3
    	$datas['rows'] = $this->db->query("select m.uid from bn_cpiao_tmp.jjd m
		left join bn_cpiao_tmp.jjo n on m.uid = n.uid
		where n.uid is null")->getAll();
    	$this->saveFiles($datas);
    }
    
    private function saveFiles($datas)
    {
    	if(!empty($datas['rows']))
    	{
    		$fp = fopen($datas['fname'], 'w');
    		fputcsv($fp, $datas['header']);
    		foreach ($datas['rows'] as $row)
    		{
    			fputcsv($fp, $row);
    		}
    		fclose($fp);
    	}
    }
}
