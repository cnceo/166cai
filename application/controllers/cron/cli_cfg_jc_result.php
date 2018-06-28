<?php

/**
 * 竞彩赔率抓取脚本
 * @date:2017-02-27
 */

defined('BASEPATH') OR exit('No direct script access allowed');

class Cli_Cfg_Jc_Result extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
    }

    public function index()
    {
        $this->captureJclqOddsResult();
        $this->captureJczqOddsResult();
    }

    /**
     * 竞彩篮球赔率
     * @date:2017-02-27
     */
    public function captureJclqOddsResult()
    {
        $this->load->library('jclq_result_sporttery');
        $this->jclq_result_sporttery->capture();
    }

    /**
     * 竞彩足球赔率
     * @date:2017-02-27
     */
    public function captureJczqOddsResult()
    {
        $this->load->library('jczq_result_sporttery');
        $this->jczq_result_sporttery->capture();
    }
}