<?php
/**
 * Copyright (c) 2017,上海快猫文化传媒有限公司.
 * 摘    要: 胜负彩 单式上传
 * 作    者: 李康建
 * 修改日期: 2017/07/27
 * 修改时间: 09:35
 */

class Rj
{
  	private $lotteryId = '19';
  	private $flag = 'rj';
    private $playType = array('1'=>'1:1');
  	public function __construct()
  	{
  		$this->CI = &get_instance();
  		$this->CI->load->model('tmp_code_model');
  	}
  	/**
  	 * [check 校验 #,1,1,#,0,0,#,0,#,3,0,1,3,#:1:1]
  	 * @author LiKangJian 2017-07-27
  	 * @param  [type] $content [description]
  	 * @return [type]          [description]
  	 */
  	public function check($content,$playType)
  	{
        $data = array();
        $res = array('code'=>0,'msg'=>'error','data'=>array());
        foreach ($content as $k => $v) 
        {
            $str = trim($v);
            $str = str_replace("*","#",$str);
            if(!preg_match('/^[013\#]{14}$/', $str))
            {
                $res['msg'] = '文件格式有误，请修改后重新上传';
                return $res;
            }
            //必须多余9个球
            preg_match_all("/(\d)/",$v,$num);
            if(count($num[0])!=9)
            {
                $res['msg'] = '文件格式有误，请修改后重新上传';
                return $res;
            }
            $i = 0;
            $arr = array();
            while( isset($str{$i}) ) 
            {
             array_push($arr, $str{$i});
             $i++;
            }
            array_push($data, implode(',', $arr ).':'.$this->playType[$playType]);
        }

        $res['code'] = 1;
        $betTnum = count($data);
        $res['data'] = array('betTnum'=>$betTnum,'money'=>$betTnum*2,'codes'=>implode(';', $data));
        $res['msg'] = 'success';
        return $res;
  	}
}