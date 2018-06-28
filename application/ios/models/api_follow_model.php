<?php

/**
 * 关注接口
 * @date:2017-05-24
 * 
 */
class Api_Follow_Model extends MY_Model
{
    public function __construct()
    {
        parent::__construct();
        $this->load->driver('cache', array('adapter' => 'redis'));
        $this->config->load('jcMatch');
        $this->redis = $this->config->item('redisList');
        $this->apiUrl = $this->config->item('apiUrl');
    }

    /**
     * [relation 关注取消关注]
     * @author LiKangJian 2017-05-24
     * @param  [type] $params [description]
     * @return [type]        [description]
     */
    public function relation($params)
    {
    	//查询关注是否存在
        $vail = $this->db->query("select id from cp_user where uid in ?",array(array($params['puid'],$params['uid'])))->getAll();
        if(count($vail)!=2 || empty($vail)) return $tag = -3;
        $row = $this->db->query("select id,follow_status from cp_united_follow where puid = ? and uid = ? ",array($params['puid'] , $params['uid']))->getRow();
        if($params['status'] == 1)
    	{
            if($row&& $row['follow_status'] == 1)
            {
             return $tag = -2;
            }else if($row&& $row['follow_status'] == 0)
            {
                $sql = "update cp_united_follow set follow_status = ? where puid = ? and uid = ? ";
                $tag = $this->db->query( $sql , array( $params['status'] , $params['puid'] , $params['uid'] ) );
            }else{
                $sql = "INSERT INTO `cp_united_follow` (`id`, `puid`, `uid`, `follow_status`, `created`, `modified`) VALUES (NULL, ?, ?, ?, NOW(), CURRENT_TIMESTAMP);";
                $tag = $this->db->query( $sql , array( $params['puid'] , $params['uid'] , $params['status'] ) );
            }

        }else{
            if($row===false){ return $tag = -1;}
            $sql = "update cp_united_follow set follow_status = ? where puid = ? and uid = ? ";
            $tag = $this->db->query( $sql , array( $params['status'] , $params['puid'] , $params['uid'] ) );
        }

    	return $tag ===false ? 0 : 1;
    }
    /**
     * [getFollowList 关注列表]
     * @author LiKangJian 2017-06-01
     * @param  [type] $params [description]
     * @return [type]        [description]
     */
    public function getFollowList($params)
    {
    	$count_sql = "SELECT count(*) as count FROM cp_united_follow WHERE uid = ? AND follow_status = 1";
        $count = $this->db->query($count_sql,array($params['uid']))->getCol();
        $sql = "SELECT  u.uid,u.uname
               FROM cp_united_follow AS f 
               LEFT JOIN cp_user AS u ON u.uid = f.puid 
               WHERE f.uid = ? AND f.follow_status = 1 order BY f.modified DESC " .$this->getLimit($params['page'],$params['pageNum']);
    	$data = $this->db->query($sql,array($params['uid']))->getAll();
        return $data;
    }
    /**
     * [isFollow 是否关注查询]
     * @author LiKangJian 2017-06-01
     * @param  [type]  $params [description]
     * @return boolean        [description]
     */
    public function isFollow($params)
    {
        $sql = "SELECT id FROM cp_united_follow WHERE puid = ? AND uid = ? AND follow_status = ?";
        $row = $this->db->query($sql,array($params['puid'],$params['uid'],1))->getRow();
        $tag = false;
        if($row) $tag = true;
        return $tag;
    }
    /**
     * [getLimit 获取limit]
     * @author LiKangJian 2017-05-25
     * @param  [type] $page    [description]
     * @param  [type] $pageNum [description]
     * @return [type]          [description]
     */
    private function getLimit($page,$pageNum)
    {
        $startNum = ($page - 1)*$pageNum;
        return ' LIMIT '.$startNum.','.$pageNum;
    }


}