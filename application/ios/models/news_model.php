<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/*
 * 我的消息 -- 模型层
 * @author:liuli
 * @date:2015-01-20
 */

class News_Model extends MY_Model
{

	/*
     * 统计用户所有消息反馈
     * @author:liuli
     * @date:2015-01-20
     */
	public function countNewsList($uid)
	{
		try 
		{
			$sql = "SELECT count(*) from cp_operation_user where uid = ? and delect_flag = 0;";
			$count = $this->slave->query($sql, array($uid))->getCol(); 
		}
		catch (Exception $e)
		{
			log_message('LOG', "countNewsList error: " . __CLASS__ . ':' . __LINE__ , "ERROR");
			return false;
		}
		return $count;
	}

	/*
     * 统计用户为读消息数
     * @author:liuli
     * @date:2015-01-20
     */
	public function countUnreadList($uid)
	{
		try 
		{
			$sql = "SELECT count(*) from cp_operation_user where uid = ? and if_see = 0 and if_reply = 1 and delect_flag = 0;";
			$count = $this->slave->query($sql, array($uid))->getCol(); 
		}
		catch (Exception $e)
		{
			log_message('LOG', "countNewsList error: " . __CLASS__ . ':' . __LINE__ , "ERROR");
			return false;
		}
		return $count;
	}

    /*
     * 分页查询用户消息
     * @author:liuli
     * @date:2015-01-20
     */
    public function getNewsList($uid, $pagenum, $pagesize = 10)
    {
    	try 
		{
			$sql = "SELECT id,uid,name,content,if_reply,if_see,type,created,modified FROM cp_operation_user where uid = ? AND delect_flag = 0 ORDER BY created desc limit ?, ?;";
			$listInfo = $this->slave->query($sql, array($uid, $pagenum, intval($pagesize)))->getAll(); 
		}
		catch (Exception $e)
		{
			log_message('LOG', "getNewsList error: " . __CLASS__ . ':' . __LINE__ , "ERROR");
			return false;
		}
    	return $listInfo;
    }

    /*
     * 查询客服回复信息
     * @author:liuli
     * @date:2015-01-20
     */
    public function getReplyList($reply_id)
    {
    	try
    	{
    		$sql = "SELECT id,name,reply_id,content,created,modified FROM cp_operation_server where reply_id = ? AND delect_flag = 0 ORDER BY created desc;";
			$listInfo = $this->slave->query($sql, array($reply_id))->getAll(); 
    	}
    	catch (Exception $e)
		{
			log_message('LOG', "getReplayList error: " . __CLASS__ . ':' . __LINE__ , "ERROR");
			return false;
		}
		return $listInfo;
    }

    /*
     * 更新消息为已读
     * @author:liuli
     * @date:2015-01-20
     */
    public function updateNewsList($uid)
    {
    	try
    	{
			$this->db->query("UPDATE cp_operation_user SET if_see = 1 WHERE uid = ? and if_reply = 1",array($uid));
    	}
    	catch (Exception $e)
		{
			log_message('LOG', "updateNewsList error: " . __CLASS__ . ':' . __LINE__ , "ERROR");
			return false;
		}
		return $listInfo;
    }

    /*
     * 记录用户反馈
     * @author:liuli
     * @date:2015-01-26
     */
    public function insertNewsList($record)
    {
    	$fields = array_keys($record);
		$sql = "insert cp_operation_user(" . implode(',', $fields) . ", created)values(" . 
		implode(',', array_map(array($this, 'maps'), $fields)) .  ", now())" ;
		return $this->db->query($sql, $record);
    }

}
