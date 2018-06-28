<?php

class Withdraw_Model extends MY_Model
{
    private $banks = array(
            '1025' => array(
                '中国工商银行（借记卡）',
                '0102',
                'ICBC'
            ),
            '3080' => array(
                '招商银行（借记卡）',
                '0308',
                'CMB'
            ),
            '105' => array(
                '中国建设银行（借记卡）',
                '0105',
                'CCB'
            ),
            '103' => array(
                '中国农业银行（借记卡）',
                '0103',
                'ABC'
            ),
            '104' => array(
                '中国银行（借记卡）',
                '0104',
                'BOC'
            ),
            '301' => array(
                '交通银行',
                '0301',
                'BOCOM'
            ),
            '307' => array(
                '平安银行',
                '0307',
                'PAB'
            ),
            '309' => array(
                '兴业银行',
                '0309',
                'CIB'
            ),
            '311' => array(
                '华夏银行（借记卡）',
                '0304',
                'HXB'
            ),
            '305' => array(
                '中国民生银行（借记卡）',
                '0305',
                'CMBC'
            ),
            '306' => array(
                '广发银行（借记卡）',
                '0306',
                'GDB'
            ),
            '314' => array(
                '上海浦东发展银行（借记卡）',
                '0310',
                'SPDB'
            ),
            '313' => array(
                '中信银行（借记卡）',
                '0302',
                'CNCB'
            ),
            '312' => array(
                '光大银行（借记卡）',
                '0303',
                'CEB'
            ),
            '316' => array(
                '南京银行（借记卡）',
                '04243010',
                'NJBC'
            ),
            '3230' => array(
                '邮政储蓄银行（借记卡）',
                '0403',
                'PSBC'
            ),
            '324' => array(
                '杭州银行（借记卡）',
                '04233310',
                'HZBC'
            ),
            '302' => array(
                '宁波银行（借记卡）',
                '04083320',
                'NBBC'
            ),
            '310' => array(
                '北京银行（借记卡）',
                '0313',
                'BCCB'
            ),
            '326' => array(
                '上海银行（借记卡）',
                '04012900',
                'BOS'
            ),
            '329' => array(
                '浙江泰隆银行（借记卡）',
                '4733450',
                'ZJTLBC'
            ),
            '332' => array(
                '金华银行（借记卡）',
                '04263380',
                'JHBC'
            ),
            '342' => array(
                '重庆农商银行（借记卡）',
                '14136530',
                'CQSB'
            ),
            '345' => array(
                '重庆银行（借记卡）',
                '04416530',
                'CQBC'
            ),
            '339' => array(
                '富滇银行（借记卡）',
                '04667310',
                'FDB'
            ),
            '344' => array(
                '恒丰银行（借记卡）',
                '0315',
                'EBCL'
            ),
            '317' => array(
                '渤海银行（借记卡）',
                '0318',
                'BOHC'
            ),
            '335' => array(
                '北京农商行（借记卡）',
                '14181000',
                'BRCB'
            ),
            '336' => array(
                '成都银行（借记卡）',
                '04296510',
                'CDSBC'
            ),
            '340' => array(
                '汉口银行（借记卡）',
                '03135210',
                'WHBC'
            ),
        );

    public function __construct()
    {
        parent::__construct();
    }

    public function getAllWithdraw($audit)
    {
    	$sqlWhere = $audit ? " and cp_withdraw.review=1" : " and (IF(cp_withdraw.money > 2000000, cp_withdraw.review=1, cp_withdraw.review in(0, 1)))";
        $sql = "SELECT cp_withdraw.trade_no,cp_withdraw.money,cp_withdraw.additions,cp_user_info.real_name FROM cp_withdraw left join cp_user_info on cp_withdraw.uid=cp_user_info.uid where cp_withdraw.created > date_sub(now(), interval 4 day) and cp_withdraw.status=0 {$sqlWhere} and cp_withdraw.remark=''";
        $withdraws = $this->db->query($sql)->getAll();
        foreach ($withdraws as $key => $withdraw) {
            $pay_info = explode('@', $withdraw['additions']);
            $withdraws[$key]['bank_id'] = $pay_info[2];
            $withdraws[$key]['bank_type'] = $this->banks[$pay_info[1]][1];
            $withdraws[$key]['bank_no'] = $this->banks[$pay_info[1]][2];
        }

        return $withdraws;
    }

    public function update_check($updata, $where)
    {
        $this->db->where($where);
        $this->db->update('cp_withdraw', $updata);
        $trade = $this->db->get_where('cp_withdraw', array(
                'trade_no' => $where['trade_no'],
        ))->row_array();
        $sql = 'select uname,n.phone from cp_user m left join cp_user_info n on m.uid = n.uid where m.uid = ?';
        $userInfo = $this->db->query($sql, array($trade[0]['uid']))->getRow();
        $MESSAGE = $this->config->item('MESSAGE');
        if ($trade[0]['status'] == 5 && $userInfo['phone'] != '') {
            $userInfo['uname'] = mb_strlen($userInfo['uname']) > 2 ? (mb_substr($userInfo['uname'], 0,
                    2).'**') : $userInfo['uname'];
            $msg = str_replace(array(
                    '#MM#月#DD#日',
                    '#MONEY#',
            ), array(
                    date('m月d日', strtotime($trade[0]['created'])),
                    m_format($trade[0]['money']),
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
            //提款成功操作
            $this->db->trans_start();
            //更新提款记录
            $res1 = $this->db->query("UPDATE cp_withdraw set status='2', succ_time=now() WHERE trade_no = ?", array($where['trade_no']));
            //总账资金流水
            $res2 = $this->db->query("insert cp_capital_log(capital_id, trade_no, ctype, money, status, created) values (1, ?, 2, ?, 2, now())", array($where['trade_no'], $trade[0]['money']));
            //钱出总账
            //$res3 = $this->db->query("update cp_capital set money = money - {$trade[0]['money']} where id=1");
            $res3 = true;
            if($res1 && $res2 && $res3)
            {
                $this->db->trans_complete();
                return true;
            }
            else
            {
                $this->db->trans_rollback();
                return false;
            }
        }

        return false;
    }

    public function updateColumn($updata, $where)
    {
        $this->db->where($where);
        $this->db->update('cp_withdraw', $updata);
    }

    public function alertEmail($content)
    {
        $alertRow = array('18', '提款失败报警', $content, date('Y-m-d H:i:s', time()));
        $isql = 'insert cp_alert_log(ctype, title, content, created) values (?, ?, ?, ?)';
        $this->db->query($isql, $alertRow);
    }
    
	public function getNeedQueryWithdraw()
    {
        $sql = "SELECT cp_withdraw.trade_no,cp_withdraw.remark FROM cp_withdraw  where cp_withdraw.created > date_sub(now(), interval 7 day) and cp_withdraw.status=5 and cp_withdraw.review=1 and cp_withdraw.remark!=''";
        $withdraws = $this->db->query($sql)->getAll();
        $orderid = array();
        foreach ($withdraws as $key => $withdraw) {
        	$remark = explode('/', $withdraw['remark']);
        	if($remark['0'] == '2000' || $remark['0'] == '2008')
        	{
        		$orderid[] = $withdraw['trade_no'];
        	}
        }
        return $orderid;
    }
    
    public function getWithdrawChannel()
    {
        $sql = "select * from cp_withdraw_channel where 1 limit 1";
        $channel = $this->db->query($sql)->getRow();
        return $channel;
    }
    
    public function getStatus($id)
    {
        $sql = "select cp_withdraw.status,cp_user_info.real_name from cp_withdraw left join cp_user_info on cp_withdraw.uid=cp_user_info.uid where trade_no = ?";
        $withdraw = $this->db->query($sql, array($id))->getRow();
        return $withdraw;
    }
    
    public function getAllNeedWd($start, $end)
    {
        $sql = "select uid,trade_no,money,additions from cp_withdraw where created>=? and created<=? and status = 5 and withdraw_channel='xianfeng'";
        $withdraws = $this->db->query($sql, array($start, $end))->getAll();
        return $withdraws;
    }
    
    public function setWithDrawSuc($withdraw)
    {
        $this->db->trans_start();
        //更新提款记录
        $res1 = $this->db->query("UPDATE cp_withdraw set status='2', succ_time=now() WHERE trade_no = ?", array($withdraw['trade_no']));
        //总账资金流水
        $res2 = $this->db->query("insert cp_capital_log(capital_id, trade_no, ctype, money, status, created) values (1, ?, 2, ?, 2, now())", array($withdraw['trade_no'], $withdraw['money']));
        //钱出总账
        //$res3 = $this->db->query("update cp_capital set money = money - {$trade[0]['money']} where id=1");
        $res3 = true;
        if ($res1 && $res2 && $res3) {
            $this->db->trans_complete();
            return true;
        } else {
            $this->db->trans_rollback();
            return false;
        }
    }

}
