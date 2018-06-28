<?php
/**
 * Copyright (c) 2017,上海快猫文化传媒有限公司.
 * 摘    要: 排列5 单式上传
 * 作    者: 李康建
 * 修改日期: 2017/07/27
 * 修改时间: 09:35
 */

class Plw
{
  	private $lotteryId = '35';
  	private $flag = 'plw';
    private $playType = array('1'=>'1:1');
  	public function __construct()
  	{
  		$this->CI = &get_instance();
  		$this->CI->load->model('tmp_code_model');
  	}
  	/**
  	 * [check 校验 4,8,2,2,3:1:1]
  	 * @author LiKangJian 2017-07-27
  	 * @param  [type] $content [description]
  	 * @return [type]          [description]
  	 */
  	public function check($content,$playType)
  	{
        $data = array();
        $ball = $this->createBall(9,false);
        $res = array('code'=>0,'msg'=>'error','data'=>array());
        foreach ($content as $k => $v) 
        {
            $str = trim($v);
            $arr = array();
            $num = array();
            $i = 0;
            while( isset($str{$i}) ) 
            {
             array_push($arr, $str{$i});
             if(preg_match('/^[0-9]*$/', $str{$i}))
             {
                array_push($num, $str{$i});
             }
             $i++;
            }
            if(sizeof($num)!=5)
            {
                $res['msg'] = '文件格式有误，请修改后重新上传';
                return $res;
            }
            foreach ($arr as $k1 => $v1) 
            {
              //同时出现逗号和空格说明格式不正确
              if( ($v1 ===' ' || $v1 ===',') && ($arr[$k1+1] ===' ' || $arr[$k1+1] ===',') )
              {
                $res['msg'] = '文件格式有误，请修改后重新上传';
                return $res;
              }
              //含数字 空格 ，以外数字都是错误
              if(!preg_match('/^[0-9]*$/', $v1) && $v1!=' ' && $v1!=',')
              {
                $res['msg'] = '文件格式有误，请修改后重新上传';
                return $res;
              }
            }
            array_push($data, implode(',', $num ).':'.$this->playType[$playType]);
        }
        $res['code'] = 1;
        $betTnum = count($data);
        $res['data'] = array('betTnum'=>$betTnum,'money'=>$betTnum*2,'codes'=>implode(';', $data));
        $res['msg'] = 'success';
        return $res;
  	}
    /**
     * [createBall 生成球]
     * @author LiKangJian 2017-07-26
     * @param  [type] $max [description]
     * @return [type]      [description]
     */
    private function createBall($max,$tag = true)
    {
        $return = array();
        $i = $tag ? 1 : 0;
        do {
           $str = $i; 
           if($i<10&&$tag)
           {
            $str = '0'.$i;
           }
           array_push($return, $str);
           $i++;
        } while ($i <= $max);
        return $return;
    }
}