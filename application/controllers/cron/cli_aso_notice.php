<?php

/**
 * ASO回调通知
 * @date:2017-11-10
 */

defined('BASEPATH') OR exit('No direct script access allowed');

class Cli_Aso_Notice extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->library('tools');
        $this->load->model('api_aso_model');
    }

    public function index()
    {
        $info = $this->api_aso_model->getAsoCallback();
        
        while(!empty($info)) 
        {
            foreach ($info as $detail) 
            {
                $req = array('idfa' => $detail['idfa']);
                $response = $this->tools->request($detail['callback'], $req);
                $response = json_decode($response, true);

                $data = array(
                    'idfa'      =>  $detail['idfa'],
                    'cstate'    =>  (!empty($response)) ? '4' : '2',
                );

                $this->api_aso_model->updateNotice($data);
            }
            $info = $this->api_aso_model->getAsoCallback();
        }
    }
}