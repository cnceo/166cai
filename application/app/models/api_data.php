<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/*
 * APP 数据接口 模型层
 * @date:2015-04-17
 */

class Api_Data extends MY_Model 
{
	public function __construct() 
	{
		parent::__construct();
		$this->load->library('tools');
		$this->busiApi = $this->config->item('busi_api');
	}

	/*
 	 * 彩种信息
 	 * @date:2015-04-17
 	 */
	public function getLotteryInfo($platform = 'android', $ctype = 'lottery_info')
    {
        $REDIS = $this->config->item('REDIS');
        $ukey = "{$REDIS['APP_CONFIG']}{$ctype}_{$platform}";
        // $ukey = "{$REDIS['LOTTERY_INFO']}$platform";
        $this->load->driver('cache', array('adapter' => 'redis'));
        $info = unserialize($this->cache->redis->get($ukey));
		if(empty($info))
		{
			$info = $this->freshLotteryInfo($platform);
		}
		return $info;
	}

	/*
 	 * 刷新彩种信息
 	 * @date:2015-04-17
 	 */
	public function freshLotteryInfo($platform = 'android', $ctype = 'lottery_info')
	{
		$REDIS = $this->config->item('REDIS');
        $ukey = "{$REDIS['APP_CONFIG']}{$ctype}_{$platform}";
		// $ukey = "{$REDIS['LOTTERY_INFO']}$platform";
		$this->load->driver('cache', array('adapter' => 'redis'));
		$sql = "SELECT lid, ltype, lname, logUrl, memo, isHot, ctype, attachFlag FROM cp_lottery_info WHERE platform = '{$platform}' AND delect_flag = 0 ORDER BY weight DESC;";
		$info = $this->slave->query($sql)->getALL();
		if(!empty($info))
		{
			$this->cache->redis->save($ukey, serialize($info), 0);
		}
		return $info;
	}

	/*
 	 * 广告轮播
 	 * @date:2015-04-17
 	 */
	public function getAddInfo($platform = 'android', $ctype = 'add_info')
	{
		$REDIS = $this->config->item('REDIS');
        $ukey = "{$REDIS['APP_CONFIG']}{$ctype}_{$platform}";
		// $ukey = "{$REDIS['ADD_INFO']}$platform";
		$this->load->driver('cache', array('adapter' => 'redis'));
		$info = unserialize($this->cache->redis->get($ukey));
		if(empty($info)) $info = $this->freshAddInfo($platform);
		return $info;
	}
	
	public function freshAddInfo($platform = 'android') {
	    $REDIS = $this->config->item('REDIS');
	    $ukey = "{$REDIS['APP_CONFIG']}add_info_{$platform}";
	    $this->load->driver('cache', array('adapter' => 'redis'));
	    $info = $this->slave->query("SELECT id, imgTitle, imgUrl, hrefUrl, lid, weight, extra, platform, channels
            FROM cp_add_info
            WHERE platform = ? AND delect_flag = 0 and start_time <= NOW() and end_time > NOW()
            ORDER BY weight DESC;", array($platform))->getALL();
	    if (empty($info)) {
	        $info = $this->slave->query("SELECT id, imgTitle, imgUrl, hrefUrl, lid, weight, extra, platform, channels
                FROM cp_add_info
                WHERE platform = ? AND delect_flag = 0 AND end_time < NOW()
                ORDER BY end_time DESC, start_time DESC, weight DESC
                limit 1", array($platform))->getALL();
	    }
	    $this->cache->redis->save($ukey, serialize($info), 0);
	    return $info;
	}

	/*
 	 * 分页查询指定彩种的开奖列表（已废弃）
 	 * @date:2015-04-20
 	 */
	public function getAwardInfo($lotteryId, $state, $pn, $ps = 10) 
	{
        $awards = array();
        $awardResponse = $this->tools->get($this->busiApi . 'ticket/data/il', array(
            'lid' => $lotteryId,
            'state' => $state,
            'pn' => $pn,
            'ps' => $ps,
        ));
        if ($awardResponse['code'] == 0) 
        {
            $awards = $awardResponse['data'];
        }

        return $awards;
    }

    /*
 	 * 查询普通彩种当前期信息（已废弃）
 	 * @date:2015-04-20
 	 */
    public function getCurrentLottery()
    {
    	$info = array();

    	$lottery = array(
            'DLT' => 23529,
            'RJ' => 19,
            'SFC' => 11,
            'QLC' => 23528,
            'QXC' =>  10022,
            'SYXW' =>  21406,
            'SSQ' =>  51,
            'FCSD' => 52,
            'PLS' => 33,
            'PLW' => 35
        );

        $response = $this->tools->get($this->busiApi . 'ticket/data/v1/il', array(
                'lid' => implode( ',', array_values( $lottery ) ),
                'ci' => '1'
        ));

        if ($response['code'] == 0) 
        {
            $info = $response['data'];
        }

        return $info;
    }

    /*
 	 * 查询指定彩种当前期信息（已废弃）
 	 * @date:2015-04-20
 	 */
    public function getCurrentByLid($lotteryId) 
    {
        $info = array();

        $response = $this->tools->get($this->busiApi . 'ticket/data/v1/il', array(
                'lid' => $lotteryId,
                'ci' => '1'
        ));

        if ($response['code'] == 0) 
        {
            $info = $response['data'];
        }

        return $info;
    }

    /*
 	 * 查询老足彩指定期号的信息（已废弃）
 	 * @date:2015-04-20
 	 */
    public function getTczqInfo($lotteryId, $issue) 
    {
        // 获取当前期场次
        $matches = array();

        $response = $this->tools->get($this->busiApi . 'ticket/data/ozc_games', array(
            'lid' => $lotteryId,
            'issue' => $issue,
        ));

        if($response['code'] == 0)
        {
            $matches = $response['data'];
        }

        return $matches;
    }

    /*
     * 查询指定彩种最新期次的信息（已废弃）
     * @date:2015-07-9
     */
    public function getLastIssue($lotteryId)
    {
        // 获取最新期次
        $info = array();

        $issueResponse = $this->tools->get($this->busiApi . 'ticket/data/il', array(
            'lid' => $lotteryId,
            'ci' => 1,
        ));

        if ($issueResponse['code'] == 0) 
        {
            $info = $issueResponse['data'];
        }
        return $info;
    }

    /*
 	 * 查询竞彩彩种在售场次内容（已废弃）
 	 * @date:2015-04-20
 	 */
    public function getJjcInfo($lotteryId, $state)
    {
        $info = array();

        $response = $this->tools->get($this->busiApi . 'ticket/data/jil', array(
            'lid' => $lotteryId,
            'state' => $state
        ));

        if ($response['code'] == 0) 
        {
            $info = $response['data'];
        }
        
        return $info;
    }

    /*
     * 获取省市信息
     * @date:2015-04-30
     */
    public function getProvince()
    {
        $sql1 = "select province, city from cp_city";      
        $details = $this->slave->query( $sql1 )->row_array();

        $sql2 = "select DISTINCT province from cp_city";
        $province = $this->slave->query( $sql2 )->row_array();
        return array('details' => $details, 'province' => $province);
    }

    /*
     * 获取版本信息
     * @date:2015-05-13
     */
    public function getAppVersion($platform)
    {
        $REDIS = $this->config->item('REDIS');
        $ukey = "{$REDIS['APP_VERSION']}$platform";
        $this->load->driver('cache', array('adapter' => 'redis'));
        $info = $this->cache->redis->hGetAll($ukey);
        if(empty($info))
        {
            $info = $this->freshAppVersion($platform);
        }
        return $info;
    }

    /*
     * 刷新版本信息
     * @date:2015-05-13
     */
    public function freshAppVersion($platform)
    {
        $REDIS = $this->config->item('REDIS');
        $ukey = "{$REDIS['APP_VERSION']}$platform";
        $this->load->driver('cache', array('adapter' => 'redis'));
        $sql = "SELECT id, version, content, platform FROM cp_app_version WHERE platform = '{$platform}' AND delect_flag = 0;";
        $info = $this->slave->query($sql)->getRow();
        if(!empty($info))
        {
            $this->cache->redis->hMSet($ukey, $info);
        }
        return $info;
    }
    
}