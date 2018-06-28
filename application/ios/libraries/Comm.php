<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/*
 * IOS 通用处理函数
 * @date:2015-04-22
 */
class Comm
{

    public static $bankTypeList = array(
        '1025' => array(
            'name' => '工商银行',
            'dname' => '中国工商银行',
            'img' => 'bank-gs.png',
        ),
        '3080' => array(
            'name' => '招商银行',
            'dname' => '招商银行',
            'img' => 'bank-zs.png',
        ),
        '105' => array(
            'name' => '建设银行',
            'dname' => '中国建设银行',
            'img' => 'bank-js.png',
        ),
        '103' => array(
            'name' => '农业银行',
            'dname' => '中国农业银行',
            'img' => 'bank-ny.png',
        ),
        '104' => array(
            'name' => '中国银行',
            'dname' => '中国银行',
            'img' => 'bank-zg.png',
        ),
        '301' => array(
            'name' => '交通银行',
            'dname' => '交通银行',
            'img' => 'bank-jt.png',
        ),
        '307' => array(
            'name' => '平安银行',
            'dname' => '平安银行',
            'img' => 'bank-pa.png',
        ),
        '309' => array(
            'name' => '兴业银行',
            'dname' => '兴业银行',
            'img' => 'bank-xy.png',
        ),
        '311' => array(
            'name' => '华夏银行',
            'dname' => '华夏银行',
            'img' => 'bank-hx.png',
        ),
        '305' => array(
            'name' => '民生银行',
            'dname' => '中国民生银行',
            'img' => 'bank-ms.png',
        ),
        '306' => array(
            'name' => '广发银行',
            'dname' => '广发银行',
            'img' => 'bank-gf.png',
        ),
        '314' => array(
            'name' => '浦发银行',
            'dname' => '上海浦东发展银行',
            'img' => 'bank-pf.png',
        ),
        '313' => array(
            'name' => '中信银行',
            'dname' => '中信银行',
            'img' => 'bank-zx.png',
        ),
        '312' => array(
            'name' => '光大银行',
            'dname' => '中国光大银行',
            'img' => 'bank-gd.png',
        ),
        '316' => array(
            'name' => '南京银行',
            'dname' => '南京银行',
            'img' => 'bank-nj.png',
        ),
        '326' => array(
            'name' => '上海银行',
            'dname' => '上海银行',
            'img' => 'bank-sh.png',
        ),
        '3230' => array(
            'name' => '中国邮政储蓄',
            'dname' => '中国邮政储蓄银行',
            'img' => 'bank-yz.png',
        ),
    );
	
	/*
 	 * 使用特定function对数组中所有元素做处理
     * @date:2015-04-17
     */
	public function arrayRecursive(&$array, $function, $apply_to_keys_also = false)
    {
        static $recursive_counter = 0;
        if (++$recursive_counter > 1000) 
        {
            die('possible deep recursion attack');
        }
        foreach ($array as $key => $value) 
        {
            if (is_array($value)) 
            {
                $this->arrayRecursive($array[$key], $function, $apply_to_keys_also);
            } 
            else 
            {
                $array[$key] = $function($value);
            }
     
            if ($apply_to_keys_also && is_string($key)) 
            {
                $new_key = $function($key);
                if ($new_key != $key) 
                {
                    $array[$new_key] = $array[$key];
                    unset($array[$key]);
                }
            }
        }
        $recursive_counter--;
    }

    /*
     * 将数组转换为JSON字符串（兼容中文）
     * @date:2015-04-17
     */
    public function JSON($array) {
        $this->arrayRecursive($array, 'urlencode', true);
        $json = json_encode($array);
        return urldecode($json);
    }

    /*
     * 银行卡信息
     * @date:2015-04-27
     */
    public function BankInfo()
    {
        $bankTypeList = self::$bankTypeList;
       
        return $bankTypeList;
    }

    /*
     * 获取中文字符长度
     * @author:liuli
     * @date:2015-01-26
     */
    public function abslength($str){

        if(empty($str)){
            return 0;
        }

        if(function_exists('mb_strlen')){
            return mb_strlen($str,'utf-8');
        }else{
            preg_match_all("/./u", $str, $ar);
            return count($ar[0]);
        }

    }

    /*
     * 用户信息加密处理
     * @date:2015-01-12
     */
    public function auth_data($data, $authtype = 'sha1'){
        $sha1_key = 'caipiao2345check';
        ksort($data);
        $str = '';
        foreach ($data as $k => $v) {
            $str .= $k . $v;
        }
        $str .= $sha1_key;
        $data['sign'] = $authtype($str);
        return $data;
    }

    /*
     * 判断请求来源
     * @date:2015-04-23
     */
    public function ISAPP()
    {
        $userAgent = $_SERVER['HTTP_USER_AGENT'];
        return $userAgent;
    }
    
    /*
     * 获取当前时间戳 精确到毫秒
     * @date:2015-08-03
     */
    public function microTime()
    {
       return time() * 1000;
    }
    
    /*
     * 获取联赛类型
     * @date:2015-09-24
     */
    public function getNameType($name)
    {
        $nameType = 0;
        if(in_array($name, array('意甲','英超','西甲','德甲','法甲')))
        {
            $nameType = 1;
        }
        return $nameType;
    }

    /*
     * 阿拉伯数字转中文金额
     * @date:2016-01-26
     */
    public function NumToCNMoney($pool)
    {
        if(empty($pool))
        {
            return '更新中...';
        }

        // 取整
        $poolArry = explode('.', $pool);
        $pool = $poolArry[0];

        $unit = array('', '万', '亿');
        $tpl = "";
        if(is_numeric($pool) && !empty($pool))
        {
            $temp = str_split(strrev(floatval($pool)), 4);
            // 升序
            krsort($temp);
            if(isset($temp[2]))
            {
                $temp[0] = '0000';
            }
            foreach ($temp as $key => $items) 
            {
                if(!isset($unit[$key]))
                {
                    $tpl .= intval(strrev($items));
                }
                else
                {
                    $num = intval(strrev($items));
                    if(!empty($num) || $key == 2)
                    {
                        $str = $num . $unit[$key];
                        $tpl .= $str;
                    }             
                }
            }
        }
        else
        {
            $tpl .= 0;
        }
        if(count($temp) <= 2)
        {
            $tpl .= "元";
        }       
        return $tpl;
    }

    // 开奖号码形态
    public function getNumberPattern($lid, $numbers)
    {
        $pattern = array();

        if(in_array($lid, array('52', '33')))
        {
            $numbers = explode(',', $numbers);
            $count = count(array_unique(array_values($numbers)));

            if($count == 1)
            {
                $mark = '豹子';
            }
            elseif($count == 2)
            {
                $mark = '组三';
            }
            else
            {
                $mark = '组六';
            }

            $pattern[] = $mark;
        }
        elseif($lid == '55')
        {
            $numbers = explode(',', $numbers);
            // 十位
            $sw = ($numbers[3] <= 4) ? '小' : '大';
            $sw .= ($numbers[3] % 2 == 0) ? '双' : '单';
            $pattern[] = $sw;
            // 个位
            $gw = ($numbers[4] <= 4) ? '小' : '大';
            $gw .= ($numbers[4] % 2 == 0) ? '双' : '单';
            $pattern[] = $gw;
            // 后三组选
            $numbers = array_slice($numbers, 2);
            $count = count(array_unique(array_values($numbers)));

            if($count == 1)
            {
                $zs = '豹子';
            }
            elseif($count == 2)
            {
                $zs = '组三';
            }
            else
            {
                $zs = '组六';
            }
            $pattern[] = $zs;
        }
        return $pattern;
    }

    // 截止时间
    public function getTimeTran($time)
    {
        $msg = '';
        $time = $time - time();
        if($time > 0)
        {
            if($time > 60 * 60)
            {
                $h = floor($time / (60 * 60));
                $msg .= $h . '小时';
                $time = $time - (60 * 60 * $h);
            }
            // 近一取整
            $m = ceil($time / 60);
            $msg .= $m . '分';
        }
        return $msg;
    }
}