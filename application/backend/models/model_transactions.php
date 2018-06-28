<?php

/**
 * Copyright (c) 2014,上海二三四五网络科技股份有限公司
 * 摘    要：交易明细管理模型
 * 作    者：wangl@2345.com
 * 修改日期：2014.11.06
 */
class Model_transactions extends MY_Model
{
    public function __construct()
    {
        $this->get_db();
    }

    /**
     * 参    数：$searchData 搜索条件
     *                 $page 页码
     *                 $pageCount 单页条数
     * 作    者：wangl
     * 功    能：交易明细列表
     * 修改日期：2014.11.11
     */
    function list_transactions($searchData, $page, $pageCount)
    {
        //条件块
        $where = " where 1";
        $where .= $this->condition("{$this->cp_user}.uname", $searchData['name']);
        //用户名查询时进行联表
        $join = "";
        $joinField = "";
        if ($this->emp($searchData['name']))
        {
            $join = " INNER  JOIN {$this->cp_user} on {$this->cp_w_l}.uid = {$this->cp_user}.uid ";
            $joinField = ", {$this->cp_user}.uname ";
        }
        $where .= $this->condition(" {$this->cp_w_l}.created", array(
            $searchData['start_time'],
            $searchData['end_time']
        ), "time");
        $where .= $this->condition("{$this->cp_w_l}.trade_no", $searchData['trade_no']);
        $where .= $this->condition(" {$this->cp_w_l}.money", array(
            $searchData['start_money'],
            $searchData['end_money']
        ), "during", "m");
        $where .= $this->condition("{$this->cp_w_l}.ctype", $searchData['jylx']);
        $where .= $this->condition(" {$this->cp_w_l}.mark", array(
            $searchData['is_shouru'],
            $searchData['is_zhichu']
        ), "choose", array(
            0,
            1
        ));
        $where .= $this->condition("{$this->cp_w_l}.uid", $searchData['uid']);

        $startSuffix = $this->tools->getTableSuffixByDate($searchData['start_time']);
        $endSuffix = $this->tools->getTableSuffixByDate($searchData['end_time']);
        if ($startSuffix)
        {
            $startSuffix = '_' . $startSuffix;
        }
        if ($endSuffix)
        {
            $endSuffix = '_' . $endSuffix;
        }
        if ($startSuffix == $endSuffix)
        {
            $table = $this->cp_w_l . $startSuffix;
            //统计条数
            $countSql = "SELECT COUNT(*) as count FROM {$this->cp_w_l} {$join} {$where}";
            $countSql = str_replace($this->cp_w_l, $table, $countSql);
            $count = $this->BcdDb->query($countSql)->row();
            //获取数据
            $select = "SELECT {$this->cp_w_l}.*{$joinField} FROM {$this->cp_w_l} {$join} {$where} ORDER BY {$this->cp_w_l}.created DESC, {$this->cp_w_l}.trade_no DESC LIMIT " . ($page - 1) * $pageCount . "," . $pageCount;
            $select = str_replace($this->cp_w_l, $table, $select);
            $result = $this->BcdDb->query($select)->row_array();
            $result = $this->parse_result($result);

            $select1 = "SELECT sum({$this->cp_w_l}.money) as mon,count({$this->cp_w_l}.id) as ct FROM {$this->cp_w_l} {$join} ";
            $sum = $sum1 = array();
            //收入统计
            $sumSelect = $select1 . $where . " and {$this->cp_w_l}.mark = '1'";
            $sumSelect = str_replace($this->cp_w_l, $table, $sumSelect);
            $sum = $this->BcdDb->query($sumSelect)->row_array();
            //支出统计
            $sum1Select = $select1 . $where . " and {$this->cp_w_l}.mark = '0'";
            $sum1Select = str_replace($this->cp_w_l, $table, $sum1Select);
            $sum1 = $this->BcdDb->query($sum1Select)->row_array();
        }
        else
        {
            $startTable = $this->cp_w_l . $startSuffix;
            $endTable = $this->cp_w_l . $endSuffix;
            $startJoin = str_replace($this->cp_w_l, $startTable, $join);
            $endJoin = str_replace($this->cp_w_l, $endTable, $join);
            $startWhere = str_replace($this->cp_w_l, $startTable, $where);
            $endWhere = str_replace($this->cp_w_l, $endTable, $where);
            //统计条数
            $countSql = "SELECT SUM(count) count FROM (
        		SELECT COUNT(*) as count FROM {$startTable} {$startJoin} {$startWhere} UNION
        		SELECT COUNT(*) as count FROM {$endTable} {$endJoin} {$endWhere}
        	) tmp";
            $count = $this->BcdDb->query($countSql)->row();
            //获取数据
            $select = "SELECT * FROM (
        		SELECT {$startTable}.*{$joinField} FROM {$startTable} {$startJoin} {$startWhere} UNION
        		SELECT {$endTable}.*{$joinField} FROM {$endTable} {$endJoin} {$endWhere}
        	) tmp WHERE 1 ORDER BY tmp.created DESC, tmp.trade_no DESC LIMIT " . ($page - 1) * $pageCount . "," . $pageCount;
            $result = $this->BcdDb->query($select)->row_array();
            $result = $this->parse_result($result);

            $sum = $sum1 = array();
            //收入统计
            $sumSelect = "SELECT SUM(mon) mon, SUM(ct) ct FROM(
        		SELECT sum({$startTable}.money) as mon,count({$startTable}.id) as ct FROM {$startTable} {$startJoin} {$startWhere} and {$startTable}.mark = '1' UNION
        		SELECT sum({$endTable}.money) as mon,count({$endTable}.id) as ct FROM {$endTable} {$endJoin} {$endWhere} and {$endTable}.mark = '1' 
        	) tmp";
            $sum = $this->BcdDb->query($sumSelect)->row_array();
            //支出统计
            $sum1Select = "SELECT SUM(mon) mon, SUM(ct) ct FROM(
        		SELECT sum({$startTable}.money) as mon,count({$startTable}.id) as ct FROM {$startTable} {$startJoin} {$startWhere} and {$startTable}.mark = '0' UNION 
        		SELECT sum({$endTable}.money) as mon,count({$endTable}.id) as ct FROM {$endTable} {$endJoin} {$endWhere} and {$endTable}.mark = '0' 
        	) tmp";
            $sum1 = $this->BcdDb->query($sum1Select)->row_array();
        }

        return array(
            $result,
            $count->count,
            array(
                $sum1[0],
                $sum[0]
            )
        );
    }

    /**
     * 参    数：$searchData 搜索条件
     *                 $page 页码
     *                 $pageCount 单页条数
     * 作    者：wangl
     * 功    能：充值提款列表
     * 修改日期：2014.11.11
     */
    public function list_recharge($searchData, $page, $pageCount)
    {
        //条件块
        $where = " where {$this->cp_w_l}.ctype = '0' ";
        $where .= $this->condition("{$this->cp_user}.channel", $searchData['registerChannel']);
        if ($this->emp($searchData['name']))
        {
            $where .= "and ({$this->cp_user}.uname = '{$searchData[name]}' or {$this->cp_w_l}.trade_no = '{$searchData[name]}')";
        }
        $where .= $this->condition(" {$this->cp_w_l}.created", array(
            $searchData['start_time'],
            $searchData['end_time']
        ), "time");

        $where .= $this->condition(" {$this->cp_w_l}.recharge_over_time", array(
            $searchData['start_r_time'],
            $searchData['end_r_time']
        ), "time");
        $where .= $this->condition(" {$this->cp_w_l}.money", array(
            $searchData['start_money'],
            $searchData['end_money']
        ), "during", "m");
        if ($this->emp($searchData['rtype']) && $this->emp($searchData['rtype1']))
        {
            $where .= $this->condition("{$this->cp_w_l}.additions", $searchData['rtype'] . "@" . $searchData['rtype1']);
        }
        elseif ($this->emp($searchData['rtype'])) 
        {
            $types = array('xzZfbWap'=>'xzpay', 'hjZfbPay'=>'hjpay', 'wftZfbWap'=>'wftpay');
            if (array_key_exists($searchData['rtype'], $types)) {
                $where .= " and ({$this->cp_w_l}.additions = '{$searchData['rtype']}' or {$this->cp_w_l}.additions = '{$types[$searchData['rtype']]}')";
            } else {
                $where .= $this->condition("{$this->cp_w_l}.additions", $searchData['rtype']);
            }
        }
        elseif ($this->nemp($searchData['rtype1']))
        {
            $where .= $this->condition("{$this->cp_w_l}.additions", $searchData['rtype'], "likeRight");
        }
        $where .= $this->condition("{$this->cp_w_l}.mark", $searchData['mark']);

        if ($this->emp($searchData['ctype']))
        {
            $where .= " and {$this->cp_w_l}.ctype = '{$searchData['ctype']}'";
        }
        if ($this->emp($searchData['platform']) && $searchData['platform'] != - 1)
        {
            $where .= " and {$this->cp_w_l}.platform = " . $searchData['platform'];
        }
        if ($searchData['reg_type'] !== FALSE && $searchData['reg_type'] > 0)
        {
            if($searchData['reg_type'] == '1')
            {
                $where .= " and {$this->cp_user}.reg_type in ('0', '2')";
            }
            else
            {
                $where .= " and {$this->cp_user}.reg_type = ".$searchData['reg_type'];
            } 
        }

        //用户名查询时进行联表
        $join = "";
        $joinField = "";
//         if ($this->emp($searchData['name']))
//         {
            $join = " INNER  JOIN {$this->cp_user} on {$this->cp_w_l}.uid = {$this->cp_user}.uid ";
            $joinField = ", {$this->cp_user}.uname, {$this->cp_user}.channel as userChannel ";
//         }

        $startSuffix = $this->tools->getTableSuffixByDate($searchData['start_time']);
        $endSuffix = $this->tools->getTableSuffixByDate($searchData['end_time']);
        if ($startSuffix)
        {
            $startSuffix = '_' . $startSuffix;
        }
        if ($endSuffix)
        {
            $endSuffix = '_' . $endSuffix;
        }
        if ($startSuffix == $endSuffix)
        {
            $table = $this->cp_w_l . $startSuffix;
            $countSql = "SELECT COUNT(*) as count FROM {$this->cp_w_l} {$join} {$where}";
            $countSql = str_replace($this->cp_w_l, $table, $countSql);
            //$count = $this->BcdDb->query($countSql)->row();
            //获取数据
            $select = "SELECT {$this->cp_w_l}.*{$joinField}
        	FROM {$this->cp_w_l}
        	{$join}
        	{$where}
        	ORDER BY {$this->cp_w_l}.created DESC
        	LIMIT " . ($page - 1) * $pageCount . "," . $pageCount;
            $select = str_replace($this->cp_w_l, $table, $select);
            $result = $this->BcdDb->query($select)->row_array();
            $result = $this->parse_result($result);

            //充值总额
            $select2 = "SELECT sum({$this->cp_w_l}.money) as sum,count(distinct({$this->cp_w_l}.uid)) as count
        	FROM {$this->cp_w_l}  {$join} {$where}";
            $select2 = str_replace($this->cp_w_l, $table, $select2);
            //$result2 = $this->BcdDb->query($select2)->row();
            //待付款订单
            $select3 = "SELECT count({$this->cp_w_l}.id) as count, sum({$this->cp_w_l}.money) as sum FROM {$this->cp_w_l}  {$join} {$where} and {$this->cp_w_l}.mark='2'";
            $select3 = str_replace($this->cp_w_l, $table, $select3);
            //$result3 = $this->BcdDb->query($select3)->row();
        }
        else
        {
            $startTable = $this->cp_w_l . $startSuffix;
            $endTable = $this->cp_w_l . $endSuffix;
            $startJoin = str_replace($this->cp_w_l, $startTable, $join);
            $endJoin = str_replace($this->cp_w_l, $endTable, $join);
            $startWhere = str_replace($this->cp_w_l, $startTable, $where);
            $endWhere = str_replace($this->cp_w_l, $endTable, $where);
            $countSql = "SELECT SUM(count) count FROM (
        		SELECT COUNT(*) as count FROM {$startTable} {$startJoin} {$startWhere} UNION 
        		SELECT COUNT(*) as count FROM {$endTable} {$endJoin} {$endWhere}
        	) tmp";
            //$count = $this->BcdDb->query($countSql)->row();
            //获取数据
            $select = "SELECT * FROM (
        		SELECT {$startTable}.*{$joinField} FROM {$startTable} {$startJoin} {$startWhere} UNION
        		SELECT {$endTable}.*{$joinField} FROM {$endTable} {$endJoin} {$endWhere}
        	) tmp WHERE 1 ORDER BY tmp.created DESC LIMIT " . ($page - 1) * $pageCount . "," . $pageCount;
            $result = $this->BcdDb->query($select)->row_array();
            $result = $this->parse_result($result);

            //充值总额
            $select2 = "SELECT SUM(sum) sum, SUM(count) count FROM (
        		SELECT sum({$startTable}.money) as sum,count(distinct({$startTable}.uid)) as count FROM {$startTable}  {$startJoin} {$startWhere} UNION 
        		SELECT sum({$endTable}.money) as sum,count(distinct({$endTable}.uid)) as count FROM {$endTable}  {$endJoin} {$endWhere}
        	) tmp";
            //$result2 = $this->BcdDb->query($select2)->row();
            //待付款订单
            $select3 = "SELECT SUM(count) count, SUM(sum) sum FROM(
        		SELECT count({$startTable}.id) as count, sum({$startTable}.money) as sum FROM {$startTable} {$startJoin} {$startWhere} and {$startTable}.mark='2' UNION
        		SELECT count({$endTable}.id) as count, sum({$endTable}.money) as sum FROM {$endTable}  {$endJoin} {$endWhere} and {$endTable}.mark='2'
        	) tmp";
            //$result3 = $this->BcdDb->query($select3)->row();
        }

        return array(
            $result,
            //$count->count,
            1000,
            array(
                //$result2->count,
                //$result2->sum - $result3->sum,
                //$result3->count,
                //$count->count - $result3->count
                0,
                0,
                0,
                0,
            )
        );

    }

    /**
     * 参    数：$searchData 搜索条件
     *          $page 页码
     *          $pageCount 单页条数
     * 作    者：wangl
     * 功    能：提款列表
     * 修改日期：2014.11.11
     */
    public function list_withdraw($searchData, $page, $pageCount, $ischeck = FALSE)
    {
        $where = " where 1 ";
        $where .= $this->condition("{$this->cp_user}.uname", $searchData['name']);
        //订单编号查询 ===修改lkj
        $where .= $this->condition("{$this->cp_withdraw}.trade_no", trim($searchData['trade_no']));
        $where .= $this->condition(" {$this->cp_withdraw}.created", array(
            $searchData['start_time'],
            $searchData['end_time']
        ), "time");
        if ($this->emp($searchData['start_r_time']) || $this->emp($searchData['end_r_time']))
        {
            $where .= $this->condition(" {$this->cp_withdraw}.modified", array(
                $searchData['start_r_time'],
                $searchData['end_r_time']
            ), "time");
            $where .= $this->condition("{$this->cp_withdraw}.status", '2');
        }
        if ($this->emp($searchData['rtype']) && $this->emp($searchData['rtype1']))
        {
            $where .= $this->condition("{$this->cp_withdraw}.additions",
                $searchData['rtype'] . "@" . $searchData['rtype1']);
        }
        elseif ($this->nemp($searchData['rtype1']))
        {
            $where .= $this->condition("{$this->cp_withdraw}.additions", $searchData['rtype'], "likeRight");
        }
        if ($this->emp($searchData['ctype']))
        {
            if ($searchData['ctype'] == '1')
            {
                $where .= " and {$this->cp_withdraw}.status=0 and {$this->cp_withdraw}.review=0";
            }
            else
            {
                $where .= " and {$this->cp_withdraw}.status='{$searchData['ctype']}'";
                ($searchData['ctype'] > 0) ? "" : $where.=" and {$this->cp_withdraw}.review=1";
            }
        }
        else
        {
                $where .= $ischeck ? " and {$this->cp_withdraw}.status in('0','5') " : "";
        }

        $where .= $this->condition(" {$this->cp_withdraw}.money", array(
            $searchData['start_money'],
            $searchData['end_money']
        ), "during", "m");
        if ($this->emp($searchData['platform']) && $searchData['platform'] != - 1)
        {
            $where .= " and {$this->cp_withdraw}.platform = " . $searchData['platform'];
        }
        if ($this->emp($searchData['channel']))
        {
            $where .= " and {$this->cp_withdraw}.withdraw_channel = '{$searchData['channel']}' ";
        }
        $join = "";
        $joinField = "";
        if ($this->emp($searchData['name']))
        {
            $join = " INNER  JOIN {$this->cp_user} on {$this->cp_withdraw}.uid = {$this->cp_user}.uid ";
            $joinField = ", {$this->cp_user}.uname ";
        }

        $select = "SELECT {$this->cp_withdraw}.* FROM {$this->cp_withdraw}
        {$join}
        {$where}
        ORDER BY {$this->cp_withdraw}.created DESC
        LIMIT " . ($page - 1) * $pageCount . "," . $pageCount;
        $result = $this->BcdDb->query($select)->row_array();
        $result = $this->parse_result($result, array(
            "bank_province",
            "bank_city"
        ));
        $count = $this->BcdDb->query("SELECT COUNT(*) as count FROM {$this->cp_withdraw} {$join} {$where}")->row();

        //统计
        $succ = $fail = $oncheck = 0;
        $select2 = "SELECT count(distinct({$this->cp_withdraw}.uid)) as count, sum({$this->cp_withdraw}.money) as mon
        FROM {$this->cp_withdraw}  {$join} {$where}";
        if ( ! $ischeck)
        {
            $select2 .= "  and {$this->cp_withdraw}.status='2'";
        }
        $result2 = $this->BcdDb->query($select2)->row();
        if ( ! $ischeck)
        {
            $select3 = "SELECT count({$this->cp_withdraw}.id) as count,{$this->cp_withdraw}.status FROM {$this->cp_withdraw} {$join} {$where} group by {$this->cp_withdraw}.status";
            $result3 = $this->BcdDb->query($select3)->row_array();
            foreach ($result3 as $st)
            {
                switch ($st['status'])
                {
                    case '0':
                    case '1':
                        $oncheck += $st['count'];
                        break;
                    case '2':
                        $succ += $st['count'];
                        break;
                    case '3':
                    case '4':
                        $fail += $st['count'];
                        break;
                    default:
                        break;
                }

            }
        }

        return array(
            $result,
            $count->count,
            array(
                $result2->count,
                $result2->mon,
                $succ,
                $fail,
                $oncheck
            )
        );
    }

    /**
     * 参    数： $updata 更新数据
     *                 $where 更新条件
     * 作    者：wangl
     * 功    能：审核
     * 修改日期：2014.11.11
     */
    public function update_check($updata, $where)
    {
        $this->master->where($where);
        $this->master->update($this->cp_withdraw, $updata);
        $trade = $this->master->get_where($this->cp_withdraw, array(
        		'trade_no' => $where['trade_no']
        ))->row_array();
        $userInfo = $this->get_user_info($trade[0]['uid']);
        $MESSAGE = $this->config->item("MESSAGE");
        if ($trade[0]['status'] == 5 && $userInfo['phone'] != '')
        {
        	$userInfo['uname'] = mb_strlen($userInfo['uname']) > 2 ? (mb_substr($userInfo['uname'], 0,
        			2) . "**") : $userInfo['uname'];
        	$msg = str_replace(array(
        			"#MM#月#DD#日",
        			"#MONEY#"
        	), array(
        			date("m月d日", strtotime($trade[0]['created'])),
        			m_format($trade[0]['money'])
        	), $MESSAGE['withdraw_succ']);
        	$this->tools->sendSms($trade[0]['uid'], $userInfo['phone'], $msg, 15, '127.0.0.1', '194');
        	
            // APP消息推送加载类
            $this->load->library('mipush');
            $pushData = array(
                'type'      =>  'withdraw_succ',
                'uid'       =>  $trade[0]['uid'],
                'money'     =>  number_format(ParseUnit($trade[0]['money'], 1), 2),
                'time'      =>  $trade[0]['created'],
                'trade_no'  =>  $where['trade_no'],
            );  
            $this->mipush->index('user', $pushData);
            return TRUE;
        }

        return FALSE;
    }

    /**
     * 参    数： $trade_no 交易编号
     *                 $ctype 类别
     *                 $content 备注
     *                 $new_trade_no 新交易编号
     * 作    者：wangl
     * 功    能：审核
     * 修改日期：2014.11.11
     */
    public function check($trade_no, $ctype, $content)
    {
        $withdraw = $this->master->get_where($this->cp_withdraw, array(
            'trade_no' => $trade_no
        ))->getRow();
        if (!empty($withdraw) && in_array($withdraw['status'], array(0, 5)))
        {
            //提款失败操作
            $userInfo = $this->get_user_info($withdraw['uid']);
            if($ctype == 8)
            {
            	$rediskeys = $this->config->item("REDIS");
            	$this->master->trans_start();
            	$this->load->library("tools");
            	$new_trade_no = $this->tools->getIncNum('UNIQUE_KEY');
            	$wallet_log = array(
            		'uid'      => $withdraw['uid'],
            		'money'    => $withdraw['money'],
            		'ctype'    => 8,
            		'mark'	   => '1',
            		'trade_no' => $new_trade_no,
            		'orderId'  => $trade_no,
            		'umoney'   => $withdraw['money'] + $userInfo['money'],
            		'channel'  => $withdraw['channel'],
            		'platform' => $withdraw['platform'],
            		'app_version' => $withdraw['app_version'],
            		'created'  => date('Y-m-d H:i:s')
            	);
            	//钱包表增加失败流水
            	$res1 = $this->master->insert($this->cp_w_l, $wallet_log);
            	//更新提款记录
            	$res2 = $this->master->query("UPDATE {$this->cp_withdraw} set status='4', fail_time=now(), content=? WHERE trade_no = ?", array($content, $trade_no));
            	//总账资金流水
            	$res3 = $this->master->query("insert cp_capital_log(capital_id, trade_no, ctype, money, status, created) values (1, ?, 3, ?, 2, now())", array($trade_no, $withdraw['money']));
            	//用户账号资金价钱
            	$res4 = $this->master->query("update cp_user set money = money + {$withdraw['money']} where uid = ?", $withdraw['uid']);
            	//钱出总账
            	//$res5 = $this->master->query("update cp_capital set money = money - {$withdraw['money']} where id=1");
            	$res5 = true;
            	$res = $res1 && $res2 && $res3 && $res4 && $res5;
            	if ($res)
            	{
            		$this->master->trans_complete();
            		//刷新用户钱包缓存
            		if($this->cache->redis->hGet($rediskeys['USER_INFO'] . $withdraw['uid'], "uname"))
            		{
            			$this->cache->redis->hSet($rediskeys['USER_INFO'] . $withdraw['uid'], "money", $withdraw['money'] + $userInfo['money']);
            		}
            		else
            		{
            			$this->load->model('model_user');
            			$this->model_user->freshUserInfo($withdraw['uid']);
            		}
            		
            		if ($userInfo['phone'] != '')
            		{
            			$MESSAGE = $this->config->item("MESSAGE");
            			$userInfo['uname'] = mb_strlen($userInfo['uname']) > 2 ? (mb_substr($userInfo['uname'], 0,
            					2) . "**") : $userInfo['uname'];
            			$msg = str_replace(array(
            					"#UNAME#",
            					"#MM#月#DD#日",
            					"#MONEY#",
            					"#REASON#"
            			), array(
            					$userInfo['uname'],
            					date("m月d日 ", strtotime($withdraw['created'])),
            					m_format($withdraw['money']),
            					$content
            			), $MESSAGE['withdraw_fail']);
            			$this->tools->sendSms($withdraw['uid'], $userInfo['phone'], $msg, 15, '127.0.0.1', '194');

                        // APP消息推送加载类
                        $this->load->library('mipush');
                        $pushData = array(
                            'type'      =>  'withdraw_fail',
                            'uid'       =>  $withdraw['uid'],
                            'time'      =>  $withdraw['created'],
                            'trade_no'  =>  $trade_no,
                            'content'   =>  $content,
                        );  
                        $this->mipush->index('user', $pushData);
            		}
            		return true;
            	}
            	else
            	{
            		$this->master->trans_rollback();
            		return false;
            	}
            }
            elseif ($ctype == 6 && $withdraw['status'] == 5)
            {
                if ($userInfo['phone'] != '')
                {
                    $userInfo['uname'] = mb_strlen($userInfo['uname']) > 2 ? (mb_substr($userInfo['uname'], 0, 2) . "**") : $userInfo['uname'];
                    $MESSAGE = $this->config->item("MESSAGE");
                    $msg = str_replace(array(
                        "#MM#月#DD#日",
                        "#MONEY#"
                            ), array(
                        date("m月d日", strtotime($withdraw['created'])),
                        m_format($withdraw['money'])
                            ), $MESSAGE['withdraw_succ']);
                    $this->tools->sendSms($withdraw['uid'], $userInfo['phone'], $msg, 15, '127.0.0.1', '194');

                    // APP消息推送加载类
                    $this->load->library('mipush');
                    $pushData = array(
                        'type'      =>  'withdraw_succ',
                        'uid'       =>  $withdraw['uid'],
                        'money'     =>  number_format(ParseUnit($withdraw['money'], 1), 2),
                        'time'      =>  $withdraw['created'],
                        'trade_no'  =>  $trade_no,
                    );  
                    $this->mipush->index('user', $pushData);
                }
            	//提款成功操作
            	$this->master->trans_start();
            	//更新提款记录
            	$res1 = $this->master->query("UPDATE {$this->cp_withdraw} set status='2', succ_time=now() WHERE trade_no = ?", array($trade_no));
            	//总账资金流水
            	$res2 = $this->master->query("insert cp_capital_log(capital_id, trade_no, ctype, money, status, created) values (1, ?, 2, ?, 2, now())", array($trade_no, $withdraw['money']));
            	//钱出总账
            	//$res3 = $this->master->query("update cp_capital set money = money - {$withdraw['money']} where id=1");
            	$res3 = true;
            	if($res1 && $res2 && $res3)
            	{
            		$this->master->trans_complete();
            		return true;
            	}
            	else
            	{
            		$this->master->trans_rollback();
            		return false;
            	}
            }
        }
        return false;
    }

    /**
     * 参    数： $searchData 查询条件数组
     * 作    者：wangl
     * 功    能：获取导出数据
     * 修改日期：2014.11.11
     */
    public function get_export_data($searchData)
    {
        $where = " where 1 ";
        $where .= $this->condition("{$this->cp_user}.uname", $searchData['name']);
        $where .= $this->condition(" {$this->cp_withdraw}.created", array(
            $searchData['start_time'],
            $searchData['end_time']
        ), "time");
        if (!empty($searchData['rtype']) && !empty($searchData['rtype1']))
        {
            $where .= $this->condition("{$this->cp_withdraw}.additions",
                $searchData['rtype'] . "@" . $searchData['rtype1']);
        }
        elseif ($this->nemp($searchData['rtype1']))
        {
            $where .= $this->condition("{$this->cp_withdraw}.additions", $searchData['rtype'], "likeRight");
        }
        if ($this->emp($searchData['ctype']))
        {
            if ($searchData['ctype'] == '1')
            {
                $where .= " and {$this->cp_withdraw}.status=0 and {$this->cp_withdraw}.review=0";
            }
            else
            {
                $where .= " and {$this->cp_withdraw}.status='{$searchData['ctype']}'";
                ($searchData['ctype'] > 0) ? "" : $where.=" and {$this->cp_withdraw}.review=1";
            }
        }
        
        $where .= $this->condition(" {$this->cp_withdraw}.money", array(
            $searchData['start_money'],
            $searchData['end_money']
        ), "during", "m");

        $join = "";
        if ($this->emp($searchData['name']))
        {
            $join = " INNER  JOIN {$this->cp_user} on {$this->cp_withdraw}.uid = {$this->cp_user}.uid ";
        }

        $select = "SELECT {$this->cp_withdraw}.money,{$this->cp_withdraw}.additions,{$this->cp_withdraw}.uid, {$this->cp_withdraw}.trade_no
    	FROM {$this->cp_withdraw}
    	{$join}
    	{$where}
    	ORDER BY {$this->cp_withdraw}.created DESC";
    	$result = $this->BcdDb->query($select)->row_array();
        $result = $this->parse_result($result, array(
            "bank_province",
            "bank_city"
        ));

        return $result;
    }

    /**
     * 参    数：$id 用户ID
     * 作    者：wangl
     * 功    能：更具用户ID查找充值/提款
     * 修改日期：2014.11.11
     */
    public function find_trans_by_uid($id)
    {
        //出票成功数 / 累计投注金额
        $date = date('Y-m-d');
        $select = "select sum(total_recharge) as total_recharge
		from (
		(select IF(sum(w.money), sum(w.money), 0) as total_recharge from cp_wallet_logs w 
		where w.created > '{$date}' AND w.ctype = '0' AND w.mark = '1' and w.uid = '{$id}')
		union all 
		(select total_recharge from cp_hight_quality_user_index where uid = '{$id}')
		) mm";
        $recharge = $this->BcdDb->query($select)->getOne();
        $select1 = "select sum(total_withdraw) as total_withdraw
		from (
		(select IF(sum(w.money), sum(w.money), 0) as total_withdraw from cp_withdraw w
		where w.succ_time > '{$date}' AND w.`status` = '2' and uid = '{$id}')
		union all
		(select total_withdraw from cp_hight_quality_user_index where uid = '{$id}')
		) mm";
        $withdraw = $this->BcdDb->query($select1)->getOne();

        return array(
            $recharge,
            $withdraw
        );
    }
    
    public function list_capital($searchData, $page, $pageCount) {
    	$data = array();
    	$where .= $this->condition(" cl.created", array($searchData['start_time'], $searchData['end_time']), "time");
    	$wtable1 = $wtable2 = 'cp_wallet_logs';
    	$time1 = $time2 = date('Y-m-d 00:00:00', strtotime('-90 days'));
    	if ($searchData['start_time'] < $time1) {
    		$str1 = substr($searchData['start_time'], 0, 4);
    		$wtable1 = 'cp_wallet_logs_'.$str1;
    		if ($str1 !== date('Y')) {
    			$time1 = $str1."-12-31 23:59:59";
    		}
    	}
    	if ($searchData['end_time'] < $time2) {
    		$str2 = substr($searchData['end_time'], 0, 4);
    		$wtable2 = 'cp_wallet_logs_'.$str2;
    		if ($str2 !== date('Y')) {
    			$time2 = $str2."-01-01 00:00:00";
    		}
    	}
    	unset($searchData['start_time'], $searchData['end_time']);
    	if ($this->emp($searchData['start_money'])) {
    		$where .= " and cl.money >= ?";
    		$data[] = $searchData['start_money']*100;
    		unset($searchData['start_money']);
    	}
    	if ($this->emp($searchData['end_money'])) {
    		$where .= " and cl.money <= ?";
    		$data[] = $searchData['end_money']*100;
    		unset($searchData['end_money']);
    	}
    	foreach ($searchData as $key => $val) {
    		if ($this->emp($val)) {
    			if ($key === 'uname') {
    				$where .= " and u.uname = ?";
    			}elseif ($key === 'content') {
    				$where .= " and wl.content = ?";
    			}else {
    				$where .= " and cl.`{$key}` = ?";
    			}
    			$data[] = $val;
    		}
    	}
    	
    	$sql = "FROM cp_capital_log as cl force index (created) LEFT JOIN #WTABLE# as wl ON cl.trade_no=wl.trade_no LEFT JOIN cp_user as u ON wl.uid=u.uid LEFT JOIN cp_user_info as ui on wl.uid=ui.uid WHERE cl.capital_id='2' and ((wl.uid is not null and cl.ctype not in ('12', '13', '14')) or (cl.ctype in ('12', '13', '14'))) {$where}";
    	
    	if ($wtable1 === $wtable2) {
    		$sql = str_replace('#WTABLE#', $wtable1, $sql);
    		$count = $this->BcdDb->query("select count(*) as count, sum(case when cl.`status`=2 then cl.money else 0 end) as `out`,  sum(case when cl.`status`=1 then cl.money else 0 end) as `in` ".$sql, $data)->getRow();
    		$sql = "SELECT cl.trade_no, u.uid, u.uname, ui.real_name, cl.ctype, cl.money, cl.`status`, cl.created, wl.content ".$sql." ORDER BY cl.trade_no DESC LIMIT " . ($page - 1) * $pageCount . "," . $pageCount;
    		$res = $this->BcdDb->query($sql, $data)->getAll();
    	}else {
    	    $count = $this->BcdDb->query("select sum(count) as count, sum(`out`) as `out`, sum(`in`) as `in`
    				from (select count(*) as count, sum(case when cl.`status`=2 then cl.money else 0 end) as `out`,  sum(case when cl.`status`=1 then cl.money else 0 end) as `in` ".str_replace('#WTABLE#', $wtable1, $sql)."
    				union select count(*) as count, sum(case when cl.`status`=2 then cl.money else 0 end) as `out`,  sum(case when cl.`status`=1 then cl.money else 0 end) as `in` ".str_replace('#WTABLE#', $wtable2, $sql).") tmp", array_merge($data, $data))->getRow();
    		$sql = "select * from (SELECT cl.trade_no, u.uid, u.uname, ui.real_name, cl.ctype, cl.money, cl.`status`, cl.created, wl.content ".str_replace('#WTABLE#', $wtable1, $sql)." and cl.created <= '".$time1."' 
    				union SELECT cl.trade_no, u.uid, u.uname, ui.real_name, cl.ctype, cl.money, cl.`status`, cl.created, wl.content ".str_replace('#WTABLE#', $wtable2, $sql)." and cl.created >= '".$time2."') tmp
    				ORDER BY tmp.trade_no DESC LIMIT " . ($page - 1) * $pageCount . "," . $pageCount;
    		$res = $this->BcdDb->query($sql, array_merge($data, $data))->getAll();
    	}
    	
    	return array(
    		'data' => $res,
    		'count'=> $count
    	);
    }
    
    public function updateReview($update, $where)
    {
        $this->master->where($where);
        $this->master->update($this->cp_withdraw, $update);
        return TRUE;
    }

    public function getWithdrawChannel()
    {
        $sql = "select * from cp_withdraw_channel where 1 limit 1";
        $channel = $this->BcdDb->query($sql)->getRow();
        return $channel;
    }
    
    public function updateWithdrawChannel($channel, $audit)
    {
        $sql = "UPDATE cp_withdraw_channel set channel=?, audit =? ";
        $this->master->query($sql, array($channel, $audit));
    }

    public function getFailWithdraw()
    {
        $sql = "SELECT trade_no FROM cp_withdraw WHERE created >= '2018-06-18 00:00:00' AND remark LIKE '9104%' AND status = 5;";
        return $this->master->query($sql)->getAll();
    }
}
