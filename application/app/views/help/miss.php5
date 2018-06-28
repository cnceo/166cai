<!doctype html> 
<html> 
<head>
    <meta charset="utf-8">
    <meta name="author" content="weblol">
    <meta name="format-detection" content="telephone=no"/>
    <meta name="viewport" content="width=device-width,user-scalable=no,minimal-ui"/>
    <meta name="apple-mobile-web-app-capable" content="yes"/>
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent"/>
    <meta name="apple-mobile-web-app-title" content="166彩票">
    <meta content="telephone=no" name="format-detection" /> 
    <meta content="email=no" name="format-detection" />
    <title>遗漏投注说明</title>
    <link rel="stylesheet" href="<?php echo getStaticFile('/caipiaoimg/static/css/cpui.min.css')?>">
</head>
<body>
    <div class="wrapper">
        <article class="article omit-help">
            <dl>
                <dt>1. 什么是遗漏</dt>
                <dd>
                    <p>遗漏是指该号码自上次开出以来至本次未出现的期数， 比如号码06的遗漏值是8，则表示06已经有8期没开出了。</p>
                </dd>
                <dt>2. 组合遗漏</dt>
                <dd>
                    <p>组合遗漏是对该玩法下全部号码组合的遗漏数据的一个 全面统计，能方便快速掌握号码组合的出现规律。例如 任选五：</p>
                    <div class="img-box"><img src="<?php echo getStaticFile('/caipiaoimg/static/images/omit-img1.png')?>" alt=""></div>
                </dd>
            </dl>
            <dl class="min-dl">
                <dt>当前遗漏：</dt>
                <dd>号码组合“01 02 03 04 05”当前已经有3721期 没有开出。</dd>
                <dt>欲出几率：</dt>
                <dd>当前遗漏÷平均遗漏，数值越高期望出现的几 率越大（平均遗漏是该组合所有遗漏值的平均值）。</dd>
                <dt>遗漏投注：</dt>
                <dd>
                    <p>用图表方式呈现该号码组合近期开出时遗漏的期数，并与该组合的历史最大遗漏、历史平均遗漏和近10次最大遗漏做对比参照，确保在合适的时机及时出手！（历史最大是该组合在历史上遗漏期数的最大值，不含当前遗漏）。例如下图表示号码组合最近几次是遗漏了226期开出、接着遗漏了526期开出、再后来遗漏了767期开出……以此类推。</p>
                    <div class="img-box"><img src="<?php echo getStaticFile('/caipiaoimg/static/images/omit-img2.png')?>" alt=""></div>
                </dd>
            </dl>
          <div class="note">
              <h3 class="note-title">温馨提醒：</h3>
              <p class="note-txt">因体育彩票管理中心对本彩种每期全部投注号码的可投注数量实行动态控制，如果您的投注方案中包括限号号码，系统将自动撤单返款。</p>
          </div>
        </article>
    </div>
    <?php $this->load->view('mobileview/common/tongji'); ?>
</body>

</html>
