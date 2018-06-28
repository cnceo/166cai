<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * 彩票O2O投注站 模型层
 */

class Betstation_Model extends MY_Model
{
	/*
     * 查询所有投注站信息
     * @date:2016-1-19
     */
	public function getBetShopInfo($condition)
	{
		$sql = "SELECT id, partnerId, partner_name, shopNum, cname, lottery_type, phone, 
		qq, webchat, other_contact, address, fail_reason, off_reason, 
		delete_flag, status, created 
		FROM cp_partner_shop
		WHERE ";

        // 查询条件
        if (is_array($condition)) 
        {
            $len = count($condition);
            $i = 0;
            foreach ($condition as $k => $v) 
            {
                $sql .= $k . '=' . $v;
                $i = $i + 1;
                if ($i < $len) 
                {
                    $sql .= " and ";
                }
            }
        } 
        elseif(is_string($condition)) 
        {
            $sql .= $condition;
        }

		return $this->slave->query($sql)->getAll();
	}

    /*
     * 查询指定ID投注站信息
     * @date:2016-1-19
     */
    public function getBetShopDetail($shopId)
    {
        $sql = "SELECT id, partnerId, partner_name, shopNum, cname, lottery_type, phone, 
        qq, webchat, other_contact, address, fail_reason, off_reason, 
        delete_flag, status, created 
        FROM cp_partner_shop
        WHERE id = ?";

        return $this->slave->query($sql, array($shopId))->getRow();
    }
}
