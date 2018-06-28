<?php

class Newuserhelp {
    private $CI;
    public function __construct()
    {
        $this->CI = &get_instance();
    }

    public function get_lottery_help(){
        //加载配置信息
        $this->CI->config->load('user_help'); 
        $data = $this->CI->config->item('lottery_help');
        $lottery_type = $this->CI->con.'_'.$this->CI->act;

        switch ($lottery_type) {
            case 'main_index':
                $this->CI->load->view('newuser/index');
                break;
            case 'ssq_index':
                $this->CI->load->view('newuser/help_number',$data['number']['ssq']);
                break;
            case 'dlt_index':
                $this->CI->load->view('newuser/help_number',$data['number']['dlt']);
                break;
            case 'syxw_index':
                $this->CI->load->view('newuser/help_number',$data['number']['syxw']);
                break;
            case 'syxw_rx5':
                $this->CI->load->view('newuser/help_number',$data['number']['syxw']);
                break;
            case 'jclq_index':
                $this->CI->load->view('newuser/help_jclq',$data['jclq']['hh']);
                break;
            case 'jclq_hh':
                $this->CI->load->view('newuser/help_jclq',$data['jclq']['hh']);
                break;
            case 'jclq_sf':
                $this->CI->load->view('newuser/help_jclq',$data['jclq']['sf']);
                break;
            case 'jclq_rfsf':
                $this->CI->load->view('newuser/help_jclq',$data['jclq']['rfsf']);
                break;
            case 'jclq_dxf':
                $this->CI->load->view('newuser/help_jclq',$data['jclq']['dxf']);
                break;
            case 'jclq_sfc':
                $this->CI->load->view('newuser/help_jclq',$data['jclq']['sfc']);
                break;
            case 'fcsd_index':
                $this->CI->load->view('newuser/help_number',$data['number']['zx']);
                break;
            case 'fcsd_zx':
                $this->CI->load->view('newuser/help_number',$data['number']['zx']);
                break;
            case 'fcsd_z3':
                $this->CI->load->view('newuser/help_number',$data['number']['z3']);
                break;
            case 'fcsd_z6':
                $this->CI->load->view('newuser/help_number',$data['number']['z6']);
                break;
            case 'qlc_index':
                $this->CI->load->view('newuser/help_number',$data['number']['qlc']);
                break;
            case 'qxc_index':
                $this->CI->load->view('newuser/help_number',$data['number']['qxc']);
                break;
            case 'plw_index':
                $this->CI->load->view('newuser/help_number',$data['number']['plw']);
                break;
            case 'pls_index':
                $this->CI->load->view('newuser/help_number',$data['number']['plszx']);
                break;
            case 'pls_zx':
                $this->CI->load->view('newuser/help_number',$data['number']['plszx']);
                break;
            case 'pls_z3':
                $this->CI->load->view('newuser/help_number',$data['number']['plsz3']);
                break;
            case 'pls_z6':
                $this->CI->load->view('newuser/help_number',$data['number']['plsz6']);
                break;
            case 'sfc_index':
                $this->CI->load->view('newuser/help_jjc',$data['jjc']['sfc']);
                break;
            case 'rj_index':
                $this->CI->load->view('newuser/help_jjc',$data['jjc']['rj']);
                break;
            case 'jczq_index':
                $this->CI->load->view('newuser/help_jczq',$data['jczq']['hh']);
                break;
            case 'jczq_hh':
                $this->CI->load->view('newuser/help_jczq',$data['jczq']['hh']);
                break;
            case 'jczq_bqc':
                $this->CI->load->view('newuser/help_jczq',$data['jczq']['bqc']);
                break;
            case 'jczq_bqc':
                $this->CI->load->view('newuser/help_jczq',$data['jczq']['bqc']);
                break;
            case 'jczq_cbf':
                $this->CI->load->view('newuser/help_jczq',$data['jczq']['cbf']);
                break;
            case 'jczq_jqs':
                $this->CI->load->view('newuser/help_jczq',$data['jczq']['jqs']);
                break;
            case 'jczq_rqspf':
                $this->CI->load->view('newuser/help_jczq',$data['jczq']['rqspf']);
                break;
            case 'jczq_spf':
                $this->CI->load->view('newuser/help_jczq',$data['jczq']['spf']);
                break;
            default:
                # code...
                break;
        }
    }   
}

    