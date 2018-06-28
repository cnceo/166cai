<?php

class Syncr_Model extends MY_Model
{

    public function __construct()
    {
        parent::__construct();
    }

    private function webInstance()
    {
        $config =& get_config();
        return empty($config['web_instance']) ? 1 : $config['web_instance'];
    }

    //直接拉取源表中的数据
    public function syncr_start($cfg)
    {
        $webInstance = $this->webInstance();
        $ssql = "SELECT " . implode(', ', $cfg['fld']) . "
            FROM {$cfg['stb']}
            WHERE {$cfg['cdt']} :cdt:
            ORDER BY m.{$cfg['odr']}
            LIMIT 5000";
        $ssqlt = str_replace(':cdt:', '', $ssql);
        $sdatas = $cfg['sdh']->query($ssqlt)->getAll();
        while ( ! empty($sdatas))
        {
            $s_data = array();
            $d_data = array();
            $cdts = array();
            foreach ($sdatas as $sdata)
            {
                array_push($cdts, $sdata[$cfg['odr']]);
                if (in_array($cfg['odr'], array('id')))
                {
                    unset($sdata[$cfg['odr']]);
                }
                if (empty($fields))
                {
                    $fields = array_keys($sdata);
                }
                array_push($s_data, '(' . implode(',', array_map(array($this, 'maps'), $fields)) . ')');
                if ( ! empty($sdata['created']))
                {
                    $sdata['created'] = date('Y-m-d H:i:s');
                }
                $d_data = $this->marge_arr($d_data, $sdata);
            }
            $dsql = "INSERT :ignore: {$cfg['dtb']}(" . implode(',', $fields) . ") VALUES";
            $dsql .= implode(',', $s_data);
            if ( ! empty($cfg['ups']))
            {
                //最后添加一个防止状态回滚数组
                //提前派奖需要状态回退操作 2017.10.09
                $dsql .= $this->onduplicate($fields, $cfg['ups'], array(), '',
                    array('rjstatus', 'rjrstatus'));
                $dsql = str_replace(':ignore:', '', $dsql);
            }
            else
            {
                $dsql = str_replace(':ignore:', 'ignore', $dsql);
            }
            $re = $cfg['ddh']->query($dsql, $d_data);
            if ($re && ! empty($cfg['sfg']))
            {
            	$newSynFlag = 1;
            	if($cfg['bit'] == 3){
                    $newSynFlag = $cfg['sfg'] . ' ^ ' . $cfg['bitval'];
                }
            	elseif($cfg['bit'] == 2)
            	{
            		$newSynFlag = "if({$cfg['sfg']} > 1, {$cfg['sfg']} >> 1, {$cfg['sfg']})";
            	}
            	elseif($cfg['bit'] == 1)
            	{
            		$newSynFlag = $cfg['sfg'] . ' ^ ' . $webInstance;
            	}
            	//更新已同步记录标识
                $sflg = "UPDATE {$cfg['ftb']} m
                SET {$cfg['sfg']} = $newSynFlag
                WHERE {$cfg['odr']}
                IN('" . implode("','", $cdts) . "') :cfg:";
                $cfgstr = '';
                if ( ! empty($cfg['cfg']))
                {
                    $cfgstr = " AND {$cfg['cfg']}";
                }
                $sflg = str_replace(':cfg:', $cfgstr, $sflg);
                $cfg['sdh']->query($sflg);
                if ( ! empty($cfg['extra']))
                {
                    $cfg['ddh']->query("UPDATE {$cfg['dtb']}
                        SET {$cfg['extra']} = 0
                        WHERE {$cfg['odr']}
                        IN('" . implode("','", $cdts) . "')");
                }
            }
            $cdt = $cdts[count($cdts) - 1];
            //按顺序递增式同步关闭设置
            if($cfg['inc_syn_close']){
                $cdt = "";
            }else{
                $cdt = " AND m.{$cfg['odr']} > $cdt ";
            }
            $ssqlt = str_replace(':cdt:', $cdt, $ssql);
            $sdatas = $cfg['sdh']->query($ssqlt)->getAll();
        }
    }

    public function syncr_delete(){
        $sdatas = $cfg['sdh']->query($ssqlt)->getAll();
    }

    //依据目标表的某些字段更行目标表
    public function syncr_bs_start($cfg)
    {
        $check_sql = "select count(*) from {$cfg['dtb']} where {$cfg['cdt']}";
        $check_num = $cfg['ddh']->query($check_sql)->getOne();
        if ($check_num > 0)
        {
            $stb = str_replace($this->db_config['dc'], $this->db_config['tmp'], $cfg['stb']);
            $tmptable = "{$stb}_temp";
            $cfg['sdh']->query("create table if not exists $tmptable like {$cfg['stb']}");
            $bs_cfg = array(
                'sdh' => $cfg['ddh'],
                'ddh' => $cfg['sdh'],
                'stb' => $cfg['dtb'],
                'dtb' => $tmptable,
                'fld' => $cfg['bsf'],
                'upf' => array(),
                'odr' => $cfg['odr'],
                'cdt' => $cfg['cdt'],
            );
            $this->syncr_start($bs_cfg);

            $jcons = $this->two_tb_fld($cfg['bsf']);
            $upsql = "update $tmptable m join {$cfg['stb']} n on ";
            $upsql .= implode(' and ', $jcons);
            $setflds = $this->two_tb_fld($cfg['sef']);
            $upsql .= " set " . implode(', ', $setflds);
            $cfg['sdh']->query($upsql);

            $sc_cfg = array(
                'sdh' => $cfg['sdh'],
                'ddh' => $cfg['ddh'],
                'stb' => "$tmptable m",
                'dtb' => preg_replace('/m$/is', '', $cfg['dtb']),
                'fld' => $this->marge_arr($cfg['bsf'], $cfg['sef']),
                'ups' => $cfg['sef'],
                'cdt' => '1',
                'odr' => 'id',
            );
            $this->syncr_start($sc_cfg);
            $cfg['sdh']->query("truncate $tmptable");
        }
    }

    //生成源数据表和临时表的联合条件
    private function two_tb_fld($flds)
    {
        $jcons = array();
        foreach ($flds as $jcon)
        {
            if (in_array($jcon, array('id')))
            {
                continue;
            }
            array_push($jcons, "m.$jcon = n.$jcon");
        }

        return $jcons;
    }

    //合并两个数组
    private function marge_arr($darr, $sarr)
    {
        if ( ! empty($sarr))
        {
            foreach ($sarr as $val)
            {
                array_push($darr, $val);
            }
        }

        return $darr;
    }

}
