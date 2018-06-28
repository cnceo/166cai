<?php

/*
 * 十一选五类 组合遗漏 模型层
 * @author:liuli
 * @date:2017-06-26
 */

class Miss_Model extends MY_Model 
{
    // 玩法
    private $playType = array(
        1   =>  array(1),                   //  前一
        2   =>  array(2, 3, 4),             //  任二 单式、三码、四码
        3   =>  array(3, 4, 5),             //  任三 单式、四码、五码
        4   =>  array(4, 5, 6, 7),          //  任四 单式、五码、六码、七码
        5   =>  array(5),                   //  任五
        6   =>  array(6),                   //  任六
        7   =>  array(7),                   //  任七
        8   =>  array(8),                   //  任八
        9   =>  array(2),                   //  前二直选
        10  =>  array(3),                   //  前三直选
        11  =>  array(2, 3, 4, 5, 6, 7, 8), //  前二组选
        12  =>  array(3),                   //  前三组选
    );

    // 玩法说明
    private $playTypeName = array(
        1   =>  'qian1_zhixuan',
        2   =>  'renxuan2',
        3   =>  'renxuan3',
        4   =>  'renxuan4',
        5   =>  'renxuan5',
        6   =>  'renxuan6',
        7   =>  'renxuan7',
        8   =>  'renxuan8',
        9   =>  'qian2_zhixuan',
        10  =>  'qian3_zhixuan',
        11  =>  'qian2_zuxuan',
        12  =>  'qian3_zuxuan',
    );

    public function __construct() 
    {
        parent::__construct();
        $this->load->driver('cache', array('adapter' => 'redis'));
    }

    public function getMissData($lid, $playType, $modType = 0) 
    {
        $playTypeArr = $this->playType;
        // 默认码
        $modType = ($playTypeArr[$playType]) ? ((in_array($modType, $playTypeArr[$playType]) ? $modType : $playTypeArr[$playType][0]) ) : 0;

        // 取缓存
        $info = $this->getMissCache($lid, $playType, $modType);

        if(empty($info))
        {
            $info = $this->refreshMissCache($lid, $playType, $modType);
        }
        return $info;
    }

    public function getMissCache($lid, $playType, $modType)
    {
        $REDIS = $this->config->item('REDIS');
        $ukey = "{$REDIS['COMBINE_MISS']}{$lid}_{$playType}_{$modType}";
        $this->load->driver('cache', array('adapter' => 'redis'));
        $info = $this->cache->redis->get($ukey);
        $info = json_decode($info, true);
        return $info;
    }

    public function refreshMissCache($lid, $playType, $modType)
    {
        $sql = "SELECT lid, issue, playType, modType, codes, curMiss, lastMiss, maxMiss, showTotal, missTotal, lastTenMissingTimes, curHit, maxHit FROM cp_miss_data WHERE lid = ? AND playType = ? AND modType = ? ORDER BY curMiss DESC";
        $info = $this->slaveDc->query($sql, array($lid, $playType, $modType))->getAll();

        if(!empty($info))
        {
            foreach ($info as $key => $items) 
            {
                // 格式处理
                $info[$key]['codes'] = str_replace('|', ' | ', $items['codes']);
                // 近九期数据
                $lastMissArr = explode(',', $items['lastTenMissingTimes']);
                array_pop($lastMissArr);
                $info[$key]['lastTenMissingTimes'] = implode(',', $lastMissArr);
                // 平均
                $avg = $items['showTotal'] > 0 ? floor($items['missTotal']/$items['showTotal']) : 0;
                $info[$key]['avg'] = (string)$avg;
                // 预出率
                $prelv = $items['showTotal'] > 0 ? sprintf("%.2f", $items['curMiss']/($items['missTotal']/$items['showTotal'])) : 0.00;
                $info[$key]['prelv'] = (string)$prelv;
            }
            $REDIS = $this->config->item('REDIS');
            $ukey = "{$REDIS['COMBINE_MISS']}{$lid}_{$playType}_{$modType}";
            $this->cache->redis->save($ukey, json_encode($info), 10);
        }
        return $info;
    }

    // 遗漏投注排名
    public function getMissDataOrder($lid)
    {
        // 取缓存
        $info = $this->getMissOrderCache($lid);

        if(empty($info))
        {
            $info = $this->refreshMissOrderCache($lid);
        }
        return $info;
    }

    public function getMissOrderCache($lid)
    {
        $REDIS = $this->config->item('REDIS');
        $ukey = "{$REDIS['COMBINE_MISS']}{$lid}_group";
        $this->load->driver('cache', array('adapter' => 'redis'));
        $info = $this->cache->redis->get($ukey);
        $info = json_decode($info, true);
        return $info;
    }

    public function refreshMissOrderCache($lid)
    {
        $info = array();
        foreach ($this->playType as $playType => $val) 
        {

            $info[$this->playTypeName[$playType]] = $this->getMissOrders($lid, $playType);
        }

        $REDIS = $this->config->item('REDIS');
        $ukey = "{$REDIS['COMBINE_MISS']}{$lid}_group";
        $this->cache->redis->save($ukey, json_encode($info), 10);
        return $info;
    }

    public function getMissOrders($lid, $playType)
    {
        $modType = $this->playType[$playType][0];
        $sql = "SELECT lid, issue, playType, modType, codes, curMiss, lastMiss, maxMiss, showTotal, missTotal, lastTenMissingTimes, curHit, maxHit FROM cp_miss_data WHERE lid = ? AND playType = ? AND modType = ? ORDER BY curMiss DESC LIMIT 5";
        $info = $this->slaveDc->query($sql, array($lid, $playType, $modType))->getAll();

        $missData = array();
        if(!empty($info))
        {
            foreach ($info as $key => $items) 
            {
                // 平均
                $avg = $items['showTotal'] > 0 ? floor($items['missTotal']/$items['showTotal']) : 0;
                $missData[$key]['codes'] = str_replace('|', ' | ', $items['codes']);
                $missData[$key]['curMiss'] = $items['curMiss'];
                $missData[$key]['avg'] = (string)$avg;
            }
        }
        return $missData;
    }
}
