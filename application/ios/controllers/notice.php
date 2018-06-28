<?php

/*
 * IOS 网站公告
 * @date:2015-05-15
 */

 class Notice extends MY_Controller 
{
	public function __construct() 
	{
		parent::__construct();
		$this->load->library('tools');
		$this->load->library('comm');
		$this->load->model('Notice_Model');
	}

	/*
	 * IOS 网站公告首页
	 * @date:2015-05-15
	 */
	public function index()
	{	
		//默认显示 推广消息
		$msgType = 0;
		$cpage = 1;
		$psize = 15;
		//根据相关条件查询出所需的网站公告
        $result = $this->Notice_Model->noticeList(array('status' => 1, 'msgType' => $msgType), $cpage, $psize);
        //总条数
        $total = 0;
        //总页数
        $page_num = 0;
        if ($result) 
        {
            $total = $this->Notice_Model->noticeCount(array('status' => 1, 'msgType' => $msgType));
            $page_num = ceil($total / $psize);
           	foreach ($result as $key => $items) 
           	{
           		$result[$key]['url'] = $this->getMsgType($msgType, $items);
           	}
        }

        // var_dump($page_num);die;
        $this->load->view('notice/index', array(
            'result' => $result,
            'total' => $total,
            'page_num' => $page_num
                )
        );
	}

	/*
	 * IOS 消息中心跳转地址
	 * @date:2016-01-26
	 */
	public function getMsgType($msgType, $items)
	{
		if(!$msgType)
		{
			// 网站公告
			$url = $this->config->item('pages_url') . 'ios/notice/detail/' . $items['id'];
		}
		else
		{
			// 推广活动
			$url = $items['url'];
		}
		return $url;
	}

	/*
	 * IOS 网站公告首页
	 * @date:2015-05-15
	 */
	public function detail($id)
	{	
		$detailInfo = $this->Notice_Model->getInfoById($id);
		$this->load->view('notice/detail', array(
            'pageTitle' => $detailInfo['title'],
            'result' => $detailInfo
                )
        );
	}

	/*
	 * IOS 网站公告首页 AJAX
	 * @date:2015-05-15
	 */
	public function ajaxNotice()
	{	
		$psize = 15;
		$cpage = $this->input->post('cpage', true);
		$msgType = $this->input->post('msgType', true);
		$result = $this->Notice_Model->noticeList(array('status' => 1, 'msgType' => $msgType), $cpage, $psize);
		if($result)
		{
			foreach ($result as $key => $items)
           	{
           		$result[$key]['url'] = $this->getMsgType($msgType, $items);
           	}
			$result = array(
				'status' => '1',
				'msg' => '加载中',
				'data' => $this->load->view('notice/ajaxNotice', array('result' => $result), true)
			);
		}
		else
		{
			$result = array(
				'status' => '0',
				'msg' => '无更多公告信息',
				'data' => ''
			);
		}
		die(json_encode($result));
	}

	/*
	 * APP 通知中心
	 * @date:2016-07-06
	 */
	public function message($token)
	{
		// 检查参数
		$data = $this->strCode(urldecode($token));
		$data = json_decode($data, true);

		if($data['uid'] != $this->uid)
		{
			echo "参数错误！";
			die;
		}

		// 查询用户基本信息
		$this->load->model('user_model');
		$uinfo = $this->user_model->getUserInfo($data['uid']);
		// var_dump($uinfo);die;
		$data = array(
			'msg_send' => (isset($uinfo['msg_send']) && ($uinfo['msg_send'] & 1)) ? true : false,
			'email_send' => (isset($uinfo['msg_send']) && ($uinfo['msg_send'] & 2)) ? true : false,
			'win_prize' => (isset($uinfo['msg_send']) && ($uinfo['msg_send'] & 4) != 0) ? false : true,
                        'chase_prize' => (isset($uinfo['msg_send']) && ($uinfo['msg_send'] & 8) != 0) ? false : true,
                        'gendan_prize' => (isset($uinfo['msg_send']) && ($uinfo['msg_send'] & 16) != 0) ? false : true,
		);
		$this->load->view('notice/message', $data);
	}

	/*
	 * APP 通知中心 Ajax
	 * @date:2016-07-06
	 */
	public function modifyMessage()
	{
		$ctype = $this->input->post('ctype', true);
		$status = $this->input->post('status', true);

		$status = $status ? '1' : '0';

		if(!empty($this->uid))
		{
			switch ($ctype)
			{
				case 'msg_send':
					$result = $this->setMsgSend($this->uid, $status, 'phone');
					break;
				case 'email_send':
					$result = $this->setMsgSend($this->uid, $status, 'email');
					break;
				case 'win_prize':
					$result = $this->setMsgSend($this->uid, $status, 'win_prize');
					break;
				case 'chase_prize':
					$result = $this->setMsgSend($this->uid, $status, 'chase_prize');
					break;
				case 'gendan_prize':
					$result = $this->setMsgSend($this->uid, $status, 'gendan_prize');
					break;
				default:
					$result = array(
						'status' => '0',
						'msg' => '修改信息错误'
					);
					break;
			}
			
		}
		else
		{
			$result = array(
				'status' => '0',
				'msg' => '登录信息失效，请重新登录'
			);
		}
		die(json_encode($result));
	}

	/*
	 * APP 通知中心 - 修改短信通知
	 * @date:2016-07-06
	 */
	public function setMsgSend($uid, $status, $ctype)
	{
		$this->load->model('user_model');
		$uinfo = $this->user_model->getUserInfo($uid);

		if(!empty($uinfo))
		{
			if($ctype == 'email' && empty($uinfo['email']))
			{
				$result = array(
					'status' => '0',
					'msg' => '您尚未绑定邮箱，请使用最新版本绑定邮箱'
				);
				return $result;
			}

			// 组装参数
			$postData = array(
				'uid' => $uid,
				'msg_send' => $status,
				'type' => $ctype
			);

			// 初始化订单信息
	        if(ENVIRONMENT === 'checkout')
			{
				$postUrl = $this->config->item('cp_host');
				$postData['HOST'] = $this->config->item('domain');
			}
			else
			{
				// $postUrl = $this->config->item('pages_url');
				$postUrl = $this->config->item('protocol') . $this->config->item('pages_url');
			}

			$postStatus = $this->tools->request($postUrl . 'api/user/updateMsgsend', $postData);
			$postStatus = json_decode($postStatus, true);

			if($postStatus['codes'] == '200')
			{
				$result = array(
					'status' => '1',
					'msg' => '修改成功',
					'data' => $postData['msg_send']?true:false
				);
			}
			else
			{
				$result = array(
					'status' => '0',
					'msg' => '修改失败'
				);
			}
		}
		else
		{
			$result = array(
				'status' => '1',
				'msg' => '修改成功',
				'data' => $postData['msg_send']?true:false
			);
		}
		return $result;
	}

	/*
	 * APP 通知中心 - 跳转指定彩种投注页
	 * @date:2016-07-27
	 */
	public function directBet($lid)
	{
		$this->load->library('BetCnName');
		$enName = BetCnName::$BetEgName[$lid];

		if(!empty($enName))
		{
			$info = array(
				'lid'	=>	$lid,
				'name'	=>	$enName
			);
		}
		else
		{
			$info = array(
				'lid'	=>	'51',
				'name'	=>	'ssq'
			);
		}
		$this->load->view('notice/directBet', $info);
	}
}