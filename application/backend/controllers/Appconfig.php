<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Appconfig extends MY_Controller
{
    private $platform_arr = array(
        'android' => '安卓',
        'ios'  =>'苹果',
        'm' => 'M版'
    );
    // 日志分类
    private $platform_log = array(
        'android'   =>  '60',
        'ios'       =>  '61',
        'm'         =>  '62'
    );
    // 平台分类
    private $platformType = array(
        'android'   =>  '1',
        'ios'       =>  '2',
        'm'         =>  '3',
    );

    public function __construct()
    {
        parent::__construct();
        $this->load->model('model_appconfig', 'Appconfig');
        $this->channels = $this->Appconfig->getChannels();
    }
    
    /*
     * 轮播图启动页配置
     * @date:2017-10-16
     */
    public function banner($platform = 'android')
    {
        if($platform == 'android') {
            $this->check_capacity('13_1_1');
        } elseif ($platform == 'ios') {
            $this->check_capacity('13_2_1');
        } else {
            $this->check_capacity('13_3_1');
        }

        $postData = $this->input->post(null, true);
        
        $addInfo = $this->Appconfig->getAddInfo($platform);
        
        // 轮播图保存
        if(!empty($postData['banner']) && in_array($postData['platform'], array('android', 'ios', 'm'))) {
        	$this->checkenv($postData['env']);
            if($platform == 'android') {
                $this->check_capacity('13_1_6');
            } elseif ($platform == 'ios') {
                $this->check_capacity('13_2_6');
            } else {
                $this->check_capacity('13_3_6');
            }
            
            $bannerInfo = array();
            $mustParam = array('weight', 'imgTitle', 'imgUrl', 'start_time', 'end_time');
            foreach ($postData['banner'] as $key => $items) {
                $full = 0;
                foreach ($mustParam as $param) {
                    if (empty($items[$param])) {
                        if ($full == 0) {
                            $full = 1;
                        } elseif ($full == 2) {
                            $this->redirect("/backend/Appconfig/banner/".$platform."?notfull=1");
                        }
                    }else {
                        if ($full == 1) $this->redirect("/backend/Appconfig/banner/".$platform."?notfull=1");
                        $full = 2;
                    }
                }
                if(!empty($items['weight']) && !empty($items['imgTitle']) && !empty($items['imgUrl']) && !empty($items['start_time']) && !empty($items['end_time'])) {
                    // 轮播图竞彩足球区分玩法
                    $extra = '';
                    if(strpos($items['lid'], '-') !== FALSE && strpos($items['lid'], '42') !== FALSE) {
                        $lidArr = explode('-', $items['lid']);
                        $items['lid'] = trim($lidArr[0]);
                        $playtype = trim($lidArr[1]) ? trim($lidArr[1]) : 0;
                        $extra = json_encode(array('playType' => $playtype));      
                    }

                    $data = array(
                        'imgTitle'  =>  $items['imgTitle'],
                        'imgUrl'    =>  $items['imgUrl'],
                        'hrefUrl'   =>  $items['hrefUrl'] ? $items['hrefUrl'] : '',
                        'lid'       =>  is_numeric($items['lid']) ? $items['lid'] : '0',
                        'weight'    =>  $items['weight'],
                        'extra'     =>  $extra,
                        'channels'  =>  $items['channels'] ? $items['channels'] : '',
                        'platform'  =>  $postData['platform'],
                        'start_time'  =>  $items['start_time'],
                        'end_time'  =>  $items['end_time'],
                        'created'   =>  date('Y-m-d H:i:s')
                    );
                    array_push($bannerInfo, $data);
                    $this->syslog($this->platform_log[$platform], $this->platform_arr[$platform]."轮播图新增内容：".
                        $data['imgTitle']."，链接{$data['hrefUrl']}，彩种：{$data['lid']}，上线期限：{$data['start_time']}-{$data['end_time']}，".
                    (in_array($platform, array(1, 2)) ? "涉及渠道：{$data['channels']}" : ''));
                }
            }
            $res1 = $this->Appconfig->delAppBanner($postData['platform']);
            if(!empty($bannerInfo)) {
                $res2 = $this->Appconfig->insertAppBanner($bannerInfo);
            }
        }
        // 启动页
        if((!empty($postData['prelaod']) || !empty($postData['delcid'])) && in_array($postData['platform'], array('android', 'ios')))
        {
        	$this->checkenv($postData['env']);
            if($platform == 'android') $this->check_capacity('13_1_7');
            else $this->check_capacity('13_2_7');
            $cid = 0;
            $ids = array();
            
            foreach ($postData['prelaod'] as $key => $items) {
                // 启动页竞彩足球区分玩法
                $extra = '';
                if(strpos($items['lid'], '-') !== FALSE && strpos($items['lid'], '42') !== FALSE) {
                    $lidArr = explode('-', $items['lid']);
                    $items['lid'] = trim($lidArr[0]);
                    $playtype = trim($lidArr[1]) ? trim($lidArr[1]) : 0;
                    $extra = json_encode(array('playType' => $playtype));      
                }
                $postData['prelaod'][$key]['lid'] = $items['lid'];
                $postData['prelaod'][$key]['extra'] = $extra;
                
                // 渠道聚合字段处理
                if($platform == 'ios') {
                    if($cid != $items['cid']) {
                        if ($cid != 0 && $end_time !== '' && $this->Appconfig->checkHasQdyByTime($this->platformType[$postData['platform']], $cid, $end_time, $ids))
                            $this->redirect('/backend/Appconfig/banner/'.$platform.'?timeerror=1');
                        $channels = $items['channels'];
                        $end_time = $items['end_time'];
                        $url = $items['url'];
                        $lid = $items['lid'];
                        $cid = $items['cid'];
                    } else {
                        $postData['prelaod'][$key]['channels'] = $channels;
                        $postData['prelaod'][$key]['end_time'] = $end_time;
                        $postData['prelaod'][$key]['url'] = $url;
                        $postData['prelaod'][$key]['lid'] = $lid;
                    }
                }else {
                    if ($this->Appconfig->checkHasQdyByTime($this->platformType[$postData['platform']], $items['cid'], $items['end_time'], array($items['id'])))
                        $this->redirect('/backend/Appconfig/banner/'.$platform.'?timeerror=1');
                }
                if (!empty($items['id'])) array_push($ids, $items['id']);
                
                //非空校验
                if (empty($items['imgUrl']) || empty($postData['prelaod'][$key]['end_time'])) {
                    unset($postData['prelaod'][$key]);
                    continue;
                }
                
                $postData['prelaod'][$key]['start_time'] = date('Y-m-d H:i:s');
                $this->syslog($this->platform_log[$platform], $this->platform_arr[$platform]."启动页".(isset($items['id']) ? '更新' : '新增')."内容：".
                        $items['title']."，链接{$items['url']}，彩种：{$items['lid']}，上线期限：{$items['start_time']}-{$items['end_time']}，".
                    (in_array($platform, array(1, 2)) ? "涉及渠道：{$items['channels']}" : ''));
            }
            if ($platform == 'ios' && $end_time !== '' && $this->Appconfig->checkHasQdyByTime($this->platformType[$postData['platform']], $cid, $end_time, $ids))
                            $this->redirect('/backend/Appconfig/banner/'.$platform.'?timeerror=1');
            // 启动页入库
            // $checkfields = array('title', 'imgUrl');
            $this->recodeAppBanner($postData['prelaod'], 2, $this->platformType[$postData['platform']]);
            $this->Appconfig->updateChanners($this->platformType[$platform]);
            
            if ($postData['delcid']) $this->Appconfig->delAppBannerByCId($postData['delcid'], $this->platformType[$postData['platform']]);
            // 兼容老版本缓存
            $this->Appconfig->refreshBannerInfo(2, $this->platformType[$postData['platform']], $postData['platform']);
        }
        // 轮播图
        $addInfo = $this->Appconfig->getAddInfo($platform);
        $timeerror = $this->input->get('timeerror');
        $notfull = $this->input->get('notfull');
        // 启动页
        $preloadInfo = $this->Appconfig->getQdyList($this->platformType[$platform]);
//         print_r($preloadInfo);exit();
        $this->load->view("appconfig/banner", array('platform' => $platform, 'addInfo' => $addInfo, 'preloadInfo' => $preloadInfo, 'channels' => $this->channels[$platform], 'timeerror' => $timeerror, 'notfull' => $notfull));
    }
    
    /**
     * [checkDiff 检查差异性]
     * @author LiKangJian 2017-08-25
     * @param  [type] $ori      [description]
     * @param  [type] $new      [description]
     * @param  [type] $platform [description]
     * @param  [type] $show_key [description]
     * @param  [type] $type_str [description]
     * @return [type]           [description]
     */
    private function checkDiff($ori,$new,$platform,$show_key,$type_str='bannner')
    {
        $key = array_keys($new[0]);
        $type_arr = array(
            'bannner' => '轮播图更新内容：',
            'preload'=>'启动页更新内容：',
        );
        foreach ($ori as $k => $v) 
        {
            foreach ($key as  $v1) 
            {
                if( $v[$v1] != $new[$k][$v1] )
                {
                    $msg = $this->platform_arr[$platform].$type_arr[$type_str] . ( empty($new[$k][$show_key]) ? $v[$show_key] : $new[$k][$show_key]);
                    $msg .= ' ， 涉及渠道：' . $new[$k]['channels'];
                    $this->syslog($this->platform_log[$platform], $msg);
                }
            }
        }
    }
    /*
     * 图片上传
     * @date:2016-06-13
     */
    public function uploadbanner($platform, $index, $type = '')
    {
        if (! file_exists ( "../uploads/appconfig/" ))
        {
            mkdir ( "../uploads/appconfig/" );
        }

        if (! file_exists ( "../uploads/appconfig/{$platform}/" ))
        {
            mkdir ( "../uploads/appconfig/{$platform}/" );
        }

        if (! file_exists ( "../uploads/appconfig/{$platform}/banner/" ))
        {
            mkdir ( "../uploads/appconfig/{$platform}/banner/" );
        }
        
        $config ['upload_path'] = "../uploads/appconfig/{$platform}/banner/";
        $config ['allowed_types'] = 'jpg|png|bmp|jpeg';
        $extension = pathinfo ( $_FILES ['file'] ['name'], PATHINFO_EXTENSION );
        
        $config ['max_size'] = 10240;
        $this->load->library ( 'upload', $config );

        if ($this->upload->do_upload ( 'file' ))
        {
            $data = $this->upload->data ();
            $res = array (
                'name'  => $data ['file_name'],
                'index' => $index,
                'path'  => "//{$this->config->item('base_url')}/uploads/appconfig/{$platform}/banner/",
                'type'  => $type,
            );
            exit ( json_encode ( $res ) );
        } 
        else
        {
            $error = $this->upload->display_errors ();
            exit ( $error );
        }
    }

    /*
     * 版本控制
     * @date:2016-06-13
     */
    public function version($platform = 'android')
    {
        if($platform == 'android')
        {
            $this->check_capacity('13_1_3');
        }
        else
        {
            $this->check_capacity('13_2_3');
        }

        $postData = $this->input->post(null, true);
        if(!empty($postData))
        {
        	$this->checkenv($postData['env']);
            switch ($postData['subType']) 
            {
                case '1':
                    // 更新彩种信息
                    if($platform == 'android')
                    {
                        $this->check_capacity('13_1_8');
                    }
                    else
                    {
                        $this->check_capacity('13_2_8');
                    }
                    $this->updateLotteryInfo($postData);
                    break;
                case '2':
                    // 更新版本号
                    if($platform == 'android')
                    {
                        $this->check_capacity('13_1_9');
                    }
                    else
                    {
                        $this->check_capacity('13_2_9');
                    }
                    $this->updateVersionInfo($postData);
                    break;
                case '3':
                    // 更新彩种信息
                    if($platform == 'android')
                    {
                        $this->check_capacity('13_1_10');
                    }
                    else
                    {
                        $this->check_capacity('13_2_10');
                    }
                    $this->updateLotterySale($postData);
                    break;
                case '4':
                    // 更新启动页弹窗
                    if($platform == 'android')
                    {
                        $this->check_capacity('13_1_11');
                    }
                    else
                    {
                        $this->check_capacity('13_2_11');
                    }
                    $this->updatePopInfo($postData);
                    break;
                case '5':
                    // 更新微信登录
                    if($platform == 'android')
                    {
                        $this->check_capacity('13_1_13');
                    }
                    else
                    {
                        $this->check_capacity('13_2_13');
                    }
                    $this->updateLoadingInfo($postData);
                    break;
                
                default:
                    # code...
                    break;
            }
        }
        // 获取彩种配置信息
        $lotteryInfo = $this->Appconfig->getLotteryInfo($platform);
        $lotterys = $this->handleLottery($lotteryInfo);
        // 获取平台配置信息
        $versionInfo = $this->Appconfig->getVersionInfo($platform);
        // 根据彩种配置和平台配置获取版本彩种销售状态
        $lotteryStatus = $this->getLotteryStatus($lotterys['lotterys'], $versionInfo);
        sort($versionInfo);
        $attachFlags = array('1' => '加奖中', '2' => '副标题标红'); //定义成数组为了以后扩展

        // 首页弹框
        // $popInfo = $this->getPopInfo($platform);
        $popInfo = $this->Appconfig->getBannerInfo(3, $this->platformType[$platform]);
        if(!empty($popInfo))
        {
            foreach ($popInfo as $key => $items) 
            {
                $extra = json_decode($items['extra'], true);
                $popInfo[$key]['needLogin'] = $extra['needLogin'];
                $popInfo[$key]['appAction'] = $extra['appAction'];
                $popInfo[$key]['time'] = $extra['time'];
                $popInfo[$key]['isShow'] = $extra['isShow'] ? '1' : '0';
            }
        }
        // 启动配置项
        $loadingInfo = $this->Appconfig->getBannerInfo(4, $this->platformType[$platform]);

        $info = array(
            'platform'      => $platform,
            'lotteryInfo'   => $lotterys,
            'versionInfo'   => $versionInfo,
            'newVersion'    => end($versionInfo),
            'lotteryStatus' => $lotteryStatus,
        	'attachFlags' 	=> $attachFlags,
            'popInfo'       => $popInfo,
            'loadingInfo'   => $loadingInfo,
        );
        $this->load->library('BetCnName');
        $info['newLottery'] = $lotteryStatus[$info['newVersion']['versionCode']]['lotteryConfig'];
        // 渠道配置
        $info['channels'] = $this->channels[$platform];
        $this->load->view("appconfig/version", $info);
    }

    /*
     * 更新彩种信息
     * @date:2016-06-13
     */
    public function updateLotteryInfo($postData)
    {
        if(!empty($postData['lottery']))
        {
            $lotteryInfo = array();
            foreach ($postData['lottery'] as $key => $items) 
            {
            	$attachFlag = 0;
            	if(isset($items['attachFlag']))
            	{
            		foreach ($items['attachFlag'] as $val)
            		{
            			$attachFlag += $val;
            		}
            	}
                $data['plid'] = $items['plid'];
                $data['lid'] = $items['lid'];
                $data['platform'] = $postData['platform'];
                $data['weight'] = $items['weight'];
                $data['memo'] = $items['memo'];
                $data['delect_flag'] = $items['delect_flag'];
                $data['attachFlag'] = $attachFlag;
                $data['channels'] = $items['channels'];
                array_push($lotteryInfo, $data);
            }
           $this->Appconfig->updateLotteryInfo($lotteryInfo); 
           $this->Appconfig->freshLotteryInfo($postData['platform']);
           //写入日志
           $this->syslog($this->platform_log[$postData['platform']], $this->platform_arr[$postData['platform']].'首页彩种操作');
        }      
    }

    /*
     * 获取指定版本信息
     * @date:2016-06-13
     */
    public function choseVersion()
    {
        $appVersionCode = $this->input->post('appVersionCode', true);
        $platform = $this->input->post('platform', true);

        $result = array(
            'status' => '0',
            'msg' => '请求失败',
            'data' => ''
        );

        $info = $this->Appconfig->getVersionDetail($platform, $appVersionCode);

        if(!empty($info))
        {
            $info['upgradeVersion'] = $info['upgradeVersion']?$info['upgradeVersion']:'';
            $result = array(
                'status' => '1',
                'msg' => '请求成功',
                'data' => $info
            );
        }
        die(json_encode($result));
    }

    /*
     * 更新彩种信息
     * @date:2016-06-13
     */
    public function updateVersionInfo($postData)
    {
        if(!empty($postData['upgradeVersion']))
        {
            $tpl = array();
            $allVersion = $this->Appconfig->getVersionInfo($postData['platform']);
            $versions = array();
            if(!empty($allVersion))
            {
                foreach ($allVersion as $key => $items) 
                {
                    $versions[$items['versionName']] = $items;
                }
            }

            $versionArry = explode(',', $postData['upgradeVersion']);
            foreach ($versionArry as $key => $version) 
            {
                if(!empty($versions))
                {
                    if($versions[$version])
                    {
                        array_push($tpl, trim($version));
                    }
                }
                else
                {
                    array_push($tpl, trim($version));
                }
            }
            $upgradeVersion = implode(',', $tpl);
        }

        $versionInfo = array(
            'versionName' => $postData['appVersionName'],
            'versionCode' => $postData['appVersionCode'],
            'showAlert' => ($postData['platform'] == 'ios')?$postData['showAlert']:'0',
            'showRedpack'  => ($postData['platform'] == 'ios')?$postData['showRedpack']:'0',
            'isCheck'   => ($postData['platform'] == 'ios')?$postData['isCheck']:'0',
            'upgradeVersion'  => ($postData['platform'] == 'ios')?$upgradeVersion:'',
            'mark' => ($postData['platform'] == 'ios' && !empty($postData['mark']))?$postData['mark']:'新版本升级即可购彩！',
            'platform' => $postData['platform'],
        );
        $this->Appconfig->updateVersionInfo($versionInfo); 
        $this->Appconfig->freshVersionInfo($postData['platform']);
        $this->syslog($this->platform_log[$postData['platform']], $this->platform_arr[$postData['platform']]."新增版本操作：".$postData['appVersionName']);
    }

    /*
     * 获取彩种销售状态
     * @date:2016-06-13
     */
    public function getLotteryStatus($lotteryInfo, $versionInfo)
    {
        $lotteryStatus = array();
        if(!empty($versionInfo))
        {
            foreach ($versionInfo as $key => $version) 
            {
                // 初始化    
                $lotteryStatus[$key]['lotteryConfig'] = $this->initConfig($lotteryInfo, $version['lotteryConfig']);
            }
        }
        return $lotteryStatus;
    }

    /*
     * 初始化
     * @date:2016-06-13
     */
    public function initConfig($lotteryInfo, $lotteryConfig)
    {
        $config = array();
        $configArry = array();
        if(!empty($lotteryConfig))
        {
            $configArry = json_decode($lotteryConfig, true);
        }
        if(!empty($lotteryInfo))
        {
            foreach ($lotteryInfo as $key => $lottery) 
            {
                // 4201 竞足单关不做处理
                if(!in_array($lottery['lid'], array('4201')))
                {
                    $config[$lottery['lid']] = !empty($configArry[$lottery['lid']])?$configArry[$lottery['lid']]:'0';
                }  
            }
        }
        return $config;
    }

    /*
     * 切换版本获取彩种销售
     * @date:2016-06-13
     */
    public function choseLottery()
    {
        $appVersionCode = $this->input->post('appVersionCode', true);
        $platform = $this->input->post('platform', true);

        $result = array(
            'status' => '0',
            'msg' => '请求失败',
            'data' => ''
        );

        // 获取彩种配置信息
        $lotteryInfo = $this->Appconfig->getLotteryInfo($platform);
        $lotterys = $this->handleLottery($lotteryInfo);
        // 获取平台配置信息
        $versionInfo = $this->Appconfig->getVersionInfo($platform);
        // 根据彩种配置和平台配置获取版本彩种销售状态
        $lotteryStatus = $this->getLotteryStatus($lotterys['lotterys'], $versionInfo);

        if(!empty($lotteryStatus[$appVersionCode]['lotteryConfig']))
        {
            $result = array(
                'status' => '1',
                'msg' => '请求成功',
                'data' => $lotteryStatus[$appVersionCode]['lotteryConfig']
            );
        }
        die(json_encode($result));
    }

    /*
     * 更新版本彩种销售配置
     * @date:2016-06-13
     */
    public function updateLotterySale($postData)
    {
        if(!empty($postData['lstatus']))
        {
            $versionInfo = array(
                'versionCode' => $postData['lotteryVersionCode'],
                'lotteryConfig' => json_encode($postData['lstatus']),
                'platform' => $postData['platform'],
            );
            $this->Appconfig->updateVersionInfo($versionInfo); 
            $this->Appconfig->freshVersionInfo($postData['platform']);
            $this->syslog($this->platform_log[$postData['platform']], $this->platform_arr[$postData['platform']].'销售开关操作');
        }
    }

    /*
     * 刷新升级版本及版本销售配置
     * @date:2016-06-13
     */
    public function freshVersionInfo($platform)
    {
        $this->Appconfig->freshVersionInfo($platform);
        $versionInfo = $this->Appconfig->getVersionInfo($platform);
        var_dump($versionInfo);die;
    }

    /*
     * 开机启动页
     * @date:2016-09-26
     */
    public function preload($platform = 'android')
    {
        if($platform == 'android')
        {
            $this->check_capacity('13_1_2');
        }
        else
        {
            $this->check_capacity('13_2_2');
        }
        $postData = $this->input->post(null, true);
        if(!empty($postData['banner']) && in_array($postData['platform'], array('android', 'ios')))
        {
        	$this->checkenv($postData['env']);
            if($platform == 'android')
            {
                $this->check_capacity('13_1_7');
            }
            else
            {
                $this->check_capacity('13_2_7');
            }
            $addInfo = $this->getPreloadInfo($platform);
            $this->checkDiff($addInfo,$postData['banner'],$postData['platform'],'imgTitle','preload');
            $bannerInfo = array();
            foreach ($postData['banner'] as $key => $items) 
            {
                if(!empty($items['imgTitle']) && !empty($items['imgUrl']))
                {
                    $bannerInfo[$key]['imgTitle'] = $items['imgTitle'];
                    $bannerInfo[$key]['imgUrl'] = $items['imgUrl'];
                    $bannerInfo[$key]['hrefUrl'] = $items['hrefUrl']?$items['hrefUrl']:'';
                    $bannerInfo[$key]['lid'] = $items['lid'] ? $items['lid'] : '0';
                    $bannerInfo[$key]['isShow'] = $items['isShow']?'1':'0';
                }     
            }
            if(!empty($bannerInfo))
            {
                $this->Appconfig->freshPreloadInfo($bannerInfo, $postData['platform']);
            }
        }
        $this->load->view("appconfig/preload", array('platform' => $platform, 'addInfo' => $this->getPreloadInfo($platform) ) );
    }
    /**
     * [getPreloadInfo 获取起始页内容]
     * @author LiKangJian 2017-08-25
     * @param  [type] $platform [description]
     * @return [type]           [description]
     */
    private function getPreloadInfo($platform)
    {
        $addInfo = $this->Appconfig->getPreloadInfo($platform);
        return $addInfo ? $addInfo : array();
    }
    /*
     * 支付成功页广告运营位
     * @date:2016-12-01
     */
    public function webBanner($platform = 'android', $position = 'payResult')
    {
        if($platform == 'android')
        {
            $this->check_capacity('13_1_5');
        }
        else
        {
            $this->check_capacity('13_2_5');
        }
        $postData = $this->input->post(null, true);
        if(!empty($postData['info']) && $postData['platform'] == $platform)
        {
        	$this->checkenv($postData['env']);
            if($platform == 'android')
            {
                $this->check_capacity('13_1_12');
            }
            else
            {
                $this->check_capacity('13_2_12');
            }
            $bannerData = array();
            $this->syslog($this->platform_log[$postData['platform']], $this->platform_arr[$postData['platform']]."支付广告操作");
            foreach ($postData['info'] as $key => $items) 
            {
                if(!empty($items['path']) && !empty($items['clid']) && (!empty($items['webUrl']) || !empty($items['appAction'])))
                {
                    foreach ($items['clid'] as $k => $lid) 
                    {
                        $bannerData[$lid]['imgUrl'] = $items['path'];
                        $bannerData[$lid]['webUrl'] = $items['webUrl'];
                        $bannerData[$lid]['appAction'] = $items['appAction'];
                        $bannerData[$lid]['index'] = $key + 1;
                        $bannerData[$lid]['tlid'] = $items['tlid'];
                    }
                }
            }
            if(!empty($bannerData))
            {
                $this->Appconfig->freshPreloadInfo($bannerData, $postData['platform'], $position);
            }
            else
            {
                $this->Appconfig->delPreloadInfo($postData['platform'], $position);
            }
        }

        $bannerInfo = $this->Appconfig->getPreloadInfo($platform, $position);

        // 彩种配置
        $this->load->library('BetCnName');
        $lotteryInfo = $this->betcnname->getLotteryInfo();

        $info = array();
        if(!empty($bannerInfo))
        {
            foreach ($bannerInfo as $lid => $items) 
            {
                $index = $items['index'] - 1;
                $info[$index]['imgUrl'] = $items['imgUrl'];
                $info[$index]['webUrl'] = $items['webUrl'];
                $info[$index]['appAction'] = $items['appAction'];
                $info[$index]['clid'] = $info[$index]['clid'] ? $info[$index]['clid'] . ',' . $lid : $lid;
                $info[$index]['tlid'] = $info[$index]['tlid'] ? $info[$index]['tlid'] . ',' . $items['tlid'] : $items['tlid'];
            }
        }

        $this->load->view("appconfig/webBanner", array('platform' => $platform, 'info' => $info, 'lotteryInfo' => $lotteryInfo));
    }

    /*
     * 首页弹框
     * @date:2016-11-29
     */
    private function getPopInfo($platform = 'android')
    {
        $popInfo = $this->Appconfig->getPreloadInfo($platform, 'popload');
        return $popInfo;
    }

    /*
     * 更新首页弹框
     * @date:2016-11-29
     */
    private function updatePopInfo($postData)
    {
        if(!empty($postData['pop']))
        {
            //渠道聚合字段处理
            foreach ($postData['pop'] as $key => $items) 
            {
                if($key % 3 == 0) $channels = $items['channels'];
                $postData['pop'][$key]['channels'] = $channels;
            }
            $popInfo = array();
            // 组装参数
            foreach ($postData['pop'] as $items) 
            {
                $data = array(
                    'id'        =>  $items['id'],
                    'title'     =>  '首页弹层',
                    'imgUrl'    =>  $items['imgUrl'],
                    'url'       =>  $items['url'],
                    'lid'       =>  $items['lid'],
                    'channels'  =>  $items['channels'],
                    'status'    =>  1,
                    'extra'     =>  json_encode(array('needLogin' => $items['needLogin'], 'appAction' => $items['appAction'], 'time' => $items['time'] ? $items['time'] : 0, 'isShow' => $items['isShow'] ? '1' : '0')),
                );
                array_push($popInfo, $data);
            }

            // $checkfields = array('imgUrl', 'url', 'extra');
            $this->recodeAppBanner($popInfo, 3, $this->platformType[$postData['platform']], $checkfields);
            // 刷新缓存
            $this->Appconfig->refreshBannerInfo(3, $this->platformType[$postData['platform']], $postData['platform']);
            $this->syslog($this->platform_log[$postData['platform']], $this->platform_arr[$postData['platform']]."首页弹层广告操作");
        }
    }

    public function getSeverTime()
    {
        $result = array(
            'status' => '1',
            'msg' => '',
            'data' => date('Y-m-d H:i:s')
        );
        die(json_encode($result));
    }
    
   	public function info($platform = 'android')
   	{
   		// $this->check_capacity('6_6_7');
   		$postData = $this->input->post(null, true);
   		if(!empty($postData['info']) && $postData['platform'] == $platform)
   		{
   			$this->checkenv($postData['env']);
   			// $this->check_capacity('6_6_10');
   			$bannerData = array();
   			foreach ($postData['info'] as $key => $items)
   			{
   				if(!empty($items['path']) && !empty($items['clid']) && (!empty($items['webUrl']) || !empty($items['appAction'])))
   				{
   					foreach ($items['clid'] as $k => $lid)
   					{
   						$bannerData[$lid]['imgUrl'] = $items['path'];
   						$bannerData[$lid]['webUrl'] = $items['webUrl'];
   						$bannerData[$lid]['appAction'] = $items['appAction'];
   						$bannerData[$lid]['index'] = $key + 1;
   						$bannerData[$lid]['tlid'] = $items['tlid'];
   					}
   				}
   			}
   			if(!empty($bannerData))
   			{
   				$this->Appconfig->freshPreloadInfo($bannerData, $postData['platform'], 'info');
   			}
   			else
   			{
   				$this->Appconfig->delPreloadInfo($postData['platform'], 'info');
   			}
   			
   			$this->syslog(39, "资讯页悬浮窗广告编辑操作");
   		}
   		$this->load->model('model_info');
   		$categoryList = $this->model_info->getCategoryList();
   		$bannerInfo = $this->Appconfig->getPreloadInfo($platform, 'info');
   		
   		$info = array();
   		if(!empty($bannerInfo))
   		{
   			foreach ($bannerInfo as $lid => $items)
   			{
   				$index = $items['index'] - 1;
   				$info[$index]['imgUrl'] = $items['imgUrl'];
   				$info[$index]['webUrl'] = $items['webUrl'];
   				$info[$index]['appAction'] = $items['appAction'];
   				$info[$index]['clid'] = $info[$index]['clid'] ? $info[$index]['clid'] . ',' . $lid : $lid;
   				$info[$index]['tlid'] = $info[$index]['tlid'] ? $info[$index]['tlid'] . ',' . $items['tlid'] : $items['tlid'];
   			}
   		}
   		
   		$this->load->library('BetCnName');
   		$lotteryInfo = $this->betcnname->getLotteryInfo();
   		$con = $this->con;
   		$act = $this->act;
   		$this->load->view('/appconfig/info', compact('platform', 'categoryList', 'info', 'lotteryInfo', 'con', 'act'));
   	}
   	
   	public function uploadinfo($platform, $index)
   	{
   		if (! file_exists ( "../uploads/appconfig/" ))
   		{
   			mkdir ( "../uploads/appconfig/" );
   		}
   	
   		if (! file_exists ( "../uploads/appconfig/{$platform}/" ))
   		{
   			mkdir ( "../uploads/appconfig/{$platform}/" );
   		}
   	
   		if (! file_exists ( "../uploads/appconfig/{$platform}/info/" ))
   		{
   			mkdir ( "../uploads/appconfig/{$platform}/info/" );
   		}
   	
   		$config ['upload_path'] = "../uploads/appconfig/{$platform}/info/";
   		$config ['allowed_types'] = 'jpg|png|bmp|jpeg';
   		$extension = pathinfo ( $_FILES ['file'] ['name'], PATHINFO_EXTENSION );
   	
   		$config ['max_size'] = 10240;
   		$this->load->library ( 'upload', $config );
   	
   		if ($this->upload->do_upload ( 'file' ))
   		{
   			$data = $this->upload->data ();
   			$res = array (
   					'name'  => $data ['file_name'],
   					'index' => $index,
   					'path'  => "//{$this->config->item('base_url')}/uploads/appconfig/{$platform}/info/"
   			);
   			exit ( json_encode ( $res ) );
   		}
   		else
   		{
   			$error = $this->upload->display_errors ();
   			exit ( $error );
   		}
   	}
   	
   	public function orderdetail($platform = 'android')
   	{
   		// switch ($platform) {
   		// 	case 'ios':
   		// 		$this->check_capacity('6_6_9');
   		// 		break;
   		// 	case 'android':
   		// 	default:
   		// 		$this->check_capacity('6_6_8');
   		// 		break;
   		// }
   		
   		$postData = $this->input->post(null, true);
   		if(!empty($postData['info']) && $postData['platform'] == $platform)
   		{
   			$this->checkenv($postData['env']);
   			// switch ($platform) {
   			// 	case 'ios':
   			// 		$this->check_capacity('6_6_11');
   			// 		break;
   			// 	case 'android':
   			// 	default:
   			// 		$this->check_capacity('6_6_12');
   			// 		break;
   			// }
   			$bannerData = array();
   			foreach ($postData['info'] as $key => $items)
   			{
   				if(!empty($items['path']) && !empty($items['clid']) && (!empty($items['webUrl']) || !empty($items['appAction'])))
   				{
   					foreach ($items['clid'] as $k => $lid)
   					{
   						$bannerData[$lid]['imgUrl'] = $items['path'];
   						$bannerData[$lid]['webUrl'] = $items['webUrl'];
   						$bannerData[$lid]['appAction'] = $items['appAction'];
   						$bannerData[$lid]['index'] = $key + 1;
   						$bannerData[$lid]['tlid'] = $items['tlid'];
   					}
   				}
   			}
   			if(!empty($bannerData))
   			{
   				$this->Appconfig->freshPreloadInfo($bannerData, $postData['platform'], 'orderdetail');
   			}
   			else
   			{
   				$this->Appconfig->delPreloadInfo($postData['platform'], 'orderdetail');
   			}
   			
   			$this->syslog(39, $platform."订单广告编辑操作");
   		}
   		$bannerInfo = $this->Appconfig->getPreloadInfo($platform, 'orderdetail');
   		 
   		$info = array();
   		if(!empty($bannerInfo))
   		{
   			foreach ($bannerInfo as $lid => $items)
   			{
   				$index = $items['index'] - 1;
   				$info[$index]['imgUrl'] = $items['imgUrl'];
   				$info[$index]['webUrl'] = $items['webUrl'];
   				$info[$index]['appAction'] = $items['appAction'];
   				$info[$index]['clid'] = $info[$index]['clid'] ? $info[$index]['clid'] . ',' . $lid : $lid;
   				$info[$index]['tlid'] = $info[$index]['tlid'] ? $info[$index]['tlid'] . ',' . $items['tlid'] : $items['tlid'];
   			}
   		}
   		 
   		$this->load->library('BetCnName');
   		$lotteryInfo = $this->betcnname->getLotteryInfo();
   		$con = $this->con;
   		$act = $this->act;
   		$this->load->view('/appconfig/orderdetail', compact('platform', 'categoryList', 'info', 'lotteryInfo', 'con', 'act'));
   	}
   	
   	public function uploadorderdetail($platform, $index)
   	{
   		if (! file_exists ( "../uploads/appconfig/" ))
   		{
   			mkdir ( "../uploads/appconfig/" );
   		}
   	
   		if (! file_exists ( "../uploads/appconfig/{$platform}/" ))
   		{
   			mkdir ( "../uploads/appconfig/{$platform}/" );
   		}
   	
   		if (! file_exists ( "../uploads/appconfig/{$platform}/orderdetail/" ))
   		{
   			mkdir ( "../uploads/appconfig/{$platform}/orderdetail/" );
   		}
   	
   		$config ['upload_path'] = "../uploads/appconfig/{$platform}/orderdetail/";
   		$config ['allowed_types'] = 'jpg|png|bmp|jpeg';
   		$extension = pathinfo ( $_FILES ['file'] ['name'], PATHINFO_EXTENSION );
   	
   		$config ['max_size'] = 10240;
   		$this->load->library ( 'upload', $config );
   	
   		if ($this->upload->do_upload ( 'file' ))
   		{
   			$data = $this->upload->data ();
   			$res = array (
   					'name'  => $data ['file_name'],
   					'index' => $index,
   					'path'  => "//{$this->config->item('base_url')}/uploads/appconfig/{$platform}/orderdetail/"
   			);
   			exit ( json_encode ( $res ) );
   		}
   		else
   		{
   			$error = $this->upload->display_errors ();
   			exit ( $error );
   		}
   	}

    public function getLoadingInfo($platform)
    {
        $loadingInfo = $this->Appconfig->getPreloadInfo($platform, 'preloading');

        if(empty($loadingInfo))
        {
            $loadingInfo = array(
                0 => array(
                    'name'      =>  'isWxLogin',
                    'desc'      =>  '微信登录入口',
                    'status'    =>  '1',
                )
            );
        }
        return $loadingInfo;
    }

    public function updateLoadingInfo($postData)
    {
        if(!empty($postData['loading']))
        {
            $this->recodeAppBanner($postData['loading'], 4, $this->platformType[$postData['platform']]);
            // 刷新缓存
            $this->Appconfig->refreshBannerInfo(4, $this->platformType[$postData['platform']], $postData['platform']);

            //写入日志
            $msg = $this->platform_arr[$postData['platform']] . '微信登录调整，涉及渠道：' . $postData['loading'][0]['channels'];
            $this->syslog($this->platform_log[$postData['platform']], $msg);
        }     
    }

    // 彩种配置处理
    public function handleLottery($lotteryInfo = array())
    {
        $baseInfo = array();
        $subInfo = array();
        $info = array();
        $lotterys = array();
        if(!empty($lotteryInfo))
        {
            foreach ($lotteryInfo as $lottery) 
            {
                $plid = $lottery['plid'];
                if($plid > 0)
                {
                    $subInfo[] = $lottery;
                }
                else
                {
                    $baseInfo[] = $lottery;
                }

                if(!in_array($plid, array(2, 3)) && $lottery['lid'] > 3)
                {
                    $info[] = $lottery;
                }

                if($lottery['lid'] > 3)
                {
                    $lotterys[] = $lottery;
                }
            }
        }
        $baseInfo = array_merge($baseInfo, $subInfo);
        return compact('baseInfo', 'info', 'lotterys');
    }

    // 首页活动配置
    public function activity($platform = 'android')
    {
        if($platform == 'android')
        {
            $this->check_capacity('13_1_4');
        }
        else
        {
            $this->check_capacity('13_2_4');
        }

        $platformArr = array(
            'android'   =>  '1',
            'ios'       =>  '2'
        );

        $postData = $this->input->post(null, true);
        if(!empty($postData))
        {
        	$this->checkenv($postData['env']);
            if($platform == 'android')
            {
                $this->check_capacity('13_1_14');
            }
            else
            {
                $this->check_capacity('13_2_14');
            }

            $old = $this->Appconfig->getActivityInfo($platformArr[$platform]);
            $postData['platform'] = $platformArr[$postData['platform']];
            switch ($postData['subType']) 
            {
                case '1':
                    // 更新活动配置
                    $this->updateActivity($postData);
                    $activitys = $postData['activity'];
                    break;
                case '2':
                    // 更新底部banner
                    $this->updateActBanner($postData);
                    $activitys = $postData['activityBanner'];
                    break;
                
                default:
                    # code...
                    break;
            }

            // 日志
            $this->checkJcActivityDiff($old, $activitys, $platform);
        }

        $info = $this->Appconfig->getActivityInfo($platformArr[$platform]);
        $config = $this->getActivityConfig($info);
        // 追号不中包赔
        $this->load->view('/appconfig/activity', array('info' => $info, 'platform' => $platform, 'config' => $config, 'channels' => $this->channels[$platform]));
    }

    // 更新活动模块
    public function updateActivity($postData)
    {
        if(!empty($postData['activity']))
        {
            $bdata['s_data'] = array();
            $bdata['d_data'] = array();

            foreach ($postData['activity'] as $key => $items) 
            {
                // 自定义活动位彩种玩法
                if(in_array($items['type'], array(4, 5, 6, 7, 8)))
                {
                    $items['extra'] = $this->exchangePlayType($items['extra']);
                }
                array_push($bdata['s_data'], "(?, ?, ?, ?, ?, ?, ?, now())");
                array_push($bdata['d_data'], $items['type']);
                array_push($bdata['d_data'], $items['content']);
                array_push($bdata['d_data'], 1);
                array_push($bdata['d_data'], $postData['platform']);
                array_push($bdata['d_data'], $items['weight']);
                array_push($bdata['d_data'], $items['extra']);
                array_push($bdata['d_data'], $items['channels']);
            }
            $this->Appconfig->updateActivity($bdata); 
       }
    }

    public function updateActBanner($postData)
    {
        if(!empty($postData['activityBanner']))
        {
            $bdata['s_data'] = array();
            $bdata['d_data'] = array();

            foreach ($postData['activityBanner'] as $key => $items) 
            {
                $extra = array(
                    'imgUrl'     =>  $items['imgUrl'],
                    'url'       =>  $items['url'],
                    'lid'       =>  ($items['appAction'] == 'bet') ? $items['lid'] : '0',
                    'appAction' =>  $items['appAction'] ? $items['appAction'] : '',
                );
                $extra = json_encode($extra);
                // 自定义活动位彩种玩法
                $extra = $this->exchangePlayType($extra);

                $items['content'] = $items['content'] ? $items['content'] : '底部banner';
                array_push($bdata['s_data'], "(?, ?, ?, ?, ?, ?, ?, now())");
                array_push($bdata['d_data'], $items['type']);
                array_push($bdata['d_data'], $items['content']);
                array_push($bdata['d_data'], 1);
                array_push($bdata['d_data'], $postData['platform']);
                array_push($bdata['d_data'], 0);
                array_push($bdata['d_data'], $extra);
                array_push($bdata['d_data'], $items['channels']);
            }
            $this->Appconfig->updateActivity($bdata); 
       }
    }

    public function getActivityConfig($info)
    {
        $activity = array();
        if(!empty($info))
        {
            foreach ($info as $items) 
            {
                $type = in_array($items['type'], array(4, 5, 6, 7, 8, 9)) ? 4 : $items['type'];
                $fun = 'activity_' . $type;
                if(method_exists($this, $fun))
                {
                    $activity[$items['type']] = $this->$fun($items);
                } 
            }
        }
        return $activity;
    }

    // 追号不中包赔
    public function activity_1($data)
    {
        $extra = json_decode($data['extra'], true);
        $data = array(
            'activity'  =>  $extra,
            'config'    =>  array(
                'lotterys'  =>  array(
                    '51'    =>  '双色球',
                    '23529' =>  '大乐透',
                    '0'     =>  '自动配置'
                ),
                'content'   =>  array(
                    '30'    =>  '追30期,不中返60元',
                    '20'    =>  '追20期,不中返10元',
                    '10'    =>  '追10期,不中返2元',
                ),
            )
        );
        return $data;
    }

    // 竞彩模块
    public function activity_2($data)
    {
        $extra = json_decode($data['extra'], true);
        $data = array(
            'activity'  =>  $extra,
            'config'    =>  array(
                'lotterys'  =>  array(
                    '42'    =>  '竞彩足球',
                    '43'    =>  '竞彩篮球',
                ),
                'playtype'  =>  array(
                    '1'     =>  '单关',
                    '2'     =>  '2串1',
                ),
            )
        );
        return $data;
    }

    public function activity_3($data)
    {
        $extra = json_decode($data['extra'], true);
        $data = array(
            'activity'  =>  $extra,
            'config'    =>  array(
                'lotterys'  =>  array(
                    '51'    =>  '双色球',
                    '23529' =>  '大乐透',
                    '0'     =>  '自动配置'
                ),
            )
        );
        return $data;
    }

    public function activity_4($data)
    {
        $extra = json_decode($data['extra'], true);
        $extra['lid'] = $this->exchangeLid($extra);
        $data = array(
            'activity'  =>  $extra,
        );
        return $data;
    }

    public function activity_5($data)
    {
        $extra = json_decode($data['extra'], true);
        $extra['lid'] = $this->exchangeLid($extra);
        $data = array(
            'activity'  =>  $extra,
        );
        return $data;
    }

    public function activity_6($data)
    {
        $extra = json_decode($data['extra'], true);
        $extra['lid'] = $this->exchangeLid($extra);
        $data = array(
            'activity'  =>  $extra,
        );
        return $data;
    }

    public function checkJcActivity()
    {
        $postData = $this->input->post(null, true);

        if(!empty($postData['lid']) && !empty($postData['playtype']) && !empty($postData['mid']))
        {
            $p = 0;
            $k = 0;
            $matches = $this->Appconfig->getJjcInfo($postData['lid']);
            $midArr = explode(',', $postData['mid']);
            foreach ($midArr as $mid) 
            {
                if(!empty($matches[$mid]))
                {
                    $k ++;
                    if($postData['lid'] == '42')
                    {
                       // 单关是否开售
                        if($postData['playtype'] == '1' && $matches[$mid]['spfFu'])
                        {
                            $p ++; 
                        }
                        // 胜平负玩法是否开售
                        if($postData['playtype'] == '2' && $matches[$mid]['spfGd'])
                        {
                            $p ++; 
                        }   
                    }
                    else
                    {
                        // 竞篮胜负
                        if($postData['playtype'] == '1' && $matches[$mid]['sfFu'])
                        {
                            $p ++;
                        }
                        // 胜平负玩法是否开售
                        if($postData['playtype'] == '2' && $matches[$mid]['sfGd'])
                        {
                            $p ++; 
                        }
                    }   
                }
            }

            if(count($midArr) != $k)
            {
                $this->ajaxReturn('n', '所选场次已过投注截止时间，请确认后再次填写');
            }

            if($postData['playtype'] == '1' && count($midArr) != $p)
            {
                $this->ajaxReturn('n', '所选场次不支持单关，请确认后再次填写');
            }

            if($postData['playtype'] == '2' && count($midArr) != $p)
            {
                $this->ajaxReturn('n', '所选场次对应玩法未开售，请确认后再次填写');
            }
            $this->ajaxReturn('y', '检查成功');
        }
        else
        {
            $this->ajaxReturn('n', '缺少必要参数');
        }
    }

    public function review($platform = 'android')
    {
        $this->load->view('/appconfig/review', array('platform' => $platform));
    }

    public function checkJcActivityDiff($old, $new, $platform)
    {
        // 原数据
        $current = array();
        foreach ($old as $key => $items) 
        {
            $current[$items['type']] = $items;
        }
        // 更新数据
        $now = array();
        foreach ($new as $key => $items) 
        {
            $now[$items['type']] = $items;
        }
        $log = '';
        if(!empty($now) && !empty($current))
        {
            foreach ($now as $type => $items) 
            {
                $msg = '';
                if($items['content'] !== $current[$type]['content'])
                {
                    $msg .= $items['content'] . ' ';
                }
                if($items['status'] !== $current[$type]['status'])
                {
                    $msg .= $items['status'] ? '开启 ' : '关闭 ';
                }
                if($items['weight'] !== $current[$type]['weight'])
                {
                    $msg .= '权重:' . $items['weight'] . ' ';
                }
                if($items['extra'] !== $current[$type]['extra'])
                {
                    $msg .= '配置:' . $items['extra'] . ' ';
                }
                if($items['channels'] !== $current[$type]['channels'])
                {
                    $msg .= '涉及渠道:' . $items['channels'] . ' ';
                }
                if($msg)
                {
                    $log .= $current[$type]['title'] . $msg . '; ';
                }
            }
        }
        if($log)
        {
            $this->syslog($this->platform_log[$platform], '首屏活动配置更新内容：' . $log);  
        }
        
    }

    // M版首页弹层
    public function pop($platform = 'm')
    {
        $this->check_capacity('13_3_2');
        $postData = $this->input->post(null, true);
        if(!empty($postData['pop']))
        {
        	$this->checkenv($postData['env']);
            $popInfo = array();
            foreach ($postData['pop'] as $key => $items) 
            {
                $data = array(
                    'path'      =>  '',
                    'url'       =>  '',
                    'isShow'    =>  '0',
                    'needLogin' =>  '',
                    'appAction' =>  '',
                    'lid'       =>  '',
                    'time'      =>  '',
                );

                if(!empty($items['path']) && !empty($items['url']))
                {
                    $data = array(
                        'path'      =>  $items['path'],
                        'url'       =>  $items['url'],
                        'isShow'    =>  $items['isShow'] ? '1' : '0',
                        'needLogin' =>  '',
                        'appAction' =>  '',
                        'lid'       =>  '',
                        'time'      =>  '',
                    );
                }
                array_push($popInfo, $data);
            }
            $this->Appconfig->freshPreloadInfo($popInfo, $postData['platform'], 'popload');
            $this->syslog($this->platform_log[$postData['platform']], $this->platform_arr[$postData['platform']]."首页弹层操作");
        }
        $popInfo = $this->getPopInfo($platform);
        $this->load->view('/appconfig/pop', array('popInfo' => $popInfo, 'platform' => $platform));
    }

    public function event($platform = 'android')
    {
        if($platform == 'android')
        {
            $this->check_capacity('13_1_15');
        } elseif($platform == 'ios'){
            $this->check_capacity('13_2_15');
        }

        $platformArr = array(
            'android'   =>  '1',
            'ios'       =>  '2'
        );

        $page = intval($this->input->get("p"));
        $searchData = array(
            "platform"  =>  $platformArr[$platform],
        );

        $page = $page <= 1 ? 1 : $page;
        $result = $this->Appconfig->list_event($searchData, $page, self::NUM_PER_PAGE);

        $pageConfig = array(
            "page"     => $page,
            "npp"      => self::NUM_PER_PAGE,
            "allCount" => $result[1]
        );

        $pages = get_pagination($pageConfig);

        // 活动开关
        $eventStatus = $this->Appconfig->getEventStatus($platform);
        $eventStatus = $eventStatus ? '1' : '0';

        $this->load->view('/appconfig/event', array('pages' => $pages,'info' => $result[0], 'eventStatus' => $eventStatus, 'platform' => $platform));
    }

    public function updateEventInfo()
    {
        $postData = $this->input->post(null, true);

        if($postData['platform'] == 'android')
        {
            $this->check_capacity('13_1_16', true);
        } elseif($postData['platform'] == 'ios') {
            $this->check_capacity('13_2_16', true);
        }

        $platformArr = array(
            'android'   =>  '1',
            'ios'       =>  '2'
        );

        // 参数检查
        if(!empty($postData['weight']) && !empty($postData['title']) && !empty($postData['path']) && !empty($postData['start_time']) && !empty($postData['end_time']) && $postData['start_time'] <= $postData['end_time'] && !($postData['lid'] == 0 && empty($postData['url'])))
        {
            $data = array(
                'title'     =>  $postData['title'],
                'path'      =>  $postData['path'],
                'url'       =>  $postData['url'] ? $postData['url'] : '',
                'lid'       =>  is_numeric($postData['lid']) ? $postData['lid'] : '0',
                'weight'    =>  intval($postData['weight']),
                'platform'  =>  $platformArr[$postData['platform']],
                'start_time'=>  $postData['start_time'],
                'end_time'  =>  $postData['end_time'],
            );

            if($postData['id'])
            {
                // 更新
                $data['id'] = $postData['id'];
                $this->Appconfig->updateEventInfo($data);
                //写入日志
                $this->syslog($this->platform_log[$postData['platform']], '更新活动信息：标题' . $data['title'] . '；图片' . $data['path'] . '；链接' . $data['url'] . '；彩种' . $data['lid'] . '；权重' . $data['weight'] . '；开始时间' . $data['start_time'] . '；结束时间' . $data['end_time']);
            }
            else
            {
                // 插入
                $this->Appconfig->recodeEventInfo($data);
                //写入日志
                $this->syslog($this->platform_log[$postData['platform']], '新建活动信息：标题' . $data['title'] . '；图片' . $data['path'] . '；链接' . $data['url'] . '；彩种' . $data['lid'] . '；权重' . $data['weight'] . '；开始时间' . $data['start_time'] . '；结束时间' . $data['end_time']);
            }
            $this->ajaxReturn('y', '修改成功');
        }
        else
        {
            $this->ajaxReturn('n', '参数错误');
        }
    }

    public function delEventInfo()
    {
        $postData = $this->input->post(null, true);

        if($postData['platform'] == 'android')
        {
            $this->check_capacity('13_1_16', true);
        } elseif($postData['platform'] == 'ios') {
            $this->check_capacity('13_2_16', true);
        }

        if(!empty($postData['id']))
        {
            $this->Appconfig->delEventInfo($postData['id']);
            //写入日志
            $this->syslog($this->platform_log[$postData['platform']], '删除活动信息 id：' . $postData['id']);
            $this->ajaxReturn('y', '修改成功');
        }
        else
        {
            $this->ajaxReturn('n', '参数错误');
        }
    }

    public function updateEventStatus()
    {
        $platform = $this->input->post('platform', true);
        $status = $this->input->post('status', true);
        $status = $status ? '1' : '0';
        $statusMsg = $status ? '开启' : '关闭';
        if($platform == 'android')
        {
            $this->check_capacity('13_1_17', true);
        }elseif($platform == 'ios'){
            $this->check_capacity('13_2_17', true);
        }

        $this->Appconfig->refreshEventStatus($status, $platform);
        //写入日志
        $this->syslog($this->platform_log[$platform], '活动中心入口' . $statusMsg);
        $this->ajaxReturn('y', '修改成功');
    }

    /**
     * [jfShop 积分商城banner管理]
     * @author LiKangJian 2018-01-02
     * @return [type] [description]
     */
    public function jfShop()
    {
        $this->check_capacity("6_6_13");
        $info = $this->input->post();
        $this->load->model('model_shouye_img', 'img');
        if ($info['banner'])
        {
            $this->check_capacity("6_6_14",true);
            foreach ($info['banner'] as $banner)
            {
                if ($banner['title'] && $banner['url'] && $banner['path'])
                {
                    $banner['position'] = 'jfbanner';
                    //$banner['path'] = '//888.166cai.cn/uploads/infobanner/'.$banner['path'];
                    $istData[] = $banner;

                }elseif (!(empty($banner['title']) && empty($banner['url']) && empty($banner['path'])))
                {
                   //$this->redirect('/backend/Appconfig/jfShop?notfull=1');
                }
            }
            $this->img->delByPosition('jfbanner');
            $this->img->insertAllData($istData);
            $this->refreshCache('jfbanner');
            foreach ($istData as $dt)
            {
                $this->syslog(39, "积分商城banner更新操作，".$dt['title']."，".$dt['url']);
            }
            echo json_encode(array('status'=>'SUCCESSS','message'=>'恭喜你，操作成功'));die;
        }
        $data['banner'] = $this->img->getListByPosition('jfbanner');
        $this->load->view('/appconfig/jfShop', $data);
    }
    /**
     * [refreshCache 写入缓存]
     * @author LiKangJian 2018-01-02
     * @param  [type] $type [description]
     * @return [type]       [description]
     */
    private function refreshCache($type)
    {
        $this->load->driver('cache', array('adapter' => 'redis'));
        $REDIS = $this->config->item('REDIS');
        $this->load->model('model_shouye_img', 'model');
        $data = $this->model->getDataByPosition($type);
        $res = array();
        $res['jfbanner'] = $data;
        $this->cache->save($REDIS['JIFEN'], serialize($res), 0);
    }

    // 礼包提醒页配置
    public function gift($platform = 'android')
    {
        $postData = $this->input->post(null, true);

        if($platform == 'android')
        {
            $this->check_capacity('13_1_18');
        }
        elseif ($platform == 'ios')
        {
            $this->check_capacity('13_2_18');
        }
        elseif ($platform == 'm')
        {
            $this->check_capacity('13_3_18');
        }

        // 提交
        if(!empty($postData['banner']))
        {
            $this->checkenv($postData['env']);
            if($platform == 'android')
            {
                $this->check_capacity('13_1_19');
            }
            elseif ($platform == 'ios')
            {
                $this->check_capacity('13_2_19');
            }
            elseif ($platform == 'm')
            {
                $this->check_capacity('13_3_19');
            }

            //入库
            $bannerInfo = array();
            foreach ($postData['banner'] as $key => $items) 
            {
                if(!empty($items['id']) && !empty($items['title']) && !empty($items['imgUrl']) && !empty($items['btnMsg']) && !empty($items['mark']))
                {
                    $extra = array(
                        'btnMsg'    =>  $items['btnMsg'],
                        'mark'      =>  $items['mark'], 
                        'appAction' =>  in_array($items['appAction'], array('bet', 'email', 'redpack')) ? $items['appAction'] : 'webview',
                    );
                    $data = array(
                        'id'            =>  $items['id'],
                        'title'         =>  $items['title'],
                        'imgUrl'        =>  $items['imgUrl'],
                        'url'           =>  $items['url'],
                        'lid'           =>  ($items['appAction'] == 'bet') ? $items['lid'] : '0',
                        'weight'        =>  '0',
                        'extra'         =>  json_encode($extra),
                        'status'        =>  isset($items['status']) ? $items['status'] : '1',
                        'channels'      =>  $items['channels'],
                    );

                    array_push($bannerInfo, $data);

                    //写入日志
                    $this->syslog($this->platform_log[$platform], '礼包提醒内容更新' . '标题：' . $data['title'] . '；图片：' . $data['imgUrl'] . '；链接：' . $data['url'] . '；图片：' . $data['imgUrl'] . '；彩种：' . $data['lid'] . '；渠道：' . $data['channels']);
                }
            }
            $this->recodeAppBanner($bannerInfo, 1, $this->platformType[$postData['platform']]);
            // 刷新缓存
            $this->Appconfig->refreshBannerInfo(1, $this->platformType[$postData['platform']], $postData['platform']);
        }

        $info = $this->Appconfig->getBannerInfo(1, $this->platformType[$platform]);
        // 格式处理
        if(!empty($info))
        {
            foreach ($info as $key => $items) 
            {
                $extra = json_decode($items['extra'], true);
                $info[$key]['mark'] = $extra['mark'];
                $info[$key]['btnMsg'] = $extra['btnMsg'];
                $info[$key]['appAction'] = $extra['appAction'] ? $extra['appAction'] : '';
            }
        }
        else
        {
            $info = array(
                0   =>  array(
                    'title'     =>  '',
                    'mark'      =>  '',
                    'imgUrl'    =>  '',
                    'btnMsg'    =>  '',
                    'url'       =>  '',
                    'appAction' =>  '',
                    'lid'       =>  '',
                    'status'    =>  '',
                )
            );
        }
        $this->load->view('/appconfig/gift', array('info' => $info, 'platform' => $platform, 'channels' => $this->channels[$platform]));
    }

    // cp_app_banner 公共配置
    public function recodeAppBanner($postData, $ctype, $platformId, $checkfields = array())
    {
        if(!empty($postData))
        {
            $fields = array('id', 'cid', 'ctype', 'title', 'imgUrl', 'url', 'lid', 'weight', 'extra', 'channels', 'start_time', 'end_time', 'status', 'platform', 'created');
            $bdata['s_data'] = array();
            $bdata['d_data'] = array();
            foreach ($postData as $items) 
            {
                if ($items['start_time'] !== '0000-00-00 00:00:00' && $items['end_time'] !== '0000-00-00 00:00:00' && $items['cid'] !== '') {
                    // 必要字段检查
                    $checkFlag = TRUE;
                    if(!empty($checkfields))
                    {
                        foreach ($checkfields as $key)
                        {
                            if($items[$key] === '')
                            {
                                $checkFlag = FALSE;
                            }
                        }
                    }
                    
                    array_push($bdata['s_data'], "(?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, now())");
                    array_push($bdata['d_data'], isset($items['id']) ? $items['id'] : null);
                    array_push($bdata['d_data'], isset($items['cid']) ? $items['cid'] : 0);
                    array_push($bdata['d_data'], $ctype);
                    array_push($bdata['d_data'], ($checkFlag && $items['title']) ? $items['title'] : '');
                    array_push($bdata['d_data'], ($checkFlag && $items['imgUrl']) ? $items['imgUrl'] : '');
                    array_push($bdata['d_data'], ($checkFlag && $items['url']) ? $items['url'] : '');
                    array_push($bdata['d_data'], ($checkFlag && $items['lid']) ? $items['lid'] : '0');
                    array_push($bdata['d_data'], ($checkFlag && $items['weight']) ? $items['weight'] : '0');
                    array_push($bdata['d_data'], ($checkFlag && $items['extra']) ? $items['extra'] : '');
                    array_push($bdata['d_data'], ($checkFlag && $items['channels']) ? $items['channels'] : '');
                    array_push($bdata['d_data'], ($checkFlag && $items['start_time']) ? $items['start_time'] : '');
                    array_push($bdata['d_data'], ($checkFlag && $items['end_time']) ? $items['end_time'] : '');
                    array_push($bdata['d_data'], isset($items['status']) ? $items['status'] : 1);
                    array_push($bdata['d_data'], $platformId);
                }
            }
            if(!empty($bdata['s_data']))
            {
                $this->Appconfig->recodeAppBanner($fields, $bdata);
                $bdata['s_data'] = array();
                $bdata['d_data'] = array();
            }
        }
    }

    // M版首页配置
    public function mindex($platform = 'm')
    {
        $this->check_capacity('13_3_3');

        $postData = $this->input->post(null, true);
        if(!empty($postData))
        {
            $this->checkenv($postData['env']);
            switch ($postData['subType']) 
            {
                case '1':
                    // 更新彩种信息
                    $this->check_capacity('13_3_8');
                    $this->updateLotteryInfo($postData);
                    break;

                default:
                    # code...
                    break;
            }
        }
        // 获取彩种配置信息
        $lotteryInfo = $this->Appconfig->getLotteryInfo($platform);
        $lotterys = $this->handleLottery($lotteryInfo);
        $attachFlags = array('1' => '加奖中', '2' => '副标题标红'); //定义成数组为了以后扩展

        $info = array(
            'platform'      => $platform,
            'lotteryInfo'   => $lotterys,
            'attachFlags'   => $attachFlags,
        );
        $this->load->view("appconfig/m_version", $info);
    }

    public function exchangePlayType($extra)
    {
        $extra = json_decode($extra, true);
        if(strpos($extra['lid'], '-') !== FALSE)
        {
            $lidArr = explode('-', $extra['lid']);
            $lid = $lidArr[0];
            if($lid == '42' && !empty($lidArr[1]) && is_numeric($lidArr[1]))
            {
                $extra['playType'] = $lidArr[1];
            }
            $extra['lid'] = $lid;
        }
        return json_encode($extra);
    }

    public function exchangeLid($extra)
    {
        $lid = $extra['lid'];
        if(!empty($extra['lid']) && $extra['lid'] == '42' && !empty($extra['playType']))
        {
            $lid = $extra['lid'] . '-' . $extra['playType'];
        }
        return $lid;
    }

    public function betBanner($platform = 'android')
    {
        $this->load->library('BetCnName');
        if($platform == 'android')
        {
            $this->check_capacity('13_1_20');
        }
        else
        {
            $this->check_capacity('13_2_20');
        }

        $postData = $this->input->post(null, true);
        if(!empty($postData['banner']))
        {
            $this->checkenv($postData['env']);
            if($platform == 'android')
            {
                $this->check_capacity('13_1_21');
            }
            else
            {
                $this->check_capacity('13_2_21');
            }

            //入库
            $bannerInfo = array();
            foreach ($postData['banner'] as $key => $items) 
            {
                if(!empty($items['id']) && !empty($items['lid']))
                {
                    $data = array(
                        'id'            =>  $items['id'],
                        'title'         =>  '投注页素材',
                        'imgUrl'        =>  $items['imgUrl'] ? $items['imgUrl'] : '',
                        'url'           =>  $items['url'] ? $items['url'] : '',
                        'lid'           =>  $items['lid'],
                        'weight'        =>  '0',
                        'extra'         =>  '',
                        'status'        =>  '1',
                        'channels'      =>  '',
                    );

                    array_push($bannerInfo, $data);

                    //写入日志
                    $this->syslog($this->platform_log[$platform], BetCnName::getCnName($items['lid']) . '投注页素材配置操作');
                }
            }
            $this->recodeAppBanner($bannerInfo, 5, $this->platformType[$postData['platform']]);
            // 刷新缓存
            $this->Appconfig->refreshBannerInfo(5, $this->platformType[$postData['platform']], $postData['platform']);
        }

        $info = array();
        $list = $this->Appconfig->getBannerInfo(5, $this->platformType[$platform]);
        if(!empty($list))
        {
            foreach ($list as $items) 
            {
                $data = array(
                    'id'        =>  $items['id'],
                    'imgUrl'    =>  $items['imgUrl'],
                    'url'       =>  $items['url'],
                    'lid'       =>  $items['lid'],
                    'lname'     =>  BetCnName::getCnName($items['lid']),
                );
                array_push($info, $data);
            }
        }
        $this->load->view("appconfig/betBanner", array('info' => $info, 'platform' => $platform));
    }
    
    public function bannerorder($platform = 'android', $cid) {
        if($platform == 'android') $this->check_capacity('13_1_22');
        else $this->check_capacity('13_2_22');
        $mark = array(
            'android' => array(
                '通用尺寸',
                '通用尺寸',
                '通用尺寸'
            ),
            'ios' => array(
                '3.5-640X796',
                '4-640X926',
                '4.7-750X1088',
                '5.5-1242X1800',
                '5.8-1125X2436',
                '3.5-640X796',
                '4-640X926',
                '4.7-750X1088',
                '5.5-1242X1800',
                '5.8-1125X2436',
                '3.5-640X796',
                '4-640X926',
                '4.7-750X1088',
                '5.5-1242X1800',
                '5.8-1125X2436',
            )
        );
        $postData = $this->input->post(null, true);
        if(!empty($postData) && in_array($postData['platform'], array('android', 'ios')))
        {
            if($platform == 'android') $this->check_capacity('13_1_23');
            else $this->check_capacity('13_2_23');
        	$this->checkenv($postData['env']);
            if (!empty($postData['prelaod'])) {
                foreach ($postData['prelaod'] as $key => &$items) {
                    $items['cid'] = $cid;
                    // 启动页竞彩足球区分玩法
                    $extra = '';
                    if(strpos($items['lid'], '-') !== FALSE && strpos($items['lid'], '42') !== FALSE)
                    {
                        $lidArr = explode('-', $items['lid']);
                        $items['lid'] = trim($lidArr[0]);
                        $playtype = trim($lidArr[1]) ? trim($lidArr[1]) : 0;
                        $extra = json_encode(array('playType' => $playtype));
                    }
                    $postData['prelaod'][$key]['lid'] = $items['lid'];
                    $postData['prelaod'][$key]['extra'] = $extra;
                    // IOS 渠道聚合字段处理
                    if($platform == 'ios')
                    {
                        if($items['start_time']) {
                            $channels = $items['channels'];
                            $start = $items['start_time'];
                            $end = $items['end_time'];
                            $url = $items['url'];
                        }else {
                            $postData['prelaod'][$key]['channels'] = $channels;
                            $postData['prelaod'][$key]['start_time'] = $start;
                            $postData['prelaod'][$key]['end_time'] = $end;
                            $postData['prelaod'][$key]['url'] = $url;
                        }
                    }
                    
                    if (empty($items['imgUrl']) || empty($postData['prelaod'][$key]['end_time']) || empty($postData['prelaod'][$key]['start_time'])) {
                        $this->redirect("/backend/Appconfig/bannerorder/".$platform."/".$cid."?notfull=1");
                    }
                    if ($items['start_time'] < date('Y-m-d H:i:s') || $this->Appconfig->checkstart($this->platformType[$postData['platform']], $cid, $items['start_time'])) 
                        $postData['prelaod'][$key]['start_time'] = date('Y-m-d H:i:s');
                }
            }
            
            // 启动页入库
            // $checkfields = array('title', 'imgUrl');
            $this->recodeAppBanner($postData['prelaod'], 2, $this->platformType[$postData['platform']]);
            if ($postData['delid']) $this->Appconfig->delAppBannerById($postData['delid']);
        }
        $result = $this->Appconfig->getAppBannerByCid($this->platformType[$platform], $cid);
        $data = array();
        $onlineArr = array();
        foreach ($result as $val) {
            if ($val['isorder']) array_push($data, $val);
            else {
                if ($platform === 'android') $online = $val;
                else array_push($onlineArr, $val);
            }
        } 
        if ($platform == 'android') {
            $chicun = $mark[$platform][$cid - 1];
            if (empty($online)) $online = array('title' => '开屏广告图 - '.$cid);
        } else {
            $chicun = array();
            $needpush = 0;
            if (empty($onlineArr)) $needpush = 1;
            for ($i = 0; $i < 5; $i++) {
                array_push($chicun, $mark[$platform][($cid - 1) * 5 + $i]);
                if ($needpush) array_push($onlineArr, array('title' => '开屏广告图 - '.$mark['ios'][($cid - 1) * 5 + $i]."-".$cid));
            }
        }
        $notfull = $this->input->get('notfull');
        $channels = $this->channels[$platform];
        $this->load->view("appconfig/bannerorder", compact('online', 'data', 'cid', 'platform', 'chicun', 'channels', 'onlineArr', 'notfull'));
    }
}
