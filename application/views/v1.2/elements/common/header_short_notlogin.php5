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
            <meta name="baidu-site-verification" content="lQnvYyQA6s" />
            <base href="<?php echo $pagesUrl; ?>" <?php if (in_array($this->con, array('notice', 'help', 'hall'))): ?> target="_blank" <?php endif;?>>
            <!--[if lte IE 6]></base><![endif]-->
            <link rel="shortcut icon" href="/favicon.ico"/>
            <link rel="bookmark" href="/favicon.ico"/>
            <link rel="stylesheet" href="<?php echo getStaticFile('/caipiaoimg/v1.1/styles/global.min.css');?>"/>

            <script src="<?php echo getStaticFile('/caipiaoimg/v1.1/js/jquery-1.8.3.min.js');?>" type="text/javascript"></script>
            
            <?php if (!$htype): ?>
            <link href="<?php echo getStaticFile('/caipiaoimg/v1.1/styles/lottery-public.min.css');?>" rel="stylesheet" />

            <?php endif; ?>

        </head>
        <body>
            <script type="text/javascript">
                var baseUrl = '<?php echo $baseUrl; ?>';
                var uri = '<?php echo str_replace(array('<', '>', 'script'), '', $_SERVER['REQUEST_URI']);?>';
                var version = 'v1.1';
                var G = {
                	baseUrl: baseUrl
                }
                var visitor = {userNickname:'<?php echo empty($this->uid) ? '未登录用户' : $this->uinfo['uname']?>'};
				window.easemobim = window.easemobim || {};
				easemobim.config = {visitor: visitor};
            </script>
            <script src='//kefu.easemob.com/webim/easemob.js'></script>
 		<script src="<?php echo getStaticFile('/caipiaoimg/v1.1/js/base.min.js'); ?>" type="text/javascript" ></script>
            <!--top begin-->
            <?php if (empty($this->uid)): 
            	$this->load->view('v1.1/elements/common/header_topbar_notlogin'); 
            else: 
            	$this->load->view('v1.1/elements/common/header_topbar'); 
            endif; ?>
            <!--top end-->

            <!--header begin-->
            <div class="header header-short">
                <div class="wrap header-inner">
                    <div class="logo">
                        <div class="logo-txt"><span class="logo-txt-name">166彩票</span></div>
                        <a href="/" class="logo-img"><img src="<?php echo getStaticFile('/caipiaoimg/v1.1/images/logo/logo-166.png');?>" srcset="<?php echo getStaticFile('/caipiaoimg/v1.1/images/logo/logo-166@2x.svg');?> 2x" width="280" height="70" alt="166彩票网"></a>
                        <h1 class="header-title"><?php echo $headTitle; ?></h1>
                    </div>
                    <div class="aside">
                        <a href="javascript:;" onclick="easemobim.bind({tenantId: '38338'})" class="btn online-service" target="_self"><i class="icon-font"></i>在线客服</a>
                        <p class="telphone"><i class="icon-font"></i>400-690-6760</p>
                    </div>
                </div>
            </div>
            <!--header end-->
        <?php endif; ?>