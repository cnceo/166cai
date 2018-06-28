<?php

if (!defined('BASEPATH')) exit('No direct script access allowed');

class Withdraw extends MY_Controller {

    /**
     * [__construct 提现]
     * @author LiKangJian 2017-06-19
     */
    public function __construct() 
    {
        parent::__construct();
        $this->load->model('newwallet_model');
        $this->load->model('Notice_Model');
        $this->load->model('pay_model');
        $this->config->load('pay');
    }
    /**
     * [index 提现主方法]
     * @author LiKangJian 2017-06-20
     * @return [type] [description]
     */
	public function index()
    {
        if(empty($this->uinfo)) $this->redirect('/main/login');
        $vdata['s_title'] = '彩票-我要提款-166彩票官网';
        $vdata['htype'] = 1;
        $vdata['withDrawMoney'] = $this->newwallet_model->getWithDraw($this->uid); //账户可提现金额
        $vdata['isTodayWithdraw'] = $this->isTodayWithdraw(); // 今日申请提款
        $vdata['isFreeze'] = $this->isFreeze(); // 账户冻结
        $vdata['bankInfo'] = $this->bankInfo;  //绑定银行卡信息
        $return = false;
        if ($this->is_ajax) {
            if (empty($this->uinfo['phone']) || empty($this->uinfo['id_card']))
            {
                echo 10;
                exit;             
            }
            if(empty($this->bankInfo))
            {
                echo 11;
                exit; 
            }
            if ($vdata['isTodayWithdraw']) 
            {
                echo 2; // 今天已提款
                exit;
            }

            if ($vdata['isFreeze']) 
            {
                echo 3; // 账户冻结
                exit;
            }
            $action = $this->input->post('action');
            if (!empty($action)) {
                switch ($action) {
                    case '_1':
                        $withdraw = $this->input->post('withdraw', true);   //提现金额
                        $real_name = $this->input->post('real_name', true);   
                        $bank_id = $this->input->post('bank_id', true);
                        if ($real_name !== $this->uinfo['real_name']) {
                            exit('12');
                        }
                        $flag = true;
                        $card_id = '';
                        foreach ($this->bankInfo as $bankInfo) {
                            if ($bankInfo['id'] == $bank_id) {
                                $flag = false;
                                $card_id = $bankInfo['bank_id'];
                                $bank_type = $bankInfo['bank_type'];
                            }
                        }
                        if ($flag) {
                            exit('13');
                        }
                        if (preg_match('/\d+(\.\d{1,2}){0,1}/', $withdraw, $matches)) {
                            if (str_replace($matches[0], '', $withdraw) !== '') {
                                exit('9');
                            }else {
                                $withdraw = intval(ParseUnit($withdraw));
                                if($withdraw >= 1 && checkMoney($withdraw))
                                {
                                    if($withdraw < 1000) exit('14');
                                    if ($vdata['withDrawMoney'] >= $withdraw) {
                                        if(!empty($card_id))
                                        {
                                            $additions = '0@'.$bank_type.'@'.$card_id;
                                            $extData['channel'] = $this->getChannelId();
                                            $return = $this->newwallet_model->setWithDraw($withdraw, $this->uid, 0, $additions, $extData);
                                            if ($return) 
                                            {
                                                echo $this->load->view("v1.1/wallet/withdraw_2", $vdata, true);
                                            } 
                                            else 
                                            {
                                                    echo 7; // 保存出错
                                            }
                                        }
                                    } else {
                                        exit('4'); // 提款金额不在合理范围
                                    }
                                }else {
                                    exit('14');
                                }
                            }
                        }else {
                            exit('9');
                        }
                        break;

                    case '_2':
                        $captcha = intval($this->input->post('captcha', true));
                        $withdraw = $this->input->post('withdraw', true);
                        $codestr = $this->primarysession->getArg('phoneCaptcha');
                        $bankId = $this->input->post('bank_id', true);
                        if ( !empty($codestr) ) {
                            if( $withdraw >=1 && checkMoney($withdraw) && $vdata['withDrawMoney'] >= $withdraw)
                            {
                                $res = $this->checkCaptcha($captcha, $this->uinfo['phone'], 'phoneCaptcha');
                                if ($res === 2) {
                                    echo 61;
                                }elseif ($res) {
                                    echo 6;
                                }else {
                                    //获取银行卡信息
                                    if(!empty($vdata['bankInfo']))
                                    {
                                        $card_id = '';
                                        foreach ($vdata['bankInfo'] as $banks) {
                                            if($banks['id'] == $bankId)
                                            {
                                                $card_id = $banks['bank_id'];
                                                $bank_type = $banks['bank_type'];
                                            }
                                        }
                                        if(!empty($card_id))
                                        {
                                            $additions = '0@'.$bank_type.'@'.$card_id;
                                            $extData['channel'] = $this->getChannelId();
                                            $return = $this->newwallet_model->setWithDraw($withdraw, $this->uid, 0, $additions, $extData);
                                            if ($return) {
                                                $this->primarysession->setArg('phoneCaptcha', '');
                                                echo $this->load->view("v1.1/wallet/withdraw_2", $vdata, true);
                                            } else {
                                                echo 7; // 保存出错
                                            }
                                        }else{
                                            echo 9; //银行卡不存在或已删除
                                        }
                                    }else{
                                        echo 9; //银行卡不存在或已删除
                                    }
                                }
                            }
                            else 
                            {
                                echo 8; //数据不合法
                            }
                        } else {
                            echo 5; // 验证码为空或数据不合法
                        }
                        break;

                    default:
                        break;
                }
            }
        } else {
            $vdata['rfshbind'] = 1;
            $this->display('wallet/withdraw', $vdata, 'v1.1');
        }
    }

    /**
     * [isTodayWithdraw 是否当日已提交提款申请]
     * @author LiKangJian 2017-06-20
     * @return boolean [description]
     */
    private function isTodayWithdraw() 
    {
        $withdrawLog = $this->newwallet_model->getWithdrawLog($this->uid);
        if (!empty($withdrawLog)) {
            return true;
        } else {
            return false;
        }
    }
}


