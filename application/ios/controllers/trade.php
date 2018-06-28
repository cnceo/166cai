<?php
defined('BASEPATH') OR die('No direct script access allowed');

class Trade extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('wallet_model', 'Wallet');
        $this->checkUserAgent();
    }

    /*
 	 * 流水详情页面
 	 * @date:2015-05-14
 	 * *********************
 	 * 函数说明：
 	 * 流水详情页面，根据不同类型返回不同页面
 	 * *********************
 	 */
    public function detail($token)
    {
        $this->load->config('pay');
        $token = urldecode($token);
        $data = $this->strCode($token);
        $data = json_decode($data, TRUE);
        if (empty($data['uid']) OR empty($data['tradeNo']))
        {
            die('订单参数缺失');
        }
        $order = $this->Wallet->getDetail($data['uid'], $data['tradeNo']);
        $order OR die('订单信息不存在');

        $payAllCfg = $this->config->item('pay_all_cfg');
        array_key_exists($order['ctype'], $payAllCfg['jylx_cfg']) OR die('未知类型');
        $this->showDetail($order);
    }

    private function showDetail($order)
    {
        $data = array();
        $balance = $order['income'] - $order['expend'];
        $sign = ($balance > 0) ? '+' : '';
        $data['balance'] = $sign . number_format(ParseUnit($balance, 1), 2);
        $data['residue'] = ParseUnit($order['umoney'], 1);
        $data['created'] = $order['created'];
        $data['tradeNo'] = $order['trade_no'];
        $data['success'] = ($order['mark'] == 2);
        $data['tradeType'] = $this->getTradeTypeStr($order);
        $data['content'] = $order['content'];

        // 提现详情优化
        if($order['ctype'] == '4')
        {
            // 查询后台操作状态
            $withdrawInfo = $this->Wallet->getWithdrawInfo($order['trade_no']);
            $data['status'] = $withdrawInfo['status'];
            $data['start_check'] = $withdrawInfo['start_check'];
            $data['fail_time'] = $withdrawInfo['fail_time'];
            $data['succ_time'] = $withdrawInfo['succ_time'];
            $data['created'] = $withdrawInfo['created'];
            $data['content'] = $withdrawInfo['content'];
            $data['balance'] = $sign . number_format(ParseUnit($withdrawInfo['money'], 1), 2);
            $this->load->view('trade/withdraw_detail', $data);
        }
        else
        {
            $this->load->view('trade/recharge_detail', $data);
        }
    }

    private function getTradeTypeStr($order)
    {
        $payAllCfg = $this->config->item('pay_all_cfg');
        $tradeTypeAry = array();
        $cType = $order['ctype'];
        if (!array_key_exists($cType, $payAllCfg['jylx_cfg']))
        {
            return '';
        }
        array_push($tradeTypeAry, $payAllCfg['jylx_cfg'][$cType]);

        $addition = $order['additions'];
        if (array_key_exists($addition, $payAllCfg['pay_cfg']))
        {
            array_push($tradeTypeAry, $payAllCfg['pay_cfg'][$addition]['name']);
        }
        else
        {
            $tmpAry = explode('@', $addition);
            $tmpKey = $tmpAry[0];
            if ($tmpKey == 'alipaysdk')
            {
                array_push($tradeTypeAry, '支付宝');
            }
            elseif($tmpKey == 'shengpaysdk')
            {
                array_push($tradeTypeAry, '银行卡');
            }
            elseif (array_key_exists($tmpKey, $payAllCfg['pay_cfg']))
            {
                array_push($tradeTypeAry, $payAllCfg['pay_cfg'][$tmpKey]['name']);
            }
        }

        return implode('-', $tradeTypeAry);
    }
}