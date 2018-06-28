<?php
class index extends My_Controller
{
	public function __construct() 
	{
		parent::__construct();
		$this->load->model ( 'cp_channel', 'channel' );
	}
    /**
     * [home 后台首页]
     * @author LiKangJian 2017-05-02
     * @return [type] [description]
     */
	public function home()
	{
	    $id = $this->session->userdata ( 'id' );
	    if(!$id) $this->redirect ('/chansys/index/login' );
        
	    $user = $this->channel->getUser($id);
	    $channels = $this->channel->getChannels(explode(',', $user['channels']));
        $page = intval($this->input->get("p"));
        $page = $page <= 1 ? 1 : $page;
        $start_time = $this->input->get("start_time", true);
        $end_time = $this->input->get("end_time", true);
        $searchData = array(
            "start_time"  => $start_time ? $start_time : "",
            "end_time" => $end_time ? $end_time : "",
            "channel_id" => $this->input->get("channel_id", true),
        );
        $this->filterTime($searchData['start_time'], $searchData['end_time']);
        $result = $this->channel->listCountData($searchData, $page, 20, $user['channels']);
        $pageConfig = array(
                "page"     => $page,
                "npp"      => 20,
                "allCount" => $result[1]['rows']
        );
        $pages = get_pagination($pageConfig);
        $infos = array(
            'search'    => $searchData,
            'result'   => $result[0],
            'pages'    => $pages,
            'channels' => $channels,
            'userFields' => explode(',', $user['fields']),
            'total' => $result[1],
        );
        
		$this->load->view ( 'index/home' ,$infos );
	}
    /**
     * [login 登录处理]
     * @author LiKangJian 2017-05-02
     * @return [type] [description]
     */
	public function login()
	{
        $name = $this->input->post ( 'name', true);
		$pass = $this->input->post ( 'pass', true);
		$this->cre_pubkey ();
		if ($name && $pass)
		{
			$this->decrypt($pass);
			//验证用户
			$res = $this->channel->checkUser ( $name, $pass );
			if ($res)
			{
			    if($res['status'] == '2') {
			        echo '<script>alert(\'账号被禁用，请联系市场！\')</script>';
			        $this->load->view ( 'index/login' );
			        return ;
			    } else {
			        $this->session->set_userdata ( array (
			            'id' => $res ['id'],
			            'uname' => $res ['uname'],
			        ) );
			        $this->channel->updateUser($res['id'], array('last_login_time' => date('Y-m-d H:i:s')));
			        $this->redirect ('/chansys/index/home');
			    }
			} else
			{
				echo '<script>alert(\'用户名密码错误！\')</script>';
				$this->load->view ( 'index/login' );
			}
		} else
		{
			
			if($this->session->userdata ( 'id' )) $this->redirect ('/chansys/index/home' );
			$this->load->view ( 'index/login' );
		}
	}
    /**
     * [logout 登出]
     * @author LiKangJian 2017-05-02
     * @return [type] [description]
     */
	public function logout()
	{
		
		$this->session->unset_userdata ( 'id' );
		$this->session->unset_userdata ( 'uname' );
		$this->redirect ('/chansys/index/login' );
	}
	
	//解密
	private function decrypt (&$pass)
	{
		$decrypt = '';
		$passArr = explode ( ' ', $pass );
		foreach ( $passArr as $ps )
		{
			$decrypt .= trim ( $this->tools->rsa_decrypt ( $ps, true ) );
		}
		if (! empty ( $decrypt ))
		{
			$decrypts = explode ( '<PSALT>', $decrypt );
			$pass = $decrypts [0];
		}
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
     * [updateChannelPwd 更新渠道密码]
     * @author Likangjian  2017-04-30
     * @return [type] [description]
     */
    public function updateChannelPwd()
    {
        $post = $this->input->post();
        if(! preg_match( '/^\S{6,16}$/', trim($post['oldpwd']) ) ||
           ! preg_match( '/^\S{6,16}$/', trim($post['newpwd']) ) ||
           ! preg_match( '/^\S{6,16}$/', trim($post['surepwd']) )
          )
        {
            $this->ajaxReturn('ERROR', '密码应在6~16位之间~');
            exit;
        }
        if(trim($post['surepwd'])!=trim($post['newpwd']))
        {
            $this->ajaxReturn('ERROR', '两次输入密码不一致~');
            exit;
        }
        $post['id'] = $this->session->userdata ( 'id' );
        $tag = $this->channel->updateChannelPwd($post);
        if($tag === -1)
        {
            $this->ajaxReturn('ERROR', '旧密码错误~');
            exit;
        }elseif($tag === -2)
        {
            $this->ajaxReturn('ERROR', '渠道不存在~');
            exit;

        }elseif($tag === false)
        {
            $this->ajaxReturn('ERROR', '修改密码失败~');
            exit;
        }else
        {
            $this->session->unset_userdata ( 'id' );
            $this->session->unset_userdata ( 'uname' );
            $this->ajaxReturn('SUCCESS', '恭喜您，修改密码成功~');
            exit;
        }
    }
    /**
     * [export description]
     * @author LiKangJian 2017-05-02
     * @return [type] [description]
     */
    public function export()
    {
        $id = $this->session->userdata ( 'id' );
        $user = $this->channel->getUser($id);
        if(!$user) $this->redirect ('/chansys/index/login' );
        
        $channels = $this->channel->getChannels(explode(',', $user['channels']));
        $start_time = $this->input->get("start_time", true);
        $channel_id = $this->input->get('channel_id', true);
        $end_time = $this->input->get("end_time", true);
        $searchData = array(
            "start_time"  => $start_time ? $start_time : "",
            "end_time" => $end_time ? $end_time : "",
            "channel_id" => $channel_id,
        );
        $this->filterTime($searchData['start_time'], $searchData['end_time']);
        $data = $this->formatExportData( $this->channel->getExportData($searchData, $user['channels']), $channels, explode(',', $user['fields']));
        $this->exportExcel($data[0] , $data[1] , $data[2]);
    }
    /**
     * [formatExportData 数据整理]
     * @author LiKangJian 2017-05-02
     * @param  [type] $data [description]
     * @return [type]       [description]
     */
    function formatExportData($data, $channels, $userFields)
    {
        $newdata = array();
        $title = array('日期', '渠道名称');
        $tableHead = array(
            'unit_price' => '分成比例/单价',
            'balance_active' => '新增激活',
            'balance_reg' => '注册',
            'balance_real' => '实名',
            'balance_yj' => '分成金额（元）',
            'balance_amount' => '渠道购彩（元）',
            'partner_lottery_num' => '渠道购彩总人数',
            'partner_active_lottery_num' => '新用户购彩人数',
            'partner_curr_lottery_total_amount' => '新用户购彩总额',
        );
        foreach ($tableHead as $key => $val) {
            if(in_array($key, $userFields)) {
                $title[] = $val;
            }
        }
        
        foreach ($data as $k => $v) {
            $newdata[$k][] = $v['date'];
            $newdata[$k][] = $channels[$v['channel_id']]['name'];
            if(in_array('unit_price', $userFields)) {
                $newdata[$k][] = $v['settle_mode'] == 1 ? m_format($v['unit_price']) : $v['unit_price'] . '%';
            }
            if(in_array('balance_active', $userFields)) {
                $newdata[$k][] = $v['cpstate'] == '2' ? $v['balance_active'] : '--';
            }
            if(in_array('balance_reg', $userFields)) {
                $newdata[$k][] = $v['balance_reg'];
            }
            if(in_array('balance_real', $userFields)) {
                $newdata[$k][] = $v['balance_real'];
            }
            if(in_array('balance_yj', $userFields)) {
                $newdata[$k][] = ($v['cpstate'] == '2' || $v['settle_mode'] == '2') ? m_format($v['balance_yj']) : '--';
            }
            if(in_array('balance_amount', $userFields)) {
                $newdata[$k][] = m_format($v['balance_amount']);
            }
            if(in_array('partner_lottery_num', $userFields)) {
                $newdata[$k][] = $v['partner_lottery_num'];
            }
            if(in_array('partner_active_lottery_num', $userFields)) {
                $newdata[$k][] = $v['partner_active_lottery_num'];
            }
            if(in_array('partner_curr_lottery_total_amount', $userFields)) {
                $newdata[$k][] = m_format($v['partner_curr_lottery_total_amount']);
            }
        }
        $filename = '彩票_'.date('Y_m_d');
        
        return array($title,$newdata,$filename);
    }
    /**
     * [exportExcel 导出]
     * @author LiKangJian 2017-05-02
     * @param  array  $title    [description]
     * @param  array  $data     [description]
     * @param  string $fileName [description]
     * @return [type]           [description]
     */
    public function exportExcel($title = array(),$data = array(),$fileName='export')
    {
        header("Content-type:application/octet-stream");
        header("Accept-Ranges:bytes");
        header("Content-type:application/vnd.ms-excel");  
        header("Content-Disposition:attachment;filename=".$fileName.".xls");
        header('Cache-Control: max-age=0');
        header("Pragma: no-cache");
        header("Expires: 0");
        //循环表头
        if(count( $title ) >0 )
        {
            foreach ($title as $k => $v) 
            {
                if($k!= count( $title ) -1)
                {
                    echo mb_convert_encoding($v, "GBK", "UTF-8") . "\t";
                }else{
                    echo mb_convert_encoding($v, "GBK", "UTF-8") . "\t\n";
                }
                
            }
        }
        //循环表内容
        if(count( $data ) > 0)
        {
            foreach ($data as $k => $v) 
            {
                foreach ($v as $k1 => $v1) 
                {
                     if($k1!= count( $v ) -1)
                    {
                        echo mb_convert_encoding($v1, "GBK", "UTF-8") . "\t";
                    }else{
                        echo mb_convert_encoding($v1, "GBK", "UTF-8") . "\t\n";
                    } 
                }

            }
        }

    }
    
    /**
     * 导出对账单
     */
    public function writeExcel()
    {
        $id = $this->session->userdata ( 'id' );
        $user = $this->channel->getUser($id);
        if(!$user) $this->redirect ('/chansys/index/login' );
        
        $start_time = $this->input->get("start_time", true);
        $channel_id = $this->input->get('channel_id', true);
        $end_time = $this->input->get("end_time", true);
        $searchData = array(
            "start_time"  => $start_time ? $start_time : "",
            "end_time" => $end_time ? $end_time : "",
            "channel_id" => $channel_id,
        );
        
        $this->filterTime($searchData['start_time'], $searchData['end_time']);
        $data = $this->channel->getExportBalanceData($searchData, $user['channels']);
        $money = isset($data['balance_yj']) ? '¥' . m_format($data['balance_yj']) : '¥0.00';
        
        $startDate = date('Y年m月', strtotime($searchData['start_time']));
        $title = "{$startDate}对账单";
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
        header('Content-Disposition: attachment;filename="【待结】公司对账单-' . $startDate . '.xls"');
        header('Cache-Control: max-age=0');
        
        $objWriter = PHPExcel_IOFactory::createWriter($this->excel, 'Excel5');
        $objWriter->save('php://output');
    }
    
    /**
     * 参    数：$time1 时间1
     *          $time2 时间2
     * 作    者：wangl
     * 功    能：过滤
     * 修改日期：2014.11.10
     */
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
                 
                 if (strtotime("-3 months", strtotime($time2)) > strtotime($time1))
                 {
                     $time2 = date("Y-m-d 23:59:59", strtotime("+3 months", strtotime($time1)));
                 }
             }
         }
         else
         {
             $time2 = date("Y-m-d 23:59:59");
             $time1 = date("Y-m-d 00:00:00", strtotime('-1 month'));
         }
     }
}