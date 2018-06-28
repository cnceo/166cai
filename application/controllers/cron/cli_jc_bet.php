<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/*
 * 竞彩（竞足/竞篮/胜负彩）投注比例占比
 * @date:2017-05-25
 */

class Cli_Jc_Bet extends MY_Controller
{
	public function __construct()
    {
        parent::__construct();
        $this->load->model('jc_bet_model');
        $this->lidMap = $this->jc_bet_model->getLidMap();
    }

    private $limit = 2000;

    // 主函数
    public function index()
    {
        $lidMap = $this->lidMap;
        if(!empty($lidMap))
        {
            foreach ($lidMap as $lid => $items) 
            {
                $this->calBetCount($lid);
            }
        }
    }

    // 按彩种统计
    public function calBetCount($lid)
    {
        $calData = array();
        $matchInfo = $this->getMatchInfo($lid);

        if(!empty($matchInfo))
        {
            if(in_array($lid, array(42, 43)))
            {
                foreach ($matchInfo as $mid => $items) 
                {
                    $calResult = $this->calMatchBet($lid, $mid);
                    $calData[$mid] = $calResult;
                }
            }
            else
            {
                if(!empty($matchInfo['cIssue']))
                {
                    $mid = $matchInfo['cIssue']['seExpect'];
                    $sale_time = date('Y-m-d H:i:s',substr($matchInfo['cIssue']['sale_time'], 0, 10));
                    $calData = $this->calSfcBet($mid, $sale_time);
                }         
            }   
        }

        if(!empty($calData))
        {
            $this->saveBetInfo($lid, $calData);
        }
    }

    // 获取在售对阵
    public function getMatchInfo($lid)
    {
        $matchCache = $this->lidMap[$lid]['matchCache'];
        $this->load->driver('cache', array('adapter' => 'redis'));
        $REDIS = $this->config->item('REDIS');
        $ukey = "{$REDIS[$matchCache]}";
        $info = $this->cache->redis->get($ukey);
        $info = json_decode($info, true);
        return $info;
    }

    // 写入缓存
    public function saveBetInfo($lid, $calData)
    {
        $countCache = $this->lidMap[$lid]['countCache'];
        $this->load->driver('cache', array('adapter' => 'redis'));
        $REDIS = $this->config->item('REDIS');
        $ukey = "{$REDIS[$countCache]}";
        $this->cache->save($ukey, json_encode($calData), 0);
    }

    public function calMatchBet($lid, $mid)
    {
        // 初始化数据
        $calResult = array();
        $betCal = array();
        
        $pTypeArr = $this->lidMap[$lid]['playType'];
        $cal = $this->lidMap[$lid]['cal'];
        foreach ($pTypeArr as $key => $playType) 
        {
            $betCal[$playType] = $cal;
        }
        
        $last_id = 0;
        if($lid == 42)
        {
            $match = $this->jc_bet_model->getJczqBet($mid);
        }
        else
        {
            $match = $this->jc_bet_model->getJclqBet($mid);
        }
        
        if(!empty($match))
        {
            foreach ($match as $key => $pTypeData) 
            {
                if(!empty($pTypeData['last_id']) && $pTypeData['last_id'] > 0)
                {
                    $last_id = $pTypeData['last_id'];
                }
                $betCal[$pTypeData['ptype']]['s'] = $pTypeData['s'] ? $pTypeData['s'] : 0;
                $betCal[$pTypeData['ptype']]['p'] = $pTypeData['p'] ? $pTypeData['p'] : 0;
                $betCal[$pTypeData['ptype']]['f'] = $pTypeData['f'] ? $pTypeData['f'] : 0;
            }
        }

        // 累计查询relation数据
        $limit = $this->limit;
        $count = $this->jc_bet_model->getRelationCount($lid, $mid, $last_id);
        if($count > 0)
        {
            $size = ceil($count/$limit);
            for ($page = 1; $page < $size + 1; $page++)
            {
                $matchs = $this->jc_bet_model->getMatchRelation($lid, $mid, $last_id, $page, $limit);
                if(!empty($matchs))
                {
                    foreach ($matchs as $key => $items) 
                    {
                        $betCal[$items['ptype']]['s'] += ($items['s'] > 0 ? 1 : 0);
                        $betCal[$items['ptype']]['p'] += ($items['p'] > 0 ? 1 : 0);
                        $betCal[$items['ptype']]['f'] += ($items['f'] > 0 ? 1 : 0);
                        $last_id = $items['orderId'];
                    }
                }
            }
        }

        // 更新统计
        foreach ($betCal as $ptype => $items)
        {
            $info = array(
                'ptype'     =>  $ptype,
                'mid'       =>  $mid,
                's'         =>  $items['s'],
                'p'         =>  $items['p'],
                'f'         =>  $items['f'],
                'last_id'   =>  $last_id ? $last_id : 0,
            );

            // 计算投注比例
            $sum = $items['s'] + $items['p'] + $items['f'];
            $info['spv'] = ($sum > 0) ? sprintf("%.1f",substr(sprintf("%.2f", $items['s']/$sum * 100), 0, -1)) : '0.0';
            $info['ppv'] = ($sum > 0) ? sprintf("%.1f",substr(sprintf("%.2f", $items['p']/$sum * 100), 0, -1)) : '0.0';
            $others = $info['spv'] + $info['ppv'];
            $info['fpv'] = ($sum > 0) ? (($others > 0 || $items['f'] > 0) ? number_format((100 - $others), 1) : '0.0') : '0.0';

            // 处理平字段
            if($lid != 42)
            {
                unset($info['p']);
                unset($info['ppv']);
                $calResult[$ptype] = $info['fpv'] . ',' . $info['spv'];
            }
            else
            {
                $calResult[$ptype] = $info['spv'] . ',' . $info['ppv'] . ',' . $info['fpv'];
            }
            $this->jc_bet_model->insertMatchDetail($lid, $info);  
        }
        return $calResult;
    }

    public function calSfcBet($mid, $sale_time)
    {
        // 初始化14场数据
        $calResult = array();        
        $betCal = $this->initSfcMatch();

        $last_id = 0;

        $match = $this->jc_bet_model->getSfcBet($mid);

        if(!empty($match))
        {
            foreach ($match as $key => $items) 
            {
                $betCal[$items['mname']]['s'] = $items['s'] ? $items['s'] : 0;
                $betCal[$items['mname']]['p'] = $items['p'] ? $items['p'] : 0;
                $betCal[$items['mname']]['f'] = $items['f'] ? $items['f'] : 0;
                $last_id = $items['last_id'];
            }
        }

        // 查询cp_orders数据
        $limit = $this->limit;
        $count = $this->jc_bet_model->getSfcOrderCount($mid, $sale_time, $last_id);

        if($count > 0)
        {
            $size = ceil($count/$limit);
            for ($page = 1; $page < $size + 1; $page++)
            {
                $orders = $this->jc_bet_model->getSfcBetOrder($mid, $sale_time, $last_id, $page, $limit);
                if(!empty($orders))
                {
                    foreach ($orders as $key => $items) 
                    {
                        $betCal['1']['s'] += preg_match('/[#10]{0,2}3[#10]{0,2}(,[#310]{0,3}){13}:/', $items['codes']);
                        $betCal['1']['p'] += preg_match('/[#30]{0,2}1[#30]{0,2}(,[#310]{0,3}){13}:/', $items['codes']); 
                        $betCal['1']['f'] += preg_match('/[#31]{0,2}0[#31]{0,2}(,[#310]{0,3}){13}:/', $items['codes']); 

                        $betCal['2']['s'] += preg_match('/([#310]{0,3},){1}[#10]{0,2}3[#10]{0,2}(,[#310]{0,3}){12}:/', $items['codes']);
                        $betCal['2']['p'] += preg_match('/([#310]{0,3},){1}[#30]{0,2}1[#30]{0,2}(,[#310]{0,3}){12}:/', $items['codes']); 
                        $betCal['2']['f'] += preg_match('/([#310]{0,3},){1}[#31]{0,2}0[#31]{0,2}(,[#310]{0,3}){12}:/', $items['codes']); 

                        $betCal['3']['s'] += preg_match('/([#310]{0,3},){2}[#10]{0,2}3[#10]{0,2}(,[#310]{0,3}){11}:/', $items['codes']);
                        $betCal['3']['p'] += preg_match('/([#310]{0,3},){2}[#30]{0,2}1[#30]{0,2}(,[#310]{0,3}){11}:/', $items['codes']); 
                        $betCal['3']['f'] += preg_match('/([#310]{0,3},){2}[#31]{0,2}0[#31]{0,2}(,[#310]{0,3}){11}:/', $items['codes']); 

                        $betCal['4']['s'] += preg_match('/([#310]{0,3},){3}[#10]{0,2}3[#10]{0,2}(,[#310]{0,3}){10}:/', $items['codes']);
                        $betCal['4']['p'] += preg_match('/([#310]{0,3},){3}[#30]{0,2}1[#30]{0,2}(,[#310]{0,3}){10}:/', $items['codes']); 
                        $betCal['4']['f'] += preg_match('/([#310]{0,3},){3}[#31]{0,2}0[#31]{0,2}(,[#310]{0,3}){10}:/', $items['codes']);

                        $betCal['5']['s'] += preg_match('/([#310]{0,3},){4}[#10]{0,2}3[#10]{0,2}(,[#310]{0,3}){9}:/', $items['codes']);
                        $betCal['5']['p'] += preg_match('/([#310]{0,3},){4}[#30]{0,2}1[#30]{0,2}(,[#310]{0,3}){9}:/', $items['codes']); 
                        $betCal['5']['f'] += preg_match('/([#310]{0,3},){4}[#31]{0,2}0[#31]{0,2}(,[#310]{0,3}){9}:/', $items['codes']);

                        $betCal['6']['s'] += preg_match('/([#310]{0,3},){5}[#10]{0,2}3[#10]{0,2}(,[#310]{0,3}){8}:/', $items['codes']);
                        $betCal['6']['p'] += preg_match('/([#310]{0,3},){5}[#30]{0,2}1[#30]{0,2}(,[#310]{0,3}){8}:/', $items['codes']); 
                        $betCal['6']['f'] += preg_match('/([#310]{0,3},){5}[#31]{0,2}0[#31]{0,2}(,[#310]{0,3}){8}:/', $items['codes']);

                        $betCal['7']['s'] += preg_match('/([#310]{0,3},){6}[#10]{0,2}3[#10]{0,2}(,[#310]{0,3}){7}:/', $items['codes']);
                        $betCal['7']['p'] += preg_match('/([#310]{0,3},){6}[#30]{0,2}1[#30]{0,2}(,[#310]{0,3}){7}:/', $items['codes']); 
                        $betCal['7']['f'] += preg_match('/([#310]{0,3},){6}[#31]{0,2}0[#31]{0,2}(,[#310]{0,3}){7}:/', $items['codes']);

                        $betCal['8']['s'] += preg_match('/([#310]{0,3},){7}[#10]{0,2}3[#10]{0,2}(,[#310]{0,3}){6}:/', $items['codes']);
                        $betCal['8']['p'] += preg_match('/([#310]{0,3},){7}[#30]{0,2}1[#30]{0,2}(,[#310]{0,3}){6}:/', $items['codes']); 
                        $betCal['8']['f'] += preg_match('/([#310]{0,3},){7}[#31]{0,2}0[#31]{0,2}(,[#310]{0,3}){6}:/', $items['codes']);

                        $betCal['9']['s'] += preg_match('/([#310]{0,3},){8}[#10]{0,2}3[#10]{0,2}(,[#310]{0,3}){5}:/', $items['codes']);
                        $betCal['9']['p'] += preg_match('/([#310]{0,3},){8}[#30]{0,2}1[#30]{0,2}(,[#310]{0,3}){5}:/', $items['codes']); 
                        $betCal['9']['f'] += preg_match('/([#310]{0,3},){8}[#31]{0,2}0[#31]{0,2}(,[#310]{0,3}){5}:/', $items['codes']);

                        $betCal['10']['s'] += preg_match('/([#310]{0,3},){9}[#10]{0,2}3[#10]{0,2}(,[#310]{0,3}){4}:/', $items['codes']);
                        $betCal['10']['p'] += preg_match('/([#310]{0,3},){9}[#30]{0,2}1[#30]{0,2}(,[#310]{0,3}){4}:/', $items['codes']); 
                        $betCal['10']['f'] += preg_match('/([#310]{0,3},){9}[#31]{0,2}0[#31]{0,2}(,[#310]{0,3}){4}:/', $items['codes']);

                        $betCal['11']['s'] += preg_match('/([#310]{0,3},){10}[#10]{0,2}3[#10]{0,2}(,[#310]{0,3}){3}:/', $items['codes']);
                        $betCal['11']['p'] += preg_match('/([#310]{0,3},){10}[#30]{0,2}1[#30]{0,2}(,[#310]{0,3}){3}:/', $items['codes']); 
                        $betCal['11']['f'] += preg_match('/([#310]{0,3},){10}[#31]{0,2}0[#31]{0,2}(,[#310]{0,3}){3}:/', $items['codes']);

                        $betCal['12']['s'] += preg_match('/([#310]{0,3},){11}[#10]{0,2}3[#10]{0,2}(,[#310]{0,3}){2}:/', $items['codes']);
                        $betCal['12']['p'] += preg_match('/([#310]{0,3},){11}[#30]{0,2}1[#30]{0,2}(,[#310]{0,3}){2}:/', $items['codes']); 
                        $betCal['12']['f'] += preg_match('/([#310]{0,3},){11}[#31]{0,2}0[#31]{0,2}(,[#310]{0,3}){2}:/', $items['codes']);

                        $betCal['13']['s'] += preg_match('/([#310]{0,3},){12}[#10]{0,2}3[#10]{0,2}(,[#310]{0,3}){1}:/', $items['codes']);
                        $betCal['13']['p'] += preg_match('/([#310]{0,3},){12}[#30]{0,2}1[#30]{0,2}(,[#310]{0,3}){1}:/', $items['codes']); 
                        $betCal['13']['f'] += preg_match('/([#310]{0,3},){12}[#31]{0,2}0[#31]{0,2}(,[#310]{0,3}){1}:/', $items['codes']);

                        $betCal['14']['s'] += preg_match('/([#310]{0,3},){13}[#10]{0,2}3[#10]{0,2}:/', $items['codes']);
                        $betCal['14']['p'] += preg_match('/([#310]{0,3},){13}[#30]{0,2}1[#30]{0,2}:/', $items['codes']); 
                        $betCal['14']['f'] += preg_match('/([#310]{0,3},){13}[#31]{0,2}0[#31]{0,2}:/', $items['codes']);
                        $last_id = $items['orderId']; 
                    }  
                }
            }
        }

        if(!empty($betCal))
        {
            foreach ($betCal as $mname => $items) 
            {
                $info = array(
                    'mid'       =>  $mid,
                    'mname'     =>  $mname,
                    's'         =>  $items['s'],
                    'p'         =>  $items['p'],
                    'f'         =>  $items['f'],
                    'last_id'   =>  $last_id ? $last_id : 0,
                );

                // 计算投注比例
                $sum = $items['s'] + $items['p'] + $items['f'];
                $info['spv'] = ($sum > 0) ? sprintf("%.1f",substr(sprintf("%.2f", $items['s']/$sum * 100), 0, -1)) : '0.0';
                $info['ppv'] = ($sum > 0) ? sprintf("%.1f",substr(sprintf("%.2f", $items['p']/$sum * 100), 0, -1)) : '0.0';
                $others = $info['spv'] + $info['ppv'];
                $info['fpv'] = ($sum > 0) ? (($others > 0 || $items['f'] > 0) ? number_format((100 - $others), 1) : '0.0') : '0.0';

                $xid = $mid . str_pad($mname, 2, "0", STR_PAD_LEFT);
                $calResult[$xid] =  $info['spv'] . ',' . $info['ppv'] . ',' . $info['fpv'];
            
                $this->jc_bet_model->insertMatchDetail($lid = 11, $info);
            }
        }
        return $calResult;
    }

    public function initSfcMatch()
    {
        $match = array();
        for ($i = 1; $i <= 14; $i++) 
        { 
            $match[$i] = array(
                's' =>  0,
                'p' =>  0,
                'f' =>  0
            );
        }
        return $match;
    }

    
}