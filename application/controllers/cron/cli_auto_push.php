<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/*
 * 半自动化推送
 * @date:2018-01-10
 */

class Cli_Auto_Push extends MY_Controller
{
	public function __construct()
    {
        parent::__construct();
        $this->load->model('autopush_model');
        $this->load->library('mipush');
    }

    // 配置
    private $ctypeArr = array(
        '1' =>  array(
            'func'      =>  'unregister',   // 未注册
            'platform'  =>  '',
            'fields'    =>  'regNum',
        ),
        '2' =>  array(
            'func'      =>  'unauth',       // 未实名（网页、M版）
            'platform'  =>  '0,3',
            'fields'    =>  'authNum',
        ),
        '3' =>  array(
            'func'      =>  'unauth',       // 未实名（安卓）
            'platform'  =>  '1',
            'fields'    =>  'authNum',
        ),
        '4' =>  array(
            'func'      =>  'unauth',       // 未实名（苹果）
            'platform'  =>  '2',
            'fields'    =>  'authNum',
        ),
        '5' =>  array(
            'func'      =>  'unwithdraw',   // 未充值（网页、M版）
            'platform'  =>  '0,3',
            'fields'    =>  'recNum',
        ),
        '6' =>  array(
            'func'      =>  'unwithdraw',   // 未充值（安卓）
            'platform'  =>  '1',
            'fields'    =>  'recNum',
        ),
        '7' =>  array(
            'func'      =>  'unwithdraw',   // 未充值（苹果）
            'platform'  =>  '2',
            'fields'    =>  'recNum',
        ),
    );

    // 脚本 - 每日定时分发任务
    public function handlePushList()
    {
        $config = $this->autopush_model->getPushConfig();
        if(!empty($config))
        {
            //入库
            $fields = array('pdate', 'ctype', 'cid', 'topic', 'config', 'ptype', 'rid', 'rname', 'title', 'content', 'action', 'url', 'created');
            $bdata['s_data'] = array();
            $bdata['d_data'] = array();

            $nextDate = date('Y-m-d', strtotime('+1 day'));
            // $nextDate = date('Y-m-d');
            foreach ($config as $items) 
            {
                // 推送配置
                $configArr = json_decode($items['config'], true);
                if(!empty($configArr))
                {
                    for ($i = 0; $i <= count($configArr); $i++) 
                    { 
                        if($i > 0 && empty($configArr[$i - 1]['time']))
                        {
                            continue;
                        }

                        $conf = $items['config'];
                        if($i > 0)
                        {
                            $conf = json_encode($configArr[$i - 1]);
                        }
                        array_push($bdata['s_data'], "(?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, now())");
                        array_push($bdata['d_data'], $nextDate);
                        array_push($bdata['d_data'], $items['id']);
                        array_push($bdata['d_data'], $i);
                        array_push($bdata['d_data'], $items['topic']);
                        array_push($bdata['d_data'], $conf);
                        array_push($bdata['d_data'], $items['ptype']);
                        array_push($bdata['d_data'], $items['rid']);
                        array_push($bdata['d_data'], $items['rname']);
                        array_push($bdata['d_data'], $items['title']);
                        array_push($bdata['d_data'], $items['content']);
                        array_push($bdata['d_data'], $items['action']);
                        array_push($bdata['d_data'], $items['url']);
                    }
                }
            }

            if(!empty($bdata['s_data']))
            {
                $this->autopush_model->insertPushList($fields, $bdata);
            }
        }
    }

    // 脚本 - 推送用户扫描
    public function scanUserInfo()
    {
        $info = $this->autopush_model->getPushTask();
        if(!empty($info))
        {
            // 汇总任务
            $tasks = array();
            foreach ($info as $items) 
            {
                if($items['cid'] > 0)
                {
                    $data = array(
                        'listId'    =>  $items['id'],
                        'cid'       =>  $items['cid'],
                        'config'    =>  $items['config'],
                        'pdate'     =>  $items['pdate'],
                    );
                    $tasks[$items['ctype']]['list'][] = $data;
                    $tasks[$items['ctype']]['lastId'] = $items['lastId'];
                }
            }

            foreach ($tasks as $ctype => $items) 
            {
                $params = $this->ctypeArr[$ctype];
                if(!empty($params))
                {
                    $params['ctype'] = $ctype;
                    $params['list'] = $items['list'];
                    $params['lastId'] = $items['lastId'];
                    $func = 'handle' . $params['func'];
                    $this->$func($params);
                }
            }
        }
    }

    // 领红包未注册
    public function handleunregister($params)
    {
        $info = $this->autopush_model->getRegisterRedpack($params['lastId']);
        if(!empty($info) && !empty($params['list']))
        {
            //入库
            $fields = array('pdate', 'ctype', 'cid', 'listId', 'uid', 'phone', 'startTime', 'created');
            $bdata['s_data'] = array();
            $bdata['d_data'] = array();

            foreach ($info as $items) 
            {
                foreach ($params['list'] as $detail) 
                {
                    $config = json_decode($detail['config'], true);
                    $startTime = $this->getStartTime($config['time']);
                    if(!empty($startTime))
                    {
                        array_push($bdata['s_data'], "(?, ?, ?, ?, ?, ?, ?, now())");
                        array_push($bdata['d_data'], $detail['pdate']);
                        array_push($bdata['d_data'], $params['ctype']);
                        array_push($bdata['d_data'], $detail['cid']);
                        array_push($bdata['d_data'], $detail['listId']);
                        array_push($bdata['d_data'], '');
                        array_push($bdata['d_data'], $items['phone']);
                        array_push($bdata['d_data'], $startTime);
                    }
                }
            }
            if(!empty($bdata['s_data']))
            {
                $this->autopush_model->insertPushDetail($fields, $bdata);
            }

            // 更新 config 表 lastId
            $lastInfo = end($info);
            $this->autopush_model->updateLastId($params['ctype'], $lastInfo['id']);
        }
    }

    // 注册未实名
    public function handleunauth($params)
    {
        $info = $this->autopush_model->getRegisterAuth($params['lastId'], $params['platform']);
        if(!empty($info) && !empty($params['list']))
        {
            //入库
            $fields = array('pdate', 'ctype', 'cid', 'listId', 'uid', 'phone', 'startTime', 'created');
            $bdata['s_data'] = array();
            $bdata['d_data'] = array();

            foreach ($info as $items) 
            {
                foreach ($params['list'] as $detail) 
                {
                    $config = json_decode($detail['config'], true);
                    $startTime = $this->getStartTime($config['time']);
                    if(!empty($startTime))
                    {
                        array_push($bdata['s_data'], "(?, ?, ?, ?, ?, ?, ?, now())");
                        array_push($bdata['d_data'], $detail['pdate']);
                        array_push($bdata['d_data'], $params['ctype']);
                        array_push($bdata['d_data'], $detail['cid']);
                        array_push($bdata['d_data'], $detail['listId']);
                        array_push($bdata['d_data'], $items['uid']);
                        array_push($bdata['d_data'], $items['phone']);
                        array_push($bdata['d_data'], $startTime);
                    }
                }
            }

            if(!empty($bdata['s_data']))
            {
                $this->autopush_model->insertPushDetail($fields, $bdata);
            }

            // 更新 config 表 lastId
            $lastInfo = end($info);
            $this->autopush_model->updateLastId($params['ctype'], $lastInfo['id']);
        }
    }

    // 实名未购彩
    public function handleunwithdraw($params)
    {
        // 检查当天是否已执行
        $lastId = date('Ymd');
        if($params['lastId'] >= $lastId)
        {
            return;
        }
        $info = $this->autopush_model->getAuthWithdraw($params['platform']);
        if(!empty($info) && !empty($params['list']))
        {
            //入库
            $fields = array('pdate', 'ctype', 'cid', 'listId', 'uid', 'phone', 'startTime', 'created');
            $bdata['s_data'] = array();
            $bdata['d_data'] = array();

            foreach ($info as $items) 
            {
                foreach ($params['list'] as $detail) 
                {
                    $config = json_decode($detail['config'], true);
                    $startTime = $this->getStartTime($config['time'], 1);
                    if(!empty($startTime))
                    {
                        array_push($bdata['s_data'], "(?, ?, ?, ?, ?, ?, ?, now())");
                        array_push($bdata['d_data'], $detail['pdate']);
                        array_push($bdata['d_data'], $params['ctype']);
                        array_push($bdata['d_data'], $detail['cid']);
                        array_push($bdata['d_data'], $detail['listId']);
                        array_push($bdata['d_data'], $items['uid']);
                        array_push($bdata['d_data'], $items['phone']);
                        array_push($bdata['d_data'], $startTime);
                    }
                }
            }

            if(!empty($bdata['s_data']))
            {
                $this->autopush_model->insertPushDetail($fields, $bdata);
            }
        }
        // 更新 config 表 lastId
        $this->autopush_model->updateLastId($params['ctype'], $lastId);
    }

    // 检查启动时间
    public function getStartTime($time, $type = 0)
    {
        $startTime = '';
        if(!empty($time))
        {
            if(strpos($time, '-') != FALSE)
            {
                $timeArr = explode('-', $time);
                $hourArr = explode(':', $timeArr[1]);
                $hour = $hourArr[0] ? str_pad(trim($hourArr[0]), 2, "0", STR_PAD_LEFT) : '00';
                $min = $hourArr[1] ? str_pad(trim($hourArr[1]), 2, "0", STR_PAD_LEFT) : '00';
                // 实名未购彩的购彩时间调整为当天
                $next = $type ? ($timeArr[0] - 1) : $timeArr[0];
                $startTime = date('Y-m-d', strtotime("+{$next} day")) . ' ' . $hour . ':' . $min . ':' . '00';
            }
            else
            {
                $startTime = date('Y-m-d H:i:s');
            }
        }
        return $startTime;
    }

    // 脚本 - 推送/短信通知用户
    public function pushToUser()
    {
        $info = $this->autopush_model->getPushToUser();
        while(!empty($info)) 
        {
            foreach ($info as $items)
            {
                $this->handleUserPush($items);
            }
            $info = $this->autopush_model->getPushToUser();
        }
    }

    // 单用户通知
    public function handleUserPush($params)
    {
        // 检查用户是否有效
        $func = 'check' . $this->ctypeArr[$params['ctype']]['func'];
        $checkRes = $this->$func($params);
        if($checkRes)
        {
            // 检查第一次且是否有红包
            if(!empty($params['rid']) && !empty($params['uid']) && $params['cid'] == '1')
            {
                $this->sendRedpack($params['uid'], $params['rid']);
            }

            // 分场景推送
            $this->noticeByType($params);

            // 更新推送成功
            $this->autopush_model->updateNoticeUser($params);
            // 更新推送记录表涉及人数
            $this->autopush_model->updateListInfo($params);
            // 更新统计表
            $tplData = array(
                'pdate'     =>  $params['pdate'],
                'ctype'     =>  $params['ctype'],
                'cid'       =>  $params['cid'],
                'listId'    =>  $params['listId'],
                'uid'       =>  $params['uid'] ? $params['uid'] : 0,
                'phone'     =>  $params['phone'],
            );
            $this->autopush_model->recordUserTpl($tplData);
        }
        else
        {
            // 已失效
            $this->autopush_model->updateExpiredUser($params);
        }
    }

    // 检查 - 领红包未注册
    public function checkunregister($params)
    {
        $info = $this->autopush_model->getUserByPhone($params['phone']);
        if(!empty($info))
        {
            return false; 
        }
        else
        {
            return true;
        }
    }

    // 检查 - 注册未实名
    public function checkunauth($params)
    {
        $info = $this->autopush_model->getUserByUid($params['uid']);
        if(!empty($info) && $info['bind_id_card_time'] > '0000-00-00 00:00:00')
        {
            return false;
        }
        else
        {
            return true;
        }
    }

    // 检查 - 实名未购彩
    public function checkunwithdraw($params)
    {
        $info = $this->autopush_model->getUserByUid($params['uid']);
        if(!empty($info) && !empty($info['id']))
        {
            return false;
        }
        else
        {
            return true;
        }
    }

    // 派发红包
    public function sendRedpack($uid, $rid = 0)
    {
        $redpack = $this->autopush_model->getRedpackInfo($rid);
        if(!empty($redpack))
        {
            $use_params = json_decode($redpack['use_params'], true);
            $start = "+ " . $use_params['start_day'] . " day";
            $valid_start = date('Y-m-d H:i:s', strtotime($start));
            $end = "+ " . $use_params['end_day'] . " day";
            $valid_end = date('Y-m-d H:i:s', strtotime($end, strtotime($valid_start)));
            $redpackData = array(
                'aid'           =>  $redpack['aid'],
                'platform_id'   =>  '0',
                'channel_id'    =>  '0',
                'uid'           =>  $uid,
                'rid'           =>  $redpack['id'],
                'valid_start'   =>  $valid_start,
                'valid_end'     =>  $valid_end,
                'get_time'      =>  date('Y-m-d H:i:s'),
                'status'        =>  1,      // 已激活
            );

            // 记录红包
            $redpackRes = $this->autopush_model->recordRedpack($redpackData);
        }
    }

    // 按场景推送
    public function noticeByType($params)
    {
        switch ($params['ptype']) 
        {
            case '0':
                $this->sendSms($params);
                break;
            case '1':
                $this->sendPush($params);
                break;
            default:
                # code...
                break;
        }
    }

    // 短信通知
    public function sendSms($params)
    {
        if(!empty($params['phone']))
        {
            $info = array(
                'uid'   =>  $params['uid'],
                'phone' =>  $params['phone'],
                'msg'   =>  $params['content'],
            );
            $this->autopush_model->sendSms($info);
        }
    }

    // 推送通知
    public function sendPush($params)
    {
        /*
        $params = array(
            'id'        =>  '37',
            'ctype'     =>  '1',
            'cid'       =>  '1',
            'listId'    =>  '191',
            'uid'       =>  '0',
            'phone'     =>  '10000244446',
            'config'    =>  '{"time":"30"}',
            'ptype'     =>  '0',
            'title'     =>  '标题测试',
            'content'   =>  '内容测试',
            'action'    =>  '0',
            'url'       =>  '',
        );
        */

        if(!empty($params['uid']))
        {
            switch ($params['action']) 
            {
                case '0':
                    // 打开APP
                    $pushData = array(
                        'type'          =>  'open_app',
                        'uid'           =>  $params['uid'],
                        'title'         =>  $params['title'],
                        'content'       =>  $params['content'],
                        'time_to_live'  =>  10 * 60 * 1000,     // 默认十分钟
                    );
                    $pushType = 'user_com';
                    break;
                case '1':
                    // 红包页面
                    $pushData = array(
                        'type'          =>  'redpack_use',
                        'uid'           =>  $params['uid'],
                        'title'         =>  $params['title'],
                        'content'       =>  $params['content'],
                        'time_to_live'  =>  10 * 60 * 1000,     // 默认十分钟
                    );
                    $pushType = 'user';
                    break;
                case '2':
                    // 打开指定URL
                    $pushData = array(
                        'type'          =>  'open_url',
                        'uid'           =>  $params['uid'],
                        'title'         =>  $params['title'],
                        'content'       =>  $params['content'],
                        'app_url'       =>  $params['url'],
                        'ios_url'       =>  $params['url'],
                        'time_to_live'  =>  10 * 60 * 1000,     // 默认十分钟
                    );
                    $pushType = 'user_com';
                    break;
                default:
                    $pushData = array();
                    break;
            }
            
            if(!empty($pushData))
            {
                $this->mipush->index($pushType, $pushData);
            }
        } 
    }

    // 脚本 - 统计推送效果
    public function analysisAutoPush()
    {
        // 删除五天前或者已统计的临时表数据
        $this->autopush_model->deletePushTpl();

        // 查询已注册的用户
        $users = $this->autopush_model->getRegisterUser();
        if(!empty($users))
        {
            foreach ($users as $items) 
            {
                // 更新临时表状态
                $this->autopush_model->updateTplDetail($items);

                // 更新list表统计
                $this->autopush_model->updateListDetail($items, $this->ctypeArr[$items['ctype']]['fields']);
            }
        }

        // 查询已实名的用户
        $users = $this->autopush_model->getAuthUser();
        if(!empty($users))
        {
            foreach ($users as $items) 
            {
                // 更新临时表状态
                $this->autopush_model->updateTplDetail($items);

                // 更新list表统计
                $this->autopush_model->updateListDetail($items, $this->ctypeArr[$items['ctype']]['fields']);
            }
        }

        // 查询已购彩的用户
        $users = $this->autopush_model->getRechargeUser();
        if(!empty($users))
        {
            foreach ($users as $items) 
            {
                // 更新临时表状态
                $this->autopush_model->updateTplDetail($items);

                // 更新list表统计
                $this->autopush_model->updateListDetail($items, $this->ctypeArr[$items['ctype']]['fields']);
            }
        }
    }
}