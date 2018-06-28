<?php

class Issue extends MY_Controller
{
    //彩种规则
    private $lrule = array(
        'ssq'  => array(
            'name'     => '双色球',
            'issueLen' => '7',
            'rule'     => array(
                '0' => '2',
                '2' => '2',
                '4' => '3',
            ),
            'lid'      => 51
        ),
        'dlt'  => array(
            'name'     => '大乐透',
            'issueLen' => '5',
            'rule'     => array(
                '1' => '2',
                '3' => '3',
                '6' => '2',
            ),
            'lid'      => 23529
        ),
        'qxc'  => array(
            'name'     => '七星彩',
            'issueLen' => '5',
            'rule'     => array(
                '2' => '3',
                '5' => '2',
                '0' => '2',
            ),
            'lid'      => 10022
        ),
        'qlc'  => array(
            'name'     => '七乐彩',
            'issueLen' => '7',
            'rule'     => array(
                '1' => '2',
                '3' => '2',
                '5' => '3',
            ),
            'lid'      => 23528
        ),
        //每天一期
        'fc3d' => array(
            'name'     => '福彩3D',
            'issueLen' => '7',
            'rule'     => array(
                '0' => '1',
            ),
            'lid'      => 52
        ),
        'pl3'  => array(
            'name'     => '排列三',
            'issueLen' => '7',
            'rule'     => array(
                '0' => '1',
            ),
            'lid'      => 33
        ),
        'pl5'  => array(
            'name'     => '排列五',
            'issueLen' => '7',
            'rule'     => array(
                '0' => '1',
            ),
            'lid'      => 35
        ),
        'syxw' => array(
            'name'     => '十一选五',
            'issueLen' => '8',
            'rule'     => array(
                '0' => '1',
            ),
            'lid'      => 21406
        ),
    	'jxsyxw' => array(
    		'name'     => '江西十一选五',
    		'issueLen' => '8',
    		'rule'     => array(
    			'0' => '1',
    		),
            'lid'      => 21407
    	),
    	'hbsyxw' => array(
    		'name'     => '湖北十一选五',
    		'issueLen' => '8',
    		'rule'     => array(
    			'0' => '1',
    		),
            'lid'      => 21408
    	),
        'gdsyxw' => array(
            'name'     => '广东十一选五',
            'issueLen' => '8',
            'rule'     => array(
                '0' => '1',
            ),
            'lid'      => 21421
        ),
    	'ks' => array(
    		'name'     => '上海快三',
    		'issueLen' => '11',
    		'rule'     => array(
    			'0' => '1',
    		),
            'lid'      => 53
    	),
    	'jlks' => array(
    		'name'     => '吉林快三',
    		'issueLen' => '11',
    		'rule'     => array(
    			'0' => '1',
    		),
            'lid'      => 56
    	),   
        'jxks' => array(
            'name'     => '江西快三',
            'issueLen' => '11',
            'rule'     => array(
                '0' => '1',
            ),
            'lid'      => 57
        ),
        'klpk' => array(
            'name'     => '快乐扑克',
            'issueLen' => '8',
            'rule'     => array(
                '0' => '1',
            ),
            'lid'      =>54
        ),
        'cqssc' => array(
            'name'     => '老时时彩',
            'issueLen' => '9',
            'rule'     => array(
                '0' => '1',
            ),
            'lid'      => 55
        ),
        'bjdc' => array(
            'name' => '北京单场',
        ),
        'sfgg' => array(
            'name' => '胜负过关',
        ),
        'sfc'  => array(
            'name' => '胜负/任九',
        ),
        'bqc'  => array(
            'name' => '半全场',
        ),
        'jqc'  => array(
            'name' => '进球彩',
        ),
    );

    public function __construct()
    {
        parent::__construct();
		$this->config->load('msg_text');
		$this->msg_text_cfg = $this->config->item('msg_text_cfg');
        $this->load->model('Model_issue');
    }

    /*
     * 期次预排
     * @author:liuli
     * @date:2015-03-27
     */
    public function pre_issue($type = '')
    {
    	$this->check_capacity('7_1_1');
        $lrule = $this->lrule;
        $type = $type ? $type : 'ssq';
        $info = $this->Model_issue->getConfigInfo($type);
        $info['name'] = $lrule[$info['lid']]['name'];
        $info['lrule'] = $lrule;
        $info['type'] = $type;
        $info['isGaopin'] = (in_array($type, array('syxw', 'jxsyxw', 'hbsyxw', 'ks', 'jlks', 'jxks', 'klpk', 'cqssc', 'gdsyxw'))) ? 1 : 0;
        $this->load->view("issue/pre_issue", $info);
    }

    /*
     * 期次预排 -- 停售配置 
     * @author:liuli
     * @date:2015-03-27
     */
    public function delay_issue($type = '')
    {
        $this->check_capacity('7_1_2');
        $lrule = $this->lrule;
        $type = $type ? $type : 'ssq';
        $info = $this->Model_issue->getConfigInfo($type);
        $info['name'] = $lrule[$info['lid']]['name'];
        $info['lrule'] = $lrule;
        $info['type'] = $type;
        $this->load->view("issue/delay_issue", $info);
    }

    /*
     * 期次预排 -- 修改预设期次
     * @author:liuli
     * @date:2015-03-27
     */
    public function modifyPreIssue()
    {
        $this->check_capacity('7_1_3', true);
        $res = array(
            'status' => '01',
            'message'    => '系统异常',
        );

        $data = $this->input->post(NULL, TRUE);
        if (empty($data['type']) || empty($data['start_date']) || empty($data['early_time']) || empty($data['issue_num']))
        {
            $res = array(
                'status' => '01',
                'message'    => '参数缺失',
            );
            die(json_encode($res));
        }

        if ( ! strtotime($data['start_date']))
        {
            $res = array(
                'status' => '02',
                'message'    => '启动时间格式错误',
            );
            die(json_encode($res));
        }

        if (in_array($data['type'], array('syxw', 'jxsyxw', 'ks', 'hbsyxw', 'gdsyxw')))
        {
            $data['early_time'] = 10;
        }
        else
        {
            if ($data['early_time'] <= 0)
            {
                $res = array(
                    'status' => '03',
                    'message'    => '提前时间必须大于0',
                );
                die(json_encode($res));
            }
        }

        if ( ! is_numeric($data['issue_num']))
        {
            $res = array(
                'status' => '04',
                'message'    => '预排期数必须是1的整数倍',
            );
            die(json_encode($res));
        }

        if ( ! preg_match("/^[1-9]\d*$/", $data['issue_num']))
        {
            $res = array(
                'status' => '04',
                'message'    => '预排期数必须是1的整数倍',
            );
            die(json_encode($res));
        }
        $res_judge = $this->Model_issue->getIssueData($data);
        //更新预设配置表
        $res_modify = $this->Model_issue->modifyPreIssue($data);
        if ($res_modify)
        {
            $res = array(
                'status' => '00',
                'message'    => '启动成功',
            );
        }


        foreach($res_judge as $key => $value)
        {
            if($value["early_time"] != $data["early_time"])
            {
                $pre["early_time"] = $data["early_time"];
            }

            if($value["start_date"] != $data["start_date"])
            {
                $pre["start_date"] = $data["start_date"];
            }

            if($value["issue_num"] != $data["issue_num"])
            {
                $pre["issue_num"] = $data["issue_num"];
            }
        }
        $lname = $this->lrule[$data['type']]['name'];
        if(isset($pre["early_time"]))
        {
            $this->syslog(16, $lname."彩种预排时间修改操作 提前截止时间修改为（".$data['early_time'].")");
        }
        if(isset($pre["start_date"]))
        {
            $this->syslog(16, $lname."彩种预排时间修改操作 预排时间修改为（".$data['start_date']."）");
        }
        if(isset($pre["issue_num"]))
        {
            in_array($lname, array('十一选五', '江西十一选五', '上海快三', '湖北十一选五', '广东十一选五')) ? $this->syslog(16, $lname."彩种预排时间修改操作 预排天数修改为(".$data['issue_num'].")") :
            $this->syslog(16, $lname."彩种预排时间修改操作 预排期数修改为(".$data['issue_num'].")");
        }

        die(json_encode($res));
    }

    /*
     * 期次预排 --推迟预设期次
     * @author:liuli
     * @date:2015-03-27
     */
    public function delayPreIssue()
    {
        $this->check_capacity('7_1_4', true);
        $data = $this->input->post(NULL, TRUE);
        if (empty($data['type']) || empty($data['delay_start_time']) || empty($data['delay_end_time']))
        {
            $res = array(
                'status' => '01',
                'message'    => '参数缺失',
            );
            die(json_encode($res));
        }
        //开始截止时间校验
        if (strtotime($data['delay_start_time']) > strtotime($data['delay_end_time']))
        {
            $res = array(
                'status' => '02',
                'message'    => '截止时间必须大于开始时间',
            );
            die(json_encode($res));
        }

        if (strtotime($data['delay_start_time']) <= 0 || strtotime($data['delay_end_time']) <= 0)
        {
            $res = array(
                'status' => '02',
                'message'    => '推迟时间不能为空',
            );
            die(json_encode($res));
        }
        $res_judge = $this->Model_issue->getIssueData($data);
        //执行推迟程序
        $this->Model_issue->updateDelayConfig($data);
        $res = array(
            'status' => '00',
            'message'    => '推迟成功',
        );
        foreach($res_judge as $key => $value)
        {
            if($value['delay_start_time'] != $data['delay_start_time'])
            {
                $pre['delay_start_time'] = $data['delay_start_time'];
            }
            if($value['delay_end_time'] != $data['delay_end_time'])
            {
                $pre['delay_end_time'] = $data['delay_end_time'];
            }
        }
        $lname = $this->lrule[$data['type']]['name'];
        if(isset($pre['delay_start_time']))
        {
            $this->syslog(16, $lname."彩种停售时间修改操作 停售开始时间修改为（".$data['delay_start_time']."）");
        }
        if(isset($pre['delay_end_time']))
        {
            $this->syslog(16, $lname."彩种停售时间修改操作 停售截止时间修改为（".$data['delay_end_time'].")");
        }
        die(json_encode($res));
    }

    private function delayIssue($dataInfo)
    {
        $issue = $dataInfo['start_issue'];
        $type = $dataInfo['type'];
        $days = $dataInfo['delay_days'];
        $issueInfo = $this->Model_issue->getSelectIssue($issue, $type);
        if ( ! empty($issueInfo))
        {
            //入库
            $fields = array('issue', 'sale_time', 'end_time', 'award_time', 'status', 'created');
            $bdata['s_data'] = array();
            $bdata['d_data'] = array();
            $count = 0;

            $data = array();
            foreach ($issueInfo as $k => $issueList)
            {
                if ($k == 0)
                {
                    $data[$k]['sale_time'] = $issueList['sale_time'];
                }
                else
                {
                    $data[$k]['sale_time'] = $this->getDelayTime($issueList['sale_time'], $days);
                }
                $data[$k]['end_time'] = $this->getDelayTime($issueList['end_time'], $days);
                $data[$k]['award_time'] = $this->getDelayTime($issueList['award_time'], $days);

                array_push($bdata['s_data'], "(?, ?, ?, ?, ?, now())");
                array_push($bdata['d_data'], $issueList['issue']);
                array_push($bdata['d_data'], $data[$k]['sale_time']);
                array_push($bdata['d_data'], $data[$k]['end_time']);
                array_push($bdata['d_data'], $data[$k]['award_time']);
                array_push($bdata['d_data'], '0');
                if (++ $count >= 500)
                {
                    $this->Model_issue->insertIssue($fields, $bdata, $type);
                    $bdata['s_data'] = array();
                    $bdata['d_data'] = array();
                    $count = 0;
                }
            }
            if ( ! empty($bdata['s_data']))
            {
                $this->Model_issue->insertIssue($fields, $bdata, $type);
                $bdata['s_data'] = array();
                $bdata['d_data'] = array();
            }

            //更新配置表
            $this->Model_issue->updateDelayConfig($dataInfo);
            $res = array(
                'status' => '00',
                'msg'    => '推迟成功',
            );
        }
        else
        {
            $res = array(
                'status' => '03',
                'msg'    => '期号错误',
            );
        }

        return $res;
    }

    //计算推迟时间
    private function getDelayTime($time, $days)
    {
        $t = strtotime($time);
        $d = $days * 86400;
        $t = $t + $d;

        return date('Y-m-d H:i:s', $t);
    }

    /*
     * 期次管理
     * @author:liuli
     * @date:2015-03-31
     */
    public function management()
    {
    	$this->check_capacity('7_2_1');
        $all_capacity = implode(',', $this->get_all_capacity());
        $page = intval($this->input->get("p"));
        $lrule = $this->lrule;
        $get_type = $this->input->get("type", TRUE);
        if ($get_type)
        {
            $type = $get_type;
        }
        else
        {
            if (empty($type))
            {
                $type = 'ssq';
            }
        }
        $searchData = array(
            "type"       => $type,
            "aduitflag"  => $this->input->get("aduitflag", TRUE),
            "start_time" => $this->input->get("start_time", TRUE),
            "end_time"   => $this->input->get("end_time", TRUE),
        );

        $per_page = 10;     //self::NUM_PER_PAGE

        //起始页数
        if (in_array($type, array('bjdc', 'sfgg', 'sfc', 'bqc', 'jqc')))
        {
            $pageMothed = "pageMothed_" . $type;
        }
        else
        {
            $pageMothed = "pageMothed";
        }
        $this->filterTime($searchData['start_time'], $searchData['end_time']);
        
        $defaultCount = $this->Model_issue->$pageMothed($searchData);

        $defaultPage = ceil(intval($defaultCount[0]) / $per_page);

        $defaultPage = $defaultPage ? $defaultPage : 1;
        // if (in_array($type, array('syxw', 'jxsyxw', 'ks', 'hbsyxw', 'klpk')))
        // {
        //     $defaultPage = 1;
        // }
        $page = $page < 1 ? $defaultPage : $page;

        if (in_array($type, array('bjdc', 'sfgg', 'sfc', 'bqc', 'jqc')))
        {
            $mothed = "getIssueList_" . $type;
        }
        else
        {
            $mothed = "getIssueList_number";
        }
        $result = $this->Model_issue->$mothed($searchData, $page, $per_page);
        //期次状态
        $issueInfo = array();
        if ( ! empty($result[0]))
        {
            foreach ($result[0] as $k => $issue)
            {
                $issueInfo[$k]['id'] = $issue['id'];
                $issueInfo[$k]['issue'] = $issue['issue'];
                $issueInfo[$k]['sale_time'] = $issue['sale_time'];
                $issueInfo[$k]['end_time'] = $issue['end_time'];
                $issueInfo[$k]['award_time'] = $issue['award_time'];
                $issueInfo[$k]['awardNum'] = $issue['awardNum'];
                $issueInfo[$k]['sale'] = $issue['sale'];
                $issueInfo[$k]['pool'] = $issue['pool'];
                $issueInfo[$k]['bonusDetail'] = $issue['bonusDetail'];
                $issueInfo[$k]['compare_status'] = $issue['status'];
                $issueInfo[$k]['rstatus'] = $issue['rstatus'];
                $issueInfo[$k]['aduitflag'] = $issue['aduitflag'];
                //获取对比状态
                if (in_array($type, array('sfc', 'bqc', 'jqc')))
                {
                    $issueInfo[$k]['status'] = $this->getIssueStatusByJjc($issue['end_time'], $issue['status'],
                        $issue['rstatus']);
                }
                elseif (in_array($type, array('bjdc', 'sfgg')))
                {
                    $issueInfo[$k]['status'] = $this->getIssueStatusByBjdc($issue['end_time'], $issue['status'],
                        $issue['rstatus']);
                }
                else
                {
                    $issueInfo[$k]['status'] = $this->getIssueStatus($issue['end_time'], $issue['status'],
                        $issue['rstatus']);
                }
            }
        }

        $pageConfig = array(
            "page"     => $page,
            "npp"      => $per_page,
            "allCount" => $result[1]
        );
        //分页设置
        $pages = get_pagination($pageConfig);

        $infos = array(
            "search" => $searchData,
            "result" => $issueInfo,
            "lrule"  => $lrule,
            "pages"  => $pages,
            "allCapacity" => $all_capacity,
        );
        if (in_array($type, array('syxw', 'jxsyxw', 'hbsyxw', 'gdsyxw')))
        {
            $this->load->view("issue/management_syxw", $infos);
        }
        // elseif(in_array($type, array('ks')))
        // {
        // 	$this->load->view("issue/management_ks", $infos);
        // }
        elseif(in_array($type, array('ks','jlks', 'jxks')))
        {
        	$this->load->view("issue/management_jlks", $infos);
        }        
        elseif(in_array($type, array('klpk')))
        {
            $this->load->view("issue/management_klpk", $infos);
        }
        elseif(in_array($type, array('cqssc')))
        {
            $this->load->view("issue/management_cqssc", $infos);
        }
        elseif (in_array($type, array('bjdc', 'sfgg')))
        {
            $this->load->view("issue/management_bjdc", $infos);
        }
        elseif (in_array($type, array('sfc', 'bqc', 'jqc')))
        {
            $this->load->view("issue/management_tczq", $infos);
        }
        elseif (in_array($type, array('ssq', 'dlt')))
        {
            $this->load->view("issue/management_ssq_dlt", $infos);
            //$this->load->view("issue/management", $infos);
        }elseif (in_array($type, array('pl3', 'fc3d')))
        {
            $this->load->view("issue/management_pl3_fc3d", $infos);
        }else{
            $this->load->view("issue/management_".$type, $infos);
        }
    }

    //获取期次状态
    private function getIssueStatus($award_time, $status, $rstatus)
    {
        $state = '开启';
        if (strtotime($award_time) <= strtotime(date('Y-m-d H:i:s')))
        {
            $state = '截止';
            if ($status == '50' && $rstatus == '50')
            {
                $state = '结期';
            }
        }

        return $state;
    }

    //获取期次状态 -- 竞技彩
    private function getIssueStatusByJjc($award_time, $status, $rstatus)
    {
        $state = '开启';
        if (strtotime($award_time) <= strtotime(date('Y-m-d H:i:s')))
        {
            $state = '截止';
            if ($status == '50' && $rstatus == '50')
            {
                $state = '结期';
            }
        }

        return $state;
    }

    //获取期次状态 -- 竞技彩
    private function getIssueStatusByBjdc($end_time, $status, $rstatus)
    {
        $state = '开启';
        if (strtotime($end_time) <= strtotime(date('Y-m-d H:i:s')))
        {
            $state = '截止';
            if ($status == 0 && $rstatus == 0)
            {
                $state = '结期';
            }
        }

        return $state;
    }

    public function modifyIssueDetail()
    {
        $this->check_capacity('7_2_4');
        $lrule = $this->lrule;
        $data = $this->input->get(NULL, TRUE);
        if (in_array($data['lid'], array('sfc', 'bqc', 'jqc')))
        {
            $mothed = "getDetailList_" . $data['lid'];
            $infos = $this->Model_issue->$mothed($data);
        }
        else
        {
            $infos = $this->Model_issue->getDetailList($data);
        }
        $infos['name'] = $lrule[$data['lid']]['name'];
        $infos['bonusDetail'] = json_decode($infos['bonusDetail'], TRUE);
        $infos['lid'] = $data['lid'];
        $infos['issue'] = $infos['issue']?$infos['issue']:$data['issue'];
        $infos['matchStatus'] = $this->input->get('matchStatus');
        $infos['allCapacity'] = implode(',', $this->get_all_capacity());
        $this->load->view("issue/detail_modify", $infos);
    }

    /*
     * 期次管理 -- 查看详情
     * @author:liuli
     * @date:2015-03-31
     */
    public function detail()
    {
        $this->check_capacity('7_2_2');
        $lrule = $this->lrule;
        $data = $this->input->get(NULL, TRUE);
        if (in_array($data['lid'], array('sfc', 'bqc', 'jqc')))
        {
            $mothed = "getDetailList_" . $data['lid'];
            $infos = $this->Model_issue->$mothed($data);
        }
        else
        {
            $infos = $this->Model_issue->getDetailList($data);
        }
        $infos['name'] = $lrule[$data['lid']]['name'];
        $infos['bonusDetail'] = json_decode($infos['bonusDetail'], TRUE);
        $infos['lid'] = $data['lid'];
        $this->load->view("issue/detail_display", $infos);
    }

    //修改期次信息
    public function modifyBonusDetail()
    {
        $this->check_capacity('7_2_4');
        $data = $this->input->get(NULL, TRUE);
        if ( ! empty($data['type']))
        {
            $info = array();
            switch ($data['type'])
            {
                case 'ssq':
                    $dfields = array();
                    $dfields['1dj']['zs'] = $data['1dj_zs'];
                    $dfields['1dj']['dzjj'] = $data['1dj_dzjj'];
                    $dfields['2dj']['zs'] = $data['2dj_zs'];
                    $dfields['2dj']['dzjj'] = $data['2dj_dzjj'];
                    $dfields['3dj']['zs'] = $data['3dj_zs'];
                    $dfields['3dj']['dzjj'] = $data['3dj_dzjj'];
                    $dfields['4dj']['zs'] = $data['4dj_zs'];
                    $dfields['4dj']['dzjj'] = $data['4dj_dzjj'];
                    $dfields['5dj']['zs'] = $data['5dj_zs'];
                    $dfields['5dj']['dzjj'] = $data['5dj_dzjj'];
                    $dfields['6dj']['zs'] = $data['6dj_zs'];
                    $dfields['6dj']['dzjj'] = $data['6dj_dzjj'];
                    $info['bonusDetail'] = json_encode($dfields);
                    break;

                case 'dlt':
                    $dfields = array();
                    $dfields['1dj']['jb']['zs'] = $data['1dj_jb_zs'];
                    $dfields['1dj']['jb']['dzjj'] = $data['1dj_jb_dzjj'];
                    $dfields['1dj']['zj']['zs'] = $data['1dj_zj_zs'];
                    $dfields['1dj']['zj']['dzjj'] = $data['1dj_zj_dzjj'];
                    $dfields['2dj']['jb']['zs'] = $data['2dj_jb_zs'];
                    $dfields['2dj']['jb']['dzjj'] = $data['2dj_jb_dzjj'];
                    $dfields['2dj']['zj']['zs'] = $data['2dj_zj_zs'];
                    $dfields['2dj']['zj']['dzjj'] = $data['2dj_zj_dzjj'];
                    $dfields['3dj']['jb']['zs'] = $data['3dj_jb_zs'];
                    $dfields['3dj']['jb']['dzjj'] = $data['3dj_jb_dzjj'];
                    $dfields['3dj']['zj']['zs'] = $data['3dj_zj_zs'];
                    $dfields['3dj']['zj']['dzjj'] = $data['3dj_zj_dzjj'];
                    $dfields['4dj']['jb']['zs'] = $data['4dj_jb_zs'];
                    $dfields['4dj']['jb']['dzjj'] = $data['4dj_jb_dzjj'];
                    $dfields['4dj']['zj']['zs'] = $data['4dj_zj_zs'];
                    $dfields['4dj']['zj']['dzjj'] = $data['4dj_zj_dzjj'];
                    $dfields['5dj']['jb']['zs'] = $data['5dj_jb_zs'];
                    $dfields['5dj']['jb']['dzjj'] = $data['5dj_jb_dzjj'];
                    $dfields['5dj']['zj']['zs'] = $data['5dj_zj_zs'];
                    $dfields['5dj']['zj']['dzjj'] = $data['5dj_zj_dzjj'];
                    $dfields['6dj']['jb']['zs'] = $data['6dj_jb_zs'];
                    $dfields['6dj']['jb']['dzjj'] = $data['6dj_jb_dzjj'];
                    if($data['6dj_zj_dzjj'] || $data['6dj_zj_zs'])
                    {
                    	$dfields['6dj']['zj']['zs'] = $data['6dj_zj_zs'];
                    	$dfields['6dj']['zj']['dzjj'] = $data['6dj_zj_dzjj'];
                    }
                    $info['bonusDetail'] = json_encode($dfields);
                    break;

                case 'qxc':
                    $dfields = array();
                    $dfields['1dj']['zs'] = $data['1dj_zs'];
                    $dfields['1dj']['dzjj'] = $data['1dj_dzjj'];
                    $dfields['2dj']['zs'] = $data['2dj_zs'];
                    $dfields['2dj']['dzjj'] = $data['2dj_dzjj'];
                    $dfields['3dj']['zs'] = $data['3dj_zs'];
                    $dfields['3dj']['dzjj'] = $data['3dj_dzjj'];
                    $dfields['4dj']['zs'] = $data['4dj_zs'];
                    $dfields['4dj']['dzjj'] = $data['4dj_dzjj'];
                    $dfields['5dj']['zs'] = $data['5dj_zs'];
                    $dfields['5dj']['dzjj'] = $data['5dj_dzjj'];
                    $dfields['6dj']['zs'] = $data['6dj_zs'];
                    $dfields['6dj']['dzjj'] = $data['6dj_dzjj'];
                    $info['bonusDetail'] = json_encode($dfields);
                    break;

                case 'qlc':
                    $dfields = array();
                    $dfields['1dj']['zs'] = $data['1dj_zs'];
                    $dfields['1dj']['dzjj'] = $data['1dj_dzjj'];
                    $dfields['2dj']['zs'] = $data['2dj_zs'];
                    $dfields['2dj']['dzjj'] = $data['2dj_dzjj'];
                    $dfields['3dj']['zs'] = $data['3dj_zs'];
                    $dfields['3dj']['dzjj'] = $data['3dj_dzjj'];
                    $dfields['4dj']['zs'] = $data['4dj_zs'];
                    $dfields['4dj']['dzjj'] = $data['4dj_dzjj'];
                    $dfields['5dj']['zs'] = $data['5dj_zs'];
                    $dfields['5dj']['dzjj'] = $data['5dj_dzjj'];
                    $dfields['6dj']['zs'] = $data['6dj_zs'];
                    $dfields['6dj']['dzjj'] = $data['6dj_dzjj'];
                    $dfields['7dj']['zs'] = $data['7dj_zs'];
                    $dfields['7dj']['dzjj'] = $data['7dj_dzjj'];
                    $info['bonusDetail'] = json_encode($dfields);
                    break;

                case 'fc3d':
                    $dfields = array();
                    $dfields['zx']['zs'] = $data['zx_zs'];
                    $dfields['zx']['dzjj'] = $data['zx_dzjj'];
                    $dfields['z3']['zs'] = $data['z3_zs'];
                    $dfields['z3']['dzjj'] = $data['z3_dzjj'];
                    $dfields['z6']['zs'] = $data['z6_zs'];
                    $dfields['z6']['dzjj'] = $data['z6_dzjj'];
                    $info['bonusDetail'] = json_encode($dfields);
                    break;

                case 'pl3':
                    $dfields = array();
                    $dfields['zx']['zs'] = $data['zx_zs'];
                    $dfields['zx']['dzjj'] = $data['zx_dzjj'];
                    $dfields['z3']['zs'] = $data['z3_zs'];
                    $dfields['z3']['dzjj'] = $data['z3_dzjj'];
                    $dfields['z6']['zs'] = $data['z6_zs'];
                    $dfields['z6']['dzjj'] = $data['z6_dzjj'];
                    $info['bonusDetail'] = json_encode($dfields);
                    break;

                case 'pl5':
                    $dfields = array();
                    $dfields['zx']['zs'] = $data['zx_zs'];
                    $dfields['zx']['dzjj'] = $data['zx_dzjj'];
                    $info['bonusDetail'] = json_encode($dfields);
                    break;

                case 'sfc':
                    $dfields = array();
                    $dfields['1dj']['zs'] = $data['1dj_zs'];
                    $dfields['1dj']['dzjj'] = $data['1dj_dzjj'];
                    $dfields['2dj']['zs'] = $data['2dj_zs'];
                    $dfields['2dj']['dzjj'] = $data['2dj_dzjj'];
                    $dfields['rj']['zs'] = $data['rj_zs'];
                    $dfields['rj']['dzjj'] = $data['rj_dzjj'];
                    $info['bonusDetail'] = json_encode($dfields);
                    $info['rj_sale'] = $data['rj_sale'];
                    $info['sfc_sale'] = $data['sfc_sale'];
                    break;

                case 'bqc':
                    $dfields = array();
                    $dfields['1dj']['zs'] = $data['1dj_zs'];
                    $dfields['1dj']['dzjj'] = $data['1dj_dzjj'];
                    $info['bonusDetail'] = json_encode($dfields);
                    break;

                case 'jqc':
                    $dfields = array();
                    $dfields['1dj']['zs'] = $data['1dj_zs'];
                    $dfields['1dj']['dzjj'] = $data['1dj_dzjj'];
                    $info['bonusDetail'] = json_encode($dfields);
                    break;

                default:
                    # code...
                    break;
            }
            if (in_array($data['type'], array('sfc', 'bqc', 'jqc')))
            {
                $table = "getIssueStatus" . $data['type'];
                $infos = $this->Model_issue->$table($data);
            }
            else
            {
                $infos = $this->Model_issue->getIssueStatus($data);
            }
            foreach($infos as $key => $value)
            {
                $infoData['end_time'] = $value[0]['end_time'];
                $infoData['status'] = $value[0]['status'];
                $infoData['rstatus'] = $value[0]['rstatus'];
            }
            //获取对比状态
            if (in_array($data['type'], array('sfc', 'bqc', 'jqc')))
            {
                $data['status'] = $this->getIssueStatusByJjc($infoData['end_time'], $infoData['status'],
                    $infoData['rstatus']);
            }
            elseif (in_array($data['type'], array('bjdc', 'sfgg')))
            {
                $data['status'] = $this->getIssueStatusByBjdc($infoData['end_time'], $infoData['status'],
                    $infoData['rstatus']);
            }
            else
            {
                $data['status'] = $this->getIssueStatus($infoData['end_time'], $infoData['status'], $infoData['rstatus']);
            }
            $info['issue'] = $data['issue'];
            $info['awardNum'] = trim($data['awardNum']);
            $info['sale'] = $data['sale'];
            $info['pool'] = $data['pool'];
            $info['type'] = $data['type'];
            $data['status'] == '截止' ? $info['d_synflag'] = 0 : $info['d_synflag'] = "";
            //入库
            if (in_array($data['type'], array('sfc', 'bqc', 'jqc')))
            {
                $mothed = "updateDetailList_" . $data['type'];
                $res = $this->Model_issue->$mothed($info);
            } else {
                //验证有误开奖号码
                $tag = $this->Model_issue->checkHasAwardNum($data['issue'],$data['type']);
                if($tag) 
                {
                    echo "<script> alert('所选期次暂无开奖号码，请选择已抓取号码期次进行详情录入~');window.location.href='".$data['backUrl']."';</script>"; 
                    exit();
                }
                $res = $this->Model_issue->updateDetailList($info);
            }
            $lname = $this->lrule[$info['type']]['name'];
            $this->syslog(17, '修改'.$lname."第".$info['issue']."期开奖详情操作" );
            header('Location:/backend/Issue/detail?lid=' . $data['type'] . '&issue=' . $data['issue']);
        }
    }

    public function saleStart()
    {
    	$env = $this->input->post('env');
    	$this->checkenv($env, true);
        $return = FALSE;
        $issues = $this->input->post('issues', TRUE);
        if ( ! empty($issues))
        {
            $return = $this->Model_issue->saleStart($issues);
        }
        echo $return;
    }

    public function issueDelete()
    {
    	$env = $this->input->post('env');
    	$this->checkenv($env, true);
        $this->check_capacity('7_2_6');
        $return = FALSE;
        $issues = $this->input->post('issues', TRUE);
        $type = $this->input->post('type', TRUE);
        if ( ! empty($issues))
        {
        	if(in_array($type, array('syxw', 'jxsyxw', 'ks', 'jlks', 'jxks', 'hbsyxw', 'klpk', 'cqssc', 'gdsyxw')))
        	{
        		$return = $this->Model_issue->issueDelete($issues, $type);
        	}
        	else
        	{
        		die($return);
        	}
        }
        $lname = array('jsxsyx' => '江西十一选五', 'syxw' => '十一选五', 'ks' => '上海快三', 'jlks' => '吉林快三', 'jxks' => '江西快三', 'hbsyxw' => '湖北十一选五', 'klpk' => '快乐扑克', 'cqssc' => '老时时彩', 'gdsyxw' => '广东十一选五');
        foreach($issues as $issue)
        {
            $this->syslog(17, $lname[$type] . "第".$issue."期进行删除期次操作" );
        }
        echo $return;
    }
    /**
     * [aduitIssue 期次号码审核操作]
     * @author LiKangJian 2017-09-22
     * @return [type] [description]
     */
    public function aduitIssue()
    {
    	$env = $this->input->post('env');
    	$this->checkenv($env, true);
        $this->check_capacity('7_2_8', true);
        $issue = $this->input->post('issue', TRUE);
        $type = $this->input->post('type', TRUE);
        $awardnum = $this->input->post('awardNum', TRUE);
        //查询并更新
        $tag = $this->Model_issue->aduitIssue($issue,$type,$awardnum);
        if($tag === true)
        {
            $this->syslog(17, $this->lrule[$type]['name'].'第'.$issue.'期开奖号码审核成功~');
            //保留接口触发同步操作
            return $this->ajaxReturn('y', '开奖号码审核成功~');
        }else{
            $this->syslog(17, $this->lrule[$type]['name'].'第'.$issue.'期开奖号码审核失败~');
            if($tag===2)
            {
                return $this->ajaxReturn('n', '请不要重复提交审核~');
            }
            return $this->ajaxReturn('n', '号码审核失败，请重新审核~');
        }
        
    }
    /**
     * [insertAwardNum 录入开奖号码]
     * @author LiKangJian 2017-09-25
     * @return [type] [description]
     */
    public function insertAwardNum()
    {
    	$env = $this->input->post('env');
    	$this->checkenv($env, true);
        $this->check_capacity('7_2_3', true);
        $issue = $this->input->post('issue', TRUE);
        $type = $this->input->post('type', TRUE);
        $awardNum = $this->input->post('awardNum', TRUE);
        //验证开奖号码的正确性
        $checkNum = $this->checkAwardNumByType($type,$awardNum);
        if(!$checkNum) return $this->ajaxReturn('n', '输入的开奖号码格式不正确~');
        //验证是否有开奖号码没有不能录入
        $isAduit = $this->Model_issue->checkIsAduit($issue,$type);
        if($isAduit) return $this->ajaxReturn('n', '号码已审核，无法修改~');
        //验证审核太
        //写入开奖号码
        $tag = $this->Model_issue->insertAwardNum($issue,$type,$awardNum);
        if($tag)
        {
            $this->syslog(17, $lname[$type] . "录入".$this->lrule[$type]['name']."第".$issue."期开奖号码为：". $awardNum);
            //触发遗漏
            $this->calculationMiss($this->lrule[$type]['lid'] ,$issue);
            return $this->ajaxReturn('y', "录入开奖号码成功~");
        }
        return $this->ajaxReturn('n', "录入开奖号码失败~");
    }
    /**
     * [checkAwardNumByType 根据彩种判断更新的号码]
     * @author LiKangJian 2017-09-22
     * @param  [type] $type [description]
     * @return [type]       [description]
     */
    private function checkAwardNumByType($type,$awardNum)
    {
        $method = 'check'.ucfirst($type).'AwardNum';
        return $this->$method($awardNum);
    }
    /**
     * [checkSsqAwardNum 验证双色球号码的正确性]
     * @author LiKangJian 2017-09-25
     * @param  [type] $awardNum [description]
     * @return [type]           [description]
     */
    private function checkSsqAwardNum($awardNum)
    {
        $arr = explode('|', $awardNum);
        $newPre = $pre = explode(',', $arr[0]);
        $next = array($arr[1]);
        $preArr = $this->getBalls(33,1);
        sort($newPre);
        $nextArr = $this->getBalls(16,1);
        if( array_intersect($pre,$preArr) == $pre 
            && array_unique($pre) == $pre 
            && $pre == $newPre
            && array_intersect($next,$nextArr) == $next 
        )
        {
            return true;
        }
        return false;
    }
    /**
     * [checkDltAwardNum 验证大乐透号码的正确性]
     * @author LiKangJian 2017-09-25
     * @param  [type] $awardNum [description]
     * @return [type]           [description]
     */
    private function checkDltAwardNum($awardNum)
    {
        $arr = explode('|', $awardNum);
        $newPre = $pre = explode(',', $arr[0]);
        $newNext = $next = explode(',', $arr[1]);
        $preArr = $this->getBalls(35,1);
        $nextArr = $this->getBalls(12,1);
        sort($newPre);
        sort($newNext);
        if( array_intersect($pre,$preArr) == $pre 
            && array_unique($pre) == $pre
            && array_intersect($next,$nextArr) == $next
            && array_unique($next) == $next 
            && $pre == $newPre
            && $next == $newNext
        )
        {
            return true;
        }
        return false;
    }
    /**
     * [checkQlcAwardNum 验证七乐彩号码的正确性]
     * @author LiKangJian 2017-09-25
     * @param  [type] $awardNum [description]
     * @return [type]           [description]
     */
    private function checkQlcAwardNum($awardNum)
    {
        $arr = explode('(', $awardNum);
        $newPre = $pre = explode(',', $arr[0]);
        sort($newPre);
        $arr = array_merge(explode(',', $arr[0]),array( trim($arr[1],')') ));
        $balls = $this->getBalls(30,1);
        if( array_intersect($arr,$balls) == $arr 
            && array_unique($arr) == $arr 
            && $pre == $newPre
        )
        {
            return true;
        }
        return false;
    }
    /**
     * [checkQxcAwardNum 验证七星彩号码的正确性]
     * @author LiKangJian 2017-09-25
     * @param  [type] $awardNum [description]
     * @return [type]           [description]
     */
    private function checkQxcAwardNum($awardNum)
    {
        return $this->checkAwardNumComm($awardNum,7);
    }
    /**
     * [checkFc3dAwardNum 验证福彩3D号码的正确性]
     * @author LiKangJian 2017-09-25
     * @param  [type] $awardNum [description]
     * @return [type]           [description]
     */
    private function checkFc3dAwardNum($awardNum)
    {
        return $this->checkAwardNumComm($awardNum,3);
    }
    /**
     * [checkPl3AwardNum 验证排列3号码的正确性]
     * @author LiKangJian 2017-09-25
     * @param  [type] $awardNum [description]
     * @return [type]           [description]
     */
    private function checkPl3AwardNum($awardNum)
    {
        return $this->checkAwardNumComm($awardNum,3);
    }
    /**
     * [checkPl5AwardNum 验证排列5号码的正确性]
     * @author LiKangJian 2017-09-25
     * @param  [type] $awardNum [description]
     * @return [type]           [description]
     */
    private function checkPl5AwardNum($awardNum)
    {
        return $this->checkAwardNumComm($awardNum,5);
    }
    /**
     * [checkAwardNumComm 公用方法]
     * @author LiKangJian 2017-09-25
     * @param  [type] $awardNum [description]
     * @param  [type] $numLen   [description]
     * @return [type]           [description]
     */
    private function checkAwardNumComm($awardNum,$numLen)
    {
        $tag = true;
        $arr = explode(',', $awardNum);
        foreach ($arr as $v) 
        {
            if(intval($v) < 0 || intval($v) > 9 )
            {
                $tag = false;
                return;
            }
        }
        return $tag && count($arr) == $numLen;
    }
    /**
     * [getBalls 号码球]
     * @author LiKangJian 2017-09-25
     * @param  [type] $len  [description]
     * @param  [type] $flag [description]
     * @return [type]       [description]
     */
    private function getBalls($len,$flag)
    {
        $balls = array();
        $i = 0;
        do
        {
         $code = (string)($i+1);//转化成字符串
         if($flag==1) $code =  strlen($code) == 2 ? $code :'0'.$code;
         array_push($balls, $code);
         $i++;
        }while ($i<$len);
        return $balls;
    }
	public function updateAwardNum()
	{
		$env = $this->input->post('env');
		$this->checkenv($env, true);
	    $this->check_capacity('7_2_7', true);
		$issue = $this->input->post('issueId', TRUE);
		$type = $this->input->post('type', TRUE);
		$awardnum = $this->input->post('awardnum', TRUE);
		$awardnumagain = $this->input->post('awardnumAgain', TRUE);
        // 快乐扑克花色
        $awardtype = $this->input->post('awardtype', TRUE);
        $awardtypeagain = $this->input->post('awardtypeAgain', TRUE);

		$awardnum = implode(',', $awardnum);
		$awardnumagain = implode(',', $awardnumagain);
		if(in_array($type, array('syxw', 'jxsyxw', 'hbsyxw', 'gdsyxw')))
		{
			if(str_replace(',', "", $awardnum) != str_replace(',', "", $awardnumagain) || preg_match('/^[0-9]{2},[0-9]{2},[0-9]{2},[0-9]{2},[0-9]{2}$/',$awardnum) == false
					|| preg_match('/^[0-9]{2},[0-9]{2},[0-9]{2},[0-9]{2},[0-9]{2}$/',$awardnumagain) == false )
			{
				return $this->ajaxReturn('n', $this->msg_text_cfg['falied']);
			}
			$row = $this->Model_issue->updateAwardNum($type, $issue, $awardnum);
		}
		elseif (in_array($type, array('ks','jlks','jxks')))
		{
			if(str_replace(',', "", $awardnum) != str_replace(',', "", $awardnumagain) || preg_match('/^[1-6]{1},[1-6]{1},[1-6]{1}$/',$awardnum) == false
					|| preg_match('/^[1-6]{1},[1-6]{1},[1-6]{1}$/',$awardnumagain) == false )
			{
				return $this->ajaxReturn('n', $this->msg_text_cfg['falied']);
			}
			$row = $this->Model_issue->updateKsAwardNum($type, $issue, $awardnum);
		}
        elseif (in_array($type, array('klpk')))
        {
            // 一副牌
            $numArr = explode(',', $awardnum);
            if(($awardtype[0] . $numArr[0] == $awardtype[1] . $numArr[1]) || ($awardtype[1] . $numArr[1] == $awardtype[2] . $numArr[2]) || ($awardtype[0] . $numArr[0] == $awardtype[2] . $numArr[2]))
            {
                return $this->ajaxReturn('n', $this->msg_text_cfg['falied']);
            }
            $awardtype = implode(',', $awardtype);
            $awardtypeagain = implode(',', $awardtypeagain);
            if(str_replace(',', "", $awardnum) != str_replace(',', "", $awardnumagain) || preg_match('/^[0-9]{2},[0-9]{2},[0-9]{2}$/',$awardnum) == false || preg_match('/^[0-9]{2},[0-9]{2},[0-9]{2}$/',$awardnumagain) == false )
            {
                return $this->ajaxReturn('n', $this->msg_text_cfg['falied']);
            }
            $awardnum = $awardnum . '|' . $awardtype;
            $row = $this->Model_issue->updateKlpkAwardNum($type, $issue, $awardnum);
        }
        elseif(in_array($type, array('cqssc')))
        {
            if(str_replace(',', "", $awardnum) != str_replace(',', "", $awardnumagain) || preg_match('/^[0-9]{1},[0-9]{1},[0-9]{1},[0-9]{1},[0-9]{1}$/',$awardnum) == false
                    || preg_match('/^[0-9]{1},[0-9]{1},[0-9]{1},[0-9]{1},[0-9]{1}$/',$awardnumagain) == false )
            {
                return $this->ajaxReturn('n', $this->msg_text_cfg['falied']);
            }
            $row = $this->Model_issue->updateCqsscAwardNum($type, $issue, $awardnum);
        }
		else
		{
			return $this->ajaxReturn('n', $this->msg_text_cfg['falied']);
		}
		
		if ($row === false)
		{
			return $this->ajaxReturn('n', $this->msg_text_cfg['falied']);
		}
		$lname = array('jsxsyx' => '江西十一选五', 'syxw' => '十一选五', 'ks' => '上海快三', 'jxks' => '江西快三', 'hbsyxw' => '湖北十一选五', 'klpk' => '快乐扑克', 'gdsyxw' => '广东十一选5');
		$this->syslog(17, $lname[$type] . "第".$issue."期修改开奖号码为".$awardnum );
		return $this->ajaxReturn('y', $this->msg_text_cfg['success']);
	}

	public function bjdcStart()
    {
    	$env = $this->input->post('env');
    	$this->checkenv($env, true);
        $return = FALSE;
        $issues = $this->input->post('issues', TRUE);
        if ( ! empty($issues))
        {
            $return = $this->Model_issue->bjdcStart($issues);
        }
        echo $return;
    }

    public function sfcStart()
    {
    	$env = $this->input->post('env');
    	$this->checkenv($env, true);
        $return = FALSE;
        $issues = $this->input->post('issues', TRUE);
        if ( ! empty($issues))
        {
            $return = $this->Model_issue->sfcStart($issues);
        }
        echo $return;
    }

    //获取对比异常的数据
    public function compareDetail()
    {
        $lrule = $this->lrule;
        $data = array();
        $data['type'] = $this->input->post('type', TRUE);
        $data['issue'] = $this->input->post('issue', TRUE);
        if (in_array($data['type'], array('sfc', 'bqc', 'jqc')))
        {
            $data['detail'] = $this->Model_issue->getCompareDetailByTczq($data);
        }
        else
        {
            $data['detail'] = $this->Model_issue->getCompareDetail($data);
        }
        $data['name'] = $lrule[$data['type']]['name'];
        if ( ! empty($data['detail']))
        {
            echo $this->load->view("issue/compare_detail", $data, TRUE);
        }
        else
        {
            echo 2;
        }
        exit;
    }

    /**
     * 删除从指定期次
     * @return json
     */
    public function recountIssue()
    {
    	$env = $this->input->post('env');
    	$this->checkenv($env, true);
        $this->check_capacity('7_2_5', true);
        $type = $this->input->post('type', TRUE);
        $issue = $this->input->post('period', TRUE);
        $lid = $this->lrule[$type]['lid'];
        if (intval($issue) > 0)
        {
            $this->calculationMiss($lid,$issue);
        }
        return $this->ajaxReturn('y', '');
    }
    /**
     * [CalculationMiss 触发遗漏计算]
     * @author LiKangJian 2017-10-18
     * @param  [type] $lid   [description]
     * @param  [type] $issue [description]
     */
    private function calculationMiss($lid,$issue)
    {
        $this->load->model('Model_missed');
        $status= $this->Model_missed->deleteIssue($lid, $issue);
        //触发计算遗漏
        $this->load->model('task_model');
        $this->task_model->updateStop(8, $lid, 0);
        $this->load->model('lottery_model', 'lottery');
        $this->lottery->frushKjHistory($lid);
    }

}
