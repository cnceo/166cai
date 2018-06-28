<?php

/**
 * Copyright (c) 2015,上海二三四五网络科技有限公司.
 * 摘    要:
 * 作    者: 刁寿钧
 * 修改日期: 2016/3/18
 * 修改时间: 7:58
 */
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * @property Api_Zhisheng_Model $dataSource
 */
class Cli_Zhisheng extends MY_Controller
{

    /*
	 * 功能：获取智胜赔率
	 * 作者：刁寿钧
	 * 日期：2016-03-10
	 * */
    public function index()
    {
        $this->load->model('api_zhisheng_model', 'dataSource');
        $this->dataSource->writeEuropeOdds();
    }
}