<?php

/**
 * Copyright (c) 2017,上海快猫文化传媒有限公司.
 * 摘    要: 红包推送
 * 作    者: 李康建
 * 修改日期: 2017/05/26
 * 修改时间: 11:50
 */
class Redpack_Push_Model extends MY_Model
{
	
	public function __construct()
	{
		parent::__construct();
		$this->load->library('mipush');	
	}
	/**
	 * [validStartPush 红包生效推送 红包生效日的10:00 过期：红包生效日的20:00 valid_start]
	 * @author LiKangJian 2017-05-31
	 * @return [type] [content]
	 */
	public function  validStartPush()
	{
		$today = date('Y-m-d');
		$next_day = date("Y-m-d",strtotime("+1 day"));
		$page = 1000;
		$i = 0;
		do {
			$sql = "SELECT l.id,l.uid,l.valid_start,l.aid,l.valid_end,l.is_push,l.status,r.p_type,r.p_name,r.money,r.money_bar 
					FROM cp_redpack_log AS l
					LEFT JOIN cp_redpack AS r ON l.rid = r.id 
					LEFT JOIN cp_user_info AS i ON l.uid = i.uid 
					WHERE l.valid_start >= '$today' AND l.valid_start < '$next_day' AND  (l.is_push & 1) = 0 AND r.p_type in(2,3) AND l.status < 2 AND l.delete_flag = 0 AND (i.app_push & 4) = 0 AND l.aid=8 AND l.status>0 ORDER BY l.valid_start DESC limit $page";
			$resData = $this->db->query($sql)->getAll();
			$i = count($resData);
			$res = $this->processData($resData);
			if(empty($res)) return;
			foreach ($res as $k => $v) 
			{
				$pushData = array(
					'type'		=>	'redpack_use',
	                'uid'		=> 	$k,
	                'title'		=> 	"您有红包生效啦",
	                'content'	=> 	'',
	                'time_to_live'	=>	36000000
				);
				$cz = count($v[2]);
				$gc = count($v[3]);
				if( $gc + $cz == 1)
				{
					if($cz == 1) 
					{
						$pushData['content'] = '三军未动，粮草先行！您有1个'.($v[2][0]['money']/100).'元充值红包已生效，还不快使用，亿万大奖等您中>>';
					}else{
						$pushData['content'] = '三军未动，粮草先行！您有1个'.($v[3][0]['money']/100).'元购彩红包已生效，还不快使用，亿万大奖等您中>>';
					}
				}else{
					if($cz > 0 && $gc > 0 )
					{
						$pushData['content'] = '红包我出，您开心就好！您有'.$cz.'个充值红包（和'.$gc.'个购彩红包）已生效，快来使用吧，亿万大奖等你中>>';
					}else if($cz > 0 && $gc == 0 )
					{
						$pushData['content'] = '红包我出，您开心就好！您有'.$cz.'个充值红包已生效，快来使用吧，亿万大奖等你中>>';
					}else if($cz == 0 && $gc > 0 )
					{
						$pushData['content'] = '红包我出，您开心就好！您有'.$gc.'个购彩红包已生效，快来使用吧，亿万大奖等你中>>';
					}            
				}
				$this->mipush->index('user', $pushData);
			    //更新状态红包表记录
			    $update_data = array_merge($v[2],$v[3]);
			    foreach ($update_data as $k1 => $v1) 
			    {
			    	$tag1 = $this->db->query("UPDATE cp_redpack_log SET is_push = (is_push | 1) WHERE id = ?", array($v1['id']));
			    }	
			}
		} while ($i % $page == 0 && $i!=0);
	}
	/**
	 * [validEndPush 红包失效推送]
	 * @author LiKangJian 2017-05-31
	 * @return [type] [content]
	 */
	public function validEndPush($end_days = 3)
	{
		// 状态位映射
		$pushArr = array(
			'3'	=>	'2',	// 失效前3天
			'1'	=>	'4',	// 失效前1天
		);
		// 推送过期时间
		$liveTime = array(
			'3'	=>	10800000,	// 失效前3天
			'1'	=>	34200000,	// 失效前1天
		);

		$cstate = $pushArr[$end_days];
		if(empty($cstate))
		{
			return;
		}
		$leftDay = $end_days -1;
		$start_day =$leftDay ==0 ? date("Y-m-d") : date("Y-m-d",strtotime("+$leftDay day"));
		$end_day = date('Y-m-d',strtotime($start_day)+60*60*24);
		$page = 1000;
		$i = 0;
		do{
			$sql = "SELECT l.uid,l.valid_start,l.valid_end,l.is_push,l.id,r.p_type,r.p_name,r.money,r.money_bar 
					FROM cp_redpack_log AS l
					LEFT JOIN cp_redpack AS r ON l.rid = r.id 
					LEFT JOIN cp_user_info AS i ON l.uid = i.uid 
					WHERE l.valid_end >= '$start_day' AND l.valid_end < '$end_day' AND l.valid_end != 0 AND (l.is_push & {$cstate}) = 0 AND l.status < 2 AND l.delete_flag = 0 AND (i.app_push & 4) = 0 limit $page";
			$res = $this->db->query($sql)->getAll();
			$i = count($res);
			$res = $this->processData($res);
			if(empty($res)) return;
			foreach ($res as $k => $v) 
			{
				$pushData = array(
					'type'		=>	'redpack_use',
	                'uid'		=> 	$k,
	                'title'		=> 	"红包到期提醒",
	                'content'	=> 	'',
	                'time_to_live'	=>	$liveTime[$end_days]
				);
				$cz  = count($v[2]);
				$gc  = count($v[3]);
				if( $gc + $cz == 1)
				{
					if($cz == 1) 
					{
						if($end_days==3)
						{
							$pushData['content'] = '您有1个'.($v[2][0]['money']/100).'元充值红包还有3天到期，请尽快使用噢>>';
						}else{
							$pushData['content'] = '您有1个'.($v[2][0]['money']/100).'元充值红包即将到期，再不使用追悔莫及噢>>';
						}

					}else{
						if($end_days==3)
						{   $pushData['content'] = '您有1个'.($v[3][0]['money']/100).'元购彩红包还有3天到期，请尽快使用噢>>';
						}else{
							$pushData['content'] = '您有1个'.($v[3][0]['money']/100).'元购彩红包即将到期，再不使用追悔莫及噢>>';
						}
					}
				}else{
					if($cz > 0 && $gc > 0 )
					{
						if($end_days==3)
						{
							$pushData['content'] = '您有'.$cz.'个充值红包（和'.$gc.'个购彩红包）还有3天到期，请尽快使用噢>>';
						}else{
							$pushData['content'] = '您有'.$cz.'个充值红包（和'.$gc.'个购彩红包）即将到期，再不使用追悔莫及噢>>';
						}
					}else if($cz > 0 && $gc == 0 )
					{
						if($end_days==3)
						{
							$pushData['content'] = '您有'.$cz.'个充值红包还有3天到期，请尽快使用噢>>';
						}else{
							$pushData['content'] = '您有'.$cz.'个充值红包即将到期，再不使用追悔莫及噢>>';
						}
					}else if($cz == 0 && $gc > 0 )
					{
						if($end_days==3)
						{
							$pushData['content'] = '您有'.$gc.'个购彩红包还有3天到期，请尽快使用噢>>';
						}else{
							$pushData['content'] = '您有'.$gc.'个购彩红包即将到期，再不使用追悔莫及噢>>';
						}
					}
				}
				$this->mipush->index('user', $pushData);
			    //更新状态红包表记录
			    $update_data = array_merge($v[2],$v[3]);
			    foreach ($update_data as $k1 => $v1) 
			    {
			    	$is_push = 2;
			    	if($end_days == 1) $is_push = 3;
			    	$tag1 = $this->db->query("UPDATE cp_redpack_log SET is_push = (is_push | {$cstate}) WHERE id = ?", array($v1['id']));
			    }
			}
		}while ($i % $page == 0 && $i!=0);

	}
	/**
	 * [processData 整理数据]
	 * @author LiKangJian 2017-05-31
	 * @param  [type] $data [content]
	 * @return [type]       [content]
	 */
	private function processData($data)
	{
		$new = array();
		foreach ($data as $k => $v) 
		{
			if(!isset($new[$v['uid']]))
			{
				$new[$v['uid']] = array('1'=>array(),'2'=>array(),'3'=>array());
			}
			if($v['p_type'] == 1){ $new[$v['uid']][1][] = $v ;}
			if($v['p_type'] == 2){ $new[$v['uid']][2][] = $v ;}
			if($v['p_type'] == 3){ $new[$v['uid']][3][] = $v ;}
		}
		return $new;
	}
        
    /**
     * 后台派发,推荐有礼,实名后推送
     */    
    public function redpackPush()
    {
        $today = date('Y-m-d');
        $next_day = date("Y-m-d",strtotime("+1 day"));
		$page = 1000;
		$i = 0;
		do{
            $sql = "SELECT l.id,l.uid,l.valid_start,l.aid,l.valid_end,l.is_push,l.status,r.p_type,r.p_name,r.money,r.money_bar 
                            FROM cp_redpack_log AS l
                            LEFT JOIN cp_redpack AS r ON l.rid = r.id 
                            LEFT JOIN cp_user_info AS i ON l.uid = i.uid 
                            WHERE l.valid_start >= '$today' AND l.valid_start < '$next_day' AND  (l.is_push & 1) = 0 AND r.p_type in(1,2,3) AND l.status < 2 AND l.delete_flag = 0 AND (i.app_push & 4) = 0 ORDER BY l.valid_start DESC limit $page ";
            
            $res = $this->db->query($sql)->getAll();
            $i = count($resData);
            $res = $this->processData($res);
            if(empty($res)) return;
            foreach ($res as $k => $v) 
            {
                    $pushData = array(
                            'type'		=>	'redpack_use',
				            'uid'		=> 	$k,
				            'title'		=> 	"您有红包到账啦",
				            'content'	=> 	'',
				            'time_to_live'	=>	36000000
                    );
                    $cj = count($v[1]);
                    $cz = count($v[2]);
                    $gc = count($v[3]);
                    if($v[3][0]['aid']==8 && $v[3][0]['money'] != '199')
                    {
                        continue;
                    }
                    if( $gc + $cz + $cj == 1)
                    {
                            if($cj == 1) 
                            {
                                $pushData['content'] = '您有1个'.($v[1][0]['money']/100).'元彩金红包已到位，还不快使用，亿万大奖等您中>>';
                                //$pushData['content'] = '三军未动，粮草先行！您有1个充'.($v[2][0]['money_bar']/100).'送'.($v[2][0]['money']/100).'红包已到位，还不快使用，亿万大奖等您中>>';
                            }else if($cz == 1) 
                            {
                                $pushData['content'] = '您有1个'.($v[2][0]['money']/100).'元充值红包已到位，还不快使用，亿万大奖等您中>>';
                                //$pushData['content'] = '三军未动，粮草先行！您有1个充'.($v[2][0]['money_bar']/100).'送'.($v[2][0]['money']/100).'红包已到位，还不快使用，亿万大奖等您中>>';
                            }else{
                                $pushData['content'] = '您有1个'.($v[3][0]['money']/100).'元购彩红包已到位，还不快使用，亿万大奖等您中>>';
                                //$pushData['content'] = '三军未动，粮草先行！您有1个满'.($v[3][0]['money_bar']/100).'减'.($v[3][0]['money']/100).'红包已到位，还不快使用，亿万大奖等您中>>';
                            }
                    }else{
                            if($v[3][0]['aid']==8 && $v[3][0]['money'] == '199')
                            {
                                if($v[3][0]['status']==0){
                                    continue;
                                }
                                $pushData['content'] = '166元红包已到位，还不快使用，亿万大奖等您中>>';
                            }
                            else
                            {
                                if($cz > 0 && $gc > 0 && $cj > 0)
                                {
                                        $pushData['content'] = '您有'.$cz.'个充值红包、'.$cj.'个彩金红包、'.$gc.'个购彩红包已到位，快来使用吧，亿万大奖等你中>>'; 
                                }else if($cj > 0  && $cz > 0 && $gc == 0){
										$pushData['content'] = '您有'.$cz.'个充值红包、'.$cj.'个彩金红包，快来使用吧，亿万大奖等你中>>'; 
                                }else if($cj > 0  && $cz == 0 && $gc > 0){
										$pushData['content'] = '您有'.$cj.'个彩金红包、'.$gc.'个购彩红包已到位，快来使用吧，亿万大奖等你中>>'; 
                                }else if($cj == 0  && $cz > 0 && $gc > 0){
										$pushData['content'] = '您有'.$cz.'个充值红包、'.$gc.'个购彩红包已到位，快来使用吧，亿万大奖等你中>>'; 
                                }else if($cz > 0 && $gc == 0 && $cj ==0)
                                {
                                        $pushData['content'] = '您有'.$cz.'个充值红包已到位，快来使用吧，亿万大奖等您中>>';
                                }else if($cz == 0 && $cj==0 && $gc > 0 )
                                {
                                        $pushData['content'] = '您有'.$gc.'个购彩红包已到位，快来使用吧，亿万大奖等您中>>';
                                }else if ($cj > 0 && $cz == 0 && $gc ==0) {
                                	    $pushData['content'] = '您有'.$cj.'个彩金红包已到位，快来使用吧，亿万大奖等您中>>';
                                }
                            }                  
                    }
                    $this->mipush->index('user', $pushData);
                //更新状态红包表记录
                $update_data = array_merge($v[1],$v[2],$v[3]);
                foreach ($update_data as $k1 => $v1) 
                {
                    $tag1 = $this->db->query("UPDATE cp_redpack_log SET is_push = (is_push | 1) WHERE id = ?", array($v1['id']));
                }

            }
		}while ($i % $page == 0 && $i!=0);



    }   
}