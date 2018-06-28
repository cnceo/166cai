<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/*
 * 总账流水
 * @date:2016-05-12
 */
class Capital_Model extends MY_Model
{
	public function __construct() 
	{
		parent::__construct();
	}

	public $ctype = array(
        'withdraw'                   => 1, // 提款
        'withdraw_succ'              => 2, // 提款成功
        'withdraw_fail'              => 3, // 提款失败
        'pay'                        => 4, // 购彩支出
		'ticket_fail'                => 5, // 出票失败
		'rebate'					 => 6, // 购彩返点
        'repaid'                     => 7, // 不中包赔	
        'plus_awards'                => 8, // 竞彩加奖
        'redpack'                    => 9, // 红包支出
        'orthers'                    => 10, // 其他后台调账
        'united_refund'              => 11, // 用户合买保底退款
        'united_awards'              => 12, // 网站合买保底派奖
        'united_web_refund'          => 13, // 网站合买保底退款
        'united_pay'                 => 14, // 网站合买订单支出
    );

    /*
     * 流水记录
     * @date:2016-05-12
     */
	public function recordCapitalLog($capital_id, $trade_no, $ctype, $money, $status, $tranc = TRUE, $redpack = array())
    {
        if($tranc)
        {
            $this->db->trans_start();
        }
        //红包数组必须包含这四个参数
// 		$redpack = array(
// 			'capitalId' => '2', //总账表id
// 			'ctype' => 'redpack', //流水表类型
// 			'money' => 100, //金额
// 			'status' => 1,	//状态
// 		);
		if($redpack)
		{
			$con = ($status == '1')?'+':'-';
			$rCon = $redpack['status'] == '1' ? '+' : '-';
			$cMoney = $money + $redpack['money'];
			// 账户加减
			//$res1 = $this->db->query("update cp_capital set money = money {$con} $cMoney where id = ?", array($capital_id));
			//$res2 = $this->db->query("update cp_capital set money = money {$rCon} {$redpack['money']} where id = ?", array($redpack['capitalId']));
			$res1 = true;
			$res2 = true;
			// 记录流水
			$res3 = $this->db->query("insert cp_capital_log(capital_id, trade_no, ctype, money, status, created) values (?, ?, ?, ?, ?, now())",
			array($capital_id, $trade_no, $this->ctype[$ctype], $cMoney, $status));
			$res4 = $this->db->query("insert cp_capital_log(capital_id, trade_no, ctype, money, status, created) values (?, ?, ?, ?, ?, now())",
			array($redpack['capitalId'], $trade_no, $this->ctype[$redpack['ctype']], $redpack['money'], $redpack['status']));
			$res = $res1 && $res2 && $res3 && $res4;
		}
		else
		{
			$con = ($status == '1')?'+':'-';
			// 账户加减
			//$res1 = $this->db->query("update cp_capital set money = money {$con} $money where id = ?", array($capital_id));
			$res1 = true;
			// 记录流水
			$res2 = $this->db->query("insert cp_capital_log(capital_id, trade_no, ctype, money, status, created) values (?, ?, ?, ?, ?, now())",
			array($capital_id, $trade_no, $this->ctype[$ctype], $money, $status));
			$res = $res1 && $res2;
		}
        
        if($res)
        {
            if($tranc)
            {
                $this->db->trans_complete();
            }
            return TRUE;
        }
        else
        {
            if($tranc)
            {
                $this->db->trans_rollback();
            }
            return FALSE;
        }
    }

}
