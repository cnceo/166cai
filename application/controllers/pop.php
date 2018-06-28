<?php
if (!defined('BASEPATH'))
	exit('No direct script access allowed');
require_once APPPATH . '/core/CommonController.php';
class Pop extends CommonController 
{
	
	public function __construct()
	{
		parent::__construct();
		//加密功能
		$this->pre_decrypt();
		$this->cre_pubkey();
	}
	
	/**
	 * 获取版本信息
	 */
	private function getVersion()
	{
		return $this->input->post('version', true) ? $this->input->post('version', true) : $this->input->get('version', true);
	}
	
	/**
	 * 登录弹层加载
	 */
	public function getLogin()
	{
		$this->pagesUrl = $this->config->item('pages_url');
		$vdata['tigger'] = $this->input->post('tigger', true);
		$vdata['rfsh'] = $this->input->post('rfsh', true);
		$this->load->library('WeixinLogin');
        $vdata['qrbLogin'] = $this->weixinlogin->qrbLogin();
		echo $this->ajaxDisplay('elements/pop/login', $vdata, $this->getVersion());
	}

    /**
     * 开售提醒弹层加载
     * author：liuz
     */
    public function getKaishou()
    {
        echo $this->ajaxDisplay('elements/pop/kaishou', '', $this->getVersion());
    }

	/**
	 * 注册弹层加载
	 */
	public function getRegister()
	{
		$this->pagesUrl = $this->config->item('pages_url');
		echo $this->ajaxDisplay('elements/pop/register', '', $this->getVersion());
	}
	
	/**
	 * 注册欢迎弹层
	 */
	public function getWelcome() 
	{
		echo $this->ajaxDisplay('elements/pop/welcome', '', $this->getVersion());
	}
	
	
	/**
	 * 完善信息弹层 
	 */
	public function getBind()
	{
		$vdata['rfsh'] = $this->input->post('rfsh') ? 1 : 0;
		$vdata['real_name'] = $this->uinfo['real_name'];
		$vdata['body'] = $this->load->view('v1.1/elements/pop/userInfo1', $vdata, true);
		echo $this->ajaxDisplay('elements/pop/userInfo', $vdata, $this->getVersion());
	}
	
	public function getUserInfo1() {
		if ($this->is_ajax) {
			$vdata = $this->input->post(null, true);
			if(empty($this->uid)) die('110');
			if($this->uinfo['real_name'] || $this->uinfo['id_card']) die('5');
			if(empty($vdata['real_name'])) die('1');
			$this->load->library('BindIdCard');
			$this->bindidcard->pcsetIdCardInfo($vdata, array('uid' => $this->uid, 'phone' => $this->uinfo['phone']));
			echo $this->load->view('v1.1/elements/pop/userInfo2', $vdata, true);
		}
	}
	
	/**
	 * 意见反馈弹层
	 */
	public function getFeedBack()
	{
		echo $this->ajaxDisplay('mynews/feedback', '', $this->getVersion());
	}

	/**
	 * 如何算奖弹层
	 */
	public function howCalculate()
	{
		$type = $this->input->post('type', true);
        $view = $type == 'dg' ? 'howCalcJCZQDG' : 'howCalculate';
        echo $this->ajaxDisplay('elements/pop/' . $view, '', $this->getVersion());
	}
	
	/**
	 * 历史开奖对比
	 */
	public function historyCompare()
	{
		$data['showBind'] = false;
		if (!empty($this->uinfo) && !$this->isBindForRecharge()) {
			$data['showBind'] = true;
		}
		echo $this->ajaxDisplay('elements/pop/historyCompare', $data, $this->getVersion());
	}
	
	/**
	 * 历史开奖对比
	 */
	public function historyNodata()
	{
		echo $this->ajaxDisplay('elements/pop/historyNodata', '', $this->getVersion());
	}

	public function howCalcJCLQ()
	{
        echo $this->ajaxDisplay('elements/pop/howCalcJCLQ', '', $this->getVersion());
	}

	/**
	 * 如何领奖弹层
	 */
	public function howReceive()
	{
        echo $this->ajaxDisplay('elements/pop/howReceive', '', $this->getVersion());
	}
	
	/**
	 * 奖金计算器弹层
	 */
	public function jiangjinCalculate()
	{
		echo $this->ajaxDisplay('elements/pop/jiangjinCalculate', '', $this->getVersion());
	}
    /**
     * 奖金计算器弹层
     */
    public function dltjiangjinCalculate()
    {
        echo $this->ajaxDisplay('elements/pop/dltjiangjinCalculate', '', $this->getVersion());
    }

	/**
	 * 如何投注弹层
	 */
	public function howBet()
	{
        echo $this->ajaxDisplay('elements/pop/howBet', '', $this->getVersion());
	}

	public function howBetJCLQ()
	{
        echo $this->ajaxDisplay('elements/pop/howBetJCLQ', '', $this->getVersion());
	}
	
	/**
	 * 用户委托协议弹层
	 */
	public function getAgreement()
	{
		$type = $this->input->post('type', true) ? $this->input->post('type', true) : $this->input->get('type', true);
		if($type == 'lottery_pro')
		{
			echo $this->ajaxDisplay('elements/pop/agreement_weituo', '', $this->getVersion());
		}
		else
		{
			echo $this->ajaxDisplay('elements/pop/agreement_xianhao', '', $this->getVersion());
		}
	}
	
	/**
	 * 查看用户身份证弹层
	 */
	public function getCardPop()
	{
		echo $this->ajaxDisplay('elements/pop/cardPop', '', $this->getVersion());
	}

	/**
	 * 查看投注站信息
	 */
	public function getBetShop()
	{
		$shopId = $this->input->post('shopId', true);
		//$shopId = '45';
		$this->load->model('betstation_model', 'Betstation');
		$shopDetail = $this->Betstation->getBetShopDetail($shopId);
		echo $this->ajaxDisplay('elements/pop/shopDetail', array('shopDetail' => $shopDetail), $this->getVersion());
	}

	/**
	 * 查看充值tips
	 */
	public function getRechargeTip()
	{
		echo $this->ajaxDisplay('elements/pop/rechargeTip', '', $this->getVersion());
	}
	
	/**
	 * 添加下线弹窗
	 */
	public function addRebate()
	{
		echo $this->ajaxDisplay('elements/pop/addRebate', '', $this->getVersion());
	}
	
	public function setRebateOdd()
	{
		if($this->uid)
		{
			$uid = $this->input->post('id', true);
			$this->load->model('rebates_model');
			$rebate = $this->rebates_model->getRebatesByUid($uid);
			$vdata = array();
			if($rebate && $rebate['puid'] == $this->uid)
			{
				$this->load->config('rebates');
				$vdata = array(
					'uid' => $rebate['uid'],
					'rebate_odds' => json_decode($rebate['rebate_odds'], true),
					'oddType' => $this->config->item('rebate_odds_type'),
				);
			}
			echo $this->ajaxDisplay('elements/pop/setRebateOdd', $vdata, $this->getVersion());
		}
	}
	
	public function imgyzm() {
		echo $this->ajaxDisplay('elements/pop/imgyzm', array(), $this->getVersion());
	}
	
	public function hemai() {
		$data['showBind'] = false;
		if (!empty($this->uinfo) && !$this->isBindForRecharge()) {
			$data['showBind'] = true;
		}
		echo $this->ajaxDisplay('elements/pop/hemai', $data, $this->getVersion());
	}
	
	/**
	 * 修改用户名弹窗
	 */
	public function modifyName()
	{
		if ($this->is_ajax)
		{
			if($this->uid && $this->uinfo['nick_name_modify_time'] == '0000-00-00 00:00:00')
			{
				echo $this->ajaxDisplay('elements/pop/modifyName', array('oldUname' => $this->uname), $this->getVersion());
			}
		}
	}
        
        /**
	 * 合买编辑个人简介
	 */
	public function editSelf()
	{
            $this->load->model('user_model');
            $user = $this->user_model->findByUid($this->uid);
            if ($user['introduction_status'] == 0 || $user['introduction_status'] == 1) {
                $user['introduction']=$user['introduction'] ? $user['introduction'] : '这家伙很懒，只等中大奖！';
            } else {
                $user['introduction']='这家伙很懒，只等中大奖！';
            }
        echo $this->ajaxDisplay('elements/pop/editSelf', array('user' => $user), $this->getVersion());
    }
    
    
    public function gendan()
    {
        if($this->uid)
        {
            $uid = $this->input->post('uid', true);
            $lid = $this->input->post('lid', true);
            $this->load->model('follow_order_model');
            $res=$this->follow_order_model->checkHasGendan($this->uid, $uid, $lid);
            if ($res['code'] != 200) {
                echo $res['code'];die();
            }
            $this->load->model('united_planner_model');
            $gendanUser = $this->united_planner_model->getUserInfo($uid, $lid);
            $gendanUser['award'] = calGrade($gendanUser['united_points'], 5);
            $lottery = array('51' => 'ssq', '23529' => 'dlt', '42' => 'jczq', '43' => 'jclq', '11' => 'sfc', '19' => 'rj', '52' => 'fcsd', '23528' => 'qlc', '10022' => 'qxc', '33' => 'pls', '35' => 'plw');
            $gendanUser['liden'] = $lottery[$lid];
            $gendanUser['lid'] = $lid;
            echo $this->ajaxDisplay('elements/pop/gendan', array('gendanUser' => $gendanUser), 'v1.1');
        }
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

