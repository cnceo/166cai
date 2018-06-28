<?php

class Model_Appconfig extends MY_Model
{

    public function __construct()
    {
        parent::__construct();
    }

    /*
     * 广告轮播
     * @date:2015-04-17
     */
    public function getAddInfo($platform = 'android') {
        $sql = "SELECT id, imgTitle, imgUrl, hrefUrl, lid, weight, extra, platform, channels, start_time, end_time, start_time > NOW() isorder 
        FROM cp_add_info WHERE platform = '{$platform}' and end_time > NOW() AND delect_flag = 0 ORDER BY weight DESC, start_time;";
        return $this->master->query($sql)->getALL();
    }

    /*
     * 删除轮播图
     * @date:2015-04-17
     */
    public function delAppBanner($platform) {
        $sql = "delete from cp_add_info where platform = ?";
        return $this->master->query($sql, array($platform));
    }

    /*
     * 更新轮播图
     * @date:2015-04-17
     */
    public function insertAppBanner($datas) {
        if (!empty($datas)) {
            $field = '';
            $fields = array_keys($datas[0]);
            foreach ($fields as $value) {
                $field .= $value.", ";
            }

            $field = substr($field, 0, -2);

            $sql = "insert into cp_add_info ({$field}) values ";
            foreach ($datas as $data) {
                $sql .= "(";
                foreach ($fields as $value) {
                    $sql .= "'{$data[$value]}', ";
                }
                $sql = substr($sql, 0, -2)."), ";
            }
            $sql = substr($sql, 0, -2);

            return $this->master->query($sql);
        }
    }

    /*
     * 彩种信息
     * @date:2015-04-17
     */
    public function getLotteryInfo($platform = 'android')
    {
        $sql = "SELECT plid, lid, ltype, lname, logUrl, weight, memo, isHot, ctype, delect_flag, attachFlag, channels FROM cp_lottery_info WHERE platform = '{$platform}' ORDER BY weight DESC;";
        $info = $this->BcdDb->query($sql)->getALL();
        return $info;
    }

    /*
     * 刷新彩种信息 -- 兼容v4.0首页改版前版本
     * @date:2015-04-17
     */
    public function freshLotteryInfo($platform = 'android', $ctype = 'lottery_info')
    {
        $REDIS = $this->config->item('REDIS');
        $ukey = "{$REDIS['APP_CONFIG']}{$ctype}_{$platform}";
        $this->load->driver('cache', array('adapter' => 'redis'));
        $sql = "SELECT lid, ltype, lname, logUrl, memo, isHot, ctype, attachFlag, channels FROM cp_lottery_info WHERE platform = '{$platform}' AND lid <> 2 ORDER BY weight DESC;";
        $info = $this->master->query($sql)->getALL();
        if(!empty($info))
        {
            $this->cache->redis->save($ukey, serialize($info), 0);
        }
        return $info;
    }

    /*
     * 更新彩种信息
     * @date:2015-04-17
     */
    public function updateLotteryInfo($datas)
    {
        if (!empty($datas))
        {
            $upd = array('weight', 'memo', 'delect_flag', 'attachFlag', 'channels');
            $field = '';
            $fields = array_keys($datas[0]);
            foreach ($fields as $value)
            {
                $field .= $value.", ";
            }

            $field = substr($field, 0, -2);

            $sql = "insert cp_lottery_info ({$field}) values ";
            foreach ($datas as $data)
            {
                $sql .= "(";
                foreach ($fields as $value)
                {
                    $sql .= "'{$data[$value]}', ";
                }
                $sql = substr($sql, 0, -2)."), ";
            }
            $sql = substr($sql, 0, -2);
            $sql .=  $this->onduplicate($fields, $upd);
            return $this->master->query($sql);
        }
    }

    /*
     * 获取版本信息
     * @date:2015-04-17
     */
    public function getVersionInfo($platform = 'android')
    {
        $REDIS = $this->config->item('REDIS');
        $ukey = "{$REDIS['APP_CONFIG']}$platform";
        $this->load->driver('cache', array('adapter' => 'redis'));
        $info = unserialize($this->cache->redis->get($ukey));
        if(empty($info))
        {
            $info = $this->freshVersionInfo($platform, $appVersionCode);
        }
        return $info;
    }

    /*
     * 刷新版本信息
     * @date:2015-04-17
     */
    public function freshVersionInfo($platform)
    {
        $REDIS = $this->config->item('REDIS');
        $ukey = "{$REDIS['APP_CONFIG']}$platform";
        $this->load->driver('cache', array('adapter' => 'redis'));
        $sql = "SELECT id, versionName, versionCode, showAlert, showRedpack, isCheck, upgradeVersion, mark, lotteryConfig, platform FROM cp_app_version_config WHERE platform = '{$platform}' ORDER BY versionCode DESC;";
        $info = $this->master->query($sql)->getALL();
        $versionData = array();
        if(!empty($info))
        {
            foreach ($info as $key => $items) 
            {
                $versionData[$items['versionCode']] = $items;
            }
            $this->cache->redis->save($ukey, serialize($versionData), 0);

        }
        return $versionData;
    }

    public function getVersionDetail($platform, $appVersionCode)
    {
        $sql = "SELECT id, versionName, versionCode, showAlert, showRedpack, isCheck, upgradeVersion, mark, lotteryConfig, platform FROM cp_app_version_config WHERE platform = '{$platform}' AND versionCode = '{$appVersionCode}';";
        $info = $this->BcdDb->query($sql)->getRow();
        return $info;
    }

    public function updateVersionInfo($versionData)
    {
        $upd = array('showAlert', 'showRedpack', 'isCheck', 'upgradeVersion', 'lotteryConfig', 'mark');
        $fields = array_keys($versionData);
        $sql = "insert cp_app_version_config(" . implode(',', $fields) . ", created)values(" . 
        implode(',', array_map(array($this, 'maps'), $fields)) .  ", now())" . $this->onduplicate($fields, $upd);
        return $this->master->query($sql, $versionData);
    }

    public function getPreloadInfo($platform, $ctype = 'preload')
    {
        $REDIS = $this->config->item('REDIS');
        $ukey = "{$REDIS['APP_CONFIG']}{$ctype}_{$platform}";
        $this->load->driver('cache', array('adapter' => 'redis'));
        $info = unserialize($this->cache->redis->get($ukey));
        return $info;
    }

    public function freshPreloadInfo($info, $platform, $ctype = 'preload')
    {
        $REDIS = $this->config->item('REDIS');
        $ukey = "{$REDIS['APP_CONFIG']}{$ctype}_{$platform}";
        $this->load->driver('cache', array('adapter' => 'redis'));
        if(!empty($info))
        {
            $this->cache->redis->save($ukey, serialize($info), 0);
        }     
    }

    public function delPreloadInfo($platform, $ctype = 'preload')
    {
        $REDIS = $this->config->item('REDIS');
        $ukey = "{$REDIS['APP_CONFIG']}{$ctype}_{$platform}";
        $this->load->driver('cache', array('adapter' => 'redis'));
        $this->cache->redis->delete($ukey);
    }

    public function getActivityInfo($platform)
    {
        $sql = "SELECT id, type, title, content, cstatus, weight, extra, platform, channels FROM cp_app_activity_config WHERE platform = '{$platform}' ORDER BY id ASC;";
        return $this->BcdDb->query($sql)->getALL();
    }

    public function updateActivity($bdata)
    {
        $fields = array('type', 'content', 'cstatus', 'platform', 'weight', 'extra', 'channels', 'created');
        $upd = array('content', 'cstatus', 'weight', 'extra', 'channels');

        $sql = "insert cp_app_activity_config(" . implode(', ', $fields) . ") values" . 
            implode(', ', $bdata['s_data']) . $this->onduplicate($fields, $upd);
        return $this->master->query($sql, $bdata['d_data']);
    }

    public function getJjcInfo($lotteryId)
    {
        $info = array();

        // 彩票数据中心
        switch ($lotteryId) 
        {
            // 竞彩足球
            case '42':
                $info = $this->getJczqMatch();
                break;
            // 竞彩篮球
            case '43':
                $info = $this->getJclqMatch();
                break;
            default:
                # code...
                break;
        }
        return $info;
    }

    public function getJczqMatch()
    {
        $this->load->driver('cache', array('adapter' => 'redis'));
        $REDIS = $this->config->item('REDIS');
        $ukey = "{$REDIS['JCZQ_MATCH']}";
        $info = $this->cache->redis->get($ukey);
        $info = json_decode($info, true);
        return $info;
    }

    public function getJclqMatch()
    {
        $this->load->driver('cache', array('adapter' => 'redis'));
        $REDIS = $this->config->item('REDIS');
        $ukey = "{$REDIS['JCLQ_MATCH']}";
        $info = $this->cache->redis->get($ukey);
        $info = json_decode($info, true);
        return $info;
    }

    // 活动信息列表
    public function list_event($searchData, $page, $pageCount)
    {
        $select = "SELECT id, title, path, url, lid, weight, platform, start_time, end_time FROM cp_app_event WHERE platform = ? AND delete_flag = 0 ORDER BY weight DESC LIMIT " . ($page - 1) * $pageCount . "," . $pageCount;
        $result = $this->master->query($select, array($searchData['platform']))->getALL();

        $count = "SELECT count(*) FROM cp_app_event WHERE platform = ? AND delete_flag = 0";
        $count = $this->master->query($count, array($searchData['platform']))->getOne();

        return array($result, $count);
    }


    public function recodeEventInfo($info)
    {
        $fields = array_keys($info);
        $sql = "insert cp_app_event(" . implode(',', $fields) . ", created)values(" . 
        implode(',', array_map(array($this, 'maps'), $fields)) .  ", now())";
        return $this->master->query($sql, $info);
    }

    public function updateEventInfo($info)
    {
        $sql = "UPDATE cp_app_event SET title = ?, path = ?, url = ?, lid = ?, weight= ?, start_time = ?, end_time = ? WHERE id = ?";
        return $this->master->query($sql, array($info['title'], $info['path'], $info['url'], $info['lid'], $info['weight'], $info['start_time'], $info['end_time'], $info['id']));
    }

    public function delEventInfo($id)
    {
        $sql = "UPDATE cp_app_event SET delete_flag = 1 WHERE id = ?";
        return $this->master->query($sql, array($id));
    }

    public function getEventStatus($platform, $ctype = 'eventStatus')
    {
        $REDIS = $this->config->item('REDIS');
        $ukey = "{$REDIS['APP_CONFIG']}{$ctype}_{$platform}";
        $this->load->driver('cache', array('adapter' => 'redis'));
        $info = unserialize($this->cache->redis->get($ukey));
        return $info;
    }

    public function refreshEventStatus($info, $platform, $ctype = 'eventStatus')
    {
        $REDIS = $this->config->item('REDIS');
        $ukey = "{$REDIS['APP_CONFIG']}{$ctype}_{$platform}";
        $this->load->driver('cache', array('adapter' => 'redis'));
        $this->cache->redis->save($ukey, serialize($info), 0);     
    }
    
    public function getBannerInfo($ctype, $platform)
    {
        $sql = "SELECT id, ctype, cid, title, imgUrl, url, lid, weight, extra, status, platform, channels FROM cp_app_banner WHERE ctype = ? AND platform = ? AND delete_flag = 0 ORDER BY id ASC;";
        return $this->master->query($sql, array($ctype, $platform))->getALL();
    }

    public function getQdyList($platform)
    {
        $result = $this->master->query("SELECT id, cid, ctype, title, imgUrl, url, lid, weight, extra, `status`, platform, channels, start_time, end_time 
            FROM cp_app_banner  
            WHERE ctype = 2 AND start_time <= NOW() AND end_time > NOW() AND platform = ? AND delete_flag = 0 
            ORDER BY cid ASC", array($platform))->getALL();
        foreach ($result as $val) {
            $data[$val['cid']][] = $val;
        }
        $ordercount = $this->master->query("select count(*) as num, cid from cp_app_banner 
            where delete_flag = 0 and platform = ? and start_time > NOW() and ctype = 2 group by cid", array($platform))->getALL();
        foreach ($ordercount as $val) {
            $data[$val['cid']]['ordercount'] = $val['num'];
        }
        return $data;
    }
    
    public function checkHasQdyByTime($platform, $cid, $end, $ids = array()) {
        $sql = 'select 1 from cp_app_banner 
            where cid = ? AND ctype= 2 AND platform = ? and ((start_time < ? and end_time >= ?) or (start_time < ? and end_time <= ? and start_time > NOW()))';
        $data = array($cid, $platform, $end, $end, $end, $end);
        if (!empty($ids)) {
            $sql .= ' and id not in ?';
            array_push($data, $ids);
        }
        return $this->BcdDb->query($sql, $data)->getOne();
    }
    
    public function updateChanners($platform){
        $this->master->query('UPDATE
        cp_app_banner as ab0
        LEFT JOIN cp_app_banner as ab1 ON ab0.platform = ab1.platform AND ab0.ctype=ab1.ctype AND ab0.cid=ab1.cid AND ab1.start_time > NOW()
        SET ab1.channels=ab0.channels
        WHERE ab0.platform = ? AND ab0.ctype = 2 AND ab0.start_time <= NOW() AND ab0.end_time > NOW()', array($platform));
    }

    public function refreshBannerInfo($ctype, $platformId, $platformName)
    {
        $ctypeArr = array(
            '1' =>  'appGiftRemind',    // 实名礼包提醒页配置
            '2' =>  'appPreload',       // 启动页
            '3' =>  'appIndexPop',      // 首页弹层
            '4' =>  'appWechatLogin',   // 微信登录
            '5' =>  'appBetBanner',     // 投注页加奖素材
        );
        $info = $this->getBannerInfo($ctype, $platformId);
        $Redistype = $ctypeArr[$ctype];
        if(!empty($info) && !empty($Redistype))
        {
            $REDIS = $this->config->item('REDIS');
            $ukey = "{$REDIS['APP_CONFIG']}{$Redistype}_{$platformName}";
            $this->load->driver('cache', array('adapter' => 'redis'));
            $this->cache->redis->save($ukey, serialize($info), 0); 
        }
    }

    public function recodeBannerInfo($data)
    {
        $fields = array_keys($data);
        $sql = "insert cp_app_banner(" . implode(',', $fields) . ", created)values(" . 
        implode(',', array_map(array($this, 'maps'), $fields)) .  ", now())" . " on duplicate key update title = values(title), imgUrl = values(imgUrl), url = values(url), lid = values(lid), weight = values(weight), extra = values(extra), status = values(status), delete_flag = values(delete_flag);"; 
        return $this->master->query($sql, $data);
    }

    // 获取渠道分组
    public function getChannels()
    {
        $sql = "SELECT c.id, c.name, c.platform, c.package, p.name as pname FROM cp_channel AS c LEFT JOIN cp_channel_package AS p ON c.package = p.id WHERE c.platform in (2, 3)";
        $data = $this->master->query($sql)->getALL();

        // 安卓 IOS 映射
        $platformArr = array(
            2   =>  'android',
            3   =>  'ios',
        );

        $channels = array();
        $packages = array();
        if(!empty($data))
        {
            foreach ($data as $items) 
            {
                $platform = $platformArr[$items['platform']];
                $channels[$platform]['detail'][] = $items;
                if(!in_array($items['package'], $packages))
                {
                    $channels[$platform]['package'][] = array(
                        'package'   =>  $items['package'],
                        'pname'     =>  $items['pname'],
                    );
                    array_push($packages, $items['package']);
                } 
            }
        }
        return $channels;
    }

    // 客户端Banner公共配置
    public function recodeAppBanner($fields, $bdata)
    {
        $sql = "INSERT cp_app_banner(" . implode(', ', $fields) . ") values" . 
            implode(', ', $bdata['s_data']) . " 
                on duplicate key update title = values(title), imgUrl = values(imgUrl), url = values(url), lid = values(lid), weight = values(weight), extra = values(extra), channels = values(channels), status = values(status), start_time = values(start_time), end_time = values(end_time)";
        $this->master->query($sql, $bdata['d_data']);
    }
    
    public function getAppBannerByCid($platform, $cid) {
        return $this->BcdDb->query("select id, ctype, cid, title, imgUrl, url, lid, weight, extra, status, platform, channels, start_time, end_time, if (start_time < now(), 0, 1) isorder
            from cp_app_banner where platform = ? and ctype = 2 and cid = ? and end_time > now() order by start_time", array($platform, $cid))->getAll();
    }
    
    public function delAppBannerById($delId) {
        $delArr = explode(',', $delId);
        if (!is_array($delArr)) $delArr = array($delArr);
        return $this->master->query('delete from cp_app_banner where id in ? and ctype = 2', array($delArr));
    }
    
    public function delAppBannerByCId($delId, $platform) {
        $delArr = explode(',', $delId);
        if (!is_array($delArr)) $delArr = array($delArr);
        return $this->master->query('delete from cp_app_banner where cid in ? and platform = ? and ctype = 2', array($delArr, $platform));
    }
    
    public function checkstart($platform, $cid, $start) {
        return $this->BcdDb->query("select 1 from
            cp_app_banner where platform = ? and ctype = '2' and cid = ? and end_time < NOW() and end_time > ?", array($platform, $cid, $start))->getOne();
    }
}
