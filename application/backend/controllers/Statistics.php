<?php

/*
 * 后台 财务对账
 * @date:2015-12-15
 */
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Statistics extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('model_statistics');
    }

    // 时间戳
    public function filterTime(&$time1, &$time2)
    {
        if (!empty($time1) || !empty($time2))
        {
            if (empty($time1))
            {
                $time1 = date("Y-m-d 00:00:00", strtotime('-3 months', strtotime($time2)));
            }
            elseif (empty($time2))
            {
                $time2 = date("Y-m-d 23:59:59", strtotime('+ 3 months', strtotime($time1)));
            }
            else
            {
                if (strtotime($time1) > strtotime($time2))
                {
                    echo "时间非法";
                    exit;
                }
                 
                // if (strtotime("-3 months", strtotime($time2)) > strtotime($time1))
                // {
                //     $time2 = date("Y-m-d 23:59:59", strtotime("+3 months", strtotime($time1)));
                // }
            }
        }
        else
        {
            $time2 = date("Y-m-d 23:59:59");
            $time1 = date("Y-m-d 00:00:00", strtotime('-1 month'));
        }
    }
    
    // 账户余额对账
    public function index()
    {
        $page = intval($this->input->get("p"));
        $page = $page <= 1 ? 1 : $page;
        $searchData = array(
            "start_time"           => $this->input->get("start_time", TRUE),
            "end_time"             => $this->input->get("end_time", TRUE),
            "searchType"           => $this->input->get("searchType", true),
        );
        if(empty($searchData['searchType'])) $searchData['searchType'] = 'day';
        //权限判断
        if($searchData['searchType'] == 'day')
        {
            $this->check_capacity('9_1_1');
        }
        else
        {
            $this->check_capacity('9_1_3');
        }
        
        $this->filterTime($searchData['start_time'], $searchData['end_time']);

        // 查询条件
        $result = $this->model_statistics->list_statistics($searchData, $page, self::NUM_PER_PAGE);

        // 分页配置
        $pageConfig = array(
            "page"     => $page,
            "npp"      => self::NUM_PER_PAGE,
            "allCount" => $result[1]
        );
        $pages = get_pagination($pageConfig);
        $pageInfo = array(
            "tj"       => $result[2][0],
            "list"     => $result[0],
            "search"   => $searchData,
            "pages"    => $pages,
        );

        echo $this->load->view("statistics/index", $pageInfo, TRUE);
    }

    // 对账详情
    public function userDetail()
    {
        $page = intval($this->input->get("p"));
        $page = $page <= 1 ? 1 : $page;
        $searchData = array(
            "date" => $this->input->get("date", TRUE),
            "searchType" => $this->input->get("searchType", TRUE)
        );
        //权限判断
        if($searchData['searchType'] == 'day')
        {
            $this->check_capacity('9_1_2');
        }
        else
        {
            $this->check_capacity('9_1_4');
        }
        // 查询条件
        $result = $this->model_statistics->getUserDetail($searchData, $page, self::NUM_PER_PAGE);

        // 分页配置
        $pageConfig = array(
            "page"     => $page,
            "npp"      => self::NUM_PER_PAGE,
            "allCount" => $result[1]
        );
        $pages = get_pagination($pageConfig);
        $pageInfo = array(
            "tj"       => $result[2][0],
            "list"     => $result[0],
            "search"   => $searchData,
            "pages"    => $pages,
        );
        $this->load->view("statistics/userDetail", $pageInfo);
    }

    // 合作商对账
    public function partner()
    {
        $page = intval($this->input->get("p"));
        $page = $page <= 1 ? 1 : $page;
        $searchData = array(
            "start_time"           => $this->input->get("start_time", TRUE),
            "end_time"             => $this->input->get("end_time", TRUE),
            "searchType"           => $this->input->get("searchType", true),
            "partnerType"           => $this->input->get("partnerType", true),
        );
        if(empty($searchData['searchType'])) $searchData['searchType'] = 'day';
        //权限判断
        if($searchData['searchType'] == 'day')
        {
            $this->check_capacity('9_2_1');
        }
        else
        {
            $this->check_capacity('9_2_2');
        }
        // 合作商统计
        $partners = $this->model_statistics->getPartner();
        if(empty($searchData['partnerType'])) $searchData['partnerType'] = $partners[0]['seller'];
        $this->filterTime($searchData['start_time'], $searchData['end_time']);

        // 查询条件
        $result = $this->model_statistics->partner_statistics($searchData, $page, self::NUM_PER_PAGE);
    
        // 分页配置
        $pageConfig = array(
            "page"     => $page,
            "npp"      => self::NUM_PER_PAGE,
            "allCount" => $result[1]
        );
        $pages = get_pagination($pageConfig);
        $pageInfo = array(
            "partners" => $partners,
            "list"     => $result[0],
            "search"   => $searchData,
            "pages"    => $pages,
        );

        echo $this->load->view("statistics/partner", $pageInfo, TRUE);
    }
    
    /**
     * 对账查询
     */
    public function balance()
    {
    	$this->check_capacity('9_3_1');
    	$this->load->view("statistics/balance");
    }
    
    /**
     * 票商对账查询
     */
    public function selectBalance()
    {
        $tabId = $this->input->get("name", true);
        //权限判断
        if($tabId == '1')
        {
            $this->check_capacity('9_3_1', true);
        }
        elseif ($tabId == '2')
        {
            $this->check_capacity('9_3_2', true);
        }
        else
        {
            $this->check_capacity('9_3_3', true);
        }
    	$page = intval($this->input->get("page"));
    	$page = $page <= 1 ? 1 : $page;
    	$searchData = array(
    		"config_id" => $this->input->get("config_id", true),
    		"start_time" => $this->input->get("start_time", true),
    		"end_time" => $this->input->get("end_time", true),
    	);
    	
    	if (empty($searchData['start_time']) && empty($searchData['end_time'])) 
    	{
    	    $searchData['end_time'] = date("Y-m-d 23:59:59", strtotime("-1 day"));
    	    $searchData['start_time'] = date("Y-m-d 00:00:00", strtotime("-1 day"));
    	}
    	if(!in_array($tabId, array('1', '2', '3')))
    	{
    		$this->ajaxReturn('n', '操作失败');
    	}
    	
    	$names = $this->model_statistics->getDataCheckNames($tabId);
    	array_unshift($names, array('id' => 'all', name => '全部'));
    	if(empty($searchData['config_id']))
    	{
    		$searchData['config_id'] = $names[0]['id'];
    	}
    	$result = $this->model_statistics->listCheckData($searchData, $tabId, $page, self::NUM_PER_PAGE);
    	$datas = array();
    	foreach ($result['0'] as $value)
    	{
    		$data = array();
    		$data['id'] = $value['id'];
    		$data['config_id'] = $value['config_id'];
    		$data['name'] = $value['name'];
    		$data['date'] = $value['date'];
    		$data['difference'] = m_format($value['s_money'] - $value['o_money']);
    		$data['s_money'] = m_format($value['s_money']);
    		$data['o_money'] = m_format($value['o_money']);
    		$data['status'] = $this->getStatusName($value['status']);
    		$data['e_flag'] = $value['e_flag'];
    		$data['reset'] = $value['r_flag'] ? 1 : (strtotime($value['date']) < strtotime("-8 day") ? 1 : 0);
    		$data['mark'] = $value['mark'];
    		$datas[] = $data;
    	}
    	
    	$infos = array(
    		'datas' => $datas,
    		'search' => $searchData,
    		'page' => $page,
    		'size' => self::NUM_PER_PAGE,
    		'count' => $result[1],
    		'names' => $names,
    	);
    	
    	$this->ajaxReturn('y', '操作成功', $infos);
    }
    
    /**
     * 对账查询导出功能
     */
    public function exportBalance()
    {
        $tabId = $this->input->get("name", true);
        //权限判断
        if($tabId == '1')
        {
            $this->check_capacity('9_3_4');
        }
        elseif ($tabId == '2')
        {
            $this->check_capacity('9_3_5');
        }
        else
        {
            $this->check_capacity('9_3_6');
        }
    	$searchData = array(
    		"config_id" => $this->input->get("config_id", true),
    		"start_time" => $this->input->get("start_time", true),
    		"end_time" => $this->input->get("end_time", true),
    	);
    	if (empty($searchData['start_time']) && empty($searchData['end_time']))
    	{
    	    $searchData['end_time'] = date("Y-m-d 23:59:59", strtotime("-1 day"));
    	    $searchData['start_time'] = date("Y-m-d 00:00:00", strtotime("-1 day"));
    	}
    	$datas = array();
    	if(in_array($tabId, array('1', '2', '3')))
    	{
    		$result = $this->model_statistics->listCheckData($searchData, $tabId);
    		$datas = $result[0];
    	}
    	
    	header('Content-Type: application/vnd.ms-excel;charset=GBK');
    	header('Content-Disposition: attachment;filename="' . date("Y_m_d_H_i_s") . '.xls"');
    	header('Cache-Control: max-age=0');
    	
    	echo mb_convert_encoding('渠道', "GBK", "UTF-8") . "\t";
    	echo mb_convert_encoding('日期', "GBK", "UTF-8") . "\t";
    	echo mb_convert_encoding('网站', "GBK", "UTF-8") . "\t";
    	echo mb_convert_encoding('渠道', "GBK", "UTF-8") . "\t";
    	echo mb_convert_encoding('差额', "GBK", "UTF-8") . "\t";
    	echo mb_convert_encoding('状态', "GBK", "UTF-8") . "\t\n";
    	foreach ($datas as $row)
    	{
    		echo mb_convert_encoding($row['name'], "GBK", "UTF-8") . "\t";
    		echo $row['date'] . "\t";
    		$difference = m_format($row['s_money'] - $row['o_money']);
    		echo m_format($row['s_money']) . "\t";
    		echo m_format($row['s_money']) . "\t";
    		echo $difference . "\t";
    		echo mb_convert_encoding($this->getStatusName($row['status']), "GBK", "UTF-8") . "\t\n";
    	}
    }
    
    /**
     * 对账错误订单导出功能
     */
    public function exporterrorBalance()
    {
        $tabId = $this->input->get("name", true);
        //权限判断
        if($tabId == '1')
        {
            $this->check_capacity('9_3_4');
        }
        elseif ($tabId == '2')
        {
            $this->check_capacity('9_3_5');
        }
        else
        {
            $this->check_capacity('9_3_6');
        }
    	$config_id = $this->input->get("config_id", true);
    	$date = $this->input->get("date", true);
    	$datas = array();
    	if(in_array($tabId, array('1', '2', '3')))
    	{
    		$result = $this->model_statistics->listErrorCheckData($config_id, $tabId, $date);
    		$datas = $result[0];
    	}
    	 
    	header('Content-Type: application/vnd.ms-excel;charset=GBK');
    	header('Content-Disposition: attachment;filename="' . date("Y_m_d_H_i_s") . '.xls"');
    	header('Cache-Control: max-age=0');
    	 
    	echo mb_convert_encoding('差错订单号', "GBK", "UTF-8") . "\t";
    	echo mb_convert_encoding('日期', "GBK", "UTF-8") . "\t";
    	echo mb_convert_encoding('网站状态', "GBK", "UTF-8") . "\t";
    	echo mb_convert_encoding('渠道状态', "GBK", "UTF-8") . "\t";
    	echo mb_convert_encoding('网站金额', "GBK", "UTF-8") . "\t";
    	echo mb_convert_encoding('渠道金额', "GBK", "UTF-8") . "\t\n";
    	foreach ($datas as $row)
    	{
    		echo mb_convert_encoding("'" . $row['trade_no'], "GBK", "UTF-8") . "\t";
    		echo $row['date'] . "\t";
    		$status = $this->getErrorStatus($tabId, $row);
    		echo mb_convert_encoding($status['s_status'], "GBK", "UTF-8") . "\t";
    		echo mb_convert_encoding($status['o_status'], "GBK", "UTF-8") . "\t";
    		echo m_format($row['s_money']) . "\t";
    		echo m_format($row['o_money']) . "\t\n";
    	}
    }
    
    /**
     * 差错记录列表
     */
    public function errorBalance()
    {
        $tabId = $this->input->get("name", true);
        //权限判断
        if($tabId == '1')
        {
            $this->check_capacity('9_3_4');
        }
        elseif ($tabId == '2')
        {
            $this->check_capacity('9_3_5');
        }
        else
        {
            $this->check_capacity('9_3_6');
        }
    	$config_id = $this->input->get("config_id", true);
    	$date = $this->input->get("date", true);
    	$datas = array();
    	$name = '';
    	if(in_array($tabId, array('1', '2', '3')))
    	{
    		$result = $this->model_statistics->listErrorCheckData($config_id, $tabId, $date);
    		foreach ($result[0] as $value)
    		{
    			$data = array();
    			$data['trade_no'] = $value['trade_no'];
    			$status = $this->getErrorStatus($tabId, $value);
    			$data['s_status'] = $status['s_status'];
    			$data['o_status'] = $status['o_status'];
    			$data['s_money'] = m_format($value['s_money']);
    			$data['o_money'] = m_format($value['o_money']);
    			$datas[] = $data;
    		}
    		
    		$name = $result[1];
   
    	}
    	$infos = array(
    		'datas' => $datas,
    		'name' => $name,
    		'tabId' => $tabId,
    		'date' => $date,
    		'config_id' => $config_id,
    	);
    	
    	$this->load->view("statistics/errorBalance", $infos);
    }
    
    
    /**
     * 设置获取比对标识
     */
    public function resetBalance()
    {
        $tabId = $this->input->get("name", true);
        //权限判断
        if($tabId == '1')
        {
            $this->check_capacity('9_3_4', true);
        }
        elseif ($tabId == '2')
        {
            $this->check_capacity('9_3_5', true);
        }
        else
        {
            $this->check_capacity('9_3_6', true);
        }

    	$id = $this->input->get("id", true);
    	$flag = $this->input->get("flag", true);
    	if(in_array($tabId, array('1', '2', '3')))
    	{
    		$result = $this->model_statistics->updateDataCheckTotal($id, $tabId, array('r_flag' => $flag));
    		if($result)
    		{
    			$this->ajaxReturn('y', '操作成功');
    		}
    	}
    	
    	$this->ajaxReturn('n', '操作失败');
    }
    
    /**
     * 设置备注信息
     */
    public function markBalance()
    {
        $tabId = $this->input->post("name", true);
        //权限判断
        if($tabId == '1')
        {
            $this->check_capacity('9_3_4', true);
        }
        elseif ($tabId == '2')
        {
            $this->check_capacity('9_3_5', true);
        }
        else
        {
            $this->check_capacity('9_3_6', true);
        }
    	$id = $this->input->post("id", true);
    	$mark = $this->input->post("mark", true);
    	if(in_array($tabId, array('1', '2', '3')))
    	{
    		$result = $this->model_statistics->updateDataCheckTotal($id, $tabId, array('mark' => $mark));
    		if($result)
    		{
    			$this->ajaxReturn('y', '操作成功');
    		}
    	}
    	 
    	$this->ajaxReturn('n', '操作失败');
    }
    
    /**
     * 返回状态名称
     * @param unknown_type $status
     */
    private function getStatusName($status)
    {
    	$statusName = array(
    		'0' => '对账中',
    		'1' => '已对账'
    	);
    	
    	return $statusName[$status];
    }
    
    /**
     * 返回差错订单状态
     * @param unknown_type $tabId
     * @param unknown_type $data
     * @return multitype:string
     */
    private function getErrorStatus($tabId, $data)
    {
    	if($tabId == 1)
    	{
    		$status = array(
    			's_status' => $data['s_status'] ? '出票成功' : '出票失败',
    			'o_status' => $data['o_status'] ? '出票成功' : '出票失败',
    		);
    	}
    	elseif ($tabId == 2)
    	{
    		$status = array(
    			's_status' => $data['s_status'] ? '充值成功' : '未支付',
    			'o_status' => $data['o_status'] ? '充值成功' : '未支付',
    		);
    	}
    	else
    	{
    		$status = array(
    			's_status' => $data['s_status'] ? '提现成功' : '提现失败',
    			'o_status' => $data['o_status'] ? '提现成功' : '提现失败',
    		);
    	}
    	
    	if($data['o_status'] && empty($data['s_status']) && empty($data['s_money']))
    	{
    	    $status['s_status'] = '无此订单';
    	}
    	
    	if($data['s_status'] && empty($data['o_status']) && empty($data['o_money']))
    	{
    		$status['o_status'] = '无此订单';
    	}
    	 
    	return $status;
    }
}
