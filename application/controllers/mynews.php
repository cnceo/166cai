<?php

class Mynews extends MY_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('news_model');
        $this->load->model('Notice_Model');
    }

    public function index($cpage = 1) {

        $cpage = $this->input->get('cpage', true);

        $pagesize = 10;     //分页条数
        $fontsize = 40;     //问题摘要字数

        //登录检查
        if(!$this->uid){
            $this->redirect('/');
            exit;
        }

        //统计总消息数量
        $count = $this->news_model->countNewsList($this->uid);

        if( !is_numeric($cpage) || $cpage <= 0 ){
            $cpage = 1;
        }
        $count = $count[0];
        $maxpage = ceil(intval($count)/$pagesize);

        if( $cpage > $maxpage ){
            $cpage = 1;
        }

        $pagenum = $pagesize*($cpage-1);

        //分页配置
        $pdata = array();
        $pdata['pagenum'] = $pagesize;      //分页条数
        //$pdata['ajaxform'] = '';
        $pdata['pagenum'] = $maxpage;

        //分页统计
        $listInfo = $this->news_model->getNewsList($this->uid,$pagenum,$pagesize);

        //var_dump($listInfo);die;

        if($listInfo){
            foreach ($listInfo as $k => $v) {
                //筛选内容过长的问题
                if($this->abslength($listInfo[$k]['content']) > $fontsize){
                    //截取部分内容
                    $listInfo[$k]['abstract'] = 1;
                    $listInfo[$k]['s_content'] = $this->utf8_substr($listInfo[$k]['content'],0,$fontsize);
                    $listInfo[$k]['l_content'] = $listInfo[$k]['content'];
                }else{
                    $listInfo[$k]['abstract'] = 0;
                    $listInfo[$k]['s_content'] = $listInfo[$k]['content'];
                    $listInfo[$k]['l_content'] = $listInfo[$k]['content'];
                }
                $replyInfo = $this->news_model->getReplyList($listInfo[$k]['id']);
                $listInfo[$k]['replyInfo'] = $replyInfo;
            }
        }

        //更新所有的消息为已读
        $this->news_model->updateNewsList($this->uid);

        $this->display("mynews/index", array(
            'listInfo' => $listInfo,
            'pageNumber' => $maxpage,
            'pagestr' => $this->load->view('v1.1/elements/common/pages', $pdata, true),
        ), 'v1.1');

    }


    public function feedback() {
        $res = array(
            'status' => '001',
            'msg' => '通信异常',
        );
        if($this->is_ajax){
            $data = $this->input->post(null, true);

            //检查数据来源正确性
            if(!is_numeric($data['feedtype'])){
                $res = array(
                    'status' => '002',
                    'msg' => '提交信息异常',
                );
                die(json_encode($res));
            }

            if($data['feedtype'] == 1){
                $data['feedtype'] = 1;
            }elseif($data['feedtype'] == 2){
                $data['feedtype'] = 2;
            }else{
                $res = array(
                    'status' => '002',
                    'msg' => '提交信息异常',
                );
                die(json_encode($res));
            }

            if(empty($data['feedcontent'])){
                $res = array(
                    'status' => '003',
                    'msg' => '反馈内容不能为空！',
                );
                die(json_encode($res));
            }

            if($this->abslength($data['feedcontent']) > 500){
                $res = array(
                    'status' => '004',
                    'msg' => '反馈内容过长，请重新输入！',
                );
                die(json_encode($res));
            }

            //组装数据
            $record = array(
                'uid' => $this->uid?$this->uid:0,
                'name' => $this->uname?$this->uname:'',
                'content' => $data['feedcontent'],
                'if_reply' => 0,
                'type' => $data['feedtype'],
                'platform' => 0
            );
            //入库
            $res = $this->news_model->insertNewsList($record);
            if($res){
                $res = array(
                    'status' => '000',
                    'msg' => 'OK',
                );
            }else{
                $res = array(
                    'status' => '005',
                    'msg' => '提交信息失败',
                );
            }
        }
        die(json_encode($res));
    }

    /*
     * 获取中文字符长度
     * @author:liuli
     * @date:2015-01-26
     */
    public function abslength($str){

        if(empty($str)){
            return 0;
        }

        if(function_exists('mb_strlen')){
            return mb_strlen($str,'utf-8');
        }else{
            preg_match_all("/./u", $str, $ar);
            return count($ar[0]);
        }

    }

    /*
     * 内容截取
     * @author:liuli
     * @date:2015-01-26
     */
    public function utf8_substr($str,$start=0,$end=10){

        if(empty($str)){
            return false;
        }
        
        if(function_exists('mb_substr')){
            if(func_num_args() >= 3){
                $end = func_get_arg(2);
                return mb_substr($str,$start,$end,'utf-8');
            }else{
                mb_internal_encoding("UTF-8");
                return mb_substr($str,$start);
            }
        }else{
            $null = "";
            preg_match_all("/./u", $str, $ar);
            if(func_num_args() >= 3){
                $end = func_get_arg(2);
                return join($null, array_slice($ar[0],$start,$end));
            }else{
                return join($null, array_slice($ar[0],$start));
            }
        }
    }

}
