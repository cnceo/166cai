<?php

/**
 * 网站公告
 * @Author liusijia
 */
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Notice extends MY_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('Notice_Model');
    }

    /**
     * 网站公告 列表
     * @Author liusijia
     */
    public function index() {
        $cpage = intval($this->input->get('cpage', true));
  		$cpage = $cpage <= 1 ? 1 : $cpage;
        $psize = 15;
        //根据相关条件查询出所需的网站公告
        $result = $this->Notice_Model->noticeList(array('status' => 1), $cpage, $psize);
        //总条数
        $total = 0;
        //总页数
        $page_num = 0;
        if ($result) {
            $total = $this->Notice_Model->noticeCount(array('status' => 1));
            $page_num = ceil($total / $psize);
            $pdata['pagenum'] = $page_num;
        }
        $this->display('notice/index', array(
            'result' => $result,
            'pagestr' => $this->load->view('v1.1/elements/common/pages', $pdata, true),
            'total' => $total,
            'page_num' => $page_num,
        	'pageNumber' => $cpage,
        	'htype' => 1,
                )
        		, 'v1.1'
        );
    }

    /**
     * 公告详细信息
     * @Author liusijia
     */
    public function detail( $id ) {     
        $detailInfo = $this->Notice_Model->getInfoById($id);
        if (empty($detailInfo)) {
        	$this->redirect('/error/');
        }
        $this->display('notice/detail', array(
            'pageTitle' => $detailInfo['title'],      //seo 优化 add by liuli
            'result' => $detailInfo,
        	'htype' => 1,
                ),
        		'v1.1'
        );
    }

}