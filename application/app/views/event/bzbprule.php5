<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="author" content="weblol">
    <meta name="format-detection" content="telephone=no"/>
    <meta name="viewport" content="width=device-width,initial-scale=1,maximum-scale=1,user-scalable=no,minimal-ui"/>
    <meta name="apple-mobile-web-app-capable" content="yes"/>
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent"/>
    <meta name="apple-mobile-web-app-title" content="166彩票">
    <meta content="telephone=no" name="format-detection" /> 
    <meta content="email=no" name="format-detection" />
    <title>追号不中包赔</title>
    <link rel="stylesheet" href="<?php echo getStaticFile('/caipiaoimg/static/css/cpui.min.css')?>">
    <link rel="stylesheet" href="<?php echo getStaticFile('/caipiaoimg/static/css/layout/msg.min.css')?>">
</head>
<body>
    <article class="wrapper msg-detail txt-bg news-bet">
        <header class="msg-detail-hd">
            <h1 style="text-align: center;">活动规则</h1>
        </header>
        <div class="msg-detail-bd">
            <ol>
                <li>1.活动时间：<?php echo date('Y年m月d日 H:i:s', strtotime($activityInfo[0]['startTime']))?>~<?php echo date('Y年m月d日 H:i:s', strtotime($activityInfo[0]['endTime']))?></li>
                <li>2.活动期间内，通过本活动页面追号的用户，若追号期间每一期皆不中奖，则返还相对应的彩金，如：双色球追30期不中返60元。</li>
                <li>3.追号投注成功是指所追号期数皆成功出票，用户自主取消追号方案（撤单）的，视为放弃本次活动。</li>
                <li>4.活动追号只支持机选1倍，不支持自选号码，追号期次为从当前期开始的连续期次。</li>
                <li>5.活动返还的彩金，双色球为在用户追号的最后一期开奖日的22:00后返还；大乐透为在用户追号的最后一期开奖日的21:00后返还。</li>
                <li>6.追号不中返还的彩金不可提现，只能用于购彩，中奖奖金可提现。</li>
                <li>7.如用户通过不正当手段参与活动，166彩票网有权不予赠送、限制提现、冻结账户以及要求用户返还不正当得利。在法律允许范围内，166彩票网保留最终解释权。</li>
                <li>8.关于活动的任何问题，请联系在线客服或拨打电话400-690-6760。</li>
            </ol>
        </div>
        <footer>
          <a href="/app/event/zhbzbp" class="btn btn-block-confirm">立即投注</a>
        </footer>
    </article>
</body>
<?php $this->load->view('mobileview/common/tongji'); ?>
</html>
