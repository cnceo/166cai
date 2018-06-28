<?php

/**
 * Copyright (c) 2015,上海二三四五网络科技有限公司.
 * 摘    要:
 * 作    者: 刁寿钧
 * 修改日期: 2015/12/7
 * 修改时间: 10:41
 */
class Optimize extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->driver('cache', array('adapter' => 'redis'));
    }

    /*
     * Carelessly planned projects take three times longer to complete than expected.
     * Carefully planned projects take four times longer to complete than expected,
     * mostly because the planners expect their planning to reduce the time it takes.
     */
    public function index()
    {
        $lotteryId = $this->input->post('lotteryId', TRUE);
        $source = $this->input->post('source', TRUE);
        $betNum = $this->input->post('betNum', TRUE);
        $midStr = $this->input->post('midStr', TRUE);
        $betStr = $this->input->post('betStr', TRUE);
        $endTime = $this->input->post('endTime', TRUE);
        $issue = $this->input->post('issue', TRUE);
        $betMoney = $this->input->post('betMoney', TRUE);
        $multi = $this->input->post('multi', TRUE);
        $openEndtime = $this->input->post('openEndtime', TRUE);

        if (empty($lotteryId) || empty($betNum) || empty($midStr) || empty($betStr) || empty($endTime)
            || empty($issue) || empty($betMoney) || empty($multi)
        ) {
            $this->redirect('/');
        }

        $matchInfoHash = $this->getMatchData($lotteryId);
        $midAry = explode(' ', $midStr);
        $aliveMidAry = array_intersect(array_keys($matchInfoHash), $midAry);
        if (count($aliveMidAry) < count($midAry)) {
            $this->display('optimize/error', compact(
                'lotteryId'
            ), 'v1.1');
        }
        else {
            $midAry = $aliveMidAry;
            sort($midAry);

            list(, $optionStr, $parlayStr) = explode('|', $betStr);
            preg_match_all('/\d(?=\*)/', $parlayStr, $matches);
            $parlays = $matches[0];
            list($matchToOption, $flatMatchOption) = $this->splitOptionStr($optionStr);

            if ($multi == 1) {
                $multi *= 2;
                $betMoney *= 2;
            }
            $minBetMoney = $betMoney / $multi * 2;
            $castStrAry = $this->composeCastStrAry($flatMatchOption, $parlays, $multi);
            $castStrAry = $this->averageOptimize($castStrAry);
            $this->load->library('BetCnName');
            $cnName = BetCnName::getCnName($lotteryId);
            $this->display('optimize/index', compact(
                'lotteryId',
                'source',
                'midAry',
                'matchToOption',
                'castStrAry',
                'matchInfoHash',
                'parlayStr',
                'betNum',
                'multi',
                'betMoney',
                'minBetMoney',
            	'cnName',
            	'openEndtime',
            	'endTime'
            ), 'v1.1');
        }
    }

    private function getMatchData($lotteryId)
    {
        $REDIS = $this->config->item('REDIS');
        $this->load->driver('cache', array('adapter' => 'redis'));
        $keyHash = array(
            JCLQ => $REDIS['JCLQ_MATCH'],
            JCZQ => $REDIS['JCZQ_MATCH'],
        );

        return json_decode($this->cache->redis->get($keyHash[$lotteryId]), TRUE);
    }

    private function splitOptionStr($optionStr)
    {
        $matchToOption = array();
        $flatMatchOption = array();
        $optionLns = explode(',', $optionStr);
        foreach ($optionLns as $optionLn) {
            list($typeStr, $offType) = explode('>', $optionLn);
            list($mid, $offMid) = explode('=', $offType);
            $optAry = explode('/', $offMid);
            foreach ($optAry as $optStr) {
                $tmpAry = array();
                preg_match('/([\d\-\:]+)(?:\(|\{)/', $optStr, $tmpAry);
                $yaaAry = array();
                preg_match('/\(([\.\d]+)\)/', $optStr, $yaaAry);
                if ( ! array_key_exists($mid, $matchToOption)) {
                    $matchToOption[$mid] = array();
                }
                if ( ! array_key_exists($typeStr, $matchToOption[$mid])) {
                    $matchToOption[$mid][$typeStr] = array();
                }
                $matchToOption[$mid][$typeStr][$tmpAry[1]] = $yaaAry[1];
                if ( ! array_key_exists($mid, $flatMatchOption)) {
                    $flatMatchOption[$mid] = array();
                }
                $flatMatchOption[$mid][] = implode('/', array($mid, $typeStr, $tmpAry[1], $yaaAry[1]));
            }
        }

        ksort($matchToOption);
        ksort($flatMatchOption);

        return array($matchToOption, $flatMatchOption);
    }

    private function composeCastStrAry($flatMatchOption, $parlays, $multi)
    {
        $castStrAry = array();
        $midAry = array_keys($flatMatchOption);
        sort($midAry);
        foreach ($parlays as $parlay) {
            $selectMatches = $this->combineList($midAry, $parlay);
            foreach ($selectMatches as $mIds) {
                $castStrAry = array_merge($castStrAry,
                    $this->cartesianProduct(array_intersect_key($flatMatchOption, array_flip($mIds))));
            }
        }

        $castWithMulti = array();
        $sortParlay = array();
        $sortOdd = array();
        foreach ($castStrAry as $castAry) {
            $odd = 1;
            foreach ($castAry as $cast) {
                $odd *= array_pop(explode('/', $cast));
            }
            $castWithMulti[] = array('cast' => $castAry, 'multi' => $multi, 'odd' => $odd);
            $sortParlay[] = count($castAry);
            $sortOdd[] = $odd;
        }
        array_multisort($sortParlay, SORT_ASC, SORT_NUMERIC,
            $sortOdd, SORT_DESC, SORT_NUMERIC,
            $castWithMulti);

        return $castWithMulti;
    }

    /*
     * [The Art of Combinations] has a relation
     * to almost every species of useful knowledge
     * that the mind of man can be employed upon.
     * ---- James Bernoulli
     */
    //explore a space of combinations
    private function combineList($arr, $n)
    {
        $len = count($arr);
        $result = array();
        for ($i = 0, $pow = pow(2, $len); $i < $pow; $i ++) {
            if ($this->bitCount($i) == $n) {
                $tmp = array();
                for ($j = 0; $j < $len; $j ++) {
                    if ($i & (1 << $j)) {
                        $tmp[] = $arr[$j];
                    }
                }
                $result[] = $tmp;
            }
        }

        return $result;
    }

    private function bitCount($i)
    {
        $count = 0;
        while ($i) {
            $count += 1;
            $i &= $i - 1;
        }

        return $count;
    }

    private function averageOptimize($castStrAry)
    {
        $multiSum = 0;
        $sum = 0;
        $allOdds = array();
        foreach ($castStrAry as $key => &$cast) {
            $multiSum += $cast['multi'];
            $sum += 1 / $cast['odd'];
            $cast['index'] = $key;
            $allOdds[] = $cast['odd'];
        }
        //not real harmonic
        $harmonic = $multiSum / $sum;
        array_multisort($allOdds, SORT_ASC, SORT_NUMERIC, $castStrAry);

        $newMultiAry = array();
        //todo when foreach ($castStrAry as $cast), here produce a bug, why
        foreach ($castStrAry as &$cast) {
            $newMultiAry[] = max(round($harmonic / ($cast['odd'] * 1)), 1);
        }

        $newMultiSum = array_sum($newMultiAry);
        $diff = $newMultiSum - $multiSum;

        if ($diff > 0) {
            while ($diff > 0) {
                $hasOp = FALSE;
                for ($i = 0, $j = count($newMultiAry); $i < $j; $i ++) {
                    if ($newMultiAry[$i] > 1) {
                        $newMultiAry[$i] -= 1;
                        $diff --;
                        if ($diff == 0) {
                            break;
                        }
                        $hasOp = TRUE;
                    }
                }
                if ( ! $hasOp) {
                    break;
                }
            }
        }
        else if ($diff < 0) {
            for ($i = count($newMultiAry); $i >= 0; $i --) {
                $newMultiAry[$i - 1] += 1;
                $diff ++;
                if ($diff == 0) {
                    break;
                }
            }
        }

        $allIndex = array();
        foreach ($castStrAry as $key => &$cast) {
            $cast['multi'] = $newMultiAry[$key];
            $allIndex[] = $cast['index'];
        }
        array_multisort($allIndex, SORT_ASC, SORT_NUMERIC, $castStrAry);

        return $castStrAry;
    }

    private function cartesianProduct($a)
    {
        $result = array(array());
        foreach ($a as $list) {
            $_tmp = array();
            foreach ($result as $result_item) {
                foreach ($list as $list_item) {
                    $_tmp[] = array_merge($result_item, array($list_item));
                }
            }
            $result = $_tmp;
        }

        return $result;
    }
}