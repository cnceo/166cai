<?php

class Issue_Model extends MY_Model {

    private $lidMap = array(
        '51' => array('table' => 'cp_ssq_paiqi', 'alertCount' => '60', 'name' => '双色球'),
        '52' => array('table' => 'cp_fc3d_paiqi', 'alertCount' => '60', 'name' => '福彩3D'),
        '33' => array('table' => 'cp_pl3_paiqi', 'alertCount' => '60', 'name' => '排列三'),
        '35' => array('table' => 'cp_pl5_paiqi', 'alertCount' => '60', 'name' => '排列五'),
        '10022' => array('table' => 'cp_qxc_paiqi', 'alertCount' => '60', 'name' => '七星彩'),
        '23528' => array('table' => 'cp_qlc_paiqi', 'alertCount' => '60', 'name' => '七乐彩'),
        '23529' => array('table' => 'cp_dlt_paiqi', 'alertCount' => '60', 'name' => '大乐透'),
        '21406' => array('table' => 'cp_syxw_paiqi', 'alertCount' => '300', 'name' => '十一选五'),
        '21407' => array('table' => 'cp_jxsyxw_paiqi', 'alertCount' => '300', 'name' => '江西十一选五'),
        '11' => array('table' => 'cp_tczq_paiqi', 'alertCount' => '1', 'name' => '老足彩'),
        '19' => array('table' => 'cp_tczq_paiqi', 'alertCount' => '1', 'name' => '老足彩'),
        '53' => array('table' => 'cp_ks_paiqi', 'alertCount' => '300', 'name' => '上海快三'),
        '56' => array('table' => 'cp_jlks_paiqi', 'alertCount' => '300', 'name' => '吉林快三'),
        '21408' => array('table' => 'cp_hbsyxw_paiqi', 'alertCount' => '300', 'name' => '湖北十一选五'),
        '54' => array('table' => 'cp_klpk_paiqi', 'alertCount' => '300', 'name' => '快乐扑克'),
        '55' => array('table' => 'cp_cqssc_paiqi', 'alertCount' => '300', 'name' => '老时时彩'),
        '57' => array('table' => 'cp_jxks_paiqi', 'alertCount' => '300', 'name' => '江西快三'),
        '21421' => array('table' => 'cp_gdsyxw_paiqi', 'alertCount' => '300', 'name' => '广东十一选五'),
    );

    public function __construct() {
        parent::__construct();
        $this->load->model('lottery_model', 'Lotery');
        $this->load->model('state_model', 'State');
    }

    public function getNumber($options = array()) {
        $issues = array();

        $issueResponse = $this->tools->get($this->busiApi . 'ticket/data/il', $options);
        if ($issueResponse['code'] == 0) {
            $issues = $issueResponse['data'];
        }

        return $issues;
    }

    public function getJC($lotteryId, $state, $issue) {
        $awards = array();
        $awardResponse = $this->tools->get($this->busiApi . 'ticket/data/jil', array(
            'lid' => $lotteryId,
            'state' => $state,
            'issue' => $issue,
            'pre_issue' => 1,
        ));
        if ($awardResponse['code'] == 0) {
            $awards = $awardResponse['data'];
        }

        return $awards;
    }

    public function compareIssue($lid, $issues)
    {
        if (empty($issues)) {
            return ;
        }
        $this->dcDB = $this->load->database('dc', true);
        $table = $this->lidMap[$lid]['table'];
        $sql = "select issue,sale_time,end_time from {$table} where issue in ? and delect_flag=0";
        $issueIds = array_keys($issues);
        $paiqis = $this->dcDB->query($sql, array($issueIds))->getAll();
        $originIssues = array();
        foreach ($paiqis as $paiqi) {
            $originIssues[$paiqi['issue']]['start'] = $paiqi['sale_time'];
            $originIssues[$paiqi['issue']]['end'] = $paiqi['end_time'];
        }
        $issues = array_intersect_key($issues, $originIssues);
        $datas = array();
        if ($issues != $originIssues) {
            foreach ($issues as $k=>$issue) {
                $issue = $this->dealIssueTime($k, $issue);
                $datas['d_data'][] = $k;
                $datas['d_data'][] = $issue['start'];
                $datas['d_data'][] = $issue['end'];
                $datas['d_data'][] = $issue['award'];
                $datas['d_data'][] = 0;
                $datas['d_data'][] = 0;
                $datas['s_data'][] = "(?, ?, ?, ?, ?, ?)";
            }
            $fields = array('issue', 'sale_time', 'end_time', 'award_time', 'synflag', 'd_synflag');
            $upd = array('sale_time', 'end_time', 'award_time', 'synflag',  'd_synflag');
            $sql = "insert " . $table . "(" . implode(', ', $fields) . ") values" .
                    implode(', ', $datas['s_data']) . $this->onduplicate($fields, $upd);
            $this->dc->query($sql, $datas['d_data']);
            $this->cfgDB = $this->load->database('cfg', TRUE);
            $this->cfgDB->query("update cp_task_manage set stop= ? where task_type= ? and lid= ?", array(0, 6, $lid));
            $starTime = date("Y-m-d H:i:s", strtotime("-20 minute"));
            $key = current(array_keys($issues));
            if ($issues[$key] != $originIssues[$key])
            {
                $issue = $this->dealIssueTime($key, $issues[$key]);
                //$sql = "update cp_orders set endTime= ? where issue= ? and lid= ? and modified>= ?";
                //$this->db->query($sql, array($issue['end'], $key, $lid, $starTime));
                //$sql = "update cp_chase_orders set endTime= ?,award_time= ? where issue= ? and lid= ?";
                //$this->db->query($sql, array($issue['end'], $issue['award'], $key, $lid));
                //$sql = "update cp_orders_ori set endTime= ? where issue= ? and lid= ? and modified>= ?";
                //$this->cfgDB->query($sql, array($issue['end'], $key, $lid, $starTime));
                $splitable = $this->getSplitTable($lid);
                $sql = "update {$splitable['split_table']} set saleTime=?,endTime= ? where issue= ? and lid= ? and modified>= ?";
                $this->cfgDB->query($sql, array($issue['start'], $issue['end'], $key, $lid, $starTime));
            }
        }
    }
    
    private function dealIssueTime($issue,$time)
    {
        $id = substr($issue, -3);
        if ($id == '001') {
            $time['start'] = date('Y-m-d H:i:s', strtotime($time['end']) - 9 * 60);
            $time['award'] = date('Y-m-d H:i:s', strtotime($time['end']));
        } else {
            $time['award'] = date('Y-m-d H:i:s', strtotime($time['end']));
        }
        return $time;
    }

    public function insertIssue($fields, $bdata, $type)
    {

        if(!empty($bdata['s_data']))
        {
            $upd = array('sale_time', 'end_time', 'award_time', 'synflag', 'status', 'd_synflag');
            $sql = "insert cp_".$type."_paiqi(" . implode(', ', $fields) . ") values" . 
            implode(', ', $bdata['s_data']) . $this->onduplicate($fields, $upd);
            $this->dc->query($sql, $bdata['d_data']);
            $this->cfgDB = $this->load->database('cfg', TRUE);
            $this->cfgDB->query("update cp_task_manage set stop= ? where task_type= ? and lid= ?", array(0, 6, 56));
        }
    }
}
