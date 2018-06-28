<?php
/**
 * Copyright (c) 2017,上海快猫文化传媒有限公司.
 * 摘    要: 排列3 单式上传
 * 作    者: 李康建
 * 修改日期: 2017/07/27
 * 修改时间: 09:35
 */

class Fcsd
{
  	private $lotteryId = '52';
  	private $flag = 'fc3d';
    private $playType = array(
                    '1'=>'1:1', //直选玩法
                    '2'=>'2:1', //组三玩法
                    '3'=>'3:3'  //组六玩法
                    );
  	public function __construct()
  	{
  		$this->CI = &get_instance();
  		$this->CI->load->model('tmp_code_model');
  	}
  	/**
     * [check 校验 2,2,3:1:1;0,7,7:2:1;1,7,9:3:3 ]
     * @author LiKangJian 2017-07-27
     * @param  [type] $content  [description]
     * @param  [type] $playType [description]
     * @return [type]           [description]
     */
  	public function check($content,$playType)
  	{
        $data = array();
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
            if(sizeof($num)!=3)
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
            //对应组三
            if($playType==2 && count(array_unique($num))!=2)
            {
                $res['msg'] = '文件格式有误，请修改后重新上传';
                return $res;
            }
            //对应组三
            if($playType==3 && count(array_unique($num))!=3)
            {
                $res['msg'] = '文件格式有误，请修改后重新上传';
                return $res;
            }
            //排序
            if($playType==2 || $playType ==3)
            {
              sort($num);
            }
            //验证球单位与2位
            array_push($data, implode(',', $num ).':'.$this->playType[$playType]);
        }
        $res['code'] = 1;
        $betTnum = count($data);
        $res['data'] = array('betTnum'=>$betTnum,'money'=>$betTnum*2,'codes'=>implode(';', $data));
        $res['msg'] = 'success';
        return $res;
  	}
    /**
     * [writeCodes 写入临时表]
     * @author LiKangJian 2017-07-26
     * @param  [type] $codes [description]
     * @return [type]        [description]
     */
    private function writeCodes($codes)
    {
       
        $tmp_id = $this->CI->tmp_code_model->writeCodes($codes);
        return $tmp_id;
    }
}