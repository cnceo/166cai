<?php
class Model_pay_config extends MY_Model
{
	
	public function __construct()
	{
		parent::__construct();
		$this->get_db();
	}
	
	public function getListByPlatform($platform) 
	{
		$sql = "select id, ctype, pay_type, rate,status_mark, mer_id, status, weight from cp_pay_config where platform = ? order by ctype, weight desc";
		$res = $this->BcdDb->query($sql, array($platform))->getAll();
		$idstr = '';
		foreach ($res as $val) 
		{
			$idstr .= ",".$val['id'];
			$data[$val['ctype']][] = $val;
		}
		$idstr = substr($idstr, 1);
		return compact('data', 'idstr');
	}
	
	public function updateStatus($platform, $condition, $status)
	{
		$where = ' where 1 and platform = ?';
		$data = array($status, $platform);
		foreach ($condition as $key => $val) 
		{
			$where .= " and ".$key." in ($val)";
			$data[] = $val;
		}
		$sql = "update cp_pay_config set status = ? {$where}";
		$this->master->query($sql, $data);
	}
	
	public function updateRate($platform, $ctype, $rate)
	{
            $rates = array();
            foreach ($rate as $k => $v) {
                $rates[] = '(' . $k . ',' . $v . ')';
            }
            $sql = "insert into cp_pay_config (id,rate) values " . implode(',', $rates) . "  on duplicate key update rate=values(rate);";
            $this->master->query($sql);
	}
	
	public function updateWeight($platform, $ctype, $weight)
	{
            foreach ($weight as $k => $v) {
            switch ($k) {
                case 'weixin':
                    $sql = "update cp_pay_config set weight = ? where ctype = ? and platform = ?";
                    $data = array($v, 2, $platform);
                    break;
                case 'weixinsaoma':
                    $sql = "update cp_pay_config set weight = ? where ctype = ? and platform = ?";
                    $data = array($v, 3, $platform);
                    break;
                case 'zhifubao':
                    $sql = "update cp_pay_config set weight = ? where ctype = ? and platform = ?";
                    $data = array($v, 4, $platform);
                    break;
                case 'jingdong':
                    $sql = "update cp_pay_config set weight = ? where ctype = ? and platform = ?";
                    $data = array($v, 8, $platform);
                    break;
                case "1":
                    $ctype = 1;
                    $sql = "update cp_pay_config set weight = ? where pay_type = ? and ctype = ? and platform = ?";
                    $data = array($v, $k, $ctype, $platform);
                    break;
                case '1_1':
                    $sql = "update cp_pay_config set pcweight = ? where ctype = ? and platform = ?";
                    $data = array($v, 1, $platform);
                    break;
                case '1_3':
                    $sql = "update cp_pay_config set pcweight = ? where ctype = ? and platform = ?";
                    $data = array($v, 3, $platform);
                    break;
                case '1_4':
                    $sql = "update cp_pay_config set pcweight = ? where ctype = ? and platform = ?";
                    $data = array($v, 4, $platform);
                    break;
                case '1_5':
                    $sql = "update cp_pay_config set pcweight = ? where ctype = ? and platform = ?";
                    $data = array($v, 5, $platform);
                    break;
                case '1_7':
                    $sql = "update cp_pay_config set pcweight = ? where ctype = ? and platform = ?";
                    $data = array($v, 7, $platform);
                    break;
                case '1_8':
                    $sql = "update cp_pay_config set pcweight = ? where ctype = ? and platform = ?";
                    $data = array($v, 8, $platform);
                    break;
                case 25:
                    $ctype = 7;
                    $sql = "update cp_pay_config set weight = ? where pay_type = ? and ctype = ? and platform = ?";
                    $data = array($v, $k, $ctype, $platform);
                    break;
                default :
                    $ctype = 1;
                    $sql = "update cp_pay_config set weight = ? where pay_type = ? and ctype = ? and platform = ?";
                    $data = array($v, $k, $ctype, $platform);
                    break;
            }
            $this->master->query($sql, $data);
        }
    }
	//更新商户号备注
	public function updateMark($mark)
	{
		$idArr = array();
		foreach ($mark as  $v) 
		{
			$sql = "update cp_pay_config set status_mark = ? where id = ?";
			array_push($idArr, $v[0]);
			$this->master->query($sql, array($v[1], $v[0]));
		}
		return $this->master->query('select platform, ctype, pay_type, mer_id, status_mark from cp_pay_config where id in ?', array($idArr))->getAll();
	}
	public function getCtypeByPlatform($platform) {
		$sql = "select distinct ctype, CASE WHEN ctype = 1 THEN pay_type ELSE 0 END as pay_type 
				from cp_pay_config where platform = ? and status=0 order by weight desc,field (ctype, 6, 2, 4, 1, 3)";
		$res = $this->master->query($sql, array($platform))->getAll();
		$data = array();
		$count = 0;
		foreach ($res as $val) {
			if ($val['ctype'] == 1) {
				if ($count >= 3) continue;
				$count++;
			}
			array_push($data, $val['ctype']."_".$val['pay_type']);
		}
		return $data;
	}
	
	public function getMoney($start, $end, $idstr)
	{
		$data = array();
		if (!empty($idstr)) {
			$sql = "select sum(money) as money, rcg_serial from cp_pay_logs where status=1 and created >= '{$start}' and created <= '$end' and rcg_serial in ($idstr) group by rcg_serial";
			$res = $this->BcdDb->query($sql)->getAll();
			foreach ($res as $val) {
				$data[$val['rcg_serial']] = $val['money'];
			}
		}
		return $data;
	}
	/**
	 * [getPayConfig 获取支付配置信息]
	 * @author LiKangJian 2017-12-20
	 * @return [type] [description]
	 */
	public function getPayConfig()
	{
		$sql = "SELECT platform,pay_type,ctype FROM `cp_pay_config` where pay_type in(5,8,9,10,11,12,15,16,17,18,19,20,21,22,24,23,28,29,31,33,34,37) ORDER BY ctype asc;";
		return $this->BcdDb->query($sql)->getAll();
	}
	/**
	 * [insertPayConfig 插入新的商户号]
	 * @author LiKangJian 2017-12-20
	 * @param  [type] $params [description]
	 * @return [type]         [description]
	 */
    public function insertPayConfig($params)
    {
    	if($params['id'])
    	{
	    	$sql = "update `cp_pay_config` set platform=?,ctype=?,pay_type=?,extra=?,params=?, mark=?,mer_id=? where id = ?";
	    	return $this->master->query($sql,array($params['platform'],$params['ctype'],$params['pay_type'],str_replace('#id#',$params['id'], $params['extra']),$params['params'],$params['mark'],$params['mer_id'],$params['id']));
    	}else{
	    	$this->master->trans_start();
	    	$sql = "insert into `cp_pay_config`(platform,ctype,pay_type,extra,params, mark,mer_id,is_add,created) values (?,?,?,?,?,?,?,1,now())";
	    	$tag = $this->master->query($sql,array($params['platform'],$params['ctype'],$params['pay_type'],$params['extra'],$params['params'],$params['mark'],$params['mer_id']));
	        $sql = 'update `cp_pay_config` set extra =? where id = ?';
	        $tag1 = $this->master->query($sql,array(str_replace('#id#',$this->master->insert_id() , $params['extra']),$this->master->insert_id()));
	        if($tag1 && $tag)
	        {
				$this->master->trans_complete();
				return true;
	        }else{
	        	$this->master->trans_rollback();
	        	return false;
	        }    		
    	}

    }
    /**
     * [verifyPayConfig 验证是否重复]
     * @author LiKangJian 2018-01-29
     * @param  [type] $params [description]
     * @return [type]         [description]
     */
    public function verifyPayConfig($params)
    {
    	$sql = "select * from `cp_pay_config` where platform = ? and ctype = ? and pay_type = ? and mer_id =? ";
    	$row = $this->master->query($sql,array($params['platform'],$params['ctype'],$params['pay_type'],$params['mer_id']))->getRow();
    	return $row ? false : true;
    }
    /**
     * [getPayAddList 获取可以删除的商户号列表]
     * @author LiKangJian 2017-12-20
     * @param  [type] $searchData [description]
     * @param  [type] $page       [description]
     * @param  [type] $pageCount  [description]
     * @return [type]             [description]
     */
    public function getPayAddList($searchData, $page, $pageCount)
    {
        $where = " where is_add =1  ";
        if(!empty($searchData['platform']))
        {
            $where .=" and platform ='{$searchData['platform']}' ";
        }
        if($searchData['ctype']!='')
        {
            $where .=" and ctype = '{$searchData['ctype']}' ";
        }        
        if(!empty($searchData['pay_type']))
        {
            $where .=" and pay_type = '{$searchData['pay_type']}' ";   
        } 
    	$sql = "select * from cp_pay_config {$where} order by id desc LIMIT " . ($page - 1) * $pageCount . "," . $pageCount;;
    	$totalSql = "select count(*) as count from cp_pay_config {$where}";
    	$result = $this->BcdDb->query($sql)->getAll();
    	$count = $this->BcdDb->query($totalSql)->getRow();
    	return array(
            $result,
            $count['count']
        );
    }
    /**
     * [delPayConfig 删除商户号]
     * @author LiKangJian 2017-12-20
     * @param  [type] $id [description]
     * @return [type]     [description]
     */
    public function delPayConfig($id)
    {
    	return $this->master->query('delete from cp_pay_config where id = ? and is_add =1',array($id));
    }
    
    public function getFreshPayConfig()
    {
        $sql = "select fresh_payconfig from cp_fresh_payconfig where 1 limit 1";
        return $this->db->query($sql)->getRow();           
    }
    
    public function updateFreshPayConfig($status)
    {
        $sql = "update cp_fresh_payconfig set fresh_payconfig=? where 1";
        return $this->db->query($sql, array($status));
    }

    public function getPcWeight()
    {
        $sql = "select ctype,pcweight,guide from cp_pay_config where platform = 1 group by ctype order by pcweight desc";
        return $this->db->query($sql)->getAll();           
    }
    
    public function getPcWeightSort()
    {
        $sql = "select ctype,pcweight,guide from cp_pay_config where platform = 1 group by ctype order by pcweight desc";
        $res = $this->master->query($sql)->getAll();
        $data = array();
        $guides = array();
        foreach ($res as $val) {
            array_push($data, "1_" . $val['ctype']);
            if ($val['ctype'] == 5) {
                array_push($data, "1_6");
                $guides["1_6"] = $val['guide'];
            }
            $guides["1_" . $val['ctype']] = $val['guide'];
        }
        return array($data, $guides);
    }
    
    public function updateGuide($platform, $guide)
    {
        foreach ($guide as $k => $v) {
            $ctype = explode('_', $k);
            $sql = "update cp_pay_config set guide = ? where ctype = ? and platform = ?";
            $data = array($v, $ctype[1], $platform);
            $this->master->query($sql, $data);
        }
    }

    public function getRateConfig($platform, $ctype = array())
    {
        $sql = "SELECT id, ctype, pay_type, mer_id, rate, params, weight FROM cp_pay_config WHERE platform = ? AND ctype IN ? AND status = 0 AND rate > 0";
        return $this->master->query($sql, array($platform, $ctype))->getAll();
    }
}