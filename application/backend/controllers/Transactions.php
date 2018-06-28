<?php

/**
 * Copyright (c) 2014,上海二三四五网络科技股份有限公司
 * 摘    要：交易明细
 * 作    者：wangl@2345.com
 * 修改日期：2014.11.11
 */
if ( ! defined('BASEPATH'))
{
    exit('No direct script access allowed');
}

class Transactions extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->library('tools');
        $this->load->model('Model_transactions');
        $this->config->load('msg_text');
        $this->msg_text_cfg = $this->config->item('msg_text_cfg');
        $this->config->load('pay');
        foreach ($this->config->item('pay_all_cfg') as $key => $value)
        {
            $this->$key = $value;
        }
    }

    /**
     * 参    数：无
     * 作    者：wangl
     * 功    能：交易明细列表
     * 修改日期：2014.11.05
     */
    public function index()
    {
        $this->check_capacity('1_2_1');
        $page = intval($this->input->get("p"));
        $page = $page <= 1 ? 1 : $page;
        $fromType = $this->input->get("fromType", TRUE); //来源
        $searchData = array(
            "name"        => $this->input->get("name", TRUE),
            "start_time"  => $this->input->get("start_time", TRUE),
            "end_time"    => $this->input->get("end_time", TRUE),
            "trade_no"    => $this->input->get("trade_no", TRUE),
            "start_money" => $this->input->get("start_money", TRUE),
            "end_money"   => $this->input->get("end_money", TRUE),
            "jylx"        => $this->input->get("jylx", TRUE),
            "is_shouru"   => $this->input->get("is_shouru", TRUE),
            "is_zhichu"   => $this->input->get("is_zhichu", TRUE),
            "uid"         => $this->input->get("uid", TRUE) //区分是否来自用户详情页面
        );
        $this->filterTime($searchData['start_time'], $searchData['end_time']);
        $result = $this->Model_transactions->list_transactions($searchData, $page, self::NUM_PER_PAGE);

        $pageConfig = array(
            "page"     => $page,
            "npp"      => self::NUM_PER_PAGE,
            "allCount" => $result[1]
        );
        $pages = get_pagination($pageConfig);

        $pageInfo = array(
            "trans"    => $result[0],
            "tj"       => $result[2],
            "pages"    => $pages,
            "search"   => $searchData,
            "fromType" => $fromType
        );
        $this->load->view("transactions", $pageInfo);
    }

    /**
     * 参    数：无
     * 作    者：wangl
     * 功    能：充值列表
     * 修改日期：2014.11.05
     */
    public function list_recharge()
    {
        $this->check_capacity('1_3_1');
        $page = intval($this->input->get("p"));
        $page = $page <= 1 ? 1 : $page;
        $searchData = array(
            "name"         => $this->input->get("name", TRUE),
            "start_time"   => $this->input->get("start_time", TRUE),
            "end_time"     => $this->input->get("end_time", TRUE),
            "start_r_time" => $this->input->get("start_r_time", TRUE),
            "end_r_time"   => $this->input->get("end_r_time", TRUE),
            "start_money"  => $this->input->get("start_money", TRUE),
            "platform"     => $this->input->get("platform", TRUE),
            "end_money"    => $this->input->get("end_money", TRUE),
            "rtype"        => $this->input->get("rtype", TRUE),
            "rtype1"       => $this->input->get("rtype1", TRUE),
            "mark"         => $this->input->get("mark", TRUE),
        	"registerChannel" => $this->input->get("registerChannel", true),
            "reg_type"     => $this->input->get("reg_type", true),
        );
        $searchData["platform"] = $searchData["platform"] - 1;
        if (empty($searchData['start_time'])) {
            //$searchData['start_time'] = date('Y-m-d 00:00:00');
            $searchData['start_time'] = date('Y-m-d H:00:00', strtotime("-6 hour"));
        }
        if (empty($searchData['end_time'])) {
            $searchData['end_time'] = date('Y-m-d 23:59:59');
        }
        $this->filterTime($searchData['start_time'], $searchData['end_time']);
        $result = $this->Model_transactions->list_recharge($searchData, $page, self::NUM_PER_PAGE);

        $pageConfig = array(
            "page"     => $page,
            "npp"      => self::NUM_PER_PAGE,
            "allCount" => $result[1]
        );
        $pages = get_pagination($pageConfig);
        $this->load->model('model_channel');
        $channelRes = $this->model_channel->getChannels();
        foreach ($channelRes as $val){
        	$channels[$val['id']] = $val;
        }
        $pageInfo = array(
            "trans"  => $result[0],
            "tj"     => $result[2],
            "pages"  => $pages,
            "search" => $searchData,
        	'channels' => $channels,
        );
        $this->load->view("recharge", $pageInfo);
    }
    
    /**
     * 补单查询 权限检查操作
     */
    public function orderSelect()
    {
        $this->check_capacity('1_3_2', true);
        $this->ajaxReturn('y', '成功');
    }
    
    /**
     * 退款权限检查
     */
    public function refundCheck()
    {
        $this->check_capacity('1_3_4', true);
        $this->ajaxReturn('y', '成功');
    }

    /**
     * 参    数：无
     * 作    者：wangl
     * 功    能：提款列表
     * 修改日期：2014.11.05
     */
    public function list_withdraw()
    {
        $this->check_capacity('1_4_1');
        $page = intval($this->input->get("p"));
        $page = $page <= 1 ? 1 : $page;
        $searchData = array(
            "name"         => $this->input->get("name", TRUE),
            "trade_no"     => $this->input->get("trade_no", TRUE),
            "start_time"   => $this->input->get("start_time", TRUE),
            "end_time"     => $this->input->get("end_time", TRUE),
            "start_r_time" => $this->input->get("start_r_time", TRUE),//已废弃
            "end_r_time"   => $this->input->get("end_r_time", TRUE),//已废弃
            "start_money"  => $this->input->get("start_money", TRUE),
            "end_money"    => $this->input->get("end_money", TRUE),
            "platform"     => $this->input->get("platform", TRUE),
            "rtype"        => $this->input->get("rtype", TRUE),	//已废弃
            "rtype1"       => $this->input->get("rtype1", TRUE),//已废弃
            "ctype"        => $this->input->get("ctype", TRUE),
            "channel"      => $this->input->get("channel", TRUE)
        );
        $searchData["platform"] = $searchData["platform"] - 1;
        $this->filterTime($searchData['start_time'], $searchData['end_time']);
        $result = $this->Model_transactions->list_withdraw($searchData, $page, self::NUM_PER_PAGE);

        $pageConfig = array(
            "page"     => $page,
            "npp"      => self::NUM_PER_PAGE,
            "allCount" => $result[1]
        );
        $pages = get_pagination($pageConfig);

        $pageInfo = array(
            "trans"  => $result[0],
            "tj"     => $result[2],
            "pages"  => $pages,
            "search" => $searchData
        );
        $this->load->view("withdrawal", $pageInfo);
    }

    /**
     * 参    数：无
     * 作    者：wangl
     * 功    能：提款审核开发
     * 修改日期：2014.11.05
     */
    public function list_check()
    {
        $this->check_capacity('2_1_1');
        $page = intval($this->input->get("p"));
        $page = $page <= 1 ? 1 : $page;
        $searchData = array(
            "name"       => $this->input->get("name", TRUE),
            "trade_no"   => $this->input->get("trade_no", TRUE),
            "rtype"      => $this->input->get("rtype", TRUE),//已废弃
            "rtype1"     => $this->input->get("rtype1", TRUE),//已废弃
            "start_time" => $this->input->get("start_time", TRUE),
            "end_time"   => $this->input->get("end_time", TRUE),
            "ctype"      => $this->input->get("ctype", TRUE)
        );
        $this->filterTime($searchData['start_time'], $searchData['end_time']);
        $result = $this->Model_transactions->list_withdraw($searchData, $page, 40, TRUE);
        $pageConfig = array(
            "page"     => $page,
            "npp"      => 40,
            "allCount" => $result[1]
        );
        $pages = get_pagination($pageConfig);
        $pageInfo = array(
            "checks" => $result[0],
            "pages"  => $pages,
            "search" => $searchData,
            "tj"     => $result[2]
        );
        $this->load->view("withdrawal_check", $pageInfo);
    }

    /**
     * 参    数：无
     * 作    者：wangl
     * 功    能：停止用户撤销操作
     * 修改日期：2014.11.05
     */
    public function stop_cancel()
    {
        $this->check_capacity('4_3', TRUE);
        $ids = $this->input->post("ids", TRUE);
        if ($ids != '')
        {
            $allid = explode(",", $ids);
            $time = date("Y-m-d H:i:s", time());
            foreach ($allid as $id)
            {
                $row = $this->Model_transactions->update_check(array(
                    "modified"    => $time,
                    "status"      => 1,
                    "start_check" => $time
                ), array(
                    "trade_no" => $id
                ));

                if ( ! $row)
                {
                    $error[] = $id;
                }
            }
            if ( ! empty($error))
            {
                return $this->ajaxReturn('n', $this->msg_text_cfg['falied'], $error);
            }
            $this->syslog(6, "停止撤销申请，订单ID:{$ids}");

            return $this->ajaxReturn('y', $this->msg_text_cfg['success']);
        }
    }

    /**
     * 参    数：无
     * 作    者：wangl
     * 功    能：导出提款列表
     * 修改日期：2014.11.05
     */
    public function export()
    {
        $this->check_capacity('1_4_1');
        $searchData = array(
            "name"        => $this->input->get("name", TRUE),
            "start_time"  => $this->input->get("start_time", TRUE),
            "end_time"    => $this->input->get("end_time", TRUE),
            "start_money" => $this->input->get("start_money", TRUE),
            "end_money"   => $this->input->get("end_money", TRUE),
            "rtype"       => $this->input->get("rtype", TRUE),
            "rtype1"      => $this->input->get("rtype1", TRUE),
            "ctype"       => $this->input->get("ctype", TRUE)
        );
        $this->filterTime($searchData['start_time'], $searchData['end_time']);
        $result = $this->Model_transactions->get_export_data($searchData);
        header('Content-Type: application/vnd.ms-excel;charset=GBK');
        header('Content-Disposition: attachment;filename="' . date("Y_m_d_H_i_s") . '.xls"');
        header('Cache-Control: max-age=0');

        echo mb_convert_encoding('订单编号', "GBK", "UTF-8") . "\t";
        echo mb_convert_encoding('用户名', "GBK", "UTF-8") . "\t";
        echo mb_convert_encoding('收款账户列', "GBK", "UTF-8") . "\t";
        echo mb_convert_encoding('收款户名列', "GBK", "UTF-8") . "\t";
        echo mb_convert_encoding('转帐金额列', "GBK", "UTF-8") . "\t";
        echo mb_convert_encoding('备注列', "GBK", "UTF-8") . "\t";
        echo mb_convert_encoding('收款银行列', "GBK", "UTF-8") . "\t";
        echo mb_convert_encoding('收款银行支行列', "GBK", "UTF-8") . "\t";
        echo mb_convert_encoding('收款省/直辖市列', "GBK", "UTF-8") . "\t";
        echo mb_convert_encoding('收款市县列', "GBK", "UTF-8") . "\t\n";


        // echo '订单编号' . "\t";
        // echo '用户名' . "\t";
        // echo '收款账户列' . "\t";
        // echo '收款户名列' . "\t";
        // echo '转帐金额列'. "\t";
        // echo '备注列'. "\t";
        // echo '收款银行列'. "\t";
        // echo '收款银行支行列' . "\t";
        // echo '收款省/直辖市列' . "\t";
        // echo '收款市县列' . "\t\n";

        foreach ($result as $row)
        {
            if (in_array($row['bank_province'], array(
                "上海",
                "北京",
                "重庆",
                "天津"
            )))
            {
                $fenhang = $row['bank_province'] . "市";
            }
            else
            {
                $fenhang = $row['bank_province'] . "省";
            }
            $bank_tmp = explode("（", $this->pay_cfg['chinabank']['child'][$row['bank_name']][0]);
            echo mb_convert_encoding("'" . $row['trade_no'], "GBK", "UTF-8") . "\t";
            echo mb_convert_encoding("'" . $row['uname'], "GBK", "UTF-8") . "\t";
            echo mb_convert_encoding("'" . $row['bank_id'], "GBK", "UTF-8") . "\t";
            echo mb_convert_encoding($row['real_name'], "GBK", "UTF-8") . "\t";
            echo m_format($row['money']) . "\t";
            echo mb_convert_encoding("提现", "GBK", "UTF-8") . "\t";
            echo mb_convert_encoding($bank_tmp[0], "GBK", "UTF-8") . "\t";
            echo mb_convert_encoding($fenhang . "分行营业部", "GBK", "UTF-8") . "\t";
            echo mb_convert_encoding($row['bank_province'], "GBK", "UTF-8") . "\t";
            echo mb_convert_encoding($row['bank_city'], "GBK", "UTF-8") . "\t\n";


            // echo "'" . $row['trade_no'] . "\t";
            // echo "'" . $row['uname'] . "\t";
            // echo "'" . $row['bank_id'] . "\t";
            // echo $row['real_name'] . "\t";
            // echo m_format($row['money']) . "\t";
            // echo "提现" . "\t";
            // echo $bank_tmp[0] . "\t";
            // echo $fenhang . "分行营业部" . "\t";
            // echo $row['bank_province']. "\t";
            // echo $row['bank_city'] . "\t\n";

        }
        $this->syslog(6, "导出提款数据");
        unset($result);
    }

    /**
     * 参    数：无
     * 作    者：wangl
     * 功    能：审核提款
     * 修改日期：2014.11.05
     */
    public function check()
    {
        $this->check_capacity('2_1_3', TRUE);
        $trade_no = $this->input->post("hid_order_id", TRUE);
        $content = str_hsc($this->input->post("content", TRUE));
        $ctype = intval($this->input->post("hid_status"));

        $row = $this->Model_transactions->check($trade_no, $ctype, $content);
        if ($row == FALSE)
        {
            return $this->ajaxReturn('n', $this->msg_text_cfg['falied']);
        }
        $this->syslog(6, "提款审核，订单ID:{$trade_no}，审核状态：" . $this->jylx_cfg[$ctype]);

        return $this->ajaxReturn('y', $this->msg_text_cfg['success']);
    }

    /**
     * 参    数：无
     * 作    者：shigx
     * 功    能：批量审核提款
     * 修改日期：2015.03.04
     */
    public function batch_check()
    {
        $this->check_capacity('2_1_2', TRUE);
        $trade_nos = $this->input->post("hid_order_ids", TRUE);
        $ctype = intval($this->input->post("hid_status"));
        if ($trade_nos)
        {
            $suc_trade_no = array();
            $err_trade_no = array();
            $trade_nos = explode(',', $trade_nos);
            foreach ($trade_nos as $trade_no)
            {
                $row = $this->Model_transactions->check($trade_no, $ctype, '');
                if ($row == FALSE)
                {
                    array_push($err_trade_no, $trade_no);
                }
                else
                {
                    array_push($suc_trade_no, $trade_no);
                }
            }

            if ($suc_trade_no)
            {
                $trade_no_str = implode(',', $suc_trade_no);
                $this->syslog(6, "提款审核，订单ID:{$trade_no_str}，审核状态：" . $this->jylx_cfg[$ctype]);
            }
            $message = "操作完成，总共执行订单总数：" . count($trade_nos) . "，其中操作成功订单数：" . count($suc_trade_no) . "，操作失败订单数：" . count($err_trade_no);

            return $this->ajaxReturn('y', $message);
        }

        return $this->ajaxReturn('n', $this->msg_text_cfg['falied']);
    }

    /**
     * 参    数：无
     * 作    者：shigx
     * 功    能：将金额格式化后ajax形式返还
     * 修改日期：2015.03.04
     */
    public function get_m_format()
    {
        $this->check_capacity('2_1_3', TRUE);
        $money = intval($this->input->post("money", TRUE));
        if ($money)
        {
            $money = m_format($money);

            return $this->ajaxReturn('y', $this->msg_text_cfg['success'], array('money' => $money));
        }

        return $this->ajaxReturn('n', $this->msg_text_cfg['falied']);
    }

    /**
     * 参    数：无
     * 作    者：shigx
     * 功    能：已操作打款操作
     * 修改日期：2015.03.06
     */
    public function has_operation()
    {
        $this->check_capacity('2_1_2', TRUE);
        $ids = $this->input->post("ids", TRUE);
        if ($ids != '')
        {
            $allid = explode(",", $ids);
            $time = date("Y-m-d H:i:s", time());
            foreach ($allid as $id)
            {
                $row = $this->Model_transactions->updateReview(array(
                    "review"   => 1
                ), array(
                    "trade_no" => $id
                ));

                if ( ! $row)
                {
                    $error[] = $id;
                }
            }
            if ( ! empty($error))
            {
                return $this->ajaxReturn('n', $this->msg_text_cfg['falied'], $error);
            }
            $this->syslog(6, "已人工审核，订单ID:{$ids}");

            return $this->ajaxReturn('y', $this->msg_text_cfg['success']);
        }
    }
    
    public function list_capital() 
    {
    	$this->check_capacity('1_5_1');
    	$page = intval($this->input->get("p"));
        $page = $page <= 1 ? 1 : $page;
        $searchData = array(
            "uname"        => $this->input->get("uname", TRUE),
        	"trade_no"     => $this->input->get("trade_no", TRUE),
        	"content"      => $this->input->get("content", TRUE),
            "start_time"   => $this->input->get("start_time", TRUE),
            "end_time"     => $this->input->get("end_time", TRUE),
            "start_money"  => $this->input->get("start_money", TRUE),
            "end_money"    => $this->input->get("end_money", TRUE),
            "ctype"        => $this->input->get("ctype", TRUE)
        );
        $this->filterTime($searchData['start_time'], $searchData['end_time']);
    	$data = $this->Model_transactions->list_capital($searchData, $page, self::NUM_PER_PAGE);	
    	$pageConfig = array("page" => $page, "npp" => self::NUM_PER_PAGE, "allCount" => $data['count']['count']);
    	$data['pages'] = get_pagination($pageConfig);
    	$data['ctypeArr'] = array(6=>'返利', 7=>'不中包赔', 8=>'加奖', 9=>'红包', 10 => '其他', 12=>'网站合买保底派奖', 13=>'网站合买保底退款', 14=>'网站合买订单支出');
    	$data['search'] = $searchData;
    	$this->load->view('capital', $data);
    }
    
    public function check_adjust()
    {
    	$this->check_capacity('2_4_1');
    	$page = intval($this->input->get("p"));
    	$page = $page <= 1 ? 1 : $page;
    	$searchData = array(
    		"uname"      => $this->input->get("uname", TRUE),
    		"start_time" => $this->input->get("start_time", TRUE),
    		"end_time"   => $this->input->get("end_time", TRUE),
    		"status"     => $this->input->get("status", TRUE)
    	);
    	if ($searchData['status'] === 'all') unset($searchData['status']);
    	$this->filterTime($searchData['start_time'], $searchData['end_time']);
    	if ($searchData['status'] == '') {
    		$searchData['status'] = '01';
    	}
    	
    	$this->load->model('model_ajust_umoney', 'model');
    	$data = $this->model->listData($searchData, $page, self::NUM_PER_PAGE);
    	$pageConfig = array("page" => $page, "npp" => self::NUM_PER_PAGE, "allCount" => $data['count']['count']);
    	$data['pages'] = get_pagination($pageConfig);
    	$data['search'] = $searchData;
    	$data['typeArr'] = array(0 => '加款', 1 => '扣款');
    	$data['ctypeArr'] = array(1 => '彩金派送 ', 2 => '奖金派送', 3 => '其它加款', 4 => '其它扣款');
    	$data['ismustcostArr'] = array(0 => '可提现', 1 => '不可提现');
    	$data['iscapitalArr'] = array(0 => '否', 1 => '是');
    	$data['statusArr'] = array(0 => '待审核 ', 1 => '审核通过');
    	$data['cancheck'] = false;
    	if (in_array('2_4_2', $this->capacity)) {
    		$data['cancheck'] = true;
    	}
    	$this->load->view('check_adjust', $data);
    }
    
    public function list_adjust()
    {
    	$this->check_capacity('1_6_1');
    	$page = intval($this->input->get("p"));
    	$page = $page <= 1 ? 1 : $page;
    	$searchData = array(
    			"uname"      => $this->input->get("uname", TRUE),
    			"start_time" => $this->input->get("start_time", TRUE),
    			"end_time"   => $this->input->get("end_time", TRUE),
    			"status"     => $this->input->get("status", TRUE),
    			"type"     	 => $this->input->get("type", TRUE),
    			"ismustcost" => $this->input->get("ismustcost", TRUE),
    			"iscapital"  => $this->input->get("iscapital", TRUE),
    			"comment"  => $this->input->get("comment", TRUE),
    	);
    	if ($searchData['status'] === 'all') unset($searchData['status']);
    	if ($searchData['type'] === 'all') unset($searchData['type']);
    	if ($searchData['ismustcost'] === 'all') unset($searchData['ismustcost']);
    	if ($searchData['iscapital'] === 'all') unset($searchData['iscapital']);
    	$this->filterTime($searchData['start_time'], $searchData['end_time']);
    	$this->load->model('model_ajust_umoney', 'model');
    	$data = $this->model->listData($searchData, $page, self::NUM_PER_PAGE);
    	$pageConfig = array("page" => $page, "npp" => self::NUM_PER_PAGE, "allCount" => $data['count']['count']);
    	$data['pages'] = get_pagination($pageConfig);
    	$data['search'] = $searchData;
    	$data['typeArr'] = array(0 => '加款', 1 => '扣款');
    	$data['ctypeArr'] = array(1 => '彩金派送 ', 2 => '奖金派送', 3 => '其它加款', 4 => '其它扣款');
    	$data['ismustcostArr'] = array(0 => '可提现', 1 => '不可提现');
    	$data['iscapitalArr'] = array(0 => '否', 1 => '是');
    	$data['statusArr'] = array(0 => '待审核 ', 1 => '审核通过', 2 => '审核失败');
    	$this->load->view('adjust', $data);
    }
    
    public function adjust()
    {
    	$id = $this->input->post('id');
    	$num = $this->input->post('num');
    	$type = $this->input->post('type');
    	$this->load->model('model_ajust_umoney', 'model');
    	if ($type == 1) {
    		$res = $this->model->adjust($num);
    		if ($res === 'moneyless') {
    			exit('2');
    		}else if ($res) {
    			$this->syslog(42, "已操作审核通过，调账订单ID：{$num}");
    			exit('1');
    		}
    	}else {
    		$failreason = $this->input->post('failreason');
    		if ($this->model->adjustfail($num, $failreason)) {
    			$this->syslog(42, "已操作审核失败，调账订单ID：{$num}");
    			exit('1');
    		}
    	}
    	exit('0');
    }
    
    /**
     * 调账审核批量操作
     */
    public function adjustBatch()
    {
        $this->check_capacity('2_4_2', true);
    	$ids = $this->input->post("ids", TRUE);
    	$type = $this->input->post('type');
    	if ($ids)
    	{
    		$suc_ids = array();
    		$err_ids = array();
    		$ids = explode(',', $ids);
    		$this->load->model('model_ajust_umoney', 'model');
    		if($type == '1')
    		{
    			foreach ($ids as $id)
    			{
    				$res = $this->model->adjust($id);
    				if ($res && $res !== 'moneyless')
    				{
    					array_push($suc_ids, $id);
    				}
    				else
    				{
    					array_push($err_ids, $id);
    				}
    			}
    			 
    			if ($suc_ids)
    			{
    				$trade_no_str = implode(',', $suc_ids);
    				$this->syslog(42, "已操作审核通过，调账订单ID：{$trade_no_str}");
    			}
    		}
    		else
    		{
    			foreach ($ids as $id)
    			{
    				$res = $this->model->adjustfail($id, '');
    				if ($res)
    				{
    					array_push($suc_ids, $id);
    				}
    				else
    				{
    					array_push($err_ids, $id);
    				}
    			}
    			
    			if ($suc_ids)
    			{
    				$trade_no_str = implode(',', $suc_ids);
    				$this->syslog(42, "已操作审核失败，调账订单ID：{$trade_no_str}");
    			}
    		}
    		
    		$message = "操作完成，总共执行订单总数：" . count($ids) . "，其中操作成功订单数：" . count($suc_ids) . "，操作失败订单数：" . count($err_ids);
    	
    		return $this->ajaxReturn('y', $message);
    	}
    	
    	return $this->ajaxReturn('n', $this->msg_text_cfg['falied']);
    }
    
    /**
     * 记录操作日志
     */
    public function refundLog()
    {
    	$trade_no = $this->input->post('trade_no');
    	$money = $this->input->post('money');
    	if($trade_no && $money)
    	{
    		$message = "对充值订单：{$trade_no}进行退款操作，退款金额：{$money}元";
    		$this->syslog(4, $message);
    	}
    	exit('1');
    }
    
    /**
     * 批量补单操作
     */
    public function supplementOrder()
    {
        $this->check_capacity('1_3_3', true);
    	$ids = $this->input->post("ids", TRUE);
    	if($ids)
    	{
    		$suc_ids = array();
    		$err_ids = array();
    		$ids = explode(',', $ids);
    		foreach ($ids as $trade_no)
    		{
    			$url = $this->config->item('base_url') . "/api/recharge/orderSelect/{$trade_no}";
    			$respone = $this->tools->request($url, array(), $tout = 10);
    			$respone = json_decode($respone, true);
    			if($respone['code'] == '0' && $respone['isDone'] == '1')
    			{
    				array_push($suc_ids, $trade_no);
    			}
    		}
    		$message = "补单已完成，" . count($suc_ids) . "/" . count($ids) . "个订单补单成功";
    		return $this->ajaxReturn('y', $message);
    	}
    	
    	return $this->ajaxReturn('n', $this->msg_text_cfg['falied']);
    }
    
    public function submitBank($id)
    {
        $this->Model_transactions->updateReview(array(
            "status" => 5,
            "start_check" => date('Y-m-d H:i:s', time())
                ), array(
            "trade_no" => $id
        ));
        $this->syslog(6, "提款审核，订单ID:{$id}，已提交至银行");
        return $this->ajaxReturn('n', "处理成功");
    }
    
    public function withdrawChannel()
    {
        $this->check_capacity('2_6_1');
        $channel = $this->input->get("channel", TRUE);
        $audit = $this->input->get("audit", TRUE);
        if($channel)
        {
            $this->Model_transactions->updateWithdrawChannel($channel, $audit);
            $choses = array('tonglian' => '通联代付', 'lianlian' => '连连代付', 'xianfeng'=> '先锋代付');
            $auditArr = array('1' => '需要人工审核', '0' => '不需要人工审核');
            $this->syslog(6, "提款渠道修改为：" . $choses[$channel] . ",提现修改为：{$auditArr[$audit]}");
        }
        $channel = $this->Model_transactions->getWithdrawChannel();
        $this->load->view("withdrawChannel", $channel);
    }

    // 批量处理提款失败退款
    public function handleWithdrawFail()
    {
        $lists = $this->Model_transactions->getFailWithdraw();
        if(!empty($lists))
        {
            foreach ($lists as $items) 
            {
                $row = $this->Model_transactions->check($items['trade_no'], 8, '银行维护，请重新发起提现');
            }
        }
        var_dump('SUCCESS');die;
    }
}
