<?php

/**
 * 敏感词分析.
 *
 * @date:2017-06-21
 */
defined('BASEPATH') or exit('No direct script access allowed');

class Cli_Sensitive_Word extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
    }
    
    public function index()
    {
        $this->load->model('united_planner_model');
        
        $words = $this->united_planner_model->getSensitiveWords();
        // 合买介绍审核
        $this->checkUnitedIntro();
        // 资讯评论审核
        $this->checkInfoComments($words);
        // 合买宣言审核
        $this->checkUnitedOrderIntro($words);
    }
    public function checkUnitedIntro()
    {
        $preg = array_filter( $this->united_planner_model->getSensitiveWordsCol() );
        //按照1000个关键词拆分 避免匹配关键词过长
        $len = ceil(count($preg) /1000);
        $preg_arr = array();
        for($i=0;$i<$len;$i++)
        {
            $preg_str = "#".implode('|', array_slice($preg,$i*1000,1000))."#";
            array_push($preg_arr, $preg_str);
        }
        $this->load->model('user_model');
        $users = $this->user_model->uncheckSensitive();
        if(!empty($users))
        {
            foreach ($users as $user)
            {
                $num = 0;
                $preg_res = array();
                foreach ($preg_arr as $preg)
                {
                    preg_match_all($preg,$user['introduction'] ,$res);
                    if(isset($res[0]) && count($res[0]) )
                    {
                        $preg_res = array_merge($preg_res,$res[0]);
                    }
                    
                }
                $preg_res = array_unique($preg_res);
                if (count($preg_res) >= 1)
                {
                    $this->user_model->updateIntroduction($user['uid'], $user['introduction'], 2,implode('|', $preg_res));
                } else {
                    $this->user_model->updateIntroduction($user['uid'], $user['introduction'], 1,'');
                }
            }
        }
    }
    
    public function checkInfoComments($words)
    {
        $this->load->model('info_comments_model');
        $info = $this->info_comments_model->getUncheckedComments();
        if(!empty($info))
        {
            foreach ($info as $comment)
            {
                $sensitives = array();
                if(!empty($words))
                {
                    foreach ($words as $word)
                    {
                        $word['word'] = trim($word['word']);
                        if($word['word'] && strstr($comment['content'], $word['word']))
                        {
                            array_push($sensitives, $word['word']);
                        }
                    }
                }
                
                $sensitives = $sensitives ? implode(' ', $sensitives) : '';
                $checkRes = !empty($sensitives) ? 2 : 1;
                // 更新审核状态
                $this->info_comments_model->updateCheckStatus($comment['id'], $checkRes, $sensitives);
                if($checkRes == 1)
                {
                    // 更新评论数
                    $this->info_comments_model->updateComNum($comment['newsId'], 1);
                }
            }
        }
    }
    
    // 合买宣言审核
    public function checkUnitedOrderIntro($words)
    {
        $this->load->model('united_order_model');
        $info = $this->united_order_model->getUncheckedIntro();
        if(!empty($info))
        {
            foreach ($info as $items) 
            {
                $sensitives = array();
                if(!empty($words))
                {
                    foreach ($words as $word)
                    {
                        $word['word'] = trim($word['word']);
                        if($word['word'] && strstr($items['introduction'], $word['word']))
                        {
                            array_push($sensitives, $word['word']);
                        }
                    }
                }
                
                $sensitives = $sensitives ? implode(' ', $sensitives) : '';
                $checkRes = !empty($sensitives) ? 2 : 1;
                // 更新审核状态
                $this->united_order_model->updateCheckIntro($items['orderId'], $checkRes, $sensitives);
            }
        }
    }
}   