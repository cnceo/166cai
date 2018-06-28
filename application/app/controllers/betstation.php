<?php

/*
 * APP 投注站
 * @date:2016-01-27
 */

class Betstation extends MY_Controller {
	
    public function __construct() 
    {
        parent::__construct();
        $this->load->model('betstation_model', 'Betstation');
    }

    /*
     * 根据彩种查看所有投注站信息
     * @parms: status说明 0：待审核，10：审核未通过，20：审核通过，30：上架，40：下架
     * @date:2016-01-27
     */
    public function allStation($lid)
    {
    	$partnerArr = $this->config->item('cfg_partner_lid');
        $search = array(
            'lottery_type' => $this->getLotteryType($lid),
            'status' => '30',
            'delete_flag' => '0',
        	'lid' => array_key_exists($lid, $partnerArr) ? $lid : '0'
        );

        $stationInfo = $this->Betstation->getBetShopInfo($search);
        $this->load->view('betstation/allStation', array('stationInfo' => $stationInfo));
    }

    /*
     * 获取彩种所属类型 福彩 体彩
     * @date:2016-01-27
     */
    public function getLotteryType($lid)
    {
        // 福彩：双色球，福彩3D，七乐彩
        if(in_array($lid, array('51', '52', '23528', '53')))
        {
            $lotteryType = 1;
        }
        else
        {
            $lotteryType = 0;
        }
        return $lotteryType;
    }
    
}