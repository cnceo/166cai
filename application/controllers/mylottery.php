<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class MyLottery extends MY_Controller {

    private $ctype = array(
            'all' => '所有交易类型',
            'income' => '收入',
            0 => '充值',
            //7 => '解除冻结预付款', //已废弃
            2 => '奖金派送',
            9 => '彩金派送',
            3 => '订单失败返款',
            13 => '返还追号预付款',
            14 => '返利',
            15 => '合买保底退款',
            17 => '返还跟单预付款',
            'expand' => '支出',
            1 => '购买彩票',
            //6 => '扣除预付款',  //已废弃          
            4 => '提款',
            12 => '冻结追号预付款',
            10 => '其他应收款项',
            11 => '其他',
            16 => '冻结跟单预付款',
    );

	/**
	 * 彩票后台入口控制器
	 */
	public function __construct()
    {
       parent::__construct();
       $this->load->model('lottery_model', 'Lottery');
       $this->load->library('BetCnName');
       $this->load->model('Notice_Model');
       
    }
    
    public function index()
    {
    	$this->account();
    }
    
    public function account()
    {
    	$vdata['htype'] = 1;
  		$cpage = 1;
  		$psize = 10;
        $this->load->model('order_model');

        // 查询条件 , 按时间阶段, 彩种, 是否支付
  		$cons = array(
            'uid'   =>  $this->uid, 
            'start' =>  date('Y-m-d', strtotime( '-1 month' )), 
            'end'   =>  date('Y-m-d 23:59:59'), 
            'lid'   =>  '0',
            'nopay' =>  false
        );

  		$odatas = $this->order_model->getOrders($cons, ($cpage-1) * $psize.", ".$psize);
  		if(!empty($odatas['totals']['total']))
  		{
  			$vdata['orders'] = $odatas['datas'];
  		}
  		$vdata['rfshbind'] = 1;
    	$this->display('mylottery/account', $vdata, 'v1.1');
    }
    
	public function detail()
    {
    	$vdata['htype'] = 1;
    	$pdata['pagenum'] = 20;

    	$cpage = intval($this->input->get('cpage', true));
  		$cpage = $cpage <= 1 ? 1 : $cpage;
  		$psize = 10;
  		//配置ajax
  		$pdata['ajaxform'] = 'trade-form';
  		
        $this->load->model('wallet_model');
        $vdata['ctype'] = $this->ctype;
    	if(in_array($this->uinfo['rebates_level'], array(0,3)))
		{
			unset($vdata['ctype']['14']);
		}

        // 查询时间起讫
        $selectedDateFrom = $this->input->post('date_from', true);
        if( empty( $selectedDateFrom ) )
        {
            $selectedDateFrom = date('Y-m-d', strtotime( '-1 month' )); // 默认为最近一个月
        }
        if(substr($selectedDateFrom, 0, 4) < '2014')
        {
        	$selectedDateFrom = '2014-01-01';
        }

        $selectedDateTo = $this->input->post('date_to', true);
        if( empty( $selectedDateTo ) )
        {
            $selectedDateTo = date('Y-m-d 23:59:59'); // 默认为最近一个月
        }
        else
        {
            $selectedDateTo = date( 'Y-m-d 23:59:59', strtotime( $selectedDateTo ) );
        }
        if(substr($selectedDateTo, 0, 4) < '2014')
        {
        	$selectedDateTo = '2014-12-31 23:59:59';
        }
        
        // 查询交易类型
        $selectedCtype = $this->input->post('ctype', true);

        // 查询条件 , 按时间阶段, 彩种, 是否支付
  		$cons = array(
            'uid'   =>  $this->uid, 
            'start' =>  $selectedDateFrom, 
            'end'   =>  $selectedDateTo, 
            'ctype' =>  ($selectedCtype=='')? 'all' : $selectedCtype
        );
  		$odatas = $this->wallet_model->getTradeDetail($cons, $cpage, $psize);
  		if(!empty($odatas['total']))
  		{
  			$vdata['income'] = $odatas['income'];
  			$vdata['umoney'] = $odatas['umoney'];
  			$vdata['pagenum'] = $pdata['pagenum'] = ceil($odatas['total'] / $psize);
  			$vdata['cpnum'] = count($odatas['datas']);
  			$vdata['orders'] = !empty( $odatas['datas'] ) ? $odatas['datas'] : array();
  			$vdata['pagestr'] = $this->load->view('v1.1/elements/common/pages', $pdata, true);
  		}
        else
        {
  			$vdata['pagenum'] = $pdata['pagenum'] = 0;
  			$vdata['cpnum'] = 0;
  			$vdata['orders'] = array();
  			$vdata['pagestr'] = ''; //$this->load->view('elements/common/pages', $pdata, true);
        }

  		if($this->is_ajax)
  		{
  			echo $this->load->view('v1.1/mylottery/detail', $vdata, true);
  		}
        else
        {
        	$vdata['rfshbind'] = 1;
            $this->display('mylottery/detail', $vdata, 'v1.1');
        }
    }
    
    // 充值
    public function recharge()
    {
    	$vdata['htype'] = 1;
    	$pdata['pagenum'] = 20;

    	$cpage = intval($this->input->get('cpage', true));
  		$cpage = $cpage <= 1 ? 1 : $cpage;
  		$psize = 10;
  		//配置ajax
  		$pdata['ajaxform'] = 'recharge-form'; //#

        $this->load->model('wallet_model');

        $vdata['ctype'] = $this->ctype;

        // 查询时间起讫
        $selectedDateFrom = $this->input->post('date_from', true);
        if( empty( $selectedDateFrom ) )
        {
            $selectedDateFrom = date('Y-m-d', strtotime( '-1 month' )); // 默认为最近一个月
        }

        $selectedDateTo = $this->input->post('date_to', true);
        if( empty( $selectedDateTo ) )
        {
            $selectedDateTo = date('Y-m-d 23:59:59'); // 默认为最近一个月
        }
        else
        {
            $selectedDateTo = date( 'Y-m-d 23:59:59', strtotime( $selectedDateTo ) );
        }

        // 查询条件 , 按时间阶段, 彩种, 是否支付
  		$cons = array(
            'uid'   =>  $this->uid, 
            'start' =>  $selectedDateFrom, 
            'end'   =>  $selectedDateTo
        );

        $odatas = $this->wallet_model->getRechargeDetail($cons, $cpage, $psize);
  		if(!empty($odatas['total']))
  		{
  			$vdata['income'] = $odatas['income'];
  			$vdata['umoney'] = $odatas['umoney'];
  			$vdata['pagenum'] = $pdata['pagenum'] = ceil($odatas['total'] / $psize);
  			$vdata['cpnum'] = count($odatas['datas']);
  			$vdata['orders'] = $odatas['datas'];
  			$vdata['pagestr'] = $this->load->view('v1.1/elements/common/pages', $pdata, true);
  		}
        else
        {
  			$vdata['pagenum'] = $pdata['pagenum'] = 0;
  			$vdata['cpnum'] = 0;
  			$vdata['orders'] = array();
  			$vdata['pagestr'] = ''; // $this->load->view('elements/common/pages', $pdata, true);
        }

  		if($this->is_ajax)
  		{
  			echo $this->load->view('v1.1/mylottery/recharge', $vdata, true);
  		}
        else
        {
        	$vdata['rfshbind'] = 1;
            $this->display('mylottery/recharge', $vdata, 'v1.1');
        }
    }

    // 提款
    public function withdrawals()
    {
    	$vdata['htype'] = 1;
    	$pdata['pagenum'] = 20;

    	$cpage = intval($this->input->get('cpage', true));
  		$cpage = $cpage <= 1 ? 1 : $cpage;
  		$psize = 10;
  		//配置ajax
  		$pdata['ajaxform'] = 'withdrawals-form'; //#

        $this->load->model('wallet_model');

        $vdata['ctype'] = $this->ctype;

        // 查询时间起讫
        $selectedDateFrom = $this->input->post('date_from', true);
        if( empty( $selectedDateFrom ) )
        {
            $selectedDateFrom = date('Y-m-d', strtotime( '-1 month' )); // 默认为最近一个月
        }

        $selectedDateTo = $this->input->post('date_to', true);
        if( empty( $selectedDateTo ) )
        {
            $selectedDateTo = date('Y-m-d 23:59:59'); // 默认为最近一个月
        }
        else
        {
            $selectedDateTo = date( 'Y-m-d 23:59:59', strtotime( $selectedDateTo ) );
        }

        // 查询条件 , 按时间阶段, 彩种, 是否支付
  		$cons = array(
            'uid'   =>  $this->uid, 
            'start' =>  $selectedDateFrom, 
            'end'   =>  $selectedDateTo
        );

  		$odatas = $this->wallet_model->getWithdrawDetail($cons, $cpage, $psize);
  		if(!empty($odatas['total']))
  		{
  			$vdata['income'] = 0;
  			$vdata['money'] = $odatas['money'];
  			$vdata['pagenum'] = $pdata['pagenum'] = ceil($odatas['total'] / $psize);
  			$vdata['cpnum'] = count($odatas['datas']);
  			$vdata['orders'] = $odatas['datas'];
  			$vdata['pagestr'] = $this->load->view('v1.1/elements/common/pages', $pdata, true);
  		}
        else
        {
  			$vdata['pagenum'] = $pdata['pagenum'] = 0;
  			$vdata['cpnum'] = 0;
  			$vdata['orders'] = array();
  			$vdata['pagestr'] = ''; //$this->load->view('elements/common/pages', $pdata, true);
        }

  		if($this->is_ajax)
  		{
            echo $this->load->view('v1.1/mylottery/withdrawals', $vdata, true);
  		}
        else
        {
        	$vdata['rfshbind'] = 1;
            $this->display('mylottery/withdrawals', $vdata, 'v1.1');
        }
    }
    
    //申请提款撤销（已废弃 2016/4/28）
    public function withdrawConceal()
    {
    	$result = 0;
    	$action = $this->input->post('action');
    	if($action == 'withdraw_conceal')
    	{
    		$trade_no = trim($this->input->post('trade_no', true));
    		if(!empty($trade_no))
    		{
    			$this->load->model('wallet_model');
    			if($this->wallet_model->withDrawFail($trade_no, $this->uid))
    			{
    				$result = 1;
    			}
    		}
    	}
    	echo $result;
    }

    public function betlog()
    {
    	$vdata['htype'] = 1;
    	$cpage = intval($this->input->get('cpage', true));
  		$cpage = $cpage <= 1 ? 1 : $cpage;
  		$psize = 10;
  		//配置ajax
  		$pdata['ajaxform'] = 'betlog-form';
  		
        $this->load->model('order_model');

        $vdata['dateSpan'] = array(
            1 => "一个月内",    
            2 => "最近三个月",
            3 => "最近六个月",
            4 => "最近一年",
        );
        
        $vdata['buyTypeSpan'] = array(
        	0 => "所有购买方式",
        	1 => "自购",
        	2 => "追号",
        	3 => "赔付",
        	4 => "发起合买",
        	5 => "参与合买",
                6 => '定制跟单'
        );

        $vdata['betType'] = array(
            0    =>     '所有彩种',
            11	 =>     '胜负彩',
            19	 =>     '任九',
            33	 =>     '排列三',
            35	 =>     '排列五',
            42	 =>     '竞彩足球',
            43	 =>     '竞彩篮球',
            51	 =>     '双色球',
            52	 =>     '福彩3D',
            10022	 => '七星彩',
            21406	 => '老11选5',
        	21407	 => '新11选5',
        	21408	 => '惊喜11选5',
            23528	 => '七乐彩',
            23529	 => '大乐透',
        	44   => '冠军彩',
        	45   => '冠亚军彩',
        	53   => '上海快三',
        	54   => '快乐扑克',
        	//55   => '老时时彩',
        	56   => '吉林快三',
            57   => '江西快三',
            21421	 => '乐11选5',
        );

        
        
        $post = $this->input->post();
        
        // 查询时间跨度
        $selectedDateSpan = $post['date'];
        if( empty( $selectedDateSpan ) )
        {
        	$selectedDateSpan = 1; // 默认为最近一个月
        }

        $dateStartEnd = $this->_getDateSpanCons( $selectedDateSpan );

        // 查询条件 , 按时间阶段, 彩种, 是否支付
  		$cons = array(
            'uid'   =>  $this->uid, 
            'start' =>  $dateStartEnd['start'], 
            'end'   =>  $dateStartEnd['end'], 
            'lid'   =>  (empty($post['lid']) || !array_key_exists($post['lid'], $vdata['betType'])) ? '0' : $post['lid'],
            'nopay' =>  (!empty( $post['nopay'] ) ? true : false ),
  	    'marginonly' =>  (!empty( $post['marginonly'] ) ? true : false ),
  	    'buyType' => !in_array($post['buyType'], array(0, 1, 2, 3, 4, 5, 6)) ? 0 : $post['buyType'],
            'kaijiang' =>  (!empty( $post['kaijiang'] ) ? true : false )
        );

  		$odatas = $this->order_model->getOrders($cons, ($cpage-1) * $psize.", ".$psize);
      // 加奖
//       if(!empty($odatas['datas']))
//       {
//         foreach ($odatas['datas'] as $key => $orderDetail) 
//         {
//           $add_money = 0;
//           if((($orderDetail['activity_ids'] & 4) == 4) && (($orderDetail['activity_status'] & 4) == 4))
//           {
//             $detail = $this->order_model->getJjDetail($orderDetail['orderId']);

//             if(!empty($detail))
//             {
//                 $add_money = $detail['add_money'];
//             }
//           }
//           $odatas['datas'][$key]['add_money'] = $add_money;
//         }
//       }

  		if(!empty($odatas['totals']['total']))
  		{
  			$vdata['pagenum'] = $pdata['pagenum'] = ceil($odatas['totals']['total'] / $psize);
  			$vdata['notover'] = $odatas['totals']['notover'];
  			$vdata['money'] = $odatas['totals']['money'];
  			$vdata['prize'] = $odatas['totals']['prize'];
  			$vdata['cpnum'] = count($odatas['datas']);
  			$vdata['orders'] = $odatas['datas'];
  			$vdata['pagestr'] = $this->load->view('v1.1/elements/common/pages', $pdata, true);
  		}
        else
        {
  			$vdata['pagenum'] = $pdata['pagenum'] = 0;
  			$vdata['cpnum'] = 0;
  			$vdata['orders'] = array();
  			$vdata['pagestr'] = ''; //$this->load->view('elements/common/pages', $pdata, true);
        }

  		if($this->is_ajax)
  		{
  			$this->get_lottery_config($vdata);
  			echo $this->load->view('v1.1/mylottery/betlog', $vdata, true);
  		}else
        {
        	$vdata['rfshbind'] = 1;
            $this->display('mylottery/betlog', $vdata, 'v1.1');
        }
    }
    
    public function chaselog() {
    	
    	$cpage = intval($this->input->get('cpage', true));
  		$cpage = $cpage <= 1 ? 1 : $cpage;
    	$date = $this->input->post('date', true) ;
    	$date = empty($date) ? 3 : $date;
    	$lid = $this->input->post('lid', true);
    	$is_chase = $this->input->post('is_chase', true);
    	$has_bonus = $this->input->post('has_bonus', true);

    	$psize = 10;
    	
    	$this->load->model('chase_order_model');
    	
    	$vdata['dateSpan'] = array(
    			3 => "最近六个月",
    			4 => "最近一年",
    	);
    	
    	$vdata['betType'] = array(
    			0        => '所有彩种',
    			51	     => '双色球',
    			23529	 => '大乐透',
    			33   	 => '排列三',
    			35  	 => '排列五',
    			52	     => '福彩3D',
    			10022	 => '七星彩',
    			23528	 => '七乐彩',
    			21406	 => '老11选5',
    			21407	 => '新11选5',
    			21408	 => '惊喜11选5',
    			53   	 => '上海快三',
    			54   	 => '快乐扑克',
    			//55   	=> '老时时彩',
    			56   	=> '吉林快三',
    	       57   => '江西快三',
    	       21421   => '乐11选5',
    	);
    	$vdata['htype'] = 1;
    	$dateStartEnd = $this->_getDateSpanCons( $date );
    	    	    	
    	$cons = array(
    			'uid'   =>  $this->uid,
    			'start' =>  $dateStartEnd['start']." 00:00:00",
    			'end'   =>  $dateStartEnd['end'],
    	);
    	
    	if ($lid) {
    		$cons['lid'] = $lid;
    	}
    	
    	if ($is_chase == 1) {
    		$cons['other'][] = " and m.status in ('0', '240')";
    	}
    	
    	if ($has_bonus == 1) {
    		$cons['other'][] = ' and m.bonus > 0';
    	}
    	$cdatas = $this->chase_order_model->getChases($cons, $cpage, $psize);
    	
    	$vdata['orders'] = $cdatas['datas'];
    	$vdata['totals'] = $cdatas['totals'];
    	$vdata['pagenum'] = $pdata['pagenum'] = ceil($cdatas['totals']['total'] / $psize);
    	$pdata['ajaxform'] = 'chaselog-form';
    	$vdata['pagestr'] = $this->load->view('v1.1/elements/common/pages', $pdata, true);
   		if($this->is_ajax)
  		{
  			$this->get_lottery_config($vdata);
  			echo $this->load->view('v1.1/mylottery/chaselog', $vdata, true);
  		}
        else
        {
        	$vdata['rfshbind'] = 1;
            $this->display('mylottery/chaselog', $vdata, 'v1.1');
        }
    }
    
    public function redpack()
    {
    	$vdata['htype'] = 1;
    	$cpage = intval($this->input->get('cpage', true));
  		$cpage = $cpage <= 1 ? 1 : $cpage;
    	$ctype = $this->input->post('ctype', true);
    	$ctype = $ctype ? $ctype : 1;
    	$psize = 10;
    	//配置ajax
    	$pdata['ajaxform'] = 'redpack-form';
    	$this->load->model('red_pack_model');
    	// 查询条件 , 按时间阶段, 彩种, 是否支付
    	$cons = array(
    		'uid'   =>  $this->uid,
    		'ctype' =>  $ctype,
    	);
    	
    	$redpacks = $this->red_pack_model->getUserRedpacks($cons, $cpage, $psize);
    	if(!empty($redpacks['totals']))
    	{
    		$vdata['pagenum'] = $pdata['pagenum'] = ceil($redpacks['totals'] / $psize);
    		$vdata['totals'] = $redpacks['totals'];
    		$vdata['cpnum'] = count($redpacks['datas']);
    		$vdata['redpacks'] = $redpacks['datas'];
    		$vdata['ctype'] = $ctype;
    		$vdata['pagestr'] = $this->load->view('v1.1/elements/common/pages', $pdata, true);
    	}
    	else
    	{
    		$vdata['pagenum'] = $pdata['pagenum'] = 0;
    		$vdata['totals'] = 0;
    		$vdata['cpnum'] = 0;
    		$vdata['redpacks'] = array();
    		$vdata['ctype'] = $ctype;
    		$vdata['pagestr'] = '';
    	}
    	
    	if($this->is_ajax)
    	{
    		echo $this->load->view('v1.1/mylottery/redpack', $vdata, true);
    	}
    	else
    	{
    		$vdata['rfshbind'] = 1;
    		$this->display('mylottery/redpack', $vdata, 'v1.1');
    	}
    }
    
    private function get_lottery_config(&$data)
    {
    	$this->load->driver('cache', array('adapter' => 'redis'));
        $REDIS = $this->config->item('REDIS');
        $lotteryConfig = $this->cache->get($REDIS['LOTTERY_CONFIG']);
        $data['lotteryConfig'] = json_decode($lotteryConfig, true);
    }
    // 根据所需时间跨度, 返回起讫日期
    private function _getDateSpanCons( $dateType ) 
    {
        $cons = array( );
        $dateFormatStr = 'Y-m-d';
        switch( $dateType )
        {
            // 一个月内
            case 1: 
                $cons['start'] = date($dateFormatStr, strtotime( '-1 month' ));
                break;
            // 三个月内
            case 2:
                $cons['start'] = date($dateFormatStr, strtotime( '-3 month' ));
                break;
            // 六个月内
            case 3:
                $cons['start'] = date($dateFormatStr, strtotime( '-6 month' ));
                break;
            // 一年内
            case 4:
                $cons['start'] = date($dateFormatStr, strtotime( '-1 year' ));
                break;
            // 默认一月内
            default:
                $cons['start'] = date($dateFormatStr, strtotime( '-1 month' ));
                break;
        }
        $cons['end'] = date( 'Y-m-d 23:59:59' );
        return $cons;
    }
    
    /**
     * 充值、支付成功页
     * @param unknown_type $trade_no
     */
    public function rchagscess($trade_no)
    {
    	if(!$this->uid)
    	{
    		$this->redirect('/');
    	}
    	
    	$this->load->model('pay_model');
    	$payLog = $this->pay_model->getPayLog($trade_no, 1);
    	if(empty($payLog))
    	{
    		$this->redirect('/error/');
    	}
    	if($payLog['status'] == 1)
    	{
    		$this->load->model('wallet_model');
    		$walletInfo = $this->wallet_model->getWalletLog($trade_no);
    		if(empty($walletInfo) || ($this->uid != $walletInfo['uid']))
    		{
    			$this->redirect('/error/');
    		}
    		$data = array(
    			'money' => ParseUnit($walletInfo['money'], 1),
    			'orderId' => $walletInfo['orderId'],
    			'orderType' => $walletInfo['status'],
    			'statusFlag' => true,
    		);
    		if($walletInfo['orderId'])
    		{
    			$orderStatus = $this->wallet_model->getOrderStatus($walletInfo['orderId'], $walletInfo['status']);
    			//未付款
    			if($orderStatus < 40)
    			{
    				if($payLog['select_num'] < 15 && in_array($orderStatus, array(0, 10)))
    				{
    					$payData['select_num'] = $payLog['select_num'] + 1;
    					$this->pay_model->updatePayLog($trade_no, $payData); //更新刷新次数
    					$vdata['refresh'] = true;
              $this->displayLess('v1.1/mylottery/paywait', $vdata);return ;
    				}
    				else
    				{
    					$data['statusFlag'] = false;
    				}
    			}
    		}
    		$this->displayLess('v1.1/mylottery/rchagscess', $data);
    	}
    	else
    	{
    		if($payLog['select_num'] < 10)
    		{
    			$payData['select_num'] = $payLog['select_num'] + 1;
    			$this->pay_model->updatePayLog($trade_no, $payData); //更新刷新次数
    			$data['refresh'] = true;
    		}
    		else
    		{
    			$data['refresh'] = false;
    		}
        $this->displayLess('v1.1/mylottery/paywait', $data);
    	}
    }

    
    public function gendanlog()
    {
        $cpage = intval($this->input->get('cpage', true));
  		$cpage = $cpage <= 1 ? 1 : $cpage;
    	$date = $this->input->post('date', true) ;
    	$date = empty($date) ? 3 : $date;
    	$lid = $this->input->post('lid', true);
    	$is_chase = $this->input->post('is_chase', true);
    	$has_bonus = $this->input->post('has_bonus', true);

    	$psize = 10;
    	
    	$this->load->model('follow_order_model');
    	
    	$vdata['dateSpan'] = array(
    			3 => "最近六个月",
    			4 => "最近一年",
    	);
    	
    	$vdata['betType'] = array(
    			0        => '所有彩种',
    			51	 => '双色球',
    			23529	 => '大乐透',
    			33   	 => '排列三',
    			35  	 => '排列五',
    			11   	 => '胜负彩',
    			19  	 => '任选九',            
                        42   	 => '竞彩足球',
                        43   	 => '竞彩篮球',            
    			52	 => '福彩3D',
    			10022	 => '七星彩',
    			23528	 => '七乐彩'
    	);
    	$vdata['htype'] = 1;
    	$dateStartEnd = $this->_getDateSpanCons( $date );
    	    	    	
    	$cons = array(
    			'uid'   =>  $this->uid,
    			'start' =>  $dateStartEnd['start']." 00:00:00",
    			'end'   =>  $dateStartEnd['end'],
    	);
    	
    	if ($lid) {
    		$cons['lid'] = $lid;
    	}
    	
    	if ($is_chase == 1) {
    		$cons['other'][] = " and m.status > 0 and m.my_status = 0";
        }else {
                $cons['other'][] = " and m.status > 0";
        }
    	
    	if ($has_bonus == 1) {
    		$cons['other'][] = ' and m.totalMargin > 0';
    	}
    	$cdatas = $this->follow_order_model->getAllOrders($cons, $cpage, $psize);
    	
    	$vdata['orders'] = $cdatas['datas'];
    	$vdata['totals'] = $cdatas['totals'];
    	$vdata['pagenum'] = $pdata['pagenum'] = ceil($cdatas['totals']['total'] / $psize);
    	$pdata['ajaxform'] = 'gendanlog-form';
    	$vdata['pagestr'] = $this->load->view('v1.1/elements/common/pages', $pdata, true);
   		if($this->is_ajax)
  		{
  			$this->get_lottery_config($vdata);
  			echo $this->load->view('v1.1/mylottery/gendanlog', $vdata, true);
  		}
        else
        {
        	$vdata['rfshbind'] = 1;
            $this->display('mylottery/gendanlog', $vdata, 'v1.1');
        }
    }
}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */
