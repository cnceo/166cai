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
		$this->load->model('info_comments_model');
		$this->load->model('user_model', 'User');
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
			$this->redirect('/app/error');
		}

		$lid = $this->category[$data['category_id']]['lid'];
		$this->load->model('lottery_model', 'Lottery');

		// 更新阅读量
		$this->info_comments_model->updateReadNum(intval($id));
		$additions = (isset($data['additions']) && $data['additions'] != 1) ? $data['additions'] : '';
		$android = false;
                $content = htmlspecialchars_decode($data['content']);
                $search = array(" ","　","\n","\r","\t");
                $replace = array("","","","","");
                $content = str_replace($search, $replace, strip_tags($content));
		if( strpos($_SERVER['HTTP_USER_AGENT'], '2345caipiao/android') !== FALSE ) $android = true;
		$info = array(
			'category'	=>	$this->category[$data['category_id']]['name'],
			'id'		=>	intval($id),
			'title'		=>	$data['title'],
			'content'	=>	ParseUrl($data['content']),
                        'cutContent'	=>      mb_substr($content, 0, 25).'...',
			'date'		=>	$data['show_time'],
			'num'		=>	($data['num'] > 9999) ? '9999' : $data['num'],
			'likeNum'	=>	($data['likeNum'] > 9999) ? '9999' : $data['likeNum'],
			'comNum'	=>	($data['comNum'] > 9999) ? '9999' : $data['comNum'],
			'lid'		=>	$lid ? $lid : $additions,
			'additions' =>  $data['additions'],
			"imgurl" 	=> $this->config->item('protocol') . $this->config->item('pages_url') . "caipiaoimg/static/images/app-icon-new.png",
			'enName'	=>	$lid ? $this->Lottery->getEnName($lid) : '',
			'isUserLike'=>	$this->isUserLike(intval($id), $this->uid),
			'banner' 	=> 	$this->getBanner($data['category_id']),
			'android'   =>  $android
		);
		$this->load->view('info/detail', $info);
	}
	
	private function getBanner($category_id)
	{
		// 版本信息
		$versionInfo = $this->getUserAgentInfo();
		$bannerInfo = array();
		// 获取弹窗信息缓存
		$this->load->model('cache_model','Cache');
		$info = $this->Cache->getPreloadInfo($platform = 'android', 'info');
		$detail = $info[$category_id];
		if(!empty($detail) && (!empty($detail['webUrl']) || in_array($detail['appAction'], array('bet', 'email')))) {
			if ($detail['appAction'] == 'email') {
				if (empty($this->uid)) {
					$detail['appAction'] = 'notlogin';
				}elseif ($versionInfo['appVersionCode'] < '11') {
					$detail['appAction'] = 'unsupport';
				}elseif (!empty($this->uinfo['email'])) {
					$detail['appAction'] = 'ignore';
				}
			}
			$bannerInfo = array(
				'imgUrl' => (strpos($detail['imgUrl'], 'http') !== FALSE) ? $detail['imgUrl'] : $this->config->item('protocol') . $detail['imgUrl'],
				'webUrl' => $detail['webUrl'],
				'appAction' => $detail['appAction'],
				'tlid' => $detail['tlid'],
				'enName' => $this->Lottery->getEnName($detail['tlid'])
			);
		}
		return $bannerInfo;
	}

	/*
	 * 获取资讯详情评论
	 * @date:2017-08-01
	 */
	public function getComment()
	{
		$id = $this->input->get("id", true);

		$page = intval($this->input->get("page", true));
		$page = $page ? $page : 1;

		$number = intval($this->input->get("number", true));
		$number = $number ? $number : 10;

		$uid = $this->uid ? $this->uid : 0;
		$info = $this->info_comments_model->getComment(intval($id), $uid, $page, $number);

		$comments = array();
		if(!empty($info))
		{
			foreach ($info as $key => $items) 
			{
				$data = $this->getComments($items, $uid);
				array_push($comments, $data);
			}
		}

		$result = array(
			'status' =>	'200',
			'msg'	 =>	'通信成功',
			'data'   => $comments
		);
		echo json_encode($result);
	}

	public function getDateFormat($date = '')
	{
		$msg = '';
		if(date('Ymd') == date('Ymd', strtotime($date)))
		{
			$msg .= '今天 ' . date('H:i:s', strtotime($date));
		}
		elseif(date('Ymd', strtotime("-1 day")) == date('Ymd', strtotime($date)))
		{
			$msg .= '昨天 ' . date('H:i:s', strtotime($date));
		}
		else
		{
			$msg .= date('m/d H:i:s', strtotime($date));
		}
		return $msg;
	}

	/*
	 * 提交资讯详情评论
	 * @date:2017-08-01
	 */
	public function postComment()
	{
		$id = $this->input->post("id", true);
		$content = $this->input->post("content", true);
		$commentId = $this->input->post("commentId", true);
                
		if(empty($this->uid))
		{
			$result = array(
				'status' =>	'300',
				'msg'	 =>	'用户未登录',
				'data'   => ''
			);
			die(json_encode($result));
		}

		if(empty($content))
		{
			$result = array(
				'status' =>	'400',
				'msg'	 =>	'评论内容不能为空',
				'data'   => ''
			);
			die(json_encode($result));
		}

		$this->load->library('comm');
		if($this->comm->abslength($content) > 250)
		{
			$result = array(
				'status' =>	'400',
				'msg'	 =>	'内容过长，请重新输入',
				'data'   => ''
			);
			die(json_encode($result));
		}

		if(intval($id) == 0)
		{
			$result = array(
				'status' =>	'400',
				'msg'	 =>	'提交参数错误，请稍后再试',
				'data'   => ''
			);
			die(json_encode($result));
		}

        $this->load->model('user_model');
        $res = $this->user_model->getCommentStatus($this->uid);
        if($res['uncomment'] == 1)
        {
            $result = array(
                'status' =>	'400',
                'msg'    => '您因不当发言或敏感信息被禁止评论',
                'data'   => ''
            );
            die(json_encode($result));                    
        }
		// 提交评论
		$comments = array(
			'newsId'	=>	$id,
			'uid'		=>	$this->uid,
			'content'	=>	$content,
			'tid'		=>	$commentId ? $commentId : 0
		);
		$info = $this->info_comments_model->postComment($comments);
		if($info)
		{
			$data = $this->getComments($info);
			
			$result = array(
				'status' =>	'200',
				'msg'	 =>	'评论成功',
				'data'   => $data
			);
		}
		else
		{
			$result = array(
				'status' =>	'400',
				'msg'	 =>	'评论失败',
				'data'   => ''
			);
		}
		die(json_encode($result));
	}

	public function isUserLike($newsId, $uid = 0)
	{
		$isLike = 0;
		if($uid)
		{
			$likesInfo = $this->info_comments_model->getCommentLike($newsId, $uid);
			if(!empty($likesInfo) && $likesInfo['isLike'])
			{
				$isLike = 1;
			}
		}
		return $isLike;
	}

	public function getComments($info, $uid = 0)
	{
		// 版本判断
		$appVersion = $this->getUserAgentInfo();

		// 回复状态
		$replyStatus = '0';
		if(!empty($info['tuid']) && !empty($info['tcontent']))
		{
			$replyStatus = '1';
			// 当前用户忽略删除、审核失败等异常
			if($uid > 0 && $info['tuid'] == $uid)
			{
				$replyStatus = '1';
			}
			elseif($info['tdelete'])
			{
				$replyStatus = '2';
			}
		}

		$reply = array(
			'status'	=>	$replyStatus,
			'uname'		=>	($replyStatus == '1' && $info['tuname']) ? $info['tuname'] : '',
			'content'	=>	($replyStatus == '1' && $info['tcontent']) ? $info['tcontent'] : '',
			'commentId'	=>	$info['tid'] ? $info['tid'] : $info['pid'],
			'checked'	=>	($info['tstatus'] == '1' && $appVersion['appVersionCode'] >= 40000) ? '1' : '0',
			'lv'		=>	$this->getUserGrade($info['tuid']),
		);

		$data = array(
			'commentId'	=>	$info['id'],
			'checked'	=>	($info['status'] == '1' && $appVersion['appVersionCode'] >= 40000) ? '1' : '0',
			'uname'		=>	$info['uname'],
			'content'	=>	$info['content'],
			'date' 		=> 	$this->getDateFormat($info['created']),
			'floor'		=>	$info['floor'] ? intval($info['floor']) : 0,
			'isAdmin'	=>	($info['uid'] == '1') ? 1 : 0,
			'reply' 	=> 	$replyStatus ? $reply : "",
			'headimgurl'	=>	$info['headimgurl'],
			'lv'		=>	$this->getUserGrade($info['uid']),
		);

		return $data;
	}

	// 获取用户等级
	public function getUserGrade($uid)
	{
		$uinfo = $this->User->getUserInfo($uid);
		return $uinfo['grade'] ? $uinfo['grade'] : '1';
	}
}
