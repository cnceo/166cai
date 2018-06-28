<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/*
 * APP 收银台 模型层
 * @date:2015-04-17
 */

class Newwallet_Model extends MY_Model
{
    public $tbname;
    public $ctype = array(
        'recharge'                   => 0, //充值-添加预付款
        'pay'                        => 1, //付款-购买彩票
        'reward'                     => 2, //奖金-奖金派送
        'drawback'                   => 3, //订单退款-订单失败返款
        'apply_for_withdraw'         => 4, //提款
        'apply_for_withdraw_succ'    => 5, //提款成功解除冻结预付款(已废弃)
        'withdraw_succ'              => 6, //申请提款成功-扣除预付款成功(已废弃)
        'apply_for_withdraw_conceal' => 7, //申请提款撤销(已废弃)
        'apply_for_withdraw_fail'    => 8, //提款失败还款
        'dispatch'                   => 9, //系统奖金派送
        'addition'                   => 10, //其他应收款项
        'transfer'                   => 11, //转帐
        'rebate'                     => 14, //联盟返点
        'united_refund'              => 15  //合买返还预付款
    );
    public $status = array(
        'withdraw_ini'     => 0, //提款申请状态
        'withdraw_lock'    => 1, //提款锁定后台处理中(已废弃)
        'withdraw_over'    => 2, //处理结束
        'withdraw_concel'  => 3, //申请取消(已废弃)
        'withdraw_fail'    => 4, //财务打款失败
        'withdraw_operate' => 5, //已操作打款
    );
    
    public function __construct()
    {
        parent::__construct();
        $this->tbname = 'cp_wallet_logs';
        $this->load->library('tools');
    }
    
    public function setWithDraw($money, $uid, $platform, $additions='', $extData = array())
    {
        $this->db->trans_start();
        $money = intval($money);
        $withdraw = $this->getWithDraw($uid);
        $cmoney = $this->getMoney($uid);
        $cmoney = $cmoney['money'];
        $result = array(
            'status' => FALSE,
            'msg' => '系统异常',
            'data' => ''
        );
        if($this->getWithdrawLog($uid))
        {
            $this->db->trans_rollback();
            $result = array(
                'status' => FALSE,
                'msg' => '获取提现记录失败',
                'data' => ''
            );
            return $result;
        }
        if($withdraw >= $money)
        {
            // 更新用户余额
            $sql = "update cp_user set money = money - $money where money >= $money and uid = ?";
            $re1 = $this->db->query($sql, $uid);
            $orderid = $this->tools->getIncNum('UNIQUE_KEY');
            $wallet_log = array(
                'uid' => $uid,
                'money' => $money,
                'ctype' => $this->ctype['apply_for_withdraw'],
                'trade_no' => $orderid,
                'umoney' => ($cmoney - $money),
                'channel' => !empty($extData['channel']) ? $extData['channel'] : 0,
                'app_version' => !empty($extData['version']) ? $extData['version'] : 0,
                'platform' => $platform
            );
            // 记录钱包流水
            $re2 = $this->db->query("insert {$this->tbname}(". implode(',', array_keys($wallet_log)) .', created)
			values('. implode(',', array_map(array($this, 'maps'), $wallet_log)) .', now())', $wallet_log);
            // 记录提款表
            $re3 = $this->db->query("insert cp_withdraw(uid, trade_no, money, umoney, additions, platform, app_version, channel, created)
					values (?, ?, ?, ?, ?, ?, ?, ?, now())", array($uid, $orderid, $money, ($cmoney - $money), $additions, $platform, $wallet_log['app_version'], $wallet_log['channel']));
            
            // 总账记录流水
            $this->load->model('capital_model');
            $re4 = $this->capital_model->recordCapitalLog('1', $orderid, 'withdraw', $money, '1', $tranc = FALSE);
            
            $re = $re1 && $re2 && $re3 && $re4;
            if($re)
            {
                $this->db->trans_complete();
                $result = array(
                    'status' => TRUE,
                    'msg' => '提现成功',
                    'data' => $orderid
                );
            }
            else
            {
                $this->db->trans_rollback();
                $result = array(
                    'status' => FALSE,
                    'msg' => '提现失败',
                    'data' => ''
                );
            }
        }
        else
        {
            $this->db->trans_rollback();
            $result = array(
                'status' => FALSE,
                'msg' => '提现金额不足',
                'data' => ''
            );
        }
        if($result['status'])	$this->freshWallet($uid);
        return $result;
    }
    
    //获得提款记录
    public function getWithdrawLog($uid)
    {
        $count = $this->db->query("SELECT count(*) FROM cp_withdraw
            WHERE status != {$this->status['withdraw_fail']} AND uid = ? AND created >= date(now()) && created < date(date_add(now(), INTERVAL 1 DAY))",
            array($uid))->getOne();
            $privilege = $this->db->query("select l.privilege from cp_growth_level l inner join cp_user_growth g on l.grade= g.grade where g.uid = ?", array($uid))->getRow();
            $countLimit = 3;
            if($privilege)
            {
                $privilege = json_decode($privilege['privilege'], true);
                $countLimit = $privilege['withdraw'];
            }
            if($count >= $countLimit)
            {
                return true;
            }
            return false;
    }
    
    //获得可提资金
    public function getWithDraw($uid)
    {
        $subtract = "if((must_cost + dispatch) > chaseMoney, (must_cost + dispatch - chaseMoney), 0)";
        return $this->db->query("SELECT if(money >= $subtract, money - $subtract, 0) FROM cp_user WHERE uid = ? FOR UPDATE",
            array($uid))->getOne();
    }
    
    //获得余额
    public function getMoney($uid)
    {
        return $this->db->query('SELECT money, blocked, must_cost, dispatch FROM cp_user WHERE uid = ? FOR UPDATE',
            array($uid))->getRow();
    }
    
    //刷新钱包
    public function freshWallet($uid)
    {
        $REDIS = $this->config->item('REDIS');
        $ukey = "{$REDIS['USER_INFO']}$uid";
        $this->load->driver('cache', array('adapter' => 'redis'));
        $uinfo = $this->cache->redis->hGet($ukey, "uname");
        if(empty($uinfo))
        {
            $this->load->model('user_model');
            $this->user_model->freshUserInfo($uid);
            return true;
        }
        else
        {
            $wallet = $this->db->query('SELECT money, blocked, dispatch FROM cp_user WHERE uid = ?', array($uid))->getRow();
            return $this->cache->redis->hMSet($ukey, $wallet);
        }
    }
}