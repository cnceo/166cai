<?php

/**
 * 充值补单脚本
 *
 * @date:2016-12-08
 */
defined('BASEPATH') or exit('No direct script access allowed');

class Cli_Recharge_Supplement extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('wallet_model');
    }

    /**
     * 补单脚本
     * @param unknown $start
     * @param unknown $end
     */
    public function index($start = '', $end = '')
    {
        if(empty($start)) {
            $start = date('Y-m-d H:i:s', strtotime("-30 minutes"));
        }
        if(empty($end)) {
            $end = date('Y-m-d H:i:s');
        }
        $id = 0;
        $num = 0;
        $total = 0;
        $recharges = $this->wallet_model->getRecharge($start, $end, $id);
        while(!empty($recharges))
        {
            foreach ($recharges as $order) {
                $url = $this->config->item('base_url') . "/api/recharge/orderSelect/{$order['trade_no']}";
                $respone = $this->tools->request($url, array(), $tout = 10);
                $respone = json_decode($respone, true);
                if($respone['code'] == '0' && $respone['isDone'] == '1')
                {
                    $num += 1;
                }
                $total += 1;
                $id = $order['id'];
            }
            
            $recharges = $this->wallet_model->getRecharge($start, $end, $id);
        }
        
        echo "补单已完成，" . $num . "/" . $total . "个订单补单成功";
    }
}
