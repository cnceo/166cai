<?php
/**
 * Copyright (c) 2015,上海二三四五网络科技股份有限公司
 * 摘    要：渠道分析管理
 * 作    者：yangqy@2345.com
 * 修改日期：2015.07.22
 */
if (!defined('BASEPATH')) exit('No direct script access allowed');
    
class ChannelAnalysis extends MY_Controller
{
	//平台
	private $platform = array(
		'1' => '网页',
		'2' => 'Android',
        '3' => 'IOS',
        '4' => 'M版'
	);
	private $settleModeArr = array(
        '1' => 'CPA',
        '2' => 'CPS',
    );
    //推广状态
    private $statusArr = array(
        '0' => '合作中',
        '1' => '合作终止',
    );
    //渠道账号状态
    private $channelAccountStatusArr = array(
        '1' => '启用中',
        '2' => '已停用',
    );
    //渠道账号展示字段
    private $displayFields = array(
        'unit_price' => '分成比例/单价',
        'balance_active' => '结算新增',
        'balance_reg' => '结算注册',
        'balance_real' => '结算实名',
        'balance_yj' => '结算分成',
        'balance_amount' => '结算渠道购彩',
        'partner_lottery_num' => '渠道购彩总人数',
        'partner_active_lottery_num' => '新用户购彩人数',
        'partner_curr_lottery_total_amount' => '新用户购彩总额'
    );
	//彩种
	private $lid = array(
		'51' => '双色球',
		'23529' => '大乐透',
		'52' => '福彩3D',
		'33' => '排列3',
		'35' => '排列5',
		'10022' => '七星彩',
		'23528' => '七乐彩',
		'21406' => '十一选五',
		'42' => '竞彩足球',
		'43' => '竞彩篮球',
		'11' => '胜负彩',
		'19' => '任选九'
	);
    public function __construct()
    {
        parent::__construct();
        $this->load->model('Model_Channel_Analysis');
        $this->load->library('tools');
    }

      /*
     * 渠道点击量统计
     * */
    public function click()
    {
    	$this->check_capacity('10_1');
    	$platform = $this->input->get("platform", true) ? $this->input->get("platform", true) : '1';
    	$timeType = $this->input->get("timeType", true) ? $this->input->get("timeType", true) : 'time1';
		$version = $this->input->get("version", true) ? $this->input->get("version", true) : 'all';
    	$searchData = compact('platform', 'timeType', 'version');
    	$result = $this->Model_Channel_Analysis->click($searchData);
		$total = array();
		foreach($result as $key => $val)
		{
			$listData[$val['id']]['name'] = $val['name'];
			$listData[$val['id']]['uv'] = $val['uv'] ? $val['uv'] : 0;
			$listData[$val['id']]['pv'] = $val['pv'] ? $val['pv'] : 0;
			$listData[$val['id']]['click_uv'] = $val['click_uv'] ? $val['click_uv'] : 0;
			$listData[$val['id']]['click_pv'] = $val['click_pv'] ? $val['click_pv'] : 0;
			$listData[$val['id']]['click_rate'] = is_numeric(Division($val['click_uv'],$val['uv'],1))?Precent(Division($val['click_uv'],$val['uv'],1)):Division($val['click_uv'],$val['uv'],1) ;   //$val['uv'] ? $val['click_uv']/$val['uv'] : 0;
			$listData[$val['id']]['rj_click'] = is_numeric(Division($val['click_pv'],$val['click_uv'],1))?number_format(Division($val['click_pv'],$val['click_uv'],1),2):Division($val['click_pv'],$val['click_uv'],1); //$val['click_uv'] ? $val['click_pv']/$val['click_uv'] : 0;
			$total['uv'] += $listData[$val['id']]['uv'];
			$total['pv'] += $listData[$val['id']]['pv'];
			$total['click_uv'] += $listData[$val['id']]['click_uv'];
			$total['click_pv'] += $listData[$val['id']]['click_pv'];
			if($searchData['platform'] == 2)
			{
				$listData[$val['id']]['lv'] = $val['lv'] ? $val['lv'] : 0;
				$total['lv'] += $listData[$val['id']]['lv'];
			}
		}
		if($platform == 1)
		{
			$uData = $this->Model_Channel_Analysis->getWebUvBydate($timeType);
			$total['uv'] = $uData['browse_uv'];
			$total['pv'] = $uData['browse_pv'];
			$total['click_uv'] = $uData['click_uv'];
			$total['click_pv'] = $uData['click_pv'];
		}
		$total['click_ratio'] = $total['uv'] ? $total['click_uv']/$total['uv'] : 0;
		$total['rj_click'] = $total['click_uv'] ? $total['click_pv']/$total['click_uv'] : 0;

    	$datas = array(
    			"search" => $searchData,
    			"list"	=> $listData,
    			'total' => $total,
    			'platform' => $this->platform,
    			'version' => $this->Model_Channel_Analysis->getAppVersion(),
    	);
       	$this->load->view("ChannelAnalysis/click", $datas);
	}
	
	/*
	 * 渠道注册统计
	 * */
	public function register()
	{
		$this->check_capacity('10_2');
        $platform = $this->input->get("platform", true) ? $this->input->get("platform", true) : '1';
        $timeType = $this->input->get("timeType", true) ? $this->input->get("timeType", true) : 'time1';
        $version = $this->input->get("version", true) ? $this->input->get("version", true) : 'all';
        $searchData = array(
    		'platform' => $platform,
    		'timeType' => $timeType,
    		'version' => $version
    	);
    	$result = $this->Model_Channel_Analysis->register($searchData);
        $total = array();
		foreach($result as $key => $value)
		{
			$listData[$value['id']]['name'] = $value['name'];
    		$listData[$value['id']]['register_num'] = $value['register_num'] ? $value['register_num']:0 ;
    		$listData[$value['id']]['register_rate'] = is_numeric(Division($value['register_num'],$value['uv'],1))?Precent(Division($value['register_num'],$value['uv'],1)):Division($value['register_num'],$value['uv'],1) ; //$value['uv']? $value['register_num']/$value['uv']:0;
    		$listData[$value['id']]['valid_user'] = $value['valid_user'] ? $value['valid_user']:0;
    		$listData[$value['id']]['valid_rate'] = is_numeric(Division($value['valid_user'],$value['uv'],1))?Precent(Division($value['valid_user'],$value['uv'],1)):Division($value['valid_user'],$value['uv'],1) ;  // $value['uv'] ? $value['valid_user'] /$value['uv']:0 ;
    		$listData[$value['id']]['complete_user'] = $value['complete_user'] ? $value['complete_user']:0;
			$listData[$value['id']]['uv'] = $value['uv'] ? $value['uv']:0;
			$total['register_num'] += $listData[$value['id']]['register_num'];
			$total['valid_user'] += $listData[$value['id']]['valid_user'];
			$total['complete_user'] += $listData[$value['id']]['complete_user'];
			$total['uv'] +=  $listData[$value['id']]['uv'];
		}
		if($platform == 1)
		{
			$uData = $this->Model_Channel_Analysis->getWebUvBydate($timeType);
			$total['uv'] = $uData['browse_uv'];
		}
		$total['register_rate'] = $total['uv'] ? $total['register_num']/$total['uv'] : 0;
		$total['valid_rate'] = $total['uv'] ? $total['valid_user']/$total['uv'] : 0;
		$datas = array(
    			"search" => $searchData,
    			"list"	=> $listData,
    			'total' => $total,
    			'platform' => $this->platform,
				'version' => $this->Model_Channel_Analysis->getAppVersion()
    	);
       	$this->load->view("ChannelAnalysis/register", $datas);
	}

	 /*
     * 渠道信息管理，处理渠道的列表
     * */
    public function manage()
    {
        $this->check_capacity('5_1_1');
    	$platform = $this->input->post("platform", true) ? $this->input->post("platform", true) : '1';
        $packages = $this->Model_Channel_Analysis->getPackages($platform);
        $packagesId = array();
        foreach ($packages as $one) {
            $packagesId[] = $one['id'];
        }
    	$name = $this->input->post("name", true);
        $settlemode = $this->input->post("settlemode", true) ? $this->input->post("settlemode", true) : '0';
        $id = $this->input->post("id", true);
        $status = $this->input->post("status", true) ? $this->input->post("status", true) : '0';

        $package = $this->input->post("package", true);
        if($package !== '0' && (!in_array($package, $packagesId) || $package === false)) {
            $package = $platform == 2 ? 1 : ($platform == 3 ? 2 : 0);
        }

    	$searchData = array(
            'platform' => $platform, 
            'name' => $name, 
            'id' => $id, 
            'settlemode' => $settlemode, 
            'status' => $status,
            'package' => $package
        );
    	$result = $this->Model_Channel_Analysis->manage($searchData);
    	$datas = array(
    		"search" => $searchData,
    		"list"	=> $result['list'],
    		'platform' => $this->platform,
    		"settleModeArr" => $this->settleModeArr,
            "statusArr" => $this->statusArr,
            "packages" => $packages,
    	);
       	$this->load->view("ChannelAnalysis/manage", $datas);
    }
    
    /*
     * 新增渠道
     * 
     * */
    public function update()
    {
		$this->check_capacity('5_1_5',true);
        $this->config->load('msg_text');
        $this->msg_text_cfg = $this->config->item('msg_text_cfg');
    	$searchData = array();
    	$postData = $this->input->post(null, true);//待会改成post
    	$searchData['platform'] = intval($postData['pplatform']);
    	$searchData['name'] = trim($postData['pchannelname']);
    	$searchData['nick_name'] = trim($postData['pnickname']);
    	$searchData['settle_mode'] = intval($postData['psettlemode']);
        $searchData['package'] = $postData['package'];
    	if($searchData['settle_mode'] == 1)
    	{
    		//$searchData['subtract_coefficient'] = floatval($postData['pcpa']);
			$searchData['unit_price'] = floatval(ParseUnit($postData['punit']));
    	}else if($searchData['settle_mode'] == 2)
    	{
    		$searchData['share_ratio'] = floatval($postData['pcps']);
            $searchData['share_ratio'] = $searchData['share_ratio'] > 100 ? 100.00 :$searchData['share_ratio'];
            $searchData['reg_time'] = $postData['reg_time'];
    	}else if($searchData['settle_mode'] == 3)
    	{
    		$searchData['month_fee'] = floatval(ParseUnit($postData['pcpt']));
    	}
        if ($searchData['platform'] == 4)
        {
            $searchData['app_path'] = trim($postData['path']);
        }
        //新增渠道验证
		if($this->validateChannel($searchData)== "n")
		{
			echo $this->ajaxReturn('n', $this->msg_text_cfg['falied']);
		}
        $searchData['password'] = md5('166cai.com');
        // 默认主包信息
        if(empty($searchData['package']))
        {
            $defaultPackage = array(
                '2' =>  '1',
                '3' =>  '2',
            );
            $searchData['package'] = $defaultPackage[$searchData['platform']] ? $defaultPackage[$searchData['platform']] : 0;
        }
    	$row = $this->Model_Channel_Analysis->update($searchData);
    	if ($row['status'] === "n")
    	{
    		echo $this->ajaxReturn('n', $this->msg_text_cfg['falied']);
		}else if($row['status'] === 'tn')
		{
			echo $this->ajaxReturn('tn', $this->msg_text_cfg['channel']['content_repeat']);
		}

        // 移动端马甲包刷新
        if(in_array($searchData['platform'], array('2', '3')))
        {
            $this->refreshPackageInfo($searchData['platform'], $row['channel_id']);
        }

        $unit_price = $searchData['settle_mode'] == 2 ? 'N/A' : m_format($searchData['unit_price']).' 元';
        $share_ratio = $searchData['settle_mode'] == 2 ? $searchData['share_ratio'].'%' : 'N/A';
        $reg_time = $searchData['settle_mode'] == 2 ? '≤'.$searchData['reg_time'].'天' : 'N/A';
        $str = '新增渠道：'.$searchData['name'].'（渠道名称），单价：'.$unit_price.' ,分成比例：'.$share_ratio.' ，注册时限：'.$reg_time;
        $this->syslog(49, $str);

    	echo $this->ajaxReturn('y', $this->msg_text_cfg['success']);
    	
    	
    }
    /*
     * 修改表中字段的值
     * 
     * */
    public function alter()
    {
        $tag = $this->check_capacity('5_1_6',true);
        $data = $this->input->post('data', true);
        $datas = json_decode($data, true);
        $channel_ids = array();
        $this->config->load('msg_text');
        $this->msg_text_cfg = $this->config->item('msg_text_cfg');
        if(empty($datas))
        {
            echo $this->ajaxReturn('y', $this->msg_text_cfg['success']);
            exit();
        }
        foreach($datas as $value)
        {
            $channel_ids[] = $value['id'];
            if(isset($value['share_ratio']) && ($value['share_ratio'] < 0 || $value['share_ratio'] > 7))
            {
                return $this->ajaxReturn('n', "分成比例(CPS):0.00-7.00");
            }
            if(isset($value['name'])) $value['name'] = trim($value['name']);
            if(isset($value['nick_name'])) $value['nick_name'] = trim($value['nick_name']);
            if(isset($value['settle_mode']))
            {
                $values = array_search(trim($value['settle_mode']),$this->settleModeArr);
            }else
            {
                if($this->validateChannel($value) == 'n')//渠道验证
                {
                    echo $this->ajaxReturn('n', $this->msg_text_cfg['falied']);
                    return;
                }
            }
            if(isset($value['settle_mode']) && (preg_match('/^(CPA|CPS)$/',$value['settle_mode'] )) == FALSE)
            {
                    return $this->ajaxReturn('n', "结算模式只能为CPA或CPS！");
            }
            if((preg_match('/^([0-9]*\.|\.?)[0-9]*$/',$value['unit_price'] )) == FALSE||
                (preg_match('/^([0-9]*\.|\.?)[0-9]*$/',$value['subtract_coefficient'] )) == FALSE||
                (preg_match('/^([0-9]*\.|\.?)[0-9]*$/',$value['share_ratio'] )) == FALSE||
                ( (preg_match('/^([0-9]*\.|\.?)[0-9]*$/',$value['reg_time'] )) == FALSE && $value['reg_time'] !='*') ||
                (preg_match('/^([0-9]*\.|\.?)[0-9]*$/',$value['ret_ratio'] )) == FALSE||
                (preg_match('/^([0-9]*\.|\.?)[0-9]*$/',$value['month_fee'] )) == FALSE)
            {
                return $this->ajaxReturn('n', "请输入大于等于0的数字");
            }
            if($value['settle_mode'] == "CPA")
            {
                $value['settle_mode'] =1;
            }
            else if($value['settle_mode'] == "CPS")
            {
                $value['settle_mode'] =2;
            }
            else if($value['settle_mode'] == "CPT")
            {
                $value['settle_mode'] =3;
            }
            if($value['ios_download'] && strpos($useragent, 'http') < 0)
            {
                return $this->ajaxReturn('n', "无效的链接，必须包含http头");
            }
            if((isset($value['month_fee']) && $value['month_fee'] > 0)|| (isset($value['unit_price']) && $value['unit_price'] > 0) )
            {

                $value['month_fee'] = ParseUnit( $value['month_fee']);
                $value['unit_price']= ParseUnit( $value['unit_price']);
                $returnValue  = $this->Model_Channel_Analysis->updateCol($value);

            }
            else
            {
                $returnValue  = $this->Model_Channel_Analysis->updateCol($value);

            }

        }
        if($returnValue == 'tn')
        {
            echo  $this->ajaxReturn('tn', $this->msg_text_cfg['channel']['content_repeat']);
        }else if($returnValue == 'n')
        {
            echo $this->ajaxReturn('n', $this->msg_text_cfg['falied']);
        }else
        {
            //批量写入日志
            $this->batch_log($channel_ids);
            echo $this->ajaxReturn('y', $this->msg_text_cfg['success']);
        }
		
    }
    
    /*
     * 查看渠道修改日志
     * 
     * */
    public function loglist()
    {
        $this->check_capacity('5_1_4');
    	$channel_id = $this->input->get('channel_id',true);
    	if($channel_id === null)
    	{
    		return $this->ajaxReturn('n', $this->msg_text_cfg['falied']);
    	}
    	$channel_id = intval($channel_id);
    	$searchData = array('channel_id'=>$channel_id);
    	$result = $this->Model_Channel_Analysis->loglist($searchData);//查询数据库部分
	    	
	    $datas = array(
    			"list"	=> $result['list'],
    			"settleModeArr" => $this->settleModeArr
    	);
    	$this->load->view("ChannelAnalysis/loglist", $datas);
    }
	
	/*
	 * 验证渠道信息是否符合规则
	 */
	public function validateChannel($arr)
	{
		$validateFlag= "y";
		foreach($arr as $key => $val)
		{
            switch ($key)
			{
				case 'platform':
					$this->validatePlatform($val,$this->platform,$validateFlag);
					break;
				case 'settle_mode':
					$this->validatePlatform($val,$this->settleModeArr,$validateFlag);
					break;
				case 'name':
				case 'nick_name':
					$this->validatAlpha($val,$validateFlag);
					break;
                case 'app_path':
                    $this->validatAlpha($val,$validateFlag);
					break;
                case 'reg_time':
                    $this->validatRegTime($val,$validateFlag);
                    break;
                case 'modify_retRatio_time':
                case 'ios_download':
                    break;
				default:
					$this->validateNum($val,$validateFlag);
					break;
			}	
		}
		return $validateFlag;
	}
	
	/*
	 * 平台规则验证
	 */
	public function validatePlatform($val,$arr,&$validateFlag)
	{
		if(! isset($arr[$val]))
		{
			$validateFlag = "n";
		}
	}
	
	/*
	 * 只能是字母数字
	 */
	public function validatAlphaNum($str,&$validateFlag)
	{
		if(!ctype_alnum($str))
		{
			$validateFlag = "n";
		}
	}
    /*
     *不限制字符
     */
    public function validatAlpha($str,&$validateFlag)
    {
        if(!isset($str))
        {
            $validateFlag = "n";
        }
    }
	/*
	 * 只能是数字，且必须大于等于0
	 */
	public function validateNum($str,&$validateFlag)
	{
		
		if(!is_numeric($str)|| (is_numeric($str)&&$str < 0))
		{
			$validateFlag = "n";
		}
		
	}
    /**
     * [validatRegTime 验证reg_time]
     * @author LiKangJian 2017-05-08
     * @param  [type] $str           [description]
     * @param  [type] &$validateFlag [description]
     * @return [type]                [description]
     */
    public function validatRegTime($str,&$validateFlag)
    {
        if( $str!='*' && !preg_match('/^\d+$/', $str) )
        {
            $validateFlag = "n";
        }
    }
	
    /**
     * 参    数：
     * 作    者：linw
     * 功    能：充值管理
     * 修改日期：2015.07.22
     */
    public function recharge()
    {
    	$this->check_capacity('10_4');
        $platform = $this->input->get("platform", true) ? $this->input->get("platform", true) : '1';
        $version = $this->input->get("version", true) ? $this->input->get("version", true) : 'all';
        $timeType = $this->input->get("timeType", true) ? $this->input->get("timeType", true) : 'time1';
        $searchData = array(
            'platform' => $platform,
            'version' => $version,
            'timeType' => $timeType,
        );
        $resutl = $this->Model_Channel_Analysis->recharge($searchData);
        $listData = array();
        $total_users = 0;
        $total_total = 0;
        $total_recharge_nums = 0;
        $total_uv = 0;
        
        foreach ($resutl['list'] as $value)
        {
            $listData[$value['id']]['channel'] = $value['name'];
            $listData[$value['id']]['recharge_users'] = $value['users'] ? $value['users'] : 0;
            $listData[$value['id']]['conversion_rate'] = is_numeric(Division($value['users'],$value['uv'],1))?Precent(Division($value['users'],$value['uv'],1),2):Division($value['users'],$value['uv'],1) ;  //$value['uv'] > 0 ? round($value['users'] / $value['uv'] * 100, 2) : '0';
            $listData[$value['id']]['total'] = $value['total'];
            $listData[$value['id']]['recharge_nums'] = $value['recharge_nums'] ? $value['recharge_nums'] : 0;
            $listData[$value['id']]['avg_recharge_money'] = is_numeric(Division($value['total'],$value['recharge_nums'],1))?number_format(ParseUnit(Division($value['total'],$value['recharge_nums'],1),1),2):Division($value['total'],$value['recharge_nums'],1) ;// $value['recharge_nums'] > 0 ? $value['total'] / $value['recharge_nums'] : 0;
            $listData[$value['id']]['avg_user_money'] = is_numeric(Division($value['total'],$value['users'],1))?number_format(ParseUnit(Division($value['total'],$value['users'],1),1),2):Division($value['total'],$value['users'],1) ;//$value['users'] > 0 ? $value['total'] / $value['users'] : 0;
            $total_users += $value['users'];
            $total_total += $value['total'];
            $total_recharge_nums += $value['recharge_nums'];
            $total_uv += $value['uv'];
        }
		//汇总
        if($platform == 1)
        {
        	$total_uv = $resutl['totalUv']; //如果是web 汇总uv取all表
        }
        $total = array(
            'users' => $total_users,
            'conversion_rate' =>is_numeric(Division($total_users ,$total_uv,1))?Precent(Division($total_users ,$total_uv,1),2):Division($total_users ,$total_uv,1) ,// $total_uv > 0 ? round($total_users / $total_uv * 100, 2) : '0',
            'total' => $total_total,
            'recharge_nums' => $total_recharge_nums,
            'avg_recharge_money' => is_numeric(Division($total_total,$total_recharge_nums,1))?number_format(ParseUnit(Division($total_total,$total_recharge_nums,1),1),2):Division($total_total,$total_recharge_nums,1) , //$total_recharge_nums > 0 ? $total_total / $total_recharge_nums : 0,
            'avg_user_money' => is_numeric(Division($total_total,$total_users,1))?number_format(ParseUnit(Division($total_total,$total_users,1),1),2):Division($total_total,$total_users,1) ,   //$total_users > 0 ? $total_total / $total_users : 0,
        );

        $datas = array(
                "search"    => $searchData,
                "list"  => $listData,
                'total' => $total,
                'platform' => $this->platform,
                'channels' => $this->Model_Channel_Analysis->getChannels(),
                'version' => $this->Model_Channel_Analysis->getAppVersion(),
        );
        $this->load->view("ChannelAnalysis/recharge", $datas);
    }
    
    /**
     * 参    数：
     * 作    者：linw
     * 功    能：投注管理
     * 修改日期：2015.07.22
     */
    public function betting()
    {
    	$this->check_capacity('10_3');
    	$platform = $this->input->get("platform", true) ? $this->input->get("platform", true) : '1';
    	$version = $this->input->get("version", true) ? $this->input->get("version", true) : 'all';
    	$timeType = $this->input->get("timeType", true) ? $this->input->get("timeType", true) : 'time1';
    	$searchData = array(
    		'platform' => $platform,
    		'version' => $version,
    		'timeType' => $timeType,
    	);
    	$resutl = $this->Model_Channel_Analysis->betting($searchData);
        $listData = array();
        $total_betting_users = 0;
        $total_total = 0;
        $total_gaopin_total = 0;
        $total_manpin_total = 0;
        $total_jingcai_total = 0;
        $total_order_nums = 0;
        $total_award_total = 0;
        $total_uv = 0;

        foreach ($resutl['list'] as $value)
    	{
            $listData[$value['id']]['channel'] = $value['name'];
    		$listData[$value['id']]['betting_users'] = $value['betting_users'] ? $value['betting_users'] : 0;
            $listData[$value['id']]['conversion_rate'] = is_numeric(Division($value['betting_users'],$value['uv'],1))?Precent(Division($value['betting_users'],$value['uv'],1),2):Division($value['betting_users'],$value['uv'],1) ;     //$value['uv'] > 0 ? round($value['betting_users'] / $value['uv'] * 100, 2) : '0';
            $listData[$value['id']]['total'] = $value['total'] ? $value['total'] : 0;
            $listData[$value['id']]['gaopin_rate'] = is_numeric(Division($value['gaopin_total'],$value['total'],1))?Precent(Division($value['gaopin_total'],$value['total'],1),2):Division($value['gaopin_total'],$value['total'],1) ;  //$value['total'] > 0 ? round($value['gaopin_total'] / $value['total'] * 100, 2) : '0';
            $listData[$value['id']]['manpin_rate'] = is_numeric(Division($value['manpin_total'],$value['total'],1))?Precent(Division($value['manpin_total'],$value['total'],1),2):Division($value['manpin_total'],$value['total'],1) ;  //$value['total'] > 0 ? round($value['manpin_total'] / $value['total'] * 100, 2) : '0';
            $listData[$value['id']]['jingcai_rate'] = is_numeric(Division($value['jingcai_total'],$value['total'],1))?Precent(Division($value['jingcai_total'],$value['total'],1),2):Division($value['jingcai_total'],$value['total'],1) ; //$value['total'] > 0 ? round($value['jingcai_total'] / $value['total'] * 100, 2) : '0';
    		$listData[$value['id']]['order_nums'] = $value['order_nums'] ? $value['order_nums'] : 0;
    		$listData[$value['id']]['avg_order_money'] = is_numeric(Division($value['total'],$value['order_nums'],1))?number_format(ParseUnit(Division($value['total'],$value['order_nums'],1),1),2):Division($value['total'],$value['order_nums'],1) ;//$value['order_nums'] > 0 ? $value['total'] / $value['order_nums'] : 0;
    		$listData[$value['id']]['avg_user_money'] = is_numeric(Division($value['total'],$value['betting_users'],1))?number_format(ParseUnit(Division($value['total'],$value['betting_users'],1),1),2):Division($value['total'],$value['betting_users'],1) ;//$value['betting_users'] > 0 ? $value['total'] / $value['betting_users'] : 0;
    		$listData[$value['id']]['award_total'] = $value['award_total'];
    		$listData[$value['id']]['award_rate'] = is_numeric(Division($value['award_total'],$value['total'],1))?Precent(Division($value['award_total'],$value['total'],1),2):Division($value['award_total'],$value['total'],1) ;// $value['total'] > 0 ? round($value['award_total'] / $value['total'] * 100, 2) : '0';
            $total_betting_users += $value['betting_users'];
            $total_total += $value['total'];
            $total_gaopin_total += $value['gaopin_total'];
            $total_manpin_total += $value['manpin_total'];
            $total_jingcai_total += $value['jingcai_total'];
            $total_order_nums += $value['order_nums'];
            $total_award_total += $value['award_total'];
            $total_uv += $value['uv'];
        }
		//汇总
		if($platform == 1)
		{
			$total_uv = $resutl['totalUv']; //如果是web 汇总uv取all表
		}
    	$total = array(
    		'users' => $total_betting_users,
    		'conversion_rate' => is_numeric(Division($total_betting_users,$total_uv,1))?Precent(Division($total_betting_users,$total_uv,1),2):Division($total_betting_users,$total_uv,1) ,//$total_uv > 0 ? round($total_betting_users / $total_uv * 100, 2) : '0',
    		'total' => $total_total,
            'gaopin_rate' => is_numeric(Division($total_gaopin_total,$total_total,1))?Precent(Division($total_gaopin_total,$total_total,1),2):Division($total_gaopin_total,$total_total,1) , //$total_total > 0 ? round($total_gaopin_total / $total_total * 100, 2) : '0',
            'manpin_rate' => is_numeric(Division($total_manpin_total,$total_total,1))?Precent(Division($total_manpin_total,$total_total,1),2):Division($total_manpin_total,$total_total,1) ,//$total_total > 0 ? round($total_manpin_total / $total_total * 100, 2) : '0',
            'jingcai_rate' => is_numeric(Division($total_jingcai_total,$total_total,1))?Precent(Division($total_jingcai_total,$total_total,1),2):Division($total_jingcai_total,$total_total,1) , //$total_total > 0 ? round($total_jingcai_total / $total_total * 100, 2) : '0',
    		'order_nums' => $total_order_nums,
    		'avg_order_money' => is_numeric(Division($total_total,$total_order_nums,1))?number_format(ParseUnit(Division($total_total,$total_order_nums,1),1),2):Division($total_total,$total_order_nums,1),//$total_order_nums > 0 ? $total_total / $total_order_nums : 0,
    		'avg_user_money' => is_numeric(Division($total_total,$total_betting_users,1))?number_format(ParseUnit(Division($total_total,$total_betting_users,1),1),2):Division($total_total,$total_betting_users,1),//$total_betting_users > 0 ? $total_total / $total_betting_users : 0,
    		'award_total' => $total_award_total,
    		'award_rate' => is_numeric(Division($total_award_total ,$total_total,1))?Precent(Division($total_award_total ,$total_total,1),2):Division($total_award_total ,$total_total,1), //$total_total > 0 ? round($total_award_total / $total_total * 100, 2) : '0',
    	);
    	$datas = array(
    			"search"	=> $searchData,
    			"list"	=> $listData,
    			'total' => $total,
    			'platform' => $this->platform,
    			'channels' => $this->Model_Channel_Analysis->getChannels(),
    			'version' => $this->Model_Channel_Analysis->getAppVersion(),
    	);
       	$this->load->view("ChannelAnalysis/betting", $datas);
    }

    /**
     * 参    数：
     * 作    者：linw
     * 功    能：成本统计
     * 修改日期：2015.07.23
     */
    public function cost()
    {
    	$this->check_capacity('10_5');
        $platform = $this->input->get("platform", true) ? $this->input->get("platform", true) : '1';
        $timeType = $this->input->get("timeType", true) ? $this->input->get("timeType", true) : 'time1';
        $searchData = array(
            'platform' => $platform,
            'timeType' => $timeType,
        );
        $countDays = array('time1' => 7, 'time2' => 30, 'time3' => 60);
        $resutl = $this->Model_Channel_Analysis->cost($searchData);
        $listData = array();
        $total_cost = 0;
        $total_total = 0;
        $total_valid_user = 0;
        $total_recharge_user = 0;
        $total_betting_user = 0;

        foreach ($resutl['list'] as $value)
        {
            $listData[$value['channel']]['channel'] = $value['name'];
            $listData[$value['channel']]['cost'] = $value['cost'] ? $value['cost'] : 0;
            $listData[$value['channel']]['valid_user_price'] =  is_numeric(Division($value['cost'], $value['valid_user'],1))?number_format(ParseUnit(Division($value['cost'], $value['valid_user'],1),1),2):Division($value['cost'], $value['valid_user'],1) ; //Division($value['cost'], $value['valid_user']);
            $listData[$value['channel']]['valid_user_value'] = is_numeric(Division($value['total'], $value['valid_user'],1))?number_format(ParseUnit(Division($value['total'], $value['valid_user'],1),1),2):Division($value['total'], $value['valid_user'],1) ; //Division($value['total'], $value['valid_user']);
            $listData[$value['channel']]['recharge_user_value'] = is_numeric(Division($value['total'], $value['recharge_users'],1))?number_format(ParseUnit(Division($value['total'], $value['recharge_users'],1),1),2):Division($value['total'], $value['recharge_users'],1) ;//Division($value['total'], $value['recharge_users']);
            $listData[$value['channel']]['betting_user_price'] =is_numeric(Division($value['total'], $value['betting_users'],1))?number_format(ParseUnit(Division($value['total'], $value['betting_users'],1),1),2):Division($value['total'], $value['betting_users'],1) ;// Division($value['total'], $value['betting_users']);
            $listData[$value['channel']]['total'] = $value['total'] ? $value['total'] : 0;
            $listData[$value['channel']]['rate'] = is_numeric(Division($value['total'], $value['cost'],1))?Precent(Division($value['total'], $value['cost'],1),2):Division($value['total'], $value['cost'],1);//Precent(Division($value['total'], $value['cost']));
            $total_cost += $value['cost'];
            $total_total += $value['total'];
            $total_valid_user += $value['valid_user'];
            $total_recharge_user += $value['recharge_users'];
            $total_betting_user += $value['betting_users'];
        }
        //汇总数据
        $total = array(
            'total_cost' => $total_cost,
            'total_valid_user_price' => is_numeric(Division($total_cost, $total_valid_user,1))?number_format(ParseUnit(Division($total_cost, $total_valid_user,1),1),2):Division($total_cost, $total_valid_user,1) ,//Division($total_cost, $total_valid_user, 1),
            'total_valid_user_value' => is_numeric(Division($total_total, $total_valid_user,1))?number_format(ParseUnit(Division($total_total, $total_valid_user,1),1),2):Division($total_total, $total_valid_user,1) ,//Division($total_total, $total_valid_user),
            'total_recharge_user_value' => is_numeric(Division($total_total, $total_recharge_user,1))?number_format(ParseUnit(Division($total_total, $total_recharge_user,1),1),2):Division($total_total, $total_recharge_user,1) ,//Division($total_total, $total_recharge_user),
            'total_betting_user_price' => is_numeric(Division($total_total, $total_betting_user,1))?number_format(ParseUnit(Division($total_total, $total_betting_user,1),1),2):Division($total_total, $total_betting_user,1) ,//Division($total_total, $total_betting_user),
            'total_total' => $total_total,
            'total_rate' => is_numeric(Division($total_total, $total_cost,1))?Precent(Division($total_total, $total_cost,1),2):Division($total_total, $total_cost,1),    //Precent(Division($total_total, $total_cost)),
            );
        $datas = array(
                "search"    => $searchData,
                "list"  => $listData,
                'total' => $total,
                'platform' => $this->platform,
                'channels' => $this->Model_Channel_Analysis->getChannels(),
        );
        $this->load->view("ChannelAnalysis/cost", $datas);
    }
    
    /**
     * 上传apk文件
     * @param type $position
     * @param type $index
     * @return json
     */
    public function upload($position, $index)
    {
        if (!file_exists("../uploads/apk/"))
        {
            mkdir("../uploads/apk/");
        }
        $extension = pathinfo($_FILES ['file'] ['name'], PATHINFO_EXTENSION);
        if ($extension != 'apk')
        {
            exit(json_encode(array('error' => '文件类型不允许')));
        }
        $this->load->model('model_channel', 'Channel');
        $config ['upload_path']   = "../uploads/apk/";
        $config ['allowed_types'] = '*';
        $config ["file_name"]     = $_FILES ['file'] ['name'];
        $config ['max_size']      = 102400;
        $config ['overwrite']     = true;
        $config ['remove_spaces'] = false;
        $this->load->library('upload', $config);
        if ($this->upload->do_upload('file'))
        {
            $data = $this->upload->data();
            $res = array(
                'name'     => $data ['file_name'],
                'index'    => $index,
                'position' => $position
            );
            $this->Model_Channel_Analysis->updateCol(array(
                'id'       =>$index,
                'app_path' =>'/uploads/apk/'.$data['file_name']
            ));
            //写日志
            $info = $this->getChannelByIds(array($index));
            $info = $info[0];
            $this->syslog(49, $info['name'].'(渠道名称)上传应用:'.$info['app_path'].'(文件名)');
            exit(json_encode($res));
        } else
        {
            $error = $this->upload->display_errors();
            exit($error);
        }
    }
    /**
     * [updateChannelPwd 更新渠道密码]
     * @author Likangjian  2017-04-30
     * @return [type] [description]
     */
    public function updateChannelPwd()
    {
        $this->check_capacity('5_1_7',true);
        $post = $this->input->post();
        $tag = $this->Model_Channel_Analysis->updateChannelPwd($post);
        if($tag === false)
        {
            $this->ajaxReturn('ERROR', '重置合作商登录密码失败~');
            exit;
        }else
        {
            //写入日志
            $info = $this->getChannelByIds(array($post['channel_id']));
            $info = $info[0];
            $this->syslog(49, '重置合作商登录密码：'.$info['name'].'（渠道名称）');
            $this->ajaxReturn('SUCCESSS', $tag);
            exit;
        }
    }
    /**
     * [upDateRule 更新规则]
     * @author Likangjian  2017-04-29
     * @return [type] [description]
     */
    public function upDateRule()
    {
        $this->check_capacity('5_1_8',true);
        $post = $this->input->post();
        $data = array('rule'=>array(),'coeff'=>array());
        $keys = array();
        date_default_timezone_set('Asia/Shanghai');
        $time = date('Y-m-d H:i:s');
        $array_keys = array_keys($post);
        foreach ($array_keys as $k => $v) 
        {
            if(strstr($v, 'min_percent_'))
            {
                $tmp = str_replace('min_percent_','',$v);
                if(! in_array( $tmp , $keys ) ) array_push($keys,$tmp);
            }
        }
        //验证值是否等于1
        if((int)array_sum($post['percent']) !=1 ){ $this->ajaxReturn('ERROR', '系统占比之和应等于1~');exit;}
        //组合成数组
        foreach ($keys as $k => $v) 
        {
            $data['coeff'][] =  array('id' => $v,'percent' => $post['percent'][$k],'modified' => $time );
            foreach ($post['min_percent_'.$v] as $k1 => $v1) 
            {
                $arr = array('coeff_id' => $v, 'min_percent' => $v1,'max_percent' => $post['max_percent_'.$v][$k1],'score'=>$post['score_'.$v][$k1],'created'=>$time);
                $tag = $this->checkRuleData($arr);
                if( $tag === false )
                {
                    $this->ajaxReturn('ERROR', '请正确填写完整的得分或比例~');exit;
                }
                $data['rule'][] = $arr;
            }
            

        }
        //再次验证数据合法性
        $tag = $this->checkRuleMinMax($data['rule']);
        if($tag === false) { $this->ajaxReturn('ERROR', '请正确填写完整的得分或比例~');exit;}
        //数据验证
        $tag = $this->Model_Channel_Analysis->upDateRule($data);
        if($tag === false)
        {
            $this->ajaxReturn('ERROR', '渠道评分标准设置失败~');
        }else
        {
            $this->syslog(49, '修改渠道评分标准');
            $this->ajaxReturn('SUCCESSS', '渠道评分标准设置成功~');
        } 
    }
    /**
     * [updateRetRatio 异步更新扣减比例]
     * @author LiKangJian 2017-05-05
     * @return [type] [description]
     */
    public function updateRetRatio()
    {
        $this->check_capacity('5_1_9',true);
        $data = $this->input->post('data', true);
        $datas = json_decode($data, true);
        $tag = $this->Model_Channel_Analysis->updateRetRatio($datas[0]);
        if($tag === false)
        {
            $this->ajaxReturn('ERROR', '扣减比例更新失败~');
        }else
        {
            //写入日志
            $info = $this->getChannelByIds( array( $datas[0]['id'] ) );
            $info = $info[0];
            $this->syslog(49, '修改扣减比例：'.$info['name'].'（渠道名称），'.$info['ret_ratio'].'（扣减比例）');
            $this->ajaxReturn('SUCCESSS', '扣减比例更新成功~');
        }
    }
    /**
     * [scoreAndRet 渠道评分]
     * @author LiKangJian 2017-04-28
     * @return [type] [description]
     */
    public function scoreAndRet()
    {
        $this->check_capacity('5_1_3');
        $data = $this->Model_Channel_Analysis->getData();
        //获取规则数据
        $rule = $this->formatRule($this->Model_Channel_Analysis->getRuleData());
        $maxLen = $this->getMaxLen($rule);

        $datas = array(
            'channels' => $data,
            'rule' => $rule,
            'maxLen' => $maxLen,
            'platform' => $this->platform,
        );

        $this->load->view("ChannelAnalysis/scoreAndRet", $datas);
    }
    /**
     * [scoreAndRet 扣减比例数据]
     * @author LiKangJian 2017-04-28
     * @return [type] [description]
     */
    public function koujianData()
    {
        $platform = $this->input->post("platform", true);
        $channel = $this->input->post("channel", true);
        $channelId = $this->input->post("channelId", true);
        $searchData = array(
            'platform' => $platform,
            'channel' => $channel,
            'channelId' => $channelId,
        );
        $data = $this->Model_Channel_Analysis->getData($searchData);

        $html = '';
        foreach ($data as $k => $v) {
            $html .= '<tr>
            <td>'.$v['name'].'</td>
            <td>
                <div class="table-modify">
                <p class="table-modify-txt" data-val="'.$v['ret_ratio'].'">'.$v['ret_ratio'].'<i></i></p>
                <p class="table-modify-ipt"><input type="text" class="ipt" value="'.$v['ret_ratio'].'"><i></i></p>
                </div>
            </td>
            <td>'.($v['modify_retRatio_time']=='0000-00-00 00:00:00' ? $v['created'] : $v['modify_retRatio_time']).'</td>
            <td><a href="javascript:;" class="btn-blue _btn-blue" data-id="'.$v['id'].'">保存</a></td>
            </tr>';
        }
        echo $html;
    }
    /**
     * [countData 渠道数据]
     * @author LiKangJian 2017-05-02
     * @return [type] [description]
     */
    public function countData()
    {
        $this->check_capacity('5_1_2');
        $page = intval($this->input->get("p"));
        $page = $page <= 1 ? 1 : $page;
        $start_time = $this->input->get("start_time", true);
        $end_time = $this->input->get("end_time", true);
        $platform = $this->input->get("platform", true);
        $settle_mode = $this->input->get("settle_mode", true);
        $channel = $this->input->get("channel", true);
        $searchData = array(
            "start_time"  => $start_time ? $start_time : "",
            "end_time" => $end_time ? $end_time : "",
            "channel" => $channel ? $channel : "",
            "settle_mode" => $settle_mode ? $settle_mode :"2",
            "platform" => $platform ? $platform :"2"
        );
        $gets = $this->input->get() ? $this->input->get() : array();
        if($this->checkPramsEmpty($gets))
        {
            $searchData['start_time'] = date('Y-m-d H:i:s',strtotime(date('Y-m-d')));
            $searchData['end_time'] = date('Y-m-d H:i:s',strtotime(date('Y-m-d'))+60*60*24-1);
        }
        $result = $this->Model_Channel_Analysis->getCountData($searchData, $page, 100);
        $pageConfig = array(
                "page"     => $page,
                "npp"      => 100,
                "allCount" => $result[1]
        );
        $pages = get_pagination($pageConfig);
        $infos = array(
            'search'    => $searchData,
            'result'   => $result[0],
            'pages'    => $pages,
            'add_actives' =>$result[2],
            'reg_nums'=>$result[3],
            'real_nums' =>$result[4],
            'total_amounts' =>$result[5],
            'balance_amounts'=>$result[6],
            'actual_divisions'=>$result[7],
            'balance_yjs'=>$result[8]
        );
             
        $this->load->view("ChannelAnalysis/countData",$infos);
    }
    /**
     * [exportCountData 导出]
     * @author LiKangJian 2017-05-02
     * @return [type] [description]
     */
    public function exportCountData()
    {
        $this->check_capacity('5_1_10');
        $page = intval($this->input->get("p"));
        $page = $page <= 1 ? 1 : $page;
        $start_time = $this->input->get("start_time", true);
        $end_time = $this->input->get("end_time", true);
        $platform = $this->input->get("platform", true);
        $settle_mode = $this->input->get("settle_mode", true);
        $channel = $this->input->get("channel", true);
        $searchData = array(
            "start_time"  => $start_time ? $start_time : "",
            "end_time" => $end_time ? $end_time : "",
            "channel" => $channel ? $channel : "",
            "settle_mode" => $settle_mode ? $settle_mode : "1",
            "platform" => $platform ? $platform : "2"
        );
        $gets = $this->input->get() ? $this->input->get() : array();
        if($this->checkPramsEmpty($gets))
        {
            $searchData['start_time'] = date('Y-m-d H:i:s',strtotime(date('Y-m-d')));
            $searchData['end_time'] = date('Y-m-d H:i:s',strtotime(date('Y-m-d'))+60*60*24-1);
        }
        $result = $this->Model_Channel_Analysis->getExportCountData($searchData);
        $res = $this->formatExportData( $result['res'] );
        //写入日志
        $this->syslog(49, '渠道数据导出：'.$res[2].'.xls');
        exportExcel( $res[0],$res[1],$res[2]);
    }
    /**
     * [formatExportData 数据整理]
     * @author LiKangJian 2017-05-02
     * @param  [type] $data [description]
     * @return [type]       [description]
     */
    function formatExportData($data)
    {
        $newdata = array();
        $title = array();
        $titleAndroid = array('日期','渠道名称','真实新增','结算新增','真实注册','注册率','结算注册','真实实名','实名率','结算实名','注册成本','实名成本','真实分成','结算分成','新用户购彩人数','新用户购彩率','新用户购彩人数/激活','真实新增购彩成本','结算新增购彩成本','真实渠道购彩(不含时限)','真实渠道购彩(含时限)','结算渠道购彩','单价/分成比例','扣减比例','新用户购彩总额','日均新用户购彩','渠道购彩总人数','次日购彩留存','渠道活跃','注册时限','核减系数得分');
        $titlePC = array('日期','渠道名称','真实注册','注册率','结算注册','真实实名','实名率','结算实名','注册成本','实名成本','真实分成','结算分成','新用户购彩人数','新用户购彩率','真实新增购彩成本','结算新增购彩成本','真实渠道购彩(不含时限)','真实渠道购彩(含时限)','结算渠道购彩','单价/分成比例','扣减比例','新用户购彩总额','日均新用户购彩','渠道购彩总人数','次日购彩留存','注册时限');
        if ($data[0]['settle_mode'] == 1) {
            $filename = '彩票_CPA_'.date('Y_m_d');
        } else {
            $filename = '彩票_CPS_'.date('Y_m_d');
        }
        if (in_array($data[0]['platform'], array(2, 3))) {
            $title = $titleAndroid;
            foreach ($data as $k => $v) {
                $newdata[] = array(
                    date('Y-m-d', strtotime($v['date'])),
                    $v['name'],
                    $v['add_active'],
                    $v['balance_active'],
                    $v['reg_num'],
                    $v['per_reg'].'%',
                    $v['balance_reg'],
                    $v['real_num'],
                    $v['per_real'].'%',
                    $v['balance_real'],
                    m_format($v['balance_reg_amount']),
                    m_format($v['balance_real_amount']),
                    m_format($v['actual_division']),
                    m_format($v['balance_yj']),
                    $v['active_lottery_num'],
                    $v['per_curr_lottery'].'%',
                    $v['curr_lottery_divided_active'].'%',                       
                    m_format($v['balance_lottery_amount']),
                    m_format($v['balance_lottery_money']),
                    m_format($v['total_amount']),
                    m_format($v['lottery_total_amount']),
                    m_format($v['balance_amount']),
                    ($data[0]['settle_mode'] == '2' ? $v['unit_price'].'%' : m_format($v['unit_price'])),
                    $v['ret_ratio'],
                    m_format($v['curr_lottery_total_amount']),
                    m_format($v['avg_curr_lottery_amount']),
                    $v['lottery_num'],
                    $v['per_next_lottery_num'].'%',
                    $v['active_num'],
                    $v['reg_time'],
                    $v['redu_coeff_score'],
                ); 
            }
        } else {
            $title = $titlePC;
            foreach ($data as $k => $v) {
                $newdata[] = array(
                    date('Y-m-d', strtotime($v['date'])),
                    $v['name'],
                    $v['reg_num'],
                    $v['per_reg'].'%',
                    $v['balance_reg'],
                    $v['real_num'],
                    $v['per_real'].'%',
                    $v['balance_real'],
                    m_format($v['balance_reg_amount']),
                    m_format($v['balance_real_amount']),
                    m_format($v['actual_division']),
                    m_format($v['balance_yj']),
                    $v['active_lottery_num'],
                    $v['per_curr_lottery'].'%',               
                    m_format($v['balance_lottery_amount']),
                    m_format($v['balance_lottery_money']),
                    m_format($v['total_amount']),
                    m_format($v['lottery_total_amount']),
                    m_format($v['balance_amount']),
                    ($data[0]['settle_mode'] == '2' ? $v['unit_price'].'%' : m_format($v['unit_price'])),
                    $v['ret_ratio'],
                    m_format($v['curr_lottery_total_amount']),
                    m_format($v['avg_curr_lottery_amount']),
                    $v['lottery_num'],
                    $v['per_next_lottery_num'].'%',
                    $v['reg_time'],
                ); 
            }
        }
        
        return array($title,$newdata,$filename);
    }

    /**
     * 导出对账单
     */
    public function writeExcel()
    {
        $start_time = $this->input->get("start_time", true);
        $end_time = $this->input->get("end_time", true);
        $platform = $this->input->get("platform", true);
        $settle_mode = $this->input->get("settle_mode", true);
        $channel = $this->input->get("channel", true);
        $searchData = array(
            "start_time"  => $start_time ? $start_time : "",
            "end_time" => $end_time ? $end_time : "",
            "channel" => $channel ? $channel : "",
            "settle_mode" => $settle_mode ? $settle_mode :"1",
            "platform" => $platform ? $platform :"2"
        );
        
        $data = $this->Model_Channel_Analysis->getExportBalanceData($searchData);
        $money = isset($data['balance_yj']) ? '¥' . m_format($data['balance_yj']) : '¥0.00';
        
        $startDate = date('Y年m月', strtotime($searchData['start_time']));
        $title = "{$startDate}结算单";
        $this->load->library('excel');
        $this->excel->setActiveSheetIndex(0);
        
        //设置字体
        $this->excel->getActiveSheet()->getDefaultStyle()->getFont()->setName('宋体');
        $styleArray = array(
            'borders' => array(
                'allborders' => array(
                    'style' => PHPExcel_Style_Border::BORDER_THIN,//细边框
                ),
            ),
        );
        //设置边框
        $this->excel->getActiveSheet()->getStyle('A1:B4')->applyFromArray($styleArray);
        //设置列宽度
        $this->excel->getActiveSheet()->getColumnDimension('A')->setWidth(46);
        $this->excel->getActiveSheet()->getColumnDimension('B')->setWidth(46);
        //设置第一行高度
        $this->excel->getActiveSheet()->getRowDimension('1')->setRowHeight(30);
        //合并单元格
        $this->excel->getActiveSheet()->mergeCells('A1:B1');
        
        //第一行设置
        $this->excel->getActiveSheet()->setCellValue('A1', $title);
        $this->excel->getActiveSheet()->getStyle('A1')->getFont()->setBold(true);
        $this->excel->getActiveSheet()->getStyle('A1')->getFont()->setSize(14);
        $this->excel->getActiveSheet()->getStyle('A1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $this->excel->getActiveSheet()->getStyle('A1')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
        //第二行设置
        $this->excel->getActiveSheet()->setCellValue('A2', '服务产品：');
        $this->excel->getActiveSheet()->setCellValue('B2', '166彩票');
        $this->excel->getActiveSheet()->getStyle('A2')->getFont()->setSize(12);
        $this->excel->getActiveSheet()->getStyle('B2')->getFont()->setSize(12);
        //第三行
        $this->excel->getActiveSheet()->setCellValue('A3', '服务时间：');
        $serviceTime = date('Y年m月d日', strtotime($searchData['start_time'])) . '-' . date('Y年m月d日', strtotime($searchData['end_time']));
        $this->excel->getActiveSheet()->setCellValue('B3', $serviceTime);
        $this->excel->getActiveSheet()->getStyle('A3')->getFont()->setSize(12);
        $this->excel->getActiveSheet()->getStyle('B3')->getFont()->setSize(12);
        //第四行
        $this->excel->getActiveSheet()->setCellValue('A4', '费用金额（小写）：');
        $this->excel->getActiveSheet()->setCellValue('B4', $money);
        $this->excel->getActiveSheet()->getStyle('A4')->getFont()->setSize(12);
        $this->excel->getActiveSheet()->getStyle('B4')->getFont()->setSize(12);
        //合并单元格
        $this->excel->getActiveSheet()->mergeCells('A5:B6');
        //第六行
        $this->excel->getActiveSheet()->setCellValue('A5', '本对账单为最终确认数据，开票依据，盖章有效！');
        $this->excel->getActiveSheet()->getStyle('A5')->getFont()->setBold(true);
        $this->excel->getActiveSheet()->getStyle('A5')->getFont()->setSize(12);
        $this->excel->getActiveSheet()->getStyle('A5')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $this->excel->getActiveSheet()->getStyle('A5')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
        
        $this->excel->getActiveSheet()->mergeCells('A7:B8');
        //第八行
        $this->excel->getActiveSheet()->setCellValue('A7', "上海彩咖网络科技有限公司开票信息，请开具增值税专用发票：");
        $this->excel->getActiveSheet()->getStyle('A7')->getFont()->setBold(true);
        $this->excel->getActiveSheet()->getStyle('A7')->getFont()->setSize(12);
        $this->excel->getActiveSheet()->getStyle('A7')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $this->excel->getActiveSheet()->getStyle('A7')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
        
        $styleArray = array(
            'borders' => array(
                'right' => array(
                    'style' => PHPExcel_Style_Border::BORDER_THIN,//细边框
                ),
            ),
        );
        $this->excel->getActiveSheet()->getStyle('A5:B8')->applyFromArray($styleArray);
        $this->excel->getActiveSheet()->getStyle('A10:A15')->applyFromArray($styleArray);
        $this->excel->getActiveSheet()->getStyle('B10:B15')->applyFromArray($styleArray);
        
        $this->excel->getActiveSheet()->mergeCells('A9:B9');
        //设置边框
        $styleArray['borders']['bottom'] = array(
            'style' => PHPExcel_Style_Border::BORDER_THIN,//细边框
        );
        $this->excel->getActiveSheet()->getStyle('A9:B9')->applyFromArray($styleArray);
        $this->excel->getActiveSheet()->getStyle('A15:B15')->applyFromArray($styleArray);
        
        $this->excel->getActiveSheet()->getRowDimension('9')->setRowHeight(90);
        $this->excel->getActiveSheet()->setCellValue('A9', "开票抬头：上海彩咖网络科技有限公司\n发票内容：技术服务费或信息服务费\n税号：91310115MA1H7F8R57\n地址及电话：上海市浦东新区张杨北路5509号1010室 021-39982670\n开户银行及账号：招商银行上海杨思支行121919470510506");
        $this->excel->getActiveSheet()->getStyle('A9')->getFont()->setSize(12);
        $this->excel->getActiveSheet()->getStyle('A9')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $this->excel->getActiveSheet()->getStyle('A9')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
        $this->excel->getActiveSheet()->getStyle('A9')->getAlignment()->setWrapText(true);
        
        //第10行
        $this->excel->getActiveSheet()->mergeCells('A10:A12');
        $this->excel->getActiveSheet()->mergeCells('B10:B12');
        $this->excel->getActiveSheet()->setCellValue('A10', '甲方（盖章）:上海彩咖网络科技有限公司');
        $this->excel->getActiveSheet()->setCellValue('B10', '乙方（盖章）：');
        $this->excel->getActiveSheet()->getStyle('A10')->getFont()->setSize(12);
        $this->excel->getActiveSheet()->getStyle('B10')->getFont()->setSize(12);
        $this->excel->getActiveSheet()->getStyle('A10')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
        $this->excel->getActiveSheet()->getStyle('A10')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_TOP);
        $this->excel->getActiveSheet()->getStyle('B10')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
        $this->excel->getActiveSheet()->getStyle('B10')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_TOP);
        //第13行
        $this->excel->getActiveSheet()->mergeCells('A13:A14');
        $this->excel->getActiveSheet()->mergeCells('B13:B14');
        $this->excel->getActiveSheet()->setCellValue('A13', '确认人：');
        $this->excel->getActiveSheet()->setCellValue('B13', '确认人：');
        $this->excel->getActiveSheet()->getStyle('A13')->getFont()->setSize(12);
        $this->excel->getActiveSheet()->getStyle('B13')->getFont()->setSize(12);
        $this->excel->getActiveSheet()->getStyle('A13')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
        $this->excel->getActiveSheet()->getStyle('A13')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_TOP);
        $this->excel->getActiveSheet()->getStyle('B13')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
        $this->excel->getActiveSheet()->getStyle('B13')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_TOP);
        //第15行
        $this->excel->getActiveSheet()->setCellValue('A15', '    年    月    日');
        $this->excel->getActiveSheet()->setCellValue('B15', '    年    月    日');
        $this->excel->getActiveSheet()->getStyle('A13')->getFont()->setSize(12);
        $this->excel->getActiveSheet()->getStyle('B13')->getFont()->setSize(12);
        
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="' . $startDate . '结算单(彩咖网络科技).xls"');
        header('Cache-Control: max-age=0');
        
        $objWriter = PHPExcel_IOFactory::createWriter($this->excel, 'Excel5');
        $objWriter->save('php://output');
    }

    /**
     * [formatRule 格式化规则数据]
     * @author LiKangJian 2017-04-28
     * @param  [type] $rule [description]
     * @return [type]       [description]
     */
    public function formatRule($rule)
    {
        $ids = array();
        $newRule = array();
        foreach ($rule as $k => $v) 
        {
            if(!in_array($v['id'], $ids))
            {
                array_push($ids, $v['id']);
            }
            $rule = array();
            if(!empty($v['rid'])) 
            {
                $rule = array('rid'=>$v['rid'],'min_percent'=>$v['min_percent'],'max_percent'=>$v['max_percent'],'score'=>$v['score']);
            }
            if(! isset( $newRule[$v['id']] ) )
            {
                $newRule[$v['id']] = array('id' => $v['id'],'name' => $v['name'],'des' => $v['des'],'percent' => $v['percent'],'created' => $v['created'],'modified' => $v['modified'],'rule'=>array());
                if(count($rule)){ $newRule[$v['id']]['rule'][] = $rule;}
                  
            }else{
                if(count($rule)){ $newRule[$v['id']]['rule'][] = $rule;}
            } 
        }

        return array_values($newRule);
    }
    /**
     * [getMaxLen description]
     * @author LiKangJian 2017-04-28
     * @param  [type] $rule [description]
     * @return [type]       [description]
     */
    public function getMaxLen($rule)
    {
        $len = 0;
        foreach ($rule as $k => $v) 
        {
            $count = count($v['rule']);
            $len = $count > $len ? $count : $len;
        }

        return $len;
    }
    /**
     * [checkRuleData 验证规则数据正确性]
     * @author Likangjian  2017-04-30
     * @param  [type] $arr [description]
     * @return [type]      [description]
     */
    public function checkRuleData($arr)
    {
        $preg1 = '/^[1-9]\d*\.\d*|0\.\d*[1-9]\d*$/';//正浮点数
        $preg2 = '/^[1-9]\d*|0$/';
        //min_percent 非负数 或者 正浮点数
        if(! preg_match($preg1, $arr['min_percent']) && ! preg_match($preg2, $arr['min_percent']))
        {
            return false;
        }
        //max_percent 非负数 或者 正浮点数
        if(! preg_match($preg1, $arr['max_percent']) && ! preg_match($preg2, $arr['max_percent']) && $arr['max_percent']!='*')
        {
            return false;
        }
        //score 非负数 或者 正浮点数
        if(! preg_match($preg1, $arr['score']) && ! preg_match($preg2, $arr['score']))
        {
            return false;
        }
        //分数
        if($arr['score'] < 0 || $arr['score'] > 10 ) { return false; }
        //都是数字
        if($arr['max_percent']!='*' && ( (float)$arr['min_percent'] >= (float)$arr['max_percent'] ) ) 
        { 
            return false; 
        }
        //验证是否大100
        if( $arr['coeff_id'] != 2 )
        {
            if( (float)$arr['min_percent'] > 100 ) { return false; }
            if( $arr['max_percent']!='*' && (float)$arr['max_percent'] > 100 ) { return false; }
        }
        
        return true;
    }
    /**
     * [checkRuleMinMax 对区间值强验证]
     * @author LiKangJian 2017-05-04
     * @param  [type] $data [description]
     * @return [type]       [description]
     */
    public function checkRuleMinMax($data)
    {
        $arr = array();
        foreach ($data as $k => $v) 
        {
            if( !isset( $arr[$v['coeff_id']] ) )
            {
                $arr[$v['coeff_id']] = array();
            } 
            $arr[$v['coeff_id']][] = array('min'=>$v['min_percent'],'max'=>$v['max_percent']);
        }
        $tag = true;
        //轮循验证
        foreach ($arr as $k => $v) 
        {
            $count = count($v);
            $flag = true;
            foreach ($v as $k1 => $v1) 
            {
                if($k1<$count-1)
                {
                    if($v1['max']=='*')
                    {

                        return $flag = false;
                    }else{
                        //第一个最大 不等第二行的最小
                       if( (float)($v[$k1+1]['min']) != (float)($v1['max']) )
                        {
                            return $flag = false;
                       }
                       //min大于max值
                       if( (float)($v1['min']) > (float)($v1['max']) ) 
                       {
                         return $flag = false;
                       }
                    }
                    
                }
            }
            if($flag===false) return $tag = false;
        }
        
        return $tag;
    }
    /**
     * [checkPramsEmpty 检测空参数方法]
     * @author LiKangJian 2017-04-17
     * @param  [type] $params [description]
     * @return [type]         [description]
     */
    public function checkPramsEmpty($params)
    {
        $count = count($params);
        $t = 0;
        if(!$count) return true;
        foreach ($params as $k => $v) 
        {
            if(empty($v) && $k!='p')
            {
                $t = $t + 1;
            } 
            if($k=='p')
            {
                $t = $t + 1;
            }
        }
        if($count!=$t) return false;
        return true;
    } 
    /**
     * [getChannelByIds 获取渠道详细信息]
     * @author LiKangJian 2017-05-05
     * @param  [type] $id [description]
     * @return [type]     [description]
     */
    public function getChannelByIds($ids)
    {
        return $this->Model_Channel_Analysis->getChannelByIds($ids);
    }
    /**
     * [batch_log 批量日志]
     * @author LiKangJian 2017-05-05
     * @param  [type] $arr [description]
     * @return [type]      [description]
     */
    public function batch_log($channel_ids)
    {
        
        $arr = $this->getChannelByIds($channel_ids);
        foreach ($arr as $key => $info) 
        {
            
            $unit_price = $info['settle_mode'] == 2 ? 'N/A' : m_format($info['unit_price']).' 元';
            $share_ratio = $info['settle_mode'] == 2 ? $info['share_ratio'].'%' : 'N/A';
            $reg_time = $info['reg_time'] == 2 ? '≤'.$info['reg_time'].'天' : 'N/A';
            $str = '修改渠道：'.$info['name'].'（渠道名称），单价：'.$unit_price.' ,分成比例：'.$share_ratio.' ，注册时限：'.$reg_time;
            $this->syslog(49, $str);
        }
        
    }  

    // 更新渠道停开售
    public function updateChnnelSale()
    {
        $this->check_capacity('5_1_11',true);
        $id = $this->input->post("id", true);
        $cstate = $this->input->post("cstate", true) ? '1' : '0';
        $name = $this->input->post("name", true);

        if(!empty($id))
        {
            $row = $this->Model_Channel_Analysis->updateChnnelSale($id, $cstate);
            if($row > 0)
            {
                $this->syslog(49, '渠道停开售设置：' . $name . '（渠道名称）');
                $this->ajaxReturn('y', '更新成功');
                exit;
            } 
        }
        $this->ajaxReturn('n', '更新失败');
        exit;
    }

    // 获取主包信息列表
    public function getPackages()
    {
        $platform = $this->input->post("platform", true);
        $info = $this->Model_Channel_Analysis->getPackages($platform);
        $result = array(
            'status' => '1',
            'msg' => '请求成功',
            'data' => $info
        );
        die(json_encode($result));
    }

    public function addPackage()
    {
        $post = $this->input->post();
        if(empty($post['pplatform']) || empty($post['packagename']))
        {
            $this->ajaxReturn('n', '新建失败，包名不能为空');
            exit;
        }

        $data = array(
            'platform'  =>  $post['pplatform'],
            'name'      =>  $post['packagename'],
        );

        // 检查包名重复
        $info = $this->Model_Channel_Analysis->getPackageUnique($data);
        if($info)
        {
            $this->ajaxReturn('n', '新建失败，应用名称不可重复');
            exit; 
        }

        $this->Model_Channel_Analysis->addPackage($data);
        $this->syslog(49, $data['platform'].'(平台)新增应用：'.$data['name'].'(应用名称)');
        $this->ajaxReturn('y', '新建成功');
        exit;
    }

    public function alterPackage()
    {
        $post = $this->input->post();
        if(empty($post['channelId']) || empty($post['packageData']))
        {
            $this->ajaxReturn('n', '修改失败，渠道或应用名称不能为空');
            exit;
        }
        $data = array(
            'id'        =>  $post['channelId'],
            'package'   =>  $post['packageData'],
        );
        $this->Model_Channel_Analysis->updatePackage($data);
        $this->ajaxReturn('y', '修改成功');
        exit;
    }

    //更改推广状态
    public function alterStatus()
    {
        $post = $this->input->post();
        if(empty($post['channelId']) || $post['newStatus'] == '')
        {
            $this->ajaxReturn('n', '切换失败，渠道或推广状态不能为空');
            exit;
        }
        $data = array(
            'id'        =>  $post['channelId'],
            'status'   =>  $post['newStatus'],
        );
        $this->Model_Channel_Analysis->updateStatus($data);
        $this->syslog(49, $data['id'].'：合作终止/合作中');
        $this->ajaxReturn('y', '修改成功');
        exit;
    }

    // 同步刷新渠道对应默认包的素材
    public function refreshPackageInfo($platform, $channelId)
    {
        // 主包渠道
        if (ENVIRONMENT === 'production')
        {
            $default = array(
                '2' =>  '10047',
                '3' =>  '10003',
            );
        }
        else
        {
            $default = array(
                '2' =>  '47',
                '3' =>  '3',
            );
        }

        // 轮播图、启动页、活动模块、彩种管理、首页弹层、微信登录、礼包提醒
        $packageConfig = array(
            'cp_add_info'   =>  array(
                'platform'  =>  array(
                    '2' =>  'android',
                    '3' =>  'ios',
                ),
                'refresh'   =>  '0',
            ),
            'cp_app_activity_config'   =>  array(
                'platform'  =>  array(
                    '2' =>  '1',
                    '3' =>  '2',
                ),
                'refresh'   =>  '0',
            ),
            'cp_lottery_info'   =>  array(
                'platform'  =>  array(
                    '2' =>  'android',
                    '3' =>  'ios',
                ),
                'refresh'   =>  '0',
            ),
            'cp_app_banner'   =>  array(
                'platform'  =>  array(
                    '2' =>  '1',
                    '3' =>  '2',
                ),
                'refresh'   =>  '1',
            ),
        );

        foreach ($packageConfig as $table => $items) 
        {
            $this->refreshChannel($table, $items['platform'][$platform], $default[$platform], $channelId, $items['refresh']);
        }
    }

    public function refreshChannel($table, $platform, $defaultChannel, $channelId, $needRefresh)
    {
        $info = $this->Model_Channel_Analysis->getChannelInfo($table, $platform);

        if(!empty($info))
        {
            $fields = array('id', 'channels');
            $bdata['s_data'] = array();
            $bdata['d_data'] = array();
            foreach ($info as $items) 
            {
                $channels = explode(',', $items['channels']);
                if(!empty($items['channels']) && in_array($defaultChannel, $channels) && !in_array($channelId, $channels))
                {
                    array_push($channels, $channelId);
                    array_push($bdata['s_data'], "(?, ?)");
                    array_push($bdata['d_data'], $items['id']);
                    array_push($bdata['d_data'], implode(',', $channels));
                }
            }

            if(!empty($bdata['s_data']))
            {
                $this->Model_Channel_Analysis->recodeChannels($table, $fields, $bdata);
                $bdata['s_data'] = array();
                $bdata['d_data'] = array();
                // 刷新缓存
                if($needRefresh)
                {
                    $this->load->model('model_appconfig', 'Appconfig');
                    $bannerArr = array(1, 2, 3, 4);
                    $platformType = array(
                        '1'   =>  'android',
                        '2'   =>  'ios',
                    );
                    foreach ($bannerArr as $ctype) 
                    {
                        $this->Appconfig->refreshBannerInfo($ctype, $platform, $platformType[$platform]);
                    }
                }
            }
        }
    }

    //渠道账号管理
    public function accountManage()
    {
        $this->check_capacity('5_2_1');

        $page = intval($this->input->get("p"));
        $page = $page <= 1 ? 1 : $page;

        $account = $this->input->get("account", true);
        $accountStatus = $this->input->get("accountStatus", true) ? $this->input->get("accountStatus", true) : '1';
        $searchData = array(
            'account' => $account,
            'accountStatus' => $accountStatus,
        );
        $result = $this->Model_Channel_Analysis->getUserChannels($searchData, $page, 20);
        if ($account === FALSE) {
            unset($searchData['account']);
        }
        $pageConfig = array(
            "page"     => $page,
            "npp"      => 20,
            "allCount" => $result['rows'],
            "query"    => http_build_query($searchData)
        );
        $pages = get_pagination($pageConfig);
        $datas = array(
            'channelAccountStatusArr' => $this->channelAccountStatusArr,
            'list' => $result['list'],
            'search' => $searchData,
            'pages' => $pages
        );
        $this->load->view("ChannelAnalysis/accountManage", $datas);
    }

    //更改渠道账号状态
    public function alterAccountStatus()
    {
        $this->check_capacity('5_2_4', true);
        $post = $this->input->post(NULL, TRUE);
        if(empty($post['accountId']) || empty($post['newStatus']))
        {
            $this->ajaxReturn('n', '操作失败，渠道账号不能为空');
            exit;
        }
        $data = array(
            'id'       =>  $post['accountId'],
            'status'   =>  $post['newStatus'],
        );
        $this->Model_Channel_Analysis->updateAccountStatus($data);
        $user = $this->Model_Channel_Analysis->getUserChannel(array('id'=>$post['accountId']));
        $this->syslog(76, '停用/启用渠道账号：'.$user['uname']);
        $this->ajaxReturn('y', '修改成功');
        exit;
    }

    //新增渠道账号
    public function addChannelUser()
    {
        $this->check_capacity('5_2_2');
        
        $datas = array(
            'displayFields' => $this->displayFields,
            'channels' => $this->Model_Channel_Analysis->getChannelSelectData(),
        );
        
        $post = $this->input->post(NULL, TRUE);
        if ($post) {
            if (!preg_match('/^[a-zA-Z0-9_-]+@[a-zA-Z0-9_-]+(\.[a-zA-Z0-9_-]+)+$/', $post['uname'])) {
                $this->ajaxReturn('ERROR', '邮件格式不正确');
                exit;
            }
            if ($this->Model_Channel_Analysis->channelUserExists($post['uname'])) {
                $this->ajaxReturn('ERROR', '该渠道账号已经存在');
                exit;
            }
            foreach ($post as $key => $value) {
                if (in_array($key, array('channels', 'fields'))) {
                    $post[$key] = implode(',', $value);
                }
            }
            $post['created'] = date('Y-m-d H:i:s');
            do{
                $str = rand_str(16);
            }while (strlen($str) != 16);
            $post['password'] = md5($str);
            $this->Model_Channel_Analysis->insertNewChannelUser($post);

            //日志和邮件
            $this->_channelUserEmail($post['uname'], $str);
            $this->syslog(76, '新增渠道账号：'.$post['uname'].'，涉及渠道：'.$post['channels'].'；展示字段：'.$post['fields'].'；');
            $this->ajaxReturn('SUCCESSS', '恭喜您，操作成功');
            exit;
        }
        
        $this->load->view("ChannelAnalysis/addAndModifyUser", $datas);
    }

    //修改渠道账号
    public function modifyChannelUser()
    {
        $this->check_capacity('5_2_3');
        $id = $this->input->get('id', true);
        $datas = array(
            'displayFields' => $this->displayFields,
            'channels' => $this->Model_Channel_Analysis->getChannelSelectData(),
        );
        $searchData['id'] = $id;
        $channelUser = $this->Model_Channel_Analysis->getUserChannel($searchData);
        $datas['oneChannelUser'] = $channelUser;
        $datas['id'] = $id;

        $post = $this->input->post(NULL, TRUE);
        if ($post) {
            foreach ($post as $key => $value) {
                if (in_array($key, array('channels', 'fields'))) {
                    $post[$key] = implode(',', $value);
                }
            }
            $this->Model_Channel_Analysis->insertNewChannelUser($post);
            $this->syslog(76, '修改渠道账号：'.$channelUser['uname'].'，涉及渠道：'.$post['channels'].'；展示字段：'.$post['fields'].'；');
            $this->ajaxReturn('SUCCESSS', '恭喜您，操作成功');
        }
        
        $this->load->view("ChannelAnalysis/addAndModifyUser", $datas);
    }

    //查看渠道账号详情
    public function channelUserDetail()
    {
        $datas = array(
            'displayFields' => $this->displayFields,
            'channels' => $this->Model_Channel_Analysis->getChannelSelectData(),
        );
        $searchData['id'] = $this->input->get('id', true);
        $oneChannelUser = $this->Model_Channel_Analysis->getUserChannel($searchData);
        $datas['oneChannelUser'] = $oneChannelUser;
        $this->load->view("ChannelAnalysis/channelUserDetail", $datas);
    }

    //重置渠道账号密码
    public function updateChannelUserPwd()
    {
        $this->check_capacity('5_2_5', true);
        do{
            $str = rand_str(16);
        }while(strlen($str) != 16);
        $post = $this->input->post();
        $tag = $this->Model_Channel_Analysis->updateChannelUserPwd($post, $str);
        if($tag === false)
        {
            $this->ajaxReturn('ERROR', '重置渠道账号密码失败~');
            exit;
        } else {
            $this->_channelUserEmail($post['user_name'], $str);
            $this->ajaxReturn('SUCCESSS', '重置渠道账号密码成功~');
            exit;
        }
    }

    //新增或者重置密码，发送邮件通知
    private function _channelUserEmail($email, $password)
    {
        $data = array();
        $data['subject'] = '【166彩票】开通渠道账号通知';
        $data['to'] = $email;
        $data['message'] = '尊敬的 '.$email.'（渠道账号）:<br />
        您好！已为您开通渠道账号，账号密码如下：<br />
        账号：'.$email.'（邮箱）<br />
        密码：'.$password.'（默认密码）<br />
        登录地址为：https://888.166cai.cn/chansys/index/login<br />
        密码是自动生成的，登录后为了方便您记住，请自行修改密码，谢谢！';
        $this->tools->sendMail($data, array(), 1);
    }
}
