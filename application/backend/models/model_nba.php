<?php

/**
 * Copyright (c) 2014,上海二三四五网络科技股份有限公司
 * 摘    要：公告管理模型
 * 作    者：shigx@2345.com
 * 修改日期：2015.03.25
 */
class Model_nba extends MY_Model
{
    public function __construct()
    {
    	parent::__construct();
    	$this->get_db();
    }
    
    public function getAllData()
    {
    	$sql = "select id, team, priority, zone from cp_info_nba
    			order by zone, priority";
    	$res = $this->BcdDb->query($sql)->getAll();
    	$data = array();
    	foreach ($res as $value) {
    		$data[$value['zone']][$value['priority']] = array(
    			'team' => $value['team'],
    			'id' => $value['id']
    		);
    	}
    	return $data;
    }
    
    public function getDataById($id) {
    	$sql = "select team from cp_info_nba where id = ?";
    	return $this->BcdDb->query($sql, array($id))->getRow();
    }
    
    public function updateNba($data, $id) {

    	$sql = "update cp_info_nba set priority = ? where id = ?";
    	$this->master->query($sql, array($data['priority'], $id));

    }
    
    public function getAll() {
    	$sql = "SELECT n.id, n.zone, n.nickName, n.priority, n.team, i.`name`, i.position, i.updateTime, i.injury, i.indices FROM
				cp_info_nba as n LEFT JOIN cp_info_injury as i ON n.id=i.team ORDER BY n.zone, n.priority, n.id";
    	$res = $this->BcdDb->query($sql)->getAll();
    	$data = array();
    	foreach ($res as $value) {
    		$data[$value['zone']][$value['priority']]['team'] = $value['team'];
    		$data[$value['zone']][$value['priority']]['nickName'] = $value['nickName'];
    		$data[$value['zone']][$value['priority']]['id'] = $value['id'];
    		if (!empty($value['name'])) {
    			$data[$value['zone']][$value['priority']]['injury'][] = array(
    				'name' => $value['name'],
    				'position' => $value['position'],
    				'updateTime' => $value['updateTime'],
    				'injury' => $value['injury'],
    				'indices' => $value['indices']
    			);
    		}
    	}
    	return $data;
    }
    
}
