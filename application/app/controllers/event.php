<?php

/*
 * APP 长期活动页
 * @date:2016-08-24
 */

class Event extends MY_Controller {

	public function __construct() 
    {
        parent::__construct();
        $this->load->model('user_model');
    }

    /*
     * 热门单关投注 - 入口
     * @date:2016-08-24
     */
    public function hotJczq()
    {
        $matches = $this->getJczqMatch();
        $hotMatch = $this->getHotMatch($matches, 1);

        $matchInfo = array();
        if(!empty($hotMatch[0]))
        {
            // 优先玩法
            $playType = $this->getJczqPlayType($hotMatch[0]);
            $playType = ($playType)?$playType:'spf';

            $weekDays = array('周日', '周一', '周二', '周三', '周四', '周五', '周六');
            $matchInfo = array(
                'items' => array(
                    'mid' => $hotMatch[0]['mid'],
                    'lname' => $hotMatch[0]['nameSname'],
                    'time' => $weekDays[date('w', strtotime($hotMatch[0]['issue']))] . ' ' . date('H:i',substr($hotMatch[0]['jzdt'], 0, 10)),
                    'hname' => $hotMatch[0]['homeSname'],
                    'aname' => $hotMatch[0]['awarySname'],
                    'rq' => ($playType == 'rqspf')?$hotMatch[0]['let']:'',
                    'type' => array('胜', '平', '胜'),
                    'odds' => array($hotMatch[0][$playType . 'Sp3'], $hotMatch[0][$playType . 'Sp1'], $hotMatch[0][$playType . 'Sp0']),
                    'class' => array(0, 0, 0),
                ),
                'mul' => array(
                    'num' => array(1, 5, 10, 20, 50, 100, 200),
                    'class' => 1,
                ),
                'timestamp' => false,
                'olList' => $this->getOlList($hotMatch[0]),
            );

        }
        $this->load->view('/event/hotJczq', array('matchInfo' => json_encode($matchInfo)));
    }

    /*
     * 热门单关投注 - 获取竞彩足球对阵信息
     * @date:2016-08-24
     */
    public function getJczqMatch()
    {
        // 竞足投注缓存
        $REDIS = $this->config->item('REDIS');
        $this->load->driver('cache', array('adapter' => 'redis'));
        $matches =  json_decode($this->cache->redis->get($REDIS['JCZQ_MATCH']), TRUE);
        return $matches;
    }

    /*
     * 热门单关投注 - 获取热门赛事
     * @date:2016-08-24
     */
    public function getHotMatch($matches = array(), $num = 1)
    {
        $matchInfo = array();
        $hotMatch = array();
        $count = 0;
        if(!empty($matches))
        {
            foreach ($matches as $match) 
            {
                if( empty($match['spfGd']) && empty($match['rqspfGd']) && empty($match['bqcGd']) && empty($match['jqsGd']) && empty($match['bfGd']) && empty($match['spfFu']) && empty($match['rqspfFu']) && empty($match['bqcFu']) && empty($match['jqsFu']) && empty($match['bfFu']) )
                {
                    // 过滤该场比赛
                }
                else
                {
                    if($match['hot'] == '1')
                    {
                        if($match['hotid'] == 0)
                        {
                            $match['hotid'] = 10;
                        }
                        $hotIdArry[] = $match['hotid'];
                        array_push($hotMatch, $match);
                    }
                }
            }

            if(!empty($hotMatch))
            {
                // 排序
                array_multisort($hotIdArry, SORT_ASC, $hotMatch);
         
                foreach ($hotMatch as $items) 
                {
                    if( (!empty($items['spfGd']) && !empty($items['spfFu'])) || (!empty($items['rqspfGd']) && !empty($items['rqspfFu'])) )
                    {
                        array_push($matchInfo, $items);
                        break;
                    }
                }
            }

            // 取默认最近一场比赛
            if(empty($matchInfo))
            {
                foreach ($matches as $val) 
                {
                    if(!empty($val['spfGd']) && !empty($val['spfSp3']) && !empty($val['spfSp1']) && !empty($val['spfSp0']))
                    {
                        $matchInfo[0] = $val;
                        break;
                    }
                }
            }
        }

        return $matchInfo;
    }

    /*
     * 热门单关投注 - 获取对阵历史
     * @date:2016-08-24
     */
    public function getOlList($match)
    {
        $this->load->model('cache_model','Cache');
        $matchData = $this->Cache->getJcMatchHistory();

        $list1 = '<em>历史交战：</em>';
        $list2 = '<em>近期状态：</em>';
        $list3 = '<em>国际排名：</em>';

        $info = $matchData[$match['mid']];
        if(!empty($info))
        {
            // 历史交战
            if(!empty($info['his']))
            {
                $hisArr = explode(',', $info['his']);
                $count = $hisArr[0] + $hisArr[1] + $hisArr[2];
                if($count > 0)
                {
                    $list1 .= '近' . $count . '场比赛，' . $match['homeSname'] . $hisArr[0] . '胜' . $hisArr[1] . '平' . $hisArr[2] . '负，进' . $hisArr[3] . '球，失' . $hisArr[4] . '球';
                }
                else
                {
                    $list1 .= '暂无';
                }
            }
            else
            {
                $list1 .= '暂无';
            }

            // 近期状态
            if(!empty($info['hstate']) && !empty($info['astate']))
            {
                $hstateArr = explode(',', $info['hstate']);
                $astateArr = explode(',', $info['astate']);
                $list2 .= $match['homeSname'] . $hstateArr[0] . '胜' . $hstateArr[1] . '平' . $hstateArr[2] . '负，';
                $list2 .= $match['awarySname'] . $astateArr[0] . '胜' . $astateArr[1] . '平' . $astateArr[2] . '负';
            }
            else
            {
                $list2 .= '暂无';
            }

            // 国际排名
            if(!empty($info['hrank']) && !empty($info['arank']))
            {
                $list3 .= $match['homeSname'] . $info['hrank'] . '，' . $match['awarySname'] . $info['arank'];
            }
        }
        
        return array($list1, $list2, $list3);
    }

    /*
     * 热门单关投注 - 投注
     * @date:2016-08-24
     */
    public function doBetJczq()
    {
        $mid = $this->input->post('mid', true);
        $spf = $this->input->post('spf', true);
        $mul = $this->input->post('mul', true);
        $money = $this->input->post('money', true);

        // 参数检查
        if(empty($mid) || $spf === '' || empty($mul) || empty($money))
        {
            $result = array(
                'status' => '0',
                'msg' => '请求参数错误',
                'data' => ''
            );
            echo json_encode($result);
            exit();
        }

        // 赛果检查
        $spfRes = array('3', '1', '0');
        $spfArr = explode(',', $spf);

        foreach ($spfArr as $items) 
        {
            if(!in_array($items, $spfRes))
            {
                $result = array(
                    'status' => '0',
                    'msg' => '请求参数错误',
                    'data' => $items
                );
                echo json_encode($result);
                exit();
            }
        }
        // 升序排序
        sort($spfArr);

        // 金额检查
        if(2*count($spfArr)*$mul != $money)
        {
            $result = array(
                'status' => '0',
                'msg' => '请求参数错误',
                'data' => ''
            );
            echo json_encode($result);
            exit();
        }

        // 登录检查
        if(empty($this->uid))
        {
            $result = array(
                'status' => '2',
                'msg' => '您尚未登录，请先登录',
                'data' => ''
            );
            echo json_encode($result);
            exit();
        }

        // 用户状态检查
        $uinfo = $this->user_model->getUserInfo($this->uid);
        if(empty($uinfo))
        {
            $result = array(
                'status' => '2',
                'msg' => '用户信息异常，请重新登录',
                'data' => ''
            );
            echo json_encode($result);
            exit();
        }

        if(isset($uinfo['userStatus']) && $uinfo['userStatus'] == '1')
        {
            $result = array(
                'status' => '0',
                'msg' => '您的账户已被注销。',
                'data' => ''
            );
            echo json_encode($result);
            exit();
        }

        if(isset($uinfo['userStatus']) && $uinfo['userStatus'] == '2')
        {
            $result = array(
                'status' => '0',
                'msg' => '您的账户已被冻结，如需解冻请联系客服。',
                'data' => ''
            );
            echo json_encode($result);
            exit();
        }

        if(empty($uinfo['real_name']) || empty($uinfo['id_card']))
        {
            $result = array(
                'status' => '0',
                'msg' => '请先完成实名认证',
                'data' => ''
            );
            echo json_encode($result);
            exit();
        }

        // 获取场次信息
        $matches = $this->getJczqMatch();
        if(empty($matches[$mid]))
        {
            $result = array(
                'status' => '0',
                'msg' => '该场次不支持此种玩法或已停售',
                'data' => ''
            );
            echo json_encode($result);
            exit();
        }

        $playType = $this->getJczqPlayType($matches[$mid]);

        if(empty($playType))
        {
            $result = array(
                'status' => '0',
                'msg' => '该场次不支持此种玩法或已停售',
                'data' => ''
            );
            echo json_encode($result);
            exit();
        }

        if(date('Y-m-d H:i:s',substr($matches[$mid]['jzdt'], 0, 10)) <= date('Y-m-d H:i:s'))
        {
            $result = array(
                'status' => '0',
                'msg' => '该场次已过投注截止时间',
                'data' => ''
            );
            echo json_encode($result);
            exit();
        }

        // 获取版本信息
        $versionInfo = $this->version;

        // 组装请求参数
        $postData = array(
            'ctype' => 'create',
            'uid' => $this->uid,
            'userName' => $uinfo['uname'],
            'codes' => $this->getJczqCodes($matches[$mid], $playType, $spfArr),
            'lid' => '42',
            'money' => trim($money),
            'multi' => trim($mul),
            'issue' => str_replace('-', '', $matches[$mid]['issue']),
            'playType' => '0',
            'betTnum' => count($spfArr),
            'isChase' => '0',
            'orderType' => '0',
            'endTime' => date('Y-m-d H:i:s',substr($matches[$mid]['jzdt'], 0, 10)),
            'codecc' => trim($mid),
            'version' => $versionInfo['appVersionName'],
            'channel' => $this->recordChannel($versionInfo['channel']),
            'buyPlatform' => $this->config->item('platform'),
        );

        if(ENVIRONMENT === 'checkout')
        {
            $orderUrl = $this->config->item('cp_host');
            $postData['HOST'] = $this->config->item('domain');
        }
        else
        {
            // $orderUrl = $this->config->item('pages_url');
            $orderUrl = $this->config->item('protocol') . $this->config->item('pages_url');
        }

        $createStatus = $this->tools->request($orderUrl . 'api/order/createOrder', $postData);
        $createStatus = json_decode($createStatus, true);

        if($createStatus['status'])
        {
            // 创建结果处理
            $payView = $this->orderComplete($createStatus['data']);
            $result = array(
                'status' => '1',
                'msg' => '创建订单成功',
                'data' => $payView
            );
        }
        else
        {
            $result = array(
                'status' => '0',
                'msg' => $createStatus['msg'],
                'data' => ''
            );
        }
        echo json_encode($result);
        exit();
    }

    /*
     * 热门单关投注 - 获取场次优先玩法
     * @date:2016-08-24
     */
    public function getJczqPlayType($match)
    {
        $playType = '';
        foreach(array('spf', 'rqspf') as $pt)
        {
            if(!empty($match[$pt . 'Fu']) && !empty($match[$pt . 'Gd']))
            {
                $playType = $pt;
                break;
            }
        }
        return $playType;
    }

    /*
     * 热门单关投注 - 组装投注串
     * @date:2016-08-24
     */
    public function getJczqCodes($match, $playType, $spfArr)
    {
        $codes = 'HH|';
        $codes .= strtoupper($playType);
        $codes .= '>';
        $codes .= $match['mid'];
        $codes .= '=';
        
        for ($i=0; $i < count($spfArr); $i++) 
        { 
            $pt = trim($spfArr[$i]);
            $codes .= $pt;
            // 让球
            if($playType == 'rqspf')
            {
                $codes .= '{' . $match['let'] . '}';
            }
            // 赔率
            $codes .= '(' . $match[$playType . 'Sp' . $pt] . ')';
            if($i < count($spfArr) - 1)
            {
                $codes .= '/';
            }
        }
        $codes .= '|1*1';
        return $codes;
    }

    /*
     * 热门单关投注 - 订单创建成功处理
     * @date:2016-08-24
     */
    public function orderComplete($data, $orderType = 0)
    {
        // 订单信息加密
        $orderDetail = $this->strCode(json_encode(array(
            'uid' => $data['uid'],
            'orderId' => $orderType ? $data['chaseId'] : $data['orderId'],
            'orderType' => $orderType
        )), 'ENCODE');

        // 跳转支付页面
        $payView = $this->config->item('pages_url') . "app/order/doPay/" . urlencode($orderDetail);
        return $payView;
    }

    /*
     * 数字彩追号不中包赔 - 入口
     * @date:2016-11-21
     */
    public function zhbzbp()
    {
        // 活动信息
        $this->load->model('activity_model');
        $activityInfo = $this->activity_model->getChaseActivity();

        // 机选号码
        $playTypeArr = array(
            '0'  => 'ssq_3',
            '1'  => 'dlt_3',
            '2'  => 'ssq_2',
            '3'  => 'dlt_2',
            '4'  => 'ssq_1',
            '5'  => 'dlt_1',
        );
        $this->load->library("jixuan");
        $this->primarysession->startSession();
        for ($i = 0; $i < 6; $i++) 
        {
            if($i%2 == 1)
            {
                // 大乐透
                $codes = $this->jixuan->getBalls('23529');
            }
            else
            {
                // 双色球
                $codes = $this->jixuan->getBalls('51');
            }
            // 存入session
            $this->primarysession->setArg('zhbzbp_' . $playTypeArr[$i], $codes);
            $betArr[$i]['type'] = $playTypeArr[$i];
            $betArr[$i]['codes'] = $codes;
            $betArr[$i]['num'] = str_replace('|', ',', $codes);
        };
        $betInfo = json_encode($betArr);
        $this->load->view('/event/zhbzbp', compact('activityInfo', 'betInfo'));
    }

    public function bzbprule()
    {
        $this->load->model('activity_model');
        $activityInfo = $this->activity_model->getChaseActivity();
        $this->load->view('/event/bzbprule', compact('activityInfo'));
    }

    public function randBall()
    {
        $playType = $this->input->post('playType', true);

        $config = array(
            'ssq' => '51',
            'dlt' => '23529'
        );
        $playTypeArr = explode('_', $playType);

        if(!empty($config[$playTypeArr[0]]))
        {
            $this->load->library("jixuan");
            $codes = $this->jixuan->getBalls($config[$playTypeArr[0]]);
            // 存入session
            $this->primarysession->startSession();
            $this->primarysession->setArg('zhbzbp_' . $playType, $codes);
            $betArr['type'] = $playType;
            $betArr['codes'] = $codes;
            $betArr['num'] = str_replace('|', ',', $codes);  

            $result = array(
                'status' => '1',
                'msg' => 'succ',
                'data' => $betArr
            );
        }
        else
        {
            $result = array(
                'status' => '0',
                'msg' => 'error',
                'data' => ''
            );
        }
        die(json_encode($result));
    }

    /*
     * 数字彩追号不中包赔 - 投注
     * @date:2016-11-21
     */
    public function doBetZhbzbp()
    {
        $playType = $this->input->post('playType', true);
        $codes = $this->input->post('codes', true);

        if(empty($playType) || empty($codes))
        {
            $result = array(
                'status' => '0',
                'msg' => '请求参数错误',
                'data' => ''
            );
            die(json_encode($result));
        }

        $config = array(
            'ssq' => '51',
            'dlt' => '23529'
        );
        $playTypeArr = explode('_', $playType);

        if(empty($config[$playTypeArr[0]]))
        {
            $result = array(
                'status' => '0',
                'msg' => '请求参数错误',
                'data' => ''
            );
            die(json_encode($result));
        }

        // 用户信息
        $userRes = $this->checkUserStatus($this->uid);

        if(!$userRes['status'])
        {
            $result = array(
                'status' => $userRes['data'],
                'msg' => $userRes['msg'],
                'data' => ''
            );
            die(json_encode($result));
        }

        // 投注信息校验
        $this->primarysession->startSession();
        $playCodes = $this->primarysession->getArg('zhbzbp_' . $playType);

        if($codes != $playCodes)
        {
            $result = array(
                'status' => '0',
                'msg' => '投注信息错误',
                'data' => '',
            );
            die(json_encode($result));
        }

        // 活动信息
        $this->load->model('activity_model');
        $activityInfo = $this->activity_model->getChaseActivityById($playTypeArr[1]);

        if(empty($activityInfo) || $activityInfo['startTime'] > date('Y-m-d H:i:s') || $activityInfo['endTime'] < date('Y-m-d H:i:s'))
        {
            $result = array(
                'status' => '0',
                'msg' => '活动已过期，感谢您的关注！',
                'data' => ''
            );
            die(json_encode($result));
        }

        // 获取版本信息
        $versionInfo = $this->version;

        // 追号信息
        $orderData = array(
            'uid'           =>  $this->uid,
            'userName'      =>  $userRes['data']['uname'],
            'lid'           =>  $config[$playTypeArr[0]],
            'type'          =>  $playTypeArr[1],
            'codes'         =>  $codes . ':1:1',
            'version'       =>  $versionInfo['appVersionName'],
            'channel'       =>  $this->recordChannel($versionInfo['channel'])
        );

        // 获取追号投注格式
        $chaseResult = $this->getChaseData($orderData);

        if(!$chaseResult['status'])
        {
            $result = array(
                'status' => '0',
                'msg' => $chaseResult['msg'],
                'data' => ''
            );
            die(json_encode($result));
        }

        // 初始化订单信息
        if(ENVIRONMENT === 'checkout')
        {
            $orderUrl = $this->config->item('cp_host');
            $chaseResult['data']['HOST'] = $this->config->item('domain');
        }
        else
        {
            $orderUrl = $this->config->item('protocol') . $this->config->item('pages_url');
        }
        
        $createStatus = $this->tools->request($orderUrl . 'api/order/createChaseOrder', $chaseResult['data']);
        $createStatus = json_decode($createStatus, true);

        if($createStatus['status'])
        {
            // 创建结果处理
            $payView = $this->orderComplete($createStatus['data'], 1);
            $result = array(
                'status' => '1',
                'msg' => '创建订单成功',
                'data' => $payView
            );
        }
        else
        {
            $result = array(
                'status' => '0',
                'msg' => $createStatus['msg'],
                'data' => ''
            );
        }

        die(json_encode($result));
    }

    /*
     * 检查用户状态
     * @date:2016-11-21
     */
    public function checkUserStatus($uid = '')
    {
        // 获取版本信息
        $versionInfo = $this->version;

        // 登录检查
        if(empty($uid))
        {
            $result = array(
                'status' => false,
                'msg' => '您尚未登录，请先登录',
                'data' => ($versionInfo['appVersionCode'] >= 9) ? '2' : '0'
            );
            return $result;
        }

        // 用户状态检查
        $uinfo = $this->user_model->getUserInfo($uid);
        if(empty($uinfo))
        {
            $result = array(
                'status' => false,
                'msg' => '用户信息异常，请重新登录',
                'data' => ($versionInfo['appVersionCode'] >= 9) ? '2' : '0'
            );
            return $result;
        }

        if(isset($uinfo['userStatus']) && $uinfo['userStatus'] == '1')
        {
            $result = array(
                'status' => false,
                'msg' => '您的账户已被注销。',
                'data' => '0'
            );
            return $result;
        }

        if(isset($uinfo['userStatus']) && $uinfo['userStatus'] == '2')
        {
            $result = array(
                'status' => false,
                'msg' => '您的账户已被冻结，如需解冻请联系客服。',
                'data' => '0'
            );
            return $result;
        }

        if(empty($uinfo['real_name']) || empty($uinfo['id_card']))
        {
            $result = array(
                'status' => false,
                'msg' => '请先完成实名认证',
                'data' => ($versionInfo['appVersionCode'] >= 9) ? '3' : '0'
            );
            return $result;
        }

        $result = array(
            'status' => true,
            'msg' => '检查通过',
            'data' => $uinfo
        );
        return $result;
    }

    /*
     * 获取追号投注格式
     * @date:2016-11-21
     */
    public function getChaseData($orderData)
    {
        // 获取期次信息
        $lotteryInfo = array(
            '51' => array(
                'lname' => 'SSQ',
                'cache' => 'SSQ_ISSUE',
                'maxIssue' => array('10', '20', '30')
            ),
            '23529' => array(
                'lname' => 'DLT',
                'cache' => 'DLT_ISSUE',
                'maxIssue' => array('10', '20', '30')
            ),
        );

        $orderData['totalIssue'] = $lotteryInfo[$orderData['lid']]['maxIssue'][$orderData['type'] - 1];

        // 检查期次信息
        if (!preg_match('/\d+$/', $orderData['totalIssue']))
        {
            $result = array(
                'status' => '0',
                'msg' => '追号期次格式错误',
                'data' => ''
            );
            return $result;
        }
        else
        {
            if($orderData['totalIssue'] <= 1)
            {
                $result = array(
                    'status' => '0',
                    'msg' => '追号信息错误',
                    'data' => ''
                );
                return $result;
            }

            if(!in_array($orderData['totalIssue'], $lotteryInfo[$orderData['lid']]['maxIssue']))
            {
                $result = array(
                    'status' => '0',
                    'msg' => '追号信息错误',
                    'data' => ''
                );
                return $result;
            }
        }

        $REDIS = $this->config->item('REDIS');
        // 当前期信息
        $caches = $this->cache->get($REDIS[$lotteryInfo[$orderData['lid']]['cache']]);
        $caches = json_decode($caches, true);

        if(!empty($caches['cIssue']))
        {
            $orderData['issue'] = $caches['cIssue']['seExpect'];
            $orderData['endTime'] = date('Y-m-d H:i:s',substr($caches['cIssue']['seFsendtime'], 0, 10));
        }
        else
        {
            $result = array(
                'status' => '0',
                'msg' => '追号信息错误',
                'data' => ''
            );
            return $result;
        }

        // 获取追号期次缓存
        $REDIS = $this->config->item('REDIS');
        $followIssues = json_decode($this->cache->hGet($REDIS['ISSUE_COMING'], $lotteryInfo[$orderData['lid']]['lname']), true);

        if(empty($followIssues))
        {
            $result = array(
                'status' => '0',
                'msg' => '追号期次获取失败',
                'data' => ''
            );
            return $result;
        }

        $index = '-1';
        foreach ($followIssues as $key => $issueData) 
        {
            if($this->getIssueFormat($issueData['issue'], $orderData['lid']) == $orderData['issue'])
            {
                $index = $key;
            }
        }

        if($index == '-1')
        {
            $result = array(
                'status' => '0',
                'msg' => '追号期次获取失败',
                'data' => ''
            );
            return $result;
        }

        $followIssues = array_slice($followIssues, $index, $orderData['totalIssue']);

        if($orderData['totalIssue'] > count($followIssues))
        {
            $result = array(
                'status' => '0',
                'msg' => '追号期次获取失败',
                'data' => ''
            );
            return $result;
        }

        // 处理追号方案格式
        $chaseDetail = $this->getChasePlan($followIssues, $orderData);

        $parmas = array(
            'uid' => $orderData['uid'],
            'userName' => $orderData['userName'],
            'buyPlatform' => $this->config->item('platform'),
            'codes' => $orderData['codes'],
            'lid' => $orderData['lid'],
            'money' => $orderData['totalIssue'] * 2,
            'playType' => 0,
            'betTnum' => 1,
            'isChase' => 0,
            'totalIssue' => $orderData['totalIssue'],
            'setStatus' => 0,
            'setMoney' => 0,
            'endTime' => $orderData['endTime'],
            'orderType' => '1',
            'chaseType' => $orderData['type'],
            'channel' => $orderData['channel'],
            'app_version' => $orderData['version'],
        );

        $parmas['chaseDetail'] = json_encode($chaseDetail);

        $result = array(
            'status' => '1',
            'msg' => '追号投注方案',
            'data' => $parmas
        );
        return $result;
    }

    /*
     * 处理追号方案格式
     * @date:2016-11-21
     */
    public function getIssueFormat($issue, $lid)
    {
        $this->load->library('libcomm');
        switch ($lid) 
        {
            case '23529':
            case '33':
            case '35':
            case '10022':
                $issue = $this->libcomm->format_issue($issue, 1, 2);
                break;
            
            default:
                $issue = $issue;
                break;
        }
        return $issue;
    }

    /*
     * 处理追号方案格式
     * @date:2016-11-21
     */
    public function getChasePlan($followIssues, $orderData)
    {
        $chaseDetail = array();
        foreach ($followIssues as $key => $items) 
        {
            $chaseDetail[$key]['issue'] = $this->getIssueFormat($items['issue'], $orderData['lid']);
            $chaseDetail[$key]['multi'] = 1;
            $chaseDetail[$key]['money'] = 2;
            $chaseDetail[$key]['award_time'] = $items['award_time'];
            $chaseDetail[$key]['endTime'] = $items['show_end_time'];
        }
        return $chaseDetail;
    }

    // 活动中心
    public function info()
    {
        $this->load->view('/event/info');
    }

    // 批量获取活动信息
    public function getEventInfo()
    {
        $page = $this->input->get('page', null);
        $number = $this->input->get('number', null);

        $page = max(1, intval($page));
        $number = intval($number) > 0 ? intval($number) : 5;

        $this->load->model('activity_model');
        $info = $this->activity_model->getAppEvent($this->config->item('platform'), $page, $number);

        $events = array();
        if(!empty($info))
        {
            $this->load->model('lottery_model', 'Lottery');
            foreach ($info as $items) 
            {
                $data = array(
                    'title'         =>  $items['title'],
                    'imgUrl'        =>  $items['path'],
                    'lid'           =>  $items['lid'] ? (string)intval($items['lid']) : '0',
                    'lname'         =>  $items['lid'] ? $this->Lottery->getEnName($items['lid']) : '',
                    'url'           =>  $items['url'],
                    'time'          =>  date('Y.m.d', strtotime($items['start_time'])) . '-' . date('Y.m.d', strtotime($items['end_time'])),
                    'expire'        =>  ($items['end_time'] < date('Y-m-d H:i:s')) ? true : false,
                );
                array_push($events, $data);
            }
        }

        $result = array(
            'status'    =>  '200',
            'msg'       =>  'success',
            'data'      =>  $events
        );
        die(json_encode($result));
    }

    // 竞彩不中包赔
    public function jcbzbp()
    {
        $this->load->model('activity_jcbzbp_model');
        $info = $this->activity_jcbzbp_model->getActivityInfo();
        $this->load->view('/event/jcbzbp', array('info' => json_encode($info)));
    }

    // 竞彩不中包赔 - 支付
    public function payJcbzbp()
    {
        $activityId = $this->input->post('activityId', true);
        $versionInfo = $this->getUserAgentInfo();

        if(empty($activityId))
        {
            $result = array(
                'status'    =>  '400',
                'msg'       =>  '请求参数错误',
                'data'      =>  ''
            );
            die(json_encode($result));
        }

        // 组装参数
        $params = array(
            'uid'           =>  $this->uid,
            'activityId'    =>  $activityId,
            'buyPlatform'   =>  $this->config->item('platform'),
            'channel'       =>  $this->recordChannel($versionInfo['channel']),
        );

        $this->load->remove_package_path();
        $this->load->model('activity_jcbzbp_model');
        $res = $this->activity_jcbzbp_model->createOrder($params);

        if($res['status'] == '200')
        {
            $orderData = array(
                'uid'       =>  $this->uid,
                'orderId'   =>  $res['data']['orderId'],
            );
            $result = array(
                'status'    =>  '200',
                'msg'       =>  '订单创建成功',
                'data'      =>  $this->orderComplete($orderData)
            );   
        }
        else
        {
            $result = array(
                'status'    =>  $res['status'],
                'msg'       =>  $res['msg'],
                'data'      =>  ''
            );
        }
        die(json_encode($result));
    }
}