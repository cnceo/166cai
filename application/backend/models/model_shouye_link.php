<?php

class Model_shouye_link extends MY_Model
{
		
    public function __construct()
    {
    	parent::__construct();
    	$this->get_db();
    }
    
    public function getDataByPosition($position)
    {
    	$sql = "select id, title, url, redflag, priority from {$this->cp_sy_ln} where position = ? order by priority";
    	$res = $this->master->query($sql, array($position))->getAll();
    	$data = array();
    	foreach ($res as $value)
    	{
    		$data[$value['priority']] = array(
    			'title' => $value['title'],
    			'url' => $value['url'],
    			'redflag' => $value['redflag'],
    			'id'  => $value['id']
    		);
    	}
    	return $data;
    }
    
    public function delByPosition($position)
    {
    	$sql = "delete from {$this->cp_sy_ln} where position = ?";
    	return $this->master->query($sql, array($position));
    }
    
    public function insertAllData($datas)
    {
    	$sql = "insert into {$this->cp_sy_ln} (position, title, url, priority, redflag) values ";
    	foreach ($datas as $data)
    	{
    		$sql .= "('{$data['position']}', '{$data['title']}', '{$data['url']}', '{$data['priority']}', '{$data['redflag']}'), ";
    	}
    	$sql = substr($sql, 0, -2);
    	return $this->master->query($sql);
    }
    
    public function updateData($data, $id) 
    {
    	$this->master->update ( $this->cp_sy_ln, $data, array('id' => $id) );
    }
    
    public function getAlldata()
    {
    	$sql = "select * from {$this->cp_sy_ln} order by position";
    	return $this->BcdDb->query($sql)->getAll();
    }

    // 首页中奖墙查询
    public function list_win($searchData, $page, $pageCount)
    {
        $where = "WHERE 1 AND created >= '{$searchData['start_time']}' AND created <= '{$searchData['end_time']}' AND delete_flag = 0 ";
        if($searchData['title'])
        {
            $where .= "AND title like '%{$searchData['title']}%' ";
        }
        if($searchData['lname'])
        {
            $where .= "AND lname like '%{$searchData['lname']}%' ";
        }
        if($searchData['status'] >= 0 && $searchData['status'] != '')
        {
            $where .= "AND status = '{$searchData['status']}' ";
        }
        if($searchData['submitter'])
        {
            $where .= "AND submitter like '%{$searchData['submitter']}%' ";
        }

        $sql = "SELECT id, title, url, content, is_top, newsId, lname, status, submitter, delete_flag, created FROM cp_shouye_win " . $where . "ORDER BY id DESC LIMIT " . ($page - 1) * $pageCount . "," . $pageCount;
        $res = $this->BcdDb->query($sql)->getAll();

        $sql = "SELECT count(*) as num FROM cp_shouye_win {$where}";
        $count = $this->BcdDb->query($sql)->getRow();

        return array(
            'data'  => $res,
            'count' => $count,
        );
    }
    
    public function recodeWinInfo($info)
    {
        $fields = array_keys($info);
        $sql = "insert cp_shouye_win(" . implode(',', $fields) . ", created)
        values(". implode(',', array_map(array($this, 'maps'), $fields)) .", now())";

        return $this->master->query($sql, $info);
    }

    public function setTopWin($id)
    {
        $sql = "UPDATE cp_shouye_win set is_top = 1 where id = ?";
        return $this->master->query($sql, array($id));
    }

    public function setDeleteWin($id)
    {
        $sql = "UPDATE cp_shouye_win set delete_flag = 1 where id = ?";
        return $this->master->query($sql, array($id));
    }

    public function getWinDetail($id)
    {
        $sql = "SELECT id, title, newsId, url, content, is_top, lname, submitter, status FROM cp_shouye_win WHERE id = ?";
        return $this->BcdDb->query($sql, array($id))->getRow();;
    }

    public function updateWinInfo($info)
    {
        $sql = "UPDATE cp_shouye_win set title = ?, newsId = ?, url = ?, content = ?, lname = ?, status = ? where id = ?";
        return $this->master->query($sql, array($info['title'], $info['newsId'], $info['url'], $info['content'], $info['lname'], $info['status'], $info['id']));
    }
}
