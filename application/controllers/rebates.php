<?php
/**
 * 联盟返点控制器
 */
if (!defined('BASEPATH'))
	exit('No direct script access allowed');

class Rebates extends MY_Controller 
{
	public function __construct()
	{
		parent::__construct();
		$this->load->config('rebates');
		$this->betType = $this->config->item('rebate_bet');
	}
	//推广联盟
	public function index()
	{
		if(empty($this->uinfo['rebates_level']) || $this->uinfo['rebates_level'] == '3')
		{
			$this->redirect('/error');
		}
		$this->load->model('rebates_model');
		$rebates = $this->rebates_model->getRebatesByUid($this->uid);
		if(!$rebates)
		{
			$this->redirect('/error');
		}
		$todayIncome = $this->rebates_model->getTodayIncome($this->uid);
		$cpage = intval($this->input->get('cpage', true));
		$cpage = $cpage <= 1 ? 1 : $cpage;
		$psize = 10;
		$this->load->library('BetCnName');
		//配置ajax
		$pdata['ajaxform'] = 'detail-form';
		$vdata['betType'] = $this->betType;
		$vdata['htype'] = 1;
		$vdata['pagenum'] = 0;
		$vdata['cpnum'] = 0;
		$vdata['count'] = 0;
		$vdata['totalMoney'] = 0;
		$vdata['lists'] = array();
		$vdata['pagestr'] = '';
		$vdata['rebates'] = $rebates;
		$vdata['todayIncome'] = $todayIncome;
		// 查询条件 , 按时间阶段, 彩种, 用户名
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
			$selectedDateTo = date( 'Y-m-d 23:59:59', strtotime( $selectedDateTo ));
		}
		$cons = array(
				'uid'   =>  $this->uid,
				'start' =>  $selectedDateFrom,
				'end'   =>  $selectedDateTo,
				'lid' => $this->input->post('lid', true),
				'userName' => $this->input->post('userName', true)
		);
		$datas = $this->rebates_model->getRebatesDetail($cons, $cpage, $psize);
		if(!empty($datas[1]))
		{
			$vdata['pagenum'] = $pdata['pagenum'] = ceil($datas[1] / $psize);
			$vdata['totalMoney'] = $datas[2];
			$vdata['cpnum'] = count($datas[0]);
			$vdata['count'] = $datas[1];
			$vdata['lists'] = $datas[0];
			$vdata['pagestr'] = $this->load->view('v1.1/elements/common/pages', $pdata, true);
		}
		if($this->is_ajax)
		{
			echo $this->load->view('v1.1/rebates/index', $vdata, true);
		}
		else
		{
			$this->display('rebates/index', $vdata, 'v1.1');
		}
	}
	
	/**
	 * 我的下线
	 */
	public function subordinate()
	{
		if(empty($this->uinfo['rebates_level']) || empty($this->uid))
		{
			die('访问错误');
		}
		$this->load->model('rebates_model');
		$rebates = $this->rebates_model->getRebatesByUid($this->uid);
		if(!$rebates || $rebates['puid'])
		{
			die('访问错误');
		}
		$cpage = intval($this->input->get('cpage', true));
  		$cpage = $cpage <= 1 ? 1 : $cpage;
		$psize = 10;
		//配置ajax
		$pdata['ajaxform'] = 'subordinate-form';
		$vdata['pagenum'] = 0;
		$vdata['cpnum'] = 0;
		$vdata['count'] = 0;
		$vdata['lists'] = array();
		$vdata['pagestr'] = '';
		$vdata['cpage'] = 0;
		$orderBy = $this->input->post('orderBy', true);
		$vdata['search'] = array(
			'start' => $this->input->post('date_from', true),
			'end' => $this->input->post('date_to', true),
			'uname' => $this->input->post('uname', true),
			'orderBy' => $orderBy,
		);
		// 查询条件 , 按时间阶段, 用户名
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
			$selectedDateTo = date( 'Y-m-d 23:59:59', strtotime( $selectedDateTo ));
		}
		$cons = array(
				'uid'   =>  $this->uid,
				'start' =>  $selectedDateFrom,
				'end'   =>  $selectedDateTo,
				'uname' => $this->input->post('uname', true),
		);
		$datas = $this->rebates_model->getSubordinate($cons, $cpage, $psize, $orderBy);
		if(!empty($datas[1]))
		{
			$vdata['pagenum'] = $pdata['pagenum'] = ceil($datas[1] / $psize);
			$vdata['cpnum'] = count($datas[0]);
			$vdata['count'] = $datas[1];
			$vdata['lists'] = $datas[0];
			$vdata['cpage'] = $cpage;
			$vdata['pagestr'] = $this->load->view('v1.1/elements/common/pages', $pdata, true);
		}
		echo $this->load->view("v1.1/rebates/subordinate", $vdata, true);
	}
	
	/**
	 * 推广链接存cookie
	 */
	public function svip()
	{
		$id = intval($this->input->get('id', true));
		if($id){
			$domain = str_replace('888.', '', $this->config->item('domain'));
			$cval = array(
    				'name' => 'rebateId',
    				'value' => $id,
					'expire' => 0,
    				'domain' => $domain,
    				'path' => '/',
    				'prefix' => '',
    				'secure' => false
    		);
    		$this->input->set_cookie($cval);
		}
		
		$this->redirect('/');
	}
}
