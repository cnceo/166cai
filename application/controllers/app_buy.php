<?php

class App_buy extends MY_Controller {

    public function __construct()
    {
       parent::__construct();
    }

    public function index() 
    {
    	$this->redirect('/activity/newmode');
    }
    
    public function sendSms()
    {
        $this->load->model('user_model');
        $ok = true;
        $msg = "";
        $uid = $this->input->post('uid', TRUE);
        $vdata = array(
        );
        $type = 'app_download';//app_download的type为4
        $tel_num = $this->input->post('tel_num', TRUE);
        $uip = UCIP;
//         if(!preg_match('/^[1][3-8]+\d{9}/', $tel_num) || strlen($tel_num) != 11)
//         {
//             $ok = false;
//             $msg = "请输入正确手机号码！" ;
//         }
//         elseif($this->user_model->isOldIp($uip))
//         {
//             $ok = false;
//             $msg = "发送太频繁，请使用其他方式下载！" ;
//         }
//         elseif($this->user_model->isThreeTimes($tel_num,$type))
//         {
//             $ok = false;
//             $msg = "每手机号码单日仅可发送三次！请使用其他方式下载！" ;
//         }
//         elseif ($this->user_model->isInFiveMinute($tel_num))
//         {
//             $ok = false;
//             $msg = "发送太频繁，请稍后再试！" ;
//         }
//         else
//         {
            $this->user_model->sendSms($uid, $vdata, $type, $tel_num, $uip, '195');
//         }
        echo json_encode(compact('ok', 'msg'));
    }
}
