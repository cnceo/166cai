<?php
class Cp_Channel extends MY_Model
{
	protected $_table = 'cp_channel';

	public function __construct()
	{
		parent::__construct ();
	}
	/**
	 * [checkUser 验证用户是否存在]
	 * @author Likangjian  2017-04-30
	 * @param  [type] $name [description]
	 * @param  [type] $pass [description]
	 * @return [type]       [description]
	 */
	public function checkUser($name, $pass)
	{
		$sql = "select id, uname, password, status from cp_channel_user where uname = ? and password = ?";
		$res = $this->slave->query($sql, array ($name,md5($pass)))->getRow();
		return $res;
	}
	
	/**
	 * 查询用户信息
	 * @param unknown $id
	 * @return unknown
	 */
	public function getUser($id) 
	{
	    return $this->slave->query("select id, channels, fields from cp_channel_user where id = ?", array($id))->getRow();
	}
	
	/**
	 * 查询用户渠道信息
	 * @param unknown $channelIds
	 * @return unknown
	 */
	public function getChannels($channelIds)
	{
	    $data = array();
	    $sql = "select id, name from cp_channel where id in ?";
	    $res = $this->slave->query($sql, array($channelIds))->getAll();
	    foreach ($res as $val) {
	        $data[$val['id']] = $val;
	    }
	    
	    return $data;
	}
	
	public function updateUser($id, $data)
	{
	    $this->db->where('id', $id);
	    $this->db->update('cp_channel_user', $data);
	    return $this->db->affected_rows();
	}
	
	/**
	 * [listCountData description]
	 * @author Likangjian  2017-04-30
	 * @param  [type] $searchData [description]
	 * @param  [type] $page       [description]
	 * @param  [type] $pageCount  [description]
	 * @return [type]             [description]
	 */
	public function listCountData($searchData, $page, $pageCount, $channels)
	{
        if(strtotime($searchData['start_time']) < strtotime('2017-05-22 00:00:00')) {
            $searchData['start_time'] = '2017-05-22 00:00:00';
        }
        
        $where = " where 1 and channel_id in ({$channels}) and (date BETWEEN ? AND ?) and cpstate in(1,2)";
        if(empty($searchData['channel_id'])) {
            unset($searchData['channel_id']);
        } else {
            $where .= " and channel_id = ?";
        }
        $select = "select date, channel_id, unit_price, balance_active, balance_reg, balance_real, 
        balance_yj, balance_amount, partner_lottery_num, partner_active_lottery_num, partner_curr_lottery_total_amount, 
        settle_mode, cpstate from cp_channel_count {$where} ORDER BY date DESC, id desc
        LIMIT " . ($page - 1) * $pageCount . "," . $pageCount;
        $totalSql = "select count(*) as rows, sum(balance_active) as balance_active, sum(balance_reg) as balance_reg, 
        sum(balance_real) as balance_real, sum(balance_yj) as balance_yj, sum(balance_amount) as balance_amount,
        sum(partner_lottery_num) as partner_lottery_num, sum(partner_active_lottery_num) as partner_active_lottery_num, sum(partner_curr_lottery_total_amount) as partner_curr_lottery_total_amount from cp_channel_count {$where}";
        $count = $this->slave->query($totalSql, $searchData)->getRow();
        $result = $this->slave->query($select, $searchData)->getAll();
        return array(
            $result,
            $count,
        );
	}
    /**
     * [updateChannelPwd 更新密码]
     * @author Likangjian  2017-04-30
     * @param  [type] $data [description]
     * @return [type]       [description]
     */
    public function updateChannelPwd($data)
    {
        $select = "SELECT * from cp_channel_user WHERE id = ? and status = 1";
        $row = $this->db->query($select,array($data['id']))->getRow();
        if(!count($row)) return $tag = -2;
        if($row['password'] != md5($data['oldpwd']) ) return $tag = -1;
        //更新
        $update = "UPDATE cp_channel_user SET password= ? WHERE id= ? ";
        $tag = $this->db->query($update,array( md5($data['newpwd']) , $data['id'] ) );
        if($tag === false) return false; 
        return true;
    }
    /**
     * [getExportData 获取导出数据源]
     * @author LiKangJian 2017-05-02
     * @param  [type] $searchData [description]
     * @return [type]             [description]
     */
    public function getExportData($searchData, $channels)
    {
        if(strtotime($searchData['start_time']) < strtotime('2017-05-22 00:00:00')) {
            $searchData['start_time'] = '2017-05-22 00:00:00';
        }
        
        $where = " where 1 and channel_id in ({$channels}) and (date BETWEEN ? AND ?) and cpstate in(1,2)";
        if(empty($searchData['channel_id'])) {
            unset($searchData['channel_id']);
        } else {
            $where .= " and channel_id = ?";
        }
        $select = "select date, channel_id, unit_price, balance_active, balance_reg, balance_real,
        balance_yj, balance_amount, partner_lottery_num, partner_active_lottery_num, partner_curr_lottery_total_amount, settle_mode, cpstate from cp_channel_count {$where} ORDER BY date DESC, id desc";
        return $this->slave->query($select, $searchData)->getAll();
    }
    
    /**
     * 查询佣金金额
     * @param unknown $searchData
     * @param unknown $channels
     * @return unknown
     */
    public function getExportBalanceData($searchData, $channels)
    {
        if(strtotime($searchData['start_time']) < strtotime('2017-05-22 00:00:00')) {
            $searchData['start_time'] = '2017-05-22 00:00:00';
        }
        
        $where = " where 1 and channel_id in ({$channels}) and (date BETWEEN ? AND ?) and cpstate in(1,2)";
        if(empty($searchData['channel_id'])) {
            unset($searchData['channel_id']);
        } else {
            $where .= " and channel_id = ?";
        }
        $select = "select sum(balance_yj) as balance_yj from cp_channel_count {$where}";
        return $this->slave->query($select, $searchData)->getRow();
    }
}