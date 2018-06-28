<?php

/**
 * Copyright (c) 2015,上海二三四五网络科技有限公司.
 * 摘    要:
 * 作    者: 刁寿钧
 * 修改日期: 2016/3/14
 * 修改时间: 19:05
 */
defined('BASEPATH') or exit('No direct script access allowed');

/**
 * @property $model_info       Model_Info
 * @property $model_info_crawl Model_Info_Crawl
 */
class Info extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->msg_text_cfg = $this->config->item('msg_text_cfg');
        $this->load->model('model_info');
        $this->load->model('model_info_crawl');
        $this->config->load('caipiao');
        foreach ($this->config->item('caipiao_all_cfg') as $key => $value)
        {
            $this->$key = $value;
        }
    }

    private $categoryUrls = array(
        1 => 'csxw',
        2 => 'ssq',
        3 => 'qtfc',
        4 => 'dlt',
        5 => 'qttc',
        6 => 'jczq',
        7 => 'sfc',
        8 => 'jclq',
        9 => 'zjtjzq',
        10 => 'zjtjlq',
    );

    public function infoList()
    {
//        $this->check_capacity("20_1");
        $this->load->view("info/info");
    }

    public function image()
    {
//        $this->check_capacity("20_1");
        $this->load->view("info/image");
    }

    public function center()
    {
        $this->check_capacity("6_3_2");
        $page = intval($this->input->get("p"));
        $page = $page <= 1 ? 1 : $page;
        $searchData = array(
            "title"      => str_hsc($this->input->get("title", TRUE)),
            "name"       => ($this->input->get("name", TRUE) === false) ? '' : $this->input->get("name", TRUE),
    		"start_time" => ($this->input->get("start_time", TRUE) === false) ? '' : $this->input->get("start_time", TRUE),
    		"end_time"   => ($this->input->get("end_time", TRUE) === false) ? '' : $this->input->get("end_time", TRUE),
    		"source"     => ($this->input->get("source", TRUE) === false) ? '' : $this->input->get("source", TRUE),
    		"isshow"     => ($this->input->get("isshow", TRUE) === false) ? '' : $this->input->get("isshow", TRUE),
    		"category"   => ($this->input->get("category", TRUE) === false) ? '' : $this->input->get("category", TRUE),
    		"submitter"   => ($this->input->get("submitter", TRUE) === false) ? '' : $this->input->get("submitter", TRUE),
        );
        if (empty($searchData['start_time'])) {
            $searchData['start_time'] = date('Y-m-d 00:00:00');
        }
        if (empty($searchData['end_time'])) {
            $searchData['end_time'] = date('Y-m-d 23:59:59');
        }
        $this->filterTime($searchData['start_time'], $searchData['end_time']);
        $result = $this->model_info->list_notice($searchData, $page, self::NUM_PER_PAGE);

        foreach ($result[0] as $key => $value)
        {
            if (mb_strlen($value['content']) > 30)
            {
                $result[0][$key]['content'] = mb_substr($value['content'], 0, 30) . "...";
            }
            $result[0][$key]['status'] = $value['status'] == 0 ? "否" : "是";
        }

        $pageConfig = array(
            "page"     => $page,
            "npp"      => self::NUM_PER_PAGE,
            "allCount" => $result[1]
        );
        $pages = get_pagination($pageConfig);
        $categoryList = $this->model_info->getCategoryList();
        $sourceList = $this->model_info->getSourceList();
        $pageInfo = array(
            "notices"      => $result[0],
            "pages"        => $pages,
            "search"       => $searchData,
            "categoryList" => $categoryList,
            "sourceList" => $sourceList,
        );
        $this->load->view("info/center", $pageInfo);
    }

    public function crawl()
    {
        $this->check_capacity("6_3_1");
        $categoryId = $this->input->get('cid', TRUE);
        if (empty($categoryId) || ! is_numeric($categoryId))
        {
            $categoryId = 1;
        }
        $postData = $this->input->post();
        if ($postData) {
            $this->check_capacity("6_3_5", true);
        	$postData = $this->input->post(null, true);
        	$id = intval($postData['updateId']);
        	$data['is_open'] = intval($postData['is_open']);
        	$data['source'] = $postData['source'];
        	$data['url'] = $postData['url'];
        	$row = $this->model_info_crawl->update($id, $data);
        	if ($row === false)
        	{
        		return $this->ajaxReturn('n', '修改失败！');
        	}
        	return $this->ajaxReturn('y', '修改成功！');
        }
        $sourceList = $this->model_info_crawl->getSource();
        $configList = $this->model_info_crawl->queryCategoryConfig($categoryId);
        $categoryList = $this->model_info_crawl->getCategoryList();

        $this->load->view("info/crawl", compact('configList', 'categoryList', 'categoryId', 'sourceList'));
    }

    public function nba()
    {
        $this->check_capacity("6_3_3");
    	$this->load->driver('cache', array('adapter' => 'redis'));
    	$REDIS = $this->config->item('REDIS');
    	$this->load->model('model_nba');
    	$info = $this->input->post('info');
    	if (!empty($info)) {
    		foreach ($info as $s => $value) {
    			foreach ($value as $v) {
    				$this->model_nba->updateNba(array('priority' => $v['priority']), $v['id']);
    			}
    		}
    		$this->refreshCache('nba');
    	}
    	$sqArr = array(1 => '西南', 2 => '太平洋', 3 => '西北', 4 => '大西洋', 5 => '东南', 6 => '中部');
    	$data = $this->model_nba->getAllData();
        $this->load->view("info/nba", array('sqArr' => $sqArr, 'data' => $data));
    }
    
    private function refreshCache($type)
    {
    	$this->load->driver('cache', array('adapter' => 'redis'));
    	$REDIS = $this->config->item('REDIS');
    	switch ($type) {
    		case 'nba':
    			$this->load->model('model_nba');
    			$data = $this->model_nba->getAll();
    			$this->cache->save($REDIS['NBA'], serialize($data), 0);
    			break;
    		case 'infobanner':
    			$this->load->model('model_shouye_img', 'model');
    			$data = $this->model->getDataByPosition('infobanner');
    			$res = unserialize($this->cache->get($REDIS['SHOUYE']));
    			$res['infobanner'] = $data;
    			$this->cache->save($REDIS['SHOUYE'], serialize($res), 0);
    			break;
    		case 'info':
    			$data['1'] = array(
    				'cname' => '彩市新闻',
    				'ename' => 'csxw',
    				'content' => $this->model_info->getIndexByCategory(1, true),
    				'xg' => $this->model_info->getIndexByCategory(1)
    			);
    			$data['2'] = array(
    				'cname' => '双色球',
    				'ename' => 'ssq',
    				'content' => $this->model_info->getIndexByCategory(2, true),
    				'xg' => $this->model_info->getIndexByCategory(2)
    			);
    			$data['3'] = array(
    				'cname' => '其他福彩',
    				'ename' => 'qtfc',
    				'content' => $this->model_info->getIndexByCategory(3, true),
    				'xg' => $this->model_info->getIndexByCategory(3)
    			);
    			$data['4'] = array(
    				'cname' => '大乐透',
    				'ename' => 'dlt',
    				'content' => $this->model_info->getIndexByCategory(4, true),
    				'xg' => $this->model_info->getIndexByCategory(4)
    			);
    			$data['5'] = array(
    				'cname' => '其他体彩',
    				'ename' => 'qttc',
    				'content' => $this->model_info->getIndexByCategory(5, true),
    				'xg' => $this->model_info->getIndexByCategory(5)
    			);
    			$data['6'] = array(
    				'cname' => '竞彩足球',
    				'ename' => 'jczq',
    				'content' => $this->model_info->getIndexByCategory(6, true),
    				'xg' => $this->model_info->getIndexByCategory(6)
    			);
    			$data['7'] = array(
    				'cname' => '胜负彩',
    				'ename' => 'sfc',
    				'content' => $this->model_info->getIndexByCategory(7, true),
    				'xg' => $this->model_info->getIndexByCategory(7)
    			);
    			$data['8'] = array(
    				'cname' => '竞彩篮球',
    				'ename' => 'jclq',
    				'content' => $this->model_info->getIndexByCategory(8, true),
    				'xg' => $this->model_info->getIndexByCategory(8)
    			);
    			$data['9'] = array(
    				'ename' => 'zjtj',
    				'content' => $this->model_info->getRecommed()
    			);
    			$res = unserialize($this->cache->get($REDIS['SHOUYE']));
    			$res['infolist'] = $data;
    			$this->cache->save($REDIS['SHOUYE'], serialize($res), 0);
    			break;
    	}
    }
    
    public function nbaedit($team)
    {
        $this->check_capacity("6_3_7");
    	$post = $this->input->post(null, true);
    	$this->load->model('model_injury');
    	$this->load->model('model_nba');
    	if (!empty($post['submit'])) {
    		$this->model_injury->delByTeam($team);
    		if (!empty($post['info'])) {
    			$this->model_injury->insertAllData($post['info']);
    		}
    		$this->refreshCache('nba');
    	}
    	$teamName = $this->model_nba->getDataById($team);
    	$data = $this->model_injury->getDataByTeam($team);
    	$this->load->view("info/nbaedit", array('data' => $data, 'team' => $team, 'teamName' => $teamName['team']));
    }

    /**
     * 参    数：无
     * 作    者：diaosj
     * 功    能：添加和更新页面
     * 修改日期：2014.11.05
     */
    public function add_update()
    {
        $this->check_capacity("6_3_6");
    	$searchData = array(
    			"title"      => str_hsc($this->input->get("title", TRUE)),
    			"name"       => ($this->input->get("name", TRUE) === false) ? '' : $this->input->get("name", TRUE),
    			"start_time" => ($this->input->get("start_time", TRUE) === false) ? '' : $this->input->get("start_time", TRUE),
    			"end_time"   => ($this->input->get("end_time", TRUE) === false) ? '' : $this->input->get("end_time", TRUE),
    			"source"     => ($this->input->get("source", TRUE) === false) ? '' : $this->input->get("source", TRUE),
    			"isshow"     => ($this->input->get("isshow", TRUE) === false) ? '' : $this->input->get("isshow", TRUE),
    			"category"   => ($this->input->get("category", TRUE) === false) ? '' : $this->input->get("category", TRUE),
    			"submitter"   => ($this->input->get("submitter", TRUE) === false) ? '' : $this->input->get("submitter", TRUE),
    	);
        // 彩市新闻投注彩种
        $lotteryList = array();
        foreach ($this->caipiao_cfg as $lid => $items) 
        {
            $lotteryList[$lid] = $items['name'];
        }
        $lotteryList[1] = '分享好友';
        $pageInfo = array();
        $id = intval($this->input->get("id"));
        if ($id > 0)
        {
            $result = $this->model_info->get_notice_by_id($id);
            $pageInfo['notice'] = $result;
        }
        $categoryList = $this->model_info->getCategoryList();
        $pageInfo['categoryList'] = $categoryList;
        $pageInfo['search'] = $searchData;
        $pageInfo['lotteryList'] = $lotteryList;
        $this->load->view("info/add", $pageInfo);
    }

    /**
     * 参    数：无
     * 作    者：diaosj
     * 功    能：添加更新操作
     * 修改日期：2014.11.05
     */
    public function do_add_update()
    {
        $this->check_capacity("6_3_6", true);
    	$submitterArr = array(
    		8 => '美琪',
    		17 => '二狗',
    		18 => '大平',
    		19 => '安妮',
    		20 => '扯蛋哥',
    		25 => '左边锋'
    	);
        $addData = array(
            "title"       => str_hsc($this->input->post("title", TRUE)),
            "content"     => str_hsc($this->input->post("content")),
            "is_show"     => intval($this->input->post("status")),
            "weight"      => intval($this->input->post("weight")),
            "category_id" => intval($this->input->post("category")),
        );
        $addData['platform'] = 0;
        $lotteryBet = $this->input->post("lotteryBet");
        $addData['additions'] = (intval($lotteryBet) && $addData['category_id'] == '1') ? intval($lotteryBet) : '';
        if ($this->input->post("platform0")) $addData['platform'] += 1;
        if ($this->input->post("platform1")) $addData['platform'] += 2;
        if ($this->input->post("platform2")) $addData['platform'] += 4;
        if ($this->input->post("platform3")) $addData['platform'] += 8;
        $id = intval($this->input->post("id"));
        if ($id > 0)
        {
            $info = $this->model_info->get_notice_by_id($id);
            if (intval($this->input->post("status")) == 1 && ($info['show_time'] == '0000-00-00 00:00:00') ) {
                $addData['show_time'] = date("Y-m-d H:i:s");
            }
            $result = $this->model_info->update($addData, $id);
        }
        else
        {
        	if (array_key_exists($this->uid, $submitterArr) && $addData['category_id'] >= 9) {
        		$addData['submitter'] = $submitterArr[$this->uid];
        	}else {
        		$addData['submitter'] = $this->uname;
        	}
            if (intval($this->input->post("status")) == 1) {
                $addData['show_time'] = date("Y-m-d H:i:s");
            }
            $addData['submitter_id'] = $this->uid;
            $addData['source_id'] = 5;//（本期资讯详情页中，新建的资讯，来源展示为「转载」。）
            $addData['likeNum'] = rand(0, 200);
            $addData['num'] = $addData['likeNum'] + rand(50, 100);
            $result = $this->model_info->add($addData);
        }
        if ($result > 0)
        {
        	$this->refreshCache('info');
            if ($id > 0)
            {
                $this->syslog(8, "更新资讯：{$addData['title']}");
            }
            else
            {
                $this->syslog(8, "新建资讯：{$addData['title']}");
            }
            $this->ajaxReturn('y', "恭喜你,操作成功");
        }
        else
        {
            $this->ajaxReturn('n', "对不起,操作失败");
        }
    }

    /**
     * 参    数：无
     * 作    者：diaosj
     * 功    能：上传图片
     * 修改日期：2014.11.05
     */
    public function upload()
    {
        $config['upload_path'] = BASEPATH . '/../uploads/info/';
        $config['allowed_types'] = 'gif|jpg|png';
        $config['max_size'] = 500;
        $config['max_width'] = 1024;
        $config['max_height'] = 768;
        $config['file_name'] = time() . rand(1, 1000);
        $this->load->library('upload', $config);
        echo $config['upload_path'];
        if ( ! is_dir($config['upload_path']))
        {
            mkdir($config['upload_path'], 0777, TRUE);
        }
        if ( ! $this->upload->do_upload("imgFile"))
        {
            $error = array(
                'error'   => 1,
                "message" => $this->upload->display_errors()
            );
        }
        else
        {
            $info = $this->upload->data();
            $error = array(
                'error' => 0,
                "url"   => "/uploads/info/" . $info['file_name']
            );
        }
        echo json_encode($error);
    }

    /**
     * 参    数：无
     * 作    者：diaosj
     * 功    能：资讯预览
     * 修改日期：2014.11.05
     */
    public function notice_view()
    {
        $this->check_capacity("6_3_6");
        $id = intval($this->input->get("id"));
        $result = $this->model_info->get_notice_by_id($id);
        $pageInfo = array(
            "notice" => $result
        );
        $this->load->view("info/preview", $pageInfo);

    }

    /**
     * 参    数：无
     * 作    者：diaosj
     * 功    能：置顶
     * 修改日期：2015.02.09
     */
    public function setTop()
    {
        $this->check_capacity("6_3_6", true);
        $id = intval($this->input->get("id"));
        $notice = $this->model_info->get_notice_by_id($id);
        if ( ! empty($notice))
        {
        	$this->refreshCache('info');
            $isTop = $notice['is_top'] ? 0 : 1;
            $result = $this->model_info->update(array("is_top" => $isTop), $id);
            if ($result > 0)
            {
                $this->syslog(8, "置顶资讯：{$id}");
                $this->ajaxReturn('y', "恭喜你,操作成功", array("is_top" => $isTop));
            }
            else
            {
                $this->ajaxReturn('n', "对不起,操作失败");
            }
        }
        else
        {
            $this->ajaxReturn('n', "资讯不存在");
        }
    }

    public function delete()
    {
        $this->check_capacity("6_3_6", true);
        $id = intval($this->input->get("id"));
        $notice = $this->model_info->get_notice_by_id($id);
        if ( ! empty($notice))
        {
            $result = $this->model_info->delete($id);
            if ($result > 0)
            {
            	$this->refreshCache('info');
                $this->ajaxReturn('y', "恭喜你,操作成功");
            }
            else
            {
                $this->ajaxReturn('n', "对不起,操作失败");
            }
        }
        else
        {
            $this->ajaxReturn('n', "资讯不存在");
        }
    }
    
    public function banner()
    {
        $this->check_capacity("6_3_4");
    	$info = $this->input->post();
    	$this->load->model('model_shouye_img', 'img');
    	if ($info['banner'])
    	{
    	    $this->check_capacity("6_3_8");
    		foreach ($info['banner'] as $banner)
    		{
    			if ($banner['title'] && $banner['url'] && $banner['path'] && $banner['priority'])
    			{
    				$banner['position'] = 'infobanner';
    				$istData[] = $banner;
    			}elseif (!(empty($banner['title']) && empty($banner['url']) && empty($banner['path'])))
    			{
    				$this->redirect('/backend/Info/banner?notfull=1');
    			}
    		}
    		$this->img->delByPosition('infobanner');
    		$this->img->insertAllData($istData);
    		$this->refreshCache('infobanner');
    		foreach ($istData as $dt)
    		{
    			$this->syslog(32, "资讯轮播图更新");
    		}
    		$this->redirect('/backend/Info/banner?saved=1');
    	}
    	$data['banner'] = $this->img->getListByPosition('infobanner');
    	$this->load->view("info/banner", $data);
    }
    
    public function uploadbanner($index)
    {
    	if (! file_exists ( "../uploads/infobanner/" ))
    	{
    		mkdir ( "../uploads/infobanner/" );
    	}
    	
    	$config ['upload_path'] = "../uploads/infobanner/";
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
    		);
    		exit ( json_encode ( $res ) );
    	} else
    	{
    		$error = $this->upload->display_errors ();
    		exit ( $error );
    	}
    }

    // 评论管理
    public function comments()
    {
        $this->check_capacity("6_8_1");
        // 查询条件
        $page = intval($this->input->get("p"));
        $page = $page <= 1 ? 1 : $page;
        $searchData = array(
            "uname"      => ($this->input->get("uname", TRUE) === false) ? '' : $this->input->get("uname", TRUE),
            "title"      => str_hsc($this->input->get("title", TRUE)),
            "number"     => ($this->input->get("number", TRUE) === false) ? '0' : '1',
            "word"       => ($this->input->get("word", TRUE) === false) ? '0' : '1',
            "chinesenumer" => ($this->input->get("chinesenumer", TRUE) === false) ? '0' : '1',
            "start_time" => ($this->input->get("start_time", TRUE) === false) ? '' : $this->input->get("start_time", TRUE),
            "end_time"   => ($this->input->get("end_time", TRUE) === false) ? '' : $this->input->get("end_time", TRUE),
            "delete"     => ($this->input->get("delete", TRUE) === false) ? '0' : '1',
            "status"     => ($this->input->get("status", TRUE) === false) ? '' : $this->input->get("status", TRUE),
            "uncomment"     => ($this->input->get("uncomment", TRUE) === false) ? '0' : '1',
            "replied"    => ($this->input->get("replied", TRUE) === false) ? '0' : '1',
            "replyadmin" => ($this->input->get("replyadmin", TRUE) === false) ? '0' : '1',  
        );

        $checkStatus = array(
            0 => '全部',
            1 => '待审核',
            2 => '审核成功',
            3 => '审核失败', 
        );
        if (empty($searchData['start_time'])) {
            $searchData['start_time'] = date('Y-m-d 00:00:00', strtotime('-1 day'));
        }
        if (empty($searchData['end_time'])) {
            $searchData['end_time'] = date('Y-m-d 23:59:59');
        }
        $this->filterTime($searchData['start_time'], $searchData['end_time']);
        $this->load->model('info_comments_model');
        $result = $this->info_comments_model->list_comments($searchData, $page, self::NUM_PER_PAGE);

        $pageConfig = array(
            "page"     => $page,
            "npp"      => self::NUM_PER_PAGE,
            "allCount" => $result[1]
        );
        $pages = get_pagination($pageConfig);

        $sourceList = $this->model_info->getSourceList();

        $pageInfo = array(
            "comments"    => $result[0],
            "pages"       => $pages,
            "search"      => $searchData,
            'checkStatus' => $checkStatus,
            "sourceList"  => $sourceList,
            "checkStatus" => $checkStatus,
        );
        $this->load->view("info/comments", $pageInfo);
    }

    // 删除评论
    public function delComments()
    {
        $this->check_capacity("6_8_2", true);
        $ids = $this->input->post("ids", TRUE);

        if(!empty($ids))
        {
            $idsArr = array();
            $this->load->model('info_comments_model');
            foreach ($ids as $id) 
            {
                $idArr = explode('-', $id);
                $idsArr[] = $idArr[1];
                $this->info_comments_model->delComment($idArr[0], $idArr[1]);
            }

            if(!empty($idsArr))
            {
                $idsArr = implode(' ', $idsArr);
                $idsArr = str_replace(" ", "','", trim($idsArr));
                $info = $this->info_comments_model->getCommentsByIds($idsArr);
                if(!empty($info))
                {
                    $log = "删除评论：";
                    foreach ($info as $items) 
                    {
                        $log .= $items['content'] . " ， /info/" . $this->categoryUrls[$items['category_id']] . "； ";
                    }
                    $this->syslog(56, $log);
                }
            }

        }
        $result = array(
            'status' => '1',
            'message' => '删除成功',
        );
        echo json_encode($result);
    }

    // 手动成功
    public function handleCommSucc()
    {
        $this->check_capacity("6_8_2", true);
        $ids = $this->input->post("ids", TRUE);

        if(!empty($ids))
        {
            $idsArr = array();
            $this->load->model('info_comments_model');
            foreach ($ids as $id) 
            {
                $idArr = explode('-', $id);
                $idsArr[] = $idArr[1];
                $this->info_comments_model->handleCommSucc($idArr[0], $idArr[1]);
            }

            if(!empty($idsArr))
            {
                $idsArr = implode(' ', $idsArr);
                $idsArr = str_replace(" ", "','", trim($idsArr));
                $info = $this->info_comments_model->getCommentsByIds($idsArr);
                if(!empty($info))
                {
                    $log = "手动成功评论：";
                    foreach ($info as $items) 
                    {
                        $log .= $items['content'] . " ， /info/" . $this->categoryUrls[$items['category_id']] . "； ";
                    }
                    $this->syslog(56, $log);
                }
            }
        }
        $result = array(
            'status' => '1',
            'message' => '操作成功',
        );
        echo json_encode($result);
    }
    
    public function handlecomment()
    {
        $this->check_capacity("6_8_3", true);
        $uid = $this->input->post("uid", TRUE);
        $status = $this->input->post("status", TRUE);
        $this->load->model('info_comments_model');
        $res = $this->info_comments_model->handlecomment($uid, $status);
        $log = $status == 1 ? "禁言用户：{$res['uname']}" : "解除禁言用户：{$res['uname']}";
        $this->syslog(59, $log);
        $result = array(
            'status' => '1',
            'message' => '操作成功',
        );
        echo json_encode($result);
    }

    public function getComment()
    {
        $this->check_capacity("6_8_4", true);
        $id = $this->input->post("id", TRUE);
        $type = $this->input->post("type", TRUE);
        $result = array(
            'status' => '0',
            'message' => '获取评论失败',
            'data' => ''
        );
        if($id)
        {
            $this->load->model('info_comments_model');
            $info = $this->info_comments_model->getCommentDetail($id);
            if($info)
            {
                // 向上查询
                $data = array();
                $data2 = array();
                $upId = $info['tid'] > 0 ? $info['tid'] : $info['pid'];
                if($upId && $type)
                {
                    $upInfo = $this->info_comments_model->getCommentDetail($upId);
                    if($upInfo)
                    {
                        $data = array(
                            'newsId'        =>  $upInfo['newsId'],
                            'commentId'     =>  $upInfo['id'],
                            'uname'         =>  $upInfo['uname'],
                            'content'       =>  emoji4img($upInfo['content']),
                            'created'       =>  $upInfo['created'],
                        );

                        // 向上查询
                        $upId2 = $upInfo['tid'] > 0 ? $upInfo['tid'] : $upInfo['pid'];
                        if($upId2)
                        {
                            $upInfo2 = $this->info_comments_model->getCommentDetail($upId2);
                            if($upInfo2)
                            {
                                $data2 = array(
                                    'newsId'        =>  $upInfo2['newsId'],
                                    'commentId'     =>  $upInfo2['id'],
                                    'uname'         =>  $upInfo2['uname'],
                                    'content'       =>  emoji4img($upInfo2['content']),
                                    'created'       =>  $upInfo2['created'],
                                );
                            }
                        }
                    }    
                }
                $result = array(
                    'status' => '1',
                    'message' => '获取评论成功',
                    'data' => array(
                        'newsId'        =>  $info['newsId'],
                        'commentId'     =>  $info['id'],
                        'uname'         =>  $info['uname'],
                        'content'       =>  emoji4img($info['content']),
                        'created'       =>  $info['created'],
                    ),
                    'up' => $data,
                    'up2' => $data2
                );
            }
        }
        echo json_encode($result);
    }

    public function postComment()
    {
        $id = $this->input->post("id", true);
        $content = $this->input->post("content", true);
        $commentId = $this->input->post("commentId", true);
        $uname = $this->input->post("uname", true);
        $time = $this->input->post("time", true);

        if(empty($content))
        {
            $result = array(
                'status' => '0',
                'msg'    => '评论内容不能为空',
                'data'   => ''
            );
            die(json_encode($result));
        }

        if($this->abslength($content) > 250)
        {
            $result = array(
                'status' => '0',
                'msg'    => '内容过长，请重新输入',
                'data'   => ''
            );
            die(json_encode($result));
        }

        if(intval($id) == 0)
        {
            $result = array(
                'status' => '0',
                'msg'    => '提交参数错误，请稍后再试',
                'data'   => ''
            );
            die(json_encode($result));
        }

        // 提交评论
        $comments = array(
            'newsId'    =>  $id,
            'uid'       =>  1,
            'content'   =>  $content,
            'tid'       =>  $commentId ? $commentId : 0,
            'status'    =>  1,
        );
        $this->load->model('info_comments_model');
        $info = $this->info_comments_model->postComment($comments);
        if($info)
        {
            // 日志
            $msg = '回复' . $uname .  '在' . $time . '的评论，回复内容：' . $content;
            $this->syslog(59, $msg);
            $result = array(
                'status' => '1',
                'msg'    => '评论成功',
                'data'   => ''
            );
        }
        else
        {
            $result = array(
                'status' => '0',
                'msg'    => '评论失败',
                'data'   => ''
            );
        }
        die(json_encode($result));
    }

    public function abslength($str)
    {
        if(empty($str))
        {
            return 0;
        }

        if(function_exists('mb_strlen'))
        {
            return mb_strlen($str,'utf-8');
        }
        else
        {
            preg_match_all("/./u", $str, $ar);
            return count($ar[0]);
        }
    }
    
    public function headimgManage()
    {
        $this->check_capacity("6_9_1");
        $page = intval($this->input->get("p"));
        $page = $page <= 1 ? 1 : $page;
        $searchData = array(
            "uname" => ($this->input->get("uname", TRUE) === false) ? '' : $this->input->get("uname", TRUE),
            "forbidden" => ($this->input->get("forbidden", TRUE) === false) ? '0' : '1',
            "start_time" => ($this->input->get("start_time", TRUE) === false) ? '' : $this->input->get("start_time", TRUE),
            "end_time" => ($this->input->get("end_time", TRUE) === false) ? '' : $this->input->get("end_time", TRUE),
        );
        if (empty($searchData['start_time'])) {
            $searchData['start_time'] = date('Y-m-d 00:00:00', strtotime('-30 day'));
        }
        if (empty($searchData['end_time'])) {
            $searchData['end_time'] = date('Y-m-d 23:59:59');
        }
        $this->filterTime($searchData['start_time'], $searchData['end_time']);
        $this->load->model('model_user');
        $result = $this->model_user->list_headimg($searchData, $page, 20);
        $pageConfig = array(
            "page" => $page,
            "npp" => 20,
            "allCount" => $result[1]
        );
        $pages = get_pagination($pageConfig);
        $config = $this->model_user->getUploadConfig();
        $pageInfo = array(
            "pages" => $pages,
            "search" => $searchData,
            "imgs" => $result[0],
            'config' => $config,
        );
        $this->load->view("info/headimg", $pageInfo);
    }

    public function updateUploadConfig()
    {
        $this->check_capacity("6_9_4", true);
        $type = $this->input->post("type", TRUE);
        $this->load->model('model_user');
        $this->model_user->updateUploadConfig($type);
        $this->syslog(70, $type == 1 ? "全部禁止上传头像操作" : "解除全部禁止上传头像操作");
        $result = array(
            'status' => '1',
            'message' => '修改成功',
        );
        echo json_encode($result);
    }

    public function deleteImg()
    {
        $this->check_capacity("6_9_2", true);
        $uid = $this->input->post("uid", TRUE);
        $this->load->model('model_user');
        if (!is_array($uid)) {
            $uid = array($uid);
        }
        $unames = $this->model_user->deleteImg($uid);
        $this->syslog(70, "删除" . implode(',', $unames) . "头像操作");
        $result = array(
            'status' => '1',
            'message' => '修改成功',
        );
        echo json_encode($result);
    }

    public function forbiddenUpload()
    {
        $this->check_capacity("6_9_3", true);
        $uid = $this->input->post("uid", TRUE);
        $type = $this->input->post("type", TRUE);
        $this->load->model('model_user');
        $uninfo = $this->model_user->forbiddenUpload($uid, $type);
        $this->syslog(70, $type == 2 ? "禁止{$uninfo['uname']}上传头像操作" : "解除禁止{$uninfo['uname']}上传头像操作");
        $result = array(
            'status' => '1',
            'message' => '修改成功',
        );
        echo json_encode($result);
    }

}