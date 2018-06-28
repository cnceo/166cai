<?php
/**
 * Copyright (c) 2017,上海快猫文化传媒有限公司.
 * 摘    要: 大乐透 单式上传
 * 作    者: 李康建
 * 修改日期: 2017/07/27
 * 修改时间: 09:35
 */

class Dlt
{
  	private $lotteryId = '51';
  	private $flag = 'dlt';
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
          $l_ball = $this->createBall(35);
          $r_ball = $this->createBall(12);
          $res = array('code'=>0,'msg'=>'error','data'=>array());
          foreach ($content as $k => $v) 
          {
              $v = trim($v);
              $regex = "/[+:\|]/";
              $regex1 = "/[,\.\|]/";
              preg_match_all($regex, $v,$preg_res);
              if(count($preg_res[0])!=1)
              {
                  $res['msg'] = '文件格式有误，请修改后重新上传';
                  return $res;
              }
              $v = preg_replace($regex,'|',$v);
              //拆分左右区 array_diff
              $arr = explode('|', $v);
              $arr[0] = explode(',', preg_replace("/[\s\.]/",',',trim($arr[0]))) ;
              $arr[1] = explode(',', preg_replace("/[\s\.]/",',',trim($arr[1]))) ;
              if(count($arr[0]) != 5 
                  || count($arr[1]) != 2 
                  || count(array_unique($arr[1])) != 2 
                  || count($arr) != 2
                  || count(array_unique($arr[0]))!=5
                  || count(array_diff($arr[0],array_intersect($arr[0],$l_ball))) != 0 
                  || count(array_diff($arr[1],array_intersect($arr[1],$r_ball))) != 0 
                  )
              {
                  $res['msg'] = '文件格式有误，请修改后重新上传';
                  return $res;
              }
              //排序
              sort($arr[0]);
              sort($arr[1]);
              array_push($data, implode(',', $arr[0]).'|'.implode(',', $arr[1]).':'.$this->playType[$playType]);
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