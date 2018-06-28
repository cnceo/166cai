<?php
defined('BASEPATH') OR die('No direct script access allowed');

/*
 * APP 用户账户明细、投注记录数据接口
 * @date:2016-01-18
 */
class Mylottery extends MY_Controller
{
	// V1.0【账户明细】订单类型
    private $lotteryType = array(
        0 => 'all',     // 账户明细
        1 => '1',       // 购彩记录
        2 => '0',       // 充值记录
        3 => 'withdraw',// 提款记录
        4 => '2',       // 中奖记录
    );

    // V1.0【投注记录】订单类型
    public $betType = array(
        '1' => '',      // 全部
        '2' => '2000',  // 已中奖
        '3' => '500'    // 待开奖
    );

    public function __construct()
    {
        parent::__construct();
        $this->load->model('wallet_model', 'Wallet');
        $this->load->model('order_model', 'Order');
        $this->load->library('BetCnName');
        $this->versionInfo = $this->getRequestHeaders();
    }

    public function index()
    {
        $result = array(
            'status' => '1',
            'msg' => '通讯成功',
            'data' => $this->getRequestHeaders()
        );
        echo json_encode($result);  
    }

    /*
     * 账户明细列表信息接口
     * @date:2016-01-18
     */
    public function getMyLotteryList()
    {
        $data = $this->strCode($this->input->post('data'));
        $data = json_decode($data, true);

        // 调试
        // $data = array(
        //     'uid' => '17',
        //     'page' => '1',
        //     'number' => '10',
        //     'ctype' => '0'
        // );

        // 参数检查
        if(empty($data['uid']) || empty($data['page']) || empty($data['number']))
        {
            $result = array(
                'status' => '0',
                'msg' => '缺少必要参数',
                'data' => ''
            );
            echo json_encode($result);
            exit();
        }

        $ltype = $this->lotteryType;
        if(isset($ltype[$data['ctype']]))
        {
            $ctype = $ltype[$data['ctype']];
        }
        else
        {
            $result = array(
                'status' => '0',
                'msg' => '缺少必要参数',
                'data' => ''
            );
            echo json_encode($result);
            exit();
        }

        $data['page'] = max(1, $data['page']);
        $cons = array(
            'uid' => $data['uid'],
            'ctype' => $ctype
        );

        $details = $this->Wallet->getTradeDetail($cons, $data['page'], $data['number']);
        $info = $this->getInfoList($details);

        $lotteryInfo = array();
        if(!empty($info['orders']))
        {
            foreach ($info['orders'] as $key => $items) 
            {
                $lotteryInfo[$key]['trade_no'] = $items['trade_no'];
                $lotteryInfo[$key]['ctypeName'] = wallet_ctype($items['ctype'], $items['additions']);
                $lotteryInfo[$key]['lid'] = $items['lid'];
                $lotteryInfo[$key]['color'] = $items['balance'] > 0 ? 'red' : 'green';
                $lotteryInfo[$key]['balance'] = $items['balance'];
                $lotteryInfo[$key]['date'] = date('m-d H:i', strtotime($items['created']));
                $lotteryInfo[$key]['umoney'] = '余额' . number_format(ParseUnit($items['umoney'], 1), 2);
                $lotteryInfo[$key]['detailUrl'] = $items['tradeDetailUrl'];
            }
        }

        $result = array(
            'status' => '1',
            'msg' => '通讯成功',
            'data' => $lotteryInfo
        );
        echo json_encode($result);
    }

    /*
     * 账户明细列表信息格式处理
     * @date:2016-01-18
     */
    private function getInfoList($details)
    {
        // http
        $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https:" : "http:";

        $info['income'] = $details['income'];
        if (empty($details['datas']))
        {
            $info['orders'] = array();
        }
        else
        {
            foreach ($details['datas'] as & $order)
            {
                $balance = $order['income'] - $order['expend'];
                $sign = ($balance > 0) ? '+' : '';
                $order['balance'] = $sign . number_format(ParseUnit($balance, 1), 2);

                if ($order['ctype'] == 1)
                {
                    $token = $this->strCode(json_encode(array(
                        'uid' => $order['uid'],
                    )), 'ENCODE');
                    $order['tradeDetailUrl'] = $protocol . $this->config->item('pages_url') . 'app/order/detail/' . $order['orderId']
                        . '/' . urlencode($token);
                    $order['lid'] = $order['additions'];
                }
                elseif(in_array($order['ctype'], array(12, 13)))
                {
                    $token = $this->strCode(json_encode(array(
                        'uid' => $order['uid'],
                    )), 'ENCODE');
                    $order['tradeDetailUrl'] = $protocol . $this->config->item('pages_url') . 'app/chase/detail/' . $order['orderId']
                        . '/' . urlencode($token);
                    $order['lid'] = $order['additions'];
                }
                else
                {
                    $tradeToken = $this->strCode(json_encode(array(
                        'uid'     => $order['uid'],
                        'tradeNo' => $order['trade_no'],
                    )), 'ENCODE');
                    $order['tradeDetailUrl'] = $protocol . $this->config->item('pages_url') . 'app/trade/detail/' . urlencode($tradeToken);
                    $order['lid'] = '0';
                }
            }
            $info['orders'] = $details['datas'];
        }

        return $info;
    }

    /*
     * 投注记录信息接口
     * @date:2015-07-17
     */
    public function getMyBetList()
    {
        $data = $this->strCode($this->input->post('data'));
        $data = json_decode($data, true);

        // 调试
        // $data = array(
        //     'uid' => '66',
        //     'baseType' => '2',   // 全部
        //     'subType' => '0',    // 全部
        //     'page' => 1,
        //     'number' => 10
        // );
        // $result = $this->getBetV2($data);
        // echo json_encode($result);die;

        $result = $this->getBetV2($data);

        echo json_encode($result);
    }

    /*
     * 投注记录信息接口
     * @version:V1.1
     * @date:2016-03-09
     */
    public function getBetV2($data)
    {
        // 投注筛选类型
        $betType = array(
            '0' => array(
                'orderType' => '-1',
                'status' => array(
                    '0' => '',      // 全部订单 - 全部订单
                    '1' => '2000',  // 全部订单 - 已中奖
                    '2' => '500',   // 全部订单 - 待中奖
                    '3' => '10',    // 全部订单 - 待付款
                )
            ),
            '1' => array(
                'orderType' => '0',
                'status' => array(
                    '0' => '',      // 自购订单 - 全部自购订单
                    '1' => '2000',  // 自购订单 - 已中奖
                    '2' => '500',   // 自购订单 - 待中奖
                	'3' => '10',    // 全部订单 - 待付款
                )
            ),
            '2' => array(
                'orderType' => '1',
                'status' => array(
                    '0' => '',       // 追号订单 - 全部追号订单
                    '1' => 'is_chase',  // 追号订单 - 追号中
                    '2' => 'chase_over',// 追号订单 - 追号完成
                    '3' => 'chase_win'  // 追号订单 - 已中奖
                )
            )
        );

        if( empty($data['uid']) || empty($data['page']) || empty($data['number']) || !isset($betType[$data['baseType']]['status'][$data['subType']]) )
        {
            $result = array(
                'status' => '0',
                'msg' => '缺少必要参数',
                'data' => ''
            );
            return $result;
        }

        $data['page'] = max(1, $data['page']);

        // 追号订单
        if($data['baseType'] == '2')
        {
            // 组装查询条件
            $cons = array(
                'uid' => $data['uid'],
                'status' => $betType[$data['baseType']]['status'][$data['subType']]
            );

            // 彩种筛选
            if(!empty($data['lid']))
            {
                $cons['lid'] = intval($data['lid']);
            }

            // 根据查询条件查询
            $this->load->model('chase_order_model');
            $odatas = $this->chase_order_model->getChases($cons, $data['page'], $data['number']);

            // 获取订单状态
            $orderStatus = $this->chase_order_model->getStatus();

            // 自购订单格式处理
            $orderInfo = $this->getChaseListFormat($data['uid'], $odatas['datas'], $orderStatus);
        }
        else
        {
            // 组装查询条件
            $cons = array(
                'uid' => $data['uid'],
            );
      
            $orderType = $betType[$data['baseType']]['orderType'];
            if($orderType >= 0)
            {
                $cons['orderType'] = $orderType;
            }

            $status = $betType[$data['baseType']]['status'][$data['subType']];
            if($status)
            {
                $cons['status'] = $status;
            }

            // 彩种筛选
            if(!empty($data['lid']))
            {
                $cons['lid'] = intval($data['lid']);
            }

            // 根据查询条件查询
            $odatas = $this->Order->getOrders($cons, $data['page'], $data['number']);
            
            // 自购订单格式处理
            $orderInfo = $this->getBetListFormat($data['uid'], $odatas['datas']);
        }

        $result = array(
            'status' => '1',
            'msg' => '通讯成功',
            'data' => $orderInfo
        );

        return $result;
    }

    /*
     * 投注记录普通投注格式
     * @date:2016-03-09
     */
    public function getBetListFormat($uid, $orders)
    {
        // http
        $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https:" : "http:";

        // 组装数据
        $orderInfo = array();

        if(!empty($orders))
        {
            $this->load->library('BetCnName');
            foreach ($orders as $key => $items) 
            {
                $orderInfo[$key]['orderId'] = $items['orderId'];
                $orderInfo[$key]['lid'] = $items['lid'];
                $orderInfo[$key]['issue'] = ($items['lid'] == BetCnName::JCZQ || $items['lid'] == BetCnName::JCLQ)?'':$items['issue'];
                $orderInfo[$key]['cnName'] = BetCnName::$BetCnName[$items['lid']];
                $orderInfo[$key]['playType'] = ($items['lid'] == BetCnName::PLS || $items['lid'] == BetCnName::FCSD)?BetCnName::$playCnName[$items['lid']][$items['playType']]:null;
                $orderInfo[$key]['money'] = number_format(ParseUnit($items['money'], 1), 2);
                $orderInfo[$key]['date'] = date('m-d H:i', strtotime($items['created']));
                $status = ($items['status'] == 510) ? '等待开奖' : parse_order_status($items['status'], $items['my_status']);
                $orderInfo[$key]['redTag'] = ($items['margin'] > 0 || $status == '待付款')?'1':'0';
                $orderInfo[$key]['statusMsg'] = ($items['margin'] > 0)?'中奖'.number_format(ParseUnit($items['margin'], 1), 2).'元':$status;
                // orderType 1、6 追号
                $orderInfo[$key]['orderType'] = $items['orderType'] ? '1' : '';

                $token = $this->strCode(json_encode(array(
                        'uid' => $uid,
                    )), 'ENCODE');
                $orderInfo[$key]['orderDetailUrl'] = $protocol . $this->config->item('pages_url') . 'app/order/detail/' . $items['orderId'] . '/' . urlencode($token);
            }
        }
        return $orderInfo;
    }
    
    /*
     * 投注记录追号投注格式
     * @date:2016-03-09
     */
    public function getChaseListFormat($uid, $orders, $orderStatus)
    {
        // http
        $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https:" : "http:";

        // 组装数据
        $orderInfo = array();

        if(!empty($orders))
        {
            $this->load->library('BetCnName');
            foreach ($orders as $key => $items) 
            {
                $orderInfo[$key]['chaseId'] = $items['chaseId'];
                $orderInfo[$key]['lid'] = $items['lid'];
                $orderInfo[$key]['cnName'] = BetCnName::$BetCnName[$items['lid']];
                $orderInfo[$key]['money'] = ParseUnit($items['money'], 1) . '元';
                $orderInfo[$key]['date'] = date('m-d H:i', strtotime($items['created']));
                $orderInfo[$key]['progress'] = $this->getChaseProgress($items, $orderStatus);
                $orderInfo[$key]['redTag'] = ($items['bonus'] > 0) ? '1' : '0';
                $orderInfo[$key]['statusMsg'] = $this->getChaseStatus($items, $orderStatus);

                $token = $this->strCode(json_encode(array(
                        'uid' => $uid,
                    )), 'ENCODE');
                $orderInfo[$key]['orderDetailUrl'] = $protocol . $this->config->item('pages_url') . 'app/chase/detail/' . $items['chaseId'] . '/' . urlencode($token);
            }
        }
        return $orderInfo;
    }

    /*
     * 追号大订单进度
     * @date:2016-03-09
     */
    public function getChaseProgress($chaseInfo, $orderStatus)
    {
        $msg = chase_status($chaseInfo['status'], $orderStatus);

        if($chaseInfo['status'] >= $orderStatus['is_chase'])
        {
            $msg .= "(" . $chaseInfo['chaseIssue'] . "/" . $chaseInfo['totalIssue'] . ")";
        }
        return $msg;
    }

    // 获取追号进度
    public function getChaseStatus($items, $orderStatus)
    {
        $status = '';
        if($items['status'] == $orderStatus['is_chase'] && $items['bonus'] == 0)
        {
            $status = '静待大奖';
        }
        elseif($items['status'] >=  $orderStatus['is_chase'])
        {
            if($items['bonus'] > 0)
            {
                $status = '中奖'. ParseUnit($items['bonus'], 1) . '元';
            }
            else
            {
                $status = '未中奖';
            }
        }
        return $status;
    }
}