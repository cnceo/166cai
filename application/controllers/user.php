<?php

class User extends MY_Controller
{

    private $pic    = array('<img src="/caipiaoimg/v1.1/img/hg.gif">',
                          '<img src="/caipiaoimg/v1.1/img/ty.gif">', 
                          '<img src="/caipiaoimg/v1.1/img/yl.gif">', 
                          '<img src="/caipiaoimg/v1.1/img/xx.gif">');

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * 合买个人信息入口
     * @param string $oid userId加密
     */
    public function index($oid)
    {
        if ($oid == "getHistoricalData")
        {
            return $this->getHistoricalData();
        }
        if ($oid == "getCount")
        {
            return $this->getCount();
        }
        if ($oid == "updateIntroduction")
        {
            return $this->updateIntroduction();
        }
        if( $oid == "getGendanData")
        {
            return $this->getGendanData();
        }
        if ($oid == "gendanlist")
        {
            return $this->gendanlist();
        }
        $uid = json_decode(strCode(urldecode($oid)), true);
        $id = intval($uid['uid']);
        $lid = intval($uid['lid']);
        if(empty($id))
        {
        	$this->redirect('/error');
        }
        $page = intval($this->input->get("cpage", true));
        $page = $page <= 1 ? 1 : $page;
        $order = $this->input->post("order", true) ? $this->input->post("order", true) : '00';
        $this->load->model('user_model');
        $user = $this->user_model->findByUid($id);
        if(empty($user))
        {
        	$this->redirect('/error');
        }
        $this->load->model('united_order_model');
        $points = $this->united_order_model->getPoints($id, 0);
        $user['record'] = calGrade($points[0]);
        $offset = ($page - 1) * 5;
        $fields = 'o.isTop, o.orderId, o.lid, o.buyTotalMoney, o.money, o.guaranteeAmount, o.issue';
        if ($this->uid) $fields .= " ,(SELECT id FROM cp_united_join WHERE orderId=o.orderId AND uid='".$this->uid."' LIMIT 1) as ujoin";
        $unitedOrders = $this->united_order_model->getOrder(array('o.uid' => $id, 'o.status' => array(40, 500, 240), 'continue' => 1, 'time' => date("Y-m-d H:i:s", strtotime("-10 day")), 'order'=>$order), $fields, true, 'isTop desc', "{$offset},5");
        $unitedOrders = array();
        $count = $this->united_order_model->getOrder(array('o.uid' => $id, 'o.status' => array(40, 500, 240), 'continue' => 1, 'time' => date("Y-m-d H:i:s", strtotime("-10 day"))), 'count(o.id) as num');
        $count = array('num'=>0);
        $pages = $this->load->view('v1.1/elements/common/pages', array('pagenum' => ceil($count['num'] / 5), 'ajaxform' => 'user_form'), true);
        $awards = $this->united_order_model->getOrder(array('o.uid' => $id, 'o.status' => 2000, 'time' => date("Y-m-d H:i:s", strtotime("-30 day")), 'o.my_status' => array(1, 3)), '', true, "created desc", "0,5");
        foreach ($awards as $k => $award)
        {
            $award['award'] = '';
            if ($award['orderMargin'] > 0)
            {
                $award['award'] = $this->recordAward($award['united_points']);
            }
            $awards[$k] = $award;
        }
        $this->load->model('united_planner_model');
        $planners = $this->united_planner_model->getPlanners(10);
        $sum = $this->united_planner_model->findByUid($id);
        $param0 = $user['uname'];
        if ($this->is_ajax)
        {
            echo $this->load->view('v1.1/user/index', compact('user', 'unitedOrders', 'awards', 'planners', 'pages', 'oid', 'order'), true);
        }
        else
        {
            $this->display('user/index', compact('user', 'unitedOrders', 'awards', 'planners', 'pages', 'oid', 'sum', 'param0', 'order'), 'v1.1');
        }
    }
    
    /**
     * ajax返回历史数据
     * @return json
     */
    public function getHistoricalData() {
        $lid = intval($this->input->post('lid', true));
        $unitedStatus = $this->input->post('unitedStatus', true);
        $uid = intval($this->input->post('uid', true));
        $page = intval($this->input->get("cpage", true));
        $page = $page <= 1 ? 1 : $page;
        $offset = ($page - 1) * 10;
        $this->load->model('united_order_model');
        if ($unitedStatus == 1) {
            $status = array(1000, 2000);
        } else {
            $status = 2000;
        }
        if ($lid == 0) {
            $where = array('o.uid' => $uid, 'o.status' => $status);
        } elseif ($lid == 19) {
            $where = array('o.uid' => $uid, 'o.lid' => array(19, 11), 'o.status' => $status);
        } elseif ($lid == 33) {
            $where = array('o.uid' => $uid, 'o.lid' => array(33, 35), 'o.status' => $status);
        } else {
            $where = array('o.uid' => $uid, 'o.lid' => $lid, 'o.status' => $status);
        }
        if ($unitedStatus != 1)
        {
            $where['o.my_status'] = array(1, 3);
        }
        $unitedOrders = $this->united_order_model->getOrder($where, '', true, "created desc", "{$offset},10");
        $count = $this->united_order_model->getOrder($where, 'count(o.id) as num');
        $pages = $this->load->view('v1.1/elements/common/pages1', array('spagenum' => ceil($count['num'] / 10), 'ajaxform' => 'history'), true);
        $weekarray = array("日", "一", "二", "三", "四", "五", "六");
        foreach ($unitedOrders as $k=>$unitedOrder)
        {
            $unitedOrder['created'] = date("m-d H:i", strtotime($unitedOrder['created'])) . "(星期" . $weekarray[date("w", strtotime($unitedOrder['created']))] . ")";
            $unitedOrder['lid'] = BetCnName::getCnName($unitedOrder['lid']) . '-' . $unitedOrder['issue'];
            $unitedOrder['returnRate'] = ($unitedOrder['orderBonus'] == 0) ? '' : number_format($unitedOrder['orderBonus'] / $unitedOrder['money'], 2);
            $unitedOrder['money'] = $unitedOrder['money'] / 100;
            $unitedOrder['orderBonus'] = number_format($unitedOrder['orderBonus'] / 100, 2);
            $unitedOrder['orderMargin'] = number_format($unitedOrder['orderMargin'] / 100, 2);
            $unitedOrder['award'] = '';
            if ($unitedOrder['orderMargin'] > 0)
            {
                $unitedOrder['award'] = $this->recordAward($unitedOrder['united_points']);
            }
            $unitedOrders[$k] = $unitedOrder;
        }
        echo $this->load->view('v1.1/user/history', compact('unitedOrders', 'pages'), true);
    }

    /**
     * 返回战绩奖励图片显示
     * @param int $orderBonus
     * @return string
     */
    private function recordAward($orderBonus) {
        $award = 0;
        $record = '';
        foreach (array(1000, 100, 10, 1) as $j => $money)
        {
            if ($orderBonus >= $money)
            {
                $award = 1;
                break;
            }
        }
        if ($award > 0)
        {
            $record = "<i class='lv{$award}'>" . $this->pic[$j] . "<sub>{$award}</sub></i>";
        }
        return $record;
    }
    
    
    public function getCount()
    {
        $lid = $this->input->post('lid', true);
        $uid = $this->input->post('uid', true);
        if (!$lid)
        {
            $lid = 0;
        }
        if ($lid == 19)
        {
            $lid = array(19, 11);
        }
        if ($lid == 33)
        {
            $lid = array(33, 35);
        }
        $this->load->model('united_planner_model');
        $count = $this->united_planner_model->findByUid($uid, '', $lid);
        if (empty($count) || !$count['winningTimes'])
        {
            $count['bonus'] = 0;
            $count['winningTimes'] = 0;
            $count['united_points'] = 0;
        }else {
        	$count['united_points'] = calGrade($count['united_points'], 5, '');
        }
        $count['bonus'] = number_format($count['bonus'] / 100, 2);
        $allLid = array(0 => '全部彩种', '51' => '双色球', '23529' => '大乐透', '42' => '竞彩足球', '43' => '竞彩篮球', '19' => '胜负/任九', '52' => '福彩3D', '23528' => '七乐彩', '10022' => '七星彩', '33' => '排列三/五');
        $count['lidName'] = $allLid[$this->input->post('lid', true)];
        echo json_encode($count);
    }

    /**
     * 更新个人简介
     */
    public function updateIntroduction()
    {
        if ($this->uid)
        {
        	$txt = $this->input->post('txt', true);
            $oldintroduction = $txt;
            if(mb_strlen($oldintroduction)<10 || mb_strlen($oldintroduction)>80)
            {
                die(json_encode(array('status' => false)));
            }
            $introduction = htmlspecialchars($txt, ENT_QUOTES);
            $this->load->model('user_model');
            $this->user_model->updateIntroduction($this->uid, $introduction);
        }
        echo json_encode(array('status' => true));
    }
    
    public function getGendanData()
    {
        $uid = $this->input->post('uid', true);
        $gendanAr = $this->input->post("gendan", true) ? $this->input->post("gendan", true) : '20';
        $this->load->model('united_planner_model');
        $gendanlists = $this->united_planner_model->getUserGendan($uid, $gendanAr);
        foreach ($gendanlists as $k => $gendanlist)
        {
            $gendanlists[$k]['award'] = '';
            if ($gendanlist['united_points'] > 0) {
                $gendanlists[$k]['award'] = $this->calGrade($gendanlist['united_points'], 6, '');
            }
        }
        $hasgendan = array();
        if ($this->uid)
        {
            $this->load->model('follow_order_model');
            $allGendans = $this->follow_order_model->getHasGendan($this->uid);
            foreach ($allGendans as $gendan) {
                $hasgendan[] = $gendan['puid'] . ',' . $gendan['lid'];
            }
            foreach ($allGendans as $gendan) {
                if ($gendan['lid'] == 11) {
                    if (!in_array($gendan['puid'] . ',11', $hasgendan) || !in_array($gendan['puid'] . ',19', $hasgendan)) {
                        $key = array_search($gendan['puid'] . ',11', $hasgendan);
                        if ($key || $key===0) array_splice($hasgendan, $key, 1);                   
                    }
                }
                if ($gendan['lid'] == 33) {
                    if (!in_array($gendan['puid'] . ',33', $hasgendan) || !in_array($gendan['puid'] . ',35', $hasgendan)) {
                        $key = array_search($gendan['puid'] . ',33', $hasgendan);
                        if($key || $key===0)array_splice($hasgendan, $key, 1);
                    }
                }
            }
        }
        $showBind = FALSE;
        if (!empty($this->uinfo) && !$this->isBindForRecharge()) {
            $showBind = true;
        }
        echo $this->load->view('v1.1/user/gendanlist', compact('gendanlists', 'uid', 'gendanAr', 'hasgendan', 'showBind'), true);
    }
    
    
    private function calGrade($points, $jiequ = 0, $num = 2)
    {
            $picArr = array('hg', 'ty', 'yl', 'xx');
            $grade = "";
            $huangguan = floor($points / 1000);
            $l = 0;
            if ($huangguan > 0)
            {
                    for ($n = $huangguan; $n > 10; $n = $n - 10)
                    {
                            if ($jiequ && $jiequ <= $l) return $grade.'<s style="font-size:18px;margin-left：2px;">···</s>';
                            $l++;
                            $grade.="<i class='lv10'><img src='/caipiaoimg/v1.1/img/".$picArr[0].$num.".gif'><sub>10</sub></i>";
                    }

                    if ($jiequ && $jiequ <= $l) return $grade.'<s style="font-size:18px;margin-left：2px;">···</s>';
                    $l++;
                    $grade.="<i class='lv{$n}'><img src='/caipiaoimg/v1.1/img/".$picArr[0].$num.".gif'><sub>{$n}</sub></i>";
            }
            $taiyang = floor(($points - $huangguan * 1000) / 100);
            if ($taiyang > 0)
            {
                    if ($jiequ && $jiequ <= $l) return $grade.'<s style="font-size:18px;margin-left：2px;">···</s>';
                    $l++;
                    $grade.="<i class='lv{$taiyang}'><img src='/caipiaoimg/v1.1/img/".$picArr[1].$num.".gif'><sub>{$taiyang}</sub></i>";
            }
            $yueliang = floor(($points - $huangguan * 1000 - $taiyang * 100) / 10);
            if ($yueliang > 0)
            {
                    if ($jiequ && $jiequ <= $l) return $grade.'<s style="font-size:18px;margin-left：2px;">···</s>';
                    $l++;
                    $grade.="<i class='lv{$yueliang}'><img src='/caipiaoimg/v1.1/img/".$picArr[2].$num.".gif'><sub>{$yueliang}</sub></i>";
            }
            $xingxing = $points - $huangguan * 1000 - $taiyang * 100 - $yueliang * 10;
            if ($xingxing > 0)
            {
                    if ($jiequ && $jiequ <= $l) return $grade.'<s style="font-size:18px;margin-left：2px;">···</s>';
                    $l++;
                    $grade.="<i class='lv{$xingxing}'><img src='/caipiaoimg/v1.1/img/".$picArr[3].$num.".gif'><sub>{$xingxing}</sub></i>";
            }
            return $grade;
    }
    
    public function gendanlist()
    {
        $uid = $this->input->post('uid', true);
        $lid = $this->input->post('lid', true);
        $page = intval($this->input->get("cpage", true));
        $page = $page <= 1 ? 1 : $page;
        $offset = ($page - 1) * 10;
        $this->load->model('follow_order_model');
        if($uid && $lid){
            $users = $this->follow_order_model->gendanList($uid, $lid, $offset, 10);
            $pages = $this->load->view('v1.1/elements/common/pages2', array('spagenum' => ceil($users[1]['count'] / 10), 'ajaxform' => 'listgendan'), true);
            echo $this->load->view('v1.1/elements/pop/gendanlist', array('users' => $users[0], 'pages' => $pages, 'lid' => $lid, 'uid' => $uid), true);
        }
    }
}
