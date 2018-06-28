<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * 竞猜活动接口  app和iOS公用地址
 * @author Administrator
 *
 */
class Jchd extends MY_Controller
{ 
	public function __construct()
	{
		parent::__construct();
		$this->load->model('user_model');
		$this->load->model('api_jchd_model','api_model');
	}

	/**
	 * 活动入口页接口
	 */
	public function index()
    {
 	    //$this->uid = 210;
	    $uinfo = array();
        if ($this->uid) {
            $uinfo = $this->user_model->getUserInfo($this->uid);
        }
        
        $currentMatch = $this->api_model->getCurrentMatchs();
        $data = array(
            'status' => "0", //状态 未开始
            'start_time' => "",
            'matachs' => array(),
        );
        if($currentMatch) {
            $data['id'] = $currentMatch['id'];
            $data['theme_id'] = $currentMatch['theme_id'];
            $data['issue'] = $currentMatch['issue'];
            $data['start_time'] = $currentMatch['start_time'];
            $data['end_time'] = $currentMatch['end_time'];
            $data['money'] = ParseUnit($currentMatch['money'] * 10, 1);
            $data['timestamp'] = time();
            $order = array();
            if($uinfo) {
                $order = $this->api_model->getJoinOrder($uinfo['uid'], $currentMatch['theme_id'], $currentMatch['issue']);
                if($order) {
                    //状态 已预测
                    $data['status'] = "2";
                }
            }
            //状态  可点击
            if ($data['status'] == '0' && (time() >= strtotime($data['start_time'])) && (time() < strtotime($data['end_time']))) {
                $data['status'] = "1";
            }
            //状态 已截止
            if ($data['status'] == '0' && (time() >= strtotime($data['end_time']))) {
                $data['status'] = "3";
            }
            $matchs = json_decode($currentMatch['plan'], true);
            foreach ($matchs as $key => $value) {
                $code = 'code' . $key;
                if(isset($order[$code])) {
                    $value['code'] = $order[$code];
                } else {
                    $value['code'] = '';
                }
                $data['matachs'][] = $this->formtMatch($value);
            }
        }

        //登录用户组织弹窗数据
        $popInfo = (object)array();
        if($uinfo) {
            $popData = $this->api_model->getPopInfo($uinfo['uid'], $data['theme_id'], $data['issue']);
            if($popData) {
                $matchs = json_decode($popData['plan'], true);
                $popInfo = array(
                    'uid' => $popData['uid'],
                    'orderId' => $popData['orderId'],
                    'defeat_num' => $popData['defeat_num'] * 10,
                    'defeat_ratio' => $popData['defeat_ratio'] . '%',
                    'money' => ParseUnit($popData['money'] * 10, 1),
                    'bouns' => number_format(ParseUnit($popData['bouns'], 1), 2),
                    'show_status' => $popData['show_status'],
                    'headimgurl' => $popData['headimgurl'] ? $popData['headimgurl'] : $this->config->item('pages_url') . 'caipiaoimg/static/images/comment-face.png',
                    'lack_num' => count($matchs) - $popData['win_num'],
                );
                
                foreach ($matchs as $key => $value) {
                    $value['code'] = $popData['code' . $key];
                    $popInfo['matachs'][] = $this->formtMatch($value);
                }
            }
        }
        
        $result = array (
            'status' => '200',
            'msg' => "请求成功",
            'data' => array(
                'currentMatch' => $data,
                'popInfo' => $popInfo,
                'uid' => isset($uinfo['uid']) ? $uinfo['uid'] : '',
            ),
        );
        
        $this->ajaxResult($result);
    }
    
    /**
     * 提交竞猜结果
     */
    public function post() {
        //$this->uid = 210;
        if (empty($this->uid)) {
            $result = array(
                'status' => '100',
                'msg'  => '您的登录已超时，请重新登录！',
                'data' => array(),
            );
            
            $this->ajaxResult($result);
        }
        
        $data = $this->input->post(null, true);
        $theme_id = intval($data['theme_id']);
        $issue = intval($data['issue']);
        $uinfo = $this->user_model->getUserInfo($this->uid);
        if(empty($theme_id) || empty($issue) || empty($data['code']) || empty($uinfo['uid'])) {
            $result = array(
                'status' => '300',
                'msg'  => '参数错误！',
                'data' => array(),
            );
            
            $this->ajaxResult($result);
        }
        //查询期次信息
        $activity = $this->api_model->getJchdConfig($data['theme_id'], $data['issue']);
        if (empty($activity)) {
            $result = array(
                'status' => '300',
                'msg'  => '参数错误！',
                'data' => array(),
            );
            
            $this->ajaxResult($result);
        }
        $nowTime = date('Y-m-d H:i:s');
        if($nowTime < $activity['start_time']) {
            $result = array(
                'status' => '300',
                'msg'  => '活动还未开始！',
                'data' => array(),
            );
            
            $this->ajaxResult($result);
        }
        if($nowTime >= $activity['end_time']) {
            $result = array(
                'status' => '300',
                'msg'  => '已过预测截止时间，您可刷新后预测下一期或购买竞彩足球！',
                'data' => array(),
            );
            
            $this->ajaxResult($result);
        }
        $codeCheck = $this->checkCode($data['code'], json_decode($activity['plan'], true));
        if(empty($codeCheck['status'])) {
            $result = array(
                'status' => '300',
                'msg'  => '参数错误！',
                'data' => array(),
            );
            
            $this->ajaxResult($result);
        }
        
        $uAgent = $_SERVER['HTTP_USER_AGENT'];
        $platform = (stripos($uAgent, '166cai/ios') == true) ? 2 : 1;
        $inserData = array(
            'uid' => $uinfo['uid'],
            'theme_id' => $activity['theme_id'],
            'issue' => $activity['issue'],
            'orderId' => $this->tools->getIncNum('UNIQUE_KEY'),
            'platform' => $platform,
            'forecast_bouns' => round($codeCheck['codes']['forecast_bouns'], 2) * 100,
        );
        if(isset($codeCheck['codes']['code1'])) {
            $inserData['code1'] = $codeCheck['codes']['code1'];
        }
        if(isset($codeCheck['codes']['code2'])) {
            $inserData['code2'] = $codeCheck['codes']['code2'];
        }
        if(isset($codeCheck['codes']['code3'])) {
            $inserData['code3'] = $codeCheck['codes']['code3'];
        }
        if(isset($codeCheck['codes']['code4'])) {
            $inserData['code4'] = $codeCheck['codes']['code4'];
        }
        $res = $this->api_model->saveOrder($inserData);
        if($res) {
            $result = array(
                'status' => '200',
                'msg'  => '您已成功完成本期预测，购买竞彩足球立即开赚！',
                'data' => array(),
            );
            
            $this->ajaxResult($result);
        } else {
            $result = array(
                'status' => '300',
                'msg'  => '预测失败！',
                'data' => array(),
            );
            
            $this->ajaxResult($result);
        }
    }
    
    /**
     * 检查投注串
     * @param unknown $code
     * @param unknown $plan
     * @return boolean[]|boolean[]|unknown[][]
     */
    private function checkCode($code, $plan) {
        $codes = explode(',', $code);
        $matchs = array();
        $codeInfo = array();
        $returnCode = array();
        foreach ($codes as $match) {
            preg_match('/(\d+)=(\d+)/i', $match, $map);
            if (empty($map['1']) || (!in_array($map['2'], array(3, 1, 0)))) {
                return array('status' => false);
            }
            $matchs[] = $map['1'];
            $codeInfo[$map['1']] = $map['2'];
        }
        $matchs = array_unique($matchs);
        if(count($matchs) != count($plan)) {
            return array('status' => false);
        }
        
        $forecast_bouns = 2;
        foreach ($plan as $key => $value) {
            if(!in_array($value['mid'], $matchs)) {
                return array('status' => false);
            }
            $returnCode['code' . $key] = $codeInfo[$value['mid']];
            $odds = explode(',', $value['sp']);
            if($codeInfo[$value['mid']] == 3) {
                $forecast_bouns *= $odds[0];
            }
            if($codeInfo[$value['mid']] == 1) {
                $forecast_bouns *= $odds[1];
            }
            if($codeInfo[$value['mid']] == 0) {
                $forecast_bouns *= $odds[2];
            }
        }
        $returnCode['forecast_bouns'] = $forecast_bouns;
        
        return array('status' => true, 'codes' => $returnCode);
    }
    
    /**
     * 竞猜记录
     */
    public function orderList() {
        //$this->uid = 210;
        if (empty($this->uid)) {
            $result = array(
                'status' => '100',
                'msg'  => '您的登录已超时，请重新登录！',
                'data' => array(),
            );
            
            $this->ajaxResult($result);
        }
        
        $data = $this->input->post(null, true);
        if (empty($data['theme_id'])) {
            $result = array(
                'status' => '300',
                'msg'  => '参数错误！',
                'data' => array(),
            );
            
            $this->ajaxResult($result);
        }
        
        $orders = $this->api_model->getUserOrders($this->uid, $data['theme_id']);
        $datas = array();
        foreach ($orders as $order) {
            $list = array();
            $list = array(
                'uid' => $order['uid'],
                'theme_id' => $order['theme_id'],
                'issue' => $order['issue'],
                'orderId' => $order['orderId'],
                'start_time' => $order['start_time'],
                'end_time' => $order['end_time'],
                'award_time' => $order['award_time'],
                'status' => $order['status'],
                'forecast_bouns' => number_format(ParseUnit($order['forecast_bouns'], 1), 2),
                'oStatus' => $order['oStatus'],
                'bouns' => ParseUnit($order['bouns'], 1),
            );
            $matchs = json_decode($order['plan'], true);
            foreach ($matchs as $key => $value) {
                $value['code'] = $order['code' . $key];
                $list['matachs'][] = $this->formtMatch($value);
            }
            $datas[] = $list;
        }
        
        $result = array(
            'status' => '200',
            'msg'  => '请求成功！',
            'data' => array(
                'orders' => $datas
            ),
        );
        
        $this->ajaxResult($result);
    }
    
    /**
     * 竞猜大神榜
     */
    public function rankList() {
        //$this->uid = 210;
        if (empty($this->uid)) {
            $result = array(
                'status' => '100',
                'msg'  => '您的登录已超时，请重新登录！',
                'data' => array(),
            );
            
            $this->ajaxResult($result);
        }
        $data = $this->input->post(null, true);
        
        $match = $this->api_model->getJchdConfig($data['theme_id'], $data['issue']);
        $datas = array();
        if ($match) {
            $ranklist = $this->api_model->getRankList($data['theme_id'], $data['issue']);
            if($ranklist) {
                $userRank = $this->api_model->getUserRank($this->uid, $data['theme_id'], $data['issue']);
                $flag = false;
                $plan = json_decode($match['plan'], true);
                foreach ($ranklist as $value) {
                    $list = array(
                        'uid' => $value['uid'],
                        'uname' => $value['uname'], 
                        'match_num' => $value['match_num'],
                        'bouns' => number_format(ParseUnit($value['bouns'], 1), 2),
                        'status' => $match['status'],
                        'rank' => $value['rank'],
                        'orderId' => $value['orderId'],
                        'forecast_bouns' => number_format(ParseUnit($value['forecast_bouns'], 1), 2),
                    );
                    if($value['uid'] == $userRank['uid']) {
                        $list['colorFlag'] = 1;
                        $flag = true;
                    } else {
                        $list['colorFlag'] = 0;
                    }
                    foreach ($plan as $key => $val) {
                        $val['code'] = $value['code' . $key];
                        $list['matachs'][] = $this->formtMatch($val);
                    }
                    
                    $datas[] = $list;
                }
                
                if (!$flag && $userRank) {
                    $user = array(
                        'uid' => $userRank['uid'],
                        'uname' => $this->uname,
                        'match_num' => $userRank['match_num'],
                        'bouns' => number_format(ParseUnit($userRank['bouns'], 1), 2),
                        'status' => $match['status'],
                        'rank' => $userRank['rank'],
                        'colorFlag' => 1,
                        'orderId' => $userRank['orderId'],
                        'forecast_bouns' => number_format(ParseUnit($userRank['forecast_bouns'], 1), 2),
                    );
                    foreach ($plan as $key => $val) {
                        $val['code'] = $userRank['code' . $key];
                        $user['matachs'][] = $this->formtMatch($val);
                    }
                    
                    $datas[] = $user;
                }
            }
        }
        
        $result = array(
            'status' => '200',
            'msg'  => '请求成功！',
            'data' => array(
                'ranks' => $datas
            ),
        );
        
        $this->ajaxResult($result);
    }
    
    /**
     * 中奖状态
     */
    public function orderBouns() {
        //$this->uid = 210;
        if (empty($this->uid)) {
            $result = array(
                'status' => '100',
                'msg'  => '您的登录已超时，请重新登录！',
                'data' => array(),
            );
            
            $this->ajaxResult($result);
        }
        
        $data = $this->input->post(null, true);
//         $data = array(
//             'orderId' => '20180425094402217102',
//         );
        
        $detail = $this->api_model->getOrderDetail($data['orderId']);
        $uinfo = $this->user_model->getUserInfo($this->uid);
        if($detail) {
            $matchs = json_decode($detail['plan'], true);
            $order = array(
                'uid' => $detail['uid'],
                'defeat_num' => $detail['defeat_num'] * 10,
                'defeat_ratio' => $detail['defeat_ratio'] . '%',
                'money' => ParseUnit($detail['money'] * 10, 1),
                'bouns' => number_format(ParseUnit($detail['bouns'], 1), 2),
                'show_status' => ($detail['uid'] != $this->uid) ? '4' : $detail['show_status'],
                'headimgurl' => $uinfo['headimgurl'] ? $uinfo['headimgurl'] : $this->config->item('pages_url') . 'caipiaoimg/static/images/comment-face.png',
                'lack_num' => count($matchs) - $detail['win_num'],
                'bouns_num' => $detail['bouns_num'] * 10,
            );
            
            foreach ($matchs as $key => $value) {
                $value['code'] = $detail['code' . $key];
                $order['matachs'][] = $this->formtMatch($value);
            }
        }
        
        $result = array(
            'status' => '200',
            'msg'  => '请求成功！',
            'data' => array(
                'detail' => $order
            ),
        );
        
        $this->ajaxResult($result);
    }
    
    /**
     * 格式化对阵输出
     * @param unknown $value
     * @return unknown[]
     */
    private function formtMatch($value)
    {
        $data = array(
            'mid' => $value['mid'],
            'home' => $value['home'],
            'away' => $value['away'],
            'playType' => $value['playType'],
            'ratio' => $value['ratio'],
            'score' => $value['score'],
            'code' => $value['code'],
        );
        
        return $data;
    }
    
    /**
     * 打印json数据，并终止程序
     * @param array $result
     */
    private function ajaxResult($result)
    {
        //header('Content-type: application/json');
        die(json_encode($result));
    }
    
}
