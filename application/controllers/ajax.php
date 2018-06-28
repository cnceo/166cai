<?php
if ( ! defined('BASEPATH')) {
    exit('No direct script access allowed');
}
require_once APPPATH . '/core/CommonController.php';

class ajax extends CommonController
{
    
    private $lids = array(
            '11'    => 'sfc',
            '19'    => 'rj',
            '33'    => 'pls',
            '35'    => 'plw',
            '42'    => 'jczq',
            '43'    => 'jclq',
            '51'    => 'ssq',
            '52'    => 'fcsd',
            '10022' => 'qxc',
            '21406' => 'syxw',
            '23528' => 'qlc',
            '23529' => 'dlt',
            '53'    => 'ks',
            '21407' => 'jxsyxw',
            '21408' => 'hbsyxw',
            '54'    => 'klpk',
            '56'    => 'jlks',
            '57'    => 'jxks',
            '21421' => 'gdsyxw',
        );
    public function __construct()
    {
        parent::__construct();
        $this->load->driver('cache', array('adapter' => 'redis'));
        $this->REDIS = $this->config->item('REDIS');
    }

    public function get()
    {
        $data = $this->input->get();
        $url = $data['url'];
        if (strpos($url, $this->config->item('pages_url')) === FALSE) 
        {
            die('访问错误');
        }
        unset($data['url']);
        if ( ! empty($data['isToken'])) 
        {
            unset($data['isToken']);
            $data['token'] = $this->token;
        }
        if (ENVIRONMENT === 'checkout') 
        {
            $url = str_replace($this->config->item('pages_url'), $this->config->item('cp_host'), $url);
        }
        $response = $this->tools->get($url, $data);
        header('Content-type: application/json');
        echo json_encode($response);
    }

    public function post()
    {
        $this->recordChannel();
        $orderData = array();
        $data = $this->input->post(NULL, TRUE);
        //单式第一次验证处理
        if(isset($data['upload_no']) && !empty($data['upload_no']))
        {
            //验证票数限制
            $limitRes = $this->getLimitByLidRes($data['lid'],$data['orderType'],$data['betTnum']);
            if(!$limitRes['code'])
            {
                $response = array(
                    'code' => 998,
                    'msg'  => $limitRes['msg'],
                    'data' => array('singleFlag'=>1),
                );
                header('Content-type: application/json');
                echo json_encode($response);die;
            }
         }
        //添加必要保存字段
        $this->saveParams($orderData, $data);
        $check = TRUE;
        //是否登录监测
        if (empty($this->uid)) 
        {
            $check = FALSE;
            $response = array(
                'code' => 9,
                'msg'  => '您的登录已超时，请重新登录！',
                'data' => array(),
            );
        }
        
        if(empty($this->uinfo['real_name']))
        {
            $check = FALSE;
            $response = array(
                'code' => 9,
                'msg'  => '您尚未进行实名认证，请刷新页面后重试。',
                'data' => array(),
            );
        }

        // 账户是否注销
        if(isset($this->uinfo['userStatus']) && in_array($this->uinfo['userStatus'], array(1, 2)))
        {
            $check = FALSE;
            if($this->uinfo['userStatus'] == '1')
            {
                $response = array(
                    'code' => 3000,
                    'msg'  => '您的登录已超时，请重新登录！',
                    'data' => array(),
                );
            }
            else
            {
                $response = array(
                    'code' => 16,
                    'msg'  => '您的账户已被冻结，如需解冻请联系客服。',
                    'data' => array(),
                );
            }
        }

        $this->load->model('wallet_model');
        //将一些不必要的字段去掉
        $data = $this->wallet_model->_padBusiParams($data, $this->uid);

        //设置订单数据
        $orderData = array_merge($orderData, $data);
        $orderData['userName'] = $this->uname;
        if ($check) 
        {
            if ($orderData['ctype'] == 'create') 
            {
                $orderData['channel'] = $this->getChannelId();
                //对单式处理
                if ($orderData['upload_no']) $this->dsscParams($orderData, $data);
                switch ($orderData['orderType']) {
                    case '1':
                        $detail = explode(';', $orderData['chases']);
                        foreach ($detail as $k => $d){
                            if (!empty($d)){
                                $d = explode('|', $d);
                                $detail[$k] = array('issue' => $d[0], 'multi' => $d[1], 'money' => $d[2], 'award_time' => $d[3], 'endTime' => $d[4]);
                            }else{
                                unset($detail[$k]);
                            }
                        }
                        $orderData['app_version'] = '0';
                        $orderData['chaseDetail'] = json_encode($detail);
                        $this->load->model('chase_order_model');
                        $res = $this->chase_order_model->createChaseOrder($orderData);
                        $resdata['orderId'] = $res['data']['chaseId'];
                        $resdata['money'] = number_format($data['money'], 2);
                        break;
                    case '4':
                        if (empty($orderData['type'])) {
                            $this->load->model('united_order_model');
                            $res = $this->united_order_model->createUnitedOrder($orderData);
                            $resdata['orderId'] = $res['data']['orderId'];
                            $resdata['money'] = number_format($data['buyMoney']+$data['guaranteeAmount'], 2);
                        }
                        break;
                    case '0':
                    default:
                        $this->load->model('neworder_model');
                        $res = $this->neworder_model->createOrder($orderData);
                        $resdata['orderId'] = $res['data']['orderId'];
                        $resdata['money'] = number_format($data['money'], 2);
                        if($res['status'])
                        {
                            //购彩红包查询
                            $redpack = $this->neworder_model->getBetRedPack($this->uid, $orderData);
                            if($redpack)
                            {
                                $resdata['redpack'] = $redpack;
                                $resdata['redpackId'] = ($redpack[0]['disable'] == 0) ? $redpack[0]['id'] : 0;
                            }
                        }
                        
                        break;
                }
                
                if ($res['status']) 
                {
                    $money = $this->wallet_model->getMoney($this->uid);
                    if (ParseUnit($orderData['orderType'] == 4 ? ($orderData['buyMoney']+$orderData['guaranteeAmount']) : $orderData['money']) > ((isset($redpack[0]) && ($redpack[0]['disable'] == 0)) ? ($money['money'] + ParseUnit(intval(preg_replace('/,/', '', strval($redpack[0]['money']))))) : $money['money'])) 
                    {
                        $response = array('code' => 12, 'msg'  => '订单支付，余额不足！', 'data' => $resdata);
                    }
                    else 
                    {
                        $response = array('code' => 0, 'msg'  => $res['msg'], 'data' => $resdata);
                    }
                    $response['data']['remain_money'] = number_format(ParseUnit($money['money'], 1), 2);
                } 
                else 
                {
                    $response = array('code' => isset($res['code']) ? $res['code'] : 13, 'msg'  => $res['msg'], 'data' => $resdata);
                }
            }if ($orderData['ctype'] == 'pay') {
				//判断是否是单式并验证票数限制
                $_oid = isset($orderData['orderId'])?$orderData['orderId']:$orderData['chaseId'];
                $inf = $this->getUploadInfo($_oid);
                if($inf)
                {
                    //验证票数限制
                    $limitRes = $this->getLimitByLidRes($inf['lid'],$orderData['orderType'],$inf['betNum']);
                    if(!$limitRes['code'])
                    {
                        $response = array(
                            'code' => 998,
                            'msg'  => $limitRes['msg'],
                            'data' => array('singleFlag'=>1),
                        );
                        header('Content-type: application/json');
                        echo json_encode($response);die;
                    }                   
                }
            	$resdata['money'] = number_format($orderData['money'], 2);
            	switch ($orderData['orderType']) {
            		case 1:
            			$resdata['chaseId'] = $orderData['chaseId'];
            			$this->load->model('chase_wallet_model');
            			$response = $this->chase_wallet_model->payChaseOrder($this->uid, $orderData, ParseUnit($data['money']));
            			 
            			if ( ! empty($response)  && $response['code'] != 400) {
            				$response = array(
            					'code' => $response['code'],
            					'msg'  => ($response['code'] != 200) ? $response['msg'] : '<div class="mod-result result-success"><div class="mod-result-bd"><i class="icon-result"></i><div class="result-txt"><h2 class="result-txt-title">恭喜您，支付成功</h2><p>支付金额：<em class="main-color-s">'.$resdata['money'].'元</em></p><p style="position: relative; top: 10px;" class="fcw">正在送往投注站出票...</p></div></div></div>',
            					'data' => array('orderId' => $orderData['chaseId'])
            				);
            			}
            			if ($response['code'] == 400) {
            				$money = $this->wallet_model->getMoney($this->uid);
            				$response = array('code' => $response['code'], 'msg'  => $response['msg'], 'data' => array('remain_money' => number_format(ParseUnit($money['money'], 1), 2)));
            			}
            			break;
            		case 4:
            			$this->load->model('united_wallet_model');
            			if ($orderData['type'] == 1) {
            				$orderData['buyPlatform'] = 0;
            				$response = $this->united_wallet_model->payBuyOrder($this->uid, $orderData, ParseUnit($data['money']));
            			}else {
            				$response = $this->united_wallet_model->payUnitedOrder($this->uid, $orderData, ParseUnit($data['money']));
            			}
            			
            			if ( ! empty($response)  && $response['code'] != 400) {
            				$response = array(
            					'code' => $response['code'],
            					'msg'  => ($response['code'] != 200) ? $response['msg'] : '<div class="mod-result result-success"><div class="mod-result-bd"><i class="icon-result"></i><div class="result-txt"><h2 class="result-txt-title">恭喜您，'.($orderData['type'] == 1 ? '参与' : '发起').'合买成功</h2><p>支付金额：<em class="main-color-s">'.$resdata['money'].'元</em></p></div></div></div>',
            					'data' => array('orderId' => $orderData['orderId'])
            				);
            			}
            			if ($response['code'] == 400) {
            				$money = $this->wallet_model->getMoney($this->uid);
            				$response = array('code' => $response['code'], 'msg'  => $response['msg'], 'data' => array('remain_money' => number_format(ParseUnit($money['money'], 1), 2)));
            			}
            			break;
                        case 5:
                            $this->load->model('follow_wallet_model');
                            $data['followId'] = $data['orderId'];
                            $response = $this->follow_wallet_model->payForAdvance($data);
                            if($response['code'] == 200){
                                $response['msg'] = '<div class="mod-result result-success"><div class="mod-result-bd"><i class="icon-result"></i><div class="result-txt"><h2 class="result-txt-title">恭喜您，定制跟单成功</h2><p>支付金额：<em class="main-color-s">'.number_format(ParseUnit($data['totalMoney'], 1), 2).'元</em></p></div></div></div>';
                            }
                            break;
            		case 0:
            		default:
            			$resdata['orderId'] = $orderData['orderId'];
            			$response = $this->wallet_model->payOrder($this->uid, array(), $orderData, ParseUnit($data['money']));
            			if ( ! empty($response) && ! in_array($response['code'], array('12', '16'))) {
            				$response = array(
            					'code' => 0,
            					'msg'  => '<div class="mod-result result-success"><div class="mod-result-bd"><i class="icon-result"></i><div class="result-txt"><h2 class="result-txt-title">恭喜您，支付成功</h2><p>支付金额：<em class="main-color-s">'.$resdata['money'].'元</em></p><p style="position: relative; top: 10px;" class="fcw">正在送往投注站出票...</p></div></div></div>',
            					'data' => array('orderId' => $resdata['orderId']),
            				);
            			}
            			if ($response['code'] == '12') {
            				$money = $this->wallet_model->getMoney($this->uid);
            				$response['data']['remain_money'] = number_format(ParseUnit($money['money'], 1), 2);
            			}
            			break;
            	}
            }
        }
        //对单式进行处理
        if($orderData['singleFlag'] && ( isset($response['data']['orderId']) || isset($response['data']['chaseId']) ) )
        {
            $this->updateOrderId($response['data']['orderId'] ? $response['data']['orderId'] :$response['data']['chaseId'] );
            $response['data']['singleFlag'] = 1;
        } 
        header('Content-type: application/json');
        echo json_encode($response);
    }

    private function saveParams(&$orderData, &$postData)
    {
        $params = array('ctype', 'endTime', 'codecc');
        foreach ($params as $param) {
            $orderData[$param] = empty($postData[$param]) ? '' : $postData[$param];
        }
        if (preg_match('/,/', $postData['money'])) {
            $postData['money'] = intval(preg_replace('/,/', '', strval($postData['money'])));
        }
    }
    public function getHistory($enName) {
        $this->load->driver('cache', array('adapter' => 'redis'));
        $REDIS = $this->config->item('REDIS');
        $current = json_decode($this->cache->get($REDIS[strtoupper($enName).'_ISSUE_TZ']), TRUE);
        $miss = json_decode($this->cache->get($REDIS[strtoupper($enName).'_MISS']), true);
        $awardArr = unserialize($this->cache->get ( $REDIS [strtoupper($enName).'_AWARD'] ));
        $history = array_slice ( $awardArr, 0, 10 );
        $hsty = array();
        foreach ( $history as $h ) {
            unset($h['pool']);
            unset($h['sale']);
            if (in_array($enName, array('ks', 'jlks', 'jxks'))) {
                $award =  explode ( ',', $h ['awardNum'] );
                $acount = count(array_unique(array_values($award)));
                $h['he'] = array_sum ($award);
                if ($acount == 1) {
                    $h['type'][] = 0;
                    $h['type'][] = 3;
                }elseif ($acount == 2) {
                    $h['type'][] = 4;
                    $h['type'][] = 3;
                    $h['type'][] = 5;
                }else {
                    $h['type'][] = 1;
                    $h['type'][] = 5;
                }
                if (in_array($h ['awardNum'], array('1,2,3', '2,3,4', '3,4,5', '4,5,6'))) {
                    $h['type'][] = 2;
                }
                $h['kd'] = max($award) - min($award);
            }else {
                $award = explode('|', $h ['awardNum']);
                $awArr = array(explode(',', $award[0]), explode(',', $award[1]));
                sort($awArr[0]);
                $c0 = count(array_unique(array_values($awArr[0])));
                $c1 = count(array_unique(array_values($awArr[1])));
                if ($c1 == 1) {
                    $h['type'] = '同花';
                }
                if ($c0 == 1) {
                    $h['type'] = '豹子';
                }elseif ($c0 == 2) {
                    $h['type'] = '对子';
                }elseif ((($awArr[0][1] == $awArr[0][0] + 1) && ($awArr[0][2] == $awArr[0][1] + 1)) || implode(',', $awArr[0]) === '01,12,13') {
                    if ($h['type'] === '同花'){
                        $h['type'] = '同花顺';
                    }else {
                        $h['type'] = '顺子';
                    }
                }
                if (empty($h['type'])) {
                    $h['type'] = '散牌';
                }
            }
            $hsty[$h['issue']] = $h;
        }

        ksort ( $hsty );
        if (!array_key_exists($current['nlIssue']['seExpect'], $hsty)) {
            $hsty[$current['nlIssue']['seExpect']] = array('issue' => $current['nlIssue']['seExpect'], 'prev' => 1);
            unset ( $hsty[min(array_keys($hsty))] );
        }
        ksort ( $hsty );
        exit(json_encode(array('miss' => $miss, 'hsty' => $hsty))) ;
    }
    
    public function getKj($enName) {
    	$this->load->driver('cache', array('adapter' => 'redis'));
    	$REDIS = $this->config->item('REDIS');
    	$kj = unserialize($this->cache->get ( $REDIS [strtoupper($enName).'_AWARD'] ));
    	$data = array();
    	foreach ($kj as $kjdt) {
    		if (strpos($kjdt['issue'], date('ymd')) !== false) {
    			if (in_array($enName, array('ks', 'jlks', 'jxks'))) {
    				$iss = substr($kjdt['issue'], -2);
    				$data[$iss]['award'] = $kjdt['awardNum'];
    				$award = explode(',', $kjdt['awardNum']);
    				$acount = count(array_unique($award));
    				$data[$iss]['he'] = array_sum($award);
    				if (in_array($kjdt['awardNum'], array('1,2,3', '2,3,4', '3,4,5', '4,5,6'))) {
    					$data[$iss]['type'] = "<span class='slh'>三连号</span>";
    				}elseif ($acount == 3) {
    					$data[$iss]['type'] = "<span class='sbt'>三不同</span>";
    				}elseif ($acount == 2) {
    					$data[$iss]['type'] = "<span class='eth'>二同号</span>";
    				}else {
    					$data[$iss]['type'] = "<span class='sth'>三同号</span>";
    				}
    			}elseif ($enName === 'klpk') {
    				$iss = substr($kjdt['issue'], -2);
    				$data[$iss]['award'] = $kjdt['awardNum'];
    				$award = explode('|', $kjdt['awardNum']);
    				$awArr = array(explode(',', $award[0]), explode(',', $award[1]));
    				sort($awArr[0]);
    				$c0 = count(array_unique(array_values($awArr[0])));
    				$c1 = count(array_unique(array_values($awArr[1])));
    				if ($c1 == 1) $data[$iss]['type'] = "<span class='th'>同花</span>";
    				if ($c0 == 1) {
    					$data[$iss]['type'] = "<span class='bz'>豹子</span>";
    				}elseif ($c0 == 2) {
    					$data[$iss]['type'] = "<span class='dz'>对子</span>";
    				}elseif ((($awArr[0][1] == $awArr[0][0] + 1) && ($awArr[0][2] == $awArr[0][1] + 1)) || implode(',', $awArr[0]) === '01,12,13') {
    					if ($data[$iss]['type'] === "<span class='th'>同花</span>"){
    						$data[$iss]['type'] = "<span class='ths'>同花顺</span>";
    					}else {
    						$data[$iss]['type'] = "<span class='sz'>顺子</span>";
    					}
    				}
    				if (empty($data[$iss]['type'])) $data[$iss]['type'] = '散牌';
    			}else {
    				$iss = substr($kjdt['issue'], -3);
    				$data[$iss]['award'] = $kjdt['awardNum'];
    				$award = explode(',', $kjdt['awardNum']);
    				$this->load->library('handlenum/HandleCqssc');
    				$data[$iss]['sw'] = $this->handlecqssc->dxds($award[3]);
    				$data[$iss]['gw'] = $this->handlecqssc->dxds($award[4]); 
    				$data[$iss]['xt'] = $this->handlecqssc->xingtai(array($award[2], $award[3], $award[4]));
    			}
    		}
    		unset($kjdt);
    	}
    	exit(json_encode($data));
    }
        
    public function getOrders($ename = 'syxw')
    {
        $ename = $ename ? $ename : 'syxw';
        if ($ename === 'syxw') {
            $lid = SYXW;
        }elseif ($ename === 'jxsyxw') {
            $lid = JXSYXW;
        }elseif ($ename === 'hbsyxw') {
            $lid = HBSYXW;
        }
        elseif ($ename === 'gdsyxw') {
            $lid = GDSYXW;
        }
        elseif ($ename === 'ks') {
            $lid = KS;
        }elseif ($ename === 'jlks') {
            $lid = JLKS;
        }elseif ($ename === 'jxks') {
            $lid = JXKS;
        }elseif ($ename === 'klpk') {
            $lid = KLPK;
        }else {
            $lid = CQSSC;
        }
        $this->load->model('order_model');
        $this->load->driver('cache', array('adapter' => 'redis'));
        $REDIS = $this->config->item('REDIS');
        $odatas = $this->order_model->getNewOrders($this->uid, $lid, 0, 5);
        $lotteryConfig = json_decode($this->cache->get($REDIS['LOTTERY_CONFIG']), true);
        foreach ($odatas as $k => $odata) 
        {
            if ($odata['status'] == 10 && strtotime("-{$lotteryConfig[$odata['lid']]['ahead']} MINUTE", strtotime($odata['endTime'])) > time()) 
            {
                $odatas[$k]['ljzf'] = 1;
            }else 
            {
                $odatas[$k]['ljzf'] = 0;
            }
        }
        exit(json_encode($odatas));
    }

    public function attachOption()
    {
        $REDIS = $this->config->item('REDIS');
        $this->load->driver('cache', array('adapter' => 'redis'));
        $response = $this->cache->redis->get($REDIS['JCZQ_MATCH']);
        $mid = $this->input->post('mid', TRUE);
        $responseAry = json_decode($response, TRUE);
        $match = $responseAry[$mid];
        if(!$match)
        {
            $this->load->model('jcmatch_model');
            $match = $this->jcmatch_model->getMatch($mid, 'odds,result,full_score,m_status');
            $odds = json_decode($match['odds'], true);
            $match['result'] = json_decode($match['result'], true);
            $codes = @unserialize($odds['bqc']);
            $match['bqcSp00'] = $match['m_status']==1?'--':$codes['aa'];
            $match['bqcSp01'] = $match['m_status']==1?'--':$codes['ad'];
            $match['bqcSp03'] = $match['m_status']==1?'--':$codes['ah'];
            $match['bqcSp10'] = $match['m_status']==1?'--':$codes['da'];
            $match['bqcSp11'] = $match['m_status']==1?'--':$codes['dd'];
            $match['bqcSp13'] = $match['m_status']==1?'--':$codes['dh'];
            $match['bqcSp30'] = $match['m_status']==1?'--':$codes['ha'];
            $match['bqcSp31'] = $match['m_status']==1?'--':$codes['hd'];
            $match['bqcSp33'] = $match['m_status']==1?'--':$codes['hh'];
            $match['bqcGd'] = $codes ? 1 : 0;
            $match['bqcFu'] = isset($codes['single']) && $codes['single'] > 0 ? 1 : 0;
            $codes = @unserialize($odds['jqs']);
            $match['jqsSp0'] = $match['m_status']==1?'--':$codes['s0'];
            $match['jqsSp1'] = $match['m_status']==1?'--':$codes['s1'];
            $match['jqsSp2'] = $match['m_status']==1?'--':$codes['s2'];
            $match['jqsSp3'] = $match['m_status']==1?'--':$codes['s3'];
            $match['jqsSp4'] = $match['m_status']==1?'--':$codes['s4'];
            $match['jqsSp5'] = $match['m_status']==1?'--':$codes['s5'];
            $match['jqsSp6'] = $match['m_status']==1?'--':$codes['s6'];
            $match['jqsSp7'] = $match['m_status']==1?'--':$codes['s7'];
            $match['jqsGd'] = $codes ? 1 : 0;
            $match['jqsFu'] = isset($codes['single']) && $codes['single'] > 0 ? 1 : 0;
            $codes = @unserialize($odds['cbf']);
            $match['bfSp00'] = $match['m_status']==1?'--':$codes['0:0'];
            $match['bfSp01'] = $match['m_status']==1?'--':$codes['0:1'];
            $match['bfSp02'] = $match['m_status']==1?'--':$codes['0:2'];
            $match['bfSp03'] = $match['m_status']==1?'--':$codes['0:3'];
            $match['bfSp04'] = $match['m_status']==1?'--':$codes['0:4'];
            $match['bfSp05'] = $match['m_status']==1?'--':$codes['0:5'];
            $match['bfSp10'] = $match['m_status']==1?'--':$codes['1:0'];
            $match['bfSp11'] = $match['m_status']==1?'--':$codes['1:1'];
            $match['bfSp12'] = $match['m_status']==1?'--':$codes['1:2'];
            $match['bfSp13'] = $match['m_status']==1?'--':$codes['1:3'];
            $match['bfSp14'] = $match['m_status']==1?'--':$codes['1:4'];
            $match['bfSp15'] = $match['m_status']==1?'--':$codes['1:5'];
            $match['bfSp20'] = $match['m_status']==1?'--':$codes['2:0'];
            $match['bfSp21'] = $match['m_status']==1?'--':$codes['2:1'];
            $match['bfSp22'] = $match['m_status']==1?'--':$codes['2:2'];
            $match['bfSp23'] = $match['m_status']==1?'--':$codes['2:3'];
            $match['bfSp24'] = $match['m_status']==1?'--':$codes['2:4'];
            $match['bfSp25'] = $match['m_status']==1?'--':$codes['2:5'];
            $match['bfSp30'] = $match['m_status']==1?'--':$codes['3:0'];
            $match['bfSp31'] = $match['m_status']==1?'--':$codes['3:1'];
            $match['bfSp32'] = $match['m_status']==1?'--':$codes['3:2'];
            $match['bfSp33'] = $match['m_status']==1?'--':$codes['3:3'];
            $match['bfSp40'] = $match['m_status']==1?'--':$codes['4:0'];
            $match['bfSp41'] = $match['m_status']==1?'--':$codes['4:1'];
            $match['bfSp42'] = $match['m_status']==1?'--':$codes['4:2'];
            $match['bfSp50'] = $match['m_status']==1?'--':$codes['5:0'];
            $match['bfSp51'] = $match['m_status']==1?'--':$codes['5:1'];
            $match['bfSp52'] = $match['m_status']==1?'--':$codes['5:2'];
            $match['bfSp90'] = $match['m_status']==1?'--':$codes['a_o'];
            $match['bfSp91'] = $match['m_status']==1?'--':$codes['d_o'];
            $match['bfSp93'] = $match['m_status']==1?'--':$codes['h_o'];
            $match['bfGd'] =  $codes ? 1 : 0;
            $match['bfFu'] = isset($codes['single']) && $codes['single'] > 0 ? 1 : 0;
        }
        $playType = $this->input->post('playType', TRUE);
        if (in_array($playType, array('dg', 'cbf'))) {
            $view = 'attach_option_' . $playType;
        }
        else {
            $view = 'attach_option';
        }
        $bqcOptions = array(
            '33' => '胜-胜',
            '31' => '胜-平',
            '30' => '胜-负',
            '13' => '平-胜',
            '11' => '平-平',
            '10' => '平-负',
            '03' => '负-胜',
            '01' => '负-平',
            '00' => '负-负',
        );
        $cbfWinOptions = array(
            '10' => '1:0',
            '20' => '2:0',
            '21' => '2:1',
            '30' => '3:0',
            '31' => '3:1',
            '32' => '3:2',
            '40' => '4:0',
            '41' => '4:1',
            '42' => '4:2',
            '50' => '5:0',
            '51' => '5:1',
            '52' => '5:2',
            '93' => '胜其他',
        );
        $cbfDrawOptions = array(
            '00' => '0:0',
            '11' => '1:1',
            '22' => '2:2',
            '33' => '3:3',
            '91' => '平其他',
        );
        $cbfLoseOptions = array(
            '01' => '0:1',
            '02' => '0:2',
            '12' => '1:2',
            '03' => '0:3',
            '13' => '1:3',
            '23' => '2:3',
            '04' => '0:4',
            '14' => '1:4',
            '24' => '2:4',
            '05' => '0:5',
            '15' => '1:5',
            '25' => '2:5',
            '90' => '负其他',
        );
        $jqsOptions = array(
            '0' => '0',
            '1' => '1',
            '2' => '2',
            '3' => '3',
            '4' => '4',
            '5' => '5',
            '6' => '6',
            '7' => '7+',
        );

        $this->load->view('v1.1/elements/jczq/' . $view, compact('match', 'bqcOptions', 'cbfWinOptions',
            'cbfDrawOptions', 'cbfLoseOptions', 'jqsOptions'));
    }

    public function attachOptionJL()
    {
        $REDIS = $this->config->item('REDIS');
        $this->load->driver('cache', array('adapter' => 'redis'));
        $response = $this->cache->redis->get($REDIS['JCLQ_MATCH']);
        $responseAry = json_decode($response, TRUE);
        $mid = $this->input->post('mid', TRUE);
        $match = $responseAry[$mid];
        if(!$match)
        {
            $this->load->model('jcmatch_model');
            $match = $this->jcmatch_model->getLqMatch($mid, 'odds,result,full_score,m_status');
            $odds = json_decode($match['odds'], true);
            $match['result'] = json_decode($match['result'], true);
            $codes = @unserialize($odds['sfc']);
            $match['sfcHs15'] = $match['m_status']==1?'--':$codes['h_1-5'];
            $match['sfcHs610'] = $match['m_status']==1?'--':$codes['h_6-10'];
            $match['sfcHs1115'] = $match['m_status']==1?'--':$codes['h_11-15'];
            $match['sfcHs1620'] = $match['m_status']==1?'--':$codes['h_16-20'];
            $match['sfcHs2125'] = $match['m_status']==1?'--':$codes['h_21-25'];
            $match['sfcHs26'] = $match['m_status']==1?'--':$codes['h_26+'];
            $match['sfcAs15'] = $match['m_status']==1?'--':$codes['a_1-5'];
            $match['sfcAs610'] = $match['m_status']==1?'--':$codes['a_6-10'];
            $match['sfcAs1115'] = $match['m_status']==1?'--':$codes['a_11-15'];
            $match['sfcAs1620'] = $match['m_status']==1?'--':$codes['a_16-20'];
            $match['sfcAs2125'] = $match['m_status']==1?'--':$codes['a_21-25'];
            $match['sfcAs26'] = $match['m_status']==1?'--':$codes['a_26+'];
            $match['sfcGd'] = $codes ? 1 : 0;
            $match['sfcFu'] = isset($codes['single']) && $codes['single'] > 0 ? 1 : 0;
        }
        $sfcOptions = array(
            '15'   => '1-5',
            '610'  => '6-10',
            '1115' => '11-15',
            '1620' => '16-20',
            '2125' => '21-25',
            '26'   => '26+',
        );

        $this->load->view('v1.1/elements/jclq/attach_option', compact('match', 'sfcOptions'));
    }

    /*
     * 功能：获取智胜赔率
     * 作者：刁寿钧
     * 日期：2016-03-10
     * */
    public function queryReferOdds()
    {
        $lid = $this->input->post('lid', TRUE);
        $cid = $this->input->post('cid', TRUE);
        $issueStr = $this->input->post('issue', TRUE);

        if (empty($lid) || empty($issueStr))
        {
            exit(json_encode(array()));
        }

        if (empty($cid))
        {
            $cid = 0;
        }

        $this->load->model('api_zhisheng_model', 'dataSource');
        $issues = explode(',', $issueStr);
        $matchToOdds = array();
        switch ($lid)
        {
            case JCZQ:
                foreach ($issues as $issue)
                {
                    $content = $this->dataSource->readEuropeOdds(JCZQ, $issue, $cid);
                                        $contentAry = json_decode($content, TRUE);
                                        if(!$content)
                                        {
                                            $this->load->model('jcmatch_model');
                                            $odds = $this->jcmatch_model->getZhisheng($issue,JCZQ);
                                            $contentAry = array();
                                            foreach ($odds as $odd)
                                            {
                                                $zhisheng=  json_decode($odd['zhisheng'],true);
                                                $contentAry[substr($odd['mid'],2)]['odds'] = $zhisheng['odds'][$cid];
                                            }
                                        }
                                        if(!$contentAry)
                                        {
                                            $contentAry=array(0,0,0,0,0,0,0,0);
                                        }
					foreach ($contentAry as $mid => $info)
					{
						$matchToOdds['20' . $mid] = (isset($info['odds']['oh']) && is_numeric($info['odds']['oh']))
							? $info['odds']
							: array('od' => '0.00', 'oa' => '0.00', 'oh' => '0.00',);
					}
				}
				break;
			case JCLQ:
				break;
			case SFC:
				foreach ($issues as $issue)
				{
					$content = $this->dataSource->readEuropeOdds(SFC, $issue, $cid);
					$contentAry = json_decode($content, TRUE);
					for ($mid = 1; $mid <= 14; $mid ++)
					{
						$matchToOdds[$mid] = (isset($contentAry[$mid]) && isset($contentAry[$mid]['odds']) && is_numeric($contentAry[$mid]['odds']['oh']))
							? $contentAry[$mid]['odds']
							: array('od' => '0.00', 'oa' => '0.00', 'oh' => '0.00',);
					}
				}
				break;
			case RJ:
				foreach ($issues as $issue)
				{
					$content = $this->dataSource->readEuropeOdds(SFC, $issue, $cid);
					$contentAry = json_decode($content, TRUE);
					for ($mid = 1; $mid <= 14; $mid ++)
					{
						$matchToOdds[$mid] = (isset($contentAry[$mid]) && isset($contentAry[$mid]['odds']) && is_numeric($contentAry[$mid]['odds']['oh']))
							? $contentAry[$mid]['odds']
							: array('od' => '0.00', 'oa' => '0.00', 'oh' => '0.00',);
					}
				}
				break;
			default:
				break;
		}
        echo json_encode($matchToOdds);
        exit;
    }
        
    //添加二级代理
    public function addRebate()
    {
        $response = array(
            'code' => 1,
            'msg' => '操作失败',
        );
        if(empty($this->uid))
        {
            $response['msg'] = '请先登录';
            $this->ajaxResult($response);
        }
        
        if($this->uinfo['rebates_level'] !== '1')
        {
            $this->ajaxResult($response);
        }
        $this->load->model('rebates_model');
        $count = $this->rebates_model->getRebatesLog($this->uid);
        if($count)
        {
            $response['msg'] = '今日添加下线已达上限';
            $this->ajaxResult($response);
        }
        $uname = $this->input->post("uname", true);
        $phone = $this->input->post("phone", true);
        $uid = $this->rebates_model->checkRebateUser($uname, $phone);
        if ($uid)
        {
            $res = $this->rebates_model->addRebate($uid, $this->uid);
            if($res)
            {
                $response['code'] = 0;
                $response['msg'] = '操作成功';
                $response['id'] = $uid;
                $this->ajaxResult($response);
            }
            else
            {
                $this->ajaxResult($response);
            }
        }
        else
        {
            $response['msg'] = '用户名或手机号错误';
            $this->ajaxResult($response);
        }
    }
    
    /**
     * 设置用户返点
     */
    public function setRebateOdd()
    {
        if(empty($this->uid))
        {
            $response['code'] = 1;
            $response['msg'] = '请先登录';
            $this->ajaxResult($response);
        }
        $vdata = $this->input->post(null, true);
        $id = $vdata['id'];
        unset($vdata['id']);
        $this->load->config('rebates');
        $oddType = $this->config->item('rebate_odds_type');
        $error = false;
        foreach ($vdata as $lid => $val)
        {
            if(!isset($oddType[$val]))
            {
                $error = true;
                break;
            }
        }
        if($error)
        {
            $response['code'] = 1;
            $response['msg'] = '注意：比例格式不符合要求！';
            $this->ajaxResult($response);
        }
         
        $this->load->model('rebates_model');
        $rebate = $this->rebates_model->getRebatesOdd($id, $this->uid); 
        if($rebate)
        {
            arsort($vdata); //对比例进行排序操作
            //判断设置值是否大于上级比例
            $upOdd = json_decode($rebate['upOdds'], true);
            $betType = $this->config->item('rebate_bet');
            $error = false;
            $errData = array();
            $oddStr = '';
            foreach ($vdata as $lid => $val)
            {
                $upLevel = isset($upOdd[$lid]) ? $upOdd[$lid] : 0;
                if($val > $upLevel)
                {
                    $error = true;
                    array_push($errData, $lid);
                }
                $oddStr .= $betType[$lid] . ":<b>" . $val . "%</b>；";
            }
            if($error)
            {
                $response['code'] = 1;
                $response['msg'] = '注意：下级返点比例不能高于上级！';
                $response['data'] = $errData;
                $this->ajaxResult($response);
            }
            $rebates_level = (checkRebateOdds($vdata) || $rebate['total_income'] > 0) ? 2 : 3;
            $rebate_odds = json_encode($vdata);
            $res = $this->rebates_model->updateRebateOdd($id, $rebate_odds, $rebates_level);
            if ($res)
            {
                $response['code'] = 0;
                $response['msg'] = '操作成功';
                $response['data']['id'] = $id;
                $response['data']['oddStr'] = mb_substr($oddStr, 0, -1);
                $this->ajaxResult($response);
            }
        }
        
        $response['code'] = 1;
        $response['msg'] = '操作失败';
        $this->ajaxResult($response);
    }
        
    /**
    * 打印json数据，并终止程序
    * @param array $result
    */
    private function ajaxResult($result)
    {
        header('Content-type: application/json');
        die(json_encode($result));
    }
    
    public function getTime()
    {
        echo time();
        exit();
    }
    
    public function getEuroSchdule($group = null)
    {
        $weekArr = array('日', '一', '二', '三', '四', '五', '六');
        $this->load->driver('cache', array('adapter' => 'redis'));
        $REDIS = $this->config->item('REDIS');
        $lotteryConfig = json_decode($this->cache->get($REDIS['LOTTERY_CONFIG']), true);
        $this->load->model('gjc_model');
        $res = $this->gjc_model->getScheduleByGroup($group);
        foreach ($res as $val) {
            $date = date('m.d', strtotime($val['begin_time']));
            if ($date >= date('m.d') && empty($result['today'])) {
                $result['today'] = $date;
            }
            $val['weekday'] = "星期".$weekArr[date('w', strtotime($val['begin_time']))];
            $val['time'] = date('H:i', strtotime($val['begin_time']));
            $val['jiezhi'] = 0;
            if (time() > (strtotime($val['begin_time']) - $lotteryConfig[JCZQ]['ahead'] * 60)) {
                $val['jiezhi'] = 1;
            }
            if (in_array($val['id'], array(50, 51))) {
                $tmp = $val['away'];
                $tid = $val['aid'];
                $val['away'] = $val['home'];
                $val['aid'] = $val['hid'];
                $val['home'] = $tmp;
                $val['hid'] = $tid;
            }
            if ($val['id'] == 51) {
                $val['full_score'] = '0:1';
            }
            $result['date'][$date][] = $val;
        }
        echo json_encode($result);exit();
    }
    
    //检查充值是否异步回调
    public function checkPay()
    {
        $trade_no = $this->input->post('trade_no', true);
        $response = array(
            'code' => 1,
            'msg' => '操作失败',
        );
        if(empty($this->uid) || empty($trade_no))
        {
            $this->ajaxResult($response);
        }
        $this->load->model('pay_model');
        $result = $this->pay_model->getPayLog($trade_no, 0);
        if($result)
        {
            if(!in_array($result['pay_type'], array('5', '9', '11','17', '21','24', '25', '28', '35','36')))
            {
                $this->ajaxResult($response);
            }
            if($result['status'] == '1')
            {
                $this->pay_model->updatePayLog($trade_no, array('sync_flag' => '1'));
                $response['code'] = 0;
                $response['msg'] = '操作成功';
                $this->ajaxResult($response);
            }
            else
            {
                $response['code'] = 2;
                $response['msg'] = '操作成功';
                $this->ajaxResult($response);
            }
        }
        
        $this->ajaxResult($response);
    }
    
    public function topBar()
    {
        if (!empty($this->cookie)) {
            echo $this->ajaxDisplay('elements/common/header_topbar', array(), 'v1.1');
        }
    }
    
    public function clickCount() {
        $param = $this->input->post('param');
        $REDIS = $this->config->item('REDIS');
        $this->load->driver('cache', array('adapter' => 'redis'));
        $res = unserialize($this->cache->get($REDIS["CLICK_COUNT"]));
        $res[$param]++;
        $this->cache->save($REDIS["CLICK_COUNT"], serialize($res), 0);
    }

    ##单式逻辑
    /**
     * [dsscParams 单式上传参数组合]
     * @author LiKangJian 2017-09-30
     * @param  [type] &$orderData [description]
     * @param  [type] &$postData  [description]
     * @return [type]             [description]
     */
    private function dsscParams(&$orderData, &$postData)
    {
        $this->load->model('tmp_code_model');
        $codes = $this->tmp_code_model->getCode($postData['upload_no']);
        $orderData['codes'] = $codes;
        //对大乐透单独处理
        if($postData['lid'] == DLT && $postData['isChase'] == 1) $orderData['codes'] = str_replace(':1:1', ':2:1',$orderData['codes']);
        $this->upload_no = $postData['upload_no'];
        unset($postData['upload_no']);
    }
    /**
     * [uploadFile 上传文件]
     * @author LiKangJian 2017-07-25
     * @return [type] [description]
     */
    public function uploadFile()
    {
        
        $post = $this->input->post(null,true);
        $res = array('code'=>0,'msg'=>'文件格式有误，请修改后重新上传','data'=>array());
        $lid = $post['lid'];
        $playType = isset($post['playType']) && !empty($post['playType']) ? $post['playType'] : '1';
        $file =  $this->uploadFileProcess();
        $file_ext = pathinfo($file);
        $file_ext = strtoupper($file_ext['extension']);
        //文件类型
        if($file_ext!='TXT')
        {
            $res['msg'] = '请先选择文件（以.txt 结尾的文本文件）';
            return $this->ajaxResult($res);
        }
        $filesize = abs(filesize($file));
        if($filesize/(1024*256) > 1)
        {
            $res['msg'] = '上传文件超过最大256KB';
            return $this->ajaxResult($res);

        }
        //正则内容
        preg_match_all("/.*\\n/", trim(file_get_contents($file))."\n", $content);
        //删除文件
        @unlink($file);
        $libery = ucfirst($this->lids[$lid]);
        require_once APPPATH . '/libraries/single/' . $libery . '.php';
        $lib = new $libery();
        $checkRes = $lib->check($content[0],$playType);
        if($checkRes['code'])
        {
            $tag = $this->writeCodes($checkRes['data']['codes'],$lid,$post['endTime']);
            if(!$tag) return $this->ajaxResult($res);
            //验证票数限制
            $limitRes = $this->getLimitByLidRes($lid,0,$checkRes['data']['betTnum']);
            if(!$limitRes['code'])
            {
                $res['msg'] = $limitRes['msg'];
                return $this->ajaxResult($res);
            }
            $checkRes['data']['upload_no'] = $tag;
            unset($checkRes['data']['codes']);
            return $this->ajaxResult($checkRes);
        }
        return $this->ajaxResult($res);

    }
    /**
     * [uploadFile 文件上传]
     * @author JackLee 2017-03-24
     * @return [type] [description]
     */
    private function uploadFileProcess()
    {
        $config['upload_path'] = dirname(BASEPATH).'/uploads/single/';
        if(!is_dir($config['upload_path'] )){mkdir($config['upload_path'],0777,true);}
        $config['allowed_types'] = 'txt';
        $config['max_size'] = '256';
        $this->load->library('upload', $config);
        $this->upload->do_upload('file');
        $data =  $this->upload->data();
        return $data['full_path'];
    }
    /**
     * [getUploadInfo 上传文件信息]
     * @author LiKangJian 2017-08-08
     * @param  [type] $orderId [description]
     * @return [type]          [description]
     */
    private function getUploadInfo($orderId)
    {
        $this->load->model('tmp_code_model');
        $res = $this->tmp_code_model->getCodeByOrderId($orderId);
        if(empty($res)) return false;
        $betNum = explode(';', $res['codes']);
        $res['betNum'] = count($betNum);
        unset($res['codes']);
        return $res;
    }
    /**
     * [writeCodes 写入临时表]
     * @author LiKangJian 2017-07-26
     * @param  [type] $codes [description]
     * @return [type]        [description]
     */
    private function writeCodes($codes,$lid,$endTime)
    {
        $this->load->model('tmp_code_model');
        $tmp_id = $this->tmp_code_model->writeCodes($codes,$lid,$endTime);
        return $tmp_id;
    }
    /**
     * [updateOrderId 更新]
     * @author LiKangJian 2017-08-08
     * @param  [type] $orderId [description]
     * @return [type]          [description]
     */
    private function updateOrderId($orderId)
    {
        $this->load->model('tmp_code_model');
        return $codes = $this->tmp_code_model->updateOrderId($orderId,$this->upload_no);
    }
    /**
     * [getEndTime 获取合买截止或者截止时间]
     * @author LiKangJian 2017-09-30
     * @param  [type] $enName [description]
     * @return [type]         [description]
     */
    public function getEndTime($enName)
    {
        $current = json_decode($this->cache->get($this->REDIS[strtoupper($enName === 'fcsd' ? 'fc3d' : $enName) . '_ISSUE']), TRUE);
        //合买提前截止时间
        $this->load->model('lottery_model');
        $lotteryConfig = $this->lottery_model->getLotteryConfig(array_search($enName, $this->lids), 'united_ahead,ahead');
        $res = array(
                'seFsendtime' => $current['cIssue']['seFsendtime'],
                'hmendTime'   => $current['cIssue']['seFsendtime']/1000 - $lotteryConfig['united_ahead'] * 60,
                );
        return $res;
    }
    /**
     * [getLimitByLidRes 计算出票限制]
     * @author LiKangJian 2017-07-28
     * @param  [type] $lid     [description]
     * @param  [type] $endTime [description]
     * @return [type]          [description]
     */
    private function getLimitByLidRes($lid,$orderType,$betTnum)
    {
        $res = array('code'=>1,'msg'=>'');
        date_default_timezone_set('Asia/Shanghai');
        $endTime = $this->getEndTime($this->lids[$lid]);
        if($orderType==4)
        {
            $endTime = $endTime['hmendTime'] ;
        }else{
            $endTime = $endTime['seFsendtime'] /1000 ;
          
        }
        $minDiff = ($endTime - time())/60;
        if($minDiff > 45 ) return $res;
        $configItem = json_decode($this->cache->get($this->REDIS['LOTTERY_CONFIG']), true);
        $configItem  = json_decode($configItem[$lid]['order_limit'], true);
        $lidArray = array(FCSD, SFC, RJ);
        //① y=对应时间后台配置票数，福彩3D、任选九、胜负彩；
        //② y=对应时间后台配置票数*5，适用于双色球、大乐透、七星彩、七乐彩、排列5、排列3；
        if($minDiff <= 5 && $betTnum > $y = in_array($lid,$lidArray) ? $configItem[0]['value'] : $configItem[0]['value'] *5)
        {
            return $res = array('code'=>0,'msg'=>'离截止不到5分钟，为及时出票请确保方案不超过'.$y.'注！');
        }

        if($minDiff > 5 && $minDiff <= 15 && $betTnum > $y = in_array($lid,$lidArray) ? $configItem[1]['value'] : $configItem[1]['value'] *5)
        {
            return $res = array('code'=>0,'msg'=>'离截止不到15分钟，为及时出票请确保方案不超过'.$y.'注！');
        }
        if($minDiff > 15 && $minDiff <= 45 && $betTnum > $y = in_array($lid,$lidArray) ? $configItem[2]['value'] : $configItem[2]['value'] *5)
        {
            return $res = array('code'=>0,'msg'=>'离截止不到45分钟，为及时出票请确保方案不超过'.$y.'注！');
        }
        return $res;
    }
}
