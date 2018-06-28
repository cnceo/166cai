<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Activity_Jchd_Model extends MY_Model
{
	public function __construct()
	{
		parent::__construct();
	}
	
	/**
	 * 根据状态返回一条期次信息
	 * @return unknown
	 */
	public function getConfigByStatus($status) {
	    $sql = "select * from cp_activity_jchd_config where modified > date_sub(now(), interval 7 day) and status = ? order by id desc limit 1";
	    return $this->db->query($sql, array($status))->getRow();
	}
	
	/**
	 * 查询胜平负投注人数
	 * @param unknown $theme_id
	 * @param unknown $issue
	 * @return unknown
	 */
	public function getCountSpfCode($theme_id, $issue) {
	    $sql = "SELECT SUM(IF(code1 = 3, 1, 0)) code1_s, SUM(IF(code1 = 1, 1, 0)) code1_p, 
        SUM(IF(code2 = 3, 1, 0)) code2_s, SUM(IF(code2 = 1, 1, 0)) code2_p, 
        SUM(IF(code3 = 3, 1, 0)) code3_s, SUM(IF(code3 = 1, 1, 0)) code3_p, 
        SUM(IF(code4 = 3, 1, 0)) code4_s, SUM(IF(code4 = 1, 1, 0)) code4_p 
        FROM cp_activity_jchd_join WHERE theme_id = ? AND issue=?";
	    
	    return $this->db->query($sql, array($theme_id, $issue))->getRow();
	}
	
	/**
	 * 查询对阵比分信息
	 * @param unknown $mid
	 * @return unknown
	 */
	public function getMatchScore($mid) {
	    return $this->dc->query("select full_score from cp_jczq_paiqi where mid = ? and status = 50 and aduitflag > 0", array($mid))->getRow();
	}
	
	/**
	 * 更新win_num  脚本执行
	 * @param unknown $theme_id
	 * @param unknown $issue
	 * @param unknown $filed
	 * @param unknown $value
	 */
	public function updateWinNum($theme_id, $issue, $filed, $value) {
	    return $this->db->query("update cp_activity_jchd_join set win_num = win_num + 1 where theme_id = ? and issue = ? and {$filed} = ?", array($theme_id, $issue, $value));
	}
	
	/**
	 * 查询该期活动中奖人数  查主库 用于计算奖金
	 * @param unknown $theme_id
	 * @param unknown $issue
	 * @param unknown $winNum
	 * @return unknown
	 */
	public function getWinCounts($theme_id, $issue, $winNum) {
	    return $this->db->query("select count(*) from cp_activity_jchd_join where theme_id = ? and issue = ? and win_num = ?", array($theme_id, $issue, $winNum))->getOne();
	}
	
	/**
	 * 查询小于win_num用户数量
	 * @param unknown $theme_id
	 * @param unknown $issue
	 * @param unknown $num
	 * @return unknown
	 */
	public function getJoinCounts($theme_id, $issue, $num) {
	    return $this->db->query("select count(*) from cp_activity_jchd_join where theme_id = ? and issue = ? and win_num < ?", array($theme_id, $issue, $num))->getOne();
	}
	
	/**
	 * 更新打败人数和打败率
	 * @param unknown $theme_id
	 * @param unknown $issue
	 * @param unknown $win_num
	 * @param unknown $data
	 * @return unknown
	 */
	public function updateDefeatNum($theme_id, $issue, $win_num, $data) {
	    $sql = "update cp_activity_jchd_join set defeat_num = ?, defeat_ratio = ?, show_status = ? where
        theme_id = ? and issue = ? and win_num = ? ";
	    return $this->db->query($sql, array($data['defeat_num'], $data['defeat_ratio'], $data['show_status'], $theme_id, $issue, $win_num));
	}
	
	/**
	 * 
	 * @param unknown $id
	 * @param unknown $data
	 * @return unknown
	 */
	public function updateConfig($id, $data)
	{
	    $this->db->where('id', $id);
	    $this->db->update('cp_activity_jchd_config', $data);
	    return $this->db->affected_rows();
	}
	
	/**
	 * 竞猜活动派奖脚本
	 */
	public function sendPrice() {
	    $sql = "select * from cp_activity_jchd_config where modified > date_sub(now(), interval 7 day) and status = 2 and cpstate = 1 limit 1";
	    $jchdConfig = $this->slave->query($sql)->getRow();
	    if(!$jchdConfig) {
	        return ;
	    }
	    
	    $plan = json_decode($jchdConfig['plan'], true);
	    $num = count($plan);
	    $orderSql = "select uid, orderId, created from cp_activity_jchd_join where theme_id = ? and issue = ? and win_num = ? and status = 0 order by id asc limit 200";
	    $orders = $this->db->query($orderSql, array($jchdConfig['theme_id'], $jchdConfig['issue'], $num))->getAll();
	    $this->load->model('capital_model');
	    $this->load->model('wallet_model');
	    $this->load->model('user_model');
	    $this->load->library('mipush');
	    while ($orders) {
	        foreach ($orders as $order) {
	            $this->db->trans_start();
	            $userInfo = $this->getUserMoney($order['uid']);
	            $trade_no = $this->tools->getIncNum('UNIQUE_KEY');
	            // 彩金派送流水
	            $wallet_log = array(
	                'uid'       => $order['uid'],
	                'money'     => $jchdConfig['bouns'],
	                'ctype'     => 9,
	                'trade_no'  => $trade_no,
	                'umoney'    => ($userInfo['money'] + $jchdConfig['bouns']),
	                'must_cost' => 0,
	                'dispatch'  => 0,
	                'mark'      => '1',
	                'orderId'   => $order['orderId'],
	                'content'   => '奖金派送（竞猜活动） '
	            );
	            
	            $res1 = $this->db->query("insert cp_wallet_logs(" . implode(',', array_keys($wallet_log)) . ', created)
		        values(' . implode(',', array_map(array($this, 'maps'), $wallet_log)) . ', now())', $wallet_log);
	            $res2 = $this->db->query("update cp_user set money = money + {$jchdConfig['bouns']} where uid = ?", array($order['uid']));
	            // 总账记录流水
	            $res3 = $this->capital_model->recordCapitalLog('2', $trade_no, 'orthers', $jchdConfig['bouns'], '2', $tranc = FALSE);
	            $res4 = $this->db->query("update cp_activity_jchd_join set status = 2 where orderId = ?", array($order['orderId']));
	            if($res1 && $res2 && $res3 && $res4)
	            {
	                $this->db->trans_complete();
	                $this->wallet_model->freshWallet($order['uid']);
// 	                $uinfo = $this->user_model->getUserInfo($order['uid']);
// 	                if(!empty($uinfo['phone'])) {
// 	                    //发短信
// 	                    $message = $this->config->item('MESSAGE');
// 	                    $msg = $message['activity_jchd'];
// 	                    $money = number_format((doubleval($jchdConfig['bouns']) / 1000) * 10, 2);
// 	                    $msg = str_replace(array('#MM#月#DD#日', '#ISSUE#', '#MONEY#'), array(date("m月d日", strtotime($order['created'])), $jchdConfig['issue'], $money), $msg);
// 	                    $this->tools->sendSms($order['uid'], $uinfo['phone'], $msg, 10, '127.0.0.1', '192');
// 	                }
	                //TODO 推送
	                $msg = '恭喜您！您于#MM#月#DD#日完成的第#ISSUE#期世界杯竞猜分得奖金#MONEY#元，已经派奖到您的账户。';
	                $pushData = array(
	                    'type'          =>  'open_url',
	                    'uid'           =>  $order['uid'],
	                    'title'         =>  '竞猜活动中奖啦',
	                    'content'       =>  str_replace(array('#MM#月#DD#日', '#ISSUE#', '#MONEY#'), array(date("m月d日", strtotime($order['created'])), $jchdConfig['issue'], $money), $msg),
	                    'app_url'       => $this->config->item("android_domain") . '/app/activity/jchd',
	                    'ios_url'       => $this->config->item("ios_domain") . '/ios/activity/jchd',
	                    'time_to_live'  =>  10 * 60 * 1000,     // 默认十分钟
	                );
	                $this->mipush->index('user_com', $pushData);
	            } else {
	                $this->db->trans_rollback();
	            }
	        }
	        
	        $orders = $this->db->query($orderSql, array($jchdConfig['theme_id'], $jchdConfig['issue'], $num))->getAll();
	    }
	    
	    //不中奖状态更新
	    $notWinSql = "update cp_activity_jchd_join set status = 1 where theme_id = ? and issue = ? and win_num < ? and status = 0 ";
	    $result = $this->db->query($notWinSql, array($jchdConfig['theme_id'], $jchdConfig['issue'], $num));
	    if($result) {
	        $this->db->query("update cp_activity_jchd_config set status = 3, cpstate = 2 where id = ?", array($jchdConfig['id']));
	    }
	}
	
	public function getUserMoney($uid)
	{
	    return $this->db->query('SELECT money, blocked, must_cost, dispatch from cp_user where uid = ? for update', array($uid))->getRow();
	}
	
	/**
	 * 排名计算
	 */
	public function rank() {
	    $sql = "select * from cp_activity_jchd_config where modified > date_sub(now(), interval 7 day) and status = 3 and cpstate = 2 limit 1";
	    $jchdConfig = $this->slave->query($sql)->getRow();
	    if(!$jchdConfig) {
	        return ;
	    }
	    
	    $id = 0;
	    $orderSql = "select id, uid, win_num, show_status from cp_activity_jchd_join where theme_id = ? and issue = ? and id > ?  order by id asc limit 1000";
	    $orders = $this->db->query($orderSql, array($jchdConfig['theme_id'], $jchdConfig['issue'], $id))->getAll();
	    $this->db->trans_start();
	    $flag = false;
	    while ($orders) {
	        $s_data = array();
	        $d_data = array();
	        foreach ($orders as $data) {
	            array_push($s_data, '(?, ?, ?, ?, ?, now())');
	            array_push($d_data, $data['uid']);
	            array_push($d_data, $jchdConfig['theme_id']);
	            $issue_num = $data['show_status'] == 1 ? 1 : 0;
	            $bouns = $issue_num > 0 ? $jchdConfig['bouns'] : 0;
	            array_push($d_data, $issue_num);
	            array_push($d_data, $bouns);
	            array_push($d_data, $data['win_num']);
	        }
	        $inisql = "insert into cp_activity_jchd_rank(uid, theme_id, issue_num, bouns, match_num, created) values
            " . implode(',', $s_data) . $this->onduplicate(array('uid', 'theme_id', 'issue_num', 'bouns', 'match_num', 'created'), array('issue_num', 'bouns', 'match_num'), array('issue_num', 'bouns', 'match_num'));
	        $res = $this->db->query($inisql, $d_data);
	        if(!$res) {
	            $this->db->trans_rollback();
	            return ;
	        }
	        
	        $id = $data['id'];
	        $orders = $this->db->query($orderSql, array($jchdConfig['theme_id'], $jchdConfig['issue'], $id))->getAll();
	        $flag = true;
	    }
	    $this->db->query("update cp_activity_jchd_config set cpstate = 0 where id = ?", array($jchdConfig['id']));
	    
	    $this->db->trans_complete();
	    
	    //计算排名
	    if($flag) {
	        //还原标识
	        $this->db->query("update cp_activity_jchd_rank set cpstate = 0 where theme_id = ?", array($jchdConfig['theme_id']));
	        $rank = 1;
	        $orderSql = "select id from cp_activity_jchd_rank where theme_id = ? and cpstate = 0 order by match_num desc, bouns desc, issue_num desc, id asc limit 1000";
	        $orders = $this->db->query($orderSql, array($jchdConfig['theme_id']))->getAll();
	        $this->db->trans_start();
	        while ($orders) {
	            $s_data = array();
	            $d_data = array();
	            foreach ($orders as $data) {
	                array_push($s_data, '(?, ?, ?)');
	                array_push($d_data, $data['id']);
	                array_push($d_data, $rank ++);
	                array_push($d_data, 1);
	            }
	            $inisql = "insert into cp_activity_jchd_rank(id, rank, cpstate) values
            " . implode(',', $s_data) . $this->onduplicate(array('id', 'rank', 'cpstate'), array('rank', 'cpstate'));
	            $res = $this->db->query($inisql, $d_data);
	            if(!$res) {
	                $this->db->trans_rollback();
	                return ;
	            }
	            
	            $orders = $this->db->query($orderSql, array($jchdConfig['theme_id']))->getAll();
	        }
	        
	        $this->db->trans_complete();
	    }
	}
	
	/**
	 * 竞猜活动派奖脚本
	 */
	public function testSendPrice($issue) {
	    $sql = "select * from cp_activity_jchd_config where issue = ?";
	    $jchdConfig = $this->slave->query($sql, array($issue))->getRow();
	    if(!$jchdConfig) {
	        return ;
	    }
	    
	    $plan = json_decode($jchdConfig['plan'], true);
	    $num = count($plan);
	    $orderSql = "select uid, orderId, created from cp_activity_jchd_join where theme_id = ? and issue = ? and win_num = ? and status = 0 order by id asc limit 200";
	    $orders = $this->db->query($orderSql, array($jchdConfig['theme_id'], $jchdConfig['issue'], $num))->getAll();
	    $this->load->model('capital_model');
	    $this->load->model('wallet_model');
	    $this->load->model('user_model');
	    $this->load->library('mipush');
	    while ($orders) {
	        foreach ($orders as $order) {
	            $this->db->trans_start();
	            $userInfo = $this->getUserMoney($order['uid']);
	            $trade_no = $this->tools->getIncNum('UNIQUE_KEY');
	            // 彩金派送流水
	            $wallet_log = array(
	                'uid'       => $order['uid'],
	                'money'     => $jchdConfig['bouns'],
	                'ctype'     => 9,
	                'trade_no'  => $trade_no,
	                'umoney'    => ($userInfo['money'] + $jchdConfig['bouns']),
	                'must_cost' => 0,
	                'dispatch'  => 0,
	                'mark'      => '1',
	                'orderId'   => $order['orderId'],
	                'content'   => '奖金派送（竞猜活动） '
	            );
	            
	            $res1 = $this->db->query("insert cp_wallet_logs(" . implode(',', array_keys($wallet_log)) . ', created)
		        values(' . implode(',', array_map(array($this, 'maps'), $wallet_log)) . ', now())', $wallet_log);
	            $res2 = $this->db->query("update cp_user set money = money + {$jchdConfig['bouns']} where uid = ?", array($order['uid']));
	            // 总账记录流水
	            $res3 = $this->capital_model->recordCapitalLog('2', $trade_no, 'orthers', $jchdConfig['bouns'], '2', $tranc = FALSE);
	            $res4 = $this->db->query("update cp_activity_jchd_join set status = 2 where orderId = ?", array($order['orderId']));
	            if($res1 && $res2 && $res3 && $res4)
	            {
	                $this->db->trans_complete();
	                $this->wallet_model->freshWallet($order['uid']);
// 	                $uinfo = $this->user_model->getUserInfo($order['uid']);
// 	                if(!empty($uinfo['phone'])) {
// 	                    //发短信
// 	                    $message = $this->config->item('MESSAGE');
// 	                    $msg = $message['activity_jchd'];
// 	                    $money = number_format((doubleval($jchdConfig['bouns']) / 1000) * 10, 2);
// 	                    $msg = str_replace(array('#MM#月#DD#日', '#ISSUE#', '#MONEY#'), array(date("m月d日", strtotime($order['created'])), $jchdConfig['issue'], $money), $msg);
// 	                    $this->tools->sendSms($order['uid'], $uinfo['phone'], $msg, 10, '127.0.0.1', '192');
// 	                }
	                //TODO 推送
	                $msg = '恭喜您！您于#MM#月#DD#日完成的第#ISSUE#期世界杯竞猜分得奖金#MONEY#元，已经派奖到您的账户。';
	                $pushData = array(
	                    'type'          =>  'open_url',
	                    'uid'           =>  $order['uid'],
	                    'title'         =>  '竞猜活动中奖啦',
	                    'content'       =>  str_replace(array('#MM#月#DD#日', '#ISSUE#', '#MONEY#'), array(date("m月d日", strtotime($order['created'])), $jchdConfig['issue'], $money), $msg),
	                    'app_url'       => $this->config->item("android_domain") . '/app/activity/jchd',
	                    'ios_url'       => $this->config->item("ios_domain") . '/ios/activity/jchd',
	                    'time_to_live'  =>  10 * 60 * 1000,     // 默认十分钟
	                );
	                $this->mipush->index('user_com', $pushData);
	            } else {
	                $this->db->trans_rollback();
	            }
	        }
	        
	        $orders = $this->db->query($orderSql, array($jchdConfig['theme_id'], $jchdConfig['issue'], $num))->getAll();
	    }
	    
	    //不中奖状态更新
// 	    $notWinSql = "update cp_activity_jchd_join set status = 1 where theme_id = ? and issue = ? and win_num < ? and status = 0 ";
// 	    $result = $this->db->query($notWinSql, array($jchdConfig['theme_id'], $jchdConfig['issue'], $num));
// 	    if($result) {
// 	        $this->db->query("update cp_activity_jchd_config set status = 3, cpstate = 2 where id = ?", array($jchdConfig['id']));
// 	    }
	}
	
	/**
	 * 排名计算
	 */
	public function testrank($issue) {
	    $sql = "select * from cp_activity_jchd_config where issue = ?";
	    $jchdConfig = $this->slave->query($sql, array($issue))->getRow();
	    $id = 0;
	    $orderSql = "select id, uid, win_num, show_status from cp_activity_jchd_join where theme_id = ? and issue = ? and id > ?  order by id asc limit 1000";
	    $orders = $this->db->query($orderSql, array($jchdConfig['theme_id'], $jchdConfig['issue'], $id))->getAll();
	    $this->db->trans_start();
	    $flag = false;
	    while ($orders) {
	        $s_data = array();
	        $d_data = array();
	        foreach ($orders as $data) {
	            array_push($s_data, '(?, ?, ?, ?, ?, now())');
	            array_push($d_data, $data['uid']);
	            array_push($d_data, $jchdConfig['theme_id']);
	            $issue_num = $data['show_status'] == 1 ? 1 : 0;
	            $bouns = $issue_num > 0 ? $jchdConfig['bouns'] : 0;
	            array_push($d_data, $issue_num);
	            array_push($d_data, $bouns);
	            array_push($d_data, $data['win_num']);
	        }
	        $inisql = "insert into cp_activity_jchd_rank(uid, theme_id, issue_num, bouns, match_num, created) values
            " . implode(',', $s_data) . $this->onduplicate(array('uid', 'theme_id', 'issue_num', 'bouns', 'match_num', 'created'), array('issue_num', 'bouns', 'match_num'), array('issue_num', 'bouns', 'match_num'));
	        $res = $this->db->query($inisql, $d_data);
	        if(!$res) {
	            $this->db->trans_rollback();
	            return ;
	        }
	        
	        $id = $data['id'];
	        $orders = $this->db->query($orderSql, array($jchdConfig['theme_id'], $jchdConfig['issue'], $id))->getAll();
	        $flag = true;
	    }
	    
	    $this->db->trans_complete();
	    
	    //计算排名
	    if($flag) {
	        //还原标识
	        $this->db->query("update cp_activity_jchd_rank set cpstate = 0 where theme_id = ?", array($jchdConfig['theme_id']));
	        $rank = 1;
	        $orderSql = "select id from cp_activity_jchd_rank where theme_id = ? and cpstate = 0 order by match_num desc, bouns desc, issue_num desc, id asc limit 1000";
	        $orders = $this->db->query($orderSql, array($jchdConfig['theme_id']))->getAll();
	        $this->db->trans_start();
	        while ($orders) {
	            $s_data = array();
	            $d_data = array();
	            foreach ($orders as $data) {
	                array_push($s_data, '(?, ?, ?)');
	                array_push($d_data, $data['id']);
	                array_push($d_data, $rank ++);
	                array_push($d_data, 1);
	            }
	            $inisql = "insert into cp_activity_jchd_rank(id, rank, cpstate) values
            " . implode(',', $s_data) . $this->onduplicate(array('id', 'rank', 'cpstate'), array('rank', 'cpstate'));
	            $res = $this->db->query($inisql, $d_data);
	            if(!$res) {
	                $this->db->trans_rollback();
	                return ;
	            }
	            
	            $orders = $this->db->query($orderSql, array($jchdConfig['theme_id']))->getAll();
	        }
	        
	        $this->db->trans_complete();
	    }
	}
}
