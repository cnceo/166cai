<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Cli_Activity_Jc extends MY_Controller
{
	public function __construct()
    {
        parent::__construct();
        $this->load->model('jcmatch_model');
    }

    public function index()
    {
        $activityInfo = $this->jcmatch_model->getActivityConfig();

        if(!empty($activityInfo))
        {
            foreach ($activityInfo as $key => $activity) 
            {
                $info = $this->jcmatch_model->getJoinConfig($activity);

                if(!empty($info))
                {
                    $flag = 0;
                    $winFlag = 0;
                    $noWinFlag = 0;
                    $payFlag = 0;
                    foreach ($info as $k => $items)
                    {
                        if($items['status'] == '0')
                        {
                            $flag++;
                        }
                        elseif($items['status'] == '1000')
                        {
                            $noWinFlag++;
                            if($items['pay_status'] == '1')
                            {
                                $payFlag++;
                            }
                        }
                        elseif($items['status'] == '2000')
                        {
                            $winFlag++;
                        }
                    }

                    // 订单全部处理完毕
                    $pay_status = 0;
                    if($flag == 0)
                    {
                        if($winFlag > 0)
                        {
                            $status = 2;
                        }
                        elseif($noWinFlag > 0) 
                        {
                            $status = 1;
                            if($noWinFlag == $payFlag)
                            {
                                $pay_status = 1;
                            }
                        }

                        $activityData = array(
                            'activity_id' => $activity['activity_id'],
                            'activity_issue' => $activity['activity_issue'],
                            'status' => $status,
                            'pay_status' => $pay_status
                        );
                        $this->jcmatch_model->synActivityConfig($activityData);
                    }
                }
            }
        }
    }
}