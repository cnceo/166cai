<?php

class Rj extends MY_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->driver('cache', array('adapter' => 'redis'));
        $this->load->model('api_zhisheng_model', 'dataSource');
    }

    public function index()
    {
        $data = $this->getData();
        $view = $data['view'];
        unset($data['view']);
        $this->display('rj/'. $view,$data, 'v1.1');
    }
    /**
     * [dssc 单式上传]
     * @author LiKangJian 2017-08-12
     * @return [type] [description]
     */
    public function dssc()
    {
        $this->display('rj/dssc',$this->getData(1), 'v1.1'); 
    }
    private function getData($type = 0)
    {
        $lotteryId = Lottery_Model::RJ;
        $REDIS = $this->config->item('REDIS');
        $issues = json_decode($this->cache->get($REDIS['SFC_ISSUE_NEW']), TRUE);
        $currIssueIds = array();
        foreach ($issues as $issue)
        {
            if ($issue['sale_time'] <= time() * 1000 && $issue['seFsendtime'] >= time() * 1000)
            {
                array_push($currIssueIds, $issue['seExpect']);
            }
        }
        if (empty($currIssueIds))
        {
            foreach ($issues as $issue)
            {
                if ($issue['seFsendtime'] >= time() * 1000)
                {
                    array_push($currIssueIds, $issue['seExpect']);
                    break;
                }
            }
        }
        
        if (!empty($currIssueIds)) {
            $minCurrentId = min($currIssueIds);
            $maxCurrentId = max($currIssueIds);
        }

        $issueId = $this->input->get('issue', TRUE);
        if (empty($issueId) && $minCurrentId)
        {
            $issueId = $minCurrentId;
        }
        
        if (empty($issueId)) {
            $endissue = end($issues);
            $this->redirect('/rj?issue='.$endissue['seExpect']);
        }
        
        foreach ($issues as $key => $issue)
        {
            if ($issue['seExpect'] == $issueId)
            {
                $currIssue = $issue;
            }
            if ($minCurrentId && $issue['seExpect'] == $minCurrentId)
            {
                $prevIssue = $issues[$key - 1];
            }else if (!$minCurrentId) {
                $prevIssue = end($issues);
            }
        }
        
        $awardIssue = $issues[0];
        $nextIssueIds = array();
        $targetIssue = $currIssue;
        foreach ($issues as $issue)
        {
            if ($issue['seExpect'] > $maxCurrentId)
            {
                $nextIssueIds[] = $issue['seExpect'];
            }
            if ($issueId >= $maxCurrentId && $issue['seExpect'] == $issueId)
            {
                $targetIssue = $issue;
            }
        }

        $awardIssue['awardInfo'] = json_decode($awardIssue['awardDetail'], TRUE);

        $apiUrl = $this->config->item('api_bf');
        $detailUrl = $this->config->item('api_info');
        $oddsUrl = $this->config->item('api_odds');
        if ( ! empty($apiUrl))
        {
            $content = $this->dataSource->readEuropeOdds(RJ, $issueId, 0);
            $contentAry = json_decode($content, TRUE);
        }
        $allMatches = json_decode($this->cache->get($REDIS['SFC_MATCH_NEW']), TRUE);
        $matches = $allMatches[$issueId];
        if (!empty($matches)) {
            foreach ($matches as &$match)
            {
                if ( ! isset($contentAry[$match['orderId']]))
                {
                    continue;
                }
                $match['hTid'] = $contentAry[$match['orderId']]['htid'];
                $match['hDetail'] = $detailUrl . 'teams/' . $match['hTid'];
                $match['aTid'] = $contentAry[$match['orderId']]['atid'];
                $match['aDetail'] = $detailUrl . 'teams/' . $match['aTid'];
                $match['hRank'] = preg_replace('/[^\d]/', '', $contentAry[$match['orderId']]['hpm']);
                $match['aRank'] = preg_replace('/[^\d]/', '', $contentAry[$match['orderId']]['apm']);
                $match['oddsUrl'] = $oddsUrl;
                $match['queryMId'] = $contentAry[$match['orderId']]['mid'];
                $match['oh'] = empty($contentAry[$match['orderId']]['odds']['oh'])
                ? '0.00'
                        : number_format($contentAry[$match['orderId']]['odds']['oh'], 2);
                $match['od'] = empty($contentAry[$match['orderId']]['odds']['od'])
                ? '0.00'
                        : number_format($contentAry[$match['orderId']]['odds']['od'], 2);
                $match['oa'] = empty($contentAry[$match['orderId']]['odds']['oa'])
                ? '0.00'
                        : number_format($contentAry[$match['orderId']]['odds']['oa'], 2);
            }
        }
        
        $view = ($issueId < $minCurrentId || !$minCurrentId) ? 'sg' : 'index';
        $this->load->model('lottery_config_model', 'lotteryConfig');
        $dsjzsj = $this->lotteryConfig->getEndTime($lotteryId);
        $dsjzsj = $dsjzsj[0];
        $this->load->config('wenan');
        $wenan = $this->config->item('wenan');
        $data = array(
            'type'         => 'cast',
            'cz'           => 'rj',
            'dsjzsj'       => $dsjzsj,
            'cnName'       => $this->Lottery->getCnName($lotteryId),
            'enName'       => $this->Lottery->getEnName($lotteryId),
            'lotteryId'    => $lotteryId,
            'currIssue'    => $currIssue,
            'targetIssue'  => $targetIssue,
            'awardIssue'   => $awardIssue,
            'issues'       => $issues,
            'prevIssue'    => $prevIssue,
            'nextIssueIds' => $nextIssueIds,
            'issueId'      => $issueId,
            'minCurrentId' => $minCurrentId,
            'multi'        => 1,
            'wenan'        => $wenan,
        );
        if($type==0){$data['matches'] =$matches;$data['view'] = $view;}
        return $data;
    }
}
