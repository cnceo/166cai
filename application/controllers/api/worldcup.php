<?php

/*
 * 2018年俄罗斯世界杯数据接口
 * @date:2018-06-06
 */
class WorldCup extends CI_Controller 
{
    //允许的IP
    private $_allowIps = array(
        '180.169.86.54',
        '58.246.134.171',
        '58.246.134.172',
        '61.152.153.74',
        '61.152.165.42',
        '61.152.165.43',
        '183.136.203.133',
    );

    public function __construct() 
    {
        parent::__construct();
        $this->load->model('api_model');
        $this->load->driver('cache', array('adapter' => 'redis'));
    }

    //按照场次顺序返回
    public function worldCupInfo()
    {
        $now = $this->input->get('time', true) ? date('Y-m-d H:i:s', strtotime($this->input->get('time', true))) : date('Y-m-d H:i:s');

        $ip = $this->_getRequestIp();
        if(!in_array($ip, $this->_allowIps)) {
            $res = array(
                'status' => FALSE,
                'msg' => '请求IP不合法',
                'data' => ''
            );
            echo json_encode($res);
            exit;
        }

        $courseInfo = json_decode($this->cache->redis->get('worldcup_course'), TRUE);

        if (empty($courseInfo)) {
            $courseInfo = $this->api_model->getWorldCupCourse();
            $this->cache->redis->save('worldcup_course', json_encode($courseInfo), 0);
        }

        $returnInfo = array();
        foreach ($courseInfo as $one) {
            if ($now >= $one['period_start_time'] && $now < $one['period_end_time']) {
                $one = $this->_timeFormat($now, $one);
                array_push($returnInfo, array('play_time'=>$one['play_time'], 'home_team'=>$one['home_team'], 'away_team'=>$one['away_team'], 'link'=>$one['link']));
            }
        }

        $res = array(
            'status' => TRUE,
            'msg' => '俄罗斯世界杯数据请求成功',
            'data' => $returnInfo
        );
        echo json_encode($res);
        exit;
    }

    private function _getRequestIp()
    {
        $ip = '';
        if(getenv('HTTP_CLIENT_IP')) {
            $ip = getenv('HTTP_CLIENT_IP');
        } elseif(getenv('HTTP_X_FORWARDED_FOR')) {
            $ip = getenv('HTTP_X_FORWARDED_FOR');
        } elseif(getenv('REMOTE_ADDR')) {
            $ip = getenv('REMOTE_ADDR');
        } else {
            $ip = $HTTP_SERVER_VARS['REMOTE_ADDR'];
        }
        return $ip;
    }

    private function _timeFormat($now, $one)
    {
        $nowDate = date('Y-m-d', strtotime($now));
        $oneDate = date('Y-m-d', strtotime($one['play_time']));
        $diff = (strtotime($oneDate) - strtotime($nowDate))/(24*60*60);
        switch ($diff) {
            case '0':
                $one['play_time'] = date('H:i', strtotime($one['play_time']));
                break;
            case '1':
                $one['play_time'] = '次日 '.date('H:i', strtotime($one['play_time']));
                break;
            
            default:
                $one['play_time'] = ltrim(date('m', strtotime($one['play_time'])), '0').'月'.ltrim(date('d', strtotime($one['play_time'])), '0').'日 '.date('H:i', strtotime($one['play_time']));
                break;
        }
        return $one;
    }
}
