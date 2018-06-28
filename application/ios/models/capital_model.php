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
        'repaid'                     => 7, // 竞彩包赔	
    );

    /*
     * 流水记录
     * @date:2016-05-12
     */
	public function recordCapitalLog($capital_id, $trade_no, $ctype, $money, $status, $tranc = TRUE)
    {
        if($tranc)
        {
            $this->db->trans_start();
        }

        $con = ($status == '1')?'+':'-';

        // 账户加减
        //$res1 = $this->db->query("update cp_capital set money = money {$con} $money where id = ?", array($capital_id));
        $res1 = true;

        // 记录流水
        $res2 = $this->db->query("insert cp_capital_log(capital_id, trade_no, ctype, money, status, created) values (?, ?, ?, ?, ?, now())", 
        array($capital_id, $trade_no, $this->ctype[$ctype], $money, $status));

        $res = $res1 && $res2;
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
