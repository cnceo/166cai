<?php
/**
 * Copyright (c) 2017,上海快猫文化传媒有限公司.
 * 摘    要: 双色球 单式上传
 * 作    者: 李康建
 * 修改日期: 2017/07/27
 * 修改时间: 09:35
 */

class Ssq
{
	private $lotteryId = '51';
	private $flag = 'ssq';
  private $playType = array('1'=>'1:1');
	public function __construct()
	{
		$this->CI = &get_instance();
		$this->CI->load->model('tmp_code_model');
	}
	/**
	 * [check 校验]
	 * @author LiKangJian 2017-07-27
	 * @param  [type] $content [description]
	 * @return [type]          [description]
	 */
	public function check($content,$playType)
	{
       $data = array();
       $res = array('code'=>0,'msg'=>'error','data'=>array());
       $leftBall  = $this->createBall(33);
       $rightBall  = $this->createBall(16);
       foreach ($content as $k => $v) 
       {
           $regex = "/[+:\|]/";
           $num_matches = preg_match($regex, $v);
           if($num_matches!=1)
           {
                $res['msg'] = '文件格式有误，请修改后重新上传';
                return $res;
           }
           $v = preg_replace($regex,'|',$v);
           //拆分
           $arr = explode('|', $v);
           //空格替换成,号
           $arr[0] = preg_replace("/[\s\.]/",',',trim($arr[0]));
           $arr[1] = trim($arr[1]);
           $temp_arr0 = explode(',', $arr[0]);
           if( count( array_intersect($leftBall,$temp_arr0) ) != 6 || count($temp_arr0)!=6 || !in_array($arr[1],$rightBall) || count($arr) !=2)
           {
                $res['msg'] = '文件格式有误，请修改后重新上传';
                return $res;
           }
           //排序 
           sort($temp_arr0);
           array_push($data, implode(',', $temp_arr0).'|'.$arr[1].':'.$this->playType[$playType]);
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