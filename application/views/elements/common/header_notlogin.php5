<!DOCTYPE HTML>
<html>
    <head>
    	<meta charset="utf-8">
        <?php
        /* --- seo优化 --- @Author liusijia --- start --- */
        $this->config->load('seo');
        $seo = $this->config->item('seo');
        $set_data = $seo[$this->con][$this->act];
        $title = str_replace(array('#cnName#', '*date*', '#pageNumber#'), array($cnName, (!empty($date) ? date('Y-m-d', $date) : ''), (($pageNumber > 1) ? '-第' . $pageNumber . '页' : '')), $set_data['title']);
        $keywords = str_replace(array('#cnName#', '*date*'), array($cnName, (!empty($date) ? date('Y-m-d', $date) : '')), $set_data['keywords']);
        $description = str_replace(array('#cnName#', '*date*'), array($cnName, (!empty($date) ? date('Y-m-d', $date) : '')), $set_data['description']);
        /* --- seo优化 --- @Author liusijia --- end --- */
        ?>
        <title><?php echo $title; ?></title>
        <meta content="<?php echo $description; //@Author liusijia    ?>" name="Description" />
        <meta content="<?php echo $keywords; //@Author liusijia   ?>" name="Keywords" />
        <link href="<?php echo getStaticFile('/caipiaoimg/v1.0/styles/global.css');?>" rel="stylesheet" />
        <link href="<?php echo getStaticFile('/caipiaoimg/v1.0/styles/login.css');?>" rel="stylesheet" />
        <script src="/source/js/jquery-1.8.3.min.js"></script>
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
        <div class="fix-foot-wrap">
        <!--header begin-->
        <div class="header">
            <div class="wrap_in">
                <div class="logo-group">
                    <h1 class="logo"><a href="/"><span class="logo-txt">2345彩票网<small>A股上市公司旗下网站</small></span></a></h1>
                    <p class="slogan"><span class="slogan-txt">100%安全购彩平台</span></p>
                </div>
                <div class="aside clearfix">
                  <p class="telphone"><span class="telphone-txt">电话：400-000-2345转8</span></p>
                </div>
            </div>
        </div>
        <!--header end-->
