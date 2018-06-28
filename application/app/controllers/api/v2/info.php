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
                $info[$key]['url'] = $this->config->item('protocol') . $this->config->item('pages_url') . 'app/info/detail/' . $items['id'];
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

    // 回复我的资讯评论
    public function getReplyList()
    {
        $data = $this->strCode($this->input->post('data'));
        $data = json_decode($data, true);

//         $data = array(
//             'uid' => '151',
//             'page' => '1',
//             'number' => '10',
//         );

        // 参数检查
        if(empty($data['uid']) || empty($data['page']) || empty($data['number']))
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
        
        $this->load->model('info_comments_model');
        $listInfo = $this->info_comments_model->getReplyList($data['uid'], $data['page'], $data['number']);
        $this->load->model('user_model', 'User');
        $uinfo = $this->User->getUserInfo($data['uid']);
        $info = array();
        if(!empty($listInfo))
        {
            // 清空缓存内的回复id
            $this->info_comments_model->refreshReplyId($data['uid'], 0);

            foreach ($listInfo as $key => $items) 
            {
                // 回复状态
                $replyStatus = (!empty($items['tuid']) && !empty($items['tcontent'])) ? ($items['tdelete'] ? '2' : '1') : '0';

                // 资讯类别
                $category = $this->category[$items['category_id']]['name'];

                $info[] = array(
                    'newsId'    =>  $items['newsId'],
                    'commentId' =>  $items['id'],
                    'uname'     =>  $items['uname'],
                    'avatar'    =>  $items['headimgurl'],
                    'content'   =>  $items['content'],
                    'date'      =>  $this->getDateFormat($items['created']),
                    'isAdmin'   =>  ($items['uid'] == '1') ? '1' : '0',
                    'title'     =>  $category ? $category . '：' . $items['title'] : $items['title'],
                    'url'       =>  $this->config->item('protocol') . $this->config->item('pages_url') . 'app/info/detail/' . $items['newsId'],
                    'reply' => array(
                        'status'    =>  $replyStatus,
                        'uname'     =>  ($replyStatus == '1' && $items['tuname']) ? $items['tuname'] : '',
                        'content'   =>  ($replyStatus == '1' && $items['tcontent']) ? $items['tcontent'] : '',
                        'commentId' =>  $items['tid'] ? $items['tid'] : $items['pid']
                    )
                );
            }
        }

        $result = array(
            'status' => '1',
            'msg' => '通讯成功',
            'data' => $info
        );
        echo json_encode($result);
    }

    // 提交资讯详情评论
    public function postComment()
    {
        $data = $this->strCode($this->input->post('data'));
        $data = json_decode($data, true);

        if(empty($data['uid']))
        {
            $result = array(
                'status' => '0',
                'msg'    => '用户未登录',
                'data'   => ''
            );
            die(json_encode($result));
        }

        if(empty($data['newsId']) || empty($data['commentId']))
        {
            $result = array(
                'status' => '0',
                'msg'    => '缺少必要参数',
                'data'   => ''
            );
            die(json_encode($result));
        }

        if(empty($data['content']))
        {
            $result = array(
                'status' => '0',
                'msg'    => '评论内容不能为空',
                'data'   => ''
            );
            die(json_encode($result));
        }

        $this->load->library('comm');
        if($this->comm->abslength($data['content']) > 250)
        {
            $result = array(
                'status' => '0',
                'msg'    => '内容过长，请重新输入',
                'data'   => ''
            );
            die(json_encode($result));
        }

        $this->load->model('user_model');
        $res = $this->user_model->getCommentStatus($data['uid']);
        if($res['uncomment'] == 1)
        {
            $result = array(
                'status' => '0',
                'msg'    => '您因不当发言或敏感信息被禁止评论',
                'data'   => ''
            );
            die(json_encode($result));                    
        }
        // 提交评论
        $comments = array(
            'newsId'    =>  $data['newsId'],
            'uid'       =>  $data['uid'],
            'content'   =>  $this->security->xss_clean($data['content']),
            'tid'       =>  $data['commentId'] ? $data['commentId'] : 0
        );
        $info = $this->info_comments_model->postComment($comments);
        if($info)
        {
            $result = array(
                'status' => '1',
                'msg'    => '评论成功',
                'data'   => array(
                    'uname'     =>  $info['uname'],
                    'content'   =>  $info['content'],
                    'date'      =>  $this->getDateFormat($info['created'])
                )
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