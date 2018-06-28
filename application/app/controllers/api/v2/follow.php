<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Copyright (c) 2017,上海快猫文化传媒有限公司.
 * 摘    要: 关注关系接口
 * 作    者: 李康建
 * 修改日期: 2017/05/24
 * 修改时间: 16:17
 */
class Follow extends MY_Controller
{

	public function __construct()
	{
		parent::__construct();
		$this->load->model('api_follow_model','follow');
		$this->load->model('user_model');
		$this->load->model('uinfo_model');
		$this->load->library('tools');
	}
	/**
	 * [relation 关注或取消关注]
	 * @author LiKangJian 2017-05-25
	 * @return [type] [description]
	 */
	public function relation()
	{
		$valid = array('puid','uid','status');
		$params = $this->strCode($this->input->post('data'));
		$params = json_decode($params, true);
		//验证必要参数
		$this->checkParams($valid,$params);
		//请求数据
		$res = $this->follow->relation($params);
		if($res == -1)
		{
			$this->errorOut('000100','不存在的关注关系');
		}else if($res == -2){
			$this->errorOut('000101','对不起，不能重复关注');
		}else if($res == -3){
			$this->errorOut('000102','关注的人或被关注的人不存在');
		}else if($res == 0){
			$this->errorOut('000103','关注失败');
		}else{
			$this->outPut('1');
		}

	}
	/**
	 * [isFollow 查询是否有关注]
	 * @author LiKangJian 2017-06-01
	 * @return boolean [description]
	 */
	public function isFollow()
	{
		$valid = array('puid','uid');
		$params = $this->strCode($this->input->post('data'));
		$params = json_decode($params, true);
		//验证必要参数
		$this->checkParams($valid,$params);
		//请求数据
		$res = $this->follow->isFollow($params);
		$tag = $res ? '1' : '0';
		$this->outPut($tag);
	}
	/**
	 * [getList 关注列表]
	 * @author LiKangJian 2017-05-25
	 * @return [type] [description]
	 */
	public function getList()
	{
		$valid = array('uid');
		$params = $this->strCode($this->input->post('data'));
		$params = json_decode($params, true);
		$params['pageNum'] = isset($params['pageNum']) ? intval($params['pageNum']) : 10;
		$params['page'] = isset($params['page']) ? intval($params['page']) : 1;
		//验证必要参数
		$this->checkParams($valid,$params);
		//请求数据
		$res = $this->follow->getFollowList($params);
		$this->outPut($res);
	}
	
	public function followRelation()
	{
		$data = $this->strCode($this->input->post('data'));
		$data = json_decode($data, true);

		if(!empty($data['puid']) && !empty($data['uid']) && isset($data['status']))
		{
			$status = $data['status'] ? '1' : '0';
			if($data['puid'] == $data['uid'] && $status == '1')
			{
				$result = array(
	                'status'	=>	'0',
	                'msg' 		=> 	'合买无法关注自己',
	                'data' 		=> 	'',
	            );
	            die(json_encode($result));
			}

			$params = array(
				'puid'		=>	$data['puid'],
				'uid'		=>	$data['uid'],
				'follow_status'	=>	$status,
			);

			if($this->follow->followRelation($params))
			{
				$result = array(
	                'status'	=>	'1',
	                'msg' 		=> 	'合买关注成功',
	                'data' 		=> 	'',
	            );
			}
			else
			{
				$result = array(
	                'status'	=>	'0',
	                'msg' 		=> 	'合买关注失败',
	                'data' 		=> 	'',
	            );
			}
		}
		else
		{
			$result = array(
                'status'	=>	'0',
                'msg' 		=> 	'缺少必要参数',
                'data' 		=> 	'',
            );
		}
		die(json_encode($result));
	}

}