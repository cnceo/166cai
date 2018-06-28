<?php

/*
 * 资讯
 * @date:2016-09-06
 */

class Info extends MY_Controller 
{
	public function __construct()
	{
		parent::__construct();
        $this->load->model('info_model');
        $this->category = $this->info_model->getCategory();
	}

    public function getInfoList()
    {
        // 调试
        // $data = array(
        //     'ctype' => '6',
        //     'page' => '1',
        //     'number' => '10'
        // );

        // http
        $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https:" : "http:";

        $data = $this->input->get(null, true);

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

        $data = $this->info_model->getInfoList($data['ctype'], $data['page'], $data['number']);

        $info = array();
        if(!empty($data))
        {
            foreach ($data as $key => $items) 
            {
                $info[$key]['title'] = $items['title'];
                $info[$key]['content'] = utf8_substr(htmltochars($items['content']), 0, 60);
                $info[$key]['date'] = date('m-d H:i', strtotime($items['created']));
                $info[$key]['num'] = ($items['num'] > 9999) ? '9999' : $items['num'];
                $info[$key]['likeNum'] = ($items['likeNum'] > 9999) ? '9999' : $items['likeNum'];
                $info[$key]['comNum'] = ($items['comNum'] > 9999) ? '9999' : $items['comNum'];
                $info[$key]['newsId'] = $items['id'];
                $info[$key]['url'] = $protocol . $this->config->item('pages_url') . 'app/info/detail/' . $items['id'];
            }
        }

        $result = array(
            'status' => '1',
            'msg' => '通讯成功',
            'data' => $info
        );
        echo json_encode($result);
    }
}