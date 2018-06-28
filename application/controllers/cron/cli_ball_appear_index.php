<?php

/**
 * Copyright (c) 2015,上海二三四五网络科技有限公司.
 * 摘    要:
 * 作    者: 刁寿钧
 * 修改日期: 2015/8/5
 * 修改时间: 10:46
 */

defined('BASEPATH') OR exit('No direct script access allowed');

class Cli_Ball_Appear_Index extends MY_Controller
{

    public function dailyUpdate()
    {
        $types = array('ssq', 'dlt', 'qlc', 'qxc', 'pl3', 'pl5', 'fc3d');
        foreach ($types as $type)
        {
            //$this->missed($type);
            $this->update($type);
        }
        //刷行双色球的开奖内存刷新
        $this->load->model('lottery_model', 'lottery');
        $this->lottery->frushKjHistory(Lottery_Model::SSQ);
        $this->lottery->frushKjHistory(Lottery_Model::DLT);
        $this->lottery->frushKjHistory(Lottery_Model::QLC);
        $this->lottery->frushKjHistory(Lottery_Model::QXC);
        $this->lottery->frushKjHistory(Lottery_Model::PLS);
        $this->lottery->frushKjHistory(Lottery_Model::FCSD);
        $this->lottery->frushKjHistory(Lottery_Model::PLW);
    }

    public function minuteUpdate()
    {
        $this->update('syxw');
        $this->update('jxsyxw');
        $this->missed('ks');
        $this->update('hbsyxw');
        $this->missed('klpk');
        // $types = array('syxw', 'jxsyxw','hbsyxw', 'ks','klpk');
        // foreach ($types as $type)
        // {
        //     $this->missed($type);
        // }   
        // 刷新最近100期的开奖信息
        $this->load->model('lottery_model', 'lottery');
        $this->lottery->refreshSyxwAwards();
        $this->lottery->refreshJxSyxwAwards();
        $this->lottery->refreshKsAwards();
        $this->lottery->refreshHbSyxwAwards();
        $this->lottery->refreshKlpkAwards();
    }

    public function initial()
    {
        $this->load->model('ball_appear_index_model', 'appearIndex');
        // $this->appearIndex->cleanTable();
        // $types = array('ssq', 'dlt', 'syxw');
        $types = array('hbsyxw');
        foreach ($types as $type)
        {
            $this->appearIndex->initialLottery($type);
        }
    }

    private function update($type)
    {
        $this->load->model('ball_appear_index_model', 'appearIndex');
        $this->appearIndex->updateLottery($type);
    }
    
    private function missed($type)
    {
    	$arr = array(
    		'ks' => 'Ks',
            'klpk' => 'Klpk'
    	);
    	if(isset($arr[$type]))
    	{
    		$this->load->library("missed/{$arr[$type]}");
    		$this->$type->exec();
    	}
    	
    }

}