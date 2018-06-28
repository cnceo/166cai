<?php

/**
 * Copyright (c) 2014,上海二三四五网络科技股份有限公司
 * 摘    要：投注站外部系统后台model
 * 作    者：xumw@2345.com
 * 修改日期：2016.1.26
 */
defined ( 'BASEPATH' ) or exit ( 'No direct script access allowed' );
class MY_Model extends CI_Model
{
	protected $_table;

	public function __construct()
	{
		$this->load->database ();
	}

	/**
	 * 对单张表的新增、根据ID修改操作
	 * 
	 * @param
	 *        	array 需要修改的字段、值 $data
	 * @param int $id        	
	 */
	public function save($data, $where = null)
	{
		if ($where)
		{
			$this->db->update ( $this->_table, $data, $where );
		} else
		{
			$data ['created'] = date ( 'Y-m-d H:i:s' );
			$this->db->insert ( $this->_table, $data );
			return $this->db->insert_id ();
		}
	}

	/**
	 * 常规根据ID删除操作
	 * 
	 * @param int $id        	
	 */
	public function delById($id)
	{
		$this->db->delete ( $this->_table, array (
				'id' => $id 
		) );
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
	
}
