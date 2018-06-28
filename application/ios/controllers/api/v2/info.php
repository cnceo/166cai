<?php

/*
 * 资讯
 * @date:2018-08-03
 */

class Info extends MY_Controller 
{
	public function __construct()
	{
		parent::__construct();
        $this->load->model('info_model');
        $this->load->model('info_comments_model');
        $this->category = $this->info_model->getCategory();
	}

    public function getInfoList()
    {
        // 调试
        // $data = array(
        //     'ctype' => '1',
        //     'page' => '1',
        //     'number' => '10',
        //     'uid' => '1'
        // );

        $data = $this->strCode($this->input->post('data'));
        $data = json_decode($data, true);

        // 参数检查
        if(empty($data['ctype']) || empty($data['page']) || empty($data['number']))
        {
            $result = array(
                'status' => '0',
                'msg' => '缺少必要参数',
                'data' => ''
            );
            echo json_encode($result);
            exit();
        }

        $data['page'] = max(1, intval($data['page']));
        $data['number'] = max(1, intval($data['number']));

        $listInfo = $this->info_model->getInfoList($data['ctype'], $data['page'], $data['number']);

        $info = array();
        if(!empty($listInfo))
        {
            foreach ($listInfo as $key => $items) 
            {
                $info[$key]['title'] = $items['title'];
                $info[$key]['content'] = utf8_substr(htmltochars($items['content']), 0, 60);
                $info[$key]['date'] = date('m-d H:i', strtotime($items['show_time']));
                $info[$key]['num'] = ($items['num'] > 9999) ? '9999' : $items['num'];
                $info[$key]['likeNum'] = ($items['likeNum'] > 9999) ? '9999' : $items['likeNum'];
                $info[$key]['comNum'] = ($items['comNum'] > 9999) ? '9999' : $items['comNum'];
                $info[$key]['newsId'] = $items['id'];
                $info[$key]['url'] = $this->config->item('protocol') . $this->config->item('pages_url') . 'ios/info/detail/' . $items['id'];
                $info[$key]['isUserLike'] = $this->isUserLike($items['id'], $data['uid']);
                $info[$key]['isTop'] = $items['is_top'] ? '1' : '0';
                $info[$key]['isComment'] = $this->isComment($items['id'], $data['uid']);
            }
        }

        $result = array(
            'status' => '1',
            'msg' => '通讯成功',
            'data' => $info
        );
        echo json_encode($result);
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

    // 资讯点赞
    public function infoLike()
    {
        $result = array(
            'status' => '0',
            'msg' => '缺少必要参数',
            'data' => ''
        );

        $data = $this->strCode($this->input->post('data'));
        $data = json_decode($data, true);

        if(!empty($data['uid']) && !empty($data['newsId']))
        {
            $info = array(
                'uid'       =>  $data['uid'],
                'newsId'    =>  $data['newsId'],
                'status'    =>  $data['status'] ? 1 : 0,
            );
            $res = $this->info_comments_model->infoLike($info);
            if($res)
            {
                $result = array(
                    'status' => '1',
                    'msg' => '操作成功',
                    'data' => ''
                );
            }
            else
            {
                $result = array(
                    'status' => '0',
                    'msg' => '操作失败',
                    'data' => ''
                );
            }
        }

        echo json_encode($result);
    }

    public function isComment($newsId, $uid = 0)
    {
        $isComment = '0';
        if($uid)
        {
            $count = $this->info_comments_model->getCommentCount($newsId, $uid);
            if($count > 0)
            {
                $isComment = '1';
            }
        }
        return $isComment;
    }
}