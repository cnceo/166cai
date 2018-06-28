<?php

class Help extends MY_Controller 
{

    /**
     * 帮助中心
     * @Author liusijia
     */
    public function index( $posString = '' ) 
    {

        $this->config->load('help');
        $help_center_rule = $this->config->item('help_center_rule');

        //seo 优化 add by liuli
        $this->config->load('seo');
        $seo_info = $this->config->item('seo');

        //输出数据
        $data = array(
            'help_center' => $this->config->item('help_center'),
            'help_center_type' => $this->config->item('help_center_type'),
            'help_empty' => $this->config->item('help_empty'),
            'help_center_type_logo' => $this->config->item('help_center_type_logo'),
            'help_center_rule' => $help_center_rule,
        );

        if( !empty( $posString ) )
        {
            $posArray = $this->parseArg( $posString );
            $data['s'] = !isset($posArray['s']) ? '0' : $posArray['s'] ; //二级分类标识
            $data['t'] = !isset($posArray['t']) ? '0' : $posArray['t'] ; //规则和简介页面标识
            $data['b'] = !isset($posArray['b']) ? '0' : $posArray['b'] ; //一级分类标识
            $data['f'] = !isset($posArray['f']) ? '0' : $posArray['f'] ; //首页帮助中心部分标识
            $data['i'] = !isset($posArray['i']) ? '0' : $posArray['i'] ; //帮助中心首页标识
            $data['pageTitle'] = $seo_info['help_title'][$data['b']][$data['s']];
            $data['cnName'] = str_replace('-', '', $data['pageTitle']);
        }
        else
        {
            $data['pageTitle'] = '彩票问题答疑-';
            $data['cnName'] = '';
            $data['s'] = '0';
            $data['t'] = '0';
            $data['b'] = '0';
            $data['f'] = '0';
            $data['i'] = '0';
        }

        $data['htype'] = 1;
        if (in_array($data['b'], $help_center_rule)) 
        {//规则 和 简介页面
            $this->displayMore('help/index_rule', $data, 'v1.1');
        } 
        else 
        {//问题页面
            $this->displayMore('help/index_question', $data, 'v1.1');
        }
    }


    private function parseArg( $posString )
    {
        $resultArray = array();
        $posArray = explode( '-', $posString );

        foreach( $posArray as $val )
        {
            $resultArray[substr( $val, 0, 1)] = substr( $val, 1);
        }
        return $resultArray;
    }

}