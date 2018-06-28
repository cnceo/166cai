<?php

/**
 * Copyright (c) 2015,上海二三四五网络科技有限公司.
 * 摘    要:
 * 作    者: 刁寿钧
 * 修改日期: 2016/3/21
 * 修改时间: 20:13
 */
if ( ! defined('BASEPATH'))
{
    exit('No direct script access allowed');
}

class Info extends MY_Controller
{
	
	private $_sy;
	
    public function __construct()
    {
        parent::__construct();
        $this->load->model('Info_Model');
        $this->load->model('info_comments_model');
        $this->load->driver('cache', array('adapter' => 'redis'));
        $REDIS = $this->config->item('REDIS');
        $this->_sy = unserialize($this->cache->redis->get($REDIS['SHOUYE']));
    }

    /**
     * 网站公告 列表
     * @Author diaosj
     */
    public function index()
    {
    	$infolist = $this->_sy['infolist'];
    	foreach ($infolist as $info) {
    		$data[$info['ename']] = $info['content'];
    	}
    	$data['banner'] = $this->_sy['infobanner'];
    	$data['htype'] = 1;
    	$this->display('/info/index', $data, 'v1.1');
    }
    
    public function lists($category){
    	$cpage = $this->input->get('cpage', true);
    	$cpage = ($cpage <= 1) ? 1 : (is_numeric($cpage) ? intval($cpage) : null);
    	if (!$cpage || $category >= 9) {
    		$this->show_404();
    	}
    	$offset = 20;
    	$infolist = $this->_sy['infolist'];
    	$res = $this->Info_Model->getListByCategory($category, ($cpage-1) * $offset, $offset);
    	$pdata['pagenum'] = ceil($res['num'] / $offset);
    	if ($cpage > $pdata['pagenum'] && $pdata['pagenum'] > 0) {
    		$this->redirect('/info/lists/'.$category.'?cpage='.$pdata['pagenum']);
    	}
    	$pagestr = $this->load->view('v1.1/elements/common/pages', $pdata, true);
    	$data = array(
    		'data' => $res['data'], 
    		'pagestr' => $pagestr, 
    		'num' => $res['num'], 
    		'pagenum' => $pdata['pagenum'],
    		'pageNumber' => $cpage,
    		'category'=> $category,
    		'ename' => $infolist[$category]['ename'],
    		'cname' => $infolist[$category]['cname']
    	);
    	if ($category == 3) {
    		list($schemes, $luckyTime, $luckyNumbs) = $this->randomSchemes(array('qlc'));
    		$data['luckyArr'] = array(
    			array('lid' => QLC, 'ename' => 'qlc', 'cname' => '七乐彩', 'info' => $this->Lottery->getKjinfo(QLC))
    		);
    		$data['schemes'] = $schemes;
    		$data['luckyTime'] = $luckyTime;
    		$data['luckyNumbs'] = $luckyNumbs;
    	}elseif (in_array($category, array(1, 2, 4, 5))) {
    		list($schemes, $luckyTime, $luckyNumbs) = $this->randomSchemes(array('ssq', 'dlt'));
    		$data['luckyArr'] = array(
    			array('lid' => SSQ, 'ename' => 'ssq', 'cname' => '双色球', 'info' => $this->Lottery->getKjinfo(SSQ)),
    			array('lid' => DLT, 'ename' => 'dlt', 'cname' => '大乐透', 'info' => $this->Lottery->getKjinfo(DLT))
    		);
    		$data['schemes'] = $schemes;
    		$data['luckyTime'] = $luckyTime;
    		$data['luckyNumbs'] = $luckyNumbs;
    	}
    	switch ($category) {
    		case '1':
    		case '2':
    			$data['kj'] = array(
	    			array('lid' => SSQ, 'ename' => 'ssq', 'cname' => '双色球', 'info' => $this->Lottery->getKjinfo(SSQ)),
	    			array('lid' => DLT, 'ename' => 'dlt', 'cname' => '大乐透', 'info' => $this->Lottery->getKjinfo(DLT))
    			);
    			break;
    		case '3':
    			$data['kj'] = array(
	    			array('lid' => FCSD, 'ename' => 'fcsd', 'enkj' => 'fc3d', 'cname' => '福彩3D', 'info' => $this->Lottery->getKjinfo(FCSD)),
	    			array('lid' => QLC, 'ename' => 'qlc', 'cname' => '七乐彩', 'info' => $this->Lottery->getKjinfo(QLC))
    			);
    			break;
    		case '4':
    			$data['kj'] = array(
	    			array('lid' => DLT, 'ename' => 'dlt', 'cname' => '大乐透', 'info' => $this->Lottery->getKjinfo(DLT)),
	    			array('lid' => SSQ, 'ename' => 'ssq', 'cname' => '双色球', 'info' => $this->Lottery->getKjinfo(SSQ))
    			);
    			break;
    		case '5':
    			$data['kj'] = array(
	    			array('lid' => PLS, 'ename' => 'pls', 'enkj' => 'pl3', 'cname' => '排列三', 'info' => $this->Lottery->getKjinfo(PLS)),
	    			array('lid' => PLW, 'ename' => 'plw', 'enkj' => 'pl5', 'cname' => '排列五', 'info' => $this->Lottery->getKjinfo(PLW))
    			);
    			break;
    		case '6':
    			list($allHotCount, $hotMatches, $focusMatches, $matches, $hot) = $this->loopMatches(JCZQ);
    			$data['matches'] = $matches;
    			$data['hotMatches'] = $hotMatches;
    			$data['hot'] = $hot;
    			$data['lotteryId'] = JCZQ;
    			$data['recomm'] = $infolist[9]['content'];
    			break;
    		case '7':
    			$data['kj'] = array(
    				array('lid' => SFC, 'ename' => 'sfc', 'cname' => '胜负彩', 'info' => $this->Lottery->getKjinfo(SFC), 'pool' => $this->Lottery->getDetail(11))
    			);
    			$data['hotMatches'] = $hotMatches;
    			$data['recomm'] = $infolist[9]['content'];
    			break;
    		case '8':
    			list($allHotCount, $hotMatches, $focusMatches, $matches, $hot) = $this->loopMatches(JCLQ);
    			$data['hotMatches'] = $hotMatches;
    			$data['matches'] = $matches;
    			$data['hot'] = $hot;
    			$data['lotteryId'] = JCLQ;
    			$data['recomm'] = $infolist[9]['content'];
    			break;
    	}
    	$this->display('/info/list', $data, 'v1.1');
    }

    public function csxw($id)
    {
        $detailInfo = $this->Info_Model->getInfoById($id);
        if ($detailInfo['n']['category_id'] != 1)
        {
            $this->show_404();
        }
        $this->info_comments_model->updateReadNum(intval($id));
        $categoryName = '彩市新闻';
        list($schemes, $luckyTime, $luckyNumbs) = $this->randomSchemes(array('ssq', 'dlt'));
        $category = 1;
        $left = $detailInfo['l'];
        $right = $detailInfo['r'];
        $cnName = $pageTitle = $detailInfo['n']['title'];
        $luckyArr = array(
        	array('lid' => SSQ, 'ename' => 'ssq', 'cname' => '双色球', 'info' => $this->Lottery->getKjinfo(SSQ)),
        	array('lid' => DLT, 'ename' => 'dlt', 'cname' => '大乐透', 'info' => $this->Lottery->getKjinfo(DLT))
        );
        $kj = array(
        	array('lid' => SSQ, 'ename' => 'ssq', 'cname' => '双色球', 'info' => $this->Lottery->getKjinfo(SSQ)),
        	array('lid' => DLT, 'ename' => 'dlt', 'cname' => '大乐透', 'info' => $this->Lottery->getKjinfo(DLT))
        );
        $result = $detailInfo['n'];
        $xg = $this->_sy['infolist'][1]['xg'];
        $htype = 1;
        $this->display(
            "info/digital",
            compact('pageTitle', 'result', 'left', 'xg', 'right', 'ename', 'kj', 'categoryName', 'schemes', 'luckyTime', 'luckyArr', 'luckyNumbs', 'category', 'cnName', 'htype'),
            'v1.1'
        );
    }

    public function ssq($id)
    {
        $detailInfo = $this->Info_Model->getInfoById($id);
        if ($detailInfo['n']['category_id'] != 2)
        {
            $this->show_404();
        }
        $this->info_comments_model->updateReadNum(intval($id));
        list($schemes, $luckyTime, $luckyNumbs) = $this->randomSchemes(array('ssq', 'dlt'));
        $categoryName = '数字彩-双色球';
        $result = $detailInfo['n'];
        $left = $detailInfo['l'];
        $right = $detailInfo['r'];
        $cnName = $pageTitle = $detailInfo['n']['title'];
        $xg = $this->_sy['infolist'][2]['xg'];
        $category = 2;
        $luckyArr = array(
        	array('lid' => SSQ, 'ename' => 'ssq', 'cname' => '双色球', 'info' => $this->Lottery->getKjinfo(SSQ)),
        	array('lid' => DLT, 'ename' => 'dlt', 'cname' => '大乐透', 'info' => $this->Lottery->getKjinfo(DLT))
        );
        $kj = array(
        	array('lid' => SSQ, 'ename' => 'ssq', 'cname' => '双色球', 'info' => $this->Lottery->getKjinfo(SSQ)),
        	array('lid' => DLT, 'ename' => 'dlt', 'cname' => '大乐透', 'info' => $this->Lottery->getKjinfo(DLT))
        );
        $htype = 1;
        $this->display(
            "info/digital",
            compact('pageTitle', 'result', 'left', 'xg', 'right', 'ename', 'kj', 'categoryName', 'schemes', 'luckyTime', 'luckyArr', 'luckyNumbs', 'category', 'cnName', 'htype'),
            'v1.1'
        );
    }

    public function qtfc($id)
    {
        $detailInfo = $this->Info_Model->getInfoById($id);
        if ($detailInfo['n']['category_id'] != 3)
        {
            $this->show_404();
        }
        $this->info_comments_model->updateReadNum(intval($id));
        list($schemes, $luckyTime, $luckyNumbs) = $this->randomSchemes(array('qlc'));
        $kj = array(
	    	array('lid' => FCSD, 'ename' => 'fcsd', 'enkj' => 'fc3d', 'cname' => '福彩3D', 'info' => $this->Lottery->getKjinfo(FCSD)),
	    	array('lid' => QLC, 'ename' => 'qlc', 'cname' => '七乐彩', 'info' => $this->Lottery->getKjinfo(QLC))
    	);
        $luckyArr = array(
        	array('lid' => QLC, 'ename' => 'qlc', 'cname' => '七乐彩', 'info' => $this->Lottery->getKjinfo(QLC))
        );
        $result = $detailInfo['n'];
        $left = $detailInfo['l'];
        $right = $detailInfo['r'];
        $cnName = $pageTitle = $detailInfo['n']['title'];
        $xg = $this->_sy['infolist'][3]['xg'];
        $category = 4;
        $categoryName = '其他福彩';
        $htype = 1;
        $this->display(
            "info/digital",
            compact('pageTitle', 'result', 'left', 'xg', 'right', 'kj', 'categoryName', 'schemes', 'luckyTime', 'luckyArr', 'luckyNumbs', 'category', 'cnName', 'htype'),
            'v1.1'
        );
    }

    public function dlt($id)
    {
        $detailInfo = $this->Info_Model->getInfoById($id);
        if ($detailInfo['n']['category_id'] != 4)
        {
            $this->show_404();
        }
        $this->info_comments_model->updateReadNum(intval($id));
        $categoryName = '数字彩-大乐透';
        $result = $detailInfo['n'];
        $left = $detailInfo['l'];
        $right = $detailInfo['r'];
        $cnName = $pageTitle = $detailInfo['n']['title'];
        $category = 4;
        $kj = array(
        	array('lid' => DLT, 'ename' => 'dlt', 'cname' => '大乐透', 'info' => $this->Lottery->getKjinfo(DLT)),
    		array('lid' => SSQ, 'ename' => 'ssq', 'cname' => '双色球', 'info' => $this->Lottery->getKjinfo(SSQ))
        );
        list($schemes, $luckyTime, $luckyNumbs) = $this->randomSchemes(array('ssq', 'dlt'));
        $xg = $this->_sy['infolist'][4]['xg'];
        $luckyArr = array(
        	array('lid' => SSQ, 'ename' => 'ssq', 'cname' => '双色球', 'info' => $this->Lottery->getKjinfo(SSQ)),
        	array('lid' => DLT, 'ename' => 'dlt', 'cname' => '大乐透', 'info' => $this->Lottery->getKjinfo(DLT))
        );
        $htype = 1;
        $this->display(
            "info/digital",
            compact('pageTitle', 'result', 'left', 'right', 'xg', 'ename', 'kj', 'categoryName', 'schemes', 'luckyTime', 'luckyArr', 'luckyNumbs', 'category', 'cnName', 'htype'),
            'v1.1'
        );
    }

    public function qttc($id)
    {
        $detailInfo = $this->Info_Model->getInfoById($id);
        if ($detailInfo['n']['category_id'] != 5)
        {
            $this->show_404();
        }
        $this->info_comments_model->updateReadNum(intval($id));
        $categoryName = '数字彩-其他体彩';
        list($schemes, $luckyTime, $luckyNumbs) = $this->randomSchemes(array('ssq', 'dlt'));
    	$luckyArr = array(
    		array('lid' => SSQ, 'ename' => 'ssq', 'cname' => '双色球', 'info' => $this->Lottery->getKjinfo(SSQ)),
    		array('lid' => DLT, 'ename' => 'dlt', 'cname' => '大乐透', 'info' => $this->Lottery->getKjinfo(DLT))
    	);
        $kj = array(
	    	array('lid' => PLS, 'ename' => 'pls', 'enkj' => 'pl3', 'cname' => '排列三', 'info' => $this->Lottery->getKjinfo(PLS)),
	    	array('lid' => PLW, 'ename' => 'plw', 'enkj' => 'pl5', 'cname' => '排列五', 'info' => $this->Lottery->getKjinfo(PLW))
    	);
        $category = 5;
        $cnName = $pageTitle = $detailInfo['n']['title'];
        $left = $detailInfo['l'];
        $right = $detailInfo['r'];
        $result = $detailInfo['n'];
        $xg = $this->_sy['infolist'][5]['xg'];
        $htype = 1;
        $this->display(
            "info/digital",
            compact('pageTitle', 'xg', 'result', 'left', 'right', 'kj', 'categoryName', 'schemes', 'luckyTime', 'luckyArr', 'luckyNumbs', 'category', 'cnName', 'htype'),
            'v1.1'
        );
    }

    public function jczq($id)
    {
        $detailInfo = $this->Info_Model->getInfoById($id);
        if ($detailInfo['n']['category_id'] != 6)
        {
            $this->show_404();
        }
        $this->info_comments_model->updateReadNum(intval($id));
        $categoryName = '竞技彩-竞彩足球';
        $lotteryId = JCZQ;
        list($allHotCount, $hotMatches, $focusMatches, $matches, $hot) = $this->loopMatches(JCZQ);
        $cnName = $pageTitle = $detailInfo['n']['title'];
        $result = $detailInfo['n'];
        $left = $detailInfo['l'];
        $right = $detailInfo['r'];
        $infolist = $this->_sy['infolist'];
        $xg = $this->_sy['infolist'][6]['xg'];
        $recomm = $infolist[9]['content'];
        $htype = 1;
        $this->display(
            "info/match",
            compact('pageTitle', 'xg', 'left', 'right', 'result','categoryName','hotMatches', 'focusMatches', 'recomm', 'allHotCount', 'lotteryId', 'matches', 'hot', 'cnName', 'htype'),
            'v1.1'
        );
    }

    public function sfc($id)
    {
        $detailInfo = $this->Info_Model->getInfoById($id);
        if ($detailInfo['n']['category_id'] != 7)
        {
            $this->show_404();
        }
        $this->info_comments_model->updateReadNum(intval($id));
        $categoryName = '竞技彩-胜负彩';
        $lotteryId = SFC;
        $sfcInfo = $this->getSFCInfo();
        $cnName = $pageTitle = $detailInfo['n']['title'];
        $left = $detailInfo['l'];
        $right = $detailInfo['r'];
        $result = $detailInfo['n'];
        $infolist = $this->_sy['infolist'];
        $xg = $this->_sy['infolist'][7]['xg'];
        $recomm = $infolist[9]['content'];
        $htype = 1;
        $this->display(
            "info/sfc",
            compact('pageTitle', 'result', 'left', 'right', 'categoryName', 'xg', 'sfcInfo', 'hotMatches', 'focusMatches', 'recomm', 'allHotCount', 'lotteryId', 'matches', 'hot', 'cnName', 'htype'),
            'v1.1'
        );
    }

    public function jclq($id)
    {
        $detailInfo = $this->Info_Model->getInfoById($id);
        if ($detailInfo['n']['category_id'] != 8)
        {
            $this->show_404();
        }
        $this->info_comments_model->updateReadNum(intval($id));
        $categoryName = '竞技彩-竞彩篮球';
        $lotteryId = JCLQ;
        list($allHotCount, $hotMatches, $focusMatches, $matches, $hot) = $this->loopMatches(JCLQ);
        $cnName = $pageTitle = $detailInfo['n']['title'];
        $left = $detailInfo['l'];
        $right = $detailInfo['r'];
        $result = $detailInfo['n'];
        $infolist = $this->_sy['infolist'];
        $recomm = $infolist[9]['content'];
        $xg = $this->_sy['infolist'][8]['xg'];
        $htype = 1;
        $this->display(
            "info/match",
            compact('pageTitle', 'result', 'left', 'right', 'xg', 'left', 'right', 'categoryName', 'hotMatches', 'focusMatches', 'recomm', 'allHotCount', 'lotteryId', 'matches', 'hot', 'cnName', 'htype'),
            'v1.1'
        );
    }

    private function randomSchemes($enames)
    {
        $REDIS = $this->config->item('REDIS');
        $luckyNumbs = unserialize($this->cache->redis->get($REDIS['NUM_LUCKY']));
        $luckyTime = $this->cache->redis->get($REDIS['LUCKY_TIME']);
        $today = date('Y-m-d');
        $ssqSchemes = unserialize($this->cache->redis->get($REDIS['SSQ_LUCKY']));
        $dltSchemes = unserialize($this->cache->redis->get($REDIS['DLT_LUCKY']));
        $qlcSchemes = unserialize($this->cache->redis->get($REDIS['QLC_LUCKY']));
//         if (empty($ssqSchemes) || empty($dltSchemes) || empty($qlcSchemes) || empty($luckyNumbs) || $luckyTime < $today) {
			if ($k == 0) {
				$allNumbs = range(1, 30);
				shuffle($allNumbs);
				for ($i = 0; $i < 12; $i ++)
				{
					$luckyNumbs[$i] = $allNumbs[$i];
				}
				$this->cache->redis->save($REDIS['NUM_LUCKY'], serialize($luckyNumbs), 86400);
				$this->cache->redis->save($REDIS['LUCKY_TIME'], $today, 86400);
			}
        	foreach ($luckyNumbs as $index => $numb){
        		$tempIndexes = array_rand(array_diff(range(1, 30), array($numb)), 5);
        		$ssqSchemesTemp = array();
        		foreach ($tempIndexes as $tIndex)
        		{
        			$ssqSchemesTemp[] = $tIndex + 1;
        		}
        		$ssqSchemesTemp[] = $numb;
        		sort($ssqSchemesTemp);
        		$tempIndex = array_rand(array_diff(range(1, 16), array($ssqSchemesTemp)), 1);
        		$ssqSchemesTemp[] = $tempIndex + 1;
        		$ssqSchemes[$index] = implode(',', $ssqSchemesTemp);
        	}
        	$schemes['ssq'] = $ssqSchemes;
        	$this->cache->redis->save($REDIS['SSQ_LUCKY'], serialize($ssqSchemes), 86400);
        	foreach ($luckyNumbs as $index => $numb){
        		$tempIndexes = array_rand(array_diff(range(1, 30), array($numb)), 4);
			    $dltSchemesTemp = array();
			    foreach ($tempIndexes as $tIndex)
			    {
			        $dltSchemesTemp[] = $tIndex + 1;
			    }
			    $dltSchemesTemp[] = $numb;
			    $tempIndexes = array_rand(array_diff(range(1, 12), array($dltSchemesTemp)), 2);
			    foreach ($tempIndexes as $tIndex)
			    {
			        $dltSchemesTemp[] = $tIndex + 1;
			    }
			    
			    $dltSchemes[$index] = implode(',', $dltSchemesTemp);
        	}
        	$schemes['dlt'] = $dltSchemes;
        	$this->cache->redis->save($REDIS['DLT_LUCKY'], serialize($dltSchemes), 86400);
        	foreach ($luckyNumbs as $index => $numb){
        		$tempIndexes = array_rand(range(1, 30), 6);
        		$qlcSchemesTemp = array();
        		foreach ($tempIndexes as $tIndex)
        		{
        			$qlcSchemesTemp[] = $tIndex + 1;
        		}
        		if (in_array($numb, $qlcSchemesTemp))
        		{
        			$tempAry = array_diff(range(1, 30), $qlcSchemesTemp);
        			$randomIndex = array_rand($tempAry);
        			$qlcSchemesTemp[] = $tempAry[$randomIndex];
        		}
        		else
        		{
        			$qlcSchemesTemp[] = $numb;
        		}
        		sort($qlcSchemesTemp);
        		$qlcSchemes[$index] = implode(',', $qlcSchemesTemp);
        	}
        	$schemes['qlc'] = $qlcSchemes;
        	$this->cache->redis->save($REDIS['QLC_LUCKY'], serialize($qlcSchemes), 86400);
//         }
        foreach ($enames as $k => $ename) {
        	$str = $ename."Schemes";
        	$schemes[$ename] = $$str;
        }
        return array($schemes, $luckyTime, $luckyNumbs);
    }

    private function loopMatches($lotteryId)
    {
        $matchAry = $this->getMatchData($lotteryId);
        if (empty($matchAry))
        {
            return array(array(), array());
        }

        $hotMatches = array();
        foreach ($matchAry as $key => $match)
        {
        	unset($matchAry[$key]['zhisheng']);
            if (empty($match['hot']))
            {
            	unset($matchAry[$key]);
                continue;
            }
            if ($lotteryId == JCZQ)
            {
                if ($match['spfGd'])
                {
                    $hotMatches[] = $match;
                }
            }
            if ($lotteryId == JCLQ)
            {
                if ($match['rfsfGd'])
                {
                    $hotMatches[] = $match;
                }
            }
        }
        $allHotCount = count($hotMatches);

        if ( ! empty($hotMatches))
        {
            $hotMatches = array_intersect_key($hotMatches, array_flip(array(array_rand($hotMatches, 1))));
        }

        $hot = array();
        if ( ! empty($matchAry))
        {
            foreach ($matchAry as $mid => $val)
            {
                if (empty($val['spfSp3']) && empty($val['rfsfGd']))
                {
                    unset($matchAry[$mid]);
                }
                else
                {
                    $hot[$val['hotid']][(($val['jzdt'] / 1000) - time()) . $mid] = $val['mid'];
                }
            }
            krsort($hot);
            foreach ($hot as $k => $value)
            {
                ksort($value);
                $hot[$k] = $value;
                $hot[$k] = array_values($hot[$k]);
            }
        }

        return array($allHotCount, $hotMatches, array(), $matchAry, $hot);
    }

    private function getMatchData($lotteryId)
    {
        $REDIS = $this->config->item('REDIS');
        switch ($lotteryId) {
        	case JCZQ:
        		$key = $REDIS['JCZQ_MATCH'];
        		break;
        	case JCLQ:
        		$key = $REDIS['JCLQ_MATCH'];
        		break;
        }
        return json_decode($this->cache->redis->get($key), TRUE);
    }

    private function getSFCInfo($issue = NULL)
    {
        $data = $this->Lottery->getDetail(11, $issue);
        $data['rj_sale'] = $this->jine_format($data['rj_sale']);
        $data['award'] = $this->jine_format($data['award']);
        $REDIS = $this->config->item('REDIS');
        $sfcCache = $this->cache->redis->get($REDIS['SFC_ISSUE']);
        $sfcAry = json_decode($sfcCache, TRUE);
        $data['awardTime'] = $sfcAry['lIssue']['awardTime'];

        return $data;
    }

    function jine_format($str)
    {
        if ($str < 1000)
        {
            return $str;
        }
        $num = strlen($str) % 3;
        $sl = substr($str, 0, $num);
        $sl = empty($sl) ? $sl : $sl . ",";
        $sr = substr($str, $num);
        $arr = str_split($sr, 3);

        return $sl . implode(',', $arr);
    }
    
    public function nbainjury(){
    	$sqArr = array(1 => '西南', 2 => '太平洋', 3 => '西北', 4 => '大西洋', 5 => '东南', 6 => '中部');
    	$matches = $this->getMatchData(JCLQ);
    	$REDIS = $this->config->item('REDIS');
    	$nba = unserialize($this->cache->redis->get($REDIS['NBA']));
    	foreach ($nba as $z => $na) {
    		foreach ($na as $p => $n) {
    			$teamArr[$n['team']] = array($n['id'], $z, $p);
    			foreach (explode('|', $n['nickName']) as $nickName) {
    				$teamArr[$nickName] = array($n['id'], $z, $p);
    			}
    		}
    	}
    	$matchs = array();
    	foreach ($matches as $k => $match) {
    		if ($match['nameSname'] == '美职篮') {
    			$key = date('md', $match['dt']/1000);
    			if ($key == date('md') || $key == date('md', strtotime("+1 day"))) {
    				$match['htid'] = $teamArr[$match['home']][0];
    				$match['atid'] = $teamArr[$match['awary']][0];
    				$match['hzone'] = $teamArr[$match['home']][1];
    				$match['azone'] = $teamArr[$match['awary']][1];
    				$match['hpriority'] = $teamArr[$match['home']][2];
    				$match['apriority'] = $teamArr[$match['awary']][2];
    				$nba[$match['azone']][$match['apriority']]['sname'] = $match['awarySname'];
    				$nba[$match['hzone']][$match['hpriority']]['sname'] = $match['homeSname'];
    				$matchs[$key][$k] = $match;
    				if (empty($first)) {
    					$first = $match;
    				}
    			}
    		}
    	}
    	if (empty($first)) {
    		$first['apriority'] = $first['hpriority'] = $first['azone'] = $first['hzone'] = 1;
    	}
    	$this->display('info/nba', array('matchs' => $matchs, 'nba' => $nba, 'sqArr' => $sqArr, 'first' => $first, 'htype' => 1), 'v1.1');
    }
    
    public function zjtjzq($id)
    {
    	$detailInfo = $this->Info_Model->getInfoById($id);
    	if ($detailInfo['n']['category_id'] != 9)
    	{
    		$this->show_404();
    	}
        $this->info_comments_model->updateReadNum(intval($id));
    	$REDIS = $this->config->item('REDIS');
    	$shouye = unserialize($this->cache->redis->get($REDIS['SHOUYE']));
    	$infolist = $shouye['infolist'];
    	$categoryName = '专家推荐';
    	$lotteryId = JCZQ;
    	list($allHotCount, $hotMatches, $focusMatches, $matches, $hot) = $this->loopMatches(JCZQ);
    	$cnName = $pageTitle = $detailInfo['n']['title'];
    	$result = $detailInfo['n'];
    	$recomm = $infolist[9]['content'];
    	$htype = 1;
    	$this->display(
    			"info/match",
    			compact('pageTitle', 'result','categoryName', 'hotMatches', 'focusMatches', 'recomm', 'allHotCount', 'lotteryId', 'matches', 'hot', 'cnName', 'htype'),
    			'v1.1'
    	);
    }
    
    public function zjtjlq($id)
    {
    	$detailInfo = $this->Info_Model->getInfoById($id);
    	if ($detailInfo['n']['category_id'] != 10)
    	{
    		$this->show_404();
    	}
        $this->info_comments_model->updateReadNum(intval($id));
    	$REDIS = $this->config->item('REDIS');
    	$shouye = unserialize($this->cache->redis->get($REDIS['SHOUYE']));
    	$infolist = $shouye['infolist'];
    	$categoryName = '专家推荐';
    	$lotteryId = JCLQ;
    	list($allHotCount, $hotMatches, $focusMatches, $matches, $hot) = $this->loopMatches(JCLQ);
    	$cnName = $pageTitle = $detailInfo['n']['title'];
    	$result = $detailInfo['n'];
    	$recomm = $infolist[9]['content'];
    	$htype = 1;
    	$this->display(
    			"info/match",
    			compact('pageTitle', 'result','categoryName', 'hotMatches', 'focusMatches', 'recomm', 'allHotCount', 'lotteryId', 'matches', 'hot', 'cnName', 'htype'),
    			'v1.1'
    	);
    }
}