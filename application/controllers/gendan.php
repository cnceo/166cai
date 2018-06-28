<?php

class Gendan extends MY_Controller
{

    public function __construct()
    {
        parent::__construct();
    }
       
    public function index()
    {
        $this->gendan(0);
    }

    public function gendan($lid)
    {
        $page = intval($this->input->get("cpage", true));
        $page = $page <= 1 ? 1 : $page;
        $data = array();
        $data['order'] = $this->input->post("order", true) ? $this->input->post("order", true) : '20';
        $data['nickname'] = $this->input->post("nickname", true);
        $data['type'] = $this->input->post("type", true) ? $this->input->post("type", true) : '0';        
        $offset = ($page - 1) * 30;
        $this->load->model('united_planner_model');
        $datas = $this->united_planner_model->getAllUser($lid, $data, $offset, 30);
        if (!$this->is_ajax) {
            $data['allInfo'] = $this->united_planner_model->getGenDanInfo();
        }
        $data['allUser'] = $datas[1];
        $data['allNum'] = $datas[0]['count'];
        $data ['lottery'] = array('51' => '双色球', '23529' => '大乐透', '42' => '竞彩足球', '43' => '竞彩篮球', '11' => '胜负/任九', '19' => '任选九', '52' => '福彩3D', '23528' => '七乐彩', '10022' => '七星彩', '33' => '排列三/五', '35' => '排列五');
        $data['lidName'] = ($lid == 0) ? "跟单大厅" : $data ['lottery'][$lid];
        $data['pages'] = $this->load->view('v1.1/elements/common/pages', array('pagenum' => ceil($datas[0]['count'] / 30), 'ajaxform' => 'user_form'), true);
        $data['hasgendan'] = array();
        if ($this->uid)
        {
            $this->load->model('follow_order_model');
            $allGendans = $this->follow_order_model->getHasGendan($this->uid);
            $has = array();
            foreach ($allGendans as $gendan) {
                $has[] = $gendan['puid'] . ',' . $gendan['lid'];
            }
            foreach ($allGendans as $gendan) {                
                if ($gendan['lid'] == 11) {
                    if (!in_array($gendan['puid'] . ',11', $has) || !in_array($gendan['puid'] . ',19', $has)) {
                        $key = array_search($gendan['puid'] . ',11', $has);
                        if ($key || $key===0) array_splice($has, $key, 1);
                    }
                }
                if ($gendan['lid'] == 33) {
                    if (!in_array($gendan['puid'] . ',33', $has) || !in_array($gendan['puid'] . ',35', $has)) {
                        $key = array_search($gendan['puid'] . ',33', $has);
                        if($key || $key===0)array_splice($has, $key, 1);
                    }
                }
            }
            $data['hasgendan'] = $has;
        }
        if ($this->is_ajax)
        {
            echo $this->load->view('v1.1/gendan/index', $data, true);
        }
        else
        {
            $this->display('gendan/index', $data, 'v1.1');
        }        
    }
    
    public function ssq()
    {
        $this->gendan(SSQ);
    }
    
    public function dlt()
    {
        $this->gendan(DLT);
    }    
    
    public function jczq()
    {
        $this->gendan(JCZQ);
    }
    
    public function jclq()
    {
        $this->gendan(JCLQ);
    }  
    
    public function sfc()
    {
        $this->gendan(SFC);
    }      
    
    public function fcsd()
    {
        $this->gendan(FCSD);
    }          
    
    public function qlc()
    {
        $this->gendan(QLC);
    }      
    
    public function qxc()
    {
        $this->gendan(QXC);
    }        
    
    public function pls()
    {
        $this->gendan(PLS);
    }        
}
