<?php

/*
 * 资讯 评论
 * @date:2017-07-28
 */
class Info_Comments_Model extends MY_Model
{
    public function __construct()
    {
        parent::__construct();
    }

    // 检查一天内未审核评论
    public function getUncheckedComments()
    {
        $sql = "SELECT id, newsId, uid, content, sensitives, 'status' FROM cp_info_comments WHERE created >= date_sub(now(), interval 1 DAY) AND status = 0 AND delete_flag = 0 ORDER BY created ASC LIMIT 500";
        return $this->db->query($sql)->getAll();
    }

    // 更新审核状态
    public function updateCheckStatus($id, $checkRes, $sensitives = '')
    {
        $sql = "UPDATE cp_info_comments SET status = ?, sensitives = ? WHERE id = ? AND delete_flag = 0";
        $this->db->query($sql, array($checkRes, $sensitives, $id));
        $res = $this->db->affected_rows();

        if($res && $checkRes == 1)
        {
            $this->checkReply($id);
        }

        return $res;
    }

    // 更新阅读量
    public function updateReadNum($id)
    {
        $sql = "update cp_info set num = num + 1,trueNum = trueNum + 1 where id = ?";
        $this->db->query($sql, array('id' => $id));
    }

    // 更新评论数
    public function updateComNum($id, $flag = 1)
    {
        if($flag)
        {
            $con = "+ 1";
            $where = "";
        }
        else
        {
            $con = "- 1";
            $where = " AND comNum > 0";
        }
        $sql = "UPDATE cp_info SET comNum = comNum {$con} WHERE id = ?{$where}";
        $this->db->query($sql, array($id));
        return $this->db->affected_rows();
    }

    // 更新点赞数
    public function updateLikeNum($id, $flag = 1)
    {
        if($flag)
        {
            $con = "+ 1";
            $where = "";
        }
        else
        {
            $con = "- 1";
            $where = " AND likeNum > 0";
        }
        $sql = "UPDATE cp_info SET likeNum = likeNum {$con},truelikeNum = truelikeNum {$con} WHERE id = ?{$where}";
        $this->db->query($sql, array($id));
        return $this->db->affected_rows();
    }

    // 查询指定资讯的评论
    public function getComment($id, $uid = 0, $page = 1, $number = 10)
    {
        if($uid)
        {
            $sql = "SELECT 
                c1.id, c1.newsId AS newsId, c1.uid AS uid, u1.uname AS uname, c1.content AS content, c1.tid AS tid, c1.pid AS pid, c1.tuid AS tuid, c1.created AS created, c1.status AS status, c1.floor AS floor, c2.content AS tcontent, u2.uname AS tuname, c2.status AS tstatus, c2.delete_flag AS tdelete,u3.headimgurl  
                FROM `cp_info_comments` AS c1 
                LEFT JOIN cp_info_comments AS c2 
                ON IF(c1.tid > 0, c1.tid, c1.pid) = c2.id 
                LEFT JOIN cp_user AS u1 
                ON c1.uid = u1.uid 
                LEFT JOIN cp_user AS u2 
                ON c2.uid = u2.uid 
                LEFT JOIN cp_user_info AS u3 
                ON c1.uid = u3.uid 
                WHERE c1.newsId = ? AND ((c1.uid = ?) OR (c1.uid <> ? AND c1.status = 1 AND c1.delete_flag = 0)) ORDER BY c1.created DESC LIMIT " . ($page - 1) * $number . ", $number";
            $info = $this->db->query($sql, array($id, $uid, $uid))->getAll();
        }
        else
        {
            $sql = "SELECT c1.id, c1.newsId AS newsId, c1.uid AS uid, u1.uname AS uname, c1.content AS content, c1.tid AS tid, c1.pid AS pid, c1.tuid AS tuid, c1.created AS created, c1.status AS status, c1.floor AS floor, c2.content AS tcontent, u2.uname AS tuname, c2.status AS tstatus, c2.delete_flag AS tdelete,u3.headimgurl FROM `cp_info_comments` AS c1 LEFT JOIN cp_info_comments AS c2 ON IF(c1.tid > 0, c1.tid, c1.pid) = c2.id LEFT JOIN cp_user AS u1 ON c1.uid = u1.uid LEFT JOIN cp_user AS u2 ON c2.uid = u2.uid LEFT JOIN cp_user_info AS u3 ON c1.uid = u3.uid WHERE c1.newsId = ? AND c1.status = 1 AND c1.delete_flag = 0 ORDER BY c1.created DESC LIMIT " . ($page - 1) * $number . ", $number";
            $info = $this->db->query($sql, array($id))->getAll();
        }
        return $info;
    }

    // 新增评论
    public function postComment($info)
    {
        $comInfo = array();
        // 事务处理
        $this->db->trans_start();

        $newsInfo = $this->db->query('SELECT id, trueComNum FROM cp_info WHERE id = ? for update', array($info['newsId']))->getRow();

        if(empty($newsInfo))
        {
            $this->db->trans_rollback();
            return $comInfo;
        }

        if(!empty($info['tid']))
        {
            $toComInfo = $this->db->query('SELECT uid, tid, pid FROM cp_info_comments WHERE id = ?', array($info['tid']))->getRow();

            if(empty($toComInfo))
            {
                $this->db->trans_rollback();
                return $comInfo;
            }

            $tuid = $toComInfo['uid'];
            $tid = $toComInfo['pid'] > 0 ? $info['tid'] : 0;
            $pid = $toComInfo['pid'] > 0 ? $toComInfo['pid'] : ((empty($toComInfo['tid']) && empty($toComInfo['pid'])) ? $info['tid'] : 0 );
        }

        if(ENVIRONMENT === 'checkout')
        {
            if($info['newsId'] > 136780)
            {
                $floor = $newsInfo['trueComNum'] + 1;
            }
        }
        else
        {
            if($info['newsId'] > 111397)
            {
                $floor = $newsInfo['trueComNum'] + 1;
            }
        }

        $comData = array(
            'newsId'    =>  $info['newsId'],
            'uid'       =>  $info['uid'],
            'content'   =>  $info['content'],
            'floor'     =>  $floor ? $floor : 0,
            'tid'       =>  $tid ? $tid : 0,
            'pid'       =>  $pid ? $pid : 0,
            'tuid'      =>  $tuid ? $tuid : 0,
            'status'    =>  ($info['uid'] == 1) ? 1 : 0,
        );

        $fields = array_keys($comData);
        $sql = "insert cp_info_comments(" . implode(',', $fields) . ", created)values(" . 
        implode(',', array_map(array($this, 'maps'), $fields)) .  ", now())" ;
        $this->db->query($sql, $comData);
        $comId = $this->db->insert_id();

        // 更新评论数
        $infoSql = "UPDATE cp_info SET trueComNum = trueComNum + 1 WHERE id = ?";
        $this->db->query($infoSql, array($info['newsId']));
        $res = $this->db->affected_rows();

        if($comId > 0 && $res)
        {
            $this->db->trans_complete();
            $comInfo = $this->getCommentDetail($comId);
            if($info['uid'] == 1)
            {
                // 网站回复直接更新评论数
                $this->updateComNum($info['newsId'], 1);
                // 更新原评论官方回复状态
                $this->updateAdminReply($info['tid']); 
                // 网站回复直接刷新回复
                $this->checkReply($comId);
            } 
        }
        else
        {
            $this->db->trans_rollback();
        }
        return $comInfo;
    }

    // 后台评论管理列表
    public function list_comments($searchData, $page, $pageCount)
    {
        $where = " WHERE 1";

        if($searchData['uname'])
        {
            if($searchData['replied'])
            {
                $where .= " AND u2.uname like '%{$searchData['uname']}%'";
            }
            else
            {
                $where .= " AND u.uname like '%{$searchData['uname']}%'";
            }  
        }
        if($searchData['title'])
        {
            $where .= " AND i.title like '%{$searchData['title']}%'";
        }
        if($searchData['start_time'] && $searchData['end_time'])
        {
            if($searchData['replied'])
            {
                $where .= " AND c2.created >= '{$searchData['start_time']}' AND c2.created <= '{$searchData['end_time']}'";
            }
            else
            {
                $where .= " AND c.created >= '{$searchData['start_time']}' AND c.created <= '{$searchData['end_time']}'";
            } 
        }
        if($searchData['number'])
        {
            if($searchData['replied'])
            {
                $where .= " AND c2.content regexp '[0-9]'";
            }
            else
            {
                $where .= " AND c.content regexp '[0-9]'";
            }   
        }
        if($searchData['word'])
        {
            if($searchData['replied'])
            {
                $where .= " AND c2.content regexp '[a-zA-Z]'";
            }
            else
            {
                $where .= " AND c.content regexp '[a-zA-Z]'";
            }  
        }
        if($searchData['chinesenumer'])
        {
            if($searchData['replied'])
            {
                $where .= " AND c2.content regexp '一|二|三|四|五|六|七|八|九|十'";
            }
            else
            {
                $where .= " AND c.content regexp '一|二|三|四|五|六|七|八|九|十'";
            }   
        }
        if($searchData['delete'])
        {
            if($searchData['replied'])
            {
                $where .= " AND c2.delete_flag = 0";
            }
            else
            {
                $where .= " AND c.delete_flag = 0";
            }   
        }
        $groupby = "";
        if($searchData['uncomment'])
        {
            if($searchData['replied'])
            {
                $where .= " AND ui2.uncomment = {$searchData['uncomment']}";
                $groupby = " group by c.uid";
            }
            else
            {
                $where .= " AND ui.uncomment = {$searchData['uncomment']}";
                $groupby = " group by c.uid";
            }
        }
        if($searchData['status'])
        {
            $status = $searchData['status'] - 1;
            if($searchData['replied'])
            {
                
                $where .= " AND c2.status = {$status}";
            }
            else
            {
                $where .= " AND c.status = {$status}";
            }      
        }
        // 本站回复过的评论
        if($searchData['replied'])
        {
            $where .= " AND c.uid = 1 AND c.tuid > 0";
        }
        else if($searchData['replyadmin'])
        {
            // 用户回复本站的评论
            $where .= " AND c.tuid = 1 AND c.admin_reply = 0";
        }
        $select = "select * from (SELECT c.id, c.tid, c.pid, c.newsId, i.title, c.uid, u.uname, u2.uname AS tuname, u2.uid AS tuid, c.content, c.floor, c.created, i.source_id, i.category_id, c.status, c.sensitives, c.delete_flag, ui.uncomment, ui2.uncomment AS tuncomment, c2.content AS tcontent, c2.floor AS tfloor, c2.sensitives AS tsensitives, c2.status AS tstatus, c2.delete_flag AS tdelete_flag, c2.created AS tcreated FROM cp_info_comments AS c LEFT JOIN cp_info AS i ON c.newsId = i.id LEFT JOIN cp_user AS u ON c.uid = u.uid LEFT JOIN cp_user AS u2 ON c.tuid = u2.uid LEFT JOIN cp_user_info AS ui ON u.uid = ui.uid LEFT JOIN cp_user_info AS ui2 ON u2.uid = ui2.uid LEFT JOIN cp_info_comments AS c2 ON IF(c.tid > 0, c.tid, c.pid) = c2.id {$where} ORDER BY c.created DESC) c {$groupby} ORDER BY c.created DESC LIMIT " . ($page - 1) * $pageCount . "," . $pageCount;
        $result = $this->db->query($select)->getAll();
        $count = $this->db->query("SELECT COUNT(*) as count FROM cp_info_comments AS c LEFT JOIN cp_info AS i ON c.newsId = i.id LEFT JOIN cp_user AS u ON c.uid = u.uid LEFT JOIN cp_user AS u2 ON c.tuid = u2.uid LEFT JOIN cp_user_info AS ui ON u.uid = ui.uid LEFT JOIN cp_user_info AS ui2 ON u2.uid = ui2.uid LEFT JOIN cp_info_comments AS c2 ON IF(c.tid > 0, c.tid, c.pid) = c2.id {$where} {$groupby}")->getOne();
        return array(
            $result,
            $count
        );
    }

    // 后台删除评论
    public function delComment($newsId, $id)
    {
        $this->db->query("UPDATE cp_info_comments SET delete_flag = 1 WHERE id = ? AND delete_flag = 0", array($id));
        $res = $this->db->affected_rows();

        $info = $this->getCommentDetail($id);
        // 已审核成功的 -1
        if($res && $info['status'] == '1')
        {
            $this->updateComNum($newsId, 0);
        }
    }

    // 后台手动成功
    public function handleCommSucc($newsId, $id)
    {
        $res = $this->updateCheckStatus($id, 1, '');
        if($res)
        {
            $this->updateComNum($newsId, 1);
        }
    }

    // 评论点赞
    public function infoLike($info)
    {
        $res = FALSE;
        $this->db->trans_start();

        // 行锁
        $this->db->query('SELECT id FROM cp_info WHERE id = ? for update', array($info['newsId']))->getRow();

        $likesInfo = $this->getCommentLike($info['newsId'], $info['uid']);

        if(!empty($likesInfo) && $likesInfo['isLike'] == $info['status'])
        {
            $res = TRUE;
            $this->db->trans_complete();
        }
        else
        {
            // 更新点赞数据
            $likeData = array(
                'newsId'    =>  $info['newsId'],
                'uid'       =>  $info['uid'],
                'isLike'    =>  $info['status'],
            );
            $res1 = $this->updateComLike($likeData);

            if(empty($likesInfo) && $info['status'] == 0)
            {
                $res2 = TRUE;
            }
            elseif($info['status'] == 1)
            {
                $res2 = $this->updateLikeNum($info['newsId'], 1);
            }
            else
            {
                $res2 = $this->updateLikeNum($info['newsId'], 0);
            }

            if($res1 && $res2)
            {
                $res = TRUE;
                $this->db->trans_complete();
            }
            else
            {
                $this->db->trans_rollback();
            }
        }
        return $res;
    }

    // 更新点赞表
    public function updateComLike($info)
    {
        $upd = array('isLike');
        $fields = array_keys($info);
        $sql = "insert cp_info_likes(" . implode(',', $fields) . ", created)values(" . 
        implode(',', array_map(array($this, 'maps'), $fields)) .  ", now())" . $this->onduplicate($fields, $upd);
        return $this->db->query($sql, $info);
    }

    // 点赞详情
    public function getCommentLike($newsId, $uid)
    {
        $sql = "SELECT id, newsId, uid, isLike, delete_flag FROM cp_info_likes WHERE newsId = ? AND uid = ?";
        return $this->db->query($sql, array($newsId, $uid))->getRow();
    }

    // 查询指定评论
    public function getCommentDetail($comId)
    {
        $sql = "SELECT c1.id, c1.newsId AS newsId, c1.uid AS uid, u1.uname AS uname, c1.content AS content, c1.tid AS tid, c1.pid AS pid, c1.tuid AS tuid, c1.created AS created, c1.status AS status, c1.floor AS floor, c2.content AS tcontent, u2.uname AS tuname, c2.status AS tstatus, c2.delete_flag AS tdelete FROM `cp_info_comments` AS c1 LEFT JOIN cp_info_comments AS c2 ON IF(c1.tid > 0, c1.tid, c1.pid) = c2.id LEFT JOIN cp_user AS u1 ON c1.uid = u1.uid LEFT JOIN cp_user AS u2 ON c2.uid = u2.uid WHERE c1.id = ?";
        return $this->db->query($sql, array($comId))->getRow();
    }

    // 后台日志 查询指定的评论信息
    public function getCommentsByIds($ids)
    {
        $sql = "SELECT c.id, c.newsId, c.uid, c.content, c.created, i.source_id, i.category_id, c.status, c.sensitives, c.delete_flag FROM cp_info_comments AS c LEFT JOIN cp_info AS i ON c.newsId = i.id WHERE c.id IN ('" . $ids . "')";
        return $this->db->query($sql)->getAll();
    }

    // 获取详情
    public function getInfoDetail($id, $uid = 0)
    {
        if($uid)
        {
            $sql = "select i.id, i.title, i.content, i.is_top, i.weight, i.category_id, i.source_id, i.submitter, i.submitter_id, i.num, i.likeNum, i.comNum, i.is_show, i.created, (if(SUM(l.isLike) > 0, 1, 0)) AS isUserLike from cp_info AS i LEFT JOIN cp_info_likes AS l ON i.id = l.newsId where i.id = ? AND uid = ?";
            $info = $this->slave->query($sql, array('id' => $id, 'uid' => $uid))->getRow();
        }
        else
        {
            $sql = "select id, title, content, is_top, weight, category_id, source_id, submitter, submitter_id, num, likeNum, comNum, is_show, created from cp_info where id = ?";
            $info = $this->slave->query($sql, array('id' => $id))->getRow();
        }
        return $info;
    }
    
    public function handlecomment($uid, $status)
    {
        $sql = "UPDATE cp_user_info SET uncomment = ? where uid = ?";
        $this->db->query($sql, array($status, $uid));
        $sql = "select uname from cp_user where uid = ?";
        $res = $this->db->query($sql, array($uid))->getRow();
        return $res;
    }

    public function getReplyList($uid, $page = 1, $number = 10)
    {
        $sql = "SELECT c1.id AS id, c1.newsId AS newsId, c1.uid AS uid, u1.uname AS uname, c1.content AS content, c1.tid AS tid, c1.pid AS pid, c1.tuid AS tuid, c1.created AS created, c2.content AS tcontent, u2.uname AS tuname, c2.delete_flag AS tdelete, i.title, i.category_id,u3.headimgurl FROM `cp_info_comments` AS c1 LEFT JOIN cp_info_comments AS c2 ON IF(c1.tid > 0, c1.tid, c1.pid) = c2.id LEFT JOIN cp_user AS u1 ON c1.uid = u1.uid LEFT JOIN cp_user AS u2 ON c2.uid = u2.uid LEFT JOIN cp_info AS i ON c1.newsId = i.id LEFT JOIN cp_user_info AS u3 ON c1.uid = u3.uid WHERE c1.tuid = ? AND c1.`status` = 1 AND c1.delete_flag = 0 ORDER BY c1.created DESC LIMIT " . ($page - 1) * $number . ", $number";
        return $this->slave->query($sql, array($uid))->getAll();
    }

    // 刷新回复id
    public function checkReply($id)
    {
        $detail = $this->getCommentDetail($id);
        if(!empty($detail) && !empty($detail['tuid']) && $detail['status'] == '1')
        {
            $this->refreshReplyId($detail['tuid'], $id);
        }
    }

    public function getReplyId($uid)
    {
        $rediskeys = $this->config->item("REDIS");
        $this->load->driver('cache', array('adapter' => 'redis'));
        $replyId = $this->cache->redis->hGet($rediskeys['USER_INFO'] . $uid, "replyId");
        $replyId = $replyId ? $replyId : 0;
        return $replyId;
    }

    public function refreshReplyId($uid, $replyId = 0)
    {
        // 刷新缓存
        $rediskeys = $this->config->item("REDIS");
        $this->load->driver('cache', array('adapter' => 'redis'));
        if($this->cache->redis->hGet($rediskeys['USER_INFO'] . $uid, "uname"))
        {
            $replyId = $replyId ? 1 : 0;
            $this->cache->redis->hSet($rediskeys['USER_INFO'] . $uid, "replyId", $replyId);
        }
    }

    // 更新原评论官方回复状态
    public function updateAdminReply($id)
    {
        $this->db->query("UPDATE cp_info_comments SET admin_reply = 1 WHERE id = ?", array($id));
    }

    // 获取评论数
    public function getCommentCount($newsId, $uid)
    {
        return $this->slave->query("SELECT count(*) FROM cp_info_comments WHERE newsId = ? AND uid = ? AND status = 1 AND delete_flag = 0", array($newsId, $uid))->getOne();
    }
}
