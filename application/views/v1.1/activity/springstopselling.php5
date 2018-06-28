<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <meta name="viewport" content="width=device-width,initial-scale=1,maximum-scale=1,user-scalable=no,minimal-ui"/>
    <meta>
    <title>2016年春节休市公告页面-166彩票官网</title>
    <link rel="stylesheet" href="/caipiaoimg/v1.1/styles/global.min.css">
    <script src="<?php echo getStaticFile('/caipiaoimg/v1.1/js/jquery-1.8.3.min.js');?>"></script>
    <style>
        body {
            background: #ffe6a3;
            font: normal 12px/1.5 simSun, sans-serif;
        }


        .sss-wrap {
            min-width: 1000px;
            _width: expression((document.documentElement.clientWidth||document.body.clientWidth)<1000?"1000px":"");
            height: 1200px;
            margin-bottom: 20px;
            background: url(<?php echo getStaticFile('/caipiaoimg/v1.1/img/active/springStopSelling/sss-bg.png');?>) 50% 0 no-repeat;
            font-family: Arial, 'Microsoft Yahei', sans-serif;
            font-family: 'Microsoft Yahei'\9;
        }
        .sss-title {
            width: 1000px;
            height: 290px;
            margin: 0 auto;
            background: url(<?php echo getStaticFile('/caipiaoimg/v1.1/img/active/springStopSelling/sss-banner.png');?>) 50% 0 no-repeat;
            text-indent: -200%;
            overflow: hidden;
            font-size: 0;
        }
        .sss-lnk {
            width: 1000px;
            margin: -270px auto 660px;
            overflow: hidden;
            *zoom: 1;
        }
        .sss-lnk a {
            float: right;
            margin-left: 20px;
            font-size: 18px;
            color: #fff;
        }
        .sss-lnk a:hover {
            color: #fff;
        }
        .sss-article {
            width: 1000px;
            margin: 0 auto 20px;
            text-align: center;
            line-height: 1.8;
            font-size: 20px;
            color: #2f2200;
        }
        .sss-article em {
            color: #d00000;
        }
        .sss-article strong {
            display: block;
            margin: 20px auto;
            font-size: 26px;
            color: #d00000;
        }
        .ssss-after {
            margin-bottom: 50px;
            color: #333;
        }
        .ssss-after img {
            vertical-align: middle;
        }
        .ssss-after span {
            display: inline-block;
            *display: inline;
            *zoom: 1;
            vertical-align: middle;
        }
        .sss-article-lnk {
            font-family: simSun;
            font-size: 12px;
        }
        .wrap_in{ font-family:Arial,​SimSun,​sans-serif;}
    </style>
</head>
<body>
<?php if (empty($this->uid)): ?>
    <div class="top_bar">
        <?php $this->load->view('v1.1/elements/common/header_topbar_notlogin'); ?>
    </div>
<?php else: ?>
    <div class="top_bar">
        <?php $this->load->view('v1.1/elements/common/header_topbar'); ?>
    </div>
<?php endif; ?>
<div class="sss-wrap">
    <h1 class="sss-title">2016年祝您猴年大吉</h1>
    <div class="sss-lnk">
        <a href="http://caipiao.2345.com/academy" target="_blank">彩票学院</a>
        <a href="http://caipiao.2345.com/chart" target="_blank">走势图</a>
        <a href="http://caipiao.2345.com/kaijiang" target="_blank">全国开奖</a>
    </div>
    <div class="sss-article">
        <p>据统计<br>离家工作之后<br>如果以每年回家7天来算<br>再除去休息的时间<br>一生能与父母相处的时间仅剩<em>45</em>天<br>所以...<br><strong>春节回家，陪好爸妈</strong></p>
        <p class="ssss-after"><img src="/caipiaoimg/v1.1/img/active/springStopSelling/sss-2345.png" alt="166彩票"><span>和您年后再约~</span></p>
        <a href="http://caipiao.2345.com/notice/detail/12894" target="_blank" class="sss-article-lnk">《2016彩票市场春节休市公告》</a>
    </div>
</div>
</body>
</html>
<?php $this->load->view('v1.1/elements/common/footer_academy');?>