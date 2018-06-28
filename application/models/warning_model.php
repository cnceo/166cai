<?php

class Warning_Model extends MY_Model {

    public function __construct()
    {
        parent::__construct();
    }
    
    
    /**
     * 查询需要报警内容
     * @param string $date  日期
     */
    public function getAlerts() 
    {
        $sql = "select w.ctype, w.phone, w.email, w.sendType, a.id, a.title, a.content from cp_alert_config w
        inner join cp_alert_log a FORCE index(created) on w.ctype=a.ctype AND w.`stop`=0
        where a.created > date_sub(now(), interval 1 HOUR) and a.status=0";
        return $this->db->query($sql)->getAll();
    }
    
    /**
     * 将状态更新为已处理
     * @param unknown_type $ids
     * @param unknown_type $status
     */
    public function updateAlert($ids, $status = 1)
    {
        $sql = "update cp_alert_log set status=? where id in ?";
        return $this->db->query($sql, array($status, $ids));
    }

    /**
     * 彩种信息
     */
    public $lotteryInfo = array(
        '23529' => array(
            'lname' => '大乐透',
            'tname' => 'dlt',
            'time'  => '90',
        ),
        '51'    => array(
            'lname' => '双色球',
            'tname' => 'ssq',
            'time'  => '90',
        ),
        '52'    => array(
            'lname' => '福彩3D',
            'tname' => 'fc3d',
            'time'  => '90',
        ),
        '33'    => array(
            'lname' => '排列三',
            'tname' => 'pl3',
            'time'  => '90',
        ),
        '35'    => array(
            'lname' => '排列五',
            'tname' => 'pl5',
            'time'  => '90',
        ),
        '10022' => array(
            'lname' => '七星彩',
            'tname' => 'qxc',
            'time'  => '90',
        ),
        '23528' => array(
            'lname' => '七乐彩',
            'tname' => 'qlc',
            'time'  => '90',
        ),
    );

    /**
     * 开奖详情到设定时间还未抓取到
     */
    public function checkAwarding()
    {
        $this->dcDB = $this->load->database('dc', true);
        // 慢频彩
        $lottery = $this->lotteryInfo;

        if(!empty($lottery))
        {
            $warnList = array();
            foreach ($lottery as $lid => $items) 
            {
                $tname = 'cp_' . $items['tname'] . '_paiqi';
                $time = date("Y-m-d H:i:s", strtotime("-3 day"));
                $sql = "SELECT issue FROM {$tname} WHERE award_time < DATE_SUB(NOW(), INTERVAL {$items['time']} MINUTE) AND rstatus = 0 and award_time>='{$time}'";
                $result = $this->dcDB->query($sql)->getRow();

                if(!empty($result))
                {
                    array_push($warnList, $items['lname']);
                }
            }

            if(!empty($warnList))
            {
                $warnList = implode(',', $warnList);
                // 报警
                $content = "彩种{$warnList}还未抓取到开奖信息";
                $warmSql = "INSERT INTO cp_alert_log
                (ctype,title,content,status,created) VALUES ('7','开奖详情未抓取报警','{$content}', '0', NOW())";
                $this->db->query($warmSql);
            }
        }
    }

    /**
     * 追号报警
     */
    public function checkChaseWarning($lid, $issue)
    {
        $sql = "SELECT m.chaseId AS chaseId
        FROM cp_chase_manage AS m 
        INNER JOIN cp_chase_orders AS o 
        ON m.chaseId = o.chaseId 
        WHERE m.lid = ? AND o.bet_flag = 1 AND o.modified < date_sub(now(), interval 30 MINUTE)
        and o.modified > date_sub(now(), interval 3 HOUR) 
        ORDER BY m.chaseId,o.issue ASC LIMIT 10";

        $orderInfo = $this->db->query($sql, array($lid))->getCol();

        if(!empty($orderInfo))
        {
            // 报警
            $this->load->library('BetCnName');
            $lname = BetCnName::getCnName($lid);
            $chaseId = implode(',', $orderInfo);
            $content = "彩种{$lname}追号订单{$chaseId}未正常提交订单";
            $warmSql = "INSERT INTO cp_alert_log
            (ctype,title,content,status,created) VALUES ('5','追号漏投报警', ?, '0', NOW())";
            $this->db->query($warmSql,array($content));
        }
    }
    
    /**
     * 高频彩种未及时派奖报警
     */
    public function checkSendPrize()
    {
        $cfgDB = $this->load->database('cfg', true);
        $lids = array(
        	KS => array('enName' => 'ks', 'cnName' => '快三', 'second' => 90),
            JLKS => array('enName' => 'jlks', 'cnName' => '吉林快三', 'second' => 90),
            JXKS => array('enName' => 'jxks', 'cnName' => '江西快三', 'second' => 150),
        	KLPK => array('enName' => 'klpk', 'cnName' => '快乐扑克', 'second' => 90),
        	JXSYXW => array('enName' => 'jxsyxw', 'cnName' => '新11选5', 'second' => 90),
        	HBSYXW => array('enName' => 'hbsyxw', 'cnName' => '惊喜11选5', 'second' => 90),
        	SYXW => array('enName' => 'syxw', 'cnName' => '山东11选5', 'second' => 90),
        	CQSSC => array('enName' => 'cqssc', 'cnName' => '老时时彩', 'second' => 90),
            GDSYXW => array('enName' => 'gdsyxw', 'cnName' => '乐11选5', 'second' => 180),
        );
        $datas = array();
        $lidName = array();
        $otherCondition = $this->db->query("select otherCondition from cp_alert_config where id = 10")->getCol();
        $otherCondition = json_decode($otherCondition[0], true);
        foreach ($otherCondition['lid'] as $lid)
        {
            $sql = "select issue from cp_{$lids[$lid]['enName']}_paiqi where award_time < date_sub(now(), interval {$lids[$lid]['second']} second) and rstatus < 80 
            and award_time > date_sub(now(), interval 2 day) and delect_flag = 0 limit 5";
            $issues = $cfgDB->query($sql)->getCol();
            if(!empty($issues)) {
            	$datas[$lid] = $issues;
            	array_push($lidName, $lids[$lid]['cnName']);
            }
                
        }
        if(!empty($datas))
        {
            $sms = '';
            foreach ($datas as $lid => $issues)
            {
                $sms .= "{$lids[$lid]['cnName']}(".implode(',', $issues).")未派奖报警;";
            }
            $warmSql = "INSERT INTO cp_alert_log (ctype,title,content,status,created)
                VALUES ('10','".implode(',', $lidName)."未派奖报警','{$sms}', '0', NOW())";
            $this->db->query($warmSql);
        }
    }
    
    /**
     * 大奖待审核报警
     */
    public function getOrderChecklist() 
    {
        $sql = "SELECT o.id, o.lid, o.issue, o.margin, u.uname
        FROM cp_orders as o
        LEFT JOIN cp_user as u ON o.uid = u.uid
        WHERE o.my_status = '2' AND o. STATUS = '2000' AND o.modified > DATE_SUB(NOW(), INTERVAL 1 HOUR) AND ((o.cstate & 32) = 0)
        ORDER BY o.created DESC";
        $this->load->library('BetCnName');
        $datas = $this->slave->query($sql)->getAll();
        if(!empty($datas))
        {
            $orders = array();
            foreach ($datas as $data)
            {
                $orders[] = $data['id'];
                $sms .= "{$data['uname']}，".BetCnName::getCnName($data['lid']).(!in_array($data['lid'], array(JCZQ, JCLQ)) ? "第{$data['issue']}期" : '')."，税后".number_format(ParseUnit($data['margin'], 1), 2)."元，请尽快处理;";
            }
            $warmSql = "INSERT INTO cp_alert_log(ctype,title,content,created) VALUES ('15', '大奖待审核报警', '{$sms}', NOW())";
            $res = $this->db->query($warmSql);
            if($res)
            {
                $this->db->query("update cp_orders set cstate = cstate | 32 where id in (".implode(',', $orders).")");
            }
        }
    }
    
    /**
     * 竞技彩15分钟未审核报警
     */
    public function jjcAduitWarn()
    {
        $sql = "select mid from cp_jczq_paiqi where modified > date_sub(now(), interval 1 HOUR) and 
        modified < date_sub(now(), interval 15 MINUTE) and status >= '2' and aduitflag != '1'";
        $result = $this->slaveDc->query($sql)->getCol();
        $bdata['s_data'] = array();
        $bdata['d_data'] = array();
        if($result)
        {
            foreach ($result as $mid)
            {
                array_push($bdata['s_data'], "(21, ?, ?, ?, now())");
                array_push($bdata['d_data'], $mid);
                array_push($bdata['d_data'], '竞彩足球比分15分钟未审核报警');
                array_push($bdata['d_data'], "竞彩足球场次{$mid}比分15分钟仍未审核，请尽快人工审核");
            }
        }
        
        $sql = "select mid from cp_jclq_paiqi where modified > date_sub(now(), interval 1 HOUR) and
        modified < date_sub(now(), interval 15 MINUTE) and status >= '2' and aduitflag != '1'";
        $result = $this->slaveDc->query($sql)->getCol();
        if($result)
        {
            foreach ($result as $mid)
            {
                array_push($bdata['s_data'], "(21, ?, ?, ?, now())");
                array_push($bdata['d_data'], $mid);
                array_push($bdata['d_data'], '竞彩篮球比分15分钟未审核报警');
                array_push($bdata['d_data'], "竞彩篮球场次{$mid}比分15分钟仍未审核，请尽快人工审核");
            }
        }
        
        if(!empty($bdata['s_data']))
        {
            $fields = array('ctype', 'ufiled', 'title', 'content', 'created');
            $sql = "insert ignore cp_alert_log(" . implode(', ', $fields) . ") values" . implode(', ', $bdata['s_data']);
            $this->db->query($sql, $bdata['d_data']);
        }
    }
    
    /**
     * 慢频彩20分钟未审核报警 定时处理 
    ① 体彩：21:00开始报警提示号码待审核；
    涉及彩种：大乐透、排三、排五、七星彩；
    ② 福彩：21:45开始报警提示号码待审核；
    涉及彩种：双色球、福彩3D、七乐彩； * 
     */
    public function numAduitWarn()
    {
        // 慢频彩
        $lottery = $this->lotteryInfo;
        $bdata['s_data'] = array();
        $bdata['d_data'] = array();
        date_default_timezone_set('PRC'); 
        $time = strtotime( date('Y-m-d'). " 20:58:00" );
        $time2 = strtotime( date('Y-m-d'). " 21:43:00" ) ;
        if(time() >=$time && time()< $time2)
        {
            unset($lottery['51']);
            unset($lottery['52']);
            unset($lottery['23528']);
        }else if(time() >= $time2){
            $lottery = $lottery;
        }else{
            $lottery = array();
        }
        if(!count($lottery)) return;
        foreach ($lottery as $lid => $items)
        {
            $tname = 'cp_' . $items['tname'] . '_paiqi';
            $sql = "select issue from {$tname} where modified > date_sub(now(), interval 1 HOUR) and status >= '2' and aduitflag = '0'";
            $result = $this->slaveDc->query($sql)->getCol();
            if(!empty($result))
            {
                array_push($bdata['s_data'], "(21,?, ?, now())");
                array_push($bdata['d_data'], "{$items['lname']}开奖号码待审核报警");
                $issues = implode(',', $result);
                array_push($bdata['d_data'], "{$items['lname']}第{$issues}期号码一致待审核，请尽快人工审核");
            }
        }
        if(!empty($bdata['s_data']))
        {
            $fields = array('ctype','title', 'content', 'created');
            $sql = "insert ignore cp_alert_log(" . implode(', ', $fields) . ") values" . implode(', ', $bdata['s_data']);
            $this->db->query($sql, $bdata['d_data']);
        }
    }
    /**
     * 提现10分钟未处理报警
     */
    public function checkWithdraw()
    {
        $sql = "select trade_no from cp_withdraw where status=0 and remark='' AND created > date_sub(now(), interval 2 HOUR) and 
        created < date_sub(now(), interval 5 MINUTE) limit 1";
        $result = $this->slave->query($sql)->getRow();
        if($result)
        {
            $warmSql = "INSERT ignore cp_alert_log (ctype,ufiled,title,content,status,created)
            VALUES ('18','{$result['trade_no']}','提现10分钟未处理报警','提款订单号：{$result['trade_no']}', '0', NOW())";
            $this->db->query($warmSql);
        }
    }

    /**
     * [jjzqNotGetBonus 竞彩足球篮球未拉取票商奖金报警]
     * @author LiKangJian 2017-07-19
     * @return [type] [description]
     */
    public function jjzqNotGetBonus()
    {
        $sql = "select m.sub_order_id,n.lid,n.ticket_seller from 
                (select sub_order_id from bn_cpiao_cfg.cp_orders_relation where 1 and modified > date(DATE_SUB(now(),INTERVAL 2 day)) and modified < date(DATE_SUB(now(),INTERVAL 1 day)) and status = 650 and lid in(42,43) group by sub_order_id) m
                join 
                bn_cpiao_cfg.cp_orders_split n on m.sub_order_id = n.sub_order_id 
                where n.status in(1000, 2000) and cpstate < 4";
        $result = $this->slaveDc->query($sql)->getAll();
        $bdata['s_data'] = array();
        $bdata['d_data'] = array();
        if($result)
        {
            foreach ($result as $v)
            {
                array_push($bdata['s_data'], "(23, ?, ?, ?, now())");
                array_push($bdata['d_data'], $v['sub_order_id']);
                $lid = $v['lid'] == 42 ? '竞彩足球' : '竞彩篮球';
                array_push($bdata['d_data'], "{$lid}24小时未拉取到对比奖金");
                array_push($bdata['d_data'], "订单号：{$v['sub_order_id']} {$lid}24小时未拉取到{$v['ticket_seller']}对比奖金，请及时关注");
            }
        }
        if(!empty($bdata['s_data']))
        {
            $fields = array('ctype', 'ufiled', 'title', 'content', 'created');
            $sql = "insert ignore cp_alert_log(" . implode(', ', $fields) . ") values" . implode(', ', $bdata['s_data']);
            $this->db->query($sql, $bdata['d_data']);
        }

    }
    
    // 查询该彩种未拉取奖金的订单 - 六小时内
    public function warningTicketBonus()
    {
    	$splitLid = $this->config->item('split_lid');
    	array_push($splitLid, SSQ, SFC);
    	$order_status = $this->orderConfig('orders');
    	$this->load->library('BetCnName');
    	$bdata['s_data'] = array();
    	$bdata['d_data'] = array();
    	foreach ($splitLid as $lid) {
    		$cpstate = in_array($lid, array(SFC, RJ)) ? 1 : 3;
    		$tables = $this->getSplitTable($lid);
    		$sql = "select sub_order_id, lid, ticket_seller
    			from {$tables['split_table']} FORCE INDEX(modified)
    			where modified < date_sub(now(), interval 6 hour) and modified > date_sub(now(), INTERVAL 2 day) and endTime < now()
    				and status in('{$order_status['win']}', '{$order_status['notwin']}') and cpstate < {$cpstate}";
    		$data = array();
    		if ($lid == SFC) {
    			$sql .= " and lid in ?";
    			array_push($data, array(SFC, RJ));
    		}elseif ($lid !== SSQ) {
    			$sql .= " and lid = ?";
    			array_push($data, $lid);
    		}else {
    			$sql .= " and lid not in ?";
    			array_push($data, array(SFC, RJ));
    		}
    		$tmpres = $this->cfgDB->query($sql, $data)->getAll();
    		foreach ($tmpres as $val) {
    			array_push($bdata['s_data'], "(23, ?, ?, ?, now())");
    			array_push($bdata['d_data'], $val['sub_order_id']);
    			array_push($bdata['d_data'], BetCnName::getCnName($val['lid'])."未拉取票商奖金报警");
    			array_push($bdata['d_data'], "订单号：{$val['sub_order_id']}，".BetCnName::getCnName($val['lid'])."，{$val['ticket_seller']}，算奖后6小时未拉到奖金");
    		}
    	}
    	if(!empty($bdata['s_data'])) {
    		$tmpArr = array('s_data' => array(), 'd_data' => array());
    		while (!empty($bdata['s_data'])) {
    			$tmpArr['s_data'] = array_slice($bdata['s_data'], 0, 100);
    			$tmpArr['d_data'] = array_slice($bdata['d_data'], 0, 300);
    			$bdata['s_data'] = array_slice($bdata['s_data'], 100);
    			$bdata['d_data'] = array_slice($bdata['d_data'], 300);
    			if (count($tmpArr['s_data']) <= 100) {
    				$fields = array('ctype', 'ufiled', 'title', 'content', 'created');
    				$sql = "insert ignore cp_alert_log(" . implode(', ', $fields) . ") values" . implode(', ', $tmpArr['s_data']);
    				$this->db->query($sql, $tmpArr['d_data']);
    				$tmpArr = array('s_data' => array(), 'd_data' => array());
    			}
    		}
            
        }
    }
    
    public function pushCheck() {
    	$res = $this->slave->query("SELECT id, ptype FROM cp_push_log 
			WHERE `status` = 0 AND created < DATE_SUB(NOW(),INTERVAL 30 MINUTE) AND created > DATE_SUB(NOW(),INTERVAL 2 HOUR) group by ptype")->getAll();
    	$bdata = array('s_data' => array(), 'd_data' => array());
    	$ptypeArr = array(
    			1  => '中奖推送',
    			2  => '中奖加奖推送',
    			3  => '提现申请推送',
    			4  => '提现失败推送',
    			5  => '反馈推送',
    			6  => '追号完成推送',
    			7  => '出票成功',
    			8  => '部分出票失败推送',
    			9  => '出票失败推送',
    			10 => '投注失败推送',
    			11 => '合买关注推送',
    			12 => '红包推送',
    			13 => '竞彩比分直播',
    			14 => '购彩推送',
    			15 => '数字彩开奖号码',
    			16 => '追号不中包赔彩金到账'
    	);
    	if (!empty($res)) {
    		foreach ($res as $val) {
    			array_push($bdata['s_data'], "(26, ?, ?, ?, now())");
    			array_push($bdata['d_data'], $val['id']);
    			array_push($bdata['d_data'], "{$ptypeArr[$val['ptype']]}30分钟未推送成功");
    			array_push($bdata['d_data'], "{$ptypeArr[$val['ptype']]}30分钟未推送成功，请人工确认推送脚本是否存在异常！");
    		}
    		$fields = array('ctype', 'ufiled', 'title', 'content', 'created');
    		$sql = "insert ignore cp_alert_log(" . implode(', ', $fields) . ") values" . implode(', ', $bdata['s_data']);
    		$this->db->query($sql, $bdata['d_data']);
    	}
    }
    
    public function hongbaoAlert($data)
    {
        $fields = array('ctype', 'title', 'content', 'created');
        $sql = "insert ignore cp_alert_log(" . implode(', ', $fields) . ") values (?,?,?,now())";
        $this->db->query($sql,$data);
    }
    
    public function jjcRelationCheck() {
        $cfgDB = $this->load->database('cfg', true);
        $sellerArr = array('qihui' => '齐汇', 'caidou' => '彩豆', 'huayang' => '华阳', 'shancai' => '善彩', 'hengju' => '恒钜');
        $lidArr = array(42 => '竞彩足球', 43 => '竞彩篮球');
        $res = $cfgDB->query("SELECT DISTINCT s.ticket_seller, s.sub_order_id, s.lid
                FROM cp_orders_split as s
                INNER JOIN cp_orders_relation as r on s.sub_order_id = r.sub_order_id
                WHERE s.lid in (42, 43) AND s.`status` = 500 AND r.`status` = 0 AND s.ticket_time > DATE_SUB(NOW(), INTERVAL 1 DAY)")->getAll();
        if (!empty($res)) {
            $bdata = array('s_data' => array(), 'd_data' => array());
            foreach ($res as $val) {
                array_push($bdata['s_data'], "(28, ?, ?, ?, now())");
                array_push($bdata['d_data'], $val['sub_order_id']);
                array_push($bdata['d_data'], "{$lidArr[$val['lid']]}出票格式有误");
                array_push($bdata['d_data'], "{$sellerArr[$val['ticket_seller']]}{$lidArr[$val['lid']]}，{$val['sub_order_id']}，出票格式有误，尽快联系票商重推出票信息；");
            }
            $fields = array('ctype', 'ufiled', 'title', 'content', 'created');
            $sql = "insert ignore cp_alert_log(" . implode(', ', $fields) . ") values" . implode(', ', $bdata['s_data']);
            $this->db->query($sql, $bdata['d_data']);
        }
    }
}
