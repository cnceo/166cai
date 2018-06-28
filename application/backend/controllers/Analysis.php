<?php

class Analysis extends MY_Controller
{
    private $platform = array(
        '1' => '网页',
        '2' => 'app',
        '3' => 'IOS',
        '4' => 'M版'
    );

    public function __construct()
    {
        parent::__construct();
        $this->load->model('model_data_analysis', 'analysis');
    }

    // 概览 - 平台类型
    private $platType = array(
        'web' => 1,
        'app' => 2,
        'ios' => 3,
        'm'   => 4,
    );

    // 彩种配置
    private $lottery = array(
        51    => '双色球',
        23529 => '大乐透',
        52    => '福彩3D',
        33    => '排列三',
        35    => '排列五',
        10022 => '七星彩',
        23528 => '七乐彩',
        42    => '竞彩足球',
        43    => '竞彩篮球',
        11    => '胜负彩',
        19    => '任选九',
        21406 => '十一选五'
    );

    /**
     * 参    数：
     * 作    者：刁寿钧
     * 功    能：点击量数据页面
     * 修改日期：2015-07-23
     */
    public function click()
    {
    	$this->check_capacity('9_2');
        $this->load->model('model_click', 'click');
        list($platform, $channelId, $version, $period, $isCsv) = $this->defaultParams();

        $fetchRecords = $this->click->fetchRecordsByPlatform($platform, $channelId, $version, $period);
        $page = 'click';
        if ($platform == 2)
        {
            $page = 'clickApp';
        }
        $records = $this->formatClickRecords($fetchRecords, $page);
        $fieldsMap = $this->decideFields($page);
        //页面显示要求补全没有记录的天数
        $records = $this->fillRecords($records, $fieldsMap, $period);
        $summary = $this->summaryRecords($records, $page);
        $numFields = $this->countableFields();

        if ($isCsv)
        {
            $head = $fieldsMap;
            $body = array();
            array_push($body, $summary);
            $fields = array_keys($head);
            foreach ($records as $record)
            {
                $tempRow = array();
                foreach ($fields as $field)
                {
                    if (in_array($field, $numFields))
                    {
                        $tempRow[$field] = number_format($record[$field]);
                    }
                    else
                    {
                        $tempRow[$field] = $record[$field];
                    }
                }
                array_push($body, $tempRow);
            }
            $fileBaseName = '点击量数据列表';

            $this->writeCSV($head, $body, $fileBaseName);
        }
        else
        {
            //这里日均的概念不是平摊到时间跨度内的每一天上，而是有记录的每一天
            $count = count($records);
            $daily = $this->dailyRecords($summary, $page, $count);
            $this->load->model('model_channel', 'channel');
            $channels = $this->channel->getChannelList($platform);
            $this->load->model('model_app_version', 'appVersion');
            $appVersions = $this->appVersion->getVersionList();

            $this->load->view('analysis/click', compact('platform', 'channelId', 'appVersions', 'version', 'period',
                'isCsv', 'channels', 'daily', 'summary', 'records', 'fieldsMap'));
        }
    }

    /**
     * 参    数：page      具体页面
     *           platform  平台
     * 作    者：刁寿钧
     * 功    能：根据具体页面与平台，决定显示哪些字段，以及具体的字段名
     * 修改日期：2015-07-24
     */
    private function decideFields($page)
    {
        if ($page == 'click') {
            return array(
                'dateStr'      => '日期',
                'uv'           => 'UV',
                'pv'           => 'PV',
                'clickUser'    => '点击用户数',
                'convertRatio' => '点击转化率',
                'clickAmount'  => '点击量',
                'avgClick'     => '人均点击量',
            );
        }

        if ($page == 'clickApp') {
            return array(
                'dateStr'      => '日期',
                'newUser'      => '新增用户',
                'activeUser'   => '活跃用户',
                'clickUser'    => '点击用户数',
                'convertRatio' => '点击转化率',
                'clickAmount'  => '点击量',
                'avgClick'     => '人均点击量',
            );
        }

        if ($page == 'register')
        {
            return array(
                'dateStr'       => '日期',
                'registerUser'  => '注册用户',
                'registerRatio' => '注册转化率',
                'validUser'     => '有效用户',
                'validRatio'    => '有效用户转化率',
                'completeUser'  => '完整信息用户',
            );
        }

        return array();
    }

    /**
     * 参    数：
     * 作    者：刁寿钧
     * 功    能：取页面POST过来的参数，供点击量数据与注册数据两个页面使用
     * 修改日期：2015-07-23
     */
    private function defaultParams()
    {
        $platform = $this->input->get('platform', TRUE);
        //默认平台为网页
        $platform OR $platform = 1;
        $channelId = $this->input->get('channelId', TRUE);
        //TODO ugly
        if ($channelId == 'all')
        {
            $channelId = 0;
        }
        $version = $this->input->get('version', TRUE);
        $period = $this->input->get('period', TRUE);
        //默认看7天内数据
        $period OR $period = 7;
        $isCsv = $this->input->get('isCsv');

        return array($platform, $channelId, $version, $period, $isCsv);
    }

    /**
     * 参    数：
     * 作    者：刁寿钧
     * 功    能：指明哪些字段可格式化
     * 修改日期：2015-07-23
     */
    private function countableFields()
    {
        $countableFields = array(
            'uv',
            'pv',
            'clickUser',
            'clickAmount',
            'newUser',
            'activeUser',
            'registerUser',
            'validUser',
            'completeUser',
        );

        return $countableFields;
    }

    /**
     * 参    数：
     * 作    者：刁寿钧
     * 功    能：构造点击数据的数组，及汇总数据
     * 修改日期：2015-07-23
     */
    private function formatClickRecords($records, $page)
    {
        $formulas = $this->getFormulas($page);
        foreach ($records as & $record)
        {
            $record['dateStr'] = $this->convertDateStr($record['date']);
            $record = $this->computeRow($formulas, $record);
        }

        return $records;
    }

    /**
     * 参    数：records       数据
     *           fieldMap      字段
     *           period        最近天数
     * 作    者：刁寿钧
     * 功    能：补齐每一天的数据
     * 修改日期：2015-08-07
     */
    private function fillRecords($records, $fieldsMap, $period)
    {
        $defaultRow = array();
        $defaultKeys = empty($records[0]) ? array_keys($fieldsMap) : array_keys($records[0]);

        $countableKeys = $this->countableFields();
        foreach ($defaultKeys as $key)
        {
            if (in_array($key, $countableKeys))
            {
                $defaultRow[$key] = 0;
            }
            else
            {
                $defaultRow[$key] = '--';
            }
        }

        $dateToData = array();
        foreach ($records as $rc)
        {
            $dateToData[$rc['date']] = $rc;
        }
        $appearDates = array_keys($dateToData);

        $fullRecords = array();
        for ($jap = 1; $jap <= $period; $jap ++)
        {
            $tempDate = date('Y-m-d', strtotime("-$jap day"));
            if (in_array($tempDate, $appearDates))
            {
                array_push($fullRecords, $dateToData[$tempDate]);
            }
            else
            {
                $row = $defaultRow;
                $row['date'] = $tempDate;
                $row['dateStr'] = $this->convertDateStr($tempDate);
                array_push($fullRecords, $row);
            }
        }

        return $fullRecords;
    }

    /**
     * 参    数：
     * 作    者：刁寿钧
     * 功    能：指标计算公式
     * 修改日期：2015-07-28
     */
    private function getFormulas($page)
    {
        if ($page == 'click')
        {
            return array(
                'convertRatio' => array(
                    'numerator'   => 'clickUser',
                    'denominator' => 'uv',
                ),
                'avgClick'     => array(
                    'numerator'   => 'clickAmount',
                    'denominator' => 'clickUser',
                ),
            );
        }

        if ($page == 'clickApp')
        {
            return array(
                'convertRatio' => array(
                    'numerator'   => 'clickUser',
                    'denominator' => 'activeUser',
                ),
                'avgClick'     => array(
                    'numerator'   => 'clickAmount',
                    'denominator' => 'clickUser',
                ),
            );
        }

        if ($page == 'register')
        {
            return array(
                'registerRatio' => array(
                    'numerator'   => 'registerUser',
                    'denominator' => 'uv',
                ),
                'validRatio'    => array(
                    'numerator'   => 'validUser',
                    'denominator' => 'uv',
                ),
            );
        }

        return array();
    }

    /**
     * 参    数：formulas       公式
     *           row            单独一行数据
     * 作    者：刁寿钧
     * 功    能：根据设定的公式计算每一行数据
     * 修改日期：2015-08-07
     */
    private function computeRow($formulas, $row)
    {
        foreach ($formulas as $division => $param)
        {
            //tricky 0.00
            if (empty($row[$param['denominator']]) OR ! ($row[$param['denominator']] * 100))
            {
                $row[$division] = '--';
                continue;
            }

            $numerator = str_replace(',', '', $row[$param['numerator']]);
            $denominator = str_replace(',', '', $row[$param['denominator']]);

            if (strstr($division, 'Ratio'))
            {
                $row[$division] = number_format($numerator / $denominator * 100, 2) . '%';
            }
            else
            {
                $row[$division] = number_format($numerator / $denominator, 2);
            }
        }

        return $row;
    }

    /**
     * 参    数：
     * 作    者：刁寿钧
     * 功    能：构造汇总数据
     * 修改日期：2015-07-24
     */
    private function summaryRecords($records, $page)
    {
        $summary = array();
        $countableFields = $this->countableFields();
        if (empty($records[0]))
        {
            return array();
        }
        $keys = array_keys($records[0]);
        $summary['dateStr'] = '汇总';
        foreach ($keys as $key)
        {
            if (in_array($key, $countableFields))
            {
                $summary[$key] = 0;
            }
        }
        foreach ($records as $record)
        {
            foreach ($record as $key => $value)
            {
                if (in_array($key, $countableFields))
                {
                    $summary[$key] += $value;
                }
            }
        }

        $formulas = $this->getFormulas($page);
        $summary = $this->computeRow($formulas, $summary);

        return $summary;
    }
    
    /**
     * 参    数：
     * 作    者：刁寿钧
     * 功    能：构造日均数据
     * 修改日期：2015-07-24
     */
    private function dailyRecords($summary, $page, $period)
    {
        $daily = array();
        $countableFields = $this->countableFields();
        $keys = array_diff(array_keys($summary), array('dateStr'));
        foreach ($keys as $key)
        {
            if (in_array($key, $countableFields))
            {
                $daily[$key] = $period ? number_format($summary[$key] / $period, 2) : 0;
            }
        }

        $formulas = $this->getFormulas($page);
        $daily = $this->computeRow($formulas, $daily);

        return $daily;
    }

    /**
     * 参    数：
     * 作    者：刁寿钧
     * 功    能：格式化显示日期
     * 修改日期：2015-07-23
     */
    private function convertDateStr($date)
    {
        $weekDay = date('w', strtotime($date));
        $weekDayStr = array(
            0 => '星期天',
            1 => '星期一',
            2 => '星期二',
            3 => '星期三',
            4 => '星期四',
            5 => '星期五',
            6 => '星期六',
        );
        $dateStr = $date . ' | ' . $weekDayStr[$weekDay];
        if ($weekDay == 6)
        {
            $dateStr = '<span class="cGreen">' . $dateStr . '</span>';
        }
        if ($weekDay == 0)
        {
            $dateStr = '<span class="cRed">' . $dateStr . '</span>';
        }

        return $dateStr;
    }

    /**
     * 参    数：
     * 作    者：刁寿钧
     * 功    能：导出Excel文件
     * 修改日期：2015-07-23
     */
    private function writeExcel($head, $body, $fileBaseName)
    {
        $this->load->library('excel');
        $this->excel->setActiveSheetIndex(0);
        $this->excel->getActiveSheet()->setTitle($fileBaseName);

        //just a placeholder
        $ph = 1;
        foreach ($head as $key => $value)
        {
            $this->excel->getActiveSheet()->setCellValueExplicit($this->excel->getColumnForXls($ph) . '1',
                $value, PHPExcel_Cell_DataType::TYPE_STRING);
            $objStyle = $this->excel->getActiveSheet()->getStyle($this->excel->getColumnForXls($ph ++) . '1');
            $objBorder = $objStyle->getBorders();
            $objBorder->getTop()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
            $objBorder->getTop()->getColor()->setARGB('FFDDDDDD');
            $objBorder->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
            $objBorder->getBottom()->getColor()->setARGB('FFDDDDDD');
            $objBorder->getLeft()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
            $objBorder->getLeft()->getColor()->setARGB('FFDDDDDD');
            $objBorder->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
            $objBorder->getRight()->getColor()->setARGB('FFDDDDDD');

            $objFill = $objStyle->getFill();
            $objFill->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
            $objFill->getStartColor()->setRGB('d6edff');
        }

        $ph = 1;
        foreach ($body as $row)
        {
            $ph ++;
            //yet another placeholder
            $yap = 1;
            foreach ($head as $key => $value)
            {
                //数据分析页面中，周六周日的数据加颜色
                if (in_array($key, array('dateStr')))
                {
                    if (strstr($row[$key], '<span class="cGreen">'))
                    {
                        $this->excel->getActiveSheet()->getStyle($this->excel->getColumnForXls($yap) . $ph)->getFont()
                            ->getColor()->setARGB(PHPExcel_Style_Color::COLOR_GREEN);
                    }
                    if (strstr($row[$key], '<span class="cRed">'))
                    {
                        $this->excel->getActiveSheet()->getStyle($this->excel->getColumnForXls($yap) . $ph)->getFont()
                            ->getColor()->setARGB(PHPExcel_Style_Color::COLOR_RED);
                    }
                    $row[$key] = strip_tags($row[$key]);
                }
                $this->excel->getActiveSheet()->setCellValueExplicit($this->excel->getColumnForXls($yap) . $ph,
                    $row[$key], PHPExcel_Cell_DataType::TYPE_STRING);
                $yap += 1;
            }
        }

        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="' . $fileBaseName . '.xls"');
        header('Cache-Control: max-age=0');

        $objWriter = PHPExcel_IOFactory::createWriter($this->excel, 'Excel5');
        $objWriter->save('php://output');
    }

    /**
     * 参    数：
     * 作    者：刁寿钧
     * 功    能：导出CSV文件
     * 修改日期：2015-07-23
     */
    private function writeCSV($head, $body, $fileBaseName)
    {
        $this->load->library('Excel');
        
        $ph = 1;
        foreach ($head as $key => $value)
        {
            $this->excel->getActiveSheet()->setCellValueExplicit($this->excel->getColumnForXls($ph ++) . '1',
                mb_convert_encoding(strip_tags($value), 'GB2312'), PHPExcel_Cell_DataType::TYPE_STRING);
        }

        $ph = 1;
        foreach ($body as $row)
        {
            $ph ++;
            //yet another placeholder
            $yap = 1;
            foreach ($head as $key => $value)
            {
                $this->excel->getActiveSheet()->setCellValueExplicit($this->excel->getColumnForXls($yap) . $ph,
                    mb_convert_encoding(strip_tags($row[$key]), 'GB2312'), PHPExcel_Cell_DataType::TYPE_STRING);
                $yap += 1;
            }
        }

        header('Content-Type: text/csv');
        header('Content-Disposition: attachment;filename="' . $fileBaseName . '.csv"');
        header('Cache-Control: max-age=0');

        $objWriter = PHPExcel_IOFactory::createWriter($this->excel, 'csv')
            ->setDelimiter(',')
            ->setEnclosure('"')
            ->setSheetIndex(0);
        $objWriter->save('php://output');
    }

    /**
     * 参    数：
     * 作    者：刁寿钧
     * 功    能：注册数据页面
     * 修改日期：2015-07-23
     */
    public function register()
    {
    	$this->check_capacity('9_3');
        $this->load->model('model_register_stat', 'register');
        $this->load->model('model_click', 'click');
        list($platform, $channelId, $version, $period, $isCsv) = $this->defaultParams();

        $registerRecords = $this->register->fetchRecordsByPlatform($platform, $channelId, $version, $period);
        $dateToUV = $this->click->getDateToUV($platform, $channelId, $version, $period);
        $page = 'register';
        $fieldsMap = $this->decideFields($page);
        $fullRegisterRecords = $this->fillRecords($registerRecords, $fieldsMap, $period);
        $records = $this->formatRegisterRecords($fullRegisterRecords, $dateToUV);
        $summary = $this->summaryRecords($records, $page);
        $numFields = $this->countableFields();

        if ($isCsv)
        {
            $head = $fieldsMap;
            $body = array();
            array_push($body, $summary);
            $fields = array_keys($head);
            foreach ($records as $record)
            {
                $tempRow = array();
                foreach ($fields as $field)
                {
                    if (in_array($field, $numFields))
                    {
                        $tempRow[$field] = number_format($record[$field]);
                    }
                    else
                    {
                        $tempRow[$field] = $record[$field];
                    }
                }
                array_push($body, $tempRow);
            }
            $fileBaseName = '注册数据列表';

            $this->writeCSV($head, $body, $fileBaseName);
        }
        else
        {
            //这里日均的概念不是平摊到时间跨度内的每一天上，而是有记录的每一天
            $count = count($records);
            $daily = $this->dailyRecords($summary, $page, $count);

            $this->load->model('model_channel', 'channel');
            $channels = $this->channel->getChannelList($platform);

            $this->load->model('model_app_version', 'appVersion');
            $appVersions = $this->appVersion->getVersionList();

            $this->load->view('analysis/register', compact('platform', 'channelId', 'appVersions', 'version', 'period',
                'isCsv', 'channels', 'daily', 'summary', 'records', 'fieldsMap'));
        }
    }

    /**
     * 参    数：
     * 作    者：刁寿钧
     * 功    能：构造注册数据的数组，及汇总数据
     * 修改日期：2015-07-23
     */
    private function formatRegisterRecords($records, $dateToUV)
    {
        foreach ($records as & $record)
        {
            $record['dateStr'] = $this->convertDateStr($record['date']);
            $record['uv'] = $dateToUV[$record['date']] OR 0;

            //tricky 0.00
            if ($record['uv'] * 100)
            {
                $record['registerRatio'] = number_format($record['registerUser'] / $record['uv'] * 100, 2) . '%';
                $record['validRatio'] = number_format($record['validUser'] / $record['uv'] * 100, 2) . '%';
            }
            else
            {
                $record['registerRatio'] = '--';
                $record['validRatio'] = '--';
            }
        }

        return $records;
    }

    public function hqua()
    {
    	$this->check_capacity('9_7');
        $platform = $this->input->post("platform", true) ? $this->input->post("platform", true) : '0';
        $channel = $this->input->post("channel", true) !== false ? $this->input->post("channel", true):'all';
        $version = $this->input->post("version", true) ? $this->input->post("version", true) : 'all';
        $loginTimesBegin = $this->input->post("loginTimesBegin", true) ? $this->input->post("loginTimesBegin", true) : '0';
        $loginTimesEnd = $this->input->post("loginTimesEnd", true) ? $this->input->post("loginTimesEnd", true) :  'max';
        $totalMoneyBegin = $this->input->post("totalMoneyBegin", true) ? $this->input->post("totalMoneyBegin", true) : '100';
        $totalMoneyEnd = $this->input->post("totalMoneyEnd", true) ? $this->input->post("totalMoneyEnd", true) :  'max';
        $uid = $this->input->get("uid", TRUE);
        $searchData = array(
            'platform' => $platform,
            'channel' => $channel,
            'version' => $version,
            'loginTimesBegin' => $loginTimesBegin,
            'loginTimesEnd' => $loginTimesEnd,
            'totalMoneyBegin' => $totalMoneyBegin,
            'totalMoneyEnd' => $totalMoneyEnd,
            'uid' => $uid
        );
        $this->load->model('model_data_analysis', 'hqdb');
        $data = $this->hqdb->get_all($searchData);
        foreach ($data as $key => $value)
        {
            $result[$key]['uname'] = $value['uname'];
            $result[$key]['uid'] = $value['uid'];
            $result[$key]['login_times_30day'] = $value['login_times_30day'];
            $result[$key]['total_betmoney'] = number_format($value['total_betmoney'], 2);
            $result[$key]['total_winmoney'] = number_format($value['total_winmoney'], 2);
            if($value['total_betmoney'] != 0 && $value['order_num'] != 0)
            {
                $result[$key]['rate_of_repay'] = round($value['total_winmoney']/$value['total_betmoney']*100,2)."%";
                $result[$key]['money_per_order'] = number_format($value['total_betmoney']/$value['order_num'], 2);
                $gao = ($value['betmoney21406'] + $value['betmoney21407'] + $value['betmoney53'] + $value['betmoney56'] + $value['betmoney57'] + $value['betmoney21408'] + $value['betmoney54'] + $value['betmoney55'])/$value['total_betmoney'];
                $man = ($value['betmoney23529']+$value['betmoney23528']+$value['betmoney10022']+$value['betmoney51']+$value['betmoney33']+$value['betmoney35']+$value['betmoney52'])/$value['total_betmoney'];
                $jing = ($value['betmoney11']+$value['betmoney19']+$value['betmoney41']+$value['betmoney42']+$value['betmoney43']+$value['betmoney44']+$value['betmoney45'])/$value['total_betmoney'];
                $result[$key]['gao'] = round($gao*100,2)."%";
                $result[$key]['man'] = round($man*100,2)."%";
                $result[$key]['jing'] = round($jing*100,2)."%";
            }
            $result[$key]['uname'] = $value['uname'];
            $result[$key]['account'] = number_format($value['account'], 2);
            $result[$key]['uname'] = $value['uname'];
            $result[$key]['last_login_time'] = $value['login_time'];
        }
        $platform = empty($platform) ? '1' : $platform;
        $channels = $this->hqdb->getChannels($platform);
        $platform = $this->platform;
        $version = $this->hqdb->getAppVersion();
       
        $this->load->view("analysis/hqua",compact('result','platform','channels','version','searchData'));
    }
    
    public function getChannels()
    {
    	$pform = $this->input->post("pform", true);
    	$this->load->model('model_data_analysis', 'hqdb');
    	$channels = $this->hqdb->getChannels($pform);
    	$options = '';
    	if(!empty($channels))
    	{
    		$options = '<option value="all">全部</option>';
    		foreach ($channels as $channel)
    		{
    			$options .= "<option value='{$channel['id']}'>{$channel['name']}</option>";
    		}
    	}
    	echo $options;
    }

    /**
     * 参    数：
     * 作    者：刘力
     * 功    能：数据分析 - 概览
     * 修改日期：2015-07-23
     */
    public function index()
    {
    	$this->check_capacity('9_1');
        // 初始化条件
        $dataInfo = array(
            'days' => 7,                // 过去七天
            'tab1' => 'conversion',     // 转化率
            'tab2' => 'allSale',        // 全国销量
            'stype' => 'day',
            'platform' => $this->platType
        );

        // 转化率查询
        $dataInfo['conversion'] = $this->getConversion($dataInfo['platform'], $dataInfo['days']);

        // 全国销量
        $dataInfo['allSale'] = $this->getAllSale($dataInfo['platform'], $dataInfo['days'], $dataInfo['stype']);

        $this->load->view("analysis/index", $dataInfo);
    }

    /**
     * 参    数：
     * 作    者：刘力
     * 功    能：数据分析 - Ajax 查询指定条件
     * 修改日期：2015-07-23
     */
    public function ajaxChart()
    {
        $days = $this->input->post("days");
        $data['ctype'] = $this->input->post("ctype");
        $platform = $this->platType;
        $stype = $this->input->post("stype")?$this->input->post("stype"):'day';
        
        switch ($data['ctype']) 
        {
            // 转化率
            case 'conversion':
                $data['info'] = $this->getConversion($platform, $days);
                break;
            // 有效用户
            case 'validUser':
                $data['info'] = $this->getValidUser($platform, $days, $stype);
                break;
            // 全国销量
            case 'allSale':
                $data['info'] = $this->getAllSale($platform, $days, $stype);
                break;
            // 各平台销量占比
            case 'platformSale':
                $data['info'] = $this->getPlatformSale($platform, $days);
                break;
            // 各彩种销量占比
            case 'lotterySale':
                $data['info'] = $this->getLotterySale($platform, $days);
                break;
            // 各彩种返奖率
            case 'lotteryAward':
                $data['info'] = $this->getLotteryAward($platform, $days);
                break;
            default:
                # code...
                break;
        }
        echo $this->load->view('analysis/ajaxChart', $data, true);
    }

    /**
     * 参    数：
     * 作    者：刘力
     * 功    能：数据分析 - 转化率查询
     * 修改日期：2015-07-23
     */
    private function getConversion($platform, $days)
    {
        $data = array();
        foreach ($platform as $platName => $platId) 
        {
            // 查询
            $click_num[$platName] = $this->analysis->getClickNum($platId, $days);
            $register_num[$platName] = $this->analysis->getRegisterNum($platId, $days);
            $recharge_num[$platName] = $this->analysis->getRechargeNum($platId, $days);
            $orders_num[$platName] = $this->analysis->getOrdersNum($platId, $days);
            // 计算
            $data[$platName]['click_conv'] = $this->conv_bfb($this->cal_blv($click_num[$platName]['click_uv'], $click_num[$platName]['browse_uv']));
            $data[$platName]['register_conv'] = $this->conv_bfb($this->cal_blv($register_num[$platName]['register_num'], $click_num[$platName]['browse_uv']));
            $data[$platName]['valid_conv'] = $this->conv_bfb($this->cal_blv($register_num[$platName]['valid_user'], $click_num[$platName]['browse_uv']));
            $data[$platName]['recharge_conv'] = $this->conv_bfb($this->cal_blv($recharge_num[$platName]['users'], $click_num[$platName]['browse_uv']));
            $data[$platName]['orders_conv'] = $this->conv_bfb($this->cal_blv($orders_num[$platName]['betting_users'], $click_num[$platName]['browse_uv']));
            $data[$platName] = json_encode(array_values($data[$platName]));
        }
        
        return $data;
    }

    /**
     * 参    数：
     * 作    者：刘力
     * 功    能：数据分析 - 总销量
     * 修改日期：2015-07-23
     */
    private function getAllSale($platform, $days, $stype)
    {
        $dateInfo = $this->getDates($days);
        $data = array();
        $info = array();

        if($stype == 'day')
        {
            foreach ($platform as $platName => $platId) 
            {
                $saleInfo = $this->analysis->getAllSale($platId, $days);
                foreach ($saleInfo as $in => $items) 
                {
                    $data[$platName][$items['date']] = ParseUnit($items['total'], 1);
                }

                foreach ($dateInfo as $key => $date) 
                {
                    $info[$platName][$date] = $data[$platName][$date]?$data[$platName][$date]:0;
                    // 总和
                    $info['all'][$date] = $info['all'][$date] + $info[$platName][$date];
                }
                $info[$platName] = json_encode(array_values($info[$platName]));
            }
            $info['all'] = json_encode(array_values($info['all']));

            return array('date' => json_encode(array_values($dateInfo)), 'total' => $info);
        }
        else
        {
            // 获取起始周 星期一日期
            $dateInfo = $this->getStartDate($dateInfo);

            foreach ($platform as $platName => $platId) 
            {
                $saleInfo = $this->analysis->getAllSale($platId, $days);

                foreach ($saleInfo as $in => $items) 
                {
                    $data[$platName][$items['date']] = ParseUnit($items['total'], 1);
                }

                foreach ($dateInfo as $key => $date) 
                {
                    $info[$platName][$date] = $data[$platName][$date]?$data[$platName][$date]:0;
                    // 总和
                    $info['all'][$date] = $info['all'][$date] + $info[$platName][$date];
                }
            }

            if($stype == 'month')
            {
                $split = 30;
            }
            else
            {
                $split = 7;
            }

            $count = $this->countDate($info, $split);

            return array('date' => json_encode(array_values($count['date'])), 'total' => $count['info']);
        }
    }

    /**
     * 参    数：
     * 作    者：刘力
     * 功    能：数据分析 - 有效用户
     * 修改日期：2015-07-23
     */
    private function getValidUser($platform, $days, $stype)
    {
        $dateInfo = $this->getDates($days);
        $data = array();
        $info = array();

        // 按日、周、月筛选
        if($stype == 'day')
        {
            foreach ($platform as $platName => $platId) 
            {
                $userInfo = $this->analysis->getValidUser($platId, $days);
                foreach ($userInfo as $in => $items) 
                {
                    $data[$platName][$items['cdate']] = $items['valid_user'];
                }

                foreach ($dateInfo as $key => $date) 
                {
                    $info[$platName][$date] = $data[$platName][$date]?$data[$platName][$date]:0;
                    // 总和
                    $info['all'][$date] = $info['all'][$date] + $info[$platName][$date];
                }
                $info[$platName] = json_encode(array_values($info[$platName]));
            }
            $info['all'] = json_encode(array_values($info['all']));
            return array('date' => json_encode(array_values($dateInfo)), 'data' => $info);
        }
        else
        {
            // 获取起始周 星期一日期
            $dateInfo = $this->getStartDate($dateInfo);
            foreach ($platform as $platName => $platId) 
            {
                $userInfo = $this->analysis->getValidUser($platId, $days);

                foreach ($userInfo as $in => $items) 
                {
                    $data[$platName][$items['cdate']] = $items['valid_user'];
                }
                foreach ($dateInfo as $key => $date) 
                {
                    $info[$platName][$date] = $data[$platName][$date]?$data[$platName][$date]:0;
                    // 总和
                    $info['all'][$date] = $info['all'][$date] + $info[$platName][$date];
                }
            }

            if($stype == 'month')
            {
                $split = 30;
            }
            else
            {
                $split = 7;
            }
            $count = $this->countDate($info, $split);
            return array('date' => json_encode(array_values($count['date'])), 'data' => $count['info']);
        }
    }

    private function getStartDate($date)
    {
        $tag = false;
        $dateInfo = array();
        foreach ($date as $in => $items) 
        {
            if(date('w', strtotime($items)) == 1)
            {
                $tag = true;
            }

            if($tag)
            {
                $dateInfo[] = $items;
            }  
        }
        return $dateInfo;
    }

    /**
     * 参    数：
     * 作    者：刘力
     * 功    能：数据分析 - 按日周月统计
     * 修改日期：2015-07-23
     */
    public function countDate($data, $split)
    {
        $info = array();
        foreach ($data as $plat => $platData) 
        {
            $count = 0;
            $countDate = array();
            foreach ($platData as $date => $items) 
            {
                if($count%$split == 0)
                {
                    $info[$plat][$date] = $items;
                    $nowDate = $date;
                    array_push($countDate, $nowDate);
                }
                else
                {
                    $info[$plat][$nowDate] = $info[$plat][$nowDate] + $items;
                }
                $count ++;
            }
            $info[$plat] = json_encode(array_values($info[$plat]));
        }
        return array('date' => $countDate, 'info' => $info);
    }

    /**
     * 参    数：
     * 作    者：刘力
     * 功    能：数据分析 - 各平台销量占比
     * 修改日期：2015-07-23
     */
    private function getPlatformSale($platform, $days)
    {
        $title = array(
            0 => '网页',
            1 => 'Android客户端'
        );

        // 查询
        $data = array();
        foreach ($platform as $platName => $platId) 
        {
            $sale = $this->analysis->getTotalSale($platId, $days);
            $info[] = $sale['total']?ParseUnit($sale['total'], 1):0;
        }
        
        foreach ($title as $key => $items) 
        {
            $data[$key]['value'] = $info[$key];
            $data[$key]['name'] = $items;
        }

        return array('title' => json_encode($title), 'data' => json_encode($data));
    }

    /**
     * 参    数：
     * 作    者：刘力
     * 功    能：数据分析 - 各彩种销量占比
     * 修改日期：2015-07-23
     */
    public function getLotterySale($platform, $days)
    {
        $data = array();
        $info = array();
        $lotteryInfo = $this->lottery;

        foreach ($platform as $platName => $platId) 
        {
            // 当前平台总销量
            $platSale = $this->analysis->getAllSaleByPlat($platId, $days);
            $saleAll[$platName] = ParseUnit($platSale['total'], 1);

            // 当前平台各彩种总销量
            $saleInfo = $this->analysis->getLotterySale($platId, $days);

            foreach ($saleInfo as $in => $items) 
            {
                $data[$platName][$items['lid']] = ParseUnit($items['total'], 1);
            }

            foreach ($lotteryInfo as $lid => $lottery) 
            {
                $info[$platName][$lid] = $data[$platName][$lid]?$data[$platName][$lid]:0;
                $info['allPlat'][$lid] = $data['allPlat'][$lid]?$data['allPlat'][$lid]:0;
                // 计算百分比
                $info[$platName][$lid] = $this->conv_bfb($this->cal_blv($info[$platName][$lid], $saleAll[$platName]));
            }
            $info[$platName] = json_encode(array_values($info[$platName]));
        }
        return array('lname' => json_encode(array_values($lotteryInfo)), 'data' => $info);
    }

    /**
     * 参    数：
     * 作    者：刘力
     * 功    能：数据分析 - 各彩种返奖率
     * 修改日期：2015-07-23
     */
    public function getLotteryAward($platform, $days)
    {
        $data = array();
        $info = array();
        $lotteryInfo = $this->lottery;
        // 查询
        $awardAllInfo = $this->analysis->getLotteryAward($plat = 0, $days);

        $awardAll = array();
        if(!empty($awardAllInfo))
        {
            foreach ($awardAllInfo as $in => $items) 
            {
                $awardAll[$items['lid']]['awards'] = ParseUnit($items['award_total'], 1);
                $awardAll[$items['lid']]['total'] = ParseUnit($items['total'], 1);
            }
        }

        foreach ($lotteryInfo as $lid => $items) 
        {
            $data[$lid]['awards'] = $awardAll[$lid]['awards']?$awardAll[$lid]['awards']:0;
            $data[$lid]['total'] = $awardAll[$lid]['total']?$awardAll[$lid]['total']:0;
            $info[$lid] = $this->conv_bfb($this->cal_blv($data[$lid]['awards'], $data[$lid]['total']));
        }

        return array('lname' => json_encode(array_values($lotteryInfo)), 'data' => json_encode(array_values($info)));
    }

    /**
     * 参    数：
     * 作    者：刘力
     * 功    能：数据分析 - 获取时间段
     * 修改日期：2015-07-23
     */
    private function getDates($days)
    {
        $startDate = date('Y-m-d', strtotime('-'.$days .'day'));
        $endDate = date('Y-m-d', strtotime('-1 day'));
        $data = $this->getAllDates($startDate, $endDate);
        return $data;
    }

    /**
     * 参    数：
     * 作    者：刘力
     * 功    能：数据分析 - 获取时间段
     * 修改日期：2015-07-23
     */
    private function getAllDates($s, $e)
    {
        if (empty($s) || empty($e) || (strtotime($s) > strtotime($e)))
        {
            return array();
        }
        $res = array();
        $datetime1 = new DateTime($s);
        $datetime2 = new DateTime($e);
        $interval  = $datetime1->diff($datetime2);
        $days = $interval->format('%a');
        for ($j = 0; $j <= $days; $j++)
        {
            $time = strtotime("+$j days", strtotime($s));
            $val = date("Y-m-d", $time);
            array_push($res, $val);
        }
        
        return $res;
    }
    
    // 比率
    private function cal_blv($fz, $fm)
    {
        if($fm == 0)
        {
            return 0;
        }
        else
        {
            return ($fz / $fm) ; 
        }
    }
    
    // 百分比处理
    private function conv_bfb($num)
    {
        return number_format($num * 100, 2);
    }

}