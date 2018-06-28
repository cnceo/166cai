<?php

/**
 * Copyright (c) 2014,上海二三四五网络科技股份有限公司
 * 摘    要：基准后台MODEL
 * 作    者：wangl@2345.com
 * 修改日期：2014.11.06
 */
defined('BASEPATH') OR exit('No direct script access allowed');

class MY_Model extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
        $this->db_config = $this->config->item('db_config');
    }

    /**
     * 参    数：无
     * 作    者：wangl
     * 功    能：获取数据表
     * 修改日期：2014.11.05
     */
    public function get_db()
    {
        $tbs = array(
            "cp_user"       => "cp_user",
            "cp_orders"     => "cp_orders",
            "user_capacity" => "user_capacity",
            "cp_w_l"        => "cp_wallet_logs",
            "cp_money"      => "cp_momey",
            "cp_notice"     => "cp_notice",
            "cp_u_i"        => "cp_user_info",
            "cp_l_i"        => "cp_login_info",
            "cp_winning"    => "cp_winning",
            "cp_withdraw"   => "cp_withdraw",
            "logs"          => "logs",
            "tongji"        => "user_tongji",
            "sms"           => "cp_sms_logs",
            "cp_op_user"    => "cp_operation_user",
            "cp_op_server"  => "cp_operation_server",
            "cp_ch_orders"  => "cp_chase_orders",
            "cp_ch_cancel"  => "cp_chase_cancel",
            "cp_p_s"        => "cp_partner_shop",
            "cp_file"       => "cp_partner_shop_file",
            "cp_u_b"        => "cp_user_bank",
        	"cp_sy_img"     => "cp_shouye_img",
        	"cp_sy_ln"      => "cp_shouye_link",
        	"cp_relationship" => "cp_relationship",
        	"cp_bn"			=> "cp_banner",
        );
        foreach ($tbs as $key => $value)
        {
            $this->$key = $value;
        }
    }
    
    protected function maps($v)
    {
    	return '?';
    }

    /**
     * 参    数：无
     * 作    者：wangl
     * 功    能：判断是否不为空
     * 修改日期：2014.11.05
     */
    public function emp($str)
    {
        return isset($str) && trim($str) !== "";
    }

    /**
     * 参    数：无
     * 作    者：wangl
     * 功    能：判断是否为空
     * 修改日期：2014.11.05
     */
    public function nemp($str)
    {
        return ! isset($str) || trim($str) == "";
    }

    /**
     * 参    数：无
     * 作    者：wangl
     * 功    能：获取用户信息
     * 修改日期：2014.11.05
     */
    public function get_user_info($uid, $key = array())
    {
    	$sql = "select uname, passid, pword, safe_grade, money, money_hide, blocked, email, last_login_time, visit_times, rebates_level,platform,m.created,
    	n.real_name, uname as nick_name, n.nick_name_modify_time, n.phone, n.id_card, n.bank_id, n.gender, n.qq, n.province, n.city, n.pay_pwd,
    	n.bank_province, n.bank_city, n.msg_send, n.push_status, n.app_push, n.userStatus
    	from cp_user m
    	left join cp_user_info n on m.uid = n.uid where m.uid = ?";
    	$userInfo = $this->BcdDb->query($sql, array($uid))->getRow();
        if (isset($userInfo['bank_id']))
        {
            $bank_info = explode("|", $userInfo['bank_id']);
            $userInfo['bank_id'] = $bank_info[0];
            $userInfo['bank_name'] = $bank_info[1];
        }

        return $userInfo;
    }

    /**
     * 参    数：无
     * 作    者：wangl
     * 功    能：赋值
     * 修改日期：2014.11.05
     */
    public function parse_result($result, $options = array())
    {
        $default = array(
            "real_name",
            "bank_id",
            "uname"
        );
        if ( ! empty($options))
        {
            $default = array_merge($default, $options);
        }

        foreach ($result as $key => $value)
        {
            $userInfo = $this->get_user_info($value['uid'], $default);
            $pay_info = explode("@", $value['additions']);
            if ($pay_info[2])
            {
                $result[$key]['pay_type'] = $pay_info[0];
                $result[$key]['bank'] = empty($pay_info[1]) ? "default" : $pay_info[1];
                $result[$key]['bank_id'] = $pay_info[2];
                $result[$key]['bank_name'] = $pay_info[1];
                $result[$key]['real_name'] = $userInfo['real_name'];
                $result[$key]['uname'] = $userInfo['uname'];
            }
            else
            {
                $result[$key]['pay_type'] = $pay_info[0];
                $result[$key]['bank'] = empty($pay_info[1]) ? "default" : $pay_info[1];
                $result[$key]['bank_id'] = $userInfo['bank_id'];
                $result[$key]['bank_name'] = $userInfo['bank_name'];
                $result[$key]['real_name'] = $userInfo['real_name'];
                $result[$key]['uname'] = $userInfo['uname'];
            }
            if ( ! empty($options))
            {
                foreach ($options as $ovalue)
                {
                    $result[$key][$ovalue] = $userInfo[$ovalue];
                }
                $bankInfo = $this->get_user_bank($value['uid'], $result[$key]['bank_id'], $result[$key]['bank']);
                $result[$key]['bank_province'] = $bankInfo[0]['bank_province'];
                $result[$key]['bank_city'] = $bankInfo[0]['bank_city'];
            }
        }

        return $result;
    }

    /**
     * 参    数：
     *                $dkey 数据库字段
     *                $val 字段值
     *                $type查询类型
     *                $addtional 额外的值
     * 作    者：wangl
     * 功    能：主要是减少重复代码
     * 修改日期：2014.11.05
     */
    public function condition($dKey, $val, $type = "equal", $additional = '')
    {
        $where = '';
        switch ($type)
        {
            case "during": //时间跨度查询是参数为数组
                if ($this->emp($val[0]) || $this->emp($val[1]))
                {
                    if ($this->nemp($val[0]))
                    {
                        $where = " and {$dKey} = '" . (($additional == 'm') ? $val[1] * 100 : $val[1]) . "'";
                    }
                    elseif ($this->nemp($val[1]))
                    {
                        $where = " and {$dKey} = '" . (($additional == 'm') ? $val[0] * 100 : $val[0]) . "'";
                    }
                    else
                    {
                        $where = " and {$dKey} >= '" . (($additional == 'm') ? $val[0] * 100 : $val[0]) . "' and {$dKey} <= '" . (($additional == 'm') ? $val[1] * 100 : $val[1]) . "'";
                    }
                }

                break;
            case "time": //时间跨度查询是参数为数组
                if ($this->emp($val[0]) || $this->emp($val[1]))
                {
                    if ($this->nemp($val[0]))
                    {
                        $nowTime = is_int($val[1]) ? strtotime('-3 months', $val[1]) : date("Y-m-d H:i:s",
                            strtotime('-3 months', $val[1]));
                        $where = " and {$dKey} >= '" . $nowTime . "' and {$dKey} <= '" . $val[1] . "'";
                    }
                    elseif ($this->nemp($val[1]))
                    {
                        $nowTime = is_int($val[0]) ? strtotime('+ 3 months', $val[0]) : date("Y-m-d H:i:s",
                            strtotime('+ 3 months', $val[0]));
                        $where = " and {$dKey} >= '" . $val[0] . "' and {$dKey} <= '" . $nowTime . "'";
                    }
                    else
                    {
                        $where = " and {$dKey} >= '" . $val[0] . "' and {$dKey} <= '" . $val[1] . "'";
                    }
                }
                break;
            case "datetime": //时间跨度查询是参数为数组
                if ($this->emp($val[0]) || $this->emp($val[1]))
                {
                    if ($this->nemp($val[0]))
                    {
                        $where = " and {$dKey} <= '" . $val[1] . "'";
                    }
                    elseif ($this->nemp($val[1]))
                    {
                        $where = " and {$dKey} >= '" . $val[0]."'";
                    }
                    else
                    {
                        $where = " and {$dKey} >= '" . $val[0] . "' and {$dKey} <= '" . $val[1] . "'";
                    }
                }
                break;  
            case "date": //时间跨度查询是参数为数组
                if ($this->emp($val[0]) || $this->emp($val[1]))
                {
                    if ($this->nemp($val[0]))
                    {
                        $where = " and {$dKey} <= '" .date("Y-m-d",strtotime($val[1]))  . "'";
                    }
                    elseif ($this->nemp($val[1]))
                    {
                        $where = " and {$dKey} >= '" . date("Y-m-d",strtotime($val[0]))."'";
                    }
                    else
                    {
                        $where = " and {$dKey} >= '" . date("Y-m-d",strtotime($val[0])) . "' and {$dKey} <= '" . date("Y-m-d",strtotime($val[1])) . "'";
                    }
                }
                break;   
            case "equal":
                if ($this->emp($val))
                {
                    $where = " and {$dKey} = '{$val}'";
                }
                break;
            case "like":
                if ($this->emp($val))
                {
                    $where = " and {$dKey} like '%{$val}%'";
                }
                break;
            case "likeRight":
                if ($this->emp($val))
                {
                    $where = " and {$dKey} like '{$val}%'";
                }
                break;
            case "choose":
                if ($this->emp($val[0]) || $this->emp($val[1]))
                {
                    if ($this->nemp($val[0]))
                    {
                        $where = " and {$dKey} = '{$additional[0]}'";
                    }
                    elseif ($this->nemp($val[1]))
                    {
                        $where = " and {$dKey} = '{$additional[1]}'";
                    }
                }
                break;
            default:
                break;
        }

        return $where;

    }


    /**
     * 参    数：无
     * 作    者：liuli
     * 功    能：获取用户银行卡信息
     * 修改日期：2015.03.12
     */
    public function get_user_bank($uid, $bank_id, $bank_type)
    {
    	$REDIS = $this->config->item('REDIS');
    	$ukey = "{$REDIS['BANK_INFO']}$uid";
    	$this->load->driver('cache', array('adapter' => 'redis'));
    	$binfo = unserialize($this->cache->redis->get($ukey));
    	$result = array();
    	if($binfo)
    	{
    		foreach ($binfo as $val)
    		{
    			if(($bank_id == $val['bank_id']) && ($bank_type == $val['bank_type']))
    			{
    				$result[] = $val;
    			}
    		}
    	}
    	if(empty($result))
    	{
    		$select = "SELECT uid, bank_id, bank_type, bank_province, bank_city FROM  cp_user_bank
    		WHERE uid = ?
    		AND bank_id = ?
    		AND bank_type = ?
    		ORDER BY modified DESC LIMIT 1";
    		$result = $this->BcdDb->query($select, array($uid, $bank_id, $bank_type))->row_array();
    	}
    	
        return $result;
    }

    protected function onduplicate($fields, $upd, $apd = array(), $_ = '')
    {
        $tail = array();
        foreach ($fields as $field)
        {
            if (in_array($field, $upd))
            {
                if (in_array($field, $apd))
                {
                    array_push($tail, "$field = $_$field + values($field)");
                }
                else
                {
                    if (in_array($field, array('status')))
                    {
                        array_push($tail, "$field = if($field < values($field), values($field), $field)");
                    }
                    else
                    {
                        array_push($tail, "$field = values($field)");
                    }
                }
            }
        }
        if ( ! empty($tail))
        {
            return " on duplicate key update " . implode(', ', $tail);
        }
    }

    public function decideMonthTable($name, $date)
    {
        $table = $name;
        if (strtotime($date) < strtotime('30 days ago midnight'))
        {
            $table .= '_' . substr($date, 0, 4) . substr($date, 5, 2);
        }

        return $table;
    }

    public function composeSelectStr($selectMap)
    {
        if (empty($selectMap))
        {
            return '';
        }
        $selectAry = array();
        foreach ($selectMap as $key => $value)
        {
            array_push($selectAry, "$key AS $value");
        }
        $selectStr = implode(', ', $selectAry);

        return $selectStr;
    }

    public function showTableCluster($db, $tableName)
    {
        $sql = "SHOW TABLES LIKE '{$tableName}_201%'";
        $tables = $this->$db->query($sql)->getCol();
        $cluster = array_merge(array($tableName), $tables);

        return $cluster;
    }

    public function orderConfig($cfg)
    {
        $this->load->config('order');

        return $this->config->item("cfg_$cfg");
    }
    
    protected function getSplitTable($lid)
    {
    	$splitlid = $this->config->item('split_lid');
    	$tables = array();
    	if(in_array($lid, $splitlid))
    	{
    		$lidmap = $this->orderConfig('lidmap');
    		$tables['split_table'] = "cp_orders_split_{$lidmap[$lid]}";
    		$tables['relation_table'] = "cp_orders_relation_{$lidmap[$lid]}";
    	}
    	else
    	{
    		$tables['split_table'] = "cp_orders_split";
    		$tables['relation_table'] = "cp_orders_relation";
    	}
    	return $tables;
    }
}
