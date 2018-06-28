<?php

/**
 * 领取红包报警
 * @author yindefu
 */
class Cli_Hongbao_Alert extends MY_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->model('warning_model', 'wanning');
        $this->load->model('red_pack_model', 'redpack');
    }

    public function day()
    {
        $dayStart = date("Y-m-d 00:00:00", strtotime("-1 days"));
        $dayEnd = date("Y-m-d 00:00:00");
        $dayCount = $this->redpack->countRedpackByTime($dayStart, $dayEnd);
        $lastStart = date("Y-m-d 00:00:00", strtotime("-2 days"));
        $lastEnd = date("Y-m-d 00:00:00", strtotime("-1 days"));
        $lastCount = $this->redpack->countRedpackByTime($lastStart, $lastEnd);
        $average = ($dayCount['count'] + $lastCount['count']) / 2;
        $more = $dayCount['count'] - $average;
        if ($more > 0) {
            $per = $more / $average;
            if ($per > 0.2) {
                $content = array(27, '领取红包数量报警', date("Y-m-d", strtotime("-1 days")) . '日领取红包数量：' . $dayCount['count'] . ',比' . date("Y-m-d", strtotime("-2 days")) . '日领取红包数量：' . $lastCount['count'] . ',高出' . round($per * 100, 2) . '%');
                $this->wanning->hongbaoAlert($content);
            }
        }
    }

    public function am()
    {
        $dayStart = date("Y-m-d 00:00:00");
        $dayEnd = date("Y-m-d 12:00:00");
        $dayCount = $this->redpack->countRedpackByTime($dayStart, $dayEnd);
        $lastStart = date("Y-m-d 00:00:00", strtotime("-1 days"));
        $lastEnd = date("Y-m-d 12:00:00", strtotime("-1 days"));
        $lastCount = $this->redpack->countRedpackByTime($lastStart, $lastEnd);
        $average = ($dayCount['count'] + $lastCount['count']) / 2;
        $more = $dayCount['count'] - $average;
        if ($more > 0) {
            $per = $more / $average;
            if ($per > 0.2) {
                $content = array(27, '领取红包数量报警', date("Y-m-d") . '日上午领取红包数量：' . $dayCount['count'] . ',比' . date("Y-m-d", strtotime("-1 days")) . '日上午领取红包数量：' . $lastCount['count'] . ',高出' . round($per * 100, 2) . '%');
                $this->wanning->hongbaoAlert($content);
            }
        }
    }
}
