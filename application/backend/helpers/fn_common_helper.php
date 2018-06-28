<?php

/**
 * Copyright (c) 2014,上海二三四五网络科技股份有限公司
 * 摘    要：公共帮助方法
 * 作    者：wangl@2345.com
 * 修改日期：2014.11.06
 */
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

define('MANAGER_ENCODE_KEY', "9#9F91%23R45rHuT=+i.cEhuDa4n3g.0");
/**
 * 参    数：$isVarish 是否使用varish代理
 * 作    者：wangl
 * 功    能：获取客户端IP
 * 修改日期：2014.11.05
 */
if (!function_exists('get_client_ip'))
{
	function get_client_ip()
	{
	    //代理IP白名单
	    $allowProxys = array(
	        '42.62.31.40',
	        '172.16.0.40',
	    );
	    $onlineip = $_SERVER['REMOTE_ADDR'];
	    if (in_array($onlineip, $allowProxys))
	    {
	        $ips = $_SERVER['HTTP_X_FORWARDED_FOR'];
	        if ($ips)
	        {
	            $ips = explode(",", $ips);
	            $curIP = array_pop($ips);
	            $onlineip = trim($curIP);
	        }
	    }
	    if (filter_var($onlineip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4))
	    {
	        return $onlineip;
	    }
	    else
	    {
	        return '0.0.0.0';
	    }
	}
}
/**
 * 参    数：$array 数组
 *                $in 输入编码
 *                $out 输出编码
 * 作    者：wangl
 * 功    能：数组转换编码
 * 修改日期：2014.11.05
 */
if (!function_exists('array_my_icov'))
{

    function array_my_icov($array, $in = "GBK", $out = "UTF-8")
    {
        $temparray = array();
        foreach ($array as $key => $value)
        {
            $key = mb_convert_encoding($key, $out, $in);
            if (!is_array($value))
            {
                if (!is_int($value))
                {
                    $value = mb_convert_encoding($value, $out, $in);
                }
            }
            else
            {
                $value = array_my_icov($value, $in, $out);
            }

            $temparray[$key] = $value;
        }

        return $temparray;
    }

}
/**
 * 参    数：$pageConfig 分页配置
 * 作    者：wangl
 * 功    能：分页函数
 * 修改日期：2014.11.05
 */
if (!function_exists('get_pagination'))
{

    function get_pagination($pageConfig)
    {
        $reload = isset($pageConfig['reload']) ? $pageConfig['reload'] : get_script_url();
        $page = $pageConfig['page'];
        $numPerPage = $pageConfig['npp'];
        $allcount = $pageConfig['allCount'];
        $tpages = ceil($pageConfig['allCount'] / $numPerPage);
        $pageCount = $tpages > 1 ? $numPerPage : $allcount;
        $adjacents = isset($pageConfig['showBlocks']) ? $pageConfig['showBlocks'] : 2;
        $query = isset($pageConfig['query']) ? $pageConfig['query'] : $_SERVER['QUERY_STRING'];
        $reload = str_replace('?' . $query, '', $reload);
        if (!empty($query))
        {
            parse_str($query, $get);
            if (isset($get['p']))
            {
                if ($get['p'] == $tpages) 
                {
                    $pageCount = $allcount - ($tpages - 1) * $numPerPage;  
                }
                unset($get['p']);
            }

            if (!empty($get))
            {
                $query = "&" . htmlentities(http_build_query($get));
            }
            else
            {
                $query = "";
            }
        }
        if ($tpages < 2)
        {
            $out = "";
        }
        else
        {
            $firstlabel = "首页";
            $lastlabel = "尾页";
            $prevlabel = "上一页";
            $nextlabel = "下一页";

            //$out = "<div class=\"pagin\">\n";
            $out = "<a href=\"" . $reload . "?p=1$query\">" . $firstlabel . "</a>\n";
            // previous
            if ($page == 2 || $page == 1)
            {
                $out .= "<a href=\"" . $reload . "?p=1$query\">" . $prevlabel . "</a>\n";
            }
            else
            {
                $out .= "<a href=\"" . $reload . "?p=" . ($page - 1) . "$query\">" . $prevlabel . "</a>\n";
            }

            // first
            if ($page > ($adjacents + 1))
            {
                $out .= "<a href=\"" . $reload . "?p=1$query\">1</a>\n";
            }

            // interval
            if ($page > ($adjacents + 2))
            {
                $out .= '<span class="pipe">...</span>';
            }

            // pages
            $pmin = ($page > $adjacents) ? ($page - $adjacents) : 1;
            $pmax = ($page < ($tpages - $adjacents)) ? ($page + $adjacents) : $tpages;
            for ($i = $pmin; $i <= $pmax; $i++)
            {
                if ($i == $page)
                {
                    $out .= "<a class=\"cur\">" . $i . "</a>\n";
                }
                elseif ($i == 1)
                {
                    $out .= "<a href=\"" . $reload . "?p=1$query\">" . $i . "</a>\n";
                }
                else
                {
                    $out .= "<a href=\"" . $reload . "?p=" . $i . "$query\">" . $i . "</a>\n";
                }
            }

            // interval
            if ($page < ($tpages - $adjacents - 1))
            {
                $out .= '<span class="pipe">...</span>';
            }

            // last
            if ($page < ($tpages - $adjacents))
            {
                $out .= "<a href=\"" . $reload . "?p=" . $tpages . "$query\">" . $tpages . "</a>\n";
            }

            // next
            if ($page < $tpages)
            {
                $out .= "<a href=\"" . $reload . "?p=" . ($page + 1) . "$query\">" . $nextlabel . "</a>\n";
            }
            else
            {
                $out .= "<a href=\"" . $reload . "?p=" . $tpages . "$query\">" . $nextlabel . "</a>\n";
            }

            $out .= "<a href=\"" . $reload . "?p=" . $tpages . "$query\">" . $lastlabel . "</a>\n";
        }

        $return_array = array(
            $out,
            $tpages,
            $pageCount,
            $allcount
        );
        return $return_array;
    }

}
/**
 * 参    数：$str 字符串
 * 作    者：wangl
 * 功    能：htmlspecialchars封装
 * 修改日期：2014.11.05
 */
if (!function_exists('str_hsc'))
{

    function str_hsc($str)
    {
        return trim(htmlspecialchars($str, ENT_QUOTES, "ISO-8859-1"));
    }

}

/**
 * 参    数：$str 字符串
 * 作    者：wangl
 * 功    能：金额格式话
 * 修改日期：2014.11.05
 */
if (!function_exists('m_format'))
{

    function m_format($str)
    {
        $str = $str / 100;
        return number_format($str, 2, ".", ",");
    }

}
if (!function_exists('format_rand'))
{

    function format_rand($num)
    {
        return sprintf("%.2f",$num);
    }

}
if (!function_exists('format_rand_percent'))
{

    function format_rand_percent($num)
    {
        return sprintf("%.2f",$num*100);
    }

}
if (!function_exists('rand_str'))
{

    function rand_str($length) 
    { 
        // 密码字符集，可任意添加你需要的字符 
        $chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789'; 
        $str = '';
        for ( $i = 0; $i < $length; $i++ ) 
        { 
         $str .= $chars[ mt_rand(0, strlen($chars) - 1) ]; 
        } 
        return $str; 
    }

}
if(! function_exists('ParseUnit'))
{
    /*
     * $io 0=乘 1=除*/
    function ParseUnit($val, $io=0)
    {
        $unit = 1000;
        if($io)
        {
            $val = (doubleval($val) / $unit) * 10;
        }else 
        {
            $val = (doubleval($val) * $unit) / 10;
        }
        return $val;
    }
}

/**
 * 参    数：$str 字符串
 * 作    者：liuli
 * 功    能：除法运算格式
 * 修改日期：2015.08.10
 */
if(! function_exists('Division'))
{
    /*
     * $io 0=乘 1=除*/
    function Division($fz, $fm, $flag = 0)
    {
        if($fm <= 0)
        {
            if($flag)
            {
                return '--';
            }
            else
            {
                return 0;
            }
        }
        else
        {
            return ($fz / $fm) ; 
        }
    }
}

if(! function_exists('Precent'))
{
    function Precent($num)
    {
        return number_format($num * 100, 2) . '%';
    }
}

//返回北京单场状态描述
if (!function_exists('getBjdcStatus'))
{
	function getBjdcStatus($time, $status, $state)
	{
		$nowTime = date('Y-m-d H:i:s');
		if($time > $nowTime)
		{
			$return = '在售';
		}
		else
		{
			if($status == 50 && $state == 1) 
			{
				$return = '结期';
			}
			else
			{
				$return = '截止';
			}
		}
		return $return;
	}
}

//返回老足彩状态描述
if (!function_exists('getTczqStatus'))
{
	function getTczqStatus($time, $status)
	{
		$time = strtotime($time);
		$nowTime = strtotime("now");
		if($time > $nowTime)
		{
			$return = '在售';
		}
		else
		{
			if(in_array($status, array(50,51)))
			{
				$return = '结期';
			}
			else
			{
				$return = '截止';
			}
		}
		return $return;
	}
}

//返回竞彩篮球、足球状态描述
if (!function_exists('getJcStatus'))
{
	function getJcStatus($time, $status, $aduitflag = 0)
	{
		$nowTime = date('Y-m-d H:i:s');
		if($time > $nowTime)
		{
			$return = '在售';
		}
		else
		{
			if($status == 50 && $aduitflag > 0)
			{
				$return = '结期';
			}
			else
			{
				$return = '截止';
			}
		}
		return $return;
	}
}

if (!function_exists('get_script_url'))
{
    function get_script_url() 
    {
        $script_url = null;
     
        if (!empty($_SERVER['SCRIPT_URL']))   
            $script_url = $_SERVER['SCRIPT_URL'];
     
        elseif (!empty($_SERVER['REDIRECT_URL'])) 
            $script_url = $_SERVER['REDIRECT_URL'];
     
        elseif (!empty($_SERVER['REQUEST_URI'])) {
            $p = parse_url($_SERVER['REQUEST_URI']);
            $script_url = $p['path'];
        }
     
        $_SERVER['SCRIPT_URL'] = $script_url;
     
        return $script_url;
     
    }
}

if (!function_exists('dd'))
{
    function dd($arr) 
    {
        echo '<pre>';
        is_array($arr) ? print_r($arr) : var_dump($arr);
        echo '</pre>';
        exit(); 
    }
}
if (!function_exists('return_page'))
{
    function return_page($msg, $gourl, $onlymsg = 0, $limittime = 0)
    {
        /*
        *$msg 信息提示的内容
        *$gourl 需要跳转的网址
        *$onlymsg 1 表示不自动跳转 0表示自动跳转
        *$limittime 跳转的时间
        */
        $cfg_ver_lang = 'utf-8';
        $htmlhead = "<html>\r\n<head>\r\n<title>温馨提示</title>\r\n<meta http-equiv=\"Content-Type\" content=\"text/html; charset={$cfg_ver_lang}\" />\r\n";
        $htmlhead .= "<base target='_self'/>\r\n</head>\r\n<body leftmargin='0' topmargin='0'>\r\n<center>\r\n<script>\r\n";
        $htmlfoot = "</script>\r\n</center>\r\n</body>\r\n</html>\r\n";
        if ($limittime == 0)
        $litime = 2000;
        else
        $litime = $limittime;
        if ($gourl == "-1") {
        if ($limittime == 0)
          $litime = 2000;
        $gourl = "javascript:history.go(-1);";
        }
        if ($gourl == "" || $onlymsg == 1) {
        $msg = "<script>alert(\"" . str_replace ( "\"", "“", $msg ) . "\");</script>";
        } else {
        $func = "  var pgo=0;
        function JumpUrl(){
        if(pgo==0){ location='$gourl'; pgo=1; }
        }\r\n";
        $rmsg = $func;
        $rmsg .= "document.write(\"<br/><div style='width:400px;padding-top:4px;height:24;font-size:10pt;border-left:1px solid #999999;border-top:1px solid #999999;border-right:1px solid #999999;background-color:#CCC;'>温馨提示：</div>\");\r\n";
        $rmsg .= "document.write(\"<div style='width:400px;height:100;font-size:10pt;border:1px solid #999999;background-color:#f9fcf3'><br/><br/>\");\r\n";
        $rmsg .= "document.write(\"" . str_replace ( "\"", "“", $msg ) . "\");\r\n";
        $rmsg .= "document.write(\"";
        if ($onlymsg == 0) {
          $rmsg .= "<br/><br/></div>\");\r\n";
          if ($gourl != "javascript:;" && $gourl != "") {
            $rmsg .= "setTimeout('JumpUrl()',$litime);";
          }
        } else {
          $rmsg .= "<br/><br/></div>\");\r\n";
        }
        $msg = $htmlhead . $rmsg . $htmlfoot;
        }
        echo $msg;
        exit();
    }
}

if (!function_exists('exportExcel'))
{
    /**
     * [exportExcel 导出]
     * @author LiKangJian 2017-05-02
     * @param  array  $title    [description]
     * @param  array  $data     [description]
     * @param  string $fileName [description]
     * @return [type]           [description]
     */
    function exportExcel($title = array(),$data = array(),$fileName='export')
    {
        header("Content-type:application/octet-stream");
        header("Accept-Ranges:bytes");
        header("Content-type:application/vnd.ms-excel");  
        header("Content-Disposition:attachment;filename=".$fileName.".xls");
        header('Cache-Control: max-age=0');
        header("Pragma: no-cache");
        header("Expires: 0");
        //循环表头
        if(count( $title ) >0 )
        {
            foreach ($title as $k => $v) 
            {
                if($k!= count( $title ) -1)
                {
                    echo mb_convert_encoding($v, "GBK", "UTF-8") . "\t";
                }else{
                    echo mb_convert_encoding($v, "GBK", "UTF-8") . "\t\n";
                }
                
            }
        }
        //循环表内容
        if(count( $data ) > 0)
        {
            foreach ($data as $k => $v) 
            {
                foreach ($v as $k1 => $v1) 
                {
                     if($k1!= count( $v ) -1)
                    {
                        echo mb_convert_encoding($v1, "GBK", "UTF-8") . "\t";
                    }else{
                        echo mb_convert_encoding($v1, "GBK", "UTF-8") . "\t\n";
                    } 
                }

            }
        }

    }
}