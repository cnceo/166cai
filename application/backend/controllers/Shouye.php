<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Shouye extends MY_Controller
{
   
	public function index($action = 'banner')
	{
		$this->check_capacity('6_2_1');
		$info = $this->input->post();
		if($info)
		{
			$this->checkenv($info['env']);
		    $this->check_capacity('6_2_2');
		}
		$this->load->model('model_shouye_img', 'img');
		$this->load->model('model_shouye_link', 'link');
		$data['banner'] = $this->img->getBannerList();
		$data['numtype'] = $this->link->getDataByPosition('numtype');
		$data['jctype'] = $this->link->getDataByPosition('jctype');
		$data['numrm'] = $this->link->getDataByPosition('numrm');
		$data['jcrm'] = $this->link->getDataByPosition('jcrm');
		$linktype = array('numtype', 'jctype', 'numrm', 'jcrm');
		$data['saved'] = $this->input->get('saved') ? 1 : 0;
		$data['notfull'] = $this->input->get('notfull') ? 1 : 0;
		$data['opennew'] = $this->input->get('opennew') ? 1 : 0;
		for ($i = 1; $i <= 4; $i++)
		{
			$data["num".$i] = $this->link->getDataByPosition("num".$i);
			$data["jc".$i] = $this->link->getDataByPosition("jc".$i);
			$linktype[] = "num".$i;
			$linktype[] = "jc".$i;
		}
		if ($info['banner']) {
			$arg = 'banner';
			$par = array('title', 'bgcolor', 'url', 'path', 'priority', 'start_time', 'end_time');
			$ur = '/backend/Shouye';
			$tt = '轮播图';
		}
		if ($arg) {
			foreach ($info[$arg] as $val) {
			    $full = 0;
				foreach ($par as $pa) {
					if (empty($val[$pa])) {
					    if ($full == 0) {
					        $full = 1;
					    } elseif ($full == 2) {
					        $this->redirect($ur.'?notfull=1');
					    }
					}else {
					    if ($full == 1) $this->redirect("/backend/Appconfig/banner/".$platform."?notfull=1");
					    $full = 2;
					}
				}
				$val['position'] = $arg;
				$istData[] = $val;
			}
			$this->img->delByPosition($arg);
			$this->img->insertAllData($istData);
			foreach ($istData as $dt){
				$this->syslog(32, "首页".$tt."更新内容：".$dt['title']."，链接{$dt['url']}，上线期限：{$dt['start_time']}-{$dt['end_time']}");
			}
			$this->redirect($ur.'?saved=1');
		}
			
		$flag = false;
		foreach ($linktype as $lt)
		{
			if ($info[$lt])
			{
				$flag = true;
				foreach ($info[$lt] as $priority => $value)
				{
					if ($value['title'] && $value['url'])
					{
						if ($value['redflag'])
						{
							$value['redflag'] = 1;
						}else
						{
							$value['redflag'] = 0;
						}
						if (empty($data[$lt][$priority]))
						{
							$value['priority'] = $priority;
							$value['position'] = $lt;
							$istData[] = $value;
						}else 
						{
							$this->link->updateData($value, $data[$lt][$priority]['id']);
							if (in_array($lt, array('numtype', 'jctype')))
							{
								$this->syslog(32, "首页资讯更新内容：".$value['title']);
							}
						}
					}else 
					{
						$this->redirect('/backend/Shouye/index/zixun?notfull=1');
					}
				}
				if (!empty($istData))
				{
					foreach ($istData as $dt)
					{
						$this->syslog(32, "首页资讯更新内容：".$dt['title']);
					}
					$this->link->insertAllData($istData);
				}
				if (in_array($lt, array('numtype', 'jctype'))) 
				{
					$this->refreshCache('ln', $lt);
				}
			}
		}
		if ($flag)
		{
			if ($info['numtype'] || $info['jctype'])
			{
				$this->redirect('/backend/Shouye/index/zixun?saved=1');
			}
			else 
			{
				$this->redirect('/backend/Shouye/index/zixun?opennew=1');
			}	
		}
		$data['action'] = $action;
		$this->load->view('shouye/index', $data);
	}
	
	public function onlinezx($type)
	{
		for ($i = 1; $i <= 4; $i++)
		{
			$linktype[] = $type.$i;
		}
		$linktype[] = $type."rm";
		foreach ($linktype as $position)
		{
			$this->refreshCache('ln', $position);
		}
		$this->syslog(32, "首页资讯内容上线成功");
		$this->redirect('/backend/Shouye?saved=1');
	}
	
	private function refreshCache($tb, $position)
	{
		if ($tb == 'ln')
		{
			$this->load->model('model_shouye_link', 'model');
		}
		else 
		{
			$this->load->model('model_shouye_img', 'model');
		}
		$data = $this->model->getDataByPosition($position);
		$this->load->driver('cache', array('adapter' => 'redis'));
		$REDIS = $this->config->item('REDIS');
		$res = unserialize($this->cache->get($REDIS['SHOUYE']));
		$res[$position] = $data;
		$this->cache->save($REDIS['SHOUYE'], serialize($res), 0);
	}
	
	public function zixun()
	{
		$this->load->model('model_shouye_link', 'model');
		$this->load->model('model_shouye_img', 'img');
		$linktype = array('numtype', 'jctype', 'numrm', 'jcrm');
		for ($i = 1; $i <= 4; $i++)
		{
			$linktype[] = "num".$i;
			$linktype[] = "jc".$i;
		}
		foreach ($linktype as $type)
		{
			$data['jingtai'][$type] = $this->model->getDataByPosition($type);
		}
		$this->load->driver('cache', array('adapter' => 'redis'));
		$REDIS = $this->config->item('REDIS');
		$data['winnews'] = unserialize($this->cache->get($REDIS['AWARD_NOTICE']));
		$this->load->view('shouye/zixun', $data);
	}
	
	public function upload($position, $index)
	{
		if (! file_exists ( "../uploads/".$position."/" ))
		{
			mkdir ( "../uploads/".$position."/" );
		}
		
		$config ['upload_path'] = "../uploads/".$position."/";
		$config ['allowed_types'] = 'jpg|png|bmp|jpeg';
		$extension = pathinfo ( $_FILES ['file'] ['name'], PATHINFO_EXTENSION );
		
		$config ['max_size'] = 10240;
		$this->load->library ( 'upload', $config );
		if ($this->upload->do_upload ( 'file' ))
		{
			$data = $this->upload->data ();
			$res = array (
					'name' => $data ['file_name'],
					'index'=> $index,
					'position'=>$position
			);
			exit ( json_encode ( $res ) );
		} else
		{
			$error = $this->upload->display_errors ();
			exit ( $error );
		}
	}
    
    // 中奖墙
    public function zhongjiang()
    {
    	$this->check_capacity('6_2_3');
    	//查询的条件
        $page = intval($this->input->get("p"));
        $page = $page <= 1 ? 1 : $page;
        $searchData = array(
            "title"             =>  $this->input->get("title", TRUE),
            "lname"             =>  $this->input->get("lname", TRUE),
            "status"            =>  $this->input->get("status"),
            "start_time"        =>  $this->input->get("start_time", TRUE),
            "end_time"          =>  $this->input->get("end_time", TRUE),
            'submitter'         =>  $this->input->get("submitter", TRUE),
        );

        if(empty($searchData['start_time']) || empty($searchData['end_time']))
        {
        	$this->filterTime($searchData['start_time'], $searchData['end_time']);
        }

    	$this->load->model('model_shouye_link', 'link');
    	$result = $this->link->list_win($searchData, $page, self::NUM_PER_PAGE);

    	// 分页
        $pageConfig = array(
            "page"     => $page,
            "npp"      => self::NUM_PER_PAGE,
            "allCount" => $result['count']['num']
        );
        $pages = get_pagination($pageConfig);

        $data = array(
            'info'     => $result['data'],
            'pages'      => $pages,
            'count'      => $result['count'],
            'search'     => $searchData,
        );

    	$this->load->view('shouye/zhongjiang', $data);
    }

    public function addWinInfo()
    {
    	$this->check_capacity('6_2_4', true);
    	$postData = $this->input->post(null, true);
    	if(!empty($postData['title']) && !empty($postData['url']) && !empty($postData['content']) && !empty($postData['lname']))
    	{
    		preg_match('/(\d+)$/is', trim($postData['url']), $match);
    		$newsId = $match[1] ? $match[1] : 0;
    		if($newsId)
    		{
    			$info = array(
	    			'title'		=>	$postData['title'],
	    			'newsId'	=>	$newsId,
	    			'url'		=>	$postData['url'],
	    			'content'	=>	$postData['content'],
	    			'lname'		=>	$postData['lname'],
	    			'status'	=>	$postData['status'] ? 1 : 0,
	    			'submitter'	=>	$this->uname,
	    		);
	    		$this->load->model('model_shouye_link', 'link');
	    		$this->link->recodeWinInfo($info);
	    		$this->syslog(32, "新增资讯：" . $info['title'] . '，域名地址：' . $info['url']);
	    		$this->ajaxReturn('y', '新建成功');
    		}
    		else
    		{
    			$this->ajaxReturn('n', '链接地址格式错误');
    		}
    	}
    	else
    	{
    		$this->ajaxReturn('n', '缺少必要参数');
    	}
    }

    public function setTopWin()
    {
    	$this->check_capacity('6_2_4', true);
    	$id = $this->input->get("id", TRUE);
    	$title = $this->input->get("title", TRUE);
    	if($id)
    	{
    		$this->load->model('model_shouye_link', 'link');
    		$this->link->setTopWin($id);
    		$this->syslog(32, "更新资讯 - 置顶：" . $title);
    		$this->ajaxReturn('y', '置顶成功');
    	}
    	else
    	{
    		$this->ajaxReturn('n', '置顶失败');
    	}
    }

    public function setDeleteWin()
    {
    	$this->check_capacity('6_2_4', true);
    	$id = $this->input->get("id", TRUE);
    	$title = $this->input->get("title", TRUE);
    	if($id)
    	{
    		$this->load->model('model_shouye_link', 'link');
    		$this->link->setDeleteWin($id);
    		$this->syslog(32, "更新资讯 - 删除：" . $title);
    		$this->ajaxReturn('y', '删除成功');
    	}
    	else
    	{
    		$this->ajaxReturn('n', '删除失败');
    	}
    }

    public function getWinDetail()
    {
    	$this->check_capacity('6_2_4', true);
    	$id = $this->input->get("id", TRUE);

    	$result = array(
            'status'	=>	'0',
            'msg' 		=> 	'查询失败',
            'data' 		=> 	''
        );

    	if($id)
    	{
    		$this->load->model('model_shouye_link', 'link');
    		$info = $this->link->getWinDetail($id);
    		if(!empty($info))
    		{
    			$result = array(
	                'status'	=>	'1',
	                'msg' 		=> 	'查询成功',
	                'data' 		=> 	$info
	            );
    		}
    	}
    	die(json_encode($result));
    }

    public function modifyWinInfo()
    {
    	$this->check_capacity('6_2_4', true);
    	$postData = $this->input->post(null, true);
    	if(!empty($postData['title']) && !empty($postData['url']) && !empty($postData['content']) && !empty($postData['lname']))
    	{
    		preg_match('/(\d+)$/is', trim($postData['url']), $match);
    		$newsId = $match[1] ? $match[1] : 0;
    		if($newsId)
    		{
    			$info = array(
    				'id'		=>	$postData['id'],
	    			'title'		=>	$postData['title'],
	    			'newsId'	=>	$newsId,
	    			'url'		=>	$postData['url'],
	    			'content'	=>	$postData['content'],
	    			'lname'		=>	$postData['lname'],
	    			'status'	=>	$postData['status'] ? 1 : 0,
	    		);
	    		$this->load->model('model_shouye_link', 'link');
	    		$this->link->updateWinInfo($info);
	    		$this->syslog(32, "更新资讯：" . $info['title'] . '，域名地址：' . $info['url']);
	    		$this->ajaxReturn('y', '修改成功');
    		}
    		else
    		{
    			$this->ajaxReturn('n', '链接地址格式错误');
    		}
    	}
    	else
    	{
    		$this->ajaxReturn('n', '缺少必要参数');
    	}
    }
}
