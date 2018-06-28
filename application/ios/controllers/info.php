<?php

/*
 * 资讯
 * @date:2016-02-03
 */

 class Info extends MY_Controller 
{
	public function __construct() 
	{
		parent::__construct();
		$this->load->model('info_model');
		$this->category = $this->info_model->getCategory();
	}

	/*
	 * 资讯详情
	 * @date:2016-09-06
	 */
	public function detail($id = 0)
	{	
		$data = $this->info_model->getInfoDetail(intval($id));

		if(empty($data))
		{
			$this->redirect('/ios/error');
		}

		$lid = $this->category[$data['category_id']]['lid'];
		$this->load->model('lottery_model', 'Lottery');

		// 更新阅读量
		$this->info_model->updateReadNum(intval($id));
		$additions = (isset($data['additions']) && $data['additions'] != 1) ? $data['additions'] : '';
		$ios = false;
		if( strpos($_SERVER['HTTP_USER_AGENT'], '166cai/iOS') !== FALSE ) $ios = true;
		$info = array(
			'category'	=>	$this->category[$data['category_id']]['name'],
			'id'		=>	intval($id),
			'title'		=>	$data['title'],
			'content'	=>	ParseUrl($data['content']),
			'date'		=>	$data['show_time'],
			'num'		=>	($data['num'] > 9999) ? '9999' : $data['num'],
			'likeNum'	=>	($data['likeNum'] > 9999) ? '9999' : $data['likeNum'],
			'comNum'	=>	($data['comNum'] > 9999) ? '9999' : $data['comNum'],
			'enName'	=>	$lid ? $this->Lottery->getEnName($lid) : '',
			'lid'		=>	$lid ? $lid : $additions,
			'additions' =>  $data['additions'],
			"imgurl" 	=>  $this->config->item('protocol') . $this->config->item('pages_url') . "caipiaoimg/static/images/app-icon-new.png",
			'ios'   	=>  $ios
		);

		// 链接地址替换
		$info['content'] = preg_replace('/(?<=\bc[n|om])(\/info\/\w+)/is', '/ios/info/detail', $info['content']);
		
		$this->load->view('info/detail', $info);
	}

}