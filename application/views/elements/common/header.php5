<?php if (!$this->is_ajax): ?>
    <!DOCTYPE HTML>
    <html>
        <head>
            <meta charset="utf-8">
            <?php
            /* --- seo优化 --- @Author liusijia --- start --- */
            $this->config->load('seo');
            $seo = $this->config->item('seo');
            $set_data = $seo[$this->con][$this->act];
            $title = str_replace(array('#cnName#', '*date*', '#pageNumber#','#pageTitle#','#issue#'), array($cnName, (!empty($date) ? date('Y-m-d', $date) : ''), (($pageNumber > 1) ? '-第' . $pageNumber . '页' : ''),(!empty($pageTitle)?$pageTitle:''),(!empty($issue)?$issue:'')), $set_data['title']);
            $keywords = ($pageNumber > 1) ? '' : str_replace(array('#cnName#', '*date*', '#issue#'), array($cnName, (!empty($date) ? date('Y-m-d', $date) : ''), (!empty($issue)?$issue:'')), $set_data['keywords']);
            $description = ($pageNumber > 1) ? '' : str_replace(array('#cnName#', '*date*', '#issue#'), array($cnName, (!empty($date) ? date('Y-m-d', $date) : ''), (!empty($issue)?$issue:'')), $set_data['description']);
            /* --- seo优化 --- @Author liusijia --- end --- */
            ?>
            <title><?php echo $title; ?></title>
            <meta content="<?php echo $description; //@Author liusijia   ?>" name="Description" />
            <meta content="<?php echo $keywords; //@Author liusijia  ?>" name="Keywords" />
            <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
            <meta name="renderer" content="webkit">
            <meta name="baidu-site-verification" content="Ecsesm8qDQ" />
            <base href="<?php echo $pagesUrl; ?>">
            <!--[if lte IE 6]></base><![endif]-->
            <link rel="shortcut icon" href="/favicon.ico"/>
            <link rel="bookmark" href="/favicon.ico"/>
            <link rel="stylesheet" href="<?php echo getStaticFile('/caipiaoimg/v1.0/styles/global.css');?>"/>

            <script src="<?php echo getStaticFile('/caipiaoimg/v1.0/js/jquery-1.8.3.min.js');?>" type="text/javascript"></script>
            
            <?php if (!$htype): ?>
            <link href="<?php echo getStaticFile('/caipiaoimg/v1.0/styles/lottery-public.css');?>" rel="stylesheet" type="text/css" />
            <link href="<?php echo getStaticFile('/caipiaoimg/v1.0/styles/lottery-custom.css');?>" rel="stylesheet" type="text/css" />

            <?php endif; ?>

        </head>
        <body>
            <script type="text/javascript">
                var baseUrl = '<?php echo $baseUrl; ?>';
                var busiUrl = '<?php echo $busiUrl; ?>';
                var passUrl = '<?php echo $passUrl; ?>';
                var payUrl = '<?php echo $payUrl; ?>';
                var fileUrl = '<?php echo $fileUrl; ?>';
                var cmsUrl = '<?php echo $cmsUrl; ?>';
                var G = {
                    busiUrl: busiUrl,
                    passUrl: passUrl,
                    payUrl: payUrl,
                    cmsUrl: cmsUrl,
                    fileUrl: fileUrl
                }
            </script>
 		<script src="<?php echo getStaticFile('/caipiaoimg/v1.0/js/base.js'); ?>" type="text/javascript" ></script>
        <div class="fix-foot-wrap">
            <!--top begin-->
            <?php if (empty($this->uid)): ?>
                <div class="top_bar">
                    <?php $this->load->view('elements/common/header_topbar_notlogin'); ?>
                </div>
            <?php else: ?>
                <div class="top_bar">
                    <?php $this->load->view('elements/common/header_topbar'); ?>
                </div>
            <?php endif; ?>
            <!--top end-->

            <!--header begin-->
            <div class="header">
                <div class="wrap_in">
                    <div class="logo-group">
                        <div class="logo"><a href=""><span class="logo-txt">2345彩票网<small>A股上市公司旗下网站</small></span></a></div>
                        <p class="slogan"><span class="slogan-txt">100%安全购彩平台</span></p>
                    </div>
                    <div class="aside clearfix">
                      <p class="telphone"><span class="telphone-txt">电话：400-000-2345转8</span></p>
                    </div>
                </div>
            </div>
            <!--header end-->

            <!--nav begin-->
            <div class="nav">
                <div class="wrap_in">
                    <div class="nav-ticket"><h2>选择彩种</h2><i class="i_navArrow"></i>
                    <?php if( $this->con != 'main' ): ?>
                        <div class="lottery-categorys">
                            <ul>
                                <li>
                                    <a href="<?php echo $baseUrl; ?>ssq" class="item-a nav-ssq">
                                        <s><img src="<?php echo getStaticFile('/caipiaoimg/v1.0/img/sprite-xz-img.png');?>" width="120" height="90" alt=""></s>
                                        <p class="cont">
                                            <span class="title"><strong class="pause">双色球</strong>
                                                <!-- <em class="redWords"><i class="arrowsIcon"></i>加奖</em> -->
                                                <em class="grayWords"><i class="arrowsIcon"></i>停售</em>
                                            </span>
                                        </p>
                                    </a>
                                </li>
                                <li>
                                    <a href="<?php echo $baseUrl; ?>dlt" class="item-a nav-dlt">
                                        <s><img src="<?php echo getStaticFile('/caipiaoimg/v1.0/img/sprite-xz-img.png');?>"width="120" height="90" alt=""></s>
                                        <p class="cont">
                                            <span class="title">
                                                <strong class="pause">大乐透</strong>
                                                <em class="grayWords"><i class="arrowsIcon"></i>停售</em>
                                            </span>
                                        </p>
                                    </a>
                                </li>
                                <li>
                                    <a href="<?php echo $baseUrl; ?>jclq/hh" class="item-a nav-jclq">
                                        <s><img src="<?php echo getStaticFile('/caipiaoimg/v1.0/img/sprite-xz-img.png');?>"width="120" height="90" alt=""></s>
                                        <p class="cont">
                                            <span class="title">
                                                <strong class="pause">竞彩篮球</strong>
                                                <em class="grayWords"><i class="arrowsIcon"></i>停售</em>
                                            </span>
                                        </p>
                                    </a>
                                </li>
                                <li>
                                    <a href="<?php echo $baseUrl; ?>jczq/hh" class="item-a nav-jczq">
                                        <s><img src="<?php echo getStaticFile('/caipiaoimg/v1.0/img/sprite-xz-img.png');?>"width="120" height="90" alt=""></s>
                                        <p class="cont">
                                            <span class="title">
                                                <strong class="pause">竞彩足球</strong>
                                                <!-- <em class="redWords"><i class="arrowsIcon"></i>单关上线</em> -->
                                                <em class="grayWords"><i class="arrowsIcon"></i>停售</em>
                                            </span>
                                        </p>
                                    </a>
                                </li>
                                <li class="other">
                                    <p>数字彩</p>
                                    <div class="tlist-m clearfix">
                                        <a href="<?php echo $baseUrl; ?>fcsd" class="pause">福彩3D</a>
                                        <a href="<?php echo $baseUrl; ?>qlc" class="pause">七乐彩</a>
                                        <a href="<?php echo $baseUrl; ?>pls" class="pause">排列三</a>
                                        <a href="<?php echo $baseUrl; ?>qxc" class="pause">七星彩</a>
                                        <a href="<?php echo $baseUrl; ?>plw" class="pause">排列五</a>
                                        <a href="<?php echo $baseUrl; ?>syxw" class="pause">老11选5</a>
                                    </div>
                                </li>
                                <li class="other last">
                                    <p>竞技彩</p>
                                    <div class="tlist-m clearfix">
                                        <a href="<?php echo $baseUrl; ?>sfc" class="pause">胜负彩<span class="grayWords"><i class="arrowsIcon"></i>停售</span></a>
                                        <a href="<?php echo $baseUrl; ?>rj" class="pause">任选九<span class="grayWords"><i class="arrowsIcon"></i>停售</span></a>
                                        <!-- <em><a href="<?php echo $baseUrl; ?>jclq/hh">竞彩篮球</a></em> -->
                                    </div>
                                </li>
                            </ul>
                        </div>
                    <?php endif; ?>
                    </div>
                    <ul class="main_nav clearfix">
                        <li class="<?php echo $this->con == 'main' ? 'cur' : ''; ?>"><a target="_self" href="/">首页</a></li>
<!--                        <li class="--><?php //echo $this->con == 'hall' ? 'cur' : ''; ?><!--"><a target="_self" href="/hall">购彩大厅</a></li>-->
                        <li class="<?php echo $this->con == 'kaijiang' ? 'cur' : ''; ?>"><a target="_self" href="/kaijiang">全国开奖</a></li>
                        <li class="<?php echo $this->con == 'chart' ? 'cur' : ''; ?>"><a target="_self" href="/chart">走势图</a></li>
                        <li class="<?php echo $this->con == 'academy' ? 'cur' : ''; ?>"><a target="_self" href="/academy">彩票学院</a></li>
                        <!-- <li class="<?php echo $this->con == 'help' ? 'cur' : ''; ?>"><a target="_self" href="<?php echo $baseUrl; ?>help/index/b4-i1">帮助中心</a></li> -->
                        <li class="<?php echo $this->con == 'phone' ? 'cur' : ''; ?>"><a target="_self" href="/app_buy">手机购彩</a></li>
                        <li class="customer"><a target="_blank" href="http://wpa.qq.com/msgrd?v=3&amp;uin=2584565084&amp;site=qq&amp;menu=yes" ><i class="i_navCustomer"></i>在线客服</a></li>
                    </ul>
                </div>
            </div>
            <!--nav end-->
            <?php if (!($this->con == 'main' && $this->act == 'index')): ?>
                <div class="wrap_in path">您的位置：<a href="/" target='_self'>首页</a><i>&gt;</i><?php echo my_crumbs(); ?></div>
            <?php endif; ?>
        <?php endif; ?>