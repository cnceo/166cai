<?php

/*
 * @date:2015-11-02
 * @liuz
 */
require_once APPPATH . '/core/CommonController.php';
class LotteryData extends CommonController
{
    private $lidMap = array(
        '51' => array('cache' => 'SSQ_ISSUE'),
        '52' => array('cache' => 'FC3D_ISSUE'),
        '33' => array('cache' => 'PLS_ISSUE'),
        '35' => array('cache' => 'PLW_ISSUE'),
        '10022' => array('cache' => 'QXC_ISSUE'),
        '23528' => array('cache' => 'QLC_ISSUE'),
        '23529' => array('cache' => 'DLT_ISSUE'),
        '11' => array('cache' => 'SFC_ISSUE'),
        '19' => array('cache' => 'RJ_ISSUE'),
        '21406' => array('cache' => 'SYXW_ISSUE_TZ'),
    );
    public function __construct()
    {
        parent::__construct();
        $this->load->driver('cache', array('adapter' => 'redis'));
        if(!in_array($this->get_client_ip(), $this->config->item('own_ip')))
        {
            $response = array(
                'code' => 1,
                'msg'  => '查询失败',
                'data' => array(),
            );
            echo json_encode($response);
            die();
        }
    }

    //彩票内页接口
    public function getAward()
    {
            $response = array(
                'code' => 0,
                'msg'  => '查询成功',
                'data' => array(),
            );
            $award = array();
            $awards = array();
            $responses = array();
            $lotteryInfo = $this->lidMap;
            $this->load->driver('cache', array('adapter' => 'redis'));
            $REDIS = $this->config->item('REDIS');
            foreach ($lotteryInfo as $lid => $items)
            {
                $caches = $this->cache->get($REDIS[$items['cache']]);
                $caches = json_decode($caches, true);
                $data = array(
                    '51' => array(
                        'lottery_name' => '双色球',
                        'open_day' => '二四日',
                        'detail' => '//'.DOMAIN.'/kaijiang/ssq',
                        'zst' => 'https://zoushi.166cai.cn/cjwssq/view/ssqzonghe.html',
                        'purchase' => '//'.DOMAIN.'/ssq',
                        'buy_together' => 'http://ssq.cpdyj.com/list_ds.html?regfrom=2345tongji&kjtj',
                        'type' => '福彩'
                    ),
                    '52' => array(
                        'lottery_name' => '福彩3D',
                        'open_day' => '每日',
                        'detail' => '//'.DOMAIN.'/kaijiang/fc3d',
                        'zst' => 'https://zoushi.166cai.cn/cjw3d/view/3d_danxuan.html',
                        'purchase' => '//'.DOMAIN.'/fcsd',
                        'buy_together' => 'http://3d.cpdyj.com/hmlist.html?regfrom=2345tongji&kjtj',
                        'type' => '福彩'
                    ),
                    '33' => array(
                        'lottery_name' => '排列3',
                        'open_day' => '每日',
                        'detail' => '//'.DOMAIN.'/kaijiang/pl3',
                        'zst' => 'https://zoushi.166cai.cn/cjwpl3/view/pl3_danxuan.html',
                        'purchase' => '//'.DOMAIN.'/pls',
                        'buy_together' => 'http://p3.cpdyj.com/hmlist.html?regfrom=2345tongji&kjtj',
                        'type' => '体彩'
                    ),
                    '35' => array(
                        'lottery_name' => '排列5',
                        'open_day' => '每日',
                        'detail' => '//'.DOMAIN.'/kaijiang/pl5',
                        'zst' => 'https://zoushi.166cai.cn/cjwpl5/view/pl5_zst.html',
                        'purchase' => '//'.DOMAIN.'/plw',
                        'buy_together' => 'http://p5.cpdyj.com/hmlist.html?regfrom=2345tongji&kjtj',
                        'type'=>'体彩'
                    ),
                    '10022' => array(
                        'lottery_name' => '七星彩',
                        'open_day' => '二五日',
                        'detail' => '//'.DOMAIN.'/kaijiang/qxc',
                        'zst' => 'https://zoushi.166cai.cn/cjw7xc/view/7xc_haoma.html',
                        'purchase' => '//'.DOMAIN.'/qxc',
                        'buy_together' => 'http://qxc.cpdyj.com/hmlist.html?regfrom=2345tongji&kjtj',
                        'type'=>'体彩'
                    ),
                    '23528' => array(
                        'lottery_name' => '七乐彩',
                        'open_day' => '一三五',
                        'detail' => '//'.DOMAIN.'/kaijiang/qlc',
                        'zst' => 'https://zoushi.166cai.cn/cjwqlc/view/qlcjiben.html',
                        'purchase' => '//'.DOMAIN.'/qlc',
                        'buy_together' => 'http://qlc.cpdyj.com/hmlist.html?regfrom=2345tongji&kjtj',
                        'type' => '福彩'
                    ),
                    '23529' => array(
                        'lottery_name' => '大乐透',
                        'open_day' => '一三六',
                        'detail' => '//'.DOMAIN.'/kaijiang/dlt',
                        'zst' => 'https://zoushi.166cai.cn/cjwdlt/view/dltjiben.html',
                        'purchase' => '//'.DOMAIN.'/dlt',
                        'buy_together' => 'http://dlt.cpdyj.com/hmlist.html?regfrom=2345tongji&kjtj',
                        'type' => '体彩'
                    )
                );
                foreach($data as $key => $value)
                {
                    if($caches['lIssue']['seLotid'] == $key)
                    {
                        foreach($value as $keys => $values)
                        {
                            $caches['lIssue']['lottery_type'] = $caches['lIssue']["seLotid"];
                            $caches['lIssue']['phase'] = $caches['lIssue']["seExpect"];
                            $caches['lIssue']['result'] = $caches['lIssue']["awardNumber"];
                            $caches['lIssue']['pool_amount'] = $caches['lIssue']["awardPool"];
                            $caches['lIssue']['time_draw'] = date("Y-m-d",$caches['lIssue']["awardTime"]/1000);
                            $caches['lIssue'][$keys] = $values;
                        }
                    }

                }
                $arr = array('lottery_type','phase','result','pool_amount','time_draw','open_day','detail','zst','purchase','buy_together','type','lottery_name');
                $lotteryId = array('51','52','33','35','10022','23528','23529');
                if(!empty($caches['lIssue']) )
                {
                    foreach($arr as $key => $value)
                    {
                        $award[$value] = $caches['lIssue'][$value];
                    }
                    array_push($awards,$award);
                }
                $responses = $awards;
            };
            if(!empty($responses) )
            {
                foreach($responses as $key => $value)
                {
                    if(in_array($responses[$key]['lottery_type'],$lotteryId ))
                    {
                        $awardHot[$key] = $responses[$key];
                    }
                }
            }

        $response['data'] = $awardHot;
        echo json_encode($response);
    }

    //体育内页接口
    public function getAllAward()
    {
            $response = array(
                'code' => 0,
                'msg'  => '查询成功',
                'data' => array(),
            );
            $award = array();
            $awards = array();
            $responses = array();
            $jclqresponse = array();
            $jczqresponse = array();
            $lotteryInfo = $this->lidMap;
            $this->load->driver('cache', array('adapter' => 'redis'));
            $REDIS = $this->config->item('REDIS');
            $jclq = json_decode($this->cache->redis->get($REDIS['JCLQ_MATCH']), TRUE);
            $arrjclq = array("mid", "weekId", "nameSname", "homeSname", "awarySname", "hot", "hotid", "sfHs", "sfHf", "sfGd", "sfFu", "cl", "sfGd", "sfFu", "let", "rfsfHs", "rfsfHf", "rfsfGd", "rfsfFu");
            $jczq = json_decode($this->cache->redis->get($REDIS['JCZQ_MATCH']), TRUE);
            $arrjczq = array("mid", "weekId", "nameSname", "homeSname", "awarySname", "hot", "hotid",  "cl", "spfSp3", "spfSp1", "spfSp0", "spfGd", "spfFu", "let", "rqspfSp3", "rqspfSp1", "rqspfSp0", "rqspfGd", "rqspfFu");
            foreach($jclq as $key => $value)
            {
                foreach($arrjclq as $keys => $values)
                {
                    $jclqaward[$values] = $jclq[$key][$values];
                    $jclqaward['mathtime'] = date("Y-m-d H:i", $jclq[$key]['dt']/1000);
                    $jclqaward['stoptime'] = date("Y-m-d H:i", $jclq[$key]['jzdt']/1000);
                }
                if($jclqaward["rfsfGd"] == 1) {
                    array_push($jclqresponse, $jclqaward);
                }
            }
            foreach($jczq as $key => $value)
            {
                foreach($arrjczq as $keys => $values)
                {
                        $jczqaward[$values] = $jczq[$key][$values];
                        $jczqaward['mathtime'] = date("Y-m-d H:i", $jczq[$key]['dt']/1000);
                        $jczqaward['stoptime'] = date("Y-m-d H:i", $jczq[$key]['jzdt']/1000);
                }
                if($jczqaward["spfGd"] == 1)
                {
                    array_push($jczqresponse, $jczqaward);
                }

            }

            foreach ($lotteryInfo as $lid => $items)
            {
                $caches = $this->cache->get($REDIS[$items['cache']]);
                $caches = json_decode($caches, true);
                $data = array(
                    '51' => array(
                        'lottery_name' => '双色球',
                        'open_day' => '二四日',
                        'open_time' => '21:15',
                        'detail' => '//'.DOMAIN.'/kaijiang/ssq',
                        'zst' => 'https://zoushi.166cai.cn/cjwssq/view/ssqzonghe.html',
                        'purchase' => '//'.DOMAIN.'/ssq',
                        'buy_together' => 'http://ssq.cpdyj.com/list_ds.html?regfrom=2345tongji&kjtj',
                        'type' => '福彩'
                    ),
                    '52' => array(
                        'lottery_name' => '福彩3D',
                        'open_day' => '每日',
                        'open_time' => '20:30',
                        'detail' => '//'.DOMAIN.'/kaijiang/fc3d',
                        'zst' => 'https://zoushi.166cai.cn/cjw3d/view/3d_danxuan.html',
                        'purchase' => '//'.DOMAIN.'/fcsd',
                        'buy_together' => 'http://3d.cpdyj.com/hmlist.html?regfrom=2345tongji&kjtj',
                        'type' => '福彩'
                    ),
                    '33' => array(
                        'lottery_name' => '排列3',
                        'open_day' => '每日',
                        'open_time' => '20:30',
                        'detail' => '//'.DOMAIN.'/kaijiang/pl3',
                        'zst' => 'https://zoushi.166cai.cn/cjwpl3/view/pl3_danxuan.html',
                        'purchase' => '//'.DOMAIN.'/pls',
                        'buy_together' => 'http://p3.cpdyj.com/hmlist.html?regfrom=2345tongji&kjtj',
                        'type' => '体彩'
                    ),
                    '35' => array(
                        'lottery_name' => '排列5',
                        'open_day' => '每日',
                        'open_time' => '20:30',
                        'detail' => '//'.DOMAIN.'/kaijiang/pl5',
                        'zst' => 'https://zoushi.166cai.cn/cjwpl5/view/pl5_zst.html',
                        'purchase' => '//'.DOMAIN.'/plw',
                        'buy_together' => 'http://p5.cpdyj.com/hmlist.html?regfrom=2345tongji&kjtj',
                        'type'=>'体彩'
                    ),
                    '10022' => array(
                        'lottery_name' => '七星彩',
                        'open_day' => '二五日',
                        'open_time' => '20:30',
                        'detail' => '//'.DOMAIN.'/kaijiang/qxc',
                        'zst' => 'https://zoushi.166cai.cn/cjw7xc/view/7xc_haoma.html',
                        'purchase' => '//'.DOMAIN.'/qxc',
                        'buy_together' => 'http://qxc.cpdyj.com/hmlist.html?regfrom=2345tongji&kjtj',
                        'type'=>'体彩'
                    ),
                    '23528' => array(
                        'lottery_name' => '七乐彩',
                        'open_day' => '一三五',
                        'open_time' => '21:15',
                        'detail' => '//'.DOMAIN.'/kaijiang/qlc',
                        'zst' => 'https://zoushi.166cai.cn/cjwqlc/view/qlcjiben.html',
                        'purchase' => '//'.DOMAIN.'/qlc',
                        'buy_together' => 'http://qlc.cpdyj.com/hmlist.html?regfrom=2345tongji&kjtj',
                        'type' => '福彩'
                    ),
                    '23529' => array(
                        'lottery_name' => '大乐透',
                        'open_day' => '一三六',
                        'open_time' => '20:30',
                        'detail' => '//'.DOMAIN.'/kaijiang/dlt',
                        'zst' => 'https://zoushi.166cai.cn/cjwdlt/view/dltjiben.html',
                        'purchase' => '//'.DOMAIN.'/dlt',
                        'buy_together' => 'http://dlt.cpdyj.com/hmlist.html?regfrom=2345tongji&kjtj',
                        'type' => '体彩'
                    )
                );
                foreach($data as $key => $value)
                {
                    if($caches['lIssue']['seLotid'] == $key)
                    {
                        $billion = floor($caches['lIssue']["awardPool"] / 100000000);
                        $million = floor(($caches['lIssue']["awardPool"] - $billion * 100000000) / 10000);
                        $yuan = $caches['lIssue']["awardPool"] % 10000;
                        foreach($value as $keys => $values)
                        {
                            $caches['lIssue']['lottery_type'] = $caches['lIssue']["seLotid"];
                            $caches['lIssue']['phase'] = $caches['lIssue']["seExpect"];
                            $caches['lIssue']['result'] = $caches['lIssue']["awardNumber"];
                            $billion > 0 ? $caches['lIssue']['pool_amount'] = $billion."亿".$million."万" : ($million > 0 ? $caches['lIssue']['pool_amount'] = $million."万".$yuan."元" : $caches['lIssue']['pool_amount'] = 0);
                            $caches['lIssue']['time_draw'] = date("Y-m-d", $caches['lIssue']["awardTime"]/1000);
                            $caches['lIssue'][$keys] = $values;
                        }
                    }

                }
                $arr = array('lottery_type', 'phase', 'result', 'pool_amount', 'time_draw', 'open_day', 'open_time', 'detail', 'zst', 'purchase', 'buy_together', 'type', 'lottery_name');
                $lotteryId = array('51', '52', '33', '35', '10022', '23528', '23529');
                if(!empty($caches['lIssue']) )
                {
                    foreach($arr as $key => $value)
                    {
                        $award[$value] = $caches['lIssue'][$value];
                    }
                    array_push($awards, $award);
                }
                $responses = $awards;
            };
            if(!empty($responses) )
            {
                foreach($responses as $key => $value)
                {
                    if(in_array($responses[$key]['lottery_type'], $lotteryId ))
                    {
                        $awardHot[$key] = $responses[$key];
                    }
                }
            }
            array_push($response['data'], $awardHot);
            array_push($response['data'], $jclqresponse);
            array_push($response['data'], $jczqresponse);
            echo json_encode($response);
    }
    
    public function getJcMatch($lottery)
    {
    	$REDIS = $this->config->item('REDIS');
    	$this->load->driver('cache', array('adapter' => 'redis'));
    	$$lottery = $this->cache->redis->get($REDIS[strtoupper($lottery).'_MATCH']);
    	$lotteryArr = json_decode($$lottery, true);
    	$hot = array();
    	if (!empty($lotteryArr))
    	{
    		foreach ($lotteryArr as $mid => $val)
    		{
    			if (empty($val['spfSp3']) && empty($val['rfsfHf']))
    			{
    				unset($lotteryArr[$mid]);
    			}else
    			{
    				$hot[$val['hotid']][(($val['jzdt']/1000)-time()).$mid] = $val['mid'];
    			}
    		}
    		krsort($hot);
    		foreach ($hot as $k => $value)
    		{
    			ksort($value);
    			$hot[$k] = $value;
    			$hot[$k] = array_values($hot[$k]);
    		}
    		$lArr = array();
    		for ($i = 1; $i <= 10; $i++) {
    			if ($i == 10) {
    				$j = 0;
    			}else {
    				$j = $i;
    			}
    			if (!empty($hot[$j])) {
    				foreach ($hot[$j] as $k => $hv) {
    					if (count($lArr) < 3) {
    						$lArr[$hv] = $lotteryArr[$hv];
    					}else {
    						unset($hot[$j][$k]);
    					}
    				}
    			}
    		}
    	}
    	exit(json_encode(array($lArr, $hot)));
    }
}

