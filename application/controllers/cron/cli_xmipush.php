<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/*
 * APP 小米推送脚本
 * @date:2017-05-24
 */

class Cli_Xmipush extends MY_Controller
{
	public function __construct()
    {
        parent::__construct();
        $this->load->model('mipush_model');
    }

    // 排除中间和合买关注
    public function index()
    {
        $pushInfo = $this->mipush_model->getPushLog();
        while(!empty($pushInfo))
        {
            foreach ($pushInfo as $info) 
            {
                $this->doMiPush($info);
            }
            $pushInfo = $this->mipush_model->getPushLog();
        }
    }

    // 中奖
    public function orderWin()
    {
        $contine = true;
        while($contine)
        {
            //便于测试环境的调试
            if(ENVIRONMENT==='development')
            {
                $this->runOrderWin();
            }
            else
            {
                $croname = "cron/cli_xmipush runOrderWin";
                system("{$this->php_path} {$this->cmd_path} $croname",  $status);
            }
            sleep(5);
        }
    }

    public function runOrderWin()
    {
        $pushInfo = $this->mipush_model->getPushLogByWin();

        if(!empty($pushInfo))
        {
            foreach ($pushInfo as $info) 
            {
                $this->doMiPush($info);
            }
        }
    }

    // 出票成功
    public function orderDraw()
    {
        $contine = true;
        while($contine)
        {
            //便于测试环境的调试
            if(ENVIRONMENT==='development')
            {
                $this->runOrderDraw();
            }
            else
            {
                $croname = "cron/cli_xmipush runOrderDraw";
                system("{$this->php_path} {$this->cmd_path} $croname",  $status);
            }
            sleep(5);
        }
    }

    public function runOrderDraw()
    {
        $pushInfo = $this->mipush_model->getPushLogByDraw();

        if(!empty($pushInfo))
        {
            foreach ($pushInfo as $info) 
            {
                $this->doMiPush($info);
            }
        }
    }

    // 合买关注
    public function unitedFollow()
    {   
        $contine = true;
        while($contine)
        {
            //便于测试环境的调试
            if(ENVIRONMENT==='development')
            {
                $this->runOrderFollow();
            }
            else
            {
                $croname = "cron/cli_xmipush runOrderFollow";
                system("{$this->php_path} {$this->cmd_path} $croname",  $status);
            }
            sleep(5);
        }
    }

    public function runOrderFollow()
    {
        $pushInfo = $this->mipush_model->getPushLogByFollow();

        if(!empty($pushInfo))
        {
            foreach ($pushInfo as $info) 
            {
                $this->doMiPush($info);
            }
        }
    }

    public function doMiPush($info)
    {
        // 测试环境限制推送
        // if(ENVIRONMENT !== 'production')
        // {
        //     return true;
        // }

        $this->load->library('mipush');
        $config = $this->mipush->getConfig();
        $appSecret = $config[$info['platform']]['appSecret'];

        if(!empty($info) && !empty($config) && !empty($appSecret))
        {
            $parmas = array(
                'MIPUSHJSON' => array(
                    'appSecret' => $appSecret,
                )
            );
            $pushRes = $this->tools->request($info['url'], $parmas);
            $pushRes = json_decode($pushRes, true);

            $messageId = $pushRes['data']['id'] ? $pushRes['data']['id'] : '';
            if($pushRes['result'] != 'ok' || empty($messageId))
            {
                // 推送失败
                log_message('LOG', "小米推送 - 返回参数: " . print_r($pushRes, true), 'app_mipush');
            }
        }

        $this->mipush_model->updatePushStatus($info['id'], $messageId);
    }

    // 回调函数
    public function pushCallback($params)
    {
        $response = json_decode($params['response'], true);
        print_r($response);exit;
        $messageId = $response['data']['id'] ? $response['data']['id'] : '';

        if(!empty($params) && $response['result'] == 'ok' && !empty($messageId))
        {
            $this->load->library('process/send_base', array('host'=>'172.16.0.39', 'port'=>10246));
            $data = array(
                'model'     =>  'worker_model',
                'method'    =>  'execute',
                'errnum'    =>  0,
                'params'    =>  array(
                    'db'    =>  'DB',
                    'sql'   =>  'update cp_push_log set status = status + 1, messageId = ? where id = ?',
                    'data'  =>  array('messageId' => $messageId, 'id' => $params['id'])
                )
            );

            $s = json_encode($data);
            $this->send_base->send($s);
        }
        else
        {
            // 推送失败 TODO
        }
    }
}