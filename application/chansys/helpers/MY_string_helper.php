<?php
if (! defined ( 'BASEPATH' ))
	exit ( 'No direct script access allowed' );

if (! function_exists ( 'parse_shop_status' ))
{

	function parse_shop_status($status)
	{
		switch ($status)
		{
			case 0 :
				return '待审核';
				break;
			case 10 :
				return '审核未通过';
				break;
			case 20 :
				return '审核通过';
				break;
			case 30 :
				return '上架';
				break;
			case 40 :
				return '下架';
				break;
		}
	}
}

if (! function_exists ( 'print_str' ))
{
	function print_str($string)
	{
		echo empty($string) ? '--' : $string;
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
                $query = "&" . http_build_query($get);
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
        $str = $str/100;
        return number_format($str, 2, ".", ",");
    }

}