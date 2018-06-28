<?php
class tag extends MY_Controller{
    
    protected $_basetypeArr = array(0 => '用户行为', 1 => '用户信息');
    
    protected $_subtypeArr = array(0 => '购彩');
    
    protected $_scopeArr = array(0 => '无', 1 => '购买彩种', 2 => '购彩方式', 4 => '购彩渠道', 8 => '购彩平台', 16 => '购彩累计天数', 32 => '购彩订单总数', 64 => '最后购彩时间', 128 => '累计购彩金额');
    
    protected $_ctypeArr = array(0 => '日常推送', 1 => '日常短信', 2 => '日常红包', 3 => '数据监控', 4 => '临时活动', 5 => '其他');
    
    protected $_upLogicArr = array(0 => '不更新 ', 1 => '每天', 2 => '每周', 3 => '每月');
    
    public function __construct() {
        parent::__construct();
        $this->load->model('model_tag', 'Model');
    }
    
    public function index() {
        $this->check_capacity('3_16_1');
        $this->load->view('tag/index');
    }
    
    public function add() {
        $this->check_capacity('3_16_2');
        $this->load->view('tag/add');
    }
    
    public function cluster() {
        $this->check_capacity('3_17_1');
        $this->load->view('tag/cluster');
    }
    
    public function add_cluster() {
        $this->check_capacity('3_17_2');
        $this->load->view('tag/add_cluster');
    }
    
    public function get_list() {
        $this->check_capacity('3_16_1', true);
        $inputArr = $this->input->get();
        $schData = array(
            'name' => $inputArr['name'],
            'start_time' => $inputArr['data'][0],
            'end_time' => $inputArr['data'][1],
            'scope'     => $inputArr['scope']
        );
        $perPage = isset($inputArr['num']) ? $inputArr['num'] : self::NUM_PER_PAGE;
        $pageNum = isset($inputArr['pageNum']) ? $inputArr['pageNum'] : 1;
        $res = $this->Model->get_list($schData, ($pageNum-1) * $perPage, $perPage);
        foreach ($res['data'] as &$val) {
            $wd = array();
            for ($i = 1; $i < 129; $i*=2) {
                if ($val['scope'] & $i) array_push($wd, $this->_scopeArr[$i]); 
            }
            $val['weidu'] = implode(',', $wd);
        }
        exit(json_encode(array('total' => $res['count'], 'curPage' => $pageNum, 'data' => $res['data'])));
    }
    
    public function get_data($id) {
        $this->check_capacity('3_16_3', true);
        $res = $this->Model->get_data($id);
        $filter = $having = array();
        $condition = json_decode($res['conditions'], true);
        foreach ($condition['filter'] as $key => $val) {
            if (empty($val['val'])) unset($condition['filter'][$key]);
        }
        foreach ($condition['having'] as $key => &$val) {
            if (empty($val)) unset($condition['having'][$key]);
            if ($key == 'total_money') $val = preg_replace(array('/00-/', '/00$/'), array('-', ''), $val);
        }
        $data = array(
            'tag_name'  => $res['tag_name'], 
            'tag_desc'  => $res['tag_desc'], 
            'base_type' => $res['base_type'], 
            'sub_type'  => $res['sub_type'],
            'date'      => $condition['date']
        );
        if (!empty($condition['filter'])) $data['filter'] = $condition['filter'];
        if (!empty($condition['having'])) $data['having'] = $condition['having'];
        exit(json_encode($data));
    }
    
    public function save_data() {
        $this->check_capacity('3_16_2', true);
        $inputArr = $this->input->post();
        $mustArr = array('tag_name' => '标签名', 'tag_desc' => '标签描述', 'date' => '购彩时间');
        foreach ($mustArr as $key => $val) {
            if (empty($inputArr[$key])) exit(json_encode(array('status' => '400', 'msg' => "请填写".$val)));
        }
        if (empty($inputArr['filter']) && empty($inputArr['having'])) exit(json_encode(array('status' => '400', 'msg' => "请填写基本条件或者复合条件")));
        $scopeArr = array('lid' => 1, 'orderType' => 2, 'channel' => 4, 'platform' => 8, 'total_day' => 16, 'total_buy_num' => 32, 'last_buy_time' => 64, 'total_money' => 128);
        $condition = array(
            'filter' => array(
                'lid' => array('logic' => '', 'val' => ''), 
                'orderType' => array('logic' => '', 'val' => ''), 
                'platform' => array('logic' => '', 'val' => ''), 
                'channel' => array('logic' => '', 'val' => '')
            ),
            'having' => array('total_money' => '0', 'total_day' => '0', 'total_buy_num' => '0', 'last_buy_time' => '0'),
            'date' => array('range' => '0', 'start' => "", 'end'   => "")
        );
        $scope = 0;
        if (isset($inputArr['filter'])) {
            $filter = json_decode($inputArr['filter'], true);
            foreach ($filter as $key => $val) {
                if (!empty($val)) {
                    $condition['filter'][$key] = $val;
                    $scope += $scopeArr[$key];
                }
            }
        }
        if (isset($inputArr['having'])) {
            $having = json_decode($inputArr['having'], true);
            foreach ($having as $key => $val) {
                if (!empty($val)) {
                    if ($key == 'total_money') {
                        $valArr = explode('-', $val);
                        foreach ($valArr as &$v) {
                            if ($v !== '*') $v = (int)$v * 100;
                        }
                        $val = implode('-', $valArr);
                    }
                    $condition['having'][$key] = $val;
                    $scope += $scopeArr[$key];
                }
            }
        }
        if (isset($inputArr['date'])) {
            $date = json_decode($inputArr['date'], true);
            if (isset($date['range'])) {
                $condition['date']['range'] = $date['range'];
            } elseif (isset($date['start']) && isset($date['end'])) {
                $condition['date']['start'] = $date['start'];
                $condition['date']['end'] = $date['end'];
            }
        }
        $data = array(
            'tag_name'  => $inputArr['tag_name'],
            'tag_desc'  => $inputArr['tag_desc'],
            'base_type' => $inputArr['base_type'],
            'sub_type'  => $inputArr['sub_type'],
            'conditions' => json_encode($condition),
            'scope'     => $scope,
        );
        $this->Model->save_data($data);
        $this->syslog(68, "新建{$data['tag_name']}标签" );
        exit(json_encode(array('status' => '200', 'msg' => '创建成功')));
    }
  
    public function del_data() {
        $this->check_capacity('3_16_3', true);
        $id = $this->input->post('id');
        if ($this->Model->del_data($id)) {
            $data = $this->Model->getTagInfo($id);
            $this->syslog(68, "删除{$data['tag_name']}标签" );
            exit(json_encode(array('status' => '200', 'msg' => '删除成功')));
        }
        exit(json_encode(array('status' => '400', 'msg' => '删除失败')));
    }
    
    public function del_cluster() {
        $this->check_capacity('3_17_3', true);
        $id = $this->input->post('id');
        if ($this->Model->del_cluster($id)) {
            $data = $this->Model->getTagInfo($id);
            $this->syslog(68, "删除{$data['tag_name']}标签" );
            exit(json_encode(array('status' => '200', 'msg' => '删除成功')));
        }
        exit(json_encode(array('status' => '400', 'msg' => '删除失败')));
    }
    
    public function get_cluster_list() {
        $this->check_capacity('3_17_1', true);
        $inputArr = $this->input->get();
        $schData = array(
            'name'      => $inputArr['name'],
            'start_time'=> $inputArr['data'][0],
            'end_time'  => $inputArr['data'][1],
            'ctype'     => $inputArr['ctype'],
            'scope'     => $inputArr['scope']
        );
        $perPage = isset($inputArr['num']) ? $inputArr['num'] : self::NUM_PER_PAGE;
        $pageNum = isset($inputArr['pageNum']) ? $inputArr['pageNum'] : 1;
        $res = $this->Model->get_cluster_list($schData, ($pageNum-1) * $perPage, $perPage);
        foreach ($res['data'] as &$val) {
            $val['ctype'] = $this->_ctypeArr[$val['ctype']];
            $val['update_logic'] = $this->_upLogicArr[$val['update_logic']];
        }
        exit(json_encode(array('total' => $res['count'], 'curPage' => $pageNum, 'data' => $res['data'])));
    }
    
    public function save_cluster($id = null) {
        $this->check_capacity('3_17_2', true);
        $inputArr = $this->input->post();
        if (isset($inputArr['tagids'])) {
            $tagids = explode(',', $inputArr['tagids']);
            sort($tagids);
        }
        $data = array();
        if (isset($inputArr['cluster_name'])) $data['cluster_name'] = $inputArr['cluster_name'];
        if (isset($inputArr['cluster_desc'])) $data['cluster_desc'] = $inputArr['cluster_desc'];
        if (isset($inputArr['ctype'])) $data['ctype'] = $inputArr['ctype'];
        if (isset($inputArr['tagids'])) $data['tag_ids'] = implode(',', $tagids);
        if (isset($inputArr['update_logic'])) $data['update_logic'] = $inputArr['update_logic'];
        if ($id) $data['id'] = $id;
        $this->Model->save_cluster($data);
        if (empty($id)) {
            $mustArr = array('cluster_name' => '集群说明', 'cluster_desc' => '集群描述', 'tagids' => '标签的集合');
            foreach ($mustArr as $key => $val) {
                if (empty($inputArr[$key])) exit(json_encode(array('status' => '400', 'msg' => "请填写".$val)));
            }
            $this->syslog(69, "新建{$data['tag_name']}标签群组" );
            exit(json_encode(array('status' => '200', 'msg' => '创建群组成功')));
        } else {
            $this->syslog(69, "修改{$data['tag_name']}标签群组" );
            exit(json_encode(array('status' => '200', 'msg' => '修改群组成功')));
        }
        exit(json_encode(array('status' => '400', 'msg' => '更新失败')));
    }
    
    public function get_cluster($id) {
        $this->check_capacity('3_17_3', true);
        $res = $this->Model->get_cluster($id);
        exit(json_encode($res));
    }
    
    public function get_tags_info($clusterid) {
        $this->check_capacity('3_17_3', true);
        $res = $this->Model->getTagsInfo($clusterid);
        exit(json_encode($res));
    }
    
    public function export($id) {
        $this->check_capacity('3_16_3', true);
        $info = $this->Model->getTagInfo($id);
        $start = 0;
        $offset = 1000;
        $this->load->library('excel');
        $this->excel->setActiveSheetIndex(0);
        $this->excel->getActiveSheet()->setCellValue('A1', 'Uid');
        while ($uids = $this->Model->getUidByTag($id, $start, $offset)) {
            foreach ($uids as $k => $uid) {
                $this->excel->getActiveSheet()->setCellValue('A'.($start+$k+2), $uid);
            }
            $start += 1000;
        }
        $fileName = $info['tag_name']."-".substr($info['runtime'], 0, 10);
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment;filename="' . $fileName . '.csv"');
        header('Cache-Control: max-age=0');
         
        $objWriter = PHPExcel_IOFactory::createWriter($this->excel, 'CSV');
        $objWriter->save('php://output');
        $this->syslog(68, "导出{$info['tag_name']}UID" );
    }
    
    public function export_cluster($id) {
        $this->check_capacity('3_17_3', true);
        $info = $this->Model->getClusterInfo($id);
        $this->load->remove_package_path();
        $fileName = $info['cluster_name']."-".substr($info['update_date'], 0, 10);
        $this->load->library('/usertag/Capture');
        $this->capture->clusterInter($id, json_decode($info['conditions'], true), $fileName);
        $this->syslog(69, "导出{$info['tag_name']}群UID" );
    }
    
    public function caculate_uids() {
        $tagids = $this->input->get_post('tagids');
        foreach ($tagids as &$tid) {
            if ($tid <= 0) unset($tid);
        }
        $res = $this->Model->caculate_uids($tagids);
        exit(json_encode($res));
    }
    
    public function get_tag_ids($type = 'top') {
        if ($type === 'top') {
            $res = $this->Model->get_top_tag_ids($type);
        }else {
            $inputArr = $this->input->get();
            $perPage = isset($inputArr['num']) ? $inputArr['num'] : 8;
            $pageNum = isset($inputArr['page']) ? $inputArr['page'] : 1;
            $scopeTypeArr = array('lid' => 1, 'method' => 2, 'channel' => 4, 'platform' => 8, 'money' => 128, 'days' => 16, 'orders' => 32, 'lasttime' => 64);
            if (!array_key_exists($type, $scopeTypeArr)) exit(array('status' => '400', 'msg' => '参数错误'));
            $res = $this->Model->get_tag_ids($scopeTypeArr[$type], ($pageNum - 1) * $perPage, $perPage);
            $res['curPage'] = $pageNum;
        }
        exit(json_encode($res));
    }
    
    public function get_lids() {
        $this->load->config('caipiao');
        $all = $this->config->item('caipiao_all_cfg');
        $res = array();
        foreach ($all['caipiao_cfg'] as $lid => $caipiao) {
            array_push($res, array('id' => $lid, 'name' => $caipiao['name']));
        }
        exit(json_encode($res));
    }
    
    public function get_scopes() {
        exit(json_encode($this->_scopeArr));
    }
}